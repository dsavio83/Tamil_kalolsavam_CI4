<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
// Application Landing Page & Public Results Portal (Default Entry Point)
$routes->get('/', 'Publishresult\Publicresult::resultview');
$routes->match(['get', 'post'], 'welcome', 'Welcome::index');
$routes->match(['GET', 'POST'], 'welcome/toggle_manager_marks', 'Welcome::toggle_manager_marks');

// Login Routes: GET serves landing page with login modal; POST processes authentication
$routes->get('login', 'Publishresult\Publicresult::resultview');
$routes->post('login', 'Login::index');
$routes->post('login/ajax_login', 'Login::ajax_login');
$routes->match(['get', 'post'], 'login/logout', 'Login::logout');

// Public Result View Endpoints (No Login Required)
$routes->get('publishresult/publicresult/cache_version', 'Publishresult\Publicresult::cache_version');
$routes->match(['get', 'post'], 'publishresult/publicresult/resultview', 'Publishresult\Publicresult::resultview');
$routes->match(['get', 'post'], 'publishresult/publicresult', 'Publishresult\Publicresult::resultview');
$routes->match(['get', 'post'], 'publishresult/publicresult/get_public_item_codes_ajax', 'Publishresult\Publicresult::get_public_item_codes_ajax');
$routes->match(['get', 'post'], 'publishresult/publicresult/get_item_results_ajax', 'Publishresult\Publicresult::get_item_results_ajax');
$routes->match(['get', 'post'], 'publishresult/publicresult/result_declared', 'Publishresult\Publicresult::result_declared');
$routes->match(['get', 'post'], 'publishresult/publicresult/get_results_by_item', 'Publishresult\Publicresult::get_results_by_item');
$routes->match(['get', 'post'], 'publishresult/publicresult/schoolpoints', 'Publishresult\Publicresult::schoolpoints');
$routes->match(['get', 'post'], 'publishresult/resultindex/schoolpoints', 'Publishresult\Resultindex::schoolpoints');
$routes->match(['get', 'post'], 'publishresult/resultindex/schoolpoints/(:any)', 'Publishresult\Resultindex::schoolpoints/$1');
$routes->match(['get', 'post'], 'publishresult/resultindex/(:any)', 'Publishresult\Resultindex::$1');
$routes->match(['get', 'post'], 'report/resultindex/schoolpoints', 'Report\Resultindex::schoolpoints');
$routes->match(['get', 'post'], 'report/resultindex/schoolpoints/(:any)', 'Report\Resultindex::schoolpoints/$1');
$routes->match(['get', 'post'], 'district/report/resultindex/schoolpoints', 'District\Report\Resultindex::schoolpoints');
$routes->match(['get', 'post'], 'district/report/resultindex/schoolpoints/(:any)', 'District\Report\Resultindex::schoolpoints/$1');
$routes->match(['get', 'post'], 'publishresult/publicresult/subdistrictpoints', 'Publishresult\Publicresult::subdistrictpoints');
$routes->match(['get', 'post'], 'publishresult/publicresult/allresults', 'Publishresult\Publicresult::allresults');
$routes->match(['get', 'post'], 'publishresult/publicresult/allfestschools', 'Publishresult\Publicresult::allfestschools');
$routes->match(['get', 'post'], 'publishresult/publicresult/festval_status', 'Publishresult\Publicresult::festval_status');
$routes->match(['get', 'post'], 'publishresult/publicresult/festival_allitem', 'Publishresult\Publicresult::festival_allitem');
$routes->match(['get', 'post'], 'publishresult/publicresult/finished_item', 'Publishresult\Publicresult::finished_item');
$routes->match(['get', 'post'], 'publishresult/publicresult/item_codes', 'Publishresult\Publicresult::item_codes');
$routes->match(['get', 'post'], 'downloads/item_codes', 'Publishresult\Publicresult::item_codes');
$routes->match(['get', 'post'], 'item_codes', 'Publishresult\Publicresult::item_codes');
$routes->match(['get', 'post'], 'welcome/selectDistrict/(:num)', 'Publishresult\Publicresult::selectDistrict/$1');
$routes->match(['get', 'post'], 'welcome/selectDistrict/(:num)/(:any)', 'Publishresult\Publicresult::selectDistrict/$1');
$routes->match(['get', 'post'], 'home/subdistrictHome/(:num)', 'Publishresult\Publicresult::subdistrictHome/$1');
$routes->match(['get', 'post'], 'home/subdistrictHome/(:num)/(:any)', 'Publishresult\Publicresult::subdistrictHome/$1');

// Confirmed PDF Endpoints
$routes->match(['get', 'post'], 'itemreport/itemreport_pdf', 'Itemreport::itemreport_pdf');
$routes->match(['get', 'post'], 'report/masterreport/item_code', 'Report\Masterreport::item_code');
$routes->match(['get', 'post'], 'report/masterreport/item_code/', 'Report\Masterreport::item_code');
$routes->match(['get', 'post'], 'report/masterreport/item_code_pdf', 'Report\Masterreport::item_code_pdf');
$routes->match(['get', 'post'], 'report/prereportpdf/participant_regno_photo', 'Report\Prereportpdf::participant_regno_photo');

// Stage Pre-Reports Endpoints & Friendly Aliases
$routes->match(['get', 'post'], 'report/prereport/callsheet_first', 'Report\Prereport::callsheet_first');
$routes->match(['get', 'post'], 'report/prereport/time_sheet', 'Report\Prereport::timesheetinterface');
$routes->match(['get', 'post'], 'report/prereport/timesheetinterface', 'Report\Prereport::timesheetinterface');
$routes->match(['get', 'post'], 'report/prereport/score_sheet', 'Report\Prereport::score_sheet_interfaces');
$routes->match(['get', 'post'], 'report/prereport/score_sheet_interfaces', 'Report\Prereport::score_sheet_interfaces');
$routes->match(['get', 'post'], 'report/prereport/tabulation', 'Report\Prereport::tabulation_report_interface');
$routes->match(['get', 'post'], 'report/prereport/tabulation_report_interface', 'Report\Prereport::tabulation_report_interface');
$routes->match(['get', 'post'], 'report/prereport/allinone', 'Report\Prereport::four_reports');
$routes->match(['get', 'post'], 'report/prereport/four_reports', 'Report\Prereport::four_reports');
$routes->match(['get', 'post'], 'report/prereportpdf/callsheet_all', 'Report\Prereportpdf::callsheet_all');
$routes->match(['get', 'post'], 'report/prereportpdf/callsheet_report', 'Report\Prereportpdf::callsheet_report');
$routes->match(['get', 'post'], 'report/prereportpdf/call_date_stage', 'Report\Prereportpdf::call_date_stage');
$routes->match(['get', 'post'], 'report/prereportpdf/tabulation_date_stage', 'Report\Prereportpdf::tabulation_date_stage');
$routes->match(['get', 'post'], 'report/prereportpdf/score_date_stage', 'Report\Prereportpdf::score_date_stage');
$routes->match(['get', 'post'], 'report/prereportpdf/four_reports', 'Report\Prereportpdf::four_reports');

// Cover Print Designer & Generator Endpoints
$routes->match(['GET', 'POST'], 'cover', 'Report\Cover_Print::index');
$routes->match(['GET', 'POST'], 'cover/templates', 'Report\Cover_Print::templates');
$routes->match(['GET', 'POST'], 'cover/designer', 'Report\Cover_Print::designer');
$routes->match(['GET', 'POST'], 'cover/designer/(:num)', 'Report\Cover_Print::designer/$1');
$routes->match(['GET', 'POST'], 'cover/save_template', 'Report\Cover_Print::save_template');
$routes->match(['GET', 'POST'], 'cover/duplicate_template/(:num)', 'Report\Cover_Print::duplicate_template/$1');
$routes->match(['GET', 'POST'], 'cover/delete_template/(:num)', 'Report\Cover_Print::delete_template/$1');
$routes->match(['GET', 'POST'], 'cover/set_default/(:num)', 'Report\Cover_Print::set_default/$1');
$routes->match(['GET', 'POST'], 'cover/generate', 'Report\Cover_Print::generate');
$routes->match(['GET', 'POST'], 'cover/generate/(:num)', 'Report\Cover_Print::generate/$1');
$routes->match(['GET', 'POST'], 'cover/ajax_filter_items', 'Report\Cover_Print::ajax_filter_items');
$routes->match(['GET', 'POST'], 'cover/ajax_preview_data', 'Report\Cover_Print::ajax_preview_data');
$routes->match(['GET', 'POST'], 'cover/upload_asset', 'Report\Cover_Print::upload_asset');
$routes->match(['GET', 'POST'], 'cover/generate_pdf', 'Report\Cover_Print::generate_pdf');

