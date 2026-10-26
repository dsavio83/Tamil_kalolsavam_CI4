<?php
$total_events_count = 0;
if (!empty($order_by_date) && is_array($order_by_date)) {
    foreach ($order_by_date as $d => $stgs) {
        if (!is_array($stgs)) continue;
        foreach ($stgs as $stg => $evts) {
            if (is_array($evts)) $total_events_count += count($evts);
        }
    }
}
?>

<div class="pagetitle mb-3 d-print-none">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="d-flex align-items-center gap-2 text-dark fw-bold mb-1">
        <i class="bi bi-list-ol text-primary"></i>
        <span>Order of Events</span>
      </h1>
      <p class="text-muted small mb-0">Chronological timetable of events for <strong><?php echo esc($selected_festival); ?></strong></p>
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
              Showing chronological event sequence arranged by start time
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

    <!-- Search Box, Time Filter & Filter Stats -->
    <div class="row g-3 align-items-center justify-content-between">
      <div class="col-12 col-md-6 col-lg-5">
        <div class="input-group input-group-sm shadow-2xs">
          <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
          <input type="text" id="orderFilterInput" class="form-control border-start-0 border-end-0 ps-0" placeholder="Search event name, code, time, or stage..." autocomplete="off">
          <button class="btn btn-white bg-white border border-start-0 text-muted" type="button" onclick="document.getElementById('orderFilterInput').value=''; applyOrderFilters();" title="Clear Search">
            <i class="bi bi-x-circle"></i>
          </button>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-7">
        <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
          <span class="text-muted small fw-semibold"><i class="bi bi-clock-history me-1"></i>Time Filter:</span>
          <div class="input-group input-group-sm" style="max-width: 150px;">
            <span class="input-group-text bg-white text-muted border-end-0">From</span>
            <input type="time" id="timeFromInput" class="form-control border-start-0">
          </div>
          <div class="input-group input-group-sm" style="max-width: 150px;">
            <span class="input-group-text bg-white text-muted border-end-0">To</span>
            <input type="time" id="timeToInput" class="form-control border-start-0">
          </div>
          <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill fw-semibold" onclick="clearTimeFilter()" title="Clear Time Filter">
            <i class="bi bi-x-circle me-1"></i>Clear
          </button>
          <span class="badge bg-light text-secondary border rounded-pill px-3 py-1.5 font-monospace ms-1">
            <i class="bi bi-calendar-event me-1"></i><?php echo count($order_by_date); ?> Days
          </span>
          <span id="eventsCountBadge" class="badge bg-light text-primary border rounded-pill px-3 py-1.5 font-monospace">
            <i class="bi bi-trophy me-1"></i><?php echo $total_events_count; ?> Events
          </span>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- No Results Message (shown when filters hide everything) -->
<div id="noFilterResults" class="card border-0 shadow-sm rounded-4 p-5 text-center my-4 d-none" style="border: 1px solid #e2e8f0 !important;">
  <div class="empty-state-container py-3">
    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 70px; height: 70px;">
      <i class="bi bi-search fs-2"></i>
    </div>
    <h5 class="fw-bold text-dark mb-1">No Matching Events</h5>
    <p class="text-muted mb-3 small" style="max-width: 420px; margin: 0 auto;">No events match the current search or time filter. Adjust or clear the filters to see the full schedule.</p>
    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold" onclick="clearAllFilters()">Clear All Filters</button>
  </div>
</div>

<!-- Schedule Table Container -->
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

    <?php
      // Flatten all items from every stage for this date and sort by time
      $flat_items = array();
      foreach ($stages as $stage => $items) {
          if (!is_array($items)) continue;
          foreach ($items as $item) {
              $item['stage_name'] = $stage;
              $flat_items[] = $item;
          }
      }
      usort($flat_items, function($a, $b) {
          $timeA = strtotime($a['time'] ?: '00:00');
          $timeB = strtotime($b['time'] ?: '00:00');
          if ($timeA == $timeB) {
              return strcmp($a['stage_name'], $b['stage_name']);
          }
          return $timeA - $timeB;
      });
    ?>

    <div class="order-date-section mb-4" data-date="<?php echo esc($date); ?>">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-calendar-event text-primary"></i>Date: <?php echo esc($date); ?>
        </h5>
        <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 font-monospace fw-bold">
          <?php echo count($flat_items); ?> Event<?php echo count($flat_items) != 1 ? 's' : ''; ?>
        </span>
      </div>

      <div class="table-responsive">
        <table class="order-table align-middle mb-0">
          <thead>
            <tr>
              <th style="width: 6%;">Sl.No</th>
              <th style="width: 11%;">Time</th>
              <th style="width: 17%;">Stage</th>
              <th style="width: 44%;">Event / Competition</th>
              <th style="width: 14%;">Category</th>
              <th class="print-only">Remarks</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $serial_no = 1;
              $ts = 0;
            ?>
            <?php foreach ($flat_items as $item): ?>
              <?php
                $ts = strtotime($item['time'] ?: '00:00');
                $minutes = ((int) date('G', $ts)) * 60 + ((int) date('i', $ts));
              ?>
              <tr class="order-item-row"
                  data-minutes="<?php echo $minutes; ?>"
                  data-search="<?php echo strtolower(($item['item_code'] ?? '') . ' ' . $item['item_name'] . ' ' . $item['time'] . ' ' . $item['fest_category'] . ' ' . $item['stage_name']); ?>">
                <td><?php echo $serial_no++; ?></td>
                <td class="td-time"><?php echo esc($item['time']); ?></td>
                <td><?php echo esc($item['stage_name']); ?></td>
                <td>
                  <?php if (!empty($item['item_code'])): ?>
                    <?php echo esc($item['item_code']); ?> - 
                  <?php endif; ?>
                  <?php echo esc($item['item_name']); ?>
                  <?php if (!empty($item['participant_count'])): ?>
                    <strong>(<?php echo (int)$item['participant_count']; ?>)</strong>
                  <?php endif; ?>
                </td>
                <td><?php echo esc($item['fest_category']); ?></td>
                <td class="print-only"></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endforeach; ?>

