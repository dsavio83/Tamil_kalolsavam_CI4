<?php
$total_items = count($retvalue ?? []);
$common_items = 0;
$specific_items = 0;
$single_items = 0;
$group_items = 0;
$off_stage_items = 0;
if (!empty($retvalue)) {
    foreach ($retvalue as $r) {
        if (($r['is_common'] ?? 'Y') === 'Y') $common_items++;
        else $specific_items++;

        if (($r['item_type'] ?? '') === 'S') $single_items++;
        else if (($r['item_type'] ?? '') === 'G') $group_items++;
        if (($r['is_off_stage'] ?? '') === 'Y') $off_stage_items++;
    }
}
$is_editing = (isset($show_edit_item) && $show_edit_item == '1');
$sel_item = $selected_item[0] ?? [];
$uInfo = $current_user_info ?? [];
?>

<div class="pagetitle d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
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

  <div style="position: relative; z-index: 9999;">
    <button type="button" id="btnAddItem" class="btn btn-primary rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 shadow-sm fw-semibold" style="font-size: 14px; position: relative; z-index: 9999; cursor: pointer;" onclick="openAddModal()">
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
          <i class="bi bi-collection-play-fill fs-4"></i>
        </div>
        <div>
          <span class="text-muted text-uppercase fw-semibold" style="font-size: 11.5px; letter-spacing: 0.5px;">Visible Items</span>
          <h4 class="mb-0 fw-bold text-dark" style="font-size: 1.45rem; line-height: 1.2;"><?php echo $total_items; ?></h4>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-sm-6 col-12">
    <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
      <div class="card-body p-3.5 d-flex align-items-center gap-3">
        <div class="rounded-3 bg-success bg-gradient text-white d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 48px; height: 48px;">
          <i class="bi bi-globe fs-4"></i>
        </div>
        <div>
          <span class="text-muted text-uppercase fw-semibold" style="font-size: 11.5px; letter-spacing: 0.5px;">Common (All)</span>
          <h4 class="mb-0 fw-bold text-success" style="font-size: 1.45rem; line-height: 1.2;"><?php echo $common_items; ?></h4>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-sm-6 col-12">
    <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
      <div class="card-body p-3.5 d-flex align-items-center gap-3">
        <div class="rounded-3 text-white d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 48px; height: 48px; background-color: #8b5cf6;">
          <i class="bi bi-geo-alt-fill fs-4"></i>
        </div>
        <div>
          <span class="text-muted text-uppercase fw-semibold" style="font-size: 11.5px; letter-spacing: 0.5px;">Specific (Allotted)</span>
          <h4 class="mb-0 fw-bold" style="font-size: 1.45rem; line-height: 1.2; color: #7c3aed;"><?php echo $specific_items; ?></h4>
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
          <!-- Scope Filter -->
          <div class="d-flex align-items-center gap-1.5">
            <label for="scopeFilter" class="text-muted small text-nowrap mb-0 d-none d-sm-inline" style="font-size: 13px;">Scope:</label>
            <select id="scopeFilter" class="form-select form-select-sm rounded-3 border-secondary-subtle" style="font-size: 13px; min-width: 135px;" onchange="applyFilters()">
              <option value="">All Scopes</option>
              <option value="Common">Common Only</option>
              <option value="Specific">Specific Only</option>
            </select>
          </div>

          <!-- Festival Filter -->
          <div class="d-flex align-items-center gap-1.5">
            <label for="festFilter" class="text-muted small text-nowrap mb-0 d-none d-sm-inline" style="font-size: 13px;">Festival:</label>
            <select id="festFilter" class="form-select form-select-sm rounded-3 border-secondary-subtle" style="font-size: 13px; min-width: 150px;" onchange="applyFilters()">
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
            <select id="pageSizeSelect" class="form-select form-select-sm rounded-3 border-secondary-subtle" style="font-size: 13px; width: 70px;" onchange="changePageSize(this.value)">
              <option value="10">10</option>
              <option value="25" selected>25</option>
              <option value="50">50</option>
              <option value="all">All</option>
            </select>
          </div>

          <!-- Instant Search Box -->
          <div class="input-group input-group-sm" style="min-width: 190px; max-width: 250px;">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" id="itemTableSearch" class="form-control bg-light border-start-0" placeholder="Search code, name..." onkeyup="applyFilters()" style="font-size: 13px;">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" id="itemMasterTable" style="font-size: 13.5px;">
        <thead class="table-light text-uppercase text-secondary border-bottom" style="font-size: 11.5px; letter-spacing: 0.5px;">
          <tr>
            <th class="ps-3 py-3 text-center sortable" style="width: 45px; cursor: pointer;" onclick="sortTable(0, 'number')">
              <span>#</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="py-3 sortable" style="width: 85px; cursor: pointer;" onclick="sortTable(1, 'number')">
              <span>Code</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="py-3 sortable" style="cursor: pointer;" onclick="sortTable(2, 'string')">
              <span>Item Name</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="py-3 sortable" style="cursor: pointer;" onclick="sortTable(3, 'string')">
              <span>Festival / Vibhagam</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="py-3 text-center sortable" style="width: 140px; cursor: pointer;" onclick="sortTable(4, 'string')">
              <span>Scope</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="py-3 text-center sortable" style="width: 85px; cursor: pointer;" onclick="sortTable(5, 'string')">
              <span>Type</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="py-3 text-center sortable" style="width: 85px; cursor: pointer;" onclick="sortTable(6, 'string')">
              <span>Gender</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="py-3 text-center sortable" style="width: 90px; cursor: pointer;" onclick="sortTable(7, 'string')">
              <span>Stage</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="py-3 text-center sortable" style="width: 145px; cursor: pointer;" onclick="sortTable(8, 'string')">
              <span>Value Points</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="py-3 sortable" style="width: 150px; cursor: pointer;" onclick="sortTable(9, 'string')">
              <span>Created / Owner</span> <span class="sort-icon text-muted">⇅</span>
            </th>
            <th class="pe-3 py-3 text-end" style="width: 100px;">
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
                  $is_common = ($val['is_common'] ?? 'Y') === 'Y';
                  $is_group = (($val['item_type'] ?? '') === 'G');
                  $is_off = (($val['is_off_stage'] ?? '') === 'Y');
                  $gen = $val['gender'] ?? 'C';
                  $allottedCount = $val['allotted_count'] ?? 0;
                  $subNames = !empty($val['allotted_subdistricts']) ? implode(', ', array_column($val['allotted_subdistricts'], 'sub_district_name')) : '';

                  // Creator info
                  $creatorUsername = $val['creator_username'] ?? '';
                  $creatorName = $val['creator_name'] ?? '';
                  $creatorType = isset($val['creator_user_type']) ? (int)$val['creator_user_type'] : null;
                  $creatorLabel = $creatorName ?: ($creatorUsername ?: 'System');
                  if ($creatorType === 0) $creatorRole = 'IT Admin';
                  elseif ($creatorType === 1) $creatorRole = 'State Admin';
                  elseif ($creatorType === 2) $creatorRole = 'Dist Admin';
                  elseif ($creatorType === 3) $creatorRole = 'Sub-Dist Admin';
                  else $creatorRole = 'Admin';

                  $json_item = htmlspecialchars(json_encode($val), ENT_QUOTES, 'UTF-8');
          ?>
          <tr data-fest="<?php echo htmlspecialchars($val['fest_name'] ?? ''); ?>" data-scope="<?php echo $is_common ? 'Common' : 'Specific'; ?>" data-item-id="<?php echo $val['item_id']; ?>">
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
                <small class="text-muted d-block" style="font-size: 11.5px;">
                  Max Participants: <?php echo (int)$val['max_participants']; ?><?php echo !empty($val['max_pinnani']) ? ' + ' . (int)$val['max_pinnani'] . ' Pinnani' : ''; ?>
                </small>
              <?php endif; ?>
            </td>
            <td class="py-2.5" style="font-size: 13px;">
              <div class="text-dark fw-semibold"><?php echo htmlspecialchars($val['fest_name'] ?? '—'); ?></div>
              <small class="text-muted"><?php echo htmlspecialchars($val['vibhagam_name'] ?? '—'); ?></small>
            </td>
            <td class="py-2.5 text-center">
              <?php if ($is_common): ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill" style="font-size: 11.5px;">
                  <i class="bi bi-globe me-1"></i>Common (All)
                </span>
              <?php else: ?>
                <span class="badge border rounded-pill px-2.5 py-1" style="background-color: #f5f3ff; color: #7c3aed; border-color: #ddd6fe; font-size: 11.5px;" title="<?php echo htmlspecialchars($subNames ?: 'No sub-districts assigned'); ?>">
                  <i class="bi bi-geo-alt-fill me-1"></i>Specific (<?php echo $allottedCount; ?> Sub-Dist)
                </span>
              <?php endif; ?>
            </td>
            <td class="py-2.5 text-center">
              <?php if ($is_group): ?>
                <span class="badge bg-light text-dark border rounded-pill px-2 py-0.5" style="font-size: 11.5px;">Group</span>
              <?php else: ?>
                <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5" style="font-size: 11.5px;">Single</span>
              <?php endif; ?>
            </td>
            <td class="py-2.5 text-center">
              <?php 
                if ($gen === 'B') {
                  echo '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-0.5" style="font-size: 11.5px;"><i class="bi bi-gender-male me-1"></i>Boy</span>';
                } else if ($gen === 'G') {
                  echo '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5" style="font-size: 11.5px;"><i class="bi bi-gender-female me-1"></i>Girl</span>';
                } else {
                  echo '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5" style="font-size: 11.5px;">Common</span>';
                }
              ?>
            </td>
            <td class="py-2.5 text-center">
              <?php if ($is_off): ?>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-0.5" style="font-size: 11.5px;">Off-Stage</span>
              <?php else: ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 11.5px;">On-Stage</span>
              <?php endif; ?>
            </td>
            <td class="py-2.5 text-center">
              <?php 
              $hasVp = !empty($val['value_points']) && trim($val['value_points']) !== '';
              $vp_lines = $hasVp ? array_filter(array_map('trim', explode("\n", $val['value_points']))) : [];
              $criteria_count = 0;
              foreach ($vp_lines as $l) {
                  if (preg_match('/^\d+[\)\.]/', $l)) $criteria_count++;
              }
              ?>
              <?php if ($hasVp): ?>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1.5 shadow-2xs" style="font-size: 11.5px; font-weight: 500;" onclick='showValuePointsPreview(<?php echo $json_item; ?>)' title="மதிப்பீட்டுக் குறிப்புகளைக் காண்க">
                  <i class="bi bi-card-checklist text-primary"></i>
                  <span><?php echo $criteria_count > 0 ? "{$criteria_count} குறிப்புகள்" : "குறிப்புகள்"; ?></span>
                </button>
              <?php else: ?>
                <span class="badge bg-light text-muted border rounded-pill px-2 py-1" style="font-size: 11px;">இல்லை</span>
              <?php endif; ?>
            </td>
            <td class="py-2.5" style="font-size: 12.5px;">
              <div class="fw-semibold text-dark text-truncate" style="max-width: 140px;" title="<?php echo htmlspecialchars($creatorLabel); ?>">
                <i class="bi bi-person-fill text-muted me-1"></i><?php echo htmlspecialchars($creatorLabel); ?>
              </div>
              <small class="badge bg-light text-secondary border px-1.5 py-0.5" style="font-size: 10.5px;"><?php echo htmlspecialchars($creatorRole); ?></small>
            </td>
            <td class="pe-3 py-2.5 text-end">
              <div class="d-inline-flex align-items-center gap-1">
                <button type="button" class="btn btn-sm btn-outline-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center shadow-2xs" style="width: 32px; height: 32px;" title="Edit Item" onclick='openEditFromData(<?php echo $json_item; ?>)'>
                  <i class="bi bi-pencil-square fs-6"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-1 d-inline-flex align-items-center justify-content-center shadow-2xs" style="width: 32px; height: 32px;" title="Delete Item" onclick="confirmDeleteItem('<?php echo $val['item_id']; ?>', '<?php echo htmlspecialchars($val['item_code']); ?>', '<?php echo htmlspecialchars(addslashes($val['item_name'])); ?>')">
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
      <p class="small mb-0" style="font-size: 13px;">Try adjusting your search keyword, scope, or festival filter.</p>
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
  <div class="modal-dialog modal-lg modal-dialog-scrollable" style="margin-top: 50px !important; margin-bottom: 30px !important;">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <!-- Modal Header -->
      <div class="modal-header bg-primary bg-gradient text-white py-3 px-4 border-0">
        <div class="d-flex align-items-center gap-2.5">
          <div class="rounded-circle bg-white text-primary p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
            <i class="bi bi-award-fill fs-5" id="modalHeaderIcon"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold mb-0 text-white" id="itemModalLabel" style="font-size: 1.15rem;">Add New Competition Item</h5>
            <small class="text-white-50" style="font-size: 12.5px;">Configure item parameters, regional scope, and participation limits</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Form -->
      <?php echo form_open_multipart('item/item_master/add_item_details', array('id' => 'itemModalForm', 'name' => 'itemModalForm')); ?>
      <input type="hidden" name="ItemIdty" id="modalItemIdty" value="" />

      <div class="modal-body p-4 bg-light" style="max-height: 75vh; overflow-y: auto;">
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
                <?php echo form_dropdown('fest_id', $festivals, '', 'id="modal_fest_id" class="form-select border-secondary-subtle" required style="font-size: 14px;" onchange="onFestivalCategoryChange(this.value)"'); ?>
              </div>

              <div class="col-md-6 col-12">
                <label for="modal_vibhagam_id" class="form-label fw-semibold text-secondary small mb-1" style="font-size: 13px;">
                  Vibhagam <span class="text-danger">*</span>
                </label>
                <select name="vibhagam_id" id="modal_vibhagam_id" class="form-select border-secondary-subtle" required style="font-size: 14px;" onchange="onVibhagamChange(this.value)">
                  <option value="">-- Select Festival Category First --</option>
                  <?php if (!empty($all_vibhagams)): ?>
                    <?php foreach ($all_vibhagams as $v): ?>
                      <option value="<?php echo $v['vibhagam_id']; ?>" data-fest-id="<?php echo $v['fest_id']; ?>"><?php echo htmlspecialchars($v['vibhagam_name']); ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 2: Item Regional Scope & Sub-District Allotment (NEW) -->
        <div class="card border-0 shadow-sm rounded-3 mb-3 bg-white">
          <div class="card-header bg-white py-2.5 px-3 border-bottom border-light d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-geo-alt-fill text-purple" style="color: #7c3aed;"></i>
              <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px; font-size: 12.5px;">2. Regional Scope & Allotment</span>
            </div>
            <span class="badge bg-light text-muted border px-2 py-0.5" style="font-size: 11px;">Jurisdiction Control</span>
          </div>
          <div class="card-body p-3">
            <div class="mb-3">
              <label class="form-label fw-semibold text-secondary small mb-2" style="font-size: 13px;">
                Who Can Participate / View This Item? <span class="text-danger">*</span>
              </label>
              <div class="d-flex flex-column flex-sm-row gap-3">
                <div class="form-check p-2.5 rounded-3 border flex-fill" style="background-color: #f8fafc;">
                  <input class="form-check-input ms-1 me-2" type="radio" name="is_common" id="scope_common" value="Y" checked onchange="toggleScopeSection()">
                  <label class="form-check-label fw-semibold text-dark cursor-pointer" for="scope_common" style="font-size: 13.5px;">
                    <i class="bi bi-globe text-success me-1"></i> Common (All Districts & Sub-Districts)
                    <small class="text-muted d-block fw-normal mt-0.5" style="font-size: 12px;">Standard item open to every school and sub-district across the state.</small>
                  </label>
                </div>
                <div class="form-check p-2.5 rounded-3 border flex-fill" style="background-color: #f8fafc;">
                  <input class="form-check-input ms-1 me-2" type="radio" name="is_common" id="scope_specific" value="N" onchange="toggleScopeSection()">
                  <label class="form-check-label fw-semibold text-dark cursor-pointer" for="scope_specific" style="font-size: 13.5px;">
                    <i class="bi bi-geo-alt-fill text-purple me-1" style="color: #7c3aed;"></i> Specific (Selected Sub-Districts Only)
                    <small class="text-muted d-block fw-normal mt-0.5" style="font-size: 12px;">Restricted item available only to designated sub-districts and districts.</small>
                  </label>
                </div>
              </div>
            </div>

            <!-- Specific Sub-District Checklist Container -->
            <div id="subdistrictsSelectionBox" style="display: none;" class="p-3 rounded-3 border border-secondary-subtle bg-light">
              <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2.5">
                <span class="fw-bold text-dark small" style="font-size: 13px;">
                  <i class="bi bi-check2-square text-primary me-1"></i> Select Allotted Sub-Districts:
                </span>
                <div class="d-flex align-items-center gap-1.5">
                  <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2 rounded-2" style="font-size: 11.5px;" onclick="selectAllSubdistricts(true)">
                    Select All
                  </button>
                  <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 rounded-2" style="font-size: 11.5px;" onclick="selectAllSubdistricts(false)">
                    Deselect All
                  </button>
                </div>
              </div>

              <!-- Filter Search for Sub-Districts -->
              <div class="input-group input-group-sm mb-2.5">
                <span class="input-group-text bg-white border-secondary-subtle"><i class="bi bi-search"></i></span>
                <input type="text" id="subdistrictSearchInput" class="form-control bg-white border-secondary-subtle" placeholder="Filter districts or sub-districts..." onkeyup="filterSubdistrictCheckboxes()" style="font-size: 13px;">
              </div>

              <!-- Accordion / Grouped list of Sub-Districts by District -->
              <div class="accordion accordion-flush rounded-3 border bg-white overflow-hidden" id="accordionDistricts" style="max-height: 240px; overflow-y: auto;">
                <?php if (!empty($districts_with_subdistricts)): ?>
                  <?php foreach ($districts_with_subdistricts as $dIdx => $dItem): ?>
                    <div class="accordion-item district-group-item" data-district-name="<?php echo htmlspecialchars(strtolower($dItem['rev_district_name'])); ?>">
                      <h2 class="accordion-header" id="heading_<?php echo $dItem['rev_district_code']; ?>">
                        <button class="accordion-button collapsed py-2 px-3 bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_<?php echo $dItem['rev_district_code']; ?>" aria-expanded="false" style="font-size: 13px;">
                          <div class="d-flex align-items-center justify-content-between w-100 pe-2">
                            <span class="fw-semibold text-dark">
                              <i class="bi bi-building text-secondary me-1"></i><?php echo htmlspecialchars($dItem['rev_district_name']); ?>
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 11px;">
                              <?php echo count($dItem['sub_districts'] ?? []); ?> Sub-Districts
                            </span>
                          </div>
                        </button>
                      </h2>
                      <div id="collapse_<?php echo $dItem['rev_district_code']; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionDistricts">
                        <div class="accordion-body p-2.5">
                          <div class="d-flex align-items-center justify-content-between pb-1.5 mb-2 border-bottom">
                            <span class="small text-muted" style="font-size: 11.5px;">Toggle entire district:</span>
                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size: 11.5px;" onclick="toggleDistrictSubdistricts(<?php echo $dItem['rev_district_code']; ?>, true)">Select District</button>
                          </div>
                          <div class="row g-2">
                            <?php foreach ($dItem['sub_districts'] as $sub): ?>
                              <div class="col-md-6 col-12 subdistrict-checkbox-col" data-sub-name="<?php echo htmlspecialchars(strtolower($sub['sub_district_name'])); ?>">
                                <div class="form-check">
                                  <input class="form-check-input subdistrict-chk sub-dist-group-<?php echo $dItem['rev_district_code']; ?>" type="checkbox" name="sub_districts[]" value="<?php echo $sub['sub_district_code']; ?>" id="chk_sd_<?php echo $sub['sub_district_code']; ?>">
                                  <label class="form-check-label text-dark small" for="chk_sd_<?php echo $sub['sub_district_code']; ?>" style="font-size: 12.5px;">
                                    <?php echo htmlspecialchars($sub['sub_district_name']); ?>
                                  </label>
                                </div>
                              </div>
                            <?php endforeach; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 3: Competition Rules -->
        <div class="card border-0 shadow-sm rounded-3 mb-3 bg-white">
          <div class="card-header bg-white py-2.5 px-3 border-bottom border-light d-flex align-items-center gap-2">
            <i class="bi bi-sliders text-success"></i>
            <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px; font-size: 12.5px;">3. Participation Rules</span>
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
                  echo form_dropdown('item_type', $item_types, 'S', 'id="modal_item_type" class="form-select border-secondary-subtle" style="font-size: 14px;" onchange="onItemTypeChange(this.value)"'); 
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

        <!-- Section 4: Time & Participant Limits -->
        <div class="card border-0 shadow-sm rounded-3 bg-white">
          <div class="card-header bg-white py-2.5 px-3 border-bottom border-light d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-warning"></i>
            <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px; font-size: 12.5px;">4. Time Duration & Limits</span>
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

        <!-- Section 5: Evaluation Criteria / Value Points -->
        <div class="card border-0 shadow-sm rounded-3 mt-3 bg-white">
          <div class="card-header bg-white py-2.5 px-3 border-bottom border-light d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-card-checklist text-info"></i>
              <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px; font-size: 12.5px;">5. Value Points / Evaluation Criteria</span>
            </div>
            <span class="badge bg-light text-muted border px-2 py-0.5" style="font-size: 11px;">Tabulation Sheet Scheme</span>
          </div>
          <div class="card-body p-3">
            <div class="mb-3">
              <label for="modal_value_points" class="form-label fw-semibold text-secondary small mb-1" style="font-size: 13px;">
                Value Points Breakdown
              </label>
              <textarea class="form-control border-secondary-subtle font-tamil" id="modal_value_points" name="value_points" rows="5" placeholder="எ.கா:&#10;1) கருத்தை மையமாகக் கொண்டிருத்தல் - 25 மதிப்பெண்கள்&#10;2) மொழிநடை - 25 மதிப்பெண்கள்&#10;3) வர்ணனையோடு கவிதை எழுதும் திறன் - 25 மதிப்பெண்கள்&#10;4) இசைவடிவத்தோடு ஏற்ற தாளநடை - 25 மதிப்பெண்கள்" style="font-family: 'TAU-Pallai', sans-serif; font-size: 14px; line-height: 1.6;"></textarea>
              <small class="text-muted d-block mt-1" style="font-size: 11.5px;">
                <i class="bi bi-info-circle me-1"></i>இக்குறிப்புகள் மதிப்பெண் தாளில் (Tabulation Sheet) அச்சிடப்பட்டு நடுவர்களின் வழிகாட்டுதலுக்குப் பயன்படும்.
              </small>
            </div>
            <div>
              <label for="modal_value_points_image" class="form-label fw-semibold text-secondary small mb-1" style="font-size: 13px;">
                மதிப்பீட்டுக் குறிப்புகள் படம் (Criteria Image - விருப்பத்தேர்வு)
              </label>
              <input type="file" class="form-control border-secondary-subtle" id="modal_value_points_image" name="value_points_image" accept=".png,.jpg,.jpeg" style="font-size: 13px;" />
              <small class="text-muted d-block mt-1" style="font-size: 11.5px;">
                <i class="bi bi-image me-1"></i>விருப்பப்பட்டால் value_points கோப்பிற்குரிய .png/.jpg படத்தை பதிவேற்றலாம்.
              </small>
            </div>
          </div>
        </div>

          <!-- Action Buttons: placed at the end of the scrollable body so Save/Cancel are always reachable -->
          <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 mt-3 pt-3 border-top border-light">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal" style="font-size: 14px;">Cancel</button>
            <button type="submit" class="btn btn-primary rounded-pill px-4 d-inline-flex align-items-center gap-2 shadow-sm fw-semibold" id="modalSubmitBtn" style="font-size: 14px;">
              <i class="bi bi-check2-circle fs-5"></i>
              <span id="modalSubmitText">Save Item</span>
            </button>
          </div>
        </div>

      <?php echo form_close(); ?>
    </div>
  </div>
