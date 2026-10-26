<?php
/**
 * Modern District Sub-District Import & Qualification Interface
 *
 * Provides a stunning, responsive two-step workflow:
 * 1. Upload Unified Winner Package (.zip) OR CSV + Photos ZIP
 * 2. Real-time Authoritative Verification Preview & Stats
 * 3. Transactional Ingestion into District Tables (d_*)
 */
?>
<div class="pagetitle mb-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
        <span class="rounded-3 bg-primary-subtle text-primary p-2 d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 44px; height: 44px;">
          <i class="bi bi-cloud-arrow-down-fill fs-4"></i>
        </span>
        <span>Sub-District Winners Import &amp; Qualification</span>
      </h1>
      <nav>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
          <li class="breadcrumb-item">Settings</li>
          <li class="breadcrumb-item active">Import Sub-District Data</li>
        </ol>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="<?php echo base_url(); ?>district/stage" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-2 shadow-xs d-inline-flex align-items-center gap-1.5">
        <i class="bi bi-layers-fill"></i>
        <span>District Stages</span>
      </a>
      <a href="<?php echo base_url(); ?>welcome" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 shadow-xs d-inline-flex align-items-center gap-1.5">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
      </a>
    </div>
  </div>
</div>

<!-- Hero District Status Banner -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0d3b66 0%, #006466 50%, #065a60 100%);">
  <div class="card-body p-4 text-white">
    <div class="row align-items-center g-3">
      <div class="col-12 col-lg-7">
        <div class="d-flex align-items-start gap-3">
          <div class="bg-white bg-opacity-20 rounded-4 p-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 62px; height: 62px; min-width: 62px;">
            <i class="bi bi-trophy-fill fs-2 text-warning"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
              <span class="badge bg-white text-dark fw-bold rounded-pill px-3 py-1 shadow-sm font-monospace">
                <i class="bi bi-geo-alt-fill text-danger me-1"></i> Revenue District: <?php echo esc($district_info['rev_district_name'] ?? ('District ' . ($district_code ?? 9))); ?> (<?php echo esc($district_code ?? 9); ?>)
              </span>
              <span class="badge bg-warning text-dark fw-bold rounded-pill px-3 py-1 shadow-sm font-monospace">
                <i class="bi bi-person-badge-fill me-1"></i> <?php echo esc(($user_group === 'W') ? 'Webmaster / Superadmin' : 'District Admin'); ?>
              </span>
            </div>
            <h4 class="fw-bold mb-1 text-white">Sub-District Winners &rarr; District Kalolsavam Roster</h4>
            <p class="mb-0 text-white-50 small">
              Upload exported packages from Sub-Districts, verify qualification data, and automatically ingest validated winners into District operational tables (<code class="text-white fw-bold">d_*</code> tables).
            </p>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-5">
        <div class="row g-2 text-center">
          <div class="col-4">
            <div class="p-2.5 bg-white bg-opacity-10 rounded-3 border border-white border-opacity-20 backdrop-blur">
              <div class="text-white-50 small fw-semibold">Qualified Parts</div>
              <div class="fs-4 fw-bold text-white font-monospace mt-1"><?php echo (int)($d_stats['participants'] ?? 0); ?></div>
            </div>
          </div>
          <div class="col-4">
            <div class="p-2.5 bg-white bg-opacity-10 rounded-3 border border-white border-opacity-20 backdrop-blur">
              <div class="text-white-50 small fw-semibold">Item Entries</div>
              <div class="fs-4 fw-bold text-white font-monospace mt-1"><?php echo (int)($d_stats['items'] ?? 0); ?></div>
            </div>
          </div>
          <div class="col-4">
            <div class="p-2.5 bg-white bg-opacity-10 rounded-3 border border-white border-opacity-20 backdrop-blur">
              <div class="text-white-50 small fw-semibold">Schools</div>
              <div class="fs-4 fw-bold text-white font-monospace mt-1"><?php echo (int)($d_stats['schools'] ?? 0); ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Flash Alerts -->
