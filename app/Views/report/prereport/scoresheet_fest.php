 <div class="col-xl-4 col-sm-12 col-md-4 col-lg-12">
        <?php
        echo form_open('report/prereportpdf/fest_scoresheet_details', 
            array('id' => 'formPWD', 'target' => '_blank')); ?>

  <div class="col-12">
    <div class="card">
      <div class="card-body">
          <h5 class="card-title">Score Sheet - Festival wise</h5>
              <div class="row">
                <div class="col-12">
                         Festival
                
                            <?php echo form_dropdown("txtfestFrom", $fest, '', 'class="form-control" id="cmbFestType"'); ?>

                             <!-- <?php echo form_dropdown("txtfestFrom", array(
                            $retdat[0]['fest_id'] => $retdat[0]['fest_name'],
                            $retdat[1]['fest_id'] => $retdat[1]['fest_name'],
                            $retdat[2]['fest_id'] => $retdat[2]['fest_name'],
                            $retdat[3]['fest_id'] => $retdat[3]['fest_name']
                            ), 'class="form-control" id="txtfestFrom" '); ?> -->
                </div>

                <div class="col-12">
                    <form name="form1" method="post" action="">
                        <center>
                            <?php echo form_submit('Report', 'View Report',
                            'class="btn btn-primary mt-3"'); ?>
                        </center>
                    </form>
                           
                    <input type="hidden" name="hidUserId" id="hidUserId" />
                </div>
              </div>
              <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>

