<div class="content-wrapper">
    <section class="content-header">
        <h1>Schedule Preview - <?php echo isset($selected_festival) ? $selected_festival : 'Festival'; ?></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Stage Management</a></li>
            <li><a href="<?php echo site_url('stage/festival_scheduling'); ?>">Festival Scheduling</a></li>
            <li class="active">Schedule Preview</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Generated Schedule</h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo site_url('stage/festival_scheduling/view_schedule?fest_id=' . $fest_id); ?>" class="btn btn-sm btn-info">
                                <i class="fa fa-eye"></i> View Full Schedule
                            </a>
                            <a href="<?php echo site_url('stage/festival_scheduling/schedule_statistics?fest_id=' . $fest_id); ?>" class="btn btn-sm btn-warning">
                                <i class="fa fa-bar-chart"></i> Statistics
                            </a>
                            <a href="<?php echo site_url('stage/festival_scheduling/export_schedule?fest_id=' . $fest_id . '&format=excel'); ?>" class="btn btn-sm btn-success">
                                <i class="fa fa-download"></i> Export Excel
                            </a>
                        </div>
                    </div>

                    <div class="box-body">
                        <?php if(isset($schedule) && !empty($schedule)): ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-success">
                                        <i class="fa fa-check-circle"></i>
                                        <strong>Schedule Generated Successfully!</strong>
                                        Total items scheduled: <?php echo count($schedule); ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Group schedule by date -->
                            <?php
                            $schedule_by_date = array();
                            foreach($schedule as $item) {
                                $date = $item['date'];
                                if(!isset($schedule_by_date[$date])) {
                                    $schedule_by_date[$date] = array();
                                }
                                $schedule_by_date[$date][] = $item;
                            }
                            ?>

                            <?php foreach($schedule_by_date as $date => $day_schedule): ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4>
                                            <i class="fa fa-calendar"></i>
                                            <?php echo date('l, F j, Y', strtotime($date)); ?>
                                            <span class="badge"><?php echo count($day_schedule); ?> items</span>
                                        </h4>
                                    </div>
                                    <div class="panel-body">
                                        <?php
                                        // Separate off-stage and on-stage items for better display
                                        $off_stage_items = array_filter($day_schedule, function($item) {
                                            return $item['type'] == 'off_stage';
                                        });
                                        $on_stage_items = array_filter($day_schedule, function($item) {
                                            return $item['type'] != 'off_stage';
                                        });
                                        ?>

                                        <?php if(!empty($off_stage_items)): ?>
                                            <div class="venue-section">
                                                <h5><i class="fa fa-home"></i> Off-Stage Items (Writing Competitions)</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Time Slot</th>
                                                                <th>Room</th>
                                                                <th>Item Code</th>
                                                                <th>Item Name</th>
                                                                <th>Duration</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach($off_stage_items as $item): ?>
                                                                <tr>
                                                                    <td>
                                                                        <strong><?php echo date('h:i A', strtotime($item['start_time'])); ?></strong>
                                                                        <br><small class="text-muted">to <?php echo date('h:i A', strtotime($item['end_time'])); ?></small>
                                                                    </td>
                                                                    <td>
                                                                        <span class="label label-info">
                                                                            <i class="fa fa-home"></i> <?php echo $item['venue']; ?>
                                                                        </span>
                                                                    </td>
                                                                    <td><strong><?php echo $item['item_code']; ?></strong></td>
                                                                    <td><?php echo $item['item_name']; ?></td>
                                                                    <td><span class="badge"><?php echo $item['duration']; ?> min</span></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if(!empty($on_stage_items)): ?>
                                            <div class="venue-section">
                                                <h5><i class="fa fa-microphone"></i> On-Stage Items</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Time Slot</th>
                                                                <th>Stage</th>
                                                                <th>Item Code</th>
                                                                <th>Item Name</th>
                                                                <th>Duration</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach($on_stage_items as $item): ?>
                                                                <tr>
                                                                    <td>
                                                                        <strong><?php echo date('h:i A', strtotime($item['start_time'])); ?></strong>
                                                                        <br><small class="text-muted">to <?php echo date('h:i A', strtotime($item['end_time'])); ?></small>
                                                                    </td>
                                                                    <td>
                                                                        <span class="label label-primary">
                                                                            <i class="fa fa-microphone"></i> <?php echo $item['venue']; ?>
                                                                        </span>
                                                                    </td>
                                                                    <td><strong><?php echo $item['item_code']; ?></strong></td>
                                                                    <td><?php echo $item['item_name']; ?></td>
                                                                    <td><span class="badge"><?php echo $item['duration']; ?> min</span></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="callout callout-info">
                                        <h4><i class="fa fa-info-circle"></i> Schedule Information</h4>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Total Days:</strong> <?php echo count($schedule_by_date); ?>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Total Items:</strong> <?php echo count($schedule); ?>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Off-stage Items:</strong>
                                                <?php echo count(array_filter($schedule, function($item) { return $item['type'] == 'off_stage'; })); ?>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>On-stage Items:</strong>
                                                <?php echo count(array_filter($schedule, function($item) { return $item['type'] == 'on_stage'; })); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                                No schedule data available.
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="box-footer">
                        <a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Back to Scheduling
                        </a>
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="fa fa-print"></i> Print Schedule
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.panel {
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.panel-heading {
    background-color: #f5f5f5;
    border-bottom: 1px solid #ddd;
    padding: 10px 15px;
}

.panel-title {
    margin: 0;
    font-size: 16px;
    color: #333;
}

.badge {
    background-color: #337ab7;
}

.table th {
    background-color: #f9f9f9;
    font-weight: bold;
}

.label {
    display: inline-block;
    padding: 4px 8px;
    font-size: 11px;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 3px;
}

.label-info {
    background-color: #5bc0de;
}

.label-primary {
    background-color: #337ab7;
}

.callout {
    border-left: 4px solid #5bc0de;
    background-color: #f9f9f9;
    padding: 15px;
    margin: 20px 0;
}

.venue-section {
    margin-bottom: 30px;
}

.venue-section h5 {
    color: #333;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #eee;
}

.venue-section .table {
    margin-bottom: 0;
}

.venue-section .table th {
    background-color: #f8f8f8;
    border-top: none;
    font-size: 12px;
    text-transform: uppercase;
    font-weight: bold;
}
</style>
