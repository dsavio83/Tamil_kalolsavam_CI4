<br />
<?php
echo form_open('user/user_registration/userinsert', array('id' => 'editUser'));

?>

<section class="section">
      <div class="row justify-content-center">
        <div class="col-md-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Existing Users</h5>

              <!-- Default Table -->
              <table class="table text-center">
                <thead>
                  <tr>
                    <th scope="col">Sl.No.</th>
                    <th scope="col">User Name</th>
                    <th scope="col">Edit</th>
                    <th scope="col">Delete</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <?php
                        $i = 1;
                        foreach ($retvalue as $value) {
                            $class_name = ($i % 2 == 0) ? 'table_row_first' : 'table_row_second';
                    ?>
                    <td scope="row"class="<?php echo $class_name; ?>"><?php echo $i; ?></th>
                    <td class="<?php echo $class_name; ?>"><?php echo ($value['user_name']); ?></td>
                    <td class="<?php echo $class_name; ?>" ><a href="javascript:void(0)" onClick="javascript:editUser('<?php echo $value['user_id'] ?>')"> <img src="<?php echo base_url(false) ?>images/edit.png" border="0"> </a> </td>
                    <td  class="<?php echo $class_name; ?>"><a href="javascript:void(0)" onClick="javascript:deleteUser('<?php echo $value['user_id'] ?>')"> <img src="<?php echo base_url(false) ?>images/delete.png" border="0"> </a></td>
                  </tr>
                  </tbody>
              <?php $i++;} ?>
        <input type="hidden" name="UserIdty" id="UserIdty" value="">
    </table>
    <?php ?>
          </div>
      </div>
  </div>
</div>
</section>

    
    
