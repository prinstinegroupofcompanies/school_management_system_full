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
        Schema::create('safety_checks', function (Blueprint $table) {
            $table->id();
            $table->string('check_number')->unique(); // Auto-generated check number
            $table->foreignId('checked_by')->constrained('users')->onDelete('cascade');
            $table->string('check_type'); // fire_safety, equipment, building, playground, etc.
            $table->string('area_checked'); // Specific area or equipment checked
            $table->text('check_description'); // What was checked
            $table->enum('status', ['passed', 'failed', 'needs_attention', 'critical'])->default('passed');
            $table->text('findings')->nullable(); // What was found during the check
            $table->text('recommendations')->nullable(); // Recommendations for improvement
            $table->text('corrective_actions')->nullable(); // Actions taken to fix issues
            $table->date('check_date'); // Date of the safety check
            $table->date('next_check_date')->nullable(); // When next check is due
            $table->json('checklist_items')->nullable(); // Checklist items and their status
            $table->json('photos')->nullable(); // Photos of issues or good practices
            $table->boolean('requires_follow_up')->default(false); // Requires follow-up action
            $table->date('follow_up_date')->nullable(); // Follow-up date
            $table->text('follow_up_notes')->nullable(); // Follow-up notes
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('approved_at')->nullable(); // When approved
            $table->text('approval_notes')->nullable(); // Approval notes
            $table->text('notes')->nullable(); // Additional notes
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            $table->index(['check_type', 'status']);
            $table->index(['area_checked', 'check_date']);
            $table->index(['checked_by', 'check_date']);
            $table->index(['status', 'requires_follow_up']);
            $table->index(['next_check_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safety_checks');
    }
};
