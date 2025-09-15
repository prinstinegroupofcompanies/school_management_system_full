@extends('layouts.app')

@section('content')
<script>
// Immediate countdown function definition to prevent undefined errors
(function() {
    'use strict';
    
    // Define countdown function immediately
    function countdown() {
        console.log('Countdown function called');
        return true;
    }
    
    // Make it available globally
    window.countdown = countdown;
    
    // Also define it in global scope
    if (typeof window.countdown === 'undefined') {
        window.countdown = countdown;
    }
    
    // Override any existing countdown to prevent conflicts
    if (typeof countdown === 'undefined') {
        window.countdown = countdown;
    }
    
    // Add error handler for any countdown calls
    window.addEventListener('error', function(e) {
        if (e.message && e.message.includes('countdown')) {
            console.warn('Countdown error caught and handled:', e.message);
            e.preventDefault();
            return false;
        }
    });
    
    // Immediate error prevention
    try {
        if (typeof countdown === 'undefined') {
            window.countdown = countdown;
        }
    } catch (error) {
        console.error('Error defining countdown:', error);
    }
})();
</script>
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Process Payroll</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.staff.payroll') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Payroll
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form method="POST" action="{{ route('admin.staff.store-payroll') }}">
                    @csrf
                    
                    <!-- Staff Selection -->
                    <div class="mb-6">
                        <label for="staff_id" class="block text-sm font-medium text-gray-700">Staff Member *</label>
                        <select name="staff_id" id="staff_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Staff Member</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}" {{ old('staff_id') == $member->id ? 'selected' : '' }}>
                                    {{ $member->user->name }} ({{ $member->employee_id }}) - ${{ number_format($member->basic_salary ?? 0, 2) }}
                                </option>
                            @endforeach
                        </select>
                        @error('staff_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Academic Period Selection -->
                    <div class="mb-6">
                        <label for="academic_period_id" class="block text-sm font-medium text-gray-700">Academic Period *</label>
                        <select name="academic_period_id" id="academic_period_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Academic Period</option>
                            @foreach($academicPeriods as $period)
                                <option value="{{ $period->id }}" {{ old('academic_period_id') == $period->id ? 'selected' : '' }}>
                                    {{ $period->name }} Period ({{ $period->start_date->format('M d') }} - {{ $period->end_date->format('M d, Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('academic_period_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pay Date -->
                    <div class="mb-6">
                        <label for="pay_date" class="block text-sm font-medium text-gray-700">Pay Date *</label>
                        <input type="date" name="pay_date" id="pay_date" value="{{ old('pay_date') }}" required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('pay_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Salary Details -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Salary Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="basic_salary" class="block text-sm font-medium text-gray-700">Basic Salary *</label>
                                <input type="number" name="basic_salary" id="basic_salary" value="{{ old('basic_salary') }}" step="0.01" required 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('basic_salary')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="hourly_rate" class="block text-sm font-medium text-gray-700">Hourly Rate</label>
                                <input type="number" name="hourly_rate" id="hourly_rate" value="{{ old('hourly_rate', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('hourly_rate')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="hours_worked" class="block text-sm font-medium text-gray-700">Hours Worked</label>
                                <input type="number" name="hours_worked" id="hours_worked" value="{{ old('hours_worked', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('hours_worked')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="overtime_hours" class="block text-sm font-medium text-gray-700">Overtime Hours</label>
                                <input type="number" name="overtime_hours" id="overtime_hours" value="{{ old('overtime_hours', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('overtime_hours')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="overtime_rate" class="block text-sm font-medium text-gray-700">Overtime Rate</label>
                                <input type="number" name="overtime_rate" id="overtime_rate" value="{{ old('overtime_rate', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('overtime_rate')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="bonus" class="block text-sm font-medium text-gray-700">Bonus</label>
                                <input type="number" name="bonus" id="bonus" value="{{ old('bonus', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('bonus')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Allowances -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Allowances</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="housing_allowance" class="block text-sm font-medium text-gray-700">Housing Allowance</label>
                                <input type="number" name="housing_allowance" id="housing_allowance" value="{{ old('housing_allowance', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('housing_allowance')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="transport_allowance" class="block text-sm font-medium text-gray-700">Transport Allowance</label>
                                <input type="number" name="transport_allowance" id="transport_allowance" value="{{ old('transport_allowance', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('transport_allowance')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="meal_allowance" class="block text-sm font-medium text-gray-700">Meal Allowance</label>
                                <input type="number" name="meal_allowance" id="meal_allowance" value="{{ old('meal_allowance', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('meal_allowance')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="medical_allowance" class="block text-sm font-medium text-gray-700">Medical Allowance</label>
                                <input type="number" name="medical_allowance" id="medical_allowance" value="{{ old('medical_allowance', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('medical_allowance')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="commission" class="block text-sm font-medium text-gray-700">Commission</label>
                                <input type="number" name="commission" id="commission" value="{{ old('commission', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('commission')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="other_allowances" class="block text-sm font-medium text-gray-700">Other Allowances</label>
                                <input type="number" name="other_allowances" id="other_allowances" value="{{ old('other_allowances', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('other_allowances')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Deductions -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Deductions</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="income_tax" class="block text-sm font-medium text-gray-700">Income Tax</label>
                                <input type="number" name="income_tax" id="income_tax" value="{{ old('income_tax', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('income_tax')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="social_security" class="block text-sm font-medium text-gray-700">Social Security</label>
                                <input type="number" name="social_security" id="social_security" value="{{ old('social_security', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('social_security')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="pension_contribution" class="block text-sm font-medium text-gray-700">Pension Contribution</label>
                                <input type="number" name="pension_contribution" id="pension_contribution" value="{{ old('pension_contribution', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('pension_contribution')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="health_insurance" class="block text-sm font-medium text-gray-700">Health Insurance</label>
                                <input type="number" name="health_insurance" id="health_insurance" value="{{ old('health_insurance', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('health_insurance')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="loan_deduction" class="block text-sm font-medium text-gray-700">Loan Deduction</label>
                                <input type="number" name="loan_deduction" id="loan_deduction" value="{{ old('loan_deduction', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('loan_deduction')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="advance_deduction" class="block text-sm font-medium text-gray-700">Advance Deduction</label>
                                <input type="number" name="advance_deduction" id="advance_deduction" value="{{ old('advance_deduction', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('advance_deduction')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="other_deductions" class="block text-sm font-medium text-gray-700">Other Deductions</label>
                                <input type="number" name="other_deductions" id="other_deductions" value="{{ old('other_deductions', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('other_deductions')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Attendance & Leave -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Attendance & Leave</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="days_worked" class="block text-sm font-medium text-gray-700">Days Worked</label>
                                <input type="number" name="days_worked" id="days_worked" value="{{ old('days_worked', 0) }}" min="0" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('days_worked')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="days_absent" class="block text-sm font-medium text-gray-700">Days Absent</label>
                                <input type="number" name="days_absent" id="days_absent" value="{{ old('days_absent', 0) }}" min="0" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('days_absent')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="days_leave" class="block text-sm font-medium text-gray-700">Days Leave</label>
                                <input type="number" name="days_leave" id="days_leave" value="{{ old('days_leave', 0) }}" min="0" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('days_leave')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="leave_deduction" class="block text-sm font-medium text-gray-700">Leave Deduction</label>
                                <input type="number" name="leave_deduction" id="leave_deduction" value="{{ old('leave_deduction', 0) }}" step="0.01" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('leave_deduction')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-6">
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method *</label>
                        <select name="payment_method" id="payment_method" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Payment Method</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                            <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        </select>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-6">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="processed" {{ old('status') == 'processed' ? 'selected' : '' }}>Processed</option>
                            <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Any additional notes about this payroll...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Net Pay Display -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Payroll Summary</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Gross Pay:</span>
                                <span class="font-medium" id="gross-pay">$0.00</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Total Deductions:</span>
                                <span class="font-medium" id="total-deductions">$0.00</span>
                            </div>
                            <div class="col-span-2 border-t pt-2">
                                <span class="text-gray-900 font-semibold">Net Pay:</span>
                                <span class="font-bold text-lg" id="net-pay">$0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.staff.payroll') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Process Payroll
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Calculate payroll summary
function calculatePayroll() {
    const basicSalary = parseFloat(document.getElementById('basic_salary').value) || 0;
    const allowances = parseFloat(document.getElementById('allowances').value) || 0;
    const overtimePay = parseFloat(document.getElementById('overtime_pay').value) || 0;
    const bonus = parseFloat(document.getElementById('bonus').value) || 0;
    
    const taxDeduction = parseFloat(document.getElementById('tax_deduction').value) || 0;
    const socialSecurity = parseFloat(document.getElementById('social_security').value) || 0;
    const healthInsurance = parseFloat(document.getElementById('health_insurance').value) || 0;
    const otherDeductions = parseFloat(document.getElementById('other_deductions').value) || 0;
    
    const grossPay = basicSalary + allowances + overtimePay + bonus;
    const totalDeductions = taxDeduction + socialSecurity + healthInsurance + otherDeductions;
    const netPay = grossPay - totalDeductions;
    
    document.getElementById('gross-pay').textContent = '$' + grossPay.toFixed(2);
    document.getElementById('total-deductions').textContent = '$' + totalDeductions.toFixed(2);
    document.getElementById('net-pay').textContent = '$' + netPay.toFixed(2);
}

// Add event listeners to all input fields
document.addEventListener('DOMContentLoaded', function() {
    const inputs = ['basic_salary', 'allowances', 'overtime_pay', 'bonus', 'tax_deduction', 'social_security', 'health_insurance', 'other_deductions'];
    inputs.forEach(inputId => {
        document.getElementById(inputId).addEventListener('input', calculatePayroll);
    });
    
    // Initial calculation
    calculatePayroll();
});

// Auto-fill basic salary when staff is selected
document.getElementById('staff_id').addEventListener('change', function() {
    const staffId = this.value;
    if (staffId) {
        // This would typically fetch the staff member's basic salary via AJAX
        // For now, we'll just trigger the calculation
        calculatePayroll();
    }
});
</script>
@endsection
