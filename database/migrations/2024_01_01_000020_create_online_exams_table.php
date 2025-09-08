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
        Schema::create('online_exams', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g., "Mathematics Mid-Term Online Exam"
            $table->text('description')->nullable();
            $table->unsignedBigInteger;
            $table->unsignedBigInteger->constrained();
            $table->unsignedBigInteger->constrained();
            $table->string('academic_year');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes');
            $table->integer('total_questions');
            $table->decimal('total_marks', 5, 2);
            $table->decimal('passing_marks', 5, 2);
            $table->enum('question_type', ['single_choice', 'multiple_choice', 'true_false', 'fill_blank', 'essay', 'mixed'])->default('mixed');
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_options')->default(false);
            $table->boolean('show_results_immediately')->default(false);
            $table->boolean('allow_review')->default(true);
            $table->boolean('allow_retake')->default(false);
            $table->integer('max_attempts')->default(1);
            $table->text('instructions')->nullable();
            $table->text('important_notes')->nullable();
            $table->enum('status', ['draft', 'published', 'ongoing', 'completed', 'cancelled'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['title', 'academic_year']);
            $table->index(['class_id', 'subject_id']);
            $table->index(['exam_date', 'start_time']);
            $table->index(['status', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_exams');
    }
}; 