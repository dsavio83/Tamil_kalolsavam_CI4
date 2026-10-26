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
    .style9{ 
        font-size: 12px;
        font-weight: bold;
        color:#000000;
    }

    .tb{
        font-size: 12px;
        font-weight: bold;
        color:#000000;}
    .ety{
        font-size: 12px;
        color:#000000;
    }



    -->
</style>
<?php $this->load->view('report/report_pdf_style'); ?>

<style>
    @media print
    {
        h1 {page-break-before:always}
    }
</style>
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
            <td height="43" align="center" class="report-pdf-title">List of Students Participating More than One Items</td>
        </tr>
    </table>


    <?php
    $s = 0;
    $prev_fest = "";
    for ($j = 0; $j < count($fees_details); $j++) {
        $s++;
        if ($prev_fest != $fees_details[$j]['fest_id']) {
            if ($j != 0) {
                print("</table>");
                ?>

                <?Php
            }

            $prev_fest = $fees_details[$j]['fest_id'];
            $s = 1;
            ?>
            <table width="245%" align="center" border="1" cellpadding="0" cellspacing="0" >
                <tr>
                    <td colspan="6" align="center" class="style2" height="26" style="border-bottom:1px #000000; border-right:0px #000000; padding:2px;">Festival :&nbsp;&nbsp;<?php echo $fees_details[$j]['fest_name']; ?></td>
                </tr>
                <tr>
                    <td width="30" align="center" class="style3" style="border-right:1px #000000; padding:2px;" height="26">Sl.No</td>
                    <td width="50" align="center" class="style3" style="border-right:1px #000000; padding:2px;">Reg No.</td>
                    <td width="60" align="center" class="style3" style="border-right:1px #000000; padding:2px;">No.&nbsp;of&nbsp;Items</td>
                    <td width="240" align="left" class="style3" style="border-right:1px #000000; padding:2px;">&nbsp;Name </td>
                    <td width="260" align="left" class="style3" style="border-right:1px #000000; padding:2px;">School</td>
                </tr>
                <?php 
                } 
                ?>
                <tr>
                    <td class="ety"  align="center"  style="border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $s; ?></td>
                    <td class="ety"  align="center"  style="border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $fees_details[$j]['participant_id']; ?></td>
                    <td class="ety"  align="center"  style="border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $fees_details[$j]['cnt']; ?></td>
                    <td class="ety"  align="left"  style="border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo wordwrap($fees_details[$j]['participant_name'], 20, '<br>'); ?></td>
                    <td class="ety"  align="left"  style="border-top:1px #000000; border-right:1px #000000; padding:2px;"><?php echo wordwrap($fees_details[$j]['school_code'] . ' - ' . $fees_details[$j]['school_name'], 40, '<br>'); ?></td>
        </tr>
        <?php 
        } 
        ?>

    </table>
</page>
