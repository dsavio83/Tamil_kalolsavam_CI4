<?php

namespace App\Controllers;

class Test extends \App\Controllers\BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return $this->nodata();
    }

    public function nodata()
    {
        if (!class_exists('FPDF', false)) {
            require_once APPPATH . 'Libraries/fpdf.php';
        }
        $fpdf = new \FPDF('P', 'mm', 'A4');
        $fpdf->Open();
        $fpdf->AddPage();
        $fpdf->SetFont('Arial', 'B', 18);
        $fpdf->SetY(50);
        $fpdf->Cell(0, 20, 'No Data Found!', 0, 1, 'C');
        $fpdf->Output('nodata.pdf', 'I');
        exit;
    }
}
