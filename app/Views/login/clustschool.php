<?php
/**
 * Tamil Kalolsavam - Cluster Schools Directory
 * Modern SaaS Cluster Schools View
 */
$cluster = $cluster ?? [];
$school = $school ?? [];
$part = $part ?? [];
$statistics = $statistics ?? null;
$venue_details = $venue_details ?? [];
$sub_district_name = $sub_district_name ?? 'Sub-District';
$district_code = $district_code ?? 0;
$sub_district_code = $sub_district_code ?? '';
$cluster_id = $cluster_id ?? '';

$cluster_user_name = $cluster[0]['user_name'] ?? 'Cluster';
$cluster_officer_name = $cluster[0]['name'] ?? $cluster_user_name;
$cluster_mobile = $cluster[0]['mobile'] ?? '';
$cluster_email = $cluster[0]['email'] ?? '';

// Build participation lookup map
$part_map = [];
if (!empty($part)) {
    foreach ($part as $p) {
        if (!empty($p['school_code'])) {
            $part_map[$p['school_code']] = true;
        }
    }
}

// Calculate cluster statistics
$total_assigned_schools = count($school);
$total_data_entered = 0;
$total_confirmed = 0;

foreach ($school as $s) {
    $has_entry = isset($part_map[$s['school_code']]) || (!empty($s['is_finalize']));
    if ($has_entry) $total_data_entered++;
    if (($s['is_finalize'] ?? '') === 'Y') $total_confirmed++;
}
$total_unconfirmed = max(0, $total_assigned_schools - $total_confirmed);
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

