<?php

namespace Database\Seeders;

use App\Models\User; // Make sure to import the User model
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Import Hash facade

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a default admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@ayuva.com',
            'password' => Hash::make('password'), // Hash the password
            'email_verified_at' => now(), // Mark email as verified
        ]);

        // You can add more users or roles here later if needed
    }
}