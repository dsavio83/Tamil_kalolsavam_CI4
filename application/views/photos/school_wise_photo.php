<?php
echo form_open_multipart('pictures/pictures/bulk_upload', array('id' => 'formschool_photo', 'name' => 'formschool_photo'));

if(!empty($retdat)){
    for ($j = 0; $j < count(@$retdat); $j++) {
        $dat[@$retdat[$j]['school_code']] = @$retdat[$j]['school_name'];
    }
} ?>
<input type="hidden" name="hiddtext" id="hiddtext" value="">
<input type="hidden" name="baseurl" id="baseurl" value="<?php echo base_url(); ?>" />
<table>
    <div class="row justify-content-center">
        <div class="col-md-12 col-sm-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"> Photo Upload - School Wise</h5>
                        <div class="row">
                           
                            <div class="col-xl-4 col-md-4 col-sm-12 col-lg-12 mt-2">
                                <strong>Enter School Code</strong>
                            </div>
                            
                            <div class="col-xl-4 col-md-4 col-sm-12 col-lg-12 mt-2">
                                    <?php echo form_input("txtSchoolCode", @$school_details[0]['school_code'], 'class="form-control" id="txtSchoolCode"  onkeypress="javascript:return numbersonly(this, event, false);"'); ?> 
                                    <div id="cmbitem"> </div>
                            </div>
                            
                            <div class="col-xl-4 col-sm-12 col-md-4 col-lg-12 text-center mt-2">
                                <?php echo form_submit('Get Details', 'Get Details', 'onClick="javascript: return fetch_schoolwise_studdetails()" class="btn btn-primary"'); ?>
                            </div>
                            
                        </div>
<?php 
$i  =   0;
if(@$participant_det) {

if(!is_numeric ($cur_num))
{
$cur_num    =   0;
} ?>

                    <div id="schcode_wise_upload">
                        <div class="row mt-4">
                            <div class="col-2"> </div>
                            <div class="col-xl-12 col-sm-12 text-center"><strong>
                                  <?php 
                                echo 'School Code : <span class="text-primary"> ';
                                echo @$school_det[0]['school_code'] ;
                                ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <?php 
                                echo '</span> School Name : <span class="text-primary">';
                                echo @$school_det[0]['school_name'] ?></strong>
                            </div>
                            <div class="col-2"> </div>
                        </div>
                    </div>

                    </div>
                </div>
            </div>
        </div>
    </table>



<div id="schcode_wise_upload">
    <center>
    <div class="card">
        <div class="card-body mt-3">
            <div class="table-responsive-sm">
    <table border="0" cellspacing="0" cellpadding="4" align="center" class="table table-borderless">
        
        <tr>
            <td colspan="4" align="left"><span class="card-title">Participant Details</span></td>
            <td colspan="4" align="right" class="text-danger"> [ Max Size : 200KB (100x150) ]</td>
        </tr>
        <tr>
            <td align="left" ><strong>Sl.No</strong></td>
            <td align="left" ><strong>Reg.No</strong></td>
            <td align="left" ><strong>Admis.No</strong></td>
            <td align="left" ><strong>Participants_Name</strong></td>
            <td align="left" ><strong>Gender</strong></td>
            <td align="left" ><strong>Class</strong></td>
            <td align="left" ><strong>Upload_Photo</strong></td>
            <td align="left" ><strong>Photo</strong></td>
        </tr>
        <?php

        foreach(@$participant_det as $part_det)
        {
        // echo "<br />".var_dump($part_det)."<br />";
        $reg    =   @$part_det['admn_no'];
        $i++;
        ?>

        <tr style="vertical-align: middle;">
            <td width="37" align="left" >
                <?php
                @$cur_num++;
                echo $cur_num;
                ?>   </td>
            <td align="left" >
<?php echo @$part_det['participant_id']; ?>
            </td>
            <td width="94" align="left" >
<?php echo @$part_det['admn_no']; ?>
                <input type="hidden" name="hidadmn<?php echo $i?>" id="hidadmn<?php echo $i?>" value="<?php echo @$part_det['admn_no'] ?>" />
                <input type="hidden" name="hidschcode<?php echo $i?>" id="hidschcode<?php echo $i?>" value="<?php echo @$part_det['school_code'] ?>" />
            </td>
            <td align="left" >
<?php echo @$part_det['participant_name']; ?>
            </td>
            <td align="left" >
<?php echo @$part_det['gender']; ?>
            </td>
            <td align="left" >
<?php echo @$part_det['class']; ?>
            </td>
            <td align="left" >
                <?php
                //echo "-->".$i;
                $userfile = 'userfile' . $i;

                echo form_upload($userfile, '', 'id ="' . $userfile . '" class="form-control"');
                ?>
                <input type="hidden" value="<?php echo $userfile; ?>"  />
            </td>
            <td width="223" align="left" >
                <?php
                if (@$Photo['pic'][$reg]) {
                    $pic_data = @$Photo['pic'][$reg];
                    if ($pic_data['thumbnail']) {
                        echo "<img src='" . $pic_data['thumbnail'] . "' width='70' height='70' class='img-thumbnail'>";
                    } elseif ($pic_data['full']) {
                        echo "<img src='" . $pic_data['full'] . "' width='70' height='70' class='img-thumbnail'>";
                    } else {
                        echo "<div class='bg-light d-flex align-items-center justify-content-center' style='width:70px; height:70px; border:1px dashed #ccc; font-size:10px;'>No Photo</div>";
                    }
                } else {
                    echo "<div class='bg-light d-flex align-items-center justify-content-center' style='width:70px; height:70px; border:1px dashed #ccc; font-size:10px;'>No Photo</div>";
                }
                ?>  </td>
        </tr>
        <?php } ?>
        </div>
    </table>
    
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <input type="hidden" name="hidtot" id="hidtot" value="<?php echo $i; ?>" />
                <?php

                echo form_submit('upload', 'Upload','onClick="return check_upload_schoolwise();" class="btn btn-primary"');
                ?>
        </div>
    </div>
   </center>
</div>
</div>
</div>
</center>
</div>
<!-- Add this where you want the Bootstrap pagination to appear in your HTML template -->
<?= $bootstrap_pagination ?>

<!-- Your existing HTML content goes here -->

<!-- Example: -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
        <div class="paging"><?php echo @$pagination; ?></div>
        </div>
    </div>
</div>
<?php

} ?>



<?php
echo form_close();
?>

