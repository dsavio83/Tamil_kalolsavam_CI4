<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-clock-history text-primary"></i> Time wise Report
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Result Reports</li>
      <li class="breadcrumb-item active">Time wise &amp; Result Number Reports</li>
    </ol>
  </nav>
</div>

<div class="row g-4">
  <!-- Card 1: Time Window Filter -->
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
      <div class="card-header bg-primary text-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-calendar2-range-fill text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Time Window Filter</h5>
            <small class="text-white-50">Results finalized within date/time interval</small>
          </div>
        </div>
        <span class="badge bg-white text-primary fw-bold shadow-sm px-2.5 py-1.5 rounded-pill small">
          <i class="bi bi-printer-fill me-1"></i> PDF
        </span>
      </div>

      <div class="card-body p-4 d-flex flex-column justify-content-between">
        <?php echo form_open('district/report/afterresult_reportpdf/time_wise_result_report', array('id' => 'formTimeWise', 'target' => '_blank', 'class' => 'needs-validation')); ?>
        
        <?php
        $time_hours = [
          '1' => '1:00', '2' => '2:00', '3' => '3:00', '4' => '4:00',
          '5' => '5:00', '6' => '6:00', '7' => '7:00', '8' => '8:00',
          '9' => '9:00', '10' => '10:00', '11' => '11:00', '12' => '12:00'
        ];
        $time_ampm = ['AM' => 'AM', 'PM' => 'PM'];
        ?>

        <!-- From Date & Time -->
        <div class="mb-3">
          <label class="form-label fw-semibold text-dark small mb-2 d-flex align-items-center gap-1.5">
            <i class="bi bi-calendar-event text-primary"></i>
            <span>From Date &amp; Time</span>
          </label>
          <div class="row g-2">
            <div class="col-6">
              <?php echo form_dropdown('txtfromDate', $date_array, '', 'id="txtfromDate" class="form-select py-2 fs-6" required'); ?>
            </div>
            <div class="col-3">
              <?php echo form_dropdown('txtfromTime', $time_hours, '12', 'id="txtfromTime" class="form-select py-2 fs-6"'); ?>
            </div>
            <div class="col-3">
              <?php echo form_dropdown('txtfromampm', $time_ampm, 'AM', 'id="txtfromampm" class="form-select py-2 fs-6"'); ?>
            </div>
          </div>
        </div>

        <!-- To Date & Time -->
        <div class="mb-4">
          <label class="form-label fw-semibold text-dark small mb-2 d-flex align-items-center gap-1.5">
            <i class="bi bi-calendar-check text-primary"></i>
            <span>To Date &amp; Time</span>
          </label>
          <div class="row g-2">
            <div class="col-6">
              <?php echo form_dropdown('txttoDate', $date_array, '', 'id="txttoDate" class="form-select py-2 fs-6" required'); ?>
            </div>
            <div class="col-3">
              <?php echo form_dropdown('txttoTime', $time_hours, '11', 'id="txttoTime" class="form-select py-2 fs-6"'); ?>
            </div>
            <div class="col-3">
              <?php echo form_dropdown('txttoampm', $time_ampm, 'PM', 'id="txttoampm" class="form-select py-2 fs-6"'); ?>
            </div>
          </div>
          <div class="form-text mt-2 text-muted small">
            <i class="bi bi-info-circle text-primary me-1"></i> Finalized results published within this window will be included.
          </div>
        </div>

        <div class="pt-3 border-top mt-auto text-end">
          <button type="submit" name="btnSubmit" id="btnSubmitTime" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm d-inline-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate Time Report</span>
          </button>
        </div>

        <input type="hidden" name="hidUserId" id="hidUserId" value="<?php echo htmlspecialchars((string)$this->session->get('USERID')); ?>" />
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
