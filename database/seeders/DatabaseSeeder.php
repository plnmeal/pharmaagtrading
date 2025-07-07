<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class, // Add this line
            // Other seeders will go here later (e.g., ProductSeeder, NewsSeeder)
        ]);
    }
}