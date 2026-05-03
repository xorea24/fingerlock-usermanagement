<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // This creates your specific Admin user
        User::factory()->create([
            'name' => 'Jireh Delacruz',
            'email' => 'jirehdelacruz@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Much cleaner than the long hash!
            'remember_token' => Str::random(10),
            'is_admin' => 1,
        ]);

        // Optional: Create 10 random users for testing your tracking system
        // User::factory(10)->create();
    }
}