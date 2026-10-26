<style type="text/css">
    table {
        font-family: Arial, Helvetica, sans-serif;
        color: #000000;
    }
    .header-text {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 10pt;
        font-weight: bold;
        color: #000000;
    }
    .data-table {
        border-collapse: collapse;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 8pt;
        margin-top: 5px;
    }
    .th-cell {
        border: 0.5px solid #000000;
        background-color: #f0f2f5;
        text-align: center;
        vertical-align: middle;
        font-weight: bold;
        font-size: 8pt;
        padding: 4px 2px;
        color: #000000;
    }
    .td-cell {
        border: 0.5px solid #000000;
        vertical-align: middle;
        font-size: 8pt;
        padding: 4px 3px;
        color: #000000;
    }
    .footer-table {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 9pt;
        color: #000000;
    }
</style>

<?php $this->load->view('report/report_pdf_style'); ?>
<page backtop="20mm" backbottom="20mm">

    <page_header>
        <?php
        $this->load->view('report/report_header');
        ?>
         
    </page_header>
    <page_footer>

        <table width="50%" border="0" align="center" cellpadding="0" cellspacing="0" class="footer-table">
            <tr>
                <td align="right" width="80" height="20" valign="top">Entered by :</td>
                <td align="right" width="500" height="20" valign="top">Signature :</td>
                <td align="left" width="100">&nbsp;</td>
            </tr>
            <tr>
                <td align="right" width="80" height="20" valign="top">Checked by :</td>
                <td align="right" height="20" valign="top">Program Convenor</td>
                <td align="left">&nbsp;</td>
            </tr>
        </table>
        <?php
        $this->load->view('report/report_footer');
        ?>
    </page_footer>
<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" style="color: #000000; font-family: Arial, Helvetica, sans-serif;">
            <tr>
                <td align="left" width="380" class="report-pdf-meta" style="padding: 2px;">
                    &nbsp;&nbsp;&nbsp;&nbsp;<?php
                    $festName = $retdat[0]['fest_name'];
                    $festName = str_replace('TAMIL KALAIVIZHA', '', $festName);
                    echo $itemcode . ' - ' . $retdat[0]['item_name'] . ' (' . (empty($festName) ? "no data" : trim($festName)) . ')';
                    ?>
                </td>
                <td align="right" width="320" class="report-pdf-meta" style="padding: 2px;">
                    <?php echo $retdat[0]['stage_name']; ?>&nbsp; on&nbsp;
                    <?php
                    $date = explode(' ', $retdat[0]['start_time']);
                    echo datetophpmodel($date[0]);
                    ?>
                </td>
            </tr>
        </table>


    <table width="96%" cellpadding="0" cellspacing="0" align="center" class="data-table">
        <thead>
            <tr>
                <th width="18" rowspan="2" align="center" class="th-cell">Sl<br>No</th>
                <th width="24" rowspan="2" align="center" class="th-cell">Reg<br>No</th>
                <th width="24" rowspan="2" align="center" class="th-cell">Code<br>No</th>
                <th width="115" rowspan="2" align="center" class="th-cell">Name</th>
                <th width="145" rowspan="2" align="center" class="th-cell">School</th>
                <th colspan="<?php echo (int)($judjes_count + 1); ?>" align="center" class="th-cell">Marks</th>
                <th width="24" rowspan="2" align="center" class="th-cell">%</th>
                <th width="16" rowspan="2" align="center" class="th-cell">R</th>
                <th width="16" rowspan="2" align="center" class="th-cell">G</th>
                <th width="16" rowspan="2" align="center" class="th-cell">P</th>
            </tr>
            <tr>
                <?php for ($k = 1; $k <= $judjes_count; $k++) { ?>
                    <th width="20" align="center" class="th-cell"><?php echo $k; ?></th>
                <?php } ?>
                <th width="24" align="center" class="th-cell">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $withheldno = 0;
            $appeal = 0;
            $sl = 0;
            for ($j = 0; $j < count($retdat); $j++) {
                $sl++;
                $symbol = '';
                if ($retdat[$j]['is_publish'] == 'N') {
                    $symbol .= '*';
                }

                if ($retdat[$j]['spo_id'] > 0) {
                    $symbol .= '*';
                    if ($symbol == '**') {
                        $appeal++;
                    }
                    $withheldno++;
                }

                $name = $retdat[$j]['participant_name'];
                $name = wordwrap($name, 24, "<br />", 1);

                $text = $retdat[$j]['school_code'] . ': ' . $retdat[$j]['school_name'];
                $text = wordwrap($text, 32, "<br />", 1);

                $marks = explode('#$#', $retdat[$j]['marks']);
                $percentage = $retdat[$j]['percentage'];
                $print = round($percentage, 2);
                ?>
                <tr>
                    <td width="18" align="center" class="td-cell"><?php echo $sl; ?></td>
                    <td width="24" align="center" class="td-cell" style="font-weight: bold;"><?php echo $retdat[$j]['participant_id'] . $symbol; ?></td>
                    <td width="24" align="center" class="td-cell"><?php echo $retdat[$j]['code_no']; ?></td>
                    <td width="115" align="left" class="td-cell"><?php echo $name; ?></td>
                    <td width="145" align="left" class="td-cell"><?php echo $text; ?></td>

                    <?php for ($k = 0; $k < $judjes_count; $k++) { ?>
                        <td width="20" align="center" class="td-cell"><?php echo @$marks[$k]; ?></td>
                    <?php } ?>

                    <td width="24" align="center" class="td-cell" style="font-weight: bold;"><?php echo $retdat[$j]['total_mark']; ?></td>
                    <td width="24" align="center" class="td-cell"><?php echo $print; ?></td>
                    <td width="16" align="center" class="td-cell" style="font-weight: bold;"><?php echo $retdat[$j]['rank']; ?></td>
                    <td width="16" align="center" class="td-cell" style="font-weight: bold;"><?php echo $retdat[$j]['grade']; ?></td>
                    <td width="16" align="center" class="td-cell"><?php echo $retdat[$j]['point']; ?></td>
                </tr>

            <?php } ?>
        </tbody>
    </table>
    <br />
    <table align="center" width="96%" border="0" cellspacing="0" cellpadding="0" class="footer-table">
        <tr>
            <td width="140" align="left">**No of withheld : <?php echo $appeal; ?></td>
            <td width="140">No.of Registered : &nbsp;&nbsp;<?php echo $participated[0]['picount']; ?></td>
            <td width="140">No of Absentee : &nbsp;&nbsp;<?php echo((count($absentee) and $absentee[0]) ? count($absentee) : 0 ); ?></td>
            <td width="130">*No of Appeal Entry : <?php echo ($withheldno - $appeal); ?></td>
        </tr>
        <tr>
            <td colspan="4" style="padding-top: 5px;">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="footer-table">
                    <tr>
                        <td valign="top" width="120">Absentee Reg No :</td>
                        <td valign="top">
                            <?php
                            for ($k = 1; $k <= count($absentee); $k++) {
                                echo $absentee[$k - 1] . '&nbsp;';
                                if (($k % 12 == 0) && ($k != 0)) {
                                    print("<br>");
                                }
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</page>
