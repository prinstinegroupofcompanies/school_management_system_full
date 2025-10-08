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
        Schema::create('admission_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique(); // Auto-generated application number
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('nationality')->default('Liberian');
            $table->string('place_of_birth')->nullable();
            $table->string('religion')->nullable();
            
            // Contact Information
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('postal_code')->nullable();
            
            // Academic Information
            $table->foreignId('applying_class_id')->constrained('class_rooms')->onDelete('cascade');
            $table->string('previous_school')->nullable();
            $table->text('previous_school_address')->nullable();
            $table->string('previous_class')->nullable();
            $table->decimal('previous_gpa', 3, 2)->nullable();
            $table->text('academic_achievements')->nullable();
            
            // Parent/Guardian Information
            $table->string('parent_first_name');
            $table->string('parent_last_name');
            $table->string('parent_middle_name')->nullable();
            $table->string('parent_phone');
            $table->string('parent_email')->nullable();
            $table->text('parent_address');
            $table->string('parent_occupation')->nullable();
            $table->string('parent_employer')->nullable();
            $table->string('relationship_to_student'); // father, mother, guardian, etc.
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            
            // Documents and Attachments
            $table->json('required_documents')->nullable(); // List of required documents
            $table->json('submitted_documents')->nullable(); // List of submitted documents
            $table->json('document_paths')->nullable(); // File paths for uploaded documents
            
            // Application Status and Process
            $table->enum('status', ['draft', 'submitted', 'under_review', 'first_level_approved', 'second_level_approved', 'rejected', 'accepted', 'enrolled'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable(); // For admin use only
            
            // Application Dates
            $table->date('application_date');
            $table->date('review_deadline')->nullable();
            $table->date('decision_date')->nullable();
            $table->date('enrollment_deadline')->nullable();
            
            // Entrance Exam Information
            $table->boolean('requires_entrance_exam')->default(true);
            $table->date('entrance_exam_date')->nullable();
            $table->time('entrance_exam_time')->nullable();
            $table->string('entrance_exam_venue')->nullable();
            $table->decimal('entrance_exam_score', 5, 2)->nullable();
            $table->text('entrance_exam_notes')->nullable();
            
            // Interview Information
            $table->boolean('requires_interview')->default(false);
            $table->date('interview_date')->nullable();
            $table->time('interview_time')->nullable();
            $table->string('interview_venue')->nullable();
            $table->text('interview_notes')->nullable();
            $table->decimal('interview_score', 5, 2)->nullable();
            
            // Financial Information
            $table->decimal('application_fee', 8, 2)->nullable();
            $table->boolean('application_fee_paid')->default(false);
            $table->date('application_fee_payment_date')->nullable();
            $table->text('payment_reference')->nullable();
            
            // Additional Information
            $table->text('special_needs')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medications')->nullable();
            $table->text('extracurricular_activities')->nullable();
            $table->text('hobbies')->nullable();
            $table->text('why_choose_school')->nullable();
            
            // System Fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('enrolled_at')->nullable();
            
            $table->json('metadata')->nullable(); // Additional data storage
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'application_date']);
            $table->index(['applying_class_id', 'status']);
            $table->index(['parent_phone', 'status']);
            $table->index(['application_number']);
            $table->index(['entrance_exam_date']);
            $table->index(['interview_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_applications');
    }
};
