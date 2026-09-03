<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Login::index');
$routes->match(['get', 'post'], 'welcome', 'Welcome::index');

$routes->match(['get', 'post'], 'login', 'Login::index');
$routes->post('login/ajax_login', 'Login::ajax_login');
$routes->match(['get', 'post'], 'login/logout', 'Login::logout');

// Public Result View Endpoints (No Login Required)
$routes->match(['get', 'post'], 'publishresult/publicresult/resultview', 'Publishresult\Publicresult::resultview');
$routes->match(['get', 'post'], 'publishresult/publicresult', 'Publishresult\Publicresult::resultview');
$routes->match(['get', 'post'], 'publishresult/publicresult/get_item_results_ajax', 'Publishresult\Publicresult::get_item_results_ajax');
$routes->match(['get', 'post'], 'publishresult/publicresult/result_declared', 'Publishresult\Publicresult::result_declared');
$routes->match(['get', 'post'], 'publishresult/publicresult/get_results_by_item', 'Publishresult\Publicresult::get_results_by_item');
$routes->match(['get', 'post'], 'publishresult/publicresult/schoolpoints', 'Publishresult\Publicresult::schoolpoints');
$routes->match(['get', 'post'], 'publishresult/publicresult/subdistrictpoints', 'Publishresult\Publicresult::subdistrictpoints');
$routes->match(['get', 'post'], 'publishresult/publicresult/allresults', 'Publishresult\Publicresult::allresults');
$routes->match(['get', 'post'], 'publishresult/publicresult/allfestschools', 'Publishresult\Publicresult::allfestschools');
$routes->match(['get', 'post'], 'publishresult/publicresult/festval_status', 'Publishresult\Publicresult::festval_status');
$routes->match(['get', 'post'], 'publishresult/publicresult/festival_allitem', 'Publishresult\Publicresult::festival_allitem');
$routes->match(['get', 'post'], 'publishresult/publicresult/finished_item', 'Publishresult\Publicresult::finished_item');
$routes->match(['get', 'post'], 'publishresult/publicresult/incomplete_item', 'Publishresult\Publicresult::incomplete_item');

// Confirmed PDF Endpoints
$routes->match(['get', 'post'], 'itemreport/itemreport_pdf', 'Itemreport::itemreport_pdf');
$routes->match(['get', 'post'], 'report/masterreport/item_code_pdf', 'Report\Masterreport::item_code_pdf');
$routes->match(['get', 'post'], 'report/prereportpdf/participant_regno_photo', 'Report\Prereportpdf::participant_regno_photo');

// ------------------------------------------------------------------
// PHASE 75 - PRIMARY BUSINESS WORKFLOW ROUTES (OPTION A: explicit GET)
// Restores reachability of the minimum primary post-login destinations.
// All target controllers self-protect via is_user_logged()/permission
// checks in their constructors. Dangerous/legacy endpoints (Test,
// Import restore/initialize, Export, Afterresult_model, staticreport,
// certificate calibration, and the broad report surface) are NOT routed.
// ------------------------------------------------------------------

// Post-login redirect targets
$routes->get('welcome/district_details', 'Welcome::district_details');
$routes->match(['get', 'post'], 'welcome/sub_district_details', 'Welcome::sub_district_details');
$routes->match(['get', 'post'], 'welcome/cluster_details', 'Welcome::cluster_details');
$routes->match(['get', 'post'], 'welcome/cluster_schools', 'Welcome::cluster_schools');
$routes->post('welcome/nonclustdetails', 'Welcome::nonclustdetails');
$routes->get('login/change_password', 'Login::change_password');
$routes->post('login/change_Pwd_insert', 'Login::change_Pwd_insert');

// Settings / Kalolsavam definition & APIs
$routes->match(['GET', 'POST'], 'admin/kalolsavam', 'Admin\Kalolsavam::index');
$routes->match(['GET', 'POST'], 'admin/kalolsavam/add_kalolsavam', 'Admin\Kalolsavam::add_kalolsavam');
$routes->match(['GET', 'POST'], 'admin/kalolsavam/edit_kalolsavam', 'Admin\Kalolsavam::edit_kalolsavam');
$routes->match(['GET', 'POST'], 'admin/kalolsavam/update_kalolsavam', 'Admin\Kalolsavam::update_kalolsavam');
$routes->match(['GET', 'POST'], 'admin/kalolsavam/get_kalolsavam_ajax', 'Admin\Kalolsavam::get_kalolsavam_ajax');
$routes->match(['GET', 'POST'], 'admin/kalolsavam/save_kalolsavam_ajax', 'Admin\Kalolsavam::save_kalolsavam_ajax');

// School Profile
$routes->match(['GET', 'POST'], 'schools/profile', 'Schools\Registration::profile');
$routes->match(['GET', 'POST'], 'schools/profile/edit', 'Schools\Registration::edit_school_profile');
$routes->match(['GET', 'POST'], 'schools/profile/update', 'Schools\Registration::update_profile');

