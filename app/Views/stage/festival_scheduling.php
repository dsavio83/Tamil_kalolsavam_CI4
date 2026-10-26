<div class="pagetitle mb-2">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="d-flex align-items-center gap-2 text-dark fw-bold mb-0 fs-4">
        <i class="bi bi-cpu-fill text-primary"></i>
        <span>Automated Stage Scheduling</span>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fs-7 fw-semibold font-monospace py-1 px-2.5">
          v2.5 AI & Multi-Stage Engine
        </span>
      </h1>
      <p class="text-muted small mb-0">High-precision stage allocation, interval-based clash prevention, and dynamic student limit audits.</p>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
      <!-- Gemini API Key Quick Status -->
      <button type="button" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs" data-bs-toggle="modal" data-bs-target="#geminiKeyModal">
        <i class="bi bi-stars text-warning"></i>
        <span class="small">Gemini AI:</span>
        <?php if (!empty($has_gemini_key)): ?>
          <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 11px;">Active (<?php echo esc($gemini_masked_key); ?>)</span>
        <?php else: ?>
          <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5" style="font-size: 11px;">Not Configured</span>
        <?php endif; ?>
      </button>

      <!-- Dynamic District Rule Pill -->
      <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs" data-bs-toggle="modal" data-bs-target="#studentLimitsModal" onclick="loadStudentLimitsAudit()">
        <i class="bi bi-person-check-fill text-primary"></i>
        <span class="small">Rule:</span>
        <span class="badge bg-primary text-white rounded-pill px-2 py-0.5" style="font-size: 11px;">
          <?php echo esc($district_limits['rule_label'] ?? 'Single: 3 | Group: 2 (Max: 5)'); ?>
        </span>
      </button>

      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-light px-2.5 py-1 rounded-pill border small">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none"><i class="bi bi-house-door-fill me-1"></i>Home</a></li>
          <li class="breadcrumb-item text-muted">Stage</li>
          <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Auto Scheduling</li>
        </ol>
      </nav>
    </div>
  </div>
</div>

<?php if(isset($message) && $message): ?>
  <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm py-2 px-3 mb-2 small" role="alert">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-check-circle-fill fs-5"></i>
      <div><?php echo $message; ?></div>
    </div>
    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if(isset($error) && $error): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm py-2 px-3 mb-2 small" role="alert">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-exclamation-octagon-fill fs-5"></i>
      <div><?php echo $error; ?></div>
    </div>
    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<!-- Dense Command Card: Parameters & Controls (Zero Space Wasted) -->
