<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use HasFactory;

    protected $table = 'payroll';

    protected $fillable = [
        'staff_id',
        'academic_period_id',
        'payroll_number',
        'pay_period_start',
        'pay_period_end',
        'pay_date',
        'basic_salary',
        'hourly_rate',
        'hours_worked',
        'overtime_hours',
        'overtime_rate',
        'housing_allowance',
        'transport_allowance',
        'meal_allowance',
        'medical_allowance',
        'bonus',
        'commission',
        'other_allowances',
        'income_tax',
        'social_security',
        'pension_contribution',
        'health_insurance',
        'loan_deduction',
        'advance_deduction',
        'other_deductions',
        'gross_salary',
        'total_deductions',
        'net_salary',
        'payment_method',
        'bank_name',
        'account_number',
        'transaction_reference',
        'status',
        'processed_by',
        'processed_at',
        'notes',
        'days_worked',
        'days_absent',
        'days_leave',
        'leave_deduction'
    ];

    protected $casts = [
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
        'pay_date' => 'date',
        'processed_at' => 'datetime',
        'basic_salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'hours_worked' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'bonus' => 'decimal:2',
        'commission' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'income_tax' => 'decimal:2',
        'social_security' => 'decimal:2',
        'pension_contribution' => 'decimal:2',
        'health_insurance' => 'decimal:2',
        'loan_deduction' => 'decimal:2',
        'advance_deduction' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'leave_deduction' => 'decimal:2'
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function calculateGrossSalary(): void
    {
        $this->gross_salary = $this->basic_salary + 
                             $this->housing_allowance + 
                             $this->transport_allowance + 
                             $this->meal_allowance + 
                             $this->medical_allowance + 
                             $this->bonus + 
                             $this->commission + 
                             $this->other_allowances +
                             ($this->overtime_hours * $this->overtime_rate);
    }

    public function calculateTotalDeductions(): void
    {
        $this->total_deductions = $this->income_tax + 
                                 $this->social_security + 
                                 $this->pension_contribution + 
                                 $this->health_insurance + 
                                 $this->loan_deduction + 
                                 $this->advance_deduction + 
                                 $this->other_deductions +
                                 $this->leave_deduction;
    }

    public function calculateNetSalary(): void
    {
        $this->calculateGrossSalary();
        $this->calculateTotalDeductions();
        $this->net_salary = $this->gross_salary - $this->total_deductions;
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('pay_period_start', [$startDate, $endDate]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'pending' => 'yellow',
            'approved' => 'blue',
            'processed' => 'green',
            'paid' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getFormattedPayDateAttribute(): string
    {
        return $this->pay_date->format('M d, Y');
    }

    public function getFormattedNetSalaryAttribute(): string
    {
        return 'L$ ' . number_format($this->net_salary, 2);
    }
}
