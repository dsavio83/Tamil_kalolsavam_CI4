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
    font-size: 11px;
    font-weight: bold;
    color: #000000;
    border: 1px solid #000000;
    padding: 5px 4px;
}
.cell-data {
    font-size: 11px;
    color: #000000;
    border: 1px solid #000000;
    padding: 4px 6px;
}
-->
</style>
<?php
if (empty($fest_name)) {
    if (!empty($festtype) && isset($festtype[0]['fest_name'])) {
        $fest_name = $festtype[0]['fest_name'];
    } elseif (!empty($festmaster)) {
        foreach ($festmaster as $fm) {
            if (isset($fest) && $fm['fest_id'] == $fest) {
                $fest_name = $fm['fest_name'];
                break;
            }
        }
    }
}
?>   
<page backtop="44mm" backbottom="22mm" backleft="15mm" backright="15mm">
    <page_header>
        <?php $this->load->view('report/report_header'); ?>
        <table width="100%" style="width: 100%; margin-top: 8px;" border="0" cellspacing="0" cellpadding="0" align="center">
            <tr>
                <td align="center" class="style1" height="24">List of Participating Schools with School Code</td>
            </tr>
        </table>
    </page_header>

    <page_footer>
        <?php $this->load->view('report/report_footer'); ?>
    </page_footer> 

    <!-- Festival Meta Information -->
    <table width="100%" style="width: 100%; margin-top: 6px; margin-bottom: 10px;" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td class="style2" width="60%" style="width: 60%; padding: 2px 0;" align="left">
                Festival : <?php echo !empty($fest_name) ? htmlspecialchars($fest_name) : 'All Categories'; ?>
            </td>
            <td class="style2" width="40%" style="width: 40%; padding: 2px 0; text-align: right;" align="right">
                Total Participating Schools : <?php echo !empty($festtype) ? count($festtype) : 0; ?>
            </td>
        </tr>
    </table>

    <!-- Main Table: 100% width with equal margins -->
    <table width="100%" style="width: 100%; border-collapse: collapse;" border="0" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <td class="cell-header" width="10%" style="width: 10%; text-align: center;">Sl.No</td>
                <td class="cell-header" width="20%" style="width: 20%; text-align: center;">School Code</td>
                <td class="cell-header" width="70%" style="width: 70%; text-align: left;">&nbsp;School Name</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            if (!empty($festtype)) {
                foreach ($festtype as $row) {
                    $count++;
            ?>
            <tr>
                <td class="cell-data" style="text-align: center;"><?php echo $count; ?></td>
                <td class="cell-data" style="text-align: center; font-weight: bold;"><?php echo htmlspecialchars($row['school_code']); ?></td>
                <td class="cell-data" style="text-align: left;">&nbsp;<?php echo htmlspecialchars($row['school_name']); ?></td>
            </tr>
            <?php
                }
            } else {
            ?>
            <tr>
                <td colspan="3" class="cell-data" style="text-align: center; padding: 20px; color: #666;">
                    No participating schools registered for this festival category yet.
                </td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</page>


