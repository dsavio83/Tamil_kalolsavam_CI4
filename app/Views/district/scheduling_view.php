<div class="pagetitle mb-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
        <span class="rounded-3 bg-primary-subtle text-primary p-2 d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 42px; height: 42px;">
          <i class="bi bi-calendar3-event-fill fs-5"></i>
        </span>
        <span>District Festival Event Scheduling</span>
      </h1>
      <nav>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
          <li class="breadcrumb-item">District</li>
          <li class="breadcrumb-item active">Event Scheduling</li>
        </ol>
      </nav>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <button type="button" class="btn btn-sm btn-success rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-sm" onclick="generateAllChestNumbers()">
        <i class="bi bi-hash"></i>
        <span>Auto-Generate Chest Numbers</span>
      </button>
      <a href="<?php echo base_url('district/scheduling/clashes'); ?>" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-sm">
        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
        <span>Clash Report</span>
      </a>
      <a href="<?php echo base_url('district/stage'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-sm">
        <i class="bi bi-easel2"></i>
        <span>Stages</span>
      </a>
    </div>
  </div>
</div>

<!-- Filters Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-3">
    <form method="GET" action="<?php echo base_url('district/scheduling'); ?>" class="row g-2 align-items-center">
      <div class="col-12 col-md-3">
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-light fw-semibold">Festival</span>
          <select name="fest_id" class="form-select" onchange="this.form.submit()">
            <option value="all" <?php echo ($fest_id === 'all') ? 'selected' : ''; ?>>All Festivals</option>
            <option value="2" <?php echo ($fest_id === '2') ? 'selected' : ''; ?>>UP General (Festival 2)</option>
            <option value="3" <?php echo ($fest_id === '3') ? 'selected' : ''; ?>>High School General (Festival 3)</option>
            <option value="4" <?php echo ($fest_id === '4') ? 'selected' : ''; ?>>Higher Secondary / VHSS (Festival 4)</option>
          </select>
        </div>
      </div>
      <div class="col-12 col-md-3">
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-light fw-semibold">Stage Type</span>
          <select name="stage_type" class="form-select" onchange="this.form.submit()">
            <option value="all" <?php echo ($stage_type === 'all') ? 'selected' : ''; ?>>All Event Types</option>
            <option value="on_stage" <?php echo ($stage_type === 'on_stage') ? 'selected' : ''; ?>>On-Stage Events</option>
            <option value="off_stage" <?php echo ($stage_type === 'off_stage') ? 'selected' : ''; ?>>Off-Stage Events</option>
          </select>
        </div>
      </div>
      <div class="col-12 col-md-6 d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
        <div class="d-inline-flex align-items-center gap-1 bg-light border px-2 py-1 rounded-3">
          <span class="small text-muted fw-semibold me-1"><i class="bi bi-lightning-charge-fill text-warning"></i>Quick Fill:</span>
          <input type="datetime-local" step="60" id="districtQuickFillDateTime" style="color-scheme: light !important; width: 195px;" class="form-control form-control-sm font-monospace border bg-white" title="Quick fill Date & Time for all events">
          <button type="button" class="btn btn-sm btn-primary py-1 px-2.5 rounded-2 fw-semibold" onclick="applyDistrictQuickFillDateTime()" title="Apply date and time to all visible rows">
            Apply
          </button>
        </div>
        <button type="button" class="btn btn-sm btn-success rounded-3 px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm" onclick="saveAllVisibleSchedules()" id="btnSaveAll" title="Save schedule for all visible items">
          <i class="bi bi-cloud-arrow-up-fill"></i>
          <span>Save All</span>
        </button>
        <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill font-monospace small">
          Events: <strong><?php echo count($items ?? []); ?></strong>
        </span>
      </div>
    </form>
  </div>
</div>

