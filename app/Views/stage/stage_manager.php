<?php
/**
 * Stage Manager Operational Console
 * Interactive interface for starting, completing programs, monitoring delays, and entering marks.
 */
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap');

.sm-console {
  font-family: 'Outfit', 'Poppins', system-ui, -apple-system, sans-serif;
  color: #0f172a;
  background: #f8fafc;
  min-height: 100vh;
  padding: 24px 15px;
}

.sm-card {
  background: #ffffff;
  border-radius: 18px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05);
  transition: all 0.25s ease;
  overflow: hidden;
}

.sm-hero {
  background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
  color: #ffffff;
  padding: 30px 36px;
  border-radius: 20px;
  margin-bottom: 24px;
  position: relative;
  overflow: hidden;
}

.sm-hero::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -10%;
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, rgba(99, 102, 241, 0.3) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}

.sm-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 9999px;
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 0.02em;
}

.sm-badge-live {
  background: rgba(16, 185, 129, 0.15);
  color: #059669;
  border: 1px solid rgba(16, 185, 129, 0.3);
}

.sm-badge-delayed {
  background: rgba(239, 68, 68, 0.15);
  color: #dc2626;
  border: 1px solid rgba(239, 68, 68, 0.3);
  animation: pulseBlink 1.5s infinite;
}

.sm-badge-scheduled {
  background: rgba(59, 130, 246, 0.15);
  color: #2563eb;
  border: 1px solid rgba(59, 130, 246, 0.3);
}

.sm-badge-completed {
  background: rgba(100, 116, 139, 0.15);
  color: #475569;
  border: 1px solid rgba(100, 116, 139, 0.3);
}

@keyframes pulseBlink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.6; }
}

@keyframes liveRadarDot {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
  70% { transform: scale(1.15); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.radar-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #10b981;
  animation: liveRadarDot 1.8s infinite;
  display: inline-block;
}

.radar-dot-red {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #ef4444;
  animation: liveRadarDot 1.8s infinite;
  display: inline-block;
}

.btn-action-start {
  background: linear-gradient(135deg, #059669 0%, #10b981 100%);
  color: #ffffff;
  border: none;
  font-weight: 700;
  padding: 14px 28px;
  border-radius: 14px;
  font-size: 1.05rem;
  box-shadow: 0 8px 20px -4px rgba(16, 185, 129, 0.4);
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 10px;
}
.btn-action-start:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px -4px rgba(16, 185, 129, 0.5);
  color: #ffffff;
}

.btn-action-complete {
  background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
  color: #ffffff;
  border: none;
  font-weight: 700;
  padding: 14px 28px;
  border-radius: 14px;
  font-size: 1.05rem;
  box-shadow: 0 8px 20px -4px rgba(239, 68, 68, 0.4);
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 10px;
}
.btn-action-complete:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px -4px rgba(239, 68, 68, 0.5);
  color: #ffffff;
}

.btn-action-marks {
  background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
  color: #ffffff;
  border: none;
  font-weight: 700;
  padding: 14px 28px;
  border-radius: 14px;
  font-size: 1.05rem;
  box-shadow: 0 8px 20px -4px rgba(99, 102, 241, 0.4);
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
}
.btn-action-marks:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px -4px rgba(99, 102, 241, 0.5);
  color: #ffffff;
}

.stage-tab-pill {
  padding: 8px 18px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.9rem;
  color: #475569;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  text-decoration: none;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.stage-tab-pill.active {
  background: #4f46e5;
  color: #ffffff;
  border-color: #4f46e5;
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
}

.delay-banner {
  background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
  border: 1px solid #fca5a5;
  color: #991b1b;
  border-radius: 14px;
  padding: 14px 20px;
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 600;
  margin-bottom: 20px;
}

.on-time-banner {
  background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
  border: 1px solid #86efac;
  color: #166534;
  border-radius: 14px;
  padding: 14px 20px;
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 600;
  margin-bottom: 20px;
}
</style>