</div>

<!-- ======================================================= -->
<!-- VALUE POINTS PREVIEW MODAL                              -->
<!-- ======================================================= -->
<div class="modal fade" id="valuePointsPreviewModal" tabindex="-1" aria-labelledby="valuePointsPreviewModalLabel" aria-hidden="true" style="z-index: 10055;">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content shadow-lg border-0 rounded-4">
      <div class="modal-header border-bottom bg-light px-4 py-3">
        <div class="d-flex align-items-center gap-2">
          <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-card-checklist fs-5"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold text-dark mb-0" id="valuePointsPreviewModalLabel">மதிப்பீட்டுக் குறிப்புகள்</h5>
            <small class="text-muted" id="vpModalSubTitle"></small>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <!-- Badges & Meta -->
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3" id="vpMetaBadges"></div>

        <!-- Rendered Table Container -->
        <div class="card border rounded-3 mb-3 overflow-hidden shadow-2xs">
          <div class="card-header bg-light py-2 px-3 fw-semibold small text-secondary d-flex justify-content-between align-items-center">
            <span><i class="bi bi-table me-1.5 text-primary"></i> &nbsp; மதிப்பீட்டுக் குறிப்புகள் அட்டவணை</span>
            </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-bordered mb-0 align-middle" id="vpDynamicTable" style="font-family: 'TAU-Pallai', sans-serif; font-size: 14px;">
                <!-- Dynamically populated -->
              </table>
            </div>
          </div>
        </div>

        <!-- Rendered PNG Image Preview -->
        <div class="card border rounded-3 mb-1 shadow-2xs" id="vpImageCard">
          <div class="card-header bg-light py-2 px-3 fw-semibold small text-secondary d-flex justify-content-between align-items-center">
            <span><i class="bi bi-image me-1.5 text-success"></i>&nbsp; அட்டவணைப் படம் (Table Image)</span>
            <a id="vpImageDownloadBtn" href="#" target="_blank" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5" style="font-size: 11.5px;">
              <i class="bi bi-box-arrow-up-right me-1"></i>படத்தைத் திற / அச்சிடு
            </a>
          </div>
          <div class="card-body p-3 text-center bg-light-subtle">
            <img id="vpImagePreview" src="" alt="Value Points Table" class="img-fluid border rounded bg-white shadow-sm" style="max-height: 280px;" />
          </div>
        </div>
      </div>
      <div class="modal-footer border-top bg-light px-4 py-2.5 d-flex justify-content-between">
        <button type="button" class="btn btn-outline-primary rounded-pill px-3.5 d-inline-flex align-items-center gap-1.5" id="vpEditShortcutBtn" style="font-size: 13.5px;">
          <i class="bi bi-pencil-square"></i>
          <span>போட்டி விவரங்களைத் திருத்து</span>
        </button>
        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal" style="font-size: 13.5px;">மூடுக</button>
      </div>
    </div>
  </div>
