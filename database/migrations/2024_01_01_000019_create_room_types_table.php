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
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Single Room", "Double Room", "Suite", "Dormitory"
            $table->string('code')->unique(); // e.g., "SR", "DR", "SU", "DM"
            $table->text('description')->nullable();
            $table->integer('capacity')->default(1); // Number of students per room
            $table->decimal('base_price', 10, 2);
            $table->string('currency')->default('LRD');
            $table->enum('pricing_type', ['per_person', 'per_room'])->default('per_person');
            $table->text('features')->nullable(); // JSON array of features
            $table->text('amenities')->nullable(); // JSON array of amenities
            $table->string('room_size_range')->nullable(); // e.g., "15-20 sqm"
            $table->string('bathroom_type')->default('Shared'); // Private, Shared, En-suite
            $table->string('kitchen_facility')->default('No'); // Yes, No, Shared
            $table->string('laundry_facility')->default('No'); // Yes, No, Shared
            $table->string('internet_access')->default('No'); // Yes, No, WiFi, Cable
            $table->string('air_conditioning')->default('No'); // Yes, No, Central
            $table->string('heating')->default('No'); // Yes, No, Central
            $table->integer('display_order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['name', 'capacity']);
            $table->index(['base_price', 'status']);
            $table->index(['display_order', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
}; 