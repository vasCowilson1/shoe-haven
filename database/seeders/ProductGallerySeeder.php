<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductGallerySeeder extends Seeder
{
    public function run(): void
    {
        Product::chunkById(50, function ($products) {
            foreach ($products as $product) {
                $existing = $product->images()->count();
                if ($existing >= 30) {
                    continue;
                }

                $rows = [];
                for ($i = 1; $i <= 30; $i++) {
                    $rows[] = [
                        'product_id' => $product->id,
                        'image_url' => "https://picsum.photos/seed/{$product->id}-{$i}/600/600",
                        'sort_order' => $i,
                        'created_at' => now(),
                    ];
                }
                ProductImage::insert($rows);
            }
        });

        $this->command->info('Inserted 30 gallery images per product.');
    }
}