</div>

<!-- ======================================================= -->
<!-- LIGHTWEIGHT HIGH-PERFORMANCE DATATABLE ENGINE           -->
<!-- ======================================================= -->
<script>
const allVibhagams = <?php echo json_encode($all_vibhagams ?? [], JSON_UNESCAPED_UNICODE); ?>;

function updateVibhagamDropdown(festId, selectedVibhagamId = null) {
  const vibhagamSelect = document.getElementById('modal_vibhagam_id');
  if (!vibhagamSelect) return;

  const currentVal = (selectedVibhagamId !== null && selectedVibhagamId !== undefined && selectedVibhagamId !== '')
    ? String(selectedVibhagamId)
    : String(vibhagamSelect.value || '');

  vibhagamSelect.innerHTML = '';

  const placeholder = document.createElement('option');
  placeholder.value = '';
  placeholder.textContent = festId ? '-- Select Vibhagam --' : '-- Select Festival Category First --';
  vibhagamSelect.appendChild(placeholder);

  if (!festId) {
    vibhagamSelect.disabled = true;
    return;
  }
  vibhagamSelect.disabled = false;

  const filtered = (allVibhagams || []).filter(v => String(v.fest_id) === String(festId));
  let matched = false;

  filtered.forEach(v => {
    const opt = document.createElement('option');
    opt.value = v.vibhagam_id;
    opt.textContent = v.vibhagam_name;
    opt.setAttribute('data-fest-id', v.fest_id);
    if (currentVal && String(v.vibhagam_id) === currentVal) {
      opt.selected = true;
      matched = true;
    }
    vibhagamSelect.appendChild(opt);
  });

  // If previous selection didn't match the new festival, smart select by item_type or prompt
  if (!matched && filtered.length > 0) {
    const itemType = document.getElementById('modal_item_type')?.value;
    if (itemType === 'G') {
      const grp = filtered.find(v => v.vibhagam_name.toLowerCase().includes('group'));
      if (grp) vibhagamSelect.value = grp.vibhagam_id;
    } else if (itemType === 'S') {
      const sgl = filtered.find(v => v.vibhagam_name.toLowerCase().includes('single'));
      if (sgl) vibhagamSelect.value = sgl.vibhagam_id;
    }
  }
}

