<link rel="prefetch" href="<?php echo base_url(); ?>report/prereport/callsheet_first">
<link rel="prefetch" href="<?php echo base_url(); ?>report/prereport/timesheetinterface">
<link rel="prefetch" href="<?php echo base_url(); ?>report/prereport/score_sheet_interfaces">
<link rel="prefetch" href="<?php echo base_url(); ?>report/prereport/tabulation_report_interface">

<style>
.stage-report-tabs-wrapper {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  padding-bottom: 3px;
}
.stage-report-tabs {
  gap: 0.5rem;
  flex-wrap: nowrap;
}
@media (min-width: 992px) {
  .stage-report-tabs {
    flex-wrap: wrap;
  }
}
.stage-report-tabs .nav-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.65rem 1.25rem;
  border-radius: 50rem;
  font-weight: 600;
  white-space: nowrap;
  letter-spacing: 0.2px;
  transition: all 0.2s ease-in-out;
}
.stage-report-tabs .nav-link i {
  margin-right: 0.55rem !important;
  font-size: 1.1rem;
  line-height: 1;
}
.stage-report-tabs .nav-link:not(.active) {
  background-color: #f8f9fa;
  color: #495057;
  border: 1px solid #e9ecef;
}
.stage-report-tabs .nav-link:not(.active):hover {
  background-color: #e9ecef;
  color: #0d6efd;
}
</style>

<div class="pagetitle mb-3">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="d-flex align-items-center gap-2 text-dark fw-bold mb-1">
        <i class="bi bi-easel2-fill text-primary"></i>
        <span>Stage Reports</span>
      </h1>
      <p class="text-muted small mb-0">Stage-wise reports — call sheet, time sheet, score sheet and tabulation as PDF.</p>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 bg-light px-3 py-1.5 rounded-pill border">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none"><i class="bi bi-house-door-fill me-1"></i>Home</a></li>
        <li class="breadcrumb-item text-muted">Reports</li>
        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Stage Reports</li>
      </ol>
    </nav>
  </div>
</div>

<!-- Navigation Pill Tabs -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-2">
    <div class="stage-report-tabs-wrapper">
      <div class="nav nav-pills nav-fill stage-report-tabs" id="reportTabs" role="tablist">
        <a class="nav-link rounded-pill text-secondary fw-semibold py-2 px-3" href="<?php echo base_url(); ?>report/prereport/callsheet_first" data-prefetch>
          <i class="bi bi-person-lines-fill me-2"></i>
          <span>Call Sheet</span>
        </a>
        <a class="nav-link rounded-pill text-secondary fw-semibold py-2 px-3" href="<?php echo base_url(); ?>report/prereport/timesheetinterface" data-prefetch>
          <i class="bi bi-clock-history me-2"></i>
          <span>Time Sheet</span>
        </a>
        <a class="nav-link rounded-pill text-secondary fw-semibold py-2 px-3" href="<?php echo base_url(); ?>report/prereport/score_sheet_interfaces" data-prefetch>
          <i class="bi bi-clipboard-check-fill me-2"></i>
          <span>Score Sheet</span>
        </a>
        <a class="nav-link rounded-pill text-secondary fw-semibold py-2 px-3" href="<?php echo base_url(); ?>report/prereport/tabulation_report_interface" data-prefetch>
          <i class="bi bi-table me-2"></i>
          <span>Tabulation</span>
        </a>
        <a class="nav-link active rounded-pill fw-semibold py-2 px-3" href="<?php echo base_url(); ?>report/prereport/four_reports" data-prefetch>
          <i class="bi bi-collection-fill me-2"></i>
          <span>All in One</span>
        </a>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  var urls = [
    '<?php echo base_url(); ?>report/prereport/callsheet_first',
    '<?php echo base_url(); ?>report/prereport/timesheetinterface',
    '<?php echo base_url(); ?>report/prereport/score_sheet_interfaces',
    '<?php echo base_url(); ?>report/prereport/tabulation_report_interface'
  ];
  function pf(u){ try{ var l=document.createElement('link'); l.rel='prefetch'; l.href=u; document.head.appendChild(l);}catch(e){} }
  if('requestIdleCallback' in window) requestIdleCallback(function(){ urls.forEach(pf); }, {timeout:2000});
  else setTimeout(function(){ urls.forEach(pf); }, 800);
  document.querySelectorAll('#reportTabs a[data-prefetch]').forEach(function(a){
    a.addEventListener('mouseenter', function(){ pf(a.href); }, {once:true});
    a.addEventListener('focus', function(){ pf(a.href); }, {once:true});
  });
})();
</script>

