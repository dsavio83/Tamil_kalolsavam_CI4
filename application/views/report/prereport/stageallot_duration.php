<div class="pagetitle"> 
      <h1>Stage</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Stage Allotment Duration</li>
        </ol>
      </nav>
</div>


<?php
echo form_open('report/prereportpdf/stageallot_duration', array('id' => 'formIWP', 'target' => '_blank'));
?>
<div class="row justify-content-center">
    <div class="col-xl-12 col-md-12 col-sm-12 col-lg-12">
     
        <div class="card">
            <div class="card-body">
              <h5 class="card-title">Stage Allotment (Festival wise)</h5>
                <div class="col-12">
                  <label for="inputNanme" class="form-label">Festival Type</label>
                  
                </div>
                <div class="col-12">
                 
                  <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-select" id="cmbFestType" onchange="javascript:fetch_callsheet_festival(this.value)"'); ?>
                </div>
                
                <br>
                <div class="text-center">
                    <?php echo form_submit('Report', 'Report', 'onClick="javascript: return callsheet()" class="btn btn-primary"'); ?>
                  
                </div>
            </div>
          </div>

    </div>
</div>



<?php
//itemwise_report_interface
echo form_close();
?>
