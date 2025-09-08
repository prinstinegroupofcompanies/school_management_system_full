@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900">Finance Reports</h1>
            <p class="text-gray-600">Live summaries pushed by Finance</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-sm text-gray-500">Total Fees</div>
                <div class="mt-1 text-2xl font-semibold">${{ number_format($totalFees, 2) }}</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-sm text-gray-500">Total Collected</div>
                <div class="mt-1 text-2xl font-semibold text-green-600">${{ number_format($totalCollected, 2) }}</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-sm text-gray-500">Total Pending</div>
                <div class="mt-1 text-2xl font-semibold text-red-600">${{ number_format($totalPending, 2) }}</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Pushed Reports</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Range</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pushed By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pushed At</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reports as $report)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($report->range) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($report->total, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($report->user)->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $report->pushed_at }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">No reports yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $reports->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection


