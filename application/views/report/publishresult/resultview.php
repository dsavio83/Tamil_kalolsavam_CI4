<div class="pagetitle">
      <h1>Result</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="<?php echo base_url(); ?> welcome" title="Home">Home</a></li>
          <li class="breadcrumb-item active">Publish Result</li>
        </ol>
      </nav>
</div>

<!-- Full Page Banner -->
<div class="container-fluid mb-4">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-primary alert-dismissible fade show" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 50%, #fecfef 100%); color: #333; border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="alert-heading mb-1">
                            <i class="bi bi-trophy-fill me-2"></i>
                            Tamil Kalaivizha Results
                        </h4>
                        <p class="mb-0">Explore festival results, school points, and more!</p>
                    </div>
                    <div class="ms-3">
                        <i class="bi bi-star-fill" style="font-size: 3rem; color: #ff6b6b;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Login Page Header Bar Below -->
<div class="container-fluid mb-4">
    <div class="row">
        <div class="col-12">
            <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 15px;">
                <div class="card-body text-center py-3">
                    <h5 class="mb-0"><i class="bi bi-house-door-fill me-2"></i>Back to Dashboard</h5>
                    <p class="mb-0">View statistics and more from the login page</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_open('', array('id' => 'festrep', 'name' => 'festrep', 'target' => '_blank')); ?>


                <br/>
                 <div class="row">
                <input type="hidden" id="hidfestId" name="hidfestId" value="">
                    <?php
                    $bg = 0;
                    foreach ($fest as $key) {

                        if ($bg == 0) {
                            $bgcolor = "#fff";
                            $bg = 1;
                        } else {
                            $bgcolor = "#fff";
                            $bg = 0;
                        }
                        ?>
                         
                        <tr>
                            
                            <div class="col-xl-4 col-md-4 col-sm-12 col-lg-12">

                                <div class="list-group mb-4">
                                  <button type="button" class="list-group-item list-group-item-action active" aria-current="true">
                                    <?php echo $key['fest_name']; ?>
                                    <span class="badge bg-danger rounded-pill">2</span>
                                  </button>
                                  <button type="button" class="list-group-item list-group-item-action">
                                    <a href="javascript:void(0)"  onClick="javascript:clickresultdeclered('<?php echo $key['fest_id']; ?>')"> Result - Declared</a>
                                  </button>
                                  <button type="button" class="list-group-item list-group-item-action">
                                    <button class="btn btn-lg" href="javascript:void(0)" onClick="javascript:return cickpointdeclared('<?php echo $key['fest_id']; ?>')" >Points - School</button>
                                  </button>
                                </div>
                            </div>
                        </tr>
            
    
        <?php } ?>
        
                             <div class="col-xl-4 col-md-4 col-sm-12 col-lg-12">
                                <div class="list-group mb-4">
                                  <button type="button" class="list-group-item list-group-item-action active" aria-current="true">
                                    All Festival
                                  </button>
                                  <button type="button" class="list-group-item list-group-item-action">
                                    <a href="../publicresult/allresults" target="_blank"> Result - Declared</a>
                                  </button>
																	
                                  <button type="button" class="list-group-item list-group-item-action">
                                    <a href="../publicresult/allfestschools" target="_blank">Points - School</a>
                                  </button>
                                </div>
                            </div>
                            
                            <div class="col-xl-4 col-md-4 col-sm-12 col-lg-12">
                                <div class="list-group mb-4">
                                  <button type="button" class="list-group-item list-group-item-action active" aria-current="true">
                                    Status View
                                  </button>
                                  <button type="button" class="list-group-item list-group-item-action">
                                    <a href="../publicresult/allresults" target="_blank"> All Results </a>
                                  </button>
																	
                                  <button type="button" class="list-group-item list-group-item-action">
                                   <a href="../publicresult/festval_status" target="_blank"> Status of Festival </a>
                                  </button>
                                </div>
                            </div>
        
        </div>
<?php
echo form_close();
?>

<!-- Fixed Result Button -->
<div class="fixed-bottom mb-3" style="right: 20px;">
    <a href="<?php echo base_url(); ?>publishresult/publicresult/resultview" class="btn btn-success btn-lg" target="_blank">
        <i class="bi bi-eye-fill me-2"></i>View Results
    </a>
</div>
                





