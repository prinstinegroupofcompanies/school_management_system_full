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
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->text('question_text');
            $table->text('question_image')->nullable();
            $table->enum('question_type', ['single_choice', 'multiple_choice', 'true_false', 'fill_blank', 'essay', 'matching'])->default('single_choice');
            $table->unsignedBigInteger('subject_id')->constrained();
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('teacher_id')->constrained();
            $table->string('academic_year');
            $table->enum('difficulty_level', ['easy', 'medium', 'hard', 'expert'])->default('medium');
            $table->decimal('marks', 5, 2)->default(1.00);
            $table->integer('time_limit_seconds')->nullable();
            $table->text('options')->nullable(); // JSON array for multiple choice questions
            $table->text('correct_answer')->nullable(); // JSON array for multiple correct answers
            $table->text('explanation')->nullable();
            $table->text('hints')->nullable();
            $table->text('tags')->nullable(); // JSON array of tags
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->decimal('success_rate', 5, 2)->default(0.00); // Percentage of correct answers
            $table->timestamps();
            
            $table->index(['question_type', 'subject_id']);
            $table->index(['class_id', 'difficulty_level']);
            $table->index(['teacher_id', 'status']);
            $table->index(['academic_year', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_banks');
    }
}; 