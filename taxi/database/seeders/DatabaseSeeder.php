<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear existing users if needed (optional)
        // User::truncate(); // Uncomment this line to clear existing users

        // Create a test user with a unique phone number
        User::factory()->create([
            'name' => 'Test User',
            'phone' => '+11234567890', // Ensure this phone number is unique
        ]);

        // Create multiple users with unique phone numbers
        for ($i = 0; $i < 10; $i++) {
            User::factory()->create([
                'phone' => '+1' . fake()->unique()->numberBetween(1000000000, 9999999999), // Generate unique phone numbers
            ]);
        }

        $this->call(CountiesSeeder::class); // Call the CountiesSeeder
    }
}
