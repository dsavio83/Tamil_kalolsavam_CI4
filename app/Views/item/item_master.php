<?php
$total_items = count($retvalue ?? []);
$single_items = 0;
$group_items = 0;
$off_stage_items = 0;
if (!empty($retvalue)) {
    foreach ($retvalue as $r) {
        if (($r['item_type'] ?? '') === 'S') $single_items++;
        else if (($r['item_type'] ?? '') === 'G') $group_items++;
        if (($r['is_off_stage'] ?? '') === 'Y') $off_stage_items++;
    }
}
$is_editing = (isset($show_edit_item) && $show_edit_item == '1');
$sel_item = $selected_item[0] ?? [];
?>

<div class="pagetitle d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
  <div>
    <h1 class="h4 mb-1 text-gray-800 fw-bold d-flex align-items-center gap-2">
      <i class="bi bi-collection-play-fill text-primary"></i> Item Master Directory
    </h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0" style="font-size: 13.5px;">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome">Home</a></li>
        <li class="breadcrumb-item">Item</li>
        <li class="breadcrumb-item active">Item Master</li>
      </ol>
    </nav>
  </div>

  <div>
    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 shadow-sm fw-semibold" style="font-size: 14px;" onclick="openAddModal()">
      <i class="bi bi-plus-circle-fill fs-5"></i>
      <span>Add New Item</span>
    </button>
  </div>
</div>

