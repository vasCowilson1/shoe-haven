<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create cart for the given user.
     */
    public static function forUser(int $userId): static
    {
        return static::firstOrCreate(['user_id' => $userId]);
    }

    /**
     * Total number of items in the cart.
     */
    public function itemCount(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Convert DB cart to the session-cart format for compatibility.
     * Returns [productId => ['quantity' => n, 'size' => '...'], ...]
     */
    public function toSessionFormat(): array
    {
        $cart = [];
        foreach ($this->items as $item) {
            $cart[$item->product_id] = [
                'quantity' => $item->quantity,
                'size' => $item->size,
            ];
        }

        return $cart;
    }
}
