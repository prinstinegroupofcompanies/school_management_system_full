<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@school.com',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'is_active' => true,
        ]);

        // Create finance user
        User::create([
            'name' => 'Finance Officer',
            'email' => 'finance@school.com',
            'password' => Hash::make('password'),
            'user_type' => 'finance',
            'is_active' => true,
        ]);

        // Create teacher user
        User::create([
            'name' => 'John Teacher',
            'email' => 'teacher@school.com',
            'password' => Hash::make('password'),
            'user_type' => 'teacher',
            'is_active' => true,
        ]);

        // Create sample class
        $class = ClassRoom::create([
            'name' => 'Grade 10A',
            'code' => 'G10A',
            'description' => 'Grade 10 Class A',
        ]);

        // Create sample subject
        $subject = Subject::create([
            'name' => 'Mathematics',
            'code' => 'MATH101',
            'description' => 'Basic Mathematics',
        ]);

        // Create sample student
        $studentUser = User::create([
            'name' => 'John Doe',
            'email' => 'student@school.com',
            'password' => Hash::make('password'),
            'user_type' => 'student',
            'is_active' => true,
        ]);

        Student::create([
            'user_id' => $studentUser->id,
            'class_id' => $class->id,
            'student_id' => 'STU001',
            'date_of_birth' => '2005-01-01',
            'address' => 'Sample Address',
            'phone' => '1234567890',
        ]);

        // Create sample teacher
        $teacherUser = User::create([
            'name' => 'Jane Teacher',
            'email' => 'jane.teacher@school.com',
            'password' => Hash::make('password'),
            'user_type' => 'teacher',
            'is_active' => true,
        ]);

        Teacher::create([
            'user_id' => $teacherUser->id,
            'employee_id' => 'TCH001',
            'department' => 'Mathematics',
            'qualification' => 'MSc Mathematics',
            'experience_years' => 5,
        ]);

        $this->command->info('Production data seeded successfully!');
        $this->command->info('Admin: admin@school.com / password');
        $this->command->info('Finance: finance@school.com / password');
        $this->command->info('Teacher: teacher@school.com / password');
        $this->command->info('Student: student@school.com / password');
    }
}
