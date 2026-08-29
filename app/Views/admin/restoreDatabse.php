<div align="center" class="heading_gray">
    <h3>Kalolsavam - Restore Database</h3>
</div>
<?php
echo form_open_multipart('import/rstoreDatabase_fromfile', array('id' => 'formKalolsavamrestore'));

?>
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
    <tr>
        <th align="left" colspan="4">Kalolsavam</th>
    </tr>	   
    <tr>
        <td align="left" width="20%" class="table_row_first">Upload Logo</td>
        <td align="left" width="30%" class="table_row_first">
            <?php echo form_upload("kalolsavamRestore", 'class="form-control_large" id="kalolsavamRestore" '); ?>
            <span class="guide_line">(.sql)</span>
        </td>
        <td align="left" colspan="2" class="table_row_first">&nbsp;</td>
    </tr>
    <tr>
        <td align="center" colspan="2">
            <?php
            echo
            form_button('restore_kalolsavam', '<- Restore ->', 'id="restore_kalolsavam" onClick="javascript:fncRestoreKalolsavam();"');
            ?>
        </td>
    </tr>
</table>
<input type="hidden" name="restore_id" id="restore_id" value="" />
<?php

echo form_close();
?>
<br/>