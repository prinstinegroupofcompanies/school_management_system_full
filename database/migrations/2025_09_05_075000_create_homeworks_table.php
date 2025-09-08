<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homeworks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('teacher_id');
            $table->date('due_date');
            $table->enum('status', ['active','completed','cancelled'])->default('active');
            $table->timestamps();

            $table->index(['class_id','subject_id']);
            $table->index(['teacher_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homeworks');
    }
};


