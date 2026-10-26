<?php

namespace App\Controllers\District\Stage;

use App\Controllers\BaseController;

class Scheduling extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->Session_Model->is_user_logged(true);
        $this->load->model('District/Stage/Stage_Model');
        $this->load->model('District/Stage/Festival_Scheduling_Model');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
    }

    protected function check_district_access(): bool
    {
        $userType  = (int)$this->session->get('USER_TYPE');
        $userGroup = (string)$this->session->get('USER_GROUP');
        return ($userGroup === 'W' || $userType === 2);
    }

    public function index()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $districtCode = (int)$this->session->get('DISTRICT');
        $festId       = $this->request->getGet('fest_id') ?: 'all';
        $stageType    = $this->request->getGet('stage_type') ?: 'all';

        $items  = $this->Festival_Scheduling_Model->get_district_items($districtCode, $festId, $stageType);
        $stages = $this->Stage_Model->get_stages($districtCode);

        $this->Content = [
            'district_code' => $districtCode,
            'fest_id'       => $festId,
            'stage_type'    => $stageType,
            'items'         => $items,
            'stages'        => $stages,
        ];

        $this->template->write_view('content', 'district/stage/scheduling_view', $this->Content);
        return $this->template->load();
    }

    public function clashes()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $districtCode = (int)$this->session->get('DISTRICT');
        $festId       = $this->request->getGet('fest_id') ?: 'all';

        $clashes = $this->Festival_Scheduling_Model->detect_participant_clashes($districtCode, $festId);

        $this->Content = [
            'district_code' => $districtCode,
            'fest_id'       => $festId,
            'clashes'       => $clashes,
        ];

        $this->template->write_view('content', 'district/stage/clashes_view', $this->Content);
        return $this->template->load();
    }

    public function save_allotment()
    {
        if (!$this->check_district_access()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $districtCode = (int)$this->session->get('DISTRICT');
        $itemCode     = (int)$this->request->getPost('item_code');

        if ($itemCode <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid item code.']);
        }

        $postData = $this->request->getPost();
        $res = $this->Festival_Scheduling_Model->save_stage_item_allotment($districtCode, $itemCode, $postData);

        return $this->response->setJSON([
            'success' => $res,
            'message' => $res ? 'Stage scheduling saved successfully!' : 'Failed to save stage scheduling.',
        ]);
    }

    public function generate_chest_numbers()
    {
        if (!$this->check_district_access()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $districtCode = (int)$this->session->get('DISTRICT');
        $itemCode     = (int)($this->request->getPost('item_code') ?? 0);

        $count = $this->Festival_Scheduling_Model->generate_district_chest_numbers($districtCode, $itemCode);

        return $this->response->setJSON([
            'success' => true,
            'message' => "Generated {$count} District chest numbers successfully.",
            'count'   => $count,
        ]);
    }
}
