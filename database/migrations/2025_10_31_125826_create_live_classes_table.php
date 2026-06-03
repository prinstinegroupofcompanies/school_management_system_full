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
        Schema::create('live_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('class_rooms')->onDelete('set null');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('meeting_url'); // Zoom, Google Meet, or custom URL
            $table->string('meeting_id')->nullable();
            $table->string('meeting_password')->nullable();
            $table->enum('platform', ['zoom', 'google_meet', 'microsoft_teams', 'custom', 'other'])->default('zoom');
            $table->datetime('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled');
            $table->json('attendance_data')->nullable(); // {student_id: joined_at, ...}
            $table->json('recording_urls')->nullable(); // Array of recording URLs
            $table->boolean('is_recorded')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['teacher_id', 'scheduled_at']);
            $table->index(['class_id', 'scheduled_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_classes');
    }
};
