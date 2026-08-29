<div class="pagetitle">
      <h1>Pre Fest Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Fee Details Report</li>
        </ol>
      </nav>
</div>

<?php
echo form_open('report/prereportpdf/feedetails', 
    array('id' => 'formPWD', 'target' => '_blank'));

for ($j = 0; $j < count($retdat); $j++) {
    $dat[$retdat[$j]['school_code']] = $retdat[$j]['school_name'];
} ?>
<input type="hidden" name="hiddtext" name="hiddtext" value="">

<div class="row justify-content-center">
            <div class="col-md-12 col-sm-6 col-xl-6">
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title">Participating Schools</h5>
                        <div class="row">
                            <div class="col-md-12">
    School Code  
    <?php echo form_input("txtSchoolCode", @$school_details[0]['school_code'], 'class="form-control" id="txtSchoolCode"
	onkeypress="javascript:return numbersonly(this, event, false);"   onBlur="javascript:fetch_report_schooldetails(this.value)"'); ?>
        
            <div id="cmbitem"> </div>
            <center>
                <?php echo form_submit('Report', 'Report', 'onClick="javascript: return fnschgpwdAddrr()" class="btn btn-primary mt-3"'); ?>

        <br/><br/>
        <span>
            <a href="../prereportpdf/all_feedet_report_all" target="_blank" class="mt-3">Fee Details ALL</a>
        </span>
        </center>
        
<?php echo form_close(); ?>