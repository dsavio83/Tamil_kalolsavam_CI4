<div class="pagetitle d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
  <div>
    <h1 class="h4 mb-1 text-gray-800 fw-bold d-flex align-items-center gap-2">
      <i class="bi bi-person-bounding-box text-primary"></i> Certificate - Reg No. Wise
    </h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0" style="font-size: 13.5px;">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome">Home</a></li>
        <li class="breadcrumb-item">Certificate</li>
        <li class="breadcrumb-item active">Reg No. Wise</li>
      </ol>
    </nav>
  </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm rounded-4 mb-4" role="alert" style="font-size: 14px;">
    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 fs-5"></i>
    <div><?php echo session()->getFlashdata('error'); ?></div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<!-- Top Section: Interactive Student Search & Certificate Generator -->
<?php echo form_open('certificate/certificate/get_certificate_pdf_participant_wise', array('id' => 'formIRL', 'target' => '_blank')); ?>
<div id="content_id">
  <?php echo view('certificate/participant_item_details', get_defined_vars()); ?>
</div>
<?php echo form_close(); ?>

<!-- Bottom Section: All Eligible Students Roster -->
<?php 
  $roster = $all_participants ?? [];
  $total_students = count($roster);
?>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4 mt-4">
  <div class="card-header bg-white py-3 px-4 border-bottom border-light d-flex flex-wrap align-items-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-people-fill text-primary"></i>
      <h6 class="mb-0 fw-bold text-dark" style="font-size: 15px;">All Certificate-Eligible Students Roster</h6>
      <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 ms-1" style="font-size: 12px;"><?php echo $total_students; ?></span>
    </div>

    <div class="d-flex align-items-center gap-3">
      <div class="input-group input-group-sm" style="max-width: 260px;">
        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
        <input type="text" id="allStudentsSearch" class="form-control bg-light border-start-0" placeholder="Filter by name, reg no, item..." onkeyup="filterAllStudents()" style="font-size: 13.5px;">
      </div>
      <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-flex align-items-center gap-1.5" onclick="printRoster();" style="font-size: 13px;">
        <i class="bi bi-printer"></i> Print
      </button>
    </div>
  </div>

  <div class="card-body p-0">
    <?php if (!empty($roster) && count($roster) > 0): ?>
      <div class="table-responsive">
        <table class="table align-middle table-hover mb-0" id="allStudentsTable" style="font-size: 14px;">
          <thead class="table-light text-uppercase text-secondary border-bottom" style="font-size: 12.5px; letter-spacing: 0.5px;">
            <tr>
              <th class="ps-4 py-3" style="width: 110px;">Reg No</th>
              <th class="py-3">Student Name</th>
              <th class="py-3">School</th>
              <th class="py-3">Item / Event</th>
              <th class="text-center py-3" style="width: 90px;">Grade</th>
              <th class="text-center py-3" style="width: 90px;">Rank</th>
              <th class="text-end pe-4 py-3" style="width: 140px;">Action</th>
            </tr>
          </thead>
          <tbody class="border-top-0">
            <?php foreach ($roster as $row): ?>
              <tr>
                <td class="ps-4 py-3">
                  <a href="javascript:void(0)" onclick="quickSelectStudent('<?php echo htmlspecialchars($row['participant_id']); ?>')" class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-2.5 py-1.5 text-decoration-none fw-bold" style="font-size: 13px;" title="Click to select this student">
                    #<?php echo htmlspecialchars($row['participant_id']); ?>
                  </a>
                </td>
                <td class="py-3">
                  <div class="fw-semibold text-dark" style="font-size: 14px;"><?php echo htmlspecialchars($row['participant_name']); ?></div>
                  <?php if (!empty($row['admn_no'])): ?>
                    <small class="text-muted" style="font-size: 12px;">Admn: <?php echo htmlspecialchars($row['admn_no']); ?></small>
                  <?php endif; ?>
                </td>
                <td class="py-3" style="font-size: 13.5px;">
                  <div class="text-dark"><?php echo htmlspecialchars($row['school_name'] ?? ''); ?></div>
                  <?php if (!empty($row['sub_district_name'])): ?>
                    <small class="text-primary d-block fw-semibold" style="font-size: 11.5px;"><i class="bi bi-geo-alt-fill me-1"></i><?php echo htmlspecialchars($row['sub_district_name']); ?></small>
                  <?php endif; ?>
                  <small class="text-muted" style="font-size: 12px;"><?php echo htmlspecialchars($row['school_code'] ?? ''); ?></small>
                </td>
                <td class="py-3">
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border font-monospace px-2 py-1" style="font-size: 12px;">
                      <?php echo htmlspecialchars($row['item_code']); ?>
                    </span>
                    <span class="fw-medium text-secondary" style="font-size: 14px;"><?php echo htmlspecialchars($row['item_name']); ?></span>
                  </div>
                </td>
                <td class="text-center py-3">
                  <?php 
                    $grade = $row['grade'] ?? '';
                    if ($grade === 'A') {
                      echo '<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-bold" style="font-size: 12px;">Grade A</span>';
                    } else if ($grade === 'B') {
                      echo '<span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 fw-bold" style="font-size: 12px;">Grade B</span>';
                    } else if ($grade === 'C') {
                      echo '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 fw-bold" style="font-size: 12px;">Grade C</span>';
                    } else {
                      echo '<span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 12px;">—</span>';
                    }
                  ?>
                </td>
                <td class="text-center py-3">
                  <?php 
                    $rank = (int)($row['rank'] ?? 0);
                    if ($rank === 1) {
                      echo '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 fw-semibold" style="font-size: 12px;"><i class="bi bi-trophy-fill text-warning me-1"></i>1st</span>';
                    } else if ($rank === 2) {
                      echo '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 fw-semibold" style="font-size: 12px;">2nd</span>';
                    } else if ($rank === 3) {
                      echo '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-semibold" style="font-size: 12px;">3rd</span>';
                    } else {
                      echo '<span class="text-muted" style="font-size: 13px;">—</span>';
                    }
                  ?>
                </td>
                <td class="text-end pe-4 py-3">
                  <div class="d-inline-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1" style="font-size: 12.5px;" onclick="quickSelectStudent('<?php echo htmlspecialchars($row['participant_id']); ?>')">
                      Select
                    </button>
                    <?php echo form_open('certificate/certificate/get_certificate_pdf_participant_wise', array('target' => '_blank', 'class' => 'd-inline ms-1')); ?>
                      <input type="hidden" name="txtParticipantId" value="<?php echo htmlspecialchars($row['participant_id']); ?>" />
                      <input type="hidden" name="item_code" value="<?php echo htmlspecialchars($row['item_code']); ?>" />
                      <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1 shadow-none" style="font-size: 12.5px;" title="Print certificate">
                        <i class="bi bi-printer"></i> Print
                      </button>
                    <?php echo form_close(); ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination Controls -->
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 px-4 py-3 border-top border-light" id="rosterPaginationBar">
        <div class="d-flex flex-wrap align-items-center gap-2">
          <label for="rosterPageSize" class="text-muted mb-0" style="font-size: 12.5px;">Rows per page:</label>
          <select id="rosterPageSize" class="form-select form-select-sm border-secondary-subtle" style="width: auto; font-size: 13px;" onchange="setRosterPageSize(this.value)">
            <option value="10">10</option>
            <option value="25" selected>25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="all">All</option>
          </select>
          <span id="rosterPageInfo" class="text-muted ms-1" style="font-size: 12.5px;"></span>
        </div>
        <nav aria-label="Roster pages">
          <ul class="pagination pagination-sm mb-0" id="rosterPagination"></ul>
        </nav>
      </div>
    <?php else: ?>
      <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
        <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">No Certificate-Eligible Students Found</h6>
        <p class="small mb-0" style="font-size: 13px;">Confirmed results will appear here automatically.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
