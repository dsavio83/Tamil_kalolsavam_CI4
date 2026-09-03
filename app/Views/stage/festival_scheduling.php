<div class="pagetitle mb-3">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="d-flex align-items-center gap-2 text-dark fw-bold mb-1">
        <i class="bi bi-cpu-fill text-primary"></i>
        <span>Automated Stage Scheduling</span>
      </h1>
      <p class="text-muted small mb-0">AI-powered stage allocation and event scheduling with participant clash prevention.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs" data-bs-toggle="modal" data-bs-target="#geminiKeyModal">
        <i class="bi bi-key-fill text-warning"></i>
        <span>Gemini API Key</span>
        <?php if (!empty($has_gemini_key)): ?>
          <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 ms-1" style="font-size: 11px;">Active</span>
        <?php else: ?>
          <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 ms-1" style="font-size: 11px;">Not Set</span>
        <?php endif; ?>
      </button>

      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-light px-3 py-1.5 rounded-pill border">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none"><i class="bi bi-house-door-fill me-1"></i>Home</a></li>
          <li class="breadcrumb-item text-muted">Stage</li>
          <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Auto Scheduling</li>
        </ol>
      </nav>
    </div>
  </div>
</div>

<?php if(isset($message) && $message): ?>
  <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-check-circle-fill fs-5"></i>
      <div><?php echo $message; ?></div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if(isset($error) && $error): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-exclamation-octagon-fill fs-5"></i>
      <div><?php echo $error; ?></div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<!-- Modern AI & Engine Showcase Card -->
