<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSection extends Model
{
    protected $fillable = [
        'headline',
        'subtitle',
        'button_text',
        'button_url',
        'secondary_button_text',
        'secondary_button_url',
        'image',
        'stats',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'stats' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }

    public static function getAllActive()
    {
        return static::where('is_active', true)->get();
    }
}
