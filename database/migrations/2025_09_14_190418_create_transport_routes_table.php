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
        // Skip this migration - table already exists from 2024_01_01_000016_create_transport_routes_table.php
        if (!Schema::hasTable('transport_routes')) {
            Schema::create('transport_routes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('transport_id')->constrained('transports')->onDelete('cascade');
                $table->string('name');
                $table->time('pickup_time');
                $table->time('dropoff_time');
                $table->json('pickup_locations')->nullable();
                $table->json('dropoff_locations')->nullable();
                $table->decimal('fare', 8, 2)->nullable();
                $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_routes');
    }
};