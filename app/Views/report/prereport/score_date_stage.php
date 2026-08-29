  <div class="col-xl-4 col-sm-12 col-md-4 col-lg-12">
<?php
echo form_open('report/prereportpdf/score_date_stage', 
    array('id' => 'clu', 'target' => '_blank'));
$fest['All'] = 'All Festivals'; ?>
<input type="hidden" name="hidUserId" id="hidUserId" />

<div class="row justify-content-center">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
          <h5 class="card-title">Score Sheet:(Date & Stage wise)</h5>
          <div class="row">
            <div class="col-6">
                    Date

                    <?php echo form_dropdown("txtDate", @$date_array, (@$date != '') ? $date : '', 'class="form-control" id="txtDate"'); ?>
            </div>

            <div class="col-6">
                Stage  
                <div id="cmbitem">
                    <?php echo form_dropdown("cmbstage", $stage, '', 'class="form-control" id="cmbstage" '); ?>          
                </div>
            </div>

            <div class="col-12">
                    <center>
                        <?php echo form_submit('Report', 'Report', 'onClick="" class="btn btn-primary mt-3"'); ?>
                    </center>            
            </div>
          </div>
          </table>
        </form>
      </div>
    </div>
  </div>
</div>
        </div>
            


<?php
//itemwise_report_interface
echo form_close();
?>