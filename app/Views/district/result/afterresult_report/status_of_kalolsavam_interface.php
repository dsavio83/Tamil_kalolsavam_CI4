<?php
/**
 * Status of Kalolsavam - Live Executive Monitor & Festival Progress Dashboard
 * Features:
 * - Live HTML preview with instant client-side filtering (No forced PDF redirects)
 * - Circle graphics: Radial progress rings, animated SVG meters, auto-refresh countdown circle
 * - Animation charts: ApexCharts circular gauge, event status donut, student gender ratio
 * - Full-page multi-card layout: 5 Core status cards, 4 Student & Logistics KPI cards
 * - Category-wise breakdown (LP, UP, HS, HSS) with progress bars, gender breakdown, and item tabs
 * - Live Stage Radar showcase with pulsating beacons and soundwave animation for on-stage events
 * - Completed vs Waiting participant analytics with Male (ஆண்) & Female (பெண்) breakdown
 * - Certificates awarded (வெற்றி பெற்று சான்றிதழ் பெற்றவர்கள்) & Printed count
 * - Stage & Judge count logistics
 * - Optional PDF export button and direct print option
 */

$live = $live_status ?? [
    'summary' => ['total' => 0, 'completed' => 0, 'live' => 0, 'pending' => 0, 'percentage' => 0, 'has_live' => false, 'live_events' => [], 'timestamp' => date('d M Y, h:i A')],
    'student_stats' => ['total_students' => 0, 'total_boys' => 0, 'total_girls' => 0, 'comp_total' => 0, 'comp_boys' => 0, 'comp_girls' => 0, 'pend_total' => 0, 'pend_boys' => 0, 'pend_girls' => 0],
    'awards_stats' => ['cert_awarded' => 0, 'cert_printed' => 0, 'rank_holders' => 0, 'grade_a' => 0, 'grade_b' => 0, 'grade_c' => 0, 'total_declared' => 0],
    'logistics_stats' => ['total_stages' => 0, 'active_stages' => 0, 'total_judges' => 0, 'participating_schools' => 0],
    'categories' => [],
    'all_items' => []
];

$summary = $live['summary'];
$students = $live['student_stats'];
$awards = $live['awards_stats'];
$logistics = $live['logistics_stats'];
$categories = $live['categories'];
$allItems = $live['all_items'];
?>

<!-- Google Fonts & Custom CSS -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap');

:root {
  --k-primary: #3b82f6;
  --k-primary-dark: #1d4ed8;
  --k-success: #10b981;
  --k-success-dark: #059669;
  --k-danger: #ef4444;
  --k-danger-dark: #dc2626;
  --k-warning: #f59e0b;
  --k-warning-dark: #d97706;
  --k-purple: #8b5cf6;
  --k-indigo: #6366f1;
  --k-slate: #0f172a;
}

.kalolsavam-dashboard {
  font-family: 'Outfit', 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  color: #1e293b;
}

.font-tamil {
  font-family: 'TAU-Pallai', 'Outfit', 'Segoe UI', sans-serif !important;
}

/* Glassmorphic & Modern Cards */
.dash-card {
  background: #ffffff;
  border-radius: 18px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.06);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  overflow: hidden;
}
.dash-card:hover {
  box-shadow: 0 8px 25px -4px rgba(15, 23, 42, 0.1);
}

/* Hero Section */
.dash-hero {
  background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
  color: #ffffff;
  padding: 24px 30px;
  border-radius: 20px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.25);
}
.dash-hero::before {
  content: '';
  position: absolute;
  top: -40%;
  right: -5%;
  width: 350px;
  height: 350px;
  background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}

/* Metric KPI Mini Cards */
.kpi-card {
  padding: 18px 20px;
  border-radius: 16px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
  position: relative;
  transition: all 0.2s ease;
}
.kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}
.kpi-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
}

/* Radar & Live Animations */
@keyframes liveRadarPulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
  70% { transform: scale(1.1); box-shadow: 0 0 0 12px rgba(239, 68, 68, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

@keyframes soundWaveAnim {
  0%, 100% { height: 4px; }
  50% { height: 16px; }
}

.radar-dot-live {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #ef4444;
  animation: liveRadarPulse 1.8s infinite;
  display: inline-block;
}

.soundwave-bar {
  width: 3px;
  height: 12px;
  background: #ef4444;
  display: inline-block;
  border-radius: 2px;
  animation: soundWaveAnim 1.2s infinite ease-in-out;
}
.soundwave-bar:nth-child(2) { animation-delay: 0.2s; }
.soundwave-bar:nth-child(3) { animation-delay: 0.4s; }
.soundwave-bar:nth-child(4) { animation-delay: 0.1s; }

/* SVG Circular Progress Meter */
.svg-progress-circle {
  transform: rotate(-90deg);
  transform-origin: 50% 50%;
}
.svg-progress-meter {
  transition: stroke-dashoffset 0.8s ease;
}

/* Filter Toolbar */
.filter-toolbar {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  padding: 16px 20px;
  box-shadow: 0 4px 15px -2px rgba(15, 23, 42, 0.05);
}

/* Category Cards */
.category-card {
  border-radius: 18px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  box-shadow: 0 4px 18px rgba(15, 23, 42, 0.05);
  transition: all 0.25s ease;
}
.category-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 28px rgba(15, 23, 42, 0.09);
}

/* Hero Pills & Live Beacons */
.hero-live-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 14px;
  border-radius: 50px;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  user-select: none;
  transition: all 0.3s ease;
}
.hero-live-pill.status-standby {
  background: #064e3b;
  color: #34d399;
  border: 1px solid #059669;
  box-shadow: 0 2px 10px rgba(6, 78, 59, 0.45);
}
.hero-live-pill.status-broadcasting {
  background: #7f1d1d;
  color: #fca5a5;
  border: 1px solid #dc2626;
  box-shadow: 0 2px 12px rgba(220, 38, 38, 0.45);
}
.hero-live-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  display: inline-block;
  flex-shrink: 0;
}
.hero-live-dot.dot-green {
  background: #10b981;
  box-shadow: 0 0 8px #34d399;
  animation: liveDotPulseGreen 2s infinite ease-in-out;
}
.hero-live-dot.dot-red {
  background: #ef4444;
  box-shadow: 0 0 10px #f87171;
  animation: liveRadarPulse 1.5s infinite ease-in-out;
}
@keyframes liveDotPulseGreen {
  0%, 100% { transform: scale(1); box-shadow: 0 0 6px rgba(16, 185, 129, 0.8); }
  50% { transform: scale(1.15); box-shadow: 0 0 12px rgba(16, 185, 129, 1); }
}

.hero-info-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 50px;
  background: rgba(255, 255, 255, 0.14);
  color: #f1f5f9;
  border: 1px solid rgba(255, 255, 255, 0.22);
  font-size: 0.78rem;
  font-weight: 500;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.hero-switch-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 14px;
  border-radius: 50px;
  background: rgba(255, 255, 255, 0.14);
  border: 1px solid rgba(255, 255, 255, 0.22);
  color: #ffffff;
  font-size: 0.8rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}
.hero-timer-badge {
  background: #ffffff;
  color: #0f172a;
  font-weight: 800;
  padding: 2px 8px;
  border-radius: 999px;
  font-size: 0.72rem;
  margin-left: 4px;
}

/* Demographic & Metric Badges */
.pill-boy {
  background: #eff6ff !important;
  color: #1d4ed8 !important;
  border: 1px solid #bfdbfe !important;
  padding: 4px 10px;
  border-radius: 50px;
  font-weight: 600;
  font-size: 0.78rem;
  display: inline-flex;
  align-items: center;
}
.pill-girl {
  background: #fdf2f8 !important;
  color: #be185d !important;
  border: 1px solid #fbcfe8 !important;
  padding: 4px 10px;
  border-radius: 50px;
  font-weight: 600;
  font-size: 0.78rem;
  display: inline-flex;
  align-items: center;
}
.pill-award {
  background: #fefce8 !important;
  color: #854d0e !important;
  border: 1px solid #fef08a !important;
  padding: 4px 10px;
  border-radius: 50px;
  font-weight: 600;
  font-size: 0.76rem;
  display: inline-flex;
  align-items: center;
}
.pill-print {
  background: #f0fdf4 !important;
  color: #15803d !important;
  border: 1px solid #bbf7d0 !important;
  padding: 4px 10px;
  border-radius: 50px;
  font-weight: 600;
  font-size: 0.76rem;
  display: inline-flex;
  align-items: center;
}
.pill-category-id {
  background: #e0f2fe;
  color: #0369a1;
  border: 1px solid #bae6fd;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 0.76rem;
  letter-spacing: 0.3px;
  display: inline-block;
}

