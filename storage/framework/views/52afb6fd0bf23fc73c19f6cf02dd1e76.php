<?php $__env->startSection('title', 'Admin Report View | AI Resume Analyzer System'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('partials.page-header', [
        'title' => 'Report Details',
        'subtitle' => 'Admin review of candidate analysis result, extracted data, and scoring output.',
        'badge' => 'Admin Report View',
        'breadcrumbs' => [
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Reports', 'url' => route('admin.reports.index')],
            ['label' => 'Report Details'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="dashboard-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-semibold mb-1"><?php echo e($resume->file_name); ?></h5>
                <p class="text-muted mb-0">Candidate: <?php echo e($resume->user?->name ?? 'Unknown User'); ?> | Job Role: <?php echo e($resume->jobRole?->title ?? 'Not Selected'); ?></p>
            </div>
            <button type="button" class="btn btn-outline-dark rounded-pill px-4" onclick="window.print()">Print Report</button>
        </div>
    </div>

    <?php echo $__env->make('user.reports.partials.report-content', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/manavendrasinh/Desktop/ai_resume_analyzer/resources/views/admin/reports/show.blade.php ENDPATH**/ ?>