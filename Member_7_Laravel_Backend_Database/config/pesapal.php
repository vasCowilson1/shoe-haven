<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pesapal Environment
    |--------------------------------------------------------------------------
    |
    | Supported values: 'sandbox' or 'production'
    |
    */
    'env' => env('PESAPAL_ENV', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Pesapal API Credentials
    |--------------------------------------------------------------------------
    |
    | Your Consumer Key and Consumer Secret obtained from the Pesapal Merchant Portal.
    | Sandbox: https://cybqa.pesapal.com/PesapalIframe/PesapalIframe3/Index
    | Production: https://www.pesapal.com
    |
    */
    'consumer_key' => env('PESAPAL_CONSUMER_KEY', ''),
    'consumer_secret' => env('PESAPAL_CONSUMER_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Pesapal Instant Payment Notification (IPN) ID
    |--------------------------------------------------------------------------
    |
    | The registered IPN ID. You can register one using:
    | php artisan pesapal:register-ipn
    |
    */
    'ipn_id' => env('PESAPAL_IPN_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | Standard currency for transactions (e.g. 'UGX', 'KES', 'USD').
    |
    */
    'currency' => env('PESAPAL_CURRENCY', 'UGX'),

    /*
    |--------------------------------------------------------------------------
    | API Base Endpoints
    |--------------------------------------------------------------------------
    */
    'endpoints' => [
        'sandbox' => 'https://cybqa.pesapal.com/pesapalv3/api',
        'production' => 'https://pay.pesapal.com/v3/api',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Webhook & Callback URLs (Optional overrides)
    |--------------------------------------------------------------------------
    */
    'callback_url' => env('PESAPAL_CALLBACK_URL', null),
    'ipn_url' => env('PESAPAL_IPN_URL', null),

    /*
    |--------------------------------------------------------------------------
    | SSL Verification
    |--------------------------------------------------------------------------
    */
    'ssl_verify' => env('PESAPAL_SSL_VERIFY', false),
];
