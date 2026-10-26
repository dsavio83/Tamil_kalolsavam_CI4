<?php 
  $cur_item_id = $item_id ?? $this->input->post('hidItemId') ?? '';
  $cur_item_name = $item_details[0]['item_name'] ?? 'Item Certificates';
  $is_group = ($item_type === 'G');
  $parts = $item_participants ?? [];
  $total_eligible = count($parts);
?>

<div class="pagetitle d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
  <div>
    <h1 class="h4 mb-1 text-gray-800 fw-bold d-flex align-items-center gap-2">
      <i class="bi bi-patch-check-fill text-primary"></i> Item Certificate Hub
    </h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0" style="font-size: 13.5px;">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome">Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>certificate/certificate/list_item_wise">Item Wise</a></li>
        <li class="breadcrumb-item active"><?php echo htmlspecialchars($cur_item_name); ?></li>
      </ol>
    </nav>
  </div>
  <div>
    <a href="<?php echo base_url('certificate/certificate/list_item_wise'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-flex align-items-center gap-2" style="font-size: 13.5px;">
      <i class="bi bi-arrow-left"></i> All Items
    </a>
  </div>
</div>

<!-- Item Hero & KPI Banner -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
  <div class="card-body p-4">
    <div class="row align-items-center g-3">
      <div class="col-lg-7 col-md-12">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-4 bg-primary bg-gradient text-white d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 54px; height: 54px;">
            <i class="bi bi-award fs-3"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 font-monospace" style="font-size: 12.5px;">
                CODE: <?php echo htmlspecialchars($cur_item_id); ?>
              </span>
              <span class="badge <?php echo $is_group ? 'bg-info-subtle text-info border-info-subtle' : 'bg-secondary-subtle text-secondary border-secondary-subtle'; ?> border px-2.5 py-1" style="font-size: 12.5px;">
                <i class="bi <?php echo $is_group ? 'bi-people-fill' : 'bi-person-fill'; ?> me-1"></i><?php echo $is_group ? 'Group Event' : 'Single Event'; ?>
              </span>
            </div>
            <h4 class="mb-0 fw-bold text-dark lh-sm" style="font-size: 1.15rem;">
              <?php echo htmlspecialchars($cur_item_name); ?>
            </h4>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-4 text-center">
        <div class="p-2.5 bg-light rounded-3 border">
          <span class="d-block fw-bold text-primary" style="font-size: 1.35rem; line-height: 1.2;"><?php echo $total_eligible; ?></span>
          <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Eligible</span>
        </div>
      </div>

      <div class="col-lg-3 col-md-8 text-lg-end">
        <?php if ($total_eligible > 0): ?>
          <?php echo form_open('certificate/certificate/get_certificate_pdf', array('target' => '_blank', 'class' => 'd-inline-block w-100 w-lg-auto')); ?>
            <input type="hidden" name="hidItemId" value="<?php echo htmlspecialchars($cur_item_id); ?>" />
            <input type="hidden" name="hidItemType" value="<?php echo htmlspecialchars($item_type ?? ''); ?>" />
            <input type="hidden" name="captain_id" value="0" />
            <button type="submit" class="btn btn-success btn-lg rounded-pill px-4 shadow-sm w-100 d-inline-flex align-items-center justify-content-center gap-2" style="font-size: 14px;">
              <i class="bi bi-printer-fill fs-5"></i>
              <span class="fw-bold">Print All (<?php echo $total_eligible; ?>)</span>
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

