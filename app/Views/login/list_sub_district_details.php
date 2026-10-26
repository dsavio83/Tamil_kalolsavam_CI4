<?php
/**
 * Tamil Kalolsavam - Sub-District Progress & School Directory
 * Modern SaaS Administrative Dashboard
 */
$Login_Model = $Login_Model ?? ($this->Login_Model ?? model('App\Models\Login\Login_Model'));
$sub_district_list = $sub_district_details ?? [];
$district_name = $district_name ?? 'District';
$district_code = (int)($district_code ?? 0);
$all_districts = $all_districts ?? [];
$user_type = (int)(session()->get('USER_TYPE') ?? 0);

// Aggregate metrics across all sub-districts of the selected revenue district
$enriched_subdists = [];
$grand_total_schools = 0;
$grand_cluster_schools = 0;
$grand_data_entered = 0;
$grand_data_not_entered = 0;
$grand_confirmed = 0;
$grand_not_confirmed = 0;
$active_subdists_count = 0;
$locked_subdists_count = 0;

if (is_array($sub_district_list)) {
    foreach ($sub_district_list as $sd) {
        $sd_code = (int)($sd['sub_district_code'] ?? 0);
        $sd_name = $sd['sub_district_name'] ?? ('Sub District ' . $sd_code);
        
        $stats = $Login_Model->get_sub_school_details($sd_code);
        $total_sch = (int)($stats['total_school'] ?? 0);
        $cluster_sch = (int)($stats['cluster_school'] ?? 0);
        $entered = (int)($stats['data_entered'] ?? 0);
        $conf = (int)($stats['confirmed'] ?? 0);
        $is_locked = ($stats['confirm_data_entry'] ?? 'N') === 'Y';
        
        $not_entered = max(0, $total_sch - $entered);
        $not_conf = max(0, $total_sch - $conf);

        $entry_pct = $total_sch > 0 ? round(($entered / $total_sch) * 100) : 0;
        $conf_pct = $total_sch > 0 ? round(($conf / $total_sch) * 100) : 0;

        if ($total_sch > 0) {
            $active_subdists_count++;
        }
        if ($is_locked) {
            $locked_subdists_count++;
        }

        $grand_total_schools += $total_sch;
        $grand_cluster_schools += $cluster_sch;
        $grand_data_entered += $entered;
        $grand_data_not_entered += $not_entered;
        $grand_confirmed += $conf;
        $grand_not_confirmed += $not_conf;

        $enriched_subdists[] = [
            'code' => $sd_code,
            'name' => $sd_name,
            'total_schools' => $total_sch,
            'cluster_schools' => $cluster_sch,
            'data_entered' => $entered,
            'data_not_entered' => $not_entered,
            'confirmed' => $conf,
            'not_confirmed' => $not_conf,
            'is_locked' => $is_locked,
            'entry_pct' => $entry_pct,
            'conf_pct' => $conf_pct,
        ];
    }
}
$total_subdistricts = count($enriched_subdists);
$overall_entry_pct = $grand_total_schools > 0 ? round(($grand_data_entered / $grand_total_schools) * 100) : 0;
$overall_conf_pct = $grand_total_schools > 0 ? round(($grand_confirmed / $grand_total_schools) * 100) : 0;
?>

<style>
/* Modern SaaS Styling for Sub-District Details */
:root {
  --admin-primary: #0284c7;
  --admin-primary-dark: #0369a1;
  --admin-border-color: #e2e8f0;
}

.hero-header-card {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 4px 15px -3px rgba(15, 23, 42, 0.05);
  padding: 1.25rem 1.5rem;
}

