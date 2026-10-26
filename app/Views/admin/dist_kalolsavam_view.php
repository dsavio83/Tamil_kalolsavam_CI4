<!-- Flatpickr CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
:root {
  --primary-gradient: linear-gradient(135deg, #0284c7 0%, #2563eb 50%, #4f46e5 100%);
  --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
  --card-hover-shadow: 0 16px 32px rgba(2, 132, 199, 0.18);
}

.kalolsavam-container {
  padding: 12px 0 35px 0;
  font-family: 'Poppins', system-ui, -apple-system, sans-serif;
}

.header-hero {
  background: var(--primary-gradient);
  border-radius: 18px;
  padding: 26px 32px;
  color: #ffffff;
  box-shadow: 0 10px 25px rgba(2, 132, 199, 0.28);
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
  border-color: #0284c7;
  box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.12);
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
  color: #0284c7;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 4px;
}

.logo-upload-box {
  border: 2px dashed #cbd5e1;
  border-radius: 16px;
  padding: 24px;
  text-align: center;
  background: #f8fafc;
  transition: all 0.2s ease;
}

.logo-upload-box:hover {
  border-color: #0284c7;
  background: #f0f9ff;
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
  background: #0284c7;
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
  background: #0369a1;
  color: #ffffff;
}

.btn-action-primary {
  background: var(--primary-gradient);
  color: #ffffff;
  border: none;
  border-radius: 28px;
  padding: 12px 32px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  box-shadow: 0 6px 20px rgba(2, 132, 199, 0.28);
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.btn-action-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(2, 132, 199, 0.4);
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

.fest-card-name {
  font-size: 1.25rem;
  font-weight: 700;
  margin: 0;
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
  background: #f0f9ff;
  color: #0284c7;
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
  background: linear-gradient(135deg, #0284c7, #2563eb);
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
  box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);
}

.duration-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 14px;
  background: #e0f2fe;
  color: #0369a1;
  border-radius: 14px;
  font-size: 0.82rem;
  font-weight: 600;
  margin-top: 8px;
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

  <!-- Header Banner -->
  <div class="header-hero">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <div class="header-badge">
          <i class="bi bi-geo-alt-fill"></i> District Level Settings
        </div>
        <h1><i class="bi bi-award-fill text-warning"></i> District Kalolsavam Management</h1>
        <p>Manage District Tamil Kalolsavam Venue, Dates & Information</p>
      </div>
      <div>
        <button type="button" onclick="window.print()" class="btn btn-light rounded-pill px-3 py-2 fw-semibold text-secondary shadow-sm">
          <i class="bi bi-printer me-1"></i> Print
        </button>
      </div>
    </div>
  </div>

  <?php $has_selected = (count(@$selected_kalolsavam ?? []) > 0); ?>

  <!-- Form Card (Hidden by default, shown on edit) -->
  <div class="modern-form-card" id="formCard" style="<?php echo $has_selected ? 'display:block;' : 'display:none;'; ?>">
    <div class="form-card-header">
      <h3 class="form-card-title">
        <i class="bi bi-pencil-square text-primary"></i> 
        <span id="formTitle">Edit District Kalolsavam</span>
      </h3>
      <button type="button" class="btn-close" onclick="hideFormCard();" aria-label="Close"></button>
    </div>

    <div class="form-card-body">
      <form id="formKalolsavam" method="post" enctype="multipart/form-data" action="<?php echo base_url('admin/kalolsavam/update_kalolsavam'); ?>">
        <?= csrf_field(); ?>
        <input type="hidden" name="kalolsavam_id" id="kalolsavam_id" value="<?php echo esc((string)(@$selected_kalolsavam[0]['dist_kalolsavam_id'] ?? ($kalolsavam_details[0]['dist_kalolsavam_id'] ?? ($kalolsavam_id ?? '')))); ?>" />
        
        <div class="row g-4">
          <div class="col-12 col-lg-8">
            <div class="row g-3">
              
              <div class="col-12">
                <label class="custom-label">District Kalolsavam Name</label>
                <input type="text" class="custom-input" id="txtKalolsavamNameDisplay" value="<?php echo esc((string)(@$selected_kalolsavam[0]['dist_kalolsavam_name'] ?? ($kalolsavam_details[0]['dist_kalolsavam_name'] ?? ''))); ?>" readonly style="background:#f1f5f9;cursor:not-allowed;" />
              </div>

              <div class="col-12">
                <label class="custom-label" for="txtKalolsavamVenue">
                  Venue <span class="text-danger">*</span>
                </label>
                <input type="text" name="txtKalolsavamVenue" id="txtKalolsavamVenue" class="custom-input" placeholder="e.g. Town Hall Ground" value="<?php echo esc((string)(@$selected_kalolsavam[0]['venue'] ?? ($kalolsavam_details[0]['venue'] ?? ''))); ?>" required />
              </div>

              <div class="col-12 col-md-6">
                <label class="custom-label" for="txtStartDate">Start Date</label>
                <div class="date-input-group">
                  <?php 
                    $curr_start = (!empty(@$selected_kalolsavam[0]['start_date']) && @$selected_kalolsavam[0]['start_date'] !== '0000-00-00') 
                      ? (string)@$selected_kalolsavam[0]['start_date'] 
                      : ((!empty($kalolsavam_details[0]['start_date']) && $kalolsavam_details[0]['start_date'] !== '0000-00-00') ? (string)$kalolsavam_details[0]['start_date'] : '');
                  ?>
                  <input type="text" name="txtStartDate" id="txtStartDate" class="custom-input datepicker" placeholder="Select start date" value="<?php echo esc($curr_start); ?>" />
                  <button type="button" class="date-calendar-btn" onclick="openPicker('txtStartDate')"><i class="bi bi-calendar-event"></i></button>
                </div>
              </div>

              <div class="col-12 col-md-6">
                <label class="custom-label" for="txtEndDate">End Date</label>
                <div class="date-input-group">
                  <?php 
                    $curr_end = (!empty(@$selected_kalolsavam[0]['end_date']) && @$selected_kalolsavam[0]['end_date'] !== '0000-00-00') 
                      ? (string)@$selected_kalolsavam[0]['end_date'] 
                      : ((!empty($kalolsavam_details[0]['end_date']) && $kalolsavam_details[0]['end_date'] !== '0000-00-00') ? (string)$kalolsavam_details[0]['end_date'] : '');
                  ?>
                  <input type="text" name="txtEndDate" id="txtEndDate" class="custom-input datepicker" placeholder="Select end date" value="<?php echo esc($curr_end); ?>" />
                  <button type="button" class="date-calendar-btn" onclick="openPicker('txtEndDate')"><i class="bi bi-calendar-check"></i></button>
                </div>
              </div>

              <div class="col-12">
                <div id="durationBadge" class="duration-pill" style="display:none;">
                  <i class="bi bi-clock-history"></i> <span id="durationText">0 Days</span>
                </div>
              </div>

            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label class="custom-label">District Logo</label>
            <div class="logo-upload-box">
              <div id="logoPreviewContainer">
                <?php 
                  $current_logo = @$selected_kalolsavam[0]['logo_name'] ?? ($kalolsavam_details[0]['logo_name'] ?? '');
                  if (!empty($current_logo) && file_exists(FCPATH . 'uploads/district/thumb_' . $current_logo)) { 
                ?>
                  <img src="<?php echo base_url('uploads/district/thumb_' . $current_logo); ?>" id="previewImg" class="logo-preview-img" alt="Logo" />
                <?php } else { ?>
                  <div id="logoPlaceholder" style="font-size: 3rem; color: #94a3b8; margin-bottom: 8px;"><i class="bi bi-image"></i></div>
                  <img src="" id="previewImg" class="logo-preview-img" alt="Logo" style="display:none;" />
                <?php } ?>
              </div>
              <p class="text-muted small mb-3">Accepted: JPG, PNG, GIF</p>
              <input type="file" name="kalolsavamLogo" id="kalolsavamLogo" accept="image/*" style="display:none;" onchange="handleLogoPreview(this);" />
              <button type="button" class="btn-upload-trigger" onclick="document.getElementById('kalolsavamLogo').click();">
                <i class="bi bi-cloud-arrow-up-fill"></i> Upload Logo
              </button>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-center align-items-center gap-3 mt-4 pt-3 border-top flex-wrap">
          <button type="button" id="btnSubmitKalolsavam" class="btn-action-primary" onclick="submitDistKalolsavamAjax();">
            <i class="bi bi-check-circle-fill"></i> Save Changes
          </button>
          <button type="button" class="btn-action-secondary" onclick="hideFormCard();">
            <i class="bi bi-x-circle"></i> Cancel
          </button>
        </div>

      </form>
    </div>
  </div>

  <!-- Records Grid (Hidden when edit form is open) -->
  <div id="kalolsavamList" style="<?php echo $has_selected ? 'display:none;' : 'display:block;'; ?>">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <h4 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
        <i class="bi bi-building text-primary"></i> District Kalolsavam Records
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
              <h2 class="fest-card-name"><?php echo esc($fest['dist_kalolsavam_name']); ?></h2>
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
            </div>

            <div class="fest-card-footer">
              <span class="text-muted small">Status: <?php echo $is_open ? '● Active' : '○ Closed'; ?></span>
              <?php if ($is_open) { ?>
                <button type="button" class="btn-card-edit" onclick="fncEditKalolsavam('<?php echo $fest['dist_kalolsavam_id']; ?>');">
                  <i class="bi bi-pencil"></i> Edit Details
                </button>
              <?php } ?>
            </div>
          </div>
        <?php } ?>
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
    onChange: function() { calculateDuration(); }
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
  if (el && el._flatpickr) el._flatpickr.open();
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
    text: 'Fetching District Kalolsavam details...',
    allowOutsideClick: false,
    didOpen: function() { Swal.showLoading(); }
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
      document.getElementById('kalolsavam_id').value = d.dist_kalolsavam_id || '';
      document.getElementById('txtKalolsavamNameDisplay').value = d.dist_kalolsavam_name || '';
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

function submitDistKalolsavamAjax() {
  var venue = document.getElementById('txtKalolsavamVenue').value.trim();
  var start = document.getElementById('txtStartDate').value.trim();
  var end = document.getElementById('txtEndDate').value.trim();

  if (!venue) {
    Swal.fire('Required Field', 'Please enter Venue.', 'warning');
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
        text: data.message || 'District Kalolsavam details have been updated successfully.',
        icon: 'success',
        confirmButtonColor: '#0284c7'
      }).then(function() {
        window.location.href = '<?php echo base_url("admin/kalolsavam"); ?>';
      });
    } else {
      Swal.fire('Error', data.message || 'Failed to save details.', 'error');
    }
  })
  .catch(function(err) {
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalHtml;
    console.error('Save error:', err);
    formEl.submit();
  });
}
</script>


