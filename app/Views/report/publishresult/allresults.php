<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tamil Kalaivizha</title>

    <link rel="shortcut icon" type="image/x-icon" href="https://photos.app.goo.gl/4Tv9UCTHxEAN44Pm7">
    <link rel="mask-icon" href="https://photos.app.goo.gl/4Tv9UCTHxEAN44Pm7" color="#111">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://s3-us-west-2.amazonaws.com/s.cdpn.io/4579/bootstrap-table.css">
	
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        .rotate-180-deg {
            transform: rotate(180deg);
        }

        hr {
            margin: 5px 0;
        }

        .docs-table tbody i {
            display: inline-block;
            margin: 0 auto;
            font-size: 2em;
            width: 100%;
            text-align: center;
        }

        ul li i {
            display: inline-block;
            margin-right: 10px;
        }

        li.divider {
            width: 100%;
        }

        li.divider hr {
            width: 90%;
            text-align: center;
        }

        li.sub-li {
            font-size: 0.8em;
        }

        @media (max-height: 600px) {
            ul.navbar-nav.side-nav > li > a {
                height: 40px;
                padding: 5px 15px;
            }
        }

        body {
            margin-top: 2px;
            padding-bottom: 52px;
        }

        #wrapper {
            height: 100%;
            padding-left: 0;
        }

        #page-wrapper {
            height: 100%;
            padding: 5px 15px;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .alerts-heading {
            font-size: 50px;
            margin: 0;
        }

        .alerts-text {
            margin: 0;
        }

        /* Edit Below to Customize Widths > 768px */
        @media (min-width: 768px) {
            /* Wrappers */
            #wrapper {
                padding-left: 225px;
                transition: padding-left 800ms ease;
            }

            #wrapper.expanded {
                padding-left: 48px;
            }

            #page-wrapper {
                padding: 0px 25px 15px 25px;
            }

            .header-text {
                display: inline-block;
                border-bottom: 1px solid lightgrey;
                font-weight: 300;
                animation-delay: 0;
                animation-duration: 600ms;
            }
        }

         /* Add custom CSS to merge table cells */
        .merged-cells {
            display: block;
            margin: 0;
            padding: 0;
            border: none;
        }
    </style>
</head>

<body>
    <div class="container">
    <?php if (count($details) > 0) { ?>
        <h4 class="text-center text-danger">Tamil Kalaivizha</h4>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 ">
                <div class="row">
                    <div class="col-xl-12 col-sm-12">
                        <h2 class="text-center text-primary">
                            School Wise OverAll Point Table
                        </h2>
                        <div class="col-xl-2"> </div>
                    </div>
                </div>
                <hr />
            </div>
        </div>
		<a href="<?php echo base_url(); ?>publishresult/publicresult/resultview" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Back to Results
        </a>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12 js-content">
                <div class="docs-table">
                    <table
                        data-toggle="table"
                        data-show-toggle="true"
                        data-show-columns="true"
                        data-search="true"
                        data-striped="true">
                        <thead>
                            <tr>
                                <th data-field="Photo">Photo</th>
                                <th data-field="Festival Name">Festival Name</th>
                                <th data-field="Reg. No">Reg. No</th>
                                <th data-field="Code No">Code No</th>
                                <th data-field="Name">Name</th>
                                <th data-field="Std">Std</th>
                                <th data-field="School Name">School Name</th>
                                <th data-field="Grade">Grade</th>
                                <th data-field="Point">Point</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            
                                <?php
                            $prev_itemcode = "";
                            $prev_festid = "";
                            foreach ($details as $value) {
                                if ($value['is_publish'] != 'N') {
                                    if ($prev_festid != $value['fest_id']) {
                            ?>
                           <!--  <tr>
                                <td class="merged-cells" colspan="9">
                                    Tamil Kalaivizha &nbsp;Result &nbsp;: &nbsp;<?php echo $value['fest_name']; ?>
                                </td>
                            </tr>
                             -->
                            
                                <?php
                                        $prev_festid = $value['fest_id'];
                                    }
                                    if ($prev_itemcode != $value['item_code']) {
                            ?>
                            <!-- <tr>
                                <td class="merged-cells" colspan="9">
                                    <b><?php echo $value['item_code'] . ' - ' . $value['item_name']; ?></b> 
                                </td>
                            </tr> -->
                            <?php
                                        $prev_itemcode = $value['item_code'];
                                    }
                            ?>
                            
                            <tr>
                                <td>
                                    <?php
                                    $reg = $value['admn_no'];
                                    if (@$Photo['pic'][$reg]) {
                                        @$pic_path = @$Photo['pic'][$reg];
                                        echo "<script>console.log('Sub District Code: " . $value['sub_district_code'] . ", School Code: " . $value['school_code'] . ", Admission No: " . $reg . ", Participant ID: " . $value['participant_id'] . ", Code No: " . $value['code_no'] . ", Photo URL: " . $pic_path . "');</script>";
                                        echo "<img src='" . @$pic_path . "' width='80' height='80'>";
                                    } else {
                                        @$pic_path = base_url() . "photos/img_user_nophoto.png";
                                        echo "<script>console.log('Sub District Code: " . $value['sub_district_code'] . ", School Code: " . $value['school_code'] . ", Admission No: " . $reg . ", Participant ID: " . $value['participant_id'] . ", Code No: " . $value['code_no'] . ", No photo: " . $pic_path . "');</script>";
                                        echo "<img src='" . @$pic_path . "' width='80' height='80'>";
                                    }
                                    ?>
                                </td>
                                <td><?php echo $value['item_code'] . ' - ' . $value['item_name']; ?></td>
                                <td><?php echo $value['participant_id']; ?></td>
                                <td><?php echo $value['code_no']; ?></td>
                                <td><?php echo wordwrap($value['participant_name'], 25, '<br>'); ?></td>
                                <td><?php echo $value['class']; ?></td>
                                <td><?php echo wordwrap($value['school_name'], 35, '<br>'); ?></td>
                                <td><?php echo $value['grade']; ?></td>
                                <td><?php echo $value['point']; ?></td>
                            </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                    } else {
                    ?>
                    <table class="table table-responsive" style="width:100%;">
                            <tr>
                                <td align="center"><h1 class="text-center">Try later...... Points are  Not Ready</h1></td>
                            </tr>
                        </table>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS and Bootstrap Table JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/4579/bootstrap.min.js"></script>
    <script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/4579/bootstrap-table.js"></script>
</body>

</html>
