<?php $this->load->view('report/report_pdf_style'); ?>
<page backtop="20mm" backbottom="22mm" backleft="10mm" backright="10mm">
    <page_header>
        <?php
        $this->load->view('report/report_header');
        ?>
    </page_header>
    <page_footer>
        <table style="width: 100%;">
            <tr>
                <td style="font-size: 10px; color: #64748b; text-align: center; padding-bottom: 4px;">
                    <em>To avoid schedule clashes, make the necessary changes in stage allotment.</em>
                </td>
            </tr>
        </table>
        <?php
        $this->load->view('report/report_footer');
        ?>
    </page_footer>   
    <?php
    $retdata = (!empty($retdata) && is_array($retdata)) ? $retdata : [];
    $displayDate = (!empty($date) && strtotime($date) !== false) ? date('d M Y', strtotime($date)) : (!empty($date) ? htmlspecialchars($date) : 'All Dates');
    $resarr = array();
    $prevdata = "";
    $items = "";
    $i = 0;

    for ($x = 0; $x < count($retdata); $x++) {
        $flag = 0;
        $item[$x] = wordwrap($retdata[$x]['item_name'], 25, '<br>') . ' (' . $retdata[$x]['stimer'] . ')<br>';
        for ($k = $x + 1; $k < count($retdata); $k++) {
            if (($retdata[$x]['participant_id']) == ($retdata[$k]['participant_id'])) {
                if ((($retdata[$x]['end_time']) > ($retdata[$k]['start_time'])) && ($flag == 0)) {
                    $item[$x] = $item[$x] . '' . wordwrap($retdata[$k]['item_name'], 25, '<br>') . ' (' . $retdata[$k]['stimer'] . ')<br>';
                    $resarr[$x] = array(
                        "id" => $retdata[$x]['participant_id'],
                        "name" => $retdata[$x]['participant_name'],
                        "item" => $item[$x],
                        "code" => $retdata[$x]['school_code'],
                        "sname" => $retdata[$x]['school_name']
                    );
                    $i++;
                }
            }
        }
    }
    ?>

    <table style="width: 100%;" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td class="report-pdf-title" style="width: 100%; padding-bottom: 3px;">
                SCHEDULE CLASHES REPORT
            </td>
        </tr>
        <tr>
            <td class="report-pdf-subtitle" style="width: 100%; padding-bottom: 6px;">
                Clashes Report on <?php echo $displayDate; ?> &nbsp;|&nbsp; All Festivals
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse;" border="0" cellpadding="0" cellspacing="0" align="center">
        <thead>
            <tr> 
                <td style="width: 7%; text-align: center; background-color: #f1f5f9; border: 0.5px solid #000000; height: 22px; font-size: 10px; font-weight: bold;">Sl.No</td>
                <td style="width: 12%; text-align: center; background-color: #f1f5f9; border: 0.5px solid #000000; height: 22px; font-size: 10px; font-weight: bold;">Reg No</td>
                <td style="width: 27%; text-align: left; background-color: #f1f5f9; border: 0.5px solid #000000; height: 22px; font-size: 10px; font-weight: bold;">&nbsp;&nbsp;Name of Student</td>
                <td style="width: 25%; text-align: left; background-color: #f1f5f9; border: 0.5px solid #000000; height: 22px; font-size: 10px; font-weight: bold;">&nbsp;&nbsp;School</td>
                <td style="width: 29%; text-align: left; background-color: #f1f5f9; border: 0.5px solid #000000; height: 22px; font-size: 10px; font-weight: bold;">&nbsp;&nbsp;Clash Items</td>
            </tr>
        </thead>
        <tbody>
            <?php
            if (empty($resarr)) {
                ?>
                <tr>
                    <td colspan="5" style="border: 0.5px solid #000000; height: 35px; text-align: center; font-weight: bold; color: #555555; font-size: 11px; padding: 10px;">
                        No schedule clashes found for the selected criteria.
                    </td>
                </tr>
                <?php
            } else {
                $count = 1;
                foreach ($resarr as $data) {
                    ?>                        
                    <tr> 
                        <td style="width: 7%; text-align: center; border: 0.5px solid #000000; padding: 6px 3px; font-size: 9px; vertical-align: top;"><?php echo $count++; ?></td>
                        <td style="width: 12%; text-align: center; border: 0.5px solid #000000; padding: 6px 3px; font-size: 9px; vertical-align: top;"><?php echo $data['id']; ?></td>
                        <td style="width: 27%; text-align: left; border: 0.5px solid #000000; padding: 6px 5px; font-size: 9px; vertical-align: top;">&nbsp;&nbsp;<?php echo wordwrap($data['name'], 25, "<br/>"); ?></td>
                        <td style="width: 25%; text-align: left; border: 0.5px solid #000000; padding: 6px 5px; font-size: 9px; vertical-align: top;">&nbsp;&nbsp;<?php echo wordwrap($data['code'] . ' - ' . $data['sname'], 25, '<br>'); ?></td>
                        <td style="width: 29%; text-align: left; border: 0.5px solid #000000; padding: 6px 5px; font-size: 9px; vertical-align: top;">&nbsp;&nbsp;<?php echo $data['item']; ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>
</page>
