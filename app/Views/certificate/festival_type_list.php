<div class="pagetitle d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h3 mb-1 text-gray-800 fw-bold d-flex align-items-center gap-2">
      <i class="bi bi-patch-check-fill text-primary"></i> Certificate - Item Wise
    </h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome">Home</a></li>
        <li class="breadcrumb-item">Certificate</li>
        <li class="breadcrumb-item active">Item Wise</li>
      </ol>
    </nav>
  </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm rounded-3 mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 fs-5"></i>
    <div><?php echo session()->getFlashdata('error'); ?></div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php echo form_open('certificate/certificate/list_item_wise', array('id' => 'formIRL')); ?>
<input type="hidden" name="hidUservalue" id="hidUservalue" value="SHOW" />

<div class="row justify-content-center">
  <div class="col-xl-8 col-lg-10 col-12">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
      <div class="card-header bg-white py-3 px-4 border-bottom border-light d-flex align-items-center gap-2">
        <div class="rounded-circle bg-primary-subtle p-2 text-primary d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
          <i class="bi bi-funnel-fill fs-6"></i>
        </div>
        <div>
          <h6 class="mb-0 fw-bold text-dark">Select Festival Category</h6>
          <small class="text-muted">Choose a festival section to view and print item certificates</small>
        </div>
      </div>
      
      <div class="card-body p-4">
        <div class="row g-3 align-items-end justify-content-center">
          <div class="col-md-8 col-12">
            <label for="cmbFestType" class="form-label fw-semibold text-secondary d-flex align-items-center gap-2 mb-2">
              <i class="bi bi-trophy-fill text-warning"></i> Festival Type
            </label>
            <?php 
              $selected_fest = $this->input->post('cmbFestType') ?? '';
              echo form_dropdown("cmbFestType", $fest, $selected_fest, 'class="form-select form-select-lg rounded-3 shadow-none border-secondary-subtle" id="cmbFestType" required'); 
            ?>
          </div>
          <div class="col-md-4 col-12">
            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 d-flex align-items-center justify-content-center gap-2 shadow-sm">
              <i class="bi bi-arrow-right-circle-fill"></i> View Items
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo form_close(); ?>