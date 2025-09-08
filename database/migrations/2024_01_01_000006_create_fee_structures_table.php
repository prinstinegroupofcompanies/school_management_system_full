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
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Standard Fee Structure", "Scholarship Fee Structure"
            $table->text('description')->nullable();
            $table->foreignId('class_id')->constrained('class_rooms')->onDelete('cascade');
            $table->string('academic_year');
            $table->enum('fee_type', ['monthly', 'quarterly', 'semester', 'annual'])->default('monthly');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('final_amount', 10, 2);
            $table->date('due_date');
            $table->integer('grace_period_days')->default(0);
            $table->decimal('late_fee_percentage', 5, 2)->default(0.00);
            $table->decimal('late_fee_amount', 10, 2)->default(0.00);
            $table->boolean('allow_installments')->default(false);
            $table->integer('max_installments')->default(1);
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['name', 'class_id', 'academic_year']);
            $table->index(['fee_type', 'status']);
            $table->index(['due_date', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
}; 