<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 d-flex align-items-center gap-3 mb-4 p-3" role="alert">
    <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
      <i class="bi bi-check-lg fs-5"></i>
    </div>
    <div class="flex-grow-1">
      <div class="fw-bold">Success</div>
      <div class="small"><?php echo esc(session()->getFlashdata('success')); ?></div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 d-flex align-items-center gap-3 mb-4 p-3" role="alert">
    <div class="rounded-circle bg-danger text-white p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
      <i class="bi bi-exclamation-triangle-fill fs-5"></i>
    </div>
    <div class="flex-grow-1">
      <div class="fw-bold">Import Error</div>
      <div class="small"><?php echo esc(session()->getFlashdata('error')); ?></div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if (!empty($preview)): ?>
  <!-- ======================================================== -->
  <!-- STEP 2: PREVIEW & QUALIFICATION INSPECTION DASHBOARD     -->
  <!-- ======================================================== -->
  <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden border border-primary border-opacity-25">
    <div class="card-header bg-white border-bottom border-light-subtle py-3 px-4">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-primary rounded-pill px-3 py-1 font-monospace">
            <i class="bi bi-fingerprint me-1"></i> <?php echo esc($preview['job_id']); ?>
          </span>
          <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-3 py-1 font-monospace">
            <i class="bi bi-building me-1"></i> <?php echo esc($preview['sub_district_name']); ?> (<?php echo esc($preview['sub_district_code']); ?>)
          </span>
          <h5 class="fw-bold mb-0 text-dark ms-1">Verification &amp; Qualification Preview</h5>
        </div>
        <div class="d-flex align-items-center gap-2">
          <?php if ($preview['has_errors']): ?>
            <a href="<?php echo esc($preview['error_download_url']); ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 shadow-xs d-inline-flex align-items-center gap-1.5">
              <i class="bi bi-file-earmark-arrow-down-fill"></i>
              <span>Download Error CSV (<?php echo (int)$preview['rejected_count']; ?>)</span>
            </a>
          <?php endif; ?>
          <a href="<?php echo base_url('import/import_data'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 shadow-xs d-inline-flex align-items-center gap-1.5">
            <i class="bi bi-arrow-counterclockwise"></i>
            <span>Cancel &amp; Re-upload</span>
          </a>
        </div>
      </div>
    </div>
    <div class="card-body p-4">
      <!-- 6 Metrics KPI Grid -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
          <div class="card border-0 shadow-xs rounded-4 p-3 text-center bg-light border border-secondary border-opacity-10 h-100">
            <div class="text-muted small fw-semibold">Total Rows</div>
            <div class="fs-3 fw-bold text-dark font-monospace mt-1"><?php echo (int)$preview['total_rows']; ?></div>
            <div class="small text-muted mt-1">All Candidates</div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
          <div class="card border-0 shadow-xs rounded-4 p-3 text-center bg-success-subtle text-success border border-success border-opacity-25 h-100">
            <div class="small fw-semibold text-success-emphasis">Qualified (Grade A)</div>
            <div class="fs-3 fw-bold font-monospace mt-1"><?php echo (int)$preview['qualified_count']; ?></div>
            <div class="small text-success mt-1">Eligible for District</div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
          <div class="card border-0 shadow-xs rounded-4 p-3 text-center bg-info-subtle text-info-emphasis border border-info border-opacity-25 h-100">
            <div class="small fw-semibold">Skipped (Duplicate)</div>
            <div class="fs-3 fw-bold font-monospace mt-1"><?php echo (int)$preview['skipped_count']; ?></div>
            <div class="small text-muted mt-1">Already in District</div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
          <div class="card border-0 shadow-xs rounded-4 p-3 text-center bg-danger-subtle text-danger border border-danger border-opacity-25 h-100">
            <div class="small fw-semibold text-danger-emphasis">Rejected / Ineligible</div>
            <div class="fs-3 fw-bold font-monospace mt-1"><?php echo (int)$preview['rejected_count']; ?></div>
            <div class="small text-danger mt-1">Disqualified / Errors</div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
          <div class="card border-0 shadow-xs rounded-4 p-3 text-center bg-primary-subtle text-primary border border-primary border-opacity-25 h-100">
            <div class="small fw-semibold text-primary-emphasis">Photos Matched</div>
            <div class="fs-3 fw-bold font-monospace mt-1"><?php echo (int)$preview['photos_matched']; ?></div>
            <div class="small text-primary mt-1">Matched &amp; Ready</div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
          <div class="card border-0 shadow-xs rounded-4 p-3 text-center bg-warning-subtle text-warning-emphasis border border-warning border-opacity-25 h-100">
            <div class="small fw-semibold">Photos Missing</div>
            <div class="fs-3 fw-bold font-monospace mt-1"><?php echo (int)$preview['photos_missing']; ?></div>
            <div class="small text-muted mt-1">No Photo in Archive</div>
          </div>
        </div>
      </div>

      <!-- Action Confirmation Box -->
      <?php if ($preview['qualified_count'] > 0): ?>
        <div class="alert rounded-4 border-0 p-3.5 mb-4 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);">
          <div class="d-flex align-items-center gap-3">
            <div class="bg-success text-white rounded-circle p-2.5 d-flex align-items-center justify-content-center shadow-xs" style="width: 46px; height: 46px;">
              <i class="bi bi-shield-check fs-4"></i>
            </div>
            <div>
              <div class="fw-bold text-success-emphasis fs-6">
                <strong><?php echo (int)$preview['qualified_count']; ?> Qualified Winner(s)</strong> are ready to be enrolled into District Kalolsavam!
              </div>
              <div class="text-muted small">
                Click the button below to safely commit participant records, school details, and photographs into District operational tables (<code class="fw-bold">d_participant_details</code>, <code class="fw-bold">d_participant_item_details</code>, <code class="fw-bold">d_school_master</code>).
              </div>
            </div>
          </div>
          <form method="post" action="<?php echo base_url('import/import_data/confirm'); ?>" class="mb-0" id="formConfirmImport">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="confirm">
            <input type="hidden" name="job_id" value="<?php echo esc($preview['job_id']); ?>">
            <button type="submit" id="btnSubmitConfirm" class="btn btn-success btn-lg rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
              <i class="bi bi-check-circle-fill"></i>
              <span>Confirm &amp; Commit to District</span>
            </button>
          </form>
        </div>
      <?php else: ?>
        <div class="alert alert-warning rounded-4 border-0 p-3 mb-4 shadow-sm d-flex align-items-center gap-2">
          <i class="bi bi-info-circle-fill fs-5 text-warning"></i>
          <div>
            <strong>No new qualified winners found.</strong> Records may have already been imported, or the uploaded file does not contain new Rank 1 and Grade A qualifiers.
          </div>
        </div>
      <?php endif; ?>

      <!-- Searchable Preview Roster Table -->
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-secondary rounded-pill px-3 py-1 font-monospace">Sample Roster</span>
          <h6 class="fw-bold mb-0 text-dark">Participant Details Preview</h6>
        </div>
        <div class="d-flex align-items-center gap-2">
          <input type="text" id="rosterSearch" class="form-control form-control-sm rounded-pill px-3" placeholder="Search by name, admn, school, item..." style="width: 260px;">
          <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-secondary active btn-filter" data-filter="all">All</button>
            <button type="button" class="btn btn-outline-success btn-filter" data-filter="QUALIFIED">Qualified</button>
            <button type="button" class="btn btn-outline-info btn-filter" data-filter="SKIPPED">Skipped</button>
            <button type="button" class="btn btn-outline-danger btn-filter" data-filter="REJECTED">Rejected</button>
          </div>
        </div>
      </div>

      <div class="table-responsive rounded-4 border border-light-subtle shadow-xs">
        <table class="table table-hover align-middle mb-0" id="previewTable">
          <thead class="table-light">
            <tr class="text-uppercase small text-muted font-monospace">
              <th style="width: 50px;">#</th>
              <th style="width: 70px;">Photo</th>
              <th>Participant &amp; Admn</th>
              <th>School Code &amp; Name</th>
              <th>Class &amp; Gender</th>
              <th>Item Code &amp; Name</th>
              <th class="text-center">Sub-Dist Rank / Grade</th>
              <th class="text-center">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $sampleRows = $preview['sample_rows'] ?? [];
            if (empty($sampleRows)): ?>
              <tr>
                <td colspan="8" class="text-center py-4 text-muted">No records available.</td>
              </tr>
            <?php else:
              foreach ($sampleRows as $i => $item):
                $r = $item['row'] ?? $item;
                $st = $item['status'] ?? 'REJECTED';
                $badgeClass = ($st === 'QUALIFIED') ? 'bg-success' : (($st === 'SKIPPED') ? 'bg-info text-dark' : 'bg-danger');
                $photoInfo = $preview['photo_map'][$i] ?? null;
                $hasPhoto = !empty($photoInfo);
              ?>
              <tr class="roster-row" data-status="<?php echo esc($st); ?>">
                <td class="font-monospace small text-muted"><?php echo $i + 1; ?></td>
                <td class="text-center">
                  <?php if ($hasPhoto): ?>
                    <span class="badge bg-success-subtle text-success rounded-circle p-2 shadow-xs" title="Photo Verified">
                      <i class="bi bi-camera-fill"></i>
                    </span>
                  <?php else: ?>
                    <span class="badge bg-secondary-subtle text-muted rounded-circle p-2 shadow-xs" title="No Photo Attached">
                      <i class="bi bi-person-x"></i>
                    </span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="fw-bold text-dark"><?php echo esc($r['participant_name'] ?? '-'); ?></div>
                  <div class="small text-muted font-monospace">Admn: <span class="fw-semibold text-dark"><?php echo esc($r['source_admn_no'] ?? '-'); ?></span></div>
                </td>
                <td>
                  <div class="fw-semibold text-dark"><?php echo esc($r['participant_school_code'] ?? '-'); ?></div>
                  <div class="small text-muted text-truncate" style="max-width: 220px;" title="<?php echo esc($r['participant_school_name'] ?? ''); ?>">
                    <?php echo esc($r['participant_school_name'] ?? ('School ' . ($r['participant_school_code'] ?? ''))); ?>
                  </div>
                </td>
                <td>
                  <span class="badge bg-light text-dark border font-monospace me-1">Std: <?php echo esc($r['participant_class'] ?? '-'); ?></span>
                  <span class="badge bg-light text-dark border font-monospace"><?php echo esc($r['participant_gender'] ?? '-'); ?></span>
                </td>
                <td>
                  <div class="fw-semibold text-primary font-monospace"><?php echo esc($r['item_code'] ?? '-'); ?></div>
                  <div class="small text-dark text-truncate" style="max-width: 200px;" title="<?php echo esc($r['item_name'] ?? ''); ?>">
                    <?php echo esc($r['item_name'] ?? '-'); ?>
                  </div>
                </td>
                <td class="text-center">
                  <span class="badge bg-warning text-dark font-monospace me-1">Rank <?php echo esc($r['rank'] ?? 1); ?></span>
                  <span class="badge bg-success font-monospace">Grade <?php echo esc($r['grade'] ?? 'A'); ?></span>
                </td>
                <td class="text-center">
                  <span class="badge <?php echo $badgeClass; ?> rounded-pill px-3 py-1 font-monospace" title="<?php echo esc($item['reason'] ?? ''); ?>">
                    <?php echo esc($st); ?>
                  </span>
                  <?php if (!empty($item['reason']) && $st === 'REJECTED'): ?>
                    <div class="small text-danger mt-1 text-truncate" style="max-width: 180px;" title="<?php echo esc($item['reason']); ?>">
                      <?php echo esc($item['reason']); ?>
                    </div>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- ======================================================== -->
