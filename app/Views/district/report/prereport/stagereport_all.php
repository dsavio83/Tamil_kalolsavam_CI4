<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-diagram-3-fill text-primary"></i> Stage Reports
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Reports</li>
      <li class="breadcrumb-item active">Stage Reports</li>
    </ol>
  </nav>
</div>

<div class="row justify-content-center g-4">
  <!-- Card 1: Stage Report All -->
  <div class="col-md-6 col-xl-5">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-header bg-primary text-white py-3 px-4 rounded-top-4">
        <h5 class="card-title mb-0 text-white fw-bold d-flex align-items-center gap-2">
          <i class="bi bi-file-earmark-pdf-fill"></i> Stage Report - All Stages
        </h5>
      </div>
      <div class="card-body p-4 d-flex flex-column justify-content-between">
        <p class="text-muted small mb-4">
          Complete program schedule report across all stages and dates with event timings and details.
        </p>
        <a href="<?php echo base_url(); ?>district/report/prereportpdf/stagereport_all" target="_blank" class="btn btn-primary rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
          <i class="bi bi-file-earmark-pdf-fill"></i>
          <span>View Stage Report (PDF)</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Card 2: Stage Allotment Abstract -->
  <div class="col-md-6 col-xl-5">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-header bg-secondary text-white py-3 px-4 rounded-top-4">
        <h5 class="card-title mb-0 text-white fw-bold d-flex align-items-center gap-2">
          <i class="bi bi-file-earmark-text-fill"></i> Stage Report - Abstract
        </h5>
      </div>
      <div class="card-body p-4 d-flex flex-column justify-content-between">
        <p class="text-muted small mb-4">
          Summary abstract of stage allocations, event counts, and timing statistics for all stages.
        </p>
        <a href="<?php echo base_url(); ?>district/report/prereportpdf/stagereport_abstract" target="_blank" class="btn btn-outline-secondary rounded-pill py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-2">
          <i class="bi bi-file-earmark-text-fill"></i>
          <span>View Abstract Report (PDF)</span>
        </a>
      </div>
    </div>
  </div>
</div>