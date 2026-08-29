<div class="pagetitle">
      <h1>Result</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Result_Itemwise</li>
        </ol>
      </nav>
</div>


<?php
echo form_open('report/timefestreportpdf/confidential', array('id' => 'formDWP', 'target' => '_blank'));
?>
<form name="form1" method="post" action="">
    <div class="row justify-content-center">
    <div class="col-12">
     <div class="card">
        <div class="card-body">
<table>
        <tr>
            <th colspan="4" align="left">&nbsp;</th>
        </tr>
        <tr>
            <td width="10%" class="">&nbsp;</td>
            <td align="left" width="27%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Festival Type : </td>
            <td align="left" width="55%" class="table_row_first"><?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-control" id="cmbFestType" onchange="javascript:fetch_item_from_festival2(this.value)"'); ?></td>
            <td width="18%">&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td align="left" width="27%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Item Name : </td>

            <td align="left" width="55%" class="table_row_first"><div id="cmbitem"><?php echo form_dropdown("cbo_item", array('select'), '', 'class="form-control" id="cbo_item"'); ?></div></td>
            <td width="18%">&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td align="left" width="27%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td align="left" width="55%" class="table_row_first">
                <label>
                    <input type="submit" name="btnSubmit" class="btn btn-primary" id="btnSubmit" value="View Result">
                </label>       </td>
            <td width="18%">&nbsp;</td>
        </tr>

        <tr>
            <td align="center" colspan="4">&nbsp;</td>
        </tr>
    </table>
</div>
</div>
</div>
</div>

    <input type="hidden" name="hidUserId" id="hidUserId" />
</form>
<?php
//itemwise_report_interface

echo form_close();
?>