// ------------------------------------------------------------------
// PHASE 75 - PRIMARY BUSINESS WORKFLOW ROUTES (OPTION A: explicit GET)
// Restores reachability of the minimum primary post-login destinations.
// All target controllers self-protect via is_user_logged()/permission
// checks in their constructors. Dangerous/legacy endpoints (Test,
// Import restore/initialize, Export, Afterresult_model, staticreport,
// certificate calibration, and the broad report surface) are NOT routed.
// ------------------------------------------------------------------

// Post-login redirect targets
$routes->match(['get', 'post'], 'welcome/district_details', 'Welcome::district_details');
$routes->match(['get', 'post'], 'welcome/district_details/', 'Welcome::district_details');
$routes->match(['get', 'post'], 'welcome/sub_district_details', 'Welcome::sub_district_details');
$routes->match(['get', 'post'], 'welcome/sub_district_details/', 'Welcome::sub_district_details');
$routes->match(['get', 'post'], 'welcome/cluster_details', 'Welcome::cluster_details');
$routes->match(['get', 'post'], 'welcome/cluster_details/', 'Welcome::cluster_details');
$routes->match(['get', 'post'], 'welcome/cluster_schools', 'Welcome::cluster_schools');
$routes->match(['get', 'post'], 'welcome/cluster_schools/', 'Welcome::cluster_schools');
$routes->match(['get', 'post'], 'welcome/entry_confirm_status', 'Welcome::entry_confirm_status');
$routes->match(['get', 'post'], 'welcome/entry_confirm_status/', 'Welcome::entry_confirm_status');
$routes->match(['get', 'post'], 'welcome/nonclustdetails', 'Welcome::nonclustdetails');
$routes->match(['get', 'post'], 'welcome/nonclustdetails/', 'Welcome::nonclustdetails');
$routes->match(['get', 'post'], 'welcome/school_dashboard', 'Welcome::school_dashboard');
$routes->match(['get', 'post'], 'welcome/school_dashboard/', 'Welcome::school_dashboard');
$routes->match(['get', 'post'], 'schools/dashboard', 'Welcome::school_dashboard');
$routes->match(['get', 'post'], 'schools/dashboard/', 'Welcome::school_dashboard');
$routes->get('login/change_password', 'Login::change_password');
$routes->get('login/change_Pwd_insert', 'Login::change_password');
$routes->post('login/change_Pwd_insert', 'Login::change_Pwd_insert');

// Settings / Kalolsavam definition & APIs
$routes->match(['GET', 'POST'], 'admin/kalolsavam', 'Admin\Kalolsavam::index');
$routes->match(['GET', 'POST'], 'admin/kalolsavam/add_kalolsavam', 'Admin\Kalolsavam::add_kalolsavam');
$routes->match(['GET', 'POST'], 'admin/kalolsavam/edit_kalolsavam', 'Admin\Kalolsavam::edit_kalolsavam');
$routes->match(['GET', 'POST'], 'admin/kalolsavam/update_kalolsavam', 'Admin\Kalolsavam::update_kalolsavam');
$routes->match(['GET', 'POST'], 'admin/kalolsavam/get_kalolsavam_ajax', 'Admin\Kalolsavam::get_kalolsavam_ajax');
$routes->match(['GET', 'POST'], 'admin/kalolsavam/save_kalolsavam_ajax', 'Admin\Kalolsavam::save_kalolsavam_ajax');

// General Settings - per-district participant item selection limits
$routes->match(['GET', 'POST'], 'admin/general_settings', 'Admin\General_Settings::index');
$routes->match(['GET', 'POST'], 'admin/general_settings/update', 'Admin\General_Settings::update');
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
$routes->match(['GET', 'POST'], 'schools/registration/fetch_school_data_json', 'Schools\Registration::fetch_school_data_json');
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
$routes->post('schools/school_master/delete_school_details', 'Schools\School_Master::delete_school_details');
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
$routes->match(['get', 'post'], 'item/item_master/get_item_allotment_details', 'Item\Item_Master::get_item_allotment_details');

// User administration
$routes->match(['GET', 'POST'], 'ajax/loadajax/(:segment)', 'Ajax\Loadajax::$1');
$routes->match(['GET', 'POST'], 'ajax/loadajax', 'Ajax\Loadajax::index');
$routes->match(['GET', 'POST'], 'user/admin_users', 'User\Admin_Users::index');
$routes->post('user/admin_users/add_admin', 'User\Admin_Users::add_admin');
$routes->post('user/admin_users/edit_admin_detials', 'User\Admin_Users::edit_admin_detials');
$routes->post('user/admin_users/update_admin_detials', 'User\Admin_Users::update_admin_detials');
$routes->post('user/admin_users/delete_admin_detials', 'User\Admin_Users::delete_admin_detials');
$routes->post('user/admin_users/generate_sub_dist_admins', 'User\Admin_Users::generate_sub_dist_admins');
$routes->match(['GET', 'POST'], 'user/admin_users/sub_district_admin_list_pdf_creation', 'User\Admin_Users::sub_district_admin_list_pdf_creation');
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
$routes->post('user/user_cluster/save_user_cluster_ajax', 'User\User_Cluster::save_user_cluster_ajax');
$routes->post('user/user_cluster/bulk_add_all_schools_ajax', 'User\User_Cluster::bulk_add_all_schools_ajax');
$routes->post('user/user_cluster/bulk_delete_user_clusters_ajax', 'User\User_Cluster::bulk_delete_user_clusters_ajax');
$routes->match(['GET', 'POST'], 'user/user_cluster/bulk_delete_user_clusters', 'User\User_Cluster::bulk_delete_user_clusters');
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

$routes->match(['get', 'post'], 'stage/allotment', 'Stage\Allotment::index');
$routes->post('stage/allotment/save_allotment', 'Stage\Allotment::save_allotment');
$routes->post('stage/allotment/update_allotment', 'Stage\Allotment::update_allotment');
$routes->match(['get', 'post'], 'stage/allotment/delete_allotment/(:any)', 'Stage\Allotment::delete_allotment/$1');
$routes->match(['get', 'post'], 'stage/allotment/delete_allotment', 'Stage\Allotment::delete_allotment');
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
$routes->match(['get', 'post'], 'stage/festival_scheduling/ajax_duration_breakdown', 'Stage\Festival_Scheduling::ajax_duration_breakdown');
$routes->match(['get', 'post'], 'stage/festival_scheduling/ajax_participant_limits', 'Stage\Festival_Scheduling::ajax_participant_limits');
$routes->match(['get', 'post'], 'stage/festival_scheduling/ajax_timeline_data', 'Stage\Festival_Scheduling::ajax_timeline_data');
$routes->match(['get', 'post'], 'stage/festival_scheduling/ajax_get_versions', 'Stage\Festival_Scheduling::ajax_get_versions');
$routes->match(['get', 'post'], 'stage/festival_scheduling/ajax_restore_version', 'Stage\Festival_Scheduling::ajax_restore_version');
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

// Session-aware dispatch for participant card index (District vs Sub-District)
$isDistrictUserForCards = false;
try {
    $sessForCards = session();
    $uTypeCards = (int)($sessForCards->get('USER_TYPE') ?? 0);
    $uGroupCards = (string)($sessForCards->get('USER_GROUP') ?? '');
    $isDistrictUserForCards = ($uTypeCards === 2 || $uGroupCards === 'W');
} catch (\Throwable $e) {
    $isDistrictUserForCards = false;
}

if ($isDistrictUserForCards) {
    $routes->match(['GET', 'POST'], 'report/prereport/participant_cardindex', 'District\Report\Reports::participant_cardindex');
    $routes->match(['GET', 'POST'], 'report/prereport/participant_cardindex_photo', 'District\Report\Reports::participant_cardindex_photo');
} else {
    $routes->match(['GET', 'POST'], 'report/prereport/participant_cardindex', 'Report\Prereport::participant_cardindex');
    $routes->match(['GET', 'POST'], 'report/prereport/participant_cardindex_photo', 'Report\Prereport::participant_cardindex_photo');
}
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
$routes->match(['GET', 'POST'], 'report/prereport/rpt_tabulation_sheet1', 'Report\Prereport::rpt_tabulation_sheet1');
$routes->match(['GET', 'POST'], 'report/prereportpdf/list_school', 'Report\Prereportpdf::list_school');
$routes->match(['GET', 'POST'], 'report/prereportpdf/schoolfestall', 'Report\Prereportpdf::schoolfestall');
$routes->match(['GET', 'POST'], 'report/prereportpdf/list_participants', 'Report\Prereportpdf::list_participants');
$routes->match(['GET', 'POST'], 'report/prereportpdf/feedetails', 'Report\Prereportpdf::feedetails');
if ($isDistrictUserForCards) {
    $routes->match(['GET', 'POST'], 'report/prereportpdf/participant_cardindex', 'District\Report\Reports::participant_cardindex_pdf');
} else {
    $routes->match(['GET', 'POST'], 'report/prereportpdf/participant_cardindex', 'Report\Prereportpdf::participant_cardindex');
}
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
$routes->match(['GET', 'POST'], 'report/prereportpdf/list_school_with_team', 'Report\Prereportpdf::list_school_with_team');

