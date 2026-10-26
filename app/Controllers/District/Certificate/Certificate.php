<?php

namespace App\Controllers\District\Certificate;

use App\Controllers\BaseController;

class Certificate extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->Session_Model->is_user_logged(true);
        $this->load->model('District/Certificate/Certificate_Model');
        $this->load->model('District/Stage/Festival_Scheduling_Model');
        $this->load->model('Pictures/Photos_Model');
        $this->template->add_js('js/certificate.js');
        $this->template->add_js('js/common.js');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
    }

    protected function check_district_access(): bool
    {
        $userType  = (int)$this->session->get('USER_TYPE');
        $userGroup = (string)$this->session->get('USER_GROUP');
        return ($userGroup === 'W' || $userGroup === 'A' || in_array($userType, [0, 1, 2], true));
    }

    protected function get_current_district_code(): int
    {
        $dist_code = (int)($this->session->get('DISTRICT') ?? 0);
        if ($dist_code <= 0) {
            $dist_code = (int)($this->session->get('district_code') ?? 0);
        }
        if ($dist_code <= 0) {
            $dist_code = (int)($this->session->get('REV_DISTRICT') ?? 0);
        }
        if ($dist_code <= 0) {
            $userId = (int)($this->session->get('USERID') ?? 0);
            if ($userId > 0) {
                $db = \Config\Database::connect();
                $u = $db->table('user_master')->select('rev_district_code')->where('user_id', $userId)->get()->getRowArray();
                if (!empty($u['rev_district_code'])) {
                    $dist_code = (int)$u['rev_district_code'];
                }
            }
        }
        if ($dist_code <= 0) {
            $dist_code = 9;
        }
        return $dist_code;
    }

    /**
     * District Certificate Template Designer
     */
    public function index()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $certificate_type_array = $this->General_Model->prepare_select_box_data('sd_certificate_type', 'ct_id,type', '', '', 'ct_id');
        $dist_code = $this->get_current_district_code();

        $font_array        = $this->Certificate_Model->get_font_array();
        $font_size_array   = $this->Certificate_Model->get_font_size_array();
        $line_height_array = $this->Certificate_Model->get_line_height_array();

        $certificate_template = $this->Certificate_Model->get_certificate_details($dist_code);

        $this->Contents = [
            'certificate_type_array' => $certificate_type_array,
            'font_array'             => $font_array,
            'font_size_array'        => $font_size_array,
            'line_height_array'      => $line_height_array,
            'certificate_template'   => $certificate_template,
            'district_code'          => $dist_code,
        ];

        $this->template->write_view('content', 'district/certificate/certificate_template_view', $this->Contents);
        return $this->template->load();
    }

    /**
     * Save Certificate Template settings
     */
    public function save_certificate_template()
    {
        if (!$this->check_district_access()) {
            if ($this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest' || $this->request->getHeaderLine('Accept') === 'application/json') {
                return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
            }
            return redirect()->to('welcome');
        }

        $dist_code = $this->get_current_district_code();

        $saved = $this->Certificate_Model->save_certificate_details($dist_code);

        if ($this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest' || $this->request->getHeaderLine('Accept') === 'application/json') {
            return $this->response->setJSON([
                'success' => $saved,
                'message' => $saved 
                    ? 'District certificate template calibration saved successfully! (சான்றிதழ் டெம்ப்ளேட் அளவீட்டு அமைப்புகள் வெற்றிகரமாகச் சேமிக்கப்பட்டன)' 
                    : 'Failed to save certificate template.',
            ]);
        }

        if ($saved) {
            $this->session->setFlashdata('swal_success', 'District certificate template calibration saved successfully! (சான்றிதழ் டெம்ப்ளேட் அளவீட்டு அமைப்புகள் வெற்றிகரமாகச் சேமிக்கப்பட்டன)');
        } else {
            $this->session->setFlashdata('swal_error', 'Failed to save certificate template.');
        }

        return redirect()->to(site_url('district/certificate'));
    }

    public function save_template()
    {
        return $this->save_certificate_template();
    }

    /**
     * List certificates eligible for print
     */
    public function print_list()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $districtCode = (int)$this->session->get('DISTRICT');
        $itemCode     = (int)($this->input->get_post('item_code') ?? 0);
        $schoolCode   = (float)($this->input->get_post('school_code') ?? 0);
        $subDistCode  = (int)($this->input->get_post('sub_district_code') ?? 0);

        $filters = [];
        if ($itemCode > 0)    $filters['item_code'] = $itemCode;
        if ($schoolCode > 0)  $filters['school_code'] = $schoolCode;
        if ($subDistCode > 0) $filters['sub_district_code'] = $subDistCode;

        $certificates = $this->Certificate_Model->get_certificates($districtCode, $filters);
        $items        = $this->Festival_Scheduling_Model->get_district_items($districtCode);

        $this->Content = [
            'district_code' => $districtCode,
            'certificates'  => $certificates,
            'items'         => $items,
            'filters'       => $filters,
        ];

        $this->template->write_view('content', 'district/certificate/certificate_print_view', $this->Content);
        return $this->template->load();
    }

    /**
     * Test certificate (with calibration graph) for district template
     */
    public function test_certificate()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $dist_code = $this->get_current_district_code();
        $certificate_template = $this->Certificate_Model->get_certificate_details($dist_code);

        $page_style = ($certificate_template[0]['page_style'] == 'P') ? 'P' : 'L';
        $line_height = ($certificate_template[0]['line_height']) ? $certificate_template[0]['line_height'] : '1';
        $top_margin = ($certificate_template[0]['top_margin']) ? $certificate_template[0]['top_margin'] : '1';
        $left_margin = ($certificate_template[0]['left_margin']) ? $certificate_template[0]['left_margin'] : '1';
        $right_margin = ($certificate_template[0]['right_margin']) ? $certificate_template[0]['right_margin'] : '1';

        $label_print = ($certificate_template[0]['label_print']) ? $certificate_template[0]['label_print'] : 'N';

        $this->load->library('_fpdf/fpdf');

        $this->fpdf->Open();

        $this->fpdf->AddPage($page_style, 'A4');
        $is_portrait = ($page_style === 'P');
        $default_coords = $is_portrait ? [
            'title_x' => 40, 'title_y' => 45, 'title_font' => 'TAU-Pallai', 'title_size' => 16,
            'name_x' => 45, 'name_y' => 85, 'name_font' => 'TAU-Pallai', 'name_size' => 14,
            'class_x' => 45, 'class_y' => 100, 'class_font' => 'TAU-Pallai', 'class_size' => 14,
            'school_x' => 45, 'school_y' => 115, 'school_font' => 'TAU-Pallai', 'school_size' => 14,
            'sub_dist_x' => 45, 'sub_dist_y' => 130, 'sub_dist_font' => 'TAU-Pallai', 'sub_dist_size' => 14,
            'dist_x' => 45, 'dist_y' => 145, 'dist_font' => 'TAU-Pallai', 'dist_size' => 14,
            'item_x' => 45, 'item_y' => 160, 'item_font' => 'TAU-Pallai', 'item_size' => 14,
            'category_x' => 45, 'category_y' => 175, 'category_font' => 'TAU-Pallai', 'category_size' => 14,
            'grade_x' => 45, 'grade_y' => 190, 'grade_font' => 'TAU-Pallai', 'grade_size' => 14,
            'ehs_X' => 45, 'ehs_Y' => 205, 'ehs_font' => 'TAU-Pallai', 'ehs_size' => 14,
            'date_x' => 35, 'date_y' => 240, 'date_font' => 'TAU-Pallai', 'date_size' => 14,
            'place_x' => 140, 'place_y' => 240, 'place_font' => 'TAU-Pallai', 'place_size' => 14,
            'photoX' => 150, 'photoY' => 45, 'photoWidth' => 150, 'photoHight' => 200,
        ] : [
            'title_x' => 70, 'title_y' => 35, 'title_font' => 'TAU-Pallai', 'title_size' => 16,
            'name_x' => 70, 'name_y' => 65, 'name_font' => 'TAU-Pallai', 'name_size' => 14,
            'class_x' => 70, 'class_y' => 75, 'class_font' => 'TAU-Pallai', 'class_size' => 14,
            'school_x' => 70, 'school_y' => 85, 'school_font' => 'TAU-Pallai', 'school_size' => 14,
            'sub_dist_x' => 70, 'sub_dist_y' => 95, 'sub_dist_font' => 'TAU-Pallai', 'sub_dist_size' => 14,
            'dist_x' => 70, 'dist_y' => 105, 'dist_font' => 'TAU-Pallai', 'dist_size' => 14,
            'item_x' => 70, 'item_y' => 115, 'item_font' => 'TAU-Pallai', 'item_size' => 14,
            'category_x' => 70, 'category_y' => 125, 'category_font' => 'TAU-Pallai', 'category_size' => 14,
            'grade_x' => 70, 'grade_y' => 135, 'grade_font' => 'TAU-Pallai', 'grade_size' => 14,
            'ehs_X' => 70, 'ehs_Y' => 145, 'ehs_font' => 'TAU-Pallai', 'ehs_size' => 14,
            'date_x' => 45, 'date_y' => 170, 'date_font' => 'TAU-Pallai', 'date_size' => 14,
            'place_x' => 190, 'place_y' => 170, 'place_font' => 'TAU-Pallai', 'place_size' => 14,
            'photoX' => 235, 'photoY' => 45, 'photoWidth' => 150, 'photoHight' => 200,
        ];
        foreach ($default_coords as $k => $v) {
            $cVal = $certificate_template[0][$k] ?? null;
            if ($cVal === null || $cVal === '') {
                $certificate_template[0][$k] = $v;
            }
        }

        $this->fpdf->SetFont('TAU-Pallai', 'B', 14);
        $this->fpdf->SetMargins($left_margin, $top_margin, $right_margin);

        if ((float)($certificate_template[0]['title_x'] ?? 0) > 0 && (float)($certificate_template[0]['title_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['title_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['title_size'] ?: 16);
            $this->fpdf->SetXY($certificate_template[0]['title_x'], $certificate_template[0]['title_y']);
            $title = !empty($certificate_template[0]['title']) ? $certificate_template[0]['title'] : 'KERALA STATE TAMIL FESTIVAL 2026 - PALAKKAD';
            $this->fpdf->Write($line_height, $title);
        }
        if ((float)($certificate_template[0]['name_x'] ?? 0) > 0 && (float)($certificate_template[0]['name_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['name_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['name_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['name_x'], $certificate_template[0]['name_y']);
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Name');
                $this->fpdf->SetXY($certificate_template[0]['name_x'] + 30, $certificate_template[0]['name_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['name_x'] + 34, $certificate_template[0]['name_y']);
                $this->fpdf->Write($line_height, 'DOMINIC SAVIO JOHN JOSEPH ANTHONY');
            } else {
                $this->fpdf->Write($line_height, 'DOMINIC SAVIO JOHN JOSEPH ANTHONY');
            }
        }
        if ((float)($certificate_template[0]['class_x'] ?? 0) > 0 && (float)($certificate_template[0]['class_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['class_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['class_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['class_x'], $certificate_template[0]['class_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Class');
                $this->fpdf->SetXY($certificate_template[0]['class_x'] + 30, $certificate_template[0]['class_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['class_x'] + 34, $certificate_template[0]['class_y']);
                $this->fpdf->Write($line_height, '5');
            } else {
                $this->fpdf->Write($line_height, '5');
            }
        }
        if ((float)($certificate_template[0]['school_x'] ?? 0) > 0 && (float)($certificate_template[0]['school_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['school_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['school_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['school_x'], $certificate_template[0]['school_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'School');
                $this->fpdf->SetXY($certificate_template[0]['school_x'] + 30, $certificate_template[0]['school_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['school_x'] + 34, $certificate_template[0]['school_y']);
                $this->fpdf->Write($line_height, "St Xavier's HS Parisakkal");
            } else {
                $this->fpdf->Write($line_height, "St Xavier's HS Parisakkal");
            }
        }
        if ((float)($certificate_template[0]['sub_dist_x'] ?? 0) > 0 && (float)($certificate_template[0]['sub_dist_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['sub_dist_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['sub_dist_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['sub_dist_x'], $certificate_template[0]['sub_dist_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Subdistrict');
                $this->fpdf->SetXY($certificate_template[0]['sub_dist_x'] + 30, $certificate_template[0]['sub_dist_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['sub_dist_x'] + 34, $certificate_template[0]['sub_dist_y']);
                $this->fpdf->Write($line_height, 'Chittur');
            } else {
                $this->fpdf->Write($line_height, 'Chittur');
            }
        }
        if ((float)($certificate_template[0]['dist_x'] ?? 0) > 0 && (float)($certificate_template[0]['dist_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['dist_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['dist_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['dist_x'], $certificate_template[0]['dist_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'District');
                $this->fpdf->SetXY($certificate_template[0]['dist_x'] + 30, $certificate_template[0]['dist_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['dist_x'] + 34, $certificate_template[0]['dist_y']);
                $this->fpdf->Write($line_height, 'Palakkad');
            } else {
                $this->fpdf->Write($line_height, 'Palakkad');
            }
        }
        if ((float)($certificate_template[0]['item_x'] ?? 0) > 0 && (float)($certificate_template[0]['item_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['item_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['item_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['item_x'], $certificate_template[0]['item_y']);
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Item');
                $this->fpdf->SetXY($certificate_template[0]['item_x'] + 30, $certificate_template[0]['item_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['item_x'] + 34, $certificate_template[0]['item_y']);
                $this->fpdf->Write($line_height, 'PATRIOTIC SONG');
            } else {
                $this->fpdf->Write($line_height, 'PATRIOTIC SONG');
            }
        }
        if ((float)($certificate_template[0]['category_x'] ?? 0) > 0 && (float)($certificate_template[0]['category_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['category_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['category_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['category_x'], $certificate_template[0]['category_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Category');
                $this->fpdf->SetXY($certificate_template[0]['category_x'] + 30, $certificate_template[0]['category_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['category_x'] + 34, $certificate_template[0]['category_y']);
                $this->fpdf->Write($line_height, 'UP Tamil Kalaivizha');
            } else {
                $this->fpdf->Write($line_height, 'UP Tamil Kalaivizha');
            }
        }
        if ((float)($certificate_template[0]['grade_x'] ?? 0) > 0 && (float)($certificate_template[0]['grade_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['grade_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['grade_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['grade_x'], $certificate_template[0]['grade_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Grade');
                $this->fpdf->SetXY($certificate_template[0]['grade_x'] + 30, $certificate_template[0]['grade_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['grade_x'] + 34, $certificate_template[0]['grade_y']);
                $this->fpdf->Write($line_height, 'A');
            } else {
                $this->fpdf->Write($line_height, 'A');
            }
        }
        if ((float)($certificate_template[0]['ehs_X'] ?? 0) > 0 && (float)($certificate_template[0]['ehs_Y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['ehs_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['ehs_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['ehs_X'], $certificate_template[0]['ehs_Y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Higher Level');
                $this->fpdf->SetXY($certificate_template[0]['ehs_X'] + 30, $certificate_template[0]['ehs_Y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['ehs_X'] + 34, $certificate_template[0]['ehs_Y']);
                $this->fpdf->Write($line_height, 'Eligible For Higher Level');
            } else {
                $this->fpdf->Write($line_height, 'Eligible For Higher Level');
            }
        }
        if ((float)($certificate_template[0]['date_x'] ?? 0) > 0 && (float)($certificate_template[0]['date_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['date_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['date_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['date_x'], $certificate_template[0]['date_y']);

            $dateVal = date('d-m-Y');
            $this->fpdf->Write($line_height, ($label_print == 'Y' ? 'Date : ' . $dateVal : $dateVal));
        }
        if ((float)($certificate_template[0]['place_x'] ?? 0) > 0 && (float)($certificate_template[0]['place_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['place_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['place_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['place_x'], $certificate_template[0]['place_y']);

            $placeVal = 'Palakkad';
            $this->fpdf->Write($line_height, ($label_print == 'Y' ? 'Place : ' . $placeVal : $placeVal));
        }

        // Participant Photo (Preview)
        $photoX = (float)($certificate_template[0]['photoX'] ?? 0);
        $photoY = (float)($certificate_template[0]['photoY'] ?? 0);
        $rawW   = (float)($certificate_template[0]['photoWidth'] ?? 0);
        $rawH   = (float)($certificate_template[0]['photoHight'] ?? 0);

        if ($photoX > 0 && $photoY > 0) {
            // Auto-scale pixel dimensions (150x200 px) to millimeters (at 96 DPI: 150px -> 39.7mm, 200px -> 52.9mm)
            if ($rawW > 50) {
                $photoW = round($rawW * 25.4 / 96, 1);
            } elseif ($rawW > 0) {
                $photoW = $rawW;
            } else {
                $photoW = 39.7;
            }

            if ($rawH > 50) {
                $photoH = round($rawH * 25.4 / 96, 1);
            } elseif ($rawH > 0) {
                $photoH = $rawH;
            } else {
                $photoH = 52.9;
            }

            $sample_photos = [
                FCPATH . 'photos/213/21045/21045_1015.jpg',
                ROOTPATH . 'photos/213/21045/21045_1015.jpg',
                FCPATH . 'photos/img_user_nophoto.png',
                ROOTPATH . 'photos/img_user_nophoto.png',
            ];
            $sample_file = null;
            foreach ($sample_photos as $sp) {
                if (file_exists($sp)) {
                    $sample_file = $sp;
                    break;
                }
            }

            $this->fpdf->Rect($photoX - 0.5, $photoY - 0.5, $photoW + 1, $photoH + 1);
            if ($sample_file) {
                try {
                    $this->fpdf->Image($sample_file, $photoX, $photoY, $photoW, $photoH);
                } catch (\Throwable $e) {
                    $this->fpdf->SetXY($photoX, $photoY + ($photoH / 2) - 2);
                    $this->fpdf->SetFont('Arial', 'B', 8);
                    $this->fpdf->Cell($photoW, 4, 'PHOTO', 0, 0, 'C');
                }
            } else {
                $this->fpdf->SetXY($photoX, $photoY + ($photoH / 2) - 2);
                $this->fpdf->SetFont('Arial', 'B', 8);
                $this->fpdf->Cell($photoW, 4, 'PHOTO', 0, 0, 'C');
            }
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
        } else {
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

    /**
     * Test certificate (clean preview without calibration graph)
     */
    public function test_certificare_withoutgraph()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $dist_code = $this->get_current_district_code();
        $certificate_template = $this->Certificate_Model->get_certificate_details($dist_code);

        $page_style   = ($certificate_template[0]['page_style'] == 'P') ? 'P' : 'L';
        $line_height  = ($certificate_template[0]['line_height']) ? $certificate_template[0]['line_height'] : '1';
        $top_margin   = ($certificate_template[0]['top_margin']) ? $certificate_template[0]['top_margin'] : '1';
        $left_margin  = ($certificate_template[0]['left_margin']) ? $certificate_template[0]['left_margin'] : '1';
        $right_margin = ($certificate_template[0]['right_margin']) ? $certificate_template[0]['right_margin'] : '1';
        $label_print  = ($certificate_template[0]['label_print']) ? $certificate_template[0]['label_print'] : 'N';

        $this->load->library('_fpdf/fpdf');

        $this->fpdf->Open();
        $this->fpdf->AddPage($page_style, 'A4');
        $is_portrait = ($page_style === 'P');
        $default_coords = $is_portrait ? [
            'title_x' => 40, 'title_y' => 45, 'title_font' => 'TAU-Pallai', 'title_size' => 16,
            'name_x' => 45, 'name_y' => 85, 'name_font' => 'TAU-Pallai', 'name_size' => 14,
            'class_x' => 45, 'class_y' => 100, 'class_font' => 'TAU-Pallai', 'class_size' => 14,
            'school_x' => 45, 'school_y' => 115, 'school_font' => 'TAU-Pallai', 'school_size' => 14,
            'sub_dist_x' => 45, 'sub_dist_y' => 130, 'sub_dist_font' => 'TAU-Pallai', 'sub_dist_size' => 14,
            'dist_x' => 45, 'dist_y' => 145, 'dist_font' => 'TAU-Pallai', 'dist_size' => 14,
            'item_x' => 45, 'item_y' => 160, 'item_font' => 'TAU-Pallai', 'item_size' => 14,
            'category_x' => 45, 'category_y' => 175, 'category_font' => 'TAU-Pallai', 'category_size' => 14,
            'grade_x' => 45, 'grade_y' => 190, 'grade_font' => 'TAU-Pallai', 'grade_size' => 14,
            'ehs_X' => 45, 'ehs_Y' => 205, 'ehs_font' => 'TAU-Pallai', 'ehs_size' => 14,
            'date_x' => 35, 'date_y' => 240, 'date_font' => 'TAU-Pallai', 'date_size' => 14,
            'place_x' => 140, 'place_y' => 240, 'place_font' => 'TAU-Pallai', 'place_size' => 14,
            'photoX' => 150, 'photoY' => 45, 'photoWidth' => 150, 'photoHight' => 200,
        ] : [
            'title_x' => 70, 'title_y' => 35, 'title_font' => 'TAU-Pallai', 'title_size' => 16,
            'name_x' => 70, 'name_y' => 65, 'name_font' => 'TAU-Pallai', 'name_size' => 14,
            'class_x' => 70, 'class_y' => 75, 'class_font' => 'TAU-Pallai', 'class_size' => 14,
            'school_x' => 70, 'school_y' => 85, 'school_font' => 'TAU-Pallai', 'school_size' => 14,
            'sub_dist_x' => 70, 'sub_dist_y' => 95, 'sub_dist_font' => 'TAU-Pallai', 'sub_dist_size' => 14,
            'dist_x' => 70, 'dist_y' => 105, 'dist_font' => 'TAU-Pallai', 'dist_size' => 14,
            'item_x' => 70, 'item_y' => 115, 'item_font' => 'TAU-Pallai', 'item_size' => 14,
            'category_x' => 70, 'category_y' => 125, 'category_font' => 'TAU-Pallai', 'category_size' => 14,
            'grade_x' => 70, 'grade_y' => 135, 'grade_font' => 'TAU-Pallai', 'grade_size' => 14,
            'ehs_X' => 70, 'ehs_Y' => 145, 'ehs_font' => 'TAU-Pallai', 'ehs_size' => 14,
            'date_x' => 45, 'date_y' => 170, 'date_font' => 'TAU-Pallai', 'date_size' => 14,
            'place_x' => 190, 'place_y' => 170, 'place_font' => 'TAU-Pallai', 'place_size' => 14,
            'photoX' => 235, 'photoY' => 45, 'photoWidth' => 150, 'photoHight' => 200,
        ];
        foreach ($default_coords as $k => $v) {
            $cVal = $certificate_template[0][$k] ?? null;
            if ($cVal === null || $cVal === '') {
                $certificate_template[0][$k] = $v;
            }
        }

        $this->fpdf->SetFont('TAU-Pallai', 'B', 14);
        $this->fpdf->SetMargins($left_margin, $top_margin, $right_margin);

        if ((float)($certificate_template[0]['title_x'] ?? 0) > 0 && (float)($certificate_template[0]['title_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['title_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['title_size'] ?: 16);
            $this->fpdf->SetXY($certificate_template[0]['title_x'], $certificate_template[0]['title_y']);
            $title = !empty($certificate_template[0]['title']) ? $certificate_template[0]['title'] : 'KERALA STATE TAMIL FESTIVAL 2026 - PALAKKAD';
            $this->fpdf->Write($line_height, ($label_print == 'Y' ? ' ' . $title : $title));
        }
        if ((float)($certificate_template[0]['name_x'] ?? 0) > 0 && (float)($certificate_template[0]['name_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['name_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['name_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['name_x'], $certificate_template[0]['name_y']);
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Name');
                $this->fpdf->SetXY($certificate_template[0]['name_x'] + 30, $certificate_template[0]['name_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['name_x'] + 34, $certificate_template[0]['name_y']);
                $this->fpdf->Write($line_height, 'DOMINIC SAVIO JOHN JOSEPH ANTHONY');
            } else {
                $this->fpdf->Write($line_height, 'DOMINIC SAVIO JOHN JOSEPH ANTHONY');
            }
        }
        if ((float)($certificate_template[0]['class_x'] ?? 0) > 0 && (float)($certificate_template[0]['class_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['class_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['class_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['class_x'], $certificate_template[0]['class_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Class');
                $this->fpdf->SetXY($certificate_template[0]['class_x'] + 30, $certificate_template[0]['class_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['class_x'] + 34, $certificate_template[0]['class_y']);
                $this->fpdf->Write($line_height, '5');
            } else {
                $this->fpdf->Write($line_height, '5');
            }
        }
        if ((float)($certificate_template[0]['school_x'] ?? 0) > 0 && (float)($certificate_template[0]['school_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['school_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['school_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['school_x'], $certificate_template[0]['school_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'School');
                $this->fpdf->SetXY($certificate_template[0]['school_x'] + 30, $certificate_template[0]['school_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['school_x'] + 34, $certificate_template[0]['school_y']);
                $this->fpdf->Write($line_height, "St Xavier's HS Parisakkal");
            } else {
                $this->fpdf->Write($line_height, "St Xavier's HS Parisakkal");
            }
        }
        if ((float)($certificate_template[0]['sub_dist_x'] ?? 0) > 0 && (float)($certificate_template[0]['sub_dist_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['sub_dist_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['sub_dist_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['sub_dist_x'], $certificate_template[0]['sub_dist_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Subdistrict');
                $this->fpdf->SetXY($certificate_template[0]['sub_dist_x'] + 30, $certificate_template[0]['sub_dist_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['sub_dist_x'] + 34, $certificate_template[0]['sub_dist_y']);
                $this->fpdf->Write($line_height, 'Chittur');
            } else {
                $this->fpdf->Write($line_height, 'Chittur');
            }
        }
        if ((float)($certificate_template[0]['dist_x'] ?? 0) > 0 && (float)($certificate_template[0]['dist_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['dist_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['dist_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['dist_x'], $certificate_template[0]['dist_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'District');
                $this->fpdf->SetXY($certificate_template[0]['dist_x'] + 30, $certificate_template[0]['dist_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['dist_x'] + 34, $certificate_template[0]['dist_y']);
                $this->fpdf->Write($line_height, 'Palakkad');
            } else {
                $this->fpdf->Write($line_height, 'Palakkad');
            }
        }
        if ((float)($certificate_template[0]['item_x'] ?? 0) > 0 && (float)($certificate_template[0]['item_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['item_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['item_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['item_x'], $certificate_template[0]['item_y']);
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Item');
                $this->fpdf->SetXY($certificate_template[0]['item_x'] + 30, $certificate_template[0]['item_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['item_x'] + 34, $certificate_template[0]['item_y']);
                $this->fpdf->Write($line_height, 'PATRIOTIC SONG');
            } else {
                $this->fpdf->Write($line_height, 'PATRIOTIC SONG');
            }
        }
        if ((float)($certificate_template[0]['category_x'] ?? 0) > 0 && (float)($certificate_template[0]['category_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['category_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['category_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['category_x'], $certificate_template[0]['category_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Category');
                $this->fpdf->SetXY($certificate_template[0]['category_x'] + 30, $certificate_template[0]['category_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['category_x'] + 34, $certificate_template[0]['category_y']);
                $this->fpdf->Write($line_height, 'UP Tamil Kalaivizha');
            } else {
                $this->fpdf->Write($line_height, 'UP Tamil Kalaivizha');
            }
        }
        if ((float)($certificate_template[0]['grade_x'] ?? 0) > 0 && (float)($certificate_template[0]['grade_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['grade_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['grade_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['grade_x'], $certificate_template[0]['grade_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Grade');
                $this->fpdf->SetXY($certificate_template[0]['grade_x'] + 30, $certificate_template[0]['grade_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['grade_x'] + 34, $certificate_template[0]['grade_y']);
                $this->fpdf->Write($line_height, 'A');
            } else {
                $this->fpdf->Write($line_height, 'A');
            }
        }
        if ((float)($certificate_template[0]['ehs_X'] ?? 0) > 0 && (float)($certificate_template[0]['ehs_Y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['ehs_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['ehs_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['ehs_X'], $certificate_template[0]['ehs_Y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Higher Level');
                $this->fpdf->SetXY($certificate_template[0]['ehs_X'] + 30, $certificate_template[0]['ehs_Y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['ehs_X'] + 34, $certificate_template[0]['ehs_Y']);
                $this->fpdf->Write($line_height, 'Eligible For Higher Level');
            } else {
                $this->fpdf->Write($line_height, 'Eligible For Higher Level');
            }
        }
        if ((float)($certificate_template[0]['date_x'] ?? 0) > 0 && (float)($certificate_template[0]['date_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['date_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['date_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['date_x'], $certificate_template[0]['date_y']);

            $dateVal = date('d-m-Y');
            $this->fpdf->Write($line_height, ($label_print == 'Y' ? 'Date : ' . $dateVal : $dateVal));
        }
        if ((float)($certificate_template[0]['place_x'] ?? 0) > 0 && (float)($certificate_template[0]['place_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['place_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['place_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['place_x'], $certificate_template[0]['place_y']);

            $placeVal = 'Palakkad';
            $this->fpdf->Write($line_height, ($label_print == 'Y' ? 'Place : ' . $placeVal : $placeVal));
        }

        // Participant Photo (Clean Preview)
        $photoX = (float)($certificate_template[0]['photoX'] ?? 0);
        $photoY = (float)($certificate_template[0]['photoY'] ?? 0);
        $rawW   = (float)($certificate_template[0]['photoWidth'] ?? 0);
        $rawH   = (float)($certificate_template[0]['photoHight'] ?? 0);

        if ($photoX > 0 && $photoY > 0) {
            // Auto-scale pixel dimensions (150x200 px) to millimeters (at 96 DPI: 150px -> 39.7mm, 200px -> 52.9mm)
            if ($rawW > 50) {
                $photoW = round($rawW * 25.4 / 96, 1);
            } elseif ($rawW > 0) {
                $photoW = $rawW;
            } else {
                $photoW = 39.7;
            }

            if ($rawH > 50) {
                $photoH = round($rawH * 25.4 / 96, 1);
            } elseif ($rawH > 0) {
                $photoH = $rawH;
            } else {
                $photoH = 52.9;
            }

            $sample_photos = [
                FCPATH . 'photos/213/21045/21045_1015.jpg',
                ROOTPATH . 'photos/213/21045/21045_1015.jpg',
                FCPATH . 'photos/img_user_nophoto.png',
                ROOTPATH . 'photos/img_user_nophoto.png',
            ];
            $sample_file = null;
            foreach ($sample_photos as $sp) {
                if (file_exists($sp)) {
                    $sample_file = $sp;
                    break;
                }
            }

            $this->fpdf->Rect($photoX - 0.5, $photoY - 0.5, $photoW + 1, $photoH + 1);
            if ($sample_file) {
                try {
                    $this->fpdf->Image($sample_file, $photoX, $photoY, $photoW, $photoH);
                } catch (\Throwable $e) {
                    $this->fpdf->SetXY($photoX, $photoY + ($photoH / 2) - 2);
                    $this->fpdf->SetFont('Arial', 'B', 8);
                    $this->fpdf->Cell($photoW, 4, 'PHOTO', 0, 0, 'C');
                }
            } else {
                $this->fpdf->SetXY($photoX, $photoY + ($photoH / 2) - 2);
                $this->fpdf->SetFont('Arial', 'B', 8);
                $this->fpdf->Cell($photoW, 4, 'PHOTO', 0, 0, 'C');
            }
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        $this->fpdf->Output('certificate_preview_clean.pdf', 'I');
        exit;
    }

    public function test_certificate_withoutgraph()
    {
        return $this->test_certificare_withoutgraph();
    }

    /**
     * Item-wise certificate list (district scope)
     */
    public function list_item_wise()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', '', 'fest_id');
        $this->Contents['fest'] = $fest;

        $fest_id = $this->input->get_post('cmbFestType');
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

        $this->template->write_view('content', 'district/certificate/item_certificate_details', $this->Contents);
        $this->template->load();
    }

    public function get_certificate_itemwise()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $item_id = $this->input->get_post('hidItemId') ?: $this->input->get_post('item_code');
        if (!empty($item_id)) {
            $district = (int)$this->session->get('DISTRICT');
            $this->db->where('item_code', $item_id);
            $this->db->where('district_code', $district);
            $this->db->set('is_certificate_printed', 1);
            $this->db->update('d_result_master');

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
            $this->template->write_view('content', 'district/certificate/certificate_itemwise', $this->Contents);
            $this->template->load();
        } else {
            return redirect()->to(base_url('district/certificate/certificate/list_item_wise'));
        }
    }

    public function get_certificate_pdf()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $item_id = $this->input->get_post('hidItemId') ?: $this->input->get_post('item_code');
        if (!empty($item_id)) {
            $dist_code = $this->get_current_district_code();
            $certificate_template = $this->Certificate_Model->get_certificate_details($dist_code);

            $captain_id = $this->input->get_post('captain_id');
            $item_type = $this->input->get_post('hidItemType') ?: $this->Certificate_Model->is_group_item($item_id);
            $participant_id = $this->input->get_post('participant_id') ?: '';

            if (empty($captain_id) || $captain_id === '0' || $captain_id === 0 || $captain_id === 'all') {
                $participant_array = $this->Certificate_Model->get_participant_details($item_id, 'all', $item_type);
            } else {
                $participant_array = $this->Certificate_Model->get_participant_details($item_id, $captain_id, $item_type, $participant_id);
            }

            if (empty($participant_array) || !is_array($participant_array)) {
                $this->session->setFlashdata('error', 'No eligible participants found to generate certificates.');
                return redirect()->to(base_url('district/certificate/certificate/list_item_wise'));
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
            return redirect()->to(base_url('district/certificate/certificate/list_item_wise'));
        }
    }

    public function create_certificate($participant_array, $certificate_template)
    {
        if (!$this->check_district_access()) {
            return;
        }
        if (@$participant_array['spo_id'] > 0 and $participant_array['grade'] == '') {
            return;
        }

        $page_style = (!empty($certificate_template[0]['page_style']) && $certificate_template[0]['page_style'] == 'P') ? 'P' : 'L';
        $line_height = (!empty($certificate_template[0]['line_height'])) ? $certificate_template[0]['line_height'] : '5';
        $top_margin = (!empty($certificate_template[0]['top_margin'])) ? $certificate_template[0]['top_margin'] : '10';
        $left_margin = (!empty($certificate_template[0]['left_margin'])) ? $certificate_template[0]['left_margin'] : '15';
        $right_margin = (!empty($certificate_template[0]['right_margin'])) ? $certificate_template[0]['right_margin'] : '15';
        $label_print = (!empty($certificate_template[0]['label_print'])) ? $certificate_template[0]['label_print'] : 'N';

        $is_portrait = ($page_style === 'P');
        $default_coords = $is_portrait ? [
            'title_x' => 40, 'title_y' => 45, 'title_font' => 'TAU-Pallai', 'title_size' => 16, 'title' => 'KERALA STATE TAMIL FESTIVAL 2026 - PALAKKAD',
            'name_x' => 45, 'name_y' => 85, 'name_font' => 'TAU-Pallai', 'name_size' => 14,
            'class_x' => 45, 'class_y' => 100, 'class_font' => 'TAU-Pallai', 'class_size' => 14,
            'school_x' => 45, 'school_y' => 115, 'school_font' => 'TAU-Pallai', 'school_size' => 14,
            'sub_dist_x' => 45, 'sub_dist_y' => 130, 'sub_dist_font' => 'TAU-Pallai', 'sub_dist_size' => 14,
            'dist_x' => 45, 'dist_y' => 145, 'dist_font' => 'TAU-Pallai', 'dist_size' => 14,
            'item_x' => 45, 'item_y' => 160, 'item_font' => 'TAU-Pallai', 'item_size' => 14,
            'category_x' => 45, 'category_y' => 175, 'category_font' => 'TAU-Pallai', 'category_size' => 14,
            'grade_x' => 45, 'grade_y' => 190, 'grade_font' => 'TAU-Pallai', 'grade_size' => 14,
            'ehs_X' => 45, 'ehs_Y' => 205, 'ehs_font' => 'TAU-Pallai', 'ehs_size' => 14,
            'date_x' => 35, 'date_y' => 240, 'date_font' => 'TAU-Pallai', 'date_size' => 14,
            'place_x' => 140, 'place_y' => 240, 'place_font' => 'TAU-Pallai', 'place_size' => 14,
            'photoX' => 150, 'photoY' => 45, 'photoWidth' => 150, 'photoHight' => 200,
        ] : [
            'title_x' => 70, 'title_y' => 35, 'title_font' => 'TAU-Pallai', 'title_size' => 16, 'title' => 'KERALA STATE TAMIL FESTIVAL 2026 - PALAKKAD',
            'name_x' => 70, 'name_y' => 65, 'name_font' => 'TAU-Pallai', 'name_size' => 14,
            'class_x' => 70, 'class_y' => 75, 'class_font' => 'TAU-Pallai', 'class_size' => 14,
            'school_x' => 70, 'school_y' => 85, 'school_font' => 'TAU-Pallai', 'school_size' => 14,
            'sub_dist_x' => 70, 'sub_dist_y' => 95, 'sub_dist_font' => 'TAU-Pallai', 'sub_dist_size' => 14,
            'dist_x' => 70, 'dist_y' => 105, 'dist_font' => 'TAU-Pallai', 'dist_size' => 14,
            'item_x' => 70, 'item_y' => 115, 'item_font' => 'TAU-Pallai', 'item_size' => 14,
            'category_x' => 70, 'category_y' => 125, 'category_font' => 'TAU-Pallai', 'category_size' => 14,
            'grade_x' => 70, 'grade_y' => 135, 'grade_font' => 'TAU-Pallai', 'grade_size' => 14,
            'ehs_X' => 70, 'ehs_Y' => 145, 'ehs_font' => 'TAU-Pallai', 'ehs_size' => 14,
            'date_x' => 45, 'date_y' => 170, 'date_font' => 'TAU-Pallai', 'date_size' => 14,
            'place_x' => 190, 'place_y' => 170, 'place_font' => 'TAU-Pallai', 'place_size' => 14,
            'photoX' => 235, 'photoY' => 45, 'photoWidth' => 150, 'photoHight' => 200,
        ];

        if (empty($certificate_template) || !is_array($certificate_template)) {
            $certificate_template = [$default_coords];
        } else {
            foreach ($default_coords as $k => $v) {
                $cVal = $certificate_template[0][$k] ?? null;
                if ($cVal === null || $cVal === '') {
                    $certificate_template[0][$k] = $v;
                }
            }
        }

        $this->fpdf->AddPage($page_style, 'A4');
        $this->fpdf->SetFont('TAU-Pallai', 'B', 14);
        $this->fpdf->SetMargins($left_margin, $top_margin, $right_margin);

        // 1. Title Header
        if ((float)($certificate_template[0]['title_x'] ?? 0) > 0 && (float)($certificate_template[0]['title_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['title_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['title_size'] ?: 16);
            $this->fpdf->SetXY($certificate_template[0]['title_x'], $certificate_template[0]['title_y']);
            $title = !empty($certificate_template[0]['title']) ? $certificate_template[0]['title'] : 'KERALA STATE TAMIL FESTIVAL 2026 - PALAKKAD';
            $this->fpdf->Write($line_height, $title);
        }

        // 2. Participant Name
        if ((float)($certificate_template[0]['name_x'] ?? 0) > 0 && (float)($certificate_template[0]['name_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['name_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['name_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['name_x'], $certificate_template[0]['name_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Name');
                $this->fpdf->SetXY($certificate_template[0]['name_x'] + 30, $certificate_template[0]['name_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['name_x'] + 34, $certificate_template[0]['name_y']);
                $this->fpdf->Write($line_height, $participant_array['participant_name'] ?? '');
            } else {
                $this->fpdf->Write($line_height, $participant_array['participant_name'] ?? '');
            }
        }

        // 3. Item Name
        if ((float)($certificate_template[0]['item_x'] ?? 0) > 0 && (float)($certificate_template[0]['item_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['item_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['item_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['item_x'], $certificate_template[0]['item_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Item');
                $this->fpdf->SetXY($certificate_template[0]['item_x'] + 30, $certificate_template[0]['item_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['item_x'] + 34, $certificate_template[0]['item_y']);
                $this->fpdf->Write($line_height, $participant_array['item_name'] ?? '');
            } else {
                $this->fpdf->Write($line_height, $participant_array['item_name'] ?? '');
            }
        }

        // 4. Category
        if ((float)($certificate_template[0]['category_x'] ?? 0) > 0 && (float)($certificate_template[0]['category_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['category_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['category_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['category_x'], $certificate_template[0]['category_y']);

            $fest_name_display = trim(str_ireplace(['TAMIL KALAIVIZHA', 'TAMIL FESTIVAL'], '', $participant_array['fest_name'] ?? ''));
            if (empty($fest_name_display)) {
                $fest_name_display = $participant_array['fest_name'] ?? '';
            }
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Category');
                $this->fpdf->SetXY($certificate_template[0]['category_x'] + 30, $certificate_template[0]['category_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['category_x'] + 34, $certificate_template[0]['category_y']);
                $this->fpdf->Write($line_height, $fest_name_display);
            } else {
                $this->fpdf->Write($line_height, $fest_name_display);
            }
        }

        // 5. Grade
        if ((float)($certificate_template[0]['grade_x'] ?? 0) > 0 && (float)($certificate_template[0]['grade_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['grade_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['grade_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['grade_x'], $certificate_template[0]['grade_y']);
            $gradeVal = !empty($participant_array['grade']) ? $participant_array['grade'] : '';
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Grade');
                $this->fpdf->SetXY($certificate_template[0]['grade_x'] + 30, $certificate_template[0]['grade_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['grade_x'] + 34, $certificate_template[0]['grade_y']);
                $this->fpdf->Write($line_height, (!empty($gradeVal) ? $gradeVal : 'NIL'));
            } else {
                $this->fpdf->Write($line_height, $gradeVal);
            }
        }

        // 6. Class / Standard
        if ((float)($certificate_template[0]['class_x'] ?? 0) > 0 && (float)($certificate_template[0]['class_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['class_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['class_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['class_x'], $certificate_template[0]['class_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Class');
                $this->fpdf->SetXY($certificate_template[0]['class_x'] + 30, $certificate_template[0]['class_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['class_x'] + 34, $certificate_template[0]['class_y']);
                $this->fpdf->Write($line_height, ($participant_array['class'] ?? ''));
            } else {
                $this->fpdf->Write($line_height, $participant_array['class'] ?? '');
            }
        }

        // 7. School Name
        if ((float)($certificate_template[0]['school_x'] ?? 0) > 0 && (float)($certificate_template[0]['school_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['school_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['school_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['school_x'], $certificate_template[0]['school_y']);

            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'School');
                $this->fpdf->SetXY($certificate_template[0]['school_x'] + 30, $certificate_template[0]['school_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['school_x'] + 34, $certificate_template[0]['school_y']);
                $this->fpdf->Write($line_height, ($participant_array['school_name'] ?? ''));
            } else {
                $this->fpdf->Write($line_height, $participant_array['school_name'] ?? '');
            }
        }

        // 8. Sub-District
        if ((float)($certificate_template[0]['sub_dist_x'] ?? 0) > 0 && (float)($certificate_template[0]['sub_dist_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['sub_dist_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['sub_dist_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['sub_dist_x'], $certificate_template[0]['sub_dist_y']);
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'Subdistrict');
                $this->fpdf->SetXY($certificate_template[0]['sub_dist_x'] + 30, $certificate_template[0]['sub_dist_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['sub_dist_x'] + 34, $certificate_template[0]['sub_dist_y']);
                $this->fpdf->Write($line_height, ($participant_array['sub_district_name'] ?? ''));
            } else {
                $this->fpdf->Write($line_height, $participant_array['sub_district_name'] ?? '');
            }
        }

        // 9. District
        if ((float)($certificate_template[0]['dist_x'] ?? 0) > 0 && (float)($certificate_template[0]['dist_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['dist_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['dist_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['dist_x'], $certificate_template[0]['dist_y']);
            $distName = !empty($participant_array['rev_district_name']) ? $participant_array['rev_district_name'] : (!empty($participant_array['district_name']) ? $participant_array['district_name'] : 'Palakkad');
            if ($label_print == 'Y') {
                $this->fpdf->Write($line_height, 'District');
                $this->fpdf->SetXY($certificate_template[0]['dist_x'] + 30, $certificate_template[0]['dist_y']);
                $this->fpdf->Write($line_height, ':');
                $this->fpdf->SetXY($certificate_template[0]['dist_x'] + 34, $certificate_template[0]['dist_y']);
                $this->fpdf->Write($line_height, $distName);
            } else {
                $this->fpdf->Write($line_height, $distName);
            }
        }

        // 10. Higher Level Eligibility (EHS) / Rank
        if ((float)($certificate_template[0]['ehs_X'] ?? 0) > 0 && (float)($certificate_template[0]['ehs_Y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['ehs_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['ehs_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['ehs_X'], $certificate_template[0]['ehs_Y']);

            $ehsLabel = '';
            $ehsVal = '';
            if (!empty($participant_array['rank']) && (int)$participant_array['rank'] === 1) {
                $ehsLabel = 'Higher Level';
                $ehsVal = 'Eligible For Higher Level';
            } else if (!empty($participant_array['rank']) && (int)$participant_array['rank'] === 2) {
                $ehsLabel = 'Rank';
                $ehsVal = 'Second Rank';
            } else if (!empty($participant_array['rank']) && (int)$participant_array['rank'] === 3) {
                $ehsLabel = 'Rank';
                $ehsVal = 'Third Rank';
            }

            if (!empty($ehsVal)) {
                if ($label_print == 'Y') {
                    $this->fpdf->Write($line_height, $ehsLabel);
                    $this->fpdf->SetXY($certificate_template[0]['ehs_X'] + 30, $certificate_template[0]['ehs_Y']);
                    $this->fpdf->Write($line_height, ':');
                    $this->fpdf->SetXY($certificate_template[0]['ehs_X'] + 34, $certificate_template[0]['ehs_Y']);
                    $this->fpdf->Write($line_height, $ehsVal);
                } else {
                    $this->fpdf->Write($line_height, $ehsVal);
                }
            }
        }

        // 11. Participant Photo
        $photoX = (float)($certificate_template[0]['photoX'] ?? 0);
        $photoY = (float)($certificate_template[0]['photoY'] ?? 0);
        $rawW   = (float)($certificate_template[0]['photoWidth'] ?? 0);
        $rawH   = (float)($certificate_template[0]['photoHight'] ?? 0);

        if ($photoX > 0 && $photoY > 0) {
            // Auto-scale pixel dimensions (150x200 px) to millimeters (at 96 DPI: 150px -> 39.7mm, 200px -> 52.9mm)
            if ($rawW > 50) {
                $photoW = round($rawW * 25.4 / 96, 1);
            } elseif ($rawW > 0) {
                $photoW = $rawW;
            } else {
                $photoW = 39.7;
            }

            if ($rawH > 50) {
                $photoH = round($rawH * 25.4 / 96, 1);
            } elseif ($rawH > 0) {
                $photoH = $rawH;
            } else {
                $photoH = 52.9;
            }

            @$pic_pathName = ($participant_array['school_code'] ?? '') . "_" . ($participant_array['admn_no'] ?? '');
            @$pic_path = $this->Photos_Model->get_Photo($pic_pathName, $participant_array['school_code'] ?? '');

            $photo_file = null;
            if (!empty($pic_path)) {
                $rel = str_replace(base_url(), '', $pic_path);
                if (file_exists(ROOTPATH . $rel)) {
                    $photo_file = ROOTPATH . $rel;
                } elseif (file_exists(FCPATH . $rel)) {
                    $photo_file = FCPATH . $rel;
                } elseif (file_exists($pic_path)) {
                    $photo_file = $pic_path;
                } else {
                    $photo_file = $pic_path;
                }
            }

            if ($photo_file) {
                try {
                    $this->fpdf->Rect($photoX - 0.5, $photoY - 0.5, $photoW + 1, $photoH + 1);
                    $this->fpdf->Image($photo_file, $photoX, $photoY, $photoW, $photoH);
                } catch (\Throwable $e) {
                    // Fail gracefully if image format unsupported
                }
            }
        }

        // 12. Issue Date
        if ((float)($certificate_template[0]['date_x'] ?? 0) > 0 && (float)($certificate_template[0]['date_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['date_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['date_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['date_x'], $certificate_template[0]['date_y']);

            $fest_dates = $this->General_Model->get_fest_date_array();
            $end_date = (!empty($fest_dates) && is_array($fest_dates)) ? end($fest_dates) : date('d-m-Y');
            $this->fpdf->Write($line_height, ($label_print == 'Y' ? 'Date : ' . $end_date : $end_date));
        }

        // 13. Place
        if ((float)($certificate_template[0]['place_x'] ?? 0) > 0 && (float)($certificate_template[0]['place_y'] ?? 0) > 0) {
            $this->fpdf->SetFont($certificate_template[0]['place_font'] ?: 'TAU-Pallai', 'B', $certificate_template[0]['place_size'] ?: 14);
            $this->fpdf->SetXY($certificate_template[0]['place_x'], $certificate_template[0]['place_y']);

            $placeName = !empty($participant_array['rev_district_name']) ? $participant_array['rev_district_name'] : (!empty($participant_array['sub_district_name']) ? $participant_array['sub_district_name'] : 'Palakkad');
            $this->fpdf->Write($line_height, ($label_print == 'Y' ? 'Place : ' . $placeName : $placeName));
        }
    }

    /**
     * School-wise certificate list (district scope)
     */
    public function list_school_wise()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $school_details = $this->Certificate_Model->school_details_certificate_wise();
        $this->Contents['school_details'] = $school_details;
        $this->template->write_view('content', 'district/certificate/certificate_details_school_wise', $this->Contents);
        $this->template->load();
    }

    public function get_certificate_school_wise()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $all_schools = $this->Certificate_Model->school_details_certificate_wise();
        $this->Contents['all_schools'] = $all_schools;

        $school_code = $this->input->get_post('hidSchoolCode') ?: $this->input->get_post('school_code');
        if (empty($school_code) && !empty($all_schools)) {
            $school_code = $all_schools[0]['school_code'];
        }

        if (!empty($school_code)) {
            $this->Contents['selected_school_code'] = $school_code;
            $this->Contents['fest'] = $this->Certificate_Model->get_school_festival_details($school_code);
            $this->Contents['school_details'] = $this->General_Model->get_data('d_school_master', 'school_code, school_name', array('school_code' => $school_code));
            $this->Contents['all_participants'] = $this->Certificate_Model->get_school_participant_details($school_code, '', '', '', '');
            $this->template->write_view('content', 'district/certificate/school_item_certificate_details', $this->Contents);
            $this->template->load();
        } else {
            return redirect()->to(base_url('district/certificate/certificate/list_school_wise'));
        }
    }

    public function get_certificate_pdf_school_wise()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $school_code = $this->input->get_post('hidSchoolCode') ?: $this->input->get_post('school_code');
        if (!empty($school_code)) {
            $dist_code = $this->get_current_district_code();
            $certificate_template = $this->Certificate_Model->get_certificate_details($dist_code);

            $festival = $this->input->get_post('cmbFestType') ?: '';
            $item_code = $this->input->get_post('item_code') ?: '';
            $captain_id = $this->input->get_post('captain_id') ?: '';
            $participant_id = $this->input->get_post('participant_id') ?: '';

            if ($item_code === '0' || $item_code === 0) $item_code = '';
            if ($captain_id === '0' || $captain_id === 0) $captain_id = '';
            if ($participant_id === '0' || $participant_id === 0) $participant_id = '';

            $participant_array = $this->Certificate_Model->get_school_participant_details($school_code, $festival, $item_code, $captain_id, $participant_id);

            if (empty($participant_array) || !is_array($participant_array)) {
                $this->session->setFlashdata('error', 'No eligible participants found for the selected school criteria.');
                return redirect()->to(base_url('district/certificate/certificate/get_certificate_school_wise?school_code=' . $school_code));
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
            return redirect()->to(base_url('district/certificate/certificate/list_school_wise'));
        }
    }

    /**
     * Registration number-wise certificate lookup (district scope)
     */
    public function list_reg_no_wise()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $participant_id = $this->input->get_post('txtParticipantId') ?: $this->input->get_post('participant_id');
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
        $this->template->write_view('content', 'district/certificate/participant_certificate_details', $this->Contents);
        $this->template->load();
    }

    public function get_certificate_pdf_participant_wise()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $participant_id = $this->input->get_post('txtParticipantId') ?: $this->input->get_post('participant_id');
        if (!empty($participant_id)) {
            $dist_code = $this->get_current_district_code();
            $certificate_template = $this->Certificate_Model->get_certificate_details($dist_code);

            $item_code = $this->input->get_post('item_code') ?: '';
            if ($item_code === '0' || $item_code === 0) $item_code = '';

            $participant_array = $this->Certificate_Model->get_school_participant_details('', '', $item_code, '', $participant_id);
            if (empty($participant_array) || !is_array($participant_array)) {
                $this->session->setFlashdata('error', 'No certificate found for this participant and item.');
                return redirect()->to(base_url('district/certificate/certificate/list_reg_no_wise'));
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
            return redirect()->to(base_url('district/certificate/certificate/list_reg_no_wise'));
        }
    }
}
