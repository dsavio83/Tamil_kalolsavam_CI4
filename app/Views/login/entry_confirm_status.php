<?php
/**
 * Tamil Kalolsavam - Entry Confirm Status
 * School data entry / confirmation / reset status directory
 */
$school_list = $school_list ?? [];
$total_schools = $total_schools ?? 0;
$total_data_entry = $total_data_entry ?? 0;
$total_confirmed = $total_confirmed ?? 0;
$total_pending = $total_pending ?? 0;
$district_name = $district_name ?? 'District';
$district_code = $district_code ?? 0;
$sub_district_name = $sub_district_name ?? 'Sub-District';
$sub_district_code = $sub_district_code ?? 0;
$user_type = (int)($user_type ?? (int)$this->session->get('USER_TYPE'));

$is_district_view = ($user_type === 2);
$can_bulk = ((int)$this->session->get('USER_TYPE') <= 3);
?>

<style>
:root {
  --admin-primary: #0284c7;
  --admin-primary-dark: #0369a1;
  --admin-border-color: #e2e8f0;
}

.hero-header-card {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 4px 15px -3px rgba(15, 23, 42, 0.05);
  padding: 1.25rem 1.5rem;
}

.kpi-tile {
  background: #ffffff;
  border: none !important;
  border-radius: 14px;
  padding: 1rem 1.15rem;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.04), 0 2px 6px -1px rgba(15, 23, 42, 0.02);
}
.kpi-tile:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.08);
}
.kpi-tile .val {
  font-size: 1.65rem;
  font-weight: 700;
  line-height: 1.2;
}
.kpi-tile .lbl {
  font-size: 0.76rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: #64748b;
  margin-top: 0.25rem;
}

.card-table-wrap {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.04);
  overflow: hidden;
  border: 1px solid #edf2f7;
}

.entry-confirm-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  margin-bottom: 0;
}
.entry-confirm-table thead th {
  background: #f8fafc;
  color: #475569;
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  padding: 0.9rem 1rem;
  border-bottom: 2px solid #e2e8f0;
  white-space: nowrap;
}
.entry-confirm-table tbody td {
  padding: 0.95rem 1rem;
  vertical-align: middle;
  border-bottom: 1px solid #f1f5f9;
  font-size: 0.88rem;
  color: #1e293b;
}
.entry-confirm-table tbody tr:hover td {
  background-color: #f8fafc;
}

