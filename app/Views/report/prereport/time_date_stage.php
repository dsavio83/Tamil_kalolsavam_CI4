  <div class="col-12 col-xl-4">
<?php echo form_open('report/prereportpdf/time_date_stage', array('id' => 'cluTime', 'target' => '_blank', 'class' => 'h-100 needs-spinner')); ?>
    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden d-flex flex-column">
        <div class="card-header bg-primary text-white py-3 px-4 d-flex align-items-center gap-2">
            <i class="bi bi-calendar2-range-fill fs-5"></i><h6 class="mb-0 fw-bold">Time Sheet by Date &amp; Stage</h6>
        </div>
        <div class="card-body p-4 flex-grow-1">
            <div class="mb-3">
                <label class="form-label fw-bold small"><i class="bi bi-calendar-event text-primary me-1"></i>Date <span class="text-danger">*</span></label>
                <?php echo form_dropdown("txtDate", $date_array, '', 'class="form-select py-2" id="txtDate3" required'); ?>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold small"><i class="bi bi-building text-primary me-1"></i>Stage <span class="text-danger">*</span></label>
                <?php echo form_dropdown("cmbstage", $stage, '', 'class="form-select py-2" id="cmbstage3" required'); ?>
            </div>
        </div>
        <div class="card-footer bg-white border-top p-3">
            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-spinner">
                <i class="bi bi-file-earmark-pdf-fill"></i><span class="btn-label">View PDF</span><span class="spinner-border spinner-border-sm d-none"></span>
            </button>
        </div>
    </div>
<?php echo form_close(); ?>
  </div>
</div>
<script>
document.querySelectorAll('form.needs-spinner').forEach(function(f){
  f.addEventListener('submit', function(){
    var b=f.querySelector('.btn-spinner'); if(!b) return;
    var s=b.querySelector('.spinner-border'), lb=b.querySelector('.btn-label');
    if(s) s.classList.remove('d-none'); b.disabled=true; if(lb) lb.textContent='Generating...';
    setTimeout(function(){ if(s) s.classList.add('d-none'); b.disabled=false; if(lb) lb.textContent='View PDF'; }, 4000);
  });
});
</script>
