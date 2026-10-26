<div class="pagetitle mb-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
        <span class="rounded-3 bg-primary-subtle text-primary p-2 d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 42px; height: 42px;">
          <i class="bi bi-patch-check-fill fs-5"></i>
        </span>
        <span>District Certificate Template Designer</span>
      </h1>
      <nav>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
          <li class="breadcrumb-item">District</li>
          <li class="breadcrumb-item active">Certificate Template</li>
        </ol>
      </nav>
    </div>
    <div class="d-flex gap-2">
      <a href="<?php echo base_url('district/certificate/print_list'); ?>" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-sm">
        <i class="bi bi-printer"></i>
        <span>Print Certificates</span>
      </a>
      <a href="<?php echo base_url(); ?>welcome" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-sm">
        <i class="bi bi-arrow-left"></i>
        <span>Dashboard</span>
      </a>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);">
  <div class="card-body p-4 text-white">
    <div class="d-flex align-items-center gap-3">
      <div class="bg-white bg-opacity-20 rounded-circle p-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 54px; height: 54px; min-width: 54px;">
        <i class="bi bi-fonts fs-3 text-warning"></i>
      </div>
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <span class="badge bg-warning text-dark fw-bold rounded-pill px-3 py-1 small font-monospace">Font Standard: TAU-Pallai</span>
          <span class="badge bg-white text-dark fw-bold rounded-pill px-3 py-1 small font-monospace">Size: 14pt</span>
        </div>
        <h5 class="fw-bold mb-0 text-white">District Tamil Kalolsavam Certificate Typography</h5>
        <p class="mb-0 text-white-50 small mt-1">Configured coordinates for printing Tamil Kalaivizha Merit &amp; Participation certificates for Revenue District <?php echo esc($district_code); ?>.</p>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
  <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
    <h5 class="card-title mb-0 fw-bold text-dark">Print Coordinate Offsets (X, Y Coordinates in mm)</h5>
    <small class="text-muted">Adjust coordinates to match your official pre-printed District certificate parchment stationery.</small>
  </div>
  <div class="card-body px-4 pb-4">
    <form id="formDistrictCertTemplate">
      <div class="row g-3">
        <!-- Participant Name -->
        <div class="col-12 col-md-6 col-lg-3">
          <label class="form-label fw-semibold small text-dark">Name X / Y</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text">X</span>
            <input type="number" name="name_x" class="form-control" value="<?php echo esc($template['name_x'] ?? 120); ?>">
            <span class="input-group-text">Y</span>
            <input type="number" name="name_y" class="form-control" value="<?php echo esc($template['name_y'] ?? 140); ?>">
          </div>
          <small class="text-muted">Font: <?php echo esc($template['name_font'] ?? 'TAU-Pallai'); ?> (<?php echo esc($template['name_size'] ?? 14); ?>pt)</small>
        </div>

        <!-- Item Name -->
        <div class="col-12 col-md-6 col-lg-3">
          <label class="form-label fw-semibold small text-dark">Item X / Y</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text">X</span>
            <input type="number" name="item_x" class="form-control" value="<?php echo esc($template['item_x'] ?? 120); ?>">
            <span class="input-group-text">Y</span>
            <input type="number" name="item_y" class="form-control" value="<?php echo esc($template['item_y'] ?? 165); ?>">
          </div>
          <small class="text-muted">Font: <?php echo esc($template['item_font'] ?? 'TAU-Pallai'); ?> (<?php echo esc($template['item_size'] ?? 14); ?>pt)</small>
        </div>

        <!-- Category -->
        <div class="col-12 col-md-6 col-lg-3">
          <label class="form-label fw-semibold small text-dark">Category X / Y</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text">X</span>
            <input type="number" name="category_x" class="form-control" value="<?php echo esc($template['category_x'] ?? 120); ?>">
            <span class="input-group-text">Y</span>
            <input type="number" name="category_y" class="form-control" value="<?php echo esc($template['category_y'] ?? 190); ?>">
          </div>
          <small class="text-muted">Font: <?php echo esc($template['category_font'] ?? 'TAU-Pallai'); ?></small>
        </div>

        <!-- Grade -->
        <div class="col-12 col-md-6 col-lg-3">
          <label class="form-label fw-semibold small text-dark">Grade X / Y</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text">X</span>
            <input type="number" name="grade_x" class="form-control" value="<?php echo esc($template['grade_x'] ?? 120); ?>">
            <span class="input-group-text">Y</span>
            <input type="number" name="grade_y" class="form-control" value="<?php echo esc($template['grade_y'] ?? 215); ?>">
          </div>
          <small class="text-muted">Font: <?php echo esc($template['grade_font'] ?? 'TAU-Pallai'); ?></small>
        </div>

        <!-- School -->
        <div class="col-12 col-md-6 col-lg-3">
          <label class="form-label fw-semibold small text-dark">School X / Y</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text">X</span>
            <input type="number" name="school_x" class="form-control" value="<?php echo esc($template['school_x'] ?? 120); ?>">
            <span class="input-group-text">Y</span>
            <input type="number" name="school_y" class="form-control" value="<?php echo esc($template['school_y'] ?? 265); ?>">
          </div>
        </div>

        <!-- Sub-District -->
        <div class="col-12 col-md-6 col-lg-3">
          <label class="form-label fw-semibold small text-dark">Sub-District X / Y</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text">X</span>
            <input type="number" name="sub_dist_x" class="form-control" value="<?php echo esc($template['sub_dist_x'] ?? 120); ?>">
            <span class="input-group-text">Y</span>
            <input type="number" name="sub_dist_y" class="form-control" value="<?php echo esc($template['sub_dist_y'] ?? 290); ?>">
          </div>
        </div>

        <!-- District -->
        <div class="col-12 col-md-6 col-lg-3">
          <label class="form-label fw-semibold small text-dark">District X / Y</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text">X</span>
            <input type="number" name="dist_x" class="form-control" value="<?php echo esc($template['dist_x'] ?? 120); ?>">
            <span class="input-group-text">Y</span>
            <input type="number" name="dist_y" class="form-control" value="<?php echo esc($template['dist_y'] ?? 315); ?>">
          </div>
        </div>

        <!-- Date & Place -->
        <div class="col-12 col-md-6 col-lg-3">
          <label class="form-label fw-semibold small text-dark">Date X / Y</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text">X</span>
            <input type="number" name="date_x" class="form-control" value="<?php echo esc($template['date_x'] ?? 120); ?>">
            <span class="input-group-text">Y</span>
            <input type="number" name="date_y" class="form-control" value="<?php echo esc($template['date_y'] ?? 340); ?>">
          </div>
        </div>

        <!-- Photo Coordinates -->
        <div class="col-12 col-md-6">
          <label class="form-label fw-semibold small text-dark">Photo Coordinates &amp; Box Size</label>
          <div class="row g-2">
            <div class="col-3">
              <div class="input-group input-group-sm">
                <span class="input-group-text">X</span>
                <input type="number" name="photoX" class="form-control" value="<?php echo esc($template['photoX'] ?? 460); ?>">
              </div>
            </div>
            <div class="col-3">
              <div class="input-group input-group-sm">
                <span class="input-group-text">Y</span>
                <input type="number" name="photoY" class="form-control" value="<?php echo esc($template['photoY'] ?? 140); ?>">
              </div>
            </div>
            <div class="col-3">
              <div class="input-group input-group-sm">
                <span class="input-group-text">W</span>
                <input type="number" name="photoWidth" class="form-control" value="<?php echo esc($template['photoWidth'] ?? 100); ?>">
              </div>
            </div>
            <div class="col-3">
              <div class="input-group input-group-sm">
                <span class="input-group-text">H</span>
                <input type="number" name="photoHight" class="form-control" value="<?php echo esc($template['photoHight'] ?? 120); ?>">
              </div>
            </div>
          </div>
        </div>

        <!-- Page Style -->
        <div class="col-12 col-md-6">
          <label class="form-label fw-semibold small text-dark">Page Style &amp; Margins</label>
          <div class="row g-2">
            <div class="col-4">
              <select name="page_style" class="form-select form-select-sm">
                <option value="P" <?php echo (($template['page_style'] ?? 'P') === 'P') ? 'selected' : ''; ?>>Portrait</option>
                <option value="L" <?php echo (($template['page_style'] ?? 'P') === 'L') ? 'selected' : ''; ?>>Landscape</option>
              </select>
            </div>
            <div class="col-4">
              <input type="number" name="top_margin" class="form-control form-control-sm" placeholder="Top M" value="<?php echo esc($template['top_margin'] ?? 10); ?>">
            </div>
            <div class="col-4">
              <input type="number" name="left_margin" class="form-control form-control-sm" placeholder="Left M" value="<?php echo esc($template['left_margin'] ?? 10); ?>">
            </div>
          </div>
        </div>
      </div>

      <div class="mt-4 pt-3 border-top">
        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
          <i class="bi bi-save me-1"></i> Save Certificate Coordinates
        </button>
      </div>
    </form>
  </div>
</div>

<script>
document.getElementById('formDistrictCertTemplate').addEventListener('submit', function(e) {
  e.preventDefault();
  const fd = new FormData(this);

  fetch('<?php echo base_url("district/certificate/save_template"); ?>', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
      alert(res.message);
    });
});
</script>
