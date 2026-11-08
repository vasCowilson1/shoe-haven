<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PesaJet API Credentials & Configuration
    |--------------------------------------------------------------------------
    */

    'api_key'         => env('PESAJET_API_KEY', ''),
    'api_secret'      => env('PESAJET_API_SECRET', ''),
    'webhook_secret'  => env('PESAJET_WEBHOOK_SECRET', env('PESAJET_SECRET_KEY', '')),
    'currency'        => env('PESAJET_CURRENCY', 'UGX'),
    'base_url'        => env('PESAJET_BASE_URL', 'https://payments.pesajet.com/api/v1'),
    'collection_path' => env('PESAJET_COLLECTION_PATH', '/payments'),
    'timeout'         => (int) env('PESAJET_TIMEOUT', 30),
    'endpoints'       => [
        'sandbox' => env('PESAJET_SANDBOX_URL', 'https://payments.pesajet.com/api/v1'),
        'live'    => env('PESAJET_LIVE_URL', 'https://payments.pesajet.com/api/v1'),
    ],
    'mock_fallback'   => (bool) env('PESAJET_MOCK_FALLBACK', false),
];
