<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PesajetService
{
    protected string $apiKey;
    protected string $secretKey;
    protected string $currency;
    protected string $callbackUrl;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey      = config('pesajet.api_key', '');
        $this->secretKey   = config('pesajet.secret_key', '');
        $this->currency    = config('pesajet.currency', 'UGX');
        $this->callbackUrl = config('pesajet.callback_url', '');
        $env               = config('pesajet.env', 'sandbox');
        $this->baseUrl     = config("pesajet.endpoints.{$env}", 'https://pay.pesajet.com/api/v1');
    }

    /**
     * Initiate a PesaJet STK-push payment for the given order.
     *
     * @param  Order  $order
     * @param  array  $billingDetails  Must include 'phone' and optionally 'provider' (MTN|Airtel)
     * @return array  ['reference' => string, 'status' => string, 'message' => string]
     *
     * @throws \RuntimeException on configuration or API errors
     */
    public function initiatePayment(Order $order, array $billingDetails = []): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException(
                'PesaJet API key is not configured. Set PESAJET_API_KEY in your .env file.'
            );
        }

        $phone    = $billingDetails['phone'] ?? $order->payment_phone ?? '';
        $provider = $billingDetails['provider'] ?? $this->detectProvider($phone);

        $payload = [
            'amount'             => (int) round($order->total_amount),
            'currency'           => $this->currency,
            'phone_number'       => $this->normalizePhone($phone),
            'provider'           => $provider,
            'external_reference' => $order->transaction_id,
            'callback_url'       => $this->callbackUrl ?: route('pesajet.callback'),
            'description'        => 'Shoe Haven Order #' . $order->id,
            'customer_name'      => $billingDetails['name'] ?? $order->shipping_name ?? 'Customer',
            'customer_email'     => $billingDetails['email'] ?? '',
        ];

        Log::info('PesaJet initiatePayment', ['order_id' => $order->id, 'payload' => $payload]);

        $response = Http::withToken($this->apiKey)
            ->withHeaders(['Accept' => 'application/json'])
            ->post("{$this->baseUrl}/payments/initiate", $payload);

        if ($response->failed()) {
            $body = $response->json() ?? [];
            $msg  = $body['message'] ?? $body['error'] ?? $response->body();
            Log::error('PesaJet API error', ['status' => $response->status(), 'body' => $body]);
            throw new \RuntimeException("PesaJet payment initiation failed: {$msg}");
        }

        $data = $response->json();

        return [
            'reference' => $data['reference'] ?? $data['transaction_id'] ?? $data['data']['reference'] ?? null,
            'status'    => $data['status'] ?? 'pending',
            'message'   => $data['message'] ?? 'Payment request sent to your phone.',
        ];
    }

    /**
     * Query the status of a previously initiated payment.
     *
     * @param  string  $reference
     * @return array  ['status' => 'SUCCESS'|'FAILED'|'PENDING', 'message' => string]
     */
    public function verifyPayment(string $reference): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException(
                'PesaJet API key is not configured. Set PESAJET_API_KEY in your .env file.'
            );
        }

        $response = Http::withToken($this->apiKey)
            ->withHeaders(['Accept' => 'application/json'])
            ->get("{$this->baseUrl}/payments/status/{$reference}");

        if ($response->failed()) {
            return ['status' => 'UNKNOWN', 'message' => 'Could not verify payment status.'];
        }

        $data = $response->json();

        return [
            'status'  => strtoupper($data['status'] ?? 'PENDING'),
            'message' => $data['message'] ?? '',
        ];
    }

    /**
     * Normalise a Ugandan phone number to international format (256XXXXXXXXX).
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            $phone = '256' . substr($phone, 1);
        } elseif (str_starts_with($phone, '+')) {
            $phone = ltrim($phone, '+');
        }

        return $phone;
    }

    /**
     * Guess the mobile money provider from the phone number.
     */
    protected function detectProvider(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        // MTN prefixes: 077, 078, 039, 031
        if (preg_match('/^(256)?(077|078|039|031)/', $phone)) {
            return 'MTN';
        }

        // Airtel prefixes: 070, 075, 074, 020
        if (preg_match('/^(256)?(070|075|074|020)/', $phone)) {
            return 'Airtel';
        }

        return 'MTN'; // default fallback
    }
}
