<?php
/**
 * Cluster Registration View - Cluster Admin Users
 */
$has_selected = (!empty($selected_user) && count($selected_user) > 0);
$selected_user_id = $has_selected ? ($selected_user[0]['user_id'] ?? '') : '';
$selected_username = $has_selected ? ($selected_user[0]['user_name'] ?? '') : '';

// Flatten assigned user rights IDs
$assigned_rights_ids = [];
if (!empty($selected_user_rights)) {
    foreach ($selected_user_rights as $r) {
        if (is_array($r) && isset($r['rf_id'])) {
            $assigned_rights_ids[] = (int)$r['rf_id'];
        } else if (is_numeric($r)) {
            $assigned_rights_ids[] = (int)$r;
        }
    }
}

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

$total_clusters = count($retvalue ?? []);
?>

<style>
:root {
  --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
  --accent-purple: #7c3aed;
  --bg-subtle: #f8fafc;
  --card-border: #e2e8f0;
  --text-main: #0f172a;
  --text-muted: #64748b;
  --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.07);
  --radius-lg: 16px;
  --radius-md: 12px;
}

.user-module-wrapper {
  padding: 10px 0 40px;
  color: var(--text-main);
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.module-header-card {
  background: #ffffff;
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

.module-title i { color: #4f46e5; font-size: 1.6rem; }
.module-subtitle { color: var(--text-muted); font-size: 0.88rem; margin-top: 4px; margin-bottom: 0; }

.nav-actions-group { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

.btn-nav-action {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 18px;
  border-radius: 50px;
  font-size: 0.875rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;
  border: 1px solid transparent;
}

.btn-primary-action {
  background: var(--primary-gradient);
  color: #ffffff !important;
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
}

.btn-primary-action:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
}

.btn-outline-action {
  background: #f8fafc;
  color: #475569 !important;
  border-color: #cbd5e1;
}

.btn-outline-action:hover {
  background: #f1f5f9;
  color: #1e293b !important;
}

.content-box-card {
  background: #ffffff;
  border-radius: var(--radius-lg);
  border: 1px solid var(--card-border);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}

.content-box-header {
  padding: 18px 24px;
  background: #ffffff;
  border-bottom: 1px solid var(--card-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}

.search-input-wrap {
  position: relative;
  min-width: 260px;
  flex-grow: 1;
  max-width: 380px;
}

.search-input-wrap i {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
}

.search-input-wrap input {
  width: 100%;
  padding: 8px 14px 8px 38px;
  border-radius: 50px;
  border: 1px solid #cbd5e1;
  font-size: 0.875rem;
  background: #f8fafc;
  outline: none;
  transition: all 0.2s;
}

.search-input-wrap input:focus {
  border-color: #6366f1;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}

.table-responsive-custom { width: 100%; overflow-x: auto; }

.modern-data-table { width: 100%; border-collapse: separate; border-spacing: 0; }

.modern-data-table th {
  background: #f8fafc;
  color: #475569;
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 14px 20px;
  border-bottom: 1px solid var(--card-border);
  white-space: nowrap;
}

.modern-data-table td {
  padding: 16px 20px;
  vertical-align: middle;
  border-bottom: 1px solid #f1f5f9;
  font-size: 0.88rem;
  color: #334155;
}

.modern-data-table tbody tr:hover td { background: #f8fafc; }
.modern-data-table tbody tr:last-child td { border-bottom: none; }

.sl-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border-radius: 8px;
  background: #f1f5f9;
  color: #475569;
  font-size: 0.75rem;
  font-weight: 700;
}

.user-avatar-group { display: flex; align-items: center; gap: 12px; }

.user-avatar-icon {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.85rem;
  text-transform: uppercase;
}

.user-title-text { font-weight: 600; color: var(--text-main); font-size: 0.92rem; }
.user-role-subtext { font-size: 0.75rem; color: var(--text-muted); }

.school-count-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  border-radius: 50px;
  font-size: 0.78rem;
  font-weight: 600;
  background: #e0f2fe;
  color: #0369a1;
  border: 1px solid #bae6fd;
}

.btn-table-action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  border-radius: 10px;
  border: 1px solid transparent;
  transition: all 0.15s ease;
  font-size: 0.95rem;
  cursor: pointer;
}

.btn-action-edit { background: #e0e7ff; color: #4338ca; border-color: #c7d2fe; }
.btn-action-edit:hover { background: #4f46e5; color: #ffffff; transform: translateY(-1px); }

.btn-action-delete { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }
.btn-action-delete:hover { background: #dc2626; color: #ffffff; transform: translateY(-1px); }

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

.form-title { font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px; }

.custom-form-label { font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px; display: block; }
.input-with-icon { position: relative; }
.input-with-icon .input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.05rem; }

.custom-text-input {
  width: 100%;
  padding: 10px 14px 10px 42px;
  border-radius: var(--radius-md);
  border: 1px solid #cbd5e1;
  font-size: 0.9rem;
  color: #1e293b;
  transition: all 0.2s;
}

.custom-text-input:focus { border-color: #6366f1; background: #ffffff; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15); outline: none; }

.toggle-pwd-btn {
  position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
  background: none; border: none; color: #94a3b8; cursor: pointer; padding: 4px; font-size: 1rem;
}

.permission-category-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: var(--radius-md); margin-bottom: 16px; overflow: hidden; }
.permission-category-header { background: #f1f5f9; padding: 12px 18px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e2e8f0; }
.category-title-wrap { display: flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 700; color: #1e293b; }
.category-title-wrap i { color: #4f46e5; font-size: 1.1rem; }
.permission-items-grid { padding: 16px 18px; display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; }

.permission-check-item {
  display: flex; align-items: center; gap: 10px; background: #ffffff; padding: 10px 14px;
  border-radius: 10px; border: 1px solid #e2e8f0; cursor: pointer; transition: all 0.15s ease;
}
.permission-check-item:hover { border-color: #c7d2fe; background: #fdfefe; }
.permission-check-item input[type="checkbox"] { width: 17px; height: 17px; accent-color: #4f46e5; cursor: pointer; }
.permission-check-label { font-size: 0.85rem; color: #334155; font-weight: 500; cursor: pointer; user-select: none; flex-grow: 1; }

.perm-controls-bar {
  display: flex; align-items: center; justify-content: space-between; background: #eef2ff;
  border: 1px solid #c7d2fe; border-radius: var(--radius-md); padding: 12px 18px; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;
}

.empty-records-box { padding: 48px 24px; text-align: center; }
.empty-records-icon { font-size: 3.5rem; color: #cbd5e1; margin-bottom: 12px; }
.empty-records-title { font-weight: 700; font-size: 1.1rem; color: #475569; }
.empty-records-desc { font-size: 0.85rem; color: #94a3b8; }
</style>

<div class="user-module-wrapper">

  <!-- Header Card -->
  <div class="module-header-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h1 class="module-title">
          <i class="bi bi-diagram-3-fill"></i> Cluster Registration
        </h1>
        <p class="module-subtitle">Manage Cluster Admin users, school assignments, and access privileges</p>
      </div>

      <div class="nav-actions-group">
        <a href="javascript:void(0)" onclick="showClusterForm('ADD');" class="btn-nav-action btn-primary-action">
          <i class="bi bi-plus-circle-fill"></i> Add Cluster User
        </a>
        <a href="<?php echo base_url('user/user_registration'); ?>" class="btn-nav-action btn-outline-action" title="Switch to Sub-District User Registration">
          <i class="bi bi-people-fill text-primary"></i> Sub-District Registration
        </a>
      </div>
    </div>
  </div>

  <!-- Form Section (Hidden by default) -->
  <div id="clusterFormSection" style="<?php echo $has_selected ? 'display:block;' : 'display:none;'; ?>">
    <div class="form-section-card">
      <?php echo form_open('user/user_registration/' . ($has_selected ? 'update_cluster_details' : 'clusterinsert'), ['id' => 'formCluster']); ?>
      
      <div class="form-header-bar">
        <div>
          <h2 class="form-title" id="formHeaderTitle">
            <i class="bi <?php echo $has_selected ? 'bi-pencil-square text-warning' : 'bi-person-plus text-primary'; ?>"></i>
            <?php echo $has_selected ? ('Edit Cluster User: ' . esc($selected_username)) : 'Create Cluster User'; ?>
          </h2>
          <p class="text-muted small mb-0 mt-1">Configure account username, password credentials, and cluster permissions.</p>
        </div>

        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="showClusterList();">
          <i class="bi bi-arrow-left me-1"></i> Back to Cluster List
        </button>
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
                   placeholder="Enter cluster username (e.g. cluster_user)" 
                   value="<?php echo esc($selected_username); ?>" required />
          </div>
        </div>

        <div class="col-12 col-md-6">
          <label class="custom-form-label" for="txtNewPassword">
            Password <?php echo $has_selected ? '<span class="text-muted fw-normal">(Leave blank to keep existing)</span>' : '<span class="text-danger">*</span>'; ?>
          </label>
          <div class="input-with-icon">
            <i class="bi bi-lock input-icon"></i>
            <input type="password" name="txtNewPassword" id="txtNewPassword" class="custom-text-input" 
                   placeholder="<?php echo $has_selected ? '••••••••' : 'Enter secure password'; ?>" />
            <button type="button" class="toggle-pwd-btn" onclick="togglePasswordVisibility('txtNewPassword', 'pwdToggleIcon')">
              <i class="bi bi-eye" id="pwdToggleIcon"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Permissions -->
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

      <!-- Categorized Grid -->
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
                $is_checked = in_array($rf_id, $assigned_rights_ids);
              ?>
                <label class="permission-check-item" for="chkRight_<?php echo $rf_id; ?>">
                  <input type="checkbox" name="chkRight_<?php echo $rf_id; ?>" id="chkRight_<?php echo $rf_id; ?>" 
                         class="perm-checkbox group-<?php echo $cat_slug; ?>" 
                         value="<?php echo $rf_id; ?>" 
                         <?php echo $is_checked ? 'checked' : ''; ?> 
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
        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-2" id="btnSubmitCluster">
          <i class="bi bi-check-circle-fill"></i> <?php echo $has_selected ? 'Update Cluster' : 'Save Cluster'; ?>
        </button>
        <button type="button" class="btn btn-light border px-4 py-2 rounded-pill fw-semibold" onclick="showClusterList();">
          <i class="bi bi-x-circle me-1"></i> Cancel
        </button>
      </div>

      <input type="hidden" name="hidUserId" id="hidUserId" value="<?php echo esc($selected_user_id); ?>" />
      <?php echo form_close(); ?>
    </div>
  </div>

  <!-- Records List -->
  <div id="clusterListSection" style="<?php echo $has_selected ? 'display:none;' : 'display:block;'; ?>">
    <div class="content-box-card">
      <div class="content-box-header">
        <div class="d-flex align-items-center gap-2">
          <span class="fw-bold text-dark fs-6">
            <i class="bi bi-diagram-3-fill text-primary me-1"></i> Existing Cluster Users
          </span>
          <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill small" id="displayedCountBadge">
            <?php echo $total_clusters; ?> Users
          </span>
        </div>

        <div class="search-input-wrap">
          <i class="bi bi-search"></i>
          <input type="text" id="clusterSearchInput" placeholder="Search cluster user..." onkeyup="filterClusterList();" />
        </div>
      </div>

      <?php if (!empty($retvalue)) { ?>
        <div class="table-responsive-custom">
          <table class="modern-data-table" id="clusterTable">
            <thead>
              <tr>
                <th width="8%" class="text-center">Sl.No</th>
                <th width="35%">User Details</th>
                <th width="18%">User Type</th>
                <th width="25%">Cluster Schools</th>
                <th width="7%" class="text-center">Edit</th>
                <th width="7%" class="text-center">Delete</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $i = 1; 
              foreach ($retvalue as $val) { 
                $uid = $val['user_id'];
                $uname = $val['user_name'];
                $initial = !empty($uname) ? strtoupper(substr($uname, 0, 2)) : 'CL';
                $school_cnt = (int)($val['cluster_school_count'] ?? 0);
                $school_names = $val['cluster_school_names'] ?? '';
              ?>
                <tr class="cluster-row-item" data-username="<?php echo strtolower(esc($uname)); ?>">
                  <td class="text-center">
                    <span class="sl-badge"><?php echo str_pad((string)$i, 2, '0', STR_PAD_LEFT); ?></span>
                  </td>
                  <td>
                    <div class="user-avatar-group">
                      <div class="user-avatar-icon"><?php echo esc($initial); ?></div>
                      <div>
                        <div class="user-title-text"><?php echo esc($uname); ?></div>
                        <div class="user-role-subtext">Cluster Admin</div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border px-2 py-1">Cluster Admin</span>
                  </td>
                  <td>
                    <?php if ($school_cnt > 0) { ?>
                      <span class="school-count-badge" title="<?php echo esc($school_names); ?>">
                        <i class="bi bi-building"></i> <?php echo $school_cnt; ?> <?php echo ($school_cnt === 1 ? 'School' : 'Schools'); ?>
                      </span>
                    <?php } else { ?>
                      <span class="text-muted small">No schools assigned</span>
                    <?php } ?>
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn-table-action btn-action-edit" title="Edit Cluster" onclick="triggerEditCluster('<?php echo esc($uid); ?>');">
                      <i class="bi bi-pencil-square"></i>
                    </button>
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn-table-action btn-action-delete" title="Delete Cluster" onclick="triggerDeleteCluster('<?php echo esc($uid); ?>', '<?php echo esc(addslashes($uname)); ?>');">
                      <i class="bi bi-trash3-fill"></i>
                    </button>
                  </td>
                </tr>
              <?php $i++; } ?>
            </tbody>
          </table>
        </div>
      <?php } else { ?>
        <div class="empty-records-box">
          <div class="empty-records-icon"><i class="bi bi-diagram-3"></i></div>
          <div class="empty-records-title">No Cluster Users Found</div>
          <div class="empty-records-desc">Click the "Add Cluster User" button above to create a new cluster account.</div>
        </div>
      <?php } ?>
    </div>
  </div>

</div>

<!-- Hidden Action Forms -->
<form id="editClusterForm" method="POST" action="<?php echo base_url('user/user_registration/edit_cluster_details'); ?>" style="display:none;">
  <input type="hidden" name="UserIdty" id="editClusterUserId" value="" />
</form>

<form id="deleteClusterForm" method="POST" action="<?php echo base_url('user/user_registration/delete_cluster_details'); ?>" style="display:none;">
  <input type="hidden" name="UserIdty" id="deleteClusterUserId" value="" />
</form>

<script>
function showClusterForm(mode) {
  if (mode === 'ADD') {
    document.getElementById('formCluster').action = '<?php echo base_url("user/user_registration/clusterinsert"); ?>';
    document.getElementById('hidUserId').value = '';
    document.getElementById('txtNewUserName').value = '';
    document.getElementById('txtNewPassword').value = '';
    document.getElementById('formHeaderTitle').innerHTML = '<i class="bi bi-person-plus text-primary"></i> Create Cluster User';
    document.getElementById('btnSubmitCluster').innerHTML = '<i class="bi bi-check-circle-fill"></i> Save Cluster';
    toggleAllPermissions(false);
  }
  document.getElementById('clusterListSection').style.display = 'none';
  document.getElementById('clusterFormSection').style.display = 'block';
  document.getElementById('clusterFormSection').scrollIntoView({ behavior: 'smooth' });
}

function showClusterList() {
  document.getElementById('clusterFormSection').style.display = 'none';
  document.getElementById('clusterListSection').style.display = 'block';
  document.getElementById('clusterListSection').scrollIntoView({ behavior: 'smooth' });
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

function filterClusterList() {
  var query = document.getElementById('clusterSearchInput').value.toLowerCase().trim();
  var rows = document.querySelectorAll('.cluster-row-item');
  var visibleCount = 0;

  rows.forEach(function(row) {
    var username = row.getAttribute('data-username') || '';
    if (username.indexOf(query) !== -1) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  var badge = document.getElementById('displayedCountBadge');
  if (badge) {
    badge.innerText = visibleCount + ' Users';
  }
}

function triggerEditCluster(userId) {
  document.getElementById('editClusterUserId').value = userId;
  document.getElementById('editClusterForm').submit();
}

function triggerDeleteCluster(userId, userName) {
  Swal.fire({
    title: 'Delete Cluster User Confirmation',
    html: 'Are you sure you want to delete cluster user <b>' + userName + '</b>?<br><span class="text-danger small">This action cannot be undone.</span>',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#64748b',
    confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> Yes, Delete User',
    cancelButtonText: 'Cancel'
  }).then(function(result) {
    if (result.isConfirmed) {
      document.getElementById('deleteClusterUserId').value = userId;
      document.getElementById('deleteClusterForm').submit();
    }
  });
}

document.addEventListener('DOMContentLoaded', function() {
  var form = document.getElementById('formCluster');
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

  var categoryHeaders = document.querySelectorAll('.category-master-check');
  categoryHeaders.forEach(function(ch) {
    var id = ch.id.replace('checkCat_', '');
    syncCategoryHeader(id);
  });
});
</script>
