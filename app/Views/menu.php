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

    // Resolve school user code if not set in session
    if (empty($school_code) && (int)$session->get('USER_TYPE') === 5) {
        $cluster = $db->table('sd_user_cluster')->select('school_code')->where('user_id', (int)$session->get('USERID'))->get()->getRowArray();
        if (!empty($cluster['school_code'])) {
            $school_code = $cluster['school_code'];
        }
    }

    if (!empty($school_code)) {
        $sch = $db->table('sd_school_master')->select('school_name, sub_district_code, rev_district_code')->where('school_code', $school_code)->get()->getRowArray();
        if (!$sch) {
            $sch = $db->table('d_school_master')->select('school_name, sub_district_code, rev_district_code')->where('school_code', $school_code)->get()->getRowArray();
        }
        if ($sch) {
            if (empty($sub_dist_code)) $sub_dist_code = $sch['sub_district_code'];
            if (empty($dist_code)) $dist_code = $sch['rev_district_code'];
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
        $dist_m = $db->table('rev_district_master')->select('rev_district_name')->where('rev_district_code', $dist_code)->where('status', 'Y')->get()->getRowArray();
        if ($dist_m) {
            $display_district = $dist_m['rev_district_name'];
        }
    }

    // Prefer the name saved by the user in their profile (user_master.name) from the
    // database, so header reflects profile edits without requiring a fresh login.
    $user_id = (int)$session->get('USERID');
    if ($user_id > 0) {
        $um = $db->table('user_master')->select('name, user_name')->where('user_id', $user_id)->get()->getRowArray();
        if ($um && !empty($um['name'])) {
            $display_user_name = $um['name'];
        } else {
            $display_user_name = (!empty($um['user_name'])) ? $um['user_name'] : $user_name;
        }
    } else {
        $display_user_name = $user_name;
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

$user_group = $session->get('USER_GROUP');
$utype = (int)$session->get('USER_TYPE');
$role_badge_title = '';
$header_display_title = '';

if ($user_group === 'W' || $utype === 0) {
    $header_display_title = 'Webmaster';
    $role_badge_title = 'Webmaster';
} elseif ($utype === 1) {
    $header_display_title = 'State Admin';
    $role_badge_title = 'State Admin';
} elseif ($utype === 2) {
    $clean_dist = trim(preg_replace('/district/i', '', (string)$display_district));
    $header_display_title = !empty($clean_dist) && $clean_dist !== 'District' ? ($clean_dist . ' District') : 'District Admin';
    $role_badge_title = 'District Admin';
} elseif ($utype === 3) {
    $clean_sub = trim(preg_replace('/sub[- ]?district/i', '', (string)$display_sub_district));
    $header_display_title = !empty($clean_sub) && $clean_sub !== 'Sub-District' ? ($clean_sub . ' Sub District') : 'Sub District Admin';
    $role_badge_title = 'Sub District Admin';
} elseif ($utype === 6) {
    $header_display_title = 'Stage Manager';
    $role_badge_title = 'Stage Manager';
} elseif ($utype === 5) {
    if (!empty($display_school_name)) {
        $header_display_title = $display_school_name . (!empty($school_code) ? ' (' . $school_code . ')' : '');
    } else {
        $header_display_title = !empty($school_code) ? ('School (' . $school_code . ')') : 'School User';
    }
    $role_badge_title = 'School User';
} elseif ($utype === 4) {
    $header_display_title = 'Cluster Admin';
    $role_badge_title = 'Cluster Admin';
} else {
    $header_display_title = $display_user_name ?: 'User';
    $role_badge_title = 'User';
}

$profile_display_name = $header_display_title;
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
      
      <?php if ($utype === 2 && $display_district && $display_district !== 'District') { ?>
      <li class="nav-item d-none d-sm-block me-3">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill font-monospace">
          <i class="bi bi-geo-alt-fill me-1"></i><?php echo esc(trim(preg_replace('/district/i', '', $display_district)) . ' District'); ?>
        </span>
      </li>
      <?php } elseif ($utype === 3 && $display_sub_district && $display_sub_district !== 'Sub-District') { ?>
      <li class="nav-item d-none d-sm-block me-3">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill font-monospace">
          <i class="bi bi-geo-alt-fill me-1"></i><?php echo esc(trim(preg_replace('/sub[- ]?district/i', '', $display_sub_district)) . ' Sub District'); ?>
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
            <h6 class="fw-bold mb-0 text-dark"><?php echo esc($profile_display_name); ?></h6>
            <small class="text-muted"><?php echo esc($role_badge_title); ?></small>
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




