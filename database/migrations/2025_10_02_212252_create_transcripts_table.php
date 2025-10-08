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
        Schema::create('transcripts', function (Blueprint $table) {
            $table->id();
            $table->string('transcript_number')->unique(); // Auto-generated transcript number
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('class_rooms')->onDelete('cascade');
            $table->string('academic_year'); // e.g., "2024-2025"
            $table->string('semester')->nullable(); // "First Semester", "Second Semester", "Annual"
            $table->enum('type', ['official', 'unofficial', 'interim'])->default('official');
            $table->enum('status', ['draft', 'generated', 'approved', 'issued', 'archived'])->default('draft');
            
            // Academic Information
            $table->decimal('gpa', 4, 2)->nullable(); // Overall GPA
            $table->decimal('cgpa', 4, 2)->nullable(); // Cumulative GPA
            $table->integer('total_credits')->default(0);
            $table->integer('earned_credits')->default(0);
            $table->integer('total_subjects')->default(0);
            $table->integer('passed_subjects')->default(0);
            $table->integer('failed_subjects')->default(0);
            
            // Ranking Information
            $table->integer('class_rank')->nullable(); // Rank within class
            $table->integer('grade_rank')->nullable(); // Rank within grade level
            $table->integer('total_students_in_class')->nullable();
            $table->integer('total_students_in_grade')->nullable();
            $table->decimal('percentile', 5, 2)->nullable(); // Percentile ranking
            
            // Grade Summary
            $table->integer('a_grades')->default(0);
            $table->integer('b_grades')->default(0);
            $table->integer('c_grades')->default(0);
            $table->integer('d_grades')->default(0);
            $table->integer('f_grades')->default(0);
            $table->integer('incomplete_grades')->default(0);
            
            // Academic Standing
            $table->enum('academic_standing', ['excellent', 'good', 'satisfactory', 'needs_improvement', 'unsatisfactory'])->nullable();
            $table->text('academic_honors')->nullable(); // Dean's List, Honor Roll, etc.
            $table->text('disciplinary_actions')->nullable(); // Any disciplinary issues
            
            // Attendance Information
            $table->integer('total_days')->nullable();
            $table->integer('days_present')->nullable();
            $table->integer('days_absent')->nullable();
            $table->decimal('attendance_percentage', 5, 2)->nullable();
            
            // Dates and Timestamps
            $table->date('generation_date');
            $table->date('issue_date')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('issued_at')->nullable();
            
            // Approval and Authorization
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('approver_signature')->nullable(); // E-signature
            $table->string('registrar_signature')->nullable(); // E-signature
            
            // File and Document Information
            $table->string('pdf_path')->nullable(); // Path to generated PDF
            $table->string('excel_path')->nullable(); // Path to generated Excel file
            $table->string('watermark')->nullable(); // Watermark text
            $table->boolean('is_sealed')->default(false); // Official seal applied
            $table->string('seal_path')->nullable(); // Path to seal image
            
            // Additional Information
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable(); // For admin use only
            $table->json('metadata')->nullable(); // Additional data storage
            $table->json('grade_data')->nullable(); // Detailed grade information
            
            $table->timestamps();
            
            // Indexes
            $table->index(['student_id', 'academic_year']);
            $table->index(['class_id', 'academic_year']);
            $table->index(['transcript_number']);
            $table->index(['status', 'generation_date']);
            $table->index(['type', 'status']);
            $table->index(['gpa', 'class_rank']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transcripts');
    }
};
