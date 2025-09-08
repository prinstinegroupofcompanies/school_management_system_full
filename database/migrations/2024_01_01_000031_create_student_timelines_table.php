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
        Schema::create('student_timelines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger->constrained();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['admission', 'class_change', 'exam_result', 'attendance', 'fee_payment', 'achievement', 'discipline', 'other'])->default('other');
            $table->string('icon')->nullable(); // FontAwesome icon class
            $table->string('color')->nullable(); // Hex color code
            $table->date('event_date');
            $table->time('event_time')->nullable();
            $table->json('data')->nullable(); // Additional data in JSON format
            $table->string('related_model')->nullable(); // Model class name
            $table->unsignedBigInteger('related_id')->nullable(); // Related record ID
            $table->unsignedBigInteger;
            $table->boolean('is_public')->default(true); // Visible to parents/guardians
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['student_id', 'type']);
            $table->index(['event_date', 'event_time']);
            $table->index(['related_model', 'related_id']);
            $table->index(['is_public', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_timelines');
    }
}; 