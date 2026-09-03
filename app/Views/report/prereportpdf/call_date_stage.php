<style type="text/css">
<!--
.style1 {
    font-size: 15px;
    font-weight: bold;
    color: #660033;
}
.style2 {
    font-size: 11px;
    font-weight: bold;
    color: #000000;
}
.style8 {
    font-size: 8px;
    color: #CC3300;
}
.style9 {
    font-size: 14px;
    font-weight: bold;
    color: #CC3300;
}
.style10 {
    font-size: 12px;
    font-weight: bold;
    color: #000000;
}
.cell-border {
    border: 1px solid #000000;
}
table.report-grid {
    border-collapse: collapse;
}
-->
</style>

<?php
// Group participants by item_code
$grouped_items = [];
if (!empty($fees_details)) {
    foreach ($fees_details as $row) {
        $code = $row['item_code'];
        if (!isset($grouped_items[$code])) {
            $grouped_items[$code] = [];
        }
        $grouped_items[$code][] = $row;
    }
}

foreach ($grouped_items as $item_code => $participants):
    $first = $participants[0];
    if ($first['time_type'] == 'M')
        $timetype = 'Minutes';
    else if ($first['time_type'] == 'S')
        $timetype = 'Second';
    else
        $timetype = 'Minutes';

    $fest_name = isset($first['fest_name']) ? $first['fest_name'] : '';
    $item_name = isset($first['item_name']) ? $first['item_name'] : '';
    $stage_name = isset($first['stage_name']) ? $first['stage_name'] : '';
    $stage_desc = isset($first['stage_desc']) ? $first['stage_desc'] : '';
    $start_time = isset($first['start_time']) ? datetophpmodel($first['start_time']) : '';
    $item_time = isset($first['item_time']) ? $first['item_time'] : '';
    $no_of_participant = isset($first['no_of_participant']) ? $first['no_of_participant'] : count($participants);
?>
<page backtop="36mm" backbottom="30mm" backleft="15mm" backright="15mm">
    <page_header>
        <?php $this->load->view('report/report_header'); ?>
        <table width="100%" style="width: 100%; margin-top: 10px;" border="0" cellspacing="0" cellpadding="0" align="center">
            <tr>
                <td align="center" class="style1" height="27">
                    Call sheet <?php echo $fest_name; ?><br />
                    <?php echo $item_code . ' - ' . $item_name; ?>
                </td>
            </tr>
        </table>
    </page_header>

    <page_footer>
        <?php $this->load->view('report/report_footer'); ?>
        <div style="margin-top: 3px;">
            <span class="style9">* </span> <span class="style8">Special Order Entry (Result to be declared) </span>
            <br />
            <span class="style9">** </span> <span class="style8">Special Order Entry (Result to be Withheld)</span>
        </div>
    </page_footer>

    <!-- Meta Info Table (Original Data, Clean 2-column layout) -->
    <table width="100%" style="width: 100%; margin-bottom: 6px;" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td class="style2" colspan="2" align="left" style="padding: 2px 0;">
                Stage No : <?php echo $stage_name . ' - ' . $stage_desc; ?>
            </td>
        </tr>
        <tr>
            <td class="style2" align="left" width="55%" style="width: 55%; padding: 2px 0;">
                Date : <?php echo $start_time; ?>
            </td>
            <td class="style2" align="right" width="45%" style="width: 45%; text-align: right; padding: 2px 0;">
                Max Time : <?php echo $item_time . ' ' . $timetype; ?>
            </td>
        </tr>
    </table>

    <!-- Main Table: 100% width with equal margins on both sides -->
    <table width="100%" style="width: 100%; border-collapse: collapse;" border="0" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="cell-border" width="7%" style="width: 7%; height: 28px; text-align: center;"><b>Sl.No</b></td>
                <td class="cell-border" width="15%" style="width: 15%; height: 28px; text-align: center;"><b>Registration No.</b></td>
                <td class="cell-border" width="10%" style="width: 10%; height: 28px; text-align: center;"><b>Cluster</b></td>
                <td class="cell-border" width="13%" style="width: 13%; height: 28px; text-align: center;"><b>Code No</b></td>
                <td class="cell-border" width="35%" style="width: 35%; height: 28px; text-align: center;"><b>Signature of Participant</b></td>
                <td class="cell-border" width="20%" style="width: 20%; height: 28px; text-align: center;"><b>Remarks</b></td>
            </tr>
        </thead>
        <tbody>
            <?php
            $s = 0;
            foreach ($participants as $p):
                $s++;
                $quato_dash = '';
                if ($p['spo_id'] != 0) {
                    if ($p['is_publish'] == 'Y') {
                        $quato_dash = '*';
                    } else {
                        $quato_dash = '**';
                    }
                }
            ?>
            <tr>
                <td class="cell-border" style="height: 34px; text-align: center; font-size: 11px;"><?php echo $s; ?></td>
                <td class="cell-border style10" style="height: 34px; text-align: center;"><?php echo $p['participant_id'] . ' ' . $quato_dash; ?></td>
                <td class="cell-border" style="height: 34px; text-align: center; font-size: 11px;"><?php echo $p['cluster_no']; ?></td>
                <td class="cell-border" style="height: 34px;">&nbsp;</td>
                <td class="cell-border" style="height: 34px;">&nbsp;</td>
                <td class="cell-border" style="height: 34px;">&nbsp;</td>
            </tr>
            <?php endforeach; ?>

            <!-- 5 Blank Rows for Spots/Appeals -->
            <?php for ($k = 0; $k < 5; $k++): ?>
            <tr>
                <td class="cell-border" style="height: 34px; text-align: center;">&nbsp;</td>
                <td class="cell-border" style="height: 34px; text-align: center;">&nbsp;</td>
                <td class="cell-border" style="height: 34px; text-align: center;">&nbsp;</td>
                <td class="cell-border" style="height: 34px;">&nbsp;</td>
                <td class="cell-border" style="height: 34px;">&nbsp;</td>
                <td class="cell-border" style="height: 34px;">&nbsp;</td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <!-- Structure Below Table (Exactly as original: borderless, spaced, same data) -->
    <table width="100%" style="width: 100%; margin-top: 18px;" border="0" align="left">
        <tr>
            <td width="35%" style="width: 35%; height: 26px; padding: 3px 0;" class="style2">No. of Participants Registered</td>
            <td width="65%" style="width: 65%; height: 26px; padding: 3px 0;" class="style2"><?php echo $no_of_participant; ?></td>
        </tr>
        <tr>
            <td class="style2" style="height: 26px; padding: 3px 0;">No. of Participants Reported</td>
            <td class="style2" style="height: 26px; padding: 3px 0;">........................</td>
        </tr>
        <tr>
            <td class="style2" style="height: 26px; padding: 3px 0;">No of Participants performed</td>
            <td class="style2" style="height: 26px; padding: 3px 0;">........................</td>
        </tr>
        <tr>
            <td colspan="2" class="style2" style="height: 30px; padding-top: 8px;">
                Result Declared at ......................................... on ..................................................... by Judges
            </td>
        </tr>
        <tr>
            <td colspan="2" class="style2" align="right" style="height: 50px; text-align: right; vertical-align: bottom; padding-top: 15px;">
                Name &amp; Signature of stage manager:
            </td>
        </tr>
    </table>
</page>
<?php endforeach; ?>