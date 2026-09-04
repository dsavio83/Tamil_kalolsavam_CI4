<!DOCTYPE html>
<html lang="ta">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo esc($view_mode === 'sub_district' ? ($sub_district_name . ' Sub-District') : ($district_name . ' District')); ?> Kalolsavam 2025-26 - பொது முடிவுகள்</title>

    <!-- Bootstrap 5 & FontAwesome 6 & Fonts -->
    <link href="<?php echo base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;800;900&family=Poppins:wght@400;500;600;700;800&family=Mukta+Malar:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="<?php echo base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>

    <style>
        :root {
            --kite-primary: #1e3a8a;
            --kite-accent: #0284c7;
            --kite-gold: #f59e0b;
            --kite-podium-bg: #1e113a;
            --kite-podium-card: rgba(255, 255, 255, 0.08);
            --tamil-font: 'TAU-Pallai', 'Mukta Malar', 'Poppins', sans-serif;
            --heading-font: 'Cinzel', 'Poppins', serif;
        }

        body {
            font-family: var(--tamil-font);
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        /* KITE Style Navbar */
        .kite-navbar {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 10px 0;
        }
        .kite-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ffffff;
            font-weight: 700;
            text-decoration: none;
            font-size: 1.15rem;
        }
        .kite-brand:hover {
            color: #e2e8f0;
        }
        .kite-nav-link {
            color: rgba(255, 255, 255, 0.88) !important;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 6px 14px !important;
            border-radius: 20px;
            transition: all 0.2s ease;
        }
        .kite-nav-link:hover, .kite-nav-link.active {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.15);
        }

        /* Header Banner */
        .portal-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);
            color: white;
            padding: 24px 0 20px;
            border-bottom: 4px solid var(--kite-gold);
            margin-bottom: 25px;
        }
        .mode-badge {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 30px;
            padding: 6px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        /* Trophy Podium Section (Image 2 Style) */
        .podium-section {
            background: radial-gradient(circle at top, #2e1065 0%, #170b2e 100%);
            border-radius: 18px;
            padding: 30px 20px;
            color: white;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }
        .podium-title {
            color: #fbbf24;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-size: 1.25rem;
            text-align: center;
            margin-bottom: 25px;
        }
        .podium-card {
            background: var(--kite-podium-card);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 20px 15px;
            text-align: center;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .podium-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.35);
        }
        .podium-card-gold {
            background: linear-gradient(180deg, rgba(245, 158, 11, 0.15) 0%, rgba(255, 255, 255, 0.08) 100%);
            border: 2px solid #f59e0b;
            transform: scale(1.04);
        }
        .rank-pill {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1rem;
            margin: 0 auto 12px;
        }
        .rank-pill-1 { background: linear-gradient(135deg, #f59e0b, #d97706); color: #000; box-shadow: 0 0 15px rgba(245, 158, 11, 0.6); }
        .rank-pill-2 { background: linear-gradient(135deg, #e2e8f0, #94a3b8); color: #0f172a; }
        .rank-pill-3 { background: linear-gradient(135deg, #d97706, #b45309); color: #fff; }
        .podium-points {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fbbf24;
            letter-spacing: 0.5px;
        }

        /* ==========================================================================
           STUNNING WINNERS PARTICIPANTS CAROUSEL / SLIDESHOW (IMAGE 2 HIGH-END MATCH)
           ========================================================================== */
        .winners-carousel-wrapper {
            background: #ffffff;
            border-radius: 20px;
            padding: 26px 24px;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.06);
            margin-bottom: 35px;
            border: 1px solid #f1f5f9;
            position: relative;
        }
        .carousel-header-title {
            font-family: var(--heading-font);
            font-size: 1.35rem;
            font-weight: 900;
            letter-spacing: 1.5px;
            color: #1e1b4b;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .carousel-nav-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 1.5px solid #e2e8f0;
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #1e293b;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .carousel-nav-btn:hover {
            background: linear-gradient(135deg, #1e3a8a, #0284c7);
            color: #ffffff;
            border-color: #1e3a8a;
            transform: scale(1.08);
            box-shadow: 0 4px 14px rgba(30, 58, 138, 0.3);
        }
        .autoplay-btn {
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 0.8rem;
            font-weight: 700;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            color: #475569;
            transition: all 0.2s ease;
        }
        .autoplay-btn.active {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .winners-carousel-track {
            display: flex;
            gap: 18px;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 15px 4px 20px;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }
        .winners-carousel-track::-webkit-scrollbar {
            height: 6px;
        }
        .winners-carousel-track::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        /* Stunning Participant Winner Card */
        .winner-card {
            flex: 0 0 350px;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 18px;
            padding: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            display: flex;
            gap: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }
        .winner-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 30px rgba(30, 27, 75, 0.12);
            border-color: #818cf8;
        }

        /* Winner Photo Box */
        .winner-photo-box {
            position: relative;
            width: 88px;
            height: 115px;
            flex-shrink: 0;
            border-radius: 12px;
            overflow: hidden;
            background: #f1f5f9;
            border: 1.5px solid #cbd5e1;
        }
        .winner-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .winner-card:hover .winner-photo {
            transform: scale(1.08);
        }
        .photo-location-tag {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(30, 27, 75, 0.92);
            color: #ffffff;
            font-size: 0.58rem;
            font-weight: 800;
            text-align: center;
            padding: 2px 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Winner Details Box */
        .winner-details {
            flex-grow: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .item-code-badge {
            font-size: 0.72rem;
            font-weight: 800;
            background: #f5f3ff;
            color: #5b21b6;
            padding: 3px 9px;
            border-radius: 10px;
            display: inline-block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            border: 1px solid #ddd6fe;
        }
        .winner-name {
            font-size: 1.02rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.3px;
            margin: 4px 0 2px;
            line-height: 1.25;
            text-transform: uppercase;
        }
        .grade-badge-custom {
            font-size: 0.72rem;
            font-weight: 800;
            color: #581c87;
            background: #faf5ff;
            border: 1px solid #e9d5ff;
            padding: 2px 8px;
            border-radius: 8px;
            letter-spacing: 0.5px;
        }
        .rank-badge-custom {
            font-size: 0.72rem;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 8px;
            letter-spacing: 0.3px;
        }
        .rank-badge-1 { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .rank-badge-2 { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
        .rank-badge-3 { background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }

        .winner-school-name {
            font-size: 0.76rem;
            color: #b45309;
            font-weight: 600;
            margin-top: 4px;
            line-height: 1.2;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Section Cards & Tables */
        .section-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 26px 24px;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.06);
            margin-bottom: 30px;
            border: 1px solid #f1f5f9;
        }
        .section-card-title {
            font-weight: 800;
            font-size: 1.18rem;
            color: #0f172a;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .kite-table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        .kite-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 14px;
            border-bottom: 2px solid #e2e8f0;
        }
        .kite-table td {
            padding: 12px 14px;
            font-size: 0.9rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .kite-table tr:hover td {
            background-color: #f8fafc;
        }

        /* Search Box */
        .search-input-group {
            position: relative;
            max-width: 480px;
        }
        .search-input-group input {
            padding-left: 42px;
            border-radius: 30px;
            border: 1.5px solid #cbd5e1;
            font-size: 0.9rem;
            height: 42px;
        }
        .search-input-group i {
            position: absolute;
            left: 15px;
            top: 13px;
            color: #94a3b8;
        }

        /* Custom Tabs */
        .nav-pills-kite .nav-link {
            border-radius: 20px;
            padding: 6px 18px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            background-color: #f1f5f9;
            margin-right: 8px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
        }
        .nav-pills-kite .nav-link.active {
            background-color: var(--kite-primary);
            color: white;
            box-shadow: 0 4px 10px rgba(30, 58, 138, 0.25);
        }

        /* Footer */
        .kite-footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 25px 0;
            font-size: 0.85rem;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- KITE Style Navbar -->
    <nav class="navbar navbar-expand-lg kite-navbar sticky-top">
        <div class="container-fluid px-3 px-md-4">
            <a class="kite-brand" href="<?php echo base_url('login'); ?>">
                <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="Logo" style="height: 36px;">
                <span>தமிழ் கலைவிழா 2025-26</span>
            </a>
            
            <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#kiteNav">
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="kiteNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="kite-nav-link" href="<?php echo base_url('login'); ?>"><i class="fas fa-home me-1"></i> Home (முகப்பு)</a>
                    </li>
                    
                    <!-- Dynamic District Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="kite-nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-landmark me-1"></i> Select District (மாவட்டம்)
                        </a>
                        <ul class="dropdown-menu shadow border-0">
                            <?php if (!empty($all_districts)) : foreach ($all_districts as $d) : ?>
                                <li>
                                    <a class="dropdown-item <?php echo ($district_code == $d['code'] && empty($sub_district_code)) ? 'active fw-bold' : ''; ?>" 
                                       href="<?php echo base_url('publishresult/publicresult/resultview?district=' . $d['code']); ?>">
                                        <?php echo esc($d['tamil']); ?> (<?php echo esc($d['name']); ?>)
                                    </a>
                                </li>
                            <?php endforeach; endif; ?>
                        </ul>
                    </li>

                    <!-- Dynamic Sub-District Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="kite-nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-map-marker-alt me-1"></i> Select Sub-District
                        </a>
                        <ul class="dropdown-menu shadow border-0" style="max-height: 350px; overflow-y: auto;">
                            <?php if (!empty($sub_districts)) : foreach ($sub_districts as $sd) : ?>
                                <li>
                                    <a class="dropdown-item <?php echo ($sub_district_code == $sd['sub_district_code']) ? 'active fw-bold' : ''; ?>" 
                                       href="<?php echo base_url('publishresult/publicresult/resultview?sub_district=' . $sd['sub_district_code']); ?>">
                                        <?php echo esc($sd['sub_district_name']); ?>
                                    </a>
                                </li>
                            <?php endforeach; endif; ?>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="kite-nav-link" href="<?php echo base_url('downloads/serve/chittur_itemcodes.html'); ?>" target="_blank"><i class="fas fa-list-ol me-1"></i> Item Codes</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="<?php echo base_url('login#districts'); ?>" class="btn btn-outline-light btn-sm rounded-pill px-3">
                        <i class="fas fa-arrow-left me-1"></i> Back to Districts
                    </a>
                    <a href="<?php echo base_url('login'); ?>" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold shadow-sm">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Header Banner -->
    <header class="portal-header">
        <div class="container-fluid px-3 px-md-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                <div class="mode-badge">
                    <i class="fas fa-award text-warning"></i>
                    <?php if ($view_mode === 'sub_district') : ?>
                        <span>துணை மாவட்ட முடிவுகள் • <strong><?php echo esc($sub_district_name); ?> Sub-District</strong> (<?php echo esc($district_name); ?> District)</span>
                    <?php else : ?>
                        <span>மாவட்ட முடிவுகள் • <strong><?php echo esc($district_name); ?> District Kalolsavam</strong></span>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-2">
                    <?php if ($view_mode === 'sub_district') : ?>
                        <a href="<?php echo base_url('publishresult/publicresult/resultview?district=' . $district_code); ?>" class="btn btn-outline-light btn-sm rounded-pill px-3">
                            <i class="fas fa-trophy me-1 text-warning"></i> View Full <?php echo esc($district_name); ?> District Results
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo base_url('login#districts'); ?>" class="btn btn-light btn-sm rounded-pill px-3 fw-semibold shadow-sm">
                        <i class="fas fa-home me-1"></i> முகப்பு (Home)
                    </a>
                </div>
            </div>

            <h2 class="fw-bold mb-1 fs-3">
                <?php if ($view_mode === 'sub_district') : ?>
                    <?php echo esc($sub_district_name); ?> துணை மாவட்ட தமிழ் கலைவிழா 2025 - 2026
                <?php else : ?>
                    <?php echo esc($district_name); ?> மாவட்ட பள்ளி தமிழ் கலைவிழா 2025 - 2026
                <?php endif; ?>
            </h2>
            <p class="mb-0 text-white-50 small">Official Public Results Portal • Government of Kerala General Education Department</p>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="container-fluid px-3 px-md-4">

        <!-- 1. TOP 3 PODIUM / LEADING STANDINGS (Image 2 Trophy Style) -->
        <section class="podium-section">
            <div class="podium-title">
                <i class="fas fa-crown text-warning me-2"></i>
                <?php echo esc($podium_title); ?> (முன்னணி நிலவரம்)
            </div>

            <div class="row g-3 justify-content-center align-items-center">
                <!-- 2nd Place (Left) -->
                <div class="col-md-4 col-sm-6 order-2 order-md-1">
                    <?php if (!empty($top_podium[1])) : $p2 = $top_podium[1]; 
                        $p2Title = ($view_mode === 'sub_district') ? ($p2['school_name'] ?? 'School') : ($p2['sub_district_name'] ?? 'Sub-District');
                        $p2Sub = ($view_mode === 'sub_district') ? ('Code: ' . ($p2['school_code'] ?? '')) : ('Code: ' . ($p2['sub_district_code'] ?? ''));
                    ?>
                        <div class="podium-card">
                            <div>
                                <div class="rank-pill rank-pill-2">2</div>
                                <h5 class="fw-bold text-white mb-1"><?php echo esc($p2Title); ?></h5>
                                <small class="text-white-50"><i class="fas fa-id-badge me-1"></i><?php echo esc($p2Sub); ?></small>
                            </div>
                            <div class="mt-3">
                                <div class="podium-points"><?php echo esc($p2['total_points']); ?></div>
                                <small class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.75rem;">POINTS</small>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="podium-card text-center py-4 text-white-50">
                            <i class="fas fa-medal fs-2 mb-2"></i>
                            <div>2nd Place</div>
                            <small>Results in progress</small>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 1st Place (Center - Gold Cup) -->
                <div class="col-md-4 col-sm-12 order-1 order-md-2">
                    <?php if (!empty($top_podium[0])) : $p1 = $top_podium[0]; 
                        $p1Title = ($view_mode === 'sub_district') ? ($p1['school_name'] ?? 'School') : ($p1['sub_district_name'] ?? 'Sub-District');
                        $p1Sub = ($view_mode === 'sub_district') ? ('Code: ' . ($p1['school_code'] ?? '')) : ('Code: ' . ($p1['sub_district_code'] ?? ''));
                    ?>
                        <div class="podium-card podium-card-gold">
                            <div class="text-center mb-2">
                                <i class="fas fa-trophy text-warning" style="font-size: 3.5rem; filter: drop-shadow(0 0 12px rgba(245, 158, 11, 0.8));"></i>
                            </div>
                            <div>
                                <div class="rank-pill rank-pill-1">1</div>
                                <h4 class="fw-bold text-white mb-1"><?php echo esc($p1Title); ?></h4>
                                <small class="text-warning fw-semibold"><i class="fas fa-id-badge me-1"></i><?php echo esc($p1Sub); ?></small>
                            </div>
                            <div class="mt-3 border-top border-warning border-opacity-25 pt-2">
                                <div class="podium-points text-warning fs-2"><?php echo esc($p1['total_points']); ?></div>
                                <small class="text-warning text-uppercase fw-bold" style="letter-spacing: 1px;">CHAMPION POINTS</small>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="podium-card podium-card-gold text-center py-4">
                            <i class="fas fa-trophy text-warning fs-1 mb-2"></i>
                            <h5 class="text-white">Champion 1st Place</h5>
                            <small class="text-white-50">Awaiting declared results</small>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 3rd Place (Right) -->
                <div class="col-md-4 col-sm-6 order-3 order-md-3">
                    <?php if (!empty($top_podium[2])) : $p3 = $top_podium[2]; 
                        $p3Title = ($view_mode === 'sub_district') ? ($p3['school_name'] ?? 'School') : ($p3['sub_district_name'] ?? 'Sub-District');
                        $p3Sub = ($view_mode === 'sub_district') ? ('Code: ' . ($p3['school_code'] ?? '')) : ('Code: ' . ($p3['sub_district_code'] ?? ''));
                    ?>
                        <div class="podium-card">
                            <div>
                                <div class="rank-pill rank-pill-3">3</div>
                                <h5 class="fw-bold text-white mb-1"><?php echo esc($p3Title); ?></h5>
                                <small class="text-white-50"><i class="fas fa-id-badge me-1"></i><?php echo esc($p3Sub); ?></small>
                            </div>
                            <div class="mt-3">
                                <div class="podium-points"><?php echo esc($p3['total_points']); ?></div>
                                <small class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.75rem;">POINTS</small>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="podium-card text-center py-4 text-white-50">
                            <i class="fas fa-medal fs-2 mb-2"></i>
                            <div>3rd Place</div>
                            <small>Results in progress</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- ==========================================================================
             2. LATEST RESULTS - PARTICIPANTS WINNERS SLIDESHOW (IMAGE 2 STUNNING DESIGN)
             ========================================================================== -->
        <section class="winners-carousel-wrapper">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <div class="carousel-header-title">
                        <i class="fas fa-sparkles text-warning"></i>
                        LATEST RESULTS – PARTICIPANTS
                    </div>
                    <small class="text-muted fw-semibold">வெற்றிபெற்ற மாணவ மணிகள் • நேரடி முடிவுகள் (Live Declared Results)</small>
                </div>
                
                <div class="d-flex align-items-center gap-2">
                    <button class="autoplay-btn active" id="autoplayToggle" onclick="toggleAutoplay()">
                        <i class="fas fa-play me-1"></i> Auto Slide
                    </button>
                    <button class="carousel-nav-btn" onclick="scrollWinners(-365)" title="Previous Winner"><i class="fas fa-arrow-left"></i></button>
                    <button class="carousel-nav-btn" onclick="scrollWinners(365)" title="Next Winner"><i class="fas fa-arrow-right"></i></button>
                </div>
            </div>

            <div class="winners-carousel-track" id="winnersTrack" onmouseenter="pauseAutoplay()" onmouseleave="resumeAutoplay()">
                <?php if (!empty($winners)) : foreach ($winners as $w) : 
                    $rankClass = ($w['rank'] == 1) ? 'rank-badge-1' : (($w['rank'] == 2) ? 'rank-badge-2' : 'rank-badge-3');
                    $rankLabel = ($w['rank'] == 1) ? '1st Place 🥇' : (($w['rank'] == 2) ? '2nd Place 🥈' : '3rd Place 🥉');
                    $locationLabel = esc($w['sub_district_name'] ?? $district_name);
                ?>
                    <div class="winner-card" onclick="fetchItemResults(<?php echo $w['item_code']; ?>, '<?php echo esc(addslashes($w['item_name'])); ?>')" title="Click to view full <?php echo esc($w['item_name']); ?> results">
                        <!-- Photo with Location Ribbon -->
                        <div class="winner-photo-box">
                            <img src="<?php echo esc($w['photo_url']); ?>" alt="<?php echo esc($w['participant_name']); ?>" class="winner-photo" onerror="this.src='<?php echo base_url('photos/img_user_nophoto.png'); ?>';">
                            <div class="photo-location-tag"><?php echo $locationLabel; ?></div>
                        </div>

                        <!-- Winner Information -->
                        <div class="winner-details">
                            <div>
                                <span class="item-code-badge mb-1">
                                    <?php echo esc($w['item_code']); ?> • <?php echo esc($w['item_name']); ?>
                                </span>
                                <div class="winner-name text-truncate" title="<?php echo esc($w['participant_name']); ?>">
                                    <?php echo esc($w['participant_name']); ?>
                                </div>
                                <div class="d-flex align-items-center gap-1 mt-1">
                                    <span class="grade-badge-custom">
                                        GRADE <?php echo esc($w['grade']); ?>
                                    </span>
                                    <span class="rank-badge-custom <?php echo $rankClass; ?>">
                                        <?php echo $rankLabel; ?>
                                    </span>
                                </div>
                            </div>

                            <div class="mt-2 pt-1 border-top border-light">
                                <div class="winner-school-name text-truncate" title="<?php echo esc($w['school_name']); ?>">
                                    <i class="fas fa-university"></i>
                                    <span class="text-truncate"><?php echo esc($w['school_name']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <div class="p-5 text-center text-muted w-100 bg-light rounded-4">
                        <i class="fas fa-trophy fs-1 text-warning mb-2 opacity-50"></i>
                        <h6 class="fw-bold text-dark">இன்னும் முடிவுகள் வெளியிடப்படவில்லை</h6>
                        <p class="mb-0 small text-muted">போட்டிகளின் முடிவுகள் அறிவிக்கப்பட்டதும் வெற்றியாளர்களின் புகைப்படங்களுடன் கூடிய ஸ்லைடு ஷோ தானாகவே இங்கு தோன்றும்.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- 3. POINTS BREAKDOWN & STANDINGS (LP, UP, HS, HSS & OVERALL AGGREGATE) -->
        <div class="row g-4 mb-4">
            <!-- If District View: Show Sub-District Standings Matrix -->
            <?php if ($view_mode === 'district' && !empty($subdistrict_points_breakdown)) : ?>
                <div class="col-12">
                    <div class="section-card">
                        <div class="section-card-title">
                            <i class="fas fa-map-marked-alt text-primary"></i>
                            துணை மாவட்டங்கள் பெற்ற புள்ளிகள் (Sub-District Points Matrix)
                        </div>
                        <div class="table-responsive">
                            <table class="kite-table table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Sub-District Name (துணை மாவட்டம்)</th>
                                        <th class="text-center">LP Points</th>
                                        <th class="text-center">UP Points</th>
                                        <th class="text-center">HS Points</th>
                                        <th class="text-center">HSS Points</th>
                                        <th class="text-center">Total Points</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $rank = 1; foreach ($subdistrict_points_breakdown as $sdb) : ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo $rank++; ?></td>
                                            <td class="fw-bold text-primary">
                                                <i class="fas fa-map-pin me-1 text-danger"></i>
                                                <?php echo esc($sdb['sub_district_name']); ?>
                                            </td>
                                            <td class="text-center"><?php echo esc($sdb['lp_points'] ?? 0); ?></td>
                                            <td class="text-center"><?php echo esc($sdb['up_points'] ?? 0); ?></td>
                                            <td class="text-center"><?php echo esc($sdb['hs_points'] ?? 0); ?></td>
                                            <td class="text-center"><?php echo esc($sdb['hss_points'] ?? 0); ?></td>
                                            <td class="text-center fw-bold text-dark fs-6 bg-warning bg-opacity-10">
                                                <?php echo esc($sdb['total_points']); ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?php echo base_url('publishresult/publicresult/resultview?sub_district=' . $sdb['sub_district_code']); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                    View Sub-District <i class="fas fa-arrow-right ms-1"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- School Points Table -->
            <div class="col-12">
                <div class="section-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="section-card-title mb-0">
                            <i class="fas fa-school text-primary"></i>
                            பள்ளிகள் பெற்ற புள்ளிகள் (School Points Breakdown)
                        </div>
                        <div class="search-input-group">
                            <i class="fas fa-search"></i>
                            <input type="text" id="schoolSearchInput" class="form-control" placeholder="Search school name or code...">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="kite-table table table-hover" id="schoolPointsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>School Code & Name</th>
                                    <th>Sub-District</th>
                                    <th class="text-center">LP</th>
                                    <th class="text-center">UP</th>
                                    <th class="text-center">HS</th>
                                    <th class="text-center">HSS</th>
                                    <th class="text-center">Total Points</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($school_points_breakdown)) : $sRank = 1; foreach ($school_points_breakdown as $sp) : ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo $sRank++; ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo esc($sp['school_name']); ?></div>
                                            <small class="text-muted">Code: <?php echo esc($sp['school_code']); ?></small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border"><?php echo esc($sp['sub_district_name'] ?? ''); ?></span></td>
                                        <td class="text-center"><?php echo esc($sp['lp_points'] ?? 0); ?></td>
                                        <td class="text-center"><?php echo esc($sp['up_points'] ?? 0); ?></td>
                                        <td class="text-center"><?php echo esc($sp['hs_points'] ?? 0); ?></td>
                                        <td class="text-center"><?php echo esc($sp['hss_points'] ?? 0); ?></td>
                                        <td class="text-center fw-bold text-primary fs-6 bg-primary bg-opacity-10">
                                            <?php echo esc($sp['total_points']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            பள்ளிகளுக்கான புள்ளிகள் இன்னும் பதிவேற்றப்படவில்லை.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. COMPLETED COMPETITIONS & INSTANT LIVE ITEM SEARCH (முடிந்த போட்டிகள் & ஐட்டம்கோடு சர்ச்) -->
        <section class="section-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <div class="section-card-title mb-1">
                        <i class="fas fa-clipboard-check text-success"></i>
                        முடிந்த போட்டிகளின் முடிவுகள் & ஐட்டம்கோடு வைஸ் சர்ச்
                    </div>
                    <small class="text-muted">போட்டியின் குறியீட்டு எண் (Item Code) அல்லது பெயரைக் கொண்டு உடனடியாக முடிவுகளைத் தேடிப் பார்க்கலாம்.</small>
                </div>
                <div class="search-input-group">
                    <i class="fas fa-search"></i>
                    <input type="text" id="itemSearchInput" class="form-control" placeholder="🔍 Search Item Code or Competition Name...">
                </div>
            </div>

            <!-- Category Filter Tabs -->
            <ul class="nav nav-pills nav-pills-kite mb-3" id="itemCatTabs">
                <li class="nav-item">
                    <button class="nav-link active" onclick="filterItemsByCategory('all', this)">All Categories (அனைத்தும்)</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" onclick="filterItemsByCategory('LP', this)">LP General</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" onclick="filterItemsByCategory('UP', this)">UP General</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" onclick="filterItemsByCategory('HS', this)">HS General</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" onclick="filterItemsByCategory('HSS', this)">HSS General</button>
                </li>
            </ul>

            <div class="table-responsive">
                <table class="kite-table table table-hover" id="itemsTable">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Competition Name (போட்டி இனம்)</th>
                            <th>Category (பிரிவு)</th>
                            <th class="text-center">Results Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($completed_items)) : foreach ($completed_items as $item) : 
                            $hasResult = !empty($item['has_results']);
                            $catShort = (strpos($item['fest_name'], 'LP') !== false) ? 'LP' : 
                                        ((strpos($item['fest_name'], 'UP') !== false) ? 'UP' : 
                                        ((strpos($item['fest_name'], 'HSS') !== false) ? 'HSS' : 'HS'));
                        ?>
                            <tr data-category="<?php echo $catShort; ?>" data-item-code="<?php echo esc($item['item_code']); ?>" data-item-name="<?php echo strtolower(esc($item['item_name'])); ?>">
                                <td class="fw-bold text-primary">
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1">
                                        <?php echo esc($item['item_code']); ?>
                                    </span>
                                </td>
                                <td class="fw-bold text-dark">
                                    <?php echo esc($item['item_name']); ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                        <?php echo esc($item['fest_name']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($hasResult) : ?>
                                        <span class="badge bg-success rounded-pill px-3 py-1">
                                            <i class="fas fa-check-circle me-1"></i> Declared (வெளியிடப்பட்டது)
                                        </span>
                                    <?php else : ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1">
                                            <i class="fas fa-clock me-1"></i> Processing (முடிவுகள் வரவில்லை)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($hasResult) : ?>
                                        <button class="btn btn-sm btn-success rounded-pill px-3 fw-semibold shadow-sm" onclick="fetchItemResults(<?php echo $item['item_code']; ?>, '<?php echo esc(addslashes($item['item_name'])); ?>')">
                                            <i class="fas fa-eye me-1"></i> View Result (ரிசல்ட் பார்க்க)
                                        </button>
                                    <?php else : ?>
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" disabled>
                                            Not Declared
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">போட்டி விவரங்கள் கிடைக்கவில்லை.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <!-- Modal for Detailed Item Results -->
    <div class="modal fade" id="itemResultModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold" id="itemResultModalTitle">
                        <i class="fas fa-award me-2 text-warning"></i>Competition Results
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="itemResultModalBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">Loading results...</p>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="kite-footer">
        <div class="container-fluid px-3 px-md-4">
            <h6 class="text-white mb-2">கேரளா மாநில பள்ளி தமிழ் கலைவிழா 2025 - 2026</h6>
            <p class="mb-0 text-white-50">Official Public Results Portal • Powered by CodeIgniter 4 & KITE Kalolsavam Engine</p>
        </div>
    </footer>

    <script>
        // Slideshow Engine Variables
        let autoSlideInterval = null;
        let isAutoplayActive = true;
        const scrollStep = 365;

        function scrollWinners(distance) {
            const track = document.getElementById('winnersTrack');
            if (track) {
                const maxScrollLeft = track.scrollWidth - track.clientWidth;
                if (distance > 0 && track.scrollLeft >= maxScrollLeft - 10) {
                    // Loop back to start smoothly
                    track.scrollTo({ left: 0, behavior: 'smooth' });
                } else if (distance < 0 && track.scrollLeft <= 10) {
                    // Loop to end
                    track.scrollTo({ left: maxScrollLeft, behavior: 'smooth' });
                } else {
                    track.scrollBy({ left: distance, behavior: 'smooth' });
                }
            }
        }

        function startAutoplay() {
            if (autoSlideInterval) clearInterval(autoSlideInterval);
            autoSlideInterval = setInterval(() => {
                if (isAutoplayActive) {
                    scrollWinners(scrollStep);
                }
            }, 3500);
        }

        function pauseAutoplay() {
            isAutoplayActive = false;
        }

        function resumeAutoplay() {
            const btn = document.getElementById('autoplayToggle');
            if (btn && btn.classList.contains('active')) {
                isAutoplayActive = true;
            }
        }

        function toggleAutoplay() {
            const btn = document.getElementById('autoplayToggle');
            if (btn.classList.contains('active')) {
                btn.classList.remove('active');
                btn.innerHTML = '<i class="fas fa-pause me-1"></i> Paused';
                isAutoplayActive = false;
            } else {
                btn.classList.add('active');
                btn.innerHTML = '<i class="fas fa-play me-1"></i> Auto Slide';
                isAutoplayActive = true;
            }
        }

        // Start slideshow on load
        document.addEventListener('DOMContentLoaded', function() {
            startAutoplay();
        });

        // Live School Search
        document.getElementById('schoolSearchInput')?.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#schoolPointsTable tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });

        // Live Item Search
        document.getElementById('itemSearchInput')?.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#itemsTable tbody tr');
            rows.forEach(row => {
                const code = row.getAttribute('data-item-code') || '';
                const name = row.getAttribute('data-item-name') || '';
                const matches = code.includes(term) || name.includes(term);
                row.style.display = matches ? '' : 'none';
            });
        });

        // Filter Items by Category
        function filterItemsByCategory(cat, btn) {
            document.querySelectorAll('#itemCatTabs .nav-link').forEach(l => l.classList.remove('active'));
            if (btn) btn.classList.add('active');

            const rows = document.querySelectorAll('#itemsTable tbody tr');
            rows.forEach(row => {
                const rowCat = row.getAttribute('data-category');
                if (cat === 'all' || rowCat === cat) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // AJAX Fetch Item Results Modal
        function fetchItemResults(itemCode, itemName) {
            const modalTitle = document.getElementById('itemResultModalTitle');
            const modalBody = document.getElementById('itemResultModalBody');
            const modalEl = new bootstrap.Modal(document.getElementById('itemResultModal'));

            modalTitle.innerHTML = `<i class="fas fa-award me-2 text-warning"></i>${itemCode} - ${itemName} Results`;
            modalBody.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">முடிவுகள் ஏற்றப்படுகின்றன (Loading Results)...</p>
                </div>
            `;
            modalEl.show();

            const distCode = '<?php echo $district_code; ?>';
            const subDistCode = '<?php echo $sub_district_code; ?>';

            fetch('<?php echo base_url("publishresult/publicresult/get_item_results_ajax"); ?>', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?php echo csrf_hash(); ?>'
                },
                body: `item_code=${itemCode}&district=${distCode}&sub_district=${subDistCode}&<?php echo csrf_token(); ?>=<?php echo csrf_hash(); ?>`
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.results && data.results.length > 0) {
                    let html = `
                        <div class="table-responsive">
                            <table class="kite-table table table-hover">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Photo</th>
                                        <th>Participant Name</th>
                                        <th>School Name</th>
                                        <th class="text-center">Grade</th>
                                        <th class="text-center">Marks</th>
                                        <th class="text-center">Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    data.results.forEach(r => {
                        let rankDisplay = (r.rank == 1) ? '<span class="badge bg-warning text-dark fw-bold">1st 🥇</span>' : 
                                          ((r.rank == 2) ? '<span class="badge bg-secondary text-white fw-bold">2nd 🥈</span>' : 
                                          ((r.rank == 3) ? '<span class="badge bg-dark text-white fw-bold">3rd 🥉</span>' : r.rank));
                        html += `
                            <tr>
                                <td class="fw-bold">${rankDisplay}</td>
                                <td>
                                    <img src="${r.photo_url}" style="width: 42px; height: 50px; object-fit: cover; border-radius: 6px;" onerror="this.src='<?php echo base_url('photos/img_user_nophoto.png'); ?>';">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">${r.participant_name}</div>
                                    <small class="text-muted">Admn: ${r.admn_no} | Class: ${r.class || '-'}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">${r.school_name}</div>
                                    <small class="text-muted">${r.sub_district_name || ''}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                        Grade ${r.grade || '-'}
                                    </span>
                                </td>
                                <td class="text-center fw-bold">${r.total_mark || '-'}</td>
                                <td class="text-center fw-bold text-primary">${r.point || 0}</td>
                            </tr>
                        `;
                    });
                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    modalBody.innerHTML = html;
                } else {
                    modalBody.innerHTML = `
                        <div class="alert alert-info text-center my-3">
                            <i class="fas fa-info-circle me-1"></i> இந்தப் போட்டிக்கான முடிவுகள் இன்னும் வெளியிடப்படவில்லை.
                        </div>
                    `;
                }
            })
            .catch(err => {
                modalBody.innerHTML = `
                    <div class="alert alert-danger text-center my-3">
                        <i class="fas fa-exclamation-triangle me-1"></i> முடிவுகளைப் பெறுவதில் பிழை ஏற்பட்டது. தயவுசெய்து மீண்டும் முயற்சிக்கவும்.
                    </div>
                `;
            });
        }
    </script>
</body>
</html>
