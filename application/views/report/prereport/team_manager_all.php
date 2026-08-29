
<div class="col-md-12 col-sm-4 col-xl-6">
<?php
echo form_open('report/prereportpdf/team_manager_all', array('id' => 'team_manager', 'target' => '_blank')); ?>

<input type="hidden" name="hidUservalue" id="hidUservalue"  value="SHOW" />

            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title"> Team Manager List</h5>
                        <div class="row">
                            <div class="col-12">
                             <label for="inputNanme" class="form-label">List of Participant Details - Festival wise </label>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                            <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-select" id="cmbFestType"'); ?> <br>
                            </div>
                            <br>
                        </div>
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