<div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden" style="border: 1px solid #e2e8f0 !important;">
  <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: linear-gradient(135deg, #f8fafc, #ffffff) !important;">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-sliders text-primary"></i>
      <span class="fw-bold text-dark small">Schedule Parameters & Stage Filters</span>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5 font-monospace small">
        <i class="bi bi-geo-alt-fill text-danger me-1"></i>Active Stages: <?php echo count($stages ?? []); ?>
      </span>
      <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5 font-monospace small">
        Dates: <?php echo esc($default_start_date ?? '2025-09-02'); ?> to <?php echo esc($default_end_date ?? '2025-09-05'); ?>
      </span>
    </div>
  </div>

  <div class="card-body p-3">
    <!-- Row 1: Festival, Event Type, Start Date, End Date -->
    <div class="row g-2 mb-2">
      <div class="col-12 col-sm-6 col-lg-3">
        <label for="fest_id" class="form-label fw-bold text-dark mb-1 small d-flex align-items-center gap-1">
          <i class="bi bi-trophy text-warning"></i>
          <span>Festival Category</span>
        </label>
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-tags-fill"></i></span>
          <select class="form-select border-start-0 py-1 font-monospace" id="fest_id" name="fest_id">
            <option value="all" selected>All Festivals (Cross-Festival)</option>
            <?php if(isset($festivals) && $festivals): ?>
              <?php foreach($festivals as $festival): ?>
                <option value="<?php echo esc($festival['fest_id']); ?>">
                  <?php echo esc($festival['fest_name']); ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <label for="stage_type_filter" class="form-label fw-bold text-dark mb-1 small d-flex align-items-center gap-1">
          <i class="bi bi-layers-half text-primary"></i>
          <span>Event Type Filter</span>
        </label>
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-funnel-fill"></i></span>
          <select class="form-select border-start-0 py-1 font-monospace" id="stage_type_filter" name="stage_type_filter">
            <option value="all" selected>All Stages (On & Off Stage)</option>
            <option value="on_stage">On-Stage Events Only</option>
            <option value="off_stage">Off-Stage Events Only</option>
          </select>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <label for="start_date" class="form-label fw-bold text-dark mb-1 small d-flex align-items-center gap-1">
          <i class="bi bi-calendar-event text-primary"></i>
          <span>Start Date</span>
        </label>
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-check"></i></span>
          <input type="date" style="color-scheme: light !important;" class="form-control border-start-0 py-1 font-monospace" id="start_date" name="start_date" value="<?php echo esc($default_start_date ?? '2025-09-02'); ?>" required>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <label for="end_date" class="form-label fw-bold text-dark mb-1 small d-flex align-items-center gap-1">
          <i class="bi bi-calendar-event-fill text-primary"></i>
          <span>End Date</span>
        </label>
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-check-fill"></i></span>
          <input type="date" style="color-scheme: light !important;" class="form-control border-start-0 py-1 font-monospace" id="end_date" name="end_date" value="<?php echo esc($default_end_date ?? '2025-09-05'); ?>" required>
        </div>
      </div>
    </div>

    <!-- Row 2: Daily Start Time, Daily End Time, Slot Duration, AI Mode -->
    <div class="row g-2 mb-3">
      <div class="col-12 col-sm-6 col-lg-3">
        <label for="start_time" class="form-label fw-bold text-dark mb-1 small d-flex align-items-center gap-1">
          <i class="bi bi-sun text-warning"></i>
          <span>Daily Start Time</span>
        </label>
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-clock"></i></span>
          <select class="form-select border-start-0 py-1 font-monospace" id="start_time" name="start_time">
            <option value="09:00:00" selected>09:00 AM</option>
            <option value="08:00:00">08:00 AM</option>
            <option value="08:30:00">08:30 AM</option>
            <option value="09:30:00">09:30 AM</option>
            <option value="10:00:00">10:00 AM</option>
          </select>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <label for="end_time" class="form-label fw-bold text-dark mb-1 small d-flex align-items-center gap-1">
          <i class="bi bi-moon-stars text-primary"></i>
          <span>Daily End Time</span>
        </label>
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-clock-fill"></i></span>
          <select class="form-select border-start-0 py-1 font-monospace" id="end_time" name="end_time">
            <option value="17:00:00" selected>05:00 PM</option>
            <option value="16:00:00">04:00 PM</option>
            <option value="16:30:00">04:30 PM</option>
            <option value="17:30:00">05:30 PM</option>
            <option value="18:00:00">06:00 PM</option>
          </select>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <label for="slot_duration" class="form-label fw-bold text-dark mb-1 small d-flex align-items-center gap-1">
          <i class="bi bi-stopwatch text-info"></i>
          <span>Default Slot Step</span>
        </label>
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-hourglass-split"></i></span>
          <select class="form-select border-start-0 py-1 font-monospace" id="slot_duration" name="slot_duration">
            <option value="30" selected>30 Minutes</option>
            <option value="45">45 Minutes</option>
            <option value="60">60 Minutes (1 Hour)</option>
            <option value="90">90 Minutes (1.5 Hours)</option>
          </select>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <label class="form-label fw-bold text-dark mb-1 small d-flex align-items-center gap-1">
          <i class="bi bi-magic text-success"></i>
          <span>Scheduling Optimization</span>
        </label>
        <div class="d-flex align-items-center justify-content-between p-1.5 px-2.5 rounded-3 bg-light border" style="height: 31px;">
          <span class="small fw-semibold text-muted font-monospace" style="font-size: 11px;">Gemini AI + Engine</span>
          <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" id="use_ai_switch" checked>
          </div>
        </div>
      </div>
    </div>

    <!-- High-Density Action Command Toolbar -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top">
      <div class="d-flex align-items-center gap-1.5 flex-wrap">
        <!-- Main Trigger -->
        <button type="button" id="btnGenerateSchedule" class="btn btn-primary btn-sm rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-sm" onclick="generateSchedule(event)">
          <i class="bi bi-magic text-warning"></i>
          <span>Auto Set Stage Schedule</span>
        </button>

        <!-- Duration Breakdown Modal Trigger -->
        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#durationModal" onclick="loadDurationBreakdown()">
          <i class="bi bi-calculator"></i>
          <span>Duration Breakdown</span>
        </button>

        <!-- Student Limits Pre-Check Trigger -->
        <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#studentLimitsModal" onclick="loadStudentLimitsAudit()">
          <i class="bi bi-people-fill"></i>
          <span>Student Limits</span>
        </button>

        <!-- Visual Timeline Gantt Trigger -->
        <button type="button" class="btn btn-outline-info text-dark btn-sm rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1" onclick="openTimelineModal()">
          <i class="bi bi-calendar-week"></i>
          <span>Timeline View</span>
        </button>

        <!-- Timetable Clash Analysis -->
        <button type="button" class="btn btn-outline-warning text-dark btn-sm rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1" onclick="analyzeClashes()">
          <i class="bi bi-shield-exclamation text-warning"></i>
          <span>Clashes Audit</span>
        </button>

        <!-- Schedule Version History Trigger -->
        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1" onclick="openVersionsModal()">
          <i class="bi bi-clock-history"></i>
          <span>Versions (<?php echo count($schedule_versions ?? []); ?>)</span>
        </button>
      </div>

      <div class="d-flex align-items-center gap-1.5 flex-wrap">
        <button type="button" class="btn btn-light border btn-sm rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1" onclick="viewSchedule()">
          <i class="bi bi-calendar3 text-primary"></i>
          <span>View Table</span>
        </button>

        <a href="<?php echo base_url(); ?>stage/festival_scheduling/order_of_events" class="btn btn-light border btn-sm rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1">
          <i class="bi bi-list-ol text-dark"></i>
          <span>Order of Events</span>
        </a>

        <a href="<?php echo site_url('stage/item_participant/participant_nodetails?fest_id=1'); ?>" class="btn btn-light border btn-sm rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1" title="View Stage Allotments">
          <i class="bi bi-calendar2-check text-success"></i>
          <span>Allotments</span>
        </a>

        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1" onclick="clearSchedule()">
          <i class="bi bi-trash3"></i>
          <span>Clear</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Dense KPI Strip: Essential Festival & Engine Metrics (1 Compact Row) -->
<div class="row g-2 mb-3">
  <div class="col-6 col-md-4 col-xl-2">
    <div class="card border-0 shadow-2xs rounded-3 p-2 bg-white border h-100">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-muted small fw-semibold" style="font-size: 11px;">Total Items</div>
          <div class="fw-bold text-dark fs-6 font-monospace"><?php echo (int)($summary_stats['total_items'] ?? 0); ?> Events</div>
        </div>
        <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
          <i class="bi bi-collection-play-fill fs-6"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="col-6 col-md-4 col-xl-2">
    <div class="card border-0 shadow-2xs rounded-3 p-2 bg-white border h-100">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-muted small fw-semibold" style="font-size: 11px;">Single vs Group</div>
          <div class="fw-bold text-dark fs-6 font-monospace">
            <?php echo (int)($summary_stats['single_items'] ?? 0); ?>S / <?php echo (int)($summary_stats['group_items'] ?? 0); ?>G
          </div>
        </div>
        <div class="rounded-circle bg-info-subtle text-info p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
          <i class="bi bi-people fs-6"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="col-6 col-md-4 col-xl-2">
    <div class="card border-0 shadow-2xs rounded-3 p-2 bg-white border h-100">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-muted small fw-semibold" style="font-size: 11px;">Off-Stage / On-Stage</div>
          <div class="fw-bold text-dark fs-6 font-monospace">
            <?php echo (int)($summary_stats['off_stage_items'] ?? 0); ?> Off / <?php echo ((int)($summary_stats['total_items'] ?? 0) - (int)($summary_stats['off_stage_items'] ?? 0)); ?> On
          </div>
        </div>
        <div class="rounded-circle bg-warning-subtle text-warning-emphasis p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
          <i class="bi bi-pencil-square fs-6"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="col-6 col-md-4 col-xl-2">
    <div class="card border-0 shadow-2xs rounded-3 p-2 bg-white border h-100">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-muted small fw-semibold" style="font-size: 11px;">Active Stages & Halls</div>
          <div class="fw-bold text-dark fs-6 font-monospace"><?php echo count($stages ?? []); ?> Venues</div>
        </div>
        <div class="rounded-circle bg-success-subtle text-success p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
          <i class="bi bi-building fs-6"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="col-6 col-md-4 col-xl-2">
    <div class="card border-0 shadow-2xs rounded-3 p-2 bg-white border h-100">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-muted small fw-semibold" style="font-size: 11px;">Clash Protection</div>
          <div class="fw-bold text-success fs-6 font-monospace">Interval Overlap</div>
        </div>
        <div class="rounded-circle bg-success-subtle text-success p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
          <i class="bi bi-shield-check fs-6"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="col-6 col-md-4 col-xl-2">
    <div class="card border-0 shadow-2xs rounded-3 p-2 bg-white border h-100">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-muted small fw-semibold" style="font-size: 11px;">Max Limit Rule</div>
          <div class="fw-bold text-primary fs-6 font-monospace"><?php echo (int)($district_limits['max_total'] ?? 5); ?> Items Max</div>
        </div>
        <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
          <i class="bi bi-ui-checks-grid fs-6"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ================= MODAL 1: GEMINI AI API KEY CONFIG ================= -->
<div class="modal fade" id="geminiKeyModal" tabindex="-1" aria-labelledby="geminiKeyModalLabel" aria-hidden="true" style="z-index: 10050;">
  <div class="modal-dialog modal-dialog-centered" style="margin-top: 60px !important;">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header bg-dark text-white py-2.5 px-3 border-0">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-stars text-warning fs-5"></i>
          <h6 class="modal-title fw-bold text-white mb-0" id="geminiKeyModalLabel">Gemini AI API Configuration</h6>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3">
        <p class="text-muted small mb-2">
          Connect your Google Gemini API key to empower automatic stage load balancing, clash resolution, and prompt-driven timetable generation.
        </p>

        <div class="mb-3">
          <label for="geminiApiKeyInput" class="form-label fw-bold text-dark small">Gemini API Key:</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
            <input type="password" class="form-control border-start-0 border-end-0 font-monospace" id="geminiApiKeyInput" placeholder="AIzaSy..." value="<?php echo esc($gemini_api_key ?? ''); ?>">
            <button class="btn btn-outline-secondary border-start-0" type="button" onclick="toggleKeyVisibility()" title="Show/Hide Key">
              <i class="bi bi-eye" id="toggleKeyIcon"></i>
            </button>
          </div>
          <div class="form-text small text-muted" style="font-size: 11px;">
            Stored securely in database general_settings. Obtain a free key from <a href="https://aistudio.google.com/" target="_blank" class="text-decoration-none">Google AI Studio</a>.
          </div>
        </div>

        <div id="geminiKeyAlert" class="alert d-none py-1.5 px-2.5 rounded-3 small"></div>

        <div class="d-flex align-items-center justify-content-between pt-1">
          <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="testGeminiKeyAjax()" id="btnTestKey">
            <i class="bi bi-wifi me-1"></i> Test Connection
          </button>
          <button type="button" class="btn btn-primary rounded-pill px-3 btn-sm fw-semibold shadow-sm" onclick="saveGeminiKeyAjax()" id="btnSaveKey">
            <i class="bi bi-check-lg me-1"></i> Save API Key
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ================= MODAL 2: DURATION BREAKDOWN ENGINE ================= -->
<div class="modal fade" id="durationModal" tabindex="-1" aria-hidden="true" style="z-index: 10050;">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header bg-primary text-white py-2.5 px-3 border-0 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-calculator fs-5"></i>
          <div>
            <h6 class="modal-title fw-bold text-white mb-0">Accurate Item Duration Breakdown Engine</h6>
            <span class="small opacity-75" style="font-size: 11px;">ஆப்ஸ்டேஜ்: 1 முறை Fixed Session நேரம் | ஆன்ஸ்டேஜ்: பங்கேற்பாளர் × தனிநபர் மேடை நேரம்</span>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button type="button" class="btn btn-light btn-sm rounded-pill py-0.5 px-2.5 fw-semibold text-primary" onclick="loadDurationBreakdown()" title="Recalculate durations">
            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
          </button>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>

      <div class="modal-body p-3">
        <!-- Duration Summary Pills (4-Card Metric Grid) -->
        <div class="row g-2 mb-2 text-center">
          <div class="col-6 col-md-3">
            <div class="p-2 rounded-3 bg-primary-subtle border border-primary-subtle h-100">
              <span class="text-muted small fw-semibold d-block">Grand Festival Duration</span>
              <span class="fw-bold text-primary fs-5 font-monospace" id="durTotal">--</span>
              <span class="text-muted d-block font-monospace" style="font-size: 11px;" id="durTotalItems">-- Items</span>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="p-2 rounded-3 bg-success-subtle border border-success-subtle h-100">
              <span class="text-muted small fw-semibold d-block">On-Stage (மேடை நேரம்)</span>
              <span class="fw-bold text-success fs-5 font-monospace" id="durOnStage">--</span>
              <span class="text-success d-block font-monospace" style="font-size: 11px;" id="durOnStageItems">பங்கேற்பாளர் × நேரம்</span>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="p-2 rounded-3 bg-warning-subtle border border-warning-subtle h-100">
              <span class="text-muted small fw-semibold d-block">Off-Stage (ஆப்ஸ்டேஜ்)</span>
              <span class="fw-bold text-warning-emphasis fs-5 font-monospace" id="durOffStage">--</span>
              <span class="text-secondary d-block font-monospace" style="font-size: 11px;" id="durOffStageItems">1 முறை மட்டுமே (Fixed)</span>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="p-2 rounded-3 bg-info-subtle border border-info-subtle h-100">
              <span class="text-muted small fw-semibold d-block">தேவைப்படும் நாட்கள் (Est. Days)</span>
              <span class="fw-bold text-info-emphasis fs-5 font-monospace" id="durDays">--</span>
              <span class="text-muted d-block font-monospace" style="font-size: 11px;" id="durDailyHours">8 மணி/நாள் கணக்கீடு</span>
            </div>
          </div>
        </div>

        <!-- Rule Clarification Notice -->
        <div class="alert alert-light border rounded-3 py-2 px-3 mb-3 small d-flex align-items-center gap-2" style="background: #f8fafc;">
          <i class="bi bi-info-circle-fill text-primary fs-5"></i>
          <div>
            <div class="fw-bold text-dark">நேரக் கணக்கீட்டு விதிமுறை (Duration Calculation Rules):</div>
            <div class="text-muted" style="font-size: 11.5px;">
              • <strong>ஆப்ஸ்டேஜ் (Off-Stage):</strong> எத்தனை மாணவர்கள் பங்குபெற்றாலும் அனைவரும் ஒரே அறையில் அமர்வர்; எனவே 1 முறை மட்டுமே நேரம் (Fixed Room Session).<br>
              • <strong>ஆன்ஸ்டேஜ் (On-Stage):</strong> ஒவ்வொரு மாணவரும் மேடையில் தனித்தனியாக ஏறி நிகழ்வை நிகழ்த்துவர் (பங்கேற்பாளர்கள் எண்ணிக்கை × தனிநபர் மேடை நேரம்).<br>
              • <strong>நாள்கள் கணக்கீடு (Festival Days):</strong> நாளொன்றுக்கு 8 மணிநேரம் (480 நிமிடங்கள்) மேடை இயங்கும் அடிப்படையில் தேவைப்படும் நாட்கள் கூட்டப்பட்டுள்ளது.
            </div>
          </div>
        </div>

        <div class="table-responsive" style="max-height: 480px;">
          <table class="table table-sm table-hover align-middle mb-0 font-monospace" style="font-size: 12px;">
            <thead class="table-light sticky-top">
              <tr>
                <th>Code</th>
                <th>Item Name</th>
                <th>Category</th>
                <th>Type</th>
                <th>Stage Mode</th>
                <th class="text-center">Entries</th>
                <th class="text-center">Unit Time</th>
                <th>Duration Rule Applied</th>
                <th class="text-end">Total Duration</th>
              </tr>
            </thead>
            <tbody id="durTableBody">
              <tr>
                <td colspan="9" class="text-center py-4 text-muted">
                  <div class="spinner-border spinner-border-sm text-primary me-2"></div> Calculating item durations...
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer bg-light py-2 px-3 border-0 d-flex justify-content-between">
        <small class="text-muted">Formula: Single On-Stage = Entries × Base Time. Off-Stage Writing = Fixed Base Time (All participants perform simultaneously in rooms).</small>
        <div class="d-flex align-items-center gap-2">
          <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="loadDurationBreakdown()">
            <i class="bi bi-arrow-clockwise me-1"></i> Recalculate
          </button>
          <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ================= MODAL 3: DYNAMIC STUDENT LIMITS PRE-CHECK ================= -->
<div class="modal fade" id="studentLimitsModal" tabindex="-1" aria-hidden="true" style="z-index: 10050;">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header bg-dark text-white py-2.5 px-3 border-0 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-person-bounding-box text-warning fs-5"></i>
          <div>
            <h6 class="modal-title fw-bold text-white mb-0">Dynamic Student Item Limit Audit</h6>
            <span class="small opacity-75" style="font-size: 11px;">Configured via District Item Settings: Single: 3, Group: 2 (Max: 5)</span>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button type="button" class="btn btn-light btn-sm rounded-pill py-0.5 px-2.5 fw-semibold text-dark" onclick="loadStudentLimitsAudit()" title="Re-check student limits">
            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
          </button>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>

      <div class="modal-body p-3">
        <!-- Audit Summary Badges -->
        <div class="row g-2 mb-3 text-center">
          <div class="col-3">
            <div class="p-2 rounded-3 bg-light border">
              <span class="text-muted small fw-semibold d-block">District Rule</span>
              <span class="fw-bold text-dark font-monospace" id="auditRuleLabel">--</span>
            </div>
          </div>
          <div class="col-3">
            <div class="p-2 rounded-3 bg-primary-subtle border border-primary-subtle">
              <span class="text-muted small fw-semibold d-block">Total Students</span>
              <span class="fw-bold text-primary fs-5 font-monospace" id="auditTotalStudents">--</span>
            </div>
          </div>
          <div class="col-3">
            <div class="p-2 rounded-3 bg-success-subtle border border-success-subtle">
              <span class="text-muted small fw-semibold d-block">Compliant (🟢)</span>
              <span class="fw-bold text-success fs-5 font-monospace" id="auditCompliantCount">--</span>
            </div>
          </div>
          <div class="col-3">
            <div class="p-2 rounded-3 bg-danger-subtle border border-danger-subtle">
              <span class="text-muted small fw-semibold d-block">Limit Violations (🔴)</span>
              <span class="fw-bold text-danger fs-5 font-monospace" id="auditViolationCount">--</span>
            </div>
          </div>
        </div>

        <div class="table-responsive" style="max-height: 480px;">
          <table class="table table-sm table-hover align-middle mb-0 font-monospace" style="font-size: 12px;">
            <thead class="table-light sticky-top">
              <tr>
                <th>#</th>
                <th>Admission No</th>
                <th>Student Name</th>
                <th>School</th>
                <th class="text-center">Single</th>
                <th class="text-center">Group</th>
                <th class="text-center">Total</th>
                <th>Enrolled Items</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>
            <tbody id="studentAuditTableBody">
              <tr>
                <td colspan="9" class="text-center py-4 text-muted">
                  <div class="spinner-border spinner-border-sm text-primary me-2"></div> Checking student limits...
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer bg-light py-2 px-3 border-0 d-flex justify-content-between">
        <small class="text-muted">Limits are dynamically enforced from <code>district_item_settings</code> for your revenue district.</small>
        <div class="d-flex align-items-center gap-2">
          <button type="button" class="btn btn-outline-dark btn-sm rounded-pill px-3" onclick="loadStudentLimitsAudit()">
            <i class="bi bi-arrow-clockwise me-1"></i> Refresh Audit
          </button>
          <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ================= MODAL 4: VISUAL TIMELINE / GANTT VIEWER ================= -->
<div class="modal fade" id="timelineModal" tabindex="-1" aria-hidden="true" style="z-index: 10050;">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header bg-info text-dark py-2.5 px-3 border-0">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-calendar-week-fill fs-5 text-dark"></i>
          <div>
            <h6 class="modal-title fw-bold text-dark mb-0">Multi-Stage Visual Schedule Timeline</h6>
            <span class="small text-dark opacity-75" style="font-size: 11px;">Stage-by-stage timetable distribution with start & end intervals</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-3">
        <div id="timelineContent">
          <div class="text-center py-5 text-muted">
            <div class="spinner-border spinner-border-sm text-info me-2"></div> Loading stage timeline...
          </div>
        </div>
      </div>

      <div class="modal-footer bg-light py-2 px-3 border-0 d-flex justify-content-between">
        <small class="text-muted">Timeline view reflects the currently generated active schedule in <code>sd_stage_item_master</code>.</small>
        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- ================= MODAL 5: SCHEDULE VERSION HISTORY ================= -->
<div class="modal fade" id="versionsModal" tabindex="-1" aria-hidden="true" style="z-index: 10050;">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header bg-secondary text-white py-2.5 px-3 border-0">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-clock-history fs-5"></i>
          <div>
            <h6 class="modal-title fw-bold text-white mb-0">Schedule Version History & 1-Click Restore</h6>
            <span class="small opacity-75" style="font-size: 11px;">Rollback or inspect previous schedule snapshots</span>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-3">
        <div id="versionAlert" class="alert d-none py-1.5 px-2.5 rounded-3 small"></div>

        <div class="table-responsive" style="max-height: 400px;">
          <table class="table table-sm table-hover align-middle mb-0 font-monospace" style="font-size: 12px;">
            <thead class="table-light sticky-top">
              <tr>
                <th>#</th>
                <th>Version Name</th>
                <th>Total Items</th>
                <th>Status</th>
                <th>Created At</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody id="versionTableBody">
              <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                  <div class="spinner-border spinner-border-sm text-secondary me-2"></div> Loading schedule versions...
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer bg-light py-2 px-3 border-0 d-flex justify-content-between">
        <small class="text-muted">Snapshots are created automatically every time Auto Schedule is generated.</small>
        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- ================= JAVASCRIPT CONTROLLERS ================= -->
<script>
// Dynamic dates passed from server (No hardcoded 2024 dates!)
var SERVER_START_DATE = '<?php echo esc($default_start_date ?? "2025-09-02"); ?>';
var SERVER_END_DATE = '<?php echo esc($default_end_date ?? "2025-09-05"); ?>';

// Auto-Set Stage Schedule
function generateSchedule(e) {
    var festSelect = document.getElementById('fest_id');
    var festId = festSelect ? festSelect.value : 'all';

    var startDateInput = document.getElementById('start_date');
    var endDateInput = document.getElementById('end_date');
    var startDate = (startDateInput && startDateInput.value) ? startDateInput.value : SERVER_START_DATE;
    var endDate = (endDateInput && endDateInput.value) ? endDateInput.value : SERVER_END_DATE;

    var startTime = (document.getElementById('start_time') ? document.getElementById('start_time').value : '') || '09:00:00';
    var endTime = (document.getElementById('end_time') ? document.getElementById('end_time').value : '') || '17:00:00';
    var slotDuration = (document.getElementById('slot_duration') ? document.getElementById('slot_duration').value : '') || '30';
    var useAi = (document.getElementById('use_ai_switch') && document.getElementById('use_ai_switch').checked) ? 'Y' : 'N';
    var stageType = (document.getElementById('stage_type_filter') ? document.getElementById('stage_type_filter').value : '') || 'all';

    var btn = document.getElementById('btnGenerateSchedule');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Generating...';
    }

    if (typeof window.showGlobalLoader === 'function') {
        window.showGlobalLoader('Calculating item durations and optimizing stage timetable...', true);
    }

    var params = {
        'fest_id': festId,
        'stage_type_filter': stageType,
        'start_date': startDate,
        'end_date': endDate,
        'start_time': startTime,
        'end_time': endTime,
        'slot_duration': slotDuration,
        'use_ai': useAi
    };

    submitSchedulingPost('<?php echo site_url("stage/festival_scheduling/generate_schedule"); ?>', params);
}

