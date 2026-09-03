<style>
/* Modern Glassmorphism & Custom Palette */
:root {
  --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
  --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  --info-gradient: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
  --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  --dark-gradient: linear-gradient(135deg, #1f2937 0%, #111827 100%);
}

.dashboard-container {
  font-family: 'Poppins', 'Open Sans', sans-serif;
  color: #1e293b;
}

.hero-banner {
  background: var(--dark-gradient);
  border-radius: 20px;
  color: white;
  padding: 40px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  margin-bottom: 30px;
}

.hero-banner::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -20%;
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, rgba(124,58,237,0.2) 0%, rgba(0,0,0,0) 70%);
  border-radius: 50%;
}

.pulsate-live {
  display: inline-block;
  width: 10px;
  height: 10px;
  background-color: #ef4444;
  border-radius: 50%;
  margin-right: 6px;
  animation: pulse-animation 1.5s infinite;
}

@keyframes pulse-animation {
  0% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
  }
  70% {
    transform: scale(1);
    box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
  }
  100% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
  }
}

.glass-card {
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.25);
  border-radius: 16px;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.08);
  transition: all 0.3s ease;
  overflow: hidden;
}

.glass-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.15);
}

.stat-header {
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: rgba(255, 255, 255, 0.8);
}

.stat-value {
  font-size: 2.2rem;
  font-weight: 700;
  margin-top: 5px;
}

.shortcut-link {
  display: flex;
  align-items: center;
  padding: 16px;
  background: white;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  transition: all 0.2s ease;
  text-decoration: none;
  color: #334155;
  font-weight: 600;
}

.shortcut-link:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
  transform: translateX(5px);
  color: #4f46e5;
}

.shortcut-icon {
  font-size: 1.5rem;
  margin-right: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 8px;
}

