<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'full_name', 'phone',
        'delivery_region', 'delivery_district', 'delivery_area', 'delivery_landmark',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
