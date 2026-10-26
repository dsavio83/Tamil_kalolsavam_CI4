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
</style>
<?php $this->load->view('report/report_pdf_style'); ?>


<?php
$count = 0;
$previous = "";
$pre = "";
$j = 0;
$db = \Config\Database::connect();

foreach ($retvalue AS $data) {
    if ($previous != $data['item_code']) {
        if (!empty($data['item_code'])) {
            $db->table('sd_result_master')->where('item_code', (int)$data['item_code'])->update(['is_printed' => 1]);
        }
        $previous = $data['item_code'];
        $count = 0;
        if ($j != 0) {
            print("</table></page>");
        }
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
            <table width="100%" height="62" border="0" cellpadding="0" cellspacing="0" align="center">
                <tr>
                    <td  align="center"  class="report-pdf-title">All Results</td>
                </tr>
                <tr>
                    <td  align="center" ><b>  Festival : <?php echo $data['fest_name']; ?>&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $data['item_code']; ?> ( <?php echo $data['item_name']; ?>)</b></td>
                </tr>
            </table>

            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
                <tr>
                    <td width="43" align="center" class="style2" style='border: 0 0 0.5px 0.5px 0 #666666;  height:20px;'>Sl No</td>
                    <td width="250" align="left" class="style2" style='border: 0 0 0.5px 0.5px 0 #666666;  height:20px;'>&nbsp;Name </td>
                    <td width="250" align="left" class="style2" style='border: 0 0 0.5px 0.5px 0 #666666;  height:20px;'>&nbsp;School </td>
                    <td width="40" align="center" class="style2" style='border: 0 0 0.5px 0.5px 0 #666666;  height:20px;'>&nbsp;Rank</td>
                    <td width="40" align="center" class="style2" style='border: 0 0 0.5px 0.5px 0 #666666;  height:20px;'>&nbsp;Grade</td>
                </tr>
                <?php
            }
            //$count=0;
            // $pre=$data['item_code'];
            $count++;
            $j++;
            ?>
            <tr>
                <td style='border: 0 0 0.5px 0.5px 0 #666666;  height:20px;' align="center"><?php echo $count; ?></td>
                <td style='border: 0 0 0.5px 0.5px 0 #666666;  height:20px;'>&nbsp;<?php echo wordwrap($data['participant_name'], 50, '<br>'); ?></td>
                <td style='border: 0 0 0.5px 0.5px 0 #666666;  height:20px;'>&nbsp;<?php echo wordwrap($data['school_code'] . ' - ' . $data['school_name'], 50, '<br>'); ?></td>
                <td style='border: 0 0 0.5px 0.5px 0 #666666;  height:20px;' align="center"><?php echo $data['rank']; ?></td>
                <td style='border: 0 0 0.5px 0.5px 0 #666666;  height:20px;' align="center"><?php echo $data['grade']; ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
</page>
