<?php
$existingElements = !empty($template['elements']) ? $template['elements'] : [];
if (empty($existingElements)) {
    $existingElements = [
        [
            'element_type' => 'static_text',
            'field_name' => null,
            'label_name' => 'Header Title',
            'static_text' => 'TAMIL KALAIVIZHA 2026-27',
            'x' => 20.0,
            'y' => 8.0,
            'width' => 180.0,
            'height' => 10.0,
            'font_family' => 'TAU-Paalai',
            'font_size' => 14,
            'font_weight' => 'bold',
            'font_style' => 'normal',
            'text_decoration' => 'none',
            'font_color' => '#000000',
            'text_align' => 'center',
            'rotation' => 0.0,
            'z_index' => 1,
            'properties_json' => '{"border":0}'
        ],
        [
            'element_type' => 'dynamic_field',
            'field_name' => 'sub_district',
            'label_name' => 'Sub-District',
            'static_text' => '{{sub_district}}',
            'x' => 20.0,
            'y' => 19.0,
            'width' => 180.0,
            'height' => 8.0,
            'font_family' => 'TAU-Paalai',
            'font_size' => 12,
            'font_weight' => 'normal',
            'font_style' => 'normal',
            'text_decoration' => 'none',
            'font_color' => '#333333',
            'text_align' => 'center',
            'rotation' => 0.0,
            'z_index' => 2,
            'properties_json' => '{"border":0}'
        ],
        [
            'element_type' => 'dynamic_field',
            'field_name' => 'category',
            'label_name' => 'Category Box',
            'static_text' => '{{category}}',
            'x' => 55.0,
            'y' => 30.0,
            'width' => 110.0,
            'height' => 11.0,
            'font_family' => 'TAU-Paalai',
            'font_size' => 14,
            'font_weight' => 'bold',
            'font_style' => 'normal',
            'text_decoration' => 'none',
            'font_color' => '#000000',
            'text_align' => 'center',
            'rotation' => 0.0,
            'z_index' => 3,
            'properties_json' => '{"border":1}'
        ],
        [
            'element_type' => 'dynamic_field',
            'field_name' => 'item_code_name',
            'label_name' => 'Item Code & Name',
            'static_text' => '{{item_code}} - {{item_name}}',
            'x' => 15.0,
            'y' => 48.0,
            'width' => 190.0,
            'height' => 15.0,
            'font_family' => 'TAU-Paalai',
            'font_size' => 20,
            'font_weight' => 'bold',
            'font_style' => 'normal',
            'text_decoration' => 'none',
            'font_color' => '#000000',
            'text_align' => 'center',
            'rotation' => 0.0,
            'z_index' => 4,
            'properties_json' => '{"border":0}'
        ],
        [
            'element_type' => 'dynamic_field',
            'field_name' => 'date',
            'label_name' => 'Date',
            'static_text' => '{{date}}',
            'x' => 20.0,
            'y' => 73.0,
            'width' => 80.0,
            'height' => 8.0,
            'font_family' => 'TAU-Paalai',
            'font_size' => 12,
            'font_weight' => 'bold',
            'font_style' => 'normal',
            'text_decoration' => 'none',
            'font_color' => '#000000',
            'text_align' => 'left',
            'rotation' => 0.0,
            'z_index' => 5,
            'properties_json' => '{"border":0}'
        ],
        [
            'element_type' => 'dynamic_field',
            'field_name' => 'time',
            'label_name' => 'Time',
            'static_text' => '{{time}}',
            'x' => 20.0,
            'y' => 83.0,
            'width' => 80.0,
            'height' => 8.0,
            'font_family' => 'TAU-Paalai',
            'font_size' => 12,
            'font_weight' => 'normal',
            'font_style' => 'normal',
            'text_decoration' => 'none',
            'font_color' => '#000000',
            'text_align' => 'left',
            'rotation' => 0.0,
            'z_index' => 6,
            'properties_json' => '{"border":0}'
        ],
        [
            'element_type' => 'dynamic_field',
            'field_name' => 'stage',
            'label_name' => 'Stage',
            'static_text' => 'Stage: {{stage}}',
            'x' => 120.0,
            'y' => 73.0,
            'width' => 80.0,
            'height' => 8.0,
            'font_family' => 'TAU-Paalai',
            'font_size' => 12,
            'font_weight' => 'bold',
            'font_style' => 'normal',
            'text_decoration' => 'none',
            'font_color' => '#000000',
            'text_align' => 'right',
            'rotation' => 0.0,
            'z_index' => 7,
            'properties_json' => '{"border":0}'
        ],
        [
            'element_type' => 'dynamic_field',
            'field_name' => 'venue',
            'label_name' => 'Venue',
            'static_text' => '{{venue}}',
            'x' => 120.0,
            'y' => 83.0,
            'width' => 80.0,
            'height' => 8.0,
            'font_family' => 'TAU-Paalai',
            'font_size' => 12,
            'font_weight' => 'normal',
            'font_style' => 'normal',
            'text_decoration' => 'none',
            'font_color' => '#000000',
            'text_align' => 'right',
            'rotation' => 0.0,
            'z_index' => 8,
            'properties_json' => '{"border":0}'
        ]
    ];
} else {
    foreach ($existingElements as &$el) {
        if (!empty($el['static_text']) && strpos($el['static_text'], '{{item_code}} = {{item_name}}') !== false) {
            $el['static_text'] = str_replace('{{item_code}} = {{item_name}}', '{{item_code}} - {{item_name}}', $el['static_text']);
        }
    }
    unset($el);
}
$templateName = $template['template_name'] ?? 'Envelope Cover Landscape';
$paperSize = $template['paper_size'] ?? 'DL';
$widthMm = (float)($template['width_mm'] ?? 220);
$heightMm = (float)($template['height_mm'] ?? 110);
$orientation = strtolower($template['orientation'] ?? 'landscape');
$bgType = $template['background_type'] ?? 'none';
$bgVal = $template['background_value'] ?? '';
$templateId = (int)($template['id'] ?? 0);
$isDefault = ($template['is_default'] ?? 'Y') === 'Y';
?>

<!-- Embedded Custom Tamil Fonts -->
<style>
@font-face {
  font-family: 'TAU-Paalai';
  src: local('TAU-Paalai'), url('<?php echo base_url("assets/fonts/TAU-Paalai.ttf"); ?>') format('truetype');
  font-weight: normal;
  font-style: normal;
}
@font-face {
  font-family: 'TAU-Paalai';
  src: local('TAU-Paalai Bold'), url('<?php echo base_url("assets/fonts/TAU-Paalai Bold.ttf"); ?>') format('truetype');
  font-weight: bold;
  font-style: normal;
}
@font-face {
  font-family: 'TAU-Maduaram';
  src: local('TAU-Marutham'), url('<?php echo base_url("assets/fonts/TAU-Marutham.ttf"); ?>') format('truetype');
  font-weight: normal;
  font-style: normal;
}
@font-face {
  font-family: 'TAU-Maduaram';
  src: local('TAU-Marutham Bold'), url('<?php echo base_url("assets/fonts/TAU-Marutham Bold.ttf"); ?>') format('truetype');
  font-weight: bold;
  font-style: normal;
}
@font-face {
  font-family: 'TAU-Pallai';
  src: local('TAU-Paalai'), url('<?php echo base_url("assets/fonts/TAU-Paalai.ttf"); ?>') format('truetype');
}

/* Designer Layout - Full Viewport Height with Independent Scrolling */
.designer-container {
  display: flex;
  gap: 0.85rem;
  height: calc(100vh - 130px);
  min-height: 580px;
  margin-bottom: 0.5rem;
}
@media (max-width: 991.98px) {
  .designer-container {
    flex-direction: column;
    height: auto;
  }
}

