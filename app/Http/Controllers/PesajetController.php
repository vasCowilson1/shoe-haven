<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PesajetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PesajetController extends Controller
{
    public function __construct(
        protected PesajetService $pesajetService
    ) {}

    /**
     * Handle the async webhook callback from PesaJet.
     * PesaJet POSTs payment result here after the customer approves/rejects on their phone.
     */
    public function callback(Request $request)
    {
        Log::info('PesaJet callback received', $request->all());

        // Resolve order by pesajet_reference or external_reference / transaction_id
        $reference = $request->input('reference')
            ?? $request->input('transaction_id')
            ?? $request->input('external_reference');

        if (! $reference) {
            Log::warning('PesaJet callback missing reference');
            return response()->json(['message' => 'Missing reference'], 422);
        }

        $order = Order::where('pesajet_reference', $reference)
            ->orWhere('transaction_id', $reference)
            ->first();

        if (! $order) {
            Log::warning('PesaJet callback: order not found', ['reference' => $reference]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        $status = strtoupper($request->input('status', ''));

        $paymentStatus = match (true) {
            in_array($status, ['SUCCESS', 'COMPLETED', 'PAID'])   => 'paid',
            in_array($status, ['FAILED', 'CANCELLED', 'EXPIRED']) => 'failed',
            $status === 'REFUNDED'                                 => 'refunded',
            in_array($status, ['PARTIALLY_REFUNDED', 'PARTIAL_REFUND']) => 'partially_refunded',
            default                                                => 'pending',
        };

        $order->update(['payment_status' => $paymentStatus]);

        if ($paymentStatus === 'paid') {
            $order->update(['status' => 'processing']);
        }

        Log::info('PesaJet callback processed', [
            'order_id'       => $order->id,
            'payment_status' => $paymentStatus,
        ]);

        return response()->json(['message' => 'OK']);
    }

    /**
     * Re-initiate payment for a pending/failed order.
     */
    public function retry(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.confirmation', $order)
                ->with('status', 'This order has already been paid.');
        }

        try {
            $result = $this->pesajetService->initiatePayment($order, [
                'phone' => $order->payment_phone,
                'name'  => $order->shipping_name,
            ]);

            $paymentStatus = in_array(strtoupper($result['status'] ?? ''), ['FAILED', 'CANCELLED', 'EXPIRED'], true)
                || empty($result['reference'])
                ? 'failed'
                : 'pending';

            $order->update([
                'pesajet_reference' => $result['reference'] ?? $order->pesajet_reference,
                'payment_status' => $paymentStatus,
            ]);

            return redirect()->route('orders.confirmation', $order)
                ->with('status', $paymentStatus === 'failed'
                    ? 'The payment request could not be completed. Please retry the payment.'
                    : 'Payment request sent to your phone. Approve the prompt to complete your order.');
        } catch (\Exception $e) {
            Log::error('PesaJet payment retry failed', [
                'order_id' => $order->id,
                'exception' => $e->getMessage(),
            ]);

            return redirect()->route('orders.confirmation', $order)
                ->withErrors(['payment' => 'We could not start the payment. Please try again.']);
        }
    }

    /**
     * AJAX endpoint to poll payment status for a given order.
     */
    public function status(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if (! $order->pesajet_reference) {
            return response()->json([
                'payment_status' => $order->payment_status,
                'status_check_failed' => true,
                'message' => 'This order has no payment reference to verify.',
            ]);
        }

        try {
            $result = $this->pesajetService->verifyPayment($order->pesajet_reference);
            $providerStatus = strtoupper($result['status'] ?? '');

            if ($providerStatus === '' || $providerStatus === 'UNKNOWN') {
                return response()->json([
                    'payment_status' => $order->payment_status,
                    'status_check_failed' => true,
                    'message' => 'Payment status could not be verified.',
                ], 503);
            }

            $paymentStatus = match ($providerStatus) {
                'SUCCESS', 'COMPLETED', 'PAID' => 'paid',
                'FAILED', 'CANCELLED', 'EXPIRED' => 'failed',
                'REFUNDED' => 'refunded',
                'PARTIALLY_REFUNDED', 'PARTIAL_REFUND' => 'partially_refunded',
                default                         => 'pending',
            };

            if ($paymentStatus !== $order->payment_status) {
                $order->update(['payment_status' => $paymentStatus]);
                if ($paymentStatus === 'paid') {
                    $order->update(['status' => 'processing']);
                }
            }

            return response()->json([
                'payment_status' => $paymentStatus,
                'status_check_failed' => false,
            ]);
        } catch (\Exception $e) {
            Log::warning('PesaJet payment status verification failed', [
                'order_id' => $order->id,
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'payment_status' => $order->payment_status,
                'status_check_failed' => true,
                'message' => 'Payment status could not be verified.',
            ], 503);
        }
    }
}
