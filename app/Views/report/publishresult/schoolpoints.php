<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Points - Tamil Kalaivizha</title>

    <link rel="shortcut icon" type="image/x-icon" href="https://photos.app.goo.gl/4Tv9UCTHxEAN44Pm7">
    <link rel="mask-icon" href="https://photos.app.goo.gl/4Tv9UCTHxEAN44Pm7" color="#111">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://s3-us-west-2.amazonaws.com/s.cdpn.io/4579/bootstrap-table.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .points-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .points-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .table th {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .rank-badge {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .points-badge {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .back-btn {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            border: none;
            border-radius: 8px;
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .back-btn:hover {
            color: white;
            background: linear-gradient(135deg, #5a6268 0%, #545b62 100%);
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .no-results i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        /* Mobile Card Layout */
        .mobile-cards {
            display: block;
        }

        /* Desktop Table Layout */
        .desktop-table {
            display: none;
        }

        @media (min-width: 992px) {
            .mobile-cards {
                display: none;
            }

            .desktop-table {
                display: block;
            }
        }

        .school-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            margin-bottom: 1rem;
        }

        .school-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .school-rank {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .school-points {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .school-details {
            padding: 1rem;
        }

        .school-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }

        .school-code {
            color: #666;
            font-size: 0.9rem;
        }

        .animate__bounceIn {
            animation: bounceIn 1s ease-in-out;
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
    <div class="points-header">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="points-title">
                        <i class="fas fa-star me-3"></i>
                        School Points
                    </h1>
                    <p class="lead">Overall School Performance - Tamil Kalaivizha</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <a href="<?php echo base_url(); ?>publishresult/publicresult/resultview" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Back to Results
        </a>

        <!-- Cup Icon Animation -->
        <div id="cup-animation" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
            <i class="fas fa-trophy text-warning" style="font-size: 5rem;"></i>
        </div>

        <?php if (count($details) > 0) { ?>
            <div class="table-container">
                <h4 class="text-center mb-4">
                    <i class="fas fa-trophy text-success me-2 animate__animated animate__bounceIn"></i>
                    School Wise Overall Point Table
                </h4>

                <!-- Mobile Card Layout -->
                <div class="mobile-cards">
                    <div class="row">
                        <?php
                        $count = 0;
                        foreach ($details as $value) {
                            $count++;
                            ?>
                            <div class="col-12 mb-4">
                                <div class="card school-card animate__animated animate__fadeInUp">
                                    <div class="card-body school-details">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="school-rank">Rank <?php echo $count; ?></div>
                                                <div class="school-name"><?php echo $value['school_name']; ?></div>
                                                <div class="school-code">School Code: <?php echo $value['school_code']; ?></div>
                                            </div>
                                            <div class="school-points"><?php echo $value['spoint']; ?> pts</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>

                <!-- Desktop Table Layout -->
                <div class="desktop-table">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag me-2"></i>Rank</th>
                                    <th><i class="fas fa-school me-2"></i>School Details</th>
                                    <th><i class="fas fa-star me-2"></i>Total Points</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 0;
                                foreach ($details as $value) {
                                    $count++;
                                    ?>
                                    <tr>
                                        <td><span class="rank-badge"><?php echo $count; ?></span></td>
                                        <td>
                                            <strong><?php echo $value['school_code']; ?></strong><br>
                                            <span class="text-muted"><?php echo wordwrap($value['school_name'], 60, '<br>'); ?></span>
                                        </td>
                                        <td><span class="points-badge"><?php echo $value['spoint']; ?> pts</span></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="no-results">
                <i class="fas fa-clock text-muted"></i>
                <h4>Try later...... Points are Not Ready</h4>
                <p class="text-muted">School points will be available once all competitions are completed.</p>
            </div>
        <?php } ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            // Show cup animation and trigger confetti
            $('#cup-animation').show().addClass('animate__animated animate__bounceIn');
            setTimeout(function() {
                $('#cup-animation').hide();
            }, 2000);

            // Trigger confetti
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 }
            });
        });
    </script>
    <!-- Confetti JS -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
</body>

</html>