/* Left Panel - Available Labels */
.designer-left-panel {
  width: 270px;
  flex-shrink: 0;
  height: 100%;
  display: flex;
  flex-direction: column;
}
.designer-left-panel .card {
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.designer-left-scroll-body {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  padding-right: 4px;
}

/* Right Panel - Element Properties */
.designer-right-panel {
  width: 320px;
  flex-shrink: 0;
  height: 100%;
  display: flex;
  flex-direction: column;
}
.designer-right-panel .card {
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.designer-right-scroll-body {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  padding-right: 4px;
}

/* Custom Scrollbars for Left and Right Panels */
.designer-left-scroll-body::-webkit-scrollbar,
.designer-right-scroll-body::-webkit-scrollbar {
  width: 6px;
}
.designer-left-scroll-body::-webkit-scrollbar-thumb,
.designer-right-scroll-body::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.designer-left-scroll-body::-webkit-scrollbar-thumb:hover,
.designer-right-scroll-body::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

@media (max-width: 991.98px) {
  .designer-left-panel, .designer-right-panel {
    width: 100%;
    height: auto;
  }
}

/* Center Panel - Canvas Viewport */
.designer-center-panel {
  flex: 1;
  min-width: 0;
  height: 100%;
  display: flex;
  flex-direction: column;
  background: #f1f5f9;
  border-radius: 1rem;
  overflow: hidden;
  position: relative;
  border: 1px solid #cbd5e1;
}
.canvas-header-bar {
  flex-shrink: 0;
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  padding: 0.45rem 0.85rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
  z-index: 10;
}
.canvas-viewport {
  flex: 1;
  overflow: auto;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  background: radial-gradient(#cbd5e1 1.2px, transparent 1.2px);
  background-size: 18px 18px;
  position: relative;
}
#paperWrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: auto;
  transition: width 0.1s ease, height 0.1s ease;
}
.paper-shadow {
  box-shadow: 0 16px 45px rgba(0, 0, 0, 0.22), 0 3px 10px rgba(0, 0, 0, 0.08);
  border-radius: 4px;
  background: #ffffff;
  position: relative;
  border: 1.5px solid #cbd5e1;
  transform-origin: center center;
  transition: transform 0.1s ease;
  overflow: visible;
}

/* Realistic Envelope Model Styling */
.envelope-model {
  background: #ffffff;
}
/* Top flap crease line with subtle 3D paper shadow */
.envelope-model::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 26px;
  background: linear-gradient(180deg, rgba(0, 0, 0, 0.05) 0%, rgba(0, 0, 0, 0.01) 85%, transparent 100%);
  border-bottom: 1.5px dashed rgba(148, 163, 184, 0.45);
  pointer-events: none;
  z-index: 1;
}
/* Center Flap Tongue / Notch */
.envelope-model .envelope-flap-notch {
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 90px;
  height: 22px;
  background: #f8fafc;
  border: 1.5px dashed rgba(148, 163, 184, 0.5);
  border-top: none;
  border-radius: 0 0 16px 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 8.5px;
  font-weight: 700;
  color: #94a3b8;
  letter-spacing: 1px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04);
  pointer-events: none;
  z-index: 2;
  user-select: none;
}
/* Right Side Wallet Flap Tab */
.envelope-model::after {
  content: 'FLAP';
  position: absolute;
  top: 0;
  right: -24px;
  width: 24px;
  height: 100%;
  border: 1.5px dashed #64748b;
  border-left: none;
  border-radius: 0 14px 14px 0;
  background: linear-gradient(90deg, rgba(241, 245, 249, 0.9) 0%, rgba(226, 232, 240, 0.5) 100%);
  color: #64748b;
  font-size: 9px;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  writing-mode: vertical-rl;
  letter-spacing: 2px;
  pointer-events: none;
  z-index: 2;
  user-select: none;
}
/* Stamp Box Guide on Upper Right */
.envelope-model .envelope-stamp-box {
  position: absolute;
  top: 12px;
  right: 14px;
  width: 58px;
  height: 68px;
  border: 1.5px dashed rgba(148, 163, 184, 0.5);
  border-radius: 3px;
  background: rgba(248, 250, 252, 0.55);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.5px;
  pointer-events: none;
  z-index: 1;
  user-select: none;
}
.envelope-model .envelope-stamp-box i {
  font-size: 1.25rem;
  margin-bottom: 2px;
  opacity: 0.7;
}
/* Subtle Postal Indicator at Bottom Left */
.envelope-model .envelope-corner-bl {
  position: absolute;
  bottom: 8px;
  left: 12px;
  font-size: 9px;
  color: #94a3b8;
  font-weight: 600;
  letter-spacing: 0.5px;
  pointer-events: none;
  z-index: 1;
  user-select: none;
  opacity: 0.75;
}

/* Visual Millimeter Grid Overlay (5mm fine grid, 25mm major grid) */
#paperContainer.show-grid::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background-image: 
    linear-gradient(to right, rgba(13, 110, 253, 0.14) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(13, 110, 253, 0.14) 1px, transparent 1px),
    linear-gradient(to right, rgba(13, 110, 253, 0.35) 1.5px, transparent 1.5px),
    linear-gradient(to bottom, rgba(13, 110, 253, 0.35) 1.5px, transparent 1.5px);
  background-size: 18.9px 18.9px, 18.9px 18.9px, 94.5px 94.5px, 94.5px 94.5px;
  pointer-events: none;
  z-index: 15;
}

.draggable-label-btn {
  width: 100%;
  text-align: left;
  border-radius: 0.5rem;
  font-size: 0.82rem;
  padding: 0.45rem 0.65rem;
  margin-bottom: 0.35rem;
  cursor: grab;
  transition: all 0.15s ease;
}
.draggable-label-btn:hover {
  background-color: #e8f0fe;
  border-color: #0d6efd;
  color: #0d6efd;
  transform: translateX(3px);
}
.draggable-label-btn:active {
  cursor: grabbing;
}
</style>

<!-- Fabric.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

<div class="pagetitle mb-2">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="d-flex align-items-center gap-2 text-dark fw-bold mb-0 fs-4">
        <i class="bi bi-palette-fill text-primary"></i>
        <span>Cover Designer</span>
      </h1>
      <p class="text-muted small mb-0" style="font-size: 0.78rem;">Visual drag-and-drop designer for Kalolsavam cover printing (1 Item = 1 Cover).</p>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 bg-light px-3 py-1 rounded-pill border" style="font-size: 0.8rem;">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none"><i class="bi bi-house-door-fill me-1"></i>Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>report/prereport/callsheet_first" class="text-decoration-none">Stage Reports</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>cover/templates" class="text-decoration-none">Cover Print</a></li>
        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Designer</li>
      </ol>
    </nav>
  </div>
</div>

<!-- Top Toolbar -->
<div class="card border-0 shadow-sm rounded-4 mb-2">
  <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    
    <!-- Template Name & Back -->
    <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 320px;">
      <a href="<?php echo base_url('cover/templates'); ?>" class="btn btn-outline-secondary btn-sm rounded-circle p-1" title="Back to Templates">
        <i class="bi bi-arrow-left fs-6"></i>
      </a>
      <input type="text" id="txtTemplateName" class="form-control form-control-sm fw-bold border-0 bg-light rounded-pill px-3" value="<?php echo esc($templateName); ?>" placeholder="Enter Template Name..." title="Template Name" required>
    </div>

    <!-- Paper Settings -->
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div class="d-flex align-items-center gap-1">
        <label for="cboPaperSize" class="small fw-semibold text-muted mb-0">Paper:</label>
        <select id="cboPaperSize" class="form-select form-select-sm rounded-pill" style="width: 140px;">
          <option value="DL" <?php echo ($paperSize === 'DL') ? 'selected' : ''; ?>>DL Envelope (220×110)</option>
          <option value="C5" <?php echo ($paperSize === 'C5') ? 'selected' : ''; ?>>C5 Envelope (229×162)</option>
          <option value="C4" <?php echo ($paperSize === 'C4') ? 'selected' : ''; ?>>C4 Envelope (324×229)</option>
          <option value="A4" <?php echo ($paperSize === 'A4') ? 'selected' : ''; ?>>A4 (210×297 mm)</option>
          <option value="A5" <?php echo ($paperSize === 'A5') ? 'selected' : ''; ?>>A5 (148×210 mm)</option>
          <option value="A3" <?php echo ($paperSize === 'A3') ? 'selected' : ''; ?>>A3 (297×420 mm)</option>
          <option value="Letter" <?php echo ($paperSize === 'Letter') ? 'selected' : ''; ?>>Letter</option>
          <option value="Legal" <?php echo ($paperSize === 'Legal') ? 'selected' : ''; ?>>Legal</option>
          <option value="Custom" <?php echo ($paperSize === 'Custom') ? 'selected' : ''; ?>>Custom...</option>
        </select>
      </div>

      <!-- Custom Dimensions (Visible only if Custom) -->
      <div id="customDimensions" class="d-flex align-items-center gap-1 <?php echo ($paperSize === 'Custom') ? '' : 'd-none'; ?>">
        <input type="number" id="txtCustomWidth" class="form-control form-control-sm rounded-pill" style="width: 75px;" value="<?php echo round($widthMm); ?>" placeholder="W mm" title="Width (mm)">
        <span class="small text-muted">×</span>
        <input type="number" id="txtCustomHeight" class="form-control form-control-sm rounded-pill" style="width: 75px;" value="<?php echo round($heightMm); ?>" placeholder="H mm" title="Height (mm)">
      </div>

      <!-- Orientation Toggle -->
      <div class="btn-group btn-group-sm rounded-pill overflow-hidden border" role="group">
        <button type="button" class="btn <?php echo ($orientation === 'portrait') ? 'btn-primary' : 'btn-light'; ?>" id="btnPortrait" title="Portrait">
          <i class="bi bi-file-earmark"></i> P
        </button>
        <button type="button" class="btn <?php echo ($orientation === 'landscape') ? 'btn-primary' : 'btn-light'; ?>" id="btnLandscape" title="Landscape">
          <i class="bi bi-file-earmark-landscape"></i> L
        </button>
      </div>

      <!-- Grid & Snapping -->
      <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle" id="btnToggleGrid" title="Toggle Grid Snapping">
        <i class="bi bi-grid-3x3"></i>
      </button>

      <!-- Undo / Redo -->
      <div class="btn-group btn-group-sm rounded-pill" role="group">
        <button type="button" class="btn btn-outline-secondary" id="btnUndo" title="Undo (Ctrl+Z)"><i class="bi bi-arrow-counterclockwise"></i></button>
        <button type="button" class="btn btn-outline-secondary" id="btnRedo" title="Redo (Ctrl+Y)"><i class="bi bi-arrow-clockwise"></i></button>
      </div>
    </div>

    <!-- Actions: Preview & Save -->
    <div class="d-flex align-items-center gap-2">
      <div class="form-check form-switch d-flex align-items-center mb-0 me-1" title="Set as default template for this sub-district">
        <input class="form-check-input" type="checkbox" id="chkIsDefault" <?php echo $isDefault ? 'checked' : ''; ?>>
        <label class="form-check-label small ms-1 text-muted fw-semibold" for="chkIsDefault">Default</label>
      </div>

      <button type="button" class="btn btn-outline-info btn-sm rounded-pill px-3 fw-semibold" id="btnToggleRealData" title="Preview with Real Data">
        <i class="bi bi-eye-fill me-1"></i> <span id="lblPreviewMode">Real Data</span>
      </button>

      <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold shadow-sm" id="btnSaveTemplate">
        <i class="bi bi-floppy-fill me-1"></i> Save
      </button>
    </div>

  </div>
