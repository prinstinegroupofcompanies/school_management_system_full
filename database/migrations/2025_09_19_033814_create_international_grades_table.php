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
        Schema::create('international_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('class_rooms')->onDelete('cascade');
            
            // Assessment details
            $table->string('assessment_type'); // 'assignment', 'quiz', 'midterm', 'final', 'project', 'participation'
            $table->string('assessment_title');
            $table->text('assessment_description')->nullable();
            $table->date('assessment_date');
            $table->string('academic_year');
            $table->string('semester'); // 'fall', 'spring', 'summer'
            
            // International Grading Standards
            $table->decimal('raw_score', 5, 2); // Actual points earned
            $table->decimal('max_score', 5, 2); // Maximum possible points
            $table->decimal('percentage', 5, 2); // Calculated percentage
            
            // Multiple international grading scales
            $table->string('letter_grade')->nullable(); // A+, A, A-, B+, B, B-, C+, C, C-, D+, D, F
            $table->decimal('gpa_points', 3, 2)->nullable(); // 4.0 scale
            $table->integer('ib_grade')->nullable(); // International Baccalaureate (1-7 scale)
            $table->string('cambridge_grade')->nullable(); // A*, A, B, C, D, E, U
            $table->decimal('percentage_grade', 5, 2)->nullable(); // 0-100 scale
            
            // Competency-based assessment
            $table->enum('proficiency_level', ['exceeds', 'meets', 'approaching', 'below'])->nullable();
            $table->json('learning_standards_met')->nullable(); // Array of standards/objectives achieved
            
            // Feedback and comments
            $table->text('teacher_comments')->nullable();
            $table->text('feedback')->nullable();
            $table->json('rubric_scores')->nullable(); // Detailed rubric breakdown
            
            // Workflow and approval
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'published'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            
            // Student and parent access
            $table->boolean('visible_to_student')->default(false);
            $table->boolean('visible_to_parent')->default(false);
            $table->timestamp('published_at')->nullable();
            
            // Weight and calculation
            $table->decimal('weight', 5, 2)->default(1.0); // Weight for final grade calculation
            $table->boolean('counts_toward_final')->default(true);
            $table->boolean('is_extra_credit')->default(false);
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['student_id', 'subject_id', 'academic_year']);
            $table->index(['teacher_id', 'assessment_type']);
            $table->index(['status', 'submitted_at']);
            $table->index(['class_id', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('international_grades');
    }
};