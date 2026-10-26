<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Finished Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <style>
        @font-face {
            font-family: 'TAU-Pallai';
            src: url('<?= base_url("assets/fonts/TAU-Paalai.ttf") ?>') format('truetype');
            font-weight: normal;
        }
        @font-face {
            font-family: 'TAU-Pallai';
            src: url('<?= base_url("assets/fonts/TAU-Paalai Bold.ttf") ?>') format('truetype');
            font-weight: bold;
        }
        .font-tamil {
            font-family: 'TAU-Pallai', 'Mukta Malar', 'Noto Sans Tamil', sans-serif !important;
            font-size: 14px !important;
        }
        body {
            background-color: #f8fafc;
            color: #1e293b;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .topbar-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            padding: 0.85rem 1.25rem;
            margin-bottom: 1.5rem;
        }
        .main-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            background: #fff;
        }
        .table thead th {
            background-color: #002d72;
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
        }
    </style>
</head>
<body class="py-4">
<div class="container-fluid px-3 px-md-4">
    <!-- Top Bar with Back Button -->
    <div class="topbar-card d-flex flex-wrap align-items-center justify-content-between gap-2">
        <a href="<?= base_url(); ?>district/report/resultindex/resultview" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Back to Results
        </a>
        <h5 class="mb-0 fw-bold text-success">Finished Items Report</h5>
        <a href="<?= base_url(); ?>district/report/resultindex/festval_status" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold">
            <i class="bi bi-speedometer2 me-1"></i> Festival Monitor
        </a>
    </div>

    <div class="card main-card">
        <div class="card-header bg-success text-white py-3 px-4">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i>
                <span>Finished Items List</span>
                <span class="badge bg-white text-success ms-auto"><?= count($totitem); ?> Completed</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 75vh; overflow-y: auto;">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead>
                        <tr class="text-center">
                            <th scope="col" style="width: 80px;">#</th>
                            <th scope="col" style="width: 140px;">Item Code</th>
                            <th scope="col" class="text-start ps-3">Item Name</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    $sn = 1;
                    foreach ($totitem as $value): 
                    ?>
                        <tr>
                            <td class="text-center text-muted fw-bold"><?= $sn++; ?></td>
                            <td class="text-center">
                                <span class="badge bg-secondary px-3 py-1.5"><?= htmlspecialchars($value['item_code']); ?></span>
                            </td>
                            <td class="ps-3 font-tamil fw-semibold">
                                <?= htmlspecialchars($value['item_name']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>