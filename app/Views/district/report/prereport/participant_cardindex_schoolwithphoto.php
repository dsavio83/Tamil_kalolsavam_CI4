<style>
  @font-face {
    font-family: 'TAU-Pallai';
    src: local('TAU-Pallai');
  }
  .font-tamil {
    font-family: 'TAU-Pallai', system-ui, -apple-system, sans-serif;
    font-size: 14px;
  }
  
  .card-report-box {
    border-radius: 18px;
    border: 1px solid rgba(226, 232, 240, 0.9);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: #ffffff;
  }
  .card-report-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 32px -4px rgba(15, 23, 42, 0.12) !important;
  }

  /* Circular Hero Icon Badges & Keyframe Animations */
  .card-icon-hero {
    width: 44px;
    height: 44px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    border: 1.5px solid rgba(255, 255, 255, 0.35);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    animation: iconCardPulse 3s infinite, iconCardFloat 3.6s ease-in-out infinite;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .card-report-box:hover .card-icon-hero {
    transform: scale(1.14) rotate(8deg);
    background: rgba(255, 255, 255, 0.32);
    border-color: rgba(255, 255, 255, 0.75);
    box-shadow: 0 0 18px rgba(255, 255, 255, 0.55);
  }

  @keyframes iconCardPulse {
    0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(255, 255, 255, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
  }

  @keyframes iconCardFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-3px); }
  }

  /* Specific icon animations on card hover */
  .card-report-box:hover .icon-camera-anim {
    animation: cameraSnap 0.55s ease;
  }
  @keyframes cameraSnap {
    0% { transform: scale(1); }
    30% { transform: scale(0.82); }
    65% { transform: scale(1.22); }
    100% { transform: scale(1); }
  }

  .card-report-box:hover .icon-person-anim {
    animation: personPulse 0.55s ease;
  }
  @keyframes personPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.22) rotate(-6deg); }
    100% { transform: scale(1); }
  }

  .card-report-box:hover .icon-building-anim {
    animation: buildingSway 0.55s ease;
  }
  @keyframes buildingSway {
    0% { transform: rotate(0deg); }
    30% { transform: rotate(-9deg); }
    70% { transform: rotate(9deg); }
    100% { transform: rotate(0deg); }
  }

  /* PDF badge in header */
  .badge-pdf {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }
  .card-report-box:hover .badge-pdf {
    transform: scale(1.06);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15) !important;
  }
  .badge-pdf i {
    display: inline-block;
    transition: transform 0.3s ease;
  }
  .card-report-box:hover .badge-pdf i {
    transform: scale(1.2);
  }

  /* Action buttons & icon animations */
  .btn-card-action {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    letter-spacing: 0.2px;
  }
  .btn-card-action i {
    display: inline-block;
    transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
  }
  .btn-card-action:hover {
    transform: translateY(-2px);
  }
  .btn-card-action:hover i {
    transform: scale(1.25) translateY(-1px);
  }
  .btn-card-action:active {
    transform: translateY(0);
  }
</style>

<div class="pagetitle mb-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
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
    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-1.5">
      <i class="bi bi-award-fill text-warning"></i> Middle Cards
    </span>
  </div>
</div>

<div class="row g-4">
  <!-- Card 1: School-Wise Participant Cards - Middle -->
  <div class="col-12 col-md-6">
    <div class="card border-0 shadow-sm card-report-box h-100 d-flex flex-column overflow-hidden">
      
      <!-- Card Header -->
      <div class="card-header text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);">
        <div class="d-flex align-items-center gap-3">
          <div class="card-icon-hero">
            <i class="bi bi-buildings-fill text-white fs-5 icon-building-anim"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">School Wise Cards (Middle)</h5>
            <small class="text-white-50">Generate batch middle participant cards for school</small>
          </div>
        </div>
        <span class="badge bg-white text-dark fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small badge-pdf">
          <i class="bi bi-printer me-1"></i> PDF
        </span>
      </div>

      <!-- Card Body -->
      <div class="card-body p-4 d-flex flex-column flex-grow-1">
        <?php echo form_open('district/report/prereportpdf/participant_cardindex_school2', array('id' => 'formPartCardMiddleSchool', 'target' => '_blank', 'class' => 'no-global-loader d-flex flex-column flex-grow-1')); ?>
        
        <!-- School Dropdown -->
        <div class="mb-4">
          <label for="txtSchoolCode" class="form-label fw-semibold text-dark small mb-1.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-building text-warning"></i>
            <span>Select School</span>
            <span class="text-danger">*</span>
          </label>
          <select name="txtSchoolCode" id="txtSchoolCode" class="form-select py-2 fs-6 font-tamil" required>
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
          <button type="submit" class="btn btn-warning text-dark w-100 rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2 btn-card-action" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%); border: none;">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate Middle Cards (PDF)</span>
          </button>
        </div>

        <?php echo form_close(); ?>
      </div>

    </div>
  </div>
