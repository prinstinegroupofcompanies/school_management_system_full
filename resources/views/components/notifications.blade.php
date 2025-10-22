<!-- Real-time Notifications Component -->
<div id="notification-container" class="fixed top-4 right-4 z-50 max-w-sm w-full">
    <!-- Notification Bell Icon -->
    <div class="relative">
        <button id="notification-bell" 
                class="bg-white rounded-full p-3 shadow-lg hover:shadow-xl transition-shadow relative">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M15 17h5l-5 5v-5zM9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            <span id="notification-badge" 
                  class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">
                <span id="notification-count">0</span>
            </span>
        </button>
    </div>

    <!-- Notifications Dropdown -->
    <div id="notification-dropdown" 
         class="absolute right-0 top-12 bg-white rounded-lg shadow-xl border border-gray-200 w-80 max-h-96 overflow-y-auto hidden">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                <button id="mark-all-read" 
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Mark all read
                </button>
            </div>
        </div>
        
        <div id="notifications-list" class="divide-y divide-gray-200">
            <!-- Notifications will be loaded here -->
        </div>
        
        <div class="p-4 border-t border-gray-200">
            <a href="#" 
               class="text-center text-sm text-blue-600 hover:text-blue-800 font-medium block">
                View all notifications
            </a>
        </div>
    </div>
</div>

<script>
class NotificationManager {
    constructor() {
        this.bell = document.getElementById('notification-bell');
        this.badge = document.getElementById('notification-badge');
        this.count = document.getElementById('notification-count');
        this.dropdown = document.getElementById('notification-dropdown');
        this.list = document.getElementById('notifications-list');
        this.markAllRead = document.getElementById('mark-all-read');
        this.isOpen = false;
        
        this.init();
    }
    
    init() {
        // Toggle dropdown
        this.bell.addEventListener('click', () => this.toggleDropdown());
        
        // Mark all as read
        this.markAllRead.addEventListener('click', () => this.markAllAsRead());
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#notification-container')) {
                this.closeDropdown();
            }
        });
        
        // Load initial notifications
        this.loadNotifications();
        
        // Set up polling for new notifications
        setInterval(() => this.checkForNewNotifications(), 30000); // Check every 30 seconds
    }
    
    toggleDropdown() {
        if (this.isOpen) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }
    
    openDropdown() {
        this.dropdown.classList.remove('hidden');
        this.isOpen = true;
        this.loadNotifications();
    }
    
    closeDropdown() {
        this.dropdown.classList.add('hidden');
        this.isOpen = false;
    }
    
    async loadNotifications() {
        try {
            const response = await fetch('{{ route("notifications.unread") }}');
            const data = await response.json();
            
            this.updateNotificationCount(data.count);
            this.renderNotifications(data.notifications);
        } catch (error) {
            console.error('Failed to load notifications:', error);
        }
    }
    
    async checkForNewNotifications() {
        try {
            const response = await fetch('{{ route("notifications.count") }}');
            const data = await response.json();
            
            this.updateNotificationCount(data.count);
        } catch (error) {
            console.error('Failed to check notification count:', error);
        }
    }
    
    updateNotificationCount(count) {
        if (count > 0) {
            this.badge.classList.remove('hidden');
            this.count.textContent = count > 99 ? '99+' : count;
        } else {
            this.badge.classList.add('hidden');
        }
    }
    
    renderNotifications(notifications) {
        if (notifications.length === 0) {
            this.list.innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <p>No new notifications</p>
                </div>
            `;
            return;
        }
        
        this.list.innerHTML = notifications.map(notification => `
            <div class="p-4 hover:bg-gray-50 cursor-pointer notification-item" 
                 data-id="${notification.id}">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center
                            ${this.getNotificationIconColor(notification.type)}">
                            ${this.getNotificationIcon(notification.type)}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                        <p class="text-sm text-gray-500 mt-1">${notification.message}</p>
                        <p class="text-xs text-gray-400 mt-2">${this.formatTimeAgo(notification.created_at)}</p>
                    </div>
                    ${notification.action_url ? `
                        <div class="flex-shrink-0">
                            <a href="${notification.action_url}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                ${notification.action_text || 'View'}
                            </a>
                        </div>
                    ` : ''}
                </div>
            </div>
        `).join('');
        
        // Add click handlers for notifications
        this.list.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', () => this.markAsRead(item.dataset.id));
        });
    }
    
    getNotificationIcon(type) {
        const icons = {
            'exam_submission': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
            'exam_graded': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'homework_assigned': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 19 16.5 19c-1.746 0-3.332-.477-4.5-1.253"></path></svg>',
            'attendance': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'default': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>'
        };
        return icons[type] || icons.default;
    }
    
    getNotificationIconColor(type) {
        const colors = {
            'exam_submission': 'bg-blue-100 text-blue-600',
            'exam_graded': 'bg-green-100 text-green-600',
            'homework_assigned': 'bg-yellow-100 text-yellow-600',
            'attendance': 'bg-purple-100 text-purple-600',
            'default': 'bg-gray-100 text-gray-600'
        };
        return colors[type] || colors.default;
    }
    
    formatTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) {
            return 'Just now';
        } else if (diffInSeconds < 3600) {
            const minutes = Math.floor(diffInSeconds / 60);
            return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
        } else if (diffInSeconds < 86400) {
            const hours = Math.floor(diffInSeconds / 3600);
            return `${hours} hour${hours > 1 ? 's' : ''} ago`;
        } else {
            const days = Math.floor(diffInSeconds / 86400);
            return `${days} day${days > 1 ? 's' : ''} ago`;
        }
    }
    
    async markAsRead(notificationId) {
        try {
            const response = await fetch(`{{ route('notifications.mark-read', '') }}/${notificationId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                // Remove the notification from the list
                const item = this.list.querySelector(`[data-id="${notificationId}"]`);
                if (item) {
                    item.remove();
                }
                
                // Update count
                this.checkForNewNotifications();
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    }
    
    async markAllAsRead() {
        try {
            const response = await fetch('{{ route("notifications.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                this.badge.classList.add('hidden');
                this.list.innerHTML = `
                    <div class="p-4 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <p>All notifications marked as read</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Failed to mark all notifications as read:', error);
        }
    }
}

// Initialize notification manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new NotificationManager();
});
</script>
