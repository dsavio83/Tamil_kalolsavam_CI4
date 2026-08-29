<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

<ul class="sidebar-nav" id="sidebar-nav">

  <li class="nav-item">
    <a class="nav-link " href="<?php echo base_url(); ?>welcome" title="Home">
      <i class="bi bi-grid"></i>
      <span>Dashboard</span>
    </a>
  </li>



<!-- End Dashboard Nav -->
<?php if (
          $this->session->get('USER_GROUP') == 'A') { ?> 
  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#settings-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-gear"></i><span>Settings</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
      <ul id="settings-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <?php if (
          $this->session->get('USER_GROUP') == 'A' && 
          $this->session->get('USER_TYPE') == 3 && !is_import_data_finish(
          $this->session->get('SUB_DISTRICT'))) { 
            ?>
      <li>
        <a href="<?php echo base_url(); ?>import/import_data">
          <i class="bi bi-circle"></i><span>Import CSV Data</span>
        </a>
      </li>
          <?php }  ?>

          <?php if (
            $this->session->get('USER_GROUP') == 'A' && 
            $this->session->get('USER_TYPE') == 2) { 
          ?>
      <li>
        <a href="<?php echo base_url(); ?>import/import_data">
          <i class="bi bi-circle"></i><span>Import Sub-District CSV Data</span>
        </a>
      </li>
          <?php } ?>

          <?php if (
            $this->session->get('USER_GROUP') == 'A' or 
            $this->session->get('USER_GROUP') == 'W') { 
          ?>

      <li>
        <a href="<?php echo base_url(); ?>admin/kalolsavam">
          <i class="bi bi-circle"></i><span>Define Kalolsavam</span>
        </a>
      </li>
       <?php if (
           $this->session->get('USER_GROUP') == 'W') {  ?>
      <li>
        <a href="<?php echo base_url(); ?>import/backup_data">
          <i class="bi bi-circle"></i><span>Backup Database</span>
        </a>
      </li>
      <?php }  ?>
      <li>
        <a href="<?php echo base_url(); ?>import/backup_data_sql">
          <i class="bi bi-circle"></i><span>Backup Database as SQL</span>
        </a>
      </li>
    <?php if (
           $this->session->get('USER_GROUP') == 'W') {  ?>
      <li>
        <a href="<?php //echo base_url();?>import/initialize_data">
          <i class="bi bi-circle"></i><span>Initialize database</span>
        </a>
      </li>
      <?php }  ?>
    <?php }  ?>

    <?php if (
            $this->session->get('USER_GROUP') == 'W') {  ?>
      <li>
        <a href="<?php echo base_url();  ?>import/restore_database">
          <i class="bi bi-circle"></i><span>Restore Database</span>
        </a>
      </li>
    <?php }  ?>
      <li>
        <a href="<?php echo base_url(); ?>login/change_password">
          <i class="bi bi-circle"></i><span>Change Password</span>
        </a>
      </li>
    </ul>
    <?php }  ?>
  </li>

  <!-- Item Management for itadmin, sub-district and district admins -->
  <?php if ($this->session->get('USER_TYPE') == 0 ||
            $this->session->get('USER_TYPE') == 2) { ?>
  <li class="nav-item">
    <a class="nav-link" href="<?php echo base_url(); ?>Item_Management">
      <i class="bi bi-list-ul"></i>
      <span>Item Management</span>
    </a>
  </li>
  <?php }  ?>

<!-- End Settingss Nav -->


