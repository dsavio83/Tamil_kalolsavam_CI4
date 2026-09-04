<style type="text/css">
    .style1 {
        font-family: 'TAU-Pallai', 'Segoe UI', Tahoma, sans-serif;
        font-size: 14px;
        font-weight: bold;
        color: #000000;
    }
    .style2 {
        font-family: 'TAU-Pallai', 'Segoe UI', Tahoma, sans-serif;
        font-size: 14px;
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
</style>

<?php
$result_numwise = $result_numwise ?? $result_num ?? [];
if (!empty($result_numwise)) {
    $count = 0;
    $previous = "";
    $pageCount = 0;
    $db = \Config\Database::connect();

    foreach ($result_numwise AS $row) {
        if (empty($row)) continue;
        foreach ($row AS $data) {
            if ($previous != $data['item_code']) {
                if (!empty($data['item_code'])) {
                    $db->table('sd_result_master')->where('item_code', (int)$data['item_code'])->update(['is_printed' => 1]);
                }
                $previous = $data['item_code'];
                $count = 0;
                if ($pageCount > 0) {
                    echo "</table></page>";
                }
                $pageCount++;
                ?>
                <page backtop="20mm" backbottom="20mm">
                    <page_header>
                        <?php $this->load->view('report/report_header'); ?>
                    </page_header>
                    <page_footer>
                        <?php $this->load->view('report/report_footer'); ?>
                    </page_footer>   
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
            }
            $count++;
            ?>
            <tr>
                <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;' align="center"><?php echo $count; ?></td>
                <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;'>&nbsp;<?php echo wordwrap($data['participant_name'], 50, '<br>'); ?></td>
                <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;'>&nbsp;<?php echo wordwrap($data['school_code'] . ' - ' . $data['school_name'], 30, '<br>'); ?></td>
                <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;' align="center"><?php echo htmlspecialchars((string)$data['rank']); ?></td>
                <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;' align="center"><?php echo htmlspecialchars((string)$data['grade']); ?></td>
                <td style='border: 0 0 0.5px 0.5px 0 #666666; height:20px;' align="center"><?php echo htmlspecialchars((string)$data['point']); ?></td>
            </tr>
            <?php
        }
    }

    if ($pageCount > 0) {
        echo "</table></page>";
    }
}
?>
