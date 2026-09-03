<?php
/**
 * Cluster User Add/Edit Form - Modern, Colorful & Responsive
 */
$is_edit = (!empty($selected_cluster) && count($selected_cluster) > 0 && !empty($selected_cluster[0]['user_id']));
$current_user_id = $is_edit ? ($selected_cluster[0]['user_id'] ?? '') : '';
$current_username = $is_edit ? ($selected_cluster[0]['user_name'] ?? '') : '';

$entered_school = $entered_school ?? [];
$selected_cluster_schools = $selected_cluster_schools ?? [];
$schools = $schools ?? [];

// Merge and deduplicate schools list for clean rendering
$all_schools_list = [];

if (!empty($selected_cluster_schools)) {
    foreach ($selected_cluster_schools as $sch) {
        $all_schools_list[$sch['school_code']] = [
            'code'     => $sch['school_code'],
            'name'     => $sch['school_name'],
            'checked'  => true,
            'disabled' => in_array($sch['school_code'], $entered_school)
        ];
    }
}

if (!empty($schools)) {
    foreach ($schools as $sch) {
        if (!isset($all_schools_list[$sch['school_code']])) {
            $all_schools_list[$sch['school_code']] = [
                'code'     => $sch['school_code'],
                'name'     => $sch['school_name'],
                'checked'  => false,
                'disabled' => false
            ];
        }
    }
}

$total_schools_count = count($all_schools_list);
?>

<style>
:root {
  --primary-gradient: linear-gradient(135deg, #0284c7 0%, #6366f1 100%);
  --card-bg: #ffffff;
  --bg-subtle: #f8fafc;
  --card-border: #e2e8f0;
  --text-main: #0f172a;
  --text-muted: #64748b;
  --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
  --radius-lg: 16px;
  --radius-md: 12px;
}

.cluster-module-wrapper {
  padding: 10px 0 40px;
  color: var(--text-main);
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.module-header-card {
  background: var(--card-bg);
  border-radius: var(--radius-lg);
  padding: 24px 28px;
  border: 1px solid var(--card-border);
  box-shadow: var(--shadow-sm);
  margin-bottom: 24px;
  position: relative;
  overflow: hidden;
}

.module-header-card::before {
  content: "";
  position: absolute;
  top: 0; left: 0; right: 0; height: 4px;
  background: var(--primary-gradient);
}

.module-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-main);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 10px;
}

