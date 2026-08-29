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
    .stylehy{
        font-size: 11px;
        font-weight: bold;
        color: black;
    }
    .style9{
        font-size: 11px;
        color: black;
        border-bottom:1px #000000; border-right:0px #000000; padding:1px;
    }
    .style55 {

        font-size: 11px;
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
if ($this->session->userdata('SUB_DISTRICT')) {
    $sub_dist_name = get_sub_dist_name($this->session->userdata('SUB_DISTRICT'));
    $label = $sub_dist_name;
}
$fest_master_details = $this->General_Model->get_fest_master_details();
if (count($fest_master_details) > 0) {
    $title = (@$fest_master_details[0]['sub_dist_kalolsavam_name']) ? @$fest_master_details[0]['sub_dist_kalolsavam_name'] : '';
    $title = wordwrap(get_sub_dist_name($this->session->userdata('SUB_DISTRICT')) . ' Sub District', 40, '<br/>');
    $venue = wordwrap(@$fest_master_details[0]['venue'], 40, '<br/>');
    $logo = @$fest_master_details[0]['logo_name'];
    $file_path = '';
    if (file_exists($this->config->item('base_path') . 'uploads/sub_district/' . $logo) and trim($logo) != '') {
        $file_path = base_url(false) . 'uploads/sub_district/thumb_' . $logo;
    } else {

        $file_path = "";
    }
}
?>


<page backtop="2mm" backbottom="0mm">

    <table align="left" border="0">
        <?php
//echo "<br /><br /><br />".var_dump($participant_details);
        for ($i = 0; $i < count($participant_details);) {
            ?>
            <tr>
                <td valign="top">
                    <?php if (@$participant_details[$i]['participant_id']) { ?>
                        <table border="1" width="275" >
                            <tr>
                                <?php
                                if (@$file_path != "") {
                                    ?>
                                    <td rowspan="2"> <img src="<?php echo @$file_path ?>" height="40"  /></td>
                                <?php } else {
                                    ?>
                                    <td rowspan="2">&nbsp; </td>
                                <?php } ?>
                                <td  class="style56" >Kerala School Kalolsavam 2025-26</td>
                            </tr>
                            <tr>

                                <td align="center" class="stylehy"><?php echo @$title . '<br> ' . @$venue; ?><br></td>
                            </tr>

                            <tr bgcolor="#CCCCCC">
                                <td bgcolor="#E5E5E5" colspan="2" align="center" class="style2" style="border-bottom:1px #666666;border-top:1px #666666; border-right:0px #666666; padding:2px;">Participant's Card</td>
                            </tr>
                            <tr>
                                <td align="center">Reg. No</td>
                                <td rowspan="2" style="border-bottom:1px #666666; border-left:1px #666666; padding:1px;" >

                                    <table>
                                        <tr>
                                            <td>
                                                <span class="style2">

                                                    <?php echo wordwrap($participant_details[$i]['participant_name'], 30, '<br/>'); ?></span><br />

                                                <?php echo wordwrap($participant_details[$i]['school_code'] . '  ' . @$participant_details[$i]['school_name'], 30, '<br/>'); ?>                 <br />

                                                <?php echo 'Class  :' . $participant_details[$i]['class']; ?>                 </td>
                                            <td align="left">&nbsp;&nbsp;
                                                <?php
                                                //echo "<br /><br />-->".var_dump($Photo['pic'][$reg]);
                                                $reg		=	$participant_details[$i]['admn_no'];
                                                if(@$Photo['pic'][$reg])
                                                {
                                                @$pic_path	=	@$Photo['pic'][$reg];
                                                echo "<img src='".@$pic_path."'width='60' height='60'>";
                                                }
                                                else
                                                {
                                                //$document_root 	=	image_url(false)."photos";
                                                @$pic_path	=	image_url()."img_user_nophoto.jpg";
                                                echo "<img src='".@$pic_path."'width='60' height='60'>";
                                                }
                                                ?>                 </td>
                                        </tr>
                                    </table>                    </td>
                            </tr>
                            <tr>
                                <td  width="54" height="36" align="center" valign="top"class="style1" style="border-bottom:1px #666666; border-right:0px #666666; padding:1px;"><?php echo $participant_details[$i]['participant_id']; ?></td>
                            </tr>
                            <?php
                            $item_details = $this->Prereport_Model->get_participant_item_details($participant_details[$i]['participant_id']);
                            $cnt = count($item_details);
                            $l = 1;
                            foreach ($item_details as $item) {
                                if ($l == $cnt)
                                    $style = "style23";
                                else
                                    $style = "style9";
                                $dat_itme = datetophpmodel($item['start_time']);
                                ?>
                                <tr>
                                    <td colspan="2"  class="<?php echo $style; ?>">
                                        <table width="325">
                                            <tr>
                                                <td class="style55" width="165" valign="top" align="left"><?php echo $item['item_code'] . '&nbsp;' . $item['item_name']; ?></td>
                                                <td class="style55" width="160" valign="top" align="right"><?php
                                                    echo ($item['cluster_no']) ? 'CL ' . $item['cluster_no'] . '&nbsp;:&nbsp;' : '';
                                                    echo $item['stage_name'] . '&nbsp;on&nbsp;' . $dat_itme;
                                                    ?>&nbsp;</td>
                                            </tr>
                                        </table>                    </td>
                                </tr>

                                <?php
                                $l++;
                            }
                            ?>
                        </table>
                <?php } ?>         </td><td></td>
                    <?php $i++; ?>
                <td valign="top">
    <?php if (@$participant_details[$i]['participant_id']) { ?>
                        <table border="1" width="275" >
                            <tr>
                                <?php
                                if (@$file_path != "") {
                                    ?>

                                    <td rowspan="2"> <img src="<?php echo $file_path ?>" height="40"></td>
                                    <?php
                                } else {
                                    ?>
                                    <td rowspan="2">&nbsp;</td>

                                    <?php
                                }
                                ?>
                                <td class="style56"  >Kerala School Kalolsavam 2025-26</td>
                            </tr>
                            <tr>

                                <td align="center" class="stylehy"><?php echo @$title . '<br> ' . @$venue; ?><br></td>
                            </tr>

                            <tr bgcolor="#CCCCCC">
                                <td bgcolor="#E5E5E5" colspan="2" align="center" class="style2" style="border-bottom:1px #666666;border-top:1px #666666; border-right:0px #666666; padding:2px;">Participant's Card</td>
                            </tr>

                            <tr>
                                <td align="center">Reg. No</td>
                                <td rowspan="2" style="border-bottom:1px #666666; border-left:1.5px #666666; padding:2px;">
                                    <table>
                                        <tr>
                                            <td>
                                                <span class="style2">

                                                <?php echo wordwrap($participant_details[$i]['participant_name'], 30, '<br/>'); ?></span><br />

                                                <?php echo wordwrap($participant_details[$i]['school_code'] . '  ' . @$participant_details[$i]['school_name'], 30, '<br/>'); ?>                 <br />

        <?php echo 'Class  :' . $participant_details[$i]['class']; ?>                 </td>
                                            <td align="left">&nbsp;&nbsp;
                                                <?php
                                                //echo "<br /><br />-->".var_dump($Photo['pic'][$reg]);
                                                $reg		=	$participant_details[$i]['admn_no'];
                                                if(@$Photo['pic'][$reg])
                                                {
                                                @$pic_path	=	@$Photo['pic'][$reg];
                                                echo "<img src='".@$pic_path."'width='60' height='60'>";
                                                }
                                                else
                                                {
                                                //$document_root 	=	image_url(false)."photos";
                                                @$pic_path	=	image_url()."img_user_nophoto.jpg";
                                                echo "<img src='".@$pic_path."'width='60' height='60'>";
                                                }
                                                ?>                 </td>
                                        </tr>
                                    </table>                    </td>
                            </tr>
                            <tr>
                                <td  width="50"class="style1" valign="top" style="border-bottom:1px #666666; border-right:0px #666666; padding:2px;" align="center"><?php echo $participant_details[$i]['participant_id']; ?></td>
                            </tr>
                            <?php
                            $item_details = $this->Prereport_Model->get_participant_item_details($participant_details[$i]['participant_id']);
                            $cnt = count($item_details);
                            $l = 1;

                            foreach ($item_details as $item) {
                                if ($l == $cnt)
                                    $style = "style23";
                                else
                                    $style = "style9";
                                $dat_itme = datetophpmodel($item['start_time']);
                                ?>
                                <tr>
                                    <td colspan="2"  class="<?php echo $style; ?>">
                                        <table width="325">
                                            <tr>
                                                <td class="style55" width="165" valign="top" align="left"><?php echo $item['item_code'] . '&nbsp;' . $item['item_name']; ?></td>
                                                <td class="style55" width="160" valign="top" align="right"><?php
                                                    echo ($item['cluster_no']) ? 'CL ' . $item['cluster_no'] . '&nbsp;:&nbsp;' : '';
                                                    echo $item['stage_name'] . '&nbsp;on&nbsp;' . $dat_itme;
                                                    ?>&nbsp;</td>
                                            </tr>
                                        </table>                    </td>
                                </tr>
                                <?php
                                $l++;
                            }
                            ?>
                        </table>
    <?php } ?>         </td><td></td>
                    <?php $i++; ?>

                <td valign="top">
                            <?php if (@$participant_details[$i]['participant_id']) { ?>       	   <table border="1" width="275" >
                            <tr>
                                <?php
                                if (@$file_path != "") {
                                    ?>
                                    <td rowspan="2"><img src="<?php echo $file_path ?>" height="40" /></td>
                                    <?php
                                } else {
                                    ?>
                                    <td rowspan="2">&nbsp;</td>
                                    <?php
                                }
                                ?>
                                <td class="style56">Kerala School Kalolsavam 2025-26</td>
                            </tr>
                            <tr>
                                <td align="center" class="stylehy"><?php echo @$title . '<br> ' . @$venue; ?><br /></td>
                            </tr>
                            <tr bgcolor="#CCCCCC">
                                <td bgcolor="#E5E5E5" colspan="2" align="center" class="style2" style="border-bottom:1px #666666;border-top:1px #666666; border-right:0px #666666; padding:2px;">Participant's Card</td>
                            </tr>
                            <tr>
                                <td align="center">Reg. No</td>
                                <td rowspan="2" style="border-bottom:1px #666666; border-left:1.5px #666666; padding:2px;"><table>
                                        <tr>
                                            <td><span class="style2"> <?php echo wordwrap($participant_details[$i]['participant_name'], 30, '<br/>'); ?></span><br />
                                            <?php echo wordwrap($participant_details[$i]['school_code'] . '  ' . @$participant_details[$i]['school_name'], 30, '<br/>'); ?> <br />
                                            <?php echo 'Class  :' . $participant_details[$i]['class']; ?> </td>
                                            <td align="left">&nbsp;&nbsp;
                                                <?php
                                                //echo "<br /><br />-->".var_dump($Photo['pic'][$reg]);
                                                $reg		=	$participant_details[$i]['admn_no'];
                                                if(@$Photo['pic'][$reg])
                                                {
                                                @$pic_path	=	@$Photo['pic'][$reg];
                                                echo "<img src='".@$pic_path."'width='60' height='60'>";
                                                }
                                                else
                                                {
                                                //$document_root 	=	image_url(false)."photos";
                                                @$pic_path	=	image_url()."img_user_nophoto.jpg";
                                                echo "<img src='".@$pic_path."'width='60' height='60'>";
                                                }
                                                ?>                      </td>
                                        </tr>
                                    </table></td>

                            </tr>
                            <tr>
                                <td  width="50"class="style1" valign="top" style="border-bottom:1px #666666; border-right:0px #666666; padding:2px;" align="center"><?php echo $participant_details[$i]['participant_id']; ?></td>
                            </tr>
                            <?php
                            $item_details = $this->Prereport_Model->get_participant_item_details($participant_details[$i]['participant_id']);
                            $cnt = count($item_details);
                            $l = 1;

                            foreach ($item_details as $item) {
                                if ($l == $cnt)
                                    $style = "style23";
                                else
                                    $style = "style9";
                                $dat_itme = datetophpmodel($item['start_time']);
                                ?>
                                <tr>
                                    <td colspan="2"  class="<?php echo $style; ?>"><table width="325">
                                            <tr>
                                                <td class="style55" width="165" valign="top" align="left"><?php echo $item['item_code'] . '&nbsp;' . $item['item_name']; ?></td>
                                                <td class="style55" width="160" valign="top" align="right"><?php
                                        echo ($item['cluster_no']) ? 'CL ' . $item['cluster_no'] . '&nbsp;:&nbsp;' : '';
                                        echo $item['stage_name'] . '&nbsp;on&nbsp;' . $dat_itme;
                                        ?>&nbsp;</td>
                                            </tr>
                                        </table></td>
                                </tr>
                                <?php
                                $l++;
                            }
                            ?>
                        </table>
    <?php } ?>      </td>
    <?php $i++; ?>
            </tr>

            <tr>
                <td colspan="5" height="30">&nbsp;</td>
            </tr>

    <?php
}
?>
    </table>
</page>