<!-- STEP 1: MODERN DRAG & DROP UPLOAD WORKFLOW               -->
<!-- ======================================================== -->
<div class="row g-4 mb-4">
  <!-- Upload Cards Column -->
  <div class="col-12 col-xl-8">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
      <div class="card-header bg-white border-bottom border-light-subtle py-3 px-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">1</span>
            <h5 class="fw-bold mb-0 text-dark">Step 1: Upload Sub-District Export Files</h5>
          </div>
          <!-- Upload Mode Pills -->
          <ul class="nav nav-pills nav-fill bg-light p-1 rounded-pill" id="uploadTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active rounded-pill px-3 py-1 small fw-semibold" id="unified-tab" data-bs-toggle="pill" data-bs-target="#unified-pane" type="button" role="tab">
                <i class="bi bi-box-seam-fill me-1"></i> Unified Package (.zip)
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link rounded-pill px-3 py-1 small fw-semibold" id="split-tab" data-bs-toggle="pill" data-bs-target="#split-pane" type="button" role="tab">
                <i class="bi bi-filetype-csv me-1"></i> CSV + Photo ZIP
              </button>
            </li>
          </ul>
        </div>
      </div>

      <div class="card-body p-4">
        <form method="post" action="<?php echo base_url('import/import_data/preview'); ?>" enctype="multipart/form-data" id="formMainUpload">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="action" value="preview">
          <input type="hidden" name="district_code" value="<?php echo esc($district_code ?? 9); ?>">

          <div class="tab-content" id="uploadTabContent">
            <!-- TAB A: UNIFIED WINNER PACKAGE (.ZIP) -->
            <div class="tab-pane fade show active" id="unified-pane" role="tabpanel">
              <div class="upload-dropzone p-4 text-center rounded-4 border-2 border-dashed border-primary border-opacity-25 bg-primary bg-opacity-10 position-relative transition-all" id="unifiedDropzone" style="cursor: pointer;">
                <input type="file" name="unified_package" id="unifiedPackageInput" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept=".zip" style="cursor: pointer; z-index: 5;">
                <div class="dropzone-content py-3">
                  <div class="mb-3">
                    <span class="rounded-circle bg-white text-primary p-3 d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px;">
                      <i class="bi bi-cloud-arrow-up-fill fs-1"></i>
                    </span>
                  </div>
                  <h5 class="fw-bold text-dark mb-1">Drag &amp; Drop Unified Winner Package (.zip) Here</h5>
                  <p class="text-muted small mb-3">
                    Drag &amp; drop the exported <code class="fw-bold">TK25-District-Winners-*.zip</code> package here or click to browse.
                  </p>
                  <button type="button" class="btn btn-primary rounded-pill px-4 py-2 shadow-xs fw-semibold pointer-events-none">
                    <i class="bi bi-folder2-open me-1.5"></i> Browse Package ZIP
                  </button>
                  <div class="mt-3 text-muted small">
                    <i class="bi bi-shield-check text-success me-1"></i> Contains <code>winners.csv</code>, <code>photos/</code> folder, and <code>manifest.json</code>
                  </div>
                </div>
                <!-- Selected File Display -->
                <div class="file-display d-none mt-3 p-3 bg-white rounded-3 shadow-xs border text-start d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-file-earmark-zip-fill fs-2 text-primary"></i>
                    <div>
                      <div class="fw-bold text-dark file-name text-truncate" style="max-width: 320px;">filename.zip</div>
                      <div class="small text-muted file-size">0 KB</div>
                    </div>
                  </div>
                  <button type="button" class="btn btn-sm btn-outline-danger rounded-pill btn-clear-file" data-target="unifiedPackageInput">
                    <i class="bi bi-trash3"></i> Change
                  </button>
                </div>
              </div>
            </div>

            <!-- TAB B: STANDALONE CSV + PHOTO ZIP -->
            <div class="tab-pane fade" id="split-pane" role="tabpanel">
              <div class="row g-3">
                <!-- CSV Upload Box -->
                <div class="col-12 col-md-6">
                  <div class="p-3.5 rounded-4 border-2 border-dashed border-secondary border-opacity-25 bg-light position-relative text-center h-100" id="csvDropzone">
                    <input type="file" name="csv_file" id="csvFileInput" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept=".csv" style="cursor: pointer; z-index: 5;">
                    <div class="py-2">
                      <i class="bi bi-filetype-csv fs-1 text-success mb-2 d-block"></i>
                      <div class="fw-bold text-dark mb-1">Sub-District Winners CSV</div>
                      <div class="small text-muted mb-2"><code>TK25_Winners_*.csv</code> or legacy export CSV</div>
                      <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 pointer-events-none">
                        Browse CSV
                      </button>
                    </div>
                    <div class="file-display d-none mt-2 p-2 bg-white rounded-3 border text-start d-flex align-items-center justify-content-between">
                      <div class="small text-truncate fw-semibold text-dark file-name" style="max-width: 160px;">file.csv</div>
                      <span class="badge bg-success-subtle text-success rounded-pill font-monospace small file-size">0 KB</span>
                    </div>
                  </div>
                </div>

                <!-- Photo ZIP Upload Box -->
                <div class="col-12 col-md-6">
                  <div class="p-3.5 rounded-4 border-2 border-dashed border-secondary border-opacity-25 bg-light position-relative text-center h-100" id="zipDropzone">
                    <input type="file" name="photos_zip" id="zipFileInput" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept=".zip" style="cursor: pointer; z-index: 5;">
                    <div class="py-2">
                      <i class="bi bi-images fs-1 text-info mb-2 d-block"></i>
                      <div class="fw-bold text-dark mb-1">Winners Photographs (.zip)</div>
                      <div class="small text-muted mb-2"><code>photos_winners_*.zip</code> archive</div>
                      <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1 pointer-events-none">
                        Browse Photo ZIP
                      </button>
                    </div>
                    <div class="file-display d-none mt-2 p-2 bg-white rounded-3 border text-start d-flex align-items-center justify-content-between">
                      <div class="small text-truncate fw-semibold text-dark file-name" style="max-width: 160px;">photos.zip</div>
                      <span class="badge bg-info-subtle text-info rounded-pill font-monospace small file-size">0 KB</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-4 pt-3 border-top border-light-subtle">
            <div class="small text-muted">
              <i class="bi bi-lock-fill text-secondary me-1"></i> Transaction-safe &bull; Anti-ZipSlip verified &bull; LP Barred
            </div>
            <button type="submit" id="btnTriggerPreview" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
              <i class="bi bi-search"></i>
              <span>Verify &amp; Preview Import Roster</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Information & Rules Side Card -->
  <div class="col-12 col-xl-4">
    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
      <div class="card-header bg-white border-bottom border-light-subtle py-3 px-4">
        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-info-circle-fill text-primary"></i>
          <span>Import Guidelines &amp; Rules</span>
        </h6>
      </div>
      <div class="card-body p-4">
        <ul class="list-unstyled mb-0 d-flex flex-column gap-3 small">
          <li class="d-flex align-items-start gap-2.5">
            <span class="badge bg-success-subtle text-success rounded-circle p-1.5 mt-0.5">
              <i class="bi bi-check-lg"></i>
            </span>
            <div>
              <strong class="text-dark">Rank 1 &amp; Grade A Only:</strong>
              <div class="text-muted">Only participants securing 1st Rank with Grade 'A' at the Sub-District Kalolsavam qualify for District competition.</div>
            </div>
          </li>
          <li class="d-flex align-items-start gap-2.5">
            <span class="badge bg-danger-subtle text-danger rounded-circle p-1.5 mt-0.5">
              <i class="bi bi-x-lg"></i>
            </span>
            <div>
              <strong class="text-dark">LP Events Excluded:</strong>
              <div class="text-muted">Lower Primary (LP / Classes 1&ndash;4) events conclude at the Sub-District level and are excluded from District Kalolsavam.</div>
            </div>
          </li>
          <li class="d-flex align-items-start gap-2.5">
            <span class="badge bg-primary-subtle text-primary rounded-circle p-1.5 mt-0.5">
              <i class="bi bi-database-check"></i>
            </span>
            <div>
              <strong class="text-dark">Automatic School Enrollment:</strong>
              <div class="text-muted">Participating schools are automatically created in <code>d_school_master</code> and <code>d_school_details</code> tables if not present.</div>
            </div>
          </li>
          <li class="d-flex align-items-start gap-2.5">
            <span class="badge bg-info-subtle text-info rounded-circle p-1.5 mt-0.5">
              <i class="bi bi-images"></i>
            </span>
            <div>
              <strong class="text-dark">Photo Security &amp; Matching:</strong>
              <div class="text-muted">Photos are verified with strict MIME type checking, validated against ZipSlip vulnerabilities, and securely stored in <code>photos/</code>.</div>
            </div>
          </li>
        </ul>

        <div class="mt-4 pt-3 border-top border-light-subtle">
          <div class="small fw-bold text-dark mb-1">Need to Export Data?</div>
          <div class="text-muted small mb-2">Export data and photos from Sub-District module:</div>
          <a href="<?php echo base_url(); ?>export/export_district_data" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 w-100 d-inline-flex align-items-center justify-content-center gap-1.5">
            <i class="bi bi-box-arrow-up-right"></i>
            <span>Go to Sub-District Export</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ======================================================== -->
