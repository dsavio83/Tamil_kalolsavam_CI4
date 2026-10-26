<?php

namespace App\Controllers\Publishresult;

class Publicresult extends \App\Controllers\BaseController {

    public $Contents = [];

    function __construct () {
        parent::__construct ();
        // No login enforcement for public access
        // No sidebar or menu for public access
        $this->template->add_js('js/stages.js');
        $this->load->model('publishresult/Resultindex_Model');
        $this->template->add_js('js/report/reportjs.js');
        $this->load->model('pictures/Photos_Model');
        $this->load->library('html2pdf');
    }

    function publicresult($param = '') {
        $this->resultview();
    }

    function resultvie() {
        $this->resultview();
    }

    function resultview() {
        // Cloudflare & Browser Cache headers for public result view
        header("Cache-Control: public, max-age=60, s-maxage=300, stale-while-revalidate=600");
        header("Vary: Accept-Encoding, User-Agent");

        $this->Contents = array();
        $db = \Config\Database::connect();

        // 1. Resolve District & Sub-District
        $sub_dist_code = $this->request->getGet('sub_district') ?: $this->request->getPost('sub_district');
        $dist_code = $this->request->getGet('district') ?: $this->request->getPost('district');

        $view_mode = 'home';
        if (!empty($sub_dist_code)) {
            $view_mode = 'sub_district';
            $sub_dist = $db->table('sub_district_master AS SDM')
                ->select('SDM.*, RDM.rev_district_name, EDM.edu_district_name')
                ->join('rev_district_master AS RDM', 'RDM.rev_district_code = SDM.rev_district_code', 'LEFT')
                ->join('edu_district_master AS EDM', 'EDM.edu_district_code = SDM.edu_district_code', 'LEFT')
                ->where('SDM.sub_district_code', $sub_dist_code)
                ->get()->getRowArray();
            if ($sub_dist) {
                $this->Contents['sub_district_code'] = $sub_dist['sub_district_code'];
                $this->Contents['sub_district_name'] = $sub_dist['sub_district_name'];
                $this->Contents['edu_district_name'] = $sub_dist['edu_district_name'] ?? '';
                $dist_code = $sub_dist['rev_district_code'];
                $this->Contents['district_name'] = $sub_dist['rev_district_name'];
                $this->Contents['district_code'] = $dist_code;
            }
        } elseif (!empty($dist_code)) {
            $view_mode = 'district';
            $dist = $db->table('rev_district_master')->where('rev_district_code', $dist_code)->get()->getRowArray();
            $this->Contents['district_code'] = $dist_code;
            $this->Contents['district_name'] = $dist['rev_district_name'] ?? 'Palakkad';
            $this->Contents['sub_district_code'] = '';
            $this->Contents['sub_district_name'] = '';
        } else {
            $view_mode = 'home';
            $this->Contents['district_code'] = '';
            $this->Contents['district_name'] = '';
            $this->Contents['sub_district_code'] = '';
            $this->Contents['sub_district_name'] = '';
        }

        $this->Contents['view_mode'] = $view_mode;

        // 2. Query districts and sub-districts that strictly have enrolled/participating schools
        $schoolSources = [];
        if ($db->tableExists('sd_school_master')) {
            $schoolSources[] = "SELECT school_code, sub_district_code, rev_district_code FROM sd_school_master";
        }
        if ($db->tableExists('d_school_master')) {
            $schoolSources[] = "SELECT school_code, sub_district_code, rev_district_code FROM d_school_master";
        }

        $allSubDistrictsRaw = [];
        $activeDistrictCodes = [];
        $all_subdistricts_by_district = [];
        $activeSchoolCountByDistrict = [];

        if (!empty($schoolSources)) {
            $activeSchoolsSql = implode(' UNION ', $schoolSources);
            $activeSql = "
                SELECT
                    sdm.sub_district_code,
                    sdm.sub_district_name,
                    sdm.rev_district_code,
                    COUNT(DISTINCT active_school.school_code) AS school_count
                FROM sub_district_master AS sdm
                INNER JOIN ({$activeSchoolsSql}) AS active_school
                    ON active_school.sub_district_code = sdm.sub_district_code
                GROUP BY sdm.sub_district_code, sdm.sub_district_name, sdm.rev_district_code
                ORDER BY sdm.sub_district_name ASC
            ";
            try {
                $allSubDistrictsRaw = $db->query($activeSql)->getResultArray();
            } catch (\Throwable $e) {}
        }

        foreach ($allSubDistrictsRaw as $sdr) {
            $districtId = (int) $sdr['rev_district_code'];
            $subDistrictId = (int) $sdr['sub_district_code'];
            $activeDistrictCodes[$districtId] = $districtId;
            $activeSchoolCountByDistrict[$districtId] = ($activeSchoolCountByDistrict[$districtId] ?? 0) + (int) $sdr['school_count'];
            $all_subdistricts_by_district[$districtId][] = [
                'code' => $subDistrictId,
                'sub_district_code' => $subDistrictId,
                'name' => $sdr['sub_district_name'],
                'sub_district_name' => $sdr['sub_district_name'],
                'rev_district_code' => $districtId,
                'school_count' => (int) $sdr['school_count']
            ];
        }

        $tamilDistMap = [
            1  => 'திருவனந்தபுரம்',
            2  => 'கொல்லம்',
            3  => 'பத்தனம்திட்டா',
            4  => 'ஆலப்புழா',
            5  => 'கோட்டயம்',
            6  => 'இடுக்கி',
            7  => 'எர்ணாகுளம்',
            8  => 'திருச்சூர்',
            9  => 'பாலக்காடு',
            10 => 'மலப்புறம்',
            11 => 'கோழிக்கோடு',
            12 => 'வயநாடு',
            13 => 'கண்ணூர்',
            14 => 'காசர்கோடு',
        ];

        // Load only districts having active participating schools
        $all_districts = [];
        $district_highlights = [];

        if (!empty($activeDistrictCodes)) {
            $activeDists = $db->table('rev_district_master')
                ->whereIn('rev_district_code', array_values($activeDistrictCodes))
                ->orderBy('rev_district_code', 'ASC')
                ->get()->getResultArray();

            foreach ($activeDists as $ad) {
                $c = (int) $ad['rev_district_code'];
                $dObj = [
                    'code' => $c,
                    'name' => $ad['rev_district_name'],
                    'tamil' => $tamilDistMap[$c] ?? $ad['rev_district_name'],
                    'tamil_name' => $tamilDistMap[$c] ?? $ad['rev_district_name'],
                    'sub_districts_count' => count($all_subdistricts_by_district[$c] ?? []),
                    'school_count' => (int) ($activeSchoolCountByDistrict[$c] ?? 0),
                    'declared_count' => 0
                ];
                $all_districts[] = $dObj;
                $district_highlights[] = $dObj;
            }
        }

        // Calculate declared items count for home district cards
        if ($view_mode === 'home') {
            foreach ($district_highlights as $idx => $dh) {
                $c = (int) $dh['code'];
                try {
                    $decCount = $db->table('d_result_master')
                        ->where('district_code', $c)
                        ->whereIn('rank', [1, 2, 3])
                        ->select('COUNT(DISTINCT item_code) AS dec_cnt')
                        ->get()->getRowArray();
                    $district_highlights[$idx]['declared_count'] = (int) ($decCount['dec_cnt'] ?? 0);
                } catch (\Throwable $e) {
                    $district_highlights[$idx]['declared_count'] = 0;
                }
            }
        }

        $this->Contents['all_districts'] = $all_districts;
        $this->Contents['eligible_districts'] = $all_districts;
        $this->Contents['district_highlights'] = $district_highlights;
        $this->Contents['all_subdistricts_by_district'] = $all_subdistricts_by_district;

        // Sub-districts for selected district (strictly those with participating schools)
        $selectedSubDistricts = [];
        if (!empty($dist_code) && isset($all_subdistricts_by_district[(int) $dist_code])) {
            $selectedSubDistricts = $all_subdistricts_by_district[(int) $dist_code];
        }
        $this->Contents['sub_districts'] = $selectedSubDistricts;
        $this->Contents['district_all_subdistricts'] = $selectedSubDistricts;

        // 4. Fetch Festivals (LP, UP, HS, HSS)
        $fest = $this->General_Model->get_data('festival_master', 'fest_id,fest_name', '', 'fest_id');
        $this->Contents['fest'] = $fest ?: array();

        // 5. Winners Scroller (Image 2 Style - Rank 1, 2, 3 Winners with Photo)
        $this->Contents['winners'] = $this->Resultindex_Model->get_winners_with_photos(
            ($view_mode === 'sub_district') ? null : ($dist_code ?: null),
            ($view_mode === 'sub_district') ? $sub_dist_code : null,
            30
        );

        // 6. Top 3 Podium (Gold Cup Standings)
        if ($view_mode === 'sub_district') {
            $this->Contents['top_podium'] = $this->Resultindex_Model->leading_schools(null, $sub_dist_code, null, 3);
            $this->Contents['podium_title'] = 'Leading Schools in ' . ($this->Contents['sub_district_name'] ?? '');
        } else {
            $this->Contents['top_podium'] = $this->Resultindex_Model->leading_subdistricts($dist_code ?: null, null, 3);
            $this->Contents['podium_title'] = 'Leading Sub-Districts in ' . ($this->Contents['district_name'] ?? '');
        }

        // 7. Aggregate School Points Breakdown (LP, UP, HS, HSS, Total)
        $this->Contents['school_points_breakdown'] = $this->Resultindex_Model->get_school_points_breakdown(
            ($view_mode === 'sub_district') ? null : ($dist_code ?: null),
            ($view_mode === 'sub_district') ? $sub_dist_code : null,
            50
        );

        // 8. Aggregate Sub-District Points Breakdown (for District View)
        if ($view_mode === 'district') {
            $this->Contents['subdistrict_points_breakdown'] = $this->Resultindex_Model->get_subdistrict_points_breakdown($dist_code);
        } else {
            $this->Contents['subdistrict_points_breakdown'] = [];
        }

        // 9. Completed Items List for Live Search
        $this->Contents['completed_items'] = $this->Resultindex_Model->get_completed_items_with_stats(
            ($view_mode === 'sub_district') ? null : ($dist_code ?: null),
            ($view_mode === 'sub_district') ? $sub_dist_code : null
        );

        // 10. Leading Stats & Championship Cards
        $this->Contents['leading_subdistricts'] = $this->Resultindex_Model->leading_subdistricts($dist_code ?: null, null, 5);
        $this->Contents['all_leading_subdistricts'] = $this->Resultindex_Model->leading_subdistricts($dist_code ?: null, null, null);
        $this->Contents['leading_schools'] = $this->Resultindex_Model->leading_schools($dist_code ?: null, $sub_dist_code ?: null, null, 5);
        $this->Contents['all_leading_schools'] = $this->Resultindex_Model->leading_schools($dist_code ?: null, $sub_dist_code ?: null, null, null);
        $this->Contents['latest_results'] = $this->Resultindex_Model->latest_results($dist_code ?: null, $sub_dist_code ?: null, 5);
        $this->Contents['all_latest_results'] = $this->Resultindex_Model->latest_results($dist_code ?: null, $sub_dist_code ?: null, 50);

        $this->Contents['all_subdistrict_points'] = $this->Contents['all_leading_subdistricts'] ?: [];
        $this->Contents['all_school_points'] = $this->Contents['all_leading_schools'] ?: [];

        $this->load->view('report/publishresult/resultview_public', $this->Contents);
    }

