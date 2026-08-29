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
	<div class="col-12 mb-3">
		<button type="button" class="btn btn-info btn-sm me-2" id="schoolProfileBtn">
			<i class="fas fa-eye"></i> <span id="schoolProfileBtnText">School Profile</span>
		</button>

		<button type="button" class="btn btn-success btn-sm me-2" data-bs-toggle="modal"
			data-bs-target="#participantModal" onclick="clearForm()" id="addParticipantBtn">
			<i class="fas fa-plus"></i> Add Participant
		</button>

		<!-- <button type="button" class="btn btn-primary btn-sm" onclick="openRegistrationReport()" id="reportBtn">
			<i class="fas fa-file-alt"></i> Report
		</button> -->
	</div>

	<div class="row justify-content-center" id="schoolDetailsSection">
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
										if (trim($this->session->get('SCHOOL_CODE')) != 0 or @$school_show == 'edit') {
											echo @$school_details[0]['school_code'];
										} else {
											echo form_input("txtSchoolCode", (string) (@$school_details[0]['school_code'] ?? ''), 'class="form-control" id="txtSchoolCode" onkeypress="javascript:return numbersonly(this, event, false);" onBlur="javascript:fetch_school_details()"');
										} ?>
									</div>
								</div>
								<div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
									School Name
								</div>
								<div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
									<?php @print ($school_details[0]['school_name']) ?>
								</div>
							</div>


							<div class="row">
								<div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
									E-mail
								</div>

								<div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
									<?php echo (@$school_show == 'show') ?
										@$school_details[0]['school_email'] :
										form_input(
											"txtSchoolEmail",
											(string) (@$school_details[0]['school_email'] ?? ''),
											'class="form-control" id="txtSchoolEmail" maxlength="100" '
										); ?>
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
										form_dropdown(
											"txtStandardFrom",
											array(0 => '', 1 => 1, 5 => 5, 8 => 8, 11 => 11),
											@$school_details[0]['class_start'],
											'id="txtStandardFrom" class="form-control" onChange="javascript:fncShowHideStd()"'
										); ?>
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
									<?php echo (@$school_show == 'show') ? @$school_details[0]['principal_name'] : form_input("txtPrincipal", (string) (@$school_details[0]['principal_name'] ?? ''), 'class="form-control" id="txtPrincipal" onkeyup="javascript:this.value=this.value.toUpperCase();"'); ?>
								</div>

								<div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
									Phone
								</div>

								<div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
									<?php echo (@$school_show == 'show') ? @$school_details[0]['principal_phone'] : form_input("txtPrincipalPhone", (string) (@$school_details[0]['principal_phone'] ?? ''), 'class="form-control" id="txtPrincipalPhone"  maxlength="11" onkeypress="javascript:return numbersonly(this, event, false);"'); ?>
								</div>
							</div>

							<div class="row">
								<div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
									Headmaster
								</div>

								<div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
									<?php echo (@$school_show == 'show') ? @$school_details[0]['hm_name'] :
										form_input(
											"txtHeadmaster",
											(string) (@$school_details[0]['hm_name'] ?? ''),
											'class="form-control" id="txtHeadmaster" 
                                    onkeyup="javascript:this.value=this.value.toUpperCase();"'
										); ?>
								</div>

								<div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
									Phone
								</div>

								<div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">
									<?php echo (@$school_show == 'show') ? @$school_details[0]['hm_phone'] :
										form_input(
											"txtHeadmasterPhone",
											(string) (@$school_details[0]['hm_phone'] ?? ''),
											'class="form-control" id="txtHeadmasterPhone"  maxlength="11" 
                                    onkeypress="javascript:return numbersonly(this, event, false);"'
										); ?>
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
													echo '<div class="row">
                                                    <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                                        Team Manager
                                                    </div>
                                                    <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2">';
													echo form_input($name, (string) ($teachers_details[0] ?? ''), 'class="form-control" id="' . $name . '"  
                                                onkeyup="javascript:this.value=this.value.toUpperCase();"') . ' 
                                                </div>
                                                    <div class="col-xl-2 col-md-2 col-sm-6 col-lg-2 mb-2">
                                                        Phone </div>
                                                    <div class="col-xl-4 col-md-4 col-sm-6 col-lg-4 mb-2"> ' .
														form_input($phone, (string) (@$teachers_details[1] ?? ''), 'class="form-control" id="' .
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
											</div>
										</div>
										</ <?php
										}
									} else {
										echo '<div class="row">
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
										echo '</div></div>';
										?>
							</div>
							<div class="clear"></div>
							<a href="javascript:void(0)" onclick="javascript:addteacher();
											return false;">Add One</a>
					<?php } ?>
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

						<div style="width:90px; float:left; display:<?php echo $LPDiv ?>; margin-right: 10px;"
							id="divLP">LP
							<?php echo (@$school_show == 'show') ? @$school_details[0]['strength_lp'] : form_input("txtTotalLP", (string) (@$school_details[0]['strength_lp'] ?? ''), 'class="form-control ml-3" id="txtTotalLP" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" onkeyup="javascript:addTotal()"'); ?>
						</div>

						<div style="width:90px; float:left; display:<?php echo $UPDiv ?>; margin-right: 10px;"
							id="divUP">UP
							<?php echo (@$school_show == 'show') ? @$school_details[0]['strength_up'] : form_input("txtTotalUP", (string) (@$school_details[0]['strength_up'] ?? ''), 'class="form-control ml-3" id="txtTotalUP" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" onkeyup="javascript:addTotal()"'); ?>
						</div>

						<div style="width:90px; float:left; display:<?php echo $HSDiv ?>; margin-right: 10px;"
							id="divHS">HS
							<?php echo (@$school_show == 'show') ? @$school_details[0]['strength_hs'] : form_input("txtTotalHS", (string) (@$school_details[0]['strength_hs'] ?? ''), 'class="form-control ml-3" id="txtTotalHS" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" onkeyup="javascript:addTotal()"'); ?>
						</div>

						<div style="width:90px; float:left; display:<?php echo $HSSDiv ?>; margin-right: 10px;"
							id="divHSS">HSS
							<?php echo (@$school_show == 'show') ? @$school_details[0]['strength_hss'] : form_input("txtTotalHSS", (string) (@$school_details[0]['strength_hss'] ?? ''), 'class="form-control ml-3" id="txtTotalHSS" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" onkeyup="javascript:addTotal()"'); ?>
						</div>

						<div style="width:90px; float:left; display:<?php echo $VHSCDiv ?>; margin-right: 10px;"
							id="divVHSC">VHSE
							<?php echo (@$school_show == 'show') ? @$school_details[0]['strength_vhss'] : form_input("txtTotalVHSS", (string) (@$school_details[0]['strength_vhss'] ?? ''), 'class="form-control ml-3" id="txtTotalVHSS" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" onkeyup="javascript:addTotal()"'); ?>
						</div>

						<div class="mr-3" style="width:120px; float:left;">Total
							<?php echo (@$school_show == 'show') ? @$school_details[0]['total_strength'] : form_input("txtTotal", (string) (@$school_details[0]['total_strength'] ?? ''), 'style="width:120px;" class="form-control ml-3" id="txtTotal" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" readonly="readonly" '); ?>
						</div>

						<input type="hidden" name="hidHSSNo" id="hidHSSNo"
							value="<?php print ((@$school_details[0]['strength_hss']) ? @$school_details[0]['strength_hss'] : 0); ?>" />
						<input type="hidden" name="hidHSSNo" id="hidVHSSNo"
							value="<?php print ((@$school_details[0]['strength_vhss']) ? @$school_details[0]['strength_vhss'] : 0); ?>" />
					</div>
				</div>

				<div class="row">
					<div class="col-12 mb-2">
						<center>
							<?php
							if (isset($is_edit) and $is_edit != 'no') {
								echo (@$school_show == 'show') ?
									form_button('Edit', 'Edit', 'onClick="javascript:fncEditSchoolDeatils()" class="btn btn-primary mt-3"') :
									form_button('Continue', 'Continue', 'onClick="javascript:fncCheckSchoolDeatils()" class="btn btn-primary mt-3"');
							}
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
echo form_open('schools/registration/save_participant', array('id' => 'formParticipant', 'enctype' => 'multipart/form-data'));
?>
<input type="hidden" name="hidSchoolId" id="hidSchoolId"
	value="<?php echo (@$school_details[0]['school_code']) ? @$school_details[0]['school_code'] : @$this->input->post('hidSchoolId'); ?>">
