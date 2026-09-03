<div id="divEntryForm">
  <!-- Page Header -->
  <div class="pagetitle mb-4">
    <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
      <i class="bi bi-trophy-fill text-primary"></i> Result Entry
    </h1>
    <nav>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
        <li class="breadcrumb-item">Results</li>
        <li class="breadcrumb-item active">Result Entry</li>
      </ol>
    </nav>
  </div>

  <style>
    .multi-color-spinner {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      border: 3px solid transparent;
      border-top-color: #0d6efd;    /* Primary Blue */
      border-right-color: #198754;  /* Success Green */
      border-bottom-color: #ffc107; /* Warning Yellow */
      border-left-color: #dc3545;   /* Danger Red */
      animation: multiColorSpin 0.65s linear infinite;
      display: inline-block;
      box-sizing: border-box;
    }
    @keyframes multiColorSpin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>

  <script>
    function showItemSpinner() {
      var spin = document.getElementById('itemCodeSpinner');
      if (spin) {
        spin.classList.remove('d-none');
        spin.classList.add('d-inline-flex');
      }
    }
    function hideItemSpinner() {
      var spin = document.getElementById('itemCodeSpinner');
      if (spin) {
        spin.classList.remove('d-inline-flex');
        spin.classList.add('d-none');
      }
    }
    function onItemCodeInput(el) {
      if (el && el.value.length === 3) {
        showItemSpinner();
        fetch_item_details_result();
      } else {
        hideItemSpinner();
      }
    }
    function moveFocus(currentInput, nextInputId) {
      if (currentInput.value.length === currentInput.maxLength) {
        var nextEl = document.getElementById(nextInputId);
        if (nextEl) nextEl.focus();
      }
    }
    function checkTotalMark(totalMarkInput) {
      if (totalMarkInput.value.length === 3) {
        var saveBtn = document.getElementById("saveButton");
        if (saveBtn) saveBtn.click();
      }
    }
  </script>

  <!-- Card 1: Item Search & Overview -->
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-primary text-white py-3 px-4 border-0 rounded-top-4 d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: rgba(255, 255, 255, 0.2);">
          <i class="bi bi-search text-white fs-5"></i>
        </div>
        <div>
          <h5 class="card-title mb-0 text-white fw-bold fs-6">Select Competition Item</h5>
          <small class="text-white-50" style="font-size: 0.8rem;">Enter 3-digit item code to load participants and scoring console</small>
        </div>
      </div>
      <?php if (!empty($selected_item)) { ?>
        <span class="badge bg-white text-primary rounded-pill px-3 py-1.5 fw-bold">
          <i class="bi bi-check-circle-fill text-success me-1"></i> Item Loaded
        </span>
      <?php } ?>
    </div>

    <div class="card-body p-4">
      <?php echo form_open('', array('id' => 'resultEntry', 'name' => 'resultEntry')); ?>

      <div class="row align-items-center g-3">
        <div class="col-md-5 col-lg-4 col-xl-3">
          <label for="txtItemCode" class="form-label fw-bold text-dark small mb-1">
            <i class="bi bi-hash text-primary me-1"></i> Item Code <span class="text-danger">*</span>
          </label>
          <div class="d-flex align-items-center gap-2">
            <div class="input-group flex-grow-1">
              <span class="input-group-text bg-light border-secondary-subtle text-primary"><i class="bi bi-key-fill"></i></span>
              <?php echo form_input("txtItemCode", (string) (@$selected_item[0]['item_code'] ?? ''), 
                  'type="number" class="form-control py-2 fw-bold text-primary shadow-none border-secondary-subtle" id="txtItemCode" inputmode="numeric" onkeypress="javascript:return numbersonly(this, event, false);" oninput="javascript:onItemCodeInput(this)" onBlur="javascript:fetch_item_details_result()" maxlength="3" placeholder="Item Code..." autofocus'); ?>
            </div>
            <div id="itemCodeSpinner" class="d-none align-items-center justify-content-center flex-shrink-0" style="width: 24px; height: 24px;" title="Loading item...">
              <div class="multi-color-spinner"></div>
            </div>
          </div>
        </div>

        <?php if (!empty($selected_item)) { ?>
          <div class="col-md-7 col-lg-8 col-xl-9">
            <div class="row g-2">
              <div class="col-sm-6 col-md-5">
                <div class="p-2.5 px-3 rounded-3 bg-light border border-light-subtle">
                  <small class="text-muted d-block" style="font-size: 0.75rem;">Item Name</small>
                  <strong class="text-dark d-block text-truncate"><?php echo htmlspecialchars($selected_item[0]['item_name']); ?></strong>
                </div>
              </div>
              <div class="col-6 col-sm-3 col-md-3">
                <div class="p-2.5 px-3 rounded-3 bg-light border border-light-subtle text-center">
                  <small class="text-muted d-block" style="font-size: 0.75rem;">Participants</small>
                  <strong class="text-primary fs-6"><?php echo (int) $selected_item[0]['total_participants']; ?></strong>
                </div>
              </div>
              <div class="col-6 col-sm-3 col-md-4">
                <div class="p-2.5 px-3 rounded-3 bg-light border border-light-subtle text-center">
                  <small class="text-muted d-block" style="font-size: 0.75rem;">Number of Judges</small>
                  <strong class="text-dark fs-6"><?php echo (int) $selected_item[0]['no_of_judges']; ?></strong>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>

      <?php
      $cnt_judges = (int) @$selected_item[0]['no_of_judges'];
      $item_total_participants = (is_array($selected_item) && isset($selected_item[0]['total_participants'])) ? (int) $selected_item[0]['total_participants'] : 0;
      if (!empty($selected_item) && is_array($selected_item) && count($selected_item) > 0 && $add_edit == 'yes' && count(@$selected_item_list) != $item_total_participants) {
      ?>
        <!-- Marks Entry Sub-Card -->
        <hr class="my-4 border-secondary-subtle">
        
        <div class="d-flex align-items-center gap-2 mb-3">
          <div class="bg-primary bg-opacity-10 p-1.5 rounded text-primary">
            <i class="bi bi-pencil-fill"></i>
          </div>
          <h6 class="fw-bold mb-0 text-dark">
            <?php echo !empty($selected_result) ? 'Edit Participant Score' : 'Enter Participant Score'; ?>
          </h6>
        </div>

        <div class="row g-3 align-items-end">
          <div class="col-6 col-md-3 col-xl-2">
            <label for="txt_code_no" class="form-label fw-bold text-dark small mb-1">Code No <span class="text-danger">*</span></label>
            <?php echo form_input(
                "txt_code_no",
                (string) (@$selected_result[0]['code_no'] ?? ''),
                'type="number" class="form-control py-2 shadow-none border-secondary-subtle fw-bold" id="txt_code_no" maxlength="3" inputmode="numeric" oninput="moveFocus(this, \'txt_reg_no\', 3)" placeholder="Code"'
            ); ?>
          </div>

          <div class="col-6 col-md-3 col-xl-2">
            <label for="txt_reg_no" class="form-label fw-bold text-dark small mb-1">Reg No <span class="text-danger">*</span></label>
            <?php echo form_input(
                "txt_reg_no",
                (string) (@$selected_result[0]['participant_id'] ?? ''),
                'type="number" class="form-control py-2 shadow-none border-secondary-subtle fw-bold" id="txt_reg_no" maxlength="4" inputmode="numeric" oninput="moveFocus(this, \'mark_1\', 4)" placeholder="Reg No"'
            ); ?>
          </div>

          <?php
          $marks_array = explode('#$#', (string) (@$selected_result[0]['marks'] ?? ''));
          for ($i = 1; $i <= $cnt_judges; $i++) {
              $currentInputId = "mark_" . $i;
              $nextInputId = ($i < $cnt_judges) ? "mark_" . ($i + 1) : "totalMark";
              ?>
              <div class="col-4 col-md-2 col-xl-1">
                <label for="<?php echo $currentInputId; ?>" class="form-label fw-bold text-dark small mb-1 text-truncate">J<?php echo $i; ?> Mark</label>
                <?php echo form_input(
                    $currentInputId,
                    (string) (@$marks_array[$i - 1] ?? ''),
                    'type="number" id="' . $currentInputId . '" maxlength="2" class="form-control py-2 text-center shadow-none border-secondary-subtle fw-bold" inputmode="numeric" oninput="moveFocus(this, \'' . $nextInputId . '\', 2)" placeholder="0"'
                ); ?>
              </div>
          <?php } ?>

          <div class="col-4 col-md-2 col-xl-2">
            <label for="totalMark" class="form-label fw-bold text-primary small mb-1">Total Mark</label>
            <?php echo form_input(
                "totalMark",
                (string) (@$selected_result[0]['total_mark'] ?? ''),
                'type="number" id="totalMark" maxlength="3" class="form-control py-2 text-center shadow-none border-primary fw-bold text-primary bg-primary bg-opacity-10" inputmode="numeric" oninput="checkTotalMark(this)" placeholder="Total"'
            ); ?>
          </div>

          <div class="col-12 col-md-4 col-xl-3 d-flex gap-2">
            <?php if (!empty($selected_result) && count($selected_result) > 0) { ?>
              <button type="button" onClick="javascript:fncSaveResultEntry()" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm flex-grow-1">
                <i class="bi bi-check-lg me-1"></i> Update
              </button>
              <button type="button" onClick="javascript:fncCancel()" class="btn btn-outline-danger rounded-pill px-3 py-2 fw-semibold">
                Cancel
              </button>
            <?php } else { ?>
              <button type="button" id="saveButton" onClick="javascript:fncSaveResultEntry()" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm flex-grow-1">
                <i class="bi bi-plus-circle-fill me-1"></i> Save Score
              </button>
            <?php } ?>
          </div>
        </div>
      <?php } ?>

      <input type="hidden" name="hidItemId" id="hidItemId" value="<?php echo (is_array($selected_item) && isset($selected_item[0]['item_code'])) ? $selected_item[0]['item_code'] : @$this->input->post('hidItemId'); ?>">
      <input type="hidden" name="hidNoJudge" id="hidNoJudge" value="<?php echo (@$cnt_judges) ?>"

      <?php echo form_close(); ?>
    </div>
  </div>

  <!-- Card 2: Results Table & Actions -->
  <?php if (!empty($selected_item_list) && count($selected_item_list) > 0) {
      $is_confirm_to_print = isset($_POST['hidBlank']) ? '_blank' : '_self';
      echo form_open('', array('id' => 'resultEntryList', 'name' => 'resultEntryList', 'target' => $is_confirm_to_print));
      $total_participants = (is_array($selected_item) && isset($selected_item[0]['total_participants'])) ? (int) $selected_item[0]['total_participants'] : 0;
  ?>
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-header text-white py-3 px-4 border-0 rounded-top-4 d-flex align-items-center justify-content-between" style="background-color: #1e293b;">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: rgba(255, 255, 255, 0.2);">
            <i class="bi bi-list-check text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Item Results Roster</h5>
            <small class="text-white-50" style="font-size: 0.8rem;">Evaluation scores and ranks recorded for this competition</small>
          </div>
        </div>
        <span class="badge bg-white text-dark rounded-pill px-3 py-1.5 fw-bold">
          <?php echo count($selected_item_list); ?> of <?php echo $total_participants; ?> Recorded
        </span>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
            <thead class="table-light text-secondary border-bottom border-light-subtle">
              <tr>
                <th class="ps-4 py-3 text-center" width="50">#</th>
                <th class="py-3 text-center" width="90">Reg No</th>
                <th class="py-3 text-center" width="90">Code</th>
                <?php for ($i = 1; $i <= @$cnt_judges; $i++) { ?>
                  <th class="py-3 text-center" width="70">J<?php echo $i; ?></th>
                <?php } ?>
                <th class="py-3 text-center" width="80">Total</th>
                <th class="py-3 text-center" width="80">%</th>
                <th class="py-3 text-center" width="90">Rank</th>
                <th class="py-3 text-center" width="80">Grade</th>
                <th class="py-3 text-center" width="70">Point</th>
                <?php if ($show_conirm_button == 'yes') { ?>
                  <th class="pe-4 py-3 text-center" width="130">Actions</th>
                <?php } ?>
              </tr>
            </thead>
            <tbody>
              <?php
              $cnt_item_list = count($selected_item_list);
              $a_grade_count = $b_grade_count = $c_grade_count = $withheld_count = 0;

              for ($i = 0; $i < $cnt_item_list; $i++) {
                  if ($selected_item_list[$i]['grade'] == 'A') $a_grade_count++;
                  else if ($selected_item_list[$i]['grade'] == 'B') $b_grade_count++;
                  else if ($selected_item_list[$i]['grade'] == 'C') $c_grade_count++;

                  $withheld_simbol = '';
                  if ($selected_item_list[$i]['is_withheld'] == 'Y') {
                      $withheld_simbol = ' <span class="badge bg-danger rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;">Withheld</span>';
                      $withheld_count++;
                  }

                  $rankNum = $selected_item_list[$i]['rank'];
                  $gradeVal = $selected_item_list[$i]['grade'];
              ?>
                <tr>
                  <td class="ps-4 text-center text-muted"><?php echo $i + 1; ?></td>
                  <td class="text-center fw-bold text-dark">
                    <?php echo htmlspecialchars((string)$selected_item_list[$i]['participant_id']) . $withheld_simbol; ?>
                  </td>
                  <td class="text-center font-monospace fw-semibold text-secondary">
                    <?php echo htmlspecialchars((string)$selected_item_list[$i]['code_no']); ?>
                  </td>
                  <?php
                  $marks_array = explode('#$#', (string) $selected_item_list[$i]['marks']);
                  for ($j = 0; $j < @$cnt_judges; $j++) {
                  ?>
                    <td class="text-center text-muted"><?php echo htmlspecialchars((string)@$marks_array[$j]); ?></td>
                  <?php } ?>
                  <td class="text-center fw-bold text-primary"><?php echo htmlspecialchars((string)$selected_item_list[$i]['total_mark']); ?></td>
                  <td class="text-center text-secondary"><?php echo htmlspecialchars((string)$selected_item_list[$i]['percentage']); ?>%</td>
                  <td class="text-center">
                    <?php if ($rankNum == 1) { ?>
                      <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-trophy-fill me-1"></i>1st</span>
                    <?php } else if ($rankNum == 2) { ?>
                      <span class="badge bg-secondary text-white rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-award-fill me-1"></i>2nd</span>
                    <?php } else if ($rankNum == 3) { ?>
                      <span class="badge rounded-pill px-2.5 py-1 fw-bold text-white" style="background-color: #b45309;"><i class="bi bi-award-fill me-1"></i>3rd</span>
                    <?php } else if (!empty($rankNum) && $rankNum > 3) { ?>
                      <span class="badge bg-light text-dark border rounded-pill px-2 py-1"><?php echo $rankNum; ?></span>
                    <?php } else { ?>
                      <span class="text-muted">-</span>
                    <?php } ?>
                  </td>
                  <td class="text-center">
                    <?php if ($gradeVal == 'A') { ?>
                      <span class="badge bg-success rounded-pill px-2.5 py-1 fw-bold">A</span>
                    <?php } else if ($gradeVal == 'B') { ?>
                      <span class="badge bg-primary rounded-pill px-2.5 py-1 fw-bold">B</span>
                    <?php } else if ($gradeVal == 'C') { ?>
                      <span class="badge bg-info text-dark rounded-pill px-2.5 py-1 fw-bold">C</span>
                    <?php } else { ?>
                      <span class="text-muted">-</span>
                    <?php } ?>
                  </td>
                  <td class="text-center fw-bold"><?php echo htmlspecialchars((string)$selected_item_list[$i]['point']); ?></td>
                  <?php if ($show_conirm_button == 'yes') { ?>
                    <td class="pe-4 text-center">
                      <div class="d-flex justify-content-center gap-1">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1" onClick="javascript:editResult('<?php echo $selected_item_list[$i]['rm_id'] ?>')">
                          <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1" onClick="javascript:deleteResult('<?php echo $selected_item_list[$i]['rm_id'] ?>')">
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
        <div class="p-4 bg-light border-top border-light-subtle rounded-bottom-4">
          <?php if ($cnt_item_list > 0 && $show_conirm_button == 'yes') { ?>
            <div class="d-flex justify-content-end">
              <button type="button" class="btn btn-success rounded-pill px-4 py-2.5 fw-bold shadow-sm d-flex align-items-center gap-2" onClick="javascript:confirmResutlEntry(); return false;">
                <i class="bi bi-check-all fs-5"></i>
                <span>Confirm Results</span>
              </button>
            </div>
          <?php } else if (isset($show_conirm_button) && 'no' == $show_conirm_button) {
              $absentee_array = (isset($absentee_list) && !empty($absentee_list)) ? explode(',', $absentee_list) : array();
          ?>
            <div class="row g-3 align-items-center">
              <div class="col-lg-8">
                <div class="d-flex flex-wrap gap-2">
                  <div class="bg-white border rounded-3 p-2 px-3 shadow-none">
                    <small class="text-muted d-block" style="font-size: 0.72rem;">Absentees</small>
                    <strong class="text-danger"><?php echo count($absentee_array); ?></strong>
                    <?php if (count($absentee_array) > 0) { ?>
                      <small class="text-muted ms-1">(Reg: <?php echo htmlspecialchars($absentee_list); ?>)</small>
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

    <input type="hidden" name="hidItemId" id="hidItemId" value="<?php echo (is_array($selected_item) && isset($selected_item[0]['item_code'])) ? $selected_item[0]['item_code'] : @$this->input->post('hidItemId'); ?>">
    <input type="hidden" name="hidNoJudge" id="hidNoJudge" value="<?php echo (@$cnt_judges) ?>">
    <input type="hidden" name="hidResultId" id="hidResultId" value="">
    <input type="hidden" name="hidBlank" id="hidBlank" value="_blank">

  <?php
      echo form_close();
  } ?>

  <!-- Hidden Print Container -->
  <div id="print_content" class="d-none"></div>

  <?php if (@$this->input->post('hidItemId') and @$show_conirm_button == 'yes') { ?>
    <script>
      window.onload = function() {
        var el = document.getElementById('txt_code_no');
        if (el) el.focus();
      };
    </script>
  <?php } ?>
</div>