<?php if (
  $this->session->get('USER_GROUP') == 'U' && 
  $this->session->get('USER_TYPE') == 5) { 
?>
    
   <li>
    <a class="nav-link collapsed" href="<?php echo base_url(); ?>doc/chittur_itemcodes.html" target="_blank">
      <i class="bi bi-ui-radios"></i>
      <span>Item Code</span>
    </a>
  </li>
  
  <li>
    <a class="nav-link collapsed" href="<?php echo base_url(); ?>schools/registration">
      <i class="bi bi-building"></i>
      <span>Registration</span>
    </a>
  </li>

  <li>
  <a class="nav-link collapsed" href="<?php echo base_url(); ?>publishresult/resultindex/resultview/">
      <i class="bi bi-stack"></i>
      <span>View Results</span>
    </a>
  </li>

  <li>
    <a class="nav-link collapsed" href="<?php echo base_url(); ?>login/change_password">
      <i class="bi bi-key"></i>
      <span>Change Password</span>
    </a>
  </li>

<?php }  ?>
  

  <?php if (
          $this->Session_Model->check_user_permission(8) ||
          $this->session->get('USER_TYPE') == 0 ||
          $this->session->get('USER_TYPE') == 1 ||
          $this->session->get('USER_TYPE') == 2 ||
          $this->session->get('USER_GROUP') == 'A') { ?>

  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#formReg-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-journal-text"></i><span>Registrations</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="formReg-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <?php if (
          $this->session->get('USER_GROUP') == 'W') { 
        ?>           
      <li>
        <a href="<?php echo base_url(); ?>user/admin_users">
          <i class="bi bi-circle"></i><span>Admin Users</span>
        </a>
      </li>
      <li>
        <a href="<?php echo base_url(); ?>welcome/district_details">
          <i class="bi bi-circle"></i><span>District List</span>
        </a>
      </li>
      <li>
        <a href="<?php echo base_url(); ?>import/restore_database">
          <i class="bi bi-circle"></i><span>Restore Database</span>
        </a>
      </li>
        <?php }  ?>

        <?php if (
          $this->session->get('USER_GROUP') == 'A') { ?>                
      <li>
        <a href="<?php echo base_url(); ?>user/user_registration">
          <i class="bi bi-circle"></i><span>User Registration</span>
        </a>
      </li>

      
        <?php }  ?>

        <?php if (
          $this->session->get('USER_TYPE') == '2') { ?>               
      <li>
        <a href="<?php echo base_url(); ?>welcome/sub_district_details">
          <i class="bi bi-circle"></i><span>Sub District List</span>
        </a>
      </li>
        <?php }  ?>

        <?php if (
          $this->Session_Model->check_user_permission(24) && 
          $this->session->get('USER_GROUP') != 'W') { ?>
          
      <li>
        <a href="<?php echo base_url(); ?>user/user_cluster">
          <i class="bi bi-circle"></i><span> Create Cluster</span>
        </a>
      </li>

      <li>
        <a href="<?php echo base_url(); ?>welcome/cluster_details">
          <i class="bi bi-circle"></i><span> Cluster School List</span>
        </a>
      </li>
      <?php }  ?>

      <?php if (
        $this->Session_Model->check_user_permission(17)|| 
        $this->Session_Model->check_user_permission(2) || 
        $this->Session_Model->check_user_permission(22)) { ?>                
      <li>
        <a href="<?php echo base_url(); ?>schools/registration">
          <i class="bi bi-circle"></i><span> School Entry</span>
        </a>
      </li>
      <?php }  ?>

      <?php if (
        $this->Session_Model->check_user_permission(23) || 
        $this->Session_Model->check_user_permission(26)) { ?>  
      <li>
        <a href="<?php echo base_url(); ?>schools/school_master">
          <i class="bi bi-circle"></i><span> School Master</span>
        </a>
      </li>
      <?php }   ?>

      <?php if ($this->Session_Model->check_user_permission(20)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>schools/special_order_entry">
          <i class="bi bi-circle"></i><span> Special Order Entry</span>
        </a>
      </li>
      <?php }  ?>
    </ul>

  </li>
      <?php }  ?>

  <!-- End Registration Nav -->

  
 <?php if (
          $this->Session_Model->check_user_permission(8)) { ?>
  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#stage-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-receipt-cutoff"></i><span>Stage</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
       
    <ul id="stage-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          
      <li>
        <a href="<?php echo base_url(); ?>report/prereport/stageallot_duration">
          <i class="bi bi-circle"></i><span>Duration of Events(Festival)</span>
        </a>
      </li>
      
       <li>
        <a href="<?php echo base_url(); ?>stage/stage_details">
          <i class="bi bi-circle"></i><span>Define Stage</span>
        </a>
      </li>
        
      <li>
        <a href="<?php echo base_url(); ?>stage/item_participant/participant_nodetails/">
          <i class="bi bi-circle"></i><span> Stage allotment (Festivalwise)</span>
        </a>
      </li>
      <li>
        <a href="<?php echo base_url(); ?>stage/allotment/">
          <i class="bi bi-circle"></i><span> Stage Allotment (Item Wise)</span>
        </a>
      </li>
      <li>
        <a href="<?php echo base_url(); ?>stage/festival_scheduling/">
          <i class="bi bi-circle"></i><span> Auto Set Stage</span>
        </a>
      </li>
      <li>
        <a href="<?php echo base_url(); ?>stage/festival_scheduling/order_of_events/">
          <i class="bi bi-circle"></i><span> Order of Events</span>
        </a>
      </li>
    </ul>
        
  </li>
  <?php }  ?>
  <!-- End Stage Nav -->
  


  <?php 
    if ($this->General_Model->is_all_stage_alloted()) { 

    if (
            $this->Session_Model->check_user_permission(8) or
            $this->Session_Model->check_user_permission(27) or
            $this->Session_Model->check_user_permission(28) or
            $this->Session_Model->check_user_permission(29) or
            $this->Session_Model->check_user_permission(30) or
            $this->Session_Model->check_user_permission(31) or
            $this->Session_Model->check_user_permission(32) or
            $this->Session_Model->check_user_permission(33) or
            $this->Session_Model->check_user_permission(34) or
            $this->Session_Model->check_user_permission(35) or
            $this->Session_Model->check_user_permission(36) or
            $this->Session_Model->check_user_permission(37) or
            $this->Session_Model->check_user_permission(38) or
            $this->Session_Model->check_user_permission(39) or
            $this->Session_Model->check_user_permission(40) or
            $this->Session_Model->check_user_permission(41) or
            $this->Session_Model->check_user_permission(42) or
            $this->Session_Model->check_user_permission(43)) {
  ?>
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo base_url(); ?>report/prereport/callsheet_first" title="Call sheet">
          <i class="bi bi-receipt-cutoff"></i>
          <span>Stage Reports</span>
        </a>
      </li>
  <?php } ?>
  <?php } ?>
