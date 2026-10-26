<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-calendar-range-fill text-primary"></i> Stage Report - Date Wise
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Reports</li>
      <li class="breadcrumb-item active">Stage Report (Date Wise)</li>
    </ol>
  </nav>
</div>

<!-- Tab Navigation between Stage Reports -->
<div class="row justify-content-center mb-4">
  <div class="col-md-8 col-lg-6 col-xl-5">
    <div class="nav nav-pills nav-fill bg-white p-1 rounded-pill shadow-sm border">
      <a class="nav-link active rounded-pill fw-semibold py-2 px-3 d-flex align-items-center justify-content-center gap-2" href="<?php echo base_url(); ?>district/report/prereport/stagereportdate">
        <i class="bi bi-calendar-range-fill"></i>
        <span>Date Wise</span>
      </a>
      <a class="nav-link rounded-pill text-secondary fw-semibold py-2 px-3 d-flex align-items-center justify-content-center gap-2" href="<?php echo base_url(); ?>district/report/prereport/stagereport_all">
        <i class="bi bi-diagram-3-fill"></i>
        <span>All Stages &amp; Abstract</span>
      </a>
    </div>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6 col-xl-5">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-header bg-primary text-white py-3 px-4 rounded-top-4">
        <h5 class="card-title mb-0 text-white fw-bold d-flex align-items-center gap-2">
          <i class="bi bi-file-earmark-pdf-fill"></i> Stage Report (Date Wise)
        </h5>
      </div>

      <div class="card-body p-4">
        <?php echo form_open('district/report/prereportpdf/stagereportdate', array('id' => 'formStageReport', 'target' => '_blank', 'class' => 'no-global-loader')); ?>

        <div class="mb-3">
          <label for="cmbstage" class="form-label fw-semibold text-dark small mb-1">
            Select Stage Name <span class="text-danger">*</span>
          </label>
          <?php echo form_dropdown("cmbstage", $stage, '', 'class="form-select form-select-lg py-2" id="cmbstage" required'); ?>
        </div>

        <div class="mb-4">
          <label for="txtDate" class="form-label fw-semibold text-dark small mb-1">
            Select Festival Date <span class="text-danger">*</span>
          </label>
          <?php echo form_dropdown("txtDate", @$date_array, (@$date != '') ? $date : 'ALL', 'class="form-select form-select-lg py-2" id="txtDate" required'); ?>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate PDF Report</span>
          </button>
          <a href="<?php echo base_url(); ?>district/report/prereportpdf/stagereport_all" target="_blank" class="btn btn-outline-secondary rounded-pill py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-diagram-3-fill"></i>
            <span>View Stage Report - All Stages (PDF)</span>
          </a>
        </div>

        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>