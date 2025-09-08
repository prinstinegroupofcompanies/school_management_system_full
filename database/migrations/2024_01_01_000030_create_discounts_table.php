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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed_amount', 'sliding_scale'])->default('percentage');
            $table->decimal('value', 10, 2);
            $table->string('currency', 3)->default('LRD');
            $table->decimal('min_amount', 10, 2)->nullable();
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_stackable')->default(false);
            $table->integer('max_usage')->nullable();
            $table->integer('current_usage')->default(0);
            $table->json('applicable_fee_types')->nullable();
            $table->json('applicable_classes')->nullable();
            $table->json('applicable_categories')->nullable();
            $table->json('applicable_groups')->nullable();
            $table->json('excluded_students')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['code', 'is_active']);
            $table->index(['name', 'is_active']);
            $table->index(['type', 'is_active']);
            $table->index(['value', 'is_active']);
            $table->index(['start_date', 'end_date']);
            $table->index(['is_stackable', 'is_active']);
            $table->index(['max_usage', 'current_usage']);
            $table->index(['applicable_fee_types', 'is_active']);
            $table->index(['applicable_classes', 'is_active']);
            $table->index(['created_at', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
