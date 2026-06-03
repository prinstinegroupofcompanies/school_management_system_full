<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            
            // Student management
            'view students',
            'create students',
            'edit students',
            'delete students',
            'view student attendance',
            'mark student attendance',
            'view student fees',
            'manage student fees',
            'view student exams',
            'manage student exams',
            'view student homework',
            'manage student homework',
            
            // Teacher management
            'view teachers',
            'create teachers',
            'edit teachers',
            'delete teachers',
            'view teacher attendance',
            'manage teacher attendance',
            'view teacher schedule',
            'manage teacher schedule',
            
            // Class management
            'view classes',
            'create classes',
            'edit classes',
            'delete classes',
            'manage class students',
            'manage class subjects',
            'view class attendance',
            'manage class attendance',
            
            // Subject management
            'view subjects',
            'create subjects',
            'edit subjects',
            'delete subjects',
            'manage subject teachers',
            'manage subject materials',
            
            // Fee management
            'view fees',
            'create fees',
            'edit fees',
            'delete fees',
            'view fee payments',
            'manage fee payments',
            'view fee reports',
            'generate fee reports',
            
            // Exam management
            'view exams',
            'create exams',
            'edit exams',
            'delete exams',
            'view exam marks',
            'manage exam marks',
            'view exam reports',
            'generate exam reports',
            
            // Library management
            'view books',
            'create books',
            'edit books',
            'delete books',
            'manage book issues',
            'view library members',
            'manage library members',
            'view library reports',
            
            // Transport management
            'view transport routes',
            'create transport routes',
            'edit transport routes',
            'delete transport routes',
            'view vehicles',
            'create vehicles',
            'edit vehicles',
            'delete vehicles',
            'view transport reports',
            
            // Hostel management
            'view hostel rooms',
            'create hostel rooms',
            'edit hostel rooms',
            'delete hostel rooms',
            'manage room assignments',
            'view hostel reports',
            
            // Online exam management
            'view online exams',
            'create online exams',
            'edit online exams',
            'delete online exams',
            'manage exam questions',
            'view exam attempts',
            'grade exam attempts',
            
            // Study materials
            'view study materials',
            'create study materials',
            'edit study materials',
            'delete study materials',
            'approve study materials',
            
            // Homework management
            'view homework',
            'create homework',
            'edit homework',
            'delete homework',
            'grade homework',
            'view homework submissions',
            
            // System administration
            'view dashboard',
            'view system settings',
            'manage system settings',
            'view system logs',
            'manage backups',
            'bypass maintenance mode',
            'view statistics',
            'generate reports',
            
            // Lesson plan management
            'review lesson plans',
            'approve lesson plans',
            'reject lesson plans',
            
            // Enrollment
            'manage enrollment',
            'generate admission letters',
            
            // Transport
            'view assigned route',
            'view assigned vehicle',
            'report maintenance',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $roles = [
            'super_admin' => $permissions,
            'admin' => [
                'view users', 'create users', 'edit users', 'delete users',
                'view students', 'create students', 'edit students', 'delete students',
                'view teachers', 'create teachers', 'edit teachers', 'delete teachers',
                'view classes', 'create classes', 'edit classes', 'delete classes',
                'view subjects', 'create subjects', 'edit subjects', 'delete subjects',
                'view fees', 'create fees', 'edit fees', 'delete fees',
                'view exams', 'create exams', 'edit exams', 'delete exams',
                'view books', 'create books', 'edit books', 'delete books',
                'view transport routes', 'create transport routes', 'edit transport routes',
                'view vehicles', 'create vehicles', 'edit vehicles',
                'view hostel rooms', 'create hostel rooms', 'edit hostel rooms',
                'view dashboard', 'view system settings', 'manage system settings',
                'view statistics', 'generate reports',
            ],
            'teacher' => [
                'view students', 'view student attendance', 'mark student attendance',
                'view student exams', 'manage student exams', 'view student homework',
                'manage student homework', 'view classes', 'view subjects',
                'view exams', 'create exams', 'edit exams', 'view exam marks',
                'manage exam marks', 'view study materials', 'create study materials',
                'edit study materials', 'view homework', 'create homework',
                'edit homework', 'grade homework', 'view homework submissions',
                'view dashboard', 'view statistics',
            ],
            'student' => [
                'view own profile', 'edit own profile', 'view own attendance',
                'view own exams', 'view own fees', 'view own homework',
                'submit homework', 'view study materials', 'view library books',
                'view own schedule', 'view own grades',
            ],
            'parent' => [
                'view own children', 'view children attendance', 'view children exams',
                'view children fees', 'view children homework', 'view children grades',
                'view children schedule',
            ],
            'staff' => [
                'view students', 'view teachers', 'view classes', 'view subjects',
                'view fees', 'view exams', 'view books', 'view transport routes',
                'view vehicles', 'view hostel rooms', 'view dashboard',
            ],
            'librarian' => [
                'view books', 'create books', 'edit books', 'delete books',
                'manage book issues', 'view library members', 'manage library members',
                'view library reports', 'view dashboard',
            ],
            'accountant' => [
                'view fees', 'create fees', 'edit fees', 'delete fees',
                'view fee payments', 'manage fee payments', 'view fee reports',
                'generate fee reports', 'view dashboard', 'view statistics',
            ],
            'vpi' => [
                'view users', 'create users', 'edit users', 'delete users',
                'view students', 'create students', 'edit students', 'delete students',
                'view teachers', 'create teachers', 'edit teachers', 'delete teachers',
                'view classes', 'create classes', 'edit classes', 'delete classes',
                'view subjects', 'create subjects', 'edit subjects', 'delete subjects',
                'view fees', 'create fees', 'edit fees', 'delete fees',
                'view exams', 'create exams', 'edit exams', 'delete exams',
                'view books', 'create books', 'edit books', 'delete books',
                'view transport routes', 'create transport routes', 'edit transport routes',
                'view vehicles', 'create vehicles', 'edit vehicles',
                'view hostel rooms', 'create hostel rooms', 'edit hostel rooms',
                'view dashboard', 'view system settings', 'manage system settings',
                'view statistics', 'generate reports',
                'review lesson plans', 'approve lesson plans', 'reject lesson plans',
                'view teacher attendance', 'manage teacher attendance',
            ],
            'vpa' => [
                'view users', 'view students', 'view teachers',
                'view classes', 'view subjects', 'view fees', 'view exams',
                'view books', 'view transport routes', 'view vehicles',
                'view hostel rooms', 'view dashboard', 'view statistics',
            ],
            'conductor_driver' => [
                'view transport routes', 'view vehicles',
                'view assigned route', 'view assigned vehicle',
                'report maintenance', 'view dashboard',
            ],
            'librarian' => [
                'view books', 'create books', 'edit books', 'delete books',
                'manage book issues', 'view library members', 'manage library members',
                'view library reports', 'view dashboard',
            ],
            'registrar' => [
                'view students', 'create students', 'edit students',
                'view classes', 'generate admission letters',
                'manage enrollment', 'view dashboard',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::create(['name' => $roleName]);
            $role->givePermissionTo($rolePermissions);
        }
    }
}
