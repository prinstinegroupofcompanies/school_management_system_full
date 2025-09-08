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
        Schema::create('scholarship_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scholarship_id');
            $table->unsignedBigInteger('student_id');
            $table->string('application_number')->unique();
            $table->date('application_date');
            $table->enum('status', ['pending', 'under_review', 'documents_pending', 'documents_verified', 'interview_scheduled', 'interview_completed', 'approved', 'rejected', 'waitlisted', 'withdrawn'])->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->json('documents_submitted')->nullable();
            $table->boolean('documents_verified')->default(false);
            $table->timestamp('documents_verified_at')->nullable();
            $table->unsignedBigInteger('documents_verified_by')->nullable();
            $table->boolean('interview_scheduled')->default(false);
            $table->date('interview_date')->nullable();
            $table->time('interview_time')->nullable();
            $table->string('interview_location')->nullable();
            $table->text('interview_notes')->nullable();
            $table->decimal('interview_score', 3, 2)->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->enum('final_decision', ['approved', 'rejected', 'waitlisted', 'pending'])->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('scholarship_id')->references('id')->on('scholarships');
            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('reviewed_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
            $table->foreign('rejected_by')->references('id')->on('users');
            $table->foreign('documents_verified_by')->references('id')->on('users');

            // Indexes
            $table->index(['application_number', 'status']);
            $table->index(['scholarship_id', 'status']);
            $table->index(['student_id', 'status']);
            $table->index(['status', 'application_date']);
            $table->index(['submitted_at', 'status']);
            $table->index(['reviewed_at', 'status']);
            $table->index(['approved_at', 'status']);
            $table->index(['rejected_at', 'status']);
            $table->index(['documents_verified', 'status']);
            $table->index(['interview_scheduled', 'status']);
            $table->index(['interview_date', 'status']);
            $table->index(['interview_score', 'status']);
            $table->index(['final_score', 'status']);
            $table->index(['final_decision', 'status']);
            $table->index(['is_active', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarship_applications');
    }
};
