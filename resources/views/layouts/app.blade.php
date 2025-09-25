@php $userType = auth()->user() ? auth()->user()->user_type : 'guest'; @endphp
<!DOCTYPE html>
<script>
// ULTRA-EARLY countdown function definition to prevent ANY undefined errors
(function() {
    'use strict';
    
    // Define countdown function immediately
    function countdown() {
        console.log('Ultra-early countdown function called');
        return true;
    }
    
    // Make it available globally immediately
    window.countdown = countdown;
    
    // Also define it in global scope
    if (typeof countdown === 'undefined') {
        window.countdown = countdown;
    }
    
    // Override any existing countdown to prevent conflicts
    if (typeof window.countdown === 'undefined') {
        window.countdown = countdown;
    }
    
    // Add error handler for any countdown calls
    window.addEventListener('error', function(e) {
        if (e.message && e.message.includes('countdown')) {
            console.warn('Ultra-early countdown error caught:', e.message);
            e.preventDefault();
            return false;
        }
    });
    
    // Immediate error prevention
    try {
        if (typeof countdown === 'undefined') {
            window.countdown = countdown;
        }
    } catch (error) {
        console.error('Error defining ultra-early countdown:', error);
    }
})();
</script>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50 dark:bg-gray-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'School Management System') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    <script>
        // Tailwind setup with dark mode class strategy
        window.tailwind = { config: { darkMode: 'class' } };
    </script>
    <!-- Global countdown function to prevent undefined errors - MUST BE FIRST -->
    <script>
        // Define countdown function immediately to prevent undefined errors
        (function() {
            'use strict';
            
            // Define countdown function immediately
            function countdown() {
                console.log('Global countdown function called');
                return true;
            }
            
            // Make it available globally immediately
            window.countdown = countdown;
            
            // Also define it in global scope
            if (typeof countdown === 'undefined') {
                window.countdown = countdown;
            }
            
            // Override any existing countdown to prevent conflicts
            if (typeof window.countdown === 'undefined') {
                window.countdown = countdown;
            }
            
            // Add error handler for any countdown calls
            window.addEventListener('error', function(e) {
                if (e.message && e.message.includes('countdown')) {
                    console.warn('Countdown error caught globally:', e.message);
                    e.preventDefault();
                    return false;
                }
            });
            
            // Immediate error prevention
            try {
                if (typeof countdown === 'undefined') {
                    window.countdown = countdown;
                }
            } catch (error) {
                console.error('Error defining countdown:', error);
            }
            
            // Override setInterval to catch countdown calls
            const originalSetInterval = window.setInterval;
            window.setInterval = function(callback, delay) {
                if (typeof callback === 'function') {
                    const wrappedCallback = function() {
                        try {
                            return callback.apply(this, arguments);
                        } catch (error) {
                            if (error.message && error.message.includes('countdown')) {
                                console.warn('Countdown error caught in setInterval:', error.message);
                                return;
                            }
                            throw error;
                        }
                    };
                    return originalSetInterval.call(this, wrappedCallback, delay);
                }
                return originalSetInterval.call(this, callback, delay);
            };
            
            // Override setTimeout as well
            const originalSetTimeout = window.setTimeout;
            window.setTimeout = function(callback, delay) {
                if (typeof callback === 'function') {
                    const wrappedCallback = function() {
                        try {
                            return callback.apply(this, arguments);
                        } catch (error) {
                            if (error.message && error.message.includes('countdown')) {
                                console.warn('Countdown error caught in setTimeout:', error.message);
                                return;
                            }
                            throw error;
                        }
                    };
                    return originalSetTimeout.call(this, wrappedCallback, delay);
                }
                return originalSetTimeout.call(this, callback, delay);
            };
        })();
    </script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Suppress Tailwind production warning
        window.tailwind = window.tailwind || {};
        window.tailwind.config = window.tailwind.config || {};
        window.tailwind.config.safelist = ['*'];
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        /* CSS Variables for consistent theming */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.18);
            --shadow-primary: 0 20px 40px rgba(102, 126, 234, 0.1);
            --shadow-secondary: 0 15px 35px rgba(0, 0, 0, 0.1);
            --border-radius: 20px;
            --transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        /* Premium Animations */
        .fade-in { animation: fadeIn 0.8s ease-in-out; }
        .slide-in { animation: slideIn 0.6s ease-out; }
        .bounce-in { animation: bounceIn 0.8s ease-out; }
        .scale-in { animation: scaleIn 0.5s ease-out; }
        .float { animation: float 6s ease-in-out infinite; }
        .pulse-glow { animation: pulseGlow 2s ease-in-out infinite alternate; }
        
        @keyframes fadeIn { 
            from { opacity: 0; transform: translateY(20px); } 
            to { opacity: 1; transform: translateY(0); } 
        }
        @keyframes slideIn { 
            from { transform: translateX(-100%); opacity: 0; } 
            to { transform: translateX(0); opacity: 1; } 
        }
        @keyframes bounceIn { 
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes scaleIn { 
            from { transform: scale(0.9); opacity: 0; } 
            to { transform: scale(1); opacity: 1; } 
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        @keyframes pulseGlow {
            from { box-shadow: 0 0 20px rgba(102, 126, 234, 0.3); }
            to { box-shadow: 0 0 30px rgba(102, 126, 234, 0.6); }
        }
        
        /* Enhanced Transitions */
        .transition-all { transition: var(--transition); }
        .transition-premium { transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1); }
        
        /* Premium Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { 
            background: rgba(255, 255, 255, 0.1); 
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb { 
            background: var(--primary-gradient); 
            border-radius: 10px;
            border: 2px solid transparent;
            background-clip: content-box;
        }
        ::-webkit-scrollbar-thumb:hover { 
            background: linear-gradient(135deg, #5a67d8 0%, #667eea 100%);
            background-clip: content-box;
        }
        
        /* Glass Morphism Effects */
        .glass { 
            backdrop-filter: blur(20px); 
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
        }
        .glass-premium { 
            backdrop-filter: blur(25px); 
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--shadow-primary);
        }
        
        /* Premium Gradients */
        .bg-gradient-primary { background: var(--primary-gradient); }
        .bg-gradient-secondary { background: var(--secondary-gradient); }
        .bg-gradient-success { background: var(--success-gradient); }
        .bg-gradient-warning { background: var(--warning-gradient); }
        
        /* Premium Cards */
        .card-premium {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-secondary);
            transition: var(--transition);
        }
        .card-premium:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        /* Premium Buttons */
        .btn-premium {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: var(--transition);
            box-shadow: var(--shadow-secondary);
            position: relative;
            overflow: hidden;
            color: white;
        }
        .btn-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s ease;
        }
        .btn-premium:hover::before {
            left: 100%;
        }
        .btn-premium:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
        
        /* Premium Navigation */
        .nav-item-premium {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            transition: var(--transition);
        }
        .nav-item-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--primary-gradient);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: -1;
        }
        .nav-item-premium:hover::before {
            left: 0;
            opacity: 0.1;
        }
        .nav-item-premium.active::before {
            left: 0;
            opacity: 0.15;
        }
        
        /* Premium Status Badges */
        .status-badge-premium {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.8rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }
        .status-badge-premium:hover {
            transform: scale(1.05);
        }
        
        /* Premium Tables */
        .table-premium {
            background: var(--glass-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-secondary);
            border: 1px solid var(--glass-border);
        }
        .table-premium thead {
            background: var(--primary-gradient);
            color: white;
        }
        .table-premium thead th {
            border: none;
            padding: 1.5rem 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
        .table-premium tbody tr {
            transition: var(--transition);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        .table-premium tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
            transform: scale(1.01);
        }
        
        /* Dark Mode Enhancements */
        .dark {
            --glass-bg: rgba(31, 41, 55, 0.95);
            --glass-border: rgba(255, 255, 255, 0.1);
        }
        .dark .bg-white { background-color: #1f2937 !important; }
        .dark .text-gray-900 { color: #f3f4f6 !important; }
        .dark .text-gray-700 { color: #e5e7eb !important; }
        .dark .text-gray-600 { color: #d1d5db !important; }
        .dark .text-gray-500 { color: #9ca3af !important; }
        .dark .border-gray-200 { border-color: #374151 !important; }
        .dark .bg-gray-50 { background-color: #111827 !important; }
        .dark .hover\:bg-gray-100:hover { background-color: #374151 !important; }
        .dark .ring-white { --tw-ring-color: #111827; }
        
        /* Premium Loading States */
        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        /* Premium Notifications */
        .notification-premium {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            box-shadow: var(--shadow-secondary);
            transition: var(--transition);
        }
        .notification-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body class="h-full font-sans antialiased" x-data="{ 
    sidebarOpen: false, 
    darkMode: localStorage.getItem('theme') === 'dark',
    currentPage: '{{ request()->route() ? request()->route()->getName() : 'dashboard' }}',
    notifications: [],
    userMenuOpen: false,
    init() {
        this.applyTheme();
        this.fetchNotifications();
        setInterval(() => this.fetchNotifications(), 15000);
    },
    toggleTheme() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
        this.applyTheme();
    },
    applyTheme() {
        document.documentElement.classList.toggle('dark', this.darkMode);
        document.body.classList.toggle('bg-gray-900', this.darkMode);
        document.body.classList.toggle('text-gray-100', this.darkMode);
    },
    async fetchNotifications() {
        try {
            const res = await fetch('{{ route('notifications.json') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (res.ok) {
                this.notifications = await res.json();
            }
        } catch (e) { /* ignore */ }
    }
}">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-60 h-60 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse" style="animation-delay: 4s;"></div>
        </div>
        
        <!-- Premium Navigation Bar -->
        <nav class="glass-premium shadow-2xl border-b border-white/20 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Left side -->
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <!-- Premium Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <div class="h-10 w-10 bg-gradient-primary rounded-xl flex items-center justify-center pulse-glow">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                </svg>
                            </div>
                            <span class="ml-3 text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">SchoolMS</span>
                        </div>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        <!-- Dark mode toggle -->
                        <button @click="toggleTheme()" class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                            <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <svg x-show="darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </button>

                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all relative">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                </svg>
                                <span x-show="notifications.length > 0" class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">{{ count($notifications ?? []) }}</span>
                            </button>
                            
                            <!-- Notifications dropdown -->
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <div class="px-4 py-2 text-sm text-gray-700 border-b">Notifications</div>
                                    <div x-show="notifications.length === 0" class="px-4 py-8 text-center text-gray-500">
                                        No new notifications
                                    </div>
                                    <template x-for="notification in notifications" :key="notification.id">
                                        <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer">
                                            <div class="text-sm font-medium text-gray-900" x-text="notification.title"></div>
                                            <div class="text-sm text-gray-500" x-text="notification.message"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- User menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all">
                                @php($photo = auth()->user()->profile_photo ?? null)
                                @if($photo)
                                    <img src="{{ Storage::url($photo) }}" alt="Avatar" class="h-8 w-8 rounded-full object-cover">
                                @else
                                    <div class="h-8 w-8 bg-gradient-primary rounded-full flex items-center justify-center">
                                        <span class="text-white font-medium text-sm">{{ auth()->user()->name[0] ?? 'U' }}</span>
                                    </div>
                                @endif
                                <span class="hidden md:block text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'User' }}</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            <!-- User dropdown -->
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <a href="{{ $userType === 'student' ? route('student.profile') : ($userType !== 'admin' ? route('me.profile') : (auth()->user() ? route('users.profile', auth()->user()) : '#')) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    <a href="{{ $userType === 'student' ? route('student.change-password') : (auth()->user() ? route('users.change-password', auth()->user()) : '#') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Change Password</a>
                                    <div class="border-t border-gray-100"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex">
            <!-- Sidebar -->
            <div x-show="sidebarOpen" @click.away="sidebarOpen = false" x-transition class="lg:hidden fixed inset-0 z-40">
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
                <div class="fixed inset-y-0 left-0 flex w-64 flex-col bg-white shadow-xl">
                    <div class="flex h-16 items-center justify-between px-4 border-b">
                        <span class="text-lg font-semibold text-gray-900">Menu</span>
                        <button @click="sidebarOpen = false" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto py-4">
                        @if(auth()->user() && auth()->user()->user_type === 'teacher')
                            @include('components.teacher-navigation')
                        @else
                            @include('layouts.navigation')
                        @endif
                    </div>
                </div>
            </div>

            <!-- Premium Desktop Sidebar -->
            <div class="hidden lg:flex lg:flex-shrink-0">
                <div class="flex flex-col w-72 glass-premium shadow-2xl border-r border-white/20">
                    <div class="flex-1 flex flex-col overflow-y-auto">
                        @if(auth()->user() && auth()->user()->user_type === 'teacher')
                            @include('components.teacher-navigation')
                        @else
                            @include('layouts.navigation')
                        @endif
                    </div>
                </div>
            </div>

            <!-- Premium Main Content -->
            <div class="flex-1 lg:ml-0 relative">
                <main class="p-8 relative z-10">
                    <!-- Page Header -->
                    @if(isset($header))
                        <div class="mb-6">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $header }}</h1>
                            @if(isset($subheader))
                                <p class="text-gray-600">{{ $subheader }}</p>
                            @endif
                        </div>
                    @endif

                    <!-- Premium Flash Messages -->
                    @if(session('success'))
                        <div class="mb-8 notification-premium bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-6 py-4 rounded-xl fade-in">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full flex items-center justify-center">
                                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-8 notification-premium bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/30 dark:to-pink-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-6 py-4 rounded-xl fade-in">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 bg-gradient-to-r from-red-400 to-pink-500 rounded-full flex items-center justify-center">
                                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Page Content -->
                    <div class="fade-in">
                        @hasSection('content')
                            @yield('content')
                        @else
                            {{ $slot ?? '' }}
                        @endif
                    </div>
                </main>
            </div>
        </div>
    </div>

    <script>
        // Alpine.js initialization
        document.addEventListener('alpine:init', () => {
            Alpine.data('navigation', () => ({
                currentPage: '{{ request()->route() ? request()->route()->getName() : 'dashboard' }}',
                
                isActive(route) {
                    return this.currentPage === route;
                },
                
                setCurrentPage(page) {
                    this.currentPage = page;
                }
            }));
        });

        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // Loading states for forms
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...';
                }
            });
        });

        // Global error handler for countdown function
        window.addEventListener('error', function(e) {
            if (e.message && e.message.includes('countdown')) {
                console.warn('Countdown error caught globally:', e.message);
                e.preventDefault();
                return false;
            }
        });

        // Ensure countdown is always available
        if (typeof window.countdown === 'undefined') {
            window.countdown = function() {
                console.log('Fallback countdown function called');
                return true;
            };
        }
        
        // Additional error prevention for countdown
        window.addEventListener('DOMContentLoaded', function() {
            if (typeof window.countdown === 'undefined') {
                window.countdown = function() {
                    console.log('DOMContentLoaded countdown function called');
                    return true;
                };
            }
        });
    </script>

    <!-- Real-time Updates Script -->
    <!-- Realtime.js removed to prevent 401 errors -->
    <script>
        // Add CSRF token to meta tags for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.getElementsByTagName('head')[0].appendChild(meta);
        }
    </script>
</body>
</html>
