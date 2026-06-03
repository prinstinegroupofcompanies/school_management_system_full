<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Clear cache (ignore if cache table doesn't exist)
            try {
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            } catch (\Exception $e) {
                // Cache table might not exist, continue anyway
            }

            // Ensure super_admin role exists
            $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);

            // Get all permissions and assign to super_admin
            $allPermissions = Permission::all();
            if ($allPermissions->isNotEmpty()) {
                $superAdminRole->syncPermissions($allPermissions);
            }

            // Create or update super admin user
            $superAdmin = User::firstOrCreate(
                ['email' => 'superadmin@school.com'],
                [
                    'name' => 'Super Administrator',
                    'username' => 'superadmin',
                    'password' => Hash::make('SuperAdmin@2025'),
                    'user_type' => 'admin',
                    'is_active' => true,
                    'must_change_password' => false,
                ]
            );

            // Assign super_admin role
            if (!$superAdmin->hasRole('super_admin')) {
                $superAdmin->assignRole('super_admin');
            }

            // Give all permissions directly (belt and suspenders)
            if ($allPermissions->isNotEmpty()) {
                $superAdmin->givePermissionTo($allPermissions);
            }

            $this->command->info('Super Admin created/updated: superadmin@school.com');
            $this->command->info('Default password: SuperAdmin@2025');
        } catch (\Exception $e) {
            $this->command->error('Error creating super admin: ' . $e->getMessage());
        }
    }
}
