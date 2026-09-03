
<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-list-task text-primary"></i> Item Result List
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Results</li>
      <li class="breadcrumb-item active">Item Result List</li>
    </ol>
  </nav>
</div>

<div class="row justify-content-center mb-4">
  <div class="col-md-8 col-lg-6 col-xl-5">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-header bg-primary text-white py-3 px-4 border-0 rounded-top-4 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: rgba(255, 255, 255, 0.2);">
            <i class="bi bi-trophy-fill text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Select Festival Category</h5>
            <small class="text-white-50" style="font-size: 0.8rem;">View event-wise completion and confirmation status</small>
          </div>
        </div>
      </div>

      <div class="card-body p-4">
        <?php echo form_open('result/resultentry/item_result_list', array('id' => 'formIRL')); ?>
        <input type="hidden" name="hidUservalue" id="hidUservalue" value="SHOW" />

        <div class="mb-4">
          <label for="cmbFestType" class="form-label fw-bold text-dark small mb-1">
            <i class="bi bi-trophy text-warning me-1"></i> Festival Category <span class="text-danger">*</span>
          </label>
          <?php echo form_dropdown("cmbFestType", $fest, $this->input->post('cmbFestType'), 'class="form-select py-2 rounded-3 shadow-none border-secondary-subtle" id="cmbFestType" required'); ?>
        </div>

        <div class="d-grid">
          <button type="submit" class="btn btn-primary rounded-pill py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-search"></i>
            <span>View Result Status</span>
          </button>
        </div>

        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>