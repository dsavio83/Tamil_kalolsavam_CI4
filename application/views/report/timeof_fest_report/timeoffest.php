<div class="pagetitle">
      <h1>Result Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Result_Item wise</li>
        </ol>
      </nav>
</div>

<?php
echo form_open('report/timefestreport/timefest_result_confidential', 
array('id' => 'timeoffest')); ?>
<div class="row justify-content-center">
    <div class="col-md-12 col-xl-12">
     <div class="card">
        <div class="card-body">
            <table>
                <h5 class="card-title">Declared Result </h5>
                
                <div class="row">
                    <div class="col-xl-2"></div>
                    <div class="col-xl-3 col-md-6 col-sm-12 col-lg-12">
                        Festival Type
                    </div>
                    
                     <div class="col-xl-5 col-md-6 col-sm-12 col-lg-12">
                        <?php echo form_dropdown("resultFest", $fest, '', 'class="form-control" id="resultFest" '); ?>
                    </div>
                    <div class="col-xl-2"></div>
                </div>
                
                <div class="row text-center">
                     <div class="col-12">
                        <input type="submit" name="btnSubmit" class="btn btn-primary mt-4" id="btnSubmit" value="GET">
                    </div>
                </div>
                    
                </div>
            </table>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="hidUserId" id="hidUserId" />

<?php
//itemwise_report_interface

echo form_close();
?>