<div class="card border-0 rounded-4 mb-4 text-white overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 45%, #312e81 100%);">
  <div class="card-body p-4 p-lg-4">
    <div class="row align-items-center g-3">
      <div class="col-12 col-lg-8">
        <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
          <span class="badge bg-white text-dark rounded-pill px-3 py-1 fw-bold shadow-sm d-inline-flex align-items-center gap-1 font-monospace" style="font-size: 11px;">
            <i class="bi bi-stars text-warning"></i> Gemini AI Ready
          </span>
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-semibold font-monospace" style="font-size: 11px;">
            Zero-Clash Engine
          </span>
          <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1 fw-semibold font-monospace" style="font-size: 11px;">
            Multi-Stage Balanced
          </span>
        </div>
        <h4 class="fw-bold mb-1 text-white">Intelligent Stage Allocation Hub</h4>
        <p class="text-light opacity-75 mb-0 small" style="line-height: 1.5;">
          Automatically schedules on-stage items, off-stage writing sessions, and assigns stages while eliminating participant timetable clashes.
        </p>
      </div>

      <div class="col-12 col-lg-4 text-lg-end">
        <div class="p-3 rounded-3 border border-white border-opacity-10 bg-white bg-opacity-10 text-center d-inline-block w-100" style="max-width: 320px;">
          <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
            <i class="bi bi-cpu text-warning fs-5"></i>
            <span class="fw-bold text-white fs-6">AI Smart Scheduler</span>
          </div>
          <small class="text-light opacity-75 d-block mb-2">Configure dates and auto-assign stages in 1 click</small>
          <button type="button" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold text-dark w-100" data-bs-toggle="modal" data-bs-target="#geminiKeyModal">
            <i class="bi bi-gear-fill me-1"></i> API Key Settings
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Main Settings Form Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="border: 1px solid #e2e8f0 !important;">
  <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: linear-gradient(135deg, #f8fafc, #ffffff) !important;">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-sliders2-vertical text-primary fs-5"></i>
      <h5 class="mb-0 fw-bold text-dark">Festival Selection & Schedule Parameters</h5>
    </div>
    <span class="badge bg-light text-muted border rounded-pill px-3 py-1 font-monospace">Step 1: Configuration</span>
  </div>

  <div class="card-body p-4">
    <!-- Row 1: Festival & Date Range -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-lg-4">
        <label for="fest_id" class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
          <i class="bi bi-trophy text-warning"></i>
          <span>Festival Category <span class="text-danger">*</span></span>
        </label>
        <div class="input-group">
          <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-tags-fill"></i></span>
          <select class="form-select border-start-0 py-2 fs-6" id="fest_id" name="fest_id" required onchange="loadFestivalDates()">
            <option value="">-- Choose Festival Category --</option>
            <?php if(isset($festivals) && $festivals): ?>
              <?php foreach($festivals as $idx => $festival): ?>
                <option value="<?php echo esc($festival['fest_id']); ?>" <?php echo ($idx === 0) ? 'selected' : ''; ?>>
                  <?php echo esc($festival['fest_name']); ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <label for="start_date" class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
          <i class="bi bi-calendar-event text-primary"></i>
          <span>Start Date <span class="text-danger">*</span></span>
        </label>
        <div class="input-group">
          <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-check"></i></span>
          <input type="date" class="form-control border-start-0 py-2" id="start_date" name="start_date" value="2024-10-29" required>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <label for="end_date" class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
          <i class="bi bi-calendar-event-fill text-primary"></i>
          <span>End Date <span class="text-danger">*</span></span>
        </label>
        <div class="input-group">
          <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-check-fill"></i></span>
          <input type="date" class="form-control border-start-0 py-2" id="end_date" name="end_date" value="2024-11-01" required>
        </div>
      </div>
    </div>

    <!-- Time Slot Engine Settings Box -->
    <div class="p-3 p-md-4 rounded-4 bg-light border mb-4">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-clock-history text-primary fs-5"></i>
          <h6 class="mb-0 fw-bold text-dark">Daily Timetable & Slot Settings</h6>
        </div>
        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="resetToDefaults()">
          <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Defaults
        </button>
      </div>

      <div class="row g-3">
        <div class="col-12 col-md-4">
          <label for="start_time" class="form-label fw-semibold text-muted small">Daily Start Time:</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-sun"></i></span>
            <select class="form-select border-start-0 py-2 font-monospace" id="start_time" name="start_time">
              <option value="09:00:00" selected>09:00 AM</option>
              <option value="08:00:00">08:00 AM</option>
              <option value="08:30:00">08:30 AM</option>
              <option value="10:00:00">10:00 AM</option>
            </select>
          </div>
        </div>

        <div class="col-12 col-md-4">
          <label for="end_time" class="form-label fw-semibold text-muted small">Daily End Time:</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-moon-stars"></i></span>
            <select class="form-select border-start-0 py-2 font-monospace" id="end_time" name="end_time">
              <option value="17:00:00" selected>05:00 PM</option>
              <option value="16:00:00">04:00 PM</option>
              <option value="16:30:00">04:30 PM</option>
              <option value="18:00:00">06:00 PM</option>
            </select>
          </div>
        </div>

        <div class="col-12 col-md-4">
          <label for="slot_duration" class="form-label fw-semibold text-muted small">Default Slot Duration:</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-stopwatch"></i></span>
            <select class="form-select border-start-0 py-2 font-monospace" id="slot_duration" name="slot_duration">
              <option value="60" selected>60 Minutes (1 Hour)</option>
              <option value="30">30 Minutes</option>
              <option value="45">45 Minutes</option>
              <option value="90">90 Minutes (1.5 Hours)</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Command Center Buttons -->
    <div class="d-flex align-items-center justify-content-center flex-wrap gap-2 pt-2">
      <!-- Main Glowing AI Trigger Button -->
      <button type="button" id="btnGenerateSchedule" class="btn btn-primary btn-lg rounded-pill shadow-sm px-4 py-2.5 fw-bold d-inline-flex align-items-center gap-2" onclick="generateSchedule(event)">
        <i class="bi bi-magic fs-4 text-warning"></i>
        <span>Auto Set Stage Schedule</span>
      </button>

      <button type="button" class="btn btn-outline-info rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1" onclick="viewSchedule()">
        <i class="bi bi-calendar3"></i>
        <span>View Schedule</span>
      </button>

      <button type="button" class="btn btn-outline-warning text-dark rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1" onclick="analyzeClashes()">
        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
        <span>Clash Analysis</span>
      </button>

      <button type="button" class="btn btn-outline-success rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1" onclick="viewStatistics()">
        <i class="bi bi-bar-chart-fill"></i>
        <span>Statistics</span>
      </button>

      <a href="<?php echo base_url(); ?>stage/festival_scheduling/order_of_events" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1">
        <i class="bi bi-list-ol"></i>
        <span>Order of Events</span>
      </a>

      <button type="button" class="btn btn-outline-danger rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1" onclick="clearSchedule()">
        <i class="bi bi-trash3"></i>
        <span>Clear Schedule</span>
      </button>
    </div>
  </div>
</div>

<!-- Scheduling Metrics Grid -->
<div class="row g-3 mb-4">
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border: 1px solid #e2e8f0 !important;">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
          <i class="bi bi-lightning-charge-fill fs-4"></i>
        </div>
        <div>
          <div class="text-muted small fw-semibold">Scheduling Engine</div>
          <div class="fw-bold text-dark fs-6 font-monospace">Auto & Gemini AI</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border: 1px solid #e2e8f0 !important;">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
          <i class="bi bi-shield-check fs-4"></i>
        </div>
        <div>
          <div class="text-muted small fw-semibold">Clash Detection</div>
          <div class="fw-bold text-success fs-6 font-monospace">Zero Clashes Active</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border: 1px solid #e2e8f0 !important;">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-warning-subtle text-warning-emphasis p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
          <i class="bi bi-clock-history fs-4"></i>
        </div>
        <div>
          <div class="text-muted small fw-semibold">Time Window</div>
          <div class="fw-bold text-dark fs-6 font-monospace">09:00 - 17:00</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white" style="border: 1px solid #e2e8f0 !important;">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-info-subtle text-info p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
          <i class="bi bi-geo-alt-fill fs-4"></i>
        </div>
        <div>
          <div class="text-muted small fw-semibold">Stage Assignment</div>
          <div class="fw-bold text-dark fs-6 font-monospace">Load Balanced</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Gemini AI API Key Modal -->
<div class="modal fade" id="geminiKeyModal" tabindex="-1" aria-labelledby="geminiKeyModalLabel" aria-hidden="true" style="z-index: 10050;">
  <div class="modal-dialog modal-dialog-centered" style="margin-top: 80px !important;">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header bg-dark text-white py-3 px-4 border-0">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-stars text-warning fs-5"></i>
          <h5 class="modal-title fw-bold text-white fs-6" id="geminiKeyModalLabel">Gemini AI API Configuration</h5>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <p class="text-muted small mb-3">
          Enter your Google Gemini API key to enable intelligent auto-scheduling, clash optimization, and stage load balancing.
        </p>

        <div class="mb-3">
          <label for="geminiApiKeyInput" class="form-label fw-bold text-dark small">Gemini API Key:</label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
            <input type="password" class="form-control border-start-0 border-end-0 font-monospace" id="geminiApiKeyInput" placeholder="AIzaSy..." value="<?php echo esc($gemini_api_key ?? ''); ?>">
            <button class="btn btn-outline-secondary border-start-0" type="button" onclick="toggleKeyVisibility()" title="Show/Hide Key">
              <i class="bi bi-eye" id="toggleKeyIcon"></i>
            </button>
          </div>
          <div class="form-text small text-muted">
            Key is securely stored in database settings. You can obtain a free API key from <a href="https://aistudio.google.com/" target="_blank" class="text-decoration-none">Google AI Studio</a>.
          </div>
        </div>

        <div id="geminiKeyAlert" class="alert d-none py-2 px-3 rounded-3 small"></div>

        <div class="d-flex align-items-center justify-content-between pt-2">
          <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="testGeminiKeyAjax()" id="btnTestKey">
            <i class="bi bi-wifi me-1"></i> Test Connection
          </button>
          <button type="button" class="btn btn-primary rounded-pill px-4 btn-sm fw-semibold shadow-sm" onclick="saveGeminiKeyAjax()" id="btnSaveKey">
            <i class="bi bi-check-lg me-1"></i> Save API Key
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function loadFestivalDates() {
    var festId = document.getElementById('fest_id').value;
    if (!festId) {
        document.getElementById('start_date').value = '';
        document.getElementById('end_date').value = '';
        return;
    }

    // Default festival dates if not set
    if (!document.getElementById('start_date').value) {
        document.getElementById('start_date').value = '2024-10-29';
    }
    if (!document.getElementById('end_date').value) {
        document.getElementById('end_date').value = '2024-11-01';
    }
}

function generateSchedule(e) {
    var festSelect = document.getElementById('fest_id');
    var festId = festSelect ? festSelect.value : '';
    if (!festId && festSelect && festSelect.options.length > 1) {
        festSelect.selectedIndex = 1;
        festId = festSelect.value;
    }

    var startDateInput = document.getElementById('start_date');
    var endDateInput = document.getElementById('end_date');
    if (startDateInput && !startDateInput.value) startDateInput.value = '2024-10-29';
    if (endDateInput && !endDateInput.value) endDateInput.value = '2024-11-01';

    var startDate = startDateInput ? startDateInput.value : '2024-10-29';
    var endDate = endDateInput ? endDateInput.value : '2024-11-01';
    var startTime = (document.getElementById('start_time') ? document.getElementById('start_time').value : '') || '09:00:00';
    var endTime = (document.getElementById('end_time') ? document.getElementById('end_time').value : '') || '17:00:00';
    var slotDuration = (document.getElementById('slot_duration') ? document.getElementById('slot_duration').value : '') || '60';

    if (!festId) {
        alert('Please select a festival category.');
        return;
    }

    // Show button loading feedback
    var btn = document.getElementById('btnGenerateSchedule') || (e ? (e.currentTarget || e.target) : null);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Generating Schedule...';
    }
    if (typeof window.showGlobalLoader === 'function') {
        window.showGlobalLoader('Generating stage schedule with clash prevention...', true);
    }

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo site_url("stage/festival_scheduling/generate_schedule"); ?>';

    var params = {
        'fest_id': festId,
        'start_date': startDate,
        'end_date': endDate,
        'start_time': startTime,
        'end_time': endTime,
        'slot_duration': slotDuration,
        'use_ai': 'Y'
    };

    for (var key in params) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = params[key];
        form.appendChild(input);
    }

    document.body.appendChild(form);
    form.submit();
}

