<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Festival Status - Tamil Kalaivizha</title>
    <link rel="shortcut icon" type="image/x-icon" href="https://photos.app.goo.gl/4Tv9UCTHxEAN44Pm7">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .status-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .status-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .status-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .status-header-card {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 1.5rem;
            font-weight: 600;
            font-size: 1.3rem;
        }

        .status-body {
            padding: 2rem;
        }

        .status-item {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }

        .status-item:hover {
            transform: translateY(-2px);
        }

        .fest-name {
            color: #667eea;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .status-link {
            color: #28a745;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .status-link:hover {
            color: #218838;
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
    </style>
</head>
<body>
    <div class="status-header">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="status-title">
                        <i class="fas fa-chart-line me-3"></i>
                        Festival Status
                    </h1>
                    <p class="lead">Competition completion status and item details</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <a href="<?php echo base_url(); ?>publishresult/publicresult/resultview" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Back to Results
        </a>

        <div class="status-card">
            <div class="status-header-card">
                <i class="fas fa-clipboard-list me-2"></i>STATUS OF COMPLETION OF ITEMS
            </div>
            <div class="status-body">
                <?php if (count($details1) > 0) { ?>
                    <?php echo form_open('', array('id' => 'festdet', 'name' => 'festdet', 'target' => '_blank')); ?>
                    <input type="hidden" name="hidfestid" id="hidfestid" value="" />

                    <div class="row">
                        <div class="col-md-4"><strong>Festival (Item Total)</strong></div>
                        <div class="col-md-4"><strong>Items Completed</strong></div>
                        <div class="col-md-4"><strong>Items not Completed</strong></div>
                    </div>

                    <?php
                    $flag = 0;
                    $bg = 0;
                    foreach ($details1 as $value) {
                        for ($j = 0; $j < count($details2); $j++) {
                            if ($value['fest_id'] == $details2[$j]['fest_id']) {
                                $finished_item = $details2[$j]['pcode'];
                                $flag = 1;
                            }
                        }

                        if ($flag == 0)
                            $finished_item = 0;
                        $remind = $value['cnt'] - $finished_item;
                        ?>

                        <div class="status-item">
                            <div class="row">
                                <div class="col-md-4">
                                    <?php if ($value['cnt'] != 0) { ?>
                                        <a href="javascript:void(0)" onclick="clicktoviewfestdet_public('<?php echo $value['fest_id']; ?>')" class="status-link">
                                            <i class="fas fa-eye me-2"></i><?php echo $value['fest_name']; ?> (<?php echo $value['cnt']; ?>)
                                        </a>
                                    <?php } else { ?>
                                        <span class="fest-name"><?php echo $value['fest_name']; ?> (<?php echo $value['cnt']; ?>)</span>
                                    <?php } ?>
                                </div>
                                <div class="col-md-4">
                                    <?php if ($finished_item != 0) { ?>
                                        <a href="javascript:void(0)" onclick="finisheditemdet_public('<?php echo $value['fest_id']; ?>')" class="status-link">
                                            <i class="fas fa-check-circle me-2"></i><?php echo $finished_item; ?>
                                        </a>
                                    <?php } else { ?>
                                        <span class="text-muted">0</span>
                                    <?php } ?>
                                </div>
                                <div class="col-md-4">
                                    <?php if ($remind != 0) { ?>
                                        <a href="javascript:void(0)" onclick="remainderitemdet_public('<?php echo $value['fest_id']; ?>')" class="status-link">
                                            <i class="fas fa-clock me-2"></i><?php echo $remind; ?>
                                        </a>
                                    <?php } else { ?>
                                        <span class="text-muted">0</span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        $flag = 0;
                    }
                    ?>
                    <?php echo form_close(); ?>
                <?php } else { ?>
                    <div class="text-center">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Festival not started</h4>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        var path = '<?php echo site_url('/'); ?>';

        function clicktoviewfestdet_public(festid) {
            $('#hidfestid').val(festid);
            $('#festdet').attr('action', path + 'publishresult/publicresult/festival_allitem');
            $('#festdet').submit();
        }

        function finisheditemdet_public(festid) {
            $('#hidfestid').val(festid);
            $('#festdet').attr('action', path + 'publishresult/publicresult/finished_item');
            $('#festdet').submit();
        }

        function remainderitemdet_public(festid) {
            $('#hidfestid').val(festid);
            $('#festdet').attr('action', path + 'publishresult/publicresult/incomplete_item');
            $('#festdet').submit();
        }
    </script>
</body>
</html>
