@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Fee Payments</h1>
        <a href="{{ route('finance.invoices.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">Bulk Send Invoices</a>
    </div>
    <div class="bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Structure</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($payments as $p)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $p->student->user->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $p->feeStructure->name ?? ($p->feeStructure->fee_type ?? 'N/A') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${{ number_format($p->amount_paid ?? $p->amount ?? 0, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') : 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500">No payments recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $payments->links() }}</div>
    </div>
</div>
@endsection