// School Registration CRUD & AJAX endpoints
$routes->match(['GET', 'POST'], 'schools/registration', 'Schools\Registration::index');
$routes->match(['GET', 'POST'], 'schools/registration/save_participant', 'Schools\Registration::save_participant');
$routes->match(['GET', 'POST'], 'schools/registration/edit_participant_details', 'Schools\Registration::edit_participant_details');
$routes->match(['GET', 'POST'], 'schools/registration/update_participant_details', 'Schools\Registration::update_participant_details');
$routes->match(['GET', 'POST'], 'schools/registration/delete_participant_details', 'Schools\Registration::delete_participant_details');
$routes->match(['GET', 'POST'], 'schools/registration/get_participant_data', 'Schools\Registration::get_participant_data');
$routes->match(['GET', 'POST'], 'schools/registration/get_school_details', 'Schools\Registration::get_school_details');
$routes->match(['GET', 'POST'], 'schools/registration/get_school_list', 'Schools\Registration::get_school_list');
$routes->match(['GET', 'POST'], 'schools/registration/get_school_data', 'Schools\Registration::get_school_data');
$routes->match(['GET', 'POST'], 'schools/registration/get_items_by_class', 'Schools\Registration::get_items_by_class');
$routes->match(['GET', 'POST'], 'schools/registration/get_item_details', 'Schools\Registration::get_item_details');
$routes->match(['GET', 'POST'], 'schools/registration/edit_school_details', 'Schools\Registration::edit_school_details');
$routes->match(['GET', 'POST'], 'schools/registration/update', 'Schools\Registration::update');
$routes->match(['GET', 'POST'], 'schools/registration/finalize_data', 'Schools\Registration::finalize_data');
$routes->match(['GET', 'POST'], 'schools/registration/update_group_captain', 'Schools\Registration::update_group_captain');

// Admin School Entry CRUD & AJAX endpoints
$routes->match(['GET', 'POST'], 'schools/school_entry', 'Schools\Registration::school_entry');
$routes->match(['GET', 'POST'], 'schools/school_entry/save_participant', 'Schools\Registration::save_participant');
$routes->match(['GET', 'POST'], 'schools/school_entry/edit_participant_details', 'Schools\Registration::edit_participant_details');
$routes->match(['GET', 'POST'], 'schools/school_entry/update_participant_details', 'Schools\Registration::update_participant_details');
$routes->match(['GET', 'POST'], 'schools/school_entry/delete_participant_details', 'Schools\Registration::delete_participant_details');
$routes->match(['GET', 'POST'], 'schools/school_entry/get_participant_data', 'Schools\Registration::get_participant_data');
$routes->match(['GET', 'POST'], 'schools/school_entry/get_school_details', 'Schools\Registration::get_school_details');
$routes->match(['GET', 'POST'], 'schools/school_entry/get_school_list', 'Schools\Registration::get_school_list');
$routes->match(['GET', 'POST'], 'schools/school_entry/get_school_data', 'Schools\Registration::get_school_data');
$routes->match(['GET', 'POST'], 'schools/school_entry/get_items_by_class', 'Schools\Registration::get_items_by_class');
$routes->match(['GET', 'POST'], 'schools/school_entry/get_item_details', 'Schools\Registration::get_item_details');
$routes->match(['GET', 'POST'], 'schools/school_entry/edit_school_details', 'Schools\Registration::edit_school_details');
$routes->match(['GET', 'POST'], 'schools/school_entry/update', 'Schools\Registration::update');
$routes->match(['GET', 'POST'], 'schools/school_entry/finalize_data', 'Schools\Registration::finalize_data');
$routes->match(['GET', 'POST'], 'schools/school_entry/update_group_captain', 'Schools\Registration::update_group_captain');

$routes->match(['GET', 'POST'], 'schools/registration_report', 'Schools\Registration_Report::index');
$routes->match(['GET', 'POST'], 'schools/registration_report/pdf_report', 'Schools\Registration_Report::pdf_report');
$routes->match(['GET', 'POST'], 'schools/registration_report/html_report', 'Schools\Registration_Report::html_report');
$routes->match(['get', 'post'], 'schools/school_master', 'Schools\School_Master::index');
$routes->post('schools/school_master/add_school_details', 'Schools\School_Master::add_school_details');
$routes->match(['get', 'post'], 'schools/school_master/edit_school_detials', 'Schools\School_Master::edit_school_detials');
$routes->match(['get', 'post'], 'schools/school_master/update_school_detials', 'Schools\School_Master::update_school_detials');
$routes->match(['get', 'post'], 'schools/special_order_entry', 'Schools\Special_Order_Entry::index');

// ------------------------------------------------------------------
// PHASE 166 - Special Order active POST routes
// Restores ONLY the five verified active Special-Order POST endpoints
// (Phase 165). All methods inherit Special_Order_Entry::__construct()
// -> is_user_logged(true); save/edit/update/delete gate permission 20;
// get_school_details is read-only (login-gated). Do NOT add GET to
// these. Do NOT route finalize_data (no such method in this controller).
// ------------------------------------------------------------------
$routes->post('schools/special_order_entry/get_school_details', 'Schools\Special_Order_Entry::get_school_details');
$routes->post('schools/special_order_entry/save_participant', 'Schools\Special_Order_Entry::save_participant');
$routes->post('schools/special_order_entry/edit_participant_details', 'Schools\Special_Order_Entry::edit_participant_details');
$routes->post('schools/special_order_entry/delete_participant_details', 'Schools\Special_Order_Entry::delete_participant_details');
$routes->post('schools/special_order_entry/update_participant_details', 'Schools\Special_Order_Entry::update_participant_details');

