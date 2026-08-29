<style>
.display_none {
    display: none;
}
</style>

<?php
echo form_open('', array('id' => 'clustschool'));
?>
<input type="hidden" name="hidClusterId" id="hidClusterId" value="1">
<div class="row justify-content-center">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

        <div class="row mt-3 text-center">
            <div class="col-xl-4 col-md-4 col-sm-12 col-lg-12"> <?php echo @$sub_admin[0]['name']; ?></div>
             <div class="col-xl-4 col-sm-12 col-md-4 col-lg-12"> <?php echo @$sub_admin[0]['mobile']; ?></div>
              <div class="col-xl-4 col-sm-12 col-md-4 col-lg-12"> <?php echo @$sub_admin[0]['email']; ?></div>
        </div>
        
        <div class="row mt-3">
            <div class="col-6 p-2"> <span style="font-weight: bold; font-size: 18px;">&nbsp;&nbsp;Clusters </span></div>
            <div class="col-6 p-2" style="text-align: right;"> <span style="font-weight: bold; font-size: 18px;">Print</span>&nbsp;&nbsp;
                <img src="<?php echo base_url(false) . 'images/print_icon.png'; ?>" title="print" class="window_print" 
                     onClick="javascript:printContent('print_content'); return false;" />
            </div>
        </div>
 
                <div class="table-responsive mt-3">
                    <table class="table table-striped">
                        
                        <tr class="text-center">
                            <th width="5%" align="center">Sl No</th>
                            <th width="20%" align="left">Cluster</th>
                            <th width="15%" align="center">Total Schools</th>
                            <th width="15%" align="center">Data Entered</th>
                            <th width="15%" align="center">Data Not Entered</th>
                            <th width="15%" align="center">Confirmed</th>
                            <th width="15%" align="center">Not Confirmed</th>
                        </tr>
                        <?php
                        $totarray = array();
                        for ($j = 0; $j < count(@$clusters); $j++) {
                            ?>
                            <tr>
                                <td align="center" class="table_row_first"><?php echo $j + 1 ?></td>
                                <td align="left" class="table_row_first">
                                    <a href="javascript:void(0)" onClick="javascript:fncShowClusterSchools('<?php echo $clusters[$j]['user_id'] ?>')">
                                        <?php echo $clusters[$j]['user_name']; ?>
                                    </a>
                                </td>
                                <td align="center" class="table_row_first"><?php echo $clusters[$j]['total']; ?>   </td>
                                <td align="center" class="table_row_first"><?php echo $clusters[$j]['data_entered']; ?>   </td>
                                <td align="center" class="table_row_first"><?php echo (int) $clusters[$j]['total'] - (int) $clusters[$j]['data_entered']; ?>  </td>
                                <td align="center" class="table_row_first"><?php echo $clusters[$j]['finialized']; ?>   </td>
                                <td align="center" class="table_row_first"><?php echo (int) $clusters[$j]['total'] - (int) $clusters[$j]['finialized']; ?>  </td>
                            </tr>
                    
                            <?php
                            array_push($totarray, $clusters[$j]['total']);
                            $flag = 0;
                        }
                        if (count($nonclust) > 0) {
                            $tot = array_sum($totarray);
                            $balance = $nonclust[0]['mcode'] - $tot;
                            ?>
                            <tr>
                                <td align="center" class="table_row_first"><?php echo $j + 1 ?></td>
                                <td align="left" class="table_row_first">&nbsp;&nbsp;&nbsp;		
                                    <a href="javascript:void(0)" onClick="javascript:nonclusterschool(<?php echo $subdst; ?>)">			
                                        Non Cluster Schools
                                    </a>
                                </td>
                                <td align="center" class="table_row_first"><?php echo $balance; ?>   </td>
                                <td align="center" class="table_row_first"><?php echo $val[0]['ent']; ?>   </td>
                                <td align="center" class="table_row_first"><?php echo ($balance) - ($val[0]['ent']); ?>  </td>
                                <td align="center" class="table_row_first"><?php echo $val[0]['fin']; ?>   </td>
                                <td align="center" class="table_row_first"><?php echo ($balance) - ($val[0]['fin']); ?>  </td>
                            </tr>
                    
                    
                        <?php } ?>
                    
                    
                    </table>
                </div>
            </div>
        </div>
    </div>

