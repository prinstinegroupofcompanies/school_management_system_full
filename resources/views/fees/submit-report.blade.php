@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Submit Finance Report</h1>
            <p class="text-gray-600">Push a summary to admin and view your submissions</p>
        </div>
        <a href="{{ route('admin.fees.reports') }}" class="px-3 py-2 bg-gray-600 text-white rounded">Admin Reports</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="POST" action="{{ route('fees.reports.push') }}" class="flex items-end space-x-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Range</label>
                <select name="range" class="mt-1 block w-48 border-gray-300 rounded-md" required>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Submit Report</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">My Submitted Reports</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Range</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted At</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($myReports as $report)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($report->range) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($report->total, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $report->pushed_at }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500">No submissions yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $myReports->links() }}</div>
        </div>
    </div>
</div>
@endsection


