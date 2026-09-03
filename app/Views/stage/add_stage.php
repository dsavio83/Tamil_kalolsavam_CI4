<style>
/* ── Stage Details Page — Design Tokens & Shared Components ── */
.stage-details-page {
  font-family: 'Poppins', 'Open Sans', sans-serif;
  color: #1e293b;
  background: linear-gradient(160deg, #f4f6fb 0%, #eef1fa 40%, #e8e4f8 100%);
  min-height: 100vh;
  padding: 24px 0 40px;
}
.stage-details-page *,
.stage-details-page *::before,
.stage-details-page *::after { box-sizing: border-box; }

/* ── Gradient Hero Banner ── */
.sd-hero {
  background: linear-gradient(135deg, #4f46e5 0%, #6d5aea 40%, #7c3aed 100%);
  border-radius: 20px;
  color: #fff;
  padding: 38px 40px 34px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(79,70,229,0.25);
  margin-bottom: 28px;
  animation: sd-fadeUp 0.6s ease both;
}
.sd-hero::before {
  content: '';
  position: absolute;
  top: -60%; right: -18%;
  width: 420px; height: 420px;
  background: radial-gradient(circle, rgba(124,58,237,0.25) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}
.sd-hero::after {
  content: '';
  position: absolute;
  bottom: -40%; left: -10%;
  width: 320px; height: 320px;
  background: radial-gradient(circle, rgba(99,102,241,0.18) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
  animation: sd-orbBreathe 4s ease-in-out infinite;
}
@keyframes sd-orbBreathe {
  0%, 100% { transform: scale(1); opacity: 0.6; }
  50% { transform: scale(1.12); opacity: 1; }
}

.sd-hero .sd-orb {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  animation: sd-orbBreathe 5s ease-in-out infinite;
}
.sd-hero .sd-orb--1 {
  width: 180px; height: 180px;
  background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
  top: 10%; left: 60%;
}
.sd-hero .sd-orb--2 {
  width: 120px; height: 120px;
  background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
  top: 50%; right: 8%;
  animation-delay: -2s;
}

.sd-hero .sd-floating-icon {
  position: absolute;
  font-size: 2.4rem;
  opacity: 0.07;
  pointer-events: none;
  color: #fff;
}
.sd-hero .sd-floating-icon:nth-child(1) { top: 12%; left: 70%; }
.sd-hero .sd-floating-icon:nth-child(2) { top: 55%; left: 82%; font-size: 1.8rem; }
.sd-hero .sd-floating-icon:nth-child(3) { top: 20%; left: 88%; font-size: 2rem; }

.sd-hero-eyebrow {
  display: inline-block;
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 2.5px;
  text-transform: uppercase;
  background: rgba(255,255,255,0.15);
  padding: 5px 14px;
  border-radius: 6px;
  margin-bottom: 12px;
  position: relative;
  z-index: 1;
}
.sd-hero h1 {
  font-size: 2rem;
  font-weight: 700;
  margin: 0 0 6px;
  position: relative;
  z-index: 1;
}
.sd-hero p {
  font-family: 'Open Sans', sans-serif;
  font-size: 0.95rem;
  opacity: 0.85;
  margin: 0 0 10px;
  position: relative;
  z-index: 1;
}
.sd-hero .breadcrumb {
  background: rgba(255,255,255,0.1);
  border-radius: 8px;
  padding: 6px 14px;
  margin: 0;
  display: inline-flex;
  position: relative;
  z-index: 1;
}
.sd-hero .breadcrumb .breadcrumb-item a {
  color: rgba(255,255,255,0.8);
  text-decoration: none;
  font-size: 0.85rem;
}
.sd-hero .breadcrumb .breadcrumb-item a:hover { color: #fff; }
.sd-hero .breadcrumb .breadcrumb-item.active,
.sd-hero .breadcrumb .breadcrumb-item + .breadcrumb-item::before {
  color: rgba(255,255,255,0.5);
}
.sd-hero .breadcrumb .breadcrumb-item.active {
  color: rgba(255,255,255,0.95);
}

.sd-hero-stat {
  position: relative;
  z-index: 1;
  background: rgba(255,255,255,0.12);
  border: 1px solid rgba(255,255,255,0.18);
  border-radius: 14px;
  padding: 16px 22px;
  text-align: center;
  backdrop-filter: blur(4px);
  animation: sd-fadeUp 0.6s ease 0.15s both;
}
.sd-hero-stat .sd-stat-num {
  font-size: 2.4rem;
  font-weight: 700;
  line-height: 1;
}
.sd-hero-stat .sd-stat-label {
  font-size: 0.72rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  opacity: 0.8;
  margin-top: 4px;
}

/* ── Glass Cards ── */
.sd-glass {
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.25);
  border-radius: 18px;
  box-shadow: 0 8px 32px rgba(31,38,135,0.08);
  transition: all 0.3s ease;
  overflow: hidden;
}

/* ── Form Field Styles ── */
.sd-field-group {
  position: relative;
}
.sd-field-group .sd-input-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 1rem;
  pointer-events: none;
  z-index: 2;
}
.sd-field-group .form-select,
.sd-field-group .form-control {
  padding-left: 42px;
  border-radius: 12px;
  border: 1.5px solid #e2e8f0;
  height: 48px;
  font-size: 0.92rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.sd-field-group .form-select:focus,
.sd-field-group .form-control:focus {
  border-color: #7c3aed;
  box-shadow: 0 0 0 3px rgba(124,58,237,0.12);
}

.sd-field-hint {
  font-size: 0.75rem;
  color: #94a3b8;
  margin-top: 5px;
  padding-left: 4px;
}

/* ── Buttons ── */
.sd-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 11px 32px;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.92rem;
  cursor: pointer;
  transition: all 0.25s ease;
}
.sd-btn-primary {
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  color: #fff;
  box-shadow: 0 4px 14px rgba(79,70,229,0.3);
}
.sd-btn-primary:hover {
  box-shadow: 0 6px 20px rgba(79,70,229,0.45);
  transform: translateY(-2px);
  color: #fff;
}
.sd-btn-danger {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: #fff;
  box-shadow: 0 4px 14px rgba(239,68,68,0.25);
}
.sd-btn-danger:hover {
  box-shadow: 0 6px 20px rgba(239,68,68,0.4);
  transform: translateY(-2px);
  color: #fff;
}

/* ── Add Stage Hero Button ── */
.sd-hero-add-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(255,255,255,0.18);
  border: 1.5px solid rgba(255,255,255,0.3);
  color: #fff;
  padding: 10px 22px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.25s ease;
  text-decoration: none;
  position: relative;
  z-index: 1;
}
.sd-hero-add-btn:hover {
  background: rgba(255,255,255,0.28);
  border-color: rgba(255,255,255,0.5);
  color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(255,255,255,0.15);
}
.sd-hero-add-btn i {
  font-size: 1.1rem;
}

/* ── Stage Modal Styles ── */
.sd-modal .modal-content {
  border: none;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(31,38,135,0.18);
}
.sd-modal .modal-header {
  background: linear-gradient(135deg, #4f46e5, #6d5aea, #7c3aed);
  color: #fff;
  padding: 20px 28px;
  border: none;
  position: relative;
  overflow: hidden;
}
.sd-modal .modal-header::before {
  content: '';
  position: absolute;
  top: -50%; right: -20%;
  width: 200px; height: 200px;
  background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}
.sd-modal .modal-header .modal-title {
  font-weight: 700;
  font-size: 1.15rem;
  display: flex;
  align-items: center;
  gap: 10px;
  position: relative;
  z-index: 1;
}
.sd-modal .modal-header .modal-title i {
  font-size: 1.3rem;
}
.sd-modal .modal-header .btn-close {
  filter: brightness(0) invert(1);
  opacity: 0.8;
  position: relative;
  z-index: 1;
}
.sd-modal .modal-header .btn-close:hover {
  opacity: 1;
}
.sd-modal .modal-body {
  padding: 28px;
}
.sd-modal .modal-footer {
  border-top: 1px solid #f1f5f9;
  padding: 16px 28px;
  display: flex;
  justify-content: center;
  gap: 12px;
}
.sd-modal .modal-footer .sd-btn {
  min-width: 140px;
}

/* Animations */
@keyframes sd-fadeUp {
  from { opacity: 0; transform: translateY(18px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes sd-modalIn {
  from { opacity: 0; transform: scale(0.92) translateY(10px); }
  to   { opacity: 1; transform: scale(1) translateY(0); }
}
.spin {
  display: inline-block;
  animation: sd-spin 0.8s linear infinite;
}
@keyframes sd-spin {
  to { transform: rotate(360deg); }
}
.sd-anim-1 { animation: sd-fadeUp 0.5s ease 0.1s both; }
.sd-anim-2 { animation: sd-fadeUp 0.5s ease 0.2s both; }

/* ── Responsive ── */
@media (max-width: 767.98px) {
  .sd-hero { padding: 28px 20px 24px; border-radius: 14px; }
  .sd-hero h1 { font-size: 1.45rem; }
  .sd-hero-stat { margin-top: 16px; }
  .sd-glass { border-radius: 14px; }
  .sd-modal .modal-body { padding: 20px; }
  .sd-modal .modal-footer { padding: 12px 20px; }
}
</style>

<!-- ═══ OPEN shared page container — stages.php will close these ═══ -->
<div class="stage-details-page">
<div class="container-fluid px-3 px-md-4">

<!-- ═══════ HERO BANNER ═══════ -->
<div class="sd-hero">
  <i class="bi bi-mic-stage sd-floating-icon"></i>
  <i class="bi bi-music-note-beamed sd-floating-icon"></i>
  <i class="bi bi-speaker sd-floating-icon"></i>
  <div class="sd-orb sd-orb--1"></div>
  <div class="sd-orb sd-orb--2"></div>
  <div class="row align-items-center">
    <div class="col-lg-7 col-md-6">
      <span class="sd-hero-eyebrow">Stage Management</span>
      <h1>Define Stages</h1>
      <p>Create and manage competition stages for the Tamil Kalolsavam festival.</p>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home"><i class="bi bi-house-fill me-1"></i>Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Define Stages</li>
        </ol>
      </nav>
    </div>
    <div class="col-lg-5 col-md-6 text-md-end mt-3 mt-md-0 d-flex align-items-center justify-content-md-end gap-3">
      <div class="sd-hero-stat d-inline-block">
        <div class="sd-stat-num"><?php echo count($stages); ?></div>
        <div class="sd-stat-label">Total Stages</div>
      </div>
      <button type="button" class="sd-hero-add-btn" data-bs-toggle="modal" data-bs-target="#sdStageModal" onclick="sdOpenAddModal()">
        <i class="bi bi-plus-lg"></i> Add Stage
      </button>
    </div>
  </div>
</div>

<!-- ═══════ ADD / EDIT STAGE MODAL ═══════ -->
<div class="modal fade sd-modal" id="sdStageModal" tabindex="-1" aria-labelledby="sdStageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sdStageModalLabel">
          <i class="bi bi-plus-circle" id="sdModalIcon"></i>
          <span id="sdModalTitle">Add Stage</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="sdStageForm" method="post" action="<?php echo base_url(); ?>stage/stage_details/add_stage">
          <input type="hidden" name="hidStId" id="sdHidStId" value="">

          <!-- Stage Name -->
          <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:0.85rem; letter-spacing:0.3px;">Stage Name</label>
            <div class="sd-field-group">
              <i class="bi bi-mic-stage sd-input-icon"></i>
              <?php
              $sd_options = array('' => '-- Select Stage --') + (@$stage_array ? $stage_array : array());
              echo form_dropdown("txtStageName", $sd_options, '', 'class="form-select" id="sdTxtStageName"');
              ?>
            </div>
            <div class="sd-field-hint"><i class="bi bi-info-circle me-1"></i>Select a stage number from the list</div>
          </div>

          <!-- Description -->
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:0.85rem; letter-spacing:0.3px;">Description</label>
            <div class="sd-field-group">
              <i class="bi bi-card-text sd-input-icon"></i>
              <input type="text" name="txtStageDescription" id="sdTxtStageDescription" class="form-control" placeholder="Enter stage description" />
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="sd-btn sd-btn-danger" data-bs-dismiss="modal">
          <i class="bi bi-x-lg"></i> Cancel
        </button>
        <button type="button" class="sd-btn sd-btn-primary" id="sdModalSubmitBtn" onclick="sdSubmitStage()">
          <i class="bi bi-check-lg"></i> <span id="sdSubmitBtnText">Add Stage</span>
        </button>
      </div>
    </div>
  </div>
</div>

<?php if (!isset($stages) || count($stages) === 0): ?>
<!-- ═══ CLOSE shared page container (stages.php would close it when stages exist) ═══ -->
</div>
</div>
<?php endif; ?>
