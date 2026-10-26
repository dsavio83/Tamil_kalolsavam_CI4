<style>
.export-card-header {
  padding: 1rem 1.25rem !important;
}
.export-card-body {
  padding: 1.25rem 1.25rem !important;
}
.export-info-box {
  padding: 0.85rem 1.1rem !important;
}
.export-info-row {
  padding: 0.35rem 0 !important;
}
.export-guidelines-box {
  padding: 1.25rem 1.5rem !important;
  background-color: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
}
.stat-card-icon {
  width: 48px;
  height: 48px;
  min-width: 48px;
  padding: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>

<div class="pagetitle mb-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
        <span class="rounded-3 bg-primary-subtle text-primary p-2 d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 42px; height: 42px;">
          <i class="bi bi-cloud-arrow-up-fill fs-5"></i>
        </span>
        <span>Export Result Data for District Kalolsavam</span>
      </h1>
      <nav>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
          <li class="breadcrumb-item">Export</li>
          <li class="breadcrumb-item active">Export Result Data</li>
        </ol>
      </nav>
    </div>
  </div>
</div>

<!-- Hero Banner: Sub-District & Revenue District Info -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);">
  <div class="card-body p-4 text-white">
    <div class="row align-items-center g-3">
      <div class="col-12 col-md-7">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 58px; height: 58px; min-width: 58px;">
            <i class="bi bi-database-check fs-2 text-white"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
              <span class="badge bg-white text-dark fw-semibold shadow-sm rounded-pill px-3 py-1 small font-monospace">Sub-District: <?php echo esc($sub_dist_code ?? session()->get('SUB_DISTRICT')); ?></span>
              <span class="badge bg-warning text-dark fw-bold shadow-sm rounded-pill px-3 py-1 small font-monospace">District: <?php echo esc($dist_code ?? session()->get('DISTRICT')); ?></span>
            </div>
            <h4 class="fw-bold mb-0 text-white"><?php echo esc($sub_dist_name ?? 'Sub-District'); ?> &rarr; <?php echo esc($dist_name ?? 'District'); ?></h4>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-5 text-md-end">
        <span class="badge bg-white text-dark fw-semibold shadow-sm px-3 py-2 rounded-pill small font-monospace">
          <i class="bi bi-shield-lock-fill me-1 text-primary"></i> MD5 Cryptographic Integrity
        </span>
      </div>
    </div>
  </div>
</div>

<!-- 4 Key Metric Stat Cards -->
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 bg-warning-subtle text-warning stat-card-icon">
          <i class="bi bi-trophy-fill fs-4"></i>
        </div>
        <div>
          <div class="small text-muted fw-semibold">Grade 'A' Qualifiers</div>
          <div class="fs-4 fw-bold text-dark font-monospace"><?php echo (int)($eligible_count ?? 0); ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-6 col-lg-3">
    <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 bg-primary-subtle text-primary stat-card-icon">
          <i class="bi bi-building fs-4"></i>
        </div>
        <div>
          <div class="small text-muted fw-semibold">Active Schools</div>
          <div class="fs-4 fw-bold text-primary font-monospace"><?php echo (int)($total_schools ?? 0); ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-6 col-lg-3">
    <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 bg-info-subtle text-info stat-card-icon">
          <i class="bi bi-images fs-4"></i>
        </div>
        <div>
          <div class="small text-muted fw-semibold">Participant Photos</div>
          <div class="fs-4 fw-bold text-info font-monospace"><?php echo (int)($photo_count ?? 0); ?></div>
          <?php if (isset($qualifier_photo_count, $total_qualifiers) && $total_qualifiers > 0): ?>
            <div class="text-muted mt-1" style="font-size: 0.75rem;">Qualifiers: <strong><?php echo (int)$qualifier_photo_count; ?> / <?php echo (int)$total_qualifiers; ?></strong></div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-6 col-lg-3">
    <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 bg-success-subtle text-success stat-card-icon">
          <i class="bi bi-patch-check-fill fs-4"></i>
        </div>
        <div>
          <div class="small text-muted fw-semibold">Export Format</div>
          <div class="fs-6 fw-bold text-success font-monospace">CSV &amp; ZIP (Sync)</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Primary Action Packages Row -->
<?php echo form_open(base_url('export/export_district_data'), array('id' => 'export_dist_data')); ?>
<input type="hidden" name="hidExport" id="hidExport" value="" />