<!-- KPI Metrics Cards -->
<div class="row g-3 mb-4">
  <div class="col-xl-3 col-sm-6 col-12">
    <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
      <div class="card-body p-3.5 d-flex align-items-center gap-3">
        <div class="rounded-3 bg-primary bg-gradient text-white d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 48px; height: 48px;">
          <i class="bi bi-trophy-fill fs-4"></i>
        </div>
        <div>
          <span class="text-muted text-uppercase fw-semibold" style="font-size: 11.5px; letter-spacing: 0.5px;">Total Events</span>
          <h4 class="mb-0 fw-bold text-dark" style="font-size: 1.45rem; line-height: 1.2;"><?php echo $total_items; ?></h4>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-sm-6 col-12">
    <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
      <div class="card-body p-3.5 d-flex align-items-center gap-3">
        <div class="rounded-3 bg-info bg-gradient text-white d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 48px; height: 48px;">
          <i class="bi bi-person-fill fs-4"></i>
        </div>
        <div>
          <span class="text-muted text-uppercase fw-semibold" style="font-size: 11.5px; letter-spacing: 0.5px;">Single Events</span>
          <h4 class="mb-0 fw-bold text-info" style="font-size: 1.45rem; line-height: 1.2;"><?php echo $single_items; ?></h4>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-sm-6 col-12">
    <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
      <div class="card-body p-3.5 d-flex align-items-center gap-3">
        <div class="rounded-3 text-white d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 48px; height: 48px; background-color: #8b5cf6;">
          <i class="bi bi-people-fill fs-4"></i>
        </div>
        <div>
          <span class="text-muted text-uppercase fw-semibold" style="font-size: 11.5px; letter-spacing: 0.5px;">Group Events</span>
          <h4 class="mb-0 fw-bold" style="font-size: 1.45rem; line-height: 1.2; color: #7c3aed;"><?php echo $group_items; ?></h4>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-sm-6 col-12">
    <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
      <div class="card-body p-3.5 d-flex align-items-center gap-3">
        <div class="rounded-3 bg-warning bg-gradient text-white d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 48px; height: 48px;">
          <i class="bi bi-pencil-square fs-4"></i>
        </div>
        <div>
          <span class="text-muted text-uppercase fw-semibold" style="font-size: 11.5px; letter-spacing: 0.5px;">Off-Stage Events</span>
          <h4 class="mb-0 fw-bold text-warning-emphasis" style="font-size: 1.45rem; line-height: 1.2;"><?php echo $off_stage_items; ?></h4>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Main Items Table Card with Search & Controls -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
  <div class="card-header bg-white py-3.5 px-4 border-bottom border-light">
    <div class="row align-items-center g-3">
      <div class="col-lg-4 col-md-12">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-list-stars text-primary fs-5"></i>
          <h6 class="mb-0 fw-bold text-dark" style="font-size: 15.5px;">Item Master Directory</h6>
          <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 ms-1" style="font-size: 12px;"><?php echo $total_items; ?></span>
        </div>
      </div>

      <div class="col-lg-8 col-md-12">
        <div class="d-flex flex-wrap align-items-center justify-content-lg-end gap-2.5">
          <!-- Festival Filter -->
          <div class="d-flex align-items-center gap-1.5">
            <label for="festFilter" class="text-muted small text-nowrap mb-0 d-none d-sm-inline" style="font-size: 13px;">Festival:</label>
            <select id="festFilter" class="form-select form-select-sm rounded-3 border-secondary-subtle" style="font-size: 13.5px; min-width: 160px;" onchange="applyFilters()">
              <option value="">All Festivals</option>
              <?php if (!empty($festivals)): ?>
                <?php foreach ($festivals as $fid => $fname): if (empty($fid)) continue; ?>
                  <option value="<?php echo htmlspecialchars($fname); ?>"><?php echo htmlspecialchars($fname); ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <!-- Entries Per Page -->
          <div class="d-flex align-items-center gap-1.5">
            <label for="pageSizeSelect" class="text-muted small text-nowrap mb-0 d-none d-sm-inline" style="font-size: 13px;">Show:</label>
            <select id="pageSizeSelect" class="form-select form-select-sm rounded-3 border-secondary-subtle" style="font-size: 13.5px; width: 75px;" onchange="changePageSize(this.value)">
              <option value="10">10</option>
              <option value="25" selected>25</option>
              <option value="50">50</option>
              <option value="all">All</option>
            </select>
          </div>

          <!-- Instant Search Box -->
          <div class="input-group input-group-sm" style="min-width: 220px; max-width: 280px;">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" id="itemTableSearch" class="form-control bg-light border-start-0" placeholder="Search code, name, category..." onkeyup="applyFilters()" style="font-size: 13.5px;">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" id="itemMasterTable" style="font-size: 14px;">
        <thead class="table-light text-uppercase text-secondary border-bottom" style="font-size: 12px; letter-spacing: 0.5px;">
          <tr>
            <th class="ps-3 py-3 text-center sortable" style="width: 50px; cursor: pointer;" onclick="sortTable(0, 'number')">
              <span>#</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="py-3 sortable" style="width: 100px; cursor: pointer;" onclick="sortTable(1, 'number')">
              <span>Code</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="py-3 sortable" style="cursor: pointer;" onclick="sortTable(2, 'string')">
              <span>Item Name</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="py-3 sortable" style="cursor: pointer;" onclick="sortTable(3, 'string')">
              <span>Festival</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="py-3 sortable" style="cursor: pointer;" onclick="sortTable(4, 'string')">
              <span>Vibhagam</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="py-3 text-center sortable" style="width: 90px; cursor: pointer;" onclick="sortTable(5, 'string')">
              <span>Gender</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="py-3 text-center sortable" style="width: 85px; cursor: pointer;" onclick="sortTable(6, 'string')">
              <span>Type</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="py-3 text-center sortable" style="width: 100px; cursor: pointer;" onclick="sortTable(7, 'number')">
              <span>Duration</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="py-3 text-center sortable" style="width: 95px; cursor: pointer;" onclick="sortTable(8, 'string')">
              <span>Stage</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="pe-3 py-3 text-end" style="width: 110px;">
              <span>Actions</span>
            </th>
          </tr>
        </thead>
        <tbody id="itemTableBody" class="border-top-0">
          <?php 
          $slno = 0;
          if (!empty($retvalue) && count($retvalue) > 0):
              foreach ($retvalue as $val):
                  $slno++;
                  $is_group = ($val['item_type'] === 'G');
                  $is_off = ($val['is_off_stage'] === 'Y');
                  $gen = $val['gender'] ?? 'C';
                  $time_str = ($val['max_time'] > 0) ? $val['max_time'] . ' ' . (($val['time_type'] === 'S') ? 'Sec' : 'Min') : '—';
                  $json_item = htmlspecialchars(json_encode($val), ENT_QUOTES, 'UTF-8');
          ?>
          <tr data-fest="<?php echo htmlspecialchars($val['fest_name'] ?? ''); ?>" data-item-id="<?php echo $val['item_id']; ?>">
            <td class="text-center fw-semibold text-muted ps-3 py-2.5"><?php echo $slno; ?></td>
            <td class="py-2.5 font-monospace fw-bold text-dark">
              <span class="badge bg-light text-dark border px-2.5 py-1" style="font-size: 12.5px;">
                <?php echo htmlspecialchars($val['item_code']); ?>
              </span>
            </td>
            <td class="py-2.5">
              <span class="fw-semibold text-dark link-primary" style="cursor: pointer;" onclick='openEditFromData(<?php echo $json_item; ?>)'>
                <?php echo htmlspecialchars($val['item_name']); ?>
              </span>
              <?php if (!empty($val['max_participants']) && $val['max_participants'] > 1): ?>
                <small class="text-muted d-block" style="font-size: 12px;">
                  Max Participants: <?php echo (int)$val['max_participants']; ?><?php echo !empty($val['max_pinnani']) ? ' + ' . (int)$val['max_pinnani'] . ' Pinnani' : ''; ?>
                </small>
              <?php endif; ?>
            </td>
            <td class="py-2.5" style="font-size: 13.5px;">
              <span class="text-dark"><?php echo htmlspecialchars($val['fest_name'] ?? '—'); ?></span>
            </td>
            <td class="py-2.5 text-secondary" style="font-size: 13.5px;">
              <?php echo htmlspecialchars($val['vibhagam_name'] ?? '—'); ?>
            </td>
            <td class="py-2.5 text-center">
              <?php 
                if ($gen === 'B') {
                  echo '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1" style="font-size: 12px;"><i class="bi bi-gender-male me-1"></i>Boy</span>';
                } else if ($gen === 'G') {
                  echo '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="font-size: 12px;"><i class="bi bi-gender-female me-1"></i>Girl</span>';
                } else {
                  echo '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1" style="font-size: 12px;">Common</span>';
                }
              ?>
            </td>
            <td class="py-2.5 text-center">
              <?php if ($is_group): ?>
                <span class="badge border rounded-pill px-2.5 py-1" style="background-color: #f5f3ff; color: #7c3aed; border-color: #ddd6fe; font-size: 12px;">
                  Group
                </span>
              <?php else: ?>
                <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1" style="font-size: 12px;">
                  Single
                </span>
              <?php endif; ?>
            </td>
            <td class="py-2.5 text-center fw-semibold text-dark" data-time="<?php echo (int)$val['max_time']; ?>" style="font-size: 13px;">
              <?php echo $time_str; ?>
            </td>
            <td class="py-2.5 text-center">
              <?php if ($is_off): ?>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1" style="font-size: 12px;">
                  Off-Stage
                </span>
              <?php else: ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 12px;">
                  On-Stage
                </span>
              <?php endif; ?>
            </td>
            <td class="pe-3 py-2.5 text-end">
              <div class="d-inline-flex align-items-center gap-1">
                <button type="button" class="btn btn-sm btn-outline-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit Item" onclick='openEditFromData(<?php echo $json_item; ?>)'>
                  <i class="bi bi-pencil-square fs-6"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Delete Item" onclick="confirmDeleteItem('<?php echo $val['item_id']; ?>', '<?php echo htmlspecialchars($val['item_code']); ?>', '<?php echo htmlspecialchars(addslashes($val['item_name'])); ?>')">
                  <i class="bi bi-trash3-fill fs-6"></i>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Empty Results Fallback -->
    <div id="noResultsMessage" class="text-center py-5 d-none text-muted">
      <i class="bi bi-search fs-3 d-block mb-2"></i>
      <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">No Matching Items Found</h6>
      <p class="small mb-0" style="font-size: 13px;">Try adjusting your search keyword or festival filter.</p>
    </div>
  </div>

  <!-- Table Footer with Pagination Controls -->
  <div class="card-footer bg-white py-3 px-4 border-top border-light">
    <div class="row align-items-center g-3">
      <div class="col-md-6 col-12">
        <span class="text-muted" style="font-size: 13.5px;" id="paginationInfo">
          Showing 1 to <?php echo min(25, $total_items); ?> of <?php echo $total_items; ?> items
        </span>
      </div>
      <div class="col-md-6 col-12">
        <nav aria-label="Table pagination">
          <ul class="pagination pagination-sm justify-content-md-end mb-0" id="paginationControls">
            <!-- Dynamically populated via JavaScript -->
          </ul>
        </nav>
      </div>
    </div>
  </div>
