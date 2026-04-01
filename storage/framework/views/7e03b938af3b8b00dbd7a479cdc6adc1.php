<?php $__env->startSection('title', 'Home | AI Resume Analyzer System'); ?>

<?php $__env->startSection('content'); ?>
    <section class="hero-panel home-hero mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <h1 class="display-5 fw-bold mb-3">AI Resume Analyzer System</h1>
                <p class="lead text-white-50 mb-4">
                    Upload resumes, get instant analysis scores, detect missing skills, compare profiles with job roles,
                    and view detailed reports while admins manage users, resumes, reports, and activity logs from one panel.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <?php if(auth()->guard()->guest()): ?>
                        <a href="<?php echo e(route('register')); ?>" class="btn btn-light rounded-pill px-4 py-2">Register as Candidate</a>
                        <a href="<?php echo e(route('register.admin')); ?>" class="btn btn-outline-light rounded-pill px-4 py-2">Register as Admin</a>
                        <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-light rounded-pill px-4 py-2">Login</a>
                    <?php else: ?>
                        <?php if(auth()->user()->role === 'admin'): ?>
                            <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-light rounded-pill px-4 py-2">Open Admin Dashboard</a>
                        <?php else: ?>
                            <a href="<?php echo e(route('user.dashboard')); ?>" class="btn btn-light rounded-pill px-4 py-2">Open Candidate Dashboard</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="dashboard-card hero-summary-card mb-3 home-visual-card">
                    <div class="home-visual-glow"></div>
                    <div class="home-visual-orb orb-one"></div>
                    <div class="home-visual-orb orb-two"></div>

                    <div class="home-visual-panel panel-back"></div>

                    <div class="home-visual-panel panel-front">
                        <div class="visual-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <div class="visual-line line-accent w-50"></div>
                        <div class="visual-line w-100"></div>
                        <div class="visual-line w-75"></div>
                        <div class="visual-line w-100"></div>
                        <div class="visual-grid">
                            <div class="visual-block"></div>
                            <div class="visual-block"></div>
                            <div class="visual-block"></div>
                            <div class="visual-block"></div>
                        </div>
                    </div>

                    <div class="home-visual-mini mini-top">
                        <span class="mini-bar"></span>
                        <span class="mini-bar short"></span>
                    </div>

                    <div class="home-visual-mini mini-bottom">
                        <span class="mini-circle"></span>
                        <span class="mini-line"></span>
                    </div>
                </div>

                <div class="dashboard-card hero-summary-card">
                    <h6 class="fw-semibold text-dark mb-3">Live Analysis Flow</h6>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="home-status-pill status-uploaded">Uploaded</span>
                        <span class="home-status-pill status-processing">Parsing</span>
                        <span class="home-status-pill status-processing">Analyzing</span>
                        <span class="home-status-pill status-completed">Completed</span>
                    </div>
                    <div class="progress mb-2" style="height: 10px;">
                        <div class="progress-bar" style="width: 85%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/manavendrasinh/Desktop/ai_resume_analyzer/resources/views/home.blade.php ENDPATH**/ ?>