<!-- Flatpickr CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
:root {
  --primary-gradient: linear-gradient(135deg, #2563eb 0%, #4f46e5 50%, #7c3aed 100%);
  --secondary-gradient: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);
  --accent-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
  --card-hover-shadow: 0 16px 32px rgba(79, 70, 229, 0.18);
}

.kalolsavam-container {
  padding: 12px 0 35px 0;
  font-family: 'Poppins', system-ui, -apple-system, sans-serif;
}

/* Header Hero Banner */
.header-hero {
  background: var(--primary-gradient);
  border-radius: 18px;
  padding: 26px 32px;
  color: #ffffff;
  box-shadow: 0 10px 25px rgba(79, 70, 229, 0.28);
  margin-bottom: 24px;
  position: relative;
  overflow: hidden;
}

.header-hero h1 {
  font-size: 1.65rem;
  font-weight: 700;
  margin: 0 0 6px 0;
  display: flex;
  align-items: center;
  gap: 12px;
  color: #ffffff;
}

.header-hero p {
  margin: 0;
  opacity: 0.9;
  font-size: 0.92rem;
}

.header-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(255, 255, 255, 0.18);
  backdrop-filter: blur(8px);
  padding: 5px 14px;
  border-radius: 24px;
  font-size: 0.8rem;
  font-weight: 600;
  border: 1px solid rgba(255, 255, 255, 0.25);
  margin-bottom: 10px;
}

/* KPI Overview Cards */
.kpi-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 22px 24px;
  box-shadow: var(--card-shadow);
  border: 1px solid #e2e8f0;
  transition: all 0.25s ease;
  height: 100%;
  display: flex;
  align-items: center;
  gap: 18px;
}

.kpi-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--card-hover-shadow);
}

.kpi-icon-box {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  flex-shrink: 0;
  color: #ffffff;
}

