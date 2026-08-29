<div class="pagetitle">
      <h1>Stage</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Define Stages</li>
        </ol>
      </nav>
</div>
 
<?php
echo form_open('stage/stage_details/add_stage', array('id' => 'formStage'));
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-xl-6 col-sm-12 col-lg-12">
        <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo (@$selected_stage[0]['stage_id'] != '') ? 'Edit Stage' : 'Add Stage'; ?></h5>

                  <div class="row">
                    
                        <div class="col-xl-3 col-md-12 col-sm-12 col-lg-12">
                          <label for="inputNanme" class="form-label">Stage Name</label>
                        </div>
                        <div class="col-xl-9 col-md-12 col-sm-12 col-lg-12">
                            <?php echo form_dropdown("txtStageName", @$stage_array, @$selected_stage[0]['stage_name'], 'class="form-control" id="txtStageName"'); ?>
                        </div>
                    </div>

                    <div class="row mt-3">
                        
                        <div class="col-xl-3 col-md-12 col-sm-12 col-lg-12">
                          <label for="inputNanme" class="form-label">Discription</label>
                          
                        </div>
                        <div class="col-xl-9 col-md-12 col-sm-12 col-lg-12">
                            <?php echo form_input("txtStageDescription", @$selected_stage[0]['stage_desc'], 'class="form-control" id="txtStageDescription"'); ?>
                        </div>
                    </div>

                    <br>
                    <div class="text-center">
                         <?php echo (@$selected_stage[0]['stage_id'] != '') ? form_button('Update', 'Update', 'onClick="javascript: return fnsUpdateStage(\'' . @$selected_stage[0]['stage_id'] . '\')" class="btn btn-primary"') . '&nbsp;' . form_button('Cancel', 'Cancel', 'onClick="javascript: return cancel()" class="btn btn-danger"') : form_submit('Add Stage', 'Add Stage', 'onClick="javascript: return fnsAddStage()" class="btn btn-primary" '); ?>
                    </div>
                </div>
            </div>
    </div>


<input type="hidden" name="hidStId" id="hidStId" />
<?php
echo form_close();
?>

