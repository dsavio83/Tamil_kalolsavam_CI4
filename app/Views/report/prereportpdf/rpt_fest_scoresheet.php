<style type="text/css">
    <!--
    .style1 {
        font-size: 18px;
        font-weight: bold;
        color: #660033;
    }
    .style2 {
        font-size: 24px;
        font-weight: bold;
    }
    .style3 {
        font-size: 11px;
        font-weight: bold;
        color: black;
    }


    -->
</style>
<?php $this->load->view('report/report_pdf_style'); ?>

<?php
$flag = 0;
$count = 0;
$check2 = '';
$i = 0;
foreach ($fest_details as $data) {

    $check = $data['fest_name'];
    $count = $data['no_of_participant'];
    if ($i != 0) {
        print("</page>");
    }
    $i++;
    ?>
    <page backtop="20mm" backbottom="40mm ">
        <page_header>
            <?php
            $this->load->view('report/report_header');
            ?>
        </page_header>
        <page_footer>
            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#D4D0C8">
                <tr>
                    <td  align="right" width="500" height="20" valign="top">Signature :</td>
                    <td align="left" width="200">&nbsp;</td>
                </tr>
                <tr>
                    <td   align="right" height="20" valign="top" >Name of judge:</td>
                    <td align="left">&nbsp;</td>
                </tr>
                <tr>
                    <td  align="right" height="40" valign="top">Addresss:</td>
                    <td align="left">&nbsp;</td>
                </tr>
            </table>
            <?php
            $this->load->view('report/report_footer');
            ?>
        </page_footer>   
        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class=						"heading_tab" style="margin-top: 2px;">
            <tr>
                <td height="37" align="center" class="report-pdf-title">Score Sheet</td>
            </tr>
        </table>
        <table width="67%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#D4D0C8">
            <tr>
                <td  align="center" height="32" colspan="11" style='border: 0 0.5px 0.5px 0 #000000 ; height:20 px;'><strong>Festival:<?php echo $data['fest_name']; ?></strong></td>
            </tr>          
            <tr>        
                <td width="37" height="32" style='border:0 0 0.5px 0.5px 0 #000000 ; height:20 px;'><strong>Item </strong></td>
                <td colspan="8" style='border:0 0 0.5px 0.5px 0 #000000 ; height:20 px;'><span class="style3"><?php echo wordwrap($data['item_code'] . " - " . $data['item_name'], 25, '<br>'); ?></span></td>
                <td style='border:0 0 0.5px 0.5px 0 #000000 ; height:20 px;' colspan="2"><strong>Date</strong><strong>:<?php echo datetophpmodel($data['start_time']); ?>&nbsp;&nbsp;<?php echo $data['stage_name']; ?></strong></td>
            </tr>
            <?php
            $root_path = defined('FCPATH') ? FCPATH : (defined('ROOTPATH') ? ROOTPATH : 'c:/xampp/htdocs/tk25/');
            $vp_img = '';
            foreach (['.png', '.PNG', '.jpg', '.JPG', '.jpeg', '.JPEG'] as $ext) {
                $candidate = $root_path . 'value_points/' . $data['item_code'] . $ext;
                if (is_file($candidate) && filesize($candidate) > 500) {
                    $vp_img = str_replace('\\', '/', $candidate);
                    break;
                }
            }
            ?>
            <?php if ($vp_img !== ''): ?>
                <tr>
                    <td align="center" colspan="11" style="border: 0 0 0.5px 0.5px 0 #000000; text-align: center; padding: 4px 0;">
                        <img src="<?php echo $vp_img; ?>" style="width: 550px;" />
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <td colspan="2" style='border:0 0 0.5px 0.5px 0 #000000 ; height:20 px;'><div align="left"><strong>.</strong></div>              	</td>
                <td colspan="6" align="center"><strong>Score for each value points</strong></td>
                <td colspan="3" style='border:0 0 0.5px 0.5px 0 #000000 ; height:20 px;'><div align="center">&nbsp;</div></td>
            </tr>
            <tr>
                <td  align="center"width=37 height="34" style='border:0 0 0.5px 0.5px 0 #000000 ; height:50 px;'><strong>Sl.No</strong>							            </td>
                <td  align="center"width=63 style='border:0 0 0.5px 0.5px 0 #000000 ; height:50 px;'><strong>Code.No  </strong>                </td>
                <td align="center" width=40 style='border:0 0 0.5px 0.5px 0 #000000 ; height:50 px;'>A</td>
                <td align="center"width=40 style='border:0 0 0.5px 0.5px 0 #000000 ; height:50 px;'>B</td>
                <td align="center" width=40 style='border:0 0 0.5px 0.5px 0 #000000 ; height:50 px;'>C</td>
                <td align="center"width=40 style='border:0 0 0.5px 0.5px 0 #000000 ; height:50 px;'>D</td>
                <td align="center"width=40 style='border:0 0 0.5px 0.5px 0 #000000 ; height:50 px;'>E</td>
                <td align="center"width=40 style='border:0 0 0.5px 0.5px 0 #000000 ; height:50 px;'>F</td>
                <td colspan="2" align="center" style='border:0 0 0.5px 0.5px 0#000000 ; height:50 px;'><strong>Total out of 100</strong></td>
                <td  align="center"width=62 style='border:0 0 0.5px 0.5px 0#000000 ; height:50 px;'><strong> Remarks</strong>                </td>
            </tr>
            <tr>
                <td  height="24" colspan="8" align="center"  style='border:0 0 0.5px 0.5px 0 #000000 ; height:50 px;'>&nbsp;</td>
                <td align="center"  style='border:0 0 0.5px 0.5px 0 #000000 ; height:50 px;'><strong>In Figures</strong></td>
                <td align="center"  height="24"  style='border:0 0 0.5px 0.5px 0 #000000 ; height:50 px;'><strong>In Words</strong></td>
                <td align="center"  height="24"  style='border:0 0 0.5px 0.5px 0 #000000 ; height:50 px;'>&nbsp;</td>
            </tr>
            <?php
            $sl = 0;
            for ($j = 0; $j < $count + 5; $j++) {
                $sl++;
                ?>           
                <tr>
                    <td align="center" width=37  height="46"  style='border:0 0 0.5px 0.5px 0 #000000 ; '><?php echo $sl; ?></td>
                    <td align="center"width=63   height="46"  style='border:0 0 0.5px 0.5px 0 #000000 ;'>&nbsp;</td>
                    <td align="center"width=27   height="46"   style='border:0 0 0.5px 0.5px 0 #000000 ; '>&nbsp;</td>
                    <td align="center"width=26   height="46"  style='border:0 0 0.5px 0.5px 0 #000000 ; '>&nbsp;</td>
                    <td align="center"width=27   height="46"  style='border:0 0 0.5px 0.5px 0 #000000 ; '>&nbsp;</td>
                    <td align="center"width=27   height="46"  style='border:0 0 0.5px 0.5px 0 #000000 ; '>&nbsp;</td>
                    <td align="center"width=26   height="46"  style='border:0 0 0.5px 0.5px 0 #000000 ; '>&nbsp;</td>
                    <td align="center"width=33   height="46"  style='border:0 0 0.5px 0.5px 0 #000000 ; '>&nbsp;</td>
                    <td align="center"width=83  style='border:0 0 0.5px 0.5px 0 #000000 ; height:50 px;'>&nbsp;</td>
                    <td align="center"width=200  height="46"  style='border:0 0 0.5px 0.5px 0 #000000 ; '>&nbsp;</td>
                    <td align="center"width=62  height="46"  style='border:0 0 0.5px 0.5px 0 #000000 ; '>&nbsp;</td>
                </tr>          
                <?php
            }
            ?>
        </table>
        <?php
    }
    ?>

</page>