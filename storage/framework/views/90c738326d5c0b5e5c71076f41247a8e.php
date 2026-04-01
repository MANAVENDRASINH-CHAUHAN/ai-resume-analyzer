<?php $__env->startSection('title', 'Activity Logs | AI Resume Analyzer System'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('partials.page-header', [
        'title' => 'Activity Logs',
        'subtitle' => 'Latest system activity, admin actions, uploads, analysis events, and log messages.',
        'badge' => 'Admin Module',
        'breadcrumbs' => [
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Activity Logs'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="dashboard-card mb-4">
        <form method="GET" action="<?php echo e(route('admin.activity-logs.index')); ?>">
            <div class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="Search by activity type, message, or IP address">
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
                        <th>Date</th>
                        <th>Type</th>
                        <th>Message</th>
                        <th>User</th>
                        <th>Resume</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($log->created_at?->format('d M Y, h:i A')); ?></td>
                            <td><?php echo e($log->activity_type); ?></td>
                            <td><?php echo e($log->activity_message); ?></td>
                            <td><?php echo e($log->user?->email ?? '-'); ?></td>
                            <td><?php echo e($log->resume?->file_name ?? '-'); ?></td>
                            <td><?php echo e($log->ip_address ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No activity logs found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($logs->hasPages()): ?>
            <div class="mt-4">
                <?php echo e($logs->links()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/manavendrasinh/Desktop/ai_resume_analyzer/resources/views/admin/activity_logs/index.blade.php ENDPATH**/ ?>