// Item Master (Manage Items Code) CRUD
$routes->match(['GET', 'POST'], 'item/item_master', 'Item\Item_Master::index');
$routes->post('item/item_master/add_item_details', 'Item\Item_Master::add_item_details');
$routes->post('item/item_master/edit_item_detials', 'Item\Item_Master::edit_item_detials');
$routes->post('item/item_master/update_item_detials', 'Item\Item_Master::update_item_detials');
$routes->post('item/item_master/delete_item_detials', 'Item\Item_Master::delete_item_detials');

// User administration
$routes->match(['GET', 'POST'], 'user/admin_users', 'User\Admin_Users::index');
$routes->match(['GET', 'POST'], 'user/user_registration', 'User\User_Registration::index');
$routes->match(['GET', 'POST'], 'user/user_registration/add_user', 'User\User_Registration::add_user');
$routes->match(['GET', 'POST'], 'user/user_registration/cluster', 'User\User_Registration::cluster');
$routes->match(['GET', 'POST'], 'user/user_registration/add_cluster', 'User\User_Registration::add_cluster');
$routes->post('user/user_registration/userinsert', 'User\User_Registration::userinsert');
$routes->post('user/user_registration/edit_user_detials', 'User\User_Registration::edit_user_detials');
$routes->post('user/user_registration/update_user_detials', 'User\User_Registration::update_user_detials');
$routes->post('user/user_registration/delete_user_detials', 'User\User_Registration::delete_user_detials');
$routes->post('user/user_registration/clusterinsert', 'User\User_Registration::clusterinsert');
$routes->post('user/user_registration/edit_cluster_details', 'User\User_Registration::edit_cluster_details');
$routes->post('user/user_registration/update_cluster_details', 'User\User_Registration::update_cluster_details');
$routes->post('user/user_registration/delete_cluster_details', 'User\User_Registration::delete_cluster_details');
$routes->match(['GET', 'POST'], 'user/user_cluster', 'User\User_Cluster::index');
$routes->match(['GET', 'POST'], 'user/user_cluster/add_form', 'User\User_Cluster::add_form');
$routes->post('user/user_cluster/save_user_cluster', 'User\User_Cluster::save_user_cluster');
$routes->match(['GET', 'POST'], 'user/user_cluster/edit_user_cluster', 'User\User_Cluster::edit_user_cluster');
$routes->post('user/user_cluster/update_user_cluster', 'User\User_Cluster::update_user_cluster');
$routes->post('user/user_cluster/delete_user_cluster', 'User\User_Cluster::delete_user_cluster');

// Stage / allotment
$routes->get('stage/stage_details', 'Stage\Stage_Details::index');

// ------------------------------------------------------------------
// PHASE 145 - Stage Details mutation routes (POST only)
// Restores the Add/Edit/Update/Delete stage workflows. CI3 auto-routing
// previously resolved these; CI4 auto-routing is OFF so the four methods
// had become unreachable (404). All methods enforce login + permission 19
// in Stage_Details itself; mutation methods are safe to expose POST-only.
// Delete is kept separate and guarded. Do NOT add GET to these.
// ------------------------------------------------------------------
$routes->post('stage/stage_details/add_stage', 'Stage\Stage_Details::add_stage');
$routes->post('stage/stage_details/edit_stage_detials', 'Stage\Stage_Details::edit_stage_detials');
$routes->post('stage/stage_details/update_stage_detials', 'Stage\Stage_Details::update_stage_detials');
$routes->post('stage/stage_details/delete_stage_detials', 'Stage\Stage_Details::delete_stage_detials');

