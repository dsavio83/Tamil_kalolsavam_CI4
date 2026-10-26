<style>
.template-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border-radius: 1rem;
}
.template-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08) !important;
}
.paper-badge {
  font-size: 0.75rem;
  font-weight: 700;
  padding: 0.35em 0.75em;
  border-radius: 50rem;
}
</style>

<div class="pagetitle mb-3">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="d-flex align-items-center gap-2 text-dark fw-bold mb-1">
        <i class="bi bi-printer-fill text-primary"></i>
        <span>Cover Print</span>
      </h1>
      <p class="text-muted small mb-0">Design, customize and print Item Covers (1 Item = 1 Page) for Kalolsavam events.</p>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 bg-light px-3 py-1.5 rounded-pill border">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none"><i class="bi bi-house-door-fill me-1"></i>Home</a></li>
        <li class="breadcrumb-item text-muted">Reports</li>
        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Cover Print</li>
      </ol>
    </nav>
  </div>
</div>

<!-- Cover Print Sub-Navigation & Actions -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div class="d-flex align-items-center gap-2">
      <a href="<?php echo base_url('cover/templates'); ?>" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold active px-3">
        <i class="bi bi-grid-fill me-1"></i> Cover Templates
      </a>
      <a href="<?php echo base_url('cover/designer'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill fw-semibold px-3">
        <i class="bi bi-palette-fill me-1"></i> Cover Designer
      </a>
      <a href="<?php echo base_url('cover/generate'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill fw-semibold px-3">
        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Generate Covers
      </a>
    </div>

    <div class="d-flex align-items-center gap-2">
      <a href="<?php echo base_url('cover/designer'); ?>" class="btn btn-primary rounded-pill shadow-sm px-4 fw-semibold">
        <i class="bi bi-plus-circle me-1"></i> + New Template
      </a>
    </div>
  </div>
</div>

<!-- Flash Notifications -->
<?php if (session()->getFlashdata('message')): ?>
  <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
    <span><?php echo esc(session()->getFlashdata('message')); ?></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
    <span><?php echo esc(session()->getFlashdata('error')); ?></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<!-- Templates Cards Grid -->
<div class="row g-4 mb-4">
  <?php if (!empty($templates)): ?>
    <?php foreach ($templates as $t): ?>
      <div class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm template-card h-100 d-flex flex-column overflow-hidden">
          <div class="card-header bg-light py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-file-earmark-text-fill text-primary fs-5"></i>
              <h6 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 180px;" title="<?php echo esc($t['template_name']); ?>">
                <?php echo esc($t['template_name']); ?>
              </h6>
            </div>
            <?php if ($t['is_default'] === 'Y'): ?>
              <span class="badge bg-success-subtle text-success border border-success-subtle paper-badge">
                <i class="bi bi-star-fill me-1"></i> Default
              </span>
            <?php endif; ?>
          </div>

          <div class="card-body p-4 flex-grow-1">
            <div class="d-flex align-items-center gap-2 mb-3">
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle paper-badge">
                <i class="bi bi-paperclip me-1"></i> <?php echo esc($t['paper_size']); ?>
              </span>
              <span class="badge bg-info-subtle text-info border border-info-subtle paper-badge">
                <i class="bi bi-arrow-repeat me-1"></i> <?php echo ucfirst(esc($t['orientation'])); ?>
              </span>
              <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle paper-badge">
                <?php echo round($t['width_mm']); ?> × <?php echo round($t['height_mm']); ?> mm
              </span>
            </div>

            <p class="text-muted small mb-3">
              <i class="bi bi-clock-history me-1"></i> Last updated: <?php echo !empty($t['updated_at']) ? date('d M Y, h:i A', strtotime($t['updated_at'])) : date('d M Y'); ?>
            </p>

            <div class="p-3 bg-light rounded-3 text-center border">
              <div class="text-muted small fw-semibold">
                <i class="bi bi-info-circle me-1"></i> Rule: 1 Item = 1 Page Cover
              </div>
            </div>
          </div>

          <div class="card-footer bg-white border-top p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
              <a href="<?php echo base_url('cover/generate?template_id=' . $t['id']); ?>" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold shadow-sm">
                <i class="bi bi-printer-fill me-1"></i> Generate Covers
              </a>
              <a href="<?php echo base_url('cover/designer/' . $t['id']); ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                <i class="bi bi-pencil-square me-1"></i> Edit
              </a>
            </div>

            <div class="d-flex align-items-center gap-1">
              <a href="<?php echo base_url('cover/duplicate_template/' . $t['id']); ?>" class="btn btn-outline-secondary btn-sm rounded-circle" title="Duplicate" onclick="return confirm('Duplicate this template?');">
                <i class="bi bi-copy"></i>
              </a>

              <?php if ($t['is_default'] !== 'Y'): ?>
                <a href="<?php echo base_url('cover/set_default/' . $t['id']); ?>" class="btn btn-outline-success btn-sm rounded-circle" title="Set as Default">
                  <i class="bi bi-star"></i>
                </a>
                <a href="<?php echo base_url('cover/delete_template/' . $t['id']); ?>" class="btn btn-outline-danger btn-sm rounded-circle" title="Delete" onclick="return confirm('Delete this template permanently?');">
                  <i class="bi bi-trash3"></i>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="col-12">
      <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
        <i class="bi bi-inbox text-muted fs-1 mb-3"></i>
        <h5 class="fw-bold text-dark">No Templates Found</h5>
        <p class="text-muted">Create your first cover template using the visual canvas designer.</p>
        <div>
          <a href="<?php echo base_url('cover/designer'); ?>" class="btn btn-primary rounded-pill px-4 fw-semibold">
            <i class="bi bi-plus-circle me-1"></i> Create First Template
          </a>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>
