<?php
/**
 * Dedicated Program Control Console for Stage Manager
 * Features:
 * - Real-Time Timing & Delay Tracker
 * - Start Program, Pause, Resume, Finish Controls
 * - Confidential Participant Roster (Strictly hidden until program starts; 100% Read-Only)
 * - Lot Range Configurator (Start/End lot, dynamic code digits)
 * - Live Participant Stepper ("Participant 1 Finished -> Next Participant 2")
 * - Sub-District Admin Mark Entry Gating
 */
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap');

.prog-console {
  font-family: 'Outfit', 'Poppins', system-ui, -apple-system, sans-serif;
  color: #0f172a;
  background: #f8fafc;
  min-height: 100vh;
  padding: 24px 15px;
}

.prog-card {
  background: #ffffff;
  border-radius: 18px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05);
  transition: all 0.25s ease;
  overflow: hidden;
}

.prog-hero {
  background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
  color: #ffffff;
  padding: 28px 32px;
  border-radius: 20px;
  margin-bottom: 24px;
  position: relative;
  overflow: hidden;
}

.prog-hero::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -10%;
  width: 380px;
  height: 380px;
  background: radial-gradient(circle, rgba(99, 102, 241, 0.28) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}

.badge-live {
  background: rgba(16, 185, 129, 0.15);
  color: #059669;
  border: 1px solid rgba(16, 185, 129, 0.3);
}

.badge-delayed {
  background: rgba(239, 68, 68, 0.15);
  color: #dc2626;
  border: 1px solid rgba(239, 68, 68, 0.3);
  animation: pulseBlink 1.5s infinite;
}

.badge-scheduled {
  background: rgba(59, 130, 246, 0.15);
  color: #2563eb;
  border: 1px solid rgba(59, 130, 246, 0.3);
}

.badge-completed {
  background: rgba(100, 116, 139, 0.15);
  color: #475569;
  border: 1px solid rgba(100, 116, 139, 0.3);
}

