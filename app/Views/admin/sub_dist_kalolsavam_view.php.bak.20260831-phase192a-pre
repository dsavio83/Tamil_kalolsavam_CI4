<div class="pagetitle">
      <h1>Settings</h1>
</div>
<!-- End Page Title -->

              <?php
                    if (@$edit_kalolsavam != 'no') {
                        echo '<br/>';
                        echo form_open_multipart('admin/kalolsavam/add_kalolsavam', array('id' => 'formKalolsavam')); ?>
                <div class="col-12">
        <div class="card"> 
            <div class="card-body">
          <h5 class="card-title">Kalolsavam Details</h5>
          <!-- Line Chart -->
          <div id="reportsChart">

                <table class="table table-borderless">
                    
                <div class="row mt-3">
                    <div class="col-xl-6 col-md-6 col-lg-12 col-sm-12">Kalolsavam Logo</div>
                    <div class="col-xl-6 col-md-6 col-lg-12 col-sm-12 mt-2"><?php display_kalolsavam_logo('sub_dist', @$selected_kalolsavam[0]['logo_name']); ?></div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-xl-6 col-md-6 col-lg-12 col-sm-12">Kalolsavam Name</div>
                    <div class="col-xl-6 col-md-6 col-lg-12 col-sm-12 mt-2"><b><?php echo @$selected_kalolsavam[0]['sub_dist_kalolsavam_name']; ?></b></div>
                </div>
                
                
                 <div class="row mt-3">
                    <div class="col-xl-6 col-md-6 col-lg-12 col-sm-12">Venue</div>
                    <div class="col-xl-6 col-md-6 col-lg-12 col-sm-12"><?php echo form_input("txtKalolsavamVenue", @$selected_kalolsavam[0]['venue'], 'class="form-control" id="txtKalolsavamVenue" '); ?> </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-xl-6 col-md-6 col-lg-12 col-sm-12">Start Date</div>
                    <div class="col-xl-6 col-md-6 col-lg-12 col-sm-12">
                    <?php
                        if ($selected_kalolsavam[0]['start_date'] == '0000-00-00')
                            $start_date = '';
                        else
                            $start_date = $selected_kalolsavam[0]['start_date'];  ?>
                        
                    <?php echo form_input("txtStartDate", @$start_date, 'class="form-control" id="txtStartDate"  
                    onfocus="javascript:displayCalendar($(\'txtStartDate\'),\'yyyy-mm-dd\',this);" readonly="readonly" '); ?>
                    
                    <img src="<?php echo base_url('images/'); ?>calender.png" onclick="displayCalendar($('txtStartDate'), 'yyyy-mm-dd', this)" width="16" height="16" style="cursor:pointer" />
                    </div>
                </div>
                
               
                <div class="row">
                    <div class="col-xl-6 col-md-12 col-lg-12 col-sm-12">End Date</div>
                    <div class="col-xl-6 col-md-12 col-lg-12 col-sm-12">
                        <?php
                            if ($selected_kalolsavam[0]['end_date'] == '0000-00-00')
                                $end_date = '';
                            else
                                $end_date = $selected_kalolsavam[0]['end_date']; ?>
                             
                        <?php echo form_input("txtEndDate", @$end_date, 'class="form-control" id="txtEndDate"  readonly="readonly"'); ?>
                        
                        <img src="<?php echo base_url('images/'); ?>calender.png" onclick="displayCalendar($('txtEndDate'), 'yyyy-mm-dd', this)" width="16" height="16" style="cursor:pointer" />
                    </div>
                </div>
                
                
                <div class="row mt-3">
                    <div class="col-xl-6 col-md-6 col-lg-12 col-sm-12">Upload Logo</div>
                    <div class="col-xl-6 col-md-6 col-lg-12 col-sm-12">
                        <?php echo form_upload("kalolsavamLogo", 'class="custom-file-input" id="kalolsavamLogo" '); ?>
                        <span class="guide_line">(.jpg, .gif, .png)</span>
                    </div>
                </div>
                
                <div class="row mt-4">
                  <div class="col-12 text-center">
                    <?php echo (count(@$selected_kalolsavam) > 0 ) ? form_button('update_kalolsavam', 'Update', 'id="update_kalolsavam"
                                onClick="javascript:fncUpdateKalolsavam(\'sub_dist\',\'' . $selected_kalolsavam[0]['sub_dist_kalolsavam_id'] . '\')" class="btn btn-primary"') . '&nbsp;&nbsp;' . form_button('Cancel', 'Cancel', 'onClick="javascript:fncCancelKalolsavam()" class="btn btn-danger"') : form_button('save_kalolsavam', 'Save', 'id="save_kalolsavam" onClick="javascript:fncSaveKalolsavam();" '); ?>
                  </div>
                </div>

              </table>
            
    <?php if (isset($kalolsavam_id) && !empty($kalolsavam_id)) { ?>
        <input type="hidden" name="kalolsavam_id" id="kalolsavam_id" value="<?php echo $kalolsavam_id; ?>" />
    <?php } ?>
    <?php
  
    echo form_close();
}
?>
 </div>
          
        </div>
  </div>
</div>

<br/>
<div class="col-12">
  <div class="card"> 
        <div class="card-body">
          <h5 class="card-title">Kalolsavam Details</h5>
          <!-- Line Chart -->
          <div id="reportsChart">

<?php
echo form_open('user/admin_users/add_admin', array('id' => 'list_formKalolsavam'));
?>
            <div class="table-responsive">
            <table class="table table-sm text-center">
                <thead>
                  <tr>
                    <th width="15px">&nbsp;</th>
                    <th width="15px">#</th>
                    <th width="120px">Kalolsavam Name</th>
                    <th width="80px">Venue</th>
                    <th width="100px">Date</th>
                    <th width="30px">Edit</th>
                  </tr>
                </thead>

                <tbody>
                    
                <tr>
                <th scope="row">
                        
                    <?php
                        $count = 0;
                        foreach ($kalolsavam_details as $kalolsavam) {
                            $count++;
                            $class_name = ($count % 2 == 0) ? '' : '';
                    ?>
                </th>
                <td width="15px">
                    <?php echo $count; ?>
                </td>

                <td width="100px">
                    <?php echo $kalolsavam['sub_dist_kalolsavam_name']; ?>
                </td>
                    
                <td width="125px"> 
                        <?php echo $kalolsavam['venue']; ?> 
                </td>
                    
                <td width="75px">
                <?php
                    if ($kalolsavam['start_date'] == '0000-00-00')
                        echo 'NULL';
                    else
                        echo date("j M Y", strtotime($kalolsavam['start_date']));
                    echo '&nbsp; - &nbsp;';
                    if ($kalolsavam['end_date'] == '0000-00-00')
                        echo 'NULL';
                    else
                        echo date("j M Y", strtotime($kalolsavam['end_date']));
                ?>
                </td>

                <td width="50px">
                   <?php if ($kalolsavam['status'] == 'O') { ?>
                        <a href="javascript:void(0)" onClick="javascript:fncEditKalolsavam('<?php echo $kalolsavam['sub_dist_kalolsavam_id'] ?>');
                                return false;">
                            <img src="<?php echo base_url(false) ?>images/edit.png" border="0">
                        </a>
                    <?php } ?>
                </td>
            </tr>
                </tbody>
                <?php } ?>
                <input type="hidden" name="sel_kalolsavam_id" id="sel_kalolsavam_id" />
            </table>
<?php

echo form_close();
?>



          </div>
          
        </div>
  </div>
</div>
</div>

