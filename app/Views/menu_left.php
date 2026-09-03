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
      <a class="nav-link <?php echo $is_dashboard ? 'active' : 'collapsed'; ?>" href="<?php echo base_url(); ?>welcome">
        <i class="bi bi-grid-1x2-fill"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <!-- Settings -->
    <?php if ($this->session->get('USER_GROUP') == 'A') { ?> 
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#settings-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-gear-fill"></i><span>Settings</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="settings-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <?php if ($this->session->get('USER_GROUP') == 'A' && $this->session->get('USER_TYPE') == 3 && !is_import_data_finish($this->session->get('SUB_DISTRICT'))) { ?>
        <li>
          <a href="<?php echo base_url(); ?>import/import_data">
            <i class="bi bi-circle-fill"></i><span>Import CSV Data</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->session->get('USER_GROUP') == 'A' && $this->session->get('USER_TYPE') == 2) { ?>
        <li>
          <a href="<?php echo base_url(); ?>import/import_data">
            <i class="bi bi-circle-fill"></i><span>Import Sub-District CSV Data</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->session->get('USER_GROUP') == 'A' || $this->session->get('USER_GROUP') == 'W') { ?>
        <li>
          <a href="<?php echo base_url(); ?>admin/kalolsavam">
            <i class="bi bi-circle-fill"></i><span>Define Kalolsavam</span>
          </a>
        </li>
        <?php if ($this->session->get('USER_GROUP') == 'W') { ?>
        <li>
          <a href="<?php echo base_url(); ?>import/backup_data">
            <i class="bi bi-circle-fill"></i><span>Backup Database</span>
          </a>
        </li>
        <?php } ?>
        <li>
          <a href="<?php echo base_url(); ?>import/backup_data_sql">
            <i class="bi bi-circle-fill"></i><span>Backup Database as SQL</span>
          </a>
        </li>
        <?php if ($this->session->get('USER_GROUP') == 'W') { ?>
        <li>
          <a href="<?php echo base_url(); ?>import/restore_database">
            <i class="bi bi-circle-fill"></i><span>Restore Database</span>
          </a>
        </li>
        <?php } ?>
        <?php } ?>

        <li>
          <a href="<?php echo base_url(); ?>item/item_master">
            <i class="bi bi-circle-fill"></i><span>Manage Items Code</span>
          </a>
        </li>

        <li>
          <a href="<?php echo base_url(); ?>login/change_password">
            <i class="bi bi-circle-fill"></i><span>Change Password</span>
          </a>
        </li>
      </ul>
    </li>
    <?php } ?>

    <!-- Item Management -->
    <?php if ($this->session->get('USER_TYPE') == 0 || $this->session->get('USER_TYPE') == 2) { ?>
    <li class="nav-item">
      <a class="nav-link <?php echo (strpos($current_uri, 'item/item_master') !== false) ? 'active' : 'collapsed'; ?>" href="<?php echo base_url(); ?>item/item_master">
        <i class="bi bi-list-check"></i>
        <span>Item Management</span>
      </a>
    </li>
    <?php } ?>

    <!-- School Specific -->
    <?php if ($this->session->get('USER_GROUP') == 'U' && $this->session->get('USER_TYPE') == 5) { ?>
    <li class="nav-item">
      <a class="nav-link collapsed" href="<?php echo base_url('downloads/serve/chittur_itemcodes.html'); ?>" target="_blank">
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
    <?php if (
            $this->Session_Model->check_user_permission(8) ||
            $this->session->get('USER_TYPE') == 0 ||
            $this->session->get('USER_TYPE') == 1 ||
            $this->session->get('USER_TYPE') == 2 ||
            $this->session->get('USER_GROUP') == 'A') { ?>
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
        <li>
          <a href="<?php echo base_url(); ?>import/restore_database">
            <i class="bi bi-circle-fill"></i><span>Restore Database</span>
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
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(24) && $this->session->get('USER_GROUP') != 'W') { ?>
        <li>
          <a href="<?php echo base_url(); ?>user/user_cluster">
            <i class="bi bi-circle-fill"></i><span>Create Cluster</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>welcome/cluster_details">
            <i class="bi bi-circle-fill"></i><span>Cluster School List</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22)) { ?>                
        <li>
          <a href="<?php echo base_url(); ?>schools/school_entry">
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
      </ul>
    </li>
    <?php } ?>

    <!-- Stage -->
    <?php if ($this->Session_Model->check_user_permission(8)) { ?>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#stage-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-easel2-fill"></i><span>Stage</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="stage-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="<?php echo base_url(); ?>report/prereport/stageallot_duration">
            <i class="bi bi-circle-fill"></i><span>Duration of Events(Festival)</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>stage/stage_details">
            <i class="bi bi-circle-fill"></i><span>Define Stage</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>stage/item_participant/participant_nodetails/">
            <i class="bi bi-circle-fill"></i><span>Stage Allotment (Festivalwise)</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>stage/allotment/">
            <i class="bi bi-circle-fill"></i><span>Stage Allotment (Item Wise)</span>
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
      </ul>
    </li>
    <?php } ?>

    <!-- Stage Reports -->
    <?php if ($this->General_Model->is_all_stage_alloted()) { 
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
      <a class="nav-link collapsed" href="<?php echo base_url(); ?>report/prereport/callsheet_first" title="Stage Reports">
        <i class="bi bi-card-checklist"></i>
        <span>Stage Reports</span>
      </a>
    </li>
    <?php } ?>
    <?php } ?>

    <!-- Pre Fest Report -->
    <?php if ($this->General_Model->is_all_stage_alloted()) { 
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
        <?php if ($this->Session_Model->check_user_permission(42)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>report/prereportpdf/eligible_school" target="_blank">
            <i class="bi bi-circle-fill"></i><span>Eligible School</span>
          </a>
        </li>
        <?php } ?>
        
        <?php if ($this->Session_Model->check_user_permission(27)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>report/prereport/list_school">
            <i class="bi bi-circle-fill"></i><span>Participating Schools (F.W)</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>report/prereport/list_school_with_team" target="_blank">
            <i class="bi bi-circle-fill"></i><span>School Contacts</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(29)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>report/prereport/itemwise_report_interface">
            <i class="bi bi-circle-fill"></i><span>List of Participants (I.W)</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(28)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>report/prereport/datewisepart">
            <i class="bi bi-circle-fill"></i><span>No. of Participants - Date Wise</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(30)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>report/prereport/list_participants">
            <i class="bi bi-circle-fill"></i><span>Lst. Participants For Team Managers</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(32)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>report/prereport/feedetails">
            <i class="bi bi-circle-fill"></i><span>Fee Details of School (Registration)</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(33)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>report/prereport/participant_cardindex">
            <i class="bi bi-circle-fill"></i><span>Participant Card (without Photo)</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>report/prereport/participant_cardindex_photo">
            <i class="bi bi-circle-fill"></i><span>Participant Card (with Photo)</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>report/prereport/participant_cardindex2">
            <i class="bi bi-circle-fill"></i><span>Participant Card - Middle</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(40)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>report/prereport/participant_limit_item_more">
            <i class="bi bi-circle-fill"></i><span>Participants More than One Item</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(37)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>report/prereport/clashes_details_interface">
            <i class="bi bi-circle-fill"></i><span>Clash Report</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(38)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>report/prereport/clustor_report">
            <i class="bi bi-circle-fill"></i><span>Cluster Report</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(39)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>report/prereport/stagereportdate">
            <i class="bi bi-circle-fill"></i><span>Stage Report</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>report/prereport/stagereport_all">
            <i class="bi bi-circle-fill"></i><span>Stage Report (Festival)</span>
          </a>
        </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(43)) { ?>
        <li>
          <a href="<?php echo base_url(); ?>report/prereport/appealed_part">
            <i class="bi bi-circle-fill"></i><span>List of Participants by Appeal</span>
          </a>
        </li>
        <?php } ?>
      </ul>
    </li>
    <?php } ?>
    <?php } ?>

    <!-- Result -->
    <?php if ($this->General_Model->is_all_stage_alloted()) { 
      if ($this->Session_Model->check_user_permission(12) ||
          $this->Session_Model->check_user_permission(45) ||
          $this->Session_Model->check_user_permission(8)) { ?>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#result-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-trophy-fill"></i><span>Result</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="result-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
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
      </ul>
    </li>
    <?php } ?>
    <?php } ?>

    <!-- Result Report -->
    <?php if ($this->General_Model->is_all_stage_alloted()) {
      if ($this->Session_Model->check_user_permission(44) || $this->Session_Model->check_user_permission(46)) { ?>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#report-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-pie-chart-fill"></i><span>Result Report</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="report-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
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
            <i class="bi bi-circle-fill"></i><span>School Wise Point Report</span>
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
            <i class="bi bi-circle-fill"></i><span>All Result</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>report/afterresultreportpdf/report_press" target="_blank">
            <i class="bi bi-circle-fill"></i><span>Report for Press</span>
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
      </ul>
    </li>
    <?php } ?>
    <?php } ?>

    <!-- Certificate -->
    <?php if ($this->Session_Model->check_user_permission(47) || $this->Session_Model->check_user_permission(48)) {
      if ($this->General_Model->is_all_stage_alloted()) { ?>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#certify-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-patch-check-fill"></i><span>Certificate</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="certify-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
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
      </ul>
    </li>
    <?php } ?>
    <?php } ?>

    <!-- Export -->
    <?php if ($this->Session_Model->check_user_permission(8)) {
      if ($this->General_Model->is_all_result_entered()) { ?>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#export-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-cloud-arrow-up-fill"></i><span>Export</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="export-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="<?php echo base_url(); ?>report/afterresultreport/higherlevel_result">
            <i class="bi bi-circle-fill"></i><span>Higher Level Competition</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>export/export_district_data">
            <i class="bi bi-circle-fill"></i><span>Export Result Data</span>
          </a>
        </li>
      </ul>
    </li>
    <?php } ?>
    <?php } ?>

    <!-- Downloads -->
    <?php if ($this->Session_Model->check_user_permission(8)) { ?>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#download-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-file-earmark-arrow-down-fill"></i><span>Downloads</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="download-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="<?php echo base_url('downloads/serve/chittur_itemcodes.html'); ?>" target="_blank">
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


                