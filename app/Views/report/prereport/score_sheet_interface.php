<link rel="prefetch" href="<?php echo base_url(); ?>report/prereport/callsheet_first">
<link rel="prefetch" href="<?php echo base_url(); ?>report/prereport/timesheetinterface">
<link rel="prefetch" href="<?php echo base_url(); ?>report/prereport/tabulation_report_interface">
<link rel="prefetch" href="<?php echo base_url(); ?>report/prereport/four_reports">

<style>
.stage-report-tabs-wrapper {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  padding-bottom: 3px;
}
.stage-report-tabs {
  gap: 0.5rem;
  flex-wrap: nowrap;
}
@media (min-width: 992px) {
  .stage-report-tabs {
    flex-wrap: wrap;
  }
}
.stage-report-tabs .nav-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.65rem 1.25rem;
  border-radius: 50rem;
  font-weight: 600;
  white-space: nowrap;
  letter-spacing: 0.2px;
  transition: all 0.2s ease-in-out;
}
.stage-report-tabs .nav-link i {
  margin-right: 0.55rem !important;
  font-size: 1.1rem;
  line-height: 1;
}
.stage-report-tabs .nav-link:not(.active) {
  background-color: #f8f9fa;
  color: #495057;
  border: 1px solid #e9ecef;
}
.stage-report-tabs .nav-link:not(.active):hover {
  background-color: #e9ecef;
  color: #0d6efd;
}
.stage-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stage-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.08) !important;
}
</style>

<div class="pagetitle mb-3">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="d-flex align-items-center gap-2 text-dark fw-bold mb-1">
        <i class="bi bi-easel2-fill text-primary"></i>
        <span>Stage Reports</span>
      </h1>
      <p class="text-muted small mb-0">Stage-wise reports — call sheet, time sheet, score sheet and tabulation as PDF.</p>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 bg-light px-3 py-1.5 rounded-pill border">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none"><i class="bi bi-house-door-fill me-1"></i>Home</a></li>
        <li class="breadcrumb-item text-muted">Reports</li>
        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Stage Reports</li>
      </ol>
    </nav>
  </div>
</div>

<!-- Navigation Pill Tabs -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-2">
    <div class="stage-report-tabs-wrapper">
      <div class="nav nav-pills nav-fill stage-report-tabs" id="reportTabs" role="tablist">
        <a class="nav-link rounded-pill text-secondary fw-semibold py-2 px-3" href="<?php echo base_url(); ?>report/prereport/callsheet_first" data-prefetch>
          <i class="bi bi-person-lines-fill me-2"></i>
          <span>Call Sheet</span>
        </a>
        <a class="nav-link rounded-pill text-secondary fw-semibold py-2 px-3" href="<?php echo base_url(); ?>report/prereport/timesheetinterface" data-prefetch>
          <i class="bi bi-clock-history me-2"></i>
          <span>Time Sheet</span>
        </a>
        <a class="nav-link active rounded-pill fw-semibold py-2 px-3" href="<?php echo base_url(); ?>report/prereport/score_sheet_interfaces" data-prefetch>
          <i class="bi bi-clipboard-check-fill me-2"></i>
          <span>Score Sheet</span>
        </a>
        <a class="nav-link rounded-pill text-secondary fw-semibold py-2 px-3" href="<?php echo base_url(); ?>report/prereport/tabulation_report_interface" data-prefetch>
          <i class="bi bi-table me-2"></i>
          <span>Tabulation</span>
        </a>
        <a class="nav-link rounded-pill text-secondary fw-semibold py-2 px-3" href="<?php echo base_url(); ?>report/prereport/four_reports" data-prefetch>
          <i class="bi bi-collection-fill me-2"></i>
          <span>All in One</span>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- 3-Column Report Generation Row: 1. Festival, 2. by Item, 3. by Date & Stage -->
