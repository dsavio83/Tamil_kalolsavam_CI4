<style type="text/css">
<!--
.style1 {
    font-size: 15px;
    font-weight: bold;
    color: #660033;
}
.cell-header {
    background-color: #f8f9fa;
    font-size: 10.5px;
    font-weight: bold;
    color: #000000;
    border: 1px solid #000000;
    padding: 5px 3px;
}
.cell-data {
    font-size: 10px;
    color: #000000;
    border: 1px solid #000000;
    padding: 4px 4px;
}
-->
</style>

<page backtop="42mm" backbottom="22mm" backleft="15mm" backright="15mm">
    <page_header>
        <?php $this->load->view('report/report_header'); ?>
        <table width="100%" style="width: 100%; margin-top: 8px;" border="0" cellspacing="0" cellpadding="0" align="center">
            <tr>
                <td align="center" class="style1" height="24">School Contacts &amp; Teams</td>
            </tr>
        </table>
    </page_header>

    <page_footer>
        <?php $this->load->view('report/report_footer'); ?>
    </page_footer> 

    <table width="100%" style="width: 100%; border-collapse: collapse; margin-top: 8px;" border="0" cellspacing="0" cellpadding="0" align="center">
        <thead>
            <tr>
                <td width="7%" style="width: 7%; text-align: center;" class="cell-header">Sl.No</td>
                <td width="28%" style="width: 28%; text-align: left;" class="cell-header">&nbsp;School</td>
                <td width="21%" style="width: 21%; text-align: left;" class="cell-header">&nbsp;Principal</td>
                <td width="21%" style="width: 21%; text-align: left;" class="cell-header">&nbsp;Headmaster</td>
                <td width="23%" style="width: 23%; text-align: left;" class="cell-header">&nbsp;Team Manager</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 1;
            if (!empty($school_details)) {
                foreach ($school_details as $school) {
                    $team_manager = str_replace('"', '', $school['teachers'] ?? '');
                    $team_manager = str_replace('#$#', '<br>PH : ', $team_manager);
                    $team_manager = str_replace('#@#', '<br>', $team_manager);
            ?>
            <tr>
                <td align="center" class="cell-data"><?php echo $count++; ?></td>
                <td align="left" class="cell-data"><b><?php echo htmlspecialchars($school['school_code']); ?></b><br><?php echo htmlspecialchars($school['school_name']); ?></td>
                <td align="left" class="cell-data">
                    <?php echo ($school['principal_name']) ? htmlspecialchars(str_replace('"', '', $school['principal_name'])) . '<br><small>PH: ' . htmlspecialchars($school['principal_phone']) . '</small>' : '-'; ?>
                </td>
                <td align="left" class="cell-data">
                    <?php echo ($school['hm_name']) ? htmlspecialchars(str_replace('"', '', $school['hm_name'])) . '<br><small>PH: ' . htmlspecialchars($school['hm_phone']) . '</small>' : '-'; ?>
                </td>
                <td align="left" class="cell-data">
                    <?php echo !empty($team_manager) ? $team_manager : '-'; ?>
                </td>
            </tr>
            <?php
                }
            } else {
            ?>
            <tr>
                <td colspan="5" align="center" class="cell-data" style="padding: 20px;">No school contact details found.</td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</page>


