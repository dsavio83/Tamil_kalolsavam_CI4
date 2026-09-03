<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-shield-lock-fill text-primary"></i> Confidential Result Report
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Result Reports</li>
      <li class="breadcrumb-item active">Confidential Results</li>
    </ol>
  </nav>
</div>

<div class="row justify-content-center">
  <div class="col-lg-8 col-xl-7">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
      <div class="card-header bg-primary text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-trophy-fill text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Declared Results by Festival</h5>
            <small class="text-white-50">Filter confidential item results</small>
          </div>
        </div>
        <span class="badge bg-white text-primary fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small">
          <i class="bi bi-lock-fill me-1"></i> Confidential
        </span>
      </div>

      <div class="card-body p-4">
        <?php echo form_open('report/timefestreport/timefest_result_confidential', array('id' => 'timeoffest', 'class' => 'needs-validation')); ?>
        
        <div class="mb-4">
          <label for="resultFest" class="form-label fw-semibold text-dark small mb-2 d-flex align-items-center gap-2">
            <i class="bi bi-award-fill text-warning"></i>
            <span>Festival Category</span>
            <span class="text-danger">*</span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-filter"></i></span>
            <?php echo form_dropdown("resultFest", $fest, set_value('resultFest', @$fest_selected), 'class="form-select border-start-0 py-2 fs-6" id="resultFest" required'); ?>
          </div>
          <div class="form-text mt-1 text-muted small">
            <i class="bi bi-info-circle text-primary me-1"></i> Select a festival category to view completed and declared competition results.
          </div>
        </div>

        <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
          <button type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-search"></i>
            <span>Get Results</span>
          </button>
        </div>

        <input type="hidden" name="hidUserId" id="hidUserId" value="<?php echo htmlspecialchars((string)$this->session->get('USERID')); ?>" />
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>
