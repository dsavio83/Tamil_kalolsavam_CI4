 <div class="col-xl-4 col-sm-12 col-md-4 col-lg-12">
<?php echo form_open('report/prereportpdf/callsheet_all', 
    array('id' => 'callsheetall', 'target' => '_blank'));
?>
<input type="hidden" name="hidUserId" id="hidUserId" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title">CallSheet Report (Festival wise)</h5>
                        <div class="row">
                            <div class="col-4">
                                Festival Type
                            </div>
                            <div class="col-8">
                                <?php echo form_dropdown("cmbFestType1", $fest, '', 'class="form-control" id="cmbFestType1"'); ?>
                            </div>
                            <br><br><br><br>
                            <div class="col-12 mb-2">
                            <center>
                                <?php echo form_submit('Report', 'Report', 'onClick="javascript: return callsheet()" class="btn btn-primary mt-3"'); ?>
                            </center>
                        </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

</form>
    
<?php echo form_close(); ?>
</div>