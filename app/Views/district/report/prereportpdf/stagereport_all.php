<style type="text/css">
    <!--
    .style1 {
        font-size: 14px;
        font-weight: bold;
        color: #660033;
    }
    .style2{
        font-size: 12px;
        font-weight: bold;
        color:#000000;
    }
    .style3{
        font-size: 11px;
        font-weight: bold;
        color:#000000;
    }
    .tb{
        font-size: 11px;
        font-weight: bold;
        color:#000000;}

    .ety{
        font-size: 12px;
        color:#000000;
    }


    -->
</style>
<style>
    @media print
    {
        h1 {page-break-before:always}
    }
</style>
<?php
$s = 0;
$prev_ddt = "";
$prev_stageid = "";
if (count($retdata) > 0) {
for ($j = 0; $j < count($retdata); $j++) {

    if ($prev_ddt != $retdata[$j]['ddt']) {
        $prev_ddt = $retdata[$j]['ddt'];
        $prev_stageid = "";
        if ($j != 0) {
            print("</table></page>");
        }
        $datt = datetophpmodel($retdata[$j]['ddt']);
        ?>
        <?php $this->load->view('report/report_pdf_style'); ?>
<page backtop="20mm" backbottom="20mm " >
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
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" >
                    <tr>
                        <td align="center" class="report-pdf-title">Stage Report</td>
                    </tr>
                </table>






            <table width="100%" align="center" border="1" cellpadding="0" cellspacing="0" >
                <tr>
                    <td colspan="7" width="685" align="center" class="style2" height="26" style="border-bottom:0px #000000; border-right:0px #000000; padding:2px;">&nbsp;&nbsp;Date:&nbsp;&nbsp;<?php
                        echo $datt;
                        ;
                        ?>
                    </td>
                </tr>
                <?php
            }

            if ($prev_stageid != $retdata[$j]['stage_id']) {
                $prev_stageid = $retdata[$j]['stage_id'];
                $s = 0;
                ?>
                <tr>
                    <td colspan="7" width="685" align="center" class="style2" height="24" style="border-top:1px #000000; border-bottom:1px #000000; padding:2px;">
                        Stage :&nbsp;<?php
                        echo $retdata[$j]['stage_name'];
                        ;
                        ?>&nbsp;&nbsp;</td>
                </tr>

                <tr>
                    <td class="tb" width="30" align="center"  style="border-right:1px #000000; padding:2px;">Sl.No</td>
                    <td class="tb" width="197" align="left"  style="border-right:1px #000000; padding:2px;">&nbsp;Item</td>
                    <td class="tb" width="163" align="left"  style="border-right:1px #000000; padding:2px;">&nbsp;Fest Name</td>
                    <td class="tb" width="90" align="center"  style="border-right:1px #000000; padding:2px;">No. of Participants/Teams</td>
                    <td class="tb" width="75" align="center" style="border-right:1px #000000; padding:2px;">Tentative Time</td>
                    <td class="tb" width="65" align="center" style="border-right:1px #000000; padding:2px;">Start Time</td>
                    <td class="tb" width="65" align="center"  style="border-right:0px #000000; padding:2px;">Remarks</td>
                </tr>
                <?php
            }
            if ($retdata[$j]['is_off_stage'] == 'N') {
                $time = $retdata[$j]['max_time'] * $retdata[$j]['no_of_participant'];
                $time = get_time_format($time);
            } else {
                if ($retdata[$j]['time_type'] == 'M')
                    $timetye = 'Minite';
                else if ($retdata[$j]['time_type'] == 'S')
                    $timetye = 'Second';
                $time = $retdata[$j]['max_time'] . '  ' . $timetye;
            }
            $s++;
            ?>
        <tr>
            <td class="ety" width="30" align="center" style="border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $s; ?></td>
            <td class="ety" width="197" align="left" style="border-top:1px #000000; border-right:1px #000000; padding:2px;">&nbsp;<?php echo $retdata[$j]['item_code'] . ' - ' . $retdata[$j]['item_name']; ?></td>
            <td class="ety" width="163" align="left" style="border-top:1px #000000; border-right:1px #000000; padding:2px;">&nbsp;<?php echo $retdata[$j]['fest_name']; ?></td>
            <td class="ety" width="90" align="center" style="border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $retdata[$j]['no_of_participant']; ?></td>
            <td class="ety" width="75" align="center" style="border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $time; ?></td>
            <td class="ety" width="65" align="center" style="border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $retdata[$j]['dtm']; ?></td>
            <td class="ety" width="65" style="border-top:1px #000000; border-right:0px #000000; padding:2px;">&nbsp;</td>
        </tr>
        <?php
        }
        ?>

    </table>
</page>
<?php
} else {
?>
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
    <table width="100%" align="center" border="1" cellpadding="4" cellspacing="0">
        <tr>
            <td align="center" class="style2" height="40">No data found for the selected criteria.</td>
        </tr>
    </table>
</page>
<?php
}
?>
