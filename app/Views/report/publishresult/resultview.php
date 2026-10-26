<style>
@font-face {
    font-family: 'TAU-Pallai';
    src: url('<?php echo base_url(); ?>assets/fonts/TAU-Paalai.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;
}
@font-face {
    font-family: 'TAU-Pallai';
    src: url('<?php echo base_url(); ?>assets/fonts/TAU-Paalai Bold.ttf') format('truetype');
    font-weight: bold;
    font-style: normal;
}
.font-tamil {
    font-family: 'TAU-Pallai', 'Mukta Malar', 'Noto Sans Tamil', sans-serif !important;
    font-size: 14px !important;
}

/* Mobile-First Custom Styles */
.rv-hero-banner {
    background: linear-gradient(135deg, #0f766e 0%, #0d9488 50%, #06b6d4 100%);
    border-radius: 1.25rem;
    padding: 1.75rem 1.5rem;
    color: #fff;
    box-shadow: 0 10px 25px -5px rgba(13, 148, 136, 0.3);
    position: relative;
    overflow: hidden;
}
.rv-hero-banner::after {
    content: '';
    position: absolute;
    top: -40px;
    right: -40px;
    width: 160px;
    height: 160px;
    background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
}
.rv-card {
    border: none;
    border-radius: 1.25rem;
    transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    background: #ffffff;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.06);
    overflow: hidden;
}
.rv-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 28px rgba(15, 23, 42, 0.12);
}
.rv-card-header {
    padding: 1.25rem 1.5rem;
    position: relative;
}
.rv-icon-bubble {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.22);
    backdrop-filter: blur(8px);
    font-size: 1.35rem;
    flex-shrink: 0;
}
.rv-btn-action {
    border-radius: 0.85rem;
    padding: 0.75rem 1rem;
    font-weight: 600;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    min-height: 48px;
}
.rv-btn-action:active {
    transform: scale(0.98);
}
.rv-live-dot {
    width: 8px;
    height: 8px;
    background-color: #22c55e;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
    animation: rvPulse 1.8s infinite cubic-bezier(0.66, 0, 0, 1);
}
@keyframes rvPulse {
    to {
        box-shadow: 0 0 0 8px rgba(34, 197, 94, 0);
    }
}
@media (max-width: 576px) {
    .rv-hero-banner {
        padding: 1.25rem 1rem;
        border-radius: 1rem;
    }
    .rv-card-header {
        padding: 1rem 1.25rem;
    }
}
</style>

