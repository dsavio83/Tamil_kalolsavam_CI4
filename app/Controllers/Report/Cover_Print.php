<?php

namespace App\Controllers\Report;

use App\Controllers\BaseController;
use App\Models\Cover\Cover_Template_Model;
use App\Libraries\Cover_Pdf;

class Cover_Print extends BaseController
{
    protected $coverModel;

    public function __construct()
    {
        parent::__construct();

        // Authentication & Stage Reports permission check
        $this->Session_Model->is_user_logged(true);
        if ($this->Session_Model->check_user_permission(34) == false && 
            $this->session->get('USER_GROUP') !== 'W' && 
            (int)$this->session->get('USER_TYPE') !== 3 && 
            (int)$this->session->get('USER_TYPE') !== 2) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        $this->coverModel = new Cover_Template_Model();

        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
    }

    public function index()
    {
        return $this->templates();
    }

    /**
     * List all cover templates
     */
    public function templates()
    {
        $subDistrict = (int)$this->session->get('SUB_DISTRICT');
        $templates = $this->coverModel->get_templates($subDistrict);

        $data = [
            'title'        => 'Cover Printing Templates',
            'templates'    => $templates,
            'sub_district' => $subDistrict,
        ];

        $this->template->write_view('content', 'cover_print/templates', $data);
        $this->template->load();
    }

    /**
     * Interactive Canvas Designer Screen
     */
    public function designer($templateId = null)
    {
        $subDistrict = (int)$this->session->get('SUB_DISTRICT');
        $template = null;

        if (!empty($templateId) && (int)$templateId > 0) {
            $template = $this->coverModel->get_template($templateId);
        } else {
            $template = $this->coverModel->get_default_template($subDistrict);
        }

        // Available Dynamic Fields
        $dynamicFields = [
            ['field' => 'item_code_name', 'label' => 'Item Code & Name', 'tag' => '{{item_code}} - {{item_name}}'],
            ['field' => 'item_code',    'label' => 'Item Code',         'tag' => '{{item_code}}'],
            ['field' => 'item_name',    'label' => 'Item Name',         'tag' => '{{item_name}}'],
            ['field' => 'category',     'label' => 'Category',          'tag' => '{{category}}'],
            ['field' => 'sub_district', 'label' => 'Sub-District',      'tag' => '{{sub_district}}'],
            ['field' => 'district',     'label' => 'District',          'tag' => '{{district}}'],
            ['field' => 'fest_venue',   'label' => 'Fest Venue',        'tag' => '{{fest_venue}}'],
            ['field' => 'stage',        'label' => 'Stage',             'tag' => '{{stage}}'],
            ['field' => 'stage_desc',   'label' => 'Stage Description', 'tag' => '{{stage_desc}}'],
            ['field' => 'venue',        'label' => 'Venue',             'tag' => '{{venue}}'],
            ['field' => 'date',         'label' => 'Date',              'tag' => '{{date}}'],
            ['field' => 'time',         'label' => 'Time',              'tag' => '{{time}}'],
            ['field' => 'event_name',   'label' => 'Event Name',        'tag' => '{{event_name}}'],
            ['field' => 'event_number', 'label' => 'Event Number',      'tag' => '{{event_number}}'],
            ['field' => 'section',      'label' => 'Section',           'tag' => '{{section}}'],
        ];

        $customLabels = $this->coverModel->get_custom_labels($subDistrict);
        $sampleData = $this->coverModel->get_sample_item($subDistrict);

        $data = [
            'title'          => ($template ? 'Edit Template: ' . esc($template['template_name']) : 'New Cover Template'),
            'template'       => $template,
            'dynamicFields'  => $dynamicFields,
            'customLabels'   => $customLabels,
            'sampleData'     => $sampleData,
            'sub_district'   => $subDistrict,
        ];

        $this->template->write_view('content', 'cover_print/designer', $data);
        $this->template->load();
    }

    /**
     * Save template via AJAX POST
     */
    public function save_template()
    {
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request method']);
        }

        $json = $this->request->getJSON(true);
        if (empty($json)) {
            $raw = $this->request->getPost('data');
            $json = !empty($raw) ? json_decode($raw, true) : $this->request->getPost();
        }

