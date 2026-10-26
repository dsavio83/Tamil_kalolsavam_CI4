<?php

namespace App\Controllers;

class Test extends BaseController
{
    public function nodata()
    {
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

    public function index()
    {
        return $this->nodata();
    }
}
