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
        color: #1e293b;
    }
    .fest-header {
        font-family: 'TAU-Pallai', 'Segoe UI', Tahoma, sans-serif;
        font-size: 14px;
        font-weight: bold;
        background-color: #f1f5f9;
        color: #0d6efd;
    }
    .col-header {
        font-size: 10px;
        font-weight: bold;
        background-color: #f8fafc;
        color: #334155;
    }
    .data-cell {
        font-size: 10px;
        color: #0f172a;
    }
</style>
<page backtop="20mm" backbottom="20mm">
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
    <table width="100%" border="0" align="center" style="margin-bottom: 8px;">
        <tr>
            <td align="center" class="style1">Item Wise Point Report</td>
        </tr>
        <?php
        $f = 0;
        foreach ($retvalue AS $data) {
            if ($f == 0) {
                $f = 1;
                ?>
                <tr>
                    <td class="style2" align="center"><b><?php echo htmlspecialchars((string)$data['school_code']); ?>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo htmlspecialchars((string)$data['school_name']); ?></b></td>
                </tr>
                <?php
            }
        }
        ?>
    </table>
    <table width="95%" border="1" align="center" cellpadding="3" cellspacing="0" style="border-collapse: collapse; border: 0.5px solid #000000;">
        <?php
        $count = 1;
        $total = 0;
        $previous = '';
        $j = 0;
        foreach ($retvalue AS $data) {
            if ($previous != $data['fest_name']) {
                $count = 1;
                $previous = $data['fest_name'];

                if ($j != 0) {
                    ?>
                    <tr>
                        <td align="center" style="height:20px; border: 0.5px solid #000000;">&nbsp;</td>
                        <td align="left" style="height:20px; border: 0.5px solid #000000;">&nbsp;</td>
                        <td colspan="2" align="right" style="height:20px; font-weight: bold; border: 0.5px solid #000000;">Total Point :&nbsp;</td>
                        <td align="center" style="height:20px; font-weight: bold; border: 0.5px solid #000000;"><?php echo $total; ?></td>
                    </tr>
                    <?php
                    $total = 0;
                }
                ?>
                <tr>
                    <td colspan="5" align="center" class="fest-header" style="height:24px; border: 0.5px solid #000000;"><?php echo htmlspecialchars((string)$data['fest_name']); ?></td>
                </tr>
                <tr class="col-header">
                    <td width="40" align="center" style="height:20px; border: 0.5px solid #000000;">Sl No</td>
                    <td width="240" align="left" style="height:20px; border: 0.5px solid #000000;">&nbsp;Competition Item</td>
                    <td width="260" align="left" style="height:20px; border: 0.5px solid #000000;">&nbsp;Participant Name</td>
                    <td width="35" align="center" style="height:20px; border: 0.5px solid #000000;">Grade</td>
                    <td width="35" align="center" style="height:20px; border: 0.5px solid #000000;">Point</td>
                </tr>
                <?php
            }
            $total = $total + $data['point'];
            $j++;
            ?>
            <tr class="data-cell">
                <td align="center" style="height:20px; border: 0.5px solid #000000;"><?php echo $count++; ?></td>
                <td align="left" style="height:20px; border: 0.5px solid #000000;">&nbsp;<?php echo htmlspecialchars((string)$data['item_code'] . ' - ' . $data['item_name']); ?></td>
                <td align="left" style="height:20px; border: 0.5px solid #000000;">&nbsp;<?php echo htmlspecialchars((string)$data['participant_name']); ?></td>
                <td align="center" style="height:20px; font-weight: bold; border: 0.5px solid #000000;"><?php echo htmlspecialchars((string)$data['grade']); ?></td>
                <td align="center" style="height:20px; font-weight: bold; border: 0.5px solid #000000;"><?php echo htmlspecialchars((string)$data['point']); ?></td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td align="center" style="height:20px; border: 0.5px solid #000000;">&nbsp;</td>
            <td align="left" style="height:20px; border: 0.5px solid #000000;">&nbsp;</td>
            <td colspan="2" align="right" style="height:20px; font-weight: bold; border: 0.5px solid #000000;">Total Point :&nbsp;</td>
            <td align="center" style="height:20px; font-weight: bold; border: 0.5px solid #000000;"><?php echo $total; ?></td>
        </tr>
    </table>
</page>
