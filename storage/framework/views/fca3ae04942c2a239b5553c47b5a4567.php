

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900"><?php echo e($student->user->name); ?></h1>
                <p class="text-gray-600 mt-2">Student ID: <?php echo e($student->student_id ?? 'N/A'); ?></p>
            </div>
            <div class="flex space-x-4">
                <a href="<?php echo e(route('students.edit', $student)); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Student
                </a>
                <a href="<?php echo e(route('students.index')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Students
                </a>
            </div>
        </div>

        <!-- Student Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Full Name:</span>
                        <p class="text-gray-900"><?php echo e($student->user->name); ?></p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Email:</span>
                        <p class="text-gray-900"><?php echo e($student->user->email); ?></p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Student ID:</span>
                        <p class="text-gray-900"><?php echo e($student->student_id ?? 'N/A'); ?></p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Admission Number:</span>
                        <p class="text-gray-900"><?php echo e($student->admission_no ?? 'N/A'); ?></p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            <?php if($student->status === 'active'): ?> bg-green-100 text-green-800
                            <?php elseif($student->status === 'inactive'): ?> bg-red-100 text-red-800
                            <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                            <?php echo e(ucfirst($student->status ?? 'active')); ?>

                        </span>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Class:</span>
                        <p class="text-gray-900"><?php echo e($student->class ? $student->class->name : 'Not Assigned'); ?></p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Class Code:</span>
                        <p class="text-gray-900"><?php echo e($student->class ? $student->class->code : 'N/A'); ?></p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Section:</span>
                        <p class="text-gray-900"><?php echo e($student->section ? $student->section->name : 'Not Assigned'); ?></p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Admission Date:</span>
                        <p class="text-gray-900"><?php echo e($student->admission_date ? $student->admission_date->format('M d, Y') : 'N/A'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Date of Birth:</span>
                        <p class="text-gray-900"><?php echo e($student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A'); ?></p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Gender:</span>
                        <p class="text-gray-900"><?php echo e(ucfirst($student->gender ?? 'N/A')); ?></p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Phone:</span>
                        <p class="text-gray-900"><?php echo e($student->phone ?? 'N/A'); ?></p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Address:</span>
                        <p class="text-gray-900"><?php echo e($student->address ?? 'N/A'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guardian Information -->
        <?php if($student->guardian_name || $student->guardian_phone || $student->guardian_email): ?>
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Guardian Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <span class="text-sm font-medium text-gray-500">Guardian Name:</span>
                    <p class="text-gray-900"><?php echo e($student->guardian_name ?? 'N/A'); ?></p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Guardian Phone:</span>
                    <p class="text-gray-900"><?php echo e($student->guardian_phone ?? 'N/A'); ?></p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Guardian Email:</span>
                    <p class="text-gray-900"><?php echo e($student->guardian_email ?? 'N/A'); ?></p>
                </div>
            </div>
            <?php if($student->guardian_address): ?>
            <div class="mt-4">
                <span class="text-sm font-medium text-gray-500">Guardian Address:</span>
                <p class="text-gray-900"><?php echo e($student->guardian_address); ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Academic Performance -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Attendance -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Attendance</h3>
                <div class="space-y-3">
                    <?php if($student->attendances && $student->attendances->count() > 0): ?>
                        <?php $__currentLoopData = $student->attendances->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($attendance->date->format('M d, Y')); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo e($attendance->subject ?? 'General'); ?></div>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    <?php if($attendance->status === 'present'): ?> bg-green-100 text-green-800
                                    <?php elseif($attendance->status === 'absent'): ?> bg-red-100 text-red-800
                                    <?php else: ?> bg-yellow-100 text-yellow-800 <?php endif; ?>">
                                    <?php echo e(ucfirst($attendance->status)); ?>

                                </span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <div class="text-gray-400 text-4xl mb-2">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <p class="text-gray-500">No attendance records found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Exam Marks -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Exam Marks</h3>
                <div class="space-y-3">
                    <?php if($student->examMarks && $student->examMarks->count() > 0): ?>
                        <?php $__currentLoopData = $student->examMarks->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mark): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($mark->subject ?? 'Subject'); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo e($mark->exam_type ?? 'Exam'); ?></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($mark->marks_obtained ?? 0); ?>/<?php echo e($mark->total_marks ?? 100); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo e(round(($mark->marks_obtained ?? 0) / ($mark->total_marks ?? 100) * 100)); ?>%</div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <div class="text-gray-400 text-4xl mb-2">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <p class="text-gray-500">No exam marks found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\SchoolManagementSystem\resources\views/students/show.blade.php ENDPATH**/ ?>