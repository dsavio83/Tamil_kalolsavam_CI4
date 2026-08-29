<?php
if ($this->session->get('SUB_DISTRICT')) {
    $sub_dist_name = get_sub_dist_name($this->session->get('SUB_DISTRICT'));
    $label = $sub_dist_name . ' Subdistrict';
}
$fest_master_details = $this->General_Model->get_fest_master_details();
if (count($fest_master_details) > 0) {
    $title = (@$fest_master_details[0]['sub_dist_kalolsavam_name']) ? @$fest_master_details[0]['sub_dist_kalolsavam_name'] : '';
    $venue = @$fest_master_details[0]['venue'];
    $start_date = datetophpmodel(@$fest_master_details[0]['start_date']);
    $end_date = datetophpmodel(@$fest_master_details[0]['end_date']);

    $logo = @$fest_master_details[0]['logo_name'];
    $file_path = '';
    if (file_exists(base_path() . 'uploads/sub_district/thumb_' . $logo)) {
        $file_path = base_url(false) . 'uploads/sub_district/thumb_' . $logo;
    }
}
?>

<table style="width: 100%;">
    <tr>
        <td style="text-align: left;">
            <?php if (@$file_path) { ?>
                <img src="<?php echo $file_path ?>" height="50">
            <?php } ?>
        </td>
        <td style="text-align: right;" align="right" valign="top">
            <strong><?php echo $title; ?></strong><br /><?php echo $venue; ?><br /><?php echo $start_date . ' - ' . $end_date; ?>
        </td>
    </tr>
    <tr>
        <td style="width: 100%" colspan="2"><hr/></td>
    </tr>
</table>