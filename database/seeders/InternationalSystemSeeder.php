<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ClassFeeStructure;
use App\Models\ClassRoom;
use App\Models\Student;

class InternationalSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding International School Management System...');
        
        // Create fee structures for existing classes
        $this->createClassFeeStructures();
        
        // Update existing students with auto-generated fields
        $this->updateExistingStudents();
        
        $this->command->info('International system seeding completed!');
    }

    private function createClassFeeStructures()
    {
        $this->command->info('Creating class fee structures...');
        
        $classes = ClassRoom::all();
        $currentYear = date('Y');
        
        foreach ($classes as $class) {
            // Skip if fee structure already exists
            if (ClassFeeStructure::where('class_id', $class->id)
                                ->where('academic_year', $currentYear)
                                ->exists()) {
                continue;
            }

            $tuitionFee = $this->getTuitionFeeByClass($class->name);
            $mandatoryFees = $tuitionFee + 1800; // tuition + other mandatory fees
            $optionalFees = 1400; // optional fees
            
            ClassFeeStructure::create([
                'class_id' => $class->id,
                'academic_year' => $currentYear,
                'tuition_fee' => $tuitionFee,
                'registration_fee' => 500,
                'library_fee' => 200,
                'laboratory_fee' => 300,
                'sports_fee' => 150,
                'technology_fee' => 250,
                'examination_fee' => 400,
                'activity_fee' => 100,
                'uniform_fee' => 350,
                'book_fee' => 450,
                'miscellaneous_fee' => 100,
                'total_mandatory_fees' => $mandatoryFees,
                'total_optional_fees' => $optionalFees,
                'total_fees' => $mandatoryFees + $optionalFees,
                'payment_frequency' => 'semester',
                'installments_allowed' => 2,
                'late_fee_percentage' => 5.0,
                'grace_period_days' => 7,
                'is_active' => true,
                'effective_from' => now()->startOfYear(),
                'effective_to' => now()->endOfYear(),
            ]);
        }
    }

    private function getTuitionFeeByClass($className): float
    {
        // Different tuition fees based on class level
        if (str_contains(strtolower($className), 'kindergarten')) {
            return 15000;
        } elseif (preg_match('/grade [1-5]/i', $className)) {
            return 18000;
        } elseif (preg_match('/grade [6-8]/i', $className)) {
            return 22000;
        } elseif (preg_match('/grade (9|10|11|12)/i', $className)) {
            return 28000;
        } else {
            return 20000; // Default
        }
    }

    private function updateExistingStudents()
    {
        $this->command->info('Updating existing students with auto-generated fields...');
        
        $students = Student::whereNull('admission_number')->get();
        
        foreach ($students as $student) {
            if (!$student->admission_number) {
                $student->admission_number = Student::generateAdmissionNumber();
            }
            if (!$student->student_number) {
                $student->student_number = Student::generateStudentNumber();
            }
            if (!$student->international_student_id) {
                $student->international_student_id = Student::generateInternationalStudentId();
            }
            
            $student->save();
            
            // Auto-assign subjects and fees
            $student->autoAssignSubjectsAndTeachers();
            $student->autoAssignFeeStructure();
        }
    }
}
