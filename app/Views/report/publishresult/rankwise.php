<style>
  .rankwise-card {
    border: 1px solid rgba(99, 102, 241, 0.15);
    background: #ffffff;
    box-shadow: 0 10px 30px -6px rgba(30, 41, 59, 0.08), 0 4px 12px -2px rgba(30, 41, 59, 0.04);
    border-radius: 1.25rem;
    overflow: hidden;
  }
  .rankwise-header {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 45%, #4338ca 100%);
    padding: 1.1rem 1.5rem;
  }
  .rank-pill-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 0.9rem;
    padding: 0.7rem 0.4rem;
    text-align: center;
    cursor: pointer;
    background: #ffffff;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    user-select: none;
  }
  .rank-pill-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px -4px rgba(0, 0, 0, 0.08);
  }
  /* Card: ALL */
  .rank-pill-card.card-all .rank-icon-box {
    background: #ede9fe;
    color: #6366f1;
  }
  .rank-pill-card.card-all.active {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    border-color: #4f46e5;
    color: #ffffff !important;
    box-shadow: 0 8px 18px -4px rgba(79, 70, 229, 0.45);
  }
  .rank-pill-card.card-all.active .rank-icon-box {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
  }
  .rank-pill-card.card-all.active .rank-label,
  .rank-pill-card.card-all.active .rank-sub {
    color: #ffffff !important;
  }

  /* Card: 1st Rank (Gold) */
  .rank-pill-card.card-1 .rank-icon-box {
    background: #fef3c7;
    color: #d97706;
  }
  .rank-pill-card.card-1.active {
    background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
    border-color: #d97706;
    color: #ffffff !important;
    box-shadow: 0 8px 18px -4px rgba(245, 158, 11, 0.45);
  }
  .rank-pill-card.card-1.active .rank-icon-box {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
  }
  .rank-pill-card.card-1.active .rank-label,
  .rank-pill-card.card-1.active .rank-sub {
    color: #ffffff !important;
  }

  /* Card: 2nd Rank (Silver) */
  .rank-pill-card.card-2 .rank-icon-box {
    background: #f1f5f9;
    color: #64748b;
  }
  .rank-pill-card.card-2.active {
    background: linear-gradient(135deg, #475569 0%, #94a3b8 100%);
    border-color: #64748b;
    color: #ffffff !important;
    box-shadow: 0 8px 18px -4px rgba(100, 116, 139, 0.45);
  }
  .rank-pill-card.card-2.active .rank-icon-box {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
  }
  .rank-pill-card.card-2.active .rank-label,
  .rank-pill-card.card-2.active .rank-sub {
    color: #ffffff !important;
  }

  /* Card: 3rd Rank (Bronze) */
  .rank-pill-card.card-3 .rank-icon-box {
    background: #ffedd5;
    color: #ea580c;
  }
  .rank-pill-card.card-3.active {
    background: linear-gradient(135deg, #c2410c 0%, #ea580c 100%);
    border-color: #c2410c;
    color: #ffffff !important;
    box-shadow: 0 8px 18px -4px rgba(234, 88, 12, 0.45);
  }
  .rank-pill-card.card-3.active .rank-icon-box {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
  }
  .rank-pill-card.card-3.active .rank-label,
  .rank-pill-card.card-3.active .rank-sub {
    color: #ffffff !important;
  }

  .rank-icon-box {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    margin: 0 auto 0.35rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    transition: all 0.2s ease;
  }
  .btn-gradient-submit {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    border: none;
    color: #ffffff;
    font-weight: 600;
    padding: 0.65rem 1.75rem;
    border-radius: 2rem;
    box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.35);
    transition: all 0.2s ease;
  }
  .btn-gradient-submit:hover {
    background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px 0 rgba(79, 70, 229, 0.45);
  }
</style>

<div class="row justify-content-center py-2">
  <div class="col-12 col-md-9 col-lg-7 col-xl-6">
    <div class="rankwise-card">
      
      <!-- Card Header -->
      <div class="rankwise-header text-white d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-white bg-opacity-20 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi bi-trophy-fill text-warning fs-5"></i>
          </div>
          <div>
            <h5 class="mb-0 fw-bold fs-6 text-white">Rank Wise Result</h5>
          </div>
        </div>
        <span class="badge bg-white bg-opacity-20 text-white fw-semibold px-2.5 py-1.5 rounded-pill small d-inline-flex align-items-center gap-1.5">
          <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
          <span>PDF</span>
        </span>
      </div>

      <!-- Card Body -->
      <div class="p-4">
        <?php echo form_open('report/resultindex/rankwise_report', array('id' => 'formRankWise', 'target' => '_blank', 'onsubmit' => 'return validateRankWise();')); ?>
        
        <!-- Festival Category -->
        <div class="mb-3.5">
          <label for="cmbFestType" class="form-label fw-bold text-dark small mb-2 d-flex align-items-center gap-2">
            <i class="bi bi-stars text-primary fs-6"></i>
            <span>Festival Category</span>
            <span class="text-danger">*</span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-primary px-3"><i class="bi bi-calendar-event"></i></span>
            <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-select border-start-0 py-2 fw-semibold fs-6" id="cmbFestType" required'); ?>
          </div>
        </div>

        <!-- Rank Position Selector (Colorful Interactive Cards) -->
        <div class="mb-4 mt-3">
          <label class="form-label fw-bold text-dark small mb-2 d-flex align-items-center gap-2">
            <i class="bi bi-award-fill text-warning fs-6"></i>
            <span>Select Rank</span>
          </label>
          
          <div class="row g-2.5">
            <!-- All Ranks -->
            <div class="col-3">
              <div class="rank-pill-card card-all active" onclick="selectRank('ALL', this);">
                <div class="rank-icon-box">
                  <i class="bi bi-trophy-fill"></i>
                </div>
                <div class="rank-label fw-bold small text-dark">ALL</div>
                <div class="rank-sub text-muted" style="font-size: 0.72rem;">Ranks</div>
              </div>
            </div>

            <!-- 1st Rank -->
            <div class="col-3">
              <div class="rank-pill-card card-1" onclick="selectRank('1', this);">
                <div class="rank-icon-box">
                  <i class="bi bi-1-circle-fill"></i>
                </div>
                <div class="rank-label fw-bold small text-dark">1st</div>
                <div class="rank-sub text-muted" style="font-size: 0.72rem;">Rank</div>
              </div>
            </div>

            <!-- 2nd Rank -->
            <div class="col-3">
              <div class="rank-pill-card card-2" onclick="selectRank('2', this);">
                <div class="rank-icon-box">
                  <i class="bi bi-2-circle-fill"></i>
                </div>
                <div class="rank-label fw-bold small text-dark">2nd</div>
                <div class="rank-sub text-muted" style="font-size: 0.72rem;">Rank</div>
              </div>
            </div>

            <!-- 3rd Rank -->
            <div class="col-3">
              <div class="rank-pill-card card-3" onclick="selectRank('3', this);">
                <div class="rank-icon-box">
                  <i class="bi bi-3-circle-fill"></i>
                </div>
                <div class="rank-label fw-bold small text-dark">3rd</div>
                <div class="rank-sub text-muted" style="font-size: 0.72rem;">Rank</div>
              </div>
            </div>
          </div>
          
          <!-- Hidden input submitted with the form -->
          <input type="hidden" name="rank" id="rank" value="ALL" />
        </div>

        <!-- Action Button -->
        <div class="d-flex align-items-center justify-content-end pt-3 border-top">
          <button type="submit" name="btnSubmit" id="btnSubmit" class="btn-gradient-submit d-inline-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Generate Report</span>
          </button>
        </div>

        <input type="hidden" name="hidUserId" id="hidUserId" value="<?php echo htmlspecialchars((string)$this->session->get('USERID')); ?>" />
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>

<script>
function selectRank(val, el) {
  document.getElementById('rank').value = val;
  document.querySelectorAll('.rank-pill-card').forEach(function(card) {
    card.classList.remove('active');
  });
  el.classList.add('active');
}

function validateRankWise() {
  var fest = document.getElementById('cmbFestType').value;
  if (!fest || fest === '0' || fest === '') {
    if (typeof swal === 'function') {
      swal("Required", "Please select a Festival Category.", "warning");
    } else {
      alert("Please select a Festival Category.");
    }
    document.getElementById('cmbFestType').focus();
    return false;
  }
  return true;
}
</script>
