<?php

namespace App\Libraries;

require_once APPPATH . 'Libraries/_fpdf/fpdf.php';

if (!defined('FPDF_FONTPATH')) {
    define('FPDF_FONTPATH', APPPATH . 'Libraries/_fpdf/font/');
}

class Cover_Pdf extends \FPDF
{
    protected $angle = 0;
    protected $paperSizes = [
        'A4'     => [210.0, 297.0],
        'A5'     => [148.0, 210.0],
        'A3'     => [297.0, 420.0],
        'Letter' => [215.9, 279.4],
        'Legal'  => [215.9, 355.6],
        'DL'     => [220.0, 110.0],
        'C5'     => [229.0, 162.0],
        'C4'     => [324.0, 229.0],
    ];

    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
        $this->SetAutoPageBreak(false);
        $this->SetMargins(0, 0, 0);

        // Register Tamil and custom fonts
        $this->registerFonts();
    }

    protected function registerFonts()
    {
        $fontDir = APPPATH . 'Libraries/_fpdf/font/';

        // TAU-Paalai & TAU-Pallai
        if (file_exists($fontDir . 'taupaalai.php')) {
            $this->AddFont('TAU-Paalai', '', 'taupaalai.php');
            $this->AddFont('TAU-Paalai', 'B', 'taupaalaib.php');
        }
        if (file_exists($fontDir . 'taupallai.php')) {
            $this->AddFont('TAU-Pallai', '', 'taupallai.php');
            $this->AddFont('TAU-Pallai', 'B', 'taupallaib.php');
        }

        // Map TAU-Maduaram & TAU-Marutham
        $this->AddFont('TAU-Maduaram', '', 'taupaalai.php');
        $this->AddFont('TAU-Maduaram', 'B', 'taupaalaib.php');

        $this->AddFont('TAU-Marutham', '', 'taupaalai.php');
        $this->AddFont('TAU-Marutham', 'B', 'taupaalaib.php');
    }

    public function SetFont($family, $style = '', $size = 0)
    {
        $f = strtolower(trim($family, "'\" "));
        if ($f === 'times new roman' || $f === 'times') {
            $family = 'times';
        } elseif ($f === 'arial' || $f === 'helvetica') {
            $family = 'helvetica';
        }
        parent::SetFont($family, $style, $size);
    }

    /**
     * Resolve dimensions in mm from paper size name or custom dimensions
     */
    public function resolvePageDimensions($paperSize, $customW = 0, $customH = 0, $orientation = 'portrait')
    {
        $sizeKey = strtoupper(trim((string)$paperSize));
        $w = 210.0;
        $h = 297.0;

        if (isset($this->paperSizes[$sizeKey])) {
            $w = $this->paperSizes[$sizeKey][0];
            $h = $this->paperSizes[$sizeKey][1];
        } elseif ($customW > 0 && $customH > 0) {
            $w = (float)$customW;
            $h = (float)$customH;
        }

        $isLandscape = (strtolower(trim((string)$orientation)) === 'landscape');
        if ($isLandscape && $w < $h) {
            // Swap if landscape
            $temp = $w;
            $w = $h;
            $h = $temp;
        } elseif (!$isLandscape && $w > $h) {
            // Swap if portrait
            $temp = $w;
            $w = $h;
            $h = $temp;
        }

        return [$w, $h];
    }

    /**
     * Rotate canvas about ($x, $y) in degrees
     */
    public function Rotate($angle, $x = -1, $y = -1)
    {
        if ($x == -1) {
            $x = $this->x;
        }
        if ($y == -1) {
            $y = $this->y;
        }
        if ($this->angle != 0) {
            $this->_out('Q');
        }
        $this->angle = $angle;
        if ($angle != 0) {
            $angleRad = $angle * M_PI / 180;
            $c = cos($angleRad);
            $s = sin($angleRad);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
        }
    }

    protected $pageRotations = [];

    /**
     * Set page rotation in degrees (0, 90, 180, 270)
     */
    public function SetPageRotation($angle, $page = null)
    {
        if ($page === null) {
            $page = $this->page;
        }
        $this->pageRotations[$page] = ((int)$angle % 360 + 360) % 360;
    }

    public function _putpages()
    {
        $nb = $this->page;
        if (!empty($this->aliasNbPages)) {
            for ($n = 1; $n <= $nb; $n++) {
                $this->pages[$n] = str_replace($this->aliasNbPages, $nb, $this->pages[$n]);
            }
        }
        if ($this->DefOrientation == 'P') {
            $wPt = $this->DefPageFormat[0] * $this->k;
            $hPt = $this->DefPageFormat[1] * $this->k;
        } else {
            $wPt = $this->DefPageFormat[1] * $this->k;
            $hPt = $this->DefPageFormat[0] * $this->k;
        }
        $filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
        for ($n = 1; $n <= $nb; $n++) {
            $this->_newobj();
            $this->_out('<</Type /Page');
            $this->_out('/Parent 1 0 R');
            if (isset($this->PageSizes[$n])) {
                $this->_out(sprintf('/MediaBox [0 0 %.2F %.2F]', $this->PageSizes[$n][0], $this->PageSizes[$n][1]));
            }
            if (!empty($this->pageRotations[$n])) {
                $this->_out('/Rotate ' . $this->pageRotations[$n]);
            }
            $this->_out('/Resources 2 0 R');
            if (isset($this->PageLinks[$n])) {
                $annots = '/Annots [';
                foreach ($this->PageLinks[$n] as $pl) {
                    $rect = sprintf('%.2F %.2F %.2F %.2F', $pl[0], $pl[1], $pl[0] + $pl[2], $pl[1] - $pl[3]);
                    $annots .= '<</Type /Annot /Subtype /Link /Rect [' . $rect . '] /Border [0 0 0] ';
                    if (is_string($pl[4])) {
                        $annots .= '/A <</S /URI /URI ' . $this->_textstring($pl[4]) . '>>>>';
                    } else {
                        $l = $this->links[$pl[4]];
                        $h = isset($this->PageSizes[$l[0]]) ? $this->PageSizes[$l[0]][1] : $hPt;
                        $annots .= sprintf('/Dest [%d 0 R /XYZ 0 %.2F null]>>', 1 + 2 * $l[0], $h - $l[1] * $this->k);
                    }
                }
                $this->_out($annots . ']');
            }
            if ($this->PDFVersion > '1.3') {
                $this->_out('/Group <</Type /Group /S /Transparency /CS /DeviceRGB>>');
            }
            $this->_out('/Contents ' . ($this->n + 1) . ' 0 R>>');
            $this->_out('endobj');
            $p = ($this->compress) ? gzcompress($this->pages[$n]) : $this->pages[$n];
            $this->_newobj();
            $this->_out('<<' . $filter . '/Length ' . strlen($p) . '>>');
            $this->_putstream($p);
            $this->_out('endobj');
        }
        $this->offsets[1] = strlen($this->buffer);
        $this->_out('1 0 obj');
        $this->_out('<</Type /Pages');
        $kids = '/Kids [';
        for ($i = 0; $i < $nb; $i++) {
            $kids .= (3 + 2 * $i) . ' 0 R ';
        }
        $this->_out($kids . ']');
        $this->_out('/Count ' . $nb);
        $this->_out(sprintf('/MediaBox [0 0 %.2F %.2F]', $wPt, $hPt));
        $this->_out('>>');
        $this->_out('endobj');
    }

    public function _endpage()
    {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }

    /**
     * Helper to parse hex color to RGB array
     */
    protected function parseColor($hex)
    {
        $hex = ltrim((string)$hex, '#');
        if (strlen($hex) === 3) {
            return [
                hexdec(str_repeat(substr($hex, 0, 1), 2)),
                hexdec(str_repeat(substr($hex, 1, 1), 2)),
                hexdec(str_repeat(substr($hex, 2, 1), 2)),
            ];
        }
        if (strlen($hex) === 6) {
            return [
                hexdec(substr($hex, 0, 2)),
                hexdec(substr($hex, 2, 2)),
                hexdec(substr($hex, 4, 2)),
            ];
        }
        return [0, 0, 0];
    }

    /**
     * Render a single item cover page using template configuration and data
     */
    public function renderCoverPage($template, $elements, $itemData, $pageRotation = 0)
    {
        $w = (float)($template['width_mm'] ?? 210);
        $h = (float)($template['height_mm'] ?? 297);
        $isLandscape = (strtolower($template['orientation'] ?? 'portrait') === 'landscape') || ($w > $h);
        $orientation = $isLandscape ? 'L' : 'P';

        // In FPDF, when orientation is 'L', AddPage expects format [height, width] (short side, long side)
        // so that FPDF internal $this->w becomes $w and $this->h becomes $h
        if ($isLandscape) {
            $format = [min($w, $h), max($w, $h)];
            $this->AddPage('L', $format);
        } else {
            $format = [min($w, $h), max($w, $h)];
            $this->AddPage('P', $format);
        }

        if ($pageRotation != 0) {
            $this->SetPageRotation($pageRotation);
        }

        // Background
        $bgType = $template['background_type'] ?? 'none';
        $bgVal = $template['background_value'] ?? '';

        if ($bgType === 'color' && !empty($bgVal)) {
            $rgb = $this->parseColor($bgVal);
            $this->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
            $this->Rect(0, 0, $w, $h, 'F');
        } elseif ($bgType === 'image' && !empty($bgVal)) {
            $bgPath = FCPATH . ltrim($bgVal, '/\\');
            if (file_exists($bgPath)) {
                $this->Image($bgPath, 0, 0, $w, $h);
            }
        }

        // Sort elements by z_index ascending
        usort($elements, function ($a, $b) {
            return ((int)($a['z_index'] ?? 1)) <=> ((int)($b['z_index'] ?? 1));
        });

        // Render each element
        foreach ($elements as $el) {
            $this->renderElement($el, $itemData);
        }
    }

    /**
     * Render individual element
     */
    protected function renderElement($el, $itemData)
    {
        $elType = $el['element_type'] ?? 'dynamic_field';
        $x = (float)($el['x'] ?? 0);
        $y = (float)($el['y'] ?? 0);
        $w = (float)($el['width'] ?? 50);
        $h = (float)($el['height'] ?? 10);
        $rotation = (float)($el['rotation'] ?? 0);

        if ($elType === 'image') {
            $imgPath = $el['static_text'] ?? '';
            $fullPath = FCPATH . ltrim($imgPath, '/\\');
            if (file_exists($fullPath) && is_file($fullPath)) {
                if ($rotation != 0) {
                    $this->Rotate($rotation, $x + ($w / 2), $y + ($h / 2));
                }
                $this->Image($fullPath, $x, $y, $w, $h);
                if ($rotation != 0) {
                    $this->Rotate(0);
                }
            }
            return;
        }

        // Prepare text content
        $text = '';
        if ($elType === 'static_text') {
            $text = (string)($el['static_text'] ?? '');
        } else {
            // Dynamic field
            $fieldName = $el['field_name'] ?? '';
            $templateStr = $el['static_text'] ?? '';

            if (empty($templateStr) || $templateStr === '{{' . $fieldName . '}}') {
                $text = (string)($itemData[$fieldName] ?? ($itemData['{{' . $fieldName . '}}'] ?? '-'));
            } else {
                // Multi-tag replacement in templateStr
                $text = $templateStr;
                foreach ($itemData as $k => $v) {
                    $placeholder = '{{' . ltrim(rtrim($k, '}'), '{') . '}}';
                    $text = str_replace($placeholder, (string)$v, $text);
                }
            }
        }

        if (trim($text) === '') {
            $text = '-';
        }

        // Font family resolution
        $font = trim((string)($el['font_family'] ?? 'TAU-Paalai'));
        $validFonts = ['TAU-Paalai', 'TAU-Pallai', 'TAU-Maduaram', 'TAU-Marutham', 'Arial', 'Times New Roman'];
        if (!in_array($font, $validFonts, true)) {
            $font = 'TAU-Paalai';
        }

        // Font style: B, I, U
        $style = '';
        if (strtolower($el['font_weight'] ?? '') === 'bold') {
            $style .= 'B';
        }
        if (strtolower($el['font_style'] ?? '') === 'italic') {
            $style .= 'I';
        }
        if (strtolower($el['text_decoration'] ?? '') === 'underline') {
            $style .= 'U';
        }

        $fontSize = (int)($el['font_size'] ?? 14);
        if ($fontSize <= 0) {
            $fontSize = 14;
        }

        $this->SetFont($font, $style, $fontSize);

        // Text color
        $rgb = $this->parseColor($el['font_color'] ?? '#000000');
        $this->SetTextColor($rgb[0], $rgb[1], $rgb[2]);

        // Alignment: L, C, R
        $alignRaw = strtolower(trim((string)($el['text_align'] ?? 'center')));
        $align = 'C';
        if ($alignRaw === 'left') {
            $align = 'L';
        } elseif ($alignRaw === 'right') {
            $align = 'R';
        }

        // Apply rotation if needed
        if ($rotation != 0) {
            $this->Rotate($rotation, $x + ($w / 2), $y + ($h / 2));
        }

        // Check if element has border frame
        $hasBorder = 0;
        if (!empty($el['border']) && (int)$el['border'] > 0) {
            $hasBorder = 1;
        } elseif (!empty($el['properties_json'])) {
            $props = is_array($el['properties_json']) ? $el['properties_json'] : json_decode($el['properties_json'], true);
            if (!empty($props['border']) && (int)$props['border'] > 0) {
                $hasBorder = 1;
            }
        }

        if ($hasBorder) {
            $this->SetDrawColor(0, 0, 0);
            $this->SetLineWidth(0.4);
        }

        $this->SetXY($x, $y);

        // Check if multi-line text
        if (strpos($text, "\n") !== false) {
            $lineHeight = max(4.0, $fontSize * 0.45);
            $this->MultiCell($w, $lineHeight, $text, $hasBorder, $align);
        } else {
            $cellH = max(6.0, $h);
            $this->Cell($w, $cellH, $text, $hasBorder, 0, $align);
        }

        if ($rotation != 0) {
            $this->Rotate(0);
        }
    }
}
