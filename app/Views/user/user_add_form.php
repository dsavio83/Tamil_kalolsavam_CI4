<?php
/**
 * Standalone Add User View - Sub-District Admin
 */
$is_district = !empty($is_district) || ((int)session()->get('USER_TYPE') === 2);
$admin_role_label = $is_district ? 'District User' : 'Sub-District Admin';

// Group user rights by label_name
$grouped_rights = [];
if (!empty($user_rights)) {
    foreach ($user_rights as $ur) {
        $lbl = !empty($ur['label_name']) ? $ur['label_name'] : 'General';
        $grouped_rights[$lbl][] = $ur;
    }
}

// Category icons map
$category_icons = [
    'User'             => 'bi-person-badge',
    'School'           => 'bi-mortarboard',
    'Stage'            => 'bi-soundwave',
    'Data Handling'    => 'bi-database-gear',
    'Pre Fest Reports' => 'bi-file-earmark-bar-graph',
    'Results'          => 'bi-trophy',
    'Certificate'      => 'bi-award',
    'General'          => 'bi-shield-check'
];
?>

<style>
:root {
  --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
  --card-border: #e2e8f0;
  --text-main: #0f172a;
  --radius-lg: 16px;
  --radius-md: 12px;
  --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.user-module-wrapper {
  padding: 10px 0 40px;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.form-section-card {
  background: #ffffff;
  border-radius: var(--radius-lg);
  border: 1px solid var(--card-border);
  box-shadow: var(--shadow-sm);
  padding: 28px;
  margin-bottom: 24px;
}

.form-header-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid var(--card-border);
  padding-bottom: 16px;
  margin-bottom: 24px;
  flex-wrap: wrap;
  gap: 12px;
}

.form-title {
  font-size: 1.35rem;
  font-weight: 700;
  color: var(--text-main);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 10px;
}

.custom-form-label {
  font-weight: 600;
  font-size: 0.875rem;
  color: #334155;
  margin-bottom: 6px;
  display: block;
}

.input-with-icon {
  position: relative;
}

.input-with-icon .input-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 1.05rem;
}

.custom-text-input {
  width: 100%;
  padding: 10px 14px 10px 42px;
  border-radius: var(--radius-md);
  border: 1px solid #cbd5e1;
  font-size: 0.9rem;
  color: #1e293b;
  transition: all 0.2s;
}

.custom-text-input:focus {
  border-color: #6366f1;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  outline: none;
}

.toggle-pwd-btn {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 4px;
  font-size: 1rem;
}

.permission-category-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: var(--radius-md);
  margin-bottom: 16px;
  overflow: hidden;
}

.permission-category-header {
  background: #f1f5f9;
  padding: 12px 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #e2e8f0;
}

.category-title-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9rem;
  font-weight: 700;
  color: #1e293b;
}

.category-title-wrap i {
  color: #4f46e5;
  font-size: 1.1rem;
}

.permission-items-grid {
  padding: 16px 18px;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 12px;
}

.permission-check-item {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #ffffff;
  padding: 10px 14px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  transition: all 0.15s ease;
  cursor: pointer;
}

.permission-check-item:hover {
  border-color: #c7d2fe;
  background: #fdfefe;
}

.permission-check-item input[type="checkbox"] {
  width: 17px;
  height: 17px;
  accent-color: #4f46e5;
  cursor: pointer;
}

.permission-check-label {
  font-size: 0.85rem;
  color: #334155;
  font-weight: 500;
  cursor: pointer;
  user-select: none;
  flex-grow: 1;
}

.perm-controls-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #eef2ff;
  border: 1px solid #c7d2fe;
  border-radius: var(--radius-md);
  padding: 12px 18px;
  margin-bottom: 16px;
  flex-wrap: wrap;
  gap: 10px;
}
</style>

