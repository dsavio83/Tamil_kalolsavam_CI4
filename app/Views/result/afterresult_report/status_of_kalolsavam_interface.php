<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-activity text-primary"></i> Status of Kalolsavam
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Result Reports</li>
      <li class="breadcrumb-item active">Festival Progress Status</li>
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
            <i class="bi bi-pie-chart-fill text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Festival Completion Progress</h5>
            <small class="text-white-50">View completed vs remaining events by date</small>
          </div>
        </div>
        <span class="badge bg-white text-primary fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small">
          <i class="bi bi-printer-fill me-1"></i> PDF
        </span>
      </div>

      <!-- Card Body -->
      <div class="card-body p-4">
        <?php echo form_open('report/afterresultreportpdf/status_of_kalolsavam1', array('id' => 'formStatusKalolsavam', 'target' => '_blank', 'class' => 'needs-validation')); ?>
        
        <!-- Festival Category -->
        <div class="mb-3">
          <label for="cmbFestType" class="form-label fw-semibold text-dark small mb-2 d-flex align-items-center gap-2">
            <i class="bi bi-trophy-fill text-warning"></i>
            <span>Festival Category</span>
            <span class="text-danger">*</span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-filter"></i></span>
            <?php echo form_dropdown("cmbFestType", $fest, 'All', 'class="form-select border-start-0 py-2 fs-6" id="cmbFestType" required'); ?>
          </div>
        </div>

        <!-- Festival Date -->
        <div class="mb-4">
          <label for="festdate" class="form-label fw-semibold text-dark small mb-2 d-flex align-items-center gap-2">
            <i class="bi bi-calendar-event text-primary"></i>
            <span>Festival Date</span>
            <span class="text-danger">*</span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-calendar3"></i></span>
            <?php echo form_dropdown("festdate", $date_array, 'All', 'class="form-select border-start-0 py-2 fs-6" id="festdate" required'); ?>
          </div>
          <div class="form-text mt-1 text-muted small">
            <i class="bi bi-info-circle text-primary me-1"></i> Select a specific competition date or choose "All Dates" for complete festival status.
          </div>
        </div>

        <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
          <button type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm d-inline-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate Status Report (PDF)</span>
          </button>
        </div>

        <input type="hidden" name="hidUserId" id="hidUserId" value="<?php echo htmlspecialchars((string)$this->session->get('USERID')); ?>" />
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>
