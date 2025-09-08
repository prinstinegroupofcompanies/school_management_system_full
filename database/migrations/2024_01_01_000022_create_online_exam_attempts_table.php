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
        Schema::create('online_exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger->constrained();
            $table->unsignedBigInteger->constrained();
            $table->string('attempt_number')->default('1');
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('time_expired_at')->nullable();
            $table->integer('time_taken_minutes')->nullable();
            $table->integer('questions_attempted')->default(0);
            $table->integer('questions_answered')->default(0);
            $table->integer('questions_skipped')->default(0);
            $table->decimal('marks_obtained', 5, 2)->default(0.00);
            $table->decimal('total_marks', 5, 2);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->string('grade')->nullable();
            $table->string('grade_point')->nullable();
            $table->enum('status', ['in_progress', 'submitted', 'time_expired', 'abandoned'])->default('in_progress');
            $table->boolean('is_passed')->default(false);
            $table->text('student_answers')->nullable(); // JSON array of student answers
            $table->text('correct_answers')->nullable(); // JSON array of correct answers
            $table->text('feedback')->nullable();
            $table->text('teacher_comments')->nullable();
            $table->boolean('is_reviewed')->default(false);
            $table->unsignedBigInteger->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'online_exam_id', 'attempt_number']);
            $table->index(['student_id', 'online_exam_id']);
            $table->index(['started_at', 'submitted_at']);
            $table->index(['status', 'is_passed']);
            $table->index(['marks_obtained', 'total_marks']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_exam_attempts');
    }
}; 