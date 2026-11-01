<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@shoehaven.com',
            'password' => Hash::make('password'),
        ]);

        UserRole::create([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);

        Profile::create([
            'user_id' => $admin->id,
            'full_name' => 'ShoeHaven Admin',
            'phone' => '+256 700 000 000',
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->call([
            RealisticProductsSeeder::class,
            SixHeroSectionSeeder::class,
        ]);
    }
}