.cluster-schools-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  margin-bottom: 0;
}
.cluster-schools-table thead th {
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
.cluster-schools-table tbody td {
  padding: 0.95rem 1rem;
  vertical-align: middle;
  border-bottom: 1px solid #f1f5f9;
  font-size: 0.88rem;
  color: #1e293b;
}
.cluster-schools-table tbody tr:hover td {
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

@media print {
  .no-print { display: none !important; }
  .card-table-wrap { box-shadow: none !important; border: 1px solid #000 !important; }
}
</style>

<div class="container-fluid px-3 px-md-4 py-3">

  <!-- Hero Header Card -->
  <div class="hero-header-card mb-4 no-print">
    <div class="row align-items-center g-3">
      <div class="col-lg-7 col-md-12">
        <div class="d-flex align-items-center gap-3">
            <a href="<?= base_url('welcome/cluster_details?sel_district_id=' . urlencode($district_code) . '&sel_sub_district_id=' . urlencode($sub_district_code)); ?>" class="btn btn-outline-secondary btn-sm rounded-3 shadow-none px-3" title="Back to Clusters">
              <i class="bi bi-arrow-left me-1"></i> Back
            </a>
          <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <h4 class="mb-0 fw-bold text-dark">
                <?= esc($cluster_officer_name); ?>
              </h4>
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1">
                <?= esc($cluster_user_name); ?>
              </span>
              <span class="badge bg-light text-secondary border rounded-pill px-2 py-1">
                <?= esc($sub_district_name); ?>
              </span>
            </div>
            <div class="d-flex align-items-center gap-3 text-muted small mt-1 flex-wrap">
              <?php if (!empty($cluster_mobile)) { ?>
                <span><i class="bi bi-telephone me-1 text-primary"></i> <?= esc($cluster_mobile); ?></span>
              <?php } ?>
              <?php if (!empty($cluster_email)) { ?>
                <span><i class="bi bi-envelope me-1 text-primary"></i> <?= esc($cluster_email); ?></span>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5 col-md-12 text-lg-end">
        <button type="button" onclick="window.print()" class="btn btn-light border btn-sm rounded-3 shadow-sm text-secondary">
          <i class="bi bi-printer me-1"></i> Print Roster
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
            <div class="val text-dark"><?= number_format($total_assigned_schools); ?></div>
            <div class="lbl">Assigned Schools</div>
          </div>
          <div class="fs-2 text-primary opacity-50"><i class="bi bi-buildings"></i></div>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="val text-info"><?= number_format($total_data_entered); ?></div>
            <div class="lbl">Data Entered</div>
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
            <div class="lbl">Confirmed Schools</div>
          </div>
          <div class="fs-2 text-success opacity-50"><i class="bi bi-patch-check"></i></div>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="val text-warning"><?= number_format($total_unconfirmed); ?></div>
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
          <i class="bi bi-list-check me-2 text-primary"></i>Cluster School Roster
        </h6>
        <span class="badge bg-secondary-subtle text-secondary"><?= $total_assigned_schools; ?> Schools</span>
      </div>

      <div class="input-group input-group-sm" style="max-width: 280px;">
        <span class="input-group-text bg-white border-secondary-subtle text-muted">
          <i class="bi bi-search"></i>
        </span>
        <input type="text" id="schoolSearchInput" class="form-control border-secondary-subtle" placeholder="Search school name / code...">
      </div>
    </div>

    <div class="table-responsive">
      <table class="table cluster-schools-table" id="clusterSchoolsTable">
        <thead>
          <tr>
            <th style="width: 6%; text-align: center;">Sl. No</th>
            <th style="width: 15%;">School Code</th>
            <th style="width: 45%;">School Name</th>
            <th style="width: 12%; text-align: center;">Data Entry</th>
            <th style="width: 12%; text-align: center;">Confirmation</th>
            <?php if ((int)$this->session->get('USER_TYPE') <= 3) { ?>
            <th style="width: 10%; text-align: center;" class="no-print">Reset Status</th>
            <?php } ?>
          </tr>
        </thead>
        <tbody>
          <?php
          if (empty($school)) {
          ?>
          <tr>
            <td colspan="<?= ((int)$this->session->get('USER_TYPE') <= 3) ? '6' : '5'; ?>" class="text-center py-5 text-muted">
              <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
              இந்தக் கிளஸ்டரில் பள்ளிகள் எதுவும் இன்னும் ஒதுக்கப்படவில்லை (No schools assigned to this cluster yet).
            </td>
          </tr>
          <?php
          } else {
              $idx = 0;
              foreach ($school as $s) {
                  $idx++;
                  $sch_code = $s['school_code'];
                  $sch_name = $s['school_name'];
                  $is_fin = ($s['is_finalize'] ?? '') === 'Y';
                  $has_entry = isset($part_map[$sch_code]) || !empty($s['is_finalize']);
          ?>
          <tr class="school-row">
            <td class="text-center text-muted fw-semibold"><?= $idx; ?></td>
            <td>
              <span class="badge bg-light text-dark border font-monospace"><?= esc($sch_code); ?></span>
            </td>
            <td>
              <span class="fw-semibold text-dark"><?= esc($sch_name); ?></span>
            </td>
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
            <?php if ((int)$this->session->get('USER_TYPE') <= 3) { ?>
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

<!-- Hidden Navigation Form -->
<form id="noncluster" method="POST" action="">
  <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>" />
  <input type="hidden" name="hidSchoolId" id="hidSchoolId" value="" />
</form>

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
      var statusEl = document.getElementById(elementId);
      if (statusEl) {
        statusEl.innerHTML = '<span class="badge-saas badge-saas-amber"><i class="bi bi-clock"></i> No</span>';
      }
      var resetBtn = document.getElementById('reset_' + schoolCode);
      if (resetBtn) {
        resetBtn.style.display = 'none';
      }
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

// Instant Table Search
document.addEventListener('DOMContentLoaded', function() {
  var searchInput = document.getElementById('schoolSearchInput');
  if (!searchInput) return;

  searchInput.addEventListener('keyup', function() {
    var query = this.value.toLowerCase().trim();
    var rows = document.querySelectorAll('#clusterSchoolsTable tbody tr.school-row');

    rows.forEach(function(row) {
      var text = row.innerText.toLowerCase();
      row.style.display = (text.indexOf(query) !== -1) ? '' : 'none';
    });
  });
});
</script>
