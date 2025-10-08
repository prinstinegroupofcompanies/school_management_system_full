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
        Schema::create('e_signature_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('signature_id')->constrained('e_signatures')->onDelete('cascade');
            $table->foreignId('approver_id')->constrained('users')->onDelete('cascade'); // User who approved
            $table->string('approval_level'); // first_level, second_level, final_approval
            $table->enum('status', ['pending', 'approved', 'rejected', 'delegated'])->default('pending');
            $table->text('approval_notes')->nullable(); // Approval notes
            $table->text('rejection_reason')->nullable(); // Rejection reason
            $table->datetime('approved_at')->nullable(); // When approved
            $table->datetime('rejected_at')->nullable(); // When rejected
            $table->foreignId('delegated_to')->nullable()->constrained('users')->nullOnDelete(); // If delegated
            $table->datetime('delegated_at')->nullable(); // When delegated
            $table->text('delegation_notes')->nullable(); // Delegation notes
            $table->string('ip_address')->nullable(); // IP address when approved
            $table->string('user_agent')->nullable(); // User agent when approved
            $table->json('metadata')->nullable(); // Additional approval data
            $table->timestamps();
            
            $table->index(['signature_id', 'approval_level']);
            $table->index(['approver_id', 'status']);
            $table->index(['status', 'approved_at']);
            $table->index(['delegated_to', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_signature_approvals');
    }
};