<div class="sm-console container-fluid">
  <!-- Top Hero Header -->
  <div class="sm-hero">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
      <div>
        <div class="d-flex align-items-center gap-2 mb-2">
          <span class="sm-badge sm-badge-live">
            <span class="radar-dot"></span> Stage Operations Center
          </span>
          <span class="badge bg-light text-dark px-3 py-2 rounded-pill font-weight-bold">
            <?= htmlspecialchars($selected_stage['stage_name'] ?? 'Stage') ?>
          </span>
        </div>
        <h2 class="h3 font-weight-bold text-white mb-1">
          Stage Manager Console
        </h2>
        <p class="text-white-50 small mb-0">
          Real-Time Competition Dispatcher, Timing Controller & Mark Entry Gateway
        </p>
      </div>

      <div class="text-right">
        <div class="small text-white-50">Assigned Stage Manager</div>
        <div class="font-weight-bold text-white fs-5">
          <i class="fa fa-user-circle mr-1"></i>
          <?= htmlspecialchars($manager_info['name'] ?? $manager_info['user_name'] ?? 'Active Coordinator') ?>
        </div>
        <div class="badge bg-secondary text-white mt-1">
          Date: <?= date('d M Y', strtotime($target_date)) ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Stage Switcher & Date Tabs -->
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <!-- Stage Selector -->
    <div class="d-flex flex-wrap gap-2 align-items-center">
      <span class="small font-weight-bold text-muted mr-2">STAGES:</span>
      <?php if (!empty($assigned_stages)): ?>
        <?php foreach ($assigned_stages as $st): ?>
          <a href="<?= base_url('stage/manager?stage_id=' . $st['stage_id'] . '&date=' . urlencode($target_date)) ?>"
             class="stage-tab-pill <?= ((int)$st['stage_id'] === (int)($selected_stage['stage_id'] ?? 0)) ? 'active' : '' ?>">
            <i class="fa fa-television"></i>
            <?= htmlspecialchars($st['stage_name']) ?>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <span class="badge bg-warning text-dark">No stages assigned yet</span>
      <?php endif; ?>
    </div>

    <!-- Festival Date Selector -->
    <div class="d-flex flex-wrap gap-2 align-items-center">
      <span class="small font-weight-bold text-muted mr-2">DATE:</span>
      <?php if (!empty($available_dates)): ?>
        <?php foreach ($available_dates as $d): ?>
          <a href="<?= base_url('stage/manager?stage_id=' . ($selected_stage['stage_id'] ?? 0) . '&date=' . urlencode($d)) ?>"
             class="btn btn-sm <?= ($target_date === $d) ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
            <?= date('D, d M', strtotime($d)) ?>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <span class="btn btn-sm btn-primary rounded-pill px-3"><?= date('D, d M', strtotime($target_date)) ?></span>
      <?php endif; ?>
    </div>
  </div>

  <div class="row">
    <!-- Left Column: Active Item Hero Card -->
    <div class="col-lg-8 mb-4">
      <div class="sm-card p-4 h-100">
        <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
          <h4 class="h5 font-weight-bold mb-0 text-dark">
            <i class="fa fa-play-circle text-primary mr-2"></i>Current Stage Program
          </h4>
          <span class="small text-muted">
            Total Stage Events: <strong><?= $completed_count ?> / <?= $total_events ?> Completed</strong>
          </span>
        </div>

        <?php if ($current_event): ?>
          <!-- Program Status Delay or On-Time Banner -->
          <?php if ($current_event['effective_status'] === 'DELAYED'): ?>
            <div class="delay-banner">
              <span class="radar-dot-red"></span>
              <div>
                <strong>⚠️ Program Running Behind Schedule!</strong>
                <div class="small">
                  Scheduled to start at <?= $current_event['sched_start_time'] ?>. Currently 
                  <strong>Delayed by <?= $current_event['computed_delay'] ?> minutes</strong>.
                  Program will be marked as started only when you click "Start Program".
                </div>
              </div>
            </div>
          <?php elseif ($current_event['effective_status'] === 'STARTED'): ?>
            <div class="on-time-banner">
              <span class="radar-dot"></span>
              <div>
                <strong>🟢 Program is LIVE on Stage!</strong>
                <div class="small">
                  Started at <?= date('h:i A', strtotime($current_event['actual_start_time'])) ?>
                  <?php if ($current_event['computed_delay'] > 0): ?>
                    (Started <?= $current_event['computed_delay'] ?>m behind schedule).
                  <?php else: ?>
                    (Started on schedule).
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php else: ?>
            <div class="alert alert-info py-2 px-3 rounded-12 small mb-3">
              <i class="fa fa-clock-o mr-1"></i> Program scheduled for <strong><?= $current_event['sched_start_time'] ?></strong>. Click "Start Program" when participants and judges are ready.
            </div>
          <?php endif; ?>

          <!-- Active Event Details Card -->
          <div class="bg-light p-4 rounded-18 mb-4 border">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
              <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                  <span class="badge bg-primary text-white px-3 py-1 font-weight-bold">
                    Item #<?= htmlspecialchars($current_event['item_code']) ?>
                  </span>
                  <span class="badge bg-secondary text-white px-2 py-1">
                    <?= htmlspecialchars($current_event['gender'] === 'B' ? 'Boys' : ($current_event['gender'] === 'G' ? 'Girls' : 'General')) ?>
                  </span>
                  <?php if ($current_event['effective_status'] === 'LIVE' || $current_event['effective_status'] === 'STARTED'): ?>
                    <span class="sm-badge sm-badge-live"><span class="radar-dot"></span> LIVE IN PROGRESS</span>
                  <?php elseif ($current_event['effective_status'] === 'DELAYED'): ?>
                    <span class="sm-badge sm-badge-delayed">⚠️ DELAYED by <?= $current_event['computed_delay'] ?> mins</span>
                  <?php else: ?>
                    <span class="sm-badge sm-badge-scheduled">SCHEDULED</span>
                  <?php endif; ?>
                </div>

                <a href="<?= base_url('stage/manager/program/' . $current_event['si_id']) ?>" class="text-decoration-none">
                  <h3 class="h4 font-weight-bold text-dark mb-1">
                    <?= htmlspecialchars($current_event['item_code']) ?> - <?= htmlspecialchars($current_event['item_name']) ?> <i class="fa fa-external-link small text-primary ml-1" style="font-size: 0.8rem;" title="Open Program Console"></i>
                  </h3>
                </a>
              </div>

              <div class="text-right">
                <div class="small text-muted">Allocated Duration</div>
                <div class="h4 font-weight-bold text-primary mb-0">
                  <?= (int)$current_event['duration_minutes'] ?> <span class="fs-6 font-weight-normal text-muted">mins</span>
                </div>
              </div>
            </div>

            <div class="row g-3 text-center pt-3 border-top">
              <div class="col-4 border-right">
                <div class="small text-muted">Scheduled Time</div>
                <div class="font-weight-bold text-dark mt-1">
                  <?= $current_event['sched_start_time'] ?> - <?= $current_event['sched_end_time'] ?>
                </div>
              </div>
              <div class="col-4 border-right">
                <div class="small text-muted">Current Status</div>
                <div class="font-weight-bold mt-1 <?= ($current_event['effective_status'] === 'DELAYED') ? 'text-danger' : (($current_event['effective_status'] === 'STARTED') ? 'text-success' : 'text-primary') ?>">
                  <?= htmlspecialchars($current_event['effective_status']) ?>
                </div>
              </div>
              <div class="col-4">
                <div class="small text-muted">Delay Duration</div>
                <div class="font-weight-bold mt-1 <?= ($current_event['computed_delay'] > 0) ? 'text-danger' : 'text-success' ?>">
                  <?= ($current_event['computed_delay'] > 0) ? ($current_event['computed_delay'] . ' mins Late') : 'On Schedule' ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Dispatch Action Buttons -->
          <div class="d-flex flex-wrap gap-2 align-items-center">
            <!-- Dedicated Console Button -->
            <a href="<?= base_url('stage/manager/program/' . $current_event['si_id']) ?>"
               class="btn btn-primary py-3 px-4 rounded-14 font-weight-bold shadow-sm d-inline-flex align-items-center gap-2">
              <i class="fa fa-sliders"></i> Open Program Console
            </a>

            <?php if ($current_event['effective_status'] !== 'STARTED'): ?>
              <button type="button" class="btn-action-start" id="btnStartProgram" data-id="<?= (int)$current_event['si_id'] ?>">
                <i class="fa fa-play"></i> Start Program
              </button>
            <?php endif; ?>

            <?php if ($current_event['effective_status'] === 'STARTED'): ?>
              <button type="button" class="btn-action-complete" id="btnCompleteProgram" data-id="<?= (int)$current_event['si_id'] ?>">
                <i class="fa fa-check-circle"></i> Complete Program
              </button>
            <?php endif; ?>

            <!-- Participants Modal Trigger Button: Hidden per user request -->
            <?php if (!empty($current_event['can_view_participants']) || !empty($can_view_participants)): ?>
              <button type="button" class="btn btn-outline-primary py-3 px-3 rounded-14 font-weight-bold d-inline-flex align-items-center gap-2 btn-view-participants" data-si-id="<?= (int)$current_event['si_id'] ?>" data-item-code="<?= (int)$current_event['item_code'] ?>" data-item-name="<?= htmlspecialchars($current_event['item_name']) ?>" title="View Participant Details" style="display: none !important;">
                <i class="fa fa-users text-primary"></i> Participants
              </button>
            <?php endif; ?>

            <!-- Mark Entry Option for Stage Manager -->
            <a href="<?= base_url('result/resultentry/index/' . $current_event['item_code']) ?>"
               class="btn-action-marks"
               target="_blank"
               title="Enter competition marks for this item">
              <i class="fa fa-pencil-square-o"></i> Enter Marks
            </a>

            <!-- Quick Call Sheet View -->
            <a href="<?= base_url('prereport/call_date_stage/' . $selected_stage['stage_id'] . '/' . urlencode($target_date)) ?>"
               target="_blank"
               class="btn btn-outline-secondary py-3 px-4 rounded-14 font-weight-bold">
              <i class="fa fa-list-alt mr-1"></i> Call Sheet
            </a>
          </div>

        <?php else: ?>
          <div class="text-center py-5">
            <div class="display-4 text-muted mb-3"><i class="fa fa-check-circle-o text-success"></i></div>
            <h4 class="h5 font-weight-bold text-dark">All Scheduled Events Completed!</h4>
            <p class="text-muted small">No more pending competitions scheduled on this stage for <?= date('d M Y', strtotime($target_date)) ?>.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Right Column: Stage Information & Timeline -->
    <div class="col-lg-4 mb-4">
      <div class="sm-card p-4 h-100">
        <h4 class="h6 font-weight-bold text-uppercase tracking-wider text-muted mb-3">
          <i class="fa fa-calendar mr-1"></i> Stage Schedule Queue (<?= count($stage_events) ?>)
        </h4>

        <?php if (!empty($stage_events)): ?>
          <div class="list-group list-group-flush">
            <?php foreach ($stage_events as $idx => $ev): ?>
              <div class="list-group-item px-0 py-3 d-flex align-items-center justify-content-between <?= ($current_event && $current_event['si_id'] === $ev['si_id']) ? 'bg-light px-2 rounded-12' : '' ?>">
                <div>
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-dark text-white" style="font-size: 0.7rem;">#<?= $ev['item_code'] ?></span>
                    <a href="<?= base_url('stage/manager/program/' . $ev['si_id']) ?>" class="small font-weight-bold text-dark text-decoration-none">
                      <?= htmlspecialchars($ev['item_name']) ?> <i class="fa fa-angle-right text-muted" style="font-size: 0.75rem;"></i>
                    </a>
                  </div>
                  <div class="small text-muted">
                    <i class="fa fa-clock-o mr-1"></i> <?= $ev['sched_start_time'] ?> (<?= $ev['duration_minutes'] ?>m)
                  </div>
                </div>

                <div class="text-right">
                  <?php if ($ev['effective_status'] === 'COMPLETED'): ?>
                    <span class="badge bg-secondary text-white"><i class="fa fa-check"></i> Done</span>
                  <?php elseif ($ev['effective_status'] === 'STARTED'): ?>
                    <span class="badge bg-success text-white">LIVE</span>
                  <?php elseif ($ev['effective_status'] === 'DELAYED'): ?>
                    <span class="badge bg-danger text-white"><?= $ev['computed_delay'] ?>m Late</span>
                  <?php else: ?>
                    <span class="badge bg-info text-white">Waiting</span>
                  <?php endif; ?>
                  <div class="mt-1 d-flex gap-2 justify-content-end align-items-center">
                    <?php if (!empty($ev['can_view_participants']) || !empty($can_view_participants)): ?>
                      <button type="button" class="btn btn-xs btn-outline-info rounded-pill px-2 py-0 btn-view-participants" data-si-id="<?= $ev['si_id'] ?>" data-item-code="<?= $ev['item_code'] ?>" data-item-name="<?= htmlspecialchars($ev['item_name']) ?>" style="font-size: 0.72rem; display: none !important;">
                        <i class="fa fa-users"></i> Participants
                      </button>
                    <?php endif; ?>
                    <a href="<?= base_url('stage/manager/program/' . $ev['si_id']) ?>" class="small text-primary font-weight-bold">Console &rarr;</a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="text-center py-4 text-muted small">
            No events scheduled on this stage for the selected date.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Beautiful Participant Details Modal -->
