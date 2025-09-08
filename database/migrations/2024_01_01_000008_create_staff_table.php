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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('employee_id')->unique();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('designation_id')->nullable();
            $table->string('qualification')->nullable();
            $table->string('specialization')->nullable();
            $table->date('joining_date')->nullable();
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'temporary'])->default('full_time');
            $table->enum('employment_status', ['active', 'probation', 'suspended', 'terminated', 'resigned', 'retired'])->default('active');
            $table->decimal('basic_salary', 10, 2)->nullable();
            $table->string('salary_currency', 3)->default('LRD');
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('tax_identification_number')->nullable();
            $table->string('social_security_number')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_address')->nullable();
            $table->json('documents')->nullable();
            $table->json('certifications')->nullable();
            $table->json('skills')->nullable();
            $table->text('bio')->nullable();
            $table->text('achievements')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('designation_id')->references('id')->on('designations');

            // Indexes
            $table->index(['employee_id', 'employment_status']);
            $table->index(['user_id', 'employment_status']);
            $table->index(['department_id', 'employment_status']);
            $table->index(['designation_id', 'employment_status']);
            $table->index(['employment_type', 'employment_status']);
            $table->index(['joining_date', 'employment_status']);
            $table->index(['created_at', 'employment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
