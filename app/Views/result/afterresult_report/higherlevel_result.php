<div align="center" class="heading_gray">
    <h5 class="card-title">Participants Eligible For Higher Level Competition</h5>
</div>
<br />

<?php
echo form_open('report/afterresultreportpdf/higherlevel_result', array('id' => 'higher_result', 'target' => '_blank'));
?>
<div class="row justify-content-center">
    <div class="col-12">
        <div class="card p-3">
            <div class="card-body">
                <table>
                    <h5 class="card-title">Select Festival</h5>
                     <div class="row">
                        <div class="col-md-3 col-sm-12 col-xl-3 mt-2">
                            Festival Type
                        </div>
                        
                         <div class="col-md-9 col-sm-12 col-xl-9 mt-2">
                            <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-control" id="cmbFestType" onchange="javascript:fetch_higherlevel_participant(this.value)"'); ?>
                        </div>
                        
                         <div class="col-md-3 col-sm-12 col-xl-3 mt-2">
                            Item Name
                        </div>
                        
                         <div class="col-md-9 col-sm-12 col-xl-9 mt-2">
                            <div id="cmbitem"><?php echo form_dropdown("cbo_item", array('select'), '', 'class="form-control" id="cbo_item" '); ?></div>
                        </div>
                        
                         <div class="col-12 text-center mt-2">
                            <input type="submit" name="btnSubmit" id="btnSubmit" value="View report" class="btn btn-primary mt-2" onclick="return checkvalsubmit();">
                        </div>
                </table>
 
 <input type="hidden" name="hidUserId" id="hidUserId" />

<?php
//itemwise_report_interface

echo form_close();
?>
<br>

<?php
echo form_open('report/afterresultreportpdf/schoolhigher_result', array('id' => 'school_result', 'target' => '_blank'));

?>
    <table>
        <h5 class="card-title">Enter School Code</h5>
         <div class="row">
            <div class="col-md-2 col-sm-12 col-xl-2 mt-2">
                School Code
            </div>
            
             <div class="col-md-4 col-sm-12 col-xl-4 mt-2">
                <?php echo form_input("txtSchoolCode", @$school_details[0]['school_code'], 'class="form-control" id="txtSchoolCode"
	            onkeypress="javascript:return numbersonly(this, event, false);"  onBlur="javascript:higher_schooldetails(this.value)"'); ?>
            </div>
            
             <div class="col-md-6 col-sm-12 col-xl-6 mt-2">
                <div id="school_name"> </div>
            </div>
        </div>
        
        <div class="row  mt-2">
             <div class="col-12 text-center">
               <input type="submit" name="btnSubmit2" id="btnSubmit2" class="btn btn-primary" value="View report" onClick="return checkschoolval();">
            </div>
            
             <div class="col-12 text-center mt-2">
                <a href="../afterresultreportpdf/schoolhigher_resultall" target="_blank">School Wise All</a>
            </div>
        </div>

</table>
<input type="hidden" name="hidUserId" id="hidUserId" />

<?php
//itemwise_report_interface

echo form_close();
?>