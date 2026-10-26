<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-person-lines-fill text-primary"></i> Participants Reports
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Pre-Fest Reports</li>
      <li class="breadcrumb-item active">Participants Reports Suite</li>
    </ol>
  </nav>
</div>

<div class="row g-4">
  <!-- Card 1: Item Wise Participants Report -->
  <div class="col-md-12 col-xl-4">
    <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column overflow-hidden">
      
      <!-- Card Header -->
      <div class="card-header bg-primary text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-music-note-beamed text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Item Wise Participants</h5>
            <small class="text-white-50">Filter by category & competition item</small>
          </div>
        </div>
        <span class="badge bg-white text-primary fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small">
          <i class="bi bi-printer me-1"></i> PDF
        </span>
      </div>

      <!-- Card Body -->
      <div class="card-body p-4 d-flex flex-column flex-grow-1">
        <?php echo form_open('district/report/prereportpdf/rpt_itemwiseparticipants', array('id' => 'formIWP', 'target' => '_blank', 'class' => 'no-global-loader d-flex flex-column flex-grow-1', 'onsubmit' => 'return validateItemReport();')); ?>
        
        <!-- Festival Category -->
        <div class="mb-3">
          <label for="cmbFestType" class="form-label fw-semibold text-dark small mb-1.5 d-flex align-items-center gap-1.5">
            <i class="bi bi-trophy-fill text-warning"></i>
            <span>Festival Category</span>
            <span class="text-danger">*</span>
          </label>
          <select name="cmbFestType" id="cmbFestType" class="form-select py-2 fs-6" required onchange="fetchFestivalItems(this.value);">
            <option value="" disabled selected>-- Select Festival Category --</option>
            <?php
            if (!empty($fest)) {
                foreach ($fest as $fId => $fName) {
                    if ($fId === 0 || $fId === '0' || empty($fId)) continue;
                    echo '<option value="' . htmlspecialchars((string)$fId) . '">' . htmlspecialchars($fName) . '</option>';
                }
            }
            ?>
          </select>
        </div>

        <!-- Competition Item -->
        <div class="mb-4">
          <div class="d-flex align-items-center justify-content-between mb-1.5">
            <label for="cbo_item" class="form-label fw-semibold text-dark small mb-0 d-flex align-items-center gap-1.5">
              <i class="bi bi-music-note-list text-primary"></i>
              <span>Competition Item</span>
              <span class="text-danger">*</span>
            </label>
            <span id="itemCountBadge" class="badge bg-primary-subtle text-primary small" style="display: none;">0 Items</span>
          </div>

          <div id="cmbitem">
            <select name="cbo_item" id="cbo_item" class="form-select py-2 fs-6 text-muted" required disabled>
              <option value="" selected>First select a festival category above</option>
            </select>
          </div>
          <small class="text-muted mt-1 d-block" style="font-size: 0.78rem;">
            <i class="bi bi-info-circle text-primary me-1"></i> Choose "ALL Item" for full category compendium.
          </small>
        </div>

        <!-- Submit Button -->
        <div class="mt-auto pt-3">
          <button type="submit" id="btnSubmitReport" class="btn btn-primary w-100 rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate Item Report (PDF)</span>
          </button>
        </div>

        <input type="hidden" name="hidUserId" id="hidUserId" value="<?php echo htmlspecialchars((string)$this->session->get('USERID')); ?>" />
        
        <?php echo form_close(); ?>

      </div>

    </div>
  </div>

<script>
/**
 * Dynamic AJAX loader for festival items
 */
function fetchFestivalItems(festId) {
  var itemContainer = document.getElementById('cmbitem');
  var countBadge = document.getElementById('itemCountBadge');
  
  if (!festId || festId === '0') {
    itemContainer.innerHTML = '<select name="cbo_item" id="cbo_item" class="form-select py-2 fs-6 text-muted" required disabled><option value="">First select a festival category above</option></select>';
    if (countBadge) countBadge.style.display = 'none';
    return;
  }

  // Show modern spinner
  itemContainer.innerHTML = '<div class="d-flex align-items-center justify-content-center py-2 px-3 bg-light rounded-3 text-primary small">' +
    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' +
    '<span>Loading items...</span></div>';

  var fd = new FormData();
  fd.append('fest_id', festId);

  fetch('<?php echo base_url(); ?>ajax/loadajax/fetch_item_from_participantt', {
    method: 'POST',
    body: fd
  })
  .then(function(res) {
    if (!res.ok) throw new Error('Network error loading items');
    return res.text();
  })
  .then(function(html) {
    itemContainer.innerHTML = html;
    var sel = itemContainer.querySelector('select');
    if (sel) {
      sel.className = 'form-select py-2 fs-6';
      sel.id = 'cbo_item';
      sel.name = 'cbo_item';
      sel.setAttribute('required', 'required');
      
      // Update item count badge
      var totalOptions = sel.options.length;
      if (countBadge && totalOptions > 0) {
        countBadge.textContent = (totalOptions - 1) + ' Items';
        countBadge.className = 'badge bg-primary-subtle text-primary small';
        countBadge.style.display = 'inline-block';
      }
    }
  })
  .catch(function(err) {
    console.error('Item loading error:', err);
    itemContainer.innerHTML = '<select name="cbo_item" id="cbo_item" class="form-select py-2 fs-6" required>' +
      '<option value="ALL">ALL Item (Fallback)</option></select>';
  });
}

/**
 * Modern Client-side Validation before opening PDF
 */
function validateItemReport() {
  var fest = document.getElementById('cmbFestType');
  var item = document.getElementById('cbo_item');

  if (!fest || !fest.value || fest.value === '0') {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'warning',
        title: 'Festival Required',
        text: 'Please select a Festival Category first.',
        confirmButtonColor: '#4154f1'
      });
    } else {
      alert('Please select a Festival Category first.');
    }
    if (fest) fest.focus();
    return false;
  }

  if (!item || !item.value || item.value === '0' || item.value === '') {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'warning',
        title: 'Item Required',
        text: 'Please select a Competition Item or choose "ALL Item".',
        confirmButtonColor: '#4154f1'
      });
    } else {
      alert('Please select a Competition Item or choose "ALL Item".');
    }
    if (item) item.focus();
    return false;
  }

  return true;
}
</script>
