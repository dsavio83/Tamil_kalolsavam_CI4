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

  .btn-card-action {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    letter-spacing: 0.2px;
  }
  .btn-card-action:hover {
    transform: translateY(-2px);
  }
</style>

<div class="pagetitle mb-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
        <i class="bi bi-person-badge-fill text-primary"></i> Participant Cards (without Photo)
      </h1>
      <nav>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>district/reports" class="text-decoration-none">District Reports</a></li>
          <li class="breadcrumb-item active">Participant Cards</li>
        </ol>
      </nav>
    </div>
    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-1.5">
      <i class="bi bi-diagram-3-fill"></i> District &amp; Sub-District Scope
    </span>
  </div>
</div>

<div class="row g-4">
  <!-- Card 1: School & Sub-District Wise Batch Download -->
  <div class="col-6">
    <div class="card border-0 shadow-sm card-report-box h-100 d-flex flex-column overflow-hidden">
      
      <!-- Card Header -->
      <div class="card-header text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
        <div class="d-flex align-items-center gap-3">
          <div class="card-icon-hero">
            <i class="bi bi-buildings-fill text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Sub-District &amp; School Wise Cards</h5>
            <small class="text-white-50">Generate single school or all schools in sub-district</small>
          </div>
        </div>
        <span class="badge bg-white text-primary fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small">
          <i class="bi bi-printer me-1"></i> PDF
        </span>
      </div>

      <!-- Card Body -->
      <div class="card-body p-4 d-flex flex-column flex-grow-1">
        <?php echo form_open('report/prereportpdf/participant_cardindex', array('id' => 'formPartCardSchool', 'target' => '_blank', 'class' => 'no-global-loader d-flex flex-column flex-grow-1')); ?>
        
        <!-- Sub-District Dropdown -->
        <div class="mb-3">
          <label for="cboSubDistCode" class="form-label fw-semibold text-dark small mb-1.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-diagram-3 text-primary"></i>
            <span>Select Sub-District</span>
            <span class="text-danger">*</span>
          </label>
          <select name="cboSubDistCode" id="cboSubDistCode" class="form-select py-2 fs-6 font-tamil" required>
            <option value="" disabled selected>-- Select Sub-District --</option>
            <?php
            if (!empty($sub_dist_array)) {
                foreach ($sub_dist_array as $code => $name) {
                    if (empty($code)) continue;
                    echo '<option value="' . htmlspecialchars((string)$code) . '">' . htmlspecialchars((string)$name) . '</option>';
                }
            }
            ?>
          </select>
        </div>

        <!-- School Dropdown -->
        <div class="mb-3">
          <label for="txtSchoolCode" class="form-label fw-semibold text-dark small mb-1.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-building text-primary"></i>
            <span>Select School</span>
            <span class="text-danger">*</span>
          </label>
          <select name="txtSchoolCode" id="txtSchoolCode" class="form-select py-2 fs-6 font-tamil" required>
            <option value="" disabled selected>-- First Select Sub-District --</option>
          </select>
          <small class="text-muted mt-1.5 d-block" style="font-size: 0.78rem;">
            <i class="bi bi-info-circle text-primary me-1"></i> Choose a specific school or choose <strong>ALL SCHOOLS</strong> to download the entire sub-district.
          </small>
        </div>

        <!-- Single-click All Schools Notice -->
        <div class="alert alert-light border border-primary border-opacity-25 rounded-3 py-2 px-3 mb-4" style="font-size: 0.8rem; background-color: #f8fafc;">
          <i class="bi bi-check2-circle text-success me-1"></i>
          <strong>Single-Click Batch Download:</strong> All schools will be merged into one PDF. Each school's cards will start on a separate page without mixing.
        </div>

        <!-- Action Buttons -->
        <div class="mt-auto pt-2 d-flex flex-column gap-2">
          <!-- Button 1: Download Selected School Cards -->
          <button type="submit" id="btnDownloadSchool" class="btn btn-primary w-100 rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2 btn-card-action" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); border: none;">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate Selected School Cards (PDF)</span>
          </button>

          <!-- Button 2: Download All Schools in Sub-District in One Click -->
          <button type="button" id="btnDownloadAllSchools" class="btn btn-success w-100 rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2 btn-card-action" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border: none;" disabled>
            <i class="bi bi-cloud-arrow-down-fill"></i>
            <span>Download All Schools in Sub-District (1-Click PDF)</span>
          </button>
        </div>

        <?php echo form_close(); ?>
      </div>

    </div>
  </div>

  <!-- Card 2: Single Participant Card by Register Number -->
  <div class="col-6">
    <div class="card border-0 shadow-sm card-report-box h-100 d-flex flex-column overflow-hidden">
      
      <!-- Card Header -->
      <div class="card-header text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);">
        <div class="d-flex align-items-center gap-3">
          <div class="card-icon-hero">
            <i class="bi bi-person-badge text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Single Card (Reg. No.)</h5>
            <small class="text-white-50">Generate participant card for an individual student</small>
          </div>
        </div>
        <span class="badge bg-white text-dark fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small">
          <i class="bi bi-printer me-1"></i> PDF
        </span>
      </div>

      <!-- Card Body -->
      <div class="card-body p-4 d-flex flex-column flex-grow-1">
        <?php echo form_open('report/prereportpdf/participant_regno', array('id' => 'part_reg', 'target' => '_blank', 'class' => 'no-global-loader d-flex flex-column flex-grow-1')); ?>
        
        <!-- Register Number Input -->
        <div class="mb-4">
          <label for="regno" class="form-label fw-semibold text-dark small mb-1.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-123 text-primary"></i>
            <span>Register Number</span>
            <span class="text-danger">*</span>
          </label>
          <input type="text" name="regno" id="regno" class="form-control py-2 fs-6 font-monospace" placeholder="Enter student register number (e.g. 101)" onkeypress="javascript:return numbersonly(this, event, false);" required />
          <small class="text-muted mt-1.5 d-block" style="font-size: 0.78rem;">
            <i class="bi bi-info-circle text-primary me-1"></i> Prints identity card showing scheduled events, dates, and stage timings.
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

