
<?php $__env->startSection('title', 'My Jobs'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <h2>My Repair Jobs</h2>
    <a href="<?php echo e(route('customer.jobs.request')); ?>" class="btn btn-primary">Request New Repair</a>
    <table class="table" style="margin-top: 1rem;">
        <thead>
            <tr><th>Vehicle</th><th>Status</th><th>Created</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($job->vehicle_model); ?></td>
                <td><span style="background: #17a2b8; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;"><?php echo e(ucfirst($job->status)); ?></span></td>
                <td><?php echo e($job->created_at->format('M d, Y')); ?></td>
                <td><a href="<?php echo e(route('customer.jobs.show', $job)); ?>" class="btn btn-primary">View</a></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="4">No jobs found</td></tr><?php endif; ?>
        </tbody>
    </table>
    <?php echo e($jobs->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\Desktop\auto-repair-shop\autorepairshop\resources\views/customer/jobs/index.blade.php ENDPATH**/ ?>