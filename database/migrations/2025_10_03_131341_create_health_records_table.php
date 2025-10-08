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
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->string('record_type'); // medical_checkup, vaccination, allergy, medication, etc.
            $table->string('title'); // Record title
            $table->text('description')->nullable(); // Detailed description
            $table->date('record_date'); // Date of the health record
            $table->date('expiry_date')->nullable(); // Expiry date for vaccinations, etc.
            $table->string('health_provider')->nullable(); // Doctor, clinic, hospital name
            $table->string('provider_contact')->nullable(); // Contact information
            $table->text('medical_notes')->nullable(); // Medical notes from provider
            $table->text('medications')->nullable(); // Current medications
            $table->text('allergies')->nullable(); // Known allergies
            $table->text('chronic_conditions')->nullable(); // Chronic health conditions
            $table->text('emergency_instructions')->nullable(); // Emergency care instructions
            $table->json('vital_signs')->nullable(); // Height, weight, blood pressure, etc.
            $table->json('attachments')->nullable(); // Medical documents, certificates
            $table->boolean('is_confidential')->default(false); // Confidential medical information
            $table->boolean('requires_follow_up')->default(false); // Requires follow-up
            $table->date('follow_up_date')->nullable(); // Next follow-up date
            $table->text('follow_up_notes')->nullable(); // Follow-up instructions
            $table->enum('status', ['active', 'expired', 'inactive'])->default('active');
            $table->text('notes')->nullable(); // Additional notes
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            $table->index(['student_id', 'record_type']);
            $table->index(['record_type', 'status']);
            $table->index(['record_date', 'status']);
            $table->index(['expiry_date']);
            $table->index(['is_confidential']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_records');
    }
};
