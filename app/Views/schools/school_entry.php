<div id="divEntryForm">
	<?php
	$school_details = $school_details ?? [];
	$participant_details = $participant_details ?? [];
	$participant_photos = $participant_photos ?? [];
	$group_details = $group_details ?? [];
	$participant_item_list = $participant_item_list ?? [];
	$is_edit = $is_edit ?? 'yes';
	$all_festival_items = $all_festival_items ?? [];
	$bank_details = $bank_details ?? [];
	$subdistrict_schools = $subdistrict_schools ?? [];
	$initial_show_school_details = $initial_show_school_details ?? false;

	$session_code = trim((string)(session()->get('SCHOOL_CODE') ?? ''));
	$active_code = (!empty($school_details[0]['school_code'])) ? $school_details[0]['school_code'] : ($session_code !== '0' ? $session_code : (service('request')->getPost('hidSchoolId') ?: ''));
	$total_participants_count = !empty($participant_details) ? count($participant_details) : 0;
	?>

	<!-- Modern Top Bar & Right-aligned Action Buttons -->
	<div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #ffffff, #f8fafc); border: 1px solid #e2e8f0 !important;">
		<div class="card-body p-3 p-md-4">
			<div class="row align-items-center g-3">
				<div class="col-md-6 col-12">
					<div class="d-flex align-items-center gap-3">
						<div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-3 shadow-sm" style="width: 48px; height: 48px; min-width: 48px; background: linear-gradient(135deg, #0d6efd, #0b5ed7) !important;">
							<i class="bi bi-people-fill fs-4"></i>
						</div>
						<div>
							<h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
								School Entry & Registrations
								<span class="badge bg-primary rounded-pill fs-7 px-2 py-1"><?php echo $total_participants_count; ?> Students</span>
							</h4>
							<div class="d-flex align-items-center gap-2 text-muted small">
								<a href="<?php echo base_url(); ?>welcome" class="text-decoration-none text-muted"><i class="bi bi-house-door me-1"></i>Home</a>
								<span>/</span>
								<span class="text-primary fw-medium">School Entry</span>
								<?php if (!empty($active_code)): ?>
									<span class="ms-2 badge bg-light text-secondary border">School: <?php echo esc($active_code); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6 col-12 text-md-end">
					<div class="d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
						<?php if (!empty($is_admin)): ?>
							<button type="button" class="btn btn-outline-primary shadow-sm rounded-3 px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2" id="schoolDetailsToggleBtn" onclick="toggleSchoolDetailsSection()">
								<i class="bi bi-building"></i> <span id="schoolDetailsToggleBtnText"><?php echo (empty($active_code) && !empty($initial_show_school_details)) ? 'Hide School Details' : 'School Details'; ?></span>
							</button>
						<?php endif; ?>

						<?php 
						$can_show_add_participant = false;
						if (!empty($is_school_user)) {
							$can_show_add_participant = true;
						} elseif (!empty($is_admin)) {
							if (!empty($active_code) && $active_code !== '0') {
								$can_show_add_participant = true;
							}
						} else {
							$can_show_add_participant = (!empty($active_code) && $active_code !== '0');
						}
						?>

						<?php if ($can_show_add_participant): ?>
							<?php if ((!isset($is_edit) || $is_edit != 'no') && (!isset($school_details[0]['is_finalize']) || $school_details[0]['is_finalize'] != 'Y')): ?>
								<button type="button" class="btn btn-success shadow-sm rounded-3 px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2" data-bs-toggle="modal"
									data-bs-target="#participantModal" onclick="clearForm()" id="addParticipantBtn">
									<i class="bi bi-person-plus-fill fs-6"></i> Add Participant
								</button>
							<?php else: ?>
								<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-7 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs">
									<i class="bi bi-lock-fill"></i> Registration Finalized & Locked
								</span>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- School Details Section (Only for Admins: IT Master, State Admin, District Admin, Sub District Admin) -->
	<?php if (!empty($is_admin)): ?>
	<?php
	$show_school_details_initial = empty($active_code) && !empty($initial_show_school_details);
	?>
	<div class="row justify-content-center mb-4" id="schoolDetailsSection" style="display: <?php echo $show_school_details_initial ? 'block' : 'none'; ?>;">
		<div class="col-12">
			<div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="border: 1px solid #e2e8f0 !important;">
				
				<div class="card-header bg-white py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2" style="background: linear-gradient(135deg, #f8fafc, #ffffff) !important; border-bottom: 1px solid #edf2f7 !important;">
					<div class="d-flex align-items-center gap-2">
						<i class="bi bi-mortarboard-fill text-primary fs-5"></i>
						<h5 class="fw-bold mb-0 text-dark">School Details & Institutional Profile</h5>
					</div>
					<div class="d-flex align-items-center gap-2">
						<?php if (!empty($active_code)): ?>
							<span class="badge bg-primary px-3 py-2 fs-7 rounded-pill">
								<i class="bi bi-hash me-1"></i>School Code: <?php echo esc($active_code); ?>
							</span>
						<?php endif; ?>
						<button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="hideSchoolDetailsSection()">
							<i class="bi bi-chevron-up me-1"></i> Collapse
						</button>
					</div>
				</div>

				<div class="card-body p-4">
					<?php echo form_open('schools/registration/update', array('id' => 'formSchool')); ?>
					<input type="hidden" name="hidTeachers" id="hidTeachers" value="<?php echo (!empty($school_details[0]['teachers'])) ? count(array_filter(explode('#@#', $school_details[0]['teachers']))) : 1; ?>">
					<input type="hidden" name="hidSchoolId" id="formSchoolHidSchoolId" value="<?php echo esc($active_code); ?>">

					<!-- School Search & Code Input Bar -->
					<?php
					$schools_json = [];
					if (!empty($subdistrict_schools)) {
						foreach ($subdistrict_schools as $sc_code => $sc_name) {
							if (!empty($sc_code) && $sc_code !== '0' && $sc_code !== 0) {
								$schools_json[] = [
									'code' => (string)$sc_code,
									'name' => (string)$sc_name
								];
							}
						}
					}
					?>
					<script>
						var SUBDISTRICT_SCHOOLS_LIST = <?php echo json_encode($schools_json); ?>;
					</script>
					<div class="p-3 mb-4 rounded-4 shadow-2xs" style="background: linear-gradient(135deg, #f8fafc, #edf2f7); border: 1px solid #e2e8f0;">
						<div class="row align-items-center g-3">
							<div class="col-lg-3 col-md-4 col-12">
								<label for="txtSearchSchool" class="form-label fw-bold text-dark small text-uppercase mb-0 d-flex align-items-center gap-2">
									<i class="bi bi-search text-primary fs-6"></i> Search School / Code:
								</label>
							</div>
							<div class="col-lg-7 col-md-8 col-12">
								<div class="input-group">
									<span class="input-group-text bg-white border-end-0 text-muted">
										<i class="bi bi-buildings"></i>
									</span>
									<input type="text" list="schoolOptionsList" id="txtSearchSchool" class="form-control border-start-0 shadow-none fw-semibold" placeholder="Enter School Code or Type School Name to search..." value="<?php echo (!empty($active_code)) ? esc($active_code . (!empty($school_details[0]['school_name']) ? ' - ' . $school_details[0]['school_name'] : '')) : ''; ?>" oninput="handleSchoolSearchInput(this.value)" onchange="handleSchoolSearchInput(this.value)" onkeypress="if(event.key === 'Enter'){ event.preventDefault(); loadSchoolFromSearch(); }">
									<datalist id="schoolOptionsList">
										<?php if (!empty($subdistrict_schools)): ?>
											<?php foreach ($subdistrict_schools as $sc_code => $sc_name): ?>
												<?php if (!empty($sc_code) && $sc_code !== '0' && $sc_code !== 0): ?>
													<option value="<?php echo esc($sc_name); ?>"><?php echo esc($sc_code); ?></option>
												<?php endif; ?>
											<?php endforeach; ?>
										<?php endif; ?>
									</datalist>
									<button type="button" id="btnLoadSchool" class="btn btn-primary px-3 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm" onclick="loadSchoolFromSearch()">
										<i class="bi bi-arrow-repeat"></i> Load School
									</button>
								</div>
							</div>
							<div class="col-lg-2 col-md-12 col-12 text-lg-end">
								<?php if (!empty($active_code)): ?>
									<span id="activeSchoolBadge" class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-7 fw-semibold">
										<i class="bi bi-check-circle-fill me-1"></i>Active: <?php echo esc($active_code); ?>
									</span>
								<?php else: ?>
									<span id="activeSchoolBadge" class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill fs-7">
										No School Loaded
									</span>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<!-- Basic School Information -->
					<div class="row mb-3 g-3">
						<div class="col-md-6">
							<label for="txtSchoolName" class="form-label fw-bold text-muted small text-uppercase">School Name</label>
							<input type="text" id="txtSchoolName" name="txtSchoolName" class="form-control bg-light fw-semibold" value="<?php echo esc(@$school_details[0]['school_name'] ?? 'Not Selected'); ?>" readonly>
						</div>
						<div class="col-md-6">
							<label for="txtSchoolType" class="form-label fw-bold text-muted small text-uppercase">School Type / Management</label>
							<input type="text" id="txtSchoolType" name="txtSchoolType" class="form-control bg-light" value="<?php
								$st = @$school_details[0]['school_type'] ?? '';
								if ($st === 'G') echo 'Government (Govt)';
								else if ($st === 'A') echo 'Aided';
								else if ($st === 'U') echo 'Unaided / Private';
								else echo esc($st ?: '-');
							?>" readonly>
						</div>
						<div class="col-md-6">
							<label for="txtSchoolEmail" class="form-label fw-bold text-muted small text-uppercase">Official E-mail</label>
							<input type="email" name="txtSchoolEmail" id="txtSchoolEmail" class="form-control" placeholder="Enter school email" value="<?php echo esc(@$school_details[0]['school_email'] ?? ''); ?>">
						</div>
						<div class="col-md-6">
							<label for="txtSchoolPhone" class="form-label fw-bold text-muted small text-uppercase">School Phone Number</label>
							<input type="text" name="txtSchoolPhone" id="txtSchoolPhone" class="form-control" placeholder="Enter school phone" maxlength="11" value="<?php echo esc(@$school_details[0]['school_phone'] ?? ''); ?>" onkeypress="return numbersonly(this, event, false);">
						</div>
					</div>

					<!-- Classes / Standards Range -->
					<div class="border-top pt-3 mb-3">
						<h6 class="fw-bold text-dark mb-3"><i class="bi bi-diagram-3-fill text-primary me-2"></i>Classes Offered (Standards Range)</h6>
						<div class="row g-3">
							<div class="col-md-6">
								<label for="txtStandardFrom" class="form-label fw-bold text-muted small text-uppercase">Standard From <span class="text-danger">*</span></label>
								<?php
								$std_from_opts = array(0 => '-- Select Standard From --', 1 => '1st Standard', 5 => '5th Standard', 8 => '8th Standard', 11 => '11th Standard');
								echo form_dropdown("txtStandardFrom", $std_from_opts, @$school_details[0]['class_start'], 'id="txtStandardFrom" class="form-select" onChange="javascript:fncShowHideStd()"');
								?>
							</div>
							<div class="col-md-6">
								<label for="txtStandardTo" class="form-label fw-bold text-muted small text-uppercase">Standard To <span class="text-danger">*</span></label>
								<?php
								$std_to_opts = array(0 => '-- Select Standard To --', 4 => '4th Standard', 5 => '5th Standard', 7 => '7th Standard', 10 => '10th Standard', 12 => '12th Standard');
								echo form_dropdown("txtStandardTo", $std_to_opts, @$school_details[0]['class_end'], 'id="txtStandardTo" class="form-select" onChange="javascript:fncShowHideStd()"');
								?>
							</div>
						</div>
					</div>

					<!-- Administration Details -->
					<div class="border-top pt-3 mb-3">
						<h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-badge-fill text-primary me-2"></i>School Administration</h6>
						<div class="row g-3">
							<div class="col-md-6">
								<label for="txtPrincipal" class="form-label fw-bold text-muted small text-uppercase">Principal Name</label>
								<input type="text" name="txtPrincipal" id="txtPrincipal" class="form-control" placeholder="Enter principal name" value="<?php echo esc(@$school_details[0]['principal_name'] ?? ''); ?>" onkeyup="this.value=this.value.toUpperCase();">
							</div>
							<div class="col-md-6">
								<label for="txtPrincipalPhone" class="form-label fw-bold text-muted small text-uppercase">Principal Phone</label>
								<input type="text" name="txtPrincipalPhone" id="txtPrincipalPhone" class="form-control" placeholder="10-digit mobile number" maxlength="11" value="<?php echo esc(@$school_details[0]['principal_phone'] ?? ''); ?>" onkeypress="return numbersonly(this, event, false);">
							</div>
							<div class="col-md-6">
								<label for="txtHeadmaster" class="form-label fw-bold text-muted small text-uppercase">Headmaster (HM) Name</label>
								<input type="text" name="txtHeadmaster" id="txtHeadmaster" class="form-control" placeholder="Enter headmaster name" value="<?php echo esc(@$school_details[0]['hm_name'] ?? ''); ?>" onkeyup="this.value=this.value.toUpperCase();">
							</div>
							<div class="col-md-6">
								<label for="txtHeadmasterPhone" class="form-label fw-bold text-muted small text-uppercase">Headmaster (HM) Phone</label>
								<input type="text" name="txtHeadmasterPhone" id="txtHeadmasterPhone" class="form-control" placeholder="10-digit mobile number" maxlength="11" value="<?php echo esc(@$school_details[0]['hm_phone'] ?? ''); ?>" onkeypress="return numbersonly(this, event, false);">
							</div>
						</div>
					</div>

					<!-- Team Managers / Escorting Teachers -->
					<div class="border-top pt-3 mb-3">
						<h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-lines-fill text-primary me-2"></i>Team Managers (Escorting Teachers)</h6>
						<div id="teachersRow">
							<?php
							$teachers_str = @$school_details[0]['teachers'] ?? '';
							$teacher_pairs = !empty($teachers_str) ? array_filter(explode('#@#', $teachers_str)) : [];
							$t_idx = 1;
							if (!empty($teacher_pairs)) {
								foreach ($teacher_pairs as $t_pair) {
									$t_info = explode('#$#', $t_pair);
									$t_name = $t_info[0] ?? '';
									$t_phone = $t_info[1] ?? '';
							?>
									<div class="row mb-2 teachersTextBox" id="teacherRow_<?php echo $t_idx; ?>">
										<div class="col-md-6 mb-2">
											<label class="form-label fw-bold text-muted small text-uppercase">Team Manager <?php echo $t_idx; ?></label>
											<input type="text" name="txtTeacher_<?php echo $t_idx; ?>" id="txtTeacher_<?php echo $t_idx; ?>" class="form-control" placeholder="Enter teacher name" value="<?php echo esc($t_name); ?>" onkeyup="this.value=this.value.toUpperCase();">
										</div>
										<div class="col-md-6 mb-2">
											<label class="form-label fw-bold text-muted small text-uppercase">Phone</label>
											<div class="input-group">
												<input type="text" name="txtPhone_<?php echo $t_idx; ?>" id="txtPhone_<?php echo $t_idx; ?>" class="form-control" placeholder="Phone number" maxlength="11" value="<?php echo esc($t_phone); ?>" onkeypress="return numbersonly(this, event, false);">
												<?php if ($t_idx > 1): ?>
													<button type="button" class="btn btn-outline-danger" onclick="this.closest('.teachersTextBox').remove();" title="Remove">✕</button>
												<?php endif; ?>
											</div>
										</div>
									</div>
							<?php
									$t_idx++;
								}
							} else {
							?>
								<div class="row mb-2 teachersTextBox" id="teacherRow_1">
									<div class="col-md-6 mb-2">
										<label class="form-label fw-bold text-muted small text-uppercase">Team Manager 1</label>
										<input type="text" name="txtTeacher_1" id="txtTeacher_1" class="form-control" placeholder="Enter teacher name" value="" onkeyup="this.value=this.value.toUpperCase();">
									</div>
									<div class="col-md-6 mb-2">
										<label class="form-label fw-bold text-muted small text-uppercase">Phone</label>
										<input type="text" name="txtPhone_1" id="txtPhone_1" class="form-control" placeholder="Phone number" maxlength="11" value="" onkeypress="return numbersonly(this, event, false);">
									</div>
								</div>
							<?php } ?>
							<div id="teachersAddBtnDiv" class="mt-2 mb-3">
								<button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addteacher();">
									<i class="bi bi-plus-circle me-1"></i> Add Another Team Manager
								</button>
							</div>
						</div>
					</div>

					<!-- Total Number of Students -->
					<div class="border-top pt-3 mb-4">
						<h6 class="fw-bold text-dark mb-3"><i class="bi bi-people-fill text-primary me-2"></i>Student Strength Statistics</h6>
						<div class="row g-2 align-items-center">
							<div class="col-6 col-md-2" id="divLP" style="display: <?php echo (!empty($school_details[0]['strength_lp']) || (isset($school_details[0]['class_start']) && @$school_details[0]['class_start'] <= 4)) ? 'block' : 'none'; ?>;">
								<label class="form-label small fw-bold text-muted mb-1">LP</label>
								<input type="text" name="txtTotalLP" id="txtTotalLP" class="form-control form-control-sm" maxlength="5" value="<?php echo esc(@$school_details[0]['strength_lp'] ?? ''); ?>" onkeypress="return numbersonly(this, event, false);" onkeyup="addTotal();">
							</div>
							<div class="col-6 col-md-2" id="divUP" style="display: <?php echo (!empty($school_details[0]['strength_up']) || (isset($school_details[0]['class_start']) && @$school_details[0]['class_start'] <= 7 && @$school_details[0]['class_end'] >= 5)) ? 'block' : 'none'; ?>;">
								<label class="form-label small fw-bold text-muted mb-1">UP</label>
								<input type="text" name="txtTotalUP" id="txtTotalUP" class="form-control form-control-sm" maxlength="5" value="<?php echo esc(@$school_details[0]['strength_up'] ?? ''); ?>" onkeypress="return numbersonly(this, event, false);" onkeyup="addTotal();">
							</div>
							<div class="col-6 col-md-2" id="divHS" style="display: <?php echo (!empty($school_details[0]['strength_hs']) || (isset($school_details[0]['class_start']) && @$school_details[0]['class_start'] <= 10 && @$school_details[0]['class_end'] >= 8)) ? 'block' : 'none'; ?>;">
								<label class="form-label small fw-bold text-muted mb-1">HS</label>
								<input type="text" name="txtTotalHS" id="txtTotalHS" class="form-control form-control-sm" maxlength="5" value="<?php echo esc(@$school_details[0]['strength_hs'] ?? ''); ?>" onkeypress="return numbersonly(this, event, false);" onkeyup="addTotal();">
							</div>
							<div class="col-6 col-md-2" id="divHSS" style="display: <?php echo (!empty($school_details[0]['strength_hss']) || (isset($school_details[0]['class_end']) && @$school_details[0]['class_end'] >= 11)) ? 'block' : 'none'; ?>;">
								<label class="form-label small fw-bold text-muted mb-1">HSS</label>
								<input type="text" name="txtTotalHSS" id="txtTotalHSS" class="form-control form-control-sm" maxlength="5" value="<?php echo esc(@$school_details[0]['strength_hss'] ?? ''); ?>" onkeypress="return numbersonly(this, event, false);" onkeyup="addTotal();">
							</div>
							<div class="col-6 col-md-2" id="divVHSC" style="display: <?php echo (!empty($school_details[0]['strength_vhss'])) ? 'block' : 'none'; ?>;">
								<label class="form-label small fw-bold text-muted mb-1">VHSE</label>
								<input type="text" name="txtTotalVHSS" id="txtTotalVHSS" class="form-control form-control-sm" maxlength="5" value="<?php echo esc(@$school_details[0]['strength_vhss'] ?? ''); ?>" onkeypress="return numbersonly(this, event, false);" onkeyup="addTotal();">
							</div>
							<div class="col-6 col-md-2">
								<label class="form-label small fw-bold text-dark mb-1">Total Strength</label>
								<input type="text" name="txtTotal" id="txtTotal" class="form-control form-control-sm bg-light fw-bold" readonly value="<?php echo esc(@$school_details[0]['total_strength'] ?? ''); ?>">
							</div>
						</div>
					</div>

					<!-- Form Action Buttons -->
					<div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
						<button type="button" class="btn btn-light border rounded-pill px-4" onclick="hideSchoolDetailsSection()">
							<i class="bi bi-arrow-left me-1"></i> Close / View Participants
						</button>
						<button type="button" id="btnSaveSchoolDetails" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background: linear-gradient(135deg, #4338ca 0%, #3b82f6 100%); border: none;" onclick="saveSchoolDetailsWithSpinner()">
							<i class="bi bi-check-circle-fill me-1"></i> Save School Details
						</button>
					</div>

					<?php echo form_close(); ?>
				</div>

			</div>
		</div>
	</div>
	<?php endif; ?>

<?php
echo form_open('schools/registration/save_participant', array('id' => 'formParticipant', 'enctype' => 'multipart/form-data'));
?>
<input type="hidden" name="hidSchoolId" id="hidSchoolId" value="<?php echo esc($active_code); ?>">
<input type="hidden" name="hidADNO" id="hidADNO" value="">
<?php
if (isset($is_edit) and $is_edit != 'no') {
	?>

	<!-- Participant Modal -->
	<div class="modal fade" id="participantModal" tabindex="-1" aria-labelledby="participantModalLabel" aria-hidden="true"
		data-bs-backdrop="static" data-bs-keyboard="false">
		<div class="modal-dialog modal-xl modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title text-white" id="participantModalLabel">Add Participant</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
						onclick="forceRemoveModalBackdrop()"></button>
				</div>
				<div class="modal-body" style="overflow-y: auto !important; max-height: 80vh;">
					<!-- Two Column Layout: Left (col-8) for inputs, Right (col-4) for class-based items -->
					<div class="row">
						<!-- Left Column: Participant Details (col-md-8) -->
						<div class="col-md-8">
							<div class="card border-0">
								<div class="card-body p-2">
									<h6 class="card-title fw-bold text-primary mb-3">Participant Details</h6>

									<!-- Row 1: Admission No. and Name (2 per row) -->
									<div class="row mb-3">
										<div class="col-md-6 mb-2">
											<label for="txtADNO" class="form-label fw-bold text-muted small text-uppercase">Admission No. <span class="text-danger">*</span></label>
											<?php echo form_input(
												"txtADNO",
												'',
												'class="form-control" id="txtADNO" maxlength="6" placeholder="Enter admission number" onkeyup="javascript:this.value=this.value.toUpperCase();"'
											); ?>
										</div>
										<div class="col-md-6 mb-2">
											<label for="txtParticipantName" class="form-label fw-bold text-muted small text-uppercase">Name <span class="text-danger">*</span></label>
											<?php echo form_input(
												"txtParticipantName",
												'',
												'class="form-control" id="txtParticipantName" placeholder="Enter student name" onkeyup="javascript:this.value=this.value.toUpperCase();"'
											); ?>
										</div>
									</div>

									<!-- Row 2: Class and Gender (2 per row) -->
									<div class="row mb-3">
										<div class="col-md-6 mb-2">
											<label for="txtClass" class="form-label fw-bold text-muted small text-uppercase">Class <span class="text-danger">*</span></label>
											<?php
											$class_array = array('' => '-- Select Class --');
											$start = (int)(@$school_details[0]['class_start'] ?: 1);
											$end = (int)(@$school_details[0]['class_end'] ?: 12);
											for ($i = $start; $i <= $end; $i++) {
												$class_array[$i] = $i;
											}
											echo form_dropdown(
												"txtClass",
												$class_array,
												'',
												'id="txtClass" class="form-select" onchange="loadItemsByClass()"'
											); ?>
										</div>
										<div class="col-md-6 mb-2">
											<label for="txtGender" class="form-label fw-bold text-muted small text-uppercase">Gender <span class="text-danger">*</span></label>
											<?php echo form_dropdown(
												"txtGender",
												array('' => '-- Select Gender --', 'B' => 'Boy', 'G' => 'Girl'),
												'',
												'class="form-select" id="txtGender" onchange="loadItemsByClass()"'
											); ?>
										</div>
									</div>

									<!-- Row 3: Item Codes (Dedicated Row) -->
									<div class="row mb-3">
										<div class="col-12">
											<label class="form-label fw-bold text-muted small text-uppercase mb-1">Item Codes (Max 5)</label>
											<div class="row g-2" id="itemCodesContainer">
												<?php
												for ($i = 1; $i <= 5; $i++) {
													echo '<div class="col-4 mb-2">';
													echo '<div class="position-relative">';
													echo form_input("txtItemCode_" . $i, '', 'class="form-control item-code-input text-center fw-bold pe-4" id="txtItemCode_' . $i . '" onkeypress="javascript:return numbersonly(this, event, false);" onKeyUp="javascript:change_text_max_to_focus(\'txtItemCode_' . $i . '\',\'txtItemCode_' . ($i + 1) . '\',this); checkItemTypeIcon(' . $i . ');" oninput="checkItemTypeIcon(' . $i . ');" onchange="validateItemCodeFinal(' . $i . ', false);" onblur="validateItemCodeFinal(' . $i . ', false);" maxlength="4" placeholder="Item ' . $i . '"');
													echo '<span id="itemTypeIcon_' . $i . '" class="position-absolute top-50 end-0 translate-middle-y me-2 d-flex align-items-center" style="display: none; pointer-events: none; z-index: 5;"></span>';
													echo '</div>';
													echo '<div class="item-details" id="itemDetails_' . $i . '" style="display: none; margin-top: 3px; padding: 2px 4px; background-color: #f8f9fa; border-radius: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">';
													echo '<small class="text-muted" id="itemName_' . $i . '" style="font-size: 0.65rem;" title=""></small>';
													echo '</div>';
													echo '</div>';
												}
												?>
											</div>
										</div>
									</div>

									<!-- Row 4: Pinnany Codes (Next Separate Row - 3 per row) -->
									<div class="row mb-3">
										<div class="col-12">
											<label class="form-label fw-bold text-muted small text-uppercase mb-1">Pinnany Codes (Max 3)</label>
											<div class="row g-2" id="pinnanyCodesContainer">
												<?php
												for ($i = 1; $i <= 3; $i++) {
													echo '<div class="col-4 mb-2">';
													echo '<div class="position-relative">';
													echo form_input(
														"txtPinnanyCode_" . $i,
														'',
														'class="form-control pinnany-code-input text-center fw-bold pe-4" id="txtPinnanyCode_' . $i . '" onkeypress="javascript:return numbersonly(this, event, false);" onKeyUp="javascript:change_text_max_to_focus(\'txtPinnanyCode_' . $i . '\',\'txtPinnanyCode_' . ($i + 1) . '\',this); checkPinnanyTypeIcon(' . $i . ');" oninput="checkPinnanyTypeIcon(' . $i . ');" onchange="validateItemCodeFinal(' . $i . ', true);" onblur="validateItemCodeFinal(' . $i . ', true);" maxlength="4" placeholder="Pinnany ' . $i . '"'
													);
													echo '<span id="pinnanyTypeIcon_' . $i . '" class="position-absolute top-50 end-0 translate-middle-y me-2 d-flex align-items-center" style="display: none; pointer-events: none; z-index: 5;"></span>';
													echo '</div>';
													echo '<div class="pinnany-details" id="pinnanyDetails_' . $i . '" style="display: none; margin-top: 3px; padding: 2px 4px; background-color: #f8f9fa; border-radius: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">';
													echo '<small class="text-muted" id="pinnanyName_' . $i . '" style="font-size: 0.65rem;" title=""></small>';
													echo '</div>';
													echo '</div>';
												}
												?>
											</div>
										</div>
									</div>

									<!-- Row 5: Photo Upload (Preview 90x90) -->
									<div class="row align-items-center g-3 mb-2">
										<div class="col-md-8">
											<label for="userfile" class="form-label fw-bold text-muted small text-uppercase">Photo Upload</label>
											<?php echo form_upload("userfile", '', 'class="form-control" id="userfile" accept="image/*" onchange="previewPhoto(this)"'); ?>
											<small class="text-muted d-block mt-1">Allowed formats: JPG, PNG, GIF (Max size: 1MB)</small>
										</div>
										<div class="col-md-4 text-center">
											<label class="form-label fw-bold text-muted small text-uppercase d-block">Photo Preview</label>
											<div id="photoPreview" class="d-flex align-items-center justify-content-center border rounded shadow-sm mx-auto" style="width: 90px; height: 90px; background-color: #f8f9fa; overflow: hidden;">
												<img id="currentPhoto" src="" alt="Photo" class="img-thumbnail p-0"
													style="width: 90px; height: 90px; object-fit: cover; display: none;" />
												<div id="noPhotoMessage" class="text-muted" style="font-size: 0.75rem;">No photo</div>
											</div>
										</div>
									</div>

								</div>
							</div>
						</div>

						<!-- Right Column: Class-based Items (col-md-4) -->
						<div class="col-md-4 d-flex flex-column">
							<div class="card border-0 bg-light rounded-3 shadow-2xs w-100 flex-grow-1 h-100">
								<div class="card-body p-3 d-flex flex-column">
									<h6 class="card-title fw-bold text-primary mb-2 d-flex align-items-center gap-1">
										<i class="bi bi-grid-fill fs-6"></i> Available Items
									</h6>
									<div id="availableItemsContainer" class="flex-grow-1 w-100" style="min-height: 380px; height: 100%; overflow-y: auto;">
										<div class="text-center text-muted py-4" id="noItemsMessage">
											<i class="bi bi-info-circle fs-4 text-secondary mb-2 d-block"></i>
											<p style="font-size: 0.85rem;" class="mb-0">Please select a class to view available items.</p>
										</div>
										<div id="itemsListContainer" style="display: none;">
											<!-- Items loaded here dynamically -->
										</div>
									</div>
								</div>
							</div>
						</div>
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
		@keyframes toastSlideInRight {
			from {
				transform: translateX(100%);
				opacity: 0;
			}
			to {
				transform: translateX(0);
				opacity: 1;
			}
		}
		.toast-slide-in {
			animation: toastSlideInRight 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
		}

		.hover-item-card {
			cursor: pointer;
		}
		.hover-item-card:hover {
			border-color: #0d6efd !important;
			background-color: #f0f7ff !important;
			box-shadow: 0 4px 12px rgba(13, 110, 253, 0.12) !important;
			transform: translateY(-1px);
		}

		/* Hide any remaining modal backdrops */
		.modal-backdrop {
			display: none !important;
			opacity: 0 !important;
			pointer-events: none !important;
		}

		/* Ensure body doesn't have modal-open class issues */
		body.modal-open {
			overflow: hidden !important;
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
			border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
			border-radius: 12px 12px 0 0 !important;
			background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%) !important;
			color: white !important;
			padding: 1rem 1.5rem !important;
		}

		.modal-header .modal-title {
			font-weight: 600 !important;
			font-size: 1.25rem !important;
		}

		.modal-header .btn-close {
			background-color: rgba(255, 255, 255, 0.25) !important;
			border-radius: 50% !important;
			width: 32px !important;
			height: 32px !important;
			opacity: 1 !important;
			padding: 6px !important;
			transition: all 0.2s ease !important;
		}

		.modal-header .btn-close:hover {
			background-color: rgba(255, 255, 255, 0.5) !important;
			opacity: 1 !important;
			transform: scale(1.1) !important;
		}

		/* Modal body styling */
		.modal-body {
			padding: 1.5rem !important;
			background: rgba(255, 255, 255, 0.98) !important;
			border-radius: 0 0 12px 12px !important;
		}

		/* Modal footer styling */
		.modal-footer {
			border-top: 1px solid rgba(0, 0, 0, 0.08) !important;
			padding: 1.25rem 1.5rem !important;
			background: #f8fafc !important;
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
			background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%) !important;
			color: white !important;
		}

		.modal-footer .btn-primary:hover {
			transform: translateY(-2px) !important;
			box-shadow: 0 4px 12px rgba(13, 110, 253, 0.35) !important;
		}

		.modal-footer .btn-secondary {
			background: #6c757d !important;
			color: white !important;
		}

		.modal-footer .btn-secondary:hover {
			transform: translateY(-2px) !important;
			box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3) !important;
		}

		/* Avatar & Participant styling */
		.participant-avatar-img {
			transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease;
			cursor: pointer;
		}
		.participant-avatar-img:hover {
			transform: scale(1.3);
			box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2) !important;
			z-index: 10;
		}

		.custom-participant-table tbody tr {
			transition: background-color 0.15s ease;
			border-bottom: 1px solid #f1f5f9;
		}
		.custom-participant-table tbody tr:hover {
			background-color: #f8fafc !important;
		}

		.shadow-2xs {
			box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
		}

		/* Modern Table Pagination Styles */
		.pagination .page-item .page-link {
			border-radius: 8px !important;
			margin: 0 2px;
			border: 1px solid #e2e8f0;
			color: #475569;
			font-weight: 500;
			font-size: 0.85rem;
			padding: 0.35rem 0.75rem;
			transition: all 0.2s ease;
		}
		.pagination .page-item.active .page-link {
			background: linear-gradient(135deg, #0d6efd, #0b5ed7) !important;
			border-color: #0d6efd !important;
			color: #ffffff !important;
			box-shadow: 0 2px 6px rgba(13, 110, 253, 0.3) !important;
		}
		.pagination .page-item:not(.active):not(.disabled) .page-link:hover {
			background-color: #f1f5f9 !important;
			color: #0d6efd !important;
			border-color: #cbd5e1;
		}
		.pagination .page-item.disabled .page-link {
			background-color: #f8fafc !important;
			color: #94a3b8 !important;
			border-color: #e2e8f0 !important;
			cursor: not-allowed;
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

		#participantModal .modal-body {
			padding: 0.75rem !important;
			overflow-y: hidden !important;
		}

		#participantModal .card-body {
			padding: 0.75rem !important;
		}

		#participantModal .form-control,
		#participantModal .form-select {
			font-size: 0.85rem !important;
			padding: 0.35rem 0.65rem !important;
		}

		#participantModal .form-label {
			font-size: 0.85rem !important;
			margin-bottom: 0.25rem !important;
		}

		#participantModal .row.mb-3 {
			margin-bottom: 0.75rem !important;
		}

		#participantModal .item-code-input,
		#participantModal .pinnany-code-input {
			font-size: 0.85rem !important;
			padding: 0.35rem 0.5rem !important;
			text-align: center !important;
		}

		#participantModal .input-group .btn {
			padding: 0.35rem 0.65rem !important;
			font-size: 0.85rem !important;
		}

		#participantModal .item-details,
		#participantModal .pinnany-details {
			min-height: 28px !important;
			padding: 3px 6px !important;
			font-size: 0.75rem !important;
			line-height: 1.2 !important;
		}

		#availableItemsContainer {
			max-height: 38vh !important;
			overflow-y: auto !important;
			padding-right: 5px !important;
		}

		/* Responsive adjustments */
		@media (min-width: 1200px) {
			#participantModal .modal-dialog {
				max-width: 970px !important;
				height: 90vh !important;
				margin: 5vh auto !important;
			}
			#participantModal .modal-content {
				height: 100% !important;
				display: flex !important;
				flex-direction: column !important;
			}
			#participantModal .modal-body {
				flex: 1 1 auto !important;
				overflow-y: hidden !important;
			}
			#participantModal .card {
				height: 100% !important;
			}
			#participantModal .card-body {
				height: 100% !important;
				display: flex !important;
				flex-direction: column !important;
			}
			#availableItemsContainer {
				flex: 1 1 auto !important;
				max-height: calc(90vh - 180px) !important;
				overflow-y: auto !important;
			}
		}

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
			background-color: #f4faf4 !important;
			border: 2px solid #28a745 !important;
			opacity: 0.8 !important;
			cursor: not-allowed !important;
		}

		.items-container {
			max-height: none !important;
			overflow-y: visible !important;
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
	</script>

<?php
$db = \Config\Database::connect();
$raw_items = !empty($all_festival_items) ? $all_festival_items : [];
if (empty($raw_items)) {
	$raw_items = $db->table('item_master IM')
		->select('IM.item_code, IM.item_name, IM.item_type, IM.gender as item_gender, IM.max_participants, IM.max_pinnani as max_pinnany, FM.fest_id, FM.fest_name, FM.class_start, FM.class_end')
		->join('festival_master FM', 'IM.fest_id = FM.fest_id')
		->orderBy('FM.fest_id', 'ASC')
		->orderBy('IM.item_code', 'ASC')
		->get()->getResultArray();
}

$target_sch = !empty($school_details[0]['school_code']) ? $school_details[0]['school_code'] : $active_code;
$registered_school_items = [];
if (!empty($target_sch)) {
	$registered_school_items = $db->table('sd_participant_item_details PD')
		->select('PD.item_code, IM.item_type, IM.max_participants, PD.admn_no')
		->join('item_master IM', 'IM.item_code = PD.item_code', 'left')
		->where('PD.school_code', $target_sch)
		->get()->getResultArray();
}
?>
<script>
window.allFestivalItems = <?php echo json_encode($raw_items); ?>;
window.schoolRegisteredItems = <?php echo json_encode($registered_school_items); ?>;
window.loadedItemsMap = {};

if (Array.isArray(window.allFestivalItems)) {
	window.allFestivalItems.forEach(function(item) {
		item.gender = (item.gender || item.item_gender || 'C').toUpperCase();
		item.item_gender = item.gender;
		item.item_type = (item.item_type || 'S').toUpperCase();
		item.max_participants = parseInt(item.max_participants, 10) || (item.item_type === 'G' ? 7 : 1);
		item.max_pinnani = parseInt(item.max_pinnani || item.max_pinnany, 10) || 0;
		item.fest_name = item.fest_name || 'General';
		item.class_start = parseInt(item.class_start, 10) || 1;
		item.class_end = parseInt(item.class_end, 10) || 12;

		window.loadedItemsMap[item.item_code] = item;
		window.loadedItemsMap[String(parseInt(item.item_code, 10))] = item;
	});
}

window.getItemSchoolCount = function(itemCode, excludeAdmnNo) {
	var count = 0;
	if (Array.isArray(window.schoolRegisteredItems)) {
		var codeNum = parseInt(itemCode, 10);
		window.schoolRegisteredItems.forEach(function(reg) {
			var isSameCode = (String(reg.item_code).trim() === String(itemCode).trim()) || (parseInt(reg.item_code, 10) === codeNum);
			if (isSameCode) {
				if (!excludeAdmnNo || String(reg.admn_no).trim() !== String(excludeAdmnNo).trim()) {
					count++;
				}
			}
		});
	}
	return count;
};

window.getSingleUserIconHtml = function() {
	return '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="#198754" viewBox="0 0 16 16" title="Single Item"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>';
};

window.getGroupUserIconHtml = function() {
	return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#0d6efd" viewBox="0 0 16 16" title="Group Item"><path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/></svg>';
};

window.updateItemCodeIcon = function(index, itemType, itemName) {
	var iconSpan = document.getElementById('itemTypeIcon_' + index);
	var detailDiv = document.getElementById('itemDetails_' + index);
	var nameSpan = document.getElementById('itemName_' + index);
	if (iconSpan) {
		if (itemType === 'G') {
			iconSpan.innerHTML = window.getGroupUserIconHtml();
			iconSpan.style.display = 'inline-flex';
		} else if (itemType === 'S' || itemType === 'P') {
			iconSpan.innerHTML = window.getSingleUserIconHtml();
			iconSpan.style.display = 'inline-flex';
		} else {
			iconSpan.innerHTML = '';
			iconSpan.style.display = 'none';
		}
	}
	if (itemName && detailDiv && nameSpan) {
		nameSpan.innerText = itemName + ' (' + (itemType || 'S') + ')';
		nameSpan.title = itemName;
		detailDiv.style.display = 'block';
	}
};

window.updatePinnanyCodeIcon = function(index, itemType, itemName) {
	var iconSpan = document.getElementById('pinnanyTypeIcon_' + index);
	var detailDiv = document.getElementById('pinnanyDetails_' + index);
	var nameSpan = document.getElementById('pinnanyName_' + index);
	if (iconSpan) {
		if (itemType === 'G') {
			iconSpan.innerHTML = window.getGroupUserIconHtml();
			iconSpan.style.display = 'inline-flex';
		} else if (itemType === 'S' || itemType === 'P') {
			iconSpan.innerHTML = window.getSingleUserIconHtml();
			iconSpan.style.display = 'inline-flex';
		} else {
			iconSpan.innerHTML = '';
			iconSpan.style.display = 'none';
		}
	}
	if (itemName && detailDiv && nameSpan) {
		nameSpan.innerText = itemName + ' (' + (itemType || 'P') + ')';
		nameSpan.title = itemName;
		detailDiv.style.display = 'block';
	}
};

window.showWarningToast = function(title, message) {
	var toastContainer = document.getElementById('appToastContainer');
	if (!toastContainer) {
		toastContainer = document.createElement('div');
		toastContainer.id = 'appToastContainer';
		toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
		toastContainer.style.zIndex = '999999';
		document.body.appendChild(toastContainer);
	}

	var toastId = 'toast_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
	var toastHtml = '<div id="' + toastId + '" class="toast align-items-center bg-white border-start border-4 border-warning shadow-lg show mb-2 toast-slide-in" role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 320px; max-width: 400px; border-radius: 8px;">' +
		'<div class="d-flex p-3 align-items-start gap-2">' +
		'<div class="fs-4 text-warning d-flex align-items-center flex-shrink-0"><i class="bi bi-exclamation-triangle-fill"></i></div>' +
		'<div class="flex-grow-1">' +
		'<div class="fw-bold text-dark fs-6 mb-1">' + (title || 'Warning') + '</div>' +
		'<div class="text-secondary small lh-sm">' + (message || '') + '</div>' +
		'</div>' +
		'<button type="button" class="btn-close ms-2 mt-1 flex-shrink-0" data-bs-dismiss="toast" aria-label="Close" onclick="var el = document.getElementById(\'' + toastId + '\'); if(el) el.remove();"></button>' +
		'</div>' +
		'</div>';
	toastContainer.insertAdjacentHTML('beforeend', toastHtml);

	// Auto-remove after 4.5 seconds
	setTimeout(function() {
		var el = document.getElementById(toastId);
		if (el) {
			el.classList.remove('show');
			el.style.opacity = '0';
			el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
			el.style.transform = 'translateX(20px)';
			setTimeout(function() {
				if (el && el.parentNode) el.remove();
			}, 400);
		}
	}, 4500);
};

window.validateItemCodeFinal = function(index, isPinnany) {
	var prefix = isPinnany ? 'txtPinnanyCode_' : 'txtItemCode_';
	var iconPrefix = isPinnany ? 'pinnanyTypeIcon_' : 'itemTypeIcon_';
	var detailPrefix = isPinnany ? 'pinnanyDetails_' : 'itemDetails_';
	var namePrefix = isPinnany ? 'pinnanyName_' : 'itemName_';

	var inputEl = document.getElementById(prefix + index);
	if (!inputEl) return;
	var val = inputEl.value.trim();
	var iconSpan = document.getElementById(iconPrefix + index);
	var detailDiv = document.getElementById(detailPrefix + index);
	var nameSpan = document.getElementById(namePrefix + index);

	if (!val) {
		if (iconSpan) { iconSpan.style.display = 'none'; iconSpan.innerHTML = ''; }
		if (detailDiv) detailDiv.style.display = 'none';
		if (nameSpan) nameSpan.innerText = '';
		inputEl.classList.remove('is-invalid');
		inputEl.style.borderColor = '';
		inputEl.style.boxShadow = '';
		return;
	}

	var txtClass = document.getElementById('txtClass');
	var txtGender = document.getElementById('txtGender');
	var selectedClass = txtClass ? txtClass.value.trim() : '';
	var selectedGender = txtGender ? txtGender.value.trim() : '';

	if (!selectedClass) {
		inputEl.classList.add('is-invalid');
		inputEl.style.borderColor = '#dc3545';
		inputEl.style.boxShadow = '0 0 0 0.25rem rgba(220, 53, 69, 0.25)';
		inputEl.value = '';
		if (iconSpan) { iconSpan.style.display = 'none'; iconSpan.innerHTML = ''; }
		if (detailDiv) detailDiv.style.display = 'none';
		if (nameSpan) nameSpan.innerText = '';

		window.showWarningToast(
			'Class Not Selected',
			'Please select a Student Class before entering item codes.'
		);

		setTimeout(function() {
			inputEl.classList.remove('is-invalid');
			inputEl.style.borderColor = '';
			inputEl.style.boxShadow = '';
		}, 2500);
		return;
	}

	var classNum = parseInt(selectedClass, 10);
	var itemCodeNum = parseInt(val, 10);

	var found = null;
	if (Array.isArray(window.allFestivalItems)) {
		found = window.allFestivalItems.find(function(it) {
			return String(it.item_code) === String(val) || parseInt(it.item_code, 10) === itemCodeNum;
		});
	}

	if (!found) {
		inputEl.classList.add('is-invalid');
		inputEl.style.borderColor = '#dc3545';
		inputEl.style.boxShadow = '0 0 0 0.25rem rgba(220, 53, 69, 0.25)';
		inputEl.value = '';
		if (iconSpan) { iconSpan.style.display = 'none'; iconSpan.innerHTML = ''; }
		if (detailDiv) detailDiv.style.display = 'none';
		if (nameSpan) nameSpan.innerText = '';

		window.showWarningToast(
			'Invalid Item Code',
			'Item code "' + val + '" does not exist in festival master.'
		);

		setTimeout(function() {
			inputEl.classList.remove('is-invalid');
			inputEl.style.borderColor = '';
			inputEl.style.boxShadow = '';
		}, 2500);
		return;
	}

	var cStart = parseInt(found.class_start, 10);
	var cEnd = parseInt(found.class_end, 10);
	var matchesClass = classNum >= cStart && classNum <= cEnd;

	if (!matchesClass) {
		inputEl.classList.add('is-invalid');
		inputEl.style.borderColor = '#dc3545';
		inputEl.style.boxShadow = '0 0 0 0.25rem rgba(220, 53, 69, 0.25)';
		inputEl.value = '';
		if (iconSpan) { iconSpan.style.display = 'none'; iconSpan.innerHTML = ''; }
		if (detailDiv) detailDiv.style.display = 'none';
		if (nameSpan) nameSpan.innerText = '';

		window.showWarningToast(
			'Invalid Item Category',
			'Item ' + val + ' (' + found.item_name + ') is only for Classes ' + cStart + ' to ' + cEnd + ', not Class ' + selectedClass + '.'
		);

		setTimeout(function() {
			inputEl.classList.remove('is-invalid');
			inputEl.style.borderColor = '';
			inputEl.style.boxShadow = '';
		}, 2500);
		return;
	}

	var itemGender = (found.gender || found.item_gender || 'C').toUpperCase();
	if (selectedGender && selectedGender !== 'C' && itemGender !== 'C' && itemGender !== selectedGender) {
		inputEl.classList.add('is-invalid');
		inputEl.style.borderColor = '#dc3545';
		inputEl.style.boxShadow = '0 0 0 0.25rem rgba(220, 53, 69, 0.25)';
		inputEl.value = '';
		if (iconSpan) { iconSpan.style.display = 'none'; iconSpan.innerHTML = ''; }
		if (detailDiv) detailDiv.style.display = 'none';
		if (nameSpan) nameSpan.innerText = '';

		var genderName = itemGender === 'B' ? 'Boys' : 'Girls';
		window.showWarningToast(
			'Invalid Gender for Item',
			'Item ' + val + ' (' + found.item_name + ') is restricted to ' + genderName + ' only.'
		);

		setTimeout(function() {
			inputEl.classList.remove('is-invalid');
			inputEl.style.borderColor = '';
			inputEl.style.boxShadow = '';
		}, 2500);
		return;
	}

	// Check 4: Duplicate item code within current modal form slots
	var duplicateFound = false;
	for (var f = 1; f <= 5; f++) {
		if (!isPinnany && f === index) continue;
		var inpOther = document.getElementById('txtItemCode_' + f);
		if (inpOther && inpOther.value.trim() !== '') {
			var otherVal = inpOther.value.trim();
			if (otherVal === val || parseInt(otherVal, 10) === itemCodeNum) {
				duplicateFound = true;
				break;
			}
		}
	}
	for (var p = 1; p <= 3; p++) {
		if (isPinnany && p === index) continue;
		var inPOther = document.getElementById('txtPinnanyCode_' + p);
		if (inPOther && inPOther.value.trim() !== '') {
			var otherPVal = inPOther.value.trim();
			if (otherPVal === val || parseInt(otherPVal, 10) === itemCodeNum) {
				duplicateFound = true;
				break;
			}
		}
	}

	if (duplicateFound) {
		inputEl.classList.add('is-invalid');
		inputEl.style.borderColor = '#dc3545';
		inputEl.style.boxShadow = '0 0 0 0.25rem rgba(220, 53, 69, 0.25)';
		inputEl.value = '';
		if (iconSpan) { iconSpan.style.display = 'none'; iconSpan.innerHTML = ''; }
		if (detailDiv) detailDiv.style.display = 'none';
		if (nameSpan) nameSpan.innerText = '';

		window.showWarningToast(
			'Duplicate Item',
			'Item ' + val + ' (' + found.item_name + ') has already been entered in another slot.'
		);

		setTimeout(function() {
			inputEl.classList.remove('is-invalid');
			inputEl.style.borderColor = '';
			inputEl.style.boxShadow = '';
		}, 2500);
		return;
	}

	// Check 5: Max participants limit check based on item_master.max_participants
	var currentAdmnNo = (jQuery('#hidADNO').val() || '').trim();
	var maxParts = parseInt(found.max_participants, 10) || (found.item_type === 'G' ? 7 : 1);
	var schoolCount = typeof window.getItemSchoolCount === 'function' ? window.getItemSchoolCount(val, currentAdmnNo) : 0;

	if (schoolCount >= maxParts) {
		inputEl.classList.add('is-invalid');
		inputEl.style.borderColor = '#dc3545';
		inputEl.style.boxShadow = '0 0 0 0.25rem rgba(220, 53, 69, 0.25)';
		inputEl.value = '';
		if (iconSpan) { iconSpan.style.display = 'none'; iconSpan.innerHTML = ''; }
		if (detailDiv) detailDiv.style.display = 'none';
		if (nameSpan) nameSpan.innerText = '';

		var limitMsg = (found.item_type === 'S')
			? 'Single Item ' + val + ' (' + found.item_name + ') is already registered by another participant in this school.'
			: 'Group Item ' + val + ' (' + found.item_name + ') has reached the maximum allowed limit of ' + maxParts + ' participants for this school.';

		window.showWarningToast(
			'Maximum Limit Reached',
			limitMsg
		);

		setTimeout(function() {
			inputEl.classList.remove('is-invalid');
			inputEl.style.borderColor = '';
			inputEl.style.boxShadow = '';
		}, 2500);
		return;
	}

	inputEl.classList.remove('is-invalid');
	inputEl.style.borderColor = '#198754';
	inputEl.style.boxShadow = '';
	setTimeout(function() { if (inputEl.style.borderColor === 'rgb(25, 135, 84)') inputEl.style.borderColor = ''; }, 1200);

	window.loadedItemsMap[val] = found;
	if (isPinnany) {
		window.updatePinnanyCodeIcon(index, found.item_type, found.item_name);
	} else {
		window.updateItemCodeIcon(index, found.item_type, found.item_name);
	}

	// Dynamically refresh available items so this assigned item is hidden
	if (typeof window.loadItemsByClass === 'function') {
		window.loadItemsByClass();
	}
};

window.checkItemTypeIcon = function(index) {
	var inputEl = document.getElementById('txtItemCode_' + index);
	var val = inputEl ? inputEl.value.trim() : '';
	if (!val) {
		window.validateItemCodeFinal(index, false);
		return;
	}
	if (val.length >= 3) {
		window.validateItemCodeFinal(index, false);
	} else {
		var iconSpan = document.getElementById('itemTypeIcon_' + index);
		var detailDiv = document.getElementById('itemDetails_' + index);
		var nameSpan = document.getElementById('itemName_' + index);
		if (iconSpan) { iconSpan.style.display = 'none'; iconSpan.innerHTML = ''; }
		if (detailDiv) detailDiv.style.display = 'none';
		if (nameSpan) nameSpan.innerText = '';
	}
};

window.checkPinnanyTypeIcon = function(index) {
	var inputEl = document.getElementById('txtPinnanyCode_' + index);
	var val = inputEl ? inputEl.value.trim() : '';
	if (!val) {
		window.validateItemCodeFinal(index, true);
		return;
	}
	if (val.length >= 3) {
		window.validateItemCodeFinal(index, true);
	} else {
		var iconSpan = document.getElementById('pinnanyTypeIcon_' + index);
		var detailDiv = document.getElementById('pinnanyDetails_' + index);
		var nameSpan = document.getElementById('pinnanyName_' + index);
		if (iconSpan) { iconSpan.style.display = 'none'; iconSpan.innerHTML = ''; }
		if (detailDiv) detailDiv.style.display = 'none';
		if (nameSpan) nameSpan.innerText = '';
	}
};

window.loadItemsByClass = function() {
	if (window._loadingItemsByClass) return;
	window._loadingItemsByClass = true;
	try {
	var txtClass = document.getElementById('txtClass');
	var txtGender = document.getElementById('txtGender');
	var availableItemsContainer = document.getElementById('availableItemsContainer');

	if (!txtClass || !availableItemsContainer) return;
	var selectedClass = (txtClass.value || '').trim();
	var selectedGender = txtGender ? (txtGender.value || '').trim().toUpperCase() : '';

	if (!selectedClass) {
		availableItemsContainer.innerHTML = '<div class="text-center text-muted py-4" id="noItemsMessage"><i class="bi bi-info-circle fs-4 text-secondary mb-2 d-block"></i><p style="font-size: 0.85rem;" class="mb-0">Please select a class to view available items.</p></div>';
		return;
	}

	var classNum = parseInt(selectedClass, 10);
	var hidAdnoEl = document.getElementById('hidADNO');
	var currentAdmnNo = (hidAdnoEl ? hidAdnoEl.value : '').trim();

	// 1. Items currently filled in THIS modal form inputs (slots 1..5 and pinnany 1..3)
	var currentFormChosen = {};
	for (var f = 1; f <= 5; f++) {
		var inp = document.getElementById('txtItemCode_' + f);
		if (inp && inp.value.trim() !== '') {
			var code = inp.value.trim();
			currentFormChosen[code] = true;
			currentFormChosen[String(parseInt(code, 10))] = true;
		}
	}
	for (var p = 1; p <= 3; p++) {
		var inP = document.getElementById('txtPinnanyCode_' + p);
		if (inP && inP.value.trim() !== '') {
			var pCode = inP.value.trim();
			currentFormChosen[pCode] = true;
			currentFormChosen[String(parseInt(pCode, 10))] = true;
		}
	}

	// Filter items for the selected class category
	var items = [];
	if (Array.isArray(window.allFestivalItems) && window.allFestivalItems.length > 0) {
		items = window.allFestivalItems.filter(function(it) {
			var cStart = parseInt(it.class_start, 10);
			var cEnd = parseInt(it.class_end, 10);

			// Class category matching
			var matchesClass = false;
			if (!isNaN(cStart) && !isNaN(cEnd)) {
				matchesClass = (classNum >= cStart && classNum <= cEnd);
			} else {
				if (classNum <= 4) matchesClass = (parseInt(it.item_code, 10) >= 100 && parseInt(it.item_code, 10) < 300);
				else if (classNum <= 7) matchesClass = (parseInt(it.item_code, 10) >= 300 && parseInt(it.item_code, 10) < 500);
				else if (classNum <= 10) matchesClass = (parseInt(it.item_code, 10) >= 500 && parseInt(it.item_code, 10) < 700);
				else matchesClass = (parseInt(it.item_code, 10) >= 700);
			}
			if (!matchesClass) return false;

			// Gender matching
			var itmGender = (it.gender || it.item_gender || 'C').toUpperCase();
			if (selectedGender && selectedGender !== 'C') {
				if (itmGender !== selectedGender && itmGender !== 'C') return false;
			}

			// Rule 1: Hide if already chosen in this participant's current form slots
			var codeStr = String(it.item_code);
			var codeInt = String(parseInt(it.item_code, 10));
			if (currentFormChosen[codeStr] || currentFormChosen[codeInt]) {
				return false;
			}

			// Rule 2: Hide if registered count has reached max_participants quota for this school
			var maxParts = parseInt(it.max_participants, 10) || (it.item_type === 'G' ? 7 : 1);
			var schoolCount = typeof window.getItemSchoolCount === 'function' ? window.getItemSchoolCount(it.item_code, currentAdmnNo) : 0;
			if (schoolCount >= maxParts) {
				return false;
			}

			return true;
		});
	}

	if (items.length === 0) {
		availableItemsContainer.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-check2-all text-success fs-3 mb-2 d-block"></i><p class="mb-0 small text-muted">All available items for Class ' + selectedClass + ' have been assigned.</p></div>';
		return;
	}

	var groupedItems = {};
	items.forEach(function (item) {
		window.loadedItemsMap[item.item_code] = item;
		window.loadedItemsMap[String(parseInt(item.item_code, 10))] = item;
		var fest = item.fest_name || 'Category Items';
		if (!groupedItems[fest]) groupedItems[fest] = [];
		groupedItems[fest].push(item);
	});

	// Update icons for any existing item values in inputs
	for (var k = 1; k <= 5; k++) {
		if (typeof window.checkItemTypeIcon === 'function') window.checkItemTypeIcon(k);
	}
	for (var p = 1; p <= 3; p++) {
		if (typeof window.checkPinnanyTypeIcon === 'function') window.checkPinnanyTypeIcon(p);
	}

	var itemsHtml = '<div class="items-container w-100">';
	for (var festName in groupedItems) {
		if (groupedItems.hasOwnProperty(festName)) {
			itemsHtml += '<div class="festival-group mb-3">' +
				'<div class="festival-title text-primary fw-bold mb-2 pb-1 border-bottom small d-flex align-items-center justify-content-between">' +
				'<span><i class="bi bi-bookmark-star-fill text-primary me-1" style="font-size: 0.75rem;"></i> ' + festName + '</span>' +
				'<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill" style="font-size: 0.68rem;">' + groupedItems[festName].length + ' items</span>' +
				'</div>' +
				'<div class="d-flex flex-column gap-2 w-100">';

			groupedItems[festName].forEach(function (item) {
				itemsHtml += '<div class="p-2 px-3 rounded-3 border bg-white border-light-subtle shadow-2xs hover-item-card item-selectable" data-item-code="' + item.item_code + '" data-item-name="' + item.item_name + '" data-item-type="' + item.item_type + '" style="cursor: pointer; transition: all 0.15s ease-in-out; width: 100%;">' +
					'<div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;" title="' + item.item_code + ' - ' + item.item_name + '">' +
					'<span class="text-primary me-1">' + item.item_code + '</span> - ' + item.item_name +
					'</div>' +
					'</div>';
			});
			itemsHtml += '</div></div>';
		}
	}
	itemsHtml += '</div>';

	availableItemsContainer.innerHTML = itemsHtml;

	// Attach click event listeners to add item directly
	var selectableItems = availableItemsContainer.querySelectorAll('.item-selectable');
	selectableItems.forEach(function(elem) {
		elem.addEventListener('click', function(e) {
			e.preventDefault();
			var itemCode = this.getAttribute('data-item-code');
			var itemName = this.getAttribute('data-item-name');
			var itemType = this.getAttribute('data-item-type');
			if (!itemCode || !itemName) return;

			var emptyInput = null;
			var fieldNumber = 0;
			for (var f = 1; f <= 5; f++) {
				var inp = document.getElementById('txtItemCode_' + f);
				if (inp && inp.value.trim() === '') {
					emptyInput = inp;
					fieldNumber = f;
					break;
				}
			}

			if (emptyInput && fieldNumber > 0) {
				emptyInput.value = itemCode;
				if (typeof window.validateItemCodeFinal === 'function') {
					window.validateItemCodeFinal(fieldNumber, false);
				} else if (typeof window.updateItemCodeIcon === 'function') {
					window.updateItemCodeIcon(fieldNumber, itemType, itemName);
				}
				// Re-render available items so this chosen item is instantly hidden from the list
				window.loadItemsByClass();
			} else {
				if (typeof Swal !== 'undefined') {
					Swal.fire({ icon: 'warning', title: 'Oops!', text: 'All 5 item slots are filled. Clear a slot to add another item.' });
				} else if (typeof swal !== 'undefined') {
					swal("Oops!", "All 5 item slots are filled. Clear a slot to add another item.", "warning");
				} else {
					alert("All 5 item slots are filled.");
				}
			}
		});
	});
	} finally {
		window._loadingItemsByClass = false;
	}
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
			var isAdmin = <?php echo (!empty($is_admin)) ? 'true' : 'false'; ?>;
			var schoolDetailsSection = document.getElementById('schoolDetailsSection');
			var registeredParticipantsWrapper = document.getElementById('registeredParticipantsWrapper');
			var schoolDetailsToggleBtnText = document.getElementById('schoolDetailsToggleBtnText');
			var addParticipantBtn = document.getElementById('addParticipantBtn');

			var currentSchoolCode = '<?php echo esc($active_code); ?>';
			var hasSchoolCode = (currentSchoolCode !== '' && currentSchoolCode !== '0');

			if (!isAdmin) {
				// School User: Always hide school details, always show registered participants, show Add Participant button
				if (schoolDetailsSection) schoolDetailsSection.style.display = 'none';
				if (registeredParticipantsWrapper) registeredParticipantsWrapper.style.display = 'block';
				if (addParticipantBtn) addParticipantBtn.style.display = 'inline-block';
				return;
			}

			// Admin Users (IT Master, State Admin, District Admin, Sub District Admin):
			// Only show Add Participant button if a school is loaded
			if (addParticipantBtn) {
				addParticipantBtn.style.display = hasSchoolCode ? 'inline-block' : 'none';
			}

			// When a school is loaded (hasSchoolCode), do NOT show/expand school details on page load or reload
			if (hasSchoolCode) {
				if (schoolDetailsSection) schoolDetailsSection.style.display = 'none';
				if (registeredParticipantsWrapper) registeredParticipantsWrapper.style.display = 'block';
				if (schoolDetailsToggleBtnText) schoolDetailsToggleBtnText.textContent = 'School Details';
			} else {
				if (schoolDetailsSection) schoolDetailsSection.style.display = 'block';
				if (registeredParticipantsWrapper) registeredParticipantsWrapper.style.display = 'none';
				if (schoolDetailsToggleBtnText) schoolDetailsToggleBtnText.textContent = 'Hide School Details';
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

		// Toggle School Details & Registered Participants Visibility
		function toggleSchoolDetailsSection() {
			var sec = document.getElementById('schoolDetailsSection');
			var part = document.getElementById('registeredParticipantsWrapper');
			var btnText = document.getElementById('schoolDetailsToggleBtnText');
			if (!sec) return;
			var isHidden = (sec.style.display === 'none' || sec.style.display === '');
			if (isHidden) {
				sec.style.display = 'block';
				if (part) part.style.display = 'none';
				if (btnText) btnText.textContent = 'Hide School Details';
			} else {
				sec.style.display = 'none';
				if (part) part.style.display = 'block';
				if (btnText) btnText.textContent = 'School Details';
			}
		}

		function showSchoolDetailsSection() {
			var sec = document.getElementById('schoolDetailsSection');
			var part = document.getElementById('registeredParticipantsWrapper');
			var btnText = document.getElementById('schoolDetailsToggleBtnText');
			if (sec) sec.style.display = 'block';
			if (part) part.style.display = 'none';
			if (btnText) btnText.textContent = 'Hide School Details';
		}

		function hideSchoolDetailsSection() {
			var sec = document.getElementById('schoolDetailsSection');
			var part = document.getElementById('registeredParticipantsWrapper');
			var btnText = document.getElementById('schoolDetailsToggleBtnText');
			if (sec) sec.style.display = 'none';
			if (part) part.style.display = 'block';
			if (btnText) btnText.textContent = 'School Details';
		}

		function handleSchoolSelect(code) {
			if (!code || code === '0' || code === 0) return;
			loadSchoolByCode(code);
		}

		function extractSchoolCode(val) {
			if (!val) return '';
			val = String(val).trim();
			
			// 1. Direct number check (e.g. "12001")
			if (/^\d+$/.test(val)) {
				return val;
			}
			
			// 2. Starts with digits (e.g. "12001 - GHSS Example")
			var match = val.match(/^(\d+)/);
			if (match && match[1]) {
				return match[1];
			}
			
			// 3. Search in SUBDISTRICT_SCHOOLS_LIST by name or code
			if (typeof SUBDISTRICT_SCHOOLS_LIST !== 'undefined' && Array.isArray(SUBDISTRICT_SCHOOLS_LIST)) {
				var lowerVal = val.toLowerCase();
				for (var i = 0; i < SUBDISTRICT_SCHOOLS_LIST.length; i++) {
					var item = SUBDISTRICT_SCHOOLS_LIST[i];
					if (item.code === val || item.name.toLowerCase().indexOf(lowerVal) !== -1) {
						return item.code;
					}
				}
			}
			
			// 4. Search in datalist options
			var list = document.getElementById('schoolOptionsList');
			if (list && list.options) {
				var lowerVal2 = val.toLowerCase();
				for (var j = 0; j < list.options.length; j++) {
					var opt = list.options[j];
					var optVal = (opt.value || '').trim();
					var optText = (opt.text || opt.innerText || '').trim();
					if (optVal.toLowerCase().indexOf(lowerVal2) !== -1) {
						var m = optVal.match(/^(\d+)/);
						if (m && m[1]) return m[1];
					}
					if (optText.toLowerCase().indexOf(lowerVal2) !== -1) {
						var m2 = optText.match(/^(\d+)/);
						if (m2 && m2[1]) return m2[1];
					}
				}
			}
			return '';
		}

		var _fetchSchoolTimer = null;
		function fetchSchoolDataAjax(code) {
			if (!code || code === '0' || !/^\d+$/.test(code)) return;
			
			clearTimeout(_fetchSchoolTimer);
			_fetchSchoolTimer = setTimeout(function() {
				var url = '<?php echo base_url("schools/registration/fetch_school_data_json"); ?>';
				jQuery.ajax({
					url: url,
					type: 'POST',
					data: { code: code },
					dataType: 'json',
					success: function(resp) {
						if (resp && resp.status === 'success' && resp.data) {
							populateSchoolForm(resp.data);
						}
					},
					error: function(err) {
						console.warn('AJAX fetch school details notice:', err);
					}
				});
			}, 200);
		}

		function populateSchoolForm(data) {
			if (!data) return;
			
			var schoolCode = data.school_code || '';
			var hidSchoolId = document.getElementById('formSchoolHidSchoolId');
			if (hidSchoolId) hidSchoolId.value = schoolCode;
			
			var pageHidSchoolId = document.getElementById('hidSchoolId');
			if (pageHidSchoolId) pageHidSchoolId.value = schoolCode;
			
			var txtSchoolName = document.getElementById('txtSchoolName');
			if (txtSchoolName) txtSchoolName.value = data.school_name || 'Not Found';
			
			var txtSchoolType = document.getElementById('txtSchoolType');
			if (txtSchoolType) txtSchoolType.value = data.school_type_text || (data.school_type === 'G' ? 'Government (Govt)' : (data.school_type === 'A' ? 'Aided' : (data.school_type === 'U' ? 'Unaided / Private' : '-')));
			
			var txtSchoolEmail = document.getElementById('txtSchoolEmail');
			if (txtSchoolEmail) txtSchoolEmail.value = data.school_email || '';
			
			var txtSchoolPhone = document.getElementById('txtSchoolPhone');
			if (txtSchoolPhone) txtSchoolPhone.value = data.school_phone || '';
			
			var txtStandardFrom = document.getElementById('txtStandardFrom');
			if (txtStandardFrom) {
				txtStandardFrom.value = data.class_start || '1';
			}
			
			var txtStandardTo = document.getElementById('txtStandardTo');
			if (txtStandardTo) {
				txtStandardTo.value = data.class_end || '10';
			}
			
			var txtPrincipal = document.getElementById('txtPrincipal');
			if (txtPrincipal) txtPrincipal.value = data.principal_name || '';
			
			var txtPrincipalPhone = document.getElementById('txtPrincipalPhone');
			if (txtPrincipalPhone) txtPrincipalPhone.value = data.principal_phone || '';
			
			var txtHeadmaster = document.getElementById('txtHeadmaster');
			if (txtHeadmaster) txtHeadmaster.value = data.hm_name || '';
			
			var txtHeadmasterPhone = document.getElementById('txtHeadmasterPhone');
			if (txtHeadmasterPhone) txtHeadmasterPhone.value = data.hm_phone || '';
			
			populateTeachersList(data.teachers || '');
			
			var txtTotalLP = document.getElementById('txtTotalLP');
			if (txtTotalLP) txtTotalLP.value = data.strength_lp || '';
			
			var txtTotalUP = document.getElementById('txtTotalUP');
			if (txtTotalUP) txtTotalUP.value = data.strength_up || '';
			
			var txtTotalHS = document.getElementById('txtTotalHS');
			if (txtTotalHS) txtTotalHS.value = data.strength_hs || '';
			
			var txtTotalHSS = document.getElementById('txtTotalHSS');
			if (txtTotalHSS) txtTotalHSS.value = data.strength_hss || '';
			
			var txtTotalVHSS = document.getElementById('txtTotalVHSS');
			if (txtTotalVHSS) txtTotalVHSS.value = data.strength_vhss || '';
			
			var txtTotal = document.getElementById('txtTotal');
			if (txtTotal) txtTotal.value = data.total_strength || '';
			
			if (typeof fncShowHideStd === 'function') fncShowHideStd();
			if (typeof addTotal === 'function') addTotal();
			
			var badge = document.getElementById('activeSchoolBadge');
			if (badge && schoolCode) {
				badge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Active: ' + schoolCode;
				badge.className = 'badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-7 fw-semibold';
			}
		}

		function populateTeachersList(teachersStr) {
			var container = document.getElementById('teachersRow');
			var hidTeachers = document.getElementById('hidTeachers');
			if (!container) return;
			
			var existingRows = container.querySelectorAll('.teachersTextBox');
			existingRows.forEach(function(row) { row.remove(); });
			
			var teacherPairs = teachersStr ? teachersStr.split('#@#').filter(function(t) { return t.trim() !== ''; }) : [];
			
			if (teacherPairs.length === 0) {
				createTeacherRow(1, '', '', false);
				if (hidTeachers) hidTeachers.value = 1;
			} else {
				teacherPairs.forEach(function(pair, index) {
					var info = pair.split('#$#');
					var name = info[0] || '';
					var phone = info[1] || '';
					var idx = index + 1;
					createTeacherRow(idx, name, phone, idx > 1);
				});
				if (hidTeachers) hidTeachers.value = teacherPairs.length;
			}
		}

		function createTeacherRow(id, name, phone, canRemove) {
			var porow = document.createElement('div');
			porow.className = 'row mb-2 teachersTextBox';
			porow.id = 'teacherRow_' + id;
			
			var col1 = document.createElement('div');
			col1.className = 'col-md-6 mb-2';
			col1.innerHTML = '<label class="form-label fw-bold text-muted small text-uppercase">Team Manager ' + id + '</label>' +
				'<input type="text" name="txtTeacher_' + id + '" id="txtTeacher_' + id + '" class="form-control" placeholder="Enter teacher name" value="' + (name || '').replace(/"/g, '&quot;') + '" onkeyup="this.value=this.value.toUpperCase();">';
				
			var col2 = document.createElement('div');
			col2.className = 'col-md-6 mb-2';
			var removeBtnHtml = canRemove ? '<button type="button" class="btn btn-outline-danger" onclick="this.closest(\'.teachersTextBox\').remove();" title="Remove">✕</button>' : '';
			col2.innerHTML = '<label class="form-label fw-bold text-muted small text-uppercase">Phone</label>' +
				'<div class="input-group">' +
					'<input type="text" name="txtPhone_' + id + '" id="txtPhone_' + id + '" class="form-control" placeholder="Phone number" maxlength="11" value="' + (phone || '').replace(/"/g, '&quot;') + '" onkeypress="return numbersonly(this, event, false);">' +
					removeBtnHtml +
				'</div>';
				
			porow.appendChild(col1);
			porow.appendChild(col2);
			
			var addBtnDiv = document.getElementById('teachersAddBtnDiv');
			var container = document.getElementById('teachersRow');
			if (addBtnDiv && container) {
				container.insertBefore(porow, addBtnDiv);
			} else if (container) {
				container.appendChild(porow);
			}
		}

		function handleSchoolSearchInput(val) {
			if (!val) return;
			var code = extractSchoolCode(val);
			if (code && code !== '0' && /^\d+$/.test(code)) {
				// Automatically fetch and populate form inputs!
				fetchSchoolDataAjax(code);
				
				// If user selected full suggestion containing dash
				if (val.indexOf(' - ') > -1) {
					var current = '<?php echo esc($active_code); ?>';
					if (code !== current) {
						loadSchoolByCode(code);
					}
				}
			}
		}

		function loadSchoolFromSearch() {
			var input = document.getElementById('txtSearchSchool');
			var val = input ? input.value.trim() : '';
			if (!val) {
				swal('Missing Information', 'Please enter a School Code or search a School Name.', 'warning');
				return;
			}
			var code = extractSchoolCode(val);
			if (!code || code === '0' || !/^\d+$/.test(code)) {
				swal('Invalid School', 'Could not resolve a matching School Code. Please enter a valid numeric School Code.', 'warning');
				return;
			}
			loadSchoolByCode(code);
		}

		function loadSchoolByCode(code) {
			if (!code) {
				var input = document.getElementById('txtSearchSchool');
				if (input) code = extractSchoolCode(input.value.trim());
			}
			if (!code || code === '0' || !/^\d+$/.test(code)) {
				swal('Missing Information', 'Please enter a valid School Code.', 'warning');
				return;
			}
			var btn = document.getElementById('btnLoadSchool');
			if (btn) {
				btn.disabled = true;
				btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Loading...';
			}
			window.location.href = '<?php echo base_url("schools/school_entry"); ?>?school_code=' + encodeURIComponent(code);
		}

		function saveSchoolDetailsWithSpinner() {
			var txtStandardFrom = document.getElementById('txtStandardFrom');
			var txtStandardTo = document.getElementById('txtStandardTo');
			var formSchool = document.getElementById('formSchool');
			var hidSchoolId = document.getElementById('formSchoolHidSchoolId');

			if (!hidSchoolId || !hidSchoolId.value.trim() || hidSchoolId.value === '0') {
				var searchInput = document.getElementById('txtSearchSchool');
				var code = searchInput ? extractSchoolCode(searchInput.value.trim()) : '';
				if (code && code !== '0' && /^\d+$/.test(code)) {
					if (hidSchoolId) hidSchoolId.value = code;
				} else {
					swal("Missing Information", "Please search or enter a valid School Code first.", "warning");
					return false;
				}
			}

			if (typeof fncCheckSchoolDeatils === 'function' && !fncCheckSchoolDeatils()) {
				return false;
			}

			var btn = document.getElementById('btnSaveSchoolDetails');
			if (btn) {
				btn.disabled = true;
				btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving School Details...';
			}

			if (formSchool) {
				formSchool.submit();
			}
			return true;
		}

		// Function to handle school code input fetch (when school details are loaded via AJAX)
		function fetch_school_details() {
			var code = document.getElementById('txtSchoolCode') ? document.getElementById('txtSchoolCode').value : '';
			if (code) {
				loadSchoolByCode(code);
			}
		}

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

		initializePage();

			// Additional forced visibility check using the unified function
			setTimeout(function () {
				updateVisibility();

				var addParticipantBtn = document.getElementById('addParticipantBtn');
				var schoolDetailsSection = document.getElementById('schoolDetailsSection');


				return 'Force reinitialization completed - check console logs';
			});

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

				// Modal events and cleanup
				document.addEventListener('DOMContentLoaded', function () {
					var participantModal = document.getElementById('participantModal');

					if (participantModal) {
						// Handle modal hidden event to ensure backdrop is properly removed
						participantModal.addEventListener('hidden.bs.modal', function () {
							forceRemoveModalBackdrop();
						});

						// Handle modal shown event to load items and refresh photos
						participantModal.addEventListener('shown.bs.modal', function () {
							if (typeof refreshModalPhoto === 'function') {
								refreshModalPhoto();
							}
							if (typeof window.loadItemsByClass === 'function') {
								window.loadItemsByClass();
							}
						});
					}

					// Bind class and gender change events cleanly using standard event delegation
					document.addEventListener('change', function (e) {
						if (e.target && (e.target.id === 'txtClass' || e.target.id === 'txtGender')) {
							if (typeof window.loadItemsByClass === 'function') {
								window.loadItemsByClass();
							}
						}
					});
				});

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
				jQuery.ajax({
					url: '<?php echo base_url(); ?>schools/registration/get_school_list',
					type: 'GET',
					dataType: 'json',
					success: function (response) {
						var schoolSelect = jQuery('#duplicateSchoolSelect');
						schoolSelect.empty();
						schoolSelect.append('<option value="">-- Select a School --</option>');

						if (response.schools && response.schools.length > 0) {
							jQuery.each(response.schools, function (index, school) {
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
				var schoolCode = jQuery('#duplicateSchoolSelect').val();
				if (!schoolCode) {
					jQuery('#duplicatePreview').hide();
					jQuery('#duplicateBtn').hide();
					return;
				}

				// Fetch school data for preview
				jQuery.ajax({
					url: '<?php echo base_url(); ?>schools/registration/get_school_data',
					type: 'POST',
					data: { school_code: schoolCode },
					dataType: 'json',
					success: function (response) {
						if (response.school) {
							var school = response.school;

							// Populate preview table
							jQuery('#previewSchoolCode').text(school.school_code || '');
							jQuery('#previewSchoolName').text(school.school_name || '');
							jQuery('#previewEmail').text(school.school_email || '');

							var schoolType = '';
							if (school.school_type == 'G') schoolType = 'Government';
							else if (school.school_type == 'A') schoolType = 'Aided';
							else if (school.school_type == 'U') schoolType = 'Unaided';
							jQuery('#previewSchoolType').text(schoolType);

							jQuery('#previewStandardFrom').text(school.class_start || '');
							jQuery('#previewStandardTo').text(school.class_end || '');
							jQuery('#previewPrincipal').text(school.principal_name || '');
							jQuery('#previewHeadmaster').text(school.hm_name || '');

							jQuery('#duplicatePreview').show();
							jQuery('#duplicateBtn').show();
						}
					},
					error: function () {
						alert('Error loading school data');
					}
				});
			}

			// Function to handle item code input changes (used for manual entry validation)
			function handleItemCodeChange(input) {
				var inputElement = jQuery(input);
				if (inputElement.length === 0) {
					console.error('Input element not found');
					return;
				}

				var itemCode = inputElement.val().trim();
				var inputId = inputElement.attr('id');

				if (!inputId) {
					console.error('Input ID not found');
					return;
				}

				var fieldNumber = inputId.replace('txtItemCode_', '');

				if (itemCode.length === 3 || itemCode.length === 4) {
					// 1. Check if the entered code is already selected in the school
					var enteredCodeInt = parseInt(itemCode);
					var enteredCodeStr = itemCode;
					if (window.currentlySelectedItems && 
						(window.currentlySelectedItems.includes(enteredCodeInt) || 
						 window.currentlySelectedItems.includes(enteredCodeStr))) {
						
						swal({
							title: "Oops!",
							text: "This item has already been assigned. Please try another one.",
							icon: "error",
							button: "OK",
						}).then(function() {
							inputElement.val('');
							var detailDiv = jQuery('#itemDetails_' + fieldNumber);
							if (detailDiv.length > 0) {
								detailDiv.hide();
							}
							setTimeout(function() {
								inputElement.focus();
							}, 50);
						});
						return;
					}

					// 2. Check if this item code is duplicate across the current participant's inputs (optional but good practice)
					var isDuplicateInForm = false;
					jQuery('.item-code-input').not(inputElement).each(function () {
						if (jQuery(this).val().trim() === itemCode) {
							isDuplicateInForm = true;
						}
					});

					if (isDuplicateInForm) {
						swal({
							title: "Oops!",
							text: "You have already selected this item for this participant.",
							icon: "warning",
							button: "OK",
						}).then(function() {
							inputElement.val('');
							var detailDiv = jQuery('#itemDetails_' + fieldNumber);
							if (detailDiv.length > 0) {
								detailDiv.hide();
							}
							setTimeout(function() {
								inputElement.focus();
							}, 50);
						});
						return;
					}

					// 3. Fetch item details from the server to check validity
					jQuery.ajax({
						url: '<?php echo base_url(); ?>schools/registration/get_item_details',
						type: 'POST',
						data: { item_code: itemCode },
						dataType: 'json',
						success: function (response) {
							var detailDiv = jQuery('#itemDetails_' + fieldNumber);
							if (detailDiv.length > 0) {
								if (response.item) {
									// Also verify category range check using festival_master data from the response!
									var participantClass = parseInt(jQuery('#txtClass').val());
									var classStart = parseInt(response.item.class_start);
									var classEnd = parseInt(response.item.class_end);
									
									if (participantClass < classStart || participantClass > classEnd) {
										swal({
											title: "Oops!",
											text: "This item is not available for class " + participantClass + " (available for classes " + classStart + "-" + classEnd + ").",
											icon: "error",
											button: "OK",
										}).then(function() {
											inputElement.val('');
											detailDiv.hide();
											setTimeout(function() {
												inputElement.focus();
											}, 50);
										});
										return;
									}

									detailDiv.find('#itemName_' + fieldNumber).text(response.item.item_name + ' (' + response.item.item_type + ')');
									detailDiv.show();
								} else {
									detailDiv.hide();
									swal({
										title: "Oops!",
										text: "Invalid item code: " + itemCode,
										icon: "error",
										button: "OK",
									}).then(function() {
										inputElement.val('');
										setTimeout(function() {
											inputElement.focus();
										}, 50);
									});
								}
							}
						},
						error: function (xhr, status, error) {
							console.error('Error validating item code:', error);
							var detailDiv = jQuery('#itemDetails_' + fieldNumber);
							if (detailDiv.length > 0) {
								detailDiv.hide();
							}
						}
					});
				} else {
					var detailDiv = jQuery('#itemDetails_' + fieldNumber);
					if (detailDiv.length > 0) {
						detailDiv.hide();
					}
				}
			}

			// Function to clear item selection
			window.clearItemSelection = function(inputId) {
				var input = document.getElementById(inputId);
				if (!input) return;

				input.value = '';
				input.disabled = false;
				input.style.backgroundColor = '';

				var fieldNumber = inputId.replace('txtItemCode_', '');
				var detailDiv = document.getElementById('itemDetails_' + fieldNumber);
				if (detailDiv) detailDiv.style.display = 'none';
				var iconSpan = document.getElementById('itemTypeIcon_' + fieldNumber);
				if (iconSpan) { iconSpan.style.display = 'none'; iconSpan.innerHTML = ''; }
				var nameSpan = document.getElementById('itemName_' + fieldNumber);
				if (nameSpan) nameSpan.innerText = '';

				if (typeof window.loadItemsByClass === 'function') {
					window.loadItemsByClass();
				}
			};

			// Function to clear all item selections
			window.clearAllItemSelections = function() {
				for (var i = 1; i <= 5; i++) {
					window.clearItemSelection('txtItemCode_' + i);
				}
				for (var p = 1; p <= 3; p++) {
					var pInput = document.getElementById('txtPinnanyCode_' + p);
					if (pInput) pInput.value = '';
					var pDetail = document.getElementById('pinnanyDetails_' + p);
					if (pDetail) pDetail.style.display = 'none';
					var pIcon = document.getElementById('pinnanyTypeIcon_' + p);
					if (pIcon) { pIcon.style.display = 'none'; pIcon.innerHTML = ''; }
					var pName = document.getElementById('pinnanyName_' + p);
					if (pName) pName.innerText = '';
				}
				if (typeof window.loadItemsByClass === 'function') {
					window.loadItemsByClass();
				}
			};

			// Enhanced clearForm function
			window.clearForm = function() {
				var txtADNO = document.getElementById('txtADNO');
				var txtParticipantName = document.getElementById('txtParticipantName');
				var txtClass = document.getElementById('txtClass');
				var txtGender = document.getElementById('txtGender');
				var userfile = document.getElementById('userfile');
				var hidADNO = document.getElementById('hidADNO');

				if (txtADNO) txtADNO.value = '';
				if (txtParticipantName) txtParticipantName.value = '';
				if (txtClass) txtClass.value = '';
				if (txtGender) txtGender.value = '';
				if (userfile) userfile.value = '';
				if (hidADNO) hidADNO.value = '';

				// Reset photo preview
				if (typeof removeCurrentPhoto === 'function') {
					removeCurrentPhoto();
				}

				// Clear all item selections
				if (typeof window.clearAllItemSelections === 'function') {
					window.clearAllItemSelections();
				}

				// Reset modal title and button for add
				var modalLabel = document.getElementById('participantModalLabel');
				if (modalLabel) modalLabel.innerText = 'Add Participant';
				var saveBtn = document.getElementById('save_participant_btn');
				if (saveBtn) {
					saveBtn.innerText = 'Save Participant';
					saveBtn.disabled = false;
					saveBtn.setAttribute('onclick', 'fncSaveParticipant()');
				}

				// Clear available items
				var availableItemsContainer = document.getElementById('availableItemsContainer');
				if (availableItemsContainer) {
					availableItemsContainer.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-info-circle fs-4 text-secondary mb-2 d-block"></i><p style="font-size: 0.85rem;" class="mb-0">Please select a class to view available items.</p></div>';
				}
			};

			// Initialize item code input handlers when document is ready
			jQuery(document).ready(function () {
				// Add change handlers to item code inputs
				jQuery('.item-code-input').each(function () {
					if (jQuery(this).length > 0) {
						jQuery(this).on('input', function () {
							handleItemCodeChange(this);
						});
					}
				});

				// Add clear buttons to item inputs
				jQuery('.item-code-input').each(function () {
					if (jQuery(this).length > 0) {
						var inputId = jQuery(this).attr('id');
						if (inputId) {
							jQuery(this).after('<button type="button" class="btn btn-outline-danger btn-sm" onclick="clearItemSelection(\'' + inputId + '\')" title="Clear selection"><i class="fas fa-times"></i></button>');
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
			// Enhanced editParticipant function with button spinner
			window.editParticipant = function (admnNo, btn) {
				if (!admnNo) {
					console.error('Admission number is missing in editParticipant');
					return;
				}
				var originalHtml = '';
				if (btn) {
					originalHtml = btn.innerHTML;
					btn.disabled = true;
					btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" style="width: 14px; height: 14px;"></span>';
				}
				var restoreBtn = function() {
					if (btn) {
						btn.disabled = false;
						btn.innerHTML = originalHtml || '<i class="bi bi-pencil-fill fs-7"></i>';
					}
				};

				var hidADNO = document.getElementById('hidADNO');
				if (hidADNO) hidADNO.value = admnNo;

				var schCode = (document.getElementById('hidSchoolId') ? document.getElementById('hidSchoolId').value : '') ||
				              (document.getElementById('formSchoolHidSchoolId') ? document.getElementById('formSchoolHidSchoolId').value : '') ||
				              '<?php echo esc($active_code ?? ''); ?>';

				var requestUrl = '<?php echo base_url('schools/registration/get_participant_data'); ?>';
				var formData = new FormData();
				formData.append('hidSchoolId', schCode);
				formData.append('hidADNO', admnNo);

				fetch(requestUrl, {
					method: 'POST',
					body: formData
				})
				.then(function (res) {
					if (!res.ok) {
						throw new Error('HTTP ' + res.status);
					}
					return res.json();
				})
				.then(function (response) {
					restoreBtn();
					if (response.error) {
						if (typeof Swal !== 'undefined') {
							Swal.fire({ icon: 'error', title: 'Error', text: response.error });
						} else if (typeof swal !== 'undefined') {
							swal("Oops!", response.error, "error");
						} else {
							alert(response.error);
						}
						return;
					}

					var txtADNO = document.getElementById('txtADNO');
					var txtParticipantName = document.getElementById('txtParticipantName');
					var txtClass = document.getElementById('txtClass');
					var txtGender = document.getElementById('txtGender');

					if (txtADNO) txtADNO.value = response.admn_no || '';
					if (txtParticipantName) txtParticipantName.value = response.participant_name || '';
					if (txtClass) txtClass.value = response.class || '';
					if (txtGender) txtGender.value = response.gender || '';

					if (response.photo_url && response.photo_url !== 'false' && response.photo_url !== 'null') {
						if (typeof loadModalPhoto === 'function') {
							loadModalPhoto(response.photo_url);
						}
					} else {
						if (typeof removeCurrentPhoto === 'function') {
							removeCurrentPhoto();
						}
					}

					// Clear item inputs first
					for (var c = 1; c <= 5; c++) {
						var inpC = document.getElementById('txtItemCode_' + c);
						if (inpC) inpC.value = '';
						var dC = document.getElementById('itemDetails_' + c);
						if (dC) dC.style.display = 'none';
						var iC = document.getElementById('itemTypeIcon_' + c);
						if (iC) { iC.style.display = 'none'; iC.innerHTML = ''; }
						var nC = document.getElementById('itemName_' + c);
						if (nC) nC.innerText = '';
					}
					for (var p = 1; p <= 3; p++) {
						var inP = document.getElementById('txtPinnanyCode_' + p);
						if (inP) inP.value = '';
						var dP = document.getElementById('pinnanyDetails_' + p);
						if (dP) dP.style.display = 'none';
						var iP = document.getElementById('pinnanyTypeIcon_' + p);
						if (iP) { iP.style.display = 'none'; iP.innerHTML = ''; }
						var nP = document.getElementById('pinnanyName_' + p);
						if (nP) nP.innerText = '';
					}

					// Populate item codes from response
					if (response.items && Array.isArray(response.items)) {
						var regularIdx = 1;
						var pinnanyIdx = 1;
						for (var i = 0; i < response.items.length; i++) {
							var item = response.items[i];
							if (item.item_type === 'P' || item.is_pinnany === 'Y') {
								if (pinnanyIdx <= 3) {
									var pInput = document.getElementById('txtPinnanyCode_' + pinnanyIdx);
									if (pInput && item && item.item_code) {
										pInput.value = item.item_code;
										if (typeof window.updatePinnanyCodeIcon === 'function') {
											window.updatePinnanyCodeIcon(pinnanyIdx, item.item_type || 'P', item.item_name || '');
										}
									}
									pinnanyIdx++;
								}
							} else {
								if (regularIdx <= 5) {
									var inputElement = document.getElementById('txtItemCode_' + regularIdx);
									if (inputElement && item && item.item_code) {
										inputElement.value = item.item_code;
										if (typeof window.updateItemCodeIcon === 'function') {
											window.updateItemCodeIcon(regularIdx, item.item_type || 'S', item.item_name || '');
										}
									}
									regularIdx++;
								}
							}
						}
					}

					// Re-render available items so current assigned items are excluded
					if (typeof window.loadItemsByClass === 'function') {
						window.loadItemsByClass();
					}

					// Set modal title and button for edit
					var modalLabel = document.getElementById('participantModalLabel');
					if (modalLabel) modalLabel.innerText = 'Edit Participant';
					var saveBtn = document.getElementById('save_participant_btn');
					if (saveBtn) {
						saveBtn.innerText = 'Update Participant';
						saveBtn.disabled = false;
						saveBtn.setAttribute('onclick', 'fncUpdateParticipant(\'' + admnNo + '\')');
					}

					var modalEl = document.getElementById('participantModal');
					if (modalEl) {
						if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
							var participantModal = bootstrap.Modal.getOrCreateInstance(modalEl);
							participantModal.show();
						} else if (typeof window.jQuery !== 'undefined' && typeof window.jQuery(modalEl).modal === 'function') {
							window.jQuery(modalEl).modal('show');
						} else {
							modalEl.style.display = 'block';
							modalEl.classList.add('show');
							document.body.classList.add('modal-open');
						}
					} else {
						console.error('Participant modal element not found!');
					}
				})
				.catch(function (err) {
					restoreBtn();
					console.error('Error loading participant data:', err);
					if (typeof Swal !== 'undefined') {
						Swal.fire({ icon: 'error', title: 'Error', text: 'Error loading participant data. Please try again.' });
					} else {
						alert('Error loading participant data');
					}
				});
			};

			// Enhanced deleteParticipant function with button & dialog spinner
			window.deleteParticipant = function (admnNo, btn) {
				var hidSchoolId = jQuery('#hidSchoolId').val() || '<?php echo esc($active_code); ?>';
				if (typeof Swal !== 'undefined') {
					Swal.fire({
						title: 'Delete Participant?',
						text: 'Are you sure you want to delete this participant (AD No: ' + admnNo + ')?',
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#dc3545',
						cancelButtonColor: '#6c757d',
						confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> Yes, Delete',
						cancelButtonText: 'Cancel'
					}).then((result) => {
						if (result.isConfirmed) {
							if (btn) {
								btn.disabled = true;
								btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" style="width: 14px; height: 14px;"></span>';
							}
							Swal.fire({
								title: 'Deleting...',
								text: 'Deleting participant. Please wait...',
								allowOutsideClick: false,
								didOpen: () => { Swal.showLoading(); }
							});
							var hidADNO = document.getElementById('hidADNO');
							if (hidADNO) hidADNO.value = admnNo;
							var formParticipant = document.getElementById('formParticipant');
							if (formParticipant) {
								formParticipant.action = '<?php echo base_url(); ?>schools/registration/delete_participant_details';
								formParticipant.submit();
							}
						}
					});
				} else {
					if (confirm('Are you sure you want to delete participant ' + admnNo + '?')) {
						if (btn) {
							btn.disabled = true;
							btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" style="width: 14px; height: 14px;"></span>';
						}
						var hidADNO = document.getElementById('hidADNO');
						if (hidADNO) hidADNO.value = admnNo;
						var formParticipant = document.getElementById('formParticipant');
						if (formParticipant) {
							formParticipant.action = '<?php echo base_url(); ?>schools/registration/delete_participant_details';
							formParticipant.submit();
						}
					}
				}
			};


	</script>
<?php } ?>


<?php
$show_participants_initial = !empty($active_code) || empty($initial_show_school_details);
?>

<div id="registeredParticipantsWrapper" style="display:<?php echo $show_participants_initial ? 'block' : 'none'; ?>;">
	<div class="row justify-content-center">
		<div class="col-12">
			<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="border: 1px solid #e2e8f0 !important;">
				<div class="card-header bg-white py-3 px-4 border-0 d-flex flex-wrap justify-content-between align-items-center gap-2" style="background: linear-gradient(135deg, #f8fafc, #ffffff) !important; border-bottom: 1px solid #edf2f7 !important;">
					<div class="d-flex align-items-center gap-2">
						<i class="bi bi-person-lines-fill text-primary fs-5"></i>
						<h5 class="fw-bold mb-0 text-dark">Registered Participants</h5>
					</div>
					<div class="d-flex align-items-center gap-2">
						<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-semibold fs-7">
							Total: <?php echo !empty($participant_details) ? count($participant_details) : 0; ?> Students
						</span>
					</div>
				</div>

				<?php if (!empty($participant_details) && count($participant_details) > 0): ?>
					<!-- Top Search & Filter Bar -->
					<div class="p-3 bg-light bg-opacity-50 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
						<div class="d-flex align-items-center gap-2">
							<label class="text-muted small fw-semibold mb-0" for="participantsPageSize">Show</label>
							<select class="form-select form-select-sm rounded-3 shadow-2xs" id="participantsPageSize" style="width: 85px;">
								<option value="5">5</option>
								<option value="10" selected>10</option>
								<option value="25">25</option>
								<option value="50">50</option>
								<option value="all">All</option>
							</select>
							<span class="text-muted small">entries per page</span>
						</div>
						<div class="d-flex align-items-center gap-2">
							<div class="input-group input-group-sm shadow-2xs" style="min-width: 260px;">
								<span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
								<input type="text" class="form-control border-start-0 border-end-0 ps-0" id="participantsSearch" placeholder="Search student name, AD No, items...">
								<button class="btn btn-white bg-white border border-start-0 text-muted" type="button" onclick="document.getElementById('participantsSearch').value=''; document.getElementById('participantsSearch').dispatchEvent(new Event('input'));" title="Clear"><i class="bi bi-x-circle"></i></button>
							</div>
						</div>
					</div>

					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-hover align-middle mb-0 custom-participant-table" id="participantsTable">
								<thead>
									<tr class="text-uppercase text-secondary fw-semibold border-bottom" style="font-size: 12px; letter-spacing: 0.5px; background-color: #f8fafc;">
										<th class="py-3 px-3 text-center sortable" data-sort="number" style="width: 50px; cursor: pointer;">
											<span>#</span> <span class="sort-icon text-muted">⇅</span>
										</th>
										<th class="py-3 px-3 text-center sortable" data-sort="number" style="width: 100px; cursor: pointer;">
											<span>AD No.</span> <span class="sort-icon text-muted">⇅</span>
										</th>
										<th class="py-3 px-3 text-center" style="width: 70px;">Photo</th>
										<th class="py-3 px-3 text-center sortable" data-sort="number" style="width: 100px; cursor: pointer;">
											<span>Reg No.</span> <span class="sort-icon text-muted">⇅</span>
										</th>
										<th class="py-3 px-3 sortable" data-sort="string" style="cursor: pointer;">
											<span>Student Name</span> <span class="sort-icon text-muted">⇅</span>
										</th>
										<th class="py-3 px-3 text-center sortable" data-sort="class" style="width: 90px; cursor: pointer;">
											<span>Class</span> <span class="sort-icon text-muted">⇅</span>
										</th>
										<th class="py-3 px-3 text-center sortable" data-sort="string" style="width: 95px; cursor: pointer;">
											<span>Gender</span> <span class="sort-icon text-muted">⇅</span>
										</th>
										<th class="py-3 px-3 sortable" data-sort="string" style="cursor: pointer;">
											<span>Registered Items</span> <span class="sort-icon text-muted">⇅</span>
										</th>
										<?php if (isset($is_edit) and $is_edit != 'no') { ?>
											<th class="py-3 px-3 text-center" style="width: 100px;">Actions</th>
										<?php } ?>
									</tr>
								</thead>
								<tbody>
									<?php
									$db = \Config\Database::connect();
									$count = 1;
									for ($j = 0; $j < count($participant_details); $j++) {
										$target_sch = !empty($school_details[0]['school_code']) ? $school_details[0]['school_code'] : $active_code;
										$items = $db->table('sd_participant_item_details PD')
											->select('PD.*, IM.item_name')
											->join('item_master IM', 'IM.item_code = PD.item_code')
											->where('PD.school_code', $target_sch)
											->where('PD.participant_id', $participant_details[$j]['participant_id'])
											->where('PD.admn_no', $participant_details[$j]['admn_no'])
											->get()->getResultArray();
										$photo_url = $participant_photos[$participant_details[$j]['admn_no']] ?? null;
										$photo_display_url = $photo_url ? $photo_url : base_url('photos/img_user_nophoto.png');
									?>
										<tr class="align-middle">
											<td class="text-center text-muted fw-semibold py-3"><?php echo $count; ?></td>
											<td class="text-center fw-bold text-dark font-monospace"><?php echo $participant_details[$j]['admn_no'] ?></td>
											<td class="text-center py-2">
												<div class="d-flex justify-content-center">
													<img src="<?php echo $photo_display_url; ?>" 
														class="rounded-circle shadow-sm participant-photo participant-avatar-img"
														style="width: 46px; height: 46px; object-fit: cover; border: 2px solid #ffffff; box-shadow: 0 3px 8px rgba(0,0,0,0.12);"
														alt="<?php echo $photo_url ? 'Photo' : 'No photo'; ?>"
														onerror="this.src='<?php echo base_url('photos/img_user_nophoto.png'); ?>'"
														data-admn-no="<?php echo $participant_details[$j]['admn_no']; ?>"
														data-school-code="<?php echo $school_details[0]['school_code'] ?? $active_code; ?>"
														loading="lazy" />
												</div>
											</td>
											<td class="text-center fw-semibold text-secondary font-monospace"><?php echo $participant_details[$j]['participant_id'] ?></td>
											<td class="py-3">
												<div class="fw-bold text-dark fs-6"><?php echo esc($participant_details[$j]['participant_name']); ?></div>
											</td>
											<td class="text-center">
												<span class="badge bg-secondary-subtle text-secondary-emphasis rounded-3 px-2 py-1 fw-bold fs-7">
													Std <?php echo $participant_details[$j]['class']; ?>
												</span>
											</td>
											<td class="text-center">
												<?php if ($participant_details[$j]['gender'] == 'B'): ?>
													<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1">
														<i class="bi bi-gender-male"></i> Boy
													</span>
												<?php else: ?>
													<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1">
														<i class="bi bi-gender-female"></i> Girl
													</span>
												<?php endif; ?>
											</td>
											<td>
												<div class="d-flex flex-wrap gap-1">
													<?php
													$has_any_item = false;
													for ($k = 0; $k < count($items); $k++) {
														if (!empty($items[$k]['item_code'])) {
															$has_any_item = true;
															$type = $items[$k]['item_type'] ?? '';
															$code = $items[$k]['item_code'];
															$name = esc($items[$k]['item_name'] ?? '');
															if ($type == 'G') {
																echo '<span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1 shadow-2xs" title="' . $name . '"><i class="bi bi-people-fill text-primary"></i> ' . $code . ' (G)</span>';
															} elseif ($type == 'P') {
																echo '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1 shadow-2xs" title="' . $name . '"><i class="bi bi-music-note-beamed text-warning"></i> ' . $code . ' (P)</span>';
															} else {
																echo '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1 shadow-2xs" title="' . $name . '"><i class="bi bi-person-fill text-success"></i> ' . $code . ' (S)</span>';
															}
														}
													}
													if (!$has_any_item) {
														echo '<span class="text-muted small fst-italic">No items</span>';
													}
													?>
												</div>
											</td>
											<?php if (isset($is_edit) and $is_edit != 'no') { ?>
												<td class="text-center py-3">
													<div class="d-flex justify-content-center gap-2">
														<button type="button"
															onClick="javascript:editParticipant('<?php echo $participant_details[$j]['admn_no'] ?>', this)"
															class="btn btn-sm btn-outline-primary rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center p-0"
															style="width: 34px; height: 34px;" title="Edit Participant">
															<i class="bi bi-pencil-fill fs-7"></i>
														</button>
														<button type="button"
															onClick="javascript:deleteParticipant('<?php echo $participant_details[$j]['admn_no'] ?>', this)"
															class="btn btn-sm btn-outline-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center p-0"
															style="width: 34px; height: 34px;" title="Delete Participant">
															<i class="bi bi-trash3-fill fs-7"></i>
														</button>
													</div>
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
					</div>

					<!-- Bottom Pagination Bar -->
					<div class="card-footer bg-white py-3 px-4 border-0 d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-top: 1px solid #edf2f7 !important;">
						<div class="text-muted small" id="participantsInfo">
							Showing 1 to <?php echo min(5, count($participant_details)); ?> of <?php echo count($participant_details); ?> entries
						</div>
						<nav aria-label="Participant Table Pagination">
							<ul class="pagination pagination-sm mb-0 gap-1" id="participantsPagination">
								<!-- Dynamic Page Numbers -->
							</ul>
						</nav>
					</div>
				<?php else: ?>
					<div class="card-body p-0">
						<div class="text-center py-5">
							<div class="empty-state-container py-4">
								<div class="empty-state-icon mb-3 bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 72px; height: 72px;">
									<i class="bi bi-people fs-2"></i>
								</div>
								<h5 class="fw-bold text-dark mb-1">No Participants Registered Yet</h5>
								<p class="text-muted mb-3 small" style="max-width: 360px; margin: 0 auto;">Start registering your students by clicking the Add Participant button below.</p>
								<button type="button" class="btn btn-success rounded-pill px-4 py-2 fw-semibold shadow-sm" data-bs-toggle="modal"
									data-bs-target="#participantModal" onclick="clearForm()">
									<i class="bi bi-plus-circle-fill me-1"></i> Add Participant
								</button>
							</div>
						</div>
					</div>
				<?php endif; ?>
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
		<div style="display:<?php echo (count(@$group_details['group_array']) > 0 && @$school_show == 'show') ? 'block' : 'none'; ?>;" class="mb-4">
			<div class="row justify-content-center">
				<div class="col-12">
					<div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border: 1px solid #e2e8f0 !important;">
						<div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f8fafc, #ffffff) !important; border-bottom: 1px solid #edf2f7 !important;">
							<div class="d-flex align-items-center gap-2">
								<i class="bi bi-award-fill text-warning fs-5"></i>
								<h5 class="fw-bold mb-0 text-dark">Captains List</h5>
							</div>
							<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1 fw-semibold">
								Total: <?php echo count($group_details['group_array'] ?? []); ?> Groups
							</span>
						</div>

						<!-- Top Filter & Search Bar for Captains -->
						<div class="p-3 bg-light bg-opacity-50 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
							<div class="d-flex align-items-center gap-2">
								<label class="text-muted small fw-semibold mb-0" for="captainsPageSize">Show</label>
								<select class="form-select form-select-sm rounded-3 shadow-2xs" id="captainsPageSize" style="width: 85px;">
									<option value="5" selected>5</option>
									<option value="10">10</option>
									<option value="25">25</option>
									<option value="all">All</option>
								</select>
								<span class="text-muted small">entries per page</span>
							</div>
							<div class="d-flex align-items-center gap-2">
								<div class="input-group input-group-sm shadow-2xs" style="min-width: 260px;">
									<span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
									<input type="text" class="form-control border-start-0 border-end-0 ps-0" id="captainsSearch" placeholder="Search item code, name, captain...">
									<button class="btn btn-white bg-white border border-start-0 text-muted" type="button" onclick="document.getElementById('captainsSearch').value=''; document.getElementById('captainsSearch').dispatchEvent(new Event('input'));" title="Clear"><i class="bi bi-x-circle"></i></button>
								</div>
							</div>
						</div>

						<div class="card-body p-0">
							<div class="table-responsive">
								<table class="table table-hover align-middle mb-0" id="captainsTable">
									<thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
										<tr class="text-uppercase text-secondary fw-semibold border-bottom" style="font-size: 12px; letter-spacing: 0.5px;">
											<th class="py-3 px-3 text-center sortable" data-sort="number" style="width: 60px; cursor: pointer;">
												<span>#</span> <span class="sort-icon text-muted">⇅</span>
											</th>
											<th class="py-3 px-3 sortable" data-sort="string" style="width: 45%; cursor: pointer;">
												<span>Item Code & Name</span> <span class="sort-icon text-muted">⇅</span>
											</th>
											<th class="py-3 px-3 sortable" data-sort="select" style="cursor: pointer;">
												<span>Assigned Captain</span> <span class="sort-icon text-muted">⇅</span>
											</th>
										</tr>
									</thead>
									<tbody>
									<?php
									$count = 1;
									$group_array = $group_details['group_array'] ?? [];
									$group_leader = $group_details['group_leader'] ?? [];
									$group_participant_array = $group_details['group_participant_array'] ?? [];
									foreach ($group_array as $groups) {
										?>
										<tr>
											<td class="text-center text-muted fw-semibold py-3"><?php echo $count; ?></td>
											<td class="py-3 px-3 fw-bold text-dark">
												<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 me-2"><?php echo $groups['item_code']; ?></span>
												<?php echo esc($groups['item_name']); ?>
												<input type="hidden" name="hidGrpItemCode_<?php echo $count ?>" id="hidGrpItemCode_<?php echo $count ?>" value="<?php echo $groups['item_code'] ?>">
											</td>
											<td class="py-3 px-3">
												<?php
												if (isset($is_edit) and $is_edit != 'no') {
													print (form_dropdown(
														'cmbGrpParticipant_' . $count,
														$group_participant_array[$groups['item_code']] ?? [],
														@$group_leader[$groups['item_code']],
														'id="cmbGrpParticipant_' . $count . '" class="form-select form-select-sm rounded-3 shadow-sm" onChange="javascript:$(\'divCaptainSubmit\').style.display = \'block\'"'
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
									</tbody>
								</table>
								<input type="hidden" name="hidGrpCount" id="hidGrpCount" value="<?php echo $count ?>">
								<div id="divCaptainSubmit" style="display:none;" class="p-3 text-center bg-light border-top">
									<?php
									if (isset($is_edit) and $is_edit != 'no') {
										echo '<button type="submit" name="group_submit" id="btnSaveCaptains" class="btn btn-primary px-4 rounded-pill shadow-sm fw-semibold" onclick="this.disabled=true; this.innerHTML=\'<span class=\\\'spinner-border spinner-border-sm me-2\\\' role=\\\'status\\\'></span>Saving Captains...\'; this.form.submit();"><i class="bi bi-check2-circle me-1"></i> Save Captains</button>';
									}
									?>
								</div>
							</div>
						</div>

						<!-- Bottom Pagination Bar for Captains -->
						<div class="card-footer bg-white py-3 px-4 border-0 d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-top: 1px solid #edf2f7 !important;">
							<div class="text-muted small" id="captainsInfo">
								Showing 1 to <?php echo min(5, count($group_details['group_array'] ?? [])); ?> of <?php echo count($group_details['group_array'] ?? []); ?> entries
							</div>
							<nav aria-label="Captains Table Pagination">
								<ul class="pagination pagination-sm mb-0 gap-1" id="captainsPagination">
									<!-- Dynamic Page Numbers -->
								</ul>
							</nav>
						</div>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="hidSchoolId" id="hidSchoolId" value="<?php echo (@$school_details[0]['school_code']) ?
			@$school_details[0]['school_code'] : (service('request')->getPost('hidSchoolId') ?: ''); ?>">
		<?php
		echo form_close();
	}
}
?>

<?php
if (!empty(@$participant_item_list)) {
	if (count(@$participant_item_list) > 0) { ?>
		<div style="display:<?php echo (@$school_show == 'show' && !empty($participant_details) && count($participant_details) > 0) ? 'block' : 'none'; ?>;" class="mb-4">
			<div class="row justify-content-center">
				<div class="col-12">
					<div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border: 1px solid #e2e8f0 !important;">
						<div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f8fafc, #ffffff) !important; border-bottom: 1px solid #edf2f7 !important;">
							<div class="d-flex align-items-center gap-2">
								<i class="bi bi-grid-3x3-gap-fill text-primary fs-5"></i>
								<h5 class="fw-bold mb-0 text-dark">Item wise Participants Summary</h5>
							</div>
							<span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-3 py-1 fw-semibold">
								Total: <?php echo count($participant_item_list); ?> Items
							</span>
						</div>

						<!-- Top Filter & Search Bar for Item-wise Summary -->
						<div class="p-3 bg-light bg-opacity-50 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
							<div class="d-flex align-items-center gap-2">
								<label class="text-muted small fw-semibold mb-0" for="itemsSummaryPageSize">Show</label>
								<select class="form-select form-select-sm rounded-3 shadow-2xs" id="itemsSummaryPageSize" style="width: 85px;">
									<option value="5">5</option>
									<option value="10" selected>10</option>
									<option value="25">25</option>
									<option value="50">50</option>
									<option value="all">All</option>
								</select>
								<span class="text-muted small">entries per page</span>
							</div>
							<div class="d-flex align-items-center gap-2">
								<div class="input-group input-group-sm shadow-2xs" style="min-width: 260px;">
									<span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
									<input type="text" class="form-control border-start-0 border-end-0 ps-0" id="itemsSummarySearch" placeholder="Search item code, name, captain...">
									<button class="btn btn-white bg-white border border-start-0 text-muted" type="button" onclick="document.getElementById('itemsSummarySearch').value=''; document.getElementById('itemsSummarySearch').dispatchEvent(new Event('input'));" title="Clear"><i class="bi bi-x-circle"></i></button>
								</div>
							</div>
						</div>

						<div class="card-body p-0">
							<div class="table-responsive">
								<table class="table table-hover align-middle mb-0" id="itemsSummaryTable">
									<thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
										<tr class="text-uppercase text-secondary fw-semibold border-bottom" style="font-size: 12px; letter-spacing: 0.5px;">
											<th class="py-3 px-3 text-center sortable" data-sort="number" style="width: 50px; cursor: pointer;">
												<span>#</span> <span class="sort-icon text-muted">⇅</span>
											</th>
											<th class="py-3 px-3 text-center sortable" data-sort="number" style="width: 100px; cursor: pointer;">
												<span>Item Code</span> <span class="sort-icon text-muted">⇅</span>
											</th>
											<th class="py-3 px-3 sortable" data-sort="string" style="cursor: pointer;">
												<span>Item Name</span> <span class="sort-icon text-muted">⇅</span>
											</th>
											<th class="py-3 px-3 text-center sortable" data-sort="number" style="width: 120px; cursor: pointer;">
												<span>Participants</span> <span class="sort-icon text-muted">⇅</span>
											</th>
											<th class="py-3 px-3 text-center sortable" data-sort="number" style="width: 110px; cursor: pointer;">
												<span>Pinnany</span> <span class="sort-icon text-muted">⇅</span>
											</th>
											<th class="py-3 px-3 sortable" data-sort="string" style="cursor: pointer;">
												<span>Captain Name</span> <span class="sort-icon text-muted">⇅</span>
											</th>
										</tr>
									</thead>
									<tbody>
									<?php
									$count = 0;
									foreach ($participant_item_list as $item_list) {
										$count++;
										?>
										<tr>
											<td class="text-center text-muted fw-semibold py-3"><?php echo $count; ?></td>
											<td class="text-center"><span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 fw-bold"><?php echo $item_list['item_code'] ?></span></td>
											<td class="fw-bold text-dark py-3"><?php echo esc($item_list['item_name']) ?></td>
											<td class="text-center">
												<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">
													<?php echo $item_list['cnt_participant'] ?>
												</span>
											</td>
											<td class="text-center">
												<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1 fw-bold">
													<?php echo $item_list['cnt_pinnany'] ?>
												</span>
											</td>
											<td class="fw-semibold text-secondary"><?php echo !empty($item_list['captian']) ? esc($item_list['captian']) : '<span class="text-muted small fst-italic">-</span>'; ?></td>
										</tr>
										<?php
									}
									?>
									</tbody>
								</table>
							</div>
						</div>

						<!-- Bottom Pagination Bar for Item-wise Summary -->
						<div class="card-footer bg-white py-3 px-4 border-0 d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-top: 1px solid #edf2f7 !important;">
							<div class="text-muted small" id="itemsSummaryInfo">
								Showing 1 to <?php echo min(5, count($participant_item_list)); ?> of <?php echo count($participant_item_list); ?> entries
							</div>
							<nav aria-label="Items Summary Table Pagination">
								<ul class="pagination pagination-sm mb-0 gap-1" id="itemsSummaryPagination">
									<!-- Dynamic Page Numbers -->
								</ul>
							</nav>
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
	<div style="display:<?php echo (@$school_show == 'show' && !empty($participant_details)) ? 'block' : 'none'; ?>;" class="mb-4" id="schoolConfirmationWrapper">
		<div class="row justify-content-center">
			<div class="col-12">
				<div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border: 1px solid #e2e8f0 !important;">
					<div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f8fafc, #ffffff) !important; border-bottom: 1px solid #edf2f7 !important;">
						<div class="d-flex align-items-center gap-2">
							<i class="bi bi-shield-check text-success fs-4"></i>
							<div>
								<h5 class="fw-bold mb-0 text-dark">Verification, Report & Final Confirmation</h5>
								<small class="text-muted">Generate official PDF report and permanently finalize school registrations</small>
							</div>
						</div>
					</div>
					<div class="card-body p-4" id="schoolConfirmationArea">
						<?php if (@$school_details[0]['is_finalize'] == 'Y'): ?>
							<!-- ALREADY FINALIZED STATE -->
							<div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center gap-3 mb-3 p-3">
								<i class="bi bi-lock-fill fs-2 text-success"></i>
								<div>
									<h6 class="fw-bold mb-1 text-success">Registration Finalized & Permanently Locked</h6>
									<p class="mb-0 small text-muted">All participant details for this school have been officially verified and locked. No further modifications, additions, or deletions can be made.</p>
								</div>
							</div>
							<div class="d-flex flex-wrap gap-2 align-items-center">
								<button type="button" class="btn btn-danger px-4 py-2 rounded-pill shadow-sm fw-semibold text-white d-inline-flex align-items-center gap-2" onclick="openPdfReport()" id="pdfReportBtn">
									<i class="bi bi-file-earmark-pdf-fill"></i> Download PDF Report
								</button>
							</div>
						<?php else: ?>
							<!-- ACTIVE CONFIRMATION FLOW -->
							<?php $hasReport = (@$school_details[0]['is_create_report'] == 'Y'); ?>
							
							<div class="mb-3 d-flex flex-wrap gap-2 align-items-center justify-content-between bg-light p-3 rounded-3 border">
								<div>
									<span class="fw-semibold text-dark d-block">Step 1: Download & Verify Official PDF Report</span>
									<small class="text-muted">Take the PDF report, verify with the Headmaster/Principal, and ensure all participant and item entries are accurate.</small>
								</div>
								<button type="button" class="btn btn-danger px-4 py-2 rounded-pill shadow-sm fw-semibold text-white d-inline-flex align-items-center gap-2" onclick="openPdfReport()" id="pdfReportBtn">
									<i class="bi bi-file-earmark-pdf-fill fs-6"></i> PDF Report
								</button>
							</div>

							<div id="reportTakeNotice" style="display: <?php echo $hasReport ? 'none' : 'block'; ?>;" class="alert alert-info border-0 rounded-3 small mb-3">
								<i class="bi bi-info-circle-fill me-2"></i> Please click the <strong>PDF Report</strong> button above to download your registration report. Once the PDF report is generated, the final confirmation option will unlock.
							</div>

							<div id="confirmControlsArea" style="display: <?php echo $hasReport ? 'block' : 'none'; ?>;">
								<div class="alert alert-warning border-0 shadow-sm rounded-3 d-flex align-items-start gap-3 mb-3 p-3">
									<i class="bi bi-exclamation-triangle-fill fs-4 text-warning flex-shrink-0 mt-1"></i>
									<div>
										<strong class="text-dark">Crucial Warning:</strong>
										<div class="text-dark small mt-1">
											Once confirmed, the registration details for this school will be <strong>permanently locked</strong> in the database. No further edits, additions, or cancellations will be permitted.
										</div>
									</div>
								</div>

								<!-- Modern Declaration Checkbox -->
								<div class="card bg-light border-0 rounded-3 p-3 mb-3" style="border: 1.5px solid #cbd5e1 !important; background-color: #f8fafc !important;">
									<div class="form-check d-flex align-items-start gap-2 m-0">
										<input class="form-check-input mt-1 flex-shrink-0" type="checkbox" id="chkFinalConfirm" onchange="toggleConfirmButton(this)" style="cursor: pointer; width: 1.25rem; height: 1.25rem;">
										<label class="form-check-label text-dark fw-medium" for="chkFinalConfirm" style="cursor: pointer; font-size: 0.93rem; line-height: 1.5;">
											I solemnly declare and confirm that all participant details, admission numbers, class entries, and item codes registered for our school have been thoroughly verified and found to be 100% accurate. I have completed all registrations and understand that finalizing will <strong>permanently lock</strong> all data.
										</label>
									</div>
								</div>

								<!-- Finalize Confirm Button -->
								<div class="d-flex flex-wrap gap-2 align-items-center">
									<button type="button" class="btn btn-success px-4 py-2 rounded-pill shadow-sm fw-semibold d-inline-flex align-items-center gap-2" id="btnFinalConfirm" onclick="confirm_school_data()" disabled>
										<i class="bi bi-shield-lock-fill fs-6"></i> Confirm & Finalize Registration
									</button>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="hidSchoolId" id="hidSchoolId"
			value="<?php echo (@$school_details[0]['school_code']) ? @$school_details[0]['school_code'] : (service('request')->getPost('hidSchoolId') ?: ''); ?>">
	</div>
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
				var codeVal = (schoolCode && schoolCode.value) ? schoolCode.value : '<?php echo esc($active_code); ?>';
				if (!codeVal) {
					if (typeof Swal !== 'undefined') {
						Swal.fire({ icon: 'warning', title: 'Select School', text: 'Please select a school first' });
					} else {
						alert('Please select a school first');
					}
					return;
				}

				var btn = document.getElementById('pdfReportBtn');
				if (btn) {
					var origContent = btn.innerHTML;
					btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Generating PDF...';
					setTimeout(function() {
						if (btn) btn.innerHTML = origContent;
					}, 2500);
				}

				window.open('<?php echo base_url('schools/registration_report/pdf_report'); ?>?school_id=' + encodeURIComponent(codeVal), '_blank');

				// Immediately unlock the confirmation section in UI
				var notice = document.getElementById('reportTakeNotice');
				if (notice) notice.style.display = 'none';
				var controls = document.getElementById('confirmControlsArea');
				if (controls) controls.style.display = 'block';
			};

			window.toggleConfirmButton = function(checkbox) {
				var btn = document.getElementById('btnFinalConfirm');
				if (btn) {
					btn.disabled = !checkbox.checked;
				}
			};

			window.confirm_school_data = function () {
				var chk = document.getElementById('chkFinalConfirm');
				if (chk && !chk.checked) {
					if (typeof Swal !== 'undefined') {
						Swal.fire({
							icon: 'warning',
							title: 'Declaration Required',
							text: 'Please check the declaration checkbox to confirm you have verified all registration details before finalizing.',
							confirmButtonColor: '#0d6efd'
						});
					} else {
						alert('Please check the declaration checkbox to confirm you have verified all registration details.');
					}
					return false;
				}

				var schoolCode = document.getElementById('hidSchoolId') ? document.getElementById('hidSchoolId').value : '<?php echo esc($active_code); ?>';

				if (typeof Swal !== 'undefined') {
					// Step 1 SweetAlert - Warning & Confirmation of Completion
					Swal.fire({
						title: 'Confirm Final Registration?',
						html: 'Are you sure you have completed all participant registrations?<br><br><div class="alert alert-warning text-start small mb-0"><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> <strong>Important Notice:</strong> Once confirmed, your school registration data will be <b>permanently locked</b>. You will no longer be able to add, edit, or delete participants.</div>',
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#0d6efd',
						cancelButtonColor: '#6c757d',
						confirmButtonText: '<i class="bi bi-arrow-right-circle me-1"></i> Yes, Proceed to Lock',
						cancelButtonText: 'No, Let me review again',
						reverseButtons: true
					}).then((result1) => {
						if (result1.isConfirmed) {
							// Step 2 SweetAlert - Final Safety Verification
							Swal.fire({
								title: 'Final Safety Verification!',
								html: 'This action <b>CANNOT BE UNDONE</b>.<br><br>Do you solemnly certify that all registration entries are 100% complete and wish to <b>permanently lock and finalize</b> registration for School <b>' + schoolCode + '</b>?',
								icon: 'error',
								showCancelButton: true,
								confirmButtonColor: '#198754',
								cancelButtonColor: '#dc3545',
								confirmButtonText: '<i class="bi bi-shield-lock-fill me-1"></i> Yes, Permanently Finalize & Lock',
								cancelButtonText: 'Cancel & Go Back',
								reverseButtons: true
							}).then((result2) => {
								if (result2.isConfirmed) {
									Swal.fire({
										title: 'Finalizing Registration...',
										html: 'Locking registration details for School ' + schoolCode + '. Please wait...',
										allowOutsideClick: false,
										allowEscapeKey: false,
										didOpen: () => {
											Swal.showLoading();
										}
									});
									document.getElementById('formFinalize').submit();
								}
							});
						}
					});
				} else {
					if (confirm('Step 1 of 2: Are you sure you have completed all participant registrations? Once confirmed, registration details will be permanently locked.')) {
						if (confirm('Step 2 of 2: FINAL WARNING: This action CANNOT be undone. Do you really want to permanently finalize and lock registration for School ' + schoolCode + '?')) {
							document.getElementById('formFinalize').submit();
						}
					}
				}
			};

			// Universal Client-side Enhanced DataTable Engine: Sort, Filter, and Pagination
			window.initTablePagination = function(options) {
				var tableId = options.tableId;
				var pageSizeSelectId = options.pageSizeSelectId;
				var searchInputId = options.searchInputId;
				var paginationUlId = options.paginationUlId;
				var infoSpanId = options.infoSpanId;
				var initialPageSize = options.defaultPageSize || 10;

				var table = document.getElementById(tableId);
				if (!table) return null;

				var tbody = table.querySelector('tbody');
				if (!tbody) return null;

				var allRows = Array.from(tbody.querySelectorAll('tr'));
				if (allRows.length === 0) return null;

				var currentPage = 1;
				var currentSize = initialPageSize;
				var currentFilter = '';
				var currentSortCol = -1;
				var currentSortDir = 'asc';

				// Extract value from a table cell according to data-sort type
				function getCellValue(cell, sortType) {
					if (!cell) return '';
					if (sortType === 'select') {
						var sel = cell.querySelector('select');
						if (sel && sel.selectedIndex >= 0) {
							return sel.options[sel.selectedIndex].text.trim().toLowerCase();
						}
						return (cell.innerText || cell.textContent || '').trim().toLowerCase();
					}
					if (sortType === 'number') {
						var num = parseFloat((cell.innerText || cell.textContent || '').replace(/[^0-9.-]/g, ''));
						return isNaN(num) ? 0 : num;
					}
					if (sortType === 'class') {
						var std = parseInt((cell.innerText || cell.textContent || '').replace(/[^0-9]/g, ''), 10);
						return isNaN(std) ? 0 : std;
					}
					return (cell.innerText || cell.textContent || '').trim().toLowerCase();
				}

				function getFilteredRows() {
					if (!currentFilter) return allRows.slice();
					var q = currentFilter.toLowerCase().trim();
					return allRows.filter(function(row) {
						var text = (row.innerText || row.textContent || '').toLowerCase();
						var selects = row.querySelectorAll('select');
						for (var s = 0; s < selects.length; s++) {
							if (selects[s].selectedIndex >= 0) {
								text += ' ' + selects[s].options[selects[s].selectedIndex].text.toLowerCase();
							}
						}
						return text.indexOf(q) > -1;
					});
				}

				function sortRows(rowsList) {
					if (currentSortCol < 0) return rowsList;
					var headerTh = table.querySelectorAll('thead th')[currentSortCol];
					var sortType = headerTh ? (headerTh.getAttribute('data-sort') || 'string') : 'string';

					return rowsList.sort(function(a, b) {
						var cellA = a.cells[currentSortCol];
						var cellB = b.cells[currentSortCol];
						var valA = getCellValue(cellA, sortType);
						var valB = getCellValue(cellB, sortType);

						if (valA < valB) return currentSortDir === 'asc' ? -1 : 1;
						if (valA > valB) return currentSortDir === 'asc' ? 1 : -1;
						return 0;
					});
				}

				function render() {
					var filtered = getFilteredRows();
					filtered = sortRows(filtered);
					var totalFiltered = filtered.length;
					var limit = (currentSize === 'all') ? totalFiltered : parseInt(currentSize, 10);
					var totalPages = (limit >= totalFiltered || limit === 0) ? 1 : Math.ceil(totalFiltered / limit);

					if (currentPage > totalPages) currentPage = totalPages;
					if (currentPage < 1) currentPage = 1;

					var startIdx = (currentPage - 1) * limit;
					var endIdx = (currentSize === 'all') ? totalFiltered : startIdx + limit;

					// Hide all rows
					allRows.forEach(function(row) {
						row.style.display = 'none';
					});

					// Show and re-number current page slice
					for (var i = startIdx; i < Math.min(endIdx, totalFiltered); i++) {
						var row = filtered[i];
						row.style.display = '';
						var firstTh = table.querySelector('thead th');
						if (firstTh && firstTh.innerText.trim().indexOf('#') > -1) {
							row.cells[0].innerText = (i + 1);
						}
						tbody.appendChild(row);
					}

					// Update info text
					var infoEl = document.getElementById(infoSpanId);
					if (infoEl) {
						if (totalFiltered === 0) {
							infoEl.innerHTML = '<span class="text-muted">No matching records found</span>';
						} else {
							var actualStart = startIdx + 1;
							var actualEnd = Math.min(endIdx, totalFiltered);
							var text = 'Showing <span class="fw-bold text-dark">' + actualStart + '</span> to <span class="fw-bold text-dark">' + actualEnd + '</span> of <span class="fw-bold text-dark">' + totalFiltered + '</span> entries';
							if (totalFiltered < allRows.length) {
								text += ' <span class="text-muted small fst-italic">(filtered from ' + allRows.length + ' total)</span>';
							}
							infoEl.innerHTML = text;
						}
					}

					// Render Pagination controls
					var paginationUl = document.getElementById(paginationUlId);
					if (paginationUl) {
						paginationUl.innerHTML = '';

						// Prev button
						var prevLi = document.createElement('li');
						prevLi.className = 'page-item ' + (currentPage === 1 ? 'disabled' : '');
						var prevLink = document.createElement('a');
						prevLink.className = 'page-link rounded-3 px-3 shadow-2xs';
						prevLink.href = 'javascript:void(0)';
						prevLink.setAttribute('aria-label', 'Previous');
						prevLink.innerHTML = '<i class="bi bi-chevron-left"></i>';
						if (currentPage > 1) {
							prevLink.addEventListener('click', function(e) {
								e.preventDefault();
								currentPage--;
								render();
							});
						}
						prevLi.appendChild(prevLink);
						paginationUl.appendChild(prevLi);

						// Page numbers
						var maxVisible = 5;
						var startPage = Math.max(1, currentPage - 2);
						var endPage = Math.min(totalPages, startPage + maxVisible - 1);
						if (endPage - startPage < maxVisible - 1) {
							startPage = Math.max(1, endPage - maxVisible + 1);
						}

						if (startPage > 1) {
							var firstLi = document.createElement('li');
							firstLi.className = 'page-item';
							var firstLink = document.createElement('a');
							firstLink.className = 'page-link rounded-3 px-3 shadow-2xs';
							firstLink.href = 'javascript:void(0)';
							firstLink.innerText = '1';
							firstLink.addEventListener('click', function(e) {
								e.preventDefault();
								currentPage = 1;
								render();
							});
							firstLi.appendChild(firstLink);
							paginationUl.appendChild(firstLi);

							if (startPage > 2) {
								var dotsLi = document.createElement('li');
								dotsLi.className = 'page-item disabled';
								dotsLi.innerHTML = '<span class="page-link border-0">...</span>';
								paginationUl.appendChild(dotsLi);
							}
						}

						for (var p = startPage; p <= endPage; p++) {
							(function(pageNumber) {
								var pageLi = document.createElement('li');
								pageLi.className = 'page-item ' + (pageNumber === currentPage ? 'active' : '');
								var pageLink = document.createElement('a');
								pageLink.className = 'page-link rounded-3 px-3 shadow-2xs ' + (pageNumber === currentPage ? 'fw-bold text-white' : '');
								pageLink.href = 'javascript:void(0)';
								pageLink.innerText = pageNumber;
								pageLink.addEventListener('click', function(e) {
									e.preventDefault();
									currentPage = pageNumber;
									render();
								});
								pageLi.appendChild(pageLink);
								paginationUl.appendChild(pageLi);
							})(p);
						}

						if (endPage < totalPages) {
							if (endPage < totalPages - 1) {
								var dotsLi2 = document.createElement('li');
								dotsLi2.className = 'page-item disabled';
								dotsLi2.innerHTML = '<span class="page-link border-0">...</span>';
								paginationUl.appendChild(dotsLi2);
							}
							var lastLi = document.createElement('li');
							lastLi.className = 'page-item';
							var lastLink = document.createElement('a');
							lastLink.className = 'page-link rounded-3 px-3 shadow-2xs';
							lastLink.href = 'javascript:void(0)';
							lastLink.innerText = totalPages;
							lastLink.addEventListener('click', function(e) {
								e.preventDefault();
								currentPage = totalPages;
								render();
							});
							lastLi.appendChild(lastLink);
							paginationUl.appendChild(lastLi);
						}

						// Next button
						var nextLi = document.createElement('li');
						nextLi.className = 'page-item ' + (currentPage === totalPages || totalPages === 0 ? 'disabled' : '');
						var nextLink = document.createElement('a');
						nextLink.className = 'page-link rounded-3 px-3 shadow-2xs';
						nextLink.href = 'javascript:void(0)';
						nextLink.setAttribute('aria-label', 'Next');
						nextLink.innerHTML = '<i class="bi bi-chevron-right"></i>';
						if (currentPage < totalPages) {
							nextLink.addEventListener('click', function(e) {
								e.preventDefault();
								currentPage++;
								render();
							});
						}
						nextLi.appendChild(nextLink);
						paginationUl.appendChild(nextLi);
					}
				}

				// Bind sortable headers
				var headers = table.querySelectorAll('thead th');
				headers.forEach(function(th, colIdx) {
					if (th.classList.contains('sortable')) {
						th.addEventListener('click', function() {
							if (currentSortCol === colIdx) {
								currentSortDir = (currentSortDir === 'asc') ? 'desc' : 'asc';
							} else {
								currentSortCol = colIdx;
								currentSortDir = 'asc';
							}

							// Update all header icons in this table
							headers.forEach(function(h, idx) {
								var icon = h.querySelector('.sort-icon');
								if (icon) {
									if (idx === colIdx) {
										icon.innerHTML = (currentSortDir === 'asc') ? '▲' : '▼';
										icon.className = 'sort-icon text-primary fw-bold';
									} else {
										icon.innerHTML = '⇅';
										icon.className = 'sort-icon text-muted';
									}
								}
							});

							render();
						});
					}
				});

				// Bind search input
				var searchEl = document.getElementById(searchInputId);
				if (searchEl) {
					searchEl.addEventListener('input', function() {
						currentFilter = this.value;
						currentPage = 1;
						render();
					});
				}

				// Bind page size select
				var sizeEl = document.getElementById(pageSizeSelectId);
				if (sizeEl) {
					sizeEl.addEventListener('change', function() {
						currentSize = this.value;
						currentPage = 1;
						render();
					});
				}

				// Run initial render
				render();

				return {
					refresh: function() {
						allRows = Array.from(tbody.querySelectorAll('tr'));
						render();
					}
				};
			};

			// Automatically initialize paginators when document is ready
			(function setupAllPaginators() {
				function initAll() {
					window.participantsPager = window.initTablePagination({
						tableId: 'participantsTable',
						pageSizeSelectId: 'participantsPageSize',
						searchInputId: 'participantsSearch',
						paginationUlId: 'participantsPagination',
						infoSpanId: 'participantsInfo',
						defaultPageSize: 10
					});

					window.captainsPager = window.initTablePagination({
						tableId: 'captainsTable',
						pageSizeSelectId: 'captainsPageSize',
						searchInputId: 'captainsSearch',
						paginationUlId: 'captainsPagination',
						infoSpanId: 'captainsInfo',
						defaultPageSize: 10
					});

					window.itemsSummaryPager = window.initTablePagination({
						tableId: 'itemsSummaryTable',
						pageSizeSelectId: 'itemsSummaryPageSize',
						searchInputId: 'itemsSummarySearch',
						paginationUlId: 'itemsSummaryPagination',
						infoSpanId: 'itemsSummaryInfo',
						defaultPageSize: 10
					});
				}

				if (document.readyState === 'loading') {
					document.addEventListener('DOMContentLoaded', initAll);
				} else {
					initAll();
				}
			})();
	</script>