/* Chat Updates Feed */
.chat-window {
  height: 320px;
  overflow-y: auto;
  padding: 15px;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.chat-bubble {
  margin-bottom: 12px;
  padding: 10px 14px;
  border-radius: 12px;
  font-size: 0.9rem;
  max-width: 90%;
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.stage-badge {
  background: #e0f2fe;
  color: #0369a1;
  font-weight: bold;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.8rem;
}

.live-stage-card {
  border-left: 5px solid #7c3aed;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  margin-bottom: 15px;
  transition: all 0.2s ease;
}

.live-stage-card:hover {
  border-left-color: #ef4444;
  transform: scale(1.01);
}
</style>

<!-- Load ApexCharts (local NiceAdmin component) and Google Font -->
<script src="<?php echo base_url(); ?>assets/apexcharts.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<div class="container-fluid dashboard-container py-4">

    <!-- Hero Banner Greeting -->
    <div class="hero-banner">
        <div class="row align-items-center">
            <div class="col-12">
                <h1 class="display-6 fw-bold mb-2"><?php echo @$welcome_label; ?></h1>
                <?php if (!empty($fest_name)): ?>
                    <h5 class="text-purple-300 mb-3" style="color: #c084fc;"><i class="bi bi-music-note-beamed me-2"></i><?php echo $fest_name; ?></h5>
                <?php endif; ?>
                <div class="d-flex flex-wrap gap-4 mt-2" style="font-size: 0.95rem;">
                    <?php if (!empty($venue)): ?>
                        <span><i class="bi bi-geo-alt-fill text-danger me-1"></i><strong>Venue:</strong> <?php echo $venue; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($start_date) && $start_date != '0000-00-00'): ?>
                        <span><i class="bi bi-calendar3 text-info me-1"></i><strong>Date:</strong> <?php echo date('d M Y', strtotime($start_date)) . ' - ' . date('d M Y', strtotime($end_date)); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($has_kalolsavam) && $has_kalolsavam): ?>
    <!-- Simple Results Button Card -->
    <div class="row mb-4 text-start">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between p-3 bg-white border rounded-3 shadow-sm">
                <div>
                    <h6 class="mb-1 fw-bold text-primary"><i class="bi bi-trophy-fill me-2 text-warning"></i>View Competition Results</h6>
                    <small class="text-muted">Check results and school points without login</small>
                </div>
                <a href="<?php echo base_url(); ?>publishresult/publicresult/resultview" class="btn btn-primary px-4" target="_blank">
                    <i class="bi bi-eye-fill me-2"></i>View Results
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Quick Navigation Shortcuts -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-grid-fill text-primary me-2"></i>Quick Action Portal</h5>
                </div>
                <div class="card-body bg-light">
                    <div class="row g-3">
                        <div class="col-md-4 col-sm-6">
                            <a href="<?php echo base_url('result/resultentry'); ?>" class="shortcut-link">
                                <div class="shortcut-icon bg-success text-white"><i class="bi bi-award"></i></div>
                                Enter Competition Marks
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <a href="<?php echo base_url('schools/registration'); ?>" class="shortcut-link">
                                <div class="shortcut-icon bg-primary text-white"><i class="bi bi-person-plus"></i></div>
                                Register Participants
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <a href="<?php echo base_url('stage/allotment'); ?>" class="shortcut-link">
                                <div class="shortcut-icon bg-info text-white"><i class="bi bi-calendar-check"></i></div>
                                Stage Allotments
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <a href="<?php echo base_url('stage/festival_scheduling'); ?>" class="shortcut-link">
                                <div class="shortcut-icon bg-warning text-white"><i class="bi bi-hourglass-split"></i></div>
                                Festival Scheduling
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <a href="<?php echo base_url('schools/school_master'); ?>" class="shortcut-link">
                                <div class="shortcut-icon bg-dark text-white"><i class="bi bi-building"></i></div>
                                School Master Profile
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <a href="<?php echo base_url('publishresult/publicresult/resultview'); ?>" class="shortcut-link" target="_blank">
                                <div class="shortcut-icon bg-danger text-white"><i class="bi bi-file-earmark-pdf"></i></div>
                                Print Consolidated Result
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Statistics Overview Cards -->
    <?php if (isset($statistics)): ?>
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card card h-100 text-white" style="background: var(--primary-gradient);">
                <div class="card-body">
                    <div class="stat-header">Total Registered Schools</div>
                    <div class="stat-value"><?php echo $statistics['total_schools']; ?></div>
                    <div class="mt-2 text-white-50"><i class="bi bi-check-circle me-1"></i>Finalized: <strong><?php echo $statistics['finalized_schools']; ?></strong></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card card h-100 text-white" style="background: var(--success-gradient);">
                <div class="card-body">
                    <div class="stat-header">Total Competitors</div>
                    <div class="stat-value"><?php echo $statistics['total_participants']; ?></div>
                    <div class="mt-2 text-white-50"><i class="bi bi-gender-ambiguous me-1"></i>Boys: <?php echo $statistics['boys_count']; ?> | Girls: <?php echo $statistics['girls_count']; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card card h-100 text-white" style="background: var(--info-gradient);">
                <div class="card-body">
                    <div class="stat-header">Total Scheduled Events</div>
                    <div class="stat-value"><?php echo $statistics['total_items']; ?></div>
                    <div class="mt-2 text-white-50"><i class="bi bi-flag-fill me-1"></i>Completed: <strong><?php echo $statistics['completed_items']; ?></strong></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card card h-100 text-white" style="background: var(--warning-gradient);">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="stat-header">Festival Progress</div>
                        <div class="stat-value"><?php echo $statistics['total_items'] > 0 ? round(($statistics['completed_items']/$statistics['total_items'])*100) : 0; ?>%</div>
                    </div>
                    <div class="progress mt-2" style="height: 6px; background: rgba(255,255,255,0.25);">
                        <div class="progress-bar bg-white" style="width: <?php echo $statistics['total_items'] > 0 ? ($statistics['completed_items']/$statistics['total_items'])*100 : 0; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Grid Content: Left Columns (Stages + Chat), Right Columns (Analytics) -->
    <div class="row g-4 mb-4">
        
        <!-- Left Side: Live Stages & Event Progress -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <span class="pulsate-live"></span>Live Stage Tracker
                    </h5>
                    <span class="badge bg-danger rounded-pill">LIVE UPDATES</span>
                </div>
                <div class="card-body">
                    
                    <?php if (isset($stages_schedule) && count($stages_schedule) > 0): ?>
                        <div class="row">
                        <?php foreach ($stages_schedule as $stage_id => $stage): ?>
                            <?php 
                                $events = $stage['events'];
                                $current_event = isset($events[0]) ? $events[0] : null; 
                                $next_event = isset($events[1]) ? $events[1] : null;
                            ?>
                            <div class="col-md-12 text-start">
                                <div class="live-stage-card p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-layers-half me-1"></i><?php echo $stage['name']; ?></h6>
                                        <span class="stage-badge"><i class="bi bi-stopwatch me-1"></i>Running Stage</span>
                                    </div>
                                    
                                    <div class="row g-2 text-start">
                                        <!-- Current Event -->
                                        <div class="col-md-6 border-end">
                                            <small class="text-muted d-block uppercase tracking-wider" style="font-size: 0.75rem;">CURRENT EVENT</small>
                                            <?php if ($current_event): ?>
                                                <strong class="text-danger" style="font-size: 1.05rem;"><i class="bi bi-play-circle-fill me-1"></i><?php echo $current_event['item_name']; ?></strong>
                                                <div class="text-muted mt-1" style="font-size: 0.8rem;">
                                                    <i class="bi bi-clock me-1"></i>Started at <?php echo date('h:i A', strtotime($current_event['start_time'])); ?>
                                                    | <span class="badge bg-light text-dark">Vibhagam: <?php echo $current_event['gender'] == 'G' ? 'Girls' : ($current_event['gender'] == 'B' ? 'Boys' : 'General'); ?></span>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">No active event running</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Next Event -->
                                        <div class="col-md-6 ps-md-3">
                                            <small class="text-muted d-block uppercase tracking-wider" style="font-size: 0.75rem;">UPCOMING NEXT</small>
                                            <?php if ($next_event): ?>
                                                <strong class="text-dark" style="font-size: 1.05rem;"><i class="bi bi-skip-forward-circle me-1"></i><?php echo $next_event['item_name']; ?></strong>
                                                <div class="text-muted mt-1" style="font-size: 0.8rem;">
                                                    <i class="bi bi-clock me-1"></i>Expected: <?php echo date('h:i A', strtotime($next_event['start_time'])); ?>
                                                    | Duration: <?php echo $next_event['item_time']; ?> min
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">No upcoming events scheduled</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar2-x-fill display-4 d-block mb-3"></i>
                            No stage schedules mapped for this Sub-District.
                            <a href="<?php echo base_url('stage/allotment'); ?>" class="btn btn-outline-primary btn-sm mt-3">Allot Stages Now</a>
                        </div>
                    <?php endif; ?>

                    <!-- Quick View Schedule Button -->
                    <div class="text-center mt-3">
                        <a href="<?php echo base_url('stage/festival_scheduling'); ?>" class="btn btn-outline-purple btn-sm" style="color: #7c3aed; border-color: #7c3aed;">
                            <i class="bi bi-calendar-event me-1"></i>Manage Program Schedules
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side: Live Ticker Chat Mockup -->
        <div class="col-lg-5 text-start">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-chat-left-dots text-primary me-2"></i>Live Announcements & Chat</h5>
                    <span class="badge bg-success rounded-pill">HELP DESK</span>
                </div>
                <div class="card-body">
                    <div class="chat-window mb-3 text-start" id="chatWindow">
                        <div class="chat-bubble bg-light text-dark">
                            <small class="text-muted d-block">Admin • 1 min ago</small>
                            <strong>🎉 Stage 1: Hand Writing results uploaded.</strong> Verification code sent to cluster leaders.
                        </div>
                        <div class="chat-bubble bg-light text-dark">
                            <small class="text-muted d-block">System • 12 mins ago</small>
                            <strong>⚠️ Notice:</strong> Recitation LP Girls starts in 5 minutes at Stage 2.
                        </div>
                        <div class="chat-bubble bg-light text-dark">
                            <small class="text-muted d-block">Helpdesk • 30 mins ago</small>
                            Food court coupons issued for all registration coordinators.
                        </div>
                    </div>
                    
                    <!-- Send announcement mockup -->
                    <div class="input-group">
                        <input type="text" id="announcementInput" class="form-control" placeholder="Type quick announcement...">
                        <button class="btn btn-primary" type="button" id="sendAnnounceBtn"><i class="bi bi-send"></i></button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Analytics Section: Charts & Distribution -->
    <?php if (isset($statistics)): ?>
    <div class="row g-4 mb-4">
        
        <!-- Left Side: Bar Charts -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3 text-start">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Category-wise Participants</h5>
                </div>
                <div class="card-body">
                    <div id="festStatsChart"></div>
                </div>
            </div>
        </div>
        
        <!-- Right Side: Gender Pie/Doughnut -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3 text-start">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Gender Distribution</h5>
                </div>
                <div class="card-body">
                    <div id="festGenderChart"></div>
                </div>
            </div>
        </div>
        
    </div>
    
    <!-- Category Detailed Cards Grid -->
    <div class="row g-4 text-start">
        
        <!-- LP -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-primary"><i class="bi bi-mortarboard-fill me-1"></i>LP Class (1-4)</h6>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Schools:</span>
                        <strong><?php echo $statistics['lp_schools']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Participants:</span>
                        <strong><?php echo $statistics['lp_total']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Finalized:</span>
                        <span class="badge bg-success"><?php echo $statistics['lp_finalized']; ?>/<?php echo $statistics['lp_schools']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- UP -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-success"><i class="bi bi-book-fill me-1"></i>UP Class (5-7)</h6>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Schools:</span>
                        <strong><?php echo $statistics['up_schools']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Participants:</span>
                        <strong><?php echo $statistics['up_total']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Finalized:</span>
                        <span class="badge bg-success"><?php echo $statistics['up_finalized']; ?>/<?php echo $statistics['up_schools']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- HS -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-info"><i class="bi bi-journal-text me-1"></i>HS Class (8-10)</h6>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Schools:</span>
                        <strong><?php echo $statistics['hs_schools']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Participants:</span>
                        <strong><?php echo $statistics['hs_total']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Finalized:</span>
                        <span class="badge bg-success"><?php echo $statistics['hs_finalized']; ?>/<?php echo $statistics['hs_schools']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- HSS -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-warning"><i class="bi bi-award-fill me-1"></i>HSS Class (11-12)</h6>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Schools:</span>
                        <strong><?php echo $statistics['hss_schools']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Participants:</span>
                        <strong><?php echo $statistics['hss_total']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Finalized:</span>
                        <span class="badge bg-success"><?php echo $statistics['hss_finalized']; ?>/<?php echo $statistics['hss_schools']; ?></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <?php endif; ?>

