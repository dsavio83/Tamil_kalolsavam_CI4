  <!-- Card 2: Call Sheet by Festival -->
  <div class="col-12 col-xl-4">
    <?php echo form_open('report/prereportpdf/callsheet_all', array('id' => 'callsheetall', 'target' => '_blank', 'class' => 'h-100 needs-spinner')); ?>
    <input type="hidden" name="hidUserId" id="hidUserId" />

    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden d-flex flex-column">
      <div class="card-header bg-primary text-white py-3 px-4 d-flex align-items-center gap-2">
        <i class="bi bi-collection-play-fill fs-5"></i>
        <h6 class="mb-0 fw-bold">Call Sheet by Festival</h6>
      </div>

      <div class="card-body p-4 flex-grow-1">
        <div class="mb-3">
          <label for="cmbFestType1" class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
            <i class="bi bi-trophy text-warning"></i>
            <span>Festival <span class="text-danger">*</span></span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-tags-fill"></i></span>
            <?php echo form_dropdown("cmbFestType1", $fest, '', 'class="form-select border-start-0 py-2" id="cmbFestType1" required'); ?>
          </div>
        </div>
      </div>

      <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
        <button type="submit" name="Report" value="Report" onclick="return callsheet(this)" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm fw-semibold d-inline-flex align-items-center gap-2 w-100 justify-content-center btn-spinner">
          <i class="bi bi-file-earmark-pdf-fill"></i>
          <span class="btn-label">View PDF</span>
          <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
      </div>
    </div>
    <?php echo form_close(); ?>
  </div>
