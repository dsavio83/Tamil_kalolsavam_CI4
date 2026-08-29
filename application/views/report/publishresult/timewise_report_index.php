<div class="pagetitle">
    <h1>Result Report</h1>
    <nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
        <li class="breadcrumb-item active">Time Wise Result Published</li>
    </ol>
    </nav>
</div>

<?php
echo form_open('report/afterresultreportpdf/time_wise_result_report', array('id' => 'rankwise', 'target' => '_blank'));

$rank[1] = 1;
$rank[2] = 2;
$rank[3] = 3;
$rank['ALL'] = 'All Rank';
/* $cur_date	=	date('Y-m-d');
  $date_array[$cur_date]	=	date('j M Y',strtotime($cur_date)); */
//echo "<br /><br /><br /><br />-------".$date_array;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
    <tr>
        <th colspan="6" align="left">Select Date &amp; Time</th>
    </tr>
    <tr>
        <td width="4%" class="">&nbsp;</td>
        <td align="left" width="25%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;From Date : </td>
        <td align="left" width="18%" class="table_row_first"><?php echo form_dropdown('txtfromDate', $date_array, '', 'id="txtfromDate" class="form-control"'); ?></td>
        <td align="left" width="18%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;From Time : </td>
        <td align="left" width="35%" class="table_row_first">	

            <?php
            $time_array_from = array('1' => '1:00', '2' => '2:00', '3' => '3:00', '4' => '4:00', '5' => '5:00', '6' => '6:00', '7' => '7:00', '8' => '8:00', '9' => '9:00', '10' => '10:00', '11' => '11:00', '12' => '12:00');
            echo form_dropdown('txtfromTime', $time_array_from, '', 'id="txtfromTime"');

            $ampm_array_from = array('AM' => 'AM', 'PM' => 'PM');
            echo form_dropdown('txtfromampm', $ampm_array_from, '', 'id="txtfromampm"');
            ?>

        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td align="left" width="25%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;To Date : </td>

        <td align="left" width="18%" class="table_row_first"><?php echo form_dropdown('txttoDate', $date_array, '', 'id="txttoDate" class="form-control"'); ?></td>
        <td align="left" width="18%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;To Time : </td>

        <td align="left" width="35%" class="table_row_first">
            <?php
            $time_array_to = array('1' => '1:00', '2' => '2:00', '3' => '3:00', '4' => '4:00', '5' => '5:00', '6' => '6:00', '7' => '7:00', '8' => '8:00', '9' => '9:00', '10' => '10:00', '11' => '11:00', '12' => '12:00');
            echo form_dropdown('txttoTime', $time_array_to, '', 'id="txttoTime"');

            $ampm_array_to = array('AM' => 'AM', 'PM' => 'PM');
            echo form_dropdown('txttoampm', $ampm_array_to, '', 'id="txttoampm"');
            ?>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td align="left" width="25%" class="table_row_first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td align="left" width="18%" class="table_row_first"><form name="form1" method="post" action="">
                <label>
                    <input type="submit" class="btn btn-primary" name="btnSubmit" id="btnSubmit" value="View report" >
                </label>
            </form>    </td>
        <td width="18%" class="table_row_first">&nbsp;</td>
        <td width="35%" class="table_row_first">&nbsp;</td>
    </tr>

    <tr>
        <td align="center" colspan="5">&nbsp;</td>
    </tr>
</table>
<input type="hidden" name="hidUserId" id="hidUserId" />

<?php
//itemwise_report_interface
echo form_close();

?>
