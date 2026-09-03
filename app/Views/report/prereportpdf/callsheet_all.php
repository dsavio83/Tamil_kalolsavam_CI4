<style type="text/css">
    <!--
    .style1 {
        font-size: 15px;
        font-weight: bold;
        color: #660033;
    }
    .style2{
        font-size: 11px;
        font-weight: bold;
        color:#000000;
    }
    .style8{
        font-size: 8px;
        color:#CC3300;
    }
    .style9 {
        font-size: 20px;
        font-weight: bold;
        color: #660033;
    }
    .style10 {
        font-size:13px;
        font-weight: bold;
        color:#000000;
    }
    -->
</style>
<?php
// Group details by item_code
$items_grouped = array();
if (!empty($fees_details) && is_array($fees_details)) {
    foreach ($fees_details as $row) {
        $items_grouped[$row['item_code']][] = $row;
    }
}

foreach ($items_grouped as $item_code => $participants):
    $first = $participants[0];
    $timetype = '';
    if (isset($first['time_type'])) {
        if ($first['time_type'] == 'M') $timetype = 'Minutes';
        else if ($first['time_type'] == 'S') $timetype = 'Second';
    }
    $quato_dash_flag = 0;
?>
<page backtop="20mm" backbottom="40mm">
    <page_header>
        <?php $this->load->view('report/report_header'); ?>
    </page_header>
    <page_footer>
        <?php $this->load->view('report/report_footer'); ?>
        <span class="style9">* </span> <span class="style8">Special Order Entry (Result to be declared) </span>
        <div style="clear:both"></div>
        <span class="style9"> ** </span>
        <span class="style8">Special Order Entry (Result to be Withheld)</span>
    </page_footer>

    <table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
        <tr>
            <td align="center" class="style1" height="27" width="800">
                Call sheet <?php echo isset($first['fest_name']) ? $first['fest_name'] : ''; ?><br />
                <?php echo $first['item_code'] . ' - ' . $first['item_name']; ?>
            </td>
        </tr>
    </table>

    <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td colspan="6" style="border-bottom:1px #000000; padding:2px;">
                <table align="center" width="100%" border="0">
                    <tr>
                        <td class="style2" align="left" width="150">Stage No : <?php echo isset($first['stage_name']) ? $first['stage_name'] : ''; ?></td>
                        <td class="style2" align="left" width="150">Date : <?php echo isset($first['start_time']) ? datetophpmodel($first['start_time']) : ''; ?></td>
                        <td align="left" class="style2" width="150">Max Time : <?php echo (isset($first['item_time']) ? $first['item_time'] : '') . ' ' . $timetype; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center" class="style2" style="border-right:1px #000000; padding:2px;" height="30">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sl.No&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td align="center" class="style2" height="25" style="border-right:1px #000000; padding:2px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Registration No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td align="center" class="style2" height="25" style="border-right:1px #000000; padding:2px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cluster&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td align="center" class="style2" height="25" style="border-right:1px #000000; padding:2px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Code No&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td width="23%" align="center" class="style2" height="25" style="border-right:1px #000000; padding:2px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Signature of Participant&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td width="14%" align="center" class="style2" height="25" style="border-right:0px #000000; padding:2px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Remarks&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <?php
        $s = 0;
        foreach ($participants as $p):
            $s++;
            $quato_dash = '';
            if (isset($p['spo_id']) && $p['spo_id'] != 0) {
                $quato_dash_flag = 1;
                $quato_dash = (isset($p['is_publish']) && $p['is_publish'] == 'Y') ? '*' : '**';
            }
        ?>
        <tr height="35">
            <td align="center" style="border-top:1px #000000; border-right:1px #000000; padding:2px;" height="35"><?php echo $s; ?></td>
            <td class="style10" align="center" style="border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $p['participant_id'] . ' ' . $quato_dash; ?></td>
            <td align="center" style="border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo isset($p['cluster_no']) ? $p['cluster_no'] : ''; ?></td>
            <td width="17%" style="border-top:1px #000000; border-right:1px #000000; padding:2px;">&nbsp;</td>
            <td style="border-top:1px #000000; border-right:1px #000000; padding:2px;">&nbsp;</td>
            <td style="border-top:1px #000000; border-right:0px #000000; padding:2px;">&nbsp;</td>
        </tr>
        <?php endforeach; ?>
    </table>

    <table width="100%" border="0" align="left">
        <tr>
            <td width="9%" height="29">&nbsp;</td>
            <td width="31%"></td>
            <td width="1%"></td>
            <td width="59%"></td>
        </tr>
        <tr>
            <td colspan="2" class="style2" height="25">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No. of Participants Registered</td>
            <td colspan="2">&nbsp;&nbsp;<?php echo isset($first['no_of_participant']) ? $first['no_of_participant'] : $s; ?></td>
        </tr>
        <tr>
            <td colspan="2" align="left" class="style2" height="25">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No. of Participants Reported</td>
            <td colspan="2">........................&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2" align="left" class="style2" height="25">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No of Participants performed</td>
            <td colspan="2">........................</td>
        </tr>
        <tr>
            <td class="style2" colspan="4" height="25">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Result Declared at&nbsp;&nbsp;.........................................&nbsp;&nbsp;on &nbsp;&nbsp;.....................................................&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;by Judges&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr>
            <td class="style2" align="right" colspan="3">&nbsp;&nbsp;&nbsp;</td>
            <td class="style2" align="right" height="50">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name &amp; Signature of stage manager:</td>
        </tr>
        <?php if ($quato_dash_flag == 1): ?>
        <tr>
            <td height="20" align="left" colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style9">* </span> <span class="style8">Special Order Entry (Result to be declared) </span></td>
        </tr>
        <tr>
            <td height="20" align="left" colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style9"> ** </span><span class="style8">Special Order Entry (Result to be Withheld)</span></td>
        </tr>
        <?php endif; ?>
    </table>
</page>
<?php endforeach; ?>




