<div class="pagetitle">
      <h1>Result_Itemwis</h1>
</div>

<br />
<?php
echo form_open('district/report/timeof_fest_report/timefest_result_common', array('id' => 'timeoffest'));

?>
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
    <tr>
        <th colspan="4" align="left"><h5 class="card-title">Declared  Result </h5> </th>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td align="left"> Festival Type : </td>
        <td align="left"><?php echo form_dropdown("resultFest", $fest, '', 'class="form-control" id="resultFest" '); ?></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td>
            <label>
                <input type="submit" name="btnSubmit" class="btn btn-primary" id="btnSubmit" value="GET">
            </label>       </td>
        <td width="18%" class="table_row_first">&nbsp;</td>
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
