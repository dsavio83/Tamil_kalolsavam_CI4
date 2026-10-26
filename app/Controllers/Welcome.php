<?php

namespace App\Controllers;


class Welcome extends \App\Controllers\BaseController {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */

	function __construct () {
		parent::__construct ();
		// Check if user is logged in via USERID session variable
		// (Authentication was verified in Login controller)
		$userId = $this->session->get('USERID');
		$changePwd = $this->session->get('CHANGEPWD');
		
		if (!$userId || (int)$userId <= 0) {
			// User not authenticated - redirect to login
			helper('url');
			redirect('login');
		}
		
		// User is authenticated - check CHANGEPWD onboarding
		// If CHANGEPWD != 'Y', redirect to complete onboarding
		if ($changePwd !== 'Y') {
			// User needs to change password first - redirect to complete onboarding
			helper('url');
			redirect('login/change_password');
		}

		if ((int)$this->session->get('USER_TYPE') === 6) {
			helper('url');
			redirect('stage/manager');
		}
        $this->template->add_js('js/admin/admin.js');
        try {
            $this->template->write_view('menu', 'menu', '');
            $this->template->write_view('left_panel', 'menu_left', '');
        } catch (\Throwable $e) {
            // Menu/left-panel render must never break the dashboard, even when
            // the database is empty or reference tables are missing.
        }
        $this->load->model('login/Login_Model');
        $this->Contents = array();
	}
	public function index()
	{
		// Check if sd_school_details entry exists for school user
		$school_code = $this->session->get('SCHOOL_CODE');
		if (!$school_code && (int)$this->session->get('USER_TYPE') == 5) {
			$db = \Config\Database::connect();
			$cluster_res = $db->table('sd_user_cluster')->select('school_code')->where('user_id', $this->session->get('USERID'))->get();
			$cluster = ($cluster_res && is_object($cluster_res)) ? $cluster_res->getRowArray() : [];
			if (!empty($cluster['school_code'])) {
				$school_code = $cluster['school_code'];
				$this->session->set('SCHOOL_CODE', $school_code);
			}
		}
		if ($school_code && $school_code != 0 && (int)$this->session->get('USER_TYPE') == 5) {
			$db = \Config\Database::connect();
			$builder = $db->table('sd_school_details');
			$builder->where('school_code', $school_code);
			if ($builder->countAllResults() == 0) {
				session()->setFlashdata('message', 'Please fill out your school profile details first.');
				return redirect()->to(base_url('schools/profile'));
			}
		}

		// $this->load->view('welcome_message');

		        //echo $this->session->get('USER_TYPE');.
				// if ($this->session->get('USER_TYPE') == 0 || $this->session->get('USER_TYPE') == 1) {
				//	redirect('welcome/district_details');
				// }
				if ((int)$this->session->get('USER_TYPE') === 5) {
					return redirect()->to(base_url('welcome/school_dashboard'));
				}
				if ($this->session->get('USER_TYPE') == 2) {
					//redirect('welcome/sub_district_details');
				}
				if ($this->session->get('USER_TYPE') == 3) {
					//redirect('welcome/cluster_details');
				}
				$welcome_label = '';
				$fest_name = '';
				$venue = '';
				$start_date = '';
				$end_date = '';
				$current_year = date('Y');
                $next_year = $current_year + 1;
                $sub_dist_code = $this->session->get('SUB_DISTRICT');

                if ((int)$this->session->get('USER_TYPE') === 3) {
                    $sub_dist_name = get_sub_dist_name($sub_dist_code);
                    $welcome_label = ($sub_dist_name ? $sub_dist_name . " " : "") . "Tamil Kalaivizha $current_year - $next_year";
                } else if ((int)$this->session->get('USER_TYPE') === 2) {
                    $dist_code = $this->session->get('DISTRICT');
                    $dist_name = get_dist_name($dist_code);
                    $welcome_label = ($dist_name ? $dist_name . " District " : "") . "Tamil Kalaivizha $current_year - $next_year";
                } else {
                    $welcome_label = "Tamil Kalaivizha $current_year - $next_year";
                }
                
                // DISTRICT ADMIN (USER_TYPE 2): Render the dedicated District Analytics Dashboard.
                // Scoped exclusively to district (d_*) tables; sub-district (sd_*) flows untouched.
                if ((int)$this->session->get('USER_TYPE') === 2) {
                    $dist_code = (int)$this->session->get('DISTRICT');

                    $this->Contents['welcome_label'] = $welcome_label;
                    $this->Contents['active_user_name'] = (string)($this->session->get('NAME') ?: $this->session->get('USER_NAME'));
                    $this->Contents['active_user_type'] = (int)$this->session->get('USER_TYPE');
                    $this->Contents['sub_dist_name'] = '';
                    $this->Contents['rev_dist_name'] = get_dist_name($dist_code);

                    $is_all_stage_alloted = ($this->General_Model && method_exists($this->General_Model, 'is_all_stage_alloted'))
                        ? $this->General_Model->is_all_stage_alloted()
                        : false;
                    $target_stage_date = $this->request ? $this->request->getGet('stage_date') : null;
                    try {
                        $dist_analytics = $this->get_district_analytics($dist_code, $is_all_stage_alloted, $target_stage_date);
                    } catch (\Throwable $e) {
                        $dist_analytics = [];
                    }
                    $dist_analytics = is_array($dist_analytics) ? $dist_analytics : [];
                    $this->Contents['crm'] = $dist_analytics;
                    $this->Contents['statistics'] = $dist_analytics['statistics'] ?? [];
                    $this->Contents['counts'] = $dist_analytics['counts'] ?? [];
                    $this->Contents['sub_districts'] = $dist_analytics['sub_districts'] ?? [];
                    $this->Contents['leading_code'] = $dist_analytics['leading_code'] ?? null;
                    $this->Contents['live_stages'] = $dist_analytics['stages'] ?? [];
                    $this->Contents['pipeline'] = $dist_analytics['pipeline'] ?? [];
                    $this->Contents['leaderboard'] = $dist_analytics['leaderboard'] ?? [];
                    $this->Contents['quick_search'] = $dist_analytics['quick_search'] ?? [];
                    $this->Contents['is_all_stage_alloted'] = $is_all_stage_alloted;
                    $this->Contents['target_stage_date'] = $dist_analytics['target_date'] ?? date('Y-m-d');
                    $this->Contents['available_stage_dates'] = $dist_analytics['available_dates'] ?? [];

                    // District kalolsavam details (name / venue / dates)
                    $fest_name = '';
                    $venue = '';
                    $start_date = '';
                    $end_date = '';
                    $this->Contents['has_kalolsavam'] = false;
                    if ($dist_code) {
                        $dist_kal = $this->db->query(
                            "SELECT dist_kalolsavam_name, venue, start_date, end_date
                             FROM d_kalolsavam_master
                             WHERE rev_district_code = ? LIMIT 1",
                            array($dist_code)
                        );
                        $dist_kal_row = ($dist_kal && is_object($dist_kal)) ? $dist_kal->getRowArray() : null;
                        if (!empty($dist_kal_row)) {
                            $fest_name = $dist_kal_row['dist_kalolsavam_name'] ?? '';
                            $venue = $dist_kal_row['venue'] ?? '';
                            $start_date = $dist_kal_row['start_date'] ?? '';
                            $end_date = $dist_kal_row['end_date'] ?? '';
                            $this->Contents['has_kalolsavam'] = true;
                        }
                    }
                    $this->Contents['fest_name'] = $fest_name;
                    $this->Contents['venue'] = $venue;
                    $this->Contents['start_date'] = $start_date;
                    $this->Contents['end_date'] = $end_date;

                    $this->template->write_view('content', 'district/dashboard', $this->Contents);
                    $this->template->load();
                    return;
                }

                // Get sub-district kalolsavam details and statistics
                $is_all_stage_alloted = ($this->General_Model && method_exists($this->General_Model, 'is_all_stage_alloted')) 
                    ? $this->General_Model->is_all_stage_alloted() 
                    : false;

                $target_stage_date = $this->request ? $this->request->getGet('stage_date') : null;
                try {
                    $crm_data = $this->get_crm_analytics($sub_dist_code, $is_all_stage_alloted, $target_stage_date);
                } catch (\Throwable $e) {
                    $crm_data = [];
                }
                $crm_data = is_array($crm_data) ? $crm_data : [];
                $this->Contents['crm'] = $crm_data;
                $this->Contents['statistics'] = $crm_data['statistics'] ?? [];
                $this->Contents['live_stages'] = $crm_data['stages'] ?? [];
                $this->Contents['pipeline'] = $crm_data['pipeline'] ?? [];
                $this->Contents['leaderboard'] = $crm_data['leaderboard'] ?? [];
                $this->Contents['quick_search'] = $crm_data['quick_search'] ?? [];
                $this->Contents['counts'] = $crm_data['counts'] ?? [];
                $this->Contents['is_all_stage_alloted'] = $is_all_stage_alloted;
                $this->Contents['target_stage_date'] = $crm_data['target_date'] ?? date('Y-m-d');
                $this->Contents['available_stage_dates'] = $crm_data['available_dates'] ?? [];

                // Active user & jurisdiction info
                $this->Contents['active_user_name'] = (string)($this->session->get('NAME') ?: $this->session->get('USER_NAME'));
                $this->Contents['active_user_type'] = (int)$this->session->get('USER_TYPE');
                $this->Contents['sub_dist_name']    = get_sub_dist_name($sub_dist_code);
                $this->Contents['rev_dist_name']    = get_dist_name($this->session->get('DISTRICT'));

                if ($sub_dist_code) {
                    $kal_q = $this->db->select('sub_dist_kalolsavam_name, venue, start_date, end_date')
                        ->from('sd_kalolsavam_master')
                        ->where('sub_district_code', $sub_dist_code)
                        ->get();
                    $kalolsavam_details = ($kal_q && is_object($kal_q)) ? $kal_q->getRowArray() : null;
                    
                    if ($kalolsavam_details) {
                        $fest_name = $kalolsavam_details['sub_dist_kalolsavam_name'];
                        $venue = $kalolsavam_details['venue'];
                        $start_date = $kalolsavam_details['start_date'];
                        $end_date = $kalolsavam_details['end_date'];
                    }
                }

               $this->Contents['has_kalolsavam'] = !empty($kalolsavam_details);

                // Fetch stage schedule for the dashboard (strictly filtered by sub-district)
                $sched_where = $sub_dist_code ? " WHERE sim.sub_district_code = " . (int)$sub_dist_code . " AND sm.sub_district_code = " . (int)$sub_dist_code : "";
                $sched_q = $this->db->query("
                    SELECT sm.stage_id, sm.stage_name, im.item_name, sim.start_time, sim.item_time, im.gender, im.item_code
                    FROM sd_stage_item_master sim
                    INNER JOIN sd_stage_master sm ON sim.stage_id = sm.stage_id
                    INNER JOIN item_master im ON sim.item_code = im.item_code
                    {$sched_where}
                    ORDER BY sm.stage_name ASC, sim.start_time ASC
                ");
                $schedule = ($sched_q && is_object($sched_q)) ? $sched_q->getResultArray() : [];
                
                $stages = array();
                foreach ($schedule as $row) {
                    $stage_id = $row['stage_id'];
                    if (!isset($stages[$stage_id])) {
                        $stages[$stage_id] = array(
                            'name' => $row['stage_name'],
                            'events' => array()
                        );
                    }
                    $stages[$stage_id]['events'][] = $row;
                }
                $this->Contents['stages_schedule'] = $stages;

				$this->Contents['welcome_label'] = $welcome_label;
				$this->Contents['fest_name'] = $fest_name;
				$this->Contents['venue'] = $venue;
				$this->Contents['start_date'] = $start_date;
				$this->Contents['end_date'] = $end_date;
				$this->template->write_view('content', 'welcome', $this->Contents);
				$this->template->load();
	}


    /* Function for listing cluster details & hierarchy (supports District Admin, Sub-District Admin, and State/IT Admin) */

    function cluster_details($message = '') {
        if ((int)$this->session->get('USER_TYPE') === 5) {
            return redirect()->to(base_url('welcome/school_dashboard'));
        }

        if ($message) {
            if (is_array($message['error_array'])) {
                foreach ($message['error_array'] as $error) {
                    $this->template->write('error', $error . '<br>');
                }
            }
        }

        $user_type = (int)$this->session->get('USER_TYPE');
        $sess_dist = (int)$this->session->get('DISTRICT');
        $sess_subdist = (int)$this->session->get('SUB_DISTRICT');
        $req_dist = $this->input->get_post('sel_district_id');
        $req_subdist = $this->input->get_post('sel_sub_district_id');

        $this->Contents = array();
        $this->template->write('title', '');

        $all_districts = [];
        $all_sub_districts = [];
        $district_code = 0;
        $district_name = '';
        $subdist = null;
        $sub_district_name = '';
        $is_district_view = false;

        if ($user_type === 3) {
            // Sub-District Admin: strictly scoped to session sub-district
            $subdist = $sess_subdist;
            $district_code = (int)$this->Login_Model->get_subdist_district_code($subdist);
            $dist_row = $this->General_Model->get_data('rev_district_master', 'rev_district_name', ['rev_district_code' => $district_code]);
            $district_name = $dist_row[0]['rev_district_name'] ?? 'District';
            $sd_row = $this->General_Model->get_data('sub_district_master', 'sub_district_name', ['sub_district_code' => $subdist]);
            $sub_district_name = $sd_row[0]['sub_district_name'] ?? 'Sub-District';
            $is_district_view = false;
        } elseif ($user_type === 2) {
            // District Admin: scoped to session district
            $district_code = $sess_dist;
            $dist_row = $this->General_Model->get_data('rev_district_master', 'rev_district_name', ['rev_district_code' => $district_code]);
            $district_name = $dist_row[0]['rev_district_name'] ?? 'District';
            $all_sub_districts = $this->General_Model->get_data('sub_district_master', '*', ['rev_district_code' => $district_code], 'sub_district_name');

            if ('' !== trim((string)$req_subdist) && 'ALL' !== strtoupper(trim((string)$req_subdist)) && '0' !== trim((string)$req_subdist)) {
                $subdist = (int)$req_subdist;
                $sd_row = $this->General_Model->get_data('sub_district_master', 'sub_district_name', ['sub_district_code' => $subdist]);
                $sub_district_name = $sd_row[0]['sub_district_name'] ?? 'Sub-District';
                $is_district_view = false;
            } else {
                // District Overview Mode: Hierarchy across all sub-districts
                $subdist = null;
                $sub_district_name = 'All Sub-Districts';
                $is_district_view = true;
            }
        } else {
            // IT Admin / State Admin (USER_TYPE <= 1) or other higher roles
            $all_districts = $this->General_Model->get_data('rev_district_master', '*', ['status' => 'Y'], 'rev_district_code');
            if (!empty($req_dist)) {
                $district_code = (int)$req_dist;
            } elseif (!empty($sess_dist)) {
                $district_code = $sess_dist;
            } else {
                $district_code = !empty($all_districts) ? (int)$all_districts[0]['rev_district_code'] : 9;
            }

            $dist_row = $this->General_Model->get_data('rev_district_master', 'rev_district_name', ['rev_district_code' => $district_code]);
            $district_name = $dist_row[0]['rev_district_name'] ?? 'District';
            $all_sub_districts = $this->General_Model->get_data('sub_district_master', '*', ['rev_district_code' => $district_code], 'sub_district_name');

            if ('' !== trim((string)$req_subdist) && 'ALL' !== strtoupper(trim((string)$req_subdist)) && '0' !== trim((string)$req_subdist)) {
                $subdist = (int)$req_subdist;
                $sd_row = $this->General_Model->get_data('sub_district_master', 'sub_district_name', ['sub_district_code' => $subdist]);
                $sub_district_name = $sd_row[0]['sub_district_name'] ?? 'Sub-District';
                $is_district_view = false;
            } else {
                $subdist = null;
                $sub_district_name = 'All Sub-Districts';
                $is_district_view = true;
            }
        }

        if ($is_district_view) {
            // Load district summary hierarchy and district clusters
            $district_summary = $this->Login_Model->get_district_subdistricts_summary($district_code);
            $district_clusters = $this->Login_Model->get_district_cluster_details($district_code);
            $sub_school = $this->Login_Model->get_district_school_details($district_code);
            $nonclust = $this->Login_Model->get_district_unclustersch_details($district_code);
            $val = $this->Login_Model->get_district_unclustersch_finentry($district_code);

            $this->Contents['district_summary'] = $district_summary;
            $this->Contents['district_clusters'] = $district_clusters;
            $this->Contents['clusters'] = $district_clusters;
            $this->Contents['sub_admin'] = [];
            $this->Contents['sub_school'] = $sub_school;
            $this->Contents['nonclust'] = $nonclust;
            $this->Contents['val'] = $val;
            $this->Contents['subdst'] = '';
        } else {
            // Load specific sub-district cluster directory
            $sub_admin = $this->Login_Model->get_sub_admin_details($subdist);
            $clusters = $this->Login_Model->get_cluster_details($subdist);
            $sub_school = $this->Login_Model->get_sub_school_details($subdist);
            $nonclust = $this->Login_Model->get_unclustersch_details($subdist);
            $val = $this->Login_Model->get_unclustersch_finentry($subdist);

            $this->Contents['sub_admin'] = $sub_admin;
            $this->Contents['clusters'] = $clusters;
            $this->Contents['sub_school'] = $sub_school;
            $this->Contents['nonclust'] = $nonclust;
            $this->Contents['val'] = $val;
            $this->Contents['subdst'] = $subdist;
            $this->Contents['district_summary'] = [];
            $this->Contents['district_clusters'] = [];
        }

        $this->Contents['district_code'] = $district_code;
        $this->Contents['district_name'] = $district_name;
        $this->Contents['sub_district_name'] = $sub_district_name;
        $this->Contents['is_district_view'] = $is_district_view;
        $this->Contents['all_districts'] = $all_districts;
        $this->Contents['all_sub_districts'] = $all_sub_districts;

        $this->template->write_view('content', 'login/clusters', $this->Contents);
        $this->template->load();
    }

    function cluster_schools() {
        if ((int)$this->session->get('USER_TYPE') === 5) {
            return redirect()->to(base_url('welcome/school_dashboard'));
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $clusterId = $this->input->get_post('hidClusterId') ?: '';
        if (empty($clusterId)) {
            $clusterId = $this->session->get('USERID');
        }
        
        $cluster = $this->Login_Model->clusterdetails($clusterId);
        $school = $this->Login_Model->schooldetails($clusterId);
        $particip = $this->Login_Model->schoolpartcip();
        $this->Contents['cluster'] = $cluster;
        $this->Contents['school'] = $school;
        $this->Contents['part'] = $particip;
        
        $sub_district_code = '';
        $school_code = '';
        if (count($school) > 0) {
            $school_code = $school[0]['school_code'];
            $sub_district_code = $school[0]['sub_district_code'];
        } else if (!empty($clusterId)) {
            $u = $this->db->table('user_master')->select('sub_district_code, rev_district_code')->where('user_id', $clusterId)->get()->getRowArray();
            if ($u) {
                $sub_district_code = $u['sub_district_code'] ?? '';
            }
        }
        if (empty($sub_district_code)) {
            $sub_district_code = $this->session->get('SUB_DISTRICT') ?: $this->input->get_post('sel_sub_district_id');
        }
        
        $this->Contents['statistics'] = $school_code ? $this->get_school_stats($school_code) : null;
        $this->Contents['venue_details'] = $this->get_venue($sub_district_code);
        $this->Contents['sub_district_name'] = $this->get_subdist_name($sub_district_code);
        $this->Contents['district_code'] = $this->Login_Model->get_subdist_district_code($sub_district_code);
        $this->Contents['sub_district_code'] = $sub_district_code;
        $this->Contents['cluster_id'] = $clusterId;
        
        $this->template->write_view('content', 'login/clustschool', $this->Contents);
        $this->template->load();
    }

    public function entry_confirm_status() {
        $user_type = (int)$this->session->get('USER_TYPE');
        $sess_dist = (int)$this->session->get('DISTRICT');
        $sess_subdist = (int)$this->session->get('SUB_DISTRICT');

        $this->Contents = array();
        $this->template->write('title', 'Entry Confirm Status');

        $district_code = 0;
        $district_name = '';
        $subdist = 0;
        $sub_district_name = '';

        if ($user_type === 3) {
            $subdist = $sess_subdist;
            $district_code = (int)$this->Login_Model->get_subdist_district_code($subdist);
            $dist_row = $this->General_Model->get_data('rev_district_master', 'rev_district_name', ['rev_district_code' => $district_code]);
            $district_name = $dist_row[0]['rev_district_name'] ?? 'District';
            $sd_row = $this->General_Model->get_data('sub_district_master', 'sub_district_name', ['sub_district_code' => $subdist]);
            $sub_district_name = $sd_row[0]['sub_district_name'] ?? 'Sub-District';
        } elseif ($user_type === 2) {
            $district_code = $sess_dist;
            $dist_row = $this->General_Model->get_data('rev_district_master', 'rev_district_name', ['rev_district_code' => $district_code]);
            $district_name = $dist_row[0]['rev_district_name'] ?? 'District';
        } elseif ($user_type <= 1 || $this->session->get('USER_GROUP') == 'W') {
            $all_districts = $this->General_Model->get_data('rev_district_master', '*', ['status' => 'Y'], 'rev_district_code');
            $req_dist = $this->input->get_post('sel_district_id');
            if (!empty($req_dist)) {
                $district_code = (int)$req_dist;
            } elseif (!empty($sess_dist)) {
                $district_code = $sess_dist;
            } else {
                $district_code = !empty($all_districts) ? (int)$all_districts[0]['rev_district_code'] : 9;
            }
            $dist_row = $this->General_Model->get_data('rev_district_master', 'rev_district_name', ['rev_district_code' => $district_code]);
            $district_name = $dist_row[0]['rev_district_name'] ?? 'District';
        }

        $school_list = $this->Login_Model->get_entry_confirm_schools($subdist, $district_code);

        $total_schools = count($school_list);
        $total_data_entry = 0;
        $total_confirmed = 0;
        foreach ($school_list as $s) {
            if (($s['has_data_entry'] ?? 'N') === 'Y') $total_data_entry++;
            if (($s['is_finalize'] ?? 'N') === 'Y') $total_confirmed++;
        }
        $total_pending = max(0, $total_schools - $total_confirmed);

        $this->Contents['school_list'] = $school_list;
        $this->Contents['total_schools'] = $total_schools;
        $this->Contents['total_data_entry'] = $total_data_entry;
        $this->Contents['total_confirmed'] = $total_confirmed;
        $this->Contents['total_pending'] = $total_pending;
        $this->Contents['district_code'] = $district_code;
        $this->Contents['district_name'] = $district_name;
        $this->Contents['sub_district_code'] = $subdist;
        $this->Contents['sub_district_name'] = $sub_district_name;
        $this->Contents['user_type'] = $user_type;

        $this->template->write_view('content', 'login/entry_confirm_status', $this->Contents);
        $this->template->load();
    }

    public function school_dashboard() {
        if (!$this->Session_Model->is_user_logged()) {
            return redirect()->to(base_url('login'));
        }

        $this->Contents = array();
        $this->template->write('title', 'School Dashboard');

        $school_code = (string)$this->session->get('SCHOOL_CODE');
        if (empty($school_code)) {
            $school_code = (string)($this->request->getGetPost('school_code') ?: '');
            if (empty($school_code)) {
                $user_id = $this->session->get('USERID');
                $user = $this->db->table('user_master')->select('user_name, sub_district_code')->where('user_id', $user_id)->get()->getRowArray();
                if ($user && is_numeric($user['user_name'])) {
                    $school_code = $user['user_name'];
                }
            }
        }

        if (empty($school_code)) {
            return redirect()->to(base_url('welcome'));
        }

        // Fetch School Profile Details
        $school_details = $this->db->table('sd_school_master sm')
            ->select('sm.school_code, sm.school_name, sm.school_type, sm.sub_district_code, sm.rev_district_code,
                      sdm.sub_district_name, rdm.rev_district_name,
                      ssd.hm_name, ssd.hm_phone, ssd.principal_name, ssd.principal_phone, ssd.school_email,
                      ssd.school_phone, ssd.is_finalize, ssd.is_create_report, ssd.total_strength')
            ->join('sub_district_master sdm', 'sm.sub_district_code = sdm.sub_district_code', 'LEFT')
            ->join('rev_district_master rdm', 'sm.rev_district_code = rdm.rev_district_code', 'LEFT')
            ->join('sd_school_details ssd', 'sm.school_code = ssd.school_code', 'LEFT')
            ->where('sm.school_code', $school_code)
            ->get()->getRowArray() ?: [];

        if (empty($school_details) && (int)$this->session->get('USER_TYPE') === 5) {
            session()->setFlashdata('message', 'Please complete your school profile details first.');
            return redirect()->to(base_url('schools/profile'));
        }

        $sub_district_code = $school_details['sub_district_code'] ?? $this->session->get('SUB_DISTRICT') ?? '';
        $sub_district_name = $school_details['sub_district_name'] ?? '';
        $rev_district_code = $school_details['rev_district_code'] ?? $this->session->get('DISTRICT') ?? '';
        $rev_district_name = $school_details['rev_district_name'] ?? '';

        if (empty($rev_district_code) && !empty($sub_district_code)) {
            $sd_row = $this->db->table('sub_district_master')
                ->select('rev_district_code')
                ->where('sub_district_code', $sub_district_code)
                ->get()->getRowArray();
            if (!empty($sd_row['rev_district_code'])) {
                $rev_district_code = $sd_row['rev_district_code'];
            }
        }

        if (empty($rev_district_name) && !empty($rev_district_code)) {
            $rd_row = $this->db->table('rev_district_master')
                ->select('rev_district_name')
                ->where('rev_district_code', $rev_district_code)
                ->get()->getRowArray();
            if (!empty($rd_row['rev_district_name'])) {
                $rev_district_name = $rd_row['rev_district_name'];
            }
        }

        // Check if Sub-District registration is confirmed/finalized
        $is_sd_confirmed = false;
        if (!empty($sub_district_code)) {
            $sdm_check = $this->db->table('sub_district_master')
                ->select('confirm_data_entry')
                ->where('sub_district_code', $sub_district_code)
                ->get()->getRowArray();
            if (!empty($sdm_check['confirm_data_entry']) && $sdm_check['confirm_data_entry'] === 'Y') {
                $is_sd_confirmed = true;
            }
        }
        if (!$is_sd_confirmed && !empty($school_code)) {
            $sm_check = $this->db->table('sd_school_master')
                ->select('master_confirm')
                ->where('school_code', (float)$school_code)
                ->get()->getRowArray();
            if (!empty($sm_check['master_confirm']) && $sm_check['master_confirm'] === 'Y') {
                $is_sd_confirmed = true;
            }
        }
        if (!$is_sd_confirmed && (($school_details['is_finalize'] ?? '') === 'Y')) {
            $is_sd_confirmed = true;
        }

        // Check if District School Registration is enabled by Admin
        $is_district_reg_enabled = false;
        if (!empty($rev_district_code)) {
            $dist_setting = $this->db->table('district_item_settings')
                ->select('allow_school_registration')
                ->where('rev_district_code', $rev_district_code)
                ->get()->getRowArray();
            if (!empty($dist_setting['allow_school_registration']) && (int)$dist_setting['allow_school_registration'] === 1) {
                $is_district_reg_enabled = true;
            }
        }
        if (!$is_district_reg_enabled) {
            $gen_row = $this->db->table('general_settings')
                ->select('value')
                ->where('name', 'dist_school_registration_enabled')
                ->get()->getRowArray();
            if (!empty($gen_row['value']) && $gen_row['value'] === '1') {
                $is_district_reg_enabled = true;
            }
        }

        // Check if all subdistrict items and results are completed
        $sd_total_items = (int)($this->db->query("
            SELECT COUNT(DISTINCT item_code) as cnt 
            FROM sd_participant_item_details 
            WHERE sub_district_code = ?
        ", [$sub_district_code])->getRowArray()['cnt'] ?? 0);

        $sd_completed_items = (int)($this->db->query("
            SELECT COUNT(DISTINCT item_code) as cnt 
            FROM sd_result_master 
            WHERE sub_district_code = ? AND is_finish = 'Y'
        ", [$sub_district_code])->getRowArray()['cnt'] ?? 0);

        $is_sd_completed = ($sd_total_items > 0 && $sd_completed_items >= $sd_total_items);

        // Also check if district qualification records exist for this subdistrict/school
        $has_d_data = false;
        if (!empty($sub_district_code)) {
            $has_d_data = ($this->db->query("
                SELECT 1 FROM d_participant_details WHERE sub_district_code = ? LIMIT 1
            ", [$sub_district_code])->getNumRows() > 0);
        }

        $can_view_district = ($is_sd_completed || $has_d_data);

        // Level selection: 'sd' (Sub-District) or 'd' (District)
        $req_level = strtolower(trim((string)$this->request->getGet('level')));
        if ($req_level === 'd' && $can_view_district) {
            $current_level = 'd';
        } else {
            $current_level = 'sd';
        }

        $is_district = ($current_level === 'd');
        $pfx = $is_district ? 'd_' : 'sd_';

        // 1. Participant Statistics (Total, Boys, Girls, LP/UP/HS/HSS)
        $stats = [
            'total_students' => 0,
            'boys_count' => 0,
            'girls_count' => 0,
            'lp_total' => 0, 'lp_boys' => 0, 'lp_girls' => 0,
            'up_total' => 0, 'up_boys' => 0, 'up_girls' => 0,
            'hs_total' => 0, 'hs_boys' => 0, 'hs_girls' => 0,
            'hss_total' => 0, 'hss_boys' => 0, 'hss_girls' => 0,
            'total_items_registered' => 0,
            'total_results_declared' => 0,
            'total_points' => 0,
            'prizes_1st' => 0,
            'prizes_2nd' => 0,
            'prizes_3rd' => 0,
            'grades_a' => 0,
            'grades_b' => 0,
            'grades_c' => 0,
            'subdistrict_rank' => '-',
            'total_schools_in_subdist' => 0
        ];

        $part_rows = $this->db->query("
            SELECT class, gender, COUNT(*) as cnt 
            FROM {$pfx}participant_details 
            WHERE school_code = ? 
            GROUP BY class, gender
        ", [$school_code])->getResultArray();

        foreach ($part_rows as $pr) {
            $cnt = (int)$pr['cnt'];
            $gender = strtoupper(trim($pr['gender']));
            $class = (int)trim($pr['class']);

            $stats['total_students'] += $cnt;
            if ($gender === 'B' || $gender === 'M') {
                $stats['boys_count'] += $cnt;
            } elseif ($gender === 'G' || $gender === 'F') {
                $stats['girls_count'] += $cnt;
            }

            if ($class >= 1 && $class <= 4) {
                $stats['lp_total'] += $cnt;
                if ($gender === 'B' || $gender === 'M') $stats['lp_boys'] += $cnt; else $stats['lp_girls'] += $cnt;
            } elseif ($class >= 5 && $class <= 7) {
                $stats['up_total'] += $cnt;
                if ($gender === 'B' || $gender === 'M') $stats['up_boys'] += $cnt; else $stats['up_girls'] += $cnt;
            } elseif ($class >= 8 && $class <= 10) {
                $stats['hs_total'] += $cnt;
                if ($gender === 'B' || $gender === 'M') $stats['hs_boys'] += $cnt; else $stats['hs_girls'] += $cnt;
            } elseif ($class >= 11 && $class <= 12) {
                $stats['hss_total'] += $cnt;
                if ($gender === 'B' || $gender === 'M') $stats['hss_boys'] += $cnt; else $stats['hss_girls'] += $cnt;
            }
        }

        // Total distinct items registered
        $item_cnt_res = $this->db->query("
            SELECT COUNT(DISTINCT item_code) as cnt 
            FROM {$pfx}participant_item_details 
            WHERE school_code = ?
        ", [$school_code])->getRowArray();
        $stats['total_items_registered'] = (int)($item_cnt_res['cnt'] ?? 0);

        // 2. Events, Stage & Schedule Status (from Stage Manager tables)
        $stage_scope_col = $is_district ? 'sim.district_code' : 'sim.sub_district_code';
        $stage_scope_val = $is_district ? $rev_district_code : $sub_district_code;
        $result_scope_col = $is_district ? 'district_code' : 'sub_district_code';

        $schedule_rows = $this->db->query("
            SELECT 
                pi.item_code,
                im.item_name,
                im.item_type,
                im.fest_id,
                fm.fest_name,
                sim.si_id,
                sim.stage_id,
                sm.stage_name,
                sm.stage_desc,
                sim.start_time,
                sim.item_time,
                sim.status AS stage_status,
                sim.delay_minutes,
                sim.actual_start_time,
                sim.actual_end_time,
                COALESCE(res.has_result, 0) AS has_result,
                COALESCE(res.is_finish, 'N') AS is_finish,
                COALESCE(pub.is_published, 0) AS is_published
            FROM (
                SELECT DISTINCT item_code, school_code
                FROM {$pfx}participant_item_details
                WHERE school_code = ?
            ) pi
            INNER JOIN item_master im ON pi.item_code = im.item_code
            LEFT JOIN festival_master fm ON im.fest_id = fm.fest_id
            INNER JOIN {$pfx}stage_item_master sim ON pi.item_code = sim.item_code AND ({$stage_scope_col} = ? OR {$stage_scope_col} = 0)
            INNER JOIN {$pfx}stage_master sm ON sim.stage_id = sm.stage_id
            LEFT JOIN (
                SELECT item_code, 1 as has_result, MAX(is_finish) as is_finish
                FROM {$pfx}result_master
                WHERE {$result_scope_col} = ?
                GROUP BY item_code
            ) res ON pi.item_code = res.item_code
            LEFT JOIN (
                SELECT item_code, 1 as is_published
                FROM {$pfx}result_publish
                WHERE {$result_scope_col} = ?
                GROUP BY item_code
            ) pub ON pi.item_code = pub.item_code
            WHERE sim.stage_id > 0 
              AND sim.start_time IS NOT NULL 
              AND sim.start_time > '1970-01-01 00:00:00'
            ORDER BY sim.start_time ASC, pi.item_code ASC
        ", [$school_code, $stage_scope_val, $stage_scope_val, $stage_scope_val])->getResultArray();

        // Query participants mapped by item_code
        $item_participants = [];
        $parts_q = $this->db->query("
            SELECT 
                pi.item_code,
                p.participant_name,
                p.admn_no,
                p.class,
                p.gender
            FROM {$pfx}participant_item_details pi
            JOIN {$pfx}participant_details p ON pi.participant_id = p.participant_id
            WHERE pi.school_code = ?
            ORDER BY pi.item_code, p.class, p.participant_name
        ", [$school_code])->getResultArray();

        foreach ($parts_q as $pq) {
            $item_participants[$pq['item_code']][] = $pq;
        }

        // Attach participants to schedule rows
        foreach ($schedule_rows as &$sr) {
            $sr['participants'] = $item_participants[$sr['item_code']] ?? [];
        }
        unset($sr);

        // 3. School Results and Grades
        $results_rows = $this->db->query("
            SELECT 
                rm.item_code,
                im.item_name,
                im.item_type,
                fm.fest_name,
                p.participant_name,
                p.admn_no,
                p.class,
                p.gender,
                rm.grade,
                rm.point,
                rm.rank,
                rm.is_finish,
                rp.is_withheld,
                COALESCE(rp.grade, rm.grade) AS published_grade
            FROM {$pfx}result_master rm
            JOIN {$pfx}participant_details p ON rm.participant_id = p.participant_id
            LEFT JOIN item_master im ON rm.item_code = im.item_code
            LEFT JOIN festival_master fm ON im.fest_id = fm.fest_id
            LEFT JOIN {$pfx}result_publish rp ON rm.item_code = rp.item_code AND rm.participant_id = rp.participant_id
            WHERE rm.school_code = ? AND rm.is_finish = 'Y'
            ORDER BY rm.rank ASC, rm.grade ASC, rm.point DESC
        ", [$school_code])->getResultArray();

        $stats['total_results_declared'] = count($results_rows);

        foreach ($results_rows as $rr) {
            $pts = (int)$rr['point'];
            $stats['total_points'] += $pts;

            $grd = strtoupper(trim($rr['published_grade'] ?? $rr['grade']));
            if ($grd === 'A') $stats['grades_a']++;
            elseif ($grd === 'B') $stats['grades_b']++;
            elseif ($grd === 'C') $stats['grades_c']++;

            $rnk = (int)$rr['rank'];
            if ($rnk === 1) $stats['prizes_1st']++;
            elseif ($rnk === 2) $stats['prizes_2nd']++;
            elseif ($rnk === 3) $stats['prizes_3rd']++;
        }

        // 4. Calculate Points Table & Rank (Sub-District or District based on current level)
        if ($is_district && !empty($rev_district_code)) {
            $rank_rows = $this->db->query("
                SELECT s.school_code, SUM(s.point) as total_points
                FROM d_school_point_details s
                WHERE s.district_code = ?
                GROUP BY s.school_code
                ORDER BY total_points DESC
            ", [$rev_district_code])->getResultArray();

            $stats['total_schools_in_subdist'] = count($rank_rows);
            $pos = 1;
            foreach ($rank_rows as $rk) {
                if ($rk['school_code'] == $school_code) {
                    $stats['subdistrict_rank'] = $pos;
                    break;
                }
                $pos++;
            }
        } elseif (!$is_district && !empty($sub_district_code)) {
            $rank_rows = $this->db->query("
                SELECT s.school_code, SUM(s.point) as total_points
                FROM sd_school_point_details s
                WHERE s.sub_district_code = ?
                GROUP BY s.school_code
                ORDER BY total_points DESC
            ", [$sub_district_code])->getResultArray();

            $stats['total_schools_in_subdist'] = count($rank_rows);
            $pos = 1;
            foreach ($rank_rows as $rk) {
                if ($rk['school_code'] == $school_code) {
                    $stats['subdistrict_rank'] = $pos;
                    break;
                }
                $pos++;
            }
        }

        $this->Contents['school'] = $school_details;
        $this->Contents['school_code'] = $school_code;
        $this->Contents['stats'] = $stats;
        $this->Contents['schedules'] = $schedule_rows;
        $this->Contents['results'] = $results_rows;
        $this->Contents['sub_district_code'] = $sub_district_code;
        $this->Contents['sub_district_name'] = $sub_district_name;
        $this->Contents['rev_district_code'] = $rev_district_code;
        $this->Contents['rev_district_name'] = $rev_district_name;
        $this->Contents['can_view_district'] = $can_view_district;
        $this->Contents['current_level'] = $current_level;
        $this->Contents['is_sd_completed'] = $is_sd_completed;
        $this->Contents['is_sd_confirmed'] = $is_sd_confirmed;
        $this->Contents['is_district_reg_enabled'] = $is_district_reg_enabled;

        $this->template->write_view('content', 'schools/school_dashboard', $this->Contents);
        $this->template->load();
    }

    
    private function get_school_stats($school_code) {
        $stats = array('total_students' => 0, 'boys_count' => 0, 'girls_count' => 0,
            'lp_total' => 0, 'lp_boys' => 0, 'lp_girls' => 0,
            'up_total' => 0, 'up_boys' => 0, 'up_girls' => 0,
            'hs_total' => 0, 'hs_boys' => 0, 'hs_girls' => 0,
            'hss_total' => 0, 'hss_boys' => 0, 'hss_girls' => 0,
            'result_percentage' => 0, 'total_items_participated' => 0, 'prizes_won' => 0, 'class_wise' => array());
        
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);
        $partTable = $isDistrict ? 'd_participant_details' : 'sd_participant_details';
        $itemTable = $isDistrict ? 'd_participant_item_details' : 'sd_participant_item_details';
        $resTable = $isDistrict ? 'd_result_master' : 'sd_result_master';

        $result = $this->db->query("SELECT gender, COUNT(*) as count FROM {$partTable} WHERE school_code = ? GROUP BY gender", array($school_code));
        if ($result) {
            foreach ($result->getResultArray() as $row) {
                $stats['total_students'] += (int)$row['count'];
                if ($row['gender'] == 'B') $stats['boys_count'] = (int)$row['count'];
                if ($row['gender'] == 'G') $stats['girls_count'] = (int)$row['count'];
            }
        }
        
        $result = $this->db->query("SELECT class, gender, COUNT(*) as count FROM {$partTable} WHERE school_code = ? GROUP BY class, gender", array($school_code));
        if ($result) {
            foreach ($result->getResultArray() as $row) {
                $class = trim($row['class']);
                if (!isset($stats['class_wise'][$class])) $stats['class_wise'][$class] = array('boys' => 0, 'girls' => 0, 'total' => 0);
                $count = (int)$row['count'];
                $stats['class_wise'][$class]['total'] += $count;
                if ($row['gender'] == 'B') $stats['class_wise'][$class]['boys'] = $count;
                if ($row['gender'] == 'G') $stats['class_wise'][$class]['girls'] = $count;
                
                if (is_numeric($class)) {
                    $c = (int)$class;
                    if ($c >= 1 && $c <= 4) {
                        $stats['lp_total'] += $count;
                        if ($row['gender'] == 'B') $stats['lp_boys'] += $count;
                        if ($row['gender'] == 'G') $stats['lp_girls'] += $count;
                    } elseif ($c >= 5 && $c <= 7) {
                        $stats['up_total'] += $count;
                        if ($row['gender'] == 'B') $stats['up_boys'] += $count;
                        if ($row['gender'] == 'G') $stats['up_girls'] += $count;
                    } elseif ($c >= 8 && $c <= 10) {
                        $stats['hs_total'] += $count;
                        if ($row['gender'] == 'B') $stats['hs_boys'] += $count;
                        if ($row['gender'] == 'G') $stats['hs_girls'] += $count;
                    } elseif ($c >= 11 && $c <= 12) {
                        $stats['hss_total'] += $count;
                        if ($row['gender'] == 'B') $stats['hss_boys'] += $count;
                        if ($row['gender'] == 'G') $stats['hss_girls'] += $count;
                    }
                }
            }
        }
        
        $result = $this->db->query("SELECT COUNT(DISTINCT item_code) as count FROM {$itemTable} WHERE school_code = ?", array($school_code));
        if ($result && $result->getNumRows() > 0) $stats['total_items_participated'] = (int)$result->getRowArray()['count'];
        
        $result = $this->db->query("SELECT COUNT(*) as count FROM {$resTable} WHERE school_code = ? AND grade IN ('A', 'B', 'C')", array($school_code));
        if ($result && $result->getNumRows() > 0) $stats['prizes_won'] = (int)$result->getRowArray()['count'];
        
        if ($stats['total_items_participated'] > 0) $stats['result_percentage'] = round(($stats['prizes_won'] / $stats['total_items_participated']) * 100, 2);
        
        return $stats;
    }
    
    private function get_venue($sub_district_code) {
        $result = $this->db->query("SELECT venue, start_date, end_date FROM sd_kalolsavam_master WHERE sub_district_code = ?", array($sub_district_code));
        return ($result && $result->getNumRows() > 0) ? $result->getRowArray() : array('venue' => '', 'start_date' => '', 'end_date' => '');
    }
    
    private function get_subdist_name($sub_district_code) {
        $result = $this->db->query("SELECT sub_district_name FROM sub_district_master WHERE sub_district_code = ?", array($sub_district_code));
        return ($result && $result->getNumRows() > 0) ? $result->getRowArray()['sub_district_name'] : '';
    }

    function pdf_report() {
        $this->load->library('html2pdf');
        $this->Contents = array();

        $content = get_encr_password('password');
        $html2pdf = new HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('project_urls.pdf', 'I');
    }

    function confirm_sub_dist_schools() {
        $this->load->model('user/User_Cluster_Model');
        $message = '';
        if ($this->session->get('USER_TYPE') == 3) {
            $message = $this->User_Cluster_Model->confirm_sub_dist_schools();
        }
        $this->cluster_details($message);
    }

    function district_details() {
        if ((int)$this->session->get('USER_TYPE') === 5) {
            return redirect()->to(base_url('welcome/school_dashboard'));
        }
        $userType = $this->session->get('USER_TYPE');
        $userGroup = $this->session->get('USER_GROUP');
        if ($userType !== null && ((int)$userType === 0 || (int)$userType === 1 || $userGroup === 'W')) {
            $this->Contents['district_details'] = $this->General_Model->get_data('rev_district_master', '*', array('status' => 'Y'), 'rev_district_code');
            $this->template->write_view('content', 'login/list_district_details', $this->Contents);
            $this->template->load();
        } else {
            return redirect()->to(base_url('welcome'));
        }
    }

    function sub_district_details() {
        if ((int)$this->session->get('USER_TYPE') === 5) {
            return redirect()->to(base_url('welcome/school_dashboard'));
        }
        $userType = $this->session->get('USER_TYPE');
        $userGroup = $this->session->get('USER_GROUP');
        if ($userType !== null && ((int)$userType === 0 || (int)$userType === 1 || (int)$userType === 2 || $userGroup === 'W')) {
            $district_id = null;
            if ((int)$userType === 2 && $this->session->get('DISTRICT')) {
                $district_id = (int)$this->session->get('DISTRICT');
            } else if ($this->input->get_post('sel_district_id')) {
                $district_id = (int)$this->input->get_post('sel_district_id');
            } else if ($this->session->get('DISTRICT')) {
                $district_id = (int)$this->session->get('DISTRICT');
            }

            if (!$district_id) {
                return redirect()->to(base_url('welcome/district_details'));
            }

            $district_details = $this->General_Model->get_data('rev_district_master', '*', array("rev_district_code" => $district_id, "status" => "Y"), 'rev_district_code');
            $this->Contents['district_name'] = $district_details[0]['rev_district_name'] ?? 'District';
            $this->Contents['district_code'] = $district_id;
            $this->Contents['sub_district_details'] = $this->General_Model->get_data('sub_district_master', '*', array("rev_district_code" => $district_id), 'sub_district_name');
            $this->template->write_view('content', 'login/list_sub_district_details', $this->Contents);
            $this->template->load();
        } else {
            return redirect()->to(base_url('welcome'));
        }
    }

    function nonclustdetails() {
        if ((int)$this->session->get('USER_TYPE') === 5) {
            return redirect()->to(base_url('welcome/school_dashboard'));
        }
        $user_type = (int)$this->session->get('USER_TYPE');
        $sess_dist = (int)$this->session->get('DISTRICT');
        $is_district = ($user_type === 2) || ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode());

        $raw_subdist = $this->input->get_post('hidClusterId') ?: $this->input->get_post('sel_sub_district_id');
        if (empty($raw_subdist) && !$is_district) {
            $raw_subdist = $this->session->get('SUB_DISTRICT');
        }
        $subdist = (!empty($raw_subdist) && (int)$raw_subdist > 0) ? (int)$raw_subdist : 0;

        $this->Contents = array();
        $this->template->write('title', '');
        $val = $this->Login_Model->nonclustdetails_sname($subdist);
        $nonschool = $this->Login_Model->nonclustdetails_nosname($subdist);
        $this->Contents['val'] = $val;
        $this->Contents['nonschool'] = $nonschool;
        $this->Contents['sub_district_code'] = $subdist > 0 ? $subdist : '';

        if ($is_district && $subdist <= 0) {
            $this->Contents['sub_district_name'] = 'All Sub-Districts (District Wide)';
            $this->Contents['district_code'] = $sess_dist;
        } else {
            $this->Contents['sub_district_name'] = $this->get_subdist_name($subdist);
            $this->Contents['district_code'] = $this->Login_Model->get_subdist_district_code($subdist) ?: $sess_dist;
        }

        $this->template->write_view('content', 'login/nonclustschool', $this->Contents);
        $this->template->load();
    }

    function changeKalolsavam() {
        $subdist = $this->session->get('SUB_DISTRICT');
        $sub_dist_name = get_sub_dist_name($this->session->get('SUB_DISTRICT'));
        if ($change = $this->Login_Model->changeKalolsavam($subdist, $sub_dist_name)) {
            $this->template->write('message', $sub_dist_name . 'Kalolsavam Name changed sucess fully<br>');
        } else
            $this->template->write('error', 'Cant Change kalolsavam Name : <br>');

        $this->template->load();
    }

    function export_photo() {
        $this->load->library('zip');
        $this->load->helper('file');
        ini_set('memory_limit', '2048M');
        $photo_path = 'photos/' . $this->session->get('SUB_DISTRICT') . '/';
        $this->zip->read_dir($photo_path);
        $this->zip->download('photos_' . $this->session->get('SUB_DISTRICT') . '.zip');
    }

    function itemMaster() {
        $query1 = mysqli_query('INSERT INTO `item_master` (`item_id`, `item_code`, `vibhagam_id`, `fest_id`, `item_name`, `gender`, `item_type`, `max_time`, `time_type`, `max_participants`, `max_pinnani`, `is_off_stage`) VALUES
(305, 998, 21, 4, "Keralanadanam (Girls)", "G", "S", 15, "M", 1, 0, "N")');
        $query2 = mysqli_query("INSERT INTO `item_master` (`item_id`, `item_code`, `vibhagam_id`, `fest_id`, `item_name`, `gender`, `item_type`, `max_time`, `time_type`, `max_participants`, `max_pinnani`, `is_off_stage`) VALUES
(304, 682, 10, 3, 'Kerala Nadanam (Girls)', 'G', 'S', 15, 'M', 1, 0, 'N')");
    }

    private function get_statistics($sub_dist_code) {
        $stats = array(
            'total_schools' => 0, 'finalized_schools' => 0,
            'lp_schools' => 0, 'lp_boys' => 0, 'lp_girls' => 0, 'lp_total' => 0, 'lp_finalized' => 0,
            'up_schools' => 0, 'up_boys' => 0, 'up_girls' => 0, 'up_total' => 0, 'up_finalized' => 0,
            'hs_schools' => 0, 'hs_boys' => 0, 'hs_girls' => 0, 'hs_total' => 0, 'hs_finalized' => 0,
            'hss_schools' => 0, 'hss_boys' => 0, 'hss_girls' => 0, 'hss_total' => 0, 'hss_finalized' => 0,
            'total_participants' => 0, 'boys_count' => 0, 'girls_count' => 0,
            'total_items' => 0, 'completed_items' => 0
        );
        
        if (!$sub_dist_code) return $stats;
        
        $school_query = "SELECT sm.school_code, sm.school_name, sd.class_start, sd.class_end, sd.is_finalize
        FROM sd_school_master sm
        LEFT JOIN sd_school_details sd ON sm.school_code = sd.school_code
        WHERE sm.sub_district_code = ? AND sm.school_status = 'A'";
        
        $schools_res = $this->db->query($school_query, array($sub_dist_code));
        $schools = ($schools_res && is_object($schools_res)) ? $schools_res->getResultArray() : [];
        $stats['total_schools'] = count($schools);
        
        $lp_schools = array();
        $up_schools = array();
        $hs_schools = array();
        $hss_schools = array();
        
        foreach ($schools as $school) {
            $start = $school['class_start'] !== null ? (int)$school['class_start'] : null;
            $end = $school['class_end'] !== null ? (int)$school['class_end'] : null;
            $finalized = $school['is_finalize'] == 'Y';
            
            if ($finalized) $stats['finalized_schools']++;
            
            $has_lp = false;
            $has_up = false;
            $has_hs = false;
            $has_hss = false;
            
            if ($start !== null && $end !== null) {
                $has_lp = ($start <= 4 && $end >= 1);
                $has_up = ($start <= 7 && $end >= 5);
                $has_hs = ($start <= 10 && $end >= 8);
                $has_hss = ($start <= 12 && $end >= 11);
            } else {
                // Guess category based on school name
                $name = strtoupper($school['school_name']);
                if (strpos($name, 'L.P.') !== false || strpos($name, 'L P') !== false || strpos($name, 'LPS') !== false || strpos($name, 'LP S') !== false || strpos($name, 'PRIMARY') !== false) {
                    $has_lp = true;
                }
                if (strpos($name, 'U.P.') !== false || strpos($name, 'U P') !== false || strpos($name, 'UPS') !== false || strpos($name, 'UP S') !== false) {
                    $has_up = true;
                }
                if (strpos($name, 'H.S.') !== false || strpos($name, 'H S') !== false || strpos($name, 'HIGH SCHOOL') !== false || strpos($name, 'H.S.S') !== false || strpos($name, 'HSS') !== false || strpos($name, 'VHSS') !== false || strpos($name, 'V.H.S.S') !== false) {
                    $has_hs = true;
                }
                if (strpos($name, 'H.S.S') !== false || strpos($name, 'HSS') !== false || strpos($name, 'VHSS') !== false || strpos($name, 'V.H.S.S') !== false) {
                    $has_hss = true;
                }
            }
            
            if ($has_lp) {
                $stats['lp_schools']++;
                $lp_schools[] = $school['school_code'];
                if ($finalized) $stats['lp_finalized']++;
            }
            if ($has_up) {
                $stats['up_schools']++;
                $up_schools[] = $school['school_code'];
                if ($finalized) $stats['up_finalized']++;
            }
            if ($has_hs) {
                $stats['hs_schools']++;
                $hs_schools[] = $school['school_code'];
                if ($finalized) $stats['hs_finalized']++;
            }
            if ($has_hss) {
                $stats['hss_schools']++;
                $hss_schools[] = $school['school_code'];
                if ($finalized) $stats['hss_finalized']++;
            }
        }
        
        $participant_query = "SELECT pd.gender, COUNT(*) as count
        FROM sd_participant_details pd
        INNER JOIN sd_school_master sm ON pd.school_code = sm.school_code
        WHERE sm.sub_district_code = ?
        GROUP BY pd.gender";
        
        $participants_res = $this->db->query($participant_query, array($sub_dist_code));
        $total_participants = ($participants_res && is_object($participants_res)) ? $participants_res->getResultArray() : [];
        
        foreach ($total_participants as $p) {
            $count = (int)$p['count'];
            $stats['total_participants'] += $count;
            if ($p['gender'] == 'B') $stats['boys_count'] += $count;
            if ($p['gender'] == 'G') $stats['girls_count'] += $count;
        }
        
        $category_query = "SELECT pd.class, pd.gender, COUNT(*) as count
        FROM sd_participant_details pd
        INNER JOIN sd_school_master sm ON pd.school_code = sm.school_code
        WHERE sm.sub_district_code = ?
        GROUP BY pd.class, pd.gender";
        
        $cat_res = $this->db->query($category_query, array($sub_dist_code));
        $participants = ($cat_res && is_object($cat_res)) ? $cat_res->getResultArray() : [];
        
        foreach ($participants as $p) {
            $class_val = trim($p['class']);
            $gender = $p['gender'];
            $count = (int)$p['count'];
            
            if (is_numeric($class_val)) {
                $class = (int)$class_val;
                
                if ($class >= 1 && $class <= 4) {
                    $stats['lp_total'] += $count;
                    if ($gender == 'B') $stats['lp_boys'] += $count;
                    if ($gender == 'G') $stats['lp_girls'] += $count;
                } elseif ($class >= 5 && $class <= 7) {
                    $stats['up_total'] += $count;
                    if ($gender == 'B') $stats['up_boys'] += $count;
                    if ($gender == 'G') $stats['up_girls'] += $count;
                } elseif ($class >= 8 && $class <= 10) {
                    $stats['hs_total'] += $count;
                    if ($gender == 'B') $stats['hs_boys'] += $count;
                    if ($gender == 'G') $stats['hs_girls'] += $count;
                } elseif ($class >= 11 && $class <= 12) {
                    $stats['hss_total'] += $count;
                    if ($gender == 'B') $stats['hss_boys'] += $count;
                    if ($gender == 'G') $stats['hss_girls'] += $count;
                }
            }
        }
        
        $rm_sd = $sub_dist_code ? " AND rm.sub_district_code = " . (int)$sub_dist_code : "";
        $item_query = "SELECT COUNT(DISTINCT im.item_code) as total_items,
            COUNT(DISTINCT CASE WHEN rm.is_finish = 'Y' THEN rm.item_code END) as completed_items
        FROM item_master im
        LEFT JOIN sd_result_master rm ON im.item_code = rm.item_code {$rm_sd}";
        $item_result = $this->db->query($item_query);
        if ($item_result) {
            $item_stats = $item_result->getRowArray();
            $stats['total_items'] = (int)$item_stats['total_items'];
            $stats['completed_items'] = (int)$item_stats['completed_items'];
        }
        
        return $stats;
    }

    /**
     * Advanced Sub-District CRM Analytics Engine
     * Strictly scopes all metrics, stages, and pipelines to the active sub-district
     */
    private function get_crm_analytics($sub_dist_code, $is_all_stage_alloted = false, $target_stage_date = null) {
        $stats = $this->get_statistics($sub_dist_code);
        $sd_id = (int)$sub_dist_code;

        if ($sd_id <= 0) {
            return [
                'statistics'      => $stats,
                'stages'          => [],
                'pipeline'        => [],
                'leaderboard'     => [],
                'quick_search'    => [],
                'available_dates' => [],
                'target_date'     => date('Y-m-d'),
                'counts'          => [
                    'schools'             => 0,
                    'finalized_schools'   => 0,
                    'unfinalized_schools' => 0,
                    'school_pct'          => 0,
                    'participants'        => 0,
                    'boys'                => 0,
                    'girls'               => 0,
                    'items'               => 0,
                    'completed_items'     => 0,
                    'pending_items'       => 0,
                    'item_pct'            => 0,
                    'published_results'   => 0,
                    'published_pct'       => 0,
                    'stages_count'        => 0,
                    'active_stages'       => 0,
                ]
            ];
        }

        // 1. Precise Allotted Items count (considering regional scope Y/N)
        $item_count_query = "SELECT COUNT(DISTINCT im.item_code) as total_items
            FROM item_master im
            WHERE im.is_common = 'Y'
               OR im.item_code IN (SELECT item_code FROM sd_item_allotment WHERE sub_district_code = {$sd_id})";
        $total_items_q = $this->db->query($item_count_query);
        $total_items_res = ($total_items_q && is_object($total_items_q)) ? $total_items_q->getRowArray() : [];
        $total_items = (int)($total_items_res['total_items'] ?? $stats['total_items']);
        $stats['total_items'] = $total_items;

        // 2. Completed items for this sub-district
        $completed_query = "SELECT COUNT(DISTINCT item_code) as completed_items
            FROM sd_result_master
            WHERE sub_district_code = {$sd_id} AND is_finish = 'Y'";
        $completed_q = $this->db->query($completed_query);
        $completed_res = ($completed_q && is_object($completed_q)) ? $completed_q->getRowArray() : [];
        $completed_items = (int)($completed_res['completed_items'] ?? 0);
        $stats['completed_items'] = $completed_items;

        // 3. Published results for this sub-district
        $published_query = "SELECT COUNT(DISTINCT item_code) as published_items
            FROM sd_result_publish
            WHERE sub_district_code = {$sd_id}";
        $published_q = $this->db->query($published_query);
        $published_res = ($published_q && is_object($published_q)) ? $published_q->getRowArray() : [];
        $published_items = (int)($published_res['published_items'] ?? 0);

        // 4. Allotted items to stages for this sub-district
        $allotted_stages_query = "SELECT COUNT(DISTINCT item_code) as allotted_count
            FROM sd_stage_item_master
            WHERE sub_district_code = {$sd_id}";
        $allotted_stages_q = $this->db->query($allotted_stages_query);
        $allotted_stages_res = ($allotted_stages_q && is_object($allotted_stages_q)) ? $allotted_stages_q->getRowArray() : [];
        $allotted_items_count = (int)($allotted_stages_res['allotted_count'] ?? 0);

        // 5. Scheduled items (with start_time set)
        $scheduled_query = "SELECT COUNT(DISTINCT item_code) as scheduled_count
            FROM sd_stage_item_master
            WHERE sub_district_code = {$sd_id} AND start_time IS NOT NULL";
        $scheduled_q = $this->db->query($scheduled_query);
        $scheduled_res = ($scheduled_q && is_object($scheduled_q)) ? $scheduled_q->getRowArray() : [];
        $scheduled_items_count = (int)($scheduled_res['scheduled_count'] ?? 0);

        // Discover scheduled dates for date-synchronized filtering
        $date_q = $this->db->query("
            SELECT DISTINCT DATE(start_time) as sched_date
            FROM sd_stage_item_master
            WHERE sub_district_code = {$sd_id}
              AND start_time IS NOT NULL
              AND start_time > '1970-01-01 00:00:00'
            ORDER BY sched_date ASC
        ");
        $date_rows = ($date_q && is_object($date_q)) ? $date_q->getResultArray() : [];

        $available_dates = [];
        foreach ($date_rows as $dr) {
            if (!empty($dr['sched_date'])) {
                $available_dates[] = $dr['sched_date'];
            }
        }

        $today = date('Y-m-d');
        if (!empty($target_stage_date)) {
            $active_date = $target_stage_date;
        } elseif (in_array($today, $available_dates)) {
            $active_date = $today;
        } elseif (!empty($available_dates)) {
            $active_date = $available_dates[0];
        } else {
            $active_date = $today;
        }

        // 6. Live Stages Detailed Status with Delay Engine
        $stages_query = "SELECT stage_id, stage_name, stage_desc
            FROM sd_stage_master
            WHERE sub_district_code = {$sd_id}
            ORDER BY stage_name ASC";
        $stage_q = $this->db->query($stages_query);
        $stage_rows = ($stage_q && is_object($stage_q)) ? $stage_q->getResultArray() : [];

        $live_stages = [];
        $active_stages_count = 0;
        $now = time();

        foreach ($stage_rows as $st) {
            $st_id = (int)$st['stage_id'];

            // Fetch assigned Stage Manager
            $mgr_q = $this->db->query("
                SELECT um.user_id, um.user_name, um.name
                FROM sd_stage_manager_assignment sma
                JOIN user_master um ON sma.user_id = um.user_id
                WHERE sma.stage_id = {$st_id} AND sma.sub_district_code = {$sd_id}
                LIMIT 1
            ");
            $mgr_row = ($mgr_q && is_object($mgr_q)) ? $mgr_q->getRowArray() : null;
            $stage_manager_name = !empty($mgr_row) ? ($mgr_row['name'] ?: $mgr_row['user_name']) : 'Unassigned';

            // Get events scheduled on this stage
            $events_query = "SELECT sim.si_id, sim.stage_id, sim.item_code, sim.start_time,
                                    sim.item_time, sim.status, sim.actual_start_time, sim.actual_end_time,
                                    sim.delay_minutes, sim.lot_start, sim.lot_end, sim.current_lot_no, sim.allow_stage_manager_marks,
                                    im.item_name, im.item_type, im.gender, im.is_off_stage, im.max_time,
                                    rm.is_finish
                             FROM sd_stage_item_master sim
                             INNER JOIN item_master im ON sim.item_code = im.item_code
                             LEFT JOIN sd_result_master rm ON rm.item_code = sim.item_code AND rm.sub_district_code = {$sd_id}
                             WHERE sim.stage_id = {$st_id} AND sim.sub_district_code = {$sd_id}
                             ORDER BY sim.start_time ASC, sim.item_code ASC";
            $events_q = $this->db->query($events_query);
            $all_events = ($events_q && is_object($events_q)) ? $events_q->getResultArray() : [];

            $date_events = [];
            foreach ($all_events as $ev) {
                if (!empty($ev['start_time']) && substr($ev['start_time'], 0, 10) === $active_date) {
                    $date_events[] = $ev;
                }
            }
            $eval_events = !empty($date_events) ? $date_events : $all_events;

            $total_st_events = count($eval_events);
            $completed_st_events = 0;
            $current_event = null;
            $next_event = null;
            $status = 'IDLE';
            $stage_delay_minutes = 0;

            foreach ($eval_events as $ev) {
                $sched_start = strtotime($ev['start_time']);
                $duration = (int)($ev['item_time'] ?: $ev['max_time'] ?: 30);
                $sched_end = $sched_start + ($duration * 60);

                $ev_status = !empty($ev['status']) ? $ev['status'] : 'SCHEDULED';
                $ev_delay = (int)($ev['delay_minutes'] ?? 0);
                $is_live = false;
                $is_delayed = false;

                if ($ev['is_finish'] === 'Y' || $ev_status === 'COMPLETED') {
                    $ev_status = 'COMPLETED';
                    $completed_st_events++;
                } elseif ($ev_status === 'STARTED') {
                    $ev_status = 'STARTED';
                    $is_live = true;
                    if ($ev_delay > 0) $is_delayed = true;
                } elseif ($ev_status === 'SCHEDULED') {
                    // Critical: Marked as delayed if current time > scheduled time and NOT yet started by Stage Manager
                    if ($now > $sched_start) {
                        $ev_status = 'DELAYED';
                        $is_delayed = true;
                        $ev_delay = max(0, (int)round(($now - $sched_start) / 60));
                    }
                }

                $ev_augmented = array_merge($ev, [
                    'effective_status' => $ev_status,
                    'duration_minutes' => $duration,
                    'is_live'          => $is_live,
                    'is_delayed'       => $is_delayed,
                    'computed_delay'   => $ev_delay,
                    'sched_start_fmt'  => date('h:i A', $sched_start),
                    'sched_end_fmt'    => date('h:i A', $sched_end)
                ]);

                if ($ev_status !== 'COMPLETED' && $current_event === null) {
                    $current_event = $ev_augmented;
                    $stage_delay_minutes = $ev_delay;
                } elseif ($ev_status !== 'COMPLETED' && $next_event === null) {
                    $next_event = $ev_augmented;
                }
            }

            if ($total_st_events > 0) {
                if ($completed_st_events === $total_st_events) {
                    $status = 'COMPLETED';
                } elseif ($current_event !== null) {
                    if ($current_event['effective_status'] === 'STARTED') {
                        $status = 'LIVE';
                        $active_stages_count++;
                    } elseif ($current_event['effective_status'] === 'DELAYED') {
                        $status = 'DELAYED';
                    } else {
                        $status = 'SCHEDULED';
                    }
                } else {
                    $status = 'SCHEDULED';
                }
            }

            $st_progress_pct = $total_st_events > 0 ? round(($completed_st_events / $total_st_events) * 100) : 0;

            $live_stages[] = [
                'stage_id'          => $st_id,
                'stage_name'        => $st['stage_name'],
                'stage_desc'        => $st['stage_desc'],
                'status'            => $status, // LIVE, DELAYED, SCHEDULED, COMPLETED, IDLE
                'stage_manager'     => $stage_manager_name,
                'total_events'      => $total_st_events,
                'completed_events'  => $completed_st_events,
                'progress_pct'      => $st_progress_pct,
                'delay_minutes'     => $stage_delay_minutes,
                'current_event'     => $current_event,
                'next_event'        => $next_event,
                'all_events'        => $eval_events,
            ];
        }

        // 7. Top 5 Leading Schools (Leaderboard)
        $top_schools_query = "SELECT sp.school_code, sm.school_name, SUM(sp.point) as total_points
            FROM sd_school_point_details sp
            INNER JOIN sd_school_master sm ON sp.school_code = sm.school_code
            WHERE sp.sub_district_code = {$sd_id}
            GROUP BY sp.school_code, sm.school_name
            ORDER BY total_points DESC
            LIMIT 5";
        $top_schools_q = $this->db->query($top_schools_query);
        $top_schools = ($top_schools_q && is_object($top_schools_q)) ? $top_schools_q->getResultArray() : [];

        // 8. Pipeline Progression (Pure English)
        $tot_sch = (int)$stats['total_schools'];
        $fin_sch = (int)$stats['finalized_schools'];
        $sch_pct = $tot_sch > 0 ? round(($fin_sch / $tot_sch) * 100) : 0;
        $allot_pct = $total_items > 0 ? min(100, round(($allotted_items_count / $total_items) * 100)) : 0;
        $sched_pct = $total_items > 0 ? min(100, round(($scheduled_items_count / $total_items) * 100)) : 0;
        $comp_pct = $total_items > 0 ? min(100, round(($completed_items / $total_items) * 100)) : 0;
        $pub_pct = $total_items > 0 ? min(100, round(($published_items / $total_items) * 100)) : 0;
        $export_ready = ($completed_items > 0 && $completed_items >= $total_items);

        $pipeline = [
            'phase1' => [
                'step'      => 1,
                'name'      => 'School Registration',
                'sub_title' => 'School Enrolment & Photos',
                'pct'       => $sch_pct,
                'status'    => ($sch_pct >= 100) ? 'COMPLETED' : ($fin_sch > 0 ? 'ACTIVE' : 'PENDING'),
                'detail'    => "{$fin_sch}/{$tot_sch} Finalized",
                'url'       => base_url('schools/school_entry'),
                'icon'      => 'bi-building-check'
            ],
            'phase2' => [
                'step'      => 2,
                'name'      => 'Stage Allotment',
                'sub_title' => 'Stage & Venue Allocation',
                'pct'       => $allot_pct,
                'status'    => ($is_all_stage_alloted) ? 'COMPLETED' : ($allotted_items_count > 0 ? 'ACTIVE' : 'PENDING'),
                'detail'    => "{$allotted_items_count}/{$total_items} Allotted",
                'url'       => base_url('stage/allotment'),
                'icon'      => 'bi-easel2-fill'
            ],
            'phase3' => [
                'step'      => 3,
                'name'      => 'Program Scheduling',
                'sub_title' => 'Timeline & Time Slots',
                'pct'       => $sched_pct,
                'status'    => ($sched_pct >= 100) ? 'COMPLETED' : ($scheduled_items_count > 0 ? 'ACTIVE' : 'PENDING'),
                'detail'    => "{$scheduled_items_count}/{$total_items} Scheduled",
                'url'       => base_url('stage/festival_scheduling'),
                'icon'      => 'bi-clock-history'
            ],
            'phase4' => [
                'step'      => 4,
                'name'      => 'Live Mark Entry',
                'sub_title' => 'Judge Scores & Results',
                'pct'       => $comp_pct,
                'status'    => ($comp_pct >= 100) ? 'COMPLETED' : ($completed_items > 0 ? 'ACTIVE' : ($is_all_stage_alloted ? 'READY' : 'LOCKED')),
                'detail'    => "{$completed_items}/{$total_items} Items Done",
                'url'       => $is_all_stage_alloted ? base_url('result/resultentry') : '#',
                'icon'      => 'bi-pencil-square'
            ],
            'phase5' => [
                'step'      => 5,
                'name'      => 'Result Publishing',
                'sub_title' => 'Public Result Broadcast',
                'pct'       => $pub_pct,
                'status'    => ($pub_pct >= 100) ? 'COMPLETED' : ($published_items > 0 ? 'ACTIVE' : 'PENDING'),
                'detail'    => "{$published_items}/{$total_items} Published",
                'url'       => base_url('publishresult/resultindex/resultview'),
                'icon'      => 'bi-broadcast'
            ],
            'phase6' => [
                'step'      => 6,
                'name'      => 'District Export',
                'sub_title' => 'Qualified Winners Intake',
                'pct'       => $export_ready ? 100 : 0,
                'status'    => $export_ready ? 'COMPLETED' : 'PENDING',
                'detail'    => $export_ready ? 'Ready to Export' : 'Awaiting Results',
                'url'       => base_url('report/afterresultreport/higherlevel_result'),
                'icon'      => 'bi-cloud-arrow-up-fill'
            ],
        ];

        // 9. Quick Search items (respecting stage allotment status and adding Stage Manager Console)
        $search_items = [];

        // Actions: Conditional display of Stage Allotment vs Mark Entry
        if (!$is_all_stage_alloted) {
            $search_items[] = ['type' => 'Action', 'title' => 'Stage Allotment (Item Wise)', 'sub' => 'Map items to festival stages', 'url' => base_url('stage/allotment'), 'icon' => 'bi-easel2-fill'];
        } else {
            $search_items[] = ['type' => 'Action', 'title' => 'Enter Competition Marks', 'sub' => 'Result entry portal for judges scores', 'url' => base_url('result/resultentry'), 'icon' => 'bi-award-fill'];
        }

        $search_items[] = ['type' => 'Action', 'title' => 'Stage Manager Console', 'sub' => 'Start programs, monitor delays & live dispatch', 'url' => base_url('stage/manager'), 'icon' => 'bi-broadcast-pin'];
        $search_items[] = ['type' => 'Action', 'title' => 'School Entry & Registration', 'sub' => 'Participant photo registration', 'url' => base_url('schools/school_entry'), 'icon' => 'bi-person-plus-fill'];
        $search_items[] = ['type' => 'Action', 'title' => 'School Master Profile', 'sub' => 'School directory & login details', 'url' => base_url('schools/school_master'), 'icon' => 'bi-building'];
        $search_items[] = ['type' => 'Action', 'title' => 'Auto Set Stage / Program Scheduling', 'sub' => 'Schedule event time slots', 'url' => base_url('stage/festival_scheduling'), 'icon' => 'bi-calendar-range'];
        $search_items[] = ['type' => 'Action', 'title' => 'Stage Call Sheets', 'sub' => 'Official call sheet printouts', 'url' => base_url('report/prereport/callsheet_first'), 'icon' => 'bi-card-checklist'];
        $search_items[] = ['type' => 'Action', 'title' => 'Publish Results Live', 'sub' => 'Public result announcement', 'url' => base_url('publishresult/resultindex/resultview'), 'icon' => 'bi-broadcast'];
        $search_items[] = ['type' => 'Action', 'title' => 'School-Wise Points Table', 'sub' => 'Consolidated school scores', 'url' => base_url('report/afterresultreport/school_wise_result_interface'), 'icon' => 'bi-trophy-fill'];
        $search_items[] = ['type' => 'Action', 'title' => 'Participant ID Cards (with Photo)', 'sub' => 'Print ID cards for students', 'url' => base_url('report/prereport/participant_cardindex_photo'), 'icon' => 'bi-person-badge-fill'];
        $search_items[] = ['type' => 'Action', 'title' => 'Item Master Management', 'sub' => 'View festival item catalogue', 'url' => base_url('item/item_master'), 'icon' => 'bi-list-stars'];
        $search_items[] = ['type' => 'Action', 'title' => 'Special Order & Appeals', 'sub' => 'Manage court/DPI appeals', 'url' => base_url('schools/special_order_entry'), 'icon' => 'bi-shield-exclamation'];
        $search_items[] = ['type' => 'Action', 'title' => 'Certificate Generation', 'sub' => 'Print item/school certificates', 'url' => base_url('certificate/certificate'), 'icon' => 'bi-patch-check-fill'];
        $search_items[] = ['type' => 'Action', 'title' => 'Higher Level District Export', 'sub' => 'Export 1st & 2nd winners', 'url' => base_url('report/afterresultreport/higherlevel_result'), 'icon' => 'bi-cloud-arrow-up-fill'];

        // Add sub-district stages
        foreach ($stage_rows as $st) {
            $search_items[] = [
                'type'  => 'Stage',
                'title' => $st['stage_name'],
                'sub'   => !empty($st['stage_desc']) ? $st['stage_desc'] : 'Stage Venue',
                'url'   => base_url('stage/manager?stage_id=' . $st['stage_id']),
                'icon'  => 'bi-layers-fill'
            ];
        }

        // Add sub-district items (up to 40)
        $sub_items_q = $this->db->query("SELECT im.item_code, im.item_name, im.gender, fm.fest_name
            FROM item_master im
            LEFT JOIN festival_master fm ON im.fest_id = fm.fest_id
            WHERE im.is_common = 'Y'
               OR im.item_code IN (SELECT item_code FROM sd_item_allotment WHERE sub_district_code = {$sd_id})
            ORDER BY im.item_code ASC LIMIT 40");
        $sub_items = ($sub_items_q && is_object($sub_items_q)) ? $sub_items_q->getResultArray() : [];

        foreach ($sub_items as $it) {
            $item_target_url = $is_all_stage_alloted ? base_url('result/resultentry/index/' . $it['item_code']) : base_url('stage/allotment');
            $search_items[] = [
                'type'  => 'Item',
                'title' => '#' . $it['item_code'] . ' ' . $it['item_name'],
                'sub'   => ($it['fest_name'] ?? 'Competition') . ' (' . ($it['gender'] === 'G' ? 'Girls' : ($it['gender'] === 'B' ? 'Boys' : 'General')) . ')',
                'url'   => $item_target_url,
                'icon'  => 'bi-music-note-beamed'
            ];
        }

        return [
            'statistics'      => $stats,
            'stages'          => $live_stages,
            'pipeline'        => $pipeline,
            'leaderboard'     => $top_schools,
            'quick_search'    => $search_items,
            'available_dates' => $available_dates,
            'target_date'     => $active_date,
            'counts'          => [
                'schools'               => $tot_sch,
                'finalized_schools'     => $fin_sch,
                'unfinalized_schools'   => max(0, $tot_sch - $fin_sch),
                'school_pct'            => $sch_pct,
                'participants'          => (int)$stats['total_participants'],
                'boys'                  => (int)$stats['boys_count'],
                'girls'                 => (int)$stats['girls_count'],
                'items'                 => $total_items,
                'completed_items'       => $completed_items,
                'pending_items'         => max(0, $total_items - $completed_items),
                'item_pct'              => $comp_pct,
                'published_results'     => $published_items,
                'published_pct'         => $pub_pct,
                'stages_count'          => count($live_stages),
                'active_stages'         => $active_stages_count,
            ]
        ];
    }

    /**
     * District-wide Analytics Engine
     * Scopes every metric to the active District using ONLY district (d_*) tables,
     * plus pure reference masters (sub_district_master, item_master, user_master).
     */
    private function get_district_analytics($dist_code, $is_all_stage_alloted = false, $target_stage_date = null) {
        $dist = (int)$dist_code;
        $stats = array(
            'total_schools'       => 0,
            'finalized_schools'   => 0,
            'total_participants'  => 0,
            'boys_count'          => 0,
            'girls_count'         => 0,
            'total_items'         => 0,
            'completed_items'     => 0,
            'subdistricts'        => 0
        );

// 1. Sub-districts under this district, auto-derived from the sub-district user
        //    accounts present in this district's database (user_master, i.e. the
        //    sub-districts that log into this district DB); names/venues joined
        //    from the reference master.
        $sd_q = $this->db->query("
            SELECT um.sub_district_code AS sub_district_code,
                   COALESCE(sdm.sub_district_name, CONCAT('Sub-District ', um.sub_district_code)) AS sub_district_name,
                   COALESCE(sdm.venue, '') AS venue
            FROM (
                SELECT DISTINCT sub_district_code
                FROM user_master
                WHERE rev_district_code = ?
                  AND sub_district_code > 0
            ) AS um
            LEFT JOIN sub_district_master sdm ON sdm.sub_district_code = um.sub_district_code
            ORDER BY sub_district_name ASC
        ", array($dist));
        $sd_rows = ($sd_q && is_object($sd_q)) ? $sd_q->getResultArray() : array();

        $sub_hash = array();
        foreach ($sd_rows as $r) {
            $code = (int)$r['sub_district_code'];
            $sub_hash[$code] = array(
                'sub_district_code' => $code,
                'sub_district_name' => $r['sub_district_name'],
                'venue'             => !empty($r['venue']) ? $r['venue'] : '',
                'schools'           => 0,
                'finalized_schools' => 0,
                'participants'      => 0,
                'boys'              => 0,
                'girls'             => 0,
                'items_completed'   => 0,
                'points'            => 0,
                'status'            => 'IDLE',
                'rank'              => 0,
                'is_leading'        => false,
            );
        }
        $stats['subdistricts'] = count($sub_hash);

        // 2. Schools + finalized count per sub-district (d_school_master)
        $sch_q = $this->db->query("
            SELECT sm.sub_district_code AS sdc, COUNT(*) AS schools,
                   SUM(CASE WHEN sd.is_finalize = 'Y' THEN 1 ELSE 0 END) AS finalized
            FROM d_school_master sm
            LEFT JOIN d_school_details sd ON sm.school_code = sd.school_code
            WHERE sm.rev_district_code = ? AND sm.school_status = 'A'
            GROUP BY sm.sub_district_code
        ", array($dist));
        if ($sch_q && is_object($sch_q)) {
            foreach ($sch_q->getResultArray() as $row) {
                $c = (int)$row['sdc'];
                if (!isset($sub_hash[$c])) continue;
                $sub_hash[$c]['schools'] = (int)$row['schools'];
                $sub_hash[$c]['finalized_schools'] = (int)($row['finalized'] ?? 0);
                $stats['total_schools'] += (int)$row['schools'];
                $stats['finalized_schools'] += (int)($row['finalized'] ?? 0);
            }
        }

        // 3. Participants (boys/girls) per sub-district (d_participant_details)
        $pt_q = $this->db->query("
            SELECT sub_district_code AS sdc, gender, COUNT(*) AS c
            FROM d_participant_details
            WHERE district_code = ?
            GROUP BY sub_district_code, gender
        ", array($dist));
        if ($pt_q && is_object($pt_q)) {
            foreach ($pt_q->getResultArray() as $row) {
                $c = (int)$row['sdc'];
                if (!isset($sub_hash[$c])) continue;
                $cnt = (int)$row['c'];
                $sub_hash[$c]['participants'] += $cnt;
                $stats['total_participants'] += $cnt;
                if ($row['gender'] === 'B') { $sub_hash[$c]['boys'] += $cnt; $stats['boys_count'] += $cnt; }
                if ($row['gender'] === 'G') { $sub_hash[$c]['girls'] += $cnt; $stats['girls_count'] += $cnt; }
            }
        }

        // 4. Completed (finished) items per sub-district (d_result_master)
        $ci_q = $this->db->query("
            SELECT sub_district_code AS sdc, COUNT(DISTINCT item_code) AS ci
            FROM d_result_master
            WHERE district_code = ? AND is_finish = 'Y'
            GROUP BY sub_district_code
        ", array($dist));
        if ($ci_q && is_object($ci_q)) {
            foreach ($ci_q->getResultArray() as $row) {
                $c = (int)$row['sdc'];
                if (isset($sub_hash[$c])) $sub_hash[$c]['items_completed'] = (int)$row['ci'];
            }
        }

        // 5. Championship points per sub-district (d_school_point_details, exclude withheld)
        $po_q = $this->db->query("
            SELECT sub_district_code AS sdc, SUM(point) AS tp
            FROM d_school_point_details
            WHERE district_code = ? AND (is_withheld IS NULL OR is_withheld <> 'Y')
            GROUP BY sub_district_code
        ", array($dist));
        if ($po_q && is_object($po_q)) {
            foreach ($po_q->getResultArray() as $row) {
                $c = (int)$row['sdc'];
                if (isset($sub_hash[$c])) $sub_hash[$c]['points'] = (int)$row['tp'];
            }
        }

        // 6. Rank sub-districts: points first, then participants, then schools
        $ranked = $sub_hash;
        uasort($ranked, function ($a, $b) {
            if ((int)$b['points'] !== (int)$a['points']) return (int)$b['points'] - (int)$a['points'];
            if ((int)$b['participants'] !== (int)$a['participants']) return (int)$b['participants'] - (int)$a['participants'];
            if ((int)$b['schools'] !== (int)$a['schools']) return (int)$b['schools'] - (int)$a['schools'];
            return strcmp($a['sub_district_name'], $b['sub_district_name']);
        });
        $lead_code = null;
        $pos = 1;
        foreach ($ranked as $c => $r) {
            $sub_hash[$c]['rank'] = $pos;
            if ($pos === 1 && ((int)$r['points'] > 0 || (int)$r['participants'] > 0 || (int)$r['schools'] > 0)) {
                $sub_hash[$c]['is_leading'] = true;
                $lead_code = $c;
            }
            $pos++;
        }
        $sub_districts = array_values($sub_hash);
        usort($sub_districts, function ($a, $b) { return $a['rank'] - $b['rank']; });

        // 7. District total competition items (common OR district-allotted)
        $total_qi = $this->db->query("
            SELECT COUNT(DISTINCT im.item_code) AS total_items
            FROM item_master im
            WHERE im.is_common = 'Y'
               OR im.item_code IN (SELECT item_code FROM d_item_allotment WHERE rev_district_code = ?)
        ", array($dist));
        $total_items = ($total_qi && is_object($total_qi)) ? (int)($total_qi->getRowArray()['total_items'] ?? 0) : 0;
        $stats['total_items'] = $total_items;

        $completed_items = 0;
        foreach ($sub_hash as $r) $completed_items += (int)$r['items_completed'];
        $stats['completed_items'] = $completed_items;

        // 8. Published results (d_result_publish)
        $pub_q = $this->db->query("SELECT COUNT(DISTINCT item_code) AS published_items FROM d_result_publish WHERE district_code = ?", array($dist));
        $published_items = ($pub_q && is_object($pub_q)) ? (int)($pub_q->getRowArray()['published_items'] ?? 0) : 0;

        // 9. Allotted to stages / scheduled (d_stage_item_master)
        $allot_q = $this->db->query("SELECT COUNT(DISTINCT item_code) AS c FROM d_stage_item_master WHERE district_code = ?", array($dist));
        $allotted_items_count = ($allot_q && is_object($allot_q)) ? (int)($allot_q->getRowArray()['c'] ?? 0) : 0;
        $sched_q = $this->db->query("SELECT COUNT(DISTINCT item_code) AS c FROM d_stage_item_master WHERE district_code = ? AND start_time IS NOT NULL", array($dist));
        $scheduled_items_count = ($sched_q && is_object($sched_q)) ? (int)($sched_q->getRowArray()['c'] ?? 0) : 0;

        // 10. Discover scheduled dates
        $date_q = $this->db->query("
            SELECT DISTINCT DATE(start_time) AS sched_date
            FROM d_stage_item_master
            WHERE district_code = ?
              AND start_time IS NOT NULL
              AND start_time > '1970-01-01 00:00:00'
            ORDER BY sched_date ASC
        ", array($dist));
        $available_dates = array();
        if ($date_q && is_object($date_q)) {
            foreach ($date_q->getResultArray() as $dr) {
                if (!empty($dr['sched_date'])) $available_dates[] = $dr['sched_date'];
            }
        }
        $today = date('Y-m-d');
        if (!empty($target_stage_date)) $active_date = $target_stage_date;
        elseif (in_array($today, $available_dates)) $active_date = $today;
        elseif (!empty($available_dates)) $active_date = $available_dates[0];
        else $active_date = $today;

        // 11. District live stages with manager (name + mobile)
        $stage_q = $this->db->query("
            SELECT stage_id, stage_name, stage_desc, venue
            FROM d_stage_master
            WHERE district_code = ?
            ORDER BY stage_name ASC
        ", array($dist));
        $stage_rows = ($stage_q && is_object($stage_q)) ? $stage_q->getResultArray() : array();

        $live_stages = array();
        $active_stages_count = 0;
        $now = time();

        foreach ($stage_rows as $st) {
            $st_id = (int)$st['stage_id'];
            $stage_manager_name = 'Unassigned';
            $stage_manager_mobile = '';

            $mgr_q = $this->db->query("
                SELECT um.name, um.mobile, um.user_name
                FROM d_stage_manager_assignment sma
                JOIN user_master um ON sma.user_id = um.user_id
                WHERE sma.district_code = ? AND sma.stage_id = ?
                LIMIT 1
            ", array($dist, $st_id));
            if ($mgr_q && is_object($mgr_q) && ($mgr_row = $mgr_q->getRowArray())) {
                $stage_manager_name = !empty($mgr_row['name']) ? $mgr_row['name'] : (string)$mgr_row['user_name'];
                $stage_manager_mobile = !empty($mgr_row['mobile']) ? (string)$mgr_row['mobile'] : '';
            }

            $events_query = "SELECT sim.si_id, sim.stage_id, sim.item_code, sim.start_time,
                                    sim.item_time, sim.status, sim.actual_start_time, sim.actual_end_time,
                                    sim.delay_minutes, sim.lot_start, sim.lot_end, sim.current_lot_no, sim.allow_stage_manager_marks,
                                    im.item_name, im.item_type, im.gender, im.is_off_stage, im.max_time,
                                    rm.is_finish
                             FROM d_stage_item_master sim
                             INNER JOIN item_master im ON sim.item_code = im.item_code
                             LEFT JOIN (SELECT DISTINCT item_code FROM d_result_master
                                        WHERE district_code = ? AND is_finish = 'Y') rm
                                ON rm.item_code = sim.item_code
                             WHERE sim.stage_id = ? AND sim.district_code = ?
                             ORDER BY sim.start_time ASC, sim.item_code ASC";
            $events_q = $this->db->query($events_query, array($dist, $st_id, $dist));
            $all_events = ($events_q && is_object($events_q)) ? $events_q->getResultArray() : array();

            $date_events = array();
            foreach ($all_events as $ev) {
                if (!empty($ev['start_time']) && substr($ev['start_time'], 0, 10) === $active_date) {
                    $date_events[] = $ev;
                }
            }
            $eval_events = !empty($date_events) ? $date_events : $all_events;

            $total_st_events = count($eval_events);
            $completed_st_events = 0;
            $current_event = null;
            $next_event = null;
            $status = 'IDLE';
            $stage_delay_minutes = 0;

            foreach ($eval_events as $ev) {
                $sched_start = strtotime($ev['start_time']);
                $duration = (int)($ev['item_time'] ?: $ev['max_time'] ?: 30);
                $sched_end = $sched_start + ($duration * 60);
                $ev_status = !empty($ev['status']) ? $ev['status'] : 'SCHEDULED';
                $ev_delay = (int)($ev['delay_minutes'] ?? 0);
                $is_live = false;
                $is_delayed = false;

                if ($ev['is_finish'] === 'Y' || $ev_status === 'COMPLETED') {
                    $ev_status = 'COMPLETED';
                    $completed_st_events++;
                } elseif ($ev_status === 'STARTED') {
                    $ev_status = 'STARTED';
                    $is_live = true;
                    if ($ev_delay > 0) $is_delayed = true;
                } elseif ($ev_status === 'SCHEDULED') {
                    if ($now > $sched_start) {
                        $ev_status = 'DELAYED';
                        $is_delayed = true;
                        $ev_delay = max(0, (int)round(($now - $sched_start) / 60));
                    }
                }

                $ev_augmented = array_merge($ev, array(
                    'effective_status' => $ev_status,
                    'duration_minutes' => $duration,
                    'is_live'          => $is_live,
                    'is_delayed'       => $is_delayed,
                    'computed_delay'   => $ev_delay,
                    'sched_start_fmt'  => date('h:i A', $sched_start),
                    'sched_end_fmt'    => date('h:i A', $sched_end)
                ));

                if ($ev_status !== 'COMPLETED' && $current_event === null) {
                    $current_event = $ev_augmented;
                    $stage_delay_minutes = $ev_delay;
                } elseif ($ev_status !== 'COMPLETED' && $next_event === null) {
                    $next_event = $ev_augmented;
                }
            }

            if ($total_st_events > 0) {
                if ($completed_st_events === $total_st_events) {
                    $status = 'COMPLETED';
                } elseif ($current_event !== null) {
                    if ($current_event['effective_status'] === 'STARTED') {
                        $status = 'LIVE';
                        $active_stages_count++;
                    } elseif ($current_event['effective_status'] === 'DELAYED') {
                        $status = 'DELAYED';
                    } else {
                        $status = 'SCHEDULED';
                    }
                } else {
                    $status = 'SCHEDULED';
                }
            }

            $st_progress_pct = $total_st_events > 0 ? round(($completed_st_events / $total_st_events) * 100) : 0;

            $live_stages[] = array(
                'stage_id'             => $st_id,
                'stage_name'           => $st['stage_name'],
                'stage_desc'           => $st['stage_desc'],
                'venue'                => !empty($st['venue']) ? $st['venue'] : '',
                'status'               => $status,
                'stage_manager'        => $stage_manager_name,
                'stage_manager_mobile' => $stage_manager_mobile,
                'total_events'         => $total_st_events,
                'completed_events'     => $completed_st_events,
                'progress_pct'         => $st_progress_pct,
                'delay_minutes'        => $stage_delay_minutes,
                'current_event'        => $current_event,
                'next_event'           => $next_event,
                'all_events'           => $eval_events,
            );
        }

        // 12. Pipeline percentages
        $tot_sch = (int)$stats['total_schools'];
        $fin_sch = (int)$stats['finalized_schools'];
        $sch_pct = $tot_sch > 0 ? round(($fin_sch / $tot_sch) * 100) : 0;
        $allot_pct = $total_items > 0 ? min(100, round(($allotted_items_count / $total_items) * 100)) : 0;
        $sched_pct = $total_items > 0 ? min(100, round(($scheduled_items_count / $total_items) * 100)) : 0;
        $comp_pct = $total_items > 0 ? min(100, round(($completed_items / $total_items) * 100)) : 0;
        $pub_pct = $total_items > 0 ? min(100, round(($published_items / $total_items) * 100)) : 0;
        $export_ready = ($completed_items > 0 && $completed_items >= $total_items);

        $pipeline = array(
            'phase1' => array(
                'step' => 1, 'name' => 'School Registration', 'sub_title' => 'Schools Enrolled & Photos',
                'pct' => $sch_pct, 'status' => ($sch_pct >= 100) ? 'COMPLETED' : ($fin_sch > 0 ? 'ACTIVE' : 'PENDING'),
                'detail' => "{$fin_sch}/{$tot_sch} Finalized", 'url' => base_url('district/schools/registration'), 'icon' => 'bi-building-check'
            ),
            'phase2' => array(
                'step' => 2, 'name' => 'Stage Allotment', 'sub_title' => 'Items To Stages',
                'pct' => $allot_pct, 'status' => ($allotted_items_count >= $total_items && $total_items > 0) ? 'COMPLETED' : ($allotted_items_count > 0 ? 'ACTIVE' : 'PENDING'),
                'detail' => "{$allotted_items_count}/{$total_items} Allotted", 'url' => base_url('stage/allotment'), 'icon' => 'bi-easel2-fill'
            ),
            'phase3' => array(
                'step' => 3, 'name' => 'Program Scheduling', 'sub_title' => 'Timeline & Time Slots',
                'pct' => $sched_pct, 'status' => ($sched_pct >= 100) ? 'COMPLETED' : ($scheduled_items_count > 0 ? 'ACTIVE' : 'PENDING'),
                'detail' => "{$scheduled_items_count}/{$total_items} Scheduled", 'url' => base_url('district/scheduling'), 'icon' => 'bi-clock-history'
            ),
            'phase4' => array(
                'step' => 4, 'name' => 'Live Mark Entry', 'sub_title' => 'Judge Scores & Results',
                'pct' => $comp_pct, 'status' => ($comp_pct >= 100) ? 'COMPLETED' : ($completed_items > 0 ? 'ACTIVE' : ($is_all_stage_alloted ? 'READY' : 'LOCKED')),
                'detail' => "{$completed_items}/{$total_items} Items Done", 'url' => base_url('district/resultentry'), 'icon' => 'bi-pencil-square'
            ),
            'phase5' => array(
                'step' => 5, 'name' => 'Result Publishing', 'sub_title' => 'Public Result Broadcast',
                'pct' => $pub_pct, 'status' => ($pub_pct >= 100) ? 'COMPLETED' : ($published_items > 0 ? 'ACTIVE' : 'PENDING'),
                'detail' => "{$published_items}/{$total_items} Published", 'url' => base_url('district/report/resultindex/resultview'), 'icon' => 'bi-broadcast'
            ),
            'phase6' => array(
                'step' => 6, 'name' => 'State Export', 'sub_title' => 'Higher Level Winners Intake',
                'pct' => $export_ready ? 100 : 0, 'status' => $export_ready ? 'COMPLETED' : 'PENDING',
                'detail' => $export_ready ? 'Ready to Export' : 'Awaiting Results', 'url' => base_url('district/report/afterresult_report/higherlevel_result'), 'icon' => 'bi-cloud-arrow-up-fill'
            ),
        );

        // 13. Leaderboard (top 5 sub-districts by points, fallback participants)
        $leaderboard = array();
        $lb_count = 0;
        foreach ($ranked as $c => $r) {
            if ($lb_count >= 5) break;
            $leaderboard[] = array_merge($r, array('is_leading' => ($c === $lead_code)));
            $lb_count++;
        }

        // 14. Quick search items (district actions only, no sub-district leaks)
        $search_items = array();
        $search_items[] = array('type' => 'Action', 'title' => 'District Import & Verification', 'sub' => 'Import sub-district winners', 'url' => base_url('district/import'), 'icon' => 'bi-cloud-download-fill');
        $search_items[] = array('type' => 'Action', 'title' => 'School Entry (Photo)', 'sub' => 'District participant photo registration', 'url' => base_url('district/schools/school_entry'), 'icon' => 'bi-person-plus-fill');
        $search_items[] = array('type' => 'Action', 'title' => 'Define District Stages', 'sub' => 'Stage & venue allocation', 'url' => base_url('district/stage'), 'icon' => 'bi-easel2-fill');
        $search_items[] = array('type' => 'Action', 'title' => 'Festival Scheduling', 'sub' => 'Allot & schedule items', 'url' => base_url('district/scheduling'), 'icon' => 'bi-calendar-range');
        $search_items[] = array('type' => 'Action', 'title' => 'Stage Manager Console', 'sub' => 'Live program dispatch', 'url' => base_url('stage/manager'), 'icon' => 'bi-broadcast-pin');
        $search_items[] = array('type' => 'Action', 'title' => 'District Result Entry', 'sub' => 'Judge scores for district items', 'url' => base_url('district/resultentry'), 'icon' => 'bi-award-fill');
        $search_items[] = array('type' => 'Action', 'title' => 'Publish Results Live', 'sub' => 'Public result broadcast', 'url' => base_url('district/report/resultindex/resultview'), 'icon' => 'bi-broadcast');
        $search_items[] = array('type' => 'Action', 'title' => 'District Points Table', 'sub' => 'Sub-district wise scores', 'url' => base_url('district/report/afterresult_report/school_wise_result_interface'), 'icon' => 'bi-trophy-fill');
        $search_items[] = array('type' => 'Action', 'title' => 'Higher Level Export', 'sub' => 'Qualified winners to state', 'url' => base_url('district/report/afterresult_report/higherlevel_result'), 'icon' => 'bi-cloud-arrow-up-fill');
        $search_items[] = array('type' => 'Action', 'title' => 'Certificate Generation', 'sub' => 'Print district certificates', 'url' => base_url('district/certificate'), 'icon' => 'bi-patch-check-fill');
        $search_items[] = array('type' => 'Action', 'title' => 'Sub-District Details', 'sub' => 'Manage sub-district profiles', 'url' => base_url('welcome/sub_district_details'), 'icon' => 'bi-grid-1x2-fill');

        foreach ($stage_rows as $st) {
            $search_items[] = array(
                'type' => 'Stage', 'title' => $st['stage_name'], 'sub' => !empty($st['stage_desc']) ? $st['stage_desc'] : 'District Stage',
                'url' => base_url('stage/manager?stage_id=' . $st['stage_id']), 'icon' => 'bi-layers-fill'
            );
        }

        $item_q = $this->db->query("
            SELECT im.item_code, im.item_name, im.gender, fm.fest_name
            FROM item_master im
            LEFT JOIN festival_master fm ON im.fest_id = fm.fest_id
            WHERE im.is_common = 'Y'
               OR im.item_code IN (SELECT item_code FROM d_item_allotment WHERE rev_district_code = ?)
            ORDER BY im.item_code ASC LIMIT 40
        ", array($dist));
        $d_items = ($item_q && is_object($item_q)) ? $item_q->getResultArray() : array();
        foreach ($d_items as $it) {
            $search_items[] = array(
                'type' => 'Item', 'title' => '#' . $it['item_code'] . ' ' . $it['item_name'],
                'sub' => ($it['fest_name'] ?? 'Competition') . ' (' . ($it['gender'] === 'G' ? 'Girls' : ($it['gender'] === 'B' ? 'Boys' : 'General')) . ')',
                'url' => base_url('district/resultentry'), 'icon' => 'bi-music-note-beamed'
            );
        }

        return array(
            'statistics'      => $stats,
            'stages'          => $live_stages,
            'pipeline'        => $pipeline,
            'leaderboard'     => $leaderboard,
            'quick_search'    => $search_items,
            'sub_districts'   => $sub_districts,
            'leading_code'    => $lead_code,
            'available_dates' => $available_dates,
            'target_date'     => $active_date,
            'counts'          => array(
                'subdistricts'          => $stats['subdistricts'],
                'schools'               => $tot_sch,
                'finalized_schools'     => $fin_sch,
                'unfinalized_schools'   => max(0, $tot_sch - $fin_sch),
                'school_pct'            => $sch_pct,
                'participants'          => (int)$stats['total_participants'],
                'boys'                  => (int)$stats['boys_count'],
                'girls'                 => (int)$stats['girls_count'],
                'items'                 => $total_items,
                'completed_items'       => $completed_items,
                'pending_items'         => max(0, $total_items - $completed_items),
                'item_pct'              => $comp_pct,
                'published_results'     => $published_items,
                'published_pct'         => $pub_pct,
                'stages_count'          => count($live_stages),
                'active_stages'         => $active_stages_count,
            )
        );
    }

    /**
     * AJAX endpoint: Sub-District Admin toggles mark entry permission for Stage Managers
     */
    public function toggle_manager_marks() {
        if (!$this->request) {
            $this->request = \Config\Services::request();
        }
        if (!$this->response) {
            $this->response = \Config\Services::response();
        }

        $userType = (int)$this->session->get('USER_TYPE');
        $subDistCode = (int)$this->session->get('SUB_DISTRICT');
        if (!in_array($userType, [0, 1, 2, 3])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized: Only festival administrators can toggle mark entry permissions.'
            ]);
        }

        $siId = (int)(($this->request ? $this->request->getPost('si_id') : null) ?: ($_POST['si_id'] ?? 0));
        $allow = ((($this->request ? $this->request->getPost('allow') : null) ?: ($_POST['allow'] ?? '')) === 'Y') ? 'Y' : 'N';

        if ($siId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid Stage Item ID provided.'
            ]);
        }

        $db = \Config\Database::connect();
        $db->table('sd_stage_item_master')
            ->where('si_id', $siId)
            ->where('sub_district_code', $subDistCode)
            ->update(['allow_stage_manager_marks' => $allow]);

        return $this->response->setJSON([
            'success' => true,
            'allow' => $allow,
            'message' => 'Stage Manager mark entry has been ' . ($allow === 'Y' ? 'ENABLED' : 'DISABLED') . ' successfully.',
            'csrf_token' => csrf_hash()
        ]);
    }
}
