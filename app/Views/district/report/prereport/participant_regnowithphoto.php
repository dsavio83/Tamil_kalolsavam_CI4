  <!-- Card 2: Single Middle Card by Register Number -->
  <div class="col-12 col-md-6">
    <div class="card border-0 shadow-sm card-report-box h-100 d-flex flex-column overflow-hidden">
      
      <!-- Card Header -->
      <div class="card-header text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);">
        <div class="d-flex align-items-center gap-3">
          <div class="card-icon-hero">
            <i class="bi bi-person-badge text-white fs-5 icon-person-anim"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Single Middle Card (Reg. No.)</h5>
            <small class="text-white-50">Generate middle participant card by register number</small>
          </div>
        </div>
        <span class="badge bg-white text-dark fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small badge-pdf">
          <i class="bi bi-printer me-1"></i> PDF
        </span>
      </div>

      <!-- Card Body -->
      <div class="card-body p-4 d-flex flex-column flex-grow-1">
        <?php echo form_open('district/report/prereportpdf/participant_regnowithphoto', array('id' => 'part_reg_middle', 'target' => '_blank', 'class' => 'no-global-loader d-flex flex-column flex-grow-1')); ?>
        
        <!-- Register Number Input -->
        <div class="mb-4">
          <label for="regno_middle" class="form-label fw-semibold text-dark small mb-1.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-123 text-primary"></i>
            <span>Register Number</span>
            <span class="text-danger">*</span>
          </label>
          <input type="text" name="regno" id="regno_middle" class="form-control py-2 fs-6 font-monospace" placeholder="Enter student register number (e.g. 101)" onkeypress="javascript:return numbersonly(this, event, false);" required />
          <small class="text-muted mt-1.5 d-block" style="font-size: 0.78rem;">
            <i class="bi bi-info-circle text-primary me-1"></i> Prints single middle participant identity card with venue, date, and schedule.
          </small>
        </div>

        <!-- Submit Button -->
        <div class="mt-auto pt-3">
          <button type="submit" class="btn btn-dark w-100 rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2 btn-card-action" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: none;">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate Single Card (PDF)</span>
          </button>
        </div>

        <?php echo form_close(); ?>
      </div>

    </div>
  </div>
</div>
