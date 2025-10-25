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
        if (!Schema::hasTable('subject_classes')) {
            Schema::create('subject_classes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subject_id')->constrained()->onDelete('cascade');
                $table->foreignId('class_id')->constrained('class_rooms')->onDelete('cascade');
                $table->timestamps();
                
                // Ensure unique combinations
                $table->unique(['subject_id', 'class_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_classes');
    }
};
