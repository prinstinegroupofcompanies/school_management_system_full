@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Income Analysis</h1>
            <p class="mt-2 text-gray-600">Detailed income breakdown and trends</p>
        </div>
        <a href="{{ route('finance.reports.financial') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            Back to Financial Reports
        </a>
    </div>

    <!-- Monthly Income Chart -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Monthly Income Trend ({{ date('Y') }})</h3>
        <div class="h-64 flex items-center justify-center text-gray-500">
            <div class="text-center">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <p>Monthly income chart</p>
                <p class="text-sm">Chart visualization coming soon</p>
            </div>
        </div>
    </div>

    <!-- Income Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Monthly Income Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Monthly Income Breakdown</h3>
            <div class="space-y-3">
                @forelse(($monthlyIncome ?? collect()) as $income)
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <div class="font-medium text-gray-900">{{ $income->month_name }}</div>
                        <div class="text-right">
                            <div class="font-medium text-green-600">${{ number_format($income->income, 2) }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No monthly income data available</p>
                @endforelse
            </div>
        </div>

        <!-- Income by Fee Type -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Income by Fee Type</h3>
            <div class="space-y-3">
                @forelse(($incomeByFeeType ?? collect()) as $feeType => $amount)
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <div class="font-medium text-gray-900">{{ ucfirst($feeType) }}</div>
                        <div class="text-right">
                            <div class="font-medium text-blue-600">${{ number_format($amount, 2) }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No fee type income data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Daily Income for Current Month -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Daily Income - {{ date('F Y') }}</h3>
        @if(($dailyIncome ?? collect())->isNotEmpty())
            <div class="grid grid-cols-7 gap-2 mb-4 text-xs text-gray-500 font-medium">
                <div class="text-center">Sun</div>
                <div class="text-center">Mon</div>
                <div class="text-center">Tue</div>
                <div class="text-center">Wed</div>
                <div class="text-center">Thu</div>
                <div class="text-center">Fri</div>
                <div class="text-center">Sat</div>
            </div>
            <div class="grid grid-cols-7 gap-2">
                @php
                    $firstDay = \Carbon\Carbon::now()->startOfMonth();
                    $lastDay = \Carbon\Carbon::now()->endOfMonth();
                    $startCalendar = $firstDay->copy()->startOfWeek();
                    $endCalendar = $lastDay->copy()->endOfWeek();
                    $dailyIncomeArray = ($dailyIncome ?? collect())->keyBy('day')->toArray();
                @endphp
                
                @for($date = $startCalendar->copy(); $date->lte($endCalendar); $date->addDay())
                    @php
                        $dayIncome = $dailyIncomeArray[$date->day] ?? null;
                        $income = $dayIncome['income'] ?? 0;
                        $isCurrentMonth = $date->month === now()->month;
                    @endphp
                    <div class="aspect-square flex flex-col items-center justify-center p-2 rounded-lg text-sm
                        {{ $isCurrentMonth ? 'bg-gray-50' : 'bg-gray-100 text-gray-400' }}
                        {{ $income > 0 ? 'bg-green-100 text-green-800 font-medium' : '' }}">
                        <div>{{ $date->day }}</div>
                        @if($income > 0)
                            <div class="text-xs">${{ number_format($income, 0) }}</div>
                        @endif
                    </div>
                @endfor
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p>No daily income data available for this month</p>
            </div>
        @endif
    </div>
</div>
@endsection
