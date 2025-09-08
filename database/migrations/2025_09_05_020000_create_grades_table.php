<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('class_rooms')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');

            // Semester 1
            $table->decimal('sem1_p1', 5, 2)->nullable();
            $table->decimal('sem1_p2', 5, 2)->nullable();
            $table->decimal('sem1_p3', 5, 2)->nullable();
            $table->decimal('sem1_exam', 5, 2)->nullable();
            $table->decimal('sem1_avg', 5, 2)->nullable();

            // Semester 2
            $table->decimal('sem2_p4', 5, 2)->nullable();
            $table->decimal('sem2_p5', 5, 2)->nullable();
            $table->decimal('sem2_p6', 5, 2)->nullable();
            $table->decimal('sem2_exam', 5, 2)->nullable();
            $table->decimal('sem2_avg', 5, 2)->nullable();

            // Yearly summary
            $table->decimal('year_avg', 5, 2)->nullable();
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->unique(['student_id','class_id','subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};