.badge-saas {
  font-size: 0.76rem;
  font-weight: 600;
  padding: 0.35rem 0.65rem;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
}
.badge-saas-green { background: #dcfce7; color: #15803d; }
.badge-saas-amber { background: #fef3c7; color: #b45309; }
.badge-saas-slate { background: #f1f5f9; color: #475569; }

.btn-reset-confirm {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
  padding: 0.35rem 0.75rem;
  font-size: 0.8rem;
  font-weight: 600;
  border-radius: 6px;
  transition: all 0.15s ease;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
}
.btn-reset-confirm:hover {
  background: #dc2626;
  color: #ffffff;
  border-color: #dc2626;
}

.bulk-action-bar {
  background: #f0f9ff;
  border: 1px solid #bae6fd;
  border-radius: 10px;
  padding: 0.75rem 1rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
}
.bulk-action-bar .selection-count {
  font-size: 0.85rem;
  font-weight: 600;
  color: #0369a1;
}
.btn-bulk-confirm {
  background: #059669;
  color: #ffffff;
  border: 1px solid #047857;
  padding: 0.4rem 1rem;
  font-size: 0.82rem;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.15s ease;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
}
.btn-bulk-confirm:hover {
  background: #047857;
  box-shadow: 0 4px 10px rgba(5, 150, 105, 0.25);
}
.btn-bulk-confirm:disabled {
  background: #94a3b8;
  border-color: #94a3b8;
  cursor: not-allowed;
  box-shadow: none;
}
.btn-bulk-reset {
  background: #dc2626;
  color: #ffffff;
  border: 1px solid #b91c1c;
  padding: 0.4rem 1rem;
  font-size: 0.82rem;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.15s ease;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
}
.btn-bulk-reset:hover {
  background: #b91c1c;
  box-shadow: 0 4px 10px rgba(220, 38, 38, 0.25);
}
.btn-bulk-reset:disabled {
  background: #94a3b8;
  border-color: #94a3b8;
  cursor: not-allowed;
  box-shadow: none;
}
.school-checkbox {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #2563eb;
}
.select-all-checkbox {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #2563eb;
}

@media print {
  .no-print { display: none !important; }
  .card-table-wrap { box-shadow: none !important; border: 1px solid #000 !important; }
}
</style>

<div class="container-fluid px-3 px-md-4 py-3">

  <!-- Hero Header Card -->
  <div class="hero-header-card mb-4 no-print">
    <div class="row align-items-center g-3">
      <div class="col-lg-8 col-md-12">
        <div class="d-flex align-items-center gap-3">
          <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <h4 class="mb-0 fw-bold text-dark">
                <i class="bi bi-clipboard2-check me-2 text-primary"></i>Entry Confirm Status
              </h4>
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1">
                <?= esc($district_name); ?>
              </span>
              <?php if (!empty($sub_district_name)) { ?>
              <span class="badge bg-light text-secondary border rounded-pill px-2 py-1">
                <?= esc($sub_district_name); ?>
              </span>
              <?php } ?>
            </div>
            <div class="text-muted small mt-1">
              School data entry, confirmation and reset status overview
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-12 text-lg-end">
        <button type="button" onclick="window.print()" class="btn btn-light border btn-sm rounded-3 shadow-sm text-secondary">
          <i class="bi bi-printer me-1"></i> Print Report
        </button>
      </div>
    </div>
  </div>

  <!-- KPI Tiles -->
  <div class="row g-3 mb-4 no-print">
    <div class="col-md-3 col-sm-6">
      <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="val text-dark"><?= number_format($total_schools); ?></div>
            <div class="lbl">Total Schools</div>
          </div>
          <div class="fs-2 text-primary opacity-50"><i class="bi bi-buildings"></i></div>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="val text-info"><?= number_format($total_data_entry); ?></div>
            <div class="lbl">Data Entry</div>
          </div>
          <div class="fs-2 text-info opacity-50"><i class="bi bi-journal-text"></i></div>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="val text-success"><?= number_format($total_confirmed); ?></div>
            <div class="lbl">Confirmed</div>
          </div>
          <div class="fs-2 text-success opacity-50"><i class="bi bi-patch-check"></i></div>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="val text-warning"><?= number_format($total_pending); ?></div>
            <div class="lbl">Pending Confirmation</div>
          </div>
          <div class="fs-2 text-warning opacity-50"><i class="bi bi-clock-history"></i></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Table Container -->
  <div class="card-table-wrap">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div class="d-flex align-items-center gap-2">
        <h6 class="mb-0 fw-bold text-dark">
          <i class="bi bi-list-check me-2 text-primary"></i>School Status Roster
        </h6>
        <span class="badge bg-secondary-subtle text-secondary"><?= $total_schools; ?> Schools</span>
      </div>

      <div class="input-group input-group-sm" style="max-width: 280px;">
        <span class="input-group-text bg-white border-secondary-subtle text-muted">
          <i class="bi bi-search"></i>
        </span>
        <input type="text" id="schoolSearchInput" class="form-control border-secondary-subtle" placeholder="Search school name / code...">
      </div>
    </div>

    <?php if ($can_bulk) { ?>
    <!-- Bulk Action Toolbar -->
    <div class="px-3 pt-3 no-print">
      <div class="bulk-action-bar" id="bulkActionBar">
        <div class="d-flex align-items-center gap-2">
          <input type="checkbox" id="selectAll" class="select-all-checkbox" onchange="toggleSelectAll(this)">
          <label for="selectAll" class="small fw-semibold text-dark mb-0" style="cursor:pointer;">Select All</label>
        </div>
        <span class="text-muted">|</span>
        <span class="selection-count" id="selectionCount">0 selected</span>
        <div class="ms-auto d-flex align-items-center gap-2">
          <button type="button" id="btnBulkConfirm" class="btn-bulk-confirm" disabled onclick="bulkConfirmSelected()">
            <i class="bi bi-check-circle"></i> Bulk Confirmation
          </button>
          <button type="button" id="btnBulkReset" class="btn-bulk-reset" disabled onclick="bulkResetSelected()">
            <i class="bi bi-arrow-counterclockwise"></i> Bulk Reset
          </button>
        </div>
      </div>
    </div>
    <?php } ?>

    <div class="table-responsive">
      <table class="table entry-confirm-table" id="entryConfirmTable">
        <thead>
          <tr>
            <?php if ($can_bulk) { ?>
            <th style="width: 4%; text-align: center;" class="no-print"><input type="checkbox" id="selectAllHead" class="select-all-checkbox" onchange="toggleSelectAll(this)"></th>
            <?php } ?>
            <th style="width: 6%; text-align: center;">Sl. No</th>
            <th style="width: 13%;">School Code</th>
            <th style="width: <?= $is_district_view ? '30%' : '45%'; ?>;">School Name</th>
            <?php if ($is_district_view) { ?>
            <th style="width: 15%;">Sub District</th>
            <?php } ?>
            <th style="width: 11%; text-align: center;">Data Entry</th>
            <th style="width: 11%; text-align: center;">Confirmation</th>
            <?php if ($can_bulk) { ?>
            <th style="width: 10%; text-align: center;" class="no-print">Reset Status</th>
            <?php } ?>
          </tr>
        </thead>
        <tbody>
          <?php
          if (empty($school_list)) {
          ?>
          <tr>
            <td colspan="<?= 5 + ($can_bulk ? 2 : 0) + ($is_district_view ? 1 : 0); ?>" class="text-center py-5 text-muted">
              <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
              தற்போது எந்தப் பள்ளிகளும் இல்லை (No schools found in the selected scope).
            </td>
          </tr>
          <?php
          } else {
              $idx = 0;
              foreach ($school_list as $s) {
                  $idx++;
                  $sch_code = $s['school_code'];
                  $sch_name = $s['school_name'];
                  $has_entry = ($s['has_data_entry'] ?? 'N') === 'Y';
                  $is_fin = ($s['is_finalize'] ?? 'N') === 'Y';
          ?>
          <tr class="school-row">
            <?php if ($can_bulk) { ?>
            <td class="text-center no-print">
              <input type="checkbox" class="school-checkbox" value="<?= esc($sch_code); ?>" data-school-name="<?= esc($sch_name, 'attr'); ?>" data-finalized="<?= $is_fin ? 'Y' : 'N'; ?>" onchange="updateBulkButtonState()" aria-label="Select <?= esc($sch_code); ?>">
            </td>
            <?php } ?>
            <td class="text-center text-muted fw-semibold"><?= $idx; ?></td>
            <td>
              <span class="badge bg-light text-dark border font-monospace"><?= esc($sch_code); ?></span>
            </td>
            <td>
              <span class="fw-semibold text-dark"><?= esc($sch_name); ?></span>
            </td>
            <?php if ($is_district_view) { ?>
            <td>
              <span class="text-secondary"><?= esc($s['sub_district_name'] ?? ''); ?></span>
            </td>
            <?php } ?>
            <td class="text-center">
              <?php if ($has_entry) { ?>
                <span class="badge-saas badge-saas-green"><i class="bi bi-check-circle"></i> Yes</span>
              <?php } else { ?>
                <span class="badge-saas badge-saas-slate"><i class="bi bi-x-circle"></i> No</span>
              <?php } ?>
            </td>
            <td class="text-center" id="confirm_status_<?= esc($sch_code); ?>">
              <?php if ($is_fin) { ?>
                <span class="badge-saas badge-saas-green"><i class="bi bi-check-circle-fill"></i> Yes</span>
              <?php } else { ?>
                <span class="badge-saas badge-saas-amber"><i class="bi bi-clock"></i> No</span>
              <?php } ?>
            </td>
            <?php if ($can_bulk) { ?>
            <td class="text-center no-print">
              <?php if ($is_fin) { ?>
                <button type="button" id="reset_<?= esc($sch_code); ?>"
                        onclick="fnc_change_confirmation_status('<?= addslashes($sch_name); ?>', '<?= esc($sch_code); ?>', 'confirm_status_<?= esc($sch_code); ?>')"
                        class="btn-reset-confirm" title="Reset confirmation status">
                  <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
              <?php } else { ?>
                <span class="text-muted small">-</span>
              <?php } ?>
            </td>
            <?php } ?>
          </tr>
          <?php
              }
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<script>
// Confirmation Reset Function
function fnc_change_confirmation_status(schoolName, schoolCode, elementId) {
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: 'Reset Confirmation Status?',
      text: 'Do you really want to reset the confirmation status of ' + schoolName + ' (' + schoolCode + ')?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc2626',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Yes, Reset',
      cancelButtonText: 'Cancel'
    }).then(function(result) {
      if (result.isConfirmed) {
        doResetConfirmation(schoolCode, elementId);
      }
    });
  } else {
    if (confirm("Reset confirmation status of " + schoolName + " (" + schoolCode + ")?")) {
      doResetConfirmation(schoolCode, elementId);
    }
  }
}

function doResetConfirmation(schoolCode, elementId) {
  var formData = new URLSearchParams();
  formData.append('school_code', schoolCode);
  formData.append('<?= csrf_token(); ?>', '<?= csrf_hash(); ?>');

  fetch('<?= base_url("ajax/loadajax/change_confirmation_status"); ?>', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: formData
  })
  .then(function(response) { return response.text(); })
  .then(function(responseText) {
    var resp = responseText.trim();
    if (resp === 'No') {
      setRowConfirmed(schoolCode, false);
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: 'Success!',
          text: 'School confirmation status has been reset successfully.',
          icon: 'success',
          confirmButtonColor: '#16a34a'
        });
      } else {
        alert('Confirmation status reset successfully.');
      }
      updateBulkButtonState();
    } else {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: 'Failed!',
          text: 'Unable to reset: ' + resp,
          icon: 'error',
          confirmButtonColor: '#dc2626'
        });
      } else {
        alert('Error: ' + resp);
      }
    }
  })
  .catch(function(err) {
    console.error('Reset error:', err);
    alert('Network error while resetting confirmation.');
  });
}

