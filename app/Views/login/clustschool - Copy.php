<style>
.prevent-select {
  -webkit-user-select: none; /* Safari */
  -ms-user-select: none; /* IE 10 and IE 11 */
  user-select: none; /* Standard syntax */
}
</style>

<?php
echo form_open('', array('id' => 'clustschool')); ?>

<input type="hidden" name="hidSchoolId" id="hidSchoolId" value="1">

<div class="row justify-content-center">
    <div class="card">
        <div class="card-body">
            <div class="col-lg-12">
    <?php
    for ($j = 0; $j < count($school); $j++) {
        $flag = 0;
        for ($k = 0; $k < count($part); $k++) {

            if ($school[$j]['school_code'] == $part[$k]['school_code']) {
                $entry = 'Yes';
                $flag = 1;
            }
        }
        if ($flag == 0)
            $entry = 'No';
        if ($school[$j]['is_finalize'] == 'Y')
            $finalize = 'Yes';
        else if ($school[$j]['is_finalize'] == 'N')
            $finalize = 'No';
        else
            $finalize = 'No';
        $status = (empty($school[$j]['is_finalize'])) ? 'N' : $school[$j]['is_finalize'];  ?>

        <br><br>

        <div class="row mt-6">
            <div class="col-12 mb-6">
                <h3 class="text-primary prevent-select">Hi,   <?php echo $school[$j]['school_name']; ?></h3>
            </div>
        </div>
  
        <div class="row justify-content-center">
            <div class="col">
                <h2 class="text-center prevent-select"> Welcome to District Tamil Kalaivizha 2025 - 2026 </h2>
                <br>
                <h5 class="text-center prevent-select"> Venue : S N V H S Nankicity Kanjikuzhi - 26.11.24 to 30.11.24 [5 Days]</h5>
                
            </div>
        </div>

        <br><br>

        <div class="row justify-content-center">
            

            <!-- <button type="button" class="btn btn-outline-secondary" href="javascript:void(0)" onClick="javascript:goschooldet('<?php echo $school[$j]['school_code'] ?>')" style="width:120px; height:75px;">
                Registration
            </button> &nbsp;&nbsp;&nbsp;&nbsp;


            <button type="button" class="btn btn-outline-secondary" href="<?php echo base_url(false); ?>doc/itemreport_pdf.pdf" target="_blank" target="_blank" style="width:120px; height:75px;">
                Item Code
            </button> &nbsp;&nbsp;&nbsp;&nbsp;

            
            <button type="button" class="btn btn-outline-secondary" href="#" style="width:120px; height:75px;">
                Results
            </button> &nbsp;&nbsp;&nbsp;&nbsp;  -->

        </div>
<?php
    $flag = 0;
} ?> 
<?php echo form_close(); ?>

</div></div></div></div>
