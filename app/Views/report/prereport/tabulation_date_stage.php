<div class="col-xl-4 col-sm-12 col-md-4 col-lg-12">
<?php echo form_open('report/prereportpdf/tabulation_date_stage', 
    array('id' => 'clu', 'target' => '_blank'));
    $fest['All'] = 'All Festivals'; ?>
<input type="hidden" name="hidUserId" id="hidUserId" />

    <div class="card">
      <div class="card-body">
          <h5 class="card-title">Tabulation Sheet <br>(Date & Stage wise)</h5>
          <div class="row">
                <div class="col-3">
                    Date
                </div>
                <div class="col-9">
                    <?php echo form_dropdown("txtDate", @$date_array, (@$date != '') ? $date : '', 'class="form-control" id="txtDate"'); ?>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-3">
                    Stage
                </div>
                <div class="col-9">
                    <div id="cmbitem">
                        <?php echo form_dropdown("cmbstage", $stage, '', 'class="form-control" id="cmbstage" '); ?>
                    </div>
                </div>
            </div>
            <br/><br/>
            <div class="row mt-3">
                <form name="form1" method="post" action="">
                    <center>
                        <?php echo form_submit('Report', 'Report', 'onClick="" class="btn btn-primary mb-4"'); ?>
                    </center>

                </form>
                      
                <input type="hidden" name="hidUserId" id="hidUserId" />
            </div>

          </div>
      </div>
    </div>
  </div>



<?php echo form_close(); ?>