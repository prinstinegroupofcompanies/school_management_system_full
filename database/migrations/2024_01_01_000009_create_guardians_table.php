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
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('guardian_id')->unique();
            $table->enum('relationship', ['father', 'mother', 'guardian', 'grandfather', 'grandmother', 'uncle', 'aunt', 'sibling', 'other'])->default('guardian');
            $table->string('occupation')->nullable();
            $table->string('employer')->nullable();
            $table->string('work_address')->nullable();
            $table->string('work_phone')->nullable();
            $table->string('work_email')->nullable();
            $table->decimal('monthly_income', 12, 2)->nullable();
            $table->string('income_currency', 3)->default('LRD');
            $table->string('education_level')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('spouse_phone')->nullable();
            $table->string('spouse_email')->nullable();
            $table->string('spouse_occupation')->nullable();
            $table->string('spouse_employer')->nullable();
            $table->string('spouse_work_address')->nullable();
            $table->string('spouse_work_phone')->nullable();
            $table->string('spouse_work_email')->nullable();
            $table->decimal('spouse_monthly_income', 12, 2)->nullable();
            $table->string('spouse_income_currency', 3)->default('LRD');
            $table->string('spouse_education_level')->nullable();
            $table->boolean('is_primary_guardian')->default(false);
            $table->boolean('is_emergency_contact')->default(false);
            $table->boolean('is_financial_guardian')->default(false);
            $table->boolean('is_academic_guardian')->default(false);
            $table->boolean('is_health_guardian')->default(false);
            $table->boolean('is_transport_guardian')->default(false);
            $table->boolean('is_hostel_guardian')->default(false);
            $table->json('permissions')->nullable();
            $table->json('preferences')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users');

            // Indexes
            $table->index(['guardian_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['relationship', 'status']);
            $table->index(['is_primary_guardian', 'status']);
            $table->index(['is_emergency_contact', 'status']);
            $table->index(['is_financial_guardian', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
