
<div class="pagetitle">
      <h1>Result</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Result Item Details</li>
        </ol>
      </nav>
</div>

<br />
<?php echo form_open('result/resultentry/item_result_list', array('id' => 'formIRL')); ?>
<input type="hidden" name="hidUservalue" id="hidUservalue"  value="SHOW" />
    <div class="row justify-content-center">
            <div class="col-md-12 col-sm-12 col-xl-12">
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title"> </h5>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                            Festival Type
                            </div>

                            <div class="col-md-12 text-center">
                            <?php echo form_dropdown("cmbFestType", $fest, $this->input->post('cmbFestType'), 'class="form-select mb-3" id="cmbFestType"'); ?>
                            </div><br>
                        </div>
                        
                        <div class="text-center">
                                <?php echo form_submit('GET', 'GET', 'class="btn btn-primary"'); ?>
                        </div>
                    </div>
                        
                </div>   

            </div>
    </div>



<?php
//itemwise_report_interface
echo form_close();
?>