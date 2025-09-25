<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\Teacher;
use App\Models\User;
use App\Events\GradeSubmitted;
use App\Events\GradeApproved;
use App\Events\GradeStatusChanged;

class TestRealtime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:realtime {action=info : Action to perform (info, create-grade, approve-grade)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test real-time functionality for grades system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'info':
                $this->showInfo();
                break;
            case 'create-grade':
                $this->createTestGrade();
                break;
            case 'approve-grade':
                $this->approveTestGrade();
                break;
            default:
                $this->error('Invalid action. Use: info, create-grade, or approve-grade');
        }
    }

    private function showInfo()
    {
        $this->info('Real-time Grades System Test');
        $this->line('');
        
        $this->line('Available test actions:');
        $this->line('• php artisan test:realtime info - Show this information');
        $this->line('• php artisan test:realtime create-grade - Create a test grade and fire submission event');
        $this->line('• php artisan test:realtime approve-grade - Approve a test grade and fire approval event');
        
        $this->line('');
        $this->line('Real-time features implemented:');
        $this->line('✅ Grade submission events');
        $this->line('✅ Grade approval events');
        $this->line('✅ Grade status change events');
        $this->line('✅ Real-time notifications');
        $this->line('✅ Live UI updates');
        $this->line('✅ Polling-based updates (30-second intervals)');
        
        $this->line('');
        $this->line('API Endpoints:');
        $this->line('• GET /api/realtime/check-updates - Check for new updates');
        $this->line('• POST /api/realtime/mark-read - Mark notifications as read');
        $this->line('• GET /api/realtime/unread-count - Get unread notification count');
    }

    private function createTestGrade()
    {
        $this->info('Creating test grade...');

        // Find or create test data
        $teacher = Teacher::with('user')->first();
        $student = Student::with('user')->first();
        $subject = Subject::first();
        $class = ClassRoom::first();

        if (!$teacher || !$student || !$subject || !$class) {
            $this->error('Required test data not found. Please ensure you have teachers, students, subjects, and classes in the database.');
            return;
        }

        // Check if grade already exists
        $existingGrade = Grade::where('student_id', $student->id)
                             ->where('class_id', $class->id)
                             ->where('subject_id', $subject->id)
                             ->where('academic_year', date('Y'))
                             ->first();

        if ($existingGrade) {
            $this->info("Using existing grade ID: {$existingGrade->id}");
            $grade = $existingGrade;
        } else {
            // Create a test grade
            $grade = Grade::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'academic_year' => date('Y'),
            'semester' => 1,
            'sem1_p1' => rand(70, 95),
            'sem1_p2' => rand(70, 95),
            'sem1_p3' => rand(70, 95),
            'sem1_exam' => rand(70, 95),
            'sem2_p4' => rand(70, 95),
            'sem2_p5' => rand(70, 95),
            'sem2_p6' => rand(70, 95),
            'sem2_exam' => rand(70, 95),
            'status' => 'pending',
            ]);

            $grade->calculateSemesterAverages();
            $grade->save();
        }

        // Fire the grade submitted event
        event(new GradeSubmitted($grade));

        $this->info("✅ Test grade created successfully!");
        $this->line("Grade ID: {$grade->id}");
        $this->line("Student: {$student->user->name}");
        $this->line("Subject: {$subject->name}");
        $this->line("Teacher: {$teacher->user->name}");
        $this->line("Status: {$grade->status}");
        $this->line("Year Average: {$grade->year_avg}");

        $this->line('');
        $this->info('🎉 GradeSubmitted event fired! Check the admin dashboard for real-time updates.');
    }

    private function approveTestGrade()
    {
        $this->info('Approving test grade...');

        // Find a pending grade
        $grade = Grade::where('status', 'pending')->with(['student.user', 'subject', 'teacher.user'])->first();

        if (!$grade) {
            $this->error('No pending grades found. Create a test grade first with: php artisan test:realtime create-grade');
            return;
        }

        $previousStatus = $grade->status;

        // Approve the grade
        $grade->update([
            'status' => 'approved',
            'approved_by' => User::where('user_type', 'admin')->first()->id ?? 1,
            'approved_at' => now(),
        ]);

        // Fire events
        event(new GradeStatusChanged($grade, $previousStatus, 'approved'));
        event(new GradeApproved($grade));

        $this->info("✅ Test grade approved successfully!");
        $this->line("Grade ID: {$grade->id}");
        $this->line("Student: {$grade->student->user->name}");
        $this->line("Subject: {$grade->subject->name}");
        $this->line("Status: {$grade->status}");

        $this->line('');
        $this->info('🎉 GradeApproved and GradeStatusChanged events fired!');
        $this->info('Check the teacher and student dashboards for real-time updates.');
    }
}