$routes->get('report/prereportpdf/all_feedet_report_all', 'Report\Prereportpdf::all_feedet_report_all');
$routes->post('report/prereportpdf/clusterreport_report', 'Report\Prereportpdf::clusterreport_report');
$routes->post('report/prereportpdf/cluster_report_all_doc', 'Report\Prereportpdf::cluster_report_all_doc');
$routes->post('report/prereportpdf/more_limit_partlist', 'Report\Prereportpdf::more_limit_partlist');
$routes->match(['GET', 'POST'], 'report/prereportpdf/more_limit_partlist_detailed', 'Report\Prereportpdf::more_limit_partlist_detailed');
$routes->post('report/prereportpdf/list_participants_datewise', 'Report\Prereportpdf::list_participants_datewise');
if ($isDistrictUserForCards) {
    $routes->match(['GET', 'POST'], 'report/prereportpdf/participant_cardindex_school_photo', 'District\Report\Reports::participant_cardindex_photo_pdf');
} else {
    $routes->match(['GET', 'POST'], 'report/prereportpdf/participant_cardindex_school_photo', 'Report\Prereportpdf::participant_cardindex_school_photo');
}
$routes->match(['GET', 'POST'], 'report/prereportpdf/participant_cardindex_school2', 'Report\Prereportpdf::participant_cardindex_school2');
$routes->match(['GET', 'POST'], 'report/prereportpdf/participant_regnowithphoto', 'Report\Prereportpdf::participant_regnowithphoto');
$routes->match(['GET', 'POST'], 'report/prereportpdf/rpt_itemwiseparticipants', 'Report\Prereportpdf::rpt_itemwiseparticipants');
$routes->match(['GET', 'POST'], 'report/prereportpdf/fest_details', 'Report\Prereportpdf::fest_details');
$routes->match(['GET', 'POST'], 'report/prereportpdf/stagereport_abstract', 'Report\Prereportpdf::stagereport_abstract');
// Compatibility aliases for legacy view form targets that use abbreviated
if ($isDistrictUserForCards) {
    $routes->match(['GET', 'POST'], 'report/prereportpdf/participant_cardindex_photo', 'District\Report\Reports::participant_cardindex_photo_pdf');
} else {
    $routes->match(['GET', 'POST'], 'report/prereportpdf/participant_cardindex_photo', 'Report\Prereportpdf::participant_cardindex_school_photo');
}
$routes->match(['GET', 'POST'], 'report/prereportpdf/participant_cardindex2', 'Report\Prereportpdf::participant_cardindex_school2');
// Bulk print: participant ID cards for all schools (school-wise separation)
$routes->match(['GET', 'POST'], 'report/prereportpdf/participant_cardindex_bulk', 'Report\Prereportpdf::participant_cardindex_bulk');
$routes->match(['GET', 'POST'], 'report/prereportpdf/participant_cardindex_photo_bulk', 'Report\Prereportpdf::participant_cardindex_photo_bulk');

$routes->match(['GET', 'POST'], 'report/prereportpdf/team_manager_all', 'Report\Prereportpdf::team_manager_all');
$routes->match(['GET', 'POST'], 'test/nodata', 'Test::nodata');
$routes->match(['GET', 'POST'], 'test', 'Test::index');

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

// Result entry / publishing
$routes->match(['GET', 'POST'], 'result/resultentry', 'Result\Resultentry::index');
$routes->match(['GET', 'POST'], 'result/resultentry/(:num)', 'Result\Resultentry::index/$1');
$routes->match(['GET', 'POST'], 'result/resultentry/index', 'Result\Resultentry::index');
$routes->match(['GET', 'POST'], 'result/resultentry/index/(:any)', 'Result\Resultentry::index/$1');
$routes->match(['GET', 'POST'], 'result/resultentry/get_item_details_result', 'Result\Resultentry::get_item_details_result');
$routes->match(['GET', 'POST'], 'result/resultentry/save_result_entry', 'Result\Resultentry::save_result_entry');
$routes->match(['GET', 'POST'], 'result/resultentry/edit_result_entry', 'Result\Resultentry::edit_result_entry');
$routes->match(['GET', 'POST'], 'result/resultentry/edit_result_entry/(:any)', 'Result\Resultentry::edit_result_entry/$1');
$routes->match(['GET', 'POST'], 'result/resultentry/delete_result_entry', 'Result\Resultentry::delete_result_entry');
$routes->match(['GET', 'POST'], 'result/resultentry/delete_result_entry/(:any)', 'Result\Resultentry::delete_result_entry/$1');
$routes->match(['GET', 'POST'], 'result/resultentry/confirm_result_entry', 'Result\Resultentry::confirm_result_entry');
$routes->match(['GET', 'POST'], 'result/resultentry/printConfidentialReport', 'Result\Resultentry::printConfidentialReport');
$routes->match(['GET', 'POST'], 'result/resultentry/item_result_list', 'Result\Resultentry::item_result_list');
$routes->match(['GET', 'POST'], 'publishresult/resultindex/resultview', 'Publishresult\Resultindex::resultview');
$routes->match(['GET', 'POST'], 'publishresult/resultindex/resultview/(:any)', 'Publishresult\Resultindex::resultview');
$routes->match(['GET', 'POST'], 'publishresult/resultindex/result_declared', 'Publishresult\Resultindex::result_declared');
$routes->match(['GET', 'POST'], 'publishresult/resultindex/get_results_by_item', 'Publishresult\Resultindex::get_results_by_item');
$routes->match(['GET', 'POST'], 'publishresult/resultindex/schoolpoints', 'Publishresult\Resultindex::schoolpoints');
$routes->match(['GET', 'POST'], 'publishresult/resultindex/allresults', 'Publishresult\Resultindex::allresults');
$routes->match(['GET', 'POST'], 'publishresult/resultindex/allfestschools', 'Publishresult\Resultindex::allfestschools');
$routes->match(['GET', 'POST'], 'publishresult/resultindex/festval_status', 'Publishresult\Resultindex::festval_status');
$routes->match(['GET', 'POST'], 'publishresult/resultindex/itemwise_report', 'Publishresult\Resultindex::itemwise_report');

// Stage Operations & Manager Console Routes
$routes->match(['GET', 'POST'], 'stage/manager', 'Stage\Manager::index');
$routes->match(['GET', 'POST'], 'stage/manager/index', 'Stage\Manager::index');
$routes->match(['GET', 'POST'], 'stage/manager/index/(:any)', 'Stage\Manager::index/$1');
$routes->match(['GET', 'POST'], 'stage/manager/program/(:any)', 'Stage\Manager::program/$1');
$routes->post('stage/manager/start_item', 'Stage\Manager::start_item');
$routes->post('stage/manager/complete_item', 'Stage\Manager::complete_item');
$routes->post('stage/manager/pause_item', 'Stage\Manager::pause_item');
$routes->post('stage/manager/resume_item', 'Stage\Manager::resume_item');
$routes->post('stage/manager/save_lot_range', 'Stage\Manager::save_lot_range');
$routes->post('stage/manager/next_participant', 'Stage\Manager::next_participant');
$routes->get('stage/manager/get_live_status', 'Stage\Manager::get_live_status');
$routes->post('stage/manager/assign_manager', 'Stage\Manager::assign_manager');
$routes->post('stage/manager/toggle_manager_marks', 'Stage\Manager::toggle_manager_marks');
$routes->post('welcome/toggle_manager_marks', 'Welcome::toggle_manager_marks');

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
$routes->post('ajax/loadajax/get_school_details_small', 'Ajax\Loadajax::get_school_details_small');
$routes->match(['GET', 'POST'], 'ajax/loadajax/change_confirmation_status', 'Ajax\Loadajax::change_confirmation_status');
$routes->match(['GET', 'POST'], 'ajax/loadajax/change_confirmation_status/', 'Ajax\Loadajax::change_confirmation_status');
$routes->match(['GET', 'POST'], 'ajax/change_confirmation_status', 'Ajax\Loadajax::change_confirmation_status');
$routes->match(['GET', 'POST'], 'ajax/change_confirmation_status/', 'Ajax\Loadajax::change_confirmation_status');
$routes->match(['GET', 'POST'], 'ajax/loadajax/reset_result_confirmation_status', 'Ajax\Loadajax::reset_result_confirmation_status');
$routes->match(['GET', 'POST'], 'ajax/reset_result_confirmation_status', 'Ajax\Loadajax::reset_result_confirmation_status');
$routes->match(['GET', 'POST'], 'ajax/loadajax/bulk_change_confirmation_status', 'Ajax\Loadajax::bulk_change_confirmation_status');
$routes->match(['GET', 'POST'], 'ajax/bulk_change_confirmation_status', 'Ajax\Loadajax::bulk_change_confirmation_status');
$routes->match(['GET', 'POST'], 'ajax/loadajax/bulk_change_sub_district_confirmation_status', 'Ajax\Loadajax::bulk_change_sub_district_confirmation_status');
$routes->match(['GET', 'POST'], 'ajax/bulk_change_sub_district_confirmation_status', 'Ajax\Loadajax::bulk_change_sub_district_confirmation_status');

