<?php

namespace App\Controllers\District\Stage;

use App\Controllers\BaseController;

class Stage extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->Session_Model->is_user_logged(true);
        $this->load->model('District/Stage/Stage_Model');
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
        $this->Content = [
            'district_code' => $districtCode,
            'stages'        => $this->Stage_Model->get_stages($districtCode),
            'stage_names'   => $this->Stage_Model->get_stage_name_array(),
        ];

        $this->template->write_view('content', 'district/stage/stage_view', $this->Content);
        return $this->template->load();
    }

    public function add_stage()
    {
        if (!$this->check_district_access()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $districtCode = (int)$this->session->get('DISTRICT');
        $stageName    = trim($this->request->getPost('txtStageName') ?? '');

        if (empty($stageName)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Stage name is required.']);
        }

        if ($this->Stage_Model->check_stagename_exists($stageName, $districtCode)) {
            return $this->response->setJSON(['success' => false, 'message' => "Stage '{$stageName}' already exists in this District."]);
        }

        $id = $this->Stage_Model->save_stage_details($districtCode, $this->request->getPost());
        return $this->response->setJSON(['success' => true, 'message' => 'District stage saved successfully!', 'stage_id' => $id]);
    }

    public function update_stage()
    {
        if (!$this->check_district_access()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $districtCode = (int)$this->session->get('DISTRICT');
        $stageId      = (int)$this->request->getPost('hidStageId');
        $stageName    = trim($this->request->getPost('txtStageName') ?? '');

        if ($this->Stage_Model->check_stagename_exists($stageName, $districtCode, $stageId)) {
            return $this->response->setJSON(['success' => false, 'message' => "Stage name '{$stageName}' already exists."]);
        }

        $this->Stage_Model->update_stage_details($stageId, $districtCode, $this->request->getPost());
        return $this->response->setJSON(['success' => true, 'message' => 'District stage updated successfully!']);
    }

    public function delete_stage()
    {
        if (!$this->check_district_access()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $districtCode = (int)$this->session->get('DISTRICT');
        $stageId      = (int)$this->request->getPost('stage_id');

        $this->Stage_Model->delete_stage_details($stageId, $districtCode);
        return $this->response->setJSON(['success' => true, 'message' => 'District stage deleted successfully.']);
    }
}
