<style type="text/css">
    .style1 {
        font-family: 'TAU-Pallai', 'Segoe UI', Tahoma, sans-serif;
        font-size: 15px;
        font-weight: bold;
        color: #0d6efd;
    }
    .style2 {
        font-family: 'TAU-Pallai', 'Segoe UI', Tahoma, sans-serif;
        font-size: 13px;
        font-weight: bold;
        color: #1e293b;
    }
    .status-text {
        font-size: 10px;
        font-weight: bold;
        color: #475569;
    }
    .col-header {
        font-size: 10px;
        font-weight: bold;
        background-color: #f1f5f9;
        color: #1e293b;
    }
    .data-cell {
        font-size: 10px;
        color: #0f172a;
    }
</style>
<?php $this->load->view('report/report_pdf_style'); ?>

<?php
$retvalue = $retvalue ?? [];
$completed = $completed ?? [];
$totalitems = $totalitems ?? [];
$grade = $grade ?? [];
$itemcomp = $itemcomp ?? [];

// Map completed items count by fest_id
$compMap = [];
foreach ($completed as $c_item) {
    if (!empty($c_item['fest_id'])) {
        $compMap[$c_item['fest_id']] = (int)$c_item['cn'];
    }
}

// Map total items count by fest_id
$totMap = [];
foreach ($totalitems as $t_item) {
    if (!empty($t_item['fest_id'])) {
        $totMap[$t_item['fest_id']] = (int)$t_item['c'];
    }
}

// Group data by fest_id
$festGroups = [];
foreach ($retvalue as $data) {
    $fId = $data['fest_id'] ?? 0;
    if (!isset($festGroups[$fId])) {
        $festGroups[$fId] = [
            'fest_id' => $fId,
            'fest_name' => $data['fest_name'] ?? '',
            'schools' => []
        ];
    }
    $festGroups[$fId]['schools'][] = $data;
}

