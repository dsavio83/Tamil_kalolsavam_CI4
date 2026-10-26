<style type="text/css">
    <!--
    .style1 {
        font-size: 14px;
        font-weight: bold;
        color: #660033;
    }
    .style2{
        font-size: 11px;
        font-weight: bold;
        color:#000000;
    }
    .style9{
        font-size: 12px;
        font-weight: bold;
        color:#000000;
    }
    .style3{
        font-size: 12px;
        font-weight: bold;
        color:#000000;
    }
    .style4{
        font-size: 8px;
        font-weight: bold;
        color:#000000;
    }
    .style5{
        font-size: 10px;
        color:#000000;
    }
    .ety{
        font-size: 12px;
        color:#000000;
    }
    -->
</style>
<?php
$prev_schoolcode = "";
$prev_festcode = "";
for ($j = 0; $j < count($part_details); $j++) {

    if (($prev_schoolcode != $part_details[$j]['school_code']) || ($prev_festcode != $part_details[$j]['fest_id'])) {
        $prev_schoolcode = $part_details[$j]['school_code'];
        $prev_festcode = $part_details[$j]['fest_id'];
        $count = 0;

        if ($j != 0) {
            print("</table></page>");
        }
        ?>



        <?php $this->load->view('report/report_pdf_style'); ?>
<page backtop="20mm" backbottom="10mm">
            <page_header>
                <?php
                $this->load->view('report/report_header');
                ?>
                

                
            </page_header>
            <page_footer>
                <?php
                $this->load->view('report/report_footer');
                ?>
            </page_footer>
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center">
                    <tr>
                        <td align="center" class="report-pdf-title" valign="top">List of  participants For Team Manager</td>
                    </tr>  
                </table>
<table width="100%" border="0" align="center" style="margin-top: 2px;">
                    <tr>
                        <td width="26%" class="report-pdf-subtitle" align="left" style="text-align:left;">&nbsp;<span class="report-pdf-subtitle">Festival: <?php echo esc($part_details[$j]['fest_name']); ?></span></td>
                        <td width="36%" class="report-pdf-subtitle" align="center" style="text-align:center;"><span class="report-pdf-subtitle">School: <?php echo esc($part_details[$j]['school_code'] . ' - ' . $part_details[$j]['school_name']); ?></span></td>
                        <td width="38%" class="report-pdf-subtitle" align="right" style="text-align:right;"><span class="report-pdf-subtitle"><?php if (!empty($part_details[$j]['sub_district_name'])) { echo 'Sub-District: ' . esc($part_details[$j]['sub_district_name']) . '&nbsp;|&nbsp;'; } ?>District: <?php echo esc(!empty($part_details[$j]['rev_district_name']) ? $part_details[$j]['rev_district_name'] : (!empty($this->session->get('DISTRICT')) ? get_dist_name($this->session->get('DISTRICT')) : '')); ?></span>&nbsp;</td>
                    </tr>
                </table>
       


            <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" style="width: 100%;">

                <tr>
                    <td style="width: 5%; text-align: center; border-right: 1px #000000; padding: 2px;" class="style2">Sl.No</td>
                    <td style="width: 25%; text-align: left; border-right: 1px #000000; padding: 2px;" class="style2">&nbsp;Item</td>
                    <td style="width: 23%; text-align: left; border-right: 1px #000000; padding: 2px;" class="style2">&nbsp;Name</td>
                    <td style="width: 7%; text-align: center; border-right: 1px #000000; padding: 2px;" class="style2">Reg No.</td>
                    <td style="width: 7%; text-align: center; border-right: 1px #000000; padding: 2px;" class="style2">Adm.No.</td>
                    <td style="width: 4%; text-align: center; border-right: 1px #000000; padding: 2px;" class="style2">B/G</td>
                    <td style="width: 5%; text-align: center; border-right: 1px #000000; padding: 2px;" class="style2">Class</td>
                    <td style="width: 7%; text-align: center; border-right: 1px #000000; padding: 2px;" class="style2">Cluster</td>
                    <td style="width: 7%; text-align: center; border-right: 1px #000000; padding: 2px;" class="style2">Stage No</td>
                    <td style="width: 10%; text-align: left; border-right: 0px #000000; padding: 2px;" class="style2">Date</td>
                </tr>
                <?php
            }
            $count++;
            if (($part_details[$j]['is_captain'] == 'Y') && ($part_details[$j]['item_type'] == 'G'))
                $iscaptain = "(C)";
            else
                $iscaptain = "";

            if (($part_details[$j]['max_time'] != "") && ($part_details[$j]['no_of_participant'] != "")) {

                $total_time_for_item = ($part_details[$j]['time_type'] == 'S') ? ceil((int) $part_details[$j]['max_time'] / 60) : (int) $part_details[$j]['max_time'];
                if ($part_details[$j]['is_off_stage'] == 'N') {
                    $time = $total_time_for_item * $part_details[$j]['no_of_participant'];
                    $time = get_time_format($time);
                } else {
                    $time = get_time_format($total_time_for_item);
                }
                $datt = datetophpmodel($part_details[$j]['ddt']);
            } else {
                $datt = "";
                $timetye = "";
                $time = "";
            }
            ?>

            <tr>
                <td class="ety" align="center" style="width: 5%; border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $count; ?></td>
                <td class="ety" align="left" style="width: 25%; border-top:1px #000000; border-right:1px #000000; padding:2px;">&nbsp;<?php echo wordwrap(esc($part_details[$j]['item_code'] . ' - ' . $part_details[$j]['item_name']), 30, '<br>'); ?></td>
                <td class="ety" align="left" style="width: 23%; border-top:1px #000000; border-right:1px #000000; padding:2px;">&nbsp;<?php echo wordwrap(esc($part_details[$j]['participant_name']), 26, '<br>'); ?></td>
                <td class="ety" align="center" style="width: 7%; border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo esc($part_details[$j]['participant_id']); ?></td>
                <td class="ety" align="center" style="width: 7%; border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo esc($part_details[$j]['admn_no']); ?></td>
                <td class="ety" align="center" style="width: 4%; border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo esc($part_details[$j]['gender']); ?></td>
                <td class="ety" align="center" style="width: 5%; border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo esc($part_details[$j]['class']); ?></td>
                <td class="ety" align="center" style="width: 7%; border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo esc($part_details[$j]['cluster_no']); ?></td>
                <td class="ety" align="center" style="width: 7%; border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo esc($part_details[$j]['stage_name'] ?? ''); ?></td>
                <td class="ety" align="left" style="width: 10%; border-top:1px #000000; border-right:0px #000000; padding:2px;"><?php echo esc($datt); ?></td>
            </tr>
            <?php
        }
        ?>

    </table>

</page>

