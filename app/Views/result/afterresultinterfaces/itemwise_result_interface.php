<div align="center" class="heading_gray">
    <h3>Score Sheet</h3>
</div>
<br />
<?php
echo form_open('report/afterresultreportpdf/item_wise_result', array('id' => 'formscore', 'target' => '_blank'));

?>
<table class="table">
    <tr>
        <th colspan="4" align="left">Score Sheet - Item wise</th>
    </tr>
    <tr>
        <td width="10%" class="">&nbsp;</td>
        <td align="left" width="27%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Festival Type : </td>
        <td align="left" width="55%" class="table_row_first"><?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-control" id="cmbFestType" onchange="javascript:fetch_item_from_festival(this.value)"'); ?></td>
        <td width="18%">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td align="left" width="27%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Item Name : </td>

        <td align="left" width="55%" class="table_row_first"><div id="cmbitem"><?php echo form_dropdown("cbo_item", array('select'), '', 'class="form-control" id="cbo_item" '); ?></div></td>
        <td width="18%">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td align="left" width="27%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td align="left" width="55%" class="table_row_first">
                <label>
                    <input type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary" value="View report" onclick="javascript: return callsheetfirst()">
                </label>
        </td>
        <td width="18%">&nbsp;</td>
    </tr>

    <tr>
        <td align="center" colspan="4">&nbsp;</td>
    </tr>
</table>
<input type="hidden" name="hidUserId" id="hidUserId" />

<?php
//itemwise_report_interface
echo form_close();

?>
