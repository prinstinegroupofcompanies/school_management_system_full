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
        Schema::create('e_signatures', function (Blueprint $table) {
            $table->id();
            $table->string('signature_id')->unique(); // Auto-generated signature ID
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // User who created the signature
            $table->string('document_type'); // lesson_plan, grade_submission, monthly_report, etc.
            $table->unsignedBigInteger('document_id'); // ID of the related document
            $table->string('signature_type'); // digital, biometric, pin, password
            $table->text('signature_data')->nullable(); // Encrypted signature data
            $table->string('signature_hash')->nullable(); // Hash for verification
            $table->string('ip_address')->nullable(); // IP address when signed
            $table->string('user_agent')->nullable(); // User agent when signed
            $table->string('device_fingerprint')->nullable(); // Device fingerprint
            $table->string('location')->nullable(); // Geographic location if available
            $table->enum('status', ['pending', 'signed', 'verified', 'expired', 'revoked'])->default('pending');
            $table->datetime('signed_at')->nullable(); // When the signature was applied
            $table->datetime('expires_at')->nullable(); // When the signature expires
            $table->datetime('verified_at')->nullable(); // When the signature was verified
            $table->text('verification_notes')->nullable(); // Verification notes
            $table->text('revocation_reason')->nullable(); // Reason for revocation
            $table->datetime('revoked_at')->nullable(); // When the signature was revoked
            $table->json('metadata')->nullable(); // Additional signature data
            $table->timestamps();
            
            $table->index(['user_id', 'document_type', 'document_id']);
            $table->index(['signature_type', 'status']);
            $table->index(['status', 'signed_at']);
            $table->index(['expires_at']);
            $table->index(['signature_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_signatures');
    }
};