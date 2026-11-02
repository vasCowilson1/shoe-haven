<?php

namespace Tests\Unit;

use App\Models\HeroSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeroSectionTest extends TestCase
{
    use RefreshDatabase;

    private function makeHero(array $overrides = []): HeroSection
    {
        return HeroSection::create(array_merge([
            'headline' => 'Step Into <span>Comfort</span> &amp; Style',
            'subtitle' => 'Premium shoes for every stride.',
            'button_text' => 'Shop Now',
            'button_url' => '/shop',
            'secondary_button_text' => 'Explore Collection',
            'secondary_button_url' => '/shop',
            'image' => 'images/hero-banner.jpg',
            'stats' => [
                ['value' => '15K+', 'label' => 'Happy Customers'],
                ['value' => '500+', 'label' => 'Shoe Styles'],
            ],
            'is_active' => true,
        ], $overrides));
    }

    public function test_get_all_active_returns_only_active_slides(): void
    {
        $this->makeHero();
        $this->makeHero();
        $this->makeHero(['is_active' => false]);

        $active = HeroSection::getAllActive();

        $this->assertCount(2, $active);
    }

    public function test_get_all_active_returns_empty_collection_when_none_active(): void
    {
        $this->makeHero(['is_active' => false]);

        $this->assertTrue(HeroSection::getAllActive()->isEmpty());
    }

    public function test_get_active_returns_first_active_slide(): void
    {
        $inactive = $this->makeHero(['is_active' => false, 'headline' => 'Inactive']);
        $active = $this->makeHero(['headline' => 'Active One']);

        $result = HeroSection::getActive();

        $this->assertInstanceOf(HeroSection::class, $result);
        $this->assertSame($active->id, $result->id);
        $this->assertNotSame($inactive->id, $result->id);
    }

    public function test_stats_are_cast_to_array(): void
    {
        $hero = $this->makeHero();

        $this->assertIsArray($hero->stats);
        $this->assertSame('15K+', $hero->stats[0]['value']);
        $this->assertSame('Shoe Styles', $hero->stats[1]['label']);
    }

    public function test_is_active_is_cast_to_boolean(): void
    {
        $this->assertTrue($this->makeHero()->is_active);
        $this->assertFalse($this->makeHero(['is_active' => false])->is_active);
    }

    public function test_hero_round_trips_stats_via_database(): void
    {
        $hero = $this->makeHero(['stats' => [['value' => '40%', 'label' => 'Max Discount']]]);
        $fresh = HeroSection::findOrFail($hero->id);

        $this->assertIsArray($fresh->stats);
        $this->assertSame('40%', $fresh->stats[0]['value']);
    }
}
