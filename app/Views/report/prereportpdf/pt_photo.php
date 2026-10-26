<style type="text/css">
    <!--
    .style1 {
        font-size: 20px;
        font-weight: bold;
        color: #660033;
    }
    .style56 {
        font-size: 14px;
        font-weight: bold;
        color: #660033;
        line-height: 1.15;
    }
    .style2 {
        font-size: 12px;
        font-weight: bold;
        color: #000000;
    }
    .stylehy {
        font-size: 10px;
        font-weight: bold;
        color: #000000;
        line-height: 1.15;
    }
    .style9 {
        font-size: 9px;
        color: #000000;
        border-bottom: 1px solid #000000;
        padding: 2px 4px;
    }
    .style55 {
        font-size: 9px;
        color: #000000;
    }
    .style23 {
        font-size: 9px;
        color: #000000;
        padding: 2px 4px;
    }
    .style3 {
        font-size: 16px;
        font-weight: bold;
        color: #000000;
    }
    .style4 {
        font-size: 11px;
        font-weight: bold;
        color: #000000;
    }
    -->
</style>
<?php
$title = '';
$dir = 'sub_district';
if ($this->session->get('SUB_DISTRICT')) {
    $sub_dist_name = get_sub_dist_name($this->session->get('SUB_DISTRICT'));
    $title = wordwrap($sub_dist_name . ' Sub District', 40, '<br/>');
    $dir = 'sub_district';
} elseif ($this->session->get('DISTRICT')) {
    $title = wordwrap(get_dist_name($this->session->get('DISTRICT')) . ' District', 40, '<br/>');
    $dir = 'district';
}

$fest_master_details = $this->General_Model->get_fest_master_details();
$venue = '';
$file_path = '';
if (count($fest_master_details) > 0) {
    if (empty($title)) {
        $title = (@$fest_master_details[0]['sub_dist_kalolsavam_name']) ? @$fest_master_details[0]['sub_dist_kalolsavam_name'] : '';
    }
    $venue = wordwrap(@$fest_master_details[0]['venue'], 40, '<br/>');
    $logo = @$fest_master_details[0]['logo_name'];
    $file_path = pdf_safe_logo_image($dir, $logo, true);
}

$chunks = array_chunk($participant_details, 6);
foreach ($chunks as $page_items) {
    $total_in_page = count($page_items);
    ?>
    <page backtop="7mm" backbottom="7mm" backleft="10mm" backright="20mm">
        <table align="center" border="0" cellspacing="0" cellpadding="0">
            <?php
            for ($i = 0; $i < $total_in_page;) {
                ?>
                <tr>
                    <!-- Card 1 -->
                    <?php if (!empty($page_items[$i]['participant_id'])) { ?>
                        <td valign="top">
                            <?php echo render_single_photo_card($page_items[$i], $Photo, $file_path, $title, $venue, $this); ?>
                        </td>
                    <?php } else { ?>
                        <td width="290" valign="top">&nbsp;</td>
                    <?php } ?>

                    <!-- Equal middle cutting gap -->
                    <td width="25" align="center" valign="middle">&nbsp;</td>
                    <?php $i++; ?>

                    <!-- Card 2 -->
                    <?php if ($i < $total_in_page && !empty($page_items[$i]['participant_id'])) { ?>
                        <td valign="top">
                            <?php echo render_single_photo_card($page_items[$i], $Photo, $file_path, $title, $venue, $this); ?>
                        </td>
                    <?php } else { ?>
                        <td width="290" valign="top">&nbsp;</td>
                    <?php } ?>
                    <?php $i++; ?>

                    <!-- Equal middle cutting gap -->
                    <td width="25" align="center" valign="middle">&nbsp;</td>

                    <!-- Card 3 -->
                    <?php if ($i < $total_in_page && !empty($page_items[$i]['participant_id'])) { ?>
                        <td valign="top">
                            <?php echo render_single_photo_card($page_items[$i], $Photo, $file_path, $title, $venue, $this); ?>
                        </td>
                    <?php } else { ?>
                        <td width="290" valign="top">&nbsp;</td>
                    <?php } ?>
                    <?php $i++; ?>
                </tr>
                <?php if ($i < $total_in_page) { ?>
                    <!-- Vertical cutting gap -->
                    <tr>
                        <td colspan="5" height="30">&nbsp;</td>
                    </tr>
                <?php } ?>
                <?php
            }
            ?>
        </table>
    </page>
    <?php
}
?>
