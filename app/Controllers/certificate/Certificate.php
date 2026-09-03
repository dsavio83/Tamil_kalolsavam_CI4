<?php

namespace App\Controllers\Certificate;


class Certificate extends \App\Controllers\BaseController {

    function __construct () {
        parent::__construct ();
        $this->Session_Model->is_user_logged(true);
        $this->template->write_view('menu', 'menu', '');
        $this->load->model('certificate/Certificate_Model');
        $this->load->model('pictures/Photos_Model');
        $this->template->add_js('js/certificate.js');
        $this->template->add_js('js/common.js');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->Contents = array();
    }

    function index() {
        if ($this->Session_Model->check_user_permission(47) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }


        $certificate_type_array = $this->General_Model->prepare_select_box_data('certificate_type', 'ct_id,type', '', '', 'ct_id');

        $sub_dist_code = $this->session->get('SUB_DISTRICT');
        $dist_code = $this->session->get('DISTRICT');

        $font_array = $this->Certificate_Model->get_font_array();
        $font_size_array = $this->Certificate_Model->get_font_size_array();
        $line_height_array = $this->Certificate_Model->get_line_height_array();

        $certificate_template = $this->Certificate_Model->get_certificate_details($dist_code, $sub_dist_code);

        $this->Contents['certificate_type_array'] = $certificate_type_array;
        $this->Contents['font_array'] = $font_array;
        $this->Contents['font_size_array'] = $font_size_array;
        $this->Contents['line_height_array'] = $line_height_array;

        $this->Contents['certificate_template'] = $certificate_template;
        $this->template->write_view('content', 'certificate/certificate_template', $this->Contents);
        $this->template->load();
    }

