<?php
if (!function_exists('renderPublicGradeBadge')) {
    function renderPublicGradeBadge($grade) {
        $g = strtoupper(trim((string)$grade));
        if ($g === 'A') {
            return '<span class="badge bg-success text-white fw-bold px-2 py-1 shadow-sm" style="font-size:0.78rem;"><i class="fas fa-award me-1"></i>Grade A 🌟</span>';
        } elseif ($g === 'B') {
            return '<span class="badge bg-primary text-white fw-bold px-2 py-1 shadow-sm" style="font-size:0.78rem;"><i class="fas fa-star me-1"></i>Grade B ⭐</span>';
        } elseif ($g === 'C') {
            return '<span class="badge text-white fw-bold px-2 py-1 shadow-sm" style="background-color:#8b5cf6; font-size:0.78rem;"><i class="fas fa-certificate me-1"></i>Grade C</span>';
        } else {
            return '<span class="text-muted fw-bold">-</span>';
        }
    }
}

/*
 * Backwards-compatibility defaults.
 * The controller payload was refactored after this view was written; keep the
 * older view renderable by supplying the fields it expects but the newer
 * payload no longer provides.
 */
if (!isset($all_school_points) || !is_array($all_school_points)) {
    $all_school_points = [];
}
if (!isset($all_subdistrict_points) || !is_array($all_subdistrict_points)) {
    $all_subdistrict_points = [];
}
if (isset($district_highlights) && is_array($district_highlights)) {
    foreach ($district_highlights as $pdIndex => $pdRow) {
        if (!array_key_exists('declared_count', $pdRow)) {
            $district_highlights[$pdIndex]['declared_count'] = 0;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php 
        if ($view_mode === 'sub_district' && !empty($sub_district_name)) {
            echo esc($sub_district_name . ' Sub District - Tamil Kalolsavam 2026-27');
        } elseif ($view_mode === 'district' && !empty($district_name)) {
            echo esc($district_name . ' District - தமிழ் கலைவிழா 2026-27');
        } else {
            echo 'தமிழ் கலைவிழா 2026-27';
        }
    ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo base_url('assets/img/favicon.png'); ?>">
    <link rel="shortcut icon" type="image/png" href="<?php echo base_url('assets/img/favicon.png'); ?>">
    <link rel="apple-touch-icon" href="<?php echo base_url('assets/img/favicon.png'); ?>">

    <!-- Bootstrap 5 CSS & FontAwesome 6 & Modern Google Fonts -->
    <link href="<?php echo base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="<?php echo base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>

    <style>
        @font-face {
            font-family: 'TAU-Maduram';
            src: url('<?php echo base_url('assets/fonts/TAU-Marutham Bold.ttf'); ?>') format('truetype');
            font-weight: bold;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'TAU-Maduram';
            src: url('<?php echo base_url('assets/fonts/TAU-Marutham.ttf'); ?>') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'TAU-Marutham';
            src: url('<?php echo base_url('assets/fonts/TAU-Marutham Bold.ttf'); ?>') format('truetype');
            font-weight: bold;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'TAU-Marutham';
            src: url('<?php echo base_url('assets/fonts/TAU-Marutham.ttf'); ?>') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'TAU-Marutham Bold';
            src: url('<?php echo base_url('assets/fonts/TAU-Marutham Bold.ttf'); ?>') format('truetype');
            font-weight: bold;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'TAU-Paalai';
            src: url('<?php echo base_url('assets/fonts/TAU-Paalai.ttf'); ?>') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'TAU-Pallai';
            src: url('<?php echo base_url('assets/fonts/TAU-Paalai.ttf'); ?>') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        .tau-maduram, .font-tau-maduram {
            font-family: 'TAU-Maduram', 'TAU-Marutham Bold', 'TAU-Marutham', sans-serif !important;
        }

        :root {
            --bg-body: #f8fafc;
            --bg-surface: #ffffff;
            --bg-card: #ffffff;
            --bg-card-hover: #f1f5f9;
            --border-subtle: #e2e8f0;
            --border-accent: #cbd5e1;
            --text-main: #0f172a;
            --text-secondary: #334155;
            --text-muted: #64748b;
            --primary-indigo: #4f46e5;
            --primary-blue: #0284c7;
            --accent-gold: #f59e0b;
            --accent-emerald: #10b981;
            --accent-purple: #8b5cf6;
            --accent-rose: #f43f5e;
            --font-display: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-body: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--bg-body);
            color: var(--text-main);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 10% 10%, rgba(79, 70, 229, 0.04) 0%, transparent 40%),
                radial-gradient(circle at 90% 15%, rgba(2, 132, 199, 0.04) 0%, transparent 45%),
                radial-gradient(circle at 50% 85%, rgba(245, 158, 11, 0.03) 0%, transparent 50%);
            background-attachment: fixed;
        }

        /* ==========================================================================
           1. MODERN GLASS LIGHT NAVBAR
           ========================================================================== */
        .glass-nav {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-subtle);
            padding: 12px 0;
            z-index: 1050;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04);
        }
        .portal-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-main);
            text-decoration: none;
        }
        .portal-brand:hover {
            color: var(--primary-indigo);
        }
        .brand-logo-img {
            height: 44px;
            width: auto;
            max-width: 52px;
            object-fit: contain;
            display: block;
            transition: transform 0.2s ease;
        }
        .portal-brand:hover .brand-logo-img {
            transform: scale(1.05);
        }
        .brand-badge {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #4f46e5 0%, #0284c7 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }
        .brand-title {
            font-family: var(--font-display);
            font-weight: 900;
            font-size: 1.3rem;
            letter-spacing: -0.2px;
            color: var(--text-main);
            margin: 0;
            line-height: 1.1;
        }
        .brand-subtitle {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin: 0;
        }

        .nav-pill-link {
            color: var(--text-secondary) !important;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 8px 18px !important;
            border-radius: 30px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            text-decoration: none;
        }
        .nav-pill-link:hover, .nav-pill-link.active {
            color: var(--primary-indigo) !important;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.08);
        }

        .btn-glow-login {
            background: linear-gradient(135deg, #4f46e5 0%, #0284c7 100%);
            color: #ffffff !important;
            font-weight: 700;
            font-size: 0.88rem;
            padding: 9px 24px;
            border-radius: 30px;
            border: none;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
            transition: all 0.25s ease;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .btn-glow-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.45);
            color: #ffffff !important;
        }

        /* ==========================================================================
           2. HERO EVENT BANNER
           ========================================================================== */
        .hero-banner {
            position: relative;
            padding: 24px 0 20px;
            margin-bottom: 18px;
            border-bottom: 1px solid var(--border-subtle);
            background: linear-gradient(180deg, #eff6ff 0%, #f8fafc 100%);
        }
        .hero-banner-home {
            background: linear-gradient(135deg, #f5f3ff 0%, #eff6ff 50%, #ecfdf5 100%);
            border-bottom: 1px solid #e2e8f0;
            padding: 28px 0 24px;
        }
        .championship-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #e0e7ff;
            border: 1px solid #c7d2fe;
            color: #4338ca;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 6px 16px;
            border-radius: 30px;
            margin-bottom: 12px;
        }
        .championship-tag-fest {
            background: linear-gradient(135deg, #fef3c7 0%, #fee2e2 100%);
            border: 1px solid #fcd34d;
            color: #92400e;
            font-family: 'TAU-Maduram', 'TAU-Marutham Bold', 'TAU-Marutham', sans-serif;
            font-size: 0.88rem;
        }
        .hero-title {
            font-family: var(--font-display);
            font-weight: 900;
            font-size: clamp(1.8rem, 3.8vw, 2.6rem);
            letter-spacing: -0.5px;
            color: var(--text-main);
            margin-bottom: 6px;
        }
        .hero-banner-home .hero-title {
            font-family: 'TAU-Maduram', 'TAU-Marutham Bold', 'TAU-Marutham', var(--font-display);
            font-weight: 900;
            letter-spacing: 0.5px;
        }
        .hero-subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin: 0;
        }

        /* ==========================================================================
           3. HOME VIEW: COLORFUL NEWS & SHOWCASE CARDS
           ========================================================================== */
        .ticker-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            margin-bottom: 18px;
        }
        .ticker-badge {
            background: linear-gradient(135deg, #ef4444 0%, #f97316 100%);
            color: white;
            font-size: 0.76rem;
            font-weight: 800;
            padding: 5px 12px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        /* ==========================================================================
           CONCISE HOME PAGE & KALAIVIZHA ARTS SHOWCASE
           ========================================================================== */
        .home-fest-banner-card {
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.04);
            margin-bottom: 16px;
            position: relative;
        }
        .fest-hero-img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            object-position: center 25%;
            display: block;
        }
        @media (max-width: 768px) {
            .fest-hero-img {
                height: 190px;
            }
        }
        .fest-banner-overlay {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.15) 0%, rgba(15, 23, 42, 0.88) 100%);
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 22px 26px 18px;
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 14px;
        }
        .fest-banner-title {
            font-family: var(--font-display);
            font-size: 1.55rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 4px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.6);
        }
        .fest-banner-sub {
            font-size: 0.88rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 0;
            font-weight: 500;
            text-shadow: 0 1px 4px rgba(0,0,0,0.6);
        }

        /* Compact District Cards */
        .home-district-card {
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
            transition: all 0.25s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .home-district-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07);
            border-color: var(--border-accent);
        }
        .hdc-header {
            padding: 16px 20px;
            color: #ffffff;
            position: relative;
        }
        .hdc-palakkad { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); }
        .hdc-idukki { background: linear-gradient(135deg, #065f46 0%, #10b981 100%); }
        .hdc-wayanad { background: linear-gradient(135deg, #92400e 0%, #f59e0b 100%); }
        
        .hdc-body {
            padding: 16px 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .hdc-stat-badge-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }
        .hdc-stat-pill {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 6px 11px;
            font-size: 0.78rem;
            color: #334155;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Cultural Arts Visual Cards */
        .fest-arts-card {
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .fest-arts-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
            border-color: var(--border-accent);
        }
        .fest-arts-img-wrap {
            position: relative;
            height: 200px;
            overflow: hidden;
            background: #f1f5f9;
        }
        .fest-arts-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .fest-arts-card:hover .fest-arts-img {
            transform: scale(1.05);
        }
        .fest-arts-floating-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .fest-arts-body {
            padding: 16px 18px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .fest-arts-title {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: 1.05rem;
            color: var(--text-main);
            margin-bottom: 4px;
        }
        .fest-arts-desc {
            color: var(--text-muted);
            font-size: 0.82rem;
            margin-bottom: 0;
            line-height: 1.4;
        }

        /* ==========================================================================
           4. DISTRICT RESULT: 3 CHAMPIONSHIP CARDS IN ROW 1
           ========================================================================== */
        .card-champion {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: all 0.25s ease;
            position: relative;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
        }
        .card-champion:hover {
            transform: translateY(-3px);
            border-color: var(--border-accent);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07);
        }
        .card-champion::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }
        .card-subdistricts::before { background: linear-gradient(90deg, #0284c7, #4f46e5); }
        .card-schools::before { background: linear-gradient(90deg, #f59e0b, #ef4444); }
        .card-latest::before { background: linear-gradient(90deg, #10b981, #06b6d4); }

        .card-header-champion {
            padding: 16px 20px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-subtle);
            background: #f8fafc;
        }
        .card-header-title {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: 1.05rem;
            letter-spacing: -0.2px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-main);
        }

        .table-champion {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }
        .table-champion th {
            background: #f1f5f9;
            color: #475569;
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 11px 18px;
            border-bottom: 1px solid var(--border-subtle);
        }
        .table-champion td {
            padding: 12px 18px;
            font-size: 0.88rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: var(--text-secondary);
        }
        .table-champion tr:last-child td {
            border-bottom: none;
        }
        .table-champion tr:hover td {
            background: #f8fafc;
        }

        .badge-points {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
            font-weight: 800;
            font-size: 0.9rem;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
        }
        .badge-points-gold {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
            font-weight: 800;
            font-size: 0.9rem;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
        }

        .card-footer-champion {
            padding: 12px 20px;
            background: #f8fafc;
            border-top: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .btn-view-more {
            color: #0284c7;
            font-weight: 700;
            font-size: 0.82rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            background: transparent;
            border: none;
            padding: 0;
            cursor: pointer;
        }
        .btn-view-more:hover {
            color: #0369a1;
            transform: translateX(3px);
        }

        /* ==========================================================================
           5. FULL VIEW DRILLDOWN CONTAINER
           ========================================================================== */
        .drilldown-view-card {
            background: var(--bg-card);
            border: 1px solid var(--border-accent);
            border-radius: 22px;
            padding: 24px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            display: none;
        }
        .btn-back-pill {
            background: #f1f5f9;
            color: var(--text-main);
            border: 1px solid var(--border-accent);
            font-weight: 700;
            font-size: 0.84rem;
            padding: 7px 20px;
            border-radius: 30px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-back-pill:hover {
            background: var(--primary-indigo);
            border-color: var(--primary-indigo);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.25);
        }

        /* ==========================================================================
           6. DISTRICT RESULT: FESTIVAL CATEGORY RESULTS (ROW 2)
           ========================================================================== */
        .card-categories-table {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 20px;
            overflow: hidden;
            margin-top: 25px;
            margin-bottom: 35px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }
        .card-categories-header {
            background: #f8fafc;
            border-bottom: 1px solid var(--border-subtle);
            padding: 16px 22px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: var(--font-display);
            font-weight: 800;
            font-size: 1.15rem;
            color: var(--text-main);
        }
        .btn-action-category {
            background: #f1f5f9;
            color: var(--text-secondary);
            border: 1px solid var(--border-subtle);
            font-weight: 700;
            font-size: 0.82rem;
            padding: 6px 16px;
            border-radius: 20px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-action-category:hover {
            background: var(--primary-indigo);
            border-color: var(--primary-indigo);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }

        /* ==========================================================================
           7. SUB-DISTRICT RESULT: 5 OVERALL CARDS IN 1 ROW
           ========================================================================== */
        .row-five-columns {
            display: flex;
            flex-wrap: wrap;
            margin-right: -8px;
            margin-left: -8px;
            margin-bottom: 25px;
        }
        .col-five {
            flex: 0 0 100%;
            max-width: 100%;
            padding-right: 8px;
            padding-left: 8px;
            margin-bottom: 16px;
        }
        @media (min-width: 576px) {
            .col-five { flex: 0 0 50%; max-width: 50%; }
        }
        @media (min-width: 992px) {
            .col-five { flex: 0 0 20%; max-width: 20%; }
        }

        .card-overall-rank {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 18px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }
        .card-overall-rank:hover {
            transform: translateY(-3px);
            border-color: var(--border-accent);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        }
        .header-rank-cat {
            padding: 12px 14px;
            font-family: var(--font-display);
            font-weight: 800;
            font-size: 0.88rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            text-align: center;
            border-bottom: 1px solid var(--border-subtle);
        }
        .hdr-overall { background: #fef3c7; color: #b45309; }
        .hdr-lp { background: #e0f2fe; color: #0369a1; }
        .hdr-up { background: #e0e7ff; color: #4338ca; }
        .hdr-hs { background: #f3e8ff; color: #7e22ce; }
        .hdr-hss { background: #ffe4e6; color: #be123c; }

        .rank-badge-podium {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            font-weight: 800;
            flex-shrink: 0;
        }
        .rb-1 { background: #f59e0b; color: #ffffff; box-shadow: 0 2px 6px rgba(245, 158, 11, 0.4); }
        .rb-2 { background: #94a3b8; color: #ffffff; }
        .rb-3 { background: #d97706; color: #ffffff; }
        .rb-other { background: #f1f5f9; color: #64748b; }

        /* ==========================================================================
           8. STATUS WIDGETS
           ========================================================================== */
        .widget-pulse-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 18px;
            padding: 16px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }
        .pulse-beacon {
            width: 10px;
            height: 10px;
            background: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 rgba(16, 185, 129, 0.4);
            animation: pulse-glow 1.8s infinite;
        }
        @keyframes pulse-glow {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* ==========================================================================
           9. WINNER SHOWCASE CAROUSEL
           ========================================================================== */
        .carousel-winner-wrap {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 22px;
            padding: 24px;
            margin-bottom: 30px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        }
        .tab-filter-pill {
            border-radius: 30px;
            padding: 6px 18px;
            font-size: 0.84rem;
            font-weight: 700;
            border: 1px solid var(--border-subtle);
            background: #f8fafc;
            color: var(--text-secondary);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .tab-filter-pill:hover, .tab-filter-pill.active {
            background: var(--primary-indigo);
            color: #ffffff;
            border-color: var(--primary-indigo);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }

        .track-winners-scroll {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 14px 4px 18px;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }
        .track-winners-scroll::-webkit-scrollbar { height: 6px; }
        .track-winners-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        .card-winner-item {
            flex: 0 0 320px;
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: 18px;
            padding: 14px;
            display: flex;
            gap: 12px;
            transition: all 0.25s ease;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }
        .card-winner-item:hover {
            transform: translateY(-4px);
            border-color: #0284c7;
            box-shadow: 0 10px 20px rgba(2, 132, 199, 0.12);
        }

        .winner-frame {
            width: 82px;
            height: 108px;
            flex-shrink: 0;
            border-radius: 12px;
            overflow: hidden;
            background: #f1f5f9;
            border: 1.5px solid var(--border-subtle);
        }
        .winner-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .badge-rank-1 { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-rank-2 { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .badge-rank-3 { background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }

        /* ==========================================================================
           10. QUICK SEARCH BOX
           ========================================================================== */
        .card-search-box {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 22px;
            padding: 24px;
            margin-bottom: 40px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        }
        .search-input-wrapper {
            position: relative;
            max-width: 460px;
            width: 100%;
        }
        .search-input-wrapper input {
            background: #ffffff;
            border: 1.5px solid var(--border-accent);
            border-radius: 30px;
            color: var(--text-main);
            padding-left: 44px;
            height: 46px;
            font-size: 0.9rem;
        }
        .search-input-wrapper input:focus {
            background: #ffffff;
            border-color: var(--primary-indigo);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
            color: var(--text-main);
        }
        .search-input-wrapper i {
            position: absolute;
            left: 16px;
            top: 15px;
            color: var(--text-muted);
        }

        /* ==========================================================================
           11. LIGHT MODAL STYLES & LOGIN FORM
           ========================================================================== */
        .modal-content-light {
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: 22px;
            color: var(--text-main);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.12);
        }
        .modal-content-light .modal-header {
            border-bottom: 1px solid var(--border-subtle);
            background: #f8fafc;
            padding: 18px 24px;
        }
        .modal-content-light .modal-footer {
            border-top: 1px solid var(--border-subtle);
            background: #f8fafc;
            padding: 14px 24px;
        }

        .login-input-group {
            position: relative;
            margin-bottom: 20px;
        }
        .login-input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6366f1;
            font-size: 1.05rem;
            z-index: 5;
        }
        .form-control-portal {
            background: #ffffff !important;
            border: 1.5px solid var(--border-accent) !important;
            border-radius: 12px !important;
            color: var(--text-main) !important;
            padding: 12px 16px 12px 46px !important;
            font-size: 0.95rem !important;
            transition: all 0.2s ease !important;
        }
        .form-control-portal:focus {
            border-color: var(--primary-indigo) !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15) !important;
            color: var(--text-main) !important;
        }
        .form-control-portal::placeholder {
            color: #94a3b8 !important;
        }
        .btn-password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            z-index: 5;
            padding: 4px;
        }
        .btn-password-toggle:hover {
            color: var(--text-main);
        }
        .btn-portal-submit {
            background: linear-gradient(135deg, #4f46e5 0%, #0284c7 100%);
            color: #ffffff !important;
            font-weight: 800;
            font-size: 1rem;
            padding: 13px 24px;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 18px rgba(79, 70, 229, 0.3);
            transition: all 0.25s ease;
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
        }
        .btn-portal-submit:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 22px rgba(79, 70, 229, 0.45);
            color: #ffffff !important;
        }
        .btn-portal-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Footer */
        .site-footer {
            border-top: 1px solid var(--border-subtle);
            background: #ffffff;
            padding: 30px 0;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
        }
    
        /* ==========================================================================
           FESTIVAL (திருவிழா) SPECIAL THEME, ANIMATIONS & MOBILE NATIVE APP STYLING
           ========================================================================== */
        :root {
            --fest-gold: #f59e0b;
            --fest-gold-bright: #fbbf24;
            --fest-red: #dc2626;
            --fest-crimson: #e11d48;
            --fest-orange: #ea580c;
            --fest-saffron: #f97316;
            --fest-teal: #0d9488;
            --fest-peacock: #0284c7;
            --fest-purple: #7c3aed;
            --fest-maroon: #881337;
            --fest-green: #059669;
            --fest-glow: 0 0 25px rgba(245, 158, 11, 0.45);
        }

        /* Tamil Typography Enhancements: User Rule specifies TAU-Pallai */
        body, p, span, div, a, button, input, select, table, th, td {
            font-family: 'TAU-Pallai', 'TAU-Paalai', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            font-size: 14px;
        }

        .tau-maduram, .font-tau-maduram, .festival-tamil-title {
            font-family: 'TAU-Maduram', 'TAU-Marutham Bold', 'TAU-Marutham', sans-serif !important;
        }

        /* Animated Floating Festive Background Orbs & Kolam Patterns */
        .festival-bg-layer {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .fest-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.28;
            animation: orbFloat 14s ease-in-out infinite alternate;
        }

        .fest-orb-1 {
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, #f59e0b 0%, #ea580c 100%);
            top: -100px;
            left: -80px;
            animation-duration: 16s;
        }

        .fest-orb-2 {
            width: 440px;
            height: 440px;
            background: radial-gradient(circle, #ec4899 0%, #7c3aed 100%);
            top: 30%;
            right: -120px;
            animation-duration: 22s;
            animation-delay: -5s;
        }

        .fest-orb-3 {
            width: 340px;
            height: 340px;
            background: radial-gradient(circle, #10b981 0%, #0284c7 100%);
            bottom: -60px;
            left: 20%;
            animation-duration: 18s;
            animation-delay: -9s;
        }

        @keyframes orbFloat {
            0% { transform: translate(0, 0) scale(1) rotate(0deg); }
            50% { transform: translate(45px, 60px) scale(1.12) rotate(45deg); }
            100% { transform: translate(-35px, 35px) scale(0.92) rotate(-30deg); }
        }

        /* Floating Kolam / Rangoli Decorative Background SVGs */
        .floating-kolam {
            position: absolute;
            opacity: 0.12;
            animation: kolamRotate 45s linear infinite;
            pointer-events: none;
        }

        .kolam-1 { top: 12%; left: 4%; width: 140px; height: 140px; animation-duration: 50s; }
        .kolam-2 { top: 48%; right: 5%; width: 180px; height: 180px; animation-duration: 65s; animation-direction: reverse; }
        .kolam-3 { bottom: 14%; left: 8%; width: 120px; height: 120px; animation-duration: 40s; }

        @keyframes kolamRotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Festive Shimmer Title Animation */
        @keyframes shimmerGlow {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .text-festive-shimmer {
            background: linear-gradient(90deg, #ea580c 0%, #f59e0b 25%, #ec4899 50%, #7c3aed 75%, #ea580c 100%);
            background-size: 200% auto;
            color: transparent;
            -webkit-background-clip: text;
            background-clip: text;
            animation: shimmerGlow 4s linear infinite;
            display: inline-block;
        }

        /* Gold Shimmer Button / Badge Glow */
        .btn-fest-gold {
            background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
            color: #ffffff !important;
            font-weight: 800;
            border: none;
            box-shadow: 0 4px 18px rgba(245, 158, 11, 0.4);
            transition: all 0.25s ease;
        }
        .btn-fest-gold:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.6);
            color: #ffffff !important;
        }

        .btn-fest-peacock {
            background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);
            color: #ffffff !important;
            font-weight: 800;
            border: none;
            box-shadow: 0 4px 18px rgba(2, 132, 199, 0.35);
            transition: all 0.25s ease;
        }
        .btn-fest-peacock:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 8px 25px rgba(2, 132, 199, 0.55);
            color: #ffffff !important;
        }

        .btn-fest-purple {
            background: linear-gradient(135deg, #7c3aed 0%, #db2777 100%);
            color: #ffffff !important;
            font-weight: 800;
            border: none;
            box-shadow: 0 4px 18px rgba(124, 58, 237, 0.35);
            transition: all 0.25s ease;
        }
        .btn-fest-purple:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 8px 25px rgba(124, 58, 237, 0.55);
            color: #ffffff !important;
        }

        /* Marquee Ticker with Festival Colors */
        .fest-ticker-wrap {
            background: linear-gradient(90deg, #fffbeb 0%, #fef3c7 50%, #fff1f2 100%);
            border: 2px solid #fde68a;
            border-radius: 18px;
            padding: 12px 18px;
            box-shadow: 0 4px 16px rgba(245, 158, 11, 0.12);
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
        }
        .fest-ticker-wrap::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: linear-gradient(180deg, #f59e0b 0%, #e11d48 100%);
        }

        /* 3 Grand Action Pods for Quick User Navigation (Children, Students, Teachers, Parents) */
        .action-pod-card {
            background: #ffffff;
            border-radius: 22px;
            border: 2px solid #f1f5f9;
            padding: 24px 20px;
            height: 100%;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.05);
            cursor: pointer;
            text-decoration: none !important;
        }
        .action-pod-card:hover {
            transform: translateY(-6px);
            border-color: #cbd5e1;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.12);
        }
        .action-pod-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            transition: height 0.2s ease;
        }
        .action-pod-subdist::after { background: linear-gradient(90deg, #f59e0b, #ea580c); }
        .action-pod-dist::after { background: linear-gradient(90deg, #0284c7, #4f46e5); }
        .action-pod-items::after { background: linear-gradient(90deg, #10b981, #0d9488); }
        .action-pod-login::after { background: linear-gradient(90deg, #7c3aed, #ec4899); }

        .action-pod-card:hover::after {
            height: 8px;
        }

        .action-pod-icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
            margin-bottom: 16px;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .action-pod-card:hover .action-pod-icon-wrap {
            transform: scale(1.12) rotate(4deg);
        }

        .icon-wrap-gold { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #b45309; }
        .icon-wrap-blue { background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); color: #0284c7; }
        .icon-wrap-green { background: linear-gradient(135deg, #dcfce7 0%, #a7f3d0 100%); color: #059669; }
        .icon-wrap-purple { background: linear-gradient(135deg, #f3e8ff 0%, #ddd6fe 100%); color: #7c3aed; }

        .action-pod-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .action-pod-desc {
            font-size: 0.88rem;
            color: #64748b;
            margin-bottom: 14px;
            line-height: 1.45;
        }

        /* Moving Toran / Festive Flags at top */
        .festive-toran-bar {
            width: 100%;
            height: 14px;
            background: repeating-linear-gradient(
                45deg,
                #f59e0b 0,
                #f59e0b 16px,
                #ea580c 16px,
                #ea580c 32px,
                #059669 32px,
                #059669 48px,
                #0284c7 48px,
                #0284c7 64px,
                #7c3aed 64px,
                #7c3aed 80px,
                #e11d48 80px,
                #e11d48 96px
            );
            background-size: 200% 100%;
            animation: toranScroll 28s linear infinite;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            position: relative;
            z-index: 1051;
        }
        @keyframes toranScroll {
            0% { background-position: 0 0; }
            100% { background-position: 200% 0; }
        }

        /* Mobile Native App Bottom Navigation Bar */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 4px 6px;
            z-index: 1060;
            box-shadow: 0 -4px 20px rgba(15, 23, 42, 0.08);
        }
        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #64748b !important;
            text-decoration: none !important;
            padding: 5px 2px;
            border-radius: 12px;
            transition: background 0.2s ease, color 0.2s ease;
            position: relative;
            flex: 1;
            max-width: 72px;
            cursor: pointer;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
            background: transparent;
        }
        .mobile-nav-item i {
            font-size: 1.22rem;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            text-align: center;
            transition: transform 0.2s ease, color 0.2s ease;
            color: inherit;
        }
        .mobile-nav-item span {
            display: block;
            width: 100%;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: clamp(0.62rem, 2.5vw, 0.72rem);
            line-height: 1.15;
            font-weight: 600;
            letter-spacing: -0.2px;
            color: inherit;
        }
        /* Highlight ONLY the selected menu item - other items remain completely normal */
        .mobile-nav-item.active {
            color: #ea580c !important;
            background: rgba(234, 88, 12, 0.09) !important;
        }
        .mobile-nav-item.active i {
            color: #ea580c !important;
            transform: translateY(-1px);
        }
        .mobile-nav-item.active span {
            font-weight: 800 !important;
            color: #ea580c !important;
        }
        .mobile-nav-item.active::after {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 22px;
            height: 3px;
            background: #ea580c;
            border-radius: 3px;
        }
        /* Inactive items are explicitly normal without active indicator */
        .mobile-nav-item:not(.active) {
            color: #64748b !important;
            background: transparent !important;
        }
        .mobile-nav-item:not(.active)::after {
            display: none !important;
        }

        @media (max-width: 768px) {
            body {
                padding-bottom: 80px; /* Space for bottom nav */
            }
            .hero-title {
                font-size: 1.65rem !important;
            }
            .brand-title {
                font-size: 1.05rem !important;
            }
        }

        @media (min-width: 769px) {
            .mobile-bottom-nav {
                display: none !important;
            }
        }

        /* Item Code Interactive Table & Modal Styling */
        .item-code-badge {
            font-family: 'Outfit', monospace;
            font-weight: 800;
            font-size: 0.95rem;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1.5px solid #bfdbfe;
            border-radius: 10px;
            padding: 4px 10px;
            display: inline-block;
        }

        .fest-category-pill {
            font-weight: 800;
            font-size: 0.76rem;
            padding: 4px 10px;
            border-radius: 30px;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }
        .fcp-lp { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .fcp-up { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
        .fcp-hs { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }
        .fcp-hss { background: #ffe4e6; color: #be123c; border: 1px solid #fecdd3; }

        /* Print Stylesheet for Item Codes Table */
        @media print {
            body * {
                visibility: hidden !important;
            }
            #printableItemCodesArea, #printableItemCodesArea * {
                visibility: visible !important;
            }
            #printableItemCodesArea {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                background: #ffffff !important;
                padding: 15px !important;
            }
            .no-print {
                display: none !important;
            }
            .print-table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            .print-table th, .print-table td {
                border: 1px solid #333 !important;
                padding: 6px 8px !important;
                font-size: 11pt !important;
                color: #000 !important;
            }
            .print-table th {
                background: #f1f5f9 !important;
                font-weight: bold !important;
            }
            @page {
                size: A4 portrait;
                margin: 15mm 10mm;
            }
        }

    </style>
</head>
<body>
    <!-- FESTIVE TORAN STRIP (Moving Pattern Banner) -->
    <div class="festive-toran-bar"></div>

    <!-- FESTIVE BACKGROUND MOVING ORBS & FLOATING KOLAMS -->
    <div class="festival-bg-layer" aria-hidden="true">
        <div class="fest-orb fest-orb-1"></div>
        <div class="fest-orb fest-orb-2"></div>
        <div class="fest-orb fest-orb-3"></div>
        <!-- Decorative SVG Kolams -->
        <svg class="floating-kolam kolam-1" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="42" fill="none" stroke="#f59e0b" stroke-width="1.5" stroke-dasharray="4 3"/>
            <circle cx="50" cy="50" r="28" fill="none" stroke="#ea580c" stroke-width="1"/>
            <polygon points="50,15 62,38 85,50 62,62 50,85 38,62 15,50 38,38" fill="none" stroke="#e11d48" stroke-width="1"/>
            <circle cx="50" cy="50" r="6" fill="#f59e0b"/>
        </svg>
        <svg class="floating-kolam kolam-2" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="44" fill="none" stroke="#0284c7" stroke-width="1.5" stroke-dasharray="3 3"/>
            <polygon points="50,10 78,50 50,90 22,50" fill="none" stroke="#7c3aed" stroke-width="1"/>
            <polygon points="10,50 50,78 90,50 50,22" fill="none" stroke="#10b981" stroke-width="1"/>
            <circle cx="50" cy="50" r="5" fill="#0284c7"/>
        </svg>
        <svg class="floating-kolam kolam-3" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="40" fill="none" stroke="#10b981" stroke-width="1" stroke-dasharray="2 4"/>
            <circle cx="50" cy="50" r="24" fill="none" stroke="#f59e0b" stroke-width="1"/>
            <circle cx="50" cy="50" r="7" fill="#10b981"/>
        </svg>
    </div>


    <!-- 1. TOP MODERN LIGHT GLASS NAVBAR -->
    <nav class="navbar navbar-expand-lg glass-nav sticky-top">
        <div class="container-fluid px-3 px-md-4">
            <a class="portal-brand" href="<?php echo base_url(); ?>">
                <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="Tamil Kalaivizha Logo" class="brand-logo-img">
                <div>
                    <h1 class="brand-title">Tamil Kalaivizha 2026</h1>
                    <div class="brand-subtitle">Official Results Portal</div>
                </div>
            </a>
            
            <button class="navbar-toggler text-dark border-0" type="button" data-bs-toggle="collapse" data-bs-target="#portalNav">
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="portalNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item">
                        <a class="nav-pill-link <?php echo ($view_mode === 'home') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    
                    <!-- Dynamic District Result Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-pill-link dropdown-toggle <?php echo ($view_mode === 'district') ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-landmark"></i> District Result
                        </a>
                        <ul class="dropdown-menu shadow border-0" style="max-height: 380px; overflow-y: auto; border-radius: 14px; border: 1px solid #e2e8f0 !important;">
                            <?php if (!empty($all_districts)) : foreach ($all_districts as $d) : ?>
                                <li>
                                    <a class="dropdown-item py-2 px-3 fw-semibold <?php echo ($district_code == $d['code'] && $view_mode === 'district') ? 'active bg-primary text-white' : ''; ?>" 
                                       href="<?php echo base_url('publishresult/publicresult/resultview?district=' . $d['code']); ?>">
                                        <i class="fas fa-location-dot me-2 text-primary"></i> <?php echo esc($d['name']); ?> District
                                    </a>
                                </li>
                            <?php endforeach; endif; ?>
                        </ul>
                    </li>

                    <!-- Sub-District Selector Modal Trigger -->
                    <li class="nav-item">
                        <a class="nav-pill-link <?php echo ($view_mode === 'sub_district') ? 'active' : ''; ?>" href="#" data-bs-toggle="modal" data-bs-target="#subDistrictSelectorModal">
                            <i class="fas fa-map-pin"></i> Sub-dist Result
                        </a>
                    </li>
                    <!-- Item Codes Directory Navigation Link -->
                    <li class="nav-item">
                        <a class="nav-pill-link" href="#" data-bs-toggle="modal" data-bs-target="#itemCodesModal">
                            <i class="fas fa-file-pdf text-success"></i> Items
                        </a>
                    </li>



                </ul>

                <div class="d-flex align-items-center gap-2">
                    <?php if ($view_mode === 'sub_district' && !empty($sub_district_name)) : ?>
                        <a href="<?php echo base_url('publishresult/publicresult/resultview?district=' . $district_code); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold d-none d-md-inline-flex align-items-center gap-1">
                            <i class="fas fa-arrow-up"></i> <?php echo esc($district_name); ?> District
                        </a>
                    <?php endif; ?>

                    <?php
                    $session = session();
                    $isLoggedIn = !empty($session->get('USERID')) && (int)$session->get('USERID') > 0;
                    $userName = $session->get('USER_NAME') ?? 'User';
                    $userType = (int)($session->get('USER_TYPE') ?? 0);
                    $dashboardUrl = ($userType === 6) ? base_url('stage/manager') : base_url('welcome');
                    ?>

                    <?php if ($isLoggedIn) : ?>
                        <a href="<?php echo $dashboardUrl; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="<?php echo base_url('login/logout'); ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1" title="Logged in as <?php echo esc($userName); ?>">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    <?php else : ?>
                        <button type="button" class="btn-glow-login" data-bs-toggle="modal" data-bs-target="#portalLoginModal">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- 2. HERO EVENT BANNER -->
    <?php if ($view_mode === 'home') : ?>
		</br>
        <!-- <header class="hero-banner hero-banner-home">
            <div class="container-fluid px-3 px-md-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <div class="championship-tag championship-tag-fest d-inline-flex flex-wrap align-items-center gap-1">
                            <span class="d-inline-flex align-items-center gap-1 text-nowrap">
                                <i class="fas fa-sparkles text-warning"></i>
                                <span class="tau-maduram">தமிழ் கலைவிழா</span> 2026-27
                            </span>
                            <span class="d-none d-sm-inline">•</span>
                            <span class="d-block d-sm-inline w-100 w-sm-auto text-secondary fw-bold" style="font-size: 0.82rem;">
                                மாவட்ட & உபமாவட்டப் போட்டிகள்
                            </span>
                        </div>
                        <h2 class="hero-title"><span class="tau-maduram">தமிழ் கலைவிழா</span> 2026-27</h2>
                        <p class="hero-subtitle">Official Live Results & Announcements Portal • Government of Kerala General Education Department</p>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-nowrap" style="white-space: nowrap;">
                        <button class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 fw-bold text-nowrap" data-bs-toggle="modal" data-bs-target="#subDistrictSelectorModal">
                            <i class="fas fa-sliders me-1"></i> Choose Sub-District
                        </button>
                        <button class="btn btn-primary btn-sm rounded-pill px-3 py-2 fw-bold shadow-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#portalLoginModal">
                            <i class="fas fa-lock me-1"></i> Official Portal Login
                        </button>
                    </div>
                </div>
            </div>
        </header> -->
    <?php else : ?>
        <header class="hero-banner">
            <div class="container-fluid px-3 px-md-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <div class="championship-tag">
                            <i class="fas fa-shield-halved"></i>
                            <?php if ($view_mode === 'sub_district') : ?>
                                Sub-District Championship • <?php echo esc($sub_district_name); ?>
                            <?php else : ?>
                                District Championship • <?php echo esc($district_name); ?>
                            <?php endif; ?>
                        </div>
                        <h2 class="hero-title">
                            <?php if ($view_mode === 'sub_district') : ?>
                                <?php echo esc($sub_district_name); ?> Sub-District Results
                            <?php else : ?>
                                <?php echo esc($district_name); ?> District Results
                            <?php endif; ?>
                        </h2>
                        <p class="hero-subtitle">Official Live Public Broadcast • Government of Kerala General Education Department</p>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#subDistrictSelectorModal">
                            <i class="fas fa-sliders me-1 text-primary"></i> Switch District / Sub-District
                        </button>
                    </div>
                </div>
            </div>
        </header>
    <?php endif; ?>

    <!-- MAIN DASHBOARD CONTENT -->
    <main class="container-fluid px-3 px-md-4">

        <?php if ($view_mode === 'home') : ?>
            <!-- ==========================================================================
                 ======================= HOME / LANDING PAGE VIEW =========================
                 ========================================================================== -->

            <!-- 1. LIVE ANNOUNCEMENTS TICKER -->
            <div class="fest-ticker-wrap">
                <div class="badge bg-danger text-white fw-bold px-3 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-2 flex-shrink-0" style="font-size: 0.82rem;">
                    <i class="fas fa-bullhorn fa-beat"></i> நேரலைச் செய்தி
                </div>
                <div class="text-truncate flex-grow-1 fw-bold text-dark" style="font-size: 0.95rem;">
                    <span class="tau-maduram text-festive-shimmer fw-bold" style="font-size: 1.12rem;">தமிழ் கலைவிழா</span> 2026-27 மாவட்ட மற்றும் உபமாவட்டப் போட்டிகளின் அதிகாரப்பூர்வ முடிவுகள் மற்றும் ஐட்டம் விவரங்கள் இங்கே நேரலையாகக் கிடைக்கும்!
                </div>
                <button class="btn btn-sm btn-fest-gold rounded-pill px-3 py-1 fw-bold flex-shrink-0" data-bs-toggle="modal" data-bs-target="#subDistrictSelectorModal">
                    <i class="fas fa-sitemap me-1"></i> உபமாவட்டம் தேர்வு
                </button>
            </div>

            <!-- 2. QUICK USER NAVIGATION: 3 GRAND ACTION PODS (Children, Students, Teachers, Parents Friendly) -->
            <div class="row g-3 g-md-4 mb-4" id="quickActionSection">
                <!-- Action Pod 1: Sub-District Results -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="action-pod-card action-pod-subdist" data-bs-toggle="modal" data-bs-target="#subDistrictSelectorModal">
                        <div>
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="action-pod-icon-wrap icon-wrap-gold">
                                    <i class="fas fa-map-location-dot"></i>
                                </div>
                                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 rounded-pill px-3 py-1 fw-bold small">
                                    சுலப அணுகல்
                                </span>
                            </div>
                            <h4 class="action-pod-title">
                                <span>உபமாவட்ட முடிவுகள்</span>
                                <i class="fas fa-arrow-right text-warning fs-5"></i>
                            </h4>
                            <p class="action-pod-desc">
                                உங்கள் உபமாவட்டத்தை நேரடியாகத் தேர்வு செய்து பள்ளிவாரியான புள்ளிகள், வெற்றியாளர்கள் மற்றும் போட்டி முடிவுகளைக் காண்க.
                            </p>
                        </div>
                        <div class="pt-2 border-top d-flex align-items-center justify-content-between text-warning fw-bold small">
                            <span><i class="fas fa-hand-pointer me-1"></i> கிளிக் செய்து தேர்வு செய்க</span>
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                </div>

                <!-- Action Pod 2: District Results -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="action-pod-card action-pod-dist" onclick="openDistrictDirectModalClean(event)">
                        <div>
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="action-pod-icon-wrap icon-wrap-blue">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 fw-bold small">
                                    முழு தரவரிசை
                                </span>
                            </div>
                            <h4 class="action-pod-title">
                                <span>மாவட்ட முடிவுகள்</span>
                                <i class="fas fa-arrow-right text-primary fs-5"></i>
                            </h4>
                            <p class="action-pod-desc">
                                பாலக்காடு, இடுக்கி, வயநாடு உள்ளிட்ட அனைத்து மாவட்டங்களின் ஒருங்கிணைந்த முடிவுகள் மற்றும் முன்னிலை விவரங்கள்.
                            </p>
                        </div>
                        <div class="pt-2 border-top d-flex align-items-center justify-content-between text-primary fw-bold small">
                            <span><i class="fas fa-landmark me-1"></i> மாவட்ட வாரியாகப் பார்க்க</span>
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                </div>

                <!-- Action Pod 3: Item Codes Directory & PDF Download -->
                <div class="col-lg-4 col-md-12 col-12">
                    <div class="action-pod-card action-pod-items" data-bs-toggle="modal" data-bs-target="#itemCodesModal">
                        <div>
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="action-pod-icon-wrap icon-wrap-green">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 fw-bold small">
                                    டவுன்லோட் & PDF
                                </span>
                            </div>
                            <h4 class="action-pod-title">
                                <span>Item Codes Directory</span>
                                <i class="fas fa-arrow-down-to-bracket text-success fs-5"></i>
                            </h4>
                            <p class="action-pod-desc">
                                LP, UP, HS, HSS கலைவிழா ஐட்டம் எண்கள், போட்டிப் பெயர்கள் மற்றும் விதிமுறைகளைப் பார்க்கவும், PDF ஆக பதிவிறக்கவும்.
                            </p>
                        </div>
                        <div class="pt-2 border-top d-flex align-items-center justify-content-between text-success fw-bold small">
                            <span><i class="fas fa-download me-1"></i> PDF டவுன்லோட் செய்க</span>
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. FESTIVAL GRAND BANNER IMAGE SHOWCASE WITH FESTIVE ACCENTS -->
            <div class="home-fest-banner-card mb-4">
                <img src="<?php echo base_url('assets/img/kalaivizha_banner.jpg'); ?>" 
                     alt="Tamil Kalolsavam Banner" 
                     class="fest-hero-img"
                     onerror="this.src='<?php echo base_url('assets/img/profile-img.png'); ?>';">
                <div class="fest-banner-overlay">
                    <div>
                        <div class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-2 shadow-sm" style="font-size: 0.82rem;">
                            <i class="fas fa-star text-danger me-1"></i> பள்ளி கலைத் திருவிழா • <span class="tau-maduram">தமிழ் கலைவிழா</span> 2026-27
                        </div>
                        <h3 class="fest-banner-title">பள்ளி மாணவர்களின் கலைத்திறன் திருவிழா</h3>
                        <p class="fest-banner-sub">அரசு பொதுக்கல்வித் துறை • மாணவர்களுக்கான அதிகாரப்பூர்வ நேரலை முடிவுகள் தளம்</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-fest-gold btn-sm rounded-pill px-3 py-2 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#subDistrictSelectorModal">
                            <i class="fas fa-map-pin me-1"></i> உபமாவட்டம் தேர்வு
                        </button>
                        <button class="btn btn-fest-peacock btn-sm rounded-pill px-3 py-2 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#itemCodesModal">
                            <i class="fas fa-file-pdf me-1"></i> ஐட்டம் கோடு PDF
                        </button>
                        <button class="btn btn-light btn-sm rounded-pill px-3 py-2 fw-bold shadow-sm text-dark" data-bs-toggle="modal" data-bs-target="#portalLoginModal">
                            <i class="fas fa-lock me-1 text-primary"></i> போர்டல் லாகின்
                        </button>
                    </div>
                </div>
            </div>

            <!-- 4. DISTRICT CARDS WITH VIBRANT FESTIVAL ACCENTS -->
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark" style="font-family: var(--font-display);">
                            <i class="fas fa-landmark text-primary me-2"></i> மாவட்ட வாரியான முடிவுகள் (District Standings)
                        </h4>
                        <small class="text-muted">முடிவுகளைப் பார்வையிட உங்கள் மாவட்டத்தைத் தேர்ந்தெடுக்கவும்</small>
                    </div>
                </div>

                <div class="row g-3 g-md-4">
                    <?php if (!empty($district_highlights)) : foreach ($district_highlights as $dh) : 
                        $cardClass = ($dh['code'] == 9) ? 'hdc-palakkad' : (($dh['code'] == 6) ? 'hdc-idukki' : 'hdc-wayanad');
                    ?>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="home-district-card">
                                <div class="hdc-header <?php echo $cardClass; ?>">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="fw-bold mb-0 text-white"><?php echo esc($dh['name']); ?></h4>
                                            <div class="small text-white text-opacity-90 fw-semibold mt-1"><?php echo esc($dh['tamil_name']); ?></div>
                                        </div>
                                        <div style="font-size: 2.2rem; opacity: 0.88;">
                                            <i class="fas fa-landmark"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="hdc-body">
                                    <div class="hdc-stat-badge-row">
                                        <span class="hdc-stat-pill">
                                            <i class="fas fa-sitemap text-primary"></i> <?php echo esc($dh['sub_districts_count']); ?> உபமாவட்டம்
                                        </span>
                                        <span class="hdc-stat-pill">
                                            <i class="fas fa-school text-warning"></i> <?php echo esc($dh['school_count']); ?> பள்ளிகள்
                                        </span>
                                        <span class="hdc-stat-pill text-success">
                                            <i class="fas fa-trophy text-success"></i> <?php echo esc($dh['declared_count']); ?> முடிவுகள்
                                        </span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="<?php echo base_url('publishresult/publicresult/resultview?district=' . $dh['code']); ?>" class="btn btn-outline-primary rounded-pill flex-grow-1 fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                            <span>முடிவுகள் (Results)</span>
                                            <i class="fas fa-arrow-right"></i>
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- 5. VISUAL ARTS FESTIVAL SHOWCASE (பாரம்பரிய கலைப் பிரிவுகள்) -->
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark" style="font-family: var(--font-display);">
                            <i class="fas fa-palette text-danger me-2"></i> கலைவிழாப் பிரிவுகள் (Festival Arts Showcase)
                        </h4>
                        <small class="text-muted">மாணவர்களின் பாரம்பரியக் கலை மற்றும் இலக்கியப் போட்டிகள்</small>
                    </div>
                </div>

                <div class="row g-3 g-md-4">
                    <!-- Dance Arts Card -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="fest-arts-card">
                            <div class="fest-arts-img-wrap">
                                <img src="<?php echo base_url('assets/img/kalaivizha_dance.jpg'); ?>" 
                                     alt="Classical Dance" 
                                     class="fest-arts-img"
                                     onerror="this.src='<?php echo base_url('assets/img/profile-img.png'); ?>';">
                                <div class="fest-arts-floating-badge">
                                    <i class="fas fa-gem text-warning me-1"></i> LP • UP • HS • HSS
                                </div>
                            </div>
                            <div class="fest-arts-body">
                                <div>
                                    <h5 class="fest-arts-title">செவ்வியல் & கிராமிய நடனம்</h5>
                                    <p class="fest-arts-desc">பரதநாட்டியம், நாடோடி நடனம், மோகினியாட்டம் மற்றும் குழு நடனப் போட்டிகள்.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Music Arts Card -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="fest-arts-card">
                            <div class="fest-arts-img-wrap">
                                <img src="<?php echo base_url('assets/img/kalaivizha_music.jpg'); ?>" 
                                     alt="Classical Music" 
                                     class="fest-arts-img"
                                     onerror="this.src='<?php echo base_url('assets/img/profile-img.png'); ?>';">
                                <div class="fest-arts-floating-badge">
                                    <i class="fas fa-music text-info me-1"></i> சங்கீதம் & இசை
                                </div>
                            </div>
                            <div class="fest-arts-body">
                                <div>
                                    <h5 class="fest-arts-title">இசை & குழுப்பாடல்</h5>
                                    <p class="fest-arts-desc">செவ்வியல் வாய்ப்பாட்டு, வீணை, மிருதங்கம், வயலின் மற்றும் தமிழ் தேசபக்திப் பாடல்கள்.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Literary Arts Card -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="fest-arts-card">
                            <div class="fest-arts-img-wrap">
                                <img src="<?php echo base_url('assets/img/kalaivizha_literary.jpg'); ?>" 
                                     alt="Tamil Literature & Recitation" 
                                     class="fest-arts-img"
                                     onerror="this.src='<?php echo base_url('assets/img/profile-img.png'); ?>';">
                                <div class="fest-arts-floating-badge">
                                    <i class="fas fa-book-open text-warning me-1"></i> தமிழ் இலக்கியம்
                                </div>
                            </div>
                            <div class="fest-arts-body">
                                <div>
                                    <h5 class="fest-arts-title">இலக்கியம் & திருக்குறள்</h5>
                                    <p class="fest-arts-desc">திருக்குறள் ஒப்புவித்தல், பேச்சுப் போட்டி, கவிதை எழுதுதல் மற்றும் கதை சொல்லல்.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($view_mode === 'district') : ?>
            <!-- ==========================================================================
                 ======================= DISTRICT RESULT INTERFACE ========================
                 ========================================================================== -->

            <!-- DRILLDOWN FULL VIEW CONTAINER (Triggered by View More... with Back Button) -->
            <div id="drilldownView" class="drilldown-view-card">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pb-3 mb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark" id="drilldownTitle">Full Rankings</h4>
                        <small class="text-muted" id="drilldownSubtitle">UP Gen. + HS Gen. + HSS Gen.</small>
                    </div>
                    <div>
                        <button type="button" class="btn-back-pill" onclick="closeDrilldown()">
                            <i class="fas fa-arrow-left"></i> Back to Summary
                        </button>
                    </div>
                </div>

                <!-- Table Body populated dynamically via JS -->
                <div class="table-responsive" id="drilldownTableContent"></div>
            </div>

            <!-- ROW 1: 3 DISTRICT CHAMPIONSHIP & PROGRAM CARDS (LEADING SUB-DISTRICTS, LEADING SCHOOLS, LATEST RESULTS) -->
            <div class="row g-3 g-xl-4 mb-4" id="districtRow1Cards">
                <!-- Card 1 (FIRST): Leading Sub-districts (அதிக ஸ்கோர் எடுக்கும் சப்டிஸ்டிக் பட்டியல்) -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card-champion card-subdistricts">
                        <div class="card-header-champion">
                            <div class="card-header-title">
                                <i class="fas fa-medal text-warning"></i> Leading Sub-districts (முன்னிலை உபமாவட்டங்கள்)
                            </div>
                        </div>
                        <div class="table-responsive flex-grow-1">
                            <table class="table-champion">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>Sub-District</th>
                                        <th style="width: 90px;" class="text-center">Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($leading_subdistricts)) : $sk = 1; foreach ($leading_subdistricts as $ls) : ?>
                                        <tr>
                                            <td class="text-center fw-bold text-muted"><?php echo $sk++; ?></td>
                                            <td>
                                                <a href="<?php echo base_url('publishresult/publicresult/resultview?sub_district=' . $ls['sub_district_code']); ?>" class="fw-semibold text-truncate text-dark text-decoration-none d-block" style="max-width: 170px;" title="<?php echo esc($ls['sub_district_name']); ?>">
                                                    <?php echo esc($ls['sub_district_name']); ?>
                                                </a>
                                                <small class="text-muted">Code: <?php echo esc($ls['sub_district_code']); ?></small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge-points-gold"><?php echo esc($ls['total_points']); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr><td colspan="3" class="text-center py-4 text-muted">Awaiting declared results</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer-champion">
                            <button type="button" class="btn-view-more" onclick="showFullSubdistricts()">
                                View More <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                            <small class="text-muted">Top Rankings</small>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Leading Schools of District -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card-champion card-schools">
                        <div class="card-header-champion">
                            <div class="card-header-title">
                                <i class="fas fa-trophy text-warning"></i> Leading Schools (முன்னிலை பள்ளிகள்)
                            </div>
                        </div>
                        <div class="table-responsive flex-grow-1">
                            <table class="table-champion">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>School</th>
                                        <th style="width: 90px;" class="text-center">Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($leading_schools)) : $sk = 1; foreach ($leading_schools as $ls) : ?>
                                        <tr>
                                            <td class="text-center fw-bold text-muted"><?php echo $sk++; ?></td>
                                            <td>
                                                <div class="fw-semibold text-truncate text-dark" style="max-width: 170px;" title="<?php echo esc($ls['school_name']); ?>">
                                                    <?php echo esc($ls['school_name']); ?>
                                                </div>
                                                <small class="text-muted">Code: <?php echo esc($ls['school_code']); ?></small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge-points-gold"><?php echo esc($ls['total_points']); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr><td colspan="3" class="text-center py-4 text-muted">Awaiting declared results</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer-champion">
                            <button type="button" class="btn-view-more" onclick="showFullSchools()">
                                View More <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                            <small class="text-muted">Top Rankings</small>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Latest Results -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card-champion card-latest">
                        <div class="card-header-champion">
                            <div class="card-header-title">
                                <i class="fas fa-clock text-success"></i> Latest Results (சமீபத்திய முடிவுகள்)
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                Live Declared
                            </span>
                        </div>
                        <div class="table-responsive flex-grow-1">
                            <table class="table-champion">
                                <thead>
                                    <tr>
                                        <th style="width: 70px;">Code</th>
                                        <th>Competition Item</th>
                                        <th style="width: 80px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($latest_results)) : foreach (array_slice($latest_results, 0, 5) as $lr) : ?>
                                        <tr>
                                            <td class="fw-bold text-primary">#<?php echo esc($lr['item_code']); ?></td>
                                            <td>
                                                <div class="fw-semibold text-truncate text-dark" style="max-width: 150px;" title="<?php echo esc($lr['item_name']); ?>">
                                                    <?php echo esc($lr['item_name']); ?>
                                                </div>
                                                <small class="text-muted"><?php echo esc($lr['fest_name']); ?></small>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" style="font-size: 0.75rem;" onclick="fetchItemResults(<?php echo $lr['item_code']; ?>, '<?php echo esc(addslashes($lr['item_name'])); ?>')">
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr><td colspan="3" class="text-center py-4 text-muted">Awaiting declarations</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer-champion">
                            <button type="button" class="btn-view-more" data-bs-toggle="modal" data-bs-target="#latestResultsModal">
                                View More <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                            <small class="text-muted">Full Event Log</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QUICK ITEM CODE SEARCH & ALL COMPETITIONS TABLE (DISTRICT - UP, HS, HSS) -->
            <section class="card-search-box mb-4" id="districtQuickSearchBox">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">
                            <i class="fas fa-magnifying-glass me-2 text-primary"></i> Quick Item Code Search (போட்டி முடிவுகள்)
                        </h5>
                        <small class="text-muted">தேர்வு செய்யப்பட்ட <?php echo esc($district_name ?: 'மாவட்ட'); ?> மாவட்டத்தின் UP, HS, HSS போட்டி முடிவுகளை ஐட்டம் எண் அல்லது பெயர் மூலம் உடனடியாகத் தேடலாம்.</small>
                    </div>

                    <div class="search-input-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" id="liveItemSearch" class="form-control" placeholder="Search Item Code or Name..." oninput="applyItemTableFilter()">
                    </div>
                </div>

                <!-- Category Filter Pills (UP, HS, HSS) -->
                <div class="d-flex flex-wrap gap-2 mb-3" id="tableCatPills">
                    <button class="tab-filter-pill active" onclick="filterCompetitionsTableCat('all', this)">All Categories</button>
                    <button class="tab-filter-pill" onclick="filterCompetitionsTableCat('UP', this)">UP Kalaivizha</button>
                    <button class="tab-filter-pill" onclick="filterCompetitionsTableCat('HS', this)">HS Kalaivizha</button>
                    <button class="tab-filter-pill" onclick="filterCompetitionsTableCat('HSS', this)">HSS Kalaivizha</button>
                </div>

                <div class="table-responsive">
                    <table class="table-champion" id="competitionsTable">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Item Code</th>
                                <th>Competition Name</th>
                                <th>Category</th>
                                <th class="text-center" style="width: 150px;">Status</th>
                                <th class="text-center" style="width: 160px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($completed_items)) : foreach ($completed_items as $item) : 
                                $hasResult = !empty($item['has_results']);
                                $catShort = (strpos($item['fest_name'], 'UP') !== false) ? 'UP' : 
                                            ((strpos($item['fest_name'], 'HSS') !== false) ? 'HSS' : 
                                            ((strpos($item['fest_name'], 'LP') !== false) ? 'LP' : 'HS'));
                            ?>
                                <tr data-cat="<?php echo $catShort; ?>" data-code="<?php echo esc($item['item_code']); ?>" data-name="<?php echo strtolower(esc($item['item_name'])); ?>">
                                    <td class="fw-bold">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                            #<?php echo esc($item['item_code']); ?>
                                        </span>
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        <?php echo esc($item['item_name']); ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border">
                                            <?php echo esc($item['fest_name']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($hasResult) : ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                                <i class="fas fa-check-circle me-1"></i> Declared
                                            </span>
                                        <?php else : ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">
                                                <i class="fas fa-clock me-1"></i> In Progress
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($hasResult) : ?>
                                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" onclick="fetchItemResults(<?php echo $item['item_code']; ?>, '<?php echo esc(addslashes($item['item_name'])); ?>')">
                                                <i class="fas fa-eye me-1"></i> View Result
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
                                    <td colspan="5" class="text-center py-4 text-muted">No competition items found for <?php echo esc($district_name ?: 'this district'); ?>.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        <?php elseif ($view_mode === 'sub_district') : ?>
            <!-- ==========================================================================
                 ===================== SUB-DISTRICT RESULT INTERFACE ======================
                 ========================================================================== -->

            <!-- ROW 1: 5 OVERALL CARDS IN 1 SINGLE ROW (TOP 5 RANKS) -->
            <div class="row-five-columns">
                <!-- 1. Overall Aggregate Card -->
                <div class="col-five">
                    <div class="card-overall-rank">
                        <div class="header-rank-cat hdr-overall">
                            <i class="fas fa-trophy me-1"></i> Overall Aggregate
                        </div>
                        <div class="p-2 table-responsive flex-grow-1">
                            <table class="w-100" style="font-size: 0.8rem;">
                                <tbody>
                                    <?php if (!empty($top5_overall)) : $or = 1; foreach ($top5_overall as $sch) : 
                                        $rcClass = ($or == 1) ? 'rb-1' : (($or == 2) ? 'rb-2' : (($or == 3) ? 'rb-3' : 'rb-other'));
                                    ?>
                                        <tr class="border-bottom">
                                            <td style="width: 26px;" class="py-1"><span class="rank-badge-podium <?php echo $rcClass; ?>"><?php echo $or++; ?></span></td>
                                            <td class="py-1 px-1 text-truncate text-dark" style="max-width: 120px;" title="<?php echo esc($sch['school_name']); ?>">
                                                <?php echo esc($sch['school_name']); ?>
                                                <?php if (!empty($sch['sub_district_name'])): ?>
                                                    <small class="text-muted d-block text-truncate" style="font-size: 0.7rem;"><?php echo esc($sch['sub_district_name']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-1 text-end fw-bold text-primary"><?php echo esc($sch['total_points']); ?></td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr><td class="text-center text-muted py-3">In progress</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 2. LP Overall Card -->
                <div class="col-five">
                    <div class="card-overall-rank">
                        <div class="header-rank-cat hdr-lp">
                            <i class="fas fa-child me-1"></i> LP Overall
                        </div>
                        <div class="p-2 table-responsive flex-grow-1">
                            <table class="w-100" style="font-size: 0.8rem;">
                                <tbody>
                                    <?php if (!empty($top5_lp)) : $lr = 1; foreach ($top5_lp as $sch) : 
                                        $rcClass = ($lr == 1) ? 'rb-1' : (($lr == 2) ? 'rb-2' : (($lr == 3) ? 'rb-3' : 'rb-other'));
                                    ?>
                                        <tr class="border-bottom">
                                            <td style="width: 26px;" class="py-1"><span class="rank-badge-podium <?php echo $rcClass; ?>"><?php echo $lr++; ?></span></td>
                                            <td class="py-1 px-1 text-truncate text-dark" style="max-width: 120px;" title="<?php echo esc($sch['school_name']); ?>">
                                                <?php echo esc($sch['school_name']); ?>
                                            </td>
                                            <td class="py-1 text-end fw-bold text-primary"><?php echo esc($sch['total_points']); ?></td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr><td class="text-center text-muted py-3">In progress</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 3. UP Overall Card -->
                <div class="col-five">
                    <div class="card-overall-rank">
                        <div class="header-rank-cat hdr-up">
                            <i class="fas fa-users me-1"></i> UP Overall
                        </div>
                        <div class="p-2 table-responsive flex-grow-1">
                            <table class="w-100" style="font-size: 0.8rem;">
                                <tbody>
                                    <?php if (!empty($top5_up)) : $ur = 1; foreach ($top5_up as $sch) : 
                                        $rcClass = ($ur == 1) ? 'rb-1' : (($ur == 2) ? 'rb-2' : (($ur == 3) ? 'rb-3' : 'rb-other'));
                                    ?>
                                        <tr class="border-bottom">
                                            <td style="width: 26px;" class="py-1"><span class="rank-badge-podium <?php echo $rcClass; ?>"><?php echo $ur++; ?></span></td>
                                            <td class="py-1 px-1 text-truncate text-dark" style="max-width: 120px;" title="<?php echo esc($sch['school_name']); ?>">
                                                <?php echo esc($sch['school_name']); ?>
                                            </td>
                                            <td class="py-1 text-end fw-bold text-primary"><?php echo esc($sch['total_points']); ?></td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr><td class="text-center text-muted py-3">In progress</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 4. HS Overall Card -->
                <div class="col-five">
                    <div class="card-overall-rank">
                        <div class="header-rank-cat hdr-hs">
                            <i class="fas fa-graduation-cap me-1"></i> HS Overall
                        </div>
                        <div class="p-2 table-responsive flex-grow-1">
                            <table class="w-100" style="font-size: 0.8rem;">
                                <tbody>
                                    <?php if (!empty($top5_hs)) : $hr = 1; foreach ($top5_hs as $sch) : 
                                        $rcClass = ($hr == 1) ? 'rb-1' : (($hr == 2) ? 'rb-2' : (($hr == 3) ? 'rb-3' : 'rb-other'));
                                    ?>
                                        <tr class="border-bottom">
                                            <td style="width: 26px;" class="py-1"><span class="rank-badge-podium <?php echo $rcClass; ?>"><?php echo $hr++; ?></span></td>
                                            <td class="py-1 px-1 text-truncate text-dark" style="max-width: 120px;" title="<?php echo esc($sch['school_name']); ?>">
                                                <?php echo esc($sch['school_name']); ?>
                                            </td>
                                            <td class="py-1 text-end fw-bold text-primary"><?php echo esc($sch['total_points']); ?></td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr><td class="text-center text-muted py-3">In progress</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 5. HSS Overall Card -->
                <div class="col-five">
                    <div class="card-overall-rank">
                        <div class="header-rank-cat hdr-hss">
                            <i class="fas fa-award me-1"></i> HSS Overall
                        </div>
                        <div class="p-2 table-responsive flex-grow-1">
                            <table class="w-100" style="font-size: 0.8rem;">
                                <tbody>
                                    <?php if (!empty($top5_hss)) : $hsr = 1; foreach ($top5_hss as $sch) : 
                                        $rcClass = ($hsr == 1) ? 'rb-1' : (($hsr == 2) ? 'rb-2' : (($hsr == 3) ? 'rb-3' : 'rb-other'));
                                    ?>
                                        <tr class="border-bottom">
                                            <td style="width: 26px;" class="py-1"><span class="rank-badge-podium <?php echo $rcClass; ?>"><?php echo $hsr++; ?></span></td>
                                            <td class="py-1 px-1 text-truncate text-dark" style="max-width: 120px;" title="<?php echo esc($sch['school_name']); ?>">
                                                <?php echo esc($sch['school_name']); ?>
                                            </td>
                                            <td class="py-1 text-end fw-bold text-primary"><?php echo esc($sch['total_points']); ?></td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr><td class="text-center text-muted py-3">In progress</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROW 2: LIVE STATUS & SCHEDULE PULSE -->
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-12">
                    <div class="widget-pulse-card h-100">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="pulse-beacon"></span>
                                <span class="fw-bold text-dark fs-6">Live Status & Declared Feed</span>
                            </div>
                            <small class="text-muted">
                                <?php if (!empty($last_published)) : ?>
                                    Latest Declaration: <strong>Item <?php echo esc($last_published['item_code']); ?> - <?php echo esc($last_published['item_name']); ?></strong> (<?php echo esc($last_published['fest_name']); ?>)
                                <?php else : ?>
                                    Live evaluation underway across venues. Declared results will stream here.
                                <?php endif; ?>
                            </small>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2">
                            <i class="fas fa-tower-broadcast me-1"></i> Live Broadcast
                        </span>
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <div class="widget-pulse-card h-100">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="fas fa-theater-masks text-primary"></i>
                                <span class="fw-bold text-dark fs-6">Stage Schedule Tracker</span>
                            </div>
                            <?php if (!empty($live_stages)) : 
                                $firstStage = $live_stages[0];
                            ?>
                                <small class="text-muted">
                                    <strong><?php echo esc($firstStage['stage_name']); ?>:</strong> Item <?php echo esc($firstStage['item_code']); ?> - <?php echo esc($firstStage['item_name']); ?> (<?php echo esc($firstStage['fest_name']); ?>)
                                </small>
                            <?php else : ?>
                                <small class="text-muted">Stage schedules and live status tracking active.</small>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#stagesModal">
                            <i class="fas fa-calendar-alt me-1"></i> View Stages
                        </button>
                    </div>
                </div>
            </div>

            <!-- AUTO-SCROLLING WINNER SHOWCASE CAROUSEL -->
            <section class="carousel-winner-wrap">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-crown text-warning me-2"></i> Winners Showcase
                        </h5>
                        <small class="text-muted">1st, 2nd, and 3rd Prize Winners with Photos and Ratings</small>
                    </div>

                    <!-- Category Filter Tabs -->
                    <div class="d-flex align-items-center gap-1 flex-wrap" id="winnerFilterTabs">
                        <button class="tab-filter-pill active" onclick="filterWinners('ALL', this)">ALL</button>
                        <button class="tab-filter-pill" onclick="filterWinners('LP', this)">LP</button>
                        <button class="tab-filter-pill" onclick="filterWinners('UP', this)">UP</button>
                        <button class="tab-filter-pill" onclick="filterWinners('HS', this)">HS</button>
                        <button class="tab-filter-pill" onclick="filterWinners('HSS', this)">HSS</button>
                    </div>

                    <!-- Controls -->
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" id="btnPlayPause" onclick="toggleWinnerAutoplay()">
                            <i class="fas fa-pause me-1"></i> Pause
                        </button>
                        <button class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 34px; height: 34px;" onclick="scrollWinnersCarousel(-340)">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 34px; height: 34px;" onclick="scrollWinnersCarousel(340)">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Winners Track -->
                <div class="track-winners-scroll" id="winnersTrack" onmouseenter="pauseWinnerAutoplay()" onmouseleave="resumeWinnerAutoplay()">
                    <?php if (!empty($winners)) : foreach ($winners as $w) : 
                        $wCat = (strpos($w['fest_name'], 'LP') !== false) ? 'LP' : 
                               ((strpos($w['fest_name'], 'UP') !== false) ? 'UP' : 
                               ((strpos($w['fest_name'], 'HSS') !== false) ? 'HSS' : 'HS'));
                        $rClass = ($w['rank'] == 1) ? 'badge-rank-1' : (($w['rank'] == 2) ? 'badge-rank-2' : 'badge-rank-3');
                        $rText = ($w['rank'] == 1) ? '1st Place 🥇' : (($w['rank'] == 2) ? '2nd Place 🥈' : '3rd Place 🥉');
                    ?>
                        <div class="card-winner-item" data-category="<?php echo $wCat; ?>" onclick="fetchItemResults(<?php echo $w['item_code']; ?>, '<?php echo esc(addslashes($w['item_name'])); ?>')" title="Click to view full results">
                            <div class="winner-frame">
                                <img src="<?php echo esc($w['photo_url']); ?>" alt="<?php echo esc($w['participant_name']); ?>" onerror="this.src='<?php echo base_url('photos/img_user_nophoto.png'); ?>';">
                            </div>
                            <div class="d-flex flex-column justify-content-between flex-grow-1 min-w-0">
                                <div>
                                    <span class="badge bg-light text-primary border small mb-1">
                                        <?php echo esc($w['item_code']); ?> • <?php echo esc($w['item_name']); ?>
                                    </span>
                                    <div class="fw-bold text-dark text-truncate" title="<?php echo esc($w['participant_name']); ?>">
                                        <?php echo esc($w['participant_name']); ?>
                                    </div>
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        <span class="badge <?php echo $rClass; ?> fw-bold">
                                            <?php echo $rText; ?>
                                        </span>
                                        <?php echo renderPublicGradeBadge($w['grade'] ?? ''); ?>
                                    </div>
                                </div>
                                <div class="mt-2 pt-1 border-top">
                                    <small class="text-truncate text-muted d-block" title="<?php echo esc($w['school_name']); ?>">
                                        <i class="fas fa-school me-1 text-primary"></i> <?php echo esc($w['school_name']); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <div class="p-4 text-center text-muted w-100">
                            <i class="fas fa-award fs-3 text-warning mb-2"></i>
                            <div>No winners published yet for this sub-district.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- QUICK ITEM CODE SEARCH & ALL COMPETITIONS TABLE -->
            <section class="card-search-box">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">
                            <i class="fas fa-magnifying-glass me-2 text-primary"></i> Quick Item Code Search
                        </h5>
                        <small class="text-muted">Instant live search lookup by Item Code (e.g. 151, 153, 355) or competition name.</small>
                    </div>

                    <div class="search-input-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" id="liveItemSearch" class="form-control" placeholder="Search Item Code or Name...">
                    </div>
                </div>

                <!-- Category Filter Pills -->
                <div class="d-flex flex-wrap gap-2 mb-3" id="tableCatPills">
                    <button class="tab-filter-pill active" onclick="filterCompetitionsTableCat('all', this)">All Categories</button>
                    <button class="tab-filter-pill" onclick="filterCompetitionsTableCat('LP', this)">LP Kalaivizha</button>
                    <button class="tab-filter-pill" onclick="filterCompetitionsTableCat('UP', this)">UP Kalaivizha</button>
                    <button class="tab-filter-pill" onclick="filterCompetitionsTableCat('HS', this)">HS Kalaivizha</button>
                    <button class="tab-filter-pill" onclick="filterCompetitionsTableCat('HSS', this)">HSS Kalaivizha</button>
                </div>

                <div class="table-responsive">
                    <table class="table-champion" id="competitionsTable">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Item Code</th>
                                <th>Competition Name</th>
                                <th>Category</th>
                                <th class="text-center" style="width: 150px;">Status</th>
                                <th class="text-center" style="width: 160px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($completed_items)) : foreach ($completed_items as $item) : 
                                $hasResult = !empty($item['has_results']);
                                $catShort = (strpos($item['fest_name'], 'LP') !== false) ? 'LP' : 
                                            ((strpos($item['fest_name'], 'UP') !== false) ? 'UP' : 
                                            ((strpos($item['fest_name'], 'HSS') !== false) ? 'HSS' : 'HS'));
                            ?>
                                <tr data-cat="<?php echo $catShort; ?>" data-code="<?php echo esc($item['item_code']); ?>" data-name="<?php echo strtolower(esc($item['item_name'])); ?>">
                                    <td class="fw-bold">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                            #<?php echo esc($item['item_code']); ?>
                                        </span>
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        <?php echo esc($item['item_name']); ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border">
                                            <?php echo esc($item['fest_name']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($hasResult) : ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                                <i class="fas fa-check-circle me-1"></i> Declared
                                            </span>
                                        <?php else : ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">
                                                <i class="fas fa-clock me-1"></i> In Progress
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($hasResult) : ?>
                                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" onclick="fetchItemResults(<?php echo $item['item_code']; ?>, '<?php echo esc(addslashes($item['item_name'])); ?>')">
                                                <i class="fas fa-eye me-1"></i> View Result
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
                                    <td colspan="5" class="text-center py-4 text-muted">No competition items found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        <?php endif; ?>

    </main>

    <!-- ==========================================================================
         MODAL: STAGES PROGRAM TIMETABLE
         ========================================================================== -->
    <div class="modal fade" id="stagesModal" tabindex="-1" aria-labelledby="stagesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content modal-content-light shadow">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-dark mb-0" id="stagesModalLabel">
                        <i class="fas fa-theater-masks me-2 text-primary"></i> Live Stage Allotments & Timetable
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table-champion">
                            <thead>
                                <tr>
                                    <th>Stage</th>
                                    <th>Item Code</th>
                                    <th>Competition Item</th>
                                    <th>Category</th>
                                    <th>Participants</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($live_stages)) : foreach ($live_stages as $st) : ?>
                                    <tr>
                                        <td class="fw-bold text-primary"><?php echo esc($st['stage_name']); ?></td>
                                        <td class="fw-bold">#<?php echo esc($st['item_code']); ?></td>
                                        <td class="fw-semibold text-dark"><?php echo esc($st['item_name']); ?></td>
                                        <td><span class="badge bg-light text-secondary border"><?php echo esc($st['fest_name']); ?></span></td>
                                        <td><?php echo esc($st['participant_count'] ?? '-'); ?></td>
                                        <td>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1">
                                                Active
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; else : ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No active stage schedules found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================================================
         MODAL: LATEST RESULTS FULL LIST
         ========================================================================== -->
    <div class="modal fade" id="latestResultsModal" tabindex="-1" aria-labelledby="latestResultsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content modal-content-light shadow">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-dark mb-0" id="latestResultsModalLabel">
                        <i class="fas fa-clock me-2 text-success"></i> All Latest Declared Results
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table-champion">
                            <thead>
                                <tr>
                                    <th style="width: 90px;">Item Code</th>
                                    <th>Competition Item</th>
                                    <th>Category</th>
                                    <th class="text-center" style="width: 140px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($latest_results)) : foreach ($latest_results as $lr) : ?>
                                    <tr>
                                        <td class="fw-bold text-primary">#<?php echo esc($lr['item_code']); ?></td>
                                        <td class="fw-semibold text-dark"><?php echo esc($lr['item_name']); ?></td>
                                        <td><span class="badge bg-light text-secondary border"><?php echo esc($lr['fest_name']); ?></span></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" onclick="fetchItemResults(<?php echo $lr['item_code']; ?>, '<?php echo esc(addslashes($lr['item_name'])); ?>')">
                                                <i class="fas fa-eye me-1"></i> View Result
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No declared results found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================================================
         MODAL: SWITCH SUB-DISTRICT SELECTOR
         ========================================================================== -->
    
    <!-- ==========================================================================
         MODAL: ITEM CODES DIRECTORY & PDF EXPORT (SUBDISTRICT & FESTIVAL-WISE)
         ========================================================================== -->
    <div class="modal fade" id="itemCodesModal" tabindex="-1" aria-labelledby="itemCodesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content modal-content-light shadow-lg" style="border-radius: 20px; border: 1.5px solid #cbd5e1;">
                
                <!-- MODAL HEADER (ENGLISH ONLY, NO TOP PRINT BUTTON) -->
                <div class="modal-header d-flex justify-content-between align-items-center bg-light py-2 px-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="badge bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fas fa-list-ol fs-6"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0 fs-6" id="itemCodesModalLabel">
                                Item Codes Directory
                            </h5>
                            <small class="text-muted" style="font-size: 0.78rem;" id="itemCodesHeaderSub">
                                Select District & Sub-District to view active competition items
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-3">
                    
                    <!-- STEP 1: DISTRICT & SUB-DISTRICT SELECTOR (MANDATORY GATEWAY) -->
                    <div class="card border border-2 mb-3 shadow-none bg-light" style="border-radius: 14px; border-color: #cbd5e1 !important;">
                        <div class="card-body p-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-5 col-12">
                                    <label class="form-label small fw-bold text-secondary mb-1">
                                        <i class="fas fa-landmark text-primary me-1"></i> 1. Select District
                                    </label>
                                    <select id="modalItemCodeDistrict" class="form-select form-select-sm border-2 rounded-3 fw-semibold" onchange="onItemCodeDistrictChange()">
                                        <option value="">-- Choose District --</option>
                                        <?php if (!empty($all_districts)) : foreach ($all_districts as $d) : ?>
                                            <option value="<?php echo $d['code']; ?>" <?php echo ($district_code == $d['code']) ? 'selected' : ''; ?>>
                                                <?php echo esc($d['name']); ?> District
                                            </option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-5 col-12">
                                    <label class="form-label small fw-bold text-secondary mb-1">
                                        <i class="fas fa-map-pin text-danger me-1"></i> 2. Select Sub-District
                                    </label>
                                    <select id="modalItemCodeSubDistrict" class="form-select form-select-sm border-2 rounded-3 fw-semibold" onchange="onItemCodeSubDistrictChange()">
                                        <option value="">-- Choose Sub-District --</option>
                                    </select>
                                </div>

                                <div class="col-md-2 col-12 d-flex align-items-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-3 w-100 fw-bold py-1" onclick="reloadModalItemCodes()" title="Refresh List">
                                        <i class="fas fa-sync-alt me-1"></i> Load
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: CATEGORY TABS & LIVE SEARCH (VISIBLE ONLY WHEN DISTRICT & SUB-DISTRICT SELECTED) -->
                    <div id="itemCodesToolbar" class="d-none">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 p-2 bg-white rounded-3 border">
                            <!-- Category Filter Tabs -->
                            <div class="d-flex align-items-center gap-1 flex-wrap" id="itemCodesCatFilterPills">
                                <button class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold" onclick="filterItemCodesModal('ALL', this)">
                                    All (<span id="modalItemCountAll">0</span>)
                                </button>
                                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-bold" onclick="filterItemCodesModal('LP', this)">
                                    LP (<span id="modalItemCountLP">0</span>)
                                </button>
                                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-bold" onclick="filterItemCodesModal('UP', this)">
                                    UP (<span id="modalItemCountUP">0</span>)
                                </button>
                                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-bold" onclick="filterItemCodesModal('HS', this)">
                                    HS (<span id="modalItemCountHS">0</span>)
                                </button>
                                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-bold" onclick="filterItemCodesModal('HSS', this)">
                                    HSS (<span id="modalItemCountHSS">0</span>)
                                </button>
                            </div>
                            
                            <!-- Search Input -->
                            <div class="position-relative" style="min-width: 220px; max-width: 320px;">
                                <input type="text" id="modalItemCodesSearch" class="form-control form-control-sm rounded-pill ps-4 py-1" placeholder="Search code or name..." onkeyup="searchItemCodesModal()">
                                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted small"></i>
                            </div>
                        </div>
                    </div>

                    <!-- PLACEHOLDER CALLOUT: PROMPT TO SELECT DISTRICT & SUBDISTRICT FIRST -->
                    <div id="itemCodesSelectionPrompt" class="text-center py-5 bg-white rounded-3 border">
                        <div class="mb-3">
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 rounded-circle p-3">
                                <i class="fas fa-map-marked-alt text-warning fs-1"></i>
                            </span>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Please Select District & Sub-District</h5>
                        <p class="text-muted small mb-3" style="max-width: 520px; margin: 0 auto;">
                            Competition items vary by district and sub-district. Please select both above to load the official item codes directory.
                        </p>
                        <div class="d-inline-flex align-items-center gap-2 small text-primary fw-semibold bg-light px-3 py-1 rounded-pill border">
                            <i class="fas fa-arrow-up"></i> Select District & Sub-District from the dropdowns above
                        </div>
                    </div>

                    <!-- SPINNER / LOADER -->
                    <div id="itemCodesLoader" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mt-2 text-muted small fw-semibold">Loading items for selected Sub-District...</div>
                    </div>

                    <!-- TABLE AREA (ENGLISH ONLY HEADERS & VALUES: Single / Group) -->
                    <div class="table-responsive d-none" id="itemCodesTableContainer" style="max-height: 480px; overflow-y: auto;">
                        <table class="table table-hover table-sm align-middle mb-0" id="modalItemCodesTable" style="font-size: 0.88rem;">
                            <thead class="table-light sticky-top" style="border-bottom: 2px solid #cbd5e1;">
                                <tr>
                                    <th style="width: 110px;" class="text-center">Item Code</th>
                                    <th>Item Name</th>
                                </tr>
                            </thead>
                            <tbody id="modalItemCodesTableBody">
                                <!-- Populated dynamically via JS -->
                            </tbody>
                        </table>
                    </div>

                </div>

                <!-- MODAL FOOTER WITH PRINT BUTTON IN PROPER PROPORTION -->
                <div class="modal-footer justify-content-between py-2 px-3 bg-light border-top">
                    <div class="small text-muted" id="itemCodesSelectedLabel">
                        <i class="fas fa-info-circle text-primary me-1"></i> <span id="itemCodesFooterCount">0</span> items loaded
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-success rounded-pill px-4 fw-bold shadow-sm d-inline-flex align-items-center gap-2" onclick="printItemCodesDirectory()">
                            <i class="fas fa-file-pdf"></i>
                            <span>Print / PDF Export</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- HIDDEN CLEAN PRINTABLE AREA FOR FULL ITEM CODES PDF -->
    <div id="printableItemCodesArea" style="display: none;">
        <div style="text-align: center; margin-bottom: 12px; border-bottom: 2px solid #000; padding-bottom: 8px;">
            <h2 style="margin: 0; font-family: 'TAU-Maduram', sans-serif; font-size: 18pt;">தமிழ் கலைவிழா 2026-27</h2>
            <h3 style="margin: 3px 0; font-size: 13pt;" id="printHeadingText">
                Item Codes Directory
            </h3>
            <p style="margin: 0; font-size: 9pt; color: #444;">Government of Kerala &bull; General Education Department</p>
        </div>
        <table class="print-table" id="printItemCodesTable">
            <thead>
                <tr>
                    <th style="width: 60px; text-align: center;">Item #</th>
                    <th>Item Name</th>
                    <th style="width: 90px; text-align: center;">Category</th>
                    <th style="width: 75px; text-align: center;">Type</th>
                    <th style="width: 80px; text-align: center;">Max Time</th>
                    <th style="width: 75px; text-align: center;">Participants</th>
                </tr>
            </thead>
            <tbody id="printItemCodesTableBody">
            </tbody>
        </table>
        <div style="margin-top: 12px; font-size: 8.5pt; text-align: right; color: #555;">
            Printed on: <?php echo date('d-m-Y H:i:s'); ?> &bull; Official Kalaivizha Portal
        </div>
    </div>

</div>
    </div>



    <!-- ==========================================================================
         MODAL: DIRECT DISTRICT RESULTS CHOOSER (NO SUB-DISTRICTS, DIRECT RESULTS)
         ========================================================================== -->
    <div class="modal fade" id="districtDirectSelectorModal" tabindex="-1" aria-labelledby="districtDirectSelectorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-light shadow-lg" style="border-radius: 20px; border: 2px solid #0284c7;">
                <div class="modal-header d-flex justify-content-between align-items-center bg-light py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="badge bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="fas fa-trophy fs-6"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0 fs-6" id="districtDirectSelectorModalLabel">
                                Select District &bull; மாவட்ட முடிவுகள்
                            </h5>
                            <small class="text-muted" style="font-size: 0.78rem;">
                                நேரடியாக மாவட்ட முடிவுகளைக் காண மாவட்டத்தைத் தேர்ந்தெடுக்கவும்
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary mb-2">
                            <i class="fas fa-landmark text-primary me-1"></i> மாவட்டத்தைத் தேர்ந்தெடுக்கவும் (Select District):
                        </label>
                        <div class="d-flex flex-column gap-2" id="districtDirectButtonsList">
                            <?php if (!empty($all_districts)) : foreach ($all_districts as $d) : 
                                $isCurr = ($district_code == $d['code'] && $view_mode === 'district');
                            ?>
                                <a href="<?php echo base_url('publishresult/publicresult/resultview?district=' . $d['code']); ?>" 
                                   class="btn btn-outline-primary py-2 px-3 rounded-3 d-flex justify-content-between align-items-center fw-bold shadow-sm <?php echo $isCurr ? 'active bg-primary text-white' : ''; ?>"
                                   style="text-decoration: none;">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="fas fa-landmark text-primary"></i>
                                        <span><?php echo esc($d['name']); ?> District Results</span>
                                    </span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end py-2 px-4 bg-light border-top">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


<div class="modal fade" id="subDistrictSelectorModal" tabindex="-1" aria-labelledby="subDistrictSelectorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-light shadow">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-dark mb-0" id="subDistrictSelectorModalLabel">
                        <i class="fas fa-map-marked-alt me-2 text-primary"></i> Select District / Sub-District
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Step 1: Select District</label>
                        <select id="modalDistrictSelect" class="form-select form-select-lg border-2 rounded-3 fs-6" onchange="onModalDistrictChange()">
                            <option value="">-- Choose a District --</option>
                            <?php if (!empty($all_districts)) : foreach ($all_districts as $d) : ?>
                                <option value="<?php echo $d['code']; ?>" <?php echo ($district_code == $d['code']) ? 'selected' : ''; ?>>
                                    <?php echo esc($d['name']); ?> District
                                </option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-secondary">Step 2: Select Sub-District or View District Results</label>
                        <div class="input-group input-group-sm mb-2" id="modalSubDistSearchWrap">
                            <span class="input-group-text bg-white border-subtle"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="modalSubDistSearch" class="form-control" placeholder="Search sub-district..." onkeyup="filterModalSubDistricts()">
                            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('modalSubDistSearch').value=''; filterModalSubDistricts();" title="Clear">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div id="modalSubDistrictsList" class="d-flex flex-column gap-2" style="max-height: 280px; overflow-y: auto;">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-primary rounded-pill px-3 fw-bold" id="btnViewDistrictDirect" onclick="viewDistrictDirect()">
                        View District Summary
                    </button>
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================================================
         MODAL: ITEM RESULT WINNER BREAKDOWN (ACCURATE GRADE BADGING)
         ========================================================================== -->
    <div class="modal fade" id="itemResultDetailModal" tabindex="-1" aria-labelledby="itemResultDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content modal-content-light shadow-lg">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="itemResultModalTitle">Item Result</h5>
                        <small class="text-muted" id="itemResultModalSubtitle">Official Declared Result</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="itemResultModalBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mt-2 text-muted">Fetching verified results from server...</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================================================
         MODAL: OFFICIAL PORTAL LOGIN (Landing Page Login Modal - Enhanced Light Theme)
         ========================================================================== -->
    <div class="modal fade" id="portalLoginModal" tabindex="-1" aria-labelledby="portalLoginModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-light shadow-lg">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #4f46e5 0%, #0284c7 100%); display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 12px rgba(79,70,229,0.3);">
                            <i class="fas fa-shield-alt fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="portalLoginModalLabel">Official Portal Login</h5>
                            <div class="text-muted" style="font-size: 0.76rem;">Tamil Kalaivizha 2026 Management System</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form id="portalLoginForm" onsubmit="return handlePortalLogin(event);" method="post" action="<?php echo base_url('login'); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body p-4">
                        <!-- Dynamic Alert Box -->
                        <div id="portalLoginAlert" class="alert d-none mb-3 py-2 px-3 small rounded-3" role="alert"></div>

                        <?php if (session()->getFlashdata('login_error')) : ?>
                            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
                                <i class="fas fa-exclamation-circle me-1"></i> <?php echo esc(session()->getFlashdata('login_error')); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Username / School Code Input -->
                        <div class="login-input-group">
                            <label for="portalUserName" class="form-label small fw-bold text-secondary mb-1">Username or School Code</label>
                            <div class="position-relative">
                                <i class="fas fa-user login-input-icon"></i>
                                <input type="text" name="txtUserName" id="portalUserName" 
                                       class="form-control form-control-portal" 
                                       placeholder="Enter username or school code" 
                                       required autocomplete="username"
                                       autofocus
                                       value="<?php echo htmlspecialchars(get_cookie('username') ?? ''); ?>">
                            </div>
                            <div id="portalUserFeedback" class="invalid-feedback d-none small mt-1">Please enter your username or school code.</div>
                        </div>

                        <!-- Password Input with Show/Hide Eye Toggle -->
                        <div class="login-input-group">
                            <label for="portalPassword" class="form-label small fw-bold text-secondary mb-1">Password</label>
                            <div class="position-relative">
                                <i class="fas fa-lock login-input-icon"></i>
                                <input type="password" name="txtPassword" id="portalPassword" 
                                       class="form-control form-control-portal" 
                                       placeholder="Enter your password" 
                                       required autocomplete="current-password"
                                       value="<?php echo htmlspecialchars(get_cookie('password') ?? ''); ?>">
                                <button type="button" class="btn-password-toggle" id="togglePortalPassword" onclick="togglePasswordVisibility()" title="Show/Hide password">
                                    <i class="fas fa-eye" id="passwordEyeIcon"></i>
                                </button>
                            </div>
                            <div id="portalPassFeedback" class="invalid-feedback d-none small mt-1">Please enter your password.</div>
                        </div>

                        <!-- Remember Me (No Item Codes Link!) -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" value="true" id="portalRememberMe" <?php echo get_cookie('remember') ? 'checked' : ''; ?>>
                                <label class="form-check-label small fw-semibold text-secondary" for="portalRememberMe">Remember me</label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="portalLoginSubmitBtn" class="btn-portal-submit">
                            <i class="fas fa-sign-in-alt fs-5"></i>
                            <span id="portalLoginBtnText">Sign In to Dashboard</span>
                        </button>
                    </div>
                </form>

                <div class="modal-footer justify-content-between py-2 px-4">
                    <span class="small text-muted" style="font-size: 0.75rem;">
                        <i class="fas fa-lock me-1 text-primary"></i> Authorized school and official access only
                    </span>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="container-fluid px-3 px-md-4">
            <h6 class="text-dark mb-1 fw-bold">Tamil Kalaivizha 2026 • Official Results Portal</h6>
            <p class="mb-0 text-muted small">Government of Kerala General Education Department • Powered by Tamil Supporters</p>
            <p id="publicResultRefreshStatus" class="mb-0 mt-2 text-muted small"><i class="fas fa-sync-alt me-1"></i> முடிவுகள் தானாகச் சரிபார்க்கப்படும்</p>
        </div>
    </footer>

    <!-- CLIENT INTERACTIVE JAVASCRIPT ENGINE -->
    <script>
        const subDistrictsByDistrict = <?php echo json_encode($all_subdistricts_by_district ?? []); ?>;
        const currentDistCode = '<?php echo $district_code; ?>';
        const currentSubDistCode = '<?php echo $sub_district_code; ?>';

        function onModalDistrictChange() {
            const distSelect = document.getElementById('modalDistrictSelect');
            const distCode = distSelect.value;
            const container = document.getElementById('modalSubDistrictsList');
            const searchInput = document.getElementById('modalSubDistSearch');
            if (searchInput) searchInput.value = '';
            container.innerHTML = '';

            const subDists = subDistrictsByDistrict[distCode] || [];
            if (subDists.length === 0) {
                container.innerHTML = '<div class="text-muted p-3 text-center small rounded-3 bg-light border"><i class="fas fa-info-circle me-1 text-primary"></i> இந்த மாவட்டத்தில் தமிழ் கலைவிழா உபமாவட்டங்கள் ஏதுமில்லை.<br><span class="text-secondary" style="font-size:0.75rem;">(No participating sub-districts found for this district)</span></div>';
                return;
            }

            subDists.forEach(sd => {
                const isCurrent = (sd.code == currentSubDistCode);
                const btn = document.createElement('a');
                btn.href = '<?php echo base_url('publishresult/publicresult/resultview?sub_district='); ?>' + sd.code;
                btn.className = `btn btn-sm text-start py-2 px-3 rounded-3 d-flex justify-content-between align-items-center subdist-item-btn ${isCurrent ? 'btn-primary text-white' : 'btn-outline-secondary'}`;
                btn.innerHTML = `
                    <span class="d-flex align-items-center">
                        <i class="fas fa-map-pin me-2 ${isCurrent ? 'text-white' : 'text-primary'}"></i>
                        <strong>${sd.name}</strong>
                    </span>
                    <i class="fas fa-chevron-right small ${isCurrent ? 'text-white' : 'text-muted'}"></i>
                `;
                container.appendChild(btn);
            });
        }

        function filterModalSubDistricts() {
            const query = (document.getElementById('modalSubDistSearch')?.value || '').toLowerCase().trim();
            const items = document.querySelectorAll('#modalSubDistrictsList .subdist-item-btn');
            items.forEach(el => {
                const text = el.innerText.toLowerCase();
                el.style.setProperty('display', text.includes(query) ? 'flex' : 'none', 'important');
            });
        }

        function viewDistrictDirect() {
            const distSelect = document.getElementById('modalDistrictSelect');
            const fallbackDistrict = (distSelect && distSelect.options.length > 1) ? distSelect.options[1].value : '';
            const distCode = (distSelect && distSelect.value) || currentDistCode || fallbackDistrict;
            if (!distCode) return;
            window.location.href = '<?php echo base_url('publishresult/publicresult/resultview?district='); ?>' + encodeURIComponent(distCode);
        }

        // Initialize modal on load if selector exists
        document.addEventListener('DOMContentLoaded', () => {
            const distSelect = document.getElementById('modalDistrictSelect');
            if (distSelect && currentDistCode) {
                distSelect.value = currentDistCode;
                onModalDistrictChange();
            } else if (distSelect && distSelect.options.length > 1) {
                distSelect.selectedIndex = 1;
                onModalDistrictChange();
            }
        });

        // ==========================================================================
        // 1. DRILLDOWN FULL RANKINGS CONTROLLER (View More...)
        // ==========================================================================
        const fullSubdistrictsData = <?php echo json_encode($all_subdistrict_points ?? []); ?>;
        const fullSchoolsData = <?php echo json_encode($all_school_points ?? []); ?>;

        function showFullSubdistricts() {
            document.getElementById('districtRow1Cards').style.display = 'none';
            const drilldown = document.getElementById('drilldownView');
            drilldown.style.display = 'block';
            document.getElementById('drilldownTitle').innerHTML = '<i class="fas fa-chess-knight text-primary me-2"></i> All Sub-Districts Ranking (Full)';
            document.getElementById('drilldownSubtitle').innerText = 'Complete cumulative points across LP, UP, HS, and HSS competitions';

            let html = `
                <table class="table-champion">
                    <thead>
                        <tr>
                            <th style="width: 70px;" class="text-center">Rank</th>
                            <th style="width: 110px;">Sub-Dist Code</th>
                            <th>Sub-District Name</th>
                            <th class="text-center" style="width: 130px;">Total Points</th>
                            <th class="text-center" style="width: 140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            if (fullSubdistrictsData.length > 0) {
                fullSubdistrictsData.forEach((sd, idx) => {
                    let rankBadge = (idx === 0) ? '<span class="badge bg-warning text-dark fw-bold px-2 py-1">1st 🥇</span>' :
                                    ((idx === 1) ? '<span class="badge bg-secondary text-white fw-bold px-2 py-1">2nd 🥈</span>' :
                                    ((idx === 2) ? '<span class="badge text-white fw-bold px-2 py-1" style="background:#d97706;">3rd 🥉</span>' : (idx + 1)));
                    html += `
                        <tr>
                            <td class="text-center fw-bold">${rankBadge}</td>
                            <td class="fw-bold text-muted">${sd.sub_district_code}</td>
                            <td class="fw-semibold text-dark">${sd.sub_district_name}</td>
                            <td class="text-center"><span class="badge-points">${sd.total_points}</span></td>
                            <td class="text-center">
                                <a href="<?php echo base_url('publishresult/publicresult/resultview?sub_district='); ?>${sd.sub_district_code}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    View Results
                                </a>
                            </td>
                        </tr>
                    `;
                });
            } else {
                html += `<tr><td colspan="5" class="text-center py-4 text-muted">No subdistrict data available.</td></tr>`;
            }

            html += `</tbody></table>`;
            document.getElementById('drilldownTableContent').innerHTML = html;
            window.scrollTo({ top: drilldown.offsetTop - 80, behavior: 'smooth' });
        }

        function showFullSchools() {
            document.getElementById('districtRow1Cards').style.display = 'none';
            const qb = document.getElementById('districtQuickSearchBox'); if (qb) qb.style.display = 'none';
            const drilldown = document.getElementById('drilldownView');
            drilldown.style.display = 'block';
            document.getElementById('drilldownTitle').innerHTML = '<i class="fas fa-trophy text-warning me-2"></i> All Participating Schools Ranking (Full)';
            document.getElementById('drilldownSubtitle').innerText = 'Complete cumulative school points across all categories';

            let html = `
                <table class="table-champion">
                    <thead>
                        <tr>
                            <th style="width: 70px;" class="text-center">Rank</th>
                            <th style="width: 100px;">School Code</th>
                            <th>School Name</th>
                            <th>Sub-District</th>
                            <th class="text-center" style="width: 130px;">Total Points</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            if (fullSchoolsData.length > 0) {
                fullSchoolsData.forEach((sch, idx) => {
                    let rankBadge = (idx === 0) ? '<span class="badge bg-warning text-dark fw-bold px-2 py-1">1st 🥇</span>' :
                                    ((idx === 1) ? '<span class="badge bg-secondary text-white fw-bold px-2 py-1">2nd 🥈</span>' :
                                    ((idx === 2) ? '<span class="badge text-white fw-bold px-2 py-1" style="background:#d97706;">3rd 🥉</span>' : (idx + 1)));
                    html += `
                        <tr>
                            <td class="text-center fw-bold">${rankBadge}</td>
                            <td class="fw-bold text-muted">${sch.school_code}</td>
                            <td class="fw-semibold text-dark">${sch.school_name}</td>
                            <td class="text-muted">${sch.sub_district_name || '-'}</td>
                            <td class="text-center"><span class="badge-points-gold">${sch.total_points}</span></td>
                        </tr>
                    `;
                });
            } else {
                html += `<tr><td colspan="5" class="text-center py-4 text-muted">No school points recorded yet.</td></tr>`;
            }

            html += `</tbody></table>`;
            document.getElementById('drilldownTableContent').innerHTML = html;
            window.scrollTo({ top: drilldown.offsetTop - 80, behavior: 'smooth' });
        }

        function closeDrilldown() {
            document.getElementById('drilldownView').style.display = 'none';
            document.getElementById('districtRow1Cards').style.display = 'flex';
            const qb = document.getElementById('districtQuickSearchBox'); if (qb) qb.style.display = 'block';
            const subdistGrid = document.getElementById('districtSubdistrictGridSection');
            if (subdistGrid) subdistGrid.style.display = 'block';
        }

        // ==========================================================================
        // 2. WINNERS CAROUSEL CONTROLLER
        // ==========================================================================
        let winnerAutoplayInterval = null;
        let isWinnerAutoplayActive = true;

        function scrollWinnersCarousel(step) {
            const track = document.getElementById('winnersTrack');
            if (!track) return;
            track.scrollBy({ left: step, behavior: 'smooth' });
        }

        function toggleWinnerAutoplay() {
            const btn = document.getElementById('btnPlayPause');
            if (isWinnerAutoplayActive) {
                pauseWinnerAutoplay();
                btn.innerHTML = '<i class="fas fa-play me-1"></i> Play';
            } else {
                resumeWinnerAutoplay();
                btn.innerHTML = '<i class="fas fa-pause me-1"></i> Pause';
            }
        }

        function pauseWinnerAutoplay() {
            if (winnerAutoplayInterval) {
                clearInterval(winnerAutoplayInterval);
                winnerAutoplayInterval = null;
            }
            isWinnerAutoplayActive = false;
        }

        function resumeWinnerAutoplay() {
            isWinnerAutoplayActive = true;
            if (!winnerAutoplayInterval) {
                winnerAutoplayInterval = setInterval(() => {
                    const track = document.getElementById('winnersTrack');
                    if (!track) return;
                    if (track.scrollLeft + track.clientWidth >= track.scrollWidth - 10) {
                        track.scrollTo({ left: 0, behavior: 'smooth' });
                    } else {
                        track.scrollBy({ left: 340, behavior: 'smooth' });
                    }
                }, 3800);
            }
        }

        function filterWinners(category, btn) {
            const buttons = document.querySelectorAll('#winnerFilterTabs .tab-filter-pill');
            buttons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const cards = document.querySelectorAll('#winnersTrack .card-winner-item');
            cards.forEach(card => {
                const cardCat = card.getAttribute('data-category');
                if (category === 'ALL' || cardCat === category) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
            const track = document.getElementById('winnersTrack');
            if (track) track.scrollTo({ left: 0, behavior: 'smooth' });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const track = document.getElementById('winnersTrack');
            if (track) {
                resumeWinnerAutoplay();
            }
        });

        // ==========================================================================
        // 3. LIVE ITEM CODE SEARCH & TABLE FILTER
        // ==========================================================================
        let currentTableCatFilter = 'all';

        function filterCompetitionsTableCat(cat, btn) {
            currentTableCatFilter = cat;
            const pills = document.querySelectorAll('#tableCatPills .tab-filter-pill');
            pills.forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            applyItemTableFilter();
        }

        function applyItemTableFilter() {
            const searchVal = (document.getElementById('liveItemSearch')?.value || '').toLowerCase().trim();
            const rows = document.querySelectorAll('#competitionsTable tbody tr[data-code]');

            rows.forEach(r => {
                const code = r.getAttribute('data-code') || '';
                const name = r.getAttribute('data-name') || '';
                const cat = r.getAttribute('data-cat') || '';

                const matchesCat = (currentTableCatFilter === 'all' || cat === currentTableCatFilter);
                const matchesSearch = (!searchVal || code.includes(searchVal) || name.includes(searchVal));

                if (matchesCat && matchesSearch) {
                    r.style.display = '';
                } else {
                    r.style.display = 'none';
                }
            });
        }

        document.getElementById('liveItemSearch')?.addEventListener('input', applyItemTableFilter);

        // ==========================================================================
        // 4. ACCURATE GRADE BADGING & ITEM RESULTS FETCH
        // ==========================================================================
        function formatGradeBadge(grade) {
            if (!grade) return '<span class="text-muted fw-bold">-</span>';
            const g = String(grade).trim().toUpperCase();
            if (g === 'A') {
                return '<span class="badge bg-success text-white fw-bold px-2 py-1 shadow-sm" style="font-size:0.8rem;"><i class="fas fa-award me-1"></i>Grade A 🌟</span>';
            } else if (g === 'B') {
                return '<span class="badge bg-primary text-white fw-bold px-2 py-1 shadow-sm" style="font-size:0.8rem;"><i class="fas fa-star me-1"></i>Grade B ⭐</span>';
            } else if (g === 'C') {
                return '<span class="badge text-white fw-bold px-2 py-1 shadow-sm" style="background-color:#8b5cf6; font-size:0.8rem;"><i class="fas fa-certificate me-1"></i>Grade C</span>';
            } else {
                return '<span class="text-muted fw-bold">-</span>';
            }
        }

        function fetchItemResults(itemCode, itemName) {
            const modalTitle = document.getElementById('itemResultModalTitle');
            const modalSubtitle = document.getElementById('itemResultModalSubtitle');
            const modalBody = document.getElementById('itemResultModalBody');

            modalTitle.innerText = `Item ${itemCode} - ${itemName}`;
            modalSubtitle.innerText = 'Official Declared Result';
            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-2 text-muted">Fetching verified results from server...</div>
                </div>
            `;

            const itemModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('itemResultDetailModal'));
            itemModal.show();

            const subDistParam = currentSubDistCode ? `&sub_district=${currentSubDistCode}` : '';
            const distParam = currentDistCode ? `&district=${currentDistCode}` : '';

            fetch(`<?php echo site_url('publishresult/publicresult/get_item_results_ajax'); ?>?item_code=${itemCode}${subDistParam}${distParam}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.results && data.results.length > 0) {
                    let html = `
                        <div class="table-responsive">
                            <table class="table-champion">
                                <thead>
                                    <tr>
                                        <th style="width: 85px;">Rank</th>
                                        <th style="width: 65px;">Photo</th>
                                        <th>Participant Name</th>
                                        <th>School Name</th>
                                        <th class="text-center">Grade</th>
                                        <th class="text-center">Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    data.results.forEach(r => {
                        let rankDisplay = (r.rank == 1) ? '<span class="badge bg-warning text-dark fw-bold px-2 py-1">1st 🥇</span>' : 
                                          ((r.rank == 2) ? '<span class="badge bg-secondary text-white fw-bold px-2 py-1">2nd 🥈</span>' : 
                                          ((r.rank == 3) ? '<span class="badge text-white fw-bold px-2 py-1" style="background:#d97706;">3rd 🥉</span>' : (r.rank || '-')));
                        html += `
                            <tr>
                                <td class="fw-bold">${rankDisplay}</td>
                                <td>
                                    <img src="${r.photo_url}" style="width: 44px; height: 54px; object-fit: cover; border-radius: 8px; border: 1px solid #cbd5e1;" onerror="this.src='<?php echo base_url('photos/img_user_nophoto.png'); ?>';">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">${r.participant_name}</div>
                                    <small class="text-muted">Admn: ${r.admn_no} | Class: ${r.class || '-'}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">${r.school_name}</div>
                                    <small class="text-muted">${r.sub_district_name || ''}</small>
                                </td>
                                <td class="text-center">
                                    ${formatGradeBadge(r.grade)}
                                </td>
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
                        <div class="alert alert-info text-center my-3 border">
                            <i class="fas fa-info-circle me-1 text-primary"></i> Results for this competition have not been declared yet.
                        </div>
                    `;
                }
            })
            .catch(err => {
                modalBody.innerHTML = `
                    <div class="alert alert-danger text-center my-3 border">
                        <i class="fas fa-exclamation-triangle me-1"></i> Error loading results. Please try again.
                    </div>
                `;
            });
        }

        // ==========================================================================
        // 5. OFFICIAL PORTAL LOGIN HANDLERS
        // ==========================================================================
        function togglePasswordVisibility() {
            const passInput = document.getElementById('portalPassword');
            const eyeIcon = document.getElementById('passwordEyeIcon');
            if (!passInput || !eyeIcon) return;
            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        async function handlePortalLogin(event) {
            event.preventDefault();
            const form = document.getElementById('portalLoginForm');
            const usernameInput = document.getElementById('portalUserName');
            const passwordInput = document.getElementById('portalPassword');
            const submitBtn = document.getElementById('portalLoginSubmitBtn');
            const btnText = document.getElementById('portalLoginBtnText');
            const alertBox = document.getElementById('portalLoginAlert');

            const username = usernameInput.value.trim();
            const password = passwordInput.value.trim();

            alertBox.className = 'alert d-none mb-3 py-2 px-3 small rounded-3';
            alertBox.innerHTML = '';
            usernameInput.classList.remove('is-invalid');
            passwordInput.classList.remove('is-invalid');

            if (!username) {
                usernameInput.classList.add('is-invalid');
                usernameInput.focus();
                return false;
            }
            if (!password) {
                passwordInput.classList.add('is-invalid');
                passwordInput.focus();
                return false;
            }

            submitBtn.disabled = true;
            btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Verifying credentials...';

            try {
                const formData = new FormData(form);
                const csrfInput = form.querySelector('input[name="csrf_test_name"]');
                const csrfToken = csrfInput ? csrfInput.value : '';

                const response = await fetch('<?php echo site_url('login/ajax_login'); ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    alertBox.className = 'alert alert-success mb-3 py-2 px-3 small rounded-3';
                    alertBox.innerHTML = '<i class="fas fa-check-circle me-1"></i> ' + (data.message || 'Login successful! Redirecting...');
                    setTimeout(() => {
                        window.location.href = data.redirect || '<?php echo base_url('welcome'); ?>';
                    }, 450);
                } else {
                    submitBtn.disabled = false;
                    btnText.innerHTML = 'Sign In to Dashboard';
                    alertBox.className = 'alert alert-danger mb-3 py-2 px-3 small rounded-3';
                    alertBox.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> ' + (data.message || 'Invalid credentials. Please try again.');
                    if (data.field === 'username') {
                        usernameInput.classList.add('is-invalid');
                        usernameInput.focus();
                    } else {
                        passwordInput.classList.add('is-invalid');
                        passwordInput.focus();
                    }
                }
            } catch (err) {
                console.error('AJAX Login error:', err);
                submitBtn.disabled = false;
                btnText.innerHTML = 'Sign In to Dashboard';
                form.submit();
            }
            return false;
        }


        // Poll only the tiny cached version token; reload the page only when
        // the selected scope actually changes. Jitter prevents synchronized bursts.
        const publicResultVersionUrl = <?php echo json_encode(
            site_url('publishresult/publicresult/cache_version')
            . '?district=' . rawurlencode((string) ($district_code ?? ''))
            . '&sub_district=' . rawurlencode((string) ($sub_district_code ?? ''))
        ); ?>;
        const publicResultInitialVersion = <?php echo json_encode((string) ($cache_version ?? '1')); ?>;
        let publicResultReloadScheduled = false;

        async function checkPublicResultVersion() {
            if (publicResultReloadScheduled) return;
            if (document.visibilityState === 'hidden') {
                schedulePublicResultVersionCheck();
                return;
            }
            try {
                const response = await fetch(publicResultVersionUrl, {
                    method: 'GET',
                    cache: 'no-store',
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' }
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data && String(data.version) !== String(publicResultInitialVersion)) {
                        publicResultReloadScheduled = true;
                        const status = document.getElementById('publicResultRefreshStatus');
                        if (status) status.innerHTML = '<i class="fas fa-sync-alt fa-spin me-1"></i> புதிய முடிவு கிடைத்தது; பக்கம் புதுப்பிக்கப்படுகிறது…';
                        window.setTimeout(() => window.location.reload(), 700);
                        return;
                    }
                }
            } catch (error) {
                // A temporary poll failure must not interrupt result viewing.
            }
            schedulePublicResultVersionCheck();
        }

        function schedulePublicResultVersionCheck() {
            const delay = 45000 + Math.floor(Math.random() * 15000);
            window.setTimeout(checkPublicResultVersion, delay);
        }
        schedulePublicResultVersionCheck();

        // NOTE: Automatic modal opening on reload is COMPLETELY REMOVED as per user requirement!
        // The modal opens exclusively on clicking the Login button.

        // Focus username input with cursor ready when login modal opens
        const portalLoginModalEl = document.getElementById('portalLoginModal');
        if (portalLoginModalEl) {
            portalLoginModalEl.addEventListener('shown.bs.modal', function () {
                const uInput = document.getElementById('portalUserName');
                if (uInput) {
                    uInput.focus();
                    if (uInput.value) {
                        const len = uInput.value.length;
                        uInput.setSelectionRange(len, len);
                    }
                }
            });
        }
    
        // ==========================================================================
        // ITEM CODES DIRECTORY & PDF EXPORT JAVASCRIPT ENGINE (DISTRICT & SUBDISTRICT DEPENDENT)
        // ==========================================================================
        let modalLoadedItems = [];
        let currentModalItemCat = 'ALL';

        function onItemCodeDistrictChange() {
            const distSelect = document.getElementById('modalItemCodeDistrict');
            const subDistSelect = document.getElementById('modalItemCodeSubDistrict');
            if (!distSelect || !subDistSelect) return;

            const distCode = distSelect.value;
            subDistSelect.innerHTML = '<option value="">-- Choose Sub-District --</option>';

            const subDists = subDistrictsByDistrict[distCode] || [];
            subDists.forEach(sd => {
                const opt = document.createElement('option');
                opt.value = sd.code;
                opt.textContent = sd.name;
                subDistSelect.appendChild(opt);
            });

            // If only 1 subdistrict, auto select
            if (subDists.length === 1) {
                subDistSelect.value = subDists[0].code;
            }

            onItemCodeSubDistrictChange();
        }

        function onItemCodeSubDistrictChange() {
            const distSelect = document.getElementById('modalItemCodeDistrict');
            const subDistSelect = document.getElementById('modalItemCodeSubDistrict');
            const distCode = distSelect ? distSelect.value : '';
            const subDistCode = subDistSelect ? subDistSelect.value : '';

            const promptEl = document.getElementById('itemCodesSelectionPrompt');
            const toolbarEl = document.getElementById('itemCodesToolbar');
            const tableContainer = document.getElementById('itemCodesTableContainer');
            const loaderEl = document.getElementById('itemCodesLoader');

            if (!distCode || !subDistCode) {
                // Keep table hidden and show selection prompt
                if (promptEl) promptEl.classList.remove('d-none');
                if (toolbarEl) toolbarEl.classList.add('d-none');
                if (tableContainer) tableContainer.classList.add('d-none');
                if (loaderEl) loaderEl.classList.add('d-none');
                modalLoadedItems = [];
                return;
            }

            // Both selected: fetch items via AJAX
            loadModalItemCodesAjax(distCode, subDistCode);
        }

        function reloadModalItemCodes() {
            const distSelect = document.getElementById('modalItemCodeDistrict');
            const subDistSelect = document.getElementById('modalItemCodeSubDistrict');
            if (distSelect && subDistSelect && distSelect.value && subDistSelect.value) {
                loadModalItemCodesAjax(distSelect.value, subDistSelect.value);
            }
        }

        function loadModalItemCodesAjax(distCode, subDistCode) {
            const promptEl = document.getElementById('itemCodesSelectionPrompt');
            const toolbarEl = document.getElementById('itemCodesToolbar');
            const tableContainer = document.getElementById('itemCodesTableContainer');
            const loaderEl = document.getElementById('itemCodesLoader');

            if (promptEl) promptEl.classList.add('d-none');
            if (tableContainer) tableContainer.classList.add('d-none');
            if (toolbarEl) toolbarEl.classList.add('d-none');
            if (loaderEl) loaderEl.classList.remove('d-none');

            // Update sub header text
            const distSelect = document.getElementById('modalItemCodeDistrict');
            const subDistSelect = document.getElementById('modalItemCodeSubDistrict');
            const distName = (distSelect && distSelect.options[distSelect.selectedIndex]) ? distSelect.options[distSelect.selectedIndex].text : '';
            const subDistName = (subDistSelect && subDistSelect.options[subDistSelect.selectedIndex]) ? subDistSelect.options[subDistSelect.selectedIndex].text : '';

            const headerSub = document.getElementById('itemCodesHeaderSub');
            if (headerSub) {
                headerSub.innerHTML = `<span class="fw-bold text-primary">${subDistName}</span> (${distName}) &bull; Active Competition Items`;
            }

            const printHeading = document.getElementById('printHeadingText');
            if (printHeading) {
                printHeading.innerHTML = `${subDistName} (${distName}) &bull; Item Codes Directory`;
            }

            fetch(`<?php echo site_url('publishresult/publicresult/get_public_item_codes_ajax'); ?>?district=${encodeURIComponent(distCode)}&sub_district=${encodeURIComponent(subDistCode)}`)
                .then(res => res.json())
                .then(data => {
                    if (loaderEl) loaderEl.classList.add('d-none');
                    if (data && data.status === 'success' && Array.isArray(data.items)) {
                        modalLoadedItems = data.items;
                        if (toolbarEl) toolbarEl.classList.remove('d-none');
                        if (tableContainer) tableContainer.classList.remove('d-none');
                        updateModalItemCounts();
                        applyModalItemFilter();
                    } else {
                        modalLoadedItems = [];
                        if (tableContainer) tableContainer.classList.remove('d-none');
                        renderItemCodesModalTable([]);
                    }
                })
                .catch(err => {
                    console.error('Error fetching item codes:', err);
                    if (loaderEl) loaderEl.classList.add('d-none');
                    if (tableContainer) tableContainer.classList.remove('d-none');
                    renderItemCodesModalTable([]);
                });
        }

        function renderItemCodesModalTable(items) {
            const tbody = document.getElementById('modalItemCodesTableBody');
            const printBody = document.getElementById('printItemCodesTableBody');
            const footerCount = document.getElementById('itemCodesFooterCount');
            if (footerCount) footerCount.innerText = items ? items.length : 0;
            if (!tbody) return;

            let html = '';
            let printHtml = '';

            if (!items || items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="2" class="text-center py-4 text-muted">No matching items found for the selected category.</td></tr>';
                if (printBody) printBody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 10px;">No items found.</td></tr>';
                return;
            }

            items.forEach(it => {
                const fId = parseInt(it.fest_id) || 1;
                const fName = it.fest_name || (fId === 1 ? 'LP' : (fId === 2 ? 'UP' : (fId === 3 ? 'HS' : 'HSS')));

                const isGroup = (it.item_type === 'G');
                // English only badges as requested: Single / Group
                const typeBadge = isGroup ? '<span class="badge bg-purple bg-opacity-10 text-purple border border-purple border-opacity-25 rounded-pill px-3 py-1 fw-bold"><i class="fas fa-users me-1"></i>Group</span>' 
                                          : '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-1 fw-bold"><i class="fas fa-user me-1"></i>Single</span>';

                const printType = isGroup ? 'Group' : 'Single';
                const maxTime = (it.max_time && it.max_time > 0) ? `${it.max_time} min` : '-';
                const maxPart = it.max_participants || (isGroup ? '7' : '1');

                // MODAL TABLE: Item Code, Item Name with Type below (hide all others)
                html += `
                    <tr>
                        <td class="text-center align-middle" style="width: 110px;">
                            <span class="item-code-badge">#${it.item_code}</span>
                        </td>
                        <td class="align-middle">
                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">${it.item_name}</div>
                            <div class="mt-1">${typeBadge}</div>
                        </td>
                    </tr>
                `;

                // PRINT/PDF TABLE: FULL COMPLETE DETAILS
                printHtml += `
                    <tr>
                        <td style="text-align: center; font-weight: bold;">#${it.item_code}</td>
                        <td style="font-weight: bold;">${it.item_name}</td>
                        <td style="text-align: center;">${fName}</td>
                        <td style="text-align: center;">${printType}</td>
                        <td style="text-align: center;">${maxTime}</td>
                        <td style="text-align: center;">${maxPart}</td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
            if (printBody) printBody.innerHTML = printHtml;
        }

        function filterItemCodesModal(category, btn) {
            currentModalItemCat = category;
            const buttons = document.querySelectorAll('#itemCodesCatFilterPills button');
            buttons.forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-secondary');
            });
            if (btn) {
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-primary');
            }

            applyModalItemFilter();
        }

        function searchItemCodesModal() {
            applyModalItemFilter();
        }

        function applyModalItemFilter() {
            const query = (document.getElementById('modalItemCodesSearch')?.value || '').toLowerCase().trim();
            const filtered = modalLoadedItems.filter(it => {
                const fId = parseInt(it.fest_id) || 1;
                const fName = (it.fest_name || '').toUpperCase();
                let matchesCat = true;
                if (currentModalItemCat === 'LP') matchesCat = (fId === 1 || fName.includes('LP'));
                else if (currentModalItemCat === 'UP') matchesCat = (fId === 2 || fName.includes('UP'));
                else if (currentModalItemCat === 'HS') matchesCat = (fId === 3 || fName.includes('HS') && !fName.includes('HSS'));
                else if (currentModalItemCat === 'HSS') matchesCat = (fId === 4 || fName.includes('HSS'));

                const matchesQuery = !query || String(it.item_code).includes(query) || (it.item_name || '').toLowerCase().includes(query);
                return matchesCat && matchesQuery;
            });

            renderItemCodesModalTable(filtered);
        }

        function updateModalItemCounts() {
            let lp = 0, up = 0, hs = 0, hss = 0;
            modalLoadedItems.forEach(it => {
                const fId = parseInt(it.fest_id) || 1;
                const fName = (it.fest_name || '').toUpperCase();
                if (fId === 1 || fName.includes('LP')) lp++;
                else if (fId === 2 || fName.includes('UP')) up++;
                else if (fId === 4 || fName.includes('HSS')) hss++;
                else hs++;
            });

            if (document.getElementById('modalItemCountAll')) document.getElementById('modalItemCountAll').innerText = modalLoadedItems.length;
            if (document.getElementById('modalItemCountLP')) document.getElementById('modalItemCountLP').innerText = lp;
            if (document.getElementById('modalItemCountUP')) document.getElementById('modalItemCountUP').innerText = up;
            if (document.getElementById('modalItemCountHS')) document.getElementById('modalItemCountHS').innerText = hs;
            if (document.getElementById('modalItemCountHSS')) document.getElementById('modalItemCountHSS').innerText = hss;
        }

        function printItemCodesDirectory() {
            const printArea = document.getElementById('printableItemCodesArea');
            if (!printArea) return;

            if (!modalLoadedItems || modalLoadedItems.length === 0) {
                alert('Please select a District & Sub-District to load items before printing.');
                return;
            }

            // Always make print area visible for browser print
            printArea.style.display = 'block';
            window.print();
            setTimeout(() => {
                printArea.style.display = 'none';
            }, 1000);
        }

        function openDistrictDirectChooser() {
            const distModal = new bootstrap.Modal(document.getElementById('subDistrictSelectorModal'));
            distModal.show();
        }

        function openDistrictSubDists(distCode) {
            const distSelect = document.getElementById('modalDistrictSelect');
            if (distSelect) {
                distSelect.value = distCode;
                onModalDistrictChange();
            }
            const modal = new bootstrap.Modal(document.getElementById('subDistrictSelectorModal'));
            modal.show();
        }

        // Initialize Item Codes Modal selector if current district / subdistrict exist
        document.addEventListener('DOMContentLoaded', () => {
            const itemDistSelect = document.getElementById('modalItemCodeDistrict');
            if (itemDistSelect) {
                if (currentDistCode) {
                    itemDistSelect.value = currentDistCode;
                    onItemCodeDistrictChange();
                    if (currentSubDistCode) {
                        const itemSubDistSelect = document.getElementById('modalItemCodeSubDistrict');
                        if (itemSubDistSelect) {
                            itemSubDistSelect.value = currentSubDistCode;
                            onItemCodeSubDistrictChange();
                        }
                    }
                }
            }
        });

    
        // ==========================================================================
        // PROPER CLEAN MODAL SWITCHING & NAVIGATION CONTROLLER
        // ==========================================================================
        function closeAllOpenModals() {
            const modalIds = [
                'subDistrictSelectorModal',
                'districtDirectSelectorModal',
                'itemCodesModal',
                'portalLoginModal',
                'itemResultDetailModal',
                'latestResultsModal',
                'stagesModal'
            ];
            modalIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    const inst = bootstrap.Modal.getInstance(el);
                    if (inst) {
                        inst.hide();
                    }
                }
            });
            // Clean up any lingering backdrops
            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            document.body.style.removeProperty('overflow');
        }

        function setActiveBottomNav(targetEl) {
            document.querySelectorAll('.mobile-bottom-nav .mobile-nav-item').forEach(item => {
                item.classList.remove('active');
            });
            if (targetEl) {
                targetEl.classList.add('active');
            }
        }

        function syncActiveBottomNavToPage() {
            document.querySelectorAll('.mobile-bottom-nav .mobile-nav-item').forEach(item => {
                item.classList.remove('active');
            });
            let el = null;
            <?php if ($view_mode === 'sub_district') : ?>
                el = document.getElementById('navItemSubDist');
            <?php elseif ($view_mode === 'district') : ?>
                el = document.getElementById('navItemDistrict');
            <?php else : ?>
                el = document.getElementById('navItemHome');
            <?php endif; ?>
            if (el) el.classList.add('active');
        }

        function navigateToHome(e) {
            if (e) e.preventDefault();
            closeAllOpenModals();
            setActiveBottomNav(document.getElementById('navItemHome'));
            window.location.href = '<?php echo base_url(); ?>';
        }

        function openSubDistrictModalClean(e, triggerEl) {
            if (e) e.preventDefault();
            closeAllOpenModals();
            setActiveBottomNav(triggerEl || document.getElementById('navItemSubDist'));
            setTimeout(() => {
                const modalEl = document.getElementById('subDistrictSelectorModal');
                if (modalEl) {
                    const inst = bootstrap.Modal.getOrCreateInstance(modalEl);
                    inst.show();
                }
            }, 100);
        }

        function openDistrictDirectModalClean(e, triggerEl) {
            if (e) e.preventDefault();
            closeAllOpenModals();
            setActiveBottomNav(triggerEl || document.getElementById('navItemDistrict'));
            setTimeout(() => {
                const modalEl = document.getElementById('districtDirectSelectorModal');
                if (modalEl) {
                    const inst = bootstrap.Modal.getOrCreateInstance(modalEl);
                    inst.show();
                }
            }, 100);
        }

        function openItemCodesModalClean(e, triggerEl) {
            if (e) e.preventDefault();
            closeAllOpenModals();
            setActiveBottomNav(triggerEl || document.getElementById('navItemItems'));
            setTimeout(() => {
                const modalEl = document.getElementById('itemCodesModal');
                if (modalEl) {
                    const inst = bootstrap.Modal.getOrCreateInstance(modalEl);
                    inst.show();
                }
            }, 100);
        }

        function openLoginModalClean(e, triggerEl) {
            if (e) e.preventDefault();
            closeAllOpenModals();
            setActiveBottomNav(triggerEl || document.getElementById('navItemLogin'));
            setTimeout(() => {
                const modalEl = document.getElementById('portalLoginModal');
                if (modalEl) {
                    const inst = bootstrap.Modal.getOrCreateInstance(modalEl);
                    inst.show();
                }
            }, 100);
        }

        // When modals close, if no other modal is visible, restore highlight to current page menu
        document.addEventListener('DOMContentLoaded', function () {
            ['subDistrictSelectorModal', 'districtDirectSelectorModal', 'itemCodesModal', 'portalLoginModal'].forEach(id => {
                const m = document.getElementById(id);
                if (m) {
                    m.addEventListener('hidden.bs.modal', function () {
                        setTimeout(() => {
                            const anyOpen = document.querySelector('.modal.show');
                            if (!anyOpen) {
                                syncActiveBottomNavToPage();
                            }
                        }, 50);
                    });
                }
            });
        });

    </script>

    <!-- MOBILE NATIVE APP BOTTOM NAVIGATION BAR -->
    <nav class="mobile-bottom-nav">
        <a href="<?php echo base_url(); ?>" class="mobile-nav-item <?php echo ($view_mode === 'home') ? 'active' : ''; ?>" id="navItemHome" onclick="navigateToHome(event)">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="#" class="mobile-nav-item <?php echo ($view_mode === 'sub_district') ? 'active' : ''; ?>" id="navItemSubDist" onclick="openSubDistrictModalClean(event, this)">
            <i class="fas fa-map-location-dot"></i>
            <span>Sub-dist</span>
        </a>
        <a href="#" class="mobile-nav-item <?php echo ($view_mode === 'district') ? 'active' : ''; ?>" id="navItemDistrict" onclick="openDistrictDirectModalClean(event, this)">
            <i class="fas fa-trophy"></i>
            <span>District</span>
        </a>
        <a href="#" class="mobile-nav-item" id="navItemItems" onclick="openItemCodesModalClean(event, this)">
            <i class="fas fa-file-pdf"></i>
            <span>Items</span>
        </a>
        <a href="#" class="mobile-nav-item" id="navItemLogin" onclick="openLoginModalClean(event, this)">
            <i class="fas fa-user-circle"></i>
            <span>Login</span>
        </a>
    </nav>

</body>
</html>
