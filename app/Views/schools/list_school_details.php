<?php
/**
 * School Master Directory View - Ultra-Modern, Colorful & Responsive
 */
$total_schools = count($retvalue ?? []);
$can_delete_schools = ((int) (session()->get('USER_TYPE') ?? -1) === 0) || (session()->get('USER_GROUP') === 'W');
$govt_count = 0;
$aided_count = 0;
$unaided_count = 0;

if (!empty($retvalue)) {
    foreach ($retvalue as $val) {
        $stype = strtoupper($val['school_type'] ?? '');
        if ($stype === 'G') $govt_count++;
        else if ($stype === 'A') $aided_count++;
        else if ($stype === 'U') $unaided_count++;
    }
}
?>

<style>
/* Modern Colorful Theme */
.swal2-container {
  z-index: 1000001 !important;
}

:root {
  --primary-gradient: linear-gradient(135deg, #4338ca 0%, #3b82f6 50%, #06b6d4 100%);
  --accent-indigo: #4338ca;
  --accent-sky: #0284c7;
  --accent-emerald: #059669;
  --accent-purple: #7c3aed;
  --card-bg: #ffffff;
  --bg-subtle: #f8fafc;
  --card-border: #e2e8f0;
  --text-main: #0f172a;
  --text-muted: #64748b;
  --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
  --shadow-md: 0 4px 12px -2px rgba(0, 0, 0, 0.07), 0 2px 6px -1px rgba(0, 0, 0, 0.04);
  --shadow-lg: 0 12px 24px -4px rgba(0, 0, 0, 0.09), 0 4px 8px -2px rgba(0, 0, 0, 0.04);
  --radius-lg: 16px;
  --radius-md: 12px;
}

.school-master-wrapper {
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
  gap: 12px;
}

.module-title i {
  color: #4338ca;
  font-size: 1.65rem;
  background: linear-gradient(135deg, #4338ca 0%, #06b6d4 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.module-subtitle {
  color: var(--text-muted);
  font-size: 0.88rem;
  margin-top: 4px;
  margin-bottom: 0;
}

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
  box-shadow: 0 4px 12px rgba(67, 56, 202, 0.25);
  border: none;
  cursor: pointer;
}

.btn-primary-action:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(67, 56, 202, 0.35);
  color: #ffffff;
}

.btn-outline-action {
  background: #f8fafc;
  color: #475569 !important;
  border: 1px solid #cbd5e1;
  cursor: pointer;
}

.btn-outline-action:hover {
  background: #f1f5f9;
  color: #0f172a !important;
  border-color: #94a3b8;
  transform: translateY(-1px);
}

/* KPI Summary Cards Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.stat-item-card {
  background: var(--card-bg);
  border-radius: var(--radius-md);
  padding: 18px 20px;
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
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  flex-shrink: 0;
}

.stat-indigo { background: #e0e7ff; color: #4338ca; }
.stat-emerald{ background: #d1fae5; color: #047857; }
.stat-sky    { background: #e0f2fe; color: #0284c7; }
.stat-purple { background: #f3e8ff; color: #7e22ce; }

.stat-value {
  font-size: 1.45rem;
  font-weight: 700;
  color: var(--text-main);
  line-height: 1.1;
}

.stat-label {
  font-size: 0.8rem;
  font-weight: 500;
  color: var(--text-muted);
  margin-top: 4px;
}

/* Main Dashboard Card */
.content-box-card {
  background: var(--card-bg);
  border-radius: var(--radius-lg);
  border: 1px solid var(--card-border);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
  margin-bottom: 24px;
}

