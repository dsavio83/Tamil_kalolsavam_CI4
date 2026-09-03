  <!-- Card 3: Call Sheet by Date & Stage -->
  <div class="col-12 col-xl-4">
    <?php echo form_open('report/prereportpdf/call_date_stage', array('id' => 'clu', 'target' => '_blank', 'class' => 'h-100 needs-spinner')); ?>
    <input type="hidden" name="hidUserId" id="hidUserId" />

    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden d-flex flex-column">
      <div class="card-header bg-primary text-white py-3 px-4 d-flex align-items-center gap-2">
        <i class="bi bi-calendar2-range-fill fs-5"></i>
        <h6 class="mb-0 fw-bold">Call Sheet by Date &amp; Stage</h6>
      </div>

      <div class="card-body p-4 flex-grow-1">
        <div class="mb-3">
          <label for="txtDate" class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
            <i class="bi bi-calendar-event text-primary"></i>
            <span>Date <span class="text-danger">*</span></span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-calendar-check"></i></span>
            <?php echo form_dropdown("txtDate", $date_array, '', 'class="form-select border-start-0 py-2" id="txtDate" required'); ?>
          </div>
        </div>

        <div class="mb-3">
          <label for="cmbstage" class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
            <i class="bi bi-building text-primary"></i>
            <span>Stage <span class="text-danger">*</span></span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-geo-alt-fill text-danger"></i></span>
            <?php echo form_dropdown("cmbstage", $stage, '', 'class="form-select border-start-0 py-2" id="cmbstage" required'); ?>
          </div>
        </div>
      </div>

      <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
        <button type="submit" name="Report" value="Report" onclick="return handleDateStageSubmit(this)" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm fw-semibold d-inline-flex align-items-center gap-2 w-100 justify-content-center btn-spinner">
          <i class="bi bi-file-earmark-pdf-fill"></i>
          <span class="btn-label">View PDF</span>
          <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
      </div>
    </div>
    <?php echo form_close(); ?>
  </div>

</div>

<script>
(function() {
  function withSpinner(btn, fn) {
    var valid = fn();
    if (valid === false) return false;
    if (btn) {
      var spinner = btn.querySelector('.spinner-border');
      var label = btn.querySelector('.btn-label');
      if (spinner) spinner.classList.remove('d-none');
      if (btn) btn.disabled = true;
      if (label) label.textContent = 'Generating...';
      setTimeout(function() {
        if (spinner) spinner.classList.add('d-none');
        if (btn) btn.disabled = false;
        if (label) label.textContent = 'View PDF';
      }, 4000);
    }
    return true;
  }
  window.callsheetfirst = function(btn) {
    return withSpinner(btn, function() {
      var fest = document.getElementById('cmbFestType');
      var item = document.getElementById('cbo_item');
      if (!fest || fest.value === '' || fest.value === '0') { swal("Oops!", "Please select a festival", "error"); if (fest) fest.focus(); return false; }
      if (!item || item.value === '' || item.value === '0') { swal("Oops!", "Please select an item", "error"); if (item) item.focus(); return false; }
      return true;
    });
  };
  window.callsheet = function(btn) {
    return withSpinner(btn, function() {
      var fest = document.getElementById('cmbFestType1');
      if (!fest || fest.value === '' || fest.value === '0') { swal("Oops!", "Please select a festival", "error"); if (fest) fest.focus(); return false; }
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

  // Enhance fetch_callsheet_festival with spinner inside dropdown
  var _origFetch = window.fetch_callsheet_festival;
  window.fetch_callsheet_festival = function(festid) {
    var container = document.getElementById('cmbitem');
    if (container) container.innerHTML = '<div class="py-2 text-center text-muted small"><span class="spinner-border spinner-border-sm me-1"></span> Loading items...</div>';
    if (typeof _origFetch === 'function') {
      return _origFetch(festid);
    }
    // fallback direct Ajax
    var oOptions = {
      method: "post",
      parameters: {'fest_id': festid},
      onSuccess: function(transport) { if (container) container.innerHTML = transport.responseText; },
      onFailure: function() { if (container) container.innerHTML = '<div class="text-danger small">Failed to load items.</div>'; }
    };
    new Ajax.Updater({}, path + "ajax/ajax2/fetch_callsheet_festival", oOptions);
  };

  // Attach spinner to any form with .needs-spinner on submit (fallback)
  document.addEventListener('submit', function(e) {
    var form = e.target;
    if (form && form.classList.contains('needs-spinner')) {
      var btn = form.querySelector('button[type="submit"]');
      if (btn && !btn.disabled) {
        var sp = btn.querySelector('.spinner-border');
        var lb = btn.querySelector('.btn-label');
        if (sp) sp.classList.remove('d-none');
        btn.disabled = true;
        if (lb) lb.textContent = 'Generating...';
        setTimeout(function(){ if (sp) sp.classList.add('d-none'); btn.disabled = false; if (lb) lb.textContent = 'View PDF'; }, 4000);
      }
    }
  });
})();
</script>
