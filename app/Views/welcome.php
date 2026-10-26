<?php
/**
 * Advanced Sub-District Analytics CRM Dashboard
 * Strictly scoped to the active Sub-District with dynamic Stage Allotment vs Mark Entry switching
 */
$is_stage_alloted = !empty($is_all_stage_alloted);
$counts = $counts ?? [
    'schools' => 0, 'finalized_schools' => 0, 'unfinalized_schools' => 0, 'school_pct' => 0,
    'participants' => 0, 'boys' => 0, 'girls' => 0,
    'items' => 0, 'completed_items' => 0, 'pending_items' => 0, 'item_pct' => 0,
    'published_results' => 0, 'published_pct' => 0, 'stages_count' => 0, 'active_stages' => 0
];
?>
<!-- Styles for Advanced CRM Analytics Dashboard -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap');

:root {
  --crm-bg: #f8fafc;
  --crm-card-bg: #ffffff;
  --crm-text-main: #0f172a;
  --crm-text-muted: #64748b;
  --crm-primary: #6366f1;
  --crm-primary-dark: #4338ca;
  --crm-gradient-hero: linear-gradient(135deg, #0f172a 0%, #1e1b4b 45%, #2e1065 100%);
  --crm-gradient-indigo: linear-gradient(135deg, #4338ca 0%, #6366f1 50%, #8b5cf6 100%);
  --crm-gradient-emerald: linear-gradient(135deg, #065f46 0%, #059669 50%, #10b981 100%);
  --crm-gradient-amber: linear-gradient(135deg, #92400e 0%, #d97706 50%, #f59e0b 100%);
  --crm-gradient-rose: linear-gradient(135deg, #9f1239 0%, #e11d48 50%, #f43f5e 100%);
  --crm-gradient-cyan: linear-gradient(135deg, #0e7490 0%, #0284c7 50%, #06b6d4 100%);
  --crm-gradient-violet: linear-gradient(135deg, #581c87 0%, #7c3aed 50%, #a855f7 100%);
  --crm-border: #e2e8f0;
  --crm-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.06), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
  --crm-shadow-hover: 0 20px 35px -5px rgba(99, 102, 241, 0.18), 0 10px 15px -5px rgba(99, 102, 241, 0.1);
  --crm-radius: 18px;
}

.crm-dashboard {
  font-family: 'Outfit', 'Poppins', system-ui, -apple-system, sans-serif;
  color: var(--crm-text-main);
  background: var(--crm-bg);
  min-height: 100vh;
}

/* Animations */
@keyframes crmFadeUp {
  from {
    opacity: 0;
    transform: translateY(22px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulseRadar {
  0% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
  }
  70% {
    transform: scale(1.1);
    box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
  }
  100% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
  }
}

@keyframes liveSoundwave {
  0%, 100% { height: 4px; }
  50% { height: 18px; }
}

@keyframes shimmerGlow {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

.anim-fade-up {
  animation: crmFadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) backwards;
}

.delay-1 { animation-delay: 0.08s; }
.delay-2 { animation-delay: 0.16s; }
.delay-3 { animation-delay: 0.24s; }
.delay-4 { animation-delay: 0.32s; }
.delay-5 { animation-delay: 0.40s; }

/* Hero Banner */
.crm-hero {
  background: var(--crm-gradient-hero);
  border-radius: var(--crm-radius);
  color: #ffffff;
  padding: 34px 40px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.5);
  margin-bottom: 28px;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.crm-hero::before {
  content: '';
  position: absolute;
  top: -60%;
  right: -15%;
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, rgba(124, 58, 237, 0.35) 0%, rgba(99, 102, 241, 0.1) 50%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}

.crm-hero::after {
  content: '';
  position: absolute;
  bottom: -40%;
  left: 20%;
  width: 350px;
  height: 350px;
  background: radial-gradient(circle, rgba(236, 72, 153, 0.2) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}

.hero-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  padding: 6px 16px;
  border-radius: 30px;
  font-size: 0.85rem;
  font-weight: 600;
  letter-spacing: 0.3px;
}

.hero-live-clock {
  font-family: 'Outfit', monospace;
  font-size: 1.15rem;
  font-weight: 700;
  background: rgba(0, 0, 0, 0.35);
  padding: 6px 18px;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.15);
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #38bdf8;
}

/* Glass & Modern Cards */
.crm-card {
  background: var(--crm-card-bg);
  border-radius: var(--crm-radius);
  border: 1px solid var(--crm-border);
  box-shadow: var(--crm-shadow);
  transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.28s cubic-bezier(0.16, 1, 0.3, 1);
  position: relative;
  overflow: hidden;
}

.crm-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--crm-shadow-hover);
}

/* KPI Stat Cards */
.kpi-card {
  color: #ffffff;
  padding: 24px;
  border-radius: var(--crm-radius);
  position: relative;
  overflow: hidden;
  box-shadow: 0 12px 25px -5px rgba(0, 0, 0, 0.15);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  min-height: 155px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.kpi-card:hover {
  transform: translateY(-6px) scale(1.01);
}

.kpi-indigo  { background: var(--crm-gradient-indigo); }
.kpi-emerald { background: var(--crm-gradient-emerald); }
.kpi-amber   { background: var(--crm-gradient-amber); }
.kpi-rose    { background: var(--crm-gradient-rose); }
.kpi-cyan    { background: var(--crm-gradient-cyan); }

.kpi-title {
  font-size: 0.82rem;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 700;
  opacity: 0.88;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.kpi-number {
  font-size: 2.35rem;
  font-weight: 800;
  letter-spacing: -0.5px;
  margin: 6px 0 2px 0;
  line-height: 1.1;
}

.kpi-subtitle {
  font-size: 0.82rem;
  opacity: 0.9;
  display: flex;
  align-items: center;
  gap: 6px;
}

.kpi-bg-icon {
  position: absolute;
  right: -10px;
  bottom: -15px;
  font-size: 5.5rem;
  opacity: 0.14;
  transform: rotate(-10deg);
  pointer-events: none;
}

/* Pipeline Stepper */
.pipeline-bar {
  background: #ffffff;
  border-radius: var(--crm-radius);
  padding: 24px 28px;
  border: 1px solid var(--crm-border);
  box-shadow: var(--crm-shadow);
  margin-bottom: 28px;
}

.pipeline-step {
  position: relative;
  text-align: center;
  padding: 10px 8px;
  border-radius: 12px;
  transition: all 0.2s ease;
}

.pipeline-step:hover {
  background: #f1f5f9;
}

.pipeline-icon-box {
  width: 46px;
  height: 46px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  margin-bottom: 8px;
  transition: transform 0.2s ease;
}

.pipeline-step:hover .pipeline-icon-box {
  transform: scale(1.1);
}

.pipe-complete {
  background: #ecfdf5;
  color: #059669;
  border: 2px solid #10b981;
}

.pipe-active {
  background: #eff6ff;
  color: #2563eb;
  border: 2px solid #3b82f6;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
}

.pipe-pending {
  background: #f8fafc;
  color: #94a3b8;
  border: 2px dashed #cbd5e1;
}

.pipe-locked {
  background: #fff1f2;
  color: #e11d48;
  border: 2px dashed #fda4af;
  opacity: 0.8;
}

/* Spotlight Search */
.spotlight-wrapper {
  position: relative;
  margin-bottom: 28px;
}

.spotlight-input-box {
  background: #ffffff;
  border-radius: 16px;
  border: 2px solid #cbd5e1;
  padding: 14px 22px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
  display: flex;
  align-items: center;
  gap: 14px;
  transition: all 0.25s ease;
}

.spotlight-input-box:focus-within {
  border-color: #6366f1;
  box-shadow: 0 12px 30px rgba(99, 102, 241, 0.18);
  transform: translateY(-2px);
}

.spotlight-input {
  border: none;
  outline: none;
  font-size: 1.05rem;
  font-weight: 500;
  width: 100%;
  color: #0f172a;
}

.spotlight-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: #ffffff;
  border-radius: 16px;
  margin-top: 8px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  border: 1px solid #e2e8f0;
  z-index: 1050;
  max-height: 380px;
  overflow-y: auto;
  display: none;
}

.spotlight-item {
  padding: 12px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #f1f5f9;
  text-decoration: none;
  color: #1e293b;
  transition: background 0.15s ease;
}

.spotlight-item:hover {
  background: #f8fafc;
  color: #4f46e5;
}

/* Command Center Action Pods */
.action-pod {
  background: #ffffff;
  border-radius: var(--crm-radius);
  border: 1px solid var(--crm-border);
  padding: 22px;
  height: 100%;
  box-shadow: var(--crm-shadow);
  transition: all 0.25s ease;
}

.action-pod:hover {
  box-shadow: var(--crm-shadow-hover);
  border-color: #cbd5e1;
}

.pod-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 18px;
  padding-bottom: 12px;
  border-bottom: 2px solid #f1f5f9;
}

.pod-icon {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  color: #ffffff;
}

.action-item-link {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  border-radius: 12px;
  color: #334155;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.92rem;
  margin-bottom: 8px;
  background: #f8fafc;
  border: 1px solid #f1f5f9;
  transition: all 0.2s ease;
}

.action-item-link:hover {
  background: #eef2ff;
  border-color: #c7d2fe;
  color: #4338ca;
  transform: translateX(4px);
}

.action-item-link i.chevron {
  font-size: 0.85rem;
  color: #94a3b8;
  transition: transform 0.2s ease;
}

.action-item-link:hover i.chevron {
  color: #4338ca;
  transform: translateX(3px);
}

/* Special High-Priority Button */
.priority-action-btn {
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  color: #ffffff !important;
  border: none;
  box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35);
}

.priority-action-btn:hover {
  background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
  color: #ffffff !important;
  transform: translateX(4px) scale(1.02);
  box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45);
}

.priority-action-btn i.chevron {
  color: rgba(255, 255, 255, 0.8) !important;
}

/* Stage Allotment Warning CTA */
.allotment-alert-card {
  background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
  border: 2px solid #f59e0b;
  border-radius: 16px;
  padding: 20px 24px;
  margin-bottom: 28px;
  box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.15);
}

/* Live Stage Tracker Cards */
.live-stage-grid-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid var(--crm-border);
  box-shadow: var(--crm-shadow);
  padding: 20px;
  margin-bottom: 20px;
  transition: all 0.28s ease;
  position: relative;
  overflow: hidden;
}

