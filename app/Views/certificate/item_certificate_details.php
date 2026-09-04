<?php
echo form_open('certificate/certificate/list_item_wise', array('id' => 'formFestSelect', 'method' => 'get'));
$cur_fest_id = $selected_fest_id ?? 1;
$cur_fest_name = $fest_name ?? 'Festival Items';
$items = $itempart ?? [];
$total_items = count($items);
$total_printed = 0;
foreach ($items as $it) {
  if (!empty($it['is_certificate_printed']) && $it['is_certificate_printed'] == 1) {
    $total_printed++;
  }
}
$total_ready = $total_items - $total_printed;
?>

<div class="pagetitle d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
  <div>
    <h1 class="h4 mb-1 text-gray-800 fw-bold d-flex align-items-center gap-2">
      <i class="bi bi-patch-check-fill text-primary"></i> Certificate - Item Wise
    </h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0" style="font-size: 13.5px;">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome">Home</a></li>
        <li class="breadcrumb-item">Certificate</li>
        <li class="breadcrumb-item active">Item Wise</li>
      </ol>
    </nav>
  </div>

  <!-- Festival Category Quick Switcher -->
  <?php if (!empty($fest) && is_array($fest)): ?>
    <div class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-4 shadow-sm border">
      <label for="cmbFestType" class="text-secondary fw-semibold text-nowrap mb-0" style="font-size: 13.5px;">
        <i class="bi bi-trophy text-warning me-1"></i> Festival:
      </label>
      <select name="cmbFestType" id="cmbFestType" class="form-select form-select-sm border-0 bg-light fw-bold text-dark" style="font-size: 14px; min-width: 180px;" onchange="this.form.submit();">
        <?php foreach ($fest as $fid => $fname): ?>
          <option value="<?php echo $fid; ?>" <?php echo ((string)$fid === (string)$cur_fest_id) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($fname); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  <?php endif; ?>
</div>
<?php echo form_close(); ?>

