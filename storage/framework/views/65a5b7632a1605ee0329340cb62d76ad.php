

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Fee Item Details</h1>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('finance.fee-items.edit', $feeItem)); ?>" class="btn btn-warning">Edit</a>
            <a href="<?php echo e(route('finance.fee-items.index')); ?>" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="text-muted small">Item Name</div>
                    <div class="h5"><?php echo e($feeItem->item_name); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Status</div>
                    <span class="badge <?php echo e($feeItem->is_active ? 'bg-success' : 'bg-secondary'); ?>"><?php echo e($feeItem->is_active ? 'Active' : 'Inactive'); ?></span>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Class</div>
                    <div><?php echo e(optional($feeItem->classRoom)->name ?? 'All'); ?></div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="text-muted small">Quantity</div>
                    <div class="h6"><?php echo e($feeItem->quantity); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Unit Price</div>
                    <div class="h6"><?php echo e(number_format($feeItem->price_per_unit, 2)); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Total</div>
                    <div class="h6"><?php echo e(number_format($feeItem->total, 2)); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Term</div>
                    <div class="h6"><?php echo e($feeItem->semester ?? 'All'); ?> <?php echo e($feeItem->year ?? ''); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.finance', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\SchoolManagementSystem\resources\views/finance/fee_items/show.blade.php ENDPATH**/ ?>