function onFestivalCategoryChange(festId) {
  updateVibhagamDropdown(festId);
}

function onVibhagamChange(vibhagamId) {
  if (!vibhagamId) return;
  const selected = (allVibhagams || []).find(v => String(v.vibhagam_id) === String(vibhagamId));
  if (!selected) return;

  const itemTypeSelect = document.getElementById('modal_item_type');
  if (itemTypeSelect) {
    const isGroup = selected.vibhagam_name.toLowerCase().includes('group');
    itemTypeSelect.value = isGroup ? 'G' : 'S';

    if (window.__itemModalMode === 'add') {
      const partInput = document.getElementById('modal_max_participants');
      if (partInput) {
        if (isGroup && (parseInt(partInput.value, 10) <= 1 || !partInput.value)) {
          partInput.value = '7';
        } else if (!isGroup && (partInput.value === '7' || !partInput.value)) {
          partInput.value = '1';
        }
      }
    }
  }
}

function onItemTypeChange(itemType) {
  const festId = document.getElementById('modal_fest_id')?.value;
  if (!festId) return;
  const filtered = (allVibhagams || []).filter(v => String(v.fest_id) === String(festId));
  const vibhagamSelect = document.getElementById('modal_vibhagam_id');
  if (!vibhagamSelect) return;

  if (itemType === 'G') {
    const grp = filtered.find(v => v.vibhagam_name.toLowerCase().includes('group'));
    if (grp) vibhagamSelect.value = grp.vibhagam_id;
  } else if (itemType === 'S') {
    const sgl = filtered.find(v => v.vibhagam_name.toLowerCase().includes('single'));
    if (sgl) vibhagamSelect.value = sgl.vibhagam_id;
  }
}

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

  // Robust delegated handler for the Add button: guarantees the modal opens even
  // if the inline onclick is shadowed by another loaded script.
  if (window.jQuery) {
    jQuery(document).on('click', '#btnAddItem', function (e) {
      e.preventDefault();
      openAddModal();
    });
  } else {
    var addBtn = document.getElementById('btnAddItem');
    if (addBtn) {
      addBtn.addEventListener('click', function (e) {
        e.preventDefault();
        openAddModal();
      });
    }
  }

  const modalEl = document.getElementById('itemModal');
  if (modalEl) {
    modalEl.addEventListener('show.bs.modal', function (evt) {
      // If triggered by the Add button (not an edit), reset to add mode.
      if (window.__itemModalMode !== 'edit') {
        resetAddFormFields();
      }
      setTimeout(() => {
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
          backdrop.style.zIndex = '10040';
        }
      }, 20);
    });
  }

  const itemForm = document.getElementById('itemModalForm');
  if (itemForm) {
    itemForm.addEventListener('submit', function(e) {
      const festId = document.getElementById('modal_fest_id')?.value;
      const vibhagamId = document.getElementById('modal_vibhagam_id')?.value;

      if (!festId) {
        e.preventDefault();
        alert('Please select a Festival Category.');
        document.getElementById('modal_fest_id').focus();
        return false;
      }

      if (!vibhagamId) {
        e.preventDefault();
        alert('Please select a valid Vibhagam.');
        document.getElementById('modal_vibhagam_id').focus();
        return false;
      }

      const isValid = (allVibhagams || []).some(v => String(v.fest_id) === String(festId) && String(v.vibhagam_id) === String(vibhagamId));
      if (!isValid) {
        e.preventDefault();
        alert('Selected Vibhagam does not belong to the selected Festival Category. Please choose a matching Vibhagam.');
        document.getElementById('modal_vibhagam_id').focus();
        return false;
      }
    });
  }

  <?php if ($is_editing && !empty($sel_item)): ?>
    openEditFromData(<?php echo json_encode($sel_item); ?>);
  <?php endif; ?>
});

