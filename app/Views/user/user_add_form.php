<div class="pagetitle">
    <h1>Add New User</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>user/user_registration" title="User Registration">User Registration</a></li>
            <li class="breadcrumb-item active">Add New User</li>
        </ol>
    </nav>
</div>
<br/>
<div style="text-align: left; margin-bottom: 10px;">
    <a href="<?php echo base_url(); ?>user/user_registration" class="btn btn-secondary">
        <i class="bi bi-arrow-left-circle"></i> Back to List
    </a>
</div>

<h5 class="card-title">Sub-District Admin User Creation</h5>
<?php echo form_open('user/user_registration/userinsert', array('id' => 'formUser')); ?>
<table>
    <?php if(!empty($user_rights)){
        if (count(@$user_rights) > 0) { ?>
        <tr>
            <div class="row">
                <div class="col-xl-4 col-md-4 col-sm-12 col-lg-12 mb-2">
                    User Name
                </div>
                <div class="col-xl-8 col-md-8 col-sm-12 col-lg-12 mb-2">
                    <?php echo form_input("txtNewUserName", '', 'class="form-control" id="txtNewUserName"'); ?>
                </div>

                <div class="col-xl-4 col-md-4 col-sm-12 col-lg-12 mb-2">
                    Password
                </div>
                <div class="col-xl-8 col-md-8 col-sm-12 col-lg-12 mb-2">
                    <?php echo form_password("txtNewPassword", '', 'class="form-control" id="txtNewPassword"'); ?>
                </div>
            </div>
        </tr>

        <tr>
            <h5 class="card-title"> User Rights</h5>
        </tr>

        <tr>
            <div class="row">
                <div class="col-xl-2"> &nbsp</div>
                <div class="col-xl-6 col-md-6 col-sm-12 col-lg-12">
                    <?php
                    $functionality_label = '';
                    for ($i = 0; $i < count($user_rights); $i++) {
                        if ($functionality_label != $user_rights[$i]['label_name']) {
                            $functionality_label = $user_rights[$i]['label_name'];
                            ?>
                            <div class="clear_both"></div>
                            <div class="functionality_label">
                                <span class="text-primary">
                                    <b><?php echo $user_rights[$i]['label_name'] ?></b>
                                </span>
                            </div>
                            <?php  }

                        $data = array(
                            'name' => 'chkRight_' . $user_rights[$i]['rf_id'],
                            'id' => 'chkRight_' . $user_rights[$i]['rf_id'],
                            'value' => $user_rights[$i]['rf_id'],
                            'style' => 'margin:5px',
                        );
                        ?>
                        <div class="clear_both"></div>
                        <div class="functionalities">
                            <?php
                            echo form_checkbox($data);
                            echo form_label($user_rights[$i]['rf_functionality'], 'chkRight_' . $user_rights[$i]['rf_id']) . '<br>';
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="col-xl-4">&nbsp;</div>
            </div>
        </tr>
        <?php }} ?>
        <tr>
            <div class="col-12 text-center">
                <?php echo form_submit('Add User', 'Add User', 'onClick="javascript: return fnsUserAdd()" class="btn btn-primary mt-3"'); ?>
                <?php echo form_button('Cancel', 'Cancel', 'onClick="javascript: return cancel()" class="btn btn-danger mt-3"'); ?>
            </div>
        </tr>
</table>
<input type="hidden" name="hidUserId" id="hidUserId" />
<?php echo form_close(); ?>

<script>
function fnsUserAdd() {
    // Validate form before submission
    var userName = document.getElementById('txtNewUserName').value;
    var password = document.getElementById('txtNewPassword').value;

    if(userName.trim() === '') {
        alert('Please enter a user name');
        return false;
    }

    if(password.trim() === '') {
        alert('Please enter a password');
        return false;
    }

    return true;
}

function cancel() {
    // Go back to list
    window.location.href = '<?php echo base_url(); ?>user/user_registration';
    return false;
}
</script>
