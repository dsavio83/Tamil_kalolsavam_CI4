<?php
/**
 * Stage Allotment Duration Report - Ultra-Modern Interactive Dashboard
 */
$fest = $fest ?? [];
$selected_fest = $selected_fest ?? 'ALL';
$allot_data = $allot_data ?? [];
$isDistrict = true;
$subdistricts = $subdistricts ?? [];
$selected_subdistrict = $selected_subdistrict ?? 'ALL';

if (isset($fest[1])) {
    unset($fest[1]);
}

// Calculate initial metrics
$total_items = count($allot_data);
$total_participants = 0;
$total_duration_minutes = 0;
$on_stage_count = 0;
$off_stage_count = 0;
$group_items_count = 0;
$single_items_count = 0;

foreach ($allot_data as $row) {
    $pdcount = (int)($row['pdcount'] ?? 0);
    $max_time = (float)($row['max_time'] ?? 0);
    $time_type = strtoupper($row['time_type'] ?? 'M');
    $is_off_stage = ($row['is_off_stage'] == '1' || strtoupper($row['is_off_stage'] ?? '') == 'Y');
    $item_type = strtoupper($row['item_type'] ?? 'S');

    $total_participants += $pdcount;

    // Calculate duration in minutes
    $unit_mins = ($time_type === 'H') ? ($max_time * 60) : $max_time;
    $item_total_mins = $unit_mins * $pdcount;
    $total_duration_minutes += $item_total_mins;

    if ($is_off_stage) {
        $off_stage_count++;
    } else {
        $on_stage_count++;
    }

    if ($item_type === 'G') {
        $group_items_count++;
    } else {
        $single_items_count++;
    }
}

// Convert total minutes to Hours and Minutes
$duration_hours = floor($total_duration_minutes / 60);
$duration_rem_minutes = round($total_duration_minutes % 60);
$formatted_total_duration = ($duration_hours > 0 ? "{$duration_hours}h " : "") . "{$duration_rem_minutes}m";
?>

