<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('study_materials')) {
            Schema::create('study_materials', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->unsignedBigInteger('subject_id');
                $table->unsignedBigInteger('class_id');
                $table->unsignedBigInteger('teacher_id');
                $table->enum('type', ['document','video','link','other'])->default('document');
                $table->string('file_path')->nullable();
                $table->string('file_name')->nullable();
                $table->bigInteger('file_size')->nullable();
                $table->string('link')->nullable();
                $table->json('tags')->nullable();
                $table->enum('status', ['active','inactive'])->default('active');
                $table->timestamps();

                $table->index(['subject_id','class_id']);
                $table->index(['teacher_id','status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('study_materials');
    }
};


