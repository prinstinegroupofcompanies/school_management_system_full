<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\SchoolAddon;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DefaultSchoolAndSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        try {
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        } catch (\Throwable $e) {
            // ignore if cache driver not available
        }
        $features = array_keys(config('school_features', []));

        // Create default school if none exists
        $defaultSchool = School::first();
        if (!$defaultSchool) {
            $defaultSchool = School::create([
                'name' => 'Default School',
                'code' => 'DEFAULT',
                'email' => 'info@school.com',
                'is_active' => true,
            ]);
        }
        // Ensure default school has all add-ons (for new or existing default school)
        foreach ($features as $key) {
            SchoolAddon::firstOrCreate(
                ['school_id' => $defaultSchool->id, 'feature_key' => $key],
                ['enabled' => true]
            );
        }

        // Ensure roles exist (avoid cache clear if cache table missing)
        if (!Role::where('name', 'super_admin')->where('guard_name', 'web')->exists()) {
            Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        }
        if (!Role::where('name', 'admin')->where('guard_name', 'web')->exists()) {
            Role::create(['name' => 'admin', 'guard_name' => 'web']);
        }

        // Super admin: superadmin@school.com (no school_id)
        $superAdmin = User::where('email', 'superadmin@school.com')->first();
        if (!$superAdmin) {
            $superAdmin = User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@school.com',
                'password' => Hash::make('password'),
                'user_type' => 'admin',
                'school_id' => null,
                'is_active' => true,
            ]);
            $superAdmin->assignRole('super_admin');
        } else {
            $superAdmin->update([
                'school_id' => null,
                'password' => Hash::make('password'),
            ]);
            if (!$superAdmin->hasRole('super_admin')) {
                $superAdmin->syncRoles(['super_admin']);
            }
        }

        // School admin: admin@school.com (belongs to default school)
        $admin = User::where('email', 'admin@school.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'School Admin',
                'email' => 'admin@school.com',
                'password' => Hash::make('password'),
                'user_type' => 'admin',
                'school_id' => $defaultSchool->id,
                'is_active' => true,
            ]);
            $admin->assignRole('admin');
        } else {
            $admin->update([
                'school_id' => $defaultSchool->id,
                'password' => Hash::make('password'),
            ]);
            if (!$admin->hasRole('admin')) {
                $admin->syncRoles(['admin']);
            }
        }

        // Migrate any other admin users (no school) to default school
        User::where('user_type', 'admin')
            ->whereNull('school_id')
            ->whereNotIn('id', [$superAdmin->id])
            ->update(['school_id' => $defaultSchool->id]);
    }
}
