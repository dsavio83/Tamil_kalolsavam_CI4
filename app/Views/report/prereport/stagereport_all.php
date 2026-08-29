<style type="text/css"> 
    <!--
    a:link {
        text-decoration: none;
    }
    a:visited {
        text-decoration: none;
    }
    a:hover {
        text-decoration: none;
    }
    a:active {
        text-decoration: none;
    }
    -->
</style>
<br />

<?php
echo form_open('', array('id' => 'stagereport', 'target' => '_blank'));
?>
<input type="hidden" name="hidUserId" id="hidUserId" />
<div class="row justify-content-center">
    <div class="col-md-12 col-sm-12 col-xl-12">
        <div class="card">
            <div class="card-body">
              <h5 class="card-title">Result Entry</h5>
                <div class="row">
                            <div class="col-md-6">
                                <label for="inputNanme" class="form-label">Stage Report - Stage wise All Date</label>
                            </div>

                            <div class="col-md-6">
                                <label for="inputNanme" class="form-label"> Stage Report -Date wise </label> 
                                
                            </div>
                </div>



                <div class="row">
                            <div class="col-md-6">
                                <label for="inputNanme" class="form-label">
                                        Click Here    
                                        <a href="../prereportpdf/stagereport_all" target="_blank" > Stage Report All </a>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <label for="inputNanme" class="form-label"> 
                                        <a href="../prereportpdf/stagereport_abstract" target="_blank" >Abstract </a>
                                </label> 
                                
                            </div>
                </div>


<?php
//itemwise_report_interface
echo form_close();
?>