// ------------------------------------------------------------------
// PHASE 101 - Stage Allotment item-details AJAX endpoint (POST only)
// Used by js/stages.js fetch_item_details() after item code entry.
// Allotment constructor enforces login + permission; method is SELECT-only.
// Do NOT add GET or wildcard routes here.
// ------------------------------------------------------------------
$routes->post('stage/allotment/get_item_details', 'Stage\Allotment::get_item_details');

// ------------------------------------------------------------------
// Import module routes (Import controller)
// import_data: Group A (USER_TYPE 2|3) | W — Sub-District import & qualification
// backup_data: Full .gz database backup download
// backup_data_sql: Group A|W — SQL backup with file listing
// restore_database: Group W — SQL restore form + upload
// All permission checks enforced inside controller methods.
// ------------------------------------------------------------------
$routes->match(['get', 'post'], 'import/import_data', 'Import::import_data');
$routes->post('import/import_data/preview', 'Import::preview');
$routes->post('import/import_data/confirm', 'Import::confirm');
$routes->match(['get', 'post'], 'import/import_data/download_error_csv', 'Import::download_error_csv');
$routes->get('import/backup_data', 'Import::backup_data');
$routes->get('import/backup_data_sql', 'Import::backup_data_sql');
$routes->match(['get', 'post'], 'import/restore_database', 'Import::restore_database');
$routes->match(['get', 'post'], 'import/clear_database', 'Import::clear_database');

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

// ------------------------------------------------------------------
// AFTER-RESULT REPORT MODULE — Interfaces + PDF generators
// higherlevel_result: Shows participants eligible for higher-level
//   competitions (class > 4, grade A). Form POSTs to PDF endpoint.
//   Uses Afterresult_model::higherlevel_result() which queries
//   sd_result_publish JOIN school_master, sd_participant_details, item_master,
//   festival_master, sd_result_master.
// schoolhigher_result / schoolhigher_resultall: School-wise higher-level.
// All methods enforce login + permission 8.
// ------------------------------------------------------------------

// Afterresultreport (Interface pages — GET shows form, POST not used directly)
$routes->match(['GET', 'POST'], 'report/afterresultreport/higherlevel_result', 'Report\Afterresultreport::higherlevel_result');
$routes->match(['GET', 'POST'], 'report/afterresultreport/school_wise_result_interface', 'Report\Afterresultreport::school_wise_result_interface');
$routes->match(['GET', 'POST'], 'report/afterresultreport/item_code_reports', 'Report\Afterresultreport::item_code_reports');
$routes->match(['GET', 'POST'], 'report/afterresultreport/status_of_kalolsavam_interface', 'Report\Afterresultreport::status_of_kalolsavam_interface');
$routes->match(['GET', 'POST'], 'report/afterresultreport/get_live_status', 'Report\Afterresultreport::get_live_status');
$routes->match(['GET', 'POST'], 'report/afterresultreport/timefest_result', 'Report\Afterresultreport::timefest_result');
$routes->match(['GET', 'POST'], 'report/afterresultreport/rankwise_participant_result', 'Report\Afterresultreport::rankwise_participant_result');

// Afterresultreportpdf (PDF generation — POST from interface forms)
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/higherlevel_result', 'Report\Afterresultreportpdf::higherlevel_result');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/schoolhigher_result', 'Report\Afterresultreportpdf::schoolhigher_result');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/schoolhigher_resultall', 'Report\Afterresultreportpdf::schoolhigher_resultall');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/school_wise_result', 'Report\Afterresultreportpdf::school_wise_result');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/school_wise_result_all', 'Report\Afterresultreportpdf::school_wise_result_all');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/allschool_points_result', 'Report\Afterresultreportpdf::allschool_points_result');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/item_code_reports', 'Report\Afterresultreportpdf::item_code_reports');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/total_points', 'Report\Afterresultreportpdf::total_points');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/report_press', 'Report\Afterresultreportpdf::report_press');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/time_wise_result_report', 'Report\Afterresultreportpdf::time_wise_result_report');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/numwise_timeresult_report', 'Report\Afterresultreportpdf::numwise_timeresult_report');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/status_of_kalolsavam', 'Report\Afterresultreportpdf::status_of_kalolsvam');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/status_of_kalolsvam', 'Report\Afterresultreportpdf::status_of_kalolsvam');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/item_wise_result', 'Report\Resultindex::itemwise_report');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/status_of_kalolsavam1', 'Report\Afterresultreportpdf::status_of_kalolsavam1');
$routes->match(['GET', 'POST'], 'report/afterresultreportpdf/datewisepart', 'Report\Afterresultreportpdf::datewisepart');

// ------------------------------------------------------------------
// EXPORT MODULE — District & Sub-District Data Export
// export/export_district_data: GET renders export form; POST with
//   hidExport=TRUE streams CSV via Export_Model::get_district_export_data().
//   Queries sd_result_publish JOIN sd_result_master, sd_participant_item_details,
//   sd_participant_details (grade A, class > 4). Encrypted row integrity.
// export/export_sub_district_data: POST-only CSV stream for sub-district.
//   Includes school_master, school_details, sd_participant_details,
//   sd_participant_item_details with encrypted checksums.
// welcome/export_photo: ZIP photo export.
// All methods enforce login + permission 8 in the Export controller.
// ------------------------------------------------------------------
$routes->match(['GET', 'POST'], 'export', 'Export::index');
$routes->match(['GET', 'POST'], 'export/export_district_data', 'Export::export_district_data');
$routes->match(['GET', 'POST'], 'export/export_district_4table_csv', 'Export::export_district_4table_csv');
$routes->post('export/export_sub_district_data', 'Export::export_sub_district_data');
$routes->match(['GET', 'POST'], 'export/export_district_winners_csv', 'Export::export_district_winners_csv');
$routes->match(['GET', 'POST'], 'export/export_winner_photos_zip', 'Export::export_winner_photos_zip');
$routes->match(['GET', 'POST'], 'export/export_winner_package', 'Export::export_winner_package');
$routes->match(['GET', 'POST'], 'welcome/export_photo', 'Welcome::export_photo');

// ------------------------------------------------------------------
// PHASE 4 - DISTRICT WINNER IMPORT & PROMOTION ENGINE
// ------------------------------------------------------------------
$routes->match(['GET', 'POST'], 'district/import', 'District\Import\Import::index');
$routes->match(['GET', 'POST'], 'district/import/index', 'District\Import\Import::index');
$routes->match(['GET', 'POST'], 'district/import/import', 'District\Import\Import::index');
$routes->post('district/import/preview', 'District\Import\Import::preview');
$routes->post('district/import/confirm', 'District\Import\Import::confirm');
$routes->match(['GET', 'POST'], 'district/import/download_error_csv', 'District\Import\Import::download_error_csv');


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
$routes->match(['GET', 'POST'], 'report/resultindex/resultview/(:any)', 'Report\Resultindex::resultview');
$routes->match(['GET', 'POST'], 'report/resultindex/result_declared', 'Report\Resultindex::result_declared');
$routes->match(['GET', 'POST'], 'report/resultindex/get_results_by_item', 'Report\Resultindex::get_results_by_item');
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

