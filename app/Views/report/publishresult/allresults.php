<!DOCTYPE html>
<html lang="ta">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>அனைத்து முடிவுகள் (All Results) - Tamil Kalaivizha</title>
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
            font-size: 14px !important;
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #1e293b;
            min-height: 100vh;
        }

        /* Top Sticky Bar */
        .ar-topbar {
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 0.75rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        /* Hero Header */
        .ar-hero {
            background: linear-gradient(135deg, #0f766e 0%, #115e59 50%, #134e4a 100%);
            color: #ffffff;
            border-radius: 1.25rem;
            padding: 2rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.3);
            position: relative;
            overflow: hidden;
        }
        .ar-hero::after {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
        }

        /* Filter Controls */
        .ar-filter-card {
            background: #ffffff;
            border-radius: 1rem;
            border: none;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.06);
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        /* Result Cards (Mobile) */
        .ar-result-card {
            background: #ffffff;
            border: none;
            border-radius: 1.15rem;
            box-shadow: 0 3px 14px rgba(15, 23, 42, 0.06);
            transition: all 0.2s ease;
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
            padding: 1.25rem;
        }
        .ar-result-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.1);
        }

        .ar-rank-badge {
            padding: 0.3rem 0.75rem;
            border-radius: 2rem;
            font-weight: 700;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .ar-rank-1 { background: linear-gradient(135deg, #fef08a 0%, #f59e0b 100%); color: #78350f; }
        .ar-rank-2 { background: linear-gradient(135deg, #f1f5f9 0%, #94a3b8 100%); color: #334155; }
        .ar-rank-3 { background: linear-gradient(135deg, #ffedd5 0%, #d97706 100%); color: #7c2d12; }
        .ar-rank-def { background: #e2e8f0; color: #475569; }

        .ar-avatar {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 2px solid #ffffff;
            box-shadow: 0 3px 8px rgba(0,0,0,0.12);
        }

        .ar-back-btn {
            background: #f8fafc;
            color: #334155;
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            padding: 0.5rem 1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }
        .ar-back-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .ar-cat-badge {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 0.25rem 0.6rem;
            border-radius: 0.5rem;
        }

        /* Responsive Views */
        .ar-mobile-view { display: block; }
        .ar-desktop-view { display: none; }
        @media (min-width: 992px) {
            .ar-mobile-view { display: none; }
            .ar-desktop-view { display: block; }
        }
    </style>
</head>
<body>

    <!-- Top Sticky Navigation -->
    <div class="ar-topbar">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <a href="<?= base_url(); ?>publishresult/resultindex/resultview" class="ar-back-btn">
                    <i class="bi bi-arrow-left"></i>
                    <span class="d-none d-sm-inline font-tamil">முடிவுகள் முகப்பு</span>
                    <span class="d-sm-none">Back</span>
                </a>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-dark bg-opacity-10 text-dark px-3 py-1.5 rounded-pill fw-bold fs-7">
                        <i class="bi bi-collection-fill me-1"></i>Sub-District Level &bull; அனைத்து முடிவுகள்
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <!-- Hero Header -->
        <div class="ar-hero">
            <div class="row align-items-center">
                <div class="col-12 col-md-9">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill text-white fs-7 mb-2" style="background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.25);">
                        <i class="bi bi-trophy-fill text-warning"></i>
                        <span>ஒருங்கிணைந்த முடிவுகள் &bull; Master Result Sheet</span>
                    </div>
                    <h1 class="h3 fw-bold mb-1">
                        அனைத்து போட்டி முடிவுகள் (All Declared Results)
                    </h1>
                    <p class="mb-0 text-white-75 font-tamil">
                        அனைத்துப் பிரிவுகளிலும் வெற்றி பெற்ற மற்றும் கிரேடு பெற்ற மாணவ-மாணவியரின் முழுமையான பட்டியல்
                    </p>
                </div>
                <div class="col-md-3 d-none d-md-block text-end">
                    <i class="bi bi-card-checklist display-3 text-white-50"></i>
                </div>
            </div>
        </div>

        <?php if (!empty($details)) { ?>
            <!-- Filter & Search Controls -->
            <div class="card ar-filter-card">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="ar-search-input" class="form-control border-start-0 font-tamil" placeholder="மாணவர் பெயர், பள்ளி, அல்லது போட்டி எண்...">
                        </div>
                    </div>
                    <div class="col-12 col-md-7 text-md-end">
                        <div class="btn-group w-100 w-md-auto flex-wrap" role="group" id="ar-fest-filter">
                            <button type="button" class="btn btn-outline-dark btn-sm active" data-fest="all">அனைத்தும் (All)</button>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-fest="2">UP</button>
                            <button type="button" class="btn btn-outline-indigo btn-sm" data-fest="3">HS</button>
                            <button type="button" class="btn btn-outline-warning btn-sm" data-fest="1">LP</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile View (Card List) -->
            <div class="ar-mobile-view" id="ar-mobile-container">
                <?php 
                foreach ($details as $row) { 
                    if (($row['is_publish'] ?? 'Y') === 'N') continue;
                    $rank = (int)($row['rank'] ?? 0);
                    $rankClass = ($rank === 1) ? 'ar-rank-1' : (($rank === 2) ? 'ar-rank-2' : (($rank === 3) ? 'ar-rank-3' : 'ar-rank-def'));
                    $rankText = ($rank === 1) ? '1st' : (($rank === 2) ? '2nd' : (($rank === 3) ? '3rd' : ($rank > 0 ? 'Rank ' . $rank : 'Participant')));

                    // Photo resolution
                    $reg = $row['admn_no'] ?? '';
                    $sch = $row['school_code'] ?? '';
                    $sub_dist = $row['sub_district_code'] ?? '';
                    $p_file = $row['photo_filename'] ?? '';
                    $pic_url = base_url('photos/img_user_nophoto.png');

                    if (!empty($p_file)) {
                        if (!empty($sub_dist) && !empty($sch) && file_exists(ROOTPATH . 'photos/' . $sub_dist . '/' . $sch . '/' . $p_file)) {
                            $pic_url = base_url('photos/' . $sub_dist . '/' . $sch . '/' . $p_file);
                        } elseif (file_exists(ROOTPATH . 'uploads/photos/' . $p_file)) {
                            $pic_url = base_url('uploads/photos/' . $p_file);
                        }
                    } elseif (!empty($Photo['pic'][$reg])) {
                        $pic_url = $Photo['pic'][$reg];
                    }
                    if ($pic_url === base_url('photos/img_user_nophoto.png') && !empty($sub_dist) && !empty($sch)) {
                        foreach (['jpg', 'jpeg', 'png'] as $ext) {
                            $cand = 'photos/' . $sub_dist . '/' . $sch . '/' . $sch . '_' . $reg . '.' . $ext;
                            if (file_exists(ROOTPATH . $cand)) {
                                $pic_url = base_url($cand);
                                break;
                            }
                        }
                    }

                    $searchData = strtolower(esc($row['participant_name'] . ' ' . $row['participant_id'] . ' ' . $row['school_name'] . ' ' . $row['school_code'] . ' ' . $row['item_name'] . ' ' . $row['item_code']));
                    $fid = (int)($row['fest_id'] ?? 0);
                ?>
                    <div class="card ar-result-card ar-item-row" data-search="<?= $searchData; ?>" data-fest="<?= $fid; ?>">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-dark bg-opacity-10 text-dark ar-cat-badge">
                                <?= esc($row['fest_name'] ?? ''); ?>
                            </span>
                            <?php if ($rank > 0) { ?>
                                <span class="ar-rank-badge <?= $rankClass; ?>">
                                    <i class="bi bi-trophy-fill"></i> <?= $rankText; ?>
                                </span>
                            <?php } ?>
                        </div>

                        <div class="d-flex align-items-start gap-3">
                            <img src="<?= $pic_url; ?>" class="ar-avatar" alt="Photo" onerror="this.src='<?= base_url('photos/img_user_nophoto.png'); ?>'">
                            <div class="flex-grow-1">
                                <h4 class="h6 fw-bold text-dark mb-0.5"><?= esc($row['participant_name']); ?></h4>
                                <div class="small text-muted mb-1 font-tamil">
                                    போட்டி: <strong><?= esc($row['item_name']); ?></strong> (<?= esc($row['item_code']); ?>)
                                </div>
                                <div class="small text-secondary mb-1">
                                    பதிவு: <strong><?= esc($row['participant_id']); ?></strong> &bull; 
                                    வகுப்பு: <strong><?= esc($row['class']); ?></strong>
                                    <?php if (!empty($row['code_no'])) { ?>
                                        &bull; செஸ்ட்: <strong><?= esc($row['code_no']); ?></strong>
                                    <?php } ?>
                                </div>
                                <div class="small text-dark font-tamil p-2 rounded bg-light">
                                    <i class="bi bi-building me-1 text-primary"></i><?= esc($row['school_name']); ?> (<?= esc($row['school_code']); ?>)
                                    <?php if (!empty($row['sub_district_name'])) { ?>
                                        <div class="mt-0.5 text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i><?= esc($row['sub_district_name']); ?></div>
                                    <?php } ?>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-2">
                                    <div>
                                        <?php
                                        $gr = strtoupper($row['grade'] ?? '');
                                        if ($gr === 'A') echo '<span class="badge bg-success rounded-pill px-2.5 py-1">Grade A</span>';
                                        elseif ($gr === 'B') echo '<span class="badge bg-warning text-dark rounded-pill px-2.5 py-1">Grade B</span>';
                                        elseif ($gr === 'C') echo '<span class="badge bg-info text-dark rounded-pill px-2.5 py-1">Grade C</span>';
                                        ?>
                                    </div>
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2.5 py-1">
                                        <?= esc($row['point']); ?> Pts
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Desktop View (Table) -->
            <div class="ar-desktop-view">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="ar-desktop-table">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center" style="width: 70px;">பரிசு</th>
                                    <th class="text-center" style="width: 60px;">படம்</th>
                                    <th>கலைவிழா & போட்டி</th>
                                    <th>பதிவு எண் & பெயர்</th>
                                    <th>வகுப்பு</th>
                                    <th>செஸ்ட் எண்</th>
                                    <th>பள்ளி & உப-மாவட்டம்</th>
                                    <th class="text-center">தரம்</th>
                                    <th class="text-center">புள்ளிகள்</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach ($details as $row) { 
                                    if (($row['is_publish'] ?? 'Y') === 'N') continue;
                                    $rank = (int)($row['rank'] ?? 0);

                                    // Photo resolution
                                    $reg = $row['admn_no'] ?? '';
                                    $sch = $row['school_code'] ?? '';
                                    $sub_dist = $row['sub_district_code'] ?? '';
                                    $p_file = $row['photo_filename'] ?? '';
                                    $pic_url = base_url('photos/img_user_nophoto.png');

                                    if (!empty($p_file)) {
                                        if (!empty($sub_dist) && !empty($sch) && file_exists(ROOTPATH . 'photos/' . $sub_dist . '/' . $sch . '/' . $p_file)) {
                                            $pic_url = base_url('photos/' . $sub_dist . '/' . $sch . '/' . $p_file);
                                        } elseif (file_exists(ROOTPATH . 'uploads/photos/' . $p_file)) {
                                            $pic_url = base_url('uploads/photos/' . $p_file);
                                        }
                                    } elseif (!empty($Photo['pic'][$reg])) {
                                        $pic_url = $Photo['pic'][$reg];
                                    }
                                    if ($pic_url === base_url('photos/img_user_nophoto.png') && !empty($sub_dist) && !empty($sch)) {
                                        foreach (['jpg', 'jpeg', 'png'] as $ext) {
                                            $cand = 'photos/' . $sub_dist . '/' . $sch . '/' . $sch . '_' . $reg . '.' . $ext;
                                            if (file_exists(ROOTPATH . $cand)) {
                                                $pic_url = base_url($cand);
                                                break;
                                            }
                                        }
                                    }

                                    $searchData = strtolower(esc($row['participant_name'] . ' ' . $row['participant_id'] . ' ' . $row['school_name'] . ' ' . $row['school_code'] . ' ' . $row['item_name'] . ' ' . $row['item_code']));
                                    $fid = (int)($row['fest_id'] ?? 0);
                                ?>
                                    <tr class="ar-item-row" data-search="<?= $searchData; ?>" data-fest="<?= $fid; ?>">
                                        <td class="text-center">
                                            <?php if ($rank === 1) { ?>
                                                <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-trophy-fill me-1"></i>1st</span>
                                            <?php } elseif ($rank === 2) { ?>
                                                <span class="badge bg-secondary rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-award-fill me-1"></i>2nd</span>
                                            <?php } elseif ($rank === 3) { ?>
                                                <span class="badge bg-dark rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-award me-1"></i>3rd</span>
                                            <?php } else { ?>
                                                <span class="text-muted small">-</span>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <img src="<?= $pic_url; ?>" class="rounded-circle shadow-sm" style="width: 44px; height: 44px; object-fit: cover;" alt="Photo" onerror="this.src='<?= base_url('photos/img_user_nophoto.png'); ?>'">
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border small mb-1"><?= esc($row['fest_name']); ?></span>
                                            <div class="fw-bold font-tamil text-dark"><?= esc($row['item_name']); ?> (<?= esc($row['item_code']); ?>)</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= esc($row['participant_name']); ?></div>
                                            <small class="text-muted">Reg: <?= esc($row['participant_id']); ?></small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border"><?= esc($row['class']); ?></span></td>
                                        <td><strong><?= esc($row['code_no']); ?></strong></td>
                                        <td>
                                            <div class="font-tamil fw-semibold text-dark"><?= esc($row['school_name']); ?></div>
                                            <small class="text-muted font-tamil">
                                                <?= esc($row['school_code']); ?>
                                                <?php if (!empty($row['sub_district_name'])) { ?>
                                                    &bull; <i class="bi bi-geo-alt-fill text-danger"></i><?= esc($row['sub_district_name']); ?>
                                                <?php } ?>
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $gr = strtoupper($row['grade'] ?? '');
                                            if ($gr === 'A') echo '<span class="badge bg-success rounded-pill px-2.5 py-1">Grade A</span>';
                                            elseif ($gr === 'B') echo '<span class="badge bg-warning text-dark rounded-pill px-2.5 py-1">Grade B</span>';
                                            elseif ($gr === 'C') echo '<span class="badge bg-info text-dark rounded-pill px-2.5 py-1">Grade C</span>';
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <strong class="text-success"><?= esc($row['point']); ?> Pts</strong>
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
                <h4 class="h5 fw-bold text-dark font-tamil">முடிவுகள் இன்னும் வெளியிடப்படவில்லை</h4>
                <p class="text-muted mb-0 font-tamil">போட்டிகள் நிறைவடைந்து மதிப்பீட்டு முடிவுகள் உறுதிசெய்யப்பட்டதும் இங்கு காண்பிக்கப்படும்.</p>
            </div>
        <?php } ?>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
    $(document).ready(function() {
        var activeFest = 'all';

        function applyFilter() {
            var search = $('#ar-search-input').val().toLowerCase().trim();
            $('.ar-item-row').each(function() {
                var itemText = $(this).data('search') || '';
                var itemFest = $(this).data('fest') + '';
                var matchesSearch = (search === '' || itemText.indexOf(search) !== -1);
                var matchesFest = (activeFest === 'all' || itemFest === activeFest);
                if (matchesSearch && matchesFest) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        $('#ar-search-input').on('keyup', applyFilter);

        $('#ar-fest-filter button').on('click', function() {
            $('#ar-fest-filter button').removeClass('active');
            $(this).addClass('active');
            activeFest = $(this).data('fest') + '';
            applyFilter();
        });
    });
    </script>
</body>
</html>