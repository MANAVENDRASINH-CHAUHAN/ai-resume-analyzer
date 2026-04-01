<?php $__env->startSection('title', 'Resume Reports | AI Resume Analyzer System'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('partials.page-header', [
        'title' => 'Resume Reports',
        'subtitle' => 'View all completed analysis reports for your uploaded resumes.',
        'badge' => 'Candidate Reports',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Dashboard', 'url' => route('user.dashboard')],
            ['label' => 'Resume Reports'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="dashboard-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-semibold mb-1">Completed Analysis History</h5>
                <p class="text-muted mb-0">Only resumes with completed analysis appear here. Each report is linked with score, skills, and extracted details.</p>
            </div>
            <a href="<?php echo e(route('user.resumes.index')); ?>" class="btn btn-outline-dark rounded-pill px-4">Go to Resume History</a>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table dashboard-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Resume File</th>
                        <th>Target Job Role</th>
                        <th>Total Score</th>
                        <th>Analysis Status</th>
                        <th>Uploaded Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resume): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?php echo e($resume->file_name); ?></div>
                                <div class="small text-muted text-uppercase"><?php echo e($resume->file_type); ?></div>
                            </td>
                            <td><?php echo e($resume->jobRole?->title ?? 'Not Selected'); ?></td>
                            <td>
                                <span class="badge rounded-pill <?php echo e($resume->resumeScore?->recommendation_badge_class ?? 'text-bg-secondary'); ?>">
                                    <?php echo e($resume->resumeScore?->total_score ?? 0); ?>/100
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill <?php echo e($resume->analysis_status_badge_class); ?>">
                                    <?php echo e(ucfirst($resume->analysis_status)); ?>

                                </span>
                            </td>
                            <td><?php echo e(optional($resume->uploaded_at)->format('d M Y, h:i A') ?? '-'); ?></td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="<?php echo e(route('user.reports.show', $resume)); ?>" class="btn btn-sm btn-outline-primary rounded-pill">View Report</a>
                                    <a href="<?php echo e(route('user.reports.print', $resume)); ?>" class="btn btn-sm btn-outline-dark rounded-pill" target="_blank">Print Report</a>
                                    <a href="<?php echo e(route('user.resumes.show', $resume)); ?>" class="btn btn-sm btn-outline-success rounded-pill">Resume Details</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="py-5 text-center">
                                <div class="empty-state-box">
                                    <div class="mb-2 fs-3 text-primary"><i class="bi bi-file-earmark-bar-graph"></i></div>
                                    <div class="fw-semibold mb-1">No completed reports available</div>
                                    <div class="text-muted mb-3">Upload and analyze a resume first to generate a full report.</div>
                                    <a href="<?php echo e(route('user.resumes.index')); ?>" class="btn btn-primary rounded-pill px-4">Open Resume History</a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($reports->hasPages()): ?>
            <div class="mt-4">
                <?php echo e($reports->links()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/manavendrasinh/Desktop/ai_resume_analyzer/resources/views/user/reports/index.blade.php ENDPATH**/ ?>