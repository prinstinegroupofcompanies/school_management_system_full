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
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('category', ['academic', 'administrative', 'support', 'management'])->default('academic');
            $table->integer('hierarchy_level')->default(0);
            $table->decimal('base_salary', 10, 2)->nullable();
            $table->string('currency', 3)->default('LRD');
            $table->text('responsibilities')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('experience_required')->nullable();
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->integer('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['code', 'status']);
            $table->index(['name', 'status']);
            $table->index(['category', 'status']);
            $table->index(['hierarchy_level', 'status']);
            $table->index(['status', 'display_order']);
            $table->index(['base_salary', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designations');
    }
};
