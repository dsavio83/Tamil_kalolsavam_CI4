<div class="pagetitle"> 
      <h1>Stage Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Score Sheet</li>
        </ol>
      </nav>
</div> 

<nav>
    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
        <a class="nav-item nav-link" data-toggle="tab" href="<?php echo base_url(); ?>report/prereport/callsheet_first" title="Call sheet" role="tab" aria-controls="nav-home" aria-selected="true">Call Sheet</a>
        
        <a class="nav-item nav-link" data-toggle="tab" href="<?php echo base_url(); ?>report/prereport/timesheetinterface" title="Timesheet - Item Wise" role="tab" aria-controls="nav-profile" aria-selected="false">Time Sheet</a>
        
        <a class="nav-item nav-link active" data-toggle="tab" 
        href="<?php echo base_url(); ?>report/prereport/score_sheet_interfaces" title="score sheet" role="tab" aria-controls="nav-contact" aria-selected="false">Score Sheet</a>
        
        <a class="nav-item nav-link" data-toggle="tab" href="<?php echo base_url(); ?>report/prereport/tabulation_report_interface" title="Tabulation sheet" role="tab" aria-controls="nav-about" aria-selected="false">Tabulation Sheet</a>
        
        <a class="nav-item nav-link" data-toggle="tab" href="<?php echo base_url(); ?>report/prereport/four_reports" title="Four_Reports" role="tab" aria-controls="nav-about" aria-selected="false">All Report in one</a>
    </div>
</nav>
<br/>

<div class="row">
<div class="col-xl-4 col-sm-12 col-md-4 col-lg-12">
<?php
    echo form_open('report/prereportpdf/print_score_sheet', 
    array('id' => 'formscore', 'target' => '_blank')); ?>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
              <h5 class="card-title">Score Sheet - Item wise</h5>
                  <div class="row">
                        <div class="col-6">
                            Festival Type

                            <?php echo form_dropdown("cmbFestType", $fest, '', 
                            'class="form-control" id="cmbFestType" 
                            onchange="javascript:fetch_item_from_festival(this.value)"'); ?>
                        </div>

                        <div class="col-6">
                            Item Name 

                            <div id="cmbitem">
                                <?php echo form_dropdown("cbo_item", array('select'), '', 
                                'class="form-control" id="cbo_item" '); ?>
                            </div>
                        </div>

                        <div class="col-12">
                            <form name="form1" method="post" action="">
                                <center>
                                    <input type="submit" name="btnSubmit" id="btnSubmit" value="View report" class="btn btn-primary mt-3" onclick="javascript: return callsheetfirst()">
                                </center>
                            </form>
                                   
                            <input type="hidden" name="hidUserId" id="hidUserId" />
                        </div>
                  </div>
          </div>
        </div>
      </div>
    </div>
</div>



<?php echo form_close(); ?>