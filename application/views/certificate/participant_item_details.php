<input type="hidden" name="hidUservalue" id="hidUservalue"  value="SHOW" />
<div class="row justify-content-center">
    <div class="col-xl-12 col-sm-12 col-md-12">
     
        <div class="card">
            <div class="card-body">
              <h5 class="card-title">Certificate Register Number Wise</h5>
                <div class="col-xl-12 col-sm-12 col-lg-12 col-md-12">
                  <label for="inputNanme" class="form-label">Register Number</label>
                </div>
                <div class="col-12">
                   <input type="text" onkeyup="javascript:fetch_participant_details_result();" onblur="javascript:fetch_participant_details_result();" 
                   onkeypress="javascript:return numbersonly(this, event, false);" id="txtParticipantId" class="form-control" value="<?php echo (isset($participant_id) && !empty($participant_id)) ? $participant_id : ''; ?>" name="txtParticipantId"/>
                    <span id="participant_detail_id"><?php echo (isset($participant_details) && !empty($participant_details)) ? $participant_details : ''; ?></span>
                </div>

                <div class="col-12">
                  <?php if (isset($item_drop_down) && !empty($item_drop_down)) { ?>
                  
                </div>
                <div class="col-12">
                    <?php echo form_input("txtStageDescription", @$selected_stage[0]['stage_desc'], 'class="form-control" id="txtStageDescription"'); ?>
                </div>
                
                <br>
                <div class="text-center">
                     <?php echo (@$selected_stage[0]['stage_id'] != '') ? form_button('Update', 'Update', 'onClick="javascript: return fnsUpdateStage(\'' . @$selected_stage[0]['stage_id'] . '\')" class="btn btn-primary"') . '&nbsp;' . form_button('Cancel', 'Cancel', 'onClick="javascript: return cancel()" class="btn btn-danger"') : form_submit('Add Stage', 'Add Stage', 'onClick="javascript: return fnsAddStage()" class="btn btn-primary" '); ?>
                </div>
            </div>
          </div>

    </div>
</div>
        
        <td align="left" width="20%" class="table_row_first"> : </td>
        <td align="left" width="65%" class="table_row_first">
            
        
    </tr>
    
        <tr >
            
            <td align="left" class="table_row_first">Item : </td>
            <td align="left" class="table_row_first" ><?php echo $item_drop_down; ?></td>
            
        </tr>
        <tr>
            <td colspan="2">
                <label>
                    <?php echo form_submit('GET', 'GET','Class="btn btn-primary"'); ?>
                </label>
            </td>
            <td >&nbsp;</td>
        </tr>
    <?php } ?>
    
</table>
</div>
</div>
</div>
</div>
</div>
