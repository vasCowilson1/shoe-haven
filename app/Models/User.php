<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'sidebar_prefs',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'sidebar_prefs' => 'array',
        ];
    }

    public function sidebarPref(string $key, mixed $default = null): mixed
    {
        return data_get($this->sidebar_prefs ?? [], $key, $default);
    }

    public function updateSidebarPrefs(array $prefs): void
    {
        $merged = array_replace($this->sidebar_prefs ?? [], $prefs);
        $this->update(['sidebar_prefs' => $merged]);
    }

    public function roleNames(): array
    {
        return $this->roles()->pluck('role')->all();
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('role', $role)->exists();
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function wishlist(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class);
    }

    public function loyaltyPoints(): HasOne
    {
        return $this->hasOne(LoyaltyPoint::class);
    }

    public function inventoryChanges(): HasMany
    {
        return $this->hasMany(InventoryHistory::class, 'changed_by');
    }

    public function isAdmin(): bool
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->contains('role', 'admin');
        }

        return $this->roles()->where('role', 'admin')->exists();
    }
}