// Global helper to safely submit POST forms with CSRF token
function submitSchedulingPost(actionUrl, params) {
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = actionUrl;

    var tokenName = '<?php echo csrf_token(); ?>';
    var tokenHash = '<?php echo csrf_hash(); ?>';
    var metaToken = document.querySelector('meta[name="csrf-token"]');
    if (metaToken && metaToken.getAttribute('content')) {
        tokenHash = metaToken.getAttribute('content');
    }

    var csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = tokenName;
    csrfInput.value = tokenHash;
    form.appendChild(csrfInput);

    for (var key in params) {
        if (params.hasOwnProperty(key)) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = params[key];
            form.appendChild(input);
        }
    }

    document.body.appendChild(form);
    form.submit();
}

function viewSchedule() {
    var festId = document.getElementById('fest_id').value || 'all';
    submitSchedulingPost('<?php echo site_url("stage/festival_scheduling/view_schedule"); ?>', { 'fest_id': festId });
}

function analyzeClashes() {
    var festId = document.getElementById('fest_id').value || 'all';
    submitSchedulingPost('<?php echo site_url("stage/festival_scheduling/analyze_clashes"); ?>', { 'fest_id': festId });
}

// Modal 2: Open Duration Breakdown Modal
function openDurationModal() {
    var modalEl = document.getElementById('durationModal');
    if (modalEl) {
        var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }
    loadDurationBreakdown();
}

