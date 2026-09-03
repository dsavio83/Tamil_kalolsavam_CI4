<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sub-District Points - Tamil Kalaivizha</title>

    <link rel="shortcut icon" type="image/x-icon" href="https://photos.app.goo.gl/4Tv9UCTHxEAN44Pm7">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'TAU-Pallai', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .points-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .points-title {
            font-size: 2.2rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid #e2e8f0;
        }

        .table th {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            border: none;
            font-weight: 600;
            font-size: 1rem;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        .rank-badge {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .points-badge {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 0.4rem 0.9rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 1rem;
        }

        .back-btn {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            border: none;
            border-radius: 50rem;
            color: white;
            padding: 0.6rem 1.4rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .back-btn:hover {
            color: white;
            background: linear-gradient(135deg, #495057 0%, #343a40 100%);
            transform: translateY(-2px);
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="points-header">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="points-title">
                        <i class="fas fa-trophy me-3 text-warning"></i>
                        Sub-District Points (துணை மாவட்ட புள்ளிகள்)
                    </h1>
                    <p class="lead mb-0">Sub-District Standings - Tamil Kalaivizha 2025-26</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <a href="<?php echo base_url(); ?>publishresult/publicresult/resultview" class="back-btn">
            <i class="fas fa-arrow-left"></i> முகப்பு முடிவுகளுக்கு செல்ல (Back to Results)
        </a>

        <?php if (!empty($details)) : ?>
            <div class="table-container">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="text-center" width="80">Rank</th>
                            <th>Sub-District Name (துணை மாவட்டம்)</th>
                            <th>Festival (பிரிவு)</th>
                            <th class="text-center" width="120">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        foreach ($details as $row) : 
                        ?>
                            <tr>
                                <td class="text-center">
                                    <span class="rank-badge">#<?php echo $rank++; ?></span>
                                </td>
                                <td class="fw-semibold">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                    <?php echo esc($row['sub_district_name'] ?? ''); ?>
                                </td>
                                <td>
                                    <?php echo esc($row['fest_name'] ?? 'General'); ?>
                                </td>
                                <td class="text-center">
                                    <span class="points-badge"><?php echo esc($row['total_points'] ?? 0); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <div class="table-container text-center py-5">
                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No Sub-District points declared yet for this category.</h4>
                <p class="text-muted small">Points will automatically appear here once results are entered and published.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>