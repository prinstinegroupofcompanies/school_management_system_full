<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\FeeStructure;
use App\Models\FeePayment;
use App\Models\StudentAttendance;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@school.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'user_type' => 'admin',
                'is_active' => true,
                'status' => 'active',
            ]
        );

        // Create finance user
        User::firstOrCreate(
            ['email' => 'finance@school.com'],
            [
                'name' => 'Finance Officer',
                'password' => Hash::make('password'),
                'user_type' => 'finance',
                'is_active' => true,
                'status' => 'active',
            ]
        );

        // Create teacher user
        $teacherUser1 = User::firstOrCreate(
            ['email' => 'teacher@school.com'],
            [
                'name' => 'John Teacher',
                'password' => Hash::make('password'),
                'user_type' => 'teacher',
                'is_active' => true,
                'status' => 'active',
            ]
        );

        // Create departments first
        $mathDept = \App\Models\Department::firstOrCreate([
            'code' => 'MATH'
        ], [
            'name' => 'Mathematics Department',
            'description' => 'Mathematics and Science Department',
        ]);

        $designation = \App\Models\Designation::firstOrCreate([
            'name' => 'Teacher'
        ], [
            'description' => 'Teaching Staff',
        ]);

        // Create Teacher record for the first teacher user
        if (!Teacher::where('user_id', $teacherUser1->id)->exists()) {
            $existingTeacher = Teacher::where('employee_id', 'TCH001')->first();
            if (!$existingTeacher) {
                Teacher::create([
                    'user_id' => $teacherUser1->id,
                    'employee_id' => 'TCH001',
                    'department_id' => $mathDept->id,
                    'designation_id' => $designation->id,
                    'qualification' => 'BSc Mathematics',
                    'joining_date' => now()->subYears(3),
                    'salary' => 50000,
                    'employment_status' => 'active',
                ]);
            }
        }

        // Create sample class
        $class = ClassRoom::firstOrCreate([
            'code' => 'G10A'
        ], [
            'name' => 'Grade 10A',
            'description' => 'Grade 10 Class A',
        ]);

        // Create sample subject
        $subject = Subject::firstOrCreate([
            'code' => 'MATH101'
        ], [
            'name' => 'Mathematics',
            'description' => 'Basic Mathematics',
        ]);

        // Create sample student
        $studentUser = User::firstOrCreate(
            ['email' => 'student@school.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'user_type' => 'student',
                'is_active' => true,
                'status' => 'active',
            ]
        );

        if (!Student::where('user_id', $studentUser->id)->exists()) {
            Student::create([
                'user_id' => $studentUser->id,
                'class_id' => $class->id,
                'student_id' => 'STU001',
                'academic_year' => date('Y'),
                'date_of_birth' => '2005-01-01',
                'address' => 'Sample Address',
                'phone' => '1234567890',
                'admission_no' => 'ADM001',
                'admission_date' => now()->subMonths(6),
                'status' => 'active',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'gender' => 'male',
            ]);
        }

        // Create additional teacher with proper relationships
        $teacherUser2 = User::firstOrCreate(
            ['email' => 'jane.teacher@school.com'],
            [
                'name' => 'Jane Teacher',
                'password' => Hash::make('password'),
                'user_type' => 'teacher',
                'is_active' => true,
                'status' => 'active',
            ]
        );

        $scienceDept = \App\Models\Department::firstOrCreate([
            'code' => 'SCI'
        ], [
            'name' => 'Science Department',
            'description' => 'Science and Chemistry Department',
        ]);

        if (!Teacher::where('user_id', $teacherUser2->id)->exists()) {
            $existingTeacher2 = Teacher::where('employee_id', 'TCH002')->first();
            if (!$existingTeacher2) {
                Teacher::create([
                    'user_id' => $teacherUser2->id,
                    'employee_id' => 'TCH002',
                    'department_id' => $scienceDept->id,
                    'designation_id' => $designation->id,
                    'qualification' => 'MSc Chemistry',
                    'joining_date' => now()->subYears(5),
                    'salary' => 60000,
                    'employment_status' => 'active',
                ]);
            }
        }

        // Create sample fee structures
        if (!FeeStructure::where('class_id', $class->id)->exists()) {
            FeeStructure::create([
                'class_id' => $class->id,
                'fee_type' => 'tuition',
                'amount' => 50000, // 50,000 LRD
                'due_date' => now()->addMonths(1),
                'academic_year' => date('Y'),
                'semester' => 1,
                'description' => 'Tuition Fee for Grade 10A',
            ]);

            FeeStructure::create([
                'class_id' => $class->id,
                'fee_type' => 'library',
                'amount' => 5000, // 5,000 LRD
                'due_date' => now()->addMonths(1),
                'academic_year' => date('Y'),
                'semester' => 1,
                'description' => 'Library Fee for Grade 10A',
            ]);
        }

        // Create sample fee payments
        if (!FeePayment::where('student_id', $studentUser->student->id ?? null)->exists() && $studentUser->student) {
            $feeStructure = FeeStructure::where('class_id', $class->id)->first();
            if ($feeStructure) {
                FeePayment::create([
                    'student_id' => $studentUser->student->id,
                    'fee_structure_id' => $feeStructure->id,
                    'amount' => $feeStructure->amount,
                    'amount_paid' => $feeStructure->amount * 0.5, // 50% paid
                    'payment_method' => 'cash',
                    'payment_date' => now()->subDays(5),
                    'status' => 'paid',
                    'payment_notes' => 'Partial payment',
                ]);
            }
        }

        // Create sample student attendance
        if (!StudentAttendance::where('student_id', $studentUser->student->id ?? null)->exists() && $studentUser->student) {
            for ($i = 1; $i <= 10; $i++) {
                StudentAttendance::create([
                    'student_id' => $studentUser->student->id,
                    'class_id' => $class->id,
                    'date' => now()->subDays($i),
                    'status' => $i <= 8 ? 'present' : 'absent', // 80% attendance
                    'marked_by' => $teacherUser1->id,
                ]);
            }
        }

        $this->command->info('Production data seeded successfully!');
        $this->command->info('Admin: admin@school.com / password');
        $this->command->info('Finance: finance@school.com / password');
        $this->command->info('Teacher: teacher@school.com / password');
        $this->command->info('Student: student@school.com / password');
    }
}
