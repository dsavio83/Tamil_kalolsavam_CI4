<div class="pagetitle">
      <h1>PreFest Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="<?php echo base_url(); ?> welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Itemwise Participants</li>
        </ol>
      </nav>
</div>


<?php
echo form_open('report/prereportpdf/rpt_itemwiseparticipants', array('id' => 'formIWP', 'target' => '_blank'));

?>
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
    <tr>
        <th colspan="2" align="left">Select Item</th>
    </tr>
    <tr>
        
        <td align="left" width="27%" class="table_row_first">Festival Type </td>
        <td align="left" width="55%" class="table_row_first"><?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-control" id="cmbFestType" onchange="javascript:fetch_item_from_participant(this.value)"'); ?></td>
        
    </tr>
    <tr>
        
        <td align="left" width="27%" class="table_row_first"> Item Name </td>

        <td align="left" width="55%" class="table_row_first"><div id="cmbitem"><?php echo form_dropdown("cbo_item", array('select'), '', 'class="form-control" id="cbo_item" '); ?></div></td>
        
    </tr>
    <tr>
        
        <td align="center" colspan="2"><form name="form1" method="post" action="">
                <label>
                    <input type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary" value="View report" onclick="javascript: return callsheetfirst()">
                </label>
            </form>
        </td>
        
    </tr>

    <tr>
        <td align="center" colspan="4">&nbsp;</td>
    </tr>
</table>
<input type="hidden" name="hidUserId" id="hidUserId" />

<?php
//itemwise_report_interface

echo form_close();
?>
