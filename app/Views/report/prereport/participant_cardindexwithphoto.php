<div class="pagetitle">
      <h1>PreFest Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Participant Card - Middle</li>
        </ol>
      </nav>
</div>

<?php
echo form_open('report/prereportpdf/participant_cardindex2', array('id' => 'formPWD', 'target' => '_blank'));
 

for ($j = 0; $j < count($retdat); $j++) {
    $dat[$retdat[$j]['school_code']] = $retdat[$j]['school_name'];
}
?>
<input type="hidden" name="hiddtext" value="" >
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
    <tr>
        <th colspan="4" align="left"><strong>Participants Cards - District Wise</strong></th>
    </tr>
    <tr>

        <td width="20%" align="left"><strong> &nbsp;District</strong></td>
        <td width="24%" align="left"><?php echo form_dropdown("cboDistCode", $dist_array, $this->input->post('cboDistCode'), 'class="form-control" id="cboDistCode"'); ?> </td>
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
<?php /* echo form_open('report/prereportpdf/participant_cardindex', array('id' => 'formPWD','target'=>'_blank'));
  

  for($j=0;$j<count($retdat); $j++){
  $dat[$retdat[$j]['school_code']] = $retdat[$j]['school_name'];
  } */
?>
<!--<input type="hidden" name="hiddtext" name="hiddtext" value="">
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
  <tr>
    <th colspan="4" align="left"><strong>Participants Cards - School Wise</strong></th>
  </tr>
  <tr>
   
    <td width="20%" align="left"><strong> &nbsp;Enter School Code  :</strong></td>
    <td width="24%" align="left"><?php //echo form_input("txtSchoolCode", @$school_details[0]['school_code'], 'class="form-control" id="txtSchoolCode"  onkeypress="javascript:return numbersonly(this, event, false);" onBlur="javascript:fetch_schooldetails(this.value)"');    ?> </td>
    <td width="55%"><div id="cmbitem"> </div><td width="1%">
  </tr>
  <tr>
    <td align="center" colspan="3"><?php //echo form_submit('Report', 'Report', 'onClick="javascript: return fnschgpwdAddrr()"');    ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
  </tr>
</table>-->
<?php
//
//echo form_close();
?>
