@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
                <p class="text-gray-600 mt-2">Stay updated with your latest notifications</p>
            </div>
            <div class="flex items-center space-x-4">
                @if($unreadCount > 0)
                <span class="bg-red-100 text-red-800 text-sm font-medium px-2.5 py-0.5 rounded-full">
                    {{ $unreadCount }} unread
                </span>
                @endif
                <button onclick="markAllAsRead()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Mark All as Read
                </button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-white rounded-lg shadow-lg">
            @forelse($notifications as $notification)
            <div class="border-b border-gray-200 last:border-b-0 {{ $notification->read_at ? 'bg-gray-50' : 'bg-white' }}">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $notification->title }}</h3>
                                @if(!$notification->read_at)
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full">New</span>
                                @endif
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-1 rounded-full">
                                    {{ ucfirst($notification->category) }}
                                </span>
                            </div>
                            <p class="text-gray-700 mb-3">{{ $notification->message }}</p>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-clock mr-2"></i>
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            @if($notification->action_url)
                            <a href="{{ $notification->action_url }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                {{ $notification->action_text ?? 'View' }}
                            </a>
                            @endif
                            @if(!$notification->read_at)
                            <button onclick="markAsRead({{ $notification->id }})" 
                                    class="text-gray-500 hover:text-gray-700 transition-colors">
                                <i class="fas fa-check"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-bell-slash"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Notifications</h3>
                <p class="text-gray-600">You're all caught up! No new notifications at the moment.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function markAllAsRead() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection
