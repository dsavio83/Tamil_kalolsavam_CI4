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
  height: auto;
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
  transition: all 0.25s ease, opacity 0.3s ease, transform 0.3s ease;
  cursor: pointer;
}

.school-card-item.removing {
  opacity: 0;
  transform: scale(0.85);
  pointer-events: none;
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

    <!-- Credentials & Action Row -->
    <div class="row g-3 mb-4 align-items-end">
      <div class="col-12 col-md-5">
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

      <div class="col-12 col-md-5">
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

      <div class="col-12 col-md-2">
        <button type="submit" id="btnSubmitForm" class="btn btn-primary w-100 rounded-pill fw-semibold py-2 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm" style="min-height: 42px;">
          <i class="bi bi-check-circle-fill" id="btnSubmitIcon"></i> <span id="btnSubmitText"><?php echo $is_edit ? 'Update' : 'Save'; ?></span>
        </button>
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
        <span class="badge bg-light text-secondary border rounded-pill px-2 py-1 small" id="remainingSchoolsCountBadge">
          <span id="remainingCountText"><?php echo count($all_schools_list); ?></span> Available
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
        <?php if (!$is_edit) { ?>
        <button type="button" class="btn btn-sm btn-primary fw-semibold shadow-sm" onclick="bulkAddAllSchools();" id="btnBulkAdd" title="Create individual cluster user accounts for all schools at once">
          <i class="bi bi-lightning-charge-fill me-1"></i> 1-Click Add All Schools
        </button>
        <?php } ?>
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
      <div class="alert alert-info py-3 text-center" id="emptySchoolsAlert">No schools available in this sub-district.</div>
    <?php } ?>



    <input type="hidden" name="hidUserId" id="hidUserId" value="<?php echo esc($current_user_id); ?>" />
    <?php echo form_close(); ?>
  </div>

</div>

<script>
// Toast Notification instance using SweetAlert2
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3200,
  timerProgressBar: true,
  didOpen: function(toast) {
    toast.addEventListener('mouseenter', Swal.stopTimer);
    toast.addEventListener('mouseleave', Swal.resumeTimer);
  }
});

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

  var isEdit = document.getElementById('hidUserId').value !== '';
  if (!isEdit) {
    var usernameInput = document.getElementById('txtNewUserName');
    var passwordInput = document.getElementById('txtNewPassword');
    if (!checked) {
      // Clear username & password when deselected
      if (usernameInput) usernameInput.value = '';
      if (passwordInput) passwordInput.value = '';
    } else if (checkboxes.length > 0) {
      if (usernameInput && !usernameInput.value.trim()) usernameInput.value = checkboxes[0].value;
      if (passwordInput && !passwordInput.value.trim()) passwordInput.value = checkboxes[0].value;
    }
  }

  updateSelectedCount();
}

function updateSelectedCount() {
  var checkboxes = document.querySelectorAll('.school-checkbox:checked, input[type="checkbox"]:disabled:checked');
  var badge = document.getElementById('selectedSchoolsCountBadge');
  if (badge) {
    badge.innerText = checkboxes.length + ' Selected';
  }
}

function updateRemainingCount() {
  var remainingItems = document.querySelectorAll('.school-card-item:not(.removing)');
  var countBadge = document.getElementById('remainingCountText');
  if (countBadge) {
    countBadge.innerText = remainingItems.length;
  }
  if (remainingItems.length === 0) {
    var container = document.getElementById('schoolsContainer');
    if (container) {
      container.innerHTML = '<div class="alert alert-success py-4 text-center w-100"><i class="bi bi-check-circle-fill fs-2 d-block mb-2 text-success"></i><h5 class="fw-bold mb-1">All Schools Assigned!</h5><p class="text-muted small mb-3">All schools in this sub-district have been assigned to clusters.</p><a href="<?= base_url('user/user_cluster') ?>" class="btn btn-primary btn-sm rounded-pill px-4"><i class="bi bi-arrow-left me-1"></i> Go to Cluster List</a></div>';
    }
    var btnBulk = document.getElementById('btnBulkAdd');
    if (btnBulk) btnBulk.style.display = 'none';
  }
}

// Auto-fill when checked, and auto-clear when unchecked
function autoFillCredentials(checkbox) {
  var usernameInput = document.getElementById('txtNewUserName');
  var passwordInput = document.getElementById('txtNewPassword');
  var isEdit = document.getElementById('hidUserId').value !== '';
  if (isEdit || !usernameInput || !passwordInput) return;

  if (checkbox.checked) {
    // When checked: set username and password to this school code
    usernameInput.value = checkbox.value;
    passwordInput.value = checkbox.value;
  } else {
    // When unchecked: check remaining selected schools
    var checkedBoxes = document.querySelectorAll('.school-checkbox:checked');
    if (checkedBoxes.length === 0) {
      // Clear username & password if no school is selected
      usernameInput.value = '';
      passwordInput.value = '';
    } else {
      // If the unchecked school was currently in username, switch to the first remaining checked school
      if (usernameInput.value === checkbox.value) {
        usernameInput.value = checkedBoxes[0].value;
        passwordInput.value = checkedBoxes[0].value;
      }
    }
  }
}

