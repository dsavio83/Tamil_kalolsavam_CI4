

<div class="container-fluid py-4">
    <?php if (isset($has_kalolsavam) && $has_kalolsavam): ?>
    <!-- Public Results Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info alert-dismissible fade show" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 15px;">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1">
                            <i class="bi bi-trophy-fill me-2"></i>
                            View Competition Results
                        </h5>
                        <p class="mb-0">Check results and school points without login</p>
                    </div>
                    <div class="ms-3">
                        <a href="<?php echo base_url(); ?>publishresult/publicresult/resultview"
                           class="btn btn-light btn-lg" target="_blank">
                            <i class="bi bi-eye-fill me-2"></i>View Results
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <style>
.dashboard-card {
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    animation: fadeInUp 0.6s ease-out;
}
.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes countUp {
    from { opacity: 0; transform: scale(0.5); }
    to { opacity: 1; transform: scale(1); }
}
.count-number {
    animation: countUp 0.8s ease-out;
    font-weight: bold;
}
.progress-animated {
    animation: progressBar 1.5s ease-out;
}
@keyframes progressBar {
    from { width: 0; }
}
.stat-icon {
    font-size: 2.5rem;
    opacity: 0.3;
    position: absolute;
    right: 15px;
    top: 15px;
}
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 class="mb-2"><?php echo @$welcome_label ?></h2>
            <?php if (!empty($venue)): ?>
            <p class="mb-1"><i class="bi bi-geo-alt-fill text-danger"></i> <strong><?php echo $venue; ?></strong></p>
            <?php endif; ?>
            <?php if (!empty($start_date) && $start_date != '0000-00-00'): ?>
            <p class="text-muted"><i class="bi bi-calendar-event"></i> <?php echo date('d.m.Y', strtotime($start_date)) . ' to ' . date('d.m.Y', strtotime($end_date)); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($statistics)): ?>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="dashboard-card card bg-primary text-white h-100">
                <div class="card-body position-relative">
                    <i class="bi bi-building stat-icon"></i>
                    <h6 class="card-subtitle mb-2 mt-3">Total Schools</h6>
                    <h2 class="count-number"><?php echo $statistics['total_schools']; ?></h2>
                    <small>Finalized: <?php echo $statistics['finalized_schools']; ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card card bg-success text-white h-100">
                <div class="card-body position-relative">
                    <i class="bi bi-people-fill stat-icon"></i>
                    <h6 class="card-subtitle mb-2 mt-3">Total Participants</h6>
                    <h2 class="count-number"><?php echo $statistics['total_participants']; ?></h2>
                    <small>Boys: <?php echo $statistics['boys_count']; ?> | Girls: <?php echo $statistics['girls_count']; ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card card bg-info text-white h-100">
                <div class="card-body position-relative">
                    <i class="bi bi-trophy-fill stat-icon"></i>
                    <h6 class="card-subtitle mb-2 mt-3">Total Items</h6>
                    <h2 class="count-number"><?php echo $statistics['total_items']; ?></h2>
                    <small>Completed: <?php echo $statistics['completed_items']; ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card card bg-warning text-white h-100">
                <div class="card-body position-relative">
                    <i class="bi bi-check-circle-fill stat-icon"></i>
                    <h6 class="card-subtitle mb-2 mt-3">Progress</h6>
                    <h2 class="count-number"><?php echo $statistics['total_items'] > 0 ? round(($statistics['completed_items']/$statistics['total_items'])*100) : 0; ?>%</h2>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar progress-animated" style="width: <?php echo $statistics['total_items'] > 0 ? ($statistics['completed_items']/$statistics['total_items'])*100 : 0; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="dashboard-card card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-mortarboard-fill"></i> LP Tamil Kalaivizha (Classes 1-4)</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <h4 class="count-number text-primary"><?php echo $statistics['lp_schools']; ?></h4>
                            <small class="text-muted">Schools</small>
                        </div>
                        <div class="col-4">
                            <h4 class="count-number text-info"><?php echo $statistics['lp_boys']; ?></h4>
                            <small class="text-muted">Boys</small>
                        </div>
                        <div class="col-4">
                            <h4 class="count-number text-danger"><?php echo $statistics['lp_girls']; ?></h4>
                            <small class="text-muted">Girls</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><strong>Total Participants:</strong> <?php echo $statistics['lp_total']; ?></span>
                        <span class="badge bg-success">Finalized: <?php echo $statistics['lp_finalized']; ?>/<?php echo $statistics['lp_schools']; ?></span>
                    </div>
                    <div class="progress mt-2" style="height: 8px;">
                        <div class="progress-bar bg-info progress-animated" style="width: <?php echo ($statistics['lp_total'] > 0) ? ($statistics['lp_boys']/$statistics['lp_total'])*100 : 50; ?>%"></div>
                        <div class="progress-bar bg-danger progress-animated" style="width: <?php echo ($statistics['lp_total'] > 0) ? ($statistics['lp_girls']/$statistics['lp_total'])*100 : 50; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="dashboard-card card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-book-fill"></i> UP Tamil Kalaivizha (Classes 5-7)</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <h4 class="count-number text-success"><?php echo $statistics['up_schools']; ?></h4>
                            <small class="text-muted">Schools</small>
                        </div>
                        <div class="col-4">
                            <h4 class="count-number text-info"><?php echo $statistics['up_boys']; ?></h4>
                            <small class="text-muted">Boys</small>
                        </div>
                        <div class="col-4">
                            <h4 class="count-number text-danger"><?php echo $statistics['up_girls']; ?></h4>
                            <small class="text-muted">Girls</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><strong>Total Participants:</strong> <?php echo $statistics['up_total']; ?></span>
                        <span class="badge bg-success">Finalized: <?php echo $statistics['up_finalized']; ?>/<?php echo $statistics['up_schools']; ?></span>
                    </div>
                    <div class="progress mt-2" style="height: 8px;">
                        <div class="progress-bar bg-info progress-animated" style="width: <?php echo ($statistics['up_total'] > 0) ? ($statistics['up_boys']/$statistics['up_total'])*100 : 50; ?>%"></div>
                        <div class="progress-bar bg-danger progress-animated" style="width: <?php echo ($statistics['up_total'] > 0) ? ($statistics['up_girls']/$statistics['up_total'])*100 : 50; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="dashboard-card card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-journal-text"></i> HS Tamil Kalaivizha (Classes 8-10)</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <h4 class="count-number text-info"><?php echo $statistics['hs_schools']; ?></h4>
                            <small class="text-muted">Schools</small>
                        </div>
                        <div class="col-4">
                            <h4 class="count-number text-info"><?php echo $statistics['hs_boys']; ?></h4>
                            <small class="text-muted">Boys</small>
                        </div>
                        <div class="col-4">
                            <h4 class="count-number text-danger"><?php echo $statistics['hs_girls']; ?></h4>
                            <small class="text-muted">Girls</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><strong>Total Participants:</strong> <?php echo $statistics['hs_total']; ?></span>
                        <span class="badge bg-success">Finalized: <?php echo $statistics['hs_finalized']; ?>/<?php echo $statistics['hs_schools']; ?></span>
                    </div>
                    <div class="progress mt-2" style="height: 8px;">
                        <div class="progress-bar bg-info progress-animated" style="width: <?php echo ($statistics['hs_total'] > 0) ? ($statistics['hs_boys']/$statistics['hs_total'])*100 : 50; ?>%"></div>
                        <div class="progress-bar bg-danger progress-animated" style="width: <?php echo ($statistics['hs_total'] > 0) ? ($statistics['hs_girls']/$statistics['hs_total'])*100 : 50; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="dashboard-card card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="bi bi-award-fill"></i> V-HSS Tamil Kalaivizha (Classes 11-12)</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <h4 class="count-number text-warning"><?php echo $statistics['hss_schools']; ?></h4>
                            <small class="text-muted">Schools</small>
                        </div>
                        <div class="col-4">
                            <h4 class="count-number text-info"><?php echo $statistics['hss_boys']; ?></h4>
                            <small class="text-muted">Boys</small>
                        </div>
                        <div class="col-4">
                            <h4 class="count-number text-danger"><?php echo $statistics['hss_girls']; ?></h4>
                            <small class="text-muted">Girls</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><strong>Total Participants:</strong> <?php echo $statistics['hss_total']; ?></span>
                        <span class="badge bg-success">Finalized: <?php echo $statistics['hss_finalized']; ?>/<?php echo $statistics['hss_schools']; ?></span>
                    </div>
                    <div class="progress mt-2" style="height: 8px;">
                        <div class="progress-bar bg-info progress-animated" style="width: <?php echo ($statistics['hss_total'] > 0) ? ($statistics['hss_boys']/$statistics['hss_total'])*100 : 50; ?>%"></div>
                        <div class="progress-bar bg-danger progress-animated" style="width: <?php echo ($statistics['hss_total'] > 0) ? ($statistics['hss_girls']/$statistics['hss_total'])*100 : 50; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
setInterval(function() {
    location.reload();
}, 300000); // Auto refresh every 5 minutes
</script>
