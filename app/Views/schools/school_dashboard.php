<?php
/**
 * Tamil Kalolsavam - Modern Responsive School User Dashboard
 * Specially designed for School Users (USER_TYPE == 5)
 */
$school = $school ?? [];
$school_code = $school_code ?? ($school['school_code'] ?? '');
$stats = $stats ?? [
    'total_students' => 0, 'boys_count' => 0, 'girls_count' => 0,
    'lp_total' => 0, 'lp_boys' => 0, 'lp_girls' => 0,
    'up_total' => 0, 'up_boys' => 0, 'up_girls' => 0,
    'hs_total' => 0, 'hs_boys' => 0, 'hs_girls' => 0,
    'hss_total' => 0, 'hss_boys' => 0, 'hss_girls' => 0,
    'total_items_registered' => 0, 'total_results_declared' => 0,
    'total_points' => 0, 'prizes_1st' => 0, 'prizes_2nd' => 0, 'prizes_3rd' => 0,
    'grades_a' => 0, 'grades_b' => 0, 'grades_c' => 0,
    'subdistrict_rank' => '-', 'total_schools_in_subdist' => 0
];
$schedules = $schedules ?? [];
$results = $results ?? [];
$sub_district_code = $sub_district_code ?? ($school['sub_district_code'] ?? '');
$sub_district_name = $sub_district_name ?? ($school['sub_district_name'] ?? '');
$rev_district_code = $rev_district_code ?? ($school['rev_district_code'] ?? '');
$rev_district_name = $rev_district_name ?? ($school['rev_district_name'] ?? '');
$can_view_district = $can_view_district ?? false;
$current_level = $current_level ?? 'sd';
$is_district = ($current_level === 'd');
$is_sd_completed = $is_sd_completed ?? false;
$is_sd_confirmed = $is_sd_confirmed ?? false;
$is_district_reg_enabled = $is_district_reg_enabled ?? false;
$is_finalized = (($school['is_finalize'] ?? '') === 'Y') || $is_sd_confirmed;
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap');

/* TAU Font Declarations */
@font-face {
  font-family: 'TAU-Paalai';
  src: local('TAU-Paalai'), local('TAU-Pallai'), url('<?= base_url('assets/fonts/TAU-Paalai.ttf'); ?>') format('truetype');
  font-weight: normal;
  font-style: normal;
}
@font-face {
  font-family: 'TAU-Paalai';
  src: local('TAU-Paalai Bold'), local('TAU-Pallai Bold'), url('<?= base_url('assets/fonts/TAU-Paalai Bold.ttf'); ?>') format('truetype');
  font-weight: bold;
  font-style: normal;
}
@font-face {
  font-family: 'TAU-Pallai';
  src: local('TAU-Pallai'), local('TAU-Paalai'), url('<?= base_url('assets/fonts/TAU-Paalai.ttf'); ?>') format('truetype');
  font-weight: normal;
  font-style: normal;
}
@font-face {
  font-family: 'TAU-Pallai';
  src: local('TAU-Pallai Bold'), local('TAU-Paalai Bold'), url('<?= base_url('assets/fonts/TAU-Paalai Bold.ttf'); ?>') format('truetype');
  font-weight: bold;
  font-style: normal;
}
@font-face {
  font-family: 'TAU-Maduram';
  src: local('TAU-Maduram'), local('TAU-Marutham'), url('<?= base_url('assets/fonts/TAU-Marutham.ttf'); ?>') format('truetype');
  font-weight: normal;
  font-style: normal;
}
@font-face {
  font-family: 'TAU-Maduram';
  src: local('TAU-Maduram Bold'), local('TAU-Marutham Bold'), url('<?= base_url('assets/fonts/TAU-Marutham Bold.ttf'); ?>') format('truetype');
  font-weight: bold;
  font-style: normal;
}
@font-face {
  font-family: 'TAU-Marutham';
  src: local('TAU-Marutham'), local('TAU-Maduram'), url('<?= base_url('assets/fonts/TAU-Marutham.ttf'); ?>') format('truetype');
  font-weight: normal;
  font-style: normal;
}
@font-face {
  font-family: 'TAU-Marutham';
  src: local('TAU-Marutham Bold'), local('TAU-Maduram Bold'), url('<?= base_url('assets/fonts/TAU-Marutham Bold.ttf'); ?>') format('truetype');
  font-weight: bold;
  font-style: normal;
}

:root {
  --sch-bg: #f8fafc;
  --sch-card-bg: #ffffff;
  --sch-text-main: #0f172a;
  --sch-text-muted: #64748b;
  --sch-primary: #0284c7;
  --sch-primary-dark: #0369a1;
  --sch-gradient-hero: linear-gradient(135deg, #0f172a 0%, #0369a1 50%, #0284c7 100%);
  --sch-gradient-indigo: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);
  --sch-gradient-emerald: linear-gradient(135deg, #065f46 0%, #10b981 100%);
  --sch-gradient-amber: linear-gradient(135deg, #92400e 0%, #f59e0b 100%);
  --sch-gradient-purple: linear-gradient(135deg, #581c87 0%, #9333ea 100%);
  --sch-border: #e2e8f0;
  --sch-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05), 0 8px 10px -6px rgba(15, 23, 42, 0.03);
  --sch-radius: 16px;
}

.school-dashboard {
  /* English + numbers use Outfit; Tamil glyphs fall back to TAU-Paalai */
  font-family: 'Outfit', 'TAU-Paalai', 'TAU-Pallai', system-ui, -apple-system, sans-serif;
  font-size: 14px;
  color: var(--sch-text-main);
  background: var(--sch-bg);
  min-height: 100vh;
}

/* English + numbers use Outfit; Tamil headings use TAU-Maduram Bold */
.school-dashboard h1,
.school-dashboard h2,
.school-dashboard h3,
.school-dashboard h4,
.school-dashboard h5,
.school-dashboard h6,
.school-dashboard .sch-heading,
.dashboard-card-header h5 {
  font-family: 'Outfit', 'TAU-Maduram', 'TAU-Marutham', system-ui, -apple-system, sans-serif;
  font-weight: 700;
}

/* Keep form controls, buttons and badges on the same English-first / Tamil-fallback stack */
.school-dashboard .btn,
.school-dashboard .form-control,
.school-dashboard .form-select,
.school-dashboard .input-group-text,
.school-dashboard .badge,
.school-dashboard .dropdown-item,
.school-dashboard input,
.school-dashboard select,
.school-dashboard textarea {
  font-family: inherit;
}

/* Animations */
@keyframes schFadeUp {
  from { opacity: 0; transform: translateY(18px); }
  to { opacity: 1; transform: translateY(0); }
}

.sch-anim-up {
  animation: schFadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) backwards;
}
.delay-1 { animation-delay: 0.06s; }
.delay-2 { animation-delay: 0.12s; }
.delay-3 { animation-delay: 0.18s; }
.delay-4 { animation-delay: 0.24s; }

/* Hero Banner */
.sch-hero {
  background: var(--sch-gradient-hero);
  border-radius: var(--sch-radius);
  color: #ffffff;
  padding: 30px 34px;
  position: relative;
  overflow: visible !important;
  z-index: 20;
  box-shadow: 0 16px 36px -10px rgba(2, 132, 199, 0.35);
}

.sch-hero-bg-glow {
  position: absolute;
  inset: 0;
  border-radius: var(--sch-radius);
  overflow: hidden;
  pointer-events: none;
  z-index: 0;
}

.sch-hero-bg-glow::before {
  content: '';
  position: absolute;
  top: -40%;
  right: -15%;
  width: 450px;
  height: 450px;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0) 70%);
  border-radius: 50%;
  pointer-events: none;
}

