<div class="pagetitle mb-3">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="d-flex align-items-center gap-2 text-dark fw-bold mb-1">
        <i class="bi bi-calendar2-range-fill text-primary"></i>
        <span>Stage Allotment (Festivalwise)</span>
      </h1>
      <p class="text-muted small mb-0">Configure stage, date, timing, clusters, and judges for all festival items in one place.</p>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 bg-light px-3 py-1.5 rounded-pill border">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none"><i class="bi bi-house-door-fill me-1"></i>Home</a></li>
        <li class="breadcrumb-item text-muted">Stage</li>
        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Festival Allotment</li>
      </ol>
    </nav>
  </div>
</div>

<?php echo form_open('stage/item_participant/participant_nodetails', array('id' => 'formIWP')); ?>
<input type="hidden" name="hidUservalue" id="hidUservalue" value="SHOW" />

<!-- Compact Single-Row Festival Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body py-3 px-4">
    <div class="row align-items-center g-3">
      <div class="col-12 col-md-auto d-flex align-items-center gap-2">
        <span class="badge bg-primary-subtle text-primary rounded-circle p-2">
          <i class="bi bi-funnel-fill fs-6"></i>
        </span>
        <label for="cmbFestType" class="form-label fw-bold text-dark mb-0 text-nowrap">
          Festival Type:
        </label>
      </div>

      <div class="col-12 col-md">
        <div class="input-group">
          <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-tags-fill"></i></span>
          <?php echo form_dropdown("cmbFestType", $fest, $fest_id, 'class="form-select border-start-0 py-2" id="cmbFestType" required'); ?>
        </div>
      </div>

      <div class="col-12 col-md-auto">
        <button type="submit" name="GET" value="GET" id="btnGet" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-semibold w-100" onclick="showFormSpinner(this, 'Fetching...')">
          <i class="bi bi-search"></i>
          <span>Get Item Details</span>
        </button>
      </div>
    </div>
  </div>
</div>

<?php echo form_close(); ?>