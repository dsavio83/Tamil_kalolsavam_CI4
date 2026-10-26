<div class="pagetitle mb-3 d-print-none">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="d-flex align-items-center gap-2 text-dark fw-bold mb-1">
        <i class="bi bi-bar-chart-line-fill text-primary"></i>
        <span>Schedule Analytics & Statistics</span>
      </h1>
      <p class="text-muted small mb-0">Festival: <strong><?php echo esc($selected_festival ?? 'Festival'); ?></strong> - Comprehensive operational statistics, stage utilization, and participant coverage.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="btn btn-secondary btn-sm rounded-pill px-3 py-1.5 fw-semibold text-white d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background-color: #6c757d; border-color: #6c757d;" onclick="if(window.history.length > 1 && document.referrer && document.referrer !== window.location.href){ history.back(); return false; }">
        <i class="bi bi-arrow-left"></i>
        <span>Back</span>
      </a>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-light px-3 py-1.5 rounded-pill border">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none"><i class="bi bi-house-door-fill me-1"></i>Home</a></li>
          <li class="breadcrumb-item"><a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="text-decoration-none">Scheduling</a></li>
          <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Statistics</li>
        </ol>
      </nav>
    </div>
  </div>
</div>

<?php 
  $total_items = (int)($stats['total_items'] ?? 0);
  $scheduled_items = (int)($stats['scheduled_items'] ?? 0);
  $total_participants = (int)($stats['total_participants'] ?? 0);
  $off_stage = (int)($stats['off_stage_items'] ?? 0);
  $on_stage = (int)($stats['on_stage_items'] ?? 0);
  $coverage_pct = ($total_items > 0) ? round(($scheduled_items / $total_items) * 100, 1) : 0;
  $venues = $stats['venue_utilization'] ?? [];
  $days = $stats['schedule_by_date'] ?? [];
?>

<!-- Action & Filter Control Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden d-print-none" style="border: 1px solid #e2e8f0 !important;">
  <div class="card-body p-3 p-md-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="btn btn-secondary btn-sm rounded-pill px-3 py-1.5 fw-semibold text-white d-inline-flex align-items-center gap-1.5 shadow-2xs hover-lift" style="background-color: #6c757d; border-color: #6c757d;" onclick="if(window.history.length > 1 && document.referrer && document.referrer !== window.location.href){ history.back(); return false; }">
        <i class="bi bi-arrow-left"></i>
        <span>Back to Scheduling</span>
      </a>

      <?php if(!empty($festivals) && count($festivals) > 1): ?>
        <form action="<?php echo site_url('stage/festival_scheduling/schedule_statistics'); ?>" method="GET" class="d-inline-flex align-items-center">
          <select name="fest_id" class="form-select form-select-sm rounded-pill px-3 shadow-none border-primary-subtle font-monospace" onchange="this.form.submit()">
            <option value="all" <?php echo (($fest_id ?? '') === 'all') ? 'selected' : ''; ?>>All Festivals</option>
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

      <?php if($coverage_pct >= 100): ?>
        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 font-monospace fw-bold d-inline-flex align-items-center gap-1">
          <i class="bi bi-patch-check-fill"></i> 100% Fully Scheduled
        </span>
      <?php elseif($coverage_pct > 0): ?>
        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1.5 font-monospace fw-bold d-inline-flex align-items-center gap-1">
          <i class="bi bi-hourglass-split"></i> <?php echo $coverage_pct; ?>% Scheduled
        </span>
      <?php else: ?>
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1.5 font-monospace fw-bold d-inline-flex align-items-center gap-1">
          <i class="bi bi-exclamation-octagon-fill"></i> Not Scheduled
        </span>
      <?php endif; ?>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
      <a href="<?php echo site_url('stage/festival_scheduling/view_schedule?fest_id=' . ($fest_id ?? '')); ?>" class="btn btn-outline-primary rounded-pill px-3 py-1.5 btn-sm fw-semibold d-inline-flex align-items-center gap-1">
        <i class="bi bi-calendar3"></i> Full Schedule
      </a>
      <a href="<?php echo site_url('stage/festival_scheduling/order_of_events?fest_id=' . ($fest_id ?? '')); ?>" class="btn btn-outline-info rounded-pill px-3 py-1.5 btn-sm fw-semibold d-inline-flex align-items-center gap-1">
        <i class="bi bi-list-ol"></i> Order of Events
      </a>
      <a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 btn-sm fw-semibold d-inline-flex align-items-center gap-1">
        <i class="bi bi-sliders"></i> Stage Settings
      </a>
      <a href="<?php echo site_url('stage/festival_scheduling/export_schedule?fest_id=' . ($fest_id ?? '') . '&format=excel'); ?>" class="btn btn-outline-success rounded-pill px-3 py-1.5 btn-sm fw-semibold d-inline-flex align-items-center gap-1">
        <i class="bi bi-file-earmark-excel"></i> Export Excel
      </a>
      <button type="button" class="btn btn-primary rounded-pill px-3 py-1.5 btn-sm fw-semibold shadow-sm d-inline-flex align-items-center gap-1" onclick="window.print()">
        <i class="bi bi-printer-fill"></i> Print Report
      </button>
    </div>
  </div>