/* Quick Status Filter Interactive Pills */
.filter-status-pill {
  border-radius: 50px !important;
  padding: 6px 16px !important;
  font-size: 0.82rem !important;
  font-weight: 600 !important;
  transition: all 0.2s ease !important;
  border-width: 1.5px !important;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  cursor: pointer;
  outline: none !important;
}
.filter-status-pill[data-status="all"] {
  background: #f1f5f9;
  color: #334155;
  border: 1.5px solid #cbd5e1;
}
.filter-status-pill[data-status="all"].active {
  background: #2563eb !important;
  color: #ffffff !important;
  border-color: #2563eb !important;
  box-shadow: 0 3px 10px rgba(37, 99, 235, 0.35) !important;
}
.filter-status-pill[data-status="COMPLETED"] {
  background: #ecfdf5;
  color: #047857;
  border: 1.5px solid #a7f3d0;
}
.filter-status-pill[data-status="COMPLETED"].active {
  background: #10b981 !important;
  color: #ffffff !important;
  border-color: #10b981 !important;
  box-shadow: 0 3px 10px rgba(16, 185, 129, 0.35) !important;
}
.filter-status-pill[data-status="LIVE"] {
  background: #fef2f2;
  color: #b91c1c;
  border: 1.5px solid #fecaca;
}
.filter-status-pill[data-status="LIVE"].active {
  background: #ef4444 !important;
  color: #ffffff !important;
  border-color: #ef4444 !important;
  box-shadow: 0 3px 10px rgba(239, 68, 68, 0.35) !important;
}
.filter-status-pill[data-status="PENDING"] {
  background: #fffbeb;
  color: #b45309;
  border: 1.5px solid #fde68a;
}
.filter-status-pill[data-status="PENDING"].active {
  background: #f59e0b !important;
  color: #ffffff !important;
  border-color: #f59e0b !important;
  box-shadow: 0 3px 10px rgba(245, 158, 11, 0.35) !important;
}

