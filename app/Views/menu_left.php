<?php
$is_first_time = (trim((string)$this->session->get('CHANGEPWD')) !== 'Y');
?>
<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

  <!-- Mobile Sidebar Header (Mobile Only) -->
  <div class="sidebar-mobile-header d-flex align-items-center justify-content-between d-xl-none">
    <div class="sidebar-mobile-brand logo d-flex align-items-center">
      <img src="<?php echo base_url(); ?>assets/img/logo.png" alt="Tamil Kalaivizha Logo" class="sidebar-mobile-logo">
      <span class="sidebar-mobile-title">Tamil Kalaivizha</span>
    </div>
    <button type="button" class="sidebar-close-btn" title="Close Menu" onclick="document.body.classList.remove('toggle-sidebar'); document.documentElement.classList.remove('toggle-sidebar');" aria-label="Close Menu">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  <ul class="sidebar-nav" id="sidebar-nav">
    <li class="nav-heading">Main Navigation</li>

<?php
$current_uri = trim(uri_string(), '/');
$is_dashboard = ($current_uri === '' || $current_uri === 'welcome' || strpos($current_uri, 'welcome/') === 0);
$is_settings_active = (
    strpos($current_uri, 'import/') === 0 ||
    strpos($current_uri, 'admin/kalolsavam') === 0 ||
    strpos($current_uri, 'admin/general_settings') === 0 ||
strpos($current_uri, 'item/item_master') === 0
);