// 1-Click Bulk Add All Schools
function bulkAddAllSchools() {
  var remainingCards = document.querySelectorAll('.school-card-item:not(.disabled):not(.removing)');
  if (remainingCards.length === 0) {
    Swal.fire({
      icon: 'info',
      title: 'No Schools to Add',
      text: 'All schools in this sub-district are already assigned to clusters.'
    });
    return;
  }

  Swal.fire({
    title: '1-Click Bulk Add All Schools?',
    html: '<p>This will automatically create a separate cluster user account for all <strong>' + remainingCards.length + '</strong> unassigned schools in this sub-district.</p><p class="text-muted small mb-0">Username & Password for each school will be set to its School Code.</p>',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#0284c7',
    cancelButtonColor: '#64748b',
    confirmButtonText: '<i class="bi bi-lightning-charge-fill me-1"></i> Yes, Add All Schools',
    cancelButtonText: 'Cancel'
  }).then(function(result) {
    if (result.isConfirmed) {
      Swal.fire({
        title: 'Creating Cluster Accounts...',
        text: 'Please wait while cluster accounts are configured for all schools.',
        allowOutsideClick: false,
        didOpen: function() {
          Swal.showLoading();
        }
      });

      var bulkFormData = new FormData();
      bulkFormData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

      fetch('<?= base_url('user/user_cluster/bulk_add_all_schools_ajax') ?>', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          '<?= config("Security")->headerName ?>': '<?= csrf_hash() ?>'
        },
        body: bulkFormData
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Bulk Addition Complete!',
            text: data.message || 'Cluster accounts created for all schools.',
            confirmButtonText: 'View Cluster List'
          }).then(function() {
            window.location.href = '<?= base_url('user/user_cluster') ?>';
          });
        } else {
          Swal.fire('Failed', data.message || 'Could not complete bulk addition.', 'error');
        }
      })
      .catch(function(err) {
        console.error(err);
        Swal.fire('Error', 'An unexpected error occurred. Please try again.', 'error');
      });
    }
  });
}

document.addEventListener('DOMContentLoaded', function() {
  updateSelectedCount();

  var form = document.getElementById('formUser');
  if (form) {
    form.addEventListener('submit', function(e) {
      var usernameInput = document.getElementById('txtNewUserName');
      var passwordInput = document.getElementById('txtNewPassword');
      var username = usernameInput ? usernameInput.value.trim() : '';
      var password = passwordInput ? passwordInput.value.trim() : '';
      var isEdit = document.getElementById('hidUserId').value !== '';

      if (!username) {
        e.preventDefault();
        Swal.fire('Required Field', 'Please enter or select a School / User Name.', 'warning');
        return false;
      }

      if (!isEdit && !password) {
        e.preventDefault();
        Swal.fire('Required Field', 'Please enter a Password.', 'warning');
        return false;
      }

      var checkedSchools = document.querySelectorAll('.school-checkbox:checked, input[type="checkbox"]:disabled:checked');
      if (checkedSchools.length === 0) {
        e.preventDefault();
        Swal.fire('Select School', 'Please select at least one school for this cluster.', 'warning');
        return false;
      }

      // If in Add mode, process via AJAX to show Toast and stay on page ready for next school
      if (!isEdit) {
        e.preventDefault();

        var btnSubmit = document.getElementById('btnSubmitForm');
        var btnText = document.getElementById('btnSubmitText');
        var btnIcon = document.getElementById('btnSubmitIcon');

        if (btnSubmit) btnSubmit.disabled = true;
        if (btnText) btnText.innerText = 'Saving...';
        if (btnIcon) btnIcon.className = 'spinner-border spinner-border-sm';

        var formData = new FormData(form);

        fetch('<?= base_url('user/user_cluster/save_user_cluster_ajax') ?>', {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: formData
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
          if (data.success) {
            // Show toast message
            Toast.fire({
              icon: 'success',
              title: data.message || 'School cluster created successfully!'
            });

            // Remove assigned school card(s) from DOM with smooth animation
            if (data.assigned_schools && data.assigned_schools.length > 0) {
              data.assigned_schools.forEach(function(code) {
                var card = document.querySelector('.school-card-item[data-school-code="' + code.toLowerCase() + '"]');
                if (card) {
                  card.classList.add('removing');
                  setTimeout(function() {
                    card.remove();
                    updateSelectedCount();
                    updateRemainingCount();
                  }, 300);
                }
              });
            }

            // Clear inputs for next school
            usernameInput.value = '';
            passwordInput.value = '';

            // Uncheck any remaining checkboxes
            var allCheckboxes = document.querySelectorAll('.school-checkbox:checked');
            allCheckboxes.forEach(function(cb) {
              cb.checked = false;
            });
            updateSelectedCount();

            // Focus on search or container for quick next selection
            var searchBox = document.getElementById('schoolSearchInput');
            if (searchBox) {
              searchBox.value = '';
              filterSchools();
            }
          } else {
            Swal.fire('Cannot Save', data.message || 'Error occurred while saving.', 'error');
          }
        })
        .catch(function(err) {
          console.error(err);
          Swal.fire('Error', 'An unexpected network error occurred.', 'error');
        })
        .finally(function() {
          if (btnSubmit) btnSubmit.disabled = false;
          if (btnText) btnText.innerText = 'Save';
          if (btnIcon) btnIcon.className = 'bi bi-check-circle-fill';
        });
      }
    });
  }
});
</script>
