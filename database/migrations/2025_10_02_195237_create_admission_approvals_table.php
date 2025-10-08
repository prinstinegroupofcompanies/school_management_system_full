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
        Schema::create('admission_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_application_id')->constrained('admission_applications')->onDelete('cascade');
            $table->foreignId('approver_id')->constrained('users')->onDelete('cascade');
            $table->enum('approval_level', ['first_level', 'second_level']);
            $table->enum('status', ['pending', 'approved', 'rejected']);
            $table->text('comments')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('e_signature')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['admission_application_id', 'approval_level']);
            $table->index(['approver_id', 'status']);
            $table->unique(['admission_application_id', 'approval_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_approvals');
    }
};
