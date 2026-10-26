<style type="text/css">
    <!--
    .report_title {
        font-size: 15px;
        font-weight: bold;
        color: #012970;
        text-align: center;

    }
    .fest_header {
        font-size: 13px;
        font-weight: bold;
        color: #1e293b;
        background-color: #f1f5f9;

    }
    .th_head {
        font-size: 11px;
        font-weight: bold;
        color: #000000;
        background-color: #e2e8f0;

    }
    .tb_cell {
        font-size: 11px;
        color: #000000;

    }
    .item_box {
        font-size: 11px;
        color: #0f172a;

        line-height: 1.4;
    }
    .item_badge {
        font-weight: bold;
        color: #0369a1;
    }
    .item_type_s {
        color: #15803d;
        font-size: 10px;
    }
    .item_type_g {
        color: #b45309;
        font-size: 10px;
    }
    -->
</style>
<?php $this->load->view('report/report_pdf_style'); ?>


<page backtop="20mm" backbottom="15mm">
    <page_header>
        <?php $this->load->view('report/report_header'); ?>
    </page_header>
    <page_footer>
        <?php $this->load->view('report/report_footer'); ?>
    </page_footer>

    <table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">
        <tr>
            <td align="center" class="report-pdf-title">
                List of Participants in Multiple Items with Event Details
            </td>
        </tr>
    </table>
    <br />

    <?php
    $prev_fest = "";
    $s = 0;
    for ($j = 0; $j < count($participants); $j++) {
        $s++;
        if ($prev_fest != $participants[$j]['fest_id']) {
            if ($j != 0) {
                print("</table><br />");
            }
            $prev_fest = $participants[$j]['fest_id'];
            $s = 1;
            ?>
            <table width="100%" align="center" border="1" cellpadding="3" cellspacing="0" style="border-collapse: collapse; border-color: #94a3b8;">
                <tr>
                    <td colspan="5" align="center" class="fest_header" style="padding: 6px; border: 1px solid #64748b;">
                        Festival: <?php echo htmlspecialchars($participants[$j]['fest_name']); ?>
                    </td>
                </tr>
                <tr>
                    <th width="35" align="center" class="th_head" style="padding: 4px; border: 1px solid #64748b;">Sl.No</th>
                    <th width="50" align="center" class="th_head" style="padding: 4px; border: 1px solid #64748b;">Reg No.</th>
                    <th width="170" align="left" class="th_head" style="padding: 4px; border: 1px solid #64748b;">Participant & School</th>
                    <th width="45" align="center" class="th_head" style="padding: 4px; border: 1px solid #64748b;">Total</th>
                    <th width="420" align="left" class="th_head" style="padding: 4px; border: 1px solid #64748b;">Participating Items & Details</th>
                </tr>
            <?php } ?>

            <tr>
                <td class="tb_cell" align="center" valign="top" style="padding: 4px; border: 1px solid #cbd5e1;">
                    <?php echo $s; ?>
                </td>
                <td class="tb_cell" align="center" valign="top" style="padding: 4px; border: 1px solid #cbd5e1; font-weight: bold;">
                    <?php echo htmlspecialchars((string)$participants[$j]['participant_id']); ?>
                </td>
                <td class="tb_cell" align="left" valign="top" style="padding: 4px; border: 1px solid #cbd5e1;">
                    <strong><?php echo htmlspecialchars($participants[$j]['participant_name']); ?></strong><br />
                    <span style="font-size: 10px; color: #475569;">
                        Class: <?php echo htmlspecialchars($participants[$j]['class']); ?> |
                        Gender: <?php echo ($participants[$j]['gender'] == 'B' ? 'Boy' : 'Girl'); ?><br />
                        <?php echo htmlspecialchars($participants[$j]['school_code'] . ' - ' . $participants[$j]['school_name']); ?>
                    </span>
                </td>
                <td class="tb_cell" align="center" valign="top" style="padding: 4px; border: 1px solid #cbd5e1; font-weight: bold; color: #0284c7;">
                    <?php echo htmlspecialchars((string)$participants[$j]['cnt']); ?>
                </td>
                <td class="item_box" align="left" valign="top" style="padding: 4px; border: 1px solid #cbd5e1;">
                    <?php
                    if (!empty($participants[$j]['items'])) {
                        $itemIndex = 1;
                        foreach ($participants[$j]['items'] as $item) {
                            $typeLabel = ($item['item_type'] == 'G' || $item['item_type'] == 'g') ? 'Group' : 'Single';
                            $typeClass = ($typeLabel == 'Group') ? 'item_type_g' : 'item_type_s';
                            $stageInfo = (!empty($item['stage_name'])) ? ' [' . $item['stage_name'] . ']' : '';
                            ?>
                            <div style="margin-bottom: 3px;">
                                <?php echo $itemIndex++; ?>. 
                                <span class="item_badge">[<?php echo htmlspecialchars((string)$item['item_code']); ?>]</span>
                                <strong><?php echo htmlspecialchars($item['item_name']); ?></strong>
                                <span class="<?php echo $typeClass; ?>">(<?php echo $typeLabel; ?>)</span>
                                <span style="color: #64748b; font-size: 10px;"><?php echo htmlspecialchars($stageInfo); ?></span>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<span style="color: #94a3b8;">No items listed</span>';
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>
    </table>

</page>