<style>
@font-face {
  font-family: 'TAU-Paalai';
  src: local('TAU-Paalai'), url('<?php echo base_url("assets/fonts/TAU-Paalai.ttf"); ?>') format('truetype');
  font-weight: normal;
  font-style: normal;
}
@font-face {
  font-family: 'TAU-Paalai';
  src: local('TAU-Paalai Bold'), url('<?php echo base_url("assets/fonts/TAU-Paalai Bold.ttf"); ?>') format('truetype');
  font-weight: bold;
  font-style: normal;
}
@font-face {
  font-family: 'TAU-Pallai';
  src: local('TAU-Paalai'), url('<?php echo base_url("assets/fonts/TAU-Paalai.ttf"); ?>') format('truetype');
}
.summary-card {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-left: 4px solid #0d6efd !important;
}
.item-checkbox {
  width: 1.15rem;
  height: 1.15rem;
  cursor: pointer;
}
</style>

<div class="pagetitle mb-3">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="d-flex align-items-center gap-2 text-dark fw-bold mb-1">
        <i class="bi bi-printer-fill text-primary"></i>
        <span>Generate Covers</span>
      </h1>
      <p class="text-muted small mb-0">Select a template, choose festival category and items, and generate a print-ready PDF (1 Item = 1 Page).</p>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 bg-light px-3 py-1.5 rounded-pill border">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none"><i class="bi bi-house-door-fill me-1"></i>Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>cover/templates" class="text-decoration-none">Cover Print</a></li>
        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Generate</li>
      </ol>
    </nav>
  </div>
</div>

<!-- Cover Print Sub-Navigation -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div class="d-flex align-items-center gap-2">
      <a href="<?php echo base_url('cover/templates'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill fw-semibold px-3">
        <i class="bi bi-grid-fill me-1"></i> Cover Templates
      </a>
      <a href="<?php echo base_url('cover/designer'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill fw-semibold px-3">
        <i class="bi bi-palette-fill me-1"></i> Cover Designer
      </a>
      <a href="<?php echo base_url('cover/generate'); ?>" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold active px-3">
        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Generate Covers
      </a>
    </div>
  </div>
</div>

