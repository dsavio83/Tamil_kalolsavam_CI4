<?php

namespace App\Controllers\District\Report;

use App\Controllers\BaseController;

/**
 * Pre-Fest Report PDF Generators (District)
 *
 * Mirrors App\Controllers\Report\Prereportpdf for the standalone district
 * module. All methods guard with check_district_access() and use the
 * district-scoped Prereport_Model (d_* tables). Views live under
 * district/report/prereportpdf/.
 */
class Prereportpdf extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        @set_time_limit(300);
        @ini_set('max_execution_time', '300');
        @ini_set('memory_limit', '512M');
        $this->Session_Model->is_user_logged(true);
        $this->template->add_js('js/report/staticreport.js');
        $this->template->add_js('js/report/reportjs.js');
        $this->load->library('html2pdf');
        $this->load->model('District/Report/Prereport_Model');
        $this->load->model('Pictures/Photos_Model');
    }

    protected function check_district_access(): bool
    {
        $userType  = (int)$this->session->get('USER_TYPE');
        $userGroup = (string)$this->session->get('USER_GROUP');
        return ($userGroup === 'W' || $userType === 2);
    }

    public function list_school()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Content = array();
        $fest_id = $this->input->post('txtfestFrom');

        if ($fest_id === 'ALL' || $fest_id === 'all') {
            $this->schoolfestall();
            exit;
        }

        if (!empty($fest_id) && $fest_id != 0) {
            $school_fest = $this->Prereport_Model->participate_school_details();
            $part_details = $this->Prereport_Model->participate_item_details($fest_id);
            $festdetails = $this->Prereport_Model->itempart_details();
            $subdistrict = $this->Prereport_Model->find_subdistrict();

            $fest_name = '';
            if (!empty($festdetails)) {
                foreach ($festdetails as $fm) {
                    if ($fm['fest_id'] == $fest_id) {
                        $fest_name = $fm['fest_name'];
                        break;
                    }
                }
            }

            $this->Content['school_fest'] = $school_fest;
            $this->Content['festtype'] = $part_details;
            $this->Content['festmaster'] = $festdetails;
            $this->Content['fest'] = $fest_id;
            $this->Content['fest_name'] = $fest_name;
            $this->Content['subdist'] = $subdistrict;
            $content = $this->load->view('district/report/prereportpdf/list_school', $this->Content, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Participatingschools_' . $fest_id . '.pdf', 'I');
            exit;
        } else {
            echo "<script>alert('Please select a Festival.'); window.close();</script>";
            exit;
        }
    }

    public function list_school_with_team()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Content = array();
        $school_details = $this->Prereport_Model->list_all_school_details();

        $this->Content['school_details'] = $school_details;

        $content = $this->load->view('district/report/prereportpdf/list_school_with_team', $this->Content, true);

        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('Participatingschools.pdf', 'I');
        exit;
    }

    public function schoolfestall()
    {
        $this->Contents = array();
        $this->Contents['school_details'] = $this->Prereport_Model->schoolalldetails();
        $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();
        $content = $this->load->view('district/report/prereportpdf/schoolfestall', $this->Contents, true);

        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('Allparticipatingschools.pdf', 'I');
        exit;
    }

    public function list_participants()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Content = array();
        $school_code = (int)$this->input->post('txtSchoolCode');
        $festpart_school = $this->input->post('Festpart');

        if ($school_code > 0) {
            $part_details = $this->Prereport_Model->part_item_details($school_code, $festpart_school);
            if (count($part_details) > 0) {
                $this->Content['subdistrict'] = $this->Prereport_Model->find_subdistrict();
                $this->Content['part_details'] = $part_details;
                $content = $this->load->view('district/report/prereportpdf/list_participant_report', $this->Content, true);

                $html2pdf = new \HTML2PDF('P', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('Listofparticipantforteam_manager.pdf', 'I');
                return;
            }
        }
        redirect('test/nodata');
    }

    public function time_date_stage()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $itemtime = $this->Prereport_Model->time_date_stage(($this->input->post('txtDate')), ($this->input->post('cmbstage')));
        $this->Contents['itemtime'] = $itemtime;
        if (count($itemtime) > 0) {
            $content = $this->load->view('district/report/prereportpdf/time_date_stage', $this->Contents, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Time_sheet.pdf', 'I');
            exit;
        } else {
            redirect('test/nodata');
        }
    }

    public function timesheet_date()
    {
        $this->Contents = array();
        $this->template->write('title', '');
        $date = $this->input->post('txtDate') ?: 'All';
        $festival = $this->input->post('cmbFestType');
        $itemtime = $this->Prereport_Model->timesheet_date($festival, $date);
        if (count($itemtime) > 0) {
            $this->Contents['itemtime'] = $itemtime;
            $this->Contents['festname'] = $this->Prereport_Model->Festname($festival);
            $content = $this->load->view('district/report/prereportpdf/timesheet_date', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Timesheet_date.pdf', 'I');
            exit;
        } else {
            redirect('test/nodata');
        }
    }

    public function list_participants_datewise()
    {
        $this->Content = array();
        $date = $_POST['txtDate'];

        $partdata = $this->Prereport_Model->list_participants_datewise($this->input->post('cmbFestType1'), $date);
        $this->Content['partdata'] = $partdata;
        $this->Content['date'] = $date;
        $this->Content['subdistrict'] = $this->Prereport_Model->find_subdistrict();
        if (count($partdata) > 0) {
            $content = $this->load->view('district/report/prereportpdf/list_participants_datewise', $this->Content, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('List_of_Participants.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    public function list_participants_stagewise()
    {
        $this->Content = array();
        $stage = $_POST['cmbstage'];

        $partdata = $this->Prereport_Model->list_participants_stagewise($this->input->post('txtDate'), $stage);
        $this->Content['partdata'] = $partdata;
        $this->Content['stage'] = $stage;
        $this->Content['subdistrict'] = $this->Prereport_Model->find_subdistrict();
        if (count($partdata) > 0) {
            $content = $this->load->view('district/report/prereportpdf/list_participants_stagewise', $this->Content, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('List_of_Participants.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    public function team_manager_all()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        @set_time_limit(300);
        @ini_set('max_execution_time', '300');
        @ini_set('memory_limit', '512M');

        $this->Contents = array();
        $festType = $this->input->post('cmbFestType') ?: 'ALL';
        $subDistrict = $this->input->post('cmbSubDistrict') ?: 'ALL';

        $part_details = $this->Prereport_Model->part_item_details_allschool($festType, $subDistrict);
        $this->Contents['part_details'] = $part_details;
        $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();
        if (count($part_details) > 0) {

            $content = $this->load->view('district/report/prereportpdf/team_manager_all', $this->Contents, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $filename = ($subDistrict && $subDistrict !== 'ALL')
                ? 'AllListofparticipant_SubDistrict_' . $subDistrict . '.pdf'
                : 'AllListofparticipantforteam_manager.pdf';
            $html2pdf->Output($filename, 'I');
            exit;
        } else {
            redirect('test/nodata');
        }
    }

    public function feedetails()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Content = array();
        $getschool = array();
        $getschool = $this->Prereport_Model->get_fee_school_single($this->input->post('txtSchoolCode'));
        if (count($getschool) > 0) {
            $fees_details = $this->Prereport_Model->get_fees_details($this->input->post('txtSchoolCode'));
            $this->Content['fees_details'] = $fees_details;
            $this->Content['subdistrict'] = $this->Prereport_Model->find_subdistrict();

            $content = $this->load->view('district/report/prereportpdf/feedetails', $this->Content, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('feedetails.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    public function all_feedet_report_all()
    {
        $this->Content = array();
        $fees_details = $this->Prereport_Model->get_fees_details_all();
        $this->Content['fees_details'] = $fees_details;
        $this->Content['subdistrict'] = $this->Prereport_Model->find_subdistrict();

        $content = $this->load->view('district/report/prereportpdf/feedetailsviewall', $this->Content, true);

        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('Feedetailsall.pdf', 'I');
    }

    public function callsheet_report()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Content = array();
        if ($this->input->post('cbo_item') != 'ALL') {
            $fees_details = $this->Prereport_Model->get_callsheet_details($this->input->post('cbo_item'));
            $this->Content['fees_details'] = $fees_details;
            $this->Content['subdistrict'] = $this->Prereport_Model->find_subdistrict();
            if (count($fees_details) > 0) {
                $content = $this->load->view('district/report/prereportpdf/callsheet_report', $this->Content, true);

                $html2pdf = new \HTML2PDF('P', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('Callsheet.pdf', 'I');
                exit;
            } else {
                redirect('test/nodata');
            }
        } else {
            $this->Content = array();

            $fees_details = $this->Prereport_Model->all_callsheet_details($this->input->post('cmbFestType'));
            $this->Content['fees_details'] = $fees_details;
            $this->Content['subdistrict'] = $this->Prereport_Model->find_subdistrict();
            if (count($fees_details) > 0) {
                $content = $this->load->view('district/report/prereportpdf/callsheet_all', $this->Content, true);

                $html2pdf = new \HTML2PDF('P', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('Callsheetall.pdf', 'I');
                exit;
            } else {
                redirect('test/nodata');
            }
        }
    }

    public function callsheet_all()
    {
        $this->Content = array();

        $festId = $this->input->post('cmbFestType1') ?: $this->input->post('cmbFestType');
        $fees_details = $this->Prereport_Model->all_callsheet_details($festId);
        $this->Content['fees_details'] = $fees_details;
        $this->Content['subdistrict'] = $this->Prereport_Model->find_subdistrict();
        if (count($fees_details) > 0) {
            $content = $this->load->view('district/report/prereportpdf/callsheet_all', $this->Content, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Callsheetall.pdf', 'I');
            exit;
        } else {
            redirect('test/nodata');
        }
    }

    public function tabulation_date_stage()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $retvalue = $this->Prereport_Model->tabulation_date_stage(($this->input->post('txtDate')), ($this->input->post('cmbstage')));
        $this->Contents['retvalue'] = $retvalue;
        if (count($retvalue) > 0) {
            $content = $this->load->view('district/report/prereportpdf/tabulation_date_stage', $this->Contents, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('tabulation.pdf', 'I');
            exit;
        } else {
            redirect('test/nodata');
        }
    }

    public function score_date_stage()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $fest_details = $this->Prereport_Model->score_date_stage(($this->input->post('txtDate')), ($this->input->post('cmbstage')));
        $this->Contents['fest_details'] = $fest_details;
        if (count($fest_details) > 0) {
            $content = $this->load->view('district/report/prereportpdf/rpt_fest_scoresheet', $this->Contents, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('score_sheet.pdf', 'I');
            exit;
        } else {
            redirect('test/nodata');
        }
    }

    public function call_date_stage()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $fees_details = $this->Prereport_Model->call_date_stage(($this->input->post('txtDate')), ($this->input->post('cmbstage')));
        $this->Contents['fees_details'] = $fees_details;
        if (count($fees_details) > 0) {
            $content = $this->load->view('district/report/prereportpdf/call_date_stage', $this->Contents, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Call_sheet.pdf', 'I');
            exit;
        } else {
            redirect('test/nodata');
        }
    }

    public function participant_cardindex()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Content = array();
        $school_code = $this->input->post('txtSchoolCode');
        if ($school_code) {
            $participant_details = $this->Prereport_Model->get_participant_details($this->input->post('txtSchoolCode'));
            $this->Content['participant_details'] = $participant_details;

            if (count($participant_details) > 0) {

                $content = $this->load->view('district/report/prereportpdf/pt', $this->Content, true);

                $html2pdf = new \HTML2PDF('L', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('participantcard.pdf', 'I');
            } else {
                redirect('test/nodata');
            }
        }
    }

    public function participant_cardindex_school_photo()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Content = array();

        $school_code = $this->input->get_post('txtSchoolCode');
        if (empty($school_code)) {
            redirect('district/report/prereport/participant_cardindex_photo');
            return;
        }

        $batchService = new \App\Libraries\PhotoPdfBatchService();
        $imageService = new \App\Libraries\PhotoPdfImageService();
        try {
            $artifact = $batchService->generate(
                [['school_code' => (string) $school_code]],
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
                    $html = $this->load->view('district/report/pt_photo_district', [
                        'schools_data' => [[
                            'school' => [
                                'school_code' => $code,
                                'school_name' => $participants[0]['school_name'] ?? '',
                            ],
                            'participants' => $participants,
                            'Photo' => $prepared['photos'],
                        ]],
                    ], true);
                    $batchService->recordMetric('after_html_generation', $code, $batchNumber, count($participants));
                    $warnings = array_merge($warnings, $prepared['warnings']);
                    unset($photoMap, $prepared);
                    return $html;
                },
                'participantcard.pdf'
            );
        } catch (\Throwable $exception) {
            if ($exception instanceof \App\Exceptions\PhotoPdfNoParticipantsException) {
                return redirect()->to('test/nodata');
            }
            log_message('error', 'District participant photo-card PDF failed for school {school}: {message}', [
                'school' => (string) $school_code,
                'message' => $exception->getMessage(),
            ]);
            return $this->response->setStatusCode(500)->setBody('Photo card generation failed. Please retry or contact the administrator with the time of the request.');
        }

        $response = $this->response->download($artifact['path'], null, true)->setFileName($artifact['name']);
        $disposition = $artifact['mime'] === 'application/pdf' ? 'inline' : 'attachment';
        $response->setHeader('Content-Disposition', $disposition . '; filename="' . $artifact['name'] . '"');
        return $response;
    }

    public function participant_regno_photo()
    {
        $this->Content = array();
        $partcard = $this->Prereport_Model->get_participant_regno($this->input->post('regno'));
        $partcard1 = $this->Prereport_Model->get_participant_regno($this->input->post('regno1'));
        $partcard2 = $this->Prereport_Model->get_participant_regno($this->input->post('regno2'));

        $this->Content['partcard'] = $partcard;
        $this->Content['partcard1'] = $partcard1;
        $this->Content['partcard2'] = $partcard2;

        if (count($partcard) > 0) {

            $this->Content['Photo'] = $this->Photos_Model->get_participant_regno_Photo($this->Content);
            $content = $this->load->view('district/report/prereportpdf/participant_regno_photo', $this->Content, true);

            $html2pdf = new \HTML2PDF('L', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('participantcard.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    public function participant_cardindex_school2()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Content = array();
        $school_code = $this->input->get_post('txtSchoolCode');
        if (empty($school_code)) {
            redirect('district/report/prereport/participant_cardindex2');
            return;
        }
        $participant_details = $this->Prereport_Model->get_participant_details($school_code);
        $this->Content['participant_details'] = $participant_details;

        if (count($participant_details) > 0) {

            $content = $this->load->view('district/report/prereportpdf/participation_cardstate_single_withoutphoto_middle', $this->Content, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('participantcard.pdf', 'I');
            return;
        } else {
            redirect('test/nodata');
        }
    }

    public function participant_regno()
    {
        $this->Content = array();
        $partcard = $this->Prereport_Model->get_participant_regno($this->input->post('regno'));
        $this->Content['partcard'] = $partcard;

        if (count($partcard) > 0) {

            $content = $this->load->view('district/report/prereportpdf/participant_regno', $this->Content, true);

            $html2pdf = new \HTML2PDF('L', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('participantcard.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    public function participant_regnowithphoto()
    {
        $this->Content = array();
        $partcard = $this->Prereport_Model->get_participant_regno($this->input->post('regno'));
        $this->Content['partcard'] = $partcard;
        if (count($partcard) > 0) {

            $content = $this->load->view('district/report/prereportpdf/participant_state_regno_middle', $this->Content, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('participantcard.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    public function participant_more_item()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Content = array();
        $fees_details = $this->Prereport_Model->list_participant_more($this->input->post('cmbFestType'));
        $this->Content['fees_details'] = $fees_details;

        if (count($fees_details) > 0) {
            $content = $this->load->view('district/report/prereportpdf/participant_more_item', $this->Content, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('participantmorethanoneitem.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    public function more_limit_partlist()
    {
        $this->Content = array();
        $fest_id = $this->input->post('cmbFestType');
        $limit = $this->input->post('txtLimitcode');
        if (!empty($fest_id)) {
            $fees_details = $this->Prereport_Model->list_more_limitpart($fest_id, $limit);
            $this->Content['fees_details'] = $fees_details;
            $this->Content['subdistrict'] = $this->Prereport_Model->find_subdistrict();

            $content = $this->load->view('district/report/prereportpdf/participant_limit_item_more', $this->Content, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('partmorethanoneitem.pdf', 'I');
            exit;
        } else {
            echo "<script>alert('Please select a festival category.'); window.close();</script>";
            exit;
        }
    }

    public function more_limit_partlist_detailed()
    {
        $this->Content = array();
        $fest_id = $this->input->post('cmbFestType');
        $limit = $this->input->post('txtLimitcode');
        if (!empty($fest_id)) {
            $participants = $this->Prereport_Model->list_more_limitpart_with_items($fest_id, $limit);
            if (empty($participants)) {
                redirect('test/nodata');
                return;
            }
            $this->Content['participants'] = $participants;
            $this->Content['subdistrict'] = $this->Prereport_Model->find_subdistrict();

            $content = $this->load->view('district/report/prereportpdf/participant_limit_item_more_detailed', $this->Content, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Multiple_Items_Participants_With_Details.pdf', 'I');
            exit;
        } else {
            echo "<script>alert('Please select a festival category.'); window.close();</script>";
            exit;
        }
    }

    public function eligible_school()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $school_details = $this->Prereport_Model->list_eligible_schools();
        $this->Contents['school_details'] = $school_details;
        $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();
        $content = $this->load->view('district/report/prereportpdf/eligible_school', $this->Contents, true);

        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('Eligible_schools.pdf', 'I');
        exit;
    }

    public function clusterreport_report()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $fest_id = $this->input->post('cmbFestType');
        $item_code = $this->input->post('cbo_item');

        if (!empty($fest_id)) {
            $festData = $this->Prereport_Model->Festname($fest_id);
            $this->Contents['fest_name'] = (!empty($festData) && isset($festData[0]['fest_name'])) ? $festData[0]['fest_name'] : '';

            if ($item_code != 'ALL' && !empty($item_code)) {
                $retdata = $this->Prereport_Model->cluster_reportreport($fest_id, $item_code);
                $this->Contents['retdata'] = !empty($retdata) ? $retdata : [];
                $content = $this->load->view('district/report/prereportpdf/clustor_report', $this->Contents, true);
            } else {
                $retdata = $this->Prereport_Model->cluster_report_all($fest_id);
                $this->Contents['retdata'] = !empty($retdata) ? $retdata : [];
                $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();
                $content = $this->load->view('district/report/prereportpdf/clustor_report_all', $this->Contents, true);
            }

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('cluster.pdf', 'I');
            exit;
        } else {
            echo "<script>alert('Please select a festival.'); window.close();</script>";
            exit;
        }
    }

    public function cluster_report_all()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $fest_id = $this->input->post('cmbFestType1');
        $festData = $this->Prereport_Model->Festname($fest_id);
        $this->Contents['fest_name'] = (!empty($festData) && isset($festData[0]['fest_name'])) ? $festData[0]['fest_name'] : '';
        $retdata = $this->Prereport_Model->cluster_report_all($fest_id);
        $this->Contents['retdata'] = !empty($retdata) ? $retdata : [];
        $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();

        $content = $this->load->view('district/report/prereportpdf/clustor_report_all', $this->Contents, true);

        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('clusterall.pdf', 'I');
        exit;
    }

    public function cluster_report_all_doc()
    {
        $this->Contents = array();
        $fest_id = $this->input->post('cmbFestType1');
        $festData = $this->Prereport_Model->Festname($fest_id);
        $this->Contents['fest_name'] = (!empty($festData) && isset($festData[0]['fest_name'])) ? $festData[0]['fest_name'] : '';
        $retdata = $this->Prereport_Model->cluster_report_all($fest_id);
        $this->Contents['retdata'] = !empty($retdata) ? $retdata : [];
        $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();
        if (count($this->Contents['retdata']) > 0) {
            header("Content-type: application/vnd.txt");
            header("Content-Disposition: attachment; filename=cluster_report.txt");
            $this->load->view('district/report/prereportpdf/clustor_report_all_doc', $this->Contents);
            exit;
        } else {
            echo "<script>alert('No data found for press report.'); window.close();</script>";
            exit;
        }
    }

    public function cluster_date_stage()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $retdata = $this->Prereport_Model->cluster_date_stage(($this->input->post('txtDate')), ($this->input->post('cmbstage')));
        $this->Contents['retdata'] = $retdata;

        $content = $this->load->view('district/report/prereportpdf/cluster_report_date', $this->Contents, True);

        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('cluster.pdf', 'I');
        exit;
    }

    public function rpt_item_with_item_code()
    {
        $this->Contents = array();
        $festdata = $this->Prereport_Model->item_list();
        $this->Contents['festdata'] = $festdata;
        $content = $this->load->view('district/report/prereportpdf/rpt_list_of_items', $this->Contents, true);
        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('itemwise_participant.pdf', 'I');
    }

    public function print_score_sheet()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $ItemCode = $_POST['cbo_item'];
        $date = $this->Prereport_Model->fetch_item_name2($ItemCode);
        if (is_array($date) && count($date) > 0) {
            $this->Contents = array();

            $this->Contents['itemcode'] = $ItemCode;
            $this->Contents['start_time'] = $date;

            $content = $this->load->view('district/report/prereportpdf/rpt_scoresheet', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('scoresheet.pdf', 'I');
            exit;
        } else {

            redirect('test/nodata');
        }
    }

    public function fest_details()
    {
        $festival_id = $this->input->post('txtfestFrom');
        $this->Content = array();
        $fest_details = $this->Prereport_Model->fetch_fest_name($festival_id);
        $this->Content['festdata'] = $fest_details;
        $content = $this->load->view('district/report/prereportpdf/fest_details', $this->Content, true);
        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('festdetails.pdf', 'I');
        exit;
    }

    public function fest_scoresheet_details()
    {
        $fest_id = $this->input->post('txtfestFrom');
        $this->Contents = array();
        $fest_details = $this->Prereport_Model->fetch_fest_scoresheet($fest_id);
        if (count($fest_details) > 0) {
            $this->Contents['fest_details'] = $fest_details;
            $content = $this->load->view('district/report/prereportpdf/rpt_fest_scoresheet', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('festscoresheet.pdf', 'I');
            exit;
        } else {

            redirect('test/nodata');
        }
    }

    public function timesheet()
    {
        $this->Contents = array();
        $this->template->write('title', '');
        $itemcode = $this->input->post('cbo_item');
        $festival = $this->input->post('cmbFestType');
        $this->Contents['itemtime'] = $this->Prereport_Model->timesheet($itemcode);

        $sessDist = (int)$this->session->get('DISTRICT');
        $b = $this->db->table('d_participant_item_details')->where('item_code', $itemcode);
        if ($sessDist > 0) $b->where('district_code', $sessDist);
        $this->Contents['item_count'] = $b->countAllResults();

        $this->Contents['festname'] = $this->Prereport_Model->Festname($festival);
        $content = $this->load->view('district/report/prereportpdf/timesheet', $this->Contents, true);
        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('Timesheet.pdf', 'I');
        exit;
    }

    public function datewisepart()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $date = $this->input->post('txtDate');
        if ($date != 'All') {

            $this->Contents['partdata'] = $this->Prereport_Model->datewise_participants($date);
            $lp = $this->Prereport_Model->lpstudents_date($date);
            $up = $this->Prereport_Model->upstudents_date($date);
            $hs = $this->Prereport_Model->hsstudents_date($date);
            $hss = $this->Prereport_Model->hssstudents_date($date);
            $school = $this->Prereport_Model->school_lpdetails();
            $this->Contents['lp'] = $lp;
            $this->Contents['up'] = $up;
            $this->Contents['hs'] = $hs;
            $this->Contents['hss'] = $hss;
            $this->Contents['school'] = $school;
            $this->Contents['date'] = $date;

            $content = $this->load->view('district/report/prereportpdf/datewisepart', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('datewise_participant.pdf', 'I');
            exit;
        } else {

            $this->Contents['partdata'] = $this->Prereport_Model->datewise_participants($date);
            $lp = $this->Prereport_Model->lpstudents();
            $up = $this->Prereport_Model->upstudents();
            $hs = $this->Prereport_Model->hsstudents();
            $hss = $this->Prereport_Model->hssstudents();
            $school = $this->Prereport_Model->school_lpdetails();
            $this->Contents['lp'] = $lp;
            $this->Contents['up'] = $up;
            $this->Contents['hs'] = $hs;
            $this->Contents['hss'] = $hss;
            $this->Contents['school'] = $school;
            $this->Contents['date'] = "";

            $content = $this->load->view('district/report/prereportpdf/datewisepart', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('datewise_participant.pdf', 'I');
            exit;
        }
    }

    public function stagereportdate()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $stageid = $this->input->post('cmbstage');
        $date = $this->input->post('txtDate');
        if ($date == 'ALL') {

            $this->Contents['date'] = "All Date";
            $this->Contents['stageid'] = $stageid;
            $this->Contents['stagedata'] = $this->Prereport_Model->alldate_stagereport($stageid);
            $this->Contents['stagename'] = $this->Prereport_Model->Stagename($stageid);
            $content = $this->load->view('district/report/prereportpdf/allstage', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Stagereport_date.pdf', 'I');
            exit;
        } else {
            $this->Contents['date'] = $date;
            $this->Contents['stageid'] = $stageid;
            $this->Contents['stagedata'] = $this->Prereport_Model->datewise_stagealtreport($date, $stageid);
            $this->Contents['stagename'] = $this->Prereport_Model->Stagename($stageid);
            $content = $this->load->view('district/report/prereportpdf/stagereportdate', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Stagereport_date.pdf', 'I');
            exit;
        }
    }

    public function stagereport_all()
    {
        $this->Contents = array();
        $this->template->write('title', '');
        $stageid = $this->input->post('cmbFestType');
        $retdata = $this->Prereport_Model->stagereport_all();
        $this->Contents['retdata'] = $retdata;
        $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();
        $content = $this->load->view('district/report/prereportpdf/stagereport_all', $this->Contents, true);
        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('stagereport_all.pdf', 'I');
        exit;
    }

    public function rpt_itemwiseparticipants()
    {
        $this->Contents = array();
        $this->template->write('title', '');
        $itemcode = trim((string)($this->input->post('cbo_item') ?: $this->input->get('cbo_item')));
        $festival = trim((string)($this->input->post('cmbFestType') ?: $this->input->get('cmbFestType')));

        if ($itemcode === '') {
            echo "<script>alert('Please select an item.'); window.close();</script>";
            exit;
        }

        if ($itemcode == 'ALL') {

            $itempart = $this->Prereport_Model->itemwise_allfestival($festival);
            if (empty($itempart)) {
                echo "<script>alert('No participants found for the selected festival.'); window.close();</script>";
                exit;
            }
            $this->Contents['itempart'] = $itempart;
            $content = $this->load->view('district/report/prereportpdf/rptallitemwise', $this->Contents, true);
        } else {

            $this->Contents['partdata'] = $this->Prereport_Model->itemwise_participants($itemcode);
            if (empty($this->Contents['partdata'])) {
                echo "<script>alert('No participants found for item code: " . htmlspecialchars($itemcode) . "'); window.close();</script>";
                exit;
            }
            $this->Contents['itemdet'] = $this->Prereport_Model->Eventname($itemcode);
            $this->Contents['festdet'] = $this->Prereport_Model->Festname($festival);
            $content = $this->load->view('district/report/prereportpdf/rpt_itemwiseparticipants', $this->Contents, true);
        }

        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('itemwiseparticipants.pdf', 'I');
        exit;
    }

    public function stageallot_duration()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $festcode = $this->input->post('cmbFestType');
        $sub_district = $this->input->post('cmbSubDistrict') ?? 'ALL';
        if ($festcode != 'ALL') {

            $allot = $this->Prereport_Model->stageallot_duration($festcode, $sub_district);
            $this->Contents['allot'] = $allot;
            $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();
            $this->Contents['groups'] = $this->Prereport_Model->groupallotduration($sub_district);
            if (count($allot) > 0) {
                $content = $this->load->view('district/report/prereportpdf/stageallot_duration', $this->Contents, true);

                $html2pdf = new \HTML2PDF('P', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('Stageallot_duration.pdf', 'I');
            } else {
                redirect('test/nodata');
            }
        } else {
            $allot = $this->Prereport_Model->stageallot_duration_all($sub_district);
            $this->Contents['allot'] = $allot;
            $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();
            $this->Contents['groups'] = $this->Prereport_Model->groupallotduration($sub_district);
            if (count($allot) > 0) {
                $content = $this->load->view('district/report/prereportpdf/stageallot_all', $this->Contents, true);

                $html2pdf = new \HTML2PDF('P', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('Stageallot_duration.pdf', 'I');
            } else {
                redirect('test/nodata');
            }
        }
    }

    public function clashes_details()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $festival = $this->input->get_post('cmbFestType');
        $date = $this->input->get_post('txtDate');

        if (empty($festival) || empty($date)) {
            return redirect()->to(base_url('district/report/prereport/clashes_details_interface'));
        }

        $this->Contents['retdata'] = $this->Prereport_Model->clash_info1($festival, $date);
        $this->Contents['date'] = $date;

        try {
            if ($festival != 'all') {
                $content = $this->load->view('district/report/prereportpdf/rpt_clashes_report', $this->Contents, true);
            } else {
                $content = $this->load->view('district/report/prereport/rpt_clashes_report_all', $this->Contents, true);
            }

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Clashes_details.pdf', 'I');
            exit;
        } catch (\Throwable $e) {
            log_message('error', 'HTML2PDF Clashes Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error generating clash report: ' . $e->getMessage());
            return redirect()->to(base_url('district/report/prereport/clashes_details_interface'));
        }
    }

    public function appealed_part()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $festcode = $this->input->post('cmbFestType');
        if (!empty($festcode)) {
            $appeal = $this->Prereport_Model->appealed_part($festcode);
            $this->Contents['appeal'] = $appeal;
            $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();

            $content = $this->load->view('district/report/prereportpdf/appealed_part', $this->Contents, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Appealed_participant.pdf', 'I');
            exit;
        } else {
            echo "<script>alert('Please select a festival category.'); window.close();</script>";
            exit;
        }
    }

    public function appeal_courtorder()
    {
        $this->Contents = array();
        $this->template->write('title', '');
        $festcode = $this->input->post('cmbFestType');
        if (!empty($festcode)) {
            $appeal = $this->Prereport_Model->appeal_courtorder($festcode);
            $this->Contents['appeal'] = $appeal;
            $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();

            $content = $this->load->view('district/report/prereportpdf/appeal_courtorder', $this->Contents, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Appeal_courtorder.pdf', 'I');
            exit;
        } else {
            echo "<script>alert('Please select a festival category.'); window.close();</script>";
            exit;
        }
    }

    public function no_value()
    {
        $this->load->view('district/report/prereportpdf/no_value');
    }

    public function stagereport_abstract()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $abstract = $this->Prereport_Model->stageallot_abstract();

        $this->Contents['abstract'] = $abstract;
        $content = $this->load->view('district/report/prereportpdf/stageallot_abstract', $this->Contents, true);
        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('StageallotAbstract.pdf', 'I');
        exit;
    }

    public function four_reports()
    {
        $item_code = trim((string)$this->input->post('item'));
        if ($item_code === '') {
            echo "<script>alert('Please enter an Item Code.'); window.close();</script>";
            exit;
        }

        $cmbFestType = trim((string)$this->input->post('cmbFestType'));
        $isAllItems = ($item_code === 'ALL');

        if ($isAllItems) {
            @set_time_limit(600);
            @ini_set('max_execution_time', '600');
            @ini_set('memory_limit', '2048M');
        }

        $fest = $this->Prereport_Model->get_festId_by_itemcode($item_code);
        $festId = (!empty($fest) && isset($fest[0]['fest_id'])) ? $fest[0]['fest_id'] : 0;

        $report_type = $this->input->post('report_type');
        $call = $this->input->post('call') ?: ($report_type == 'call' ? 'Call Sheet' : '');
        $time = $this->input->post('time') ?: ($report_type == 'time' ? 'Time Sheet' : '');
        $tabulation = $this->input->post('tabulation') ?: $this->input->post('tab') ?: ($report_type == 'tabulation' ? 'Tabulation Sheet' : '');
        $score = $this->input->post('score') ?: ($report_type == 'score' ? 'Score Sheet' : '');
        $participants = $this->input->post('participants') ?: ($report_type == 'participants' ? 'Participant List' : '');

        if ($call == 'Call Sheet') {
            if ($isAllItems) {
                $festForAll = ($cmbFestType !== '' && $cmbFestType !== 'ALL' && $cmbFestType !== '0') ? $cmbFestType : 'ALL';
                $this->Content = array();
                $this->Content['fees_details'] = $this->Prereport_Model->all_callsheet_details($festForAll);
                $this->Content['subdistrict'] = $this->Prereport_Model->find_subdistrict();
                if (!empty($this->Content['fees_details'])) {
                    $content = $this->load->view('district/report/prereportpdf/callsheet_all', $this->Content, true);

                    $html2pdf = new \HTML2PDF('P', 'A4', 'en');
                    $html2pdf->pdf->SetDisplayMode('fullpage');
                    $html2pdf->WriteHTML($content, '');
                    $html2pdf->Output('Callsheet_All.pdf', 'I');
                    exit;
                }
            } else {
                $fees_details = $this->Prereport_Model->get_callsheet_details($item_code);
                $this->Content = array();
                $this->Content['fees_details'] = $fees_details;
                $this->Content['subdistrict'] = $this->Prereport_Model->find_subdistrict();
                if (!empty($fees_details)) {
                    $content = $this->load->view('district/report/prereportpdf/callsheet_report', $this->Content, true);

                    $html2pdf = new \HTML2PDF('P', 'A4', 'en');
                    $html2pdf->pdf->SetDisplayMode('fullpage');
                    $html2pdf->WriteHTML($content, '');
                    $html2pdf->Output('Callsheet_' . $item_code . '.pdf', 'I');
                    exit;
                }
            }
        } elseif ($time == 'Time Sheet') {
            if ($isAllItems) {
                $festForAll = ($cmbFestType !== '' && $cmbFestType !== 'ALL' && $cmbFestType !== '0') ? $cmbFestType : 'ALL';
                $items = $this->Prereport_Model->get_items_for_all_reports($festForAll);
                $sessDist = (int)$this->session->get('DISTRICT');
                $pages = [];
                foreach ($items as $it) {
                    $itemtime = $this->Prereport_Model->timesheet($it['item_code']);
                    if (empty($itemtime)) continue;
                    $b = $this->db->table('d_participant_item_details')->where('item_code', $it['item_code']);
                    if ($sessDist > 0) $b->where('district_code', $sessDist);
                    $pages[] = [
                        'itemtime'   => $itemtime,
                        'item_count' => $b->countAllResults(),
                        'festname'   => $this->Prereport_Model->Festname($it['fest_id']),
                    ];
                }
                if (!empty($pages)) {
                    $this->render_all_report('district/report/prereportpdf/timesheet', $pages, 'Timesheet_All.pdf');
                }
            } else {
                $this->Contents = array();
                $this->Contents['itemtime'] = $this->Prereport_Model->timesheet($item_code);
                $sessDist = (int)$this->session->get('DISTRICT');
                $b = $this->db->table('d_participant_item_details')->where('item_code', $item_code);
                if ($sessDist > 0) $b->where('district_code', $sessDist);
                $this->Contents['item_count'] = $b->countAllResults();
                $this->Contents['festname'] = $this->Prereport_Model->Festname($festId);
                if (!empty($this->Contents['itemtime'])) {
                    $content = $this->load->view('district/report/prereportpdf/timesheet', $this->Contents, true);
                    $html2pdf = new \HTML2PDF('P', 'A4', 'en');
                    $html2pdf->pdf->SetDisplayMode('fullpage');
                    $html2pdf->WriteHTML($content, '');
                    $html2pdf->Output('Timesheet_' . $item_code . '.pdf', 'I');
                    exit;
                }
            }
        } elseif ($tabulation == 'Tabulation Sheet') {
            if ($isAllItems) {
                $festForAll = ($cmbFestType !== '' && $cmbFestType !== 'ALL' && $cmbFestType !== '0') ? $cmbFestType : 'ALL';
                $items = $this->Prereport_Model->get_items_for_all_reports($festForAll);
                $pages = [];
                foreach ($items as $it) {
                    $retdata = $this->Prereport_Model->tabulation_details($it['item_code']);
                    if (empty($retdata)) continue;
                    $pages[] = [
                        'retdata'  => $retdata,
                        'retdata1' => $this->Prereport_Model->tabulation_fest_details($it['fest_id']),
                    ];
                }
                if (!empty($pages)) {
                    $this->render_all_report('district/report/prereport/rpt_tabulation_sheet_item', $pages, 'Tabulation_Sheet_All.pdf');
                }
            } else {
                $this->Contents = array();
                $retdata = $this->Prereport_Model->tabulation_details($item_code);
                $retdata1 = $this->Prereport_Model->tabulation_fest_details($festId);
                $this->Contents['retdata'] = $retdata;
                $this->Contents['retdata1'] = $retdata1;
                if (!empty($retdata)) {
                    $content = $this->load->view('district/report/prereport/rpt_tabulation_sheet_item', $this->Contents, true);
                    $html2pdf = new \HTML2PDF('P', 'A4', 'en');
                    $html2pdf->pdf->SetDisplayMode('fullpage');
                    $html2pdf->WriteHTML($content, '');
                    $html2pdf->Output('Tabulation_sheet_' . $item_code . '.pdf', 'I');
                    exit;
                }
            }
        } elseif ($score == 'Score Sheet') {
            if ($isAllItems) {
                $festForAll = ($cmbFestType !== '' && $cmbFestType !== 'ALL' && $cmbFestType !== '0') ? $cmbFestType : 'ALL';
                $items = $this->Prereport_Model->get_items_for_all_reports($festForAll);
                $pages = [];
                foreach ($items as $it) {
                    $date = $this->Prereport_Model->fetch_item_name2($it['item_code']);
                    if (empty($date)) continue;
                    $pages[] = [
                        'itemcode'    => $it['item_code'],
                        'start_time'  => $date,
                    ];
                }
                if (!empty($pages)) {
                    $this->render_all_report('district/report/prereportpdf/rpt_scoresheet', $pages, 'Scoresheet_All.pdf');
                }
            } else {
                $date = $this->Prereport_Model->fetch_item_name2($item_code);
                if (!empty($date)) {
                    $this->Contents = array();
                    $this->Contents['itemcode'] = $item_code;
                    $this->Contents['start_time'] = $date;

                    $content = $this->load->view('district/report/prereportpdf/rpt_scoresheet', $this->Contents, true);
                    $html2pdf = new \HTML2PDF('P', 'A4', 'en');
                    $html2pdf->pdf->SetDisplayMode('fullpage');
                    $html2pdf->WriteHTML($content, '');
                    $html2pdf->Output('scoresheet_' . $item_code . '.pdf', 'I');
                    exit;
                }
            }
        } elseif ($participants == 'Participant List') {
            if ($isAllItems) {
                $festForAll = ($cmbFestType !== '' && $cmbFestType !== 'ALL' && $cmbFestType !== '0') ? $cmbFestType : 'ALL';
                $items = $this->Prereport_Model->get_items_for_all_reports($festForAll);
                $pages = [];
                foreach ($items as $it) {
                    $partdata = $this->Prereport_Model->itemwise_participants($it['item_code']);
                    if (empty($partdata)) continue;
                    $pages[] = [
                        'partdata' => $partdata,
                        'itemdet'  => $this->Prereport_Model->Eventname($it['item_code']),
                        'festdet'  => $this->Prereport_Model->Festname($it['fest_id']),
                    ];
                }
                if (!empty($pages)) {
                    $this->render_all_report('district/report/prereportpdf/rpt_itemwiseparticipants', $pages, 'Participants_All.pdf');
                }
            } else {
                $this->Contents = array();
                $this->Contents['partdata'] = $this->Prereport_Model->itemwise_participants($item_code);
                $this->Contents['itemdet'] = $this->Prereport_Model->Eventname($item_code);
                $this->Contents['festdet'] = $this->Prereport_Model->Festname($festId);
                if (!empty($this->Contents['partdata'])) {
                    $content = $this->load->view('district/report/prereportpdf/rpt_itemwiseparticipants', $this->Contents, true);
                    $html2pdf = new \HTML2PDF('P', 'A4', 'en');
                    $html2pdf->pdf->SetDisplayMode('fullpage');
                    $html2pdf->WriteHTML($content, '');
                    $html2pdf->Output('itemwiseparticipants_' . $item_code . '.pdf', 'I');
                    exit;
                }
            }
        }

        echo "<script>alert('No data found for Item Code: " . htmlspecialchars($item_code) . "'); window.close();</script>";
        exit;
    }

    /**
     * Render multiple per-item HTML pages into a single multi-page PDF.
     * Strips leading <style> from pages after the first (only one <style> block needed).
     */
    private function render_all_report(string $view, array $pages, string $filename): void
    {
        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        foreach ($pages as $idx => $data) {
            $chunk = $this->load->view($view, $data, true);
            if ($idx > 0) {
                $chunk = preg_replace('/<style[^>]*>.*?<\/style>\s*/is', '', $chunk, 1);
            }
            $chunk = preg_replace('/<\/page>\s*<p>[\s\S]*$/i', '</page>', $chunk);
            $html2pdf->WriteHTML($chunk, '');
            unset($chunk, $data);
        }
        $html2pdf->Output($filename, 'I');
        exit;
    }
}