function toggleScopeSection() {
  const isSpecific = document.getElementById('scope_specific').checked;
  const box = document.getElementById('subdistrictsSelectionBox');
  if (box) {
    box.style.display = isSpecific ? 'block' : 'none';
  }
}

function selectAllSubdistricts(select) {
  document.querySelectorAll('.subdistrict-chk').forEach(chk => {
    chk.checked = select;
  });
}

function toggleDistrictSubdistricts(distCode, select) {
  document.querySelectorAll('.sub-dist-group-' + distCode).forEach(chk => {
    chk.checked = select !== undefined ? select : !chk.checked;
  });
}

function filterSubdistrictCheckboxes() {
  const term = (document.getElementById('subdistrictSearchInput').value || '').toLowerCase().trim();
  document.querySelectorAll('.district-group-item').forEach(group => {
    const distName = group.getAttribute('data-district-name') || '';
    let hasMatch = distName.includes(term);

    group.querySelectorAll('.subdistrict-checkbox-col').forEach(subCol => {
      const subName = subCol.getAttribute('data-sub-name') || '';
      if (!term || subName.includes(term) || distName.includes(term)) {
        subCol.style.display = '';
        hasMatch = true;
      } else {
        subCol.style.display = 'none';
      }
    });

    group.style.display = (!term || hasMatch) ? '' : 'none';
    if (term && hasMatch) {
      const collapseEl = group.querySelector('.accordion-collapse');
      if (collapseEl && !collapseEl.classList.contains('show')) {
        const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapseEl);
        bsCollapse.show();
      }
    }
  });
}