<!-- End Stage Report Nav -->
  <?php 
if ($this->General_Model->is_all_stage_alloted()) { 

  if ($this->Session_Model->check_user_permission(8) or
            $this->Session_Model->check_user_permission(27) or
            $this->Session_Model->check_user_permission(28) or
            $this->Session_Model->check_user_permission(29) or
            $this->Session_Model->check_user_permission(30) or
            $this->Session_Model->check_user_permission(31) or
            $this->Session_Model->check_user_permission(32) or
            $this->Session_Model->check_user_permission(33) or
            $this->Session_Model->check_user_permission(34) or
            $this->Session_Model->check_user_permission(35) or
            $this->Session_Model->check_user_permission(36) or
            $this->Session_Model->check_user_permission(37) or
            $this->Session_Model->check_user_permission(38) or
            $this->Session_Model->check_user_permission(39) or
            $this->Session_Model->check_user_permission(40) or
            $this->Session_Model->check_user_permission(41) or
            $this->Session_Model->check_user_permission(42) or
            $this->Session_Model->check_user_permission(43)) {
  ?>
  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#pre_fest-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-gem"></i><span>Pre Fest Report</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <?php } ?>
    <ul id="pre_fest-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <?php if ($this->Session_Model->check_user_permission(42)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>report/prereportpdf/eligible_school" target="_blank" title="Eligible Schools">
          <i class="bi bi-circle"></i><span>Eligible School</span>
        </a>
      </li>
      <?php } ?>
      
    <?php if ($this->Session_Model->check_user_permission(27)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>report/prereport/list_school" title="List of participating schools with school code">
          <i class="bi bi-circle"></i><span> Participating Schools (F.W)</span>
        </a>
      </li>

      <li>
        <a href="<?php echo base_url(); ?>report/prereport/list_school_with_team" title="School Contacts">
          <i class="bi bi-circle"></i><span> School Contacts</span>
        </a>
      </li>
    <?php } ?>

    <?php if ($this->Session_Model->check_user_permission(29)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>report/prereport/itemwise_report_interface" title="Participants list item wise/All">
          <i class="bi bi-circle"></i><span> List of Participants (I.W)</span>
        </a>
      </li>
      
      <?php } ?>

      <?php if ($this->Session_Model->check_user_permission(28)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>report/prereport/datewisepart" title="Date wise participants for press and food">
          <i class="bi bi-circle"></i><span>No. of Participants - Date Wise </span>
        </a>
      </li>
      <?php } ?>

      <?php if ($this->Session_Model->check_user_permission(30)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>report/prereport/list_participants" title="Participant details school wise">
          <i class="bi bi-circle"></i><span>Lst. Participants For Team Managers - Schools </span>
        </a>
      </li>
      <?php } ?>

      <?php if ($this->Session_Model->check_user_permission(32)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>report/prereport/feedetails" title="Fee details for registration commitee">
          <i class="bi bi-circle"></i><span>Fee Details of School (Registration) </span>
        </a>
      </li>
      <?php } ?>

      <?php if ($this->Session_Model->check_user_permission(33)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>report/prereport/participant_cardindex" title="participant card ">
          <i class="bi bi-circle"></i><span> Participant Card (without_Photo)</span>
        </a>
      </li>

      <li>
        <a href="<?php echo base_url(); ?>report/prereport/participant_cardindex_photo" title="participant card ">
          <i class="bi bi-circle"></i><span> Participant Card (with_Photo)</span>
        </a>
      </li>

      <li>
        <a href="<?php echo base_url(); ?>report/prereport/participant_cardindex2" title="participant card ">
          <i class="bi bi-circle"></i><span> Participant Card - Middle</span>
        </a>
      </li>
      <?php } ?>

      <?php if ($this->Session_Model->check_user_permission(40)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>report/prereport/participant_limit_item_more" title="participant more than one item">
          <i class="bi bi-circle"></i><span> Participants More than One Item</span>
        </a>
      </li>
      <?php } ?>

      <?php if ($this->Session_Model->check_user_permission(37)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>report/prereport/clashes_details_interface" title="clashes ">
          <i class="bi bi-circle"></i><span>Clash Report </span>
        </a>
      </li>
      <?php } ?>

      <?php if ($this->Session_Model->check_user_permission(38)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>report/prereport/clustor_report" title="cluster report ">
          <i class="bi bi-circle"></i><span>Cluster Report </span>
        </a>
      </li>
      <?php } ?>

      <?php if ($this->Session_Model->check_user_permission(39)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>report/prereport/stagereportdate" title="stage report">
          <i class="bi bi-circle"></i><span> Stage Report</span>
        </a>
      </li>

      <li>
      <a href="<?php echo base_url(); ?>report/prereport/stagereport_all" title="stage report">
         <i class="bi bi-circle"></i><span> Stage Report(Festival)</span>
      </a>
     </li>
      <?php } ?>


      <?php if ($this->Session_Model->check_user_permission(43)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>report/prereport/appealed_part"  title="List of Participants by Appeal">
          <i class="bi bi-circle"></i><span>List of Participants by Appeal </span>
        </a>
      </li>
      <?php } ?>

    </ul>
  </li>
    <?php } ?>

  <!-- End Pre Fest Nav -->

  <?php 
      if ($this->General_Model->is_all_stage_alloted()) { 
          
      if (
        $this->Session_Model->check_user_permission(12) or
        $this->Session_Model->check_user_permission(45) or
        $this->Session_Model->check_user_permission(8)) { ?>

  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#result-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-journal-text"></i><span>Result</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
     <?php } ?>
    <ul id="result-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <?php if ($this->Session_Model->check_user_permission(12)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>result/resultentry/" title="Result Entry">
          <i class="bi bi-circle"></i><span>Result Entry</span>
        </a>
      </li>
      <li>
        <a href="<?php echo base_url(); ?>result/resultentry/item_result_list/">
          <i class="bi bi-circle"></i><span>Item Result List</span>
        </a>
      </li>
      <?php } ?>

      <?php if ($this->Session_Model->check_user_permission(45)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>publishresult/resultindex/resultview/" title="Publish Result">
          <i class="bi bi-circle"></i><span>Publish Result</span>
        </a>
      </li>
      <?php } ?>
    </ul>
  </li>
  <?php } ?>

