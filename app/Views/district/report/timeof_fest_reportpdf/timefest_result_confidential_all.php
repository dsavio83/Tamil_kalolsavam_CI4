
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

<?php
$k = 0;
$appeal = 0;
$withheldno = 0;
$previtem = "";
$Absent_count = 0;

$no = 0;
for ($j = 0; $j < count($festresult); $j++) {

    if ($previtem != $festresult[$j]['item_code']) {
        $sl = 0;

        $temp = $previtem;
        $previtem = $festresult[$j]['item_code'];
        $this->load->model('report/Timefest_model');
        $itemcount = $this->Timefest_model->timeoffest_count($temp);
        $absentee_list = $this->Timefest_model->timeoffest_result_absentee($temp);
        $absentee = explode(',', (string)$absentee_list);
        $this->Reg_no = array();

        if ($j != 0) {
            $rt = 0;
            for ($ty = 0; $ty < count($absentee); $ty++) {
                $this->Reg_no[$ty] = $absentee[$ty];
                $rt++;
            }
            $Absent_reg_no = (count($absentee) and (trim($absentee[0]))) ? count($absentee) : '0';
            $no_reg_tot = $itemcount[0]['picount'];

            $withheldcount = $withheldno - $appeal;

            print("</tbody></table><br /><table align='center' width='96%' border='0' cellspacing='0' cellpadding='0' class='footer-table'><tr>
        <td width='140' align='left'>**No of Withheld :&nbsp;&nbsp;$appeal</td>
        <td width='140'>No.of Registered :&nbsp;&nbsp;$no_reg_tot</td>
        <td width='140'>No of Absentee :&nbsp;&nbsp;$Absent_reg_no</td>
        <td width='130'>*No of Appeal Entry :&nbsp;&nbsp;$withheldcount</td>
        </tr><tr><td colspan='4' style='padding-top: 5px;'><table width='100%' border='0' cellspacing='0' cellpadding='0' class='footer-table'><tr><td valign='top' width='120'>Absentee Reg No :</td><td valign='top'>");
            for ($i = 1; $i <= $Absent_reg_no; $i++) {
                echo $this->Reg_no[$i - 1] . '&nbsp;';

                if (($i % 12 == 0) && ($i != 0)) {
                    print("<br>");
                }
            }
            print("</td></tr>");
            print("</table>");
            print("</td></tr>");
            print("</table></page>");
            $withheldno = 0;
        }
        $ctt = (int)$festresult[$j]['no_of_judges'];
        ?>   
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
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <?php
                            $festName = $festresult[$j]['fest_name'];
                            $festName = str_replace('TAMIL KALAIVIZHA', '', $festName);
                            echo $festresult[$j]['item_code'] . ' - ' . $festresult[$j]['item_name'] . ' (' . (empty($festName) ? "no data" : trim($festName)) . ')';
                            ?> 
                        </td>
                        <td align="right" width="320" class="report-pdf-meta" style="padding: 2px;">
                            <?php echo $festresult[$j]['stage_name'] . ' on ' . datetophpmodel($festresult[$j]['start_time']); ?> 
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
                        <th colspan="<?php echo $ctt + 1; ?>" align="center" class="th-cell">Marks</th>
                        <th width="24" rowspan="2" align="center" class="th-cell">%</th>
                        <th width="16" rowspan="2" align="center" class="th-cell">R</th>
                        <th width="16" rowspan="2" align="center" class="th-cell">G</th>
                        <th width="16" rowspan="2" align="center" class="th-cell">P</th>
                    </tr>
                    <tr>
                        <?php
                        $ct = (int)$festresult[$j]['no_of_judges'];
                        for ($k = 0; $k < $ct; $k++) {
                            ?>
                            <th width="20" align="center" class="th-cell"><?php echo $k + 1; ?></th>
                        <?php } ?>
                        <th width="24" align="center" class="th-cell">Total</th>
                    </tr>
                </thead>
                <tbody>
                <?php
            }
            $sl++;
            $symbol = '';
            if ($festresult[$j]['is_publish'] == 'N') {
                $symbol .= '*';
            }

            if ($festresult[$j]['spo_id'] > 0) {
                $symbol .= '*';
                if ($symbol == '**') {
                    $appeal++;
                }
                $withheldno++;
            }

            $name = $festresult[$j]['participant_name'];
            $name = wordwrap($name, 24, "<br />", 1);

            $text = $festresult[$j]['school_code'] . ': ' . $festresult[$j]['school_name'];
            $text = wordwrap($text, 32, "<br />", 1);

            $marks = explode('#$#', $festresult[$j]['marks']);
            $percentage = $festresult[$j]['percentage'];
            $prnt = round($percentage, 2);
            ?>
            <tr>
                <td width="18" align="center" class="td-cell"><?php echo $sl; ?></td>
                <td width="24" align="center" class="td-cell" style="font-weight: bold;"><?php echo $festresult[$j]['participant_id'] . $symbol; ?></td>
                <td width="24" align="center" class="td-cell"><?php echo $festresult[$j]['code_no']; ?></td>
                <td width="115" align="left" class="td-cell"><?php echo $name; ?></td>
                <td width="145" align="left" class="td-cell"><?php echo $text; ?></td>
                <?php
                for ($k = 0; $k < $ct; $k++) {
                    ?>
                    <td width="20" align="center" class="td-cell"><?php echo @$marks[$k]; ?></td>
                    <?php
                }
                ?>
                <td width="24" align="center" class="td-cell" style="font-weight: bold;"><?php echo $festresult[$j]['total_mark']; ?></td>
                <td width="24" align="center" class="td-cell"><?php echo $prnt; ?></td>
                <td width="16" align="center" class="td-cell" style="font-weight: bold;"><?php echo $festresult[$j]['rank']; ?></td>
                <td width="16" align="center" class="td-cell" style="font-weight: bold;"><?php echo $festresult[$j]['grade']; ?></td>
                <td width="16" align="center" class="td-cell"><?php echo $festresult[$j]['point']; ?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <?php
    $temp = $festresult[$j - 1]['item_code'];
    $itemcount = $this->Timefest_model->timeoffest_count($festresult[$j - 1]['item_code']);
    $absentee_list = $this->Timefest_model->timeoffest_result_absentee($temp);
    $absentee = explode(',', (string)$absentee_list);

    $this->Reg_no = array();
    $rt = 0;
    for ($ty = 0; $ty < count($absentee); $ty++) {
        $this->Reg_no[$ty] = $absentee[$ty];
        $rt++;
    }
    $Absent_reg_no = (count($absentee) and (trim($absentee[0]))) ? count($absentee) : '0';
    $no_reg_tot = $itemcount[0]['picount'];
    $withheldcount = $withheldno - $appeal;

    print("<br /><table align='center' width='96%' border='0' cellspacing='0' cellpadding='0' class='footer-table'><tr>
        <td width='140' align='left'>**No of Withheld :&nbsp;&nbsp;$appeal</td>
        <td width='140'>No.of Registered :&nbsp;&nbsp;$no_reg_tot</td>
        <td width='140'>No of Absentee :&nbsp;&nbsp;$Absent_reg_no</td>
        <td width='130'>*No of Appeal Entry :&nbsp;&nbsp;$withheldcount</td>
        </tr><tr><td colspan='4' style='padding-top: 5px;'><table width='100%' border='0' cellspacing='0' cellpadding='0' class='footer-table'><tr><td valign='top' width='120'>Absentee Reg No :</td><td valign='top'>");
    for ($i = 1; $i <= $Absent_reg_no; $i++) {
        echo $this->Reg_no[$i - 1] . '&nbsp;';

        if (($i % 12 == 0) && ($i != 0)) {
            print("<br>");
        }
    }
    print("</td></tr>");
    print("</table>");
    print("</td></tr>");
    print("</table>");
    ?>
</page>
