<?php
echo form_open('report/prereportpdf/appealed_part', array('id' => 'formIWP', 'target' => '_blank'));

?>

<div class="pagetitle">
      <h1>PreFest Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="<?php echo base_url(); ?> welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Appeal(Festival wise)</li>
        </ol>
      </nav>
</div>
<div class="row justify-content-center">
    <div class="col-8">
        <div class="card">
            <div class="card-body">
                    <h5 class="card-title">List of Participants by Appeal(Festival wise)</h5>
                        <table width="100%" border="0" cellspacing="0" cellpadding="4" align="center">
                            <div class="row">
                                <div class="col-3">
                                    Festival Type
                                </div>

                                <div class="col-9">
                                    <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-control" id="cmbFestType" onchange="javascript:fetch_callsheet_festival(this.value)"'); ?>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <center>
                                     <?php echo form_submit('Report', 'Report', 'onClick="javascript: return callsheet()" class="btn btn-primary"'); ?>
                                </center>
                                
                            </div>
                            
                        </table>
                    </div>
                </div>
            </div>
        </div>

<?php
echo form_close();
?>