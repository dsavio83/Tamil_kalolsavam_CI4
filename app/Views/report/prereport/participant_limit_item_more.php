<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-person-lines-fill text-primary"></i> Multiple Items Participation
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Pre-Fest Reports</li>
      <li class="breadcrumb-item active">Multiple Items</li>
    </ol>
  </nav>
</div>

<div class="row g-4">
  <!-- Card 1: Summary Report -->
  <div class="col-md-12 col-xl-6">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-header bg-primary text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: rgba(255, 255, 255, 0.2);">
            <i class="bi bi-people-fill text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Multiple Items Participants</h5>
            <small class="text-white-50" style="font-size: 0.8rem;">Summary count register of students participating in multiple events</small>
          </div>
        </div>
        <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold"><i class="bi bi-printer me-1"></i> PDF</span>
      </div>

      <div class="card-body p-4 d-flex flex-column justify-content-between">
        <?php echo form_open('report/prereportpdf/more_limit_partlist', array('id' => 'formMoreLimitSummary', 'target' => '_blank', 'class' => 'no-global-loader')); ?>

        <div class="mb-3">
          <label for="cmbFestType" class="form-label fw-bold text-dark small mb-1">
            <i class="bi bi-trophy text-warning me-1"></i> Festival Category <span class="text-danger">*</span>
          </label>
          <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-select py-2 rounded-3 shadow-none border-secondary-subtle" id="cmbFestType" required'); ?>
          <div class="form-text text-muted small mt-1">
            <i class="bi bi-info-circle me-1"></i> Select the competition category (LP, UP, HS, HSS).
          </div>
        </div>

        <div class="mb-4">
          <label for="txtLimitcode" class="form-label fw-bold text-dark small mb-1">
            <i class="bi bi-123 text-primary me-1"></i> Minimum Items Limit <span class="text-danger">*</span>
          </label>
          <?php echo form_dropdown("txtLimitcode", $no_array, '', 'class="form-select py-2 rounded-3 shadow-none border-secondary-subtle" id="txtLimitcode" required'); ?>
          <div class="form-text text-muted small mt-1">
            <i class="bi bi-info-circle me-1"></i> Filters students participating in at least this number of events.
          </div>
        </div>

        <div class="bg-light p-3 rounded-3 mb-4 border border-light-subtle">
          <div class="text-muted small mb-1 d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill text-success"></i> <span>Consolidated participant register grouped by festival</span>
          </div>
          <div class="text-muted small d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill text-success"></i> <span>Shows Reg No, Name, School, and total participating count</span>
          </div>
        </div>

        <div class="d-grid pt-2">
          <button type="submit" class="btn btn-primary rounded-pill py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate Summary Report (PDF)</span>
          </button>
        </div>

        <?php echo form_close(); ?>
      </div>
    </div>
  </div>

  <!-- Card 2: Detailed Report with Item Names -->
  <div class="col-md-12 col-xl-6">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-header text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between" style="background-color: #1e293b;">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: rgba(255, 255, 255, 0.2);">
            <i class="bi bi-card-checklist text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Multiple Items Participants With Details</h5>
            <small class="text-white-50" style="font-size: 0.8rem;">Detailed breakdown showing all enrolled event names & codes</small>
          </div>
        </div>
        <span class="badge bg-white text-dark rounded-pill px-3 py-1 fw-bold"><i class="bi bi-printer me-1"></i> PDF</span>
      </div>

      <div class="card-body p-4 d-flex flex-column justify-content-between">
        <?php echo form_open('report/prereportpdf/more_limit_partlist_detailed', array('id' => 'formMoreLimitDetailed', 'target' => '_blank', 'class' => 'no-global-loader')); ?>

        <div class="mb-3">
          <label for="cmbFestTypeDetailed" class="form-label fw-bold text-dark small mb-1">
            <i class="bi bi-trophy text-warning me-1"></i> Festival Category <span class="text-danger">*</span>
          </label>
          <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-select py-2 rounded-3 shadow-none border-secondary-subtle" id="cmbFestTypeDetailed" required'); ?>
          <div class="form-text text-muted small mt-1">
            <i class="bi bi-info-circle me-1"></i> Select the competition category (LP, UP, HS, HSS).
          </div>
        </div>

        <div class="mb-4">
          <label for="txtLimitcodeDetailed" class="form-label fw-bold text-dark small mb-1">
            <i class="bi bi-123 text-primary me-1"></i> Minimum Items Limit <span class="text-danger">*</span>
          </label>
          <?php echo form_dropdown("txtLimitcode", $no_array, '', 'class="form-select py-2 rounded-3 shadow-none border-secondary-subtle" id="txtLimitcodeDetailed" required'); ?>
          <div class="form-text text-muted small mt-1">
            <i class="bi bi-info-circle me-1"></i> Filters students participating in at least this number of events.
          </div>
        </div>

        <div class="bg-light p-3 rounded-3 mb-4 border border-light-subtle">
          <div class="text-muted small mb-1 d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill text-success"></i> <span>Full item-by-item breakdown listing each event name and code</span>
          </div>
          <div class="text-muted small d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill text-success"></i> <span>Includes item type (Single/Group), stage venue and schedule timings</span>
          </div>
        </div>

        <div class="d-grid pt-2">
          <button type="submit" class="btn text-white rounded-pill py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" style="background-color: #1e293b; border-color: #1e293b;">
            <i class="bi bi-file-earmark-pdf-fill text-warning"></i>
            <span>Generate Detailed Report with Items (PDF)</span>
          </button>
        </div>

        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>

