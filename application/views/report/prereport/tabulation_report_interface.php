<div class="col-xl-4 col-sm-12 col-md-4 col-lg-12">
<?php echo form_open('report/prereport/rpt_tabulation_sheet', 
array('id' => 'formTabItem', 'target' => '_blank'));  ?>

    <div class="card">
      <div class="card-body">
          <h5 class="card-title">Tabulation Sheet (Item Wise)</h5>
          <div class="row">
                <div class="col-4">
                    Festival Type
                </div>
                <div class="col-8">
                    <?php echo form_dropdown("cmbFestType", $fest, '', 'class="form-control" id="cmbFestType" onchange="javascript:fetch_item_from_festival(this.value)"'); ?>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-4">
                    Item Name
                </div>
                <div class="col-8">
                    <div id="cmbitem">
                        <?php echo form_dropdown("cbo_item", array('select'), '', 'class="form-control" id="cbo_item" '); ?>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-4">
                    No of Participants
                </div>
                <div class="col-8">
                    <?php echo form_input("txtParticipantNum", '', 
                    'class="form-control" id="txtParticipantNum" 
                    onkeypress="javascript:return numbersonly(this, event, false);"'); ?>    (No of rows to be printed)
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <form name="form1" method="post" action="">
                        <center>
                            <input type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary mt-3" value="View report" onclick="javascript: return tabulationitemsubmit()">
                        </center>
                    </form>
                           
                    <input type="hidden" name="hidUserId" id="hidUserId" />
                </div>
            </div>
      </div>
    </div>
  </div>


<input type="hidden" name="hidUserId" id="hidUserId" />
<?php echo form_close(); ?>
