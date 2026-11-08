<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PesaJet API Credentials & Configuration
    |--------------------------------------------------------------------------
    |
    | Official PesaJet Pay REST API configuration.
    | Base URL: https://payments.pesajet.com/api/v1
    | Authentication: X-API-Key header (and Bearer token)
    | Webhooks: HMAC-SHA256 signature verification via X-Webhook-Signature
    |
    */

    'api_key'        => env('PESAJET_API_KEY', ''),
    'webhook_secret' => env('PESAJET_WEBHOOK_SECRET', env('PESAJET_SECRET_KEY', '')),
    'currency'       => env('PESAJET_CURRENCY', 'UGX'),
    'base_url'       => env('PESAJET_BASE_URL', 'https://payments.pesajet.com/api/v1'),
    'timeout'        => (int) env('PESAJET_TIMEOUT', 30),
    'endpoints'      => [
        'sandbox' => env('PESAJET_SANDBOX_URL', 'https://payments.pesajet.com/api/v1'),
        'live'    => env('PESAJET_LIVE_URL', 'https://payments.pesajet.com/api/v1'),
    ],
    'mock_fallback'  => (bool) env('PESAJET_MOCK_FALLBACK', false),
];