// ------------------------------------------------------------------
// CHITTUR ITEM CODES - District/Taluk filtered item codes
// ------------------------------------------------------------------
$routes->get('chittur', 'Chittur::index');
$routes->get('chittur/getDistricts', 'Chittur::getDistricts');
$routes->get('chittur/getTaluks', 'Chittur::getTaluks');
$routes->get('chittur/getItemCodes', 'Chittur::getItemCodes');

// ------------------------------------------------------------------
// USER CLUSTER ENDPOINTS
// ------------------------------------------------------------------
$routes->match(['GET', 'POST'], 'user/user_cluster', 'User\User_Cluster::index');
$routes->match(['GET', 'POST'], 'user/user_cluster/add_form', 'User\User_Cluster::add_form');
$routes->match(['GET', 'POST'], 'user/user_cluster/save_user_cluster', 'User\User_Cluster::save_user_cluster');
$routes->match(['GET', 'POST'], 'user/user_cluster/edit_user_cluster', 'User\User_Cluster::edit_user_cluster');
$routes->match(['GET', 'POST'], 'user/user_cluster/update_user_cluster', 'User\User_Cluster::update_user_cluster');
$routes->match(['GET', 'POST'], 'user/user_cluster/delete_user_cluster', 'User\User_Cluster::delete_user_cluster');
$routes->post('user/user_cluster/save_user_cluster_ajax', 'User\User_Cluster::save_user_cluster_ajax');
$routes->post('user/user_cluster/bulk_add_all_schools_ajax', 'User\User_Cluster::bulk_add_all_schools_ajax');
$routes->post('user/user_cluster/bulk_delete_user_clusters_ajax', 'User\User_Cluster::bulk_delete_user_clusters_ajax');
$routes->match(['GET', 'POST'], 'user/user_cluster/bulk_delete_user_clusters', 'User\User_Cluster::bulk_delete_user_clusters');

// ------------------------------------------------------------------
// DISTRICT KALOLSAVAM OPERATIONAL ROUTES
// ------------------------------------------------------------------

// 0. District School Entry & Registration
$routes->match(['GET', 'POST'], 'district/schools/registration', 'District\Schools\Registration::index');
$routes->match(['GET', 'POST'], 'district/schools/school_entry', 'District\Schools\Registration::school_entry');
$routes->match(['GET', 'POST'], 'district/registration', 'District\Schools\Registration::index');
$routes->match(['GET', 'POST'], 'district/school_entry', 'District\Schools\Registration::school_entry');
$routes->match(['GET', 'POST'], 'district/schools/school_entry/toggle_school_registration', 'District\Schools\Registration::toggle_school_registration');
$routes->match(['GET', 'POST'], 'district/schools/registration/toggle_school_registration', 'District\Schools\Registration::toggle_school_registration');

// District Schools AJAX endpoints
$routes->match(['GET', 'POST'], 'district/schools/registration/save_participant', 'District\Schools\Registration::save_participant');
$routes->match(['GET', 'POST'], 'district/schools/registration/edit_participant_details', 'District\Schools\Registration::edit_participant_details');
$routes->match(['GET', 'POST'], 'district/schools/registration/update_participant_details', 'District\Schools\Registration::update_participant_details');
$routes->match(['GET', 'POST'], 'district/schools/registration/delete_participant_details', 'District\Schools\Registration::delete_participant_details');
$routes->match(['GET', 'POST'], 'district/schools/registration/get_participant_data', 'District\Schools\Registration::get_participant_data');
$routes->match(['GET', 'POST'], 'district/schools/registration/get_school_details', 'District\Schools\Registration::get_school_details');
$routes->match(['GET', 'POST'], 'district/schools/registration/get_school_list', 'District\Schools\Registration::get_school_list');
$routes->match(['GET', 'POST'], 'district/schools/registration/get_school_data', 'District\Schools\Registration::get_school_data');
$routes->match(['GET', 'POST'], 'district/schools/registration/get_items_by_class', 'District\Schools\Registration::get_items_by_class');
$routes->match(['GET', 'POST'], 'district/schools/registration/fetch_school_data_json', 'District\Schools\Registration::fetch_school_data_json');
$routes->match(['GET', 'POST'], 'district/schools/registration/get_item_details', 'District\Schools\Registration::get_item_details');
$routes->match(['GET', 'POST'], 'district/schools/registration/edit_school_details', 'District\Schools\Registration::edit_school_details');
$routes->match(['GET', 'POST'], 'district/schools/registration/update', 'District\Schools\Registration::update');
$routes->match(['GET', 'POST'], 'district/schools/registration/finalize_data', 'District\Schools\Registration::finalize_data');
$routes->match(['GET', 'POST'], 'district/schools/registration/update_group_captain', 'District\Schools\Registration::update_group_captain');

// Same for district/schools/school_entry/*
$routes->match(['GET', 'POST'], 'district/schools/school_entry/save_participant', 'District\Schools\Registration::save_participant');
$routes->match(['GET', 'POST'], 'district/schools/school_entry/edit_participant_details', 'District\Schools\Registration::edit_participant_details');
$routes->match(['GET', 'POST'], 'district/schools/school_entry/update_participant_details', 'District\Schools\Registration::update_participant_details');
$routes->match(['GET', 'POST'], 'district/schools/school_entry/delete_participant_details', 'District\Schools\Registration::delete_participant_details');
$routes->match(['GET', 'POST'], 'district/schools/school_entry/get_participant_data', 'District\Schools\Registration::get_participant_data');
$routes->match(['GET', 'POST'], 'district/schools/school_entry/get_school_details', 'District\Schools\Registration::get_school_details');
$routes->match(['GET', 'POST'], 'district/schools/school_entry/get_school_list', 'District\Schools\Registration::get_school_list');
$routes->match(['GET', 'POST'], 'district/schools/school_entry/get_school_data', 'District\Schools\Registration::get_school_data');
$routes->match(['GET', 'POST'], 'district/schools/school_entry/get_items_by_class', 'District\Schools\Registration::get_items_by_class');
$routes->match(['GET', 'POST'], 'district/schools/school_entry/get_item_details', 'District\Schools\Registration::get_item_details');
$routes->match(['GET', 'POST'], 'district/schools/school_entry/edit_school_details', 'District\Schools\Registration::edit_school_details');
$routes->match(['GET', 'POST'], 'district/schools/school_entry/update', 'District\Schools\Registration::update');
$routes->match(['GET', 'POST'], 'district/schools/school_entry/finalize_data', 'District\Schools\Registration::finalize_data');
$routes->match(['GET', 'POST'], 'district/schools/school_entry/update_group_captain', 'District\Schools\Registration::update_group_captain');

// 1. District Stages
$routes->match(['GET', 'POST'], 'district/stage', 'District\Stage\Stage::index');
$routes->match(['GET', 'POST'], 'district/stage/index', 'District\Stage\Stage::index');
$routes->match(['GET', 'POST'], 'district/stage/stage', 'District\Stage\Stage::index');
$routes->post('district/stage/add_stage', 'District\Stage\Stage::add_stage');
$routes->post('district/stage/update_stage', 'District\Stage\Stage::update_stage');
$routes->post('district/stage/delete_stage', 'District\Stage\Stage::delete_stage');

// 2. District Scheduling & Clash Detection
$routes->match(['GET', 'POST'], 'district/scheduling', 'District\Stage\Scheduling::index');
$routes->match(['GET', 'POST'], 'district/scheduling/index', 'District\Stage\Scheduling::index');
$routes->match(['GET', 'POST'], 'district/stage/scheduling', 'District\Stage\Scheduling::index');
$routes->match(['GET', 'POST'], 'district/scheduling/clashes', 'District\Stage\Scheduling::clashes');
$routes->match(['GET', 'POST'], 'district/stage/clashes', 'District\Stage\Scheduling::clashes');
$routes->post('district/scheduling/save_allotment', 'District\Stage\Scheduling::save_allotment');
$routes->post('district/stage/save_allotment', 'District\Stage\Scheduling::save_allotment');
$routes->post('district/scheduling/generate_chest_numbers', 'District\Stage\Scheduling::generate_chest_numbers');
$routes->post('district/stage/generate_chest_numbers', 'District\Stage\Scheduling::generate_chest_numbers');

