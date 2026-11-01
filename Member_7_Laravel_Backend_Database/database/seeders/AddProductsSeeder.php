<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Database\Seeder;

class AddProductsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all()->keyBy('id');

        $target = 10;

        $newProducts = [
            1 => [ // Sneakers
                ['name' => 'Speed Runner', 'brand' => 'NikeStyle', 'price' => 280000, 'original_price' => 320000, 'discount' => 13, 'is_new' => true, 'stock' => 30],
                ['name' => 'Classic Low-Top', 'brand' => 'VansComfy', 'price' => 175000, 'original_price' => null, 'discount' => null, 'is_new' => false, 'stock' => 45],
                ['name' => 'Retro Court', 'brand' => 'PumaForce', 'price' => 260000, 'original_price' => null, 'discount' => null, 'is_new' => false, 'stock' => 22],
                ['name' => 'Air Glide', 'brand' => 'NikeStyle', 'price' => 350000, 'original_price' => 400000, 'discount' => 13, 'is_new' => true, 'stock' => 18],
                ['name' => 'Stripe Runner', 'brand' => 'AdidasPro', 'price' => 220000, 'original_price' => null, 'discount' => null, 'is_new' => false, 'stock' => 35],
                ['name' => 'Mesh Breathe', 'brand' => 'PumaForce', 'price' => 190000, 'original_price' => 230000, 'discount' => 17, 'is_new' => false, 'stock' => 28],
                ['name' => 'Fusion Knit', 'brand' => 'NikeStyle', 'price' => 310000, 'original_price' => null, 'discount' => null, 'is_new' => true, 'stock' => 20],
                ['name' => 'Urban Trek', 'brand' => 'TimberWalk', 'price' => 270000, 'original_price' => null, 'discount' => null, 'is_new' => false, 'stock' => 15],
                ['name' => 'Suede Classic', 'brand' => 'VansComfy', 'price' => 185000, 'original_price' => 215000, 'discount' => 14, 'is_new' => false, 'stock' => 40],
            ],
            3 => [ // Men
                ['name' => 'Formal Lace-Up', 'brand' => 'FormalPlus', 'price' => 480000, 'original_price' => null, 'discount' => null, 'is_new' => false, 'stock' => 25],
                ['name' => 'Casual Moc', 'brand' => 'SoftStep', 'price' => 200000, 'original_price' => null, 'discount' => null, 'is_new' => false, 'stock' => 38],
                ['name' => 'Hiker Pro', 'brand' => 'TimberWalk', 'price' => 420000, 'original_price' => 490000, 'discount' => 14, 'is_new' => false, 'stock' => 12],
                ['name' => 'Slip-On Loafer', 'brand' => 'FormalPlus', 'price' => 230000, 'original_price' => null, 'discount' => null, 'is_new' => true, 'stock' => 30],
            ],
            4 => [ // Women
                ['name' => 'Block Heel', 'brand' => 'ClassySole', 'price' => 320000, 'original_price' => null, 'discount' => null, 'is_new' => true, 'stock' => 20],
                ['name' => 'Wedge Sandal', 'brand' => 'SoftStep', 'price' => 210000, 'original_price' => 250000, 'discount' => 16, 'is_new' => false, 'stock' => 35],
                ['name' => 'Pointed Pump', 'brand' => 'ClassySole', 'price' => 370000, 'original_price' => null, 'discount' => null, 'is_new' => false, 'stock' => 18],
                ['name' => 'Espadrille Flat', 'brand' => 'SoftStep', 'price' => 145000, 'original_price' => null, 'discount' => null, 'is_new' => true, 'stock' => 42],
                ['name' => 'Ankle Strap Heel', 'brand' => 'ClassySole', 'price' => 410000, 'original_price' => 470000, 'discount' => 13, 'is_new' => false, 'stock' => 14],
            ],
            5 => [ // Kids
                ['name' => 'Fun Light', 'brand' => 'TinyFeet', 'price' => 95000, 'original_price' => null, 'discount' => null, 'is_new' => true, 'stock' => 65],
                ['name' => 'Sporty Kid', 'brand' => 'TinyFeet', 'price' => 115000, 'original_price' => 135000, 'discount' => 15, 'is_new' => false, 'stock' => 55],
                ['name' => 'Rain Boot Mini', 'brand' => 'TinyFeet', 'price' => 100000, 'original_price' => null, 'discount' => null, 'is_new' => false, 'stock' => 40],
                ['name' => 'School Lace', 'brand' => 'TinyFeet', 'price' => 105000, 'original_price' => null, 'discount' => null, 'is_new' => false, 'stock' => 70],
                ['name' => 'Toddler Sandal', 'brand' => 'TinyFeet', 'price' => 75000, 'original_price' => 90000, 'discount' => 17, 'is_new' => true, 'stock' => 80],
                ['name' => 'Cute Sneaker', 'brand' => 'TinyFeet', 'price' => 110000, 'original_price' => null, 'discount' => null, 'is_new' => false, 'stock' => 60],
                ['name' => 'Baby Moc', 'brand' => 'TinyFeet', 'price' => 65000, 'original_price' => null, 'discount' => null, 'is_new' => true, 'stock' => 90],
            ],
            6 => [ // Sports
                ['name' => 'Trail Endurance', 'brand' => 'NikeStyle', 'price' => 340000, 'original_price' => null, 'discount' => null, 'is_new' => true, 'stock' => 20],
                ['name' => 'Sprint Elite', 'brand' => 'AdidasPro', 'price' => 300000, 'original_price' => 350000, 'discount' => 14, 'is_new' => false, 'stock' => 25],
                ['name' => 'Cross Trainer', 'brand' => 'PumaForce', 'price' => 260000, 'original_price' => null, 'discount' => null, 'is_new' => false, 'stock' => 32],
                ['name' => 'Basketball High', 'brand' => 'NikeStyle', 'price' => 380000, 'original_price' => 440000, 'discount' => 14, 'is_new' => false, 'stock' => 15],
                ['name' => 'Yoga Flex', 'brand' => 'AdidasPro', 'price' => 195000, 'original_price' => null, 'discount' => null, 'is_new' => true, 'stock' => 28],
            ],
        ];

        $imgCount = 12;
        $imgIndex = 0;

        foreach ($newProducts as $catId => $products) {
            $cat = $categories->get($catId);
            if (! $cat) {
                continue;
            }

            $existingCount = Product::where('category_id', $catId)->count();
            $needed = $target - $existingCount;

            if ($needed <= 0) {
                continue;
            }

            $toAdd = array_slice($products, 0, $needed);

            foreach ($toAdd as $data) {
                $imgNum = ($imgIndex % $imgCount) + 1;
                $imgIndex++;

                $product = Product::updateOrCreate(
                    ['name' => $data['name']],
                    [
                        'brand' => $data['brand'],
                        'price' => $data['price'],
                        'original_price' => $data['original_price'],
                        'image' => 'images/products/shoe-'.$imgNum.'.jpg',
                        'category_id' => $catId,
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

        $this->command->info('Added products to reach 10 per category.');
    }
}
