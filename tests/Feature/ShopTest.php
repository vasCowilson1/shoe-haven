<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSizeStock;
use App\Models\ProductStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesShopData;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;
    use CreatesShopData;

    public function test_shop_index_renders_and_lists_products(): void
    {
        $this->makeProductWithStock(['name' => 'Cloud Walker']);

        $response = $this->get('/shop');

        $response->assertOk()
            ->assertSee('Cloud Walker')
            ->assertSee('Sneakers');
    }

    public function test_shop_index_filters_by_category_slug(): void
    {
        $sneakers = $this->makeCategory('Sneakers', 'sneakers');
        $men = $this->makeCategory('Men', 'men');
        $this->makeProductWithStock(['name' => 'Sneaker Only', 'category_id' => $sneakers->id]);
        $this->makeProductWithStock(['name' => 'Men Boot', 'category_id' => $men->id]);

        $response = $this->get('/shop?category=sneakers');

        $response->assertOk()
            ->assertSee('Sneaker Only')
            ->assertDontSee('Men Boot');
    }

    public function test_shop_index_filters_by_brand(): void
    {
        $this->makeProductWithStock(['name' => 'Nike Style Shoe', 'brand' => 'NikeStyle']);
        $this->makeProductWithStock(['name' => 'Adidas Shoe', 'brand' => 'AdidasPro']);

        $response = $this->get('/shop?brand=NikeStyle');

        $response->assertOk()
            ->assertSee('Nike Style Shoe')
            ->assertDontSee('Adidas Shoe');
    }

    public function test_shop_index_searches_by_name(): void
    {
        $this->makeProductWithStock(['name' => 'Ultra Boost Run']);
        $this->makeProductWithStock(['name' => 'Casual Loafer']);

        $response = $this->get('/shop?search=Boost');

        $response->assertOk()
            ->assertSee('Ultra Boost Run')
            ->assertDontSee('Casual Loafer');
    }

    public function test_shop_index_sorts_by_price_ascending(): void
    {
        $this->makeProductWithStock(['name' => 'Cheap Shoe', 'price' => 100000]);
        $this->makeProductWithStock(['name' => 'Pricey Shoe', 'price' => 500000]);

        $response = $this->get('/shop?sort=price_asc');
        $content = $response->getContent();

        $this->assertTrue(strpos($content, 'Cheap Shoe') < strpos($content, 'Pricey Shoe'));
    }

    public function test_shop_index_sorts_by_price_descending(): void
    {
        $this->makeProductWithStock(['name' => 'Cheap Shoe', 'price' => 100000]);
        $this->makeProductWithStock(['name' => 'Pricey Shoe', 'price' => 500000]);

        $response = $this->get('/shop?sort=price_desc');
        $content = $response->getContent();

        $this->assertTrue(strpos($content, 'Pricey Shoe') < strpos($content, 'Cheap Shoe'));
    }

    public function test_shop_index_ignores_invalid_sort_value(): void
    {
        $this->makeProductWithStock(['name' => 'Any Shoe']);

        $this->get('/shop?sort=hack')->assertOk();
    }

    public function test_product_detail_page_renders_product_info(): void
    {
        $product = $this->makeProductWithStock([
            'name' => 'Retro Court Low',
            'brand' => 'PumaForce',
            'price' => 220000,
        ]);
        \App\Models\ProductDescription::create([
            'product_id' => $product->id,
            'description' => 'A timeless classic.',
        ]);

        $response = $this->get("/shop/{$product->id}");

        $response->assertOk()
            ->assertSee('Retro Court Low')
            ->assertSee('PumaForce')
            ->assertSee('220,000')
            ->assertSee('A timeless classic.')
            ->assertSee('Add to Cart')
            ->assertSee('40');
    }

    public function test_product_detail_page_sets_recently_viewed_cookie(): void
    {
        $product = $this->makeProductWithStock();

        $this->get("/shop/{$product->id}")
            ->assertOk()
            ->assertCookie('recently_viewed', (string) $product->id);
    }

    public function test_product_detail_page_shows_out_of_stock_when_no_stock(): void
    {
        $product = $this->makeProductWithStock([], 0);

        $this->get("/shop/{$product->id}")
            ->assertOk()
            ->assertSee('Out of stock');
    }

    public function test_recently_viewed_page_returns_empty_without_cookie(): void
    {
        $this->get('/recently-viewed')->assertOk();
    }

    public function test_recently_viewed_page_shows_cookie_products(): void
    {
        $product = $this->makeProductWithStock(['name' => 'Recently Seen']);

        $response = $this->withCookie('recently_viewed', (string) $product->id)
            ->get('/recently-viewed');

        $response->assertOk()
            ->assertSee('Recently Seen');
    }
}
