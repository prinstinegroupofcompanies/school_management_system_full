

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Exams</h1>
                <p class="text-gray-600 mt-2">View and take your exams</p>
            </div>
        </div>

        <!-- Upcoming Exams -->
        <div class="bg-white rounded-lg shadow-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Upcoming Exams</h2>
            </div>
            <div class="p-6">
                <?php if($upcomingExams->count() > 0): ?>
                <div class="grid gap-4">
                    <?php $__currentLoopData = $upcomingExams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900"><?php echo e($exam->title); ?></h3>
                                <div class="mt-2 text-sm text-gray-600">
                                    <div class="flex items-center space-x-4">
                                        <span><i class="fas fa-book mr-2"></i><?php echo e($exam->subject->name ?? 'Subject'); ?></span>
                                        <span><i class="fas fa-users mr-2"></i><?php echo e($exam->class->name ?? 'Class'); ?></span>
                                        <span><i class="fas fa-calendar mr-2"></i><?php echo e($exam->start_date->format('M d, Y')); ?></span>
                                        <span><i class="fas fa-clock mr-2"></i><?php echo e($exam->start_time); ?></span>
                                    </div>
                                </div>
                                <?php if($exam->instructions): ?>
                                <p class="mt-2 text-sm text-gray-700"><?php echo e(Str::limit($exam->instructions, 100)); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="ml-6">
                                <a href="<?php echo e(route('student.exams.take', $exam)); ?>" 
                                   class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Take Exam
                                </a>
                            </div>
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
                    <p class="text-gray-600">You don't have any upcoming exams at the moment.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Completed Exams -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Completed Exams</h2>
            </div>
            <div class="p-6">
                <?php if($completedExams->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $completedExams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo e($exam->title); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo e($exam->subject->name ?? 'N/A'); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo e($exam->start_date->format('M d, Y')); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($exam->attempts->where('student_id', auth()->user()->student->id)->first()): ?>
                                    <?php ($attempt = $exam->attempts->where('student_id', auth()->user()->student->id)->first()); ?>
                                    <?php if($attempt->status === 'submitted'): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Submitted
                                        </span>
                                    <?php elseif($attempt->status === 'graded'): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Graded
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo e(ucfirst($attempt->status)); ?>

                                        </span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Not Taken
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($exam->attempts->where('student_id', auth()->user()->student->id)->first()): ?>
                                    <?php ($attempt = $exam->attempts->where('student_id', auth()->user()->student->id)->first()); ?>
                                    <?php if($attempt->status === 'graded'): ?>
                                        <div class="text-sm text-gray-900"><?php echo e($attempt->score); ?>/<?php echo e($exam->total_marks ?? 100); ?></div>
                                    <?php else: ?>
                                        <div class="text-sm text-gray-500">Pending</div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="text-sm text-gray-500">-</div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <?php if($exam->attempts->where('student_id', auth()->user()->student->id)->first()): ?>
                                    <?php ($attempt = $exam->attempts->where('student_id', auth()->user()->student->id)->first()); ?>
                                    <?php if($attempt->status === 'graded'): ?>
                                        <a href="<?php echo e(route('student.exams.results', $attempt)); ?>" 
                                           class="text-blue-600 hover:text-blue-900">View Results</a>
                                    <?php elseif($attempt->status === 'submitted'): ?>
                                        <span class="text-gray-500">Awaiting Results</span>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('student.exams.take', $exam)); ?>" 
                                           class="text-blue-600 hover:text-blue-900">Continue</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <a href="<?php echo e(route('student.exams.take', $exam)); ?>" 
                                       class="text-blue-600 hover:text-blue-900">Take Exam</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Completed Exams</h3>
                    <p class="text-gray-600">You haven't completed any exams yet.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\SchoolManagementSystem\resources\views/student/exams/index.blade.php ENDPATH**/ ?>