<style>
.display_none {
    width: 0px;
    height: :0px;
    display:none;
}
</style>
<div align="center" class="heading_gray"></div>
<?php echo form_open("", ["id" => "resreport", "target" => "_blank"]); ?>
<input type="hidden" name="hiditemcode" id="hiditemcode" />
<input type="hidden" name="hidFestcode" id="hidFestcode" value="<?php echo $fest; ?>" />
<div class="row justify-content-center">
    <div class="col-xl-12 col-md-12">
     <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12 card-title">
                     &nbsp; &nbsp;Festival : &nbsp;<?php echo $resultdet[0]["fest_name"]; ?>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
    <tr>
        <th align="left" width="44%" >&nbsp;&nbsp;&nbsp;Item </th>
        <th align="left" width="30%" >Item Type</th>
        <th width="17%" >&nbsp;</th>
    </tr>
    <?php foreach ($resultdet as $value) {
        if ($value["item_type"] == "S") {
            $item_type = "Single";
        } else {
            $item_type = "Group";
        } ?>
        <tr>
            <td class="table_row_first">&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" onClick="javascript:resultprintout('<?php echo $value[
                "item_code"
            ]; ?>')">
                    <?php echo $value["item_code"] .
                        " - " .
                        $value["item_name"]; ?></a></td>
            <td align="left" width="44%" class="table_row_first"><?php echo $item_type; ?></td>
            <td align="left" width="30%" class="table_row_first">
            </td>
        </tr>
        <?php
    } ?>
    <tr>
        <td class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="javascript:void(0)" onClick="javascript:resultallprint()">
                All Results</a></td>
        <td class="table_row_first"></td>
        <td class="table_row_first"></td>
    </tr>

</table>
</div>
</div>
</div>
</div>

<!-- print content starts here----------------------------------->
<div id="print_content" class="display_none" >

    <table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="table" style="margin-top:15px;">
        <tr>
            <th colspan="4" align="left">Result Declared Items Festival:&nbsp;<?php echo $resultdet[0][
                "fest_name"
            ]; ?></th>
        </tr>
        <tr>
            <th align="left" width="44%" >&nbsp;&nbsp;&nbsp;Item Code & Name</th>
            <th align="left" width="30%" >Item Type</th>
            <th width="17%" >&nbsp;</th>
        </tr>
        <?php foreach ($resultdet as $value) {
            if ($value["item_type"] == "S") {
                $item_type = "Single";
            } else {
                $item_type = "Group";
            } ?>
            <tr>
                <td class="table_row_first">&nbsp;&nbsp;&nbsp;
                    <?php echo $value["item_code"] .
                        " - " .
                        $value["item_name"]; ?></td>
                <td align="left" width="44%" class="table_row_first"><?php echo $item_type; ?></td>
                <td align="left" width="30%" class="table_row_first">
                </td>
            </tr>
            <?php
        } ?>


        <tr>
            <td align="center" colspan="3">&nbsp;</td>
        </tr>
    </table>



</div>
<!-- print content end here----------------------------------->
<?php //itemwise_report_interface

echo form_close();
?>