.module-title i { color: #0284c7; font-size: 1.6rem; }
.module-subtitle { color: var(--text-muted); font-size: 0.88rem; margin-top: 4px; margin-bottom: 0; }

.form-section-card {
  background: var(--card-bg);
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
  font-size: 1.25rem;
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
  border-color: #0284c7;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
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

.toggle-pwd-btn:hover { color: #475569; }

/* Schools Section */
.schools-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #f0f9ff;
  border: 1px solid #bae6fd;
  border-radius: var(--radius-md);
  padding: 12px 18px;
  margin-bottom: 18px;
  flex-wrap: wrap;
  gap: 12px;
}

.schools-search-box {
  position: relative;
  min-width: 240px;
  max-width: 320px;
  flex-grow: 1;
}

.schools-search-box i {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
}

.schools-search-box input {
  width: 100%;
  padding: 7px 12px 7px 34px;
  border-radius: 50px;
  border: 1px solid #cbd5e1;
  font-size: 0.84rem;
  outline: none;
  background: #ffffff;
}

.schools-search-box input:focus {
  border-color: #0284c7;
  box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

.schools-grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 12px;
  max-height: 480px;
  overflow-y: auto;
  padding: 4px;
}

.school-card-item {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #ffffff;
  padding: 12px 16px;
  border-radius: var(--radius-md);
  border: 1px solid #e2e8f0;
  transition: all 0.15s ease;
  cursor: pointer;
}

.school-card-item:hover {
  border-color: #7dd3fc;
  background: #f0f9ff;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
}

.school-card-item.disabled {
  background: #f8fafc;
  opacity: 0.75;
  cursor: not-allowed;
}

.school-card-item input[type="checkbox"] {
  width: 18px;
  height: 18px;
  accent-color: #0284c7;
  cursor: pointer;
}

.school-card-info {
  flex-grow: 1;
  user-select: none;
}

.school-card-code {
  font-size: 0.75rem;
  font-weight: 700;
  color: #0284c7;
  background: #e0f2fe;
  padding: 2px 6px;
  border-radius: 4px;
  display: inline-block;
  margin-bottom: 2px;
}

.school-card-name {
  font-size: 0.88rem;
  font-weight: 600;
  color: #1e293b;
  line-height: 1.3;
}
</style>

<div class="cluster-module-wrapper">

  <!-- Page Header -->
  <div class="module-header-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h1 class="module-title">
          <i class="bi bi-diagram-3-fill"></i> <?php echo $is_edit ? 'Edit Cluster School User' : 'Add Cluster School User'; ?>
        </h1>
        <p class="module-subtitle">Assign schools to cluster user account for event data management</p>
      </div>

      <a href="<?php echo base_url('user/user_cluster'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Cluster List
      </a>
    </div>
  </div>

  <!-- Form Card -->
  <div class="form-section-card">
    <?php echo form_open($is_edit ? 'user/user_cluster/update_user_cluster' : 'user/user_cluster/save_user_cluster', ['id' => 'formUser']); ?>

    <!-- Credentials Block -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-6">
        <label class="custom-form-label" for="txtNewUserName">
          User Name <span class="text-danger">*</span>
        </label>
        <div class="input-with-icon">
          <i class="bi bi-person input-icon"></i>
          <input type="text" name="txtNewUserName" id="txtNewUserName" class="custom-text-input" 
                 placeholder="Enter cluster user name" 
                 value="<?php echo esc($current_username); ?>" required />
        </div>
      </div>

      <div class="col-12 col-md-6">
        <label class="custom-form-label" for="txtNewPassword">
          Password <?php echo $is_edit ? '<span class="text-muted fw-normal">(Leave blank to keep existing)</span>' : '<span class="text-danger">*</span>'; ?>
        </label>
        <div class="input-with-icon">
          <i class="bi bi-lock input-icon"></i>
          <input type="password" name="txtNewPassword" id="txtNewPassword" class="custom-text-input" 
                 placeholder="<?php echo $is_edit ? '••••••••' : 'Enter cluster password'; ?>" />
          <button type="button" class="toggle-pwd-btn" onclick="togglePasswordVisibility('txtNewPassword', 'pwdToggleIcon')">
            <i class="bi bi-eye" id="pwdToggleIcon"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Schools Section Header -->
    <div class="schools-toolbar">
      <div class="d-flex align-items-center gap-2">
        <span class="fw-bold text-dark fs-6">
          <i class="bi bi-buildings text-primary me-1"></i> Assign Schools to Cluster
        </span>
        <span class="badge bg-white text-primary border rounded-pill px-2 py-1 small" id="selectedSchoolsCountBadge">
          0 Selected
        </span>
      </div>

      <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="schools-search-box">
          <i class="bi bi-search"></i>
          <input type="text" id="schoolSearchInput" placeholder="Filter schools..." onkeyup="filterSchools();">
        </div>
        <button type="button" class="btn btn-sm btn-light border fw-semibold" onclick="toggleAllSchools(true);">
          <i class="bi bi-check-all text-success me-1"></i> Select All
        </button>
        <button type="button" class="btn btn-sm btn-light border fw-semibold" onclick="toggleAllSchools(false);">
          <i class="bi bi-x text-danger me-1"></i> Clear
        </button>
      </div>
    </div>

    <!-- Schools Grid -->
    <?php if (!empty($all_schools_list)) { ?>
      <div class="schools-grid-container" id="schoolsContainer">
        <?php foreach ($all_schools_list as $code => $sch) { 
          $is_checked = !empty($sch['checked']);
          $is_disabled = !empty($sch['disabled']);
          $sch_name = $sch['name'];
        ?>
          <label class="school-card-item <?php echo $is_disabled ? 'disabled' : ''; ?>" 
                 data-school-code="<?php echo strtolower(esc($code)); ?>" 
                 data-school-name="<?php echo strtolower(esc($sch_name)); ?>">
            <?php if ($is_disabled) { ?>
              <input type="checkbox" name="<?php echo esc($code); ?>chk" value="<?php echo esc($code); ?>" checked disabled />
              <input type="hidden" name="<?php echo esc($code); ?>" value="<?php echo esc($code); ?>" />
            <?php } else { ?>
              <input type="checkbox" class="school-checkbox" name="<?php echo esc($code); ?>" value="<?php echo esc($code); ?>" 
                     <?php echo $is_checked ? 'checked' : ''; ?> onchange="updateSelectedCount(); autoFillCredentials(this);" />
            <?php } ?>
            <div class="school-card-info">
              <span class="school-card-code"><?php echo esc($code); ?></span>
              <div class="school-card-name"><?php echo esc($sch_name); ?></div>
            </div>
          </label>
        <?php } ?>
      </div>
    <?php } else { ?>
      <div class="alert alert-info py-3 text-center">No schools available in this sub-district.</div>
    <?php } ?>

    <!-- Form Submit Actions -->
    <div class="d-flex justify-content-center align-items-center gap-3 mt-4 pt-3 border-top flex-wrap">
      <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill"></i> <?php echo $is_edit ? 'Update Cluster' : 'Save Cluster'; ?>
      </button>
      <a href="<?php echo base_url('user/user_cluster'); ?>" class="btn btn-light border px-4 py-2 rounded-pill fw-semibold">
        <i class="bi bi-x-circle me-1"></i> Cancel
      </a>
    </div>

    <input type="hidden" name="hidUserId" id="hidUserId" value="<?php echo esc($current_user_id); ?>" />
    <?php echo form_close(); ?>
  </div>

</div>

<script>
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

function filterSchools() {
  var query = document.getElementById('schoolSearchInput').value.toLowerCase().trim();
  var items = document.querySelectorAll('.school-card-item');

  items.forEach(function(item) {
    var code = item.getAttribute('data-school-code') || '';
    var name = item.getAttribute('data-school-name') || '';

    if (code.indexOf(query) !== -1 || name.indexOf(query) !== -1) {
      item.style.display = 'flex';
    } else {
      item.style.display = 'none';
    }
  });
}

function toggleAllSchools(checked) {
  var checkboxes = document.querySelectorAll('.school-checkbox:not(:disabled)');
  checkboxes.forEach(function(cb) {
    cb.checked = checked;
  });
  updateSelectedCount();
}

function updateSelectedCount() {
  var checkboxes = document.querySelectorAll('.school-checkbox:checked, input[type="checkbox"]:disabled:checked');
  var badge = document.getElementById('selectedSchoolsCountBadge');
  if (badge) {
    badge.innerText = checkboxes.length + ' Selected';
  }
}

function autoFillCredentials(checkbox) {
  var usernameInput = document.getElementById('txtNewUserName');
  var passwordInput = document.getElementById('txtNewPassword');
  var isEdit = document.getElementById('hidUserId').value !== '';

  if (!isEdit && checkbox.checked && usernameInput && !usernameInput.value.trim()) {
    usernameInput.value = checkbox.value;
    if (passwordInput && !passwordInput.value.trim()) {
      passwordInput.value = checkbox.value;
    }
  }
}

document.addEventListener('DOMContentLoaded', function() {
  updateSelectedCount();

  var form = document.getElementById('formUser');
  if (form) {
    form.addEventListener('submit', function(e) {
      var username = document.getElementById('txtNewUserName').value.trim();
      var password = document.getElementById('txtNewPassword').value.trim();
      var isEdit = document.getElementById('hidUserId').value !== '';

      if (!username) {
        e.preventDefault();
        Swal.fire('Required Field', 'Please enter a valid User Name.', 'warning');
        return false;
      }

      if (!isEdit && !password) {
        e.preventDefault();
        Swal.fire('Required Field', 'Please enter a Password.', 'warning');
        return false;
      }
    });
  }
});
</script>
