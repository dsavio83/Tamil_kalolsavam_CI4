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

/* Mobile-First Status Styles */
.fs-topbar {
    background: #ffffff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    margin-bottom: 1.5rem;
}
.fs-hero {
    background: linear-gradient(135deg, #0f766e 0%, #0d9488 50%, #06b6d4 100%);
    color: #ffffff;
    border-radius: 1.25rem;
    padding: 2rem 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 10px 25px -5px rgba(13, 148, 136, 0.3);
    position: relative;
    overflow: hidden;
}
.fs-hero::after {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 180px;
    height: 180px;
    background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
}
.fs-card {
    border: none;
    border-radius: 1.15rem;
    background: #ffffff;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.06);
    transition: all 0.2s ease;
    margin-bottom: 1rem;
    padding: 1.25rem;
}
.fs-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.1);
}
.fs-back-btn {
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
.fs-back-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.fs-prog-bar {
    height: 8px;
    border-radius: 4px;
    background: #e2e8f0;
    overflow: hidden;
    margin-top: 0.5rem;
}
.fs-prog-fill {
    height: 100%;
    border-radius: 4px;
    background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    transition: width 0.6s ease;
}

/* Mobile cards vs Desktop table */
.fs-mobile-view { display: block; }
.fs-desktop-view { display: none; }
@media (min-width: 992px) {
    .fs-mobile-view { display: none; }
    .fs-desktop-view { display: block; }
}
</style>

<div class="container-fluid px-0 px-md-3">
    <!-- Top Bar with Back Button -->
    <div class="fs-topbar d-flex align-items-center justify-content-between">
        <a href="<?= base_url(); ?>district/report/resultindex/resultview" class="fs-back-btn">
            <i class="bi bi-arrow-left"></i>
            <span class="d-none d-sm-inline font-tamil">முடிவுகள் முகப்பு (Back to Results)</span>
            <span class="d-sm-none">Back to Results</span>
        </a>
        <span class="badge bg-success bg-opacity-10 text-success px-3 py-1.5 rounded-pill fw-bold fs-7">
            <i class="bi bi-speedometer2 me-1"></i>District Festival Monitor
        </span>
    </div>

    <!-- Hero Header -->
    <div class="fs-hero">
        <div class="row align-items-center">
            <div class="col-12 col-md-9">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill text-white fs-7 mb-2" style="background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.25);">
                    <i class="bi bi-pie-chart-fill"></i>
                    <span>நிகழ்வு நிலை &bull; Event Monitoring</span>
                </div>
                <h1 class="h3 fw-bold mb-1">
                    கலைவிழா போட்டிகள் நிலை (Status of Kalaivizha)
                </h1>
                <p class="mb-0 text-white-75 font-tamil">
                    மாவட்ட அளவிலான போட்டிகளின் முழுமை நிலை மற்றும் முடிவுகள் கண்காணிப்பு
                </p>
            </div>
            <div class="col-md-3 d-none d-md-block text-end">
                <i class="bi bi-graph-up-arrow display-3 text-white-50"></i>
            </div>
        </div>
    </div>

    <?php if (count($details1) > 0) { ?>
        <?php echo form_open('', array('id' => 'festdet', 'name' => 'festdet', 'target' => '_blank')); ?>
        <input type="hidden" name="hidfestid" id="hidfestid" value="" />

        <!-- Mobile View (Card List) -->
        <div class="fs-mobile-view">
            <?php 
            foreach ($details1 as $value) {
                if ((int)$value['fest_id'] === 1) continue; // Bar LP in District

                $finished_item = 0;
                foreach ($details2 as $d2) {
                    if ($value['fest_id'] == $d2['fest_id']) {
                        $finished_item = $d2['pcode'];
                        break;
                    }
                }
                $tot = (int)$value['cnt'];
                $remind = max(0, $tot - (int)$finished_item);
                $pct = ($tot > 0) ? round(($finished_item / $tot) * 100) : 0;
            ?>
                <div class="card fs-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h4 class="h6 fw-bold mb-0 text-dark"><?= htmlspecialchars($value['fest_name']); ?></h4>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 fw-bold fs-7">
                            <?= $pct; ?>% முடிந்தது
                        </span>
                    </div>

                    <div class="fs-prog-bar mb-3">
                        <div class="fs-prog-fill" style="width: <?= $pct; ?>%;"></div>
                    </div>

                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <div class="small text-muted font-tamil">மொத்தப் போட்டி</div>
                                <div class="fw-bold fs-6">
                                    <?php if ($tot != 0) { ?>
                                        <a href="javascript:void(0)" onclick="clicktoviewfestdet_district('<?= $value['fest_id']; ?>')" class="text-primary text-decoration-none">
                                            <?= $tot; ?>
                                        </a>
                                    <?php } else { echo 0; } ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-success bg-opacity-10">
                                <div class="small text-success font-tamil">முடிந்தது</div>
                                <div class="fw-bold fs-6 text-success">
                                    <?php if ($finished_item != 0) { ?>
                                        <a href="javascript:void(0)" onclick="finisheditemdet_district('<?= $value['fest_id']; ?>')" class="text-success text-decoration-none">
                                            <?= $finished_item; ?>
                                        </a>
                                    <?php } else { echo 0; } ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-danger bg-opacity-10">
                                <div class="small text-danger font-tamil">மீதம்</div>
                                <div class="fw-bold fs-6 text-danger">
                                    <?php if ($remind != 0) { ?>
                                        <a href="javascript:void(0)" onclick="remainderitemdet_district('<?= $value['fest_id']; ?>')" class="text-danger text-decoration-none">
                                            <?= $remind; ?>
                                        </a>
                                    <?php } else { echo 0; } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- Desktop View (Table) -->
        <div class="fs-desktop-view">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>கலைவிழா பிரிவு (Festival Category)</th>
                                <th class="text-center" style="width: 160px;">மொத்தப் போட்டிகள்</th>
                                <th class="text-center" style="width: 160px;">முடிவடைந்தவை</th>
                                <th class="text-center" style="width: 160px;">முடிவடையாதவை</th>
                                <th style="width: 200px;">முழுமை சதவீதம்</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach ($details1 as $value) {
                                if ((int)$value['fest_id'] === 1) continue; // Bar LP in District

                                $finished_item = 0;
                                foreach ($details2 as $d2) {
                                    if ($value['fest_id'] == $d2['fest_id']) {
                                        $finished_item = $d2['pcode'];
                                        break;
                                    }
                                }
                                $tot = (int)$value['cnt'];
                                $remind = max(0, $tot - (int)$finished_item);
                                $pct = ($tot > 0) ? round(($finished_item / $tot) * 100) : 0;
                            ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($value['fest_name']); ?></div>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($tot != 0) { ?>
                                            <a href="javascript:void(0)" onclick="clicktoviewfestdet_district('<?= $value['fest_id']; ?>')" class="badge bg-primary text-white rounded-pill px-3 py-1.5 fs-7 text-decoration-none">
                                                <?= $tot; ?> போட்டிகள்
                                            </a>
                                        <?php } else { echo '<span class="badge bg-light text-muted">0</span>'; } ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($finished_item != 0) { ?>
                                            <a href="javascript:void(0)" onclick="finisheditemdet_district('<?= $value['fest_id']; ?>')" class="badge bg-success text-white rounded-pill px-3 py-1.5 fs-7 text-decoration-none">
                                                <?= $finished_item; ?> நிறைவு
                                            </a>
                                        <?php } else { echo '<span class="badge bg-light text-muted">0</span>'; } ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($remind != 0) { ?>
                                            <a href="javascript:void(0)" onclick="remainderitemdet_district('<?= $value['fest_id']; ?>')" class="badge bg-danger text-white rounded-pill px-3 py-1.5 fs-7 text-decoration-none">
                                                <?= $remind; ?> மீதம்
                                            </a>
                                        <?php } else { echo '<span class="badge bg-light text-muted">0</span>'; } ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="fs-prog-bar flex-grow-1 my-0">
                                                <div class="fs-prog-fill" style="width: <?= $pct; ?>%;"></div>
                                            </div>
                                            <span class="small fw-bold text-muted"><?= $pct; ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    <?php } else { ?>
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4 bg-white">
            <div class="text-muted mb-3">
                <i class="bi bi-calendar-x display-3 text-warning"></i>
            </div>
            <h4 class="h5 fw-bold text-dark font-tamil">கலைவிழா இன்னும் தொடங்கப்படவில்லை</h4>
            <p class="text-muted mb-0 font-tamil">போட்டிகள் திட்டமிடப்பட்டு தொடங்கப்பட்டதும் அவற்றின் நிலை இங்கு நேரலையாகக் காட்டப்படும்.</p>
        </div>
    <?php } ?>
</div>

<script>
function clicktoviewfestdet_district(festid) {
    var hid = document.getElementById('hidfestid');
    var form = document.getElementById('festdet');
    if (hid) hid.value = festid;
    if (form) {
        form.action = '<?= base_url(); ?>district/report/resultindex/festival_allitem';
        form.submit();
    }
}

function finisheditemdet_district(festid) {
    var hid = document.getElementById('hidfestid');
    var form = document.getElementById('festdet');
    if (hid) hid.value = festid;
    if (form) {
        form.action = '<?= base_url(); ?>district/report/resultindex/finished_item';
        form.submit();
    }
}

function remainderitemdet_district(festid) {
    var hid = document.getElementById('hidfestid');
    var form = document.getElementById('festdet');
    if (hid) hid.value = festid;
    if (form) {
        form.action = '<?= base_url(); ?>district/report/resultindex/incomplete_item';
        form.submit();
    }
}
</script>