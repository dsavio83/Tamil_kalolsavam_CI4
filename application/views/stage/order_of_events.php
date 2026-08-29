<div class="content-wrapper">
    <section class="content-header">
        <h1>Order of Events - <?php echo $selected_festival; ?></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Stage Management</a></li>
            <li class="active">Order of Events</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Events Schedule</h3>
                        <div class="box-tools">
                            <a href="<?php echo base_url(); ?>stage/festival_scheduling/order_of_events/download?format=txt&fest_id=<?php echo $fest_id; ?>" class="btn btn-sm btn-info">Download Text</a>
                            <a href="<?php echo base_url(); ?>stage/festival_scheduling/order_of_events/download?format=csv&fest_id=<?php echo $fest_id; ?>" class="btn btn-sm btn-success">Download CSV</a>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if (empty($order_by_date) || !is_array($order_by_date)): ?>
                            <p>No events scheduled.</p>
                        <?php else: ?>
                            <?php foreach ($order_by_date as $date => $stages): ?>
                                <?php if (!is_array($stages)) continue; ?>
                                <h4>Date: <?php echo $date; ?></h4>
                                <?php foreach ($stages as $stage => $items): ?>
                                    <?php if (!is_array($items)) continue; ?>
                                    <h5>Stage: <?php echo $stage; ?></h5>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Time</th>
                                                <th>Item Name</th>
                                                <th>Fest Category</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($items as $item): ?>
                                                <tr>
                                                    <td><?php echo $item['time']; ?></td>
                                                    <td><?php echo $item['item_name']; ?></td>
                                                    <td><?php echo $item['fest_category']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
