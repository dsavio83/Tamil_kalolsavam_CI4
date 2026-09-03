<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-award-fill text-primary"></i> Grade Wise (School)
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Result Reports</li>
      <li class="breadcrumb-item active">Grade Wise School</li>
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
            <i class="bi bi-building-check text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">School in Grade Wise Report</h5>
            <small class="text-white-50">Filter participants and grades by school code</small>
          </div>
        </div>
        <span class="badge bg-white text-primary fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small">
          <i class="bi bi-printer-fill me-1"></i> PDF
        </span>
      </div>

      <!-- Card Body -->
      <div class="card-body p-4">
        <?php echo form_open('report/resultindex/gradewise_report', array('id' => 'formGradeWise', 'target' => '_blank', 'class' => 'needs-validation')); ?>
        
        <div class="mb-4">
          <label for="txtSchoolCode" class="form-label fw-semibold text-dark small mb-2 d-flex align-items-center gap-2">
            <i class="bi bi-hash text-primary"></i>
            <span>School Code</span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-building"></i></span>
            <input type="text" name="txtSchoolCode" id="txtSchoolCode" class="form-control border-start-0 py-2 fs-6 font-monospace" placeholder="Enter school code (or leave blank for All Schools)" onkeypress="return numbersonly(this, event, false);" onblur="lookupSchoolName(this.value);" />
          </div>
          <div id="schoolNameFeedback" class="mt-2" style="display:none;"></div>
          <div class="form-text mt-1 text-muted small">
            <i class="bi bi-info-circle text-primary me-1"></i> Enter a School Code to filter grades for that school, or click <strong>All Schools Report</strong> below.
          </div>
        </div>

        <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 pt-3 border-top">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 w-100 w-sm-auto justify-content-center" onclick="submitAllSchools();">
            <i class="bi bi-collection-fill"></i>
            <span>All Schools Report</span>
          </button>

          <button type="submit" name="Report" id="btnReport" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm d-inline-flex align-items-center gap-2 w-100 w-sm-auto justify-content-center">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate School Grade Report</span>
          </button>
        </div>

        <input type="hidden" name="hidUserId" id="hidUserId" value="<?php echo htmlspecialchars((string)$this->session->get('USERID')); ?>" />
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>

<script>
function submitAllSchools() {
  document.getElementById('txtSchoolCode').value = '';
  document.getElementById('formGradeWise').submit();
}

function lookupSchoolName(code) {
  var feedback = document.getElementById('schoolNameFeedback');
  if (!code || code.trim() === '') {
    feedback.style.display = 'none';
    return;
  }
  
  var formData = new FormData();
  formData.append('fest_id', code);

  fetch('<?php echo base_url(); ?>ajax/ajax2/fetch_school_from_festival', {
    method: 'POST',
    body: formData
  })
  .then(function(r) { return r.text(); })
  .then(function(html) {
    if (html && html.trim().length > 0) {
      feedback.innerHTML = '<div class="alert alert-info py-2 px-3 mb-0 rounded-3 small d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-primary"></i> <span>' + html + '</span></div>';
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
