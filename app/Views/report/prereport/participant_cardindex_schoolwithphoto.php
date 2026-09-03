<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-card-heading text-warning"></i> Participant Cards - Middle
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Pre-Fest Reports</li>
      <li class="breadcrumb-item active">Participant Card - Middle</li>
    </ol>
  </nav>
</div>

<div class="row g-4">
  <!-- Card 1: School-Wise Participant Cards - Middle -->
  <div class="col-md-12 col-xl-6">
    <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column overflow-hidden">
      
      <!-- Card Header -->
      <div class="card-header text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;">
        <div class="d-flex align-items-center gap-2">
          <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-buildings-fill text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">School Wise Cards (Middle)</h5>
            <small class="text-white-50">Generate batch middle participant cards for school</small>
          </div>
        </div>
        <span class="badge bg-white text-dark fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small">
          <i class="bi bi-printer me-1"></i> PDF
        </span>
      </div>

      <!-- Card Body -->
      <div class="card-body p-4 d-flex flex-column flex-grow-1">
        <?php echo form_open('report/prereportpdf/participant_cardindex_school2', array('id' => 'formPartCardMiddleSchool', 'target' => '_blank', 'class' => 'no-global-loader d-flex flex-column flex-grow-1')); ?>
        
        <!-- School Dropdown -->
        <div class="mb-4">
          <label for="txtSchoolCode" class="form-label fw-semibold text-dark small mb-1.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-building text-warning"></i>
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
            <i class="bi bi-info-circle text-warning me-1"></i> Formats vertical cards designed for lanyard badge holders with full festival details.
          </small>
        </div>

        <!-- Submit Button -->
        <div class="mt-auto pt-3">
          <button type="submit" class="btn btn-warning text-dark w-100 rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2" style="background-color: #f59e0b; border-color: #f59e0b;">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate Middle Cards (PDF)</span>
          </button>
        </div>

        <?php echo form_close(); ?>
      </div>

    </div>
  </div>
