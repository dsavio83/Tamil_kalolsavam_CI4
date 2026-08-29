<div class="pagetitle">
      <h1>PreFest Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Participant Card (With Photo)</li>
        </ol>
      </nav>
</div>

<?php
echo form_open('report/prereportpdf/participant_cardindex_school_photo', array('id' => 'formPW', 'target' => '_blank'));
 

for ($j = 0; $j < count($retdat); $j++) {
    $dat[$retdat[$j]['school_code']] = $retdat[$j]['school_name'];
}
?>
<input type="hidden" name="hiddtext" id="hiddtext" value="">
<div class="container">
    <div class="row">
        <div class="col-12 mt-2">
           <h3 class="card-title"> Participants Cards - School Wise </h3>
        </div>
    </div>


    <div class="row">
        <div class="col-xl-3 col-sm-6 col-md-3 col-lg-6">
            Enter School Code
        </div>

        <div class="col-xl-3 col-sm-6 col-md-3 col-lg-6 mt-2">
            <?php echo form_input("txtSchoolCode", @$school_details[0]['school_code'], 'class="form-control" id="txtSchoolCode"  onkeypress="javascript:return numbersonly(this, event, false);" onBlur="javascript:fetch_schooldetails(this.value)"'); ?>
        </div>
        
        <div class="col-xl-3 col-sm-6 col-md-3 col-lg-6">
            <div id="cmbitem"> </div>
        </div>
    </div>
        

    
    
    <div class="row">
        <div class="col-12 text-center mt-3">
                <?php echo form_submit('Report', 'Report', 'onClick="javascript: return fnschgpwdAddrr()" class="btn btn-primary"'); ?>
        </div>
    </div>
    </div>
  

<?php
echo form_close();
 
?>
