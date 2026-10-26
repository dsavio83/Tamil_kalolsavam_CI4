<!-- Page Header & Breadcrumb -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
      <span class="rounded-3 bg-danger-subtle text-danger p-1.5 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
        <i class="bi bi-database-fill-dash fs-6"></i>
      </span>
      <span>Clear Database Data</span>
    </h5>
    <p class="text-muted small mb-0" style="font-size: 0.78rem;">
      Authorized scoped clearing of operational competition data.
    </p>
  </div>
  <div>
    <?php
    $clearType = is_array($clear_scope) ? key($clear_scope) : (string)$clear_scope;
    $badgeClass = ($clearType === 'all') ? 'bg-danger-subtle text-danger border-danger-subtle' : 'bg-warning-subtle text-warning-emphasis border-warning-subtle';
    $badgeIcon  = ($clearType === 'all') ? 'bi-globe-americas' : 'bi-filter-square';
    $badgeText  = ($clearType === 'all') ? 'Webmaster — Full Scope' : (($clearType === 'district') ? 'District Admin Scope' : 'Sub-District Admin Scope');
    ?>
    <span class="badge <?php echo $badgeClass; ?> border rounded-pill px-2.5 py-1 font-monospace small">
      <i class="bi <?php echo $badgeIcon; ?> me-1"></i><?php echo $badgeText; ?>
    </span>
  </div>
</div>

<div class="row g-3 mb-3">
  <!-- Scope Summary Card -->
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden border-start border-warning border-4 h-100">
      <div class="card-header bg-white border-bottom border-light-subtle py-2.5 px-3">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
          <span class="rounded-3 bg-warning-subtle text-warning p-1.5 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
            <i class="bi bi-bullseye fs-6"></i>
          </span>
          Clear Scope
        </h6>
      </div>
      <div class="card-body p-3.5">
        <?php if ($clearType === 'all'): ?>
          <h4 class="fw-bold text-danger mb-1">Entire Database</h4>
          <p class="text-muted small mb-2">All sub-districts, districts and shared system data tables will be cleared by the Webmaster.</p>
        <?php elseif ($clearType === 'district'): ?>
          <h4 class="fw-bold text-warning-emphasis mb-1"><?php echo esc(get_dist_name((int)$clear_scope['district'])); ?></h4>
          <p class="text-muted small mb-2">Revenue District <code>#<?php echo (int)$clear_scope['district']; ?></code> — all sub-districts within this district will be cleared.</p>
          <div class="small text-muted"><i class="bi bi-geo-alt-fill me-1"></i>District Code: <span class="font-monospace"><?php echo (int)$clear_scope['district']; ?></span></div>
        <?php else: ?>
          <h4 class="fw-bold text-warning-emphasis mb-1"><?php echo esc(get_sub_dist_name((int)$clear_scope['subdistrict'])); ?></h4>
          <p class="text-muted small mb-2">Sub-District <code>#<?php echo (int)$clear_scope['subdistrict']; ?></code> — only this sub-district's operational data will be cleared.</p>
          <div class="small text-muted"><i class="bi bi-geo-alt-fill me-1"></i>Sub-District Code: <span class="font-monospace"><?php echo (int)$clear_scope['subdistrict']; ?></span></div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Critical Warning Callout -->
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden border-danger border-4" style="background: #fff8f8;">
      <div class="card-body p-3.5">
        <div class="d-flex align-items-start gap-3">
          <div class="rounded-circle bg-danger-subtle text-danger p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
          </div>
          <div class="flex-grow-1">
            <h6 class="fw-bold text-danger mb-1">Irreversible Action</h6>
            <p class="text-dark small mb-2">
              Clearing will <strong>permanently delete</strong> the operational data rows enumerated below within the selected scope.
              This includes schools, participants, results, schedules, certificates and generated user accounts.
              Take a fresh SQL backup before proceeding.
            </p>
            <div>
              <a href="<?php echo base_url(); ?>import/backup_data_sql?do=backup" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-xs d-inline-flex align-items-center gap-1.5 fw-semibold">
                <i class="bi bi-download"></i>
                <span>Take Database Backup Now</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Affected Tables Preview -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
  <div class="card-header bg-white border-bottom border-light-subtle py-2.5 px-3">
    <div class="d-flex align-items-center gap-2">
      <span class="rounded-3 bg-secondary-subtle text-secondary p-1.5 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
        <i class="bi bi-table fs-6"></i>
      </span>
      <h6 class="mb-0 fw-bold text-dark">Rows to be Cleared</h6>
      <span class="badge bg-light text-secondary border border-light-subtle rounded-pill ms-auto small font-monospace"><?php echo count($clear_stats); ?> tables</span>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive mb-0">
      <table class="table table-sm table-hover align-middle mb-0">
        <thead class="table-light small text-secondary-emphasis">
          <tr>
            <th class="px-3 py-2">Table</th>
            <th class="text-end px-3 py-2 w-25">Rows in Scope</th>
          </tr>
        </thead>
        <tbody>
          <?php $grandTotal = 0; ?>
          <?php foreach ($clear_stats as $table => $count): ?>
            <?php if ($count === '*') { $count = 'All'; } else { $grandTotal += (int)$count; } ?>
            <tr>
              <td class="px-3 py-1.5 font-monospace small text-secondary"><?php echo esc($table); ?></td>
              <td class="text-end px-3 py-1.5 small fw-semibold"><?php echo $count; ?></td>
            </tr>
          <?php endforeach; ?>
          <tr class="table-light">
            <td class="px-3 py-2 small fw-bold text-dark">Data rows (estimated)</td>
            <td class="text-end px-3 py-2 small fw-bold text-danger"><?php echo $grandTotal; ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Confirmation Form -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
  <div class="card-header bg-white border-bottom border-light-subtle py-2.5 px-3 border-top border-danger border-3">
    <div class="d-flex align-items-center gap-2">
      <span class="rounded-3 bg-danger-subtle text-danger p-1.5 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
        <i class="bi bi-eraser-fill fs-6"></i>
      </span>
      <h6 class="mb-0 fw-bold text-danger">Confirm Clear Operation</h6>
    </div>
  </div>

  <div class="card-body p-3.5">
    <?php echo form_open('import/clear_database', array('id' => 'formClearDatabase', 'class' => 'm-0')); ?>

    <!-- Confirmation Checkbox Safeguard -->
    <div class="p-3 bg-danger-subtle border border-danger-subtle rounded-3 mb-3">
      <div class="form-check d-flex align-items-start gap-2 m-0">
        <?php echo form_checkbox('i_confirm_clear', '1', FALSE, 'id="i_confirm_clear" class="form-check-input mt-0.5 border-danger flex-shrink-0"'); ?>
        <label for="i_confirm_clear" class="form-check-label small text-danger-emphasis">
          <span class="fw-bold">I confirm that I have taken a backup and understand that all in-scope operational data below will be permanently deleted.</span>
        </label>
      </div>
    </div>

    <!-- Hidden Token -->
    <?php echo form_hidden('confirm_token', isset($confirm_token) ? $confirm_token : ''); ?>

    <!-- Action Buttons -->
    <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top border-light-subtle">
      <a href="<?php echo base_url(); ?>welcome" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-x-circle me-1"></i>Cancel
      </a>
      <a href="<?php echo base_url(); ?>import/restore_database" class="btn btn-sm btn-outline-dark rounded-pill px-3">
        <i class="bi bi-arrow-counterclockwise me-1"></i>Restore Page
      </a>
      <button type="submit" id="btnClearDatabase" class="btn btn-sm btn-danger rounded-pill px-4 shadow-sm fw-semibold d-inline-flex align-items-center gap-1.5">
        <i class="bi bi-eraser"></i>
        <span>Clear Database Data</span>
      </button>
    </div>

    <?php echo form_close(); ?>
  </div>
