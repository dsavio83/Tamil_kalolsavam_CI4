  <!-- Card 2: Result Number Filter -->
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
      <div class="card-header bg-secondary text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #475569 0%, #334155 100%) !important;">
        <div class="d-flex align-items-center gap-2">
          <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-hash text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Result Number Query</h5>
            <small class="text-white-50">Lookup by official result announcement number</small>
          </div>
        </div>
        <span class="badge bg-white text-dark fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small">
          <i class="bi bi-printer-fill me-1"></i> PDF
        </span>
      </div>

      <div class="card-body p-4 d-flex flex-column justify-content-between">
        <?php echo form_open('district/report/afterresult_reportpdf/numwise_timeresult_report', array('id' => 'formNumWise', 'target' => '_blank', 'class' => 'needs-validation', 'onsubmit' => 'return validateNumWise();')); ?>
        
        <div class="mb-4">
          <label for="txtResultno" class="form-label fw-semibold text-dark small mb-2 d-flex align-items-center gap-2">
            <i class="bi bi-tag-fill text-secondary"></i>
            <span>Result Announcement Number</span>
            <span class="text-danger">*</span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-card-text"></i></span>
            <input type="text" name="txtResultno" id="txtResultno" class="form-control border-start-0 py-2 fs-6 font-monospace" placeholder="e.g. 1 or 1,2,3" required />
          </div>
          <div class="form-text mt-2 text-muted small">
            <i class="bi bi-info-circle text-secondary me-1"></i> Enter a single Result Number or comma-separated list of announcement numbers.
          </div>
        </div>

        <div class="pt-3 border-top mt-auto text-end">
          <button type="submit" name="btnSubmit" id="btnSubmitNum" class="btn btn-secondary rounded-pill px-4 py-2 fw-semibold shadow-sm d-inline-flex align-items-center gap-2" style="background-color: #334155; border-color: #334155;">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate Number Report</span>
          </button>
        </div>

        <input type="hidden" name="hidUserId" id="hidUserId" value="<?php echo htmlspecialchars((string)$this->session->get('USERID')); ?>" />
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>

<script>
function validateNumWise() {
  var num = document.getElementById('txtResultno').value.trim();
  if (num === '') {
    if (typeof swal === 'function') {
      swal("Required", "Please enter a valid Result Number.", "warning");
    } else {
      alert("Please enter a valid Result Number.");
    }
    document.getElementById('txtResultno').focus();
    return false;
  }
  return true;
}
</script>