.sch-hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  border-radius: 9999px;
  font-size: 0.76rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

/* KPI Tiles */
.kpi-metric-card {
  background: var(--sch-card-bg);
  border-radius: var(--sch-radius);
  padding: 18px 20px 14px 20px;
  border: 1px solid var(--sch-border);
  box-shadow: var(--sch-shadow);
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  position: relative;
  overflow: hidden;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.kpi-metric-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 16px 30px -8px rgba(15, 23, 42, 0.09);
  border-color: #cbd5e1;
}

.kpi-metric-card .metric-card-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
}

.kpi-metric-card .icon-bubble {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  color: #ffffff;
  flex-shrink: 0;
}

.kpi-metric-card .metric-val {
  font-size: 1.95rem;
  font-weight: 800;
  line-height: 1.15;
  color: var(--sch-text-main);
  letter-spacing: -0.02em;
}

.kpi-metric-card .metric-lbl-twoline {
  font-size: 0.92rem;
  font-weight: 600;
  color: var(--sch-text-muted);
  line-height: 1.35;
  min-height: 2.7em;
  display: flex;
  align-items: center;
  font-family: 'Outfit', 'TAU-Paalai', 'TAU-Pallai', system-ui, sans-serif;
  margin-top: 6px;
  margin-bottom: 8px;
}

.kpi-metric-card .metric-card-bottom {
  width: 100%;
  padding-top: 10px;
  margin-top: auto;
  border-top: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
}

/* Category Mini Cards */
.cat-pill-card {
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid var(--sch-border);
  padding: 14px 16px;
  box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
  transition: all 0.2s ease;
}
.cat-pill-card:hover {
  border-color: #94a3b8;
  transform: translateY(-2px);
}

/* Dashboard Table Wrap */
.dashboard-card-wrap {
  background: #ffffff;
  border-radius: var(--sch-radius);
  border: 1px solid var(--sch-border);
  box-shadow: var(--sch-shadow);
  overflow: hidden;
}

.dashboard-card-header {
  padding: 18px 24px;
  border-bottom: 1px solid var(--sch-border);
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}

.table-modern {
  width: 100%;
  margin-bottom: 0;
  border-collapse: separate;
  border-spacing: 0;
  font-family: 'Outfit', 'TAU-Paalai', 'TAU-Pallai', system-ui, -apple-system, sans-serif;
  font-size: 14px;
}

.table-modern thead th {
  background: #f8fafc;
  color: #334155;
  font-family: 'Outfit', 'TAU-Maduram', 'TAU-Marutham', system-ui, -apple-system, sans-serif;
  font-size: 14px;
  font-weight: 700;
  letter-spacing: 0.02em;
  padding: 12px 14px;
  border-bottom: 2px solid #e2e8f0;
  white-space: nowrap;
}

.table-modern tbody td {
  padding: 12px 14px;
  vertical-align: top;
  border-bottom: 1px solid #f1f5f9;
  font-family: 'Outfit', 'TAU-Paalai', 'TAU-Pallai', system-ui, -apple-system, sans-serif;
  font-size: 14px;
  color: #1e293b;
  line-height: 1.5;
}

.table-modern tbody tr:last-child td {
  border-bottom: none;
}

.table-modern tbody tr:hover td {
  background-color: #f8fafc;
}

/* Participant Stack & Mini Cards */
.participant-stack {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.participant-card-item {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 6px 10px;
  transition: all 0.15s ease;
}
.participant-card-item:hover {
  background: #f1f5f9;
  border-color: #cbd5e1;
}
.participant-card-item .p-name {
  font-weight: 700;
  color: #0f172a;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 6px;
}
.participant-card-item .p-meta {
  font-size: 13px;
  color: #475569;
  padding-left: 20px;
  line-height: 1.45;
}

/* Stage & Schedule Merged Box */
.stage-schedule-box {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.stage-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
  border-radius: 6px;
  font-weight: 700;
  font-size: 14px;
  box-shadow: 0 1px 2px rgba(29, 78, 216, 0.06);
}
.schedule-time-box {
  padding-left: 2px;
  font-size: 13px;
  line-height: 1.45;
}

/* Status Badges */
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 10px;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  white-space: nowrap;
}

