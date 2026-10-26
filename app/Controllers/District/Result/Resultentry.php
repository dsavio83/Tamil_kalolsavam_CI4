<?php

namespace App\Controllers\District\Result;

use App\Controllers\BaseController;
use HTML2PDF;

class Resultentry extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->Session_Model->is_user_logged(true);
        $this->template->add_js('js/result.js');
        $this->template->add_js('js/common.js');
        $this->load->library('html2pdf');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->load->model('District/Result/Result_Model');
        $this->load->model('District/Stage/Festival_Scheduling_Model');
        $this->load->model('General_Model');
    }

    protected function check_district_access(): bool
    {
        $userType  = (int)$this->session->get('USER_TYPE');
        $userGroup = (string)$this->session->get('USER_GROUP');
        $district  = (int)$this->session->get('DISTRICT');
        return ($userGroup === 'W' || $userType === 2) && $district > 0;
    }

    public function index($message = array(), $item_code = '')
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $districtCode = (int)$this->session->get('DISTRICT');

        // When routed from district/resultentry/index/(:any) or district/resultentry/(:num)
        if (!is_array($message)) {
            if (empty($item_code) && !empty($message)) {
                $item_code = (string)$message;
            }
            $message = array();
        }

        if (is_array($message) && !empty($message['error_array'])) {
            if (count($message['error_array']) > 0) {
                $error_msg = implode('\n', $message['error_array']);
                $this->Content['error_msg'] = $error_msg;
                $this->template->write('error', $error_msg);
            }
        }

        if (is_array($message) && !empty($message['success_array'])) {
            if (count($message['success_array']) > 0) {
                $success_msg = implode('\n', $message['success_array']);
                $this->Content['success_msg'] = $success_msg;
                $this->template->write('message', $success_msg);
            }
        }

        if (is_array($item_code)) {
            $item_code = '';
        }
        if (empty($item_code)) {
            $item_code = $this->input->post('hidItemId') 
                ?: $this->input->post('txtItemCode') 
                ?: $this->request->getPost('code')
                ?: $this->request->getGet('item_code')
                ?: $this->request->getGet('code')
                ?: $this->input->get('item_code')
                ?: $this->input->get('code');
        }

        if (!isset($this->Content)) {
            $this->Content = array();
        }

        if ($item_code) {
            $this->Content['item_code'] = (string)$item_code;
            $this->Content['selected_item'] = $this->Result_Model->get_item_details_result((int)$item_code, $districtCode);
            $this->Content['selected_item_list'] = $this->Result_Model->get_item_result_list((int)$item_code, $districtCode);
            $this->Content['item_participants'] = $this->Result_Model->get_item_participants((int)$item_code, $districtCode);

            if (is_array($this->Content['selected_item_list']) && count($this->Content['selected_item_list']) > 0) {
                $this->Content['show_conirm_button'] = ($this->Result_Model->check_confirm_item((int)$item_code, $districtCode)) ? 'yes' : 'no';
            } else {
                $this->Content['show_conirm_button'] = 'yes';
            }

            $this->Content['absentee_list'] = $this->Result_Model->get_absentee_list((int)$item_code, $districtCode);
            $this->Content['add_edit'] = ($this->Content['show_conirm_button'] == 'no') ? 'no' : 'yes';
        }

        if (!empty($this->Content['selected_result']) || !empty($this->Content['preserve_inputs'])) {
            $this->Content['add_edit'] = 'yes';
        }

        $settings = $this->resolve_lot_settings($item_code, $districtCode);
        $this->Content['lot_start'] = $settings['lot_start'];
        $this->Content['lot_end'] = $settings['lot_end'];
        $this->Content['max_code_digits'] = $settings['max_code_digits'];
        $this->Content['district_code'] = $districtCode;

        $this->Content['item_code'] = $this->Content['item_code'] ?? (string)$item_code;
        $this->Content['selected_item'] = $this->Content['selected_item'] ?? false;
        $this->Content['selected_item_list'] = $this->Content['selected_item_list'] ?? array();
        $this->Content['selected_result'] = $this->Content['selected_result'] ?? array();
        $this->Content['show_conirm_button'] = $this->Content['show_conirm_button'] ?? 'yes';
        $this->Content['absentee_list'] = $this->Content['absentee_list'] ?? false;
        $this->Content['add_edit'] = $this->Content['add_edit'] ?? 'yes';
        $this->Content['item_participants'] = $this->Content['item_participants'] ?? array();

        $this->template->write_view('content', 'district/result/resultentry_view', $this->Content);
        return $this->template->load();
    }

    private function resolve_lot_settings($item_code, $districtCode)
    {
        $lot_start = 1;
        $lot_end = null;
        $max_code_digits = 3;

        if (!empty($item_code) && $districtCode > 0) {
            $db = \Config\Database::connect();
            $stageItem = $db->table('d_stage_item_master')
                ->where('item_code', (int)$item_code)
                ->where('district_code', $districtCode)
                ->get()->getRowArray();
            if (!empty($stageItem)) {
                $lot_start = !empty($stageItem['lot_start']) ? (int)$stageItem['lot_start'] : 1;
                $lot_end = !empty($stageItem['lot_end']) ? (int)$stageItem['lot_end'] : null;
                $max_code_digits = $lot_end ? strlen((string)$lot_end) : 3;
            }
        }

        return [
            'lot_start'       => $lot_start,
            'lot_end'         => $lot_end,
            'max_code_digits' => $max_code_digits,
        ];
    }

    public function get_item_details_result()
    {
        $districtCode = (int)$this->session->get('DISTRICT');
        $item_id = $this->input->post('code') 
            ?: $this->request->getPost('code') 
            ?: $this->input->post('hidItemId') 
            ?: $this->input->post('txtItemCode');

        if (!isset($this->Content)) {
            $this->Content = array();
        }

        if (empty($item_id)) {
            $this->Content['item_code'] = '';
            $this->Content['selected_item'] = false;
            $this->Content['selected_item_list'] = array();
            $this->Content['selected_result'] = array();
            $this->Content['show_conirm_button'] = 'yes';
            $this->Content['absentee_list'] = false;
            $this->Content['item_participants'] = array();
            $this->Content['add_edit'] = 'yes';
            $this->Content['lot_start'] = 1;
            $this->Content['lot_end'] = null;
            $this->Content['max_code_digits'] = 3;
            $this->Content['district_code'] = $districtCode;
            $this->response->setHeader('X-CSRF-TOKEN', csrf_hash());
            return $this->response->setBody(view('district/result/resultentry_view', $this->Content));
        }

        $this->Content['item_code'] = (string)$item_id;
        $this->Content['selected_item'] = $this->Result_Model->get_item_details_result((int)$item_id, $districtCode);
        $this->Content['selected_item_list'] = $this->Result_Model->get_item_result_list((int)$item_id, $districtCode);
        $this->Content['selected_result'] = array();
        if (is_array($this->Content['selected_item_list']) && count($this->Content['selected_item_list']) > 0) {
            $this->Content['show_conirm_button'] = ($this->Result_Model->check_confirm_item((int)$item_id, $districtCode)) ? 'yes' : 'no';
        } else {
            $this->Content['show_conirm_button'] = 'yes';
        }
        $this->Content['absentee_list'] = $this->Result_Model->get_absentee_list((int)$item_id, $districtCode);
        $this->Content['item_participants'] = $this->Result_Model->get_item_participants((int)$item_id, $districtCode);
        $this->Content['add_edit'] = ($this->Content['show_conirm_button'] == 'no') ? 'no' : 'yes';

        $settings = $this->resolve_lot_settings($item_id, $districtCode);
        $this->Content['lot_start'] = $settings['lot_start'];
        $this->Content['lot_end'] = $settings['lot_end'];
        $this->Content['max_code_digits'] = $settings['max_code_digits'];
        $this->Content['district_code'] = $districtCode;

        $this->response->setHeader('X-CSRF-TOKEN', csrf_hash());
        return $this->response->setBody(view('district/result/resultentry_view', $this->Content));
    }

    public function save_result_entry()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $is_get = (strtoupper(request()->getMethod()) === 'GET');
        $raw_reg_no = $this->input->post('txt_reg_no') ?: request()->getPost('txt_reg_no');
        $code_no = $this->input->post('txt_code_no') ?: request()->getPost('txt_code_no');
        $item_code = $this->input->post('hidItemId') 
            ?: request()->getPost('hidItemId') 
            ?: $this->input->post('txtItemCode') 
            ?: request()->getPost('txtItemCode');

        if ($is_get || (empty($raw_reg_no) && empty($code_no))) {
            return redirect()->to(base_url('district/resultentry' . (!empty($item_code) ? '/index/' . $item_code : '')));
        }

        $this->Content['show_conirm_button'] = 'yes';
        $this->Content['add_edit'] = 'yes';

        $message = $this->Result_Model->save_result_details($code_no, $item_code);

        // AJAX response
        if ($this->request->isAJAX()) {
            $this->response->setHeader('X-CSRF-TOKEN', csrf_hash());
            if (empty($message['error_array']) && !empty($message['success_array'])) {
                return $this->response->setJSON([
                    'status'    => 'success',
                    'message'   => implode("\n", $message['success_array']),
                    'item_code' => $item_code
                ]);
            } else {
                return $this->response->setJSON([
                    'status'    => 'error',
                    'message'   => implode("\n", $message['error_array'] ?? ['Failed to save result.']),
                    'item_code' => $item_code
                ]);
            }
        }

        // Post-Redirect-Get pattern
        if (empty($message['error_array']) && !empty($message['success_array'])) {
            session()->setFlashdata('message', implode('\n', $message['success_array']));
            return redirect()->to(base_url('district/resultentry' . (!empty($item_code) ? '/index/' . $item_code : '')));
        }

        if (!empty($message['error_array'])) {
            $this->Content['preserve_inputs'] = true;
        }
        $this->index($message, $item_code);
    }

    public function edit_result_entry($rm_id = '')
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $rm_id = $rm_id 
            ?: $this->input->post('hidResultId') 
            ?: $this->request->getPost('hidResultId') 
            ?: $this->request->getGet('id') 
            ?: $this->request->getGet('hidResultId');

        $this->Content['show_conirm_button'] = 'yes';
        $this->Content['add_edit'] = 'yes';
        $this->Content['selected_result'] = $this->Result_Model->get_selected_result_details((int)$rm_id);

        $item_code = $this->input->post('hidItemId') 
            ?: $this->request->getPost('hidItemId') 
            ?: $this->request->getGet('item_code');

        if (empty($item_code) && !empty($this->Content['selected_result'][0]['item_code'])) {
            $item_code = $this->Content['selected_result'][0]['item_code'];
        }
        $this->index(array(), $item_code);
    }

    public function delete_result_entry($rm_id = '')
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $rm_id = $rm_id 
            ?: $this->input->post('hidResultId') 
            ?: $this->request->getPost('hidResultId') 
            ?: $this->request->getGet('id') 
            ?: $this->request->getGet('hidResultId');

        if ($rm_id) {
            $db = \Config\Database::connect();
            $db->table('d_result_master')->where('rm_id', (int)$rm_id)->delete();
        }

        $this->Content['show_conirm_button'] = 'yes';
        $this->Content['add_edit'] = 'yes';
        $item_code = $this->input->post('hidItemId') 
            ?: $this->request->getPost('hidItemId') 
            ?: $this->request->getGet('item_code');

        // AJAX support: return JSON so the frontend can refresh in place without a blank page.
        if ($this->request->isAJAX()) {
            $this->response->setHeader('X-CSRF-TOKEN', csrf_hash());
            return $this->response->setJSON([
                'status'    => 'success',
                'message'   => 'Result entry deleted successfully.',
                'item_code' => $item_code,
                'rm_id'     => $rm_id
            ]);
        }

        session()->setFlashdata('message', 'Result entry deleted successfully.');
        return redirect()->to(base_url('district/resultentry' . (!empty($item_code) ? '/index/' . $item_code : '')));
    }

    public function confirm_result_entry()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $districtCode = (int)$this->session->get('DISTRICT');
        $item_code = $this->input->post('hidItemId') ?: $this->request->getPost('hidItemId');

        if (!empty($item_code)) {
            $item_code = (int)$item_code;
            if ($this->Result_Model->set_confirm_result($item_code, $districtCode)) {
                $findFirstrank = $this->Result_Model->findTwoFirstRank($item_code, $districtCode);
                if (!empty($findFirstrank[0]['firstRanks']) && $findFirstrank[0]['firstRanks'] > 1) {
                    $this->Result_Model->reset_result_confirmation_status($item_code, $districtCode);
                    session()->setFlashdata('error', 'There are ' . $findFirstrank[0]['firstRanks'] . ' first ranks. Only one First rank is allowed.');
                } else {
                    session()->setFlashdata('message', 'District result details confirmed successfully.');
            \App\Libraries\PublicResultCache::invalidate($districtCode, null);
                }
            } else {
                session()->setFlashdata('error', 'Failed to confirm District result details.');
            }

            return redirect()->to(base_url('district/resultentry/index/' . $item_code));
        }

        return redirect()->to(base_url('district/resultentry'));
    }

    public function printConfidentialReport()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $itemcode = $this->input->post('hidItemId') ?: $this->request->getPost('hidItemId');
        if (!empty($itemcode)) {
            $itemcode = (int)$itemcode;
            $districtCode = (int)$this->session->get('DISTRICT');

            $this->load->model('District/Report/Timefest_Model');
            $this->Contents = array();
            $this->template->write('title', '');

            $festival     = $this->input->post('hidFestcode') ?: $this->request->getPost('hidFestcode');
            $result_no    = $this->Timefest_Model->fetch_resultNo($itemcode);
            $festname     = $this->Timefest_Model->fetch_festname($festival);
            $retdat       = $this->Timefest_Model->timeoffest_result($itemcode);
            $participated = $this->Timefest_Model->timeoffest_count($itemcode);
            $absentee_list= $this->Timefest_Model->timeoffest_result_absentee($itemcode);
            $absentee     = explode(',', (string)$absentee_list);

            if (count($retdat) > 0) {
                $this->Contents['judjes_count'] = $retdat[0]['no_of_judges'] ?? 3;
                $this->Contents['itemcode']     = $itemcode;
                $this->Contents['festname']     = $festname;
                $this->Contents['retdat']       = $retdat;
                $this->Contents['participated'] = $participated;
                $this->Contents['absentee']     = $absentee;
                $this->Contents['result_no']    = $result_no;

                $content = $this->load->view('district/report/timeof_fest_reportpdf/timefest_result_confidential', $this->Contents, true);

                $html2pdf = new HTML2PDF('P', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('District_Confidential_Report.pdf', 'I');
                exit;
            } else {
                session()->setFlashdata('error', 'No confirmed score data found to print confidential report.');
                return redirect()->to(base_url('district/resultentry/index/' . $itemcode));
            }
        }

        return redirect()->to(base_url('district/resultentry'));
    }

    public function item_result_list()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $districtCode = (int)$this->session->get('DISTRICT');
        $this->Contents = array();

        // Get festival master excluding LP (fest_id = 1)
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', 'fest_id != 1', '', 'fest_id');
        if (isset($fest[1])) {
            unset($fest[1]);
        }
        $this->Contents['fest'] = $fest;

        $this->template->write_view('content', 'district/result/festival_type_list', $this->Contents);

        $fest_id = $this->input->post('cmbFestType') ?: $this->input->get('cmbFestType') ?: $this->request->getGet('fest_id');
        if ($fest_id && (int)$fest_id !== 1) {
            $this->Contents = array();
            $this->Contents['fest'] = $this->General_Model->get_data('festival_master', 'fest_name', array('fest_id' => $fest_id));
            $itempart = $this->Result_Model->get_item_result((int)$fest_id, $districtCode);
            $single   = $this->Result_Model->get_item_result_single((int)$fest_id, $districtCode);
            $this->Contents['itempart'] = $itempart;
            $this->Contents['single']   = $single;

            if ((is_array($itempart) && count($itempart) > 0) || (is_array($single) && count($single) > 0)) {
                $this->template->write_view('content', 'district/result/item_result_details', $this->Contents);
            }
        }

        return $this->template->load();
    }

    // Retain compatibility methods
    public function save_mark()
    {
        return $this->save_result_entry();
    }

    public function confirm_result()
    {
        return $this->confirm_result_entry();
    }

    public function toggle_withhold()
    {
        if (!$this->check_district_access()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $districtCode   = (int)$this->session->get('DISTRICT');
        $itemCode       = (int)$this->request->getPost('item_code');
        $dParticipantId = (int)$this->request->getPost('d_participant_id');
        $isWithheld     = trim((string)$this->request->getPost('is_withheld'));

        $success = $this->Result_Model->toggle_withhold($itemCode, $districtCode, $dParticipantId, $isWithheld);

        if ($success) {
            \App\Libraries\PublicResultCache::invalidate($districtCode, null);
        }

        return $this->response->setJSON([
            'success' => $success,
            'message' => $success ? 'Withholding status updated.' : 'Failed to update withholding status.',
        ]);
    }
}