$routes->get('stage/allotment', 'Stage\Allotment::index');
$routes->match(['get', 'post'], 'stage/item_participant/participant_nodetails', 'Stage\Item_Participant::participant_nodetails');
$routes->post('stage/item_participant/stage_allot_fest_all', 'Stage\Item_Participant::stage_allot_fest_all');
$routes->match(['get', 'post'], 'stage/festival_scheduling', 'Stage\Festival_Scheduling::index');
$routes->match(['get', 'post'], 'stage/festival_scheduling/order_of_events', 'Stage\Festival_Scheduling::order_of_events');
$routes->match(['get', 'post'], 'stage/festival_scheduling/generate_schedule', 'Stage\Festival_Scheduling::generate_schedule');
$routes->match(['get', 'post'], 'stage/festival_scheduling/view_schedule', 'Stage\Festival_Scheduling::view_schedule');
$routes->match(['get', 'post'], 'stage/festival_scheduling/analyze_clashes', 'Stage\Festival_Scheduling::analyze_clashes');
$routes->match(['get', 'post'], 'stage/festival_scheduling/schedule_statistics', 'Stage\Festival_Scheduling::schedule_statistics');
$routes->match(['get', 'post'], 'stage/festival_scheduling/export_schedule', 'Stage\Festival_Scheduling::export_schedule');
$routes->match(['get', 'post'], 'stage/festival_scheduling/save_gemini_key', 'Stage\Festival_Scheduling::save_gemini_key');
$routes->match(['get', 'post'], 'stage/festival_scheduling/test_gemini_key', 'Stage\Festival_Scheduling::test_gemini_key');
$routes->match(['get', 'post'], 'stage/festival_scheduling/clear_schedule', 'Stage\Festival_Scheduling::clear_schedule');
$routes->get('stage/festival_scheduling/order_of_events/download', 'Stage\Festival_Scheduling::download');
$routes->get('stage/festival_scheduling/download', 'Stage\Festival_Scheduling::download');
$routes->match(['GET', 'POST'], 'report/prereport/stageallot_duration', 'Report\Prereport::stageallot_duration');
$routes->match(['GET', 'POST'], 'report/prereport/callsheet_first', 'Report\Prereport::callsheet_first');
$routes->match(['GET', 'POST'], 'report/prereport/callsheet_all', 'Report\Prereport::callsheet_all');
$routes->match(['GET', 'POST'], 'report/prereport/timesheetinterface', 'Report\Prereport::timesheetinterface');
$routes->match(['GET', 'POST'], 'report/prereport/score_sheet_interfaces', 'Report\Prereport::score_sheet_interfaces');
$routes->match(['GET', 'POST'], 'report/prereport/tabulation_report_interface', 'Report\Prereport::tabulation_report_interface');
$routes->match(['GET', 'POST'], 'report/prereport/four_reports', 'Report\Prereport::four_reports');
// Additional Pre Fest Report interface routes (full menu coverage)
$routes->match(['GET', 'POST'], 'report/prereport/list_school', 'Report\Prereport::list_school');
$routes->match(['GET', 'POST'], 'report/prereport/list_participants', 'Report\Prereport::list_participants');
$routes->match(['GET', 'POST'], 'report/prereport/team_manager_all', 'Report\Prereport::team_manager_all');
$routes->match(['GET', 'POST'], 'report/prereport/feedetails', 'Report\Prereport::feedetails');
$routes->match(['GET', 'POST'], 'report/prereport/participant_cardindex', 'Report\Prereport::participant_cardindex');
$routes->match(['GET', 'POST'], 'report/prereport/participant_cardindex_photo', 'Report\Prereport::participant_cardindex_photo');
$routes->match(['GET', 'POST'], 'report/prereport/participant_cardindex2', 'Report\Prereport::participant_cardindex2');
$routes->match(['GET', 'POST'], 'report/prereport/participant_regno', 'Report\Prereport::participant_regno');
$routes->match(['GET', 'POST'], 'report/prereport/participant_more_item', 'Report\Prereport::participant_more_item');
$routes->match(['GET', 'POST'], 'report/prereport/participant_limit_item_more', 'Report\Prereport::participant_limit_item_more');
$routes->match(['GET', 'POST'], 'report/prereport/clustor_report', 'Report\Prereport::clustor_report');
$routes->match(['GET', 'POST'], 'report/prereport/clustor_report_all', 'Report\Prereport::clustor_report_all');
$routes->match(['GET', 'POST'], 'report/prereport/fest_details', 'Report\Prereport::fest_details');
$routes->match(['GET', 'POST'], 'report/prereport/itemwise_report_interface', 'Report\Prereport::itemwise_report_interface');
$routes->match(['GET', 'POST'], 'report/prereport/datewisepart', 'Report\Prereport::datewisepart');
$routes->match(['GET', 'POST'], 'report/prereport/stagereportdate', 'Report\Prereport::stagereportdate');
$routes->match(['GET', 'POST'], 'report/prereport/stagereport_all', 'Report\Prereport::stagereport_all');
$routes->match(['GET', 'POST'], 'report/prereport/score_sheet_fest', 'Report\Prereport::score_sheet_fest');
$routes->match(['GET', 'POST'], 'report/prereport/appealed_part', 'Report\Prereport::appealed_part');
$routes->match(['GET', 'POST'], 'report/prereport/appeal_courtorder', 'Report\Prereport::appeal_courtorder');
$routes->match(['GET', 'POST'], 'report/prereport/clashes_details_interface', 'Report\Prereport::clashes_details_interface');

