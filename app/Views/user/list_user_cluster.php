<?php
/**
 * Cluster Users List View - Modern, Colorful & Responsive
 */
$total_clusters = count($retvalue ?? []);
$total_assigned_schools = 0;
$active_clusters = 0;
$pending_pwd_clusters = 0;

if (!empty($retvalue)) {
    foreach ($retvalue as $val) {
        $total_assigned_schools += (int)($val['total'] ?? 0);
        if (($val['is_change_password'] ?? '') === 'N') {
            $pending_pwd_clusters++;
        } else {
            $active_clusters++;
        }
    }
}
?>

<style>
/* Modern Colorful Theme Variables */
:root {
  --primary-gradient: linear-gradient(135deg, #0284c7 0%, #6366f1 100%);
  --accent-cyan: #06b6d4;
  --accent-purple: #8b5cf6;
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

.cluster-module-wrapper {
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

.module-title i { color: #0284c7; font-size: 1.6rem; }
.module-subtitle { color: var(--text-muted); font-size: 0.88rem; margin-top: 4px; margin-bottom: 0; }

/* Nav Actions */
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
  box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
}

.btn-primary-action:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(2, 132, 199, 0.35);
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
.stat-icon-sky    { background: #e0f2fe; color: #0284c7; }
.stat-icon-emerald{ background: #d1fae5; color: #047857; }

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
  background: #e0f2fe;
  color: #0369a1;
  border: 1px solid #bae6fd;
  font-size: 0.78rem;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 50px;
}

/* Filter Tabs Bar */
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
  background: #0284c7;
  color: #ffffff;
  border-color: #0284c7;
  box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
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
  border-color: #0284c7;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
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
  background: linear-gradient(135deg, #0284c7 0%, #6366f1 100%);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.9rem;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
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

/* Assigned Schools Badge */
.schools-count-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 50px;
  font-size: 0.8rem;
  font-weight: 600;
  background: #e0f2fe;
  color: #0369a1;
  border: 1px solid #bae6fd;
  cursor: default;
}

.schools-count-badge i {
  color: #0284c7;
  font-size: 0.9rem;
}

.schools-empty-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  border-radius: 50px;
  font-size: 0.78rem;
  font-weight: 500;
  background: #f1f5f9;
  color: #94a3b8;
  border: 1px solid #e2e8f0;
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

/* Checkbox & Bulk Selection System */
.form-check-input {
  cursor: pointer;
  width: 1.15rem;
  height: 1.15rem;
  border: 1.5px solid #cbd5e1;
  border-radius: 4px;
  vertical-align: middle;
  transition: all 0.15s ease-in-out;
}
.form-check-input:checked {
  background-color: #dc2626;
  border-color: #dc2626;
}
.form-check-input:indeterminate {
  background-color: #dc2626;
  border-color: #dc2626;
}
.cluster-row-item.is-selected td {
  background-color: #fff1f2 !important;
}
.cluster-row-item.is-selected:hover td {
  background-color: #ffe4e6 !important;
}
.row-fade-out {
  opacity: 0;
  transform: translateX(-15px);
  transition: all 0.3s ease;
}

/* Floating Batch Actions Toolbar */
.floating-batch-bar {
  position: fixed;
  bottom: 24px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 1050;
  animation: slideUpBatch 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}
.floating-batch-content {
  background: #0f172a;
  color: #ffffff;
  border-radius: 50px;
  padding: 10px 22px;
  display: flex;
  align-items: center;
  gap: 18px;
  box-shadow: 0 10px 25px -3px rgba(15, 23, 42, 0.35), 0 4px 10px -4px rgba(15, 23, 42, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.12);
}
@keyframes slideUpBatch {
  from { transform: translate(-50%, 25px); opacity: 0; }
  to { transform: translate(-50%, 0); opacity: 1; }
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
  .module-header-card { padding: 18px; }
  .content-box-header { padding: 14px 16px; }
  .search-input-wrap { max-width: 100%; }
  .filter-tabs-bar { width: 100%; overflow-x: auto; padding-bottom: 4px; }
  .floating-batch-content { padding: 8px 16px; gap: 10px; font-size: 0.85rem; }
}
</style>

<div class="cluster-module-wrapper">

  <!-- Header Card -->
  <div class="module-header-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h1 class="module-title">
          <i class="bi bi-diagram-3-fill"></i> User Cluster Management
        </h1>
        <p class="module-subtitle">Assign school clusters to dedicated cluster admin accounts and manage access permissions</p>
      </div>

      <div class="nav-actions-group">
        <a href="<?php echo base_url('user/user_cluster/add_form'); ?>" class="btn-nav-action btn-primary-action">
          <i class="bi bi-plus-circle-fill"></i> Add Cluster School
        </a>
        <a href="<?php echo base_url('user/user_registration'); ?>" class="btn-nav-action btn-outline-action" title="Switch to Sub-District User Registration">
          <i class="bi bi-people-fill text-primary"></i> Sub-District Registration
        </a>
      </div>
    </div>
  </div>

  <!-- Summary Stats -->
  <div class="stats-grid">
    <div class="stat-item-card" onclick="filterBySchoolStatus('all');">
      <div class="stat-icon-box stat-icon-indigo">
        <i class="bi bi-person-lines-fill"></i>
      </div>
      <div>
        <div class="stat-content-val" id="totalClusterCount"><?php echo $total_clusters; ?></div>
        <div class="stat-content-lbl">Total Cluster Accounts</div>
      </div>
    </div>

    <div class="stat-item-card" onclick="filterBySchoolStatus('has_schools');">
      <div class="stat-icon-box stat-icon-sky">
        <i class="bi bi-building"></i>
      </div>
      <div>
        <div class="stat-content-val"><?php echo $total_assigned_schools; ?></div>
        <div class="stat-content-lbl">Assigned Schools Total</div>
      </div>
    </div>

    <div class="stat-item-card" onclick="filterBySchoolStatus('all');">
      <div class="stat-icon-box stat-icon-emerald">
        <i class="bi bi-shield-check"></i>
      </div>
      <div>
        <div class="stat-content-val"><?php echo $active_clusters; ?></div>
        <div class="stat-content-lbl">Active Cluster Logins</div>
      </div>
    </div>
  </div>

  <!-- Cluster List Card -->
  <div class="content-box-card">
    
    <!-- Table Header Bar -->
    <div class="content-box-header">
      <div class="header-title-box">
        <h3 class="header-title-text">
          <i class="bi bi-table text-primary"></i> Existing Cluster Users
        </h3>
        <span class="header-badge-count" id="displayedCountBadge">
          <?php echo $total_clusters; ?> Clusters
        </span>
      </div>

      <!-- Batch Actions Bar (Shown when rows are selected) -->
      <div class="batch-actions-wrap" id="batchActionsBar" style="display: none;">
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-bold d-inline-flex align-items-center gap-1">
            <i class="bi bi-check2-circle"></i> <span id="batchCountBadge">0</span> Selected
          </span>
          <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 py-1 shadow-sm d-inline-flex align-items-center gap-2 fw-semibold" onclick="confirmBulkDelete();">
            <i class="bi bi-trash3-fill"></i> Delete Selected (<span id="batchBtnCount">0</span>)
          </button>
          <button type="button" class="btn btn-sm btn-light border rounded-pill px-2 py-1 text-muted" onclick="clearClusterSelection();" title="Clear selection">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>

      <!-- Quick Filter Tabs -->
      <div class="filter-tabs-bar">
        <button type="button" class="filter-tab-btn active" id="tabAll" onclick="filterBySchoolStatus('all');">
          All Clusters <span class="tab-count"><?php echo $total_clusters; ?></span>
        </button>
        <button type="button" class="filter-tab-btn" id="tabWithSchools" onclick="filterBySchoolStatus('has_schools');">
          With Schools <span class="tab-count"><?php 
            $with_cnt = 0;
            foreach ($retvalue ?? [] as $r) { if ((int)($r['total'] ?? 0) > 0) $with_cnt++; }
            echo $with_cnt;
          ?></span>
        </button>
        <button type="button" class="filter-tab-btn" id="tabNoSchools" onclick="filterBySchoolStatus('no_schools');">
          No Schools <span class="tab-count"><?php 
            $no_cnt = 0;
            foreach ($retvalue ?? [] as $r) { if ((int)($r['total'] ?? 0) === 0) $no_cnt++; }
            echo $no_cnt;
          ?></span>
        </button>
      </div>

      <!-- Live Search Input -->
      <div class="search-input-wrap">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="clusterSearchInput" placeholder="Search user or school..." oninput="handleSearchInput();" />
        <button type="button" class="search-clear-btn" id="searchClearBtn" onclick="clearSearch();" title="Clear Search">
          <i class="bi bi-x-circle-fill"></i>
        </button>
      </div>
    </div>

    <?php if (!empty($retvalue)) { ?>
      <div class="table-responsive-custom">
        <table class="modern-data-table" id="clusterTable">
          <thead>
            <tr>
              <th width="4%" class="text-center" style="vertical-align: middle;">
                <input type="checkbox" id="selectAllClusters" class="form-check-input" title="Select All Visible Clusters" onchange="toggleSelectAllClusters(this.checked);" />
              </th>
              <th width="6%" class="text-center">Sl.No</th>
              <th width="32%">User Name / Account</th>
              <th width="32%">Assigned Schools</th>
              <th width="14%">Account Status</th>
              <th width="12%" class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody id="clusterTableBody">
            <?php 
            $i = 1; 
            foreach ($retvalue as $val) { 
              $uid = $val['user_id'];
              $uname = $val['user_name'];
              $school_count = (int)($val['total'] ?? 0);
              $school_names = !empty($val['school_names']) ? $val['school_names'] : '';
              $is_pending_pwd = (($val['is_change_password'] ?? '') === 'N');
              $initial = !empty($uname) ? strtoupper(substr($uname, 0, 2)) : 'CL';
              $school_status_key = ($school_count > 0) ? 'has_schools' : 'no_schools';
            ?>
              <tr class="cluster-row-item" 
                  id="clusterRow_<?php echo esc($uid); ?>"
                  data-userid="<?php echo esc($uid); ?>"
                  data-username="<?php echo strtolower(esc($uname)); ?>" 
                  data-display-name="<?php echo esc($uname); ?>"
                  data-school-count="<?php echo $school_count; ?>"
                  data-schools="<?php echo strtolower(esc($school_names)); ?>"
                  data-school-status="<?php echo $school_status_key; ?>">
                <td class="text-center" style="vertical-align: middle;">
                  <input type="checkbox" class="form-check-input cluster-row-check" value="<?php echo esc($uid); ?>" data-username="<?php echo esc($uname); ?>" onchange="handleRowCheckChange(this);" />
                </td>
                <td class="text-center">
                  <span class="badge bg-light text-secondary border px-2 py-1 rounded fw-bold row-index-badge">
                    <?php echo str_pad((string)$i, 2, '0', STR_PAD_LEFT); ?>
                  </span>
                </td>
                <td>
                  <div class="user-cell-wrap">
                    <div class="user-avatar-circle"><?php echo esc($initial); ?></div>
                    <div>
                      <div class="user-name-text"><?php echo esc($uname); ?></div>
                      <div class="user-role-badge">
                        <i class="bi bi-diagram-3 text-primary"></i> Cluster Admin
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <?php if ($school_count > 0) { ?>
                    <span class="schools-count-badge" title="<?php echo esc($school_names); ?>">
                      <i class="bi bi-buildings"></i>
                      <span><b><?php echo $school_count; ?></b> <?php echo ($school_count === 1 ? 'School' : 'Schools'); ?> Assigned</span>
                    </span>
                  <?php } else { ?>
                    <span class="schools-empty-badge">
                      <i class="bi bi-dash-circle"></i> No Schools Assigned
                    </span>
                  <?php } ?>
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
                  <div class="d-flex align-items-center justify-content-center gap-2">
                    <button type="button" class="btn-table-action btn-action-edit" title="Edit Cluster & Schools" onclick="editUser('<?php echo esc($uid); ?>');">
                      <i class="bi bi-pencil-square"></i>
                    </button>
                    <button type="button" class="btn-table-action btn-action-delete" title="Delete Cluster" onclick="deleteUser('<?php echo esc($uid); ?>', '<?php echo esc(addslashes($uname)); ?>');">
                      <i class="bi bi-trash3-fill"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php $i++; } ?>
          </tbody>
        </table>
      </div>

      <!-- Dynamic No Results Box -->
      <div class="empty-records-box" id="noSearchResultsBox" style="display:none;">
        <div class="empty-records-icon"><i class="bi bi-search"></i></div>
        <div class="empty-records-title">No Matching Cluster Accounts Found</div>
        <div class="empty-records-desc">Try modifying your search keywords or switching filters.</div>
        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-3" onclick="clearSearch();">
          <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Search Filter
        </button>
      </div>

      <!-- Table Footer -->
      <div class="content-box-footer">
        <div>
          Showing <b id="visibleCountText"><?php echo $total_clusters; ?></b> of <b><?php echo $total_clusters; ?></b> cluster accounts
        </div>
        <div>
          <i class="bi bi-info-circle text-primary me-1"></i> Click on the edit icon to reconfigure assigned cluster schools.
        </div>
      </div>

    <?php } else { ?>
      <div class="empty-records-box">
        <div class="empty-records-icon"><i class="bi bi-diagram-3"></i></div>
        <div class="empty-records-title">No Cluster Accounts Found</div>
        <div class="empty-records-desc">Click the "Add Cluster School" button above to register your first cluster.</div>
      </div>
    <?php } ?>

  </div>

  <!-- Floating Bottom Batch Actions Toolbar -->
  <div id="floatingBatchBar" class="floating-batch-bar" style="display: none;">
    <div class="floating-batch-content">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-check2-square text-danger fs-5"></i>
        <span class="fw-bold"><span id="floatingSelectedCount">0</span> cluster accounts selected</span>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1 fw-semibold shadow-sm" onclick="confirmBulkDelete();">
          <i class="bi bi-trash3-fill"></i> Delete Selected
        </button>
        <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-2 py-1" onclick="clearClusterSelection();">
          Cancel
        </button>
      </div>
    </div>
  </div>

</div>

<!-- Hidden Action Forms -->
<form id="actionClusterForm" method="POST" action="" style="display:none;">
  <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
  <input type="hidden" name="UserIdty" id="actionUserIdty" value="" />
</form>

<form id="bulkDeleteClusterForm" method="POST" action="<?php echo base_url('user/user_cluster/bulk_delete_user_clusters'); ?>" style="display:none;">
  <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
  <input type="hidden" name="selected_user_ids" id="bulkDeleteSelectedUserIds" value="" />
</form>

<script>
var currentSchoolFilter = 'all';

// Multi-Row Selection Management
function getVisibleClusterRows() {
  return Array.from(document.querySelectorAll('.cluster-row-item')).filter(function(row) {
    return row.style.display !== 'none';
  });
}

function getSelectedClusterCheckboxes() {
  return Array.from(document.querySelectorAll('.cluster-row-check:checked'));
}

function updateBatchBarState() {
  var visibleRows = getVisibleClusterRows();
  var selectedChecks = getSelectedClusterCheckboxes();
  var selectedCount = selectedChecks.length;
  
  var visibleChecks = visibleRows.map(function(r) {
    return r.querySelector('.cluster-row-check');
  }).filter(Boolean);

  var selectAllBox = document.getElementById('selectAllClusters');
  if (selectAllBox) {
    if (visibleChecks.length > 0 && selectedCount >= visibleChecks.length && visibleChecks.every(function(c) { return c.checked; })) {
      selectAllBox.checked = true;
      selectAllBox.indeterminate = false;
    } else if (selectedCount > 0) {
      selectAllBox.checked = false;
      selectAllBox.indeterminate = true;
    } else {
      selectAllBox.checked = false;
      selectAllBox.indeterminate = false;
    }
  }

  // Update batch bars
  var batchBar = document.getElementById('batchActionsBar');
  var floatingBar = document.getElementById('floatingBatchBar');
  var countBadge = document.getElementById('batchCountBadge');
  var btnCount = document.getElementById('batchBtnCount');
  var floatingCount = document.getElementById('floatingSelectedCount');

  if (countBadge) countBadge.innerText = selectedCount;
  if (btnCount) btnCount.innerText = selectedCount;
  if (floatingCount) floatingCount.innerText = selectedCount;

  if (selectedCount > 0) {
    if (batchBar) batchBar.style.display = 'block';
    if (floatingBar) floatingBar.style.display = 'block';
  } else {
    if (batchBar) batchBar.style.display = 'none';
    if (floatingBar) floatingBar.style.display = 'none';
  }
}

function toggleSelectAllClusters(checked) {
  var visibleRows = getVisibleClusterRows();
  visibleRows.forEach(function(row) {
    var chk = row.querySelector('.cluster-row-check');
    if (chk) {
      chk.checked = checked;
      if (checked) {
        row.classList.add('is-selected');
      } else {
        row.classList.remove('is-selected');
      }
    }
  });
  updateBatchBarState();
}

function handleRowCheckChange(chk) {
  var row = chk.closest('tr');
  if (row) {
    if (chk.checked) {
      row.classList.add('is-selected');
    } else {
      row.classList.remove('is-selected');
    }
  }
  updateBatchBarState();
}

function clearClusterSelection() {
  document.querySelectorAll('.cluster-row-check').forEach(function(chk) {
    chk.checked = false;
  });
  document.querySelectorAll('.cluster-row-item').forEach(function(row) {
    row.classList.remove('is-selected');
  });
  updateBatchBarState();
}

// Bulk Delete Execution with SweetAlert2 and AJAX
function confirmBulkDelete() {
  var selectedChecks = getSelectedClusterCheckboxes();
  var count = selectedChecks.length;
  if (count === 0) {
    Swal.fire({
      icon: 'info',
      title: 'No Accounts Selected',
      text: 'Please select one or more cluster accounts using the checkboxes.'
    });
    return;
  }

  var selectedUserIds = selectedChecks.map(function(c) { return c.value; });
  var sampleNames = selectedChecks.slice(0, 5).map(function(c) {
    return '<code>' + (c.getAttribute('data-username') || c.value) + '</code>';
  }).join(', ');
  if (count > 5) {
    sampleNames += ' <i>and ' + (count - 5) + ' more...</i>';
  }

  Swal.fire({
    title: 'Delete ' + count + ' Cluster Accounts?',
    html: '<div class="text-start">' +
            '<p class="mb-2">Are you sure you want to permanently delete the selected <b>' + count + '</b> cluster account(s)?</p>' +
            '<div class="p-2 bg-light rounded border small mb-3">' +
              '<strong>Selected Accounts:</strong> ' + sampleNames +
            '</div>' +
            '<div class="alert alert-danger py-2 px-3 small mb-0 d-flex align-items-start gap-2">' +
              '<i class="bi bi-exclamation-triangle-fill text-danger fs-5 mt-0 flex-shrink-0"></i>' +
              '<div><b>Warning:</b> This will permanently remove the <b>user credentials</b>, <b>user role permissions</b>, and unassign all assigned cluster schools.</div>' +
            '</div>' +
          '</div>',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#64748b',
    confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> Yes, Delete ' + count + ' Accounts',
    cancelButtonText: 'Cancel'
  }).then(function(result) {
    if (result.isConfirmed) {
      executeBulkDelete(selectedUserIds);
    }
  });
}

function executeBulkDelete(userIds) {
  Swal.fire({
    title: 'Deleting Accounts...',
    html: 'Please wait while cluster accounts and associated roles are being removed.',
    allowOutsideClick: false,
    didOpen: function() {
      Swal.showLoading();
    }
  });

  var formData = new FormData();
  formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
  userIds.forEach(function(id) {
    formData.append('userIds[]', id);
  });

  fetch('<?php echo base_url("user/user_cluster/bulk_delete_user_clusters_ajax"); ?>', {
    method: 'POST',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      '<?= config("Security")->headerName ?>': '<?= csrf_hash() ?>'
    },
    body: formData
  })
  .then(function(response) {
    return response.json();
  })
  .then(function(data) {
    if (data.success) {
      var deletedIds = data.deleted_ids || userIds;
      var deletedCount = data.deleted_count || deletedIds.length;

      // Animate removal of rows from DOM
      deletedIds.forEach(function(id) {
        var row = document.getElementById('clusterRow_' + id);
        if (row) {
          row.classList.add('row-fade-out');
          setTimeout(function() {
            if (row.parentNode) row.parentNode.removeChild(row);
            refreshCountersAfterDelete();
          }, 300);
        }
      });

      clearClusterSelection();

      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: data.message || (deletedCount + ' cluster accounts deleted successfully'),
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Delete Failed',
        text: data.message || 'An error occurred while deleting accounts.'
      });
    }
  })
  .catch(function(err) {
    console.error('Delete error:', err);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'A network or server error occurred while deleting cluster accounts.'
    });
  });
}

