

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
                                        if (trim($this->session->userdata('SCHOOL_CODE')) != 0 or @ $school_show == 'edit') {
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
                                            Phone 
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


<input type="hidden" name="hidSchoolId" id="hidSchoolId" value="
<?php echo (@$school_details[0]['school_code']) ? 
    @$school_details[0]['school_code'] : @$this->input->post('hidSchoolId'); ?>">
    <?php
    echo form_close(); ?>

    <?php
    echo "<div class='clear_both'></div>";
    echo form_open('schools/registration/save_participant', array('id' => 'formParticipant'));
    if (isset($is_edit) and $is_edit != 'no') {
        ?>
        <div id="divAddParticipants" style="display:<?php echo (@$school_show == 'show') ? 'block' : 'none'; ?>">
            <?php
            if (isset($save_update) and count($save_update) > 0) {
                foreach ($save_update as $err) {
                    print($err . '<br>');
                }
            } ?>
        <div class="row justify-content-center">
            <div class="col-md-12 col-sm-12 col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"> Entry Form / Add Participants</h5>
            
            <table width="100%" border="0" cellspacing="0" cellpadding="4" align="center">
                <tr>
                    <div class="row">
                    
                        <div class="col-6 mt-3">
                            Admission No.
                            <?php echo form_input("txtADNO", @$selected_participant[0]['admn_no'], 
                            'class="form-control" id="txtADNO" maxlength="6" 
                            onkeypress="javascript:this.value=this.value.toUpperCase();"'); ?>
                        </div>
                        
                        <div class="col-6 mt-3">
                            Name
                            <?php echo form_input("txtParticipantName", @$selected_participant[0]['participant_name'], 
                            'class="form-control" id="txtParticipantName"  
                            onkeyup="javascript:this.value=this.value.toUpperCase();"'); ?>
                        </div>
                        
                    </div>
                </tr>

                <tr>
                    <div class="row">
                        <div class="col-6 mt-3">
                            Class
                            <?php 
                            $class_array = array();
                                for ($i = @$school_details[0]['class_start']; $i <= @$school_details[0]['class_end']; $i++) {
                                    $class_array[$i] = $i;  }
                            echo form_dropdown("txtClass", $class_array, @$selected_participant[0]['class'], 
                            'id="txtClass" class="form-control"');  ?>
                        </div>
                        
                        <div class="col-6 mt-3">
                            Gender 
                            <?php echo form_dropdown("txtGender", array('B' => 'Boy', 'G' => 'Girl'), 
                            @$selected_participant[0]['gender'],
                            'id="txtGender" class="form-control"'); ?>
                        </div>
                    </div>
                </tr>
                <tr>
                    <div class="row">
                        <div class="col-6 mt-3">
                        Item Codes

                        <?php
                            $items_selected = $this->Registration_Model->get_participant_item_details(@$selected_participant[0]['participant_id'], @$selected_participant[0]['admn_no'], 'C', 'N');
                            echo '<div class="clear_both"></div> <div class="teachersTextBox"> <div class="row">';
                            for ($i = 1; $i <= 6; $i++) {
                                echo '<div class="col-lg-3 col-md-4 col-sm-4 col-xl-4 col-6 mt-3">';
                                echo form_input("txtItemCode_" . $i, @$items_selected[$i - 1]['item_code'], 'class="form-control" id="txtItemCode_' . $i . '" onkeypress="javascript:return numbersonly(this, event, false);" onKeyUp="javascript:change_text_max_to_focus(\'txtItemCode_' . $i . '\',\'txtItemCode_' . ($i + 1) . '\',this);" maxlength="4"');
                                echo '</div>';
                            }
                            for ($i = 7; $i <= 10; $i++) {
                                echo '<div class="col-lg-3 col-md-4 col-sm-4 col-xl-4 col-6 d-none mt-3">';
                                echo form_input("txtItemCode_" . $i, @$items_selected[$i - 1]['item_code'], 'class="form-control" id="txtItemCode_' . $i . '" onkeypress="javascript:return numbersonly(this, event, false);" onKeyUp="javascript:change_text_max_to_focus(\'txtItemCode_' . $i . '\',\'txtItemCode_' . ($i + 1) . '\',this);" maxlength="4"');
                                echo '</div>';
                            }
                            echo '</div> </div>';
                            ?>
                        </div>

                        <div class="col-6 mt-3">
                        Pinnany Codes

                            <?php
                                $pinnany_selected = $this->Registration_Model->get_participant_item_details(@$selected_participant[0]['participant_id'], @$selected_participant[0]['admn_no'], 'P');
                                echo '<div class="clear_both"></div><div class="teachersTextBox"> <div class="row">';

                                for ($i = 1; $i <= 5; $i++) {
                                    $isHidden = $i > 2 ? 'd-none' : ''; // Hide input fields 3 to 5

                                    echo '<div class="col-lg-4 col-md-4 col-sm-4 col-xl-4 col-6 mt-3">';
                                    echo '<div class="form-group ' . $isHidden . '">';
                                    echo form_input("txtPinnanyCode_" . $i, @$pinnany_selected[$i - 1]['item_code'], 'class="form-control" id="txtPinnanyCode_' . $i . '" onKeyUp="javascript:change_text_max_to_focus(\'txtPinnanyCode_' . $i . '\',\'txtPinnanyCode_' . ($i + 1) . '\',this);" maxlength="4"');
                                    echo '</div>';
                                    echo '</div>';
                                }

                                echo '</div> </div>';
                                ?>

                        
                        </div>
                    </div>
                </tr>

                <div style="width:0px;height:0px;overflow:hidden;">
                <?php echo form_dropdown("txtBank", $bank_details, @$selected_participant[0]
                ['bank_id'], 'id="txtBank" class="input-xlarge"'); ?>
                <?php echo form_input("txtBranch", @$selected_participant[0]
                ['branch_name'], 'class="input-xlarge" id="txtBranch"
                onkeyup="javascript:this.value=this.value.toUpperCase();"'); ?>
                <?php echo form_input("txtAccNo", @$selected_participant[0]['account_no'],
                'class="input-xlarge" id="txtAccNo" onkeypress="javascript:return numbersonly(this, event, false);"
                onCopy="return false" onDrag="return false" onDrop="return false" onPaste="return false" minlength="9" '); ?>
                <?php echo form_input("txtIfscCode", @$selected_participant[0]['ifsc_code'],
                'class="input-xlarge" id="txtIfscCode"
                onkeyup="javascript:this.value=this.value.toUpperCase();"  maxlength="11" '); ?>
                </div>

                <div class="row">
                    <div class="col-12 justify-content-center mt-4">
                        <center>
                        <?php echo (count(@$selected_participant) > 0 ) ? form_button('update_participant', 'Update Participant',
                        'id="update_participant" onClick="javascript:fncUpdateParticipant(\'' . $selected_participant[0]['admn_no'] .
                        '\')"  class="btn btn-primary" mt-4') . '&nbsp;&nbsp;' . form_button('Cancel', 'Cancel',
                        'onClick="javascript:fncCancelParticipant()"  class="btn btn-danger" mt-4') : form_button('save_participant',
                        'Save Participant', 'id="update_participant" onClick="javascript:fncSaveParticipant()" class="btn btn-primary" mt-4'); ?>
                    </center>
                    </div>
                </div>
        </table>

</div>
</div>
</div>
</div>
            <input type="hidden" name="hidSchoolId" id="hidSchoolId" value="<?php echo (@$school_details[0]['school_code']) ? @$school_details[0]['school_code'] : @$this->input->post('hidSchoolId'); ?>">
            <input type="hidden" name="hidADNO" id="hidADNO" value="">
            <?php
            
            ?>
        </div>
    <?php } ?>
    

      <?php
               if(!empty(@$participant_details)){

                   if((count(@$participant_details)>0) && (@$school_show == 'show')){
                    $disblock = "block";
                   } else {
                    $disblock = "none";
                   }

               } else {
                $disblock = "none";
               }
      ?>

    <div style="display:<?php echo $disblock; ?>">
        <?php
        
        ?>
        <div class="row justify-content-center">
            <div class="col-md-12 col-sm-12 col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"> Participants List</h5>
        
        <div class="table-responsive">
        <table width="60%" border="1px solid #CCCCCC" cellspacing="0" cellpadding="4" align="center" class="table">
            <tr align="center">
                <th style="border-left:1px solid #CCCCCC;" width="6%">Sl No</th>
                <th style="border-left:1px solid #CCCCCC;" width="6%">AD No.</th>
                <th style="border-left:1px solid #CCCCCC;" width="6%">Reg No.</th>
                <th style="border-left:1px solid #CCCCCC;" width="20%">Name of participant</th>
                <th style="border-left:1px solid #CCCCCC;" width="6%">Class</th>
                <th style="border-left:1px solid #CCCCCC;" width="8%">Boy / Girl </th>
                <th style="border-left:1px solid #CCCCCC;" width="30%" colspan="5">mouse over the code show item name</th>
                <?php if (isset($is_edit) and $is_edit != 'no') { ?>
                    <th style="border-left:1px solid #CCCCCC;" align="center" width="8%" align="center" >Edit </th>
                    <th style="border-left:1px solid #CCCCCC;" align="center" width="8%" align="center" >Delete</th>
                <?php } ?>
            </tr>
            <?php
            $count = 1;
            for ($j = 0; $j < count($participant_details); $j++) {
                $items = $this->Registration_Model->get_participant_item_details($participant_details[$j]['participant_id'], $participant_details[$j]['admn_no']);
                $classname = ($j % 2 == 0) ? 'table_row_second' : 'table_row_first'
                ?>
                <tr>
                    <td align="center"  style="border-left:1px solid #CCCCCC;" class="<?php echo $classname ?>"><?php echo $count; ?></td>
                    <td align="center"  style="border-left:1px solid #CCCCCC;" class="<?php echo $classname ?>"><?php echo $participant_details[$j]['admn_no'] ?></td>
                    <td align="center"  style="border-left:1px solid #CCCCCC;" class="<?php echo $classname ?>"><?php echo $participant_details[$j]['participant_id'] ?></td>
                    <td align="left"  style="border-left:1px solid #CCCCCC;" class="<?php echo $classname ?>"><?php echo $participant_details[$j]['participant_name'] ?></td>
                    <td align="center"  style="border-left:1px solid #CCCCCC;" class="<?php echo $classname ?>"><?php echo $participant_details[$j]['class'] ?></td>
                    <td align="center"  style="border-left:1px solid #CCCCCC;" class="<?php echo $classname ?>"><?php echo ($participant_details[$j]['gender'] == 'B') ? 'Boy' : 'Girl'; ?></td>
                    <?php
                    for ($k = 0; $k < 5; $k++) {
                        ?>
                        <td style="border-left:1px solid #CCCCCC;" width="4%" align="center" class="<?php echo $classname ?>">
                        <label style="cursor:pointer; <?php echo (@$items[$k]['spo_id'] != 0) ? 'color:#FF0000;' : '' ?>" 
                        title="<?php echo (@$items[$k]['item_name']) ? 
                        ((@$items[$k]['item_type'] == 'P') ? @$items[$k]['item_name'] . '(Pinnany)' : @$items[$k]['item_name']) : ''; ?>">
                        <?php echo (@$items[$k]['item_code']) ? @$items[$k]['item_code'] . '(' . @$items[$k]['item_type'] . ')' : ''; ?></label>
                        </td>
                        <?php
                    }
                    ?>
                    <?php if (isset($is_edit) and $is_edit != 'no') { ?>
                        <td align="center"  style="border-left:1px solid #CCCCCC;" align="center" class="<?php echo $classname ?>">
                            <a href="javascript:void(0)" onClick="javascript:editParticipant('<?php echo $participant_details[$j]['admn_no'] ?>')">
                                <img src="<?php echo base_url(false) ?>images/edit.png" border="0">
                            </a>
                        </td>
                        <td align="center"  style="border-left:1px solid #CCCCCC;" style="border-rightt:1px solid #CCCCCC;" align="center" class="<?php echo $classname ?>">
                            <a href="javascript:void(0)" onClick="javascript:deleteParticipant('<?php echo $participant_details[$j]['admn_no'] ?>')">
                                <img src="<?php echo base_url(false) ?>images/delete.png" border="0">
                            </a>
                        </td>
                    <?php } ?>
                </tr>
                <?php
                $count++;
            }
            ?>
        </table>
        </div>
    </div>
</div>
</div>
</div>
        <?php
        
        ?>
    </div>

    <?php
    echo form_close();
    ?>



    <?php
       if(!empty(@$group_details['group_array'])){
    if (count(@$group_details['group_array']) > 0) {

        echo form_open('schools/registration/update_group_captain', array('id' => 'formGroupLeader'));
        ?>
        <div style="display:<?php echo (count(@$group_details['group_array']) > 0 && @$school_show == 'show') ? 'block' : 'none'; ?>; padding:10px 0 10px; 0">
            <?php
            
            ?>
            <div class="row justify-content-center">
            <div class="col-md-12 col-sm-12 col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"> Captains List</h5>
            
            <div class="table-responsive-sm">
            <table width="100%" border="0" cellspacing="0" cellpadding="4" align="center">
                <tr>
                    <th>Item Code - Item Names</th>
                    <th>Participants</th>
                </tr>
                <?php
                $count = 1;
                $group_array = $group_details['group_array'];
                $group_leader = $group_details['group_leader'];
                $group_participant_array = $group_details['group_participant_array'];
                foreach ($group_array as $groups) {
                    $classname = ($j % 2 == 0) ? 'table_row_second' : 'table_row_first'
                    ?>
                    <tr>
                        <td width="30%" class="<?php echo $classname ?>">
                            <?php echo $groups['item_code'] . ' - ' . $groups['item_name'] ?>
                            <input type="hidden" name="hidGrpItemCode_<?php echo $count ?>" id="hidGrpItemCode_<?php echo $count ?>" 
                            value="<?php echo $groups['item_code'] ?>">
                        </td>
                        <td width="70%" class="<?php echo $classname ?>"> 
                            <?php
                            if (isset($is_edit) and $is_edit != 'no') {
                                print(form_dropdown('cmbGrpParticipant_' . $count, $group_participant_array[$groups['item_code']], @$group_leader[$groups['item_code']],
                                'id="cmbGrpParticipant_"' . $count . '
                                class="form-control" onChange="javascript:$(\'divCaptainSubmit\').style.display = \'block\'"'));
                            } else {
                                print(@$group_participant_array[$groups['item_code']][@$group_leader[$groups['item_code']]]);
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                    $count++;
                }
                ?>
                <input type="hidden" name="hidGrpCount" id="hidGrpCount" value="<?php echo $count ?>">
                <tr>
                    <td align="center" colspan="4"><div id="divCaptainSubmit" style="display:none">
                            <?php
                            if (isset($is_edit) and $is_edit != 'no') {
                                echo form_submit('group_submit', 'Submit','class="btn btn-primary"');
                            }
                            ?>
                        </div></td>
                </tr>

            </table>
            </div></div></div></div></div></div>
            <?php
            
            ?>
        </div>
        <input type="hidden" name="hidSchoolId" id="hidSchoolId" value="<?php echo (@$school_details[0]['school_code']) ?
        @$school_details[0]['school_code'] : @$this->input->post('hidSchoolId'); ?>">
        <?php
        echo form_close();
    } }
    ?>

    <?php

    if(!empty(@$participant_item_list)){

    if (count(@$participant_item_list) > 0) { ?>
        <div style="display:<?php echo (@$school_show == 'show') ? 'block' : 'none'; ?>; padding:10px 0 10px; 0">
            <?php
            
            ?>
            
            <div class="row justify-content-center">
            <div class="col-md-12 col-sm-12 col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"> Item wise Participants</h5>
            
            <div class="table-responsive-sm">
            <table width="100%" border="0" cellspacing="0" cellpadding="4" align="center">
                
                <tr>
                    <th align="center" width="5%">SL No</th>
                    <th width="10%">Item Code</th>
                    <th width="20%">Item Name</th>
                    <th align="center" width="15%">No of Participants</th>
                    <th align="center" width="10%">No of Pinnany</th>

                    <th width="20%">Name of Participant</th>
                </tr>
                <?php
                $count = 0;
                foreach ($participant_item_list as $item_list) {
                    $count++;
                    $class_name = ($count % 2 == 0) ? 'table_row_second' : 'table_row_first';
                    ?>
                    <tr>
                        <td  align="center" class="<?php echo $class_name; ?>"><?php echo $count; ?></td>
                        <td class="<?php echo $class_name; ?>"><?php echo $item_list['item_code'] ?></td>
                        <td class="<?php echo $class_name; ?>"><?php echo $item_list['item_name'] ?></td>
                        <td align="center" class="<?php echo $class_name; ?>"><?php echo $item_list['cnt_participant'] ?></td>
                        <td align="center" class="<?php echo $class_name; ?>"><?php echo $item_list['cnt_pinnany'] ?></td>
                        <td class="<?php echo $class_name; ?>"><?php echo @$item_list['captian'] ?></td>
                    </tr>
                    <?php
                }
                ?>

            </table>
            <!-- </div> -->
            <?php
            
            ?>
        </div></div></div></div></div>
    <?php }
}
?>




    <?php
    echo form_open('schools/registration/finalize_data', array('id' => 'formFinalize'));
    ?>
    <div style="display:<?php echo (@$school_show == 'show') ? 'block' : 'none'; ?>; padding:10px 0 10px; 0">
        <?php
        
        ?>
        <div class="row justify-content-center">
            <div class="col-md-12 col-sm-12 col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"> Report</h5>
            
            <div class="table-responsive-sm">
        <table>
            
            <tr>
                
                <td>
                    <?php
                    if (isset($is_edit) and $is_edit != 'no' and @ $school_details[0]['is_create_report'] == 'Y') {
                        ?>

                        <label style="color:#990000"><strong>Warning: Once confirmed the entry details cannot be modified. Before confirming ensure the report has been certified by the Headmaster/Principal.</strong></label>
                        
                        <?php
                        echo form_button('finalize_submit', 'Confirm', 'onClick="javascript:confirm_school_data();" class="btn btn-success mt-3"');
                    }
                    echo '&nbsp;&nbsp;' . form_button('create_report', 'Create Report', 'onClick="javascript:create_school_report();" class="btn btn-primary mt-3"');
                    echo '&nbsp;&nbsp;' . form_button('report_html', 
					'Report HTML', 'onClick="javascript:create_school_report_html();" class="btn btn-info mt-3"');
                    
                    ?>
                </td>
            </tr>

        </table>
        <?php
        
        ?>
    </div></div></div></div></div>
    <input type="hidden" name="hidSchoolId" id="hidSchoolId" value="<?php echo (@$school_details[0]['school_code']) ? @$school_details[0]['school_code'] : @$this->input->post('hidSchoolId'); ?>">
    <?php
    echo form_close();
    ?>
</div>
<?php if (@$error_msg) { ?>
    <script language="JavaScript">
        window.onload = alert('<?php echo @$error_msg; ?>');
    </script>
    <?php
}?>

<script language="JavaScript">
    function create_school_report_html() {
        document.getElementById('formSchool').action = '<?php echo base_url(); ?>schools/registration_report_html';
        document.getElementById('formSchool').submit();
    }
</script>


