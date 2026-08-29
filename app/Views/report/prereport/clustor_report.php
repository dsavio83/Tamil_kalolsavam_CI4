<div class="pagetitle">
      <h1>PreFest Report</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="<?php echo base_url(); ?> welcome" title="Home">Home</a></li>
      <li class="breadcrumb-item active">Cluster Report</li>
    </ol>
  </nav>
</div>

<div class="row">
<div class="col-md-12 col-sm-12 col-xl-4">
<?php
echo form_open('report/prereportpdf/clusterreport_report', 
array('id' => 'clu', 'target' => '_blank')); ?>
<input type="hidden" name="hidUserId" id="hidUserId" />
<div class="row justify-content-center">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                    <h5 class="card-title">Cluster Report:<br> (Item wise)</h5>
                <table width="100%" border="0" cellspacing="0" cellpadding="4" align="center">
                            <!-- content start -->
                            <div class="row">
                                 <div class="col-12 mb-2">
                                    Festival Type
                                </div>

                                 <div class="col-12 mb-2">
                                    <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-control" id="cmbFestType" onchange="javascript:fetch_callsheet_festival(this.value)"'); ?>
                                </div>

                                <div class="col-12 mb-2">
                                    Item Name
                                </div>

                                 <div class="col-12 mb-2">
                                    <div id="cmbitem">
                                    <?php echo form_dropdown("cbo_item", array('select'), '', 'class="form-control" id="cbo_item" '); ?>
                                    </div>
                                </div>
                            </div>
                             <!-- content end -->

                            <!-- button start -->
                            <div class="row">
                                <div class="col-12">
                                     <?php echo form_submit('Report', 'Report', 'onClick="javascript: return clureport()" class="btn btn-primary mt-3"'); ?>
                                 </div>
                            </div>
                            <!-- button end -->
                      </form>
                </table>
            </div>
        </div>
    </div>
</div>
</div>


<?php
echo form_close();
?>