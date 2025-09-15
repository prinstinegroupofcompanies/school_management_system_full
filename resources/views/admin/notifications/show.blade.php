@extends('layouts.app')

@section('title', 'Notification Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Notification Details</h1>
        <div class="flex space-x-4">
            <a href="{{ route('admin.notifications.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back to Notifications
            </a>
            @if($notification->status == 'failed')
                <form method="POST" action="{{ route('admin.notifications.bulk-action') }}" class="inline">
                    @csrf
                    <input type="hidden" name="action" value="resend">
                    <input type="hidden" name="notification_ids[]" value="{{ $notification->id }}">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        Resend
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <!-- Notification Header -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $notification->title }}</h2>
                    <span class="px-3 py-1 text-sm font-medium rounded-full
                        {{ $notification->status == 'sent' ? 'bg-green-100 text-green-800' : 
                           ($notification->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                            'bg-red-100 text-red-800') }}">
                        {{ ucfirst($notification->status) }}
                    </span>
                </div>
                <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                    <span>Type: {{ ucfirst(str_replace('_', ' ', $notification->type)) }}</span>
                    <span>Method: {{ ucfirst($notification->delivery_method) }}</span>
                    <span>Sent: {{ $notification->created_at->format('M d, Y H:i') }}</span>
                </div>
            </div>

            <div class="px-6 py-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Message</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $notification->message }}</p>
                </div>
            </div>
        </div>

        <!-- Recipient Information -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recipient Information</h3>
            </div>
            <div class="px-6 py-4">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-medium text-gray-900">{{ $notification->user->name }}</h4>
                        <p class="text-sm text-gray-500">{{ $notification->user->email }}</p>
                        <p class="text-sm text-gray-500">User Type: {{ ucfirst($notification->user->user_type) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Details -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Delivery Details</h3>
            </div>
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Delivery Method</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($notification->delivery_method) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $notification->status == 'sent' ? 'bg-green-100 text-green-800' : 
                                   ($notification->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                    'bg-red-100 text-red-800') }}">
                                {{ ucfirst($notification->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $notification->created_at->format('M d, Y H:i:s') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sent At</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $notification->sent_at ? $notification->sent_at->format('M d, Y H:i:s') : 'Not sent yet' }}
                        </dd>
                    </div>
                    @if($notification->read_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Read At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $notification->read_at->format('M d, Y H:i:s') }}</dd>
                    </div>
                    @endif
                    @if($notification->failed_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Failed At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $notification->failed_at->format('M d, Y H:i:s') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Error Details (if failed) -->
        @if($notification->status == 'failed' && $notification->error_message)
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 text-red-600">Error Details</h3>
            </div>
            <div class="px-6 py-4">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm text-red-700">{{ $notification->error_message }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Actions</h3>
            </div>
            <div class="px-6 py-4">
                <div class="flex space-x-4">
                    <a href="{{ route('admin.notifications.index') }}" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Back to List
                    </a>
                    @if($notification->status == 'failed')
                        <form method="POST" action="{{ route('admin.notifications.bulk-action') }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="resend">
                            <input type="hidden" name="notification_ids[]" value="{{ $notification->id }}">
                            <button type="submit" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                                    onclick="return confirm('Are you sure you want to resend this notification?')">
                                Resend Notification
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.notifications.bulk-action') }}" class="inline">
                        @csrf
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="notification_ids[]" value="{{ $notification->id }}">
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                                onclick="return confirm('Are you sure you want to delete this notification?')">
                            Delete Notification
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