<!-- End Result Nav -->

<?php if(
  $this->General_Model->is_all_stage_alloted()){
  if (
    $this->Session_Model->check_user_permission(44) or 
    $this->Session_Model->check_user_permission(46)) { ?>
  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#report-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-calendar2-range"></i><span>Result Report</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
      <?php } ?>
    <ul id="report-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <?php if ($this->Session_Model->check_user_permission(46)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>report/timefestreport/timefest_result_confidential/" title="Confidential Report">
          <i class="bi bi-circle"></i><span>Confidential Result Report </span>
        </a>
      </li>
        <?php } ?>

        <?php if ($this->Session_Model->check_user_permission(44)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>report/afterresultreport/school_wise_result_interface"   title="Item Wise Point">
          <i class="bi bi-circle"></i><span>Item Wise Point</span>
        </a>
      </li>

      <li>
        <a href="<?php echo base_url(); ?>report/afterresultreport/item_code_reports"   title="Item wise point ">
          <i class="bi bi-circle"></i><span>Item Code Wise Result</span>
        </a>
      </li>

      <li>
        <a href="<?php echo base_url(); ?>report/afterresultreportpdf/school_wise_result_all" target="_blank" title="school wise point">
          <i class="bi bi-circle"></i><span>School Wise Point Report </span>
        </a>
      </li>

      <li>
        <a href="<?php echo base_url();?>report/resultindex/gradewise_interface"  title="Grade wise Result">
          <i class="bi bi-circle"></i><span>Grade Wise (School)</span>
        </a>
      </li>

      <li>
        <a href="<?php echo base_url(); ?>report/resultindex/rankwise_result" title="Rank wise Result">
          <i class="bi bi-circle"></i><span>Rank Wise </span>
        </a>
      </li>

      <li>
        <a href="<?php echo base_url(); ?>report/resultindex/gradewise_result" title="Grade wise Result">
          <i class="bi bi-circle"></i><span>Grade Wise</span>
        </a>
      </li>

      <li>
        <a href="<?php echo base_url(); ?>report/afterresultreportpdf/total_points" target="_blank" title="Full Result">
          <i class="bi bi-circle"></i><span>All Result</span>
        </a>
      </li>

      <li>
        <a href="<?php echo base_url(); ?>report/afterresultreportpdf/report_press" target="_blank" title="Report for Press">
          <i class="bi bi-circle"></i><span>Report for Press</span>
        </a>
      </li>

      <li>
        <a href="<?php echo base_url(); ?>report/resultindex/report_press_timewise" target="_blank" title="Report for Press">
          <i class="bi bi-circle"></i><span>Time wise Report</span>
        </a>
      </li>

      <li>
        <a href="<?php echo base_url(); ?>report/afterresultreport/status_of_kalolsavam_interface"  title="Kalolsavam Status">
          <i class="bi bi-circle"></i><span>Status of Kalolsavam</span>
        </a>
      </li>



      <?php } ?>          
    </ul>
  </li>
