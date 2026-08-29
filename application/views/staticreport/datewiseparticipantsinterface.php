<?php
echo form_open('staticreport/Onstagereport1/rpt_datewiseparticipants', array('id' => 'formDWP'));
?>
<form name="form1" method="post" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
        <tr>
            <th colspan="4" align="left">Select Item</th>
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
            <td align="left" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Date :</td>
            <td align="left" class="table_row_first"><input class="form-control date_field" type="text" onfocus="displayCalendar($('txtDate'), 'yyyy-mm-dd', this)" name="txtDate" id="txtDate" value="<?php echo @$date; ?>">
                        <!--<input class="form-control date_field" type="text"   onFocus="javascript:vDateType='3'" onBlur="DateFormat(this,this.value,event,true,'3')" onKeyDown="DateFormat(this,this.value,event,false,'3')" onKeyUp="DateFormat(this,this.value,event,false,'3')" maxlength="10"  name="txtDate" id="txtDate" value="<?php //echo $start_date;    ?>">-->
                <img src="<?php echo image_url(); ?>calender.png" onclick="displayCalendar($('txtDate'), 'yyyy-mm-dd', this)" width="16" height="16" style="cursor:pointer" /></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td align="left" width="27%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td align="left" width="55%" class="table_row_first">
                <label>
                    <input type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary" value="View report">
                </label>
            </td>
            <td width="18%">&nbsp;</td>
        </tr>

        <tr>
            <td align="center" colspan="4">&nbsp;</td>
        </tr>
    </table>
    <input type="hidden" name="hidUserId" id="hidUserId" />
</form>
<?php
//itemwise_report_interface
echo form_close();
?>
