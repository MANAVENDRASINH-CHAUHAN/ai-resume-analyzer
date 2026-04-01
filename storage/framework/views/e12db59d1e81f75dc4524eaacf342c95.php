<?php
    $links = [
        [
            'label' => 'Dashboard',
            'route' => 'admin.dashboard',
            'match' => 'admin.dashboard',
            'icon' => 'bi-grid',
        ],
        [
            'label' => 'Job Roles',
            'route' => 'admin.job-roles.index',
            'match' => 'admin.job-roles.*',
            'icon' => 'bi-briefcase',
        ],
        [
            'label' => 'Resumes',
            'route' => 'admin.resumes.index',
            'match' => 'admin.resumes.*',
            'icon' => 'bi-file-earmark-richtext',
        ],
        [
            'label' => 'Reports',
            'route' => 'admin.reports.index',
            'match' => 'admin.reports.*',
            'icon' => 'bi-bar-chart-line',
        ],
    ];
?>

<aside class="admin-sidebar">
    <div class="sidebar-user-card mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="sidebar-user-icon">
                <i class="bi bi-person-workspace"></i>
            </div>
            <div>
                <div class="fw-semibold"><?php echo e(auth()->user()->name); ?></div>
                <div class="small text-white-50">Administrator</div>
            </div>
        </div>
    </div>

    <nav class="nav flex-column gap-2">
        <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route($link['route'])); ?>" class="sidebar-link <?php echo e(request()->routeIs($link['match']) ? 'active' : ''); ?>">
                <i class="bi <?php echo e($link['icon']); ?>"></i>
                <span><?php echo e($link['label']); ?></span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>
</aside>
<?php /**PATH /Users/manavendrasinh/Desktop/ai_resume_analyzer/resources/views/partials/admin-sidebar.blade.php ENDPATH**/ ?>