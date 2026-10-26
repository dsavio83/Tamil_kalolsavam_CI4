<div align="center" class="heading_gray">
    <h3>Appeal_Courtorder</h3>
</div>
<br />
<?php
echo form_open('district/report/prereportpdf/appeal_courtorder', array('id' => 'formIWP', 'target' => '_blank'));
?>
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
    <tr>
        <th colspan="2" align="left">Stage Allotment (Festival wise)</th>
    </tr>
    <tr>
        
        <td align="left" width="27%" class="table_row_first">Festival Type  </td>
        <td align="left" width="55%" class="table_row_first"><?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-control" id="cmbFestType" onchange="javascript:fetch_callsheet_festival(this.value)"'); ?></td>
        
    </tr>
    <tr>
        
        <td align="left" width="55%" class="table_row_first">      <label>
                <?php echo form_submit('Report', 'Report', 'onClick="javascript: return callsheet()" class="btn btn-primary"'); ?>
            </label>
                </td>
    </tr>
</form>
</table>


<?php
//itemwise_report_interface
echo form_close();
?>