?>
<?php if ($is_first_time) { ?>
    <li class="nav-item">
      <a class="nav-link <?php echo ($current_uri === 'login/change_password') ? 'active' : 'collapsed'; ?>" href="<?php echo base_url(); ?>login/change_password">
        <i class="bi bi-key-fill text-warning"></i>
        <span>Change Password (Required)</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed logout-item" href="<?php echo base_url(); ?>login/logout">
        <i class="bi bi-box-arrow-in-right"></i>
        <span>Logout</span>
      </a>
    </li>
<?php } else { ?>

    <li class="nav-item">
      <?php
      $dashboard_url = base_url('welcome');
      if ((int)$this->session->get('USER_TYPE') === 5) {
          $dashboard_url = base_url('welcome/school_dashboard');
      } elseif ((int)$this->session->get('USER_TYPE') === 6) {
          $dashboard_url = base_url('stage/manager');
      }
      ?>
      <a class="nav-link <?php echo $is_dashboard ? 'active' : 'collapsed'; ?>" href="<?php echo $dashboard_url; ?>">
        <i class="bi bi-grid-1x2-fill"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <?php if ($this->session->get('USER_TYPE') == 6) { ?>
    <li class="nav-item">
      <a class="nav-link <?php echo (strpos($current_uri, 'stage/manager') === 0) ? 'active' : 'collapsed'; ?>" href="<?php echo base_url('stage/manager'); ?>">
        <i class="bi bi-display"></i>
        <span>Stage Manager</span>
      </a>
    </li>
    <?php } ?>

    <!-- Settings -->
    <?php if ($this->session->get('USER_TYPE') != 6 && ($this->session->get('USER_GROUP') == 'A' || $this->session->get('USER_GROUP') == 'W' || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2, 3], true))) { ?>
    <li class="nav-item">
      <a class="nav-link <?php echo $is_settings_active ? '' : 'collapsed'; ?>" data-bs-target="#settings-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-gear-fill"></i><span>Settings</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="settings-nav" class="nav-content collapse <?php echo $is_settings_active ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
        <?php if ($this->session->get('USER_GROUP') == 'A' && $this->session->get('USER_TYPE') == 3 && !is_import_data_finish($this->session->get('SUB_DISTRICT'))) { ?>
        <li>
          <a href="<?php echo base_url(); ?>import/import_data" class="<?php echo ($current_uri === 'import/import_data') ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i><span>Import CSV Data</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->session->get('USER_TYPE') == 2) { ?>
        <li>
          <a href="<?php echo base_url(); ?>district/import" class="<?php echo ($current_uri === 'district/import') ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i><span>Import Sub-District CSV Data</span>
          </a>
        </li>
        <?php } elseif ($this->session->get('USER_GROUP') == 'W') { ?>
        <li>
          <a href="<?php echo base_url(); ?>import/import_data" class="<?php echo ($current_uri === 'import/import_data') ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i><span>Import Sub-District CSV Data</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->session->get('USER_GROUP') == 'A' || $this->session->get('USER_GROUP') == 'W' || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2, 3], true)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>admin/kalolsavam" class="<?php echo (strpos($current_uri, 'admin/kalolsavam') === 0) ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i><span>Define Kalolsavam</span>
          </a>
        </li>
				<?php if ($this->session->get('USER_GROUP') == 'W') { ?>
        <li>
          <a href="<?php echo base_url(); ?>admin/general_settings" class="<?php echo (strpos($current_uri, 'admin/general_settings') === 0) ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i><span>General Settings</span>
          </a>
        </li>
				<?php } ?>
				<?php if ($this->session->get('USER_GROUP') == 'W' || $this->session->get('USER_GROUP') == 'A' || (int)$this->session->get('USER_TYPE') == 2) { ?>
<?php } ?>
        <li>
          <a href="<?php echo base_url(); ?>import/backup_data_sql" class="<?php echo (strpos($current_uri, 'import/backup_data_sql') === 0 || strpos($current_uri, 'import/backup_data') === 0) ? 'active' : ''; ?>">

            <i class="bi bi-circle-fill"></i><span>Backup Database</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>import/restore_database" class="<?php echo (strpos($current_uri, 'import/restore_database') === 0) ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i><span>Restore Database</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>import/clear_database" class="<?php echo (strpos($current_uri, 'import/clear_database') === 0) ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i><span>Clear Database</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->session->get('USER_TYPE') == 0 || $this->session->get('USER_TYPE') == 2 || $this->session->get('USER_TYPE') == 3) { ?>
        <li>
          <a href="<?php echo base_url(); ?>item/item_master">
            <i class="bi bi-circle-fill"></i><span>Item Management</span>
          </a>
        </li>
        <?php } ?>

        <li>
          <a href="<?php echo base_url(); ?>login/change_password">
            <i class="bi bi-circle-fill"></i><span>Change Password</span>
          </a>
        </li>
      </ul>
    </li>
    <?php } ?>

    <!-- School Specific -->
    <?php if ($this->session->get('USER_GROUP') == 'U' && $this->session->get('USER_TYPE') == 5) { ?>
    <li class="nav-item">
      <a class="nav-link collapsed" href="<?php echo base_url('downloads/item_codes' . ($this->session->get('SUB_DISTRICT') ? '?sub_district=' . $this->session->get('SUB_DISTRICT') : '')); ?>">
        <i class="bi bi-upc-scan"></i>
        <span>Item Code</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" href="<?php echo base_url(); ?>schools/registration">
        <i class="bi bi-building"></i>
        <span>Registration</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" href="<?php echo base_url(); ?>schools/profile">
        <i class="bi bi-person-badge"></i>
        <span>School Profile</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" href="<?php echo base_url(); ?>login/change_password">
        <i class="bi bi-shield-lock"></i>
        <span>Change Password</span>
      </a>
    </li>
    <?php } ?>

    <!-- Registrations -->
    <?php if ($this->session->get('USER_TYPE') != 6 && (
            $this->Session_Model->check_user_permission(8) ||
            $this->session->get('USER_TYPE') == 0 ||
            $this->session->get('USER_TYPE') == 1 ||
            $this->session->get('USER_TYPE') == 2 ||
            $this->session->get('USER_GROUP') == 'A')) { ?>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#formReg-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-journal-bookmark-fill"></i><span>Registrations</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="formReg-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <?php if ($this->session->get('USER_GROUP') == 'W') { ?>           
        <li>
          <a href="<?php echo base_url(); ?>user/admin_users">
            <i class="bi bi-circle-fill"></i><span>Admin Users</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>welcome/district_details">
            <i class="bi bi-circle-fill"></i><span>District List</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->session->get('USER_GROUP') == 'A') { ?>                
        <li>
          <a href="<?php echo base_url(); ?>user/user_registration">
            <i class="bi bi-circle-fill"></i><span>User Registration</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->session->get('USER_TYPE') == '2') { ?>               
        <li>
          <a href="<?php echo base_url(); ?>welcome/sub_district_details">
            <i class="bi bi-circle-fill"></i><span>Sub District List</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>district/schools/registration">
            <i class="bi bi-circle-fill"></i><span>District Registration</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(24) && $this->session->get('USER_GROUP') != 'W') { ?>
        <li>
          <a href="<?php echo base_url(); ?>user/user_cluster">
            <i class="bi bi-circle-fill"></i><span>Create Cluster</span>
          </a>
        </li>
        <!-- <li>
          <a href="<?php echo base_url(); ?>welcome/cluster_details">
            <i class="bi bi-circle-fill"></i><span>Cluster School List</span>
          </a>
        </li> -->
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22)) { ?>                
        <li>
          <a href="<?php echo base_url((int)$this->session->get('USER_TYPE') === 2 ? 'district/schools/school_entry' : 'schools/school_entry'); ?>">
            <i class="bi bi-circle-fill"></i><span>School Entry</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(23) || $this->Session_Model->check_user_permission(26)) { ?>  
        <li>
          <a href="<?php echo base_url(); ?>schools/school_master">
            <i class="bi bi-circle-fill"></i><span>School Master</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(20)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>schools/special_order_entry">
            <i class="bi bi-circle-fill"></i><span>Special Order Entry</span>
          </a>
        </li>
        <?php } ?>

        <?php if ((int)$this->session->get('USER_TYPE') === 3 || (int)$this->session->get('USER_TYPE') === 2 || $this->session->get('USER_GROUP') == 'W') { ?>
        <li>
          <a href="<?php echo base_url(); ?>welcome/entry_confirm_status" class="<?php echo (strpos($current_uri, 'welcome/entry_confirm_status') === 0) ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i><span>Entry Confirm Status</span>
          </a>
        </li>
        <?php } ?>
      </ul>
    </li>
    <?php } ?>

    <!-- Stage -->
    <?php if ($this->session->get('USER_TYPE') != 6 && $this->Session_Model->check_user_permission(8)) { ?>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#stage-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-easel2-fill"></i><span>Stage</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="stage-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <?php if ($this->session->get('USER_TYPE') == 2) { ?>
        <li>
          <a href="<?php echo base_url(); ?>district/report/prereport/stageallot_duration">
            <i class="bi bi-circle-fill"></i><span>Duration of Events(F.W)</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>district/stage">
            <i class="bi bi-circle-fill"></i><span>Define Stage</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>district/scheduling">
            <i class="bi bi-circle-fill"></i><span>Festival Scheduling</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>district/scheduling/clashes">
            <i class="bi bi-circle-fill"></i><span>Clash Detection</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>stage/item_participant/participant_nodetails/">
            <i class="bi bi-circle-fill"></i><span>Stage Allotment (F.W)</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>stage/allotment/">
            <i class="bi bi-circle-fill"></i><span>Stage Allotment (I.W)</span>
          </a>
        </li>
        <?php } else { ?>
        <li>
          <a href="<?php echo base_url(); ?>report/prereport/stageallot_duration">
            <i class="bi bi-circle-fill"></i><span>Duration of Events(F.W)</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>stage/stage_details">
            <i class="bi bi-circle-fill"></i><span>Define Stage</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>stage/item_participant/participant_nodetails/">
            <i class="bi bi-circle-fill"></i><span>Stage Allotment (F.W)</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>stage/allotment/">
            <i class="bi bi-circle-fill"></i><span>Stage Allotment (I.W)</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>stage/festival_scheduling/">
            <i class="bi bi-circle-fill"></i><span>Auto Set Stage</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>stage/festival_scheduling/order_of_events/">
            <i class="bi bi-circle-fill"></i><span>Order of Events</span>
          </a>
        </li>
        <?php } ?>
      </ul>
    </li>
    <?php } ?>

    <!-- Stage Reports -->
    <?php if ($this->session->get('USER_TYPE') != 6 && ($this->session->get('USER_TYPE') == 2 || $this->General_Model->is_all_stage_alloted())) { 
      if ($this->Session_Model->check_user_permission(8) ||
          $this->Session_Model->check_user_permission(27) ||
          $this->Session_Model->check_user_permission(28) ||
          $this->Session_Model->check_user_permission(29) ||
          $this->Session_Model->check_user_permission(30) ||
          $this->Session_Model->check_user_permission(31) ||
          $this->Session_Model->check_user_permission(32) ||
          $this->Session_Model->check_user_permission(33) ||
          $this->Session_Model->check_user_permission(34) ||
          $this->Session_Model->check_user_permission(35) ||
          $this->Session_Model->check_user_permission(36) ||
          $this->Session_Model->check_user_permission(37) ||
          $this->Session_Model->check_user_permission(38) ||
          $this->Session_Model->check_user_permission(39) ||
          $this->Session_Model->check_user_permission(40) ||
          $this->Session_Model->check_user_permission(41) ||
          $this->Session_Model->check_user_permission(42) ||
          $this->Session_Model->check_user_permission(43)) { ?>
    <li class="nav-item">
      <?php $is_stage_reports = (strpos($current_uri, 'callsheet') !== false || strpos($current_uri, 'timesheet') !== false || strpos($current_uri, 'score_sheet') !== false || strpos($current_uri, 'tabulation') !== false || strpos($current_uri, 'four_reports') !== false || strpos($current_uri, 'cover') === 0); ?>
      <a class="nav-link <?php echo $is_stage_reports ? '' : 'collapsed'; ?>" data-bs-target="#stageReports-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-card-checklist"></i><span>Stage Reports</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="stageReports-nav" class="nav-content collapse <?php echo $is_stage_reports ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="<?php echo base_url() . (($this->session->get('USER_TYPE') == 2) ? 'district/report/prereport/callsheet_first' : 'report/prereport/callsheet_first'); ?>">
            <i class="bi bi-circle-fill"></i><span>Call Sheet</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url() . (($this->session->get('USER_TYPE') == 2) ? 'district/report/prereport/timesheetinterface' : 'report/prereport/timesheetinterface'); ?>">
            <i class="bi bi-circle-fill"></i><span>Time Sheet</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url() . (($this->session->get('USER_TYPE') == 2) ? 'district/report/prereport/tabulation_report_interface' : 'report/prereport/tabulation_report_interface'); ?>">
            <i class="bi bi-circle-fill"></i><span>Tabulation</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url() . (($this->session->get('USER_TYPE') == 2) ? 'district/report/prereport/score_sheet_interfaces' : 'report/prereport/score_sheet_interfaces'); ?>">
            <i class="bi bi-circle-fill"></i><span>Score Sheet</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url() . (($this->session->get('USER_TYPE') == 2) ? 'district/report/prereport/four_reports' : 'report/prereport/four_reports'); ?>">
            <i class="bi bi-circle-fill"></i><span>All In One</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>cover/templates">
            <i class="bi bi-circle-fill"></i><span>Cover Print</span>
          </a>
        </li>
      </ul>
    </li>
    <?php } ?>
    <?php } ?>

    <!-- Pre Fest Report -->
    <?php if ($this->session->get('USER_TYPE') != 6 && ($this->session->get('USER_TYPE') == 2 || $this->General_Model->is_all_stage_alloted())) { 
      if ($this->Session_Model->check_user_permission(8) ||
          $this->Session_Model->check_user_permission(27) ||
          $this->Session_Model->check_user_permission(28) ||
          $this->Session_Model->check_user_permission(29) ||
          $this->Session_Model->check_user_permission(30) ||
          $this->Session_Model->check_user_permission(31) ||
          $this->Session_Model->check_user_permission(32) ||
          $this->Session_Model->check_user_permission(33) ||
          $this->Session_Model->check_user_permission(34) ||
          $this->Session_Model->check_user_permission(35) ||
          $this->Session_Model->check_user_permission(36) ||
          $this->Session_Model->check_user_permission(37) ||
          $this->Session_Model->check_user_permission(38) ||
          $this->Session_Model->check_user_permission(39) ||
          $this->Session_Model->check_user_permission(40) ||
          $this->Session_Model->check_user_permission(41) ||
          $this->Session_Model->check_user_permission(42) ||
          $this->Session_Model->check_user_permission(43)) { ?>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#pre_fest-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-file-earmark-bar-graph-fill"></i><span>Pre Fest Report</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="pre_fest-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <?php $pre_pfx = ($this->session->get('USER_TYPE') == 2) ? 'district/report/prereport' : 'report/prereport'; ?>
        <?php $pre_pfx_pdf = ($this->session->get('USER_TYPE') == 2) ? 'district/report/prereportpdf' : 'report/prereportpdf'; ?>
        <?php $pre_card = ($this->session->get('USER_TYPE') == 2) ? 'district/reports' : 'report/prereport'; ?>
        <?php if ($this->Session_Model->check_user_permission(42)) { ?>
        <li>
          <a href="<?php echo base_url() . $pre_pfx_pdf . '/eligible_school'; ?>" target="_blank">
            <i class="bi bi-circle-fill"></i><span>Eligible School</span><i class="bi bi-file-earmark-pdf menu-pdf-icon"></i>
          </a>
        </li>
        <?php } ?>
        
        <?php if ($this->Session_Model->check_user_permission(27)) { ?>
        <li>
          <a href="<?php echo base_url() . $pre_pfx . '/list_school'; ?>">
            <i class="bi bi-circle-fill"></i><span>Participating Schools (F.W)</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url() . $pre_pfx_pdf . '/list_school_with_team'; ?>" target="_blank">
            <i class="bi bi-circle-fill"></i><span>School Contacts</span><i class="bi bi-file-earmark-pdf menu-pdf-icon"></i>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(29)) { ?>
        <li>
          <a href="<?php echo base_url() . $pre_pfx . '/itemwise_report_interface'; ?>">
            <i class="bi bi-circle-fill"></i><span>List of Participants (I.W)</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(28)) { ?>
        <li>
          <a href="<?php echo base_url() . $pre_pfx . '/datewisepart'; ?>">
            <i class="bi bi-circle-fill"></i><span>No. of Participants - Date Wise</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(30)) { ?>
        <li>
          <a href="<?php echo base_url() . $pre_pfx . '/list_participants'; ?>">
            <i class="bi bi-circle-fill"></i><span>Participants & Team Managers</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(32)) { ?>
        <li>
          <a href="<?php echo base_url() . $pre_pfx . '/feedetails'; ?>">
            <i class="bi bi-circle-fill"></i><span>Fee Details</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(33)) { ?>
        <li>
          <a href="<?php echo base_url() . $pre_card . '/participant_cardindex'; ?>">
            <i class="bi bi-circle-fill"></i><span>Participant IdCard (W.O.P)</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url() . $pre_card . '/participant_cardindex_photo'; ?>">
            <i class="bi bi-circle-fill"></i><span>Participant IdCard (W.P)</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url() . $pre_pfx . '/participant_cardindex2'; ?>">
            <i class="bi bi-circle-fill"></i><span>Participant Card - Middle</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(40)) { ?>
        <li>
          <a href="<?php echo base_url() . $pre_pfx . '/participant_limit_item_more'; ?>">
            <i class="bi bi-circle-fill"></i><span>Participants More than One Item</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(37)) { ?>
        <li>
          <a href="<?php echo base_url() . $pre_pfx . '/clashes_details_interface'; ?>">
            <i class="bi bi-circle-fill"></i><span>Clash Report</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(38)) { ?>
        <li>
          <a href="<?php echo base_url() . $pre_pfx . '/clustor_report'; ?>">
            <i class="bi bi-circle-fill"></i><span>Cluster Report</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(39)) { ?>
        <li>
          <a href="<?php echo base_url() . $pre_pfx . '/stagereportdate'; ?>">
            <i class="bi bi-circle-fill"></i><span>Stage Report</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url() . $pre_pfx . '/stagereport_all'; ?>">
            <i class="bi bi-circle-fill"></i><span>Stage Report (Festival)</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(43)) { ?>
        <li>
          <a href="<?php echo base_url() . $pre_pfx . '/appealed_part'; ?>">
            <i class="bi bi-circle-fill"></i><span>List of Participants by Appeal</span>
          </a>
        </li>
        <?php } ?>
      </ul>
    </li>
    <?php } ?>
    <?php } ?>

    <!-- Result -->
    <?php if ($this->session->get('USER_TYPE') != 6 && ($this->session->get('USER_TYPE') == 2 || $this->General_Model->is_all_stage_alloted())) { 
      if ($this->session->get('USER_TYPE') == 2 ||
          $this->Session_Model->check_user_permission(12) ||
          $this->Session_Model->check_user_permission(45) ||
          $this->Session_Model->check_user_permission(8)) { ?>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#result-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-trophy-fill"></i><span>Result</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="result-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <?php if ($this->session->get('USER_TYPE') == 2) { ?>
        <li>
          <a href="<?php echo base_url(); ?>district/resultentry/">
            <i class="bi bi-circle-fill"></i><span>Result Entry</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>district/resultentry/item_result_list/">
            <i class="bi bi-circle-fill"></i><span>Item Result List</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>district/report/resultindex/resultview">
            <i class="bi bi-circle-fill"></i><span>Publish Result</span>
          </a>
        </li>
        <?php } else { ?>
        <?php if ($this->Session_Model->check_user_permission(12)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>result/resultentry/">
            <i class="bi bi-circle-fill"></i><span>Result Entry</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>result/resultentry/item_result_list/">
            <i class="bi bi-circle-fill"></i><span>Item Result List</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(45)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>publishresult/resultindex/resultview/">
            <i class="bi bi-circle-fill"></i><span>Publish Result</span>
          </a>
        </li>
        <?php } ?>
        <?php } ?>
      </ul>
    </li>
    <?php } ?>
    <?php } ?>

    <!-- Result Report -->
    <?php if ($this->session->get('USER_TYPE') != 6 && ($this->session->get('USER_TYPE') == 2 || $this->General_Model->is_all_stage_alloted())) {
      if ($this->session->get('USER_TYPE') == 2 || $this->Session_Model->check_user_permission(44) || $this->Session_Model->check_user_permission(46)) { ?>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#report-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-pie-chart-fill"></i><span>Result Report</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="report-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <?php if ($this->session->get('USER_TYPE') == 2) { ?>
        <li>
          <a href="<?php echo base_url(); ?>district/reports">
            <i class="bi bi-circle-fill"></i><span>Champion Scoreboard</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>district/report/timeof_fest_report/timefest_result_confidential">
            <i class="bi bi-circle-fill"></i><span>Confidential Reports</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>district/report/afterresult_report/school_wise_result_interface">
            <i class="bi bi-circle-fill"></i><span>Item Wise Point</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>district/report/afterresult_report/item_code_reports">
            <i class="bi bi-circle-fill"></i><span>Item Code Wise Result</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>district/report/afterresult_reportpdf/school_wise_result_all" target="_blank">
            <i class="bi bi-circle-fill"></i><span>School Wise Point Report</span><i class="bi bi-file-earmark-pdf menu-pdf-icon"></i>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>district/report/resultindex/gradewise_interface">
            <i class="bi bi-circle-fill"></i><span>Grade Wise (School)</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>district/report/resultindex/rankwise_result">
            <i class="bi bi-circle-fill"></i><span>Rank Wise</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>district/report/resultindex/gradewise_result">
            <i class="bi bi-circle-fill"></i><span>Grade Wise</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>district/report/afterresult_reportpdf/total_points" target="_blank">
            <i class="bi bi-circle-fill"></i><span>All Result</span><i class="bi bi-file-earmark-pdf menu-pdf-icon"></i>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>district/report/afterresult_reportpdf/report_press" target="_blank">
            <i class="bi bi-circle-fill"></i><span>Report for Press</span><i class="bi bi-file-earmark-pdf menu-pdf-icon"></i>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>district/report/resultindex/report_press_timewise">
            <i class="bi bi-circle-fill"></i><span>Time wise Report</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>district/report/afterresult_report/status_of_kalolsavam_interface">
            <i class="bi bi-circle-fill"></i><span>Status of Kalolsavam</span>
          </a>
        </li>
        <?php } else { ?>
        <?php if ($this->Session_Model->check_user_permission(46)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>report/timefestreport/timefest_result_confidential/">
            <i class="bi bi-circle-fill"></i><span>Confidential Result Report</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(44)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>report/afterresultreport/school_wise_result_interface">
            <i class="bi bi-circle-fill"></i><span>Item Wise Point</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>report/afterresultreport/item_code_reports">
            <i class="bi bi-circle-fill"></i><span>Item Code Wise Result</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>report/afterresultreportpdf/school_wise_result_all" target="_blank">
            <i class="bi bi-circle-fill"></i><span>School Wise Point Report</span><i class="bi bi-file-earmark-pdf menu-pdf-icon"></i>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>report/resultindex/gradewise_interface">
            <i class="bi bi-circle-fill"></i><span>Grade Wise (School)</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>report/resultindex/rankwise_result">
            <i class="bi bi-circle-fill"></i><span>Rank Wise</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>report/resultindex/gradewise_result">
            <i class="bi bi-circle-fill"></i><span>Grade Wise</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>report/afterresultreportpdf/total_points" target="_blank">
            <i class="bi bi-circle-fill"></i><span>All Result</span><i class="bi bi-file-earmark-pdf menu-pdf-icon"></i>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>report/afterresultreportpdf/report_press" target="_blank">
            <i class="bi bi-circle-fill"></i><span>Report for Press</span><i class="bi bi-file-earmark-pdf menu-pdf-icon"></i>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>report/resultindex/report_press_timewise">
            <i class="bi bi-circle-fill"></i><span>Time wise Report</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>report/afterresultreport/status_of_kalolsavam_interface">
            <i class="bi bi-circle-fill"></i><span>Status of Kalolsavam</span>
          </a>
        </li>
        <?php } ?>
        <?php } ?>          
      </ul>
    </li>
    <?php } ?>
    <?php } ?>

    <!-- Certificate -->
    <?php if ($this->session->get('USER_TYPE') != 6 && ($this->session->get('USER_TYPE') == 2 || $this->Session_Model->check_user_permission(47) || $this->Session_Model->check_user_permission(48))) {
      if ($this->session->get('USER_TYPE') == 2 || $this->General_Model->is_all_stage_alloted()) { ?>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#certify-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-patch-check-fill"></i><span>Certificate</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="certify-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <?php if ($this->session->get('USER_TYPE') == 2) { ?>
        <li>
          <a id="menu-cert-template" href="<?php echo base_url(); ?>district/certificate">
            <i class="bi bi-circle-fill"></i><span>District Certificate Template</span>
          </a>
        </li>
        <li>
          <a id="menu-cert-item" href="<?php echo base_url(); ?>district/certificate/list_item_wise">
            <i class="bi bi-circle-fill"></i><span>Certificate - Item Wise</span>
          </a>
        </li>
        <li>
          <a id="menu-cert-school" href="<?php echo base_url(); ?>district/certificate/list_school_wise">
            <i class="bi bi-circle-fill"></i><span>Certificate - School Wise</span>
          </a>
        </li>
        <li>
          <a id="menu-cert-reg" href="<?php echo base_url(); ?>district/certificate/list_reg_no_wise">
            <i class="bi bi-circle-fill"></i><span>Certificate - Reg No. Wise</span>
          </a>
        </li>
        <?php } else { ?>
        <?php if ($this->Session_Model->check_user_permission(47)) { ?>
        <li>
          <a id="menu-cert-template" href="<?php echo base_url(); ?>certificate/certificate/">
            <i class="bi bi-circle-fill"></i><span>Certificate - Template</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(48) && $this->General_Model->is_certificate_template_set()) { ?>
        <li>
          <a id="menu-cert-item" href="<?php echo base_url(); ?>certificate/certificate/list_item_wise">
            <i class="bi bi-circle-fill"></i><span>Certificate - Item Wise</span>
          </a>
        </li>
        <li>
          <a id="menu-cert-school" href="<?php echo base_url(); ?>certificate/certificate/list_school_wise">
            <i class="bi bi-circle-fill"></i><span>Certificate - School Wise</span>
          </a>
        </li>
        <li>
          <a id="menu-cert-reg" href="<?php echo base_url(); ?>certificate/certificate/list_reg_no_wise">
            <i class="bi bi-circle-fill"></i><span>Certificate - Reg No. Wise</span>
          </a>
        </li>
        <?php } ?>
        <?php } ?>
      </ul>
    </li>
    <?php } ?>
    <?php } ?>

    <!-- Export (district-aware: shown only when all festival results are entered and confirmed) -->
    <?php 
    $export_user_type = (int)$this->session->get('USER_TYPE');
    if (($export_user_type === 3 || $export_user_type === 2 || $export_user_type === 1 || $this->session->get('USER_GROUP') === 'W') && $this->Session_Model->check_user_permission(8)) {
      if ($this->General_Model->is_all_result_entered()) { ?>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#export-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-cloud-arrow-up-fill"></i><span>Export</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="export-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="<?php echo base_url() . (($export_user_type === 2) ? 'district/report/afterresult_report/higherlevel_result' : 'report/afterresultreport/higherlevel_result'); ?>">
            <i class="bi bi-circle-fill"></i><span>Higher Level Competition</span>
          </a>
        </li>
        <?php if ($export_user_type !== 2) { ?>
        <li>
          <a href="<?php echo base_url(); ?>export/export_district_data">
            <i class="bi bi-circle-fill"></i><span>Export Result Data</span>
          </a>
        </li>
        <?php } ?>
      </ul>
    </li>
    <?php } ?>
    <?php } ?>

    <!-- Downloads -->
    <?php if ($this->session->get('USER_TYPE') != 6 && $this->Session_Model->check_user_permission(8)) { ?>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#download-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-file-earmark-arrow-down-fill"></i><span>Downloads</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="download-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="<?php echo base_url('downloads/item_codes' . ($this->session->get('SUB_DISTRICT') ? '?sub_district=' . $this->session->get('SUB_DISTRICT') : '')); ?>">
            <i class="bi bi-circle-fill"></i><span>Item Code</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url('downloads/serve/kalolsavam.pdf'); ?>" target="_blank">
            <i class="bi bi-circle-fill"></i><span>Manual</span>
          </a>
        </li>
      </ul>
    </li>
    <?php } ?>



    <!-- Logout -->
    <li class="nav-item mt-3 pt-2 border-top">
      <a class="nav-link collapsed logout-item" href="<?php echo base_url(); ?>login/logout">
        <i class="bi bi-box-arrow-right text-danger"></i>
        <span class="text-danger fw-semibold">Logout</span>
      </a>
    </li>

<?php } ?>

  </ul>

</aside><!-- End Sidebar -->


                