    function save_certificate_template() {
        if ($this->Session_Model->check_user_permission(47) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $sub_dist_code = $this->session->get('SUB_DISTRICT');
        $dist_code = $this->session->get('DISTRICT');
        $this->Certificate_Model->save_certificate_details($dist_code, $sub_dist_code);

        $this->session->setFlashdata('message', 'Certificate template calibration saved successfully!');
        return redirect()->to(base_url('certificate/certificate'));
    }

    function test_certificate() {
        if ($this->Session_Model->check_user_permission(47) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $sub_dist_code = $this->session->get('SUB_DISTRICT');
        $dist_code = $this->session->get('DISTRICT');

        $certificate_template = $this->Certificate_Model->get_certificate_details($dist_code, $sub_dist_code);

        $page_style = ($certificate_template[0]['page_style'] == 'P') ? 'P' : 'L';
        $line_height = ($certificate_template[0]['line_height']) ? $certificate_template[0]['line_height'] : '1';
        $top_margin = ($certificate_template[0]['top_margin']) ? $certificate_template[0]['top_margin'] : '1';
        $left_margin = ($certificate_template[0]['left_margin']) ? $certificate_template[0]['left_margin'] : '1';
        $right_margin = ($certificate_template[0]['right_margin']) ? $certificate_template[0]['right_margin'] : '1';

        $label_print = ($certificate_template[0]['label_print']) ? $certificate_template[0]['label_print'] : 'N';

        $this->load->library('_fpdf/fpdf');

        $this->fpdf->Open();

        $this->fpdf->AddPage($page_style, 'A4');
        $this->fpdf->SetFont('Arial', 'B', 14);

        $this->fpdf->SetMargins($left_margin, $top_margin, $right_margin);


        if ($certificate_template[0]['title_x'] and $certificate_template[0]['title_y']) {
            $this->fpdf->SetFont($certificate_template[0]['title_font'], 'B', $certificate_template[0]['title_size']);
            $this->fpdf->SetXY($certificate_template[0]['title_x'], $certificate_template[0]['title_y']);
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, ' ' . 'KERALA SCHOOL TAMIL KALAIVIZHA');
            } else {
                $this->fpdf->Write($line_height, 'KERALA SCHOOL TAMIL KALAIVIZHA');
            }
        }
        if ($certificate_template[0]['name_x'] and $certificate_template[0]['name_y']) {
            $this->fpdf->SetFont($certificate_template[0]['name_font'], 'B', $certificate_template[0]['name_size']);
            $this->fpdf->SetXY($certificate_template[0]['name_x'], $certificate_template[0]['name_y']);
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Name : ' . 'DOMINIC SAVIO JOHN JOSEPH ANTHONY');
            } else {
                $this->fpdf->Write($line_height, 'DOMINIC SAVIO JOHN JOSEPH ANTHONY');
            }
        }
        if ($certificate_template[0]['item_x'] and $certificate_template[0]['item_y']) {
            $this->fpdf->SetFont($certificate_template[0]['item_font'], 'B', $certificate_template[0]['item_size']);
            $this->fpdf->SetXY($certificate_template[0]['item_x'], $certificate_template[0]['item_y']);
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Item : ' . 'PATRIOTIC SONG');
            } else {
                $this->fpdf->Write($line_height, 'PATRIOTIC SONG');
            }
        }
        if ($certificate_template[0]['category_x'] and $certificate_template[0]['category_y']) {
            $this->fpdf->SetFont($certificate_template[0]['category_font'], 'B', $certificate_template[0]['category_size']);
            $this->fpdf->SetXY($certificate_template[0]['category_x'], $certificate_template[0]['category_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Category : ' . 'UP Tamil Kalaivizha');
            } else {
                $this->fpdf->Write($line_height, 'UP Tamil Kalaivizha');
            }
        }
        if ($certificate_template[0]['grade_x'] and $certificate_template[0]['grade_y']) {
            $this->fpdf->SetFont($certificate_template[0]['grade_font'], 'B', $certificate_template[0]['grade_size']);
            $this->fpdf->SetXY($certificate_template[0]['grade_x'], $certificate_template[0]['grade_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Grade : ' . 'A');
            } else {
                $this->fpdf->Write($line_height, 'A');
            }
        }
        if ($certificate_template[0]['class_x'] and $certificate_template[0]['class_y']) {
            $this->fpdf->SetFont($certificate_template[0]['class_font'], 'B', $certificate_template[0]['class_size']);
            $this->fpdf->SetXY($certificate_template[0]['class_x'], $certificate_template[0]['class_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Class : ' . '5');
            } else {
                $this->fpdf->Write($line_height, '5');
            }
        }
        if ($certificate_template[0]['school_x'] and $certificate_template[0]['school_y']) {
            $this->fpdf->SetFont($certificate_template[0]['school_font'], 'B', $certificate_template[0]['school_size']);
            $this->fpdf->SetXY($certificate_template[0]['school_x'], $certificate_template[0]['school_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'School : ' . "St Xavier's HS Parisakkal");
            } else {
                $this->fpdf->Write($line_height, "St Xavier's HS Parisakkal");
            }
        }
        if ($certificate_template[0]['sub_dist_x'] and $certificate_template[0]['sub_dist_y']) {
            $this->fpdf->SetFont($certificate_template[0]['sub_dist_font'], 'B', $certificate_template[0]['sub_dist_size']);
            $this->fpdf->SetXY($certificate_template[0]['sub_dist_x'], $certificate_template[0]['sub_dist_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Chittur');
            } else {
                $this->fpdf->Write($line_height, 'Chittur');
            }
        }
        if ($certificate_template[0]['dist_x'] and $certificate_template[0]['dist_y']) {
            $this->fpdf->SetFont($certificate_template[0]['dist_font'], 'B', $certificate_template[0]['dist_size']);
            $this->fpdf->SetXY($certificate_template[0]['dist_x'], $certificate_template[0]['dist_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Idukki');
            } else {
                $this->fpdf->Write($line_height, 'Idukki');
            }
        }
        if ($certificate_template[0]['date_x'] and $certificate_template[0]['date_y']) {
            $this->fpdf->SetFont($certificate_template[0]['date_font'], 'B', $certificate_template[0]['date_size']);
            $this->fpdf->SetXY($certificate_template[0]['date_x'], $certificate_template[0]['date_y']);

            $this->fpdf->Write($line_height, '21 Nov 2024');
        }
        if ($certificate_template[0]['place_x'] and $certificate_template[0]['place_y']) {
            $this->fpdf->SetFont($certificate_template[0]['place_font'], 'B', $certificate_template[0]['place_size']);
            $this->fpdf->SetXY($certificate_template[0]['place_x'], $certificate_template[0]['place_y']);

            $this->fpdf->Write($line_height, 'Chittur');
        }

        if ($certificate_template[0]['ehs_X'] and $certificate_template[0]['ehs_Y']) {
            $this->fpdf->SetFont($certificate_template[0]['ehs_font'], 'B', $certificate_template[0]['ehs_size']);
            $this->fpdf->SetXY($certificate_template[0]['ehs_X'], $certificate_template[0]['ehs_Y']);
            $this->fpdf->Write($line_height, 'Eligible For Higher Level');
        }

        if ($page_style == 'L') {
            $this->fpdf->Line(5, 5, 5, 200);
            $this->fpdf->Line(5, 5, 255, 5);
            $this->fpdf->Line(255, 5, 255, 30);
            $this->fpdf->SetXY(10, 10);
            $this->fpdf->SetFont('Arial', 'B', 10);
            $this->fpdf->Write(0, "(From Left , From Top)");
            for ($i = 5; $i < 260; $i = $i + 25) {
                for ($j = 30; $j < 200; $j = $j + 25) {
                    $this->fpdf->SetXY($i, $j - 2);
                    $this->fpdf->SetFont('Arial', 'B', 8);
                    $this->fpdf->Write(0, "(" . $i . ", " . $j . ")");
                    $this->fpdf->Line($i, $j, $i, 200);
                    $this->fpdf->Line($i, $j, 200, $j);
                }
            }
            $this->fpdf->Line(5, 200, 255, 200);
        }//End if($page_style   ==  'L')
        else {
            $this->fpdf->Line(5, 5, 5, 180);
            $this->fpdf->Line(5, 5, 180, 5);
            $this->fpdf->Line(180, 5, 180, 30);
            $this->fpdf->SetXY(10, 10);
            $this->fpdf->SetFont('Arial', 'B', 10);
            $this->fpdf->Write(0, "(From Left , From Top)");
            for ($i = 5; $i < 200; $i = $i + 25) {
                for ($j = 30; $j < 250; $j = $j + 25) {
                    $this->fpdf->SetXY($i, $j - 2);
                    $this->fpdf->SetFont('Arial', 'B', 8);
                    $this->fpdf->Write(0, "(" . $i . ", " . $j . ")");
                    $this->fpdf->Line($i, $j, $i, 180);
                    $this->fpdf->Line($i, $j, 180, $j);
                }
            }
            $this->fpdf->Line(5, 230, 180, 230);
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        $this->fpdf->Output('certificate_preview_graph.pdf', 'I');
        exit;
    }

    function test_certificare_withoutgraph() {

        if ($this->Session_Model->check_user_permission(47) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $sub_dist_code = $this->session->get('SUB_DISTRICT');
        $dist_code = $this->session->get('DISTRICT');

        $certificate_template = $this->Certificate_Model->get_certificate_details($dist_code, $sub_dist_code);

        $page_style = ($certificate_template[0]['page_style'] == 'P') ? 'P' : 'L';
        $line_height = ($certificate_template[0]['line_height']) ? $certificate_template[0]['line_height'] : '1';
        $top_margin = ($certificate_template[0]['top_margin']) ? $certificate_template[0]['top_margin'] : '1';
        $left_margin = ($certificate_template[0]['left_margin']) ? $certificate_template[0]['left_margin'] : '1';
        $right_margin = ($certificate_template[0]['right_margin']) ? $certificate_template[0]['right_margin'] : '1';

        $label_print = ($certificate_template[0]['label_print']) ? $certificate_template[0]['label_print'] : 'N';

        $this->load->library('_fpdf/fpdf');

        $this->fpdf->Open();

        $this->fpdf->AddPage($page_style, 'A4');
        $this->fpdf->SetFont('Arial', 'B', 14);

        $this->fpdf->SetMargins($left_margin, $top_margin, $right_margin);

        
        if ($certificate_template[0]['title_x'] and $certificate_template[0]['title_y']) {
            $this->fpdf->SetFont($certificate_template[0]['title_font'], 'B', $certificate_template[0]['title_size']);
            $this->fpdf->SetXY($certificate_template[0]['title_x'], $certificate_template[0]['title_y']);
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, ' ' . 'KERALA SCHOOL TAMIL KALAIVIZHA');
            } else {
                $this->fpdf->Write($line_height, 'KERALA SCHOOL TAMIL KALAIVIZHA');
            }
        }
        if ($certificate_template[0]['name_x'] and $certificate_template[0]['name_y']) {
            $this->fpdf->SetFont($certificate_template[0]['name_font'], 'B', $certificate_template[0]['name_size']);
            $this->fpdf->SetXY($certificate_template[0]['name_x'], $certificate_template[0]['name_y']);
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Name : ' . 'DOMINIC SAVIO JOHN JOSEPH ANTHONY');
            } else {
                $this->fpdf->Write($line_height, 'DOMINIC SAVIO JOHN JOSEPH ANTHONY');
            }
        }
        if ($certificate_template[0]['item_x'] and $certificate_template[0]['item_y']) {
            $this->fpdf->SetFont($certificate_template[0]['item_font'], 'B', $certificate_template[0]['item_size']);
            $this->fpdf->SetXY($certificate_template[0]['item_x'], $certificate_template[0]['item_y']);
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Item : ' . 'PATRIOTIC SONG');
            } else {
                $this->fpdf->Write($line_height, 'PATRIOTIC SONG');
            }
        }
        if ($certificate_template[0]['category_x'] and $certificate_template[0]['category_y']) {
            $this->fpdf->SetFont($certificate_template[0]['category_font'], 'B', $certificate_template[0]['category_size']);
            $this->fpdf->SetXY($certificate_template[0]['category_x'], $certificate_template[0]['category_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Category : ' . 'UP Tamil Kalaivizha');
            } else {
                $this->fpdf->Write($line_height, 'UP Tamil Kalaivizha');
            }
        }
        if ($certificate_template[0]['grade_x'] and $certificate_template[0]['grade_y']) {
            $this->fpdf->SetFont($certificate_template[0]['grade_font'], 'B', $certificate_template[0]['grade_size']);
            $this->fpdf->SetXY($certificate_template[0]['grade_x'], $certificate_template[0]['grade_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Grade : ' . 'A');
            } else {
                $this->fpdf->Write($line_height, 'A');
            }
        }
        if ($certificate_template[0]['class_x'] and $certificate_template[0]['class_y']) {
            $this->fpdf->SetFont($certificate_template[0]['class_font'], 'B', $certificate_template[0]['class_size']);
            $this->fpdf->SetXY($certificate_template[0]['class_x'], $certificate_template[0]['class_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Class : ' . '5');
            } else {
                $this->fpdf->Write($line_height, '5');
            }
        }
        if ($certificate_template[0]['school_x'] and $certificate_template[0]['school_y']) {
            $this->fpdf->SetFont($certificate_template[0]['school_font'], 'B', $certificate_template[0]['school_size']);
            $this->fpdf->SetXY($certificate_template[0]['school_x'], $certificate_template[0]['school_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'School : ' . "St Xavier's HS Parisakkal");
            } else {
                $this->fpdf->Write($line_height, "St Xavier's HS Parisakkal");
            }
        }
        if ($certificate_template[0]['sub_dist_x'] and $certificate_template[0]['sub_dist_y']) {
            $this->fpdf->SetFont($certificate_template[0]['sub_dist_font'], 'B', $certificate_template[0]['sub_dist_size']);
            $this->fpdf->SetXY($certificate_template[0]['sub_dist_x'], $certificate_template[0]['sub_dist_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Chittur');
            } else {
                $this->fpdf->Write($line_height, 'Chittur');
            }
        }
        if ($certificate_template[0]['dist_x'] and $certificate_template[0]['dist_y']) {
            $this->fpdf->SetFont($certificate_template[0]['dist_font'], 'B', $certificate_template[0]['dist_size']);
            $this->fpdf->SetXY($certificate_template[0]['dist_x'], $certificate_template[0]['dist_y']);
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'District : ' . 'Idukki');
            } else {
                $this->fpdf->Write($line_height, 'Idukki');
            }
        }

        if ($certificate_template[0]['date_x'] and $certificate_template[0]['date_y']) {
            $this->fpdf->SetFont($certificate_template[0]['date_font'], 'B', $certificate_template[0]['date_size']);
            $this->fpdf->SetXY($certificate_template[0]['date_x'], $certificate_template[0]['date_y']);

            $this->fpdf->Write($line_height, '21 Nov 2024');
        }
        if ($certificate_template[0]['place_x'] and $certificate_template[0]['place_y']) {
            $this->fpdf->SetFont($certificate_template[0]['place_font'], 'B', $certificate_template[0]['place_size']);
            $this->fpdf->SetXY($certificate_template[0]['place_x'], $certificate_template[0]['place_y']);

            $this->fpdf->Write($line_height, 'Chittur');
        }
        if ($certificate_template[0]['ehs_X'] and $certificate_template[0]['ehs_Y']) {
            $this->fpdf->SetFont($certificate_template[0]['ehs_font'], 'B', $certificate_template[0]['ehs_size']);
            $this->fpdf->SetXY($certificate_template[0]['ehs_X'], $certificate_template[0]['ehs_Y']);
            $this->fpdf->Write($line_height, 'Eligible For Higher Level');
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        $this->fpdf->Output('certificate_preview_clean.pdf', 'I');
        exit;
    }

    function list_item_wise() {
        if ($this->Session_Model->check_user_permission(48) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', '', 'fest_id');
        $this->Contents['fest'] = $fest;

        $fest_id = $this->input->post('cmbFestType') ?: $this->request->getGet('cmbFestType');
        if (empty($fest_id) && !empty($fest)) {
            $fest_keys = array_keys($fest);
            $fest_id = reset($fest_keys);
        }
        $this->Contents['selected_fest_id'] = $fest_id;

        if ($fest_id) {
            $fest_info = $this->General_Model->get_data('festival_master', 'fest_name', array('fest_id' => $fest_id));
            $this->Contents['fest_name'] = $fest_info[0]['fest_name'] ?? '';
            $this->Contents['itempart'] = $this->Certificate_Model->get_item_certificate($fest_id);
        } else {
            $this->Contents['itempart'] = [];
        }

        $this->template->write_view('content', 'certificate/item_certificate_details', $this->Contents);
        $this->template->load();
    }

    function get_certificate_itemwise() {
        if ($this->Session_Model->check_user_permission(48) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $item_id = $this->input->post('hidItemId') ?: $this->request->getGet('item_code') ?: $this->request->getGet('hidItemId');
        if (!empty($item_id)) {
            $this->db->where('item_code', $item_id);
            $this->db->set('is_certificate_printed', 1);
            $this->db->update('result_master');

            $this->Contents['item_id'] = $item_id;
            $this->Contents['item_details'] = $this->General_Model->get_data('item_master', 'item_code, item_name, item_type, fest_id', array('item_code' => $item_id));
            $this->Contents['item_type'] = $this->Certificate_Model->is_group_item($item_id);
            $this->Contents['javascript_code'] = '';
            if ('G' == $this->Contents['item_type']) {
                $this->Contents['javascript_code'] = ' onchange="javascript:getPinnaniParticipant(this.value);"';
                $this->Contents['dropdown_title'] = 'Group Captain';
            } else if ('S' == $this->Contents['item_type']) {
                $this->Contents['dropdown_title'] = 'Participants';
            }
            $this->Contents['captain_detail'] = $this->Certificate_Model->get_captains_details($item_id);
            $this->Contents['item_participants'] = $this->Certificate_Model->get_participant_details($item_id, 'all', $this->Contents['item_type']);
            $this->template->write_view('content', 'certificate/certificate_itemwise', $this->Contents);
            $this->template->load();
        } else {
            return redirect()->to(base_url('certificate/certificate/list_item_wise'));
        }
    }

    function get_certificate_pdf() {
        if ($this->Session_Model->check_user_permission(48) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $item_id = $this->input->post('hidItemId') ?: $this->request->getGet('hidItemId');
        if (!empty($item_id)) {
            $sub_dist_code = $this->session->get('SUB_DISTRICT');
            $dist_code = $this->session->get('DISTRICT');
            $certificate_template = $this->Certificate_Model->get_certificate_details($dist_code, $sub_dist_code);

            $captain_id = $this->input->post('captain_id') ?: $this->request->getGet('captain_id');
            $item_type = $this->input->post('hidItemType') ?: $this->request->getGet('hidItemType') ?: $this->Certificate_Model->is_group_item($item_id);
            $participant_id = $this->input->post('participant_id') ?: $this->request->getGet('participant_id') ?: '';

            if (empty($captain_id) || $captain_id === '0' || $captain_id === 0 || $captain_id === 'all') {
                $participant_array = $this->Certificate_Model->get_participant_details($item_id, 'all', $item_type);
            } else {
                $participant_array = $this->Certificate_Model->get_participant_details($item_id, $captain_id, $item_type, $participant_id);
            }

            if (empty($participant_array) || !is_array($participant_array)) {
                $this->session->setFlashdata('error', 'No eligible participants found to generate certificates.');
                return redirect()->to(base_url('certificate/certificate/list_item_wise'));
            }

            $this->load->library('_fpdf/fpdf');
            foreach ($participant_array as $p) {
                $this->create_certificate($p, $certificate_template);
            }

            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="certificates.pdf"');
            $this->fpdf->Output('certificates.pdf', 'I');
            exit;
        } else {
            return redirect()->to(base_url('certificate/certificate/list_item_wise'));
        }
    }

    function create_certificate($participant_array, $certificate_template) {
        if ($this->Session_Model->check_user_permission(48) == false) {
            return;
        }
        if (@$participant_array['spo_id'] > 0 and $participant_array['grade'] == '') {
            return;
        }

        // Fallback default coordinates if template is uncalibrated
        if (empty($certificate_template) || !is_array($certificate_template) || (empty($certificate_template[0]['name_x']) && empty($certificate_template[0]['name_y']))) {
            $certificate_template = array(array(
                'page_style' => 'L',
                'line_height' => 5,
                'top_margin' => 10,
                'left_margin' => 15,
                'right_margin' => 15,
                'label_print' => 'Y',
                'title_x' => 70, 'title_y' => 35, 'title_font' => 'Arial', 'title_size' => 16, 'title' => 'KERALA STATE TAMIL FESTIVAL 2026',
                'name_x' => 80, 'name_y' => 70, 'name_font' => 'Arial', 'name_size' => 13,
                'class_x' => 80, 'class_y' => 80, 'class_font' => 'Arial', 'class_size' => 11,
                'school_x' => 80, 'school_y' => 90, 'school_font' => 'Arial', 'school_size' => 11,
                'sub_dist_x' => 80, 'sub_dist_y' => 100, 'sub_dist_font' => 'Arial', 'sub_dist_size' => 11,
                'item_x' => 80, 'item_y' => 110, 'item_font' => 'Arial', 'item_size' => 11,
                'category_x' => 80, 'category_y' => 120, 'category_font' => 'Arial', 'category_size' => 11,
                'grade_x' => 80, 'grade_y' => 130, 'grade_font' => 'Arial', 'grade_size' => 13,
                'ehs_X' => 80, 'ehs_Y' => 140, 'ehs_font' => 'Arial', 'ehs_size' => 11,
                'date_x' => 45, 'date_y' => 170, 'date_font' => 'Arial', 'date_size' => 10,
                'place_x' => 190, 'place_y' => 170, 'place_font' => 'Arial', 'place_size' => 10,
                'photoX' => 0, 'photoY' => 0, 'photoWidth' => 0, 'photoHight' => 0
            ));
        }

        $page_style = (!empty($certificate_template[0]['page_style']) && $certificate_template[0]['page_style'] == 'P') ? 'P' : 'L';
        $line_height = (!empty($certificate_template[0]['line_height'])) ? $certificate_template[0]['line_height'] : '5';
        $top_margin = (!empty($certificate_template[0]['top_margin'])) ? $certificate_template[0]['top_margin'] : '10';
        $left_margin = (!empty($certificate_template[0]['left_margin'])) ? $certificate_template[0]['left_margin'] : '15';
        $right_margin = (!empty($certificate_template[0]['right_margin'])) ? $certificate_template[0]['right_margin'] : '15';
        $label_print = (!empty($certificate_template[0]['label_print'])) ? $certificate_template[0]['label_print'] : 'N';

        $this->fpdf->AddPage($page_style, 'A4');
        $this->fpdf->SetFont('Arial', 'B', 14);
        $this->fpdf->SetMargins($left_margin, $top_margin, $right_margin);
        if (!empty($certificate_template[0]['title_x']) && !empty($certificate_template[0]['title_y'])) {
            $this->fpdf->SetFont($certificate_template[0]['title_font'], 'B', $certificate_template[0]['title_size']);
            $this->fpdf->SetXY($certificate_template[0]['title_x'], $certificate_template[0]['title_y']);
            $this->fpdf->Write($line_height, $certificate_template[0]['title']);
        }
        if (!empty($certificate_template[0]['name_x']) && !empty($certificate_template[0]['name_y'])) {
            $this->fpdf->SetFont($certificate_template[0]['name_font'], 'B', $certificate_template[0]['name_size']);
            $this->fpdf->SetXY($certificate_template[0]['name_x'], $certificate_template[0]['name_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Name');
                $this->fpdf->SetXY($certificate_template[0]['name_x'] + 25, $certificate_template[0]['name_y']);
                $this->fpdf->Write($line_height, ' : ' . '    ' . $participant_array['participant_name']);
            } else {
                $this->fpdf->Write($line_height, $participant_array['participant_name']);
            }
        }
        if (!empty($certificate_template[0]['item_x']) && !empty($certificate_template[0]['item_y'])) {
            $this->fpdf->SetFont($certificate_template[0]['item_font'], 'B', $certificate_template[0]['item_size']);
            $this->fpdf->SetXY($certificate_template[0]['item_x'], $certificate_template[0]['item_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Item');
                $this->fpdf->SetXY($certificate_template[0]['item_x'] + 25, $certificate_template[0]['item_y']);
                $this->fpdf->Write($line_height, ' : ' . '     ' . $participant_array['item_name']);
            } else {
                $this->fpdf->Write($line_height, $participant_array['item_name']);
            }
        }
        if (!empty($certificate_template[0]['category_x']) && !empty($certificate_template[0]['category_y'])) {
            $this->fpdf->SetFont($certificate_template[0]['category_font'], 'B', $certificate_template[0]['category_size']);
            $this->fpdf->SetXY($certificate_template[0]['category_x'], $certificate_template[0]['category_y']);
        
            $fest_name_display = str_replace('TAMIL KALAIVIZHA', '', $participant_array['fest_name'] ?? '');
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Category');
                $this->fpdf->SetXY($certificate_template[0]['category_x'] + 25, $certificate_template[0]['category_y']);
                $this->fpdf->Write($line_height, ' : ' . '     ' . $fest_name_display);
            } else {
                $this->fpdf->Write($line_height, $fest_name_display);
            }
        }

        if (!empty($certificate_template[0]['grade_x']) && !empty($certificate_template[0]['grade_y'])) {
            $this->fpdf->SetFont($certificate_template[0]['grade_font'], 'B', $certificate_template[0]['grade_size']);
            $this->fpdf->SetXY($certificate_template[0]['grade_x'], $certificate_template[0]['grade_y']);
            if ($label_print == 'Y') {
                if (!empty($participant_array['grade'])) {
                    $this->fpdf->Write($line_height, 'Grade');
                    $this->fpdf->SetXY($certificate_template[0]['grade_x'] + 25, $certificate_template[0]['grade_y']);
                    $this->fpdf->Write($line_height, ' : ' . '     ' . $participant_array['grade']);
                } else {
                    $this->fpdf->Write($line_height, 'Grade');
                    $this->fpdf->SetXY($certificate_template[0]['grade_x'] + 25, $certificate_template[0]['grade_y']);
                    $this->fpdf->Write($line_height, ' : NIL');
                }
            } else {
                $this->fpdf->Write($line_height, $participant_array['grade'] ?? '');
            }
        }
        if (!empty($certificate_template[0]['class_x']) && !empty($certificate_template[0]['class_y'])) {
            $this->fpdf->SetFont($certificate_template[0]['class_font'], 'B', $certificate_template[0]['class_size']);
            $this->fpdf->SetXY($certificate_template[0]['class_x'], $certificate_template[0]['class_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Class');
                $this->fpdf->SetXY($certificate_template[0]['class_x'] + 25, $certificate_template[0]['class_y']);
                $this->fpdf->Write($line_height, ' : ' . '     ' . ($participant_array['class'] ?? ''));
            } else {
                $this->fpdf->Write($line_height, $participant_array['class'] ?? '');
            }
        }
        if (!empty($certificate_template[0]['school_x']) && !empty($certificate_template[0]['school_y'])) {
            $this->fpdf->SetFont($certificate_template[0]['school_font'], 'B', $certificate_template[0]['school_size']);
            $this->fpdf->SetXY($certificate_template[0]['school_x'], $certificate_template[0]['school_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'School');
                $this->fpdf->SetXY($certificate_template[0]['school_x'] + 25, $certificate_template[0]['school_y']);
                $this->fpdf->Write($line_height, ' : ' . '     ' . ($participant_array['school_name'] ?? ''));
            } else {
                $this->fpdf->Write($line_height, $participant_array['school_name'] ?? '');
            }
        }
        if (!empty($certificate_template[0]['sub_dist_x']) && !empty($certificate_template[0]['sub_dist_y'])) {
            $this->fpdf->SetFont($certificate_template[0]['sub_dist_font'], 'B', $certificate_template[0]['sub_dist_size']);
            $this->fpdf->SetXY($certificate_template[0]['sub_dist_x'], $certificate_template[0]['sub_dist_y']);
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'District');
                $this->fpdf->SetXY($certificate_template[0]['sub_dist_x'] + 25, $certificate_template[0]['sub_dist_y']);
                $this->fpdf->Write($line_height, ' : ' . '     ' . ($participant_array['sub_district_name'] ?? ''));
            } else {
                $this->fpdf->Write($line_height, $participant_array['sub_district_name'] ?? '');
            }
        }
        if (!empty($certificate_template[0]['ehs_X']) && !empty($certificate_template[0]['ehs_Y'])) {
            $this->fpdf->SetFont($certificate_template[0]['ehs_font'], 'B', $certificate_template[0]['ehs_size']);
            $this->fpdf->SetXY($certificate_template[0]['ehs_X'], $certificate_template[0]['ehs_Y']);

            if (!empty($participant_array['rank']) && $participant_array['rank'] == 1) {
                $this->fpdf->Write($line_height, 'Eligible For Higher Level');
            }
        }
        if (!empty($certificate_template[0]['photoX']) && !empty($certificate_template[0]['photoY'])) {
            @$pic_pathName = $participant_array['school_code'] . "_" . $participant_array['admn_no'];
            @$pic_path = $this->Photos_Model->get_Photo($pic_pathName, $participant_array['school_code']);

            if ($pic_path) {
                $this->fpdf->Image($pic_path, $certificate_template[0]['photoX'], $certificate_template[0]['photoY'], $certificate_template[0]['photoWidth'], $certificate_template[0]['photoHight']);
            }
        }
        if (!empty($certificate_template[0]['date_x']) && !empty($certificate_template[0]['date_y'])) {
            $this->fpdf->SetFont($certificate_template[0]['date_font'], 'B', $certificate_template[0]['date_size']);
            $this->fpdf->SetXY($certificate_template[0]['date_x'], $certificate_template[0]['date_y']);

            $fest_dates = $this->General_Model->get_fest_date_array();
            $end_date = (!empty($fest_dates) && is_array($fest_dates)) ? end($fest_dates) : date('d-m-Y');
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Date');
                $this->fpdf->SetXY($certificate_template[0]['date_x'] + 25, $certificate_template[0]['date_y']);
                $this->fpdf->Write($line_height, ' : ' . $end_date);
            } else {
                $this->fpdf->Write($line_height, $end_date);
            }
        }
        if (!empty($certificate_template[0]['place_x']) && !empty($certificate_template[0]['place_y'])) {
            $this->fpdf->SetFont($certificate_template[0]['place_font'], 'B', $certificate_template[0]['place_size']);
            $this->fpdf->SetXY($certificate_template[0]['place_x'], $certificate_template[0]['place_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Place');
                $this->fpdf->SetXY($certificate_template[0]['place_x'] + 25, $certificate_template[0]['place_y']);
                $this->fpdf->Write($line_height, ' : ' . ($participant_array['sub_district_name'] ?? ''));
            } else {
                $this->fpdf->Write($line_height, $participant_array['sub_district_name'] ?? '');
            }
        }
    }

    function list_school_wise() {
        if ($this->Session_Model->check_user_permission(48) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $school_details = $this->Certificate_Model->school_details_certificate_wise();
        $this->Contents['school_details'] = $school_details;
        $this->template->write_view('content', 'certificate/certificate_details_school_wise', $this->Contents);
        $this->template->load();
    }

    function get_certificate_school_wise() {
        if ($this->Session_Model->check_user_permission(48) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        $all_schools = $this->Certificate_Model->school_details_certificate_wise();
        $this->Contents['all_schools'] = $all_schools;

        $school_code = $this->input->post('hidSchoolCode') ?: $this->request->getGet('school_code') ?: $this->request->getGet('hidSchoolCode');
        if (empty($school_code) && !empty($all_schools)) {
            $school_code = $all_schools[0]['school_code'];
        }

        if (!empty($school_code)) {
            $this->Contents['selected_school_code'] = $school_code;
            $this->Contents['fest'] = $this->Certificate_Model->get_school_festival_details($school_code);
            $this->Contents['school_details'] = $this->General_Model->get_data('school_master', 'school_code, school_name', array('school_code' => $school_code));
            $this->Contents['all_participants'] = $this->Certificate_Model->get_school_participant_details($school_code, '', '', '', '');
            $this->template->write_view('content', 'certificate/school_item_certificate_details', $this->Contents);
            $this->template->load();
        } else {
            return redirect()->to(base_url('certificate/certificate/list_school_wise'));
        }
    }

    function get_certificate_pdf_school_wise() {
        if ($this->Session_Model->check_user_permission(48) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $school_code = $this->input->post('hidSchoolCode') ?: $this->request->getGet('school_code') ?: $this->request->getGet('hidSchoolCode');
        if (!empty($school_code)) {
            $sub_dist_code = $this->session->get('SUB_DISTRICT');
            $dist_code = $this->session->get('DISTRICT');
            $certificate_template = $this->Certificate_Model->get_certificate_details($dist_code, $sub_dist_code);

            $festival = $this->input->post('cmbFestType') ?: $this->request->getGet('cmbFestType') ?: '';
            $item_code = $this->input->post('item_code') ?: $this->request->getGet('item_code') ?: '';
            $captain_id = $this->input->post('captain_id') ?: $this->request->getGet('captain_id') ?: '';
            $participant_id = $this->input->post('participant_id') ?: $this->request->getGet('participant_id') ?: '';

            if ($item_code === '0' || $item_code === 0) $item_code = '';
            if ($captain_id === '0' || $captain_id === 0) $captain_id = '';
            if ($participant_id === '0' || $participant_id === 0) $participant_id = '';

            $participant_array = $this->Certificate_Model->get_school_participant_details($school_code, $festival, $item_code, $captain_id, $participant_id);

            if (empty($participant_array) || !is_array($participant_array)) {
                $this->session->setFlashdata('error', 'No eligible participants found for the selected school criteria.');
                return redirect()->to(base_url('certificate/certificate/get_certificate_school_wise?school_code=' . $school_code));
            }

            $this->load->library('_fpdf/fpdf');
            foreach ($participant_array as $p) {
                $this->create_certificate($p, $certificate_template);
            }

            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="school_certificates.pdf"');
            $this->fpdf->Output('school_certificates.pdf', 'I');
            exit;
        } else {
            return redirect()->to(base_url('certificate/certificate/list_school_wise'));
        }
    }

    function list_reg_no_wise() {
        if ($this->Session_Model->check_user_permission(48) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        $participant_id = $this->input->post('txtParticipantId') ?: $this->request->getGet('participant_id') ?: $this->request->getGet('txtParticipantId');
        if (!empty($participant_id)) {
            $this->Contents['participant_id'] = $participant_id;
            $participant_details = $this->Certificate_Model->get_participant_details_with_id($participant_id);
            if (is_array($participant_details) && count($participant_details) > 0) {
                $this->Contents['participant_details'] = $participant_details[0]['participant_name'] . ' ,  ' . $participant_details[0]['school_name'];
                $this->Contents['participant_info'] = $participant_details[0];
            }
            $item_details_array = $this->Certificate_Model->get_participant_item_details($participant_id);
            if (is_array($item_details_array) && count($item_details_array) > 0) {
                $this->Contents['item_details_array'] = $item_details_array;
                $this->Contents['item_drop_down'] = form_dropdown('item_code', $item_details_array, '', 'class="form-select border-secondary-subtle" id="item_code"');
            }
        }

        $this->Contents['all_participants'] = $this->Certificate_Model->get_school_participant_details('', '', '', '', '');
        $this->template->write_view('content', 'certificate/participant_certificate_details', $this->Contents);
        $this->template->load();
    }

    function get_certificate_pdf_participant_wise() {
        if ($this->Session_Model->check_user_permission(48) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $participant_id = $this->input->post('txtParticipantId') ?: $this->input->post('participant_id') ?: $this->request->getGet('participant_id') ?: $this->request->getGet('txtParticipantId');
        if (!empty($participant_id)) {
            $sub_dist_code = $this->session->get('SUB_DISTRICT');
            $dist_code = $this->session->get('DISTRICT');
            $certificate_template = $this->Certificate_Model->get_certificate_details($dist_code, $sub_dist_code);

            $item_code = $this->input->post('item_code') ?: $this->request->getGet('item_code') ?: '';
            if ($item_code === '0' || $item_code === 0) $item_code = '';

            $participant_array = $this->Certificate_Model->get_school_participant_details('', '', $item_code, '', $participant_id);
            if (empty($participant_array) || !is_array($participant_array)) {
                $this->session->setFlashdata('error', 'No certificate found for this participant and item.');
                return redirect()->to(base_url('certificate/certificate/list_reg_no_wise'));
            }

            $this->load->library('_fpdf/fpdf');
            foreach ($participant_array as $p) {
                $this->create_certificate($p, $certificate_template);
            }

            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="participant_certificate.pdf"');
            $this->fpdf->Output('participant_certificate.pdf', 'I');
            exit;
        } else {
            return redirect()->to(base_url('certificate/certificate/list_reg_no_wise'));
        }
    }

}
