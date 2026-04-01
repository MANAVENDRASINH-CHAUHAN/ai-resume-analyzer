<?php $__env->startSection('title', 'Analysis Reports | AI Resume Analyzer System'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('partials.page-header', [
        'title' => 'Analysis Reports',
        'subtitle' => 'Search and review completed analysis reports across all users.',
        'badge' => 'Admin Module',
        'breadcrumbs' => [
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Reports'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="dashboard-card mb-4">
        <form method="GET" action="<?php echo e(route('admin.reports.index')); ?>">
            <div class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="Search by file name, candidate, email, or job role">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table dashboard-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Candidate</th>
                        <th>Job Role</th>
                        <th>Total Score</th>
                        <th>Recommendation</th>
                        <th>Uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resume): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($resume->file_name); ?></td>
                            <td><?php echo e($resume->user?->name ?? 'Unknown User'); ?></td>
                            <td><?php echo e($resume->jobRole?->title ?? 'Not Selected'); ?></td>
                            <td><?php echo e($resume->resumeScore?->total_score ?? '-'); ?></td>
                            <td>
                                <span class="badge rounded-pill <?php echo e($resume->resumeScore?->recommendation_badge_class ?? 'text-bg-secondary'); ?>">
                                    <?php echo e($resume->resumeScore?->recommendation_label ?? 'N/A'); ?>

                                </span>
                            </td>
                            <td><?php echo e(optional($resume->uploaded_at)->format('d M Y') ?? '-'); ?></td>
                            <td>
                                <a href="<?php echo e(route('admin.reports.show', $resume)); ?>" class="btn btn-sm btn-outline-primary rounded-pill">View Report</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No reports found.</td>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/manavendrasinh/Desktop/ai_resume_analyzer/resources/views/admin/reports/index.blade.php ENDPATH**/ ?>