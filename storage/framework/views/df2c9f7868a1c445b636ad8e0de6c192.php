

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Create Fee Item</h1>
        <a href="<?php echo e(route('finance.fee-items.index')); ?>" class="btn btn-outline-secondary">Back</a>
    </div>
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST" action="<?php echo e(route('finance.fee-items.store')); ?>" class="card p-3 shadow-sm">
        <?php echo csrf_field(); ?>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Item Name</label>
                <input type="text" class="form-control" name="item_name" value="<?php echo e(old('item_name')); ?>" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" min="1" class="form-control" name="quantity" value="<?php echo e(old('quantity', 1)); ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Price per unit</label>
                <input type="number" step="0.01" min="0" class="form-control" name="price_per_unit" value="<?php echo e(old('price_per_unit', 0)); ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Total (auto if empty)</label>
                <input type="number" step="0.01" min="0" class="form-control" name="total" value="<?php echo e(old('total')); ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Class (optional)</label>
                <select class="form-select" name="class_id">
                    <option value="">All Classes</option>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($class->id); ?>" <?php if(old('class_id')==$class->id): ?> selected <?php endif; ?>><?php echo e($class->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Semester (optional)</label>
                <input type="text" class="form-control" name="semester" value="<?php echo e(old('semester')); ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Year (optional)</label>
                <input type="number" min="2000" max="2100" class="form-control" name="year" value="<?php echo e(old('year', $currentYear)); ?>">
            </div>
        </div>
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?php if(old('is_active', 1)): ?> checked <?php endif; ?>>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="<?php echo e(route('finance.fee-items.index')); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.finance', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\SchoolManagementSystem\resources\views/finance/fee_items/create.blade.php ENDPATH**/ ?>