// Pre-report PDFs (call sheet family + full report surface) — all open in new tab via target _blank
$routes->match(['GET', 'POST'], 'report/prereportpdf/stageallot_duration', 'Report\Prereportpdf::stageallot_duration');
$routes->match(['GET', 'POST'], 'report/prereportpdf/callsheet_report', 'Report\Prereportpdf::callsheet_report');
$routes->match(['GET', 'POST'], 'report/prereportpdf/callsheet_all', 'Report\Prereportpdf::callsheet_all');
$routes->match(['GET', 'POST'], 'report/prereportpdf/call_date_stage', 'Report\Prereportpdf::call_date_stage');
$routes->match(['GET', 'POST'], 'report/prereportpdf/timesheet', 'Report\Prereportpdf::timesheet');
$routes->match(['GET', 'POST'], 'report/prereportpdf/timesheet_date', 'Report\Prereportpdf::timesheet_date');
$routes->match(['GET', 'POST'], 'report/prereportpdf/time_date_stage', 'Report\Prereportpdf::time_date_stage');
$routes->match(['GET', 'POST'], 'report/prereportpdf/print_score_sheet', 'Report\Prereportpdf::print_score_sheet');
$routes->match(['GET', 'POST'], 'report/prereportpdf/fest_scoresheet_details', 'Report\Prereportpdf::fest_scoresheet_details');
$routes->match(['GET', 'POST'], 'report/prereportpdf/score_date_stage', 'Report\Prereportpdf::score_date_stage');
$routes->match(['GET', 'POST'], 'report/prereportpdf/tabulation_date_stage', 'Report\Prereportpdf::tabulation_date_stage');
$routes->match(['GET', 'POST'], 'report/prereportpdf/four_reports', 'Report\Prereportpdf::four_reports');
$routes->match(['GET', 'POST'], 'report/prereport/rpt_tabulation_sheet', 'Report\Prereport::rpt_tabulation_sheet');
$routes->match(['GET', 'POST'], 'report/prereportpdf/list_school', 'Report\Prereportpdf::list_school');
$routes->match(['GET', 'POST'], 'report/prereportpdf/schoolfestall', 'Report\Prereportpdf::schoolfestall');
$routes->match(['GET', 'POST'], 'report/prereportpdf/list_participants', 'Report\Prereportpdf::list_participants');
$routes->match(['GET', 'POST'], 'report/prereportpdf/feedetails', 'Report\Prereportpdf::feedetails');
$routes->match(['GET', 'POST'], 'report/prereportpdf/participant_cardindex', 'Report\Prereportpdf::participant_cardindex');
$routes->match(['GET', 'POST'], 'report/prereportpdf/participant_regno', 'Report\Prereportpdf::participant_regno');
$routes->match(['GET', 'POST'], 'report/prereportpdf/participant_more_item', 'Report\Prereportpdf::participant_more_item');
$routes->match(['GET', 'POST'], 'report/prereportpdf/eligible_school', 'Report\Prereportpdf::eligible_school');
$routes->match(['GET', 'POST'], 'report/prereportpdf/cluster_report_all', 'Report\Prereportpdf::cluster_report_all');
$routes->match(['GET', 'POST'], 'report/prereportpdf/cluster_date_stage', 'Report\Prereportpdf::cluster_date_stage');
$routes->match(['GET', 'POST'], 'report/prereportpdf/datewisepart', 'Report\Prereportpdf::datewisepart');
$routes->match(['GET', 'POST'], 'report/prereportpdf/stagereportdate', 'Report\Prereportpdf::stagereportdate');
$routes->match(['GET', 'POST'], 'report/prereportpdf/stagereport_all', 'Report\Prereportpdf::stagereport_all');
$routes->match(['GET', 'POST'], 'report/prereportpdf/appealed_part', 'Report\Prereportpdf::appealed_part');
$routes->match(['GET', 'POST'], 'report/prereportpdf/appeal_courtorder', 'Report\Prereportpdf::appeal_courtorder');
$routes->match(['GET', 'POST'], 'report/prereportpdf/clashes_details', 'Report\Prereportpdf::clashes_details');
// ------------------------------------------------------------------
// PRE-FEST REPORT — Controlled Migration (2026-09-02)
// Restores all previously-unrouted Pre Fest Report endpoints whose
// target views/forms/AJAX already reference them. No wildcard routes.
// Methods chosen from actual form usage: POST for form submissions
// (PDF generation reading POST), GET for direct anchor links.
// ------------------------------------------------------------------
// CRITICAL FIX: School Contacts menu link (menu_left.php:325) points to
// report/prereport/list_school_with_team but the method lives in
// Report\Prereportpdf::list_school_with_team. The alias route preserves
// the existing menu URL while reaching the existing controller method.
$routes->match(['GET', 'POST'], 'report/prereport/list_school_with_team', 'Report\Prereportpdf::list_school_with_team');
$routes->get('report/prereportpdf/all_feedet_report_all', 'Report\Prereportpdf::all_feedet_report_all');
$routes->post('report/prereportpdf/clusterreport_report', 'Report\Prereportpdf::clusterreport_report');
$routes->post('report/prereportpdf/cluster_report_all_doc', 'Report\Prereportpdf::cluster_report_all_doc');
$routes->post('report/prereportpdf/more_limit_partlist', 'Report\Prereportpdf::more_limit_partlist');
$routes->match(['GET', 'POST'], 'report/prereportpdf/more_limit_partlist_detailed', 'Report\Prereportpdf::more_limit_partlist_detailed');
$routes->post('report/prereportpdf/list_participants_datewise', 'Report\Prereportpdf::list_participants_datewise');
$routes->post('report/prereportpdf/list_participants_stagewise', 'Report\Prereportpdf::list_participants_stagewise');
$routes->post('report/prereportpdf/participant_cardindex_school_photo', 'Report\Prereportpdf::participant_cardindex_school_photo');
$routes->post('report/prereportpdf/participant_cardindex_school2', 'Report\Prereportpdf::participant_cardindex_school2');
$routes->post('report/prereportpdf/participant_regnowithphoto', 'Report\Prereportpdf::participant_regnowithphoto');
$routes->post('report/prereportpdf/rpt_itemwiseparticipants', 'Report\Prereportpdf::rpt_itemwiseparticipants');
$routes->post('report/prereportpdf/fest_details', 'Report\Prereportpdf::fest_details');
$routes->get('report/prereportpdf/stagereport_abstract', 'Report\Prereportpdf::stagereport_abstract');
// Compatibility aliases for legacy view form targets that use abbreviated
// names but resolve to the same PDF implementations (preserve URLs).
$routes->post('report/prereportpdf/participant_cardindex_photo', 'Report\Prereportpdf::participant_cardindex_school_photo');
$routes->post('report/prereportpdf/participant_cardindex2', 'Report\Prereportpdf::participant_cardindex_school2');
$routes->post('report/prereportpdf/team_manager_all', 'Report\Prereportpdf::team_manager_all');