// 3. District Result Entry & Evaluation
$routes->match(['GET', 'POST'], 'district/resultentry', 'District\Result\Resultentry::index');
$routes->match(['GET', 'POST'], 'district/resultentry/(:num)', 'District\Result\Resultentry::index/$1');
$routes->match(['GET', 'POST'], 'district/resultentry/index', 'District\Result\Resultentry::index');
$routes->match(['GET', 'POST'], 'district/resultentry/index/(:any)', 'District\Result\Resultentry::index/$1');
$routes->match(['GET', 'POST'], 'district/result/resultentry', 'District\Result\Resultentry::index');
$routes->match(['GET', 'POST'], 'district/result/resultentry/index', 'District\Result\Resultentry::index');
$routes->match(['GET', 'POST'], 'district/result/resultentry/index/(:any)', 'District\Result\Resultentry::index/$1');
$routes->match(['GET', 'POST'], 'district/resultentry/get_item_details_result', 'District\Result\Resultentry::get_item_details_result');
$routes->match(['GET', 'POST'], 'district/result/get_item_details_result', 'District\Result\Resultentry::get_item_details_result');
$routes->match(['GET', 'POST'], 'district/resultentry/save_result_entry', 'District\Result\Resultentry::save_result_entry');
$routes->match(['GET', 'POST'], 'district/result/save_result_entry', 'District\Result\Resultentry::save_result_entry');
$routes->match(['GET', 'POST'], 'district/resultentry/edit_result_entry', 'District\Result\Resultentry::edit_result_entry');
$routes->match(['GET', 'POST'], 'district/resultentry/edit_result_entry/(:any)', 'District\Result\Resultentry::edit_result_entry/$1');
$routes->match(['GET', 'POST'], 'district/result/edit_result_entry', 'District\Result\Resultentry::edit_result_entry');
$routes->match(['GET', 'POST'], 'district/resultentry/delete_result_entry', 'District\Result\Resultentry::delete_result_entry');
$routes->match(['GET', 'POST'], 'district/resultentry/delete_result_entry/(:any)', 'District\Result\Resultentry::delete_result_entry/$1');
$routes->match(['GET', 'POST'], 'district/result/delete_result_entry', 'District\Result\Resultentry::delete_result_entry');
$routes->match(['GET', 'POST'], 'district/resultentry/confirm_result_entry', 'District\Result\Resultentry::confirm_result_entry');
$routes->match(['GET', 'POST'], 'district/result/confirm_result_entry', 'District\Result\Resultentry::confirm_result_entry');
$routes->match(['GET', 'POST'], 'district/resultentry/printConfidentialReport', 'District\Result\Resultentry::printConfidentialReport');
$routes->match(['GET', 'POST'], 'district/result/printConfidentialReport', 'District\Result\Resultentry::printConfidentialReport');
$routes->match(['GET', 'POST'], 'district/resultentry/item_result_list', 'District\Result\Resultentry::item_result_list');
$routes->match(['GET', 'POST'], 'district/result/item_result_list', 'District\Result\Resultentry::item_result_list');
$routes->post('district/resultentry/save_mark', 'District\Result\Resultentry::save_mark');
$routes->post('district/result/save_mark', 'District\Result\Resultentry::save_mark');
$routes->post('district/resultentry/confirm_result', 'District\Result\Resultentry::confirm_result');
$routes->post('district/result/confirm_result', 'District\Result\Resultentry::confirm_result');
$routes->post('district/resultentry/toggle_withhold', 'District\Result\Resultentry::toggle_withhold');
$routes->post('district/result/toggle_withhold', 'District\Result\Resultentry::toggle_withhold');

// 4. District Dual Championship & Reports
$routes->match(['GET', 'POST'], 'district/reports', 'District\Report\Reports::index');
$routes->match(['GET', 'POST'], 'district/reports/index', 'District\Report\Reports::index');
$routes->match(['GET', 'POST'], 'district/report/reports', 'District\Report\Reports::index');
$routes->match(['GET', 'POST'], 'district/reports/participant_cardindex', 'District\Report\Reports::participant_cardindex');
$routes->match(['GET', 'POST'], 'district/reports/participant_cardindex_photo', 'District\Report\Reports::participant_cardindex_photo');
$routes->match(['GET', 'POST'], 'district/reports/participant_cardindex_pdf', 'District\Report\Reports::participant_cardindex_pdf');
$routes->match(['GET', 'POST'], 'district/reports/participant_cardindex_photo_pdf', 'District\Report\Reports::participant_cardindex_photo_pdf');
$routes->match(['GET', 'POST'], 'district/reports/ajax_get_schools_by_subdistrict', 'District\Report\Reports::ajax_get_schools_by_subdistrict');

// 5. District Certificates
$routes->match(['GET', 'POST'], 'district/certificate', 'District\Certificate\Certificate::index');
$routes->match(['GET', 'POST'], 'district/certificate/index', 'District\Certificate\Certificate::index');
$routes->match(['GET', 'POST'], 'district/certificate/certificate', 'District\Certificate\Certificate::index');
$routes->post('district/certificate/save_template', 'District\Certificate\Certificate::save_template');
$routes->match(['GET', 'POST'], 'district/certificate/print_list', 'District\Certificate\Certificate::print_list');
$routes->match(['GET', 'POST'], 'district/certificate/certificate/print_list', 'District\Certificate\Certificate::print_list');

// 6. District Pre-Fest Report Interfaces
$routes->match(['GET', 'POST'], 'district/report/prereport/list_school', 'District\Report\Prereport::list_school');
$routes->match(['GET', 'POST'], 'district/report/prereport/list_participants', 'District\Report\Prereport::list_participants');
$routes->match(['GET', 'POST'], 'district/report/prereport/team_manager_all', 'District\Report\Prereport::team_manager_all');
$routes->match(['GET', 'POST'], 'district/report/prereport/feedetails', 'District\Report\Prereport::feedetails');
$routes->match(['GET', 'POST'], 'district/report/prereport/allinone', 'District\Report\Prereport::four_reports');
$routes->match(['GET', 'POST'], 'district/report/prereport/four_reports', 'District\Report\Prereport::four_reports');
$routes->match(['GET', 'POST'], 'district/report/prereport/callsheet_first', 'District\Report\Prereport::callsheet_first');
$routes->match(['GET', 'POST'], 'district/report/prereport/callsheet_all', 'District\Report\Prereport::callsheet_all');
$routes->match(['GET', 'POST'], 'district/report/prereport/participant_cardindex2', 'District\Report\Prereport::participant_cardindex2');
$routes->match(['GET', 'POST'], 'district/report/prereport/participant_regno', 'District\Report\Prereport::participant_regno');
$routes->match(['GET', 'POST'], 'district/report/prereport/participant_more_item', 'District\Report\Prereport::participant_more_item');
$routes->match(['GET', 'POST'], 'district/report/prereport/participant_limit_item_more', 'District\Report\Prereport::participant_limit_item_more');
$routes->match(['GET', 'POST'], 'district/report/prereport/clustor_report', 'District\Report\Prereport::clustor_report');
$routes->match(['GET', 'POST'], 'district/report/prereport/clustor_report_all', 'District\Report\Prereport::clustor_report_all');
$routes->match(['GET', 'POST'], 'district/report/prereport/score_sheet', 'District\Report\Prereport::score_sheet_interfaces');
$routes->match(['GET', 'POST'], 'district/report/prereport/score_sheet_interfaces', 'District\Report\Prereport::score_sheet_interfaces');
$routes->match(['GET', 'POST'], 'district/report/prereport/fest_details', 'District\Report\Prereport::fest_details');
$routes->match(['GET', 'POST'], 'district/report/prereport/tabulation', 'District\Report\Prereport::tabulation_report_interface');
$routes->match(['GET', 'POST'], 'district/report/prereport/tabulation_report_interface', 'District\Report\Prereport::tabulation_report_interface');
$routes->match(['GET', 'POST'], 'district/report/prereport/rpt_tabulation_sheet', 'District\Report\Prereport::rpt_tabulation_sheet');
$routes->match(['GET', 'POST'], 'district/report/prereport/tabulation_report_interface1', 'District\Report\Prereport::tabulation_report_interface1');
$routes->match(['GET', 'POST'], 'district/report/prereport/rpt_tabulation_sheet1', 'District\Report\Prereport::rpt_tabulation_sheet1');
$routes->match(['GET', 'POST'], 'district/report/prereport/time_sheet', 'District\Report\Prereport::timesheetinterface');
$routes->match(['GET', 'POST'], 'district/report/prereport/timesheetinterface', 'District\Report\Prereport::timesheetinterface');
$routes->match(['GET', 'POST'], 'district/report/prereport/datewisepart', 'District\Report\Prereport::datewisepart');
$routes->match(['GET', 'POST'], 'district/report/prereport/stagereportdate', 'District\Report\Prereport::stagereportdate');
$routes->match(['GET', 'POST'], 'district/report/prereport/stagereport_all', 'District\Report\Prereport::stagereport_all');
$routes->match(['GET', 'POST'], 'district/report/prereport/score_sheet_fest', 'District\Report\Prereport::score_sheet_fest');
$routes->match(['GET', 'POST'], 'district/report/prereport/itemwise_report_interface', 'District\Report\Prereport::itemwise_report_interface');
$routes->match(['GET', 'POST'], 'district/report/prereport/stageallot_duration', 'District\Report\Prereport::stageallot_duration');
$routes->match(['GET', 'POST'], 'district/report/prereport/appealed_part', 'District\Report\Prereport::appealed_part');
$routes->match(['GET', 'POST'], 'district/report/prereport/appeal_courtorder', 'District\Report\Prereport::appeal_courtorder');
$routes->match(['GET', 'POST'], 'district/report/prereport/clashes_details_interface', 'District\Report\Prereport::clashes_details_interface');
$routes->match(['GET', 'POST'], 'district/report/prereport/pdf_report', 'District\Report\Prereportpdf::four_reports');

