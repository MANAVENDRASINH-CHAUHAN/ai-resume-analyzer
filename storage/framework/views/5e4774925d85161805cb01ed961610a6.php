<?php $__env->startSection('title', 'Analysis Report | AI Resume Analyzer System'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('partials.page-header', [
        'title' => 'Analysis Report',
        'subtitle' => 'Detailed result view for the selected analyzed resume.',
        'badge' => 'Completed Report',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Dashboard', 'url' => route('user.dashboard')],
            ['label' => 'Resume Reports', 'url' => route('user.reports.index')],
            ['label' => 'Analysis Report'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="dashboard-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-semibold mb-1"><?php echo e($resume->file_name); ?></h5>
                <p class="text-muted mb-0">Professional result report with score summary, skills, and improvement guidance.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?php echo e(route('user.reports.print', $resume)); ?>" target="_blank" class="btn btn-outline-dark rounded-pill px-4">Print Report</a>
                <a href="<?php echo e(route('user.resumes.show', $resume)); ?>" class="btn btn-outline-primary rounded-pill px-4">Resume Details</a>
            </div>
        </div>
    </div>

    <?php echo $__env->make('user.reports.partials.report-content', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/manavendrasinh/Desktop/ai_resume_analyzer/resources/views/user/reports/show.blade.php ENDPATH**/ ?>