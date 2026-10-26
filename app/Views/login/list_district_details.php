<?php
/**
 * Tamil Kalolsavam - Statewide District Progress & School Directory
 * Modern SaaS Administrative Dashboard
 */
$Login_Model = model('App\Models\Login\Login_Model');
$district_list = $district_details ?? [];

// Aggregate metrics across all 14 revenue districts
$enriched_districts = [];
$grand_total_schools = 0;
$grand_data_entered = 0;
$grand_data_not_entered = 0;
$grand_confirmed = 0;
$grand_not_confirmed = 0;
$active_districts_count = 0;

if (is_array($district_list)) {
    foreach ($district_list as $dist) {
        $code = (int)($dist['rev_district_code'] ?? 0);
        $stats = $Login_Model->get_district_school_details($code);
        $total_sch = (int)($stats['total_school'] ?? 0);
        $entered = (int)($stats['data_entered'] ?? 0);
        $conf = (int)($stats['confirmed'] ?? 0);
        $not_entered = max(0, $total_sch - $entered);
        $not_conf = max(0, $total_sch - $conf);

        $entry_pct = $total_sch > 0 ? round(($entered / $total_sch) * 100) : 0;
        $conf_pct = $total_sch > 0 ? round(($conf / $total_sch) * 100) : 0;

        if ($total_sch > 0) {
            $active_districts_count++;
        }

        $grand_total_schools += $total_sch;
        $grand_data_entered += $entered;
        $grand_data_not_entered += $not_entered;
        $grand_confirmed += $conf;
        $grand_not_confirmed += $not_conf;

        $enriched_districts[] = [
            'code' => $code,
            'name' => $dist['rev_district_name'] ?? 'District ' . $code,
            'total_schools' => $total_sch,
            'data_entered' => $entered,
            'data_not_entered' => $not_entered,
            'confirmed' => $conf,
            'not_confirmed' => $not_conf,
            'entry_pct' => $entry_pct,
            'conf_pct' => $conf_pct,
        ];
    }
}
$total_districts = count($enriched_districts);
$overall_entry_pct = $grand_total_schools > 0 ? round(($grand_data_entered / $grand_total_schools) * 100) : 0;
$overall_conf_pct = $grand_total_schools > 0 ? round(($grand_confirmed / $grand_total_schools) * 100) : 0;
?>

