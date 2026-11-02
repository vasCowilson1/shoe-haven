<?php

namespace Tests\Feature\Concerns;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSizeStock;
use App\Models\ProductStock;

trait CreatesShopData
{
    protected function makeCategory(string $name, string $slug): Category
    {
        return Category::firstOrCreate(['slug' => $slug], ['name' => $name]);
    }

    protected function makeProduct(array $overrides = []): Product
    {
        if (!isset($overrides['category_id'])) {
            $overrides['category_id'] = $this->makeCategory('Sneakers', 'sneakers')->id;
        }

        $product = Product::create(array_merge([
            'name' => 'Air Max Motion',
            'brand' => 'NikeStyle',
            'price' => 285000,
            'original_price' => null,
            'image' => 'images/products/shoe-1.jpg',
            'is_new' => true,
            'discount' => null,
        ], $overrides));

        return $product;
    }

    protected function makeProductWithStock(array $overrides = [], int $quantity = 20): Product
    {
        $product = $this->makeProduct($overrides);

        ProductStock::create(['product_id' => $product->id, 'quantity' => $quantity]);
        ProductSizeStock::create(['product_id' => $product->id, 'size' => '40', 'quantity' => 10]);
        ProductSizeStock::create(['product_id' => $product->id, 'size' => '41', 'quantity' => 5]);

        return $product;
    }
}
