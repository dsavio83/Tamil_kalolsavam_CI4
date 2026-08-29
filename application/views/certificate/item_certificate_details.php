<style>
.display_none {
    display: none;
}
</style>

<?php
echo form_open('', array('id' => 'formIWPq'));
$slno = 0;
?>
<input type="hidden" name="hidItemId" id="hidItemId" value=""  />
<div class="row justify-content-center">
    <div class="col-xl-12 col-sm-12 col-md-12">
              <div class="card recent-sales overflow-auto">
                <div class="card-body">
                  <h5 class="card-title">Certificate <span>| Item wise Print</span></h5>
            <!-- Table Start --->
<div class="table-responsive-md">
    <table id="paginationNumbers" class="table" width="100%">
               
        <th colspan="9" align="left"> 
            &nbsp;&nbsp;&nbsp;Festival 
            <?php echo $itempart[0]['fest_name']; ?>&nbsp;&nbsp;</th>
        <th align="right">Print&nbsp;&nbsp;<img src="<?php echo base_url(false) . 'images/print_icon.png'; ?>" title="print" class="window_print" onClick="javascript:printContent('print_content');
                            return false;" /> </th>
    </tr>
    <tr class="text-center">
        <th>Sl. No</th>
        <th>Is Printed?</th>
        <th> Item Code & Name  </th>
        <th>Item Type</th>
        <th> No.Students</th>
        <th>Participation</th>
        <th>Non-Participation</th>
        <th>A Grade</th>
        <th>B Grade</th>
        <th>C Grade</th>
    </tr>
    <?php
    for ($j = 0; $j < count($itempart); $j++) {
        if ($itempart[$j]['item_type'] == 'S')
            $itemtype = 'Single';
        else if ($itempart[$j]['item_type'] == 'G')
            $itemtype = 'Group';
        else
            $itemtype = '';
        if ($itempart[$j]['is_certificate_printed'] == 0)
            $printed = 'No';
        else
            $printed = 'Yes';

        $slno++;
        ?>
        <tr>
            <td width="3%"><?php echo $slno; ?></td>
            <td width="4%"><?php echo $printed; ?></td>
            <td width="41%">
                
                <a href="javascript:void(0)" onClick="javascript:getItemCertificate('<?php echo $itempart[$j]['item_code'] ?>')">
                    <?php echo $itempart[$j]['item_code'] . '&nbsp;-&nbsp;' . $itempart[$j]['item_name']; ?></a>
            </td>
            <td align="center" ><?php echo $itemtype; ?></td>
            <?php $itempart[$j]['cpt'] = $this->General_Model->get_record_count('participant_item_details', 'is_captain="Y" AND item_code=' . $itempart[$j]['item_code'], 'participant_id'); ?>
            <td align="center"><?php echo $itempart[$j]['cpt']; ?></td>
            <td align="center"><?php echo $itempart[$j]['participated_no']; ?></td>
            <td align="center"><?php echo $itempart[$j]['cpt'] - $itempart[$j]['participated_no']; ?></td>
            <td align="center"><?php echo $itempart[$j]['a_grade']; ?></td>
            <td align="center"><?php echo $itempart[$j]['b_grade']; ?></td>
            <td align="center"><?php echo $itempart[$j]['c_grade']; ?></td>
        </tr>
        <?php
    }
    ?>
</table>
</div>
</div>
</div>
</div>
</div>
<!-- print content starts here----------------------------------->
<div id="print_content" class="display_none" >
    <?php
    //$this->load->view('report/report_header');
    ?>
    <table width="100%" border="1" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
        <tr>
            <th colspan="8" align="left"> 
                &nbsp;&nbsp;&nbsp;Festival  : 
                <?php echo $itempart[0]['fest_name']; ?>&nbsp;&nbsp;</th>
        </tr>
        <tr>
            <th width="40%">&nbsp;Item Code & Name  </th>
            <th align="center" width="10%">Item Type</th>
            <th align="center" width="10%"> No. Students</th>
            <th align="center" width="10%">Participation</th>
            <th align="center" width="10%">Non- Participation</th>
            <th align="center" width="10%">A Grade</th>
            <th align="center" width="10%">B Grade</th>
            <th align="center" width="10%">C Grade</th>
        </tr>
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
                <td width="40%">
                    &nbsp;
                    <?php echo $itempart[$j]['item_code'] . '&nbsp;-&nbsp;' . $itempart[$j]['item_name']; ?>
                </td>
                <td align="center" ><?php echo $itemtype; ?></td>
                <!-- <td align="center"><?php //echo $single[$j]['stage_name'];    ?></td> -->
                <td align="center"><?php echo $itempart[$j]['cpt']; ?></td>
                <td align="center"><?php echo $itempart[$j]['participated_no']; ?></td>
                <td align="center"><?php echo $itempart[$j]['cpt'] - $itempart[$j]['participated_no']; ?></td>
                <td align="center">&nbsp;<?php echo $itempart[$j]['a_grade']; ?></td>
                <td align="center">&nbsp;<?php echo $itempart[$j]['b_grade']; ?></td>
                <td align="center">&nbsp;<?php echo $itempart[$j]['c_grade']; ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
    //$this->load->view('report/report_footer');
    ?>
</div>
<!-- display content ends here --------------------------------------->
<?php
//itemwise_report_interface
echo form_close();
?>