<!-- STEP 3: RECENT IMPORT AUDIT HISTORY                      -->
<!-- ======================================================== -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
  <div class="card-header bg-white border-bottom border-light-subtle py-3 px-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-clock-history text-secondary fs-5"></i>
        <h5 class="fw-bold mb-0 text-dark">Recent Import Audit History</h5>
      </div>
      <span class="badge bg-light text-dark border font-monospace px-3 py-1 small">
        Last 15 Batches
      </span>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr class="text-uppercase small text-muted font-monospace">
            <th class="ps-4">Job / Export ID</th>
            <th>Sub-District</th>
            <th>Imported At</th>
            <th class="text-center">Total Rows</th>
            <th class="text-center">Promoted</th>
            <th class="text-center">Photos</th>
            <th>Imported By</th>
            <th class="pe-4 text-center">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($history)): ?>
            <tr>
              <td colspan="8" class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-3 d-block mb-1 text-secondary opacity-50"></i>
                No Sub-District data has been imported yet.
              </td>
            </tr>
          <?php else:
            foreach ($history as $h): ?>
            <tr>
              <td class="ps-4 font-monospace small">
                <div class="fw-bold text-dark"><?php echo esc($h['import_id']); ?></div>
                <div class="text-muted small"><?php echo esc($h['export_id']); ?></div>
              </td>
              <td>
                <span class="badge bg-light text-dark border font-monospace">
                  <?php echo esc($h['sub_district_name'] ?? ('Sub-District ' . $h['sub_district_code'])); ?> (<?php echo esc($h['sub_district_code']); ?>)
                </span>
              </td>
              <td class="small text-muted font-monospace">
                <?php echo date('d-M-Y h:i A', strtotime($h['imported_at'])); ?>
              </td>
              <td class="text-center font-monospace fw-semibold">
                <?php echo (int)$h['total_rows']; ?>
              </td>
              <td class="text-center">
                <span class="badge bg-success font-monospace px-2.5 py-1">
                  +<?php echo (int)$h['valid_rows']; ?>
                </span>
              </td>
              <td class="text-center font-monospace small">
                <span class="text-success fw-bold"><?php echo (int)$h['photos_matched']; ?></span> / <span class="text-muted"><?php echo (int)$h['photos_expected']; ?></span>
              </td>
              <td class="small">
                <span class="text-dark fw-semibold"><?php echo esc($h['imported_by_username'] ?? ('User #' . $h['imported_by_user_id'])); ?></span>
              </td>
              <td class="pe-4 text-center">
                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 font-monospace">
                  <i class="bi bi-check-circle-fill me-1"></i> SUCCESS
                </span>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
