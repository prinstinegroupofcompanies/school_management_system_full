<nav class="flex-1 px-4 py-4 space-y-2">
    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('dashboard') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
        </svg>
        Dashboard
    </a>

    <!-- My Students -->
    <a href="{{ route('teacher.students.index') }}" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('teacher.students*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('teacher.students*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
        </svg>
        My Students
    </a>

    <!-- My Subjects -->
    <a href="{{ route('teacher.subjects.index') }}" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('teacher.subjects*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('teacher.subjects*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        My Subjects
    </a>

    <!-- My Classes -->
    <a href="{{ route('teacher.classes.index') }}" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('teacher.classes*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('teacher.classes*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        My Classes
    </a>

    <!-- Exams -->
    <div x-data="{ open: {{ request()->routeIs('teacher.exams.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('teacher.exams.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('teacher.exams.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M4 6h16" />
                </svg>
                Exams
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="{{ route('teacher.exams.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('teacher.exams.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M4 6h16" />
                </svg>
                My Exams
            </a>
            <a href="{{ route('teacher.exams.create') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('teacher.exams.create') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Exam
            </a>
            <a href="{{ route('teacher.exams.upcoming') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('teacher.exams.upcoming') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                </svg>
                Upcoming Exams
            </a>
        </div>
    </div>

    <!-- Grades -->
    <div x-data="{ open: {{ request()->routeIs('teacher.grades.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('teacher.grades.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('teacher.grades.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M4 6h16" />
                </svg>
                Grades
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="{{ route('teacher.grades.create') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('teacher.grades.create') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Grades
            </a>
            <a href="{{ route('teacher.grades.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('teacher.grades.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                My Grades
            </a>
        </div>
    </div>

    <!-- Homework -->
    <div x-data="{ open: {{ request()->routeIs('homework.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('homework.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('homework.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Homework
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="{{ route('homework.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('homework.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                All Homework
            </a>
            <a href="{{ route('homework.create') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('homework.create') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create Homework
            </a>
        </div>
    </div>

    <!-- Study Materials -->
    <div x-data="{ open: {{ request()->routeIs('study-materials.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('study-materials.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('study-materials.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Study Materials
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="{{ route('study-materials.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('study-materials.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                All Materials
            </a>
            <a href="{{ route('study-materials.create') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('study-materials.create') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Upload Material
            </a>
        </div>
    </div>

    <!-- Attendance -->
    <a href="{{ route('attendance.index') }}" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('attendance.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('attendance.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Attendance
    </a>

    <!-- Profile -->
    <a href="{{ route('teacher.profile') }}" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('teacher.profile*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('teacher.profile*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        My Profile
    </a>

    <!-- Help Section -->
    <div class="pt-6 mt-6 border-t border-gray-200">
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
