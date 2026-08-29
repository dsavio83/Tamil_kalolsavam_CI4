<div class="content-wrapper">
    <section class="content-header">
        <h1>Clash Analysis - <?php echo isset($selected_festival) ? $selected_festival : 'Festival'; ?></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Stage Management</a></li>
            <li><a href="<?php echo site_url('stage/festival_scheduling'); ?>">Festival Scheduling</a></li>
            <li class="active">Clash Analysis</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Participant Clash Analysis</h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo site_url('stage/festival_scheduling/generate_schedule?fest_id=' . $fest_id); ?>" class="btn btn-sm btn-primary">
                                <i class="fa fa-magic"></i> Generate Schedule
                            </a>
                        </div>
                    </div>

                    <div class="box-body">
                        <?php if(isset($clashes) && !empty($clashes)): ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                                <strong>Clash Detected!</strong>
                                <?php echo $total_clashes; ?> participants are registered for multiple items.
                                The auto-scheduling system will attempt to resolve these clashes.
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Participant ID</th>
                                                    <th>Number of Items</th>
                                                    <th>Item Codes</th>
                                                    <th>Item Names</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $counter = 1; ?>
                                                <?php foreach($clashes as $clash): ?>
                                                    <tr>
                                                        <td><?php echo $counter++; ?></td>
                                                        <td><?php echo $clash['participant_id']; ?></td>
                                                        <td>
                                                            <span class="badge bg-red"><?php echo $clash['item_count']; ?></span>
                                                        </td>
                                                        <td><?php echo $clash['items']; ?></td>
                                                        <td><?php echo $clash['item_names']; ?></td>
                                                        <td>
                                                            <button type="button" class="btn btn-xs btn-info" onclick="toggleClashDetails(<?php echo $counter; ?>)">
                                                                View Details
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr class="collapse" id="clash-details-<?php echo $counter; ?>">
                                                        <td colspan="6">
                                                            <div class="clash-details">
                                                                <strong>Items that may clash:</strong>
                                                                <ul>
                                                                    <?php
                                                                    $item_codes = explode(',', $clash['items']);
                                                                    $item_names = explode(',', $clash['item_names']);
                                                                    for($i = 0; $i < count($item_codes); $i++):
                                                                    ?>
                                                                        <li>
                                                                            <strong><?php echo $item_codes[$i]; ?>:</strong> <?php echo $item_names[$i]; ?>
                                                                        </li>
                                                                    <?php endfor; ?>
                                                                </ul>
                                                                <p class="text-muted">
                                                                    <i class="fa fa-info-circle"></i>
                                                                    The auto-scheduling system will try to schedule these items at different times to avoid participant conflicts.
                                                                </p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="callout callout-info">
                                        <h4><i class="fa fa-lightbulb-o"></i> Clash Resolution Strategy</h4>
                                        <p>The auto-scheduling system uses the following strategy to resolve clashes:</p>
                                        <ul>
                                            <li>Analyzes participant registration patterns</li>
                                            <li>Identifies conflicting time slots</li>
                                            <li>Prioritizes larger events over smaller ones</li>
                                            <li>Distributes events across available venues and time slots</li>
                                            <li>Ensures no participant has overlapping events</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        <?php else: ?>
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i>
                                <strong>No Clashes Detected!</strong>
                                All participants are registered for single items only.
                                The auto-scheduling system can proceed without conflict resolution.
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="callout callout-success">
                                        <h4><i class="fa fa-thumbs-up"></i> Clean Schedule</h4>
                                        <p>Your festival has no participant clashes. This means:</p>
                                        <ul>
                                            <li>Each participant is registered for only one item</li>
                                            <li>No time slot conflicts to resolve</li>
                                            <li>Scheduling will be straightforward</li>
                                            <li>Maximum venue utilization possible</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="box-footer">
                        <a href="<?php echo site_url('stage/festival_scheduling'); ?>" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Back to Scheduling
                        </a>
                        <?php if(isset($clashes) && !empty($clashes)): ?>
                            <a href="<?php echo site_url('stage/festival_scheduling/generate_schedule?fest_id=' . $fest_id); ?>" class="btn btn-primary">
                                <i class="fa fa-magic"></i> Generate Schedule Anyway
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.badge {
    background-color: #dd4b39;
    color: white;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
}

.clash-details {
    background-color: #f9f9f9;
    padding: 15px;
    border-radius: 4px;
    margin-top: 10px;
}

.clash-details ul {
    margin-bottom: 10px;
}

.clash-details li {
    margin-bottom: 5px;
}

.callout {
    border-left: 4px solid #f39c12;
    background-color: #f9f9f9;
    padding: 15px;
    margin: 20px 0;
}

.callout-success {
    border-left-color: #5cb85c;
}

.callout-info {
    border-left-color: #5bc0de;
}

.table th {
    background-color: #f5f5f5;
    font-weight: bold;
}

.table td {
    vertical-align: middle;
}

<script>
function toggleClashDetails(counter) {
    var detailsRow = document.getElementById('clash-details-' + counter);
    if (detailsRow.style.display === 'none' || detailsRow.style.display === '') {
        detailsRow.style.display = 'table-row';
    } else {
        detailsRow.style.display = 'none';
    }
}

// Show first clash details by default
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($clashes) && !empty($clashes)): ?>
        var firstClashDetails = document.getElementById('clash-details-1');
        if (firstClashDetails) {
            firstClashDetails.style.display = 'table-row';
        }
    <?php endif; ?>
});
</script>
</style>
