@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
                    <p class="text-gray-600 mt-2">Stay updated with important announcements</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        @if($notifications && $notifications->count() > 0)
            <div class="space-y-4">
                @foreach($notifications as $notification)
                    <div class="bg-white shadow rounded-lg p-6 {{ $notification->read_at ? 'opacity-75' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    @if(!$notification->read_at)
                                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    @endif
                                    <h3 class="text-lg font-medium text-gray-900">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </h3>
                                </div>
                                
                                @if(isset($notification->data['message']))
                                    <p class="text-gray-600 mt-2">{{ Str::limit($notification->data['message'], 150) }}</p>
                                @endif
                                
                                <div class="flex items-center space-x-4 mt-4 text-sm text-gray-500">
                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                    @if($notification->data['type'] ?? false)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($notification->data['type']) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="ml-6">
                                <a href="{{ route('student.notifications.show', $notification->id) }}" 
                                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="mt-8">
                    {{ $notifications->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="max-w-md mx-auto">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12 7M4.828 7H3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-1.828M4.828 7l2.586-2.586a2 2 0 012.828 0L12 7m8 0v10a2 2 0 01-2 2H7" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                    <p class="mt-1 text-sm text-gray-500">You're all caught up! No new notifications at the moment.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
