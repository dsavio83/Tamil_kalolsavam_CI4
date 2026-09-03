<link rel="prefetch" href="<?php echo base_url(); ?>report/prereport/callsheet_first">
<link rel="prefetch" href="<?php echo base_url(); ?>report/prereport/timesheetinterface">
<link rel="prefetch" href="<?php echo base_url(); ?>report/prereport/score_sheet_interfaces">
<link rel="prefetch" href="<?php echo base_url(); ?>report/prereport/four_reports">
<div class="pagetitle mb-3">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="d-flex align-items-center gap-2 text-dark fw-bold mb-1">
        <i class="bi bi-easel2-fill text-primary"></i>
        <span>Stage Reports</span>
      </h1>
      <p class="text-muted small mb-0">Stage-wise reports — call sheet, time sheet, score sheet and tabulation as PDF.</p>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 bg-light px-3 py-1.5 rounded-pill border">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none"><i class="bi bi-house-door-fill me-1"></i>Home</a></li>
        <li class="breadcrumb-item text-muted">Reports</li>
        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Stage Reports</li>
      </ol>
    </nav>
  </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-2">
    <div class="nav nav-pills nav-fill gap-2" id="reportTabs" role="tablist">
      <a class="nav-link rounded-pill text-secondary fw-semibold py-2 px-3 d-flex align-items-center justify-content-center gap-1.5" href="<?php echo base_url(); ?>report/prereport/callsheet_first"><i class="bi bi-person-lines-fill"></i><span>Call Sheet</span></a>
      <a class="nav-link rounded-pill text-secondary fw-semibold py-2 px-3 d-flex align-items-center justify-content-center gap-1.5" href="<?php echo base_url(); ?>report/prereport/timesheetinterface"><i class="bi bi-clock-history"></i><span>Time Sheet</span></a>
      <a class="nav-link rounded-pill text-secondary fw-semibold py-2 px-3 d-flex align-items-center justify-content-center gap-1.5" href="<?php echo base_url(); ?>report/prereport/score_sheet_interfaces"><i class="bi bi-clipboard-check-fill"></i><span>Score Sheet</span></a>
      <a class="nav-link active rounded-pill fw-semibold py-2 px-3 d-flex align-items-center justify-content-center gap-1.5" href="<?php echo base_url(); ?>report/prereport/tabulation_report_interface"><i class="bi bi-table"></i><span>Tabulation</span></a>
      <a class="nav-link rounded-pill text-secondary fw-semibold py-2 px-3 d-flex align-items-center justify-content-center gap-1.5" href="<?php echo base_url(); ?>report/prereport/four_reports"><i class="bi bi-collection-fill"></i><span>All in One</span></a>
    </div>
  </div>
</div>

<script>
(function(){
  var urls = ['<?php echo base_url(); ?>report/prereport/callsheet_first','<?php echo base_url(); ?>report/prereport/timesheetinterface','<?php echo base_url(); ?>report/prereport/score_sheet_interfaces','<?php echo base_url(); ?>report/prereport/four_reports'];
  function pf(u){ try{ var l=document.createElement('link'); l.rel='prefetch'; l.href=u; document.head.appendChild(l);}catch(e){} }
  if('requestIdleCallback' in window) requestIdleCallback(function(){ urls.forEach(pf); },{timeout:2000}); else setTimeout(function(){urls.forEach(pf);},800);
})();
</script>

<div class="row g-4 mb-4">
  <div class="col-12 col-xl-4">
<?php echo form_open('report/prereport/rpt_tabulation_sheet1', array('id' => 'formIWP', 'target' => '_blank', 'class' => 'h-100 needs-spinner')); ?>
    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden d-flex flex-column">
      <div class="card-header bg-primary text-white py-3 px-4 d-flex align-items-center gap-2">
        <i class="bi bi-table fs-5"></i><h6 class="mb-0 fw-bold">Tabulation by Festival</h6>
      </div>
      <div class="card-body p-4 flex-grow-1">
          <label class="form-label fw-bold small"><i class="bi bi-trophy text-warning me-1"></i>Festival <span class="text-danger">*</span></label>
          <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-select py-2" id="cmbFestType" required'); ?>
      </div>
      <div class="card-footer bg-white border-top p-3">
        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-spinner" onclick="return notSelected(this)">
          <i class="bi bi-file-earmark-pdf-fill"></i><span class="btn-label">View PDF</span><span class="spinner-border spinner-border-sm d-none"></span>
        </button>
      </div>
    </div>
<?php echo form_close(); ?>
  </div>