<?php echo form_open('report/prereportpdf/four_reports', array('id' => 'formIWP', 'target' => '_blank', 'class' => 'no-global-loader')); ?>
<input type="hidden" name="report_type" id="report_type" value="">
<div class="row justify-content-center">
  <div class="col-md-9 col-xl-7">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-header bg-primary text-white py-3 px-4 rounded-top-4">
        <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
          <i class="bi bi-collection-fill"></i> All Reports by Item Code
        </h5>
        <small class="text-white-50">Generate Call Sheet, Time Sheet, Tabulation, Score Sheet, or Participant List for any item.</small>
      </div>
      <div class="card-body p-4">
        
        <!-- Mode Switcher: Only ONE mode active at a time -->
        <div class="d-flex justify-content-center mb-4">
          <div class="btn-group p-1 bg-light rounded-pill border shadow-xs" role="group" aria-label="Input Mode Switcher">
            <input type="radio" class="btn-check" name="input_mode" id="modeItemCode" value="code" checked autocomplete="off">
            <label class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-1" for="modeItemCode">
              <i class="bi bi-hash"></i> Enter Item Code
            </label>

            <input type="radio" class="btn-check" name="input_mode" id="modeFestival" value="fest" autocomplete="off">
            <label class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-1" for="modeFestival">
              <i class="bi bi-flag-fill"></i> Or Select Festival
            </label>
          </div>
        </div>

        <!-- Section 1: Direct Item Code Input (Active when mode is 'code') -->
        <div id="sectionItemCode">
          <div class="mb-4">
            <label for="item" class="form-label fw-bold text-dark small d-flex align-items-center">
              <i class="bi bi-hash text-primary me-1"></i> Enter Item Code <span class="text-danger ms-1">*</span>
            </label>
            <div class="input-group input-group-lg">
              <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
              <input type="text" name="item" class="form-control border-start-0 ps-1" id="item" autofocus onkeypress="return numbersonly(this, event, false);" placeholder="e.g. 151">
            </div>
            <div class="form-text text-muted small mt-1">
              Enter the numeric item code directly to generate reports.
            </div>
          </div>
        </div>

        <!-- Section 2: Festival & Item Dropdowns (Active when mode is 'fest') -->
        <div id="sectionFestival" style="display: none;">
          <div class="card bg-light border-0 rounded-3 p-3 mb-4">
            <div class="row g-2 align-items-center">
              <div class="col-md-6">
                <label for="cmbFestTypeFour" class="form-label small text-secondary fw-semibold mb-1">
                  <i class="bi bi-flag-fill text-primary me-1"></i> Select Festival: <span class="text-danger">*</span>
                </label>
                <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-select py-2" id="cmbFestTypeFour" onchange="fetch_items_for_four_reports(this.value)"'); ?>
              </div>
              <div class="col-md-6">
                <label for="cbo_item" class="form-label small text-secondary fw-semibold mb-1">
                  <i class="bi bi-list-check text-primary me-1"></i> Select Item: <span class="text-danger">*</span>
                </label>
                <div id="divItemContainer">
                  <select name="cbo_item" id="cbo_item" class="form-select py-2">
                    <option value="">-- Select Festival First --</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="form-text text-muted small mt-2">
              Select festival first, then choose an item from the generated list.
            </div>
          </div>
        </div>

        <!-- Report Generation Action Buttons -->
        <label class="form-label fw-bold text-dark small mb-2 d-flex align-items-center">
          <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i> Select Report to Generate:
        </label>
        <div class="row g-2">
          <div class="col-sm-6 col-md-4">
            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-1.5 report-btn shadow-sm" data-type="call" name="call" value="Call Sheet">
              <i class="bi bi-person-lines-fill"></i>
              <span>Call Sheet</span>
            </button>
          </div>
          <div class="col-sm-6 col-md-4">
            <button type="submit" class="btn btn-info text-white w-100 rounded-pill py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-1.5 report-btn shadow-sm" data-type="time" name="time" value="Time Sheet">
              <i class="bi bi-clock-history"></i>
              <span>Time Sheet</span>
            </button>
          </div>
          <div class="col-sm-6 col-md-4">
            <button type="submit" class="btn btn-secondary w-100 rounded-pill py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-1.5 report-btn shadow-sm" data-type="tabulation" name="tabulation" value="Tabulation Sheet">
              <i class="bi bi-table"></i>
              <span>Tabulation</span>
            </button>
          </div>
          <div class="col-sm-6 col-md-4">
            <button type="submit" class="btn btn-warning text-dark w-100 rounded-pill py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-1.5 report-btn shadow-sm" data-type="score" name="score" value="Score Sheet">
              <i class="bi bi-clipboard-check-fill"></i>
              <span>Score Sheet</span>
            </button>
          </div>
          <div class="col-sm-12 col-md-8">
            <button type="submit" class="btn btn-success w-100 rounded-pill py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-1.5 report-btn shadow-sm" data-type="participants" name="participants" value="Participant List">
              <i class="bi bi-people-fill"></i>
              <span>Participant List</span>
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<?php echo form_close(); ?>

