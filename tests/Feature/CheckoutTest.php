<?php

namespace Tests\Feature;

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
            'name' => 'John Buyer',
            'phone' => '0777123456',
            'address' => 'Kampala, Uganda',
            'region' => 'Central',
            'district' => 'Kampala',
            'area' => 'Central Division',
            'landmark' => 'Near Shoprite, Nakumatt Building',
        ];
    }

    public function test_checkout_index_redirects_when_cart_is_empty(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['cart' => []])
            ->get('/checkout')
            ->assertRedirect('/cart');
    }

    public function test_checkout_index_renders_when_cart_has_items(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProductWithStock(['name' => 'Checkout Shoe']);

        $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => '40']]])
            ->get('/checkout')
            ->assertOk()
            ->assertSee('Checkout Shoe')
            ->assertSee('Continue to Pesapal');
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
        $user = User::factory()->create();
        $product = $this->makeProductWithStock(['price' => 100000]);
        $stockBefore = ProductStock::where('product_id', $product->id)->value('quantity');

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 2, 'size' => '40']]])
            ->post('/checkout', $this->validCheckoutPayload());

        $order = Order::firstOrFail();
        $response->assertRedirect('https://payments.example.test/checkout');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_amount' => 200000,
            'payment_method' => 'Pesapal',
            'status' => 'pending',
            'payment_status' => 'pending',
            'pesapal_tracking_id' => 'test-tracking-'.$order->id,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'size' => '40',
            'quantity' => 2,
            'price_at_sale' => 100000,
        ]);

        $this->assertNull(session('cart'));
    }

    public function test_checkout_decrements_stock_and_records_inventory_history(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProductWithStock(['price' => 50000], 50);
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
            'product_id' => $product->id,
            'change_type' => 'sale',
            'change_amount' => -3,
            'changed_by' => $user->id,
        ]);
    }

    public function test_checkout_rejects_when_quantity_exceeds_size_stock(): void
    {
        $user = User::factory()->create();
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
        $user = User::factory()->create();
        $product = $this->makeProductWithStock([], 2);

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 10, 'size' => null]]])
            ->post('/checkout', $this->validCheckoutPayload());

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_starts_pesapal_payment_without_client_selected_method(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProductWithStock();

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => '40']]])
            ->post('/checkout', $this->validCheckoutPayload());

        $response->assertRedirect('https://payments.example.test/checkout');
        $this->assertDatabaseHas('orders', ['payment_method' => 'Pesapal']);
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
        $user = User::factory()->create();
        $product = $this->makeProductWithStock(['price' => 250000, 'original_price' => 300000]);

        $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => '41']]])
            ->post('/checkout', $this->validCheckoutPayload());

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 1,
            'price_at_sale' => 250000,
        ]);
    }
}
