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
        Schema::create('homework_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('subject_id')->constrained();
            $table->unsignedBigInteger('class_id')->constrained();
            $table->unsignedBigInteger('teacher_id')->constrained();
            $table->string('assignment_type')->default('homework');
            $table->text('instructions')->nullable();
            $table->text('attachments')->nullable(); // JSON array of file paths
            $table->text('rubric')->nullable(); // JSON array of rubric criteria
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('due_date');
            $table->boolean('allow_late_submission')->default(true);
            $table->decimal('late_penalty_percentage', 5, 2)->default(0.00);
            $table->decimal('total_points', 5, 2)->default(100.00);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['teacher_id', 'is_published']);
            $table->index(['class_id', 'subject_id']);
            $table->index(['due_date', 'is_active']);
            $table->index(['assignment_type', 'is_published']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homework_assignments');
    }
};