<div class="row g-4 mb-4">
  <!-- Package 1: District Official Dataset (CSV) -->
  <div class="col-12 col-md-6 col-xl-3">
    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="border: 2px solid #10b981 !important;">
      <div class="card-header bg-success bg-opacity-10 border-bottom export-card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center shadow-xs" style="width: 38px; height: 38px; min-width: 38px;">
            <i class="bi bi-table fs-5"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-0 text-dark">District Official CSV</h6>
            <small class="text-success fw-semibold">First with A Grade Only</small>
          </div>
        </div>
        <span class="badge bg-success text-white rounded-pill px-2.5 py-1 small font-monospace">OFFICIAL</span>
      </div>

      <div class="card-body export-card-body d-flex flex-column justify-content-between">
        <div>
          <p class="text-muted small mb-3" style="line-height: 1.55;">
            Exports structured official data for District Kalolsavam: School Profiles, School Infrastructure, First with Grade 'A' Qualifying Participants, and their Event Registrations.
          </p>

          <div class="bg-light export-info-box rounded-3 border mb-3 small">
            <div class="d-flex align-items-center justify-content-between export-info-row">
              <span class="text-muted">Filter:</span>
              <span class="badge bg-success-subtle text-success fw-bold">First with A Grade</span>
            </div>
            <div class="d-flex align-items-center justify-content-between export-info-row">
              <span class="text-muted">Data Scope:</span>
              <span class="fw-bold text-dark font-monospace">Schools, Students &amp; Events</span>
            </div>
            <div class="d-flex align-items-center justify-content-between export-info-row">
              <span class="text-muted">Target:</span>
              <span class="text-primary fw-semibold">District Kalolsavam</span>
            </div>
          </div>
        </div>

        <div class="pt-3 border-top">
          <button type="button" id="btnExportData" onclick="triggerDistrictExport();" class="btn btn-success w-100 rounded-pill py-2 px-3 fw-semibold shadow-sm d-inline-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-download fs-5"></i>
            <span>Export Official Dataset</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Package 2: Standard Flat Winners Dataset (CSV) -->
  <div class="col-12 col-md-6 col-xl-3">
    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
      <div class="card-header bg-white border-bottom export-card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; min-width: 38px;">
            <i class="bi bi-filetype-csv fs-5"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-0 text-dark">Winners CSV (V1)</h6>
            <small class="text-muted">Standardized flat format</small>
          </div>
        </div>
        <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 small font-monospace">V1 SPEC</span>
      </div>

      <div class="card-body export-card-body d-flex flex-column justify-content-between">
        <div>
          <p class="text-muted small mb-3" style="line-height: 1.55;">
            Compiles all qualifying First Rank with Grade 'A' students (Classes 5-12) into a single flat CSV with cryptographic row HMAC checksums.
          </p>

          <div class="bg-light export-info-box rounded-3 border mb-3 small">
            <div class="d-flex align-items-center justify-content-between export-info-row">
              <span class="text-muted">Format:</span>
              <span class="font-monospace fw-bold text-dark">TK25-CSV-V1</span>
            </div>
            <div class="d-flex align-items-center justify-content-between export-info-row">
              <span class="text-muted">Qualification:</span>
              <span class="badge bg-success-subtle text-success">Rank 1 + Grade A</span>
            </div>
            <div class="d-flex align-items-center justify-content-between export-info-row">
              <span class="text-muted">LP Section:</span>
              <span class="badge bg-danger-subtle text-danger">Excluded</span>
            </div>
          </div>
        </div>

        <div class="pt-3 border-top">
          <a href="<?php echo base_url('export/export_district_winners_csv'); ?>" class="btn btn-outline-primary w-100 rounded-pill py-2 px-3 fw-semibold shadow-xs d-inline-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-download fs-5"></i>
            <span>Download CSV (V1)</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Package 3: Participant Photos Archive (ZIP) -->
  <div class="col-12 col-md-6 col-xl-3">
    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
      <div class="card-header bg-white border-bottom export-card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="rounded-circle bg-info-subtle text-info p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; min-width: 38px;">
            <i class="bi bi-file-earmark-zip-fill fs-5"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-0 text-dark">Winners Photos (ZIP)</h6>
            <small class="text-muted">Rank 1 Grade A photos</small>
          </div>
        </div>
        <span class="badge bg-info-subtle text-info rounded-pill px-2.5 py-1 small font-monospace">PHOTOS</span>
      </div>

      <div class="card-body export-card-body d-flex flex-column justify-content-between">
        <div>
          <p class="text-muted small mb-3" style="line-height: 1.55;">
            Bundles photographs of only those student participants who qualified (First with A Grade) for the District competition into an indexed archive.
          </p>

          <div class="bg-light export-info-box rounded-3 border mb-3 small">
            <div class="d-flex align-items-center justify-content-between export-info-row">
              <span class="text-muted">Naming:</span>
              <span class="font-monospace fw-bold text-dark">{sch}_{admn}.jpg</span>
            </div>
            <div class="d-flex align-items-center justify-content-between export-info-row">
              <span class="text-muted">Filter:</span>
              <span class="badge bg-info-subtle text-info font-monospace">Qualifiers Only</span>
            </div>
            <div class="d-flex align-items-center justify-content-between export-info-row">
              <span class="text-muted">Structure:</span>
              <span class="badge bg-primary-subtle text-primary">photos/</span>
            </div>
          </div>
        </div>

        <div class="pt-3 border-top">
          <a href="<?php echo base_url('export/export_winner_photos_zip'); ?>" class="btn btn-outline-info text-info-emphasis w-100 rounded-pill py-2 px-3 fw-semibold shadow-xs d-inline-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-file-zip fs-5"></i>
            <span>Download Photos ZIP</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Package 4: Complete Unified Winner Package (ZIP) -->
  <div class="col-12 col-md-6 col-xl-3">
    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden border-primary" style="border: 2px solid #2563eb !important;">
      <div class="card-header bg-primary bg-opacity-10 border-bottom export-card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="rounded-circle bg-primary text-white p-2 d-flex align-items-center justify-content-center shadow-xs" style="width: 38px; height: 38px; min-width: 38px;">
            <i class="bi bi-box-seam-fill fs-5"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-0 text-dark">Unified Winner Bundle</h6>
            <small class="text-primary fw-semibold">CSV + Photos + Schools</small>
          </div>
        </div>
        <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 small font-monospace">ALL-IN-ONE</span>
      </div>

      <div class="card-body export-card-body d-flex flex-column justify-content-between">
        <div>
          <p class="text-muted small mb-3" style="line-height: 1.55;">
            Packs <code>winners.csv</code>, qualifying <code>schools.json</code>, all verified photo files, and <code>manifest.json</code> into a single secured bundle.
          </p>

          <div class="bg-light export-info-box rounded-3 border mb-3 small">
            <div class="d-flex align-items-center justify-content-between export-info-row">
              <span class="text-muted">Contents:</span>
              <span class="font-monospace fw-bold text-dark">CSV+Schools+Photos</span>
            </div>
            <div class="d-flex align-items-center justify-content-between export-info-row">
              <span class="text-muted">Verification:</span>
              <span class="badge bg-success-subtle text-success">Automated Matching</span>
            </div>
            <div class="d-flex align-items-center justify-content-between export-info-row">
              <span class="text-muted">Integrity:</span>
              <span class="badge bg-primary-subtle text-primary">Cryptographic</span>
            </div>
          </div>
        </div>

        <div class="pt-3 border-top">
          <a href="<?php echo base_url('export/export_winner_package'); ?>" class="btn btn-primary w-100 rounded-pill py-2 px-3 fw-semibold shadow-sm d-inline-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-download fs-5"></i>
            <span>Download Unified Bundle</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Security & Compliance Guidelines -->
