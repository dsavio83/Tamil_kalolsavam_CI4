<?php
$subdistricts = $subdistricts ?? [];
$schools = $schools ?? [];
$isEdit = !empty($selected_user[0]['user_id']);
?>

<!-- Add / Edit Admin User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <!-- Modal Header -->
      <div class="modal-header bg-light border-bottom border-light-subtle py-3 px-4">
        <div class="d-flex align-items-center gap-3">
          <div id="modalIconContainer" class="rounded-3 <?php echo $isEdit ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle'; ?> p-2 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi <?php echo $isEdit ? 'bi-pencil-square' : 'bi-person-plus-fill'; ?> fs-5" id="userModalIcon"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold text-dark mb-0" id="userModalLabel">
              <?php echo $isEdit ? 'Edit Admin User' : 'Add New Admin User'; ?>
            </h5>
            <p class="text-muted small mb-0" id="userModalSubTitle" style="font-size: 0.8rem;">
              <?php echo $isEdit ? 'Update credentials, roles, and assigned jurisdiction.' : 'Configure administrative credentials and territorial access.'; ?>
            </p>
          </div>
        </div>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body p-4">
        <?php echo form_open('user/admin_users/add_admin', array('id' => 'formUser', 'class' => 'm-0')); ?>
        <input type="hidden" name="show_generate_admin" id="show_generate_admin" value="<?php echo (!empty($show_generate_admin)) ? 0 : 1; ?>" />
        <input type="hidden" name="hidUserId" id="hidUserId" value="<?php echo esc($selected_user[0]['user_id'] ?? ''); ?>" />

        <!-- Row 1: Username & Password -->
        <div class="row g-3 mb-3">
          <!-- Username -->
          <div class="col-12 col-md-6">
            <label for="txtNewUserName" class="form-label small fw-semibold text-secondary mb-1">
              <i class="bi bi-person-fill text-primary me-1"></i>User Name
              <span class="text-danger">*</span>
            </label>
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3"><i class="bi bi-at"></i></span>
              <?php echo form_input("txtNewUserName", (string) (@$selected_user[0]['user_name'] ?? ''), 'class="form-control form-control-sm rounded-end-3 font-monospace" id="txtNewUserName" placeholder="e.g. palakkad_admin" autocomplete="off"'); ?>
            </div>
            <div class="form-text text-muted small" style="font-size: 0.72rem;">Unique login identifier across the system.</div>
          </div>

          <!-- Password -->
          <div class="col-12 col-md-6">
            <label for="txtNewPassword" class="form-label small fw-semibold text-secondary mb-1">
              <i class="bi bi-key-fill text-primary me-1"></i>Password
              <span class="text-danger" id="pwdRequiredStar" style="<?php echo $isEdit ? 'display:none;' : ''; ?>">*</span>
            </label>
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3"><i class="bi bi-shield-lock"></i></span>
              <?php echo form_password("txtNewPassword", '', 'class="form-control form-control-sm border-end-0 font-monospace" id="txtNewPassword" placeholder="' . ($isEdit ? 'Leave blank to keep unchanged' : 'Enter password') . '" autocomplete="new-password"'); ?>
              <button class="btn btn-outline-secondary border-start-0 rounded-end-3" type="button" id="btnTogglePwd" onclick="togglePasswordVisibility('txtNewPassword', this);" title="Show/Hide Password">
                <i class="bi bi-eye-fill"></i>
              </button>
            </div>
            <div class="form-text text-muted small" id="pwdHelpText" style="font-size: 0.72rem;">
              <?php echo $isEdit ? 'Leave blank to preserve current active password.' : 'Minimum 4 characters recommended.'; ?>
            </div>
          </div>
        </div>

        <!-- Row 2: User Role Selection -->
        <div class="row g-3 mb-3">
          <div class="col-12">
            <label for="userType" class="form-label small fw-semibold text-secondary mb-1">
              <i class="bi bi-person-badge-fill text-primary me-1"></i>User Role / Hierarchy
              <span class="text-danger">*</span>
            </label>
            <?php echo form_dropdown("userType", $user_types, @$selected_user[0]['user_type'], 'id="userType" class="form-select form-select-sm rounded-3" onChange="javascript:loadDistrict()"'); ?>
            <div id="divGenerateSubDistAdmin_user_type" class="mt-1"></div>
          </div>
        </div>

        <!-- Row 3: Territorial Jurisdiction (Cascading Dynamic Dropdowns) -->
        <div class="p-3 bg-light rounded-3 border border-light-subtle mb-2">
          <div class="small fw-semibold text-secondary mb-2.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-geo-alt-fill text-danger"></i>
            <span>Jurisdiction & Scope</span>
          </div>

          <div class="row g-2.5">
            <!-- District -->
            <div class="col-12 col-md-4" id="divDistrict" style="display:<?php echo (isset($selected_user[0]['rev_district_code']) && @$selected_user[0]['rev_district_code'] != '0') ? 'block' : 'none'; ?>;">
              <label for="cmbDistrict" class="form-label small fw-semibold text-secondary mb-1">
                Revenue District
              </label>
              <?php echo form_dropdown("cmbDistrict", $states, @$selected_user[0]['rev_district_code'], 'id="cmbDistrict" class="form-select form-select-sm rounded-3" onChange="javascript:loadSubDistrict()"'); ?>
              <div id="divGenerateSubDistAdmin_dist" class="mt-1"></div>
            </div>

            <!-- Sub-district -->
            <div class="col-12 col-md-4" id="divSubdistrict" style="display:<?php echo (isset($selected_user[0]['sub_district_code']) && @$selected_user[0]['sub_district_code'] != '0') ? 'block' : 'none'; ?>;">
              <label for="cmbSubDistrict" class="form-label small fw-semibold text-secondary mb-1">
                Sub-District
              </label>
              <div id="divSubdistrictCombo">
                <?php echo form_dropdown("cmbSubDistrict", $subdistricts, @$selected_user[0]['sub_district_code'], 'id="cmbSubDistrict" class="form-select form-select-sm rounded-3" onChange="javascript:loadSchool()"'); ?>
              </div>
              <div id="divGenerateSubDistAdmin_sub_dist" class="mt-1"></div>
            </div>

            <!-- School -->
            <div class="col-12 col-md-4" id="divSchool" style="display:<?php echo (isset($selected_user[0]['school_code']) && @$selected_user[0]['school_code'] != '0') ? 'block' : 'none'; ?>;">
              <label for="cmbSchool" class="form-label small fw-semibold text-secondary mb-1">
                School
              </label>
              <div id="divSchoolCombo">
                <?php echo form_dropdown("cmbSchool", $schools, @$selected_user[0]['school_code'], 'id="cmbSchool" class="form-select form-select-sm rounded-3"'); ?>
              </div>
            </div>
          </div>
        </div>

        <?php echo form_close(); ?>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer bg-light px-4 py-2.5 border-top border-light-subtle d-flex justify-content-between">
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3.5" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i>Cancel
        </button>
        <button type="button" id="btnSubmitUser" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold shadow-sm d-inline-flex align-items-center gap-1.5" onclick="submitAdminUserForm();">
          <i class="bi bi-check2-circle" id="btnSubmitUserIcon"></i>
          <span id="btnSubmitUserSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          <span id="btnSubmitUserText"><?php echo $isEdit ? 'Update Admin User' : 'Save Admin User'; ?></span>
        </button>
      </div>
    </div>
  </div>
</div>

<?php if ($isEdit): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
    var modalEl = document.getElementById('userModal');
    if (modalEl) {
      var myModal = bootstrap.Modal.getOrCreateInstance(modalEl);
      myModal.show();
    }
  }
});
</script>
<?php endif; ?>
