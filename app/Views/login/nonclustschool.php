<style>
    .display_none {
        display: none;
    }
</style>

<?php
echo form_open('', array('id' => 'noncluster'));
?>
<input type="hidden" name="hidSchoolId" id="hidSchoolId" value="1">
<!-- Back button -->
<?php if ((int)$this->session->get('USER_TYPE') != 5) { ?>
<div class="row mt-3 mb-3">
    <div class="col-12">
        <a href="<?php echo base_url('welcome/cluster_details?sel_sub_district_id=' . urlencode(isset($sub_district_code) ? $sub_district_code : '')); ?>" class="btn btn-primary">
            <i class="bi bi-arrow-return-left"></i> Back
        </a>
    </div>
</div>
<?php } ?>
<div class="row justify-content-center mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mt-3">
                    <div class="col-6 p-2"> <span style="font-weight: bold; font-size: 18px;">&nbsp;&nbsp;Clusters </span></div>
                    <div class="col-6 p-2" style="text-align: right;"> <span style="font-weight: bold; font-size: 18px;">Print</span>&nbsp;&nbsp;
                        <img src="<?php echo base_url('images/print_icon.png'); ?>" title="print" class="window_print"
                            onClick="javascript:printContent('print_content'); return false;" />
                    </div>
                </div>
                <div class="table-responsive-sm">
                    <table class="table table-striped mt-4">

                        <tr>
                            <th align="left" width="5%">SI.No</th>
                            <th width="50%" align="left">School</th>
                            <th width="15%" align="center">Data Entered</th>
                            <th width="10%" align="center">Confirmed</th>
                            <?php if ($this->session->get('USER_TYPE') <= 3) { ?>
                                <th width="15%" align="center">Reset Confirmation</th>
                            <?php } else { ?>
                                <th width="15%" align="center">&nbsp;</th>
                            <?php } ?>
                        </tr>
                        <?php
                        $countt = 0;
                        for ($j = 0; $j < count($val); $j++) {
                            $countt++;


                            $entry = 'Yes';
                            if ($val[$j]['is_finalize'] == 'Y')
                                $finalize = 'Yes';
                            else if ($val[$j]['is_finalize'] == 'N')
                                $finalize = 'No';
                            else
                                $finalize = 'No';
                            $status = (empty($school[$j]['is_finalize'])) ? 'N' : $school[$j]['is_finalize'];
                        ?>
                            <tr>
                                <td align="left"><?php echo $countt; ?></td>
                                <td align="left" &nbsp;&nbsp;&nbsp;>
                                    <a href="javascript:void(0)" onClick="javascript:nonclustschooldet('<?php echo $val[$j]['school_code'] ?>')">
                                        <?php echo $val[$j]['school_code']; ?>&nbsp;-&nbsp;
                                        <?php echo $val[$j]['school_name']; ?></a>
                                </td>
                                <td align="center"><?php echo $entry; ?></td>
                                <td id="<?php echo 'confirm_' . ($j + 1); ?>" align="center"><?php echo $finalize; ?></td>
                                <?php if ($this->session->get('USER_TYPE') <= 3) { ?>
                                    <td id="<?php echo 'confirm_' . ($j + 1); ?>_dis" align="center">
                                        <?php
                                        if ('Yes' == $finalize) {
                                        ?>
                                            <button type="button" id="reset_<?php echo $val[$j]['school_code']; ?>"
                                                onClick="fnc_change_confirmation_status('<?php echo addslashes($val[$j]['school_name']) . ' - ' . $val[$j]['school_code']; ?>', '<?php echo $val[$j]['school_code']; ?>', '<?php echo 'confirm_' . ($j + 1); ?>'); return false;"
                                                style="color: #dc3545; cursor: pointer; font-weight: bold; background: none; border: none; text-decoration: underline;"
                                                title="Reset confirmation status for <?php echo $val[$j]['school_name']; ?>">
                                                Reset
                                            </button>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                <?php } else { ?>
                                    <td align="center">&nbsp;</td>
                                <?php } ?>
                            </tr>

                            <?php
                            $flag = 0;
                        }
                        if (count($nonschool) > 0) {
                            for ($k = 0; $k < count($nonschool); $k++) {
                                $fgflag = 0;

                                for ($l = 0; $l < count($val); $l++) {
                                    if ($val[$l]['school_code'] == $nonschool[$k]['school_code']) {
                                        $fgflag = 1;
                                        break;
                                    }
                                }
                                if ($fgflag == 0) {

                                    $countt++;
                                    $entry = 'No';
                                    $finalize = 'No';

                                    //$status = (empty($school[$j]['is_finalize']))? 'N' : $school[$j]['is_finalize'];
                            ?>
                                    <tr>
                                        <td align="left"><?php echo $countt; ?></td>
                                        <td align="left" &nbsp;&nbsp;&nbsp;>
                                            <a href="javascript:void(0)" onClick="javascript:nonclustschooldet('<?php echo $nonschool[$k]['school_code'] ?>')">
                                                <?php echo $nonschool[$k]['school_code']; ?>&nbsp;-&nbsp;
                                                <?php echo $nonschool[$k]['school_name']; ?></a>
                                        </td>
                                        <td align="center"><?php echo $entry; ?></td>
                                        <td id="<?php echo 'confirm_' . ($k + 1); ?>" align="center"><?php echo $finalize; ?></td>
                                        <?php if ($this->session->get('USER_TYPE') <= 3) { ?>
                                            <td id="<?php echo 'confirm_' . ($k + 1); ?>_dis" align="center">
                                                <?php
                                                if ('Yes' == $finalize) {
                                                ?>
                                                    <button type="button" id="reset_nonschool_<?php echo $nonschool[$k]['school_code']; ?>"
                                                        onClick="fnc_change_confirmation_status('<?php echo addslashes($nonschool[$k]['school_name']) . ' - ' . $nonschool[$k]['school_code']; ?>', '<?php echo $nonschool[$k]['school_code']; ?>', '<?php echo 'confirm_' . ($k + 1); ?>'); return false;"
                                                        style="color: #dc3545; cursor: pointer; font-weight: bold; background: none; border: none; text-decoration: underline;"
                                                        title="Reset confirmation status for <?php echo $nonschool[$k]['school_name']; ?>">
                                                        Reset
                                                    </button>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                        <?php } else { ?>
                                            <td align="center">&nbsp;</td>
                                        <?php } ?>
                                    </tr>

                        <?php
                                }
                            }
                        }
                        ?>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="sel_sub_district_id" id="sel_sub_district_id" value="<?php //echo $this->input->post('sel_sub_district_id');  
                                                                                    ?>" />
    <?php
    echo form_close();
    ?>
    <!-- print content start here -->
    <div id="print_content" class="display_none">
        <div class="row justify-content-center mt-3">
            <div class="col-xl-12 col-sm-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm mt-4">
                                <tr>
                                    <th colspan="5">Non Cluster Schools

                                <tr>
                                    <th align="left" width="5%">SI.No</th>
                                    <th width="50%" align="left">School</th>
                                    <th width="15%" align="center">Data Entered</th>
                                    <th width="10%" align="center">Confirmed</th>
                                </tr>
                                <?php
                                $countt = 0;
                                for ($j = 0; $j < count($val); $j++) {
                                    $countt++;


                                    $entry = 'Yes';
                                    if ($val[$j]['is_finalize'] == 'Y')
                                        $finalize = 'Yes';
                                    else if ($val[$j]['is_finalize'] == 'N')
                                        $finalize = 'No';
                                    else
                                        $finalize = 'No';
                                    $status = (empty($school[$j]['is_finalize'])) ? 'N' : $school[$j]['is_finalize'];
                                ?>
                                    <tr>
                                        <td align="center"><?php echo $countt; ?></td>
                                        <td align="left" &nbsp;&nbsp;&nbsp;>

                                            <?php echo $val[$j]['school_code']; ?>&nbsp;-&nbsp;
                                            <?php echo $val[$j]['school_name']; ?></td>
                                        <td align="center"><?php echo $entry; ?></td>
                                        <td id="<?php echo 'confirm_' . ($j + 1); ?>" align="center"><?php echo $finalize; ?></td>


                                    </tr>

                                    <?php
                                    $flag = 0;
                                }
                                if (count($nonschool) > 0) {
                                    for ($k = 0; $k < count($nonschool); $k++) {
                                        $fgflag = 0;

                                        for ($l = 0; $l < count($val); $l++) {
                                            if ($val[$l]['school_code'] == $nonschool[$k]['school_code']) {
                                                $fgflag = 1;
                                                break;
                                            }
                                        }
                                        if ($fgflag == 0) {
                                            $countt++;
                                            $entry = 'No';
                                            $finalize = 'No';

                                            //$status = (empty($school[$j]['is_finalize']))? 'N' : $school[$j]['is_finalize'];
                                    ?>
                                            <tr>
                                                <td align="center"><?php echo $countt; ?></td>
                                                <td align="left" &nbsp;&nbsp;&nbsp;>
                                                    <?php echo $nonschool[$k]['school_code']; ?>&nbsp;-&nbsp;
                                                    <?php echo $nonschool[$k]['school_name']; ?></td>
                                                <td align="center"><?php echo $entry; ?></td>
                                                <td id="<?php echo 'confirm_' . ($k + 1); ?>" align="center"><?php echo $finalize; ?></td>
                                            </tr>

                                <?php
                                        }
                                    }
                                }
                                ?>

                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- print content end here -->