<div class="row g-4 mb-4">

  <!-- Card 1: Score Sheet by Festival -->
  <div class="col-12 col-lg-4 d-flex">
    <?php echo form_open('report/prereportpdf/fest_scoresheet_details', array('id' => 'formPWDFest', 'target' => '_blank', 'class' => 'w-100 d-flex flex-column needs-spinner no-global-loader')); ?>
    <div class="card border-0 shadow-sm rounded-4 w-100 flex-grow-1 overflow-hidden d-flex flex-column stage-card">
      <div class="card-header bg-primary text-white py-3 px-4 d-flex align-items-center">
        <i class="bi bi-trophy-fill fs-5 me-2"></i>
        <h6 class="mb-0 fw-bold">Score Sheet by Festival</h6>
      </div>

      <div class="card-body p-4 flex-grow-1 d-flex flex-column">
        <div class="mb-3">
          <label for="txtfestFromScore" class="form-label fw-bold text-dark small d-flex align-items-center">
            <i class="bi bi-flag-fill text-warning me-1"></i>
            <span>Festival <span class="text-danger">*</span></span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-tags-fill"></i></span>
            <?php echo form_dropdown("txtfestFrom", $fest, '', 'class="form-select border-start-0 py-2" id="txtfestFromScore" required'); ?>
          </div>
        </div>
      </div>

      <div class="card-footer bg-white border-top p-3 mt-auto">
        <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill shadow-sm fw-semibold d-inline-flex align-items-center justify-content-center btn-spinner" onclick="return checkScoreFest(this)">
          <i class="bi bi-file-earmark-pdf-fill me-2"></i>
          <span class="btn-label">View PDF</span>
          <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
        </button>
      </div>
    </div>
    <?php echo form_close(); ?>
  </div>

  <!-- Card 2: Score Sheet by Item -->
  <div class="col-12 col-lg-4 d-flex">
    <?php echo form_open('report/prereportpdf/print_score_sheet', array('id' => 'formscore', 'target' => '_blank', 'class' => 'w-100 d-flex flex-column needs-spinner no-global-loader')); ?>
    <div class="card border-0 shadow-sm rounded-4 w-100 flex-grow-1 overflow-hidden d-flex flex-column stage-card">
      <div class="card-header bg-primary text-white py-3 px-4 d-flex align-items-center">
        <i class="bi bi-clipboard-check-fill fs-5 me-2"></i>
        <h6 class="mb-0 fw-bold">Score Sheet by Item</h6>
      </div>

      <div class="card-body p-4 flex-grow-1 d-flex flex-column">
        <div class="mb-3">
          <label for="cmbFestTypeScore" class="form-label fw-bold text-dark small d-flex align-items-center">
            <i class="bi bi-trophy text-warning me-1"></i>
            <span>Festival <span class="text-danger">*</span></span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-tags-fill"></i></span>
            <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-select border-start-0 py-2" id="cmbFestTypeScore" onchange="fetch_item_from_festival(this.value)" required'); ?>
          </div>
        </div>

        <div class="mb-3">
          <label for="cbo_item" class="form-label fw-bold text-dark small d-flex align-items-center">
            <i class="bi bi-card-list text-primary me-1"></i>
            <span>Item <span class="text-danger">*</span></span>
          </label>
          <div id="cmbitem">
            <?php echo form_dropdown("cbo_item", array('' => '-- Select Festival First --'), '', 'class="form-select py-2" id="cbo_item" required'); ?>
          </div>
        </div>
      </div>

      <div class="card-footer bg-white border-top p-3 mt-auto">
        <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill shadow-sm fw-semibold d-inline-flex align-items-center justify-content-center btn-spinner" onclick="return checkScoreItem(this)">
          <i class="bi bi-file-earmark-pdf-fill me-2"></i>
          <span class="btn-label">View PDF</span>
          <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
        </button>
      </div>
    </div>
    <?php echo form_close(); ?>
  </div>

  <!-- Card 3: Score Sheet by Date & Stage -->
  <div class="col-12 col-lg-4 d-flex">
    <?php echo form_open('report/prereportpdf/score_date_stage', array('id' => 'cluScore', 'target' => '_blank', 'class' => 'w-100 d-flex flex-column needs-spinner no-global-loader')); ?>
    <div class="card border-0 shadow-sm rounded-4 w-100 flex-grow-1 overflow-hidden d-flex flex-column stage-card">
      <div class="card-header bg-primary text-white py-3 px-4 d-flex align-items-center">
        <i class="bi bi-calendar2-range-fill fs-5 me-2"></i>
        <h6 class="mb-0 fw-bold">Score Sheet by Date &amp; Stage</h6>
      </div>

      <div class="card-body p-4 flex-grow-1 d-flex flex-column">
        <div class="mb-3">
          <label for="txtDateScore" class="form-label fw-bold text-dark small d-flex align-items-center">
            <i class="bi bi-calendar-event text-primary me-1"></i>
            <span>Date <span class="text-danger">*</span></span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-check"></i></span>
            <?php echo form_dropdown("txtDate", $date_array, '', 'class="form-select border-start-0 py-2" id="txtDateScore" required'); ?>
          </div>
        </div>

        <div class="mb-3">
          <label for="cmbstageScore" class="form-label fw-bold text-dark small d-flex align-items-center">
            <i class="bi bi-building text-primary me-1"></i>
            <span>Stage <span class="text-danger">*</span></span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-geo-alt-fill text-danger"></i></span>
            <?php echo form_dropdown("cmbstage", $stage, '', 'class="form-select border-start-0 py-2" id="cmbstageScore" required'); ?>
          </div>
        </div>
      </div>

      <div class="card-footer bg-white border-top p-3 mt-auto">
        <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill shadow-sm fw-semibold d-inline-flex align-items-center justify-content-center btn-spinner" onclick="return checkScoreDateStage(this)">
          <i class="bi bi-file-earmark-pdf-fill me-2"></i>
          <span class="btn-label">View PDF</span>
          <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
        </button>
      </div>
    </div>
    <?php echo form_close(); ?>
  </div>

