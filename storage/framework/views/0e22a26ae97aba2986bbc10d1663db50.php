
<?php $__env->startSection('title', 'Invoices'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <h2>My Invoices</h2>
    <table class="table">
        <thead>
            <tr><th>Invoice #</th><th>Amount</th><th>Status</th><th>Due Date</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($inv->invoice_number); ?></td>
                <td>$<?php echo e(number_format($inv->total, 2)); ?></td>
                <td><span style="background: #ffc107; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;"><?php echo e(ucfirst($inv->status)); ?></span></td>
                <td><?php echo e($inv->due_date?->format('M d, Y') ?? 'N/A'); ?></td>
                <td><a href="<?php echo e(route('customer.invoices.show', $inv)); ?>" class="btn btn-primary">View</a></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="5">No invoices</td></tr><?php endif; ?>
        </tbody>
    </table>
    <?php echo e($invoices->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\Desktop\auto-repair-shop\autorepairshop\resources\views/customer/invoices/index.blade.php ENDPATH**/ ?>