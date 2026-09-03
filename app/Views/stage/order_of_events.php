<div class="pagetitle mb-3 d-print-none">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="d-flex align-items-center gap-2 text-dark fw-bold mb-1">
        <i class="bi bi-list-ol text-primary"></i>
        <span>Order of Events</span>
      </h1>
      <p class="text-muted small mb-0">Chronological timetable of events and stage allotments for <strong><?php echo esc($selected_festival); ?></strong></p>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 bg-light px-3 py-1.5 rounded-pill border">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none"><i class="bi bi-house-door-fill me-1"></i>Home</a></li>
        <li class="breadcrumb-item text-muted">Stage</li>
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>stage/festival_scheduling" class="text-decoration-none">Scheduling</a></li>
        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Order of Events</li>
      </ol>
    </nav>
  </div>
</div>

<!-- Printable Header for Paper / PDF Export -->
<div class="d-none d-print-block text-center mb-4 pb-2 border-bottom">
  <h2 class="fw-bold mb-1 text-dark">Tamil Kalolsavam</h2>
  <h4 class="text-secondary mb-1">Order of Events Timetable - <?php echo esc($selected_festival); ?></h4>
  <small class="text-muted">Generated on <?php echo date('d-m-Y h:i A'); ?></small>
</div>

<!-- Header Control Card with Festival Selector & Actions -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden d-print-none" style="border: 1px solid #e2e8f0 !important;">
  <div class="card-body p-4">
    <div class="row align-items-center g-3">
      <div class="col-12 col-lg-6">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-3 bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; min-width: 48px; background: linear-gradient(135deg, #0d6efd, #0b5ed7) !important;">
            <i class="bi bi-calendar2-week fs-4"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <h5 class="fw-bold text-dark mb-0"><?php echo esc($selected_festival); ?></h5>
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5 small">Live Timetable</span>
            </div>
            <div class="text-muted small mt-1">
              Showing chronological event sequence across all designated stages
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6 text-lg-end">
        <div class="d-flex align-items-center justify-content-lg-end gap-2 flex-wrap">
          <?php if (!empty($festivals) && count($festivals) > 1): ?>
            <form action="<?php echo base_url('stage/festival_scheduling/order_of_events'); ?>" method="GET" class="d-inline-flex align-items-center me-1 position-relative" id="festSelectForm">
              <select name="fest_id" id="festSelectDropdown" class="form-select form-select-sm rounded-pill px-3 shadow-none border-primary-subtle" onchange="loadFestivalEvents(this)">
                <?php foreach ($festivals as $f): ?>
                  <option value="<?php echo esc($f['fest_id']); ?>" <?php echo ($f['fest_id'] == $fest_id) ? 'selected' : ''; ?>>
                    <?php echo esc($f['fest_name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <!-- Small Colorful Spinner Nearby -->
              <div id="festLoadingSpinner" class="d-none align-items-center gap-1.5 ms-2" style="white-space: nowrap;">
                <span class="spinner-colorful"></span>
                <span class="small fw-semibold text-primary font-monospace" style="font-size: 11px;">Loading...</span>
              </div>
            </form>
          <?php endif; ?>

          <a href="<?php echo base_url(); ?>stage/festival_scheduling" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 btn-sm fw-semibold">
            <i class="bi bi-sliders me-1"></i> Stage Settings
          </a>
          <a href="<?php echo base_url(); ?>stage/festival_scheduling/download?format=csv&fest_id=<?php echo $fest_id; ?>" class="btn btn-outline-success rounded-pill px-3 py-1.5 btn-sm fw-semibold">
            <i class="bi bi-filetype-csv me-1"></i> Export CSV
          </a>
          <button type="button" class="btn btn-primary rounded-pill px-3.5 py-1.5 btn-sm fw-semibold shadow-sm" onclick="window.print()">
            <i class="bi bi-printer-fill me-1"></i> Print Timetable
          </button>
        </div>
      </div>
    </div>

    <hr class="my-3 text-muted opacity-25">

    <!-- Search Box & Filter Stats -->
    <div class="row g-3 align-items-center justify-content-between">
      <div class="col-12 col-md-6 col-lg-5">
        <div class="input-group input-group-sm shadow-2xs">
          <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
          <input type="text" id="orderFilterInput" class="form-control border-start-0 border-end-0 ps-0" placeholder="Search event name, code, time, or stage..." autocomplete="off">
          <button class="btn btn-white bg-white border border-start-0 text-muted" type="button" onclick="document.getElementById('orderFilterInput').value=''; document.getElementById('orderFilterInput').dispatchEvent(new Event('input'));" title="Clear">
            <i class="bi bi-x-circle"></i>
          </button>
        </div>
      </div>
      <div class="col-12 col-md-6 text-md-end">
        <?php
        $total_dates_count = !empty($order_by_date) ? count($order_by_date) : 0;
        $total_events_count = 0;
        if (!empty($order_by_date)) {
            foreach ($order_by_date as $d => $stgs) {
                foreach ($stgs as $stg => $evts) {
                    $total_events_count += count($evts);
                }
            }
        }
        ?>
        <div class="d-inline-flex align-items-center gap-2">
          <span class="badge bg-light text-secondary border rounded-pill px-3 py-1.5 font-monospace">
            <i class="bi bi-calendar-event me-1"></i><?php echo $total_dates_count; ?> Days
          </span>
          <span class="badge bg-light text-primary border rounded-pill px-3 py-1.5 font-monospace">
            <i class="bi bi-trophy me-1"></i><?php echo $total_events_count; ?> Events
          </span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Schedule Timeline Container -->
<?php if (empty($order_by_date) || !is_array($order_by_date)): ?>
  <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4" style="border: 1px solid #e2e8f0 !important;">
    <div class="empty-state-container py-3">
      <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 70px; height: 70px;">
        <i class="bi bi-calendar-x fs-2"></i>
      </div>
      <h5 class="fw-bold text-dark mb-1">No Events Scheduled Yet</h5>
      <p class="text-muted mb-3 small" style="max-width: 420px; margin: 0 auto;">No scheduled events were found for <?php echo esc($selected_festival); ?>. Generate stage schedules automatically using Gemini AI or the Auto Scheduler.</p>
      <div>
        <a href="<?php echo base_url(); ?>stage/festival_scheduling" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
          <i class="bi bi-magic me-1"></i> Open Auto Stage Scheduler
        </a>
      </div>
    </div>
  </div>
<?php else: ?>

  <?php foreach ($order_by_date as $date => $stages): ?>
    <?php if (!is_array($stages)) continue; ?>
    
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden order-date-card" style="border: 1px solid #e2e8f0 !important;">
      <!-- Date Header -->
      <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: linear-gradient(135deg, #1e293b, #0f172a) !important; color: #fff;">
        <div class="d-flex align-items-center gap-2">
          <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
            <i class="bi bi-calendar-event text-white"></i>
          </div>
          <h5 class="mb-0 fw-bold text-white">Date: <?php echo esc($date); ?></h5>
        </div>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-white text-dark rounded-pill px-3 py-1 font-monospace fw-bold">
            <?php echo count($stages); ?> Stage<?php echo count($stages) > 1 ? 's' : ''; ?>
          </span>
        </div>
      </div>

      <div class="card-body p-3 p-md-4">
        <div class="row g-3 g-md-4">
          <?php foreach ($stages as $stage => $items): ?>
            <?php if (!is_array($items)) continue; ?>
            <div class="col-12 col-xl-6 order-stage-col">
              <div class="border rounded-4 h-100 overflow-hidden bg-white shadow-xs" style="border-color: #e2e8f0 !important;">
                <!-- Stage Header -->
                <div class="py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0 !important;">
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">
                      <i class="bi bi-geo-alt-fill fs-8"></i>
                    </span>
                    <h6 class="mb-0 fw-bold text-dark fs-6"><?php echo esc($stage); ?></h6>
                  </div>
                  <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2.5 py-1 font-monospace small fw-bold">
                    <?php echo count($items); ?> Items
                  </span>
                </div>

                <!-- Items Table -->
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0 order-items-table">
                    <thead>
                      <tr class="text-uppercase text-secondary fw-semibold border-bottom" style="font-size: 11px; letter-spacing: 0.5px; background-color: #fbfcfe;">
                        <th class="ps-3 py-2 text-center" style="width: 22%;">Time</th>
                        <th class="py-2" style="width: 58%;">Event / Competition</th>
                        <th class="pe-3 py-2 text-center" style="width: 20%;">Category</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($items as $item): ?>
                        <tr class="order-item-row" data-search="<?php echo strtolower(($item['item_code'] ?? '') . ' ' . $item['item_name'] . ' ' . $item['time'] . ' ' . $item['fest_category'] . ' ' . $stage); ?>">
                          <td class="ps-3 py-2.5 text-center">
                            <span class="badge bg-light text-dark border font-monospace px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1">
                              <i class="bi bi-clock text-primary"></i><?php echo esc($item['time']); ?>
                            </span>
                          </td>
                          <td class="py-2.5">
                            <div class="d-flex align-items-center gap-2">
                              <?php if (!empty($item['item_code'])): ?>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-2 py-0.5 rounded-pill small">
                                  <?php echo esc($item['item_code']); ?>
                                </span>
                              <?php endif; ?>
                              <span class="fw-bold text-dark fs-7"><?php echo esc($item['item_name']); ?></span>
                            </div>
                          </td>
                          <td class="pe-3 py-2.5 text-center">
                            <span class="badge bg-secondary-subtle text-secondary border font-monospace px-2.5 py-1 rounded-pill">
                              <?php echo esc($item['fest_category']); ?>
                            </span>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
  var filterInput = document.getElementById("orderFilterInput");
  if (filterInput) {
    filterInput.addEventListener("input", function() {
      var query = this.value.trim().toLowerCase();
      var rows = document.querySelectorAll("tr.order-item-row");

      rows.forEach(function(row) {
        var searchData = row.getAttribute("data-search") || row.textContent.toLowerCase();
        if (!query || searchData.indexOf(query) !== -1) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });

      // Also hide empty stage cards if all rows inside are hidden
      document.querySelectorAll(".order-stage-col").forEach(function(col) {
        var visibleRows = col.querySelectorAll("tr.order-item-row:not([style*='display: none'])");
        if (query && visibleRows.length === 0) {
          col.style.display = "none";
        } else {
          col.style.display = "";
        }
      });

      // Hide empty date cards
      document.querySelectorAll(".order-date-card").forEach(function(card) {
        var visibleCols = card.querySelectorAll(".order-stage-col:not([style*='display: none'])");
        if (query && visibleCols.length === 0) {
          card.style.display = "none";
        } else {
          card.style.display = "";
        }
      });
    });
  }
});

function loadFestivalEvents(selectElem) {
  var spinner = document.getElementById("festLoadingSpinner");
  if (spinner) {
    spinner.classList.remove("d-none");
    spinner.classList.add("d-inline-flex");
  }
  selectElem.style.pointerEvents = "none";
  selectElem.style.opacity = "0.7";
  selectElem.form.submit();
}
</script>

<style>
.spinner-colorful {
  width: 1.15rem;
  height: 1.15rem;
  border-radius: 50%;
  border: 2.5px solid transparent;
  border-top-color: #0d6efd; /* Vibrant Blue */
  border-right-color: #d63384; /* Vibrant Pink */
  border-bottom-color: #198754; /* Vibrant Emerald */
  border-left-color: #ffc107; /* Vibrant Amber */
  animation: colorfulSpinnerRotate 0.65s linear infinite;
  display: inline-block;
  vertical-align: middle;
}

@keyframes colorfulSpinnerRotate {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@media print {
  body {
    background: #fff !important;
    font-size: 11pt !important;
  }
  #header, #sidebar, .pagetitle, .btn, .d-print-none {
    display: none !important;
  }
  #main {
    margin: 0 !important;
    padding: 0 !important;
  }
  .card {
    border: 1px solid #cbd5e1 !important;
    box-shadow: none !important;
    page-break-inside: avoid;
    margin-bottom: 1.5rem !important;
  }
  .card-header {
    background: #f1f5f9 !important;
    color: #0f172a !important;
    border-bottom: 2px solid #334155 !important;
  }
  .order-stage-col {
    width: 50% !important;
    float: left !important;
  }
  .table th, .table td {
    padding: 5px 8px !important;
  }
}
</style>
