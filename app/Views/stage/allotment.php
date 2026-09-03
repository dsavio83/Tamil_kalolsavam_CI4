<div id="divEntryForm">

<div class="pagetitle mb-3">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="d-flex align-items-center gap-2 text-dark fw-bold mb-1">
        <i class="bi bi-calendar2-check-fill text-primary"></i>
        <span>Stage Allotment (Item Wise)</span>
      </h1>
      <p class="text-muted small mb-0">Configure stage, date, time schedule, clusters, and judges for an individual event.</p>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 bg-light px-3 py-1.5 rounded-pill border">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none"><i class="bi bi-house-door-fill me-1"></i>Home</a></li>
        <li class="breadcrumb-item text-muted">Stage</li>
        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Item Wise Allotment</li>
      </ol>
    </nav>
  </div>
</div>

<?php echo form_open('stage/allotment/save_allotment', array('id' => 'formAllotment', 'name' => 'formAllotment')); ?>

<!-- Item Code Search & Details Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
  <div class="card-header bg-gradient bg-primary text-white py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-search fs-5"></i>
      <h6 class="mb-0 fw-bold">Select Event / Item</h6>
    </div>
    <?php if (!empty($selected_item[0]['item_code'])) { ?>
      <span class="badge bg-white text-primary rounded-pill font-monospace px-3 py-1">Item #<?php echo esc($selected_item[0]['item_code']); ?></span>
    <?php } ?>
  </div>

  <div class="card-body p-4">
    <div class="row align-items-center g-3">
      <div class="col-12 col-md-5 col-lg-4">
        <label for="txtItemCode" class="form-label fw-bold text-dark d-flex align-items-center gap-1 small">
          <i class="bi bi-upc-scan text-primary"></i>
          <span>Item Code <span class="text-danger">*</span></span>
        </label>
        <div class="input-group" id="divSchoolCode">
          <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-hash"></i></span>
          <?php echo form_input("txtItemCode", (string) (@$selected_item[0]['item_code'] ?? ''), 'class="form-control border-start-0 py-2 font-monospace fw-bold fs-6" id="txtItemCode" placeholder="Enter Item Code (e.g. 101)" onkeypress="javascript:return numbersonly(this, event, false);" onBlur="javascript:fetch_item_details()"'); ?>
          <button type="button" class="btn btn-outline-primary" onclick="fetch_item_details()" title="Fetch Item Details">
            <i class="bi bi-arrow-clockwise"></i> Load
          </button>
        </div>
        <small class="text-muted mt-1 d-block">Type code & blur or click Load.</small>
      </div>

      <?php if (!empty($selected_item[0]['item_name'])) { ?>
      <div class="col-12 col-md-7 col-lg-8">
        <div class="p-3 bg-light rounded-3 border">
          <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-2">
            <div>
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1">
                <?php echo (@$selected_item[0]['item_type'] == 'G') ? 'Group Event' : 'Single Event'; ?>
              </span>
              <?php if (@$selected_item[0]['is_off_stage'] == 'Y') { ?>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Off Stage</span>
              <?php } else { ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle">On Stage</span>
              <?php } ?>
              <h5 class="fw-bold text-dark mt-1 mb-0"><?php echo esc($selected_item[0]['item_name']); ?></h5>
            </div>
            <div class="d-flex gap-2">
              <div class="text-center px-2 py-1 bg-white rounded border">
                <small class="text-muted d-block" style="font-size: 11px;">Participants</small>
                <span class="fw-bold text-dark font-monospace"><?php echo (int)@$selected_item[0]['total_participants']; ?></span>
              </div>
              <div class="text-center px-2 py-1 bg-white rounded border">
                <small class="text-muted d-block" style="font-size: 11px;">Est. Time</small>
                <span class="fw-bold text-primary font-monospace"><?php echo esc(get_time_format((int) @$selected_item[0]['total_time'])); ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>
</div>

<!-- Allotment Parameters Form Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
  <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center gap-2">
    <i class="bi bi-sliders text-primary fs-5"></i>
    <h6 class="mb-0 fw-bold text-dark">Stage & Schedule Settings</h6>
  </div>

  <div class="card-body p-4">
    <?php
    $stage_array = array('0' => '-- Select Stage --');
    for ($i = 0; $i < count($stages); $i++) {
        $stage_array[$stages[$i]['stage_id']] = $stages[$i]['stage_name'];
    }

    $date = '';
    $hour = 'HH';
    $minute = 'MM';
    if (!empty($alloted_records[0]['start_time'])) {
        $explode_date = explode(' ', $alloted_records[0]['start_time']);
        $date = $explode_date[0];
        if (!empty($explode_date[1])) {
            $time_explode = explode(':', $explode_date[1]);
            $hour = $time_explode[0];
            $minute = $time_explode[1];
        }
    }

    $value_array = array();
    $limit = (@$selected_item[0]['is_off_stage'] == 'Y') ? 1 : (($no_of_clusters > (int)@$selected_item[0]['total_participants']) ? (int)@$selected_item[0]['total_participants'] : $no_of_clusters);
    if ($limit < 1) { $limit = 1; }
    for ($i = 1; $i <= $limit; $i++) {
        $value_array[$i] = $i;
    }

    $judge_array = array('0' => '0');
    for ($i = 1; $i <= $no_of_judges; $i++) {
        $judge_array[$i] = $i;
    }
    ?>

    <div class="row g-3">
      <!-- Stage -->
      <div class="col-12 col-md-6 col-lg-4">
        <label for="cmbStage" class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
          <i class="bi bi-building text-primary"></i>
          <span>Stage Venue <span class="text-danger">*</span></span>
        </label>
        <?php echo form_dropdown("cmbStage", $stage_array, @$alloted_records[0]['stage_id'], 'id="cmbStage" class="form-select py-2" required'); ?>
      </div>

      <!-- Date -->
      <div class="col-12 col-md-6 col-lg-4">
        <label for="txtDate" class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
          <i class="bi bi-calendar-event text-primary"></i>
          <span>Date <span class="text-danger">*</span></span>
        </label>
        <?php echo form_dropdown("txtDate", @$date_array, $date, 'class="form-select py-2" id="txtDate" required'); ?>
      </div>

      <!-- Time -->
      <div class="col-12 col-md-6 col-lg-4">
        <label class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
          <i class="bi bi-clock text-primary"></i>
          <span>Start Time (HH:MM) <span class="text-danger">*</span></span>
        </label>
        <div class="d-flex align-items-center gap-1">
          <div class="flex-fill">
            <?php echo form_dropdown("txtHour", @$hour_array, ($hour !== '') ? $hour : 'HH', 'class="form-select py-2 font-monospace" id="txtHour" maxlength="2" onfocus="javascript:clearText(\'txtHour\', \'HH\')" onBlur="javascript:clearText(\'txtHour\', \'HH\')" onkeyup="javascript:change_text_max_to_focus(\'txtHour\', \'txtMin\', \'2\')" required'); ?>
          </div>
          <span class="fw-bold text-muted">:</span>
          <div class="flex-fill">
            <?php echo form_dropdown("txtMin", @$min_array, ($minute !== '') ? $minute : 'MM', 'class="form-select py-2 font-monospace" id="txtMin" maxlength="2" onfocus="javascript:clearText(\'txtMin\', \'MM\')" onBlur="javascript:clearText(\'txtMin\', \'MM\')" required'); ?>
          </div>
        </div>
      </div>

      <!-- Clusters -->
      <div class="col-12 col-md-6 col-lg-6">
        <label for="cmbNoOfCluster" class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
          <i class="bi bi-diagram-3 text-primary"></i>
          <span>Number of Clusters <span class="text-danger">*</span></span>
        </label>
        <?php echo form_dropdown("cmbNoOfCluster", $value_array, @$alloted_records[0]['no_of_cluster'], 'id="cmbNoOfCluster" class="form-select py-2 font-monospace" required'); ?>
      </div>

      <!-- Judges -->
      <div class="col-12 col-md-6 col-lg-6">
        <label for="cmbNoOfJudges" class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
          <i class="bi bi-award text-primary"></i>
          <span>Number of Judges <span class="text-danger">*</span></span>
        </label>
        <?php echo form_dropdown("cmbNoOfJudges", $judge_array, (@$alloted_records[0]['no_of_judges']) ? @$alloted_records[0]['no_of_judges'] : '3', 'id="cmbNoOfJudges" class="form-select py-2 font-monospace" required'); ?>
      </div>
    </div>

    <hr class="my-4 text-muted opacity-25">

    <div class="d-flex justify-content-center">
      <?php
      if (isset($is_edit) && $is_edit != 'no') {
          $is_update = (!empty($alloted_records) && count($alloted_records) > 0);
          $button_text = $is_update ? 'Update Stage Allotment' : 'Save Stage Allotment';
          $btn_icon = $is_update ? '<i class="bi bi-arrow-repeat me-1"></i>' : '<i class="bi bi-check-circle-fill me-1"></i>';

          echo form_button(array(
              'name' => 'submit_button',
              'id' => 'submit_button',
              'content' => $btn_icon . ' ' . $button_text,
              'class' => 'btn btn-primary px-4 py-2 rounded-pill shadow-sm fw-semibold d-inline-flex align-items-center gap-2',
              'onClick' => 'javascript:fncCheckAllotmentDeatils(' . ($is_update ? '1' : '0') . ')'
          ));
      }
      ?>
    </div>
  </div>
</div>

<input type="hidden" name="hidItemId" id="hidItemId" value="<?php echo (@$selected_item[0]['item_code']) ? @$selected_item[0]['item_code'] : @$this->input->post('hidItemId'); ?>">
<input type="hidden" name="hidMaxPartcipants" id="hidMaxPartcipants" value="<?php @print($selected_item[0]['total_participants']) ?>">
<input type="hidden" name="hidMaxTime" id="hidMaxTime" value="<?php @print($selected_item[0]['max_time']) ?>">
<input type="hidden" name="hidTimeType" id="hidTimeType" value="<?php @print($selected_item[0]['time_type']) ?>">
<input type="hidden" name="hidNoOfCluster" id="hidNoOfCluster" value="<?php @print(@$alloted_records[0]['no_of_cluster']) ?>">

<?php echo form_close(); ?>

<!-- Cluster Master Details Card -->
<?php if (!empty($cluster_master_details) && count($cluster_master_details) > 0) { ?>
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
  <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-grid-3x3-gap-fill text-primary fs-5"></i>
      <h6 class="mb-0 fw-bold text-dark">Cluster Schedule Overview</h6>
    </div>
    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 font-monospace"><?php echo count($cluster_master_details); ?> Clusters</span>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light border-bottom text-secondary text-uppercase small fw-bold">
          <tr>
            <th class="ps-4 py-3 text-center" style="width: 12%;">Cluster</th>
            <th class="py-3" style="width: 25%;">Stage</th>
            <th class="py-3 text-center" style="width: 18%;">Participants</th>
            <th class="py-3" style="width: 22%;">Start Time</th>
            <th class="pe-4 py-3" style="width: 23%;">End Time</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <?php foreach ($cluster_master_details as $cluster_master) { ?>
            <tr>
              <td class="ps-4 py-3 text-center">
                <span class="badge bg-primary text-white font-monospace px-2.5 py-1">Cluster <?php echo esc($cluster_master['cluster_no']); ?></span>
              </td>
              <td class="py-3 fw-semibold text-dark">
                <i class="bi bi-geo-alt-fill text-danger me-1"></i><?php echo esc($cluster_master['stage_name']); ?>
              </td>
              <td class="py-3 text-center font-monospace">
                <span class="badge bg-light text-dark border px-2.5 py-1">
                  <i class="bi bi-people-fill text-primary me-1"></i><?php echo esc($cluster_master['no_of_participant']); ?>
                </span>
              </td>
              <td class="py-3 font-monospace small text-dark">
                <i class="bi bi-clock-history text-muted me-1"></i><?php echo esc(datetophpmodel($cluster_master['start_time']) . ' ' . timephpmodel($cluster_master['start_time'])); ?>
              </td>
              <td class="pe-4 py-3 font-monospace small text-dark">
                <i class="bi bi-clock-history text-muted me-1"></i><?php echo esc(datetophpmodel($cluster_master['end_time']) . ' ' . timephpmodel($cluster_master['end_time'])); ?>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php } ?>

<!-- Participant Cluster Allotment Card -->
<?php if (!empty($alloted_records) && count($alloted_records) > 0) { ?>
<?php echo form_open('stage/allotment/update_cluster_participant', array('id' => 'formUpdateClusterParticipant', 'name' => 'formUpdateClusterParticipant')); ?>

<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
  <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-person-lines-fill text-primary fs-5"></i>
      <h6 class="mb-0 fw-bold text-dark">Participant Cluster Allotments</h6>
    </div>
    <span class="badge bg-light text-dark border rounded-pill px-3 py-1 font-monospace"><?php echo count($alloted_records); ?> Participants</span>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light border-bottom text-secondary text-uppercase small fw-bold">
          <tr>
            <th class="ps-4 py-3" style="width: 25%;">Registration No.</th>
            <th class="py-3" style="width: 45%;">School Code</th>
            <th class="pe-4 py-3" style="width: 30%;">Assigned Cluster</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <?php
          $value_array = array();
          $limit = (int)@$alloted_records[0]['no_of_cluster'];
          if ($limit < 1) { $limit = 1; }
          for ($i = 1; $i <= $limit; $i++) {
              $value_array[$i] = 'Cluster ' . $i;
          }

          for ($i = 0; $i < count($alloted_records); $i++) {
          ?>
            <tr>
              <td class="ps-4 py-3">
                <span class="badge bg-dark-subtle text-dark border font-monospace px-2.5 py-1">
                  <i class="bi bi-person-badge me-1"></i><?php echo esc($alloted_records[$i]['participant_id']); ?>
                </span>
              </td>
              <td class="py-3 font-monospace fw-semibold text-dark">
                <?php echo esc($alloted_records[$i]['school_code']); ?>
              </td>
              <td class="pe-4 py-3">
                <?php
                echo form_dropdown(
                    "cmbClusterNo_" . $alloted_records[$i]['cp_id'],
                    $value_array,
                    $alloted_records[$i]['cluster_no'],
                    'id="cmbClusterNo_' . $alloted_records[$i]['cp_id'] . '" class="form-select form-select-sm py-1 font-monospace"'
                );
                ?>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <input type="hidden" name="hidClusterItemCode" id="hidClusterItemCode" value="<?php @print($alloted_records[0]['item_code']) ?>">

  <?php if ($this->Session_Model->check_user_permission(3)) { ?>
  <div class="card-footer bg-white p-3 d-flex justify-content-center border-top">
    <?php
    echo form_button(array(
        'name' => 'update_button',
        'id' => 'update_button',
        'content' => '<i class="bi bi-arrow-repeat me-1"></i> Update Participant Clusters',
        'class' => 'btn btn-primary px-4 py-2 rounded-pill shadow-sm fw-semibold d-inline-flex align-items-center gap-2',
        'onClick' => 'javascript:fncUpdateCluster()'
    ));
    ?>
  </div>
  <?php } ?>
</div>

<?php echo form_close(); ?>
<?php } ?>

</div>
