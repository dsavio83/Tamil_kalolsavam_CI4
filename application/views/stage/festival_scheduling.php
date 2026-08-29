<div class="content-wrapper">
    <section class="content-header">
        <h1>Festival Auto Scheduling System</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Stage Management</a></li>
            <li class="active">Festival Scheduling</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Auto Schedule Festival Events</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <?php if(isset($message) && $message): ?>
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($error) && $error): ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fest_id">Select Festival:</label>
                                    <select class="form-control" id="fest_id" name="fest_id" required onchange="loadFestivalDates()">
                                        <option value="">-- Select Festival --</option>
                                        <?php if(isset($festivals) && $festivals): ?>
                                            <?php foreach($festivals as $festival): ?>
                                                <option value="<?php echo $festival['fest_id']; ?>">
                                                    <?php echo $festival['fest_name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date">Festival Start Date:</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date">Festival End Date:</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary" onclick="generateSchedule()">
                                        <i class="fa fa-magic"></i> Auto Set Stage
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="viewSchedule()">
                                        <i class="fa fa-eye"></i> View Schedule
                                    </button>
                                    <button type="button" class="btn btn-warning" onclick="analyzeClashes()">
                                        <i class="fa fa-exclamation-triangle"></i> Analyze Clashes
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="viewStatistics()">
                                        <i class="fa fa-bar-chart"></i> View Statistics
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="callout callout-info">
                                    <h4><i class="icon fa fa-info"></i> Festival Scheduling Information</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Festival Duration:</strong> <?php echo isset($_POST['start_date']) ? $_POST['start_date'] : 'October 29'; ?> to <?php echo isset($_POST['end_date']) ? $_POST['end_date'] : 'November 1'; ?> (4 days)</p>
                                            <p><strong>Time Slots:</strong> 9:00 AM to 5:00 PM daily</p>
                                            <p><strong>Day 1:</strong> Off-stage items (writing competitions) in Rooms 19, 20, 21</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Days 2-4:</strong> On-stage items in designated stages</p>
                                            <p><strong>Auto-scheduling considers:</strong> Participant clashes, venue availability, and time constraints</p>
                                            <p><strong>Off-stage Items:</strong> Handwriting, Story Writing, Versification, Essay Writing</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Time Slot Configuration -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-clock-o"></i> Time Slot Configuration</h3>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Start Time:</label>
                                                    <select class="form-control" id="start_time" name="start_time">
                                                        <option value="09:00:00" selected>9:00 AM</option>
                                                        <option value="08:00:00">8:00 AM</option>
                                                        <option value="08:30:00">8:30 AM</option>
                                                        <option value="10:00:00">10:00 AM</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>End Time:</label>
                                                    <select class="form-control" id="end_time" name="end_time">
                                                        <option value="17:00:00" selected>5:00 PM</option>
                                                        <option value="16:00:00">4:00 PM</option>
                                                        <option value="16:30:00">4:30 PM</option>
                                                        <option value="18:00:00">6:00 PM</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Slot Duration:</label>
                                                    <select class="form-control" id="slot_duration" name="slot_duration">
                                                        <option value="60" selected>60 minutes</option>
                                                        <option value="30">30 minutes</option>
                                                        <option value="45">45 minutes</option>
                                                        <option value="90">90 minutes</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>&nbsp;</label><br>
                                                    <button type="button" class="btn btn-sm btn-default" onclick="resetToDefaults()">
                                                        <i class="fa fa-refresh"></i> Reset to Defaults
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Schedule Status</span>
                        <span class="info-box-number">Auto</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Clash Detection</span>
                        <span class="info-box-number">Active</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Time Management</span>
                        <span class="info-box-number">9AM-5PM</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-map-marker"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Venue Allocation</span>
                        <span class="info-box-number">Auto</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function loadFestivalDates() {
    var festId = document.getElementById('fest_id').value;
    if (!festId) {
        document.getElementById('start_date').value = '';
        document.getElementById('end_date').value = '';
        return;
    }

    // Set default dates based on festival selection
    // You can modify these dates based on your festival configuration
    document.getElementById('start_date').value = '2024-10-29';
    document.getElementById('end_date').value = '2024-11-01';
}

function generateSchedule() {
    var festId = document.getElementById('fest_id').value;
    var startDate = document.getElementById('start_date').value;
    var endDate = document.getElementById('end_date').value;
    var startTime = document.getElementById('start_time').value;
    var endTime = document.getElementById('end_time').value;
    var slotDuration = document.getElementById('slot_duration').value;

    if (!festId) {
        alert('Please select a festival first');
        return;
    }

    if (!startDate || !endDate) {
        alert('Please select both start and end dates');
        return;
    }

    if (confirm('This will generate a new auto-schedule for the selected festival with the configured time slots. Continue?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo site_url("stage/festival_scheduling/generate_schedule"); ?>';

        var festInput = document.createElement('input');
        festInput.type = 'hidden';
        festInput.name = 'fest_id';
        festInput.value = festId;
        form.appendChild(festInput);

        var startDateInput = document.createElement('input');
        startDateInput.type = 'hidden';
        startDateInput.name = 'start_date';
        startDateInput.value = startDate;
        form.appendChild(startDateInput);

        var endDateInput = document.createElement('input');
        endDateInput.type = 'hidden';
        endDateInput.name = 'end_date';
        endDateInput.value = endDate;
        form.appendChild(endDateInput);

        var startTimeInput = document.createElement('input');
        startTimeInput.type = 'hidden';
        startTimeInput.name = 'start_time';
        startTimeInput.value = startTime;
        form.appendChild(startTimeInput);

        var endTimeInput = document.createElement('input');
        endTimeInput.type = 'hidden';
        endTimeInput.name = 'end_time';
        endTimeInput.value = endTime;
        form.appendChild(endTimeInput);

        var slotDurationInput = document.createElement('input');
        slotDurationInput.type = 'hidden';
        slotDurationInput.name = 'slot_duration';
        slotDurationInput.value = slotDuration;
        form.appendChild(slotDurationInput);

        document.body.appendChild(form);
        form.submit();
    }
}

function resetToDefaults() {
    document.getElementById('start_time').value = '09:00:00';
    document.getElementById('end_time').value = '17:00:00';
    document.getElementById('slot_duration').value = '60';
}

function viewSchedule() {
    var festId = document.getElementById('fest_id').value;
    if (!festId) {
        alert('Please select a festival first');
        return;
    }

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo site_url("stage/festival_scheduling/view_schedule"); ?>';

    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'fest_id';
    input.value = festId;
    form.appendChild(input);

    document.body.appendChild(form);
    form.submit();
}

function analyzeClashes() {
    var festId = document.getElementById('fest_id').value;
    if (!festId) {
        alert('Please select a festival first');
        return;
    }

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo site_url("stage/festival_scheduling/analyze_clashes"); ?>';

    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'fest_id';
    input.value = festId;
    form.appendChild(input);

    document.body.appendChild(form);
    form.submit();
}

function viewStatistics() {
    var festId = document.getElementById('fest_id').value;
    if (!festId) {
        alert('Please select a festival first');
        return;
    }

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo site_url("stage/festival_scheduling/schedule_statistics"); ?>';

    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'fest_id';
    input.value = festId;
    form.appendChild(input);

    document.body.appendChild(form);
    form.submit();
}

// Set default dates on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set default dates if no festival is selected
    var today = new Date();
    var defaultStart = new Date('2024-10-29');
    var defaultEnd = new Date('2024-11-01');

    if (!document.getElementById('start_date').value) {
        document.getElementById('start_date').value = defaultStart.toISOString().split('T')[0];
    }
    if (!document.getElementById('end_date').value) {
        document.getElementById('end_date').value = defaultEnd.toISOString().split('T')[0];
    }
});
</script>
