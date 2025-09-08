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
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['academic', 'sports', 'arts', 'need_based', 'merit_based', 'special_category', 'other'])->default('academic');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('LRD');
            $table->enum('amount_type', ['fixed', 'percentage', 'variable'])->default('fixed');
            $table->decimal('percentage', 5, 2)->nullable();
            $table->decimal('min_amount', 10, 2)->nullable();
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->integer('available_slots')->nullable();
            $table->integer('awarded_slots')->default(0);
            $table->date('application_start_date');
            $table->date('application_end_date');
            $table->date('award_date')->nullable();
            $table->string('eligibility_criteria')->nullable();
            $table->json('required_documents')->nullable();
            $table->json('selection_criteria')->nullable();
            $table->boolean('requires_interview')->default(false);
            $table->boolean('requires_essay')->default(false);
            $table->text('essay_topic')->nullable();
            $table->integer('essay_word_limit')->nullable();
            $table->boolean('is_renewable')->default(false);
            $table->integer('renewal_years')->nullable();
            $table->text('renewal_criteria')->nullable();
            $table->enum('status', ['active', 'inactive', 'closed', 'archived'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->integer('display_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['code', 'status']);
            $table->index(['name', 'status']);
            $table->index(['type', 'status']);
            $table->index(['amount', 'status']);
            $table->index(['amount_type', 'status']);
            $table->index(['application_start_date', 'status']);
            $table->index(['application_end_date', 'status']);
            $table->index(['available_slots', 'awarded_slots']);
            $table->index(['is_renewable', 'status']);
            $table->index(['is_featured', 'status']);
            $table->index(['status', 'display_order']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