<style>
/* Modern SaaS Styling for District Details */
:root {
  --admin-primary: #2563eb;
  --admin-primary-dark: #1d4ed8;
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
.kpi-districts::before { background: linear-gradient(90deg, #475569, #334155); }
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

/* District Avatar */
.dist-avatar {
  width: 36px;
  height: 36px;
  min-width: 36px;
  border-radius: 10px;
  background: linear-gradient(135deg, #eff6ff, #dbeafe);
  color: #2563eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.8rem;
  border: 1px solid #bfdbfe;
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
  color: #2563eb;
  background-color: #eff6ff;
  border: 1px solid #bfdbfe;
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
  background-color: #2563eb;
  border-color: #2563eb;
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
  .dist-avatar {
    display: none !important;
  }
}
</style>

<div class="container-fluid px-0">

  <!-- Print-Only Header -->
  <div class="print-only-header d-none">
    <div class="text-center pb-2 border-bottom mb-3">
      <h3 class="fw-bold mb-1">TAMIL KALOLSAVAM 2025</h3>
      <h5 class="text-secondary mb-1">Statewide Revenue District School Registration &amp; Confirmation Status</h5>
      <p class="small text-muted mb-0">Generated on <?php echo date('d M Y, h:i A'); ?></p>
    </div>
  </div>

  <!-- 1. Hero Header & Global Actions Bar -->
  <div class="hero-header-card mb-3 no-print">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; background: linear-gradient(135deg, #0284c7, #0369a1);">
          <i class="bi bi-buildings-fill fs-4"></i>
        </div>
        <div>
          <div class="d-flex align-items-center gap-2 mb-0.5">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5 text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">
              Statewide Directorate
            </span>
            <span class="text-muted small">&bull;</span>
            <span class="text-muted small" style="font-size: 0.76rem;">District Progress Matrix</span>
          </div>
          <h4 class="fw-bold text-dark mb-0" style="font-size: 1.35rem; letter-spacing: -0.3px;">
            Revenue District School Progress &amp; Directory
          </h4>
          <p class="text-muted small mb-0 mt-0.5" style="font-size: 0.8rem;">
            Real-time school registration, data entry completion rates, and confirmation metrics across all 14 revenue districts.
          </p>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="d-flex align-items-center gap-2">
        <!-- Print Official Report Trigger -->
        <button type="button" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-xs" onclick="window.print();" title="Print Statewide District Report">
          <i class="bi bi-printer-fill text-dark"></i>
          <span>Print Report</span>
        </button>

        <!-- Live Total Counter Badge -->
        <span class="badge bg-light text-muted border rounded-pill px-3 py-2 font-monospace" style="font-size: 0.8rem;">
          <?php echo $total_districts; ?> Districts &bull; <?php echo $grand_total_schools; ?> Schools
        </span>
      </div>
    </div>
  </div>

  <!-- 2. Borderless KPI Metric Stat Cards -->
  <div class="row g-2.5 mb-3 kpi-row no-print">
    <!-- Total Districts -->
    <div class="col-6 col-md">
      <div class="kpi-tile kpi-districts" title="Total Revenue Districts">
        <div class="d-flex align-items-center gap-3">
          <div class="kpi-icon-box bg-secondary-subtle text-secondary">
            <i class="bi bi-geo-alt-fill fs-5"></i>
          </div>
          <div class="overflow-hidden">
            <div class="text-muted fw-bold text-truncate" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">Districts</div>
            <div class="fw-bolder text-dark lh-1 mt-1" style="font-size: 1.35rem;"><?php echo $total_districts; ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Registered Schools -->
    <div class="col-6 col-md">
      <div class="kpi-tile kpi-schools" title="Total Registered Schools">
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

  <!-- Hidden Form for Sub-District Navigation Drill-Down -->
  <?php echo form_open('welcome/sub_district_details', array('id' => 'list_district', 'class' => 'm-0 p-0')); ?>
  <input type="hidden" name="sel_district_id" id="sel_district_id" value="" />
  <?php echo form_close(); ?>

  <!-- 3. Revenue District Table Card -->
  <div class="roster-card mb-4">
    <!-- Card Header with Instant Live Search -->
    <div class="card-header no-print">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-table text-primary fs-5"></i>
          <span class="fw-bold text-dark" style="font-size: 0.95rem;">Revenue District Directory &amp; Progress Tracking</span>
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5 ms-1" style="font-size: 0.72rem;">
            <?php echo $total_districts; ?> Districts
          </span>
        </div>

        <!-- Instant Search Bar -->
        <div class="d-flex align-items-center gap-2">
          <div class="input-group input-group-sm" style="width: 270px;">
            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-pill ps-3">
              <i class="bi bi-search small"></i>
            </span>
            <input type="text" id="tableSearch" class="form-control border-start-0 rounded-end-pill small font-monospace shadow-none" placeholder="Instant search district..." onkeyup="filterDistrictTable();" autocomplete="off" />
          </div>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table align-middle mb-0" id="districtTable" style="font-size: 0.88rem;">
        <thead>
          <tr>
            <th scope="col" class="text-center py-2.5 ps-3" style="width: 50px;">#</th>
            <th scope="col" class="py-2.5" style="width: 220px;">Revenue District</th>
            <th scope="col" class="text-center py-2.5" style="width: 130px;">Total Schools</th>
            <th scope="col" class="py-2.5" style="width: 220px;">Data Entry Progress</th>
            <th scope="col" class="py-2.5" style="width: 220px;">Confirmation Status</th>
            <th scope="col" class="text-center py-2.5" style="width: 140px;">Activity Status</th>
            <th scope="col" class="text-center py-2.5 pe-3 no-print" style="width: 150px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($enriched_districts)): ?>
            <?php foreach ($enriched_districts as $idx => $d): 
              $isZero = ($d['total_schools'] === 0);
              $isComplete = (!$isZero && $d['confirmed'] === $d['total_schools']);
              $isInProgress = (!$isZero && $d['data_entered'] > 0);
            ?>
              <tr class="district-row" data-search="<?php echo strtolower(esc($d['name'] . ' ' . $d['code'])); ?>">
                <!-- Index -->
                <td class="text-center text-muted small font-monospace py-3 ps-3">
                  <?php echo $idx + 1; ?>
                </td>

                <!-- District Name & Identifier -->
                <td class="py-3">
                  <div class="d-flex align-items-center gap-2.5">
                    <div class="dist-avatar shadow-xs" title="District Code: <?php echo esc($d['code']); ?>">
                      <?php echo esc($d['code']); ?>
                    </div>
                    <div>
                      <a href="javascript:void(0);" onclick="fncListSubDistrictDetails('<?php echo esc($d['code']); ?>'); return false;" class="fw-bold text-dark text-decoration-none d-flex align-items-center gap-1.5" title="Click to view Sub-districts of <?php echo esc($d['name']); ?>">
                        <span><?php echo esc($d['name']); ?></span>
                        <i class="bi bi-chevron-right small text-muted no-print" style="font-size: 0.72rem;"></i>
                      </a>
                      <span class="text-muted small d-block font-monospace" style="font-size: 0.72rem;">Code: <?php echo esc($d['code']); ?></span>
                    </div>
                  </div>
                </td>

                <!-- Total Schools -->
                <td class="text-center py-3">
                  <?php if ($isZero): ?>
                    <span class="badge bg-light text-muted border px-2.5 py-1 font-monospace">0</span>
                  <?php else: ?>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fw-bold font-monospace">
                      <?php echo $d['total_schools']; ?>
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
                        <i class="bi bi-check-circle-fill"></i> Entered: <?php echo $d['data_entered']; ?>
                      </span>
                      <span class="metric-tag tag-pending">
                        <i class="bi bi-hourglass-split"></i> Pending: <?php echo $d['data_not_entered']; ?>
                      </span>
                    </div>
                    <div class="progress progress-slim">
                      <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $d['entry_pct']; ?>%;" aria-valuenow="<?php echo $d['entry_pct']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="text-muted small mt-0.5 text-end font-monospace" style="font-size: 0.68rem;">
                      <?php echo $d['entry_pct']; ?>% completed
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
                        <i class="bi bi-patch-check-fill"></i> Conf: <?php echo $d['confirmed']; ?>
                      </span>
                      <span class="metric-tag tag-unconfirmed">
                        Unconf: <?php echo $d['not_confirmed']; ?>
                      </span>
                    </div>
                    <div class="progress progress-slim">
                      <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $d['conf_pct']; ?>%;" aria-valuenow="<?php echo $d['conf_pct']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="text-muted small mt-0.5 text-end font-monospace" style="font-size: 0.68rem;">
                      <?php echo $d['conf_pct']; ?>% confirmed
                    </div>
                  <?php endif; ?>
                </td>

                <!-- Activity Status -->
                <td class="text-center py-3">
                  <?php if ($isZero): ?>
                    <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1 small fw-normal">
                      No Schools
                    </span>
                  <?php elseif ($isComplete): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small fw-semibold">
                      <i class="bi bi-check-all me-1"></i>Finalized
                    </span>
                  <?php elseif ($isInProgress): ?>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-semibold">
                      <i class="bi bi-activity me-1"></i>Active
                    </span>
                  <?php else: ?>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 small fw-medium">
                      <i class="bi bi-clock me-1"></i>Pending
                    </span>
                  <?php endif; ?>
                </td>

                <!-- Actions -->
                <td class="text-center py-3 pe-3 no-print">
                  <button type="button" class="btn btn-drilldown shadow-xs" onclick="fncListSubDistrictDetails('<?php echo esc($d['code']); ?>');" title="Drill-down to view sub-districts">
                    <i class="bi bi-folder2-open"></i>
                    <span>View Sub-Districts</span>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-2 text-secondary d-block mb-2"></i>
                <h6 class="fw-bold text-dark mb-1">No revenue district records found</h6>
                <p class="small text-muted mb-0">Check system configuration in revenue district master.</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
        <tfoot>
          <tr>
            <td class="text-center">#</td>
            <td>Statewide Grand Total (14 Districts)</td>
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
                <?php echo $active_districts_count; ?> Active
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
 * Modern Drill-down Navigation to Sub-District Details
 */
function fncListSubDistrictDetails(id) {
  var sel = document.getElementById('sel_district_id');
  if (sel) {
    sel.value = id;
  }
  var form = document.getElementById('list_district');
  if (form) {
    var basePath = (typeof path !== 'undefined') ? path : '<?php echo site_url("/"); ?>';
    form.action = basePath + 'welcome/sub_district_details';
    form.submit();
  }
}
window.fncListSubDistrictDetails = fncListSubDistrictDetails;

/**
 * Instant Client-Side Live Search
 */
function filterDistrictTable() {
  var searchInput = document.getElementById('tableSearch');
  var query = searchInput ? searchInput.value.toLowerCase().trim() : '';
  var rows = document.querySelectorAll('#districtTable tbody tr.district-row');

  rows.forEach(function(row) {
    var searchData = row.getAttribute('data-search') || '';
    if (query === '' || searchData.indexOf(query) > -1) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}
</script>