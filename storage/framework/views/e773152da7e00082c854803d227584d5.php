<?php $__env->startSection('title', 'Profile'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('partials.page-header', [
        'title' => 'Profile',
        'subtitle' => 'This placeholder profile page keeps the interface consistent while full editing features are added later.',
        'badge' => 'Candidate Account',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Dashboard', 'url' => route('user.dashboard')],
            ['label' => 'Profile'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" value="<?php echo e(auth()->user()->name); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control" value="<?php echo e(auth()->user()->email); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" value="<?php echo e(auth()->user()->phone); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control text-capitalize" value="<?php echo e(auth()->user()->role); ?>" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/manavendrasinh/Desktop/ai_resume_analyzer/resources/views/user/profile.blade.php ENDPATH**/ ?>