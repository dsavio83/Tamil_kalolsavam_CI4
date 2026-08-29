<?php
echo form_open_multipart('pictures/pictures/upload', 
    array('id' => 'formreg_photo', 'name' => 'formreg_photo'));


//var_dump(@$item_det);
if(!empty(@$retdat)){
    for ($j = 0; $j < count(@$retdat); $j++) {
        $dat[@$retdat[$j]['school_code']] = @$retdat[$j]['school_name'];
    } } ?>
<input type="hidden" name="baseurl" id="baseurl" value="<?php echo base_url(); ?>" />
<input type="hidden" name="hiddtext" value="" />
<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="table table-borderless">
    <div class="row justify-content-center">
        <div class="col-md-12 col-sm-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                <div class="row">
                    <div class="col-12 mt-3">
                       <h5 class="card-title"> Admission Number Wise Photo Upload </h5>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-xl-2 col-md-2 col-sm-12 col-lg-12 mt-2">
                        School Code
                    </div>
                    <div class="col-xl-2 col-md-2 col-sm-12 col-lg-12 mt-2">
                        <?php echo form_input("txtSchoolCode", @$school_details[0]['school_code'], 'class="form-control" id="txtSchoolCode"  onkeypress="javascript:return numbersonly(this, event, false);" '); ?>
                    </div>

                    <div class="col-xl-2 col-md-2 col-sm-12 col-lg-12 mt-2">
                        Admission Number
                    </div>

                    <div class="col-xl-2 col-md-2 col-sm-12 col-lg-12 mt-2">
                        <?php echo form_input("txtRegNum", @$school_details[0]['school_code'], 'class="form-control" id="txtRegNum" '); ?>
                    </div>
                    <div class="col-xl-2 col-md-2 col-sm-12 col-lg-12 text-center mt-2">
                        <?php echo form_submit('GetDetails', 'GetDetails', 'onClick="javascript: return fetch_studdetails()" class="btn btn-primary"'); ?>
                    </div>
                </div>

</table>
    </div>
        </div>
            </div>
                </div></table>


<?php if(@$participant_det) {?>
<div id="regnum_wise_upload">
<table>
    <div class="row justify-content-center">
        <div class="col-md-12 col-sm-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                        <div class="row">
                            <div class="col-12 mt-3">
                               <h5 class="card-title"> Participant Details </h5>
                            </div>
                        </div>

                        <div class="row">
                            
                            <div class="col-8">
                                <div class="row mb-3">
                                    <div class="col-3">
                                        School Code
                                    </div>
                                     <div class="col-6">
                                        <?php echo @$participant_det[0]['school_code'] . " - " . @$participant_det[0]['school_name']; ?>

                                        <input type="hidden" name="hidschcode" id="hidschcode" value="<?php echo @$participant_det[0]['school_code'] ?>" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-3">
                                        Admission Number
                                    </div>
                                     <div class="col-6">
                                        <?php echo @$participant_det[0]['admn_no']; ?>
                                        <input type="hidden" name="hidadmn" id="hidadmn" value="<?php echo @$participant_det[0]['admn_no'] ?>" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-3">
                                        Participant Name
                                    </div>
                                     <div class="col-6">
                                        <?php echo @$participant_det[0]['participant_name']; ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-3">
                                        Item Name
                                    </div>
                                     <div class="col-6">
                                        <?php
                                            foreach (@$item_det as $item) {
                                                echo'';
                                                echo @$item['item_name'] . "<br/>";
                                            }
                                            //echo wordwrap(@$item['item_name'],30,"<br />\n");
                                            ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-3">
                                        
                                    </div>
                                     <div class="col-6">
                                        
                                    </div>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="row">
                                    <div class="col-12">
                                    <?php
                                    if (@$Photo && $Photo['thumbnail']) {
                                        echo "<img src='" . $Photo['thumbnail'] . "' width='100' height='100' class='img-thumbnail'>";
                                    } elseif (@$Photo && $Photo['full']) {
                                        echo "<img src='" . $Photo['full'] . "' width='100' height='100' class='img-thumbnail'>";
                                    } else {
                                        echo "<div class='bg-light d-flex align-items-center justify-content-center' style='width:100px; height:100px; border:1px dashed #ccc;'>No Photo</div>";
                                    }
                                    ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                    <?php echo form_upload('userfile', '"'); ?> 
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                            [ Max Size : 200KB (150x200)]
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                    <?php echo form_submit('upload', 'Upload','onClick="return check_upload()" class="btn btn-primary"'); ?>
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } ?>

<?php 
echo form_close();
?>
