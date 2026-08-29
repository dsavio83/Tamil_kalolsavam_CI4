<!-- <div class="pagetitle">
      <h1>PreFest Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Participant Card (Single)</li>
        </ol>
      </nav>
</div> -->
<br />
<?php
echo form_open('report/prereportpdf/participant_regno', array('id' => 'part_reg', 'target' => '_blank'));
 

for ($j = 0; $j < count($retdat); $j++) {
    $dat[$retdat[$j]['school_code']] = $retdat[$j]['school_name'];
}
?>

<input type="hidden" name="hiddtext" name="hiddtext" value="">
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
    <tr>
        <th colspan="2" align="left"><strong>Participants Cards - Reg No Wise</strong></th>
    </tr>
    <tr>

        <td width="20%" align="left" class="table_row_first"><strong> &nbsp;Enter Register Number :</strong></td>
        <td width="24%" align="left" class="table_row_first"><?php echo form_input("regno", '', 'class="form-control" id="regno" onkeypress="javascript:return numbersonly(this, event, false);" '); ?> </td>
    </tr>
    <tr>
        <td align="center" colspan="2"><?php echo form_submit('Card', 'Card','class="btn btn-primary mt-3"'); ?></td>
    </tr>
</table>
<?php
echo form_close();
 
?>