.live-stage-grid-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--crm-shadow-hover);
}

.live-stage-grid-card.is-live {
  border-left: 6px solid #ef4444;
}

.live-stage-grid-card.is-delayed {
  border-left: 6px solid #f59e0b;
  background: linear-gradient(to right, #fffbeb 0%, #ffffff 20%);
}

.live-stage-grid-card.is-completed {
  border-left: 6px solid #10b981;
}

.live-stage-grid-card.is-scheduled {
  border-left: 6px solid #3b82f6;
}

/* Suppress blue border and outline on reload and target navigation */
#liveStageTracker,
#liveStageTracker:target,
#liveStageTracker:focus,
#liveStageTracker:focus-visible,
#liveStageTracker:active,
#liveStageTracker .card-header,
#liveStageTracker .card-header:focus,
#liveStageTracker .card-header:focus-visible {
  outline: none !important;
  border: none !important;
  box-shadow: var(--crm-shadow) !important;
}

:target {
  outline: none !important;
}

.stage-date-filter-btn.is-loading {
  opacity: 0.75;
  pointer-events: none;
}

.radar-dot {
  display: inline-block;
  width: 10px;
  height: 10px;
  background-color: #ef4444;
  border-radius: 50%;
  animation: pulseRadar 1.6s infinite;
  margin-right: 6px;
}

.soundwave-box {
  display: inline-flex;
  align-items: flex-end;
  gap: 2px;
  height: 18px;
  margin-left: 8px;
}

.soundwave-bar {
  width: 3px;
  background-color: #ef4444;
  border-radius: 2px;
  animation: liveSoundwave 1s ease-in-out infinite alternate;
}

.soundwave-bar:nth-child(2) { animation-delay: 0.2s; }
.soundwave-bar:nth-child(3) { animation-delay: 0.4s; }
.soundwave-bar:nth-child(4) { animation-delay: 0.1s; }