<!-- KPI Overview Bar -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
  <div class="card-body p-3">
    <div class="row align-items-center g-3">
      <div class="col-md-6 col-12">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-3 bg-primary-subtle text-primary p-2.5 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
            <i class="bi bi-grid-3x3-gap-fill fs-5"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-0 text-dark" style="font-size: 15px;"><?php echo htmlspecialchars($cur_fest_name); ?></h6>
            <small class="text-muted" style="font-size: 13px;">Select an item to view winners and print certificates</small>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-12">
        <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
          <span class="badge bg-light text-dark border px-3 py-2 fw-semibold" style="font-size: 13px;">
            Total: <strong class="text-primary"><?php echo $total_items; ?></strong>
          </span>
          <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-semibold" style="font-size: 13px;">
            <i class="bi bi-check-circle-fill me-1"></i> Printed: <strong><?php echo $total_printed; ?></strong>
          </span>
          <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 fw-semibold" style="font-size: 13px;">
            <i class="bi bi-clock-history me-1"></i> Ready: <strong><?php echo $total_ready; ?></strong>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Items Table Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
  <div class="card-header bg-white py-3 px-4 border-bottom border-light d-flex flex-wrap align-items-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-list-check text-primary"></i>
      <h6 class="mb-0 fw-bold text-dark" style="font-size: 15px;">Participated Items List</h6>
    </div>

    <div class="d-flex align-items-center gap-3">
      <div class="input-group input-group-sm" style="max-width: 250px;">
        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
        <input type="text" id="itemTableSearch" class="form-control bg-light border-start-0" placeholder="Search item or code..." onkeyup="filterItemTable()" style="font-size: 13.5px;">
      </div>
      <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-flex align-items-center gap-1.5" onclick="window.print();" style="font-size: 13px;">
        <i class="bi bi-printer"></i> Print
      </button>
    </div>
  </div>

  <div class="card-body p-0">
    <?php echo form_open('', array('id' => 'formIWPq')); ?>
    <input type="hidden" name="hidItemId" id="hidItemId" value="" />
    
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" id="itemCertTable" style="font-size: 14px;">
        <thead class="table-light text-uppercase text-secondary border-bottom" style="font-size: 12.5px; letter-spacing: 0.5px;">
          <tr>
            <th class="py-3 px-3 text-center" style="width: 55px;">#</th>
            <th class="py-3 px-3 text-center" style="width: 105px;">Status</th>
            <th class="py-3 px-3">Item Code & Name</th>
            <th class="py-3 px-3 text-center" style="width: 90px;">Type</th>
            <th class="py-3 px-3 text-center" style="width: 85px;">Students</th>
            <th class="py-3 px-3 text-center" style="width: 85px;">Present</th>
            <th class="py-3 px-3 text-center" style="width: 65px;"><span class="badge bg-success-subtle text-success px-2 py-1" style="font-size: 12px;">A</span></th>
            <th class="py-3 px-3 text-center" style="width: 65px;"><span class="badge bg-info-subtle text-info px-2 py-1" style="font-size: 12px;">B</span></th>
            <th class="py-3 px-3 text-center" style="width: 65px;"><span class="badge bg-warning-subtle text-warning border px-2 py-1" style="font-size: 12px;">C</span></th>
            <th class="py-3 px-3 text-center" style="width: 120px;">Action</th>
          </tr>
        </thead>
        <tbody class="border-top-0">
          <?php 
            $slno = 0;
            if (!empty($items) && count($items) > 0):
              foreach ($items as $row): 
                $slno++;
                $isGroup = ($row['item_type'] === 'G');
                $isPrinted = (!empty($row['is_certificate_printed']) && $row['is_certificate_printed'] == 1);
                $cpt = $this->General_Model->get_record_count('sd_participant_item_details', 'is_captain="Y" AND item_code=' . (int)$row['item_code'], 'participant_id');
          ?>
          <tr>
            <td class="text-center fw-semibold text-muted py-3 px-3"><?php echo $slno; ?></td>
            <td class="text-center py-3 px-3">
              <?php if ($isPrinted): ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" style="font-size: 12px;">
                  <i class="bi bi-check-circle-fill"></i> Printed
                </span>
              <?php else: ?>
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" style="font-size: 12px;">
                  <i class="bi bi-clock-history"></i> Ready
                </span>
              <?php endif; ?>
            </td>
            <td class="py-3 px-3">
              <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border px-2 py-1 font-monospace fw-bold" style="font-size: 12.5px;">
                  <?php echo $row['item_code']; ?>
                </span>
                <a href="javascript:void(0)" onclick="getItemCertificate('<?php echo $row['item_code']; ?>')" class="fw-semibold text-decoration-none text-dark link-primary" style="font-size: 14px;">
                  <?php echo htmlspecialchars($row['item_name']); ?>
                </a>
              </div>
            </td>
            <td class="text-center py-3 px-3">
              <?php if ($isGroup): ?>
                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1" style="font-size: 12px;">
                  Group
                </span>
              <?php else: ?>
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1" style="font-size: 12px;">
                  Single
                </span>
              <?php endif; ?>
            </td>
            <td class="text-center fw-semibold text-dark py-3 px-3"><?php echo (int)$cpt; ?></td>
            <td class="text-center fw-semibold text-success py-3 px-3"><?php echo (int)$row['participated_no']; ?></td>
            <td class="text-center fw-bold text-success py-3 px-3"><?php echo (int)($row['a_grade'] ?? 0); ?></td>
            <td class="text-center fw-bold text-info py-3 px-3"><?php echo (int)($row['b_grade'] ?? 0); ?></td>
            <td class="text-center fw-bold text-warning-emphasis py-3 px-3"><?php echo (int)($row['c_grade'] ?? 0); ?></td>
            <td class="text-center py-3 px-3">
              <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1 shadow-none" style="font-size: 13px;" onclick="getItemCertificate('<?php echo $row['item_code']; ?>')">
                <span>Select</span> <i class="bi bi-chevron-right"></i>
              </button>
            </td>
          </tr>
          <?php 
              endforeach; 
            else:
          ?>
          <tr>
            <td colspan="10" class="text-center py-5 text-muted">
              <i class="bi bi-inbox fs-3 d-block mb-2"></i>
              <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">No Items Found for this Festival</h6>
              <p class="small mb-0" style="font-size: 13px;">Please select another festival category above.</p>
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php echo form_close(); ?>
  </div>
</div>

<script>
function getItemCertificate(itemcode) {
  var hid = document.getElementById('hidItemId');
  var form = document.getElementById('formIWPq');
  if (hid && form) {
    hid.value = itemcode;
    form.action = '<?php echo base_url("certificate/certificate/get_certificate_itemwise"); ?>';
    form.submit();
  }
}

function filterItemTable() {
  const input = document.getElementById('itemTableSearch');
  const filter = input.value.toLowerCase();
  const table = document.getElementById('itemCertTable');
  if (!table) return;
  const rows = table.getElementsByTagName('tr');

  for (let i = 1; i < rows.length; i++) {
    const text = rows[i].textContent || rows[i].innerText;
    if (text.toLowerCase().indexOf(filter) > -1) {
      rows[i].style.display = '';
    } else {
      rows[i].style.display = 'none';
    }
  }
}
</script>