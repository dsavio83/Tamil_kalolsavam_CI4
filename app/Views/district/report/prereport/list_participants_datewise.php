  <!-- Card 2: Date Wise Participants Report -->
  <div class="col-md-12 col-xl-4">
    <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column overflow-hidden">
      
      <!-- Card Header -->
      <div class="card-header text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;">
        <div class="d-flex align-items-center gap-2">
          <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-calendar-event-fill text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Date Wise Participants</h5>
            <small class="text-white-50">Filter by category & scheduled date</small>
          </div>
        </div>
        <span class="badge bg-white text-dark fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small">
          <i class="bi bi-printer me-1"></i> PDF
        </span>
      </div>

      <!-- Card Body -->
      <div class="card-body p-4 d-flex flex-column flex-grow-1">
        <?php echo form_open('district/report/prereportpdf/list_participants_datewise', array('id' => 'list_participants_datewise', 'target' => '_blank', 'class' => 'no-global-loader d-flex flex-column flex-grow-1')); ?>
        
        <!-- Festival Category -->
        <div class="mb-3">
          <label for="cmbFestType1" class="form-label fw-semibold text-dark small mb-1.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-trophy-fill text-warning"></i>
            <span>Festival Category</span>
            <span class="text-danger">*</span>
          </label>
          <select name="cmbFestType1" id="cmbFestType1" class="form-select py-2 fs-6" required>
            <option value="ALL">All Categories (Combined)</option>
            <?php
            if (!empty($fest)) {
                foreach ($fest as $fId => $fName) {
                    if ($fId === 0 || $fId === '0' || empty($fId) || $fId === 'ALL') continue;
                    echo '<option value="' . htmlspecialchars((string)$fId) . '">' . htmlspecialchars($fName) . '</option>';
                }
            }
            ?>
          </select>
        </div>

        <!-- Festival Date -->
        <div class="mb-4">
          <label for="txtDate" class="form-label fw-semibold text-dark small mb-1.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-calendar3 text-primary"></i>
            <span>Festival Date</span>
            <span class="text-danger">*</span>
          </label>
          <select name="txtDate" id="txtDate" class="form-select py-2 fs-6" required>
            <?php
            if (!empty($date_array)) {
                foreach ($date_array as $dVal => $dLabel) {
                    echo '<option value="' . htmlspecialchars((string)$dVal) . '">' . htmlspecialchars($dLabel) . '</option>';
                }
            }
            ?>
          </select>
          <small class="text-muted mt-1 d-block" style="font-size: 0.78rem;">
            <i class="bi bi-info-circle text-primary me-1"></i> Choose "All Dates" for the complete schedule.
          </small>
        </div>

        <!-- Submit Button -->
        <div class="mt-auto pt-3">
          <button type="submit" class="btn btn-dark w-100 rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2" style="background-color: #1e293b; border-color: #1e293b;">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate Date Report (PDF)</span>
          </button>
        </div>

        <input type="hidden" name="hidUserId" id="hidUserIdDate" value="<?php echo htmlspecialchars((string)$this->session->get('USERID')); ?>" />
        
        <?php echo form_close(); ?>

      </div>

    </div>
  </div>
