<style type="text/css">
    .style1 {
        font-family: 'TAU-Pallai', 'Segoe UI', Tahoma, sans-serif;
        font-size: 14px;
        font-weight: bold;
        color: #000000;
    }
    .style2 {
        font-family: 'TAU-Pallai', 'Segoe UI', Tahoma, sans-serif;
        font-size: 12px;
        font-weight: bold;
        color: #000000;
    }
    .style3 { 
        font-size: 10px;
        font-weight: bold;
        color: #000000;
    }
    .tb {
        font-size: 10px;
        font-weight: bold;
        color: #000000;
    }
    .ety {
        font-size: 10px;
        color: #000000;
    }
    p.page1 { page-break-after:always; }
</style>

<?php
$timewise = $timewise ?? $time_wise ?? [];
if (!empty($timewise['values'])) {
    $count = 0;
    $previous = "";
    $pre = "";
    $j = 0;
    $db = \Config\Database::connect();
    ?>
    <page backtop="20mm" backbottom="20mm">
        <page_header>
            <?php $this->load->view('report/report_header'); ?>
        </page_header>
        <page_footer>
            <?php $this->load->view('report/report_footer'); ?>
        </page_footer>

        <?php
        foreach ($timewise['values'] AS $data) {
            $item_code = $data['item_code'];
            $flag = 0;

            if (!empty($timewise['disc_code'])) {
                foreach ($timewise['disc_code'] AS $no_data) {
                    $no_code = $no_data['item_code'];
                    if ($data['item_code'] == $no_code) {
                        $flag = 1;
                    }
                }
            }

            if ($flag != 1) {
                if ($previous != $data['item_code'] && !empty($timewise['parti_det'][$item_code])) {
                    if (!empty($data['item_code'])) {
                        $db->table('sd_result_master')->where('item_code', (int)$data['item_code'])->update(['is_printed' => 1]);
                    }
                    $previous = $data['item_code'];
                    $count = 0;
                    if ($j != 0) {
                        echo "<br /><br />";
                    }
                    ?>
                    <table width="100%" height="62" border="0" cellpadding="0" cellspacing="0" align="center">
                        <tr>
                            <td align="left"><b>Festival : <?php echo htmlspecialchars((string)$data['fest_name']); ?>&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars((string)$data['item_code']); ?> ( <?php echo htmlspecialchars((string)$data['item_name']); ?>)</b></td>
                            <td align="right"><b>&nbsp;&nbsp;&nbsp;&nbsp; Result No. <?php echo htmlspecialchars((string)$data['result_no']); ?></b></td>
                        </tr>
                    </table>

                    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
                        <tr>
                            <td width="43" align="center" class="style2" style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;'>Sl No</td>
                            <td width="250" align="left" class="style2" style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;'>&nbsp;Name </td>
                            <td width="250" align="left" class="style2" style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;'>&nbsp;School </td>
                            <td width="40" align="center" class="style2" style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;'>&nbsp;Rank</td>
                            <td width="40" align="center" class="style2" style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;'>&nbsp;Grade</td>
                            <td width="40" align="center" class="style2" style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;'>&nbsp;Point</td>
                        </tr>
                        <?php
                        $j++;

                        foreach ($timewise['parti_det'][$item_code] AS $parti_data) {
                            if ($parti_data['rank'] != 0) {
                                $count++;
                                ?>
                                <tr>
                                    <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;' align="center"><?php echo $count; ?></td>
                                    <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;'>&nbsp;<?php echo wordwrap($parti_data['participant_name'], 50, '<br>'); ?></td>
                                    <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;'>&nbsp;<?php echo wordwrap($parti_data['school_code'] . ' - ' . $parti_data['school_name'], 30, '<br>'); ?></td>
                                    <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;' align="center"><?php echo $parti_data['rank']; ?></td>
                                    <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;' align="center"><?php echo $parti_data['grade']; ?></td>
                                    <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;' align="center"><?php echo $parti_data['point']; ?></td>
                                </tr>
                                <?php
                            }
                        }

                        foreach ($timewise['parti_det'][$item_code] AS $parti_data) {
                            if ($parti_data['rank'] == 0) {
                                $count++;
                                ?>
                                <tr>
                                    <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;' align="center"><?php echo $count; ?></td>
                                    <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;'>&nbsp;<?php echo wordwrap($parti_data['participant_name'], 50, '<br>'); ?></td>
                                    <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;'>&nbsp;<?php echo wordwrap($parti_data['school_code'] . ' - ' . $parti_data['school_name'], 30, '<br>'); ?></td>
                                    <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;' align="center"><?php echo $parti_data['rank']; ?></td>
                                    <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;' align="center"><?php echo $parti_data['grade']; ?></td>
                                    <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;' align="center"><?php echo $parti_data['point']; ?></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </table>
                    <?php
                }
            }
        }
        ?>
    </page>
    <?php
}
?>