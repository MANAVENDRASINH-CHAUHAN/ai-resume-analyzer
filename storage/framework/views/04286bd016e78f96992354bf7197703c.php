<?php $__env->startSection('title', 'Manage Resumes | AI Resume Analyzer System'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('partials.page-header', [
        'title' => 'Manage Resumes',
        'subtitle' => 'Search and monitor all uploaded resumes across the system.',
        'badge' => 'Admin Module',
        'breadcrumbs' => [
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Resumes'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="dashboard-card mb-4">
        <form method="GET" action="<?php echo e(route('admin.resumes.index')); ?>">
            <div class="row g-3">
                <div class="col-md-7">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="Search by file name, user, or job role">
                </div>
                <div class="col-md-3">
                    <select name="analysis_status" class="form-select">
                        <option value="">All Analysis Status</option>
                        <option value="pending" <?php if(request('analysis_status') === 'pending'): echo 'selected'; endif; ?>>Pending</option>
                        <option value="in_progress" <?php if(request('analysis_status') === 'in_progress'): echo 'selected'; endif; ?>>In Progress</option>
                        <option value="completed" <?php if(request('analysis_status') === 'completed'): echo 'selected'; endif; ?>>Completed</option>
                        <option value="error" <?php if(request('analysis_status') === 'error'): echo 'selected'; endif; ?>>Error</option>
                    </select>
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
                        <th>Upload Status</th>
                        <th>Analysis Status</th>
                        <th>Score</th>
                        <th>Uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $resumes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resume): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($resume->file_name); ?></td>
                            <td><?php echo e($resume->user?->name ?? 'Unknown User'); ?></td>
                            <td><?php echo e($resume->jobRole?->title ?? 'Not Selected'); ?></td>
                            <td><span class="badge rounded-pill <?php echo e($resume->upload_status_badge_class); ?>"><?php echo e(ucfirst($resume->upload_status)); ?></span></td>
                            <td><span class="badge rounded-pill <?php echo e($resume->analysis_status_badge_class); ?>"><?php echo e(ucfirst($resume->analysis_status)); ?></span></td>
                            <td><?php echo e($resume->resumeScore?->total_score ?? '-'); ?></td>
                            <td><?php echo e(optional($resume->uploaded_at)->format('d M Y') ?? '-'); ?></td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="<?php echo e(route('admin.resumes.show', $resume)); ?>" class="btn btn-sm btn-outline-primary rounded-pill">View</a>
                                    <a href="<?php echo e(route('admin.resumes.download', $resume)); ?>" class="btn btn-sm btn-outline-dark rounded-pill">Download</a>
                                    <form action="<?php echo e(route('admin.resumes.destroy', $resume)); ?>" method="POST" onsubmit="return confirm('Delete this resume?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No resumes found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($resumes->hasPages()): ?>
            <div class="mt-4">
                <?php echo e($resumes->links()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/manavendrasinh/Desktop/ai_resume_analyzer/resources/views/admin/resumes/index.blade.php ENDPATH**/ ?>