</div>

<!-- Chart Rendering Scripts -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. ApexCharts Category-wise Graph
    var statsEl = document.getElementById('festStatsChart');
    if (statsEl && typeof ApexCharts !== 'undefined') {
        var festStatsChart = new ApexCharts(statsEl, {
            series: [{
                name: 'Boys',
                data: [
                    <?php echo @$statistics['lp_boys'] ?? 0; ?>, 
                    <?php echo @$statistics['up_boys'] ?? 0; ?>, 
                    <?php echo @$statistics['hs_boys'] ?? 0; ?>, 
                    <?php echo @$statistics['hss_boys'] ?? 0; ?>
                ]
            }, {
                name: 'Girls',
                data: [
                    <?php echo @$statistics['lp_girls'] ?? 0; ?>, 
                    <?php echo @$statistics['up_girls'] ?? 0; ?>, 
                    <?php echo @$statistics['hs_girls'] ?? 0; ?>, 
                    <?php echo @$statistics['hss_girls'] ?? 0; ?>
                ]
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 6
                }
            },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: {
                categories: ['LP (1-4)', 'UP (5-7)', 'HS (8-10)', 'HSS (11-12)']
            },
            yaxis: {
                min: 0,
                forceNiceScale: true
            },
            colors: ['#4f46e5', '#ec4899'],
            legend: { position: 'top' },
            grid: { borderColor: '#f1f5f9' },
            fill: { opacity: 1 }
        });
        festStatsChart.render();
    }

    // 2. ApexCharts Gender Distribution Donut
    var genderEl = document.getElementById('festGenderChart');
    if (genderEl && typeof ApexCharts !== 'undefined') {
        var festGenderChart = new ApexCharts(genderEl, {
            series: [
                <?php echo @$statistics['boys_count'] ?? 0; ?>, 
                <?php echo @$statistics['girls_count'] ?? 0; ?>
            ],
            chart: {
                type: 'donut',
                height: 350
            },
            labels: ['Boys', 'Girls'],
            colors: ['#4f46e5', '#ec4899'],
            legend: { position: 'bottom' },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: { width: 260 },
                    legend: { position: 'bottom' }
                }
            }]
        });
        festGenderChart.render();
    }

    // 3. Mock Chat interactivity
    var sendBtn = document.getElementById('sendAnnounceBtn');
    var chatWindow = document.getElementById('chatWindow');
    var announceInput = document.getElementById('announcementInput');

    function sendAnnouncement() {
        var text = announceInput.value.trim();
        if (text === '') return;

        var bubble = document.createElement('div');
        bubble.className = 'chat-bubble bg-light text-dark';
        bubble.innerHTML = '<small class="text-muted d-block">You • Just Now</small><strong>📢 ' + 
            escapeHtml(text) + '</strong>';
        chatWindow.appendChild(bubble);
        announceInput.value = '';
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }

    sendBtn.addEventListener('click', sendAnnouncement);
    announceInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendAnnouncement();
        }
    });

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
</script>

