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
        Schema::create('e_signature_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_name'); // Template name
            $table->string('template_code')->unique(); // Template code for reference
            $table->text('description')->nullable(); // Template description
            $table->string('document_type'); // Type of document this template applies to
            $table->json('signature_fields'); // Fields that require signatures
            $table->json('approval_workflow')->nullable(); // Approval workflow configuration
            $table->json('signature_requirements'); // Signature requirements (type, verification, etc.)
            $table->integer('expiry_days')->default(30); // Signature expiry in days
            $table->boolean('requires_witness')->default(false); // Whether witness signature is required
            $table->boolean('requires_notarization')->default(false); // Whether notarization is required
            $table->json('notification_settings')->nullable(); // Notification settings
            $table->json('security_settings')->nullable(); // Security settings
            $table->boolean('is_active')->default(true); // Template status
            $table->integer('sort_order')->default(0); // For ordering templates
            $table->json('metadata')->nullable(); // Additional template data
            $table->timestamps();
            
            $table->index(['document_type', 'is_active']);
            $table->index(['template_code']);
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_signature_templates');
    }
};