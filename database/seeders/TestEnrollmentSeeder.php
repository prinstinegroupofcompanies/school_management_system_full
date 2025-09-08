<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TestEnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding class, subjects, user, student, and enrollments...');

        $class = ClassRoom::firstOrCreate(
            ['code' => 'G10'],
            ['name' => 'Grade 10']
        );

        $math = Subject::firstOrCreate(
            ['code' => 'MATH10'],
            [
                'name' => 'Mathematics',
                'class_id' => $class->id,
                'level' => 'junior',
                'is_active' => true,
                'teacher_id' => null,
                'type' => 'core',
                'credits' => 1,
                'hours_per_week' => 5,
                'passing_marks' => 40.00,
                'full_marks' => 100.00,
                'status' => 'active',
            ]
        );

        $eng = Subject::firstOrCreate(
            ['code' => 'ENG10'],
            [
                'name' => 'English',
                'class_id' => $class->id,
                'level' => 'junior',
                'is_active' => true,
                'teacher_id' => null,
                'type' => 'core',
                'credits' => 1,
                'hours_per_week' => 5,
                'passing_marks' => 40.00,
                'full_marks' => 100.00,
                'status' => 'active',
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'student1@example.com'],
            [
                'name' => 'Test Student',
                'password' => Hash::make('password'),
                'user_type' => 'student',
                'status' => 'active',
            ]
        );

        // Create minimal guardian to satisfy NOT NULL foreign key
        $guardianId = null;
        if (Schema::hasTable('guardians')) {
            $columns = Schema::getColumnListing('guardians');
            $data = [
                'created_at' => now(),
                'updated_at' => now(),
            ];
            // Only set optional columns if they exist in current schema
            if (in_array('guardian_id', $columns)) {
                $data['guardian_id'] = 'GDN'.Str::random(6);
            }
            if (in_array('relationship', $columns)) {
                $data['relationship'] = 'guardian';
            }
            if (in_array('status', $columns)) {
                $data['status'] = 'active';
            }
            if (in_array('user_id', $columns)) {
                $guardianUser = User::firstOrCreate(
                    ['email' => 'guardian1@example.com'],
                    [
                        'name' => 'Primary Guardian',
                        'password' => Hash::make('password'),
                        'user_type' => 'parent',
                        'status' => 'active',
                    ]
                );
                $data['user_id'] = $guardianUser->id;
            }
            $guardianId = DB::table('guardians')->insertGetId($data);
        }

        $student = Student::firstOrCreate(
            ['student_id' => 'STU1001'],
            [
                'user_id' => $user->id,
                'class_id' => $class->id,
                'section_id' => null,
                'guardian_id' => $guardianId,
                'admission_no' => 'ADM1001',
                'admission_date' => now()->toDateString(),
                'first_name' => 'Test',
                'last_name' => 'Student',
                'gender' => 'male',
                'date_of_birth' => now()->subYears(15)->toDateString(),
                'level' => 'junior',
                'academic_year' => '2024-2025',
                'status' => 'active',
            ]
        );

        $student->subjects()->sync([$math->id, $eng->id]);

        $this->command->info('Class created: ' . $class->name . ' (ID ' . $class->id . ')');
        $this->command->info('Subjects: ' . $math->name . ', ' . $eng->name);
        $this->command->info('Student created: ' . $user->name . ' (ID ' . $student->id . ')');
        $this->command->info('Enrolled subjects count: ' . $student->subjects()->count());
        $this->command->info('Enrolled subjects: ' . $student->subjects()->pluck('name')->implode(', '));
    }
}


