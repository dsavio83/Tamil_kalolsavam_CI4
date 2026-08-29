<div class="pagetitle">
      <h1>Result</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Certificate Participant</li>
        </ol>
      </nav>
</div>
<?php
echo form_open('certificate/certificate/get_certificate_pdf', array('id' => 'formIRL'));

?>
<input type="hidden" name="hidUservalue" id="hidUservalue"  value="SHOW" />
<input type="hidden" name="hidItemId" id="hidItemId"  value="<?php echo $this->input->post('hidItemId') ?>" />
<input type="hidden" name="hidItemType" id="hidItemType"  value="<?php echo $item_type; ?>" />

<div class="row">
    <div class="col-12">
        <h3 class="card-title"> Certificate - Participant Wise </h3>
    </div>
</div>

<?php if (is_array(@$captain_detail) and count(@$captain_detail) > 0) { ?>
<div class="row">
    <div class="col-xl-3"></div>
    <div class="col-xl-6">
        <?php echo form_dropdown("captain_id", @$captain_detail, '', 
        'class="form-control" id="captain_id" ' . $javascript_code);
        ?>
    </div>
    <div class="col-xl-3"></div>
</div>

<?php if ('G' == $item_type) { ?>
<div class="row" id="all_participant_tr_id">
    <div class="col-xl-3"></div>
    <div class="col-xl-6">
    <div id="all_participant_id">
        <select  class="form-control" name="participant_id" id="participant_id" >
            <option vlaue="0">All Participant</option>
        </select>
    </div>
    </div>
    <div class="col-xl-3"></div>
</div>
    <?php } ?>
        
<div class="row mt-3">
    <div class="col-12 text-center">
    <?php echo form_submit('GET', 'GET', 'class="btn btn-primary"'); ?>
    </div>
</div>
    <?php } ?>
   


<?php
//itemwise_report_interface

echo form_close();
?>