<style>
/* Modern Theme Variables */
:root {
  --primary-gradient: linear-gradient(135deg, #4338ca 0%, #3b82f6 50%, #06b6d4 100%);
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

.stage-duration-wrapper {
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

/* Nav Action Buttons */
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
  padding: 8px 16px;
  border-radius: 50px;
  font-size: 0.82rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;
  border: 1px solid #cbd5e1;
  background: #f8fafc;
  color: #475569 !important;
}

.btn-nav-action:hover {
  background: #f1f5f9;
  color: #0f172a !important;
  border-color: #94a3b8;
  transform: translateY(-1px);
}

.btn-pdf-export {
  background: var(--primary-gradient) !important;
  color: #ffffff !important;
  border: none !important;
  box-shadow: 0 4px 14px rgba(67, 56, 202, 0.35) !important;
}

.btn-pdf-export:hover,
.btn-pdf-export:focus,
.btn-pdf-export:active {
  background: linear-gradient(135deg, #3730a3 0%, #2563eb 50%, #0891b2 100%) !important;
  color: #ffffff !important;
  border: none !important;
  box-shadow: 0 6px 20px rgba(67, 56, 202, 0.5) !important;
  transform: translateY(-2px);
}

.btn-pdf-export i,
.btn-pdf-export:hover i,
.btn-pdf-export:focus i,
.btn-pdf-export:active i {
  color: #ffffff !important;
}

/* KPI Summary Cards Grid */
.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.metric-card {
  background: var(--card-bg);
  border-radius: var(--radius-md);
  padding: 18px 20px;
  border: 1px solid var(--card-border);
  box-shadow: var(--shadow-sm);
  display: flex;
  align-items: center;
  gap: 16px;
  transition: all 0.2s ease;
}

.metric-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
  border-color: #cbd5e1;
}

.metric-icon-box {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  flex-shrink: 0;
}

.metric-indigo { background: #e0e7ff; color: #4338ca; }
.metric-sky    { background: #e0f2fe; color: #0284c7; }
.metric-emerald{ background: #d1fae5; color: #047857; }
.metric-amber  { background: #fef3c7; color: #d97706; }

.metric-value {
  font-size: 1.45rem;
  font-weight: 700;
  color: var(--text-main);
  line-height: 1.1;
}

.metric-label {
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
  padding: 20px 24px;
  background: #ffffff;
  border-bottom: 1px solid var(--card-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
}

/* Festival Filter Pills */
.filter-pills-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  padding: 14px 24px;
  background: #f8fafc;
  border-bottom: 1px solid var(--card-border);
}

.filter-pill-btn {
  padding: 6px 14px;
  border-radius: 50px;
  font-size: 0.8rem;
  font-weight: 600;
  border: 1px solid #cbd5e1;
  background: #ffffff;
  color: #475569;
  cursor: pointer;
  transition: all 0.15s ease;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.filter-pill-btn:hover {
  background: #f1f5f9;
  color: #1e293b;
  border-color: #94a3b8;
}

.filter-pill-btn.active,
.filter-pill-btn.active:hover,
.filter-pill-btn.active:focus {
  background: linear-gradient(135deg, #4338ca 0%, #3b82f6 100%) !important;
  color: #ffffff !important;
  border-color: #4338ca !important;
  box-shadow: 0 3px 8px rgba(67, 56, 202, 0.3) !important;
}

/* Secondary Filter / Search Controls */
.secondary-controls-bar {
  padding: 12px 24px;
  background: #ffffff;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 14px;
}

.search-box-wrap {
  position: relative;
  min-width: 260px;
  max-width: 340px;
  flex-grow: 1;
}

.search-box-wrap i.search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 0.95rem;
}

.search-box-wrap input {
  width: 100%;
  padding: 8px 36px 8px 38px;
  border-radius: 50px;
  border: 1px solid #cbd5e1;
  font-size: 0.86rem;
  background: #f8fafc;
  outline: none;
  transition: all 0.2s;
}

.search-box-wrap input:focus {
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

.type-filter-group {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
}

.type-filter-btn {
  padding: 5px 12px;
  border-radius: 50px;
  font-size: 0.76rem;
  font-weight: 600;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  color: #64748b;
  cursor: pointer;
  transition: all 0.15s ease;
}

.type-filter-btn:hover { background: #f1f5f9; color: #1e293b; }
.type-filter-btn.active,
.type-filter-btn.active:hover,
.type-filter-btn.active:focus {
  background: #0284c7 !important;
  color: #ffffff !important;
  border-color: #0284c7 !important;
}

/* Modern Data Table */
.table-responsive-custom {
  width: 100%;
  overflow-x: auto;
}

.duration-data-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
}

.duration-data-table th {
  background: #f8fafc;
  color: #475569;
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  padding: 13px 18px;
  border-bottom: 1px solid var(--card-border);
  white-space: nowrap;
}

.duration-data-table td {
  padding: 14px 18px;
  vertical-align: middle;
  border-bottom: 1px solid #f1f5f9;
  font-size: 0.88rem;
  color: #334155;
  transition: background 0.15s ease;
}

.duration-data-table tbody tr:hover td {
  background: #f8fafc;
}

.duration-data-table tbody tr:last-child td {
  border-bottom: none;
}

/* Custom Badges */
.item-code-badge {
  font-size: 0.82rem;
  font-weight: 700;
  color: #4338ca;
  background: #e0e7ff;
  border: 1px solid #c7d2fe;
  padding: 3px 8px;
  border-radius: 6px;
  display: inline-block;
}

.fest-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 10px;
  border-radius: 50px;
  font-size: 0.78rem;
  font-weight: 600;
  background: #f1f5f9;
  color: #334155;
  border: 1px solid #e2e8f0;
}

.type-pill {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 3px 9px;
  border-radius: 50px;
  font-size: 0.76rem;
  font-weight: 600;
}

.type-pill-single { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.type-pill-group  { background: #fdf4ff; color: #86198f; border: 1px solid #f5d0fe; }

.stage-type-pill {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 10px;
  border-radius: 50px;
  font-size: 0.78rem;
  font-weight: 600;
}

.stage-on  { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
.stage-off { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }

.duration-calc-badge {
  font-size: 0.84rem;
  font-weight: 700;
  color: #0f172a;
  background: #f8fafc;
  padding: 4px 10px;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.duration-calc-badge i { color: #0284c7; }

/* Table Footer Card */
.content-box-footer {
  padding: 14px 24px;
  background: #f8fafc;
  border-top: 1px solid var(--card-border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.84rem;
  color: var(--text-muted);
  flex-wrap: wrap;
  gap: 10px;
}

/* Empty State */
.empty-records-box {
  padding: 50px 24px;
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

/* Print Styles */
@media print {
  .module-header-card, .filter-pills-bar, .secondary-controls-bar, .nav-actions-group, .content-box-footer {
    display: none !important;
  }
  .content-box-card {
    border: none !important;
    box-shadow: none !important;
  }
}

/* Mobile responsive */
@media (max-width: 768px) {
  .module-header-card { padding: 18px; }
  .content-box-header { padding: 14px 16px; }
  .filter-pills-bar { padding: 10px 16px; }
  .secondary-controls-bar { padding: 10px 16px; }
  .search-box-wrap { max-width: 100%; }
}
</style>

<div class="stage-duration-wrapper">

  <!-- Header Card -->
  <div class="module-header-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h1 class="module-title">
          <i class="bi bi-stopwatch-fill"></i> Stage Allotment Duration Report
        </h1>
        <p class="module-subtitle">Real-time timeline analysis, participant volume, and estimated stage performance duration</p>
      </div>

      <div class="nav-actions-group">
        <!-- PDF Form Trigger Button -->
        <button type="button" class="btn-nav-action btn-pdf-export" onclick="submitPdfReport();">
          <i class="bi bi-file-earmark-pdf-fill"></i> Export Official PDF
        </button>
        <button type="button" class="btn-nav-action" onclick="window.print();" title="Print Current View">
          <i class="bi bi-printer-fill text-secondary"></i> Print View
        </button>
        <a href="<?php echo base_url('district/report/prereport/timesheetinterface'); ?>" class="btn-nav-action" title="View Time Sheet">
          <i class="bi bi-clock-history text-primary"></i> Time Sheet
        </a>
      </div>
    </div>
  </div>

  <!-- KPI Metric Cards -->
  <div class="metrics-grid">
    <div class="metric-card">
      <div class="metric-icon-box metric-indigo">
        <i class="bi bi-collection-play-fill"></i>
      </div>
      <div>
        <div class="metric-value" id="kpiTotalItems"><?php echo $total_items; ?></div>
        <div class="metric-label">Total Events / Items</div>
      </div>
    </div>

    <div class="metric-card">
      <div class="metric-icon-box metric-sky">
        <i class="bi bi-people-fill"></i>
      </div>
      <div>
        <div class="metric-value" id="kpiTotalParticipants"><?php echo $total_participants; ?></div>
        <div class="metric-label">Total Participants / Teams</div>
      </div>
    </div>

    <div class="metric-card">
      <div class="metric-icon-box metric-emerald">
        <i class="bi bi-hourglass-split"></i>
      </div>
      <div>
        <div class="metric-value" id="kpiTotalDuration"><?php echo $formatted_total_duration; ?></div>
        <div class="metric-label">Estimated Stage Runtime</div>
      </div>
    </div>

    <div class="metric-card">
      <div class="metric-icon-box metric-amber">
        <i class="bi bi-soundwave"></i>
      </div>
      <div>
        <div class="metric-value" id="kpiStageSplit">
          <span class="text-success"><?php echo $on_stage_count; ?> On</span> / <span class="text-primary"><?php echo $off_stage_count; ?> Off</span>
        </div>
        <div class="metric-label">On-Stage vs Off-Stage</div>
      </div>
    </div>
  </div>

  <!-- Main Table Dashboard Card -->
  <div class="content-box-card">
    
    <!-- Top Header -->
    <div class="content-box-header">
      <div class="d-flex align-items-center gap-2">
        <h3 class="fs-5 fw-bold text-dark m-0 d-flex align-items-center gap-2">
          <i class="bi bi-table text-primary"></i> Stage Duration Schedule Breakdown
        </h3>
        <span class="badge bg-primary-subtle text-primary border rounded-pill px-3 py-1 small fw-bold" id="itemCountBadge">
          <?php echo $total_items; ?> Events
        </span>
      </div>

      <!-- Sub-District Selector & Category Display -->
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <?php if (!empty($subdistricts)) { ?>
          <div class="d-flex align-items-center gap-1.5 bg-light border rounded-pill px-2.5 py-1">
            <i class="bi bi-geo-alt-fill text-primary small"></i>
            <span class="small fw-semibold text-muted">Sub-District:</span>
            <select id="cmbSubDistrictSelector" class="form-select form-select-sm border-0 bg-transparent py-0 ps-1 pe-4 fw-bold text-dark" style="font-size:0.84rem; cursor:pointer;" onchange="onSubDistrictChange(this.value);">
              <option value="ALL" <?php echo ($selected_subdistrict === 'ALL' || empty($selected_subdistrict)) ? 'selected' : ''; ?>>All Sub-Districts (Consolidated)</option>
              <?php foreach ($subdistricts as $sd) { 
                $sCode = (string)$sd['sub_district_code'];
                $sName = $sd['sub_district_name'];
                $isSel = ($selected_subdistrict == $sCode);
              ?>
                <option value="<?php echo esc($sCode); ?>" <?php echo $isSel ? 'selected' : ''; ?>>
                  <?php echo esc($sCode . ' - ' . $sName); ?>
                </option>
              <?php } ?>
            </select>
          </div>
        <?php } ?>
        <span class="badge bg-light text-dark border px-2 py-1" id="activeCategoryDisplay">All Festivals</span>
      </div>
    </div>

    <!-- Festival Filter Pills -->
    <div class="filter-pills-bar">
      <span class="small fw-bold text-muted text-uppercase me-2 d-none d-lg-inline">Festival:</span>
      <button type="button" class="filter-pill-btn <?php echo ($selected_fest === 'ALL' || empty($selected_fest)) ? 'active' : ''; ?>" 
              data-fest-id="ALL" onclick="filterByFestival('ALL', 'All Festivals');">
        <i class="bi bi-grid-fill"></i> All Festivals
      </button>

      <?php 
      foreach ($fest as $fid => $fname) {
          if (empty($fid) || $fid === '0' || $fid === 'ALL') continue;
          $is_active = ($selected_fest == $fid);
      ?>
        <button type="button" class="filter-pill-btn <?php echo $is_active ? 'active' : ''; ?>" 
                data-fest-id="<?php echo esc($fid); ?>" 
                onclick="filterByFestival('<?php echo esc($fid); ?>', '<?php echo esc(addslashes($fname)); ?>');">
          <i class="bi bi-mortarboard-fill"></i> <?php echo esc($fname); ?>
        </button>
      <?php } ?>
    </div>

    <!-- Secondary Controls (Search & Type Filters) -->
    <div class="secondary-controls-bar">
      <!-- Real-time Live Search -->
      <div class="search-box-wrap">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="durationTableSearch" placeholder="Search item code, event name..." oninput="handleTableSearch();" />
        <button type="button" class="search-clear-btn" id="searchClearBtn" onclick="clearTableSearch();" title="Clear Search">
          <i class="bi bi-x-circle-fill"></i>
        </button>
      </div>

      <!-- Item Type & Stage Type Quick Filters -->
      <div class="type-filter-group">
        <button type="button" class="type-filter-btn active" id="typeFilterAll" onclick="filterByType('ALL');">
          All Types
        </button>
        <button type="button" class="type-filter-btn" id="typeFilterOn" onclick="filterByType('ON_STAGE');">
          <i class="bi bi-soundwave text-success"></i> On-Stage
        </button>
        <button type="button" class="type-filter-btn" id="typeFilterOff" onclick="filterByType('OFF_STAGE');">
          <i class="bi bi-pencil-square text-primary"></i> Off-Stage
        </button>
        <button type="button" class="type-filter-btn" id="typeFilterGroup" onclick="filterByType('GROUP');">
          <i class="bi bi-people text-purple"></i> Group (G)
        </button>
        <button type="button" class="type-filter-btn" id="typeFilterSingle" onclick="filterByType('SINGLE');">
          <i class="bi bi-person text-dark"></i> Single (S)
        </button>
      </div>
    </div>

    <!-- Table Container -->
    <?php if (!empty($all_data_empty_flag = empty($allot_data))) { ?>
      <div class="empty-records-box">
        <div class="empty-records-icon"><i class="bi bi-calendar-x"></i></div>
        <div class="empty-records-title">No Stage Allotment Data Available</div>
        <div class="empty-records-desc">Please verify that participants are registered and items are scheduled for this festival.</div>
      </div>
    <?php } else { ?>
      <div class="table-responsive-custom">
        <table class="duration-data-table" id="durationTable">
          <thead>
            <tr>
              <th width="5%" class="text-center">Sl.No</th>
              <th width="8%">Item Code</th>
              <th width="28%">Item / Event Name</th>
              <th width="16%">Festival Category</th>
              <th width="10%">Type</th>
              <th width="10%" class="text-center">Entries</th>
              <th width="10%">Unit Time</th>
              <th width="13%">Total Duration</th>
            </tr>
          </thead>
          <tbody id="durationTableBody">
            <?php 
            $i = 1;
            foreach ($allot_data as $row) {
                $code = $row['item_code'] ?? '';
                $name = $row['item_name'] ?? '';
                $fest_name = $row['fest_name'] ?? '';
                $fest_id = $row['fest_id'] ?? '';
                $pdcount = (int)($row['pdcount'] ?? 0);
                $max_time = (float)($row['max_time'] ?? 0);
                $time_type = strtoupper($row['time_type'] ?? 'M');
                $is_off_stage = ($row['is_off_stage'] == '1' || strtoupper($row['is_off_stage'] ?? '') == 'Y');
                $item_type = strtoupper($row['item_type'] ?? 'S');

                $unit_mins = ($time_type === 'H') ? ($max_time * 60) : $max_time;
                $row_total_mins = $unit_mins * $pdcount;

                $row_hours = floor($row_total_mins / 60);
                $row_rem_mins = round($row_total_mins % 60);
                $formatted_row_duration = ($row_hours > 0 ? "{$row_hours}h " : "") . "{$row_rem_mins}m";
            ?>
              <tr class="duration-row" 
                  data-fest-id="<?php echo esc($fest_id); ?>"
                  data-item-code="<?php echo strtolower(esc($code)); ?>"
                  data-item-name="<?php echo strtolower(esc($name)); ?>"
                  data-fest-name="<?php echo strtolower(esc($fest_name)); ?>"
                  data-is-off="<?php echo $is_off_stage ? '1' : '0'; ?>"
                  data-item-type="<?php echo esc($item_type); ?>"
                  data-pdcount="<?php echo $pdcount; ?>"
                  data-total-mins="<?php echo $row_total_mins; ?>">
                
                <td class="text-center">
                  <span class="badge bg-light text-secondary border px-2 py-1 rounded fw-bold">
                    <?php echo str_pad((string)$i, 2, '0', STR_PAD_LEFT); ?>
                  </span>
                </td>

                <td>
                  <span class="item-code-badge"><?php echo esc($code); ?></span>
                </td>

                <td>
                  <div class="fw-semibold text-dark"><?php echo esc($name); ?></div>
                  <div class="d-flex align-items-center gap-2 mt-1">
                    <?php if ($is_off_stage) { ?>
                      <span class="stage-type-pill stage-off">
                        <i class="bi bi-pencil-square"></i> Off-Stage
                      </span>
                    <?php } else { ?>
                      <span class="stage-type-pill stage-on">
                        <i class="bi bi-soundwave"></i> On-Stage
                      </span>
                    <?php } ?>
                  </div>
                </td>

                <td>
                  <span class="fest-badge">
                    <i class="bi bi-mortarboard text-primary"></i> <?php echo esc($fest_name); ?>
                  </span>
                </td>

                <td>
                  <?php if ($item_type === 'G') { ?>
                    <span class="type-pill type-pill-group">
                      <i class="bi bi-people-fill"></i> Group (G)
                    </span>
                  <?php } else { ?>
                    <span class="type-pill type-pill-single">
                      <i class="bi bi-person-fill"></i> Single (S)
                    </span>
                  <?php } ?>
                </td>

                <td class="text-center">
                  <span class="badge bg-light text-dark border px-2 py-1 rounded fw-bold">
                    <i class="bi bi-person text-secondary me-1"></i><?php echo $pdcount; ?>
                  </span>
                </td>

                <td>
                  <span class="text-muted small fw-semibold">
                    <?php echo $max_time; ?> <?php echo ($time_type === 'H' ? 'hrs' : 'mins'); ?>
                  </span>
                </td>

                <td>
                  <div class="duration-calc-badge" title="<?php echo $row_total_mins; ?> total minutes">
                    <i class="bi bi-stopwatch"></i>
                    <span><?php echo $formatted_row_duration; ?></span>
                    <span class="text-muted small">(<?php echo round($row_total_mins); ?>m)</span>
                  </div>
                </td>

              </tr>
            <?php $i++; } ?>
          </tbody>
        </table>
      </div>

      <!-- No Matching Filter Results Box -->
      <div class="empty-records-box" id="noResultsBox" style="display:none;">
        <div class="empty-records-icon"><i class="bi bi-search"></i></div>
        <div class="empty-records-title">No Matching Events Found</div>
        <div class="empty-records-desc">Try clearing your search query or selecting a different filter.</div>
        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-3" onclick="clearAllFilters();">
          <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
        </button>
      </div>

      <!-- Table Footer -->
      <div class="content-box-footer">
        <div>
          Showing <b id="visibleCountText"><?php echo $total_items; ?></b> of <b><?php echo $total_items; ?></b> events
        </div>
        <div class="d-flex align-items-center gap-3">
          <span>Filtered Total: <b class="text-primary" id="filteredTotalDuration"><?php echo $formatted_total_duration; ?></b></span>
        </div>
      </div>

    <?php } ?>

  </div>

</div>

<!-- Hidden PDF Export Form -->
<?php echo form_open('district/report/prereportpdf/stageallot_duration', array('id' => 'formExportPdf', 'target' => '_blank', 'style' => 'display:none;')); ?>
  <input type="hidden" name="cmbFestType" id="pdfFestType" value="<?php echo esc($selected_fest); ?>" />
  <input type="hidden" name="cmbSubDistrict" id="pdfSubDistrict" value="<?php echo esc($selected_subdistrict); ?>" />
<?php echo form_close(); ?>

<script>
var currentFestival = '<?php echo esc($selected_fest); ?>';
var currentSubDistrict = '<?php echo esc($selected_subdistrict); ?>';
var currentTypeFilter = 'ALL';

function onSubDistrictChange(subDistCode) {
  currentSubDistrict = subDistCode;
  var subDistInput = document.getElementById('pdfSubDistrict');
  if (subDistInput) subDistInput.value = subDistCode;

  var url = new URL(window.location.href);
  url.searchParams.set('fest', currentFestival);
  url.searchParams.set('sub_district', subDistCode);
  window.location.href = url.toString();
}

// Festival Pill Selection
function filterByFestival(festId, festName) {
  currentFestival = festId;
  document.getElementById('pdfFestType').value = festId;

  var pills = document.querySelectorAll('.filter-pill-btn');
  pills.forEach(function(pill) {
    if (pill.getAttribute('data-fest-id') === festId) {
      pill.classList.add('active');
    } else {
      pill.classList.remove('active');
    }
  });

  var disp = document.getElementById('activeCategoryDisplay');
  if (disp) disp.innerText = festName;

  applyCombinedFilters();
}

// Type Filter Selection
function filterByType(typeKey) {
  currentTypeFilter = typeKey;

  var buttons = document.querySelectorAll('.type-filter-btn');
  buttons.forEach(function(btn) { btn.classList.remove('active'); });

  if (typeKey === 'ALL') document.getElementById('typeFilterAll')?.classList.add('active');
  else if (typeKey === 'ON_STAGE') document.getElementById('typeFilterOn')?.classList.add('active');
  else if (typeKey === 'OFF_STAGE') document.getElementById('typeFilterOff')?.classList.add('active');
  else if (typeKey === 'GROUP') document.getElementById('typeFilterGroup')?.classList.add('active');
  else if (typeKey === 'SINGLE') document.getElementById('typeFilterSingle')?.classList.add('active');

  applyCombinedFilters();
}

// Search Input Handlers
function handleTableSearch() {
  var query = document.getElementById('durationTableSearch').value.trim();
  var clearBtn = document.getElementById('searchClearBtn');
  if (clearBtn) clearBtn.style.display = (query.length > 0) ? 'block' : 'none';
  applyCombinedFilters();
}

function clearTableSearch() {
  var input = document.getElementById('durationTableSearch');
  if (input) input.value = '';
  var clearBtn = document.getElementById('searchClearBtn');
  if (clearBtn) clearBtn.style.display = 'none';
  applyCombinedFilters();
}

function clearAllFilters() {
  clearTableSearch();
  filterByFestival('ALL', 'All Festivals');
  filterByType('ALL');
}

// Combined Filter Engine
function applyCombinedFilters() {
  var query = (document.getElementById('durationTableSearch')?.value || '').toLowerCase().trim();
  var rows = document.querySelectorAll('.duration-row');

  var visibleCount = 0;
  var visibleParticipants = 0;
  var visibleMinutes = 0;
  var onStageCount = 0;
  var offStageCount = 0;

  rows.forEach(function(row) {
    var rowFestId = row.getAttribute('data-fest-id') || '';
    var code = row.getAttribute('data-item-code') || '';
    var name = row.getAttribute('data-item-name') || '';
    var festName = row.getAttribute('data-fest-name') || '';
    var isOff = row.getAttribute('data-is-off') || '0';
    var itemType = row.getAttribute('data-item-type') || 'S';
    var pdcount = parseInt(row.getAttribute('data-pdcount') || '0', 10);
    var totalMins = parseFloat(row.getAttribute('data-total-mins') || '0');

    // 1. Festival Match
    var matchesFest = (currentFestival === 'ALL' || rowFestId === currentFestival);

    // 2. Search Query Match
    var matchesSearch = (code.indexOf(query) !== -1 || name.indexOf(query) !== -1 || festName.indexOf(query) !== -1);

    // 3. Type Match
    var matchesType = true;
    if (currentTypeFilter === 'ON_STAGE') matchesType = (isOff === '0');
    else if (currentTypeFilter === 'OFF_STAGE') matchesType = (isOff === '1');
    else if (currentTypeFilter === 'GROUP') matchesType = (itemType === 'G');
    else if (currentTypeFilter === 'SINGLE') matchesType = (itemType === 'S');

    if (matchesFest && matchesSearch && matchesType) {
      row.style.display = '';
      visibleCount++;
      visibleParticipants += pdcount;
      visibleMinutes += totalMins;

      if (isOff === '1') offStageCount++;
      else onStageCount++;
    } else {
      row.style.display = 'none';
    }
  });

  // Toggle Table vs Empty State
  var tableEl = document.getElementById('durationTable');
  var noResultsBox = document.getElementById('noResultsBox');
  if (tableEl && noResultsBox) {
    if (visibleCount === 0 && rows.length > 0) {
      tableEl.style.display = 'none';
      noResultsBox.style.display = 'block';
    } else {
      tableEl.style.display = '';
      noResultsBox.style.display = 'none';
    }
  }

  // Format Total Duration
  var hours = Math.floor(visibleMinutes / 60);
  var remMins = Math.round(visibleMinutes % 60);
  var formattedDuration = (hours > 0 ? hours + 'h ' : '') + remMins + 'm';

  // Update Dynamic Counters
  document.getElementById('visibleCountText').innerText = visibleCount;
  document.getElementById('itemCountBadge').innerText = visibleCount + ' Events';
  document.getElementById('filteredTotalDuration').innerText = formattedDuration;

  // Update Top KPI Cards
  document.getElementById('kpiTotalItems').innerText = visibleCount;
  document.getElementById('kpiTotalParticipants').innerText = visibleParticipants;
  document.getElementById('kpiTotalDuration').innerText = formattedDuration;
  document.getElementById('kpiStageSplit').innerHTML = '<span class="text-success">' + onStageCount + ' On</span> / <span class="text-primary">' + offStageCount + ' Off</span>';
}

// PDF Form Submission
function submitPdfReport() {
  var form = document.getElementById('formExportPdf');
  if (form) {
    form.submit();
  }
}
</script>
