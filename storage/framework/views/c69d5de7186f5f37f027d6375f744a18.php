

<?php $__env->startSection('content'); ?>
<div class="container">
    <h1>Pending Payments</h1>
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Student</th>
                <th>Class</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Reference</th>
                <th>Submitted</th>
                <th>Receipt</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($p->student->user->name ?? 'Student'); ?></td>
                <td><?php echo e(optional($p->student->class)->name); ?></td>
                <td><?php echo e(number_format($p->amount, 2)); ?></td>
                <td><?php echo e($p->payment_method); ?></td>
                <td><?php echo e($p->transaction_reference); ?></td>
                <td><?php echo e($p->created_at->format('Y-m-d H:i')); ?></td>
                <td>
                    <?php if($p->receipt_path): ?>
                        <a href="<?php echo e(asset('storage/'.$p->receipt_path)); ?>" target="_blank">View</a>
                    <?php endif; ?>
                </td>
                <td>
                    <form method="POST" action="<?php echo e(route('finance.payments.approve', $p)); ?>" style="display:inline-block">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-sm btn-success" onclick="return confirm('Approve this payment?')">Approve</button>
                    </form>
                    <form method="POST" action="<?php echo e(route('finance.payments.reject', $p)); ?>" style="display:inline-block">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="reason" value="Insufficient details">
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Reject this payment?')">Reject</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="8" class="text-center">No pending payments.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php echo e($pending->links()); ?>

</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.finance', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\SchoolManagementSystem\resources\views/finance/payments/index.blade.php ENDPATH**/ ?>