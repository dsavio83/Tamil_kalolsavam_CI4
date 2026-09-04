<?php
$session = session();
$sub_dist_code = $session->get('SUB_DISTRICT');
$dist_code = $session->get('DISTRICT');
$school_code = $session->get('SCHOOL_CODE');
$user_name = $session->get('USER_NAME') ?: $session->get('USER');

$display_user_name = '';
$display_district = '';
$display_sub_district = '';
$display_school_name = '';

try {
    $db = \Config\Database::connect();
    if ($school_code && empty($sub_dist_code)) {
        $sch = $db->table('sd_school_master')->select('school_name, sub_district_code, rev_district_code')->where('school_code', $school_code)->get()->getRowArray();
        if ($sch) {
            $sub_dist_code = $sch['sub_district_code'];
            $dist_code = $sch['rev_district_code'];
            $display_school_name = $sch['school_name'];
        }
    }

    if ($sub_dist_code) {
        $sub_m = $db->table('sub_district_master')->select('sub_district_name')->where('sub_district_code', $sub_dist_code)->get()->getRowArray();
        if ($sub_m) {
            $display_sub_district = $sub_m['sub_district_name'];
        }
    }

    if ($dist_code) {
        $dist_m = $db->table('rev_district_master')->select('rev_district_name')->where('rev_district_code', $dist_code)->get()->getRowArray();
        if ($dist_m) {
            $display_district = $dist_m['rev_district_name'];
        }
    }

    if ($user_name !== '' && $user_name !== null) {
        $display_user_name = $user_name;
    } else {
        $user_id = (int)$session->get('USERID');
        if ($user_id > 0) {
            $um = $db->table('user_master')->select('name, user_name')->where('user_id', $user_id)->get()->getRowArray();
            if ($um) {
                $display_user_name = (!empty($um['name'])) ? $um['name'] : $um['user_name'];
            }
        }
    }
    if (empty($display_user_name)) {
        $display_user_name = 'User';
    }
} catch (\Throwable $e) {
}

if (empty($display_district)) {
    $display_district = 'District';
}
if (empty($display_sub_district)) {
    $display_sub_district = 'Sub-District';
}
$header_title = 'Tamil Kalaivizha';
$profile_display_name = $display_school_name ? ($display_school_name . ' (' . $school_code . ')') : $display_user_name;
?>
<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

  <div class="header-brand-container d-flex align-items-center">
    <a href="<?php echo base_url(); ?>welcome" title="Tamil Kalaivizha" class="logo d-flex align-items-center text-decoration-none">
      <img src="<?php echo base_url(); ?>assets/img/logo.png" alt="Tamil Kalaivizha Logo" class="header-logo-img">
      <span class="header-brand-title">Tamil Kalaivizha</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn" title="Toggle Navigation Menu"></i>
  </div><!-- End Logo -->

  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center m-0 p-0 list-unstyled">
      
      <?php if ($display_sub_district && $display_sub_district !== 'Sub-District') { ?>
      <li class="nav-item d-none d-sm-block me-3">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill font-monospace">
          <i class="bi bi-geo-alt-fill me-1"></i><?php echo esc($display_sub_district); ?>
        </span>
      </li>
      <?php } ?>

      <!-- User Profile Dropdown -->
      <li class="nav-item dropdown pe-3">
        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <img src="<?php echo base_url(); ?>assets/img/profile-img.png" alt="Profile" class="rounded-circle shadow-sm" width="36" height="36">
          <span class="d-none d-md-block dropdown-toggle ps-2 fw-semibold text-dark"><?php echo esc($profile_display_name); ?></span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(-16px, 38px);">
          <li class="dropdown-header text-start">
            <h6 class="fw-bold mb-0 text-dark"><?php echo esc($display_user_name); ?></h6>
            <small class="text-muted"><?php echo esc($display_district); ?><?php echo ($display_sub_district && $display_sub_district !== 'Sub-District') ? ' - ' . esc($display_sub_district) : ''; ?></small>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>

          <li>
            <?php
            $user_type = (int)session()->get('USER_TYPE');
            $profile_url = ($user_type == 5) ? base_url('schools/profile') : base_url('welcome');
            ?>
            <a class="dropdown-item d-flex align-items-center" href="<?php echo $profile_url; ?>">
              <i class="bi bi-person text-primary"></i>
              <span>My Profile</span>
            </a>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>

          <li>
            <a class="dropdown-item d-flex align-items-center" href="<?php echo base_url('login/change_password'); ?>">
              <i class="bi bi-key text-warning"></i>
              <span>Change Password</span>
            </a>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>

          <li>
            <a class="dropdown-item d-flex align-items-center text-danger" href="<?php echo base_url(); ?>login/logout">
              <i class="bi bi-box-arrow-right text-danger"></i>
              <span class="fw-semibold">Logout</span>
            </a>
          </li>

        </ul><!-- End Profile Dropdown Items -->
      </li><!-- End Profile Nav -->

    </ul>
  </nav><!-- End Icons Navigation -->

</header><!-- End Header -->