<input type="hidden" name="hidADNO" id="hidADNO" value="">
<?php
if (isset($is_edit) and $is_edit != 'no') {
	?>



	<!-- Participant Modal -->
	<div class="modal fade" id="participantModal" tabindex="-1" aria-labelledby="participantModalLabel" aria-hidden="true"
		data-bs-backdrop="static" data-bs-keyboard="false">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="participantModalLabel">Add Participant</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
						onclick="forceRemoveModalBackdrop()">X</button>
				</div>
				<div class="modal-body">
					<!-- Two Column Layout: Left (col-8) for inputs, Right (col-4) for class-based items -->
					<div class="row">
						<!-- Left Column: Participant Details (col-8) -->
						<div class="col-md-8">
							<div class="card border-0">
								<div class="card-body">
									<h6 class="card-title mb-3">Participant Details</h6>

									<!-- Basic Information -->
									<div class="row mb-3">
										<div class="col-md-6">
											<label for="txtADNO" class="form-label">Admission No.</label>
											<?php echo form_input(
												"txtADNO",
												'',
												'class="form-control" id="txtADNO" maxlength="6" onkeypress="javascript:this.value=this.value.toUpperCase();"'
											); ?>
										</div>
										<div class="col-md-6">
											<label for="txtParticipantName" class="form-label">Name</label>
											<?php echo form_input(
												"txtParticipantName",
												'',
												'class="form-control" id="txtParticipantName" onkeyup="javascript:this.value=this.value.toUpperCase();"'
											); ?>
										</div>
									</div>

									<div class="row mb-3">
										<div class="col-md-6">
											<label for="txtClass" class="form-label">Class</label>
											<?php
											$class_array = array('' => 'Select Class');
											for ($i = @$school_details[0]['class_start']; $i <= @$school_details[0]['class_end']; $i++) {
												$class_array[$i] = $i;
											}
											echo form_dropdown(
												"txtClass",
												$class_array,
												'',
												'id="txtClass" class="form-control" onchange="loadItemsByClass()"'
											); ?>
										</div>
										<div class="col-md-6">
											<label for="txtGender" class="form-label">Gender</label>
											<?php echo form_dropdown(
												"txtGender",
												array('' => 'Select Gender') + array('B' => 'Boy', 'G' => 'Girl'),
												'',
												'class="form-control" id="txtGender" onchange="loadItemsByClass()"'
											); ?>
										</div>
									</div>

									<!-- Item Codes Section -->
									<div class="mb-3">
										<label class="form-label">Item Codes</label>
										<div class="teachersTextBox">
											<div class="row" id="itemCodesContainer">
												<?php
												for ($i = 1; $i <= 5; $i++) {
													echo '<div class="col-sm-6 col-xl-2 col-md-4 mb-2">';
													echo '<div class="input-group">';
													echo form_input("txtItemCode_" . $i, '', 'class="form-control item-code-input" id="txtItemCode_' . $i . '" onkeypress="javascript:return numbersonly(this, event, false);" onKeyUp="javascript:change_text_max_to_focus(\'txtItemCode_' . $i . '\',\'txtItemCode_' . ($i + 1) . '\',this);" maxlength="4" placeholder=""');
													//echo '<button class="btn btn-outline-secondary btn-sm select-item-btn" type="button" data-input="txtItemCode_' . $i . '">Select</button>';
													echo '</div>';
													echo '<div class="item-details" id="itemDetails_' . $i . '" style="display: none; margin-top: 5px; padding: 5px; background-color: #f8f9fa; border-radius: 4px;">';
													echo '<small class="text-muted" id="itemName_' . $i . '"></small>';
													echo '</div>';
													echo '</div>';
												}
												?>
											</div>
										</div>
									</div>



									<div class="mb-3">
										<div class="mb-2">
											<!-- <input class="form-check-input" type="checkbox" id="enablePinnanyCodes"> -->
											<!-- <label class="form-check-label" for="enablePinnanyCodes"> -->
												Pinnany Codes
											<!-- </label> -->
										</div>

										<div class="teachersTextBox">
											<div class="row" id="pinnanyCodesContainer">
												<?php
												for ($i = 1; $i <= 3; $i++) {
													echo '<div class="col-sm-4 col-xl-2 col-md-4 mb-2">';
													echo '<div class="input-group">';
													echo form_input(
														"txtPinnanyCode_" . $i,
														'',
														'class="form-control pinnany-code-input" id="txtPinnanyCode_' . $i . '" onKeyUp="javascript:change_text_max_to_focus(\'txtPinnanyCode_' . $i . '\',\'txtPinnanyCode_' . ($i + 1) . '\',this);" maxlength="4" placeholder="" '
													);
													echo '</div>';
													echo '<div class="pinnany-details" id="pinnanyDetails_' . $i . '" style="display: none; margin-top: 5px; padding: 5px; background-color: #f8f9fa; border-radius: 4px;">';
													echo '<small class="text-muted" id="pinnanyName_' . $i . '"></small>';
													echo '</div>';
													echo '</div>';
												}
												?>
											</div>
										</div>
									</div>

									<!-- Photo Upload -->
									<div class="mb-3">
										<label for="userfile" class="form-label">Photo Upload</label>
										<?php echo form_upload("userfile", '', 'class="form-control" id="userfile" accept="image/*" onchange="previewPhoto(this)"'); ?>
										<div id="photoPreviewContainer" style="margin-top: 10px;">
											<div id="photoPreview" class="text-center">
												<img id="currentPhoto" src="" alt="" class="img-thumbnail"
													style="max-width: 150px; max-height: 150px; display: none;" />
												<div id="noPhotoMessage" class="text-muted">No photo uploaded</div>
												<button type="button" id="removePhotoBtn"
													class="btn btn-sm btn-outline-danger mt-2"
													onclick="removeCurrentPhoto()" style="display: none;">Remove
													Photo</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- Right Column: Class-based Items (col-4) -->
						<div class="col-md-4">
							<div class="card border-0">
								<div class="card-body">
									<h6 class="card-title mb-3">Available Items</h6>
									<div id="availableItemsContainer" style="max-height: 600px; overflow-y: auto;">
										<div class="text-center text-muted" id="noItemsMessage">
											<p>Please select a class and gender to view available items.</p>
										</div>
										<div id="itemsListContainer" style="display: none;">
											<!-- Items will be loaded here dynamically -->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Hidden form fields for bank details -->
					<div style="width:0px;height:0px;overflow:hidden;">
						<?php echo form_dropdown("txtBank", $bank_details, '', 'id="txtBank" class="input-xlarge"'); ?>
						<?php echo form_input("txtBranch", '', 'class="input-xlarge" id="txtBranch" onkeyup="javascript:this.value=this.value.toUpperCase();"'); ?>
						<?php echo form_input("txtAccNo", '', 'class="input-xlarge" id="txtAccNo" onkeypress="javascript:return numbersonly(this, event, false);" onCopy="return false" onDrag="return false" onDrop="return false" onPaste="return false" minlength="9"'); ?>
						<?php echo form_input("txtIfscCode", '', 'class="input-xlarge" id="txtIfscCode" onkeyup="javascript:this.value=this.value.toUpperCase();" maxlength="11"'); ?>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
						onclick="forceRemoveModalBackdrop()">Cancel</button>
					<button type="button" id="save_participant_btn" onclick="fncSaveParticipant()"
						class="btn btn-primary">Save Participant</button>
				</div>
			</div>
		</div>
	</div>


	<style>
		/* Hide any remaining modal backdrops */
		.modal-backdrop {
			display: none !important;
			opacity: 0 !important;
			pointer-events: none !important;
		}

		/* Ensure body doesn't have modal-open class issues */
		body.modal-open {
			overflow: auto !important;
			padding-right: 0 !important;
		}

		/* Enhanced modal styling with shadow and backdrop blur */
		.modal-content {
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
			border: none !important;
			border-radius: 12px !important;
			backdrop-filter: blur(10px) !important;
			background: rgba(255, 255, 255, 0.95) !important;
		}

		/* Modal backdrop with blur effect */
		.modal-backdrop.show {
			background: rgba(0, 0, 0, 0.6) !important;
			backdrop-filter: blur(8px) !important;
			-webkit-backdrop-filter: blur(8px) !important;
		}

		/* Modal dialog enhanced positioning */
		.modal-dialog {
			transform: translateY(-20px) !important;
			transition: transform 0.3s ease-out !important;
		}

		.modal.show .modal-dialog {
			transform: translateY(0) !important;
		}

		/* Modal header styling */
		.modal-header {
			border-bottom: 1px solid rgba(0, 0, 0, 0.1) !important;
			border-radius: 12px 12px 0 0 !important;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
			color: white !important;
			padding: 1rem 1.5rem !important;
		}

		.modal-header .modal-title {
			font-weight: 600 !important;
			font-size: 1.25rem !important;
		}

		.modal-header .btn-close {
			background: rgba(255, 255, 255, 0.2) !important;
			border-radius: 50% !important;
			width: 32px !important;
			height: 32px !important;
			opacity: 0.8 !important;
			transition: all 0.2s ease !important;
		}

		.modal-header .btn-close:hover {
			background: rgba(255, 255, 255, 0.3) !important;
			opacity: 1 !important;
			transform: rotate(90deg) !important;
		}

		/* Modal body styling */
		.modal-body {
			padding: 2rem !important;
			background: rgba(255, 255, 255, 0.95) !important;
			border-radius: 0 0 12px 12px !important;
		}

		/* Modal footer styling */
		.modal-footer {
			border-top: 1px solid rgba(0, 0, 0, 0.1) !important;
			padding: 1.5rem 2rem !important;
			background: rgba(248, 250, 252, 0.8) !important;
			border-radius: 0 0 12px 12px !important;
		}

		/* Button enhancements */
		.modal-footer .btn {
			border-radius: 8px !important;
			padding: 0.5rem 1.5rem !important;
			font-weight: 500 !important;
			transition: all 0.2s ease !important;
			border: none !important;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
		}

		.modal-footer .btn-primary {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
			color: white !important;
		}

		.modal-footer .btn-primary:hover {
			transform: translateY(-2px) !important;
			box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4) !important;
		}

		.modal-footer .btn-secondary {
			background: #6c757d !important;
			color: white !important;
		}

		.modal-footer .btn-secondary:hover {
			transform: translateY(-2px) !important;
			box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4) !important;
		}

		/* Form control enhancements */
		.modal-body .form-control {
			border-radius: 8px !important;
			border: 2px solid #e9ecef !important;
			padding: 0.75rem 1rem !important;
			transition: all 0.2s ease !important;
			background: rgba(255, 255, 255, 0.9) !important;
		}

		.modal-body .form-control:focus {
			border-color: #667eea !important;
			box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
			background: white !important;
		}

		/* Animation for modal appearance */
		@keyframes modalFadeIn {
			from {
				opacity: 0;
				transform: scale(0.9) translateY(-50px);
			}

			to {
				opacity: 1;
				transform: scale(1) translateY(0);
			}
		}

		.modal.show .modal-dialog {
			animation: modalFadeIn 0.3s ease-out !important;
		}

		/* Enhanced styles for the new two-column layout */
		.modal-xl {
			max-width: 1200px !important;
		}

		/* Item selection styling */
		.item-selectable {
			transition: all 0.2s ease !important;
			border: 2px solid transparent !important;
		}

		.item-selectable:hover {
			background-color: #f8f9fa !important;
			border-color: #667eea !important;
			transform: translateY(-2px) !important;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
		}

		.item-details,
		.pinnany-details {
			font-size: 0.875rem !important;
			border-left: 3px solid #667eea !important;
		}

		/* Input group styling */
		.input-group .btn {
			border-radius: 0 0.375rem 0.375rem 0 !important;
		}

		/* Clear button styling */
		.btn-outline-danger {
			border-color: #dc3545 !important;
		}

		.btn-outline-danger:hover {
			background-color: #dc3545 !important;
			color: white !important;
		}

		/* Loading spinner */
		.spinner-border-sm {
			width: 1rem !important;
			height: 1rem !important;
		}

		/* Responsive adjustments */
		@media (max-width: 1200px) {
			.modal-xl {
				max-width: 95% !important;
			}
		}

		@media (max-width: 768px) {
			.modal-dialog {
				margin: 0.5rem !important;
			}

			.col-md-8,
			.col-md-4 {
				margin-bottom: 1rem !important;
			}
		}

		/* Enhanced form styling */
		.form-label {
			font-weight: 600 !important;
			color: #495057 !important;
			margin-bottom: 0.5rem !important;
		}

		/* Card styling for sections */
		.card {
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
			border: 1px solid rgba(0, 0, 0, 0.125) !important;
			border-radius: 8px !important;
		}

		/* Badge styling */
		.badge {
			font-size: 0.75rem !important;
			padding: 0.25rem 0.5rem !important;
		}

		/* List group item styling */
		.list-group-item {
			border: 1px solid rgba(0, 0, 0, 0.125) !important;
			margin-bottom: 0.5rem !important;
		}

		/* Photo preview styling */
		#photoPreviewContainer {
			border: 1px solid #dee2e6 !important;
			border-radius: 8px !important;
			padding: 1rem !important;
			background-color: #f8f9fa !important;
		}

		/* Disabled input styling */
		.item-code-input:disabled {
			background-color: #e9ecef !important;
			opacity: 1 !important;
			cursor: not-allowed !important;
		}

		/* Item selection styling */
		.festival-group {
			border: 1px solid #e9ecef !important;
			border-radius: 8px !important;
			padding: 10px !important;
			background-color: #f8f9fa !important;
		}

		.festival-title {
			border-bottom: 2px solid #667eea !important;
			padding-bottom: 5px !important;
			margin-bottom: 10px !important;
			font-size: 1rem !important;
		}

		.available-item {
			background-color: #f8fff8 !important;
			border: 2px solid #28a745 !important;
			transition: all 0.2s ease !important;
		}

		.available-item:hover {
			background-color: #e8f5e8 !important;
			transform: translateY(-2px) !important;
			box-shadow: 0 4px 8px rgba(40, 167, 69, 0.2) !important;
		}

		.disabled-item {
			background-color: #fff8f8 !important;
			border: 2px solid #dc3545 !important;
			opacity: 0.7 !important;
			cursor: not-allowed !important;
		}

		.items-container {
			max-height: 500px !important;
			overflow-y: auto !important;
			padding: 5px !important;
		}

		/* Enhanced list group styling */
		.list-group-item {
			padding: 10px 15px !important;
			margin-bottom: 5px !important;
			border-radius: 8px !important;
			transition: all 0.2s ease !important;
		}

		/* Badge positioning */
		.list-group-item .badge {
			margin-left: 5px !important;
			font-size: 0.7rem !important;
		}

		/* Loading state */
		.spinner-border-sm {
			width: 1rem !important;
			height: 1rem !important;
			margin-right: 10px !important;
		}

		/* Text styling */
		.text-muted {
			color: #6c757d !important;
		}

		/* Alert styling */
		.alert {
			padding: 10px 15px !important;
			margin-bottom: 15px !important;
			border-radius: 8px !important;
		}

		.alert-danger {
			color: #721c24 !important;
			background-color: #f8d7da !important;
			border-color: #f5c6cb !important;
		}
	</style>

	<script>
		// Global functions - MUST be defined first
		window.forceRemoveModalBackdrop = function() {
			var backdrops = document.querySelectorAll('.modal-backdrop');
			backdrops.forEach(function (backdrop) { backdrop.remove(); });
			document.body.classList.remove('modal-open');
			document.body.style.overflow = '';
			document.body.style.paddingRight = '';
		};

		window.loadItemsByClass = function() {
			if (typeof $ === 'undefined') return;
			var txtClass = $('#txtClass'), txtGender = $('#txtGender'), availableItemsContainer = $('#availableItemsContainer');
			if (txtClass.length === 0 || txtGender.length === 0 || availableItemsContainer.length === 0) return;
			var selectedClass = txtClass.val(), selectedGender = txtGender.val(), schoolCode = $('#hidSchoolId').val(), participantId = $('#hidADNO').val();
			if (!selectedClass) { availableItemsContainer.html('<div class="text-center text-muted"><p>Please select a class to view available items.</p></div>'); return; }
			availableItemsContainer.html('<div class="text-center"><div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div><p>Loading items...</p></div>');
			$.ajax({ url: '<?php echo base_url(); ?>schools/registration/get_items_by_class', type: 'POST', data: { class: selectedClass, gender: selectedGender, school_code: schoolCode, participant_id: participantId }, dataType: 'json',
				success: function (response) {
					if (response.error) { availableItemsContainer.html('<div class="alert alert-danger"><p>' + response.error + '</p></div>'); return; }
					var items = response.items || [], selectedItems = response.selected_items || [], itemsHtml = '';
					if (items.length === 0) { itemsHtml = '<div class="text-center text-muted"><p>No items available.</p></div>'; }
					else {
						var groupedItems = {};
						items.forEach(function (item) { if (!groupedItems[item.fest_name]) groupedItems[item.fest_name] = []; groupedItems[item.fest_name].push(item); });
						itemsHtml = '<div class="items-container">';
						for (var festName in groupedItems) { if (groupedItems.hasOwnProperty(festName)) {
							itemsHtml += '<div class="festival-group mb-3"><h6 class="festival-title text-primary mb-2">' + festName + '</h6><div class="list-group">';
							groupedItems[festName].forEach(function (item) {
								var isSelected = selectedItems.includes(item.item_code), disabledClass = isSelected ? 'disabled-item' : 'available-item', cursor = isSelected ? 'not-allowed' : 'pointer';
								var itemTypeBadge = item.item_type === 'G' ? '<span class="badge bg-primary">Group</span>' : '<span class="badge bg-success">Single</span>';
								var genderBadge = item.gender === 'B' ? '<span class="badge bg-info">Boys</span>' : item.gender === 'G' ? '<span class="badge bg-warning">Girls</span>' : '<span class="badge bg-secondary">Common</span>';
								itemsHtml += '<div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center item-selectable ' + disabledClass + '" data-item-code="' + item.item_code + '" data-item-name="' + item.item_name + '" data-item-type="' + item.item_type + '" style="cursor: ' + cursor + '; margin-bottom: 5px; border-radius: 8px; position: relative;">';
								itemsHtml += '<div style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%);"><span style="color: ' + (isSelected ? '#dc3545' : '#28a745') + '; font-size: 12px;">●</span></div>';
								itemsHtml += '<div style="margin-left: 20px;"><strong>' + item.item_code + '</strong> - ' + item.item_name + '<br><small class="text-muted">Festival: ' + item.fest_name + '</small></div>';
								itemsHtml += '<div>' + itemTypeBadge + ' ' + genderBadge + (isSelected ? ' <span class="badge bg-danger">Selected</span>' : '') + '</div></div>';
							});
							itemsHtml += '</div></div>';
						}}
						itemsHtml += '</div>';
					}
					availableItemsContainer.html(itemsHtml);
					$('.item-selectable.available-item').off('click').on('click', function () {
						var itemCode = $(this).data('item-code'), itemName = $(this).data('item-name'), itemType = $(this).data('item-type');
						if (!itemCode || !itemName) return;
						var emptyInput = $('.item-code-input').filter(function () { return $(this).val() === ''; }).first();
						if (emptyInput.length > 0) {
							emptyInput.val(itemCode);
							var fieldNumber = emptyInput.attr('id').replace('txtItemCode_', ''), detailDiv = $('#itemDetails_' + fieldNumber);
							if (detailDiv.length > 0) { detailDiv.find('#itemName_' + fieldNumber).text(itemName + ' (' + itemType + ')'); detailDiv.show(); }
						} else { alert('All item code fields are filled.'); }
					});
					$('.item-selectable.disabled-item').off('click').on('click', function () { alert('This item is already selected by another participant.'); });
				},
				error: function (xhr, status, error) { availableItemsContainer.html('<div class="alert alert-danger"><p>Error loading items.</p></div>'); }
			});
		};

		// School Details Validation Function
		function fncCheckSchoolDeatils() {


			// Get form elements
			var txtStandardFrom = document.getElementById('txtStandardFrom');
			var txtStandardTo = document.getElementById('txtStandardTo');
			var txtSchoolEmail = document.getElementById('txtSchoolEmail');
			var txtPrincipal = document.getElementById('txtPrincipal');
			var txtPrincipalPhone = document.getElementById('txtPrincipalPhone');
			var txtHeadmaster = document.getElementById('txtHeadmaster');
			var txtHeadmasterPhone = document.getElementById('txtHeadmasterPhone');
			var txtTotalLP = document.getElementById('txtTotalLP');
			var txtTotalUP = document.getElementById('txtTotalUP');
			var txtTotalHS = document.getElementById('txtTotalHS');
			var txtTotalHSS = document.getElementById('txtTotalHSS');
			var txtTotalVHSS = document.getElementById('txtTotalVHSS');
			var txtTotal = document.getElementById('txtTotal');

			// Validate Standard From
			if (!txtStandardFrom || txtStandardFrom.value === '' || txtStandardFrom.value === '0') {
				swal("Oops!", "Please select Standard From", "error");
				if (txtStandardFrom) txtStandardFrom.focus();
				return false;
			}

			// Validate Standard To
			if (!txtStandardTo || txtStandardTo.value === '' || txtStandardTo.value === '0') {
				swal("Oops!", "Please select Standard To", "error");
				if (txtStandardTo) txtStandardTo.focus();
				return false;
			}

			// Validate Standard Range Logic
			var standardFrom = parseInt(txtStandardFrom.value);
			var standardTo = parseInt(txtStandardTo.value);

			if (standardFrom > standardTo) {
				swal("Oops!", "Standard From cannot be greater than Standard To", "error");
				if (txtStandardFrom) txtStandardFrom.focus();
				return false;
			}

			// Validate email format if provided
			if (txtSchoolEmail && txtSchoolEmail.value.trim() !== '') {
				var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
				if (!emailPattern.test(txtSchoolEmail.value.trim())) {
					swal("Oops!", "Please enter a valid email address", "error");
					if (txtSchoolEmail) txtSchoolEmail.focus();
					return false;
				}
			}

			// Validate Principal Phone if Principal name is provided
			if (txtPrincipal && txtPrincipal.value.trim() !== '' && txtPrincipalPhone) {
				var phone = txtPrincipalPhone.value.trim();
				if (phone !== '' && (phone.length < 10 || phone.length > 11 || !/^\d+$/.test(phone))) {
					swal("Oops!", "Please enter a valid Principal phone number (10-11 digits)", "error");
					if (txtPrincipalPhone) txtPrincipalPhone.focus();
					return false;
				}
			}

			// Validate Headmaster Phone if Headmaster name is provided
			if (txtHeadmaster && txtHeadmaster.value.trim() !== '' && txtHeadmasterPhone) {
				var phone = txtHeadmasterPhone.value.trim();
				if (phone !== '' && (phone.length < 10 || phone.length > 11 || !/^\d+$/.test(phone))) {
					swal("Oops!", "Please enter a valid Headmaster phone number (10-11 digits)", "error");
					if (txtHeadmasterPhone) txtHeadmasterPhone.focus();
					return false;
				}
			}

			// Validate strength fields if provided
			var strengthFields = [
				{ element: txtTotalLP, name: 'LP' },
				{ element: txtTotalUP, name: 'UP' },
				{ element: txtTotalHS, name: 'HS' },
				{ element: txtTotalHSS, name: 'HSS' },
				{ element: txtTotalVHSS, name: 'VHSS' }
			];

			for (var i = 0; i < strengthFields.length; i++) {
				var field = strengthFields[i];
				if (field.element && field.element.value.trim() !== '') {
					var value = field.element.value.trim();
					if (!/^\d+$/.test(value)) {
						swal("Oops!", "Please enter a valid number for " + field.name + " strength", "error");
						if (field.element) field.element.focus();
						return false;
					}
					if (parseInt(value) < 0) {
						swal("Oops!", field.name + " strength cannot be negative", "error");
						if (field.element) field.element.focus();
						return false;
					}
				}
			}

			// Validate Total if provided
			if (txtTotal && txtTotal.value.trim() !== '') {
				var totalValue = txtTotal.value.trim();
				if (!/^\d+$/.test(totalValue)) {
					swal("Oops!", "Please enter a valid total strength", "error");
					if (txtTotal) txtTotal.focus();
					return false;
				}
				if (parseInt(totalValue) < 0) {
					swal("Oops!", "Total strength cannot be negative", "error");
					if (txtTotal) txtTotal.focus();
					return false;
				}
			}


			return true;
		}

		// Get school data and confirmation status
		var schoolCode = '<?php echo !empty($school_details) && !empty($school_details[0]['school_code']) ? $school_details[0]['school_code'] : ''; ?>';
		var confirmStatus = '<?php echo !empty($school_details) && !empty($school_details[0]['is_finalize']) ? $school_details[0]['is_finalize'] : 'N'; ?>';
		var schoolShow = '<?php echo isset($school_show) ? $school_show : ''; ?>';

		var schoolCodeInput = document.getElementById('txtSchoolCode');
		var schoolDetailsSection = document.getElementById('schoolDetailsSection');
		var addParticipantBtn = document.getElementById('addParticipantBtn');
		var schoolProfileBtn = document.getElementById('schoolProfileBtn');
		var schoolProfileBtnText = document.getElementById('schoolProfileBtnText');

		// Enhanced visibility management function
		function updateVisibility() {
			// Get fresh references to elements
			var schoolCodeInputElement = document.getElementById('txtSchoolCode');
			var schoolDetailsSection = document.getElementById('schoolDetailsSection');
			var addParticipantBtn = document.getElementById('addParticipantBtn');
			var schoolProfileBtnText = document.getElementById('schoolProfileBtnText');
			var reportBtn = document.getElementById('reportBtn');

			var currentSchoolCode = schoolCodeInputElement ? schoolCodeInputElement.value.trim() : '';
			var sessionSchoolCode = '<?php echo !empty($school_details) && !empty($school_details[0]['school_code']) ? $school_details[0]['school_code'] : ''; ?>';
			var confirmStatus = '<?php echo !empty($school_details) && !empty($school_details[0]['is_finalize']) ? $school_details[0]['is_finalize'] : 'N'; ?>';
			var schoolShow = '<?php echo isset($school_show) ? $school_show : ''; ?>';

			var hasSchoolCode = currentSchoolCode !== '' || sessionSchoolCode !== '';
			var isConfirmed = confirmStatus === 'Y' || confirmStatus === 'Yes' || confirmStatus === '1' || confirmStatus === 'true';
			var isEditing = schoolShow === 'edit';










			// RULE 1: School Code not contain then Add Participant button hide and show School Details
			if (!hasSchoolCode) {
				// No school code - Show School Details, Hide Add Participant button, Hide Report button
				if (schoolDetailsSection) {
					schoolDetailsSection.style.display = 'block';

				}
				if (addParticipantBtn) {
					addParticipantBtn.style.display = 'none';

				}
				if (reportBtn) {
					reportBtn.style.display = 'none';

				}
				if (schoolProfileBtnText) {
					schoolProfileBtnText.textContent = 'Hide School Details';

				}
			}
			// RULE 2: Confirm Status is Yes or True then not show Add Participant Button
			else if (isConfirmed) {
				// Confirmed - Hide Add Participant button and School Details section, Show Report button
				if (schoolDetailsSection) {
					schoolDetailsSection.style.display = 'none';

				}
				if (addParticipantBtn) {
					addParticipantBtn.style.display = 'none';

				}
				if (reportBtn) {
					reportBtn.style.display = 'inline-block';

				}
				if (schoolProfileBtnText) {
					schoolProfileBtnText.textContent = 'School Profile';

				}
			}
			// RULE 3: Edit button show then show Add Participant Button
			else if (isEditing) {
				// Editing - Show School Details, Add Participant button, and Report button
				if (schoolDetailsSection) {
					schoolDetailsSection.style.display = 'block';

				}
				if (addParticipantBtn) {
					addParticipantBtn.style.display = 'inline-block';

				}
				if (reportBtn) {
					reportBtn.style.display = 'inline-block';

				}
				if (schoolProfileBtnText) {
					schoolProfileBtnText.textContent = 'Hide School Details';

				}
			}
			// Default case: Has school code, not confirmed, not editing
			else {
				// Has school code - Hide School Details, Show Add Participant button and Report button
				if (schoolDetailsSection) {
					schoolDetailsSection.style.display = 'none';

				}
				if (addParticipantBtn) {
					addParticipantBtn.style.display = 'inline-block';

				}
				if (reportBtn) {
					reportBtn.style.display = 'inline-block';

				}
				if (schoolProfileBtnText) {
					schoolProfileBtnText.textContent = 'School Profile';

				}
			}
		}

		// Initialize the visibility state
		function initializeVisibility() {

			updateVisibility();
		}

		// Function to handle school code input changes
		function handleSchoolCodeChange() {
			updateVisibility();
		}

		// Function to toggle School Profile section
		function toggleSchoolProfile() {
			var schoolDetailsSection = document.getElementById('schoolDetailsSection');
			var schoolProfileBtnText = document.getElementById('schoolProfileBtnText');

			if (schoolDetailsSection) {
				var isVisible = schoolDetailsSection.style.display === 'block';


				// RULE 4: School Profile Button click then show/hide School Details div
				if (isVisible) {
					// Currently visible - hide it
					schoolDetailsSection.style.display = 'none';

					if (schoolProfileBtnText) {
						schoolProfileBtnText.textContent = 'School Profile';

					}
				} else {
					// Currently hidden - show it
					schoolDetailsSection.style.display = 'block';

					if (schoolProfileBtnText) {
						schoolProfileBtnText.textContent = 'Hide School Details';

					}
				}

				// Update Add Participant button visibility based on current state
				updateVisibility();

			} else {
				console.error('❌ School Details Section NOT FOUND!');
			}
		}

		// Function to handle school code input fetch (when school details are loaded via AJAX)
		function fetch_school_details() {
			// Update visibility after school details are fetched
			setTimeout(function () {
				updateVisibility();
			}, 100);
		}

		// Override the fncEditSchoolDeatils function to ensure proper visibility
		var originalEditFunction = window.fncEditSchoolDeatils;
		window.fncEditSchoolDeatils = function () {


			// Call original function if it exists
			if (originalEditFunction && typeof originalEditFunction === 'function') {
				originalEditFunction();
			} else {
				// Fallback to original behavior
				var formSchool = document.getElementById('formSchool');
				if (formSchool) {
					formSchool.action = (typeof path !== 'undefined' ? path : '') + 'schools/registration/edit_school_details';
					formSchool.submit();
				}
			}

			// Update visibility after edit mode is activated
			setTimeout(function () {
				updateVisibility();
			}, 100);
		};

		// Enhanced initialization with multiple fallback methods
		function initializePage() {

			// Multiple methods to find elements
			var schoolCodeInputElement = document.getElementById('txtSchoolCode') ||
				document.querySelector('input[name="txtSchoolCode"]') ||
				document.querySelector('#divSchoolCode input');

			var schoolProfileBtnElement = document.getElementById('schoolProfileBtn') ||
				document.querySelector('button[id="schoolProfileBtn"]');



			// Enhanced event listener attachment with error handling
			if (schoolCodeInputElement) {
				try {
					schoolCodeInputElement.addEventListener('input', handleSchoolCodeChange);
					schoolCodeInputElement.addEventListener('blur', handleSchoolCodeChange);

				} catch (e) {
					console.error('❌ Error adding listeners to School Code Input:', e);
				}
			} else {
				console.error('❌ School Code Input NOT FOUND - Available inputs:');
				var inputs = document.querySelectorAll('input');
				inputs.forEach(function (input, index) {
				});
			}

			if (schoolProfileBtnElement) {
				try {
					schoolProfileBtnElement.addEventListener('click', toggleSchoolProfile);

				} catch (e) {
					console.error('❌ Error adding click listener to School Profile Button:', e);
				}
			} else {
				console.error('❌ School Profile Button NOT FOUND - Available buttons:');
				var buttons = document.querySelectorAll('button');
				buttons.forEach(function (button, index) {
				});
			}

			// Initialize visibility using the new unified function

			initializeVisibility();

			// Additional check after a short delay
			setTimeout(function () {
				var postCheckAddParticipantBtn = document.getElementById('addParticipantBtn');
				var postCheckSchoolDetailsSection = document.getElementById('schoolDetailsSection');
				if (postCheckAddParticipantBtn) {
				}
				if (postCheckSchoolDetailsSection) {
				}
			}, 500);
		}

		// Multiple initialization triggers
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () {
				setTimeout(initializePage, 100);
			});
		} else {
			setTimeout(initializePage, 100);
		}

		// Additional fallback initialization
		window.addEventListener('load', function () {
			setTimeout(initializePage, 200);
			// Ensure photos load properly after page load
			setTimeout(function () {
				ensurePhotosLoad();
			}, 500);
		});

		// Override the confirm_school_data function to ensure proper Add Participant button handling
		var originalConfirmFunction = window.confirm_school_data;
		window.confirm_school_data = function () {
			// RULE 2 & 5: Confirm Status is Yes then hide Add Participant Button and School Details Edit Button

			// Call original function if it exists
			if (originalConfirmFunction && typeof originalConfirmFunction === 'function') {
				originalConfirmFunction();
			} else {
				// Fallback to default confirmation behavior
				if (confirm('Are you sure you want to confirm this school data? This action cannot be undone.')) {
					// Submit the finalize form
					var formFinalize = document.getElementById('formFinalize');
					if (formFinalize) {
						formFinalize.submit();
					}
				}
			}

			// Update visibility after confirmation
			setTimeout(function () {
				updateVisibility();
			}, 100);
		};

		// Force cache refresh and reinitialize
		function forceReinitialize() {

			// Force immediate initialization
			initializePage();

			// Additional forced visibility check using the unified function
			setTimeout(function () {
				updateVisibility();

				var addParticipantBtn = document.getElementById('addParticipantBtn');
				var schoolDetailsSection = document.getElementById('schoolDetailsSection');


				return 'Force reinitialization completed - check console logs';
			}

			// Add a test function for debugging (can be called from browser console)
			window.testSchoolProfileToggle = function () {
					toggleSchoolProfile();
					return 'Test function executed - check console logs above';
				};

			// Manual initialization function
			window.manualInit = function () {
				initializePage();
				return 'Manual initialization completed';
			};

			// Function to open registration report in new tab
			function openRegistrationReport() {
				var schoolCode = document.getElementById('hidSchoolId');
				if (!schoolCode || !schoolCode.value) {
					alert('Please select a school first');
					return;
				}

				// Create a form to submit school ID to HTML report controller
				var form = document.createElement('form');
				form.method = 'POST';
				form.action = '<?php echo base_url(); ?>schools/registration_report/html_report';
				form.target = '_blank';

				var hiddenField = document.createElement('input');
				hiddenField.type = 'hidden';
				hiddenField.name = 'hidSchoolId';
				hiddenField.value = schoolCode.value;

				form.appendChild(hiddenField);
				document.body.appendChild(form);
				form.submit();
				document.body.removeChild(form);
			}

			// Global function to open registration report (accessible from anywhere)
			window.openRegistrationReport = openRegistrationReport;

			// School Details Save and Hide Function (Global Scope)
			function saveAndHideSchoolDetails() {

				// Get form elements for debugging
				var txtStandardFrom = document.getElementById('txtStandardFrom');
				var txtStandardTo = document.getElementById('txtStandardTo');
				var formSchool = document.getElementById('formSchool');
				var hidSchoolId = document.getElementById('hidSchoolId');

				// RULE 5: Continue then update data and hide School Details and Show Add Participant Button (if not confirm)

				// First validate the data by calling the validation function
				if (typeof fncCheckSchoolDeatils === 'function' && fncCheckSchoolDeatils()) {
					// If validation passes, submit the school form
					if (formSchool) {
						// Ensure all required fields are present
						var requiredFields = [
							'txtStandardFrom', 'txtStandardTo', 'hidSchoolId'
						];

						var missingFields = [];
						for (var i = 0; i < requiredFields.length; i++) {
							var field = document.getElementById(requiredFields[i]);
							if (!field || !field.value || field.value === '0') {
								missingFields.push(requiredFields[i]);
							}
						}

						if (missingFields.length > 0) {
							swal("Error", "Please fill in all required fields: " + missingFields.join(', '), "error");
							return;
						}

						// Log all form data before submission
						var formData = new FormData(formSchool);
						for (var pair of formData.entries()) {
						}

						// Submit the form
						try {
							formSchool.submit();
						} catch (error) {
							swal("Error", "Error submitting form. Please try again.", "error");
						}
					} else {
						swal("Error", "Form not found. Please refresh the page and try again.", "error");
					}
				} else {

				}
			}

			<?php if (isset($is_edit) and $is_edit != 'no') { ?>

				// Modal backdrop fix - ensure proper cleanup
				document.addEventListener('DOMContentLoaded', function () {
					var participantModal = document.getElementById('participantModal');

					if (participantModal) {
						// Handle modal hide event to ensure backdrop is properly removed
						participantModal.addEventListener('hidden.bs.modal', function () {
							setTimeout(function () {
								forceRemoveModalBackdrop();
							}, 50);
						});

						// Handle modal hide event with slight delay to ensure cleanup
						participantModal.addEventListener('hide.bs.modal', function () {
							setTimeout(function () {
								forceRemoveModalBackdrop();
							}, 50);
						});

						// Handle modal show event
						participantModal.addEventListener('show.bs.modal', function () {
						});

						// Handle modal shown event
						participantModal.addEventListener('shown.bs.modal', function () {
						});
					}

					// Start monitoring for backdrop elements
					startBackdropMonitor();

					// Add photo refresh when modal is shown
					participantModal.addEventListener('show.bs.modal', function () {
						setTimeout(function () {
							refreshModalPhoto();
						}, 100);
					});
				});



				// Monitor for backdrop elements and remove them
				function startBackdropMonitor() {
					var monitorInterval;

					// Check every 100ms for backdrops when modal is not visible
					monitorInterval = setInterval(function () {
						var participantModal = document.getElementById('participantModal');
						if (participantModal) {
							var modal = bootstrap.Modal.getInstance(participantModal);
							var isModalVisible = modal && modal._isShown;

							if (!isModalVisible) {
								var backdrops = document.querySelectorAll('.modal-backdrop');
								if (backdrops.length > 0) {
									forceRemoveModalBackdrop();
								}
							}
						}
					}, 100);

					// Stop monitoring after 30 seconds
					setTimeout(function () {
						if (monitorInterval) {
							clearInterval(monitorInterval);
						}
					}, 30000);
				}

				// Function to properly close modal with backdrop cleanup
				function closeParticipantModal() {
					var participantModal = document.getElementById('participantModal');
					if (participantModal) {
						var modal = bootstrap.Modal.getInstance(participantModal);
						if (modal) {
							modal.hide();
							// Force cleanup after modal hide
							setTimeout(function () {
								forceRemoveModalBackdrop();
							}, 150);
						}
					}
				}

				// Global function to force cleanup modal backdrop (can be called from anywhere)
				window.forceCleanupModalBackdrop = forceRemoveModalBackdrop;
			<?php } else { ?>
				function saveAndHideSchoolDetails() {
					alert('Edit functionality is not available for this school.');
				}
			<?php } ?>


			function loadSchoolList() {
				// Fetch list of schools for duplication
				$.ajax({
					url: '<?php echo base_url(); ?>schools/registration/get_school_list',
					type: 'GET',
					dataType: 'json',
					success: function (response) {
						var schoolSelect = $('#duplicateSchoolSelect');
						schoolSelect.empty();
						schoolSelect.append('<option value="">-- Select a School --</option>');

						if (response.schools && response.schools.length > 0) {
							$.each(response.schools, function (index, school) {
								schoolSelect.append('<option value="' + school.school_code + '">' +
									school.school_code + ' - ' + school.school_name + '</option>');
							});
						} else {
							schoolSelect.append('<option value="">No schools available</option>');
						}
					},
					error: function () {
						alert('Error loading school list');
					}
				});
			}

			function loadSchoolData() {
				var schoolCode = $('#duplicateSchoolSelect').val();
				if (!schoolCode) {
					$('#duplicatePreview').hide();
					$('#duplicateBtn').hide();
					return;
				}

				// Fetch school data for preview
				$.ajax({
					url: '<?php echo base_url(); ?>schools/registration/get_school_data',
					type: 'POST',
					data: { school_code: schoolCode },
					dataType: 'json',
					success: function (response) {
						if (response.school) {
							var school = response.school;

							// Populate preview table
							$('#previewSchoolCode').text(school.school_code || '');
							$('#previewSchoolName').text(school.school_name || '');
							$('#previewEmail').text(school.school_email || '');

							var schoolType = '';
							if (school.school_type == 'G') schoolType = 'Government';
							else if (school.school_type == 'A') schoolType = 'Aided';
							else if (school.school_type == 'U') schoolType = 'Unaided';
							$('#previewSchoolType').text(schoolType);

							$('#previewStandardFrom').text(school.class_start || '');
							$('#previewStandardTo').text(school.class_end || '');
							$('#previewPrincipal').text(school.principal_name || '');
							$('#previewHeadmaster').text(school.hm_name || '');

							$('#duplicatePreview').show();
							$('#duplicateBtn').show();
						}
					},
					error: function () {
						alert('Error loading school data');
					}
				});
			}

			// Function to handle item code input changes (used for manual entry validation)
			function handleItemCodeChange(input) {
				var inputElement = $(input);
				if (inputElement.length === 0) {
					console.error('Input element not found');
					return;
				}

				var itemCode = inputElement.val();
				var inputId = inputElement.attr('id');

				if (!inputId) {
					console.error('Input ID not found');
					return;
				}

				var fieldNumber = inputId.replace('txtItemCode_', '');

				if (itemCode.length === 4) {
					// Fetch item details
					$.ajax({
						url: '<?php echo base_url(); ?>schools/registration/get_item_details',
						type: 'POST',
						data: { item_code: itemCode },
						dataType: 'json',
						success: function (response) {
							var detailDiv = $('#itemDetails_' + fieldNumber);
							if (detailDiv.length > 0) {
								if (response.item) {
									detailDiv.find('#itemName_' + fieldNumber).text(response.item.item_name + ' (' + response.item.item_type + ')');
									detailDiv.show();
								} else {
									detailDiv.hide();
									alert('Invalid item code: ' + itemCode);
									inputElement.val('');
								}
							}
						},
						error: function (xhr, status, error) {
							console.error('Error validating item code:', error);
							var detailDiv = $('#itemDetails_' + fieldNumber);
							if (detailDiv.length > 0) {
								detailDiv.hide();
							}
							alert('Error validating item code');
						}
					});
				} else {
					var detailDiv = $('#itemDetails_' + fieldNumber);
					if (detailDiv.length > 0) {
						detailDiv.hide();
					}
				}
			}

			// Function to clear item selection
			function clearItemSelection(inputId) {
				var input = $('#' + inputId);
				if (input.length === 0) {
					console.error('Input element not found:', inputId);
					return;
				}

				input.val('');
				input.prop('disabled', false);
				input.css('background-color', '');

				var fieldNumber = inputId.replace('txtItemCode_', '');
				var detailDiv = $('#itemDetails_' + fieldNumber);
				if (detailDiv.length > 0) {
					detailDiv.hide();
				}
			}

			// Function to clear all item selections
			function clearAllItemSelections() {
				$('.item-code-input').each(function () {
					$(this).val('');
					$(this).prop('disabled', false);
					$(this).css('background-color', '');
					var fieldNumber = $(this).attr('id').replace('txtItemCode_', '');
					$('#itemDetails_' + fieldNumber).hide();
				});
			}

			// Enhanced clearForm function
			function clearForm() {
				$('#txtADNO').val('');
				$('#txtParticipantName').val('');
				$('#txtClass').val('');
				$('#txtGender').val('');
				$('#userfile').val('');

				// Reset photo preview
				removeCurrentPhoto();

				// Clear all item selections
				clearAllItemSelections();

				// Clear available items
				$('#availableItemsContainer').html('<div class="text-center text-muted"><p>Please select a class and gender to view available items.</p></div>');

				// Clear participant ID
				$('#hidADNO').val('');

				// Clear pinnany codes inputs
				$('.pinnany-code-input').val('');
			}

			// Initialize item code input handlers when document is ready
			$(document).ready(function () {
				// Add change handlers to item code inputs
				$('.item-code-input').each(function () {
					if ($(this).length > 0) {
						$(this).on('input', function () {
							handleItemCodeChange(this);
						});
					}
				});

				// Add clear buttons to item inputs
				$('.item-code-input').each(function () {
					if ($(this).length > 0) {
						var inputId = $(this).attr('id');
						if (inputId) {
							$(this).after('<button type="button" class="btn btn-outline-danger btn-sm ms-1" onclick="clearItemSelection(\'' + inputId + '\')" title="Clear selection"><i class="fas fa-times"></i></button>');
						}
					}
				});
			});

			// Photo preview functions
			function previewPhoto(input) {
				var currentPhoto = document.getElementById('currentPhoto');
				var noPhotoMessage = document.getElementById('noPhotoMessage');
				var removePhotoBtn = document.getElementById('removePhotoBtn');

				if (input.files && input.files[0]) {
					var reader = new FileReader();
					reader.onload = function (e) {
						currentPhoto.src = e.target.result;
						currentPhoto.style.display = 'block';
						noPhotoMessage.style.display = 'none';
						removePhotoBtn.style.display = 'inline-block';
					};
					reader.readAsDataURL(input.files[0]);
				}
			}

			function removeCurrentPhoto() {
				var currentPhoto = document.getElementById('currentPhoto');
				var noPhotoMessage = document.getElementById('noPhotoMessage');
				var removePhotoBtn = document.getElementById('removePhotoBtn');
				var userfile = document.getElementById('userfile');

				if (currentPhoto) {
					currentPhoto.src = '';
					currentPhoto.style.display = 'none';
					currentPhoto.onerror = null;
				}
				if (noPhotoMessage) noPhotoMessage.style.display = 'block';
				if (removePhotoBtn) removePhotoBtn.style.display = 'none';
				if (userfile) userfile.value = '';
			}

			function loadModalPhoto(photoUrl) {
				var currentPhoto = document.getElementById('currentPhoto');
				var noPhotoMessage = document.getElementById('noPhotoMessage');
				var removePhotoBtn = document.getElementById('removePhotoBtn');

				if (!currentPhoto || !noPhotoMessage || !removePhotoBtn) {
					console.error('Photo preview elements not found');
					return;
				}

				if (photoUrl && photoUrl !== 'false' && photoUrl !== 'null' && photoUrl !== null && photoUrl !== '') {
					// Remove any existing cache buster params
					var baseUrl = photoUrl.split('?')[0];
					var cacheBuster = Date.now() + '_' + Math.random();
					currentPhoto.src = baseUrl + '?v=' + cacheBuster;
					currentPhoto.style.display = 'block';
					noPhotoMessage.style.display = 'none';
					removePhotoBtn.style.display = 'inline-block';

					// Handle image load error
					currentPhoto.onerror = function () {
						console.error('Failed to load photo:', baseUrl);
						removeCurrentPhoto();
					};
				} else {
					removeCurrentPhoto();
				}
			}

			// Function to refresh modal photo when modal is shown
			function refreshModalPhoto() {
				var currentPhoto = document.getElementById('currentPhoto');
				if (currentPhoto && currentPhoto.src && currentPhoto.src !== '') {
					var baseUrl = currentPhoto.src.split('?')[0];
					var cacheBuster = Date.now() + '_' + Math.random();
					currentPhoto.src = baseUrl + '?v=' + cacheBuster;
				}
			}

			// Function to ensure photos load properly in participant list
			function ensurePhotosLoad() {
				var photos = document.querySelectorAll('.participant-photo');
				photos.forEach(function (photo) {
					if (photo.src && photo.src !== '') {
						var baseUrl = photo.src.split('?')[0];
						var cacheBuster = Date.now() + '_' + Math.random();
						photo.src = baseUrl + '?v=' + cacheBuster;
					}
				});
			}

			// Override the editParticipant function to set participant ID
			var originalEditParticipant = window.editParticipant;
			window.editParticipant = function (admnNo) {

				// Set the participant ID for item selection
				var hidADNO = $('#hidADNO');
				if (hidADNO.length > 0) {
					hidADNO.val(admnNo);
				}

				// Call original function if it exists
				if (originalEditParticipant && typeof originalEditParticipant === 'function') {
					originalEditParticipant(admnNo);
				} else {
					// Fallback to default behavior
					var hidSchoolId = $('#hidSchoolId');
					if (hidSchoolId.length === 0) {

						return;
					}

					$.ajax({
						url: '<?php echo base_url(); ?>schools/registration/get_participant_data',
						type: 'POST',
						data: {
							hidSchoolId: hidSchoolId.val(),
							hidADNO: admnNo
						},
						dataType: 'json',
						success: function (response) {
							if (response.error) {
								alert(response.error);
							} else {
								// Populate form fields safely
								var txtADNO = $('#txtADNO');
								var txtParticipantName = $('#txtParticipantName');
								var txtClass = $('#txtClass');
								var txtGender = $('#txtGender');

								if (txtADNO.length > 0) txtADNO.val(response.admn_no);
								if (txtParticipantName.length > 0) txtParticipantName.val(response.participant_name);
								if (txtClass.length > 0) txtClass.val(response.class);
								if (txtGender.length > 0) txtGender.val(response.gender);

								// Load photo immediately
								if (response.photo_url && response.photo_url !== 'false' && response.photo_url !== 'null') {
									loadModalPhoto(response.photo_url);
								} else {
									removeCurrentPhoto();
								}

								// Load items for the selected class
								loadItemsByClass();

								// Populate item codes if any
								if (response.items && Array.isArray(response.items)) {
									for (var i = 0; i < response.items.length; i++) {
										var item = response.items[i];
										var inputId = 'txtItemCode_' + (i + 1);
										var inputElement = $('#' + inputId);

										if (inputElement.length > 0) {
											inputElement.val(item.item_code);

											// Show item details
											var detailDiv = $('#itemDetails_' + (i + 1));
											if (detailDiv.length > 0) {
												detailDiv.find('#itemName_' + (i + 1)).text(item.item_name + ' (' + item.item_type + ')');
												detailDiv.show();
											}

											// Disable the input
											//inputElement.prop('disabled', true);
											//inputElement.css('background-color', '#e9ecef');
										}
									}
								}

								// Open the modal and refresh photo after shown
								var modalEl = document.getElementById('participantModal');
								var participantModal = new bootstrap.Modal(modalEl);
								modalEl.addEventListener('shown.bs.modal', function () {
									if (response.photo_url && response.photo_url !== 'false' && response.photo_url !== 'null') {
										loadModalPhoto(response.photo_url);
									}
								}, { once: true });
								participantModal.show();
							}
						},
						error: function (xhr, status, error) {

							alert('Error loading participant data');
						}
					});
				}
			};


	</script>
<?php } ?>


<?php
$show_participants = (@$school_show == 'show');
?>

<div style="display:<?php echo $show_participants ? 'block' : 'none'; ?>">
	<div class="row justify-content-center">
		<div class="col-md-12 col-sm-12 col-xl-12">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title">
						Participants
					</h5>

					<?php if (!empty($participant_details) && count($participant_details) > 0): ?>
						<div class="table-responsive">
							<table class="table table-striped table-hover table-bordered">
								<thead class="table-dark">
									<tr>
										<th width="5%">#</th>
										<th width="6%">AD No.</th>
										<th width="8%">Photo</th>
										<th width="6%">Reg No.</th>
										<th width="18%">Name</th>
										<th width="5%">Class</th>
										<th width="7%">Gender</th>
										<th width="25%">Items</th>
										<?php if (isset($is_edit) and $is_edit != 'no') { ?>
											<th width="10%">Actions</th>
										<?php } ?>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = 1;
									for ($j = 0; $j < count($participant_details); $j++) {
										$items = $this->Registration_Model->get_participant_item_details($participant_details[$j]['participant_id'], $participant_details[$j]['admn_no']);
										$photo_url = $participant_photos[$participant_details[$j]['admn_no']];
										$classname = ($j % 2 == 0) ? 'table_row_second' : 'table_row_first'
											?>
										<tr>
											<td class="text-center"><?php echo $count; ?></td>
											<td class="text-center"><?php echo $participant_details[$j]['admn_no'] ?></td>
											<td class="text-center">
												<?php
												$photo_display_url = $photo_url ? $photo_url : base_url('photos/img_user_nophoto.png');
												?>
												<img src="<?php echo $photo_display_url; ?>" width="45" height="45"
													class="img-thumbnail participant-photo"
													alt="<?php echo $photo_url ? 'Photo' : 'No photo'; ?>"
													onerror="this.src='<?php echo base_url('photos/img_user_nophoto.png'); ?>'"
													data-admn-no="<?php echo $participant_details[$j]['admn_no']; ?>"
													data-school-code="<?php echo $school_details[0]['school_code']; ?>"
													loading="lazy" />
											</td>
											<td class="text-center"><?php echo $participant_details[$j]['participant_id'] ?>
											</td>
											<td><?php echo $participant_details[$j]['participant_name'] ?></td>
											<td class="text-center"><?php echo $participant_details[$j]['class'] ?></td>
											<td class="text-center">
												<?php echo ($participant_details[$j]['gender'] == 'B') ? 'Boy' : 'Girl'; ?>
											</td>
											<td>
												<?php
												$item_codes = [];
												for ($k = 0; $k < 5; $k++) {
													if (@$items[$k]['item_code']) {
														$badge_class = (@$items[$k]['spo_id'] != 0) ? 'bg-danger' : 'bg-secondary';
														$item_codes[] = '<span class="badge ' . $badge_class . '" title="' . (@$items[$k]['item_name']) . '">' . @$items[$k]['item_code'] . '(' . @$items[$k]['item_type'] . ')</span>';
													}
												}
												echo implode(' ', $item_codes);
												?>
											</td>
											<?php if (isset($is_edit) and $is_edit != 'no') { ?>
												<td class="text-center">
													<a href="javascript:void(0)"
														onClick="javascript:editParticipant('<?php echo $participant_details[$j]['admn_no'] ?>')"
														class="btn btn-sm me-1" title="Edit">
														<img src="<?php echo base_url(); ?>images/edit.png" alt="Edit" width="16"
															height="16">
													</a>
													<a href="javascript:void(0)"
														onClick="javascript:deleteParticipant('<?php echo $participant_details[$j]['admn_no'] ?>')"
														class="btn btn-sm" title="Delete">
														<img src="<?php echo base_url(); ?>images/delete.png" alt="Delete"
															width="16" height="16">
													</a>
												</td>
											<?php } ?>
										</tr>
										<?php
										$count++;
									}
									?>
								</tbody>
							</table>
						</div>
					<?php else: ?>
						<div class="text-center py-5">
							<div class="empty-state-container">
								<div class="empty-state-icon mb-3" style="font-size: 4rem; opacity: 0.3;">
									<i class="fas fa-users"></i>
								</div>
								<h5 class="text-muted">No participants found</h5>
								<p class="text-muted mb-4">Start by adding your first participant to the system.</p>
								<?php if (!empty($school_details) && !empty($school_details[0]['school_code'])): ?>
									<button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
										data-bs-target="#participantModal" onclick="clearForm()">
										<i class="fas fa-plus"></i> Add Participant
									</button>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
echo form_close();
?>



<?php
if (!empty(@$group_details['group_array'])) {
	if (count(@$group_details['group_array']) > 0) {

		echo form_open('schools/registration/update_group_captain', array('id' => 'formGroupLeader'));
		?>
		<div
			style="display:<?php echo (count(@$group_details['group_array']) > 0 && @$school_show == 'show') ? 'block' : 'none'; ?>; padding:10px 0 10px; 0">
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
												<input type="hidden" name="hidGrpItemCode_<?php echo $count ?>"
													id="hidGrpItemCode_<?php echo $count ?>"
													value="<?php echo $groups['item_code'] ?>">
											</td>
											<td width="70%" class="<?php echo $classname ?>">
												<?php
												if (isset($is_edit) and $is_edit != 'no') {
													print (form_dropdown(
														'cmbGrpParticipant_' . $count,
														$group_participant_array[$groups['item_code']],
														@$group_leader[$groups['item_code']],
														'id="cmbGrpParticipant_"' . $count . '
                                class="form-control" onChange="javascript:$(\'divCaptainSubmit\').style.display = \'block\'"'
													));
												} else {
													print (@$group_participant_array[$groups['item_code']][@$group_leader[$groups['item_code']]]);
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
										<td align="center" colspan="4">
											<div id="divCaptainSubmit" style="display:none">
												<?php
												if (isset($is_edit) and $is_edit != 'no') {
													echo form_submit('group_submit', 'Submit', 'class="btn btn-primary"');
												}
												?>
											</div>
										</td>
									</tr>

								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php

		?>
		</div>
		<input type="hidden" name="hidSchoolId" id="hidSchoolId" value="<?php echo (@$school_details[0]['school_code']) ?
			@$school_details[0]['school_code'] : @$this->input->post('hidSchoolId'); ?>">
		<?php
		echo form_close();
	}
}
?>

<?php

if (!empty(@$participant_item_list)) {

	if (count(@$participant_item_list) > 0) { ?>
		<div
			style="display:<?php echo (@$school_show == 'show' && !empty($participant_details) && count($participant_details) > 0) ? 'block' : 'none'; ?>; padding:10px 0 10px; 0">
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
											<td align="center" class="<?php echo $class_name; ?>"><?php echo $count; ?></td>
											<td class="<?php echo $class_name; ?>"><?php echo $item_list['item_code'] ?></td>
											<td class="<?php echo $class_name; ?>"><?php echo $item_list['item_name'] ?></td>
											<td align="center" class="<?php echo $class_name; ?>">
												<?php echo $item_list['cnt_participant'] ?>
											</td>
											<td align="center" class="<?php echo $class_name; ?>">
												<?php echo $item_list['cnt_pinnany'] ?>
											</td>
											<td class="<?php echo $class_name; ?>"><?php echo @$item_list['captian'] ?></td>
										</tr>
										<?php
									}
									?>

								</table>
								<!-- </div> -->
								<?php

								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php }
}
?>




	<?php
	echo form_open('schools/registration/finalize_data', array('id' => 'formFinalize'));
	?>
	<div
		style="display:<?php echo (@$school_show == 'show' && !empty($participant_details)) ? 'block' : 'none'; ?>; padding:10px 0 10px; 0">
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
										if (isset($is_edit) and $is_edit != 'no' and @$school_details[0]['is_create_report'] == 'Y') {
											?>

											<label style="color:#990000"><strong>Warning: Once confirmed the entry details
													cannot be modified. Before confirming ensure the report has been
													certified by the Headmaster/Principal.</strong></label><br />

											<?php
											echo form_button('finalize_submit', 'Confirm', 'onClick="javascript:confirm_school_data();" class="btn btn-success mt-3"');
										}
										?>
										<!--<button type="button" class="btn btn-primary mt-3 me-2" onclick="openPdfReport()" id="pdfReportBtn">-->
										<!--	<i class="fas fa-file-pdf"></i> PDF Report-->
										<!--</button>-->
										<button type="button" class="btn btn-info mt-3" onclick="openHtmlReport()"
											id="htmlReportBtn">
											<i class="fas fa-file-alt"></i> HTML Report
										</button>
									</td>
								</tr>

							</table>
							<?php

							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="hidSchoolId" id="hidSchoolId"
			value="<?php echo (@$school_details[0]['school_code']) ? @$school_details[0]['school_code'] : @$this->input->post('hidSchoolId'); ?>">
		<?php
		echo form_close();
		?>
	</div>
	<?php if (@$error_msg) { ?>
		<script language="JavaScript">
				window.onload = alert('<?php echo @$error_msg; ?>');
		</script>
		<?php
	} ?>

	<script>
			window.openPdfReport = function () {
				var schoolCode = document.getElementById('hidSchoolId');
				if (!schoolCode || !schoolCode.value) {
					alert('Please select a school first');
					return;
				}
				window.open('<?php echo base_url(); ?>schools/registration_report/pdf_report?school_id=' + schoolCode.value, '_blank');
			};

			window.openHtmlReport = function () {
				var schoolCode = document.getElementById('hidSchoolId');
				if (!schoolCode || !schoolCode.value) {
					alert('Please select a school first');
					return;
				}
				window.open('<?php echo base_url(); ?>schools/registration_report/html_report?school_id=' + schoolCode.value, '_blank');
			};

			

		
	</script>
