<?php
    $breadcrumbs = $breadcrumbs ?? [];
    $badge = $badge ?? null;
?>

<div class="page-header-card mb-4">
    <?php if(! empty($breadcrumbs)): ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $breadcrumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="breadcrumb-item <?php echo e($loop->last ? 'active' : ''); ?>" <?php if($loop->last): ?> aria-current="page" <?php endif; ?>>
                        <?php if(! $loop->last && ! empty($breadcrumb['url'])): ?>
                            <a href="<?php echo e($breadcrumb['url']); ?>"><?php echo e($breadcrumb['label']); ?></a>
                        <?php else: ?>
                            <?php echo e($breadcrumb['label']); ?>

                        <?php endif; ?>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ol>
        </nav>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <h1 class="page-title mb-1"><?php echo e($title ?? 'Page Title'); ?></h1>
            <p class="text-muted mb-0"><?php echo e($subtitle ?? 'Page subtitle goes here.'); ?></p>
        </div>

        <?php if($badge): ?>
            <span class="badge text-bg-light border rounded-pill px-3 py-2"><?php echo e($badge); ?></span>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /Users/manavendrasinh/Desktop/ai_resume_analyzer/resources/views/partials/page-header.blade.php ENDPATH**/ ?>