var rosterPageSize = 25;
var rosterCurrentPage = 1;
var rosterFilteredRows = [];

function getRosterRows() {
  var table = document.getElementById('allStudentsTable');
  if (!table || !table.tBodies.length) return [];
  return Array.prototype.slice.call(table.tBodies[0].getElementsByTagName('tr'));
}

function isRowMatchingFilter(row) {
  var input = document.getElementById('allStudentsSearch');
  var filter = input ? input.value.toLowerCase().trim() : '';
  if (filter === '') return true;
  var text = row.textContent || row.innerText || '';
  return text.toLowerCase().indexOf(filter) > -1;
}

function appendEllipsis(nav) {
  var li = document.createElement('li');
  li.className = 'page-item disabled';
  li.innerHTML = '<span class="page-link">&hellip;</span>';
  nav.appendChild(li);
}

function appendPageBtn(nav, label, page, disabled, active) {
  var li = document.createElement('li');
  li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
  var a = document.createElement('a');
  a.className = 'page-link';
  a.href = 'javascript:void(0)';
  a.innerHTML = label;
  if (!disabled) {
    a.addEventListener('click', function () {
      rosterCurrentPage = page;
      renderRosterPagination();
    });
  }
  li.appendChild(a);
  nav.appendChild(li);
}

function renderRosterPagination() {
  var rows = getRosterRows();
  rosterFilteredRows = rows.filter(isRowMatchingFilter);

  var total = rosterFilteredRows.length;
  var pageSize = (rosterPageSize === 'all') ? (total || 1) : parseInt(rosterPageSize, 10);
  var totalPages = Math.max(1, Math.ceil(total / pageSize));
  if (rosterCurrentPage > totalPages) rosterCurrentPage = totalPages;
  if (rosterCurrentPage < 1) rosterCurrentPage = 1;

  var start = (rosterCurrentPage - 1) * pageSize;
  var end = Math.min(start + pageSize, total);

  // Hide everything first, then show only the current page slice
  rows.forEach(function (row) { row.style.display = 'none'; });
  rosterFilteredRows.slice(start, end).forEach(function (row) { row.style.display = ''; });

  var info = document.getElementById('rosterPageInfo');
  if (info) {
    info.textContent = total > 0
      ? 'Showing ' + (start + 1) + '\u2013' + end + ' of ' + total + ' students'
      : 'Showing 0 of 0 students';
  }

  var nav = document.getElementById('rosterPagination');
  if (!nav) return;
  nav.innerHTML = '';
  if (total === 0) return;

  appendPageBtn(nav, '&laquo;', rosterCurrentPage - 1, rosterCurrentPage <= 1, false);

  var windowSize = 5;
  var startPage = Math.max(1, rosterCurrentPage - Math.floor(windowSize / 2));
  var endPage = Math.min(totalPages, startPage + windowSize - 1);
  if (endPage - startPage < windowSize - 1) {
    startPage = Math.max(1, endPage - windowSize + 1);
  }
  if (startPage > 1) {
    appendPageBtn(nav, '1', 1, false, false);
    if (startPage > 2) appendEllipsis(nav);
  }
  for (var p = startPage; p <= endPage; p++) {
    appendPageBtn(nav, p, p, false, p === rosterCurrentPage);
  }
  if (endPage < totalPages) {
    if (endPage < totalPages - 1) appendEllipsis(nav);
    appendPageBtn(nav, totalPages, totalPages, false, false);
  }

  appendPageBtn(nav, '&raquo;', rosterCurrentPage + 1, rosterCurrentPage >= totalPages, false);
}

function setRosterPageSize(val) {
  rosterPageSize = (val === 'all') ? 'all' : parseInt(val, 10);
  rosterCurrentPage = 1;
  renderRosterPagination();
}

function filterAllStudents() {
  rosterCurrentPage = 1;
  renderRosterPagination();
}

function quickSelectStudent(regNo) {
  var input = document.getElementById('txtParticipantId');
  if (input) {
    input.value = regNo;
    fetch_participant_details_result();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
}

// Print the full filtered roster (all matching rows, not just current page),
// then restore the paginated view.
function printRoster() {
  var rows = getRosterRows();
  rows.forEach(function (row) { row.style.display = 'none'; });
  rosterFilteredRows.forEach(function (row) { row.style.display = ''; });
  var bar = document.getElementById('rosterPaginationBar');
  if (bar) bar.style.display = 'none';
  window.print();
  if (bar) bar.style.display = '';
  renderRosterPagination();
}

// Initialize pagination on page load
renderRosterPagination();
</script>