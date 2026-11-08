<?php

namespace Tests\System;

use App\Models\Category;
use App\Models\InventoryHistory;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BuyerJourneyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function makeProduct(): Product
    {
        $category = Category::firstOrCreate(['slug' => 'sneakers'], ['name' => 'Sneakers']);

        $product = Product::create([
            'name' => 'Journey Runner Shoe',
            'brand' => 'Nike',
            'price' => 120000,
            'image' => 'images/products/shoe-1.jpg',
            'category_id' => $category->id,
            'is_new' => true,
        ]);

        ProductStock::create(['product_id' => $product->id, 'quantity' => 10]);

        return $product;
    }

    public function test_full_buyer_journey_from_registration_to_review(): void
    {
        $password = 'secret-password';
        $email = 'buyer@example.com';

        $this->post('/register', [
            'name' => 'Buyer One',
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ])->assertRedirect('/');

        $this->assertAuthenticated();

        $product = $this->makeProduct();

        $this->get('/')->assertOk()->assertSee($product->name);
        $this->get('/shop')->assertOk()->assertSee($product->name);
        $this->get("/shop/{$product->id}")->assertOk()->assertSee('Journey Runner Shoe');

        $this->post("/cart/{$product->id}/add", ['quantity' => 2, 'size' => '42'])
            ->assertRedirect();

        $this->withSession(['cart' => [$product->id => ['quantity' => 2, 'size' => '42']]])
            ->get('/cart')
            ->assertOk()
            ->assertSee('Journey Runner Shoe');

        $this->withSession(['cart' => [$product->id => ['quantity' => 2, 'size' => null]]])
            ->post('/checkout', [
                'name' => 'Buyer One',
                'phone' => '+256 700 111 111',
                'address' => 'Kampala Road, Kampala',
                'region' => 'Central',
                'district' => 'Kampala',
                'area' => 'Kawempe Division',
                'landmark' => 'Opposite Kawempe Market',
                'payment_method' => 'mtn',
            ])
            ->assertRedirect();

        $order = Order::where('user_id', $this->app->make('auth')->id())->latest('id')->first();
        $this->assertNotNull($order);
        $this->assertSame('pending', $order->status);

        $this->get("/orders/{$order->id}/confirmation")
            ->assertOk()
            ->assertSee('Payment pending')
            ->assertSee('#' . $order->id);

        $this->get('/orders')
            ->assertOk()
            ->assertSee('#' . $order->id);

        $this->get("/orders/{$order->id}")
            ->assertOk()
            ->assertSee('Journey Runner Shoe')
            ->assertSee('2');

        $this->post("/reviews/{$product->id}", [
            'rating' => 5,
            'content' => 'Excellent quality and fast delivery.',
        ])->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'product_id' => $product->id,
            'rating' => 5,
            'content' => 'Excellent quality and fast delivery.',
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_buyer_journey_decrements_stock_and_records_inventory_history(): void
    {
        $user = \App\Models\User::factory()->create();
        $product = $this->makeProduct();
        $product->sizeStock()->create(['size' => '42', 'quantity' => 10]);

        $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 3, 'size' => '42']]])
            ->post('/checkout', [
                'name' => $user->name,
                'phone' => '+256 700 222 222',
                'address' => 'Entebbe Road',
                'region' => 'Central',
                'district' => 'Wakiso',
                'area' => 'Kira',
                'landmark' => 'Kira Road, near Kira Police Station',
                'payment_method' => 'airtel',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('product_stock', [
            'product_id' => $product->id,
            'quantity' => 7,
        ]);

        $this->assertDatabaseHas('product_size_stock', [
            'product_id' => $product->id,
            'size' => '42',
            'quantity' => 7,
        ]);

        $this->assertDatabaseHas('inventory_history', [
            'product_id' => $product->id,
            'change_type' => 'sale',
            'change_amount' => -3,
        ]);
    }

    public function test_buyer_cannot_view_another_buyers_order(): void
    {
        $owner = \App\Models\User::factory()->create();
        $other = \App\Models\User::factory()->create();
        $product = $this->makeProduct();

        $this->actingAs($owner)
            ->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => null]]])
            ->post('/checkout', [
                'name' => $owner->name,
                'phone' => '+256 700 333 333',
                'address' => 'Kololo',
                'region' => 'Central',
                'district' => 'Kampala',
                'area' => 'Nakawa Division',
                'landmark' => 'Kololo, Acacia Avenue',
                'payment_method' => 'mtn',
            ]);

        $order = Order::where('user_id', $owner->id)->latest('id')->first();

        $this->actingAs($other)->get("/orders/{$order->id}")->assertForbidden();
        $this->actingAs($other)->get("/orders/{$order->id}/confirmation")->assertForbidden();
    }

    public function test_checkout_rejects_oversized_quantity(): void
    {
        $user = \App\Models\User::factory()->create();
        $product = $this->makeProduct();

        $this->actingAs($user)
            ->withSession(['cart' => [$product->id => ['quantity' => 999, 'size' => null]]])
            ->post('/checkout', [
                'name' => $user->name,
                'phone' => '+256 700 444 444',
                'address' => 'Ntinda',
                'region' => 'Central',
                'district' => 'Kampala',
                'area' => 'Rubaga Division',
                'landmark' => 'Ntinda, Kulambiro Road',
                'payment_method' => 'mtn',
            ])
            ->assertSessionHasErrors('error');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('product_stock', [
            'product_id' => $product->id,
            'quantity' => 10,
        ]);
    }

    public function test_wishlist_flow_for_logged_in_buyer(): void
    {
        $user = \App\Models\User::factory()->create();
        $product = $this->makeProduct();

        $this->actingAs($user)->post("/wishlist/{$product->id}/toggle")->assertRedirect();

        $this->assertDatabaseHas('wishlist', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($user)->get('/wishlist')->assertOk()->assertSee('Journey Runner Shoe');
    }
}
