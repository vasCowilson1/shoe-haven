<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesShopData;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;
    use CreatesShopData;

    private function makeOrder(User $user, array $overrides = []): Order
    {
        return Order::create(array_merge([
            'user_id' => $user->id,
            'total_amount' => 150000,
            'payment_method' => 'MTN Mobile Money',
            'payment_phone' => '0777123456',
            'status' => 'pending',
            'payment_status' => 'pending',
            'transaction_id' => 'TXN-' . strtoupper(substr(uniqid(), -8)),
            'payment_initiated_at' => now(),
            'shipping_name' => 'John Buyer',
            'shipping_address' => 'Kampala, Uganda',
            'shipping_phone' => '0777123456',
        ], $overrides));
    }

    public function test_orders_index_requires_authentication(): void
    {
        $this->get('/orders')->assertRedirect('/login');
    }

    public function test_orders_index_lists_only_own_orders(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $myOrder = $this->makeOrder($owner);
        $this->makeOrder($other);

        $response = $this->actingAs($owner)->get('/orders');

        $response->assertOk()
            ->assertSee('#' . $myOrder->id);
    }

    public function test_order_detail_page_renders(): void
    {
        $user = User::factory()->create();
        $order = $this->makeOrder($user);
        $product = $this->makeProductWithStock(['name' => 'Ordered Shoe', 'price' => 50000]);
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 3,
            'price_at_sale' => 50000,
        ]);

        $response = $this->actingAs($user)->get("/orders/{$order->id}");

        $response->assertOk()
            ->assertSee('Ordered Shoe')
            ->assertSee('150,000');
    }

    public function test_user_cannot_view_another_users_order(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $order = $this->makeOrder($owner);

        $this->actingAs($intruder)->get("/orders/{$order->id}")->assertForbidden();
    }

    public function test_confirmation_page_requires_authentication(): void
    {
        $user = User::factory()->create();
        $order = $this->makeOrder($user);

        $this->get("/orders/{$order->id}/confirmation")->assertRedirect('/login');
    }

    public function test_confirmation_page_renders_for_owner(): void
    {
        $user = User::factory()->create();
        $order = $this->makeOrder($user);
        $product = $this->makeProductWithStock(['name' => 'Confirmed Shoe']);
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'price_at_sale' => 100000,
        ]);

        $response = $this->actingAs($user)->get("/orders/{$order->id}/confirmation");

        $response->assertOk()
            ->assertSee('Order Confirmed!')
            ->assertSee('#' . $order->id)
            ->assertSee('View Order');
    }

    public function test_confirmation_page_denies_non_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $order = $this->makeOrder($owner);

        $this->actingAs($other)->get("/orders/{$order->id}/confirmation")->assertForbidden();
    }

    public function test_order_status_defaults_to_pending(): void
    {
        $user = User::factory()->create();
        $order = $this->makeOrder($user);

        $this->assertSame('pending', $order->status);
        $this->assertSame('pending', $order->payment_status);
    }
}
