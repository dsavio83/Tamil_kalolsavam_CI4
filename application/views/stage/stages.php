 <div class="col-lg-12 col-md-6 col-xl-6 col-sm-12">
<?php
echo form_open('stage/stage_details/add_stage', array('id' => 'editStage'));
?>
    
     
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Stages</h5>

              <!-- Default Table -->
              <table class="table">
                <thead>
                  <tr>        
                    <th scope="col">#</th>
                    <th scope="col">Stage Name</th>
                    <th scope="col">Stage description</th>
                    <th scope="col">Edit</th>
                    <th scope="col">Delete</th>
                  </tr>
                </thead>
                <tbody>
                    <?php
                        $i = 1;
                        foreach ($stages as $value) {
                            $class_name = ($i % 2 == 0) ? 'table_row_first' : 'table_row_second';
                    ?>
                  <tr>
                    <th scope="row" class="<?php echo $class_name; ?>"><?php echo $i; ?></th>
                    <td class="<?php echo $class_name; ?>"><?php echo ($value['stage_name']); ?></td>
                    <td class="<?php echo $class_name; ?>"><?php echo ($value['stage_desc']); ?></td>
                    <td class="<?php echo $class_name; ?>" ><a href="javascript:void(0)" onClick="javascript:editStage('<?php echo $value['stage_id'] ?>')"> <img src="<?php echo base_url(false) ?>images/edit.png" border="0"></a> </td>
                    <td class="<?php echo $class_name; ?>"><a href="javascript:void(0)" onClick="javascript:deleteStage('<?php echo $value['stage_id'] ?>')"> <img src="<?php echo base_url(false) ?>images/delete.png" border="0"> </a> </td>
                  </tr>
                </tbody>
              <?php $i++;} ?>
              </table>
        <input type="hidden" name="hidStageId" id="hidStageId" value="">
        
               <?php ?>
      </div>
    </div>
  </div>
  </div>




    
    
        
   