.badge-scheduled { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
.badge-live { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; animation: pulseRadar 2s infinite; }
.badge-completed { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }
.badge-declared { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-weight: 700; }
.badge-pending { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

/* Rank Ribbons */
.rank-badge-1 {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  color: #ffffff;
  font-weight: 700;
  padding: 5px 12px;
  border-radius: 9999px;
  box-shadow: 0 4px 10px rgba(217, 119, 6, 0.3);
}
.rank-badge-2 {
  background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
  color: #ffffff;
  font-weight: 700;
  padding: 5px 12px;
  border-radius: 9999px;
}
.rank-badge-3 {
  background: linear-gradient(135deg, #b45309 0%, #78350f 100%);
  color: #ffffff;
  font-weight: 700;
  padding: 5px 12px;
  border-radius: 9999px;
}

.rank-badge-1,
.rank-badge-2,
.rank-badge-3 {
  display: inline-block;
  white-space: nowrap;
}

.rank-badge-1 sup,
.rank-badge-2 sup,
.rank-badge-3 sup {
  font-size: 0.68em;
  line-height: 0;
}

/* Grade Badges */
.grade-badge-a { background: #10b981; color: #ffffff; font-weight: 800; border-radius: 6px; padding: 4px 10px; }
.grade-badge-b { background: #3b82f6; color: #ffffff; font-weight: 800; border-radius: 6px; padding: 4px 10px; }
.grade-badge-c { background: #f59e0b; color: #ffffff; font-weight: 800; border-radius: 6px; padding: 4px 10px; }

/* Action Banner */
.public-portal-banner {
  background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  border-radius: var(--sch-radius);
  color: #ffffff;
  padding: 24px 30px;
  box-shadow: 0 12px 25px rgba(15, 23, 42, 0.15);
  border: 1px solid #334155;
}

@media print {
  .no-print { display: none !important; }
  .dashboard-card-wrap { box-shadow: none !important; border: 1px solid #000 !important; }
}
</style>

<div class="school-dashboard px-3 px-md-4 py-4">

  <!-- 1. School Header Card -->
  <div class="sch-hero mb-4 sch-anim-up position-relative">
    <div class="sch-hero-bg-glow"></div>
    <div class="row align-items-center g-3 position-relative" style="z-index: 1;">
      <div class="col-lg-8 col-md-12">
        <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
          <?php if ($is_district): ?>
            <span class="sch-hero-badge" style="background: rgba(245, 158, 11, 0.25); border-color: rgba(245, 158, 11, 0.4);">
              <i class="bi bi-trophy-fill text-warning"></i> மாவட்டப் பிரிவு (District Level)
            </span>
            <!-- District level: Winners directly imported -->
            <span class="badge text-white px-3 py-1 rounded-pill" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);">
              <i class="bi bi-patch-check-fill me-1"></i> தகுதிபெற்ற வெற்றியாளர்கள் (Qualified Winners)
            </span>
          <?php else: ?>
            <span class="sch-hero-badge">
              <i class="bi bi-geo-alt-fill text-info"></i> சப்டிஸ்டிக் பிரிவு (Sub-District)
            </span>
            <?php if ($is_sd_confirmed): ?>
              <span class="badge bg-success text-white px-3 py-1 rounded-pill shadow-sm">
                <i class="bi bi-check-circle-fill me-1"></i> பதிவு உறுதி செய்யப்பட்டது (Confirmed)
              </span>
            <?php else: ?>
              <span class="badge bg-warning text-dark px-3 py-1 rounded-pill shadow-sm">
                <i class="bi bi-clock-history me-1"></i> பதிவு வரைவு நிலை (In-Progress)
              </span>
            <?php endif; ?>
          <?php endif; ?>
        </div>

        <h3 class="mb-1 fw-bold text-white d-flex align-items-center flex-wrap gap-2">
          <span style="color: #ff3385; font-weight: 800; text-shadow: 0 2px 10px rgba(255, 51, 133, 0.35);"><?= esc($school_code); ?></span>
          <span class="text-white-50">-</span>
          <span><?= esc($school['school_name'] ?? 'School Name'); ?></span>
        </h3>
        
        <div class="d-flex align-items-center gap-3 text-white-50 small flex-wrap mt-2">
          <span>
            <i class="bi bi-geo-alt-fill text-info me-1"></i>
            <?= esc($school['sub_district_name'] ?? ''); ?> துணை மாவட்டம் (<?= esc($school['rev_district_name'] ?? ''); ?>)
          </span>
          <?php if (!empty($school['hm_name'])): ?>
            <span>
              <i class="bi bi-person-fill text-warning me-1"></i>
              HM: <?= esc($school['hm_name']); ?>
              <?php if (!empty($school['hm_phone'])): ?>
                (<?= esc($school['hm_phone']); ?>)
              <?php endif; ?>
            </span>
          <?php endif; ?>
          <?php if (!empty($school['school_email'])): ?>
            <span>
              <i class="bi bi-envelope-fill text-light me-1"></i>
              <?= esc($school['school_email']); ?>
            </span>
          <?php endif; ?>
        </div>
      </div>

      <div class="col-lg-4 col-md-12 text-lg-end no-print">
        <?php if (!empty($can_view_district)): ?>
          <!-- Sub-District / District Level Switcher Dropup (Pure English & Opens Upwards) -->
          <div class="mb-2">
            <div class="dropup d-inline-block" style="position: relative; z-index: 1050;">
              <button class="btn btn-sm btn-light dropdown-toggle fw-bold rounded-pill px-3 py-1.5 shadow-sm d-inline-flex align-items-center gap-1.5 border" type="button" id="levelSelectDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.85rem;">
                <?php if ($is_district): ?>
                  <i class="bi bi-trophy-fill text-warning"></i>
                  <span>District</span>
                <?php else: ?>
                  <i class="bi bi-geo-alt-fill text-primary"></i>
                  <span>Sub-District</span>
                <?php endif; ?>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mb-1 py-1" aria-labelledby="levelSelectDropdown" style="min-width: 190px; font-size: 0.88rem; z-index: 1060; box-shadow: 0 10px 25px rgba(0,0,0,0.18) !important;">
                <li class="px-3 py-1.5 text-muted small fw-semibold text-uppercase border-bottom mb-1" style="font-size: 0.68rem; letter-spacing: 0.05em;">
                  Select Level
                </li>
                <li>
                  <a class="dropdown-item py-2 px-3 fw-semibold d-flex align-items-center justify-content-between <?= !$is_district ? 'active bg-primary text-white' : 'text-dark'; ?>" 
                     href="<?= base_url('welcome/school_dashboard?level=sd'); ?>">
                    <span class="d-flex align-items-center gap-2">
                      <i class="bi bi-geo-alt-fill <?= !$is_district ? 'text-white' : 'text-primary'; ?>"></i>
                      <span>Sub-District</span>
                    </span>
                    <?php if (!$is_district): ?>
                      <i class="bi bi-check-lg"></i>
                    <?php endif; ?>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item py-2 px-3 fw-semibold d-flex align-items-center justify-content-between <?= $is_district ? 'active text-white' : 'text-dark'; ?>" 
                     style="<?= $is_district ? 'background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);' : ''; ?>"
                     href="<?= base_url('welcome/school_dashboard?level=d'); ?>">
                    <span class="d-flex align-items-center gap-2">
                      <i class="bi bi-trophy-fill <?= $is_district ? 'text-white' : 'text-warning'; ?>"></i>
                      <span>District</span>
                    </span>
                    <?php if ($is_district): ?>
                      <i class="bi bi-check-lg"></i>
                    <?php endif; ?>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        <?php endif; ?>

        <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
          <?php if (!$is_district): ?>
            <?php if (!$is_sd_confirmed): ?>
              <a href="<?= base_url('schools/registration'); ?>" class="btn btn-light btn-sm fw-semibold rounded-pill px-3 shadow-sm">
                <i class="bi bi-people-fill text-primary me-1"></i> மாணவர்கள் பதிவு
              </a>
            <?php endif; ?>
          <?php else: ?>
            <?php if (!empty($is_district_reg_enabled)): ?>
              <a href="<?= base_url('district/schools/registration'); ?>" class="btn btn-warning text-dark btn-sm fw-bold rounded-pill px-3 shadow-sm">
                <i class="bi bi-person-plus-fill me-1"></i> மாவட்டப் பதிவு (Registration)
              </a>
            <?php endif; ?>
          <?php endif; ?>
          <a href="<?= base_url('login/change_password'); ?>" class="btn btn-outline-light btn-sm fw-semibold rounded-pill px-3">
            <i class="bi bi-person-gear me-1"></i> சுயவிவரம்
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. KPI Metric Cards Row -->
  <div class="row g-3 mb-4">
    <!-- Total Students Registered -->
    <div class="col-xl-3 col-md-6 sch-anim-up delay-1">
      <div class="kpi-metric-card">
        <div class="metric-card-top">
          <div class="metric-val"><?= number_format($stats['total_students']); ?></div>
          <div class="icon-bubble" style="background: var(--sch-gradient-indigo);">
            <i class="bi bi-people-fill"></i>
          </div>
        </div>
        <div class="metric-lbl-twoline">
          பதிவுசெய்த<br>மாணவர்கள்
        </div>
        <div class="metric-card-bottom">
          <div class="d-flex align-items-center justify-content-between w-100 gap-2">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 flex-fill text-center" style="font-size: 0.82rem;">
              <i class="bi bi-gender-male me-1"></i>ஆண்: <?= $stats['boys_count']; ?>
            </span>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 flex-fill text-center" style="font-size: 0.82rem;">
              <i class="bi bi-gender-female me-1"></i>பெண்: <?= $stats['girls_count']; ?>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Events / Competitions Participated -->
    <div class="col-xl-3 col-md-6 sch-anim-up delay-2">
      <div class="kpi-metric-card">
        <div class="metric-card-top">
          <div class="metric-val"><?= number_format($stats['total_items_registered']); ?></div>
          <div class="icon-bubble" style="background: var(--sch-gradient-emerald);">
            <i class="bi bi-calendar2-check-fill"></i>
          </div>
        </div>
        <div class="metric-lbl-twoline">
          பங்கேற்கும்<br>போட்டிகள்
        </div>
        <div class="metric-card-bottom">
          <div class="d-flex align-items-center justify-content-between w-100 gap-1">
            <?php if (!$is_district): ?>
              <span class="badge bg-light text-secondary border px-1.5 py-1.5 flex-fill text-center" style="font-size: 0.8rem;">LP: <?= $stats['lp_total']; ?></span>
            <?php endif; ?>
            <span class="badge bg-light text-secondary border px-1.5 py-1.5 flex-fill text-center" style="font-size: 0.8rem;">UP: <?= $stats['up_total']; ?></span>
            <span class="badge bg-light text-secondary border px-1.5 py-1.5 flex-fill text-center" style="font-size: 0.8rem;">HS: <?= $stats['hs_total']; ?></span>
            <span class="badge bg-light text-secondary border px-1.5 py-1.5 flex-fill text-center" style="font-size: 0.8rem;">HSS: <?= $stats['hss_total']; ?></span>
          </div>
        </div>
      </div>
    </div>

    <!-- School Total Points -->
    <div class="col-xl-3 col-md-6 sch-anim-up delay-3">
      <div class="kpi-metric-card">
        <div class="metric-card-top">
          <div class="metric-val text-warning">
            <?= number_format($stats['total_points']); ?> <span class="fs-6 text-muted fw-normal">Pts</span>
          </div>
          <div class="icon-bubble" style="background: var(--sch-gradient-amber);">
            <i class="bi bi-star-fill"></i>
          </div>
        </div>
        <div class="metric-lbl-twoline">
          பள்ளியின்<br>மொத்த புள்ளிகள்
        </div>
        <div class="metric-card-bottom">
          <div class="w-100">
            <?php if ($stats['subdistrict_rank'] !== '-' && (int)$stats['subdistrict_rank'] > 0): ?>
              <span class="badge bg-warning-subtle text-dark border border-warning-subtle fw-semibold d-flex align-items-center justify-content-center w-100 py-1.5" style="font-size: 0.82rem;">
                <i class="bi bi-trophy-fill text-warning me-1"></i><?= $is_district ? 'மாவட்ட தரம்: #' : 'துணை மாவட்ட தரம்: #'; ?><?= $stats['subdistrict_rank']; ?>
              </span>
            <?php else: ?>
              <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fw-semibold d-flex align-items-center justify-content-center w-100 py-1.5" style="font-size: 0.82rem;">
                <i class="bi bi-hourglass-split me-1"></i>முடிவு நிலுவையில்
              </span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Prizes & Grades Won -->
    <div class="col-xl-3 col-md-6 sch-anim-up delay-4">
      <div class="kpi-metric-card">
        <div class="metric-card-top">
          <div class="metric-val text-primary">
            <?= number_format($stats['prizes_1st'] + $stats['prizes_2nd'] + $stats['prizes_3rd']); ?> 
            <span class="fs-6 text-muted fw-normal">பரிசுகள்</span>
          </div>
          <div class="icon-bubble" style="background: var(--sch-gradient-purple);">
            <i class="bi bi-award-fill"></i>
          </div>
        </div>
        <div class="metric-lbl-twoline">
          வெற்றி பெற்ற<br>கிரேடுகள்
        </div>
        <div class="metric-card-bottom">
          <div class="d-flex align-items-center justify-content-between w-100 gap-1.5">
            <span class="badge bg-success flex-fill text-center py-1.5" title="A Grade" style="font-size: 0.82rem;">A: <?= $stats['grades_a']; ?></span>
            <span class="badge bg-primary flex-fill text-center py-1.5" title="B Grade" style="font-size: 0.82rem;">B: <?= $stats['grades_b']; ?></span>
            <span class="badge bg-info text-dark flex-fill text-center py-1.5" title="C Grade" style="font-size: 0.82rem;">C: <?= $stats['grades_c']; ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 3. Category Breakdown Mini Bar -->
  <div class="row g-3 mb-4 sch-anim-up delay-2">
    <?php if (!$is_district): ?>
    <div class="col-md-3 col-6">
      <div class="cat-pill-card d-flex justify-content-between align-items-center">
        <div>
          <div class="fw-bold text-dark fs-6">LP Kalaivizha</div>
          <div class="small text-muted">வகுப்பு 1 - 4</div>
        </div>
        <div class="text-end">
          <div class="fw-bold fs-5 text-primary"><?= $stats['lp_total']; ?></div>
          <div class="small text-muted">ஆ: <?= $stats['lp_boys']; ?> | பெ: <?= $stats['lp_girls']; ?></div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <div class="<?= $is_district ? 'col-md-4 col-12' : 'col-md-3 col-6'; ?>">
      <div class="cat-pill-card d-flex justify-content-between align-items-center">
        <div>
          <div class="fw-bold text-dark fs-6">UP Kalaivizha</div>
          <div class="small text-muted">வகுப்பு 5 - 7</div>
        </div>
        <div class="text-end">
          <div class="fw-bold fs-5 text-success"><?= $stats['up_total']; ?></div>
          <div class="small text-muted">ஆ: <?= $stats['up_boys']; ?> | பெ: <?= $stats['up_girls']; ?></div>
        </div>
      </div>
    </div>
    <div class="<?= $is_district ? 'col-md-4 col-12' : 'col-md-3 col-6'; ?>">
      <div class="cat-pill-card d-flex justify-content-between align-items-center">
        <div>
          <div class="fw-bold text-dark fs-6">HS Kalaivizha</div>
          <div class="small text-muted">வகுப்பு 8 - 10</div>
        </div>
        <div class="text-end">
          <div class="fw-bold fs-5 text-warning"><?= $stats['hs_total']; ?></div>
          <div class="small text-muted">ஆ: <?= $stats['hs_boys']; ?> | பெ: <?= $stats['hs_girls']; ?></div>
        </div>
      </div>
    </div>
    <div class="<?= $is_district ? 'col-md-4 col-12' : 'col-md-3 col-6'; ?>">
      <div class="cat-pill-card d-flex justify-content-between align-items-center">
        <div>
          <div class="fw-bold text-dark fs-6">HSS Kalaivizha</div>
          <div class="small text-muted">வகுப்பு 11 - 12</div>
        </div>
        <div class="text-end">
          <div class="fw-bold fs-5" style="color: #9333ea;"><?= $stats['hss_total']; ?></div>
          <div class="small text-muted">ஆ: <?= $stats['hss_boys']; ?> | பெ: <?= $stats['hss_girls']; ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- 4. Main Content: Two Interactive Tabs / Sections -->
  <div class="row g-4 mb-4">
    <!-- Competition Stage & Schedule Status -->
    <div class="col-12 sch-anim-up delay-3">
      <div class="dashboard-card-wrap">
        <div class="dashboard-card-header">
          <div class="d-flex align-items-center gap-2">
            <span class="rounded-circle p-2 bg-primary-subtle text-primary">
              <i class="bi bi-diagram-3-fill fs-5"></i>
            </span>
            <div>
              <h5 class="mb-0 fw-bold text-dark"><?= $is_district ? 'மாவட்டப் போட்டி மேடை மற்றும் நேர அட்டவணை (District Stage & Schedule)' : 'போட்டி மேடை மற்றும் நேர அட்டவணை (Stage & Schedule Status)'; ?></h5>
              <div class="small text-muted"><?= $is_district ? 'மாவட்ட ஸ்டேஜ் மேனேஜர் அட்டவணைப்படி மேடையும் நேரமும் நிர்ணயிக்கப்பட்ட போட்டிகள்' : 'துணை மாவட்ட ஸ்டேஜ் மேனேஜர் அட்டவணைப்படி மேடையும் நேரமும் நிர்ணயிக்கப்பட்ட போட்டிகள்'; ?></div>
            </div>
          </div>

          <?php if (!empty($schedules)): ?>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-semibold small">
              <?= count($schedules); ?> போட்டிகள் திட்டமிடப்பட்டுள்ளன
            </span>
            <div class="input-group input-group-sm" style="max-width: 240px;">
              <span class="input-group-text bg-white border-secondary-subtle text-muted">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" id="schSearchInput" class="form-control border-secondary-subtle" placeholder="போட்டி / மாணவர் பெயர்...">
            </div>
          </div>
          <?php endif; ?>
        </div>

        <?php if (empty($schedules)): ?>
          <div class="p-4 text-center">
            <div class="py-4">
              <i class="bi bi-calendar-x fs-1 text-warning d-block mb-3 opacity-75"></i>
              <h6 class="fw-bold text-dark mb-1"><?= $is_district ? 'மாவட்ட அளவிலான போட்டி மேடை மற்றும் நேர அட்டவணை இன்னும் அமைக்கப்படவில்லை' : 'போட்டி மேடை மற்றும் நேர அட்டவணை இன்னும் அமைக்கப்படவில்லை'; ?></h6>
              <p class="text-muted small mb-0"><?= $is_district ? 'மாவட்ட ஸ்டேஜ் மேனேஜர் / அட்மின் தரப்பில் போட்டிகளுக்கான மேடையும் தொடங்கும் நேரமும் நிர்ணயிக்கப்பட்ட பின் (Stage & Schedule Set), அந்த விவரங்கள் இங்கே நேரடியாகக் காட்சியளிக்கும்.' : 'ஸ்டேஜ் மேனேஜர் / அட்மின் தரப்பில் போட்டிகளுக்கான மேடையும் தொடங்கும் நேரமும் நிர்ணயிக்கப்பட்ட பின் (Stage & Schedule Set), அந்த விவரங்கள் இங்கே நேரடியாகக் காட்சியளிக்கும்.'; ?></p>
            </div>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-modern" id="schEventsTable">
              <thead>
                <tr>
                  <th style="width: 50px;" class="text-center">வ.எண்</th>
                  <th style="width: 28%;">போட்டி விவரம்</th>
                  <th style="width: 32%;">பங்கேற்கும் மாணவர்கள்</th>
                  <th style="width: 25%;">ஒதுக்கப்பட்ட மேடை & நேரம்</th>
                  <th class="text-center" style="width: 15%;">நிலை (Status)</th>
                </tr>
              </thead>
              <tbody>
                <?php $sl = 1; foreach ($schedules as $item): 
                  $parts = $item['participants'] ?? [];
                  $item_status = \App\Libraries\CompetitionStatus::resolve($item);
                ?>
                  <tr>
                    <td class="text-center text-muted fw-semibold"><?= $sl++; ?></td>
                    <td>
                      <div class="fw-bold text-dark">
                        <span class="badge bg-secondary-subtle text-secondary me-1"><?= esc($item['item_code']); ?></span>
                        <?= esc($item['item_name'] ?? 'Item'); ?>
                      </div>
                      <div class="small text-muted mt-1.5 d-flex flex-wrap align-items-center gap-1">
                        <span class="badge bg-light text-dark border"><?= esc($item['fest_name'] ?? 'General'); ?></span>
                        <?php if (($item['item_type'] ?? '') === 'G'): ?>
                          <span class="badge bg-info-subtle text-info border border-info-subtle">குழுப் போட்டி (Group)</span>
                        <?php else: ?>
                          <span class="badge bg-light text-secondary border">தனிப் போட்டி (Single)</span>
                        <?php endif; ?>
                        <?php if (!empty($item['item_time'])): ?>
                          <span class="text-muted ms-1"><i class="bi bi-stopwatch text-secondary"></i> <?= esc($item['item_time']); ?> நிமிடம்</span>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td>
                      <?php if (empty($parts)): ?>
                        <span class="text-muted small">மாணவர் பெயர் இல்லை</span>
                      <?php else: ?>
                        <div class="participant-stack">
                          <?php foreach ($parts as $p): ?>
                            <div class="participant-card-item">
                              <div class="p-name">
                                <?php if (strtoupper($p['gender'] ?? '') === 'G'): ?>
                                  <i class="bi bi-gender-female text-danger fs-6" title="மாணவி"></i>
                                <?php else: ?>
                                  <i class="bi bi-gender-male text-primary fs-6" title="மாணவர்"></i>
                                <?php endif; ?>
                                <span><?= esc($p['participant_name']); ?></span>
                              </div>
                              <div class="p-meta">
                                <div><span class="text-muted">வகுப்பு:</span> <span class="fw-semibold text-dark"><?= esc($p['class']); ?></span></div>
                                <?php if (!empty($p['admn_no'])): ?>
                                  <div><span class="text-muted">அட்மிஷன் எண்:</span> <span class="fw-semibold text-dark"><?= esc($p['admn_no']); ?></span></div>
                                <?php endif; ?>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="stage-schedule-box">
                        <!-- 1. ஒதுக்கப்பட்ட மேடை (மேடைப் பெயர் மட்டும்) -->
                        <div>
                          <?php if (!empty($item['stage_name'])): ?>
                            <span class="stage-pill">
                              <i class="bi bi-geo-alt-fill text-danger"></i>
                              <?= esc($item['stage_name']); ?>
                            </span>
                          <?php else: ?>
                            <span class="status-badge badge-pending">
                              <i class="bi bi-hourglass-split"></i> மேடை ஒதுக்கீடு ஆகிறது
                            </span>
                          <?php endif; ?>
                        </div>

                        <!-- 2. திட்டமிடப்பட்ட நாள் & நேரம் (அடுத்தடுத்து) -->
                        <div class="schedule-time-box">
                          <?php if (!empty($item['start_time']) && $item['start_time'] !== '0000-00-00 00:00:00'): ?>
                            <div class="fw-semibold text-dark d-flex align-items-center gap-1.5">
                              <i class="bi bi-calendar-event text-primary"></i>
                              <span><?= date('d-m-Y', strtotime($item['start_time'])); ?></span>
                            </div>
                            <div class="text-muted d-flex align-items-center gap-1.5 mt-0.5">
                              <i class="bi bi-clock text-secondary"></i>
                              <span><?= date('h:i A', strtotime($item['start_time'])); ?></span>
                            </div>
                          <?php else: ?>
                            <div class="text-muted d-flex align-items-center gap-1">
                              <i class="bi bi-clock-history"></i>
                              <span>நேரம் அறிவிக்கப்படும்</span>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </td>
                    <td class="text-center align-middle">
                      <span class="status-badge <?= esc($item_status['badge']); ?>">
                        <i class="bi <?= esc($item_status['icon']); ?>"></i> <?= esc($item_status['label']); ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-3 border-top" style="border-color: var(--sch-border) !important;">
            <div class="d-flex align-items-center gap-2 small text-muted">
              <span>Show</span>
              <select id="schPageSize" class="form-select form-select-sm rounded-3" style="width: 80px;">
                <option value="5" selected>5</option>
                <option value="15">15</option>
                <option value="25">25</option>
              </select>
              <span>entries</span>
            </div>
            <div id="schTableInfo" class="small text-muted"></div>
            <nav aria-label="Stage schedule pagination">
              <ul class="pagination pagination-sm mb-0" id="schPagination"></ul>
            </nav>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Declared Results of this School -->
    <div class="col-12 sch-anim-up delay-4">
      <div class="dashboard-card-wrap">
        <div class="dashboard-card-header">
          <div class="d-flex align-items-center gap-2">
            <span class="rounded-circle p-2 bg-success-subtle text-success">
              <i class="bi bi-trophy-fill fs-5"></i>
            </span>
            <div>
              <h5 class="mb-0 fw-bold text-dark"><?= $is_district ? 'மாவட்ட அளவிலான பள்ளி மாணவர்களின் முடிவுகள் & கிரேடு தரம் (District Results & Grades)' : 'பள்ளி மாணவர்களின் முடிவுகள் & கிரேடு தரம் (Declared Results & Grades)'; ?></h5>
              <div class="small text-muted"><?= $is_district ? 'மாவட்டப் போட்டிகளில் இப்பள்ளி மாணவர்கள் பெற்ற முடிவுகள் மற்றும் கிரேடு விவரங்கள்' : 'இப்பள்ளி மாணவர்கள் வெற்றி பெற்ற பரிசுகள் மற்றும் கிரேடு மதிப்பெண் விவரங்கள்'; ?></div>
            </div>
          </div>

          <div>
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold">
              மொத்தம்: <?= count($results); ?> முடிவுகள்
            </span>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-modern" id="schResultsTable">
            <thead>
              <tr>
                <th style="width: 60px;" class="text-center">வ.எண்</th>
                <th>போட்டி விவரம்</th>
                <th>மாணவர் பெயர்</th>
                <th>வகுப்பு</th>
                <th class="text-center">பரிசு தரம் (Rank)</th>
                <th class="text-center">கிரேடு (Grade)</th>
                <th class="text-center">பள்ளி புள்ளிகள்</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($results)): ?>
                <tr>
                  <td colspan="7" class="text-center py-5">
                    <div class="py-3">
                      <i class="bi bi-award fs-1 text-muted opacity-50 d-block mb-3"></i>
                      <h6 class="fw-bold text-secondary mb-1"><?= $is_district ? 'மாவட்ட அளவிலான முடிவுகள் இன்னும் வெளியிடப்படவில்லை' : 'முடிவுகள் இன்னும் வெளியிடப்படவில்லை'; ?></h6>
                      <p class="text-muted small mb-0"><?= $is_district ? 'மாவட்ட நடுவர்கள் மற்றும் அட்மின் தரப்பில் முடிவுகள் பதிவு செய்யப்பட்டு வெளியிடப்பட்டவுடன் இங்கு தானாகவே காட்சியளிக்கும்.' : 'நடுவர்கள் மற்றும் அட்மின் தரப்பில் முடிவுகள் பதிவு செய்யப்பட்டு வெளியிடப்பட்டவுடன் இங்கு தானாகவே காட்சியளிக்கும்.'; ?></p>
                    </div>
                  </td>
                </tr>
              <?php else: ?>
                <?php $rsl = 1; foreach ($results as $res): 
                  $grade = strtoupper(trim($res['published_grade'] ?? $res['grade']));
                  $rank = (int)$res['rank'];
                  $point = (int)$res['point'];
                ?>
                  <tr>
                    <td class="text-center text-muted fw-semibold"><?= $rsl++; ?></td>
                    <td>
                      <div class="fw-bold text-dark">
                        <span class="badge bg-secondary-subtle text-secondary me-1"><?= esc($res['item_code']); ?></span>
                        <?= esc($res['item_name'] ?? ''); ?>
                      </div>
                      <div class="small text-muted"><?= esc($res['fest_name'] ?? ''); ?></div>
                    </td>
                    <td>
                      <div class="fw-bold text-dark"><?= esc($res['participant_name']); ?></div>
                      <div class="small text-muted">#<?= esc($res['admn_no']); ?></div>
                    </td>
                    <td>
                      <span class="badge bg-light text-dark border">வகுப்பு <?= esc($res['class']); ?></span>
                    </td>
                    <td class="text-center">
                      <?php if ($rank === 1): ?>
                        <span class="rank-badge-1"><i class="bi bi-trophy-fill me-1"></i>1<sup>st</sup></span>
                      <?php elseif ($rank === 2): ?>
                        <span class="rank-badge-2"><i class="bi bi-award-fill me-1"></i>2<sup>nd</sup></span>
                      <?php elseif ($rank === 3): ?>
                        <span class="rank-badge-3"><i class="bi bi-award me-1"></i>3<sup>rd</sup></span>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center">
                      <?php if ($grade === 'A'): ?>
                        <span class="grade-badge-a">A Grade</span>
                      <?php elseif ($grade === 'B'): ?>
                        <span class="grade-badge-b">B Grade</span>
                      <?php elseif ($grade === 'C'): ?>
                        <span class="grade-badge-c">C Grade</span>
                      <?php else: ?>
                        <span class="badge bg-secondary text-white"><?= esc($grade); ?></span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center">
                      <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 px-3 py-1 fw-bold">
                        +<?= $point; ?> Pts
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <?php if (!empty($results)): ?>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-3 border-top" style="border-color: var(--sch-border) !important;">
          <div class="d-flex align-items-center gap-2 small text-muted">
            <span>Show</span>
            <select id="resPageSize" class="form-select form-select-sm rounded-3" style="width: 80px;">
              <option value="5" selected>5</option>
              <option value="15">15</option>
              <option value="25">25</option>
            </select>
            <span>entries</span>
          </div>
          <div id="resTableInfo" class="small text-muted"></div>
          <nav aria-label="Declared results pagination">
            <ul class="pagination pagination-sm mb-0" id="resPagination"></ul>
          </nav>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- 5. Public Results & Overall Standings Navigation Banner -->
  <div class="public-portal-banner sch-anim-up delay-4 mb-4">
    <div class="row align-items-center g-3">
      <div class="col-lg-8 col-md-12">
        <div class="d-flex align-items-center gap-3">
          <span class="fs-1 text-warning">
            <i class="bi bi-globe2"></i>
          </span>
          <div>
            <h5 class="fw-bold mb-1 text-white">பொது முடிவுகளைக் காண பப்ளிக் ரிசல்ட் போர்ட்டல் (Public Results Portal)</h5>
            <p class="text-white-50 mb-0 small">
              மற்ற அனைத்து பள்ளிகளின் நிலை, துணை மாவட்டம் மற்றும் மாவட்ட அளவிலான ஒட்டுமொத்த தரவரிசைப் பட்டியல் மற்றும் அனைத்து முடிவுகளையும் பொது தளத்தில் காணலாம்.
            </p>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-12 text-lg-end">
        <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
          <a href="<?= base_url('publishresult/publicresult/schoolpoints'); ?>" target="_blank" class="btn btn-warning fw-bold px-3 py-2 rounded-pill shadow-sm">
            <i class="bi bi-bar-chart-fill me-1"></i> பள்ளி புள்ளிகள் பட்டியல்
          </a>
          <a href="<?= base_url('publishresult/publicresult/resultview'); ?>" target="_blank" class="btn btn-outline-light fw-semibold px-3 py-2 rounded-pill">
            <i class="bi bi-box-arrow-up-right me-1"></i> பப்ளிக் ரிசல்ட்
          </a>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
// Stage & Schedule table: client-side pagination (5 / 15 / 25) + live search
document.addEventListener('DOMContentLoaded', function() {
  var table = document.getElementById('schEventsTable');
  if (!table) return;

  var tbody = table.querySelector('tbody');
  if (!tbody) return;

  var allRows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
  if (allRows.length === 0) return;

  var searchInput = document.getElementById('schSearchInput');
  var pageSizeSelect = document.getElementById('schPageSize');
  var paginationUl = document.getElementById('schPagination');
  var infoEl = document.getElementById('schTableInfo');

  var currentPage = 1;
  var pageSize = pageSizeSelect ? (parseInt(pageSizeSelect.value, 10) || 5) : 5;
  var currentFilter = '';

  function getFilteredRows() {
    if (!currentFilter) return allRows;
    var q = currentFilter.toLowerCase();
    return allRows.filter(function(row) {
      return (row.textContent || '').toLowerCase().indexOf(q) > -1;
    });
  }

  function createPagerItem(label, page, disabled, active) {
    var li = document.createElement('li');
    li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
    var a = document.createElement('a');
    a.className = 'page-link rounded-3 px-3 shadow-2xs' + (active ? ' fw-bold text-white' : '');
    a.href = 'javascript:void(0)';
    a.innerHTML = label;
    if (!disabled && !active) {
      a.addEventListener('click', function(e) {
        e.preventDefault();
        currentPage = page;
        render();
      });
    }
    li.appendChild(a);
    return li;
  }

  function renderPagination(totalPages) {
    if (!paginationUl) return;
    paginationUl.innerHTML = '';

    paginationUl.appendChild(createPagerItem('<i class="bi bi-chevron-left"></i>', currentPage - 1, currentPage === 1));

    var maxVisible = 5;
    var startPage = Math.max(1, currentPage - 2);
    var endPage = Math.min(totalPages, startPage + maxVisible - 1);
    if (endPage - startPage < maxVisible - 1) {
      startPage = Math.max(1, endPage - maxVisible + 1);
    }

    if (startPage > 1) {
      paginationUl.appendChild(createPagerItem('1', 1));
      if (startPage > 2) {
        var dots = document.createElement('li');
        dots.className = 'page-item disabled';
        dots.innerHTML = '<span class="page-link border-0">...</span>';
        paginationUl.appendChild(dots);
      }
    }

    for (var p = startPage; p <= endPage; p++) {
      paginationUl.appendChild(createPagerItem(String(p), p, false, p === currentPage));
    }

    if (endPage < totalPages) {
      if (endPage < totalPages - 1) {
        var dots2 = document.createElement('li');
        dots2.className = 'page-item disabled';
        dots2.innerHTML = '<span class="page-link border-0">...</span>';
        paginationUl.appendChild(dots2);
      }
      paginationUl.appendChild(createPagerItem(String(totalPages), totalPages));
    }

    paginationUl.appendChild(createPagerItem('<i class="bi bi-chevron-right"></i>', currentPage + 1, currentPage === totalPages));
  }

  function render() {
    var filtered = getFilteredRows();
    var total = filtered.length;
    var totalPages = Math.max(1, Math.ceil(total / pageSize));
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    var start = (currentPage - 1) * pageSize;
    var end = start + pageSize;

    allRows.forEach(function(row) {
      row.style.display = 'none';
    });

    filtered.forEach(function(row, idx) {
      if (row.cells[0]) row.cells[0].textContent = idx + 1;
      if (idx >= start && idx < end) row.style.display = '';
    });

    if (infoEl) {
      if (total === 0) {
        infoEl.innerHTML = '<span class="text-muted">No matching records found</span>';
      } else {
        infoEl.innerHTML = 'Showing <span class="fw-bold text-dark">' + (start + 1) + '</span> to <span class="fw-bold text-dark">' + Math.min(end, total) + '</span> of <span class="fw-bold text-dark">' + total + '</span> entries';
      }
    }

    renderPagination(totalPages);
  }

  if (searchInput) {
    searchInput.addEventListener('input', function() {
      currentFilter = this.value.trim();
      currentPage = 1;
      render();
    });
  }

  if (pageSizeSelect) {
    pageSizeSelect.addEventListener('change', function() {
      pageSize = parseInt(this.value, 10) || 5;
      currentPage = 1;
      render();
    });
  }

  render();
});
</script>

<script>
// Declared Results & Grades table: client-side pagination (5 / 15 / 25)
document.addEventListener('DOMContentLoaded', function() {
  var table = document.getElementById('schResultsTable');
  if (!table) return;

  var tbody = table.querySelector('tbody');
  if (!tbody) return;

  var allRows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
  if (allRows.length === 0 || allRows.length === 1 && allRows[0].cells.length === 1) return;

  var pageSizeSelect = document.getElementById('resPageSize');
  var paginationUl = document.getElementById('resPagination');
  var infoEl = document.getElementById('resTableInfo');

  var currentPage = 1;
  var pageSize = pageSizeSelect ? (parseInt(pageSizeSelect.value, 10) || 5) : 5;

  function createPagerItem(label, page, disabled, active) {
    var li = document.createElement('li');
    li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
    var a = document.createElement('a');
    a.className = 'page-link rounded-3 px-3 shadow-2xs' + (active ? ' fw-bold text-white' : '');
    a.href = 'javascript:void(0)';
    a.innerHTML = label;
    if (!disabled && !active) {
      a.addEventListener('click', function(e) {
        e.preventDefault();
        currentPage = page;
        render();
      });
    }
    li.appendChild(a);
    return li;
  }

  function renderPagination(totalPages) {
    if (!paginationUl) return;
    paginationUl.innerHTML = '';

    paginationUl.appendChild(createPagerItem('<i class="bi bi-chevron-left"></i>', currentPage - 1, currentPage === 1));

    var maxVisible = 5;
    var startPage = Math.max(1, currentPage - 2);
    var endPage = Math.min(totalPages, startPage + maxVisible - 1);
    if (endPage - startPage < maxVisible - 1) {
      startPage = Math.max(1, endPage - maxVisible + 1);
    }

    if (startPage > 1) {
      paginationUl.appendChild(createPagerItem('1', 1));
      if (startPage > 2) {
        var dots = document.createElement('li');
        dots.className = 'page-item disabled';
        dots.innerHTML = '<span class="page-link border-0">...</span>';
        paginationUl.appendChild(dots);
      }
    }

    for (var p = startPage; p <= endPage; p++) {
      paginationUl.appendChild(createPagerItem(String(p), p, false, p === currentPage));
    }

    if (endPage < totalPages) {
      if (endPage < totalPages - 1) {
        var dots2 = document.createElement('li');
        dots2.className = 'page-item disabled';
        dots2.innerHTML = '<span class="page-link border-0">...</span>';
        paginationUl.appendChild(dots2);
      }
      paginationUl.appendChild(createPagerItem(String(totalPages), totalPages));
    }

    paginationUl.appendChild(createPagerItem('<i class="bi bi-chevron-right"></i>', currentPage + 1, currentPage === totalPages));
  }

  function render() {
    var total = allRows.length;
    var totalPages = Math.max(1, Math.ceil(total / pageSize));
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    var start = (currentPage - 1) * pageSize;
    var end = start + pageSize;

    allRows.forEach(function(row, idx) {
      if (row.cells[0]) row.cells[0].textContent = idx + 1;
      row.style.display = (idx >= start && idx < end) ? '' : 'none';
    });

    if (infoEl) {
      infoEl.innerHTML = 'Showing <span class="fw-bold text-dark">' + (start + 1) + '</span> to <span class="fw-bold text-dark">' + Math.min(end, total) + '</span> of <span class="fw-bold text-dark">' + total + '</span> entries';
    }

    renderPagination(totalPages);
  }

  if (pageSizeSelect) {
    pageSizeSelect.addEventListener('change', function() {
      pageSize = parseInt(this.value, 10) || 5;
      currentPage = 1;
      render();
    });
  }

  render();
});
</script>
