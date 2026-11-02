<?php

namespace Tests;

use App\Models\Order;
use App\Services\PesapalService;

class FakePesapalService extends PesapalService
{
    public function __construct() {}

    public function submitOrder(Order $order, array $billingDetails = []): array
    {
        return [
            'redirect_url' => 'https://payments.example.test/checkout',
            'order_tracking_id' => 'test-tracking-'.$order->id,
            'merchant_reference' => $order->transaction_id,
        ];
    }
}
