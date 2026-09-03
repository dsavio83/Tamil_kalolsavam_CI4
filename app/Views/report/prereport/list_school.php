<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-buildings-fill text-primary"></i> Participating Schools Report
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Reports</li>
      <li class="breadcrumb-item active">Participating Schools</li>
    </ol>
  </nav>
</div>

<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6 col-xl-5">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-header bg-primary text-white py-3 px-4 rounded-top-4">
        <h5 class="card-title mb-0 text-white fw-bold d-flex align-items-center gap-2">
          <i class="bi bi-file-earmark-pdf-fill"></i> Participating Schools
        </h5>
      </div>

      <div class="card-body p-4">
        <?php echo form_open('report/prereportpdf/list_school', array('id' => 'formListSchool', 'target' => '_blank', 'class' => 'no-global-loader')); ?>
        
        <div class="mb-4">
          <label for="txtfestFrom" class="form-label fw-semibold text-dark small mb-2">
            Select Festival Category <span class="text-danger">*</span>
          </label>
          <select name="txtfestFrom" id="txtfestFrom" class="form-select form-select-lg py-2" required>
            <option value="ALL">All Categories (Combined Report)</option>
            <?php
            if (!empty($retdat)) {
                foreach ($retdat as $fest) {
                    echo '<option value="' . htmlspecialchars($fest['fest_id']) . '">' . htmlspecialchars($fest['fest_name']) . '</option>';
                }
            }
            ?>
          </select>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary rounded-pill py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate PDF Report</span>
          </button>
          <a href="<?php echo base_url(); ?>report/prereport/list_school_with_team" target="_blank" class="btn btn-outline-secondary rounded-pill py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-telephone-fill"></i>
            <span>View School Contacts &amp; Teams (PDF)</span>
          </a>
        </div>

        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>
