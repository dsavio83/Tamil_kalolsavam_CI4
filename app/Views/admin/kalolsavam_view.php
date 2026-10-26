<!-- Flatpickr CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
:root {
  --primary-accent: #2563eb;
  --primary-hover: #1d4ed8;
  --slate-border: #e2e8f0;
  --slate-bg: #f8fafc;
  --card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
  --card-hover-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.08);
}

.kalolsavam-dashboard-container {
  padding: 12px 0 35px 0;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  color: #0f172a;
}

/* 1. Hero Header Card */
.hero-header-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid var(--slate-border);
  box-shadow: var(--card-shadow);
  padding: 1.5rem 1.75rem;
}

.header-icon-box {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
  color: #2563eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.6rem;
  flex-shrink: 0;
}

/* 2. KPI Summary Tiles */
.kpi-tile {
  background: #ffffff;
  border-radius: 14px;
  border: 1px solid var(--slate-border);
  padding: 1.15rem 1.25rem;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  position: relative;
  box-shadow: 0 2px 8px -1px rgba(15, 23, 42, 0.04);
  height: 100%;
}

.kpi-tile:hover {
  transform: translateY(-2px);
  box-shadow: var(--card-hover-shadow);
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
  font-size: 1.65rem;
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
}

.kpi-tile .sub-text {
  font-size: 0.76rem;
  color: #94a3b8;
  margin-top: 0.2rem;
}

/* 3. Modern Form Card */
.modern-form-card {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
  border: 1px solid var(--slate-border);
  overflow: hidden;
  margin-bottom: 25px;
  animation: slideDown 0.25s ease-out;
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}

.form-card-header {
  background: #f8fafc;
  border-bottom: 1px solid var(--slate-border);
  padding: 1.1rem 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.form-card-title {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 10px;
}

.form-card-body {
  padding: 1.75rem;
}

.custom-label {
  font-size: 0.82rem;
  font-weight: 700;
  color: #334155;
  margin-bottom: 6px;
  display: block;
}

.custom-input {
  width: 100%;
  border: 1.5px solid #cbd5e1;
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 0.92rem;
  color: #0f172a;
  background-color: #f8fafc;
  transition: all 0.15s ease;
  box-sizing: border-box;
}

.custom-input:focus {
  background-color: #ffffff;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  outline: none;
}

.date-input-group {
  position: relative;
  display: flex;
  align-items: center;
}

.date-input-group input {
  padding-right: 38px;
}

.date-calendar-btn {
  position: absolute;
  right: 10px;
  background: none;
  border: none;
  color: #2563eb;
  font-size: 1.1rem;
  cursor: pointer;
  padding: 4px;
}

.date-presets {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-top: 8px;
}

.preset-chip {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  padding: 3px 10px;
  font-size: 0.72rem;
  font-weight: 600;
  color: #475569;
  cursor: pointer;
  transition: all 0.15s;
}

.preset-chip:hover {
  background: #2563eb;
  color: #ffffff;
  border-color: #2563eb;
}

.duration-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 12px;
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #dbeafe;
  border-radius: 14px;
  font-size: 0.78rem;
  font-weight: 700;
}

/* Logo Upload Area */
.logo-upload-box {
  border: 2px dashed #cbd5e1;
  border-radius: 14px;
  padding: 20px;
  text-align: center;
  background: #f8fafc;
  transition: all 0.2s ease;
}

.logo-upload-box:hover {
  border-color: #2563eb;
  background: #f0f7ff;
}

.logo-preview-img {
  max-width: 120px;
  max-height: 120px;
  border-radius: 10px;
  object-fit: contain;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  margin-bottom: 10px;
  border: 2px solid #ffffff;
}

/* 4. Kalolsavam Cards Grid */
.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
  gap: 20px;
}

.fest-card {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: var(--card-shadow);
  border: 1px solid var(--slate-border);
  overflow: hidden;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  display: flex;
  flex-direction: column;
}

.fest-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--card-hover-shadow);
  border-color: #cbd5e1;
}

.fest-card-header {
  background: #f8fafc;
  border-bottom: 1px solid #f1f5f9;
  padding: 1.25rem 1.5rem;
  position: relative;
}

.fest-card-year {
  font-size: 1.45rem;
  font-weight: 800;
  margin: 0 0 4px 0;
  letter-spacing: -0.02em;
  color: #0f172a;
}

