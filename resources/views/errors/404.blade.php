<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found - {{ config('app.name', 'School Management System') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Countdown function definition - MUST BE FIRST -->
    <script>
        // Define countdown function immediately to prevent undefined errors
        (function() {
            'use strict';
            
            // Define countdown function immediately
            function countdown() {
                console.log('404 page countdown function called');
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
                    console.warn('Countdown error caught in 404 page:', e.message);
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
                console.error('Error defining countdown in 404 page:', error);
            }
        })();
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Custom animations */
        .fade-in-up { animation: fadeInUp 1s ease-out; }
        .bounce-in { animation: bounceIn 1.2s ease-out; }
        .float { animation: float 6s ease-in-out infinite; }
        .pulse-slow { animation: pulse 3s infinite; }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        /* Gradient text */
        .gradient-text { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Glassmorphism effect */
        .glass { 
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
    </style>
</head>
<body class="h-full font-sans antialiased bg-gradient-to-br from-blue-50 via-white to-indigo-50" x-data="{ 
    countdown: 10,
    showConfetti: false
}">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <!-- Floating shapes -->
            <div class="absolute top-20 left-20 w-32 h-32 bg-blue-200 rounded-full opacity-20 float"></div>
            <div class="absolute top-40 right-20 w-24 h-24 bg-purple-200 rounded-full opacity-20 float" style="animation-delay: -2s;"></div>
            <div class="absolute bottom-20 left-40 w-28 h-28 bg-indigo-200 rounded-full opacity-20 float" style="animation-delay: -4s;"></div>
            <div class="absolute bottom-40 right-40 w-20 h-20 bg-pink-200 rounded-full opacity-20 float" style="animation-delay: -1s;"></div>
            
            <!-- Grid pattern -->
            <div class="absolute inset-0 opacity-5">
                <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, #667eea 1px, transparent 0); background-size: 50px 50px;"></div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-2xl w-full text-center relative z-10 space-y-8">
            <!-- 404 Number -->
            <div class="fade-in-up">
                <h1 class="text-9xl font-bold gradient-text mb-4 bounce-in">404</h1>
                <div class="w-32 h-1 bg-gradient-to-r from-blue-500 to-purple-600 mx-auto rounded-full"></div>
            </div>

            <!-- Error Message -->
            <div class="glass rounded-3xl p-8 shadow-2xl border border-white/20 fade-in-up" style="animation-delay: 0.3s;">
                <div class="w-24 h-24 bg-gradient-to-r from-red-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Oops! Page Not Found</h2>
                <p class="text-lg text-gray-600 mb-6">The page you're looking for doesn't exist or has been moved.</p>
                
                <!-- Search Box -->
                <div class="max-w-md mx-auto mb-6">
                    <div class="relative">
                        <input 
                            type="text" 
                            placeholder="Search for pages..." 
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                        </svg>
                        Go to Dashboard
                    </a>
                    
                    <button @click="history.back()" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Go Back
                    </button>
                </div>
            </div>

            <!-- Helpful Links -->
            <div class="glass rounded-2xl p-6 shadow-xl border border-white/20 fade-in-up" style="animation-delay: 0.6s;">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Popular Pages</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @php
                        $studentsRoute = null;
                        if (auth()->check()) {
                            try {
                                $studentsRoute = auth()->user()->user_type === 'student' ? 'student.dashboard' : 'students.index';
                                $studentsUrl = route($studentsRoute);
                            } catch (Exception $e) {
                                $studentsRoute = null;
                            }
                        }
                    @endphp
                    @if($studentsRoute)
                    <a href="{{ $studentsUrl }}" class="group p-3 rounded-xl border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 hover:scale-105">
                    @else
                    <a href="{{ route('login') }}" class="group p-3 rounded-xl border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 hover:scale-105">
                    @endif
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-900 group-hover:text-blue-700">Students</p>
                    </a>

                    @php
                        $teachersRoute = null;
                        if (auth()->check()) {
                            try {
                                $teachersRoute = auth()->user()->user_type === 'student' ? 'student.teachers.index' : 'teachers.index';
                                $teachersUrl = route($teachersRoute);
                            } catch (Exception $e) {
                                $teachersRoute = null;
                            }
                        }
                    @endphp
                    @if($teachersRoute)
                    <a href="{{ $teachersUrl }}" class="group p-3 rounded-xl border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-all duration-200 hover:scale-105">
                    @else
                    <a href="{{ route('login') }}" class="group p-3 rounded-xl border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-all duration-200 hover:scale-105">
                    @endif
                        <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-900 group-hover:text-purple-700">Teachers</p>
                    </a>

                    <a href="{{ route('classes.index') }}" class="group p-3 rounded-xl border border-gray-200 hover:border-yellow-300 hover:bg-yellow-50 transition-all duration-200 hover:scale-105">
                        <div class="w-8 h-8 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-900 group-hover:text-yellow-700">Classes</p>
                    </a>

                    <a href="{{ route('exams.types.index') }}" class="group p-3 rounded-xl border border-gray-200 hover:border-red-300 hover:bg-red-50 transition-all duration-200 hover:scale-105">
                        <div class="w-8 h-8 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-900 group-hover:text-red-700">Exams</p>
                    </a>
                </div>
            </div>

            <!-- Auto-redirect Notice -->
            <div class="text-center fade-in-up" style="animation-delay: 0.9s;">
                <p class="text-sm text-gray-500">
                    Redirecting to dashboard in <span x-text="countdown" class="font-semibold text-blue-600"></span> seconds...
                </p>
            </div>
        </div>
    </div>

    <script>
        // Countdown timer
        const timer = setInterval(() => {
            if (countdown > 0) {
                countdown--;
            } else {
                clearInterval(timer);
                window.location.href = "{{ route('dashboard') }}";
            }
        }, 1000);

        // Add entrance animations
        document.addEventListener('DOMContentLoaded', () => {
            // Stagger animation for elements
            const elements = document.querySelectorAll('.fade-in-up');
            elements.forEach((element, index) => {
                element.style.animationDelay = `${0.3 * index}s`;
            });
        });

        // Add hover effects
        document.querySelectorAll('a, button').forEach(element => {
            element.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.05)';
            });
            
            element.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>
