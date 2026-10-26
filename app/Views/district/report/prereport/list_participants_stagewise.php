  <!-- Card 3: Stage & Date Wise Participants Report -->
  <div class="col-md-12 col-xl-4">
    <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column overflow-hidden">
      
      <!-- Card Header -->
      <div class="card-header text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%) !important;">
        <div class="d-flex align-items-center gap-2">
          <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-geo-alt-fill text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Stage &amp; Date Wise</h5>
            <small class="text-white-50">Filter by stage &amp; competition date</small>
          </div>
        </div>
        <span class="badge bg-white text-teal fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small" style="color: #0d9488 !important;">
          <i class="bi bi-printer me-1"></i> PDF
        </span>
      </div>

      <!-- Card Body -->
      <div class="card-body p-4 d-flex flex-column flex-grow-1">
        <?php echo form_open('district/report/prereportpdf/list_participants_stagewise', array('id' => 'list_participants_stagewise', 'target' => '_blank', 'class' => 'no-global-loader d-flex flex-column flex-grow-1')); ?>
        
        <!-- Stage Name -->
        <div class="mb-3">
          <label for="cmbstage" class="form-label fw-semibold text-dark small mb-1.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-pin-map-fill text-danger"></i>
            <span>Stage / Venue Name</span>
            <span class="text-danger">*</span>
          </label>
          <select name="cmbstage" id="cmbstage" class="form-select py-2 fs-6" required>
            <option value="All">All Stages</option>
            <?php
            if (!empty($stage)) {
                foreach ($stage as $sId => $sName) {
                    if ($sId === 'All' || empty($sId)) continue;
                    echo '<option value="' . htmlspecialchars((string)$sId) . '">' . htmlspecialchars($sName) . '</option>';
                }
            }
            ?>
          </select>
        </div>

        <!-- Festival Date -->
        <div class="mb-4">
          <label for="txtDateStage" class="form-label fw-semibold text-dark small mb-1.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-calendar3 text-primary"></i>
            <span>Festival Date</span>
            <span class="text-danger">*</span>
          </label>
          <select name="txtDate" id="txtDateStage" class="form-select py-2 fs-6" required>
            <?php
            if (!empty($date_array)) {
                foreach ($date_array as $dVal => $dLabel) {
                    echo '<option value="' . htmlspecialchars((string)$dVal) . '">' . htmlspecialchars($dLabel) . '</option>';
                }
            }
            ?>
          </select>
          <small class="text-muted mt-1 d-block" style="font-size: 0.78rem;">
            <i class="bi bi-info-circle text-primary me-1"></i> Choose "All Dates" for the full stage lineup.
          </small>
        </div>

        <!-- Submit Button -->
        <div class="mt-auto pt-3">
          <button type="submit" class="btn text-white w-100 rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2" style="background-color: #0d9488; border-color: #0d9488;">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate Stage Report (PDF)</span>
          </button>
        </div>

        <input type="hidden" name="hidUserId" id="hidUserIdStage" value="<?php echo htmlspecialchars((string)$this->session->get('USERID')); ?>" />
        
        <?php echo form_close(); ?>

      </div>

    </div>
  </div>
</div>