</div>

<!-- Main 3-Panel Designer Area -->
<div class="designer-container">

  <!-- LEFT PANEL: Available Labels & Assets (Full Height & Independent Scroll) -->
  <div class="designer-left-panel">
    <div class="card border-0 shadow-sm rounded-4 p-3">
      
      <!-- Panel Sticky Header -->
      <div class="d-flex align-items-center justify-content-between mb-2 flex-shrink-0 pb-2 border-bottom">
        <h6 class="fw-bold text-dark d-flex align-items-center mb-0">
          <i class="bi bi-tags-fill text-primary me-2"></i>Available Labels
        </h6>
        <button type="button" class="btn btn-sm btn-outline-primary rounded-circle p-1" id="btnOpenCustomLabelModal" title="Add Custom Label">
          <i class="bi bi-plus-lg"></i>
        </button>
      </div>

      <!-- Scrollable Body (Independent scroll) -->
      <div class="designer-left-scroll-body">
        
        <div class="text-muted small mb-2 fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Dynamic Item Data</div>
        
        <?php foreach ($dynamicFields as $df): ?>
          <button type="button" class="btn btn-light border draggable-label-btn d-flex align-items-center justify-content-between" 
                  data-field="<?php echo esc($df['field']); ?>" 
                  data-label="<?php echo esc($df['label']); ?>" 
                  data-tag="<?php echo esc($df['tag']); ?>"
                  onclick="addDynamicElement('<?php echo esc($df['field']); ?>', '<?php echo esc($df['label']); ?>', '<?php echo esc($df['tag']); ?>')">
            <span class="fw-semibold text-truncate"><?php echo esc($df['label']); ?></span>
            <i class="bi bi-plus text-primary fs-5"></i>
          </button>
        <?php endforeach; ?>

        <?php if (!empty($customLabels)): ?>
          <div class="text-muted small mt-3 mb-1 fw-semibold text-uppercase" style="font-size: 0.72rem;">Custom Labels</div>
          <?php foreach ($customLabels as $cl): ?>
            <button type="button" class="btn btn-light border draggable-label-btn d-flex align-items-center justify-content-between"
                    data-field="<?php echo esc($cl['field_name'] ?? ''); ?>" 
                    data-label="<?php echo esc($cl['label_name']); ?>" 
                    data-tag="<?php echo esc($cl['default_text'] ?? $cl['label_name']); ?>"
                    onclick="addCustomElement('<?php echo esc($cl['label_type']); ?>', '<?php echo esc($cl['label_name']); ?>', '<?php echo esc($cl['default_text'] ?? ''); ?>')">
              <span class="fw-semibold text-truncate"><?php echo esc($cl['label_name']); ?></span>
              <i class="bi bi-plus text-success fs-5"></i>
            </button>
          <?php endforeach; ?>
        <?php endif; ?>

        <hr class="my-3 text-muted">

        <div class="text-muted small mb-2 fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Add Text &amp; Assets</div>

        <button type="button" class="btn btn-outline-secondary w-100 rounded-pill btn-sm mb-2 text-start px-3 fw-semibold" onclick="addStaticText()">
          <i class="bi bi-fonts me-2 text-primary"></i> + Static Text
        </button>

        <button type="button" class="btn btn-outline-secondary w-100 rounded-pill btn-sm mb-3 text-start px-3 fw-semibold" onclick="document.getElementById('fileUploadAsset').click()">
          <i class="bi bi-image me-2 text-success"></i> + School Logo / Image
        </button>
        <input type="file" id="fileUploadAsset" class="d-none" accept=".png,.jpg,.jpeg,.svg" onchange="handleAssetUpload(this)">

        <div class="p-2.5 bg-light rounded-3 text-center border mt-2">
          <div class="small text-muted" style="font-size: 0.76rem;">
            <i class="bi bi-info-circle me-1 text-primary"></i> Click any label to place it onto the envelope canvas.
          </div>
        </div>

      </div>

    </div>
  </div>

  <!-- CENTER PANEL: Interactive Paper Canvas with Zoom Bar & Auto-Fit -->
  <div class="designer-center-panel">
    
    <!-- Canvas Top Header Bar (Zoom & View Controls) -->
    <div class="canvas-header-bar">
      <!-- Left: Dimensions & Preset badge -->
      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-semibold" id="badgePaperDimensions">
          <i class="bi bi-envelope-paper me-1"></i> DL Envelope (220 × 110 mm) – Landscape
        </span>
      </div>

      <!-- Right: Full Zoom Options & Controls -->
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <!-- Zoom Out -->
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" id="btnZoomOut" title="Zoom Out (-10%)" style="width: 30px; height: 30px; padding: 0;">
          <i class="bi bi-dash"></i>
        </button>

        <!-- Zoom Slider -->
        <div class="d-flex align-items-center" style="width: 100px;">
          <input type="range" class="form-range" id="zoomSlider" min="30" max="200" step="5" value="100" title="Slide to Zoom">
        </div>

        <!-- Zoom In -->
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" id="btnZoomIn" title="Zoom In (+10%)" style="width: 30px; height: 30px; padding: 0;">
          <i class="bi bi-plus"></i>
        </button>

        <!-- Zoom Pct Text -->
        <span class="badge bg-light text-dark border px-2 py-1.5 fw-bold" id="lblZoomPct" style="min-width: 46px;">100%</span>

        <!-- Preset Dropdown -->
        <select id="cboZoomPreset" class="form-select form-select-sm rounded-pill" style="width: 88px;">
          <option value="100">100%</option>
          <option value="85">85%</option>
          <option value="75">75%</option>
          <option value="65">65%</option>
          <option value="50">50%</option>
          <option value="125">125%</option>
          <option value="150">150%</option>
        </select>

        <div class="vr mx-1 my-1"></div>

        <!-- Fit Width -->
        <button type="button" class="btn btn-sm btn-primary rounded-pill px-2.5 py-1 fw-semibold" id="btnFitWidth" title="Fit to Container Width (பக்க அகலத்திற்கு பொருத்து)">
          <i class="bi bi-arrows-expand-vertical me-1"></i> Fit Width
        </button>

        <!-- Fit Page -->
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1" id="btnFitPage" title="Fit entire cover on screen (முழு திரை)">
          <i class="bi bi-aspect-ratio me-1"></i> Fit Page
        </button>

        <!-- 1:1 Reset -->
        <button type="button" class="btn btn-sm btn-light border rounded-pill px-2 py-1" id="btnZoom100" title="Reset to 100%">
          1:1
        </button>
      </div>
    </div>

    <!-- Canvas Viewport -->
    <div class="canvas-viewport" id="canvasViewport">
      <div id="paperWrapper">
        <div class="paper-shadow envelope-model" id="paperContainer">
          <div class="envelope-flap-notch" id="envelopeFlapNotch">FLAP ▾</div>
          <div class="envelope-stamp-box" id="envelopeStampBox">
            <i class="bi bi-postage-heart"></i>
            <span>STAMP</span>
          </div>
          <div class="envelope-corner-bl" id="envelopeWatermark">
            <i class="bi bi-envelope-paper me-1"></i> ENVELOPE COVER
          </div>
          <canvas id="coverCanvas"></canvas>
        </div>
      </div>
    </div>

  </div>

  <!-- RIGHT PANEL: Element Properties & Styling (Full Height & Independent Scroll) -->
  <div class="designer-right-panel">
    <div class="card border-0 shadow-sm rounded-4 p-3">
      
      <!-- Panel Sticky Header -->
      <div class="d-flex align-items-center justify-content-between mb-2 flex-shrink-0 pb-2 border-bottom">
        <h6 class="fw-bold text-dark d-flex align-items-center mb-0">
          <i class="bi bi-sliders text-primary me-2"></i>Element Properties
        </h6>
      </div>

      <!-- Scrollable Body (Independent scroll) -->
      <div class="designer-right-scroll-body">
        
        <div id="noSelectionMsg" class="p-4 bg-light rounded-4 text-center border">
          <i class="bi bi-cursor text-muted fs-3 mb-2 d-block"></i>
          <p class="small text-muted mb-0">Select any element on the paper canvas to edit its font, position, color, and alignment.</p>
        </div>

        <!-- Properties Form (Active when element is selected) -->
        <div id="propertiesForm" class="d-none">

          <!-- Text / Content -->
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted mb-1">Text Content / Template</label>
            <textarea id="propText" class="form-control form-control-sm rounded-3" rows="2"></textarea>
          </div>

          <!-- Font Family & Font Size -->
          <div class="row g-2 mb-3">
            <div class="col-7">
              <label class="form-label small fw-bold text-muted mb-1">Font Family</label>
              <select id="propFontFamily" class="form-select form-select-sm rounded-pill">
                <option value="TAU-Paalai" selected>TAU-Paalai (Default)</option>
                <option value="TAU-Maduaram">TAU-Maduaram</option>
                <option value="Arial">Arial</option>
                <option value="Times New Roman">Times New Roman</option>
              </select>
            </div>
            <div class="col-5">
              <label class="form-label small fw-bold text-muted mb-1">Size (pt)</label>
              <input type="number" id="propFontSize" class="form-control form-control-sm rounded-pill" value="14" min="8" max="72">
            </div>
          </div>

          <!-- Styles: Bold, Italic, Underline & Color -->
          <div class="row g-2 mb-3 align-items-center">
            <div class="col-7">
              <label class="form-label small fw-bold text-muted mb-1">Style &amp; Align</label>
              <div class="d-flex align-items-center gap-1">
                <div class="btn-group btn-group-sm rounded-pill overflow-hidden border">
                  <button type="button" class="btn btn-light" id="btnBold" title="Bold"><i class="bi bi-type-bold"></i></button>
                  <button type="button" class="btn btn-light" id="btnItalic" title="Italic"><i class="bi bi-type-italic"></i></button>
                  <button type="button" class="btn btn-light" id="btnUnderline" title="Underline"><i class="bi bi-type-underline"></i></button>
                </div>
              </div>
            </div>
            <div class="col-5">
              <label class="form-label small fw-bold text-muted mb-1">Color</label>
              <div class="input-group input-group-sm">
                <input type="color" id="propFontColor" class="form-control form-control-color rounded-pill p-1" value="#000000" style="width: 42px; height: 31px;">
                <input type="text" id="propFontColorHex" class="form-control form-control-sm rounded-end-pill" value="#000000" style="font-size: 0.75rem;">
              </div>
            </div>
          </div>

          <!-- Text Alignment -->
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted mb-1">Text Alignment</label>
            <div class="btn-group btn-group-sm w-100 rounded-pill overflow-hidden border" role="group">
              <button type="button" class="btn btn-light flex-fill" id="btnAlignLeft" title="Left"><i class="bi bi-text-left"></i></button>
              <button type="button" class="btn btn-light flex-fill" id="btnAlignCenter" title="Center"><i class="bi bi-text-center"></i></button>
              <button type="button" class="btn btn-light flex-fill" id="btnAlignRight" title="Right"><i class="bi bi-text-right"></i></button>
            </div>
          </div>

          <!-- Boxed Border Toggle (As shown in hand-drawn sketch) -->
          <div class="form-check form-switch mb-3 p-2 bg-light rounded-3 border">
            <input class="form-check-input ms-0 me-2" type="checkbox" id="propBorderBox" role="switch">
            <label class="form-check-label small fw-bold text-dark" for="propBorderBox">
              <i class="bi bi-square me-1 text-primary"></i> Border Box
            </label>
          </div>

          <!-- Position & Size in Millimeters -->
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-bold text-muted mb-1">X (mm)</label>
              <input type="number" id="propPosX" class="form-control form-control-sm rounded-pill" value="0">
            </div>
            <div class="col-6">
              <label class="form-label small fw-bold text-muted mb-1">Y (mm)</label>
              <input type="number" id="propPosY" class="form-control form-control-sm rounded-pill" value="0">
            </div>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-bold text-muted mb-1">Width (mm)</label>
              <input type="number" id="propWidth" class="form-control form-control-sm rounded-pill" value="50">
            </div>
            <div class="col-6">
              <label class="form-label small fw-bold text-muted mb-1">Rotation (°)</label>
              <input type="number" id="propRotation" class="form-control form-control-sm rounded-pill" value="0" min="-360" max="360">
            </div>
          </div>

          <!-- Layer Controls -->
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted mb-1">Layer Order &amp; Actions</label>
            <div class="btn-group btn-group-sm w-100 rounded-pill overflow-hidden border mb-2">
              <button type="button" class="btn btn-light flex-fill" id="btnBringToFront" title="Bring to Front"><i class="bi bi-front"></i> Front</button>
              <button type="button" class="btn btn-light flex-fill" id="btnBringForward" title="Bring Forward"><i class="bi bi-arrow-up-short"></i> Up</button>
              <button type="button" class="btn btn-light flex-fill" id="btnSendBackward" title="Send Backward"><i class="bi bi-arrow-down-short"></i> Down</button>
              <button type="button" class="btn btn-light flex-fill" id="btnSendToBack" title="Send to Back"><i class="bi bi-back"></i> Back</button>
            </div>

            <div class="d-flex align-items-center gap-2">
              <button type="button" class="btn btn-outline-secondary btn-sm flex-fill rounded-pill" id="btnDuplicate" title="Duplicate (Ctrl+D)">
                <i class="bi bi-copy me-1"></i> Duplicate
              </button>
              <button type="button" class="btn btn-outline-danger btn-sm flex-fill rounded-pill" id="btnDeleteElement" title="Delete (Del)">
                <i class="bi bi-trash3 me-1"></i> Delete
              </button>
            </div>
          </div>

        </div>

        <hr class="my-3 text-muted">

        <!-- Paper Background Section -->
        <div>
          <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-paint-bucket me-1"></i> Paper Background</h6>
          <div class="d-flex align-items-center gap-2 mb-2">
            <select id="cboBgType" class="form-select form-select-sm rounded-pill">
              <option value="none" <?php echo ($bgType === 'none') ? 'selected' : ''; ?>>None (White)</option>
              <option value="color" <?php echo ($bgType === 'color') ? 'selected' : ''; ?>>Color Fill</option>
              <option value="image" <?php echo ($bgType === 'image') ? 'selected' : ''; ?>>Background Image</option>
            </select>
          </div>

          <div id="bgColorPickerRow" class="mb-2 <?php echo ($bgType === 'color') ? '' : 'd-none'; ?>">
            <div class="input-group input-group-sm">
              <input type="color" id="propBgColor" class="form-control form-control-color rounded-pill p-1" value="<?php echo esc($bgVal ?: '#ffffff'); ?>" style="width: 42px; height: 31px;">
              <input type="text" id="propBgColorHex" class="form-control form-control-sm rounded-end-pill" value="<?php echo esc($bgVal ?: '#ffffff'); ?>">
            </div>
          </div>

          <div id="bgImageRow" class="mb-2 <?php echo ($bgType === 'image') ? '' : 'd-none'; ?>">
            <button type="button" class="btn btn-outline-secondary btn-sm w-100 rounded-pill" onclick="document.getElementById('fileUploadBg').click()">
              <i class="bi bi-upload me-1"></i> Upload Background Image
            </button>
            <input type="file" id="fileUploadBg" class="d-none" accept=".png,.jpg,.jpeg" onchange="handleBgUpload(this)">
            <div id="lblBgImgName" class="small text-muted text-truncate mt-1"><?php echo esc($bgVal); ?></div>
          </div>
        </div>

      </div>

    </div>
  </div>

