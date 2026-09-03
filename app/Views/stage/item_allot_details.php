<?php
echo form_open(base_url() . 'stage/item_participant/stage_allot_fest_all', array('id' => 'formIWPq'));
$total_single = count($single);
$total_group = count($itempart);
$total_items = $total_single + $total_group;
$fest_title = @$itempart[0]['fest_name'] ? @$itempart[0]['fest_name'] : (@$single[0]['fest_name'] ? @$single[0]['fest_name'] : 'Festival');
?>
<input type="hidden" name="txtItemCode" id="txtItemCode" value="" />
<input type="hidden" name="txtFestId" id="txtFestId" value="<?php echo $fest_id; ?>" />

<!-- Festival Info & Summary Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
  <div class="card-body p-4">
    <div class="row align-items-center g-3">
      <div class="col-12 col-lg-6">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; min-width: 52px;">
            <i class="bi bi-stars fs-3"></i>
          </div>
          <div>
            <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 mb-1 font-monospace">Allotment Workspace</span>
            <h4 class="fw-bold text-dark m-0"><?php echo esc($fest_title); ?></h4>
          </div>
        </div>
      </div>
      
      <div class="col-12 col-lg-6">
        <div class="d-flex align-items-center justify-content-lg-end gap-2 flex-wrap">
          <div class="px-3 py-2 bg-light rounded-3 border text-center">
            <div class="small text-muted fw-semibold">Total Items</div>
            <div class="fs-5 fw-bold text-dark font-monospace"><?php echo $total_items; ?></div>
          </div>
          <div class="px-3 py-2 bg-primary-subtle rounded-3 border border-primary-subtle text-center">
            <div class="small text-primary fw-semibold">Single Items</div>
            <div class="fs-5 fw-bold text-primary font-monospace"><?php echo $total_single; ?></div>
          </div>
          <div class="px-3 py-2 bg-info-subtle rounded-3 border border-info-subtle text-center">
            <div class="small text-info fw-semibold">Group Items</div>
            <div class="fs-5 fw-bold text-info font-monospace"><?php echo $total_group; ?></div>
          </div>
        </div>
      </div>
    </div>

    <hr class="my-3 text-muted opacity-25">

    <!-- Live Search Filter & Batch Quick Fill -->
    <div class="row g-2 align-items-center justify-content-between">
      <div class="col-12 col-md-5 col-lg-4">
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
          <input type="text" id="itemTableSearch" class="form-control border-start-0" placeholder="Quick search item name or code..." autocomplete="off">
        </div>
      </div>
      <div class="col-12 col-md-7 col-lg-8 text-md-end">
        <span class="text-muted small me-2"><i class="bi bi-info-circle me-1"></i>Click an item name to open individual stage configuration.</span>
      </div>
    </div>
  </div>
</div>

