<style type="text/css">
<!--
.report-title {
    font-size: 13px;
    font-weight: bold;
    color: #660033;
    text-align: center;
}
.fest-title {
    font-size: 11px;
    font-weight: bold;
    color: #000000;
    text-align: center;
    padding-top: 1px;
}
.meta-item {
    font-size: 10px;
    color: #000000;
    line-height: 1.35;
}
.th-cell {
    font-size: 9.5px;
    font-weight: bold;
    color: #000000;
    border: 0.25mm solid #000000;
    padding: 3px 2px;
}
.td-cell {
    font-size: 9px;
    color: #000000;
    border: 0.25mm solid #000000;
    padding: 2.5px 2px;
}
-->
</style>

<?php
$isDistrictAdmin = ((int)$this->session->get('USER_TYPE') === 2 || (int)$this->session->get('USER_TYPE') === 0);
$prev_festid = "";

for ($j = 0; $j < count($part_details); $j++) {

    if ($prev_festid != $part_details[$j]['fest_id']) {
        $count = 0;
        $prev_festid = $part_details[$j]['fest_id'];
        if ($j != 0) {
            echo "</tbody></table></page>";
        }

        $district_name = !empty($part_details[$j]['rev_district_name']) 
            ? $part_details[$j]['rev_district_name'] 
            : (!empty($this->session->get('DISTRICT')) ? get_dist_name($this->session->get('DISTRICT')) : '');
        $sub_district_name = !empty($part_details[$j]['sub_district_name']) 
            ? $part_details[$j]['sub_district_name'] 
            : (!empty($this->session->get('SUB_DISTRICT')) ? get_sub_dist_name($this->session->get('SUB_DISTRICT')) : '');
        ?>

        <?php $this->load->view('report/report_pdf_style'); ?>
<page backtop="20mm" backbottom="18mm" backleft="5mm" backright="5mm">
            <page_header>
                <?php $this->load->view('report/report_header'); ?>
                
            </page_header>

            <page_footer>
                <?php $this->load->view('report/report_footer'); ?>
            </page_footer>
<table style="width: 100%; margin-top: 1px;" border="0" cellspacing="0" cellpadding="0" align="center">
                    <tr>
                        <td align="center" class="report-pdf-title" style="width: 100%; text-align: center;">List of participants For Team Manager</td>
                    </tr>
                    <tr>
                        <td align="center" class="report-pdf-subtitle" style="width: 100%; text-align: center;">
                            Festival: <?php echo esc($part_details[$j]['fest_name']); ?>
                        </td>
                    </tr>
                </table>


            <table style="width: 100%; margin-top: 1px; margin-bottom: 5px;" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr>
                    <td style="width: <?php echo $isDistrictAdmin ? '60%' : '100%'; ?>; text-align: left;" class="meta-item" valign="top">
                        <strong>School Name:</strong> <?php echo esc($part_details[$j]['school_name']); ?><br/>
                        <strong>School Code:</strong> <?php echo esc($part_details[$j]['school_code']); ?>
                    </td>
                    <?php if ($isDistrictAdmin) { ?>
                    <td style="width: 40%; text-align: right;" align="right" class="meta-item" valign="top">
                        <strong>District:</strong> <?php echo esc($district_name); ?><br/>
                        <strong>Sub-District:</strong> <?php echo esc($sub_district_name); ?>
                    </td>
                    <?php } ?>
                </tr>
            </table>

            <table style="width: 100%;" border="0" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <td class="th-cell" style="width: 4%; text-align: center;">Sl.No</td>
                        <td class="th-cell" style="width: 24%; text-align: left;">&nbsp;Item</td>
                        <td class="th-cell" style="width: 22%; text-align: left;">&nbsp;Name</td>
                        <td class="th-cell" style="width: 7%; text-align: center;">Reg No.</td>
                        <td class="th-cell" style="width: 7%; text-align: center;">Adm.No.</td>
                        <td class="th-cell" style="width: 4%; text-align: center;">B/G</td>
                        <td class="th-cell" style="width: 5%; text-align: center;">Class</td>
                        <td class="th-cell" style="width: 6%; text-align: center;">Cluster</td>
                        <td class="th-cell" style="width: 8%; text-align: center;">Stage No</td>
                        <td class="th-cell" style="width: 13%; text-align: center; white-space: nowrap;">Date</td>
                    </tr>
                </thead>
                <tbody>
        <?php
    }

    $count++;
    $datt = !empty($part_details[$j]['ddt']) ? datetophpmodel($part_details[$j]['ddt']) : '';
    ?>
    <tr>
        <td class="td-cell" style="width: 4%; text-align: center;"><?php echo $count; ?></td>
        <td class="td-cell" style="width: 24%; text-align: left;">&nbsp;<?php echo wordwrap(esc($part_details[$j]['item_code'] . ' - ' . $part_details[$j]['item_name']), 33, '<br/>'); ?></td>
        <td class="td-cell" style="width: 22%; text-align: left;">&nbsp;<?php echo wordwrap(esc($part_details[$j]['participant_name']), 28, '<br/>'); ?></td>
        <td class="td-cell" style="width: 7%; text-align: center;"><?php echo esc($part_details[$j]['participant_id']); ?></td>
        <td class="td-cell" style="width: 7%; text-align: center;"><?php echo esc($part_details[$j]['admn_no']); ?></td>
        <td class="td-cell" style="width: 4%; text-align: center;"><?php echo esc($part_details[$j]['gender']); ?></td>
        <td class="td-cell" style="width: 5%; text-align: center;"><?php echo esc($part_details[$j]['class']); ?></td>
        <td class="td-cell" style="width: 6%; text-align: center;"><?php echo esc($part_details[$j]['cluster_no']); ?></td>
        <td class="td-cell" style="width: 8%; text-align: center;"><?php echo esc($part_details[$j]['stage_name']); ?></td>
        <td class="td-cell" style="width: 13%; text-align: center; white-space: nowrap;"><?php echo esc($datt); ?></td>
    </tr>
    <?php
}

if (count($part_details) > 0) {
    echo "</tbody></table></page>";
}
?>
