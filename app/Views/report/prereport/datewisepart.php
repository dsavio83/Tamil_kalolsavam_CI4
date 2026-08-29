<div class="pagetitle">
      <h1>PreFest Report</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Datewise Participants</li>
        </ol>
      </nav>
</div>

<br />
<?php
echo form_open('report/prereportpdf/datewisepart', array('id' => 'formDWP', 'target' => '_blank')); ?>
<form name="form1" method="post" action="">
    <div class="row justify-content-center">
            <div class="col-md-12 col-sm-12 col-xl-8">
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title">Datewise Participants </h5>
                        <div class="row">
                            <div class="col-xl-2"></div>
                            <div class="col-xl-3 col-sm-12 col-md-3 col-lg-12">
                            <label for="inputNanme" >Select Date</label> 
                            </div>
                            <div class="col-xl-6 col-sm-12 col-md-6 col-lg-12 text-center">
                                    <?php echo form_dropdown("txtDate", @$date_array, (@$date != '') ? $date : '', 'class="form-select" id="txtDate"'); ?>
                                        <!--<input class="form-control date_field" type="text" onfocus="displayCalendar(document.formAllotment.txtDate,'yyyy-mm-dd',this)" name="txtDate" id="txtDate" value="<?php echo @$date; ?>" readonly="readonly">-->
                                            <!--<input class="form-control date_field" type="text"   onFocus="javascript:vDateType='3'" onBlur="DateFormat(this,this.value,event,true,'3')" onKeyDown="DateFormat(this,this.value,event,false,'3')" onKeyUp="DateFormat(this,this.value,event,false,'3')" maxlength="10"  name="txtDate" id="txtDate" value="<?php //echo $start_date;    ?>">-->
                                        <!--<img src="<?php echo image_url(); ?>calender.png" onclick="displayCalendar(document.formAllotment.txtDate,'yyyy-mm-dd',this)" width="16" height="16" style="cursor:pointer" />-->
                            </div>
                            <div class="col-xl-2"></div>
                        </div>
                        <div class="row">
                           
                        <div class="text-center">
                               <input type="submit" class="btn btn-primary mt-4" name="btnSubmit" id="btnSubmit" value="View report">
                        </div>
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
