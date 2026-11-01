<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class UpdateProductImagesSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $imgCount = 37;
        $indices = range(1, $imgCount);
        shuffle($indices);

        foreach ($products as $i => $product) {
            $num = $indices[$i % $imgCount];
            $product->update(['image' => "images/products/shoe-{$num}.jpg"]);
        }

        $this->command->info('Updated '.count($products).' products with varied images.');
    }
}