function loadDurationBreakdown() {
    var festId = document.getElementById('fest_id').value || 'all';
    var tbody = document.getElementById('durTableBody');
    if (!tbody) return;
    tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Calculating item durations...</td></tr>';

    fetch('<?php echo site_url("stage/festival_scheduling/ajax_duration_breakdown"); ?>?fest_id=' + encodeURIComponent(festId), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) {
        if (!res.ok) throw new Error('HTTP status ' + res.status + ' (' + res.statusText + ')');
        return res.json();
    })
    .then(function(res) {
        if (res.status === 'success' && res.data) {
            var d = res.data;
            if (document.getElementById('durTotal')) {
                document.getElementById('durTotal').innerText = d.grand_total_formatted + ' (' + d.grand_total_minutes + 'm)';
            }
            if (document.getElementById('durTotalItems')) {
                document.getElementById('durTotalItems').innerText = d.total_items + ' Programs';
            }
            if (document.getElementById('durOnStage')) {
                document.getElementById('durOnStage').innerText = d.on_stage_formatted + ' (' + d.on_stage_minutes + 'm)';
            }
            if (document.getElementById('durOnStageItems')) {
                document.getElementById('durOnStageItems').innerText = d.on_stage_count + ' மேடை நிகழ்வுகள் (Entries × நேரம்)';
            }
            if (document.getElementById('durOffStage')) {
                document.getElementById('durOffStage').innerText = d.off_stage_formatted + ' (' + d.off_stage_minutes + 'm)';
            }
            if (document.getElementById('durOffStageItems')) {
                document.getElementById('durOffStageItems').innerText = d.off_stage_count + ' ஆப்ஸ்டேஜ் நிகழ்வுகள் (1 முறை மட்டும்)';
            }
            if (document.getElementById('durDays')) {
                document.getElementById('durDays').innerText = d.est_days_needed + ' நாள்(கள்)';
            }
            if (document.getElementById('durDailyHours')) {
                document.getElementById('durDailyHours').innerText = (d.daily_operating_hours || 8) + ' மணி/நாள் கணக்கீடு';
            }

            var html = '';
            d.items.forEach(function(item) {
                var badgeClass = item.item_type === 'Group' ? 'bg-info text-dark' : 'bg-light text-dark border';
                var offBadge = item.is_off_stage === 'Y' ? '<span class="badge bg-warning text-dark">Off-Stage (Hall)</span>' : '<span class="badge bg-primary">On-Stage</span>';

                html += '<tr>' +
                    '<td class="fw-bold">[' + item.item_code + ']</td>' +
                    '<td class="fw-semibold">' + item.item_name + '</td>' +
                    '<td><span class="badge bg-light text-secondary border">' + item.fest_name + '</span></td>' +
                    '<td><span class="badge ' + badgeClass + '">' + item.item_type + '</span></td>' +
                    '<td>' + offBadge + '</td>' +
                    '<td class="text-center fw-bold">' + item.entries + '</td>' +
                    '<td class="text-center">' + item.unit_time + 'm</td>' +
                    '<td><span class="text-muted small">' + item.calc_mode + '</span></td>' +
                    '<td class="text-end fw-bold text-primary">' + item.formatted_duration + '</td>' +
                '</tr>';
            });
            tbody.innerHTML = html || '<tr><td colspan="9" class="text-center py-3 text-muted">No items found for this category.</td></tr>';
        } else {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-3"><i class="bi bi-exclamation-triangle me-1"></i> ' + (res.message || 'Failed to load duration calculations.') + '</td></tr>';
        }
    })
    .catch(function(err) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-3"><i class="bi bi-exclamation-triangle me-1"></i> Error fetching duration breakdown: ' + (err.message || err) + '</td></tr>';
    });
}