// Bulk Confirm / Reset Functions
function toggleSelectAll(source) {
  var checkboxes = document.querySelectorAll('.school-checkbox');
  checkboxes.forEach(function(cb) {
    var row = cb.closest('tr.school-row');
    if (!row || row.style.display !== 'none') {
      cb.checked = source.checked;
    }
  });
  updateBulkButtonState();
}

function getSelectedSchoolCodes(finalizedFilter) {
  var codes = [];
  var checkboxes = document.querySelectorAll('.school-checkbox:checked');
  checkboxes.forEach(function(cb) {
    var row = cb.closest('tr.school-row');
    if (row && row.style.display === 'none') return;
    var fin = cb.getAttribute('data-finalized');
    if (finalizedFilter === 'confirm' && fin === 'Y') return;
    if ((finalizedFilter === 'reset' || finalizedFilter === 'unconfirm') && fin !== 'Y') return;
    codes.push(cb.value);
  });
  return codes;
}

function updateBulkButtonState() {
  var confirmCodes = getSelectedSchoolCodes('confirm');
  var resetCodes = getSelectedSchoolCodes('reset');
  var btnConfirm = document.getElementById('btnBulkConfirm');
  var btnReset = document.getElementById('btnBulkReset');
  var countEl = document.getElementById('selectionCount');

  if (btnConfirm) btnConfirm.disabled = confirmCodes.length === 0;
  if (btnReset) btnReset.disabled = resetCodes.length === 0;
  if (countEl) {
    var total = confirmCodes.length + resetCodes.length;
    countEl.textContent = total + ' selected';
  }
}

