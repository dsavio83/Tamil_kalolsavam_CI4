<div class="content-wrapper">
    <section class="content-header">
        <h1>Schedule Statistics - <?php echo isset($selected_festival) ? $selected_festival : 'Festival'; ?></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Stage Management</a></li>
            <li><a href="<?php echo site_url('stage/festival_scheduling'); ?>">Festival Scheduling</a></li>
            <li class="active">Schedule Statistics</li>
        </ol>
    </section>

    <section class="content">
        <?php if(isset($stats) && $stats): ?>
            <!-- Statistics Overview -->
            <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua"><i class="fa fa-calendar-o"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Items</span>
                            <span class="info-box-number"><?php echo $stats['total_items']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Scheduled Items</span>
                            <span class="info-box-number"><?php echo $stats['scheduled_items']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="fa fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Participants</span>
                            <span class="info-box-number"><?php echo number_format($stats['total_participants']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="fa fa-map-marker"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Venues Used</span>
                            <span class="info-box-number"><?php echo count($stats['venue_utilization']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Statistics -->
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Event Distribution</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="stat-item">
                                        <h4><?php echo $stats['off_stage_items']; ?></h4>
                                        <p>Off-stage Items</p>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-info" style="width: <?php echo ($stats['total_items'] > 0) ? ($stats['off_stage_items'] / $stats['total_items'] * 100) : 0; ?>%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="stat-item">
                                        <h4><?php echo $stats['on_stage_items']; ?></h4>
                                        <p>On-stage Items</p>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-success" style="width: <?php echo ($stats['total_items'] > 0) ? ($stats['on_stage_items'] / $stats['total_items'] * 100) : 0; ?>%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Schedule Coverage</h3>
                        </div>
                        <div class="box-body">
                            <div class="stat-item">
                                <h4><?php echo number_format(($stats['scheduled_items'] / $stats['total_items'] * 100), 1); ?>%</h4>
                                <p>Items Scheduled</p>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-warning" style="width: <?php echo ($stats['total_items'] > 0) ? ($stats['scheduled_items'] / $stats['total_items'] * 100) : 0; ?>%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Schedule Breakdown -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Daily Schedule Breakdown</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <?php if(isset($stats['schedule_by_date']) && !empty($stats['schedule_by_date'])): ?>
                                    <?php foreach($stats['schedule_by_date'] as $date => $day_items): ?>
                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                            <div class="day-stat">
                                                <h4><?php echo date('M j', strtotime($date)); ?></h4>
                                                <h3 class="text-primary"><?php echo count($day_items); ?></h3>
                                                <p>Events</p>
                                                <small class="text-muted">
                                                    <?php echo date('l', strtotime($date)); ?>
                                                </small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-md-12">
                                        <p class="text-center text-muted">No schedule data available</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Venue Utilization -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title">Venue Utilization</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <?php if(isset($stats['venue_utilization']) && !empty($stats['venue_utilization'])): ?>
                                    <?php foreach($stats['venue_utilization'] as $venue => $venue_items): ?>
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="venue-stat">
                                                <h5><?php echo $venue; ?></h5>
                                                <h3 class="text-success"><?php echo count($venue_items); ?></h3>
                                                <p>Events Hosted</p>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-success" style="width: <?php echo min(count($venue_items) * 10, 100); ?>%">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-md-12">
                                        <p class="text-center text-muted">No venue utilization data available</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-footer">
                            <a href="<?php echo site_url('stage/festival_scheduling/view_schedule?fest_id=' . $fest_id); ?>" class="btn btn-info">
                                <i class="fa fa-eye"></i> View Full Schedule
                            </a>
                            <a href="<?php echo site_url('stage/festival_scheduling/export_schedule?fest_id=' . $fest_id . '&format=excel'); ?>" class="btn btn-success">
                                <i class="fa fa-download"></i> Export Schedule
                            </a>
                            <a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back to Scheduling
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <h4><i class="icon fa fa-warning"></i> No Statistics Available!</h4>
                        No schedule statistics found for this festival.
                        <a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="btn btn-primary btn-sm">
                            <i class="fa fa-magic"></i> Generate Schedule First
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>

<style>
.info-box {
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
}

.info-box-icon {
    border-radius: 2px 0 0 2px;
    display: block;
    float: left;
    height: 90px;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0, 0, 0, 0.2);
}

.info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
    display: block;
}

.info-box-text {
    text-transform: uppercase;
    font-weight: bold;
    font-size: 14px;
}

.info-box-number {
    font-size: 24px;
    font-weight: bold;
}

.stat-item {
    text-align: center;
    margin-bottom: 20px;
}

.stat-item h4 {
    font-size: 24px;
    font-weight: bold;
    margin: 0;
}

.stat-item p {
    color: #666;
    margin: 5px 0;
    font-size: 14px;
}

.progress {
    height: 10px;
    margin: 10px 0;
    border-radius: 5px;
}

.progress-bar {
    border-radius: 5px;
}

.day-stat, .venue-stat {
    text-align: center;
    padding: 20px;
    border: 1px solid #e1e1e1;
    border-radius: 4px;
    margin-bottom: 15px;
    background-color: #f9f9f9;
}

.day-stat h4, .venue-stat h5 {
    color: #333;
    margin: 0 0 10px 0;
}

.day-stat h3, .venue-stat h3 {
    margin: 0;
    font-size: 28px;
}

.venue-stat h5 {
    font-size: 16px;
    margin-bottom: 10px;
}
</style>
