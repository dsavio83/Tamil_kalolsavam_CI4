<div class="pagetitle mb-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
        <span class="rounded-3 bg-primary-subtle text-primary p-2 d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 42px; height: 42px;">
          <i class="bi bi-cloud-arrow-down-fill fs-5"></i>
        </span>
        <span>District Winner Import &amp; Promotion Engine</span>
      </h1>
      <nav>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
          <li class="breadcrumb-item">District</li>
          <li class="breadcrumb-item active">Winner Import</li>
        </ol>
      </nav>
    </div>
    <div>
      <a href="<?php echo base_url(); ?>welcome" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-sm">
        <i class="bi bi-arrow-left"></i>
        <span>Dashboard</span>
      </a>
    </div>
  </div>
</div>

<!-- Hero Banner: District Intake Info -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #065f46 0%, #059669 60%, #10b981 100%);">
  <div class="card-body p-4 text-white">
    <div class="row align-items-center g-3">
      <div class="col-12 col-md-8">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-white bg-opacity-20 rounded-circle p-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 58px; height: 58px; min-width: 58px;">
            <i class="bi bi-shield-check fs-2 text-white"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
              <span class="badge bg-white text-dark fw-bold shadow-sm rounded-pill px-3 py-1 small font-monospace">Revenue District: <?php echo esc($district_code ?? session()->get('DISTRICT')); ?></span>
              <span class="badge bg-warning text-dark fw-bold shadow-sm rounded-pill px-3 py-1 small font-monospace">Role: District Admin</span>
            </div>
            <h4 class="fw-bold mb-0 text-white">Sub-District Winners &rarr; District Qualification Roster</h4>
            <p class="mb-0 text-white-50 small mt-1">Authoritative verification against live Sub-District results. Automatic photo processing with ZipSlip &amp; malware defense.</p>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-4 text-md-end">
        <span class="badge bg-white text-dark fw-semibold shadow-sm px-3 py-2 rounded-pill small font-monospace">
          <i class="bi bi-cpu-fill me-1 text-success"></i> Real-time DB Verification
        </span>
      </div>
    </div>
  </div>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
    <div><?php echo esc(session()->getFlashdata('success')); ?></div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
    <div><?php echo esc(session()->getFlashdata('error')); ?></div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if (!empty($preview)): ?>
  <!-- PREVIEW SECTION: Validation Metrics & Confirmation -->
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom border-light-subtle py-3 px-4">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-primary rounded-pill px-3 py-1 font-monospace">JOB: <?php echo esc($preview['job_id']); ?></span>
          <h5 class="fw-bold mb-0 text-dark">Step 2: Review &amp; Authoritative Qualification Preview</h5>
        </div>
        <div class="d-flex align-items-center gap-2">
          <?php if ($preview['has_errors']): ?>
            <a href="<?php echo esc($preview['error_download_url']); ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 d-inline-flex align-items-center gap-1.5 shadow-sm">
              <i class="bi bi-file-earmark-arrow-down-fill"></i>
              <span>Download Error CSV (<?php echo (int)$preview['rejected_count']; ?>)</span>
            </a>
          <?php endif; ?>
          <a href="<?php echo base_url('district/import'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-x-circle me-1"></i> Cancel
          </a>
        </div>
      </div>
    </div>
    <div class="card-body p-4">
      <!-- Summary Metric Cards -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
          <div class="card border-0 shadow-xs rounded-3 p-3 text-center bg-light">
            <div class="text-muted small fw-semibold">Total Rows</div>
            <div class="fs-4 fw-bold text-dark font-monospace"><?php echo (int)$preview['total_rows']; ?></div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
          <div class="card border-0 shadow-xs rounded-3 p-3 text-center bg-success-subtle text-success">
            <div class="small fw-semibold">Qualified</div>
            <div class="fs-4 fw-bold font-monospace"><?php echo (int)$preview['qualified_count']; ?></div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
          <div class="card border-0 shadow-xs rounded-3 p-3 text-center bg-info-subtle text-info">
            <div class="small fw-semibold">Skipped (Dup)</div>
            <div class="fs-4 fw-bold font-monospace"><?php echo (int)$preview['skipped_count']; ?></div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
          <div class="card border-0 shadow-xs rounded-3 p-3 text-center bg-danger-subtle text-danger">
            <div class="small fw-semibold">Rejected</div>
            <div class="fs-4 fw-bold font-monospace"><?php echo (int)$preview['rejected_count']; ?></div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
          <div class="card border-0 shadow-xs rounded-3 p-3 text-center bg-primary-subtle text-primary">
            <div class="small fw-semibold">Photos Matched</div>
            <div class="fs-4 fw-bold font-monospace"><?php echo (int)$preview['photos_matched']; ?></div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
          <div class="card border-0 shadow-xs rounded-3 p-3 text-center bg-warning-subtle text-warning">
            <div class="small fw-semibold">Photos Missing</div>
            <div class="fs-4 fw-bold font-monospace"><?php echo (int)$preview['photos_missing']; ?></div>
          </div>
        </div>
      </div>

      <!-- Action Confirmation Box -->
      <?php if ($preview['qualified_count'] > 0): ?>
        <div class="alert alert-success rounded-4 border-0 p-3 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-shield-check fs-3 text-success"></i>
            <div>
              <strong><?php echo (int)$preview['qualified_count']; ?> winners</strong> verified against authoritative Sub-District records.
              <div class="small text-muted">Clicking confirm will promote participants, assign items, and link photographs into District tables.</div>
            </div>
          </div>
          <form method="post" action="<?php echo base_url('district/import/confirm'); ?>" class="mb-0">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="job_id" value="<?php echo esc($preview['job_id']); ?>">
            <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
              <i class="bi bi-check-circle-fill"></i>
              <span>Confirm &amp; Commit Qualification</span>
            </button>
          </form>
        </div>
      <?php else: ?>
        <div class="alert alert-warning rounded-4 border-0 p-3 mb-4 shadow-sm d-flex align-items-center gap-2">
          <i class="bi bi-exclamation-triangle-fill fs-4 text-warning"></i>
          <div>
            <strong>No new qualifying records to commit.</strong>
            <?php if ($preview['skipped_count'] > 0): ?>
              All valid rows were already imported previously.
            <?php endif; ?>
            <?php if ($preview['rejected_count'] > 0): ?>
              <?php echo (int)$preview['rejected_count']; ?> rows failed authoritative validation. Download the Error CSV for details.
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Sample Preview Table -->
      <h6 class="fw-bold text-dark mb-3">Participant Qualification Preview (Showing first <?php echo count($preview['sample_rows']); ?> records):</h6>
      <div class="table-responsive rounded-3 border border-light-subtle">
        <table class="table table-hover align-middle mb-0 small">
          <thead class="table-light">
            <tr>
              <th scope="col" style="width: 50px;">#</th>
              <th scope="col">Status</th>
              <th scope="col">Participant</th>
              <th scope="col">School</th>
              <th scope="col">Item</th>
              <th scope="col" class="text-center">Class</th>
              <th scope="col" class="text-center">Rank</th>
              <th scope="col" class="text-center">Grade</th>
              <th scope="col">Validation / Reason</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($preview['sample_rows'] as $r): ?>
              <?php
                $row = $r['row'];
                $status = $r['status'];
                $badgeClass = ($status === 'QUALIFIED') ? 'bg-success' : (($status === 'SKIPPED') ? 'bg-info' : 'bg-danger');
              ?>
              <tr>
                <td class="font-monospace text-muted"><?php echo $r['index'] + 1; ?></td>
                <td><span class="badge <?php echo $badgeClass; ?> rounded-pill px-2.5 py-1 font-monospace"><?php echo esc($status); ?></span></td>
                <td>
                  <div class="fw-bold text-dark"><?php echo esc($row['participant_name'] ?? ''); ?></div>
                  <div class="text-muted font-monospace" style="font-size: 0.75rem;">Admn: <?php echo esc($row['source_admn_no'] ?? ''); ?></div>
                </td>
                <td>
                  <div class="font-monospace fw-semibold"><?php echo esc($row['participant_school_code'] ?? ''); ?></div>
                  <div class="text-muted text-truncate" style="max-width: 180px; font-size: 0.75rem;"><?php echo esc($row['participant_school_name'] ?? ''); ?></div>
                </td>
                <td>
                  <div class="fw-semibold text-dark"><?php echo esc($row['item_name'] ?? ''); ?></div>
                  <div class="text-muted font-monospace" style="font-size: 0.75rem;">Code: <?php echo esc($row['item_code'] ?? ''); ?> (<?php echo esc($row['item_type'] ?? 'S'); ?>)</div>
                </td>
                <td class="text-center font-monospace"><?php echo esc($row['participant_class'] ?? ''); ?></td>
                <td class="text-center font-monospace fw-bold"><?php echo esc($row['rank'] ?? ''); ?></td>
                <td class="text-center"><span class="badge bg-primary-subtle text-primary fw-bold"><?php echo esc($row['grade'] ?? ''); ?></span></td>
                <td>
                  <?php if ($status === 'QUALIFIED'): ?>
                    <span class="text-success"><i class="bi bi-check-circle me-1"></i> Verified Live DB</span>
                  <?php elseif ($status === 'SKIPPED'): ?>
                    <span class="text-info"><i class="bi bi-info-circle me-1"></i> Already in District</span>
                  <?php else: ?>
                    <span class="text-danger fw-semibold"><i class="bi bi-x-circle me-1"></i> <?php echo esc($r['reason'] ?? ''); ?></span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php else: ?>
  <!-- UPLOAD FORM SECTION -->
  <div class="row g-4 mb-4">
    <div class="col-12 col-lg-8">
      <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-header bg-white border-bottom border-light-subtle py-3 px-4">
          <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-cloud-arrow-up text-primary"></i>
            <span>Step 1: Upload Winner Artifacts</span>
          </h5>
        </div>
        <div class="card-body p-4">
          <!-- Nav Tabs for Upload Options -->
          <ul class="nav nav-pills mb-4 nav-justified bg-light p-1.5 rounded-3" id="importTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active rounded-3 py-2 fw-semibold" id="unified-tab" data-bs-toggle="pill" data-bs-target="#tab-unified" type="button" role="tab">
                <i class="bi bi-box-seam-fill me-1.5 text-primary"></i> Option 1: Unified Winner Bundle (.zip)
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link rounded-3 py-2 fw-semibold" id="individual-tab" data-bs-toggle="pill" data-bs-target="#tab-individual" type="button" role="tab">
                <i class="bi bi-files me-1.5 text-secondary"></i> Option 2: Standalone CSV + Photo ZIP
              </button>
            </li>
          </ul>

          <div class="tab-content" id="importTabContent">
            <!-- TAB 1: Unified Bundle -->
            <div class="tab-pane fade show active" id="tab-unified" role="tabpanel">
              <form method="post" action="<?php echo base_url('district/import/preview'); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="mb-4">
                  <label class="form-label fw-bold text-dark">
                    Unified Winner Package Archive (.zip) <span class="text-danger">*</span>
                  </label>
                  <input type="file" name="unified_package" class="form-control form-control-lg rounded-3" accept=".zip" required>
                  <div class="form-text text-muted">
                    Upload the unified package generated by Sub-District export (contains <code>winners.csv</code>, <code>photos/</code>, and <code>manifest.json</code>).
                  </div>
                </div>

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top">
                  <div class="text-muted small">
                    <i class="bi bi-shield-lock text-success me-1"></i> Checked for ZipSlip, bombs &amp; MIME validation
                  </div>
                  <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="bi bi-search"></i>
                    <span>Verify &amp; Preview Qualification</span>
                  </button>
                </div>
              </form>
            </div>

            <!-- TAB 2: Individual Files -->
            <div class="tab-pane fade" id="tab-individual" role="tabpanel">
              <form method="post" action="<?php echo base_url('district/import/preview'); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                  <label class="form-label fw-bold text-dark">
                    Certified Winners CSV (.csv) <span class="text-danger">*</span>
                  </label>
                  <input type="file" name="csv_file" class="form-control rounded-3" accept=".csv" required>
                  <div class="form-text text-muted">
                    Exported Sub-District CSV file conforming to <code>TK25-DISTRICT-CSV-V1</code> specification.
                  </div>
                </div>

                <div class="mb-4">
                  <label class="form-label fw-bold text-dark">
                    Photos Archive ZIP (.zip) <span class="text-muted small fw-normal">(Optional)</span>
                  </label>
                  <input type="file" name="photos_zip" class="form-control rounded-3" accept=".zip">
                  <div class="form-text text-muted">
                    ZIP archive containing winner photos named <code>{school_code}_{admn_no}.jpg</code> or <code>{admn_no}.jpg</code>.
                  </div>
                </div>

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top">
                  <div class="text-muted small">
                    <i class="bi bi-shield-lock text-success me-1"></i> Direct binary scan of image headers
                  </div>
                  <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="bi bi-search"></i>
                    <span>Verify &amp; Preview Qualification</span>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Side: Rules & Guidelines -->
    <div class="col-12 col-lg-4">
      <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
        <div class="card-header bg-white border-bottom border-light-subtle py-3 px-4">
          <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-info-circle-fill text-primary"></i>
            <span>Qualification Rules &amp; Security</span>
          </h6>
        </div>
        <div class="card-body p-4">
          <div class="d-flex flex-column gap-3 small">
            <div class="d-flex gap-2">
              <i class="bi bi-database-fill-check text-primary fs-5 mt-n1"></i>
              <div>
                <strong>Authoritative DB Verification:</strong> CSV inputs are checked against live <code>sd_result_master</code> and <code>sd_result_publish</code> tables.
              </div>
            </div>

            <div class="d-flex gap-2">
              <i class="bi bi-ban text-danger fs-5 mt-n1"></i>
              <div>
                <strong>Lower Primary Hard Exclusion:</strong> Festival 1 (Classes 1&ndash;4) items cannot qualify for District. Any LP rows are automatically rejected.
              </div>
            </div>

            <div class="d-flex gap-2">
              <i class="bi bi-trophy-fill text-warning fs-5 mt-n1"></i>
              <div>
                <strong>Rank 1 + Grade A:</strong> Only participants with confirmed Rank 1, Grade 'A', and non-withheld status qualify for District promotion.
              </div>
            </div>

            <div class="d-flex gap-2">
              <i class="bi bi-shield-lock-fill text-success fs-5 mt-n1"></i>
              <div>
                <strong>Anti-ZipSlip &amp; Content Inspection:</strong> Archives are guarded against path traversal, zip-bombs, disguised PHP scripts, and non-image MIME types.
              </div>
            </div>

            <div class="d-flex gap-2">
              <i class="bi bi-arrow-repeat text-info fs-5 mt-n1"></i>
              <div>
                <strong>Safe Re-import:</strong> Duplicate uploads are detected via unique keys and reported as skipped, ensuring zero duplicates.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- RECENT IMPORT AUDIT HISTORY -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
  <div class="card-header bg-white border-bottom border-light-subtle py-3 px-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
      <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
        <i class="bi bi-clock-history text-secondary"></i>
        <span>Recent Import Audit Trail</span>
      </h5>
      <span class="badge bg-light text-muted rounded-pill px-3 py-1 font-monospace">Revenue District: <?php echo esc($district_code ?? session()->get('DISTRICT')); ?></span>
    </div>
  </div>
  <div class="card-body p-4">
    <?php if (!empty($history)): ?>
      <div class="table-responsive rounded-3 border border-light-subtle">
        <table class="table table-hover align-middle mb-0 small">
          <thead class="table-light">
            <tr>
              <th scope="col">Import ID</th>
              <th scope="col">Source Sub-District</th>
              <th scope="col">Date &amp; Time</th>
              <th scope="col" class="text-center">Total</th>
              <th scope="col" class="text-center">Valid</th>
              <th scope="col" class="text-center">Rejected</th>
              <th scope="col" class="text-center">Photos Linked</th>
              <th scope="col">Imported By</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($history as $h): ?>
              <tr>
                <td class="font-monospace fw-bold text-dark"><?php echo esc($h['import_id']); ?></td>
                <td>
                  <span class="badge bg-light text-dark font-monospace"><?php echo esc($h['sub_district_name'] ?? ('SD ' . $h['sub_district_code'])); ?></span>
                </td>
                <td class="text-muted"><?php echo esc(date('d M Y, h:i A', strtotime($h['imported_at']))); ?></td>
                <td class="text-center font-monospace fw-semibold"><?php echo (int)$h['total_rows']; ?></td>
                <td class="text-center font-monospace fw-bold text-success"><?php echo (int)$h['valid_rows']; ?></td>
                <td class="text-center font-monospace fw-bold <?php echo ((int)$h['rejected_rows'] > 0) ? 'text-danger' : 'text-muted'; ?>">
                  <?php echo (int)$h['rejected_rows']; ?>
                </td>
                <td class="text-center font-monospace text-primary"><?php echo (int)$h['photos_matched']; ?></td>
                <td class="text-muted small"><?php echo esc($h['imported_by_username'] ?? 'Admin'); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="text-center py-4 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
        <p class="mb-0">No previous District import batches recorded for this Revenue District.</p>
      </div>
    <?php endif; ?>
  </div>
</div>