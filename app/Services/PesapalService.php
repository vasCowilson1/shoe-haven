<?php

namespace App\Services;

use App\Models\Order;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PesapalService
{
    protected string $env;
    protected string $baseUrl;
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $currency;

    public function __construct()
    {
        $this->env = config('pesapal.env', 'sandbox');
        $this->baseUrl = config("pesapal.endpoints.{$this->env}", 'https://cybqa.pesapal.com/pesapalv3/api');
        $this->consumerKey = (string) config('pesapal.consumer_key', '');
        $this->consumerSecret = (string) config('pesapal.consumer_secret', '');
        $this->currency = (string) config('pesapal.currency', 'UGX');
    }

    /**
     * Get a configured HTTP client instance.
     */
    protected function http(): PendingRequest
    {
        $client = Http::acceptJson()->timeout(25);

        // In sandbox or when ssl_verify is disabled, bypass SSL validation for local dev environments
        if (! config('pesapal.ssl_verify', true) || $this->env === 'sandbox') {
            $client = $client->withoutVerifying();
        }

        return $client;
    }

    /**
     * Get the base API URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Verify that credentials are configured.
     */
    protected function ensureConfigured(): void
    {
        if (empty($this->consumerKey) || empty($this->consumerSecret)) {
            throw new Exception('Pesapal API credentials not configured. Please set PESAPAL_CONSUMER_KEY and PESAPAL_CONSUMER_SECRET in your .env file.');
        }
    }

    /**
     * Request an access token from Pesapal v3 API and cache it.
     */
    public function getAuthToken(): string
    {
        $this->ensureConfigured();

        $cacheKey = "pesapal_auth_token_{$this->env}_" . substr(md5($this->consumerKey), 0, 8);

        return Cache::remember($cacheKey, now()->addMinutes(4), function () {
            $url = "{$this->baseUrl}/Auth/RequestToken";

            $response = $this->http()
                ->asJson()
                ->post($url, [
                    'consumer_key' => $this->consumerKey,
                    'consumer_secret' => $this->consumerSecret,
                ]);

            if ($response->failed()) {
                Log::error('Pesapal Auth Token Request Failed', [
                    'status' => $response->status(),
                    'body' => $response->json() ?? $response->body(),
                ]);
                $errorMsg = $response->json('message') ?? $response->json('error.message') ?? 'Failed to authenticate with Pesapal API.';
                throw new Exception("Pesapal Authentication Error: {$errorMsg}");
            }

            $data = $response->json();
            $token = $data['token'] ?? null;

            if (! $token) {
                throw new Exception('Pesapal Authentication failed: Token not found in response.');
            }

            return $token;
        });
    }

    /**
     * Register an Instant Payment Notification (IPN) URL with Pesapal.
     */
    public function registerIpnUrl(?string $url = null): string
    {
        $ipnUrl = $url ?: (config('pesapal.ipn_url') ?: route('pesapal.ipn'));
        $token = $this->getAuthToken();

        $endpoint = "{$this->baseUrl}/URLSetup/RegisterIPN";

        $response = $this->http()
            ->withToken($token)
            ->asJson()
            ->post($endpoint, [
                'url' => $ipnUrl,
                'ipn_notification_type' => 'GET',
            ]);

        if ($response->failed()) {
            Log::error('Pesapal Register IPN Failed', [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ]);
            $errorMsg = $response->json('message') ?? 'Failed to register IPN with Pesapal.';
            throw new Exception("Pesapal IPN Registration Error: {$errorMsg}");
        }

        $data = $response->json();
        $ipnId = $data['ipn_id'] ?? null;

        if (! $ipnId) {
            throw new Exception('Pesapal IPN registration did not return an IPN ID.');
        }

        Cache::put("pesapal_ipn_id_{$this->env}", $ipnId, now()->addDays(30));

        return $ipnId;
    }

    /**
     * Get the active IPN ID from config or cache, or register one on the fly.
     */
    public function getRegisteredIpnId(): string
    {
        $configuredId = config('pesapal.ipn_id');
        if (! empty($configuredId)) {
            return $configuredId;
        }

        $cachedId = Cache::get("pesapal_ipn_id_{$this->env}");
        if (! empty($cachedId)) {
            return $cachedId;
        }

        return $this->registerIpnUrl();
    }

    /**
     * Submit an order to Pesapal v3 to generate the secure payment redirect URL.
     *
     * @param  Order  $order
     * @param  array  $billingDetails  ['name' => ..., 'email' => ..., 'phone' => ..., 'address' => ..., 'area' => ..., 'district' => ..., 'region' => ..., 'landmark' => ...]
     * @return array{redirect_url: string, order_tracking_id: string, merchant_reference: string}
     */
    public function submitOrder(Order $order, array $billingDetails = []): array
    {
        $token = $this->getAuthToken();
        $notificationId = $this->getRegisteredIpnId();

        $fullName = trim($billingDetails['name'] ?? ($order->shipping_name ?? 'Customer'));
        $nameParts = preg_split('/\s+/', $fullName, 2);
        $firstName = $nameParts[0] ?? 'ShoeHaven';
        $lastName = $nameParts[1] ?? 'Customer';

        $phone = $billingDetails['phone'] ?? ($order->shipping_phone ?? ($order->payment_phone ?? ''));
        $email = $billingDetails['email'] ?? ($order->user?->email ?? 'buyer@shoehaven.com');

        $line1 = $billingDetails['address'] ?? ($order->shipping_address ?? ($billingDetails['landmark'] ?? ''));
        if (empty($line1)) {
            $line1 = $order->shipping_area ?? 'Delivery Address';
        }

        $city = $billingDetails['area'] ?? ($order->shipping_area ?? ($order->shipping_city ?? 'Kampala'));
        $state = $billingDetails['region'] ?? ($order->shipping_region ?? 'Central');

        $merchantReference = $order->transaction_id ?: ('TXN-SH-' . $order->id);
        $callbackUrl = config('pesapal.callback_url') ?: route('pesapal.callback');

        $payload = [
            'id' => $merchantReference,
            'currency' => $this->currency,
            'amount' => (float) $order->total_amount,
            'description' => "Shoe Haven Order #{$order->id}",
            'callback_url' => $callbackUrl,
            'notification_id' => $notificationId,
            'billing_address' => [
                'email_address' => $email,
                'phone_number' => $phone,
                'country_code' => 'UG',
                'first_name' => $firstName,
                'last_name' => $lastName,
                'line_1' => $line1,
                'line_2' => $billingDetails['landmark'] ?? ($order->shipping_landmark ?? ''),
                'city' => $city,
                'state' => $state,
                'postal_code' => '',
                'zip_code' => '',
            ],
        ];

        $endpoint = "{$this->baseUrl}/Transactions/SubmitOrderRequest";

        $response = $this->http()
            ->withToken($token)
            ->asJson()
            ->post($endpoint, $payload);

        if ($response->failed()) {
            Log::error('Pesapal Submit Order Request Failed', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'response' => $response->json() ?? $response->body(),
            ]);

            $errorMsg = $response->json('message') ?? $response->json('error.message') ?? 'Could not initiate payment with Pesapal.';
            throw new Exception("Pesapal Submission Error: {$errorMsg}");
        }

        $data = $response->json();
        $redirectUrl = $data['redirect_url'] ?? null;
        $orderTrackingId = $data['order_tracking_id'] ?? null;

        if (! $redirectUrl || ! $orderTrackingId) {
            Log::error('Pesapal Submit Order Missing Redirect URL or Tracking ID', ['response' => $data]);
            throw new Exception('Pesapal did not return a valid payment link.');
        }

        return [
            'redirect_url' => $redirectUrl,
            'order_tracking_id' => $orderTrackingId,
            'merchant_reference' => $merchantReference,
        ];
    }

    /**
     * Get the real-time status of a transaction from Pesapal.
     *
     * @param  string  $orderTrackingId
     * @return array
     */
    public function getTransactionStatus(string $orderTrackingId): array
    {
        $token = $this->getAuthToken();
        $endpoint = "{$this->baseUrl}/Transactions/GetTransactionStatus";

        $response = $this->http()
            ->withToken($token)
            ->get($endpoint, [
                'orderTrackingId' => $orderTrackingId,
            ]);

        if ($response->failed()) {
            Log::error('Pesapal Get Transaction Status Failed', [
                'tracking_id' => $orderTrackingId,
                'status' => $response->status(),
                'response' => $response->json() ?? $response->body(),
            ]);

            $errorMsg = $response->json('message') ?? 'Failed to retrieve transaction status from Pesapal.';
            throw new Exception("Pesapal Transaction Status Error: {$errorMsg}");
        }

        return $response->json() ?? [];
    }
}
