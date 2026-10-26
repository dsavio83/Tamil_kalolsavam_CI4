<div class="pagetitle mb-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
        <span class="rounded-3 bg-warning-subtle text-warning-emphasis p-2 d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 42px; height: 42px;">
          <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        </span>
        <span>District Participant Clashes Report</span>
      </h1>
      <nav>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
          <li class="breadcrumb-item">District</li>
          <li class="breadcrumb-item"><a href="<?php echo base_url('district/scheduling'); ?>">Scheduling</a></li>
          <li class="breadcrumb-item active">Clash Report</li>
        </ol>
      </nav>
    </div>
    <div>
      <a href="<?php echo base_url('district/scheduling'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-sm">
        <i class="bi bi-arrow-left"></i>
        <span>Back to Scheduling</span>
      </a>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
  <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h5 class="card-title mb-0 fw-bold text-dark">Contestants Enrolled in Multiple District Items</h5>
      <small class="text-muted">Ensure these events are not scheduled concurrently on different stages.</small>
    </div>
    <span class="badge bg-warning text-dark fw-bold rounded-pill px-3 py-1.5">
      <?php echo count($clashes ?? []); ?> Clashing Candidates
    </span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th class="ps-4">#</th>
            <th>Candidate Details</th>
            <th>Sub-District &amp; School</th>
            <th>Total Items</th>
            <th class="pe-4">Registered Competitions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($clashes)): ?>
            <?php foreach ($clashes as $idx => $c): ?>
              <tr>
                <td class="ps-4 fw-bold text-muted"><?php echo $idx + 1; ?></td>
                <td>
                  <div class="fw-bold text-dark"><?php echo esc($c['participant_name']); ?></div>
                  <small class="text-muted font-monospace">ID: #<?php echo esc($c['d_participant_id']); ?></small>
                </td>
                <td>
                  <span class="badge bg-primary-subtle text-primary rounded-pill small mb-0.5"><?php echo esc($c['sub_district_name'] ?: 'Sub-District ' . $c['sub_district_code']); ?></span>
                  <div class="small text-muted text-truncate" style="max-width: 240px;"><?php echo esc($c['school_name'] ?: 'School ' . $c['school_code']); ?></div>
                </td>
                <td>
                  <span class="badge bg-danger rounded-pill px-2.5 py-1 fw-bold">
                    <?php echo esc($c['item_count']); ?> Items
                  </span>
                </td>
                <td class="pe-4">
                  <div class="small text-dark font-monospace"><?php echo esc($c['item_names']); ?></div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center py-5 text-muted">
                <i class="bi bi-check-circle fs-1 text-success opacity-50 d-block mb-2"></i>
                <span>No scheduling clashes found. All contestants have single-item enrollments.</span>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
