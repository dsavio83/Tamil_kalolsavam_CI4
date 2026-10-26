<style type="text/css">
    <!--
    .style1 {
        font-size: 25px;
        font-weight: bold;
        color: #660033;
    }
    .style56 {
        font-size: 15px;
        font-weight: bold;
        color: #660033;
    }
    .style2 {
        font-size: 15px;
        font-weight: bold;
        color: black;
    }
    .style9{
        font-size: 12px;
        color: black;
        border-bottom:1px #000000; border-right:0px #000000; padding:1px;
    }
    .style55 {

        font-size: 12px;
        color: black;
    }
    .style23{
        font-size: 12px;
        color: black;
    }
    .style3 {
        font-size: 18px;
        font-weight: bold;
        color:black;
    }
    .style4 {
        font-size: 12px;
        font-weight: bold;
        color:black;
    }
    -->
</style>

<?php
$title = '';
if ($this->session->get('SUB_DISTRICT')) {
    $title = wordwrap(get_sub_dist_name($this->session->get('SUB_DISTRICT')) . ' Sub District', 40, '<br/>');
    $dir = 'sub_district';
} else if ($this->session->get('DISTRICT')) {
    $title = wordwrap(get_dist_name($this->session->get('DISTRICT')) . ' District', 40, '<br/>');
    $dir = 'district';
} else {
    $dir = 'sub_district';
}

$venue = '';
$file_path = '';
$fest_master_details = $this->General_Model->get_fest_master_details();
if (!empty($fest_master_details) && count($fest_master_details) > 0) {
    $venue = wordwrap(@$fest_master_details[0]['venue'], 40, '<br/>');
    $logo = @$fest_master_details[0]['logo_name'];
    $file_path = pdf_safe_logo_image($dir, $logo, true);
}
?>

<page backtop="7mm" backbottom="7mm" backleft="7.2mm" backright="12.8mm">
    <table align="center" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <?php if (!empty($partcard) && is_array($partcard) && count($partcard) > 0 && !empty($partcard[0]['participant_id'])) { ?>
                <td valign="top">
                    <?php echo render_single_photo_card($partcard[0], $Photo, $file_path, $title, $venue, $this, $partcard); ?>
                </td>
            <?php } else { ?>
                <td width="290" valign="top">&nbsp;</td>
            <?php } ?>
            <?php if (!empty($partcard1) && is_array($partcard1) && count($partcard1) > 0 && !empty($partcard1[0]['participant_id'])) { ?>
                <td width="40" align="center" valign="middle">&nbsp;</td>
                <td valign="top">
                    <?php echo render_single_photo_card($partcard1[0], $Photo, $file_path, $title, $venue, $this, $partcard1); ?>
                </td>
            <?php } ?>
            <?php if (!empty($partcard2) && is_array($partcard2) && count($partcard2) > 0 && !empty($partcard2[0]['participant_id'])) { ?>
                <td width="40" align="center" valign="middle">&nbsp;</td>
                <td valign="top">
                    <?php echo render_single_photo_card($partcard2[0], $Photo, $file_path, $title, $venue, $this, $partcard2); ?>
                </td>
            <?php } ?>
        </tr>
    </table>
</page>