// Ajax2 endpoints
$routes->match(['GET', 'POST'], 'ajax/ajax2/fetch_callsheet_festival', 'Ajax\Ajax2::fetch_callsheet_festival');
$routes->match(['GET', 'POST'], 'ajax/ajax2/fetch_school_from_festival', 'Ajax\Ajax2::fetch_school_from_festival');
$routes->match(['GET', 'POST'], 'ajax/ajax2/fetch_item_name', 'Ajax\Ajax2::fetch_item_name');

// Loadajax endpoints for festival/item lookups
$routes->match(['GET', 'POST'], 'ajax/loadajax/fetch_item_from_festival', 'Ajax\Loadajax::fetch_item_from_festival');
$routes->match(['GET', 'POST'], 'ajax/loadajax/fetch_item_from_festival2', 'Ajax\Loadajax::fetch_item_from_festival2');
$routes->match(['GET', 'POST'], 'ajax/loadajax/fetch_item_from_participantt', 'Ajax\Loadajax::fetch_item_from_participantt');
$routes->match(['GET', 'POST'], 'ajax/loadajax/fetch_higherlevel', 'Ajax\Loadajax::fetch_higherlevel');

// Test Grid calibration and Fallback no-data PDF routes
$routes->match(['GET', 'POST', 'HEAD'], 'test', 'Test::index');
$routes->match(['GET', 'POST', 'HEAD'], 'test/index', 'Test::index');
$routes->match(['GET', 'POST', 'HEAD'], 'test/nodata', 'Test::nodata');

// Result entry / publishing (primary landing pages only)
$routes->get('result/resultentry', 'Result\Resultentry::index');
$routes->post('result/resultentry/get_item_details_result', 'Result\Resultentry::get_item_details_result');
$routes->match(['get', 'post'], 'result/resultentry/item_result_list', 'Result\Resultentry::item_result_list');
$routes->get('publishresult/resultindex/resultview', 'Publishresult\Resultindex::resultview');

// ------------------------------------------------------------------
// PHASE 97 - Protected Admin Users AJAX endpoints (POST only)
// Required by js/user/adminusers.js loadSubDistrict()/loadSchool().
// Loadajax constructor enforces login + USER_GROUP == 'W'.
// Do NOT add GET or wildcard routes here.
// ------------------------------------------------------------------
$routes->post('ajax/loadajax/get_subdistrict_details', 'Ajax\Loadajax::get_subdistrict_details');
$routes->post('ajax/loadajax/get_school_details', 'Ajax\Loadajax::get_school_details');
$routes->post('ajax/loadajax/get_edu_district_details', 'Ajax\Loadajax::get_edu_district_details');
$routes->post('ajax/loadajax/get_subdistrict_details_of_edu_district', 'Ajax\Loadajax::get_subdistrict_details_of_edu_district');
$routes->post('ajax/loadajax/get_subdistrict_details_small', 'Ajax\Loadajax::get_subdistrict_details_small');
$routes->post('ajax/loadajax/change_confirmation_status', 'Ajax\Loadajax::change_confirmation_status');
$routes->post('ajax/change_confirmation_status', 'Ajax\Loadajax::change_confirmation_status');

// ------------------------------------------------------------------
// PHASE 101 - Stage Allotment item-details AJAX endpoint (POST only)
// Used by js/stages.js fetch_item_details() after item code entry.
// Allotment constructor enforces login + permission; method is SELECT-only.
// Do NOT add GET or wildcard routes here.
// ------------------------------------------------------------------
$routes->post('stage/allotment/get_item_details', 'Stage\Allotment::get_item_details');

// ------------------------------------------------------------------
// Import module routes (Import controller)
// import_data: Group A (USER_TYPE 2|3) — CSV import form + upload
// backup_data: Full .gz database backup download
// backup_data_sql: Group A|W — SQL backup with file listing
// restore_database: Group W — SQL restore form + upload
// All permission checks enforced inside controller methods.
// ------------------------------------------------------------------
$routes->match(['get', 'post'], 'import/import_data', 'Import::import_data');
$routes->get('import/backup_data', 'Import::backup_data');
$routes->get('import/backup_data_sql', 'Import::backup_data_sql');
$routes->match(['get', 'post'], 'import/restore_database', 'Import::restore_database');

// ------------------------------------------------------------------
// RESULT REPORT SUITE — Controlled Migration (2026-09-03)
// Explicit routes for all Result Report interfaces, PDF generators,
// and AJAX report endpoints under Timefestreport, Afterresultreport,
// and Report\Resultindex.
// ------------------------------------------------------------------

