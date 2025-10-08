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
        Schema::create('visitor_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Category name (e.g., "Parents", "Vendors", "Contractors")
            $table->string('code')->unique(); // Category code for easy reference
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Icon class or image path
            $table->string('color')->nullable(); // Color code for UI
            $table->boolean('requires_approval')->default(false); // Whether visitors in this category need approval
            $table->boolean('requires_escort')->default(false); // Whether visitors in this category need escort
            $table->integer('max_visits_per_day')->nullable(); // Maximum visits per day for this category
            $table->json('allowed_areas')->nullable(); // Areas this category can access
            $table->json('restricted_areas')->nullable(); // Areas this category cannot access
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // For ordering categories
            $table->json('metadata')->nullable(); // Additional category data
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_categories');
    }
};
