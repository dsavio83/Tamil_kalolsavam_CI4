<?php

namespace App\Controllers\District\Report;

use App\Controllers\BaseController;

class Reports extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->Session_Model->is_user_logged(true);
        $this->load->model('District/Report/Tabulation_Model');
        $this->load->model('District/Stage/Festival_Scheduling_Model');
        $this->load->model('District/Report/Prereport_Model');
        $this->load->model('Pictures/Photos_Model');
        $this->load->library('html2pdf');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
    }

    protected function check_district_access(): bool
    {
        $userType  = (int)$this->session->get('USER_TYPE');
        $userGroup = (string)$this->session->get('USER_GROUP');
        return ($userGroup === 'W' || $userType === 2);
    }

    /**
     * Dual Championship Scoreboard Landing
     */
    public function index()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $districtCode = (int)$this->session->get('DISTRICT');
        $festId       = (int)($this->request->getGet('fest_id') ?? 0);

        $schoolPoints     = $this->Tabulation_Model->get_school_championship_points($districtCode, $festId);
        $subdistTrophy    = $this->Tabulation_Model->get_subdistrict_rolling_trophy_points($districtCode, $festId);

        $this->Content = [
            'district_code'  => $districtCode,
            'fest_id'        => $festId,
            'school_points'  => $schoolPoints,
            'subdist_trophy' => $subdistTrophy,
        ];

        $this->template->write_view('content', 'district/report/championship_view', $this->Content);
        return $this->template->load();
    }


    /**
     * Participant Cards (Without Photo) - District Scope
     */
    public function participant_cardindex()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $districtCode = (int)$this->session->get('DISTRICT');
        $sub_dist_array = $this->General_Model->prepare_select_box_data(
            'sub_district_master',
            'sub_district_code,sub_district_name',
            ['rev_district_code' => $districtCode],
            'Select Sub-District',
            'sub_district_code'
        );

        $this->Content = [
            'district_code'  => $districtCode,
            'sub_dist_array' => $sub_dist_array,
        ];

        $this->template->write_view('content', 'district/report/participant_cardindex', $this->Content);
        return $this->template->load();
    }

    /**
     * Participant Cards (With Photo) - District Scope
     */
    public function participant_cardindex_photo()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $districtCode = (int)$this->session->get('DISTRICT');
        $sub_dist_array = $this->General_Model->prepare_select_box_data(
            'sub_district_master',
            'sub_district_code,sub_district_name',
            ['rev_district_code' => $districtCode],
            'Select Sub-District',
            'sub_district_code'
        );

        $this->Content = [
            'district_code'  => $districtCode,
            'sub_dist_array' => $sub_dist_array,
        ];

        $this->template->write_view('content', 'district/report/participant_cardindex_photo', $this->Content);
        return $this->template->load();
    }

    /**
     * AJAX: Get schools in sub-district that have registered participants
     */
    public function ajax_get_schools_by_subdistrict()
    {
        if (!$this->check_district_access()) {
            return $this->response->setJSON([]);
        }

        $subDistCode = $this->request->getGetPost('sub_district_code') ?: $this->input->get_post('sub_district_code');
        if (empty($subDistCode)) {
            return $this->response->setJSON([]);
        }

        $schools = $this->Prereport_Model->list_school_details($subDistCode);
        return $this->response->setJSON($schools);
    }

    /**
     * Participant Cards PDF Generation (Without Photo)
     * Supports single school and 1-click batch download of all schools in sub-district.
     */
    public function participant_cardindex_pdf()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        @set_time_limit(300);
        @ini_set('max_execution_time', '300');
        @ini_set('memory_limit', '512M');

        @set_time_limit(300);
        @ini_set('max_execution_time', '300');
        @ini_set('memory_limit', '512M');

        if (!class_exists('HTML2PDF')) {
            require_once APPPATH . 'Libraries/_html2pdf.php';
        }

        $schoolCode = $this->request->getGetPost('txtSchoolCode') ?: $this->input->get_post('txtSchoolCode');
        $subDistCode = $this->request->getGetPost('cboSubDistCode') ?: $this->input->get_post('cboSubDistCode');

        $content = '';

        if ($schoolCode === 'ALL' || empty($schoolCode)) {
            // Batch mode: all schools in sub-district
            if (empty($subDistCode)) {
                return redirect()->to('report/prereport/participant_cardindex');
            }
            $schools = $this->Prereport_Model->list_school_details($subDistCode);
            foreach ($schools as $sch) {
                $sch_code = $sch['school_code'];
                $participants = $this->Prereport_Model->get_participant_details($sch_code);
                if (!empty($participants)) {
                    $content .= $this->load->view('district/report/pt_district', [
                        'schools_data' => [[
                            'school'       => $sch,
                            'participants' => $participants,
                        ]],
                    ], true);
                    unset($participants);
                }
            }
            unset($schools);
        } else {
            // Single school mode
            $participants = $this->Prereport_Model->get_participant_details($schoolCode);
            if (!empty($participants)) {
                $content = $this->load->view('district/report/pt_district', [
                    'schools_data' => [[
                        'school' => [
                            'school_code' => $schoolCode,
                            'school_name' => $participants[0]['school_name'] ?? '',
                        ],
                        'participants' => $participants,
                    ]],
                ], true);
            }
            unset($participants);
        }

        if ($content === '') {
            return redirect()->to('test/nodata');
        }

        $html2pdf = new \HTML2PDF('L', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('participantcard.pdf', 'I');
        exit;
    }

    /**
     * Participant Cards PDF Generation (With Photo)
     * Supports single school and 1-click batch download of all schools in sub-district with photos.
     */
    public function participant_cardindex_photo_pdf()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        if (!class_exists('HTML2PDF')) {
            require_once APPPATH . 'Libraries/_html2pdf.php';
        }

        $schoolCode = $this->request->getGetPost('txtSchoolCode') ?: $this->input->get_post('txtSchoolCode');
        $subDistCode = $this->request->getGetPost('cboSubDistCode') ?: $this->input->get_post('cboSubDistCode');

        if ($schoolCode === 'ALL' || empty($schoolCode)) {
            // Batch mode: all schools in sub-district with photo
            if (empty($subDistCode)) {
                return redirect()->to('report/prereport/participant_cardindex_photo');
            }
            $schools = $this->Prereport_Model->list_school_details($subDistCode);
            $schoolRows = $schools;
        } else {
            $schoolRows = [[
                'school_code' => (string) $schoolCode,
                'school_name' => '',
            ]];
        }

        $batchService = new \App\Libraries\PhotoPdfBatchService();
        $imageService = new \App\Libraries\PhotoPdfImageService();
        try {
            $artifact = $batchService->generate(
                $schoolRows,
                function ($code) {
                    return $this->Prereport_Model->get_participant_details_max_id($code);
                },
                function ($code, $afterId, $limit, $maxId) {
                    return $this->Prereport_Model->get_participant_details_batch($code, $afterId, $limit, $maxId);
                },
                function ($school, $participants, $jobDirectory, array &$warnings, $batchNumber) use ($imageService, $batchService) {
                    $code = (string) $school['school_code'];
                    $photoMap = $this->Photos_Model->get_schoolwise_Photo(['participant_det' => $participants], $code, true);
                    $batchService->recordMetric('after_photo_load', $code, $batchNumber, count($participants));
                    $prepared = $imageService->prepareBatch($participants, $photoMap ?: [], $jobDirectory, $code, $batchNumber);
                    $schoolEntry = $school;
                    if (empty($schoolEntry['school_name'])) {
                        $schoolEntry['school_name'] = $participants[0]['school_name'] ?? '';
                    }
                    $html = $this->load->view('district/report/pt_photo_district', [
                        'schools_data' => [[
                            'school' => $schoolEntry,
                            'participants' => $participants,
                            'Photo' => $prepared['photos'],
                    ]],
                    ], true);
                    $batchService->recordMetric('after_html_generation', $code, $batchNumber, count($participants));
                    $warnings = array_merge($warnings, $prepared['warnings']);
                    unset($photoMap, $prepared, $schoolEntry);
                    return $html;
                },
                'participantcard_photo.pdf'
            );
        } catch (\Throwable $exception) {
            if ($exception instanceof \App\Exceptions\PhotoPdfNoParticipantsException) {
                return redirect()->to('test/nodata');
            }
            log_message('error', 'District photo-card PDF failed: {message}', ['message' => $exception->getMessage()]);
            return $this->response->setStatusCode(500)->setBody('Photo card generation failed. Please retry or contact the administrator with the time of the request.');
        }

        unset($schools, $schoolRows);
        $response = $this->response->download($artifact['path'], null, true)->setFileName($artifact['name']);
        $disposition = $artifact['mime'] === 'application/pdf' ? 'inline' : 'attachment';
        $response->setHeader('Content-Disposition', $disposition . '; filename="' . $artifact['name'] . '"');
        return $response;
    }
}