</div>

<!-- Modal: Create Custom Label -->
<div class="modal fade" id="modalCustomLabel" tabindex="-1" aria-labelledby="modalCustomLabelLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header border-bottom py-3 px-4">
        <h5 class="modal-title fw-bold" id="modalCustomLabelLabel"><i class="bi bi-tag-fill text-primary me-2"></i>Create New Label</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="mb-3">
          <label for="txtNewLabelName" class="form-label small fw-bold">Label Name <span class="text-danger">*</span></label>
          <input type="text" id="txtNewLabelName" class="form-control rounded-pill" placeholder="e.g. Festival Motto">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-bold">Label Type</label>
          <div class="d-flex gap-4">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="radLabelType" id="radStaticText" value="static_text" checked>
              <label class="form-check-label" for="radStaticText">Static Text</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="radLabelType" id="radDynamicField" value="dynamic_field">
              <label class="form-check-label" for="radDynamicField">Dynamic Field</label>
            </div>
          </div>
        </div>
        <div class="mb-3">
          <label for="txtNewDefaultText" class="form-label small fw-bold">Default Text</label>
          <input type="text" id="txtNewDefaultText" class="form-control rounded-pill" placeholder="e.g. KALOLSAVAM 2026">
        </div>
      </div>
      <div class="modal-footer border-top py-2 px-4">
        <button type="button" class="btn btn-secondary rounded-pill btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary rounded-pill btn-sm px-4 fw-semibold" id="btnSaveCustomLabel">Save Label</button>
      </div>
    </div>
  </div>
</div>

<!-- JAVASCRIPT: Interactive Canvas Engine -->
<script>
// State variables
var templateId = <?php echo (int)$templateId; ?>;
var paperSize = "<?php echo esc($paperSize); ?>";
var paperWidthMm = <?php echo (float)$widthMm; ?>;
var paperHeightMm = <?php echo (float)$heightMm; ?>;
var paperOrientation = "<?php echo esc($orientation); ?>";
var backgroundType = "<?php echo esc($bgType); ?>";
var backgroundValue = "<?php echo esc($bgVal); ?>";
var isRealDataPreview = false;

// Standard mm conversion factor (3.7795 px per mm at 96 DPI)
var MM_TO_PX = 3.7795275591;

