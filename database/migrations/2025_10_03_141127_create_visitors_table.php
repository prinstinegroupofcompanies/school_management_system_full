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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id')->unique(); // Auto-generated visitor ID
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('id_number')->nullable(); // National ID, passport, etc.
            $table->string('id_type')->nullable(); // passport, national_id, driver_license, etc.
            $table->string('organization')->nullable(); // Company, school, etc.
            $table->string('position')->nullable(); // Job title or role
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('Liberia');
            $table->string('postal_code')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->enum('visitor_type', ['parent', 'guardian', 'vendor', 'contractor', 'official', 'guest', 'other'])->default('guest');
            $table->foreignId('category_id')->nullable()->constrained('visitor_categories')->nullOnDelete();
            $table->boolean('is_blacklisted')->default(false);
            $table->text('blacklist_reason')->nullable();
            $table->date('blacklist_date')->nullable();
            $table->boolean('requires_escort')->default(false);
            $table->text('special_instructions')->nullable();
            $table->json('attachments')->nullable(); // ID documents, photos, etc.
            $table->json('metadata')->nullable(); // Additional visitor data
            $table->timestamps();
            
            $table->index(['visitor_type', 'is_blacklisted']);
            $table->index(['category_id', 'is_blacklisted']);
            $table->index(['email', 'phone']);
            $table->index(['first_name', 'last_name']);
            $table->index(['organization']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
