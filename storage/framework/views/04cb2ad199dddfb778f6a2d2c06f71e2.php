<?php $__env->startSection('title', 'Login'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="form-card">
                <div class="text-center mb-4">
                    <div class="brand-mark mx-auto mb-3">AI</div>
                    <h2 class="fw-bold mb-2">Login</h2>
                    <p class="text-muted mb-0">Use the same login form for both candidate and admin users.</p>
                </div>

                <form action="<?php echo e(route('login.submit')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control rounded-3" required>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-dark rounded-pill px-4 w-100">Login</button>
                </form>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        No account?
                        <a href="<?php echo e(route('register')); ?>">Candidate Register</a>
                        or
                        <a href="<?php echo e(route('register.admin')); ?>">Admin Register</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/manavendrasinh/Desktop/ai_resume_analyzer/resources/views/auth/login.blade.php ENDPATH**/ ?>