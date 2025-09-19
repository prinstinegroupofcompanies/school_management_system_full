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
        Schema::create('class_fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('class_rooms')->onDelete('cascade');
            $table->string('academic_year');
            
            // Tuition and Fees Breakdown
            $table->decimal('tuition_fee', 10, 2)->default(0);
            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->decimal('library_fee', 10, 2)->default(0);
            $table->decimal('laboratory_fee', 10, 2)->default(0);
            $table->decimal('sports_fee', 10, 2)->default(0);
            $table->decimal('technology_fee', 10, 2)->default(0);
            $table->decimal('examination_fee', 10, 2)->default(0);
            $table->decimal('activity_fee', 10, 2)->default(0);
            $table->decimal('transport_fee', 10, 2)->default(0);
            $table->decimal('hostel_fee', 10, 2)->default(0);
            $table->decimal('meal_fee', 10, 2)->default(0);
            $table->decimal('uniform_fee', 10, 2)->default(0);
            $table->decimal('book_fee', 10, 2)->default(0);
            $table->decimal('miscellaneous_fee', 10, 2)->default(0);
            
            // Calculated totals
            $table->decimal('total_mandatory_fees', 10, 2)->default(0);
            $table->decimal('total_optional_fees', 10, 2)->default(0);
            $table->decimal('total_fees', 10, 2)->default(0);
            
            // Payment terms
            $table->enum('payment_frequency', ['monthly', 'quarterly', 'semester', 'annual'])->default('semester');
            $table->integer('installments_allowed')->default(1);
            $table->decimal('late_fee_percentage', 5, 2)->default(5.00);
            $table->integer('grace_period_days')->default(7);
            
            // Status and validity
            $table->boolean('is_active')->default(true);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['class_id', 'academic_year']);
            $table->index(['is_active', 'effective_from']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_fee_structures');
    }
};