<div class="modal fade" id="schoolMasterModal" tabindex="-1" aria-labelledby="schoolMasterModalLabel" aria-hidden="true"
	data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
			
			<!-- Modal Header -->
			<div class="modal-header text-white px-4 py-3" style="background: linear-gradient(135deg, #4338ca 0%, #3b82f6 50%, #06b6d4 100%);">
				<div class="d-flex align-items-center gap-2">
					<i class="bi bi-mortarboard-fill fs-4"></i>
					<h5 class="modal-title fw-bold m-0" id="schoolMasterModalLabel">Update School</h5>
				</div>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="forceRemoveModalBackdrop()"></button>
			</div>

			<!-- Modal Body -->
			<div class="modal-body p-4" style="background: #ffffff; overflow-y: auto !important; max-height: 80vh;">
				<?php
				if (@$selected_school[0]['school_name'] or $this->Session_Model->check_user_permission(26)) {
					$is_edit_mode = !empty(@$selected_school[0]['school_code']);
					echo form_open('schools/school_master/' . ($is_edit_mode ? 'update_school_detials' : 'add_school_details'), array('id' => 'formUser'));
				?>

					<div class="mb-4">
						<span class="badge bg-primary-subtle text-primary border rounded-pill px-3 py-1 fw-bold small">
							<i class="bi bi-info-circle me-1"></i> Update Institutional Record
						</span>
					</div>

					<div class="row g-3">
						
						<!-- School Code -->
						<?php if ($this->Session_Model->check_user_permission(26)) { ?>
							<div class="col-12 col-md-5">
								<label for="txtSchoolCode" class="form-label fw-bold text-dark small mb-1">
									<i class="bi bi-upc-scan text-primary me-1"></i> School Code
								</label>
								<div class="input-group">
									<span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-hash"></i></span>
									<input type="text" name="txtSchoolCode" id="txtSchoolCode" 
										   class="form-control border-start-0 ps-1" 
										   placeholder="Auto-generate if blank"
										   value="<?php echo esc((string) (@$selected_school[0]['school_code'] ?? '')); ?>"
										   onkeypress="javascript:return numbersonly(this, event, false);" />
								</div>
								<div class="form-text text-muted small mt-1">Leave empty to automatically assign code.</div>
							</div>
						<?php } ?>

						<!-- School Type -->
						<div class="col-12 <?php echo $this->Session_Model->check_user_permission(26) ? 'col-md-7' : 'col-md-12'; ?>">
							<label for="cmbSchoolType" class="form-label fw-bold text-dark small mb-1">
								<i class="bi bi-tag-fill text-primary me-1"></i> School Type / Management <span class="text-danger">*</span>
							</label>
							<div class="input-group">
								<span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-buildings"></i></span>
								<?php
								$value_array = array(0 => '-- Select School Type --', 'G' => 'Government (Govt)', 'A' => 'Aided', 'U' => 'Unaided / Private');
								echo form_dropdown("cmbSchoolType", $value_array, @$selected_school[0]['school_type'], 'id="cmbSchoolType" class="form-select border-start-0 ps-1"');
								?>
							</div>
						</div>

						<!-- School Name -->
						<div class="col-12">
							<label for="txtSchoolName" class="form-label fw-bold text-dark small mb-1">
								<i class="bi bi-mortarboard text-primary me-1"></i> Full School Name <span class="text-danger">*</span>
							</label>
							<div class="input-group">
								<span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-building"></i></span>
								<input type="text" name="txtSchoolName" id="txtSchoolName" 
									   class="form-control border-start-0 ps-1" 
									   placeholder="Enter official institution name"
									   value="<?php echo esc((string) (@$selected_school[0]['school_name'] ?? '')); ?>" required />
							</div>
						</div>

						<!-- Location & Administrative Section -->
						<?php if ($this->session->get('USER_GROUP') == 'W' || $this->Session_Model->check_user_permission(26)) { ?>
							<div class="col-12 mt-4">
								<div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
									<h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
										<i class="bi bi-geo-alt-fill text-primary"></i> Administrative Jurisdiction & Zone
									</h6>

									<div class="row g-3">
										
										<!-- Revenue District -->
										<?php if ($this->session->get('USER_GROUP') == 'W') { ?>
											<div class="col-12 col-md-4" id="divDistrict">
												<label for="cmbDistrict" class="form-label fw-semibold text-muted small mb-1">Revenue District</label>
												<?php echo form_dropdown("cmbDistrict", $districts, @$selected_school[0]['rev_district_code'], 'id="cmbDistrict" class="form-select" onChange="javascript:loadEduDistrict()"'); ?>
											</div>
										<?php } ?>

										<!-- Education District -->
										<?php if ($this->Session_Model->check_user_permission(26)) { ?>
											<div class="col-12 col-md-4" id="divEduDistrict" style="display:<?php echo ((isset($selected_school[0]['edu_district_code']) && @$selected_school[0]['edu_district_code'] != '0') || isset($edu_districts)) ? 'block' : 'none'; ?>">
												<label for="cmbEduDistrict" class="form-label fw-semibold text-muted small mb-1">Education District</label>
												<div id="divEdudistrictCombo">
													<?php echo form_dropdown("cmbEduDistrict", @$edu_districts, @$selected_school[0]['edu_district_code'], 'id="cmbEduDistrict" class="form-select" onChange="javascript:loadSubDistrict()"'); ?>
												</div>
											</div>

											<!-- Sub-District -->
											<div class="col-12 col-md-4" id="divSubdistrict" style="display:<?php echo (isset($selected_school[0]['sub_district_code']) && @$selected_school[0]['sub_district_code'] != '0') ? 'block' : 'none'; ?>">
												<label for="cmbSubDistrict" class="form-label fw-semibold text-muted small mb-1">Sub-District</label>
												<div id="divSubdistrictCombo">
													<?php echo form_dropdown("cmbSubDistrict", ($subdistricts ?? []), @$selected_school[0]['sub_district_code'], 'id="cmbSubDistrict" class="form-select" onChange="javascript:loadSchool()"'); ?>
												</div>
											</div>
										<?php } ?>

									</div>
								</div>
							</div>
						<?php } ?>

					</div>

					<input type="hidden" name="hidUserId" id="hidUserId" value="<?php echo esc((string) (@$selected_school[0]['school_code'] ?? '')); ?>" />

					<!-- Modal Footer Actions -->
					<div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
						<button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal" onclick="forceRemoveModalBackdrop()">
							<i class="bi bi-x-circle me-1"></i> Cancel
						</button>

						<?php if (!empty(@$selected_school[0]['school_code'])) { ?>
							<button type="button" id="btnUpdateSchool" class="btn btn-primary rounded-pill px-4 fw-semibold" 
									style="background: linear-gradient(135deg, #4338ca 0%, #3b82f6 100%); border: none;"
									onClick="javascript: return fncUpdateSchool('<?php echo esc(@$selected_school[0]['school_code']); ?>', '<?php echo esc($this->session->get('USER_TYPE')); ?>')">
								<i class="bi bi-check-circle-fill me-1"></i> Update School
							</button>
						<?php } else { ?>
							<button type="submit" id="btnAddSchool" class="btn btn-primary rounded-pill px-4 fw-semibold" 
									style="background: linear-gradient(135deg, #4338ca 0%, #3b82f6 100%); border: none;"
									onClick="javascript: return fncAddSchool('<?php echo esc($this->session->get('USER_TYPE')); ?>')">
								<i class="bi bi-check-circle-fill me-1"></i> Update School
							</button>
						<?php } ?>
					</div>

				<?php
					echo form_close();
				} else {
				?>
					<div class="text-center text-muted py-5">
						<i class="bi bi-shield-lock text-warning display-4 d-block mb-2"></i>
						<h6 class="fw-bold text-dark">Access Restricted</h6>
						<p class="small text-muted mb-0">You do not have administrative permission to add or modify school records.</p>
					</div>
				<?php } ?>
			</div>

		</div>
	</div>
</div>