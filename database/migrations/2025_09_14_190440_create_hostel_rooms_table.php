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
        // Skip this migration - table already exists from earlier migration

        if (!Schema::hasTable('hostel_rooms')) {

            Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained('hostels')->onDelete('cascade');
            $table->string('room_number');
            $table->string('room_name')->nullable();
            $table->string('building')->nullable();
            $table->integer('floor')->nullable();
            $table->string('wing')->nullable();
            $table->integer('capacity')->default(1);
            $table->integer('current_occupancy')->default(0);
            $table->decimal('room_size', 8, 2)->nullable();
            $table->text('furniture')->nullable();
            $table->json('amenities')->nullable();
            $table->boolean('air_conditioning')->default(false);
            $table->boolean('heating')->default(false);
            $table->boolean('internet')->default(false);
            $table->string('bathroom_type')->nullable();
            $table->boolean('kitchen_facility')->default(false);
            $table->boolean('laundry_facility')->default(false);
            $table->decimal('monthly_rent', 8, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('rent_type')->nullable();
            $table->decimal('security_deposit', 8, 2)->default(0);
            $table->decimal('utility_charges', 8, 2)->default(0);
            $table->enum('status', ['available', 'occupied', 'maintenance', 'reserved', 'cleaning', 'inactive'])->default('available');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->text('rules_regulations')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_rooms');
    }
};