<!-- Scheduling Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th class="ps-4">Code</th>
            <th>Item Name &amp; Category</th>
            <th class="text-center">Participants</th>
            <th>Stage Venue</th>
            <th class="text-center">Judges</th>
            <th>Date &amp; Time (HH:MM) &amp; Lots</th>
            <th class="text-end pe-4">Save</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($items)): ?>
            <?php foreach ($items as $item): ?>
              <tr id="row-item-<?php echo esc($item['item_code']); ?>">
                <td class="ps-4 font-monospace fw-bold text-primary">
                  #<?php echo esc($item['item_code']); ?>
                </td>
                <td>
                  <div class="fw-bold text-dark"><?php echo esc($item['item_name']); ?></div>
                  <div class="d-flex align-items-center gap-1.5 flex-wrap mt-0.5">
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill small"><?php echo esc($item['fest_name']); ?></span>
                    <?php if ($item['is_off_stage'] === 'Y'): ?>
                      <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill small">Off-Stage</span>
                    <?php else: ?>
                      <span class="badge bg-info-subtle text-info-emphasis rounded-pill small">On-Stage</span>
                    <?php endif; ?>
                    <span class="badge bg-light text-muted border rounded-pill small"><?php echo ($item['item_type'] === 'G') ? 'Group' : 'Single'; ?></span>
                  </div>
                </td>
                <td class="text-center">
                  <span class="badge bg-primary rounded-pill px-2.5 py-1">
                    <?php echo esc($item['total_participants']); ?> Contestants
                  </span>
                </td>
                <td style="min-width: 170px;">
                  <select class="form-select form-select-sm" id="stage_<?php echo esc($item['item_code']); ?>">
                    <option value="">-- Unassigned --</option>
                    <?php foreach ($stages as $stg): ?>
                      <option value="<?php echo esc($stg['stage_id']); ?>" <?php echo ((int)$item['stage_id'] === (int)$stg['stage_id']) ? 'selected' : ''; ?>>
                        <?php echo esc($stg['stage_name']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </td>
                <td style="width: 105px;" class="text-center">
                  <?php $judgesVal = !empty($item['no_of_judges']) ? (int)$item['no_of_judges'] : 3; ?>
                  <select class="form-select form-select-sm text-center font-monospace" id="judges_<?php echo esc($item['item_code']); ?>">
                    <?php for ($j = 1; $j <= 5; $j++): ?>
                      <option value="<?php echo $j; ?>" <?php echo ($judgesVal === $j) ? 'selected' : ''; ?>>
                        <?php echo $j; ?>
                      </option>
                    <?php endfor; ?>
                  </select>
                </td>
                <td style="min-width: 220px;">
                  <div class="d-flex flex-column gap-1">
                    <input type="datetime-local" step="60" style="color-scheme: light !important;" class="form-control form-control-sm font-monospace dt-picker-input" id="start_<?php echo esc($item['item_code']); ?>" value="<?php echo !empty($item['start_time']) ? date('Y-m-d\TH:i', strtotime($item['start_time'])) : ''; ?>">
                    <div class="d-flex gap-1 align-items-center small text-muted">
                      <span>Lot:</span>
                      <?php
                        $partCount = (int)($item['total_participants'] ?? 0);
                        $lotStart = !empty($item['lot_start']) ? (int)$item['lot_start'] : 1;
                        if (!empty($item['lot_end'])) {
                            $lotEnd = (int)$item['lot_end'];
                        } else {
                            $lotEnd = ($partCount > 0) ? ($lotStart + $partCount - 1) : $lotStart;
                        }
                      ?>
                      <input type="number" class="form-control form-control-sm py-0 px-1 text-center lot-start-input" 
                             placeholder="1" 
                             id="lot_start_<?php echo esc($item['item_code']); ?>" 
                             data-item-code="<?php echo esc($item['item_code']); ?>"
                             data-participants="<?php echo $partCount; ?>"
                             value="<?php echo esc($lotStart); ?>" 
                             min="1"
                             oninput="autoCalcLotEnd(this)"
                             onchange="autoCalcLotEnd(this)"
                             title="Lot Starting Number (Auto-calculates End Number from participant count)"
                             style="width: 55px;">
                      <span>to</span>
                      <input type="number" class="form-control form-control-sm py-0 px-1 text-center lot-end-input" 
                             placeholder="End" 
                             id="lot_end_<?php echo esc($item['item_code']); ?>" 
                             value="<?php echo esc($lotEnd); ?>" 
                             min="1"
                             title="Lot Ending Number"
                             style="width: 55px;">
                      <span class="badge bg-light text-secondary border px-1" title="<?php echo $partCount; ?> contestants in this event" style="font-size: 0.7rem;">
                        (<?php echo $partCount; ?>)
                      </span>
                    </div>
                  </div>
                </td>
                <td class="text-end pe-4">
                  <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="saveItemSchedule(<?php echo (int)$item['item_code']; ?>)">
                    <i class="bi bi-check2"></i> Save
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">
                <i class="bi bi-calendar-x fs-1 text-secondary opacity-50 d-block mb-2"></i>
                <span>No eligible District events found matching filters.</span>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
function autoCalcLotEnd(startInput) {
  if (!startInput) return;
  const itemCode = startInput.getAttribute('data-item-code');
  const participants = parseInt(startInput.getAttribute('data-participants'), 10) || 0;
  const endInput = document.getElementById('lot_end_' + itemCode);
  if (!endInput) return;

  const startVal = parseInt(startInput.value, 10);
  if (!isNaN(startVal) && startVal > 0) {
    if (participants > 0) {
      endInput.value = startVal + participants - 1;
    } else {
      endInput.value = startVal;
    }
  } else if (startInput.value === '') {
    endInput.value = '';
  }
}

function saveItemSchedule(itemCode, silent = false) {
  const stageId   = document.getElementById('stage_' + itemCode)?.value || '';
  const judges    = document.getElementById('judges_' + itemCode)?.value || 3;
  const startTime = document.getElementById('start_' + itemCode)?.value || '';
  const lotStartInput = document.getElementById('lot_start_' + itemCode);
  const lotStart  = lotStartInput ? lotStartInput.value : 1;
  const lotEnd    = document.getElementById('lot_end_' + itemCode)?.value || '';
  const participants = lotStartInput ? (lotStartInput.getAttribute('data-participants') || 0) : 0;

  const fd = new FormData();
  fd.append('item_code', itemCode);
  fd.append('stage_id', stageId);
  fd.append('no_of_judges', judges);
  fd.append('day_no', 1);
  fd.append('item_order', 0);
  fd.append('start_time', startTime ? startTime.replace('T', ' ') + ':00' : '');
  fd.append('lot_start', lotStart);
  fd.append('lot_end', lotEnd);
  fd.append('no_of_participant', participants);

  return fetch('<?php echo base_url("district/scheduling/save_allotment"); ?>', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
      if (!silent) {
        if (res.success) {
          alert(res.message);
        } else {
          alert(res.message || 'Save failed');
        }
      }
      return res;
    })
    .catch(err => {
      if (!silent) alert('Network error while saving item #' + itemCode);
      return { success: false };
    });
}

