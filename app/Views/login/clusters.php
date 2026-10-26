<?php
/**
 * Tamil Kalolsavam - Cluster Details & Directory
 * Modern SaaS Administrative Dashboard
 * 100% English Labels - Stunning SaaS Design
 */
$district_code = (int)($district_code ?? 0);
$district_name = $district_name ?? 'District';
$is_district_view = !empty($is_district_view);
$subdst = !empty($subdst) ? (int)$subdst : 0;
$sub_district_name = $sub_district_name ?? ($is_district_view ? 'All Sub-Districts' : 'Sub-District');
$all_districts = $all_districts ?? [];
$all_sub_districts = $all_sub_districts ?? [];
$clusters = $clusters ?? [];
$district_summary = $district_summary ?? [];
$district_clusters = $district_clusters ?? [];
$nonclust = $nonclust ?? [];
$val = $val ?? [['ent' => 0, 'fin' => 0]];
$sub_school = $sub_school ?? ['total_school' => 0, 'cluster_school' => 0, 'data_entered' => 0, 'confirmed' => 0];

$tot_school = (int)($sub_school['total_school'] ?? 0);
$clust_school = (int)($sub_school['cluster_school'] ?? 0);
$data_entered = (int)($sub_school['data_entered'] ?? 0);
$confirmed = (int)($sub_school['confirmed'] ?? 0);
$not_entered = max(0, $tot_school - $data_entered);
$not_confirmed = max(0, $tot_school - $confirmed);

$overall_entry_pct = $tot_school > 0 ? round(($data_entered / $tot_school) * 100) : 0;
$overall_conf_pct = $tot_school > 0 ? round(($confirmed / $tot_school) * 100) : 0;

$cluster_totals = 0;
foreach ($clusters as $c) {
    $cluster_totals += (int)($c['total'] ?? 0);
}
$calculated_balance = max(0, ((int)($nonclust[0]['mcode'] ?? $tot_school)) - $cluster_totals);
$non_entered = (int)($val[0]['ent'] ?? 0);
$non_fin = (int)($val[0]['fin'] ?? 0);
$non_pending_entry = max(0, $calculated_balance - $non_entered);
$non_unconfirmed = max(0, $calculated_balance - $non_fin);
$non_conf_pct = $calculated_balance > 0 ? round(($non_fin / $calculated_balance) * 100) : 0;
?>

<style>
/* Modern SaaS Styling for Cluster Directory */
:root {
  --primary-accent: #2563eb;
  --primary-accent-hover: #1d4ed8;
  --emerald-accent: #059669;
  --amber-accent: #d97706;
  --slate-border: #e2e8f0;
  --slate-bg: #f8fafc;
}

.cluster-dashboard-container {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  color: #0f172a;
}

/* Hero Header Card */
.hero-header-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid var(--slate-border);
  box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
  padding: 1.25rem 1.75rem;
}

/* KPI Summary Cards */
.kpi-tile {
  background: #ffffff;
  border-radius: 14px;
  border: 1px solid var(--slate-border);
  padding: 1.15rem 1.25rem;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  position: relative;
  box-shadow: 0 2px 8px -1px rgba(15, 23, 42, 0.04);
}
.kpi-tile:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 24px -4px rgba(15, 23, 42, 0.08);
}
.kpi-tile .icon-wrap {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
}
.kpi-tile .val {
  font-size: 1.85rem;
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.15;
  color: #0f172a;
}
.kpi-tile .lbl {
  font-size: 0.74rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #64748b;
  margin-top: 0.35rem;
  line-height: 1.3;
  min-height: 2.6em;
}
.kpi-tile .sub-text {
  font-size: 0.76rem;
  color: #94a3b8;
  margin-top: 0.2rem;
}
.btn-back-inline {
  display: inline-flex !important;
  flex-direction: row !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 0.45rem !important;
  white-space: nowrap !important;
  flex-shrink: 0 !important;
  height: 38px !important;
  padding: 0 1.1rem !important;
}

/* Dedicated IT Admin Scope Switcher Bar */
.admin-scope-bar {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 0.85rem 1.25rem;
  box-shadow: 0 2px 8px -1px rgba(15, 23, 42, 0.04);
}

/* Modern SaaS Table */
.card-table-wrap {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
  overflow: hidden;
  border: 1px solid var(--slate-border);
}

.cluster-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  margin-bottom: 0;
}
.cluster-table thead th {
  background: #f8fafc;
  color: #475569;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 1rem 1.15rem;
  border-bottom: 2px solid #e2e8f0;
  white-space: nowrap;
}
.cluster-table tbody td {
  padding: 1rem 1.15rem;
  vertical-align: middle;
  border-bottom: 1px solid #f1f5f9;
  font-size: 0.88rem;
  color: #1e293b;
}
.cluster-table tbody tr.cluster-row {
  transition: background-color 0.15s ease;
}
.cluster-table tbody tr.cluster-row:hover td {
  background-color: #f8fafc;
}

