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
        Schema::create('transcript_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transcript_id')->constrained('transcripts')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('subject_code')->nullable(); // Subject code for display
            $table->string('subject_name');
            $table->integer('credits')->default(1);
            $table->string('grade_letter'); // A, B, C, D, F, I, W, etc.
            $table->decimal('grade_points', 3, 1)->nullable(); // Grade points (4.0 scale)
            $table->decimal('percentage', 5, 2)->nullable(); // Percentage score
            $table->string('semester')->nullable(); // Which semester this grade belongs to
            $table->string('academic_year')->nullable(); // Academic year
            $table->enum('status', ['passed', 'failed', 'incomplete', 'withdrawn', 'audit'])->default('passed');
            $table->text('notes')->nullable(); // Additional notes about the grade
            $table->boolean('is_repeated')->default(false); // If this is a repeated course
            $table->boolean('is_transfer')->default(false); // If this is a transfer credit
            $table->string('transfer_institution')->nullable(); // Institution if transfer credit
            $table->date('grade_date')->nullable(); // When the grade was assigned
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->timestamps();
            
            // Indexes
            $table->index(['transcript_id', 'subject_id']);
            $table->index(['subject_id', 'grade_letter']);
            $table->index(['semester', 'academic_year']);
            $table->unique(['transcript_id', 'subject_id', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transcript_grades');
    }
};