function resetAddFormFields() {
  try {
    const form = document.getElementById('itemModalForm');
    if (form) {
      form.reset();
      if (typeof getCsrfToken === 'function') {
        const tok = getCsrfToken();
        if (tok) {
          const csrfEl = form.querySelector('input[name="csrf_test_name"]');
          if (csrfEl) csrfEl.value = tok;
        }
      }
      form.action = '<?php echo base_url("item/item_master/add_item_details"); ?>';
    }

    document.getElementById('modalItemIdty').value = '';
    document.getElementById('modal_item_code').value = '';
    document.getElementById('modal_item_name').value = '';
    document.getElementById('modal_fest_id').value = '';
    updateVibhagamDropdown('');
    document.getElementById('modal_gender').value = 'C';
    document.getElementById('modal_item_type').value = 'S';
    document.getElementById('modal_max_time').value = '';
    document.getElementById('modal_time_type').value = 'M';
    document.getElementById('modal_max_participants').value = '1';
    document.getElementById('modal_max_pinnani').value = '0';
    document.getElementById('modal_is_off_stage').value = 'N';
    if (document.getElementById('modal_value_points')) {
      document.getElementById('modal_value_points').value = '';
    }
    if (document.getElementById('modal_value_points_image')) {
      document.getElementById('modal_value_points_image').value = '';
    }
    if (document.getElementById('subdistrictSearchInput')) {
      document.getElementById('subdistrictSearchInput').value = '';
    }

    document.getElementById('scope_common').checked = true;
    selectAllSubdistricts(false);
    toggleScopeSection();

    document.getElementById('itemModalLabel').innerText = 'Add New Competition Item';
    document.getElementById('modalSubmitText').innerText = 'Save Item';
    document.getElementById('modalHeaderIcon').className = 'bi bi-plus-circle-fill fs-5';
  } catch (err) {
    console.error('resetAddFormFields error:', err);
  }
}

