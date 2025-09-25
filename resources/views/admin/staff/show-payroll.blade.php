@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Payroll Details</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.staff.payroll.print', $payroll) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </a>
                    <a href="{{ route('admin.staff.payroll.edit', $payroll) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
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
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <!-- Staff Information -->
                <div class="mb-8">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Staff Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Staff Member</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $payroll->staff->user->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Employee ID</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $payroll->staff->employee_id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Department</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $payroll->staff->department->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Designation</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $payroll->staff->designation->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payroll Information -->
                <div class="mb-8">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Payroll Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Payroll Number</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $payroll->payroll_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pay Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $payroll->pay_date ? $payroll->pay_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pay Period</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $payroll->pay_period_start ? $payroll->pay_period_start->format('M d') : 'N/A' }} - 
                                {{ $payroll->pay_period_end ? $payroll->pay_period_end->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($payroll->status == 'paid') bg-green-100 text-green-800
                                @elseif($payroll->status == 'processed') bg-blue-100 text-blue-800
                                @elseif($payroll->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($payroll->status == 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($payroll->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Salary Breakdown -->
                <div class="mb-8">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Salary Breakdown</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Earnings -->
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-3">Earnings</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Basic Salary</span>
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($payroll->basic_salary, 2) }}</span>
                                </div>
                                @if($payroll->housing_allowance > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Housing Allowance</span>
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($payroll->housing_allowance, 2) }}</span>
                                </div>
                                @endif
                                @if($payroll->transport_allowance > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Transport Allowance</span>
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($payroll->transport_allowance, 2) }}</span>
                                </div>
                                @endif
                                @if($payroll->meal_allowance > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Meal Allowance</span>
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($payroll->meal_allowance, 2) }}</span>
                                </div>
                                @endif
                                @if($payroll->medical_allowance > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Medical Allowance</span>
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($payroll->medical_allowance, 2) }}</span>
                                </div>
                                @endif
                                @if($payroll->bonus > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Bonus</span>
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($payroll->bonus, 2) }}</span>
                                </div>
                                @endif
                                @if($payroll->overtime_hours > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Overtime ({{ $payroll->overtime_hours }}h × ${{ number_format($payroll->overtime_rate, 2) }})</span>
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($payroll->overtime_hours * $payroll->overtime_rate, 2) }}</span>
                                </div>
                                @endif
                                <hr class="my-2">
                                <div class="flex justify-between font-semibold">
                                    <span class="text-sm text-gray-900">Gross Salary</span>
                                    <span class="text-sm text-gray-900">${{ number_format($payroll->gross_salary, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Deductions -->
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-3">Deductions</h4>
                            <div class="space-y-2">
                                @if($payroll->income_tax > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Income Tax</span>
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($payroll->income_tax, 2) }}</span>
                                </div>
                                @endif
                                @if($payroll->social_security > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Social Security</span>
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($payroll->social_security, 2) }}</span>
                                </div>
                                @endif
                                @if($payroll->pension_contribution > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Pension Contribution</span>
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($payroll->pension_contribution, 2) }}</span>
                                </div>
                                @endif
                                @if($payroll->health_insurance > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Health Insurance</span>
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($payroll->health_insurance, 2) }}</span>
                                </div>
                                @endif
                                @if($payroll->loan_deduction > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Loan Deduction</span>
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($payroll->loan_deduction, 2) }}</span>
                                </div>
                                @endif
                                @if($payroll->advance_deduction > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Advance Deduction</span>
                                    <span class="text-sm font-medium text-gray-900">${{ number_format($payroll->advance_deduction, 2) }}</span>
                                </div>
                                @endif
                                <hr class="my-2">
                                <div class="flex justify-between font-semibold">
                                    <span class="text-sm text-gray-900">Total Deductions</span>
                                    <span class="text-sm text-gray-900">${{ number_format($payroll->total_deductions, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Net Pay -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Net Pay</h3>
                        <span class="text-2xl font-bold text-green-600">${{ number_format($payroll->net_salary, 2) }}</span>
                    </div>
                </div>

                <!-- Additional Information -->
                @if($payroll->notes)
                <div class="mt-8">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Notes</h3>
                    <p class="text-sm text-gray-900">{{ $payroll->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
