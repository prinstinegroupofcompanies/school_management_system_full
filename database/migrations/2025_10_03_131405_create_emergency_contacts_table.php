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
        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contact name
            $table->string('organization'); // Hospital, police, fire department, etc.
            $table->string('contact_type'); // medical, police, fire, ambulance, etc.
            $table->string('phone_primary'); // Primary phone number
            $table->string('phone_secondary')->nullable(); // Secondary phone number
            $table->string('email')->nullable(); // Email address
            $table->string('address')->nullable(); // Physical address
            $table->string('city')->nullable(); // City
            $table->string('state')->nullable(); // State/Province
            $table->string('country')->default('Liberia'); // Country
            $table->string('postal_code')->nullable(); // Postal code
            $table->text('services_provided')->nullable(); // Services they provide
            $table->text('specialization')->nullable(); // Medical specialization, etc.
            $table->text('availability')->nullable(); // Hours of operation
            $table->text('notes')->nullable(); // Additional notes
            $table->boolean('is_active')->default(true); // Active status
            $table->integer('priority')->default(1); // Priority level (1-5)
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();
            
            $table->index(['contact_type', 'is_active']);
            $table->index(['organization', 'is_active']);
            $table->index(['priority', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_contacts');
    }
};
