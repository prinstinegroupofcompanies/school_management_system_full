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
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_number')->unique(); // Auto-generated log number
            $table->foreignId('visitor_id')->constrained('visitors')->onDelete('cascade');
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('cascade'); // If visiting a student
            $table->foreignId('staff_id')->nullable()->constrained('staff')->onDelete('cascade'); // If visiting staff
            $table->foreignId('checked_in_by')->constrained('users')->onDelete('cascade'); // Staff who checked in visitor
            $table->foreignId('checked_out_by')->nullable()->constrained('users')->onDelete('cascade'); // Staff who checked out visitor
            $table->string('purpose'); // Purpose of visit
            $table->text('purpose_details')->nullable(); // Detailed description of visit purpose
            $table->string('destination'); // Where they're going (classroom, office, etc.)
            $table->string('escort_name')->nullable(); // Name of escort if required
            $table->string('escort_phone')->nullable(); // Phone of escort
            $table->enum('status', ['checked_in', 'checked_out', 'overdue', 'cancelled'])->default('checked_in');
            $table->datetime('check_in_time'); // When they checked in
            $table->datetime('expected_check_out_time')->nullable(); // Expected check out time
            $table->datetime('actual_check_out_time')->nullable(); // Actual check out time
            $table->text('check_in_notes')->nullable(); // Notes during check in
            $table->text('check_out_notes')->nullable(); // Notes during check out
            $table->json('attachments')->nullable(); // Photos, documents, etc.
            $table->boolean('vehicle_parked')->default(false); // Whether they have a vehicle parked
            $table->string('vehicle_plate')->nullable(); // Vehicle license plate
            $table->string('vehicle_make')->nullable(); // Vehicle make
            $table->string('vehicle_model')->nullable(); // Vehicle model
            $table->string('vehicle_color')->nullable(); // Vehicle color
            $table->text('special_instructions')->nullable(); // Special instructions for this visit
            $table->boolean('emergency_contact_notified')->default(false); // Whether emergency contact was notified
            $table->text('emergency_contact_notes')->nullable(); // Notes about emergency contact notification
            $table->json('metadata')->nullable(); // Additional log data
            $table->timestamps();
            
            $table->index(['visitor_id', 'status']);
            $table->index(['student_id', 'status']);
            $table->index(['staff_id', 'status']);
            $table->index(['checked_in_by', 'check_in_time']);
            $table->index(['status', 'check_in_time']);
            $table->index(['check_in_time', 'check_out_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};
