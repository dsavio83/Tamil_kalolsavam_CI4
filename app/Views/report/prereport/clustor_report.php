<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-grid-3x3-gap-fill text-primary"></i> Cluster Reports
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Reports</li>
      <li class="breadcrumb-item active">Cluster Reports</li>
    </ol>
  </nav>
</div>

<div class="row g-4">
  <!-- Card 1: Item Wise Cluster Report -->
  <div class="col-md-12 col-xl-4">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-header bg-primary text-white py-3 px-4 rounded-top-4">
        <h5 class="card-title mb-0 text-white fw-bold d-flex align-items-center gap-2">
          <i class="bi bi-file-earmark-pdf-fill"></i> Item Wise Report
        </h5>
        <small class="text-white-50">Cluster report filtered by festival and item</small>
      </div>

      <div class="card-body p-4">
        <?php echo form_open('report/prereportpdf/clusterreport_report', array('id' => 'formCluItem', 'target' => '_blank', 'class' => 'no-global-loader')); ?>

        <div class="mb-3">
          <label for="cmbFestType" class="form-label fw-semibold text-dark small mb-1">
            Festival Category <span class="text-danger">*</span>
          </label>
          <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-select py-2" id="cmbFestType" required onchange="loadClusterItems(this.value)"'); ?>
        </div>

        <div class="mb-4">
          <label for="cbo_item" class="form-label fw-semibold text-dark small mb-1">
            Item Name <span class="text-danger">*</span>
          </label>
          <div id="divClusterItem">
            <select name="cbo_item" id="cbo_item" class="form-select py-2" required>
              <option value="ALL">All Items</option>
            </select>
          </div>
        </div>

        <div class="d-grid gap-2 mt-auto">
          <button type="submit" class="btn btn-primary rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate PDF Report</span>
          </button>
        </div>

        <?php echo form_close(); ?>
      </div>
    </div>
  </div>

<script>
function loadClusterItems(festId) {
  var itemContainer = document.getElementById('divClusterItem');
  if (!festId || festId == '0') {
    itemContainer.innerHTML = '<select name="cbo_item" id="cbo_item" class="form-select py-2" required><option value="ALL">All Items</option></select>';
    return;
  }
  itemContainer.innerHTML = '<div class="text-center py-2 text-primary small"><span class="spinner-border spinner-border-sm me-1"></span> Loading items...</div>';
  var fd = new FormData();
  fd.append('fest_id', festId);
  fetch('<?php echo base_url(); ?>ajax/loadajax/fetch_item_from_participantt', {
    method: 'POST',
    body: fd
  })
  .then(function(res) { return res.text(); })
  .then(function(html) {
    itemContainer.innerHTML = html;
    var sel = itemContainer.querySelector('select');
    if (sel) sel.className = 'form-select py-2';
  })
  .catch(function(err) {
    itemContainer.innerHTML = '<select name="cbo_item" id="cbo_item" class="form-select py-2" required><option value="ALL">All Items</option></select>';
  });
}
</script>