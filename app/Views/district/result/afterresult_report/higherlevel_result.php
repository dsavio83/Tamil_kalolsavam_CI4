<div class="pagetitle mb-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
        <span class="rounded-3 bg-warning-subtle text-warning p-2 d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 42px; height: 42px;">
          <i class="bi bi-trophy-fill fs-5"></i>
        </span>
        <span>Higher Level Competition</span>
      </h1>
      <nav>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
          <li class="breadcrumb-item">Export</li>
          <li class="breadcrumb-item active">Higher Level Competition</li>
        </ol>
      </nav>
    </div>
    <!-- <div>
      <button type="button" onclick="window.history.back();" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-sm">
        <i class="bi bi-arrow-left"></i>
        <span>Back</span>
      </button>
    </div> -->
  </div>
</div>

<!-- Overview Criteria Banner -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden bg-primary bg-gradient text-white">
  <div class="card-body p-4">
    <div class="row align-items-center g-3">
      <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 56px; height: 56px; min-width: 56px;">
            <i class="bi bi-award fs-2 text-white"></i>
          </div>
          <div>
            <h5 class="fw-bold mb-1 text-white">District Kalolsavam Qualification</h5>
            <p class="mb-0 text-white-50 small">
              Generate certified participant lists who achieved <span class="badge bg-warning text-dark fw-bold px-2 py-0.5 rounded-pill">Grade A</span> in UP, HS, and HSS competitions eligible for district level festivals.
            </p>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-4 text-md-end">
        <div class="d-inline-flex flex-column align-items-md-end gap-1.5">
          <span class="badge bg-white text-dark fw-semibold shadow-sm px-3 py-1.5 rounded-pill small font-monospace">
            <i class="bi bi-check2-all me-1 text-success"></i> Withheld Excluded
          </span>
          <span class="badge bg-white text-dark fw-semibold shadow-sm px-3 py-1.5 rounded-pill small font-monospace">
            <i class="bi bi-mortarboard-fill me-1 text-primary"></i> Classes 5 – 12
          </span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Card 1: By Festival & Item -->
  <div class="col-12 col-lg-6">
    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
      <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2.5">
          <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-calendar3-event fs-5"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-0 text-dark">Festival & Item Wise Report</h6>
            <small class="text-muted">Filter by competition category and item</small>
          </div>
        </div>
        <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 small font-monospace">PDF</span>
      </div>

      <div class="card-body p-4">
        <?php echo form_open(base_url('district/report/afterresult_reportpdf/higherlevel_result'), array('id' => 'higher_result', 'target' => '_blank', 'onsubmit' => 'return validateFestForm();')); ?>
        
        <!-- Festival Selection -->
        <div class="mb-3.5">
          <label for="cmbFestType" class="form-label fw-semibold text-dark small d-flex align-items-center gap-1.5 mb-1.5">
            <i class="bi bi-flag-fill text-primary"></i>
            <span>Festival Category <span class="text-danger">*</span></span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-stars"></i></span>
            <?php 
              $festAttributes = 'class="form-select border-start-0 py-2 fs-6 shadow-none" id="cmbFestType" onchange="fetchHigherLevelItems(this.value);" required';
              echo form_dropdown("cmbFestType", $fest, '', $festAttributes); 
            ?>
          </div>
          <div class="form-text text-muted small mt-1">
            Shows all categories in order: UP, HS, HSS, or All Festivals.
          </div>
        </div>

        <!-- Item Selection (Dynamic AJAX) -->
        <div class="mb-4">
          <label for="cbo_item" class="form-label fw-semibold text-dark small d-flex align-items-center gap-1.5 mb-1.5">
            <i class="bi bi-music-note-beamed text-primary"></i>
            <span>Competition Item <span class="text-danger">*</span></span>
          </label>
          <div id="cmbitemWrapper" class="position-relative">
            <div id="cmbitem">
              <select name="cbo_item" id="cbo_item" class="form-select py-2 fs-6 shadow-none">
                <option value="ALL">All Items</option>
              </select>
            </div>
            <div id="itemLoadingSpinner" class="position-absolute end-0 top-50 translate-middle-y me-3" style="display: none;">
              <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
          </div>
          <div class="form-text text-muted small mt-1">
            Items dynamically refresh when festival category changes.
          </div>
        </div>

        <input type="hidden" name="hidUserId" id="hidUserId" value="<?php echo htmlspecialchars((string)session()->get('USERID')); ?>" />

        <div class="pt-3 border-top d-flex justify-content-end">
          <button type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary rounded-pill px-4 py-2.5 fw-semibold shadow-sm d-inline-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
            <span>Generate Item Report</span>
          </button>
        </div>

        <?php echo form_close(); ?>
      </div>
    </div>
  </div>

  <!-- Card 2: By School Code -->
  <div class="col-12 col-lg-6">
    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
      <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2.5">
          <div class="rounded-circle bg-success-subtle text-success p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-building fs-5"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-0 text-dark">School Wise Higher Level Report</h6>
            <small class="text-muted">Filter by specific school code or view all</small>
          </div>
        </div>
        <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 small font-monospace">SCHOOL</span>
      </div>

      <div class="card-body p-4">
        <?php echo form_open(base_url('district/report/afterresult_reportpdf/schoolhigher_result'), array('id' => 'school_result', 'target' => '_blank', 'onsubmit' => 'return validateSchoolForm();')); ?>

        <!-- School Code Input -->
        <div class="mb-4">
          <label for="txtSchoolCode" class="form-label fw-semibold text-dark small d-flex align-items-center gap-1.5 mb-1.5">
            <i class="bi bi-hash text-success"></i>
            <span>School Code <span class="text-danger">*</span></span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-bank"></i></span>
            <input type="text" name="txtSchoolCode" id="txtSchoolCode" class="form-control border-start-0 py-2 fs-6 font-monospace shadow-none" placeholder="Enter school code (e.g. 21001)" onkeypress="return numbersonly(this, event, false);" onblur="lookupSchoolDetails(this.value);" required />
            <button type="button" class="btn btn-outline-secondary border-start-0" onclick="document.getElementById('txtSchoolCode').value=''; document.getElementById('school_name_display').innerHTML='';" title="Clear">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
          <div id="school_name_display" class="mt-2.5"></div>
          <div class="form-text text-muted small mt-1">
            Type a school code to verify school name and view qualified students.
          </div>
        </div>

        <input type="hidden" name="hidUserId2" id="hidUserId2" value="<?php echo htmlspecialchars((string)session()->get('USERID')); ?>" />

        <!-- Dual Action Buttons -->
        <div class="pt-4 border-top d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2.5">
          <a href="<?php echo base_url('district/report/afterresult_reportpdf/schoolhigher_resultall'); ?>" target="_blank" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-2 w-100 w-sm-auto justify-content-center shadow-xs">
            <i class="bi bi-collection-fill text-primary"></i>
            <span>School Wise All (Consolidated)</span>
          </a>

          <button type="submit" name="btnSubmit2" id="btnSubmit2" class="btn btn-success rounded-pill px-4 py-2.5 fw-semibold shadow-sm d-inline-flex align-items-center gap-2 w-100 w-sm-auto justify-content-center">
            <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
            <span>Generate School Report</span>
          </button>
        </div>

        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>

