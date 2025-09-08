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
        Schema::create('question_bank', function (Blueprint $table) {
            $table->id();
            $table->text('question_text');
            $table->string('question_image')->nullable();
            $table->enum('question_type', ['multiple_choice', 'true_false', 'essay', 'fill_blank', 'matching'])->default('multiple_choice');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('teacher_id');
            $table->string('academic_year');
            $table->enum('difficulty_level', ['easy', 'medium', 'hard', 'expert'])->default('medium');
            $table->decimal('marks', 5, 2)->default(1.00);
            $table->integer('time_limit_seconds')->nullable();
            $table->json('options')->nullable();
            $table->string('correct_answer')->nullable();
            $table->text('explanation')->nullable();
            $table->text('hints')->nullable();
            $table->json('tags')->nullable();
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->integer('usage_count')->default(0);
            $table->decimal('success_rate', 5, 2)->default(0.00);
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->foreign('class_id')->references('id')->on('classrooms');
            $table->foreign('teacher_id')->references('id')->on('users');

            // Indexes
            $table->index(['question_type', 'status']);
            $table->index(['subject_id', 'status']);
            $table->index(['class_id', 'status']);
            $table->index(['teacher_id', 'status']);
            $table->index(['academic_year', 'status']);
            $table->index(['difficulty_level', 'status']);
            $table->index(['marks', 'status']);
            $table->index(['time_limit_seconds', 'status']);
            $table->index(['usage_count', 'status']);
            $table->index(['success_rate', 'status']);
            $table->index(['tags', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_bank');
    }
};
