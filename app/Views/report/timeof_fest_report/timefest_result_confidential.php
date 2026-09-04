<div class="pagetitle">
      <h1>Result</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Result_Itemwise</li>
        </ol>
      </nav>
</div>
<br />
<?php
echo form_open('report/timefestreportpdf/confidential', array('id' => 'formDWP', 'target' => '_blank'));
?>
 <div class="row justify-content-center">
  <div class="col-md-12 col-sm-12 col-xl-12">
    <div class="card">
      <div class="card-body">
          <h5 class="card-title">Time Sheet (Item)</h5>
          <div class="row">
            <div class="col-6">
                Festival Type
                
                <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-control" id="cmbFestType" onchange="javascript:fetch_item_from_festival2(this.value)"'); ?>
            </div>

            <div class="col-6">
                Item Name
            </div>
            
            <div class="col-6">
                <div id="cmbitem">
                    <?php echo form_dropdown("cbo_item", array('select'), '', 'class="form-control" id="cbo_item"'); ?>  
                </div>
            </div>
            
            <div class="col-12">
                    <center>
                        <input type="submit" name="btnSubmit" class="btn btn-primary" id="btnSubmit" value="View Result">
                    </center>
                
            </div>
        </div>
        </div>
    </div>
</div>
</div>

<input type="hidden" name="hidUserId" id="hidUserId" />

<?php echo form_close(); ?>