    function item_codes() {
        $session = session();
        $user_id = $session->get('USERID');

        // Require login for admin/backend item code directory
        if (empty($user_id)) {
            return redirect()->to(base_url('login'));
        }

        $db = \Config\Database::connect();

        $sub_dist_code = $session->get('SUB_DISTRICT');
        $dist_code = $session->get('DISTRICT');
        $user_type = (int)$session->get('USER_TYPE');
        $school_code = $session->get('SCHOOL_CODE');

        // Schools always follow the sub-district stored on the school record.
        if (!empty($school_code) && ($user_type === 5 || empty($sub_dist_code))) {
            $sch = $db->table('sd_school_master')->select('sub_district_code, rev_district_code')->where('school_code', $school_code)->get()->getRowArray();
            if ($sch) {
                $sub_dist_code = $sch['sub_district_code'];
                $dist_code = $sch['rev_district_code'];
            }
        }
        // Only Webmaster and IT/State Admin can browse the full item directory.
        // Every other role stays locked to the scope resolved from its session/school.
        $is_full_access = in_array($user_type, [0, 1], true);
        $scope_sub_dist_code = $sub_dist_code;
        $scope_dist_code = $dist_code;

        if ($is_full_access) {
            $sub_dist_code = null;
            $dist_code = null;
            $scope_missing = false;
        } elseif ($user_type === 2) {
            $sub_dist_code = null;
            $dist_code = $scope_dist_code;
            $scope_missing = empty($dist_code);
        } else {
            $sub_dist_code = $scope_sub_dist_code;
            $dist_code = $scope_dist_code;
            $scope_missing = empty($sub_dist_code);
        }

        // Keep the page heading tied to the logged-in user's sub-district.
        $heading_sub_dist_code = $is_full_access ? null : $scope_sub_dist_code;

        // If sub_district is specified, automatically detect its parent district
        if (!empty($sub_dist_code) && empty($dist_code)) {
            $sdRow = $db->table('sub_district_master')->select('rev_district_code')->where('sub_district_code', (int)$sub_dist_code)->get()->getRowArray();
            if ($sdRow) {
                $dist_code = $sdRow['rev_district_code'];
            }
        }

        $fest_id = $this->request->getGet('category') ?: $this->request->getGet('fest_id');
        $item_type = $this->request->getGet('type') ?: $this->request->getGet('item_type');
        $search = $this->request->getGet('search');

        $hideLp = $user_type === 2;
        if ($hideLp && (int)$fest_id === 1) {
            $fest_id = null;
        }
        $items = $scope_missing
            ? []
            : $this->Resultindex_Model->get_item_codes_filtered($dist_code, $sub_dist_code, $fest_id, $item_type, $search, $hideLp);

        // Compute summary counts
        $totalCount = count($items);
        $lpCount = 0; $upCount = 0; $hsCount = 0; $hssCount = 0;
        foreach ($items as $it) {
            $fid = (int)($it['fest_id'] ?? 0);
            if ($fid === 1) $lpCount++;
            elseif ($fid === 2) $upCount++;
            elseif ($fid === 3) $hsCount++;
            elseif ($fid === 4) $hssCount++;
        }

        // Handle AJAX requests for real-time live filtering without page reloads
        if ($this->request->isAJAX() || $this->request->getGet('ajax') == '1') {
            return $this->response->setJSON([
                'status' => 'success',
                'district' => $dist_code,
                'sub_district' => $sub_dist_code,
                'category' => $fest_id,
                'type' => $item_type,
                'search' => $search,
                'hide_lp' => $hideLp,
                'counts' => [
                    'total' => $totalCount,
                    'lp' => $lpCount,
                    'up' => $upCount,
                    'hs' => $hsCount,
                    'hss' => $hssCount,
                ],
                'items' => $items
            ]);
        }

        // Resolve the logged-in user's sub-district for the repeated print heading.
        $sub_dist_name = '';
        if (!empty($heading_sub_dist_code)) {
            $sdInfo = $db->table('sub_district_master')->select('sub_district_name')->where('sub_district_code', (int)$heading_sub_dist_code)->get()->getRowArray();
            if ($sdInfo) $sub_dist_name = $sdInfo['sub_district_name'];
        }
        $dist_name = '';
        if (!empty($dist_code)) {
            $dInfo = $db->table('rev_district_master')->select('rev_district_name')->where('rev_district_code', (int)$dist_code)->get()->getRowArray();
            if ($dInfo) $dist_name = $dInfo['rev_district_name'];
        }

        $this->Contents = array();
        $this->Contents['items'] = $items;
        $this->Contents['sub_district_name'] = $sub_dist_name;
        $this->Contents['district_name'] = $dist_name;
        $this->Contents['selected_district'] = $dist_code;
        $this->Contents['selected_sub_district'] = $sub_dist_code;
        $this->Contents['selected_fest_id'] = $fest_id;
        $this->Contents['selected_item_type'] = $item_type;
        $this->Contents['hide_lp'] = $hideLp;
        $this->Contents['search_query'] = $search;
        $this->Contents['counts'] = [
            'total' => $totalCount,
            'lp' => $lpCount,
            'up' => $upCount,
            'hs' => $hsCount,
            'hss' => $hssCount,
        ];

        // This endpoint is a print/download page: return only the item table PDF.
        $activeFestival = $db->table('kalolsavam_master')
            ->select('kalolsavam_year')
            ->where('status', 'O')
            ->get()
            ->getRowArray();
        $festivalYear = trim((string) ($activeFestival['kalolsavam_year'] ?? ''));
        if ($festivalYear === '') {
            $year = (int) date('Y');
            $festivalYear = $year . '-' . ($year + 1);
        }
        if (preg_match('/^(\d{4})-(\d{2})$/', $festivalYear, $yearParts)) {
            $festivalYear = $yearParts[1] . '-20' . $yearParts[2];
        }
        $titleSubDistrict = $sub_dist_name !== '' ? $sub_dist_name . ' Sub-district Kalaivizha' : 'Item Codes Kalaivizha';
        $this->Contents['pdf_title'] = trim($titleSubDistrict . ' ' . $festivalYear);

        // Keep the directory as an HTML preview; the browser's Print action
        // produces the clean table-only PDF through the print stylesheet.
        $this->load->model('Session_Model');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->template->write_view('content', 'report/publishresult/item_codes_plain', $this->Contents);
        $this->template->load();
    }

