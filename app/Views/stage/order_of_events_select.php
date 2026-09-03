<div class="pagetitle mb-3">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="d-flex align-items-center gap-2 text-dark fw-bold mb-1">
        <i class="bi bi-list-ol text-primary"></i>
        <span>Order of Events</span>
      </h1>
      <p class="text-muted small mb-0">View, organize, and export the chronological order of events across festival stages.</p>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 bg-light px-3 py-1.5 rounded-pill border">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none"><i class="bi bi-house-door-fill me-1"></i>Home</a></li>
        <li class="breadcrumb-item text-muted">Stage</li>
        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Order of Events</li>
      </ol>
    </nav>
  </div>
</div>

<!-- Compact Single-Row Festival Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body py-3 px-4">
    <form method="post" action="<?php echo base_url(); ?>stage/festival_scheduling/order_of_events" id="formOrderSelect">
      <div class="row align-items-center g-3">
        <div class="col-12 col-md-auto d-flex align-items-center gap-2">
          <span class="badge bg-primary-subtle text-primary rounded-circle p-2">
            <i class="bi bi-trophy-fill fs-6"></i>
          </span>
          <label for="fest_id" class="form-label fw-bold text-dark mb-0 text-nowrap">
            Select Festival:
          </label>
        </div>

        <div class="col-12 col-md">
          <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-tags-fill"></i></span>
            <select class="form-select border-start-0 py-2" id="fest_id" name="fest_id" required>
              <option value="">-- Choose Festival Category --</option>
              <?php if (isset($festivals) && $festivals): ?>
                <?php foreach ($festivals as $festival): ?>
                  <option value="<?php echo esc($festival['fest_id']); ?>">
                    <?php echo esc($festival['fest_name']); ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>
        </div>

        <div class="col-12 col-md-auto">
          <button type="submit" id="btnOrderSelect" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-semibold w-100">
            <span class="spinner-colorful d-none" id="orderSelectSpinner"></span>
            <i class="bi bi-list-check" id="orderSelectIcon"></i>
            <span id="orderSelectText">View Order of Events</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Helpful Info Card -->
<div class="card border-0 bg-primary-subtle rounded-4 p-4 text-primary">
  <div class="d-flex align-items-start gap-3">
    <div class="rounded-circle bg-white text-primary p-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; min-width: 44px;">
      <i class="bi bi-info-circle-fill fs-4"></i>
    </div>
    <div>
      <h6 class="fw-bold mb-1 text-primary-emphasis">About Order of Events</h6>
      <p class="mb-0 text-primary-emphasis small">
        This tool aggregates scheduled events by festival date, stage venue, and start time. You can export clean printable timetables in CSV or Text formats for distribution to stage managers and judges.
      </p>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  var form = document.getElementById("formOrderSelect");
  if (form) {
    form.addEventListener("submit", function() {
      var spin = document.getElementById("orderSelectSpinner");
      var icon = document.getElementById("orderSelectIcon");
      var txt = document.getElementById("orderSelectText");
      var btn = document.getElementById("btnOrderSelect");
      if (spin) spin.classList.remove("d-none");
      if (icon) icon.classList.add("d-none");
      if (txt) txt.innerText = "Loading Events...";
      if (btn) btn.style.pointerEvents = "none";
    });
  }
});
</script>

<style>
.spinner-colorful {
  width: 1.1rem;
  height: 1.1rem;
  border-radius: 50%;
  border: 2.5px solid transparent;
  border-top-color: #ffffff; /* Top White for contrast */
  border-right-color: #f59e0b; /* Amber */
  border-bottom-color: #10b981; /* Emerald */
  border-left-color: #ec4899; /* Pink */
  animation: colorfulSpinnerRotate 0.65s linear infinite;
  display: inline-block;
  vertical-align: middle;
}

@keyframes colorfulSpinnerRotate {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
