<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'phone' => '+254700000000', // Use a valid phone number
            'role' => 'admin',
            'is_approved' => true,
            'is_admin' => true,
            'password' => Hash::make('1234'), // Use your preferred password
        ]);
        
        $this->command->info('Admin user created successfully!');
    }
}