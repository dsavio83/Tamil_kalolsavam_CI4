<div class="col-xl-4 col-sm-12 col-md-4 col-lg-12">
<?php echo form_open('report/prereportpdf/timesheet_date', 
array('id' => 'formIWP', 'target' => '_blank')); ?>
<div class="row"> 
    <div class="card">
        <div class="card-body">
        <h5 class="card-title">Time Sheet(Date Wise)</h5>
            <div class="row">
                <div class="col-4">
                    Festival Type 
                </div>
                <div class="col-8">
                    <?php echo form_dropdown("cmbFestType", $fest, '', 
                    'class="form-control" id="cmbFestType"'); ?>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-4"> 
                    Date
                </div>
                <div class="col-8">
                
                    <?php echo form_dropdown("txtDate", @$date_array, (@$date != '') ? $date : '', 'class="form-control" id="txtDate"'); ?>
                </div>
            </div>

            <div>
                <center>
                    <input type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary mt-3" value="View report">
                </center>
            </div>
            </form>
        </div>
    </div>
</div>
</div>


        
<input type="hidden" name="hidUserId" id="hidUserId" />

<?php echo form_close(); ?>
