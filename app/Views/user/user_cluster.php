<?php
echo form_open('user/user_cluster/save_user_cluster', array('id' => 'formUser'));
 
?>

<h5 class="card-title"> User Creation</h5>

    <div class="row justify-content-center mb-3">
        <div class="col-xl-2 col-md-2"></div>
        <div class="col-xl-2 col-md-2 col-sm-12 col-lg-12">
            User Name :
        </div>
        
        <div class="col-xl-6 col-md-6 col-sm-12 col-lg-12">
            <?php echo form_input("txtNewUserName", @$selected_cluster[0]['user_name'], 'class="form-control" id="txtNewUserName"'); ?>
        </div>
        <div class="col-xl-2 col-md-2"></div>
    </div>
    
    <div class="row justify-content-center mb-3">
         <div class="col-xl-2 col-md-2"></div>
        <div class="col-xl-2 col-md-2 col-sm-12 col-lg-12">
            Password :
        </div>
    
         <div class="col-xl-6 col-md-6 col-sm-12 col-lg-12">
            <?php echo form_password("txtNewPassword", '', 'class="form-control" id="txtNewPassword"'); ?>
            <?php echo (@$selected_cluster[0]['user_name']) ? "<br>( Leave password field blank, if you don't want to change the password. )" : ''; ?>
        </div>
         <div class="col-xl-2 col-md-2"></div>
    </div>
        
    <div class="row justify-content-center mb-3">
        <div class="col-xl-12 col-md-12 col-sm-12 col-lg-12 text-center">
            <?php echo (@$selected_cluster[0]['user_id'] != '') ? form_button('Update Cluster', 'Update Cluster', 'onClick="javascript: return fncUpdateCluster(\'' . @$selected_cluster[0]['user_id'] . '\')" class="btn btn-primary"') . '&nbsp;' . form_button('Cancel', 'Cancel', 'onClick="javascript: return cancel_cluster()"class="btn btn-danger"') : form_submit('Add Cluster', 'Add Cluster', 'onClick="javascript: return fncAddCluster()" class="btn btn-primary"'); ?> 
        </div>
    </div>
    
  <hr>
  
    <div class="table-responsive">
    <table class="table table-sm">
    
    <?php if (count(@$schools) > 0 || count(@$selected_cluster_schools) > 0) { ?>
    
        <h5 class="card-title"> Schools </h5>
            <div class="row">
            <div class="col-xl-6 col-md-6 col-sm-12 col-lg-12">
            
            
             
                        <?php
                        $newTR = 0;
                        for ($j = 0; $j < count(@$selected_cluster_schools); $j++) {

                            $data = array();
                            if (in_array($selected_cluster_schools[$j]['school_code'], $entered_school)) {
                                $data = array(
                                    'name' => $selected_cluster_schools[$j]['school_code'] . 'chk',
                                    'id' => $selected_cluster_schools[$j]['school_code'] . 'chk',
                                    'value' => $selected_cluster_schools[$j]['school_code'],
                                    'disabled' => 'TRUE',
                                    'checked' => 'TRUE',
                                );
                                ?>
                            <input type="hidden" name="<?php echo $selected_cluster_schools[$j]['school_code']; ?>" 
                            id="<?php echo $selected_cluster_schools[$j]['school_code']; ?>" 
                            value="<?php echo $selected_cluster_schools[$j]['school_code']; ?>" />
                            <?php
                        } else {
                            $data = array(
                                'name' => $selected_cluster_schools[$j]['school_code'],
                                'id' => $selected_cluster_schools[$j]['school_code'],
                                'value' => $selected_cluster_schools[$j]['school_code'],
                                'checked' => 'TRUE',
                            );
                        }
                        if ($newTR % 2 == 0) {
                            ?>
            </div>
           <div class="col-xl-6 col-md-6 col-sm-12 col-lg-12">

                    <?php  }

                echo '<div class="">' . form_checkbox($data);
                echo form_label($selected_cluster_schools[$j]['school_code'] . ' - ' . 
                $selected_cluster_schools[$j]['school_name'], 
                $selected_cluster_schools[$j]['school_code']) . '</div>';
                ?>
                
                <?php
                $newTR++;
            }


            for ($i = 0; $i < count(@$schools); $i++) {
                if (count(@$selected_schools) > 0 && @$selected_schools[0] != 0) {
                    $checked = (in_array($user_rights[$i]['rf_id'], @$selected_user_rights)) ? 'TRUE' : '';
                } else {
                    $checked = '';
                }
                $data = array(
                    'name' => $schools[$i]['school_code'],
                    'id' => $schools[$i]['school_code'],
                    'value' => $schools[$i]['school_code'],
                    'checked' => $checked,
                );
                if ($newTR % 2 == 0) {
                    ?>
                </div>
              <div class="col-xl-6 col-md-6 col-sm-12 col-lg-12">

                    <?php }

                echo '<div class="">' . form_checkbox($data);
                echo form_label($schools[$i]['school_code'] . ' - ' . 
                $schools[$i]['school_name'], 
                $schools[$i]['school_code']) . '</div>';
                ?>
               
                <?php
                $newTR++;
            }
            ?>
            
         </div>
<?php } ?>

</table>
</div>
<input type="hidden" name="hidUserId" id="hidUserId" />
<?php
echo form_close();
 
?>