<!-- print content starts here----------------------------------->
<div id="print_content" class="display_none" >
    <table width="100%" border="1" cellspacing="0" cellpadding="6" align="center" class="heading_tab" style="margin-top:15px;">
        <tr>
            <th width="5%" align="center">Sl No</th>
            <th width="20%" align="left">Cluster</th>
            <th width="15%" align="center">Total Schools</th>
            <th width="15%" align="center">Data Entered</th>
            <th width="15%" align="center">Data Not Entered</th>
            <th width="15%" align="center">Confirmed</th>
            <th width="15%" align="center">Not Confirmed</th>
        </tr>
        <?php
        for ($j = 0; $j < count(@$clusters); $j++) {
            ?>
            <tr>
                <td align="center" class="table_row_first"><?php echo $j + 1 ?></td>
                <td align="left" class="table_row_first">&nbsp;&nbsp;&nbsp;					
                    <?php echo $clusters[$j]['user_name']; ?>
                </td>
                <td align="center" class="table_row_first"><?php echo $clusters[$j]['total']; ?>   </td>
                <td align="center" class="table_row_first"><?php echo $clusters[$j]['data_entered']; ?>   </td>
                <td align="center" class="table_row_first"><?php echo (int) $clusters[$j]['total'] - (int) $clusters[$j]['data_entered']; ?>  </td>
                <td align="center" class="table_row_first"><?php echo $clusters[$j]['finialized']; ?>   </td>
                <td align="center" class="table_row_first"><?php echo (int) $clusters[$j]['total'] - (int) $clusters[$j]['finialized']; ?>  </td>
            </tr>
            <?php
            $flag = 0;
        }
        /*
          if(count($nonclust)>0){
          ?>
          <tr>
          <td align="center" class="table_row_first"><?php // echo $j+1?></td>
          <td align="left" class="table_row_first">&nbsp;&nbsp;&nbsp;
          <?php //echo $clusters[$j]['user_name'];?>
          </td>
          <td align="center" class="table_row_first"><?php //echo $clusters[$j]['total']; ?>   </td>
          <td align="center" class="table_row_first"><?php //echo $clusters[$j]['data_entered']; ?>   </td>
          <td align="center" class="table_row_first"><?php //echo (int)$clusters[$j]['total']-(int)$clusters[$j]['data_entered']; ?>  </td>
          <td align="center" class="table_row_first"><?php //echo $clusters[$j]['finialized']; ?>   </td>
          <td align="center" class="table_row_first"><?php //echo (int)$clusters[$j]['total']-(int)$clusters[$j]['finialized']; ?>  </td>
          </tr>
          <?php

          } */
        ?>

    </table>

    <table width="100%" border="1" cellspacing="0" cellpadding="6" align="center" class="heading_tab" style="margin-top:15px;">
        <tr>
            <td class="table_row_first" align="center">
                <?php
                print('Total School&nbsp;&nbsp;&nbsp;<strong>' . $sub_school['total_school'] . '</strong>&nbsp;&nbsp;&nbsp;&nbsp;' .
                        'Clustered School&nbsp;&nbsp;&nbsp;<strong>' . $sub_school['cluster_school'] . '</strong>&nbsp;&nbsp;&nbsp;&nbsp;' .
                        'Data Entered School&nbsp;&nbsp;&nbsp;<strong>' . $sub_school['data_entered'] . '</strong>&nbsp;&nbsp;&nbsp;&nbsp;' .
                        'Data Confirmed School&nbsp;&nbsp;&nbsp;<strong>' . $sub_school['confirmed'] . '</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                ?>
            </td>
        </tr>

    </table>

</div>
<!-- display content ends here --------------------------------------->
<?php


echo form_close();
echo '<br/>';

/* echo form_open('', array('id' => 'confirm_sub_dist'));
   */
?>
<!--<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="heading_tab" style="margin-top:15px;">
        <tr>
                <th align="left">
                        Confirm
        </th>
        <tr>
                <td class="table_row_first" align="center">
<?php /* print('Total School&nbsp;&nbsp;&nbsp;<strong>'.$sub_school['total_school'].'</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.
  'Clustered School&nbsp;&nbsp;&nbsp;<strong>'.$sub_school['cluster_school'].'</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.
  'Data Entered School&nbsp;&nbsp;&nbsp;<strong>'.$sub_school['data_entered'].'</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.
  'Data Confirmed School&nbsp;&nbsp;&nbsp;<strong>'.$sub_school['confirmed'].'</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); */ ?>
            </td>
        </tr>
<?php
/* if($this->session->userdata('USER_TYPE') == 3)
  {
  if ('N' == $sub_school['confirm_data_entry'])
  { */
?>
                        <tr>
                                <td class="table_row_first" align="left">
                                        <label style="color:#990000"><strong>Warning: Once confirmed new additions/entry details cannot be permitted</strong></label>
                                                <br /><br />
<?php //print(form_button('Confirm','Confirm','onClick="javascript:fncConfirnSubDistAdmin();"')); ?>
                                </td>
                        </tr>
<?php
/* }
  else if ('Y' == $sub_school['confirm_data_entry'])
  { */
?>		
                        <tr>
                                <td class="table_row_first" align="left">
<?php //print(form_button('data_export','Export Data','onClick="javascript:fncExportSubDistrictData();return false;"')); ?>
                                </td>
                        </tr>
<?php
/* }
  } */
?>
        </tr>
</table>-->
<input type="hidden" name="sel_sub_district_id" id="sel_sub_district_id" value="<?php echo $this->input->post('sel_sub_district_id'); ?>"/>
<?php
/* 
  echo form_close(); */
?>