<div class="container-fluid px-0 px-md-3">
    <!-- Header Hero Banner (Mobile-First) -->
    <div class="rv-hero-banner mb-4">
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
            <div>
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill text-white fs-7 mb-2" style="background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.25);">
                    <span class="rv-live-dot"></span>
                    <span class="fw-semibold text-uppercase tracking-wider" style="font-size: 0.75rem;">Sub-District Portal &bull; நேரடி முடிவுகள்</span>
                </div>
                <h1 class="h3 fw-bold mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-megaphone-fill text-warning"></i>
                    <span>Publish Results</span>
                </h1>
                <p class="mb-0 text-white-50 font-tamil">
                    Sub District Level Festival Results & School Points are available here. Click on the respective festival to view the results or school points.
                </p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-white bg-opacity-10 px-3 py-2 rounded-pill text-white fs-7">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-white text-decoration-none"><i class="bi bi-house-door"></i> Home</a></li>
                    <li class="breadcrumb-item text-white-50">Sub-District</li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Results</li>
                </ol>
            </nav>
        </div>
    </div>

    <?php echo form_open('', array('id' => 'festrep', 'name' => 'festrep', 'target' => '_blank', 'class' => 'no-global-loader')); ?>
    <input type="hidden" id="hidfestId" name="hidfestId" value="">

    <!-- Category Results Grid (Mobile-First 1-col to 3-col) -->
    <div class="row g-3 g-md-4 mb-4">
        <?php 
        $categoryThemes = [
            1 => [
                'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                'icon' => 'bi-stars',
                'tamil' => 'தொடக்கப்பள்ளி கலைவிழா',
                'badge' => 'LP Level',
                'accent' => '#f59e0b'
            ],
            2 => [
                'gradient' => 'linear-gradient(135deg, #0284c7 0%, #0369a1 100%)',
                'icon' => 'bi-award-fill',
                'tamil' => 'நடுநிலைப் பள்ளி கலைவிழா',
                'badge' => 'UP Level',
                'accent' => '#0284c7'
            ],
            3 => [
                'gradient' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)',
                'icon' => 'bi-trophy-fill',
                'tamil' => 'உயர்நிலைப் பள்ளி கலைவிழா',
                'badge' => 'HS Level',
                'accent' => '#6366f1'
            ],
            4 => [
                'gradient' => 'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)',
                'icon' => 'bi-mortarboard-fill',
                'tamil' => 'மேல்நிலைப் பள்ளி கலைவிழா',
                'badge' => 'HSS Level',
                'accent' => '#8b5cf6'
            ],
        ];

        foreach ($fest as $key) {
            $fid = (int)$key['fest_id'];
            $th = $categoryThemes[$fid] ?? [
                'gradient' => 'linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%)',
                'icon' => 'bi-trophy',
                'tamil' => 'கலைவிழா',
                'badge' => 'Category',
                'accent' => '#0d6efd'
            ];
        ?>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card rv-card h-100">
                    <div class="rv-card-header text-white" style="background: <?php echo $th['gradient']; ?>;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-white bg-opacity-25 rounded-pill px-2.5 py-1 text-white fw-bold fs-7">
                                <?php echo $th['badge']; ?>
                            </span>
                            <span class="d-flex align-items-center gap-1.5 fs-7 bg-white bg-opacity-20 px-2.5 py-0.5 rounded-pill">
                                <span class="rv-live-dot"></span> Live
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rv-icon-bubble">
                                <i class="bi <?php echo $th['icon']; ?>"></i>
                            </div>
                            <div>
                                <h2 class="h6 fw-bold mb-0 text-white"><?php echo htmlspecialchars($key['fest_name']); ?></h2>
                                <div class="text-white-75 font-tamil"><?php echo $th['tamil']; ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-3 p-md-4 d-flex flex-column gap-2.5 justify-content-center">
                        <button type="button" class="btn btn-primary rv-btn-action shadow-sm" onClick="clickresultdeclered('<?php echo $key['fest_id']; ?>')">
                            <i class="bi bi-trophy-fill text-warning"></i>
                            <span>Result Declared</span>
                        </button>

                        <button type="button" class="btn btn-outline-primary rv-btn-action" onClick="cickpointdeclared('<?php echo $key['fest_id']; ?>')">
                            <i class="bi bi-building"></i>
                            <span>School Points</span>
                        </button>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Master Consolidated Card -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card rv-card h-100 border-start border-4 border-dark">
                <div class="rv-card-header text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 fw-bold fs-7">
                            Master Portal
                        </span>
                        <span class="text-white-50 fs-7">Consolidated</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="rv-icon-bubble">
                            <i class="bi bi-collection-fill"></i>
                        </div>
                        <div>
                            <h2 class="h6 fw-bold mb-0 text-white">All Festivals </h2>
                            <div class="text-white-75 font-tamil">அனைத்து கலைவிழாகள்</div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4 d-flex flex-column gap-2.5 justify-content-center">
                    <a href="<?php echo base_url(); ?>publishresult/resultindex/allresults" class="btn btn-dark rv-btn-action shadow-sm" target="_blank">
                        <i class="bi bi-trophy-fill text-warning"></i>
                        <span>All Results Declared</span>
                    </a>

                    <a href="<?php echo base_url(); ?>publishresult/resultindex/allfestschools" class="btn btn-outline-dark rv-btn-action" target="_blank">
                        <i class="bi bi-bar-chart-fill text-primary"></i>
                        <span>All School Points</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Festival Status & Monitoring Card -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card rv-card h-100 border-start border-4 border-success">
                <div class="rv-card-header text-white" style="background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-white text-teal rounded-pill px-2.5 py-1 fw-bold fs-7" style="color: #0f766e;">
                            Monitoring
                        </span>
                        <span class="text-white-50 fs-7">Live Dashboard</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="rv-icon-bubble">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <div>
                            <h2 class="h6 fw-bold mb-0 text-white">Festival Status</h2>
                            <div class="text-white-75 font-tamil">போட்டிகள் நிறைவு & கண்காணிப்பு</div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4 d-flex flex-column gap-2.5 justify-content-center">
                    <a href="<?php echo base_url(); ?>publishresult/resultindex/festval_status" class="btn btn-success rv-btn-action shadow-sm text-white" target="_blank">
                        <i class="bi bi-pie-chart-fill"></i>
                        <span>Status of Festival</span>
                    </a>

                    <a href="<?php echo base_url(); ?>publishresult/resultindex/allresults" class="btn btn-outline-success rv-btn-action" target="_blank">
                        <i class="bi bi-check2-circle"></i>
                        <span>All Results</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script>
function clickresultdeclered(festid) {
    var hid = document.getElementById('hidfestId');
    var form = document.getElementById('festrep');
    if (hid) hid.value = festid;
    if (form) {
        form.action = '<?php echo base_url(); ?>publishresult/resultindex/result_declared';
        form.submit();
    }
}

function cickpointdeclared(festid) {
    var hid = document.getElementById('hidfestId');
    var form = document.getElementById('festrep');
    if (hid) hid.value = festid;
    if (form) {
        form.action = '<?php echo base_url(); ?>publishresult/resultindex/schoolpoints';
        form.submit();
    }
}
</script>
