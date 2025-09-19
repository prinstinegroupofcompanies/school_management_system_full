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
        Schema::create('student_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Who performed the action
            
            // Activity classification
            $table->string('activity_type'); // 'enrollment', 'grade_update', 'attendance', 'payment', 'promotion', 'discipline', 'achievement'
            $table->string('activity_category'); // 'academic', 'financial', 'administrative', 'disciplinary', 'extracurricular'
            $table->string('activity_title');
            $table->text('activity_description');
            
            // Related entities
            $table->string('related_model')->nullable(); // Model class name (Subject, Grade, etc.)
            $table->unsignedBigInteger('related_id')->nullable(); // ID of related model
            $table->json('related_data')->nullable(); // Additional context data
            
            // Academic context
            $table->string('academic_year')->nullable();
            $table->string('semester')->nullable();
            $table->foreignId('class_id')->nullable()->constrained('class_rooms')->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            
            // Change tracking
            $table->json('old_values')->nullable(); // Previous state
            $table->json('new_values')->nullable(); // New state
            $table->json('metadata')->nullable(); // Additional context
            
            // Impact assessment
            $table->enum('impact_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->boolean('requires_parent_notification')->default(false);
            $table->boolean('requires_admin_review')->default(false);
            
            // Status and visibility
            $table->enum('status', ['active', 'archived', 'deleted'])->default('active');
            $table->boolean('is_visible_to_student')->default(false);
            $table->boolean('is_visible_to_parent')->default(false);
            
            // Timestamps and tracking
            $table->timestamp('activity_timestamp'); // When the actual activity occurred
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['student_id', 'activity_type', 'activity_timestamp']);
            $table->index(['academic_year', 'semester']);
            $table->index(['activity_category', 'impact_level']);
            $table->index(['related_model', 'related_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_activity_logs');
    }
};