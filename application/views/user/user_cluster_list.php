<div class="pagetitle">
    <h1>Cluster Registration</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>user/user_registration" title="User Registration">User Registration</a></li>
            <li class="breadcrumb-item active">Cluster Registration</li>
        </ol>
    </nav>
</div>
<br/>
<div style="text-align: left; margin-bottom: 10px;">
    <a href="<?php echo base_url(); ?>user/user_registration/add_cluster" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Cluster
    </a>
</div>

<!-- Cluster Users List Section (Default View) -->
<div id="clusterListSection">
    <table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
        <tr>
            <th colspan="5" align="left">Existing Cluster Users</th>
        </tr>
        <tr>
            <th align="center" width="8%" class="table_row_first">Sl.No.</th>
            <th align="left" width="35%" class="table_row_first">User Name</th>
            <th align="left" width="15%" class="table_row_first">User Type</th>
            <th align="left" width="27%" class="table_row_first">Cluster Schools</th>
            <th align="center" width="7.5%" class="table_row_first">Edit</th>
            <th align="center" width="7.5%" class="table_row_first">Delete</th>
        </tr>

        <?php
        $i = 1;

        if(!empty($retvalue)) {
            foreach ($retvalue as $value) {
                $class_name = ($i % 2 == 0) ? 'table_row_first' : 'table_row_second';

                // User type is always Cluster Admin for this page
                $user_type = 'Cluster Admin';
                ?>
                <tr>
                    <td align="center" class="<?php echo $class_name; ?>"><?php echo $i; ?></td>
                    <td align="left" class="<?php echo $class_name; ?>"><?php echo ($value['user_name']); ?></td>
                    <td align="left" class="<?php echo $class_name; ?>"><?php echo $user_type ?></td>
                    <td align="left" class="<?php echo $class_name; ?>">
                        <?php if($value['cluster_school_count'] > 0) { ?>
                            <span title="<?php echo htmlspecialchars($value['cluster_school_names']); ?>">
                                <?php echo $value['cluster_school_count']; ?> schools
                            </span>
                        <?php } else { ?>
                            <span style="color: #999;">No schools assigned</span>
                        <?php } ?>
                    </td>
                    <td align="center" class="<?php echo $class_name; ?>">
                        <a href="javascript:void(0)" onclick="editCluster('<?php echo $value['user_id'] ?>')">
                            <img src="<?php echo base_url(false) ?>images/edit.png" border="0" width="18" height="20">
                        </a>
                    </td>
                    <td align="center" class="<?php echo $class_name; ?>">
                        <a href="javascript:void(0)" onclick="deleteCluster('<?php echo $value['user_id'] ?>', '<?php echo $user_type ?>', '<?php echo ($value['user_name']); ?>')">
                            <img src="<?php echo base_url(false) ?>images/delete.png" border="0" width="17" height="20">
                        </a>
                    </td>
                </tr>
                <?php $i++;
            }
        } else { ?>
            <tr>
                <td colspan="6" align="center" class="table_row_first">No cluster users found</td>
            </tr>
        <?php } ?>
    </table>
</div>

<!-- Cluster Creation Form Section (Hidden by default) -->
<div id="clusterFormSection" style="display: none;">
    <?php
    echo form_open('user/user_registration/clusterinsert', array('id' => 'formCluster'));
    ?>
    <div style="text-align: left; margin-bottom: 10px;">
        <button type="button" class="btn btn-secondary" onclick="showClusterList()">
            <i class="bi bi-arrow-left-circle"></i> Back to List
        </button>
    </div>

    <h5 class="card-title">Cluster User Creation</h5>
    <table>
        <?php if(!empty($user_rights)){
            if (count(@$user_rights) > 0) { ?>
            <tr>
                <div class="row">
                    <div class="col-xl-4 col-md-4 col-sm-12 col-lg-12 mb-2">
                        User Name
                    </div>
                    <div class="col-xl-8 col-md-8 col-sm-12 col-lg-12 mb-2">
                        <?php echo form_input("txtNewUserName", @$selected_user[0]['user_name'], 'class="form-control" id="txtNewUserName"'); ?>
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

                            if(!empty($selected_user_rights)){
                                if (count(@$selected_user_rights) > 0 && @$selected_user_rights[0] != 0) {
                                    $checked = (in_array($user_rights[$i]['rf_id'], @$selected_user_rights)) ? 'TRUE' : '';
                                } else {
                                    $checked = '';
                                }
                            } else {
                                $checked = '';
                            }

                            $data = array(
                                'name' => 'chkRight_' . $user_rights[$i]['rf_id'],
                                'id' => 'chkRight_' . $user_rights[$i]['rf_id'],
                                'value' => $user_rights[$i]['rf_id'],
                                'checked' => $checked,
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
                    <?php echo (@$selected_user[0]['user_id'] != '') ? form_button('Update Cluster', 'Update Cluster', 'onClick="javascript: return fnsClusterUpdate(\'' . @$selected_user[0]['user_id'] . '\')" class="btn btn-primary"') . '&nbsp;' . form_button('Cancel', 'Cancel', 'onClick="javascript: return cancel()" class="btn btn-danger"') : form_submit('Add Cluster', 'Add Cluster', 'onClick="javascript: return fnsClusterAdd()" class="btn btn-primary mt-3"'); ?>
                </div>
            </tr>
    </table>
    <input type="hidden" name="hidUserId" id="hidUserId" />
    <?php
    echo form_close();
    ?>
</div>

<script>
function showClusterForm() {
    document.getElementById('clusterListSection').style.display = 'none';
    document.getElementById('clusterFormSection').style.display = 'block';
}

function showClusterList() {
    document.getElementById('clusterFormSection').style.display = 'none';
    document.getElementById('clusterListSection').style.display = 'block';
}

function editCluster(userId) {
    // Create a hidden form to submit the edit request
    var editForm = document.createElement('form');
    editForm.method = 'POST';
    editForm.action = '<?php echo base_url(); ?>user/user_registration/edit_cluster_details';

    var hiddenField = document.createElement('input');
    hiddenField.type = 'hidden';
    hiddenField.name = 'UserIdty';
    hiddenField.value = userId;

    editForm.appendChild(hiddenField);
    document.body.appendChild(editForm);
    editForm.submit();
}

function deleteCluster(userId, userType, userName) {
    if(confirm('Are you sure you want to delete cluster user: ' + userName + '?')) {
        // Create a hidden form to submit the delete request
        var deleteForm = document.createElement('form');
        deleteForm.method = 'POST';
        deleteForm.action = '<?php echo base_url(); ?>user/user_registration/delete_cluster_details';

        var hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = 'UserIdty';
        hiddenField.value = userId;

        deleteForm.appendChild(hiddenField);
        document.body.appendChild(deleteForm);
        deleteForm.submit();
    }
}

function fnsClusterAdd() {
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

function fnsClusterUpdate(userId) {
    // Validate form before update
    var userName = document.getElementById('txtNewUserName').value;

    if(userName.trim() === '') {
        alert('Please enter a user name');
        return false;
    }

    return true;
}

function cancel() {
    // Clear form and go back to list
    document.getElementById('formCluster').reset();
    document.getElementById('hidUserId').value = '';
    showClusterList();
    return false;
}
</script>
