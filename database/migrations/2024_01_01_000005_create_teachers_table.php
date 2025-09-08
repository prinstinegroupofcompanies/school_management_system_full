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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('employee_id')->unique();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('designation_id')->constrained('designations')->onDelete('cascade');
            $table->string('qualification');
            $table->string('specialization')->nullable();
            $table->integer('experience_years')->default(0);
            $table->date('joining_date');
            $table->date('contract_end_date')->nullable();
            $table->decimal('salary', 10, 2);
            $table->string('bank_name')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_class_teacher')->default(false);
            $table->boolean('restricted_mode')->default(false);
            $table->text('restriction_reason')->nullable();
            $table->date('restriction_date')->nullable();
            $table->timestamp('restriction_expires_at')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('signature')->nullable();
            $table->text('bio')->nullable();
            $table->json('social_media_links')->nullable();
            $table->text('awards_achievements')->nullable();
            $table->text('certifications')->nullable();
            $table->json('languages_known')->nullable();
            $table->json('interests_hobbies')->nullable();
            $table->timestamps();
            
            $table->index(['employee_id', 'is_active']);
            $table->index(['department_id', 'designation_id']);
            $table->index(['is_class_teacher', 'is_active']);
            $table->index(['joining_date', 'contract_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
}; 