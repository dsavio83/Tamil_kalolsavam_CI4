<div class="pagetitle mb-3">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="d-flex align-items-center gap-2 text-dark fw-bold mb-1">
        <i class="bi bi-shield-exclamation text-warning"></i>
        <span>Participant Timetable Clash Analysis</span>
      </h1>
      <p class="text-muted small mb-0">Pre-scheduling student conflict detection & multi-item overlap audit for <strong><?php echo esc($selected_festival ?? 'All Festivals'); ?></strong></p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i> Back to Scheduling
      </a>
      <a href="<?php echo site_url('stage/festival_scheduling/generate_schedule?fest_id=' . ($fest_id ?? 'all')); ?>" class="btn btn-primary btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm">
        <i class="bi bi-magic text-warning"></i> Auto Schedule
      </a>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-12 col-md-4">
    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-warning-subtle text-warning p-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
          <i class="bi bi-people-fill fs-5"></i>
        </div>
        <div>
          <div class="text-muted small fw-semibold">Multi-Item Students</div>
          <div class="fw-bold text-dark fs-5 font-monospace"><?php echo (int)($total_clashes ?? 0); ?> Students</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-4">
    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
          <i class="bi bi-calendar-range-fill fs-5"></i>
        </div>
        <div>
          <div class="text-muted small fw-semibold">Scope Category</div>
          <div class="fw-bold text-dark fs-6"><?php echo esc($selected_festival ?? 'All Festivals'); ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-4">
    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
          <i class="bi bi-shield-check fs-5"></i>
        </div>
        <div>
          <div class="text-muted small fw-semibold">Scheduler Resolution</div>
          <div class="fw-bold text-success fs-6">Automatic Interval Offsetting</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="border: 1px solid #e2e8f0 !important;">
  <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-person-lines-fill text-primary fs-5"></i>
      <h5 class="mb-0 fw-bold text-dark">Multi-Item Participant Audit Report</h5>
    </div>
    <span class="badge bg-light text-muted border rounded-pill px-3 py-1 font-monospace">
      <?php echo count($clashes ?? []); ?> Records Identified
    </span>
  </div>

  <div class="card-body p-0">
    <?php if (!empty($clashes)): ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light text-muted small text-uppercase">
            <tr>
              <th class="ps-4 py-3" style="width: 50px;">#</th>
              <th class="py-3">Participant Details</th>
              <th class="py-3">School</th>
              <th class="py-3 text-center" style="width: 120px;">Item Count</th>
              <th class="py-3">Enrolled Competitions</th>
              <th class="py-3 text-center pe-4" style="width: 140px;">Conflict Status</th>
            </tr>
          </thead>
          <tbody>
            <?php $i = 1; foreach ($clashes as $clash): ?>
              <tr>
                <td class="ps-4 text-muted fw-semibold font-monospace"><?php echo $i++; ?></td>
                <td>
                  <div class="fw-bold text-dark">
                    <?php echo esc(!empty($clash['participant_name']) ? $clash['participant_name'] : 'Student #' . ($clash['participant_id'] ?? '')); ?>
                  </div>
                  <div class="text-muted small font-monospace">
                    Adm No: <?php echo esc($clash['admn_no'] ?? 'N/A'); ?>
                  </div>
                </td>
                <td>
                  <div class="text-dark small fw-medium">
                    <?php echo esc(!empty($clash['school_name']) ? $clash['school_name'] : 'School Code: ' . ($clash['school_code'] ?? 'N/A')); ?>
                  </div>
                </td>
                <td class="text-center">
                  <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 fw-bold">
                    <?php echo (int)($clash['item_count'] ?? 0); ?> Items
                  </span>
                </td>
                <td>
                  <div class="d-flex flex-wrap gap-1">
                    <?php
                    $item_codes = explode(',', $clash['items'] ?? '');
                    $item_names = explode(',', $clash['item_names'] ?? '');
                    for ($k = 0; $k < count($item_codes); $k++):
                    ?>
                      <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 small">
                        <strong>[<?php echo trim($item_codes[$k]); ?>]</strong> <?php echo esc(trim($item_names[$k] ?? '')); ?>
                      </span>
                    <?php endfor; ?>
                  </div>
                </td>
                <td class="text-center pe-4">
                  <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small">
                    <i class="bi bi-check-circle me-1"></i> Auto-Separated
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="text-center py-5">
        <div class="mb-3">
          <i class="bi bi-shield-check text-success display-4"></i>
        </div>
        <h5 class="fw-bold text-dark">Zero Student Conflicts Detected</h5>
        <p class="text-muted small max-w-md mx-auto">All participants are registered within safe item allotments. Auto-scheduling can proceed smoothly with maximum stage utilization.</p>
        <a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="btn btn-primary btn-sm rounded-pill px-4 py-2 mt-2">
          <i class="bi bi-arrow-left me-1"></i> Return to Scheduling Hub
        </a>
      </div>
    <?php endif; ?>
  </div>

  <div class="card-footer bg-light py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="text-muted small">
      <i class="bi bi-info-circle text-primary me-1"></i>
      Our Scheduling Engine guarantees strictly separate time windows: <code>(start1 &lt; end2) AND (start2 &lt; end1)</code>.
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        Back
      </a>
      <a href="<?php echo site_url('stage/festival_scheduling/generate_schedule?fest_id=' . ($fest_id ?? 'all')); ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
        <i class="bi bi-play-fill me-1"></i> Run Scheduler Now
      </a>
    </div>
  </div>
</div>
