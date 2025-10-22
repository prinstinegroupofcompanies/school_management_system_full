@extends('layouts.app')

@section('title', 'E-Signature Approvals')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">E-Signature Approvals</h1>
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('admin.e-signatures.approvals') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <select name="approver_id" class="rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Approvers</option>
                @foreach($approvers as $approver)
                    <option value="{{ $approver->id }}" {{ request('approver_id') == $approver->id ? 'selected' : '' }}>
                        {{ $approver->name }}
                    </option>
                @endforeach
            </select>
            <select name="level" class="rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Levels</option>
                @foreach($levels as $level)
                    <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>{{ $level }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                Filter
            </button>
        </form>
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Signature ID</th>
                    <th class="py-2 px-4 border-b">Document Type</th>
                    <th class="py-2 px-4 border-b">Approver</th>
                    <th class="py-2 px-4 border-b">Level</th>
                    <th class="py-2 px-4 border-b">Status</th>
                    <th class="py-2 px-4 border-b">Submitted</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($approvals as $approval)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $approval->signature_id }}</td>
                    <td class="py-2 px-4 border-b">{{ $approval->document_type->name ?? '' }}</td>
                    <td class="py-2 px-4 border-b">{{ $approval->approver->name ?? '' }}</td>
                    <td class="py-2 px-4 border-b">{{ $approval->level }}</td>
                    <td class="py-2 px-4 border-b">{{ ucfirst($approval->status) }}</td>
                    <td class="py-2 px-4 border-b">{{ $approval->submitted_at ? $approval->submitted_at->format('Y-m-d') : '-' }}</td>
                    <td class="py-2 px-4 border-b">
                        <a href="{{ route('admin.e-signatures.approvals.show', $approval->id) }}" class="text-blue-600 hover:underline">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-4 text-center text-gray-500">No approvals found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $approvals->links() }}
        </div>
    </div>
</div>
@endsection