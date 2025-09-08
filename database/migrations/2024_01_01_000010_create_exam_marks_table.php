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
        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('exam_schedule_id')->constrained('exam_schedules')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('class_rooms')->onDelete('cascade');
            $table->string('academic_year');
            $table->decimal('marks_obtained', 5, 2);
            $table->decimal('total_marks', 5, 2);
            $table->decimal('percentage', 5, 2);
            $table->string('grade')->nullable();
            $table->string('grade_point')->nullable();
            $table->text('remarks')->nullable();
            $table->text('teacher_comments')->nullable();
            $table->text('parent_comments')->nullable();
            $table->boolean('is_absent')->default(false);
            $table->boolean('is_late')->default(false);
            $table->time('submission_time')->nullable();
            $table->enum('status', ['draft', 'published', 'final'])->default('draft');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->timestamp('marked_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'exam_schedule_id', 'subject_id']);
            $table->index(['student_id', 'academic_year']);
            $table->index(['exam_schedule_id', 'subject_id']);
            $table->index(['class_id', 'academic_year']);
            $table->index(['status', 'marked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_marks');
    }
}; 