<br><br><br>
<?php
   if (count($details) > 0) {
       ?>
		 <a href="<?php echo base_url(); ?>publishresult/publicresult/resultview" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Back to Results
        </a>
<section class="section">
   <div class="row justify-content-center">
      <div class="col-md-12">
         <div class="card">
            <div class="card-body">
               <!-- Table Start --->
               <div class="table-responsive">
                  <div class="table-bordered">
                     <table id="paginationNumbers" class="table" width="100%">
                        <?php
                           $prev_itemcode = "";
                           $prev_festid = "";
                           foreach ($details as $value) {
                           
                               if ($prev_festid != $value['fest_id']) {
                                   ?>
                        <tr >
                           <td colspan="8" align="center" height="27"> Result:<?php echo $details[0]['fest_name']; ?></td>
                        </tr>
                        <?php
                           }
                           if ($prev_itemcode != $value['item_code']) {
                               $prev_itemcode = $value['item_code'];
                               ?>
                        <tr>
                           <td width="18" colspan="8" align="center" height="25"><?php echo $value['item_code'] . '  ' . $value['item_name']; ?></td>
                        </tr>
                        <tr>
                           <td align="center" height="25">Reg. No</td>
                           <td align="center" height="25">Code No.</td>
                           <td align="center" height="25">Name</td>
                           <td align="center" height="25">STD</td>
                           <td align="center" height="25">School Name</td>
                           <!--<td align="center" height="25">Rank</td>-->
                           <td align="center" height="25">Grade</td>
                           <td align="center" height="25">Point</td>
                        </tr>
                        <?php } ?>
                        <tr>
                           <td align="center" height="25"><?php echo $value['participant_id']; ?></td>
                           <td align="center" height="25"><?php echo $value['code_no']; ?></td>
                           <td height="25">&nbsp;<?php echo wordwrap($value['participant_name'], 25, '<br>'); ?></td>
                           <td align="center" height="25"><?php echo $value['class']; ?></td>
                           <td height="25">&nbsp;<?php echo wordwrap($value['school_name'], 35, '<br>'); ?></td>
                           <!--<td height="25" align="center"><?php //echo $value['rank'];    ?></td>-->
                           <td height="25" align="center"><?php echo $value['grade']; ?></td>
                           <td height="25" align="center"><?php echo $value['point']; ?></td>
                        </tr>
                        <?php } ?>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
</section>
</div>
<?php
   } else {
       ?>
       <div class="container">
           <div class="row align-align-center">
               <h1>Please Wait....... Result Not Prepared</h1>
           </div>
       </div>

<?php } ?>