// 7. District Pre-Fest Report PDFs
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/list_school', 'District\Report\Prereportpdf::list_school');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/list_school_with_team', 'District\Report\Prereportpdf::list_school_with_team');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/schoolfestall', 'District\Report\Prereportpdf::schoolfestall');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/list_participants', 'District\Report\Prereportpdf::list_participants');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/time_date_stage', 'District\Report\Prereportpdf::time_date_stage');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/timesheet_date', 'District\Report\Prereportpdf::timesheet_date');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/list_participants_datewise', 'District\Report\Prereportpdf::list_participants_datewise');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/list_participants_stagewise', 'District\Report\Prereportpdf::list_participants_stagewise');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/team_manager_all', 'District\Report\Prereportpdf::team_manager_all');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/feedetails', 'District\Report\Prereportpdf::feedetails');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/all_feedet_report_all', 'District\Report\Prereportpdf::all_feedet_report_all');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/callsheet_report', 'District\Report\Prereportpdf::callsheet_report');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/callsheet_all', 'District\Report\Prereportpdf::callsheet_all');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/tabulation_date_stage', 'District\Report\Prereportpdf::tabulation_date_stage');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/score_date_stage', 'District\Report\Prereportpdf::score_date_stage');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/call_date_stage', 'District\Report\Prereportpdf::call_date_stage');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/participant_cardindex', 'District\Report\Prereportpdf::participant_cardindex');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/participant_cardindex_school_photo', 'District\Report\Prereportpdf::participant_cardindex_school_photo');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/participant_cardindex_photo', 'District\Report\Reports::participant_cardindex_photo_pdf');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/participant_regno_photo', 'District\Report\Prereportpdf::participant_regno_photo');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/participant_cardindex_school2', 'District\Report\Prereportpdf::participant_cardindex_school2');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/participant_regno', 'District\Report\Prereportpdf::participant_regno');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/participant_regnowithphoto', 'District\Report\Prereportpdf::participant_regnowithphoto');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/participant_more_item', 'District\Report\Prereportpdf::participant_more_item');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/more_limit_partlist', 'District\Report\Prereportpdf::more_limit_partlist');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/more_limit_partlist_detailed', 'District\Report\Prereportpdf::more_limit_partlist_detailed');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/eligible_school', 'District\Report\Prereportpdf::eligible_school');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/clusterreport_report', 'District\Report\Prereportpdf::clusterreport_report');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/cluster_report_all', 'District\Report\Prereportpdf::cluster_report_all');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/cluster_report_all_doc', 'District\Report\Prereportpdf::cluster_report_all_doc');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/cluster_date_stage', 'District\Report\Prereportpdf::cluster_date_stage');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/rpt_item_with_item_code', 'District\Report\Prereportpdf::rpt_item_with_item_code');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/print_score_sheet', 'District\Report\Prereportpdf::print_score_sheet');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/fest_details', 'District\Report\Prereportpdf::fest_details');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/fest_scoresheet_details', 'District\Report\Prereportpdf::fest_scoresheet_details');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/timesheet', 'District\Report\Prereportpdf::timesheet');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/datewisepart', 'District\Report\Prereportpdf::datewisepart');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/stagereportdate', 'District\Report\Prereportpdf::stagereportdate');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/stagereport_all', 'District\Report\Prereportpdf::stagereport_all');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/rpt_itemwiseparticipants', 'District\Report\Prereportpdf::rpt_itemwiseparticipants');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/stageallot_duration', 'District\Report\Prereportpdf::stageallot_duration');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/clashes_details', 'District\Report\Prereportpdf::clashes_details');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/appealed_part', 'District\Report\Prereportpdf::appealed_part');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/appeal_courtorder', 'District\Report\Prereportpdf::appeal_courtorder');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/no_value', 'District\Report\Prereportpdf::no_value');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/stagereport_abstract', 'District\Report\Prereportpdf::stagereport_abstract');
$routes->match(['GET', 'POST'], 'district/report/prereportpdf/four_reports', 'District\Report\Prereportpdf::four_reports');

// 8. District Result Index (Publish Result)
$routes->match(['GET', 'POST'], 'district/report/resultindex/resultview', 'District\Report\Resultindex::resultview');
$routes->match(['GET', 'POST'], 'district/report/resultindex/result_declared', 'District\Report\Resultindex::result_declared');
$routes->match(['GET', 'POST'], 'district/report/resultindex/get_results_by_item', 'District\Report\Resultindex::get_results_by_item');
$routes->match(['GET', 'POST'], 'district/report/resultindex/schoolpoints', 'District\Report\Resultindex::schoolpoints');
$routes->match(['GET', 'POST'], 'district/report/resultindex/allresults', 'District\Report\Resultindex::allresults');
$routes->match(['GET', 'POST'], 'district/report/resultindex/festval_status', 'District\Report\Resultindex::festval_status');
$routes->match(['GET', 'POST'], 'district/report/resultindex/festival_allitem', 'District\Report\Resultindex::festival_allitem');
$routes->match(['GET', 'POST'], 'district/report/resultindex/finished_item', 'District\Report\Resultindex::finished_item');
$routes->match(['GET', 'POST'], 'district/report/resultindex/incomplete_item', 'District\Report\Resultindex::incomplete_item');
$routes->match(['GET', 'POST'], 'district/report/resultindex/gradewise_interface', 'District\Report\Resultindex::gradewise_interface');
$routes->match(['GET', 'POST'], 'district/report/resultindex/gradewise_report', 'District\Report\Resultindex::gradewise_report');
$routes->match(['GET', 'POST'], 'district/report/resultindex/itemwise_result', 'District\Report\Resultindex::itemwise_result');
$routes->match(['GET', 'POST'], 'district/report/resultindex/itemwise_report', 'District\Report\Resultindex::itemwise_report');
$routes->match(['GET', 'POST'], 'district/report/resultindex/rankwise_result', 'District\Report\Resultindex::rankwise_result');
$routes->match(['GET', 'POST'], 'district/report/resultindex/rankwise_report', 'District\Report\Resultindex::rankwise_report');
$routes->match(['GET', 'POST'], 'district/report/resultindex/gradewise_result', 'District\Report\Resultindex::gradewise_result');
$routes->match(['GET', 'POST'], 'district/report/resultindex/grade_report', 'District\Report\Resultindex::grade_report');
$routes->match(['GET', 'POST'], 'district/report/resultindex/allfestschools', 'District\Report\Resultindex::allfestschools');
$routes->match(['GET', 'POST'], 'district/report/resultindex/report_press_timewise', 'District\Report\Resultindex::report_press_timewise');

// 9. District After-Result Report Interfaces
$routes->match(['GET', 'POST'], 'district/report/afterresult_report/higherlevel_result', 'District\Report\Afterresultreport::higherlevel_result');
$routes->match(['GET', 'POST'], 'district/report/afterresult_report/timefest_result', 'District\Report\Afterresultreport::timefest_result');
$routes->match(['GET', 'POST'], 'district/report/afterresult_report/school_wise_result_interface', 'District\Report\Afterresultreport::school_wise_result_interface');
$routes->match(['GET', 'POST'], 'district/report/afterresult_report/lookup_code', 'District\Report\Afterresultreport::lookup_code');
$routes->match(['GET', 'POST'], 'district/report/afterresult_report/rankwise_participant_result', 'District\Report\Afterresultreport::rankwise_participant_result');
$routes->match(['GET', 'POST'], 'district/report/afterresult_report/status_of_kalolsavam_interface', 'District\Report\Afterresultreport::status_of_kalolsavam_interface');
$routes->match(['GET', 'POST'], 'district/report/afterresult_report/get_live_status', 'District\Report\Afterresultreport::get_live_status');
$routes->match(['GET', 'POST'], 'district/report/afterresult_report/item_code_reports', 'District\Report\Afterresultreport::item_code_reports');