.content-box-header {
  padding: 18px 24px;
  background: #ffffff;
  border-bottom: 1px solid var(--card-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 14px;
}

/* Geographic Filter Bar */
.geo-filter-bar {
  padding: 14px 24px;
  background: #f8fafc;
  border-bottom: 1px solid var(--card-border);
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

/* Type Filter Tabs Bar */
.filter-tabs-bar {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
}

.filter-tab-btn {
  padding: 6px 14px;
  border-radius: 50px;
  font-size: 0.8rem;
  font-weight: 600;
  border: 1px solid #cbd5e1;
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
  background: #4338ca;
  color: #ffffff;
  border-color: #4338ca;
  box-shadow: 0 2px 6px rgba(67, 56, 202, 0.25);
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
  min-width: 250px;
  max-width: 320px;
  flex-grow: 1;
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
  padding: 8px 36px 8px 38px;
  border-radius: 50px;
  border: 1px solid #cbd5e1;
  font-size: 0.86rem;
  background: #f8fafc;
  outline: none;
  transition: all 0.2s;
}

.search-input-wrap input:focus {
  border-color: #4338ca;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(67, 56, 202, 0.12);
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
}

/* Modern Data Table */
.table-responsive-custom {
  width: 100%;
  overflow-x: auto;
}

.school-data-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
}

.school-data-table th {
  background: #f8fafc;
  color: #475569;
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  padding: 13px 20px;
  border-bottom: 1px solid var(--card-border);
  white-space: nowrap;
}

.school-data-table td {
  padding: 14px 20px;
  vertical-align: middle;
  border-bottom: 1px solid #f1f5f9;
  font-size: 0.88rem;
  color: #334155;
  transition: background 0.15s ease;
}

.school-data-table tbody tr:hover td {
  background: #f8fafc;
}

.school-data-table tbody tr:last-child td {
  border-bottom: none;
}

/* Cell Elements */
.school-avatar-circle {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: linear-gradient(135deg, #4338ca 0%, #3b82f6 100%);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.88rem;
  box-shadow: 0 2px 5px rgba(67, 56, 202, 0.2);
  flex-shrink: 0;
}

.school-code-badge {
  font-size: 0.82rem;
  font-weight: 700;
  color: #4338ca;
  background: #e0e7ff;
  border: 1px solid #c7d2fe;
  padding: 4px 9px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.school-type-pill {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 0.78rem;
  font-weight: 600;
  white-space: nowrap;
}

.type-govt    { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
.type-aided   { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
.type-unaided { background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; }

/* Action Edit Button */
.btn-table-edit {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 10px;
  border: 1px solid #c7d2fe;
  background: #e0e7ff;
  color: #4338ca;
  transition: all 0.15s ease;
  font-size: 0.95rem;
  cursor: pointer;
  text-decoration: none;
}

.btn-table-edit:hover {
  background: #4338ca;
  color: #ffffff;
  transform: translateY(-2px);
  box-shadow: 0 3px 8px rgba(67, 56, 202, 0.3);
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

/* Printable Hidden Element */
.display_none { display: none; }

@media print {
  body * { visibility: hidden; }
  #print_content, #print_content * { visibility: visible; }
  #print_content { position: absolute; left: 0; top: 0; width: 100%; display: block !important; }
}
</style>

<div class="school-master-wrapper">

  <!-- Header Card -->
  <div class="module-header-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h1 class="module-title">
          <i class="bi bi-mortarboard-fill"></i> School Master & Directory
        </h1>
        <p class="module-subtitle">Manage registered institutions, school codes, management types, and administrative zones</p>
      </div>

      <div class="nav-actions-group">
        <?php if ($this->Session_Model->check_user_permission(26)) { ?>
          <button type="button" class="btn-nav-action btn-primary-action" onclick="showAddSchoolModal();">
            <i class="bi bi-plus-circle-fill"></i> Add School
          </button>
        <?php } ?>
        <button type="button" class="btn-nav-action btn-outline-action" onclick="javascript:printContent('print_content');" title="Print School Directory">
          <i class="bi bi-printer-fill text-primary"></i> Print Directory
        </button>
      </div>
    </div>
  </div>

  <!-- KPI Stat Cards -->
  <div class="stats-grid">
    <div class="stat-item-card" onclick="filterBySchoolType('ALL');">
      <div class="stat-icon-box stat-indigo">
        <i class="bi bi-buildings"></i>
      </div>
      <div>
        <div class="stat-value" id="kpiTotalSchools"><?php echo $total_schools; ?></div>
        <div class="stat-label">Total Institutions</div>
      </div>
    </div>

    <div class="stat-item-card" onclick="filterBySchoolType('G');">
      <div class="stat-icon-box stat-emerald">
        <i class="bi bi-shield-check"></i>
      </div>
      <div>
        <div class="stat-value"><?php echo $govt_count; ?></div>
        <div class="stat-label">Government Schools</div>
      </div>
    </div>

    <div class="stat-item-card" onclick="filterBySchoolType('A');">
      <div class="stat-icon-box stat-sky">
        <i class="bi bi-award-fill"></i>
      </div>
      <div>
        <div class="stat-value"><?php echo $aided_count; ?></div>
        <div class="stat-label">Aided Schools</div>
      </div>
    </div>

    <div class="stat-item-card" onclick="filterBySchoolType('U');">
      <div class="stat-icon-box stat-purple">
        <i class="bi bi-bank2"></i>
      </div>
      <div>
        <div class="stat-value"><?php echo $unaided_count; ?></div>
        <div class="stat-label">Unaided / Private</div>
      </div>
    </div>
  </div>

  <!-- Main Data Table Card -->
  <div class="content-box-card">
    
    <!-- Optional District/Sub-District Filtering Form -->
    <?php if (!empty($can_use_zone_filter)) { ?>
      <?php echo form_open('schools/school_master/', array('id' => 'filter', 'class' => 'm-0', 'method' => 'get')); ?>
        <div class="geo-filter-bar">
          <span class="small fw-bold text-dark d-flex align-items-center gap-1">
            <i class="bi bi-geo-alt-fill text-primary"></i> Zone Filter:
          </span>

          <?php if ($this->session->get('USER_GROUP') == 'W' || in_array((int)$this->session->get('USER_TYPE'), [0, 1], true)) { ?>
            <div id="divDistrictFilter" style="min-width: 200px;">
              <?php echo form_dropdown("cmbDistrictFilter", $districts, $selected_filter_district ?? 0, 'id="cmbDistrictFilter" class="form-select form-select-sm" onChange="javascript:loadSubDistrictFilter()"'); ?>
            </div>
          <?php } else if ($this->session->get('DISTRICT')) { ?>
            <input type="hidden" name="cmbDistrictFilter" id="cmbDistrictFilter" value="<?php echo esc((string)$this->session->get('DISTRICT')); ?>" />
          <?php } ?>

          <div id="divSubDistrictFilter" style="min-width: 200px; display:<?php echo (!empty($selected_filter_district) || $this->session->get('DISTRICT')) ? 'block' : 'none'; ?>;">
            <?php echo form_dropdown("cmbSubDistrictFilter", @$sub_districts, $selected_filter_sub_district ?? 0, 'id="cmbSubDistrictFilter" class="form-select form-select-sm"'); ?>
          </div>

          <button type="submit" name="filter" value="filter" class="btn btn-sm btn-primary rounded-pill px-3">
            <i class="bi bi-arrow-right-circle me-1"></i> Apply Filter
          </button>
          <button type="submit" name="clear_filter" value="1" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-x-circle me-1"></i> Clear
          </button>
        </div>
      <?php echo form_close(); ?>
    <?php } ?>

    <!-- Table Toolbar Header -->
    <div class="content-box-header">
      
      <!-- Filter Tabs -->
      <div class="filter-tabs-bar">
        <button type="button" class="filter-tab-btn active" id="tabTypeAll" onclick="filterBySchoolType('ALL');">
          All Schools <span class="tab-count"><?php echo $total_schools; ?></span>
        </button>
        <button type="button" class="filter-tab-btn" id="tabTypeGovt" onclick="filterBySchoolType('G');">
          Government <span class="tab-count"><?php echo $govt_count; ?></span>
        </button>
        <button type="button" class="filter-tab-btn" id="tabTypeAided" onclick="filterBySchoolType('A');">
          Aided <span class="tab-count"><?php echo $aided_count; ?></span>
        </button>
        <button type="button" class="filter-tab-btn" id="tabTypeUnaided" onclick="filterBySchoolType('U');">
          Unaided <span class="tab-count"><?php echo $unaided_count; ?></span>
        </button>
      </div>

      <!-- Real-Time Search Bar -->
      <div class="search-input-wrap">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="schoolSearchInput" placeholder="Search school code, name..." oninput="handleSchoolSearch();" />
        <button type="button" class="search-clear-btn" id="schoolSearchClearBtn" onclick="clearSchoolSearch();" title="Clear Search">
          <i class="bi bi-x-circle-fill"></i>
        </button>
      </div>

    </div>

    <!-- Data Table -->
    <?php if (!empty($retvalue)) { ?>
      <div class="table-responsive-custom">
        <table class="school-data-table" id="schoolTable">
          <thead>
            <tr>
              <th width="7%" class="text-center">Sl.No</th>
              <th width="14%">School Code</th>
              <th width="50%">School Name</th>
              <th width="20%">Management Type</th>
              <th width="9%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody id="schoolTableBody">
            <?php 
            $i = 1;
            foreach ($retvalue as $value) {
                $code = $value['school_code'] ?? '';
                $name = $value['school_name'] ?? '';
                $stype = strtoupper($value['school_type'] ?? '');
                $initial = !empty($name) ? strtoupper(substr($name, 0, 2)) : 'SC';

                $type_label = 'Other';
                $type_class = 'type-govt';
                if ($stype === 'G') {
                    $type_label = 'Government (Govt)';
                    $type_class = 'type-govt';
                } else if ($stype === 'A') {
                    $type_label = 'Aided';
                    $type_class = 'type-aided';
                } else if ($stype === 'U') {
                    $type_label = 'Unaided / Private';
                    $type_class = 'type-unaided';
                }
            ?>
              <tr class="school-row" 
                  data-code="<?php echo strtolower(esc($code)); ?>" 
                  data-name="<?php echo strtolower(esc($name)); ?>" 
                  data-type="<?php echo esc($stype); ?>">
                
                <td class="text-center">
                  <span class="badge bg-light text-secondary border px-2 py-1 rounded fw-bold">
                    <?php echo str_pad((string)$i, 2, '0', STR_PAD_LEFT); ?>
                  </span>
                </td>

                <td>
                  <span class="school-code-badge">
                    <i class="bi bi-hash"></i><?php echo esc($code); ?>
                  </span>
                </td>

                <td>
                  <div class="d-flex align-items-center gap-3">
                    <div class="school-avatar-circle"><?php echo esc($initial); ?></div>
                    <div class="fw-semibold text-dark fs-6"><?php echo esc($name); ?></div>
                  </div>
                </td>

                <td>
                  <span class="school-type-pill <?php echo $type_class; ?>">
                    <i class="bi bi-building"></i> <?php echo esc($type_label); ?>
                  </span>
                </td>

                <td class="text-center text-nowrap">
                  <div class="d-inline-flex align-items-center justify-content-center gap-2 flex-nowrap">
                  <button type="button" class="btn-table-edit" title="Edit School Details" onclick="editUser('<?php echo esc($code); ?>');">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <?php if ($can_delete_schools) { ?>
                    <button type="button" class="btn-table-edit text-danger" title="Delete School" onclick="deleteSchool('<?php echo esc($code); ?>', '<?php echo esc($name, 'js'); ?>');">
                      <i class="bi bi-trash"></i>
                    </button>
                  <?php } ?>
                  </div>
                </td>

              </tr>
            <?php $i++; } ?>
          </tbody>
        </table>
      </div>

      <!-- Empty Search State -->
      <div class="empty-records-box" id="noSchoolResultsBox" style="display:none;">
        <div class="empty-records-icon"><i class="bi bi-search"></i></div>
        <div class="empty-records-title">No Matching Schools Found</div>
        <div class="empty-records-desc">Try modifying your search keywords or switching filters.</div>
        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-3" onclick="clearSchoolSearch();">
          <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
        </button>
      </div>

      <!-- Table Footer -->
      <div class="content-box-footer">
        <div>
          Showing <b id="visibleSchoolCountText"><?php echo $total_schools; ?></b> of <b><?php echo $total_schools; ?></b> schools
        </div>
        <div>
          <i class="bi bi-info-circle text-primary me-1"></i> Click on the edit button to update school details or code assignments.
        </div>
      </div>

    <?php } else { ?>
      <div class="empty-records-box">
        <div class="empty-records-icon"><i class="bi bi-mortarboard"></i></div>
        <div class="empty-records-title">No Schools Registered</div>
        <div class="empty-records-desc">Click the "Add School" button above to register your first institution.</div>
      </div>
    <?php } ?>

  </div>

</div>

<!-- Hidden Action Form for fallback legacy interactions -->
<?php echo form_open('schools/school_master/', array('id' => 'editUser', 'style' => 'display:none;')); ?>
  <input type="hidden" name="UserIdty" id="UserIdty" value="" />
<?php echo form_close(); ?>
<?php if ($can_delete_schools) { ?>
  <?php echo form_open('schools/school_master/delete_school_details', array('id' => 'deleteSchoolForm', 'style' => 'display:none;')); ?>
    <input type="hidden" name="UserIdty" id="deleteSchoolCode" value="" />
  <?php echo form_close(); ?>
<?php } ?>

<!-- Print Content Container -->
<div id="print_content" class="display_none">
  <div style="font-family: Arial, sans-serif; padding: 20px;">
    <h3 style="text-align: center; margin-bottom: 5px;">School Master Directory</h3>
    <p style="text-align: center; color: #666; font-size: 13px; margin-top: 0;">Institutional Registration Records</p>
    <table width="100%" border="1" cellspacing="0" cellpadding="8" style="border-collapse: collapse; margin-top: 15px; font-size: 12px;">
      <thead>
        <tr style="background-color: #f1f5f9;">
          <th width="8%" style="text-align: center;">Sl.No</th>
          <th width="20%">School Code</th>
          <th width="50%">School Name</th>
          <th width="22%">School Type</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $pi = 1;
        foreach ($retvalue as $pval) {
            $ptype = 'Unaided';
            if ($pval['school_type'] == 'G') $ptype = 'Government';
            else if ($pval['school_type'] == 'A') $ptype = 'Aided';
        ?>
          <tr>
            <td align="center"><?php echo $pi; ?></td>
            <td><?php echo esc($pval['school_code']); ?></td>
            <td><?php echo esc($pval['school_name']); ?></td>
            <td><?php echo esc($ptype); ?></td>
          </tr>
        <?php $pi++; } ?>
      </tbody>
    </table>
  </div>
</div>

<script>
var currentSchoolType = 'ALL';
function deleteSchool(code, name) {
  Swal.fire({
    target: document.body,
    title: 'Delete this school?',
    text: 'Delete "' + name + '" (Code: ' + code + ')? Its school master and details records will be removed.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete school',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#dc2626',
    reverseButtons: true
  }).then(function (result) {
    if (!result.isConfirmed) return;

    var deleteForm = document.getElementById('deleteSchoolForm');
    var deleteCode = document.getElementById('deleteSchoolCode');
    deleteCode.value = code;
    var formData = new FormData(deleteForm);

    Swal.fire({
      title: 'Deleting school...',
      text: 'Please wait while the school is deleted.',
      allowOutsideClick: false,
      allowEscapeKey: false,
      showConfirmButton: false,
      didOpen: function () { Swal.showLoading(); }
    });

    fetch(deleteForm.action, {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(function (response) {
      return response.json().then(function (data) {
        if (!response.ok || !data.success) {
          throw new Error(data.message || 'The school could not be deleted.');
        }
        return data;
      });
    }).then(function () {
      Swal.fire({
        title: 'School deleted',
        text: 'The school was deleted successfully.',
        icon: 'success',
        showConfirmButton: false,
        timer: 1200,
        confirmButtonColor: '#4338ca'
      }).then(function () {
        var listUrl = new URL(deleteForm.action.replace(/delete_school_details\/?$/, ''), window.location.href);
        var currentUrl = new URL(window.location.href);
        ['cmbDistrictFilter', 'cmbSubDistrictFilter'].forEach(function (filterName) {
          var filterValue = currentUrl.searchParams.get(filterName);
          if (filterValue !== null && filterValue !== '') {
            listUrl.searchParams.set(filterName, filterValue);
          }
        });
        var freshUrl = listUrl;
        freshUrl.searchParams.set('_refresh', Date.now().toString());
        window.location.assign(freshUrl.toString());
      });
    }).catch(function (error) {
      Swal.fire({
        title: 'Delete failed',
        text: error.message || 'The school could not be deleted. Please try again.',
        icon: 'error',
        confirmButtonColor: '#4338ca'
      });
    });
  });
}

function filterBySchoolType(typeKey) {
  currentSchoolType = typeKey;

  var tabs = document.querySelectorAll('.filter-tab-btn');
  tabs.forEach(function(tab) { tab.classList.remove('active'); });

  if (typeKey === 'ALL') document.getElementById('tabTypeAll')?.classList.add('active');
  else if (typeKey === 'G') document.getElementById('tabTypeGovt')?.classList.add('active');
  else if (typeKey === 'A') document.getElementById('tabTypeAided')?.classList.add('active');
  else if (typeKey === 'U') document.getElementById('tabTypeUnaided')?.classList.add('active');

  applyCombinedSchoolFilter();
}

function handleSchoolSearch() {
  var query = document.getElementById('schoolSearchInput').value.trim();
  var clearBtn = document.getElementById('schoolSearchClearBtn');
  if (clearBtn) clearBtn.style.display = (query.length > 0) ? 'block' : 'none';
  applyCombinedSchoolFilter();
}

function clearSchoolSearch() {
  var input = document.getElementById('schoolSearchInput');
  if (input) input.value = '';
  var clearBtn = document.getElementById('schoolSearchClearBtn');
  if (clearBtn) clearBtn.style.display = 'none';

  filterBySchoolType('ALL');
}

function applyCombinedSchoolFilter() {
  var query = (document.getElementById('schoolSearchInput')?.value || '').toLowerCase().trim();
  var rows = document.querySelectorAll('.school-row');
  var visibleCount = 0;

  rows.forEach(function(row) {
    var code = row.getAttribute('data-code') || '';
    var name = row.getAttribute('data-name') || '';
    var type = row.getAttribute('data-type') || '';

    var matchesSearch = (code.indexOf(query) !== -1 || name.indexOf(query) !== -1);
    var matchesType = (currentSchoolType === 'ALL' || type === currentSchoolType);

    if (matchesSearch && matchesType) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  var tableEl = document.getElementById('schoolTable');
  var noResultsBox = document.getElementById('noSchoolResultsBox');
  if (tableEl && noResultsBox) {
    if (visibleCount === 0 && rows.length > 0) {
      tableEl.style.display = 'none';
      noResultsBox.style.display = 'block';
    } else {
      tableEl.style.display = '';
      noResultsBox.style.display = 'none';
    }
  }

  var countText = document.getElementById('visibleSchoolCountText');
  if (countText) countText.innerText = visibleCount;
}
</script>
