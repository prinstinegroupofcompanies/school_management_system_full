<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_class', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('class_room_id');
            $table->boolean('is_class_teacher')->default(false);
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('unassigned_at')->nullable();
            $table->timestamps();

            $table->unique(['teacher_id','class_room_id']);
            $table->index(['class_room_id','is_class_teacher']);
            $table->index(['teacher_id','is_class_teacher']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_class');
    }
};


