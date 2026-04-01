<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Report | AI Resume Analyzer System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/app.css')); ?>" rel="stylesheet">
    <style>
        body {
            background: #ffffff;
        }

        .print-toolbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: #ffffff;
            padding: 1rem 0;
        }

        @media print {
            .print-toolbar {
                display: none !important;
            }

            body {
                background: #ffffff !important;
            }

            .dashboard-card,
            .stat-card,
            .page-header-card {
                box-shadow: none !important;
                border: 1px solid #d1d5db !important;
            }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="print-toolbar no-print d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1">Printable Analysis Report</h3>
                <p class="text-muted mb-0"><?php echo e($resume->file_name); ?></p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="window.print()">Print Now</button>
                <a href="<?php echo e(route('user.reports.show', $resume)); ?>" class="btn btn-outline-dark rounded-pill px-4">Back to Report</a>
            </div>
        </div>

        <div class="page-header-card mb-4">
            <h1 class="page-title mb-1">AI Resume Analyzer System</h1>
            <p class="text-muted mb-0">Print-friendly resume analysis report for project submission or review.</p>
        </div>

        <?php echo $__env->make('user.reports.partials.report-content', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</body>
</html>
<?php /**PATH /Users/manavendrasinh/Desktop/ai_resume_analyzer/resources/views/user/reports/print.blade.php ENDPATH**/ ?>