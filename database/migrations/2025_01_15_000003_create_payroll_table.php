<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->string('payroll_number')->unique();
            $table->date('pay_period_start');
            $table->date('pay_period_end');
            $table->date('pay_date');
            
            // Basic Salary Information
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->decimal('hours_worked', 8, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('overtime_rate', 8, 2)->default(0);
            
            // Allowances
            $table->decimal('housing_allowance', 10, 2)->default(0);
            $table->decimal('transport_allowance', 10, 2)->default(0);
            $table->decimal('meal_allowance', 10, 2)->default(0);
            $table->decimal('medical_allowance', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->decimal('commission', 10, 2)->default(0);
            $table->decimal('other_allowances', 10, 2)->default(0);
            
            // Deductions
            $table->decimal('income_tax', 10, 2)->default(0);
            $table->decimal('social_security', 10, 2)->default(0);
            $table->decimal('pension_contribution', 10, 2)->default(0);
            $table->decimal('health_insurance', 10, 2)->default(0);
            $table->decimal('loan_deduction', 10, 2)->default(0);
            $table->decimal('advance_deduction', 10, 2)->default(0);
            $table->decimal('other_deductions', 10, 2)->default(0);
            
            // Calculations
            $table->decimal('gross_salary', 10, 2);
            $table->decimal('total_deductions', 10, 2);
            $table->decimal('net_salary', 10, 2);
            
            // Payment Information
            $table->enum('payment_method', ['bank_transfer', 'cash', 'check', 'mobile_money'])->default('bank_transfer');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('transaction_reference')->nullable();
            
            // Status and Processing
            $table->enum('status', ['draft', 'pending', 'approved', 'processed', 'paid', 'cancelled'])->default('draft');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();
            
            // Leave and Attendance
            $table->integer('days_worked')->default(0);
            $table->integer('days_absent')->default(0);
            $table->integer('days_leave')->default(0);
            $table->decimal('leave_deduction', 10, 2)->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['staff_id', 'pay_period_start']);
            $table->index(['pay_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll');
    }
};
