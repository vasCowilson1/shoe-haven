<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Men' => 'men',
            'Women' => 'women',
            'Kids' => 'kids',
            'Sports' => 'sports',
        ];

        $categoryModels = [];
        foreach ($categories as $name => $slug) {
            $categoryModels[$slug] = Category::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }

        $productDefs = [
            ['name' => 'Runner Pro X', 'brand' => 'NikeStyle', 'price' => 320000, 'original_price' => null, 'category' => 'sports', 'is_new' => true, 'discount' => null, 'stock' => 25],
            ['name' => 'Urban Classic', 'brand' => 'AdidasPro', 'price' => 210000, 'original_price' => 265000, 'category' => 'men', 'is_new' => false, 'discount' => 20, 'stock' => 40],
            ['name' => 'Trail Blazer Boot', 'brand' => 'TimberWalk', 'price' => 430000, 'original_price' => null, 'category' => 'men', 'is_new' => false, 'discount' => null, 'stock' => 18],
            ['name' => 'Elegance Heel', 'brand' => 'ClassySole', 'price' => 340000, 'original_price' => null, 'category' => 'women', 'is_new' => true, 'discount' => null, 'stock' => 22],
            ['name' => 'Comfort Flats', 'brand' => 'SoftStep', 'price' => 180000, 'original_price' => null, 'category' => 'women', 'is_new' => false, 'discount' => null, 'stock' => 33],
            ['name' => 'Little Sprinter', 'brand' => 'TinyFeet', 'price' => 120000, 'original_price' => 150000, 'category' => 'kids', 'is_new' => true, 'discount' => 20, 'stock' => 50],
            ['name' => 'Court Champion', 'brand' => 'PumaForce', 'price' => 295000, 'original_price' => null, 'category' => 'sports', 'is_new' => false, 'discount' => null, 'stock' => 15],
            ['name' => 'Everyday Slip-On', 'brand' => 'VansComfy', 'price' => 150000, 'original_price' => null, 'category' => 'men', 'is_new' => false, 'discount' => null, 'stock' => 60],
            ['name' => 'Air Max Fury', 'brand' => 'NikeStyle', 'price' => 380000, 'original_price' => 420000, 'category' => 'sports', 'is_new' => true, 'discount' => 10, 'stock' => 30],
            ['name' => 'Leather Oxford', 'brand' => 'FormalPlus', 'price' => 450000, 'original_price' => null, 'category' => 'men', 'is_new' => true, 'discount' => null, 'stock' => 20],
            ['name' => 'Stiletto Charm', 'brand' => 'ClassySole', 'price' => 390000, 'original_price' => 460000, 'category' => 'women', 'is_new' => false, 'discount' => 15, 'stock' => 16],
            ['name' => 'Toddler Walker', 'brand' => 'TinyFeet', 'price' => 85000, 'original_price' => 100000, 'category' => 'kids', 'is_new' => true, 'discount' => 15, 'stock' => 70],
            ['name' => 'Cloud Racer', 'brand' => 'PumaForce', 'price' => 275000, 'original_price' => null, 'category' => 'sports', 'is_new' => false, 'discount' => null, 'stock' => 28],
            ['name' => 'Loafers Elite', 'brand' => 'VansComfy', 'price' => 195000, 'original_price' => 230000, 'category' => 'men', 'is_new' => false, 'discount' => 15, 'stock' => 45],
            ['name' => 'Ballet Wrap', 'brand' => 'SoftStep', 'price' => 160000, 'original_price' => null, 'category' => 'women', 'is_new' => true, 'discount' => null, 'stock' => 35],
            ['name' => 'School Classic', 'brand' => 'TinyFeet', 'price' => 110000, 'original_price' => 130000, 'category' => 'kids', 'is_new' => false, 'discount' => 15, 'stock' => 80],
            ['name' => 'Ultra Boost', 'brand' => 'AdidasPro', 'price' => 360000, 'original_price' => null, 'category' => 'sports', 'is_new' => true, 'discount' => null, 'stock' => 22],
            ['name' => 'Ankle Boot', 'brand' => 'TimberWalk', 'price' => 310000, 'original_price' => 370000, 'category' => 'women', 'is_new' => false, 'discount' => 16, 'stock' => 14],
            ['name' => 'Canvas Hi-Top', 'brand' => 'VansComfy', 'price' => 140000, 'original_price' => null, 'category' => 'men', 'is_new' => false, 'discount' => null, 'stock' => 55],
        ];

        $products = [];
        foreach ($productDefs as $i => $data) {
            $imgIndex = ($i % 12) + 1;
            $data['image'] = 'images/products/shoe-'.$imgIndex.'.jpg';
            $products[] = $data;
        }

        foreach ($products as $data) {
            $product = Product::updateOrCreate(
                ['name' => $data['name']],
                [
                    'brand' => $data['brand'],
                    'price' => $data['price'],
                    'original_price' => $data['original_price'],
                    'image' => $data['image'],
                    'category_id' => $categoryModels[$data['category']]->id,
                    'is_new' => $data['is_new'],
                    'discount' => $data['discount'],
                ]
            );

            ProductStock::updateOrCreate(
                ['product_id' => $product->id],
                ['quantity' => $data['stock']]
            );
        }
    }
}
