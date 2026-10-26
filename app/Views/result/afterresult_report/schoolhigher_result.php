
<style type="text/css">
    <!--
    .style1 {
        font-size: 15px;
        font-weight: bold;
        color: #660033;
    }
</style>
<?php $this->load->view('report/report_pdf_style'); ?>


<?php
$prevfestid = "";
$schoolid = "";
for ($j = 0; $j < count($retvalue); $j++) {

    if ($schoolid != $retvalue[$j]['school_code']) {

        $count = 0;
        $schoolid = $retvalue[$j]['school_code'];
        $prevfestid = "";

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
            <table width="100%" height="62" border="0" align="center">
                <tr>
                    <td align="center" class="report-pdf-title" height="30" valign="top">Participants Eligible For Higher Level Competition From <?php echo esc($retvalue[$j]['school_name']) . ' (' . esc($retvalue[$j]['school_code']) . ')'; ?></td>
                </tr>
            </table>
            <table width="96%" border="1" align="center" cellpadding="0" cellspacing="0">
                <?php
            }
            if ($prevfestid != $retvalue[$j]['fest_id']) {
                $prevfestid = $retvalue[$j]['fest_id'];
                $count = 0;
                ?>

                <tr><td colspan="4" align="center" style='border: 0 0 0.5px 0.5px 0 #000000; height:20px; font-weight:bold; background-color:#f0f0f0;'>
                        <?php echo esc($retvalue[$j]['fest_name']); ?>
                    </td></tr>
                <tr>
                    <td width="40" align="center" style='border: 0 0 0.5px 0.5px 0 #000000; height:20px; font-weight:bold;'>Sl No </td>
                    <td width="195" align="left" style='border: 0 0 0.5px 0.5px 0 #000000; height:20px; font-weight:bold;'>Item</td>
                    <td width="300" align="left" style='border: 0 0 0.5px 0.5px 0 #000000; height:20px; font-weight:bold;'>Name of Participant</td>
                    <td width="60" align="center" style='border: 0 0 0.5px 0.5px 0 #000000; height:20px; font-weight:bold;'>Class</td>
                </tr>

                <?php
            }
            $count++;
            ?>

            <tr>
                <td align="center" style='border: 0 0 0.5px 0.5px 0 #000000; height:20px;'><?php echo $count; ?></td>
                <td align="left" style='border: 0 0 0.5px 0.5px 0 #000000; height:20px;'><?php echo esc($retvalue[$j]['item_code']) . ' - ' . esc($retvalue[$j]['item_name']); ?></td>
                <td align="left" style='border: 0 0 0.5px 0.5px 0 #000000; height:20px;'><?php echo wordwrap(esc($retvalue[$j]['participant_name']), 50, '<br>'); ?></td>
                <td align="center" style='border: 0 0 0.5px 0.5px 0 #000000; height:20px;'><?php echo esc($retvalue[$j]['class']); ?></td>
            </tr>
            <?php
        }
        ?>

    </table>
</page>
