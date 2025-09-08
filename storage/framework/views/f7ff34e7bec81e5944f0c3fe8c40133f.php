<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .summary { margin: 10px 0; }
        .totals { margin-top: 8px; width: 100%; }
        .totals td { padding: 4px 6px; }
        .right { text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f2f2f2; }
    </style>
    </head>
<body>
    <div class="header">
        <div>
            <?php if(!empty($schoolLogoUrl)): ?>
                <img src="<?php echo e($schoolLogoUrl); ?>" alt="Logo" style="height:50px;margin-bottom:6px;">
            <?php endif; ?>
            <h2><?php echo e($schoolName ?? 'School Fee Invoice'); ?></h2>
            <div>Date: <?php echo e(now()->format('Y-m-d')); ?></div>
        </div>
        <div>
            <div>Invoice #: <?php echo e($invoiceNo ?? ('INV-' . $studentFee->id)); ?></div>
            <div>Student: <?php echo e($student->full_name ?? $student->user->name); ?></div>
            <div>ID: <?php echo e($student->student_id ?? $student->id); ?></div>
            <div>Class: <?php echo e(optional($student->class)->name); ?></div>
            <div>Term: <?php echo e($studentFee->semester ?? 'All'); ?> - <?php echo e($studentFee->year); ?></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php ($lines = $studentFee->fee_breakdown ?? []); ?>
            <?php $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($line['item_name']); ?></td>
                <td><?php echo e($line['quantity']); ?></td>
                <td><?php echo e(number_format($line['price_per_unit'], 2)); ?></td>
                <td><?php echo e(number_format($line['total'], 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="right"><strong>Total:</strong></td>
            <td class="right" style="width:140px;"><?php echo e(number_format($studentFee->total_amount, 2)); ?></td>
        </tr>
        <tr>
            <td class="right"><strong>Paid:</strong></td>
            <td class="right"><?php echo e(number_format($studentFee->paid_amount, 2)); ?></td>
        </tr>
        <tr>
            <td class="right"><strong>Balance:</strong></td>
            <td class="right"><?php echo e(number_format($studentFee->balance, 2)); ?></td>
        </tr>
    </table>

    <div class="summary">
        <h4>Payment Options</h4>
        <p><strong>Bank:</strong> <?php echo e($bankDetails['bank_name'] ?? ''); ?> (<?php echo e($bankDetails['bank_account'] ?? ''); ?>)</p>
        <p><strong>Mobile Money:</strong> <?php echo e($mobileMoney['provider'] ?? ''); ?> - <?php echo e($mobileMoney['number'] ?? ''); ?></p>
    </div>

    <?php if(!empty($note)): ?>
        <div class="summary">
            <strong>Note:</strong>
            <p><?php echo e($note); ?></p>
        </div>
    <?php endif; ?>
    
</body>
</html>


<?php /**PATH C:\Users\DELL\SchoolManagementSystem\resources\views/finance/invoices/pdf.blade.php ENDPATH**/ ?>