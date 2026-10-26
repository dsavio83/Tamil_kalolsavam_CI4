<div class="pagetitle d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
  <div>
    <h1 class="h4 mb-1 text-gray-800 fw-bold d-flex align-items-center gap-2">
      <i class="bi bi-patch-check-fill text-primary"></i> School Certificate Hub
    </h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0" style="font-size: 13.5px;">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome">Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>district/certificate/list_school_wise">School Wise</a></li>
        <li class="breadcrumb-item active"><?php echo htmlspecialchars($school_details[0]['school_name'] ?? 'School Certificates'); ?></li>
      </ol>
    </nav>
  </div>
  <div class="d-flex align-items-center gap-2">
    <?php if (!empty($all_schools) && is_array($all_schools)): ?>
      <div class="d-flex align-items-center gap-2 bg-white px-3 py-1.5 rounded-4 shadow-sm border">
        <label for="schoolQuickSwitcher" class="text-secondary fw-semibold text-nowrap mb-0 d-none d-md-inline" style="font-size: 13.5px;">
          <i class="bi bi-arrow-repeat text-primary me-1"></i> Switch School:
        </label>
        <select id="schoolQuickSwitcher" class="form-select form-select-sm border-0 bg-light fw-bold text-dark" 
                style="max-width: 270px; font-size: 14px;" 
                onchange="if(this.value) window.location.href='<?php echo base_url('district/certificate/get_certificate_school_wise?school_code='); ?>'+this.value;">
          <?php foreach ($all_schools as $s): ?>
            <option value="<?php echo htmlspecialchars($s['school_code']); ?>" <?php echo ($s['school_code'] == ($selected_school_code ?? $school_details[0]['school_code'] ?? '')) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($s['school_code'] . ' - ' . $s['school_name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    <?php endif; ?>
    <a href="<?php echo base_url('district/certificate/list_school_wise'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-flex align-items-center gap-2" style="font-size: 13.5px;">
      <i class="bi bi-arrow-left"></i> All Schools
    </a>
  </div>
</div>

<?php 
  $curr_school_code = $school_details[0]['school_code'] ?? $selected_school_code ?? '';
  $curr_school_name = $school_details[0]['school_name'] ?? 'School Certificates';
  $parts = $all_participants ?? [];
  $total_certs = count($parts);
  $grade_a_cnt = 0;
  $grade_b_cnt = 0;
  $grade_c_cnt = 0;
  $unique_items = [];
  foreach ($parts as $p) {
    if (($p['grade'] ?? '') === 'A') $grade_a_cnt++;
    else if (($p['grade'] ?? '') === 'B') $grade_b_cnt++;
    else if (($p['grade'] ?? '') === 'C') $grade_c_cnt++;
    if (!empty($p['item_code'])) $unique_items[$p['item_code']] = true;
  }
?>

<!-- School Hero & KPI Banner -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
  <div class="card-body p-4">
    <div class="row align-items-center g-3">
      <div class="col-lg-6 col-md-12">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-4 bg-primary bg-gradient text-white d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 54px; height: 54px;">
            <i class="bi bi-building fs-3"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 font-monospace" style="font-size: 12.5px;">
                CODE: <?php echo htmlspecialchars($curr_school_code); ?>
              </span>
              <span class="badge bg-light text-secondary border px-2.5 py-1" style="font-size: 12.5px;">
                <i class="bi bi-geo-alt-fill text-danger me-1"></i><?php echo htmlspecialchars($parts[0]['sub_district_name'] ?? 'Sub District'); ?>
              </span>
            </div>
            <h4 class="mb-0 fw-bold text-dark lh-sm" style="font-size: 1.15rem;">
              <?php echo htmlspecialchars($curr_school_name); ?>
            </h4>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6">
        <div class="d-flex align-items-center justify-content-lg-center gap-2.5">
          <div class="text-center px-3 py-2 bg-light rounded-3 border flex-fill">
            <span class="d-block fw-bold text-primary" style="font-size: 1.35rem; line-height: 1.2;"><?php echo $total_certs; ?></span>
            <small class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Eligible</small>
          </div>
          <div class="text-center px-3 py-2 bg-light rounded-3 border flex-fill">
            <span class="d-block fw-bold text-success" style="font-size: 1.35rem; line-height: 1.2;"><?php echo $grade_a_cnt; ?></span>
            <small class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Grade A</small>
          </div>
          <div class="text-center px-3 py-2 bg-light rounded-3 border flex-fill">
            <span class="d-block fw-bold text-info" style="font-size: 1.35rem; line-height: 1.2;"><?php echo count($unique_items); ?></span>
            <small class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Items</small>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 text-lg-end">
        <?php if ($total_certs > 0): ?>
          <?php echo form_open('district/certificate/get_certificate_pdf_school_wise', array('target' => '_blank', 'class' => 'd-inline-block w-100 w-lg-auto')); ?>
            <input type="hidden" name="hidSchoolCode" value="<?php echo htmlspecialchars($curr_school_code); ?>" />
            <input type="hidden" name="cmbFestType" value="" />
            <input type="hidden" name="item_code" value="" />
            <input type="hidden" name="captain_id" value="" />
            <input type="hidden" name="participant_id" value="" />
            <button type="submit" class="btn btn-success btn-lg rounded-pill px-4 shadow-sm w-100 d-inline-flex align-items-center justify-content-center gap-2" style="font-size: 14px;">
              <i class="bi bi-printer-fill fs-5"></i>
              <span class="fw-bold">Print All (<?php echo $total_certs; ?>)</span>
            </button>
          <?php echo form_close(); ?>
        <?php else: ?>
          <button type="button" class="btn btn-secondary rounded-pill px-4 w-100 disabled" style="font-size: 14px;" disabled>
            <i class="bi bi-dash-circle me-1"></i> No Certificates
          </button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Main 2-Column Dashboard -->
<div class="row g-4">
  <!-- Left Side: Interactive Filtering Card -->
  <div class="col-xl-4 col-lg-5 col-12">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white sticky-top" style="top: 1.5rem; z-index: 10;">
      <div class="card-header bg-white py-3 px-4 border-bottom border-light d-flex align-items-center gap-2">
        <i class="bi bi-funnel-fill text-primary"></i>
        <h6 class="mb-0 fw-bold text-dark" style="font-size: 15px;">Filter & Custom Print</h6>
      </div>

      <div class="card-body p-4">
        <?php if (is_array($fest) && count($fest) > 0): ?>
          <?php echo form_open('district/certificate/get_certificate_pdf_school_wise', array('id' => 'formSchoolFilter', 'target' => '_blank')); ?>
          <input type="hidden" name="hidSchoolCode" id="hidSchoolCode" value="<?php echo htmlspecialchars($curr_school_code); ?>" />

          <div class="mb-3">
            <label for="cmbFestType" class="form-label fw-semibold text-secondary d-flex align-items-center gap-2 mb-1" style="font-size: 14px;">
              <i class="bi bi-trophy-fill text-warning"></i> Festival Category
            </label>
            <?php 
              echo form_dropdown("cmbFestType", $fest, '', 
                'class="form-select rounded-3 border-secondary-subtle" style="font-size: 14px;" id="cmbFestType" onchange="get_school_items(this.value)"'); 
            ?>
          </div>

          <div class="mb-3">
            <label for="item_code" class="form-label fw-semibold text-secondary d-flex align-items-center gap-2 mb-1" style="font-size: 14px;">
              <i class="bi bi-tag-fill text-info"></i> Event / Item
            </label>
            <div id="all_item_code">
              <select class="form-select rounded-3 border-secondary-subtle" style="font-size: 14px;" name="item_code" id="item_code" onchange="get_school_captains(this.value)">
                <option value="0">All Items</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label for="captain_id" class="form-label fw-semibold text-secondary d-flex align-items-center gap-2 mb-1" style="font-size: 14px;">
              <i class="bi bi-person-badge-fill text-primary"></i> Participant / Captain
            </label>
            <div id="all_captain_id">
              <select class="form-select rounded-3 border-secondary-subtle" style="font-size: 14px;" name="captain_id" id="captain_id" onchange="get_school_group_participants(this.value)">
                <option value="0">All Participants</option>
              </select>
            </div>
          </div>

          <div class="mb-4">
            <label for="participant_id" class="form-label fw-semibold text-secondary d-flex align-items-center gap-2 mb-1" style="font-size: 14px;">
              <i class="bi bi-people-fill text-success"></i> Group Member (Pinnani)
            </label>
            <div id="all_group_participant_id">
              <select class="form-select rounded-3 border-secondary-subtle" style="font-size: 14px;" name="participant_id" id="participant_id">
                <option value="0">All Participants</option>
              </select>
            </div>
          </div>

          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary rounded-pill py-2.5 d-flex align-items-center justify-content-center gap-2 shadow-sm fw-semibold" style="font-size: 14px;">
              <i class="bi bi-printer-fill"></i> Generate Filtered PDF
            </button>
            <button type="reset" class="btn btn-light rounded-pill py-2 text-secondary btn-sm" style="font-size: 13.5px;" onclick="setTimeout(()=>get_school_items('0'), 100);">
              <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
            </button>
          </div>
          <?php echo form_close(); ?>
        <?php else: ?>
          <div class="text-center py-4 text-muted">
            <i class="bi bi-exclamation-circle fs-3 d-block mb-2 text-warning"></i>
            <small style="font-size: 13.5px;">No festival data available for this school.</small>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Right Side: Eligible Participants Roster -->
  <div class="col-xl-8 col-lg-7 col-12">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
      <div class="card-header bg-white py-3 px-4 border-bottom border-light d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-people-fill text-primary"></i>
          <h6 class="mb-0 fw-bold text-dark" style="font-size: 15px;">Eligible Participants Roster</h6>
          <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 ms-1" style="font-size: 12px;"><?php echo $total_certs; ?></span>
        </div>
        <div>
          <div class="input-group input-group-sm" style="max-width: 220px;">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" id="rosterSearch" class="form-control bg-light border-start-0" placeholder="Filter roster..." onkeyup="filterRosterTable()" style="font-size: 13.5px;">
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <?php if (!empty($parts) && count($parts) > 0): ?>
          <div class="table-responsive">
            <table class="table align-middle table-hover mb-0" id="rosterTable" style="font-size: 14px;">
              <thead class="table-light text-uppercase text-secondary" style="font-size: 12.5px; letter-spacing: 0.5px;">
                <tr>
                  <th class="ps-4 py-3">Participant</th>
                  <th class="py-3">Item / Event</th>
                  <th class="text-center py-3">Grade</th>
                  <th class="text-center py-3">Rank</th>
                  <th class="text-end pe-4 py-3">Action</th>
                </tr>
              </thead>
              <tbody class="border-top-0">
                <?php foreach ($parts as $idx => $p): ?>
                  <tr>
                    <td class="ps-4 py-3">
                      <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 36px; height: 36px; font-size: 13.5px;">
                          <?php echo strtoupper(substr($p['participant_name'] ?? 'P', 0, 1)); ?>
                        </div>
                        <div>
                          <div class="fw-semibold text-dark" style="font-size: 14px;"><?php echo htmlspecialchars($p['participant_name']); ?></div>
                          <div class="text-muted small" style="font-size: 12.5px;">
                            <span>Reg: <strong>#<?php echo htmlspecialchars($p['participant_id']); ?></strong></span>
                            <?php if (!empty($p['class'])): ?>
                              <span>• Class: <?php echo htmlspecialchars($p['class']); ?></span>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="py-3">
                      <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border font-monospace px-2 py-1" style="font-size: 12.5px;">
                          <?php echo htmlspecialchars($p['item_code']); ?>
                        </span>
                        <span class="fw-medium text-secondary" style="font-size: 14px;"><?php echo htmlspecialchars($p['item_name']); ?></span>
                      </div>
                      <small class="text-muted d-block mt-0.5" style="font-size: 12px;">
                        <?php echo htmlspecialchars(str_replace('TAMIL KALAIVIZHA', '', $p['fest_name'] ?? '')); ?>
                      </small>
                    </td>
                    <td class="text-center py-3">
                      <?php 
                        $grade = $p['grade'] ?? '';
                        if ($grade === 'A') {
                          echo '<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-bold" style="font-size: 12px;">Grade A</span>';
                        } else if ($grade === 'B') {
                          echo '<span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 fw-bold" style="font-size: 12px;">Grade B</span>';
                        } else if ($grade === 'C') {
                          echo '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 fw-bold" style="font-size: 12px;">Grade C</span>';
                        } else {
                          echo '<span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 12px;">N/A</span>';
                        }
                      ?>
                    </td>
                    <td class="text-center py-3">
                      <?php 
                        $rank = (int)($p['rank'] ?? 0);
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
                      <?php echo form_open('district/certificate/get_certificate_pdf_school_wise', array('target' => '_blank', 'class' => 'd-inline')); ?>
                        <input type="hidden" name="hidSchoolCode" value="<?php echo htmlspecialchars($curr_school_code); ?>" />
                        <input type="hidden" name="item_code" value="<?php echo htmlspecialchars($p['item_code']); ?>" />
                        <input type="hidden" name="participant_id" value="<?php echo htmlspecialchars($p['participant_id']); ?>" />
                        <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1.5 shadow-none" style="font-size: 13px;" title="Print this participant certificate">
                          <i class="bi bi-printer"></i> Print
                        </button>
                      <?php echo form_close(); ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="text-center py-5">
            <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center p-3 mb-2" style="width: 52px; height: 52px;">
              <i class="bi bi-inbox fs-4"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">No Participants Eligible for Certificates</h6>
            <p class="text-muted small mb-0" style="font-size: 13px;">There are currently no finalized or confirmed results with grades for this school.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
function filterRosterTable() {
  const input = document.getElementById('rosterSearch');
  const filter = input.value.toLowerCase();
  const table = document.getElementById('rosterTable');
  if (!table) return;
  const rows = table.getElementsByTagName('tr');

  for (let i = 1; i < rows.length; i++) {
    const text = rows[i].textContent || rows[i].innerText;
    if (text.toLowerCase().indexOf(filter) > -1) {
      rows[i].style.display = '';
    } else {
      rows[i].style.display = 'none';
    }
  }
}
</script>