/* Leaderboard Ranking Badges */
.rank-badge-1 { background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%); color: #ffffff; }
.rank-badge-2 { background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%); color: #ffffff; }
.rank-badge-3 { background: linear-gradient(135deg, #b45309 0%, #78350f 100%); color: #ffffff; }

/* Responsive adjustments */
@media (max-width: 768px) {
  .crm-hero {
    padding: 24px 20px;
  }
  .kpi-number {
    font-size: 1.9rem;
  }
  .pipeline-bar {
    padding: 16px;
    overflow-x: auto;
  }
  .pipeline-step {
    min-width: 110px;
  }
}
</style>

<!-- Load ApexCharts and Google Fonts -->
<script src="<?php echo base_url(); ?>assets/apexcharts.js"></script>

<!-- Immediate Early (X, Y) Scroll Restoration Script -->
<script>
(function() {
    try {
        if ('scrollRestoration' in history) { history.scrollRestoration = 'manual'; }
        var sX = sessionStorage.getItem('stageTrackerScrollX') || 0;
        var sY = sessionStorage.getItem('stageTrackerScrollY');
        var sT = sessionStorage.getItem('stageTrackerReloadTime');
        if (sY !== null && sT && (Date.now() - parseInt(sT, 10) < 300000)) {
            window.scrollTo(parseInt(sX, 10), parseInt(sY, 10));
        }
    } catch (e) {}
})();
</script>

<div class="crm-dashboard py-4 px-3 px-md-4">

    <!-- 1. HERO BANNER: Live Clock and Festival Metadata -->
    <div class="crm-hero anim-fade-up">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h1 class="display-6 fw-bold mb-2 text-white">
                    <?php echo esc($welcome_label); ?>
                </h1>

                <?php if (!empty($fest_name)): ?>
                    <h5 class="mb-3" style="color: #cbd5e1; font-weight: 500;">
                        <i class="bi bi-music-note-beamed text-warning me-2"></i><?php echo esc($fest_name); ?>
                    </h5>
                <?php endif; ?>

                <div class="d-flex flex-wrap gap-3 mt-2 text-white-50" style="font-size: 0.92rem;">
                    <?php if (!empty($venue)): ?>
                        <span><i class="bi bi-geo-alt-fill text-danger me-1"></i><strong class="text-white">Venue:</strong> <?php echo esc($venue); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($start_date) && $start_date != '0000-00-00'): ?>
                        <span><i class="bi bi-calendar3 text-info me-1"></i><strong class="text-white">Date:</strong> <?php echo date('d M Y', strtotime($start_date)) . ' - ' . date('d M Y', strtotime($end_date)); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4 text-lg-end text-start">
                <!-- Live Clock -->
                <div class="hero-live-clock mb-3">
                    <i class="bi bi-clock-history text-info"></i>
                    <span id="liveClockDisplay">--:--:--</span>
                </div>
                <div class="text-white-50 small" id="liveDateDisplay">
                    <?php echo date('l, d F Y'); ?>
                </div>

                <!-- Quick Public Results Portal -->
                <div class="mt-3">
                    <a href="<?php echo base_url('publishresult/publicresult/resultview'); ?>" class="btn btn-sm btn-outline-light rounded-pill px-3 shadow-sm" target="_blank">
                        <i class="bi bi-trophy-fill text-warning me-1"></i> Public Scoreboard <i class="bi bi-box-arrow-up-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. STAGE ALLOTMENT CONDITIONAL STATUS BANNER (English Only) -->
    <?php if (!$is_stage_alloted): ?>
        <div class="allotment-alert-card anim-fade-up delay-1">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="font-size: 2.2rem; color: #d97706;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">
                            Stage Allotment Incomplete (Action Required)
                        </h5>
                        <div class="text-muted" style="font-size: 0.92rem;">
                            Stage allocation is pending. <strong>Competition Result & Mark Entry is temporarily locked</strong> to safeguard event integrity. Allocate items to stages to automatically unlock live scoring.
                        </div>
                    </div>
                </div>
                <div>
                    <a href="<?php echo base_url('stage/allotment'); ?>" class="btn btn-warning fw-bold px-4 py-2 shadow-sm rounded-pill text-dark d-inline-flex align-items-center gap-2">
                        <i class="bi bi-easel2-fill"></i> Allot Stages Now
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow-sm border-0 mb-4 anim-fade-up delay-1" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-left: 5px solid #10b981 !important;">
            <div class="card-body py-3 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                    <div>
                        <strong class="text-dark">Stage Allotment Completed &mdash; Ready for Results &amp; Scoring!</strong>
                        <div class="text-muted small">All competition items have been assigned to festival stages. <strong>Mark Entry & Result Evaluation is now active</strong>.</div>
                    </div>
                </div>
                <a href="<?php echo base_url('result/resultentry'); ?>" class="btn btn-sm btn-success fw-bold rounded-pill px-3 py-1 shadow-sm d-inline-flex align-items-center gap-1">
                    <i class="bi bi-award-fill"></i> Enter Competition Marks
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- 3. TOP KPI CRM METRIC CARDS (Count-up numerical animation) -->
    <div class="row g-3 mb-4 anim-fade-up delay-1">
        <!-- Schools -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-indigo">
                <div class="kpi-title">
                    <span>Enrolled Schools</span>
                    <i class="bi bi-buildings"></i>
                </div>
                <div class="kpi-number count-up" data-target="<?php echo $counts['schools']; ?>">0</div>
                <div class="kpi-subtitle">
                    <i class="bi bi-check2-circle"></i>
                    <span>Finalized: <strong><?php echo $counts['finalized_schools']; ?></strong> (<?php echo $counts['school_pct']; ?>%)</span>
                </div>
                <i class="bi bi-buildings kpi-bg-icon"></i>
            </div>
        </div>

        <!-- Participants -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-emerald">
                <div class="kpi-title">
                    <span>Total Participants</span>
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="kpi-number count-up" data-target="<?php echo $counts['participants']; ?>">0</div>
                <div class="kpi-subtitle">
                    <i class="bi bi-gender-ambiguous"></i>
                    <span>Boys: <strong><?php echo $counts['boys']; ?></strong> | Girls: <strong><?php echo $counts['girls']; ?></strong></span>
                </div>
                <i class="bi bi-people-fill kpi-bg-icon"></i>
            </div>
        </div>

        <!-- Competition Items -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-amber">
                <div class="kpi-title">
                    <span>Competition Items</span>
                    <i class="bi bi-trophy"></i>
                </div>
                <div class="kpi-number count-up" data-target="<?php echo $counts['items']; ?>">0</div>
                <div class="kpi-subtitle">
                    <i class="bi bi-flag-fill"></i>
                    <span>Finished: <strong><?php echo $counts['completed_items']; ?></strong> (<?php echo $counts['item_pct']; ?>%)</span>
                </div>
                <i class="bi bi-trophy kpi-bg-icon"></i>
            </div>
        </div>

        <!-- Results & Published -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-rose">
                <div class="kpi-title">
                    <span>Published Results</span>
                    <i class="bi bi-broadcast"></i>
                </div>
                <div class="kpi-number count-up" data-target="<?php echo $counts['published_results']; ?>">0</div>
                <div class="kpi-subtitle">
                    <i class="bi bi-clock-history"></i>
                    <span>Pending: <strong><?php echo $counts['pending_items']; ?></strong> items remaining</span>
                </div>
                <i class="bi bi-broadcast kpi-bg-icon"></i>
            </div>
        </div>
    </div>

    <!-- 4. FESTIVAL LIFECYCLE PROGRESS PIPELINE (6-Phase Stepper) -->
    <div class="pipeline-bar anim-fade-up delay-2">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold text-dark mb-0">
                <i class="bi bi-kanban-fill text-primary me-2"></i>Festival Operations Lifecycle Pipeline
            </h6>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                Live State Tracking
            </span>
        </div>

        <div class="row g-2 justify-content-between">
            <?php foreach ($pipeline as $pKey => $pStep): ?>
                <?php
                $stepClass = 'pipe-pending';
                if ($pStep['status'] === 'COMPLETED') $stepClass = 'pipe-complete';
                elseif ($pStep['status'] === 'ACTIVE') $stepClass = 'pipe-active';
                elseif ($pStep['status'] === 'LOCKED') $stepClass = 'pipe-locked';
                ?>
                <div class="col-md-2 col-4">
                    <a href="<?php echo $pStep['url']; ?>" class="text-decoration-none d-block">
                        <div class="pipeline-step">
                            <div class="pipeline-icon-box <?php echo $stepClass; ?>">
                                <i class="bi <?php echo $pStep['icon']; ?>"></i>
                            </div>
                            <div class="fw-bold text-dark small text-truncate"><?php echo esc($pStep['name']); ?></div>
                            <div class="text-muted" style="font-size: 0.72rem;"><?php echo esc($pStep['sub_title'] ?? $pStep['tamil_title'] ?? ''); ?></div>
                            <div class="badge <?php echo ($pStep['status'] === 'COMPLETED') ? 'bg-success' : (($pStep['status'] === 'ACTIVE') ? 'bg-primary' : (($pStep['status'] === 'LOCKED') ? 'bg-danger' : 'bg-secondary')); ?> rounded-pill mt-1" style="font-size: 0.68rem;">
                                <?php echo $pStep['pct']; ?>% &bull; <?php echo $pStep['status']; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 5. SPOTLIGHT INSTANT QUICK-FIND SEARCH BAR -->
    <div class="spotlight-wrapper anim-fade-up delay-2">
        <div class="spotlight-input-box">
            <i class="bi bi-search text-primary fs-5"></i>
            <input type="text" id="spotlightSearch" class="spotlight-input" placeholder="Spotlight Quick Find: Item #, Name (#101, Bharathanatyam), Stage, or School... (Type to jump)" autocomplete="off">
            <span class="badge bg-light text-muted border px-2 py-1 small">Press ESC to clear</span>
        </div>

        <!-- Spotlight Dropdown Results -->
        <div id="spotlightDropdown" class="spotlight-dropdown"></div>
    </div>

    <!-- 6. COMMAND CENTER (Segmented 4-Pod Operational Hub) -->
    <div class="row g-3 mb-4 anim-fade-up delay-3">
        
        <!-- Pod 1: Registration Desk -->
        <div class="col-lg-3 col-md-6">
            <div class="action-pod">
                <div class="pod-header">
                    <div class="pod-icon" style="background: var(--crm-gradient-indigo);">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">School & Registration</h6>
                        <small class="text-muted">Enrolment & ID Desk</small>
                    </div>
                </div>

                <a href="<?php echo base_url('schools/school_entry'); ?>" class="action-item-link">
                    <span><i class="bi bi-person-plus-fill text-primary me-2"></i>School Entry (Photo)</span>
                    <i class="bi bi-chevron-right chevron"></i>
                </a>
                <a href="<?php echo base_url('schools/school_master'); ?>" class="action-item-link">
                    <span><i class="bi bi-building text-secondary me-2"></i>School Master Profile</span>
                    <i class="bi bi-chevron-right chevron"></i>
                </a>
                <a href="<?php echo base_url('report/prereport/participant_cardindex_photo'); ?>" class="action-item-link" target="_blank">
                    <span><i class="bi bi-card-image text-success me-2"></i>Photo ID Cards Print</span>
                    <i class="bi bi-chevron-right chevron"></i>
                </a>
                <a href="<?php echo base_url('report/prereport/list_school'); ?>" class="action-item-link" target="_blank">
                    <span><i class="bi bi-card-checklist text-info me-2"></i>Participating Schools</span>
                    <i class="bi bi-chevron-right chevron"></i>
                </a>
            </div>
        </div>

        <!-- Pod 2: Stage & Scheduling Control -->
        <div class="col-lg-3 col-md-6">
            <div class="action-pod">
                <div class="pod-header">
                    <div class="pod-icon" style="background: var(--crm-gradient-amber);">
                        <i class="bi bi-easel2"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Stage & Scheduling</h6>
                        <small class="text-muted">Venue & Program Dispatch</small>
                    </div>
                </div>

                <!-- Stage Manager Console Quick Access -->
                <a href="<?php echo base_url('stage/manager'); ?>" class="action-item-link priority-action-btn" style="background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);">
                    <span><i class="bi bi-broadcast-pin me-2"></i>Stage Manager Console</span>
                    <i class="bi bi-chevron-right chevron"></i>
                </a>

                <a href="<?php echo base_url('stage/stage_details'); ?>" class="action-item-link">
                    <span><i class="bi bi-sliders text-warning me-2"></i>Define Stages</span>
                    <i class="bi bi-chevron-right chevron"></i>
                </a>

                <!-- DYNAMIC SWITCHING: Stage Allotment is shown ONLY when NOT yet completed -->
                <?php if (!$is_stage_alloted): ?>
                    <a href="<?php echo base_url('stage/allotment'); ?>" class="action-item-link priority-action-btn">
                        <span><i class="bi bi-easel2-fill me-2"></i>Stage Allotment (Allot Now)</span>
                        <i class="bi bi-chevron-right chevron"></i>
                    </a>
                <?php endif; ?>

                <a href="<?php echo base_url('stage/festival_scheduling'); ?>" class="action-item-link">
                    <span><i class="bi bi-calendar-range text-info me-2"></i>Auto Set Stage Schedule</span>
                    <i class="bi bi-chevron-right chevron"></i>
                </a>
                <a href="<?php echo base_url('report/prereport/callsheet_first'); ?>" class="action-item-link" target="_blank">
                    <span><i class="bi bi-file-earmark-text text-primary me-2"></i>Stage Call Sheets</span>
                    <i class="bi bi-chevron-right chevron"></i>
                </a>
                <a href="<?php echo base_url('stage/festival_scheduling/order_of_events'); ?>" class="action-item-link">
                    <span><i class="bi bi-list-ol text-dark me-2"></i>Order of Events</span>
                    <i class="bi bi-chevron-right chevron"></i>
                </a>
            </div>
        </div>

        <!-- Pod 3: Results & Marks Desk -->
        <div class="col-lg-3 col-md-6">
            <div class="action-pod">
                <div class="pod-header">
                    <div class="pod-icon" style="background: var(--crm-gradient-emerald);">
                        <i class="bi bi-trophy"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Marks & Results</h6>
                        <small class="text-muted">Evaluation & Judgement</small>
                    </div>
                </div>

                <!-- DYNAMIC SWITCHING: Mark Entry is shown ONLY when Stage Allotment IS completed -->
                <?php if ($is_stage_alloted): ?>
                    <a href="<?php echo base_url('result/resultentry'); ?>" class="action-item-link priority-action-btn">
                        <span><i class="bi bi-award-fill me-2"></i>Enter Competition Marks</span>
                        <i class="bi bi-chevron-right chevron"></i>
                    </a>
                    <a href="<?php echo base_url('result/resultentry/item_result_list'); ?>" class="action-item-link">
                        <span><i class="bi bi-check2-all text-success me-2"></i>Item Result Confirmation</span>
                        <i class="bi bi-chevron-right chevron"></i>
                    </a>
                <?php else: ?>
                    <div class="p-2 mb-2 bg-light border border-dashed rounded text-muted small text-center">
                        <i class="bi bi-lock-fill text-warning me-1"></i>
                        Result entry will unlock automatically after stage allotment
                    </div>
                <?php endif; ?>

                <a href="<?php echo base_url('publishresult/resultindex/resultview'); ?>" class="action-item-link">
                    <span><i class="bi bi-broadcast text-danger me-2"></i>Publish Results Live</span>
                    <i class="bi bi-chevron-right chevron"></i>
                </a>
                <a href="<?php echo base_url('schools/special_order_entry'); ?>" class="action-item-link">
                    <span><i class="bi bi-shield-exclamation text-warning me-2"></i>Special Orders / Appeals</span>
                    <i class="bi bi-chevron-right chevron"></i>
                </a>
            </div>
        </div>

        <!-- Pod 4: Reports, Tabulation & District Export -->
        <div class="col-lg-3 col-md-6">
            <div class="action-pod">
                <div class="pod-header">
                    <div class="pod-icon" style="background: var(--crm-gradient-violet);">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Tabulation & Export</h6>
                        <small class="text-muted">Reports & District Qualification</small>
                    </div>
                </div>

                <a href="<?php echo base_url('report/afterresultreport/school_wise_result_interface'); ?>" class="action-item-link" target="_blank">
                    <span><i class="bi bi-bar-chart-fill text-primary me-2"></i>School-Wise Points</span>
                    <i class="bi bi-chevron-right chevron"></i>
                </a>
                <a href="<?php echo base_url('report/afterresultreport/status_of_kalolsavam_interface'); ?>" class="action-item-link" target="_blank">
                    <span><i class="bi bi-pie-chart-fill text-info me-2"></i>Status of Kalaivizha</span>
                    <i class="bi bi-chevron-right chevron"></i>
                </a>
                <a href="<?php echo base_url('report/resultindex/rankwise_result'); ?>" class="action-item-link" target="_blank">
                    <span><i class="bi bi-award text-success me-2"></i>Rank-Wise Results</span>
                    <i class="bi bi-chevron-right chevron"></i>
                </a>
                <a href="<?php echo base_url('certificate/certificate'); ?>" class="action-item-link">
                    <span><i class="bi bi-patch-check text-warning me-2"></i>Certificate Generation</span>
                    <i class="bi bi-chevron-right chevron"></i>
                </a>
                <a href="<?php echo base_url('report/afterresultreport/higherlevel_result'); ?>" class="action-item-link">
                    <span><i class="bi bi-cloud-arrow-up-fill text-danger me-2"></i>District Export</span>
                    <i class="bi bi-chevron-right chevron"></i>
                </a>
            </div>
        </div>

    </div>

    <!-- 7. LIVE STAGES TRACKER (Strict Sub-District Only) -->
    <?php if ($is_stage_alloted): ?>
    <div id="liveStageTracker" class="card shadow-sm border-0 mb-4 anim-fade-up delay-3">
        <script>
            (function() {
                try {
                    var savedX = sessionStorage.getItem('stageTrackerScrollX') || 0;
                    var savedY = sessionStorage.getItem('stageTrackerScrollY');
                    var reloadTime = sessionStorage.getItem('stageTrackerReloadTime');
                    if (savedY !== null && reloadTime && (Date.now() - parseInt(reloadTime, 10) < 300000)) {
                        if ('scrollRestoration' in history) { history.scrollRestoration = 'manual'; }
                        window.scrollTo(parseInt(savedX, 10), parseInt(savedY, 10));
                    }
                } catch (e) {}
            })();
        </script>
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="radar-dot"></span>
                <h5 class="card-title mb-0 fw-bold text-dark">
                    Live Stage Status &amp; Schedule Tracker
                </h5>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <?php if (!empty($available_stage_dates) && count($available_stage_dates) > 1): ?>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Festival Dates">
                        <?php foreach ($available_stage_dates as $d): ?>
                            <a href="<?php echo base_url('welcome?stage_date=' . $d . '#liveStageTracker'); ?>" 
                               class="btn <?php echo ($target_stage_date === $d) ? 'btn-primary' : 'btn-outline-secondary'; ?> rounded-pill px-3 py-1 me-1 stage-date-filter-btn"
                               data-stage-date="<?php echo $d; ?>"
                               style="font-size: 0.78rem;">
                                <i class="bi bi-calendar-event me-1"></i><?php echo date('M d, Y', strtotime($d)); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php elseif (!empty($target_stage_date)): ?>
                    <span class="badge bg-light text-dark border rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                        <i class="bi bi-calendar-event me-1 text-primary"></i><?php echo date('M d, Y', strtotime($target_stage_date)); ?>
                    </span>
                <?php endif; ?>

                <span class="badge bg-danger rounded-pill px-3 py-1">
                    <?php echo $counts['active_stages']; ?> STAGES ACTIVE
                </span>
                <a href="<?php echo base_url('report/prereport/callsheet_first'); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3" target="_blank">
                    <i class="bi bi-printer me-1"></i> Call Sheets
                </a>
            </div>
        </div>

        <div class="card-body">
            <?php if (!empty($live_stages) && count($live_stages) > 0): ?>
                <div class="row g-3">
                    <?php foreach ($live_stages as $st): ?>
                        <?php
                        $stClass = ($st['status'] === 'LIVE') ? 'is-live' : (($st['status'] === 'DELAYED') ? 'is-delayed' : (($st['status'] === 'COMPLETED') ? 'is-completed' : 'is-scheduled'));
                        $curr = $st['current_event'];
                        $next = $st['next_event'];
                        ?>
                        <div class="col-lg-6 col-12">
                            <div class="live-stage-grid-card <?php echo $stClass; ?>">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1 d-flex align-items-center">
                                            <i class="bi bi-layers-half text-primary me-2"></i>
                                            <?php echo esc($st['stage_name']); ?>
                                            <?php if (!empty($st['stage_desc'])): ?>
                                                <small class="text-muted ms-2 fw-normal">(<?php echo esc($st['stage_desc']); ?>)</small>
                                            <?php endif; ?>
                                        </h6>
                                        <div class="text-muted small" style="font-size: 0.75rem;">
                                            <i class="bi bi-person-badge text-primary me-1"></i>Manager: <strong class="<?php echo ($st['stage_manager'] !== 'Unassigned') ? 'text-dark' : 'text-secondary'; ?>"><?php echo esc($st['stage_manager']); ?></strong>
                                        </div>
                                    </div>
                                    <div>
                                        <?php if ($st['status'] === 'LIVE'): ?>
                                            <span class="badge bg-danger rounded-pill px-2 py-1 d-inline-flex align-items-center">
                                                <span class="radar-dot" style="width: 7px; height: 7px; margin-right: 4px;"></span> ON STAGE NOW
                                                <span class="soundwave-box">
                                                    <span class="soundwave-bar" style="background:#fff;"></span>
                                                    <span class="soundwave-bar" style="background:#fff;"></span>
                                                    <span class="soundwave-bar" style="background:#fff;"></span>
                                                </span>
                                            </span>
                                        <?php elseif ($st['status'] === 'DELAYED'): ?>
                                            <span class="badge bg-warning text-dark border border-warning rounded-pill px-2 py-1 d-inline-flex align-items-center">
                                                <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> DELAYED (<?php echo (int)$st['delay_minutes']; ?>m Late)
                                            </span>
                                        <?php elseif ($st['status'] === 'COMPLETED'): ?>
                                            <span class="badge bg-success rounded-pill px-2 py-1">
                                                <i class="bi bi-check-circle me-1"></i> COMPLETED
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-1">
                                                SCHEDULED
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Progress bar on stage -->
                                <div class="d-flex justify-content-between small text-muted mb-1" style="font-size: 0.78rem;">
                                    <span>Progress: <strong><?php echo $st['completed_events']; ?> / <?php echo $st['total_events']; ?> Events</strong></span>
                                    <span><?php echo $st['progress_pct']; ?>%</span>
                                </div>
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar <?php echo ($st['status'] === 'COMPLETED') ? 'bg-success' : (($st['status'] === 'DELAYED') ? 'bg-warning' : 'bg-primary'); ?>" style="width: <?php echo $st['progress_pct']; ?>%;"></div>
                                </div>

                                <div class="row g-2">
                                    <!-- Current Event -->
                                    <div class="col-sm-6 border-end">
                                        <small class="text-muted d-block fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">CURRENT EVENT</small>
                                        <?php if ($curr): ?>
                                            <div class="fw-bold <?php echo ($curr['effective_status'] === 'STARTED') ? 'text-danger' : (($curr['effective_status'] === 'DELAYED') ? 'text-warning-emphasis' : 'text-dark'); ?> text-truncate mt-1" style="font-size: 0.95rem;">
                                                <i class="bi <?php echo ($curr['effective_status'] === 'STARTED') ? 'bi-play-circle-fill text-danger' : 'bi-clock-history text-warning'; ?> me-1"></i>#<?php echo $curr['item_code']; ?> <?php echo esc($curr['item_name']); ?>
                                            </div>
                                            <div class="text-muted small mt-1" style="font-size: 0.78rem;">
                                                <i class="bi bi-clock me-1"></i>Sched: <?php echo date('h:i A', strtotime($curr['start_time'])); ?>
                                                <span class="badge bg-light text-dark ms-1">
                                                    <?php echo $curr['gender'] == 'G' ? 'Girls' : ($curr['gender'] == 'B' ? 'Boys' : 'General'); ?>
                                                </span>
                                            </div>
                                            <?php if ($curr['effective_status'] === 'DELAYED'): ?>
                                                <div class="badge bg-danger-subtle text-danger border border-danger-subtle mt-1 small" style="font-size: 0.72rem;">
                                                    <i class="bi bi-stopwatch me-1"></i>Delayed by <?php echo (int)$curr['computed_delay']; ?> mins (Pending Start)
                                                </div>
                                            <?php elseif ($curr['effective_status'] === 'STARTED'): ?>
                                                <div class="badge bg-success-subtle text-success border border-success-subtle mt-1 small" style="font-size: 0.72rem;">
                                                    <i class="bi bi-broadcast me-1"></i>Started at <?php echo !empty($curr['actual_start_time']) ? date('h:i A', strtotime($curr['actual_start_time'])) : date('h:i A', strtotime($curr['start_time'])); ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="text-muted fst-italic mt-1 small">No active running item</div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Next Event -->
                                    <div class="col-sm-6 ps-sm-3">
                                        <small class="text-muted d-block fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">UPCOMING NEXT</small>
                                        <?php if ($next): ?>
                                            <div class="fw-bold text-dark text-truncate mt-1" style="font-size: 0.95rem;">
                                                <i class="bi bi-skip-forward-circle me-1 text-info"></i>#<?php echo $next['item_code']; ?> <?php echo esc($next['item_name']); ?>
                                            </div>
                                            <div class="text-muted small mt-1" style="font-size: 0.78rem;">
                                                <i class="bi bi-clock me-1"></i>Exp: <?php echo date('h:i A', strtotime($next['start_time'])); ?>
                                                <span class="badge bg-light text-dark ms-1">
                                                    <?php echo $next['item_time']; ?> min
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-muted fst-italic mt-1 small">No subsequent events</div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Action bar for Stage -->
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 pt-2 border-top">
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <a href="<?php echo base_url('report/prereport/callsheet_first'); ?>" class="btn btn-xs btn-outline-secondary rounded-pill px-3 py-1" style="font-size: 0.78rem;" target="_blank">
                                            <i class="bi bi-printer me-1"></i>Call Sheet
                                        </a>
                                        <a href="<?php echo base_url('stage/manager?stage_id=' . $st['stage_id']); ?>" class="btn btn-xs btn-outline-primary rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                                            <i class="bi bi-speedometer2 me-1"></i>Stage Console
                                        </a>
                                        <?php if ($is_stage_alloted): ?>
                                            <a href="<?php echo base_url('result/resultentry' . ($curr ? '/index/' . $curr['item_code'] : '')); ?>" class="btn btn-xs btn-outline-success rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                                                <i class="bi bi-pencil-square me-1"></i>Enter Marks
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo base_url('stage/allotment'); ?>" class="btn btn-xs btn-outline-warning rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                                                <i class="bi bi-easel me-1"></i>Allot Stage
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($curr): ?>
                                        <div class="form-check form-switch m-0 d-inline-flex align-items-center" title="Allow Stage Manager to enter marks for this item">
                                            <input class="form-check-input toggle-marks-switch" type="checkbox" role="switch" id="mgrSwitch_<?php echo $curr['si_id']; ?>" data-si-id="<?php echo $curr['si_id']; ?>" <?php echo (!empty($curr['allow_stage_manager_marks']) && $curr['allow_stage_manager_marks'] === 'Y') ? 'checked' : ''; ?>>
                                            <label class="form-check-label ms-1 text-muted fw-semibold" for="mgrSwitch_<?php echo $curr['si_id']; ?>" style="font-size: 0.72rem; cursor: pointer;">
                                                Manager Marks: <span class="badge <?php echo (!empty($curr['allow_stage_manager_marks']) && $curr['allow_stage_manager_marks'] === 'Y') ? 'bg-success' : 'bg-secondary'; ?> text-white" id="badgeSwitch_<?php echo $curr['si_id']; ?>"><?php echo (!empty($curr['allow_stage_manager_marks']) && $curr['allow_stage_manager_marks'] === 'Y') ? 'ON' : 'OFF'; ?></span>
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar2-x-fill display-4 text-warning d-block mb-3"></i>
                    <h6>No Stages or Schedules Defined Yet</h6>
                    <div class="small mb-3">Define festival stages and allot competition items to activate live stage tracking.</div>
                    <a href="<?php echo base_url('stage/stage_details'); ?>" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-plus-circle me-1"></i> Define Stages &amp; Venues
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- 8. DETAILED CRM ANALYTICS & CHARTS -->
    <div class="row g-3 mb-4 anim-fade-up delay-4">
        
        <!-- Left: Category-wise Distribution Bar Chart -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Category-wise Student Distribution
                    </h6>
                    <span class="badge bg-light text-muted border">LP, UP, HS, HSS</span>
                </div>
                <div class="card-body">
                    <div id="festStatsChart"></div>
                </div>
            </div>
        </div>

        <!-- Right: Leaderboard / Top 5 Schools -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-trophy-fill text-warning me-2"></i>Top 5 Leading Schools (Points Leaderboard)
                    </h6>
                    <a href="<?php echo base_url('report/afterresultreport/school_wise_result_interface'); ?>" class="small text-decoration-none" target="_blank">
                        View All <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($leaderboard) && count($leaderboard) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($leaderboard as $idx => $lead): ?>
                                <?php
                                $rankBadge = 'rank-badge-3';
                                if ($idx === 0) $rankBadge = 'rank-badge-1';
                                elseif ($idx === 1) $rankBadge = 'rank-badge-2';
                                ?>
                                <div class="list-group-item d-flex align-items-center justify-content-between py-3 px-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold <?php echo $rankBadge; ?>" style="width: 34px; height: 34px; font-size: 0.9rem;">
                                            <?php echo $idx + 1; ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 210px;">
                                                <?php echo esc($lead['school_name']); ?>
                                            </div>
                                            <small class="text-muted">Code: <?php echo esc($lead['school_code']); ?></small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-primary rounded-pill fs-6 px-3">
                                            <?php echo (int)$lead['total_points']; ?> pts
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-trophy display-5 text-muted opacity-50 d-block mb-2"></i>
                            <p class="small mb-0">Points and rankings will appear here as results are published.</p>
                            <small class="text-muted">(Awaiting competition results)</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- 9. SECTION BREAKDOWN STATS CARDS (LP, UP, HS, HSS) -->
    <?php if (isset($statistics)): ?>
    <div class="row g-3 mb-4 anim-fade-up delay-4">
        
        <!-- LP -->
        <div class="col-xl-3 col-sm-6">
            <div class="crm-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-primary mb-0"><i class="bi bi-mortarboard-fill me-1"></i>LP Class (1-4)</h6>
                    <span class="badge bg-primary-subtle text-primary rounded-pill"><?php echo $statistics['lp_schools']; ?> Schools</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Total Participants:</span>
                    <strong><?php echo $statistics['lp_total']; ?></strong>
                </div>
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Boys / Girls:</span>
                    <span><?php echo $statistics['lp_boys']; ?> B / <?php echo $statistics['lp_girls']; ?> G</span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Finalized:</span>
                    <span class="badge bg-success-subtle text-success"><?php echo $statistics['lp_finalized']; ?>/<?php echo $statistics['lp_schools']; ?></span>
                </div>
            </div>
        </div>

        <!-- UP -->
        <div class="col-xl-3 col-sm-6">
            <div class="crm-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-success mb-0"><i class="bi bi-book-fill me-1"></i>UP Class (5-7)</h6>
                    <span class="badge bg-success-subtle text-success rounded-pill"><?php echo $statistics['up_schools']; ?> Schools</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Total Participants:</span>
                    <strong><?php echo $statistics['up_total']; ?></strong>
                </div>
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Boys / Girls:</span>
                    <span><?php echo $statistics['up_boys']; ?> B / <?php echo $statistics['up_girls']; ?> G</span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Finalized:</span>
                    <span class="badge bg-success-subtle text-success"><?php echo $statistics['up_finalized']; ?>/<?php echo $statistics['up_schools']; ?></span>
                </div>
            </div>
        </div>

        <!-- HS -->
        <div class="col-xl-3 col-sm-6">
            <div class="crm-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-info mb-0"><i class="bi bi-journal-text me-1"></i>HS Class (8-10)</h6>
                    <span class="badge bg-info-subtle text-info rounded-pill"><?php echo $statistics['hs_schools']; ?> Schools</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Total Participants:</span>
                    <strong><?php echo $statistics['hs_total']; ?></strong>
                </div>
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Boys / Girls:</span>
                    <span><?php echo $statistics['hs_boys']; ?> B / <?php echo $statistics['hs_girls']; ?> G</span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Finalized:</span>
                    <span class="badge bg-success-subtle text-success"><?php echo $statistics['hs_finalized']; ?>/<?php echo $statistics['hs_schools']; ?></span>
                </div>
            </div>
        </div>

        <!-- HSS -->
        <div class="col-xl-3 col-sm-6">
            <div class="crm-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-warning mb-0"><i class="bi bi-award-fill me-1"></i>HSS Class (11-12)</h6>
                    <span class="badge bg-warning-subtle text-warning rounded-pill"><?php echo $statistics['hss_schools']; ?> Schools</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Total Participants:</span>
                    <strong><?php echo $statistics['hss_total']; ?></strong>
                </div>
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Boys / Girls:</span>
                    <span><?php echo $statistics['hss_boys']; ?> B / <?php echo $statistics['hss_girls']; ?> G</span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Finalized:</span>
                    <span class="badge bg-success-subtle text-success"><?php echo $statistics['hss_finalized']; ?>/<?php echo $statistics['hss_schools']; ?></span>
                </div>
            </div>
        </div>

    </div>
    <?php endif; ?>

</div>

<!-- Client-Side Search Data and Scripts -->
<script>
const SPOTLIGHT_DATA = <?php echo json_encode($quick_search ?? []); ?>;

document.addEventListener("DOMContentLoaded", function() {

    // 1. Live Digital Clock
    function updateClock() {
        const now = new Date();
        const clockEl = document.getElementById('liveClockDisplay');
        if (clockEl) {
            let h = now.getHours();
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12;
            h = h ? h : 12;
            clockEl.textContent = `${String(h).padStart(2, '0')}:${m}:${s} ${ampm}`;
        }
    }
    updateClock();
    setInterval(updateClock, 1000);

    // 2. Count-Up Numerical Animation Engine
    const counters = document.querySelectorAll('.count-up');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target') || 0, 10);
        const duration = 1200; // ms
        const startTime = performance.now();

        function updateCount(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            // EaseOutExpo curve
            const easeProgress = (progress === 1) ? 1 : 1 - Math.pow(2, -10 * progress);
            const currentVal = Math.floor(easeProgress * target);
            counter.textContent = currentVal.toLocaleString();

            if (progress < 1) {
                requestAnimationFrame(updateCount);
            } else {
                counter.textContent = target.toLocaleString();
            }
        }
        requestAnimationFrame(updateCount);
    });

    // 3. Spotlight Instant Filter
    const searchInput = document.getElementById('spotlightSearch');
    const dropdown = document.getElementById('spotlightDropdown');

    if (searchInput && dropdown) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            if (query.length === 0) {
                dropdown.style.display = 'none';
                dropdown.innerHTML = '';
                return;
            }

            const matches = SPOTLIGHT_DATA.filter(item => {
                return (item.title && item.title.toLowerCase().includes(query)) ||
                       (item.sub && item.sub.toLowerCase().includes(query)) ||
                       (item.type && item.type.toLowerCase().includes(query));
            }).slice(0, 10); // Limit to top 10 matches

            if (matches.length === 0) {
                dropdown.innerHTML = '<div class="p-3 text-muted text-center small"><i class="bi bi-search me-1"></i>No matching records found</div>';
                dropdown.style.display = 'block';
                return;
            }

            let html = '';
            matches.forEach(m => {
                let badgeClass = 'bg-primary-subtle text-primary border-primary-subtle';
                if (m.type === 'Stage') badgeClass = 'bg-warning-subtle text-warning border-warning-subtle';
                if (m.type === 'Item')  badgeClass = 'bg-success-subtle text-success border-success-subtle';

                html += `
                <a href="${m.url}" class="spotlight-item">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi ${m.icon || 'bi-arrow-right-circle'} fs-5 text-primary"></i>
                        <div>
                            <div class="fw-bold">${escapeHtml(m.title)}</div>
                            <small class="text-muted">${escapeHtml(m.sub || '')}</small>
                        </div>
                    </div>
                    <span class="badge ${badgeClass} border rounded-pill">${escapeHtml(m.type)}</span>
                </a>`;
            });

            dropdown.innerHTML = html;
            dropdown.style.display = 'block';
        });

        // Close on ESC
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                dropdown.style.display = 'none';
            }
        });

        // Close on outside click
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // 4. ApexCharts Category-wise Graph
    const statsEl = document.getElementById('festStatsChart');
    if (statsEl && typeof ApexCharts !== 'undefined') {
        const festStatsChart = new ApexCharts(statsEl, {
            series: [{
                name: 'Boys',
                data: [
                    <?php echo @$statistics['lp_boys'] ?? 0; ?>, 
                    <?php echo @$statistics['up_boys'] ?? 0; ?>, 
                    <?php echo @$statistics['hs_boys'] ?? 0; ?>, 
                    <?php echo @$statistics['hss_boys'] ?? 0; ?>
                ]
            }, {
                name: 'Girls',
                data: [
                    <?php echo @$statistics['lp_girls'] ?? 0; ?>, 
                    <?php echo @$statistics['up_girls'] ?? 0; ?>, 
                    <?php echo @$statistics['hs_girls'] ?? 0; ?>, 
                    <?php echo @$statistics['hss_girls'] ?? 0; ?>
                ]
            }],
            chart: {
                type: 'bar',
                height: 320,
                toolbar: { show: false },
                fontFamily: 'Outfit, sans-serif'
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    borderRadius: 8
                }
            },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: {
                categories: ['LP (1-4)', 'UP (5-7)', 'HS (8-10)', 'HSS (11-12)'],
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                min: 0,
                forceNiceScale: true
            },
            colors: ['#4f46e5', '#ec4899'],
            legend: { position: 'top', horizontalAlign: 'right' },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
            fill: { opacity: 1 }
        });
        festStatsChart.render();
    }

    // 5. Full (X, Y) Scroll Position Persistence Engine
    (function() {
        const STORAGE_KEY = 'stageTrackerScrollY';
        const STORAGE_KEY_X = 'stageTrackerScrollX';
        const TIMESTAMP_KEY = 'stageTrackerReloadTime';

        // Function to record exact current (X, Y) scroll coordinates
        function recordCurrentScroll() {
            try {
                const currentX = window.pageXOffset || document.documentElement.scrollLeft || window.scrollX || 0;
                const currentY = window.pageYOffset || document.documentElement.scrollTop || window.scrollY || 0;
                sessionStorage.setItem(STORAGE_KEY, String(Math.round(currentY)));
                sessionStorage.setItem(STORAGE_KEY_X, String(Math.round(currentX)));
                sessionStorage.setItem(TIMESTAMP_KEY, String(Date.now()));
            } catch (err) {}
        }

        // Continually track scroll position on this page so any manual or auto reload (F5) restores to exact (x, y)
        let scrollTimer = null;
        window.addEventListener('scroll', function() {
            if (!scrollTimer) {
                scrollTimer = setTimeout(function() {
                    recordCurrentScroll();
                    scrollTimer = null;
                }, 100);
            }
        }, { passive: true });

        // Save on unload / pagehide
        window.addEventListener('beforeunload', recordCurrentScroll);
        window.addEventListener('pagehide', recordCurrentScroll);

        // Capture scroll position on date button click
        const dateButtons = document.querySelectorAll('.stage-date-filter-btn');
        dateButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                try {
                    recordCurrentScroll();

                    // Show spinner in clicked button
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.className = 'spinner-border spinner-border-sm me-1';
                        icon.setAttribute('role', 'status');
                    }
                    this.classList.add('is-loading');

                    // Subtle dim effect on stage card
                    const tracker = document.getElementById('liveStageTracker');
                    if (tracker) {
                        tracker.style.transition = 'opacity 0.2s ease';
                        tracker.style.opacity = '0.65';
                    }
                } catch (err) {
                    console.warn('Scroll storage error:', err);
                }
            });
        });

        // Restore exact (X, Y) scroll position after reload
        try {
            const savedY = sessionStorage.getItem(STORAGE_KEY);
            const savedX = sessionStorage.getItem(STORAGE_KEY_X) || '0';
            const reloadTime = sessionStorage.getItem(TIMESTAMP_KEY);

            if (savedY !== null && reloadTime && (Date.now() - parseInt(reloadTime, 10) < 300000)) {
                const targetY = parseInt(savedY, 10);
                const targetX = parseInt(savedX, 10);
                
                if ('scrollRestoration' in history) {
                    history.scrollRestoration = 'manual';
                }

                const applyScrollAndRestore = function() {
                    window.scrollTo({ top: targetY, behavior: 'instant' });
                    window.scrollTo({ left: targetX, top: targetY, behavior: 'instant' });
                    window.scrollTo(targetX, targetY);

                    const tracker = document.getElementById('liveStageTracker');
                    if (tracker) {
                        tracker.style.opacity = '1';
                    }
                    if (window.location.hash === '#liveStageTracker') {
                        try {
                            history.replaceState(null, document.title, window.location.pathname + window.location.search);
                        } catch (e) {}
                    }
                };

                window.scrollTo({ top: targetY, behavior: 'instant' });
                window.scrollTo({ left: targetX, top: targetY, behavior: 'instant' });
                window.scrollTo(targetX, targetY);
                requestAnimationFrame(applyScrollAndRestore);
                setTimeout(applyScrollAndRestore, 40);
                setTimeout(applyScrollAndRestore, 120);
                setTimeout(applyScrollAndRestore, 250);
                setTimeout(applyScrollAndRestore, 500);
                window.addEventListener('load', applyScrollAndRestore);
            } else if (window.location.hash === '#liveStageTracker') {
                const tracker = document.getElementById('liveStageTracker');
                if (tracker) {
                    setTimeout(() => {
                        tracker.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        try {
                            history.replaceState(null, document.title, window.location.pathname + window.location.search);
                        } catch (e) {}
                    }, 60);
                }
            }
        } catch (err) {
            console.warn('Scroll restore error:', err);
        }
    })();

    // Handle Sub-District Admin Stage Manager Mark Entry Toggle
    document.querySelectorAll('.toggle-marks-switch').forEach(sw => {
        sw.addEventListener('change', function() {
            const siId = this.getAttribute('data-si-id');
            const isChecked = this.checked;
            const allow = isChecked ? 'Y' : 'N';
            const badge = document.getElementById('badgeSwitch_' + siId);
            
            const formData = new FormData();
            formData.append('si_id', siId);
            formData.append('allow', allow);
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfToken) formData.append('csrf_test_name', csrfToken);

            fetch('<?php echo base_url("welcome/toggle_manager_marks"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (badge) {
                        badge.className = (allow === 'Y') ? 'badge bg-success text-white' : 'badge bg-secondary text-white';
                        badge.textContent = (allow === 'Y') ? 'ON' : 'OFF';
                    }
                } else {
                    alert(data.message || 'Failed to update setting.');
                    this.checked = !isChecked;
                }
            })
            .catch(err => {
                alert('Network error while saving permission.');
                this.checked = !isChecked;
            });
        });
    });
});
</script>