.fest-card-name {
  font-size: 0.95rem;
  font-weight: 600;
  margin: 0;
  color: #475569;
}

.fest-status-badge {
  position: absolute;
  top: 1.25rem;
  right: 1.5rem;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

.fest-card-body {
  padding: 1.25rem 1.5rem;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.fest-meta-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.fest-meta-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #f1f5f9;
  color: #2563eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  flex-shrink: 0;
}

.fest-meta-label {
  font-size: 0.72rem;
  color: #64748b;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.04em;
  margin-bottom: 1px;
}

.fest-meta-value {
  font-size: 0.9rem;
  font-weight: 600;
  color: #0f172a;
}

.fest-card-footer {
  padding: 1rem 1.5rem;
  background: #f8fafc;
  border-top: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.btn-card-edit {
  background: #2563eb;
  color: #ffffff;
  border: 1px solid #1d4ed8;
  border-radius: 20px;
  padding: 6px 18px;
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  text-decoration: none;
  transition: all 0.15s ease;
  box-shadow: 0 2px 6px rgba(37, 99, 235, 0.2);
}

.btn-card-edit:hover {
  background: #1d4ed8;
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
  transform: translateY(-1px);
}

@media print {
  .no-print { display: none !important; }
  .fest-card { box-shadow: none !important; border: 1px solid #000 !important; }
}
</style>

<div class="container-fluid px-3 px-md-4 py-3 kalolsavam-dashboard-container">

  <!-- 1. Top Hero Header Card -->
  <div class="hero-header-card mb-4 no-print">
    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
      <div class="d-flex align-items-center gap-3">
        <div class="header-icon-box">
          <i class="bi bi-trophy-fill"></i>
        </div>
        <div>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <h4 class="mb-0 fw-bold text-dark tracking-tight">
              Kalolsavam Management
            </h4>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-semibold">
              <i class="bi bi-shield-check me-1"></i> State Level
            </span>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small fw-semibold">
              <i class="bi bi-broadcast me-1"></i> Active Edition
            </span>
          </div>
          <p class="text-muted small mb-0 mt-1">
            Master academic schedule, venue allocations, timeline milestones, and festival branding.
          </p>
        </div>
      </div>

      <!-- Right side: Quick Print Utility (Header Edit button removed as requested) -->
      <div class="d-flex align-items-center gap-2">
        <button type="button" onclick="window.print()" class="btn btn-light border btn-sm rounded-3 shadow-sm text-secondary fw-semibold px-3 py-2 text-nowrap">
          <i class="bi bi-printer me-1"></i> Print Schedule
        </button>
      </div>
    </div>
  </div>

  <!-- 2. KPI Summary Tiles (4 Cards) -->
  <?php 
    $active_fest = !empty($kalolsavam_details) ? $kalolsavam_details[0] : null;
    $start_d = ($active_fest && $active_fest['start_date'] != '0000-00-00') ? date("d M Y", strtotime($active_fest['start_date'])) : 'Not Set';
    $end_d = ($active_fest && $active_fest['end_date'] != '0000-00-00') ? date("d M Y", strtotime($active_fest['end_date'])) : 'Not Set';
    $venue = ($active_fest && !empty($active_fest['venue'])) ? $active_fest['venue'] : 'Venue Not Configured';
    $fest_year = $active_fest['kalolsavam_year'] ?? date('Y') . '-' . (date('Y') + 1);
    $has_selected = (count(@$selected_kalolsavam ?? []) > 0);
  ?>
  <div class="row g-3 mb-4 no-print">
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="val"><?php echo esc($fest_year); ?></div>
            <div class="lbl">Academic Year</div>
            <div class="sub-text">Current Active Cycle</div>
          </div>
          <div class="icon-wrap" style="background: #eff6ff; color: #2563eb;">
            <i class="bi bi-mortarboard-fill"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
      <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="val text-truncate" style="max-width: 180px;" title="<?php echo esc($venue); ?>"><?php echo esc($venue); ?></div>
            <div class="lbl">Official Venue</div>
            <div class="sub-text">Main Arena / Grounds</div>
          </div>
          <div class="icon-wrap" style="background: #f5f3ff; color: #7c3aed;">
            <i class="bi bi-geo-alt-fill"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
      <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="val" style="font-size: 1.35rem;"><?php echo ($start_d !== 'Not Set') ? $start_d : 'Not Scheduled'; ?></div>
            <div class="lbl">Schedule Dates</div>
            <div class="sub-text"><?php echo ($end_d !== 'Not Set') ? ('Until ' . $end_d) : 'Dates Pending'; ?></div>
          </div>
          <div class="icon-wrap" style="background: #f0f9ff; color: #0284c7;">
            <i class="bi bi-calendar2-range-fill"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
      <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="val text-success"><i class="bi bi-check-circle-fill me-1"></i>Active</div>
            <div class="lbl">Festival Status</div>
            <div class="sub-text">System Live &amp; Configured</div>
          </div>
          <div class="icon-wrap" style="background: #f0fdf4; color: #16a34a;">
            <i class="bi bi-patch-check-fill"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 3. Form Card (Revealed when Edit button on card is clicked) -->
  <div class="modern-form-card" id="formCard" style="<?php echo $has_selected ? 'display:block;' : 'display:none;'; ?>">
    <div class="form-card-header">
      <h3 class="form-card-title">
        <i class="bi bi-sliders text-primary"></i> 
        <span id="formTitle"><?php echo $has_selected ? 'Edit Kalolsavam Configuration' : 'Add New Kalolsavam'; ?></span>
      </h3>
      <button type="button" class="btn-close shadow-none" onclick="hideFormCard();" aria-label="Close"></button>
    </div>

    <div class="form-card-body">
      <form id="formKalolsavam" method="post" enctype="multipart/form-data" action="<?php echo base_url('admin/kalolsavam/update_kalolsavam'); ?>">
        <?= csrf_field(); ?>
        <input type="hidden" name="kalolsavam_id" id="kalolsavam_id" value="<?php echo esc((string)(@$selected_kalolsavam[0]['kalolsavam_id'] ?? ($active_fest['kalolsavam_id'] ?? ($kalolsavam_id ?? '')))); ?>" />
        
        <div class="row g-4">
          <!-- Left Column: Text Inputs & Date Pickers -->
          <div class="col-12 col-lg-8">
            <div class="row g-3">
              
              <div class="col-12 col-md-8">
                <label class="custom-label" for="txtKalolsavamName">
                  Kalolsavam Name <span class="text-danger">*</span>
                </label>
                <input type="text" name="txtKalolsavamName" id="txtKalolsavamName" class="custom-input" placeholder="e.g. Kerala State Tamil Kalolsavam" value="<?php echo esc((string)(@$selected_kalolsavam[0]['kalolsavam_name'] ?? ($active_fest['kalolsavam_name'] ?? ''))); ?>" required />
              </div>

              <div class="col-12 col-md-4">
                <label class="custom-label" for="txtKalolsavamYear">
                  Academic Year <span class="text-danger">*</span>
                </label>
                <input type="text" name="txtKalolsavamYear" id="txtKalolsavamYear" class="custom-input" placeholder="2025-2026" value="<?php echo esc((string)(@$selected_kalolsavam[0]['kalolsavam_year'] ?? ($active_fest['kalolsavam_year'] ?? ''))); ?>" required />
              </div>

              <div class="col-12">
                <label class="custom-label" for="txtKalolsavamVenue">
                  Festival Venue <span class="text-danger">*</span>
                </label>
                <input type="text" name="txtKalolsavamVenue" id="txtKalolsavamVenue" class="custom-input" placeholder="e.g. Govt Victoria College Ground, Palakkad" value="<?php echo esc((string)(@$selected_kalolsavam[0]['venue'] ?? ($active_fest['venue'] ?? ''))); ?>" required />
              </div>

              <!-- Date Picker Row -->
              <div class="col-12 col-md-6">
                <label class="custom-label" for="txtStartDate">
                  Start Date
                </label>
                <div class="date-input-group">
                  <?php 
                    $curr_start = (!empty(@$selected_kalolsavam[0]['start_date']) && @$selected_kalolsavam[0]['start_date'] !== '0000-00-00') 
                      ? (string)@$selected_kalolsavam[0]['start_date'] 
                      : ((!empty($active_fest['start_date']) && $active_fest['start_date'] !== '0000-00-00') ? (string)$active_fest['start_date'] : '');
                  ?>
                  <input type="text" name="txtStartDate" id="txtStartDate" class="custom-input datepicker" placeholder="Select start date" value="<?php echo esc($curr_start); ?>" />
                  <button type="button" class="date-calendar-btn" onclick="openPicker('txtStartDate')"><i class="bi bi-calendar-event"></i></button>
                </div>
              </div>

              <div class="col-12 col-md-6">
                <label class="custom-label" for="txtEndDate">
                  End Date
                </label>
                <div class="date-input-group">
                  <?php 
                    $curr_end = (!empty(@$selected_kalolsavam[0]['end_date']) && @$selected_kalolsavam[0]['end_date'] !== '0000-00-00') 
                      ? (string)@$selected_kalolsavam[0]['end_date'] 
                      : ((!empty($active_fest['end_date']) && $active_fest['end_date'] !== '0000-00-00') ? (string)$active_fest['end_date'] : '');
                  ?>
                  <input type="text" name="txtEndDate" id="txtEndDate" class="custom-input datepicker" placeholder="Select end date" value="<?php echo esc($curr_end); ?>" />
                  <button type="button" class="date-calendar-btn" onclick="openPicker('txtEndDate')"><i class="bi bi-calendar-check"></i></button>
                </div>
              </div>

              <!-- Quick Presets & Duration Badge -->
              <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-1">
                  <div class="date-presets">
                    <span class="text-muted small me-1">Quick Presets:</span>
                    <button type="button" class="preset-chip" onclick="setQuickDate(1)">1 Day</button>
                    <button type="button" class="preset-chip" onclick="setQuickDate(3)">3 Days</button>
                    <button type="button" class="preset-chip" onclick="setQuickDate(5)">5 Days</button>
                    <button type="button" class="preset-chip" onclick="setQuickDate(7)">1 Week</button>
                    <button type="button" class="preset-chip text-danger" onclick="clearDates()">Clear</button>
                  </div>
                  <div id="durationBadge" class="duration-pill" style="display:none;">
                    <i class="bi bi-clock-history"></i> <span id="durationText">0 Days Event</span>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!-- Right Column: Logo Upload Dropzone -->
          <div class="col-12 col-lg-4">
            <label class="custom-label">
              Official Festival Logo
            </label>
            <div class="logo-upload-box">
              <div id="logoPreviewContainer">
                <?php 
                  $current_logo = @$selected_kalolsavam[0]['logo_name'] ?? ($active_fest['logo_name'] ?? '');
                  if (!empty($current_logo) && file_exists(FCPATH . 'uploads/state/thumb_' . $current_logo)) { 
                ?>
                  <img src="<?php echo base_url('uploads/state/thumb_' . $current_logo); ?>" id="previewImg" class="logo-preview-img" alt="Logo" />
                <?php } else { ?>
                  <div id="logoPlaceholder" style="font-size: 2.8rem; color: #94a3b8; margin-bottom: 8px;">
                    <i class="bi bi-image"></i>
                  </div>
                  <img src="" id="previewImg" class="logo-preview-img" alt="Logo" style="display:none;" />
                <?php } ?>
              </div>

              <p class="text-muted small mb-3">Accepted: JPG, PNG, GIF (Max 2MB)</p>
              
              <input type="file" name="kalolsavamLogo" id="kalolsavamLogo" accept="image/*" style="display:none;" onchange="handleLogoPreview(this);" />
              <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1.5 fw-semibold" onclick="document.getElementById('kalolsavamLogo').click();">
                <i class="bi bi-cloud-arrow-up-fill me-1"></i> Choose Logo File
              </button>
            </div>
          </div>
        </div>

        <!-- Submit & Action Buttons -->
        <div class="d-flex justify-content-center align-items-center gap-2 mt-4 pt-3 border-top flex-wrap">
          <button type="button" id="btnSubmitKalolsavam" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm" onclick="submitKalolsavamAjax();">
            <i class="bi bi-check-circle-fill me-1"></i> <span id="btnSubmitText">Save Configuration Changes</span>
          </button>
          <button type="button" class="btn btn-light border rounded-pill px-4 py-2 fw-semibold text-secondary shadow-none" onclick="hideFormCard();">
            <i class="bi bi-x-circle me-1"></i> Cancel
          </button>
        </div>

      </form>
    </div>
  </div>

  <!-- 4. Kalolsavam Records Grid (With exclusive Edit button on the card) -->
  <div id="kalolsavamList" style="<?php echo $has_selected ? 'display:none;' : 'display:block;'; ?>">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
        <i class="bi bi-collection-fill text-primary"></i> Active &amp; Historical Editions
      </h5>
      <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill small">
        Total: <?php echo count($kalolsavam_details ?? []); ?> Editions
      </span>
    </div>

    <?php if (!empty($kalolsavam_details)) { ?>
      <div class="cards-grid">
        <?php foreach ($kalolsavam_details as $fest) { 
          $has_start = (!empty($fest['start_date']) && $fest['start_date'] !== '0000-00-00' && strtotime($fest['start_date']) !== false);
          $has_end   = (!empty($fest['end_date']) && $fest['end_date'] !== '0000-00-00' && strtotime($fest['end_date']) !== false);

          if ($has_start && $has_end) {
              if ($fest['start_date'] === $fest['end_date']) {
                  $date_display = date("j M Y", strtotime($fest['start_date']));
              } else {
                  $date_display = date("j M Y", strtotime($fest['start_date'])) . ' — ' . date("j M Y", strtotime($fest['end_date']));
              }
          } else if ($has_start) {
              $date_display = 'From ' . date("j M Y", strtotime($fest['start_date'])) . ' (End Date Not Set)';
          } else if ($has_end) {
              $date_display = 'Until ' . date("j M Y", strtotime($fest['end_date']));
          } else {
              $date_display = 'Dates Not Configured';
          }
          $is_open = ($fest['status'] == 'O');
        ?>
          <div class="fest-card">
            <!-- Card Header -->
            <div class="fest-card-header">
              <span class="fest-status-badge <?php echo $is_open ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle'; ?>">
                <?php echo $is_open ? '● Active' : '○ Closed'; ?>
              </span>
              <h3 class="fest-card-year"><?php echo esc($fest['kalolsavam_year']); ?></h3>
              <p class="fest-card-name"><?php echo esc($fest['kalolsavam_name']); ?></p>
            </div>

            <!-- Card Body -->
            <div class="fest-card-body">
              <div class="fest-meta-item">
                <div class="fest-meta-icon"><i class="bi bi-geo-alt"></i></div>
                <div>
                  <div class="fest-meta-label">Venue Location</div>
                  <div class="fest-meta-value"><?php echo esc(!empty($fest['venue']) ? $fest['venue'] : 'Not Configured'); ?></div>
                </div>
              </div>

              <div class="fest-meta-item">
                <div class="fest-meta-icon"><i class="bi bi-calendar3"></i></div>
                <div>
                  <div class="fest-meta-label">Event Timeline</div>
                  <div class="fest-meta-value"><?php echo esc($date_display); ?></div>
                </div>
              </div>

              <?php if (!empty($fest['logo_name'])) { ?>
                <div class="fest-meta-item">
                  <div class="fest-meta-icon" style="color: #059669; background: #ecfdf5;"><i class="bi bi-patch-check-fill"></i></div>
                  <div>
                    <div class="fest-meta-label">Official Logo</div>
                    <div class="fest-meta-value text-success small fw-bold">Uploaded &amp; Linked</div>
                  </div>
                </div>
              <?php } ?>
            </div>

            <!-- Card Footer: Exclusive Edit Details Button on Card -->
            <div class="fest-card-footer">
              <span class="text-muted small font-monospace">Edition #<?php echo esc($fest['kalolsavam_id']); ?></span>
              <?php if ($is_open) { ?>
                <button type="button" class="btn-card-edit" onclick="fncEditKalolsavam('<?php echo $fest['kalolsavam_id']; ?>');">
                  <i class="bi bi-pencil-square"></i> Edit Details
                </button>
              <?php } else { ?>
                <span class="badge bg-light text-muted border rounded-pill px-3 py-1">Archived</span>
              <?php } ?>
            </div>
          </div>
        <?php } ?>
      </div>
    <?php } else { ?>
      <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
        <div style="font-size: 3rem; color: #cbd5e1;"><i class="bi bi-calendar-x"></i></div>
        <h5 class="mt-3 text-secondary fw-bold">No Kalolsavam Records Found</h5>
        <p class="text-muted small">No active or archived festival records were returned.</p>
      </div>
    <?php } ?>
  </div>

</div>

<!-- Hidden Form for Legacy Post Submissions -->
<form id="list_formKalolsavam" method="post" action="<?php echo base_url('admin/kalolsavam/edit_kalolsavam'); ?>" style="display:none;">
  <?= csrf_field(); ?>
  <input type="hidden" name="sel_kalolsavam_id" id="sel_kalolsavam_id" value="" />
</form>

<script>
var startPicker = null;
var endPicker = null;

document.addEventListener('DOMContentLoaded', function() {
  startPicker = flatpickr("#txtStartDate", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "F j, Y",
    allowInput: true,
    onChange: function(selectedDates, dateStr) {
      if (endPicker && selectedDates.length > 0) {
        var startDate = selectedDates[0];
        endPicker.set('minDate', dateStr);
        endPicker.jumpToDate(startDate);

        var currentEnd = endPicker.selectedDates[0];
        if (!currentEnd || currentEnd < startDate) {
          endPicker.setDate(dateStr, true);
        }
      }
      calculateDuration();
    }
  });

  endPicker = flatpickr("#txtEndDate", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "F j, Y",
    allowInput: true,
    onOpen: function(selectedDates, dateStr, instance) {
      if (startPicker && startPicker.selectedDates.length > 0) {
        var startD = startPicker.selectedDates[0];
        instance.set('minDate', startPicker.input.value);
        if (selectedDates.length === 0) {
          instance.jumpToDate(startD);
        }
      }
    },
    onChange: function() {
      calculateDuration();
    }
  });

  if (startPicker && startPicker.selectedDates.length > 0) {
    var initialStart = startPicker.selectedDates[0];
    if (endPicker) {
      endPicker.set('minDate', startPicker.input.value);
      if (endPicker.selectedDates.length === 0) {
        endPicker.jumpToDate(initialStart);
      }
    }
  }

  calculateDuration();
});

function openPicker(id) {
  var el = document.getElementById(id);
  if (el && el._flatpickr) {
    el._flatpickr.open();
  }
}

function calculateDuration() {
  var startVal = document.getElementById('txtStartDate').value;
  var endVal = document.getElementById('txtEndDate').value;
  var badge = document.getElementById('durationBadge');
  var text = document.getElementById('durationText');

  if (startVal && endVal) {
    var d1 = new Date(startVal);
    var d2 = new Date(endVal);
    var timeDiff = d2.getTime() - d1.getTime();
    var daysDiff = Math.round(timeDiff / (1000 * 3600 * 24)) + 1;

    if (daysDiff > 0) {
      badge.style.display = 'inline-flex';
      text.innerText = daysDiff + (daysDiff === 1 ? ' Day Event' : ' Days Event');
    } else {
      badge.style.display = 'none';
    }
  } else if (startVal) {
    badge.style.display = 'inline-flex';
    text.innerText = 'Start Date Selected';
  } else {
    badge.style.display = 'none';
  }
}

function setQuickDate(days) {
  var today = new Date();
  var endDay = new Date();
  endDay.setDate(today.getDate() + (days - 1));

  var formatDate = function(d) {
    var month = '' + (d.getMonth() + 1);
    var day = '' + d.getDate();
    var year = d.getFullYear();
    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;
    return [year, month, day].join('-');
  };

  if (startPicker) startPicker.setDate(formatDate(today), true);
  if (endPicker) endPicker.setDate(formatDate(endDay), true);
}

function clearDates() {
  if (startPicker) startPicker.clear();
  if (endPicker) endPicker.clear();
  calculateDuration();
}

function handleLogoPreview(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      var img = document.getElementById('previewImg');
      var placeholder = document.getElementById('logoPlaceholder');
      img.src = e.target.result;
      img.style.display = 'inline-block';
      if (placeholder) placeholder.style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function showFormCard() {
  document.getElementById('formCard').style.display = 'block';
  document.getElementById('kalolsavamList').style.display = 'none';
  document.getElementById('formCard').scrollIntoView({ behavior: 'smooth' });
}

function hideFormCard() {
  document.getElementById('formCard').style.display = 'none';
  document.getElementById('kalolsavamList').style.display = 'block';
  document.getElementById('kalolsavamList').scrollIntoView({ behavior: 'smooth' });
}

function fncEditKalolsavam(id) {
  Swal.fire({
    title: 'Loading...',
    text: 'Fetching Kalolsavam details...',
    allowOutsideClick: false,
    didOpen: function() {
      Swal.showLoading();
    }
  });

  fetch('<?php echo base_url("admin/kalolsavam/get_kalolsavam_ajax"); ?>?kalolsavam_id=' + encodeURIComponent(id), {
    method: 'GET'
  })
  .then(function(res) { return res.json(); })
  .then(function(json) {
    Swal.close();
    if (json.csrf_hash && window.updateCsrfToken) {
      window.updateCsrfToken(json.csrf_hash);
    }
    if (json.success && json.data) {
      var d = json.data;
      document.getElementById('kalolsavam_id').value = d.kalolsavam_id || '';
      document.getElementById('txtKalolsavamName').value = d.kalolsavam_name || '';
      document.getElementById('txtKalolsavamYear').value = d.kalolsavam_year || '';
      document.getElementById('txtKalolsavamVenue').value = d.venue || '';

      if (d.start_date && d.start_date !== '0000-00-00') {
        if (startPicker) startPicker.setDate(d.start_date, true);
        else document.getElementById('txtStartDate').value = d.start_date;
      } else {
        if (startPicker) startPicker.clear();
        else document.getElementById('txtStartDate').value = '';
      }

      if (d.end_date && d.end_date !== '0000-00-00') {
        if (endPicker) endPicker.setDate(d.end_date, true);
        else document.getElementById('txtEndDate').value = d.end_date;
      } else {
        if (endPicker) endPicker.clear();
        else document.getElementById('txtEndDate').value = '';
      }

      var img = document.getElementById('previewImg');
      var placeholder = document.getElementById('logoPlaceholder');
      if (d.logo_url) {
        img.src = d.logo_url;
        img.style.display = 'inline-block';
        if (placeholder) placeholder.style.display = 'none';
      } else {
        img.style.display = 'none';
        if (placeholder) placeholder.style.display = 'block';
      }

      document.getElementById('formTitle').innerText = 'Edit Kalolsavam Details (' + d.kalolsavam_year + ')';
      showFormCard();
    } else {
      document.getElementById('sel_kalolsavam_id').value = id;
      document.getElementById('list_formKalolsavam').submit();
    }
  })
  .catch(function(err) {
    Swal.close();
    console.error('Fetch error:', err);
    document.getElementById('sel_kalolsavam_id').value = id;
    document.getElementById('list_formKalolsavam').submit();
  });
}

function fncCancelKalolsavam() {
  hideFormCard();
}

function submitKalolsavamAjax() {
  var name = document.getElementById('txtKalolsavamName').value.trim();
  var year = document.getElementById('txtKalolsavamYear').value.trim();
  var venue = document.getElementById('txtKalolsavamVenue').value.trim();
  var start = document.getElementById('txtStartDate').value.trim();
  var end = document.getElementById('txtEndDate').value.trim();

  if (!name) {
    Swal.fire('Required Field', 'Please enter the Kalolsavam Name.', 'warning');
    return;
  }
  if (!year) {
    Swal.fire('Required Field', 'Please enter the Academic Year.', 'warning');
    return;
  }
  if (!venue) {
    Swal.fire('Required Field', 'Please enter the Festival Venue.', 'warning');
    return;
  }
  if (start && end && new Date(start) > new Date(end)) {
    Swal.fire('Invalid Date', 'End Date cannot be earlier than Start Date.', 'error');
    return;
  }

  var submitBtn = document.getElementById('btnSubmitKalolsavam');
  var originalHtml = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';

  var formEl = document.getElementById('formKalolsavam');
  var formData = new FormData(formEl);

  // Ensure CSRF token is in formData
  if (window.getCsrfToken && !formData.get('csrf_test_name')) {
    var tok = window.getCsrfToken();
    if (tok) formData.append('csrf_test_name', tok);
  }

  fetch('<?php echo base_url("admin/kalolsavam/save_kalolsavam_ajax"); ?>', {
    method: 'POST',
    body: formData
  })
  .then(function(res) {
    return res.json().catch(function() {
      throw new Error('Server response error (HTTP ' + res.status + ')');
    });
  })
  .then(function(data) {
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalHtml;

    if (data.csrf_hash && window.updateCsrfToken) {
      window.updateCsrfToken(data.csrf_hash);
    }

    if (data.success) {
      hideFormCard();
      Swal.fire({
        title: 'Success!',
        text: data.message || 'Kalolsavam details have been saved successfully.',
        icon: 'success',
        confirmButtonColor: '#2563eb'
      }).then(function() {
        window.location.href = '<?php echo base_url("admin/kalolsavam"); ?>';
      });
    } else {
      Swal.fire('Error', data.message || 'Failed to save Kalolsavam details.', 'error');
    }
  })
  .catch(function(err) {
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalHtml;
    console.error('Save error:', err);
    // Submit normally via form as reliable fallback
    formEl.submit();
  });
}
</script>
