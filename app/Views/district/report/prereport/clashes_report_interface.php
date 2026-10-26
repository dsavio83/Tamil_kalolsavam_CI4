<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-triangle-fill text-primary"></i> Schedule Clash Report
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Reports</li>
      <li class="breadcrumb-item active">Schedule Clashes</li>
    </ol>
  </nav>
</div>

<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6 col-xl-5">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-header bg-primary text-white py-3 px-4 rounded-top-4">
        <h5 class="card-title mb-0 text-white fw-bold d-flex align-items-center gap-2">
          <i class="bi bi-file-earmark-pdf-fill"></i> Schedule Clash Analysis
        </h5>
      </div>

      <div class="card-body p-4">
        <?php echo form_open('district/report/prereportpdf/clashes_details', array('id' => 'formClash', 'target' => '_blank', 'class' => 'no-global-loader')); ?>

        <div class="mb-3">
          <label for="cmbFestType" class="form-label fw-semibold text-dark small mb-1">
            Festival Category <span class="text-danger">*</span>
          </label>
          <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-select py-2" id="cmbFestType" required'); ?>
        </div>

        <div class="mb-4">
          <label for="txtDate" class="form-label fw-semibold text-dark small mb-1">
            Festival Date <span class="text-danger">*</span>
          </label>
          <?php echo form_dropdown('txtDate', $date_array, '', 'id="txtDate" class="form-select py-2" required'); ?>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate Clash Report</span>
          </button>
        </div>

        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>