<div id="divEntryForm">
<div class="pagetitle">
      <h1>Registrations</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">School Registration</li>
        </ol>
      </nav>
</div>

    <?php
    echo form_open('schools/registration/update', array('id' => 'formSchool')); ?>

<input type="hidden" name="hidTeachers" id="hidTeachers" value="1">

    <div class="row justify-content-center">
        <div class="col-md-12 col-sm-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"> School Details</h5>
                    <div class="table-responsive-sm">
                        <table>
                            <div class="row">
                                <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                    School Code
                                </div>

                                <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
                                    <div id="divSchoolCode">
                                        <?php
                                        if (trim($this->session->get('SCHOOL_CODE')) != 0 or @ $school_show == 'edit') {
                                            echo @$school_details[0]['school_code'];
                                        } else {
                                            echo form_input("txtSchoolCode", @$school_details[0]['school_code'], 'class="form-control" id="txtSchoolCode" onkeypress="javascript:return numbersonly(this, event, false);" onBlur="javascript:fetch_school_details()"');
                                        } ?>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                    School Name
                                </div>
                                <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
                                    <?php @print($school_details[0]['school_name']) ?>
                                </div>
                            </div>
        
                            
                            <div class="row">
                                 <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                    E-mail
                                </div>

                                  <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
                                    <?php echo (@$school_show == 'show') ? 
                                    @$school_details[0]['school_email'] : 
                                    form_input("txtSchoolEmail", @$school_details[0]['school_email'], 
                                    'class="form-control" id="txtSchoolEmail" maxlength="100" '); ?>
                                </div>

                                <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                    School Type
                                </div>

                                 <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
                                    <?php
                                        if (@$school_details[0]['school_type'] == 'G')
                                            $type = 'Government';
                                        else if (@$school_details[0]['school_type'] == 'A')
                                            $type = 'Aided';
                                        else if (@$school_details[0]['school_type'] == 'U')
                                            $type = 'Unaided';
                                        else
                                            $type = '';
                                        echo $type; ?>
                                </div>
                            </div>
        
                            <div class="row">
                                 <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                    Standard From
                                </div>

                                 <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
                                    <?php echo (@$school_show == 'show') ? 
                                    @$school_details[0]['class_start'] : 
                                    form_dropdown("txtStandardFrom", array(0 => '', 1 => 1, 5 => 5, 8 => 8, 11 => 11), @$school_details[0]['class_start'], 
                                    'id="txtStandardFrom" class="form-control" onChange="javascript:fncShowHideStd()"'); ?>
                                </div>

                                <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                    Standard To
                                </div>

                                 <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
                                    <?php echo (@$school_show == 'show') ? @$school_details[0]['class_end'] : form_dropdown("txtStandardTo", array(0 => '', 4 => 4, 5 => 5, 7 => 7, 10 => 10, 12 => 12), @$school_details[0]['class_end'], 'id="txtStandardTo" class="form-control"  onChange="javascript:fncShowHideStd()"'); ?>
                                </div>
                            </div>

                            <div class="row">
                                 <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                    Principal
                                </div>

                                 <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
                                    <?php echo (@$school_show == 'show') ? @$school_details[0]['principal_name'] : form_input("txtPrincipal", @$school_details[0]['principal_name'], 'class="form-control" id="txtPrincipal" onkeyup="javascript:this.value=this.value.toUpperCase();"'); ?>
                                </div>

                                <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                    Phone
                                </div>

                                 <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
                                    <?php echo (@$school_show == 'show') ? @$school_details[0]['principal_phone'] : form_input("txtPrincipalPhone", @$school_details[0]['principal_phone'], 'class="form-control" id="txtPrincipalPhone"  maxlength="11" onkeypress="javascript:return numbersonly(this, event, false);"'); ?>
                                </div>
                            </div>

                            <div class="row">
                                 <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                    Headmaster
                                </div>

                                 <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
                                    <?php echo (@$school_show == 'show') ? @$school_details[0]['hm_name'] : 
                                    form_input("txtHeadmaster", @$school_details[0]['hm_name'], 
                                    'class="form-control" id="txtHeadmaster" 
                                    onkeyup="javascript:this.value=this.value.toUpperCase();"'); ?>
                                </div>

                                <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                    Phone
                                </div>

                                 <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
                                    <?php echo (@$school_show == 'show') ? @$school_details[0]['hm_phone'] : 
                                    form_input("txtHeadmasterPhone", @$school_details[0]['hm_phone'], 
                                    'class="form-control" id="txtHeadmasterPhone"  maxlength="11" 
                                    onkeypress="javascript:return numbersonly(this, event, false);"'); ?>
                                </div>
                            </div>

                            <div class="row">
                                 
                                <div id="teachersRow">
                                    <?php
                                    if (@$school_show == 'show') {
                                        if (@$school_details[0]['teachers'] != '') {
                                            $teachers = explode("#@#", $school_details[0]['teachers']);
                                            echo '';
                                            for ($i = 0; $i < count($teachers); $i++) {
                                                $teachers_details = explode('#$#', $teachers[$i]);
                                                if (count($teachers_details) > 1) {
                                                    echo '<div class="row">
                                                    <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                                        Team Manager
                                                    </div>
                                                    <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
                                                        ' . @$teachers_details[0] . '</div>
                                                    <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                                        Phone </div>
                                                    <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">' 
                                                        . @$teachers_details[1] . '</div></div></div>';
                                                }
                                            }
                                            echo '';
                                        }
                                        ?>
                                    </div>
                                    <div class="clear"></div>
                                    <?php
                                } else if (@$school_show == 'edit') {
                                    if (@$school_details[0]['teachers'] != '') {
                                        $teachers = explode("#@#", $school_details[0]['teachers']);
                                        for ($i = 1; $i < count($teachers); $i++) {
                                            $teachers_details = explode('#$#', $teachers[$i - 1]);
                                            ?>
                                            <div class="clear_both"></div>
                                            <div class="teachersTextBox">
                                                <?php
                                                $name = 'txtTeacher_' . $i;
                                                $phone = 'txtPhone_' . $i;
                                                echo'<div class="row">
                                                    <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                                        Team Manager
                                                    </div>
                                                    <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">';
                                                echo form_input($name, $teachers_details[0], 'class="form-control" id="' . $name . '"  
                                                onkeyup="javascript:this.value=this.value.toUpperCase();"') . ' 
                                                </div>
                                                    <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                                        Phone </div>
                                                    <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2"> ' . 
                                                form_input($phone, @$teachers_details[1], 'class="form-control" id="' . 
                                                $phone . '" onkeypress="javascript:return numbersonly(this, event, false);" maxlength="11"') . "</div>";
                                                echo '</div> ';
                                            }
                                            //$teachersCount = (int) count($teachers) - 1;
                                                //echo "<script>$('hidTeachers').value=" . $teachersCount . "</script>";
                                            ?>
                                        </div>
                                        <div class="clear"></div>
                                        <div class="row">
                                            <div class="col-2"> </div>
                                            <div class="col-10">
                                        <a href="javascript:void(0)" onclick="javascript:addteacher();
                                                return false;">Add One</a>
                                        </div>
                                    </div>
                                           <?php
                                       } else {
                                           echo "<script>$('hidTeachers').value=0</script>";
                                           ?>
                                        </div>

                                        <div class="clear"></div>
                                        <div class="row">
                                            <div class="col-2"> </div>
                                            <div class="col-10">
                                        <a href="javascript:void(0)" onclick="javascript:addteacher();
                                                return false;">Add One</a>
                                            </div></div>
                                            </
                                           <?php
                                       }
                                   } else {
                                    echo'<div class="row">
                                        <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                            Team Manager 
                                        </div>
                                        <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">'; 
                                            echo form_input("txtTeacher_1", '', 'class="form-control" id="txtTeacher_1" onkeyup="javascript:this.value=this.value.toUpperCase();"') . '
                                        </div>
                                        <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                            3.Phone 
                                        </div>
                                        <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2"> 
                                            ' . form_input("txtPhone_1", '', 'class="form-control" id="txtPhone_1" onkeypress="javascript:return numbersonly(this, event, false);" maxlength="11"');
                                        echo'</div></div>';
                                       ?>
                                    </div>
                                    <div class="clear"></div>
                                    <a href="javascript:void(0)" onclick="javascript:addteacher();
                                            return false;">Add One</a>
                                       <?php  }  ?>                            
                            </div>

                            <div class="row mt-3">
                                 <div class="col-xl-3 col-md-3 col-sm-6 col-lg-2 mb-3">
                                    Total number of students
                                </div>

                                 <div class="col-xl-9 col-md-9 col-sm-6 col-lg-9 mb-2">
                                        <?php
                                        $LPDiv = (@$school_details[0]['strength_lp'] != 0 && @$school_details[0]['strength_lp'] != '') ? 'block' : 'none';
                                        $UPDiv = (@$school_details[0]['strength_up'] != 0 && @$school_details[0]['strength_up'] != '') ? 'block' : 'none';
                                        $HSDiv = (@$school_details[0]['strength_hs'] != 0 && @$school_details[0]['strength_hs'] != '') ? 'block' : 'none';
                                        $HSSDiv = (@$school_details[0]['strength_hss'] || @$school_details[0]['strength_vhss']) ? 'block' : 'none';
                                        $VHSCDiv = (@$school_details[0]['strength_vhss'] || @$school_details[0]['strength_hss']) ? 'block' : 'none'; ?>

                                        <div style="width:90px; float:left; display:<?php echo $LPDiv ?>; margin-right: 10px;" id="divLP">LP <?php echo (@$school_show == 'show') ? @$school_details[0]['strength_lp'] : form_input("txtTotalLP", @$school_details[0]['strength_lp'], 'class="form-control ml-3" id="txtTotalLP" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" onkeyup="javascript:addTotal()"'); ?> </div>

                                        <div style="width:90px; float:left; display:<?php echo $UPDiv ?>; margin-right: 10px;" id="divUP">UP <?php echo (@$school_show == 'show') ? @$school_details[0]['strength_up'] : form_input("txtTotalUP", @$school_details[0]['strength_up'], 'class="form-control ml-3" id="txtTotalUP" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" onkeyup="javascript:addTotal()"'); ?> </div>

                                        <div style="width:90px; float:left; display:<?php echo $HSDiv ?>; margin-right: 10px;" id="divHS">HS <?php echo (@$school_show == 'show') ? @$school_details[0]['strength_hs'] : form_input("txtTotalHS", @$school_details[0]['strength_hs'], 'class="form-control ml-3" id="txtTotalHS" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" onkeyup="javascript:addTotal()"'); ?> </div>

                                        <div style="width:90px; float:left; display:<?php echo $HSSDiv ?>; margin-right: 10px;" id="divHSS">HSS <?php echo (@$school_show == 'show') ? @$school_details[0]['strength_hss'] : form_input("txtTotalHSS", @$school_details[0]['strength_hss'], 'class="form-control ml-3" id="txtTotalHSS" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" onkeyup="javascript:addTotal()"'); ?> </div>

                                        <div style="width:90px; float:left; display:<?php echo $VHSCDiv ?>; margin-right: 10px;" id="divVHSC">VHSE <?php echo (@$school_show == 'show') ? @$school_details[0]['strength_vhss'] : form_input("txtTotalVHSS", @$school_details[0]['strength_vhss'], 'class="form-control ml-3" id="txtTotalVHSS" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" onkeyup="javascript:addTotal()"'); ?> </div>

                                        <div class="mr-3" style="width:120px; float:left;">Total <?php echo (@$school_show == 'show') ? @$school_details[0]['total_strength'] : form_input("txtTotal", @$school_details[0]['total_strength'], 'style="width:120px;" class="form-control ml-3" id="txtTotal" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" readonly="readonly" '); ?> </div>

                                        <input type="hidden" name="hidHSSNo" id="hidHSSNo" value="<?php print((@$school_details[0]['strength_hss']) ? @$school_details[0]['strength_hss'] : 0); ?>" />
                                        <input type="hidden" name="hidHSSNo" id="hidVHSSNo" value="<?php print((@$school_details[0]['strength_vhss']) ? @$school_details[0]['strength_vhss'] : 0); ?>" />
                                </div>
                            </div>

                             <div class="row">
                                 <div class="col-12 mb-2">
                                    <center>
                                    <?php
                                        if (isset($is_edit) and $is_edit != 'no') {
                                            echo (@$school_show == 'show') ?
                                            form_button('Edit', 'Edit', 'onClick="javascript:fncEditSchoolDeatils()" class="btn btn-primary mt-3"') :
                                              form_button('Continue', 'Continue', 'onClick="javascript:fncCheckSchoolDeatils()" class="btn btn-primary mt-3"');  }  
                                    ?>
                                        
                                       <center>
                                </div>
                            </div>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>





