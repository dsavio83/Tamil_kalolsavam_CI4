
<?php
$userType = (int)$this->session->get('USER_TYPE');
$userGroup = $this->session->get('USER_GROUP');
$isDistrictOrWebmaster = ($userType === 2 || $userType === 0 || $userGroup === 'W');
$sessSubDistrict = $this->session->get('SUB_DISTRICT');
$sessSubDistrictName = !empty($sessSubDistrict) ? get_sub_dist_name($sessSubDistrict) : '';
?>
  <!-- Card 2: All Schools / Festival-Wise Team Manager List -->
  <div class="col-md-12 col-xl-6">
    <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column overflow-hidden">
      
      <!-- Card Header -->
      <div class="card-header text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;">
        <div class="d-flex align-items-center gap-2">
          <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-collection-fill text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Sub-District Schools Consolidated List</h5>
            <small class="text-white-50">Single combined PDF for all schools in the sub-district</small>
          </div>
        </div>
        <span class="badge bg-white text-dark fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small">
          <i class="bi bi-printer me-1"></i> PDF
        </span>
      </div>

      <!-- Card Body -->
      <div class="card-body p-4 d-flex flex-column flex-grow-1">
        <?php echo form_open('district/report/prereportpdf/team_manager_all', array('id' => 'team_manager', 'target' => '_blank', 'class' => 'no-global-loader d-flex flex-column flex-grow-1')); ?>
        
        <input type="hidden" name="hidUservalue" id="hidUservalue" value="SHOW" />

        <!-- Sub-District Selection -->
        <div class="mb-3">
          <label for="cmbSubDistrict" class="form-label fw-semibold text-dark small mb-1.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-geo-alt-fill text-primary"></i>
            <span>Sub-District</span>
            <span class="text-danger">*</span>
          </label>
          <select name="cmbSubDistrict" id="cmbSubDistrict" class="form-select py-2 fs-6" required>
            <?php if ($isDistrictOrWebmaster) { ?>
              <option value="ALL">All Sub-Districts (Combined Master)</option>
              <?php if (!empty($subdistricts)) {
                  foreach ($subdistricts as $sd) { ?>
                    <option value="<?php echo htmlspecialchars((string)$sd['sub_district_code']); ?>">
                      <?php echo htmlspecialchars($sd['sub_district_code'] . ' - ' . $sd['sub_district_name']); ?>
                    </option>
                  <?php }
              } ?>
            <?php } else { ?>
              <option value="<?php echo htmlspecialchars((string)$sessSubDistrict); ?>">
                <?php echo htmlspecialchars($sessSubDistrict . ' - ' . $sessSubDistrictName); ?>
              </option>
            <?php } ?>
          </select>
          <small class="text-muted mt-1 d-block" style="font-size: 0.78rem;">
            <i class="bi bi-info-circle text-primary me-1"></i> Generates participant lists for all schools in the selected sub-district into a single PDF.
          </small>
        </div>

        <!-- Festival Category -->
        <div class="mb-4">
          <label for="cmbFestType" class="form-label fw-semibold text-dark small mb-1.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-trophy-fill text-warning"></i>
            <span>Festival Category</span>
            <span class="text-danger">*</span>
          </label>
          <select name="cmbFestType" id="cmbFestType" class="form-select py-2 fs-6" required>
            <option value="ALL">All Festival Categories (Combined Master)</option>
            <?php
            if (!empty($fest)) {
                foreach ($fest as $fId => $fName) {
                    if ($fId === 0 || $fId === '0' || empty($fId) || $fId === 'ALL') continue;
                    echo '<option value="' . htmlspecialchars((string)$fId) . '">' . htmlspecialchars($fName) . '</option>';
                }
            }
            ?>
          </select>
          <small class="text-muted mt-1 d-block" style="font-size: 0.78rem;">
            <i class="bi bi-info-circle text-primary me-1"></i> Generates participant lists for all participating schools in the sub-district.
          </small>
        </div>

        <!-- Submit Button -->
        <div class="mt-auto pt-3">
          <button type="submit" class="btn btn-dark w-100 rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2" style="background-color: #1e293b; border-color: #1e293b;">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate Sub-District Schools Report (PDF)</span>
          </button>
        </div>

        <?php echo form_close(); ?>
      </div>

    </div>
  </div>
</div>