// Paper size catalog in mm (DL Envelope first)
var PAPER_SIZES = {
  'DL':     [220, 110],
  'C5':     [229, 162],
  'C4':     [324, 229],
  'A4':     [210, 297],
  'A5':     [148, 210],
  'A3':     [297, 420],
  'Letter': [215.9, 279.4],
  'Legal':  [215.9, 355.6]
};

// Initial Elements from Database
var initialElements = <?php echo json_encode($existingElements); ?>;
var sampleItemData = <?php echo json_encode($sampleData); ?> || {};
if (!sampleItemData.sub_district) sampleItemData.sub_district = 'Chittur';
if (!sampleItemData.district) sampleItemData.district = 'Palakkad';
if (!sampleItemData.fest_venue) sampleItemData.fest_venue = 'GHSS Kozhippara';
if (!sampleItemData.stage) sampleItemData.stage = 'Stage 1';
if (!sampleItemData.stage_desc) sampleItemData.stage_desc = 'Main Auditorium';
if (!sampleItemData.venue) sampleItemData.venue = 'GHSS Kozhippara';

// Fabric.js Canvas instance & dimensions
var canvas;
var currentZoom = 1.0;
var unzoomedWidthPx = 0;
var unzoomedHeightPx = 0;
var undoStack = [];
var redoStack = [];
var isRecordingState = true;
var snapGridSizeMm = 5; // 5mm grid
var isSnapGridEnabled = false;

document.addEventListener('DOMContentLoaded', function() {
  initDesigner();
});

function initDesigner() {
  // Initialize Fabric Canvas
  canvas = new fabric.Canvas('coverCanvas', {
    preserveObjectStacking: true,
    selection: true,
    uniformScaling: false
  });

  // Calculate pixel dimensions and auto-fit to container width
  updatePaperDimensions();

  // Load existing elements
  loadInitialElements();

  // Event Listeners for Object Selection & Transformation
  canvas.on('selection:created', onObjectSelected);
  canvas.on('selection:updated', onObjectSelected);
  canvas.on('selection:cleared', onObjectDeselected);
  canvas.on('object:modified', function() {
    saveCanvasState();
    updatePropertiesFromSelection();
  });
  canvas.on('object:moving', onObjectMoving);

  // Keyboard Shortcuts
  window.addEventListener('keydown', handleKeydown);

  // Setup UI Listeners
  setupEventListeners();

  // Initial State
  saveCanvasState();

  // Auto-fit to container width with slight delay for container rendering
  setTimeout(function() {
    fitToWidth();
  }, 100);
}

function updatePaperDimensions() {
  var isLandscape = (paperOrientation === 'landscape');
  var w = paperWidthMm;
  var h = paperHeightMm;

  if (isLandscape && w < h) {
    var tmp = w; w = h; h = tmp;
  } else if (!isLandscape && w > h) {
    var tmp = w; w = h; h = tmp;
  }
  paperWidthMm = w;
  paperHeightMm = h;

  unzoomedWidthPx = Math.round(paperWidthMm * MM_TO_PX);
  unzoomedHeightPx = Math.round(paperHeightMm * MM_TO_PX);

  var container = document.getElementById('paperContainer');
  if (container) {
    container.style.width = unzoomedWidthPx + 'px';
    container.style.height = unzoomedHeightPx + 'px';
    container.classList.add('envelope-model');
  }

  canvas.setWidth(unzoomedWidthPx);
  canvas.setHeight(unzoomedHeightPx);

  // Update Paper Dimension Badge
  var badge = document.getElementById('badgePaperDimensions');
  if (badge) {
    var label = (paperSize === 'DL') ? 'DL Envelope (220 × 110 mm)' : (paperSize === 'C5' ? 'C5 Envelope (229 × 162 mm)' : paperSize);
    badge.innerHTML = '<i class="bi bi-envelope-paper me-1"></i> ' + label + ' (' + Math.round(w) + ' × ' + Math.round(h) + ' mm) – ' + (isLandscape ? 'Landscape' : 'Portrait');
  }

  // Apply background
  applyCanvasBackground();
  canvas.renderAll();

  // Re-scale paper to fit container
  fitToWidth();
}

function setZoomLevel(zoom, updateControls) {
  if (updateControls === undefined) updateControls = true;
  zoom = Math.max(0.25, Math.min(2.5, zoom));
  currentZoom = parseFloat(zoom.toFixed(2));

  var scaledW = Math.round(unzoomedWidthPx * currentZoom);
  var scaledH = Math.round(unzoomedHeightPx * currentZoom);

  var paperContainer = document.getElementById('paperContainer');
  var paperWrapper = document.getElementById('paperWrapper');

  if (paperContainer) {
    paperContainer.style.transform = 'scale(' + currentZoom + ')';
    paperContainer.style.transformOrigin = 'center center';
  }

  if (paperWrapper) {
    paperWrapper.style.width = scaledW + 'px';
    paperWrapper.style.height = scaledH + 'px';
  }

  var pct = Math.round(currentZoom * 100);
  if (updateControls) {
    var slider = document.getElementById('zoomSlider');
    if (slider) slider.value = pct;
    var lbl = document.getElementById('lblZoomPct');
    if (lbl) lbl.innerText = pct + '%';
    var cbo = document.getElementById('cboZoomPreset');
    if (cbo) {
      cbo.value = (pct >= 45 && pct <= 155) ? pct : 'custom';
    }
  }
}

function fitToWidth() {
  var viewport = document.getElementById('canvasViewport');
  if (!viewport || !unzoomedWidthPx) return;
  var availW = viewport.clientWidth - 60; // 30px padding on each side
  if (availW < 150) availW = 600;
  var ratio = availW / unzoomedWidthPx;
  // Fit nicely up to 100%
  var zoom = Math.min(ratio, 1.0);
  setZoomLevel(zoom);
}

function fitToPage() {
  var viewport = document.getElementById('canvasViewport');
  if (!viewport || !unzoomedWidthPx || !unzoomedHeightPx) return;
  var availW = viewport.clientWidth - 60;
  var availH = viewport.clientHeight - 60;
  if (availW < 150) availW = 600;
  if (availH < 150) availH = 400;
  var ratioW = availW / unzoomedWidthPx;
  var ratioH = availH / unzoomedHeightPx;
  var zoom = Math.min(ratioW, ratioH, 1.0);
  setZoomLevel(zoom);
}

function applyCanvasBackground() {
  if (backgroundType === 'color' && backgroundValue) {
    canvas.setBackgroundColor(backgroundValue, canvas.renderAll.bind(canvas));
  } else if (backgroundType === 'image' && backgroundValue) {
    fabric.Image.fromURL('<?php echo base_url(); ?>' + backgroundValue.replace(/^\/+/, ''), function(img) {
      img.scaleToWidth(canvas.getWidth());
      canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
    });
  } else {
    // Keep canvas transparent so realistic envelope model and paper texture are visible
    canvas.setBackgroundColor('transparent', canvas.renderAll.bind(canvas));
    canvas.setBackgroundImage(null, canvas.renderAll.bind(canvas));
  }
}

function loadInitialElements() {
  if (!initialElements || !initialElements.length) return;

  initialElements.forEach(function(el) {
    var xPx = (parseFloat(el.x) || 0) * MM_TO_PX;
    var yPx = (parseFloat(el.y) || 0) * MM_TO_PX;
    var wPx = (parseFloat(el.width) || 50) * MM_TO_PX;
    var fontSizePx = Math.round((parseInt(el.font_size) || 14) * 1.333);

    if (el.element_type === 'image' && el.static_text) {
      fabric.Image.fromURL('<?php echo base_url(); ?>' + el.static_text.replace(/^\/+/, ''), function(img) {
        img.set({
          left: xPx,
          top: yPx,
          scaleX: wPx / img.width,
          scaleY: wPx / img.width,
          angle: parseFloat(el.rotation) || 0,
          element_type: 'image',
          custom_id: el.id,
          static_text: el.static_text
        });
        canvas.add(img);
        canvas.renderAll();
      });
      return;
    }

    var hasBorder = (el.border === 1 || el.border === '1');
    if (!hasBorder && el.properties_json) {
      try {
        var pj = (typeof el.properties_json === 'string') ? JSON.parse(el.properties_json) : el.properties_json;
        if (pj && pj.border) hasBorder = true;
      } catch(e) {}
    }

    var textContent = el.static_text || '{{' + (el.field_name || 'item_name') + '}}';
    var textObj = new fabric.Textbox(textContent, {
      left: xPx,
      top: yPx,
      width: wPx,
      fontFamily: el.font_family || 'TAU-Paalai',
      fontSize: fontSizePx,
      fontWeight: el.font_weight || 'normal',
      fontStyle: el.font_style || 'normal',
      underline: (el.text_decoration === 'underline'),
      fill: el.font_color || '#000000',
      textAlign: el.text_align || 'center',
      angle: parseFloat(el.rotation) || 0,
      element_type: el.element_type || 'dynamic_field',
      field_name: el.field_name || '',
      label_name: el.label_name || 'Label',
      static_text: textContent,
      stroke: hasBorder ? '#000000' : null,
      strokeWidth: hasBorder ? 1.5 : 0,
      border: hasBorder ? 1 : 0,
      custom_id: el.id
    });

    canvas.add(textObj);
  });

  canvas.renderAll();
}

