<div class="pagetitle">
      <h1>PreFest Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Team Manager List</li>
        </ol>
      </nav>
</div>

<?php
echo form_open('report/prereportpdf/list_participants', array('id' => 'formPWD', 'target' => '_blank'));

for ($j = 0; $j < count($retdat); $j++) {
    $dat[$retdat[$j]['school_code']] = $retdat[$j]['school_name'];
}
?>
<input type="hidden" name="hiddtext" id="hiddtext" value="">

<div class="row">
    <div class="col-md-6 col-sm-12 col-xl-6">
        <div class="card">
            <div class="card-body">
            <h5 class="card-title"> List of Participant - School wise</h5>
                <div class="row">
                    <div class="col-md-12">
                    <label for="inputNanme" class="form-label">School Code</label> 
                    </div>

                    <div class="col-md-12">
                       <?php echo form_input("txtSchoolCode", @$school_details[0]['school_code'], 'class="form-control" id="txtSchoolCode"
                             onkeypress="javascript:return numbersonly(this, event, false);"  onBlur="javascript:fetch_schooldetails(this.value)"'); ?> 
                    </div>
                    <div class="col-md-12">
                         &nbsp;
                    </div>

                    <div class="col-md-12 text-center">
                    <?php echo form_dropdown("Festpart", $pfest, '', 'class="form-select" id="Festpart"'); ?>
                    </div>
                </div>

                <div class="row">
                  
                <div class="text-center">
                       <?php echo form_submit('Report', 'Report', 'onClick="javascript: return fnschgpwdAddrr()" class="btn btn-primary mt-4"'); ?>
                </div>
            </div>
                
        </div>   
        <?php
echo form_close();
?>
    </div>
    </div>
