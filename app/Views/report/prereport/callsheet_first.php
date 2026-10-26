<style>
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
        <span>Call Sheet</span>
      </h1>
      <p class="text-muted small mb-0">Stage-wise reports — call sheet, time sheet, score sheet and tabulation as PDF.</p>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 bg-light px-3 py-1.5 rounded-pill border">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none"><i class="bi bi-house-door-fill me-1"></i>Home</a></li>
        <li class="breadcrumb-item text-muted">Reports</li>
        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Call Sheet</li>
      </ol>
    </nav>
  </div>
</div>

<!-- 3-Column Report Generation Row: 1. Festival, 2. by Item, 3. by Date & Stage -->
<div class="row g-4 mb-4">

  <!-- Card 1: Call Sheet by Festival -->
  <div class="col-12 col-lg-4 d-flex">
    <?php echo form_open('report/prereportpdf/callsheet_all', array('id' => 'callsheetall', 'target' => '_blank', 'class' => 'w-100 d-flex flex-column needs-spinner no-global-loader')); ?>
    <input type="hidden" name="hidUserId" id="hidUserId1" />

    <div class="card border-0 shadow-sm rounded-4 w-100 flex-grow-1 overflow-hidden d-flex flex-column stage-card">
      <div class="card-header bg-primary text-white py-3 px-4 d-flex align-items-center">
        <i class="bi bi-trophy-fill fs-5 me-2"></i>
        <h6 class="mb-0 fw-bold">Call Sheet by Festival</h6>
      </div>

      <div class="card-body p-4 flex-grow-1 d-flex flex-column">
        <div class="mb-3">
          <label for="cmbFestType1" class="form-label fw-bold text-dark small d-flex align-items-center">
            <i class="bi bi-flag-fill text-warning me-1"></i>
            <span>Festival <span class="text-danger">*</span></span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-tags-fill"></i></span>
            <?php echo form_dropdown("cmbFestType1", $fest, '', 'class="form-select border-start-0 py-2" id="cmbFestType1" required'); ?>
          </div>
        </div>
      </div>

      <div class="card-footer bg-white border-top p-3 mt-auto">
        <button type="submit" name="Report" value="Report" onclick="return callsheet(this)" class="btn btn-primary w-100 py-2 rounded-pill shadow-sm fw-semibold d-inline-flex align-items-center justify-content-center btn-spinner">
          <i class="bi bi-file-earmark-pdf-fill me-2"></i>
          <span class="btn-label">View PDF</span>
          <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
        </button>
      </div>
    </div>
    <?php echo form_close(); ?>
  </div>

  <!-- Card 2: Call Sheet by Item -->
  <div class="col-12 col-lg-4 d-flex">
    <?php echo form_open('report/prereportpdf/callsheet_report', array('id' => 'callsheet', 'target' => '_blank', 'class' => 'w-100 d-flex flex-column needs-spinner no-global-loader')); ?>
    <input type="hidden" name="hidUserId" id="hidUserId" />

    <div class="card border-0 shadow-sm rounded-4 w-100 flex-grow-1 overflow-hidden d-flex flex-column stage-card">
      <div class="card-header bg-primary text-white py-3 px-4 d-flex align-items-center">
        <i class="bi bi-journal-text fs-5 me-2"></i>
        <h6 class="mb-0 fw-bold">Call Sheet by Item</h6>
      </div>

      <div class="card-body p-4 flex-grow-1 d-flex flex-column">
        <div class="mb-3">
          <label for="cmbFestTypeCallItem" class="form-label fw-bold text-dark small d-flex align-items-center">
            <i class="bi bi-trophy text-warning me-1"></i>
            <span>Festival <span class="text-danger">*</span></span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-tags-fill"></i></span>
            <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-select border-start-0 py-2" id="cmbFestTypeCallItem" onchange="fetch_callsheet_festival(this.value)" required'); ?>
          </div>
        </div>

        <div class="mb-3">
          <label for="cbo_item" class="form-label fw-bold text-dark small d-flex align-items-center">
            <i class="bi bi-card-list text-primary me-1"></i>
            <span>Item <span class="text-danger">*</span></span>
          </label>
          <div id="cmbitem">
            <?php echo form_dropdown("cbo_item", array('' => '-- Select Festival First --'), '', 'id="cbo_item" class="form-select py-2" required'); ?>
          </div>
        </div>
      </div>

      <div class="card-footer bg-white border-top p-3 mt-auto">
        <button type="submit" name="Report" value="Report" onclick="return callsheetfirst(this)" class="btn btn-primary w-100 py-2 rounded-pill shadow-sm fw-semibold d-inline-flex align-items-center justify-content-center btn-spinner">
          <i class="bi bi-file-earmark-pdf-fill me-2"></i>
          <span class="btn-label">View PDF</span>
          <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
        </button>
      </div>
    </div>
    <?php echo form_close(); ?>
  </div>

  <!-- Card 3: Call Sheet by Date & Stage -->
  <div class="col-12 col-lg-4 d-flex">
    <?php echo form_open('report/prereportpdf/call_date_stage', array('id' => 'clu', 'target' => '_blank', 'class' => 'w-100 d-flex flex-column needs-spinner no-global-loader')); ?>
    <input type="hidden" name="hidUserId" id="hidUserId2" />

    <div class="card border-0 shadow-sm rounded-4 w-100 flex-grow-1 overflow-hidden d-flex flex-column stage-card">
      <div class="card-header bg-primary text-white py-3 px-4 d-flex align-items-center">
        <i class="bi bi-calendar2-range-fill fs-5 me-2"></i>
        <h6 class="mb-0 fw-bold">Call Sheet by Date &amp; Stage</h6>
      </div>

      <div class="card-body p-4 flex-grow-1 d-flex flex-column">
        <div class="mb-3">
          <label for="txtDate" class="form-label fw-bold text-dark small d-flex align-items-center">
            <i class="bi bi-calendar-event text-primary me-1"></i>
            <span>Date <span class="text-danger">*</span></span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-check"></i></span>
            <?php echo form_dropdown("txtDate", $date_array, '', 'class="form-select border-start-0 py-2" id="txtDate" required'); ?>
          </div>
        </div>

        <div class="mb-3">
          <label for="cmbstage" class="form-label fw-bold text-dark small d-flex align-items-center">
            <i class="bi bi-building text-primary me-1"></i>
            <span>Stage <span class="text-danger">*</span></span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-geo-alt-fill text-danger"></i></span>
            <?php echo form_dropdown("cmbstage", $stage, '', 'class="form-select border-start-0 py-2" id="cmbstage" required'); ?>
          </div>
        </div>
      </div>

      <div class="card-footer bg-white border-top p-3 mt-auto">
        <button type="submit" name="Report" value="Report" onclick="return handleDateStageSubmit(this)" class="btn btn-primary w-100 py-2 rounded-pill shadow-sm fw-semibold d-inline-flex align-items-center justify-content-center btn-spinner">
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
  window.callsheet = function(btn) {
    return withSpinner(btn, function() {
      var fest = document.getElementById('cmbFestType1');
      if (!fest || fest.value === '' || fest.value === '0') { swal("Oops!", "Please select a festival", "error"); if (fest) fest.focus(); return false; }
      return true;
    });
  };
  window.callsheetfirst = function(btn) {
    return withSpinner(btn, function() {
      var fest = document.getElementById('cmbFestTypeCallItem') || document.getElementById('cmbFestType');
      var item = document.getElementById('cbo_item');
      if (!fest || fest.value === '' || fest.value === '0') { swal("Oops!", "Please select a festival", "error"); if (fest) fest.focus(); return false; }
      if (!item || item.value === '' || item.value === '0') { swal("Oops!", "Please select an item", "error"); if (item) item.focus(); return false; }
      return true;
    });
  };
  window.handleDateStageSubmit = function(btn) {
    return withSpinner(btn, function() {
      var d = document.getElementById('txtDate');
      var s = document.getElementById('cmbstage');
      if (!d || d.value === '') { swal("Oops!", "Please select a date", "error"); if (d) d.focus(); return false; }
      if (!s || s.value === '') { swal("Oops!", "Please select a stage", "error"); if (s) s.focus(); return false; }
      return true;
    });
  };

  // Robust festival items AJAX loader
  window.fetch_callsheet_festival = window.fetch_item_from_festival = function(festid) {
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

