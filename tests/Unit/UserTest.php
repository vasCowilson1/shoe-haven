<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_admin_returns_true_when_user_has_admin_role(): void
    {
        $user = User::factory()->create();
        UserRole::create(['user_id' => $user->id, 'role' => 'admin']);

        $this->assertTrue($user->isAdmin());
    }

    public function test_is_admin_returns_false_when_user_has_only_user_role(): void
    {
        $user = User::factory()->create();
        UserRole::create(['user_id' => $user->id, 'role' => 'user']);

        $this->assertFalse($user->isAdmin());
    }

    public function test_is_admin_returns_false_when_user_has_no_roles(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->isAdmin());
    }

    public function test_user_has_one_profile(): void
    {
        $user = User::factory()->create();
        Profile::create(['user_id' => $user->id, 'full_name' => 'John Doe', 'phone' => '0777000000']);

        $this->assertInstanceOf(Profile::class, $user->profile);
        $this->assertSame('John Doe', $user->profile->full_name);
    }

    public function test_user_has_many_roles(): void
    {
        $user = User::factory()->create();
        UserRole::create(['user_id' => $user->id, 'role' => 'admin']);
        UserRole::create(['user_id' => $user->id, 'role' => 'user']);

        $this->assertCount(2, $user->roles);
    }

    public function test_user_has_many_orders(): void
    {
        $user = User::factory()->create();
        Order::create([
            'user_id' => $user->id,
            'total_amount' => 150000,
            'payment_method' => 'MTN Mobile Money',
            'payment_phone' => '0777000000',
            'status' => 'pending',
            'payment_status' => 'pending',
            'transaction_id' => 'TXN-TEST123',
            'shipping_name' => 'John Doe',
            'shipping_address' => 'Kampala',
            'shipping_phone' => '0777000000',
        ]);

        $this->assertCount(1, $user->orders);
        $this->assertInstanceOf(Order::class, $user->orders->first());
    }

    public function test_password_is_hashed(): void
    {
        $user = User::factory()->create(['password' => 'secret-password']);

        $this->assertNotSame('secret-password', $user->password);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('secret-password', $user->password));
    }
}