/* Distinctive Non-Cluster Row */
.cluster-table tbody tr.nonclust-row td {
  background: #fffbeb !important;
  border-top: 2px solid #fde68a;
  border-bottom: 2px solid #fde68a;
}
.cluster-table tbody tr.nonclust-row:hover td {
  background: #fef3c7 !important;
}

/* Badges */
.badge-pill {
  font-size: 0.78rem;
  font-weight: 700;
  padding: 0.32rem 0.7rem;
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
}
.badge-pill-blue { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
.badge-pill-emerald { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
.badge-pill-amber { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
.badge-pill-slate { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

/* Micro Progress Bar */
.micro-prog-wrap {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  width: 100%;
  max-width: 130px;
}
.micro-prog-bar {
  flex-grow: 1;
  height: 6px;
  background: #e2e8f0;
  border-radius: 6px;
  overflow: hidden;
}
.micro-prog-val {
  height: 100%;
  border-radius: 6px;
  transition: width 0.3s ease;
}
.micro-prog-text {
  font-size: 0.74rem;
  font-weight: 700;
  color: #64748b;
  min-width: 32px;
}

/* Action Buttons */
.btn-view-schools {
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
  padding: 0.4rem 0.95rem;
  font-size: 0.82rem;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.15s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  cursor: pointer;
}
.btn-view-schools:hover {
  background: #2563eb;
  color: #ffffff;
  border-color: #2563eb;
  box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
}

.btn-view-nonclust {
  background: #fef3c7;
  color: #92400e;
  border: 1px solid #fde68a;
  padding: 0.4rem 0.95rem;
  font-size: 0.82rem;
  font-weight: 700;
  border-radius: 8px;
  transition: all 0.15s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  cursor: pointer;
}
.btn-view-nonclust:hover {
  background: #d97706;
  color: #ffffff;
  border-color: #d97706;
  box-shadow: 0 4px 10px rgba(217, 119, 6, 0.25);
}

@media print {
  body { background: #ffffff !important; }
  .no-print { display: none !important; }
  .card-table-wrap { box-shadow: none !important; border: 1px solid #cbd5e1 !important; }
  .cluster-table th, .cluster-table td { border: 1px solid #cbd5e1 !important; }
}
</style>

<div class="container-fluid px-3 px-md-4 py-3 cluster-dashboard-container">

  <!-- Top Hero Header -->
  <div class="hero-header-card mb-4 no-print">
    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
      <!-- Left side: Inline Back Button + Exactly 2-Line Header Info -->
      <div class="d-flex align-items-center gap-3">
        <?php if ($is_district_view && (int)$this->session->get('USER_TYPE') <= 1) { ?>
        <a href="<?= base_url('welcome/district_details'); ?>" class="btn btn-outline-secondary btn-sm rounded-3 shadow-none fw-semibold btn-back-inline" title="Back to District Directory">
          <i class="bi bi-arrow-left fs-6"></i>
          <span>Districts</span>
        </a>
        <?php } elseif (!$is_district_view && (int)$this->session->get('USER_TYPE') <= 2) { ?>
        <a href="<?= base_url('welcome/cluster_details?sel_district_id=' . urlencode($district_code)); ?>" class="btn btn-outline-secondary btn-sm rounded-3 shadow-none fw-semibold btn-back-inline" title="Back to District Overview">
          <i class="bi bi-arrow-left fs-6"></i>
          <span>District Overview</span>
        </a>
        <?php } ?>
        <div>
          <!-- Line 1: Title followed immediately by badges side-by-side -->
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <h4 class="mb-0 fw-bold text-dark fs-5 text-nowrap">
              <?php if ($is_district_view) { ?>
                <?= esc($district_name); ?> District - Cluster Directory
              <?php } else { ?>
                <?= esc($sub_district_name); ?> - Cluster Directory
              <?php } ?>
            </h4>
            <?php if ($is_district_view) { ?>
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-semibold text-nowrap" style="font-size: 0.78rem;">
                <?= count($all_sub_districts); ?> Sub-Districts
              </span>
              <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-semibold text-nowrap" style="font-size: 0.78rem;">
                <?= count($clusters); ?> Active Clusters
              </span>
              <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1 fw-semibold text-nowrap" style="font-size: 0.78rem;">
                <?= number_format($tot_school); ?> Schools
              </span>
            <?php } else { ?>
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-semibold text-nowrap" style="font-size: 0.78rem;">
                <?= count($clusters); ?> Active Clusters
              </span>
              <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1 fw-semibold text-nowrap" style="font-size: 0.78rem;">
                District: <?= esc($district_name); ?>
              </span>
            <?php } ?>
          </div>
          <!-- Line 2: Description -->
          <p class="text-muted small mb-0 mt-1">
            <?php if ($is_district_view) { ?>
              Hierarchical overview of sub-districts, cluster allocations, school registrations, and confirmation status across <?= esc($district_name); ?> District.
            <?php } else { ?>
              Overview of cluster allocations, student registration progress, and confirmation status in <?= esc($sub_district_name); ?>.
            <?php } ?>
          </p>
        </div>
      </div>

      <!-- Right side: Print Report Button -->
      <div class="d-flex align-items-center gap-2 flex-shrink-0">
        <button type="button" onclick="window.print()" class="btn btn-light border btn-sm rounded-3 shadow-sm text-secondary fw-semibold px-3 py-2 text-nowrap">
          <i class="bi bi-printer me-1"></i> Print Report
        </button>
      </div>
    </div>
  </div>

  <?php 
  $user_type_int = (int)$this->session->get('USER_TYPE');
  $can_switch_scope = ($user_type_int <= 2 || strtolower((string)$this->session->get('USER_NAME')) === 'itadmin');
  if ($can_switch_scope) { 
  ?>
  <!-- Administrative Scope Switcher Bar (Visible to IT Admin, State Admin, and District Admin) -->
  <div class="admin-scope-bar mb-4 no-print">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5">
      <div class="d-flex align-items-center gap-2">
        <?php if ($user_type_int <= 1) { ?>
          <span class="badge bg-dark text-white rounded-pill px-2.5 py-1 small fw-semibold d-inline-flex align-items-center gap-1">
            <i class="bi bi-shield-lock-fill text-warning"></i> Admin Scope
          </span>
          <span class="text-muted small fw-medium">Switch District &amp; Sub-District:</span>
        <?php } else { ?>
          <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 small fw-semibold d-inline-flex align-items-center gap-1">
            <i class="bi bi-geo-alt-fill"></i> <?= esc($district_name); ?> District Admin
          </span>
          <span class="text-muted small fw-medium">Filter by Sub-District:</span>
        <?php } ?>
      </div>

      <div class="d-flex align-items-center gap-3 flex-wrap">
        <!-- District Dropdown (Visible only if State / IT Admin) -->
        <?php if ($user_type_int <= 1 && !empty($all_districts)) { ?>
        <div class="d-inline-flex align-items-center gap-1.5">
          <label class="small text-secondary fw-bold mb-0">District:</label>
          <select class="form-select form-select-sm rounded-3 shadow-xs border-secondary-subtle fw-semibold bg-white" onchange="window.location.href='<?= base_url('welcome/cluster_details?sel_district_id='); ?>' + this.value" style="min-width: 140px;">
            <?php foreach ($all_districts as $d) { ?>
              <option value="<?= esc($d['rev_district_code']); ?>" <?= ($district_code == (int)$d['rev_district_code']) ? 'selected' : ''; ?>>
                <?= esc($d['rev_district_name']); ?>
              </option>
            <?php } ?>
          </select>
        </div>
        <?php } ?>

        <!-- Sub-District Dropdown (Available for District Admin, State Admin, and IT Admin) -->
        <?php if (!empty($all_sub_districts)) { ?>
        <div class="d-inline-flex align-items-center gap-1.5">
          <label class="small text-secondary fw-bold mb-0">Sub-District:</label>
          <select class="form-select form-select-sm rounded-3 shadow-xs border-secondary-subtle fw-semibold bg-white" onchange="window.location.href='<?= base_url('welcome/cluster_details?sel_district_id=' . $district_code . '&sel_sub_district_id='); ?>' + this.value" style="min-width: 200px;">
            <option value="" <?= ($is_district_view ? 'selected' : ''); ?>>
              ★ All Sub-Districts (Overview)
            </option>
            <?php foreach ($all_sub_districts as $s) { ?>
              <option value="<?= esc($s['sub_district_code']); ?>" <?= (!$is_district_view && $subdst == (int)$s['sub_district_code']) ? 'selected' : ''; ?>>
                <?= esc($s['sub_district_name']); ?>
              </option>
            <?php } ?>
          </select>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>
  <?php } ?>

  <!-- KPI Summary Tiles (5 Cards) -->
  <div class="row g-3 mb-4 no-print">
    <div class="col-xl col-md-4 col-sm-6">
      <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="val"><?= number_format($tot_school); ?></div>
            <div class="lbl">Total<br>Schools</div>
            <div class="sub-text">In <?= esc($is_district_view ? ($district_name . ' District') : $sub_district_name); ?></div>
          </div>
          <div class="icon-wrap" style="background: #eff6ff; color: #2563eb;">
            <i class="bi bi-buildings"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl col-md-4 col-sm-6">
      <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="val text-primary"><?= number_format($clust_school); ?></div>
            <div class="lbl">Clustered<br>Schools</div>
            <div class="sub-text"><?= count($clusters); ?> Clusters assigned</div>
          </div>
          <div class="icon-wrap" style="background: #f0fdf4; color: #059669;">
            <i class="bi bi-diagram-3-fill"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl col-md-4 col-sm-6">
      <div class="kpi-tile" style="border-color: #fef08a; background: #fffdf5;">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="val" style="color: #b45309;"><?= number_format($calculated_balance); ?></div>
            <div class="lbl" style="color: #b45309;">Non-Cluster<br>Schools</div>
            <div class="sub-text">Unallocated schools</div>
          </div>
          <div class="icon-wrap" style="background: #fef3c7; color: #d97706;">
            <i class="bi bi-diagram-2-fill"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl col-md-6 col-sm-6">
      <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="val text-info"><?= number_format($data_entered); ?> <span class="fs-6 fw-normal text-muted">(<?= $overall_entry_pct; ?>%)</span></div>
            <div class="lbl">Data<br>Entered</div>
            <div class="sub-text"><?= number_format($not_entered); ?> Pending entry</div>
          </div>
          <div class="icon-wrap" style="background: #f0f9ff; color: #0284c7;">
            <i class="bi bi-journal-check"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl col-md-6 col-sm-6">
      <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="val text-success"><?= number_format($confirmed); ?> <span class="fs-6 fw-normal text-muted">(<?= $overall_conf_pct; ?>%)</span></div>
            <div class="lbl">Confirmed<br>Schools</div>
            <div class="sub-text"><?= number_format($not_confirmed); ?> Unconfirmed</div>
          </div>
          <div class="icon-wrap" style="background: #f0fdf4; color: #16a34a;">
            <i class="bi bi-patch-check-fill"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if ($is_district_view) { ?>
  <!-- DISTRICT-WIDE HIERARCHICAL OVERVIEW MODE -->

  <!-- 1. Search Toolbar for Sub-Districts -->
  <div class="d-flex justify-content-between align-items-center mb-3 no-print flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <div class="input-group input-group-sm" style="max-width: 320px;">
        <span class="input-group-text bg-white border-secondary-subtle text-muted">
          <i class="bi bi-search"></i>
        </span>
        <input type="text" id="clusterSearchInput" class="form-control border-secondary-subtle py-2" placeholder="Search sub-district name...">
      </div>
      <span id="searchMatchBadge" class="badge bg-light text-muted border"></span>
    </div>
    <div class="text-muted small fw-semibold">
      Showing all <?= count($district_summary); ?> sub-districts under <?= esc($district_name); ?> District
    </div>
  </div>

  <!-- 2. Sub-District Hierarchy Table -->
  <div class="card-table-wrap mb-4">
    <div class="table-responsive">
      <table class="table cluster-table" id="subDistrictTable">
        <thead>
          <tr>
            <th style="width: 5%; text-align: center;">Sl. No</th>
            <th style="width: 22%;">Sub-District Name</th>
            <th style="width: 9%; text-align: center;">Clusters</th>
            <th style="width: 9%; text-align: center;">Total Schools</th>
            <th style="width: 9%; text-align: center;">Clustered</th>
            <th style="width: 9%; text-align: center;">Non-Cluster</th>
            <th style="width: 11%; text-align: center;">Data Entered</th>
            <th style="width: 11%; text-align: center;">Confirmed</th>
            <th style="width: 8%; text-align: center;">Status</th>
            <th style="width: 7%; text-align: center;" class="no-print">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $k = 0;
          $sum_tot_schools = 0;
          $sum_clusters = 0;
          $sum_clust_schools = 0;
          $sum_non_clust = 0;
          $sum_entered = 0;
          $sum_confirmed = 0;

          if (!empty($district_summary)) {
              foreach ($district_summary as $sd) {
                  $k++;
                  $sd_tot = (int)($sd['total_schools'] ?? 0);
                  $sd_cl_cnt = (int)($sd['cluster_count'] ?? 0);
                  $sd_cl_sch = (int)($sd['cluster_schools'] ?? 0);
                  $sd_non_cl = (int)($sd['non_cluster_schools'] ?? 0);
                  $sd_ent = (int)($sd['data_entered'] ?? 0);
                  $sd_conf = (int)($sd['confirmed'] ?? 0);
                  $sd_is_locked = (($sd['confirm_data_entry'] ?? 'N') === 'Y');

                  $sum_tot_schools += $sd_tot;
                  $sum_clusters += $sd_cl_cnt;
                  $sum_clust_schools += $sd_cl_sch;
                  $sum_non_clust += $sd_non_cl;
                  $sum_entered += $sd_ent;
                  $sum_confirmed += $sd_conf;

                  $sd_ent_pct = $sd_tot > 0 ? round(($sd_ent / $sd_tot) * 100) : 0;
                  $sd_conf_pct = $sd_tot > 0 ? round(($sd_conf / $sd_tot) * 100) : 0;
          ?>
          <tr class="subdistrict-row">
            <td class="text-center text-muted fw-semibold"><?= $k; ?></td>
            <td>
              <a href="<?= base_url('welcome/cluster_details?sel_district_id=' . $district_code . '&sel_sub_district_id=' . $sd['sub_district_code']); ?>" class="fw-bold text-primary text-decoration-none d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt text-secondary"></i>
                <span><?= esc($sd['sub_district_name']); ?></span>
              </a>
            </td>
            <td class="text-center">
              <span class="badge-pill <?= ($sd_cl_cnt > 0) ? 'badge-pill-blue' : 'badge-pill-slate'; ?>">
                <?= $sd_cl_cnt; ?>
              </span>
            </td>
            <td class="text-center">
              <span class="badge-pill badge-pill-slate fw-bold"><?= $sd_tot; ?></span>
            </td>
            <td class="text-center">
              <span class="badge-pill <?= ($sd_cl_sch > 0) ? 'badge-pill-blue' : 'badge-pill-slate'; ?>"><?= $sd_cl_sch; ?></span>
            </td>
            <td class="text-center">
              <span class="badge-pill <?= ($sd_non_cl > 0) ? 'badge-pill-amber fw-bold' : 'badge-pill-slate'; ?>"><?= $sd_non_cl; ?></span>
            </td>
            <td class="text-center">
              <span class="badge-pill badge-pill-blue"><?= $sd_ent; ?></span>
              <div class="d-flex justify-content-center mt-1">
                <div class="micro-prog-wrap">
                  <div class="micro-prog-bar">
                    <div class="micro-prog-val bg-info" style="width: <?= $sd_ent_pct; ?>%"></div>
                  </div>
                  <span class="micro-prog-text"><?= $sd_ent_pct; ?>%</span>
                </div>
              </div>
            </td>
            <td class="text-center">
              <span class="badge-pill badge-pill-emerald"><?= $sd_conf; ?></span>
              <div class="d-flex justify-content-center mt-1">
                <div class="micro-prog-wrap">
                  <div class="micro-prog-bar">
                    <div class="micro-prog-val bg-success" style="width: <?= $sd_conf_pct; ?>%"></div>
                  </div>
                  <span class="micro-prog-text"><?= $sd_conf_pct; ?>%</span>
                </div>
              </div>
            </td>
            <td class="text-center">
              <?php if ($sd_is_locked) { ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 fw-semibold small">Confirmed</span>
              <?php } else { ?>
                <span class="badge bg-light text-muted border rounded-pill px-2 py-1 small">Open</span>
              <?php } ?>
            </td>
            <td class="text-center no-print text-nowrap">
              <a href="<?= base_url('welcome/cluster_details?sel_district_id=' . $district_code . '&sel_sub_district_id=' . $sd['sub_district_code']); ?>" class="btn-view-schools" title="View clusters and schools in <?= esc($sd['sub_district_name']); ?>">
                <i class="bi bi-arrow-right-circle"></i> View Clusters
              </a>
              <?php if ($sd_non_cl > 0) { ?>
              <button type="button" onclick="nonclusterschool(<?= (int)$sd['sub_district_code']; ?>)" class="btn-view-nonclust ms-1" title="View non-cluster schools in <?= esc($sd['sub_district_name']); ?>">
                <i class="bi bi-diagram-2"></i> Non-Cluster (<?= $sd_non_cl; ?>)
              </button>
              <?php } ?>
            </td>
          </tr>
          <?php
              }
          } else {
          ?>
          <tr>
            <td colspan="10" class="text-center py-4 text-muted">
              No sub-districts found for this district.
            </td>
          </tr>
          <?php } ?>
        </tbody>
        <tfoot class="bg-light fw-bold border-top border-2">
          <tr>
            <td colspan="2" class="text-end pe-3 py-3 text-secondary text-uppercase font-monospace small">District Grand Total:</td>
            <td class="text-center py-3"><span class="badge bg-primary px-2 py-1 fs-6"><?= $sum_clusters; ?></span></td>
            <td class="text-center py-3"><span class="badge bg-secondary px-2 py-1 fs-6"><?= $sum_tot_schools; ?></span></td>
            <td class="text-center py-3"><span class="badge bg-primary px-2 py-1 fs-6"><?= $sum_clust_schools; ?></span></td>
            <td class="text-center py-3"><span class="badge bg-warning text-dark px-2 py-1 fs-6"><?= $sum_non_clust; ?></span></td>
            <td class="text-center py-3"><span class="badge bg-info text-white px-2 py-1 fs-6"><?= $sum_entered; ?></span></td>
            <td class="text-center py-3"><span class="badge bg-success px-2 py-1 fs-6"><?= $sum_confirmed; ?></span></td>
            <td colspan="2" class="no-print"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <!-- 3. District Master Clusters List (All Clusters in this District) -->
  <div class="d-flex justify-content-between align-items-center mb-2 no-print flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <h5 class="mb-0 fw-bold text-dark fs-6">
        <i class="bi bi-diagram-3-fill text-primary me-1"></i> Active Clusters in <?= esc($district_name); ?> District
      </h5>
      <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5 small fw-semibold">
        <?= count($district_clusters); ?> Clusters
      </span>
    </div>
  </div>

  <div class="card-table-wrap mb-4">
    <div class="table-responsive">
      <table class="table cluster-table" id="districtClusterTable">
        <thead>
          <tr>
            <th style="width: 5%; text-align: center;">Sl. No</th>
            <th style="width: 22%;">Cluster Name / Officer</th>
            <th style="width: 14%;">Sub-District</th>
            <th style="width: 11%; text-align: center;">Total Schools</th>
            <th style="width: 11%; text-align: center;">Data Entered</th>
            <th style="width: 11%; text-align: center;">Data Pending</th>
            <th style="width: 11%; text-align: center;">Confirmed</th>
            <th style="width: 15%; text-align: center;" class="no-print">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $m = 0;
          if (!empty($district_clusters)) {
              foreach ($district_clusters as $dc) {
                  $m++;
                  $dc_total = (int)($dc['total'] ?? 0);
                  $dc_entered = (int)($dc['data_entered'] ?? 0);
                  $dc_fin = (int)($dc['finialized'] ?? 0);
                  $dc_pending_entry = max(0, $dc_total - $dc_entered);
                  $dc_conf_pct = $dc_total > 0 ? round(($dc_fin / $dc_total) * 100) : 0;
                  $officer_display = !empty($dc['name']) ? $dc['name'] . ' (' . $dc['user_name'] . ')' : $dc['user_name'];
          ?>
          <tr class="cluster-row">
            <td class="text-center text-muted fw-semibold"><?= $m; ?></td>
            <td>
              <a href="javascript:void(0)" onclick="fncShowClusterSchools('<?= esc($dc['user_id']); ?>', '<?= esc($dc['sub_district_code']); ?>')" class="fw-bold text-primary text-decoration-none d-flex align-items-center gap-2">
                <i class="bi bi-diagram-3 text-secondary"></i>
                <span><?= esc($officer_display); ?></span>
              </a>
            </td>
            <td>
              <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1 fw-semibold small">
                <?= esc($dc['sub_district_name']); ?>
              </span>
            </td>
            <td class="text-center">
              <span class="badge-pill badge-pill-slate"><?= $dc_total; ?></span>
            </td>
            <td class="text-center">
              <span class="badge-pill badge-pill-blue"><?= $dc_entered; ?></span>
            </td>
            <td class="text-center">
              <span class="badge-pill <?= ($dc_pending_entry > 0) ? 'badge-pill-amber' : 'badge-pill-slate'; ?>">
                <?= $dc_pending_entry; ?>
              </span>
            </td>
            <td class="text-center">
              <span class="badge-pill badge-pill-emerald"><?= $dc_fin; ?></span>
              <div class="d-flex justify-content-center mt-1">
                <div class="micro-prog-wrap">
                  <div class="micro-prog-bar">
                    <div class="micro-prog-val bg-success" style="width: <?= $dc_conf_pct; ?>%"></div>
                  </div>
                  <span class="micro-prog-text"><?= $dc_conf_pct; ?>%</span>
                </div>
              </div>
            </td>
            <td class="text-center no-print">
              <button type="button" onclick="fncShowClusterSchools('<?= esc($dc['user_id']); ?>', '<?= esc($dc['sub_district_code']); ?>')" class="btn-view-schools" title="View schools in this cluster">
                <i class="bi bi-arrow-right-circle"></i> View Schools
              </button>
            </td>
          </tr>
          <?php
              }
          } else {
          ?>
          <tr>
            <td colspan="8" class="text-center py-4 text-muted">
              No clusters configured in this district.
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php } else { ?>
  <!-- SPECIFIC SUB-DISTRICT CLUSTER VIEW MODE -->

  <!-- Search & Quick Navigation Toolbar -->
  <div class="d-flex justify-content-between align-items-center mb-3 no-print flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <div class="input-group input-group-sm" style="max-width: 320px;">
        <span class="input-group-text bg-white border-secondary-subtle text-muted">
          <i class="bi bi-search"></i>
        </span>
        <input type="text" id="clusterSearchInput" class="form-control border-secondary-subtle py-2" placeholder="Search cluster name...">
      </div>
      <span id="searchMatchBadge" class="badge bg-light text-muted border"></span>
    </div>

    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-view-nonclust" onclick="nonclusterschool(<?= $subdst; ?>)">
        <i class="bi bi-diagram-2-fill"></i> View Non-Cluster Schools (<?= $calculated_balance; ?>)
      </button>
    </div>
  </div>

  <!-- Main Cluster Table -->
  <div class="card-table-wrap">
    <div class="table-responsive">
      <table class="table cluster-table" id="clusterMainTable">
        <thead>
          <tr>
            <th style="width: 5%; text-align: center;">Sl. No</th>
            <th style="width: 26%;">Cluster Name</th>
            <th style="width: 11%; text-align: center;">Total Schools</th>
            <th style="width: 11%; text-align: center;">Data Entered</th>
            <th style="width: 11%; text-align: center;">Data Pending</th>
            <th style="width: 11%; text-align: center;">Confirmed</th>
            <th style="width: 11%; text-align: center;">Unconfirmed</th>
            <th style="width: 14%; text-align: center;" class="no-print">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $j = 0;
          if (!empty($clusters)) {
              foreach ($clusters as $c) {
                  $j++;
                  $c_total = (int)($c['total'] ?? 0);
                  $c_entered = (int)($c['data_entered'] ?? 0);
                  $c_fin = (int)($c['finialized'] ?? 0);
                  $c_pending_entry = max(0, $c_total - $c_entered);
                  $c_unconfirmed = max(0, $c_total - $c_fin);
                  $c_conf_pct = $c_total > 0 ? round(($c_fin / $c_total) * 100) : 0;
          ?>
          <tr class="cluster-row">
            <td class="text-center text-muted fw-semibold"><?= $j; ?></td>
            <td>
              <a href="javascript:void(0)" onclick="fncShowClusterSchools('<?= esc($c['user_id']); ?>', '<?= esc($subdst); ?>')" class="fw-bold text-primary text-decoration-none d-flex align-items-center gap-2">
                <i class="bi bi-diagram-3 text-secondary"></i>
                <span><?= esc($c['user_name']); ?></span>
              </a>
            </td>
            <td class="text-center">
              <span class="badge-pill badge-pill-slate"><?= $c_total; ?></span>
            </td>
            <td class="text-center">
              <span class="badge-pill badge-pill-blue"><?= $c_entered; ?></span>
            </td>
            <td class="text-center">
              <span class="badge-pill <?= ($c_pending_entry > 0) ? 'badge-pill-amber' : 'badge-pill-slate'; ?>">
                <?= $c_pending_entry; ?>
              </span>
            </td>
            <td class="text-center">
              <span class="badge-pill badge-pill-emerald"><?= $c_fin; ?></span>
              <div class="d-flex justify-content-center mt-1">
                <div class="micro-prog-wrap">
                  <div class="micro-prog-bar">
                    <div class="micro-prog-val bg-success" style="width: <?= $c_conf_pct; ?>%"></div>
                  </div>
                  <span class="micro-prog-text"><?= $c_conf_pct; ?>%</span>
                </div>
              </div>
            </td>
            <td class="text-center">
              <span class="badge-pill <?= ($c_unconfirmed > 0) ? 'badge-pill-amber' : 'badge-pill-slate'; ?>">
                <?= $c_unconfirmed; ?>
              </span>
            </td>
            <td class="text-center no-print">
              <button type="button" onclick="fncShowClusterSchools('<?= esc($c['user_id']); ?>', '<?= esc($subdst); ?>')" class="btn-view-schools" title="View schools in this cluster">
                <i class="bi bi-arrow-right-circle"></i> View Schools
              </button>
            </td>
          </tr>
          <?php
              }
          } else {
          ?>
          <tr>
            <td colspan="8" class="text-center py-4 text-muted">
              No clusters configured in this sub-district.
            </td>
          </tr>
          <?php } ?>

          <!-- Distinctive Non-Cluster Schools Row -->
          <tr class="nonclust-row">
            <td class="text-center fw-bold" style="color: #92400e;"><?= $j + 1; ?></td>
            <td>
              <a href="javascript:void(0)" onclick="nonclusterschool(<?= $subdst; ?>)" class="text-decoration-none d-flex align-items-center gap-2">
                <div class="icon-wrap" style="width: 36px; height: 36px; border-radius: 8px; background: #fde68a; color: #b45309; display: flex; align-items: center; justify-content: center;">
                  <i class="bi bi-diagram-2-fill fs-5"></i>
                </div>
                <div>
                  <span class="d-block fw-bold" style="color: #92400e;">Non-Cluster Schools</span>
                  <small class="text-muted">Schools not allocated to any cluster</small>
                </div>
              </a>
            </td>
            <td class="text-center">
              <span class="badge-pill badge-pill-amber fw-bold"><?= $calculated_balance; ?></span>
            </td>
            <td class="text-center">
              <span class="badge-pill badge-pill-blue"><?= $non_entered; ?></span>
            </td>
            <td class="text-center">
              <span class="badge-pill <?= ($non_pending_entry > 0) ? 'badge-pill-amber' : 'badge-pill-slate'; ?>">
                <?= $non_pending_entry; ?>
              </span>
            </td>
            <td class="text-center">
              <span class="badge-pill badge-pill-emerald"><?= $non_fin; ?></span>
              <div class="d-flex justify-content-center mt-1">
                <div class="micro-prog-wrap">
                  <div class="micro-prog-bar">
                    <div class="micro-prog-val bg-warning" style="width: <?= $non_conf_pct; ?>%"></div>
                  </div>
                  <span class="micro-prog-text"><?= $non_conf_pct; ?>%</span>
                </div>
              </div>
            </td>
            <td class="text-center">
              <span class="badge-pill <?= ($non_unconfirmed > 0) ? 'badge-pill-amber' : 'badge-pill-slate'; ?>">
                <?= $non_unconfirmed; ?>
              </span>
            </td>
            <td class="text-center no-print">
              <button type="button" onclick="nonclusterschool(<?= $subdst; ?>)" class="btn-view-nonclust" title="View non-cluster schools">
                <i class="bi bi-arrow-right-circle"></i> View Schools
              </button>
            </td>
          </tr>
        </tbody>
        <tfoot class="bg-light fw-bold border-top border-2">
          <tr>
            <td colspan="2" class="text-end pe-3 py-3 text-secondary text-uppercase font-monospace small">Sub-District Total:</td>
            <td class="text-center py-3"><span class="badge bg-secondary px-2 py-1 fs-6"><?= $tot_school; ?></span></td>
            <td class="text-center py-3"><span class="badge bg-primary px-2 py-1 fs-6"><?= $data_entered; ?></span></td>
            <td class="text-center py-3"><span class="badge bg-warning text-dark px-2 py-1 fs-6"><?= $not_entered; ?></span></td>
            <td class="text-center py-3"><span class="badge bg-success px-2 py-1 fs-6"><?= $confirmed; ?></span></td>
            <td class="text-center py-3"><span class="badge bg-secondary px-2 py-1 fs-6"><?= $not_confirmed; ?></span></td>
            <td class="no-print"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
  <?php } ?>

</div>

<!-- Hidden Navigation Form -->
<form id="clustschool" method="POST" action="">
  <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>" />
  <input type="hidden" name="hidClusterId" id="hidClusterId" value="" />
  <input type="hidden" name="sel_district_id" id="nav_district_id" value="<?= esc($district_code); ?>" />
  <input type="hidden" name="sel_sub_district_id" id="nav_sub_district_id" value="<?= esc($subdst ?: ''); ?>" />
</form>

<script>
// URL synchronization: updates browser address bar so F5 / reload ALWAYS reloads this exact sub-district & district
(function() {
  if (window.history && window.history.replaceState) {
    var isDistView = <?= $is_district_view ? 'true' : 'false'; ?>;
    var expectedSubdist = '<?= esc($subdst ?: ""); ?>';
    var expectedDist = '<?= esc($district_code); ?>';
    var searchParams = new URLSearchParams(window.location.search);
    if (isDistView) {
      if (searchParams.get('sel_district_id') !== expectedDist || searchParams.has('sel_sub_district_id')) {
        searchParams.set('sel_district_id', expectedDist);
        searchParams.delete('sel_sub_district_id');
        var newUrl = window.location.pathname + '?' + searchParams.toString();
        window.history.replaceState(null, '', newUrl);
      }
    } else {
      if (searchParams.get('sel_sub_district_id') !== expectedSubdist || searchParams.get('sel_district_id') !== expectedDist) {
        searchParams.set('sel_district_id', expectedDist);
        searchParams.set('sel_sub_district_id', expectedSubdist);
        var newUrl = window.location.pathname + '?' + searchParams.toString();
        window.history.replaceState(null, '', newUrl);
      }
    }
  }
  try {
    localStorage.setItem('tk25_active_district', '<?= esc($district_code); ?>');
    localStorage.setItem('tk25_active_sub_district', '<?= esc($subdst ?: ""); ?>');
  } catch (e) {}
})();

// Pure Vanilla JavaScript Form Navigation
function fncShowClusterSchools(clusterId, subdist) {
  var form = document.getElementById('clustschool');
  var input = document.getElementById('hidClusterId');
  var subInput = document.getElementById('nav_sub_district_id');
  if (!form || !input) return;
  input.value = clusterId;
  if (subInput && subdist) subInput.value = subdist;
  form.action = '<?= base_url("welcome/cluster_schools"); ?>';
  form.submit();
}

function nonclusterschool(subdist) {
  var form = document.getElementById('clustschool');
  var input = document.getElementById('hidClusterId');
  var subInput = document.getElementById('nav_sub_district_id');
  if (!form || !input) return;
  input.value = subdist;
  if (subInput && subdist) subInput.value = subdist;
  form.action = '<?= base_url("welcome/nonclustdetails"); ?>';
  form.submit();
}

// Live Table Search for both Sub-Districts and Clusters
document.addEventListener('DOMContentLoaded', function() {
  var searchInput = document.getElementById('clusterSearchInput');
  if (!searchInput) return;

  searchInput.addEventListener('keyup', function() {
    var query = this.value.toLowerCase().trim();
    var clusterRows = document.querySelectorAll('#clusterMainTable tbody tr.cluster-row, #districtClusterTable tbody tr.cluster-row');
    var subdistRows = document.querySelectorAll('#subDistrictTable tbody tr.subdistrict-row');
    var matched = 0;

    var rows = (subdistRows.length > 0) ? subdistRows : clusterRows;
    rows.forEach(function(row) {
      var text = row.innerText.toLowerCase();
      if (text.indexOf(query) !== -1) {
        row.style.display = '';
        matched++;
      } else {
        row.style.display = 'none';
      }
    });

    var badge = document.getElementById('searchMatchBadge');
    if (badge) {
      badge.textContent = query ? (matched + ' match' + (matched === 1 ? '' : 'es')) : '';
    }
  });
});
</script>
