<?php

namespace App\Controllers;


class Test  extends \App\Controllers\BaseController {
    
    function __construct () {
		parent::__construct ();
    }

    function index() {
        $orientation = strtoupper(trim((string)($this->request->getGet('orientation') ?: ($_GET['orientation'] ?? ''))));
        if ($orientation !== 'P' && $orientation !== 'L') {
            $sub_dist_code = $this->session->get('SUB_DISTRICT');
            $dist_code = $this->session->get('DISTRICT');
            if ($sub_dist_code && $dist_code) {
                $this->load->model('certificate/Certificate_Model');
                $tpl = $this->Certificate_Model->get_certificate_details($dist_code, $sub_dist_code);
                $orientation = (!empty($tpl[0]['page_style']) && $tpl[0]['page_style'] === 'P') ? 'P' : 'L';
            } else {
                $orientation = 'L';
            }
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        http_response_code(200);
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="test_grid_' . strtolower($orientation) . '.pdf"');

        $this->load->library('_fpdf/fpdf');
        $this->fpdf->Open();
        $this->fpdf->AddPage($orientation, 'A4');
        $this->fpdf->SetAutoPageBreak(false);

        if ($orientation === 'L') {
            // A4 Landscape: 297mm width x 210mm height
            $maxX = 290;
            $maxY = 200;
            $step = 10;

            $this->fpdf->SetDrawColor(160, 160, 160);
            $this->fpdf->Rect(5, 5, $maxX - 5, $maxY - 5);

            $this->fpdf->SetFont('Arial', 'B', 8);
            $this->fpdf->SetTextColor(100, 100, 100);
            $this->fpdf->Text(8, 4, "A4 LANDSCAPE COORDINATE GRID (297 x 210 mm) - Calibration Units in mm");

            for ($x = 10; $x < $maxX; $x += $step) {
                if ($x % 50 === 0) {
                    $this->fpdf->SetDrawColor(120, 120, 120);
                    $this->fpdf->SetLineWidth(0.3);
                } else {
                    $this->fpdf->SetDrawColor(215, 215, 215);
                    $this->fpdf->SetLineWidth(0.1);
                }
                $this->fpdf->Line($x, 5, $x, $maxY);
            }

            for ($y = 10; $y < $maxY; $y += $step) {
                if ($y % 50 === 0) {
                    $this->fpdf->SetDrawColor(120, 120, 120);
                    $this->fpdf->SetLineWidth(0.3);
                } else {
                    $this->fpdf->SetDrawColor(215, 215, 215);
                    $this->fpdf->SetLineWidth(0.1);
                }
                $this->fpdf->Line(5, $y, $maxX, $y);
            }

            $this->fpdf->SetTextColor(50, 50, 50);
            $this->fpdf->SetFont('Arial', '', 4.5);
            for ($x = 10; $x < $maxX; $x += 20) {
                for ($y = 10; $y < $maxY; $y += 10) {
                    $this->fpdf->SetXY($x + 0.5, $y + 0.5);
                    $this->fpdf->Write(0, "$x,$y");
                }
            }
        } else {
            // A4 Portrait: 210mm width x 297mm height
            $maxX = 200;
            $maxY = 285;
            $step = 10;

            $this->fpdf->SetDrawColor(160, 160, 160);
            $this->fpdf->Rect(5, 5, $maxX - 5, $maxY - 5);

            $this->fpdf->SetFont('Arial', 'B', 8);
            $this->fpdf->SetTextColor(100, 100, 100);
            $this->fpdf->Text(8, 4, "A4 PORTRAIT COORDINATE GRID (210 x 297 mm) - Calibration Units in mm");

            for ($x = 10; $x < $maxX; $x += $step) {
                if ($x % 50 === 0) {
                    $this->fpdf->SetDrawColor(120, 120, 120);
                    $this->fpdf->SetLineWidth(0.3);
                } else {
                    $this->fpdf->SetDrawColor(215, 215, 215);
                    $this->fpdf->SetLineWidth(0.1);
                }
                $this->fpdf->Line($x, 5, $x, $maxY);
            }

            for ($y = 10; $y < $maxY; $y += $step) {
                if ($y % 50 === 0) {
                    $this->fpdf->SetDrawColor(120, 120, 120);
                    $this->fpdf->SetLineWidth(0.3);
                } else {
                    $this->fpdf->SetDrawColor(215, 215, 215);
                    $this->fpdf->SetLineWidth(0.1);
                }
                $this->fpdf->Line(5, $y, $maxX, $y);
            }

            $this->fpdf->SetTextColor(50, 50, 50);
            $this->fpdf->SetFont('Arial', '', 4.5);
            for ($x = 10; $x < $maxX; $x += 20) {
                for ($y = 10; $y < $maxY; $y += 10) {
                    $this->fpdf->SetXY($x + 0.5, $y + 0.5);
                    $this->fpdf->Write(0, "$x,$y");
                }
            }
        }

        $this->fpdf->Output('test_grid_' . strtolower($orientation) . '.pdf', 'I');
        exit;
    }

    function nodata() {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="No_Data.pdf"');

        $this->load->library('_fpdf/fpdf');

        $this->fpdf->Open();

        $this->fpdf->AddPage();
        $this->fpdf->SetFont('Arial', 'B', 16);
        $this->fpdf->SetY(40);
        $this->fpdf->Cell(190, 15, 'No Data Found!', 0, 1, 'C');
        $this->fpdf->Output('No_Data.pdf', 'I');
        exit;
    }

    function backup() {
        // Load the DB utility class
        $this->load->dbutil();


        $prefs = array(
            //'tables'      => array('school_master'),  // Array of tables to backup.
            'ignore' => array(), // List of tables to omit from the backup
            'format' => 'zip', // gzip, zip, txt
            'filename' => 'mybackup.sql', // File name - NEEDED ONLY WITH ZIP FILES
            'add_drop' => TRUE, // Whether to add DROP TABLE statements to backup file
            'add_insert' => TRUE, // Whether to add INSERT data to backup file
            'newline' => "\n"               // Newline character used in backup file
        );

        $backup = $this->dbutil->backup($prefs);

        // Backup your entire database and assign it to a variable
        //& $this->dbutil->backup();
        // Load the file helper and write the file to your server
        $this->load->helper('file');
        write_file('/doc/mybackup.zip', $backup);

        // Load the download helper and send the file to your desktop
        $this->load->helper('download');
        force_download('mybackup.zip', $backup);
    }

    function export_data() {
        $this->load->helper('csv/csv');

        $sub_district_code = $this->session->get('SUB_DISTRICT');
        $this->db->where('sub_district_code', $sub_district_code);
        $school_master = $this->db->get('school_master');
        //$this->Contents['school_master']			=	$school_master;

        $this->db->where('SM.sub_district_code', $sub_district_code);
        $this->db->join('school_master AS SM', 'SD.school_code = SM.school_code');
        $school_details = $this->db->get('school_details AS SD');
        //$this->Contents['school_details']			=	$school_details;



        $this->db->where('sub_district_code', $sub_district_code);
        $participant_details = $this->db->get('participant_details');
        //$this->Contents['participant_details']			=	$participant_details;


        $this->db->where('PM.sub_district_code', $sub_district_code);
        $this->db->join('participant_details AS PM', 'PM.participant_id = PD.participant_id');
        $this->db->select('PD.*');
        $participant_item_details = $this->db->get('participant_item_details AS PD');
        //$this->Contents['participant_item_details']		=		$participant_item_details;

        $export_array = array();
        $export_array = array_merge($export_array, $school_master->getResultArray(), $school_details->getResultArray(), $participant_details->getResultArray(), $participant_item_details->getResultArray());
        array_to_csv($export_array, 'eee.csv');

        /* header("Content-type: application/ms-excel");
          header("Content-Disposition: attachment; filename=cluster_report.xls");
          $this->load->view('test/test',$this->Contents); */
    }

    function excel_reader() { //echo $this->config->item('base_path').'uploads/cluster_report.xls';
        // Load the spreadsheet reader library
        $this->load->library('csvreader');

        // Read the spreadsheet via a relative path to the document
        // for example $this->excel_reader->read('./uploads/file.xls');
        $csvData = $this->csvreader->parse_file($this->config->item('base_path') . 'uploads/eee.csv');

        // Get the contents of the first worksheet
        //$worksheet = $this->excel_reader->worksheets[0]; 
        print('<pre>');
        print_r($csvData);
        print('</pre>');
    }

}

?>
