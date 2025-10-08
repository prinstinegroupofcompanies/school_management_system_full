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
        Schema::create('report_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_name'); // Template name
            $table->string('template_code')->unique(); // Template code for reference
            $table->text('description')->nullable(); // Template description
            $table->string('report_type'); // academic, financial, administrative, attendance, etc.
            $table->string('category'); // student, teacher, staff, finance, etc.
            $table->json('data_sources'); // Data sources and queries
            $table->json('report_structure'); // Report structure and layout
            $table->json('filters'); // Available filters and parameters
            $table->json('charts_config'); // Chart configurations
            $table->json('export_formats'); // Supported export formats (PDF, Excel, CSV)
            $table->string('template_file')->nullable(); // Template file path
            $table->json('permissions'); // Access permissions
            $table->json('notification_settings'); // Notification settings
            $table->boolean('is_public')->default(false); // Public template
            $table->boolean('is_active')->default(true); // Template status
            $table->integer('sort_order')->default(0); // For ordering templates
            $table->json('metadata')->nullable(); // Additional template data
            $table->timestamps();
            
            $table->index(['report_type', 'category']);
            $table->index(['is_active', 'is_public']);
            $table->index(['template_code']);
            $table->index(['sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_templates');
    }
};