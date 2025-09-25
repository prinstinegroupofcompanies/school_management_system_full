<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // RolePermissionSeeder::class, // Commented out due to Spatie Permission incompatibility
            UserSeeder::class,
            DebugSeeder::class, // Debug seeder with comprehensive data
            ScholarshipSeeder::class,
            ProductionSeeder::class, // Add production data for Render deployment
            InternationalSystemSeeder::class, // Enhanced international features
        ]);
    }
}
