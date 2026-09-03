<style>
  .cert-card {
    border: 1px solid #e2e8f0;
    background: #ffffff;
    border-radius: 1.15rem;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);
    margin-bottom: 1.5rem;
    overflow: hidden;
  }
  .cert-top-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #334155 100%);
    padding: 1.2rem 1.6rem;
  }
  .cert-card-body {
    padding: 1.4rem 1.6rem;
  }
  .cert-section-header {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.88rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #334155;
    margin-bottom: 1.1rem;
    padding-bottom: 0.6rem;
    border-bottom: 1.5px solid #f1f5f9;
  }
  .cert-form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.83rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.45rem;
  }
  .cert-form-label i {
    font-size: 0.95rem;
    flex-shrink: 0;
  }
  .cert-table {
    margin-bottom: 0;
  }
  .cert-table thead th {
    background-color: #f8fafc;
    color: #475569;
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    border-bottom: 2px solid #e2e8f0;
    padding: 0.8rem 1.25rem;
    vertical-align: middle;
  }
  .cert-table tbody td {
    padding: 0.65rem 1.25rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
  }
  .cert-table tbody tr:hover {
    background-color: #f8fafc;
  }
  .field-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.65rem;
    font-weight: 600;
    font-size: 0.86rem;
    color: #1e293b;
  }
  .field-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    flex-shrink: 0;
  }
  .btn-gradient-save {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    border: none;
    color: #ffffff;
    font-weight: 600;
    padding: 0.52rem 1.35rem;
    border-radius: 2rem;
    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  .btn-gradient-save:hover {
    background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(79, 70, 229, 0.45);
  }
  .btn-preview-link {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.45rem 1rem;
    font-size: 0.82rem;
    font-weight: 600;
    border-radius: 2rem;
    transition: all 0.2s ease;
  }
  .input-coord {
    max-width: 90px;
    margin: 0 auto;
  }
</style>

<?php
$tpl = @$certificate_template[0] ?? [];
$isSet = $this->General_Model->is_certificate_template_set();

$fields = [
    ['label' => 'Title Header',            'icon' => 'bi-trophy-fill',        'bg' => '#fef3c7', 'color' => '#d97706', 'x' => 'txtTitleX',       'y' => 'txtTitleY',       'font' => 'cboTitleFont',   'size' => 'cboTitleSize',   'val_x' => @$tpl['title_x'],       'val_y' => @$tpl['title_y'],       'val_font' => @$tpl['title_font'],   'val_size' => @$tpl['title_size']],
    ['label' => 'Participant Name',        'icon' => 'bi-person-fill',        'bg' => '#e0e7ff', 'color' => '#4f46e5', 'x' => 'txtNameX',        'y' => 'txtNameY',        'font' => 'cboNameFont',    'size' => 'cboNameSize',    'val_x' => @$tpl['name_x'],        'val_y' => @$tpl['name_y'],        'val_font' => @$tpl['name_font'],    'val_size' => @$tpl['name_size']],
    ['label' => 'Item Name',               'icon' => 'bi-palette-fill',       'bg' => '#fce7f3', 'color' => '#db2777', 'x' => 'txtItemX',        'y' => 'txtItemY',        'font' => 'cboItemFont',    'size' => 'cboItemSize',    'val_x' => @$tpl['item_x'],        'val_y' => @$tpl['item_y'],        'val_font' => @$tpl['item_font'],    'val_size' => @$tpl['item_size']],
    ['label' => 'Category',                'icon' => 'bi-tag-fill',           'bg' => '#dcfce7', 'color' => '#16a34a', 'x' => 'txtCategoryX',    'y' => 'txtCategoryY',    'font' => 'cboCategoryFont','size' => 'cboCategorySize','val_x' => @$tpl['category_x'],    'val_y' => @$tpl['category_y'],    'val_font' => @$tpl['category_font'],'val_size' => @$tpl['category_size']],
    ['label' => 'Grade',                   'icon' => 'bi-award-fill',         'bg' => '#fef9c3', 'color' => '#ca8a04', 'x' => 'txtGradeX',       'y' => 'txtGradeY',       'font' => 'cboGradeFont',   'size' => 'cboGradeSize',   'val_x' => @$tpl['grade_x'],       'val_y' => @$tpl['grade_y'],       'val_font' => @$tpl['grade_font'],   'val_size' => @$tpl['grade_size']],
    ['label' => 'Class / Standard',        'icon' => 'bi-book-fill',          'bg' => '#ffedd5', 'color' => '#ea580c', 'x' => 'txtClassX',       'y' => 'txtClassY',       'font' => 'cboClassFont',   'size' => 'cboClassSize',   'val_x' => @$tpl['class_x'],       'val_y' => @$tpl['class_y'],       'val_font' => @$tpl['class_font'],   'val_size' => @$tpl['class_size']],
    ['label' => 'School Name',             'icon' => 'bi-building-fill',      'bg' => '#e0f2fe', 'color' => '#0284c7', 'x' => 'txtSchoolX',      'y' => 'txtSchoolY',      'font' => 'cboSchoolFont',  'size' => 'cboSchoolSize',  'val_x' => @$tpl['school_x'],      'val_y' => @$tpl['school_y'],      'val_font' => @$tpl['school_font'],  'val_size' => @$tpl['school_size']],
    ['label' => 'Subdistrict',             'icon' => 'bi-geo-alt-fill',       'bg' => '#ede9fe', 'color' => '#7c3aed', 'x' => 'txtSubdistrictX', 'y' => 'txtSubdistrictY', 'font' => 'cboSubDistFont', 'size' => 'cboSubDistSize', 'val_x' => @$tpl['sub_dist_x'],   'val_y' => @$tpl['sub_dist_y'],   'val_font' => @$tpl['sub_dist_font'],'val_size' => @$tpl['sub_dist_size']],
    ['label' => 'District',                'icon' => 'bi-map-fill',           'bg' => '#ccfbf1', 'color' => '#0d9488', 'x' => 'txtDistrictX',    'y' => 'txtDistrictY',    'font' => 'cboDistFont',    'size' => 'cboDistSize',    'val_x' => @$tpl['dist_x'],        'val_y' => @$tpl['dist_y'],        'val_font' => @$tpl['dist_font'],    'val_size' => @$tpl['dist_size']],
    ['label' => 'Issue Date',              'icon' => 'bi-calendar-date-fill', 'bg' => '#f1f5f9', 'color' => '#475569', 'x' => 'txtDateX',        'y' => 'txtDateY',        'font' => 'cboDateFont',    'size' => 'cboDateSize',    'val_x' => @$tpl['date_x'],        'val_y' => @$tpl['date_y'],        'val_font' => @$tpl['date_font'],    'val_size' => @$tpl['date_size']],
    ['label' => 'Place',                   'icon' => 'bi-pin-map-fill',       'bg' => '#fee2e2', 'color' => '#dc2626', 'x' => 'txtPlaceX',       'y' => 'txtPlaceY',       'font' => 'cboPlaceFont',   'size' => 'cboPlaceSize',   'val_x' => @$tpl['place_x'],       'val_y' => @$tpl['place_y'],       'val_font' => @$tpl['place_font'],   'val_size' => @$tpl['place_size']],
    ['label' => 'Higher Level Eligibility','icon' => 'bi-star-fill',          'bg' => '#fef08a', 'color' => '#854d0e', 'x' => 'txtehsX',        'y' => 'txtehsY',        'font' => 'cboehsFont',     'size' => 'cboehsSize',     'val_x' => @$tpl['ehs_X'],         'val_y' => @$tpl['ehs_Y'],         'val_font' => @$tpl['ehs_font'],     'val_size' => @$tpl['ehs_size']],
];
?>

<?php if (session()->getFlashdata('message') || session()->getFlashdata('success')) { ?>
  <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-3.5 d-flex align-items-center gap-2.5 px-4 py-3 border-0 bg-success bg-opacity-10 text-success" role="alert">
    <i class="bi bi-check-circle-fill text-success fs-5"></i>
    <div class="fw-bold"><?php echo htmlspecialchars((string)(session()->getFlashdata('message') ?? session()->getFlashdata('success'))); ?></div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php } ?>

<?php echo form_open('certificate/certificate/save_certificate_template', array('id' => 'formCertificate')); ?>

<!-- Top Action Header Card -->
<div class="cert-card">
  <div class="cert-top-header text-white d-flex flex-wrap align-items-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
      <div class="bg-white bg-opacity-20 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
        <i class="bi bi-patch-check-fill text-warning fs-5"></i>
      </div>
      <div>
        <h5 class="mb-0 fw-bold fs-6 text-white">Certificate Template Calibration</h5>
      </div>
    </div>
    
    <div class="d-flex align-items-center flex-wrap gap-2.5">
      <?php if ($isSet) { ?>
        <a href="<?php echo base_url('certificate/certificate/test_certificate'); ?>" target="_blank" class="btn btn-sm btn-outline-light btn-preview-link shadow-sm">
          <i class="bi bi-grid-3x3 text-info"></i>
          <span>Preview (Graph)</span>
        </a>
        <a href="<?php echo base_url('certificate/certificate/test_certificare_withoutgraph'); ?>" target="_blank" class="btn btn-sm btn-outline-light btn-preview-link shadow-sm">
          <i class="bi bi-file-earmark-check text-success"></i>
          <span>Preview (Clean)</span>
        </a>
      <?php } ?>
      <button type="button" onclick="openTestGrid();" class="btn btn-sm btn-outline-light btn-preview-link shadow-sm">
        <i class="bi bi-rulers text-warning"></i>
        <span>Test Grid</span>
      </button>
      <button type="submit" name="Save" value="Save" class="btn-gradient-save shadow-sm">
        <i class="bi bi-check2-circle"></i>
        <span>Save Template</span>
      </button>
    </div>
  </div>
</div>

<!-- Section 1: Page & Layout Settings -->
<div class="cert-card">
  <div class="cert-card-body">
    <div class="cert-section-header">
      <i class="bi bi-sliders text-primary fs-5"></i>
      <span>Page &amp; Layout Settings</span>
    </div>
    
    <div class="row g-3">
      <!-- Fest Name -->
      <div class="col-12 col-md-6 col-lg-4">
        <label for="txtTitle" class="cert-form-label">
          <i class="bi bi-type-h1 text-primary"></i>
          <span>Fest Header Title</span>
        </label>
        <?php echo form_input('txtTitle', @$tpl['title'], 'id="txtTitle" class="form-control form-control-sm fw-semibold py-1.5" placeholder="e.g. KERALA SCHOOL TAMIL KALAIVIZHA"'); ?>
      </div>

      <!-- Certificate Type -->
      <div class="col-6 col-md-6 col-lg-2">
        <label for="cboCtType" class="cert-form-label">
          <i class="bi bi-card-heading text-primary"></i>
          <span>Type</span>
        </label>
        <?php echo form_dropdown('cboCtType', $certificate_type_array, @$tpl['type_id'], 'id="cboCtType" class="form-select form-select-sm fw-semibold py-1.5"'); ?>
      </div>

      <!-- Page Style / Orientation -->
      <div class="col-6 col-md-4 col-lg-2">
        <label for="cboPageStyle" class="cert-form-label">
          <i class="bi bi-file-earmark text-primary"></i>
          <span>Orientation</span>
        </label>
        <?php echo form_dropdown('cboPageStyle', ['P' => 'Portrait', 'L' => 'Landscape'], @$tpl['page_style'], 'id="cboPageStyle" class="form-select form-select-sm fw-semibold py-1.5"'); ?>
      </div>

      <!-- Label Print -->
      <div class="col-6 col-md-4 col-lg-2">
        <label for="cboLabelPrint" class="cert-form-label">
          <i class="bi bi-tag text-primary"></i>
          <span>Print Labels</span>
        </label>
        <?php echo form_dropdown('cboLabelPrint', ['N' => 'No', 'Y' => 'Yes'], @$tpl['label_print'], 'id="cboLabelPrint" class="form-select form-select-sm fw-semibold py-1.5"'); ?>
      </div>

      <!-- Line Height -->
      <div class="col-6 col-md-4 col-lg-2">
        <label for="cboLineHeight" class="cert-form-label">
          <i class="bi bi-distribute-vertical text-primary"></i>
          <span>Line Height</span>
        </label>
        <?php echo form_dropdown('cboLineHeight', $line_height_array, @$tpl['line_height'], 'id="cboLineHeight" class="form-select form-select-sm fw-semibold py-1.5"'); ?>
      </div>

      <!-- Margins -->
      <div class="col-4 col-md-4 col-lg-4">
        <label for="cboTopMargin" class="cert-form-label">
          <i class="bi bi-arrow-bar-down text-primary"></i>
          <span>Top Margin (mm)</span>
        </label>
        <?php echo form_dropdown('cboTopMargin', $line_height_array, @$tpl['top_margin'], 'id="cboTopMargin" class="form-select form-select-sm fw-semibold py-1.5"'); ?>
      </div>

      <div class="col-4 col-md-4 col-lg-4">
        <label for="cboLeftMargin" class="cert-form-label">
          <i class="bi bi-arrow-bar-right text-primary"></i>
          <span>Left Margin (mm)</span>
        </label>
        <?php echo form_dropdown('cboLeftMargin', $line_height_array, @$tpl['left_margin'], 'id="cboLeftMargin" class="form-select form-select-sm fw-semibold py-1.5"'); ?>
      </div>

      <div class="col-4 col-md-4 col-lg-4">
        <label for="cboRightMargin" class="cert-form-label">
          <i class="bi bi-arrow-bar-left text-primary"></i>
          <span>Right Margin (mm)</span>
        </label>
        <?php echo form_dropdown('cboRightMargin', $line_height_array, @$tpl['right_margin'], 'id="cboRightMargin" class="form-select form-select-sm fw-semibold py-1.5"'); ?>
      </div>
    </div>
  </div>
</div>

<!-- Section 2: Coordinates & Typography Matrix -->
<div class="cert-card">
  <div style="padding: 1.4rem 1.6rem 0.6rem 1.6rem;">
    <div class="cert-section-header">
      <i class="bi bi-grid-3x3-gap-fill text-primary fs-5"></i>
      <span>Field Coordinates &amp; Typography Matrix</span>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table cert-table align-middle">
      <thead>
        <tr>
          <th style="min-width: 220px; padding-left: 1.6rem;">Field Name</th>
          <th class="text-center" style="width: 130px;">X (Left mm)</th>
          <th class="text-center" style="width: 130px;">Y (Top mm)</th>
          <th style="min-width: 160px;">Font Family</th>
          <th style="width: 120px;">Size</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($fields as $f) { ?>
          <tr>
            <td style="padding-left: 1.6rem;">
              <div class="field-badge">
                <span class="field-icon" style="background: <?php echo $f['bg']; ?>; color: <?php echo $f['color']; ?>;">
                  <i class="bi <?php echo $f['icon']; ?>"></i>
                </span>
                <span><?php echo $f['label']; ?></span>
              </div>
            </td>
            <td class="text-center">
              <input type="text" name="<?php echo $f['x']; ?>" id="<?php echo $f['x']; ?>" value="<?php echo htmlspecialchars((string)$f['val_x']); ?>" class="form-control form-control-sm text-center fw-semibold input-coord py-1.5" maxlength="3" onkeypress="return numbersonly(this, event, false);" placeholder="0" />
            </td>
            <td class="text-center">
              <input type="text" name="<?php echo $f['y']; ?>" id="<?php echo $f['y']; ?>" value="<?php echo htmlspecialchars((string)$f['val_y']); ?>" class="form-control form-control-sm text-center fw-semibold input-coord py-1.5" maxlength="3" onkeypress="return numbersonly(this, event, false);" placeholder="0" />
            </td>
            <td>
              <?php echo form_dropdown($f['font'], $font_array, $f['val_font'], 'id="' . $f['font'] . '" class="form-select form-select-sm fw-semibold py-1.5"'); ?>
            </td>
            <td>
              <?php echo form_dropdown($f['size'], $font_size_array, $f['val_size'], 'id="' . $f['size'] . '" class="form-select form-select-sm fw-semibold py-1.5"'); ?>
            </td>
          </tr>
        <?php } ?>

        <!-- Photo Row -->
        <tr>
          <td style="padding-left: 1.6rem;">
            <div class="field-badge">
              <span class="field-icon" style="background: #f3e8ff; color: #9333ea;">
                <i class="bi bi-camera-fill"></i>
              </span>
              <span>Participant Photo</span>
            </div>
          </td>
          <td class="text-center">
            <input type="text" name="photoX" id="photoX" value="<?php echo htmlspecialchars((string)@$tpl['photoX']); ?>" class="form-control form-control-sm text-center fw-semibold input-coord py-1.5" maxlength="3" onkeypress="return numbersonly(this, event, false);" placeholder="0" />
          </td>
          <td class="text-center">
            <input type="text" name="photoY" id="photoY" value="<?php echo htmlspecialchars((string)@$tpl['photoY']); ?>" class="form-control form-control-sm text-center fw-semibold input-coord py-1.5" maxlength="3" onkeypress="return numbersonly(this, event, false);" placeholder="0" />
          </td>
          <td>
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light text-muted small py-1 fw-bold">W</span>
              <input type="text" name="photoWidth" id="photoWidth" value="<?php echo htmlspecialchars((string)@$tpl['photoWidth']); ?>" class="form-control form-control-sm text-center fw-semibold py-1.5" maxlength="3" onkeypress="return numbersonly(this, event, false);" placeholder="Width (mm)" />
            </div>
          </td>
          <td>
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light text-muted small py-1 fw-bold">H</span>
              <input type="text" name="photoHight" id="photoHight" value="<?php echo htmlspecialchars((string)@$tpl['photoHight']); ?>" class="form-control form-control-sm text-center fw-semibold py-1.5" maxlength="3" onkeypress="return numbersonly(this, event, false);" placeholder="Height (mm)" />
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Bottom Save Bar -->
  <div class="p-3.5 px-4 bg-light border-top d-flex align-items-center justify-content-between flex-wrap gap-3" style="padding: 1rem 1.6rem;">
    <div class="text-muted small d-flex align-items-center gap-2">
      <i class="bi bi-info-circle-fill text-primary fs-6"></i>
      <span>Coordinates (X, Y) are in millimeters (mm) from the page top-left origin.</span>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="submit" name="Save" value="Save" class="btn-gradient-save shadow-sm">
        <i class="bi bi-check2-circle"></i>
        <span>Save Template</span>
      </button>
    </div>
  </div>
</div>

<?php echo form_close(); ?>

<script>
function openTestGrid() {
  var orientationSelect = document.getElementById('cboPageStyle');
  var orientation = orientationSelect ? orientationSelect.value : 'L';
  var testUrl = '<?php echo base_url('test'); ?>?orientation=' + encodeURIComponent(orientation);
  window.open(testUrl, '_blank');
}
</script>