// Timefestreport & Timefestreportpdf (Confidential Results)
$routes->match(['GET', 'POST'], 'report/timefestreport/timefest_result_confidential', 'Report\Timefestreport::timefest_result_confidential');
$routes->match(['GET', 'POST'], 'report/timefestreport/timefest_result', 'Report\Timefestreport::timefest_result');
$routes->match(['GET', 'POST'], 'report/timefestreport/timefest_result_common', 'Report\Timefestreport::timefest_result_common');
$routes->match(['GET', 'POST'], 'report/timefestreportpdf/confidential', 'Report\Timefestreportpdf::confidential');
$routes->match(['GET', 'POST'], 'report/timefestreportpdf/common_report', 'Report\Timefestreportpdf::common_report');
$routes->match(['GET', 'POST'], 'report/timefestreportpdf/datewisepart', 'Report\Timefestreportpdf::datewisepart');

// Afterresultreport (Interfaces)
$routes->match(['GET', 'POST'], 'report/afterresultreport/school_wise_result_interface', 'Report\Afterresultreport::school_wise_result_interface');
$routes->match(['GET', 'POST'], 'report/afterresultreport/item_code_reports', 'Report\Afterresultreport::item_code_reports');
$routes->match(['GET', 'POST'], 'report/afterresultreport/status_of_kalolsavam_interface', 'Report\Afterresultreport::status_of_kalolsavam_interface');
$routes->match(['GET', 'POST'], 'report/afterresultreport/timefest_result', 'Report\Afterresultreport::timefest_result');
$routes->match(['GET', 'POST'], 'report/afterresultreport/rankwise_participant_result', 'Report\Afterresultreport::rankwise_participant_result');
$routes->match(['GET', 'POST'], 'report/afterresultreport/higherlevel_result', 'Report\Afterresultreport::higherlevel_result');

// Afterresultreportpdf (PDF and Print Reports)
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/school_wise_result', 'Report\Afterresultreportpdf::school_wise_result');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/school_wise_result_all', 'Report\Afterresultreportpdf::school_wise_result_all');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/allschool_points_result', 'Report\Afterresultreportpdf::allschool_points_result');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/item_code_reports', 'Report\Afterresultreportpdf::item_code_reports');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/total_points', 'Report\Afterresultreportpdf::total_points');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/report_press', 'Report\Afterresultreportpdf::report_press');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/time_wise_result_report', 'Report\Afterresultreportpdf::time_wise_result_report');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/numwise_timeresult_report', 'Report\Afterresultreportpdf::numwise_timeresult_report');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/status_of_kalolsavam', 'Report\Afterresultreportpdf::status_of_kalolsvam');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/status_of_kalolsavam1', 'Report\Afterresultreportpdf::status_of_kalolsavam1');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/datewisepart', 'Report\Afterresultreportpdf::datewisepart');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/higherlevel_result', 'Report\Afterresultreportpdf::higherlevel_result');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/schoolhigher_result', 'Report\Afterresultreportpdf::schoolhigher_result');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/schoolhigher_resultall', 'Report\Afterresultreportpdf::schoolhigher_resultall');

// ------------------------------------------------------------------
// EXPORT MODULE — Controlled Migration (2026-09-03)
// Export menu: "Export Result Data" -> export/export_district_data. GET renders
// the export/district_data form; POST with hidExport=TRUE streams CSV. The
// "Export Photo" button on that page posts to welcome/export_photo (ZIP).
// export/export_sub_district_data streams the sub-district CSV (POST only).
// All methods enforce login + permission 8 in the Export controller.
// Do NOT add GET to export_sub_district_data (CSV-only POST).
// ------------------------------------------------------------------
$routes->match(['GET', 'POST'], 'export', 'Export::index');
$routes->match(['GET', 'POST'], 'export/export_district_data', 'Export::export_district_data');
$routes->post('export/export_sub_district_data', 'Export::export_sub_district_data');
$routes->match(['GET', 'POST'], 'welcome/export_photo', 'Welcome::export_photo');

