<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\FeePayment;
use App\Models\PaymentRecord;
use App\Models\StudentAttendance;
use App\Models\TeacherAttendance;
use App\Models\ExamSchedule;
use App\Models\Notification;
use App\Models\StudentFee;
use Illuminate\Support\Facades\Hash;

class DebugSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Starting debug seeder...');

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@school.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'user_type' => 'admin',
                'is_active' => true,
                'status' => 'active',
            ]
        );
        $this->command->info('Admin user created: ' . $admin->email);

        // Create teacher user
        $teacherUser = User::firstOrCreate(
            ['email' => 'teacher@school.com'],
            [
                'name' => 'Teacher User',
                'password' => Hash::make('password'),
                'user_type' => 'teacher',
                'is_active' => true,
                'status' => 'active',
            ]
        );
        $this->command->info('Teacher user created: ' . $teacherUser->email);

        // Create student user
        $studentUser = User::firstOrCreate(
            ['email' => 'student@school.com'],
            [
                'name' => 'Student User',
                'password' => Hash::make('password'),
                'user_type' => 'student',
                'is_active' => true,
                'status' => 'active',
            ]
        );
        $this->command->info('Student user created: ' . $studentUser->email);

        // Create finance user
        $financeUser = User::firstOrCreate(
            ['email' => 'finance@school.com'],
            [
                'name' => 'Finance User',
                'password' => Hash::make('password'),
                'user_type' => 'finance',
                'is_active' => true,
                'status' => 'active',
            ]
        );
        $this->command->info('Finance user created: ' . $financeUser->email);

        // Create class
        $class = ClassRoom::firstOrCreate(
            ['name' => 'Grade 10A'],
            [
                'code' => 'G10A',
                'description' => 'Grade 10 Class A',
            ]
        );
        $this->command->info('Class created: ' . $class->name);

        // Create subject
        $subject = Subject::firstOrCreate(
            ['name' => 'Mathematics'],
            [
                'description' => 'Basic Mathematics',
            ]
        );
        $this->command->info('Subject created: ' . $subject->name);

        // Create department first
        $department = \App\Models\Department::firstOrCreate(
            ['name' => 'Mathematics'],
            [
                'description' => 'Mathematics Department',
            ]
        );

        // Create designation first
        $designation = \App\Models\Designation::firstOrCreate(
            ['name' => 'Senior Teacher'],
            [
                'code' => 'ST',
                'description' => 'Senior Teacher Position',
            ]
        );

        // Create teacher record
        $teacher = Teacher::firstOrCreate(
            ['employee_id' => 'TCH001'],
            [
                'user_id' => $teacherUser->id,
                'department_id' => $department->id,
                'designation_id' => $designation->id,
                'qualification' => 'BSc Mathematics',
                'joining_date' => now()->subYears(2),
                'salary' => 50000,
                'employment_status' => 'active',
            ]
        );
        $this->command->info('Teacher record created: ' . $teacher->employee_id);

        // Create student record
        $student = Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'student_id' => 'STU001',
                'class_id' => $class->id,
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
            ]
        );
        $this->command->info('Student record created: ' . $student->student_id);

        // Create fee structure first
        $feeStructure = \App\Models\FeeStructure::firstOrCreate(
            ['name' => 'Basic Fee'],
            [
                'class_id' => $class->id,
                'academic_year' => date('Y'),
                'total_amount' => 1000,
                'final_amount' => 1000,
                'due_date' => now()->addMonth(),
            ]
        );

        // Create sample fee payment
        $feePayment = FeePayment::firstOrCreate(
            ['student_id' => $student->id, 'payment_date' => today()],
            [
                'fee_structure_id' => $feeStructure->id,
                'payment_no' => 'PAY' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'amount_due' => 1000,
                'amount_total' => 1000,
                'amount_paid' => 1000,
                'balance_amount' => 0,
                'due_date' => now()->addMonth(),
                'status' => 'paid',
                'payment_method' => 'cash',
            ]
        );
        $this->command->info('Fee payment created: $' . $feePayment->amount_paid);

        // Create sample payment record
        $paymentRecord = PaymentRecord::firstOrCreate(
            ['student_id' => $student->id, 'amount' => 500],
            [
                'status' => 'approved',
                'payment_method' => 'bank_transfer',
            ]
        );
        $this->command->info('Payment record created: $' . $paymentRecord->amount);

        // Create sample student attendance
        $studentAttendance = StudentAttendance::firstOrCreate(
            ['student_id' => $student->id, 'attendance_date' => today()],
            [
                'status' => 'present',
            ]
        );
        $this->command->info('Student attendance created: ' . $studentAttendance->status);

        // Create sample teacher attendance
        $teacherAttendance = TeacherAttendance::firstOrCreate(
            ['teacher_id' => $teacher->id, 'date' => today()],
            [
                'status' => 'present',
            ]
        );
        $this->command->info('Teacher attendance created: ' . $teacherAttendance->status);

        // Create sample exam schedule
        $examSchedule = ExamSchedule::firstOrCreate(
            ['exam_name' => 'Midterm Exam', 'exam_date' => now()->addDays(7)],
            [
                'subject_id' => $subject->id,
                'class_id' => $class->id,
                'start_time' => '09:00',
                'end_time' => '12:00',
                'is_live' => false,
            ]
        );
        $this->command->info('Exam schedule created: ' . $examSchedule->exam_name);

        // Create sample notification
        $notification = Notification::firstOrCreate(
            ['user_id' => $admin->id, 'title' => 'Welcome to School Management System'],
            [
                'message' => 'System has been successfully deployed!',
                'type' => 'info',
            ]
        );
        $this->command->info('Notification created: ' . $notification->title);

        // Create sample student fee
        $studentFee = StudentFee::firstOrCreate(
            ['student_id' => $student->id, 'fee_type' => 'tuition'],
            [
                'amount' => 2000,
                'balance' => 1000,
                'status' => 'pending',
                'due_date' => now()->addMonth(),
            ]
        );
        $this->command->info('Student fee created: $' . $studentFee->amount);

        $this->command->info('Debug seeder completed successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@school.com / password');
        $this->command->info('Teacher: teacher@school.com / password');
        $this->command->info('Student: student@school.com / password');
        $this->command->info('Finance: finance@school.com / password');
    }
}
