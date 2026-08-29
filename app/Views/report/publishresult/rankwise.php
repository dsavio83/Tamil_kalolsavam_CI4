<div class="pagetitle">
    <h1>Result Report</h1>
    <nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
        <li class="breadcrumb-item active">Rank Wise Participants Result Details</li>
    </ol>
    </nav>
</div>
<?php
echo form_open('report/resultindex/rankwise_report', array('id' => 'rankwise', 'target' => '_blank'));


$rank[1] = 1;
$rank[2] = 2;
$rank[3] = 3;
$rank['ALL'] = 'All Rank';
?>
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
    <tr>
        <th colspan="4" align="left"><h5 class="card-title"> Select Item &amp; Rank</h5></th>
    </tr>
    <tr>
        <td width="10%" class="">&nbsp;</td>
        <td align="left" width="27%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Festival Type : </td>
        <td align="left" width="55%" class="table_row_first"><?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-control" id="cmbFestType" '); ?></td>
        <td width="18%">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td align="left" width="27%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rank :</td>

        <td align="left" width="55%" class="table_row_first"><?php echo form_dropdown("rank", $rank, '', 'class="form-control" id="rank" '); ?>&nbsp;</td>
        <td width="18%">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td align="left" width="27%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td align="left" width="55%" class="table_row_first"><form name="form1" method="post" action="">
                <label>
                    <input type="submit" class="btn btn-primary" name="btnSubmit" id="btnSubmit" value="View report" onClick="return rankwise();">
                </label>
            </form>    </td>
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
