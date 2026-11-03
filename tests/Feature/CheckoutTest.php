<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\InventoryHistory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSizeStock;
use App\Models\ProductStock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesShopData;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;
    use CreatesShopData;

    private function validCheckoutPayload(): array
    {
        return [
            'name'     => 'John Buyer',
            'phone'    => '0777123456',
            'address'  => 'Kampala, Uganda',
            'region'   => 'Central',
            'district' => 'Kampala',
            'area'     => 'Central Division',
            'landmark' => 'Near Shoprite, Nakumatt Building',
            'provider' => 'MTN',
        ];
    }

    public function test_checkout_index_redirects_when_cart_is_empty(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['cart' => []])
            ->get('/checkout')
            ->assertRedirect('/cart')
            ->assertSessionHas('status', 'Your cart is empty. Add an item before checking out.');
    }

    public function test_checkout_index_renders_when_cart_has_items(): void
    {
        $user    = User::factory()->create();
        $product = $this->makeProductWithStock(['name' => 'Checkout Shoe']);

        $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => '40']]])
            ->get('/checkout')
            ->assertOk()
            ->assertSee('Checkout Shoe')
            ->assertSee('Pay with PesaJet')
            ->assertSee('Online payment is temporarily unavailable while our service is under maintenance.')
            ->assertSee('0768233701')
            ->assertSee('0743034372');
    }

    public function test_pending_payment_confirmation_does_not_claim_order_is_paid(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProductWithStock();

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => '40']]])
            ->post('/checkout', $this->validCheckoutPayload());

        $order = Order::firstOrFail();
        $response->assertRedirect(route('orders.confirmation', $order));

        $this->actingAs($user)
            ->get(route('orders.confirmation', $order))
            ->assertOk()
            ->assertSee('Payment pending')
            ->assertSee('Approve the mobile-money prompt sent')
            ->assertDontSee('Order Confirmed!');
    }

    public function test_checkout_reports_payment_initiation_failure_and_keeps_cart(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProductWithStock();
        $service = \Mockery::mock(\App\Services\PesajetService::class)->makePartial();
        $service->shouldReceive('initiatePayment')->once()->andThrow(new \RuntimeException('provider secret detail'));
        $this->app->instance(\App\Services\PesajetService::class, $service);

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => '40']]])
            ->post('/checkout', $this->validCheckoutPayload());

        $response->assertSessionHasErrors([
            'payment' => 'We could not start the mobile-money payment. Your cart has been kept; please check your details and try again.',
        ])->assertDontSee('provider secret detail');
        $this->assertSame(
            1,
            Cart::forUser($user->id)->items()->where('product_id', $product->id)->value('quantity')
        );
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_sends_the_selected_mobile_money_provider(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProductWithStock();
        $service = \Mockery::mock(\App\Services\PesajetService::class)->makePartial();
        $service->shouldReceive('initiatePayment')
            ->once()
            ->withArgs(fn (Order $order, array $details) => $details['provider'] === 'Airtel')
            ->andReturn(['reference' => 'airtel-test-ref', 'status' => 'PENDING']);
        $this->app->instance(\App\Services\PesajetService::class, $service);

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => '40']]])
            ->post('/checkout', [...$this->validCheckoutPayload(), 'provider' => 'Airtel']);

        $response->assertRedirect(route('orders.confirmation', Order::firstOrFail()));
    }

    public function test_failed_payment_initiation_response_displays_retry_state(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProductWithStock();
        $service = \Mockery::mock(\App\Services\PesajetService::class)->makePartial();
        $service->shouldReceive('initiatePayment')
            ->once()
            ->andReturn(['reference' => 'failed-test-ref', 'status' => 'FAILED']);
        $this->app->instance(\App\Services\PesajetService::class, $service);

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => '40']]])
            ->post('/checkout', $this->validCheckoutPayload());

        $order = Order::firstOrFail();
        $response->assertRedirect(route('orders.confirmation', $order));
        $this->assertSame('failed', $order->payment_status);

        $this->actingAs($user)
            ->get(route('orders.confirmation', $order))
            ->assertOk()
            ->assertSee('Payment was not completed')
            ->assertSee('Retry Payment');
    }

    public function test_payment_status_check_updates_the_order_state(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProductWithStock();

        $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => '40']]])
            ->post('/checkout', $this->validCheckoutPayload());

        $order = Order::firstOrFail();

        $this->actingAs($user)
            ->getJson(route('orders.pay.status', $order))
            ->assertOk()
            ->assertJson([
                'payment_status' => 'paid',
                'status_check_failed' => false,
            ]);

        $this->assertSame('paid', $order->fresh()->payment_status);
    }

    public function test_checkout_index_requires_authentication(): void
    {
        $product = $this->makeProductWithStock();

        $this->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => '40']]])
            ->get('/checkout')
            ->assertRedirect('/login');
    }

    public function test_checkout_creates_order_and_clears_cart(): void
    {
        $user    = User::factory()->create();
        $product = $this->makeProductWithStock(['price' => 100000]);

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 2, 'size' => '40']]])
            ->post('/checkout', $this->validCheckoutPayload());

        $order = Order::firstOrFail();
        $response->assertRedirect(route('orders.confirmation', $order));

        $this->assertDatabaseHas('orders', [
            'user_id'        => $user->id,
            'total_amount'   => 200000,
            'payment_method' => 'PesaJet',
            'status'         => 'pending',
            'payment_status' => 'pending',
            'pesajet_reference' => 'test-ref-' . $order->id,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id'   => $product->id,
            'size'         => '40',
            'quantity'     => 2,
            'price_at_sale'=> 100000,
        ]);

        $this->assertNull(session('cart'));
    }

    public function test_checkout_decrements_stock_and_records_inventory_history(): void
    {
        $user      = User::factory()->create();
        $product   = $this->makeProductWithStock(['price' => 50000], 50);
        $sizeBefore = ProductSizeStock::where('product_id', $product->id)->where('size', '40')->value('quantity');

        $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 3, 'size' => '40']]])
            ->post('/checkout', $this->validCheckoutPayload());

        $this->assertSame(47, ProductStock::where('product_id', $product->id)->value('quantity'));
        $this->assertSame(
            $sizeBefore - 3,
            ProductSizeStock::where('product_id', $product->id)->where('size', '40')->value('quantity')
        );

        $this->assertDatabaseHas('inventory_history', [
            'product_id'  => $product->id,
            'change_type' => 'sale',
            'change_amount'=> -3,
            'changed_by'  => $user->id,
        ]);
    }

    public function test_checkout_rejects_when_quantity_exceeds_size_stock(): void
    {
        $user    = User::factory()->create();
        $product = $this->makeProductWithStock();
        ProductSizeStock::where('product_id', $product->id)->where('size', '40')->update(['quantity' => 1]);

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 5, 'size' => '40']]])
            ->post('/checkout', $this->validCheckoutPayload());

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(20, ProductStock::where('product_id', $product->id)->value('quantity'));
    }

    public function test_checkout_rejects_when_quantity_exceeds_total_stock(): void
    {
        $user    = User::factory()->create();
        $product = $this->makeProductWithStock([], 2);

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 10, 'size' => null]]])
            ->post('/checkout', $this->validCheckoutPayload());

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_starts_pesajet_payment_without_client_selected_method(): void
    {
        $user    = User::factory()->create();
        $product = $this->makeProductWithStock();

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => '40']]])
            ->post('/checkout', $this->validCheckoutPayload());

        $order = Order::firstOrFail();
        $response->assertRedirect(route('orders.confirmation', $order));
        $this->assertDatabaseHas('orders', ['payment_method' => 'PesaJet']);
    }

    public function test_checkout_redirects_to_cart_when_cart_empty(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['cart' => []])
            ->post('/checkout', $this->validCheckoutPayload())
            ->assertRedirect('/cart');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_item_creation_uses_sale_price(): void
    {
        $user    = User::factory()->create();
        $product = $this->makeProductWithStock(['price' => 250000, 'original_price' => 300000]);

        $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => '41']]])
            ->post('/checkout', $this->validCheckoutPayload());

        $this->assertDatabaseHas('order_items', [
            'product_id'   => $product->id,
            'quantity'     => 1,
            'price_at_sale'=> 250000,
        ]);
    }
}
