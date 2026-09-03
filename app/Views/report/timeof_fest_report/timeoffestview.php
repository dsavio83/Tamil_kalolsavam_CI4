<?php echo form_open("report/timefestreportpdf/confidential", ["id" => "resreport", "target" => "_blank", "class" => "d-none"]); ?>
<input type="hidden" name="hiditemcode" id="hiditemcode" value="" />
<input type="hidden" name="hidFestcode" id="hidFestcode" value="<?php echo htmlspecialchars((string)@$fest); ?>" />
<?php echo form_close(); ?>

<div class="row justify-content-center mt-4">
  <div class="col-lg-10 col-xl-9">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
      
      <!-- Card Header -->
      <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
          <div class="bg-primary-subtle text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi bi-card-checklist fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 fw-bold fs-6 text-dark">
              <?php echo htmlspecialchars(@$resultdet[0]["fest_name"] ?? 'Festival Results'); ?>
            </h5>
            <small class="text-muted">
              <?php echo count($resultdet); ?> declared <?php echo (count($resultdet) === 1) ? 'item' : 'items'; ?> found
            </small>
          </div>
        </div>

        <div>
          <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" onclick="printAllConfidential();">
            <i class="bi bi-printer-fill"></i>
            <span>Print All Results (PDF)</span>
          </button>
        </div>
      </div>

      <!-- Card Body / Table -->
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted text-uppercase small">
              <tr>
                <th scope="col" class="ps-4 py-3" style="width: 80px;">Sl. No</th>
                <th scope="col" class="py-3" style="width: 140px;">Item Code</th>
                <th scope="col" class="py-3">Competition Item Name</th>
                <th scope="col" class="py-3 text-center" style="width: 130px;">Item Type</th>
                <th scope="col" class="pe-4 py-3 text-end" style="width: 150px;">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $sl = 1;
              foreach ($resultdet as $value): 
                $isSingle = ($value["item_type"] == "S");
                $badgeClass = $isSingle ? "bg-info-subtle text-info-emphasis" : "bg-warning-subtle text-warning-emphasis";
                $typeText = $isSingle ? "Single" : "Group";
                $typeIcon = $isSingle ? "bi-person" : "bi-people";
              ?>
              <tr>
                <td class="ps-4 fw-semibold text-muted small"><?php echo $sl++; ?></td>
                <td>
                  <span class="badge bg-light text-dark border font-monospace px-2.5 py-1.5 fw-semibold fs-7">
                    <?php echo htmlspecialchars($value["item_code"]); ?>
                  </span>
                </td>
                <td class="fw-medium text-dark">
                  <span class="font-tamil" style="font-family: 'TAU-Pallai', 'Segoe UI', sans-serif; font-size: 14px;">
                    <?php echo htmlspecialchars($value["item_name"]); ?>
                  </span>
                </td>
                <td class="text-center">
                  <span class="badge <?php echo $badgeClass; ?> rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 small">
                    <i class="bi <?php echo $typeIcon; ?>"></i>
                    <span><?php echo $typeText; ?></span>
                  </span>
                </td>
                <td class="pe-4 text-end">
                  <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1 shadow-sm" onclick="printItemConfidential('<?php echo htmlspecialchars($value['item_code']); ?>');">
                    <i class="bi bi-file-earmark-pdf"></i>
                    <span>Print PDF</span>
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Footer action -->
      <div class="card-footer bg-light py-3 px-4 border-top d-flex align-items-center justify-content-between">
        <span class="text-muted small">
          <i class="bi bi-shield-check text-success me-1"></i> Official Confidential Tabulation
        </span>
        <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm d-inline-flex align-items-center gap-2" onclick="printAllConfidential();">
          <i class="bi bi-printer-fill"></i>
          <span>Batch Print All Results</span>
        </button>
      </div>

    </div>
  </div>
</div>

<script>
function printItemConfidential(itemCode) {
  var form = document.getElementById('resreport');
  var input = document.getElementById('hiditemcode');
  if (form && input) {
    input.value = itemCode;
    form.submit();
  }
}

function printAllConfidential() {
  var form = document.getElementById('resreport');
  var input = document.getElementById('hiditemcode');
  if (form && input) {
    input.value = 'ALL';
    form.submit();
  }
}
</script>
