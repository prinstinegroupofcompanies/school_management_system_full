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
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $payment->payment_reference ?? $payment->transaction_reference ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Amount</h3>
                        <p class="mt-1 text-2xl font-bold text-green-600">${{ number_format($payment->amount ?? $payment->amount_paid ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Payment Method</h3>
                        <p class="mt-1 text-lg text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'N/A')) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Payment Date</h3>
                        <p class="mt-1 text-lg text-gray-900">{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : ($payment->created_at ? $payment->created_at->format('M d, Y') : 'N/A') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Status</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($payment->status === 'approved' || $payment->status === 'paid') bg-green-100 text-green-800
                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Submitted Date</h3>
                        <p class="mt-1 text-lg text-gray-900">{{ $payment->created_at->format('M d, Y H:i A') }}</p>
                    </div>
                </div>

                @if($payment->details)
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Payment Details</h3>
                    <p class="mt-1 text-gray-900">{{ $payment->details }}</p>
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

        <!-- Payment Receipt -->
        @if(isset($payment->receipt_path) && $payment->receipt_path)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="bg-gray-50 px-6 py-4">
                <h2 class="text-xl font-semibold text-gray-900">Payment Receipt</h2>
            </div>
            <div class="p-6">
                <div class="text-center">
                    @php
                        $receiptPath = $payment->receipt_path;
                        $extension = pathinfo($receiptPath, PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    @endphp
                    
                    @if($isImage)
                        <div class="max-w-md mx-auto">
                            <img src="{{ asset('storage/' . $receiptPath) }}" 
                                 alt="Payment Receipt" 
                                 class="w-full h-auto rounded-lg shadow-md border border-gray-200">
                        </div>
                        <div class="mt-4">
                            <a href="{{ asset('storage/' . $receiptPath) }}" 
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                View Full Size
                            </a>
                        </div>
                    @else
                        <div class="bg-gray-100 rounded-lg p-8">
                            <i class="fas fa-file-alt text-gray-400 text-6xl mb-4"></i>
                            <p class="text-gray-600 mb-4">Receipt Document</p>
                            <a href="{{ asset('storage/' . $receiptPath) }}" 
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-download mr-2"></i>
                                Download Receipt
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Payment Actions -->
        @if($payment->status === 'pending')
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
    if (confirm('Are you sure you want to approve this payment? This action cannot be undone.')) {
        // Create form to submit approval
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("finance.payments.approve", $payment->id) }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectPayment() {
    const reason = prompt('Please provide a reason for rejection:');
    if (reason !== null) { // Check for null (cancel) vs empty string
        // Create form to submit rejection
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("finance.payments.reject", $payment->id) }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add reason field
        const reasonField = document.createElement('input');
        reasonField.type = 'hidden';
        reasonField.name = 'reason';
        reasonField.value = reason;
        form.appendChild(reasonField);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
