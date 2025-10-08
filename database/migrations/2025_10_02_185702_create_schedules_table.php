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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['exam', 'event', 'class', 'meeting', 'holiday', 'other'])->default('event');
            $table->enum('category', ['academic', 'administrative', 'social', 'sports', 'cultural', 'other'])->default('academic');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('venue')->nullable();
            $table->text('instructions')->nullable();
            $table->text('important_notes')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'cancelled'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurrence_type', ['daily', 'weekly', 'monthly', 'yearly'])->nullable();
            $table->integer('recurrence_interval')->nullable();
            $table->date('recurrence_end_date')->nullable();
            $table->json('recurrence_days')->nullable(); // For weekly recurrence
            $table->boolean('requires_approval')->default(true);
            $table->boolean('is_public')->default(true);
            $table->boolean('is_active')->default(true);
            $table->json('attachments')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index(['start_date', 'end_date']);
            $table->index(['type', 'status']);
            $table->index(['created_by', 'status']);
            $table->index(['requires_approval', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
