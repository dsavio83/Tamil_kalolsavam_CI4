<?php
echo form_open('user/user_registration/userinsert', array('id' => 'editUser')); 
?>

<div class="mb-3">
    <a href="<?php echo base_url('user/user_cluster/add_form'); ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Cluster School
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <h5 class="card-title">Existing Users</h5>
        <tr class="text-center">
            <th align="center" class="table_row_first">Sl.No.</th>
            <th align="center" class="table_row_first">User Name</th>
            <th align="center" class="table_row_first">No of Schools</th>
            <th align="center" class="table_row_first">Edit</th>
            <th align="center" class="table_row_first">Delete</th>
        </tr>
        <?php
        $i = 1;
        foreach ($retvalue as $value) {
            $class_name = ($i % 2 == 0) ? 'table_row_first' : 'table_row_second';
        ?>
        <tr>
            <td align="center" class="<?php echo $class_name; ?>"><?php echo $i; ?></td>
            <td align="center" class="<?php echo $class_name; ?>"><?php echo ($value['user_name']); ?></td>
            <td align="center" class="<?php echo $class_name; ?>"><?php echo ($value['total']); ?></td>
            <td align="center" class="<?php echo $class_name; ?>">
                <a href="javascript:void(0)" onClick="javascript:editUser('<?php echo $value['user_id'] ?>')">
                    <img src="<?php echo base_url(false) ?>images/edit.png" border="0" width="16" height="16">
                </a>
            </td>
            <td align="center" class="<?php echo $class_name; ?>">
                <a href="javascript:void(0)" onClick="javascript:deleteUser('<?php echo $value['user_id'] ?>')">
                    <img src="<?php echo base_url(false) ?>images/delete.png" border="0" width="16" height="16">
                </a>
            </td>
        </tr>
        <?php $i++; } ?>
        <input type="hidden" name="UserIdty" id="UserIdty" value="">
    </table>
</div>
<?php echo form_close(); ?>