<!-- Main 2-Column Section -->
<div class="row g-4">
  <!-- Left Column: Filter / Custom Selection -->
  <div class="col-xl-4 col-lg-5 col-12">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white sticky-top" style="top: 1.5rem; z-index: 10;">
      <div class="card-header bg-white py-3 px-4 border-bottom border-light d-flex align-items-center gap-2">
        <i class="bi bi-funnel-fill text-primary"></i>
        <h6 class="mb-0 fw-bold text-dark" style="font-size: 15px;">Custom Filter & Print</h6>
      </div>

      <div class="card-body p-4">
        <?php if (is_array(@$captain_detail) && count(@$captain_detail) > 0): ?>
          <?php echo form_open('certificate/certificate/get_certificate_pdf', array('id' => 'formIRL', 'target' => '_blank')); ?>
          <input type="hidden" name="hidUservalue" id="hidUservalue" value="SHOW" />
          <input type="hidden" name="hidItemId" id="hidItemId" value="<?php echo htmlspecialchars($cur_item_id); ?>" />
          <input type="hidden" name="hidItemType" id="hidItemType" value="<?php echo htmlspecialchars($item_type ?? ''); ?>" />

          <div class="mb-3">
            <label for="captain_id" class="form-label fw-semibold text-secondary d-flex align-items-center gap-2 mb-1" style="font-size: 14px;">
              <i class="bi bi-person-badge-fill text-primary"></i> 
              <span><?php echo $is_group ? 'Group Captain / School' : 'Participant'; ?></span>
            </label>
            <?php 
              echo form_dropdown("captain_id", @$captain_detail, '', 
                'class="form-select rounded-3 border-secondary-subtle" style="font-size: 14px;" id="captain_id" ' . ($javascript_code ?? ''));
            ?>
          </div>

          <?php if ($is_group): ?>
            <div class="mb-3" id="all_participant_tr_id">
              <label for="participant_id" class="form-label fw-semibold text-secondary d-flex align-items-center gap-2 mb-1" style="font-size: 14px;">
                <i class="bi bi-people-fill text-success"></i> <span>Group Member (Pinnani)</span>
              </label>
              <div id="all_participant_id">
                <select class="form-select rounded-3 border-secondary-subtle" style="font-size: 14px;" name="participant_id" id="participant_id">
                  <option value="0">All Group Participants</option>
                </select>
              </div>
            </div>
          <?php endif; ?>

          <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary rounded-pill py-2.5 d-flex align-items-center justify-content-center gap-2 shadow-sm fw-semibold" style="font-size: 14px;">
              <i class="bi bi-printer-fill"></i> Generate Certificate (PDF)
            </button>
            <a href="<?php echo base_url('certificate/certificate/list_item_wise'); ?>" class="btn btn-light rounded-pill py-2 text-secondary btn-sm text-center" style="font-size: 13.5px;">
              Back to Item List
            </a>
          </div>
          <?php echo form_close(); ?>
        <?php else: ?>
          <div class="text-center py-4 text-muted">
            <i class="bi bi-info-circle fs-3 d-block mb-2 text-warning"></i>
            <small style="font-size: 13.5px;">No eligible participant records found for this item.</small>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Right Column: Participant Certificate Roster -->
  <div class="col-xl-8 col-lg-7 col-12">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
      <div class="card-header bg-white py-3 px-4 border-bottom border-light d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-people-fill text-primary"></i>
          <h6 class="mb-0 fw-bold text-dark" style="font-size: 15px;">Eligible Participants Roster</h6>
          <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 ms-1" style="font-size: 12px;"><?php echo $total_eligible; ?></span>
        </div>
        <div>
          <div class="input-group input-group-sm" style="max-width: 220px;">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" id="itemRosterSearch" class="form-control bg-light border-start-0" placeholder="Filter..." onkeyup="filterItemRoster()" style="font-size: 13.5px;">
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <?php if (!empty($parts) && count($parts) > 0): ?>
          <div class="table-responsive">
            <table class="table align-middle table-hover mb-0" id="itemRosterTable" style="font-size: 14px;">
              <thead class="table-light text-uppercase text-secondary" style="font-size: 12.5px; letter-spacing: 0.5px;">
                <tr>
                  <th class="ps-4 py-3">Participant</th>
                  <th class="py-3">School</th>
                  <th class="text-center py-3">Grade</th>
                  <th class="text-center py-3">Rank</th>
                  <th class="text-end pe-4 py-3">Action</th>
                </tr>
              </thead>
              <tbody class="border-top-0">
                <?php foreach ($parts as $p): ?>
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
                    <td class="py-3" style="font-size: 13.5px;">
                      <div class="fw-medium text-dark"><?php echo htmlspecialchars($p['school_name'] ?? ''); ?></div>
                      <?php if (!empty($p['sub_district_name'])): ?>
                        <small class="text-primary d-block fw-semibold" style="font-size: 11.5px;"><i class="bi bi-geo-alt-fill me-1"></i><?php echo htmlspecialchars($p['sub_district_name']); ?></small>
                      <?php endif; ?>
                      <small class="text-muted" style="font-size: 12px;"><?php echo htmlspecialchars($p['school_code'] ?? ''); ?></small>
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
                          echo '<span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 12px;">—</span>';
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
                      <?php echo form_open('certificate/certificate/get_certificate_pdf', array('target' => '_blank', 'class' => 'd-inline')); ?>
                        <input type="hidden" name="hidItemId" value="<?php echo htmlspecialchars($cur_item_id); ?>" />
                        <input type="hidden" name="hidItemType" value="<?php echo htmlspecialchars($item_type ?? ''); ?>" />
                        <input type="hidden" name="captain_id" value="<?php echo htmlspecialchars($p['participant_id']); ?>" />
                        <input type="hidden" name="participant_id" value="<?php echo htmlspecialchars($p['participant_id']); ?>" />
                        <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1.5 shadow-none" style="font-size: 13px;" title="Print certificate for this student">
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
          <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
            <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">No Participants in this Item</h6>
            <p class="small mb-0" style="font-size: 13px;">No results or certificate eligibilities have been confirmed yet.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
function filterItemRoster() {
  const input = document.getElementById('itemRosterSearch');
  const filter = input.value.toLowerCase();
  const table = document.getElementById('itemRosterTable');
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