<div class="card border-0 shadow-sm rounded-4 export-guidelines-box mb-4">
  <div class="d-flex align-items-start gap-3">
    <div class="rounded-circle bg-secondary-subtle text-secondary p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; min-width: 40px;">
      <i class="bi bi-info-circle-fill fs-5"></i>
    </div>
    <div>
      <h6 class="fw-bold mb-2 text-dark">Data Verification Guidelines for District Upload</h6>
      <ul class="small text-muted mb-0 ps-3" style="line-height: 1.7;">
        <li class="mb-1">Ensure all competition result entries are completed and officially published before performing the final export.</li>
        <li class="mb-1">Do not rename the generated CSV file (<code><?php echo esc($filename_csv ?? 'dist_data.csv'); ?></code>), as the District Kalolsavam import wizard validates the sub-district prefix.</li>
        <li>The accompanying photo archive must be extracted into the District server's photo directory without modifying file names.</li>
      </ul>
    </div>
  </div>
</div>

<script>
function triggerDistrictExport() {
  var form = document.getElementById('export_dist_data');
  var hidExport = document.getElementById('hidExport');
  var btn = document.getElementById('btnExportData');

  if (hidExport && form) {
    hidExport.value = 'TRUE';
    form.action = '<?php echo base_url("export/export_district_data"); ?>';

    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Preparing CSV Dataset...';
    }

    if (typeof Toastify !== 'undefined') {
      Toastify({
        text: "Generating District Kalolsavam CSV export...",
        duration: 4000,
        gravity: "top",
        position: "right",
        style: { background: "linear-gradient(to right, #10b981, #059669)" }
      }).showToast();
    }

    form.submit();

    setTimeout(function() {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-download fs-5"></i><span>Export & Download CSV Data</span>';
      }
    }, 4000);
  }
}

function triggerPhotosExport() {
  var form = document.getElementById('export_dist_data');
  var btn = document.getElementById('btnExportPhotos');

  if (form) {
    form.action = '<?php echo base_url("welcome/export_photo/"); ?>';

    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Packaging Photos ZIP...';
    }

    if (typeof Toastify !== 'undefined') {
      Toastify({
        text: "Bundling student photographs into ZIP archive...",
        duration: 4000,
        gravity: "top",
        position: "right",
        style: { background: "linear-gradient(to right, #0284c7, #0369a1)" }
      }).showToast();
    }

    form.submit();

    setTimeout(function() {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-file-zip fs-5"></i><span>Export & Download Photos (ZIP)</span>';
      }
    }, 4000);
  }
}
</script>