// Modal 3: Open Student Limits Pre-Check Modal
function openStudentLimitsModal() {
    var modalEl = document.getElementById('studentLimitsModal');
    if (modalEl) {
        var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }
    loadStudentLimitsAudit();
}

function loadStudentLimitsAudit() {
    var festId = document.getElementById('fest_id').value || 'all';
    var tbody = document.getElementById('studentAuditTableBody');
    if (!tbody) return;
    tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Checking student limits...</td></tr>';

    fetch('<?php echo site_url("stage/festival_scheduling/ajax_participant_limits"); ?>?fest_id=' + encodeURIComponent(festId), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) {
        if (!res.ok) throw new Error('HTTP status ' + res.status + ' (' + res.statusText + ')');
        return res.json();
    })
    .then(function(res) {
        if (res.status === 'success' && res.data) {
            var d = res.data;
            if (d.limits && d.limits.rule_label && document.getElementById('auditRuleLabel')) {
                document.getElementById('auditRuleLabel').innerText = d.limits.rule_label;
            }
            if (document.getElementById('auditTotalStudents')) {
                document.getElementById('auditTotalStudents').innerText = d.total_students;
            }
            if (document.getElementById('auditCompliantCount')) {
                document.getElementById('auditCompliantCount').innerText = d.compliant_count;
            }
            if (document.getElementById('auditViolationCount')) {
                document.getElementById('auditViolationCount').innerText = d.violation_count;
            }

            var html = '';
            var count = 1;
            if (d.all_students && d.all_students.length > 0) {
                d.all_students.forEach(function(st) {
                    var statusBadge = st.is_violating
                        ? '<span class="badge bg-danger">🔴 Exceeded (' + st.violation_reason + ')</span>'
                        : '<span class="badge bg-success-subtle text-success border border-success-subtle">🟢 Compliant</span>';

                    html += '<tr>' +
                        '<td>' + (count++) + '</td>' +
                        '<td class="fw-bold">' + (st.admn_no || 'N/A') + '</td>' +
                        '<td class="fw-semibold">' + (st.participant_name || 'Student') + '</td>' +
                        '<td class="small text-muted">' + (st.school_name || ('Code: ' + st.school_code)) + '</td>' +
                        '<td class="text-center fw-bold">' + st.single_count + '</td>' +
                        '<td class="text-center fw-bold">' + st.group_count + '</td>' +
                        '<td class="text-center fw-bold text-primary">' + st.total_count + '</td>' +
                        '<td class="small text-truncate" style="max-width: 250px;" title="' + (st.items_list || '') + '">' + (st.items_list || 'None') + '</td>' +
                        '<td class="text-center">' + statusBadge + '</td>' +
                    '</tr>';
                });
                tbody.innerHTML = html;
            } else {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted"><i class="bi bi-info-circle me-1"></i> இந்த பிரிவில் இதுவரை எந்த மாணவரும் பதிவுசெய்யப்படவில்லை.<br><span class="small opacity-75">("All Festivals" பிரிவைத் தேர்வுசெய்து சரிபார்க்கவும்).</span></td></tr>';
            }
        } else {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-3"><i class="bi bi-exclamation-triangle me-1"></i> ' + (res.message || 'Failed to load student limit audits.') + '</td></tr>';
        }
    })
    .catch(function(err) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-3"><i class="bi bi-exclamation-triangle me-1"></i> Error auditing student limits: ' + (err.message || err) + '</td></tr>';
    });
}

