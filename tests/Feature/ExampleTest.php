<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\HeroSection;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_the_home_page_shows_hero_sections_and_products(): void
    {
        Category::create(['name' => 'Sneakers', 'slug' => 'sneakers']);
        $category = Category::first();

        HeroSection::create([
            'headline' => 'Step Into Comfort',
            'subtitle' => 'Premium shoes for every stride.',
            'button_text' => 'Shop Now',
            'button_url' => '/shop',
            'secondary_button_text' => 'Explore',
            'secondary_button_url' => '/shop',
            'image' => 'images/hero-banner.jpg',
            'stats' => [['value' => '15K+', 'label' => 'Happy Customers']],
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Air Max Motion',
            'brand' => 'NikeStyle',
            'price' => 285000,
            'image' => 'images/products/shoe-1.jpg',
            'category_id' => $category->id,
            'is_new' => true,
        ]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Step Into Comfort')
            ->assertSee('Air Max Motion');
    }
}
