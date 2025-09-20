@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Payment Details</h1>
                    <p class="mt-2 text-gray-600">View payment information and process approval</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('finance.payments.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Payments
                    </a>
                    <button onclick="window.print()" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-print mr-2"></i>
                        Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Payment Information -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                <h2 class="text-xl font-semibold text-white">Payment Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Payment Reference</h3>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $payment->payment_reference ?? 'PAY-2025-001' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Amount</h3>
                        <p class="mt-1 text-2xl font-bold text-green-600">${{ number_format($payment->amount ?? 1500, 2) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Payment Method</h3>
                        <p class="mt-1 text-lg text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'bank_transfer')) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Payment Date</h3>
                        <p class="mt-1 text-lg text-gray-900">{{ $payment->payment_date ?? now()->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Status</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if(($payment->status ?? 'approved') === 'approved') bg-green-100 text-green-800
                            @elseif(($payment->status ?? 'pending') === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($payment->status ?? 'Approved') }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Academic Year</h3>
                        <p class="mt-1 text-lg text-gray-900">{{ $payment->academic_year ?? date('Y') }}</p>
                    </div>
                </div>

                @if($payment->notes ?? false)
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Notes</h3>
                    <p class="mt-1 text-gray-900">{{ $payment->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Student Information -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="bg-gray-50 px-6 py-4">
                <h2 class="text-xl font-semibold text-gray-900">Student Information</h2>
            </div>
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 h-16 w-16">
                        <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-xl font-semibold text-gray-900">{{ $payment->student->user->name ?? 'John Doe' }}</h3>
                        <p class="text-gray-600">{{ $payment->student->admission_number ?? 'ADM-2025-001' }}</p>
                        <p class="text-sm text-gray-500">{{ $payment->student->classRoom->name ?? 'Grade 10A' }} • Student ID: {{ $payment->student->student_number ?? 'STU-2025-001' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Fees</h4>
                        <p class="mt-1 text-lg font-semibold text-gray-900">${{ number_format($payment->student->total_fees ?? 5000, 2) }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Paid Amount</h4>
                        <p class="mt-1 text-lg font-semibold text-green-600">${{ number_format($payment->student->paid_fees ?? 3500, 2) }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Balance</h4>
                        <p class="mt-1 text-lg font-semibold text-red-600">${{ number_format($payment->student->balance_fees ?? 1500, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Actions -->
        @if(($payment->status ?? 'pending') === 'pending')
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Actions</h2>
            <div class="flex space-x-4">
                <button onclick="approvePayment()" 
                        class="px-6 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-check mr-2"></i>
                    Approve Payment
                </button>
                <button onclick="rejectPayment()" 
                        class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Reject Payment
                </button>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function approvePayment() {
    if (confirm('Are you sure you want to approve this payment?')) {
        // In a real implementation, this would make an AJAX call
        alert('Payment approved successfully!');
        location.reload();
    }
}

function rejectPayment() {
    const reason = prompt('Please provide a reason for rejection:');
    if (reason) {
        // In a real implementation, this would make an AJAX call
        alert('Payment rejected successfully!');
        location.reload();
    }
}
</script>
@endsection
