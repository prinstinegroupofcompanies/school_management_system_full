@extends('layouts.student')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">My Finances</h1>
        @php($payTarget = (isset($firstUnpaidFee) && $firstUnpaidFee) ? $firstUnpaidFee : ($fees->where('balance', '>', 0)->first() ?? null))
        @if($payTarget && ($payTarget->balance ?? 0) > 0)
            <a href="{{ route('student.finance.pay', $payTarget) }}" class="inline-flex items-center bg-indigo-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                Pay Fees
            </a>
        @elseif($balanceAmount <= 0)
            <button class="inline-flex items-center bg-green-500 text-white text-sm font-semibold px-4 py-2 rounded-lg cursor-not-allowed" title="All fees paid">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                All Paid
            </button>
        @endif
    </div>

    <!-- Financial Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Fees</dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">${{ number_format($totalAmount, 2) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Amount Paid</dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">${{ number_format($paidAmount, 2) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 {{ $balanceAmount > 0 ? 'bg-red-500' : 'bg-green-500' }} rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Outstanding Balance</dt>
                        <dd class="text-lg font-medium {{ $balanceAmount > 0 ? 'text-red-600' : 'text-green-600' }}">${{ number_format($balanceAmount, 2) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices -->
    <div class="table-premium overflow-hidden mb-6">
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="text-left">Term</th>
                    <th class="text-left">Total</th>
                    <th class="text-left">Paid</th>
                    <th class="text-left">Balance</th>
                    <th class="text-left">Due</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800">
                @forelse($fees as $fee)
                <tr class="border-b last:border-0">
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-200">
                        <div>
                            <div class="font-medium">{{ $fee->feeStructure->name ?? ($fee->description ?? 'School Fee') }}</div>
                            <div class="text-sm text-gray-500">{{ $fee->semester ?? 'Academic Year' }} {{ $fee->year }}</div>
                            <div class="text-sm text-gray-500">Class: {{ $fee->student->classRoom->name ?? 'N/A' }}</div>
                            @if($fee->fee_type)
                                <div class="text-xs text-blue-600">{{ ucfirst($fee->fee_type) }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="status-badge-premium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                            ${{ number_format($fee->total_amount, 2) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="status-badge-premium bg-green-100 text-green-700 dark:bg-green-700/40 dark:text-green-300">
                            ${{ number_format($fee->paid_amount, 2) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="status-badge-premium {{ $fee->balance > 0 ? 'bg-rose-100 text-rose-700 dark:bg-rose-700/40 dark:text-rose-300' : 'bg-green-100 text-green-700 dark:bg-green-700/40 dark:text-green-300' }}">
                            ${{ number_format($fee->balance, 2) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div>
                            <div class="text-gray-700 dark:text-gray-200">{{ $fee->due_date ? \Carbon\Carbon::parse($fee->due_date)->format('M j, Y') : '-' }}</div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $fee->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                   ($fee->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($fee->status) }}
                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="{{ route('student.invoices.download', $fee) }}" class="btn-premium inline-block text-xs px-3 py-2">Invoice</a>
                        @if($fee->balance > 0)
                        <a href="{{ route('student.finance.pay', $fee) }}" class="inline-block bg-indigo-600 text-white text-xs font-semibold px-3 py-2 rounded-lg hover:bg-indigo-700 transition">Pay Now</a>
                        @else
                        <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-3 py-2 rounded-lg">Paid</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No invoices available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Payment Options -->
        <div class="card-premium p-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Payment Options</h2>
            <div class="space-y-2">
                <div>
                    <div class="text-sm text-gray-500">Bank</div>
                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $bankDetails['bank_name'] ?? '' }}</div>
                    <div class="text-gray-600 dark:text-gray-300">{{ $bankDetails['bank_account'] ?? '' }}</div>
                </div>
                <div class="pt-2">
                    <div class="text-sm text-gray-500">Mobile Money</div>
                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $mobileMoney['provider'] ?? '' }}</div>
                    <div class="text-gray-600 dark:text-gray-300">{{ $mobileMoney['number'] ?? '' }}</div>
                </div>
            </div>
        </div>

        <!-- Payment History (Approved) -->
        <div class="card-premium p-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Payment History</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-600 dark:text-gray-300 border-b">
                            <th class="py-2">Date</th>
                            <th class="py-2">Amount</th>
                            <th class="py-2">Method</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $p)
                        <tr class="border-b last:border-0">
                            <td class="py-2 text-gray-700 dark:text-gray-200">{{ $p->created_at->format('Y-m-d') }}</td>
                            <td class="py-2 text-gray-700 dark:text-gray-200">{{ number_format($p->amount, 2) }}</td>
                            <td class="py-2 text-gray-700 dark:text-gray-200">{{ $p->payment_method }}</td>
                            <td class="py-2">
                                <span class="status-badge-premium {{ $p->status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-700/40 dark:text-green-300' : ($p->status === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-700/40 dark:text-rose-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-700/40 dark:text-amber-200') }}">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                            <td class="py-2">
                                @if($p->receipt_path)
                                <a class="text-indigo-600 hover:text-indigo-800" href="{{ asset('storage/'.$p->receipt_path) }}" target="_blank">View</a>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500 dark:text-gray-400">No payments yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pending Payments -->
    <div class="mt-6 card-premium p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Pending Payments</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-600 dark:text-gray-300 border-b">
                        <th class="py-2">Date</th>
                        <th class="py-2">Amount</th>
                        <th class="py-2">Method</th>
                        <th class="py-2">Status</th>
                        <th class="py-2">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingPayments as $p)
                    <tr class="border-b last:border-0">
                        <td class="py-2 text-gray-700 dark:text-gray-200">{{ $p->created_at->format('Y-m-d') }}</td>
                        <td class="py-2 text-gray-700 dark:text-gray-200">{{ number_format($p->amount, 2) }}</td>
                        <td class="py-2 text-gray-700 dark:text-gray-200">{{ $p->payment_method }}</td>
                        <td class="py-2">
                            <span class="status-badge-premium bg-amber-100 text-amber-800 dark:bg-amber-700/40 dark:text-amber-200">Pending</span>
                        </td>
                        <td class="py-2">
                            @if($p->receipt_path)
                            <a class="text-indigo-600 hover:text-indigo-800" href="{{ asset('storage/'.$p->receipt_path) }}" target="_blank">View</a>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500 dark:text-gray-400">No pending payments.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


