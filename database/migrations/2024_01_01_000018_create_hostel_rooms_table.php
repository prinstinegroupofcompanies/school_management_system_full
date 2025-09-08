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
        Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->string('room_name')->nullable(); // e.g., "Sunrise Room", "Ocean View"
            $table->unsignedBigInteger('hostel_id')->nullable();
            $table->string('building')->nullable();
            $table->string('floor')->nullable();
            $table->string('wing')->nullable();
            $table->integer('capacity')->default(2); // Number of students per room
            $table->integer('current_occupancy')->default(0);
            $table->decimal('room_size', 8, 2)->nullable(); // in square meters
            $table->string('furniture')->nullable(); // Description of furniture
            $table->text('amenities')->nullable(); // JSON array of amenities
            $table->string('air_conditioning')->default('No'); // Yes, No, Central
            $table->string('heating')->default('No'); // Yes, No, Central
            $table->string('internet')->default('No'); // Yes, No, WiFi, Cable
            $table->string('bathroom_type')->default('Shared'); // Private, Shared, En-suite
            $table->string('kitchen_facility')->default('No'); // Yes, No, Shared
            $table->string('laundry_facility')->default('No'); // Yes, No, Shared
            $table->decimal('monthly_rent', 10, 2);
            $table->string('currency')->default('LRD');
            $table->enum('rent_type', ['monthly', 'quarterly', 'semester', 'annual'])->default('monthly');
            $table->decimal('security_deposit', 10, 2)->default(0.00);
            $table->decimal('utility_charges', 10, 2)->default(0.00);
            $table->enum('status', ['available', 'occupied', 'maintenance', 'reserved', 'inactive'])->default('available');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->text('rules_regulations')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['room_number', 'building']);
            $table->index(['room_type_id', 'status']);
            $table->index(['capacity', 'current_occupancy']);
            $table->index(['monthly_rent', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_rooms');
    }
}; 