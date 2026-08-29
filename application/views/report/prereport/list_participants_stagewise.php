<?php
echo form_open('report/prereportpdf/list_participants_stagewise', array('id' => 'list_participants_stagewise', 'target' => '_blank'));

?>
<input type="hidden" name="hidUserId" id="hidUserId" />
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
    <tr>
        <th colspan="2" align="left">Participants List:(Stage & Date wise)</th>
    </tr>
    <tr>
        
        <td align="left" width="27%" class="table_row_first">Stage Name  </td>

        <td align="left" width="55%" class="table_row_first"><div id="cmbitem"><?php echo form_dropdown("cmbstage", $stage, '', 'class="form-control" id="cmbstage" '); ?></div></td>
        
    </tr>
    <tr>
        
        <td align="left" class="table_row_first">Date </td>
        <td align="left" class="table_row_first">
            <?php echo form_dropdown("txtDate", @$date_array, (@$date != '') ? $date : '', 'class="form-control" id="txtDate"'); ?>
                <!--<input class="form-control date_field" type="text" onfocus="displayCalendar(document.formAllotment.txtDate,'yyyy-mm-dd',this)" name="txtDate" id="txtDate" value="<?php echo @$date; ?>" readonly="readonly">-->
                    <!--<input class="form-control date_field" type="text"   onFocus="javascript:vDateType='3'" onBlur="DateFormat(this,this.value,event,true,'3')" onKeyDown="DateFormat(this,this.value,event,false,'3')" onKeyUp="DateFormat(this,this.value,event,false,'3')" maxlength="10"  name="txtDate" id="txtDate" value="<?php //echo $start_date;    ?>">-->
                <!--<img src="<?php echo image_url(); ?>calender.png" onclick="displayCalendar(document.formAllotment.txtDate,'yyyy-mm-dd',this)" width="16" height="16" style="cursor:pointer" />-->
           </td>
        
    </tr>
    <tr>
       
        <td align="center" colspan="2">      <label>
                <?php echo form_submit('Report', 'Report', 'onClick="" class="btn btn-primary"'); ?>
            </label>
            </form>    </td>
       
    </tr>

</table>


<?php
//itemwise_report_interface

echo form_close();
?>