<script>
// Fetch items dynamically for selected festival category
function fetchHigherLevelItems(festId) {
  var wrapper = document.getElementById('cmbitem');
  var spinner = document.getElementById('itemLoadingSpinner');
  if (spinner) spinner.style.display = 'block';

  var formData = new FormData();
  formData.append('fest_id', festId);

  var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  fetch('<?php echo base_url("ajax/loadajax/fetch_higherlevel"); ?>', {
    method: 'POST',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': csrfToken
    },
    body: formData
  })
  .then(function(res) { return res.text(); })
  .then(function(html) {
    if (wrapper) {
      wrapper.innerHTML = html;
      var newSelect = wrapper.querySelector('select');
      if (newSelect) {
        newSelect.className = 'form-select py-2 fs-6 shadow-none';
      }
    }
    if (spinner) spinner.style.display = 'none';
  })
  .catch(function(err) {
    console.error(err);
    if (spinner) spinner.style.display = 'none';
  });
}

// Live lookup school details on blur
function lookupSchoolDetails(schoolCode) {
  var display = document.getElementById('school_name_display');
  if (!schoolCode || schoolCode.trim() === '') {
    if (display) display.innerHTML = '';
    return;
  }

  if (display) {
    display.innerHTML = '<span class="text-muted small"><span class="spinner-border spinner-border-sm me-1"></span>Verifying school code...</span>';
  }

  var formData = new FormData();
  formData.append('fest_id', schoolCode.trim());

  var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  fetch('<?php echo base_url("ajax/ajax2/fetch_school_from_festival"); ?>', {
    method: 'POST',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': csrfToken
    },
    body: formData
  })
  .then(function(res) { return res.text(); })
  .then(function(text) {
    if (display) {
      if (text && text.trim() !== '') {
        display.innerHTML = '<div class="alert alert-success py-2 px-3 mb-0 rounded-3 small d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i><span>' + text.trim() + '</span></div>';
      } else {
        display.innerHTML = '<div class="alert alert-warning py-2 px-3 mb-0 rounded-3 small d-flex align-items-center gap-2"><i class="bi bi-exclamation-triangle-fill text-warning"></i><span>No school found with code ' + schoolCode + '</span></div>';
      }
    }
  })
  .catch(function() {
    if (display) {
      display.innerHTML = '';
    }
  });
}

// Validation for festival form
function validateFestForm() {
  var fest = document.getElementById('cmbFestType');
  if (!fest || fest.value === '' || fest.value === '0') {
    if (typeof Toastify !== 'undefined') {
      Toastify({
        text: "Please select a Festival Category first!",
        duration: 3500,
        gravity: "top",
        position: "right",
        style: { background: "linear-gradient(to right, #ef4444, #f87171)" }
      }).showToast();
    } else {
      alert("Please select a Festival Category first!");
    }
    fest.focus();
    return false;
  }
  return true;
}

// Validation for school form
function validateSchoolForm() {
  var sc = document.getElementById('txtSchoolCode');
  if (!sc || sc.value.trim() === '') {
    if (typeof Toastify !== 'undefined') {
      Toastify({
        text: "Please enter a valid School Code!",
        duration: 3500,
        gravity: "top",
        position: "right",
        style: { background: "linear-gradient(to right, #ef4444, #f87171)" }
      }).showToast();
    } else {
      alert("Please enter a valid School Code!");
    }
    sc.focus();
    return false;
  }
  return true;
}

// Prevent non-numeric characters
function numbersonly(myfield, e, dec) {
  var key;
  var keychar;
  if (window.event) key = window.event.keyCode;
  else if (e) key = e.which;
  else return true;
  keychar = String.fromCharCode(key);
  if ((key == null) || (key == 0) || (key == 8) || (key == 9) || (key == 13) || (key == 27))
    return true;
  else if ((("0123456789").indexOf(keychar) > -1))
    return true;
  else
    return false;
}
</script>