<div class="user-module-wrapper">
  <div class="pagetitle mb-3">
    <h1>Add New User</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('welcome'); ?>" title="Home">Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url('user/user_registration'); ?>" title="User Registration">User Registration</a></li>
        <li class="breadcrumb-item active">Add New User</li>
      </ol>
    </nav>
  </div>

  <div class="form-section-card">
    <?php echo form_open('user/user_registration/userinsert', ['id' => 'formUser']); ?>
    
    <div class="form-header-bar">
      <div>
        <h2 class="form-title">
          <i class="bi bi-person-plus text-primary"></i> Create <?php echo $admin_role_label; ?>
        </h2>
        <p class="text-muted small mb-0 mt-1">Configure account credentials and authorized module rights.</p>
      </div>

      <a href="<?php echo base_url('user/user_registration'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Back to List
      </a>
    </div>

    <!-- Credentials & Role Assignment -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-6">
        <label class="custom-form-label" for="txtNewUserName">
          User Name <span class="text-danger">*</span>
        </label>
        <div class="input-with-icon">
          <i class="bi bi-person input-icon"></i>
          <input type="text" name="txtNewUserName" id="txtNewUserName" class="custom-text-input" 
                 placeholder="Enter username (e.g. subdist_user or stagemanager1)" required />
        </div>
      </div>

      <div class="col-12 col-md-6">
        <label class="custom-form-label" for="txtNewPassword">
          Password <span class="text-danger">*</span>
        </label>
        <div class="input-with-icon">
          <i class="bi bi-lock input-icon"></i>
          <input type="password" name="txtNewPassword" id="txtNewPassword" class="custom-text-input" 
                 placeholder="Enter secure password" required />
          <button type="button" class="toggle-pwd-btn" onclick="togglePasswordVisibility('txtNewPassword', 'pwdToggleIcon')">
            <i class="bi bi-eye" id="pwdToggleIcon"></i>
          </button>
        </div>
      </div>

      <!-- User Role Selector -->
      <div class="col-12 col-md-6">
        <label class="custom-form-label" for="selUserType">
          User Role <span class="text-danger">*</span>
        </label>
        <div class="input-with-icon">
          <i class="bi bi-shield-lock input-icon"></i>
          <select name="selUserType" id="selUserType" class="custom-text-input form-select" onchange="handleRoleChange(this.value);" required>
            <option value="3" selected><?php echo $admin_role_label; ?></option>
            <option value="6">Stage Manager</option>
          </select>
        </div>
        <div class="form-text small text-muted">
          <?php echo $admin_role_label; ?> manages portal data; Stage Manager operates live stage schedule and lot stepping.
        </div>
      </div>

      <!-- Assigned Stage Dropdown (Dynamic for Stage Manager) -->
      <div class="col-12 col-md-6" id="stageAssignmentWrapper" style="display:none;">
        <label class="custom-form-label" for="selAssignedStage">
          Assigned Festival Stage <span class="text-danger" id="stageReqAsterisk">*</span>
        </label>
        <div class="input-with-icon">
          <i class="bi bi-soundwave input-icon"></i>
          <select name="selAssignedStage" id="selAssignedStage" class="custom-text-input form-select">
            <option value="">-- Select Festival Stage --</option>
            <?php if (!empty($stages)) { ?>
              <?php foreach ($stages as $stg) { ?>
                <option value="<?php echo $stg['stage_id']; ?>">
                  <?php echo esc($stg['stage_name']) . (!empty($stg['stage_desc']) ? ' (' . esc($stg['stage_desc']) . ')' : ''); ?>
                </option>
              <?php } ?>
            <?php } ?>
          </select>
        </div>
        <div class="form-text small text-muted">
          The assigned manager will conduct live programs and control participant lot sequencing on this stage.
        </div>
      </div>
    </div>

    <!-- Stage Manager Workflow Notice -->
    <div id="stageManagerInfoBox" class="alert alert-light border border-purple-subtle bg-purple bg-opacity-10 rounded-3 mb-4 p-3" style="display:none; align-items:center; gap:12px;">
      <i class="bi bi-info-circle-fill text-purple fs-4"></i>
      <div class="small text-dark">
        <strong>Stage Manager Onboarding Note:</strong> When created, Stage Managers start with initial password pending (<code>CHANGEPWD = 'N'</code>). Upon first login, they will change their password and will be directly routed to their dedicated <strong>Stage Manager Console</strong> (<code>stage/manager</code>).
      </div>
    </div>

    <!-- Stage Manager Role Permissions (Exclusive & Strictly Scoped) -->
    <div id="stageManagerPermissionsWrapper" style="display:none;">
      <div class="perm-controls-bar border-purple-subtle bg-purple bg-opacity-10 mb-3 p-3 rounded-3">
        <div>
          <div class="fw-bold text-purple d-flex align-items-center gap-2">
            <i class="bi bi-shield-lock-fill"></i> Stage Manager Permissions (Role Restricted)
          </div>
          <div class="small text-muted mt-1">
            Stage Managers have strictly restricted scope. Standard created accounts have NO other module rights. Only Stage Management, Participant Viewing, and Mark Entry may be granted.
          </div>
        </div>
      </div>

      <div class="permission-category-card border-purple-subtle mb-3">
        <div class="permission-category-header bg-purple bg-opacity-10">
          <div class="category-title-wrap">
            <i class="bi bi-person-check-fill text-purple"></i>
            <span>Authorized Stage Manager Permissions</span>
            <span class="badge bg-purple text-white rounded-pill px-2 py-1 small">3 Permitted</span>
          </div>
        </div>

        <div class="permission-items-grid p-3">
          <!-- 1. Stage Management -->
          <label class="permission-check-item border rounded-3 p-3 d-flex align-items-start gap-2" for="chkSmRight_3">
            <input type="checkbox" name="chkRight_3" id="chkSmRight_3" 
                   class="perm-checkbox-sm form-check-input mt-1" 
                   value="3" checked disabled />
            <div>
              <span class="permission-check-label fw-bold d-block text-dark">Stage Management &amp; Console</span>
              <span class="text-muted small">Access to Stage Manager Console (stage/manager), live program dispatch, lot stepping &amp; schedule delay tracking.</span>
            </div>
          </label>

          <!-- 2. Participant View -->
          <label class="permission-check-item border rounded-3 p-3 d-flex align-items-start gap-2" for="chkSmRight_29">
            <input type="checkbox" name="chkRight_29" id="chkSmRight_29" 
                   class="perm-checkbox-sm form-check-input mt-1" 
                   value="29" disabled />
            <div>
              <span class="permission-check-label fw-bold d-block text-dark">View Participant Details (Confidential)</span>
              <span class="text-muted small">Allows viewing student names, schools, and rosters. Leave unchecked unless explicitly authorized by Admin.</span>
            </div>
          </label>

          <!-- 3. Result / Mark Entry -->
          <label class="permission-check-item border rounded-3 p-3 d-flex align-items-start gap-2" for="chkSmRight_12">
            <input type="checkbox" name="chkRight_12" id="chkSmRight_12" 
                   class="perm-checkbox-sm form-check-input mt-1" 
                   value="12" disabled />
            <div>
              <span class="permission-check-label fw-bold d-block text-dark">Result &amp; Mark Entry</span>
              <span class="text-muted small">Allows entering judges' scores and competition marks directly from the stage console.</span>
            </div>
          </label>
        </div>
      </div>

      <div class="alert alert-warning py-2 px-3 rounded-12 small mb-4">
        <i class="bi bi-shield-exclamation me-1"></i> <strong>Confidentiality Policy:</strong> In accordance with festival integrity rules, student identities are kept anonymous by default. Stage Managers have no access to School Registration, Reports, Certificates, or System Configuration.
      </div>
    </div>

    <!-- Sub-District Admin Permissions (Hidden for Stage Manager) -->
    <div id="adminPermissionsWrapper">
      <!-- Permissions Control Bar -->
      <div class="perm-controls-bar">
        <div class="fw-bold text-primary d-flex align-items-center gap-2">
          <i class="bi bi-shield-lock-fill"></i> User Rights & Module Permissions
        </div>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-sm btn-light border fw-semibold" onclick="toggleAllPermissions(true)">
            <i class="bi bi-check-all text-success me-1"></i> Select All
          </button>
          <button type="button" class="btn btn-sm btn-light border fw-semibold" onclick="toggleAllPermissions(false)">
            <i class="bi bi-x text-danger me-1"></i> Deselect All
          </button>
        </div>
      </div>

      <!-- Categorized Permissions Grid -->
      <?php if (!empty($grouped_rights)) { ?>
        <?php foreach ($grouped_rights as $cat_name => $items) { 
          $cat_icon = $category_icons[$cat_name] ?? 'bi-folder2-open';
          $cat_slug = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($cat_name));
        ?>
          <div class="permission-category-card">
            <div class="permission-category-header">
              <div class="category-title-wrap">
                <i class="bi <?php echo $cat_icon; ?>"></i>
                <span><?php echo esc($cat_name); ?></span>
                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-1 small">
                  <?php echo count($items); ?>
                </span>
              </div>
              <div class="form-check m-0">
                <input class="form-check-input category-master-check" type="checkbox" id="checkCat_<?php echo $cat_slug; ?>" 
                       onchange="toggleCategoryGroup('<?php echo $cat_slug; ?>', this.checked)">
                <label class="form-check-label small text-muted user-select-none" for="checkCat_<?php echo $cat_slug; ?>">
                  Select All
                </label>
              </div>
            </div>

            <div class="permission-items-grid">
              <?php foreach ($items as $ur) { 
                $rf_id = (int)$ur['rf_id'];
              ?>
                <label class="permission-check-item" for="chkRight_<?php echo $rf_id; ?>">
                  <input type="checkbox" name="chkRight_<?php echo $rf_id; ?>" id="chkRight_<?php echo $rf_id; ?>" 
                         class="perm-checkbox group-<?php echo $cat_slug; ?>" 
                         value="<?php echo $rf_id; ?>" 
                         onchange="syncCategoryHeader('<?php echo $cat_slug; ?>');" />
                  <span class="permission-check-label"><?php echo esc($ur['rf_functionality']); ?></span>
                </label>
              <?php } ?>
            </div>
          </div>
        <?php } ?>
      <?php } ?>
    </div>

    <!-- Actions -->
    <div class="d-flex justify-content-center align-items-center gap-3 mt-4 pt-3 border-top flex-wrap">
      <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill"></i> Save User
      </button>
      <a href="<?php echo base_url('user/user_registration'); ?>" class="btn btn-light border px-4 py-2 rounded-pill fw-semibold">
        <i class="bi bi-x-circle me-1"></i> Cancel
      </a>
    </div>

    <input type="hidden" name="hidUserId" id="hidUserId" value="" />
    <?php echo form_close(); ?>
  </div>