.badge-paused {
  background: rgba(245, 158, 11, 0.15);
  color: #d97706;
  border: 1px solid rgba(245, 158, 11, 0.3);
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

.btn-ctrl-start {
  background: linear-gradient(135deg, #059669 0%, #10b981 100%);
  color: #ffffff;
  border: none;
  font-weight: 700;
  padding: 13px 26px;
  border-radius: 14px;
  font-size: 1rem;
  box-shadow: 0 6px 18px -3px rgba(16, 185, 129, 0.4);
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.btn-ctrl-start:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 22px -3px rgba(16, 185, 129, 0.5);
  color: #ffffff;
}

.btn-ctrl-pause {
  background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
  color: #ffffff;
  border: none;
  font-weight: 700;
  padding: 13px 24px;
  border-radius: 14px;
  font-size: 1rem;
  box-shadow: 0 6px 18px -3px rgba(245, 158, 11, 0.4);
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.btn-ctrl-pause:hover {
  transform: translateY(-2px);
  color: #ffffff;
}

.btn-ctrl-resume {
  background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
  color: #ffffff;
  border: none;
  font-weight: 700;
  padding: 13px 24px;
  border-radius: 14px;
  font-size: 1rem;
  box-shadow: 0 6px 18px -3px rgba(59, 130, 246, 0.4);
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.btn-ctrl-resume:hover {
  transform: translateY(-2px);
  color: #ffffff;
}

.btn-ctrl-finish {
  background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
  color: #ffffff;
  border: none;
  font-weight: 700;
  padding: 13px 26px;
  border-radius: 14px;
  font-size: 1rem;
  box-shadow: 0 6px 18px -3px rgba(239, 68, 68, 0.4);
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.btn-ctrl-finish:hover {
  transform: translateY(-2px);
  color: #ffffff;
}

.btn-ctrl-marks {
  background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
  color: #ffffff;
  border: none;
  font-weight: 700;
  padding: 13px 26px;
  border-radius: 14px;
  font-size: 1rem;
  box-shadow: 0 6px 18px -3px rgba(99, 102, 241, 0.4);
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
}
.btn-ctrl-marks:hover {
  transform: translateY(-2px);
  color: #ffffff;
}

.stepper-card {
  background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
  border: 2px solid #86efac;
  border-radius: 18px;
  padding: 24px;
}

.stepper-btn {
  background: linear-gradient(135deg, #059669 0%, #10b981 100%);
  color: #ffffff;
  border: none;
  font-weight: 800;
  padding: 16px 32px;
  border-radius: 14px;
  font-size: 1.15rem;
  box-shadow: 0 8px 24px -4px rgba(16, 185, 129, 0.45);
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 10px;
}
.stepper-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 28px -4px rgba(16, 185, 129, 0.55);
  color: #ffffff;
}

.locked-card {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border: 2px dashed #cbd5e1;
  border-radius: 18px;
  padding: 48px 24px;
  text-align: center;
}

.delay-alert-banner {
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

.prog-active-btn-row {
  display: flex !important;
  flex-direction: row !important;
  flex-wrap: nowrap !important;
  width: 100% !important;
  gap: 10px !important;
}

.prog-active-btn-row .btn-ctrl-pause,
.prog-active-btn-row .btn-ctrl-resume,
.prog-active-btn-row .btn-ctrl-finish {
  flex: 1 1 50% !important;
  min-width: 0 !important;
  justify-content: center !important;
  text-align: center !important;
  white-space: nowrap !important;
  padding: 13px 14px !important;
}

@media (max-width: 576px) {
  .prog-active-btn-row {
    gap: 6px !important;
  }
  .prog-active-btn-row .btn-ctrl-pause,
  .prog-active-btn-row .btn-ctrl-resume,
  .prog-active-btn-row .btn-ctrl-finish {
    padding: 10px 8px !important;
    font-size: 0.85rem !important;
    border-radius: 12px !important;
  }
  .btn-ctrl-start {
    width: 100% !important;
    justify-content: center !important;
    padding: 12px 14px !important;
    font-size: 0.95rem !important;
  }
}
</style>

<div class="prog-console container-fluid">
  <!-- Navigation Header & Breadcrumbs -->
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 bg-transparent p-0">
        <li class="breadcrumb-item"><a href="<?= base_url('welcome') ?>" class="text-decoration-none">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('stage/manager?stage_id=' . $item['stage_id']) ?>" class="text-decoration-none">Stage Manager</a></li>
        <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Item #<?= $item['item_code'] ?> - <?= htmlspecialchars($item['item_name']) ?> (<?= $participant_count ?> <?= ($participant_count === 1) ? 'Participant' : 'Participants' ?>)</li>
      </ol>
    </nav>

    <a href="<?= base_url('stage/manager?stage_id=' . $item['stage_id']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
      <i class="fa fa-arrow-left mr-1"></i> Back to Stage Dashboard
    </a>
  </div>

  <!-- Hero Header -->
  <div class="prog-hero">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
      <div>
        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
          <span class="badge bg-light text-dark px-3 py-1 rounded-pill font-weight-bold">
            <i class="fa fa-television text-primary mr-1"></i> <?= htmlspecialchars($item['stage_name']) ?>
          </span>
          <span class="badge bg-primary text-white px-3 py-1 font-weight-bold">
            Item #<?= htmlspecialchars($item['item_code']) ?>
          </span>
          <span class="badge bg-secondary text-white px-2 py-1">
            <?= htmlspecialchars($item['gender'] === 'B' ? 'Boys' : ($item['gender'] === 'G' ? 'Girls' : 'General')) ?>
          </span>
          <span id="badgeStatus" class="badge <?= ($status === 'STARTED') ? 'badge-live' : (($status === 'DELAYED') ? 'badge-delayed' : (($status === 'PAUSED') ? 'badge-paused' : (($status === 'COMPLETED') ? 'badge-completed' : 'badge-scheduled'))) ?> px-3 py-1 rounded-pill">
            <?php if ($status === 'STARTED'): ?>
              <span class="radar-dot mr-1"></span> LIVE ON STAGE
            <?php elseif ($status === 'DELAYED'): ?>
              ⚠️ DELAYED by <?= $computed_delay ?> mins
            <?php elseif ($status === 'PAUSED'): ?>
              ⏸️ PAUSED
            <?php elseif ($status === 'COMPLETED'): ?>
              ✓ COMPLETED
            <?php else: ?>
              SCHEDULED
            <?php endif; ?>
          </span>
        </div>

        <h1 class="h3 font-weight-bold text-white mb-1 d-flex flex-wrap align-items-center gap-2">
          <span><?= htmlspecialchars($item['item_code']) ?> - <?= htmlspecialchars($item['item_name']) ?></span>
          <span class="text-white-50 fs-5 font-weight-normal" id="headerPartCount">
            (<?= $participant_count ?> <?= ($participant_count === 1) ? 'Participant' : 'Participants' ?>)
          </span>
        </h1>
        <p class="text-white-50 small mb-0">
          Stage Manager Dedicated Dispatcher &amp; Timing Console
        </p>
      </div>

      <div class="text-right">
        <div class="small text-white-50">Scheduled Timing</div>
        <div class="h4 font-weight-bold text-white mb-0">
          <?= $sched_start_time ?> - <?= $sched_end_time ?>
        </div>
        <div class="small text-white-50">
          Duration: <?= $duration_minutes ?> mins | Participants: <?= $participant_count ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Delay or Live Notice Banner -->
  <?php if ($status === 'DELAYED'): ?>
    <div class="delay-alert-banner" id="delayNotice">
      <span class="radar-dot-red"></span>
      <div>
        <strong>⚠️ Competition Running Behind Schedule!</strong>
        <div class="small">
          Scheduled to begin at <?= $sched_start_time ?>. It is running 
          <strong><?= $computed_delay ?> minutes late</strong>. Program status remains scheduled until you click "Start Program".
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="row">
    <!-- Left Column: Controls, Stepper & Participant List -->
    <div class="col-lg-8 mb-4">
      
      <!-- Primary Operation Control Card -->
      <div class="prog-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
          <h4 class="h5 font-weight-bold text-dark mb-0">
            <i class="fa fa-cogs text-primary mr-2"></i>Program Dispatch Controls
          </h4>
          <span class="small text-muted" id="timingStatusLabel">
            Status: <strong><?= htmlspecialchars($status) ?></strong>
          </span>
        </div>

        <?php 
          $lotSetupLocked = in_array($status, ['STARTED', 'PAUSED', 'COMPLETED'], true);
          $isMarksActive = ($status === 'COMPLETED' && !$item_confirmed && ($allow_stage_manager_marks === 'Y' || !empty($can_enter_marks)));
        ?>

        <!-- Dispatch Buttons Toolbar -->
        <div class="mb-3">
          <!-- Start Program Button (Visible when Scheduled or Delayed) -->
          <button type="button" class="btn-ctrl-start w-100 justify-content-center mb-2" id="btnStartProgram" data-id="<?= (int)$item['si_id'] ?>" style="<?= ($status === 'STARTED' || $status === 'COMPLETED') ? 'display:none !important;' : '' ?>">
            <i class="fa fa-play mr-1"></i> Start Program
          </button>

          <!-- Operational Action Pair: Always in the EXACT SAME single horizontal row side-by-side -->
          <div class="prog-active-btn-row mb-2" id="activeOpsRow" style="<?= ($status === 'STARTED' || $status === 'PAUSED') ? 'display:flex !important;' : 'display:none !important;' ?>">
            <!-- Pause Program Button -->
            <button type="button" class="btn-ctrl-pause flex-fill" id="btnPauseProgram" data-id="<?= (int)$item['si_id'] ?>" style="<?= ($status === 'STARTED') ? 'display:inline-flex !important;' : 'display:none !important;' ?>">
              <i class="fa fa-pause mr-1"></i> Pause Program
            </button>

            <!-- Resume Program Button -->
            <button type="button" class="btn-ctrl-resume flex-fill" id="btnResumeProgram" data-id="<?= (int)$item['si_id'] ?>" style="<?= ($status === 'PAUSED') ? 'display:inline-flex !important;' : 'display:none !important;' ?>">
              <i class="fa fa-play-circle mr-1"></i> Resume Program
            </button>

            <!-- Finish Program Button -->
            <button type="button" class="btn-ctrl-finish flex-fill" id="btnCompleteProgram" data-id="<?= (int)$item['si_id'] ?>" style="<?= ($status === 'STARTED' || $status === 'PAUSED') ? 'display:inline-flex !important;' : 'display:none !important;' ?>">
              <i class="fa fa-check-circle mr-1"></i> Finish Program
            </button>
          </div>

          <!-- Secondary Toolbar: Mark Entry & Participant Details Modal -->
          <div class="d-flex flex-wrap gap-2 align-items-center">
            <!-- Mark Entry Button: Gated by Sub-District Admin / Permissions -->
            <div id="marksToolbarContainer" class="flex-fill">
              <?php if ($isMarksActive): ?>
                <a href="<?= base_url('result/resultentry/index/' . $item['item_code']) ?>"
                   class="btn-ctrl-marks w-100 justify-content-center"
                   id="btnEnterMarks"
                   target="_blank"
                   title="Authorized to record competition marks">
                  <i class="fa fa-pencil-square-o mr-1"></i> Enter Marks
                </a>
              <?php else: ?>
                <button type="button" class="btn btn-secondary py-3 px-3 rounded-14 font-weight-bold w-100 justify-content-center d-inline-flex align-items-center" id="btnEnterMarksLocked" title="Click to view permission details">
                  <i class="fa fa-lock mr-1"></i> Marks Locked by Admin
                </button>
              <?php endif; ?>
            </div>

            <!-- View Participant Details Modal Trigger Button: Hidden per user request -->
            <div style="display: none !important;">
            <?php if (!empty($can_view_participants)): ?>
              <button type="button" class="btn btn-outline-primary py-3 px-3 rounded-14 font-weight-bold flex-fill d-inline-flex align-items-center justify-content-center gap-2 btn-open-part-modal" data-si-id="<?= (int)$item['si_id'] ?>" data-item-code="<?= (int)$item['item_code'] ?>" data-item-name="<?= htmlspecialchars($item['item_name']) ?>" title="View student participant details in modal" style="display: none !important;">
                <i class="fa fa-users text-primary"></i>
                <span>View Participants (Modal)</span>
              </button>
            <?php else: ?>
              <button type="button" class="btn btn-outline-secondary py-3 px-3 rounded-14 font-weight-bold flex-fill d-inline-flex align-items-center justify-content-center gap-2" id="btnParticipantsLocked" title="Participant details are confidential and restricted by admin" disabled style="display: none !important;">
                <i class="fa fa-lock text-muted"></i>
                <span>Participants Confidential</span>
              </button>
            <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="small text-muted">
          <i class="fa fa-info-circle mr-1 text-primary"></i>
          Click <strong>Start Program</strong> to commence competition timing. Progress through participants one by one using the live stepper below.
        </div>
      </div>

      <!-- Live Participant Stepper Card ("Participant 1 Finished -> Next Participant 2") -->
      <div class="stepper-card mb-4" id="stepperSection" style="<?= ($status === 'STARTED' || $status === 'PAUSED') ? '' : 'display:none;' ?>">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
          <div>
            <span class="badge bg-success text-white px-3 py-1 rounded-pill mb-1">
              <i class="fa fa-microphone mr-1"></i> LIVE ON STAGE
            </span>
            <h3 class="h4 font-weight-bold text-dark mb-0">
              Current Performer: <span class="text-success" id="displayCurrentLot">Lot #<?= $current_lot_no ?></span>
              <span class="text-muted fs-6 font-weight-normal"> (Range: <?= $lot_start ?> to <?= $lot_end ?>)</span>
            </h3>
          </div>

          <div class="text-right">
            <span class="badge bg-white text-dark border px-3 py-2 rounded-pill font-weight-bold" id="stepperProgressBadge">
              Lot <?= $current_lot_no ?> of <?= $lot_end ?>
            </span>
          </div>
        </div>

        <div class="p-3 bg-white rounded-14 border mb-3 shadow-sm">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
              <div class="small text-muted">Active Competition Lot</div>
              <div class="h2 font-weight-bold text-dark mb-0" id="stepperBigLot">
                Lot #<?= $current_lot_no ?>
              </div>
            </div>

            <div>
              <button type="button" class="stepper-btn" id="btnNextParticipant" data-id="<?= (int)$item['si_id'] ?>">
                <i class="fa fa-check-square-o"></i>
                <span id="btnNextText">Mark Lot #<?= $current_lot_no ?> Finished &amp; Next Lot</span>
              </button>
            </div>
          </div>
        </div>

        <div class="small text-success font-weight-bold">
          <i class="fa fa-check-circle mr-1"></i> Stepper automatically tracks participant lot advancement and finishes when all lots perform.
        </div>
      </div>

      <!-- Participant Roster Card: Hidden per user request -->
      <div class="prog-card p-4 d-none" id="participantRosterCard" style="display: none !important;">
        <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom flex-wrap gap-2">
          <div>
            <h4 class="h5 font-weight-bold text-dark mb-0">
              <i class="fa fa-users text-primary mr-2"></i>Participant Roster (<?= $participant_count ?>)
            </h4>
            <div class="small text-muted">Direct Database Records: Name, School &amp; Sub-District</div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-muted border px-3 py-1 rounded-pill">
              <?= (!empty($can_view_participants)) ? 'Read-Only' : 'Confidential' ?>
            </span>
          </div>
        </div>

        <?php if (!empty($can_view_participants)): ?>
          <div id="rosterTableBox">
            <div class="alert alert-info py-2 px-3 rounded-12 small mb-3">
              <i class="fa fa-info-circle mr-1"></i> <strong>Participant Details:</strong> Showing all participants registered in your sub-district jurisdiction for this event.
            </div>

            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0" id="participantTable">
                <thead class="bg-light">
                  <tr>
                    <th style="width: 70px;">Lot #</th>
                    <th style="width: 100px;">Admn No</th>
                    <th>பெயர் (Participant Name)</th>
                    <th>பள்ளி (School Name)</th>
                    <th>சப்டிஸ்டிக் (Sub-District)</th>
                    <th style="width: 75px;" class="text-center">Class</th>
                    <th style="width: 75px;" class="text-center">Gender</th>
                    <th style="width: 110px;" class="text-center">Status</th>
                  </tr>
                </thead>
                <tbody id="participantTableBody">
                  <?php if (!empty($participants)): ?>
                    <?php foreach ($participants as $index => $p): ?>
                      <?php 
                        $lotNum = $lot_start + $index;
                        $isDone = ($current_lot_no > $lotNum);
                        $isOnStage = ($current_lot_no === $lotNum);
                      ?>
                      <tr class="<?= $isOnStage ? 'table-success fw-bold' : '' ?>" id="pRow_<?= $lotNum ?>" data-lot="<?= $lotNum ?>">
                        <td class="font-weight-bold text-primary">#<?= $lotNum ?></td>
                        <td class="font-monospace text-secondary"><?= htmlspecialchars($p['admn_no'] ?? '') ?></td>
                        <td class="font-weight-bold text-dark"><?= htmlspecialchars($p['participant_name'] ?? '') ?></td>
                        <td class="small text-muted"><?= htmlspecialchars($p['school_name'] ?? 'School') ?></td>
                        <td>
                          <span class="badge bg-info text-white"><?= htmlspecialchars($p['sub_district_name'] ?? ($item['sub_district_name'] ?? 'Chittur')) ?></span>
                        </td>
                        <td class="text-center"><?= htmlspecialchars($p['class'] ?? '') ?></td>
                        <td class="text-center">
                          <span class="badge bg-light text-dark border">
                            <?= ($p['gender'] === 'B') ? 'Boys' : (($p['gender'] === 'G') ? 'Girls' : 'Gen') ?>
                          </span>
                        </td>
                        <td class="text-center lot-status-cell">
                          <?php if ($status === 'COMPLETED' || $isDone): ?>
                            <span class="badge bg-secondary text-white"><i class="fa fa-check"></i> Done</span>
                          <?php elseif ($isOnStage): ?>
                            <span class="badge bg-success text-white"><span class="radar-dot" style="width:6px;height:6px;"></span> On Stage</span>
                          <?php else: ?>
                            <span class="badge bg-light text-muted border">Waiting</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="8" class="text-center py-4 text-muted">
                        No participants registered for this event yet.
                      </td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php else: ?>
          <div class="alert alert-secondary p-4 text-center rounded-14 border mb-0" id="rosterLockedNotice">
            <i class="fa fa-lock fa-3x text-muted mb-3 d-block"></i>
            <h5 class="font-weight-bold text-dark mb-2">Participant Details Confidential</h5>
            <p class="text-muted small mb-0" style="max-width: 580px; margin: 0 auto;">
              Student participant identities, school assignments, and admission numbers are strictly confidential to prevent evaluation bias. Viewing this roster requires explicit authorization from the Festival Administrator.
            </p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Right Column: Lot Configuration & Information -->
    <div class="col-lg-4 mb-4">
      
      <!-- Lot Number Range Settings Card -->
      <div class="prog-card p-4 mb-4">
        <h4 class="h6 font-weight-bold text-uppercase tracking-wider text-muted mb-3">
          <i class="fa fa-sort-numeric-asc mr-1"></i> Lot Range &amp; Code Setup
        </h4>
        <p class="small text-muted mb-3">
          Entering <strong>Lot Start</strong> automatically sets <strong>Lot End</strong> based on the total participant count (<?= $participant_count ?>).
        </p>

        <form id="lotRangeForm" onsubmit="return false;">
          <input type="hidden" name="si_id" value="<?= (int)$item['si_id'] ?>">
          
          <div class="row g-2 mb-2">
            <div class="col-6">
              <label for="lot_start" class="small font-weight-bold text-dark mb-1">Lot Start</label>
              <input type="number" class="form-control text-center font-weight-bold" id="lot_start" name="lot_start" value="<?= $lot_start ?>" min="1" required <?= $lotSetupLocked ? 'disabled' : '' ?>>
            </div>
            <div class="col-6">
              <label for="lot_end" class="small font-weight-bold text-dark mb-1">Lot End (Auto)</label>
              <input type="number" class="form-control text-center font-weight-bold" id="lot_end" name="lot_end" value="<?= $lot_end ?>" min="1" required <?= $lotSetupLocked ? 'disabled' : '' ?>>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="small text-muted">
              <i class="fa fa-users text-primary mr-1"></i> Total: <strong id="totalPartsLabel"><?= $participant_count ?></strong>
            </span>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0" id="btnAutoCalcLot" style="font-size:0.75rem; <?= $lotSetupLocked ? 'display:none !important;' : '' ?>" <?= $lotSetupLocked ? 'disabled' : '' ?>>
              <i class="fa fa-magic mr-1"></i> Auto-Match End
            </button>
          </div>

          <!-- Dynamic Digit Calculator Display -->
          <div class="bg-light p-3 rounded-14 border mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small text-muted">Result Code Digits:</span>
              <span class="badge bg-primary text-white font-weight-bold" id="codeDigitsBadge">
                <?= $max_code_digits ?> Digits Max
              </span>
            </div>
            <div class="small text-muted" id="codeDigitsHelp">
              Mark entry inputs will only accept <?= $max_code_digits ?> digits within range <?= $lot_start ?> to <?= $lot_end ?>.
            </div>
          </div>

          <button type="button" class="btn btn-primary w-100 rounded-12 font-weight-bold py-2" id="btnSaveLotRange" style="<?= $lotSetupLocked ? 'display:none !important;' : '' ?>" <?= $lotSetupLocked ? 'disabled' : '' ?>>
            <i class="fa fa-save mr-1"></i> Update Lot Range
          </button>
          <?php if ($lotSetupLocked): ?>
            <div class="alert alert-secondary small mt-2 mb-0 text-muted" style="border-left: 4px solid #6c757d;">
              <i class="fa fa-lock mr-1"></i> Lot range is locked after the program starts or finishes.
            </div>
          <?php endif; ?>
        </form>
      </div>

      <!-- Mark Entry Gating Information Card -->
      <div class="prog-card p-4 mb-4" id="markPolicyCard">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 class="h6 font-weight-bold text-uppercase tracking-wider text-muted mb-0">
            <i class="fa fa-lock mr-1"></i> Mark Entry Policy
          </h4>
          <span id="markPolicyBadge" class="badge <?= ($isMarksActive) ? 'bg-success' : 'bg-secondary' ?> text-white px-2 py-1 rounded-pill">
            <?= ($isMarksActive) ? 'Active' : 'Locked' ?>
          </span>
        </div>
        
        <div id="markPolicyContent">
          <?php if ($isMarksActive): ?>
            <div class="alert alert-success d-flex align-items-start gap-2 mb-3">
              <i class="fa fa-check-circle fs-5 mt-1"></i>
              <div>
                <strong>Entry Open</strong>
                <div class="small">The Sub-District Administrator has authorized you to record judges' scores directly from this console.</div>
              </div>
            </div>
            <a href="<?= base_url('result/resultentry/index/' . $item['item_code']) ?>"
               class="btn btn-success w-100 rounded-12 font-weight-bold py-2"
               id="launchMarksBtn"
               target="_blank">
              <i class="fa fa-pencil mr-1"></i> Launch Mark Entry
            </a>
          <?php else: ?>
            <div class="alert alert-warning d-flex align-items-start gap-2 mb-3">
              <i class="fa fa-lock fs-5 mt-1"></i>
              <div>
                <strong><?= $item_confirmed ? 'Confirmed - Locked' : 'Waiting for Program Finish' ?></strong>
                <div class="small"><?= $item_confirmed ? 'This result has been confirmed by the Sub-District Administrator. Stage Manager editing is no longer allowed.' : 'Marks open only after the program is finished and the Sub-District Administrator toggle is enabled.' ?></div>
              </div>
            </div>
            <button type="button" class="btn btn-outline-secondary w-100 rounded-12 font-weight-bold py-2" id="launchMarksBtnLocked" disabled>
              <i class="fa fa-ban mr-1"></i> Mark Entry Disabled
            </button>
          <?php endif; ?>
        </div>

        <?php if (in_array($user_type, [0, 1, 2, 3])): ?>
          <div class="mt-3 pt-3 border-top">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <strong class="small text-dark d-block">Admin Delegation: Mark Entry</strong>
                <span class="text-muted" style="font-size: 0.78rem;">Allow Stage Manager to enter marks</span>
              </div>
              <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" role="switch" id="toggleAdminMarksSwitch" <?= ($allow_stage_manager_marks === 'Y') ? 'checked' : '' ?> style="width: 2.8em; height: 1.4em; cursor: pointer;">
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <div>
                <strong class="small text-dark d-block">Admin Delegation: View Participants</strong>
                <span class="text-muted" style="font-size: 0.78rem;">Allow Stage Manager to view participants</span>
              </div>
              <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" role="switch" id="toggleAdminParticipantsSwitch" <?= (!empty($allow_stage_manager_participants) && $allow_stage_manager_participants === 'Y') ? 'checked' : '' ?> style="width: 2.8em; height: 1.4em; cursor: pointer;">
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- Quick Summary Card -->
      <div class="prog-card p-4">
        <h4 class="h6 font-weight-bold text-uppercase tracking-wider text-muted mb-3">
          <i class="fa fa-info-circle mr-1"></i> Competition Summary
        </h4>
        <dl class="row small mb-0">
          <dt class="col-6 text-muted">Item Type:</dt>
          <dd class="col-6 font-weight-bold text-dark"><?= ($item['item_type'] === 'G') ? 'Group' : 'Single' ?></dd>

          <dt class="col-6 text-muted">Max Duration:</dt>
          <dd class="col-6 font-weight-bold text-dark"><?= $duration_minutes ?> Minutes</dd>

          <dt class="col-6 text-muted">Stage Venue:</dt>
          <dd class="col-6 font-weight-bold text-dark"><?= htmlspecialchars($item['stage_name']) ?></dd>

          <dt class="col-6 text-muted">Registered Count:</dt>
          <dd class="col-6 font-weight-bold text-dark"><?= $participant_count ?> Participants</dd>
        </dl>
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
              <div class="h4 font-weight-bold text-primary mb-0" id="modalTotalCount"><?= $participant_count ?></div>
            </div>
          </div>
          <div class="col-sm-3 col-6">
            <div class="bg-white p-3 rounded-14 border shadow-sm">
              <div class="small text-muted">Current Performer</div>
              <div class="h4 font-weight-bold text-success mb-0" id="modalCurrentLot">Lot #<?= $current_lot_no ?></div>
            </div>
          </div>
          <div class="col-sm-3 col-6">
            <div class="bg-white p-3 rounded-14 border shadow-sm">
              <div class="small text-muted">Sub-District</div>
              <div class="h6 font-weight-bold text-dark mb-0 text-truncate" id="modalSubDistrict"><?= htmlspecialchars($item['sub_district_name'] ?? 'Chittur') ?></div>
            </div>
          </div>
          <div class="col-sm-3 col-6">
            <div class="bg-white p-3 rounded-14 border shadow-sm">
              <div class="small text-muted">Stage Venue</div>
              <div class="h6 font-weight-bold text-dark mb-0 text-truncate" id="modalStageName"><?= htmlspecialchars($item['stage_name']) ?></div>
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
  const siId = <?= (int)$item['si_id'] ?>;
  const totalParticipants = <?= (int)$participant_count ?>;
  let currentLot = <?= (int)$current_lot_no ?>;
  let lotStart = <?= (int)$lot_start ?>;
  let lotEnd = <?= (int)$lot_end ?>;
  const canOpenMarks = <?= $isMarksActive ? 'true' : 'false' ?>;

  const btnStart = document.getElementById('btnStartProgram');
  const btnPause = document.getElementById('btnPauseProgram');
  const btnResume = document.getElementById('btnResumeProgram');
  const btnComplete = document.getElementById('btnCompleteProgram');
  const btnNext = document.getElementById('btnNextParticipant');
  const btnSaveLot = document.getElementById('btnSaveLotRange');
  const lotEndInput = document.getElementById('lot_end');
  const lotStartInput = document.getElementById('lot_start');
  const btnAutoCalc = document.getElementById('btnAutoCalcLot');
  const toggleAdminSwitch = document.getElementById('toggleAdminMarksSwitch');

  function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  }

  // Helper: SweetAlert wrapper with fallback
  function showAlert(options) {
    if (typeof Swal !== 'undefined' && Swal.fire) {
      return Swal.fire(options);
    } else {
      alert(options.title || options.text || 'Notification');
      return Promise.resolve({ isConfirmed: true });
    }
  }

  // 1. START PROGRAM
  if (btnStart) {
    btnStart.addEventListener('click', function() {
      showAlert({
        title: 'Start Program?',
        text: 'Are you ready to commence Item #<?= $item['item_code'] ?> on stage? The confidential participant roster will be unlocked and timing will begin.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa fa-play mr-1"></i> Yes, Start Program',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (!result.isConfirmed) return;

        btnStart.disabled = true;
        btnStart.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Starting...';

        const formData = new FormData();
        formData.append('si_id', siId);
        const token = getCsrfToken();
        if (token) formData.append('csrf_test_name', token);

        fetch('<?= base_url('stage/manager/start_item') ?>', {
          method: 'POST',
          body: formData
        })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            showAlert({
              icon: 'success',
              title: 'Program Started!',
              text: data.message || 'Competition timing is now active and roster unlocked.',
              timer: 1500,
              showConfirmButton: false
            }).then(() => {
              window.location.reload();
            });
          } else {
            showAlert({
              icon: 'error',
              title: 'Failed to Start',
              text: data.message || 'Could not start the program.'
            });
            btnStart.disabled = false;
            btnStart.innerHTML = '<i class="fa fa-play"></i> Start Program';
          }
        })
        .catch(err => {
          showAlert({
            icon: 'error',
            title: 'Server Error',
            text: 'Could not connect to server.'
          });
          btnStart.disabled = false;
          btnStart.innerHTML = '<i class="fa fa-play"></i> Start Program';
        });
      });
    });
  }

  // 2. PAUSE PROGRAM
  if (btnPause) {
    btnPause.addEventListener('click', function() {
      showAlert({
        title: 'Pause Program?',
        text: 'Temporarily pause performances on stage for Item #<?= $item['item_code'] ?>?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d97706',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa fa-pause mr-1"></i> Yes, Pause',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (!result.isConfirmed) return;

        const formData = new FormData();
        formData.append('si_id', siId);
        const token = getCsrfToken();
        if (token) formData.append('csrf_test_name', token);

        fetch('<?= base_url('stage/manager/pause_item') ?>', {
          method: 'POST',
          body: formData
        })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            showAlert({
              icon: 'info',
              title: 'Program Paused',
              text: data.message,
              timer: 1400,
              showConfirmButton: false
            }).then(() => {
              window.location.reload();
            });
          } else {
            showAlert({ icon: 'error', title: 'Error', text: data.message });
          }
        });
      });
    });
  }

  // 3. RESUME PROGRAM
  if (btnResume) {
    btnResume.addEventListener('click', function() {
      showAlert({
        title: 'Resume Program?',
        text: 'Resume live performance on stage for Item #<?= $item['item_code'] ?>?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa fa-play mr-1"></i> Yes, Resume',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (!result.isConfirmed) return;

        const formData = new FormData();
        formData.append('si_id', siId);
        const token = getCsrfToken();
        if (token) formData.append('csrf_test_name', token);

        fetch('<?= base_url('stage/manager/resume_item') ?>', {
          method: 'POST',
          body: formData
        })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            showAlert({
              icon: 'success',
              title: 'Program Resumed',
              text: data.message,
              timer: 1400,
              showConfirmButton: false
            }).then(() => {
              window.location.reload();
            });
          } else {
            showAlert({ icon: 'error', title: 'Error', text: data.message });
          }
        });
      });
    });
  }

  // 4. COMPLETE / FINISH PROGRAM
  if (btnComplete) {
    btnComplete.addEventListener('click', function() {
      showAlert({
        title: 'Finish Program?',
        text: 'Mark Item #<?= $item['item_code'] ?> as COMPLETED on stage? This will record actual completion timing and close the stage queue.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa fa-check-circle mr-1"></i> Yes, Finish Program',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (!result.isConfirmed) return;

        btnComplete.disabled = true;
        btnComplete.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Finishing...';

        const formData = new FormData();
        formData.append('si_id', siId);
        const token = getCsrfToken();
        if (token) formData.append('csrf_test_name', token);

        fetch('<?= base_url('stage/manager/complete_item') ?>', {
          method: 'POST',
          body: formData
        })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            showAlert({
              icon: 'success',
              title: 'Program Completed!',
              text: data.message || 'Program successfully completed and recorded in database.',
              timer: 1600,
              showConfirmButton: false
            }).then(() => {
              window.location.href = '<?= base_url('stage/manager?stage_id=' . $item['stage_id']) ?>';
            });
          } else {
            showAlert({ icon: 'error', title: 'Error', text: data.message });
            btnComplete.disabled = false;
            btnComplete.innerHTML = '<i class="fa fa-check-circle"></i> Finish Program';
          }
        })
        .catch(err => {
          showAlert({ icon: 'error', title: 'Server Error', text: 'Connection failed.' });
          btnComplete.disabled = false;
          btnComplete.innerHTML = '<i class="fa fa-check-circle"></i> Finish Program';
        });
      });
    });
  }

  // 5. NEXT PARTICIPANT STEPPER
  if (btnNext) {
    btnNext.addEventListener('click', function() {
      showAlert({
        title: 'Complete Participant Lot #' + currentLot + '?',
        html: 'Mark <strong>Lot #' + currentLot + '</strong> as finished and call <strong>Lot #' + (currentLot + 1) + '</strong> on stage?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa fa-check mr-1"></i> Yes, Advance Next Lot',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (!result.isConfirmed) return;

        btnNext.disabled = true;
        const formData = new FormData();
        formData.append('si_id', siId);
        const token = getCsrfToken();
        if (token) formData.append('csrf_test_name', token);

        fetch('<?= base_url('stage/manager/next_participant') ?>', {
          method: 'POST',
          body: formData
        })
        .then(r => r.json())
        .then(data => {
          btnNext.disabled = false;
          if (data.success) {
            const prevLot = data.previous_lot_no || currentLot;

            // Mark previous row as Done
            const prevRow = document.getElementById('pRow_' + prevLot);
            if (prevRow) {
              prevRow.classList.remove('table-success', 'fw-bold');
              const cell = prevRow.querySelector('.lot-status-cell');
              if (cell) cell.innerHTML = '<span class="badge bg-secondary text-white"><i class="fa fa-check"></i> Done</span>';
            }

            if (data.is_all_finished) {
              btnNext.classList.remove('stepper-btn');
              btnNext.classList.add('btn', 'btn-danger', 'py-3', 'px-4', 'font-weight-bold');
              btnNext.innerHTML = '<i class="fa fa-check-circle"></i> All Finished - Click Finish Program';
              btnNext.onclick = function() {
                if (btnComplete) btnComplete.click();
              };

              showAlert({
                icon: 'success',
                title: 'All Participants Finished!',
                text: data.message || 'All participants have completed their performance. You can now finish the program.',
                confirmButtonColor: '#dc2626',
                confirmButtonText: '<i class="fa fa-check-circle mr-1"></i> Finish Program Now',
                showCancelButton: true,
                cancelButtonText: 'Stay on Page'
              }).then((res) => {
                if (res.isConfirmed && btnComplete) {
                  btnComplete.click();
                }
              });
            } else {
              currentLot = data.current_lot_no;
              document.getElementById('displayCurrentLot').textContent = 'Lot #' + currentLot;
              document.getElementById('stepperBigLot').textContent = 'Lot #' + currentLot;
              document.getElementById('stepperProgressBadge').textContent = 'Lot ' + currentLot + ' of ' + lotEnd;
              document.getElementById('btnNextText').textContent = 'Mark Lot #' + currentLot + ' Finished & Next Lot';

              // Highlight active row in table
              const activeRow = document.getElementById('pRow_' + currentLot);
              if (activeRow) {
                activeRow.classList.add('table-success', 'fw-bold');
                const cell = activeRow.querySelector('.lot-status-cell');
                if (cell) cell.innerHTML = '<span class="badge bg-success text-white"><span class="radar-dot" style="width:6px;height:6px;"></span> On Stage</span>';
                activeRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
              }

              showAlert({
                icon: 'success',
                title: 'Lot #' + prevLot + ' Finished',
                text: 'Now on stage: Lot #' + currentLot,
                timer: 1600,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
              });
            }
          } else {
            showAlert({ icon: 'error', title: 'Error', text: data.message });
          }
        })
        .catch(err => {
          btnNext.disabled = false;
          showAlert({ icon: 'error', title: 'Server Error', text: 'Communication error.' });
        });
      });
    });
  }

  // 6. SAVE LOT RANGE CONFIGURATION
  if (btnSaveLot) {
    btnSaveLot.addEventListener('click', function() {
      const startVal = parseInt(lotStartInput.value, 10);
      const endVal = parseInt(lotEndInput.value, 10);

      if (isNaN(startVal) || isNaN(endVal) || startVal < 1 || endVal < startVal) {
        showAlert({
          icon: 'warning',
          title: 'Invalid Lot Range',
          text: 'End Lot must be greater than or equal to Start Lot.'
        });
        return;
      }

      this.disabled = true;
      this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

      const formData = new FormData();
      formData.append('si_id', siId);
      formData.append('lot_start', startVal);
      formData.append('lot_end', endVal);
      const token = getCsrfToken();
      if (token) formData.append('csrf_test_name', token);

      fetch('<?= base_url('stage/manager/save_lot_range') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        btnSaveLot.disabled = false;
        btnSaveLot.innerHTML = '<i class="fa fa-save mr-1"></i> Update Lot Range';
        if (data.success) {
          lotStart = data.lot_start;
          lotEnd = data.lot_end;
          document.getElementById('codeDigitsBadge').textContent = data.max_digits + ' Digits Max';
          document.getElementById('codeDigitsHelp').textContent = 'Mark entry inputs will only accept ' + data.max_digits + ' digits within range ' + lotStart + ' to ' + lotEnd + '.';
          showAlert({
            icon: 'success',
            title: 'Lot Range Updated!',
            text: data.message,
            timer: 1800,
            showConfirmButton: false
          });
        } else {
          showAlert({ icon: 'error', title: 'Error', text: data.message });
        }
      })
      .catch(err => {
        btnSaveLot.disabled = false;
        btnSaveLot.innerHTML = '<i class="fa fa-save mr-1"></i> Update Lot Range';
        showAlert({ icon: 'error', title: 'Server Error', text: 'Communication error.' });
      });
    });
  }

  // 7. AUTO-CALCULATE LOT END ON LOT START INPUT
  function updateDigitPreview() {
    const endVal = parseInt(lotEndInput?.value, 10) || 0;
    const startVal = parseInt(lotStartInput?.value, 10) || 1;
    if (endVal > 0) {
      const digits = String(endVal).length;
      const badge = document.getElementById('codeDigitsBadge');
      const help = document.getElementById('codeDigitsHelp');
      if (badge) badge.textContent = digits + ' Digits Max';
      if (help) help.textContent = 'Mark entry inputs will only accept ' + digits + ' digits within range ' + startVal + ' to ' + endVal + '.';
    }
  }

  function autoCalcLotEnd() {
    if (!lotStartInput || !lotEndInput) return;
    const startVal = parseInt(lotStartInput.value, 10);
    if (!isNaN(startVal) && startVal >= 1 && totalParticipants > 0) {
      const calculatedEnd = startVal + totalParticipants - 1;
      lotEndInput.value = calculatedEnd;
      updateDigitPreview();
    }
  }

  if (lotStartInput) {
    lotStartInput.addEventListener('input', autoCalcLotEnd);
    lotStartInput.addEventListener('change', autoCalcLotEnd);
  }

  if (lotEndInput) {
    lotEndInput.addEventListener('input', updateDigitPreview);
  }

  if (btnAutoCalc) {
    btnAutoCalc.addEventListener('click', function(e) {
      e.preventDefault();
      autoCalcLotEnd();
      showAlert({
        icon: 'info',
        title: 'Auto-Matched Lot End',
        text: 'Calculated Lot End = ' + lotEndInput.value + ' for ' + totalParticipants + ' participants.',
        timer: 1500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
      });
    });
  }

  // 8. CLICK ON LOCKED MARKS BUTTON
  function bindLockedMarksButton() {
    const btnLocked = document.getElementById('btnEnterMarksLocked');
    if (btnLocked) {
      btnLocked.onclick = function() {
        showAlert({
          icon: 'info',
          title: 'Mark Entry Locked by Admin',
          html: 'Score entry for this item is currently restricted.<br><br>Only when authorized and assigned by the <strong>Sub-District Administrator</strong> will the Stage Manager be able to enter marks.',
          confirmButtonColor: '#4f46e5'
        });
      };
    }
  }
  bindLockedMarksButton();

  // 9. ADMIN DELEGATION CONTROL: AJAX TOGGLE SWITCH
  if (toggleAdminSwitch) {
    toggleAdminSwitch.addEventListener('change', function() {
      const isAllowed = this.checked ? 'Y' : 'N';
      const formData = new FormData();
      formData.append('si_id', siId);
      formData.append('allow', isAllowed);
      const token = getCsrfToken();
      if (token) formData.append('csrf_test_name', token);

      fetch('<?= base_url('stage/manager/toggle_manager_marks') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          const badge = document.getElementById('markPolicyBadge');
          const content = document.getElementById('markPolicyContent');
          const toolbar = document.getElementById('marksToolbarContainer');
          const marksUrl = '<?= base_url('result/resultentry/index/' . $item['item_code']) ?>';

          if (data.allow === 'Y' && canOpenMarks) {
            if (badge) {
              badge.className = 'badge bg-success text-white px-2 py-1 rounded-pill';
              badge.textContent = 'Active';
            }
            if (content) {
              content.innerHTML = `
                <div class="alert alert-success d-flex align-items-start gap-2 mb-3">
                  <i class="fa fa-check-circle fs-5 mt-1"></i>
                  <div>
                    <strong>Entry Open</strong>
                    <div class="small">The Sub-District Administrator has authorized you to record judges' scores directly from this console.</div>
                  </div>
                </div>
                <a href="${marksUrl}" class="btn btn-success w-100 rounded-12 font-weight-bold py-2" id="launchMarksBtn" target="_blank">
                  <i class="fa fa-pencil mr-1"></i> Launch Mark Entry
                </a>`;
            }
            if (toolbar) {
              toolbar.innerHTML = `
                <a href="${marksUrl}" class="btn-ctrl-marks" id="btnEnterMarks" target="_blank" title="Sub-District Admin has permitted mark entry">
                  <i class="fa fa-pencil-square-o"></i> Enter Marks
                </a>`;
            }
          } else {
            if (badge) {
              badge.className = 'badge bg-secondary text-white px-2 py-1 rounded-pill';
              badge.textContent = 'Locked';
            }
            if (content) {
              content.innerHTML = `
                <div class="alert alert-warning d-flex align-items-start gap-2 mb-3">
                  <i class="fa fa-lock fs-5 mt-1"></i>
                  <div>
                    <strong>Mark Entry Locked by Admin</strong>
                    <div class="small">Score entry is currently restricted to central desk officials. If you need entry permission, request the Sub-District Admin to enable the toggle.</div>
                  </div>
                </div>
                <button type="button" class="btn btn-outline-secondary w-100 rounded-12 font-weight-bold py-2" id="launchMarksBtnLocked" disabled>
                  <i class="fa fa-ban mr-1"></i> Mark Entry Disabled
                </button>`;
            }
            if (toolbar) {
              toolbar.innerHTML = `
                <button type="button" class="btn btn-secondary py-3 px-3 rounded-14 font-weight-bold w-100 justify-content-center d-inline-flex align-items-center" id="btnEnterMarksLocked" title="Click to view permission details">
                  <i class="fa fa-lock mr-1"></i> Marks Locked by Admin
                </button>`;
              bindLockedMarksButton();
            }
          }

          showAlert({
            icon: 'success',
            title: data.allow === 'Y' && canOpenMarks ? 'Mark Entry Assigned!' : 'Mark Entry Permission Saved!',
            text: data.message,
            timer: 1600,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
          });
        } else {
          toggleAdminSwitch.checked = !toggleAdminSwitch.checked;
          showAlert({ icon: 'error', title: 'Permission Error', text: data.message });
        }
      })
      .catch(err => {
        toggleAdminSwitch.checked = !toggleAdminSwitch.checked;
        showAlert({ icon: 'error', title: 'Error', text: 'Server communication failed.' });
      });
    });
  }

  // 9b. ADMIN DELEGATION: PARTICIPANTS VIEW TOGGLE
  const toggleParticipantsSwitch = document.getElementById('toggleAdminParticipantsSwitch');
  if (toggleParticipantsSwitch) {
    toggleParticipantsSwitch.addEventListener('change', function() {
      const isAllowed = this.checked ? 'Y' : 'N';
      const formData = new FormData();
      formData.append('si_id', '<?= (int)$item['si_id'] ?>');
      formData.append('allow', isAllowed);

      fetch('<?= base_url('stage/manager/toggle_manager_participants') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          showAlert({
            icon: 'success',
            title: data.allow === 'Y' ? 'Participant Viewing Enabled!' : 'Participant Viewing Locked!',
            text: data.message,
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
          });
          setTimeout(() => { window.location.reload(); }, 1200);
        } else {
          toggleParticipantsSwitch.checked = !toggleParticipantsSwitch.checked;
          showAlert({ icon: 'error', title: 'Permission Error', text: data.message });
        }
      })
      .catch(err => {
        toggleParticipantsSwitch.checked = !toggleParticipantsSwitch.checked;
        showAlert({ icon: 'error', title: 'Error', text: 'Server communication failed.' });
      });
    });
  }

  // 10. PARTICIPANT MODAL HANDLER
  function openParticipantsModal(pSiId, pItemCode, pDefaultName) {
    const modalEl = document.getElementById('participantDetailsModal');
    if (!modalEl) return;

    let modalInstance = null;
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
      modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    } else if (typeof $ !== 'undefined' && $(modalEl).modal) {
      modalInstance = { show: () => $(modalEl).modal('show'), hide: () => $(modalEl).modal('hide') };
    }

    // Reset modal content to loading state
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
  document.querySelectorAll('.btn-open-part-modal').forEach(btn => {
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