function resetToDefaults() {
    document.getElementById('start_time').value = '09:00:00';
    document.getElementById('end_time').value = '17:00:00';
    document.getElementById('slot_duration').value = '60';
}

function viewSchedule() {
    var festId = document.getElementById('fest_id').value;
    if (!festId) {
        alert('Please select a festival category first.');
        return;
    }

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo site_url("stage/festival_scheduling/view_schedule"); ?>';

    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'fest_id';
    input.value = festId;
    form.appendChild(input);

    document.body.appendChild(form);
    form.submit();
}

function analyzeClashes() {
    var festId = document.getElementById('fest_id').value;
    if (!festId) {
        alert('Please select a festival category first.');
        return;
    }

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo site_url("stage/festival_scheduling/analyze_clashes"); ?>';

    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'fest_id';
    input.value = festId;
    form.appendChild(input);

    document.body.appendChild(form);
    form.submit();
}

function viewStatistics() {
    var festId = document.getElementById('fest_id').value;
    if (!festId) {
        alert('Please select a festival category first.');
        return;
    }

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo site_url("stage/festival_scheduling/schedule_statistics"); ?>';

    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'fest_id';
    input.value = festId;
    form.appendChild(input);

    document.body.appendChild(form);
    form.submit();
}

function clearSchedule() {
    var festId = document.getElementById('fest_id').value;
    if (!festId) {
        alert('Please select a festival category to clear.');
        return;
    }

    if (confirm('Are you sure you want to clear the entire schedule for this festival? This cannot be undone.')) {
        window.location.href = '<?php echo base_url("stage/festival_scheduling/clear_schedule?fest_id="); ?>' + encodeURIComponent(festId);
    }
}