<script>
var currentMode = 'code';

function setInputMode(mode) {
  currentMode = mode;
  var secCode = document.getElementById('sectionItemCode');
  var secFest = document.getElementById('sectionFestival');
  var itemInput = document.getElementById('item');
  var festSelect = document.getElementById('cmbFestTypeFour');
  var itemContainer = document.getElementById('divItemContainer');

  if (mode === 'code') {
    // Activate Item Code mode, deactivate Festival mode
    secCode.style.display = 'block';
    secFest.style.display = 'none';

    // Reset and disable festival/item fields
    if (festSelect) festSelect.value = '';
    if (itemContainer) {
      itemContainer.innerHTML = '<select name="cbo_item" id="cbo_item" class="form-select py-2"><option value="">-- Select Festival First --</option></select>';
    }
    if (itemInput) {
      itemInput.disabled = false;
      itemInput.focus();
    }
  } else {
    // Activate Festival mode, deactivate Item Code mode
    secCode.style.display = 'none';
    secFest.style.display = 'block';

    // Clear direct item input
    if (itemInput) {
      itemInput.value = '';
    }
    if (festSelect) {
      festSelect.focus();
    }
  }
}

document.querySelectorAll('input[name="input_mode"]').forEach(function(radio) {
  radio.addEventListener('change', function() {
    setInputMode(this.value);
  });
});

function fetch_items_for_four_reports(festid) {
  var container = document.getElementById('divItemContainer');
  if (!container) return;
  if (!festid || festid === '' || festid === '0') {
    container.innerHTML = '<select name="cbo_item" id="cbo_item" class="form-select py-2"><option value="">-- Select Festival First --</option></select>';
    return;
  }
  container.innerHTML = '<div class="py-2 text-center text-muted small"><span class="spinner-border spinner-border-sm me-1 text-primary"></span> Loading items...</div>';
  
  var formData = new FormData();
  formData.append('fest_id', festid);

  fetch('<?php echo base_url(); ?>ajax/loadajax/fetch_item_from_festival', {
    method: 'POST',
    body: formData
  })
  .then(function(res) { return res.text(); })
  .then(function(html) {
    container.innerHTML = html;
    var sel = container.querySelector('select');
    if (sel) {
      sel.id = 'cbo_item';
      sel.name = 'cbo_item';
      sel.className = 'form-select py-2';
      sel.addEventListener('change', function() {
        if (this.value && this.value !== '0' && this.value !== 'ALL') {
          var itemInput = document.getElementById('item');
          if (itemInput) {
            itemInput.value = this.value;
          }
        }
      });
    }
  })
  .catch(function(err) {
    console.error(err);
    container.innerHTML = '<div class="text-danger small py-2">Failed to load items.</div>';
  });
}

// Global fallback mapping
window.fetch_callsheet_festival = fetch_items_for_four_reports;

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.report-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      var itemInput = document.getElementById('item');
      var itemVal = '';

      if (currentMode === 'code') {
        itemVal = (itemInput ? itemInput.value : '').trim();
        if (!itemVal) {
          e.preventDefault();
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'warning',
              title: 'Oops!',
              text: 'Please enter an Item Code.',
              confirmButtonColor: '#0d6efd'
            });
          } else {
            alert('Please enter an Item Code.');
          }
          if (itemInput) itemInput.focus();
          return false;
        }
      } else {
        // Festival & Item mode
        var festSelect = document.getElementById('cmbFestTypeFour');
        var festVal = festSelect ? festSelect.value : '';
        var cboItem = document.getElementById('cbo_item');
        var selectedItemVal = cboItem ? cboItem.value : '';

        if (!festVal || festVal === '' || festVal === '0') {
          e.preventDefault();
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'warning',
              title: 'Oops!',
              text: 'Please select a Festival first.',
              confirmButtonColor: '#0d6efd'
            });
          } else {
            alert('Please select a Festival first.');
          }
          if (festSelect) festSelect.focus();
          return false;
        }

        if (!selectedItemVal || selectedItemVal === '' || selectedItemVal === '0' || selectedItemVal === 'ALL') {
          e.preventDefault();
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'warning',
              title: 'Oops!',
              text: 'Please select an Item from the list.',
              confirmButtonColor: '#0d6efd'
            });
          } else {
            alert('Please select an Item from the list.');
          }
          if (cboItem) cboItem.focus();
          return false;
        }

        // Set the item value into #item for backend processing
        if (itemInput) {
          itemInput.value = selectedItemVal;
        }
      }

      var type = this.getAttribute('data-type');
      var typeInput = document.getElementById('report_type');
      if (typeInput) {
        typeInput.value = type;
      }
    });
  });
});
</script>
