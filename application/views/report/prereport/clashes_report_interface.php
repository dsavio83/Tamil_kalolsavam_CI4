<div class="pagetitle">
      <h1>PreFest Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="<?php echo base_url(); ?> welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Clash Report</li>
        </ol>
      </nav>
</div>

<?php
echo form_open('report/prereportpdf/clashes_details', array('id' => 'formIWP', 'target' => '_blank'));
?>

<form name="form1" method="post" action="">
    <div class="row justify-content-center">
        <div class="col-md-12 col-sm-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Select Festival</h5>
                    <table width="100%" border="0" cellspacing="0" cellpadding="4" align="center">
                        <tr>
                            <div class="row">
                                 <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                    Festival Type
                                </div>

                                 <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
                                    <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-control" id="cmbFestType"'); ?>
                                </div>

                                <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                    Date
                                </div>

                                 <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
                                    <?php echo form_dropdown('txtDate', $date_array, '', 'id="txtDate" class="form-control"'); ?>
                                </div>
                            </div>

                            <div class="row">
                                <center>
                                 <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                    <input type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary mt-3" value="View report" onclick="javascript: return clureport()">
                                </div>
                            </center>
                            </div>
            
 </form>
        
    </table>
    <input type="hidden" name="hidUserId" id="hidUserId" />

    <?php
//itemwise_report_interface
    echo form_close();
    ?>