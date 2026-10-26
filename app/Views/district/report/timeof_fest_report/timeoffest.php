<div class="pagetitle mb-3">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
        <span class="rounded-3 bg-primary-subtle text-primary p-2 d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 40px; height: 40px;">
          <i class="bi bi-shield-lock-fill fs-5"></i>
        </span>
        <span>Confidential Result Report</span>
      </h1>
      <nav>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
          <li class="breadcrumb-item">Result Reports</li>
          <li class="breadcrumb-item active">Confidential Results</li>
        </ol>
      </nav>
    </div>
    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-semibold d-inline-flex align-items-center gap-1.5 shadow-xs">
      <i class="bi bi-lock-fill"></i>
      <span>Confidential &bull; Official Only</span>
    </span>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-lg-10 col-xl-9">
    <div class="card border-0 shadow-sm rounded-4 mb-3">
      <div class="card-body p-3">
        <?php echo form_open('district/report/timeof_fest_report/timefest_result_confidential', array('id' => 'timeoffest', 'class' => 'm-0 needs-validation')); ?>
        <div class="row g-2 align-items-center">
          
          <div class="col-12 col-md">
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0 text-muted py-2 px-3 fw-semibold">
                <i class="bi bi-award-fill text-warning me-1.5"></i>
                <span>Festival Category:</span>
              </span>
              <?php echo form_dropdown("resultFest", $fest, set_value('resultFest', @$fest_selected), 'class="form-select border-start-0 py-2 fs-6 fw-medium text-dark shadow-none" id="resultFest" required onchange="if(this.value){ document.getElementById(\'timeoffest\').submit(); }"'); ?>
            </div>
          </div>

          <div class="col-12 col-md-auto">
            <button type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold shadow-sm d-inline-flex align-items-center justify-content-center gap-2 w-100">
              <i class="bi bi-search"></i>
              <span>Get Results</span>
            </button>
          </div>

        </div>
        <input type="hidden" name="hidUserId" id="hidUserId" value="<?php echo htmlspecialchars((string)$this->session->get('USERID')); ?>" />
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>