// Gemini Key Visibility Toggle
function toggleKeyVisibility() {
    var input = document.getElementById('geminiApiKeyInput');
    var icon = document.getElementById('toggleKeyIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Save Gemini Key via AJAX
function saveGeminiKeyAjax() {
    var key = document.getElementById('geminiApiKeyInput').value.trim();
    var alertBox = document.getElementById('geminiKeyAlert');
    var btn = document.getElementById('btnSaveKey');

    if (!key) {
        alertBox.className = 'alert alert-danger py-2 px-3 rounded-3 small';
        alertBox.innerText = 'Please enter a valid API key.';
        alertBox.classList.remove('d-none');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving...';

    var formData = new FormData();
    formData.append('gemini_api_key', key);

    fetch('<?php echo site_url("stage/festival_scheduling/save_gemini_key"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save API Key';
        alertBox.classList.remove('d-none');
        if (data.status === 'success') {
            alertBox.className = 'alert alert-success py-2 px-3 rounded-3 small';
            alertBox.innerText = data.message;
            setTimeout(function() {
                var modal = bootstrap.Modal.getInstance(document.getElementById('geminiKeyModal'));
                if (modal) modal.hide();
                window.location.reload();
            }, 1200);
        } else {
            alertBox.className = 'alert alert-danger py-2 px-3 rounded-3 small';
            alertBox.innerText = data.message;
        }
    })
    .catch(function(err) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save API Key';
        alertBox.className = 'alert alert-danger py-2 px-3 rounded-3 small';
        alertBox.innerText = 'Network error while saving key.';
        alertBox.classList.remove('d-none');
    });
}

// Test Gemini Key Connection via AJAX
function testGeminiKeyAjax() {
    var key = document.getElementById('geminiApiKeyInput').value.trim();
    var alertBox = document.getElementById('geminiKeyAlert');
    var btn = document.getElementById('btnTestKey');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Testing...';

    var formData = new FormData();
    formData.append('gemini_api_key', key);

    fetch('<?php echo site_url("stage/festival_scheduling/test_gemini_key"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-wifi me-1"></i> Test Connection';
        alertBox.classList.remove('d-none');
        if (data.status === 'success') {
            alertBox.className = 'alert alert-success py-2 px-3 rounded-3 small';
            alertBox.innerText = data.message;
        } else {
            alertBox.className = 'alert alert-warning py-2 px-3 rounded-3 small';
            alertBox.innerText = data.message;
        }
    })
    .catch(function(err) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-wifi me-1"></i> Test Connection';
        alertBox.className = 'alert alert-danger py-2 px-3 rounded-3 small';
        alertBox.innerText = 'Network error during connection test.';
        alertBox.classList.remove('d-none');
    });
}

// Default dates on page load
document.addEventListener('DOMContentLoaded', function() {
    var defaultStart = new Date('2024-10-29');
    var defaultEnd = new Date('2024-11-01');

    if (!document.getElementById('start_date').value) {
        document.getElementById('start_date').value = defaultStart.toISOString().split('T')[0];
    }
    if (!document.getElementById('end_date').value) {
        document.getElementById('end_date').value = defaultEnd.toISOString().split('T')[0];
    }
});
</script>

