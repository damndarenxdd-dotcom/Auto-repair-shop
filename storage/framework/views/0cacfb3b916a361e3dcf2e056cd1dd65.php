
<?php $__env->startSection('title', 'Notifications'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <h2>Notifications</h2>
    <form method="POST" action="<?php echo e(route('customer.notifications.readAll')); ?>" style="margin-bottom: 1rem;"><?php echo csrf_field(); ?><button type="submit" class="btn btn-primary">Mark All as Read</button></form>
    <div>
        <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div style="background: <?php echo e($notif->read ? '#f8f9fa' : '#e7f3ff'); ?>; padding: 1rem; margin: 0.5rem 0; border-left: 4px solid <?php echo e($notif->read ? '#ddd' : '#007bff'); ?>;">
            <h4><?php echo e($notif->title); ?></h4>
            <p><?php echo e($notif->message); ?></p>
            <small><?php echo e($notif->created_at->format('M d, Y H:i')); ?></small>
            <?php if(!$notif->read): ?><form method="POST" action="<?php echo e(route('customer.notifications.read', $notif)); ?>" style="display: inline;"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?><button type="submit" class="btn btn-primary">Mark as Read</button></form><?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><p>No notifications</p><?php endif; ?>
    </div>
    <?php echo e($notifications->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\Desktop\auto-repair-shop\autorepairshop\resources\views/customer/notifications/index.blade.php ENDPATH**/ ?>