function setRowConfirmed(code, confirmed) {
  var statusEl = document.getElementById('confirm_status_' + code);
  if (statusEl) {
    if (confirmed) {
      statusEl.innerHTML = '<span class="badge-saas badge-saas-green"><i class="bi bi-check-circle-fill"></i> Yes</span>';
    } else {
      statusEl.innerHTML = '<span class="badge-saas badge-saas-amber"><i class="bi bi-clock"></i> No</span>';
    }
  }

  var checkbox = document.querySelector('.school-checkbox[value="' + code + '"]');
  if (!checkbox) return;
  checkbox.checked = false;
  checkbox.setAttribute('data-finalized', confirmed ? 'Y' : 'N');

  var row = checkbox.closest('tr.school-row');
  if (!row) return;
  var tds = row.querySelectorAll('td.no-print');
  var resetCell = tds.length > 1 ? tds[tds.length - 1] : null;
  if (!resetCell) return;

  var existingBtn = document.getElementById('reset_' + code);
  var placeholder = resetCell.querySelector('span.text-muted');

  if (confirmed) {
    if (!existingBtn) {
      if (placeholder) placeholder.remove();
      var schoolName = checkbox.getAttribute('data-school-name') || code;
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.id = 'reset_' + code;
      btn.className = 'btn-reset-confirm';
      btn.title = 'Reset confirmation status';
      btn.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i> Reset';
      btn.addEventListener('click', function() {
        fnc_change_confirmation_status(schoolName, code, 'confirm_status_' + code);
      });
      resetCell.appendChild(btn);
    } else {
      existingBtn.style.display = '';
    }
  } else {
    if (existingBtn) existingBtn.remove();
    if (!resetCell.querySelector('span.text-muted')) {
      var sp = document.createElement('span');
      sp.className = 'text-muted small';
      sp.textContent = '-';
      resetCell.appendChild(sp);
    }
  }
}