        if (empty($json) || empty($json['template_name'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Template name is required']);
        }

        $width = (float)($json['width_mm'] ?? 210);
        $height = (float)($json['height_mm'] ?? 297);

        if ($width <= 0 || $height <= 0) {
            return $this->response->setJSON(['status' => false, 'message' => 'Paper width and height must be greater than zero']);
        }

        $subDistrict = (int)$this->session->get('SUB_DISTRICT');
        $district = (int)$this->session->get('DISTRICT');
        $userId = (int)$this->session->get('USERID');

        $templateData = [
            'id'                => (int)($json['id'] ?? 0),
            'template_name'     => trim($json['template_name']),
            'sub_district_code' => $subDistrict,
            'district_code'     => $district,
            'paper_size'        => trim($json['paper_size'] ?? 'A4'),
            'width_mm'          => $width,
            'height_mm'         => $height,
            'orientation'       => strtolower(trim($json['orientation'] ?? 'portrait')),
            'background_type'   => strtolower(trim($json['background_type'] ?? 'none')),
            'background_value'  => $json['background_value'] ?? null,
            'is_default'        => ($json['is_default'] ?? 'N') === 'Y' ? 'Y' : 'N',
            'created_by'        => $userId,
        ];

        $elementsData = $json['elements'] ?? [];
        if (is_string($elementsData)) {
            $elementsData = json_decode($elementsData, true) ?? [];
        }

        $savedId = $this->coverModel->save_template($templateData, $elementsData);

        if ($savedId) {
            return $this->response->setJSON([
                'status'      => true,
                'message'     => 'Cover template saved successfully!',
                'template_id' => $savedId,
            ]);
        }

        return $this->response->setJSON([
            'status'  => false,
            'message' => 'Database error while saving template',
        ]);
    }

    /**
     * Duplicate a template
     */
    public function duplicate_template($templateId)
    {
        $userId = (int)$this->session->get('USERID');
        $newId = $this->coverModel->duplicate_template((int)$templateId, $userId);

        if ($newId) {
            $this->session->setFlashdata('message', 'Template duplicated successfully.');
        } else {
            $this->session->setFlashdata('error', 'Failed to duplicate template.');
        }

        return redirect()->to(base_url('cover/templates'));
    }

    /**
     * Delete a template
     */
    public function delete_template($templateId)
    {
        $ok = $this->coverModel->delete_template((int)$templateId);
        if ($ok) {
            $this->session->setFlashdata('message', 'Template deleted successfully.');
        } else {
            $this->session->setFlashdata('error', 'Failed to delete template.');
        }

        return redirect()->to(base_url('cover/templates'));
    }

    /**
     * Set template as default
     */
    public function set_default($templateId)
    {
        $subDistrict = (int)$this->session->get('SUB_DISTRICT');
        $ok = $this->coverModel->set_default_template((int)$templateId, $subDistrict);

        if ($ok) {
            $this->session->setFlashdata('message', 'Default template updated.');
        } else {
            $this->session->setFlashdata('error', 'Failed to update default template.');
        }

        return redirect()->to(base_url('cover/templates'));
    }

    /**
     * Cover Generation Screen
     */
    public function generate($templateId = null)
    {
        $subDistrict = (int)$this->session->get('SUB_DISTRICT');
        $selectedTemplateId = (int)($templateId ?: ($this->request->getGet('template_id') ?: 0));

        $templates = $this->coverModel->get_templates($subDistrict);
        $categories = $this->coverModel->get_subdistrict_categories();
        $stages = $this->coverModel->get_subdistrict_stages($subDistrict);
        $dates = $this->coverModel->get_subdistrict_dates($subDistrict);
        $venues = $this->coverModel->get_subdistrict_venues($subDistrict);

        // Initial list of items (all categories)
        $items = $this->coverModel->get_subdistrict_items($subDistrict);

        $data = [
            'title'                => 'Generate Covers',
            'templates'            => $templates,
            'selected_template_id' => $selectedTemplateId,
            'categories'           => $categories,
            'stages'               => $stages,
            'dates'                => $dates,
            'venues'               => $venues,
            'items'                => $items,
            'sub_district'         => $subDistrict,
        ];

        $this->template->write_view('content', 'cover_print/generate', $data);
        $this->template->load();
    }

    /**
     * AJAX filter items based on Category, Date, Stage, Venue
     */
    public function ajax_filter_items()
    {
        $subDistrict = (int)$this->session->get('SUB_DISTRICT');

        $filters = [
            'category' => $this->request->getPost('category'),
            'date'     => $this->request->getPost('date'),
            'stage'    => $this->request->getPost('stage'),
            'venue'    => $this->request->getPost('venue'),
        ];

        $items = $this->coverModel->get_subdistrict_items($subDistrict, $filters);

        return $this->response->setJSON([
            'status' => true,
            'count'  => count($items),
            'items'  => $items,
        ]);
    }

    /**
     * AJAX preview data for selected item
     */
    public function ajax_preview_data()
    {
        $subDistrict = (int)$this->session->get('SUB_DISTRICT');
        $itemCode = (int)$this->request->getGetPost('item_code');
        $templateId = (int)$this->request->getGetPost('template_id');

        $template = null;
        if ($templateId > 0) {
            $template = $this->coverModel->get_template($templateId);
        } else {
            $template = $this->coverModel->get_default_template($subDistrict);
        }

        $itemData = null;
        if ($itemCode > 0) {
            $items = $this->coverModel->get_subdistrict_items($subDistrict, ['item_codes' => [$itemCode]]);
            if (!empty($items)) {
                $item = $items[0];
                $catCode = !empty($item['category_code']) ? $item['category_code'] : 'LP';
                $catTitle = in_array($catCode, ['LP', 'UP', 'HS', 'HSS']) ? ($catCode . ' TAMIL KALAIVIZHA') : (!empty($item['fest_name']) ? $item['fest_name'] : 'LP TAMIL KALAIVIZHA');

                $itemData = [
                    'item_code'    => (string)($item['item_code'] ?? '351'),
                    'item_name'    => $item['item_name'] ?? 'STORY WRITING',
                    'category'     => $catTitle,
                    'category_name'=> $catTitle,
                    'category_code'=> $catCode,
                    'sub_district' => $item['sub_district'] ?? 'Chittur',
                    'district'     => $item['district'] ?? 'Palakkad',
                    'fest_venue'   => $item['fest_venue'] ?? 'GHSS Kozhippara',
                    'stage'        => (!empty($item['stage_display']) && $item['stage_display'] !== '-') ? $item['stage_display'] : 'Stage 1',
                    'stage_desc'   => (!empty($item['stage_desc']) && $item['stage_desc'] !== '-') ? $item['stage_desc'] : 'Main Auditorium',
                    'venue'        => (!empty($item['venue_display']) && $item['venue_display'] !== '-') ? $item['venue_display'] : 'GHSS Kozhippara',
                    'date'         => (!empty($item['date_formatted']) && $item['date_formatted'] !== '-') ? $item['date_formatted'] : date('d-m-Y'),
                    'time'         => (!empty($item['time_formatted']) && $item['time_formatted'] !== '-') ? $item['time_formatted'] : '10:05 AM',
                    'event_name'   => !empty($item['item_category']) ? $item['item_category'] : 'Off-Stage',
                    'event_number' => (string)($item['item_code'] ?? '351'),
                    'section'      => (!empty($item['is_team_item']) && $item['is_team_item'] === 'Y') ? 'Group' : 'Individual',
                ];
            }
        }

        if (!$itemData) {
            $itemData = $this->coverModel->get_sample_item($subDistrict);
        }

        return $this->response->setJSON([
            'status'   => true,
            'data'     => $itemData,
            'template' => $template
        ]);
    }

    /**
     * Upload Image / Logo Asset with MIME validation
     */
    public function upload_asset()
    {
        $file = $this->request->getFile('asset_file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => $file ? $file->getErrorString() : 'No file uploaded',
            ]);
        }

