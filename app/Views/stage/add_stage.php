<?php
/**
 * Modern & Innovative Stage Management (Define Stages) Interface
 * Tamil Kalolsavam 2026-27
 */
$stages = $stages ?? [];
$stats = $stats ?? [
    'total_stages' => count($stages),
    'total_items' => 0,
    'total_scheduled' => 0,
    'total_completed' => 0,
    'total_managers' => 0,
    'next_stage_num' => count($stages) + 1,
    'next_stage_name' => 'Stage ' . (count($stages) + 1)
];
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap');

/* ── Global Page Tokens ── */
.sd-master-wrapper {
  font-family: 'TAU-Pallai', 'Outfit', 'Poppins', 'Mukta Malar', 'Noto Sans Tamil', system-ui, -apple-system, sans-serif;
  color: #0f172a;
  background: linear-gradient(175deg, #f8fafc 0%, #f1f5f9 40%, #e2e8f0 100%);
  min-height: 100vh;
  padding: 24px 15px 60px;
}

.sd-master-wrapper *,
.sd-master-wrapper *::before,
.sd-master-wrapper *::after {
  box-sizing: border-box;
}

/* ── Hero Command Header ── */
.sd-hero-banner {
  background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4338ca 75%, #6366f1 100%);
  border-radius: 24px;
  color: #ffffff;
  padding: 36px 36px 32px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 20px 40px -10px rgba(49, 46, 129, 0.35);
  margin-bottom: 28px;
}

.sd-hero-banner::before {
  content: '';
  position: absolute;
  top: -40%;
  right: -10%;
  width: 440px;
  height: 440px;
  background: radial-gradient(circle, rgba(129, 140, 248, 0.25) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}

.sd-hero-banner::after {
  content: '';
  position: absolute;
  bottom: -50%;
  left: 20%;
  width: 360px;
  height: 360px;
  background: radial-gradient(circle, rgba(6, 182, 212, 0.18) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}

.sd-hero-particles {
  position: absolute;
  inset: 0;
  overflow: hidden;
  pointer-events: none;
}

.sd-hero-icon-float {
  position: absolute;
  color: #ffffff;
  opacity: 0.08;
  font-size: 3.2rem;
  pointer-events: none;
}
.sd-hero-icon-float--1 { top: 12%; right: 28%; font-size: 4rem; transform: rotate(15deg); }
.sd-hero-icon-float--2 { bottom: 10%; right: 6%; font-size: 5rem; transform: rotate(-10deg); }
.sd-hero-icon-float--3 { top: 20%; left: 45%; font-size: 2.8rem; transform: rotate(-25deg); }

.sd-hero-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  padding: 6px 14px;
  border-radius: 30px;
  font-size: 0.76rem;
  font-weight: 600;
  letter-spacing: 1.2px;
  text-transform: uppercase;
  color: #e0e7ff;
  margin-bottom: 12px;
}

.sd-hero-title {
  font-size: 2.2rem;
  font-weight: 800;
  letter-spacing: -0.5px;
  margin-bottom: 6px;
  color: #ffffff;
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}

.sd-hero-title .sd-title-tamil {
  font-size: 1.35rem;
  font-weight: 600;
  opacity: 0.9;
  background: rgba(255, 255, 255, 0.15);
  padding: 3px 12px;
  border-radius: 8px;
}

.sd-hero-desc {
  font-size: 0.96rem;
  opacity: 0.88;
  max-width: 680px;
  margin-bottom: 16px;
  line-height: 1.55;
  color: #e0e7ff;
}

.sd-hero-breadcrumb {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(0, 0, 0, 0.18);
  padding: 6px 14px;
  border-radius: 10px;
  font-size: 0.84rem;
  color: #cbd5e1;
}

.sd-hero-breadcrumb a {
  color: #e2e8f0;
  text-decoration: none;
  transition: color 0.2s;
}

.sd-hero-breadcrumb a:hover {
  color: #ffffff;
}

/* ── Live KPI Metric Cards ── */
.sd-kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 14px;
  margin-top: 24px;
}

.sd-kpi-card {
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.22);
  border-radius: 18px;
  padding: 16px 18px;
  display: flex;
  align-items: center;
  gap: 14px;
  transition: all 0.25s ease;
}

