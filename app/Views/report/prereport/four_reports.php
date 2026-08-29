<div class="pagetitle">
      <h1>Stage Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Four Reports in one Place</li>
        </ol>
      </nav>
</div>

<nav>
    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
        <a class="nav-item nav-link" data-toggle="tab" href="<?php echo base_url(); ?>report/prereport/callsheet_first" title="Call sheet" role="tab" aria-controls="nav-home" aria-selected="true">Call Sheet</a>
        
        <a class="nav-item nav-link" data-toggle="tab" href="<?php echo base_url(); ?>report/prereport/timesheetinterface" title="Timesheet - Item Wise" role="tab" aria-controls="nav-profile" aria-selected="false">Time Sheet</a>
        
        <a class="nav-item nav-link" data-toggle="tab" 
        href="<?php echo base_url(); ?>report/prereport/score_sheet_interfaces" title="score sheet" role="tab" aria-controls="nav-contact" aria-selected="false">Score Sheet</a>
        
        <a class="nav-item nav-link" data-toggle="tab" href="<?php echo base_url(); ?>report/prereport/tabulation_report_interface" title="Tabulation sheet" role="tab" aria-controls="nav-about" aria-selected="false">Tabulation Sheet</a>
        
        <a class="nav-item nav-link active" data-toggle="tab" href="<?php echo base_url(); ?>report/prereport/four_reports" title="Four_Reports" role="tab" aria-controls="nav-about" aria-selected="false">All Report in one</a>
    </div>
</nav>
<br/>

<?php
echo form_open('report/prereportpdf/four_reports', array('id' => 'formIWP', 'target' => '_blank'));

?>
<div class="row justify-content-center">
            <div class="col-md-12 col-sm-12 col-xl-12">
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title">Select Item</h5>
                        <div class="row">
                            <div class="col-12">
                                Enter Item Code 
                                    <?php echo form_input("item", '', 'class="form-control" id="item" onkeypress="javascript:return numbersonly(this, event, false);" '); ?>
                            </div>

            <div class="col-12 mt-3">
            <div class="row">
                

            <form name="form1" method="post" action="">

                <div class="col mb-2">
                    <input type="submit" class="btn btn-primary" name="call" id="call" value="Call Sheet" onClick="">
                </div>

                <div class="col mb-2">
                    <input type="submit" class="btn btn-primary" name="time" id="time" value="Time Sheet" onClick="">
                </div>

                <div class="col mb-2">
                    <input type="submit" class="btn btn-primary" name="tabulation" id="tabulation" value="Tabulation Sheet" onClick="">
                    </div>

                <div class="col mb-2">    
                    <input type="submit" class="btn btn-primary" name="score" id="score" value="Score Sheet" onClick="">
                    </div>

                <div class="col mb-2">
                    <input type="submit" class="btn btn-primary" name="participants" id="participants" value="Participant List" onClick="">
                </div>
                
            </form>
        </div>
    </div>
</div>
</div>
</div>
</div>
</div>


<input type="hidden" name="hidUserId" id="hidUserId" />

<?php echo form_close(); ?>