<?php $__env->startSection('title', $pageTitle ?? 'Register'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="form-card">
                <div class="text-center mb-4">
                    <div class="brand-mark mx-auto mb-3">AI</div>
                    <div class="d-inline-flex flex-wrap gap-2 mb-3">
                        <a href="<?php echo e(route('register')); ?>" class="btn <?php echo e(($registrationRole ?? 'candidate') === 'candidate' ? 'btn-dark' : 'btn-outline-dark'); ?> rounded-pill px-4">Candidate Register</a>
                        <a href="<?php echo e(route('register.admin')); ?>" class="btn <?php echo e(($registrationRole ?? 'candidate') === 'admin' ? 'btn-dark' : 'btn-outline-dark'); ?> rounded-pill px-4">Admin Register</a>
                    </div>
                    <h2 class="fw-bold mb-2"><?php echo e($pageTitle ?? 'Registration'); ?></h2>
                    <p class="text-muted mb-0"><?php echo e($pageSubtitle ?? 'Create your account.'); ?></p>
                </div>

                <form action="<?php echo e($submitRoute ?? route('register.submit')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" value="<?php echo e(old('name')); ?>" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" value="<?php echo e(old('phone')); ?>" class="form-control rounded-3">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-dark rounded-pill px-4 w-100 mt-4">Register</button>
                </form>

                <div class="text-center mt-4">
                    <small class="text-muted">Already registered? <a href="<?php echo e(route('login')); ?>">Login here</a></small>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/manavendrasinh/Desktop/ai_resume_analyzer/resources/views/auth/register.blade.php ENDPATH**/ ?>