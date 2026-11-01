<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PesapalService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PesapalController extends Controller
{
    public function __construct(
        protected PesapalService $pesapalService
    ) {}

    /**
     * Handle the customer return/redirect from Pesapal after payment.
     */
    public function callback(Request $request): RedirectResponse
    {
        $trackingId = $request->query('OrderTrackingId');
        $merchantReference = $request->query('OrderMerchantReference');

        Log::info('Pesapal Callback Received', [
            'tracking_id' => $trackingId,
            'merchant_reference' => $merchantReference,
            'query' => $request->query(),
        ]);

        if (! $trackingId && ! $merchantReference) {
            return redirect()->route('orders.index')
                ->with('error', 'Missing transaction reference from payment gateway.');
        }

        $order = $this->findOrder($merchantReference, $trackingId);

        if (! $order) {
            Log::warning('Pesapal Callback: Order not found', [
                'merchant_reference' => $merchantReference,
                'tracking_id' => $trackingId,
            ]);

            return redirect()->route('orders.index')
                ->with('error', 'We could not match the returned payment to an order.');
        }

        try {
            $statusData = $this->pesapalService->getTransactionStatus($trackingId);
            $this->applyPaymentStatus($order, $statusData, $trackingId);

            Session::forget('cart');

            if ($order->payment_status === 'paid') {
                return redirect()->route('orders.confirmation', $order)
                    ->with('success', 'Payment received successfully! Your order is being processed.');
            }

            if ($order->payment_status === 'failed') {
                return redirect()->route('orders.confirmation', $order)
                    ->with('error', 'Payment was cancelled or unsuccessful. You can retry payment below.');
            }

            return redirect()->route('orders.confirmation', $order)
                ->with('info', 'Your payment is currently pending confirmation from your provider.');
        } catch (Exception $e) {
            Log::error('Pesapal Callback Exception', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('orders.confirmation', $order)
                ->with('info', 'Payment status check in progress. Please check back shortly.');
        }
    }

    /**
     * Handle asynchronous Instant Payment Notification (IPN) webhooks from Pesapal.
     */
    public function ipn(Request $request): JsonResponse
    {
        $trackingId = $request->input('OrderTrackingId', $request->query('OrderTrackingId'));
        $merchantReference = $request->input('OrderMerchantReference', $request->query('OrderMerchantReference'));
        $notificationType = $request->input('OrderNotificationType', $request->query('OrderNotificationType', 'IPNchange'));

        Log::info('Pesapal IPN Webhook Received', [
            'tracking_id' => $trackingId,
            'merchant_reference' => $merchantReference,
            'notification_type' => $notificationType,
        ]);

        if ($trackingId) {
            try {
                $order = $this->findOrder($merchantReference, $trackingId);
                if ($order) {
                    $statusData = $this->pesapalService->getTransactionStatus($trackingId);
                    $this->applyPaymentStatus($order, $statusData, $trackingId);
                }
            } catch (Exception $e) {
                Log::error('Pesapal IPN Processing Error', [
                    'tracking_id' => $trackingId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Pesapal expects this exact JSON response structure to acknowledge receipt
        return response()->json([
            'orderNotificationType' => $notificationType,
            'orderTrackingId' => $trackingId,
            'orderMerchantReference' => $merchantReference,
            'status' => 200,
        ], 200);
    }

    /**
     * Retry payment for an unpaid or failed order.
     */
    public function retry(Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.confirmation', $order)
                ->with('info', 'This order is already marked as paid.');
        }

        try {
            $result = $this->pesapalService->submitOrder($order, [
                'name' => $order->shipping_name,
                'phone' => $order->shipping_phone ?? $order->payment_phone,
                'email' => $order->user?->email ?? auth()->user()->email,
                'region' => $order->shipping_region,
                'district' => $order->shipping_district,
                'area' => $order->shipping_area,
                'landmark' => $order->shipping_landmark,
                'address' => $order->shipping_address,
            ]);

            $order->update([
                'pesapal_tracking_id' => $result['order_tracking_id'],
                'payment_initiated_at' => now(),
            ]);

            return redirect()->away($result['redirect_url']);
        } catch (Exception $e) {
            Log::error('Pesapal Retry Payment Failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Unable to initiate payment: ' . $e->getMessage());
        }
    }

    /**
     * Find order by merchant reference, tracking ID, or order ID.
     */
    protected function findOrder(?string $merchantReference, ?string $trackingId): ?Order
    {
        if ($merchantReference) {
            $order = Order::where('transaction_id', $merchantReference)->first();
            if ($order) {
                return $order;
            }

            if (is_numeric($merchantReference)) {
                $order = Order::find((int) $merchantReference);
                if ($order) {
                    return $order;
                }
            }
        }

        if ($trackingId) {
            $order = Order::where('pesapal_tracking_id', $trackingId)->first();
            if ($order) {
                return $order;
            }
        }

        return null;
    }

    /**
     * Update order record based on Pesapal transaction status data.
     */
    protected function applyPaymentStatus(Order $order, array $statusData, string $trackingId): void
    {
        $statusCode = (int) ($statusData['status_code'] ?? 0);
        $statusDescription = strtoupper((string) ($statusData['payment_status_description'] ?? ''));
        $paymentMethod = $statusData['payment_method'] ?? null;

        $updates = [
            'pesapal_tracking_id' => $trackingId,
            'payment_method' => 'Pesapal',
        ];

        // Status code 1 = Completed / Paid
        if ($statusCode === 1 || $statusDescription === 'COMPLETED') {
            $updates['payment_status'] = 'paid';
            if ($order->status === 'pending') {
                $updates['status'] = 'confirmed';
            }
        } elseif ($statusCode === 2 || $statusDescription === 'FAILED') {
            $updates['payment_status'] = 'failed';
        } elseif ($statusCode === 3 || $statusDescription === 'REVERSED') {
            $updates['payment_status'] = 'refunded';
        } else {
            $updates['payment_status'] = 'pending';
        }

        $order->update($updates);
    }
}