/* Modern styling for Import interface */
.upload-dropzone:hover {
  border-color: #0d6efd !important;
  background-color: rgba(13, 110, 253, 0.08) !important;
}
.border-dashed {
  border-style: dashed !important;
}
.shadow-xs {
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
.pointer-events-none {
  pointer-events: none;
}
.transition-all {
  transition: all 0.2s ease-in-out;
}
.backdrop-blur {
  backdrop-filter: blur(8px);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // File input drag & drop handlers
  function setupFileInput(inputId, dropzoneId) {
    const input = document.getElementById(inputId);
    const dropzone = document.getElementById(dropzoneId);
    if (!input || !dropzone) return;

    const display = dropzone.querySelector('.file-display');
    const nameEl = dropzone.querySelector('.file-name');
    const sizeEl = dropzone.querySelector('.file-size');
    const content = dropzone.querySelector('.dropzone-content') || dropzone.querySelector('.py-2');

    input.addEventListener('change', function () {
      if (this.files && this.files[0]) {
        const file = this.files[0];
        if (nameEl) nameEl.textContent = file.name;
        if (sizeEl) sizeEl.textContent = (file.size / (1024 * 1024) > 1) ?
          (file.size / (1024 * 1024)).toFixed(2) + ' MB' :
          (file.size / 1024).toFixed(1) + ' KB';
        if (display) display.classList.remove('d-none');
        if (content && dropzoneId === 'unifiedDropzone') content.classList.add('d-none');
      }
    });

    const clearBtn = dropzone.querySelector('.btn-clear-file');
    if (clearBtn) {
      clearBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        input.value = '';
        if (display) display.classList.add('d-none');
        if (content && dropzoneId === 'unifiedDropzone') content.classList.remove('d-none');
      });
    }
  }

  setupFileInput('unifiedPackageInput', 'unifiedDropzone');
  setupFileInput('csvFileInput', 'csvDropzone');
  setupFileInput('zipFileInput', 'zipDropzone');

  // Search filter for preview table
  const searchInput = document.getElementById('rosterSearch');
  const rows = document.querySelectorAll('#previewTable .roster-row');
  let currentFilter = 'all';

  function filterTable() {
    const q = searchInput ? searchInput.value.toLowerCase().trim() : '';
    rows.forEach(function (row) {
      const text = row.textContent.toLowerCase();
      const status = row.getAttribute('data-status');
      const matchesSearch = q === '' || text.includes(q);
      const matchesFilter = currentFilter === 'all' || status === currentFilter;
      row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
    });
  }

  if (searchInput) {
    searchInput.addEventListener('input', filterTable);
  }

  document.querySelectorAll('.btn-filter').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      currentFilter = this.getAttribute('data-filter');
      filterTable();
    });
  });

  // Submit loading indicators
  const formMain = document.getElementById('formMainUpload');
  if (formMain) {
    formMain.addEventListener('submit', function () {
      const btn = document.getElementById('btnTriggerPreview');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Inspecting &amp; Validating...';
      }
    });
  }

  const formConfirm = document.getElementById('formConfirmImport');
  if (formConfirm) {
    formConfirm.addEventListener('submit', function () {
      const btn = document.getElementById('btnSubmitConfirm');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Ingesting into District DB...';
      }
    });
  }
});
</script>