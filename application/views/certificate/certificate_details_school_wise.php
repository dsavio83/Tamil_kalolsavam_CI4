<style>
.display_none {
    display: none;
}
</style>

<div class="pagetitle">
      <h1>Certificate</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Certificate School Wise</li>
        </ol>
      </nav>
</div>

<?php
echo form_open('', array('id' => 'formIWPq'));
?>
<input type="hidden" name="hidSchoolCode" id="hidSchoolCode" value=""  />

<div class="row justify-content-center">
    <div class="col-xl-12 col-md-12 col-sm-12">
              <div class="card recent-sales overflow-auto">
                <div class="card-body">
                  
            <!-- Table Start --->
            <div class="table-responsive-md">
    <table id="paginationNumbers" class="table" width="100%">
    <tr>
        <th colspan="5" align="left">
            <h5 class="card-title">Certificate - School Wise </span></h5>
        </th> 
             
        <th align="right">
            Print&nbsp;<img src="<?php echo base_url(false) . 'images/print_icon.png'; ?>" title="print" class="" onClick="javascript:printContent('print_content');
                        return false;" /></th>
    </tr>
    <tr>
        <th width="5%">SI.No </th>
        <th width="37%">&nbsp;&nbsp;&nbsp;School Name & Code </th>
        <!--<th align="center" width="8%">Total Item</th>
        <th align="center" width="8%">No.Students</th>-->
        <th align="center" width="8%">Participation</th>
        <th align="center" width="8%">A Grade</th>
        <th align="center" width="8%">B Grade</th>
        <th align="center" width="8%">C Grade</th>
        <!--<th align="center" width="8%">Total Points</th>-->
    </tr>
    <?php
    for ($j = 0; $j < count($school_details); $j++) {
        ?>
        <tr>
            <td align="center" ><?php echo $j + 1; ?></td>
            <td width="27%">
                &nbsp;&nbsp;
                <a href="javascript:void(0)" onClick="javascript:getSchoolWiseCertificate(<?php echo $school_details[$j]['school_code']; ?>)">
                    <?php echo $school_details[$j]['school_code'] . '&nbsp;-&nbsp;' . $school_details[$j]['school_name']; ?></a>
            </td>
            <!--<td align="center" ><?php //echo $itemtype;    ?></td>
            <td align="center"><?php // echo $itempart[$j]['cpt'];    ?></td>-->
            <td align="center"><?php echo $school_details[$j]['no_participation']; ?></td>
            <td align="center"><?php echo $school_details[$j]['a_grade']; ?></td>
            <td align="center"><?php echo $school_details[$j]['b_grade']; ?></td>
            <td align="center"><?php echo $school_details[$j]['c_grade']; ?></td>
            <!--<td align="center"><?php echo $school_details[$j]['total_point']; ?></td>-->
        </tr>
        <?php
    }
    ?>
</table>
<!-- print content starts here----------------------------------->
<div id="print_content" class="display_none" >
    <?php
    //$this->load->view('report/report_header');
    ?>
    <table width="100%" border="1" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
        <tr>
            <th colspan="6" align="left"> 
                &nbsp;&nbsp;&nbsp; Certificate - School Wise 
                </td>
        </tr>
        <tr>
            <th width="5%">SI.No </th>
            <th width="37%">&nbsp;&nbsp;&nbsp;School Name & Code </th>
            <!--<th align="center" width="8%">Total Item</th>
            <th align="center" width="8%">No.Students</th>-->
            <th align="center" width="8%">Participation</th>
            <th align="center" width="8%">A Grade</th>
            <th align="center" width="8%">B Grade</th>
            <th align="center" width="8%">C Grade</th>
            <!--<th align="center" width="8%">Total Points</th>-->
        </tr>
        <?php
        for ($j = 0; $j < count($school_details); $j++) {
            ?>
            <tr>
                <td align="center"><?php echo $j + 1; ?></td>
                <td width="27%">
                    &nbsp;&nbsp;
                    <?php echo $school_details[$j]['school_code'] . '&nbsp;-&nbsp;' . $school_details[$j]['school_name']; ?>
                </td>
                <!--<td align="center" >&nbsp;<?php //echo $itemtype;    ?></td>
                <td align="center">&nbsp;<?php // echo $itempart[$j]['cpt'];    ?></td>-->
                <td align="center">&nbsp;<?php echo $school_details[$j]['no_participation']; ?></td>
                <td align="center">&nbsp;<?php echo $school_details[$j]['a_grade']; ?></td>
                <td align="center">&nbsp;<?php echo $school_details[$j]['b_grade']; ?></td>
                <td align="center">&nbsp;<?php echo $school_details[$j]['c_grade']; ?></td>
                <!--<td align="center">&nbsp;<?php echo $school_details[$j]['total_point']; ?></td>-->
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
    //$this->load->view('report/report_footer');
    ?>
</div>
<!-- display content ends here --------------------------------------->
<?php
//itemwise_report_interface
echo form_close();
?>
