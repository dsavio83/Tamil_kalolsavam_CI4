<div class="pagetitle">
      <h1>PreFest Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="<?php echo base_url(); ?> welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">School List</li>
        </ol>
      </nav>
</div>

<?php echo form_open('report/prereportpdf/list_school/', 
array('id' => 'formPWD', 'target' => '_blank')); ?>

<div class="row justify-content-center">
            <div class="col-md-12 col-sm-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title">Participating Schools</h5>
                        <div class="row">
                            <div class="col-md-12">
                             <label for="inputNanme" class="form-label mb-3">List  of Participating Schools with School Code</label> 
                            </div>
                            
                            <div class="col-md-12 text-center">
                            <select name="txtfestFrom" id="txtfestFrom" class="form-control selectpicker">
                                <?php
                                $festivals = array(
                                    $retdat[0]['fest_id'] => $retdat[0]['fest_name'],
                                    $retdat[1]['fest_id'] => $retdat[1]['fest_name'],
                                    $retdat[2]['fest_id'] => $retdat[2]['fest_name'],
                                    $retdat[3]['fest_id'] => $retdat[3]['fest_name']
                                );

                                foreach ($festivals as $fest_id => $fest_name) {
                                    echo '<option value="' . $fest_id . '">' . $fest_name . '</option>';
                                }
                                ?>
                            </select>


                            </div>
                            
                        </div>
                        
                        <div class="text-center">
                               <?php echo form_submit('Report', 'Report', 'onClick="javascript: return fnschgpwdAddrr()" class="btn btn-primary mt-3"'); ?>
                        </div>
                    </div>
                        
                </div>   

            </div>
    </div>

<?php
echo form_close();
?>
