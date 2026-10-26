<div align="center" class="heading_gray">
    <h3>List Of Participants - More Items</h3>
</div>
<br />

<?php
echo form_open('district/report/prereportpdf/participant_more_item', array('id' => 'formIWP', 'target' => '_blank'));
?>
<input type="hidden" name="hidUserId" id="hidUserId" />
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
    <tr>
        <th colspan="2" align="left">List of Participants in More than one Item</th>
    </tr>
    <tr>
        
        <td align="left" width="27%">Festival Type </td>
        <td align="left" width="55%"><?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-control" id="cmbFestType"'); ?></td>
        
    </tr>
   
    <tr>
        
        <td align="left" colspan="2">      <label>
                <?php echo form_submit('Report', 'Report', 'onClick="javascript: return morethanone()" class="btn btn-primary"'); ?>
            </label>
            </form>
        </td>
        
    </tr>

   
</table>


<?php
//itemwise_report_interface
echo form_close();
?>