<div class="modal fade" id="participantDetailsModal" tabindex="-1" aria-labelledby="partModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg rounded-20 overflow-hidden">
      <div class="modal-header bg-primary text-white py-3 px-4">
        <div>
          <h5 class="modal-title font-weight-bold d-flex align-items-center gap-2 mb-1" id="partModalTitle">
            <i class="fa fa-users"></i>
            <span id="modalItemTitle">Participant Details</span>
          </h5>
          <div class="small text-white-50" id="modalItemSubTitle">
            Stage &amp; Sub-District Participant Roster
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4 bg-light">
        <!-- Quick Stats Banner -->
        <div class="row g-3 mb-3 text-center">
          <div class="col-sm-3 col-6">
            <div class="bg-white p-3 rounded-14 border shadow-sm">
              <div class="small text-muted">Total Participants</div>
              <div class="h4 font-weight-bold text-primary mb-0" id="modalTotalCount">0</div>
            </div>
          </div>
          <div class="col-sm-3 col-6">
            <div class="bg-white p-3 rounded-14 border shadow-sm">
              <div class="small text-muted">Current Performer</div>
              <div class="h4 font-weight-bold text-success mb-0" id="modalCurrentLot">Lot #--</div>
            </div>
          </div>
          <div class="col-sm-3 col-6">
            <div class="bg-white p-3 rounded-14 border shadow-sm">
              <div class="small text-muted">Sub-District</div>
              <div class="h6 font-weight-bold text-dark mb-0 text-truncate" id="modalSubDistrict">--</div>
            </div>
          </div>
          <div class="col-sm-3 col-6">
            <div class="bg-white p-3 rounded-14 border shadow-sm">
              <div class="small text-muted">Stage Venue</div>
              <div class="h6 font-weight-bold text-dark mb-0 text-truncate" id="modalStageName">--</div>
            </div>
          </div>
        </div>

        <!-- Filter / Search Box -->
        <div class="input-group mb-3 shadow-sm">
          <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
          <input type="text" class="form-control border-start-0 ps-0" id="modalParticipantSearch" placeholder="Search participant name, school, admission no, sub-district...">
        </div>

        <!-- Participants Table -->
        <div class="bg-white rounded-14 border shadow-sm overflow-hidden">
          <div class="table-responsive" style="max-height: 460px;">
            <table class="table table-hover align-middle mb-0" id="modalParticipantTable">
              <thead class="bg-light sticky-top">
                <tr>
                  <th style="width: 70px;" class="text-center">Lot #</th>
                  <th>பெயர் (Participant Name)</th>
                  <th>பள்ளி (School Name)</th>
                  <th>சப்டிஸ்டிக் (Sub-District)</th>
                  <th style="width: 100px;">மாவட்டம் (District)</th>
                  <th style="width: 90px;" class="text-center">Admn No</th>
                  <th style="width: 70px;" class="text-center">Class</th>
                  <th style="width: 70px;" class="text-center">Gender</th>
                  <th style="width: 90px;" class="text-center">Status</th>
                </tr>
              </thead>
              <tbody id="modalParticipantTableBody">
                <tr>
                  <td colspan="9" class="text-center py-5 text-muted">
                    <i class="fa fa-spinner fa-spin fa-2x mb-2 text-primary d-block"></i>
                    Loading participant details from database...
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="modal-footer bg-white py-3 px-4 border-top d-flex justify-content-between">
        <div class="small text-muted">
          <i class="fa fa-shield text-success mr-1"></i> Direct database records for Stage Manager coordination.
        </div>
        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const btnStart = document.getElementById('btnStartProgram');
  const btnComplete = document.getElementById('btnCompleteProgram');

  if (btnStart) {
    btnStart.addEventListener('click', function() {
      const siId = this.getAttribute('data-id');
      if (!confirm('Are you sure you want to mark this program as STARTED on stage now?')) return;

      this.disabled = true;
      this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Starting...';

      const formData = new FormData();
      formData.append('si_id', siId);
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      if (csrfToken) formData.append('csrf_test_name', csrfToken);

      fetch('<?= base_url('stage/manager/start_item') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          alert('Program Started! Live timer has been activated.');
          window.location.reload();
        } else {
          alert('Error: ' + data.message);
          btnStart.disabled = false;
          btnStart.innerHTML = '<i class="fa fa-play"></i> Start Program';
        }
      })
      .catch(err => {
        alert('Server connection error. Please try again.');
        btnStart.disabled = false;
        btnStart.innerHTML = '<i class="fa fa-play"></i> Start Program';
      });
    });
  }

  if (btnComplete) {
    btnComplete.addEventListener('click', function() {
      const siId = this.getAttribute('data-id');
      if (!confirm('Mark this competition as COMPLETED? Next scheduled item will be loaded.')) return;

      this.disabled = true;
      this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Completing...';

      const formData = new FormData();
      formData.append('si_id', siId);
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      if (csrfToken) formData.append('csrf_test_name', csrfToken);

      fetch('<?= base_url('stage/manager/complete_item') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          alert('Program completed successfully!');
          window.location.reload();
        } else {
          alert('Error: ' + data.message);
          btnComplete.disabled = false;
          btnComplete.innerHTML = '<i class="fa fa-check-circle"></i> Complete Program';
        }
      })
      .catch(err => {
        alert('Server connection error. Please try again.');
        btnComplete.disabled = false;
        btnComplete.innerHTML = '<i class="fa fa-check-circle"></i> Complete Program';
      });
    });
  }

  // PARTICIPANT MODAL HANDLER
  function openParticipantsModal(pSiId, pItemCode, pDefaultName) {
    const modalEl = document.getElementById('participantDetailsModal');
    if (!modalEl) return;

    let modalInstance = null;
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
      modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    } else if (typeof $ !== 'undefined' && $(modalEl).modal) {
      modalInstance = { show: () => $(modalEl).modal('show'), hide: () => $(modalEl).modal('hide') };
    }

    const titleEl = document.getElementById('modalItemTitle');
    const subTitleEl = document.getElementById('modalItemSubTitle');
    const tbody = document.getElementById('modalParticipantTableBody');

    if (titleEl) titleEl.textContent = pDefaultName || 'Participant Details';
    if (subTitleEl) subTitleEl.textContent = 'Fetching student records directly from database...';
    if (tbody) {
      tbody.innerHTML = `
        <tr>
          <td colspan="9" class="text-center py-5 text-muted">
            <i class="fa fa-spinner fa-spin fa-2x mb-2 text-primary d-block"></i>
            Loading participant details directly from database...
          </td>
        </tr>`;
    }
    if (modalInstance) modalInstance.show();

    let fetchUrl = '<?= base_url('stage/manager/get_participants_modal') ?>?';
    if (pSiId) fetchUrl += 'si_id=' + encodeURIComponent(pSiId);
    else if (pItemCode) fetchUrl += 'item_code=' + encodeURIComponent(pItemCode);

    fetch(fetchUrl)
      .then(r => r.json())
      .then(data => {
        if (!data.success) {
          if (tbody) {
            tbody.innerHTML = `
              <tr>
                <td colspan="9" class="text-center py-4 text-danger">
                  <i class="fa fa-exclamation-triangle mr-1"></i> ${data.message || 'Failed to load participants.'}
                </td>
              </tr>`;
          }
          return;
        }

        if (titleEl) titleEl.textContent = 'Item #' + data.item_code + ' - ' + data.item_name;
        if (subTitleEl) subTitleEl.textContent = 'Stage: ' + (data.stage_name || 'N/A') + ' | Sub-District: ' + (data.sub_district_name || 'N/A') + ' | Total: ' + data.total_participants + ' Participants';
        
        const totCountEl = document.getElementById('modalTotalCount');
        const curLotEl = document.getElementById('modalCurrentLot');
        const subDistEl = document.getElementById('modalSubDistrict');
        const stageNameEl = document.getElementById('modalStageName');
        if (totCountEl) totCountEl.textContent = data.total_participants;
        if (curLotEl) curLotEl.textContent = 'Lot #' + data.current_lot_no;
        if (subDistEl) subDistEl.textContent = data.sub_district_name || 'All';
        if (stageNameEl) stageNameEl.textContent = data.stage_name || 'N/A';

        if (!data.participants || data.participants.length === 0) {
          if (tbody) {
            tbody.innerHTML = `
              <tr>
                <td colspan="9" class="text-center py-4 text-muted">
                  No participants registered for this event in your sub-district jurisdiction.
                </td>
              </tr>`;
          }
          return;
        }

        let rowsHtml = '';
        data.participants.forEach((p) => {
          const lotNo = p.lot_no;
          const isOnStage = (p.lot_status === 'On Stage');
          const isDone = (p.lot_status === 'Done');
          let statusBadge = '<span class="badge bg-light text-muted border">Waiting</span>';
          if (isDone) {
            statusBadge = '<span class="badge bg-secondary text-white"><i class="fa fa-check"></i> Done</span>';
          } else if (isOnStage) {
            statusBadge = '<span class="badge bg-success text-white"><span class="radar-dot" style="width:6px;height:6px;"></span> On Stage</span>';
          }

          const searchIndex = (p.participant_name + ' ' + (p.school_name || '') + ' ' + (p.sub_district_name || '') + ' ' + (p.admn_no || '')).toLowerCase();

          rowsHtml += `
            <tr class="${isOnStage ? 'table-success fw-bold' : ''}" data-search="${searchIndex}">
              <td class="text-center font-weight-bold text-primary">#${lotNo}</td>
              <td class="font-weight-bold text-dark">${p.participant_name || ''}</td>
              <td class="small text-muted">${p.school_name || ''}</td>
              <td><span class="badge bg-info text-white">${p.sub_district_name || ''}</span></td>
              <td class="small text-muted">${p.rev_district_name || ''}</td>
              <td class="text-center font-monospace text-secondary">${p.admn_no || ''}</td>
              <td class="text-center">${p.class || ''}</td>
              <td class="text-center">
                <span class="badge bg-light text-dark border">
                  ${p.gender === 'B' ? 'Boys' : (p.gender === 'G' ? 'Girls' : 'Gen')}
                </span>
              </td>
              <td class="text-center">${statusBadge}</td>
            </tr>`;
        });
        if (tbody) tbody.innerHTML = rowsHtml;
      })
      .catch(err => {
        if (tbody) {
          tbody.innerHTML = `
            <tr>
              <td colspan="9" class="text-center py-4 text-danger">
                <i class="fa fa-exclamation-triangle mr-1"></i> Failed to communicate with server.
              </td>
            </tr>`;
        }
      });
  }

  // Bind trigger buttons
  document.querySelectorAll('.btn-view-participants').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const pSiId = this.getAttribute('data-si-id');
      const pItemCode = this.getAttribute('data-item-code');
      const pItemName = this.getAttribute('data-item-name');
      openParticipantsModal(pSiId, pItemCode, pItemName);
    });
  });

  // Client-side search inside modal
  const modalSearch = document.getElementById('modalParticipantSearch');
  if (modalSearch) {
    modalSearch.addEventListener('input', function() {
      const q = this.value.toLowerCase().trim();
      const rows = document.querySelectorAll('#modalParticipantTableBody tr[data-search]');
      rows.forEach(r => {
        const text = r.getAttribute('data-search') || '';
        r.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }
});
</script>
