@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Fee Structure Details</h1>
            <p class="mt-2 text-gray-600">{{ $feeStructure->name }} - {{ $feeStructure->classRoom->name ?? 'No Class' }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('finance.fees.structures.edit', $feeStructure) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            <a href="{{ route('finance.fees.structures.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                Back to List
            </a>
        </div>
    </div>

    <!-- Fee Structure Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Fee Structure Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                    <p class="text-lg font-medium text-gray-900">{{ $feeStructure->name }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Class</label>
                    <p class="text-lg text-gray-900">{{ $feeStructure->classRoom->name ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Fee Type</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $feeStructure->fee_type === 'tuition' ? 'bg-blue-100 text-blue-800' : 
                           ($feeStructure->fee_type === 'library' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($feeStructure->fee_type) }}
                    </span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Academic Year</label>
                    <p class="text-lg text-gray-900">{{ $feeStructure->academic_year }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $feeStructure->status === 'active' ? 'bg-green-100 text-green-800' : 
                           ($feeStructure->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($feeStructure->status) }}
                    </span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Active for New Enrollments</label>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $feeStructure->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $feeStructure->is_active ? 'Yes' : 'No' }}
                    </span>
                </div>
            </div>

            @if($feeStructure->description)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-500 mb-2">Description</label>
                    <p class="text-gray-900 bg-gray-50 p-3 rounded-md">{{ $feeStructure->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Financial Details -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Financial Details</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-blue-700 mb-1">Total Amount</label>
                    <p class="text-2xl font-bold text-blue-900">${{ number_format($feeStructure->total_amount, 2) }}</p>
                </div>

                @if($feeStructure->discount_percentage > 0 || $feeStructure->discount_amount > 0)
                    <div class="bg-green-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-green-700 mb-1">Discount</label>
                        <p class="text-lg font-semibold text-green-900">
                            @if($feeStructure->discount_percentage > 0)
                                {{ $feeStructure->discount_percentage }}%
                            @endif
                            @if($feeStructure->discount_amount > 0)
                                ${{ number_format($feeStructure->discount_amount, 2) }}
                            @endif
                        </p>
                    </div>
                @endif

                <div class="bg-green-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-green-700 mb-1">Final Amount</label>
                    <p class="text-2xl font-bold text-green-900">${{ number_format($feeStructure->final_amount, 2) }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Due Date</label>
                    <p class="text-lg text-gray-900">{{ $feeStructure->due_date ? $feeStructure->due_date->format('M d, Y') : 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Grace Period</label>
                    <p class="text-lg text-gray-900">{{ $feeStructure->grace_period_days }} days</p>
                </div>

                @if($feeStructure->late_fee_percentage > 0 || $feeStructure->late_fee_amount > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Late Fee</label>
                        <p class="text-lg text-gray-900">
                            @if($feeStructure->late_fee_percentage > 0)
                                {{ $feeStructure->late_fee_percentage }}% per day
                            @endif
                            @if($feeStructure->late_fee_amount > 0)
                                Max: ${{ number_format($feeStructure->late_fee_amount, 2) }}
                            @endif
                        </p>
                    </div>
                @endif

                @if($feeStructure->allow_installments)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Installments</label>
                        <p class="text-lg text-gray-900">Up to {{ $feeStructure->max_installments }} installments allowed</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Student Assignments -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Student Fee Assignments</h3>
        </div>
        <div class="p-6">
            @if($feeStructure->studentFees->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($feeStructure->studentFees as $studentFee)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $studentFee->student->user->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $studentFee->student->student_id ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($studentFee->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($studentFee->paid_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($studentFee->balance, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $studentFee->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                               ($studentFee->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($studentFee->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="text-lg font-medium">No students assigned yet</p>
                    <p class="text-sm">Students will be automatically assigned when they enroll in the class.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
