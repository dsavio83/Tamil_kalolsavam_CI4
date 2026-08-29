<br />
<?php
echo form_open(base_url() . 'stage/item_participant/stage_allot_fest_all', array('id' => 'formIWPq'));

?>
<input type="hidden" name="txtItemCode" id="txtItemCode" value=""  />
<input type="hidden" name="txtFestId" id="txtFestId" value="<?php echo $fest_id; ?>"  />
<div class="row">
    <div class="col-12 mt-2">
        <h3 class="card-title">Festival  :   <?php echo @$itempart[0]['fest_name'] ? @$itempart[0]['fest_name'] : @$single[0]['fest_name']; ?></h3>
    </div>
</div>
<div class="table-responsive-sm">
    <table class="table table-striped">
    <tr>
        <th width="25%" class="table_row_first"> &nbsp;Item</th>
        <th align="center" width="10%" class="table_row_first"> No of Participants</th>
        <th align="left" width="50%" class="table_row_first">Date&Time (Date HH:MM)</th>
        <th align="left" width="35%" class="table_row_first">Stag_Name</th>
        <th align="center" width="13%" class="table_row_first">No. of Cluster</th>
        <th align="center" width="12%" class="table_row_first">No of judges</th>
    </tr>

    <?php
    $judge_array = array();

    for ($i = 1; $i <= $no_of_judges; $i++)
        $judge_array[$i] = $i;
    for ($j = 0; $j < count($single); $j++) {
        $item_code = $single[$j]['item_code'];
        $date = date('Y-m-d', strtotime($single[$j]['start_time']));
        $hour = date('H', strtotime($single[$j]['start_time']));
        $min = date('i', strtotime($single[$j]['start_time']));
        $stage_array = array();
        for ($i = 0; $i < count($stages); $i++) {
            $stage_array[$stages[$i]['stage_id']] = $stages[$i]['stage_name'];
        }

        $value_array = array();
        $limit = (@$single[$j]['is_off_stage'] == 'Y') ? 1 : (($no_of_clusters > @$single[$j]['cpt']) ? @$single[$j]['cpt'] : $no_of_clusters);
        for ($i = 1; $i <= $limit; $i++)
            $value_array[$i] = $i;
        ?>
        <tr>
            <td  class="table_row_first" align="left">
                <a href="javascript:void(0)" onClick="javascript:getitemstagedet('<?php echo $single[$j]['item_code'] ?>')">
                    <?php echo $single[$j]['item_code'] . '&nbsp;-&nbsp;' . $single[$j]['item_name'] . '(' . $single[$j]['item_type'] . ')'; ?></a> </td>

            <td align="center" class="table_row_first">
                <?php echo $single[$j]['cpt']; ?></td>
        <input type="hidden" name="hidMaxPartcipants<?php echo $item_code; ?>" id="hidMaxPartcipants<?php echo $item_code; ?>" value="<?php echo $single[$j]['cpt']; ?>"  /> 
        <td align="left" class="table_row_first">
            <div class="row">
                <div class="col-xl-6 col-sm-6">
                    <?php echo form_dropdown('txtDate' . $item_code, $date_array, $date, 'id="txtDate' . $item_code . '" class="form-control"'); ?>
                </div>
                <div class="col-xl-3 col-sm-3">
                    <?php echo form_dropdown('txtHour' . $item_code, @$hour_array, $hour, 'class="form-control" id="txtHour' . $item_code . '"'); ?>
                </div>
                <div class="col-xl-3 col-sm-3">
                    <?php echo form_dropdown('txtMin' . $item_code, @$min_array, $min, 'class="form-control" id="txtMin' . $item_code . '"'); ?>
                </div>
            </div>
        </td>


        <td align="left" class="table_row_first"> 
            <?php
                $attributes = 'class="form-control" id="cmbStage' . $item_code . '"';
                echo form_dropdown('cmbStage' . $item_code, $stage_array, @$single[$j]['stage_id'], $attributes);
                ?>
                </td>
        <td align="center" class="table_row_first">
            <?php echo form_dropdown('cmbNoOfCluster' . $item_code, $value_array, @$single[$j]['no_of_cluster'], 'id="cmbNoOfCluster' . $item_code . '" class="form-control"'); ?>
            <?php //echo $single[$j]['no_of_cluster'];  ?></td>
        <td align="center" class="table_row_first">
            <?php echo form_dropdown('cmbNoOfJudges' . $item_code, $judge_array, (@$single[$j]['no_of_judges']) ? @$single[$j]['no_of_judges'] : '3', 'id="cmbNoOfJudges' . $item_code . '" class="form-control"'); ?>
        </td>
    </tr>



    <?php
}
?>


