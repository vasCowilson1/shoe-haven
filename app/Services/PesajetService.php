<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PesajetService
{
    protected string $apiKey;
    protected string $webhookSecret;
    protected string $currency;
    protected string $baseUrl;
    protected int $timeout;
    protected bool $mockFallback;

    public function __construct()
    {
        $this->apiKey        = (string) config('pesajet.api_key', '');
        $this->webhookSecret = (string) config('pesajet.webhook_secret', '');
        $this->currency      = (string) config('pesajet.currency', 'UGX');
        $this->baseUrl       = rtrim((string) config('pesajet.base_url', 'https://payments.pesajet.com/api/v1'), '/');
        $this->timeout       = (int) config('pesajet.timeout', 30);
        $this->mockFallback  = (bool) config('pesajet.mock_fallback', false);
    }

    /**
     * Initiate a PesaJet mobile money collection for the given order.
     *
     * @param  Order  $order
     * @param  array  $billingDetails  Must include 'phone' and optionally 'provider' (mtn|airtel)
     * @return array  ['reference' => string, 'transaction_id' => string, 'status' => string, 'message' => string]
     *
     * @throws \RuntimeException on configuration or API errors
     */
    public function initiatePayment(Order $order, array $billingDetails = []): array
    {
        if (empty($this->apiKey)) {
            if ($this->mockFallback || app()->environment('local', 'testing')) {
                Log::warning('PesaJet API key not configured; using test transaction in local/test environment.');
                return [
                    'reference'      => 'SIM-' . $order->transaction_id,
                    'transaction_id' => 'TXN-' . $order->id . '-' . time(),
                    'status'         => 'PENDING',
                    'message'        => 'Payment request simulated. Approve the prompt on your mobile phone to complete.',
                ];
            }

            throw new \RuntimeException(
                'PesaJet API key is not configured. Set PESAJET_API_KEY in your .env file.'
            );
        }

        $phone       = (string) ($billingDetails['phone'] ?? $order->payment_phone ?? '');
        $rawProvider = (string) ($billingDetails['provider'] ?? $this->detectProvider($phone));
        $provider    = strtolower($rawProvider);
        if (! in_array($provider, ['mtn', 'airtel'], true)) {
            $provider = 'mtn';
        }

        $formattedPhone = $this->formatPhoneNumber($phone);

        $payload = [
            'type'        => 'COLLECTION',
            'amount'      => (int) round($order->total_amount),
            'currency'    => $this->currency,
            'phoneNumber' => $formattedPhone,
            'provider'    => $provider,
            'reference'   => (string) $order->transaction_id,
            'description' => 'Shoe Haven Order #' . $order->id,
            'metadata'    => [
                'order_id'       => $order->id,
                'customer_name'  => $billingDetails['name'] ?? $order->shipping_name ?? 'Customer',
                'customer_email' => $billingDetails['email'] ?? '',
            ],
        ];

        Log::info('PesaJet initiate payment request', ['order_id' => $order->id, 'payload' => $payload]);

        $response = Http::withHeaders([
            'X-API-Key'    => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])
        ->withToken($this->apiKey)
        ->timeout($this->timeout)
        ->post("{$this->baseUrl}/payments", $payload);

        if ($response->failed()) {
            $body = $response->json() ?? [];
            $msg  = $body['message'] ?? $body['error'] ?? $response->body();
            Log::error('PesaJet API error', ['status' => $response->status(), 'body' => $body]);
            throw new \RuntimeException("PesaJet payment initiation failed: {$msg}");
        }

        $data = $response->json();

        $transactionId = $data['transactionId'] ?? $data['transaction_id'] ?? $data['id'] ?? $data['reference'] ?? null;
        $status        = strtoupper($data['status'] ?? 'PENDING');

        return [
            'reference'      => $transactionId ?? $order->transaction_id,
            'transaction_id' => $transactionId,
            'status'         => $status,
            'message'        => $data['message'] ?? 'Payment prompt sent to your phone. Approve the prompt to complete.',
            'data'           => $data,
        ];
    }

    /**
     * Query transaction status from PesaJet by transaction ID.
     */
    public function verifyPayment(string $transactionId): array
    {
        if (empty($this->apiKey)) {
            if ($this->mockFallback || app()->environment('local', 'testing')) {
                return ['status' => 'COMPLETED', 'message' => 'Simulated payment completed'];
            }

            throw new \RuntimeException('PesaJet API key is not configured.');
        }

        $response = Http::withHeaders([
            'X-API-Key' => $this->apiKey,
            'Accept'    => 'application/json',
        ])
        ->withToken($this->apiKey)
        ->timeout($this->timeout)
        ->get("{$this->baseUrl}/payments/{$transactionId}");

        if ($response->failed()) {
            return ['status' => 'UNKNOWN', 'message' => 'Could not verify payment status with PesaJet.'];
        }

        $data = $response->json();

        return [
            'status'  => strtoupper($data['status'] ?? 'PENDING'),
            'message' => $data['message'] ?? '',
            'data'    => $data,
        ];
    }

    /**
     * Cryptographically verify HMAC-SHA256 signature of incoming webhook payloads.
     */
    public function verifyWebhookSignature(string $rawPayload, ?string $signature): bool
    {
        if (empty($this->webhookSecret)) {
            Log::info('PESAJET_WEBHOOK_SECRET is empty; accepting webhook without signature check.');
            return true;
        }

        if (empty($signature)) {
            return false;
        }

        $expected = hash_hmac('sha256', $rawPayload, $this->webhookSecret);

        return hash_equals($expected, $signature);
    }

    /**
     * Format Ugandan phone number to E.164 (+256XXXXXXXXX).
     */
    public function formatPhoneNumber(string $phoneNumber): string
    {
        $cleaned = preg_replace('/[\s\-]/', '', $phoneNumber);

        if (str_starts_with($cleaned, '0')) {
            $cleaned = '+256' . substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '256')) {
            $cleaned = '+' . $cleaned;
        } elseif (! str_starts_with($cleaned, '+')) {
            $cleaned = '+' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Detect carrier network from Ugandan phone number.
     * MTN: 077, 078, 076, 079, 039
     * Airtel: 070, 075, 074
     */
    public function detectProvider(string $phoneNumber): string
    {
        $cleaned = preg_replace('/[\s\-\+]/', '', $phoneNumber);

        if (preg_match('/^(256|0)?(77|78|76|79|39)\d{7}$/', $cleaned)) {
            return 'mtn';
        }

        if (preg_match('/^(256|0)?(70|75|74)\d{7}$/', $cleaned)) {
            return 'airtel';
        }

        return 'mtn';
    }
}