async function saveAllVisibleSchedules() {
  const rows = document.querySelectorAll('tr[id^="row-item-"]');
  if (!rows || rows.length === 0) {
    alert('No events found to save.');
    return;
  }
  const btn = document.getElementById('btnSaveAll');
  const origHtml = btn ? btn.innerHTML : '';
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Saving...';
  }

  let successCount = 0;
  let failCount = 0;

  for (const row of rows) {
    const itemCode = row.id.replace('row-item-', '');
    try {
      const res = await saveItemSchedule(itemCode, true);
      if (res && res.success) {
        successCount++;
      } else {
        failCount++;
      }
    } catch (e) {
      failCount++;
    }
  }

  if (btn) {
    btn.disabled = false;
    btn.innerHTML = origHtml;
  }

  alert(`Save All Completed!\nSuccessfully saved: ${successCount}\nFailed: ${failCount}`);
}

function generateAllChestNumbers() {
  if (!confirm('Auto-generate sequential District chest numbers for all enrolled participants?')) return;
  fetch('<?php echo base_url("district/scheduling/generate_chest_numbers"); ?>', { method: 'POST' })
    .then(r => r.json())
    .then(res => {
      alert(res.message);
      location.reload();
    });
}

function applyDistrictQuickFillDateTime() {
  const qf = document.getElementById('districtQuickFillDateTime');
  if (!qf || !qf.value) {
    alert('Please choose a date and time in the Quick Fill box first.');
    return;
  }
  const val = qf.value;
  let count = 0;
  document.querySelectorAll('input[type="datetime-local"].dt-picker-input').forEach(inp => {
    if (inp.id !== 'districtQuickFillDateTime') {
      inp.value = val;
      count++;
    }
  });
  if (count > 0 && typeof event !== 'undefined' && event.target) {
    const btn = event.target.closest('button');
    if (btn) {
      const origText = btn.innerHTML;
      btn.innerHTML = '<i class="bi bi-check2 me-1"></i>Applied (' + count + ')';
      btn.classList.replace('btn-primary', 'btn-success');
      setTimeout(() => {
        btn.innerHTML = origText;
        btn.classList.replace('btn-success', 'btn-primary');
      }, 1500);
    }
  }
}
</script>

<style>
:root, html, body {
  color-scheme: light !important;
}
input, select, textarea, button,
input[type="date"],
input[type="time"],
input[type="datetime-local"],
.dt-picker-input {
  color-scheme: light !important;
}
</style>
