<!DOCTYPE html>
<html lang="ta">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>பள்ளிப் புள்ளிகள் (School Points Leaderboard) - Tamil Kalaivizha</title>
    <link rel="shortcut icon" type="image/x-icon" href="https://photos.app.goo.gl/4Tv9UCTHxEAN44Pm7">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        @font-face {
            font-family: 'TAU-Pallai';
            src: url('<?= base_url("assets/fonts/TAU-Paalai.ttf") ?>') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'TAU-Pallai';
            src: url('<?= base_url("assets/fonts/TAU-Paalai Bold.ttf") ?>') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        .font-tamil {
            font-family: 'TAU-Pallai', 'Mukta Malar', 'Noto Sans Tamil', sans-serif !important;
            font-size: 14px;
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #1e293b;
            min-height: 100vh;
        }

        /* Top Sticky Bar */
        .sp-topbar {
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
            padding: 0.85rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid #e2e8f0;
        }

        .sp-back-btn {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            border-radius: 9999px;
            padding: 0.45rem 1.15rem;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }
        .sp-back-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* Section Badge next to back button */
        .sp-section-badge {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 0.48rem 1.15rem;
            border-radius: 9999px;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            letter-spacing: 0.3px;
        }

        /* Podium Cards Styling */
        .sp-podium-card {
            border: none;
            border-radius: 1.5rem;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            text-align: center;
            padding: 1.75rem 1.25rem 1.5rem;
        }
        .sp-podium-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.12);
        }

        /* Rank 1 (Champion) */
        .sp-podium-card.rank-1 {
            background: linear-gradient(180deg, #fffdf0 0%, #ffffff 100%);
            border: 2px solid #fcd34d;
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.18), 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .sp-podium-card.rank-1:hover {
            transform: translateY(-8px) scale(1.015);
            box-shadow: 0 20px 40px rgba(245, 158, 11, 0.28);
        }
        .sp-podium-card.rank-1::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #f59e0b, #fbbf24, #f59e0b);
        }

        /* Rank 2 (Silver) */
        .sp-podium-card.rank-2 {
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            border: 2px solid #cbd5e1;
        }
        .sp-podium-card.rank-2::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #94a3b8, #cbd5e1, #94a3b8);
        }

        /* Rank 3 (Bronze) */
        .sp-podium-card.rank-3 {
            background: linear-gradient(180deg, #fff7ed 0%, #ffffff 100%);
            border: 2px solid #fed7aa;
        }
        .sp-podium-card.rank-3::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #ea580c, #fb923c, #ea580c);
        }

        /* 3D Rotating Trophy Cup (Rank 1) */
        .sp-trophy-3d-scene {
            perspective: 900px;
            width: 86px;
            height: 86px;
            margin: 0.25rem auto 0.85rem;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sp-trophy-pedestal-glow {
            position: absolute;
            bottom: 4px;
            width: 60px;
            height: 14px;
            border-radius: 50%;
            background: radial-gradient(ellipse at center, rgba(245, 158, 11, 0.7) 0%, rgba(245, 158, 11, 0) 70%);
            animation: pedestalPulse 2.4s ease-in-out infinite alternate;
        }
        .sp-trophy-3d-cup {
            width: 76px;
            height: 76px;
            transform-style: preserve-3d;
            animation: spinTrophy3D 5s linear infinite;
            filter: drop-shadow(0 10px 14px rgba(180, 83, 9, 0.35));
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @keyframes spinTrophy3D {
            0% {
                transform: rotateY(0deg) rotateX(4deg);
            }
            50% {
                transform: rotateY(180deg) rotateX(-4deg);
            }
            100% {
                transform: rotateY(360deg) rotateX(4deg);
            }
        }
        @keyframes pedestalPulse {
            0% { transform: scale(0.85); opacity: 0.6; }
            100% { transform: scale(1.15); opacity: 1; }
        }

        /* Floating Silver & Bronze Icons */
        .sp-trophy-silver-wrap {
            width: 72px;
            height: 72px;
            margin: 0.25rem auto 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: floatSilver 3s ease-in-out infinite alternate;
            filter: drop-shadow(0 8px 12px rgba(100, 116, 139, 0.25));
        }
        @keyframes floatSilver {
            0% { transform: translateY(0px); }
            100% { transform: translateY(-7px); }
        }

        .sp-trophy-bronze-wrap {
            width: 72px;
            height: 72px;
            margin: 0.25rem auto 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: floatBronze 3.2s ease-in-out infinite alternate;
            filter: drop-shadow(0 8px 12px rgba(194, 65, 12, 0.25));
        }
        @keyframes floatBronze {
            0% { transform: translateY(0px); }
            100% { transform: translateY(-7px); }
        }

        /* Champion Slot Above Button */
        .sp-champion-slot {
            min-height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.4rem;
        }
        .sp-champion-badge {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 1.5px;
            padding: 0.22rem 0.9rem;
            border-radius: 9999px;
            text-transform: uppercase;
            box-shadow: 0 2px 6px rgba(217, 119, 6, 0.35);
            display: inline-flex;
            align-items: center;
        }

        /* Rank Badges: All 3 Buttons Exactly Same Size */
        .sp-rank-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.42rem 1.25rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 700;
            min-width: 195px;
            height: 38px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
            border: none;
        }
        .sp-rank-pill-1 {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            box-shadow: 0 3px 8px rgba(217, 119, 6, 0.3);
        }
        .sp-rank-pill-2 {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            color: #ffffff;
            box-shadow: 0 3px 8px rgba(71, 85, 105, 0.25);
        }
        .sp-rank-pill-3 {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
            color: #ffffff;
            box-shadow: 0 3px 8px rgba(194, 65, 12, 0.25);
        }

        /* School Title Wrap & Auto-Resizing Single Line */
        .sp-school-title-wrap {
            width: 100%;
            height: 2.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            white-space: nowrap;
            padding: 0 4px;
            box-sizing: border-box;
            text-align: center;
            margin-bottom: 0.35rem;
        }
        .sp-school-title-fit {
            display: inline-block;
            white-space: nowrap !important;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #0f172a;
            font-weight: 800;
            line-height: 1.2;
            font-size: 1.35rem;
            letter-spacing: -0.2px;
            max-width: 100%;
            transition: font-size 0.05s ease-out;
        }
        .sp-school-title-fit-1 {
            font-size: 1.6rem;
            font-weight: 900;
            color: #0b1329;
        }

        /* School Code (Prominent and Larger, without "குறியீடு:") */
        .sp-school-code {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            font-family: 'Segoe UI', system-ui, -apple-system, monospace;
            letter-spacing: 0.5px;
            margin-bottom: 0.35rem;
        }

        /* Sub-District Line Below School Code */
        .sp-subdist-wrap {
            margin-bottom: 1rem;
        }

        /* 360-Degree 3D Spinning Location Icon */
        .sp-loc-pin-spin {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            perspective: 500px;
            animation: spinLocPin 3s linear infinite;
            margin-right: 4px;
        }
        @keyframes spinLocPin {
            0% {
                transform: rotateY(0deg);
            }
            100% {
                transform: rotateY(360deg);
            }
        }

        /* Search Card */
        .sp-search-card {
            background: #ffffff;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
            padding: 0.85rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        /* School Card (Mobile) */
        .sp-school-card {
            background: #ffffff;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 3px 12px rgba(15, 23, 42, 0.05);
            transition: all 0.2s ease;
            margin-bottom: 0.75rem;
            padding: 1rem;
        }
        .sp-school-card:hover {
            transform: translateX(4px);
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.09);
        }
        .sp-rank-num {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
            background: #f1f5f9;
            color: #334155;
            flex-shrink: 0;
        }
        .sp-pts-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 1.15rem;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.95rem;
            color: #ffffff;
            white-space: nowrap;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .sp-pts-gold {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }
        .sp-pts-silver {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 3px 8px rgba(16, 185, 129, 0.25);
        }
        .sp-pts-bronze {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 3px 8px rgba(16, 185, 129, 0.25);
        }
        .sp-pts-default {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);
        }

        /* Responsive Views */
        .sp-mobile-view { display: block; }
        .sp-desktop-view { display: none; }
        @media (min-width: 992px) {
            .sp-mobile-view { display: none; }
            .sp-desktop-view { display: block; }
        }
    </style>
