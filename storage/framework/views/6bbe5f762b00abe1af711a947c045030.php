<?php $__env->startSection('title', 'Manage Job Roles | AI Resume Analyzer System'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('partials.page-header', [
        'title' => 'Manage Job Roles',
        'subtitle' => 'Create, edit, activate, or delete job roles used for resume analysis.',
        'badge' => 'Admin Module',
        'breadcrumbs' => [
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Job Roles'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="dashboard-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <form method="GET" action="<?php echo e(route('admin.job-roles.index')); ?>" class="row g-3 flex-grow-1">
                <div class="col-md-6">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="Search by title or description">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Active</option>
                        <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
            <a href="<?php echo e(route('admin.job-roles.create')); ?>" class="btn btn-outline-dark rounded-pill px-4">Add Job Role</a>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table dashboard-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Required Skills</th>
                        <th>Preferred Experience</th>
                        <th>Min Score</th>
                        <th>Status</th>
                        <th>Resumes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $jobRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jobRole): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($jobRole->title); ?></td>
                            <td>
                                <?php $__empty_2 = true; $__currentLoopData = $jobRole->required_skills_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                    <span class="skill-pill"><?php echo e($skill); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                    <span class="text-muted">No skills</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($jobRole->preferred_experience ?: '-'); ?></td>
                            <td><?php echo e($jobRole->min_score); ?></td>
                            <td><span class="badge rounded-pill <?php echo e($jobRole->status_badge_class); ?>"><?php echo e(ucfirst($jobRole->status)); ?></span></td>
                            <td><?php echo e($jobRole->resumes_count); ?></td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="<?php echo e(route('admin.job-roles.edit', $jobRole)); ?>" class="btn btn-sm btn-outline-primary rounded-pill">Edit</a>
                                    <form action="<?php echo e(route('admin.job-roles.destroy', $jobRole)); ?>" method="POST" onsubmit="return confirm('Delete this job role?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No job roles found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($jobRoles->hasPages()): ?>
            <div class="mt-4">
                <?php echo e($jobRoles->links()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/manavendrasinh/Desktop/ai_resume_analyzer/resources/views/admin/job_roles/index.blade.php ENDPATH**/ ?>