function addDynamicElement(fieldName, labelName, tag) {
  var fontSizePt = 16;
  if (fieldName === 'item_code' || fieldName === 'item_name') fontSizePt = 20;

  var wMm = 140;
  var wPx = wMm * MM_TO_PX;
  var fontSizePx = Math.round(fontSizePt * 1.333);

  var textObj = new fabric.Textbox(tag, {
    left: (canvas.getWidth() - wPx) / 2,
    top: 50 + (canvas.getObjects().length * 35) % (canvas.getHeight() - 100),
    width: wPx,
    fontFamily: 'TAU-Paalai',
    fontSize: fontSizePx,
    fontWeight: (fieldName === 'item_code' || fieldName === 'item_name' || fieldName === 'category') ? 'bold' : 'normal',
    fill: '#000000',
    textAlign: 'center',
    element_type: 'dynamic_field',
    field_name: fieldName,
    label_name: labelName,
    static_text: tag
  });

  canvas.add(textObj);
  canvas.setActiveObject(textObj);
  canvas.renderAll();
  saveCanvasState();
}

function addCustomElement(labelType, labelName, defaultText) {
  var tag = defaultText || labelName;
  var wPx = 120 * MM_TO_PX;

  var textObj = new fabric.Textbox(tag, {
    left: (canvas.getWidth() - wPx) / 2,
    top: 80,
    width: wPx,
    fontFamily: 'TAU-Paalai',
    fontSize: Math.round(14 * 1.333),
    fill: '#000000',
    textAlign: 'center',
    element_type: labelType,
    label_name: labelName,
    static_text: tag
  });

  canvas.add(textObj);
  canvas.setActiveObject(textObj);
  canvas.renderAll();
  saveCanvasState();
}

function addStaticText() {
  var textObj = new fabric.Textbox('Static Text', {
    left: canvas.getWidth() / 4,
    top: canvas.getHeight() / 3,
    width: 100 * MM_TO_PX,
    fontFamily: 'TAU-Paalai',
    fontSize: Math.round(16 * 1.333),
    fill: '#000000',
    textAlign: 'center',
    element_type: 'static_text',
    label_name: 'Static Text',
    static_text: 'Static Text'
  });

  canvas.add(textObj);
  canvas.setActiveObject(textObj);
  canvas.renderAll();
  saveCanvasState();
}

function onObjectSelected(e) {
  document.getElementById('noSelectionMsg').classList.add('d-none');
  document.getElementById('propertiesForm').classList.remove('d-none');
  updatePropertiesFromSelection();
}

function onObjectDeselected() {
  document.getElementById('noSelectionMsg').classList.remove('d-none');
  document.getElementById('propertiesForm').classList.add('d-none');
}

function updatePropertiesFromSelection() {
  var obj = canvas.getActiveObject();
  if (!obj) return;

  var isText = obj.type === 'textbox' || obj.type === 'text';

  document.getElementById('propText').value = isText ? (obj.static_text !== undefined ? obj.static_text : obj.text) : '';
  document.getElementById('propText').disabled = !isText;

  document.getElementById('propFontFamily').value = obj.fontFamily || 'TAU-Paalai';
  var ptSize = Math.round((obj.fontSize || 18) / 1.333);
  document.getElementById('propFontSize').value = ptSize;

  document.getElementById('btnBold').classList.toggle('btn-primary', obj.fontWeight === 'bold');
  document.getElementById('btnBold').classList.toggle('btn-light', obj.fontWeight !== 'bold');

  document.getElementById('btnItalic').classList.toggle('btn-primary', obj.fontStyle === 'italic');
  document.getElementById('btnItalic').classList.toggle('btn-light', obj.fontStyle !== 'italic');

  document.getElementById('btnUnderline').classList.toggle('btn-primary', !!obj.underline);
  document.getElementById('btnUnderline').classList.toggle('btn-light', !obj.underline);

  var hexColor = obj.fill || '#000000';
  document.getElementById('propFontColor').value = hexColor;
  document.getElementById('propFontColorHex').value = hexColor;

  var align = obj.textAlign || 'center';
  document.getElementById('btnAlignLeft').classList.toggle('btn-primary', align === 'left');
  document.getElementById('btnAlignCenter').classList.toggle('btn-primary', align === 'center');
  document.getElementById('btnAlignRight').classList.toggle('btn-primary', align === 'right');

  document.getElementById('propPosX').value = Math.round(obj.left / MM_TO_PX);
  document.getElementById('propPosY').value = Math.round(obj.top / MM_TO_PX);
  document.getElementById('propWidth').value = Math.round((obj.width * (obj.scaleX || 1)) / MM_TO_PX);
  document.getElementById('propRotation').value = Math.round(obj.angle || 0);

  var propBorderBox = document.getElementById('propBorderBox');
  if (propBorderBox) {
    var hasBorder = (obj.border === 1 || obj.border === '1' || (obj.strokeWidth && obj.strokeWidth > 0));
    propBorderBox.checked = !!hasBorder;
  }
}

// Snapping to Center Alignment Guides
function onObjectMoving(e) {
  var obj = e.target;
  var canvasW = canvas.getWidth();
  var canvasH = canvas.getHeight();

  var objCenter = obj.getCenterPoint();
  var snapThreshold = 10; // px

  // Horizontal Center Snap
  if (Math.abs(objCenter.x - canvasW / 2) < snapThreshold) {
    obj.set({ left: (canvasW - (obj.width * (obj.scaleX || 1))) / 2 });
  }

  // Vertical Center Snap
  if (Math.abs(objCenter.y - canvasH / 2) < snapThreshold) {
    obj.set({ top: (canvasH - (obj.height * (obj.scaleY || 1))) / 2 });
  }

  // Snap to Grid if enabled
  if (isSnapGridEnabled) {
    var gridPx = snapGridSizeMm * MM_TO_PX;
    obj.set({
      left: Math.round(obj.left / gridPx) * gridPx,
      top: Math.round(obj.top / gridPx) * gridPx
    });
  }

  updatePropertiesFromSelection();
}

