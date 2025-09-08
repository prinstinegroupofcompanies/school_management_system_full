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
        Schema::create('class_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Class 9", "Grade 10"
            $table->string('code')->unique(); // e.g., "C9", "G10"
            $table->text('description')->nullable();
            $table->integer('capacity')->default(40);
            $table->unsignedBigInteger('class_teacher_id')->nullable();
            $table->string('room_number')->nullable();
            $table->string('building')->nullable();
            $table->string('floor')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['name', 'is_active']);
            $table->index(['class_teacher_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_rooms');
    }
}; 