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
        Schema::create('homework', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger;
            $table->unsignedBigInteger;
            $table->unsignedBigInteger->constrained();
            $table->unsignedBigInteger->constrained();
            $table->string('academic_year');
            $table->date('assigned_date');
            $table->date('due_date');
            $table->time('due_time')->nullable();
            $table->decimal('total_marks', 5, 2)->default(10.00);
            $table->text('instructions')->nullable();
            $table->text('materials_required')->nullable();
            $table->text('attachments')->nullable(); // JSON array of file paths
            $table->enum('submission_type', ['online', 'offline', 'both'])->default('online');
            $table->boolean('allow_late_submission')->default(false);
            $table->integer('late_submission_penalty')->default(0); // Percentage penalty
            $table->boolean('require_approval')->default(false);
            $table->enum('status', ['draft', 'published', 'ongoing', 'completed', 'cancelled'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['title', 'academic_year']);
            $table->index(['class_id', 'section_id', 'subject_id']);
            $table->index(['assigned_date', 'due_date']);
            $table->index(['teacher_id', 'status']);
            $table->index(['status', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homework');
    }
}; 