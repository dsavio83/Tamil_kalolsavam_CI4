<style type="text/css">
    .style1 {
        font-family: 'TAU-Pallai', 'Segoe UI', Tahoma, sans-serif;
        font-size: 14px;
        font-weight: bold;
        color: #0d6efd;
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
    .data-cell {
        font-size: 10px;
        color: #000000;
    }
</style>
<style>
    @media print {
        h1 { page-break-before: always; }
    }
</style>

<?php
$rankwise = $rankwise ?? $item_code ?? [];
if (!empty($rankwise)) {
    $prevrankcode = "";
    $j = 0;
    $count = 0;

    foreach ($rankwise as $value) {
        if (($value['is_publish'] ?? '') == 'N') {
            continue;
        }

        if ($prevrankcode != $value['fest_id']) {
            $count = 0;
            $prevrankcode = $value['fest_id'];

            if ($j != 0) {
                echo "</table></page>";
            }
            $j++;
            ?>
            <page backtop="20mm" backbottom="20mm">
                <page_header>
                    <?php $this->load->view('report/report_header'); ?>
                </page_header>
                <page_footer>
                    <?php $this->load->view('report/report_footer'); ?>
                </page_footer>

                <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="margin-bottom: 8px;">
                    <tr>
                        <td height="30" align="center" class="style1">
                            Item Wise Participants Details - <?php echo htmlspecialchars((string)$value['fest_name']); ?>
                        </td>
                    </tr>
                </table>

                <table width="100%" align="center" border="1" cellpadding="3" cellspacing="0" style="border-collapse: collapse; border: 0.5px solid #000000;">
                    <tr>
                        <td colspan="6" align="center" class="style1" height="24" style="background-color: #f1f5f9; border: 0.5px solid #000000;">
                            Winners in <?php echo htmlspecialchars((string)($value['item_code'] . ' - ' . $value['item_name'])); ?>
                        </td>
                    </tr>
                    <tr style="background-color: #f8fafc;">
                        <td width="40" align="center" class="style3" style="border: 0.5px solid #000000;">Sl.No</td>
                        <td width="170" align="left" class="style3" style="border: 0.5px solid #000000;">&nbsp;Participant Name</td>
                        <td width="260" align="left" class="style3" style="border: 0.5px solid #000000;">&nbsp;School</td>
                        <td width="45" align="center" class="style3" style="border: 0.5px solid #000000;">Rank</td>
                        <td width="45" align="center" class="style3" style="border: 0.5px solid #000000;">Grade</td>
                        <td width="45" align="center" class="style3" style="border: 0.5px solid #000000;">Point</td>
                    </tr>
            <?php
        }

        $count++;
        ?>
        <tr>
            <td align="center" class="data-cell" style="border: 0.5px solid #000000;"><?php echo $count; ?></td>
            <td align="left" class="data-cell" style="border: 0.5px solid #000000;">&nbsp;<?php echo htmlspecialchars((string)$value['participant_name']); ?></td>
            <td align="left" class="data-cell" style="border: 0.5px solid #000000;">&nbsp;<?php echo htmlspecialchars((string)($value['school_code'] . ' - ' . $value['school_name'] . ' (' . ($value['sub_district_name'] ?? '') . ')')); ?></td>
            <td align="center" class="data-cell" style="border: 0.5px solid #000000;"><?php echo htmlspecialchars((string)$value['rank']); ?></td>
            <td align="center" class="data-cell" style="border: 0.5px solid #000000;"><?php echo htmlspecialchars((string)$value['grade']); ?></td>
            <td align="center" class="data-cell" style="border: 0.5px solid #000000;"><?php echo htmlspecialchars((string)$value['point']); ?></td>
        </tr>
        <?php
    }

    if ($j > 0) {
        echo "</table></page>";
    }
}
?>
