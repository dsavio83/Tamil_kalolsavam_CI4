<div class="content-wrapper">
    <section class="content-header">
        <h1>Festival Schedule - <?php echo isset($selected_festival) ? $selected_festival : 'Festival'; ?></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Stage Management</a></li>
            <li><a href="<?php echo site_url('stage/festival_scheduling'); ?>">Festival Scheduling</a></li>
            <li class="active">View Schedule</li>
        </ol>
    </section>

    <section class="content">
        <?php if(isset($schedule_by_date) && !empty($schedule_by_date)): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Complete Schedule</h3>
                            <div class="box-tools pull-right">
                                <a href="<?php echo site_url('stage/festival_scheduling/schedule_statistics?fest_id=' . $fest_id); ?>" class="btn btn-sm btn-warning">
                                    <i class="fa fa-bar-chart"></i> View Statistics
                                </a>
                                <a href="<?php echo site_url('stage/festival_scheduling/export_schedule?fest_id=' . $fest_id . '&format=excel'); ?>" class="btn btn-sm btn-success">
                                    <i class="fa fa-download"></i> Export
                                </a>
                                <button type="button" class="btn btn-sm btn-info" onclick="printSchedule()">
                                    <i class="fa fa-print"></i> Print
                                </button>
                            </div>
                        </div>

                        <div class="box-body">
                            <?php foreach($schedule_by_date as $date => $day_schedule): ?>
                                <div class="day-schedule">
                                    <div class="day-header">
                                        <h3>
                                            <i class="fa fa-calendar"></i>
                                            <?php echo date('l, F j, Y', strtotime($date)); ?>
                                            <span class="badge bg-blue"><?php echo count($day_schedule); ?> events</span>
                                        </h3>
                                    </div>

                                    <div class="row">
                                        <?php
                                        // Group by venue for better display
                                        $venue_groups = array();
                                        foreach($day_schedule as $item) {
                                            $venue = $item['stage_name'];
                                            if(!isset($venue_groups[$venue])) {
                                                $venue_groups[$venue] = array();
                                            }
                                            $venue_groups[$venue][] = $item;
                                        }
                                        ?>

                                        <?php foreach($venue_groups as $venue => $venue_items): ?>
                                            <div class="col-md-6">
                                                <div class="venue-schedule">
                                                    <div class="venue-header">
                                                        <h4><i class="fa fa-map-marker"></i> <?php echo $venue; ?></h4>
                                                    </div>
                                                    <div class="venue-body">
                                                        <?php foreach($venue_items as $item): ?>
                                                            <div class="schedule-item <?php echo $item['is_off_stage'] == 'Y' ? 'off-stage' : 'on-stage'; ?>">
                                                                <div class="time-badge">
                                                                    <?php echo date('h:i A', strtotime($item['start_time'])); ?>
                                                                </div>
                                                                <div class="item-details">
                                                                    <strong><?php echo $item['item_name']; ?></strong>
                                                                    <br><small>Code: <?php echo $item['item_code']; ?></small>
                                                                    <?php if($item['is_off_stage'] == 'Y'): ?>
                                                                        <span class="label label-info">Off Stage</span>
                                                                    <?php else: ?>
                                                                        <span class="label label-primary">On Stage</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <h4><i class="icon fa fa-warning"></i> No Schedule Found!</h4>
                        No schedule has been generated for this festival yet.
                        <a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="btn btn-primary btn-sm">
                            <i class="fa fa-magic"></i> Generate Schedule
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>

<style>
.day-schedule {
    margin-bottom: 30px;
    border: 1px solid #ddd;
    border-radius: 5px;
    overflow: hidden;
}

.day-header {
    background-color: #3c8dbc;
    color: white;
    padding: 15px;
    margin: 0;
}

.day-header h3 {
    margin: 0;
    font-size: 18px;
}

.venue-schedule {
    margin-bottom: 20px;
    border: 1px solid #e1e1e1;
    border-radius: 4px;
}

.venue-header {
    background-color: #f5f5f5;
    padding: 10px 15px;
    border-bottom: 1px solid #e1e1e1;
}

.venue-header h4 {
    margin: 0;
    font-size: 14px;
    color: #333;
}

.venue-body {
    padding: 0;
}

.schedule-item {
    padding: 10px 15px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
}

.schedule-item:last-child {
    border-bottom: none;
}

.time-badge {
    background-color: #3c8dbc;
    color: white;
    padding: 5px 10px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: bold;
    margin-right: 15px;
    min-width: 60px;
    text-align: center;
}

.item-details {
    flex: 1;
}

.item-details strong {
    font-size: 14px;
    color: #333;
}

.item-details small {
    color: #666;
}

.off-stage .time-badge {
    background-color: #5bc0de;
}

.on-stage .time-badge {
    background-color: #f39c12;
}

.label {
    display: inline-block;
    padding: 3px 8px;
    font-size: 11px;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 3px;
    margin-left: 10px;
}

.label-info {
    background-color: #5bc0de;
    color: white;
}

.label-primary {
    background-color: #337ab7;
    color: white;
}

.badge {
    background-color: #337ab7;
    color: white;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
}

@media print {
    .breadcrumb, .box-tools, .btn {
        display: none !important;
    }

    .day-schedule {
        break-inside: avoid;
        margin-bottom: 20px;
    }
}
</style>

<script>
function printSchedule() {
    window.print();
}
</script>
