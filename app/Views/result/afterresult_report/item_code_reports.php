<div class="pagetitle">
    <h1>Result Report</h1>
    <nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
        <li class="breadcrumb-item active">Item Codewise Report</li>
    </ol>
    </nav>
</div>

<?php
echo form_open('report/afterresultreportpdf/item_code_reports', array('id' => 'formIWP', 'target' => '_blank'));

?>
<div class="row">
    <div class="col-12">
        <h4 class="card-title">Select Item</h4>
    </div>
</div>

<div class="row">
    <div class="col-12">
        Enter Item Code
    </div>
    <div class="col-12 mt-2">
        <?php echo form_input("item", '', 'class="form-control" id="item" onkeypress="javascript:return numbersonly(this, event, false);" '); ?>
    </div>
</div>

<div class="row text-center mb-3">
    <div class="col-12 mt-1">
        <form name="form1" method="post" action="">
                <label>
                    <input type="submit" name="report" class="btn btn-primary mt-4" id="report" value="Report" onClick="">
                </label>
            </form>
    </div>
</div>
<input type="hidden" name="hidUserId" id="hidUserId" />

<?php
//itemwise_report_interface
echo form_close();

?>
