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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('admission_no')->unique();
            $table->string('roll_no')->nullable();
            $table->foreignId('class_id')->constrained('class_rooms')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->string('academic_year');
            $table->date('admission_date');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('date_of_birth');
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->string('religion')->nullable();
            $table->string('caste')->nullable();
            $table->string('mother_tongue')->nullable();
            $table->string('nationality')->default('Liberian');
            $table->foreignId('student_category_id')->nullable()->constrained('student_categories')->onDelete('set null');
            $table->foreignId('student_group_id')->nullable()->constrained('student_groups')->onDelete('set null');
            $table->foreignId('student_house_id')->nullable()->constrained('student_houses')->onDelete('set null');
            $table->decimal('height', 5, 2)->nullable(); // in cm
            $table->decimal('weight', 5, 2)->nullable(); // in kg
            $table->date('as_on_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_transport')->default(false);
            $table->boolean('is_hostel')->default(false);
            $table->string('previous_school')->nullable();
            $table->string('previous_class')->nullable();
            $table->foreignId('admission_query_id')->nullable()->constrained('admission_queries')->onDelete('set null');
            $table->json('sibling_ids')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->text('allergies')->nullable();
            $table->text('special_needs')->nullable();
            $table->foreignId('transport_route_id')->nullable()->constrained('transport_routes')->onDelete('set null');
            $table->foreignId('hostel_room_id')->nullable()->constrained('hostel_rooms')->onDelete('set null');
            $table->foreignId('guardian_id')->constrained('guardians')->onDelete('cascade');
            $table->foreignId('father_id')->nullable()->constrained('guardians')->onDelete('set null');
            $table->foreignId('mother_id')->nullable()->constrained('guardians')->onDelete('set null');
            $table->foreignId('local_guardian_id')->nullable()->constrained('guardians')->onDelete('set null');
            $table->foreignId('fee_structure_id')->nullable()->constrained('fee_structures')->onDelete('set null');
            $table->foreignId('scholarship_id')->nullable()->constrained('scholarships')->onDelete('set null');
            $table->foreignId('discount_id')->nullable()->constrained('discounts')->onDelete('set null');
            $table->decimal('wallet_balance', 10, 2)->default(0.00);
            $table->date('last_payment_date')->nullable();
            $table->enum('payment_status', ['paid', 'partial', 'unpaid', 'overdue'])->default('unpaid');
            $table->boolean('restricted_access')->default(false);
            $table->text('restriction_reason')->nullable();
            $table->date('restriction_date')->nullable();
            $table->timestamp('restriction_expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['admission_no', 'academic_year']);
            $table->index(['class_id', 'section_id', 'academic_year']);
            $table->index(['is_active', 'payment_status']);
            $table->index(['guardian_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
}; 