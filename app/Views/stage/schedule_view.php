<div class="pagetitle mb-3 d-print-none">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="d-flex align-items-center gap-2 text-dark fw-bold mb-1">
        <i class="bi bi-calendar3-range text-primary"></i>
        <span>Full Stage Schedule</span>
      </h1>
      <p class="text-muted small mb-0">Festival: <strong><?php echo esc($selected_festival ?? 'Festival'); ?></strong> - Comprehensive chronological timetable across all stages.</p>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 bg-light px-3 py-1.5 rounded-pill border">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none"><i class="bi bi-house-door-fill me-1"></i>Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="text-decoration-none">Scheduling</a></li>
        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Schedule View</li>
      </ol>
    </nav>
  </div>
</div>

<?php if(isset($schedule_by_date) && !empty($schedule_by_date)): ?>
  <!-- Action Control Card -->
  <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden d-print-none" style="border: 1px solid #e2e8f0 !important;">
    <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <?php if(!empty($festivals) && count($festivals) > 1): ?>
          <form action="<?php echo site_url('stage/festival_scheduling/view_schedule'); ?>" method="GET" class="d-inline-flex align-items-center me-1">
            <select name="fest_id" class="form-select form-select-sm rounded-pill px-3 shadow-none border-primary-subtle" onchange="this.form.submit()">
              <?php foreach ($festivals as $f): ?>
                <option value="<?php echo esc($f['fest_id']); ?>" <?php echo ($f['fest_id'] == ($fest_id ?? '')) ? 'selected' : ''; ?>>
                  <?php echo esc($f['fest_name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </form>
        <?php else: ?>
          <span class="badge bg-primary text-white rounded-pill px-3 py-1.5 font-monospace"><?php echo esc($selected_festival); ?></span>
        <?php endif; ?>
        <span class="badge bg-light text-secondary border rounded-pill px-3 py-1.5 font-monospace">
          <i class="bi bi-calendar-event me-1"></i><?php echo count($schedule_by_date); ?> Days
        </span>
      </div>

      <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 btn-sm fw-semibold">
          <i class="bi bi-sliders me-1"></i> Stage Settings
        </a>
        <a href="<?php echo site_url('stage/festival_scheduling/order_of_events?fest_id=' . ($fest_id ?? '')); ?>" class="btn btn-outline-info rounded-pill px-3 py-1.5 btn-sm fw-semibold">
          <i class="bi bi-list-ol me-1"></i> Order of Events
        </a>
        <a href="<?php echo site_url('stage/festival_scheduling/schedule_statistics?fest_id=' . ($fest_id ?? '')); ?>" class="btn btn-outline-warning text-dark rounded-pill px-3 py-1.5 btn-sm fw-semibold">
          <i class="bi bi-bar-chart-fill text-warning me-1"></i> Statistics
        </a>
        <a href="<?php echo site_url('stage/festival_scheduling/export_schedule?fest_id=' . ($fest_id ?? '') . '&format=excel'); ?>" class="btn btn-outline-success rounded-pill px-3 py-1.5 btn-sm fw-semibold">
          <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
        <button type="button" class="btn btn-primary rounded-pill px-3.5 py-1.5 btn-sm fw-semibold shadow-sm" onclick="window.print()">
          <i class="bi bi-printer-fill me-1"></i> Print Timetable
        </button>
      </div>
    </div>
  </div>

  <!-- Date Wise Schedule Cards -->
  <?php foreach($schedule_by_date as $date => $day_schedule): ?>
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="border: 1px solid #e2e8f0 !important;">
      <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: linear-gradient(135deg, #1e293b, #0f172a) !important; color: #fff;">
        <div class="d-flex align-items-center gap-2">
          <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
            <i class="bi bi-calendar2-date-fill text-white"></i>
          </div>
          <h5 class="mb-0 fw-bold text-white"><?php echo date('l, F j, Y', strtotime($date)); ?></h5>
        </div>
        <span class="badge bg-white text-dark rounded-pill px-3 py-1 font-monospace fw-bold"><?php echo count($day_schedule); ?> Items</span>
      </div>

      <div class="card-body p-3 p-md-4">
        <?php
        $venue_groups = array();
        foreach($day_schedule as $item) {
            $venue = $item['stage_name'] ?? 'Main Stage';
            if(!isset($venue_groups[$venue])) {
                $venue_groups[$venue] = array();
            }
            $venue_groups[$venue][] = $item;
        }
        ?>

        <div class="row g-3 g-md-4">
          <?php foreach($venue_groups as $venue => $venue_items): ?>
            <div class="col-12 col-xl-6">
              <div class="border rounded-4 overflow-hidden h-100 bg-white shadow-xs" style="border-color: #e2e8f0 !important;">
                <div class="py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0 !important;">
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">
                      <i class="bi bi-geo-alt-fill fs-8"></i>
                    </span>
                    <h6 class="mb-0 fw-bold text-dark fs-6"><?php echo esc($venue); ?></h6>
                  </div>
                  <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2.5 py-1 font-monospace small fw-bold">
                    <?php echo count($venue_items); ?> Items
                  </span>
                </div>

                <div class="p-0">
                  <ul class="list-group list-group-flush">
                    <?php foreach($venue_items as $item): ?>
                      <li class="list-group-item d-flex align-items-center justify-content-between p-3">
                        <div class="d-flex align-items-center gap-3">
                          <span class="badge bg-light text-dark border font-monospace px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                            <i class="bi bi-clock text-primary"></i><?php echo date('h:i A', strtotime($item['start_time'])); ?>
                          </span>
                          <div>
                            <div class="fw-bold text-dark mb-0"><?php echo esc($item['item_name']); ?></div>
                            <small class="text-muted font-monospace">Code: #<?php echo esc($item['item_code']); ?></small>
                          </div>
                        </div>
                        <div>
                          <?php if(!empty($item['is_off_stage']) && $item['is_off_stage'] == 'Y'): ?>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 small">Off Stage</span>
                          <?php else: ?>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small">On Stage</span>
                          <?php endif; ?>
                        </div>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

<?php else: ?>
  <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4" style="border: 1px solid #e2e8f0 !important;">
    <div class="empty-state-container py-3">
      <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 70px; height: 70px;">
        <i class="bi bi-calendar-x fs-2"></i>
      </div>
      <h5 class="fw-bold text-dark mb-1">No Schedule Created Yet</h5>
      <p class="text-muted mb-3 small" style="max-width: 420px; margin: 0 auto;">No stage schedule has been generated for this festival yet. Run the auto stage scheduler to generate an optimal timetable.</p>
      <div>
        <a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
          <i class="bi bi-magic me-1"></i> Open Auto Stage Scheduler
        </a>
      </div>
    </div>
  </div>
<?php endif; ?>

<style>
@media print {
  #header, #sidebar, .pagetitle, .btn, .d-print-none {
    display: none !important;
  }
  #main {
    margin: 0 !important;
    padding: 0 !important;
  }
  .card {
    border: 1px solid #cbd5e1 !important;
    box-shadow: none !important;
    page-break-inside: avoid;
  }
}
</style>

