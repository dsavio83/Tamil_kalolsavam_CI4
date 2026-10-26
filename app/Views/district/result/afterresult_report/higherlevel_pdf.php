<style type="text/css">
    <!--
    .style1 {
        font-size: 15px;
        font-weight: bold;
        color: #0d6efd;
    }
    .style2{
        font-size: 10px;
        font-weight: bold;
        color:#000000;
    }
</style>
<?php $this->load->view('report/report_pdf_style'); ?>

<?php
$count = 0;
$prevfestid = "";
for ($j = 0; $j < count($retvalue); $j++) {
    if ($prevfestid != $retvalue[$j]['fest_id']) {
        $count = 0;
        $prevfestid = $retvalue[$j]['fest_id'];
        if ($j != 0) {
            print("</table></page>");
        }
        ?>

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
            <table width="100%"  border="0" align="center">
                <tr>
                    <td  align="center" height="30" valign="top" class="report-pdf-title">Participants Eligible For Higher Level Competition in <?php echo esc($retvalue[$j]['fest_name']); ?></td>
                </tr></table>

            <table width="96%" border="1" align="center" cellpadding="0" cellspacing="0">


                <tr>
                    <td width="35" align="center" style='border: 0 0 0.5px 0.5px 0 #000000;  height:20px; font-weight:bold;'>Sl No </td>
                    <td width="175" align="left" style='border: 0 0 0.5px 0.5px 0 #000000;  height:20px; font-weight:bold;'>Item</td>
                    <td width="210" align="left" style='border: 0 0 0.5px 0.5px 0 #000000;  height:20px; font-weight:bold;'>Name of Participant</td>
                    <td width="40" align="center" style='border: 0 0 0.5px 0.5px 0 #000000;  height:20px; font-weight:bold;'>Class</td>
                    <td width="200" align="left" style='border: 0 0 0.5px 0.5px 0 #000000;  height:20px; font-weight:bold;'>School </td>
                </tr>
                <?php
            }
            if ($retvalue[$j]['grade'] == 'A') {
                $count++;
                ?>



                <tr>
                    <td align="center" style='border: 0 0 0.5px 0.5px 0 #000000;  height:20px;' class="style2"><?php echo $count; ?></td>
                    <td align="left" style='border: 0 0 0.5px 0.5px 0 #000000;  height:20px;' class="style2"><?php echo wordwrap(esc($retvalue[$j]['item_code'] . ' - ' . $retvalue[$j]['item_name']), 30, '<br>'); ?></td>
                    <td align="left" style='border: 0 0 0.5px 0.5px 0 #000000;  height:20px;' class="style2"><?php echo wordwrap(esc($retvalue[$j]['participant_name']), 40, '<br>'); ?></td>
                    <td align="center" style='border: 0 0 0.5px 0.5px 0 #000000;  height:20px;' class="style2"><?php echo esc($retvalue[$j]['class']); ?></td>
                    <td align="left" style='border: 0 0 0.5px 0.5px 0 #000000;  height:20px;' class="style2">
                        <?php echo wordwrap(esc($retvalue[$j]['school_code'] . ' - ' . $retvalue[$j]['school_name']), 40, '<br>'); ?>
                        <?php if (!empty($retvalue[$j]['sub_district_name'])) { ?>
                            <br/><span style="font-size:9px; color:#555;">(<?php echo esc($retvalue[$j]['sub_district_name']); ?>)</span>
                        <?php } ?>
                    </td>

                </tr>
                <?php
            }
        }
        ?>

    </table>
</page>
