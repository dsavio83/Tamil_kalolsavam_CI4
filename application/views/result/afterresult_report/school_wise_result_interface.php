<div class="pagetitle">
    <h1>Result Report</h1>
    <nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
        <li class="breadcrumb-item active">Item Wise Point Result</li>
    </ol>
    </nav>
</div>

<?php
echo form_open('report/afterresultreportpdf/school_wise_result', array('id' => 'formPWD', 'target' => '_blank'));

//for($j=0;$j<count($retdat); $j++){
//$dat[$retdat[$j]['school_code']] = $retdat[$j]['school_name'];
//}
?>
<input type="hidden" name="hiddtext" id="hiddtext" value="">
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
    <tr>
        <h5 class="card-title">List of Participant Details - School wise</h5>
    </tr>
    <tr>
    <td width="21%" align="left" class="table_row_first"> &nbsp;</td>
        <td width="21%" align="left" class="table_row_first"> School Code  :</td>
        <td width="23%" align="left" class="table_row_first"><?php echo form_input("txtSchoolCode", @$school_details[0]['school_code'], 'class="form-control" id="txtSchoolCode"
	onkeypress="javascript:return numbersonly(this, event, false);"  onBlur="javascript:fetch_schooldetails(this.value)"'); ?> </td>
        <td width="55%" class="table_row_first"><div id="cmbitem"> </div><td width="1%">
    </tr>
<tr class="text-center mt-4">
    <td align="center" colspan="4"><?php echo form_submit('Report', 'Report', 'class="btn btn-primary mt-4"','onClick="javascript: return fnschgpwdAddrr()"'); ?></td>
</tr>
<tr class="text-center mt-4">
    <td colspan="4" align="center"><a href="../afterresultreportpdf/allschool_points_result" target="_blank">All School </a></td></tr>
<tr>
</table>
<?php
echo form_close();

?>
