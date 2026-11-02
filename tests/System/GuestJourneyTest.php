<?php

namespace Tests\System;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscription;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GuestJourneyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function makeProduct(): Product
    {
        $category = Category::firstOrCreate(['slug' => 'kids'], ['name' => 'Kids']);

        $product = Product::create([
            'name' => 'Guest Explorer Sandal',
            'brand' => 'Sketchers',
            'price' => 75000,
            'image' => 'images/products/shoe-3.jpg',
            'category_id' => $category->id,
        ]);

        ProductStock::create(['product_id' => $product->id, 'quantity' => 8]);

        return $product;
    }

    public function test_guest_can_browse_the_whole_public_site(): void
    {
        $product = $this->makeProduct();

        $this->get('/')->assertOk()->assertSee('Guest Explorer Sandal');
        $this->get('/shop')->assertOk()->assertSee('Guest Explorer Sandal');
        $this->get('/shop?category=kids')->assertOk()->assertSee('Guest Explorer Sandal');
        $this->get('/shop?category=sneakers')->assertOk()->assertOk();
        $this->get("/shop/{$product->id}")->assertOk()->assertSee('Guest Explorer Sandal');
        $this->get('/about')->assertOk();
        $this->get('/contact')->assertOk();
        $this->get('/help')->assertOk();
        $this->get('/privacy')->assertOk();
        $this->get('/terms')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
        $this->get('/sitemap.xml')->assertOk();
    }

    public function test_guest_browse_then_recently_viewed_flow(): void
    {
        $product = $this->makeProduct();

        $this->get("/shop/{$product->id}")
            ->assertOk()
            ->assertCookie('recently_viewed', (string) $product->id);

        $this->withCookie('recently_viewed', (string) $product->id)
            ->get('/recently-viewed')
            ->assertOk()
            ->assertSee('Guest Explorer Sandal');
    }

    public function test_guest_contact_form_stores_message(): void
    {
        $this->post('/contact', [
            'name' => 'Guest Visitor',
            'email' => 'guest@example.com',
            'subject' => 'Order question',
            'message' => 'When will my order arrive?',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Guest Visitor',
            'email' => 'guest@example.com',
            'subject' => 'Order question',
            'message' => 'When will my order arrive?',
        ]);
    }

    public function test_guest_can_subscribe_to_newsletter(): void
    {
        $this->post('/newsletter/subscribe', [
            'email' => 'newsletter-fan@example.com',
        ])->assertRedirect();

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'newsletter-fan@example.com',
        ]);
    }

    public function test_guest_actions_redirect_to_login(): void
    {
        $product = $this->makeProduct();

        $this->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => null]]])
            ->get('/checkout')
            ->assertRedirect('/login');

        $this->get('/orders')->assertRedirect('/login');
        $this->get('/wishlist')->assertRedirect('/login');
        $this->get('/profile')->assertRedirect('/login');

        $this->post("/wishlist/{$product->id}/toggle")->assertRedirect('/login');
        $this->post("/reviews/{$product->id}", ['rating' => 5, 'comment' => 'Nice'])
            ->assertRedirect('/login');

        $this->assertDatabaseCount('wishlist', 0);
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_guest_cart_flow(): void
    {
        $product = $this->makeProduct();

        $this->post("/cart/{$product->id}/add", ['quantity' => 1, 'size' => '38'])
            ->assertRedirect();

        $this->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => '38']]])
            ->get('/cart')
            ->assertOk()
            ->assertSee('Guest Explorer Sandal');
    }
}
