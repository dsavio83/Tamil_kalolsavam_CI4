<style type="text/css">
    <!--
    .style1 {
        font-size: 14px;
        font-weight: bold;
        color: #660033;
    }
    .style2{
        font-size: 11px;
        font-weight: bold;
        color:#000000;
    }
    .style3{
        font-size: 12px;
        color:#000000;
    }
    -->
</style>
<page backtop="30mm" backbottom="20mm ">
    <page_header>
        <?php
        $this->load->view('report/report_header');
        ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="4" align="center">

            <tr>
                <td height="23" align="center" class="style1">Reports of &nbsp; <?php echo $stagename[0]['stage_name']; ?> </td>
            </tr>
        </table>
    </page_header>
    <page_footer>
        <?php
        $this->load->view('report/report_footer');
        ?>
    </page_footer>



    <table width="100%" border="1" cellpadding="0" cellspacing="0">
        <?php
        $count = 0;
        $grandtotal = 0;
        $prev_date = "";
        foreach ($stagedata as $data) {

            if ($data['time_type'] == 'M')
                $time_type = 'Minutes';
            if ($data['is_off_stage'] == 'Y') {
                $time = $data['max_time'] . ' ' . $time_type;
            } else if ($data['is_off_stage'] == 'N') {

                $time = $data['max_time'] * $data['no_of_participant'];
                $time = get_time_format($time);
            }


            if ($prev_date != $data['ddt']) {
                $prev_date = $data['ddt'];
                $dater = datetophpmodel($data['ddt']);
                $count = 0;
                ?>
                <tr><td class="style2" colspan="6" style="border-bottom:1px #000000; border-right:1px #000000;" align="center">Item on &nbsp;<?php echo $dater; ?></td></tr>
                <tr>
                    <td class="style2" align="center" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">Sl.No. </td>
                    <td class="style2" align="left"  style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">&nbsp;Item</td>
                    <td class="style2" align="center" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;"> No.of<br />Participants/Teams</td>
                    <td class="style2" align="center" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;"> Tentative Time </td>
                    <td class="style2" align="center" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;"> Start Time </td>
                    <td class="style2" align="center" width="150" style="border-bottom:1px #000000; border-right:0px #000000; padding:2px;">Remarks </td>
                </tr>
                <?php
            }

            $count++;
            ?>
            <tr>
                <td class="style3" align="center" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $count; ?></td>
                <td class="style3" align="left" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;"><?php echo wordwrap($data['item_code'] . ' - ' . $data['item_name'] . ' (' . $data['fest_name'] . ')',35,'<br>'); ?></td>
                <td class="style3" align="center" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $data['no_of_participant']; ?></td>
                <td class="style3" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;" align="center"><?php echo $time; ?></td>
                <td class="style3" align="center" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $data['dtm']; ?></td>
                <td  class="style3"style="border-bottom:1px #000000; border-right:0px #000000; padding:2px;">&nbsp;</td>
            </tr>
        <?php } ?>


    </table>

</page>