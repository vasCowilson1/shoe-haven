<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDescription;
use App\Models\ProductImage;
use App\Models\ProductSizeStock;
use App\Models\ProductStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(): Product
    {
        $category = Category::create(['name' => 'Sneakers', 'slug' => 'sneakers']);

        return Product::create([
            'name' => 'Air Max Motion',
            'brand' => 'NikeStyle',
            'price' => 285000,
            'original_price' => 320000,
            'image' => 'images/products/shoe-1.jpg',
            'category_id' => $category->id,
            'is_new' => true,
            'discount' => 11,
        ]);
    }

    public function test_product_belongs_to_category(): void
    {
        $product = $this->makeProduct();

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertSame('Sneakers', $product->category->name);
    }

    public function test_product_has_one_stock(): void
    {
        $product = $this->makeProduct();
        ProductStock::create(['product_id' => $product->id, 'quantity' => 42]);

        $this->assertInstanceOf(ProductStock::class, $product->stock);
        $this->assertSame(42, $product->stock->quantity);
    }

    public function test_product_has_many_size_stock_rows(): void
    {
        $product = $this->makeProduct();
        ProductSizeStock::create(['product_id' => $product->id, 'size' => '40', 'quantity' => 5]);
        ProductSizeStock::create(['product_id' => $product->id, 'size' => '41', 'quantity' => 7]);

        $this->assertCount(2, $product->sizeStock);
        $this->assertSame(7, $product->sizeStock->where('size', '41')->first()->quantity);
    }

    public function test_product_images_are_ordered_by_sort_order(): void
    {
        $product = $this->makeProduct();
        ProductImage::create(['product_id' => $product->id, 'image_url' => 'images/products/shoe-2.jpg', 'sort_order' => 2]);
        ProductImage::create(['product_id' => $product->id, 'image_url' => 'images/products/shoe-1.jpg', 'sort_order' => 1]);

        $this->assertSame('images/products/shoe-1.jpg', $product->images->first()->image_url);
        $this->assertSame('images/products/shoe-2.jpg', $product->images->last()->image_url);
    }

    public function test_product_has_one_description(): void
    {
        $product = $this->makeProduct();
        ProductDescription::create(['product_id' => $product->id, 'description' => 'Premium quality footwear.']);

        $this->assertInstanceOf(ProductDescription::class, $product->description);
        $this->assertSame('Premium quality footwear.', $product->description->description);
    }

    public function test_product_has_many_comments(): void
    {
        $product = $this->makeProduct();
        $user = \App\Models\User::factory()->create();

        $product->comments()->create(['user_id' => $user->id, 'rating' => 5, 'content' => 'Great shoes!']);
        $product->comments()->create(['user_id' => $user->id, 'rating' => 4, 'content' => 'Nice!']);

        $this->assertCount(2, $product->comments);
    }

    public function test_product_discount_and_is_new_are_stored(): void
    {
        $product = $this->makeProduct();

        $this->assertSame(11, $product->discount);
        $this->assertTrue($product->is_new);
        $this->assertSame(320000, $product->original_price);
    }
}
