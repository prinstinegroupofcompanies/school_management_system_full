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
        Schema::create('book_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Fiction", "Non-Fiction", "Academic", "Reference"
            $table->string('code')->unique(); // e.g., "FIC", "NFIC", "ACAD", "REF"
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // FontAwesome icon class
            $table->string('color')->nullable(); // Hex color code
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['name', 'is_active']);
            $table->index(['display_order', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_categories');
    }
}; 