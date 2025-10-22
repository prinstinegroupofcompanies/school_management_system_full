<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Guardian;

class StudentSeeder extends Seeder
{
    public function run()
    {
        // Get classes that have a class teacher
        $classes = ClassRoom::whereNotNull('class_teacher_id')->get();
        
        if ($classes->isEmpty()) {
            $this->command->info('No classes with teachers found. Please run TeacherSeeder first.');
            return;
        }

        // Get or create a guardian
        $guardian = Guardian::first();
        if (!$guardian) {
            $guardianUser = User::firstOrCreate([
                'email' => 'guardian@school.com'
            ], [
                'name' => 'Guardian User',
                'password' => bcrypt('password'),
                'user_type' => 'parent',
                'is_active' => true,
            ]);

            $guardian = Guardian::create([
                'user_id' => $guardianUser->id,
                'guardian_id' => 'GUA' . str_pad($guardianUser->id, 4, '0', STR_PAD_LEFT),
                'relationship' => 'Parent',
            ]);
        }

        foreach ($classes as $class) {
            // Create 5 students for each class
            for ($i = 1; $i <= 5; $i++) {
                $studentUser = User::firstOrCreate([
                    'email' => 'student' . $class->id . $i . '@school.com'
                ], [
                    'name' => 'Student ' . $class->name . ' ' . $i,
                    'password' => bcrypt('password'),
                    'user_type' => 'student',
                    'is_active' => true,
                ]);

                if (!$studentUser->student) {
                    Student::create([
                        'user_id' => $studentUser->id,
                        'student_id' => 'STU' . str_pad($studentUser->id, 4, '0', STR_PAD_LEFT),
                        'admission_number' => 'ADM' . str_pad($studentUser->id, 4, '0', STR_PAD_LEFT),
                        'admission_no' => 'ADM' . str_pad($studentUser->id, 4, '0', STR_PAD_LEFT),
                        'class_id' => $class->id,
                        'guardian_id' => $guardian->id,
                        'first_name' => 'Student',
                        'last_name' => $class->name . ' ' . $i,
                        'date_of_birth' => now()->subYears(15 + $i),
                        'gender' => $i % 2 == 0 ? 'female' : 'male',
                        'phone' => '123456789' . $i,
                        'address' => 'Student Address ' . $i,
                        'admission_date' => now()->subMonths(6),
                        'status' => 'active',
                        'academic_year' => date('Y'),
                        'curriculum_type' => 'international',
                    ]);
                }
            }
        }

        $this->command->info('Students created successfully for all classes with teachers!');
    }
}