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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_number')->unique(); // e.g., "V001", "V002"
            $table->string('registration_number')->unique(); // License plate number
            $table->string('vehicle_type'); // e.g., "Bus", "Van", "Car"
            $table->string('make'); // e.g., "Toyota", "Ford", "Mercedes"
            $table->string('model'); // e.g., "Hiace", "Transit", "Sprinter"
            $table->integer('year_of_manufacture')->nullable();
            $table->string('color')->nullable();
            $table->integer('seating_capacity')->default(20);
            $table->string('engine_number')->nullable();
            $table->string('chassis_number')->nullable();
            $table->string('fuel_type')->default('Petrol'); // Petrol, Diesel, Electric, Hybrid
            $table->decimal('fuel_efficiency', 5, 2)->nullable(); // km/liter
            $table->string('insurance_number')->nullable();
            $table->date('insurance_expiry_date')->nullable();
            $table->string('fitness_certificate_number')->nullable();
            $table->date('fitness_expiry_date')->nullable();
            $table->string('permit_number')->nullable();
            $table->date('permit_expiry_date')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_license_number')->nullable();
            $table->date('driver_license_expiry_date')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('driver_address')->nullable();
            $table->string('conductor_name')->nullable();
            $table->string('conductor_phone')->nullable();
            $table->string('conductor_address')->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->string('purchase_currency')->default('LRD');
            $table->date('purchase_date')->nullable();
            $table->decimal('current_value', 12, 2)->nullable();
            $table->string('current_currency')->default('LRD');
            $table->enum('status', ['active', 'maintenance', 'repair', 'inactive', 'sold', 'scrapped'])->default('active');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['vehicle_number', 'registration_number']);
            $table->index(['vehicle_type', 'status']);
            $table->index(['seating_capacity', 'is_active']);
            $table->index(['insurance_expiry_date', 'fitness_expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
}; 