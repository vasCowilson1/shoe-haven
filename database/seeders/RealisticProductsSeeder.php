<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDescription;
use App\Models\ProductImage;
use App\Models\ProductSizeStock;
use App\Models\ProductStock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RealisticProductsSeeder extends Seeder
{
    public function run(): void
    {
        // Disable FKs and re-seed
        Schema::disableForeignKeyConstraints();
        ProductImage::truncate();
        ProductDescription::truncate();
        ProductSizeStock::truncate();
        ProductStock::truncate();
        Product::query()->delete();
        Schema::enableForeignKeyConstraints();

        $categorySlugs = ['sneakers', 'men', 'women', 'kids', 'sports'];
        $categoryNames = ['Sneakers', 'Men', 'Women', 'Kids', 'Sports'];

        $categories = [];
        foreach ($categorySlugs as $i => $slug) {
            $categories[$slug] = Category::firstOrCreate(
                ['slug' => $slug],
                ['name' => $categoryNames[$i]]
            );
        }

        $cats = Category::all()->keyBy('id');

        $catIdMap = [
            1 => $categories['sneakers']->id,
            3 => $categories['men']->id,
            4 => $categories['women']->id,
            5 => $categories['kids']->id,
            6 => $categories['sports']->id,
        ];

        $sizeRanges = [
            1 => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
            3 => ['38', '39', '40', '41', '42', '43', '44', '45'],
            4 => ['34', '35', '36', '37', '38', '39', '40', '41', '42'],
            5 => ['24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34'],
            6 => ['38', '39', '40', '41', '42', '43', '44', '45'],
        ];

        $brands = ['NikeStyle', 'AdidasPro', 'PumaForce', 'VansComfy', 'FormalPlus', 'SoftStep', 'TimberWalk', 'ClassySole', 'TinyFeet'];

        $products = [
            // ===== Sneakers (cat 1) — 45 products =====
            ['Air Max Motion', 285000, 320000, 11, true, 1, 'NikeStyle'],
            ['Retro Court Low', 220000, null, null, false, 1, 'PumaForce'],
            ['Cloud Walker', 310000, 360000, 14, true, 1, 'AdidasPro'],
            ['Urban Runner', 265000, null, null, false, 1, 'NikeStyle'],
            ['Fresh Knit', 195000, 230000, 15, false, 1, 'AdidasPro'],
            ['Swift Breathe', 175000, null, null, true, 1, 'PumaForce'],
            ['Classic Canvas', 145000, null, null, false, 1, 'VansComfy'],
            ['Flame Runner', 340000, 390000, 13, true, 1, 'NikeStyle'],
            ['Street Glide', 205000, null, null, false, 1, 'PumaForce'],
            ['Suede Classic Low', 165000, null, null, false, 1, 'VansComfy'],
            ['Air Max Pulse', 295000, 340000, 13, true, 1, 'NikeStyle'],
            ['Ultra Boost Run', 330000, 380000, 13, true, 1, 'AdidasPro'],
            ['Flex Trainer', 185000, null, null, false, 1, 'PumaForce'],
            ['Old Skool Zip', 155000, null, null, false, 1, 'VansComfy'],
            ['Vapor Fly', 370000, 420000, 12, true, 1, 'NikeStyle'],
            ['Energy Cloud', 210000, 250000, 16, false, 1, 'AdidasPro'],
            ['Racer TR', 195000, null, null, true, 1, 'PumaForce'],
            ['Slip-On Mesh', 125000, null, null, false, 1, 'VansComfy'],
            ['Air Force Stride', 310000, null, null, false, 1, 'NikeStyle'],
            ['Grand Court', 230000, 270000, 15, false, 1, 'AdidasPro'],
            ['Cell Regulate', 200000, null, null, true, 1, 'PumaForce'],
            ['Authentic Lace', 135000, null, null, false, 1, 'VansComfy'],
            ['Zoom Rival', 355000, 400000, 11, true, 1, 'NikeStyle'],
            ['Duramo Speed', 170000, null, null, false, 1, 'AdidasPro'],
            ['Smash Court', 160000, null, null, false, 1, 'PumaForce'],
            ['Era Classic', 140000, null, null, false, 1, 'VansComfy'],
            ['Pegasus Trail', 290000, 330000, 12, false, 1, 'NikeStyle'],
            ['Lite Racer', 150000, null, null, true, 1, 'AdidasPro'],
            ['Rebound Layup', 240000, null, null, false, 1, 'PumaForce'],
            ['Chukka Low', 175000, null, null, false, 1, 'VansComfy'],
            ['Revolution Run', 260000, 300000, 13, false, 1, 'NikeStyle'],
            ['Pure Boost', 280000, null, null, true, 1, 'AdidasPro'],
            ['Voyage Clog', 190000, null, null, false, 1, 'PumaForce'],
            ['Half Cab Reissue', 225000, 260000, 13, false, 1, 'VansComfy'],
            ['Wildhorse GTX', 380000, 430000, 12, true, 1, 'NikeStyle'],
            ['Solar Glide', 250000, null, null, false, 1, 'AdidasPro'],
            ['Trail Fox', 215000, null, null, true, 1, 'PumaForce'],
            ['Sk8-Hi Moc', 185000, null, null, false, 1, 'VansComfy'],
            ['Free Run Easy', 235000, 280000, 16, false, 1, 'NikeStyle'],
            ['Response Super', 205000, null, null, false, 1, 'AdidasPro'],
            ['Carson Runner', 145000, null, null, true, 1, 'PumaForce'],
            ['Style 36 Reissue', 160000, null, null, false, 1, 'VansComfy'],
            ['Phantom GT', 320000, 370000, 14, true, 1, 'NikeStyle'],
            ['EQT Support', 270000, null, null, false, 1, 'AdidasPro'],
            ['Mobium Elite', 230000, null, null, false, 1, 'PumaForce'],

            // ===== Men (cat 3) — 45 products =====
            ['Leather Derby', 450000, null, null, true, 3, 'FormalPlus'],
            ['Casual Loafer', 210000, null, null, false, 3, 'SoftStep'],
            ['Trail Hiker', 380000, 440000, 14, false, 3, 'TimberWalk'],
            ['Oxford Lace', 490000, null, null, false, 3, 'FormalPlus'],
            ['Desert Boot', 320000, 370000, 14, false, 3, 'TimberWalk'],
            ['Moccasin Slip', 185000, null, null, true, 3, 'SoftStep'],
            ['Wingtip Brogue', 520000, null, null, true, 3, 'FormalPlus'],
            ['Espadrille Coastal', 130000, null, null, false, 3, 'VansComfy'],
            ['Chukka Casual', 290000, null, null, false, 3, 'TimberWalk'],
            ['Monk Strap', 470000, 530000, 11, false, 3, 'FormalPlus'],
            ['Canvas Boat Shoe', 165000, null, null, false, 3, 'SoftStep'],
            ['Chelsea Boot', 410000, 470000, 13, true, 3, 'TimberWalk'],
            ['Wholecut Oxford', 560000, null, null, true, 3, 'FormalPlus'],
            ['Driving Loafer', 195000, null, null, false, 3, 'SoftStep'],
            ['Work Boot Solid', 360000, null, null, false, 3, 'TimberWalk'],
            ['Slip-On Venetian', 240000, 280000, 14, false, 3, 'SoftStep'],
            ['Double Monk Strap', 510000, null, null, false, 3, 'FormalPlus'],
            ['Hiking Mid GTX', 430000, null, null, true, 3, 'TimberWalk'],
            ['Plain Toe Blucher', 440000, 500000, 12, false, 3, 'FormalPlus'],
            ['Espresso Loafer', 200000, null, null, false, 3, 'SoftStep'],
            ['Logger Boot', 480000, null, null, false, 3, 'TimberWalk'],
            ['Saddle Oxford', 460000, null, null, true, 3, 'FormalPlus'],
            ['Camp Moc', 175000, null, null, false, 3, 'SoftStep'],
            ['Winter Chukka', 330000, 380000, 13, false, 3, 'TimberWalk'],
            ['Spectator Oxford', 540000, null, null, false, 3, 'FormalPlus'],
            ['Deck Slip-On', 155000, null, null, true, 3, 'SoftStep'],
            ['Field Boot', 390000, null, null, false, 3, 'TimberWalk'],
            ['Cap Toe Oxford', 500000, 570000, 12, true, 3, 'FormalPlus'],
            ['Woven Loafer', 220000, null, null, false, 3, 'SoftStep'],
            ['Urban Hiker Low', 310000, null, null, false, 3, 'TimberWalk'],
            ['Longwing Blucher', 480000, null, null, false, 3, 'FormalPlus'],
            ['Tassel Loafer', 260000, 300000, 13, false, 3, 'SoftStep'],
            ['Mountain Trekker', 420000, 480000, 13, true, 3, 'TimberWalk'],
            ['Suede Chukka', 270000, null, null, false, 3, 'SoftStep'],
            ['Semi-Brogue Oxford', 530000, null, null, false, 3, 'FormalPlus'],
            ['Traveler Slip-On', 190000, null, null, true, 3, 'VansComfy'],
            ['Expedition Boot', 440000, null, null, false, 3, 'TimberWalk'],
            ['Jodhpur Boot', 490000, null, null, false, 3, 'FormalPlus'],
            ['Bit Loafer', 340000, 390000, 13, false, 3, 'SoftStep'],
            ['Rain Boot Tall', 250000, null, null, false, 3, 'TimberWalk'],
            ['Padded Collar Loafer', 205000, null, null, true, 3, 'SoftStep'],
            ['Formal Oxford Patent', 580000, null, null, true, 3, 'FormalPlus'],
            ['Casual Wedge Boot', 300000, null, null, false, 3, 'TimberWalk'],
            ['Belgian Loafer', 235000, 275000, 15, false, 3, 'SoftStep'],
            ['Nubuck Desert Boot', 350000, null, null, false, 3, 'TimberWalk'],

            // ===== Women (cat 4) — 45 products =====
            ['Stiletto Point', 350000, null, null, true, 4, 'ClassySole'],
            ['Block Heel Sandal', 230000, 270000, 15, false, 4, 'SoftStep'],
            ['Pump Classic', 390000, null, null, false, 4, 'ClassySole'],
            ['Flat Ballet', 150000, null, null, true, 4, 'SoftStep'],
            ['Wedge Heel', 280000, 330000, 15, false, 4, 'ClassySole'],
            ['Ankle Strap Heel', 410000, null, null, false, 4, 'ClassySole'],
            ['Slide Sandal', 120000, null, null, true, 4, 'SoftStep'],
            ['Kitten Heel', 260000, 300000, 13, false, 4, 'ClassySole'],
            ['Platform Wedge', 320000, null, null, false, 4, 'SoftStep'],
            ['Lace-Up Flat', 140000, null, null, false, 4, 'VansComfy'],
            ['D\'Orsay Flat', 180000, null, null, true, 4, 'ClassySole'],
            ['Espadrille Wedge', 200000, 240000, 17, false, 4, 'SoftStep'],
            ['Peep Toe Pump', 370000, 420000, 12, true, 4, 'ClassySole'],
            ['Gladiator Sandal', 160000, null, null, false, 4, 'SoftStep'],
            ['Slingback Pump', 340000, null, null, false, 4, 'ClassySole'],
            ['Mule Slide', 130000, null, null, true, 4, 'SoftStep'],
            ['T-Strap Heel', 380000, null, null, false, 4, 'ClassySole'],
            ['Woven Flat', 145000, 170000, 15, false, 4, 'SoftStep'],
            ['Cone Heel Pump', 420000, 480000, 13, false, 4, 'ClassySole'],
            ['Jelly Sandal', 90000, null, null, false, 4, 'SoftStep'],
            ['Evening Pump', 460000, null, null, true, 4, 'ClassySole'],
            ['Comfort Loafer', 190000, null, null, false, 4, 'SoftStep'],
            ['Mary Jane Flat', 155000, null, null, false, 4, 'ClassySole'],
            ['Strappy Heel', 400000, 450000, 11, false, 4, 'ClassySole'],
            ['Fisherman Sandal', 135000, null, null, true, 4, 'SoftStep'],
            ['Pointed Flat', 170000, null, null, false, 4, 'ClassySole'],
            ['Cork Wedge', 250000, null, null, false, 4, 'SoftStep'],
            ['Crystal Heel', 500000, null, null, true, 4, 'ClassySole'],
            ['Ankle Bootie Heel', 360000, 410000, 12, false, 4, 'SoftStep'],
            ['Bow Ballet Flat', 165000, null, null, false, 4, 'ClassySole'],
            ['Chain Strap Heel', 430000, null, null, false, 4, 'ClassySole'],
            ['Plimsoll Flat', 110000, null, null, true, 4, 'VansComfy'],
            ['Lace Espadrille', 175000, null, null, false, 4, 'SoftStep'],
            ['Cutout Bootie', 310000, 360000, 14, false, 4, 'ClassySole'],
            ['Square Toe Heel', 330000, null, null, false, 4, 'ClassySole'],
            ['Monk Strap Heel', 270000, null, null, true, 4, 'SoftStep'],
            ['Knee High Boot', 500000, 570000, 12, false, 4, 'TimberWalk'],
            ['Raffia Wedge', 215000, null, null, false, 4, 'SoftStep'],
            ['Chunky Sneaker', 220000, 260000, 15, true, 4, 'NikeStyle'],
            ['Over The Knee', 550000, null, null, false, 4, 'ClassySole'],
            ['Sock Sneaker Low', 185000, null, null, true, 4, 'AdidasPro'],
            ['Western Bootie', 350000, null, null, false, 4, 'TimberWalk'],
            ['Platform Sneaker', 210000, null, null, false, 4, 'PumaForce'],
            ['Lace-Up Combat', 290000, 340000, 15, false, 4, 'TimberWalk'],
            ['Embellished Flat', 195000, null, null, false, 4, 'ClassySole'],

            // ===== Kids (cat 5) — 45 products =====
            ['Mini Runner', 95000, 110000, 14, true, 5, 'TinyFeet'],
            ['Play Sneaker', 115000, null, null, false, 5, 'TinyFeet'],
            ['Rain Boot', 100000, null, null, false, 5, 'TinyFeet'],
            ['School Shoe', 105000, null, null, false, 5, 'TinyFeet'],
            ['Toddler Sandal', 75000, 90000, 17, true, 5, 'TinyFeet'],
            ['Cute Canvas', 85000, null, null, false, 5, 'TinyFeet'],
            ['Baby Moccasin', 65000, null, null, true, 5, 'TinyFeet'],
            ['Sporty Kid', 125000, 145000, 14, false, 5, 'TinyFeet'],
            ['Light Walk', 80000, null, null, false, 5, 'TinyFeet'],
            ['Jump Star', 110000, null, null, true, 5, 'TinyFeet'],
            ['Toddler Boot', 95000, null, null, false, 5, 'TinyFeet'],
            ['Lace-Up School', 120000, 140000, 14, false, 5, 'TinyFeet'],
            ['Baby Sneaker', 70000, null, null, true, 5, 'TinyFeet'],
            ['Summer Sandal', 65000, null, null, false, 5, 'TinyFeet'],
            ['First Walker', 85000, null, null, true, 5, 'TinyFeet'],
            ['Junior Runner', 130000, null, null, false, 5, 'TinyFeet'],
            ['Water Shoe', 55000, 70000, 21, false, 5, 'TinyFeet'],
            ['Pre-Walker Soft', 60000, null, null, true, 5, 'TinyFeet'],
            ['Canvas Hi-Top', 100000, null, null, false, 5, 'TinyFeet'],
            ['Girls Ballet Flat', 80000, 95000, 16, false, 5, 'TinyFeet'],
            ['Boys Casual', 90000, null, null, false, 5, 'TinyFeet'],
            ['Winter Boot Kid', 140000, 165000, 15, true, 5, 'TinyFeet'],
            ['Slip-On School', 95000, null, null, false, 5, 'TinyFeet'],
            ['Toddler Mule', 70000, null, null, false, 5, 'TinyFeet'],
            ['Kids Trail', 110000, null, null, true, 5, 'TinyFeet'],
            ['Baby Oxford', 75000, null, null, false, 5, 'TinyFeet'],
            ['Girls Sandal Bow', 85000, 100000, 15, false, 5, 'TinyFeet'],
            ['Active Kid TR', 135000, null, null, false, 5, 'TinyFeet'],
            ['Toddler Sneaker', 80000, null, null, true, 5, 'TinyFeet'],
            ['School Boot', 115000, null, null, false, 5, 'TinyFeet'],
            ['Baby Sandal', 55000, null, null, false, 5, 'TinyFeet'],
            ['Kids Rain Boot', 95000, 110000, 14, false, 5, 'TinyFeet'],
            ['Grip Sock Shoe', 65000, null, null, true, 5, 'TinyFeet'],
            ['Little Star', 90000, null, null, false, 5, 'TinyFeet'],
            ['Kids Hiker Low', 125000, null, null, false, 5, 'TinyFeet'],
            ['First Sneaker', 70000, 85000, 18, true, 5, 'TinyFeet'],
            ['Toddler Slip-On', 75000, null, null, false, 5, 'TinyFeet'],
            ['Girls Bootie', 120000, null, null, false, 5, 'TinyFeet'],
            ['Kids Sport Flex', 130000, 150000, 13, false, 5, 'TinyFeet'],
            ['Summer Mesh', 85000, null, null, true, 5, 'TinyFeet'],
            ['Boys Derby', 100000, null, null, false, 5, 'TinyFeet'],
            ['Toddler Rainboot', 80000, null, null, false, 5, 'TinyFeet'],
            ['Kids Canvas Low', 75000, null, null, false, 5, 'TinyFeet'],
            ['Girls Sneaker', 95000, 110000, 14, true, 5, 'TinyFeet'],
            ['Kids Slip Resistant', 105000, null, null, false, 5, 'TinyFeet'],

            // ===== Sports (cat 6) — 45 products =====
            ['Endurance Run', 350000, null, null, true, 6, 'NikeStyle'],
            ['Sprint Elite', 300000, 350000, 14, false, 6, 'AdidasPro'],
            ['Cross Trainer X', 260000, null, null, false, 6, 'PumaForce'],
            ['Basketball Pro', 380000, 440000, 14, false, 6, 'NikeStyle'],
            ['Yoga Flex', 195000, null, null, true, 6, 'AdidasPro'],
            ['Trail Blaze', 340000, null, null, false, 6, 'TimberWalk'],
            ['Court Ace', 250000, 290000, 14, false, 6, 'PumaForce'],
            ['Marathon Pro', 420000, null, null, true, 6, 'NikeStyle'],
            ['Gym Trainer', 230000, null, null, false, 6, 'AdidasPro'],
            ['Speed Burst', 280000, 320000, 13, false, 6, 'PumaForce'],
            ['Iron Lift', 270000, null, null, false, 6, 'NikeStyle'],
            ['Tempo Run', 310000, 360000, 14, true, 6, 'AdidasPro'],
            ['Turf Striker', 220000, null, null, false, 6, 'PumaForce'],
            ['Rowing Flex', 240000, null, null, true, 6, 'NikeStyle'],
            ['HIIT Trainer', 290000, 340000, 15, false, 6, 'AdidasPro'],
            ['Volleyball Ace', 235000, null, null, false, 6, 'PumaForce'],
            ['Long Run Pro', 400000, null, null, false, 6, 'NikeStyle'],
            ['Studio Cycle', 210000, 250000, 16, false, 6, 'AdidasPro'],
            ['Agility XT', 255000, null, null, true, 6, 'PumaForce'],
            ['Trail Ultra', 380000, 440000, 14, false, 6, 'NikeStyle'],
            ['Cardio Glide', 225000, null, null, false, 6, 'AdidasPro'],
            ['Power Jump', 265000, null, null, false, 6, 'PumaForce'],
            ['Recovery Slide', 120000, null, null, true, 6, 'SoftStep'],
            ['Plyo Force', 320000, 370000, 14, false, 6, 'NikeStyle'],
            ['Speed Lace', 185000, null, null, false, 6, 'AdidasPro'],
            ['Flex Weave', 275000, null, null, true, 6, 'PumaForce'],
            ['Distance Elite', 440000, 500000, 12, true, 6, 'NikeStyle'],
            ['Spinning Pro', 200000, null, null, false, 6, 'AdidasPro'],
            ['Gridiron', 350000, null, null, false, 6, 'PumaForce'],
            ['Traverse GTX', 410000, 470000, 13, false, 6, 'TimberWalk'],
            ['Climbing Approach', 360000, null, null, false, 6, 'NikeStyle'],
            ['Stride Racer', 290000, 330000, 12, true, 6, 'AdidasPro'],
            ['Padel Court', 240000, null, null, false, 6, 'PumaForce'],
            ['Trail Speed', 330000, null, null, false, 6, 'NikeStyle'],
            ['Indoor Court', 215000, 250000, 14, false, 6, 'AdidasPro'],
            ['Rugged TR', 300000, null, null, true, 6, 'PumaForce'],
            ['Fitness Walker', 170000, null, null, false, 6, 'SoftStep'],
            ['Sprint Spike', 260000, null, null, false, 6, 'NikeStyle'],
            ['Elliptical Trainer', 190000, null, null, true, 6, 'AdidasPro'],
            ['Versa Trainer', 285000, 330000, 14, false, 6, 'PumaForce'],
            ['Vertical Jump', 370000, null, null, false, 6, 'NikeStyle'],
            ['Rapid Fire', 250000, null, null, false, 6, 'AdidasPro'],
            ['Lateral Quick', 230000, null, null, true, 6, 'PumaForce'],
            ['Peak Performance', 450000, 520000, 13, true, 6, 'NikeStyle'],
            ['Fast Twitch', 310000, null, null, false, 6, 'AdidasPro'],
        ];

        $imgCounter = 1;

        $galleryPool = [];
        for ($i = 1; $i <= 344; $i++) {
            $galleryPool[] = "images/products/shoe-{$i}.jpg";
        }

        $descriptions = [
            'Everyday comfort meets timeless style. Built with durable materials and a cushioned insole for all-day wear.',
            'Premium craftsmanship and modern design come together in this versatile pair, perfect for any occasion.',
            'Engineered for performance without compromising on looks. Lightweight, breathable, and built to last.',
            'A wardrobe staple crafted from quality materials. Pair it with anything for an effortlessly polished look.',
            'Designed with your comfort in mind — flexible sole, padded collar, and breathable lining for long-lasting wear.',
        ];

        foreach ($products as $data) {
            $imgNum = $imgCounter++;

            $catId = $data[5];
            $realCatId = $catIdMap[$catId] ?? $catId;
            $cat = $cats->get($realCatId);
            if (! $cat) {
                continue;
            }

            $product = Product::create([
                'name' => $data[0],
                'brand' => $data[6] ?? $brands[array_rand($brands)],
                'price' => $data[1],
                'original_price' => $data[2],
                'image' => "images/products/shoe-{$imgNum}.jpg",
                'category_id' => $realCatId,
                'is_new' => $data[4],
                'discount' => $data[3],
            ]);

            ProductStock::create([
                'product_id' => $product->id,
                'quantity' => rand(10, 80),
            ]);

            $sizes = $sizeRanges[$catId] ?? ['39', '40', '41', '42', '43'];
            foreach ($sizes as $size) {
                ProductSizeStock::create([
                    'product_id' => $product->id,
                    'size' => $size,
                    'quantity' => rand(2, 15),
                ]);
            }

            $offset = (($product->id - 1) * 5) % count($galleryPool);
            $gallery = array_slice($galleryPool, $offset, 5);
            foreach ($gallery as $i => $imageUrl) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $imageUrl,
                    'sort_order' => $i + 1,
                ]);
            }

            ProductDescription::create([
                'product_id' => $product->id,
                'description' => $descriptions[$product->id % count($descriptions)]
                    .' '.$product->name.' by '.$product->brand
                    .' — a '.$cat->name.' essential at an unbeatable price.',
            ]);
        }

        $this->command->info('Seeded '.count($products).' realistic products with unique images & sizes.');
    }
}
