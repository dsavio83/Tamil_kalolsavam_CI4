<div class="pagetitle">
      <h1>Certificate</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Certificate School Wise</li>
        </ol>
      </nav>
</div>

<?php
echo form_open('certificate/certificate/get_certificate_pdf_school_wise', array('id' => 'formIRL'));

?>
<input type="hidden" name="hidUservalue" id="hidUservalue"  value="SHOW" />
<input type="hidden" name="hidSchoolCode" id="hidSchoolCode"  value="<?php echo $school_details[0]['school_code']; ?>" />
<div class="row mt-2">
    <div class="col-12">
        <h3 class="card-title"> <?php echo $school_details[0]['school_name']; ?>&nbsp; - &nbsp;Certificate Details</h3>
    </div>
</div>
            
    <?php if (is_array($fest) and count($fest) > 0) { ?>
<div class="row mt-2">
<div class="col-xl-1"> </div>
    <div class="col-xl-4 col-sm-12 col-lg-6 col-md-6 mt-2">
            Festival Type
    </div>

    <div class="col-xl-6 col-sm-12 col-lg-6 col-md-6 mt-2">
            <?php echo form_dropdown("cmbFestType", $fest, '', 
                'class="form-control" id="cmbFestType" 
                onchange="javascript:get_school_items(this.value)"'); ?>
    </div>
</div>


<div class="row mt-2">
<div class="col-xl-1"> </div>
    <div class="col-xl-4 col-sm-12 col-lg-6 col-md-6 mt-2">
    Item 
    </div>

    <div class="col-xl-6 col-sm-12 col-lg-6 col-md-6 mt-2">
        <div id="all_item_code">
            <select  class="form-control" name="item_code" id="item_code" >
                <option value="0">All Items</option>
            </select>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-xl-1"> </div>
    <div class="col-xl-4 col-sm-12 col-lg-6 col-md-6 mt-2">
    Participant / Captain
    </div>
    
    <div class="col-xl-6 col-sm-12 col-lg-6 col-md-6 mt-2">
        <div id="all_captain_id">
            <select  class="form-control" name="captain_id" id="captain_id" >
                <option value="0">All Participants</option>
            </select>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-xl-1"> </div>
    <div class="col-xl-4 col-sm-12 col-lg-6 col-md-6 mt-2">
        Participant( Group / Pinnani)
    </div>
    
    <div class="col-xl-6 col-sm-12 col-lg-6 col-md-6 mt-2">
        <div id="all_group_participant_id">
            <select  class="form-control" name="participant_id" id="participant_id" >
                <option value="0">All Participants</option>
            </select>
        </div>
    </div>
</div>


<div class="row mb-2">
    <div class="col-12 text-center mt-4">
    <?php echo form_submit('GET', 'GET', 'class="btn btn-primary"'); ?>
    </div>
</div>

<?php } ?>


<?php
//itemwise_report_interface

echo form_close();
?>
