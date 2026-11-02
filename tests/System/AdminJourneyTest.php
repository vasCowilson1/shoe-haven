<?php

namespace Tests\System;

use App\Models\Category;
use App\Models\HeroSection;
use App\Models\InventoryHistory;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminJourneyTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->roles()->create(['role' => 'admin']);

        return $admin;
    }

    private function makeBuyerOrder(): Order
    {
        $buyer = User::factory()->create();
        $category = Category::firstOrCreate(['slug' => 'men'], ['name' => 'Men']);
        $product = Product::create([
            'name' => 'Admin Managed Shoe',
            'brand' => 'Adidas',
            'price' => 90000,
            'image' => 'images/products/shoe-2.jpg',
            'category_id' => $category->id,
        ]);
        ProductStock::create(['product_id' => $product->id, 'quantity' => 5]);

        $this->actingAs($buyer)
            ->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => null]]])
            ->post('/checkout', [
                'name' => $buyer->name,
                'phone' => '+256 700 555 555',
                'address' => 'Muyenga',
                'region' => 'Central',
                'district' => 'Kampala',
                'area' => 'Makindye Division',
                'landmark' => 'Muyenga, Tank Hill Road',
                'payment_method' => 'mtn',
            ]);

        return Order::where('user_id', $buyer->id)->latest('id')->first();
    }

    public function test_full_admin_journey_manage_product_order_inventory_and_hero(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $this->get('/admin')->assertOk()->assertSee('Dashboard');

        $category = Category::firstOrCreate(['slug' => 'sports'], ['name' => 'Sports']);

        $this->post('/admin/products', [
            'name' => 'Admin Created Trainer',
            'brand' => 'Puma',
            'price' => 150000,
            'original_price' => 180000,
            'discount' => 15,
            'image' => 'https://example.com/trainer.jpg',
            'category_id' => $category->id,
            'stock' => 25,
            'is_new' => 1,
        ])->assertRedirect('/admin/products');

        $product = Product::where('name', 'Admin Created Trainer')->first();
        $this->assertNotNull($product);
        $this->assertSame(25, $product->stock->quantity);
        $this->assertSame(1, (int) $product->is_new);
        $this->assertSame(15, (int) $product->discount);

        $this->get('/admin/products')->assertOk()->assertSee('Admin Created Trainer');
        $this->get('/shop')->assertOk()->assertSee('Admin Created Trainer');
        $this->get("/shop/{$product->id}")->assertOk()->assertSee('Admin Created Trainer');

        $this->post("/admin/inventory/{$product->id}/stock", ['quantity' => 40])
            ->assertRedirect();

        $this->assertDatabaseHas('product_stock', [
            'product_id' => $product->id,
            'quantity' => 40,
        ]);

        $this->assertDatabaseHas('inventory_history', [
            'product_id' => $product->id,
            'change_type' => 'restock',
            'change_amount' => 15,
            'changed_by' => $admin->id,
        ]);

        $this->put("/admin/products/{$product->id}", [
            'name' => 'Admin Created Trainer Pro',
            'brand' => 'Puma',
            'price' => 160000,
            'image' => 'https://example.com/trainer-pro.jpg',
            'category_id' => $category->id,
            'stock' => 40,
        ])->assertRedirect('/admin/products');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Admin Created Trainer Pro',
            'price' => 160000,
        ]);

        $this->get("/shop/{$product->id}")->assertOk()->assertSee('Admin Created Trainer Pro');
    }

    public function test_admin_manages_buyer_order_status(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeBuyerOrder();

        $this->actingAs($admin)->get('/admin/orders')->assertOk()->assertSee('#' . $order->id);
        $this->actingAs($admin)->get("/admin/orders/{$order->id}")->assertOk()->assertSee('Admin Managed Shoe');

        $this->actingAs($admin)
            ->post("/admin/orders/{$order->id}/status", ['status' => 'shipped'])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'shipped']);

        $buyer = User::find($order->user_id);
        $this->actingAs($buyer)->get('/orders')->assertOk()->assertSee('shipped');
    }

    public function test_admin_updates_hero_and_homepage_reflects_it(): void
    {
        $admin = $this->makeAdmin();

        HeroSection::create([
            'headline' => 'Original Headline',
            'subtitle' => 'Original subtitle',
            'button_text' => 'Shop Now',
            'button_url' => '/shop',
            'secondary_button_text' => 'Learn More',
            'secondary_button_url' => '/about',
            'stats' => [['value' => '500+', 'label' => 'Styles']],
            'is_active' => true,
        ]);

        $this->actingAs($admin)->get('/admin/hero')->assertOk()->assertSee('Original Headline');

        $this->actingAs($admin)->post('/admin/hero', [
            'headline' => 'Mega Sale Updated',
            'subtitle' => 'Up to 50% off everything',
            'button_text' => 'Shop The Sale',
            'button_url' => '/shop',
            'secondary_button_text' => 'View Deals',
            'secondary_button_url' => '/shop?sale=1',
            'stat_value_0' => '700+',
            'stat_label_0' => 'Styles',
        ])->assertRedirect('/admin/hero');

        $this->get('/')->assertOk()->assertSee('Mega Sale Updated')->assertSee('Up to 50% off everything');

        $this->assertDatabaseHas('hero_sections', [
            'headline' => 'Mega Sale Updated',
            'subtitle' => 'Up to 50% off everything',
        ]);
    }

    public function test_admin_user_list_shows_buyers_and_roles(): void
    {
        $admin = $this->makeAdmin();
        $buyer = User::factory()->create(['name' => 'Visible Buyer Name']);

        $this->actingAs($admin)->get('/admin/users')->assertOk()->assertSee('Visible Buyer Name');
        $this->actingAs($admin)->get("/admin/users/{$buyer->id}")->assertOk()->assertSee('Visible Buyer Name');
    }

    public function test_regular_user_is_forbidden_from_admin_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($user)->post('/admin/products')->assertForbidden();
        $this->actingAs($user)->get('/admin/orders')->assertForbidden();
    }
}
