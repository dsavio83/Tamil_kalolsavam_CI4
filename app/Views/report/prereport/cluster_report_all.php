
  <!-- Card 2: Festival Wise Cluster Report -->
  <div class="col-md-12 col-xl-4">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-header bg-secondary text-white py-3 px-4 rounded-top-4">
        <h5 class="card-title mb-0 text-white fw-bold d-flex align-items-center gap-2">
          <i class="bi bi-file-earmark-ruled-fill"></i> Festival Wise Report
        </h5>
        <small class="text-white-50">Cluster report grouped by festival category</small>
      </div>

      <div class="card-body p-4 d-flex flex-column justify-content-between">
        <?php echo form_open('report/prereportpdf/cluster_report_all', array('id' => 'formCluFest', 'target' => '_blank', 'class' => 'no-global-loader')); ?>

        <div class="mb-4">
          <label for="cmbFestType1" class="form-label fw-semibold text-dark small mb-1">
            Festival Category <span class="text-danger">*</span>
          </label>
          <?php echo form_dropdown("cmbFestType1", $fest, '', 'class="form-select py-2" id="cmbFestType1" required'); ?>
        </div>

        <div class="d-grid gap-2 mt-4">
          <button type="submit" class="btn btn-primary rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate PDF Report</span>
          </button>
          <button type="submit" formaction="<?php echo base_url(); ?>report/prereportpdf/cluster_report_all_doc" class="btn btn-outline-secondary rounded-pill py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-file-earmark-text-fill"></i>
            <span>Export Press Report (TXT)</span>
          </button>
        </div>

        <?php echo form_close(); ?>
      </div>
    </div>
  </div>