</div>

<!-- ======================================================= -->
<!-- STYLISH ADD / EDIT ITEM BOOTSTRAP MODAL DIALOG          -->
<!-- ======================================================= -->
<div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true" style="z-index: 10050;">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" style="margin-top: 80px !important; margin-bottom: 30px !important;">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <!-- Modal Header -->
      <div class="modal-header bg-primary bg-gradient text-white py-3 px-4 border-0">
        <div class="d-flex align-items-center gap-2.5">
          <div class="rounded-circle bg-white text-primary p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
            <i class="bi bi-award-fill fs-5" id="modalHeaderIcon"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold mb-0 text-white" id="itemModalLabel" style="font-size: 1.15rem;">Add New Competition Item</h5>
            <small class="text-white-50" style="font-size: 12.5px;">Fill in competition details and participation limits</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Form -->
      <?php echo form_open('item/item_master/add_item_details', array('id' => 'itemModalForm', 'name' => 'itemModalForm')); ?>
      <input type="hidden" name="ItemIdty" id="modalItemIdty" value="" />

      <div class="modal-body p-4 bg-light">
        <!-- Section 1: Basic Information -->
        <div class="card border-0 shadow-sm rounded-3 mb-3 bg-white">
          <div class="card-header bg-white py-2.5 px-3 border-bottom border-light d-flex align-items-center gap-2">
            <i class="bi bi-info-circle-fill text-primary"></i>
            <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px; font-size: 12.5px;">1. Basic Information</span>
          </div>
          <div class="card-body p-3">
            <div class="row g-3">
              <div class="col-md-4 col-12">
                <label for="modal_item_code" class="form-label fw-semibold text-secondary small mb-1" style="font-size: 13px;">
                  Item Code <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted border-secondary-subtle"><i class="bi bi-hash"></i></span>
                  <input type="text" class="form-control font-monospace border-secondary-subtle" id="modal_item_code" name="item_code" placeholder="e.g. 151" required style="font-size: 14px;" />
                </div>
              </div>

              <div class="col-md-8 col-12">
                <label for="modal_item_name" class="form-label fw-semibold text-secondary small mb-1" style="font-size: 13px;">
                  Item Name <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control border-secondary-subtle" id="modal_item_name" name="item_name" placeholder="e.g. STORY TELLING" required style="font-size: 14px;" />
              </div>

              <div class="col-md-6 col-12">
                <label for="modal_fest_id" class="form-label fw-semibold text-secondary small mb-1" style="font-size: 13px;">
                  Festival Category <span class="text-danger">*</span>
                </label>
                <?php echo form_dropdown('fest_id', $festivals, '', 'id="modal_fest_id" class="form-select border-secondary-subtle" required style="font-size: 14px;"'); ?>
              </div>

              <div class="col-md-6 col-12">
                <label for="modal_vibhagam_id" class="form-label fw-semibold text-secondary small mb-1" style="font-size: 13px;">
                  Vibhagam <span class="text-danger">*</span>
                </label>
                <?php echo form_dropdown('vibhagam_id', $vibhagams, '', 'id="modal_vibhagam_id" class="form-select border-secondary-subtle" required style="font-size: 14px;"'); ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 2: Competition Rules -->
        <div class="card border-0 shadow-sm rounded-3 mb-3 bg-white">
          <div class="card-header bg-white py-2.5 px-3 border-bottom border-light d-flex align-items-center gap-2">
            <i class="bi bi-sliders text-success"></i>
            <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px; font-size: 12.5px;">2. Participation Rules</span>
          </div>
          <div class="card-body p-3">
            <div class="row g-3">
              <div class="col-md-4 col-12">
                <label for="modal_gender" class="form-label fw-semibold text-secondary small mb-1" style="font-size: 13px;">Gender Eligibility</label>
                <?php 
                  $genders = array('C' => 'Common (All)', 'B' => 'Boy Only', 'G' => 'Girl Only');
                  echo form_dropdown('gender', $genders, 'C', 'id="modal_gender" class="form-select border-secondary-subtle" style="font-size: 14px;"'); 
                ?>
              </div>

              <div class="col-md-4 col-12">
                <label for="modal_item_type" class="form-label fw-semibold text-secondary small mb-1" style="font-size: 13px;">Event Type</label>
                <?php 
                  $item_types = array('S' => 'Single Event', 'G' => 'Group Event');
                  echo form_dropdown('item_type', $item_types, 'S', 'id="modal_item_type" class="form-select border-secondary-subtle" style="font-size: 14px;"'); 
                ?>
              </div>

              <div class="col-md-4 col-12">
                <label for="modal_is_off_stage" class="form-label fw-semibold text-secondary small mb-1" style="font-size: 13px;">Stage Classification</label>
                <?php 
                  $off_stage = array('N' => 'On-Stage Event', 'Y' => 'Off-Stage Event');
                  echo form_dropdown('is_off_stage', $off_stage, 'N', 'id="modal_is_off_stage" class="form-select border-secondary-subtle" style="font-size: 14px;"'); 
                ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 3: Time & Participant Limits -->
        <div class="card border-0 shadow-sm rounded-3 bg-white">
          <div class="card-header bg-white py-2.5 px-3 border-bottom border-light d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-warning"></i>
            <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px; font-size: 12.5px;">3. Time Duration & Limits</span>
          </div>
          <div class="card-body p-3">
            <div class="row g-3">
              <div class="col-md-3 col-6">
                <label for="modal_max_time" class="form-label fw-semibold text-secondary small mb-1" style="font-size: 13px;">Max Time</label>
                <input type="text" class="form-control border-secondary-subtle" id="modal_max_time" name="max_time" placeholder="e.g. 5" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g,'')" style="font-size: 14px;" />
              </div>

              <div class="col-md-3 col-6">
                <label for="modal_time_type" class="form-label fw-semibold text-secondary small mb-1" style="font-size: 13px;">Time Unit</label>
                <?php 
                  $time_types = array('M' => 'Minutes', 'S' => 'Seconds');
                  echo form_dropdown('time_type', $time_types, 'M', 'id="modal_time_type" class="form-select border-secondary-subtle" style="font-size: 14px;"'); 
                ?>
              </div>

              <div class="col-md-3 col-6">
                <label for="modal_max_participants" class="form-label fw-semibold text-secondary small mb-1" style="font-size: 13px;">Max Participants</label>
                <input type="text" class="form-control border-secondary-subtle" id="modal_max_participants" name="max_participants" placeholder="e.g. 1" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g,'')" style="font-size: 14px;" />
              </div>

              <div class="col-md-3 col-6">
                <label for="modal_max_pinnani" class="form-label fw-semibold text-secondary small mb-1" style="font-size: 13px;">Max Pinnani</label>
                <input type="text" class="form-control border-secondary-subtle" id="modal_max_pinnani" name="max_pinnani" placeholder="e.g. 0" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g,'')" style="font-size: 14px;" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer bg-white border-top border-light py-3 px-4">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal" style="font-size: 14px;">Cancel</button>
        <button type="submit" class="btn btn-primary rounded-pill px-4 d-inline-flex align-items-center gap-2 shadow-sm fw-semibold" id="modalSubmitBtn" style="font-size: 14px;">
          <i class="bi bi-check2-circle fs-5"></i>
          <span id="modalSubmitText">Save Item</span>
        </button>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>