</div>

<script>
function handleRoleChange(roleVal) {
  var role = parseInt(roleVal, 10);
  var stageWrap = document.getElementById('stageAssignmentWrapper');
  var infoBox = document.getElementById('stageManagerInfoBox');
  var asterisk = document.getElementById('stageReqAsterisk');
  var stageSelect = document.getElementById('selAssignedStage');
  var adminPerms = document.getElementById('adminPermissionsWrapper');
  var smPerms = document.getElementById('stageManagerPermissionsWrapper');

  if (role === 6) {
    if (stageWrap) stageWrap.style.display = 'block';
    if (infoBox) infoBox.style.display = 'flex';
    if (asterisk) asterisk.style.display = 'inline';
    if (adminPerms) {
      adminPerms.style.display = 'none';
      adminPerms.querySelectorAll('input').forEach(function(el) { el.disabled = true; });
    }
    if (smPerms) {
      smPerms.style.display = 'block';
      smPerms.querySelectorAll('input').forEach(function(el) { el.disabled = false; });
    }
  } else {
    if (stageWrap) stageWrap.style.display = 'none';
    if (infoBox) infoBox.style.display = 'none';
    if (asterisk) asterisk.style.display = 'none';
    if (stageSelect) stageSelect.value = '';
    if (smPerms) {
      smPerms.style.display = 'none';
      smPerms.querySelectorAll('input').forEach(function(el) { el.disabled = true; });
    }
    if (adminPerms) {
      adminPerms.style.display = 'block';
      adminPerms.querySelectorAll('input').forEach(function(el) { el.disabled = false; });
    }
  }
}

