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
        Schema::create('student_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('section_id');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('academic_year');
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'late', 'excused', 'half_day'])->default('present');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_excused')->default(false);
            $table->string('excuse_reason')->nullable();
            $table->string('excuse_document')->nullable();
            $table->unsignedBigInteger('marked_by')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('marked_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('class_id')->references('id')->on('classrooms');
            $table->foreign('section_id')->references('id')->on('sections');
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->foreign('marked_by')->references('id')->on('users');
            $table->foreign('verified_by')->references('id')->on('users');

            // Indexes
            $table->index(['student_id', 'status']);
            $table->index(['class_id', 'status']);
            $table->index(['section_id', 'status']);
            $table->index(['subject_id', 'status']);
            $table->index(['academic_year', 'status']);
            $table->index(['attendance_date', 'status']);
            $table->index(['status', 'attendance_date']);
            $table->index(['is_excused', 'status']);
            $table->index(['marked_by', 'status']);
            $table->index(['verified_by', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendance');
    }
};
