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

        $view_mode = 'district';
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
        }

        // Default to Palakkad (District 9) if not specified
        if (empty($dist_code)) {
            $dist_code = 9;
        }

        $this->Contents['sub_district_code'] = $this->Contents['sub_district_code'] ?? '';
        $this->Contents['sub_district_name'] = $this->Contents['sub_district_name'] ?? '';
        $this->Contents['view_mode'] = $view_mode;

        if (empty($this->Contents['district_name'])) {
            $dist = $db->table('rev_district_master')->where('rev_district_code', $dist_code)->get()->getRowArray();
            $this->Contents['district_code'] = $dist_code;
            $this->Contents['district_name'] = $dist['rev_district_name'] ?? 'Palakkad';
        }

        // 2. Fetch Dynamic Target Districts from Database
        $targetDistrictCodes = [9, 6, 12];
        $allDistrictsList = $db->table('rev_district_master')
            ->whereIn('rev_district_code', $targetDistrictCodes)
            ->orderBy('rev_district_code', 'ASC')
            ->get()->getResultArray();

        $tamilDistMap = [
            9 => 'பாலக்காடு',
            6 => 'இடுக்கி',
            12 => 'வயநாடு'
        ];
        $all_districts = [];
        foreach ($allDistrictsList as $ad) {
            $c = (int)$ad['rev_district_code'];
            $all_districts[] = [
                'code' => $c,
                'name' => $ad['rev_district_name'],
                'tamil' => $tamilDistMap[$c] ?? $ad['rev_district_name']
            ];
        }
        $this->Contents['all_districts'] = $all_districts;

        // 3. Fetch Sub-Districts for the current District
        $this->Contents['sub_districts'] = $db->table('sub_district_master')
            ->where('rev_district_code', $dist_code)
            ->orderBy('sub_district_name', 'ASC')
            ->get()->getResultArray();

        // 4. Fetch Festivals (LP, UP, HS, HSS)
        $fest = $this->General_Model->get_data('festival_master', 'fest_id,fest_name', '', 'fest_id');
        $this->Contents['fest'] = $fest ?: array();

        // 5. Winners Scroller (Image 2 Style - Rank 1, 2, 3 Winners with Photo)
        $this->Contents['winners'] = $this->Resultindex_Model->get_winners_with_photos(
            ($view_mode === 'sub_district') ? null : $dist_code,
            ($view_mode === 'sub_district') ? $sub_dist_code : null,
            30
        );

        // 6. Top 3 Podium (Gold Cup Standings)
        if ($view_mode === 'sub_district') {
            $this->Contents['top_podium'] = $this->Resultindex_Model->leading_schools(null, $sub_dist_code, null, 3);
            $this->Contents['podium_title'] = 'Leading Schools in ' . $this->Contents['sub_district_name'];
        } else {
            $this->Contents['top_podium'] = $this->Resultindex_Model->leading_subdistricts($dist_code, null, 3);
            $this->Contents['podium_title'] = 'Leading Sub-Districts in ' . $this->Contents['district_name'];
        }

        // 7. Aggregate School Points Breakdown (LP, UP, HS, HSS, Total)
        $this->Contents['school_points_breakdown'] = $this->Resultindex_Model->get_school_points_breakdown(
            ($view_mode === 'sub_district') ? null : $dist_code,
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
            ($view_mode === 'sub_district') ? null : $dist_code,
            ($view_mode === 'sub_district') ? $sub_dist_code : null
        );

        // 10. Legacy Leading Stats (for backward compatibility)
        $this->Contents['leading_subdistricts'] = $this->Resultindex_Model->leading_subdistricts($dist_code, null, 5);
        $this->Contents['leading_schools'] = $this->Resultindex_Model->leading_schools($dist_code, $sub_dist_code, null, 5);
        $this->Contents['latest_results'] = $this->Resultindex_Model->latest_results($dist_code, $sub_dist_code, 5);

        $this->load->view('report/publishresult/resultview_public', $this->Contents);
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
        //print_r($details);

        $this->Contents['details'] = $details;
        $this->Contents['Photo'] = $this->Photos_Model->get_rank_Photo_public($this->Contents);
        $this->load->view('report/publishresult/allresults', $this->Contents);
    }

    function allfestschools() {

        //$festid=$this->input->post('hidfestId');
        $this->Contents = array();
        $this->template->write('title', '');
        $details = $this->Resultindex_Model->allfestschoolpoints();
        //print_r($details);

        $this->Contents['details'] = $details;
        $this->load->view('report/publishresult/schoolpoints', $this->Contents);
    }

    function festval_status() {

        $this->Contents = array();
        $details1 = $this->Resultindex_Model->festval_status1();
        $details2 = $this->Resultindex_Model->festival_status2();
        //print_r($details);
        $this->Contents['details1'] = $details1;
        $this->Contents['details2'] = $details2;
        $this->load->view('report/publishresult/festival_status_public', $this->Contents);
    }

    function festival_allitem() {

        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->input->post('hidfestid');
        $totitem = $this->Resultindex_Model->festval_allitems($fest);
        //print_r($totitem);
        $this->Contents['totitem'] = $totitem;
        $this->load->view('report/publishresult/festival_allitem', $this->Contents);
    }

    function finished_item() {

        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->input->post('hidfestid');
        $totitem = $this->Resultindex_Model->finished_allitems($fest);
        //print_r($totitem);
        $this->Contents['totitem'] = $totitem;
        $this->load->view('report/publishresult/finished_allitem', $this->Contents);
    }

    function incomplete_item() {

        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->input->post('hidfestid');
        $totitem = $this->Resultindex_Model->incomplete_allitems($fest);
        //print_r($totitem);
        $this->Contents['totitem'] = $totitem;
        $this->load->view('report/publishresult/incomplete_item', $this->Contents);
    }

}

?>