.sd-kpi-card:hover {
  background: rgba(255, 255, 255, 0.18);
  transform: translateY(-2px);
  border-color: rgba(255, 255, 255, 0.4);
}

.sd-kpi-icon {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
  flex-shrink: 0;
}

.sd-kpi-icon--stages { background: linear-gradient(135deg, #06b6d4, #3b82f6); color: #fff; }
.sd-kpi-icon--items { background: linear-gradient(135deg, #f59e0b, #ef4444); color: #fff; }
.sd-kpi-icon--managers { background: linear-gradient(135deg, #10b981, #059669); color: #fff; }
.sd-kpi-icon--next { background: linear-gradient(135deg, #a855f7, #ec4899); color: #fff; }

.sd-kpi-val {
  font-size: 1.65rem;
  font-weight: 800;
  line-height: 1.1;
  color: #ffffff;
}

.sd-kpi-label {
  font-size: 0.72rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  color: #cbd5e1;
  margin-top: 3px;
}

/* ── Primary Action Button in Hero ── */
.sd-hero-action-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.35);
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
  padding: 13px 28px;
  border-radius: 16px;
  font-size: 0.98rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  text-decoration: none;
}

.sd-hero-action-btn:hover {
  transform: translateY(-3px) scale(1.02);
  box-shadow: 0 12px 30px rgba(16, 185, 129, 0.55);
  color: #ffffff;
}

/* ── Modern Toolbar & Control Bar ── */
.sd-control-bar {
  background: #ffffff;
  border-radius: 20px;
  padding: 16px 22px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
}

.sd-search-box {
  position: relative;
  flex: 1;
  min-width: 260px;
  max-width: 420px;
}

.sd-search-box i.search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 1.05rem;
  pointer-events: none;
}

.sd-search-box input {
  width: 100%;
  height: 44px;
  padding: 8px 36px 8px 42px;
  border: 1.5px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.9rem;
  transition: all 0.2s;
  background: #f8fafc;
  outline: none;
}

.sd-search-box input:focus {
  background: #ffffff;
  border-color: #6366f1;
  box-shadow: 0 0 0 3.5px rgba(99, 102, 241, 0.15);
}

.sd-search-clear {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  display: none;
  font-size: 0.9rem;
}

/* Filter Pills */
.sd-filter-pills {
  display: inline-flex;
  background: #f1f5f9;
  padding: 4px;
  border-radius: 12px;
  gap: 4px;
}

.sd-filter-btn {
  background: transparent;
  border: none;
  padding: 7px 16px;
  border-radius: 9px;
  font-size: 0.82rem;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
}

.sd-filter-btn.active {
  background: #ffffff;
  color: #4338ca;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
}

/* View Mode Switcher */
.sd-view-switcher {
  display: inline-flex;
  background: #f1f5f9;
  padding: 4px;
  border-radius: 12px;
  gap: 4px;
}

.sd-view-btn {
  background: transparent;
  border: none;
  width: 40px;
  height: 36px;
  border-radius: 9px;
  color: #64748b;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  transition: all 0.2s;
}

.sd-view-btn.active {
  background: #ffffff;
  color: #4338ca;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
}

/* ── Stage Cards Grid (Visual Arena Boards) ── */
.sd-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 22px;
}

.sd-stage-card {
  background: #ffffff;
  border-radius: 20px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.05);
  overflow: hidden;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  flex-direction: column;
  position: relative;
}

.sd-stage-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 18px 36px -6px rgba(15, 23, 42, 0.12);
  border-color: #cbd5e1;
}

.sd-card-stripe {
  height: 6px;
  width: 100%;
}
.sd-card-stripe--1 { background: linear-gradient(90deg, #4f46e5, #6366f1); }
.sd-card-stripe--2 { background: linear-gradient(90deg, #7c3aed, #a855f7); }
.sd-card-stripe--3 { background: linear-gradient(90deg, #06b6d4, #3b82f6); }
.sd-card-stripe--4 { background: linear-gradient(90deg, #10b981, #14b8a6); }
.sd-card-stripe--5 { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.sd-card-stripe--6 { background: linear-gradient(90deg, #ec4899, #f43f5e); }

.sd-card-header {
  padding: 18px 20px 14px;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
}

.sd-card-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  padding: 6px 14px;
  border-radius: 12px;
}

.sd-card-badge-icon {
  width: 32px;
  height: 32px;
  border-radius: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  color: #ffffff;
}

.sd-card-badge-name {
  font-size: 1.05rem;
  font-weight: 700;
  color: #1e293b;
}

.sd-card-status {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 0.74rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

.sd-card-status--active {
  background: rgba(16, 185, 129, 0.12);
  color: #059669;
  border: 1px solid rgba(16, 185, 129, 0.25);
}

.sd-card-status--empty {
  background: rgba(100, 116, 139, 0.1);
  color: #64748b;
  border: 1px solid rgba(100, 116, 139, 0.2);
}

.sd-card-body {
  padding: 0 20px 18px;
  flex: 1;
}

.sd-card-venue {
  font-size: 1.02rem;
  font-weight: 600;
  color: #0f172a;
  margin-bottom: 12px;
  display: flex;
  align-items: flex-start;
  gap: 8px;
  line-height: 1.4;
}

.sd-card-venue i {
  color: #6366f1;
  font-size: 1.15rem;
  margin-top: 1px;
  flex-shrink: 0;
}

.sd-card-meta-list {
  background: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 14px;
  padding: 12px 14px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.sd-meta-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.85rem;
}

.sd-meta-item-left {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #64748b;
  font-weight: 500;
}

.sd-meta-item-left i {
  font-size: 0.95rem;
}

.sd-meta-item-right {
  font-weight: 700;
  color: #1e293b;
}

.sd-card-footer {
  background: #fafbfc;
  border-top: 1px solid #f1f5f9;
  padding: 12px 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.sd-card-actions-left {
  display: flex;
  align-items: center;
  gap: 6px;
}

.sd-btn-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
  border: 1.5px solid transparent;
  background: #ffffff;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.sd-btn-icon--edit:hover {
  background: #eff6ff;
  border-color: #93c5fd;
  color: #2563eb;
  transform: translateY(-1px);
}

.sd-btn-icon--delete:hover {
  background: #fef2f2;
  border-color: #fca5a5;
  color: #dc2626;
  transform: translateY(-1px);
}

.sd-btn-console {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  border-radius: 10px;
  font-size: 0.8rem;
  font-weight: 700;
  background: #f1f5f9;
  color: #334155;
  text-decoration: none;
  transition: all 0.2s;
  border: 1px solid #e2e8f0;
}

.sd-btn-console:hover {
  background: linear-gradient(135deg, #4f46e5, #6366f1);
  color: #ffffff;
  border-color: transparent;
  transform: translateY(-1px);
}

/* ── Modern High-Density Table View ── */
.sd-table-container {
  background: #ffffff;
  border-radius: 20px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.05);
  overflow: hidden;
}

.sd-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  margin: 0;
}

.sd-table thead th {
  background: #f8fafc;
  padding: 14px 18px;
  font-size: 0.76rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #64748b;
  border-bottom: 2px solid #e2e8f0;
  white-space: nowrap;
}

.sd-table tbody td {
  padding: 16px 18px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-size: 0.92rem;
}

.sd-table tbody tr {
  transition: background 0.15s;
}

.sd-table tbody tr:hover {
  background: #f8fafc;
}

.sd-num-pill {
  width: 32px;
  height: 32px;
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #f1f5f9;
  color: #334155;
  font-weight: 800;
  font-size: 0.85rem;
}

/* ── Empty State ── */
.sd-empty-box {
  background: #ffffff;
  border-radius: 24px;
  border: 2px dashed #cbd5e1;
  padding: 60px 24px;
  text-align: center;
  margin-top: 20px;
}

.sd-empty-icon-wrap {
  width: 90px;
  height: 90px;
  border-radius: 28px;
  background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
  color: #4f46e5;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 2.8rem;
  margin-bottom: 20px;
  box-shadow: 0 10px 25px rgba(79, 70, 229, 0.12);
}

.sd-empty-title {
  font-size: 1.4rem;
  font-weight: 800;
  color: #1e293b;
  margin-bottom: 8px;
}

.sd-empty-text {
  font-size: 0.95rem;
  color: #64748b;
  max-width: 480px;
  margin: 0 auto 24px;
  line-height: 1.5;
}

/* ── Add / Edit Modal Styles ── */
.sd-modal .modal-content {
  border: none;
  border-radius: 24px;
  box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
  overflow: hidden;
}

.sd-modal .modal-header {
  background: linear-gradient(135deg, #1e1b4b 0%, #3730a3 50%, #4f46e5 100%);
  color: #ffffff;
  padding: 24px 28px;
  border: none;
  position: relative;
}

.sd-modal .modal-title {
  font-size: 1.25rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  gap: 10px;
}

.sd-modal .btn-close {
  filter: brightness(0) invert(1);
  opacity: 0.8;
}

.sd-modal .modal-body {
  padding: 28px;
  background: #ffffff;
}

.sd-modal .modal-footer {
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
  padding: 18px 28px;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 12px;
}

/* Preset Chips in Modal */
.sd-preset-label {
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  color: #64748b;
  margin-bottom: 8px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.sd-preset-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 18px;
}

.sd-preset-chip {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  color: #334155;
  font-size: 0.8rem;
  font-weight: 600;
  padding: 5px 12px;
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.sd-preset-chip:hover {
  background: #e0e7ff;
  border-color: #a5b4fc;
  color: #3730a3;
  transform: translateY(-1px);
}

.sd-field-floating {
  position: relative;
  margin-bottom: 18px;
}

.sd-field-floating label {
  font-size: 0.84rem;
  font-weight: 700;
  color: #334155;
  margin-bottom: 6px;
  display: block;
}

.sd-field-input-wrap {
  position: relative;
}

.sd-field-input-wrap i {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 1.05rem;
}

.sd-field-input-wrap .form-control,
.sd-field-input-wrap .form-select {
  padding-left: 42px;
  height: 48px;
  border-radius: 12px;
  border: 1.5px solid #e2e8f0;
  font-size: 0.95rem;
  transition: all 0.2s;
}

.sd-field-input-wrap .form-control:focus,
.sd-field-input-wrap .form-select:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 3.5px rgba(99, 102, 241, 0.15);
}

@media (max-width: 768px) {
  .sd-hero-banner { padding: 26px 20px; border-radius: 18px; }
  .sd-hero-title { font-size: 1.6rem; }
  .sd-hero-title .sd-title-tamil { font-size: 1rem; }
  .sd-cards-grid { grid-template-columns: 1fr; }
  .sd-control-bar { flex-direction: column; align-items: stretch; }
  .sd-search-box { max-width: 100%; }
}
</style>

<div class="sd-master-wrapper">
<div class="container-fluid px-2 px-md-3">

  <!-- ═══════ HERO COMMAND BANNER ═══════ -->
  <div class="sd-hero-banner">
    <div class="sd-hero-particles">
      <i class="bi bi-mic-fill sd-hero-icon-float sd-hero-icon-float--1"></i>
      <i class="bi bi-speaker-fill sd-hero-icon-float sd-hero-icon-float--2"></i>
      <i class="bi bi-stars sd-hero-icon-float sd-hero-icon-float--3"></i>
    </div>

    <div class="row align-items-center position-relative" style="z-index: 2;">
      <div class="col-lg-8 col-md-7">
        <!-- <div class="sd-hero-pill">
          <i class="bi bi-easel2-fill"></i>
          <span>Competition Arenas & Stages</span>
        </div> -->
        <h1 class="sd-hero-title">
          Define Stages
        </h1>
        <p class="sd-hero-desc">
          Create festival stages, configure venues and locations, manage stage allocations, and streamline program scheduling.
        </p>
        <div class="sd-hero-breadcrumb">
          <a href="<?php echo base_url('welcome'); ?>" title="Home"><i class="bi bi-house-door-fill me-1"></i>Home</a>
          <i class="bi bi-chevron-right small opacity-50"></i>
          <span class="text-white fw-semibold">Define Stages</span>
        </div>
      </div>

      <div class="col-lg-4 col-md-5 text-md-end mt-4 mt-md-0">
        <button type="button" class="sd-hero-action-btn" onclick="sdOpenAddModal('<?php echo esc($stats['next_stage_name']); ?>')">
          <i class="bi bi-plus-circle-fill fs-5"></i>
          <span>Add New Stage</span>
        </button>
      </div>
    </div>

    <!-- Live KPI Metric Counters -->
    <div class="sd-kpi-grid position-relative" style="z-index: 2;">
      <div class="sd-kpi-card">
        <div class="sd-kpi-icon sd-kpi-icon--stages">
          <i class="bi bi-easel2-fill"></i>
        </div>
        <div>
          <div class="sd-kpi-val"><?php echo esc((string)$stats['total_stages']); ?></div>
          <div class="sd-kpi-label">Total Stages</div>
        </div>
      </div>

      <div class="sd-kpi-card">
        <div class="sd-kpi-icon sd-kpi-icon--items">
          <i class="bi bi-trophy-fill"></i>
        </div>
        <div>
          <div class="sd-kpi-val"><?php echo esc((string)$stats['total_items']); ?></div>
          <div class="sd-kpi-label">Allotted Competitions</div>
        </div>
      </div>

      <div class="sd-kpi-card">
        <div class="sd-kpi-icon sd-kpi-icon--managers">
          <i class="bi bi-person-badge-fill"></i>
        </div>
        <div>
          <div class="sd-kpi-val"><?php echo esc((string)$stats['total_managers']); ?></div>
          <div class="sd-kpi-label">Stage Managers</div>
        </div>
      </div>

      <div class="sd-kpi-card">
        <div class="sd-kpi-icon sd-kpi-icon--next">
          <i class="bi bi-lightning-charge-fill"></i>
        </div>
        <div>
          <div class="sd-kpi-val" style="font-size: 1.35rem;"><?php echo esc($stats['next_stage_name']); ?></div>
          <div class="sd-kpi-label">Next Stage</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══════ CONTROL & TOOLBAR ═══════ -->
  <div class="sd-control-bar">
    <!-- Live Search -->
    <div class="sd-search-box">
      <i class="bi bi-search search-icon"></i>
      <input type="text" id="sdLiveSearch" placeholder="Search by stage name or venue location..." autocomplete="off" onkeyup="sdHandleLiveSearch(this.value)">
      <button type="button" id="sdSearchClearBtn" class="sd-search-clear" onclick="sdClearSearch()" title="Clear">
        <i class="bi bi-x-circle-fill"></i>
      </button>
    </div>

    <!-- Quick Filter Pills -->
    <div class="sd-filter-pills">
      <button type="button" class="sd-filter-btn active" data-filter="all" onclick="sdSetFilter('all', this)">
        All (<?php echo count($stages); ?>)
      </button>
      <button type="button" class="sd-filter-btn" data-filter="events" onclick="sdSetFilter('events', this)">
        With Competitions
      </button>
      <button type="button" class="sd-filter-btn" data-filter="free" onclick="sdSetFilter('free', this)">
        Available / Empty
      </button>
    </div>

    <!-- Dual View Switcher -->
    <div class="sd-view-switcher">
      <button type="button" id="sdBtnGridView" class="sd-view-btn active" title="Grid View" onclick="sdSwitchView('grid')">
        <i class="bi bi-grid-fill"></i>
      </button>
      <button type="button" id="sdBtnTableView" class="sd-view-btn" title="Table View" onclick="sdSwitchView('table')">
        <i class="bi bi-table"></i>
      </button>
    </div>
  </div>

  <!-- ═══════ VIEW MODE 1: STAGE CARDS GRID ═══════ -->
  <div id="sdGridView" class="sd-cards-grid">
    <?php
    $i = 1;
    foreach ($stages as $stage):
        $stripeClass = 'sd-card-stripe--' . (($i - 1) % 6 + 1);
        $badgeClass = 'sd-card-stripe--' . (($i - 1) % 6 + 1);
        $itemCount = (int)($stage['item_count'] ?? 0);
        $hasManager = !empty($stage['stage_manager_name']);
        $filterTag = $itemCount > 0 ? 'events' : 'free';
    ?>
    <div class="sd-stage-card" data-stage-id="<?php echo esc($stage['stage_id']); ?>" 
         data-stage-name="<?php echo esc($stage['stage_name']); ?>" 
         data-stage-desc="<?php echo esc($stage['stage_desc']); ?>"
         data-filter-type="<?php echo $filterTag; ?>">
      
      <div class="sd-card-stripe <?php echo $stripeClass; ?>"></div>

      <div class="sd-card-header">
        <div class="sd-card-badge">
          <div class="sd-card-badge-icon <?php echo $badgeClass; ?>">
            <i class="bi bi-mic-fill"></i>
          </div>
          <span class="sd-card-badge-name"><?php echo esc($stage['stage_name']); ?></span>
        </div>

        <?php if ($itemCount > 0): ?>
          <span class="sd-card-status sd-card-status--active">
            <i class="bi bi-check-circle-fill"></i> <?php echo $itemCount; ?> <?php echo ($itemCount === 1) ? 'Competition' : 'Competitions'; ?>
          </span>
        <?php else: ?>
          <span class="sd-card-status sd-card-status--empty">
            <i class="bi bi-dash-circle"></i> Available / Empty
          </span>
        <?php endif; ?>
      </div>

      <div class="sd-card-body">
        <div class="sd-card-venue" title="<?php echo esc($stage['stage_desc']); ?>">
          <i class="bi bi-geo-alt-fill"></i>
          <span><?php echo esc($stage['stage_desc']); ?></span>
        </div>

        <div class="sd-card-meta-list">
          <div class="sd-meta-item">
            <div class="sd-meta-item-left">
              <i class="bi bi-trophy text-warning"></i>
              <span>Allotted Competitions</span>
            </div>
            <div class="sd-meta-item-right">
              <?php echo $itemCount; ?> <?php echo ($itemCount === 1) ? 'Item' : 'Items'; ?>
            </div>
          </div>

          <div class="sd-meta-item">
            <div class="sd-meta-item-left">
              <i class="bi bi-person-badge text-primary"></i>
              <span>Stage Manager</span>
            </div>
            <div class="sd-meta-item-right text-truncate" style="max-width: 150px;">
              <?php if ($hasManager): ?>
                <span class="text-success fw-bold"><?php echo esc($stage['stage_manager_name']); ?></span>
              <?php else: ?>
                <span class="text-muted small">Not Assigned</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="sd-card-footer">
        <div class="sd-card-actions-left">
          <button type="button" class="sd-btn-icon sd-btn-icon--edit" title="Edit Stage" 
                  onclick="sdOpenEditModal('<?php echo esc($stage['stage_id']); ?>', '<?php echo esc($stage['stage_name'], 'js'); ?>', '<?php echo esc($stage['stage_desc'], 'js'); ?>')">
            <i class="bi bi-pencil-square"></i>
          </button>
          <button type="button" class="sd-btn-icon sd-btn-icon--delete" title="Delete Stage" 
                  onclick="sdDeleteStage('<?php echo esc($stage['stage_id']); ?>', this)">
            <i class="bi bi-trash3-fill"></i>
          </button>
        </div>

        <div class="d-flex align-items-center gap-2">
          <a href="<?php echo base_url('stage/allotment'); ?>" class="sd-btn-console" title="Go to Stage Allotment">
            <i class="bi bi-calendar2-check-fill"></i>
            <span>Allotment</span>
          </a>
          <a href="<?php echo base_url('stage/manager'); ?>" class="sd-btn-console" title="Stage Manager Operations Console">
            <i class="bi bi-sliders2-vertical"></i>
            <span>Console</span>
          </a>
        </div>
      </div>
    </div>
    <?php
        $i++;
    endforeach;
    ?>
  </div>

  <!-- ═══════ VIEW MODE 2: HIGH-DENSITY TABLE VIEW ═══════ -->
  <div id="sdTableView" class="sd-table-container" style="display: none;">
    <div class="table-responsive">
      <table class="sd-table" id="sdMainDataTable">
        <thead>
          <tr>
            <th style="width: 50px;">#</th>
            <th>Stage Name</th>
            <th>Venue / Location</th>
            <th style="text-align: center;">Allotted Competitions</th>
            <th>Stage Manager</th>
            <th style="text-align: center;">Status</th>
            <th style="text-align: center; width: 140px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $j = 1;
          foreach ($stages as $st):
              $itemCount = (int)($st['item_count'] ?? 0);
              $filterTag = $itemCount > 0 ? 'events' : 'free';
          ?>
          <tr data-stage-id="<?php echo esc($st['stage_id']); ?>"
              data-stage-name="<?php echo esc($st['stage_name']); ?>"
              data-stage-desc="<?php echo esc($st['stage_desc']); ?>"
              data-filter-type="<?php echo $filterTag; ?>">
            <td><span class="sd-num-pill"><?php echo $j; ?></span></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary border rounded-pill px-3 py-1 fw-bold">
                  <i class="bi bi-mic-fill me-1"></i><?php echo esc($st['stage_name']); ?>
                </span>
              </div>
            </td>
            <td>
              <div class="fw-semibold text-dark">
                <i class="bi bi-geo-alt-fill text-muted me-1 small"></i>
                <?php echo esc($st['stage_desc']); ?>
              </div>
            </td>
            <td style="text-align: center;">
              <?php if ($itemCount > 0): ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                  <?php echo $itemCount; ?> <?php echo ($itemCount === 1) ? 'Item' : 'Items'; ?>
                </span>
              <?php else: ?>
                <span class="badge bg-light text-muted border rounded-pill px-3 py-1">0 Items</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if (!empty($st['stage_manager_name'])): ?>
                <span class="text-dark fw-semibold"><i class="bi bi-person-check-fill text-success me-1"></i><?php echo esc($st['stage_manager_name']); ?></span>
              <?php else: ?>
                <span class="text-muted small">Not Assigned</span>
              <?php endif; ?>
            </td>
            <td style="text-align: center;">
              <?php if ($itemCount > 0): ?>
                <span class="badge bg-indigo text-white rounded-pill px-2 py-1 small" style="background:#4f46e5;">Scheduled</span>
              <?php else: ?>
                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 small">Available</span>
              <?php endif; ?>
            </td>
            <td style="text-align: center;">
              <div class="d-flex align-items-center justify-content-center gap-1">
                <button type="button" class="sd-btn-icon sd-btn-icon--edit" title="Edit Stage"
                        onclick="sdOpenEditModal('<?php echo esc($st['stage_id']); ?>', '<?php echo esc($st['stage_name'], 'js'); ?>', '<?php echo esc($st['stage_desc'], 'js'); ?>')">
                  <i class="bi bi-pencil-square"></i>
                </button>
                <button type="button" class="sd-btn-icon sd-btn-icon--delete" title="Delete Stage"
                        onclick="sdDeleteStage('<?php echo esc($st['stage_id']); ?>', this)">
                  <i class="bi bi-trash3-fill"></i>
                </button>
                <a href="<?php echo base_url('stage/manager'); ?>" class="sd-btn-icon" title="Stage Manager Operations" style="color: #6366f1;">
                  <i class="bi bi-sliders2-vertical"></i>
                </a>
              </div>
            </td>
          </tr>
          <?php
              $j++;
          endforeach;
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ═══════ EMPTY STATE SCREEN ═══════ -->
  <div id="sdEmptyState" class="sd-empty-box" style="<?php echo count($stages) === 0 ? 'display:block;' : 'display:none;'; ?>">
    <div class="sd-empty-icon-wrap">
      <i class="bi bi-mic"></i>
    </div>
    <div class="sd-empty-title" id="sdEmptyTitle">No Stages Defined Yet</div>
    <div class="sd-empty-text" id="sdEmptyText">
      To schedule festival competitions, start by creating your first stage using the button below.
    </div>
    <button type="button" class="sd-hero-action-btn" onclick="sdOpenAddModal('<?php echo esc($stats['next_stage_name']); ?>')">
      <i class="bi bi-plus-circle-fill me-1"></i> Create First Stage
    </button>
  </div>

</div>
</div>

<!-- ═══════ ADD / EDIT STAGE MODAL (WITH VENUE PRESET CHIPS) ═══════ -->
<div class="modal fade sd-modal" id="sdStageModal" tabindex="-1" aria-labelledby="sdStageModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sdStageModalLabel">
          <i class="bi bi-plus-circle-fill" id="sdModalIcon"></i>
          <span id="sdModalTitle">Add New Stage</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="sdStageForm" method="post" action="<?php echo base_url('stage/stage_details/add_stage'); ?>">
          <input type="hidden" name="hidStId" id="sdHidStId" value="">

          <!-- Stage Name Dropdown -->
          <div class="sd-field-floating">
            <label for="sdTxtStageName"><i class="bi bi-mic-fill text-primary me-1"></i>Stage Name <span class="text-danger">*</span></label>
            <div class="sd-field-input-wrap">
              <i class="bi bi-easel2"></i>
              <?php
              $sd_options = array('' => '-- Select Stage --') + (@$stage_array ? $stage_array : array());
              echo form_dropdown("txtStageName", $sd_options, '', 'class="form-select" id="sdTxtStageName" required');
              ?>
            </div>
            <div class="form-text small text-muted mt-1">e.g. Stage 1, Stage 2 ... Stage 50</div>
          </div>

          <!-- Quick Venue Preset Chips -->
          <div class="mt-3">
            <div class="sd-preset-label">
              <i class="bi bi-stars text-warning"></i>
              <span>Quick Venue Presets</span>
            </div>
            <div class="sd-preset-chips">
              <button type="button" class="sd-preset-chip" onclick="sdPickPreset('Main Auditorium')">
                🏛️ Main Auditorium
              </button>
              <button type="button" class="sd-preset-chip" onclick="sdPickPreset('Open Air Theatre')">
                🎪 Open Air Theatre
              </button>
              <button type="button" class="sd-preset-chip" onclick="sdPickPreset('Seminar Hall')">
                🎓 Seminar Hall
              </button>
              <button type="button" class="sd-preset-chip" onclick="sdPickPreset('School Ground Stage')">
                🏟️ School Ground Stage
              </button>
              <button type="button" class="sd-preset-chip" onclick="sdPickPreset('Cultural Hall')">
                🎭 Cultural Hall
              </button>
              <button type="button" class="sd-preset-chip" onclick="sdPickPreset('Mini Hall A')">
                🏢 Mini Hall A
              </button>
            </div>
          </div>

          <!-- Description / Venue Input -->
          <div class="sd-field-floating">
            <label for="sdTxtStageDescription"><i class="bi bi-geo-alt-fill text-primary me-1"></i>Stage Description / Venue <span class="text-danger">*</span></label>
            <div class="sd-field-input-wrap">
              <i class="bi bi-card-text"></i>
              <input type="text" name="txtStageDescription" id="sdTxtStageDescription" class="form-control" 
                     placeholder="e.g. Main Auditorium - Ground Floor" required />
            </div>
            <div class="form-text small text-muted mt-1">Specify stage location, hall name, or campus venue details.</div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i> Cancel
        </button>
        <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold" id="sdModalSubmitBtn" 
                style="background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); border: none;" onclick="sdSubmitStage()">
          <i class="bi bi-check-circle-fill me-1"></i> <span id="sdSubmitBtnText">Save Stage</span>
        </button>
      </div>
    </div>
  </div>
</div>