function refreshCountersAfterDelete() {
  var remainingRows = document.querySelectorAll('.cluster-row-item');
  var totalCount = remainingRows.length;
  
  // Update Sl.No badges
  var idx = 1;
  remainingRows.forEach(function(r) {
    var badge = r.querySelector('.row-index-badge');
    if (badge) {
      badge.innerText = String(idx).padStart(2, '0');
    }
    idx++;
  });

  // Update total counts
  var totalEl = document.getElementById('totalClusterCount');
  if (totalEl) totalEl.innerText = totalCount;

  // Update tab counts
  var withSchoolsCount = 0;
  var noSchoolsCount = 0;
  var totalAssignedSchools = 0;
  remainingRows.forEach(function(r) {
    var st = r.getAttribute('data-school-status');
    var sCount = parseInt(r.getAttribute('data-school-count') || '0', 10);
    totalAssignedSchools += sCount;
    if (st === 'has_schools') withSchoolsCount++;
    if (st === 'no_schools') noSchoolsCount++;
  });

  var tabAll = document.getElementById('tabAll');
  if (tabAll) {
    var countSpan = tabAll.querySelector('.tab-count');
    if (countSpan) countSpan.innerText = totalCount;
  }
  var tabWith = document.getElementById('tabWithSchools');
  if (tabWith) {
    var countSpan = tabWith.querySelector('.tab-count');
    if (countSpan) countSpan.innerText = withSchoolsCount;
  }
  var tabNo = document.getElementById('tabNoSchools');
  if (tabNo) {
    var countSpan = tabNo.querySelector('.tab-count');
    if (countSpan) countSpan.innerText = noSchoolsCount;
  }

  // Re-apply filter and empty view if total is 0
  applyCombinedFilter();

  if (totalCount === 0) {
    var tableWrap = document.querySelector('.table-responsive-custom');
    var noRes = document.getElementById('noSearchResultsBox');
    var footer = document.querySelector('.content-box-footer');
    if (tableWrap) tableWrap.style.display = 'none';
    if (noRes) noRes.style.display = 'none';
    if (footer) footer.style.display = 'none';
    
    // Show empty records
    var container = document.querySelector('.content-box-card');
    if (container && !document.getElementById('emptyStateBoxInitial')) {
      var emptyDiv = document.createElement('div');
      emptyDiv.id = 'emptyStateBoxInitial';
      emptyDiv.className = 'empty-records-box';
      emptyDiv.innerHTML = '<div class="empty-records-icon"><i class="bi bi-diagram-3"></i></div>' +
                           '<div class="empty-records-title">No Cluster Accounts Found</div>' +
                           '<div class="empty-records-desc">Click the "Add Cluster School" button above to register your first cluster.</div>';
      container.appendChild(emptyDiv);
    }
  }
}