<!-- Allotment Table Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
  <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-table text-primary fs-5"></i>
      <h6 class="mb-0 fw-bold text-dark">Stage Allotment Matrix</h6>
    </div>
    <div class="small text-muted">
      Showing <span id="visibleCount" class="fw-bold text-dark"><?php echo $total_items; ?></span> of <?php echo $total_items; ?> events
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" id="allotmentTable">
        <thead class="table-light border-bottom text-secondary text-uppercase small fw-bold">
          <tr>
            <th scope="col" style="width: 25%; min-width: 220px;" class="ps-4 py-3">Item Details</th>
            <th scope="col" style="width: 9%; min-width: 80px;" class="text-center py-3">Participants</th>
            <th scope="col" style="width: 28%; min-width: 270px;" class="py-3">Date & Time (HH:MM)</th>
            <th scope="col" style="width: 20%; min-width: 170px;" class="py-3">Stage Venue</th>
            <th scope="col" style="width: 9%; min-width: 90px;" class="text-center py-3">Clusters</th>
            <th scope="col" style="width: 9%; min-width: 90px;" class="text-center pe-4 py-3">Judges</th>
          </tr>
        </thead>
        <tbody class="divide-y">

        <?php
        $judge_array = array();
        for ($i = 1; $i <= $no_of_judges; $i++) {
            $judge_array[$i] = $i;
        }

        $stage_array = array();
        for ($i = 0; $i < count($stages); $i++) {
            $stage_array[$stages[$i]['stage_id']] = $stages[$i]['stage_name'];
        }

        /* 1. SINGLE ITEMS */
        for ($j = 0; $j < count($single); $j++) {
            $item_code = $single[$j]['item_code'];
            $date = date('Y-m-d', strtotime($single[$j]['start_time']));
            $hour = date('H', strtotime($single[$j]['start_time']));
            $min = date('i', strtotime($single[$j]['start_time']));

            $value_array = array();
            $limit = (@$single[$j]['is_off_stage'] == 'Y') ? 1 : (($no_of_clusters > @$single[$j]['cpt']) ? @$single[$j]['cpt'] : $no_of_clusters);
            for ($i = 1; $i <= $limit; $i++) {
                $value_array[$i] = $i;
            }
        ?>
          <tr class="allot-row" data-search="<?php echo strtolower($single[$j]['item_code'] . ' ' . $single[$j]['item_name']); ?>">
            <td class="ps-4 py-3">
              <input type="hidden" name="hidMaxPartcipants<?php echo $item_code; ?>" id="hidMaxPartcipants<?php echo $item_code; ?>" value="<?php echo $single[$j]['cpt']; ?>" />
              <div class="d-flex align-items-start gap-2">
                <span class="badge bg-dark-subtle text-dark border font-monospace px-2 py-1 flex-shrink-0"><?php echo esc($single[$j]['item_code']); ?></span>
                <div>
                  <a href="javascript:void(0)" onclick="getitemstagedet('<?php echo $single[$j]['item_code']; ?>')" class="text-primary fw-semibold text-decoration-none d-block item-title-link" title="Click to view/edit individual stage details">
                    <?php echo esc($single[$j]['item_name']); ?>
                  </a>
                  <div class="d-flex align-items-center gap-1 mt-1">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 11px;">Single</span>
                    <?php if (@$single[$j]['is_off_stage'] == 'Y') { ?>
                      <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size: 11px;">Off Stage</span>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </td>

            <td class="text-center py-3">
              <span class="badge bg-light text-dark border px-2.5 py-1.5 font-monospace fs-6">
                <i class="bi bi-person-fill text-primary me-1"></i><?php echo $single[$j]['cpt']; ?>
              </span>
            </td>

            <td class="py-3">
              <div class="d-flex align-items-center gap-1">
                <div class="flex-grow-1">
                  <?php echo form_dropdown('txtDate' . $item_code, $date_array, $date, 'id="txtDate' . $item_code . '" class="form-select form-select-sm shadow-none"'); ?>
                </div>
                <div style="width: 72px;">
                  <?php echo form_dropdown('txtHour' . $item_code, @$hour_array, $hour, 'class="form-select form-select-sm shadow-none font-monospace" id="txtHour' . $item_code . '" title="Hour"'); ?>
                </div>
                <span class="text-muted fw-bold">:</span>
                <div style="width: 72px;">
                  <?php echo form_dropdown('txtMin' . $item_code, @$min_array, $min, 'class="form-select form-select-sm shadow-none font-monospace" id="txtMin' . $item_code . '" title="Minute"'); ?>
                </div>
              </div>
            </td>

            <td class="py-3">
              <?php
              $attributes = 'class="form-select form-select-sm shadow-none" id="cmbStage' . $item_code . '"';
              echo form_dropdown('cmbStage' . $item_code, $stage_array, @$single[$j]['stage_id'], $attributes);
              ?>
            </td>

            <td class="text-center py-3">
              <?php echo form_dropdown('cmbNoOfCluster' . $item_code, $value_array, @$single[$j]['no_of_cluster'], 'id="cmbNoOfCluster' . $item_code . '" class="form-select form-select-sm shadow-none text-center font-monospace"'); ?>
            </td>

            <td class="text-center pe-4 py-3">
              <?php echo form_dropdown('cmbNoOfJudges' . $item_code, $judge_array, (@$single[$j]['no_of_judges']) ? @$single[$j]['no_of_judges'] : '3', 'id="cmbNoOfJudges' . $item_code . '" class="form-select form-select-sm shadow-none text-center font-monospace"'); ?>
            </td>
          </tr>
        <?php } ?>

        /* 2. GROUP ITEMS */
        <?php
        for ($j = 0; $j < count($itempart); $j++) {
            $item_code = $itempart[$j]['item_code'];
            $date = date('Y-m-d', strtotime($itempart[$j]['start_time']));
            $hour = date('H', strtotime($itempart[$j]['start_time']));
            $min = date('i', strtotime($itempart[$j]['start_time']));

            $value_array = array();
            $limit = (@$itempart[$j]['is_off_stage'] == 'Y') ? 1 : (($no_of_clusters > @$itempart[$j]['cpt']) ? @$itempart[$j]['cpt'] : $no_of_clusters);
            for ($i = 1; $i <= $limit; $i++) {
                $value_array[$i] = $i;
            }
        ?>
          <tr class="allot-row" data-search="<?php echo strtolower($itempart[$j]['item_code'] . ' ' . $itempart[$j]['item_name']); ?>">
            <td class="ps-4 py-3">
              <input type="hidden" name="hidMaxPartcipants<?php echo $item_code; ?>" id="hidMaxPartcipants<?php echo $item_code; ?>" value="<?php echo $itempart[$j]['cpt']; ?>" />
              <div class="d-flex align-items-start gap-2">
                <span class="badge bg-dark-subtle text-dark border font-monospace px-2 py-1 flex-shrink-0"><?php echo esc($itempart[$j]['item_code']); ?></span>
                <div>
                  <a href="javascript:void(0)" onclick="getitemstagedet('<?php echo $itempart[$j]['item_code']; ?>')" class="text-primary fw-semibold text-decoration-none d-block item-title-link" title="Click to view/edit individual stage details">
                    <?php echo esc($itempart[$j]['item_name']); ?>
                  </a>
                  <div class="d-flex align-items-center gap-1 mt-1">
                    <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 11px;">Group</span>
                    <?php if (@$itempart[$j]['is_off_stage'] == 'Y') { ?>
                      <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size: 11px;">Off Stage</span>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </td>

            <td class="text-center py-3">
              <span class="badge bg-light text-dark border px-2.5 py-1.5 font-monospace fs-6">
                <i class="bi bi-people-fill text-info me-1"></i><?php echo $itempart[$j]['cpt']; ?>
              </span>
            </td>

            <td class="py-3">
              <div class="d-flex align-items-center gap-1">
                <div class="flex-grow-1">
                  <?php echo form_dropdown('txtDate' . $item_code, $date_array, $date, 'id="txtDate' . $item_code . '" class="form-select form-select-sm shadow-none"'); ?>
                </div>
                <div style="width: 72px;">
                  <?php echo form_dropdown('txtHour' . $item_code, @$hour_array, $hour, 'class="form-select form-select-sm shadow-none font-monospace" id="txtHour' . $item_code . '" title="Hour"'); ?>
                </div>
                <span class="text-muted fw-bold">:</span>
                <div style="width: 72px;">
                  <?php echo form_dropdown('txtMin' . $item_code, @$min_array, $min, 'class="form-select form-select-sm shadow-none font-monospace" id="txtMin' . $item_code . '" title="Minute"'); ?>
                </div>
              </div>
            </td>

            <td class="py-3">
              <?php
              $attributes = 'class="form-select form-select-sm shadow-none" id="cmbStage' . $item_code . '"';
              echo form_dropdown('cmbStage' . $item_code, $stage_array, @$itempart[$j]['stage_id'], $attributes);
              ?>
            </td>

            <td class="text-center py-3">
              <?php echo form_dropdown('cmbNoOfCluster' . $item_code, $value_array, @$itempart[$j]['no_of_cluster'], 'id="cmbNoOfCluster' . $item_code . '" class="form-select form-select-sm shadow-none text-center font-monospace"'); ?>
            </td>

            <td class="text-center pe-4 py-3">
              <?php echo form_dropdown('cmbNoOfJudges' . $item_code, $judge_array, (@$itempart[$j]['no_of_judges']) ? @$itempart[$j]['no_of_judges'] : '3', 'id="cmbNoOfJudges' . $item_code . '" class="form-select form-select-sm shadow-none text-center font-monospace"'); ?>
            </td>
          </tr>
        <?php } ?>

        </tbody>
      </table>
    </div>
  </div>

  <!-- Table Card Footer Action -->
  <div class="card-footer bg-white p-3 d-flex align-items-center justify-content-between flex-wrap gap-2 border-top">
    <div class="text-muted small">
      <i class="bi bi-shield-check text-success me-1"></i>Ensure dates, stages, and judges are correctly assigned before saving.
    </div>
    <div>
      <button type="submit" name="Allotment" value="Allotment" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-2 fw-semibold" onclick="showFormSpinner(this, 'Saving Allotments...')">
        <i class="bi bi-check2-circle fs-5"></i>
        <span>Save Allotment</span>
      </button>
    </div>
  </div>
</div>

<!-- Sticky Bottom Floating Action Bar -->
<div class="position-sticky bottom-0 start-0 end-0 bg-white border-top shadow-lg py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4" style="z-index: 990; margin-top: -30px;">
  <div class="d-flex align-items-center gap-2">
    <span class="badge bg-primary rounded-circle p-2"><i class="bi bi-check-all fs-6"></i></span>
    <span class="fw-semibold text-dark d-none d-sm-inline">Allotment Ready</span>
    <span class="text-muted small">(<?php echo $total_items; ?> Items)</span>
  </div>
  <div class="d-flex align-items-center gap-2">
    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
      <i class="bi bi-arrow-up-circle me-1"></i> Top
    </button>
    <button type="submit" name="Allotment" value="Allotment" class="btn btn-success px-4 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-2 fw-bold" onclick="showFormSpinner(this, 'Saving Allotments...')">
      <i class="bi bi-floppy2-fill"></i>
      <span>Save Stage Allotments</span>
    </button>
  </div>
</div>

<?php echo form_close(); ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
  var searchInput = document.getElementById("itemTableSearch");
  var visibleCountEl = document.getElementById("visibleCount");
  var rows = document.querySelectorAll("#allotmentTable tbody tr.allot-row");

  if (searchInput) {
    searchInput.addEventListener("input", function() {
      var query = this.value.trim().toLowerCase();
      var count = 0;

      rows.forEach(function(row) {
        var text = row.getAttribute("data-search") || row.textContent.toLowerCase();
        if (!query || text.indexOf(query) !== -1) {
          row.style.display = "";
          count++;
        } else {
          row.style.display = "none";
        }
      });

      if (visibleCountEl) {
        visibleCountEl.textContent = count;
      }
    });
  }
});
</script>

<style>
.item-title-link:hover {
  text-decoration: underline !important;
  color: #0d6efd !important;
}
#allotmentTable tbody tr:hover {
  background-color: #f8fafc;
}
</style>

