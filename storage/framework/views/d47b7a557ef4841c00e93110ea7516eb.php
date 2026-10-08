
<?php $__env->startSection('title', 'Login'); ?>
<?php $__env->startSection('content'); ?>
<div style="max-width: 400px; margin: 2rem auto;">
    <div class="card">
        <h2>Login</h2>
        <form method="POST" action="<?php echo e(route('login')); ?>"><?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo e(old('email')); ?>" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>
        <p style="margin-top: 1rem;"><a href="<?php echo e(route('register')); ?>">Create Account</a></p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\Desktop\auto-repair-shop\autorepairshop\resources\views/auth/login.blade.php ENDPATH**/ ?>