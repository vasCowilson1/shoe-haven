<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\HeroSection;
use App\Models\InventoryHistory;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesShopData;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;
    use CreatesShopData;

    private function makeAdmin(): User
    {
        $admin = User::factory()->create();
        UserRole::create(['user_id' => $admin->id, 'role' => 'admin']);

        return $admin;
    }

    private function makeRegularUser(): User
    {
        $user = User::factory()->create();
        UserRole::create(['user_id' => $user->id, 'role' => 'user']);

        return $user;
    }

    public function test_admin_routes_redirect_guests_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/admin/products')->assertRedirect('/login');
        $this->get('/admin/hero')->assertRedirect('/login');
    }

    public function test_admin_routes_are_forbidden_for_regular_users(): void
    {
        $user = $this->makeRegularUser();

        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($user)->get('/admin/orders')->assertForbidden();
    }

    public function test_admin_dashboard_is_accessible_for_admin(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_admin_can_create_product_with_stock(): void
    {
        $admin = $this->makeAdmin();
        $category = $this->makeCategory('Sneakers', 'sneakers');

        $response = $this->actingAs($admin)->post('/admin/products', [
            'name' => 'Speed Runner',
            'brand' => 'NikeStyle',
            'price' => 280000,
            'original_price' => '',
            'discount' => 0,
            'category_id' => $category->id,
            'image' => 'https://picsum.photos/seed/sr1/600/600',
            'stock' => 25,
            'is_new' => 1,
        ]);

        $response->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('products', ['name' => 'Speed Runner', 'brand' => 'NikeStyle', 'price' => 280000]);
        $this->assertDatabaseHas('product_stock', ['quantity' => 25]);
    }

    public function test_admin_product_creation_requires_valid_image_url(): void
    {
        $admin = $this->makeAdmin();
        $category = $this->makeCategory('Sneakers', 'sneakers');

        $response = $this->actingAs($admin)->post('/admin/products', [
            'name' => 'Bad Image Product',
            'brand' => 'NikeStyle',
            'price' => 100000,
            'category_id' => $category->id,
            'image' => 'not-a-url',
            'stock' => 5,
        ]);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseMissing('products', ['name' => 'Bad Image Product']);
    }

    public function test_admin_can_update_product(): void
    {
        $admin = $this->makeAdmin();
        $product = $this->makeProductWithStock(['name' => 'Old Name']);

        $response = $this->actingAs($admin)->put("/admin/products/{$product->id}", [
            'name' => 'New Name',
            'brand' => 'PumaForce',
            'price' => 999000,
            'original_price' => '',
            'discount' => 5,
            'category_id' => $product->category_id,
            'image' => 'https://picsum.photos/seed/x2/600/600',
            'stock' => 30,
            'is_new' => 0,
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Name',
            'brand' => 'PumaForce',
            'price' => 999000,
        ]);
        $this->assertDatabaseHas('product_stock', ['product_id' => $product->id, 'quantity' => 30]);
    }

    public function test_admin_can_delete_product_without_orders(): void
    {
        $admin = $this->makeAdmin();
        $product = $this->makeProductWithStock();

        $response = $this->actingAs($admin)->delete("/admin/products/{$product->id}");

        $response->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_cannot_delete_product_referenced_by_orders(): void
    {
        $admin = $this->makeAdmin();
        $user = User::factory()->create();
        $product = $this->makeProductWithStock();

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 100000,
            'payment_method' => 'MTN Mobile Money',
            'payment_phone' => '0777000000',
            'status' => 'pending',
            'payment_status' => 'pending',
            'transaction_id' => 'TXN-FKTEST1',
            'shipping_name' => 'John',
            'shipping_address' => 'Kampala',
            'shipping_phone' => '0777000000',
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'price_at_sale' => 100000,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/products/{$product->id}");

        $response->assertRedirect(route('admin.products.index'))
            ->assertSessionHasErrors('error');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_admin_can_update_stock_and_records_history(): void
    {
        $admin = $this->makeAdmin();
        $product = $this->makeProductWithStock([], 10);

        $response = $this->actingAs($admin)->post("/admin/inventory/{$product->id}/stock", ['quantity' => 45]);

        $response->assertRedirect(route('admin.inventory.index'));

        $this->assertSame(45, ProductStock::where('product_id', $product->id)->value('quantity'));
        $this->assertDatabaseHas('inventory_history', [
            'product_id' => $product->id,
            'previous_quantity' => 10,
            'new_quantity' => 45,
            'change_amount' => 35,
            'change_type' => 'restock',
            'changed_by' => $admin->id,
        ]);
    }

    public function test_admin_stock_update_rejects_negative_quantity(): void
    {
        $admin = $this->makeAdmin();
        $product = $this->makeProductWithStock([], 10);

        $response = $this->actingAs($admin)->post("/admin/inventory/{$product->id}/stock", ['quantity' => -5]);

        $response->assertSessionHasErrors('quantity');
        $this->assertSame(10, ProductStock::where('product_id', $product->id)->value('quantity'));
    }

    public function test_admin_can_update_hero_section(): void
    {
        $admin = $this->makeAdmin();
        HeroSection::create([
            'headline' => 'Old Headline',
            'subtitle' => 'Old subtitle.',
            'button_text' => 'Shop Now',
            'button_url' => '/shop',
            'secondary_button_text' => 'Explore',
            'secondary_button_url' => '/shop',
            'image' => 'images/hero-banner.jpg',
            'stats' => [],
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post('/admin/hero', [
            'headline' => 'New Headline',
            'subtitle' => 'New subtitle text.',
            'button_text' => 'Buy Now',
            'button_url' => '/shop',
            'secondary_button_text' => 'Explore Collection',
            'secondary_button_url' => '/shop',
            'stat_value_0' => '15K+',
            'stat_label_0' => 'Happy Customers',
        ]);

        $response->assertRedirect(route('admin.hero.edit'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('hero_sections', ['headline' => 'New Headline', 'subtitle' => 'New subtitle text.']);
        $this->assertSame([['value' => '15K+', 'label' => 'Happy Customers']], HeroSection::getActive()->stats);
    }

    public function test_admin_hero_update_creates_hero_when_none_exists(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post('/admin/hero', [
            'headline' => 'Brand New',
            'subtitle' => 'Subtitle',
            'button_text' => 'Shop Now',
            'button_url' => '/shop',
            'secondary_button_text' => 'Explore',
            'secondary_button_url' => '/shop',
        ]);

        $this->assertDatabaseHas('hero_sections', ['headline' => 'Brand New', 'is_active' => true]);
    }

    public function test_admin_can_update_order_status(): void
    {
        $admin = $this->makeAdmin();
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 100000,
            'payment_method' => 'MTN Mobile Money',
            'payment_phone' => '0777000000',
            'status' => 'pending',
            'payment_status' => 'pending',
            'transaction_id' => 'TXN-FKTEST2',
            'shipping_name' => 'John',
            'shipping_address' => 'Kampala',
            'shipping_phone' => '0777000000',
        ]);

        $response = $this->actingAs($admin)->post("/admin/orders/{$order->id}/status", ['status' => 'delivered']);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'delivered']);
    }

    public function test_admin_products_index_shows_products(): void
    {
        $admin = $this->makeAdmin();
        $this->makeProductWithStock(['name' => 'Admin List Shoe']);

        $this->actingAs($admin)->get('/admin/products')
            ->assertOk()
            ->assertSee('Admin List Shoe');
    }

    public function test_admin_users_index_is_accessible(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/admin/users')->assertOk();
    }
}
