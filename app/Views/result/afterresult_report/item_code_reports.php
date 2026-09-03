<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-file-earmark-bar-graph-fill text-primary"></i> Item Code Wise Result
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Result Reports</li>
      <li class="breadcrumb-item active">Item Code Wise</li>
    </ol>
  </nav>
</div>

<div class="row justify-content-center">
  <div class="col-lg-8 col-xl-7">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
      
      <!-- Card Header -->
      <div class="card-header bg-primary text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-music-note-beamed text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Item Code Result Query</h5>
            <small class="text-white-50">Generate winners & point report for specific item</small>
          </div>
        </div>
        <span class="badge bg-white text-primary fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small">
          <i class="bi bi-printer-fill me-1"></i> PDF
        </span>
      </div>

      <!-- Card Body -->
      <div class="card-body p-4">
        <?php echo form_open('report/afterresultreportpdf/item_code_reports', array('id' => 'formItemCode', 'target' => '_blank', 'class' => 'needs-validation', 'onsubmit' => 'return validateItemCode();')); ?>
        
        <div class="mb-4">
          <label for="item" class="form-label fw-semibold text-dark small mb-2 d-flex align-items-center gap-2">
            <i class="bi bi-123 text-primary"></i>
            <span>Competition Item Code</span>
            <span class="text-danger">*</span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-tag-fill"></i></span>
            <input type="text" name="item" id="item" class="form-control border-start-0 py-2 fs-6 font-monospace" placeholder="Enter Item Code (e.g. 151)" required onkeypress="return numbersonly(this, event, false);" onblur="lookupItemName(this.value);" />
          </div>
          <div id="itemNameFeedback" class="mt-2" style="display:none;"></div>
          <div class="form-text mt-1 text-muted small">
            <i class="bi bi-info-circle text-primary me-1"></i> Enter the numerical code of the competition item to print its declared results, ranks, and participants.
          </div>
        </div>

        <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
          <button type="submit" name="report" id="btnReport" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm d-inline-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate Item Result (PDF)</span>
          </button>
        </div>

        <input type="hidden" name="hidUserId" id="hidUserId" value="<?php echo htmlspecialchars((string)$this->session->get('USERID')); ?>" />
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>

<script>
function validateItemCode() {
  var code = document.getElementById('item').value.trim();
  if (code === '') {
    if (typeof swal === 'function') {
      swal("Required", "Please enter a valid Item Code.", "warning");
    } else {
      alert("Please enter a valid Item Code.");
    }
    document.getElementById('item').focus();
    return false;
  }
  return true;
}

function lookupItemName(code) {
  var feedback = document.getElementById('itemNameFeedback');
  if (!code || code.trim() === '') {
    feedback.style.display = 'none';
    return;
  }
  
  var formData = new FormData();
  formData.append('code', code);

  fetch('<?php echo base_url(); ?>ajax/ajax2/fetch_item_name', {
    method: 'POST',
    body: formData
  })
  .then(function(r) { return r.text(); })
  .then(function(text) {
    var trimmed = text.trim();
    if (trimmed && trimmed !== 'Invalid Item Code') {
      feedback.innerHTML = '<div class="alert alert-success py-2 px-3 mb-0 rounded-3 small d-flex align-items-center gap-2 font-tamil" style="font-family: \'TAU-Pallai\', sans-serif; font-size: 14px;"><i class="bi bi-music-note-beamed text-success"></i> <span>' + trimmed + '</span></div>';
      feedback.style.display = 'block';
    } else if (trimmed === 'Invalid Item Code') {
      feedback.innerHTML = '<div class="alert alert-warning py-2 px-3 mb-0 rounded-3 small d-flex align-items-center gap-2"><i class="bi bi-exclamation-triangle-fill text-warning"></i> <span>Item not found for code ' + code + '</span></div>';
      feedback.style.display = 'block';
    } else {
      feedback.style.display = 'none';
    }
  })
  .catch(function() {
    feedback.style.display = 'none';
  });
}
</script>
