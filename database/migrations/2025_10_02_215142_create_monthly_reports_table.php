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
        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->unique(); // Auto-generated report number
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('report_month'); // e.g., "2024-10"
            $table->string('report_year'); // e.g., "2024"
            $table->enum('report_type', ['teacher', 'staff', 'department', 'school'])->default('teacher');
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved', 'rejected'])->default('draft');
            
            // Report Period Information
            $table->date('report_period_start');
            $table->date('report_period_end');
            $table->integer('total_working_days')->default(0);
            $table->integer('actual_working_days')->default(0);
            $table->integer('days_absent')->default(0);
            $table->integer('days_late')->default(0);
            $table->integer('days_early_departure')->default(0);
            
            // Performance Metrics Summary
            $table->decimal('overall_performance_score', 5, 2)->nullable(); // 0-100
            $table->decimal('attendance_score', 5, 2)->nullable(); // 0-100
            $table->decimal('punctuality_score', 5, 2)->nullable(); // 0-100
            $table->decimal('task_completion_score', 5, 2)->nullable(); // 0-100
            $table->decimal('quality_score', 5, 2)->nullable(); // 0-100
            $table->decimal('collaboration_score', 5, 2)->nullable(); // 0-100
            
            // Teaching Specific Metrics (for teachers)
            $table->integer('lessons_taught')->default(0);
            $table->integer('lessons_planned')->default(0);
            $table->integer('lesson_plans_submitted')->default(0);
            $table->integer('lesson_plans_approved')->default(0);
            $table->integer('lesson_plans_rejected')->default(0);
            $table->integer('grades_submitted')->default(0);
            $table->integer('grades_approved')->default(0);
            $table->integer('grades_rejected')->default(0);
            $table->integer('students_taught')->default(0);
            $table->integer('subjects_taught')->default(0);
            $table->integer('classes_taught')->default(0);
            
            // Administrative Metrics (for staff)
            $table->integer('tasks_assigned')->default(0);
            $table->integer('tasks_completed')->default(0);
            $table->integer('tasks_overdue')->default(0);
            $table->integer('meetings_attended')->default(0);
            $table->integer('meetings_missed')->default(0);
            $table->integer('projects_managed')->default(0);
            $table->integer('projects_completed')->default(0);
            $table->integer('deadlines_met')->default(0);
            $table->integer('deadlines_missed')->default(0);
            
            // Professional Development
            $table->integer('training_hours')->default(0);
            $table->integer('workshops_attended')->default(0);
            $table->integer('certifications_earned')->default(0);
            $table->text('professional_development_activities')->nullable();
            
            // Achievements and Highlights
            $table->text('key_achievements')->nullable();
            $table->text('challenges_faced')->nullable();
            $table->text('improvements_made')->nullable();
            $table->text('innovations_introduced')->nullable();
            $table->text('student_feedback_summary')->nullable();
            $table->text('peer_feedback_summary')->nullable();
            
            // Goals and Objectives
            $table->text('goals_achieved')->nullable();
            $table->text('goals_not_achieved')->nullable();
            $table->text('next_month_goals')->nullable();
            $table->text('support_needed')->nullable();
            
            // Report Content
            $table->text('executive_summary')->nullable();
            $table->text('detailed_analysis')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('action_items')->nullable();
            $table->text('notes')->nullable();
            
            // Approval and Review
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('review_comments')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // File Attachments
            $table->json('attachments')->nullable(); // Supporting documents
            $table->string('pdf_path')->nullable(); // Generated PDF report
            $table->string('excel_path')->nullable(); // Generated Excel report
            
            // Metadata
            $table->json('metadata')->nullable(); // Additional data storage
            $table->boolean('is_auto_generated')->default(false); // System generated vs manual
            $table->boolean('is_confidential')->default(false); // Confidential report flag
            $table->string('version')->default('1.0'); // Report version
            
            $table->timestamps();
            
            // Indexes
            $table->index(['staff_id', 'report_month', 'report_year']);
            $table->index(['report_type', 'status']);
            $table->index(['report_month', 'report_year']);
            $table->index(['status', 'submitted_at']);
            $table->index(['reviewed_by', 'status']);
            $table->index(['approved_by', 'status']);
            $table->unique(['staff_id', 'report_month', 'report_year', 'report_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_reports');
    }
};
