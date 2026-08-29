<div class="pagetitle">
      <h1>Export</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="<?php echo base_url(); ?> welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Export Result Data</li>
        </ol>
      </nav>
</div>

<?php
echo form_open('', array('id' => 'export_dist_data'));
?>

<div class="row justify-content-center">
            <div class="col-md-12 col-sm-6 col-xl-6">
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title">Participating Schools</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <center>
                                Export Result Data for District Kalolsavam
                            </div>
                    </div>
                    <center>
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <input type="hidden" name="hidExport" id="hidExport" />
                            <?php print(form_button('data_export', 'Export Data', 'onClick="javascript:fncExportDistrictData();return false;" class="btn btn-primary mt-3"')); ?>
                        </div>
                    
                        <div class="col-md-6">
                            <?php print(form_button('export_photo', 'Export Photo', 'onClick="javascript:fnc_export_photo();return false;" class="btn btn-primary mt-3"')); ?>
                    </div>
                    </div>
                </center>
                </div>
            </div>
        </div>
    </div>


<?php echo form_close(); ?>