function runBulkAction(action) {
  action = (action === 'reset') ? 'unconfirm' : action;
  var codes = getSelectedSchoolCodes(action);
  if (codes.length === 0) return;

  var actionLabel = (action === 'confirm') ? 'Confirm' : 'Reset';
  var nextStatus = (action === 'confirm') ? 'Yes' : 'No';

  function doBulk() {
    var formData = new URLSearchParams();
    formData.append('action', action);
    codes.forEach(function(code) { formData.append('school_codes[]', code); });
    formData.append('<?= csrf_token(); ?>', '<?= csrf_hash(); ?>');

    fetch('<?= base_url("ajax/loadajax/bulk_change_confirmation_status"); ?>', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(resp) {
      if (resp && resp.success > 0) {
        var msg = '<b>' + resp.success + '</b> school(s) ' + actionLabel + (actionLabel === 'Reset' ? ' successfully.' : 'ed successfully.');
        if (resp.failed > 0) msg += '<br><span class="text-danger">' + resp.failed + ' failed.</span>';
        if (resp.errors && resp.errors.length > 0) {
          msg += '<br><small class="text-muted">' + resp.errors.slice(0, 5).join('<br>') + '</small>';
        }
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: 'Success!',
            html: msg,
            icon: (resp.failed > 0) ? 'warning' : 'success',
            confirmButtonColor: '#16a34a'
          });
        } else {
          alert(msg);
        }
        var results = (resp && typeof resp.results === 'object' && resp.results !== null) ? resp.results : {};
        codes.forEach(function(code) {
          var confirmed;
          if (Object.prototype.hasOwnProperty.call(results, code)) {
            confirmed = (String(results[code]).toUpperCase() === 'Y');
          } else {
            confirmed = (nextStatus === 'Yes');
          }
          setRowConfirmed(code, confirmed);
        });
        var selectAll = document.getElementById('selectAll');
        var selectAllHead = document.getElementById('selectAllHead');
        if (selectAll) selectAll.checked = false;
        if (selectAllHead) selectAllHead.checked = false;
        updateBulkButtonState();
      } else {
        var errMsg = (resp && resp.errors && resp.errors.length > 0) ? resp.errors.join('<br>') : 'No schools were processed.';
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: 'Failed!',
            html: errMsg,
            icon: 'error',
            confirmButtonColor: '#dc2626'
          });
        } else {
          alert('Error: ' + errMsg);
        }
      }
    })
    .catch(function(err) {
      console.error('Bulk error:', err);
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: 'Error!',
          text: 'Network error while processing bulk ' + actionLabel.toLowerCase() + ' operation.',
          icon: 'error',
          confirmButtonColor: '#dc2626'
        });
      } else {
        alert('Network error while processing bulk ' + actionLabel.toLowerCase() + ' operation.');
      }
    });
  }

  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: 'Bulk ' + actionLabel + ' Schools?',
      html: 'Do you really want to <b>' + actionLabel.toLowerCase() + '</b> the confirmation status of <b>' + codes.length + '</b> selected school(s)?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: (action === 'confirm') ? '#059669' : '#dc2626',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Yes, ' + actionLabel,
      cancelButtonText: 'Cancel'
    }).then(function(result) {
      if (result.isConfirmed) {
        doBulk();
      }
    });
  } else {
    if (confirm('Bulk ' + actionLabel + ' ' + codes.length + ' selected school(s)?')) {
      doBulk();
    }
  }
}

function bulkConfirmSelected() {
  runBulkAction('confirm');
}

function bulkResetSelected() {
  runBulkAction('reset');
}

// Instant Table Search
document.addEventListener('DOMContentLoaded', function() {
  var searchInput = document.getElementById('schoolSearchInput');
  if (!searchInput) return;

  searchInput.addEventListener('keyup', function() {
    var query = this.value.toLowerCase().trim();
    var rows = document.querySelectorAll('#entryConfirmTable tbody tr.school-row');

    rows.forEach(function(row) {
      var text = row.innerText.toLowerCase();
      row.style.display = (text.indexOf(query) !== -1) ? '' : 'none';
    });
    updateBulkButtonState();
  });
});
</script>