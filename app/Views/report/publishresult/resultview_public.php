<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result - Tamil Kalaivizha</title>
    <link rel="shortcut icon" type="image/x-icon" href="https://photos.app.goo.gl/4Tv9UCTHxEAN44Pm7">
    <link rel="mask-icon" href="https://photos.app.goo.gl/4Tv9UCTHxEAN44Pm7" color="#111">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .result-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .result-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .fest-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .fest-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .fest-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .fest-body {
            padding: 1.5rem;
        }

        .btn-result {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 8px;
            color: white;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 0.5rem;
            width: calc(100% - 1rem);
            text-align: left;
        }

        .btn-result:hover {
            background: linear-gradient(135deg, #218838 0%, #1ab394 100%);
            color: white;
            transform: translateX(5px);
        }

        .badge-count {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.8rem;
            float: right;
        }

        .footer-text {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
            background: white;
            border-radius: 15px;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="result-header">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="result-title">
                        <i class="fas fa-trophy me-3"></i>
                        Tamil Kalaivizha Results
                    </h1>
                    <p class="lead">View competition results and school points</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php echo form_open('', array('id' => 'festrep', 'name' => 'festrep')); ?>
        <input type="hidden" id="hidfestId" name="hidfestId" value="">

        <div class="row">
            <?php
            $bg = 0;
            foreach ($fest as $key) {
                if ($bg == 0) {
                    $bgcolor = "#fff";
                    $bg = 1;
                } else {
                    $bgcolor = "#fff";
                    $bg = 0;
                }
                ?>
                <div class="col-xl-4 col-md-6 col-sm-12 col-lg-12">
                    <div class="fest-card">
                        <div class="fest-header">
                            <?php echo $key['fest_name']; ?>
                            <span class="badge-count">2</span>
                        </div>
                        <div class="fest-body">
                            <button type="button" class="btn-result" onclick="clickresultdeclered('<?php echo $key['fest_id']; ?>')">
                                <i class="fas fa-medal me-2"></i>Result - Declared
                            </button>
                            <button type="button" class="btn-result" onclick="cickpointdeclared('<?php echo $key['fest_id']; ?>')">
                                <i class="fas fa-star me-2"></i>Points - School
                            </button>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <!-- All Festival Section -->
            <div class="col-xl-4 col-md-12 col-sm-12 col-lg-12">
                <div class="fest-card">
                    <div class="fest-header">
                        All Festival
                    </div>
                    <div class="fest-body"><br/>
                        <a href="<?php echo base_url(); ?>publishresult/publicresult/allresults" class="btn-result">
                            <i class="fas fa-medal me-2"></i>Result - Declared
                        </a>
						<br/><br/><br/>
                        <a href="<?php echo base_url(); ?>publishresult/publicresult/allfestschools" class="btn-result">
                            <i class="fas fa-star me-2"></i>Points - School
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status View Section -->
            <div class="col-xl-4 col-md-6 col-sm-12 col-lg-12">
                <div class="fest-card">
                    <div class="fest-header">
                        Status View
                    </div>
                    <div class="fest-body">
						<br/>
                        <a href="<?php echo base_url(); ?>publishresult/publicresult/allresults" class="btn-result">
                            <i class="fas fa-list me-2"></i>All Results
                        </a>
						<br/><br/><br/>
                        <a href="<?php echo base_url(); ?>publishresult/publicresult/festval_status" class="btn-result">
                            <i class="fas fa-chart-line me-2"></i>Status of Festival
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php echo form_close(); ?>

        <div class="footer-text">
            <p><i class="fas fa-info-circle me-2"></i>Click on any button above to view results and school points</p>
            <small class="text-muted">Tamil Kalaivizha - Public Results Portal</small>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        var path = '<?php echo base_url(); ?>';

        function clickresultdeclered(festid) {
            $('#hidfestId').val(festid);
            $('#festrep').attr('action', path + 'publishresult/publicresult/result_declared');
            $('#festrep').submit();
        }

        function cickpointdeclared(festid) {
            $('#hidfestId').val(festid);
            $('#festrep').attr('action', path + 'publishresult/publicresult/schoolpoints');
            $('#festrep').submit();
        }
    </script>
</body>
</html>
