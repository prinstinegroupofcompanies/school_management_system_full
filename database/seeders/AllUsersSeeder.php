<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Guardian;
use App\Models\ClassRoom;
use App\Models\TransportRoute;
use App\Models\Vehicle;
use App\Models\TransportAssignment;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AllUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cache (ignore if cache table doesn't exist)
        try {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Exception $e) {
            // Cache table might not exist, continue anyway
        }

        $this->command->info('Creating users for all roles...');
        $this->command->newLine();

        $credentials = [];

        // 1. Super Admin
        $superAdmin = $this->createUser([
            'name' => 'Super Administrator',
            'email' => 'superadmin@school.com',
            'username' => 'superadmin',
            'password' => 'SuperAdmin@2025',
            'user_type' => 'admin',
        ], 'super_admin');
        $credentials[] = ['Role' => 'Super Admin', 'Email' => $superAdmin->email, 'Username' => $superAdmin->username, 'Password' => 'SuperAdmin@2025'];

        // 2. Admin (VPI)
        $admin = $this->createUser([
            'name' => 'Vice Principal Instruction',
            'email' => 'vpi@school.com',
            'username' => 'vpi',
            'password' => 'VPI@2025',
            'user_type' => 'admin',
        ], 'vpi');
        $credentials[] = ['Role' => 'Admin (VPI)', 'Email' => $admin->email, 'Username' => $admin->username, 'Password' => 'VPI@2025'];

        // 3. Admin (VPA)
        $vpa = $this->createUser([
            'name' => 'Vice Principal Administration',
            'email' => 'vpa@school.com',
            'username' => 'vpa',
            'password' => 'VPA@2025',
            'user_type' => 'admin',
        ], 'vpa');
        $credentials[] = ['Role' => 'Admin (VPA)', 'Email' => $vpa->email, 'Username' => $vpa->username, 'Password' => 'VPA@2025'];

        // 4. Registrar
        $registrar = $this->createUser([
            'name' => 'Registrar',
            'email' => 'registrar@school.com',
            'username' => 'registrar',
            'password' => 'Registrar@2025',
            'user_type' => 'admin',
        ], 'registrar');
        $credentials[] = ['Role' => 'Registrar', 'Email' => $registrar->email, 'Username' => $registrar->username, 'Password' => 'Registrar@2025'];

        // 5. Teacher
        $teacher = $this->createUser([
            'name' => 'John Teacher',
            'email' => 'teacher@school.com',
            'username' => 'teacher',
            'password' => 'Teacher@2025',
            'user_type' => 'teacher',
        ], 'teacher');
        
        // Create teacher profile (with minimal required fields)
        $department = \App\Models\Department::first();
        $designation = \App\Models\Designation::first();
        
        $teacherProfile = \App\Models\Teacher::firstOrCreate(
            ['user_id' => $teacher->id],
            [
                'employee_id' => 'EMP001',
                'department_id' => $department ? $department->id : null,
                'designation_id' => $designation ? $designation->id : null,
                'qualification' => 'Bachelor\'s Degree',
                'experience_years' => 5,
                'joining_date' => now()->subYears(2),
                'salary' => 50000.00,
                'status' => 'active',
                'is_active' => true,
            ]
        );
        $credentials[] = ['Role' => 'Teacher', 'Email' => $teacher->email, 'Username' => $teacher->username, 'Password' => 'Teacher@2025'];

        // 6. Librarian
        $librarian = $this->createUser([
            'name' => 'Sarah Librarian',
            'email' => 'librarian@school.com',
            'username' => 'librarian',
            'password' => 'Librarian@2025',
            'user_type' => 'librarian',
        ], 'librarian');
        $credentials[] = ['Role' => 'Librarian', 'Email' => $librarian->email, 'Username' => $librarian->username, 'Password' => 'Librarian@2025'];

        // 7. Accountant/Finance
        $accountant = $this->createUser([
            'name' => 'Finance Officer',
            'email' => 'finance@school.com',
            'username' => 'finance',
            'password' => 'Finance@2025',
            'user_type' => 'finance',
        ], 'accountant');
        $credentials[] = ['Role' => 'Accountant/Finance', 'Email' => $accountant->email, 'Username' => $accountant->username, 'Password' => 'Finance@2025'];

        // 8. Conductor/Driver
        $driver = $this->createUser([
            'name' => 'Driver Conductor',
            'email' => 'driver@school.com',
            'username' => 'driver',
            'password' => 'Driver@2025',
            'user_type' => 'staff',
        ], 'conductor_driver');
        
        // Create transport assignment if route/vehicle exists
        $route = TransportRoute::first();
        $vehicle = Vehicle::first();
        if ($route && $vehicle) {
            TransportAssignment::firstOrCreate(
                [
                    'user_id' => $driver->id,
                    'vehicle_id' => $vehicle->id,
                    'route_id' => $route->id,
                ],
                [
                    'assigned_from' => now(),
                    'is_active' => true,
                ]
            );
        }
        $credentials[] = ['Role' => 'Driver/Conductor', 'Email' => $driver->email, 'Username' => $driver->username, 'Password' => 'Driver@2025'];

        // 9. Parent (Create first for guardian_id)
        $parent = $this->createUser([
            'name' => 'Parent Guardian',
            'email' => 'parent@school.com',
            'username' => 'parent',
            'password' => 'Parent@2025',
            'user_type' => 'parent',
        ], 'parent');
        
        // Create guardian record
        $guardian = Guardian::firstOrCreate(
            ['user_id' => $parent->id],
            [
                'guardian_id' => 'G' . str_pad((Guardian::count() + 1), 4, '0', STR_PAD_LEFT),
                'relationship' => 'guardian',
                'is_primary_guardian' => true,
                'status' => 'active',
            ]
        );
        $credentials[] = ['Role' => 'Parent', 'Email' => $parent->email, 'Username' => $parent->username, 'Password' => 'Parent@2025'];

        // 10. Student (Link to guardian)
        $student = $this->createUser([
            'name' => 'Student Test',
            'email' => 'student@school.com',
            'username' => 'student.test.2025',
            'password' => 'Student@2025',
            'user_type' => 'student',
            'must_change_password' => false, // Set to true to test password change
        ], 'student');
        
        // Create student profile
        $class = ClassRoom::first();
        if ($class) {
            $studentProfile = Student::firstOrCreate(
                ['user_id' => $student->id],
                [
                    'admission_no' => 'ADM' . date('Y') . '001',
                    'student_id' => 'STU001',
                    'class_id' => $class->id,
                    'academic_year' => date('Y'),
                    'admission_date' => now(),
                    'first_name' => 'Student',
                    'last_name' => 'Test',
                    'gender' => 'male',
                    'date_of_birth' => now()->subYears(15),
                    'guardian_id' => $guardian->id,
                    'status' => 'active',
                    'is_active' => true,
                ]
            );
        }
        $credentials[] = ['Role' => 'Student', 'Email' => $student->email, 'Username' => $student->username, 'Password' => 'Student@2025'];

        // Display credentials table
        $this->command->info('==========================================');
        $this->command->info('        LOGIN CREDENTIALS SUMMARY');
        $this->command->info('==========================================');
        $this->command->newLine();

        foreach ($credentials as $cred) {
            $this->command->line("Role: <fg=cyan>{$cred['Role']}</>");
            $this->command->line("  Email: <fg=green>{$cred['Email']}</>");
            $this->command->line("  Username: <fg=yellow>{$cred['Username']}</>");
            $this->command->line("  Password: <fg=red>{$cred['Password']}</>");
            $this->command->newLine();
        }

        $this->command->info('==========================================');
        $this->command->info('All users created successfully!');
        $this->command->info('==========================================');
    }

    /**
     * Create a user with specified role.
     */
    protected function createUser(array $data, string $roleName): User
    {
        $user = User::updateOrCreate(
            ['email' => $data['email']],
            array_merge($data, [
                'password' => Hash::make($data['password']),
                'is_active' => true,
                'must_change_password' => $data['must_change_password'] ?? false,
            ])
        );

        // Assign role if it exists
        $role = Role::where('name', $roleName)->first();
        if ($role && !$user->hasRole($roleName)) {
            $user->assignRole($roleName);
        }

        return $user;
    }
}