// 10. District After-Result Report PDFs
$routes->match(['GET', 'POST'], 'district/report/afterresult_reportpdf/school_wise_result', 'District\Report\Afterresultreportpdf::school_wise_result');
$routes->match(['GET', 'POST'], 'district/report/afterresult_reportpdf/school_wise_result_all', 'District\Report\Afterresultreportpdf::school_wise_result_all');
$routes->match(['GET', 'POST'], 'district/report/afterresult_reportpdf/status_of_kalolsvam', 'District\Report\Afterresultreportpdf::status_of_kalolsvam');
$routes->match(['GET', 'POST'], 'district/report/afterresult_reportpdf/status_of_kalolsavam', 'District\Report\Afterresultreportpdf::status_of_kalolsvam');
$routes->match(['GET', 'POST'], 'district/report/afterresult_reportpdf/status_of_kalolsavam1', 'District\Report\Afterresultreportpdf::status_of_kalolsavam1');
$routes->match(['GET', 'POST'], 'district/report/afterresult_reportpdf/datewisepart', 'District\Report\Afterresultreportpdf::datewisepart');
$routes->match(['GET', 'POST'], 'district/report/afterresult_reportpdf/total_points', 'District\Report\Afterresultreportpdf::total_points');
$routes->match(['GET', 'POST'], 'district/report/afterresult_reportpdf/report_press', 'District\Report\Afterresultreportpdf::report_press');
$routes->match(['GET', 'POST'], 'district/report/afterresult_reportpdf/allschool_points_result', 'District\Report\Afterresultreportpdf::allschool_points_result');
$routes->match(['GET', 'POST'], 'district/report/afterresult_reportpdf/higherlevel_result', 'District\Report\Afterresultreportpdf::higherlevel_result');
$routes->match(['GET', 'POST'], 'district/report/afterresult_reportpdf/schoolhigher_result', 'District\Report\Afterresultreportpdf::schoolhigher_result');
$routes->match(['GET', 'POST'], 'district/report/afterresult_reportpdf/schoolhigher_resultall', 'District\Report\Afterresultreportpdf::schoolhigher_resultall');
$routes->match(['GET', 'POST'], 'district/report/afterresult_reportpdf/item_code_reports', 'District\Report\Afterresultreportpdf::item_code_reports');
$routes->match(['GET', 'POST'], 'district/report/afterresult_reportpdf/time_wise_result_report', 'District\Report\Afterresultreportpdf::time_wise_result_report');
$routes->match(['GET', 'POST'], 'district/report/afterresult_reportpdf/numwise_timeresult_report', 'District\Report\Afterresultreportpdf::numwise_timeresult_report');

// 11. District Timefest Report Interfaces & PDFs
$routes->match(['GET', 'POST'], 'district/report/timeof_fest_report/timefest_result', 'District\Report\Timefestreport::timefest_result');
$routes->match(['GET', 'POST'], 'district/report/timeof_fest_report/timefest_result_confidential', 'District\Report\Timefestreport::timefest_result_confidential');
$routes->match(['GET', 'POST'], 'district/report/timeof_fest_report/timefest_result_common', 'District\Report\Timefestreport::timefest_result_common');
$routes->match(['GET', 'POST'], 'district/report/timeof_fest_reportpdf/datewisepart', 'District\Report\Timefestreportpdf::datewisepart');
$routes->match(['GET', 'POST'], 'district/report/timeof_fest_reportpdf/common_report', 'District\Report\Timefestreportpdf::common_report');
$routes->match(['GET', 'POST'], 'district/report/timeof_fest_reportpdf/confidential', 'District\Report\Timefestreportpdf::confidential');

// 12. District Master Report
$routes->match(['GET', 'POST'], 'district/report/masterreport/item_code', 'District\Report\Masterreport::item_code');
$routes->match(['GET', 'POST'], 'district/report/masterreport/item_code_pdf', 'District\Report\Masterreport::item_code_pdf');

// 13. District Certificate extended flows
$routes->match(['GET', 'POST'], 'district/certificate', 'District\Certificate\Certificate::index');
$routes->match(['GET', 'POST'], 'district/certificate/index', 'District\Certificate\Certificate::index');
$routes->match(['GET', 'POST'], 'district/certificate/certificate', 'District\Certificate\Certificate::index');
$routes->match(['GET', 'POST'], 'district/certificate/certificate/index', 'District\Certificate\Certificate::index');
$routes->match(['GET', 'POST'], 'district/certificate/save_certificate_template', 'District\Certificate\Certificate::save_certificate_template');
$routes->match(['GET', 'POST'], 'district/certificate/certificate/save_certificate_template', 'District\Certificate\Certificate::save_certificate_template');
$routes->match(['GET', 'POST'], 'district/certificate/save_template', 'District\Certificate\Certificate::save_template');
$routes->match(['GET', 'POST'], 'district/certificate/certificate/save_template', 'District\Certificate\Certificate::save_template');
$routes->match(['GET', 'POST'], 'district/certificate/test_certificate', 'District\Certificate\Certificate::test_certificate');
$routes->match(['GET', 'POST'], 'district/certificate/certificate/test_certificate', 'District\Certificate\Certificate::test_certificate');
$routes->match(['GET', 'POST'], 'district/certificate/test_certificare_withoutgraph', 'District\Certificate\Certificate::test_certificare_withoutgraph');
$routes->match(['GET', 'POST'], 'district/certificate/test_certificate_withoutgraph', 'District\Certificate\Certificate::test_certificare_withoutgraph');
$routes->match(['GET', 'POST'], 'district/certificate/certificate/test_certificare_withoutgraph', 'District\Certificate\Certificate::test_certificare_withoutgraph');
$routes->match(['GET', 'POST'], 'district/certificate/certificate/test_certificate_withoutgraph', 'District\Certificate\Certificate::test_certificare_withoutgraph');
$routes->match(['GET', 'POST'], 'district/certificate/list_item_wise', 'District\Certificate\Certificate::list_item_wise');
$routes->match(['GET', 'POST'], 'district/certificate/certificate/list_item_wise', 'District\Certificate\Certificate::list_item_wise');
$routes->match(['GET', 'POST'], 'district/certificate/get_certificate_itemwise', 'District\Certificate\Certificate::get_certificate_itemwise');
$routes->match(['GET', 'POST'], 'district/certificate/certificate/get_certificate_itemwise', 'District\Certificate\Certificate::get_certificate_itemwise');
$routes->match(['GET', 'POST'], 'district/certificate/get_certificate_pdf', 'District\Certificate\Certificate::get_certificate_pdf');
$routes->match(['GET', 'POST'], 'district/certificate/certificate/get_certificate_pdf', 'District\Certificate\Certificate::get_certificate_pdf');
$routes->match(['GET', 'POST'], 'district/certificate/create_certificate', 'District\Certificate\Certificate::create_certificate');
$routes->match(['GET', 'POST'], 'district/certificate/list_school_wise', 'District\Certificate\Certificate::list_school_wise');
$routes->match(['GET', 'POST'], 'district/certificate/certificate/list_school_wise', 'District\Certificate\Certificate::list_school_wise');
$routes->match(['GET', 'POST'], 'district/certificate/get_certificate_school_wise', 'District\Certificate\Certificate::get_certificate_school_wise');
$routes->match(['GET', 'POST'], 'district/certificate/certificate/get_certificate_school_wise', 'District\Certificate\Certificate::get_certificate_school_wise');
$routes->match(['GET', 'POST'], 'district/certificate/get_certificate_pdf_school_wise', 'District\Certificate\Certificate::get_certificate_pdf_school_wise');
$routes->match(['GET', 'POST'], 'district/certificate/certificate/get_certificate_pdf_school_wise', 'District\Certificate\Certificate::get_certificate_pdf_school_wise');
$routes->match(['GET', 'POST'], 'district/certificate/list_reg_no_wise', 'District\Certificate\Certificate::list_reg_no_wise');
$routes->match(['GET', 'POST'], 'district/certificate/certificate/list_reg_no_wise', 'District\Certificate\Certificate::list_reg_no_wise');
$routes->match(['GET', 'POST'], 'district/certificate/get_certificate_pdf_participant_wise', 'District\Certificate\Certificate::get_certificate_pdf_participant_wise');
$routes->match(['GET', 'POST'], 'district/certificate/certificate/get_certificate_pdf_participant_wise', 'District\Certificate\Certificate::get_certificate_pdf_participant_wise');