<?php endif; ?>

<script>
function toMinutes(timeValue) {
  if (!timeValue) return null;
  var parts = timeValue.split(":");
  if (parts.length !== 2) return null;
  return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
}

function applyOrderFilters() {
  var filterInput = document.getElementById("orderFilterInput");
  var fromInput = document.getElementById("timeFromInput");
  var toInput = document.getElementById("timeToInput");

  var query = filterInput ? filterInput.value.trim().toLowerCase() : "";
  var fromMin = toMinutes(fromInput ? fromInput.value : null);
  var toMin = toMinutes(toInput ? toInput.value : null);

  var rows = document.querySelectorAll("tr.order-item-row");
  var visibleCount = 0;

  rows.forEach(function(row) {
    var show = true;
    var searchData = row.getAttribute("data-search") || row.textContent.toLowerCase();

    if (query && searchData.indexOf(query) === -1) {
      show = false;
    }

    if (show && fromMin !== null) {
      var rowMin = parseInt(row.getAttribute("data-minutes") || "0", 10);
      if (rowMin < fromMin) show = false;
    }
    if (show && toMin !== null) {
      var rowMin2 = parseInt(row.getAttribute("data-minutes") || "0", 10);
      if (rowMin2 > toMin) show = false;
    }

    row.style.display = show ? "" : "none";
    if (show) visibleCount++;
  });

  // Hide empty date sections
  document.querySelectorAll(".order-date-section").forEach(function(section) {
    var visibleRows = section.querySelectorAll("tr.order-item-row:not([style*='display: none'])");
    section.style.display = visibleRows.length > 0 ? "" : "none";
  });

  var eventsBadge = document.getElementById("eventsCountBadge");
  if (eventsBadge) {
    eventsBadge.innerHTML = '<i class="bi bi-trophy me-1"></i>' + visibleCount + ' / <span id="totalEventsLabel"></span>';
    document.getElementById("totalEventsLabel").textContent = <?php echo $total_events_count; ?>;
  }

  var noResults = document.getElementById("noFilterResults");
  var hasAnySections = document.querySelectorAll(".order-date-section:not([style*='display: none'])").length;
  var hasFiltering = (query !== "") || (fromMin !== null) || (toMin !== null);

  if (noResults) {
    if (hasFiltering && hasAnySections === 0) {
      noResults.classList.remove("d-none");
    } else {
      noResults.classList.add("d-none");
    }
  }
}

function clearTimeFilter() {
  var fromInput = document.getElementById("timeFromInput");
  var toInput = document.getElementById("timeToInput");
  if (fromInput) fromInput.value = "";
  if (toInput) toInput.value = "";
  applyOrderFilters();
}

function clearAllFilters() {
  var filterInput = document.getElementById("orderFilterInput");
  if (filterInput) filterInput.value = "";
  clearTimeFilter();
}

document.addEventListener("DOMContentLoaded", function() {
  var filterInput = document.getElementById("orderFilterInput");
  if (filterInput) {
    filterInput.addEventListener("input", applyOrderFilters);
  }
  var fromInput = document.getElementById("timeFromInput");
  var toInput = document.getElementById("timeToInput");
  if (fromInput) fromInput.addEventListener("change", applyOrderFilters);
  if (toInput) toInput.addEventListener("change", applyOrderFilters);
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

/* Ordinary bordered table (plain, no decoration) */
.order-table {
  border-collapse: collapse;
  width: 100%;
  background: #fff;
}
.order-table th,
.order-table td {
  border: 1px solid #000;
  padding: 5px 8px;
  font-size: 13px;
  vertical-align: middle;
}
.order-table tbody tr {
  height: 38px;
}
.order-table td.td-time {
  white-space: nowrap;
  overflow: hidden;
}
.order-table thead th {
  font-weight: 700;
  text-align: center;
}
.print-only {
  display: none;
}

@page {
  size: A4 portrait;
  margin: 14mm 12mm;
}

@media print {
  body {
    background: #fff !important;
    font-size: 12pt !important;
    color: #000 !important;
  }
  #header, #sidebar, .pagetitle, .btn, #noFilterResults, .d-print-none {
    display: none !important;
  }
  #main {
    margin: 0 !important;
    padding: 0 !important;
  }
  .card {
    border: none !important;
    box-shadow: none !important;
    padding: 0 !important;
    background: #fff !important;
  }
  .order-date-section {
    margin-bottom: 1.2rem !important;
  }
  .order-date-section .table-responsive {
    overflow: visible !important;
  }
  .order-date-section h5 {
    font-size: 12pt !important;
    color: #000 !important;
  }
  .order-date-section h5 i,
  .order-date-section h5 .bi {
    display: none !important;
  }
  .order-date-section .badge {
    display: none !important;
  }
  .order-table {
    border-collapse: collapse !important;
    width: 100% !important;
  }
  .order-table th,
  .order-table td {
    border: 1px solid #000 !important;
    padding: 5px 8px !important;
    font-size: 12pt !important;
    color: #000 !important;
    background: #fff !important;
  }
  .order-table thead {
    display: table-header-group;
  }
  .order-table thead th {
    font-weight: 700 !important;
    text-align: center !important;
  }
  .order-table tr {
    break-inside: avoid;
    page-break-inside: avoid;
  }
  .order-table td.td-time {
    white-space: nowrap !important;
    overflow: hidden !important;
  }
  .print-only {
    display: table-cell !important;
  }
}
</style>