

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Upcoming Exams</h1>
                <div class="flex items-center space-x-4">
                    <a href="<?php echo e(route('student.dashboard')); ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <?php if($upcomingExams->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $upcomingExams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-300">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <?php echo e($exam->examType->name ?? 'Exam'); ?>

                        </span>
                    </div>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-2"><?php echo e($exam->title); ?></h3>
                    <p class="text-sm text-gray-600 mb-4"><?php echo e($exam->description); ?></p>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <strong>Date:</strong> <?php echo e(\Carbon\Carbon::parse($exam->start_date)->format('M d, Y')); ?>

                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <strong>Time:</strong> <?php echo e(\Carbon\Carbon::parse($exam->start_time)->format('h:i A')); ?> - <?php echo e(\Carbon\Carbon::parse($exam->end_time)->format('h:i A')); ?>

                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <strong>Subject:</strong> <?php echo e($exam->subject->name ?? 'N/A'); ?>

                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <strong>Class:</strong> <?php echo e($exam->class->name ?? 'N/A'); ?>

                        </div>
                    </div>

                    <!-- Countdown Timer -->
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                        <div class="text-center">
                            <p class="text-sm font-medium text-blue-900 mb-2">Time Remaining</p>
                            <div id="countdown-<?php echo e($exam->id); ?>" class="text-lg font-bold text-blue-700">
                                <span id="days-<?php echo e($exam->id); ?>">0</span>d 
                                <span id="hours-<?php echo e($exam->id); ?>">0</span>h 
                                <span id="minutes-<?php echo e($exam->id); ?>">0</span>m 
                                <span id="seconds-<?php echo e($exam->id); ?>">0</span>s
                            </div>
                        </div>
                    </div>

                    <?php if($exam->instructions): ?>
                    <div class="mt-4 p-3 bg-yellow-50 rounded-lg">
                        <h4 class="text-sm font-medium text-yellow-900 mb-1">Instructions</h4>
                        <p class="text-sm text-yellow-800"><?php echo e($exam->instructions); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <div class="text-center py-12">
            <div class="text-gray-400 text-6xl mb-4">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Upcoming Exams</h3>
            <p class="text-gray-600">You don't have any upcoming exams scheduled.</p>
            <a href="<?php echo e(route('student.dashboard')); ?>" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                Back to Dashboard
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Countdown Timer Function
function updateCountdown(examId, targetDate) {
    const now = new Date().getTime();
    const distance = targetDate - now;

    if (distance < 0) {
        document.getElementById(`countdown-${examId}`).innerHTML = "Exam Started!";
        return;
    }

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    document.getElementById(`days-${examId}`).innerHTML = days;
    document.getElementById(`hours-${examId}`).innerHTML = hours;
    document.getElementById(`minutes-${examId}`).innerHTML = minutes;
    document.getElementById(`seconds-${examId}`).innerHTML = seconds;
}

// Initialize countdown timers
document.addEventListener('DOMContentLoaded', function() {
    <?php $__currentLoopData = $upcomingExams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    const targetDate<?php echo e($exam->id); ?> = new Date('<?php echo e($exam->start_date); ?> <?php echo e($exam->start_time); ?>').getTime();
    updateCountdown(<?php echo e($exam->id); ?>, targetDate<?php echo e($exam->id); ?>);
    setInterval(function() {
        updateCountdown(<?php echo e($exam->id); ?>, targetDate<?php echo e($exam->id); ?>);
    }, 1000);
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\SchoolManagementSystem\resources\views/student/exams/upcoming.blade.php ENDPATH**/ ?>