</div>

<script>
// Prefetch sibling tabs on idle/hover
(function(){
  var urls = [
    '<?php echo base_url(); ?>report/prereport/callsheet_first',
    '<?php echo base_url(); ?>report/prereport/timesheetinterface',
    '<?php echo base_url(); ?>report/prereport/score_sheet_interfaces',
    '<?php echo base_url(); ?>report/prereport/tabulation_report_interface',
    '<?php echo base_url(); ?>report/prereport/four_reports'
  ];
  function pf(u){ try{ var l=document.createElement('link'); l.rel='prefetch'; l.href=u; document.head.appendChild(l);}catch(e){} }
  if('requestIdleCallback' in window) requestIdleCallback(function(){ urls.forEach(pf); }, {timeout:2000});
  else setTimeout(function(){ urls.forEach(pf); }, 800);
  document.querySelectorAll('#reportTabs a[data-prefetch]').forEach(function(a){
    a.addEventListener('mouseenter', function(){ pf(a.href); }, {once:true});
    a.addEventListener('focus', function(){ pf(a.href); }, {once:true});
  });
})();

// Form validations & spinner logic
(function() {
  function withSpinner(btn, fn) {
    var valid = fn();
    if (valid === false) return false;
    if (btn) {
      var spinner = btn.querySelector('.spinner-border');
      var label = btn.querySelector('.btn-label');
      if (spinner) spinner.classList.remove('d-none');
      if (label) label.textContent = 'Generating...';
      setTimeout(function() {
        if (spinner) spinner.classList.add('d-none');
        if (label) label.textContent = 'View PDF';
      }, 2500);
    }
    return true;
  }
  window.checkScoreFest = function(btn) {
    return withSpinner(btn, function() {
      var fest = document.getElementById('txtfestFromScore');
      if (!fest || fest.value === '' || fest.value === '0') { swal("Oops!", "Please select a festival", "error"); if (fest) fest.focus(); return false; }
      return true;
    });
  };
  window.checkScoreItem = function(btn) {
    return withSpinner(btn, function() {
      var fest = document.getElementById('cmbFestTypeScore');
      var item = document.getElementById('cbo_item');
      if (!fest || fest.value === '' || fest.value === '0') { swal("Oops!", "Please select a festival", "error"); if (fest) fest.focus(); return false; }
      if (!item || item.value === '' || item.value === '0') { swal("Oops!", "Please select an item", "error"); if (item) item.focus(); return false; }
      return true;
    });
  };
  window.checkScoreDateStage = function(btn) {
    return withSpinner(btn, function() {
      var d = document.getElementById('txtDateScore');
      var s = document.getElementById('cmbstageScore');
      if (!d || d.value === '') { swal("Oops!", "Please select a date", "error"); if (d) d.focus(); return false; }
      if (!s || s.value === '') { swal("Oops!", "Please select a stage", "error"); if (s) s.focus(); return false; }
      return true;
    });
  };

  // Robust festival items AJAX loader
  window.fetch_item_from_festival = window.fetch_callsheet_festival = function(festid) {
    var container = document.getElementById('cmbitem');
    if (!container) return;
    if (!festid || festid === '' || festid === '0') {
      container.innerHTML = '<select name="cbo_item" id="cbo_item" class="form-select py-2" required><option value="">-- Select Festival First --</option></select>';
      return;
    }
    container.innerHTML = '<div class="py-2 text-center text-muted small"><span class="spinner-border spinner-border-sm me-1"></span> Loading items...</div>';
    var formData = new FormData();
    formData.append('fest_id', festid);
    fetch('<?php echo base_url(); ?>ajax/loadajax/fetch_item_from_festival', {
      method: 'POST',
      body: formData
    })
    .then(function(r) { return r.text(); })
    .then(function(html) {
      container.innerHTML = html;
      var sel = container.querySelector('select');
      if (sel) {
        sel.id = 'cbo_item';
        sel.name = 'cbo_item';
        sel.className = 'form-select py-2';
        sel.required = true;
      }
    })
    .catch(function(err) {
      console.error(err);
      container.innerHTML = '<div class="text-danger small">Failed to load items.</div>';
    });
  };
})();
</script>
