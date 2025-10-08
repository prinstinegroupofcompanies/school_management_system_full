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
        Schema::create('report_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('report_templates')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('schedule_name'); // Schedule name
            $table->text('description')->nullable(); // Schedule description
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly', 'custom'])->default('monthly');
            $table->json('schedule_config'); // Schedule configuration (days, times, etc.)
            $table->json('report_params'); // Report parameters and filters
            $table->json('recipients'); // Email recipients
            $table->json('export_settings'); // Export format and settings
            $table->boolean('is_active')->default(true); // Schedule status
            $table->datetime('last_executed')->nullable(); // Last execution time
            $table->datetime('next_execution')->nullable(); // Next execution time
            $table->integer('execution_count')->default(0); // Total executions
            $table->json('metadata')->nullable(); // Additional schedule data
            $table->timestamps();
            
            $table->index(['template_id', 'is_active']);
            $table->index(['created_by', 'is_active']);
            $table->index(['frequency', 'is_active']);
            $table->index(['next_execution']);
            $table->index(['last_executed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_schedules');
    }
};