</head>
<body>

    <?php 
    $currentFest = (int)($festid ?? ($details[0]['fest_id'] ?? 1));
    $festCategoryLabels = [
        1 => 'LP Schools',
        2 => 'UP Schools',
        3 => 'HS Schools',
        4 => 'HSS Schools'
    ];
    $categoryLabel = $festCategoryLabels[$currentFest] ?? (!empty($details[0]['fest_name']) ? esc($details[0]['fest_name']) : 'Schools');
    ?>

    <!-- Top Sticky Navigation -->
    <div class="sp-topbar">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <a href="<?= base_url(); ?>publishresult/resultindex/resultview" class="sp-back-btn">
                        <i class="bi bi-arrow-left"></i>
                        <span class="font-tamil">முடிவுகள் முகப்பு</span>
                    </a>
                    <!-- Only Active Category Label Shown (மற்ற கேட்டகிரி மறைக்கப்பட்டது) -->
                    <span class="sp-section-badge">
                        <i class="bi bi-mortarboard-fill"></i>
                        <span><?= esc($categoryLabel); ?></span>
                    </span>
                </div>
                <div class="d-none d-md-flex align-items-center gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill fw-bold" style="font-size: 0.85rem;">
                        <i class="bi bi-star-fill text-warning me-1"></i>Sub-District School Championship
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <!-- Hero Header has been removed as requested -->

        <?php if (!empty($details)) { ?>
            <!-- Podium Highlights for Top 3 Schools -->
            <div class="row g-3 mb-4 align-items-stretch">
                <!-- Rank 2 -->
                <?php if (isset($details[1])) { ?>
                    <div class="col-12 col-md-4 order-2 order-md-1">
                        <div class="card sp-podium-card rank-2 h-100">
                            <div class="sp-trophy-silver-wrap">
                                <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <linearGradient id="silverCup" x1="16" y1="8" x2="48" y2="40" gradientUnits="userSpaceOnUse">
                                            <stop offset="0%" stop-color="#FFFFFF"/>
                                            <stop offset="35%" stop-color="#E2E8F0"/>
                                            <stop offset="70%" stop-color="#94A3B8"/>
                                            <stop offset="100%" stop-color="#475569"/>
                                        </linearGradient>
                                        <linearGradient id="silverRim" x1="18" y1="8" x2="46" y2="8" gradientUnits="userSpaceOnUse">
                                            <stop offset="0%" stop-color="#F8FAFC"/>
                                            <stop offset="50%" stop-color="#FFFFFF"/>
                                            <stop offset="100%" stop-color="#64748B"/>
                                        </linearGradient>
                                        <linearGradient id="silverHandle" x1="8" y1="12" x2="56" y2="30" gradientUnits="userSpaceOnUse">
                                            <stop offset="0%" stop-color="#F1F5F9"/>
                                            <stop offset="50%" stop-color="#94A3B8"/>
                                            <stop offset="100%" stop-color="#334155"/>
                                        </linearGradient>
                                        <linearGradient id="silverBase" x1="20" y1="44" x2="44" y2="58" gradientUnits="userSpaceOnUse">
                                            <stop offset="0%" stop-color="#CBD5E1"/>
                                            <stop offset="50%" stop-color="#64748B"/>
                                            <stop offset="100%" stop-color="#334155"/>
                                        </linearGradient>
                                    </defs>
                                    <path d="M18 14C11 14 8 20 8 26C8 33 14 36 20 37" stroke="url(#silverHandle)" stroke-width="4" stroke-linecap="round"/>
                                    <path d="M46 14C53 14 56 20 56 26C56 33 50 36 44 37" stroke="url(#silverHandle)" stroke-width="4" stroke-linecap="round"/>
                                    <path d="M18 12H46C46 12 46 29 32 37C18 29 18 12 18 12Z" fill="url(#silverCup)"/>
                                    <ellipse cx="32" cy="12" rx="14" ry="3.5" fill="url(#silverRim)"/>
                                    <path d="M22 15C22 15 23 27 30 33C26 29 25 20 25 15H22Z" fill="#FFFFFF" fill-opacity="0.6"/>
                                    <path d="M28 36H36V46H28V36Z" fill="url(#silverCup)"/>
                                    <ellipse cx="32" cy="46" rx="6" ry="2" fill="url(#silverBase)"/>
                                    <path d="M24 47H40L42 54H22L24 47Z" fill="url(#silverBase)"/>
                                    <rect x="18" y="54" width="28" height="5" rx="2" fill="url(#silverBase)"/>
                                    <text x="32" y="27" font-size="13" font-weight="bold" fill="#334155" text-anchor="middle" font-family="sans-serif">2</text>
                                </svg>
                            </div>
                            <!-- Reserved Slot for Champion Space (Hidden for 2nd Place) -->
                            <div class="sp-champion-slot invisible">
                                <span class="sp-champion-badge">CHAMPION</span>
                            </div>

                            <!-- 2nd Place Button (Same Size) -->
                            <div class="mb-3">
                                <span class="sp-rank-pill sp-rank-pill-2 font-tamil">
                                    <i class="bi bi-award-fill"></i>2-ம் இடம் (2nd Place)
                                </span>
                            </div>
                            <div class="sp-school-title-wrap" title="<?= esc($details[1]['school_name']); ?>">
                                <span class="sp-school-title-fit font-tamil" data-max-size="23"><?= esc($details[1]['school_name']); ?></span>
                            </div>
                            <div class="sp-school-code"><?= esc($details[1]['school_code']); ?></div>
                            <?php if (!empty($details[1]['sub_district_name'])) { ?>
                                <div class="sp-subdist-wrap">
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 font-tamil d-inline-flex align-items-center">
                                        <span class="sp-loc-pin-spin">
                                            <i class="bi bi-geo-alt-fill text-danger fs-6"></i>
                                        </span>
                                        <?= esc($details[1]['sub_district_name']); ?>
                                    </span>
                                </div>
                            <?php } ?>
                            <div class="mt-auto">
                                <span class="sp-pts-badge sp-pts-silver font-tamil"><?= esc($details[1]['spoint']); ?> புள்ளிகள்</span>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- Rank 1 (Champion with 3D Rotating Cup) -->
                <?php if (isset($details[0])) { ?>
                    <div class="col-12 col-md-4 order-1 order-md-2">
                        <div class="card sp-podium-card rank-1 h-100" title="Click for Celebration!">
                            <div class="sp-trophy-3d-scene">
                                <div class="sp-trophy-pedestal-glow"></div>
                                <div class="sp-trophy-3d-cup">
                                    <svg width="74" height="74" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <defs>
                                            <linearGradient id="goldCup" x1="16" y1="8" x2="48" y2="40" gradientUnits="userSpaceOnUse">
                                                <stop offset="0%" stop-color="#FFF3A8"/>
                                                <stop offset="25%" stop-color="#FCD34D"/>
                                                <stop offset="60%" stop-color="#F59E0B"/>
                                                <stop offset="100%" stop-color="#B45309"/>
                                            </linearGradient>
                                            <linearGradient id="goldRim" x1="18" y1="8" x2="46" y2="8" gradientUnits="userSpaceOnUse">
                                                <stop offset="0%" stop-color="#FEF08A"/>
                                                <stop offset="50%" stop-color="#FFFFFF"/>
                                                <stop offset="100%" stop-color="#D97706"/>
                                            </linearGradient>
                                            <linearGradient id="goldHandle" x1="8" y1="12" x2="56" y2="30" gradientUnits="userSpaceOnUse">
                                                <stop offset="0%" stop-color="#FDE68A"/>
                                                <stop offset="50%" stop-color="#F59E0B"/>
                                                <stop offset="100%" stop-color="#92400E"/>
                                            </linearGradient>
                                            <linearGradient id="goldBase" x1="20" y1="44" x2="44" y2="58" gradientUnits="userSpaceOnUse">
                                                <stop offset="0%" stop-color="#FCD34D"/>
                                                <stop offset="50%" stop-color="#D97706"/>
                                                <stop offset="100%" stop-color="#78350F"/>
                                            </linearGradient>
                                        </defs>
                                        <path d="M18 14C11 14 8 20 8 26C8 33 14 36 20 37" stroke="url(#goldHandle)" stroke-width="4" stroke-linecap="round"/>
                                        <path d="M46 14C53 14 56 20 56 26C56 33 50 36 44 37" stroke="url(#goldHandle)" stroke-width="4" stroke-linecap="round"/>
                                        <path d="M18 12H46C46 12 46 29 32 37C18 29 18 12 18 12Z" fill="url(#goldCup)"/>
                                        <ellipse cx="32" cy="12" rx="14" ry="3.5" fill="url(#goldRim)"/>
                                        <path d="M22 15C22 15 23 27 30 33C26 29 25 20 25 15H22Z" fill="#FFFFFF" fill-opacity="0.45"/>
                                        <path d="M28 36H36V46H28V36Z" fill="url(#goldCup)"/>
                                        <ellipse cx="32" cy="46" rx="6" ry="2" fill="url(#goldBase)"/>
                                        <path d="M24 47H40L42 54H22L24 47Z" fill="url(#goldBase)"/>
                                        <rect x="18" y="54" width="28" height="5" rx="2" fill="url(#goldBase)"/>
                                        <path d="M32 20L33.3 23.3L36.8 23.6L34.1 25.9L34.9 29.4L32 27.6L29.1 29.4L29.9 25.9L27.2 23.6L30.7 23.3L32 20Z" fill="#FFFFFF" fill-opacity="0.95"/>
                                    </svg>
                                </div>
                            </div>
                            <!-- Champion Badge Above 1st Place Button -->
                            <div class="sp-champion-slot">
                                <span class="sp-champion-badge">
                                    <i class="bi bi-trophy-fill me-1"></i>CHAMPION
                                </span>
                            </div>

                            <!-- 1st Place Button (Same Size) -->
                            <div class="mb-3">
                                <span class="sp-rank-pill sp-rank-pill-1 font-tamil">
                                    <i class="bi bi-star-fill"></i>முதலிடம் (1st Place)
                                </span>
                            </div>
                            <div class="sp-school-title-wrap" title="<?= esc($details[0]['school_name']); ?>">
                                <span class="sp-school-title-fit sp-school-title-fit-1 font-tamil fw-bolder" data-max-size="26"><?= esc($details[0]['school_name']); ?></span>
                            </div>
                            <div class="sp-school-code" style="font-size: 1.35rem;"><?= esc($details[0]['school_code']); ?></div>
                            <?php if (!empty($details[0]['sub_district_name'])) { ?>
                                <div class="sp-subdist-wrap">
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 font-tamil d-inline-flex align-items-center">
                                        <span class="sp-loc-pin-spin">
                                            <i class="bi bi-geo-alt-fill text-danger fs-6"></i>
                                        </span>
                                        <?= esc($details[0]['sub_district_name']); ?>
                                    </span>
                                </div>
                            <?php } ?>
                            <div class="mt-auto">
                                <span class="sp-pts-badge sp-pts-gold font-tamil fs-6"><?= esc($details[0]['spoint']); ?> புள்ளிகள்</span>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- Rank 3 -->
                <?php if (isset($details[2])) { ?>
                    <div class="col-12 col-md-4 order-3 order-md-3">
                        <div class="card sp-podium-card rank-3 h-100">
                            <div class="sp-trophy-bronze-wrap">
                                <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <linearGradient id="bronzeCup" x1="16" y1="8" x2="48" y2="40" gradientUnits="userSpaceOnUse">
                                            <stop offset="0%" stop-color="#FED7AA"/>
                                            <stop offset="35%" stop-color="#FB923C"/>
                                            <stop offset="70%" stop-color="#C2410C"/>
                                            <stop offset="100%" stop-color="#7C2D12"/>
                                        </linearGradient>
                                        <linearGradient id="bronzeRim" x1="18" y1="8" x2="46" y2="8" gradientUnits="userSpaceOnUse">
                                            <stop offset="0%" stop-color="#FFEDD5"/>
                                            <stop offset="50%" stop-color="#FFFFFF"/>
                                            <stop offset="100%" stop-color="#9A3412"/>
                                        </linearGradient>
                                        <linearGradient id="bronzeHandle" x1="8" y1="12" x2="56" y2="30" gradientUnits="userSpaceOnUse">
                                            <stop offset="0%" stop-color="#FDBA74"/>
                                            <stop offset="50%" stop-color="#EA580C"/>
                                            <stop offset="100%" stop-color="#7C2D12"/>
                                        </linearGradient>
                                        <linearGradient id="bronzeBase" x1="20" y1="44" x2="44" y2="58" gradientUnits="userSpaceOnUse">
                                            <stop offset="0%" stop-color="#FB923C"/>
                                            <stop offset="50%" stop-color="#9A3412"/>
                                            <stop offset="100%" stop-color="#431407"/>
                                        </linearGradient>
                                    </defs>
                                    <path d="M18 14C11 14 8 20 8 26C8 33 14 36 20 37" stroke="url(#bronzeHandle)" stroke-width="4" stroke-linecap="round"/>
                                    <path d="M46 14C53 14 56 20 56 26C56 33 50 36 44 37" stroke="url(#bronzeHandle)" stroke-width="4" stroke-linecap="round"/>
                                    <path d="M18 12H46C46 12 46 29 32 37C18 29 18 12 18 12Z" fill="url(#bronzeCup)"/>
                                    <ellipse cx="32" cy="12" rx="14" ry="3.5" fill="url(#bronzeRim)"/>
                                    <path d="M22 15C22 15 23 27 30 33C26 29 25 20 25 15H22Z" fill="#FFFFFF" fill-opacity="0.5"/>
                                    <path d="M28 36H36V46H28V36Z" fill="url(#bronzeCup)"/>
                                    <ellipse cx="32" cy="46" rx="6" ry="2" fill="url(#bronzeBase)"/>
                                    <path d="M24 47H40L42 54H22L24 47Z" fill="url(#bronzeBase)"/>
                                    <rect x="18" y="54" width="28" height="5" rx="2" fill="url(#bronzeBase)"/>
                                    <text x="32" y="27" font-size="13" font-weight="bold" fill="#7C2D12" text-anchor="middle" font-family="sans-serif">3</text>
                                </svg>
                            </div>
                            <!-- Reserved Slot for Champion Space (Hidden for 3rd Place) -->
                            <div class="sp-champion-slot invisible">
                                <span class="sp-champion-badge">CHAMPION</span>
                            </div>

                            <!-- 3rd Place Button (Same Size) -->
                            <div class="mb-3">
                                <span class="sp-rank-pill sp-rank-pill-3 font-tamil">
                                    <i class="bi bi-award"></i>3-ம் இடம் (3rd Place)
                                </span>
                            </div>
                            <div class="sp-school-title-wrap" title="<?= esc($details[2]['school_name']); ?>">
                                <span class="sp-school-title-fit font-tamil" data-max-size="23"><?= esc($details[2]['school_name']); ?></span>
                            </div>
                            <div class="sp-school-code"><?= esc($details[2]['school_code']); ?></div>
                            <?php if (!empty($details[2]['sub_district_name'])) { ?>
                                <div class="sp-subdist-wrap">
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 font-tamil d-inline-flex align-items-center">
                                        <span class="sp-loc-pin-spin">
                                            <i class="bi bi-geo-alt-fill text-danger fs-6"></i>
                                        </span>
                                        <?= esc($details[2]['sub_district_name']); ?>
                                    </span>
                                </div>
                            <?php } ?>
                            <div class="mt-auto">
                                <span class="sp-pts-badge sp-pts-bronze font-tamil"><?= esc($details[2]['spoint']); ?> புள்ளிகள்</span>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Real-time Filter Search Card -->
            <div class="card sp-search-card">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="sp-filter-input" class="form-control border-start-0 font-tamil" placeholder="பள்ளி பெயர் அல்லது குறியீட்டைத் தட்டச்சு செய்யவும் (Search School)...">
                </div>
            </div>

            <!-- Mobile View (Card List) -->
            <div class="sp-mobile-view" id="sp-mobile-container">
                <?php 
                $rank = 0;
                foreach ($details as $row) { 
                    $rank++;
                    $rankBg = ($rank === 1) ? 'background:#fef08a; color:#78350f;' : (($rank === 2) ? 'background:#e2e8f0; color:#334155;' : (($rank === 3) ? 'background:#fed7aa; color:#7c2d12;' : ''));
                ?>
                    <div class="card sp-school-card sp-item-row" data-search="<?= strtolower(esc($row['school_name'] . ' ' . $row['school_code'] . ' ' . ($row['sub_district_name'] ?? ''))); ?>">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="sp-rank-num" style="<?= $rankBg; ?>"><?= $rank; ?></div>
                                <div>
                                    <h4 class="h6 fw-bold text-dark font-tamil mb-0"><?= esc($row['school_name']); ?></h4>
                                    <div class="small text-muted font-tamil mt-0.5">
                                        <strong class="text-dark font-monospace"><?= esc($row['school_code']); ?></strong>
                                        <?php if (!empty($row['sub_district_name'])) { ?>
                                            &bull; <span class="sp-loc-pin-spin"><i class="bi bi-geo-alt-fill text-danger"></i></span> <?= esc($row['sub_district_name']); ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="sp-pts-badge sp-pts-default">
                                <?= esc($row['spoint']); ?> Pts
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Desktop View (Table) -->
            <div class="sp-desktop-view">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="sp-desktop-table">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center" style="width: 80px;">இடம் (Rank)</th>
                                    <th style="width: 130px;">பள்ளிக் குறியீடு</th>
                                    <th>பள்ளி பெயர் (School Name)</th>
                                    <th>உப-மாவட்டம் (Sub-District)</th>
                                    <th class="text-center" style="width: 140px;">மொத்தப் புள்ளிகள்</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $rank = 0;
                                foreach ($details as $row) { 
                                    $rank++;
                                ?>
                                    <tr class="sp-item-row <?= ($rank <= 3) ? 'table-warning bg-opacity-25' : ''; ?>" data-search="<?= strtolower(esc($row['school_name'] . ' ' . $row['school_code'] . ' ' . ($row['sub_district_name'] ?? ''))); ?>">
                                        <td class="text-center">
                                            <?php if ($rank === 1) { ?>
                                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1.5 fw-bold"><i class="bi bi-trophy-fill me-1"></i>1st</span>
                                            <?php } elseif ($rank === 2) { ?>
                                                <span class="badge bg-secondary rounded-pill px-3 py-1.5 fw-bold"><i class="bi bi-award-fill me-1"></i>2nd</span>
                                            <?php } elseif ($rank === 3) { ?>
                                                <span class="badge bg-dark rounded-pill px-3 py-1.5 fw-bold"><i class="bi bi-award me-1"></i>3rd</span>
                                            <?php } else { ?>
                                                <span class="badge bg-light text-dark border"><?= $rank; ?></span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary font-monospace"><?= esc($row['school_code']); ?></span>
                                        </td>
                                        <td>
                                            <div class="fw-bold font-tamil text-dark"><?= esc($row['school_name']); ?></div>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['sub_district_name'])) { ?>
                                                <span class="badge bg-light text-dark border font-tamil">
                                                    <span class="sp-loc-pin-spin"><i class="bi bi-geo-alt-fill text-danger me-1"></i></span><?= esc($row['sub_district_name']); ?>
                                                </span>
                                            <?php } else { ?>
                                                <span class="text-muted small">-</span>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="sp-pts-badge sp-pts-default"><?= esc($row['spoint']); ?> Pts</span>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php } else { ?>
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4 bg-white">
                <div class="text-muted mb-3">
                    <i class="bi bi-hourglass-split display-3 text-warning"></i>
                </div>
                <h4 class="h5 fw-bold text-dark font-tamil">புள்ளிகள் பட்டியல் இன்னும் தயாராகவில்லை</h4>
                <p class="text-muted mb-0 font-tamil">போட்டிகள் நிறைவடைந்து முடிவுகள் சரிபார்க்கப்பட்டதும் பள்ளி வாரியான புள்ளிகள் இங்கு தோன்றும்.</p>
            </div>
        <?php } ?>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Confetti (Local with CDN fallback) -->
    <script src="<?= base_url(); ?>assets/js/canvas-confetti.min.js"></script>
    <script>
        if (typeof confetti === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"><\/script>');
        }
    </script>

    <script>
    function launchCelebration() {
        if (typeof confetti !== 'function') return;

        // 1. Initial Grand Burst (பலூன் பிளாஷ்ட் / Central Cannon Blast)
        confetti({
            particleCount: 120,
            spread: 85,
            origin: { y: 0.55 },
            colors: ['#f59e0b', '#10b981', '#ef4444', '#3b82f6', '#8b5cf6', '#ffd700', '#ec4899'],
            scalar: 1.15,
            disableForReducedMotion: true
        });

        // 2. Fireworks Blast Sequence (வானவேடிக்கை - continuous celebratory bursts from both sides)
        var duration = 3500;
        var end = Date.now() + duration;
        var colors = ['#f59e0b', '#fbbf24', '#10b981', '#3b82f6', '#ef4444', '#a855f7'];

        (function frame() {
            confetti({
                particleCount: 4,
                angle: 60,
                spread: 55,
                origin: { x: 0, y: 0.65 },
                colors: colors
            });
            confetti({
                particleCount: 4,
                angle: 120,
                spread: 55,
                origin: { x: 1, y: 0.65 },
                colors: colors
            });

            if (Date.now() < end) {
                requestAnimationFrame(frame);
            }
        }());

        // 3. Sky fireworks bursts (வானவேடிக்கை பல வண்ணத் தூவல்கள்)
        var bursts = 0;
        var skyInterval = setInterval(function() {
            bursts++;
            if (bursts > 6) {
                clearInterval(skyInterval);
                return;
            }
            confetti({
                particleCount: 45,
                spread: 360,
                startVelocity: 26,
                ticks: 60,
                origin: { 
                    x: 0.2 + Math.random() * 0.6, 
                    y: 0.15 + Math.random() * 0.35 
                },
                colors: ['#ffd700', '#ff4500', '#00e5ff', '#ff007f', '#76ff03', '#ffffff']
            });
        }, 400);
    }

    $(document).ready(function() {
        // Trigger celebration on every page load
        <?php if (!empty($details)) { ?>
            setTimeout(launchCelebration, 350);
        <?php } ?>

        // Re-launch on clicking 1st place champion card
        $('.sp-podium-card.rank-1').css('cursor', 'pointer').on('click', function() {
            launchCelebration();
        });

        // Quick live filter
        $('#sp-filter-input').on('keyup', function() {
            var val = $(this).val().toLowerCase().trim();
            $('.sp-item-row').each(function() {
                var text = $(this).data('search') || '';
                if (text.indexOf(val) !== -1 || val === '') {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Dynamic single-line font resizing for podium school names
        autoResizeSchoolTitles();
    });

    // Auto-resize school titles so they stay on a single line and fit card width
    function autoResizeSchoolTitles() {
        var elements = document.querySelectorAll('.sp-school-title-fit');
        elements.forEach(function(el) {
            var parent = el.closest('.sp-school-title-wrap');
            if (!parent) return;

            var isRank1 = el.classList.contains('sp-school-title-fit-1');
            var maxFs = parseFloat(el.getAttribute('data-max-size')) || (isRank1 ? 26 : 23);
            var minFs = 11;

            // Reset to max font size first to measure scrollWidth
            el.style.setProperty('font-size', maxFs + 'px', 'important');

            var availWidth = parent.clientWidth - 8;
            if (availWidth <= 0) return;

            var currentWidth = el.scrollWidth;
            if (currentWidth > availWidth) {
                var calculatedFs = (availWidth / currentWidth) * maxFs * 0.98;
                calculatedFs = Math.max(minFs, Math.min(maxFs, Math.floor(calculatedFs * 10) / 10));
                el.style.setProperty('font-size', calculatedFs + 'px', 'important');

                var safety = 0;
                while (el.scrollWidth > availWidth && calculatedFs > minFs && safety < 20) {
                    calculatedFs -= 0.5;
                    el.style.setProperty('font-size', calculatedFs + 'px', 'important');
                    safety++;
                }
            }
        });
    }

    window.addEventListener('load', autoResizeSchoolTitles);
    window.addEventListener('resize', autoResizeSchoolTitles);
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(autoResizeSchoolTitles);
    }
    if (window.ResizeObserver) {
        var ro = new ResizeObserver(function() {
            autoResizeSchoolTitles();
        });
        document.querySelectorAll('.sp-school-title-wrap').forEach(function(wrap) {
            ro.observe(wrap);
        });
    }
    </script>
</body>
</html>