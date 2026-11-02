<?php

return [
    'api_key'      => env('PESAJET_API_KEY', ''),
    'secret_key'   => env('PESAJET_SECRET_KEY', ''),
    'currency'     => env('PESAJET_CURRENCY', 'UGX'),
    'callback_url' => env('PESAJET_CALLBACK_URL', ''),
    'env'          => env('PESAJET_ENV', 'sandbox'),
    'endpoints'    => [
        'sandbox' => 'https://pay.pesajet.com/api/v1',
        'live'    => 'https://pay.pesajet.com/api/v1',
    ],
];
