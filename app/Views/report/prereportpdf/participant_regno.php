<style type="text/css">
    <!--
    .style1 {
        font-size: 25px;
        font-weight: bold;
        color: #660033;
    }
    .style56 {
        font-size: 17px;
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
if ($this->session->get('SUB_DISTRICT')) {
    /* $sub_dist_name		=	get_sub_dist_name($this->session->get('SUB_DISTRICT'));
      $label				=	$sub_dist_name; */
    $title = wordwrap(get_sub_dist_name($this->session->get('SUB_DISTRICT')) . ' Sub District', 40, '<br/>');
} else if ($this->session->get('DISTRICT')) {
    $title = wordwrap(get_dist_name($this->session->get('DISTRICT')) . '  District', 40, '<br/>');
}
$fest_master_details = $this->General_Model->get_fest_master_details();
if (count($fest_master_details) > 0) {
    //$title		=	(@$fest_master_details[0]['sub_dist_kalolsavam_name']) ? @$fest_master_details[0]['sub_dist_kalolsavam_name'] : '';
    //$title		=	wordwrap(get_sub_dist_name($this->session->get('SUB_DISTRICT')).' Sub District',40,'<br/>');
    $venue = wordwrap(@$fest_master_details[0]['venue'], 40, '<br/>');
    $logo = @$fest_master_details[0]['logo_name'];
    $file_path = pdf_safe_logo_image('sub_district', $logo, false);
}
?>

<page backtop="5mm" backbottom="0mm ">

    <table align="left" width="288" border="1" >

        <tr valign="top">
            <?php
            if ($file_path != "") {
                ?>

                <td rowspan="2"> <img src="<?php echo $file_path ?>" height="40"></td>
            <?php } else {
                ?>
                <td rowspan="2" height="40">&nbsp;</td>
            <?php } ?>

            <td align="center" class="style56">Kerala School Kalolsavam 2025-26</td>
        </tr>
        <tr>
            <td align="center" class="style2"><?php echo $title . '<br> ' . $venue; ?><br></td>
        </tr>


        <tr bgcolor="#CCCCCC">

            <td bgcolor="#E5E5E5" colspan="2" align="center" class="style2" style="border-bottom:1px #000000;border-top:1px #000000; border-right:0px #000000; padding:2px;">Participant's Card</td>
        </tr>
        <tr>
            <td align="center">Reg. No</td>

            <td rowspan="2" style="border-bottom:1px #000000; border-left:1px #000000; padding:0px;"><span class="style2">&nbsp;<?php echo $partcard[0]['participant_name']; ?></span><br>
                &nbsp;<?php echo $partcard[0]['school_code'] . '  ' . $partcard[0]['school_name']; ?><br>
                &nbsp;<?php echo 'Class  :' . $partcard[0]['class']; ?></td>
        </tr>
        <tr>
            <td  width="50"class="style1" valign="top" style="border-bottom:1px #000000; border-right:0px #000000; padding:2px;" align="center"><?php echo $partcard[0]['participant_id']; ?></td>
        </tr>

        <?php
        $count = 1;
        $cnt = count($partcard);
        for ($j = 0; $j < count($partcard); $j++) {
            $dat_itme = datetophpmodel($partcard[$j]['datee']);
            if ($cnt != ($j + 1)) {
                ?>

                <tr>
                    <td align="left" colspan="2" class="style55">&nbsp;<?php echo $partcard[$j]['item_code'] . '&nbsp;&nbsp;&nbsp;' . $partcard[$j]['item_name']; ?></td>
                </tr>

                <tr>
                    <td align="right" colspan="2" class="style9" style="border-bottom:1px #000000; border-right:0px #000000; padding:2px;"><?php echo 'Cluster ' . $partcard[$j]['cluster_no'] . ' :&nbsp;&nbsp; ' . $partcard[$j]['stage_name'] . '&nbsp; :  on  ' . $dat_itme; ?>&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <?php
            } else {
                ?>
                <tr>
                    <td colspan="2" class="style55" align="left">&nbsp;<?php echo $partcard[$j]['item_code'] . '&nbsp;&nbsp;&nbsp;' . $partcard[$j]['item_name']; ?></td>
                </tr>
                <tr>
                    <td colspan="2" class="style55" align="right"><?php echo 'Cluster ' . $partcard[$j]['cluster_no'] . ' :&nbsp;&nbsp; ' . $partcard[$j]['stage_name'] . '&nbsp; : on ' . $dat_itme; ?>&nbsp;&nbsp;&nbsp;</td>
                </tr>


                <?php
            }
            $count++;
        }
        ?>
    </table>
</page>