</div>

<script>
(function () {
  const form = document.getElementById('formClearDatabase');
  if (!form) return;
  const chk = document.getElementById('i_confirm_clear');
  const btn = document.getElementById('btnClearDatabase');

  function updateState() {
    const checked = chk && chk.checked;
    if (btn) {
      btn.disabled = !checked;
      btn.classList.toggle('opacity-50', !checked);
    }
  }
  if (chk) chk.addEventListener('change', updateState);
  updateState();

  form.addEventListener('submit', function (ev) {
    ev.preventDefault();
    if (!(chk && chk.checked)) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Confirmation Required',
          text: 'Please tick the confirmation checkbox to proceed.',
          confirmButtonColor: '#ef4444',
          confirmButtonText: 'OK'
        });
      }
      return false;
    }

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'warning',
        title: 'Are you absolutely sure?',
        html: 'This will <strong>permanently delete</strong> all in-scope operational data.<br><br>This action <u>cannot be undone</u>.',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-eraser me-1"></i> Yes, Clear Database Data',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          btn.disabled = true;
          btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Clearing...';
          form.submit();
        }
      });
      return false;
    }

    return true;
  });
})();
</script>

<?php if (isset($clear_result) && is_array($clear_result) && !empty($clear_result)) { ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (typeof Swal === 'undefined') return;
  const isSuccess = <?php echo ($clear_result['type'] === 'success') ? 'true' : 'false'; ?>;
  Swal.fire({
    icon: isSuccess ? 'success' : 'error',
    title: "<?php echo addslashes($clear_result['title']); ?>",
    text: "<?php echo addslashes($clear_result['text']); ?>",
    confirmButtonColor: isSuccess ? '#16a34a' : '#ef4444',
    confirmButtonText: '<i class="bi bi-check-lg me-1"></i> OK',
    timer: isSuccess ? 4000 : 0,
    timerProgressBar: true,
    allowOutsideClick: false
  });
});
</script>
<?php } ?>