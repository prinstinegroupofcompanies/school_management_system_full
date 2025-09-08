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
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g., "Mid-Term Examination 2024"
            $table->text('description')->nullable();
            // Foreign keys
            $table->foreignId('exam_type_id')->constrained('exam_types');
            $table->foreignId('class_id')->constrained('class_rooms');
            $table->string('academic_year');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('venue')->nullable();
            $table->text('instructions')->nullable();
            $table->text('important_notes')->nullable();
            $table->enum('status', ['draft', 'published', 'ongoing', 'completed', 'cancelled'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['title', 'academic_year']);
            $table->index(['exam_type_id', 'class_id']);
            $table->index(['start_date', 'end_date']);
            $table->index(['status', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_schedules');
    }
}; 