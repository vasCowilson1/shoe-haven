<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\PesajetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PesajetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'pesajet.api_key'        => 'pk_test_sample_key',
            'pesajet.webhook_secret' => 'whsec_sample_secret',
            'pesajet.base_url'       => 'https://payments.pesajet.com/api/v1',
        ]);
    }

    public function test_phone_number_formatting_and_provider_detection(): void
    {
        $service = new PesajetService();

        $this->assertSame('+256771234567', $service->formatPhoneNumber('0771234567'));
        $this->assertSame('+256771234567', $service->formatPhoneNumber('256771234567'));
        $this->assertSame('+256771234567', $service->formatPhoneNumber('+256771234567'));

        $this->assertSame('mtn', $service->detectProvider('0771234567'));
        $this->assertSame('mtn', $service->detectProvider('0781234567'));
        $this->assertSame('mtn', $service->detectProvider('0761234567'));
        $this->assertSame('airtel', $service->detectProvider('0701234567'));
        $this->assertSame('airtel', $service->detectProvider('0751234567'));
    }

    public function test_service_initiate_payment_sends_official_payload(): void
    {
        Http::fake([
            'https://payments.pesajet.com/api/v1/payments' => Http::response([
                'transactionId' => 'txn_123456789',
                'status'        => 'PENDING',
                'reference'     => 'TXN-ABC123XYZ',
                'message'       => 'Prompt sent to customer',
            ], 200),
        ]);

        $user = User::factory()->create();
        $order = Order::create([
            'user_id'              => $user->id,
            'total_amount'         => 150000,
            'payment_method'       => 'PesaJet',
            'payment_phone'        => '0777123456',
            'status'               => 'pending',
            'payment_status'       => 'pending',
            'transaction_id'       => 'TXN-ABC123XYZ',
            'payment_initiated_at' => now(),
            'shipping_name'        => 'Alice',
            'shipping_city'        => 'Kampala',
            'shipping_region'      => 'Central',
            'shipping_district'    => 'Kampala',
            'shipping_area'        => 'Central Division',
            'shipping_landmark'    => 'Clock Tower',
            'shipping_phone'       => '0777123456',
        ]);

        $service = new PesajetService();
        $result = $service->initiatePayment($order, [
            'phone'    => '0777123456',
            'provider' => 'mtn',
            'name'     => 'Alice',
            'email'    => 'alice@example.com',
        ]);

        $this->assertSame('txn_123456789', $result['transaction_id']);
        $this->assertSame('PENDING', $result['status']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://payments.pesajet.com/api/v1/payments'
                && $request->header('X-API-Key')[0] === 'pk_test_sample_key'
                && $request['type'] === 'COLLECTION'
                && $request['amount'] === 150000
                && $request['currency'] === 'UGX'
                && $request['phoneNumber'] === '+256777123456'
                && $request['provider'] === 'mtn'
                && $request['reference'] === 'TXN-ABC123XYZ';
        });
    }

    public function test_service_verify_payment_calls_correct_endpoint(): void
    {
        Http::fake([
            'https://payments.pesajet.com/api/v1/payments/txn_123456789' => Http::response([
                'transactionId' => 'txn_123456789',
                'status'        => 'COMPLETED',
                'amount'        => 150000,
            ], 200),
        ]);

        $service = new PesajetService();
        $result = $service->verifyPayment('txn_123456789');

        $this->assertSame('COMPLETED', $result['status']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://payments.pesajet.com/api/v1/payments/txn_123456789'
                && $request->header('X-API-Key')[0] === 'pk_test_sample_key';
        });
    }

    public function test_webhook_callback_processes_completed_payment_with_valid_signature(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id'              => $user->id,
            'total_amount'         => 80000,
            'payment_method'       => 'PesaJet',
            'payment_phone'        => '0777123456',
            'status'               => 'pending',
            'payment_status'       => 'pending',
            'transaction_id'       => 'TXN-WH-001',
            'pesajet_reference'    => 'txn_wh_001',
            'payment_initiated_at' => now(),
            'shipping_name'        => 'Bob',
            'shipping_city'        => 'Kampala',
            'shipping_region'      => 'Central',
            'shipping_district'    => 'Kampala',
            'shipping_area'        => 'Central Division',
            'shipping_landmark'    => 'Shoprite',
            'shipping_phone'       => '0777123456',
        ]);

        $payload = json_encode([
            'event'         => 'payment.completed',
            'transactionId' => 'txn_wh_001',
            'status'        => 'COMPLETED',
            'amount'        => 80000,
            'currency'      => 'UGX',
            'reference'     => 'TXN-WH-001',
        ]);

        $signature = hash_hmac('sha256', $payload, 'whsec_sample_secret');

        $response = $this->call(
            'POST',
            route('pesajet.callback'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE'           => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertOk();
        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('processing', $order->fresh()->status);
    }

    public function test_webhook_callback_rejects_tampered_payload_signature(): void
    {
        $payload = json_encode([
            'event'         => 'payment.completed',
            'transactionId' => 'txn_wh_999',
            'status'        => 'COMPLETED',
        ]);

        $response = $this->call(
            'POST',
            route('pesajet.callback'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE'           => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => 'invalid_tampered_signature',
            ],
            $payload
        );

        $response->assertStatus(401);
    }
}

