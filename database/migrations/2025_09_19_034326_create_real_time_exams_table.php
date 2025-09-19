<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Enhanced exam papers table (only create if doesn't exist)
        if (!Schema::hasTable('exam_papers')) {
            Schema::create('exam_papers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('class_rooms')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            
            // Exam configuration
            $table->string('exam_type'); // 'quiz', 'midterm', 'final', 'assignment'
            $table->integer('duration_minutes');
            $table->integer('total_marks');
            $table->integer('passing_marks');
            
            // Scheduling
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->boolean('is_published')->default(false);
            $table->boolean('is_active')->default(true);
            
            // Access control
            $table->json('allowed_students')->nullable(); // Specific student IDs if restricted
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('show_results_immediately')->default(false);
            $table->boolean('allow_review')->default(true);
            
            $table->timestamps();
            
            $table->index(['subject_id', 'class_id', 'is_published']);
            });
        }

        // Exam questions table (only create if doesn't exist)
        if (!Schema::hasTable('exam_questions')) {
            Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_paper_id')->constrained()->onDelete('cascade');
            $table->integer('question_number');
            $table->string('question_type'); // 'multiple_choice', 'true_false', 'short_answer', 'essay', 'fill_blank'
            
            // Question content
            $table->text('question_text');
            $table->json('question_media')->nullable(); // Images, videos, audio files
            $table->text('instructions')->nullable();
            
            // For multiple choice questions
            $table->json('options')->nullable(); // Array of options
            $table->json('correct_answers')->nullable(); // Array of correct answer indices/values
            
            // Scoring
            $table->decimal('points', 5, 2);
            $table->boolean('is_required')->default(true);
            
            // Metadata
            $table->string('difficulty_level')->default('medium'); // easy, medium, hard
            $table->json('learning_objectives')->nullable();
            $table->integer('estimated_time_minutes')->nullable();
            
            $table->timestamps();
            
            $table->index(['exam_paper_id', 'question_number']);
            });
        }

        // Student exam attempts table
        if (!Schema::hasTable('student_exam_attempts')) {
            Schema::create('student_exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_paper_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            
            // Attempt tracking
            $table->integer('attempt_number')->default(1);
            $table->datetime('started_at');
            $table->datetime('submitted_at')->nullable();
            $table->datetime('auto_submit_at'); // Auto-submit time
            
            // Status and progress
            $table->enum('status', ['in_progress', 'submitted', 'auto_submitted', 'abandoned', 'under_review', 'graded'])->default('in_progress');
            $table->integer('time_spent_minutes')->default(0);
            $table->json('answers')->nullable(); // Student's answers
            $table->json('question_order')->nullable(); // Order questions were presented
            
            // Scoring
            $table->decimal('raw_score', 5, 2)->nullable();
            $table->decimal('percentage_score', 5, 2)->nullable();
            $table->string('letter_grade')->nullable();
            $table->boolean('is_passed')->nullable();
            
            // Review and feedback
            $table->text('teacher_feedback')->nullable();
            $table->json('question_feedback')->nullable(); // Per-question feedback
            $table->boolean('reviewed_by_teacher')->default(false);
            $table->datetime('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Security and integrity
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('security_flags')->nullable(); // Tab switches, copy attempts, etc.
            $table->boolean('flagged_for_review')->default(false);
            
            $table->timestamps();
            
            $table->index(['exam_paper_id', 'student_id']);
            $table->index(['status', 'submitted_at']);
            $table->unique(['exam_paper_id', 'student_id', 'attempt_number']);
            });
        }

        // Real-time homework system
        if (!Schema::hasTable('homework_assignments')) {
            Schema::create('homework_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('class_rooms')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            
            // Assignment details
            $table->string('assignment_type'); // 'homework', 'project', 'research', 'presentation'
            $table->json('instructions')->nullable();
            $table->json('attachments')->nullable(); // Files, links, resources
            $table->json('rubric')->nullable(); // Grading rubric
            
            // Scheduling
            $table->datetime('assigned_at');
            $table->datetime('due_date');
            $table->boolean('allow_late_submission')->default(true);
            $table->decimal('late_penalty_percentage', 5, 2)->default(10.0);
            
            // Scoring
            $table->integer('total_points');
            $table->boolean('is_published')->default(false);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            $table->index(['subject_id', 'class_id', 'due_date']);
            });
        }

        // Homework submissions table
        if (!Schema::hasTable('homework_submissions')) {
            Schema::create('homework_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('homework_assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            
            // Submission content
            $table->text('submission_text')->nullable();
            $table->json('attachments')->nullable(); // Student uploaded files
            $table->json('submission_data')->nullable(); // Additional structured data
            
            // Timing
            $table->datetime('submitted_at');
            $table->boolean('is_late')->default(false);
            $table->integer('days_late')->default(0);
            
            // Status and review
            $table->enum('status', ['submitted', 'under_review', 'graded', 'returned', 'resubmitted'])->default('submitted');
            $table->decimal('score', 5, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('letter_grade')->nullable();
            
            // Feedback system
            $table->text('teacher_feedback')->nullable();
            $table->json('detailed_feedback')->nullable(); // Structured feedback per criteria
            $table->json('inline_comments')->nullable(); // Comments on specific parts
            $table->boolean('needs_revision')->default(false);
            $table->text('revision_notes')->nullable();
            
            // Review tracking
            $table->datetime('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('feedback_sent')->default(false);
            $table->datetime('feedback_sent_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['homework_assignment_id', 'student_id']);
            $table->index(['status', 'submitted_at']);
            $table->unique(['homework_assignment_id', 'student_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homework_submissions');
        Schema::dropIfExists('homework_assignments');
        Schema::dropIfExists('student_exam_attempts');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exam_papers');
    }
};