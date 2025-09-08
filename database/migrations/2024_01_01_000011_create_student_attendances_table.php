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
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('class_rooms')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->string('academic_year');
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'late', 'half_day', 'sick_leave', 'other_leave'])->default('present');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->text('remarks')->nullable();
            $table->text('teacher_remarks')->nullable();
            $table->text('parent_remarks')->nullable();
            $table->boolean('is_excused')->default(false);
            $table->string('excuse_reason')->nullable();
            $table->string('excuse_document')->nullable();
            $table->foreignId('marked_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('marked_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'attendance_date', 'subject_id']);
            $table->index(['student_id', 'academic_year']);
            $table->index(['class_id', 'section_id', 'attendance_date']);
            $table->index(['attendance_date', 'status']);
            $table->index(['marked_by', 'marked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
}; 