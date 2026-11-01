<?php

namespace Database\Seeders;

use App\Models\HeroSection;
use Illuminate\Database\Seeder;

class HeroSectionSeeder extends Seeder
{
    public function run(): void
    {
        HeroSection::create([
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
            'is_active' => true,
        ]);
    }
}