<!-- ======================================================= -->
<!-- LIGHTWEIGHT HIGH-PERFORMANCE DATATABLE ENGINE           -->
<!-- ======================================================= -->
<script>
let allRows = [];
let filteredRows = [];
let currentPage = 1;
let pageSize = 25;
let currentSortCol = 1;
let currentSortDir = 'asc';

document.addEventListener('DOMContentLoaded', function() {
  const tbody = document.getElementById('itemTableBody');
  if (tbody) {
    allRows = Array.from(tbody.querySelectorAll('tr'));
    filteredRows = [...allRows];
    renderTable();
  }

  const modalEl = document.getElementById('itemModal');
  if (modalEl) {
    modalEl.addEventListener('show.bs.modal', function () {
      setTimeout(() => {
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
          backdrop.style.zIndex = '10040';
        }
      }, 20);
    });
  }

  <?php if ($is_editing && !empty($sel_item)): ?>
    openEditFromData(<?php echo json_encode($sel_item); ?>);
  <?php endif; ?>
});

function openAddModal() {
  const form = document.getElementById('itemModalForm');
  if (form) {
    form.reset();
    form.action = '<?php echo base_url("item/item_master/add_item_details"); ?>';
  }
  document.getElementById('modalItemIdty').value = '';
  document.getElementById('modal_item_code').value = '';
  document.getElementById('modal_item_name').value = '';
  document.getElementById('modal_fest_id').value = '';
  document.getElementById('modal_vibhagam_id').value = '';
  document.getElementById('modal_gender').value = 'C';
  document.getElementById('modal_item_type').value = 'S';
  document.getElementById('modal_max_time').value = '';
  document.getElementById('modal_time_type').value = 'M';
  document.getElementById('modal_max_participants').value = '1';
  document.getElementById('modal_max_pinnani').value = '0';
  document.getElementById('modal_is_off_stage').value = 'N';

  document.getElementById('itemModalLabel').innerText = 'Add New Competition Item';
  document.getElementById('modalSubmitText').innerText = 'Save Item';
  document.getElementById('modalHeaderIcon').className = 'bi bi-plus-circle-fill fs-5';
  
  const modalEl = document.getElementById('itemModal');
  const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
  modal.show();
}

