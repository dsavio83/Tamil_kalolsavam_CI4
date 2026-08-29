<?php
echo form_open('user/user_cluster/save_user_cluster', array('id' => 'formUser'));
?>

<div class="mb-3">
    <a href="<?php echo base_url('user/user_cluster'); ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left-circle"></i> Back to List
    </a>
</div>

<h5 class="card-title">User Creation</h5>

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
        <div class="position-relative">
            <?php echo form_password("txtNewPassword", '', 'class="form-control" id="txtNewPassword"'); ?>
            <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-2" onclick="togglePassword()" style="border: none; background: none; padding: 0;">
                <i class="bi bi-eye-slash" id="passwordToggleIcon"></i>
            </button>
        </div>
        <?php echo (@$selected_cluster[0]['user_name']) ? "<br>( Leave password field blank, if you don't want to change the password. )" : ''; ?>
    </div>
    <div class="col-xl-2 col-md-2"></div>
</div>

<div class="row justify-content-center mb-3">
    <div class="col-xl-12 col-md-12 col-sm-12 col-lg-12 text-center">
        <?php 
        if (@$selected_cluster[0]['user_id'] != '') {
            echo form_button('Update Cluster', '<i class="bi bi-check-circle"></i> Update Cluster', 'onClick="javascript: return fncUpdateCluster(\'' . @$selected_cluster[0]['user_id'] . '\')" class="btn btn-primary"');
            echo '&nbsp;';
            echo form_button('Cancel', '<i class="bi bi-x-circle"></i> Cancel', 'onClick="javascript: return cancel_cluster()" class="btn btn-danger"');
        } else {
            echo form_button([
				'name'    => 'add_cluster',
				'content' => '<i class="bi bi-plus-circle"></i> Add Cluster',
				'class'   => 'btn btn-primary',
				'type'    => 'submit',
				'onClick' => 'javascript: return fncAddCluster()',
				'escape'  => false  // ✅ allows HTML inside content
			]);
        }
        ?>
    </div>
</div>

<hr>

<div class="table-responsive">
    <table class="table table-sm">
        <?php if (count(@$schools) > 0 || count(@$selected_cluster_schools) > 0) { ?>
        <h5 class="card-title">Schools</h5>
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
                <?php } 
                echo '<div class="">' . form_checkbox($data);
                echo form_label($selected_cluster_schools[$j]['school_code'] . ' - ' . 
                    $selected_cluster_schools[$j]['school_name'], 
                    $selected_cluster_schools[$j]['school_code']) . '</div>';
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
                $newTR++;
                }
                ?>
            </div>
        </div>
        <?php } ?>
    </table>
</div>

<input type="hidden" name="hidUserId" id="hidUserId" />
<?php echo form_close(); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all checkboxes in the form
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    const usernameField = document.getElementById('txtNewUserName');
    const passwordField = document.getElementById('txtNewPassword');

    // Add event listener to each checkbox
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                // When checkbox is checked, set username and password to school code
                const schoolCode = this.value;
                usernameField.value = schoolCode;
                passwordField.value = schoolCode;
            } else {
                // When checkbox is unchecked, clear username and password
                usernameField.value = '';
                passwordField.value = '';
            }
        });
    });
});

// Password visibility toggle function
function togglePassword() {
    const passwordField = document.getElementById('txtNewPassword');
    const toggleIcon = document.getElementById('passwordToggleIcon');

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    }
}
</script>