function setupEventListeners() {
  // Paper Size Dropdown
  document.getElementById('cboPaperSize').addEventListener('change', function() {
    var sz = this.value;
    paperSize = sz;
    if (PAPER_SIZES[sz]) {
      paperWidthMm = PAPER_SIZES[sz][0];
      paperHeightMm = PAPER_SIZES[sz][1];
      document.getElementById('customDimensions').classList.add('d-none');
    } else {
      document.getElementById('customDimensions').classList.remove('d-none');
      paperWidthMm = parseFloat(document.getElementById('txtCustomWidth').value) || 210;
      paperHeightMm = parseFloat(document.getElementById('txtCustomHeight').value) || 297;
    }
    updatePaperDimensions();
  });

  // Custom Dimensions
  document.getElementById('txtCustomWidth').addEventListener('input', function() {
    paperWidthMm = parseFloat(this.value) || 210;
    updatePaperDimensions();
  });
  document.getElementById('txtCustomHeight').addEventListener('input', function() {
    paperHeightMm = parseFloat(this.value) || 297;
    updatePaperDimensions();
  });

  // Orientation Buttons
  document.getElementById('btnPortrait').addEventListener('click', function() {
    paperOrientation = 'portrait';
    this.classList.replace('btn-light', 'btn-primary');
    document.getElementById('btnLandscape').classList.replace('btn-primary', 'btn-light');
    updatePaperDimensions();
  });
  document.getElementById('btnLandscape').addEventListener('click', function() {
    paperOrientation = 'landscape';
    this.classList.replace('btn-light', 'btn-primary');
    document.getElementById('btnPortrait').classList.replace('btn-primary', 'btn-light');
    updatePaperDimensions();
  });

  // Grid Snapping & Visual Millimeter Grid Overlay
  var btnToggleGrid = document.getElementById('btnToggleGrid');
  if (btnToggleGrid) {
    btnToggleGrid.addEventListener('click', function() {
      isSnapGridEnabled = !isSnapGridEnabled;
      var paper = document.getElementById('paperContainer');
      if (paper) {
        paper.classList.toggle('show-grid', isSnapGridEnabled);
      }
      this.classList.toggle('btn-primary', isSnapGridEnabled);
      this.classList.toggle('btn-outline-secondary', !isSnapGridEnabled);
      this.title = isSnapGridEnabled ? 'Grid: ON (5mm Snapping)' : 'Toggle Grid Snapping';
    });
  }

  // Text Property Change
  document.getElementById('propText').addEventListener('input', function() {
    var obj = canvas.getActiveObject();
    if (obj && (obj.type === 'textbox' || obj.type === 'text')) {
      obj.set('text', this.value);
      obj.set('static_text', this.value);
      canvas.renderAll();
      saveCanvasState();
    }
  });

  // Font Family
  document.getElementById('propFontFamily').addEventListener('change', function() {
    var obj = canvas.getActiveObject();
    if (obj) {
      obj.set('fontFamily', this.value);
      canvas.renderAll();
      saveCanvasState();
    }
  });

  // Font Size
  document.getElementById('propFontSize').addEventListener('input', function() {
    var obj = canvas.getActiveObject();
    if (obj) {
      obj.set('fontSize', Math.round((parseInt(this.value) || 14) * 1.333));
      canvas.renderAll();
      saveCanvasState();
    }
  });

  // Bold
  document.getElementById('btnBold').addEventListener('click', function() {
    var obj = canvas.getActiveObject();
    if (obj) {
      var isB = (obj.fontWeight === 'bold');
      obj.set('fontWeight', isB ? 'normal' : 'bold');
      this.classList.toggle('btn-primary', !isB);
      this.classList.toggle('btn-light', isB);
      canvas.renderAll();
      saveCanvasState();
    }
  });

  // Italic
  document.getElementById('btnItalic').addEventListener('click', function() {
    var obj = canvas.getActiveObject();
    if (obj) {
      var isI = (obj.fontStyle === 'italic');
      obj.set('fontStyle', isI ? 'normal' : 'italic');
      this.classList.toggle('btn-primary', !isI);
      this.classList.toggle('btn-light', isI);
      canvas.renderAll();
      saveCanvasState();
    }
  });

  // Underline
  document.getElementById('btnUnderline').addEventListener('click', function() {
    var obj = canvas.getActiveObject();
    if (obj) {
      var isU = !!obj.underline;
      obj.set('underline', !isU);
      this.classList.toggle('btn-primary', !isU);
      this.classList.toggle('btn-light', isU);
      canvas.renderAll();
      saveCanvasState();
    }
  });

  // Color
  document.getElementById('propFontColor').addEventListener('input', function() {
    var color = this.value;
    document.getElementById('propFontColorHex').value = color;
    var obj = canvas.getActiveObject();
    if (obj) {
      obj.set('fill', color);
      canvas.renderAll();
      saveCanvasState();
    }
  });
  document.getElementById('propFontColorHex').addEventListener('input', function() {
    var color = this.value;
    document.getElementById('propFontColor').value = color;
    var obj = canvas.getActiveObject();
    if (obj) {
      obj.set('fill', color);
      canvas.renderAll();
      saveCanvasState();
    }
  });

  // Alignment
  ['Left', 'Center', 'Right'].forEach(function(al) {
    document.getElementById('btnAlign' + al).addEventListener('click', function() {
      var alignLower = al.toLowerCase();
      var obj = canvas.getActiveObject();
      if (obj) {
        obj.set('textAlign', alignLower);
        ['Left', 'Center', 'Right'].forEach(function(o) {
          document.getElementById('btnAlign' + o).classList.remove('btn-primary');
          document.getElementById('btnAlign' + o).classList.add('btn-light');
        });
        this.classList.remove('btn-light');
        this.classList.add('btn-primary');
        canvas.renderAll();
        saveCanvasState();
      }
    });
  });

  // Pos X, Y, Width, Rotation
  document.getElementById('propPosX').addEventListener('input', function() {
    var obj = canvas.getActiveObject();
    if (obj) {
      obj.set('left', (parseFloat(this.value) || 0) * MM_TO_PX);
      canvas.renderAll();
    }
  });
  document.getElementById('propPosY').addEventListener('input', function() {
    var obj = canvas.getActiveObject();
    if (obj) {
      obj.set('top', (parseFloat(this.value) || 0) * MM_TO_PX);
      canvas.renderAll();
    }
  });
  document.getElementById('propWidth').addEventListener('input', function() {
    var obj = canvas.getActiveObject();
    if (obj) {
      obj.set('width', (parseFloat(this.value) || 50) * MM_TO_PX);
      canvas.renderAll();
    }
  });
  document.getElementById('propRotation').addEventListener('input', function() {
    var obj = canvas.getActiveObject();
    if (obj) {
      obj.set('angle', parseFloat(this.value) || 0);
      canvas.renderAll();
    }
  });

  // Layer Controls
  document.getElementById('btnBringToFront').addEventListener('click', function() {
    var obj = canvas.getActiveObject();
    if (obj) { canvas.bringToFront(obj); canvas.renderAll(); saveCanvasState(); }
  });
  document.getElementById('btnBringForward').addEventListener('click', function() {
    var obj = canvas.getActiveObject();
    if (obj) { canvas.bringForward(obj); canvas.renderAll(); saveCanvasState(); }
  });
  document.getElementById('btnSendBackward').addEventListener('click', function() {
    var obj = canvas.getActiveObject();
    if (obj) { canvas.sendBackwards(obj); canvas.renderAll(); saveCanvasState(); }
  });
  document.getElementById('btnSendToBack').addEventListener('click', function() {
    var obj = canvas.getActiveObject();
    if (obj) { canvas.sendToBack(obj); canvas.renderAll(); saveCanvasState(); }
  });

  // Duplicate
  document.getElementById('btnDuplicate').addEventListener('click', duplicateSelectedObject);

  // Delete Element
  document.getElementById('btnDeleteElement').addEventListener('click', deleteSelectedObject);

  // Zoom Controls
  var btnZoomIn = document.getElementById('btnZoomIn');
  if (btnZoomIn) {
    btnZoomIn.addEventListener('click', function() {
      setZoomLevel(currentZoom + 0.1);
    });
  }

  var btnZoomOut = document.getElementById('btnZoomOut');
  if (btnZoomOut) {
    btnZoomOut.addEventListener('click', function() {
      setZoomLevel(currentZoom - 0.1);
    });
  }

  var zoomSlider = document.getElementById('zoomSlider');
  if (zoomSlider) {
    zoomSlider.addEventListener('input', function() {
      setZoomLevel(parseInt(this.value, 10) / 100, false);
      var lbl = document.getElementById('lblZoomPct');
      if (lbl) lbl.innerText = this.value + '%';
    });
    zoomSlider.addEventListener('change', function() {
      setZoomLevel(parseInt(this.value, 10) / 100, true);
    });
  }

  var cboZoomPreset = document.getElementById('cboZoomPreset');
  if (cboZoomPreset) {
    cboZoomPreset.addEventListener('change', function() {
      var val = this.value;
      if (val === 'fit-width') {
        fitToWidth();
      } else if (val === 'fit-page') {
        fitToPage();
      } else {
        var pct = parseInt(val, 10);
        if (!isNaN(pct)) {
          setZoomLevel(pct / 100);
        }
      }
    });
  }

  var btnFitWidth = document.getElementById('btnFitWidth');
  if (btnFitWidth) {
    btnFitWidth.addEventListener('click', fitToWidth);
  }

  var btnFitPage = document.getElementById('btnFitPage');
  if (btnFitPage) {
    btnFitPage.addEventListener('click', fitToPage);
  }

  var btnZoom100 = document.getElementById('btnZoom100');
  if (btnZoom100) {
    btnZoom100.addEventListener('click', function() {
      setZoomLevel(1.0);
    });
  }

  var lblZoomPct = document.getElementById('lblZoomPct');
  if (lblZoomPct) {
    lblZoomPct.style.cursor = 'pointer';
    lblZoomPct.title = 'Click to reset to 100%';
    lblZoomPct.addEventListener('click', function() {
      setZoomLevel(1.0);
    });
  }

  // Ctrl + Mouse wheel zoom on canvas viewport
  var canvasViewport = document.getElementById('canvasViewport');
  if (canvasViewport) {
    canvasViewport.addEventListener('wheel', function(e) {
      if (e.ctrlKey) {
        e.preventDefault();
        var delta = e.deltaY < 0 ? 0.05 : -0.05;
        setZoomLevel(currentZoom + delta);
      }
    }, { passive: false });
  }

  // Auto-refit on window resize
  var resizeTimeout;
  window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(function() {
      fitToWidth();
    }, 150);
  });

  // Border Box Switch (Box border around element)
  var propBorderBox = document.getElementById('propBorderBox');
  if (propBorderBox) {
    propBorderBox.addEventListener('change', function() {
      var obj = canvas.getActiveObject();
      if (obj) {
        var isChecked = this.checked;
        obj.set('stroke', isChecked ? '#000000' : null);
        obj.set('strokeWidth', isChecked ? 1.5 : 0);
        obj.border = isChecked ? 1 : 0;
        canvas.renderAll();
        saveCanvasState();
      }
    });
  }

  // Undo / Redo
  document.getElementById('btnUndo').addEventListener('click', undo);
  document.getElementById('btnRedo').addEventListener('click', redo);

  // Toggle Real Data Preview
  document.getElementById('btnToggleRealData').addEventListener('click', toggleRealDataPreview);

  // Save Template Button
  document.getElementById('btnSaveTemplate').addEventListener('click', saveTemplate);

  // Background Dropdown
  document.getElementById('cboBgType').addEventListener('change', function() {
    backgroundType = this.value;
    document.getElementById('bgColorPickerRow').classList.toggle('d-none', backgroundType !== 'color');
    document.getElementById('bgImageRow').classList.toggle('d-none', backgroundType !== 'image');
    if (backgroundType === 'none') {
      backgroundValue = '';
      applyCanvasBackground();
    }
  });
  document.getElementById('propBgColor').addEventListener('input', function() {
    backgroundValue = this.value;
    document.getElementById('propBgColorHex').value = this.value;
    applyCanvasBackground();
  });
  document.getElementById('propBgColorHex').addEventListener('input', function() {
    backgroundValue = this.value;
    document.getElementById('propBgColor').value = this.value;
    applyCanvasBackground();
  });

  // Custom Label Modal Opener
  document.getElementById('btnOpenCustomLabelModal').addEventListener('click', function() {
    var m = new bootstrap.Modal(document.getElementById('modalCustomLabel'));
    m.show();
  });

  // Save Custom Label
  document.getElementById('btnSaveCustomLabel').addEventListener('click', function() {
    var name = document.getElementById('txtNewLabelName').value.trim();
    if (!name) { alert('Label name is required'); return; }
    var type = document.querySelector('input[name="radLabelType"]:checked').value;
    var defText = document.getElementById('txtNewDefaultText').value.trim() || name;

    addCustomElement(type, name, defText);
    bootstrap.Modal.getInstance(document.getElementById('modalCustomLabel')).hide();
    document.getElementById('txtNewLabelName').value = '';
    document.getElementById('txtNewDefaultText').value = '';
  });
}