.kpi-icon-indigo { background: linear-gradient(135deg, #4f46e5, #6366f1); }
.kpi-icon-purple { background: linear-gradient(135deg, #7c3aed, #a855f7); }
.kpi-icon-sky    { background: linear-gradient(135deg, #0284c7, #38bdf8); }
.kpi-icon-amber  { background: linear-gradient(135deg, #d97706, #f59e0b); }

.kpi-content h6 {
  font-size: 0.78rem;
  text-transform: uppercase;
  color: #64748b;
  font-weight: 600;
  margin: 0 0 4px 0;
  letter-spacing: 0.5px;
}

.kpi-content h4 {
  font-size: 1.15rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

/* Modern Form Card */
.modern-form-card {
  background: #ffffff;
  border-radius: 18px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
  border: 1px solid #e2e8f0;
  overflow: hidden;
  margin-bottom: 25px;
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.form-card-header {
  background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
  border-bottom: 1px solid #e2e8f0;
  padding: 20px 28px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.form-card-title {
  font-size: 1.15rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 10px;
}

.form-card-body {
  padding: 28px;
}

.custom-label {
  font-size: 0.88rem;
  font-weight: 600;
  color: #334155;
  margin-bottom: 8px;
  display: block;
}

.custom-input {
  width: 100%;
  border: 1.5px solid #cbd5e1;
  border-radius: 12px;
  padding: 12px 16px;
  font-size: 0.95rem;
  color: #0f172a;
  background-color: #f8fafc;
  transition: all 0.2s ease;
  box-sizing: border-box;
}

.custom-input:focus {
  background-color: #ffffff;
  border-color: #4f46e5;
  box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
  outline: none;
}

.date-input-group {
  position: relative;
  display: flex;
  align-items: center;
}

.date-input-group input {
  padding-right: 42px;
}

.date-calendar-btn {
  position: absolute;
  right: 12px;
  background: none;
  border: none;
  color: #4f46e5;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 4px;
}

.date-presets {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 10px;
}

.preset-chip {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  padding: 4px 12px;
  font-size: 0.75rem;
  font-weight: 600;
  color: #475569;
  cursor: pointer;
  transition: all 0.2s;
}

.preset-chip:hover {
  background: #4f46e5;
  color: #ffffff;
  border-color: #4f46e5;
}

.duration-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 14px;
  background: #eef2ff;
  color: #4338ca;
  border-radius: 14px;
  font-size: 0.82rem;
  font-weight: 600;
  margin-top: 8px;
}

/* Logo Upload Area */
.logo-upload-box {
  border: 2px dashed #cbd5e1;
  border-radius: 16px;
  padding: 24px;
  text-align: center;
  background: #f8fafc;
  transition: all 0.2s ease;
}

.logo-upload-box:hover {
  border-color: #4f46e5;
  background: #f5f3ff;
}

.logo-preview-img {
  max-width: 140px;
  max-height: 140px;
  border-radius: 12px;
  object-fit: contain;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
  margin-bottom: 12px;
  border: 2px solid #ffffff;
}

.btn-upload-trigger {
  background: #4f46e5;
  color: #ffffff;
  border-radius: 25px;
  padding: 8px 20px;
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.btn-upload-trigger:hover {
  background: #4338ca;
  color: #ffffff;
}

/* Action Buttons */
.btn-action-primary {
  background: var(--primary-gradient);
  color: #ffffff;
  border: none;
  border-radius: 28px;
  padding: 12px 32px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  box-shadow: 0 6px 20px rgba(79, 70, 229, 0.28);
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.btn-action-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4);
  color: #ffffff;
}

.btn-action-secondary {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #cbd5e1;
  border-radius: 28px;
  padding: 12px 28px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.btn-action-secondary:hover {
  background: #e2e8f0;
  color: #0f172a;
}

/* Kalolsavam Cards Grid */
.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 24px;
}

.fest-card {
  background: #ffffff;
  border-radius: 18px;
  box-shadow: var(--card-shadow);
  border: 1px solid #e2e8f0;
  overflow: hidden;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
}

.fest-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--card-hover-shadow);
}

.fest-card-header {
  background: var(--primary-gradient);
  padding: 22px 28px;
  color: #ffffff;
  position: relative;
}

.fest-card-year {
  font-size: 1.85rem;
  font-weight: 800;
  margin: 0;
  letter-spacing: -0.5px;
}

.fest-card-name {
  font-size: 1rem;
  font-weight: 500;
  margin: 4px 0 0 0;
  opacity: 0.95;
}

.fest-status-badge {
  position: absolute;
  top: 20px;
  right: 24px;
  padding: 5px 14px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(6px);
  border: 1px solid rgba(255, 255, 255, 0.35);
  color: #ffffff;
}

.fest-card-body {
  padding: 24px 28px;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.fest-meta-item {
  display: flex;
  align-items: center;
  gap: 14px;
}

.fest-meta-icon {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  background: #f1f5f9;
  color: #4f46e5;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  flex-shrink: 0;
}

.fest-meta-label {
  font-size: 0.75rem;
  color: #64748b;
  text-transform: uppercase;
  font-weight: 600;
  letter-spacing: 0.3px;
  margin-bottom: 2px;
}

.fest-meta-value {
  font-size: 0.95rem;
  font-weight: 600;
  color: #0f172a;
}

.fest-card-footer {
  padding: 16px 28px;
  background: #f8fafc;
  border-top: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.btn-card-edit {
  background: linear-gradient(135deg, #4f46e5, #6366f1);
  color: #ffffff;
  border: none;
  border-radius: 20px;
  padding: 8px 20px;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  text-decoration: none;
  transition: all 0.2s;
}

.btn-card-edit:hover {
  color: #ffffff;
  box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
}

@media (max-width: 768px) {
  .header-hero {
    padding: 20px;
    border-radius: 16px;
  }
  .form-card-body, .fest-card-body, .fest-card-header, .fest-card-footer {
    padding: 18px 20px;
  }
  .cards-grid {
    grid-template-columns: 1fr;
  }
  .btn-action-primary, .btn-action-secondary {
    width: 100%;
    justify-content: center;
  }
}
</style>

<div class="kalolsavam-container">

  <!-- Header Hero Banner -->
  <div class="header-hero">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <div class="header-badge">
          <i class="bi bi-shield-check"></i> State Administration Settings
        </div>
        <h1><i class="bi bi-trophy-fill text-warning"></i> Kalolsavam Management</h1>
        <p>Manage Tamil Kalolsavam Master Schedule, Venues & Information</p>
      </div>
      <div>
        <?php if (!empty($kalolsavam_details) && $kalolsavam_details[0]['status'] == 'O') { ?>
          <button type="button" id="btnOpenEdit" class="btn btn-light rounded-pill px-4 py-2 fw-semibold text-primary shadow-sm" onclick="showFormCard();">
            <i class="bi bi-pencil-square me-1"></i> Edit Kalolsavam
          </button>
        <?php } ?>
      </div>
    </div>
  </div>

  <!-- KPI Overview Cards -->
  <?php 
    $active_fest = !empty($kalolsavam_details) ? $kalolsavam_details[0] : null;
    $start_d = ($active_fest && $active_fest['start_date'] != '0000-00-00') ? date("d M Y", strtotime($active_fest['start_date'])) : 'Not Set';
    $end_d = ($active_fest && $active_fest['end_date'] != '0000-00-00') ? date("d M Y", strtotime($active_fest['end_date'])) : 'Not Set';
    $venue = ($active_fest && !empty($active_fest['venue'])) ? $active_fest['venue'] : 'Venue Not Configured';
    $fest_year = $active_fest['kalolsavam_year'] ?? date('Y') . '-' . (date('Y') + 1);
    $has_selected = (count(@$selected_kalolsavam ?? []) > 0);
  ?>
  <div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="kpi-card">
        <div class="kpi-icon-box kpi-icon-indigo">
          <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div class="kpi-content">
          <h6>Academic Year</h6>
          <h4><?php echo esc($fest_year); ?></h4>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="kpi-card">
        <div class="kpi-icon-box kpi-icon-purple">
          <i class="bi bi-geo-alt-fill"></i>
        </div>
        <div class="kpi-content">
          <h6>Venue</h6>
          <h4 class="text-truncate" style="max-width: 170px;" title="<?php echo esc($venue); ?>"><?php echo esc($venue); ?></h4>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="kpi-card">
        <div class="kpi-icon-box kpi-icon-sky">
          <i class="bi bi-calendar2-range-fill"></i>
        </div>
        <div class="kpi-content">
          <h6>Event Dates</h6>
          <h4 style="font-size: 0.95rem;"><?php echo ($start_d !== 'Not Set') ? $start_d : 'Not Scheduled'; ?></h4>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="kpi-card">
        <div class="kpi-icon-box kpi-icon-amber">
          <i class="bi bi-award-fill"></i>
        </div>
        <div class="kpi-content">
          <h6>Status</h6>
          <h4><span class="badge bg-primary rounded-pill px-3 py-1">Active</span></h4>
        </div>
      </div>
    </div>
  </div>

  <!-- Form Card (Add / Edit Form) -->
  <div class="modern-form-card" id="formCard" style="<?php echo $has_selected ? 'display:block;' : 'display:none;'; ?>">
    <div class="form-card-header">
      <h3 class="form-card-title">
        <i class="bi bi-sliders text-primary"></i> 
        <span id="formTitle"><?php echo $has_selected ? 'Edit Kalolsavam Details' : 'Add New Kalolsavam'; ?></span>
      </h3>
      <button type="button" class="btn-close" onclick="hideFormCard();" aria-label="Close"></button>
    </div>

    <div class="form-card-body">
      <form id="formKalolsavam" method="post" enctype="multipart/form-data" action="<?php echo base_url('admin/kalolsavam/update_kalolsavam'); ?>">
        <input type="hidden" name="kalolsavam_id" id="kalolsavam_id" value="<?php echo esc((string)(@$selected_kalolsavam[0]['kalolsavam_id'] ?? ($active_fest['kalolsavam_id'] ?? ($kalolsavam_id ?? '')))); ?>" />
        
        <div class="row g-4">
          <!-- Form Inputs Column -->
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
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                  <div class="date-presets">
                    <span class="text-muted small me-1">Presets:</span>
                    <button type="button" class="preset-chip" onclick="setQuickDate(1)">1 Day</button>
                    <button type="button" class="preset-chip" onclick="setQuickDate(3)">3 Days</button>
                    <button type="button" class="preset-chip" onclick="setQuickDate(5)">5 Days</button>
                    <button type="button" class="preset-chip" onclick="setQuickDate(7)">1 Week</button>
                    <button type="button" class="preset-chip text-danger" onclick="clearDates()">Clear</button>
                  </div>
                  <div id="durationBadge" class="duration-pill" style="display:none;">
                    <i class="bi bi-clock-history"></i> <span id="durationText">0 Days</span>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!-- Logo Upload Column -->
          <div class="col-12 col-lg-4">
            <label class="custom-label">
              Festival Logo
            </label>
            <div class="logo-upload-box">
              <div id="logoPreviewContainer">
                <?php 
                  $current_logo = @$selected_kalolsavam[0]['logo_name'] ?? ($active_fest['logo_name'] ?? '');
                  if (!empty($current_logo) && file_exists(FCPATH . 'uploads/state/thumb_' . $current_logo)) { 
                ?>
                  <img src="<?php echo base_url('uploads/state/thumb_' . $current_logo); ?>" id="previewImg" class="logo-preview-img" alt="Logo" />
                <?php } else { ?>
                  <div id="logoPlaceholder" style="font-size: 3rem; color: #94a3b8; margin-bottom: 8px;">
                    <i class="bi bi-image"></i>
                  </div>
                  <img src="" id="previewImg" class="logo-preview-img" alt="Logo" style="display:none;" />
                <?php } ?>
              </div>

              <p class="text-muted small mb-3">Accepted: JPG, PNG, GIF (Max 2MB)</p>
              
              <input type="file" name="kalolsavamLogo" id="kalolsavamLogo" accept="image/*" style="display:none;" onchange="handleLogoPreview(this);" />
              <button type="button" class="btn-upload-trigger" onclick="document.getElementById('kalolsavamLogo').click();">
                <i class="bi bi-cloud-arrow-up-fill"></i> Upload Logo
              </button>
            </div>
          </div>
        </div>

        <!-- Submit & Action Buttons -->
        <div class="d-flex justify-content-center align-items-center gap-3 mt-4 pt-3 border-top flex-wrap">
          <button type="button" id="btnSubmitKalolsavam" class="btn-action-primary" onclick="submitKalolsavamAjax();">
            <i class="bi bi-check-circle-fill"></i> <span id="btnSubmitText">Save Kalolsavam Details</span>
          </button>
          <button type="button" class="btn-action-secondary" onclick="hideFormCard();">
            <i class="bi bi-x-circle"></i> Cancel
          </button>
        </div>

      </form>
    </div>
  </div>

  <!-- Kalolsavam Records Section (Hidden when form is open) -->
  <div id="kalolsavamList" style="<?php echo $has_selected ? 'display:none;' : 'display:block;'; ?>">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <h4 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
        <i class="bi bi-collection-fill text-primary"></i> Kalolsavam Records
      </h4>
      <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
        Total: <?php echo count($kalolsavam_details ?? []); ?> Records
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
            <div class="fest-card-header">
              <span class="fest-status-badge">
                <?php echo $is_open ? '● Active' : '○ Closed'; ?>
              </span>
              <h2 class="fest-card-year"><?php echo esc($fest['kalolsavam_year']); ?></h2>
              <p class="fest-card-name"><?php echo esc($fest['kalolsavam_name']); ?></p>
            </div>

            <div class="fest-card-body">
              <div class="fest-meta-item">
                <div class="fest-meta-icon"><i class="bi bi-geo-alt"></i></div>
                <div>
                  <div class="fest-meta-label">Venue</div>
                  <div class="fest-meta-value"><?php echo esc(!empty($fest['venue']) ? $fest['venue'] : 'Not Configured'); ?></div>
                </div>
              </div>

              <div class="fest-meta-item">
                <div class="fest-meta-icon"><i class="bi bi-calendar3"></i></div>
                <div>
                  <div class="fest-meta-label">Schedule Duration</div>
                  <div class="fest-meta-value"><?php echo esc($date_display); ?></div>
                </div>
              </div>

              <?php if (!empty($fest['logo_name'])) { ?>
                <div class="fest-meta-item">
                  <div class="fest-meta-icon"><i class="bi bi-award"></i></div>
                  <div>
                    <div class="fest-meta-label">Logo</div>
                    <div class="fest-meta-value text-primary"><small><i class="bi bi-check2-circle"></i> Uploaded</small></div>
                  </div>
                </div>
              <?php } ?>
            </div>

            <div class="fest-card-footer">
              <span class="text-muted small">Record ID: #<?php echo esc($fest['kalolsavam_id']); ?></span>
              <?php if ($is_open) { ?>
                <button type="button" class="btn-card-edit" onclick="fncEditKalolsavam('<?php echo $fest['kalolsavam_id']; ?>');">
                  <i class="bi bi-pencil"></i> Edit Details
                </button>
              <?php } else { ?>
                <span class="badge bg-secondary">Archived</span>
              <?php } ?>
            </div>
          </div>
        <?php } ?>
      </div>
    <?php } else { ?>
      <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
        <div style="font-size: 3.5rem; color: #cbd5e1;"><i class="bi bi-calendar-x"></i></div>
        <h5 class="mt-3 text-secondary">No Kalolsavam Records Found</h5>
        <p class="text-muted">Create a new record using the form above.</p>
      </div>
    <?php } ?>
  </div>

</div>

<!-- Hidden Form for Legacy Post Submissions -->
<form id="list_formKalolsavam" method="post" action="<?php echo base_url('admin/kalolsavam/edit_kalolsavam'); ?>" style="display:none;">
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

        // If End Date is not set yet or earlier than Start Date, automatically synchronize to the same date / month / year
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
      // If End Date is not set yet, jump to the month and year of Start Date
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

  // Initial sync on page load if Start Date exists
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

  var formData = new FormData();
  formData.append('kalolsavam_id', id);

  fetch('<?php echo base_url("admin/kalolsavam/get_kalolsavam_ajax"); ?>', {
    method: 'POST',
    body: formData
  })
  .then(function(res) { return res.json(); })
  .then(function(json) {
    Swal.close();
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
  .catch(function() {
    Swal.close();
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

  fetch('<?php echo base_url("admin/kalolsavam/save_kalolsavam_ajax"); ?>', {
    method: 'POST',
    body: formData
  })
  .then(function(res) { return res.json(); })
  .then(function(data) {
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalHtml;

    if (data.success) {
      hideFormCard();
      Swal.fire({
        title: 'Success!',
        text: data.message || 'Kalolsavam details have been saved successfully.',
        icon: 'success',
        confirmButtonColor: '#4f46e5'
      }).then(function() {
        window.location.href = '<?php echo base_url("admin/kalolsavam"); ?>';
      });
    } else {
      Swal.fire('Error', data.message || 'Failed to save Kalolsavam details.', 'error');
    }
  })
  .catch(function() {
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalHtml;
    formEl.submit();
  });
}
</script>
