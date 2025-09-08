

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Fee Items</h1>
            <p class="text-muted mb-0">Manage tuition, books, uniforms, and other billable items.</p>
        </div>
        <a class="btn btn-primary" href="<?php echo e(route('finance.fee-items.create')); ?>">
            <i class="bi bi-plus-lg me-1"></i> Add Item
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-3" method="GET">
                <div class="col-md-3">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($class->id); ?>" <?php if(request('class_id')==$class->id): ?> selected <?php endif; ?>><?php echo e($class->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Semester</label>
                    <input type="text" name="semester" class="form-control" placeholder="Semester" value="<?php echo e(request('semester')); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Year</label>
                    <input type="number" name="year" class="form-control" placeholder="Year" value="<?php echo e(request('year')); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="active" class="form-select">
                        <option value="">All</option>
                        <option value="1" <?php if(request('active')==='1'): ?> selected <?php endif; ?>>Active</option>
                        <option value="0" <?php if(request('active')==='0'): ?> selected <?php endif; ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3">
        <?php $__empty_1 = true; $__currentLoopData = $feeItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title mb-0"><?php echo e($item->item_name); ?></h5>
                        <span class="badge <?php echo e($item->is_active ? 'bg-success' : 'bg-secondary'); ?>"><?php echo e($item->is_active ? 'Active' : 'Inactive'); ?></span>
                    </div>
                    <div class="text-muted small mb-3">
                        <div>Class: <strong><?php echo e(optional($item->classRoom)->name ?? 'All'); ?></strong></div>
                        <div>Term: <strong><?php echo e($item->semester ?? 'All'); ?> <?php echo e($item->year ?? ''); ?></strong></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <div>
                            <div class="small text-muted">Qty × Unit</div>
                            <div><strong><?php echo e($item->quantity); ?></strong> × <strong><?php echo e(number_format($item->price_per_unit, 2)); ?></strong></div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Total</div>
                            <div class="h5 mb-0"><?php echo e(number_format($item->total, 2)); ?></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex gap-2">
                    <a href="<?php echo e(route('finance.fee-items.show', $item)); ?>" class="btn btn-outline-primary btn-sm">View</a>
                    <a href="<?php echo e(route('finance.fee-items.edit', $item)); ?>" class="btn btn-outline-warning btn-sm">Edit</a>
                    <form action="<?php echo e(route('finance.fee-items.destroy', $item)); ?>" method="POST" class="ms-auto">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this fee item?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="alert alert-info">No fee items found.</div>
        </div>
        <?php endif; ?>
    </div>

    <div class="mt-3">
        <?php echo e($feeItems->withQueryString()->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.finance', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\SchoolManagementSystem\resources\views/finance/fee_items/index.blade.php ENDPATH**/ ?>