if (!empty($festGroups)) {
    foreach ($festGroups as $festId => $festData) {
        $festName = $festData['fest_name'];
        $schools = $festData['schools'];
        $cn = $compMap[$festId] ?? 0;
        $tot = $totMap[$festId] ?? 0;
        ?>
        <page backtop="20mm" backbottom="20mm">
            <page_header>
                <?php $this->load->view('report/report_header'); ?>
            </page_header>
            <page_footer>
                <?php $this->load->view('report/report_footer'); ?>
            </page_footer>

            <table width="100%" border="0" align="center" style="margin-bottom: 8px;">
                <tr>
                    <td align="center" class="report-pdf-title">Grade-Point Report of all schools</td>
                </tr>
                <tr>
                    <td align="center" class="style2" style="padding-top: 2px;">
                        <b><?php echo htmlspecialchars((string)$festName); ?></b>
                    </td>
                </tr>
                <?php if ($tot > 0 || $cn > 0): ?>
                <tr>
                    <td align="center" class="status-text" style="padding-top: 2px;">
                        Result Declared <?php echo $cn; ?><?php echo ($tot > 0) ? ' / ' . $tot : ''; ?> Items
                    </td>
                </tr>
                <?php endif; ?>
            </table>

            <table width="95%" border="1" align="center" cellpadding="3" cellspacing="0" style="border-collapse: collapse; border: 0.5px solid #000000;">
                <thead>
                    <tr class="col-header">
                        <td width="40" rowspan="2" align="center" style="height:20px; font-weight: bold; background-color: #f1f5f9; border: 0.5px solid #000000;">Sl No</td>
                        <td width="450" rowspan="2" align="left" style="height:20px; font-weight: bold; background-color: #f1f5f9; border: 0.5px solid #000000;">&nbsp;School</td>
                        <td colspan="3" width="105" align="center" style="height:14px; font-weight: bold; background-color: #f1f5f9; border: 0.5px solid #000000;">Grade</td>
                        <td width="55" rowspan="2" align="center" style="height:20px; font-weight: bold; background-color: #f1f5f9; border: 0.5px solid #000000;">Point</td>
                    </tr>
                    <tr class="col-header">
                        <td width="35" align="center" style="height:14px; font-weight: bold; background-color: #f1f5f9; border: 0.5px solid #000000;">A</td>
                        <td width="35" align="center" style="height:14px; font-weight: bold; background-color: #f1f5f9; border: 0.5px solid #000000;">B</td>
                        <td width="35" align="center" style="height:14px; font-weight: bold; background-color: #f1f5f9; border: 0.5px solid #000000;">C</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 1;
                    foreach ($schools as $data) {
                        $agrade = 0;
                        $bgrade = 0;
                        $cgrade = 0;
                        foreach ($grade as $g) {
                            if (($g['school_code'] == $data['school_code']) && ($g['fest_id'] == $festId)) {
                                if ($g['grade'] == 'A') $agrade = $g['grd'];
                                elseif ($g['grade'] == 'B') $bgrade = $g['grd'];
                                elseif ($g['grade'] == 'C') $cgrade = $g['grd'];
                            }
                        }
                        ?>
                        <tr class="data-cell">
                            <td width="40" align="center" style="height:20px; border: 0.5px solid #000000;"><?php echo $count++; ?></td>
                            <td width="450" align="left" style="height:20px; border: 0.5px solid #000000;">
                                &nbsp;<?php echo htmlspecialchars((string)($data['school_code'] . ' - ' . $data['school_name'])); ?>
                                <?php if (!empty($data['sub_district_name'])): ?>
                                    <span style="font-size: 8px; color: #475569;">(<?php echo htmlspecialchars((string)$data['sub_district_name']); ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td width="35" align="center" style="height:20px; border: 0.5px solid #000000;"><?php echo (int)$agrade; ?></td>
                            <td width="35" align="center" style="height:20px; border: 0.5px solid #000000;"><?php echo (int)$bgrade; ?></td>
                            <td width="35" align="center" style="height:20px; border: 0.5px solid #000000;"><?php echo (int)$cgrade; ?></td>
                            <td width="55" align="center" style="height:20px; font-weight: bold; border: 0.5px solid #000000;"><?php echo (int)($data['cnt'] ?? $data['point'] ?? 0); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </page>
        <?php
    }
} else {
    // Empty state page
    ?>
    <page backtop="20mm" backbottom="20mm">
        <page_header>
            <?php $this->load->view('report/report_header'); ?>
        </page_header>
        <page_footer>
            <?php $this->load->view('report/report_footer'); ?>
        </page_footer>

        <table width="100%" border="0" align="center" style="margin-bottom: 8px;">
            <tr>
                <td align="center" class="style1">Grade-Point Report of all schools</td>
            </tr>
        </table>

        <table width="95%" border="1" align="center" cellpadding="3" cellspacing="0" style="border-collapse: collapse; border: 0.5px solid #000000;">
            <thead>
                <tr class="col-header">
                    <td width="40" rowspan="2" align="center" style="height:20px; font-weight: bold; background-color: #f1f5f9; border: 0.5px solid #000000;">Sl No</td>
                    <td width="450" rowspan="2" align="left" style="height:20px; font-weight: bold; background-color: #f1f5f9; border: 0.5px solid #000000;">&nbsp;School</td>
                    <td colspan="3" width="105" align="center" style="height:14px; font-weight: bold; background-color: #f1f5f9; border: 0.5px solid #000000;">Grade</td>
                    <td width="55" rowspan="2" align="center" style="height:20px; font-weight: bold; background-color: #f1f5f9; border: 0.5px solid #000000;">Point</td>
                </tr>
                <tr class="col-header">
                    <td width="35" align="center" style="height:14px; font-weight: bold; background-color: #f1f5f9; border: 0.5px solid #000000;">A</td>
                    <td width="35" align="center" style="height:14px; font-weight: bold; background-color: #f1f5f9; border: 0.5px solid #000000;">B</td>
                    <td width="35" align="center" style="height:14px; font-weight: bold; background-color: #f1f5f9; border: 0.5px solid #000000;">C</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" align="center" style="height:40px; font-size: 12px; color: #64748b; border: 0.5px solid #000000;">
                        No declared results found.
                    </td>
                </tr>
            </tbody>
        </table>
    </page>
    <?php
}
?>
