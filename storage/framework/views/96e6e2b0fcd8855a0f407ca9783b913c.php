<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom sticky-top app-navbar">
    <div class="container-fluid px-3 px-lg-4">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="<?php echo e(route('home')); ?>">
            <span>AI Resume Analyzer</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('home') ? 'active' : ''); ?>" href="<?php echo e(route('home')); ?>">Home</a>
                </li>

                <?php if(auth()->guard()->guest()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('login') ? 'active' : ''); ?>" href="<?php echo e(route('login')); ?>">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary rounded-pill px-4 <?php echo e(request()->routeIs('register', 'register.admin') ? 'active' : ''); ?>" href="<?php echo e(route('register')); ?>">Register</a>
                    </li>
                <?php else: ?>
                    <?php if(auth()->user()->role === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('admin.dashboard')); ?>">Admin Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.job-roles.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.job-roles.index')); ?>">Manage Job Roles</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('user.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('user.dashboard')); ?>">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('user.resumes.*') ? 'active' : ''); ?>" href="<?php echo e(route('user.resumes.index')); ?>">My Resumes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('user.profile') ? 'active' : ''); ?>" href="<?php echo e(route('user.profile')); ?>">Profile</a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <button
                            type="button"
                            class="btn btn-link nav-link position-relative notification-trigger"
                            data-notification-trigger
                            data-mark-read-url="<?php echo e(route('notifications.mark-all-read')); ?>"
                            title="Mark all notifications as read"
                        >
                            <i class="bi bi-bell fs-5"></i>
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-pill d-none"
                                data-notification-badge
                                data-url="<?php echo e(route('notifications.unread-count')); ?>"
                            >
                                0
                            </span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <span class="navbar-text small text-muted px-lg-2"><?php echo e(auth()->user()->name); ?></span>
                    </li>
                    <li class="nav-item">
                        <form action="<?php echo e(route('logout')); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-outline-dark rounded-pill px-4">Logout</button>
                        </form>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php /**PATH /Users/manavendrasinh/Desktop/ai_resume_analyzer/resources/views/partials/navbar.blade.php ENDPATH**/ ?>