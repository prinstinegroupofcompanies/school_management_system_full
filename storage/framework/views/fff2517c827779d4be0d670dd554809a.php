

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <?php if(session('success')): ?>
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 px-4 py-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 px-4 py-3"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">My Finances</h1>
        <?php ($payTarget = (isset($firstUnpaidFee) && $firstUnpaidFee) ? $firstUnpaidFee : ($fees->first() ?? null)); ?>
        <?php if($payTarget && ($payTarget->balance ?? 0) > 0): ?>
            <a href="<?php echo e(route('student.finance.create-payment', $payTarget)); ?>" class="inline-flex items-center bg-indigo-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                Pay Fees
            </a>
        <?php elseif($payTarget): ?>
            <button class="inline-flex items-center bg-gray-300 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg cursor-not-allowed" title="No outstanding balance">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                Pay Fees
            </button>
        <?php endif; ?>
    </div>

    <!-- Invoices -->
    <div class="table-premium overflow-hidden mb-6">
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="text-left">Term</th>
                    <th class="text-left">Total</th>
                    <th class="text-left">Paid</th>
                    <th class="text-left">Balance</th>
                    <th class="text-left">Due</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800">
                <?php $__empty_1 = true; $__currentLoopData = $fees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b last:border-0">
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-200"><?php echo e($fee->semester ?? 'All'); ?> - <?php echo e($fee->year); ?></td>
                    <td class="px-4 py-3"><span class="status-badge-premium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200"><?php echo e(number_format($fee->total_amount, 2)); ?></span></td>
                    <td class="px-4 py-3"><span class="status-badge-premium bg-green-100 text-green-700 dark:bg-green-700/40 dark:text-green-300"><?php echo e(number_format($fee->paid_amount, 2)); ?></span></td>
                    <td class="px-4 py-3"><span class="status-badge-premium <?php echo e($fee->balance > 0 ? 'bg-rose-100 text-rose-700 dark:bg-rose-700/40 dark:text-rose-300' : 'bg-green-100 text-green-700 dark:bg-green-700/40 dark:text-green-300'); ?>"><?php echo e(number_format($fee->balance, 2)); ?></span></td>
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-200"><?php echo e($fee->due_date ? \Carbon\Carbon::parse($fee->due_date)->format('Y-m-d') : '-'); ?></td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="<?php echo e(route('student.invoices.download', $fee)); ?>" class="btn-premium inline-block text-xs px-3 py-2">Invoice</a>
                        <?php if($fee->balance > 0): ?>
                        <a href="<?php echo e(route('student.finance.create-payment', $fee)); ?>" class="inline-block bg-indigo-600 text-white text-xs font-semibold px-3 py-2 rounded-lg hover:bg-indigo-700 transition">Pay now</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No invoices available.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Payment Options -->
        <div class="card-premium p-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Payment Options</h2>
            <div class="space-y-2">
                <div>
                    <div class="text-sm text-gray-500">Bank</div>
                    <div class="font-medium text-gray-900 dark:text-gray-100"><?php echo e($bankDetails['bank_name'] ?? ''); ?></div>
                    <div class="text-gray-600 dark:text-gray-300"><?php echo e($bankDetails['bank_account'] ?? ''); ?></div>
                </div>
                <div class="pt-2">
                    <div class="text-sm text-gray-500">Mobile Money</div>
                    <div class="font-medium text-gray-900 dark:text-gray-100"><?php echo e($mobileMoney['provider'] ?? ''); ?></div>
                    <div class="text-gray-600 dark:text-gray-300"><?php echo e($mobileMoney['number'] ?? ''); ?></div>
                </div>
            </div>
        </div>

        <!-- Payment History (Approved) -->
        <div class="card-premium p-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Payment History</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-600 dark:text-gray-300 border-b">
                            <th class="py-2">Date</th>
                            <th class="py-2">Amount</th>
                            <th class="py-2">Method</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b last:border-0">
                            <td class="py-2 text-gray-700 dark:text-gray-200"><?php echo e($p->created_at->format('Y-m-d')); ?></td>
                            <td class="py-2 text-gray-700 dark:text-gray-200"><?php echo e(number_format($p->amount, 2)); ?></td>
                            <td class="py-2 text-gray-700 dark:text-gray-200"><?php echo e($p->payment_method); ?></td>
                            <td class="py-2">
                                <span class="status-badge-premium <?php echo e($p->status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-700/40 dark:text-green-300' : ($p->status === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-700/40 dark:text-rose-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-700/40 dark:text-amber-200')); ?>">
                                    <?php echo e(ucfirst($p->status)); ?>

                                </span>
                            </td>
                            <td class="py-2">
                                <?php if($p->receipt_path): ?>
                                <a class="text-indigo-600 hover:text-indigo-800" href="<?php echo e(asset('storage/'.$p->receipt_path)); ?>" target="_blank">View</a>
                                <?php else: ?>
                                <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500 dark:text-gray-400">No payments yet.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pending Payments -->
    <div class="mt-6 card-premium p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Pending Payments</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-600 dark:text-gray-300 border-b">
                        <th class="py-2">Date</th>
                        <th class="py-2">Amount</th>
                        <th class="py-2">Method</th>
                        <th class="py-2">Status</th>
                        <th class="py-2">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $pendingPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-b last:border-0">
                        <td class="py-2 text-gray-700 dark:text-gray-200"><?php echo e($p->created_at->format('Y-m-d')); ?></td>
                        <td class="py-2 text-gray-700 dark:text-gray-200"><?php echo e(number_format($p->amount, 2)); ?></td>
                        <td class="py-2 text-gray-700 dark:text-gray-200"><?php echo e($p->payment_method); ?></td>
                        <td class="py-2">
                            <span class="status-badge-premium bg-amber-100 text-amber-800 dark:bg-amber-700/40 dark:text-amber-200">Pending</span>
                        </td>
                        <td class="py-2">
                            <?php if($p->receipt_path): ?>
                            <a class="text-indigo-600 hover:text-indigo-800" href="<?php echo e(asset('storage/'.$p->receipt_path)); ?>" target="_blank">View</a>
                            <?php else: ?>
                            <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500 dark:text-gray-400">No pending payments.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.student', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\SchoolManagementSystem\resources\views/student/finance/index.blade.php ENDPATH**/ ?>