<style>
.display_none {
    display: none;
}
</style>

<?php
echo form_open('', array('id' => 'formIWPq'));
?>
<style>
    .display_none
{
    width: 0px;
    height: :0px;
     display:none

}
</style>
<input type="hidden" name="hidItemId" id="hidItemId" value="" />

<section class="section">
      <div class="row justify-content-center">
        <div class="col-md-12">

          <div class="card">
            <div class="card-body">
                 <!-- Table Start --->
            <div class="table-responsive mt-4">
    <table id="paginationNumbers" class="table table-bordered-sm">
    <tr>
        <th colspan="<?php echo (
            $this->session->get('USER_GROUP') == 'A' || $this->session->get('USER_GROUP') == 'W') ? 6 : 5 ?>" align="left"> 
            &nbsp;&nbsp;&nbsp;Festival  : 
            <?php echo $itempart[0]['fest_name']; ?>&nbsp;&nbsp;</th>
        <th align="right">Print&nbsp;&nbsp;

            <img src="<?php echo base_url(false) . 'images/print_icon.png'; ?>" title="print" class="window_print" 
                                                onClick="javascript:printContent('print_content');
                                                        return false;" /></th>
    </tr>
    <tr class="text-center">
        <th>Item Code & Name  </th>
        <th align="center">Item Type</th>
            <!-- <th align="center">Stage</th> -->
        <th align="center"> No of Students</th>
        <th align="center">Result Entered</th>
        <th align="center">Result Not Entered</th>
        <th align="center">Confirmed</th>
        <?php
        if ($this->session->get('USER_GROUP') == 'A' || $this->session->get('USER_GROUP') == 'W') {
            ;
            ?>
            <th align="center" width="20%" >Reset <br>Confirmation</th>
<?php } ?>
    </tr>

    <?php
    for ($j = 0; $j < count($single); $j++) {
        if ($single[$j]['item_type'] == 'S')
            $itemtype = 'Single';
        else if ($single[$j]['item_type'] == 'G')
            $itemtype = 'Group';
        else
            $itemtype = '';
        ?>
        <tr>
            <td>
               
                <a href="javascript:void(0)" onClick="javascript:getItemResult('<?php echo $single[$j]['item_code'] ?>')">
    <?php echo $single[$j]['item_code'] . '&nbsp;-&nbsp;' . $single[$j]['item_name']; ?></a>
            </td>
            <td align="center"><?php echo $itemtype; ?></td>
            <!-- <td align="center" ><?php //echo $single[$j]['stage_name'];    ?></td> -->
            <td align="center" ><?php echo $single[$j]['cpt']; ?></td>
            <td align="center" ><?php echo $single[$j]['participated_no']; ?></td>
            <td align="center" ><?php echo $single[$j]['cpt'] - $single[$j]['participated_no'] ?></td>
            <td id="result_confirm_single<?php echo $j; ?>_dis" align="center" ><?php echo $single[$j]['is_confirmed']; ?></td>
            <?php
            if ($this->session->get('USER_GROUP') == 'A' || $this->session->get('USER_GROUP') == 'W') {
                ;
                ?>
                <td id="result_confirm_single<?php echo $j; ?>" align="center" >
                       <?php if ($single[$j]['is_confirmed'] == 'Yes') { ?>
                        <a href="javascript:void(0);" 
                           onClick="javascript:resetResultConfirmation('<?php echo $single[$j]['item_code'] ?>',
                                           '<?php echo $single[$j]['item_code'] . '&nbsp;-&nbsp;' . $single[$j]['item_name']; ?>',
                                           'result_confirm_single<?php echo $j; ?>');">Reset</a>
                <?php } ?>
                </td>
        <?php } ?>
        </tr>

        <?php
    }
    ?>


    <?php
    for ($j = 0; $j < count($itempart); $j++) {
        if ($itempart[$j]['item_type'] == 'S')
            $itemtype = 'Single';
        else if ($itempart[$j]['item_type'] == 'G')
            $itemtype = 'Group';
        else
            $itemtype = '';
        ?>
        <tr>
            <td>
                
                <a href="javascript:void(0)" onClick="javascript:getItemResult('<?php echo $itempart[$j]['item_code'] ?>')">
    <?php echo $itempart[$j]['item_code'] . '&nbsp;-&nbsp;' . $itempart[$j]['item_name']; ?></a>
            </td>
            <td align="center"  ><?php echo $itemtype; ?></td>
            <!-- <td align="center" ><?php //echo $single[$j]['stage_name'];    ?></td> -->
            <td align="center" ><?php echo $itempart[$j]['cpt']; ?></td>
            <td align="center" ><?php echo $itempart[$j]['participated_no']; ?></td>
            <td align="center" ><?php echo $itempart[$j]['cpt'] - $itempart[$j]['participated_no']; ?></td>
            <td id="result_confirm_group<?php echo $j; ?>_dis"  align="center" ><?php echo $itempart[$j]['is_confirmed']; ?></td>
            <?php
            if ($this->session->get('USER_GROUP') == 'A' || $this->session->get('USER_GROUP') == 'W') {
                ;
                ?>
                <td id="result_confirm_group<?php echo $j; ?>" align="center" >
                       <?php if ($itempart[$j]['is_confirmed'] == 'Yes') { ?>
                        <a href="javascript:void(0);" 
                           onClick="javascript:resetResultConfirmation('<?php echo $itempart[$j]['item_code'] ?>',
                                           '<?php echo $itempart[$j]['item_code'] . '&nbsp;-&nbsp;' . $itempart[$j]['item_name']; ?>',
                                           'result_confirm_group<?php echo $j; ?>');">Reset</a>
            <?php } ?>
                </td>
        <?php } ?>
        </tr>
    <?php
}
?>
</table>
</div>
</div>
</div>
</div>
</section>


