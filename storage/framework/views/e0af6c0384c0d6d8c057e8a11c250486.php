<?php $__env->startSection('title', 'Upload Resume | AI Resume Analyzer System'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('partials.page-header', [
        'title' => 'Upload Resume',
        'subtitle' => 'Upload your resume in PDF, DOC, or DOCX format and select a target job role for future analysis.',
        'badge' => 'Candidate Upload',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Dashboard', 'url' => route('user.dashboard')],
            ['label' => 'My Resumes', 'url' => route('user.resumes.index')],
            ['label' => 'Upload Resume'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="feature-icon">
                        <i class="bi bi-cloud-arrow-up"></i>
                    </div>
                    <div>
                        <h5 class="fw-semibold mb-1">Resume Upload Form</h5>
                        <p class="text-muted mb-0">The target job role is required so the same resume can be matched with the correct role in future analysis phases.</p>
                    </div>
                </div>

                <form action="<?php echo e(route('user.resumes.store')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <div class="mb-4">
                        <label for="job_role_id" class="form-label fw-medium">Target Job Role</label>
                        <select name="job_role_id" id="job_role_id" class="form-select <?php $__errorArgs = ['job_role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Select a job role</option>
                            <?php $__currentLoopData = $jobRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jobRole): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($jobRole->id); ?>" <?php if(old('job_role_id') == $jobRole->id): echo 'selected'; endif; ?>>
                                    <?php echo e($jobRole->title); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div class="form-text">This helps the system compare your resume with a selected career role later.</div>
                        <?php $__errorArgs = ['job_role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-4">
                        <label for="resume_file" class="form-label fw-medium">Resume File</label>
                        <input type="file" name="resume_file" id="resume_file" class="form-control <?php $__errorArgs = ['resume_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept=".pdf,.doc,.docx" required>
                        <div class="form-text">Allowed formats: PDF, DOC, DOCX. Maximum file size: 5 MB.</div>
                        <?php $__errorArgs = ['resume_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="d-flex flex-wrap gap-3">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-upload me-2"></i>Upload Resume
                        </button>
                        <a href="<?php echo e(route('user.resumes.index')); ?>" class="btn btn-outline-dark rounded-pill px-4">Back to History</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card h-100">
                <h5 class="fw-semibold mb-3">Upload Instructions</h5>
                <ul class="text-muted ps-3 mb-0 upload-help-list">
                    <li>Use a clear and readable resume file.</li>
                    <li>Only PDF, DOC, and DOCX formats are accepted.</li>
                    <li>Choose the job role carefully because it will be used for role matching later.</li>
                    <li>After upload, the resume will be stored with pending analysis status.</li>
                </ul>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/manavendrasinh/Desktop/ai_resume_analyzer/resources/views/user/resumes/create.blade.php ENDPATH**/ ?>