</div>

<?php if(isset($stats) && $stats && $total_items > 0): ?>

  <!-- 4 KPI Summary Cards -->
  <div class="row g-3 mb-4">
    <!-- Total Competition Items -->
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white stat-card" style="border: 1px solid #e2e8f0 !important;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted small fw-semibold text-uppercase tracking-wider">Total Items</span>
            <h3 class="fw-bold text-dark mt-1 mb-0 font-monospace fs-2"><?php echo $total_items; ?></h3>
            <span class="text-muted small">All competition events</span>
          </div>
          <div class="stat-icon-wrapper rounded-4 d-flex align-items-center justify-content-center shadow-xs" style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #4338ca; width: 54px; height: 54px;">
            <i class="bi bi-trophy-fill fs-3"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Scheduled Items -->
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white stat-card" style="border: 1px solid #e2e8f0 !important;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted small fw-semibold text-uppercase tracking-wider">Scheduled Items</span>
            <h3 class="fw-bold text-success mt-1 mb-0 font-monospace fs-2"><?php echo $scheduled_items; ?></h3>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 small mt-1 font-monospace">
              <?php echo $coverage_pct; ?>% Allocated
            </span>
          </div>
          <div class="stat-icon-wrapper rounded-4 d-flex align-items-center justify-content-center shadow-xs" style="background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #15803d; width: 54px; height: 54px;">
            <i class="bi bi-calendar-check-fill fs-3"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Participants -->
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white stat-card" style="border: 1px solid #e2e8f0 !important;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted small fw-semibold text-uppercase tracking-wider">Total Participants</span>
            <h3 class="fw-bold text-dark mt-1 mb-0 font-monospace fs-2"><?php echo number_format($total_participants); ?></h3>
            <span class="text-muted small">Registered students</span>
          </div>
          <div class="stat-icon-wrapper rounded-4 d-flex align-items-center justify-content-center shadow-xs" style="background: linear-gradient(135deg, #fef3c7, #fde68a); color: #b45309; width: 54px; height: 54px;">
            <i class="bi bi-people-fill fs-3"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Venues / Stages Used -->
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 p-3 h-100 bg-white stat-card" style="border: 1px solid #e2e8f0 !important;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted small fw-semibold text-uppercase tracking-wider">Stages Utilized</span>
            <h3 class="fw-bold text-primary mt-1 mb-0 font-monospace fs-2"><?php echo count($venues); ?></h3>
            <span class="text-muted small">Active stages & rooms</span>
          </div>
          <div class="stat-icon-wrapper rounded-4 d-flex align-items-center justify-content-center shadow-xs" style="background: linear-gradient(135deg, #e0f2fe, #bae6fd); color: #0369a1; width: 54px; height: 54px;">
            <i class="bi bi-geo-alt-fill fs-3"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Event Distribution & Schedule Coverage Visual Gauges -->
  <div class="row g-4 mb-4">
    <!-- Event Distribution (On-Stage vs Off-Stage) -->
    <div class="col-12 col-lg-6">
      <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden bg-white" style="border: 1px solid #e2e8f0 !important;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between" style="border-color: #f1f5f9 !important;">
          <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
              <i class="bi bi-pie-chart-fill"></i>
            </div>
            <h6 class="mb-0 fw-bold text-dark">Stage Category Distribution</h6>
          </div>
          <span class="badge bg-light text-muted border rounded-pill font-monospace small">Total: <?php echo $total_items; ?> Items</span>
        </div>

        <div class="card-body p-4">
          <!-- Stacked Progress Bar -->
          <?php 
            $on_stage_pct = ($total_items > 0) ? round(($on_stage / $total_items) * 100, 1) : 0;
            $off_stage_pct = ($total_items > 0) ? round(($off_stage / $total_items) * 100, 1) : 0;
          ?>
          <div class="progress rounded-pill mb-4" style="height: 14px; background-color: #f1f5f9;">
            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $on_stage_pct; ?>%" title="On-Stage: <?php echo $on_stage_pct; ?>%"></div>
            <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $off_stage_pct; ?>%" title="Off-Stage: <?php echo $off_stage_pct; ?>%"></div>
          </div>

          <div class="row g-3 text-center">
            <div class="col-6">
              <div class="p-3 rounded-4 bg-light border border-primary-subtle">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                  <span class="rounded-circle bg-primary d-inline-block" style="width: 10px; height: 10px;"></span>
                  <span class="text-muted small fw-semibold">On-Stage Items</span>
                </div>
                <h4 class="fw-bold text-dark font-monospace mb-0"><?php echo $on_stage; ?></h4>
                <small class="text-primary fw-bold font-monospace"><?php echo $on_stage_pct; ?>% of total</small>
              </div>
            </div>

            <div class="col-6">
              <div class="p-3 rounded-4 bg-light border border-warning-subtle">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                  <span class="rounded-circle bg-warning d-inline-block" style="width: 10px; height: 10px;"></span>
                  <span class="text-muted small fw-semibold">Off-Stage Items</span>
                </div>
                <h4 class="fw-bold text-dark font-monospace mb-0"><?php echo $off_stage; ?></h4>
                <small class="text-warning-emphasis fw-bold font-monospace"><?php echo $off_stage_pct; ?>% of total</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Schedule Completion & Status -->
    <div class="col-12 col-lg-6">
      <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden bg-white" style="border: 1px solid #e2e8f0 !important;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between" style="border-color: #f1f5f9 !important;">
          <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
              <i class="bi bi-speedometer2"></i>
            </div>
            <h6 class="mb-0 fw-bold text-dark">Schedule Completion Rate</h6>
          </div>
          <span class="badge bg-light text-muted border rounded-pill font-monospace small"><?php echo $scheduled_items; ?> of <?php echo $total_items; ?></span>
        </div>

        <div class="card-body p-4">
          <div class="d-flex align-items-baseline justify-content-between mb-2">
            <span class="fw-bold text-dark fs-5 font-monospace"><?php echo $coverage_pct; ?>%</span>
            <span class="text-muted small font-monospace"><?php echo ($total_items - $scheduled_items); ?> items remaining</span>
          </div>

          <div class="progress rounded-pill mb-4" style="height: 14px; background-color: #f1f5f9;">
            <div class="progress-bar <?php echo ($coverage_pct >= 100) ? 'bg-success' : 'bg-primary'; ?>" role="progressbar" style="width: <?php echo min($coverage_pct, 100); ?>%"></div>
          </div>

          <div class="p-3 rounded-4 bg-light border d-flex align-items-center gap-3">
            <div class="rounded-circle <?php echo ($coverage_pct >= 100) ? 'bg-success text-white' : 'bg-warning text-dark'; ?> d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
              <i class="bi <?php echo ($coverage_pct >= 100) ? 'bi-check-lg' : 'bi-clock-history'; ?> fs-5"></i>
            </div>
            <div>
              <div class="fw-bold text-dark small mb-0">
                <?php echo ($coverage_pct >= 100) ? 'Complete Stage Coverage' : 'Partial Schedule Active'; ?>
              </div>
              <small class="text-muted">
                <?php echo ($coverage_pct >= 100) 
                  ? 'All festival competition items are successfully scheduled into stages and rooms without time conflicts.' 
                  : 'Run the auto stage scheduler to allocate the remaining items to available stages.'; ?>
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Daily Schedule Breakdown -->
  <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden bg-white" style="border: 1px solid #e2e8f0 !important;">
    <div class="card-header py-3 px-4 border-bottom bg-light d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-color: #e2e8f0 !important;">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-calendar3-week text-primary fs-5"></i>
        <h6 class="mb-0 fw-bold text-dark">Daily Timetable Distribution</h6>
      </div>
      <span class="badge bg-primary text-white rounded-pill px-3 py-1 font-monospace"><?php echo count($days); ?> Scheduled Days</span>
    </div>

    <div class="card-body p-3 p-md-4">
      <?php if(!empty($days)): ?>
        <div class="row g-3">
          <?php foreach($days as $date => $day_items): ?>
            <?php 
              $day_count = count($day_items);
              $day_on_stage = 0;
              $day_off_stage = 0;
              foreach($day_items as $di) {
                if(!empty($di['is_off_stage']) && $di['is_off_stage'] == 'Y') $day_off_stage++;
                else $day_on_stage++;
              }
            ?>
            <div class="col-12 col-sm-6 col-xl-3">
              <div class="border rounded-4 p-3 bg-white shadow-2xs h-100 hover-lift" style="border: 1px solid #e2e8f0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="badge bg-light text-dark border font-monospace px-2.5 py-1 fw-bold">
                    <i class="bi bi-calendar-day text-primary me-1"></i><?php echo date('M j, Y', strtotime($date)); ?>
                  </span>
                  <span class="text-muted small"><?php echo date('l', strtotime($date)); ?></span>
                </div>

                <div class="py-2">
                  <h3 class="fw-bold text-primary font-monospace mb-0"><?php echo $day_count; ?></h3>
                  <span class="text-muted small">Total Events</span>
                </div>

                <div class="d-flex align-items-center gap-2 pt-2 border-top">
                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5 small">
                    On: <?php echo $day_on_stage; ?>
                  </span>
                  <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 small">
                    Off: <?php echo $day_off_stage; ?>
                  </span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-muted text-center my-3">No daily schedule items found.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Stage & Venue Utilization Grid -->
  <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden bg-white" style="border: 1px solid #e2e8f0 !important;">
    <div class="card-header py-3 px-4 border-bottom bg-light d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-color: #e2e8f0 !important;">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-building-check text-success fs-5"></i>
        <h6 class="mb-0 fw-bold text-dark">Stage & Venue Load Utilization</h6>
      </div>
      <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 font-monospace"><?php echo count($venues); ?> Venues Active</span>
    </div>

    <div class="card-body p-3 p-md-4">
      <?php if(!empty($venues)): ?>
        <?php 
          $max_venue_items = 1;
          foreach($venues as $v_items) {
            if(count($v_items) > $max_venue_items) $max_venue_items = count($v_items);
          }
        ?>
        <div class="row g-3">
          <?php foreach($venues as $venue_name => $venue_items): ?>
            <?php 
              $v_count = count($venue_items);
              $v_load_pct = round(($v_count / $max_venue_items) * 100);
            ?>
            <div class="col-12 col-md-6 col-xl-4">
              <div class="border rounded-4 p-3 bg-white shadow-2xs h-100" style="border: 1px solid #e2e8f0 !important; padding: 1.2rem !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-circle p-0 d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px;">
                      <i class="bi bi-geo-alt-fill text-white" style="font-size: 0.85rem;"></i>
                    </span>
                    <h6 class="fw-bold text-dark mb-0"><?php echo esc($venue_name); ?></h6>
                  </div>
                  <span class="badge bg-dark text-white rounded-pill px-2.5 py-1 font-monospace small">
                    <?php echo $v_count; ?> Events
                  </span>
                </div>

                <div class="d-flex align-items-center justify-content-between text-muted small mt-2 mb-1">
                  <span>Relative Load:</span>
                  <span class="font-monospace fw-semibold"><?php echo $v_load_pct; ?>%</span>
                </div>

                <div class="progress rounded-pill mb-2" style="height: 8px; background-color: #f1f5f9;">
                  <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $v_load_pct; ?>%"></div>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-2">
                  <small class="text-muted font-monospace">Stage Status:</small>
                  <?php if($v_count > 10): ?>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 small">Heavy Traffic</span>
                  <?php else: ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small">Optimal Balance</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-muted text-center my-3">No venue utilization data available.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Bottom Navigation & Action Bar -->
  <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden d-print-none" style="border: 1px solid #e2e8f0 !important; background: #f8fafc;">
    <div class="card-body p-3 p-md-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
      <a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="btn btn-secondary rounded-pill px-4 py-2 fw-semibold text-white d-inline-flex align-items-center gap-2 hover-lift shadow-2xs" style="background-color: #6c757d; border-color: #6c757d;" onclick="if(window.history.length > 1 && document.referrer && document.referrer !== window.location.href){ history.back(); return false; }">
        <i class="bi bi-arrow-left"></i>
        <span>Go Back</span>
      </a>

      <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="<?php echo site_url('stage/festival_scheduling/view_schedule?fest_id=' . ($fest_id ?? '')); ?>" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold btn-sm">
          <i class="bi bi-calendar3 me-1"></i> Full Schedule View
        </a>
        <a href="<?php echo site_url('stage/festival_scheduling/order_of_events?fest_id=' . ($fest_id ?? '')); ?>" class="btn btn-outline-info rounded-pill px-3 py-2 fw-semibold btn-sm">
          <i class="bi bi-list-ol me-1"></i> Order of Events
        </a>
        <a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold btn-sm shadow-sm">
          <i class="bi bi-sliders me-1"></i> Stage Scheduling Hub
        </a>
      </div>
    </div>
  </div>