function openAddModal() {
  // Open the modal FIRST, then reset fields. Reset cannot block opening.
  window.__itemModalMode = 'add';
  const modalEl = document.getElementById('itemModal');
  if (modalEl) {
    try {
      const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.show();
    } catch (e) {
      modalEl.style.display = 'block';
      modalEl.classList.add('show');
      document.body.classList.add('modal-open');
    }
  }
  resetAddFormFields();
}

function openEditFromData(item) {
  if (!item) return;
  window.__itemModalMode = 'edit';
  const form = document.getElementById('itemModalForm');
  if (form) {
    form.action = '<?php echo base_url("item/item_master/update_item_detials"); ?>';
  }
  document.getElementById('modalItemIdty').value = item.item_id || '';
  document.getElementById('modal_item_code').value = item.item_code || '';
  document.getElementById('modal_item_name').value = item.item_name || '';
  document.getElementById('modal_fest_id').value = item.fest_id || '';
  updateVibhagamDropdown(item.fest_id, item.vibhagam_id);
  document.getElementById('modal_gender').value = item.gender || 'C';
  document.getElementById('modal_item_type').value = item.item_type || 'S';
  document.getElementById('modal_max_time').value = item.max_time || '';
  document.getElementById('modal_time_type').value = item.time_type || 'M';
  document.getElementById('modal_max_participants').value = item.max_participants || '';
  document.getElementById('modal_max_pinnani').value = item.max_pinnani || '';
  document.getElementById('modal_is_off_stage').value = item.is_off_stage || 'N';
  if (document.getElementById('modal_value_points')) {
    document.getElementById('modal_value_points').value = item.value_points || '';
  }
  if (document.getElementById('modal_value_points_image')) {
    document.getElementById('modal_value_points_image').value = '';
  }

  // Scope handling
  const isCommon = (item.is_common === 'Y' || !item.is_common);
  if (isCommon) {
    document.getElementById('scope_common').checked = true;
    selectAllSubdistricts(false);
    toggleScopeSection();
  } else {
    document.getElementById('scope_specific').checked = true;
    selectAllSubdistricts(false);
    toggleScopeSection();

    // Pre-populate checkboxes from server-side data first (reliable, always present).
    // The AJAX call below is a safety net and never overrides existing selections,
    // so a slow/failed AJAX can no longer wipe the allotments on save.
    const serverAllotted = (item.allotted_subdistricts && Array.isArray(item.allotted_subdistricts))
      ? item.allotted_subdistricts.map(sd => String(sd.sub_district_code))
      : [];
    serverAllotted.forEach(code => {
      const chk = document.getElementById('chk_sd_' + code);
      if (chk) {
        chk.checked = true;
        const parentCollapse = chk.closest('.accordion-collapse');
        if (parentCollapse && !parentCollapse.classList.contains('show')) {
          const bsCollapse = bootstrap.Collapse.getOrCreateInstance(parentCollapse);
          bsCollapse.show();
        }
      }
    });

    // AJAX fallback (also adds any allotted codes not present in server data)
    fetch('<?php echo base_url("item/item_master/get_item_allotment_details"); ?>?item_code=' + encodeURIComponent(item.item_code))
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success' && Array.isArray(data.allotted_sub_districts)) {
          data.allotted_sub_districts.forEach(code => {
            const chk = document.getElementById('chk_sd_' + code);
            if (chk) {
              chk.checked = true;
            }
          });
        }
      })
      .catch(err => console.error('Error fetching allotment:', err));
  }

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
    
    // Inject CSRF token if available
    if (typeof getCsrfToken === 'function') {
      const tok = getCsrfToken();
      if (tok) {
        const c = document.createElement('input');
        c.type = 'hidden';
        c.name = 'csrf_test_name';
        c.value = tok;
        f.appendChild(c);
      }
    }
    
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
  const scopeFilter = (document.getElementById('scopeFilter').value || '').trim();

  filteredRows = allRows.filter(row => {
    const text = row.textContent.toLowerCase();
    const festName = row.getAttribute('data-fest') || '';
    const scopeVal = row.getAttribute('data-scope') || '';

    const matchesSearch = !searchTerm || text.includes(searchTerm);
    const matchesFest = !festFilter || festName === festFilter;
    const matchesScope = !scopeFilter || scopeVal === scopeFilter;

    return matchesSearch && matchesFest && matchesScope;
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
    if (type === 'number') {
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
    row.cells[0].innerText = i + 1; // dynamically update Sl.No
    tbody.appendChild(row);
  }

  document.getElementById('paginationInfo').innerText = `Showing ${start + 1} to ${end} of ${total} items`;

  // Build Pagination Buttons
  const pag = document.getElementById('paginationControls');
  pag.innerHTML = '';

  const prevLi = document.createElement('li');
  prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
  prevLi.innerHTML = `<a class="page-link" href="javascript:void(0)" onclick="goToPage(${currentPage - 1})"><i class="bi bi-chevron-left"></i></a>`;
  pag.appendChild(prevLi);

  let startPage = Math.max(1, currentPage - 2);
  let endPage = Math.min(totalPages, startPage + 4);
  if (endPage - startPage < 4) {
    startPage = Math.max(1, endPage - 4);
  }

  for (let p = startPage; p <= endPage; p++) {
    const li = document.createElement('li');
    li.className = `page-item ${p === currentPage ? 'active' : ''}`;
    li.innerHTML = `<a class="page-link" href="javascript:void(0)" onclick="goToPage(${p})">${p}</a>`;
    pag.appendChild(li);
  }

  const nextLi = document.createElement('li');
  nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
  nextLi.innerHTML = `<a class="page-link" href="javascript:void(0)" onclick="goToPage(${currentPage + 1})"><i class="bi bi-chevron-right"></i></a>`;
  pag.appendChild(nextLi);
}

function goToPage(page) {
  currentPage = page;
  renderTable();
}

function showValuePointsPreview(item) {
  if (!item) return;
  document.getElementById('valuePointsPreviewModalLabel').innerText = 'மதிப்பீட்டுக் குறிப்புகள்: #' + item.item_code + ' - ' + item.item_name;
  document.getElementById('vpModalSubTitle').innerText = (item.fest_name || '') + (item.vibhagam_name ? ' • ' + item.vibhagam_name : '');

  // Meta badges
  const metaContainer = document.getElementById('vpMetaBadges');
  if (metaContainer) {
    metaContainer.innerHTML = `
      <span class="badge bg-light text-dark border px-2.5 py-1">குறியீடு: <strong>${item.item_code}</strong></span>
      <span class="badge bg-light text-dark border px-2.5 py-1">வகை: <strong>${item.item_type === 'G' ? 'குழுப் போட்டி (Group)' : 'தனியாள் போட்டி (Single)'}</strong></span>
      <span class="badge bg-light text-dark border px-2.5 py-1">நேரம்: <strong>${item.max_time || 5} ${item.time_type === 'H' ? 'மணி' : 'நிமிடம்'}</strong></span>
      <span class="badge bg-light text-dark border px-2.5 py-1">பங்கேற்பாளர்: <strong>${item.max_participants || 1}</strong></span>
    `;
  }

  // Parse value points text into table
  const table = document.getElementById('vpDynamicTable');
  if (table) {
    table.innerHTML = '';
    const vpText = item.value_points || '';
    const lines = vpText.split(/\r?\n/).map(l => l.trim()).filter(l => l !== '');
    const titleLines = [];
    const rows = [];

    lines.forEach(line => {
      const m = line.match(/^(\d+)\s*[\)\.]\s*(?:[\-\–]\s*)?(.*)$/);
      if (m) {
        const num = m[1];
        const rest = m[2].trim();
        const rm = rest.match(/^(.*?)\s*[\-\–]\s*([^\-\–]+)$/);
        if (rm) {
          rows.push({ no: num, desc: rm[1].trim(), marks: rm[2].trim() });
        } else {
          rows.push({ no: num, desc: rest, marks: '' });
        }
      } else {
        titleLines.push(line);
      }
    });

    const titleHtml = titleLines.length > 0 
      ? `<tr class="table-primary text-center fw-bold" style="background-color: #e0e7ff;"><td colspan="3" class="py-2.5 text-primary-emphasis" style="font-size: 15px;">${titleLines.join('<br>')}</td></tr>`
      : '';

    const headHtml = `
      <tr class="table-light text-center fw-bold text-secondary" style="font-size: 13px;">
        <th style="width: 55px;">எண்</th>
        <th style="text-align: left; padding-left: 15px;">மதிப்பீட்டுக் குறிப்பு</th>
        <th style="width: 140px;">மதிப்பெண்</th>
      </tr>
    `;

    let bodyHtml = '';
    if (rows.length > 0) {
      rows.forEach(r => {
        bodyHtml += `
          <tr>
            <td class="text-center fw-semibold text-secondary">${r.no}</td>
            <td class="ps-3">${r.desc}</td>
            <td class="text-center fw-semibold text-primary">${r.marks}</td>
          </tr>
        `;
      });
    } else {
      bodyHtml = `<tr><td colspan="3" class="text-center text-muted py-3">மதிப்பீட்டுக் குறிப்புகள் பதிவு செய்யப்படவில்லை.</td></tr>`;
    }

    table.innerHTML = `<thead>${titleHtml}${headHtml}</thead><tbody>${bodyHtml}</tbody>`;
  }

  // Image preview
  const imgCard = document.getElementById('vpImageCard');
  const imgEl = document.getElementById('vpImagePreview');
  const imgDownload = document.getElementById('vpImageDownloadBtn');
  const imgSrc = '<?php echo base_url("value_points/"); ?>' + item.item_code + '.png?v=' + Date.now();

  if (imgCard && imgEl) {
    imgCard.style.display = 'block';
    imgEl.src = imgSrc;
    if (imgDownload) imgDownload.href = imgSrc;
    imgEl.onerror = function() {
      imgCard.style.display = 'none';
    };
    imgEl.onload = function() {
      imgCard.style.display = 'block';
    };
  }

  // Edit shortcut button
  const editBtn = document.getElementById('vpEditShortcutBtn');
  if (editBtn) {
    editBtn.onclick = function() {
      const vpModalEl = document.getElementById('valuePointsPreviewModal');
      const vpModal = bootstrap.Modal.getInstance(vpModalEl);
      if (vpModal) vpModal.hide();
      openEditFromData(item);
    };
  }

  const modalEl = document.getElementById('valuePointsPreviewModal');
  const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
  modal.show();
}
</script>
