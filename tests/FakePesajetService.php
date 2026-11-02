<?php

namespace Tests;

use App\Models\Order;
use App\Services\PesajetService;

class FakePesajetService extends PesajetService
{
    public function __construct() {}

    public function initiatePayment(Order $order, array $billingDetails = []): array
    {
        return [
            'reference' => 'test-ref-' . $order->id,
            'status'    => 'pending',
            'message'   => 'Payment request sent to your phone.',
        ];
    }

    public function verifyPayment(string $reference): array
    {
        return ['status' => 'SUCCESS', 'message' => 'Payment successful'];
    }
}
