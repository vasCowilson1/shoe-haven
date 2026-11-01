<?php

namespace Database\Seeders;

use App\Models\HeroSection;
use Illuminate\Database\Seeder;

class SixHeroSectionSeeder extends Seeder
{
    public function run(): void
    {
        HeroSection::where('id', '>', 0)->update(['is_active' => false]);

        $heroes = [
            [
                'headline' => 'Step Into <span>Comfort</span> &amp; Style',
                'subtitle' => 'Premium shoes for every stride — from everyday sneakers to statement heels. Quality craftsmanship, unbeatable prices, delivered to your door.',
                'button_text' => 'Shop Now',
                'button_url' => '/shop',
                'secondary_button_text' => 'Explore Collection',
                'secondary_button_url' => '/shop',
                'image' => 'images/hero-banner.jpg',
                'stats' => [
                    ['value' => '15K+', 'label' => 'Happy Customers'],
                    ['value' => '500+', 'label' => 'Shoe Styles'],
                    ['value' => '4.9', 'label' => 'Average Rating', 'icon' => 'star'],
                ],
            ],
            [
                'headline' => 'Summer Sale — Up to <span>40% Off</span>',
                'subtitle' => 'Beat the heat with our freshest drops. Lightweight sneakers, breathable runners, and sandals — all at unbeatable summer prices.',
                'button_text' => 'Shop Sale',
                'button_url' => '/shop?sort=price_asc',
                'secondary_button_text' => 'New Arrivals',
                'secondary_button_url' => '/shop?sort=latest',
                'image' => 'images/hero-banner-2.jpg',
                'stats' => [
                    ['value' => '40%', 'label' => 'Max Discount'],
                    ['value' => '200+', 'label' => 'Styles on Sale'],
                    ['value' => 'LIMITED', 'label' => 'Stock Available', 'icon' => 'star'],
                ],
            ],
            [
                'headline' => 'Premium <span>Leather</span> Collection',
                'subtitle' => 'Handcrafted genuine leather footwear for the modern professional. Dress shoes, loafers, and boots that command respect.',
                'button_text' => 'View Collection',
                'button_url' => '/shop?brand=Timberland',
                'secondary_button_text' => 'Size Guide',
                'secondary_button_url' => '/shop',
                'image' => 'images/hero-banner-3.jpg',
                'stats' => [
                    ['value' => '100%', 'label' => 'Genuine Leather'],
                    ['value' => '24 mo.', 'label' => 'Warranty'],
                    ['value' => '4.8', 'label' => 'Rating', 'icon' => 'star'],
                ],
            ],
            [
                'headline' => 'Run <span>Faster</span>. Go Further.',
                'subtitle' => 'Engineered for performance — our running shoes feature responsive cushioning, carbon-plated soles, and breathable mesh uppers.',
                'button_text' => 'Shop Runners',
                'button_url' => '/shop?category=running',
                'secondary_button_text' => 'View Tech',
                'secondary_button_url' => '/shop',
                'image' => 'images/hero-banner.jpg',
                'stats' => [
                    ['value' => '10K+', 'label' => 'Runners Sold'],
                    ['value' => '5★', 'label' => 'Cushion Rating'],
                    ['value' => '2-day', 'label' => 'Delivery', 'icon' => 'star'],
                ],
            ],
            [
                'headline' => 'Street <span>Culture</span> Since 2024',
                'subtitle' => 'Sneakers that make a statement. Collaborate with top designers and bring the latest streetwear trends to your doorstep.',
                'button_text' => 'Explore Streetwear',
                'button_url' => '/shop?category=sneakers',
                'secondary_button_text' => 'Lookbook',
                'secondary_button_url' => '/shop',
                'image' => 'images/hero-banner-2.jpg',
                'stats' => [
                    ['value' => '50+', 'label' => 'Designer Collabs'],
                    ['value' => '300+', 'label' => 'Street Styles'],
                    ['value' => '4.9', 'label' => 'Community Rating', 'icon' => 'star'],
                ],
            ],
            [
                'headline' => 'Holiday <span>Gift Guide</span>',
                'subtitle' => 'Find the perfect pair for everyone on your list. From cozy boots to elegant heels, gift boxes available at checkout.',
                'button_text' => 'Gift Shop',
                'button_url' => '/shop?sort=price_desc',
                'secondary_button_text' => 'Gift Cards',
                'secondary_button_url' => '/shop',
                'image' => 'images/hero-banner-3.jpg',
                'stats' => [
                    ['value' => '100+', 'label' => 'Gift Ideas'],
                    ['value' => 'Free Box', 'label' => 'Gift Wrapping'],
                    ['value' => '4.9', 'label' => 'Satisfaction', 'icon' => 'star'],
                ],
            ],
        ];

        foreach ($heroes as $data) {
            HeroSection::create($data + ['is_active' => true]);
        }

        $this->command->info('Seeded 6 hero sections.');
    }
}
