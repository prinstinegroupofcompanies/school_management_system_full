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
        Schema::create('exam_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Mid-Term", "Final", "Unit Test", "Quiz"
            $table->string('code')->unique(); // e.g., "MT", "FT", "UT", "QZ"
            $table->text('description')->nullable();
            $table->enum('type', ['written', 'oral', 'practical', 'online', 'mixed'])->default('written');
            $table->integer('total_marks')->default(100);
            $table->decimal('passing_marks', 5, 2)->default(40.00);
            $table->integer('duration_minutes')->nullable();
            $table->boolean('is_compulsory')->default(true);
            $table->boolean('counts_for_final')->default(true);
            $table->integer('weightage_percentage')->default(100);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['name', 'type']);
            $table->index(['is_compulsory', 'counts_for_final']);
            $table->index(['status', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_types');
    }
}; 