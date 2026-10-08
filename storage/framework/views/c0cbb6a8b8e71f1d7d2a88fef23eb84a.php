
<?php $__env->startSection('title', 'Mechanic Dashboard'); ?>
<?php $__env->startSection('content'); ?>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="card">
        <h3><?php echo e($assignedJobs); ?></h3>
        <p>Assigned Jobs</p>
    </div>
    <div class="card">
        <h3><?php echo e($inProgressJobs); ?></h3>
        <p>In Progress</p>
    </div>
    <div class="card">
        <h3><?php echo e($completedJobs); ?></h3>
        <p>Completed</p>
    </div>
</div>

<div class="card">
    <h2>My Jobs</h2>
    <table class="table">
        <thead>
            <tr><th>Vehicle</th><th>Status</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $recentJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($job->vehicle_model); ?> (<?php echo e($job->license_plate); ?>)</td>
                <td><span style="background: #007bff; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;"><?php echo e(ucfirst($job->status)); ?></span></td>
                <td><a href="<?php echo e(route('mechanic.jobs.show', $job)); ?>" class="btn btn-primary">View</a></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="3">No jobs assigned yet</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="<?php echo e(route('mechanic.jobs.index')); ?>" class="btn btn-primary" style="margin-top: 1rem;">View All Jobs</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\Desktop\auto-repair-shop\autorepairshop\resources\views/mechanic/dashboard.blade.php ENDPATH**/ ?>