<?php else: ?>

  <!-- Empty State Card -->
  <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4 bg-white" style="border: 1px solid #e2e8f0 !important;">
    <div class="py-4">
      <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 76px; height: 76px;">
        <i class="bi bi-bar-chart-fill fs-2"></i>
      </div>
      <h4 class="fw-bold text-dark mb-1">No Schedule Statistics Available</h4>
      <p class="text-muted mb-4 small" style="max-width: 440px; margin: 0 auto;">
        No stage scheduling records found for <strong><?php echo esc($selected_festival ?? 'this festival'); ?></strong>. Run the auto stage scheduler to generate optimal stage allotments and timetable statistics.
      </p>
      <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
        <a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="btn btn-secondary rounded-pill px-4 py-2 fw-semibold text-white d-inline-flex align-items-center gap-1 shadow-2xs" style="background-color: #6c757d; border-color: #6c757d;" onclick="if(window.history.length > 1 && document.referrer && document.referrer !== window.location.href){ history.back(); return false; }">
          <i class="bi bi-arrow-left me-1"></i> Go Back
        </a>
        <a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
          <i class="bi bi-magic me-1"></i> Open Auto Stage Scheduler
        </a>
      </div>
    </div>
  </div>

<?php endif; ?>

<script>
// Smart Back Navigation: Checks browser history, falls back to festival scheduling hub
function goBack() {
  if (document.referrer && document.referrer.indexOf(window.location.host) !== -1) {
    history.back();
  } else {
    window.location.href = '<?php echo site_url("stage/festival_scheduling"); ?>';
  }
}
</script>

<style>
.stat-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06) !important;
}
.hover-lift {
  transition: transform 0.15s ease;
}
.hover-lift:hover {
  transform: translateY(-2px);
}
.shadow-2xs {
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.tracking-wider {
  letter-spacing: 0.05em;
}

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
