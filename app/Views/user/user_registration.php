<?php
/**
 * User Registration View - Sub-District Admin Users
 * Ultra-Modern UI with Responsive Data Table, Quick Status Filters, Live Search, and Rights Matrix
 */
$has_selected = (!empty($selected_user) && count($selected_user) > 0);
$selected_user_id = $has_selected ? ($selected_user[0]['user_id'] ?? '') : '';
$selected_username = $has_selected ? ($selected_user[0]['user_name'] ?? '') : '';

// Flatten assigned user rights IDs for clean lookup
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

// Stats calculation
$total_users = count($retvalue ?? []);
$active_users = 0;
$pending_pwd_users = 0;
if (!empty($retvalue)) {
    foreach ($retvalue as $u) {
        if (($u['is_change_password'] ?? '') === 'N') {
            $pending_pwd_users++;
        } else {
            $active_users++;
        }
    }
}
?>

<style>
/* Modern Design Variables */
:root {
  --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
  --card-bg: #ffffff;
  --bg-subtle: #f8fafc;
  --card-border: #e2e8f0;
  --text-main: #0f172a;
  --text-muted: #64748b;
  --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.03);
  --radius-lg: 16px;
  --radius-md: 12px;
}

.user-module-wrapper {
  padding: 10px 0 40px;
  color: var(--text-main);
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Glass Header Card */
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

.module-title i { color: #4f46e5; font-size: 1.6rem; }
.module-subtitle { color: var(--text-muted); font-size: 0.88rem; margin-top: 4px; margin-bottom: 0; }

/* Nav Pills */
.nav-actions-group {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

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
  color: #ffffff;
}

.btn-outline-action {
  background: #f8fafc;
  color: #475569 !important;
  border-color: #cbd5e1;
}

.btn-outline-action:hover {
  background: #f1f5f9;
  color: #1e293b !important;
  border-color: #94a3b8;
}

/* Stats Cards */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.stat-item-card {
  background: var(--card-bg);
  border-radius: var(--radius-md);
  padding: 18px 22px;
  border: 1px solid var(--card-border);
  box-shadow: var(--shadow-sm);
  display: flex;
  align-items: center;
  gap: 16px;
  transition: all 0.2s ease;
  cursor: pointer;
}

.stat-item-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
  border-color: #cbd5e1;
}

.stat-icon-box {
  width: 50px;
  height: 50px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  flex-shrink: 0;
}

.stat-icon-indigo { background: #e0e7ff; color: #4338ca; }
.stat-icon-emerald { background: #d1fae5; color: #047857; }
.stat-icon-amber   { background: #fef3c7; color: #b45309; }

.stat-content-val {
  font-size: 1.45rem;
  font-weight: 700;
  color: var(--text-main);
  line-height: 1.1;
}

.stat-content-lbl {
  font-size: 0.82rem;
  font-weight: 500;
  color: var(--text-muted);
  margin-top: 4px;
}

/* Main Content Card (Table Wrapper) */
.content-box-card {
  background: var(--card-bg);
  border-radius: var(--radius-lg);
  border: 1px solid var(--card-border);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}

.content-box-header {
  padding: 20px 24px;
  background: #ffffff;
  border-bottom: 1px solid var(--card-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
}

.header-title-box {
  display: flex;
  align-items: center;
  gap: 12px;
}

.header-title-text {
  font-size: 1.1rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.header-badge-count {
  background: #eef2ff;
  color: #4338ca;
  border: 1px solid #c7d2fe;
  font-size: 0.78rem;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 50px;
}

/* Filter Pills Bar */
.filter-tabs-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.filter-tab-btn {
  padding: 6px 14px;
  border-radius: 50px;
  font-size: 0.8rem;
  font-weight: 600;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  color: #64748b;
  cursor: pointer;
  transition: all 0.15s ease;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.filter-tab-btn:hover {
  background: #f1f5f9;
  color: #1e293b;
}

.filter-tab-btn.active {
  background: #4f46e5;
  color: #ffffff;
  border-color: #4f46e5;
  box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
}

.filter-tab-btn .tab-count {
  background: rgba(0, 0, 0, 0.08);
  padding: 1px 7px;
  border-radius: 50px;
  font-size: 0.72rem;
}

.filter-tab-btn.active .tab-count {
  background: rgba(255, 255, 255, 0.25);
  color: #ffffff;
}

/* Search Box */
.search-input-wrap {
  position: relative;
  min-width: 260px;
  flex-grow: 1;
  max-width: 320px;
}

.search-input-wrap i.search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 0.95rem;
}

.search-input-wrap input {
  width: 100%;
  padding: 9px 36px 9px 38px;
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

.search-clear-btn {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  background: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 0;
  display: none;
  font-size: 0.9rem;
}

.search-clear-btn:hover { color: #475569; }

/* Modern Table */
.table-responsive-custom {
  width: 100%;
  overflow-x: auto;
}

.modern-data-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
}

.modern-data-table th {
  background: #f8fafc;
  color: #475569;
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  padding: 14px 20px;
  border-bottom: 1px solid var(--card-border);
  border-top: 1px solid #f1f5f9;
  white-space: nowrap;
}

.modern-data-table td {
  padding: 16px 20px;
  vertical-align: middle;
  border-bottom: 1px solid #f1f5f9;
  font-size: 0.88rem;
  color: #334155;
  transition: background 0.15s ease;
}

.modern-data-table tbody tr:hover td {
  background: #f8fafc;
}

.modern-data-table tbody tr:last-child td {
  border-bottom: none;
}

/* User Avatar & Info */
.user-cell-wrap {
  display: flex;
  align-items: center;
  gap: 14px;
}

.user-avatar-circle {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.9rem;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
  flex-shrink: 0;
}

.user-name-text {
  font-weight: 600;
  color: #0f172a;
  font-size: 0.94rem;
  line-height: 1.3;
}

.user-role-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 0.75rem;
  color: #64748b;
  margin-top: 2px;
}

/* Rights Badge */
.rights-count-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  border-radius: 50px;
  font-size: 0.78rem;
  font-weight: 600;
  background: #f1f5f9;
  color: #334155;
  border: 1px solid #e2e8f0;
}

.rights-count-badge i {
  color: #4f46e5;
  font-size: 0.85rem;
}

/* Status Badges */
.status-pill {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 6px 14px;
  border-radius: 50px;
  font-size: 0.78rem;
  font-weight: 600;
  white-space: nowrap;
}

.status-pill-active {
  background: #ecfdf5;
  color: #047857;
  border: 1px solid #a7f3d0;
}

.status-indicator-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  display: inline-block;
}

.status-indicator-dot.active {
  background: #10b981;
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
}

.status-pill-pending {
  background: #fffbeb;
  color: #b45309;
  border: 1px solid #fde68a;
}

/* Action Buttons */
.btn-table-action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 10px;
  border: 1px solid transparent;
  transition: all 0.15s ease;
  font-size: 0.95rem;
  text-decoration: none;
  cursor: pointer;
}

.btn-action-edit {
  background: #e0e7ff;
  color: #4338ca;
  border-color: #c7d2fe;
}

.btn-action-edit:hover {
  background: #4f46e5;
  color: #ffffff;
  transform: translateY(-2px);
  box-shadow: 0 3px 8px rgba(79, 70, 229, 0.3);
}

.btn-action-delete {
  background: #fee2e2;
  color: #b91c1c;
  border-color: #fecaca;
}

.btn-action-delete:hover {
  background: #dc2626;
  color: #ffffff;
  transform: translateY(-2px);
  box-shadow: 0 3px 8px rgba(220, 38, 38, 0.3);
}

/* Table Footer Card */
.content-box-footer {
  padding: 14px 24px;
  background: #f8fafc;
  border-top: 1px solid var(--card-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.82rem;
  color: var(--text-muted);
}

/* Empty State */
.empty-records-box {
  padding: 48px 24px;
  text-align: center;
}

.empty-records-icon {
  font-size: 3.5rem;
  color: #cbd5e1;
  margin-bottom: 12px;
}

.empty-records-title {
  font-weight: 700;
  font-size: 1.1rem;
  color: #475569;
}

.empty-records-desc {
  font-size: 0.85rem;
  color: #94a3b8;
  margin-top: 4px;
}

/* Form Styles */
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

.toggle-pwd-btn:hover {
  color: #475569;
}

/* Category Permissions Grouping */
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
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
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

/* Mobile responsive adjustments */
@media (max-width: 768px) {
  .module-header-card {
    padding: 18px;
  }
  
  .content-box-header {
    padding: 14px 16px;
  }

  .permission-items-grid {
    grid-template-columns: 1fr;
  }

  .search-input-wrap {
    max-width: 100%;
  }

  .filter-tabs-bar {
    width: 100%;
    overflow-x: auto;
    padding-bottom: 4px;
  }
}
</style>

<div class="user-module-wrapper">

  <!-- Header Card -->
  <div class="module-header-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h1 class="module-title">
          <i class="bi bi-people-fill"></i> User Registration
        </h1>
        <p class="module-subtitle">Manage Sub-District Admin accounts, login credentials, and authorized module permissions</p>
      </div>

      <div class="nav-actions-group">
        <a href="javascript:void(0)" onclick="showUserForm('ADD');" class="btn-nav-action btn-primary-action">
          <i class="bi bi-person-plus-fill"></i> Add Sub-District User
        </a>
        <a href="<?php echo base_url('user/user_cluster'); ?>" class="btn-nav-action btn-outline-action" title="Switch to Cluster Registration">
          <i class="bi bi-diagram-3-fill text-primary"></i> Cluster Registration
        </a>
      </div>
    </div>
  </div>

  <!-- Summary Stats -->
  <div class="stats-grid">
    <div class="stat-item-card" onclick="filterByStatus('all');">
      <div class="stat-icon-box stat-icon-indigo">
        <i class="bi bi-person-lines-fill"></i>
      </div>
      <div>
        <div class="stat-content-val" id="totalUsersCount"><?php echo $total_users; ?></div>
        <div class="stat-content-lbl">Total Registered Users</div>
      </div>
    </div>

    <div class="stat-item-card" onclick="filterByStatus('active');">
      <div class="stat-icon-box stat-icon-emerald">
        <i class="bi bi-shield-check"></i>
      </div>
      <div>
        <div class="stat-content-val"><?php echo $active_users; ?></div>
        <div class="stat-content-lbl">Active Accounts</div>
      </div>
    </div>

    <div class="stat-item-card" onclick="filterByStatus('pending');">
      <div class="stat-icon-box stat-icon-amber">
        <i class="bi bi-key-fill"></i>
      </div>
      <div>
        <div class="stat-content-val"><?php echo $pending_pwd_users; ?></div>
        <div class="stat-content-lbl">Initial Password Pending</div>
      </div>
    </div>
  </div>

  <!-- User Creation / Edit Form (Hidden by default, opened on Add or Edit) -->
  <div id="userFormSection" style="<?php echo $has_selected ? 'display:block;' : 'display:none;'; ?>">
    <div class="form-section-card">
      <?php echo form_open('user/user_registration/' . ($has_selected ? 'update_user_detials' : 'userinsert'), ['id' => 'formUser']); ?>
      
      <div class="form-header-bar">
        <div>
          <h2 class="form-title" id="formHeaderTitle">
            <i class="bi <?php echo $has_selected ? 'bi-pencil-square text-warning' : 'bi-person-plus text-primary'; ?>"></i>
            <?php echo $has_selected ? ('Edit User: ' . esc($selected_username)) : 'Create Sub-District Admin User'; ?>
          </h2>
          <p class="text-muted small mb-0 mt-1">Configure account credentials and authorized functional permissions.</p>
        </div>

        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="showUsersList();">
          <i class="bi bi-arrow-left me-1"></i> Back to User List
        </button>
      </div>

      <!-- User Basic Credentials -->
      <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
          <label class="custom-form-label" for="txtNewUserName">
            User Name <span class="text-danger">*</span>
          </label>
          <div class="input-with-icon">
            <i class="bi bi-person input-icon"></i>
            <input type="text" name="txtNewUserName" id="txtNewUserName" class="custom-text-input" 
                   placeholder="Enter username (e.g. subdist_user)" 
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
                   placeholder="<?php echo $has_selected ? '••••••••' : 'Enter new secure password'; ?>" />
            <button type="button" class="toggle-pwd-btn" onclick="togglePasswordVisibility('txtNewPassword', 'pwdToggleIcon')">
              <i class="bi bi-eye" id="pwdToggleIcon"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Permissions / User Rights Header -->
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
      <?php } else { ?>
        <div class="alert alert-info py-2 small">No customizable module permissions available.</div>
      <?php } ?>

      <!-- Form Action Buttons -->
      <div class="d-flex justify-content-center align-items-center gap-3 mt-4 pt-3 border-top flex-wrap">
        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-2" id="btnSubmitUser">
          <i class="bi bi-check-circle-fill"></i> <?php echo $has_selected ? 'Update User' : 'Save User'; ?>
        </button>
        <button type="button" class="btn btn-light border px-4 py-2 rounded-pill fw-semibold" onclick="showUsersList();">
          <i class="bi bi-x-circle me-1"></i> Cancel
        </button>
      </div>

      <input type="hidden" name="hidUserId" id="hidUserId" value="<?php echo esc($selected_user_id); ?>" />
      <?php echo form_close(); ?>
    </div>
  </div>

  <!-- Users List Section (Existing Sub-District Admin Users Table) -->
  <div id="usersListSection" style="<?php echo $has_selected ? 'display:none;' : 'display:block;'; ?>">
    <div class="content-box-card">
      
      <!-- Table Header Bar with Filters & Live Search -->
      <div class="content-box-header">
        <div class="header-title-box">
          <h3 class="header-title-text">
            <i class="bi bi-table text-primary"></i> Existing Sub-District Admin Users
          </h3>
          <span class="header-badge-count" id="displayedCountBadge">
            <?php echo $total_users; ?> Users
          </span>
        </div>

        <!-- Quick Status Filter Tabs -->
        <div class="filter-tabs-bar">
          <button type="button" class="filter-tab-btn active" id="tabAll" onclick="filterByStatus('all');">
            All Users <span class="tab-count"><?php echo $total_users; ?></span>
          </button>
          <button type="button" class="filter-tab-btn" id="tabActive" onclick="filterByStatus('active');">
            Active <span class="tab-count"><?php echo $active_users; ?></span>
          </button>
          <button type="button" class="filter-tab-btn" id="tabPending" onclick="filterByStatus('pending');">
            Pending Password <span class="tab-count"><?php echo $pending_pwd_users; ?></span>
          </button>
        </div>

        <!-- Live Search Input -->
        <div class="search-input-wrap">
          <i class="bi bi-search search-icon"></i>
          <input type="text" id="userSearchInput" placeholder="Search user name..." oninput="handleSearchInput();" />
          <button type="button" class="search-clear-btn" id="searchClearBtn" onclick="clearSearch();" title="Clear Search">
            <i class="bi bi-x-circle-fill"></i>
          </button>
        </div>
      </div>

      <?php if (!empty($retvalue)) { ?>
        <div class="table-responsive-custom">
          <table class="modern-data-table" id="userTable">
            <thead>
              <tr>
                <th width="7%" class="text-center">Sl.No</th>
                <th width="38%">User Name / Account</th>
                <th width="20%">Permissions</th>
                <th width="20%">Account Status</th>
                <th width="7.5%" class="text-center">Edit</th>
                <th width="7.5%" class="text-center">Delete</th>
              </tr>
            </thead>
            <tbody id="userTableBody">
              <?php 
              $i = 1; 
              foreach ($retvalue as $val) { 
                $uid = $val['user_id'];
                $uname = $val['user_name'];
                $is_pending_pwd = (($val['is_change_password'] ?? '') === 'N');
                $status_key = $is_pending_pwd ? 'pending' : 'active';
                $initial = !empty($uname) ? strtoupper(substr($uname, 0, 2)) : 'US';
                $rights_cnt = isset($val['rights_count']) ? (int)$val['rights_count'] : 0;
              ?>
                <tr class="user-row-item" data-username="<?php echo strtolower(esc($uname)); ?>" data-status="<?php echo $status_key; ?>">
                  <td class="text-center">
                    <span class="badge bg-light text-secondary border px-2 py-1 rounded fw-bold">
                      <?php echo str_pad((string)$i, 2, '0', STR_PAD_LEFT); ?>
                    </span>
                  </td>
                  <td>
                    <div class="user-cell-wrap">
                      <div class="user-avatar-circle"><?php echo esc($initial); ?></div>
                      <div>
                        <div class="user-name-text"><?php echo esc($uname); ?></div>
                        <div class="user-role-badge">
                          <i class="bi bi-shield-check text-primary"></i> Sub-District Admin
                        </div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="rights-count-badge">
                      <i class="bi bi-key-fill"></i>
                      <span><?php echo ($rights_cnt > 0) ? ($rights_cnt . ' Rights') : 'Custom Rights'; ?></span>
                    </span>
                  </td>
                  <td>
                    <?php if ($is_pending_pwd) { ?>
                      <span class="status-pill status-pill-pending">
                        <i class="bi bi-exclamation-triangle-fill"></i> Initial Password
                      </span>
                    <?php } else { ?>
                      <span class="status-pill status-pill-active">
                        <span class="status-indicator-dot active"></span> Active
                      </span>
                    <?php } ?>
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn-table-action btn-action-edit" title="Edit User & Permissions" onclick="triggerEditUser('<?php echo esc($uid); ?>');">
                      <i class="bi bi-pencil-square"></i>
                    </button>
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn-table-action btn-action-delete" title="Delete User" onclick="triggerDeleteUser('<?php echo esc($uid); ?>', '<?php echo esc(addslashes($uname)); ?>');">
                      <i class="bi bi-trash3-fill"></i>
                    </button>
                  </td>
                </tr>
              <?php $i++; } ?>
            </tbody>
          </table>
        </div>

        <!-- Dynamic No Search Results Box (Initially hidden) -->
        <div class="empty-records-box" id="noSearchResultsBox" style="display:none;">
          <div class="empty-records-icon"><i class="bi bi-search"></i></div>
          <div class="empty-records-title">No Matching Sub-District Users Found</div>
          <div class="empty-records-desc">Try adjusting your search keyword or selected filter status.</div>
          <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-3" onclick="clearSearch();">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Search Filter
          </button>
        </div>

        <!-- Table Footer -->
        <div class="content-box-footer">
          <div>
            Showing <b id="visibleCountText"><?php echo $total_users; ?></b> of <b><?php echo $total_users; ?></b> users
          </div>
          <div>
            <i class="bi bi-info-circle text-primary me-1"></i> Click on the edit icon to modify account privileges.
          </div>
        </div>

      <?php } else { ?>
        <div class="empty-records-box">
          <div class="empty-records-icon"><i class="bi bi-people"></i></div>
          <div class="empty-records-title">No Sub-District Admin Users Found</div>
          <div class="empty-records-desc">Click the "Add Sub-District User" button above to create a new user account.</div>
        </div>
      <?php } ?>
    </div>
  </div>

</div>

<!-- Hidden Action Forms -->
<form id="editUserForm" method="POST" action="<?php echo base_url('user/user_registration/edit_user_detials'); ?>" style="display:none;">
  <input type="hidden" name="UserIdty" id="editUserId" value="" />
</form>

<form id="deleteUserForm" method="POST" action="<?php echo base_url('user/user_registration/delete_user_detials'); ?>" style="display:none;">
  <input type="hidden" name="UserIdty" id="deleteUserId" value="" />
</form>

<script>
var currentStatusFilter = 'all';

// Toggle Add/Edit Form vs List Section
function showUserForm(mode) {
  if (mode === 'ADD') {
    document.getElementById('formUser').action = '<?php echo base_url("user/user_registration/userinsert"); ?>';
    document.getElementById('hidUserId').value = '';
    document.getElementById('txtNewUserName').value = '';
    document.getElementById('txtNewPassword').value = '';
    document.getElementById('formHeaderTitle').innerHTML = '<i class="bi bi-person-plus text-primary"></i> Create Sub-District Admin User';
    document.getElementById('btnSubmitUser').innerHTML = '<i class="bi bi-check-circle-fill"></i> Save User';
    toggleAllPermissions(false);
  }
  document.getElementById('usersListSection').style.display = 'none';
  document.getElementById('userFormSection').style.display = 'block';
  document.getElementById('userFormSection').scrollIntoView({ behavior: 'smooth' });
}

function showUsersList() {
  document.getElementById('userFormSection').style.display = 'none';
  document.getElementById('usersListSection').style.display = 'block';
  document.getElementById('usersListSection').scrollIntoView({ behavior: 'smooth' });
}

// Password toggle helper
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

// Permissions Master Helpers
function toggleAllPermissions(checked) {
  var checkboxes = document.querySelectorAll('.perm-checkbox');
  checkboxes.forEach(function(cb) {
    cb.checked = checked;
  });
  var masterChecks = document.querySelectorAll('.category-master-check');
  masterChecks.forEach(function(mc) {
    mc.checked = checked;
  });
}

function toggleCategoryGroup(catSlug, checked) {
  var groupCheckboxes = document.querySelectorAll('.group-' + catSlug);
  groupCheckboxes.forEach(function(cb) {
    cb.checked = checked;
  });
}

function syncCategoryHeader(catSlug) {
  var groupCheckboxes = document.querySelectorAll('.group-' + catSlug);
  var total = groupCheckboxes.length;
  var checkedCount = 0;
  groupCheckboxes.forEach(function(cb) {
    if (cb.checked) checkedCount++;
  });
  var catHeader = document.getElementById('checkCat_' + catSlug);
  if (catHeader) {
    catHeader.checked = (total > 0 && checkedCount === total);
  }
}

// Filter by Status (Tabs)
function filterByStatus(statusKey) {
  currentStatusFilter = statusKey;

  // Update tab UI active states
  var tabs = document.querySelectorAll('.filter-tab-btn');
  tabs.forEach(function(tab) { tab.classList.remove('active'); });

  if (statusKey === 'all') {
    var el = document.getElementById('tabAll');
    if (el) el.classList.add('active');
  } else if (statusKey === 'active') {
    var el = document.getElementById('tabActive');
    if (el) el.classList.add('active');
  } else if (statusKey === 'pending') {
    var el = document.getElementById('tabPending');
    if (el) el.classList.add('active');
  }

  applyCombinedFilter();
}

// Live Search & Combined Filter Logic
function handleSearchInput() {
  var query = document.getElementById('userSearchInput').value.trim();
  var clearBtn = document.getElementById('searchClearBtn');
  if (clearBtn) {
    clearBtn.style.display = (query.length > 0) ? 'block' : 'none';
  }
  applyCombinedFilter();
}

function clearSearch() {
  var input = document.getElementById('userSearchInput');
  if (input) {
    input.value = '';
  }
  var clearBtn = document.getElementById('searchClearBtn');
  if (clearBtn) {
    clearBtn.style.display = 'none';
  }
  currentStatusFilter = 'all';
  var tabs = document.querySelectorAll('.filter-tab-btn');
  tabs.forEach(function(tab) { tab.classList.remove('active'); });
  var tabAll = document.getElementById('tabAll');
  if (tabAll) tabAll.classList.add('active');

  applyCombinedFilter();
}

function applyCombinedFilter() {
  var query = (document.getElementById('userSearchInput')?.value || '').toLowerCase().trim();
  var rows = document.querySelectorAll('.user-row-item');
  var visibleCount = 0;

  rows.forEach(function(row) {
    var username = row.getAttribute('data-username') || '';
    var status = row.getAttribute('data-status') || '';

    var matchesSearch = (username.indexOf(query) !== -1);
    var matchesStatus = (currentStatusFilter === 'all' || status === currentStatusFilter);

    if (matchesSearch && matchesStatus) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  // Toggle table vs empty results
  var tableEl = document.getElementById('userTable');
  var noResultsBox = document.getElementById('noSearchResultsBox');
  if (tableEl && noResultsBox) {
    if (visibleCount === 0 && rows.length > 0) {
      tableEl.style.display = 'none';
      noResultsBox.style.display = 'block';
    } else {
      tableEl.style.display = '';
      noResultsBox.style.display = 'none';
    }
  }

  // Update counters
  var badge = document.getElementById('displayedCountBadge');
  if (badge) {
    badge.innerText = visibleCount + ' Users';
  }
  var countText = document.getElementById('visibleCountText');
  if (countText) {
    countText.innerText = visibleCount;
  }
}

// Action Handlers
function triggerEditUser(userId) {
  document.getElementById('editUserId').value = userId;
  document.getElementById('editUserForm').submit();
}

function triggerDeleteUser(userId, userName) {
  Swal.fire({
    title: 'Delete User Confirmation',
    html: 'Are you sure you want to delete user <b>' + userName + '</b>?<br><span class="text-danger small">This action cannot be undone.</span>',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#64748b',
    confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> Yes, Delete User',
    cancelButtonText: 'Cancel'
  }).then(function(result) {
    if (result.isConfirmed) {
      document.getElementById('deleteUserId').value = userId;
      document.getElementById('deleteUserForm').submit();
    }
  });
}

// Form Validation on Submit
document.addEventListener('DOMContentLoaded', function() {
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
        Swal.fire('Required Field', 'Please enter a Password for the new user.', 'warning');
        return false;
      }
    });
  }

  // Initial synchronization of category headers
  var categoryHeaders = document.querySelectorAll('.category-master-check');
  categoryHeaders.forEach(function(ch) {
    var id = ch.id.replace('checkCat_', '');
    syncCategoryHeader(id);
  });
});
</script>
