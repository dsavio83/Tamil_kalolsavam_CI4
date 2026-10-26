<div class="pagetitle mb-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
        <span class="rounded-3 bg-primary-subtle text-primary p-2 d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 42px; height: 42px;">
          <i class="bi bi-printer-fill fs-5"></i>
        </span>
        <span>District Certificates Generation &amp; Print</span>
      </h1>
      <nav>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
          <li class="breadcrumb-item">District</li>
          <li class="breadcrumb-item active">Print Certificates</li>
        </ol>
      </nav>
    </div>
    <div class="d-flex gap-2">
      <a href="<?php echo base_url('district/certificate'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-sm">
        <i class="bi bi-sliders"></i>
        <span>Coordinate Template</span>
      </a>
    </div>
  </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-3">
    <form method="GET" action="<?php echo base_url('district/certificate/print_list'); ?>" class="row g-2 align-items-center">
      <div class="col-12 col-md-5">
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-light">Filter by Event</span>
          <select name="item_code" class="form-select" onchange="this.form.submit()">
            <option value="">-- All Competitions --</option>
            <?php if (!empty($items)): ?>
              <?php foreach ($items as $it): ?>
                <option value="<?php echo esc($it['item_code']); ?>" <?php echo (!empty($filters['item_code']) && (int)$filters['item_code'] === (int)$it['item_code']) ? 'selected' : ''; ?>>
                  #<?php echo esc($it['item_code']); ?> - <?php echo esc($it['item_name']); ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>
      </div>
      <div class="col-12 col-md-7 text-md-end">
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-monospace small">
          Eligible Awardees: <strong><?php echo count($certificates ?? []); ?></strong>
        </span>
      </div>
    </form>
  </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
  <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between">
    <h5 class="card-title mb-0 fw-bold text-dark">Awarded Contestants (Grade A/B/C &amp; Ranks 1/2/3)</h5>
    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" onclick="window.print()">
      <i class="bi bi-printer me-1"></i> Print View
    </button>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th class="ps-4">Cert #</th>
            <th>Contestant</th>
            <th>Competition</th>
            <th>Sub-District &amp; School</th>
            <th>Rank</th>
            <th class="pe-4">Grade</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($certificates)): ?>
            <?php foreach ($certificates as $c): ?>
              <tr>
                <td class="ps-4 font-monospace fw-bold text-primary">
                  #<?php echo esc($c['certificate_id']); ?>
                </td>
                <td>
                  <div class="fw-bold text-dark"><?php echo esc($c['participant_name']); ?></div>
                  <small class="text-muted">Admn: <?php echo esc($c['admn_no']); ?></small>
                </td>
                <td>
                  <span class="fw-semibold text-dark"><?php echo esc($c['item_name']); ?></span>
                  <small class="text-muted d-block"><?php echo esc($c['fest_name']); ?></small>
                </td>
                <td>
                  <div class="fw-semibold text-dark small"><?php echo esc($c['school_name']); ?></div>
                  <small class="text-muted"><?php echo esc($c['sub_district_name']); ?></small>
                </td>
                <td>
                  <?php if ((int)$c['rank'] === 1): ?>
                    <span class="badge bg-warning text-dark fw-bold rounded-pill px-2.5 py-1">1st Rank</span>
                  <?php elseif ((int)$c['rank'] === 2): ?>
                    <span class="badge bg-secondary text-white rounded-pill px-2.5 py-1">2nd Rank</span>
                  <?php elseif ((int)$c['rank'] === 3): ?>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">3rd Rank</span>
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
                <td class="pe-4">
                  <span class="badge bg-success rounded-pill px-2.5 py-1 fw-bold"><?php echo esc($c['grade']); ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="bi bi-patch-question fs-1 text-secondary opacity-50 d-block mb-2"></i>
                <span>No certificate records found matching criteria.</span>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
