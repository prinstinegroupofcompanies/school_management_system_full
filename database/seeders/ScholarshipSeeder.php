<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Scholarship;
use App\Models\Student;
use App\Models\ScholarshipApplication;
use App\Models\ClassRoom;
use App\Models\Subject;

class ScholarshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample scholarships
        $scholarships = [
            [
                'name' => 'Academic Excellence Scholarship',
                'code' => 'SCH-ACE-2025',
                'description' => 'Awarded to students with outstanding academic performance and high GPA',
                'amount' => 5000.00,
                'type' => 'merit',
                'currency' => 'USD',
                'academic_year' => '2024-2025',
                'application_deadline' => '2025-12-31',
                'max_recipients' => 10,
                'current_recipients' => 0,
                'is_active' => true,
                'is_merit_based' => true,
                'is_need_based' => false,
                'is_sports_based' => false,
                'is_community_based' => false,
                'is_academic_based' => true,
                'created_by' => null,
                'notes' => 'For students with GPA above 3.5'
            ],
            [
                'name' => 'Need-Based Financial Aid',
                'code' => 'SCH-NBF-2025',
                'description' => 'Financial assistance for students with demonstrated financial need',
                'amount' => 3000.00,
                'type' => 'need',
                'currency' => 'USD',
                'academic_year' => '2024-2025',
                'application_deadline' => '2025-11-30',
                'max_recipients' => 20,
                'current_recipients' => 0,
                'is_active' => true,
                'is_merit_based' => false,
                'is_need_based' => true,
                'is_sports_based' => false,
                'is_community_based' => false,
                'is_academic_based' => false,
                'created_by' => null,
                'notes' => 'For students from low-income families'
            ],
            [
                'name' => 'Sports Achievement Scholarship',
                'code' => 'SCH-SPT-2025',
                'description' => 'Awarded to students who excel in sports and athletics',
                'amount' => 2500.00,
                'type' => 'sports',
                'currency' => 'USD',
                'academic_year' => '2024-2025',
                'application_deadline' => '2025-10-15',
                'max_recipients' => 8,
                'current_recipients' => 0,
                'is_active' => true,
                'is_merit_based' => false,
                'is_need_based' => false,
                'is_sports_based' => true,
                'is_community_based' => false,
                'is_academic_based' => false,
                'created_by' => null,
                'notes' => 'For students participating in school sports'
            ],
            [
                'name' => 'Community Service Scholarship',
                'code' => 'SCH-CMS-2025',
                'description' => 'For students who have made significant contributions to their community',
                'amount' => 2000.00,
                'type' => 'community',
                'currency' => 'USD',
                'academic_year' => '2024-2025',
                'application_deadline' => '2025-09-30',
                'max_recipients' => 12,
                'current_recipients' => 0,
                'is_active' => true,
                'is_merit_based' => false,
                'is_need_based' => false,
                'is_sports_based' => false,
                'is_community_based' => true,
                'is_academic_based' => false,
                'created_by' => null,
                'notes' => 'For students with community service hours'
            ],
            [
                'name' => 'STEM Excellence Scholarship',
                'code' => 'SCH-STEM-2025',
                'description' => 'For students excelling in Science, Technology, Engineering, and Mathematics',
                'amount' => 4000.00,
                'type' => 'merit',
                'currency' => 'USD',
                'academic_year' => '2024-2025',
                'application_deadline' => '2025-12-15',
                'max_recipients' => 15,
                'current_recipients' => 0,
                'is_active' => true,
                'is_merit_based' => true,
                'is_need_based' => false,
                'is_sports_based' => false,
                'is_community_based' => false,
                'is_academic_based' => true,
                'created_by' => null,
                'notes' => 'For students with high performance in STEM subjects'
            ]
        ];

        foreach ($scholarships as $scholarshipData) {
            Scholarship::create($scholarshipData);
        }

        // Award some scholarships to existing students
        // $this->awardScholarships(); // Commented out for now due to foreign key constraints
    }

    private function awardScholarships()
    {
        // Get some students to award scholarships to
        $students = Student::where('status', 'active')->take(5)->get();
        $scholarships = Scholarship::where('is_active', true)->get();

        if ($students->count() > 0 && $scholarships->count() > 0) {
            // Award Academic Excellence Scholarship to first student
            $academicScholarship = $scholarships->where('type', 'merit')->first();
            if ($academicScholarship && $students->count() > 0) {
                $student = $students->first();
                
                // Create scholarship application
                ScholarshipApplication::create([
                    'scholarship_id' => $academicScholarship->id,
                    'student_id' => $student->id,
                    'application_number' => 'APP-' . $academicScholarship->id . '-' . $student->id . '-' . time(),
                    'application_date' => now()->subDays(30),
                    'status' => 'approved',
                    'submitted_at' => now()->subDays(30),
                    'approved_at' => now()->subDays(25),
                    'approved_by' => 1,
                    'final_decision' => 'approved',
                    'is_active' => true
                ]);

                // Update scholarship recipient count
                $academicScholarship->increment('current_recipients');
            }

            // Award Need-Based Financial Aid to second student
            $needScholarship = $scholarships->where('type', 'need')->first();
            if ($needScholarship && $students->count() > 1) {
                $student = $students->skip(1)->first();
                
                // Create scholarship application
                ScholarshipApplication::create([
                    'scholarship_id' => $needScholarship->id,
                    'student_id' => $student->id,
                    'application_number' => 'APP-' . $needScholarship->id . '-' . $student->id . '-' . time(),
                    'application_date' => now()->subDays(20),
                    'status' => 'approved',
                    'submitted_at' => now()->subDays(20),
                    'approved_at' => now()->subDays(15),
                    'approved_by' => 1,
                    'final_decision' => 'approved',
                    'is_active' => true
                ]);

                // Update scholarship recipient count
                $needScholarship->increment('current_recipients');
            }

            // Award Sports Achievement Scholarship to third student
            $sportsScholarship = $scholarships->where('type', 'sports')->first();
            if ($sportsScholarship && $students->count() > 2) {
                $student = $students->skip(2)->first();
                
                // Create scholarship application
                ScholarshipApplication::create([
                    'scholarship_id' => $sportsScholarship->id,
                    'student_id' => $student->id,
                    'application_number' => 'APP-' . $sportsScholarship->id . '-' . $student->id . '-' . time(),
                    'application_date' => now()->subDays(15),
                    'status' => 'approved',
                    'submitted_at' => now()->subDays(15),
                    'approved_at' => now()->subDays(10),
                    'approved_by' => 1,
                    'final_decision' => 'approved',
                    'is_active' => true
                ]);

                // Update scholarship recipient count
                $sportsScholarship->increment('current_recipients');
            }
        }
    }
}