function duplicateSelectedObject() {
  var obj = canvas.getActiveObject();
  if (!obj) return;

  obj.clone(function(cloned) {
    cloned.set({
      left: obj.left + 15,
      top: obj.top + 15,
      element_type: obj.element_type,
      field_name: obj.field_name,
      label_name: obj.label_name,
      static_text: obj.static_text
    });
    canvas.add(cloned);
    canvas.setActiveObject(cloned);
    canvas.renderAll();
    saveCanvasState();
  });
}

function deleteSelectedObject() {
  var obj = canvas.getActiveObject();
  if (obj) {
    canvas.remove(obj);
    canvas.discardActiveObject();
    canvas.renderAll();
    saveCanvasState();
  }
}

function handleKeydown(e) {
  // If active in an input/textarea, let it type normally
  if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) return;

  if (e.key === 'Delete' || e.key === 'Backspace') {
    deleteSelectedObject();
  } else if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
    e.preventDefault();
    duplicateSelectedObject();
  } else if ((e.ctrlKey || e.metaKey) && e.key === 'z') {
    e.preventDefault();
    undo();
  } else if ((e.ctrlKey || e.metaKey) && e.key === 'y') {
    e.preventDefault();
    redo();
  }
}

// Undo / Redo
function saveCanvasState() {
  if (!isRecordingState) return;
  var json = canvas.toJSON(['element_type', 'field_name', 'label_name', 'static_text', 'custom_id']);
  undoStack.push(JSON.stringify(json));
  if (undoStack.length > 30) undoStack.shift();
  redoStack = [];
}

function undo() {
  if (undoStack.length <= 1) return;
  isRecordingState = false;
  redoStack.push(undoStack.pop());
  var state = undoStack[undoStack.length - 1];
  canvas.loadFromJSON(JSON.parse(state), function() {
    canvas.renderAll();
    isRecordingState = true;
  });
}

function redo() {
  if (!redoStack.length) return;
  isRecordingState = false;
  var state = redoStack.pop();
  undoStack.push(state);
  canvas.loadFromJSON(JSON.parse(state), function() {
    canvas.renderAll();
    isRecordingState = true;
  });
}

// Real Data Preview Toggle
function toggleRealDataPreview() {
  isRealDataPreview = !isRealDataPreview;
  var btn = document.getElementById('btnToggleRealData');
  var lbl = document.getElementById('lblPreviewMode');

  if (isRealDataPreview) {
    btn.classList.replace('btn-outline-info', 'btn-info');
    lbl.innerText = 'Template Mode';

    // Replace placeholders with real values on canvas
    canvas.getObjects().forEach(function(obj) {
      if (obj.type === 'textbox' || obj.type === 'text') {
        var tmpl = obj.static_text || obj.text;
        var rendered = tmpl;
        for (var k in sampleItemData) {
          rendered = rendered.split('{{' + k + '}}').join(sampleItemData[k]);
        }
        obj.set('text', rendered);
      }
    });
  } else {
    btn.classList.replace('btn-info', 'btn-outline-info');
    lbl.innerText = 'Real Data';

    // Restore template tags
    canvas.getObjects().forEach(function(obj) {
      if (obj.type === 'textbox' || obj.type === 'text') {
        obj.set('text', obj.static_text || obj.text);
      }
    });
  }
  canvas.renderAll();
}

// Handle Asset Upload (Logo / Image)
function handleAssetUpload(input) {
  if (!input.files || !input.files[0]) return;
  var formData = new FormData();
  formData.append('asset_file', input.files[0]);
  formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

  fetch('<?php echo base_url("cover/upload_asset"); ?>', {
    method: 'POST',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
    },
    body: formData
  })
  .then(function(r) { return r.json(); })
  .then(function(res) {
    if (res.status && res.url) {
      fabric.Image.fromURL(res.url, function(img) {
        var maxW = 60 * MM_TO_PX;
        if (img.width > maxW) {
          img.scaleToWidth(maxW);
        }
        img.set({
          left: (canvas.getWidth() - (img.width * img.scaleX)) / 2,
          top: 30,
          element_type: 'image',
          static_text: res.file_path,
          label_name: 'Image Logo'
        });
        canvas.add(img);
        canvas.setActiveObject(img);
        canvas.renderAll();
        saveCanvasState();
      });
    } else {
      alert(res.message || 'Upload failed');
    }
  })
  .catch(function(err) {
    alert('Upload error: ' + err.message);
  });
}

// Handle Background Image Upload
function handleBgUpload(input) {
  if (!input.files || !input.files[0]) return;
  var formData = new FormData();
  formData.append('asset_file', input.files[0]);
  formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

  fetch('<?php echo base_url("cover/upload_asset"); ?>', {
    method: 'POST',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
    },
    body: formData
  })
  .then(function(r) { return r.json(); })
  .then(function(res) {
    if (res.status && res.file_path) {
      backgroundValue = res.file_path;
      document.getElementById('lblBgImgName').innerText = res.file_path;
      applyCanvasBackground();
    } else {
      alert(res.message || 'Background upload failed');
    }
  });
}

// Save Template to Database
function saveTemplate() {
  var tmplName = document.getElementById('txtTemplateName').value.trim();
  if (!tmplName) {
    alert('Please enter a Template Name');
    document.getElementById('txtTemplateName').focus();
    return;
  }

  // Restore template tags if currently in Real Data preview
  if (isRealDataPreview) {
    toggleRealDataPreview();
  }

  var objects = canvas.getObjects();
  var elements = [];

  objects.forEach(function(obj, idx) {
    var xMm = obj.left / MM_TO_PX;
    var yMm = obj.top / MM_TO_PX;
    var wMm = (obj.width * (obj.scaleX || 1)) / MM_TO_PX;
    var hMm = (obj.height * (obj.scaleY || 1)) / MM_TO_PX;
    var fontPt = Math.round((obj.fontSize || 18) / 1.333);

    var hasBorder = (obj.border === 1 || obj.border === '1' || (obj.strokeWidth && obj.strokeWidth > 0)) ? 1 : 0;

    elements.push({
      element_type:    obj.element_type || (obj.type === 'image' ? 'image' : 'dynamic_field'),
      field_name:      obj.field_name || null,
      label_name:      obj.label_name || 'Label',
      static_text:     obj.static_text || obj.text,
      x:               parseFloat(xMm.toFixed(2)),
      y:               parseFloat(yMm.toFixed(2)),
      width:           parseFloat(wMm.toFixed(2)),
      height:          parseFloat(hMm.toFixed(2)),
      font_family:     obj.fontFamily || 'TAU-Paalai',
      font_size:       fontPt,
      font_weight:     obj.fontWeight === 'bold' ? 'bold' : 'normal',
      font_style:      obj.fontStyle === 'italic' ? 'italic' : 'normal',
      text_decoration: obj.underline ? 'underline' : 'none',
      font_color:      obj.fill || '#000000',
      text_align:      obj.textAlign || 'center',
      rotation:        parseFloat((obj.angle || 0).toFixed(2)),
      z_index:         idx + 1,
      border:          hasBorder,
      properties_json: JSON.stringify({ border: hasBorder })
    });
  });

  var isDefaultVal = document.getElementById('chkIsDefault') ? (document.getElementById('chkIsDefault').checked ? 'Y' : 'N') : '<?= $isDefault ? "Y" : "N" ?>';

  var payload = {
    id: templateId,
    template_name: tmplName,
    paper_size: paperSize,
    width_mm: paperWidthMm,
    height_mm: paperHeightMm,
    orientation: paperOrientation,
    background_type: backgroundType,
    background_value: backgroundValue,
    is_default: isDefaultVal,
    elements: elements
  };
  payload['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';

  var saveBtn = document.getElementById('btnSaveTemplate');
  saveBtn.disabled = true;
  saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

  fetch('<?php echo base_url("cover/save_template"); ?>', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
    },
    body: JSON.stringify(payload)
  })
  .then(function(r) { return r.json(); })
  .then(function(res) {
    saveBtn.disabled = false;
    saveBtn.innerHTML = '<i class="bi bi-floppy-fill me-1"></i> Save';

    if (res.status) {
      templateId = res.template_id;
      if (window.history && window.history.replaceState) {
        window.history.replaceState(null, '', '<?= base_url("cover/designer/"); ?>' + res.template_id);
      }
      Swal.fire({
        icon: 'success',
        title: 'Saved!',
        text: res.message,
        confirmButtonColor: '#0d6efd',
        timer: 2000
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: res.message || 'Failed to save template',
        confirmButtonColor: '#dc3545'
      });
    }
  })
  .catch(function(err) {
    saveBtn.disabled = false;
    saveBtn.innerHTML = '<i class="bi bi-floppy-fill me-1"></i> Save';
    alert('Request failed: ' + err.message);
  });
}
</script>
