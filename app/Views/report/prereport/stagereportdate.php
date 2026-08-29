 <div class="pagetitle">
      <h1>Stage Report</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="<?php echo base_url(); ?> welcome" title="Home">Home</a></li>
      <li class="breadcrumb-item active">Stage Report</li>
    </ol>
  </nav>
</div>

 <div align="center" class="heading_gray">
    <h3></h3>
</div>
<br />
<?php
echo form_open('report/prereportpdf/stagereportdate', array('id' => 'formStageReport', 'target' => '_blank'));

?>
<form name="form1" method="post" action="">
<div class="row justify-content-center">
        <div class="col-md-12 col-sm-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Stage Report - Date wise</h5>
    <table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
        
        <tr>
            <td align="left" width="27%">
                Stage Name 
            </td>
            <td align="left" width="55%"><div id="cmbitem"><?php echo form_dropdown("cmbstage", $stage, '', 'class="form-control" id="cmbstage" '); ?></div>
            </td>
            
        </tr>
        <tr>
            
            <td align="left">Date </td>
            <td align="left"> <?php echo form_dropdown("txtDate", @$date_array, (@$date != '') ? $date : '', 'class="form-control" id="txtDate"'); ?>
                    <!--<input class="form-control date_field" type="text" onfocus="displayCalendar(document.formAllotment.txtDate,'yyyy-mm-dd',this)" name="txtDate" id="txtDate" value="<?php echo @$date; ?>" readonly="readonly">-->
                        <!--<input class="form-control date_field" type="text"   onFocus="javascript:vDateType='3'" onBlur="DateFormat(this,this.value,event,true,'3')" onKeyDown="DateFormat(this,this.value,event,false,'3')" onKeyUp="DateFormat(this,this.value,event,false,'3')" maxlength="10"  name="txtDate" id="txtDate" value="<?php //echo $start_date;    ?>">-->
                    <!--<img src="<?php echo image_url(); ?>calender.png" onclick="displayCalendar(document.formAllotment.txtDate,'yyyy-mm-dd',this)" width="16" height="16" style="cursor:pointer" />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->
        </tr>
        <tr class="text-center">
            <td colspan="2">
                <label>
                    <input type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary mt-3" value="View report">
                </label>       
            </td>
        </tr>

    </table>
    <input type="hidden" name="stagename" id="stagename" />
</form>
</div>
</div>
</div>
</div>
<?php
//itemwise_report_interface

echo form_close();
?>