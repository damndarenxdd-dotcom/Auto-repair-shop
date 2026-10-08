
<?php $__env->startSection('title', 'Manager Dashboard'); ?>
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
    <div class="card">
        <h3><?php echo e($pendingJobs); ?></h3>
        <p>Pending</p>
    </div>
</div>

<div class="card">
    <h2>Manager Dashboard</h2>
    <nav style="margin-top: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo e(route('manager.jobs.index')); ?>" class="btn btn-primary">View Jobs</a>
        <a href="<?php echo e(route('manager.jobs.create')); ?>" class="btn btn-primary">Create New Job</a>
        <a href="<?php echo e(route('manager.invoices.index')); ?>" class="btn btn-primary">Manage Invoices</a>
    </nav>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\Desktop\auto-repair-shop\autorepairshop\resources\views/manager/dashboard.blade.php ENDPATH**/ ?>