@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Safety Checks</h1>
    <table class="min-w-full bg-white">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Check Number</th>
                <th class="py-2 px-4 border-b">Type</th>
                <th class="py-2 px-4 border-b">Area</th>
                <th class="py-2 px-4 border-b">Status</th>
                <th class="py-2 px-4 border-b">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($checks as $check)
            <tr>
                <td class="py-2 px-4 border-b">{{ $check->check_number }}</td>
                <td class="py-2 px-4 border-b">{{ $check->check_type }}</td>
                <td class="py-2 px-4 border-b">{{ $check->area_checked }}</td>
                <td class="py-2 px-4 border-b">{{ ucfirst($check->status) }}</td>
                <td class="py-2 px-4 border-b">{{ $check->check_date }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-4 text-center text-gray-500">No safety checks found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">
        {{ $checks->links() }}
    </div>
</div>
@endsection