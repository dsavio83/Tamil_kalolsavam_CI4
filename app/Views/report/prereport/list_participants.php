<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-people-fill text-primary"></i> Team Managers Reports
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Pre-Fest Reports</li>
      <li class="breadcrumb-item active">Participants for Team Managers</li>
    </ol>
  </nav>
</div>

<div class="row g-4">
  <!-- Card 1: School-Wise Participant List for Team Managers -->
  <div class="col-md-12 col-xl-6">
    <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column overflow-hidden">
      
      <!-- Card Header -->
      <div class="card-header bg-primary text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-building-check text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">School Wise Participant List</h5>
            <small class="text-white-50">List with chest numbers for school escorting teachers</small>
          </div>
        </div>
        <span class="badge bg-white text-primary fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small">
          <i class="bi bi-printer me-1"></i> PDF
        </span>
      </div>

<?php
$isDistrictAdmin = ((int)$this->session->get('USER_TYPE') === 2 || (int)$this->session->get('USER_TYPE') === 0 || $this->session->get('USER_GROUP') === 'W');
?>
      <!-- Card Body -->
      <div class="card-body p-4 d-flex flex-column flex-grow-1">
        <?php echo form_open('report/prereportpdf/list_participants', array('id' => 'formListPartSchool', 'target' => '_blank', 'class' => 'no-global-loader d-flex flex-column flex-grow-1')); ?>
        
        <!-- Sub-District Filter (District / Webmaster) -->
        <?php if ($isDistrictAdmin && !empty($subdistricts)) { ?>
        <div class="mb-3">
          <label for="cmbSubDistrictFilterSchool" class="form-label fw-semibold text-dark small mb-1.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-geo-alt-fill text-primary"></i>
            <span>Filter by Sub-District</span>
          </label>
          <select id="cmbSubDistrictFilterSchool" class="form-select py-2 fs-6">
            <option value="ALL">-- All Sub-Districts --</option>
            <?php foreach ($subdistricts as $sd) { ?>
              <option value="<?php echo htmlspecialchars((string)$sd['sub_district_code']); ?>">
                <?php echo htmlspecialchars($sd['sub_district_code'] . ' - ' . $sd['sub_district_name']); ?>
              </option>
            <?php } ?>
          </select>
          <small class="text-muted mt-1 d-block" style="font-size: 0.78rem;">
            <i class="bi bi-funnel text-primary me-1"></i> Selecting a sub-district filters the participating schools below.
          </small>
        </div>
        <?php } ?>

        <!-- School Dropdown -->
        <div class="mb-3">
          <label for="txtSchoolCode" class="form-label fw-semibold text-dark small mb-1.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-buildings-fill text-primary"></i>
            <span>Select School</span>
            <span class="text-danger">*</span>
          </label>
          <select name="txtSchoolCode" id="txtSchoolCode" class="form-select py-2 fs-6" required>
            <option value="" disabled selected>-- Select Participating School --</option>
            <?php
            if (!empty($retdat)) {
                foreach ($retdat as $s) {
                    $sdCode = $s['sub_district_code'] ?? '';
                    $sdName = !empty($s['sub_district_name']) ? ' (' . $s['sub_district_name'] . ')' : '';
                    echo '<option value="' . htmlspecialchars((string)$s['school_code']) . '" data-subdistrict="' . htmlspecialchars((string)$sdCode) . '">' . htmlspecialchars($s['school_code'] . ' - ' . $s['school_name'] . $sdName) . '</option>';
                }
            }
            ?>
          </select>
        </div>

        <!-- Festival Category -->
        <div class="mb-4">
          <label for="Festpart" class="form-label fw-semibold text-dark small mb-1.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-trophy-fill text-warning"></i>
            <span>Festival Category</span>
            <span class="text-danger">*</span>
          </label>
          <select name="Festpart" id="Festpart" class="form-select py-2 fs-6" required>
            <option value="0">All Festival Categories</option>
            <?php
            if (!empty($pfest)) {
                foreach ($pfest as $fId => $fName) {
                    if ($fId === 0 || $fId === '0' || empty($fId)) continue;
                    echo '<option value="' . htmlspecialchars((string)$fId) . '">' . htmlspecialchars($fName) . '</option>';
                }
            }
            ?>
          </select>
          <small class="text-muted mt-1 d-block" style="font-size: 0.78rem;">
            <i class="bi bi-info-circle text-primary me-1"></i> Generates individual school participant list grouped by event.
          </small>
        </div>

        <!-- Submit Button -->
        <div class="mt-auto pt-3">
          <button type="submit" class="btn btn-primary w-100 rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate School Report (PDF)</span>
          </button>
        </div>

        <input type="hidden" name="hidUserId" value="<?php echo htmlspecialchars((string)$this->session->get('USERID')); ?>" />
        <?php echo form_close(); ?>
      </div>

    </div>
  </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var subDistFilter = document.getElementById('cmbSubDistrictFilterSchool');
    var schoolSelect = document.getElementById('txtSchoolCode');
    if (!subDistFilter || !schoolSelect) return;

    var allOptions = Array.from(schoolSelect.querySelectorAll('option:not([value=""])'));

    subDistFilter.addEventListener('change', function() {
        var selectedSub = this.value;
        var currentVal = schoolSelect.value;
        var hasValidSelection = false;

        schoolSelect.innerHTML = '<option value="" disabled selected>-- Select Participating School --</option>';

        allOptions.forEach(function(opt) {
            var optSub = opt.getAttribute('data-subdistrict');
            if (selectedSub === 'ALL' || selectedSub === '' || optSub === selectedSub) {
                var clone = opt.cloneNode(true);
                if (clone.value === currentVal) {
                    clone.selected = true;
                    hasValidSelection = true;
                }
                schoolSelect.appendChild(clone);
            }
        });

        if (!hasValidSelection) {
            schoolSelect.value = '';
        }
    });
});
</script>