// Filter by Status (Tabs)
function filterBySchoolStatus(filterKey) {
  currentSchoolFilter = filterKey;

  var tabs = document.querySelectorAll('.filter-tab-btn');
  tabs.forEach(function(tab) { tab.classList.remove('active'); });

  if (filterKey === 'all') {
    var el = document.getElementById('tabAll');
    if (el) el.classList.add('active');
  } else if (filterKey === 'has_schools') {
    var el = document.getElementById('tabWithSchools');
    if (el) el.classList.add('active');
  } else if (filterKey === 'no_schools') {
    var el = document.getElementById('tabNoSchools');
    if (el) el.classList.add('active');
  }

  applyCombinedFilter();
}

// Live Search & Combined Filter Logic
function handleSearchInput() {
  var query = document.getElementById('clusterSearchInput').value.trim();
  var clearBtn = document.getElementById('searchClearBtn');
  if (clearBtn) {
    clearBtn.style.display = (query.length > 0) ? 'block' : 'none';
  }
  applyCombinedFilter();
}

function clearSearch() {
  var input = document.getElementById('clusterSearchInput');
  if (input) { input.value = ''; }
  var clearBtn = document.getElementById('searchClearBtn');
  if (clearBtn) { clearBtn.style.display = 'none'; }
  
  currentSchoolFilter = 'all';
  var tabs = document.querySelectorAll('.filter-tab-btn');
  tabs.forEach(function(tab) { tab.classList.remove('active'); });
  var tabAll = document.getElementById('tabAll');
  if (tabAll) tabAll.classList.add('active');

  applyCombinedFilter();
}

