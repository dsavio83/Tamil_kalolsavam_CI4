<!DOCTYPE html>
<html lang="ta">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>வெற்றி முடிவுகள் (Competition Results) - Tamil Kalaivizha</title>
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
        .rd-topbar {
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 0.75rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        /* Hero Banner */
        .rd-hero {
            background: linear-gradient(135deg, #0f766e 0%, #0d9488 60%, #06b6d4 100%);
            color: #ffffff;
            border-radius: 1.25rem;
            padding: 2rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(13, 148, 136, 0.35);
            position: relative;
            overflow: hidden;
        }
        .rd-hero::after {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
        }

        /* Selector Box */
        .rd-select-card {
            background: #ffffff;
            border-radius: 1.25rem;
            border: none;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.06);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .rd-select {
            height: 52px;
            border-radius: 0.85rem;
            font-size: 1rem;
            border: 2px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .rd-select:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.15);
        }

        /* Results Display Container */
        #results-container {
            min-height: 250px;
            transition: all 0.3s ease;
        }
        .rd-placeholder-card {
            background: #ffffff;
            border-radius: 1.25rem;
            border: 2px dashed #cbd5e1;
            padding: 3.5rem 1.5rem;
            text-align: center;
        }

        /* Back Button */
        .rd-back-btn {
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
        .rd-back-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        @media (max-width: 576px) {
            .rd-hero {
                padding: 1.5rem 1rem;
                border-radius: 1rem;
            }
            .rd-select-card {
                padding: 1.25rem 1rem;
                border-radius: 1rem;
            }
        }
    </style>
</head>
<body>

    <!-- Top Sticky Bar -->
    <div class="rd-topbar">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <a href="<?= base_url(); ?>publishresult/resultindex/resultview" class="rd-back-btn">
                    <i class="bi bi-arrow-left"></i>
                    <span class="d-none d-sm-inline font-tamil">முடிவுகள் முகப்பு</span>
                    <span class="d-sm-none">Back</span>
                </a>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-teal bg-opacity-10 text-success px-3 py-1.5 rounded-pill fw-bold fs-7">
                        <i class="bi bi-shield-check me-1"></i>Sub-District Level Results
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <!-- Hero Header (Mobile-First) -->
        <div class="rd-hero">
            <div class="row align-items-center">
                <div class="col-12 col-md-9">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill text-white fs-7 mb-2" style="background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.25);">
                        <i class="bi bi-trophy-fill text-warning"></i>
                        <span>வெற்றி முடிவுகள் அறிவிப்பு &bull; Results Portal</span>
                    </div>
                    <h1 class="h3 fw-bold mb-1 rd-main-title">
                        போட்டி முடிவுகள் (Competition Results)
                    </h1>
                    <p class="mb-0 text-white-50 font-tamil">
                        போட்டியைத் தேர்வு செய்து வெற்றியாளர்கள் பட்டியல் மற்றும் மதிப்பெண்களைக் காண்க
                    </p>
                </div>
                <div class="col-md-3 d-none d-md-block text-end">
                    <i class="bi bi-trophy display-3 text-white-50"></i>
                </div>
            </div>
        </div>

        <!-- Item Selector Card -->
        <div class="card rd-select-card">
            <label for="itemSelect" class="form-label fw-bold text-dark d-flex align-items-center gap-2 mb-2 font-tamil">
                <i class="bi bi-funnel-fill text-success"></i>
                <span>போட்டியைத் தேர்ந்தெடுக்கவும் (Select Competition Item):</span>
            </label>
            <div class="position-relative">
                <select class="form-select rd-select font-tamil" id="itemSelect" name="itemSelect">
                    <option value="">-- போட்டியைத் தேர்வு செய்யவும் (Select Item) --</option>
                    <?php if (!empty($items)) { ?>
                        <?php foreach ($items as $item) { ?>
                            <option value="<?php echo esc($item['item_code']); ?>" data-name="<?php echo esc($item['item_name']); ?>" <?php echo (!empty($item['has_result'])) ? 'data-has-result="1"' : ''; ?>>
                                [<?php echo esc($item['item_code']); ?>] <?php echo esc($item['item_name']); ?><?php if (!empty($item['has_result'])) { echo ' ✓ (முடிவு அறிவிக்கப்பட்டது - Result Declared)'; } ?>
                            </option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
            <div class="form-text font-tamil text-muted mt-2">
                <i class="bi bi-info-circle me-1"></i>போட்டியைத் தேர்ந்தெடுத்தவுடன் வெற்றியாளர்கள் விவரம் தானாகவே திரையில் தோன்றும்.
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="rd-loading" class="text-center py-5" style="display: none;">
            <div class="spinner-border text-success" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h5 class="mt-3 fw-bold text-muted font-tamil">முடிவுகள் ஏற்றப்படுகின்றன...</h5>
        </div>

        <!-- Results Display Container -->
        <div id="results-container">
            <div class="rd-placeholder-card">
                <div class="text-muted mb-3">
                    <i class="bi bi-trophy display-3 text-warning opacity-75"></i>
                </div>
                <h4 class="h5 fw-bold text-dark font-tamil">போட்டியைத் தேர்ந்தெடுக்கவும்</h4>
                <p class="text-muted mb-0 font-tamil">மேலே உள்ள பட்டியலிலிருந்து ஒரு போட்டியைத் தேர்ந்தெடுத்து முடிவுகளைக் காணவும்.</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Canvas Confetti -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>

    <script>
    $(document).ready(function() {
        var festid = '<?php echo esc($festid); ?>';

        $('#itemSelect').on('change', function() {
            var itemid = $(this).val();
            var itemName = $(this).find('option:selected').data('name') || '';

            if (!itemid) {
                $('#results-container').html(`
                    <div class="rd-placeholder-card">
                        <div class="text-muted mb-3">
                            <i class="bi bi-trophy display-3 text-warning opacity-75"></i>
                        </div>
                        <h4 class="h5 fw-bold text-dark font-tamil">போட்டியைத் தேர்ந்தெடுக்கவும்</h4>
                        <p class="text-muted mb-0 font-tamil">மேலே உள்ள பட்டியலிலிருந்து ஒரு போட்டியைத் தேர்ந்தெடுத்து முடிவுகளைக் காணவும்.</p>
                    </div>
                `);
                $('.rd-main-title').html('போட்டி முடிவுகள் (Competition Results)');
                return;
            }

            // Show loading
            $('#results-container').hide();
            $('#rd-loading').show();

            $.ajax({
                url: '<?php echo base_url(); ?>publishresult/resultindex/get_results_by_item',
                type: 'POST',
                data: { festid: festid, itemid: itemid },
                success: function(response) {
                    $('#rd-loading').hide();
                    $('#results-container').html(response).fadeIn(300);

                    // Update title
                    if (itemName) {
                        $('.rd-main-title').html('<i class="bi bi-trophy-fill text-warning me-2"></i>' + itemName + ' [' + itemid + ']');
                    }

                    // Trigger celebratory confetti
                    try {
                        confetti({
                            particleCount: 90,
                            spread: 70,
                            origin: { y: 0.6 }
                        });
                    } catch (e) {}
                },
                error: function() {
                    $('#rd-loading').hide();
                    $('#results-container').html(`
                        <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4 bg-light">
                            <div class="text-danger mb-3">
                                <i class="bi bi-exclamation-triangle-fill display-4"></i>
                            </div>
                            <h4 class="h5 fw-bold text-danger font-tamil">முடிவுகளை ஏற்றுவதில் பிழை ஏற்பட்டது</h4>
                            <p class="text-muted mb-0 font-tamil">தயவுசெய்து சிறிது நேரம் கழித்து மீண்டும் முயற்சிக்கவும்.</p>
                        </div>
                    `).fadeIn(300);
                }
            });
        });

        // Auto-select declared item on load if available, or by URL parameter
        var urlParams = new URLSearchParams(window.location.search);
        var paramItem = urlParams.get('item_code') || urlParams.get('itemid');
        if (paramItem && $('#itemSelect option[value="' + paramItem + '"]').length) {
            $('#itemSelect').val(paramItem).trigger('change');
        } else {
            var firstDeclared = $('#itemSelect option[data-has-result="1"]').first().val();
            if (firstDeclared) {
                $('#itemSelect').val(firstDeclared).trigger('change');
            }
        }
    });
    </script>
</body>
</html>