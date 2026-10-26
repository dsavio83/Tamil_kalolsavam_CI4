<?php
/**
 * District Analytics CRM Dashboard
 * Scoped exclusively to district (d_*) tables. Shows per-sub-district statistics,
 * live stage updates with stage manager name & phone, and the leading sub-district.
 */
$is_stage_alloted = !empty($is_all_stage_alloted);
$counts = $counts ?? [
    'subdistricts' => 0, 'schools' => 0, 'finalized_schools' => 0, 'unfinalized_schools' => 0, 'school_pct' => 0,
    'participants' => 0, 'boys' => 0, 'girls' => 0,
    'items' => 0, 'completed_items' => 0, 'pending_items' => 0, 'item_pct' => 0,
    'published_results' => 0, 'published_pct' => 0, 'stages_count' => 0, 'active_stages' => 0
];
$sub_districts = $sub_districts ?? [];
$live_stages = $live_stages ?? [];
$pipeline = $pipeline ?? [];
$leaderboard = $leaderboard ?? [];
$quick_search = $quick_search ?? [];
$has_live = ($counts['active_stages'] ?? 0) > 0;
$has_scheduled = ($counts['stages_count'] ?? 0) > 0;
$all_done = ($counts['items'] ?? 0) > 0 && ($counts['completed_items'] ?? 0) >= ($counts['items'] ?? 0);

function dist_status_badge($all_done, $has_live, $has_scheduled) {
    if ($all_done) {
        return ['text' => 'COMPLETED', 'class' => 'bg-success', 'icon' => 'bi-check-circle'];
    }
    if ($has_live) {
        return ['text' => 'ON STAGE', 'class' => 'bg-danger', 'icon' => 'bi-broadcast'];
    }
    if ($has_scheduled) {
        return ['text' => 'SCHEDULED', 'class' => 'bg-secondary-subtle text-secondary border', 'icon' => 'bi-calendar-event'];
    }
    return ['text' => 'REGISTERED', 'class' => 'bg-info-subtle text-info-emphasis border', 'icon' => 'bi-person-check'];
}
?>
<!-- District Analytics CRM Dashboard Styles -->
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

