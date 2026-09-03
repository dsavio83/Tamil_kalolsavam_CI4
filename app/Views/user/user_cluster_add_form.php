<?php
/**
 * Standalone Add Cluster View
 */
$grouped_rights = [];
if (!empty($user_rights)) {
    foreach ($user_rights as $ur) {
        $lbl = !empty($ur['label_name']) ? $ur['label_name'] : 'General';
        $grouped_rights[$lbl][] = $ur;
    }
}

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

.input-with-icon { position: relative; }
.input-with-icon .input-icon {
  position: absolute; left: 14px; top: 50%;
  transform: translateY(-50%); color: #94a3b8; font-size: 1.05rem;
}

.custom-text-input {
  width: 100%; padding: 10px 14px 10px 42px;
  border-radius: var(--radius-md); border: 1px solid #cbd5e1;
  font-size: 0.9rem; color: #1e293b; transition: all 0.2s;
}

.custom-text-input:focus {
  border-color: #6366f1; background: #ffffff;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15); outline: none;
}

.toggle-pwd-btn {
  position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
  background: none; border: none; color: #94a3b8; cursor: pointer; padding: 4px; font-size: 1rem;
}

.permission-category-card {
  background: #f8fafc; border: 1px solid #e2e8f0;
  border-radius: var(--radius-md); margin-bottom: 16px; overflow: hidden;
}

.permission-category-header {
  background: #f1f5f9; padding: 12px 18px;
  display: flex; align-items: center; justify-content: space-between;
  border-bottom: 1px solid #e2e8f0;
}

.category-title-wrap {
  display: flex; align-items: center; gap: 8px;
  font-size: 0.9rem; font-weight: 700; color: #1e293b;
}

.category-title-wrap i { color: #4f46e5; font-size: 1.1rem; }

.permission-items-grid {
  padding: 16px 18px;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 12px;
}

.permission-check-item {
  display: flex; align-items: center; gap: 10px;
  background: #ffffff; padding: 10px 14px;
  border-radius: 10px; border: 1px solid #e2e8f0;
  transition: all 0.15s ease; cursor: pointer;
}

.permission-check-item:hover { border-color: #c7d2fe; background: #fdfefe; }
.permission-check-item input[type="checkbox"] { width: 17px; height: 17px; accent-color: #4f46e5; cursor: pointer; }
.permission-check-label { font-size: 0.85rem; color: #334155; font-weight: 500; cursor: pointer; user-select: none; flex-grow: 1; }

.perm-controls-bar {
  display: flex; align-items: center; justify-content: space-between;
  background: #eef2ff; border: 1px solid #c7d2fe;
  border-radius: var(--radius-md); padding: 12px 18px;
  margin-bottom: 16px; flex-wrap: wrap; gap: 10px;
}
</style>

<div class="user-module-wrapper">
  <div class="pagetitle mb-3">
    <h1>Add New Cluster</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('welcome'); ?>" title="Home">Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url('user/user_registration/cluster'); ?>" title="Cluster Registration">Cluster Registration</a></li>
        <li class="breadcrumb-item active">Add New Cluster</li>
      </ol>
    </nav>
  </div>

  <div class="form-section-card">
    <?php echo form_open('user/user_registration/clusterinsert', ['id' => 'formCluster']); ?>
    
    <div class="form-header-bar">
      <div>
        <h2 class="form-title">
          <i class="bi bi-person-plus text-primary"></i> Create Cluster Admin User
        </h2>
        <p class="text-muted small mb-0 mt-1">Configure cluster credentials and authorized privileges.</p>
      </div>

      <a href="<?php echo base_url('user/user_registration/cluster'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Cluster List
      </a>
    </div>

    <!-- Credentials -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-6">
        <label class="custom-form-label" for="txtNewUserName">
          User Name <span class="text-danger">*</span>
        </label>
        <div class="input-with-icon">
          <i class="bi bi-person input-icon"></i>
          <input type="text" name="txtNewUserName" id="txtNewUserName" class="custom-text-input" 
                 placeholder="Enter cluster username (e.g. cluster_user)" required />
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
    </div>

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

    <!-- Actions -->
    <div class="d-flex justify-content-center align-items-center gap-3 mt-4 pt-3 border-top flex-wrap">
      <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill"></i> Save Cluster
      </button>
      <a href="<?php echo base_url('user/user_registration/cluster'); ?>" class="btn btn-light border px-4 py-2 rounded-pill fw-semibold">
        <i class="bi bi-x-circle me-1"></i> Cancel
      </a>
    </div>

    <input type="hidden" name="hidUserId" id="hidUserId" value="" />
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
  var form = document.getElementById('formCluster');
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
