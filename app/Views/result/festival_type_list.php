
<?php
$selected_fest = (isset($this->input) && is_object($this->input)) ? $this->input->post('cmbFestType') : (request()->getPost('cmbFestType') ?? '');
?>
<style>
  @font-face {
    font-family: 'TAU-Pallai';
    src: local('TAU-Pallai');
  }
  .font-tamil {
    font-family: 'TAU-Pallai', system-ui, -apple-system, sans-serif;
    font-size: 14px;
  }
  .glass-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid rgba(226, 232, 240, 0.9);
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
    transition: all 0.2s ease;
  }
  
  /* Hero Icon Styling & Animations */
  .fest-hero-icon {
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.16);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1.5px solid rgba(255, 255, 255, 0.35);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    animation: festPulseGlow 3s infinite, festIconFloat 3.8s ease-in-out infinite;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .fest-hero-icon:hover {
    background: rgba(255, 255, 255, 0.28);
    transform: scale(1.1) rotate(6deg) !important;
    border-color: rgba(255, 255, 255, 0.65);
    box-shadow: 0 0 20px rgba(255, 255, 255, 0.5) !important;
  }
  
  @keyframes festPulseGlow {
    0% {
      box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.45);
    }
    70% {
      box-shadow: 0 0 0 12px rgba(255, 255, 255, 0);
    }
    100% {
      box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
    }
  }
  
  @keyframes festIconFloat {
    0%, 100% {
      transform: translateY(0);
    }
    50% {
      transform: translateY(-3px);
    }
  }

  /* Pill Box for dropdown */
  .fest-pill-box {
    background: #ffffff;
    border-radius: 50rem;
    padding: 3px 8px 3px 14px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    border: 2px solid rgba(255, 255, 255, 0.4);
    transition: all 0.25s ease;
  }
  .fest-pill-box:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.18);
    border-color: rgba(255, 255, 255, 0.8);
  }
  .fest-pill-box:focus-within {
    border-color: #fde047;
    box-shadow: 0 0 0 4px rgba(253, 224, 71, 0.35), 0 6px 20px rgba(0, 0, 0, 0.15);
  }

  /* Trophy icon inside pill */
  .fest-trophy-icon {
    transition: transform 0.3s ease;
    filter: drop-shadow(0 1px 2px rgba(217, 119, 6, 0.4));
  }
  .fest-pill-box:hover .fest-trophy-icon {
    transform: scale(1.15) rotate(-8deg);
  }

  /* Load Status Button */
  .btn-fest-load {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #1e293b !important;
    letter-spacing: 0.2px;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .btn-fest-load .fest-spin-icon {
    display: inline-block;
    transition: transform 0.55s cubic-bezier(0.34, 1.56, 0.64, 1);
  }
  .btn-fest-load:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.45) !important;
    background: linear-gradient(135deg, #fcd34d 0%, #fbbf24 50%, #f59e0b 100%);
  }
  .btn-fest-load:hover .fest-spin-icon {
    transform: rotate(180deg) scale(1.2);
  }
  .btn-fest-load:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3) !important;
  }
</style>

<!-- Page Header & Breadcrumbs -->
<div class="pagetitle mb-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
        <i class="bi bi-list-task text-primary"></i> Item Result List
      </h1>
      <nav>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
          <li class="breadcrumb-item">Results</li>
          <li class="breadcrumb-item active">Item Result List</li>
        </ol>
      </nav>
    </div>
    <?php if (!empty($selected_fest) && is_array($fest) && !empty($fest[$selected_fest])) { ?>
      <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-1.5 font-tamil">
        <i class="bi bi-check-circle-fill"></i> Selected: <?php echo htmlspecialchars((string)$fest[$selected_fest]); ?>
      </span>
    <?php } ?>
  </div>
</div>

<!-- Full Width, Single-Row Select Festival Category Card -->
<div class="glass-card mb-4 overflow-hidden border-0 shadow-sm">
  <div class="px-4 py-3" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
    <div class="row align-items-center g-3 text-white">
      
      <!-- Left Side: Icon & Title -->
      <div class="col-lg-5 col-xl-5">
        <div class="d-flex align-items-center gap-3">
          <div class="fest-hero-icon" title="Festival Category Status">
            <i class="bi bi-trophy-fill text-warning fs-4"></i>
          </div>
          <div>
            <h5 class="fw-bold mb-1 text-white d-flex align-items-center gap-2">
              <span>Select Festival Category</span>
            </h5>
            <p class="mb-0 text-white-50 small">View event-wise completion and confirmation status</p>
          </div>
        </div>
      </div>

      <!-- Right Side: Single-Row Form (Dropdown + Action Button) -->
      <div class="col-lg-7 col-xl-7">
        <?php echo form_open('result/resultentry/item_result_list', array('id' => 'formIRL', 'class' => 'm-0')); ?>
        <input type="hidden" name="hidUservalue" id="hidUservalue" value="SHOW" />

        <div class="d-flex align-items-center justify-content-lg-end gap-2 flex-wrap flex-sm-nowrap">
          <!-- White Pill Container with Leading Trophy Icon and Select -->
          <div class="fest-pill-box d-flex align-items-center flex-grow-1" style="max-width: 440px;">
            <span class="ps-2 pe-1 text-warning d-flex align-items-center">
              <i class="bi bi-trophy-fill fs-5 fest-trophy-icon"></i>
            </span>
            <?php
              $fest_options = (is_array($fest) && !isset($fest[''])) ? (['' => '-- Choose Festival Category --'] + $fest) : ($fest ?? []);
              echo form_dropdown(
                "cmbFestType",
                $fest_options,
                $selected_fest,
                'class="form-select border-0 bg-transparent fw-bold text-dark py-2 shadow-none pe-4 font-tamil" id="cmbFestType" style="cursor: pointer;" required'
              );
            ?>
          </div>

          <!-- Amber / Gold Pill Action Button with Rotating Icon on Hover -->
          <button type="submit" class="btn btn-warning rounded-pill py-2.5 px-4 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 text-dark btn-fest-load flex-shrink-0">
            <i class="bi bi-arrow-repeat fest-spin-icon fs-5"></i>
            <span>Load Status</span>
          </button>
        </div>

        <?php echo form_close(); ?>
      </div>

    </div>
  </div>
</div>