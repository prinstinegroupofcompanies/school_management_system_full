@php 
$userType = auth()->user()->user_type ?? 'guest'; 

// Helper function to safely generate routes (only declare once)
if (!function_exists('safeRoute')) {
    function safeRoute($name, $parameters = [], $fallback = '#') {
        try {
            if (Route::has($name)) {
                return route($name, $parameters);
            }
        } catch (\Exception $e) {
            // Log the error but don't break the page
            \Log::warning("Navigation route error: {$name}", ['error' => $e->getMessage()]);
        }
        return $fallback;
    }
}
@endphp

<nav class="flex-1 px-6 py-6 space-y-3">
    <!-- Premium Dashboard -->
    <a href="{{ $userType === 'student' ? route('student.dashboard') : route('dashboard') }}" 
       class="nav-item-premium group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-premium {{ (request()->routeIs('dashboard') || request()->routeIs('student.dashboard')) ? 'bg-gradient-to-r from-blue-100 to-purple-100 text-blue-700 shadow-lg' : 'text-gray-700 hover:bg-white/50 hover:text-gray-900' }}">
        <svg class="mr-3 h-5 w-5 {{ (request()->routeIs('dashboard') || request()->routeIs('student.dashboard')) ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
        </svg>
        Dashboard
    </a>

    <!-- Premium Students Section -->
    @if($userType !== 'student' && $userType !== 'finance')
    <div x-data="{ open: {{ request()->routeIs('students.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="nav-item-premium group w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-xl transition-premium {{ request()->routeIs('students.*') ? 'bg-gradient-to-r from-blue-100 to-purple-100 text-blue-700 shadow-lg' : 'text-gray-700 hover:bg-white/50 hover:text-gray-900' }}">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('students.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
                Students
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-6 space-y-2 mt-2">
            <a href="{{ route('admin.students.index') }}" class="group flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-premium {{ request()->routeIs('admin.students.index') ? 'bg-gradient-to-r from-blue-50 to-purple-50 text-blue-600 shadow-md' : 'text-gray-600 hover:bg-white/30 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                All Students
            </a>
            <a href="{{ route('admin.students.create') }}" class="group flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-premium {{ request()->routeIs('admin.students.create') ? 'bg-gradient-to-r from-blue-50 to-purple-50 text-blue-600 shadow-md' : 'text-gray-600 hover:bg-white/30 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Student
            </a>
        </div>
    </div>
    @endif

    {{-- Gradesheet and Exams for Student --}}
    @if($userType === 'student')
    @if(Route::has('student.grades.grade-sheet'))
    <a href="{{ route('student.grades.grade-sheet') }}" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('student.grades.grade-sheet') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('student.grades.grade-sheet') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-9 4h12M4 6h16" />
        </svg>
        My Gradesheet
    </a>
    @endif
    <div x-data="{ open: {{ request()->routeIs('student.finance.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="nav-item-premium group w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-xl transition-premium {{ request()->routeIs('student.finance.*') ? 'bg-gradient-to-r from-blue-100 to-purple-100 text-blue-700 shadow-lg' : 'text-gray-700 hover:bg-white/50 hover:text-gray-900' }}">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('student.finance.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                Fees & Payments
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-6 space-y-2 mt-2">
            <a href="{{ route('student.finance.index') }}" class="group flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-premium {{ request()->routeIs('student.finance.index') ? 'bg-gradient-to-r from-blue-50 to-purple-50 text-blue-600 shadow-md' : 'text-gray-600 hover:bg-white/30 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-9 4h12M4 6h16" />
                </svg>
                My Finances
            </a>
        </div>
    </div>
    <a href="{{ $userType === 'student' ? route('student.exams.upcoming') : route('exams.upcoming') }}" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ (request()->is('exams/upcoming') || request()->is('student/exams/upcoming')) ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
        <svg class="mr-3 h-5 w-5 {{ (request()->is('exams/upcoming') || request()->is('student/exams/upcoming')) ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
        </svg>
        Upcoming Exams
    </a>
    @endif

    @if($userType === 'admin')
    <!-- Staff Management -->
    <div x-data="{ open: {{ request()->routeIs('admin.staff.*') ? 'true' : 'false' }} }">
        <button @click="open = !open"
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.staff.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.staff.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Staff Management
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="{{ route('admin.staff.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.staff.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                All Staff
            </a>
            <a href="{{ route('admin.staff.performance') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.staff.performance') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Performance
            </a>
            <a href="{{ route('admin.staff.schedules') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.staff.schedules') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z" />
                </svg>
                Schedules
            </a>
            <a href="{{ route('admin.staff.payroll') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.staff.payroll') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                Payroll
            </a>
        </div>
    </div>

    <!-- Notifications & Alerts -->
    <div x-data="{ open: {{ request()->routeIs('admin.notifications.*') ? 'true' : 'false' }} }">
        <button @click="open = !open"
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.notifications.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.notifications.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12 7M4.828 7H3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-1.828M4.828 7l2.586-2.586a2 2 0 012.828 0L12 7m8 0v10a2 2 0 01-2 2H7m5-5h5m-5 0l-2.5-2.5M12 12l2.5 2.5" />
                </svg>
                Notifications
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="{{ route('admin.notifications.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.notifications.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                All Notifications
            </a>
            <a href="{{ route('admin.notifications.create') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.notifications.create') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Send Notification
            </a>
            <a href="{{ route('admin.notifications.templates') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.notifications.templates') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Templates
            </a>
        </div>
    </div>

    <!-- Reports & Analytics -->
    <div x-data="{ open: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }">
        <button @click="open = !open"
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.reports.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Reports
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="{{ route('admin.reports.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.reports.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Dashboard
            </a>
            <a href="{{ route('admin.reports.academic') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.reports.academic') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332-.477-4.5-1.253" />
                </svg>
                Academic
            </a>
            <a href="{{ route('admin.reports.financial') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.reports.financial') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                Financial
            </a>
            <a href="{{ route('admin.reports.attendance') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.reports.attendance') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Attendance
            </a>
        </div>
    </div>

    <!-- Finance Management -->
    <div x-data="{ open: {{ request()->routeIs('admin.finance_officers.*') || request()->routeIs('admin.fees.reports') ? 'true' : 'false' }} }">
        <button @click="open = !open"
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all {{ (request()->routeIs('admin.finance_officers.*') || request()->routeIs('admin.fees.reports')) ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 {{ (request()->routeIs('admin.finance_officers.*') || request()->routeIs('admin.fees.reports')) ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                Finance
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="{{ route('admin.finance_officers.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.finance_officers.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Finance Officers
            </a>
            <a href="{{ route('admin.fees.reports') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.fees.reports') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6h6v6m-7 4h8a2 2 0 002-2V7a2 2 0 00-2-2H9l-2 2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Finance Reports
            </a>
        </div>
    </div>
    @endif

    {{-- Grades for Admin (Approvals) --}}
    @if($userType === 'admin')
    <a href="{{ route('admin.grades.index') }}" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.grades.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.grades.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-9 4h12M4 6h16" />
        </svg>
        Grades Approvals
    </a>
    @endif

    <!-- Teachers (hidden for students) -->
    @if($userType !== 'student' && $userType !== 'finance')
    <div x-data="{ open: {{ request()->routeIs('admin.teachers.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.teachers.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.teachers.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Teachers
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="{{ route('admin.teachers.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.teachers.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                All Teachers
            </a>
            <a href="{{ route('admin.teachers.create') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.teachers.create') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Teacher
            </a>
        </div>
    </div>
    @endif

    {{-- Grades for Teacher --}}
    @if($userType === 'teacher')
    <div x-data="{ open: {{ request()->routeIs('teacher.grades.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('teacher.grades.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('teacher.grades.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-9 4h12M4 6h16" />
                </svg>
                Grade Management
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="{{ route('teacher.grades.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('teacher.grades.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                View Grades
            </a>
            <a href="{{ route('teacher.grades.create') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('teacher.grades.create') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Grade
            </a>
            <a href="{{ route('teacher.grades.bulk-create') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('teacher.grades.bulk-create') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Bulk Entry
            </a>
        </div>
    </div>
    @endif

    {{-- Parent Navigation --}}
    @if($userType === 'parent')
    <div x-data="{ open: {{ request()->routeIs('parent.grades.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('parent.grades.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('parent.grades.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-9 4h12M4 6h16" />
                </svg>
                Children's Grades
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="{{ route('parent.grades.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('parent.grades.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                All Grades
            </a>
            @if(Route::has('parent.grades.progress'))
            <a href="{{ route('parent.grades.progress') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('parent.grades.progress') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Academic Progress
            </a>
            @endif
            @if(Route::has('parent.grades.download'))
            <a href="{{ route('parent.grades.download') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('parent.grades.download') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Download Reports
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Classes (hidden for students) -->
    @if($userType !== 'student' && $userType !== 'finance')
    <div x-data="{ open: {{ request()->routeIs('classes.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('classes.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('classes.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Classes
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="{{ route('admin.classes.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.classes.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                All Classes
            </a>
            <a href="{{ route('admin.classes.create') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.classes.create') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Class
            </a>
        </div>
    </div>
    @endif

    <!-- Subjects (hidden for students) -->
    @if($userType !== 'student' && $userType !== 'finance')
    <div x-data="{ open: {{ request()->routeIs('subjects.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('subjects.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('subjects.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Subjects
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="{{ route('admin.subjects.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.subjects.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                All Subjects
            </a>
            <a href="{{ route('admin.subjects.create') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.subjects.create') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Subject
            </a>
        </div>
    </div>
    @endif

    

    <!-- Fees (hidden for students) -->
    @if($userType !== 'student' && $userType !== 'finance')
    <div x-data="{ open: {{ request()->routeIs('admin.fee-structures.*') || request()->routeIs('finance.payments.*') ? 'true' : 'false' }} }">
        <button @click="open = !open"
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.fee-structures.*') || request()->routeIs('finance.payments.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.fee-structures.*') || request()->routeIs('finance.payments.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                Fees
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="{{ route('admin.fee-structures.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('admin.fee-structures.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Fee Structures
            </a>
            <a href="{{ route('finance.payments.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('finance.payments.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Payments
            </a>
        </div>
    </div>
    @endif

    @if($userType !== 'finance')
    <!-- Library -->
    <a href="{{ $userType === 'student' ? route('student.library.index') : route('library.index') }}" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ (request()->routeIs('library.*') || request()->routeIs('student.library.*')) ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
        <svg class="mr-3 h-5 w-5 {{ (request()->routeIs('library.*') || request()->routeIs('student.library.*')) ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        Library
    </a>
    @endif

    @if($userType !== 'finance')
    <!-- Transport -->
    <a href="{{ $userType === 'student' ? route('student.transport.index') : route('transport.index') }}" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ (request()->routeIs('transport.*') || request()->routeIs('student.transport.*')) ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
        <svg class="mr-3 h-5 w-5 {{ (request()->routeIs('transport.*') || request()->routeIs('student.transport.*')) ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
        </svg>
        Transport
    </a>
    @endif

    @if($userType !== 'finance')
    <!-- Hostel -->
    <a href="{{ $userType === 'student' ? route('student.hostel.index') : route('hostel.index') }}" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ (request()->routeIs('hostel.*') || request()->routeIs('student.hostel.*')) ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
        <svg class="mr-3 h-5 w-5 {{ (request()->routeIs('hostel.*') || request()->routeIs('student.hostel.*')) ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        Hostel
    </a>
    @endif

    @if($userType !== 'finance')
    <!-- Attendance -->
    <a href="{{ $userType === 'student' ? route('student.attendance.index') : route('attendance.index') }}" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ (request()->routeIs('attendance.*') || request()->routeIs('student.attendance.*')) ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
        <svg class="mr-3 h-5 w-5 {{ (request()->routeIs('attendance.*') || request()->routeIs('student.attendance.*')) ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Attendance
    </a>
    @endif

    <!-- Users (hidden for students) -->
    @if($userType !== 'student' && $userType !== 'finance')
    <a href="{{ route('users.index') }}" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('users.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('users.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
        </svg>
        Users
    </a>
    @endif

    <!-- Settings -->
    <a href="{{ $userType === 'student' ? route('student.settings.index') : route('settings.index') }}" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ (request()->routeIs('settings.*') || request()->routeIs('student.settings.*')) ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
        <svg class="mr-3 h-5 w-5 {{ (request()->routeIs('settings.*') || request()->routeIs('student.settings.*')) ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        Settings
    </a>

    <!-- Help Section -->
    <div class="pt-6 mt-6 border-t border-gray-200">
        <div class="px-3 py-3 flex items-center space-x-3">
            @php($photo = auth()->user()->profile_photo ?? null)
            @if($photo)
                <img src="{{ Storage::url($photo) }}" alt="Avatar" class="h-8 w-8 rounded-full object-cover">
            @else
                <div class="h-8 w-8 bg-gradient-primary rounded-full flex items-center justify-center">
                    <span class="text-white font-medium text-sm">{{ auth()->user()->name[0] ?? 'U' }}</span>
                </div>
            @endif
            <div class="min-w-0">
                <div class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name ?? 'User' }}</div>
                <div class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? '' }}</div>
            </div>
        </div>
        <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
            Help & Support
        </div>
        <a href="#" class="group flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-900 transition-all">
            <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Help Center
        </a>
        <a href="#" class="group flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-900 transition-all">
            <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Contact Support
        </a>
    </div>
</nav>