function openEditFromData(item) {
  if (!item) return;
  const form = document.getElementById('itemModalForm');
  if (form) {
    form.action = '<?php echo base_url("item/item_master/update_item_detials"); ?>';
  }
  document.getElementById('modalItemIdty').value = item.item_id || '';
  document.getElementById('modal_item_code').value = item.item_code || '';
  document.getElementById('modal_item_name').value = item.item_name || '';
  document.getElementById('modal_fest_id').value = item.fest_id || '';
  document.getElementById('modal_vibhagam_id').value = item.vibhagam_id || '';
  document.getElementById('modal_gender').value = item.gender || 'C';
  document.getElementById('modal_item_type').value = item.item_type || 'S';
  document.getElementById('modal_max_time').value = item.max_time || '';
  document.getElementById('modal_time_type').value = item.time_type || 'M';
  document.getElementById('modal_max_participants').value = item.max_participants || '';
  document.getElementById('modal_max_pinnani').value = item.max_pinnani || '';
  document.getElementById('modal_is_off_stage').value = item.is_off_stage || 'N';

  document.getElementById('itemModalLabel').innerText = 'Edit Item: #' + item.item_code + ' - ' + item.item_name;
  document.getElementById('modalSubmitText').innerText = 'Update Item';
  document.getElementById('modalHeaderIcon').className = 'bi bi-pencil-square fs-5';

  const modalEl = document.getElementById('itemModal');
  const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
  modal.show();
}