/* Status Badges */
.badge-status-completed {
  background: #ecfdf5 !important;
  color: #047857 !important;
  border: 1px solid #a7f3d0 !important;
  font-weight: 700;
  padding: 5px 12px;
  border-radius: 50px;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
.badge-status-live {
  background: #fef2f2 !important;
  color: #dc2626 !important;
  border: 1px solid #fecaca !important;
  font-weight: 800;
  padding: 5px 12px;
  border-radius: 50px;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}
.badge-status-pending {
  background: #f8fafc !important;
  color: #64748b !important;
  border: 1px solid #e2e8f0 !important;
  font-weight: 600;
  padding: 5px 12px;
  border-radius: 50px;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
.badge-status-scheduled {
  background: #eff6ff !important;
  color: #1d4ed8 !important;
  border: 1px solid #bfdbfe !important;
  font-weight: 700;
  padding: 5px 12px;
  border-radius: 50px;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

/* Live Stage Showcase Card */
.live-showcase-card {
  background: linear-gradient(135deg, #fff1f2 0%, #fee2e2 100%);
  border: 2px solid #fca5a5;
  border-radius: 18px;
  padding: 18px 24px;
  box-shadow: 0 6px 20px -3px rgba(239, 68, 68, 0.15);
}

/* Clean Scrollbar for Item Lists */
.card-items-scroll {
  max-height: 380px;
  overflow-y: auto;
}
.card-items-scroll::-webkit-scrollbar {
  width: 6px;
}
.card-items-scroll::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
</style>

<div class="kalolsavam-dashboard mb-5">

  <!-- ============================================================== -->
  <!-- 1. HERO HEADER: Live Dashboard Controls & Actions              -->
  <!-- ============================================================== -->
  <div class="dash-hero mb-4">
    <div class="row align-items-center g-3">
      <div class="col-lg-7">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
          <span class="hero-live-pill <?= $summary['has_live'] ? 'status-broadcasting' : 'status-standby' ?>" id="heroLiveStatusPill">
            <span class="hero-live-dot <?= $summary['has_live'] ? 'dot-red' : 'dot-green' ?>" id="heroLiveDot"></span>
            <span id="heroLiveStatusText"><?= $summary['has_live'] ? 'ON-STAGE BROADCAST LIVE' : 'LIVE MONITOR ACTIVE' ?></span>
          </span>
          <span class="hero-info-pill" id="lblLastUpdated">
            <i class="bi bi-clock text-info me-1"></i> Updated: <?= htmlspecialchars($summary['timestamp']); ?>
          </span>
        </div>
        <h1 class="h3 fw-bold mb-1 text-white font-tamil d-flex align-items-center gap-2">
          <i class="bi bi-pie-chart-fill text-warning"></i>
          <span>Status of Kalolsavam - Live Festival Monitor</span>
        </h1>
        <p class="text-white-50 mb-0 small">
          நிகழ்நேர கலைவிழா முன்னேற்றம், பிரிவு வாரியான முடிவுகள், மாணவர்கள் பங்களிப்பு மற்றும் மேடை செயல்பாடுகள்
        </p>
      </div>

      <div class="col-lg-5 text-lg-end">
        <div class="d-inline-flex flex-wrap align-items-center gap-2 justify-content-lg-end">
          
          <!-- Auto Refresh Switch -->
          <div class="form-check form-switch hero-switch-pill me-1">
            <input class="form-check-input mt-0" type="checkbox" id="chkAutoRefresh" checked>
            <label class="form-check-label user-select-none text-white small" for="chkAutoRefresh">
              <i class="bi bi-arrow-repeat me-1 text-warning"></i> Auto (30s)
              <span class="hero-timer-badge" id="lblTimerCount">30</span>
            </label>
          </div>

          <!-- Manual Refresh Button -->
          <button type="button" class="btn btn-light rounded-pill px-3 py-1.5 fw-semibold shadow-sm d-inline-flex align-items-center gap-1.5 text-primary small" id="btnManualRefresh">
            <i class="bi bi-arrow-clockwise" id="iconRefreshSpin"></i>
            <span>Refresh</span>
          </button>

          <!-- Export PDF Button -->
          <button type="button" class="btn btn-warning rounded-pill px-3 py-1.5 fw-semibold shadow-sm d-inline-flex align-items-center gap-1.5 text-dark small" id="btnExportPdf">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>Export PDF</span>
          </button>

          <!-- Print Dashboard -->
          <button type="button" class="btn btn-outline-light rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 small" onclick="window.print();">
            <i class="bi bi-printer"></i>
            <span>Print</span>
          </button>

        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 2. LIVE STAGE BROADCAST SHOWCASE (Shown when event is on stage) -->
  <!-- ============================================================== -->
  <div id="liveShowcaseContainer" class="mb-4" style="<?= $summary['has_live'] ? '' : 'display:none;' ?>">
    <div class="live-showcase-card">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-danger text-white rounded-circle p-3 d-flex align-items-center justify-content-center shadow" style="width: 52px; height: 52px;">
            <i class="bi bi-broadcast fs-4"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2 mb-1">
              <span class="badge bg-danger text-white fw-bold px-2.5 py-1 rounded-pill">
                <span class="radar-dot-live me-1" style="background:#ffffff;"></span> LIVE ON STAGE NOW
              </span>
              <div class="d-inline-flex align-items-center gap-1">
                <span class="soundwave-bar"></span>
                <span class="soundwave-bar"></span>
                <span class="soundwave-bar"></span>
                <span class="soundwave-bar"></span>
              </div>
            </div>
            <h5 class="fw-bold text-dark mb-0 font-tamil" id="liveEventTitle">
              <?php if (!empty($summary['live_events'])): ?>
                #<?= $summary['live_events'][0]['item_code']; ?> - <?= htmlspecialchars($summary['live_events'][0]['item_name']); ?>
              <?php else: ?>
                Competition In Progress
              <?php endif; ?>
            </h5>
            <small class="text-muted" id="liveEventVenue">
              <?php if (!empty($summary['live_events'])): ?>
                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                Stage: <strong><?= htmlspecialchars($summary['live_events'][0]['stage_name']); ?></strong> 
                (<?= htmlspecialchars($summary['live_events'][0]['fest_name']); ?>)
              <?php endif; ?>
            </small>
          </div>
        </div>

        <div class="d-flex align-items-center gap-2">
          <div class="bg-white rounded-4 px-3 py-2 border shadow-sm text-center">
            <small class="text-muted d-block" style="font-size:0.75rem;">Stage Status</small>
            <span class="fw-bold text-danger fs-6" id="liveEventStageStatus">STARTED</span>
          </div>
          <div class="bg-white rounded-4 px-3 py-2 border shadow-sm text-center">
            <small class="text-muted d-block" style="font-size:0.75rem;">Started Time</small>
            <span class="fw-bold text-dark fs-6" id="liveEventStartTime">
              <?= !empty($summary['live_events'][0]['actual_start_time']) ? date('h:i A', strtotime($summary['live_events'][0]['actual_start_time'])) : (!empty($summary['live_events'][0]['start_time']) ? date('h:i A', strtotime($summary['live_events'][0]['start_time'])) : '--:--') ?>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 3. TOP KPI CARDS: 5 Event Status Multi-Cards                   -->
  <!-- ============================================================== -->
  <div class="row g-3 mb-4">

    <!-- Card 1: Total Competitions -->
    <div class="col-xl col-md-4 col-sm-6">
      <div class="kpi-card h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted fw-semibold small">மொத்த போட்டிகள்</span>
          <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
            <i class="bi bi-trophy-fill"></i>
          </div>
        </div>
        <div class="d-flex align-items-baseline gap-2">
          <h2 class="h3 fw-bold mb-0 text-dark" id="kpiTotalEvents"><?= $summary['total']; ?></h2>
          <span class="text-muted small">Items</span>
        </div>
        <small class="text-primary mt-1 d-block fw-medium" style="font-size: 0.78rem;">
          <i class="bi bi-calendar-check me-1"></i> Across all 4 Categories
        </small>
      </div>
    </div>

    <!-- Card 2: Completed Competitions -->
    <div class="col-xl col-md-4 col-sm-6">
      <div class="kpi-card h-100 border-success border-opacity-25" style="background: linear-gradient(180deg, #ffffff 0%, #f0fdf4 100%);">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-success fw-semibold small">முடிவடைந்தவை</span>
          <div class="kpi-icon bg-success bg-opacity-10 text-success">
            <i class="bi bi-check2-circle"></i>
          </div>
        </div>
        <div class="d-flex align-items-baseline gap-2">
          <h2 class="h3 fw-bold mb-0 text-success" id="kpiCompletedEvents"><?= $summary['completed']; ?></h2>
          <span class="badge bg-success text-white rounded-pill px-2 py-0.5" style="font-size:0.75rem;" id="kpiCompletedPct"><?= $summary['percentage']; ?>%</span>
        </div>
        <small class="text-success mt-1 d-block fw-medium" style="font-size: 0.78rem;">
          <i class="bi bi-award-fill me-1"></i> Results declared & confirmed
        </small>
      </div>
    </div>

    <!-- Card 3: Live Now On Stage -->
    <div class="col-xl col-md-4 col-sm-6">
      <div class="kpi-card h-100 <?= $summary['has_live'] ? 'border-danger border-opacity-50' : '' ?>" style="<?= $summary['has_live'] ? 'background: linear-gradient(180deg, #ffffff 0%, #fef2f2 100%);' : '' ?>">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-danger fw-semibold small d-flex align-items-center gap-1">
            <span class="radar-dot-live" style="<?= $summary['has_live'] ? '' : 'background:#94a3b8;' ?>"></span>
            <span>மேடையில் நடப்பவை</span>
          </span>
          <div class="kpi-icon bg-danger bg-opacity-10 text-danger">
            <i class="bi bi-broadcast"></i>
          </div>
        </div>
        <div class="d-flex align-items-baseline gap-2">
          <h2 class="h3 fw-bold mb-0 text-danger" id="kpiLiveEvents"><?= $summary['live']; ?></h2>
          <span class="badge <?= $summary['has_live'] ? 'bg-danger' : 'bg-secondary' ?> text-white rounded-pill px-2 py-0.5" style="font-size:0.75rem;" id="kpiLiveStatusBadge">
            <?= $summary['has_live'] ? 'LIVE NOW' : 'IDLE' ?>
          </span>
        </div>
        <small class="text-muted mt-1 d-block" style="font-size: 0.78rem;" id="kpiLiveDesc">
          <?= $summary['has_live'] ? 'Active competition on stage' : 'No event currently on stage' ?>
        </small>
      </div>
    </div>

    <!-- Card 4: Pending / Scheduled Competitions -->
    <div class="col-xl col-md-6 col-sm-6">
      <div class="kpi-card h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted fw-semibold small">நடைபெற வேண்டியவை</span>
          <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
            <i class="bi bi-hourglass-split"></i>
          </div>
        </div>
        <div class="d-flex align-items-baseline gap-2">
          <h2 class="h3 fw-bold mb-0 text-dark" id="kpiPendingEvents"><?= $summary['pending']; ?></h2>
          <span class="text-muted small">Remaining</span>
        </div>
        <small class="text-warning mt-1 d-block fw-medium" style="font-size: 0.78rem;">
          <i class="bi bi-clock-history me-1"></i> Waiting / Scheduled on stage
        </small>
      </div>
    </div>

    <!-- Card 5: Overall Progress Circular Ring -->
    <div class="col-xl col-md-6 col-sm-12">
      <div class="kpi-card h-100 d-flex align-items-center justify-content-between">
        <div>
          <span class="text-muted fw-semibold small d-block mb-1">முன்னேற்றம் (Progress)</span>
          <h2 class="h3 fw-bold mb-0 text-primary" id="kpiProgressText"><?= $summary['percentage']; ?>%</h2>
          <small class="text-muted" style="font-size: 0.75rem;" id="kpiProgressRatio"><?= $summary['completed']; ?> of <?= $summary['total']; ?> Finished</small>
        </div>

        <!-- SVG Circular Ring Meter -->
        <div class="position-relative d-flex align-items-center justify-content-center" style="width: 58px; height: 58px;">
          <svg width="58" height="58" viewBox="0 0 36 36" class="svg-progress-circle">
            <path stroke="#e2e8f0" stroke-width="3.5" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
            <path id="svgProgressPath" stroke="url(#progressGrad)" stroke-width="3.5" stroke-dasharray="<?= $summary['percentage']; ?>, 100" stroke-linecap="round" fill="none" class="svg-progress-meter" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
            <defs>
              <linearGradient id="progressGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#3b82f6" />
                <stop offset="100%" stop-color="#10b981" />
              </linearGradient>
            </defs>
          </svg>
          <span class="position-absolute fs-6 fw-bold text-dark" style="font-size:0.75rem !important;" id="svgProgressPctLabel"><?= $summary['percentage']; ?>%</span>
        </div>
      </div>
    </div>

  </div>

  <!-- ============================================================== -->
  <!-- 4. PARTICIPANT GENDER & LOGISTICS KPI CARDS (User Requirements) -->
  <!-- ============================================================== -->
  <div class="row g-3 mb-4">

    <!-- Student Stats 1: Completed Participants -->
    <div class="col-lg-3 col-md-6">
      <div class="kpi-card h-100 border-start border-4 border-success">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div>
            <span class="text-success fw-bold small d-block">போட்டி முடிந்த மாணவர்கள்</span>
            <small class="text-muted" style="font-size:0.72rem;">Completed Participants</small>
          </div>
          <div class="kpi-icon bg-success bg-opacity-10 text-success" style="width:38px; height:38px; font-size:1.1rem;">
            <i class="bi bi-people-fill"></i>
          </div>
        </div>
        <div class="d-flex align-items-baseline gap-2 mb-2">
          <h3 class="fw-bold text-dark mb-0" id="statCompStudents"><?= $students['comp_total']; ?></h3>
          <span class="text-muted small">பேர் பங்கெடுத்தனர்</span>
        </div>
        <div class="d-flex align-items-center gap-2 pt-2 border-top">
          <span class="pill-boy" id="statCompBoys">
            👦 ஆண் (Boys): <strong class="ms-1"><?= $students['comp_boys']; ?></strong>
          </span>
          <span class="pill-girl" id="statCompGirls">
            👧 பெண் (Girls): <strong class="ms-1"><?= $students['comp_girls']; ?></strong>
          </span>
        </div>
      </div>
    </div>

    <!-- Student Stats 2: Ready / Pending Participants -->
    <div class="col-lg-3 col-md-6">
      <div class="kpi-card h-100 border-start border-4 border-warning">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div>
            <span class="text-warning fw-bold small d-block">பங்கெடுக்க தயாரானவர்கள்</span>
            <small class="text-muted" style="font-size:0.72rem;">Waiting / Ready to Compete</small>
          </div>
          <div class="kpi-icon bg-warning bg-opacity-10 text-warning" style="width:38px; height:38px; font-size:1.1rem;">
            <i class="bi bi-person-check-fill"></i>
          </div>
        </div>
        <div class="d-flex align-items-baseline gap-2 mb-2">
          <h3 class="fw-bold text-dark mb-0" id="statPendStudents"><?= $students['pend_total']; ?></h3>
          <span class="text-muted small">பேர் தயார் நிலையில்</span>
        </div>
        <div class="d-flex align-items-center gap-2 pt-2 border-top">
          <span class="pill-boy" id="statPendBoys">
            👦 ஆண் (Boys): <strong class="ms-1"><?= $students['pend_boys']; ?></strong>
          </span>
          <span class="pill-girl" id="statPendGirls">
            👧 பெண் (Girls): <strong class="ms-1"><?= $students['pend_girls']; ?></strong>
          </span>
        </div>
      </div>
    </div>

    <!-- Awards Stats: Certificates & Winners -->
    <div class="col-lg-3 col-md-6">
      <div class="kpi-card h-100 border-start border-4 border-primary">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div>
            <span class="text-primary fw-bold small d-block">வெற்றி & சான்றிதழ்கள்</span>
            <small class="text-muted" style="font-size:0.72rem;">Certificates & Ranks</small>
          </div>
          <div class="kpi-icon bg-primary bg-opacity-10 text-primary" style="width:38px; height:38px; font-size:1.1rem;">
            <i class="bi bi-patch-check-fill"></i>
          </div>
        </div>
        <div class="d-flex align-items-baseline gap-2 mb-2">
          <h3 class="fw-bold text-dark mb-0" id="statCertAwarded"><?= $awards['cert_awarded']; ?></h3>
          <span class="text-muted small">சான்றிதழ் பெற்றவர்கள்</span>
        </div>
        <div class="d-flex align-items-center gap-1.5 flex-wrap pt-2 border-top small" style="font-size:0.75rem;">
          <span class="pill-print" id="statCertPrinted">
            <i class="bi bi-printer text-success me-1"></i>அச்சிடப்பட்டவை: <strong class="ms-1"><?= $awards['cert_printed']; ?></strong>
          </span>
          <span class="pill-award" id="statRankHolders">
            <i class="bi bi-trophy text-warning me-1"></i>Ranks (1,2,3): <strong class="ms-1"><?= $awards['rank_holders']; ?></strong>
          </span>
        </div>
      </div>
    </div>

    <!-- Logistics Stats: Stages, Judges & Schools -->
    <div class="col-lg-3 col-md-6">
      <div class="kpi-card h-100 border-start border-4 border-purple" style="border-left-color: #8b5cf6 !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div>
            <span class="fw-bold small d-block" style="color:#8b5cf6;">கலைவிழா வளங்கள்</span>
            <small class="text-muted" style="font-size:0.72rem;">Stages & Judges Logistics</small>
          </div>
          <div class="kpi-icon bg-purple bg-opacity-10" style="color:#8b5cf6; background: rgba(139, 92, 246, 0.1); width:38px; height:38px; font-size:1.1rem;">
            <i class="bi bi-building"></i>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div>
            <small class="text-muted d-block" style="font-size:0.7rem;">மேடைகள் (Stages)</small>
            <h4 class="fw-bold text-dark mb-0" id="statTotalStages"><?= $logistics['total_stages']; ?></h4>
          </div>
          <div>
            <small class="text-muted d-block" style="font-size:0.7rem;">நடுவர்கள் (Judges)</small>
            <h4 class="fw-bold text-dark mb-0" id="statTotalJudges"><?= $logistics['total_judges']; ?></h4>
          </div>
          <div>
            <small class="text-muted d-block" style="font-size:0.7rem;">பள்ளிகள் (Schools)</small>
            <h4 class="fw-bold text-dark mb-0" id="statTotalSchools"><?= $logistics['participating_schools']; ?></h4>
          </div>
        </div>
        <div class="pt-2 border-top text-muted small" style="font-size:0.75rem;" id="statLogisticsDesc">
          <i class="bi bi-check-circle-fill text-success me-1"></i> Active Venues Ready
        </div>
      </div>
    </div>

  </div>

  <!-- ============================================================== -->
  <!-- 5. ANIMATION CHARTS ROW: ApexCharts Graphical Visualizations    -->
  <!-- ============================================================== -->
  <div class="row g-3 mb-4">

    <!-- Chart Card 1: Overall Progress & Event Status Donut -->
    <div class="col-lg-6">
      <div class="dash-card p-4 h-100">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div>
            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2 font-tamil">
              <i class="bi bi-pie-chart-fill text-primary"></i>
              <span>போட்டி நிலை வரைபடம் (Competition Status Chart)</span>
            </h6>
            <small class="text-muted">முடிவடைந்தவை vs மேடையில் நடப்பவை vs நடைபெற வேண்டியவை</small>
          </div>
          <span class="badge bg-light text-primary border px-2.5 py-1 rounded-pill small">Interactive</span>
        </div>
        
        <div class="row align-items-center">
          <div class="col-sm-7">
            <div id="chartEventStatus" style="min-height: 250px;"></div>
          </div>
          <div class="col-sm-5">
            <div class="p-3 bg-light rounded-4 border">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small text-muted"><span class="badge bg-success rounded-circle p-1 me-1"> </span> முடிவடைந்தவை:</span>
                <span class="fw-bold text-success" id="donutLblCompleted"><?= $summary['completed']; ?></span>
              </div>
              <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small text-muted"><span class="badge bg-danger rounded-circle p-1 me-1"> </span> மேடையில்:</span>
                <span class="fw-bold text-danger" id="donutLblLive"><?= $summary['live']; ?></span>
              </div>
              <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="small text-muted"><span class="badge bg-warning rounded-circle p-1 me-1"> </span> மீதமுள்ளவை:</span>
                <span class="fw-bold text-warning" id="donutLblPending"><?= $summary['pending']; ?></span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $summary['percentage']; ?>%" id="barMiniProgress"></div>
              </div>
              <div class="d-flex justify-content-between text-muted mt-1" style="font-size:0.7rem;">
                <span>முன்னேற்றம்:</span>
                <span class="fw-bold text-dark" id="barMiniPct"><?= $summary['percentage']; ?>%</span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Chart Card 2: Student Gender & Category Comparison -->
    <div class="col-lg-6">
      <div class="dash-card p-4 h-100">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div>
            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2 font-tamil">
              <i class="bi bi-bar-chart-line-fill text-indigo" style="color:#6366f1;"></i>
              <span>பிரிவு வாரியான ஒப்பீடு & பாலின விகிதம் (Category & Gender)</span>
            </h6>
            <small class="text-muted">LP, UP, HS, HSS பிரிவுகளின் முடிவுகள் மற்றும் ஆண்/பெண் விவரம்</small>
          </div>
          <span class="badge bg-light text-indigo border px-2.5 py-1 rounded-pill small" style="color:#6366f1;">Analytics</span>
        </div>

        <div id="chartCategoryComparison" style="min-height: 250px;"></div>
      </div>
    </div>

  </div>

  <!-- ============================================================== -->
  <!-- 6. CONTROLS & FILTER TOOLBAR CARD                               -->
  <!-- ============================================================== -->
  <div class="filter-toolbar mb-4">
    <div class="row g-3 align-items-center">

      <!-- Category Selector -->
      <div class="col-lg-3 col-md-6">
        <label for="selectFestFilter" class="form-label fw-semibold text-dark small mb-1 d-flex align-items-center gap-1.5">
          <i class="bi bi-filter-circle text-primary"></i>
          <span>கலைவிழா பிரிவு (Category)</span>
        </label>
        <select class="form-select border rounded-3 py-2 fs-6 shadow-none" id="selectFestFilter">
          <?php foreach ($fest as $fid => $fname): ?>
            <option value="<?= htmlspecialchars((string)$fid); ?>"><?= htmlspecialchars((string)$fname); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Date Selector -->
      <div class="col-lg-3 col-md-6">
        <label for="selectDateFilter" class="form-label fw-semibold text-dark small mb-1 d-flex align-items-center gap-1.5">
          <i class="bi bi-calendar-event text-primary"></i>
          <span>போட்டி தேதி (Date)</span>
        </label>
        <select class="form-select border rounded-3 py-2 fs-6 shadow-none" id="selectDateFilter">
          <?php foreach ($date_array as $dtVal => $dtName): ?>
            <option value="<?= htmlspecialchars((string)$dtVal); ?>"><?= htmlspecialchars((string)$dtName); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Quick Search -->
      <div class="col-lg-3 col-md-6">
        <label for="txtSearchQuery" class="form-label fw-semibold text-dark small mb-1 d-flex align-items-center gap-1.5">
          <i class="bi bi-search text-primary"></i>
          <span>விரைவு தேடல் (Search Item)</span>
        </label>
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control border-start-0 py-2 fs-6 shadow-none" id="txtSearchQuery" placeholder="Search item code / name...">
          <button class="btn btn-outline-secondary border border-start-0" type="button" id="btnClearSearch" title="Clear">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>

      <!-- View Selector & Status Buttons -->
      <div class="col-lg-3 col-md-6 text-lg-end pt-lg-3">
        <div class="btn-group w-100 shadow-sm" role="group" aria-label="View Switch">
          <button type="button" class="btn btn-primary active py-2 fw-semibold small" id="btnViewCards">
            <i class="bi bi-grid-fill me-1"></i> Cards
          </button>
          <button type="button" class="btn btn-outline-primary py-2 fw-semibold small" id="btnViewTable">
            <i class="bi bi-table me-1"></i> Master Table
          </button>
          <button type="button" class="btn btn-outline-primary py-2 fw-semibold small" id="btnViewBoth">
            <i class="bi bi-layout-split me-1"></i> Both
          </button>
        </div>
      </div>

    </div>

    <!-- Quick Status Filter Pills -->
    <div class="d-flex flex-wrap align-items-center gap-2 pt-3 mt-3 border-top">
      <span class="text-muted small fw-semibold me-1"><i class="bi bi-funnel-fill me-1"></i> Filter Status:</span>
      <button type="button" class="filter-status-pill active" data-status="all">
        All Items (<span id="pillCountAll"><?= $summary['total']; ?></span>)
      </button>
      <button type="button" class="filter-status-pill" data-status="COMPLETED">
        <i class="bi bi-check2 me-1"></i> Completed (<span id="pillCountComp"><?= $summary['completed']; ?></span>)
      </button>
      <button type="button" class="filter-status-pill" data-status="LIVE">
        <span class="radar-dot-live me-1" style="width:8px; height:8px;"></span> Live Now (<span id="pillCountLive"><?= $summary['live']; ?></span>)
      </button>
      <button type="button" class="filter-status-pill" data-status="PENDING">
        <i class="bi bi-hourglass me-1"></i> Pending (<span id="pillCountPend"><?= $summary['pending']; ?></span>)
      </button>
    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 7. CATEGORY-WISE MULTI-CARDS GRID                               -->
  <!-- ============================================================== -->
  <div id="sectionCategoryCards" class="mb-4">
    <div class="row g-4" id="categoryCardsGrid">
      <?php foreach ($categories as $cat): ?>
        <div class="col-xl-6 category-card-wrapper" data-fest-id="<?= $cat['fest_id']; ?>">
          <div class="category-card p-4 h-100">
            
            <!-- Category Card Header -->
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
              <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                  <span class="pill-category-id">
                    Category #<?= $cat['fest_id']; ?>
                  </span>
                  <?php if ($cat['live'] > 0): ?>
                    <span class="badge bg-danger text-white fw-bold px-2 py-0.5 rounded-pill small">
                      <span class="radar-dot-live me-1" style="width:6px;height:6px;background:#fff;"></span> LIVE
                    </span>
                  <?php endif; ?>
                </div>
                <h5 class="fw-bold mb-0 text-dark font-tamil"><?= htmlspecialchars($cat['fest_name']); ?></h5>
              </div>

              <!-- Animated SVG Circular Mini Meter for Category -->
              <div class="d-flex align-items-center gap-2">
                <div class="position-relative d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                  <svg width="52" height="52" viewBox="0 0 36 36" class="svg-progress-circle">
                    <path stroke="#f1f5f9" stroke-width="3.5" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    <path stroke="#10b981" stroke-width="3.5" stroke-dasharray="<?= $cat['percentage']; ?>, 100" stroke-linecap="round" fill="none" class="svg-progress-meter" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                  </svg>
                  <span class="position-absolute fs-6 fw-bold text-dark" style="font-size: 0.72rem !important;"><?= $cat['percentage']; ?>%</span>
                </div>
              </div>
            </div>

            <!-- Progress Bar & Status Chips -->
            <div class="mb-3">
              <div class="d-flex justify-content-between text-muted small mb-1">
                <span>முடிவடைந்தவை: <strong><?= $cat['completed']; ?> / <?= $cat['total']; ?></strong> Items</span>
                <span class="fw-bold text-success"><?= $cat['percentage']; ?>% Complete</span>
              </div>
              <div class="progress" style="height: 8px; border-radius: 4px;">
                <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: <?= $cat['percentage']; ?>%"></div>
              </div>
            </div>

            <!-- Mini Summary Row for Students in this Category -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-2 bg-light rounded-3 mb-3 small">
              <span class="text-muted">
                <i class="bi bi-people text-primary me-1"></i> மாணவர்கள்: <strong><?= $cat['total_boys'] + $cat['total_girls']; ?></strong>
              </span>
              <span class="text-primary">
                👦 ஆண்: <strong><?= $cat['total_boys']; ?></strong>
              </span>
              <span class="text-danger">
                👧 பெண்: <strong><?= $cat['total_girls']; ?></strong>
              </span>
              <span class="badge bg-white text-dark border px-2 py-0.5">
                Completed: <strong><?= $cat['completed']; ?></strong> | Pending: <strong><?= $cat['pending']; ?></strong>
              </span>
            </div>

            <!-- Sub Tabs: Completed vs Pending Items -->
            <ul class="nav nav-pills nav-fill mb-3 bg-light p-1 rounded-pill small" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill py-1 fw-semibold small" data-bs-toggle="pill" data-bs-target="#tab-comp-<?= $cat['fest_id']; ?>" type="button">
                  <i class="bi bi-check-circle-fill text-success me-1"></i> Completed (<?= count($cat['completed_items']); ?>)
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill py-1 fw-semibold small" data-bs-toggle="pill" data-bs-target="#tab-pend-<?= $cat['fest_id']; ?>" type="button">
                  <i class="bi bi-hourglass-split text-warning me-1"></i> Pending (<?= count($cat['pending_items']); ?>)
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill py-1 fw-semibold small" data-bs-toggle="pill" data-bs-target="#tab-all-<?= $cat['fest_id']; ?>" type="button">
                  <i class="bi bi-list-ul me-1"></i> All (<?= count($cat['items']); ?>)
                </button>
              </li>
            </ul>

            <!-- Tab Content for Items -->
            <div class="tab-content card-items-scroll">

              <!-- Completed Tab -->
              <div class="tab-pane fade show active" id="tab-comp-<?= $cat['fest_id']; ?>" role="tabpanel">
                <?php if (!empty($cat['completed_items'])): ?>
                  <div class="list-group list-group-flush">
                    <?php foreach ($cat['completed_items'] as $item): ?>
                      <div class="list-group-item px-2 py-2.5 border-0 border-bottom d-flex align-items-center justify-content-between item-list-row" data-code="<?= $item['item_code']; ?>" data-name="<?= strtolower(htmlspecialchars($item['item_name'])); ?>" data-status="COMPLETED">
                        <div class="d-flex align-items-center gap-2.5">
                          <span class="badge bg-success bg-opacity-10 text-success rounded-circle p-2">
                            <i class="bi bi-check2"></i>
                          </span>
                          <div>
                            <div class="fw-semibold text-dark mb-0 font-tamil" style="font-size:0.9rem;">
                              <span class="badge bg-light text-dark border me-1">#<?= $item['item_code']; ?></span>
                              <?= htmlspecialchars($item['item_name']); ?>
                            </div>
                            <small class="text-muted" style="font-size:0.75rem;">
                              <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($item['stage_name']); ?>
                              <?php if ($item['result_count'] > 0): ?>
                                • <span class="text-success"><i class="bi bi-award me-1"></i><?= $item['result_count']; ?> Positions Declared</span>
                              <?php endif; ?>
                            </small>
                          </div>
                        </div>
                        <span class="badge badge-status-completed px-2.5 py-1 rounded-pill small">
                          Completed
                        </span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <div class="text-center py-4 text-muted small">
                    <i class="bi bi-inbox fs-3 d-block mb-1 text-black-50"></i>
                    இப்பிரிவில் முடிவுகள் எதுவும் இன்னும் அறிவிக்கப்படவில்லை.<br>
                    <span class="text-black-50" style="font-size:0.75rem;">Completed results will appear here automatically.</span>
                  </div>
                <?php endif; ?>
              </div>

              <!-- Pending Tab -->
              <div class="tab-pane fade" id="tab-pend-<?= $cat['fest_id']; ?>" role="tabpanel">
                <?php if (!empty($cat['pending_items'])): ?>
                  <div class="list-group list-group-flush">
                    <?php foreach ($cat['pending_items'] as $item): ?>
                      <div class="list-group-item px-2 py-2.5 border-0 border-bottom d-flex align-items-center justify-content-between item-list-row" data-code="<?= $item['item_code']; ?>" data-name="<?= strtolower(htmlspecialchars($item['item_name'])); ?>" data-status="<?= $item['calculated_status']; ?>">
                        <div class="d-flex align-items-center gap-2.5">
                          <span class="badge bg-light text-muted rounded-circle p-2 border">
                            <i class="bi bi-clock"></i>
                          </span>
                          <div>
                            <div class="fw-semibold text-dark mb-0 font-tamil" style="font-size:0.9rem;">
                              <span class="badge bg-light text-dark border me-1">#<?= $item['item_code']; ?></span>
                              <?= htmlspecialchars($item['item_name']); ?>
                            </div>
                            <small class="text-muted" style="font-size:0.75rem;">
                              <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($item['stage_name']); ?>
                              <?php if (!empty($item['start_time'])): ?>
                                • <i class="bi bi-calendar-event me-1"></i><?= date('d M, h:i A', strtotime($item['start_time'])); ?>
                              <?php endif; ?>
                            </small>
                          </div>
                        </div>
                        <span class="badge <?= ($item['calculated_status'] === 'LIVE') ? 'badge-status-live' : 'badge-status-pending' ?> px-2.5 py-1 rounded-pill small">
                          <?= ($item['calculated_status'] === 'LIVE') ? 'LIVE NOW' : 'Pending' ?>
                        </span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <div class="text-center py-4 text-muted small">
                    <i class="bi bi-check2-all fs-3 d-block mb-1 text-success"></i>
                    இப்பிரிவின் அனைத்துப் போட்டிகளும் வெற்றிகரமாக முடிவடைந்துவிட்டன!
                  </div>
                <?php endif; ?>
              </div>

              <!-- All Items Tab -->
              <div class="tab-pane fade" id="tab-all-<?= $cat['fest_id']; ?>" role="tabpanel">
                <div class="list-group list-group-flush">
                  <?php foreach ($cat['items'] as $item): ?>
                    <div class="list-group-item px-2 py-2.5 border-0 border-bottom d-flex align-items-center justify-content-between item-list-row" data-code="<?= $item['item_code']; ?>" data-name="<?= strtolower(htmlspecialchars($item['item_name'])); ?>" data-status="<?= $item['calculated_status']; ?>">
                      <div class="d-flex align-items-center gap-2.5">
                        <span class="badge <?= ($item['calculated_status'] === 'COMPLETED') ? 'bg-success bg-opacity-10 text-success' : (($item['calculated_status'] === 'LIVE') ? 'bg-danger bg-opacity-10 text-danger' : 'bg-light text-muted border') ?> rounded-circle p-2">
                          <i class="bi <?= ($item['calculated_status'] === 'COMPLETED') ? 'bi-check2' : (($item['calculated_status'] === 'LIVE') ? 'bi-broadcast' : 'bi-clock') ?>"></i>
                        </span>
                        <div>
                          <div class="fw-semibold text-dark mb-0 font-tamil" style="font-size:0.9rem;">
                            <span class="badge bg-light text-dark border me-1">#<?= $item['item_code']; ?></span>
                            <?= htmlspecialchars($item['item_name']); ?>
                          </div>
                          <small class="text-muted" style="font-size:0.75rem;">
                            <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($item['stage_name']); ?>
                            • Participants: <?= $item['participant_count']; ?>
                          </small>
                        </div>
                      </div>
                      <span class="badge <?= ($item['calculated_status'] === 'COMPLETED') ? 'badge-status-completed' : (($item['calculated_status'] === 'LIVE') ? 'badge-status-live' : 'badge-status-pending') ?> px-2.5 py-1 rounded-pill small">
                        <?= $item['status_label']; ?>
                      </span>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>

            </div>

          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 8. MASTER COMPREHENSIVE SCHEDULE & STATUS TABLE                 -->
  <!-- ============================================================== -->
  <div id="sectionMasterTable" class="dash-card p-4 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-3 border-bottom">
      <div>
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2 font-tamil">
          <i class="bi bi-table text-primary"></i>
          <span>அனைத்துப் போட்டிகளின் முழு பட்டியல் (Master Event Roster)</span>
        </h5>
        <small class="text-muted">தேடல், நிலை மற்றும் மேடை ஒதுக்கீடுகளுடன் கூடிய விரிவான அட்டவணை</small>
      </div>

      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill small">
          காட்டப்படுபவை: <strong id="lblTableShowingCount"><?= count($allItems); ?></strong> / <?= count($allItems); ?> போட்டிகள்
        </span>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" id="tableMasterEvents">
        <thead class="table-light text-muted small text-uppercase">
          <tr>
            <th style="width: 70px;">குறியீடு</th>
            <th>போட்டியின் பெயர் (Item Name)</th>
            <th style="width: 140px;">பிரிவு (Category)</th>
            <th style="width: 90px;">வகை</th>
            <th>மேடை & இடம் (Stage)</th>
            <th style="width: 140px;">நேரம் / தேதி</th>
            <th style="width: 120px;" class="text-center">பங்கேற்பாளர்கள்</th>
            <th style="width: 130px;" class="text-center">நிலை (Status)</th>
          </tr>
        </thead>
        <tbody id="tableMasterBody">
          <?php if (!empty($allItems)): ?>
            <?php foreach ($allItems as $item): ?>
              <tr class="master-table-row" data-code="<?= $item['item_code']; ?>" data-fest="<?= $item['fest_id']; ?>" data-status="<?= $item['calculated_status']; ?>" data-name="<?= strtolower(htmlspecialchars($item['item_name'])); ?>">
                <td>
                  <span class="badge bg-light text-dark border fw-bold px-2 py-1">#<?= $item['item_code']; ?></span>
                </td>
                <td>
                  <div class="fw-bold text-dark font-tamil"><?= htmlspecialchars($item['item_name']); ?></div>
                  <?php if ($item['result_count'] > 0): ?>
                    <small class="text-success"><i class="bi bi-award-fill me-1"></i><?= $item['result_count']; ?> Positions Declared</small>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="badge bg-light text-primary border rounded-pill px-2.5 py-1 small">
                    <?= htmlspecialchars($item['fest_name']); ?>
                  </span>
                </td>
                <td>
                  <span class="badge bg-light text-secondary border px-2 py-0.5 small">
                    <?= ($item['item_type'] === 'G') ? 'Group' : 'Single' ?>
                  </span>
                </td>
                <td>
                  <div class="small fw-semibold text-dark"><?= htmlspecialchars($item['stage_name']); ?></div>
                  <?php if (!empty($item['venue'])): ?>
                    <small class="text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($item['venue']); ?></small>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($item['start_time'])): ?>
                    <div class="small fw-medium text-dark"><?= date('d M Y', strtotime($item['start_time'])); ?></div>
                    <small class="text-muted" style="font-size:0.75rem;"><?= date('h:i A', strtotime($item['start_time'])); ?></small>
                  <?php else: ?>
                    <span class="text-muted small">--</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <span class="badge bg-light text-dark border px-2 py-1">
                    <?= $item['participant_count']; ?> (👦 <?= $item['boys_count']; ?> | 👧 <?= $item['girls_count']; ?>)
                  </span>
                </td>
                <td class="text-center">
                  <span class="badge <?= ($item['calculated_status'] === 'COMPLETED') ? 'badge-status-completed' : (($item['calculated_status'] === 'LIVE') ? 'badge-status-live' : 'badge-status-pending') ?> px-2.5 py-1.5 rounded-pill small">
                    <?= $item['status_label']; ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2 text-black-50"></i>
                போட்டிகள் எதுவும் கிடைக்கவில்லை (No items found).
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- ApexCharts Script Inclusion -->
<script src="<?php echo base_url(); ?>assets/apexcharts.js"></script>

<!-- Embedded Dashboard Client Controller -->
<script>
document.addEventListener('DOMContentLoaded', function () {

  // Global State
  var dashboardState = {
    fest: 'All',
    date: 'All',
    status: 'all',
    search: '',
    view: 'cards',
    autoRefresh: true,
    timerSeconds: 30,
    timerInterval: null
  };

  // Cached DOM elements
  var elSearch = document.getElementById('txtSearchQuery');
  var elClearSearch = document.getElementById('btnClearSearch');
  var elFestFilter = document.getElementById('selectFestFilter');
  var elDateFilter = document.getElementById('selectDateFilter');
  var elTimerCount = document.getElementById('lblTimerCount');
  var elAutoRefresh = document.getElementById('chkAutoRefresh');
  var elBtnRefresh = document.getElementById('btnManualRefresh');
  var elIconSpin = document.getElementById('iconRefreshSpin');
  var elBtnExportPdf = document.getElementById('btnExportPdf');
  var statusPills = document.querySelectorAll('.filter-status-pill');

  var secCards = document.getElementById('sectionCategoryCards');
  var secTable = document.getElementById('sectionMasterTable');

  // Chart Instances
  var chartStatus = null;
  var chartCategory = null;

  // Initial Chart Data
  var initialCompleted = <?= (int)$summary['completed']; ?>;
  var initialLive = <?= (int)$summary['live']; ?>;
  var initialPending = <?= (int)$summary['pending']; ?>;

  var categoryNames = [];
  var categoryCompleted = [];
  var categoryPending = [];

  <?php foreach ($categories as $c): ?>
    categoryNames.push(<?= json_encode($c['fest_name']); ?>);
    categoryCompleted.push(<?= (int)$c['completed']; ?>);
    categoryPending.push(<?= (int)$c['pending']; ?>);
  <?php endforeach; ?>

  // 1. Initialize ApexCharts
  function initCharts() {
    if (typeof ApexCharts === 'undefined') {
      console.warn('ApexCharts library not yet loaded');
      return;
    }

    // Chart 1: Donut Chart for Event Status
    var optionsStatus = {
      series: [initialCompleted, initialLive, initialPending],
      labels: ['Completed', 'Live Now', 'Pending'],
      colors: ['#10b981', '#ef4444', '#f59e0b'],
      chart: {
        type: 'donut',
        height: 250,
        animations: {
          enabled: true,
          easing: 'easeinout',
          speed: 800
        }
      },
      dataLabels: {
        enabled: true,
        formatter: function (val, opts) {
          return opts.w.config.series[opts.seriesIndex];
        }
      },
      plotOptions: {
        pie: {
          donut: {
            size: '65%',
            labels: {
              show: true,
              total: {
                show: true,
                label: 'Total Items',
                fontSize: '13px',
                fontWeight: 600,
                color: '#64748b',
                formatter: function (w) {
                  return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                }
              }
            }
          }
        }
      },
      legend: {
        show: false
      },
      stroke: {
        width: 2,
        colors: ['#ffffff']
      }
    };

    var elChartStatus = document.querySelector("#chartEventStatus");
    if (elChartStatus) {
      chartStatus = new ApexCharts(elChartStatus, optionsStatus);
      chartStatus.render();
    }

    // Chart 2: Stacked Bar for Category Breakdown
    var optionsCategory = {
      series: [{
        name: 'Completed (முடிவடைந்தவை)',
        data: categoryCompleted
      }, {
        name: 'Pending (மீதமுள்ளவை)',
        data: categoryPending
      }],
      colors: ['#10b981', '#cbd5e1'],
      chart: {
        type: 'bar',
        height: 250,
        stacked: true,
        toolbar: { show: false },
        animations: {
          enabled: true,
          easing: 'easeinout',
          speed: 800
        }
      },
      plotOptions: {
        bar: {
          horizontal: true,
          borderRadius: 6,
          barHeight: '60%'
        }
      },
      dataLabels: {
        enabled: true,
        formatter: function (val) {
          return val > 0 ? val : '';
        }
      },
      xaxis: {
        categories: categoryNames,
        labels: {
          style: { fontSize: '11px', fontFamily: 'Outfit, sans-serif' }
        }
      },
      yaxis: {
        labels: {
          style: { fontSize: '12px', fontWeight: 600, fontFamily: 'TAU-Pallai, Outfit, sans-serif' }
        }
      },
      legend: {
        position: 'top',
        horizontalAlign: 'right',
        fontSize: '12px',
        markers: { radius: 12 }
      },
      fill: { opacity: 1 }
    };

    var elChartCategory = document.querySelector("#chartCategoryComparison");
    if (elChartCategory) {
      chartCategory = new ApexCharts(elChartCategory, optionsCategory);
      chartCategory.render();
    }
  }

  // Run Chart Init
  initCharts();

  // 2. Client-Side Instant Filter Engine
  function applyFilters() {
    var q = dashboardState.search.trim().toLowerCase();
    var selFest = dashboardState.fest;
    var selStatus = dashboardState.status;

    // Filter Master Table Rows
    var tableRows = document.querySelectorAll('.master-table-row');
    var visibleTableCount = 0;

    tableRows.forEach(function (row) {
      var code = row.getAttribute('data-code') || '';
      var name = row.getAttribute('data-name') || '';
      var fest = row.getAttribute('data-fest') || '';
      var status = row.getAttribute('data-status') || '';

      var matchSearch = (q === '' || code.includes(q) || name.includes(q));
      var matchFest = (selFest === 'All' || fest === selFest);
      var matchStatus = (selStatus === 'all' || status.toUpperCase() === selStatus.toUpperCase());

      if (matchSearch && matchFest && matchStatus) {
        row.style.display = '';
        visibleTableCount++;
      } else {
        row.style.display = 'none';
      }
    });

    var lblTableCount = document.getElementById('lblTableShowingCount');
    if (lblTableCount) {
      lblTableCount.textContent = visibleTableCount;
    }

    // Filter Category Cards
    var catCards = document.querySelectorAll('.category-card-wrapper');
    catCards.forEach(function (card) {
      var festId = card.getAttribute('data-fest-id');
      if (selFest === 'All' || festId === selFest) {
        card.style.display = '';
      } else {
        card.style.display = 'none';
      }

      // Filter rows inside category card
      var itemRows = card.querySelectorAll('.item-list-row');
      itemRows.forEach(function (iRow) {
        var code = iRow.getAttribute('data-code') || '';
        var name = iRow.getAttribute('data-name') || '';
        var status = iRow.getAttribute('data-status') || '';

        var matchSearch = (q === '' || code.includes(q) || name.includes(q));
        var matchStatus = (selStatus === 'all' || status.toUpperCase() === selStatus.toUpperCase());

        if (matchSearch && matchStatus) {
          iRow.style.display = '';
        } else {
          iRow.style.display = 'none';
        }
      });
    });
  }

  // 3. View Switcher
  document.getElementById('btnViewCards').addEventListener('click', function () {
    secCards.style.display = '';
    secTable.style.display = 'none';
    this.classList.add('active', 'btn-primary');
    this.classList.remove('btn-outline-primary');
    document.getElementById('btnViewTable').classList.remove('active', 'btn-primary');
    document.getElementById('btnViewTable').classList.add('btn-outline-primary');
    document.getElementById('btnViewBoth').classList.remove('active', 'btn-primary');
    document.getElementById('btnViewBoth').classList.add('btn-outline-primary');
  });

  document.getElementById('btnViewTable').addEventListener('click', function () {
    secCards.style.display = 'none';
    secTable.style.display = '';
    this.classList.add('active', 'btn-primary');
    this.classList.remove('btn-outline-primary');
    document.getElementById('btnViewCards').classList.remove('active', 'btn-primary');
    document.getElementById('btnViewCards').classList.add('btn-outline-primary');
    document.getElementById('btnViewBoth').classList.remove('active', 'btn-primary');
    document.getElementById('btnViewBoth').classList.add('btn-outline-primary');
  });

  document.getElementById('btnViewBoth').addEventListener('click', function () {
    secCards.style.display = '';
    secTable.style.display = '';
    this.classList.add('active', 'btn-primary');
    this.classList.remove('btn-outline-primary');
    document.getElementById('btnViewCards').classList.remove('active', 'btn-primary');
    document.getElementById('btnViewCards').classList.add('btn-outline-primary');
    document.getElementById('btnViewTable').classList.remove('active', 'btn-primary');
    document.getElementById('btnViewTable').classList.add('btn-outline-primary');
  });

  // Default view: Cards View
  secCards.style.display = '';
  secTable.style.display = 'none';

  // 4. Status Filter Pills Click Handler
  statusPills.forEach(function (pill) {
    pill.addEventListener('click', function () {
      statusPills.forEach(p => p.classList.remove('active'));
      this.classList.add('active');
      dashboardState.status = this.getAttribute('data-status');
      applyFilters();
    });
  });

  // Search input handlers
  if (elSearch) {
    elSearch.addEventListener('input', function () {
      dashboardState.search = this.value;
      applyFilters();
    });
  }

  if (elClearSearch) {
    elClearSearch.addEventListener('click', function () {
      if (elSearch) elSearch.value = '';
      dashboardState.search = '';
      applyFilters();
    });
  }

  // 5. AJAX Data Fetcher & Live Updater
  function fetchLiveStatus(showSpinner) {
    if (showSpinner && elIconSpin) {
      elIconSpin.classList.add('spin-icon');
    }

    var festVal = elFestFilter ? elFestFilter.value : 'All';
    var dateVal = elDateFilter ? elDateFilter.value : 'All';

    // Determine current district or subdistrict AJAX URL
    var isDistrict = window.location.pathname.includes('/district/');
    var endpoint = isDistrict
      ? '<?php echo base_url(); ?>district/report/afterresult_report/get_live_status'
      : '<?php echo base_url(); ?>report/afterresultreport/get_live_status';

    var formData = new FormData();
    formData.append('fest', festVal);
    formData.append('date', dateVal);

    fetch(endpoint, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(res => {
      if (showSpinner && elIconSpin) {
        setTimeout(() => elIconSpin.classList.remove('spin-icon'), 400);
      }

      if (res.status === 'success' && res.data) {
        updateDashboardData(res.data);
      }
    })
    .catch(err => {
      if (showSpinner && elIconSpin) {
        elIconSpin.classList.remove('spin-icon');
      }
      console.error('Error fetching live status:', err);
    });
  }

  // Update DOM with fresh live status data
  function updateDashboardData(data) {
    var sum = data.summary;
    var std = data.student_stats;
    var awd = data.awards_stats;
    var log = data.logistics_stats;

    // 1. Update Core KPI Cards
    document.getElementById('kpiTotalEvents').textContent = sum.total;
    document.getElementById('kpiCompletedEvents').textContent = sum.completed;
    document.getElementById('kpiCompletedPct').textContent = sum.percentage + '%';
    document.getElementById('kpiLiveEvents').textContent = sum.live;
    document.getElementById('kpiPendingEvents').textContent = sum.pending;
    document.getElementById('kpiProgressText').textContent = sum.percentage + '%';
    document.getElementById('kpiProgressRatio').textContent = sum.completed + ' of ' + sum.total + ' Finished';

    // SVG Circular Progress
    var svgPath = document.getElementById('svgProgressPath');
    if (svgPath) {
      svgPath.setAttribute('stroke-dasharray', sum.percentage + ', 100');
    }
    var svgLbl = document.getElementById('svgProgressPctLabel');
    if (svgLbl) svgLbl.textContent = sum.percentage + '%';

    // 2. Update Student Statistics
    document.getElementById('statCompStudents').textContent = std.comp_total;
    document.getElementById('statCompBoys').innerHTML = '👦 ஆண்: <strong>' + std.comp_boys + '</strong>';
    document.getElementById('statCompGirls').innerHTML = '👧 பெண்: <strong>' + std.comp_girls + '</strong>';

    document.getElementById('statPendStudents').textContent = std.pend_total;
    document.getElementById('statPendBoys').innerHTML = '👦 ஆண்: <strong>' + std.pend_boys + '</strong>';
    document.getElementById('statPendGirls').innerHTML = '👧 பெண்: <strong>' + std.pend_girls + '</strong>';

    document.getElementById('statCertAwarded').textContent = awd.cert_awarded;
    document.getElementById('statCertPrinted').innerHTML = '<i class="bi bi-printer text-success me-1"></i>அச்சிடப்பட்டவை: <strong>' + awd.cert_printed + '</strong>';
    document.getElementById('statRankHolders').innerHTML = '<i class="bi bi-trophy text-warning me-1"></i>Ranks: <strong>' + awd.rank_holders + '</strong>';

    document.getElementById('statTotalStages').textContent = log.total_stages;
    document.getElementById('statTotalJudges').textContent = log.total_judges;
    document.getElementById('statTotalSchools').textContent = log.participating_schools;

    // 3. Update Live Banner & Hero Status Pill
    var showcase = document.getElementById('liveShowcaseContainer');
    var heroPill = document.getElementById('heroLiveStatusPill');
    var heroDot = document.getElementById('heroLiveDot');
    var heroText = document.getElementById('heroLiveStatusText');

    if (sum.has_live) {
      if (showcase) showcase.style.display = '';
      if (heroPill) heroPill.className = 'hero-live-pill status-broadcasting';
      if (heroDot) heroDot.className = 'hero-live-dot dot-red';
      if (heroText) heroText.textContent = 'ON-STAGE BROADCAST LIVE';

      if (sum.live_events && sum.live_events.length > 0) {
        var ev = sum.live_events[0];
        document.getElementById('liveEventTitle').textContent = '#' + ev.item_code + ' - ' + ev.item_name;
        document.getElementById('liveEventVenue').innerHTML = '<i class="bi bi-geo-alt-fill text-danger me-1"></i>Stage: <strong>' + ev.stage_name + '</strong> (' + ev.fest_name + ')';
      }
    } else {
      if (showcase) showcase.style.display = 'none';
      if (heroPill) heroPill.className = 'hero-live-pill status-standby';
      if (heroDot) heroDot.className = 'hero-live-dot dot-green';
      if (heroText) heroText.textContent = 'LIVE MONITOR ACTIVE';
    }

    // 4. Update Pills
    document.getElementById('pillCountAll').textContent = sum.total;
    document.getElementById('pillCountComp').textContent = sum.completed;
    document.getElementById('pillCountLive').textContent = sum.live;
    document.getElementById('pillCountPend').textContent = sum.pending;

    // 5. Update Charts
    if (chartStatus) {
      chartStatus.updateSeries([sum.completed, sum.live, sum.pending]);
    }

    var newCatComp = [];
    var newCatPend = [];
    data.categories.forEach(function (c) {
      newCatComp.push(c.completed);
      newCatPend.push(c.pending);
    });

    if (chartCategory) {
      chartCategory.updateSeries([{
        name: 'Completed (முடிவடைந்தவை)',
        data: newCatComp
      }, {
        name: 'Pending (மீதமுள்ளவை)',
        data: newCatPend
      }]);
    }

    // Update Mini Donut legend
    var dComp = document.getElementById('donutLblCompleted');
    if (dComp) dComp.textContent = sum.completed;
    var dLive = document.getElementById('donutLblLive');
    if (dLive) dLive.textContent = sum.live;
    var dPend = document.getElementById('donutLblPending');
    if (dPend) dPend.textContent = sum.pending;
    var barMini = document.getElementById('barMiniProgress');
    if (barMini) barMini.style.width = sum.percentage + '%';
    var barMiniPct = document.getElementById('barMiniPct');
    if (barMiniPct) barMiniPct.textContent = sum.percentage + '%';

    // Update Timestamp
    var lblUpd = document.getElementById('lblLastUpdated');
    if (lblUpd) lblUpd.innerHTML = '<i class="bi bi-clock text-info me-1"></i> Updated: ' + sum.timestamp;

    applyFilters();
  }

  // Filter Dropdown Change Handlers
  if (elFestFilter) {
    elFestFilter.addEventListener('change', function () {
      dashboardState.fest = this.value;
      fetchLiveStatus(true);
    });
  }

  if (elDateFilter) {
    elDateFilter.addEventListener('change', function () {
      dashboardState.date = this.value;
      fetchLiveStatus(true);
    });
  }

  // Manual Refresh
  if (elBtnRefresh) {
    elBtnRefresh.addEventListener('click', function () {
      dashboardState.timerSeconds = 30;
      fetchLiveStatus(true);
    });
  }

  // 6. Auto-Refresh Ticker Timer (30 seconds)
  dashboardState.timerInterval = setInterval(function () {
    if (!dashboardState.autoRefresh) return;

    dashboardState.timerSeconds--;
    if (elTimerCount) {
      elTimerCount.textContent = dashboardState.timerSeconds;
    }

    if (dashboardState.timerSeconds <= 0) {
      dashboardState.timerSeconds = 30;
      fetchLiveStatus(false);
    }
  }, 1000);

  if (elAutoRefresh) {
    elAutoRefresh.addEventListener('change', function () {
      dashboardState.autoRefresh = this.checked;
      if (this.checked) {
        dashboardState.timerSeconds = 30;
        if (elTimerCount) elTimerCount.textContent = '30';
      } else {
        if (elTimerCount) elTimerCount.textContent = 'Off';
      }
    });
  }

  // 7. Export PDF Action: Opens status_of_kalolsavam1 with chosen filters
  if (elBtnExportPdf) {
    elBtnExportPdf.addEventListener('click', function () {
      var festVal = elFestFilter ? elFestFilter.value : 'All';
      var dateVal = elDateFilter ? elDateFilter.value : 'All';

      var isDistrict = window.location.pathname.includes('/district/');
      var targetUrl = isDistrict
        ? '<?php echo base_url(); ?>district/report/afterresult_reportpdf/status_of_kalolsavam1'
        : '<?php echo base_url(); ?>report/afterresultreportpdf/status_of_kalolsavam1';

      // Create a dynamic form to submit POST in a new tab
      var form = document.createElement('form');
      form.method = 'POST';
      form.action = targetUrl;
      form.target = '_blank';

      var inputFest = document.createElement('input');
      inputFest.type = 'hidden';
      inputFest.name = 'cmbFestType';
      inputFest.value = festVal;
      form.appendChild(inputFest);

      var inputDate = document.createElement('input');
      inputDate.type = 'hidden';
      inputDate.name = 'festdate';
      inputDate.value = dateVal;
      form.appendChild(inputDate);

      document.body.appendChild(form);
      form.submit();
      document.body.removeChild(form);
    });
  }

});
</script>