@keyframes crmFadeUp {
  from { opacity: 0; transform: translateY(22px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes pulseRadar {
  0%   { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
  70%  { transform: scale(1.1);   box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
  100% { transform: scale(0.95);  box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}
@keyframes liveSoundwave {
  0%, 100% { height: 4px; }
  50%      { height: 18px; }
}
.anim-fade-up { animation: crmFadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) backwards; }
.delay-1 { animation-delay: 0.08s; }
.delay-2 { animation-delay: 0.16s; }
.delay-3 { animation-delay: 0.24s; }
.delay-4 { animation-delay: 0.32s; }

.crm-hero {
  background: var(--crm-gradient-hero);
  border-radius: var(--crm-radius);
  color: #ffffff;
  padding: 34px 40px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.5);
  margin-bottom: 26px;
  border: 1px solid rgba(255, 255, 255, 0.08);
}
.crm-hero::before {
  content: '';
  position: absolute;
  top: -60%; right: -15%;
  width: 500px; height: 500px;
  background: radial-gradient(circle, rgba(124, 58, 237, 0.35) 0%, rgba(99, 102, 241, 0.1) 50%, transparent 70%);
  border-radius: 50%; pointer-events: none;
}
.crm-hero::after {
  content: '';
  position: absolute;
  bottom: -40%; left: 20%;
  width: 350px; height: 350px;
  background: radial-gradient(circle, rgba(236, 72, 153, 0.2) 0%, transparent 70%);
  border-radius: 50%; pointer-events: none;
}
.hero-pill {
  display: inline-flex; align-items: center; gap: 8px;
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  padding: 6px 16px; border-radius: 30px;
  font-size: 0.85rem; font-weight: 600; letter-spacing: 0.3px;
}
.hero-live-clock {
  font-family: 'Outfit', monospace;
  font-size: 1.15rem; font-weight: 700;
  background: rgba(0, 0, 0, 0.35);
  padding: 6px 18px; border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.15);
  display: inline-flex; align-items: center; gap: 8px; color: #38bdf8;
}

.crm-card {
  background: var(--crm-card-bg);
  border-radius: var(--crm-radius);
  border: 1px solid var(--crm-border);
  box-shadow: var(--crm-shadow);
  position: relative;
  overflow: hidden;
}

.kpi-card {
  color: #ffffff;
  padding: 24px;
  border-radius: var(--crm-radius);
  position: relative;
  overflow: hidden;
  box-shadow: 0 12px 25px -5px rgba(0, 0, 0, 0.15);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  min-height: 155px;
  display: flex; flex-direction: column; justify-content: space-between;
}
.kpi-card:hover { transform: translateY(-6px) scale(1.01); }
.kpi-indigo  { background: var(--crm-gradient-indigo); }
.kpi-emerald { background: var(--crm-gradient-emerald); }
.kpi-amber   { background: var(--crm-gradient-amber); }
.kpi-rose    { background: var(--crm-gradient-rose); }
.kpi-cyan    { background: var(--crm-gradient-cyan); }
.kpi-violet  { background: var(--crm-gradient-violet); }
.kpi-title {
  font-size: 0.82rem; text-transform: uppercase; letter-spacing: 1px;
  font-weight: 700; opacity: 0.88;
  display: flex; align-items: center; justify-content: space-between;
}
.kpi-number { font-size: 2.35rem; font-weight: 800; letter-spacing: -0.5px; margin: 6px 0 2px 0; line-height: 1.1; }
.kpi-subtitle { font-size: 0.82rem; opacity: 0.9; display: flex; align-items: center; gap: 6px; }
.kpi-bg-icon {
  position: absolute; right: -10px; bottom: -15px;
  font-size: 5.5rem; opacity: 0.14; transform: rotate(-10deg); pointer-events: none;
}

.pipeline-bar {
  background: #ffffff;
  border-radius: var(--crm-radius);
  padding: 24px 28px;
  border: 1px solid var(--crm-border);
  box-shadow: var(--crm-shadow);
  margin-bottom: 26px;
}
.pipeline-step { position: relative; text-align: center; padding: 10px 8px; border-radius: 12px; transition: all 0.2s ease; }
.pipeline-step:hover { background: #f1f5f9; }
.pipeline-icon-box {
  width: 46px; height: 46px; border-radius: 50%;
  display: inline-flex; align-items: center; justify-content: center;
  font-size: 1.25rem; margin-bottom: 8px; transition: transform 0.2s ease;
}
.pipeline-step:hover .pipeline-icon-box { transform: scale(1.1); }
.pipe-complete { background: #ecfdf5; color: #059669; border: 2px solid #10b981; }
.pipe-active   { background: #eff6ff; color: #2563eb; border: 2px solid #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2); }
.pipe-pending  { background: #f8fafc; color: #94a3b8; border: 2px dashed #cbd5e1; }
.pipe-locked   { background: #fff1f2; color: #e11d48; border: 2px dashed #fda4af; opacity: 0.8; }

.spotlight-wrapper { position: relative; margin-bottom: 26px; }
.spotlight-input-box {
  background: #ffffff; border-radius: 16px; border: 2px solid #cbd5e1;
  padding: 14px 22px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
  display: flex; align-items: center; gap: 14px; transition: all 0.25s ease;
}
.spotlight-input-box:focus-within { border-color: #6366f1; box-shadow: 0 12px 30px rgba(99, 102, 241, 0.18); transform: translateY(-2px); }
.spotlight-input { border: none; outline: none; font-size: 1.05rem; font-weight: 500; width: 100%; color: #0f172a; }
.spotlight-dropdown {
  position: absolute; top: 100%; left: 0; right: 0; background: #ffffff;
  border-radius: 16px; margin-top: 8px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  border: 1px solid #e2e8f0; z-index: 1050; max-height: 380px; overflow-y: auto; display: none;
}
.spotlight-item {
  padding: 12px 20px; display: flex; align-items: center; justify-content: space-between;
  border-bottom: 1px solid #f1f5f9; text-decoration: none; color: #1e293b; transition: background 0.15s ease;
}
.spotlight-item:hover { background: #f8fafc; color: #4f46e5; }

.action-pod {
  background: #ffffff; border-radius: var(--crm-radius); border: 1px solid var(--crm-border);
  padding: 22px; height: 100%; box-shadow: var(--crm-shadow); transition: all 0.25s ease;
}
.action-pod:hover { box-shadow: var(--crm-shadow-hover); border-color: #cbd5e1; }
.pod-header { display: flex; align-items: center; gap: 10px; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 2px solid #f1f5f9; }
.pod-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #ffffff; }
.action-item-link {
  display: flex; align-items: center; justify-content: space-between;
  padding: 10px 14px; border-radius: 12px; color: #334155; text-decoration: none;
  font-weight: 600; font-size: 0.92rem; margin-bottom: 8px;
  background: #f8fafc; border: 1px solid #f1f5f9; transition: all 0.2s ease;
}
.action-item-link:hover { background: #eef2ff; border-color: #c7d2fe; color: #4338ca; transform: translateX(4px); }
.action-item-link i.chevron { font-size: 0.85rem; color: #94a3b8; transition: transform 0.2s ease; }
.action-item-link:hover i.chevron { color: #4338ca; transform: translateX(3px); }
.priority-action-btn { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #ffffff !important; border: none; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35); }
.priority-action-btn:hover { background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%); color: #ffffff !important; transform: translateX(4px) scale(1.02); box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45); }
.priority-action-btn i.chevron { color: rgba(255, 255, 255, 0.8) !important; }

.live-stage-grid-card {
  background: #ffffff; border-radius: 16px; border: 1px solid var(--crm-border);
  box-shadow: var(--crm-shadow); padding: 20px; margin-bottom: 20px; transition: all 0.28s ease; position: relative; overflow: hidden;
}
.live-stage-grid-card:hover { transform: translateY(-4px); box-shadow: var(--crm-shadow-hover); }
.live-stage-grid-card.is-live { border-left: 6px solid #ef4444; }
.live-stage-grid-card.is-delayed { border-left: 6px solid #f59e0b; background: linear-gradient(to right, #fffbeb 0%, #ffffff 20%); }
.live-stage-grid-card.is-completed { border-left: 6px solid #10b981; }
.live-stage-grid-card.is-scheduled { border-left: 6px solid #3b82f6; }

.radar-dot { display: inline-block; width: 10px; height: 10px; background-color: #ef4444; border-radius: 50%; animation: pulseRadar 1.6s infinite; margin-right: 6px; }
.soundwave-box { display: inline-flex; align-items: flex-end; gap: 2px; height: 18px; margin-left: 8px; }
.soundwave-bar { width: 3px; background-color: #ef4444; border-radius: 2px; animation: liveSoundwave 1s ease-in-out infinite alternate; }
.soundwave-bar:nth-child(2) { animation-delay: 0.2s; }
.soundwave-bar:nth-child(3) { animation-delay: 0.4s; }
.soundwave-bar:nth-child(4) { animation-delay: 0.1s; }

.rank-badge-1 { background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%); color: #ffffff; }
.rank-badge-2 { background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%); color: #ffffff; }
.rank-badge-3 { background: linear-gradient(135deg, #b45309 0%, #78350f 100%); color: #ffffff; }

.checkmark-anim { display: inline-flex; align-items: center; gap: 6px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.4px; }

@media (max-width: 768px) {
  .crm-hero { padding: 24px 20px; }
  .kpi-number { font-size: 1.9rem; }
  .pipeline-bar { padding: 16px; overflow-x: auto; }
  .pipeline-step { min-width: 110px; }
}
</style>

<!-- Load ApexCharts -->
<script src="<?php echo base_url(); ?>assets/apexcharts.js"></script>

<div class="crm-dashboard py-4 px-3 px-md-4">

    <!-- 1. HERO BANNER -->
    <div class="crm-hero anim-fade-up">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <span class="hero-pill">
                        <i class="bi bi-shield-check text-warning"></i>
                        Logged in: <strong><?php echo esc($active_user_name ?? 'Administrator'); ?></strong>
                        <span class="badge bg-primary bg-opacity-75 ms-1"><?php echo esc($rev_dist_name ?? 'District'); ?> District</span>
                    </span>
                </div>
                <h1 class="display-6 fw-bold mb-2 text-white">
                    <?php echo esc($welcome_label ?? 'District Dashboard'); ?>
                </h1>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="hero-pill" style="background:rgba(16,185,129,0.25);border-color:rgba(52,211,153,0.35);">
                        <i class="bi bi-grid-1x2-fill text-success"></i>
                        <strong><?php echo (int)($counts['subdistricts'] ?? 0); ?></strong> Sub-Districts
                    </span>
                    <span class="hero-pill">
                        <i class="bi bi-trophy-fill text-warning"></i>
                        <?php if (!empty($fest_name)): ?><?php echo esc($fest_name); ?><?php else: ?>Tamil Kalaivizha<?php endif; ?>
                    </span>
                </div>
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
                <div class="hero-live-clock mb-3">
                    <i class="bi bi-clock-history text-info"></i>
                    <span id="liveClockDisplay">--:--:--</span>
                </div>
                <div class="text-white-50 small" id="liveDateDisplay">
                    <?php echo date('l, d F Y'); ?>
                </div>
                <div class="d-flex gap-2 mt-3 justify-content-lg-end">
                    <a href="<?php echo base_url('district/import'); ?>" class="btn btn-sm btn-outline-light rounded-pill px-3 shadow-sm">
                        <i class="bi bi-cloud-download-fill text-success me-1"></i> Import Winners
                    </a>
                    <a href="<?php echo base_url('district/stage'); ?>" class="btn btn-sm btn-outline-light rounded-pill px-3 shadow-sm">
                        <i class="bi bi-easel2-fill text-warning me-1"></i> Define Stages
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. TOP KPI CARDS -->
    <div class="row g-3 mb-4 anim-fade-up delay-1">
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-indigo">
                <div class="kpi-title"><span>Sub-Districts</span><i class="bi bi-grid-1x2-fill"></i></div>
                <div class="kpi-number count-up" data-target="<?php echo (int)$counts['subdistricts']; ?>">0</div>
                <div class="kpi-subtitle"><i class="bi bi-trophy-fill"></i><span>Festival Coverage Units</span></div>
                <i class="bi bi-grid-1x2-fill kpi-bg-icon"></i>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-emerald">
                <div class="kpi-title"><span>Enrolled Schools</span><i class="bi bi-buildings"></i></div>
                <div class="kpi-number count-up" data-target="<?php echo (int)$counts['schools']; ?>">0</div>
                <div class="kpi-subtitle"><i class="bi bi-check2-circle"></i><span>Finalized: <strong><?php echo (int)$counts['finalized_schools']; ?></strong> (<?php echo (int)$counts['school_pct']; ?>%)</span></div>
                <i class="bi bi-buildings kpi-bg-icon"></i>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-cyan">
                <div class="kpi-title"><span>Total Participants</span><i class="bi bi-people-fill"></i></div>
                <div class="kpi-number count-up" data-target="<?php echo (int)$counts['participants']; ?>">0</div>
                <div class="kpi-subtitle"><i class="bi bi-gender-ambiguous"></i><span>Boys: <strong><?php echo (int)$counts['boys']; ?></strong> | Girls: <strong><?php echo (int)$counts['girls']; ?></strong></span></div>
                <i class="bi bi-people-fill kpi-bg-icon"></i>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card kpi-amber">
                <div class="kpi-title"><span>Competition Items</span><i class="bi bi-trophy"></i></div>
                <div class="kpi-number count-up" data-target="<?php echo (int)$counts['items']; ?>">0</div>
                <div class="kpi-subtitle"><i class="bi bi-flag-fill"></i><span>Finished: <strong><?php echo (int)$counts['completed_items']; ?></strong> (<?php echo (int)$counts['item_pct']; ?>%)</span></div>
                <i class="bi bi-trophy kpi-bg-icon"></i>
            </div>
        </div>
    </div>

    <!-- 3. PIPELINE -->
    <div class="pipeline-bar anim-fade-up delay-2">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold text-dark mb-0">
                <i class="bi bi-kanban-fill text-primary me-2"></i>District Festival Operations Lifecycle
            </h6>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Live State Tracking</span>
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
                            <div class="pipeline-icon-box <?php echo $stepClass; ?>"><i class="bi <?php echo $pStep['icon']; ?>"></i></div>
                            <div class="fw-bold text-dark small text-truncate"><?php echo esc($pStep['name']); ?></div>
                            <div class="text-muted" style="font-size: 0.72rem;"><?php echo esc($pStep['sub_title']); ?></div>
                            <div class="badge <?php echo ($pStep['status'] === 'COMPLETED') ? 'bg-success' : (($pStep['status'] === 'ACTIVE') ? 'bg-primary' : (($pStep['status'] === 'LOCKED') ? 'bg-danger' : 'bg-secondary')); ?> rounded-pill mt-1" style="font-size: 0.68rem;">
                                <?php echo $pStep['pct']; ?>% &bull; <?php echo $pStep['status']; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 4. SPOTLIGHT QUICK FIND -->
    <div class="spotlight-wrapper anim-fade-up delay-2">
        <div class="spotlight-input-box">
            <i class="bi bi-search text-primary fs-5"></i>
            <input type="text" id="spotlightSearch" class="spotlight-input" placeholder="Spotlight Quick Find: Action, Stage, or Item #/Name..." autocomplete="off">
            <span class="badge bg-light text-muted border px-2 py-1 small">Press ESC to clear</span>
        </div>
        <div id="spotlightDropdown" class="spotlight-dropdown"></div>
    </div>

    <!-- 5. SUB-DISTRICT COMMAND TABLE (CORE) -->
    <div class="crm-card mb-4 anim-fade-up delay-3">
        <div class="p-4 pb-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-grid-1x2-fill text-primary me-2"></i>Sub-District Command Table
                </h5>
                <div class="text-muted small">Per-sub-district registration, performance and live stage status</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <?php if ($has_live): ?>
                    <span class="badge bg-danger rounded-pill px-3 py-1"><span class="radar-dot" style="width:7px;height:7px;"></span><?php echo (int)$counts['active_stages']; ?> STAGES LIVE</span>
                <?php endif; ?>
                <span class="badge bg-primary-subtle text-primary border rounded-pill px-3 py-1"><?php echo (int)$counts['stages_count']; ?> Stages Defined</span>
            </div>
        </div>
        <div class="p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-muted text-uppercase small" style="font-size:0.72rem;letter-spacing:0.6px;">
                            <th class="border-0 py-2">Rank</th>
                            <th class="border-0 py-2">Sub-District</th>
                            <th class="border-0 py-2 text-center">Schools</th>
                            <th class="border-0 py-2 text-center">Participants (B / G)</th>
                            <th class="border-0 py-2 text-center">Items Done</th>
                            <th class="border-0 py-2 text-center">Points</th>
                            <th class="border-0 py-2 text-center">Live Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($sub_districts)): ?>
                            <?php foreach ($sub_districts as $sd): ?>
                                <?php
                                $rk = (int)$sd['rank'];
                                $stBadge = dist_status_badge($all_done, $has_live, $has_scheduled);
                                $isLead = !empty($sd['is_leading']);
                                ?>
                                <tr class="<?php echo $isLead ? 'table-warning' : ''; ?>">
                                    <td>
                                        <?php if ($rk <= 3): ?>
                                            <span class="badge rank-badge-<?php echo $rk; ?> rounded-circle" style="width:32px;height:32px;line-height:28px;font-size:0.9rem;"><?php echo $rk; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary rounded-circle" style="width:32px;height:32px;line-height:26px;border:1px dashed #cbd5e1;"><?php echo $rk; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                            <?php echo esc($sd['sub_district_name']); ?>
                                            <?php if ($isLead): ?>
                                                <span class="badge bg-warning text-dark rounded-pill d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-trophy-fill"></i> LEADING
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-muted small text-truncate" style="max-width:230px;"><i class="bi bi-geo-alt text-danger me-1"></i><?php echo !empty($sd['venue']) ? esc($sd['venue']) : 'Venue not set'; ?></div>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold"><?php echo (int)$sd['schools']; ?></div>
                                        <div class="text-muted" style="font-size:0.72rem;"><?php echo (int)$sd['finalized_schools']; ?> finalized</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold"><?php echo (int)$sd['participants']; ?></div>
                                        <div style="font-size:0.75rem;">B <strong class="text-primary"><?php echo (int)$sd['boys']; ?></strong> | G <strong class="text-danger"><?php echo (int)$sd['girls']; ?></strong></div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3"><?php echo (int)$sd['items_completed']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?php echo ((int)$sd['points'] > 0) ? 'bg-warning text-dark' : 'bg-secondary-subtle text-secondary'; ?> rounded-pill px-3"><?php echo (int)$sd['points']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?php echo $stBadge['class']; ?> rounded-pill d-inline-flex align-items-center gap-1">
                                            <i class="bi <?php echo $stBadge['icon']; ?>"></i><?php echo $stBadge['text']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-grid-1x2 text-warning display-4 d-block mb-2"></i>
                                    No sub-districts found for this district.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 6. LIVE STAGES TRACKER + LEADERBOARD -->
    <div class="row g-3 mb-4 anim-fade-up delay-4">
        <div class="col-lg-8">
            <div id="liveStageTracker" class="crm-card h-100">
                <div class="p-4 pb-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <?php if ($has_live): ?><span class="radar-dot"></span><?php endif; ?>
                        <h5 class="fw-bold text-dark mb-0">Live Stage Status Tracker</h5>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <?php if (!empty($available_stage_dates) && count($available_stage_dates) > 1): ?>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Festival Dates">
                                <?php foreach ($available_stage_dates as $d): ?>
                                    <a href="<?php echo base_url('welcome?stage_date=' . $d . '#liveStageTracker'); ?>"
                                       class="btn <?php echo ($target_stage_date === $d) ? 'btn-primary' : 'btn-outline-secondary'; ?> rounded-pill px-3 py-1 me-1"
                                       style="font-size:0.78rem;">
                                        <i class="bi bi-calendar-event me-1"></i><?php echo date('M d, Y', strtotime($d)); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif (!empty($target_stage_date)): ?>
                            <span class="badge bg-light text-dark border rounded-pill px-3 py-1" style="font-size:0.78rem;">
                                <i class="bi bi-calendar-event me-1 text-primary"></i><?php echo date('M d, Y', strtotime($target_stage_date)); ?>
                            </span>
                        <?php endif; ?>
                        <span class="badge bg-danger rounded-pill px-3 py-1"><?php echo (int)$counts['active_stages']; ?> STAGES ACTIVE</span>
                    </div>
                </div>
                <div class="p-4 pt-1">
                    <?php if (!empty($live_stages)): ?>
                        <div class="row g-2">
                            <?php foreach ($live_stages as $st): ?>
                                <?php
                                $stClass = ($st['status'] === 'LIVE') ? 'is-live' : (($st['status'] === 'DELAYED') ? 'is-delayed' : (($st['status'] === 'COMPLETED') ? 'is-completed' : 'is-scheduled'));
                                $curr = $st['current_event'];
                                $next = $st['next_event'];
                                ?>
                                <div class="col-lg-6 col-12">
                                    <div class="live-stage-grid-card <?php echo $stClass; ?>" style="margin-bottom:0;">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1 d-flex align-items-center">
                                                    <i class="bi bi-layers-half text-primary me-2"></i><?php echo esc($st['stage_name']); ?>
                                                    <?php if (!empty($st['stage_desc'])): ?><small class="text-muted ms-2 fw-normal">(<?php echo esc($st['stage_desc']); ?>)</small><?php endif; ?>
                                                </h6>
                                                <div class="text-muted small" style="font-size:0.75rem;">
                                                    <i class="bi bi-person-badge text-primary me-1"></i>Manager:
                                                    <strong class="<?php echo ($st['stage_manager'] !== 'Unassigned') ? 'text-dark' : 'text-secondary'; ?>"><?php echo esc($st['stage_manager']); ?></strong>
                                                    <?php if (!empty($st['stage_manager_mobile'])): ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size:0.7rem;">
                                                            <i class="bi bi-telephone-fill me-1"></i><?php echo esc($st['stage_manager_mobile']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div>
                                                <?php if ($st['status'] === 'LIVE'): ?>
                                                    <span class="badge bg-danger rounded-pill px-2 py-1 d-inline-flex align-items-center">
                                                        <span class="radar-dot" style="width:7px;height:7px;margin-right:4px;"></span> ON STAGE NOW
                                                        <span class="soundwave-box"><span class="soundwave-bar" style="background:#fff;"></span><span class="soundwave-bar" style="background:#fff;"></span><span class="soundwave-bar" style="background:#fff;"></span><span class="soundwave-bar" style="background:#fff;"></span></span>
                                                    </span>
                                                <?php elseif ($st['status'] === 'DELAYED'): ?>
                                                    <span class="badge bg-warning text-dark border border-warning rounded-pill px-2 py-1 d-inline-flex align-items-center">
                                                        <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> DELAYED (<?php echo (int)$st['delay_minutes']; ?>m Late)
                                                    </span>
                                                <?php elseif ($st['status'] === 'COMPLETED'): ?>
                                                    <span class="badge bg-success rounded-pill px-2 py-1"><i class="bi bi-check-circle me-1"></i> COMPLETED</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-1">SCHEDULED</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between small text-muted mb-1" style="font-size:0.78rem;">
                                            <span>Progress: <strong><?php echo $st['completed_events']; ?> / <?php echo $st['total_events']; ?> Events</strong></span>
                                            <span><?php echo $st['progress_pct']; ?>%</span>
                                        </div>
                                        <div class="progress mb-3" style="height:6px;">
                                            <div class="progress-bar <?php echo ($st['status'] === 'COMPLETED') ? 'bg-success' : (($st['status'] === 'DELAYED') ? 'bg-warning' : 'bg-primary'); ?>" style="width: <?php echo $st['progress_pct']; ?>%;"></div>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-sm-6 border-end">
                                                <small class="text-muted d-block fw-bold" style="font-size:0.72rem;letter-spacing:0.5px;">CURRENT EVENT</small>
                                                <?php if ($curr): ?>
                                                    <div class="fw-bold <?php echo ($curr['effective_status'] === 'STARTED') ? 'text-danger' : (($curr['effective_status'] === 'DELAYED') ? 'text-warning-emphasis' : 'text-dark'); ?> text-truncate mt-1" style="font-size:0.95rem;">
                                                        <i class="bi <?php echo ($curr['effective_status'] === 'STARTED') ? 'bi-play-circle-fill text-danger' : 'bi-clock-history text-warning'; ?> me-1"></i>#<?php echo $curr['item_code']; ?> <?php echo esc($curr['item_name']); ?>
                                                    </div>
                                                    <div class="text-muted small mt-1" style="font-size:0.78rem;">
                                                        <i class="bi bi-clock me-1"></i>Sched: <?php echo date('h:i A', strtotime($curr['start_time'])); ?>
                                                        <span class="badge bg-light text-dark ms-1"><?php echo $curr['gender'] == 'G' ? 'Girls' : ($curr['gender'] == 'B' ? 'Boys' : 'General'); ?></span>
                                                    </div>
                                                    <?php if ($curr['effective_status'] === 'DELAYED'): ?>
                                                        <div class="badge bg-danger-subtle text-danger border border-danger-subtle mt-1 small" style="font-size:0.72rem;"><i class="bi bi-stopwatch me-1"></i>Delayed by <?php echo (int)$curr['computed_delay']; ?> mins (Pending Start)</div>
                                                    <?php elseif ($curr['effective_status'] === 'STARTED'): ?>
                                                        <div class="badge bg-success-subtle text-success border border-success-subtle mt-1 small" style="font-size:0.72rem;"><i class="bi bi-broadcast me-1"></i>Started at <?php echo !empty($curr['actual_start_time']) ? date('h:i A', strtotime($curr['actual_start_time'])) : date('h:i A', strtotime($curr['start_time'])); ?></div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <div class="text-muted fst-italic mt-1 small">No active running item</div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-sm-6 ps-sm-3">
                                                <small class="text-muted d-block fw-bold" style="font-size:0.72rem;letter-spacing:0.5px;">UPCOMING NEXT</small>
                                                <?php if ($next): ?>
                                                    <div class="fw-bold text-dark text-truncate mt-1" style="font-size:0.95rem;"><i class="bi bi-skip-forward-circle me-1 text-info"></i>#<?php echo $next['item_code']; ?> <?php echo esc($next['item_name']); ?></div>
                                                    <div class="text-muted small mt-1" style="font-size:0.78rem;"><i class="bi bi-clock me-1"></i>Exp: <?php echo date('h:i A', strtotime($next['start_time'])); ?><span class="badge bg-light text-dark ms-1"><?php echo $next['item_time']; ?> min</span></div>
                                                <?php else: ?>
                                                    <div class="text-muted fst-italic mt-1 small">No subsequent events</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 pt-2 border-top">
                                            <a href="<?php echo base_url('stage/manager?stage_id=' . $st['stage_id']); ?>" class="btn btn-xs btn-outline-primary rounded-pill px-3 py-1" style="font-size:0.78rem;">
                                                <i class="bi bi-speedometer2 me-1"></i>Stage Console
                                            </a>
                                            <a href="<?php echo base_url('district/report/prereport/callsheet_first'); ?>" class="btn btn-xs btn-outline-secondary rounded-pill px-3 py-1" style="font-size:0.78rem;" target="_blank">
                                                <i class="bi bi-printer me-1"></i>Call Sheet
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar2-x-fill display-4 text-warning d-block mb-3"></i>
                            <h6>No District Stages or Schedules Defined Yet</h6>
                            <div class="small mb-3">Define district stages and allot competition items to activate live stage tracking.</div>
                            <a href="<?php echo base_url('district/stage'); ?>" class="btn btn-primary rounded-pill px-4"><i class="bi bi-plus-circle me-1"></i> Define Stages &amp; Venues</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="crm-card h-100">
                <div class="p-4 pb-0 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-trophy-fill text-warning me-2"></i>Leading Rank</h5>
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1">Championship</span>
                </div>
                <div class="p-4">
                    <?php if (!empty($leaderboard)): ?>
                        <?php foreach ($leaderboard as $idx => $lb): ?>
                            <?php
                            $r = (int)$lb['rank'];
                            $isLead = !empty($lb['is_leading']);
                            ?>
                            <div class="d-flex align-items-center gap-3 <?php echo ($idx > 0) ? 'pt-3 mt-3 border-top' : ''; ?>">
                                <span class="badge <?php echo $r <= 3 ? 'rank-badge-' . $r : 'bg-secondary-subtle text-secondary'; ?> rounded-circle flex-shrink-0" style="width:34px;height:34px;line-height:29px;font-size:0.95rem;"><?php echo $r; ?></span>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                        <?php echo esc($lb['sub_district_name']); ?>
                                        <?php if ($isLead): ?><i class="bi bi-trophy-fill text-warning"></i><?php endif; ?>
                                    </div>
                                    <div class="text-muted small"><?php echo (int)$lb['participants']; ?> participants &bull; <?php echo (int)$lb['items_completed']; ?> items done</div>
                                </div>
                                <span class="badge <?php echo ((int)$lb['points'] > 0) ? 'bg-warning text-dark' : 'bg-secondary-subtle text-secondary'; ?> rounded-pill px-3 flex-shrink-0"><?php echo (int)$lb['points']; ?> pts</span>
                            </div>
                        <?php endforeach; ?>
                        <?php if (!empty($leading_code)): ?>
                            <div class="alert alert-warning border-0 bg-warning-subtle text-warning-emphasis mt-3 mb-0 small rounded-4 d-flex align-items-center gap-2">
                                <i class="bi bi-flag-fill"></i>
                                <span><strong><?php echo array_values(array_filter($sub_districts, function($sd) use ($leading_code) { return (int)$sd['sub_district_code'] === (int)$leading_code; }))[0]['sub_district_name'] ?? ''; ?></strong> is currently leading the district championship.</span>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-bar-chart-fill display-4 text-primary d-block mb-2 opacity-50"></i>
                            <span>Points table will appear as sub-district results are finalized.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 7. COMMAND CENTER PODS -->
    <div class="row g-3 mb-4 anim-fade-up delay-4">
        <div class="col-lg-3 col-md-6">
            <div class="action-pod">
                <div class="pod-header">
                    <div class="pod-icon" style="background: var(--crm-gradient-indigo);"><i class="bi bi-person-badge"></i></div>
                    <div><h6 class="fw-bold mb-0 text-dark">Import & Registration</h6><small class="text-muted">Winner Intake Desk</small></div>
                </div>
                <a href="<?php echo base_url('district/import'); ?>" class="action-item-link priority-action-btn"><span><i class="bi bi-cloud-download-fill me-2"></i>Import Sub-District Winners</span><i class="bi bi-chevron-right chevron"></i></a>
                <a href="<?php echo base_url('district/schools/school_entry'); ?>" class="action-item-link"><span><i class="bi bi-person-plus-fill text-primary me-2"></i>School Entry (Photo)</span><i class="bi bi-chevron-right chevron"></i></a>
                <a href="<?php echo base_url('district/schools/registration'); ?>" class="action-item-link"><span><i class="bi bi-journal-text text-secondary me-2"></i>Registration Status</span><i class="bi bi-chevron-right chevron"></i></a>
                <a href="<?php echo base_url('item/item_master'); ?>" class="action-item-link"><span><i class="bi bi-list-stars text-info me-2"></i>Item Catalogue</span><i class="bi bi-chevron-right chevron"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="action-pod">
                <div class="pod-header">
                    <div class="pod-icon" style="background: var(--crm-gradient-amber);"><i class="bi bi-easel2"></i></div>
                    <div><h6 class="fw-bold mb-0 text-dark">Stage & Scheduling</h6><small class="text-muted">Venue & Program Dispatch</small></div>
                </div>
                <a href="<?php echo base_url('stage/manager'); ?>" class="action-item-link priority-action-btn"><span><i class="bi bi-broadcast-pin me-2"></i>Stage Manager Console</span><i class="bi bi-chevron-right chevron"></i></a>
                <a href="<?php echo base_url('district/stage'); ?>" class="action-item-link"><span><i class="bi bi-sliders text-warning me-2"></i>Define District Stages</span><i class="bi bi-chevron-right chevron"></i></a>
                <a href="<?php echo base_url('district/scheduling'); ?>" class="action-item-link"><span><i class="bi bi-calendar-range text-info me-2"></i>Festival Scheduling</span><i class="bi bi-chevron-right chevron"></i></a>
                <a href="<?php echo base_url('stage/allotment'); ?>" class="action-item-link"><span><i class="bi bi-easel2-fill text-dark me-2"></i>Stage Allotment (Item)</span><i class="bi bi-chevron-right chevron"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="action-pod">
                <div class="pod-header">
                    <div class="pod-icon" style="background: var(--crm-gradient-emerald);"><i class="bi bi-trophy"></i></div>
                    <div><h6 class="fw-bold mb-0 text-dark">Marks & Results</h6><small class="text-muted">Evaluation & Judgement</small></div>
                </div>
                <a href="<?php echo base_url('district/resultentry'); ?>" class="action-item-link priority-action-btn"><span><i class="bi bi-award-fill me-2"></i>Enter Competition Marks</span><i class="bi bi-chevron-right chevron"></i></a>
                <a href="<?php echo base_url('district/report/resultindex/resultview'); ?>" class="action-item-link"><span><i class="bi bi-broadcast text-danger me-2"></i>Publish Results Live</span><i class="bi bi-chevron-right chevron"></i></a>
                <a href="<?php echo base_url('district/report/afterresult_report/status_of_kalolsavam_interface'); ?>" class="action-item-link"><span><i class="bi bi-pie-chart-fill text-info me-2"></i>Status of Kalaivizha</span><i class="bi bi-chevron-right chevron"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="action-pod">
                <div class="pod-header">
                    <div class="pod-icon" style="background: var(--crm-gradient-violet);"><i class="bi bi-file-earmark-bar-graph"></i></div>
                    <div><h6 class="fw-bold mb-0 text-dark">Tabulation & Export</h6><small class="text-muted">Reports & State Qualification</small></div>
                </div>
                <a href="<?php echo base_url('district/report/afterresult_report/school_wise_result_interface'); ?>" class="action-item-link" target="_blank"><span><i class="bi bi-bar-chart-fill text-primary me-2"></i>District Points Table</span><i class="bi bi-chevron-right chevron"></i></a>
                <a href="<?php echo base_url('district/report/afterresult_report/higherlevel_result'); ?>" class="action-item-link"><span><i class="bi bi-cloud-arrow-up-fill text-danger me-2"></i>Higher Level Export</span><i class="bi bi-chevron-right chevron"></i></a>
                <a href="<?php echo base_url('district/certificate'); ?>" class="action-item-link"><span><i class="bi bi-patch-check text-warning me-2"></i>Certificates</span><i class="bi bi-chevron-right chevron"></i></a>
                <a href="<?php echo base_url('welcome/sub_district_details'); ?>" class="action-item-link"><span><i class="bi bi-grid-1x2-fill text-secondary me-2"></i>Sub-District Details</span><i class="bi bi-chevron-right chevron"></i></a>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    // Live clock
    function tickClock() {
        var el = document.getElementById('liveClockDisplay');
        if (!el) return;
        var now = new Date();
        var hh = String(now.getHours()).padStart(2, '0');
        var mm = String(now.getMinutes()).padStart(2, '0');
        var ss = String(now.getSeconds()).padStart(2, '0');
        el.textContent = hh + ':' + mm + ':' + ss;
    }
    tickClock();
    setInterval(tickClock, 1000);

    // Count-up animation
    function animateCountUp(el) {
        var target = parseInt(el.getAttribute('data-target'), 10) || 0;
        var duration = 800;
        var start = performance.now();
        function step(ts) {
            var p = Math.min((ts - start) / duration, 1);
            var eased = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.round(target * eased);
            if (p < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }
    document.querySelectorAll('.count-up').forEach(function (el) {
        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) { animateCountUp(el); obs.unobserve(el); }
                });
            }, { threshold: 0.3 });
            io.observe(el);
        } else {
            animateCountUp(el);
        }
    });

    // Spotlight quick find
    var searchItems = <?php echo json_encode($quick_search, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    var input = document.getElementById('spotlightSearch');
    var dropdown = document.getElementById('spotlightDropdown');
    if (input && dropdown) {
        input.addEventListener('input', function () {
            var q = input.value.trim().toLowerCase();
            dropdown.innerHTML = '';
            if (!q) { dropdown.style.display = 'none'; return; }
            var matches = searchItems.filter(function (it) {
                return (it.title + ' ' + it.sub + ' ' + it.type + ' ' + it.title).toLowerCase().indexOf(q) !== -1;
            });
            if (matches.length === 0) {
                dropdown.innerHTML = '<div class="p-3 text-muted text-center">No matching results</div>';
            } else {
                matches.slice(0, 15).forEach(function (it) {
                    var a = document.createElement('a');
                    a.className = 'spotlight-item';
                    a.href = it.url;
                    a.innerHTML = '<span><i class="bi ' + it.icon + ' me-2 text-primary"></i><strong>' + it.title + '</strong><small class="text-muted d-block ms-4">' + it.sub + '</small></span>';
                    dropdown.appendChild(a);
                });
            }
            dropdown.style.display = 'block';
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { dropdown.style.display = 'none'; input.value = ''; }
        });
        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) dropdown.style.display = 'none';
        });
    }
})();
</script>