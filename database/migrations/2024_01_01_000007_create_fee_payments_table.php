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
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_no')->unique();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('fee_structure_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('amount_due', 10, 2);
            $table->decimal('amount_total', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('late_fee_amount', 10, 2)->default(0.00);
            $table->decimal('fine_amount', 10, 2)->default(0.00);
            $table->decimal('balance_amount', 10, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'online', 'mobile_money', 'wallet'])->default('cash');
            $table->string('transaction_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('check_number')->nullable();
            $table->string('mobile_money_provider')->nullable();
            $table->string('mobile_money_number')->nullable();
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->date('payment_date');
            $table->date('due_date');
            $table->integer('installment_number')->default(1);
            $table->text('payment_notes')->nullable();
            $table->text('receipt_notes')->nullable();
            $table->string('receipt_number')->nullable();
            $table->foreignId('collected_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['active', 'cancelled', 'refunded'])->default('active');
            $table->timestamps();
            
            $table->index(['payment_no', 'student_id']);
            $table->index(['payment_date', 'payment_status']);
            $table->index(['student_id', 'fee_structure_id']);
            $table->index(['due_date', 'payment_status']);
            $table->index(['collected_by', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
    }
}; 