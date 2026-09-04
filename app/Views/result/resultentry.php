<?php
$selected_item = $selected_item ?? false;
$selected_item_list = $selected_item_list ?? [];
$selected_result = $selected_result ?? [];
$absentee_list = $absentee_list ?? false;
$show_conirm_button = $show_conirm_button ?? 'yes';
$add_edit = $add_edit ?? 'yes';
$item_code = $item_code ?? '';
$post_item_id = (string) ($item_code ?: (request()->getPost('code') ?? request()->getPost('hidItemId') ?? request()->getPost('txtItemCode') ?? ''));

$item_info = (is_array($selected_item) && isset($selected_item[0])) ? $selected_item[0] : null;
$item_code_val = $item_info ? (string)($item_info['item_code'] ?? '') : $post_item_id;
$item_name_val = $item_info ? (string)($item_info['item_name'] ?? '') : '';
$total_participants = $item_info ? (int)($item_info['total_participants'] ?? 0) : 0;
$cnt_judges = $item_info ? (int)($item_info['no_of_judges'] ?? 0) : 0;

$cnt_item_list = is_countable($selected_item_list) ? count($selected_item_list) : 0;
$progress_pct = ($total_participants > 0) ? min(100, (int)round(($cnt_item_list / $total_participants) * 100)) : 0;
?>
<div id="divEntryForm" class="w-100">
  <style>
    @font-face {
      font-family: 'TAU-Pallai';
      src: local('TAU-Pallai');
    }
    .font-tamil {
      font-family: 'TAU-Pallai', system-ui, -apple-system, sans-serif;
      font-size: 14px;
    }
    .display_none {
      display: none !important;
    }
    .multi-color-spinner {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      border: 3px solid transparent;
      border-top-color: #0d6efd;
      border-right-color: #198754;
      border-bottom-color: #ffc107;
      border-left-color: #dc3545;
      animation: reMultiSpin 0.65s linear infinite;
      display: inline-block;
      box-sizing: border-box;
    }
    @keyframes reMultiSpin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .glass-card {
      background: #ffffff;
      border-radius: 16px;
      border: 1px solid rgba(226, 232, 240, 0.9);
      box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
      transition: all 0.2s ease;
    }
    .badge-rank-1 {
      background: linear-gradient(135deg, #f59e0b, #d97706);
      color: #ffffff;
      box-shadow: 0 2px 6px rgba(217, 119, 6, 0.3);
    }
    .badge-rank-2 {
      background: linear-gradient(135deg, #94a3b8, #64748b);
      color: #ffffff;
      box-shadow: 0 2px 6px rgba(100, 116, 139, 0.3);
    }
    .badge-rank-3 {
      background: linear-gradient(135deg, #d97706, #92400e);
      color: #ffffff;
      box-shadow: 0 2px 6px rgba(146, 64, 14, 0.3);
    }
    .badge-grade-a {
      background: linear-gradient(135deg, #10b981, #059669);
      color: #ffffff;
    }
    .badge-grade-b {
      background: linear-gradient(135deg, #3b82f6, #2563eb);
      color: #ffffff;
    }
    .badge-grade-c {
      background: linear-gradient(135deg, #06b6d4, #0891b2);
      color: #ffffff;
    }
    .judge-card {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 12px 14px;
      transition: all 0.15s ease-in-out;
    }
    .judge-card:focus-within {
      background: #ffffff;
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }
    .btn-action-icon {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: transform 0.15s ease, background-color 0.15s ease;
    }
    .btn-action-icon:hover {
      transform: scale(1.08);
    }
  </style>

  <!-- Page Title & Breadcrumbs -->
  <div class="pagetitle mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div>
        <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
          <i class="bi bi-trophy-fill text-primary"></i> Result Entry
        </h1>
        <nav>
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item">Result</li>
            <li class="breadcrumb-item active">Result Entry</li>
          </ol>
        </nav>
      </div>
      <?php if (!empty($selected_item)) { ?>
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-1.5">
          <i class="bi bi-check-circle-fill"></i> Item Loaded: #<?php echo htmlspecialchars((string)$item_code_val); ?>
        </span>
      <?php } ?>
    </div>
  </div>

  <!-- Card 1: Item Search & Overview -->
  <div class="glass-card mb-4 overflow-hidden">
    <div class="p-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
      <div class="row align-items-center g-3 text-white">
        <div class="col-lg-6">
          <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(4px);">
              <i class="bi bi-search text-white fs-4"></i>
            </div>
            <div>
              <h5 class="fw-bold mb-1 text-white">Competition Item Selection</h5>
              <p class="mb-0 text-white-50 small">Enter the 3-digit item code to load participants and score sheets</p>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <?php echo form_open('', array('id' => 'resultEntry', 'name' => 'resultEntry', 'onsubmit' => 'return false;')); ?>
          <div class="row g-2 align-items-center justify-content-lg-end">
            <div class="col-sm-8 col-md-7 col-lg-8">
              <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden bg-white p-1">
                <span class="input-group-text bg-white border-0 text-primary ps-3 pe-2">
                  <i class="bi bi-hash fs-4 fw-bold"></i>
                </span>
                <?php echo form_input(
                    "txtItemCode",
                    "",
                    'type="number" class="form-control border-0 fw-bold fs-5 text-dark shadow-none" id="txtItemCode" placeholder="Item Code (e.g. 101)" inputmode="numeric" onkeypress="javascript:return numbersonly(this, event, false);" onkeydown="if(event.key===\'Enter\'||event.keyCode===13){event.preventDefault();fetch_item_details_result(true);}" oninput="if(this.value.length===3){fetch_item_details_result();}" onfocus="this.select();" onBlur="javascript:fetch_item_details_result()" maxlength="3" autofocus'
                ); ?>
                <span class="input-group-text bg-white border-0 pe-3">
                  <div id="itemCodeSpinner" class="d-none align-items-center justify-content-center" title="Fetching item details...">
                    <div class="multi-color-spinner"></div>
                  </div>
                </span>
              </div>
            </div>
            <div class="col-sm-4 col-md-3 col-lg-4">
              <button type="button" class="btn btn-warning w-100 rounded-pill py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-1.5" onclick="fetch_item_details_result(true);">
                <i class="bi bi-arrow-repeat"></i> Load
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Active Item Metadata Strip (when item is loaded) -->
    <?php if (!empty($selected_item)) { ?>
      <div class="p-4 bg-light border-top border-light-subtle">
        <div class="row g-3 align-items-center">
          <div class="col-md-5 col-lg-5">
            <small class="text-muted text-uppercase fw-semibold d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Competition Name</small>
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-primary rounded-pill px-2.5 py-1 font-monospace">#<?php echo htmlspecialchars((string)$item_code_val); ?></span>
              <strong class="fs-6 text-dark font-tamil"><?php echo htmlspecialchars($item_name_val); ?></strong>
            </div>
          </div>

          <div class="col-6 col-sm-3 col-md-2 col-lg-2">
            <div class="bg-white p-2.5 px-3 rounded-3 border text-center">
              <small class="text-muted d-block" style="font-size: 0.72rem;">Participants</small>
              <span class="fw-bold text-dark fs-6"><?php echo $total_participants; ?></span>
            </div>
          </div>

          <div class="col-6 col-sm-3 col-md-2 col-lg-2">
            <div class="bg-white p-2.5 px-3 rounded-3 border text-center">
              <small class="text-muted d-block" style="font-size: 0.72rem;">Judges</small>
              <span class="fw-bold text-primary fs-6"><?php echo $cnt_judges; ?></span>
            </div>
          </div>

          <div class="col-md-5 col-lg-3">
            <div class="bg-white p-2.5 px-3 rounded-3 border">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="text-muted" style="font-size: 0.72rem;">Scoring Progress</small>
                <small class="fw-bold text-primary"><?php echo $cnt_item_list; ?> / <?php echo $total_participants; ?> (<?php echo $progress_pct; ?>%)</small>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $progress_pct; ?>%;" aria-valuenow="<?php echo $progress_pct; ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Marks Entry Compact Single Row Console -->
        <?php
        $is_editing = !empty($selected_result);
        if ($add_edit == 'yes') {
        ?>
          <hr class="my-3 border-secondary-subtle">
          
          <div id="marksEntryConsole" class="bg-white rounded-3 p-3 border border-primary border-opacity-25 shadow-sm <?php echo ($cnt_item_list == $total_participants && !$is_editing) ? 'd-none' : ''; ?>">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="d-flex align-items-center gap-1.5" id="consoleTitle">
                <i class="bi bi-pencil-square text-primary"></i>
                <strong class="text-dark small">
                  <?php echo $is_editing ? 'Edit Participant Score' : 'Enter Participant Score'; ?>
                </strong>
              </div>
              <small class="text-muted" style="font-size: 0.75rem;">
                <i class="bi bi-keyboard me-1"></i> Press Enter to save
              </small>
            </div>

            <div class="d-flex align-items-end gap-2 flex-wrap flex-lg-nowrap">
              <!-- Code No -->
              <div class="flex-shrink-0" style="width: 100px;">
                <label for="txt_code_no" class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.78rem;">
                  Code No <span class="text-danger">*</span>
                </label>
                <?php echo form_input(
                    "txt_code_no",
                    (string) (@$selected_result[0]['code_no'] ?? ''),
                    'type="number" class="form-control form-control-sm text-center fw-bold border-secondary-subtle" id="txt_code_no" maxlength="3" inputmode="numeric" placeholder="Code" oninput="moveFocus(this, \'txt_reg_no\', 3)"'
                ); ?>
              </div>

              <!-- Reg No -->
              <div class="flex-shrink-0" style="width: 110px;">
                <label for="txt_reg_no" class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.78rem;">
                  Reg No <span class="text-danger">*</span>
                </label>
                <?php echo form_input(
                    "txt_reg_no",
                    (string) (@$selected_result[0]['participant_id'] ?? ''),
                    'type="number" class="form-control form-control-sm text-center fw-bold border-secondary-subtle" id="txt_reg_no" maxlength="4" inputmode="numeric" placeholder="Reg No" oninput="moveFocus(this, \'mark_1\', 4)"'
                ); ?>
              </div>

              <!-- Judge Marks in same row -->
              <?php
              $marks_array = explode('#$#', (string) (@$selected_result[0]['marks'] ?? ''));
              for ($i = 1; $i <= $cnt_judges; $i++) {
                  $currentInputId = "mark_" . $i;
                  $nextInputId = ($i < $cnt_judges) ? "mark_" . ($i + 1) : "totalMark";
              ?>
                <div class="flex-shrink-0" style="width: 85px;">
                  <label for="<?php echo $currentInputId; ?>" class="form-label small fw-semibold text-muted mb-1 text-center d-block" style="font-size: 0.78rem;">
                    Judge #<?php echo $i; ?>
                  </label>
                  <?php echo form_input(
                      $currentInputId,
                      (string) (@$marks_array[$i - 1] ?? ''),
                      'type="number" id="' . $currentInputId . '" maxlength="2" class="form-control form-control-sm text-center fw-bold border-primary-subtle" inputmode="numeric" placeholder="0" oninput="onJudgeMarkInput(this, \'' . $nextInputId . '\')"'
                  ); ?>
                </div>
              <?php } ?>

              <!-- Total Mark in same row -->
              <div class="flex-shrink-0" style="width: 95px;">
                <label for="totalMark" class="form-label small fw-bold text-primary mb-1 text-center d-block" style="font-size: 0.78rem;">
                  Total
                </label>
                <?php echo form_input(
                    "totalMark",
                    (string) (@$selected_result[0]['total_mark'] ?? ''),
                    'type="number" id="totalMark" maxlength="3" class="form-control form-control-sm text-center fw-bold text-primary bg-primary bg-opacity-10 border-primary" inputmode="numeric" placeholder="Total" onkeydown="if(event.key===\'Enter\'||event.keyCode===13){event.preventDefault();document.getElementById(\'saveButton\').click();}" oninput="checkTotalMark(this)"'
                ); ?>
              </div>

              <!-- Hidden Result Id for Editing -->
              <input type="hidden" name="hidResultId" id="resultEntry_hidResultId" value="<?php echo htmlspecialchars((string)(@$selected_result[0]['rm_id'] ?? '')); ?>">

              <!-- Action Buttons in same row -->
              <div class="d-flex gap-1 align-items-end flex-shrink-0 ms-auto">
                <button type="button" class="btn btn-sm btn-primary px-3 fw-bold shadow-sm d-flex align-items-center gap-1" id="saveButton" onClick="javascript:fncSaveResultEntry()" title="<?php echo $is_editing ? 'Update Score' : 'Save Score (Enter)'; ?>">
                  <i class="bi <?php echo $is_editing ? 'bi-save2' : 'bi-check2-circle'; ?>"></i> <span><?php echo $is_editing ? 'Update' : 'Save'; ?></span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary px-2.5 <?php echo $is_editing ? '' : 'd-none'; ?>" id="cancelEditButton" onClick="javascript:fncCancel()" title="Cancel Edit">
                  Cancel
                </button>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    <?php } else if (!empty($item_code_val)) { ?>
      <div class="p-3 bg-white border-top">
        <div class="alert alert-warning border-0 rounded-3 mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
          <span>உருப்படி எண் <strong>#<?php echo htmlspecialchars((string)$item_code_val); ?></strong>-க்கான விபரங்கள் எதுவும் கிடைக்கவில்லை. சரியான உருப்படி எண்ணை உள்ளிடவும்.</span>
        </div>
      </div>
    <?php } ?>

    <!-- Hidden Fields for Item Search Form -->
    <input type="hidden" name="hidItemId" id="hidItemId" value="<?php echo htmlspecialchars((string)$item_code_val); ?>">
    <input type="hidden" name="hidNoJudge" id="hidNoJudge" value="<?php echo (int)$cnt_judges; ?>">
    <?php echo form_close(); ?>
  </div>

  <!-- Card 2: Results Table & Actions -->
  <?php if (!empty($selected_item_list) && count($selected_item_list) > 0) {
      echo form_open('', array('id' => 'resultEntryList', 'name' => 'resultEntryList', 'target' => '_self'));
  ?>
    <div class="glass-card mb-4 overflow-hidden">
      <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
          <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-table"></i>
          </div>
          <div>
            <h5 class="fw-bold mb-0 text-dark">Results Roster</h5>
            <small class="text-muted">Showing all recorded results for item #<?php echo htmlspecialchars((string)$item_code_val); ?></small>
          </div>
        </div>
        <span class="badge bg-light text-secondary border rounded-pill px-3 py-2 font-monospace">
          Total Entries: <?php echo $cnt_item_list; ?>
        </span>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table id="dtResultsTable" class="table table-hover align-middle mb-0" width="100%">
            <thead class="table-light text-secondary border-bottom">
              <tr>
                <th class="ps-4 text-center" width="5%">Sl</th>
                <th class="text-center" width="11%">Reg No</th>
                <th class="text-center" width="9%">Code</th>
                <?php for ($i = 1; $i <= $cnt_judges; $i++) { ?>
                  <th class="text-center" width="8%">Judge <?php echo $i; ?></th>
                <?php } ?>
                <th class="text-center" width="9%">Total</th>
                <th class="text-center" width="8%">% Mark</th>
                <th class="text-center" width="10%">Rank</th>
                <th class="text-center" width="8%">Grade</th>
                <th class="text-center" width="8%">Points</th>
                <?php if ($show_conirm_button == 'yes') { ?>
                  <th class="pe-4 text-center" width="10%">Actions</th>
                <?php } ?>
              </tr>
            </thead>
            <tbody>
              <?php
              $a_grade_count = $b_grade_count = $c_grade_count = $withheld_count = 0;
              for ($i = 0; $i < $cnt_item_list; $i++) {
                  $row = $selected_item_list[$i];
                  if ($row['grade'] == 'A') $a_grade_count++;
                  else if ($row['grade'] == 'B') $b_grade_count++;
                  else if ($row['grade'] == 'C') $c_grade_count++;

                  $withheld_simbol = '';
                  if ($row['is_withheld'] == 'Y') {
                      $withheld_simbol = '<span class="badge bg-warning text-dark ms-1 small" title="Withheld">* Withheld</span>';
                      $withheld_count++;
                  }

                  $rankNum = (int)($row['rank'] ?? 0);
                  $gradeVal = trim((string)($row['grade'] ?? ''));
                  $marks_array = explode('#$#', (string)($row['marks'] ?? ''));
              ?>
                <tr id="result_row_<?php echo $row['rm_id']; ?>"
                    data-rm-id="<?php echo htmlspecialchars((string)$row['rm_id']); ?>"
                    data-participant-id="<?php echo htmlspecialchars((string)$row['participant_id']); ?>"
                    data-code-no="<?php echo htmlspecialchars((string)$row['code_no']); ?>"
                    data-marks="<?php echo htmlspecialchars((string)($row['marks'] ?? '')); ?>"
                    data-total-mark="<?php echo htmlspecialchars((string)$row['total_mark']); ?>">
                  <td class="ps-4 text-center text-muted fw-semibold"><?php echo $i + 1; ?></td>
                  <td class="text-center">
                    <span class="fw-bold text-dark font-monospace"><?php echo htmlspecialchars((string)$row['participant_id']); ?></span>
                    <?php echo $withheld_simbol; ?>
                  </td>
                  <td class="text-center font-monospace text-secondary"><?php echo htmlspecialchars((string)$row['code_no']); ?></td>
                  <?php for ($j = 0; $j < $cnt_judges; $j++) { ?>
                    <td class="text-center text-secondary"><?php echo htmlspecialchars((string)($marks_array[$j] ?? '')); ?></td>
                  <?php } ?>
                  <td class="text-center fw-bold text-primary fs-6"><?php echo htmlspecialchars((string)$row['total_mark']); ?></td>
                  <td class="text-center text-muted"><?php echo htmlspecialchars((string)$row['percentage']); ?>%</td>
                  <td class="text-center">
                    <?php if ($rankNum == 1) { ?>
                      <span class="badge badge-rank-1 rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-trophy-fill me-1"></i>1st</span>
                    <?php } else if ($rankNum == 2) { ?>
                      <span class="badge badge-rank-2 rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-award-fill me-1"></i>2nd</span>
                    <?php } else if ($rankNum == 3) { ?>
                      <span class="badge badge-rank-3 rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-award-fill me-1"></i>3rd</span>
                    <?php } else if ($rankNum > 3) { ?>
                      <span class="badge bg-light text-dark border rounded-pill px-2 py-1"><?php echo $rankNum; ?></span>
                    <?php } else { ?>
                      <span class="text-muted">-</span>
                    <?php } ?>
                  </td>
                  <td class="text-center">
                    <?php if ($gradeVal == 'A') { ?>
                      <span class="badge badge-grade-a rounded-pill px-2.5 py-1 fw-bold">A</span>
                    <?php } else if ($gradeVal == 'B') { ?>
                      <span class="badge badge-grade-b rounded-pill px-2.5 py-1 fw-bold">B</span>
                    <?php } else if ($gradeVal == 'C') { ?>
                      <span class="badge badge-grade-c rounded-pill px-2.5 py-1 fw-bold">C</span>
                    <?php } else { ?>
                      <span class="text-muted">-</span>
                    <?php } ?>
                  </td>
                  <td class="text-center fw-bold text-dark"><?php echo htmlspecialchars((string)$row['point']); ?></td>
                  <?php if ($show_conirm_button == 'yes') { ?>
                    <td class="pe-4 text-center">
                      <div class="d-flex justify-content-center gap-1">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-action-icon" title="Edit Result" onClick="javascript:editResult('<?php echo $row['rm_id']; ?>')">
                          <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-action-icon" title="Delete Result" onClick="javascript:deleteResult('<?php echo $row['rm_id']; ?>')">
                          <i class="bi bi-trash3"></i>
                        </button>
                      </div>
                    </td>
                  <?php } ?>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>

        <!-- Footer / Action Area -->
        <div class="p-4 bg-light border-top border-light-subtle">
          <?php if ($cnt_item_list > 0 && $show_conirm_button == 'yes') { ?>
            <div class="d-flex justify-content-end">
              <button type="button" class="btn btn-success rounded-pill px-4 py-2.5 fw-bold shadow-sm d-flex align-items-center gap-2" onClick="javascript:confirmResutlEntry(); return false;">
                <i class="bi bi-check-all fs-5"></i>
                <span>Confirm Results</span>
              </button>
            </div>
          <?php } else if ($show_conirm_button == 'no') {
              $absentee_array = (isset($absentee_list) && !empty($absentee_list)) ? explode(',', $absentee_list) : array();
          ?>
            <div class="row g-3 align-items-center">
              <div class="col-lg-8">
                <div class="d-flex flex-wrap gap-2">
                  <div class="bg-white border rounded-3 p-2 px-3 shadow-none">
                    <small class="text-muted d-block" style="font-size: 0.72rem;">Absentees</small>
                    <strong class="text-danger"><?php echo count($absentee_array); ?></strong>
                    <?php if (count($absentee_array) > 0) { ?>
                      <small class="text-muted ms-1">(Reg: <?php echo htmlspecialchars((string)$absentee_list); ?>)</small>
                    <?php } ?>
                  </div>
                  <div class="bg-white border rounded-3 p-2 px-3 shadow-none">
                    <small class="text-muted d-block" style="font-size: 0.72rem;">Withheld</small>
                    <strong class="text-warning"><?php echo $withheld_count; ?></strong>
                  </div>
                  <div class="bg-white border rounded-3 p-2 px-3 shadow-none text-center">
                    <small class="text-muted d-block" style="font-size: 0.72rem;">A Grade</small>
                    <strong class="text-success"><?php echo $a_grade_count; ?></strong>
                  </div>
                  <div class="bg-white border rounded-3 p-2 px-3 shadow-none text-center">
                    <small class="text-muted d-block" style="font-size: 0.72rem;">B Grade</small>
                    <strong class="text-primary"><?php echo $b_grade_count; ?></strong>
                  </div>
                  <div class="bg-white border rounded-3 p-2 px-3 shadow-none text-center">
                    <small class="text-muted d-block" style="font-size: 0.72rem;">C Grade</small>
                    <strong class="text-info"><?php echo $c_grade_count; ?></strong>
                  </div>
                </div>
              </div>

              <div class="col-lg-4 d-flex justify-content-lg-end">
                <button type="button" class="btn text-white rounded-pill px-4 py-2.5 fw-bold shadow-sm d-flex align-items-center gap-2" style="background-color: #1e293b;" onClick="javascript:printConfidentialReport(); return false;">
                  <i class="bi bi-file-earmark-pdf-fill text-warning"></i>
                  <span>Print Confidential Report</span>
                </button>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

    <!-- Hidden Fields for Results Table Form -->
    <input type="hidden" name="hidItemId" id="hidItemId" value="<?php echo htmlspecialchars((string)$item_code_val); ?>">
    <input type="hidden" name="hidNoJudge" id="hidNoJudge" value="<?php echo (int)$cnt_judges; ?>">
    <input type="hidden" name="hidResultId" id="hidResultId" value="">
    <input type="hidden" name="hidCntItemList" id="hidCntItemList" value="<?php echo (int)$cnt_item_list; ?>">
    <input type="hidden" name="hidTotalPart" id="hidTotalPart" value="<?php echo (int)$total_participants; ?>">
  <?php
      echo form_close();
  } ?>

  <!-- Hidden Print Container for PDF & Confidential Printing -->
  <div id="print_content" class="display_none">
    <table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
      <tr>
        <td>
          <table width="100%" border="1" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
            <tr>
              <th width="4%" colspan="4" align="left">Result Entry.</th>
            </tr>
            <tr>
              <td align="left" width="20%" class="table_row_first">Item : </td>
              <td align="left" colspan="3" width="30%" class="table_row_first"><b><?php echo (is_array($selected_item) && isset($selected_item[0])) ? ($selected_item[0]['item_code'] . ' - ' . $selected_item[0]['item_name']) : ''; ?></b></td>
            </tr>
            <tr>
              <td align="left" width="20%" class="table_row_first">Number of participants: </td>
              <td align="left" width="30%" colspan="0" class="table_row_first"><?php echo $total_participants; ?></td>
              <td align="left" width="20%" class="table_row_first">Number of Judges : </td>
              <td align="left" width="30%" colspan="0" class="table_row_first"><?php echo $cnt_judges; ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table width="100%" border="1" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
            <tr>
              <th align="left" width="6%">Sl No</th>
              <th align="left" width="10%">Reg No</th>
              <th align="left" width="8%">Code No</th>
              <th align="left" width="8%">Total</th>
              <th align="left" width="8%">% Mark</th>
              <th align="center" width="8%">Rank</th>
              <th align="center" width="8%">Grade</th>
              <th align="center" width="8%">Point</th>
            </tr>
            <?php
            for ($i = 0; $i < $cnt_item_list; $i++) {
                $row = $selected_item_list[$i];
                $withheld_simbol = ($row['is_withheld'] == 'Y') ? '<span class="with_held"> * </span>' : '';
                $classname = ($i % 2 == 0) ? 'table_row_second' : 'table_row_first';
            ?>
              <tr>
                <td align="left" class="<?php echo $classname; ?>"><?php echo $i + 1; ?></td>
                <td align="left" class="<?php echo $classname; ?>"><?php echo $row['participant_id'] . $withheld_simbol; ?></td>
                <td align="left" class="<?php echo $classname; ?>"><?php echo $row['code_no']; ?></td>
                <td align="left" class="<?php echo $classname; ?>"><?php echo $row['total_mark']; ?></td>
                <td align="left" class="<?php echo $classname; ?>"><?php echo $row['percentage']; ?></td>
                <td align="center" class="<?php echo $classname; ?>"><?php echo $row['rank']; ?></td>
                <td align="center" class="<?php echo $classname; ?>"><?php echo $row['grade']; ?></td>
                <td align="center" class="<?php echo $classname; ?>"><?php echo $row['point']; ?></td>
              </tr>
            <?php } ?>
          </table>
        </td>
      </tr>
    </table>
  </div>

  <?php if (!empty($item_code_val) && ($show_conirm_button ?? 'yes') == 'yes') { ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var consoleEl = document.getElementById('marksEntryConsole');
        <?php if (!empty($selected_result)) { ?>
          if (consoleEl) {
            consoleEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        <?php } ?>
        var el = document.getElementById('txt_code_no');
        if (el) el.focus();
      });
    </script>
  <?php } ?>
</div>