        $allowedExts = ['png', 'jpg', 'jpeg', 'svg'];
        $ext = strtolower($file->getExtension());

        if (!in_array($ext, $allowedExts, true)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Only PNG, JPG, JPEG, and SVG files are allowed.',
            ]);
        }

        // Verify MIME type
        $mime = $file->getMimeType();
        $allowedMimes = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/svg'];
        if (!in_array($mime, $allowedMimes, true)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Invalid image MIME type: ' . esc($mime),
            ]);
        }

        $uploadDir = FCPATH . 'uploads/cover_assets/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $newName = 'asset_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $file->move($uploadDir, $newName);

        $relPath = 'uploads/cover_assets/' . $newName;

        return $this->response->setJSON([
            'status'    => true,
            'message'   => 'Image uploaded successfully!',
            'file_path' => $relPath,
            'url'       => base_url($relPath),
        ]);
    }

    /**
     * Generate Print-Ready PDF (1 Item = 1 Page)
     */
    public function generate_pdf()
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $templateId = (int)$this->request->getPost('template_id');
        $selectedItems = $this->request->getPost('selected_items');
        $category = $this->request->getPost('category') ?: 'ALL';
        $pdfRotation = (int)($this->request->getPost('pdf_rotation') ?? 0);

        if (empty($templateId)) {
            echo "<script>alert('Please select a Cover Template.'); window.close();</script>";
            exit;
        }

        $template = $this->coverModel->get_template($templateId);
        if (!$template) {
            echo "<script>alert('Selected template not found.'); window.close();</script>";
            exit;
        }

        if (empty($selectedItems) || !is_array($selectedItems)) {
            echo "<script>alert('Please select at least one Item to generate covers.'); window.close();</script>";
            exit;
        }

        $subDistrict = (int)$this->session->get('SUB_DISTRICT');
        $userId = (int)$this->session->get('USERID');

        // Fetch selected item records
        $items = $this->coverModel->get_subdistrict_items($subDistrict, [
            'item_codes' => $selectedItems,
        ]);

        if (empty($items)) {
            echo "<script>alert('No matching item records found in database.'); window.close();</script>";
            exit;
        }

        // Initialize PDF generator
        $orientation = (strtolower($template['orientation'] ?? 'portrait') === 'landscape') ? 'L' : 'P';
        $paperSize = $template['paper_size'] ?? 'A4';
        $customW = (float)($template['width_mm'] ?? 210);
        $customH = (float)($template['height_mm'] ?? 297);

        $pdf = new Cover_Pdf($orientation, 'mm', [$customW, $customH]);
        $pdf->SetDisplayMode('fullpage');

        // Render One Cover Per Item (1 Item = 1 Page)
        foreach ($items as $it) {
            $catCode = !empty($it['category_code']) ? $it['category_code'] : 'LP';
            $catTitle = in_array($catCode, ['LP', 'UP', 'HS', 'HSS']) ? ($catCode . ' TAMIL KALAIVIZHA') : (!empty($it['fest_name']) ? $it['fest_name'] : 'LP TAMIL KALAIVIZHA');

            $itemData = [
                'item_code'    => (string)($it['item_code'] ?? ''),
                'item_name'    => $it['item_name'] ?? '',
                'category'     => $catTitle,
                'category_name'=> $catTitle,
                'category_code'=> $catCode,
                'sub_district' => $it['sub_district'] ?? 'Chittur',
                'district'     => $it['district'] ?? 'Palakkad',
                'fest_venue'   => $it['fest_venue'] ?? 'GHSS Kozhippara',
                'stage'        => (!empty($it['stage_display']) && $it['stage_display'] !== '-') ? $it['stage_display'] : '-',
                'stage_desc'   => (!empty($it['stage_desc']) && $it['stage_desc'] !== '-') ? $it['stage_desc'] : '-',
                'venue'        => (!empty($it['venue_display']) && $it['venue_display'] !== '-') ? $it['venue_display'] : '-',
                'date'         => (!empty($it['date_formatted']) && $it['date_formatted'] !== '-') ? $it['date_formatted'] : '-',
                'time'         => (!empty($it['time_formatted']) && $it['time_formatted'] !== '-') ? $it['time_formatted'] : '-',
                'event_name'   => !empty($it['item_category']) ? $it['item_category'] : 'Event',
                'event_number' => (string)($it['item_code'] ?? ''),
                'section'      => (!empty($it['is_team_item']) && $it['is_team_item'] === 'Y') ? 'Group' : 'Individual',
            ];

            $pdf->renderCoverPage($template, $template['elements'], $itemData, $pdfRotation);
        }

        // Log generation history
        $this->coverModel->log_generation([
            'template_id'       => $templateId,
            'sub_district_code' => $subDistrict,
            'category'          => $category,
            'filter_data'       => ['selected_items_count' => count($items), 'items' => $selectedItems],
            'item_count'        => count($items),
            'generated_by'      => $userId,
        ]);

        $filename = 'Kalolsavam_Covers_' . date('Ymd_His') . '.pdf';

        // Output inline for browser print-ready display in new tab
        $pdf->Output($filename, 'I');
        exit;
    }
}
