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
echo form_open('report/prereportpdf/participant_regno_photo', array('id' => 'part_reg', 'target' => '_blank'));
 

for ($j = 0; $j < count($retdat); $j++) {
    $dat[$retdat[$j]['school_code']] = $retdat[$j]['school_name'];
}
?>

<input type="hidden" name="hiddtext"  value="">
<div class="row">
    <div class="col-12"> <h5 class="card-title"> Participants Cards - Reg No Wise</h5></div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-4 col-sm-12 col-lg-12">
        Enter Register Number
    </div>
    <div class="col-xl-3 col-md-3 col-sm-12 col-lg-12 mt-2">
        <?php echo form_input("regno", '', 
            'class="form-control" id="regno" onkeypress="javascript:return numbersonly(this, 
            event, false);" '); ?>
    </div>

    <div class="col-xl-3 col-md-3 col-sm-12 col-lg-12 mt-2">
    <?php echo form_input("regno1", '', 'class="form-control" id="regno1" 
        onkeypress="javascript:return numbersonly(this, event, false);" '); ?>
    </div>

    <div class="col-xl-3 col-md-3 col-sm-12 col-lg-12 mt-2">
        <?php echo form_input("regno2", '', 'class="form-control" id="regno2" 
        onkeypress="javascript:return numbersonly(this, event, false);" '); ?>
    </div>
</div>

<div class="row text-center mb-2">
    <div class="col mt-3">
    <?php echo form_submit('Card', 'Card','class="btn btn-primary mt-3"'); ?>
</div>

<?php
echo form_close();
 
?>
