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
        Schema::create('live_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('class_rooms')->onDelete('set null');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->integer('duration_minutes');
            $table->integer('total_marks')->default(100);
            $table->integer('passing_marks')->default(50);
            $table->enum('status', ['scheduled', 'active', 'completed', 'cancelled'])->default('scheduled');
            $table->boolean('allow_late_submission')->default(false);
            $table->integer('late_submission_penalty')->default(0); // Percentage
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('show_results_immediately')->default(false);
            $table->json('questions')->nullable(); // Question bank or exam structure
            $table->json('settings')->nullable(); // Additional exam settings
            $table->integer('attempts_allowed')->default(1);
            $table->timestamps();
            
            $table->index(['teacher_id', 'start_time']);
            $table->index(['class_id', 'start_time']);
            $table->index('status');
        });

        // Exam submissions/attempts
        Schema::create('live_exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_exam_id')->constrained('live_exams')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->datetime('started_at');
            $table->datetime('submitted_at')->nullable();
            $table->integer('time_spent_minutes')->nullable();
            $table->json('answers')->nullable(); // {question_id: answer, ...}
            $table->integer('score')->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->enum('status', ['in_progress', 'submitted', 'auto_submitted', 'graded'])->default('in_progress');
            $table->text('teacher_remarks')->nullable();
            $table->timestamps();
            
            $table->unique(['live_exam_id', 'student_id']);
            $table->index(['student_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_exam_attempts');
        Schema::dropIfExists('live_exams');
    }
};