// Resultindex (Report namespace: Grade, Rank, Time-wise Interfaces and Reports)
$routes->match(['GET', 'POST'], 'report/resultindex/gradewise_interface', 'Report\Resultindex::gradewise_interface');
$routes->match(['GET', 'POST'], 'report/resultindex/gradewise_report', 'Report\Resultindex::gradewise_report');
$routes->match(['GET', 'POST'], 'report/resultindex/rankwise_result', 'Report\Resultindex::rankwise_result');
$routes->match(['GET', 'POST'], 'report/resultindex/rankwise_report', 'Report\Resultindex::rankwise_report');
$routes->match(['GET', 'POST'], 'report/resultindex/gradewise_result', 'Report\Resultindex::gradewise_result');
$routes->match(['GET', 'POST'], 'report/resultindex/grade_report', 'Report\Resultindex::grade_report');
$routes->match(['GET', 'POST'], 'report/resultindex/report_press_timewise', 'Report\Resultindex::report_press_timewise');
$routes->match(['GET', 'POST'], 'report/resultindex/itemwise_result', 'Report\Resultindex::itemwise_result');
$routes->match(['GET', 'POST'], 'report/resultindex/itemwise_report', 'Report\Resultindex::itemwise_report');
$routes->match(['GET', 'POST'], 'report/resultindex/allfestschools', 'Report\Resultindex::allfestschools');
$routes->match(['GET', 'POST'], 'report/resultindex/resultview', 'Report\Resultindex::resultview');
$routes->match(['GET', 'POST'], 'report/resultindex/result_declared', 'Report\Resultindex::result_declared');
$routes->match(['GET', 'POST'], 'report/resultindex/schoolpoints', 'Report\Resultindex::schoolpoints');
$routes->match(['GET', 'POST'], 'report/resultindex/allresults', 'Report\Resultindex::allresults');
$routes->match(['GET', 'POST'], 'report/resultindex/festval_status', 'Report\Resultindex::festval_status');
$routes->match(['GET', 'POST'], 'report/resultindex/festival_allitem', 'Report\Resultindex::festival_allitem');
$routes->match(['GET', 'POST'], 'report/resultindex/finished_item', 'Report\Resultindex::finished_item');
$routes->match(['GET', 'POST'], 'report/resultindex/incomplete_item', 'Report\Resultindex::incomplete_item');

// ------------------------------------------------------------------
// CERTIFICATE MODULE — Controlled Migration (2026-09-03)
// Restores all Certificate endpoints: template management, item-wise,
// school-wise, and register-number-wise certificate PDF generation.
// All methods enforce login + permission 47/48 in Certificate controller.
// ------------------------------------------------------------------

// Certificate Template Management (Permission 47)
$routes->match(['GET', 'POST', 'HEAD'], 'certificate/certificate', 'Certificate\Certificate::index');
$routes->match(['GET', 'POST', 'HEAD'], 'certificate/certificate/index', 'Certificate\Certificate::index');
$routes->match(['GET', 'POST', 'HEAD'], 'certificate/certificate/save_certificate_template', 'Certificate\Certificate::save_certificate_template');
$routes->match(['GET', 'POST', 'HEAD'], 'certificate/certificate/save_certificate_template/(:any)', 'Certificate\Certificate::save_certificate_template');
$routes->match(['GET', 'HEAD'], 'certificate/certificate/test_certificate', 'Certificate\Certificate::test_certificate');
$routes->match(['GET', 'HEAD'], 'certificate/certificate/test_certificare_withoutgraph', 'Certificate\Certificate::test_certificare_withoutgraph');

// Certificate Item-Wise (Permission 48)
$routes->match(['GET', 'POST'], 'certificate/certificate/list_item_wise', 'Certificate\Certificate::list_item_wise');
$routes->match(['GET', 'POST'], 'certificate/certificate/get_certificate_itemwise', 'Certificate\Certificate::get_certificate_itemwise');
$routes->match(['GET', 'POST'], 'certificate/certificate/get_certificate_itemwise/', 'Certificate\Certificate::get_certificate_itemwise');
$routes->match(['GET', 'POST'], 'certificate/certificate/get_certificate_pdf', 'Certificate\Certificate::get_certificate_pdf');

// Certificate School-Wise (Permission 48)
$routes->match(['GET', 'POST'], 'certificate/certificate/list_school_wise', 'Certificate\Certificate::list_school_wise');
$routes->match(['GET', 'POST'], 'certificate/certificate/get_certificate_school_wise', 'Certificate\Certificate::get_certificate_school_wise');
$routes->match(['GET', 'POST'], 'certificate/certificate/get_certificate_school_wise/', 'Certificate\Certificate::get_certificate_school_wise');
$routes->match(['GET', 'POST'], 'certificate/certificate/get_certificate_pdf_school_wise', 'Certificate\Certificate::get_certificate_pdf_school_wise');

// Certificate Register-Number-Wise (Permission 48)
$routes->match(['GET', 'POST'], 'certificate/certificate/list_reg_no_wise', 'Certificate\Certificate::list_reg_no_wise');
$routes->match(['GET', 'POST'], 'certificate/certificate/get_certificate_pdf_participant_wise', 'Certificate\Certificate::get_certificate_pdf_participant_wise');

// ------------------------------------------------------------------
// CERTIFICATE AJAX ENDPOINTS — Required by js/certificate.js
// Provides dynamic dropdowns for group participants, school items,
// school captains, and participant details lookup.
// ------------------------------------------------------------------
$routes->post('ajax/loadajax/get_all_group_participants', 'Ajax\Loadajax::get_all_group_participants');
$routes->post('ajax/loadajax/get_school_items', 'Ajax\Loadajax::get_school_items');
$routes->post('ajax/loadajax/get_school_captains', 'Ajax\Loadajax::get_school_captains');
$routes->post('ajax/loadajax/get_participant_details', 'Ajax\Loadajax::get_participant_details');

// ------------------------------------------------------------------
// SECURE STATIC FILE DOWNLOADS — Task 1.2
// Controller-based serving with filename allowlist.
// Replaces direct doc/ static access for chittur_itemcodes.html
// and kalolsavam.pdf. Path traversal blocked via basename check.
// ------------------------------------------------------------------
$routes->get('downloads/serve/(:segment)', 'Downloads::serve/$1');

