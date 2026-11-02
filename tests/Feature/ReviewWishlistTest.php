<?php

namespace Tests\Feature;

use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesShopData;
use Tests\TestCase;

class ReviewWishlistTest extends TestCase
{
    use RefreshDatabase;
    use CreatesShopData;

    public function test_guest_can_see_review_form_prompt(): void
    {
        $product = $this->makeProductWithStock();

        $this->get("/shop/{$product->id}")
            ->assertOk()
            ->assertSee('Log in')
            ->assertSee('leave a review');
    }

    public function test_authenticated_user_can_submit_review(): void
    {
        $user = \App\Models\User::factory()->create();
        $product = $this->makeProductWithStock(['name' => 'Review Shoe']);

        $response = $this->actingAs($user)->post("/reviews/{$product->id}", [
            'rating' => 5,
            'content' => 'Absolutely love these shoes!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => 5,
            'content' => 'Absolutely love these shoes!',
            'status' => 'approved',
        ]);
    }

    public function test_review_requires_rating_between_one_and_five(): void
    {
        $user = \App\Models\User::factory()->create();
        $product = $this->makeProductWithStock();

        $this->actingAs($user)->post("/reviews/{$product->id}", [
            'rating' => 9,
            'content' => 'Too high a rating!',
        ])->assertSessionHasErrors('rating');

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_review_requires_content(): void
    {
        $user = \App\Models\User::factory()->create();
        $product = $this->makeProductWithStock();

        $this->actingAs($user)->post("/reviews/{$product->id}", [
            'rating' => 4,
        ])->assertSessionHasErrors('content');

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_review_appears_on_product_page(): void
    {
        $user = \App\Models\User::factory()->create();
        $product = $this->makeProductWithStock();
        $product->comments()->create([
            'user_id' => $user->id,
            'rating' => 5,
            'content' => 'Fantastic quality.',
            'status' => 'approved',
        ]);

        $this->actingAs($user)->get("/shop/{$product->id}")
            ->assertOk()
            ->assertSee('Fantastic quality.')
            ->assertSee('Reviews (1)');
    }

    public function test_guest_toggle_redirects_to_login(): void
    {
        $product = $this->makeProductWithStock();

        $this->post("/wishlist/{$product->id}/toggle")->assertRedirect('/login');
    }

    public function test_user_can_add_to_wishlist(): void
    {
        $user = \App\Models\User::factory()->create();
        $product = $this->makeProductWithStock();

        $this->actingAs($user)->post("/wishlist/{$product->id}/toggle");

        $this->assertDatabaseHas('wishlist', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_user_can_remove_from_wishlist(): void
    {
        $user = \App\Models\User::factory()->create();
        $product = $this->makeProductWithStock();
        Wishlist::create(['user_id' => $user->id, 'product_id' => $product->id]);

        $this->actingAs($user)->post("/wishlist/{$product->id}/toggle");

        $this->assertDatabaseMissing('wishlist', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_wishlist_page_shows_wishlisted_products(): void
    {
        $user = \App\Models\User::factory()->create();
        $product = $this->makeProductWithStock(['name' => 'Wanted Shoe']);
        Wishlist::create(['user_id' => $user->id, 'product_id' => $product->id]);

        $this->actingAs($user)->get('/wishlist')
            ->assertOk()
            ->assertSee('Wanted Shoe');
    }

    public function test_wishlist_page_is_empty_for_new_user(): void
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)->get('/wishlist')->assertOk();
    }
}
