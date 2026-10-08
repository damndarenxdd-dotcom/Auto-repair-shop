
<?php $__env->startSection('title', 'Customer Dashboard'); ?>
<?php $__env->startSection('content'); ?>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="card">
        <h3><?php echo e($totalJobs); ?></h3>
        <p>Total Jobs</p>
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
        <h3><?php echo e($unreadNotifications); ?></h3>
        <p>Unread Notifications</p>
    </div>
</div>

<div class="card">
    <h2>Quick Actions</h2>
    <nav style="margin-top: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo e(route('customer.jobs.request')); ?>" class="btn btn-primary">Request Repair Job</a>
        <a href="<?php echo e(route('customer.jobs.index')); ?>" class="btn btn-primary">My Jobs</a>
        <a href="<?php echo e(route('customer.notifications.index')); ?>" class="btn btn-primary">Notifications</a>
        <a href="<?php echo e(route('customer.invoices.index')); ?>" class="btn btn-primary">Invoices</a>
    </nav>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\Desktop\auto-repair-shop\autorepairshop\resources\views/customer/dashboard.blade.php ENDPATH**/ ?>