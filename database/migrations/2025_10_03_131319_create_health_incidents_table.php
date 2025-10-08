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
        Schema::create('health_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_number')->unique(); // Auto-generated incident number
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->string('incident_type'); // injury, illness, accident, emergency, etc.
            $table->string('severity'); // minor, moderate, major, critical
            $table->string('location'); // classroom, playground, cafeteria, etc.
            $table->text('description'); // Detailed description of the incident
            $table->text('symptoms')->nullable(); // Symptoms observed
            $table->text('actions_taken')->nullable(); // Actions taken immediately
            $table->text('medical_treatment')->nullable(); // Medical treatment provided
            $table->text('follow_up_required')->nullable(); // Follow-up actions needed
            $table->enum('status', ['reported', 'investigating', 'resolved', 'closed'])->default('reported');
            $table->datetime('incident_date'); // When the incident occurred
            $table->datetime('reported_date'); // When it was reported
            $table->datetime('resolved_date')->nullable(); // When it was resolved
            $table->text('investigation_notes')->nullable(); // Investigation findings
            $table->text('prevention_measures')->nullable(); // Measures to prevent recurrence
            $table->json('witnesses')->nullable(); // Witness information
            $table->json('attachments')->nullable(); // Photos, documents, etc.
            $table->boolean('parent_notified')->default(false);
            $table->boolean('authorities_notified')->default(false);
            $table->text('notes')->nullable(); // Additional notes
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            $table->index(['incident_type', 'severity']);
            $table->index(['status', 'incident_date']);
            $table->index(['student_id', 'incident_date']);
            $table->index(['staff_id', 'incident_date']);
            $table->index(['reported_by', 'incident_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_incidents');
    }
};
