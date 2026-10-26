<style type="text/css">
    <!--
    .style1 {
        font-size: 13px;
        font-weight: bold;
        color: #000000;
    }
    .tb {
        font-size: 9px;
        font-weight: bold;
        color: #000000;
    }
    .ety {
        font-size: 9px;
        color: #000000;
    }
    -->
</style>
<?php $this->load->view('report/report_pdf_style'); ?>

<?php
if (!function_exists('fmt_datewise_count')) {
    function fmt_datewise_count($val) {
        return (empty($val) || $val == 0) ? '-' : $val;
    }
}

// Modern O(1) Lookup Maps for Category Data
$lp_map = [];
if (!empty($lp) && is_array($lp)) {
    foreach ($lp as $r) {
        $lp_map[$r['school_code']][$r['gender']] = (int)($r['cntlp'] ?? 0);
    }
}

$up_map = [];
if (!empty($up) && is_array($up)) {
    foreach ($up as $r) {
        $up_map[$r['school_code']][$r['gender']] = (int)($r['upid'] ?? 0);
    }
}

$hs_map = [];
if (!empty($hs) && is_array($hs)) {
    foreach ($hs as $r) {
        $hs_map[$r['school_code']][$r['gender']] = (int)($r['hsid'] ?? 0);
    }
}

$hss_map = [];
if (!empty($hss) && is_array($hss)) {
    foreach ($hss as $r) {
        $hss_map[$r['school_code']][$r['gender']] = (int)($r['hssid'] ?? 0);
    }
}
?>
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

    <table align="center" style="width: 100%;">
        <tr>
            <td align="center" class="report-pdf-title" style="padding-bottom: 3px;">
                Total participants list<?php
                if (!empty($date)) {
                    echo ' on ' . datetophpmodel($date);
                }
                ?>
            </td>
        </tr>
    </table>

    <table border="0" align="center" cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td class="tb" width="28" style="border: 1px solid #000000; padding: 2px;" align="center" rowspan="2">Sl.No.</td>
            <td class="tb" width="304" style="border: 1px solid #000000; padding: 2px 6px;" align="left" rowspan="2">School</td>
            <td class="tb" width="44" style="border: 1px solid #000000; padding: 2px;" colspan="2" align="center">UP</td>
            <td class="tb" width="44" style="border: 1px solid #000000; padding: 2px;" colspan="2" align="center">HS</td>
            <td class="tb" width="48" style="border: 1px solid #000000; padding: 2px;" colspan="2" align="center">HSS</td>
            <td class="tb" width="52" style="border: 1px solid #000000; padding: 2px;" colspan="2" align="center">Total</td>
            <td class="tb" width="40" style="border: 1px solid #000000; padding: 2px 0px; font-size: 8px; line-height: 1.1;" rowspan="2" align="center">Grand<br/>Total</td>
        </tr>
        <tr>
            <td class="tb" width="22" style="border: 1px solid #000000; padding: 2px;" align="center">B</td>
            <td class="tb" width="22" style="border: 1px solid #000000; padding: 2px;" align="center">G</td>
            <td class="tb" width="22" style="border: 1px solid #000000; padding: 2px;" align="center">B</td>
            <td class="tb" width="22" style="border: 1px solid #000000; padding: 2px;" align="center">G</td>
            <td class="tb" width="24" style="border: 1px solid #000000; padding: 2px;" align="center">B</td>
            <td class="tb" width="24" style="border: 1px solid #000000; padding: 2px;" align="center">G</td>
            <td class="tb" width="26" style="border: 1px solid #000000; padding: 2px;" align="center">B</td>
            <td class="tb" width="26" style="border: 1px solid #000000; padding: 2px;" align="center">G</td>
        </tr>
        <?php
        $count = 0;
        $totals = [
            'up_b'        => 0,
            'up_g'        => 0,
            'hs_b'        => 0,
            'hs_g'        => 0,
            'hss_b'       => 0,
            'hss_g'       => 0,
            'total_b'     => 0,
            'total_g'     => 0,
            'grand_total' => 0,
        ];

        if (!empty($school) && is_array($school)) {
            foreach ($school as $sch) {
                $count++;
                $code = $sch['school_code'];
                $name = $sch['school_name'];
                $school_full_name = $code . ' - ' . $name;

                $no_up_boys  = $up_map[$code]['B'] ?? 0;
                $no_up_girls = $up_map[$code]['G'] ?? 0;

                $no_hs_boys  = $hs_map[$code]['B'] ?? 0;
                $no_hs_girls = $hs_map[$code]['G'] ?? 0;

                $no_hss_boys  = $hss_map[$code]['B'] ?? 0;
                $no_hss_girls = $hss_map[$code]['G'] ?? 0;

                $school_sum_boys  = $no_up_boys + $no_hs_boys + $no_hss_boys;
                $school_sum_girls = $no_up_girls + $no_hs_girls + $no_hss_girls;
                $total_school_sum = $school_sum_boys + $school_sum_girls;

                $totals['up_b']        += $no_up_boys;
                $totals['up_g']        += $no_up_girls;
                $totals['hs_b']        += $no_hs_boys;
                $totals['hs_g']        += $no_hs_girls;
                $totals['hss_b']       += $no_hss_boys;
                $totals['hss_g']       += $no_hss_girls;
                $totals['total_b']     += $school_sum_boys;
                $totals['total_g']     += $school_sum_girls;
                $totals['grand_total'] += $total_school_sum;
                ?>
                <tr>
                    <td class="ety" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo $count; ?></td>
                    <td class="ety" style="border: 1px solid #000000; padding: 2px 6px;" align="left"><?php echo wordwrap($school_full_name, 50, '<br/>'); ?></td>
                    <td class="ety" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($no_up_boys); ?></td>
                    <td class="ety" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($no_up_girls); ?></td>
                    <td class="ety" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($no_hs_boys); ?></td>
                    <td class="ety" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($no_hs_girls); ?></td>
                    <td class="ety" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($no_hss_boys); ?></td>
                    <td class="ety" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($no_hss_girls); ?></td>
                    <td class="ety" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($school_sum_boys); ?></td>
                    <td class="ety" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($school_sum_girls); ?></td>
                    <td class="ety" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($total_school_sum); ?></td>
                </tr>
                <?php
            }
        }
        ?>
        <tr>
            <td class="tb" style="border: 1px solid #000000; padding: 2px 8px;" align="right" colspan="2">Grand Total</td>
            <td class="tb" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($totals['up_b']); ?></td>
            <td class="tb" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($totals['up_g']); ?></td>
            <td class="tb" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($totals['hs_b']); ?></td>
            <td class="tb" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($totals['hs_g']); ?></td>
            <td class="tb" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($totals['hss_b']); ?></td>
            <td class="tb" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($totals['hss_g']); ?></td>
            <td class="tb" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($totals['total_b']); ?></td>
            <td class="tb" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($totals['total_g']); ?></td>
            <td class="tb" style="border: 1px solid #000000; padding: 2px;" align="center"><?php echo fmt_datewise_count($totals['grand_total']); ?></td>
        </tr>
    </table>
</page>
