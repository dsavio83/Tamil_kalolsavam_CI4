  <div class="col-12 col-xl-4">
<?php echo form_open('report/prereportpdf/fest_scoresheet_details', array('id' => 'formPWDFest', 'target' => '_blank', 'class' => 'h-100 needs-spinner')); ?>
    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden d-flex flex-column">
        <div class="card-header bg-primary text-white py-3 px-4 d-flex align-items-center gap-2">
            <i class="bi bi-trophy-fill fs-5"></i><h6 class="mb-0 fw-bold">Score Sheet by Festival</h6>
        </div>
        <div class="card-body p-4 flex-grow-1">
            <label class="form-label fw-bold small"><i class="bi bi-flag-fill text-warning me-1"></i>Festival <span class="text-danger">*</span></label>
            <?php echo form_dropdown("txtfestFrom", $fest, '', 'class="form-select py-2" id="txtfestFromScore" required'); ?>
        </div>
        <div class="card-footer bg-white border-top p-3">
            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-spinner">
                <i class="bi bi-file-earmark-pdf-fill"></i><span class="btn-label">View PDF</span><span class="spinner-border spinner-border-sm d-none"></span>
            </button>
        </div>
    </div>
<?php echo form_close(); ?>
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
