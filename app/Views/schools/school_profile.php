<div id="divEntryForm">
	<div class="pagetitle mb-4">
		<h1><i class="bi bi-building-gear me-2 text-primary"></i>School Profile</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
				<li class="breadcrumb-item active">School Profile</li>
			</ol>
		</nav>
	</div>

	<?php
	echo form_open('schools/profile/update', array('id' => 'formSchool')); ?>

	<input type="hidden" name="hidTeachers" id="hidTeachers" value="1">

	<div class="row justify-content-center" id="schoolDetailsSection">
		<div class="col-12">
			<div class="card shadow-sm border-0 mb-4">
				<div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between">
					<h5 class="card-title mb-0 fw-bold text-primary">
						<i class="bi bi-info-circle-fill me-2"></i>School Profile Details
					</h5>
					<div class="d-flex gap-2 align-items-center mt-2 mt-sm-0">
						<?php if (!empty($school_details[0]['school_code'])): ?>
							<span class="badge bg-primary px-3 py-2 fs-6">
								<i class="bi bi-hash me-1"></i>Code: <?php echo esc($school_details[0]['school_code']); ?>
							</span>
						<?php endif; ?>
						<?php if (!empty($school_details[0]['school_type'])): ?>
							<span class="badge bg-secondary px-3 py-2 fs-6">
								<?php
								if (@$school_details[0]['school_type'] == 'G') echo 'Government';
								else if (@$school_details[0]['school_type'] == 'A') echo 'Aided';
								else if (@$school_details[0]['school_type'] == 'U') echo 'Unaided';
								else echo esc(@$school_details[0]['school_type']);
								?>
							</span>
						<?php endif; ?>
					</div>
				</div>
				<div class="card-body p-4">
					
					<!-- Section 1: Basic Information -->
					<div class="row mb-4">
						<div class="col-md-6 mb-3">
							<label class="form-label fw-bold text-muted small text-uppercase">School Code</label>
							<div id="divSchoolCode">
								<?php
								$session_code = trim((string)(session()->get('SCHOOL_CODE') ?? ''));
								$active_code = (!empty($school_details[0]['school_code'])) ? $school_details[0]['school_code'] : (($session_code !== '' && $session_code !== '0') ? $session_code : '');
								if (($session_code !== '' && $session_code !== '0') || @$school_show == 'edit' || (int)session()->get('USER_TYPE') == 5) {
									echo '<input type="text" name="txtSchoolCode" id="txtSchoolCode" class="form-control bg-light fw-bold" value="' . esc($active_code) . '" readonly>';
								} else {
									echo form_input("txtSchoolCode", (string) ($active_code ?? ''), 'class="form-control fw-bold" id="txtSchoolCode" placeholder="Enter school code" onkeypress="javascript:return numbersonly(this, event, false);" onBlur="javascript:fetch_school_details()"');
								} ?>
							</div>
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label fw-bold text-muted small text-uppercase">School Name</label>
							<input type="text" class="form-control bg-light fw-semibold" value="<?php echo esc(@$school_details[0]['school_name'] ?? ''); ?>" readonly>
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label fw-bold text-muted small text-uppercase">E-mail Address</label>
							<?php echo (@$school_show == 'show') ?
								'<input type="text" class="form-control bg-light" value="' . esc(@$school_details[0]['school_email'] ?? '') . '" readonly>' :
								form_input(
									"txtSchoolEmail",
									(string) (@$school_details[0]['school_email'] ?? ''),
									'class="form-control" id="txtSchoolEmail" placeholder="Enter school email address" maxlength="100"'
								); ?>
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label fw-bold text-muted small text-uppercase">School Type</label>
							<input type="text" class="form-control bg-light" value="<?php
								if (@$school_details[0]['school_type'] == 'G') echo 'Government';
								else if (@$school_details[0]['school_type'] == 'A') echo 'Aided';
								else if (@$school_details[0]['school_type'] == 'U') echo 'Unaided';
								else echo esc(@$school_details[0]['school_type'] ?? '');
							?>" readonly>
						</div>
					</div>

					<!-- Section 2: Standards / Classes Offered -->
					<div class="border-top pt-3 mb-4">
						<h6 class="fw-bold text-secondary mb-3"><i class="bi bi-mortarboard-fill me-2 text-primary"></i>Classes Offered (Standards)</h6>
						<div class="row">
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold text-muted small text-uppercase">Standard From <span class="text-danger">*</span></label>
								<?php echo (@$school_show == 'show') ?
									'<input type="text" class="form-control bg-light" value="' . esc(@$school_details[0]['class_start'] ?? '') . '" readonly>' :
									form_dropdown(
										"txtStandardFrom",
										array(0 => '-- Select Standard From --', 1 => 1, 5 => 5, 8 => 8, 11 => 11),
										@$school_details[0]['class_start'],
										'id="txtStandardFrom" class="form-select" onChange="javascript:fncShowHideStd()"'
									); ?>
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold text-muted small text-uppercase">Standard To <span class="text-danger">*</span></label>
								<?php echo (@$school_show == 'show') ?
									'<input type="text" class="form-control bg-light" value="' . esc(@$school_details[0]['class_end'] ?? '') . '" readonly>' :
									form_dropdown(
										"txtStandardTo",
										array(0 => '-- Select Standard To --', 4 => 4, 5 => 5, 7 => 7, 10 => 10, 12 => 12),
										@$school_details[0]['class_end'],
										'id="txtStandardTo" class="form-select" onChange="javascript:fncShowHideStd()"'
									); ?>
							</div>
						</div>
					</div>

					<!-- Section 3: School Leadership -->
					<div class="border-top pt-3 mb-4">
						<h6 class="fw-bold text-secondary mb-3"><i class="bi bi-people-fill me-2 text-primary"></i>School Administration</h6>
						<div class="row">
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold text-muted small text-uppercase">Principal Name</label>
								<?php echo (@$school_show == 'show') ?
									'<input type="text" class="form-control bg-light" value="' . esc(@$school_details[0]['principal_name'] ?? '') . '" readonly>' :
									form_input("txtPrincipal", (string) (@$school_details[0]['principal_name'] ?? ''), 'class="form-control" id="txtPrincipal" placeholder="Enter principal name" onkeyup="javascript:this.value=this.value.toUpperCase();"'); ?>
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold text-muted small text-uppercase">Principal Phone</label>
								<?php echo (@$school_show == 'show') ?
									'<input type="text" class="form-control bg-light" value="' . esc(@$school_details[0]['principal_phone'] ?? '') . '" readonly>' :
									form_input("txtPrincipalPhone", (string) (@$school_details[0]['principal_phone'] ?? ''), 'class="form-control" id="txtPrincipalPhone" placeholder="Enter phone number" maxlength="11" onkeypress="javascript:return numbersonly(this, event, false);"'); ?>
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold text-muted small text-uppercase">Headmaster Name</label>
								<?php echo (@$school_show == 'show') ?
									'<input type="text" class="form-control bg-light" value="' . esc(@$school_details[0]['hm_name'] ?? '') . '" readonly>' :
									form_input("txtHeadmaster", (string) (@$school_details[0]['hm_name'] ?? ''), 'class="form-control" id="txtHeadmaster" placeholder="Enter headmaster name" onkeyup="javascript:this.value=this.value.toUpperCase();"'); ?>
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label fw-bold text-muted small text-uppercase">Headmaster Phone</label>
								<?php echo (@$school_show == 'show') ?
									'<input type="text" class="form-control bg-light" value="' . esc(@$school_details[0]['hm_phone'] ?? '') . '" readonly>' :
									form_input("txtHeadmasterPhone", (string) (@$school_details[0]['hm_phone'] ?? ''), 'class="form-control" id="txtHeadmasterPhone" placeholder="Enter phone number" maxlength="11" onkeypress="javascript:return numbersonly(this, event, false);"'); ?>
							</div>
						</div>
					</div>

					<!-- Section 4: Team Managers -->
					<div class="border-top pt-3 mb-4">
						<h6 class="fw-bold text-secondary mb-3"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Team Managers (Escorting Teachers)</h6>
						<div id="teachersRow">
							<?php
							if (@$school_show == 'show') {
								if (@$school_details[0]['teachers'] != '') {
									$teachers = explode("#@#", $school_details[0]['teachers']);
									for ($i = 0; $i < count($teachers); $i++) {
										$teachers_details = explode('#$#', $teachers[$i]);
										if (count($teachers_details) > 1) {
											echo '<div class="row mb-2">
												<div class="col-md-6 mb-3">
													<label class="form-label fw-bold text-muted small text-uppercase">Team Manager ' . ($i + 1) . '</label>
													<input type="text" class="form-control bg-light fw-semibold" value="' . esc($teachers_details[0]) . '" readonly>
												</div>
												<div class="col-md-6 mb-3">
													<label class="form-label fw-bold text-muted small text-uppercase">Phone</label>
													<input type="text" class="form-control bg-light" value="' . esc($teachers_details[1]) . '" readonly>
												</div>
											</div>';
										}
									}
								}
							} else if (@$school_show == 'edit') {
								if (@$school_details[0]['teachers'] != '') {
									$teachers = explode("#@#", $school_details[0]['teachers']);
									for ($i = 1; $i < count($teachers); $i++) {
										$teachers_details = explode('#$#', $teachers[$i - 1]);
										?>
										<div class="teachersTextBox">
											<?php
											$name = 'txtTeacher_' . $i;
											$phone = 'txtPhone_' . $i;
											echo '<div class="row mb-2">
												<div class="col-md-6 mb-3">
													<label class="form-label fw-bold text-muted small text-uppercase">Team Manager ' . $i . '</label>' .
													form_input($name, (string) ($teachers_details[0] ?? ''), 'class="form-control" id="' . $name . '" placeholder="Enter teacher name" onkeyup="javascript:this.value=this.value.toUpperCase();"') .
												'</div>
												<div class="col-md-6 mb-3">
													<label class="form-label fw-bold text-muted small text-uppercase">Phone</label>';
											if ($i > 1) {
												echo '<div class="input-group">' .
													form_input($phone, (string) (@$teachers_details[1] ?? ''), 'class="form-control" id="' . $phone . '" placeholder="Phone number" onkeypress="javascript:return numbersonly(this, event, false);" maxlength="11"') .
													'<button class="btn btn-outline-danger" type="button" onclick="this.closest(\'.teachersTextBox\').remove();">✕</button></div>';
											} else {
												echo form_input($phone, (string) (@$teachers_details[1] ?? ''), 'class="form-control" id="' . $phone . '" placeholder="Phone number" onkeypress="javascript:return numbersonly(this, event, false);" maxlength="11"');
											}
											echo '</div></div>';
											?>
										</div>
										<?php
									}
									$teachersCount = (int) count($teachers) - 1;
									echo "<script>$('hidTeachers').value=" . $teachersCount . "</script>";
								} else {
									echo "<script>$('hidTeachers').value=0</script>";
								}
								?>
								<div class="mt-2" id="teachersAddBtnDiv">
									<button type="button" class="btn btn-outline-primary btn-sm" onclick="javascript:addteacher(); return false;">
										<i class="bi bi-plus-circle me-1"></i>Add Another Team Manager
									</button>
								</div>
								<?php
							} else {
								echo '<div class="row mb-2">
									<div class="col-md-6 mb-3">
										<label class="form-label fw-bold text-muted small text-uppercase">Team Manager 1</label>' .
										form_input("txtTeacher_1", '', 'class="form-control" id="txtTeacher_1" placeholder="Enter teacher name" onkeyup="javascript:this.value=this.value.toUpperCase();"') .
									'</div>
									<div class="col-md-6 mb-3">
										<label class="form-label fw-bold text-muted small text-uppercase">Phone</label>' .
										form_input("txtPhone_1", '', 'class="form-control" id="txtPhone_1" placeholder="Phone number" onkeypress="javascript:return numbersonly(this, event, false);" maxlength="11"') .
									'</div>
								</div>';
								?>
								<div class="mt-2" id="teachersAddBtnDiv">
									<button type="button" class="btn btn-outline-primary btn-sm" onclick="javascript:addteacher(); return false;">
										<i class="bi bi-plus-circle me-1"></i>Add Another Team Manager
									</button>
								</div>
								<?php
							}
							?>
						</div>
					</div>

					<!-- Section 5: Student Strength by Category -->
					<div class="border-top pt-3 mb-4">
						<h6 class="fw-bold text-secondary mb-3"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Student Strength by Category</h6>
						<?php
		$classStart = (int) (@$school_details[0]['class_start'] ?? 0);
		$classEnd = (int) (@$school_details[0]['class_end'] ?? 0);
		$LPDiv = ($classStart === 1) ? 'block' : 'none';
		$UPDiv = ($classEnd > 4 && $classStart <= 5 && $classStart > 0) ? 'block' : 'none';
		$HSDiv = ($classEnd > 7 && $classStart <= 8 && $classStart > 0) ? 'block' : 'none';
		$HSSDiv = ($classEnd > 10) ? 'block' : 'none';
		$VHSCDiv = ($classEnd > 10) ? 'block' : 'none';
						?>

						<div class="row g-3 align-items-end">
							<div class="col-6 col-sm-4 col-md-2" id="divLP" style="display:<?php echo $LPDiv ?>;">
								<label class="form-label fw-bold small text-muted">LP (1-4)</label>
								<?php echo (@$school_show == 'show') ?
									'<input type="text" class="form-control bg-light" value="' . esc(@$school_details[0]['strength_lp'] ?? '0') . '" readonly>' :
									form_input("txtTotalLP", (string) (@$school_details[0]['strength_lp'] ?? ''), 'class="form-control" id="txtTotalLP" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" onkeyup="javascript:addTotal()" placeholder="0"'); ?>
							</div>

							<div class="col-6 col-sm-4 col-md-2" id="divUP" style="display:<?php echo $UPDiv ?>;">
								<label class="form-label fw-bold small text-muted">UP (5-7)</label>
								<?php echo (@$school_show == 'show') ?
									'<input type="text" class="form-control bg-light" value="' . esc(@$school_details[0]['strength_up'] ?? '0') . '" readonly>' :
									form_input("txtTotalUP", (string) (@$school_details[0]['strength_up'] ?? ''), 'class="form-control" id="txtTotalUP" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" onkeyup="javascript:addTotal()" placeholder="0"'); ?>
							</div>

							<div class="col-6 col-sm-4 col-md-2" id="divHS" style="display:<?php echo $HSDiv ?>;">
								<label class="form-label fw-bold small text-muted">HS (8-10)</label>
								<?php echo (@$school_show == 'show') ?
									'<input type="text" class="form-control bg-light" value="' . esc(@$school_details[0]['strength_hs'] ?? '0') . '" readonly>' :
									form_input("txtTotalHS", (string) (@$school_details[0]['strength_hs'] ?? ''), 'class="form-control" id="txtTotalHS" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" onkeyup="javascript:addTotal()" placeholder="0"'); ?>
							</div>

							<div class="col-6 col-sm-4 col-md-2" id="divHSS" style="display:<?php echo $HSSDiv ?>;">
								<label class="form-label fw-bold small text-muted">HSS (11-12)</label>
								<?php echo (@$school_show == 'show') ?
									'<input type="text" class="form-control bg-light" value="' . esc(@$school_details[0]['strength_hss'] ?? '0') . '" readonly>' :
									form_input("txtTotalHSS", (string) (@$school_details[0]['strength_hss'] ?? ''), 'class="form-control" id="txtTotalHSS" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" onkeyup="javascript:addTotal()" placeholder="0"'); ?>
							</div>

							<div class="col-6 col-sm-4 col-md-2" id="divVHSC" style="display:<?php echo $VHSCDiv ?>;">
								<label class="form-label fw-bold small text-muted">VHSE</label>
								<?php echo (@$school_show == 'show') ?
									'<input type="text" class="form-control bg-light" value="' . esc(@$school_details[0]['strength_vhss'] ?? '0') . '" readonly>' :
									form_input("txtTotalVHSS", (string) (@$school_details[0]['strength_vhss'] ?? ''), 'class="form-control" id="txtTotalVHSS" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" onkeyup="javascript:addTotal()" placeholder="0"'); ?>
							</div>

							<div class="col-6 col-sm-4 col-md-2">
								<label class="form-label fw-bold text-primary small">Total Students</label>
								<?php echo (@$school_show == 'show') ?
									'<input type="text" class="form-control bg-light fw-bold text-primary" value="' . esc(@$school_details[0]['total_strength'] ?? '0') . '" readonly>' :
									form_input("txtTotal", (string) (@$school_details[0]['total_strength'] ?? ''), 'class="form-control bg-light fw-bold text-primary" id="txtTotal" maxlength="5" onkeypress="javascript:return numbersonly(this, event, false);" readonly="readonly" placeholder="0"'); ?>
							</div>

							<input type="hidden" name="hidHSSNo" id="hidHSSNo"
								value="<?php print ((@$school_details[0]['strength_hss']) ? @$school_details[0]['strength_hss'] : 0); ?>" />
							<input type="hidden" name="hidHSSNo" id="hidVHSSNo"
								value="<?php print ((@$school_details[0]['strength_vhss']) ? @$school_details[0]['strength_vhss'] : 0); ?>" />
						</div>
					</div>

					<!-- Section 6: Action Buttons -->
					<div class="d-flex justify-content-center gap-3 mt-4 pt-3 border-top">
						<?php
						if (isset($is_edit) and $is_edit != 'no') {
							echo (@$school_show == 'show') ?
								'<button type="button" class="btn btn-warning px-5 py-2 fw-bold" id="btnEditSchoolProfile" onclick="javascript:fncEditSchoolProfile(this)"><i class="bi bi-pencil-square me-2"></i>Edit School Profile</button>' :
								'<button type="button" class="btn btn-primary px-5 py-2 fw-bold shadow-sm" id="btnSaveSchoolProfile" onclick="javascript:fncSaveSchoolProfile(this)"><i class="bi bi-check-circle-fill me-2"></i>Save School Profile</button>';
						}
						?>
					</div>

				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="hidSchoolId" id="hidSchoolId" value="<?php echo (!empty($school_details[0]['school_code'])) ? $school_details[0]['school_code'] : (service('request')->getPost('hidSchoolId') ?: $active_code); ?>">
	<?php echo form_close(); ?>
</div>

<script>
function fncEditSchoolProfile(btn) {
	if (btn) {
		btn.disabled = true;
		btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading Edit...';
	}
	var form = document.getElementById('formSchool');
	if (form) {
		form.action = '<?php echo base_url(); ?>schools/profile/edit';
		form.submit();
	} else {
		window.location.href = '<?php echo base_url(); ?>schools/profile/edit';
	}
}

function fncSaveSchoolProfile(btn) {
	if (typeof fncCheckSchoolDeatils === 'function') {
		if (!fncCheckSchoolDeatils()) return false;
	}
	var saveBtn = btn || document.getElementById('btnSaveSchoolProfile');
	if (saveBtn) {
		saveBtn.disabled = true;
		saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
	}
	var form = document.getElementById('formSchool');
	if (form) {
		form.action = '<?php echo base_url(); ?>schools/profile/update';
		form.submit();
	}
}

<?php if (@$school_show != 'show'): ?>
if (typeof fncShowHideStd === 'function') {
	fncShowHideStd();
}
<?php endif; ?>
</script>
