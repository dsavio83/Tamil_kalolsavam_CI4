  <!-- Card 3: Date & Stage Wise Cluster Report -->
  <div class="col-md-12 col-xl-4">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-header bg-dark text-white py-3 px-4 rounded-top-4">
        <h5 class="card-title mb-0 text-white fw-bold d-flex align-items-center gap-2">
          <i class="bi bi-calendar2-range-fill"></i> Date &amp; Stage Report
        </h5>
        <small class="text-white-50">Cluster report filtered by date and stage</small>
      </div>

      <div class="card-body p-4 d-flex flex-column justify-content-between">
        <?php echo form_open('report/prereportpdf/cluster_date_stage', array('id' => 'formCluDateStage', 'target' => '_blank', 'class' => 'no-global-loader')); ?>

        <div class="mb-3">
          <label for="txtDate" class="form-label fw-semibold text-dark small mb-1">
            Festival Date <span class="text-danger">*</span>
          </label>
          <?php echo form_dropdown("txtDate", @$date_array, (@$date != '') ? $date : 'All', 'class="form-select py-2" id="txtDate" required'); ?>
        </div>

        <div class="mb-4">
          <label for="cmbstage" class="form-label fw-semibold text-dark small mb-1">
            Stage Name <span class="text-danger">*</span>
          </label>
          <?php echo form_dropdown("cmbstage", $stage, '', 'class="form-select py-2" id="cmbstage" required'); ?>
        </div>

        <div class="d-grid gap-2 mt-auto">
          <button type="submit" class="btn btn-primary rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate PDF Report</span>
          </button>
        </div>

        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>