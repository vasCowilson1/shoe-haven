<?php

namespace Tests\Feature;

use App\Models\ProductStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesShopData;
use Tests\TestCase;

class CartFlowTest extends TestCase
{
    use RefreshDatabase;
    use CreatesShopData;

    public function test_cart_index_renders_empty_cart(): void
    {
        $this->withSession(['cart' => []])->get('/cart')->assertOk();
    }

    public function test_product_can_be_added_to_cart_with_size(): void
    {
        $product = $this->makeProductWithStock(['name' => 'Air Max']);

        $response = $this->post("/cart/{$product->id}/add", ['quantity' => 2, 'size' => '40']);

        $response->assertRedirect();
        $this->assertSame([
            $product->id => ['quantity' => 2, 'size' => '40'],
        ], session('cart'));
    }

    public function test_product_can_be_added_without_size_when_product_has_no_sizes(): void
    {
        $product = $this->makeProduct();
        ProductStock::create(['product_id' => $product->id, 'quantity' => 20]);

        $this->post("/cart/{$product->id}/add", ['quantity' => 1]);

        $this->assertSame(1, session('cart')[$product->id]['quantity']);
        $this->assertNull(session('cart')[$product->id]['size']);
    }

    public function test_sized_product_requires_size_selection(): void
    {
        $product = $this->makeProductWithStock();

        $this->post("/cart/{$product->id}/add", ['quantity' => 1])
            ->assertSessionHasErrors('size');

        $this->assertNull(session('cart'));
    }

    public function test_adding_same_product_increments_quantity(): void
    {
        $product = $this->makeProductWithStock();

        $this->post("/cart/{$product->id}/add", ['quantity' => 2, 'size' => '40']);
        $this->post("/cart/{$product->id}/add", ['quantity' => 3, 'size' => '40']);

        $this->assertSame(5, session('cart')[$product->id]['quantity']);
    }

    public function test_quantity_can_be_updated(): void
    {
        $product = $this->makeProductWithStock();

        $this->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => '40']]])
            ->patch("/cart/{$product->id}", ['quantity' => 4]);

        $this->assertSame(4, session('cart')[$product->id]['quantity']);
    }

    public function test_product_can_be_removed_from_cart(): void
    {
        $product = $this->makeProductWithStock();

        $this->withSession(['cart' => [$product->id => ['quantity' => 1, 'size' => '40']]])
            ->delete("/cart/{$product->id}");

        $this->assertArrayNotHasKey($product->id, session('cart'));
    }

    public function test_cart_index_shows_products_and_total(): void
    {
        $product = $this->makeProductWithStock(['name' => 'Cart Shoe', 'price' => 50000]);

        $response = $this->withSession(['cart' => [$product->id => ['quantity' => 3, 'size' => '41']]])
            ->get('/cart');

        $response->assertOk()
            ->assertSee('Cart Shoe')
            ->assertSee('150,000');
    }
}