function confirmDeleteItem(itemId, itemCode, itemName) {
  if (confirm('Are you sure you want to delete Item #' + itemCode + ' (' + itemName + ')?')) {
    const f = document.createElement('form');
    f.method = 'POST';
    f.action = '<?php echo base_url("item/item_master/delete_item_detials"); ?>';
    const h = document.createElement('input');
    h.type = 'hidden';
    h.name = 'ItemIdty';
    h.value = itemId;
    f.appendChild(h);
    document.body.appendChild(f);
    f.submit();
  }
}

function applyFilters() {
  const searchTerm = (document.getElementById('itemTableSearch').value || '').toLowerCase().trim();
  const festFilter = (document.getElementById('festFilter').value || '').trim();

  filteredRows = allRows.filter(row => {
    const text = row.textContent.toLowerCase();
    const festName = row.getAttribute('data-fest') || '';

    const matchesSearch = !searchTerm || text.includes(searchTerm);
    const matchesFest = !festFilter || festName === festFilter;

    return matchesSearch && matchesFest;
  });

  currentPage = 1;
  renderTable();
}

function changePageSize(size) {
  pageSize = size === 'all' ? allRows.length : parseInt(size, 10);
  currentPage = 1;
  renderTable();
}

function sortTable(colIndex, type) {
  if (currentSortCol === colIndex) {
    currentSortDir = currentSortDir === 'asc' ? 'desc' : 'asc';
  } else {
    currentSortCol = colIndex;
    currentSortDir = 'asc';
  }

  // Update header arrow icons
  document.querySelectorAll('#itemMasterTable th.sortable').forEach((th, idx) => {
    const icon = th.querySelector('.sort-icon');
    if (idx === colIndex) {
      icon.innerHTML = currentSortDir === 'asc' ? '▲' : '▼';
      icon.className = 'sort-icon text-primary fw-bold';
    } else {
      icon.innerHTML = '⇅';
      icon.className = 'sort-icon text-muted';
    }
  });

  filteredRows.sort((a, b) => {
    let valA, valB;
    if (colIndex === 7) {
      valA = parseInt(a.cells[7].getAttribute('data-time') || 0, 10);
      valB = parseInt(b.cells[7].getAttribute('data-time') || 0, 10);
    } else if (type === 'number') {
      valA = parseFloat(a.cells[colIndex].innerText.replace(/[^0-9.-]/g, '')) || 0;
      valB = parseFloat(b.cells[colIndex].innerText.replace(/[^0-9.-]/g, '')) || 0;
    } else {
      valA = a.cells[colIndex].innerText.toLowerCase().trim();
      valB = b.cells[colIndex].innerText.toLowerCase().trim();
    }

    if (valA < valB) return currentSortDir === 'asc' ? -1 : 1;
    if (valA > valB) return currentSortDir === 'asc' ? 1 : -1;
    return 0;
  });

  renderTable();
}