    function get_public_item_codes_ajax() {
        $distCode = (int) ($this->request->getPost('district') ?: $this->request->getGet('district') ?: 0);
        $subDistCode = (int) ($this->request->getPost('sub_district') ?: $this->request->getGet('sub_district') ?: 0);

        if ($subDistCode <= 0 && $distCode <= 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Please select a District and Sub-District.',
                'items' => []
            ]);
        }

        $items = $this->Resultindex_Model->get_item_codes_filtered($distCode ?: null, $subDistCode ?: null);

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setJSON([
                'status' => 'success',
                'district' => $distCode,
                'sub_district' => $subDistCode,
                'count' => count($items ?: []),
                'items' => $items ?: []
            ]);
    }

    function get_item_results_ajax() {
        $item_code = $this->request->getPost('item_code') ?: $this->request->getGet('item_code');
        $dist_code = $this->request->getPost('district') ?: $this->request->getGet('district');
        $sub_dist_code = $this->request->getPost('sub_district') ?: $this->request->getGet('sub_district');

        if (empty($item_code)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Item code is required.']);
        }

        $results = $this->Resultindex_Model->get_item_results_detail($item_code, $dist_code, $sub_dist_code);
        return $this->response->setJSON([
            'status' => 'success',
            'item_code' => $item_code,
            'count' => count($results),
            'results' => $results
        ]);
    }

    function result_declared() {
        $fesid = $this->input->post('hidfestId');
        $this->Contents = array();
        $this->template->write('title', '');
        $this->Contents['items'] = $this->Resultindex_Model->festval_allitems($fesid);
        $this->Contents['festid'] = $fesid;

        $this->load->view('report/publishresult/result_declared', $this->Contents);
    }

    function get_results_by_item() {
        $this->load->helper('url');
        $fesid = $this->input->post('festid');
        $itemid = $this->input->post('itemid');
        $details = $this->Resultindex_Model->result_rank($fesid, $itemid);
        $this->Contents['details'] = $details;
        $this->Contents['Photo'] = $this->Photos_Model->get_rank_Photo_public($this->Contents);
        // Return HTML for the table
        $html = $this->load->view('report/publishresult/result_declared_table', $this->Contents, true);
        echo $html;
    }

    function schoolpoints() {
        $festid = $this->input->post('hidfestId') ?: $this->request->getGet('fest_id') ?: 1;
        $dist_code = $this->request->getGet('district') ?: null;
        $sub_dist_code = $this->request->getGet('sub_district') ?: null;
        $this->Contents = array();
        $this->template->write('title', '');
        $details = $this->Resultindex_Model->schoolpoints($festid);

        $this->Contents['details'] = $details;
        $this->Contents['festid'] = $festid;
        $this->load->view('report/publishresult/schoolpoints', $this->Contents);
    }

    function subdistrictpoints() {
        $festid = $this->input->post('hidfestId') ?: $this->request->getGet('fest_id') ?: 1;
        $dist_code = $this->request->getGet('district') ?: null;
        $this->Contents = array();
        $this->template->write('title', '');
        $details = $this->Resultindex_Model->subdistrictpoints($festid, $dist_code);

        $this->Contents['details'] = $details;
        $this->Contents['festid'] = $festid;
        $this->load->view('report/publishresult/subdistrictpoints', $this->Contents);
    }

    function allresults() {
        $this->Contents = array();
        $this->template->write('title', '');
        $details = $this->Resultindex_Model->allresults();

        $this->Contents['details'] = $details;
        $this->Contents['Photo'] = $this->Photos_Model->get_rank_Photo_public($this->Contents);
        $this->load->view('report/publishresult/allresults', $this->Contents);
    }

    function allfestschools() {
        $this->Contents = array();
        $this->template->write('title', '');
        $details = $this->Resultindex_Model->allfestschoolpoints();

        $this->Contents['details'] = $details;
        $this->load->view('report/publishresult/schoolpoints', $this->Contents);
    }

    function festval_status() {
        $this->Contents = array();
        $details1 = $this->Resultindex_Model->festval_status1();
        $details2 = $this->Resultindex_Model->festival_status2();
        $this->Contents['details1'] = $details1;
        $this->Contents['details2'] = $details2;
        $this->load->view('report/publishresult/festival_status_public', $this->Contents);
    }

    function festival_allitem() {
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->input->post('hidfestid');
        $totitem = $this->Resultindex_Model->festval_allitems($fest);
        $this->Contents['totitem'] = $totitem;
        $this->load->view('report/publishresult/festival_allitem', $this->Contents);
    }

    function finished_item() {
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->input->post('hidfestid');
        $totitem = $this->Resultindex_Model->finished_allitems($fest);
        $this->Contents['totitem'] = $totitem;
        $this->load->view('report/publishresult/finished_allitem', $this->Contents);
    }

    function incomplete_item() {
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->input->post('hidfestid');
        $totitem = $this->Resultindex_Model->incomplete_allitems($fest);
        $this->Contents['totitem'] = $totitem;
        $this->load->view('report/publishresult/incomplete_item', $this->Contents);
    }

}
