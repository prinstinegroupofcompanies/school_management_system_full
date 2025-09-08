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
        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->string('route_name'); // e.g., "Route A - Central Monrovia", "Route B - Paynesville"
            $table->string('route_code')->unique(); // e.g., "R001", "R002"
            $table->text('description')->nullable();
            $table->text('route_details')->nullable(); // Detailed route description
            $table->string('start_location');
            $table->string('end_location');
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->integer('estimated_duration_minutes')->nullable();
            $table->time('morning_pickup_time')->nullable();
            $table->time('morning_dropoff_time')->nullable();
            $table->time('afternoon_pickup_time')->nullable();
            $table->time('afternoon_dropoff_time')->nullable();
            $table->decimal('fare_amount', 10, 2);
            $table->string('currency')->default('LRD');
            $table->enum('fare_type', ['monthly', 'quarterly', 'semester', 'annual'])->default('monthly');
            $table->integer('max_capacity')->default(50);
            $table->integer('current_capacity')->default(0);
            $table->enum('status', ['active', 'inactive', 'maintenance', 'suspended'])->default('active');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['route_name', 'route_code']);
            $table->index(['start_location', 'end_location']);
            $table->index(['status', 'is_active']);
            $table->index(['current_capacity', 'max_capacity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_routes');
    }
}; 