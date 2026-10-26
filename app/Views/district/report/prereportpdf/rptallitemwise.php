<style type="text/css">
    <!--
    .style1 {
        font-size: 14px;
        font-weight: bold;
        color: #660033;
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
<?php
$prev_item_code = "";

for ($j = 0; $j < count($itempart); $j++) {

    if ($prev_item_code != $itempart[$j]['item_code']) {

        $prev_item_code = $itempart[$j]['item_code'];
        $count = 0;
        if ($j != 0) {
            print("</table></page>");
        }
        ?>


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
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top: 2px;">
                    <tr>
                        <td class="report-pdf-title" align="center">Itemwise participants list : <?php echo $itempart[$j]['fest_name']; ?></td>
                    </tr>
                </table>
 


            <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="7" class="tb" align="center" style=" border-bottom:1px #000000; border-right:0px #000000; padding:2px;">Item  :&nbsp;&nbsp;
                        <?php echo $itempart[$j]['item_code']; ?>&nbsp;-&nbsp;<?php echo $itempart[$j]['item_name']; ?>&nbsp;&nbsp;</td>

                </tr>

                <tr>
                    <td class="tb" width='35'  align="center" style=" border-bottom:1px #000000; border-right:1px #000000; padding:2px;">Sl.No.</td>
                    <td class="tb" width='55'  align="center" style=" border-bottom:1px #000000; border-right:1px #000000; padding:2px;">Reg No.</td>
                    <td class="tb" width='200'  align="left" style=" border-bottom:1px #000000; border-right:1px #000000; padding:2px;">&nbsp;Name</td>
                    <td class="tb" width='30'  align="center" style=" border-bottom:1px #000000; border-right:1px #000000; padding:2px;">B/G</td>
                    <td class="tb"  width='35'  align="center" style=" border-bottom:1px #000000; border-right:1px #000000; padding:2px;">Class</td>
                    <td class="tb" width='190'  align="left" style=" border-bottom:1px #000000; border-right:1px #000000; padding:2px;">&nbsp;School</td>
                    <td class="tb" width='135'  align="left" style=" border-bottom:1px #000000; border-right:0px #000000; padding:2px;">&nbsp;Sub District</td>
                </tr>
                <?php
            }


            $count++;
            ?>
            <tr>
                <td class="ety"  align="center" style=" border-bottom:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $count; ?></td>
                <td class="ety"  align="center" style=" border-bottom:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $itempart[$j]['participant_id']; ?></td>
                <td class="ety" align="left" style=" border-bottom:1px #000000; border-right:1px #000000; padding:2px;"><?php echo wordwrap($itempart[$j]['participant_name'], 25, '<br>'); ?></td>
                <td class="ety" align="center" style=" border-bottom:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $itempart[$j]['gender']; ?></td>                  
                <td class="ety" align="center" style=" border-bottom:1px #000000; border-right:1px #000000; padding:2px;"><?php echo $itempart[$j]['class']; ?></td>
                <td class="ety" align="left" style=" border-bottom:1px #000000; border-right:1px #000000; padding:2px;"><?php echo wordwrap($itempart[$j]['school_code'] . ' - ' . $itempart[$j]['school_name'], 26, '<br>'); ?></td>
                <td class="ety" align="left" style=" border-bottom:1px #000000; border-right:0px #000000; padding:2px;"><?php echo htmlspecialchars($itempart[$j]['sub_district_name'] ?? ''); ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
</page>
