<div class="content-wrapper">
    <section class="content-header">
        <h1>Order of Events</h1>
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
                        <h3 class="box-title">Select Festival</h3>
                    </div>
                    <div class="box-body">
                        <form method="post" action="<?php echo base_url(); ?>stage/festival_scheduling/order_of_events">
                            <div class="form-group">
                                <label for="fest_id">Select Festival:</label>
                                <select class="form-control" id="fest_id" name="fest_id" required>
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
                            <button type="submit" class="btn btn-primary">View Order of Events</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
