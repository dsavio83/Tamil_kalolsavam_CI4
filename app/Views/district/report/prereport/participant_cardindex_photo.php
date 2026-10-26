<div class="pagetitle">
      <h1>PreFest Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Participant Card</li>
        </ol>
      </nav>
</div> 

<?php
echo form_open('district/report/prereportpdf/participant_cardindex_photo', array('id' => 'formPWD', 'target' => '_blank'));
 

for ($j = 0; $j < count($retdat); $j++) {
    $dat[$retdat[$j]['school_code']] = $retdat[$j]['school_name'];
}
?>
<input type="hidden" name="hiddtext" id="hiddtext" value="" >
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
    <tr>
        <th colspan="3" align="left"><strong>Participants Cards - Subdistrict Wise</strong></th>
    </tr>
    <tr>

        <td width="20%" align="left"><strong> &nbsp;Subdistrict  </strong></td>
        <td width="24%" align="left"><?php echo form_dropdown("cboSubDistCode", $sub_dist_array, $this->input->post('cboSubDistCode'), 'class="form-control" id="cboSubDistCode"'); ?> </td>
        <td width="55%"><div id="cmbitem"> </div><td width="1%">
    </tr>
    <tr>   
        <td width="20%" align="left"><strong> &nbsp;No.of Cards per sheet </strong></td>
        <td width="24%" align="left"><?php echo form_dropdown('cboNoofcards', array('N' => 'Four(4)', 'Y' => 'Maximam'), 'id="cboLabelPrint" class="form-control"') ?></td>
        <td width="55%"><div id="cmbitem"> </div><td width="1%">
    </tr>
    <tr>
        <td align="center" colspan="3"><?php echo form_submit('Report', 'Report', 'class="btn btn-primary mt-3"'); ?></td>
    </tr>
</table>
<?php
echo form_close();
 
?>

<br />

