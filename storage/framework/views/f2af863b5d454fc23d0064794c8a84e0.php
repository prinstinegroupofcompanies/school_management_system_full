

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Gradesheet (<?php echo e($year); ?>)</h1>
        <div class="space-x-2">
            <a href="<?php echo e(route('student.gradesheet.pdf', ['year' => $year, 'period' => $period])); ?>" class="px-3 py-2 bg-green-600 text-white rounded">Download PDF</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sem 1 Avg</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sem 2 Avg</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Avg</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-900"><?php echo e($g->subject->name ?? ''); ?></td>
                    <td class="px-6 py-4 text-sm"><?php echo e($g->sem1_avg ?? '-'); ?></td>
                    <td class="px-6 py-4 text-sm"><?php echo e($g->sem2_avg ?? '-'); ?></td>
                    <td class="px-6 py-4 text-sm"><?php echo e($g->year_avg ?? '-'); ?></td>
                    <td class="px-6 py-4 text-sm capitalize"><?php echo e($g->status); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">No grades yet.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\SchoolManagementSystem\resources\views/student/gradesheet/show.blade.php ENDPATH**/ ?>