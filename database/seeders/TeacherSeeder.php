<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\Student;

class TeacherSeeder extends Seeder
{
    public function run()
    {
        // Get or create a department
        $department = Department::firstOrCreate([
            'name' => 'Mathematics',
            'description' => 'Mathematics Department'
        ]);

        // Get or create a designation
        $designation = Designation::firstOrCreate([
            'name' => 'Senior Teacher',
            'description' => 'Senior Teacher Position'
        ]);

        // Get the teacher user
        $teacherUser = User::where('email', 'teacher@school.com')->first();
        
        if ($teacherUser) {
            // Get or create teacher record
            $teacher = $teacherUser->teacher ?? Teacher::create([
                'user_id' => $teacherUser->id,
                'teacher_id' => 'TCH' . str_pad($teacherUser->id, 4, '0', STR_PAD_LEFT),
                'employee_id' => 'EMP' . str_pad($teacherUser->id, 4, '0', STR_PAD_LEFT),
                'department_id' => $department->id,
                'designation_id' => $designation->id,
                'qualification' => 'M.Sc Mathematics',
                'specialization' => 'Advanced Mathematics',
                'experience_years' => 5,
                'joining_date' => now()->subYears(2),
                'contract_end_date' => now()->addYears(3),
                'salary' => 50000.00,
                'is_active' => true,
                'is_class_teacher' => true,
            ]);

            // Create some subjects for the teacher
            $subjects = [
                ['name' => 'Mathematics', 'code' => 'MATH101', 'description' => 'Basic Mathematics'],
                ['name' => 'Advanced Mathematics', 'code' => 'MATH201', 'description' => 'Advanced Mathematics'],
                ['name' => 'Statistics', 'code' => 'STAT101', 'description' => 'Basic Statistics'],
            ];

            foreach ($subjects as $subjectData) {
                Subject::firstOrCreate([
                    'name' => $subjectData['name'],
                    'code' => $subjectData['code'],
                ], [
                    'description' => $subjectData['description'],
                    'teacher_id' => $teacher->id,
                    'level' => 'secondary',
                    'type' => 'core',
                ]);
            }

            // Create some classes and assign the teacher as class teacher
            $classes = [
                ['name' => 'Grade 10A', 'code' => 'G10A'],
                ['name' => 'Grade 11B', 'code' => 'G11B'],
                ['name' => 'Grade 12C', 'code' => 'G12C'],
            ];

            foreach ($classes as $classData) {
                $class = ClassRoom::firstOrCreate([
                    'name' => $classData['name'],
                    'code' => $classData['code'],
                ], [
                    'description' => $classData['name'] . ' Classroom',
                    'capacity' => 30,
                    'room_number' => 'R' . substr($classData['code'], -1),
                    'building' => 'Main Building',
                    'floor' => 1,
                ]);
                
                // Update the class_teacher_id to ensure it's set correctly
                $class->update(['class_teacher_id' => $teacher->id]);
            }

            $this->command->info('Teacher record created successfully with subjects, classes, and students!');
        } else {
            $this->command->info('Teacher user not found or already has a teacher record.');
        }
    }
}