// Modal 4: Open Timeline Modal
function openTimelineModal() {
    var modalEl = document.getElementById('timelineModal');
    var modal = new bootstrap.Modal(modalEl);
    modal.show();

    var festId = document.getElementById('fest_id').value || 'all';
    var container = document.getElementById('timelineContent');
    container.innerHTML = '<div class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm text-info me-2"></div> Loading stage timeline...</div>';

    fetch('<?php echo site_url("stage/festival_scheduling/ajax_timeline_data"); ?>?fest_id=' + encodeURIComponent(festId), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) {
        if (!res.ok) throw new Error('HTTP status ' + res.status + ' (' + res.statusText + ')');
        return res.json();
    })
    .then(function(res) {
        if (res.status === 'success' && res.timeline && res.timeline.length > 0) {
            // Group timeline by date
            var byDate = {};
            res.timeline.forEach(function(item) {
                if (!byDate[item.date]) byDate[item.date] = [];
                byDate[item.date].push(item);
            });

            var html = '';
            for (var d in byDate) {
                html += '<div class="card border rounded-3 mb-3 overflow-hidden shadow-2xs">' +
                    '<div class="card-header bg-light py-2 px-3 fw-bold text-dark d-flex align-items-center justify-content-between">' +
                        '<span><i class="bi bi-calendar3 me-1.5 text-primary"></i> Date: ' + d + '</span>' +
                        '<span class="badge bg-secondary-subtle text-secondary font-monospace">' + byDate[d].length + ' Events Scheduled</span>' +
                    '</div>' +
                    '<div class="card-body p-2">' +
                        '<div class="row g-2">';

                byDate[d].forEach(function(ev) {
                    var isOff = ev.is_off_stage === 'Y';
                    var stageColor = isOff ? 'border-warning-subtle bg-warning-subtle' : 'border-primary-subtle bg-primary-subtle';
                    var icon = isOff ? 'bi-pencil' : 'bi-mic';

                    html += '<div class="col-12 col-md-6 col-xl-4">' +
                        '<div class="p-2 rounded-3 border ' + stageColor + ' h-100">' +
                            '<div class="d-flex align-items-center justify-content-between mb-1">' +
                                '<span class="badge bg-dark font-monospace">' + ev.stage_name + '</span>' +
                                '<span class="fw-bold font-monospace small text-primary">' + ev.start_time + ' - ' + ev.end_time + ' (' + ev.duration_min + 'm)</span>' +
                            '</div>' +
                            '<div class="fw-bold text-dark small text-truncate">[' + ev.item_code + '] ' + ev.item_name + '</div>' +
                            '<div class="text-muted small d-flex align-items-center gap-1 font-monospace" style="font-size: 11px;">' +
                                '<i class="bi ' + icon + '"></i> ' + (ev.fest_name || 'Competition') + ' (' + (ev.item_type === 'G' ? 'Group' : 'Single') + ')' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                });

                html += '</div></div></div>';
            }
            container.innerHTML = html;
        } else {
            container.innerHTML = '<div class="text-center py-5 text-muted"><i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>No active schedule found. Click <strong>Auto Set Stage Schedule</strong> first to generate timetable slots.</div>';
        }
    })
    .catch(function(err) {
        container.innerHTML = '<div class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle me-1"></i> Error loading schedule timeline: ' + (err.message || err) + '</div>';
    });
}

