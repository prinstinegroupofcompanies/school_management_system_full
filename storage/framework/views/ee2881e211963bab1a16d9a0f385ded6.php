

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1">Bulk Send Invoices</h1>
            <p class="text-muted mb-0">Generate invoices for one or more classes and notify students.</p>
        </div>
        <a href="<?php echo e(route('finance.fee-items.index')); ?>" class="btn btn-outline-secondary">Manage Fee Items</a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('finance.invoices.bulk-send')); ?>" class="card shadow-sm">
        <?php echo csrf_field(); ?>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-lg-6">
                    <label class="form-label">Select Classes</label>
                    <div class="d-flex gap-2 mb-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">Select All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAll">Clear</button>
                        <span class="text-muted small ms-auto" id="selectedCount">0 selected</span>
                    </div>
                    <select name="class_ids[]" id="classSelect" class="form-select" multiple required size="10">
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($class->id); ?>"><?php echo e($class->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <small class="text-muted">Tip: Hold Ctrl (Windows) or Cmd (Mac) to select multiple items.</small>
                </div>
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Semester (optional)</label>
                            <select name="semester" class="form-select">
                                <option value="">Default/All</option>
                                <?php $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($sem); ?>"><?php echo e($sem); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Year</label>
                            <input type="number" name="year" class="form-control" min="2000" max="2100" value="<?php echo e($currentYear); ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Note (optional)</label>
                            <textarea name="note" class="form-control" rows="4" placeholder="Add a short message that will appear on invoices (e.g., payment deadline, reminder, etc.)"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                Invoices will include the current fee items (global + class-specific) and school payment details from System Settings.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2 bg-white">
            <button type="submit" id="submitBtn" class="btn btn-primary">
                <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner" role="status" aria-hidden="true"></span>
                Generate & Send
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('classSelect');
    const selectAllBtn = document.getElementById('selectAll');
    const clearAllBtn = document.getElementById('clearAll');
    const count = document.getElementById('selectedCount');
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('submitSpinner');

    function updateCount() {
        count.textContent = `${Array.from(select.selectedOptions).length} selected`;
    }
    select.addEventListener('change', updateCount);
    updateCount();

    selectAllBtn.addEventListener('click', () => {
        Array.from(select.options).forEach(o => o.selected = true);
        select.dispatchEvent(new Event('change'));
    });
    clearAllBtn.addEventListener('click', () => {
        Array.from(select.options).forEach(o => o.selected = false);
        select.dispatchEvent(new Event('change'));
    });

    submitBtn.closest('form').addEventListener('submit', () => {
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
    });
});
</script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.finance', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\SchoolManagementSystem\resources\views/finance/invoices/create.blade.php ENDPATH**/ ?>