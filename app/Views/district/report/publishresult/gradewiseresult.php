<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-star-fill text-warning"></i> Grade Wise Result
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Result Reports</li>
      <li class="breadcrumb-item active">Grade Wise Participants</li>
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
            <i class="bi bi-patch-check-fill text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Select Festival &amp; Grade</h5>
            <small class="text-white-50">Filter participants by awarded grade</small>
          </div>
        </div>
        <span class="badge bg-white text-primary fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small">
          <i class="bi bi-printer-fill me-1"></i> PDF
        </span>
      </div>

      <!-- Card Body -->
      <div class="card-body p-4">
        <?php echo form_open('district/report/resultindex/grade_report', array('id' => 'formGradeReport', 'target' => '_blank', 'class' => 'needs-validation', 'onsubmit' => 'return validateGradeForm();')); ?>
        
        <?php
        $gradeOptions = [
            'A'   => 'A - Grade (Outstanding)',
            'B'   => 'B - Grade (Commendable)',
            'C'   => 'C - Grade (Meritorious)',
            'ALL' => 'All Grades (A, B & C)'
        ];
        ?>

        <!-- Festival Category -->
        <div class="mb-3">
          <label for="cmbFestType" class="form-label fw-semibold text-dark small mb-2 d-flex align-items-center gap-2">
            <i class="bi bi-trophy text-warning"></i>
            <span>Festival Category</span>
            <span class="text-danger">*</span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-filter"></i></span>
            <?php echo form_dropdown("cmbFestType", $fest, 'ALL', 'class="form-select border-start-0 py-2 fs-6" id="cmbFestType" required'); ?>
          </div>
        </div>

        <!-- Grade Selection -->
        <div class="mb-4">
          <label for="grade" class="form-label fw-semibold text-dark small mb-2 d-flex align-items-center gap-2">
            <i class="bi bi-star-half text-warning"></i>
            <span>Grade Awarded</span>
            <span class="text-danger">*</span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-award"></i></span>
            <?php echo form_dropdown("grade", $gradeOptions, 'ALL', 'class="form-select border-start-0 py-2 fs-6" id="grade" required'); ?>
          </div>
          <div class="form-text mt-1 text-muted small">
            <i class="bi bi-info-circle text-primary me-1"></i> Filter all participants who attained a specific grade across competition events.
          </div>
        </div>

        <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
          <button type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm d-inline-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate Grade Report (PDF)</span>
          </button>
        </div>

        <input type="hidden" name="hidUserId" id="hidUserId" value="<?php echo htmlspecialchars((string)$this->session->get('USERID')); ?>" />
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>

<script>
function validateGradeForm() {
  var fest = document.getElementById('cmbFestType').value;
  if (!fest || fest === '') {
    if (typeof swal === 'function') {
      swal("Required", "Please select a Festival Category.", "warning");
    } else {
      alert("Please select a Festival Category.");
    }
    document.getElementById('cmbFestType').focus();
    return false;
  }
  return true;
}
</script>
