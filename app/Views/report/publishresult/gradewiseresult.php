<style type="text/css">
    <!--
    .style1 {
        font-size: 14px;
        font-weight: bold;
        color: #0d6efd;
    }
    .style2{
        font-size: 12px;
        font-weight: bold;
        color:#000000;
    }
    .style3{ 
        font-size: 11px;
        font-weight: bold;
        color:#000000;
    }


    -->
</style>
<div class="pagetitle">
    <h1>Result Report</h1>
    <nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
        <li class="breadcrumb-item active">Grade Wise Participant Result Details</li>
    </ol>
    </nav>
</div>

<?php
echo form_open('report/resultindex/grade_report', array('id' => 'radewise', 'target' => '_blank'));


$rank['A'] = ' A  -  GRADE ';
$rank['B'] = ' B  -  GRADE ';
$rank['C'] = ' C  -  GRADE ';
$rank['ALL'] = ' All GRADE ';
?>
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
    <tr>
        <th colspan="4" align="left">Select Festival&amp; Grade</th>
    </tr>
    <tr>
        <td width="10%" class="">&nbsp;</td>
        <td align="left" width="27%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Festival Type : </td>
        <td align="left" width="55%" class="table_row_first"><?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-control" id="cmbFestType" '); ?></td>
        <td width="18%">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td align="left" width="27%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Grade :</td>

        <td align="left" width="55%" class="table_row_first" ><?php echo form_dropdown("grade", $rank, '', 'class="form-control" id="grade" '); ?>&nbsp;</td>
        <td width="18%">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td align="left" width="27%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td align="left" width="55%" class="table_row_first"><form name="form1" method="post" action="">
                <label>
                    <input type="submit" name="btnSubmit"  class="btn btn-primary" id="btnSubmit" value="View report" onClick="return rankwise();">
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
