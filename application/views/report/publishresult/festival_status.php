<div class="pagetitle">
      <h1>Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="<?php echo base_url(); ?> welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Status of Kalaivizha</li>
        </ol>
      </nav>
</div>


<?php
if (count($details1) > 0) {

    echo form_open('', array('id' => 'festdet', 'name' => 'festdet', 'target' => '_blank'));
    ?>
    <input type="hidden" name="hidfestid" id="hidfestid" value="" />

                <ul class="list-group">
                  <li class="list-group-item active">STATUS OF COMPLETION OF ITEMS </li>
                  <li class="list-group-item">
                  <div class="row">
                      <div class="col-4">Festival (Item Total) </div>
                      <div class="col-4">Items Completed </div>
                      <div class="col-4">Items not Completed</div>
                  </div>
                  </li>
                  <?php
                    $flag = 0;
                    $bg = 0;
                    foreach ($details1 as $value) {
            
                        for ($j = 0; $j < count($details2); $j++) {
            
                            if ($value['fest_id'] == $details2[$j]['fest_id']) {
            
                                $finished_item = $details2[$j]['pcode'];
                                $flag = 1;
                            }
                        }
            
                        if ($flag == 0)
                            $finished_item = 0;
                        $remind = $value['cnt'] - $finished_item;
                        if ($bg == 0) {
                            $bgcolor = "#fff";
                            $bg = 1;
                        } else {
                            $bgcolor = "#fff";
                            $bg = 0;
                        }
                        ?>
                  <li class="list-group-item">
                    <div class="row">
                      <div class="col-4"> 
                         <?php if ($value['cnt'] != 0) { ?>
                                    <a href="javascript:void(0)"  onClick="javascript:clicktoviewfestdet_public('<?php echo $value['fest_id']; ?>')">
                                        <?php echo $value['fest_name']; ?> (<?php echo $value['cnt']; ?>)</a>
                                    <?php
                                } else {
                                    echo $value['cnt'];
                                }
                                ?>
                      </div>
                      <div class="col-4"> 
                        <?php if ($finished_item != 0) { ?>
                                    <a href="javascript:void(0)"  onClick="javascript:finisheditemdet_public('<?php echo $value['fest_id']; ?>')"><?php echo $finished_item; ?></a>
                                    <?php
                                } else {
                                    echo 0;
                                }
                                ?>
                      </div>
                      <div class="col-4"> 
                        <?php if ($remind != 0) { ?>
                                    <a href="javascript:void(0)"  onClick="javascript:remainderitemdet_public('<?php echo $value['fest_id']; ?>')"><?php echo $remind; ?></a>
                                    <?php
                                } else {
                                    echo 0;
                                }
                                ?>  
                    </div>
                  </div>
                  </li>
                  <?php
                        $flag = 0;
                    }
                    ?>
                </ul>
                    
 

    <?php
    echo form_close();
} else {
    ?>
    <div align="center"><h1 class="text-center">Festival not started</h1></div>

    <?php
}
?>
