<?php $userType = auth()->user()->user_type ?? 'guest'; ?>

<nav class="flex-1 px-6 py-6 space-y-3">
    <!-- Premium Dashboard -->
    <a href="<?php echo e($userType === 'student' ? route('student.dashboard') : route('dashboard')); ?>" 
       class="nav-item-premium group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-premium <?php echo e((request()->routeIs('dashboard') || request()->routeIs('student.dashboard')) ? 'bg-gradient-to-r from-blue-100 to-purple-100 text-blue-700 shadow-lg' : 'text-gray-700 hover:bg-white/50 hover:text-gray-900'); ?>">
        <svg class="mr-3 h-5 w-5 <?php echo e((request()->routeIs('dashboard') || request()->routeIs('student.dashboard')) ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
        </svg>
        Dashboard
    </a>

    <!-- Premium Students Section -->
    <?php if($userType !== 'student' && $userType !== 'finance'): ?>
    <div x-data="{ open: <?php echo e(request()->routeIs('students.*') ? 'true' : 'false'); ?> }">
        <button @click="open = !open" 
                class="nav-item-premium group w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-xl transition-premium <?php echo e(request()->routeIs('students.*') ? 'bg-gradient-to-r from-blue-100 to-purple-100 text-blue-700 shadow-lg' : 'text-gray-700 hover:bg-white/50 hover:text-gray-900'); ?>">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 <?php echo e(request()->routeIs('students.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
                Students
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-6 space-y-2 mt-2">
            <a href="<?php echo e(route('students.index')); ?>" class="group flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-premium <?php echo e(request()->routeIs('students.index') ? 'bg-gradient-to-r from-blue-50 to-purple-50 text-blue-600 shadow-md' : 'text-gray-600 hover:bg-white/30 hover:text-gray-900'); ?>">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                All Students
            </a>
            <a href="<?php echo e(route('students.create')); ?>" class="group flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-premium <?php echo e(request()->routeIs('students.create') ? 'bg-gradient-to-r from-blue-50 to-purple-50 text-blue-600 shadow-md' : 'text-gray-600 hover:bg-white/30 hover:text-gray-900'); ?>">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Student
            </a>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if($userType === 'student'): ?>
    <a href="<?php echo e(route('student.gradesheet.show')); ?>" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('student.gradesheet.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'); ?>">
        <svg class="mr-3 h-5 w-5 <?php echo e(request()->routeIs('student.gradesheet.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-9 4h12M4 6h16" />
        </svg>
        My Gradesheet
    </a>
    <div x-data="{ open: <?php echo e(request()->routeIs('student.finance.*') ? 'true' : 'false'); ?> }">
        <button @click="open = !open" 
                class="nav-item-premium group w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-xl transition-premium <?php echo e(request()->routeIs('student.finance.*') ? 'bg-gradient-to-r from-blue-100 to-purple-100 text-blue-700 shadow-lg' : 'text-gray-700 hover:bg-white/50 hover:text-gray-900'); ?>">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 <?php echo e(request()->routeIs('student.finance.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                Fees & Payments
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-6 space-y-2 mt-2">
            <a href="<?php echo e(route('student.finance.index')); ?>" class="group flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-premium <?php echo e(request()->routeIs('student.finance.index') ? 'bg-gradient-to-r from-blue-50 to-purple-50 text-blue-600 shadow-md' : 'text-gray-600 hover:bg-white/30 hover:text-gray-900'); ?>">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-9 4h12M4 6h16" />
                </svg>
                My Finances
            </a>
        </div>
    </div>
    <a href="<?php echo e($userType === 'student' ? route('student.exams.upcoming') : route('exams.upcoming')); ?>" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e((request()->is('exams/upcoming') || request()->is('student/exams/upcoming')) ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'); ?>">
        <svg class="mr-3 h-5 w-5 <?php echo e((request()->is('exams/upcoming') || request()->is('student/exams/upcoming')) ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
        </svg>
        Upcoming Exams
    </a>
    <?php endif; ?>

    <?php if($userType === 'admin'): ?>
    <!-- Finance Management -->
    <div x-data="{ open: <?php echo e(request()->routeIs('admin.finance_officers.*') || request()->routeIs('admin.fees.reports') ? 'true' : 'false'); ?> }">
        <button @click="open = !open"
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e((request()->routeIs('admin.finance_officers.*') || request()->routeIs('admin.fees.reports')) ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'); ?>">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 <?php echo e((request()->routeIs('admin.finance_officers.*') || request()->routeIs('admin.fees.reports')) ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                Finance
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="<?php echo e(route('admin.finance_officers.index')); ?>" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('admin.finance_officers.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'); ?>">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Finance Officers
            </a>
            <a href="<?php echo e(route('admin.fees.reports')); ?>" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('admin.fees.reports') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'); ?>">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6h6v6m-7 4h8a2 2 0 002-2V7a2 2 0 00-2-2H9l-2 2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Finance Reports
            </a>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if($userType === 'admin'): ?>
    <a href="<?php echo e(route('admin.grades.index')); ?>" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('admin.grades.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'); ?>">
        <svg class="mr-3 h-5 w-5 <?php echo e(request()->routeIs('admin.grades.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-9 4h12M4 6h16" />
        </svg>
        Grades Approvals
    </a>
    <?php endif; ?>

    <!-- Teachers (hidden for students) -->
    <?php if($userType !== 'student' && $userType !== 'finance'): ?>
    <div x-data="{ open: <?php echo e(request()->routeIs('admin.teachers.*') ? 'true' : 'false'); ?> }">
        <button @click="open = !open" 
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('admin.teachers.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'); ?>">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 <?php echo e(request()->routeIs('admin.teachers.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Teachers
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="<?php echo e(route('admin.teachers.index')); ?>" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('admin.teachers.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'); ?>">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                All Teachers
            </a>
            <a href="<?php echo e(route('admin.teachers.create')); ?>" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('admin.teachers.create') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'); ?>">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Teacher
            </a>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if($userType === 'teacher'): ?>
    <div x-data="{ open: <?php echo e(request()->routeIs('teacher.grades.*') ? 'true' : 'false'); ?> }">
        <button @click="open = !open" 
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('teacher.grades.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'); ?>">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 <?php echo e(request()->routeIs('teacher.grades.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-9 4h12M4 6h16" />
                </svg>
                Grades
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="<?php echo e(route('teacher.grades.create')); ?>" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('teacher.grades.create') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'); ?>">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Grades
            </a>
            <a href="<?php echo e(route('teacher.grades.index')); ?>" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('teacher.grades.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'); ?>">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                My Grades
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Classes (hidden for students) -->
    <?php if($userType !== 'student' && $userType !== 'finance'): ?>
    <div x-data="{ open: <?php echo e(request()->routeIs('classes.*') ? 'true' : 'false'); ?> }">
        <button @click="open = !open" 
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('classes.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'); ?>">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 <?php echo e(request()->routeIs('classes.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Classes
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="<?php echo e(route('classes.index')); ?>" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('classes.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'); ?>">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                All Classes
            </a>
            <a href="<?php echo e(route('classes.create')); ?>" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('classes.create') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'); ?>">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Class
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Subjects (hidden for students) -->
    <?php if($userType !== 'student' && $userType !== 'finance'): ?>
    <div x-data="{ open: <?php echo e(request()->routeIs('subjects.*') ? 'true' : 'false'); ?> }">
        <button @click="open = !open" 
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('subjects.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'); ?>">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 <?php echo e(request()->routeIs('subjects.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Subjects
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="<?php echo e(route('subjects.index')); ?>" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('subjects.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'); ?>">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                All Subjects
            </a>
            <a href="<?php echo e(route('subjects.create')); ?>" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('subjects.create') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'); ?>">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Subject
            </a>
        </div>
    </div>
    <?php endif; ?>

    

    <!-- Fees (hidden for students) -->
    <?php if($userType !== 'student' && $userType !== 'finance'): ?>
    <div x-data="{ open: <?php echo e(request()->routeIs('admin.fees.*') ? 'true' : 'false'); ?> }">
        <button @click="open = !open" 
                class="group w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('admin.fees.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'); ?>">
            <div class="flex items-center">
                <svg class="mr-3 h-5 w-5 <?php echo e(request()->routeIs('admin.fees.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                Fees
            </div>
            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" x-transition class="ml-4 space-y-1">
            <a href="<?php echo e(route('admin.fees.structures.index')); ?>" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('admin.fees.structures.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'); ?>">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Fee Structures
            </a>
            <a href="<?php echo e(route('admin.fees.payments.index')); ?>" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('admin.fees.payments.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'); ?>">
                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Payments
            </a>
        </div>
    </div>
    <?php endif; ?>

    <?php if($userType !== 'finance'): ?>
    <!-- Library -->
    <a href="<?php echo e($userType === 'student' ? route('student.library.index') : route('library.index')); ?>" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e((request()->routeIs('library.*') || request()->routeIs('student.library.*')) ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'); ?>">
        <svg class="mr-3 h-5 w-5 <?php echo e((request()->routeIs('library.*') || request()->routeIs('student.library.*')) ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        Library
    </a>
    <?php endif; ?>

    <?php if($userType !== 'finance'): ?>
    <!-- Transport -->
    <a href="<?php echo e($userType === 'student' ? route('student.transport.index') : route('transport.index')); ?>" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e((request()->routeIs('transport.*') || request()->routeIs('student.transport.*')) ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'); ?>">
        <svg class="mr-3 h-5 w-5 <?php echo e((request()->routeIs('transport.*') || request()->routeIs('student.transport.*')) ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
        </svg>
        Transport
    </a>
    <?php endif; ?>

    <?php if($userType !== 'finance'): ?>
    <!-- Hostel -->
    <a href="<?php echo e($userType === 'student' ? route('student.hostel.index') : route('hostel.index')); ?>" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e((request()->routeIs('hostel.*') || request()->routeIs('student.hostel.*')) ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'); ?>">
        <svg class="mr-3 h-5 w-5 <?php echo e((request()->routeIs('hostel.*') || request()->routeIs('student.hostel.*')) ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        Hostel
    </a>
    <?php endif; ?>

    <?php if($userType !== 'finance'): ?>
    <!-- Attendance -->
    <a href="<?php echo e($userType === 'student' ? route('student.attendance.index') : route('attendance.index')); ?>" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e((request()->routeIs('attendance.*') || request()->routeIs('student.attendance.*')) ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'); ?>">
        <svg class="mr-3 h-5 w-5 <?php echo e((request()->routeIs('attendance.*') || request()->routeIs('student.attendance.*')) ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Attendance
    </a>
    <?php endif; ?>

    <!-- Users (hidden for students) -->
    <?php if($userType !== 'student' && $userType !== 'finance'): ?>
    <a href="<?php echo e(route('users.index')); ?>" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e(request()->routeIs('users.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'); ?>">
        <svg class="mr-3 h-5 w-5 <?php echo e(request()->routeIs('users.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
        </svg>
        Users
    </a>
    <?php endif; ?>

    <!-- Settings -->
    <a href="<?php echo e($userType === 'student' ? route('student.settings.index') : route('settings.index')); ?>" 
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all <?php echo e((request()->routeIs('settings.*') || request()->routeIs('student.settings.*')) ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'); ?>">
        <svg class="mr-3 h-5 w-5 <?php echo e((request()->routeIs('settings.*') || request()->routeIs('student.settings.*')) ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        Settings
    </a>

    <!-- Help Section -->
    <div class="pt-6 mt-6 border-t border-gray-200">
        <div class="px-3 py-3 flex items-center space-x-3">
            <?php ($photo = auth()->user()->profile_photo ?? null); ?>
            <?php if($photo): ?>
                <img src="<?php echo e(Storage::url($photo)); ?>" alt="Avatar" class="h-8 w-8 rounded-full object-cover">
            <?php else: ?>
                <div class="h-8 w-8 bg-gradient-primary rounded-full flex items-center justify-center">
                    <span class="text-white font-medium text-sm"><?php echo e(auth()->user()->name[0] ?? 'U'); ?></span>
                </div>
            <?php endif; ?>
            <div class="min-w-0">
                <div class="text-sm font-semibold text-gray-900 truncate"><?php echo e(auth()->user()->name ?? 'User'); ?></div>
                <div class="text-xs text-gray-500 truncate"><?php echo e(auth()->user()->email ?? ''); ?></div>
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
<?php /**PATH C:\Users\DELL\SchoolManagementSystem\resources\views/layouts/navigation.blade.php ENDPATH**/ ?>