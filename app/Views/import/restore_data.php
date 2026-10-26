<!-- Page Header & Breadcrumb -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
      <span class="rounded-3 bg-danger-subtle text-danger p-1.5 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
        <i class="bi bi-database-fill-exclamation fs-6"></i>
      </span>
      <span>Restore Kalolsavam Database</span>
    </h5>
    <p class="text-muted small mb-0" style="font-size: 0.78rem;">
      Restore system data from an official SQL backup archive (.sql).
    </p>
  </div>
  <div>
    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 font-monospace small">
      <i class="bi bi-shield-lock-fill me-1"></i>Administrator / Webmaster
    </span>
  </div>
</div>

<!-- Critical Warning & Backup Prompt Callout -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden border-start border-danger border-4" style="background: #fff8f8;">
  <div class="card-body p-3.5">
    <div class="d-flex align-items-start gap-3">
      <div class="rounded-circle bg-danger-subtle text-danger p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
      </div>
      <div class="flex-grow-1">
        <h6 class="fw-bold text-danger mb-1">
          Critical Safety Warning
        </h6>
        <p class="text-dark small mb-2">
          Restoring the database will <strong>permanently overwrite and delete all existing operational data</strong>, including schools, participant entries, competition schedules, and published scores. Ensure you take a fresh backup before proceeding.
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

<!-- Restore Form Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
  <div class="card-header bg-white border-bottom border-light-subtle py-2.5 px-3">
    <div class="d-flex align-items-center gap-2">
      <span class="rounded-3 bg-secondary-subtle text-secondary p-1.5 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
        <i class="bi bi-file-earmark-arrow-up fs-6"></i>
      </span>
      <h6 class="mb-0 fw-bold text-dark">
        Upload Database Backup (.sql)
      </h6>
    </div>
  </div>

  <div class="card-body p-3.5">
    <?php echo form_open_multipart('import/restore_database', array('id' => 'formKalolsavam', 'class' => 'm-0')); ?>

    <!-- File Picker Row -->
    <div class="mb-3">
      <label for="kalolsavamdatabase" class="form-label small fw-semibold text-secondary mb-1">
        <i class="bi bi-filetype-sql me-1 text-primary"></i>Select Kalolsavam Database Backup File
        <span class="text-danger">*</span>
      </label>
      <?php echo form_upload("kalolsavamdatabase", 'class="form-control form-control-sm rounded-3 font-monospace" id="kalolsavamdatabase" accept=".sql"'); ?>
      <div class="form-text text-muted small mt-1 d-flex align-items-center gap-1.5">
        <i class="bi bi-info-circle text-primary"></i>
        <span>Only uncompressed SQL backup dump files (<code>.sql</code>) up to 16 MB are accepted.</span>
      </div>
    </div>

    <!-- Confirmation Checkbox Safeguard -->
    <div class="p-3 bg-danger-subtle border border-danger-subtle rounded-3 mb-3">
      <div class="form-check d-flex align-items-start gap-2 m-0">
        <?php echo form_checkbox('i_confirm', '1', FALSE, 'id="i_confirm" class="form-check-input mt-0.5 border-danger flex-shrink-0"'); ?>
        <label for="i_confirm" class="form-check-label small text-danger-emphasis">
          <span class="fw-bold">I confirm that I have taken a backup and understand that all existing operational data will be permanently overwritten.</span>
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
      <button type="button" name="save_kalolsavamdatabase" id="save_kalolsavamdatabase" onClick="javascript:fncrestoreKalolsavamdatabase();" class="btn btn-sm btn-danger rounded-pill px-4 shadow-sm fw-semibold d-inline-flex align-items-center gap-1.5">
        <i class="bi bi-arrow-counterclockwise"></i>
        <span>Proceed with Database Restore</span>
      </button>
    </div>

    <?php echo form_close(); ?>
  </div>
</div>

<!-- Clear Database Data (scoped) -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 border-start border-danger border-4">
  <div class="card-header bg-white border-bottom border-light-subtle py-2.5 px-3">
    <div class="d-flex align-items-center gap-2">
      <span class="rounded-3 bg-danger-subtle text-danger p-1.5 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
        <i class="bi bi-eraser-fill fs-6"></i>
      </span>
      <h6 class="mb-0 fw-bold text-danger">Clear Database Data</h6>
    </div>
  </div>
  <div class="card-body p-3.5">
    <p class="text-muted small mb-3">
      Permanently delete operational competition data scoped to your access level. The Webmaster may clear the entire database,
      while District and Sub-District admins may only clear data belonging to their own scope.
    </p>
    <div>
      <a href="<?php echo base_url(); ?>import/clear_database" class="btn btn-sm btn-outline-danger rounded-pill px-4 shadow-xs fw-semibold d-inline-flex align-items-center gap-1.5">
        <i class="bi bi-eraser"></i>
        <span>Open Clear Database Page</span>
      </a>
    </div>
  </div>
</div>