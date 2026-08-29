<div class="pagetitle">
      <h1>Certificate</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Certificate Item Details</li>
        </ol>
      </nav>
</div>
<?php
echo form_open('certificate/certificate/list_item_wise', array('id' => 'formIRL')); ?>
<input type="hidden" name="hidUservalue" id="hidUservalue" value="SHOW" />
<div class="row justify-content-center">
    <div class="col-xl-12 col-sm-12 col-md-12">
    
        <div class="card">
            <div class="card-body">
              <h5 class="card-title"> Certificate School Wise</h5>
                <div class="col-12">
                  <label for="inputNanme" class="form-label">Festival Type</label>
                  
                </div>
                <div class="col-12">
                    <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-select" id="cmbFestType"'); ?>
                </div>

                <div class="col-12">
                    <br>
                </div>
                
                <br>
                <div class="text-center">
                      <?php echo form_submit('GET', 'GET', 'class="btn btn-primary"'); ?>
                </div>
            </div>
          </div>

    </div>
</div>

<?php
//itemwise_report_interface
echo form_close();
?>