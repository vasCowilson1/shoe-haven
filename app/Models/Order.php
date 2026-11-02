<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'total_amount', 'payment_method', 'payment_phone',
        'status', 'payment_status', 'transaction_id', 'payment_initiated_at',
        'shipping_name', 'shipping_address', 'shipping_city',
        'shipping_region', 'shipping_district', 'shipping_area', 'shipping_landmark', 'shipping_phone',
        'pesajet_reference',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