<!-- print content starts here----------------------------------->
<div id="print_content" class="display_none" >
<?php
//$this->load->view('report/report_header');
?>
    <section class="section">
      <div class="row justify-content-center">
        <div class="col-md-8">

          <div class="card">
            <div class="card-body">
<table class="table-bordered">
    <br>
        <tr>
            <th colspan="7" align="left"> 
                &nbsp;&nbsp;&nbsp;Festival  : 
<?php echo $itempart[0]['fest_name']; ?>&nbsp;&nbsp;</th>
        </tr>
        <tr>
            <th width="27%" >&nbsp;&nbsp;&nbsp;Item Code & Name  </th>
            <th align="center" width="10%" >Item Type</th>
                <!-- <th align="center" width="10%" >Stage</th> -->
            <th align="center" width="10%" > No of Students</th>
            <th align="center" width="15%" >Result Entered</th>
            <th align="center" width="15%" >Result Not Entered</th>
            <th align="center" width="10%" >Confirmed</th>
        </tr>

        <?php
        for ($j = 0; $j < count($single); $j++) {
            if ($single[$j]['item_type'] == 'S')
                $itemtype = 'Single';
            else if ($single[$j]['item_type'] == 'G')
                $itemtype = 'Group';
            else
                $itemtype = '';
            ?>
            <tr>
                <td width="27%" >
                    &nbsp;&nbsp;
    <?php echo $single[$j]['item_code'] . '&nbsp;-&nbsp;' . $single[$j]['item_name']; ?>
                </td>
                <td align="center"  ><?php echo $itemtype; ?></td>
                <!-- <td align="center" ><?php //echo $single[$j]['stage_name'];    ?></td> -->
                <td align="center" ><?php echo $single[$j]['cpt']; ?></td>
                <td align="center" ><?php echo $single[$j]['participated_no']; ?></td>
                <td align="center" ><?php echo $single[$j]['cpt'] - $single[$j]['participated_no'] ?></td>
                <td id="result_confirm_single<?php echo $j; ?>_dis" align="center" ><?php echo $single[$j]['is_confirmed']; ?></td>
            </tr>

    <?php
}
?>


        <?php
        for ($j = 0; $j < count($itempart); $j++) {
            if ($itempart[$j]['item_type'] == 'S')
                $itemtype = 'Single';
            else if ($itempart[$j]['item_type'] == 'G')
                $itemtype = 'Group';
            else
                $itemtype = '';
            ?>
            <tr>
                <td width="27%" >
                    &nbsp;&nbsp;
    <?php echo $itempart[$j]['item_code'] . '&nbsp;-&nbsp;' . $itempart[$j]['item_name']; ?>
                </td>
                <td align="center"  ><?php echo $itemtype; ?></td>
                <!-- <td align="center" ><?php //echo $single[$j]['stage_name'];   ?></td> -->
                <td align="center" ><?php echo $itempart[$j]['cpt']; ?></td>
                <td align="center" ><?php echo $itempart[$j]['participated_no']; ?></td>
                <td align="center" ><?php echo $itempart[$j]['cpt'] - $itempart[$j]['participated_no']; ?></td>
                <td id="result_confirm_group<?php echo $j; ?>_dis"  align="center" ><?php echo $itempart[$j]['is_confirmed']; ?></td>
            </tr>
        <?php
    }
    ?>
    </table>
</div>
</div>
</div>
</div>
</section>
<?php
//$this->load->view('report/report_footer');
?>
</div>
<!-- display content ends here --------------------------------------->
<?php
//itemwise_report_interface
echo form_close();
?>