<div class="col-md-12 col-sm-12 col-xl-4">

            <?php
            echo form_open('report/prereportpdf/cluster_date_stage', 
                array('id' => 'clust', 'target' => '_blank'));
            $fest['All'] = 'All Festivals'; ?>
<input type="hidden" name="hidUserId" id="hidUserId" />
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                    <h5 class="card-title">Cluster Report: <br>(Date & Stage wise)</h5>
                        <table>
                            <div class="row">
                                 <div class="col-12 mb-2">
                                    Date
                                </div>

                                 <div class="col-12 mb-2">
                                    <?php echo form_dropdown("txtDate", @$date_array, (@$date != '') ? $date : '', 'class="form-control" id="txtDate"'); ?>
                                </div>

                                <div class="col-12 mb-2">
                                    Stage
                                </div>

                                 <div class="col-12 mb-2">
                                    <div id="cmbitem">
                                        <?php echo form_dropdown("cmbstage", $stage, '', 'class="form-control" id="cmbstage" '); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                 <div class="col-4 mb-2">
                                    <?php echo form_submit('Report', 'Report', 'onClick="" class="btn btn-primary mb-1"'); ?>
                                </div>

                                 <div class="col-6 mb-2">
                                    <?php echo form_submit('Report_for_press', 'Report For Press', 'onClick="javascript: return cluster_doc_date_stage()" class="btn btn-primary mb-1"'); ?>
                                </div>
                            </div>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php 
echo form_close();
?>