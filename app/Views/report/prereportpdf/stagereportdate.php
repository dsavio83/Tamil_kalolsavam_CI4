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
<?php $this->load->view('report/report_pdf_style'); ?>
<page backtop="20mm" backbottom="20mm ">
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
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center">
            <tr>
                <td align="center" class="report-pdf-title">Stage Reports</td>
            </tr>
            <tr>
                <td align="center"><strong><?php echo $stagename[0]['stage_name']; ?> (<?php echo datetophpmodel($date); ?>)</strong></td>
            </tr>
        </table>




    <table width="100%" border="1" cellpadding="0" cellspacing="0">
        <tr>
            <td class="tb" height="25" align="center" width="30" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">Sl.No. </td>
            <td class="tb" height="25" align="left" width="197" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">&nbsp;&nbsp;Item</td>
            <td class="tb" height="25" align="left" width="163" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">&nbsp;&nbsp;Fest Name</td>
            <td class="tb" height="25" align="center" width="90" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;"> No.of <br />
                Participants/Teams</td>
            <td class="tb" height="25" align="center" width="75" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;"> Tentative Time </td>
            <td class="tb" height="25" align="center" width="65" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">Start Time </td>
            <td class="tb" height="25" align="center" width="65" style="border-bottom:1px #000000; border-right:0px #000000; padding:2px;">Remarks </td>
        </tr>
        <?php
        $count = 0;
        $grandtotal = 0;
        foreach ($stagedata as $data) {
            $count++;

            if (isset($data['is_off_stage']) && $data['is_off_stage'] == 'Y') {
                $time_type = (isset($data['time_type']) && $data['time_type'] == 'M') ? 'Minutes' : 'Seconds';
                $time = $data['max_time'] . ' ' . $time_type;
            } else {
                $time = $data['max_time'] * $data['no_of_participant'];
                $time = get_time_format($time);
            }
            ?>
            <tr>
                <td class="ety" width="30" align="center" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $count; ?></td>
                <td class="ety" width="197" align="left" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">&nbsp;<?php echo $data['item_code'] . ' - ' . $data['item_name']; ?></td>
                <td class="ety" width="163" align="left" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">&nbsp;<?php echo $data['fest_name']; ?></td>
                <td class="ety" width="90" align="center" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $data['no_of_participant']; ?></td>
                <td class="ety" width="75" align="center" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $time; ?></td>
                <td class="ety" width="65" align="center" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $data['start_time']; ?></td>
                <td class="ety" width="65" style="border-bottom:1px #000000; border-right:0px #000000; padding:2px;">&nbsp;</td>
            </tr>
        <?php } ?>

    </table>

</page>
