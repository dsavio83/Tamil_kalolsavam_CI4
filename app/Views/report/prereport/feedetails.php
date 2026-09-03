<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-cash-coin text-success"></i> School Fee Details Reports
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Pre-Fest Reports</li>
      <li class="breadcrumb-item active">Fee Details of School</li>
    </ol>
  </nav>
</div>

<div class="row g-4">
  <!-- Card 1: School Wise Fee Statement -->
  <div class="col-md-12 col-xl-6">
    <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column overflow-hidden">
      
      <!-- Card Header -->
      <div class="card-header bg-success text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-receipt-cutoff text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">School Fee Statement</h5>
            <small class="text-white-50">Registration & affiliation fees for an individual school</small>
          </div>
        </div>
        <span class="badge bg-white text-success fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small">
          <i class="bi bi-printer me-1"></i> PDF
        </span>
      </div>

      <!-- Card Body -->
      <div class="card-body p-4 d-flex flex-column flex-grow-1">
        <?php echo form_open('report/prereportpdf/feedetails', array('id' => 'formFeeDetails', 'target' => '_blank', 'class' => 'no-global-loader d-flex flex-column flex-grow-1')); ?>
        
        <!-- School Dropdown -->
        <div class="mb-4">
          <label for="txtSchoolCode" class="form-label fw-semibold text-dark small mb-1.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-buildings-fill text-success"></i>
            <span>Select School</span>
            <span class="text-danger">*</span>
          </label>
          <select name="txtSchoolCode" id="txtSchoolCode" class="form-select py-2 fs-6" required>
            <option value="" disabled selected>-- Select Participating School --</option>
            <?php
            if (!empty($retdat)) {
                foreach ($retdat as $s) {
                    echo '<option value="' . htmlspecialchars((string)$s['school_code']) . '">' . htmlspecialchars($s['school_code'] . ' - ' . $s['school_name']) . '</option>';
                }
            }
            ?>
          </select>
          <small class="text-muted mt-1.5 d-block" style="font-size: 0.78rem;">
            <i class="bi bi-info-circle text-success me-1"></i> Generates printable fee challan/statement for UP, HS, HSS & VHSS sections.
          </small>
        </div>

        <!-- Submit Button -->
        <div class="mt-auto pt-3">
          <button type="submit" class="btn btn-success w-100 rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate School Fee Statement (PDF)</span>
          </button>
        </div>

        <?php echo form_close(); ?>
      </div>

    </div>
  </div>

  <!-- Card 2: All Schools Consolidated Fee Statement -->
  <div class="col-md-12 col-xl-6">
    <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column overflow-hidden">
      
      <!-- Card Header -->
      <div class="card-header text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%) !important;">
        <div class="d-flex align-items-center gap-2">
          <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-wallet2 text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">All Schools Fee Summary</h5>
            <small class="text-white-50">Sub-district wide master fee collection register</small>
          </div>
        </div>
        <span class="badge bg-white text-dark fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small">
          <i class="bi bi-printer me-1"></i> PDF
        </span>
      </div>

      <!-- Card Body -->
      <div class="card-body p-4 d-flex flex-column flex-grow-1">
        <div class="mb-4">
          <p class="text-secondary small mb-3">
            Generate the consolidated registration and affiliation fee statement for all registered institutions in the sub-district across all education sections.
          </p>
          <div class="p-3 bg-light rounded-3 border">
            <div class="d-flex align-items-center gap-2 mb-2">
              <i class="bi bi-check2-circle text-teal fs-5" style="color: #0d9488;"></i>
              <span class="small fw-semibold text-dark">Includes affiliation and participant fees</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-check2-circle text-teal fs-5" style="color: #0d9488;"></i>
              <span class="small fw-semibold text-dark">Categorized by UP, HS, HSS and VHSS</span>
            </div>
          </div>
        </div>

        <!-- Direct PDF Generation Action -->
        <div class="mt-auto pt-3">
          <a href="<?php echo base_url(); ?>report/prereportpdf/all_feedet_report_all" target="_blank" class="btn btn-teal w-100 rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2 text-white" style="background-color: #0d9488; border-color: #0d9488;">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate Consolidated Fee Register (PDF)</span>
          </a>
        </div>
      </div>

    </div>
  </div>
</div>