<?php
for ($j = 0; $j < count($itempart); $j++) {
    $item_code = $itempart[$j]['item_code'];
    $date = date('Y-m-d', strtotime($itempart[$j]['start_time']));
    $hour = date('H', strtotime($itempart[$j]['start_time']));
    $min = date('i', strtotime($itempart[$j]['start_time']));
    $stage_array = array();
    for ($i = 0; $i < count($stages); $i++) {
        $stage_array[$stages[$i]['stage_id']] = $stages[$i]['stage_name'];
    }

    $value_array = array();
    $limit = (@$itempart[$j]['is_off_stage'] == 'Y') ? 1 : (($no_of_clusters > @$itempart[$j]['cpt']) ? @$itempart[$j]['cpt'] : $no_of_clusters);
    for ($i = 1; $i <= $limit; $i++)
        $value_array[$i] = $i;
    ?>
    <tr>
        <td  class="table_row_first" align="left">
            <a href="javascript:void(0)" onClick="javascript:getitemstagedet('<?php echo $itempart[$j]['item_code'] ?>')">
                <?php echo $itempart[$j]['item_code'] . '&nbsp;-&nbsp;' . $itempart[$j]['item_name'] . '(' . $itempart[$j]['item_type'] . ')'; ?></a> </td>

        <td align="center" class="table_row_first">
            <?php echo $itempart[$j]['cpt']; ?></td>
    <input type="hidden" name="hidMaxPartcipants<?php echo $item_code; ?>" id="hidMaxPartcipants<?php echo $item_code; ?>" value="<?php echo $itempart[$j]['cpt']; ?>"  />
    <td align="left" class="table_row_first">
      <div class="row">
            <div class="col-xl-6 col-sm-6">
                <?php
                    $attributes = 'class="form-control" id="txtDate' . $item_code . '"';
                    echo form_dropdown('txtDate' . $item_code, $date_array, $date, $attributes);
                    ?>
            </div>
            <div class="col-xl-3 col-sm-3">
                <?php echo form_dropdown('txtHour' . $item_code, @$hour_array, $hour, 'class="form-control" id="txtHour' . $item_code . '"'); ?>
            </div>
            <div class="col-xl-3 col-sm-3">
                <?php echo form_dropdown('txtMin' . $item_code, @$min_array, $min, 'class="form-control" id="txtMin' . $item_code . '"'); ?>
            </div>
        </div>
    </td>


    <td align="left" class="table_row_first"> 
        <?php
            $attributes = 'class="form-control" id="cmbStage' . $item_code . '"';
            echo form_dropdown('cmbStage' . $item_code, $stage_array, @$itempart[$j]['stage_id'], $attributes);
            ?>
        <?php //echo $itempart[$j]['stage_name'];  ?></td>
    <td align="center" class="table_row_first">
        <?php echo form_dropdown('cmbNoOfCluster' . $item_code, $value_array, @$itempart[$j]['no_of_cluster'], 'id="cmbNoOfCluster' . $item_code . '" class="form-control"'); ?>
        <?php //echo $itempart[$j]['no_of_cluster'];  ?></td>
    <td align="center" class="table_row_first">
        <?php echo form_dropdown('cmbNoOfJudges' . $item_code, $judge_array, (@$itempart[$j]['no_of_judges']) ? @$itempart[$j]['no_of_judges'] : '3', 'id="cmbNoOfJudges' . $item_code . '" class="form-control"'); ?>
    </td>
    </tr>

    <?php }
?>

</table>
<div class="row mt-3">
    <div class="col-12 mt-2 text-center">
        <?php echo form_submit('Allotment', 'Allotment', 'class="btn btn-primary"') ?>
    </div>
</div>
</div>


<?php
//itemwise_report_interface

echo form_close();
?>