function renderTable() {
  const tbody = document.getElementById('itemTableBody');
  const noMsg = document.getElementById('noResultsMessage');
  const total = filteredRows.length;

  if (total === 0) {
    allRows.forEach(r => r.style.display = 'none');
    if (noMsg) noMsg.classList.remove('d-none');
    document.getElementById('paginationInfo').innerText = 'Showing 0 to 0 of 0 items';
    document.getElementById('paginationControls').innerHTML = '';
    return;
  }

  if (noMsg) noMsg.classList.add('d-none');

  const totalPages = Math.ceil(total / pageSize);
  if (currentPage > totalPages) currentPage = totalPages;
  if (currentPage < 1) currentPage = 1;

  const start = (currentPage - 1) * pageSize;
  const end = Math.min(start + pageSize, total);

  // Hide all rows first
  allRows.forEach(r => r.style.display = 'none');

  // Re-append sorted rows in slice order and show them
  for (let i = start; i < end; i++) {
    const row = filteredRows[i];
    row.style.display = '';
    row.cells[0].innerText = i + 1; // dynamically update Sl.No for current order
    tbody.appendChild(row);
  }

  // Update counter text
  document.getElementById('paginationInfo').innerText = 
    `Showing ${start + 1} to ${end} of ${total} items` + (total !== allRows.length ? ` (filtered from ${allRows.length} total)` : '');

  // Render Pagination controls
  renderPaginationControls(totalPages);
}

function renderPaginationControls(totalPages) {
  const container = document.getElementById('paginationControls');
  if (!container) return;

  if (totalPages <= 1) {
    container.innerHTML = '';
    return;
  }

  let html = '';

  // Prev Button
  html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
    <a class="page-link shadow-none" href="javascript:void(0)" onclick="goToPage(${currentPage - 1})" aria-label="Previous">
      <span aria-hidden="true">&laquo;</span>
    </a>
  </li>`;

  // Page numbers (smart window)
  let startPage = Math.max(1, currentPage - 2);
  let endPage = Math.min(totalPages, currentPage + 2);

  if (startPage > 1) {
    html += `<li class="page-item"><a class="page-link shadow-none" href="javascript:void(0)" onclick="goToPage(1)">1</a></li>`;
    if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
  }

  for (let p = startPage; p <= endPage; p++) {
    html += `<li class="page-item ${p === currentPage ? 'active' : ''}">
      <a class="page-link shadow-none" href="javascript:void(0)" onclick="goToPage(${p})">${p}</a>
    </li>`;
  }

  if (endPage < totalPages) {
    if (endPage < totalPages - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    html += `<li class="page-item"><a class="page-link shadow-none" href="javascript:void(0)" onclick="goToPage(${totalPages})">${totalPages}</a></li>`;
  }

  // Next Button
  html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
    <a class="page-link shadow-none" href="javascript:void(0)" onclick="goToPage(${currentPage + 1})" aria-label="Next">
      <span aria-hidden="true">&raquo;</span>
    </a>
  </li>`;

  container.innerHTML = html;
}

function goToPage(page) {
  currentPage = page;
  renderTable();
  const tableCard = document.getElementById('itemMasterTable');
  if (tableCard) tableCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>
