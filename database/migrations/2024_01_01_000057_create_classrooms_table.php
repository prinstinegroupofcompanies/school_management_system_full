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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->integer('capacity')->default(30);
            $table->unsignedBigInteger('class_teacher_id')->nullable();
            $table->string('room_number')->nullable();
            $table->string('building')->nullable();
            $table->string('floor')->nullable();
            $table->string('wing')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance', 'archived'])->default('active');
            $table->integer('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('class_teacher_id')->references('id')->on('users');

            // Indexes
            $table->index(['code', 'status']);
            $table->index(['name', 'status']);
            $table->index(['class_teacher_id', 'status']);
            $table->index(['room_number', 'status']);
            $table->index(['building', 'status']);
            $table->index(['floor', 'status']);
            $table->index(['wing', 'status']);
            $table->index(['capacity', 'status']);
            $table->index(['status', 'display_order']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