<script>
document.addEventListener('DOMContentLoaded', function () {
  const cboSubDist = document.getElementById('cboSubDistCode');
  const txtSchool = document.getElementById('txtSchoolCode');
  const btnAllSchools = document.getElementById('btnDownloadAllSchools');
  const formPart = document.getElementById('formPartCardSchool');

  cboSubDist.addEventListener('change', function () {
    const subDistCode = this.value;
    if (!subDistCode) {
      txtSchool.innerHTML = '<option value="" disabled selected>-- First Select Sub-District --</option>';
      btnAllSchools.disabled = true;
      return;
    }

    txtSchool.innerHTML = '<option value="" disabled selected>Loading schools...</option>';
    btnAllSchools.disabled = false;

    // Fetch schools in selected subdistrict
    const fetchUrl = '<?php echo base_url("district/reports/ajax_get_schools_by_subdistrict"); ?>?sub_district_code=' + encodeURIComponent(subDistCode);
    
    fetch(fetchUrl)
      .then(response => response.json())
      .then(data => {
        let html = '<option value="ALL" style="font-weight:bold; color:#059669;">-- ALL SCHOOLS IN THIS SUB-DISTRICT --</option>';
        if (Array.isArray(data) && data.length > 0) {
          data.forEach(function (sch) {
            html += '<option value="' + sch.school_code + '">' + sch.school_code + ' - ' + sch.school_name + '</option>';
          });
        } else {
          html += '<option value="" disabled>No registered schools found in this sub-district</option>';
        }
        txtSchool.innerHTML = html;
        txtSchool.selectedIndex = 0;
      })
      .catch(err => {
        console.error('Error fetching schools:', err);
        txtSchool.innerHTML = '<option value="ALL">-- ALL SCHOOLS IN THIS SUB-DISTRICT --</option>';
      });
  });

  // Dedicated "Download All Schools" Click Handler
  btnAllSchools.addEventListener('click', function () {
    const subDistCode = cboSubDist.value;
    if (!subDistCode) {
      alert('Please select a Sub-District first.');
      cboSubDist.focus();
      return;
    }
    txtSchool.value = 'ALL';
    formPart.submit();
  });
});
</script>