// Modal 5: Open Version History Modal
function openVersionsModal() {
    var modalEl = document.getElementById('versionsModal');
    var modal = new bootstrap.Modal(modalEl);
    modal.show();

    loadVersionsList();
}

function loadVersionsList() {
    var festId = document.getElementById('fest_id').value || 'all';
    var tbody = document.getElementById('versionTableBody');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-secondary me-2"></div> Loading schedule versions...</td></tr>';

    fetch('<?php echo site_url("stage/festival_scheduling/ajax_get_versions"); ?>?fest_id=' + encodeURIComponent(festId), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) {
        if (!res.ok) throw new Error('HTTP status ' + res.status + ' (' + res.statusText + ')');
        return res.json();
    })
    .then(function(res) {
        if (res.status === 'success' && res.versions) {
            if (res.versions.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No schedule versions saved yet. Versions are created automatically when you run Auto Schedule.</td></tr>';
                return;
            }

            var html = '';
            var count = 1;
            res.versions.forEach(function(v) {
                var activeBadge = (v.is_active == 1)
                    ? '<span class="badge bg-success">Active Live</span>'
                    : '<span class="badge bg-light text-muted border">Archived</span>';

                var restoreBtn = (v.is_active == 1)
                    ? '<span class="text-muted small">Current Live</span>'
                    : '<button type="button" class="btn btn-outline-primary btn-sm rounded-pill py-0.5 px-2.5" onclick="restoreScheduleVersion(' + v.version_id + ')"><i class="bi bi-arrow-counterclockwise me-1"></i>Restore</button>';

                html += '<tr>' +
                    '<td>' + (count++) + '</td>' +
                    '<td class="fw-bold text-dark">' + v.version_name + '</td>' +
                    '<td><span class="badge bg-primary-subtle text-primary font-monospace">' + v.total_items + ' Items</span></td>' +
                    '<td>' + activeBadge + '</td>' +
                    '<td class="small text-muted">' + v.created_at + '</td>' +
                    '<td class="text-end">' + restoreBtn + '</td>' +
                '</tr>';
            });
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-3"><i class="bi bi-exclamation-triangle me-1"></i> ' + (res.message || 'Failed to load schedule versions.') + '</td></tr>';
        }
    })
    .catch(function(err) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-3"><i class="bi bi-exclamation-triangle me-1"></i> Error fetching schedule versions: ' + (err.message || err) + '</td></tr>';
    });
}

