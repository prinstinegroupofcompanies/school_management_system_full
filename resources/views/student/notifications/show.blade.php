@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $notification->data['title'] ?? 'Notification' }}</h1>
                    <p class="text-gray-600 mt-2">{{ $notification->created_at->format('F j, Y g:i A') }}</p>
                </div>
                <a href="{{ route('student.notifications.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Notifications
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-6">
                <!-- Notification Content -->
                <div class="prose prose-lg max-w-none">
                    @if(isset($notification->data['message']))
                        {!! nl2br(e($notification->data['message'])) !!}
                    @else
                        <p>No message content available.</p>
                    @endif
                </div>

                <!-- Notification Meta -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($notification->data['type'] ?? 'General') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Received</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $notification->created_at->format('F j, Y g:i A') }}</dd>
                        </div>
                        @if($notification->read_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Read</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $notification->read_at->format('F j, Y g:i A') }}</dd>
                            </div>
                        @endif
                        @if(isset($notification->data['priority']))
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Priority</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $notification->data['priority'] === 'high' ? 'bg-red-100 text-red-800' : 
                                           ($notification->data['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ ucfirst($notification->data['priority']) }}
                                    </span>
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
