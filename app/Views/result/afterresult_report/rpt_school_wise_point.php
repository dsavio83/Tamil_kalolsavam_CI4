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
        color: #1e293b;
    }
    .fest-header {
        font-family: 'TAU-Pallai', 'Segoe UI', Tahoma, sans-serif;
        font-size: 14px;
        font-weight: bold;
        background-color: #f1f5f9;
        color: #0d6efd;
    }
    .col-header {
        font-size: 10px;
        font-weight: bold;
        background-color: #f8fafc;
        color: #334155;
    }
    .data-cell {
        font-size: 10px;
        color: #0f172a;
    }
</style>
<?php $this->load->view('report/report_pdf_style'); ?>

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
    <?php
    $isItem = !empty($is_item);
    if (!isset($is_item)) {
        if (!empty($code_info)) {
            $isItem = !empty($code_info['is_item']);
        } elseif (!empty($retvalue) && count($retvalue) > 1) {
            $firstItem = $retvalue[0]['item_code'];
            $allSameItem = true;
            foreach ($retvalue as $r) {
                if ($r['item_code'] != $firstItem) { $allSameItem = false; break; }
            }
            $isItem = $allSameItem;
        }
    }

    $dispCode = '';
    $dispName = '';
    if (!empty($code_info)) {
        $dispCode = $code_info['code'] ?? '';
        $dispName = $code_info['name'] ?? '';
    } elseif (!empty($retvalue) && isset($retvalue[0])) {
        if ($isItem) {
            $dispCode = $retvalue[0]['item_code'] ?? '';
            $dispName = $retvalue[0]['item_name'] ?? '';
        } else {
            $dispCode = $retvalue[0]['school_code'] ?? '';
            $dispName = $retvalue[0]['school_name'] ?? '';
        }
    } elseif (!empty($school_info)) {
        $dispCode = $school_info['code'] ?? $school_info['school_code'] ?? '';
        $dispName = $school_info['name'] ?? $school_info['school_name'] ?? '';
    } elseif (!empty($code)) {
        $dispCode = $code;
    } elseif (!empty($school_code)) {
        $dispCode = $school_code;
    }
    ?>
    <table width="100%" border="0" align="center" style="margin-bottom: 8px;">
        <tr>
            <td align="center" class="report-pdf-title">Item Wise Point Report</td>
        </tr>
        <?php if (!empty($dispCode)): ?>
        <tr>
            <td class="style2" align="center"><b><?php echo htmlspecialchars((string)$dispCode); ?><?php echo !empty($dispName) ? '  -  ' . htmlspecialchars((string)$dispName) : ''; ?></b></td>
        </tr>
        <?php endif; ?>
    </table>
    <table width="95%" border="1" align="center" cellpadding="3" cellspacing="0" style="border-collapse: collapse; border: 0.5px solid #000000;">
        <?php
        if (!empty($retvalue)) {
            $count = 1;
            $previous = '';
            foreach ($retvalue AS $data) {
                if ($previous != $data['fest_name']) {
                    $count = 1;
                    $previous = $data['fest_name'];
                    ?>
                    <tr>
                        <td colspan="5" align="center" class="fest-header" style="height:24px; border: 0.5px solid #000000;"><?php echo htmlspecialchars((string)$data['fest_name']); ?></td>
                    </tr>
                    <tr class="col-header">
                        <td width="40" align="center" style="height:20px; border: 0.5px solid #000000;">Sl No</td>
                        <td width="240" align="left" style="height:20px; border: 0.5px solid #000000;">&nbsp;<?php echo $isItem ? 'School Name' : 'Competition Item'; ?></td>
                        <td width="260" align="left" style="height:20px; border: 0.5px solid #000000;">&nbsp;Participant Name</td>
                        <td width="35" align="center" style="height:20px; border: 0.5px solid #000000;">Grade</td>
                        <td width="35" align="center" style="height:20px; border: 0.5px solid #000000;">Point</td>
                    </tr>
                    <?php
                }
                ?>
                <tr class="data-cell">
                    <td align="center" style="height:20px; border: 0.5px solid #000000;"><?php echo $count++; ?></td>
                    <td align="left" style="height:20px; border: 0.5px solid #000000;">&nbsp;<?php echo htmlspecialchars($isItem ? ((string)$data['school_code'] . ' - ' . $data['school_name']) : ((string)$data['item_code'] . ' - ' . $data['item_name'])); ?></td>
                    <td align="left" style="height:20px; border: 0.5px solid #000000;">&nbsp;<?php echo htmlspecialchars((string)$data['participant_name']); ?></td>
                    <td align="center" style="height:20px; font-weight: bold; border: 0.5px solid #000000;"><?php echo htmlspecialchars((string)$data['grade']); ?></td>
                    <td align="center" style="height:20px; font-weight: bold; border: 0.5px solid #000000;"><?php echo htmlspecialchars((string)$data['point']); ?></td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr class="col-header">
                <td width="40" align="center" style="height:20px; border: 0.5px solid #000000;">Sl No</td>
                <td width="240" align="left" style="height:20px; border: 0.5px solid #000000;">&nbsp;<?php echo $isItem ? 'School Name' : 'Competition Item'; ?></td>
                <td width="260" align="left" style="height:20px; border: 0.5px solid #000000;">&nbsp;Participant Name</td>
                <td width="35" align="center" style="height:20px; border: 0.5px solid #000000;">Grade</td>
                <td width="35" align="center" style="height:20px; border: 0.5px solid #000000;">Point</td>
            </tr>
            <tr>
                <td colspan="5" align="center" style="height:40px; font-size: 12px; color: #64748b; border: 0.5px solid #000000;">
                    No declared results found for this code.
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
</page>
