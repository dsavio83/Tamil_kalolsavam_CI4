<div class="pagetitle">
      <h1>Stage Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Tabulation Sheet</li>
        </ol>
      </nav>
</div>

<nav>
    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
        <a class="nav-item nav-link" data-toggle="tab" href="<?php echo base_url(); ?>report/prereport/callsheet_first" title="Call sheet" role="tab" aria-controls="nav-home" aria-selected="true">Call Sheet</a>
        
        <a class="nav-item nav-link" data-toggle="tab" href="<?php echo base_url(); ?>report/prereport/timesheetinterface" title="Timesheet - Item Wise" role="tab" aria-controls="nav-profile" aria-selected="false">Time Sheet</a>
        
        <a class="nav-item nav-link" data-toggle="tab" 
        href="<?php echo base_url(); ?>report/prereport/score_sheet_interfaces" title="score sheet" role="tab" aria-controls="nav-contact" aria-selected="false">Score Sheet</a>
        
        <a class="nav-item nav-link active" data-toggle="tab" href="<?php echo base_url(); ?>report/prereport/tabulation_report_interface" title="Tabulation sheet" role="tab" aria-controls="nav-about" aria-selected="false">Tabulation Sheet</a>
        
        <a class="nav-item nav-link" data-toggle="tab" href="<?php echo base_url(); ?>report/prereport/four_reports" title="Four_Reports" role="tab" aria-controls="nav-about" aria-selected="false">All Report in one</a>
    </div>
</nav>
<br/>

<div class="row">
  <div class="col-xl-4 col-sm-12 col-md-4 col-lg-12">
<?php
echo form_open('report/prereport/rpt_tabulation_sheet1', 
array('id' => 'formIWP', 'target' => '_blank')); ?>

    <div class="card">
      <div class="card-body">
          <h5 class="card-title">Tabulation Sheet <br>(Festival Wise)</h5>
          <div class="row">
            <div class="col-4">
              Festival Type
            </div>
            <div class="col-8">
                    <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-control" id="cmbFestType"'); ?>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
                <form name="form1" method="post" action="">
                    <center>
                        <input type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary mt-3" value="View report" onclick="javascript:return notSelected()">
                    </center>
                    <br/><br/><br/><br/><br/>
                </form>
                       
                <input type="hidden" name="hidUserId" id="hidUserId" />
            </div>
          </div>
      </div>
    </div>
  </div>


<input type="hidden" name="hidUserId" id="hidUserId" />
<?php echo form_close(); ?>