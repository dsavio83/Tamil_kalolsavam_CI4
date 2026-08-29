 
<?php
echo form_open('', array('id' => 'formIWPq'));
?>
<input type="hidden" name="txtItemCode" id="txtItemCode" value=""  />
<section class="section">
      <div class="row">
        <div class="col-lg-12 col-xl-12 col-sm-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Festival  &nbsp;<?php echo $itempart[0]['fest_name']; ?></h5>

              <!-- Default Table -->
              <table class="table">
                <thead>
                  <tr class="text-center">
                    <th scope="col">Item Code Name</th>
                    <th scope="col">Item Type</th>
                    <th scope="col">No. of Students</th>
                    <th scope="col">Date & Time</th>
                    <th scope="col">Stage</th>
                    <th scope="col">No. of Cluster</th>
                  </tr>
                </thead>
                
                <tbody>
                  <tr>
                    <?php
                        for ($j = 0; $j < count($single); $j++) {
                            if ($single[$j]['item_type'] == 'S')
                                $itemtype = 'Single';
                            else if ($single[$j]['item_type'] == 'G')
                                $itemtype = 'Group';
                            else
                                $itemtype = '';
                    ?>
                    <th> 
                        <a href="javascript:void(0)" onClick="javascript:getitemstagedet('<?php echo $single[$j]['item_code'] ?>')">
                        <?php echo $single[$j]['item_code'] . '&nbsp;-&nbsp;' . $single[$j]['item_name']; ?></a>
                    </th>
                    <td> <?php echo $itemtype; ?></td>
                    <td> <?php echo $single[$j]['cpt']; ?></td>
                    <td> <?php echo $single[$j]['start_time']; ?></td>
                    <td> <?php echo $single[$j]['stage_name']; ?></td>
                    <td><?php echo $single[$j]['no_of_cluster']; ?></td>
                  </tr>

    <?php }?>


    <?php
    for ($j = 0; $j < count($itempart); $j++) {
        if ($itempart[$j]['item_type'] == 'S')
            $itemtype = 'Single';
        else if ($itempart[$j]['item_type'] == 'G')
            $itemtype = 'Group';
        else
            $itemtype = '';
        ?>
        <tr>
            <th>
               ;
                <a href="javascript:void(0)" onClick="javascript:getitemstagedet('<?php echo $itempart[$j]['item_code'] ?>')">
                    <?php echo $itempart[$j]['item_code'] . '&nbsp;-&nbsp;' . $itempart[$j]['item_name']; ?></a> </th>
            <td align="center" width="15%">
                <?php echo $itemtype; ?></td>
            <td align="center" width="10%">
                <?php echo $itempart[$j]['cpt']; ?></td>
            <td align="center" width="20%">
                <?php echo $itempart[$j]['start_time']; ?></td>
            <td align="center" width="10%"> 
                <?php echo $itempart[$j]['stage_name']; ?></td>
            <td align="center" width="20%">
                <?php echo $itempart[$j]['no_of_cluster']; ?></td>
        </tr>



        <?php
    }
    ?>
    </tbody>
              </table>
              <!-- End Default Table Example -->
            </div>
          </div>
      </div>
  </div>
</section>


<?php
//itemwise_report_interface
echo form_close();
?>