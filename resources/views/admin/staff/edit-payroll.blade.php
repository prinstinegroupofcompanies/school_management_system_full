@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Payroll Record</h1>
        <a href="{{ route('admin.staff.payroll') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            Back to Payroll
        </a>
    </div>

    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.staff.payroll.update', $payroll) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Staff Member</label>
                    <select name="staff_id" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        <option value="">Select staff member</option>
                        @foreach($staff as $member)
                            <option value="{{ $member->id }}" {{ $payroll->staff_id == $member->id ? 'selected' : '' }}>
                                {{ $member->user->name ?? 'N/A' }} ({{ $member->employee_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Academic Period</label>
                    <select name="academic_period_id" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        <option value="">Select period</option>
                        @foreach($academicPeriods as $period)
                            <option value="{{ $period->id }}" {{ $payroll->academic_period_id == $period->id ? 'selected' : '' }}>
                                {{ $period->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Basic Salary</label>
                    <input type="number" name="basic_salary" value="{{ $payroll->basic_salary }}" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Allowances</label>
                    <input type="number" name="allowances" value="{{ $payroll->allowances }}" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Deductions</label>
                    <input type="number" name="deductions" value="{{ $payroll->deductions }}" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bonus</label>
                    <input type="number" name="bonus" value="{{ $payroll->bonus }}" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Overtime Hours</label>
                    <input type="number" name="overtime_hours" value="{{ $payroll->overtime_hours }}" step="0.1" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Overtime Rate</label>
                    <input type="number" name="overtime_rate" value="{{ $payroll->overtime_rate }}" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tax Deduction</label>
                    <input type="number" name="tax_deduction" value="{{ $payroll->tax_deduction }}" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        <option value="pending" {{ $payroll->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processed" {{ $payroll->status === 'processed' ? 'selected' : '' }}>Processed</option>
                        <option value="paid" {{ $payroll->status === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
            </div>

            <!-- Calculated Fields (Read-only) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Gross Pay</label>
                    <input type="text" value="${{ number_format($payroll->gross_pay ?? 0, 2) }}" class="mt-1 block w-full border-gray-300 rounded-md bg-gray-100" readonly>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Net Pay</label>
                    <input type="text" value="${{ number_format($payroll->net_pay ?? 0, 2) }}" class="mt-1 block w-full border-gray-300 rounded-md bg-gray-100" readonly>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="button" onclick="history.back()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Update Payroll
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
