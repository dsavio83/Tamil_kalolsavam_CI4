
<div class="col-md-12 col-sm-12 col-xl-4">

    <?php
    echo form_open('report/prereportpdf/cluster_report_all', 
    array('id' => 'cluster', 'target' => '_blank')); ?>
<input type="hidden" name="hidUserId" id="hidUserId" />
<div class="row justify-content-center">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                    <h5 class="card-title">Cluster Report:<br> (Festival wise)</h5>
                        <table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" >
                            <div class="row">
                                 <div class="col-12 mb-2">
                                    Festival Type
                                </div>

                                 <div class="col-12 mb-2">
                                    <?php echo form_dropdown("cmbFestType1", $fest, '', 'class="form-control" id="cmbFestType1" '); ?>
                                </div>
                            </div>
                            <br><br><br>
                             <div class="row">
                                 <div class="col-4 mb-3">
                                    <?php echo form_submit('Report', 'Report', 'onClick="javascript: return cluster()" class="btn btn-primary"'); ?>
                                </div>

                                <div class="col-6 mb-3">
                                    <?php echo form_submit('Report_for_press', 'Report For Press', 'onClick="javascript: return cluster_doc()" class="btn btn-primary mb-1"'); ?>
                                </div>
                            </div>
                            
    
                    </table>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>

<?php
    echo form_close();
?>