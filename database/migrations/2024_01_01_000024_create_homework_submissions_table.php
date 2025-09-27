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
        Schema::create('homework_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('homework_id')->constrained();
            $table->unsignedBigInteger('student_id')->constrained();
            $table->text('submission_text')->nullable();
            $table->text('attachments')->nullable(); // JSON array of file paths
            $table->timestamp('submitted_at');
            $table->boolean('is_late')->default(false);
            $table->integer('late_minutes')->default(0);
            $table->decimal('late_penalty', 5, 2)->default(0.00);
            $table->decimal('marks_obtained', 5, 2)->nullable();
            $table->decimal('total_marks', 5, 2);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->text('teacher_feedback')->nullable();
            $table->text('student_comments')->nullable();
            $table->enum('status', ['submitted', 'graded', 'returned', 'resubmitted'])->default('submitted');
            $table->boolean('is_approved')->default(false);
            $table->unsignedBigInteger('graded_by')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();
            
            $table->unique(['homework_id', 'student_id']);
            $table->index(['homework_id', 'status']);
            $table->index(['student_id', 'submitted_at']);
            $table->index(['is_late', 'status']);
            $table->index(['marks_obtained', 'total_marks']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homework_submissions');
    }
}; 