<form action="<?php echo base_url('cover/generate_pdf'); ?>" method="POST" target="_blank" id="formGenerateCovers">
  <?php echo csrf_field(); ?>

  <!-- Filters Row -->
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 px-4 border-bottom">
      <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
        <i class="bi bi-funnel-fill text-primary me-2"></i> Selection &amp; Filtering Options
      </h6>
    </div>
    <div class="card-body p-4">
      <div class="row g-3">

        <!-- Template Selection -->
        <div class="col-12 col-md-6 col-lg-4">
          <label for="cmbTemplate" class="form-label small fw-bold text-muted">
            <i class="bi bi-layout-text-window-reverse me-1 text-primary"></i> Cover Template <span class="text-danger">*</span>
          </label>
          <select name="template_id" id="cmbTemplate" class="form-select rounded-pill" required onchange="updateSummaryCard()">
            <?php foreach ($templates as $t): ?>
              <?php 
                $isSelected = false;
                if (!empty($selected_template_id)) {
                    $isSelected = ((int)$t['id'] === (int)$selected_template_id);
                } else {
                    $isSelected = ($t['is_default'] === 'Y');
                }
              ?>
              <option value="<?php echo $t['id']; ?>" <?php echo $isSelected ? 'selected' : ''; ?>
                      data-paper="<?php echo esc($t['paper_size']); ?>"
                      data-orientation="<?php echo ucfirst(esc($t['orientation'])); ?>"
                      data-dims="<?php echo round($t['width_mm']) . ' × ' . round($t['height_mm']) . ' mm'; ?>">
                <?php echo esc($t['template_name']); ?> (<?php echo esc($t['paper_size']); ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Category Selection -->
        <div class="col-12 col-md-6 col-lg-4">
          <label for="cmbCategory" class="form-label small fw-bold text-muted">
            <i class="bi bi-flag-fill me-1 text-warning"></i> Category
          </label>
          <select name="category" id="cmbCategory" class="form-select rounded-pill" onchange="filterItems()">
            <option value="ALL">All Categories</option>
            <option value="LP">LP TAMIL KALAIVIZHA</option>
            <option value="UP">UP TAMIL KALAIVIZHA</option>
            <option value="HS">HS TAMIL KALAIVIZHA</option>
            <option value="HSS">HSS TAMIL KALAIVIZHA</option>
          </select>
        </div>

        <!-- Print Rotation / Feed Direction -->
        <div class="col-12 col-md-6 col-lg-4">
          <label for="cmbRotation" class="form-label small fw-bold text-muted">
            <i class="bi bi-arrow-repeat me-1 text-success"></i> Print Rotation (சுழற்சி)
          </label>
          <select name="pdf_rotation" id="cmbRotation" class="form-select rounded-pill" onchange="updateSummaryCard()">
            <option value="0" selected>Normal Landscape (0° - 220×110 mm)</option>
            <option value="90">Rotate 90° Clockwise (வலது சுழற்சி)</option>
            <option value="270">Rotate 270° Counter-Clockwise (இடது சுழற்சி)</option>
            <option value="180">Rotate 180° Inverted (தலைகீழ்)</option>
          </select>
        </div>

        <!-- Date Selection -->
        <div class="col-12 col-md-4 col-lg-4">
          <label for="cmbDate" class="form-label small fw-bold text-muted">
            <i class="bi bi-calendar-event me-1 text-info"></i> Date
          </label>
          <select name="date" id="cmbDate" class="form-select rounded-pill" onchange="filterItems()">
            <option value="All">All Dates</option>
            <?php foreach ($dates as $d): ?>
              <option value="<?php echo esc($d); ?>"><?php echo date('d-m-Y', strtotime($d)); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Stage Selection -->
        <div class="col-12 col-md-4 col-lg-4">
          <label for="cmbStage" class="form-label small fw-bold text-muted">
            <i class="bi bi-easel2-fill me-1 text-secondary"></i> Stage
          </label>
          <select name="stage" id="cmbStage" class="form-select rounded-pill" onchange="filterItems()">
            <option value="All">All Stages</option>
            <?php foreach ($stages as $stg): ?>
              <option value="<?php echo $stg['stage_id']; ?>"><?php echo esc($stg['stage_name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Venue Selection -->
        <div class="col-12 col-md-4 col-lg-4">
          <label for="cmbVenue" class="form-label small fw-bold text-muted">
            <i class="bi bi-geo-alt-fill me-1 text-danger"></i> Venue
          </label>
          <select name="venue" id="cmbVenue" class="form-select rounded-pill" onchange="filterItems()">
            <option value="All">All Venues</option>
            <?php foreach ($venues as $v): ?>
              <option value="<?php echo esc($v); ?>"><?php echo esc($v); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

      </div>
    </div>
  </div>

  <!-- Confirmation Summary Card -->
  <div class="card border-0 shadow-sm rounded-4 summary-card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
          <i class="bi bi-check2-circle text-primary fs-4"></i>
          <span>Generation Summary</span>
        </h5>
        <div class="text-muted small">
          Template: <strong id="sumTemplateName" class="text-dark">-</strong> | 
          Paper: <strong id="sumPaper" class="text-dark">-</strong> | 
          Category: <strong id="sumCategory" class="text-dark">All</strong> |
          Rotation: <strong id="sumRotation" class="text-dark">Normal (0°)</strong>
        </div>
        <div class="mt-2">
          <span class="badge bg-primary fs-6 fw-bold px-3 py-1.5 rounded-pill">
            <span id="sumSelectedCount"><?php echo count($items); ?></span> Covers will be generated
          </span>
          <span class="text-muted small ms-2 fw-semibold">(1 Item = 1 Page)</span>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-primary rounded-pill px-4 fw-semibold shadow-sm" id="btnLivePreview" onclick="previewSampleCover()">
          <i class="bi bi-eye-fill me-1"></i> Preview Sample
        </button>

        <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold shadow-sm py-2">
          <i class="bi bi-file-earmark-pdf-fill me-1"></i> Generate PDF (New Tab)
        </button>
      </div>
    </div>
  </div>

  <!-- Items Selection Table -->
  <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom">
      <div class="d-flex align-items-center gap-3">
        <h6 class="mb-0 fw-bold text-dark">
          <i class="bi bi-card-checklist text-primary me-2"></i>Select Items (<span id="tableItemCount"><?php echo count($items); ?></span> items available)
        </h6>
        <div class="btn-group btn-group-sm rounded-pill border" role="group">
          <button type="button" class="btn btn-light fw-semibold" onclick="toggleSelectAll(true)">Select All</button>
          <button type="button" class="btn btn-light fw-semibold" onclick="toggleSelectAll(false)">Unselect All</button>
        </div>
      </div>

      <div style="width: 250px;">
        <input type="text" id="txtSearchTable" class="form-control form-control-sm rounded-pill px-3" placeholder="Search item name or code..." oninput="searchTable()">
      </div>
    </div>

    <div class="table-responsive" style="max-height: 520px;">
      <table class="table table-hover align-middle mb-0" id="itemsTable">
        <thead class="table-light sticky-top">
          <tr>
            <th width="45" class="text-center">
              <input type="checkbox" id="chkMaster" class="form-check-input item-checkbox" checked onchange="toggleSelectAll(this.checked)">
            </th>
            <th width="100">Item Code</th>
            <th>Item Name</th>
            <th width="100">Category</th>
            <th width="120">Date</th>
            <th width="110">Time</th>
            <th width="140">Stage</th>
            <th width="140">Venue</th>
          </tr>
        </thead>
        <tbody id="itemsTableBody">
          <?php if (!empty($items)): ?>
            <?php foreach ($items as $it): ?>
              <tr class="item-row" data-name="<?php echo strtolower(esc($it['item_name'])); ?>" data-code="<?php echo esc($it['item_code']); ?>">
                <td class="text-center">
                  <input type="checkbox" name="selected_items[]" value="<?php echo esc($it['item_code']); ?>" class="form-check-input item-checkbox item-select-check" checked onchange="updateSelectedCount()">
                </td>
                <td><span class="badge bg-secondary-subtle text-secondary fw-bold rounded-pill px-2.5"><?php echo esc($it['item_code']); ?></span></td>
                <td class="fw-semibold text-dark"><?php echo esc($it['item_name']); ?></td>
                <td>
                  <?php
                    $catCode = $it['category_code'] ?? 'GENERAL';
                    $bgClass = 'bg-primary-subtle text-primary';
                    if ($catCode === 'LP') $bgClass = 'bg-success-subtle text-success';
                    elseif ($catCode === 'UP') $bgClass = 'bg-info-subtle text-info';
                    elseif ($catCode === 'HS') $bgClass = 'bg-warning-subtle text-warning';
                    elseif ($catCode === 'HSS') $bgClass = 'bg-danger-subtle text-danger';
                  ?>
                  <span class="badge <?php echo $bgClass; ?> fw-bold rounded-pill px-2"><?php echo esc($it['category_display'] ?? ($catCode . ' TAMIL KALAIVIZHA')); ?></span>
                </td>
                <td class="text-muted small"><?php echo esc($it['date_formatted']); ?></td>
                <td class="text-muted small"><?php echo esc($it['time_formatted']); ?></td>
                <td><span class="badge bg-light text-dark border"><?php echo esc($it['stage_display']); ?></span></td>
                <td class="text-muted small"><?php echo esc($it['venue_display']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center py-4 text-muted">No items found for this sub-district.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>

</form>

<!-- Live Sample Preview Modal -->
<div class="modal fade" id="modalPreviewSample" tabindex="-1" aria-labelledby="modalPreviewSampleLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header border-bottom py-3 px-4">
        <h5 class="modal-title fw-bold" id="modalPreviewSampleLabel">
          <i class="bi bi-eye-fill text-primary me-2"></i>Sample Cover Preview
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 text-center bg-light" id="previewModalBody">
        <div id="previewLoadingSpinner" class="py-5">
          <div class="spinner-border text-primary" role="status"></div>
          <div class="small text-muted mt-2">Loading sample cover...</div>
        </div>
        <div id="previewCardFrame" class="d-none mx-auto bg-white shadow-sm p-4 rounded-3 border text-start" style="max-width: 520px; min-height: 400px; font-family: 'TAU-Paalai', Arial, sans-serif;">
          <!-- Dynamically filled by JS -->
        </div>
      </div>
      <div class="modal-footer border-top py-2 px-4">
        <button type="button" class="btn btn-secondary rounded-pill btn-sm px-4" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  updateSummaryCard();
  updateSelectedCount();
});

function updateSummaryCard() {
  var sel = document.getElementById('cmbTemplate');
  var opt = sel.options[sel.selectedIndex];
  if (opt) {
    document.getElementById('sumTemplateName').innerText = opt.text;
    document.getElementById('sumPaper').innerText = (opt.getAttribute('data-paper') || 'A4') + ' ' + (opt.getAttribute('data-orientation') || 'Portrait');
  }
  var catSel = document.getElementById('cmbCategory');
  if (catSel && document.getElementById('sumCategory')) {
    document.getElementById('sumCategory').innerText = catSel.options[catSel.selectedIndex].text;
  }
  var rotSel = document.getElementById('cmbRotation');
  if (rotSel && document.getElementById('sumRotation')) {
    document.getElementById('sumRotation').innerText = rotSel.options[rotSel.selectedIndex].text.split('(')[0].trim();
  }
}

function updateSelectedCount() {
  var checks = document.querySelectorAll('.item-select-check:checked');
  var total = checks.length;
  document.getElementById('sumSelectedCount').innerText = total;

  var allChecks = document.querySelectorAll('.item-select-check');
  document.getElementById('chkMaster').checked = (total > 0 && total === allChecks.length);
}

function toggleSelectAll(check) {
  var checks = document.querySelectorAll('.item-select-check');
  checks.forEach(function(c) {
    // Only check if visible
    var row = c.closest('tr');
    if (!row || row.style.display !== 'none') {
      c.checked = check;
    }
  });
  document.getElementById('chkMaster').checked = check;
  updateSelectedCount();
}

function searchTable() {
  var q = document.getElementById('txtSearchTable').value.toLowerCase().trim();
  var rows = document.querySelectorAll('#itemsTableBody tr.item-row');
  var matchCount = 0;

  rows.forEach(function(r) {
    var name = r.getAttribute('data-name') || '';
    var code = r.getAttribute('data-code') || '';
    if (name.includes(q) || code.includes(q)) {
      r.style.display = '';
      matchCount++;
    } else {
      r.style.display = 'none';
    }
  });

  document.getElementById('tableItemCount').innerText = matchCount;
  updateSelectedCount();
}

function filterItems() {
  updateSummaryCard();

  var category = document.getElementById('cmbCategory').value;
  var date = document.getElementById('cmbDate').value;
  var stage = document.getElementById('cmbStage').value;
  var venue = document.getElementById('cmbVenue').value;

  var formData = new FormData();
  formData.append('category', category);
  formData.append('date', date);
  formData.append('stage', stage);
  formData.append('venue', venue);

  var tbody = document.getElementById('itemsTableBody');
  tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Filtering items...</td></tr>';

  fetch('<?php echo base_url("cover/ajax_filter_items"); ?>', {
    method: 'POST',
    body: formData
  })
  .then(function(r) { return r.json(); })
  .then(function(res) {
    if (res.status && res.items) {
      renderFilteredTable(res.items);
    } else {
      tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">No items matched the selected filters.</td></tr>';
      document.getElementById('tableItemCount').innerText = '0';
      updateSelectedCount();
    }
  })
  .catch(function(err) {
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Error loading items: ' + err.message + '</td></tr>';
  });
}

function renderFilteredTable(items) {
  var tbody = document.getElementById('itemsTableBody');
  tbody.innerHTML = '';
  document.getElementById('tableItemCount').innerText = items.length;

  if (!items.length) {
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">No items found for selected filter.</td></tr>';
    updateSelectedCount();
    return;
  }

  items.forEach(function(it) {
    var catCode = it.category_code || 'GENERAL';
    var bgClass = 'bg-primary-subtle text-primary';
    if (catCode === 'LP') bgClass = 'bg-success-subtle text-success';
    else if (catCode === 'UP') bgClass = 'bg-info-subtle text-info';
    else if (catCode === 'HS') bgClass = 'bg-warning-subtle text-warning';
    else if (catCode === 'HSS') bgClass = 'bg-danger-subtle text-danger';

    var tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.setAttribute('data-name', (it.item_name || '').toLowerCase());
    tr.setAttribute('data-code', it.item_code);

    tr.innerHTML = `
      <td class="text-center">
        <input type="checkbox" name="selected_items[]" value="${it.item_code}" class="form-check-input item-checkbox item-select-check" checked onchange="updateSelectedCount()">
      </td>
      <td><span class="badge bg-secondary-subtle text-secondary fw-bold rounded-pill px-2.5">${it.item_code}</span></td>
      <td class="fw-semibold text-dark">${it.item_name}</td>
      <td><span class="badge ${bgClass} fw-bold rounded-pill px-2">${it.category_display || (catCode + ' TAMIL KALAIVIZHA')}</span></td>
      <td class="text-muted small">${it.date_formatted || '-'}</td>
      <td class="text-muted small">${it.time_formatted || '-'}</td>
      <td><span class="badge bg-light text-dark border">${it.stage_display || '-'}</span></td>
      <td class="text-muted small">${it.venue_display || '-'}</td>
    `;
    tbody.appendChild(tr);
  });

  updateSelectedCount();
}

function previewSampleCover() {
  var firstChecked = document.querySelector('.item-select-check:checked');
  var itemCode = firstChecked ? firstChecked.value : '';
  var tmplId = document.getElementById('cmbTemplate') ? document.getElementById('cmbTemplate').value : '';

  var m = new bootstrap.Modal(document.getElementById('modalPreviewSample'));
  m.show();

  var spinner = document.getElementById('previewLoadingSpinner');
  var frame = document.getElementById('previewCardFrame');
  spinner.classList.remove('d-none');
  frame.classList.add('d-none');

  fetch('<?php echo base_url("cover/ajax_preview_data"); ?>?item_code=' + encodeURIComponent(itemCode) + '&template_id=' + encodeURIComponent(tmplId))
  .then(function(r) { return r.json(); })
  .then(function(res) {
    spinner.classList.add('d-none');
    frame.classList.remove('d-none');

    var d = res.data || {};
    var itemCodeDisplay = d.item_code || '101';
    var itemNameDisplay = d.item_name || 'Item Name';
    var itemCombo = itemCodeDisplay + ' - ' + itemNameDisplay;
    var categoryDisplay = d.category || 'HS TAMIL KALAIVIZHA';

    frame.innerHTML = `
      <div style="position: relative; border: 2px solid #64748b; border-radius: 8px; background: #ffffff; padding: 22px 26px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); font-family: 'TAU-Pallai', 'TAU-Paalai', Arial, sans-serif;">
        <!-- Flap Simulation Tab -->
        <div style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 75px; height: 12px; background: #f8fafc; border: 1px dashed #94a3b8; border-top: none; border-radius: 0 0 8px 8px;"></div>
        
        <!-- Header Title -->
        <div class="text-center" style="font-size: 14pt; font-weight: bold; color: #000000; letter-spacing: 0.5px; line-height: 1.2;">
          TAMIL KALAIVIZHA 2026-27
        </div>
        
        <!-- Sub-District -->
        <div class="text-center mt-1 text-secondary fw-semibold" style="font-size: 11pt;">
          ${d.sub_district ? d.sub_district + ' Sub-District' : 'Sub-District'}
        </div>

        <!-- Category Box (Shown Once in a clean rectangular bordered box) -->
        <div class="text-center my-3">
          <div style="display: inline-block; border: 1.5px solid #000000; padding: 4px 24px; font-weight: bold; font-size: 13pt; color: #000000; letter-spacing: 0.5px; background: #ffffff;">
            ${categoryDisplay}
          </div>
        </div>

        <!-- Item Code & Name (Using hyphen separator) -->
        <div class="text-center my-3">
          <div style="font-size: 18pt; font-weight: bold; color: #000000; letter-spacing: 0.5px;">
            ${itemCombo}
          </div>
        </div>

        <!-- Footer Row: Date & Time on left, Stage & Venue on right -->
        <div class="d-flex justify-content-between align-items-end mt-4 pt-2 border-top" style="font-size: 11pt; color: #1e293b;">
          <div class="text-start">
            <div style="font-weight: bold;">Date: <span style="font-weight: normal;">${d.date || '-'}</span></div>
            <div>Time: <span style="font-weight: normal;">${d.time || '-'}</span></div>
          </div>
          <div class="text-end">
            <div style="font-weight: bold;">Stage: <span style="font-weight: normal;">${d.stage || '-'}</span></div>
            <div>Venue: <span style="font-weight: normal;">${d.venue || '-'}</span></div>
          </div>
        </div>
      </div>
      <div class="text-center text-muted small mt-2">
        <i class="bi bi-info-circle me-1 text-primary"></i> Preview matches the DL Envelope Layout (220 × 110 mm)
      </div>
    `;
  })
  .catch(function(err) {
    spinner.classList.add('d-none');
    frame.classList.remove('d-none');
    frame.innerHTML = '<div class="alert alert-danger p-3">Failed to load preview: ' + err.message + '</div>';
  });
}
</script>
