

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Fee Structures</h1>
    </div>

    <div class="bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $feeStructures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900"><?php echo e($fs->student->user->name ?? 'N/A'); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-500"><?php echo e($fs->class->name ?? 'N/A'); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-500"><?php echo e($fs->fee_type ?? $fs->name ?? 'Structure'); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-900">$<?php echo e(number_format($fs->amount ?? 0, 2)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500">No fee structures found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="p-4"><?php echo e(method_exists($feeStructures, 'links') ? $feeStructures->links() : ''); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\SchoolManagementSystem\resources\views/fees/structures.blade.php ENDPATH**/ ?>