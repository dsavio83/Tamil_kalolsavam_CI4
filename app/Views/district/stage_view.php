<div class="pagetitle mb-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
        <span class="rounded-3 bg-primary-subtle text-primary p-2 d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 42px; height: 42px;">
          <i class="bi bi-easel2-fill fs-5"></i>
        </span>
        <span>District Stages Management</span>
      </h1>
      <nav>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
          <li class="breadcrumb-item">District</li>
          <li class="breadcrumb-item active">Define Stages</li>
        </ol>
      </nav>
    </div>
    <div class="d-flex gap-2">
      <a href="<?php echo base_url('district/scheduling'); ?>" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-sm">
        <i class="bi bi-calendar-event"></i>
        <span>Event Scheduling</span>
      </a>
      <a href="<?php echo base_url(); ?>welcome" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-sm">
        <i class="bi bi-arrow-left"></i>
        <span>Dashboard</span>
      </a>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Left Column: Add / Edit Stage Card -->
  <div class="col-12 col-lg-5">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
        <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
          <i class="bi bi-plus-circle-fill text-primary"></i>
          <span id="formStageTitle">Add New District Stage</span>
        </h5>
        <small class="text-muted">Stages created here will host District competitions across all sub-districts.</small>
      </div>
      <div class="card-body px-4 pb-4">
        <form id="formDistrictStage">
          <input type="hidden" name="hidStageId" id="hidStageId" value="0">

          <div class="mb-3">
            <label class="form-label fw-semibold small text-dark">Stage Name <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light"><i class="bi bi-display"></i></span>
              <input type="text" name="txtStageName" id="txtStageName" class="form-control" placeholder="e.g. Stage 1 - Main Auditorium" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small text-dark">Venue / Location</label>
            <div class="input-group">
              <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
              <input type="text" name="txtVenue" id="txtVenue" class="form-control" placeholder="e.g. Govt Victoria College Ground">
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold small text-dark">Stage Description / Sound Facilities</label>
            <textarea name="txtStageDescription" id="txtStageDescription" class="form-control" rows="3" placeholder="Stage specifications, lighting, acoustic details..."></textarea>
          </div>

          <div class="d-flex align-items-center gap-2">
            <button type="submit" id="btnSaveStage" class="btn btn-primary rounded-pill px-4 shadow-sm">
              <i class="bi bi-check-lg me-1"></i> Save Stage
            </button>
            <button type="button" id="btnResetStage" class="btn btn-outline-secondary rounded-pill px-3" style="display:none;" onclick="resetStageForm()">
              Cancel Edit
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Right Column: List of Stages -->
  <div class="col-12 col-lg-7">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
      <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between">
        <div>
          <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="bi bi-list-stars text-primary"></i>
            <span>Active District Stages</span>
          </h5>
          <small class="text-muted">Total stages erected for Revenue District <?php echo esc($district_code); ?></small>
        </div>
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fw-bold">
          <?php echo count($stages ?? []); ?> Stages
        </span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-4" style="width: 70px;">#</th>
                <th>Stage Name</th>
                <th>Venue</th>
                <th>Description</th>
                <th class="text-end pe-4" style="width: 120px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($stages)): ?>
                <?php foreach ($stages as $idx => $stg): ?>
                  <tr id="row-stage-<?php echo esc($stg['stage_id']); ?>">
                    <td class="ps-4 fw-bold text-muted"><?php echo $idx + 1; ?></td>
                    <td>
                      <div class="fw-bold text-dark"><?php echo esc($stg['stage_name']); ?></div>
                      <small class="text-muted font-monospace">ID: #<?php echo esc($stg['stage_id']); ?></small>
                    </td>
                    <td><?php echo esc($stg['venue'] ?: '-'); ?></td>
                    <td><small class="text-muted text-truncate d-inline-block" style="max-width: 180px;"><?php echo esc($stg['stage_desc'] ?: '-'); ?></small></td>
                    <td class="text-end pe-4">
                      <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary" title="Edit" onclick="editStage(<?php echo htmlspecialchars(json_encode($stg)); ?>)">
                          <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger" title="Delete" onclick="deleteStage(<?php echo (int)$stg['stage_id']; ?>, '<?php echo esc(addslashes($stg['stage_name'])); ?>')">
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center py-5 text-muted">
                    <i class="bi bi-easel2 fs-1 text-secondary opacity-50 d-block mb-2"></i>
                    <span>No District stages defined yet. Use the form on the left to add your first stage.</span>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('formDistrictStage').addEventListener('submit', function(e) {
  e.preventDefault();
  const id = document.getElementById('hidStageId').value;
  const url = id > 0 ? '<?php echo base_url("district/stage/update_stage"); ?>' : '<?php echo base_url("district/stage/add_stage"); ?>';
  const formData = new FormData(this);

  fetch(url, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        alert(res.message);
        location.reload();
      } else {
        alert(res.message || 'Operation failed');
      }
    })
    .catch(err => alert('Network error: ' + err));
});

function editStage(stg) {
  document.getElementById('formStageTitle').innerText = 'Edit District Stage #' + stg.stage_id;
  document.getElementById('hidStageId').value = stg.stage_id;
  document.getElementById('txtStageName').value = stg.stage_name;
  document.getElementById('txtVenue').value = stg.venue || '';
  document.getElementById('txtStageDescription').value = stg.stage_desc || '';
  document.getElementById('btnResetStage').style.display = 'inline-block';
}

function resetStageForm() {
  document.getElementById('formStageTitle').innerText = 'Add New District Stage';
  document.getElementById('formDistrictStage').reset();
  document.getElementById('hidStageId').value = '0';
  document.getElementById('btnResetStage').style.display = 'none';
}

function deleteStage(id, name) {
  if (!confirm('Are you sure you want to delete stage "' + name + '"?')) return;
  const fd = new FormData();
  fd.append('stage_id', id);

  fetch('<?php echo base_url("district/stage/delete_stage"); ?>', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        alert(res.message);
        location.reload();
      } else {
        alert(res.message);
      }
    });
}
</script>
