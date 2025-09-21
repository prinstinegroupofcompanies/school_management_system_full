@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Financial Reports</h1>
            <p class="mt-2 text-gray-600">Comprehensive financial analysis and reporting</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('finance.reports.payments') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Payment Reports
            </a>
            <a href="{{ route('finance.reports.income') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                Income Analysis
            </a>
        </div>
    </div>

    <!-- Financial Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                    <p class="text-2xl font-bold text-green-600">${{ number_format($totalRevenue, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Monthly Revenue</p>
                    <p class="text-2xl font-bold text-blue-600">${{ number_format($monthlyRevenue, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Outstanding</p>
                    <p class="text-2xl font-bold text-red-600">${{ number_format($totalOutstanding, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Students</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($totalStudents) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Monthly Revenue Trend -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Monthly Revenue Trend</h3>
            <div class="h-64 flex items-center justify-center text-gray-500">
                <div class="text-center">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p>Revenue trend chart</p>
                    <p class="text-sm">Chart visualization coming soon</p>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Methods</h3>
            <div class="space-y-3">
                @forelse($paymentMethods as $method)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div class="font-medium text-gray-900">{{ ucfirst($method->payment_method) }}</div>
                        <div class="text-right">
                            <div class="font-medium text-gray-900">${{ number_format($method->total, 2) }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No payment method data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Outstanding by Class -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Outstanding by Class</h3>
            <div class="space-y-3">
                @forelse($outstandingByClass as $outstanding)
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                        <div class="font-medium text-gray-900">{{ $outstanding->class_name }}</div>
                        <div class="text-right">
                            <div class="font-medium text-red-600">${{ number_format($outstanding->outstanding, 2) }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No outstanding amounts</p>
                @endforelse
            </div>
        </div>

        <!-- Large Payments -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Large Payments (≥$1,000)</h3>
            <div class="space-y-3">
                @forelse($largePayments as $payment)
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <div>
                            <div class="font-medium text-gray-900">{{ $payment->student->user->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $payment->student->classRoom->name ?? 'N/A' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-medium text-green-600">${{ number_format($payment->amount_paid ?? $payment->amount ?? 0, 2) }}</div>
                            <div class="text-xs text-gray-500">{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : ($payment->created_at ? $payment->created_at->format('M d, Y') : 'N/A') }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No large payments found</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('finance.reports.payments') }}" 
               class="flex items-center justify-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="font-medium text-blue-900">Payment Reports</span>
            </a>

            <a href="{{ route('finance.reports.income') }}" 
               class="flex items-center justify-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                <span class="font-medium text-green-900">Income Analysis</span>
            </a>

            <a href="{{ route('finance.fees.structures.index') }}" 
               class="flex items-center justify-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <svg class="w-6 h-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <span class="font-medium text-purple-900">Fee Structures</span>
            </a>
        </div>
    </div>
</div>
@endsection