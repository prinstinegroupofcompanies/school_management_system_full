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
        Schema::create('student_timeline', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['academic', 'attendance', 'fee', 'exam', 'achievement', 'disciplinary', 'transfer', 'graduation', 'other'])->default('academic');
            $table->enum('category', ['milestone', 'event', 'achievement', 'warning', 'note', 'update'])->default('event');
            $table->date('event_date');
            $table->time('event_time')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->json('metadata')->nullable();
            $table->string('related_model')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('is_public')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('created_by')->references('id')->on('users');

            // Indexes
            $table->index(['student_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['category', 'status']);
            $table->index(['event_date', 'status']);
            $table->index(['is_public', 'status']);
            $table->index(['is_featured', 'status']);
            $table->index(['related_model', 'related_id']);
            $table->index(['created_by', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_timeline');
    }
};
