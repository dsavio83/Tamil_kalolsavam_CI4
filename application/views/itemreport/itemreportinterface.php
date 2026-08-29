<?php
echo form_open('itemreport/', array('id' => 'formPWD'));
?>

<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="table" style="margin-top:15px;">
    <tr>
        <th colspan="4" align="left"><strong>List of Item details</strong></th>
    </tr>

    <tr>

        <td width="23%" align="left" class="table_row_first"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Festival  :</td>
        <td width="77%" align="left" class="table_row_first"><?php echo form_dropdown("txtfestFrom", $retdat, 'id="txtfestFrom"'); ?></td>
    </tr>
    <tr>
        <td align="center" colspan="2"><?php echo form_submit('Get', 'Get', 'class="btn btn-primary"'); ?></td>
    </tr>
</table>
<?php
echo form_close();
?>