function restoreScheduleVersion(versionId) {
    if (!confirm('Are you sure you want to restore this schedule snapshot? It will replace the current live timetable.')) {
        return;
    }

    var alertBox = document.getElementById('versionAlert');
    var tokenName = '<?php echo csrf_token(); ?>';
    var tokenHash = '<?php echo csrf_hash(); ?>';
    var metaToken = document.querySelector('meta[name="csrf-token"]');
    if (metaToken && metaToken.getAttribute('content')) {
        tokenHash = metaToken.getAttribute('content');
    }

    var formData = new FormData();
    formData.append(tokenName, tokenHash);
    formData.append('version_id', versionId);

    fetch('<?php echo site_url("stage/festival_scheduling/ajax_restore_version"); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': tokenHash,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        alertBox.classList.remove('d-none');
        if (data.status === 'success') {
            alertBox.className = 'alert alert-success py-1.5 px-2.5 rounded-3 small';
            alertBox.innerText = data.message;
            loadVersionsList();
            setTimeout(function() { window.location.reload(); }, 1200);
        } else {
            alertBox.className = 'alert alert-danger py-1.5 px-2.5 rounded-3 small';
            alertBox.innerText = data.message;
        }
    })
    .catch(function() {
        alertBox.className = 'alert alert-danger py-1.5 px-2.5 rounded-3 small';
        alertBox.innerText = 'Network error restoring version.';
        alertBox.classList.remove('d-none');
    });
}

// Clear Schedule with Double Safety Confirmation
function clearSchedule() {
    var festSelect = document.getElementById('fest_id');
    var festId = festSelect ? festSelect.value : 'all';
    var festName = (festSelect && festSelect.selectedIndex >= 0) ? festSelect.options[festSelect.selectedIndex].text : 'All Festivals';
    var targetUrl = '<?php echo site_url("stage/festival_scheduling/clear_schedule?fest_id="); ?>' + encodeURIComponent(festId);

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Clear Schedule?',
            html: 'Are you sure you want to clear the stage schedule for <strong>' + festName + '</strong>?<br><br>' +
                  '<div class="alert alert-warning text-start small mb-0 rounded-3">' +
                  '<i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> ' +
                  '<strong>Warning:</strong> This will delete all generated timetable slots and stage allotments for this category.' +
                  '</div>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-arrow-right-circle me-1"></i> Yes, Proceed to Clear',
            cancelButtonText: 'No, Keep Schedule',
            reverseButtons: true,
            focusCancel: true
        }).then((result1) => {
            if (result1.isConfirmed) {
                Swal.fire({
                    title: 'Final Safety Verification!',
                    html: 'This action <strong>CANNOT BE UNDONE</strong>!<br><br>' +
                          'Do you strictly confirm that you want to <strong>permanently wipe out</strong> the entire stage schedule for <strong>' + festName + '</strong>?',
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#b02a37',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> Yes, Permanently Clear Schedule',
                    cancelButtonText: 'Cancel & Abort',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result2) => {
                    if (result2.isConfirmed) {
                        Swal.fire({
                            title: 'Clearing Schedule...',
                            html: 'Removing all stage and timetable records. Please wait...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                        window.location.href = targetUrl;
                    }
                });
            }
        });
    } else {
        if (confirm('Are you sure you want to clear the schedule for ' + festName + '?')) {
            if (confirm('FINAL WARNING: Permanently wipe out the schedule for ' + festName + '?')) {
                window.location.href = targetUrl;
            }
        }
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
        alertBox.className = 'alert alert-danger py-1.5 px-2.5 rounded-3 small';
        alertBox.innerText = 'Please enter a valid API key.';
        alertBox.classList.remove('d-none');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving...';

    var tokenName = '<?php echo csrf_token(); ?>';
    var tokenHash = '<?php echo csrf_hash(); ?>';
    var metaToken = document.querySelector('meta[name="csrf-token"]');
    if (metaToken && metaToken.getAttribute('content')) {
        tokenHash = metaToken.getAttribute('content');
    }

    var formData = new FormData();
    formData.append(tokenName, tokenHash);
    formData.append('gemini_api_key', key);

    fetch('<?php echo site_url("stage/festival_scheduling/save_gemini_key"); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': tokenHash,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save API Key';
        alertBox.classList.remove('d-none');
        if (data.status === 'success') {
            alertBox.className = 'alert alert-success py-1.5 px-2.5 rounded-3 small';
            alertBox.innerText = data.message;
            setTimeout(function() { window.location.reload(); }, 1200);
        } else {
            alertBox.className = 'alert alert-danger py-1.5 px-2.5 rounded-3 small';
            alertBox.innerText = data.message;
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save API Key';
        alertBox.className = 'alert alert-danger py-1.5 px-2.5 rounded-3 small';
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

    var tokenName = '<?php echo csrf_token(); ?>';
    var tokenHash = '<?php echo csrf_hash(); ?>';
    var metaToken = document.querySelector('meta[name="csrf-token"]');
    if (metaToken && metaToken.getAttribute('content')) {
        tokenHash = metaToken.getAttribute('content');
    }

    var formData = new FormData();
    formData.append(tokenName, tokenHash);
    formData.append('gemini_api_key', key);

    fetch('<?php echo site_url("stage/festival_scheduling/test_gemini_key"); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': tokenHash,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-wifi me-1"></i> Test Connection';
        alertBox.classList.remove('d-none');
        if (data.status === 'success') {
            alertBox.className = 'alert alert-success py-1.5 px-2.5 rounded-3 small';
            alertBox.innerText = data.message;
        } else {
            alertBox.className = 'alert alert-warning py-1.5 px-2.5 rounded-3 small';
            alertBox.innerText = data.message;
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-wifi me-1"></i> Test Connection';
        alertBox.className = 'alert alert-danger py-1.5 px-2.5 rounded-3 small';
        alertBox.innerText = 'Network error during connection test.';
        alertBox.classList.remove('d-none');
    });
}

// Auto attach modal refresh listeners
document.addEventListener('DOMContentLoaded', function() {
    var durModalEl = document.getElementById('durationModal');
    if (durModalEl) {
        durModalEl.addEventListener('show.bs.modal', function() {
            loadDurationBreakdown();
        });
    }
    var limitsModalEl = document.getElementById('studentLimitsModal');
    if (limitsModalEl) {
        limitsModalEl.addEventListener('show.bs.modal', function() {
            loadStudentLimitsAudit();
        });
    }
});
</script>
