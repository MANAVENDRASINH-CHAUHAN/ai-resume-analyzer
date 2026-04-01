<?php $__env->startSection('title', 'Manage Users | AI Resume Analyzer System'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('partials.page-header', [
        'title' => 'Manage Users',
        'subtitle' => 'Search, filter, edit, and manage candidate and admin accounts.',
        'badge' => 'Admin Module',
        'breadcrumbs' => [
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Users'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="dashboard-card mb-4">
        <form method="GET" action="<?php echo e(route('admin.users.index')); ?>">
            <div class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control" placeholder="Search by name or email">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin" <?php if(request('role') === 'admin'): echo 'selected'; endif; ?>>Admin</option>
                        <option value="candidate" <?php if(request('role') === 'candidate'): echo 'selected'; endif; ?>>Candidate</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Active</option>
                        <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
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
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Resumes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($user->name); ?></td>
                            <td><?php echo e($user->email); ?></td>
                            <td><?php echo e($user->phone ?: '-'); ?></td>
                            <td><span class="badge rounded-pill <?php echo e($user->role_badge_class); ?>"><?php echo e(ucfirst($user->role)); ?></span></td>
                            <td><span class="badge rounded-pill <?php echo e($user->status_badge_class); ?>"><?php echo e(ucfirst($user->status)); ?></span></td>
                            <td><?php echo e($user->resumes_count); ?></td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="btn btn-sm btn-outline-primary rounded-pill">View</a>
                                    <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn btn-sm btn-outline-dark rounded-pill">Edit</a>
                                    <form action="<?php echo e(route('admin.users.destroy', $user)); ?>" method="POST" onsubmit="return confirm('Delete this user account?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($users->hasPages()): ?>
            <div class="mt-4">
                <?php echo e($users->links()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/manavendrasinh/Desktop/ai_resume_analyzer/resources/views/admin/users/index.blade.php ENDPATH**/ ?>