<div class="pagetitle mb-4">
  <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2">
    <i class="bi bi-megaphone-fill text-primary"></i> Publish Result
  </h1>
  <nav>
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item">Results</li>
      <li class="breadcrumb-item active">Publish Result</li>
    </ol>
  </nav>
</div>

<?php echo form_open('', array('id' => 'festrep', 'name' => 'festrep', 'target' => '_blank', 'class' => 'no-global-loader')); ?>
<input type="hidden" id="hidfestId" name="hidfestId" value="">

<div class="row g-4">
  <?php 
  $categoryThemes = [
      0 => ['header' => '#0d6efd', 'icon' => 'bi-stars'],
      1 => ['header' => '#0284c7', 'icon' => 'bi-award'],
      2 => ['header' => '#6366f1', 'icon' => 'bi-trophy'],
      3 => ['header' => '#8b5cf6', 'icon' => 'bi-mortarboard'],
  ];
  $themeIdx = 0;
  foreach ($fest as $key) { 
      $theme = $categoryThemes[$themeIdx % count($categoryThemes)];
      $themeIdx++;
  ?>
    <div class="col-md-6 col-xl-4">
      <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-header text-white py-3 px-4 border-0 rounded-top-4 d-flex align-items-center justify-content-between" style="background-color: <?php echo $theme['header']; ?>;">
          <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: rgba(255, 255, 255, 0.2);">
              <i class="bi <?php echo $theme['icon']; ?> text-white fs-5"></i>
            </div>
            <div>
              <h5 class="card-title mb-0 text-white fw-bold fs-6"><?php echo htmlspecialchars($key['fest_name']); ?></h5>
              <small class="text-white-50" style="font-size: 0.8rem;">Category Results & Points</small>
            </div>
          </div>
          <span class="badge bg-white rounded-pill px-2.5 py-1 fw-bold" style="color: <?php echo $theme['header']; ?>;">Live</span>
        </div>

        <div class="card-body p-4 d-flex flex-column gap-3 justify-content-center">
          <button type="button" class="btn btn-primary rounded-pill py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" onClick="javascript:clickresultdeclered('<?php echo $key['fest_id']; ?>')">
            <i class="bi bi-trophy-fill text-warning"></i>
            <span>Result - Declared</span>
          </button>

          <button type="button" class="btn btn-outline-primary rounded-pill py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2" onClick="javascript:return cickpointdeclared('<?php echo $key['fest_id']; ?>')">
            <i class="bi bi-building"></i>
            <span>Points - School</span>
          </button>
        </div>
      </div>
    </div>
  <?php } ?>

  <!-- Master Consolidated Card -->
  <div class="col-md-6 col-xl-4">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-header text-white py-3 px-4 border-0 rounded-top-4 d-flex align-items-center justify-content-between" style="background-color: #1e293b;">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: rgba(255, 255, 255, 0.2);">
            <i class="bi bi-collection-fill text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">All Festivals (Master)</h5>
            <small class="text-white-50" style="font-size: 0.8rem;">Sub-district wide combined summary</small>
          </div>
        </div>
        <span class="badge bg-white text-dark rounded-pill px-2.5 py-1 fw-bold">Master</span>
      </div>

      <div class="card-body p-4 d-flex flex-column gap-3 justify-content-center">
        <a href="<?php echo base_url(); ?>publishresult/publicresult/allresults" class="btn text-white rounded-pill py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" style="background-color: #1e293b;" target="_blank">
          <i class="bi bi-trophy-fill text-warning"></i>
          <span>Result - Declared (All)</span>
        </a>

        <a href="<?php echo base_url(); ?>publishresult/publicresult/allfestschools" class="btn btn-outline-dark rounded-pill py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2" target="_blank">
          <i class="bi bi-building"></i>
          <span>Points - School (All)</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Status & Overview Card -->
  <div class="col-md-6 col-xl-4">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-header text-white py-3 px-4 border-0 rounded-top-4 d-flex align-items-center justify-content-between" style="background-color: #0f766e;">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: rgba(255, 255, 255, 0.2);">
            <i class="bi bi-graph-up-arrow text-white fs-5"></i>
          </div>
          <div>
            <h5 class="card-title mb-0 text-white fw-bold fs-6">Festival Status</h5>
            <small class="text-white-50" style="font-size: 0.8rem;">Event completion & monitoring</small>
          </div>
        </div>
        <span class="badge bg-white text-success rounded-pill px-2.5 py-1 fw-bold">Status</span>
      </div>

      <div class="card-body p-4 d-flex flex-column gap-3 justify-content-center">
        <a href="<?php echo base_url(); ?>publishresult/publicresult/festval_status" class="btn btn-success rounded-pill py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" target="_blank">
          <i class="bi bi-speedometer2"></i>
          <span>Status of Festival</span>
        </a>

        <a href="<?php echo base_url(); ?>publishresult/publicresult/allresults" class="btn btn-outline-success rounded-pill py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2" target="_blank">
          <i class="bi bi-card-checklist"></i>
          <span>All Results</span>
        </a>
      </div>
    </div>
  </div>
</div>

<?php echo form_close(); ?>

<script>
function clickresultdeclered(festid) {
  var hid = document.getElementById('hidfestId');
  var form = document.getElementById('festrep');
  if (hid) hid.value = festid;
  if (form) {
    form.action = '<?php echo base_url(); ?>publishresult/publicresult/result_declared';
    form.submit();
  }
}
function cickpointdeclared(festid) {
  var hid = document.getElementById('hidfestId');
  var form = document.getElementById('festrep');
  if (hid) hid.value = festid;
  if (form) {
    form.action = '<?php echo base_url(); ?>publishresult/publicresult/schoolpoints';
    form.submit();
  }
}
</script>