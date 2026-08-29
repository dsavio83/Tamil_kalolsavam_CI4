<div class="pagetitle">
      <h1>PreFest Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">List Participant More than One Item</li>
        </ol>
      </nav>
</div>

<?php
echo form_open('report/prereportpdf/more_limit_partlist', array('id' => 'formIWP', 'target' => '_blank'));
 
?>
<input type="hidden" name="hidUserId" id="hidUserId" />
<div class="row">
    <div class="col-12">
        <h5 class="card-title">List of Participants in More than one Item </h5>
    </div>
</div>

<div class="row">
    <div class="col-xl-4 col-md-4 col-sm-12 col-lg-12 mt-3">
    Festival Type
    </div>

    <div class="col-xl-4 col-md-4 col-sm-12 col-lg-12 mt-3">
        <?php echo form_dropdown("cmbFestType", $fest, '', 
            'class="form-control" id="cmbFestType"'); ?>
    </div>
</div>

<div class="row">
    <div class="col-xl-4 col-md-4 col-sm-12 col-lg-12 mt-3">
        Limit Number
    </div>

    <div class="col-xl-4 col-md-4 col-sm-12 col-lg-12 mt-3">
        <?php echo form_dropdown("txtLimitcode", $no_array, '', 
            'class="form-control" id="txtLimitcode" '); ?>
    </div>
</div>

<div class="row">
    <div class="col-12 text-center">
    <?php echo form_submit('Report', 'Report', 
                'onClick="javascript: return morethanonelimit()" 
                class="btn btn-primary mt-3"'); ?>
    </div>
</div>


<?php
//itemwise_report_interface
echo form_close();
 
?>
