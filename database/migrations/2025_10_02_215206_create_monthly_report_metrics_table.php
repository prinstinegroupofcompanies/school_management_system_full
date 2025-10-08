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
        Schema::create('monthly_report_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_report_id')->constrained('monthly_reports')->onDelete('cascade');
            $table->string('metric_name'); // e.g., "attendance_rate", "lesson_plans_submitted"
            $table->string('metric_category'); // e.g., "attendance", "teaching", "administrative"
            $table->string('metric_type'); // e.g., "count", "percentage", "score", "duration"
            $table->decimal('metric_value', 10, 3)->nullable(); // The actual metric value
            $table->string('metric_unit')->nullable(); // e.g., "hours", "days", "students", "%"
            $table->text('metric_description')->nullable(); // Description of what this metric represents
            $table->decimal('target_value', 10, 3)->nullable(); // Target or benchmark value
            $table->decimal('previous_value', 10, 3)->nullable(); // Previous period value for comparison
            $table->decimal('improvement_percentage', 5, 2)->nullable(); // % improvement from previous period
            $table->enum('performance_status', ['excellent', 'good', 'satisfactory', 'needs_improvement', 'poor'])->nullable();
            $table->text('notes')->nullable(); // Additional notes about this metric
            $table->json('metadata')->nullable(); // Additional data storage
            $table->timestamps();
            
            // Indexes
            $table->index(['monthly_report_id', 'metric_category']);
            $table->index(['metric_name', 'metric_category']);
            $table->index(['performance_status']);
            $table->index(['metric_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_report_metrics');
    }
};