/* Borderless KPI Tiles */
.kpi-tile {
  background: #ffffff;
  border: none !important;
  border-radius: 14px;
  padding: 1rem 1.15rem;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  position: relative;
  overflow: hidden;
  user-select: none;
  box-shadow: 0 2px 10px -2px rgba(15, 23, 42, 0.06);
}
.kpi-tile:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 22px -4px rgba(15, 23, 42, 0.1);
}
.kpi-tile::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
}
.kpi-subdists::before { background: linear-gradient(90deg, #475569, #334155); }
.kpi-schools::before { background: linear-gradient(90deg, #0284c7, #0369a1); }
.kpi-entered::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
.kpi-pending::before { background: linear-gradient(90deg, #f43f5e, #e11d48); }
.kpi-confirmed::before { background: linear-gradient(90deg, #10b981, #059669); }

.kpi-icon-box {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* Roster Card & Table */
.roster-card {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 4px 20px -3px rgba(15, 23, 42, 0.05);
  overflow: hidden;
}

.roster-card .card-header {
  background: #ffffff;
  border-bottom: 1px solid #f1f5f9;
  padding: 1.1rem 1.35rem;
}

.roster-card .table thead th {
  background-color: #f8fafc;
  color: #475569;
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  border-bottom: 1px solid #e2e8f0;
  padding: 11px 14px;
}

.roster-card .table tbody td {
  padding: 13px 14px;
  border-bottom: 1px solid #f1f5f9;
}

.roster-card .table tbody tr {
  transition: background-color 0.15s ease;
}
.roster-card .table tbody tr:hover {
  background-color: #f8fafc;
}

.roster-card .table tfoot td {
  background-color: #f8fafc;
  border-top: 2px solid #cbd5e1;
  padding: 12px 14px;
  font-weight: 700;
}

/* Sub-District Avatar */
.subdist-avatar {
  width: 36px;
  height: 36px;
  min-width: 36px;
  border-radius: 10px;
  background: linear-gradient(135deg, #e0f2fe, #bae6fd);
  color: #0284c7;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.8rem;
  border: 1px solid #7dd3fc;
}

/* Slim Progress Bar */
.progress-slim {
  height: 6px;
  border-radius: 3px;
  background-color: #f1f5f9;
  overflow: hidden;
}

.metric-tag {
  font-family: var(--bs-font-monospace);
  font-size: 0.74rem;
  padding: 2px 7px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  gap: 3px;
}
.tag-entered { background-color: #fef3c7; color: #b45309; }
.tag-pending { background-color: #ffe4e6; color: #e11d48; }
.tag-confirmed { background-color: #d1fae5; color: #047857; }
.tag-unconfirmed { background-color: #f1f5f9; color: #64748b; }

/* Drill-Down Action Button */
.btn-drilldown {
  color: #0284c7;
  background-color: #f0f9ff;
  border: 1px solid #bae6fd;
  border-radius: 20px;
  padding: 4px 12px;
  font-size: 0.78rem;
  font-weight: 600;
  transition: all 0.15s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}
.btn-drilldown:hover {
  color: #ffffff;
  background-color: #0284c7;
  border-color: #0284c7;
}

/* Print Stylesheet */
@media print {
  .no-print,
  .hero-header-card,
  .kpi-row,
  .btn,
  .input-group,
  #tableSearch,
  .navbar,
  .sidebar,
  header,
  footer {
    display: none !important;
  }
  .print-only-header {
    display: block !important;
    margin-bottom: 20px;
  }
  body {
    background: #ffffff !important;
    color: #000000 !important;
  }
  .roster-card {
    box-shadow: none !important;
    border: none !important;
  }
  table {
    width: 100% !important;
    border-collapse: collapse !important;
    font-size: 11pt !important;
  }
  th, td {
    border: 1px solid #333333 !important;
    padding: 6px 8px !important;
    color: #000000 !important;
  }
  .subdist-avatar {
    display: none !important;
  }
}
</style>

<div class="container-fluid px-0">

  <!-- Print-Only Header -->
  <div class="print-only-header d-none">
    <div class="text-center pb-2 border-bottom mb-3">
      <h3 class="fw-bold mb-1">TAMIL KALOLSAVAM 2025</h3>
      <h5 class="text-secondary mb-1"><?php echo esc($district_name); ?> Revenue District &bull; Sub-District School Registration &amp; Confirmation Status</h5>
      <p class="small text-muted mb-0">Generated on <?php echo date('d M Y, h:i A'); ?></p>
    </div>
  </div>

  <!-- 1. Hero Header & Global Actions Bar -->
  <div class="hero-header-card mb-3 no-print">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; background: linear-gradient(135deg, #0ea5e9, #0284c7);">
          <i class="bi bi-pin-map-fill fs-4"></i>
        </div>
        <div>
          <div class="d-flex align-items-center gap-2 mb-0.5">
            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2.5 py-0.5 text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">
              Sub-District Directorate
            </span>
            <span class="text-muted small">&bull;</span>
            <span class="text-muted small fw-semibold" style="font-size: 0.76rem;"><?php echo esc($district_name); ?> District</span>
          </div>
          <h4 class="fw-bold text-dark mb-0" style="font-size: 1.35rem; letter-spacing: -0.3px;">
            Sub-District School Progress &amp; Directory
          </h4>
          <p class="text-muted small mb-0 mt-0.5" style="font-size: 0.8rem;">
            Real-time monitoring of school registrations, cluster assignments, data entry completion rates, and confirmation metrics in <strong><?php echo esc($district_name); ?></strong>.
          </p>
        </div>
      </div>

      <!-- Action Buttons & Selectors -->
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <!-- Back to Districts Button -->
        <?php if ($user_type === 0 || $user_type === 1): ?>
          <a href="<?php echo site_url('welcome/district_details'); ?>" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-xs" title="Return to Statewide District Matrix">
            <i class="bi bi-arrow-left"></i>
            <span>Districts</span>
          </a>
        <?php endif; ?>

        <!-- District Switcher Dropdown for State / IT Admin -->
        <?php if (!empty($all_districts) && ($user_type === 0 || $user_type === 1)): ?>
          <div class="d-inline-flex align-items-center gap-1.5 bg-light border rounded-pill px-2.5 py-1.5 shadow-xs">
            <i class="bi bi-buildings text-muted small"></i>
            <select class="form-select form-select-sm border-0 bg-transparent py-0 ps-1 pe-4 shadow-none fw-semibold text-dark" style="font-size: 0.8rem; cursor: pointer;" onchange="switchDistrict(this.value);" title="Switch to another Revenue District">
              <?php foreach ($all_districts as $d): ?>
                <option value="<?php echo esc($d['rev_district_code']); ?>" <?php echo ((int)$d['rev_district_code'] === $district_code) ? 'selected' : ''; ?>>
                  <?php echo esc($d['rev_district_name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        <?php endif; ?>

        <!-- Print Official Report Trigger -->
        <button type="button" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-xs" onclick="window.print();" title="Print Sub-District Status Report">
          <i class="bi bi-printer-fill text-dark"></i>
          <span>Print Report</span>
        </button>

        <!-- Live Total Counter Badge -->
        <span class="badge bg-light text-muted border rounded-pill px-3 py-2 font-monospace" style="font-size: 0.8rem;">
          <?php echo $total_subdistricts; ?> Sub-Districts &bull; <?php echo $grand_total_schools; ?> Schools
        </span>
      </div>
    </div>
  </div>

  <!-- 2. Borderless KPI Metric Stat Cards -->
  <div class="row g-2.5 mb-3 kpi-row no-print">
    <!-- Total Sub-Districts -->
    <div class="col-6 col-md">
      <div class="kpi-tile kpi-subdists" title="Total Sub-Districts in this District">
        <div class="d-flex align-items-center gap-3">
          <div class="kpi-icon-box bg-secondary-subtle text-secondary">
            <i class="bi bi-geo-alt-fill fs-5"></i>
          </div>
          <div class="overflow-hidden">
            <div class="text-muted fw-bold text-truncate" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">Sub-Districts</div>
            <div class="fw-bolder text-dark lh-1 mt-1" style="font-size: 1.35rem;"><?php echo $total_subdistricts; ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Registered Schools -->
    <div class="col-6 col-md">
      <div class="kpi-tile kpi-schools" title="Total Registered Schools in District">
        <div class="d-flex align-items-center gap-3">
          <div class="kpi-icon-box bg-info-subtle text-info-emphasis">
            <i class="bi bi-building fs-5"></i>
          </div>
          <div class="overflow-hidden">
            <div class="text-muted fw-bold text-truncate" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Schools</div>
            <div class="fw-bolder text-primary lh-1 mt-1" style="font-size: 1.35rem;"><?php echo $grand_total_schools; ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Entered -->
    <div class="col-6 col-md">
      <div class="kpi-tile kpi-entered" title="Schools that have entered data">
        <div class="d-flex align-items-center gap-3">
          <div class="kpi-icon-box bg-warning-subtle text-warning-emphasis">
            <i class="bi bi-pencil-square fs-5"></i>
          </div>
          <div class="overflow-hidden">
            <div class="text-muted fw-bold text-truncate" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">Data Entered</div>
            <div class="fw-bolder text-warning-emphasis lh-1 mt-1" style="font-size: 1.35rem;"><?php echo $grand_data_entered; ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pending Entry -->
    <div class="col-6 col-md">
      <div class="kpi-tile kpi-pending" title="Schools with data entry pending">
        <div class="d-flex align-items-center gap-3">
          <div class="kpi-icon-box bg-danger-subtle text-danger">
            <i class="bi bi-clock-history fs-5"></i>
          </div>
          <div class="overflow-hidden">
            <div class="text-muted fw-bold text-truncate" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">Entry Pending</div>
            <div class="fw-bolder text-danger lh-1 mt-1" style="font-size: 1.35rem;"><?php echo $grand_data_not_entered; ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Finalized / Confirmed -->
    <div class="col-6 col-md">
      <div class="kpi-tile kpi-confirmed" title="Schools finalized and confirmed">
        <div class="d-flex align-items-center gap-3">
          <div class="kpi-icon-box bg-success-subtle text-success">
            <i class="bi bi-check2-circle fs-5"></i>
          </div>
          <div class="overflow-hidden">
            <div class="text-muted fw-bold text-truncate" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">Confirmed</div>
            <div class="fw-bolder text-success lh-1 mt-1" style="font-size: 1.35rem;"><?php echo $grand_confirmed; ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Hidden Form for Drill-Down Navigation (Compatible with both new and legacy calls) -->
  <?php echo form_open('welcome/cluster_details', array('id' => 'list_sub_district', 'class' => 'm-0 p-0')); ?>
  <input type="hidden" name="sel_sub_district_id" id="sel_sub_district_id" value="" />
  <?php echo form_close(); ?>

  <!-- Secondary Alias Form ID for legacy script compatibility -->
  <form id="list_district" action="<?php echo site_url('welcome/cluster_details'); ?>" method="post" style="display:none;">
    <input type="hidden" name="sel_sub_district_id" id="legacy_sel_sub_district_id" value="" />
  </form>

  <!-- Hidden Form for Switching Revenue District on the fly -->
  <?php echo form_open('welcome/sub_district_details', array('id' => 'districtSwitchForm', 'class' => 'm-0 p-0')); ?>
  <input type="hidden" name="sel_district_id" id="switch_district_id" value="" />
  <?php echo form_close(); ?>

  <!-- 3. Sub-District Table Card -->
  <div class="roster-card mb-4">
    <!-- Card Header with Instant Live Search -->
    <div class="card-header no-print">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-table text-primary fs-5"></i>
          <span class="fw-bold text-dark" style="font-size: 0.95rem;">Sub-District Directory &amp; Operational Matrix</span>
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5 ms-1" style="font-size: 0.72rem;">
            <?php echo $total_subdistricts; ?> Sub-Districts
          </span>
        </div>

        <!-- Instant Search Bar -->
        <div class="d-flex align-items-center gap-2">
          <div class="input-group input-group-sm" style="width: 270px;">
            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-pill ps-3">
              <i class="bi bi-search small"></i>
            </span>
            <input type="text" id="tableSearch" class="form-control border-start-0 rounded-end-pill small font-monospace shadow-none" placeholder="Instant search sub-district..." onkeyup="filterSubDistrictTable();" autocomplete="off" />
          </div>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table align-middle mb-0" id="subDistrictTable" style="font-size: 0.88rem;">
        <thead>
          <tr>
            <th scope="col" class="text-center py-2.5 ps-3" style="width: 50px;">#</th>
            <th scope="col" class="py-2.5" style="width: 220px;">Sub-District Name</th>
            <th scope="col" class="text-center py-2.5" style="width: 130px;">Total Schools</th>
            <th scope="col" class="py-2.5" style="width: 220px;">Data Entry Progress</th>
            <th scope="col" class="py-2.5" style="width: 220px;">Confirmation Status</th>
            <th scope="col" class="text-center py-2.5" style="width: 140px;">Lock Status</th>
            <th scope="col" class="text-center py-2.5 pe-3 no-print" style="width: 150px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($enriched_subdists)): ?>
            <?php foreach ($enriched_subdists as $idx => $sd): 
              $isZero = ($sd['total_schools'] === 0);
              $isComplete = (!$isZero && $sd['confirmed'] === $sd['total_schools']);
              $isInProgress = (!$isZero && $sd['data_entered'] > 0);
            ?>
              <tr class="subdist-row" data-search="<?php echo strtolower(esc($sd['name'] . ' ' . $sd['code'])); ?>">
                <!-- Index -->
                <td class="text-center text-muted small font-monospace py-3 ps-3">
                  <?php echo $idx + 1; ?>
                </td>

                <!-- Sub-District Name & Identifier -->
                <td class="py-3">
                  <div class="d-flex align-items-center gap-2.5">
                    <div class="subdist-avatar shadow-xs" title="Sub-District Code: <?php echo esc($sd['code']); ?>">
                      <?php echo esc($sd['code']); ?>
                    </div>
                    <div>
                      <a href="javascript:void(0);" onclick="fncListClustertDetails('<?php echo esc($sd['code']); ?>'); return false;" class="fw-bold text-dark text-decoration-none d-flex align-items-center gap-1.5" title="Click to view Clusters &amp; Schools of <?php echo esc($sd['name']); ?>">
                        <span><?php echo esc($sd['name']); ?></span>
                        <i class="bi bi-chevron-right small text-muted no-print" style="font-size: 0.72rem;"></i>
                      </a>
                      <span class="text-muted small d-block font-monospace" style="font-size: 0.72rem;">Code: <?php echo esc($sd['code']); ?></span>
                    </div>
                  </div>
                </td>

                <!-- Total Schools -->
                <td class="text-center py-3">
                  <?php if ($isZero): ?>
                    <span class="badge bg-light text-muted border px-2.5 py-1 font-monospace">0</span>
                  <?php else: ?>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fw-bold font-monospace">
                      <?php echo $sd['total_schools']; ?>
                    </span>
                  <?php endif; ?>
                </td>

                <!-- Data Entry Progress -->
                <td class="py-3">
                  <?php if ($isZero): ?>
                    <span class="text-muted small">&mdash;</span>
                  <?php else: ?>
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <span class="metric-tag tag-entered">
                        <i class="bi bi-check-circle-fill"></i> Entered: <?php echo $sd['data_entered']; ?>
                      </span>
                      <span class="metric-tag tag-pending">
                        <i class="bi bi-hourglass-split"></i> Pending: <?php echo $sd['data_not_entered']; ?>
                      </span>
                    </div>
                    <div class="progress progress-slim">
                      <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $sd['entry_pct']; ?>%;" aria-valuenow="<?php echo $sd['entry_pct']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="text-muted small mt-0.5 text-end font-monospace" style="font-size: 0.68rem;">
                      <?php echo $sd['entry_pct']; ?>% completed
                    </div>
                  <?php endif; ?>
                </td>

                <!-- Confirmation Status -->
                <td class="py-3">
                  <?php if ($isZero): ?>
                    <span class="text-muted small">&mdash;</span>
                  <?php else: ?>
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <span class="metric-tag tag-confirmed">
                        <i class="bi bi-patch-check-fill"></i> Conf: <?php echo $sd['confirmed']; ?>
                      </span>
                      <span class="metric-tag tag-unconfirmed">
                        Unconf: <?php echo $sd['not_confirmed']; ?>
                      </span>
                    </div>
                    <div class="progress progress-slim">
                      <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $sd['conf_pct']; ?>%;" aria-valuenow="<?php echo $sd['conf_pct']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="text-muted small mt-0.5 text-end font-monospace" style="font-size: 0.68rem;">
                      <?php echo $sd['conf_pct']; ?>% confirmed
                    </div>
                  <?php endif; ?>
                </td>

                <!-- Data Entry Lock & Activity Status -->
                <td class="text-center py-3">
                  <?php if ($isZero): ?>
                    <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1 small fw-normal">
                      No Schools
                    </span>
                  <?php elseif ($sd['is_locked']): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small fw-semibold" title="Data entry confirmed &amp; locked">
                      <i class="bi bi-lock-fill me-1"></i>Locked
                    </span>
                  <?php else: ?>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 small fw-medium" title="Data entry in progress / open">
                      <i class="bi bi-unlock-fill me-1"></i>Open
                    </span>
                  <?php endif; ?>
                </td>

                <!-- Actions -->
                <td class="text-center py-3 pe-3 no-print">
                  <button type="button" class="btn btn-drilldown shadow-xs" onclick="fncListClustertDetails('<?php echo esc($sd['code']); ?>');" title="Drill-down to view Clusters and Schools">
                    <i class="bi bi-folder2-open"></i>
                    <span>View Clusters</span>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-2 text-secondary d-block mb-2"></i>
                <h6 class="fw-bold text-dark mb-1">No sub-district records found</h6>
                <p class="small text-muted mb-0">No sub-districts are configured under <?php echo esc($district_name); ?> district.</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
        <tfoot>
          <tr>
            <td class="text-center">#</td>
            <td>District Grand Total (<?php echo $total_subdistricts; ?> Sub-Districts)</td>
            <td class="text-center font-monospace text-primary"><?php echo $grand_total_schools; ?></td>
            <td>
              <div class="d-flex align-items-center justify-content-between font-monospace small">
                <span>Entered: <?php echo $grand_data_entered; ?></span>
                <span class="text-danger">Pending: <?php echo $grand_data_not_entered; ?></span>
              </div>
            </td>
            <td>
              <div class="d-flex align-items-center justify-content-between font-monospace small">
                <span class="text-success">Confirmed: <?php echo $grand_confirmed; ?></span>
                <span class="text-muted">Unconf: <?php echo $grand_not_confirmed; ?></span>
              </div>
            </td>
            <td class="text-center">
              <span class="badge bg-dark text-white rounded-pill px-2.5 py-1 small font-monospace">
                <?php echo $active_subdists_count; ?> Active
              </span>
            </td>
            <td class="text-center no-print">
              <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="window.print();">
                <i class="bi bi-printer me-1"></i>Print
              </button>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<script>
/**
 * Drill-down Navigation to Clusters & Schools
 */
function fncListClustertDetails(id) {
  var basePath = (typeof path !== 'undefined') ? path : '<?php echo site_url("/"); ?>';
  if (!basePath.endsWith('/')) basePath += '/';
  var distCode = '<?php echo esc($district_code ?? 9); ?>';
  window.location.href = basePath + 'welcome/cluster_details?sel_district_id=' + encodeURIComponent(distCode) + '&sel_sub_district_id=' + encodeURIComponent(id);
}
window.fncListClustertDetails = fncListClustertDetails;

/**
 * Switch Revenue District on the fly (for State / IT Admin)
 */
function switchDistrict(districtCode) {
  if (!districtCode) return;
  var basePath = (typeof path !== 'undefined') ? path : '<?php echo site_url("/"); ?>';
  if (!basePath.endsWith('/')) basePath += '/';
  window.location.href = basePath + 'welcome/sub_district_details?sel_district_id=' + encodeURIComponent(districtCode);
}
window.switchDistrict = switchDistrict;

/**
 * Instant Client-Side Live Search
 */
function filterSubDistrictTable() {
  var searchInput = document.getElementById('tableSearch');
  var query = searchInput ? searchInput.value.toLowerCase().trim() : '';
  var rows = document.querySelectorAll('#subDistrictTable tbody tr.subdist-row');

  rows.forEach(function(row) {
    var searchData = row.getAttribute('data-search') || '';
    if (query === '' || searchData.indexOf(query) > -1) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}
window.filterSubDistrictTable = filterSubDistrictTable;

/**
 * Legacy Print Helper Compatibility
 */
function printContent(contentId) {
  window.print();
}
window.printContent = printContent;
</script>