function applyCombinedFilter() {
  var query = (document.getElementById('clusterSearchInput')?.value || '').toLowerCase().trim();
  var rows = document.querySelectorAll('.cluster-row-item');
  var visibleCount = 0;

  rows.forEach(function(row) {
    var username = row.getAttribute('data-username') || '';
    var schools = row.getAttribute('data-schools') || '';
    var schoolStatus = row.getAttribute('data-school-status') || '';

    var matchesSearch = (username.indexOf(query) !== -1 || schools.indexOf(query) !== -1);
    var matchesFilter = (currentSchoolFilter === 'all' || schoolStatus === currentSchoolFilter);

    if (matchesSearch && matchesFilter) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  // Toggle table vs empty results
  var tableEl = document.getElementById('clusterTable');
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
    badge.innerText = visibleCount + ' Clusters';
  }
  var countText = document.getElementById('visibleCountText');
  if (countText) {
    countText.innerText = visibleCount;
  }

  // Update select-all and batch bar state for currently visible rows
  updateBatchBarState();
}

// Action Handlers
function editUser(userId) {
  var form = document.getElementById('actionClusterForm');
  document.getElementById('actionUserIdty').value = userId;
  form.action = '<?php echo base_url("user/user_cluster/edit_user_cluster"); ?>';
  form.submit();
}

function deleteUser(userId, userName) {
  var displayName = userName ? userName : 'this cluster user';
  Swal.fire({
    title: 'Delete Cluster Confirmation',
    html: '<div class="text-start">' +
            '<p class="mb-2">Are you sure you want to delete cluster user <b>' + displayName + '</b>?</p>' +
            '<div class="alert alert-danger py-2 px-3 small mb-0 d-flex align-items-start gap-2">' +
              '<i class="bi bi-exclamation-triangle-fill text-danger fs-5 mt-0 flex-shrink-0"></i>' +
              '<div><b>Warning:</b> This will permanently delete user credentials, <b>user role permissions</b>, and unassign all assigned cluster schools.</div>' +
            '</div>' +
          '</div>',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#64748b',
    confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> Yes, Delete',
    cancelButtonText: 'Cancel'
  }).then(function(result) {
    if (result.isConfirmed) {
      executeBulkDelete([userId]);
    }
  });
}
</script>