<?php } ?>

<!-- End Result Report Nav -->

  <?php if (
            $this->Session_Model->check_user_permission(47) or
            $this->Session_Model->check_user_permission(48) ) {

            if($this->General_Model->is_all_stage_alloted()) { ?>

     <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#certify-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-journal-text"></i>
      <span>Certificate</span>
      <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="certify-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <?php if ($this->Session_Model->check_user_permission(47)) { ?>
      <li>
        <a href="<?php echo base_url(); ?>certificate/certificate/" title="Certificate - Template">
          <i class="bi bi-circle"></i><span>Certificate - Template</span>
        </a>
      </li>
          <?php } ?>

          <?php if (
                  $this->Session_Model->check_user_permission(48) and
                  $this->General_Model->is_certificate_template_set()) { ?>
      <li>
        <a href="<?php echo base_url(); ?>certificate/certificate/list_item_wise" title="Certificate - Item Wise">
          <i class="bi bi-circle"></i><span>Certificate - Item Wise</span>
        </a>
      </li>
  
      <li>
        <a href="<?php echo base_url(); ?>certificate/certificate/list_school_wise" title="Certificate - School Wise">
          <i class="bi bi-circle"></i><span>Certificate - School Wise</span>
        </a>
      </li>
  
      <li>
        <a href="<?php echo base_url(); ?>certificate/certificate/list_reg_no_wise" title="Certificate - Regiser Number Wise">
          <i class="bi bi-circle"></i><span>Certificate - Reg No. Wise</span>
        </a>
      </li>
          <?php } ?>
    </ul>
  </li>
         <?php } ?>
        <?php } ?>

<!-- End Certificate Nav -->



  <?php 
        if (
            $this->Session_Model->check_user_permission(8) ) {

            if($this->General_Model->is_all_result_entered()) { ?>
  
  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#export-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-journal-text"></i><span>Export</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
 <?php } ?>
    <ul id="export-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="<?php echo base_url(); ?>report/afterresultreport/higherlevel_result"  title="participant Eligible For Higher Level Competition">
          <i class="bi bi-circle"></i><span>Higher Level Competition</span>
        </a>
      </li>
      <li>
        <a href="<?php echo base_url(); ?>export/export_district_data" title="Export Data for District Kalolsavam">
          <i class="bi bi-circle"></i><span>Export Result Data</span>
        </a>
      </li>
      
    </ul>
  </li>
<?php } ?>

<!-- End Export Nav -->

<?php if($this->Session_Model->check_user_permission(8)){?>
<li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#download-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-journal-text"></i><span>Downloads</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="download-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="<?php echo base_url(); ?>doc/chittur_itemcodes.html" target="_blank">
          <i class="bi bi-circle"></i><span>Item Code</span>
        </a>
      </li>
      <li>
        <a href="<?php echo base_url(); ?>doc/kalolsavam.pdf" target="_blank">
          <i class="bi bi-circle"></i><span>Manual</span>
        </a>
      </li>
      
    </ul>
  </li>
  <?php } ?>
  <!-- End Download Nav -->

  <li class="nav-item">
    <a class="nav-link collapsed" href="<?php echo base_url(); ?>login/logout">
      <i class="bi bi-box-arrow-in-right"></i>
      <span>Logout</span>
    </a>
  </li><!-- End Logout Nav -->

</ul>

</aside><!-- End Sidebar-->


                