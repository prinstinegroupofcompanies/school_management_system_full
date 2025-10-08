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
        Schema::create('report_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('report_templates')->onDelete('cascade');
            $table->foreignId('schedule_id')->nullable()->constrained('report_schedules')->nullOnDelete();
            $table->foreignId('executed_by')->constrained('users')->onDelete('cascade');
            $table->string('execution_id')->unique(); // Auto-generated execution ID
            $table->enum('status', ['pending', 'running', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->json('report_params'); // Report parameters used
            $table->json('filters'); // Applied filters
            $table->string('file_path')->nullable(); // Generated file path
            $table->string('file_name')->nullable(); // Generated file name
            $table->string('file_size')->nullable(); // File size
            $table->string('export_format'); // Export format (PDF, Excel, CSV)
            $table->datetime('started_at')->nullable(); // Execution start time
            $table->datetime('completed_at')->nullable(); // Execution completion time
            $table->integer('execution_time')->nullable(); // Execution time in seconds
            $table->text('error_message')->nullable(); // Error message if failed
            $table->json('execution_log')->nullable(); // Execution log
            $table->json('metadata')->nullable(); // Additional execution data
            $table->timestamps();
            
            $table->index(['template_id', 'status']);
            $table->index(['schedule_id', 'status']);
            $table->index(['executed_by', 'status']);
            $table->index(['status', 'started_at']);
            $table->index(['execution_id']);
            $table->index(['export_format']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_executions');
    }
};