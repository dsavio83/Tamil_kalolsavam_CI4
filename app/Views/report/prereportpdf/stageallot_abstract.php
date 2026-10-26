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
        color: black;
    }
    .style3 {
        font-size: 12px;
        color:black;
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
<table align="center" width="100%">
            <tr><td align="center" class="report-pdf-title"> Stage Report(Abstract)</td></tr>
        </table>
  

    <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0">
        <?php $isDistrict = ((int)session()->get('USER_TYPE') === 2); ?>
        <tr>
            <td width="105" rowspan="2" align="center" class="style2" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">Stage</td>
            <td width="135" rowspan="2" align="center" class="style2" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">Date</td>
            <td width="480" colspan="<?php echo $isDistrict ? 3 : 4; ?>" align="center" class="style2" style="border-bottom:1px #000000; border-right:0px #000000; padding:2px;">Number of Items</td>
        </tr>
        <tr>
            <?php if (!$isDistrict): ?>
            <td width="120" align="center" class="style2" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">LP Tamil<br />Kalaivizha</td>
            <td width="120" align="center" class="style2" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">UP Tamil<br />Kalaivizha</td>
            <td width="120" align="center" class="style2" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">HS Tamil<br />Kalaivizha</td>
            <td width="120" align="center" class="style2" style="border-bottom:1px #000000; border-right:0px #000000; padding:2px;">HSS Tamil<br />Kalaivizha</td>
            <?php else: ?>
            <td width="160" align="center" class="style2" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">UP Tamil<br />Kalaivizha</td>
            <td width="160" align="center" class="style2" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">HS Tamil<br />Kalaivizha</td>
            <td width="160" align="center" class="style2" style="border-bottom:1px #000000; border-right:0px #000000; padding:2px;">HSS Tamil<br />Kalaivizha</td>
            <?php endif; ?>
        </tr>

        <?php
        // Group items by stage and date
        $stages_data = array();
        if (!empty($abstract)) {
            foreach ($abstract as $row) {
                $stage_id = $row['stage_id'];
                $dt = $row['dt'];
                if (!isset($stages_data[$stage_id])) {
                    $stages_data[$stage_id] = array(
                        'stage_name' => $row['stage_name'],
                        'dates'      => array()
                    );
                }
                if (!isset($stages_data[$stage_id]['dates'][$dt])) {
                    $stages_data[$stage_id]['dates'][$dt] = array(
                        1 => 0, // LP Tamil Kalaivizha
                        2 => 0, // UP Tamil Kalaivizha
                        3 => 0, // HS Tamil Kalaivizha
                        4 => 0  // HSS Tamil Kalaivizha
                    );
                }
                $fest_id = (int)$row['fest_id'];
                if (isset($stages_data[$stage_id]['dates'][$dt][$fest_id])) {
                    $stages_data[$stage_id]['dates'][$dt][$fest_id] = (int)$row['itcode'];
                }
            }
        }

        foreach ($stages_data as $stage_id => $sdata) {
            $num_dates = count($sdata['dates']);
            $d_idx = 0;
            foreach ($sdata['dates'] as $dt => $fcounts) {
                $d_idx++;
                $is_first = ($d_idx === 1);
                $is_last  = ($d_idx === $num_dates);
                $stage_border_bottom = $is_last ? '1px #000000' : '0px #000000';
                ?>
                <tr>
                    <td width="105" align="center" class="style3" style="border-bottom:<?php echo $stage_border_bottom; ?>; border-right:1px #000000; padding:2px;">
                        <?php echo $is_first ? $sdata['stage_name'] : ''; ?>
                    </td>
                    <td width="135" align="center" class="style3" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">
                        <?php echo datetophpmodel($dt); ?>
                    </td>
                    <?php if (!$isDistrict): ?>
                    <td width="120" align="center" class="style3" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">
                        <?php echo $fcounts[1]; ?>
                    </td>
                    <td width="120" align="center" class="style3" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">
                        <?php echo $fcounts[2]; ?>
                    </td>
                    <td width="120" align="center" class="style3" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">
                        <?php echo $fcounts[3]; ?>
                    </td>
                    <td width="120" align="center" class="style3" style="border-bottom:1px #000000; border-right:0px #000000; padding:2px;">
                        <?php echo $fcounts[4]; ?>
                    </td>
                    <?php else: ?>
                    <td width="160" align="center" class="style3" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">
                        <?php echo $fcounts[2]; ?>
                    </td>
                    <td width="160" align="center" class="style3" style="border-bottom:1px #000000; border-right:1px #000000; padding:2px;">
                        <?php echo $fcounts[3]; ?>
                    </td>
                    <td width="160" align="center" class="style3" style="border-bottom:1px #000000; border-right:0px #000000; padding:2px;">
                        <?php echo $fcounts[4]; ?>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php
            }
        }
        ?>
    </table>


</page>