<style type="text/css">
<!--
.style1 {
    font-size: 15px;
    font-weight: bold;
    color: #660033;
}
.style2 {
    font-size: 11px;
    font-weight: bold;
    color: #000000;
}
.cell-header {
    background-color: #f8f9fa;
    font-size: 10.5px;
    font-weight: bold;
    color: #000000;
    border: 1px solid #000000;
    padding: 4px 3px;
}
.cell-data {
    font-size: 10.5px;
    color: #000000;
    border: 1px solid #000000;
    padding: 3px 4px;
}
-->
</style>

<?php $this->load->view('report/report_pdf_style'); ?>
<page backtop="20mm" backbottom="22mm" backleft="15mm" backright="15mm">
    <page_header>
        <?php $this->load->view('report/report_header'); ?>
        
    </page_header>

    <page_footer>
        <?php $this->load->view('report/report_footer'); ?>
    </page_footer>
<table width="100%" style="width: 100%; margin-top: 8px;" border="0" cellspacing="0" cellpadding="0" align="center">
            <tr>
                <td align="center" class="report-pdf-title" height="24">Itemwise Participants List</td>
            </tr>
        </table>
 

    <!-- Meta Information: Item & Festival (Positioned cleanly below header with ample spacing) -->
    <table width="100%" style="width: 100%; margin-top: 6px; margin-bottom: 8px;" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td class="style2" align="left" width="60%" style="width: 60%; padding: 2px 0;">
                Item : <?php echo isset($itemdet[0]['item_code']) ? $itemdet[0]['item_code'] : ''; ?> - <?php echo isset($itemdet[0]['item_name']) ? $itemdet[0]['item_name'] : ''; ?>
            </td>
            <td class="style2" align="right" width="40%" style="width: 40%; text-align: right; padding: 2px 0;">
                Festival : <?php echo isset($festdet[0]['fest_name']) ? $festdet[0]['fest_name'] : ''; ?>
            </td>
        </tr>
    </table>

    <!-- Main Participants Table: 100% width with equal margins -->
    <table width="100%" style="width: 100%; border-collapse: collapse;" border="0" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="cell-header" width="7%" style="width: 7%; text-align: center;">Sl.No.</td>
                <td class="cell-header" width="13%" style="width: 13%; text-align: center;">Reg No.</td>
                <td class="cell-header" width="28%" style="width: 28%; text-align: left;">&nbsp;Name</td>
                <td class="cell-header" width="7%" style="width: 7%; text-align: center;">B/G</td>
                <td class="cell-header" width="8%" style="width: 8%; text-align: center;">Class</td>
                <td class="cell-header" width="37%" style="width: 37%; text-align: left;">&nbsp;School</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            if (!empty($partdata)) {
                foreach ($partdata as $data) {
                    $count++;
                    $star = (!empty($data['spo_id']) && $data['spo_id'] > 0) ? '*' : '';
            ?>
            <tr>
                <td class="cell-data" style="text-align: center;"><?php echo $count; ?></td>
                <td class="cell-data" style="text-align: center; font-weight: bold;"><?php echo $data['participant_id'] . $star; ?></td>
                <td class="cell-data" style="text-align: left;">&nbsp;<?php echo htmlspecialchars($data['participant_name']); ?></td>
                <td class="cell-data" style="text-align: center;"><?php echo $data['gender']; ?></td>
                <td class="cell-data" style="text-align: center;"><?php echo $data['class']; ?></td>
                <td class="cell-data" style="text-align: left;">&nbsp;<?php echo htmlspecialchars($data['school_code'] . ' - ' . $data['school_name']); ?></td>
            </tr>
            <?php
                }
            }
            ?>
        </tbody>
    </table>
</page>