function togglePasswordVisibility(inputId, iconId) {
  var input = document.getElementById(inputId);
  var icon = document.getElementById(iconId);
  if (!input || !icon) return;

  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('bi-eye');
    icon.classList.add('bi-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.remove('bi-eye-slash');
    icon.classList.add('bi-eye');
  }
}

function toggleAllPermissions(checked) {
  var checkboxes = document.querySelectorAll('.perm-checkbox');
  checkboxes.forEach(function(cb) { cb.checked = checked; });
  var masterChecks = document.querySelectorAll('.category-master-check');
  masterChecks.forEach(function(mc) { mc.checked = checked; });
}

function toggleCategoryGroup(catSlug, checked) {
  var groupCheckboxes = document.querySelectorAll('.group-' + catSlug);
  groupCheckboxes.forEach(function(cb) { cb.checked = checked; });
}

function syncCategoryHeader(catSlug) {
  var groupCheckboxes = document.querySelectorAll('.group-' + catSlug);
  var total = groupCheckboxes.length;
  var checkedCount = 0;
  groupCheckboxes.forEach(function(cb) { if (cb.checked) checkedCount++; });
  var catHeader = document.getElementById('checkCat_' + catSlug);
  if (catHeader) { catHeader.checked = (total > 0 && checkedCount === total); }
}

document.addEventListener('DOMContentLoaded', function() {
  var form = document.getElementById('formUser');
  if (form) {
    form.addEventListener('submit', function(e) {
      var username = document.getElementById('txtNewUserName').value.trim();
      var password = document.getElementById('txtNewPassword').value.trim();

      if (!username) {
        e.preventDefault();
        Swal.fire('Required Field', 'Please enter a valid User Name.', 'warning');
        return false;
      }
      if (!password) {
        e.preventDefault();
        Swal.fire('Required Field', 'Please enter a Password.', 'warning');
        return false;
      }
    });
  }
});
</script>
