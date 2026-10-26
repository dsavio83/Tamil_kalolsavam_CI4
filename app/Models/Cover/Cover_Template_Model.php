<?php

namespace App\Models\Cover;

use App\Models\BaseModel;

class Cover_Template_Model extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get list of cover templates for sub-district (or global)
     */
    public function get_templates($subDistrictCode = 0)
    {
        $builder = $this->db->table('cover_templates');
        $builder->where('status', 'A');

        $subDistrictCode = (int)$subDistrictCode;
        if ($subDistrictCode > 0) {
            $builder->groupStart()
                    ->where('sub_district_code', $subDistrictCode)
                    ->orWhere('sub_district_code', 0)
                    ->groupEnd();
        }

        $builder->orderBy('is_default DESC, id DESC');
        return $builder->get()->getResultArray();
    }

    /**
     * Get single template with its elements
     */
    public function get_template($templateId)
    {
        $template = $this->db->table('cover_templates')
            ->where('id', (int)$templateId)
            ->where('status', 'A')
            ->get()
            ->getRowArray();

        if (!$template) {
            return null;
        }

        $elements = $this->db->table('cover_elements')
            ->where('template_id', (int)$templateId)
            ->orderBy('z_index ASC, id ASC')
            ->get()
            ->getResultArray();

        $template['elements'] = $elements;
        return $template;
    }

    /**
     * Get default template for sub-district (or global default)
     */
    public function get_default_template($subDistrictCode = 0)
    {
        $builder = $this->db->table('cover_templates');
        $builder->where('status', 'A');
        $builder->where('is_default', 'Y');

        $subDistrictCode = (int)$subDistrictCode;
        if ($subDistrictCode > 0) {
            $builder->groupStart()
                    ->where('sub_district_code', $subDistrictCode)
                    ->orWhere('sub_district_code', 0)
                    ->groupEnd();
        }

        $builder->orderBy('sub_district_code DESC, id DESC');
        $row = $builder->get()->getRowArray();
        if ($row) {
            return $this->get_template($row['id']);
        }

        // Fallback: prefer DL envelope landscape, then any template
        $dl = $this->db->table('cover_templates')
            ->where('status', 'A')
            ->where('paper_size', 'DL')
            ->orderBy('id DESC')
            ->get()
            ->getRowArray();
        if ($dl) {
            return $this->get_template($dl['id']);
        }

        $first = $this->db->table('cover_templates')
            ->where('status', 'A')
            ->orderBy('id DESC')
            ->get()
            ->getRowArray();
        return $first ? $this->get_template($first['id']) : null;
    }

    /**
     * Save template and its elements (Insert or Update)
     */
    public function save_template($templateData, $elementsData)
    {
        $this->db->transStart();

        $templateId = (int)($templateData['id'] ?? 0);
        $now = date('Y-m-d H:i:s');

        $dbTemplateData = [
            'template_name'     => trim($templateData['template_name'] ?? 'Cover Template'),
            'sub_district_code' => (int)($templateData['sub_district_code'] ?? 0),
            'district_code'     => (int)($templateData['district_code'] ?? 0),
            'paper_size'        => trim($templateData['paper_size'] ?? 'A4'),
            'width_mm'          => (float)($templateData['width_mm'] ?? 210.0),
            'height_mm'         => (float)($templateData['height_mm'] ?? 297.0),
            'orientation'       => strtolower(trim($templateData['orientation'] ?? 'portrait')),
            'background_type'   => strtolower(trim($templateData['background_type'] ?? 'none')),
            'background_value'  => $templateData['background_value'] ?? null,
            'status'            => 'A',
            'updated_at'        => $now,
        ];

        if (isset($templateData['is_default']) && $templateData['is_default'] === 'Y') {
            $dbTemplateData['is_default'] = 'Y';
            // Reset other defaults for this sub_district
            $this->db->table('cover_templates')
                ->where('sub_district_code', $dbTemplateData['sub_district_code'])
                ->update(['is_default' => 'N']);
        }

        if ($templateId > 0) {
            $this->db->table('cover_templates')
                ->where('id', $templateId)
                ->update($dbTemplateData);
        } else {
            $dbTemplateData['created_by'] = (int)($templateData['created_by'] ?? 1);
            $dbTemplateData['created_at'] = $now;
            $dbTemplateData['is_default'] = $templateData['is_default'] ?? 'N';
            $this->db->table('cover_templates')->insert($dbTemplateData);
            $templateId = $this->db->insertID();
        }

        // Replace elements
        $this->db->table('cover_elements')->where('template_id', $templateId)->delete();

        if (!empty($elementsData) && is_array($elementsData)) {
            $batchElements = [];
            $zIndex = 1;
            foreach ($elementsData as $el) {
                $batchElements[] = [
                    'template_id'     => $templateId,
                    'element_type'    => trim($el['element_type'] ?? 'dynamic_field'),
                    'field_name'      => !empty($el['field_name']) ? trim($el['field_name']) : null,
                    'label_name'      => trim($el['label_name'] ?? 'Label'),
                    'static_text'     => $el['static_text'] ?? null,
                    'x'               => (float)($el['x'] ?? $el['pos_x_mm'] ?? 0),
                    'y'               => (float)($el['y'] ?? $el['pos_y_mm'] ?? 0),
                    'width'           => (float)($el['width'] ?? $el['width_mm'] ?? 50),
                    'height'          => (float)($el['height'] ?? $el['height_mm'] ?? 15),
                    'font_family'     => trim($el['font_family'] ?? 'TAU-Paalai'),
                    'font_size'       => (int)($el['font_size'] ?? $el['font_size_pt'] ?? 14),
                    'font_weight'     => trim($el['font_weight'] ?? 'normal'),
                    'font_style'      => trim($el['font_style'] ?? 'normal'),
                    'text_decoration' => trim($el['text_decoration'] ?? 'none'),
                    'font_color'      => trim($el['font_color'] ?? '#000000'),
                    'text_align'      => trim($el['text_align'] ?? 'center'),
                    'rotation'        => (float)($el['rotation'] ?? 0),
                    'z_index'         => (int)($el['z_index'] ?? $zIndex++),
                    'properties_json' => !empty($el['properties_json']) ? (is_string($el['properties_json']) ? $el['properties_json'] : json_encode($el['properties_json'])) : null,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }

            if (!empty($batchElements)) {
                $this->db->table('cover_elements')->insertBatch($batchElements);
            }
        }

        $this->db->transComplete();

        return $this->db->transStatus() ? $templateId : false;
    }

    /**
     * Duplicate a template
     */
    public function duplicate_template($templateId, $userId = 1)
    {
        $template = $this->get_template($templateId);
        if (!$template) {
            return false;
        }

        $template['id'] = 0;
        $template['template_name'] = $template['template_name'] . ' (Copy)';
        $template['is_default'] = 'N';
        $template['created_by'] = $userId;

        return $this->save_template($template, $template['elements']);
    }

    /**
     * Delete a template
     */
    public function delete_template($templateId)
    {
        $this->db->transStart();
        $this->db->table('cover_elements')->where('template_id', (int)$templateId)->delete();
        $this->db->table('cover_templates')->where('id', (int)$templateId)->delete();
        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /**
     * Set default template
     */
    public function set_default_template($templateId, $subDistrictCode = 0)
    {
        $this->db->transStart();
        $this->db->table('cover_templates')
            ->where('sub_district_code', (int)$subDistrictCode)
            ->update(['is_default' => 'N']);

        $this->db->table('cover_templates')
            ->where('id', (int)$templateId)
            ->update(['is_default' => 'Y']);

        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /**
     * Custom Labels
     */
    public function get_custom_labels($subDistrictCode = 0)
    {
        return $this->db->table('cover_custom_labels')
            ->where('status', 'A')
            ->groupStart()
                ->where('sub_district_code', (int)$subDistrictCode)
                ->orWhere('sub_district_code', 0)
            ->groupEnd()
            ->orderBy('id ASC')
            ->get()
            ->getResultArray();
    }

    public function save_custom_label($data)
    {
        $now = date('Y-m-d H:i:s');
        $insert = [
            'sub_district_code' => (int)($data['sub_district_code'] ?? 0),
            'label_name'        => trim($data['label_name']),
            'label_type'        => trim($data['label_type'] ?? 'static_text'),
            'field_name'        => !empty($data['field_name']) ? trim($data['field_name']) : null,
            'default_text'      => !empty($data['default_text']) ? trim($data['default_text']) : null,
            'status'            => 'A',
            'created_by'        => (int)($data['created_by'] ?? 1),
            'created_at'        => $now,
            'updated_at'        => $now,
        ];
        return $this->db->table('cover_custom_labels')->insert($insert);
    }

    // ==========================================
    // SUB-DISTRICT FESTIVAL & ITEM DATA METHODS
    // ==========================================

    /**
     * Get category list for Sub-District (LP, UP, HS, HSS)
     */
    public function get_subdistrict_categories()
    {
        $order_by = "CASE WHEN fest_name LIKE '%LP%' THEN 1 WHEN fest_name LIKE '%UP%' THEN 2 WHEN fest_name LIKE '%HSS%' THEN 4 WHEN fest_name LIKE '%HS%' THEN 3 ELSE fest_id END ASC";
        $query = $this->db->table('festival_master')
            ->orderBy($order_by)
            ->get();
        $rows = $query ? $query->getResultArray() : [];

        $categories = [];
        foreach ($rows as $r) {
            $name = trim($r['fest_name']);
            $code = 'GENERAL';
            if (stripos($name, 'LP') !== false || stripos($name, 'LOWER PRIMARY') !== false) {
                $code = 'LP';
            } elseif (stripos($name, 'UP') !== false || stripos($name, 'UPPER PRIMARY') !== false) {
                $code = 'UP';
            } elseif (stripos($name, 'HSS') !== false || stripos($name, 'HIGHER SECONDARY') !== false) {
                $code = 'HSS';
            } elseif (stripos($name, 'HS') !== false || stripos($name, 'HIGH SCHOOL') !== false) {
                $code = 'HS';
            }

            $displayName = in_array($code, ['LP', 'UP', 'HS', 'HSS']) ? ($code . ' TAMIL KALAIVIZHA') : $name;

            $categories[$code] = [
                'fest_id'   => $r['fest_id'],
                'fest_name' => $displayName,
                'code'      => $code,
            ];
        }

        // Ensure LP, UP, HS, HSS are always available with TAMIL KALAIVIZHA suffix
        $standard = ['LP' => 1, 'UP' => 2, 'HS' => 3, 'HSS' => 4];
        foreach ($standard as $sc => $fid) {
            if (!isset($categories[$sc])) {
                $categories[$sc] = [
                    'fest_id'   => $fid,
                    'fest_name' => $sc . ' TAMIL KALAIVIZHA',
                    'code'      => $sc,
                ];
            }
        }

        return $categories;
    }

    /**
     * Get scheduled dates for Sub-District
     */
    public function get_subdistrict_dates($subDistrictCode)
    {
        $subDistrictCode = (int)$subDistrictCode;
        $sdRow = $this->db->table('sub_district_master')
            ->select('rev_district_code')
            ->where('sub_district_code', $subDistrictCode)
            ->get()
            ->getRowArray();
        $revDistrictCode = (int)($sdRow['rev_district_code'] ?? ((int)$this->session->get('DISTRICT') ?: 9));

        $builder = $this->db->table('sd_stage_item_master SIM');
        $builder->select('DISTINCT DATE(SIM.start_time) as schedule_date', false);
        $builder->join('item_master IM', 'IM.item_code = SIM.item_code', 'inner');
        $builder->where('SIM.sub_district_code', $subDistrictCode);
        $builder->where('SIM.start_time >', '2000-01-01 00:00:00');

        $builder->groupStart();
        $builder->where('IM.is_common', 'Y');
        if ($subDistrictCode > 0 || $revDistrictCode > 0) {
            $builder->orWhere("IM.item_code IN (SELECT item_code FROM sd_item_allotment WHERE (sub_district_code = {$subDistrictCode} AND {$subDistrictCode} > 0) OR (rev_district_code = {$revDistrictCode} AND {$revDistrictCode} > 0))", null, false);
        }
        $builder->groupEnd();

        $builder->orderBy('schedule_date', 'ASC');
        $query = $builder->get();
        if (!$query) {
            return [];
        }
        $rows = $query->getResultArray();

        $dates = [];
        foreach ($rows as $r) {
            if (!empty($r['schedule_date'])) {
                $dates[] = $r['schedule_date'];
            }
        }
        return $dates;
    }

    /**
     * Get stages for Sub-District
     */
    public function get_subdistrict_stages($subDistrictCode)
    {
        return $this->db->table('sd_stage_master')
            ->where('sub_district_code', (int)$subDistrictCode)
            ->orderBy('stage_id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get venues for Sub-District
     */
    public function get_subdistrict_venues($subDistrictCode)
    {
        $subDistrictCode = (int)$subDistrictCode;
        $venues = [];

        // 1. Check primary host venue defined in sd_kalolsavam_master
        $kal = $this->db->table('sd_kalolsavam_master')
            ->select('venue')
            ->where('sub_district_code', $subDistrictCode)
            ->get()
            ->getRowArray();
        if (!empty($kal['venue']) && trim($kal['venue']) !== '') {
            $venues[] = trim($kal['venue']);
        }

        // 2. Check stage venue descriptions in sd_stage_master if distinct from stage names
        $rows = $this->db->table('sd_stage_master')
            ->select('stage_name, stage_desc')
            ->where('sub_district_code', $subDistrictCode)
            ->get();
        if ($rows) {
            foreach ($rows->getResultArray() as $r) {
                $desc = trim($r['stage_desc'] ?? '');
                $name = trim($r['stage_name'] ?? '');
                if (!empty($desc) && strcasecmp($desc, $name) !== 0 && stripos($desc, 'Stage') === false && !in_array($desc, $venues, true)) {
                    $venues[] = $desc;
                }
            }
        }

        if (empty($venues)) {
            $venues[] = 'GHSS Kozhippara';
        }

        return array_values(array_unique($venues));
    }

    /**
     * Get items list for Sub-District matching filters
     */
    public function get_subdistrict_items($subDistrictCode, $filters = [])
    {
        $subDistrictCode = (int)$subDistrictCode;

        // Fetch Sub-District Name and Revenue District Name
        $sdRow = $this->db->table('sub_district_master SDM')
            ->select('SDM.sub_district_name, SDM.rev_district_code, RDM.rev_district_name')
            ->join('rev_district_master RDM', 'RDM.rev_district_code = SDM.rev_district_code', 'left')
            ->where('SDM.sub_district_code', $subDistrictCode)
            ->get()
            ->getRowArray();
        $subDistrictName = !empty($sdRow['sub_district_name']) ? trim($sdRow['sub_district_name']) : 'Chittur';
        $districtName    = !empty($sdRow['rev_district_name']) ? trim($sdRow['rev_district_name']) : 'Palakkad';
        $revDistrictCode = (int)($sdRow['rev_district_code'] ?? 0);
        if ($revDistrictCode <= 0) {
            $revDistrictCode = (int)($this->session->get('DISTRICT') ?: 9);
        }

        // Fetch host festival venue from sd_kalolsavam_master
        $kal = $this->db->table('sd_kalolsavam_master')
            ->select('venue')
            ->where('sub_district_code', $subDistrictCode)
            ->get()
            ->getRowArray();
        $defaultVenue = !empty($kal['venue']) && trim($kal['venue']) !== '' ? trim($kal['venue']) : 'GHSS Kozhippara';

        $builder = $this->db->table('item_master IM');
        $builder->select("
            IM.item_code,
            IM.item_name,
            IM.item_type,
            IM.is_off_stage,
            CASE WHEN IM.is_off_stage = 'Y' THEN 'Off-Stage' ELSE 'On-Stage' END AS item_category,
            CASE WHEN IM.item_type = 'G' THEN 'Y' ELSE 'N' END AS is_team_item,
            FM.fest_id,
            FM.fest_name,
            CASE 
                WHEN FM.fest_name LIKE '%LP%' OR FM.fest_name LIKE '%LOWER PRIMARY%' THEN 'LP'
                WHEN FM.fest_name LIKE '%UP%' OR FM.fest_name LIKE '%UPPER PRIMARY%' THEN 'UP'
                WHEN FM.fest_name LIKE '%HSS%' OR FM.fest_name LIKE '%HIGHER SECONDARY%' THEN 'HSS'
                WHEN FM.fest_name LIKE '%HS%' OR FM.fest_name LIKE '%HIGH SCHOOL%' THEN 'HS'
                ELSE 'GENERAL'
            END AS category_code,
            SIM.start_time,
            COALESCE(SM.stage_name, 'Stage 1') AS stage_name,
            COALESCE(SM.stage_desc, '') AS stage_desc
        ", false);

        $builder->join('festival_master FM', 'FM.fest_id = IM.fest_id', 'left');
        $builder->join('sd_stage_item_master SIM', "SIM.item_code = IM.item_code AND SIM.sub_district_code = {$subDistrictCode}", 'left');
        $builder->join('sd_stage_master SM', "SM.stage_id = SIM.stage_id AND SM.sub_district_code = {$subDistrictCode}", 'left');

        // Filter district code based eligible items only:
        // Common items (is_common = 'Y', 40 items) are eligible for all districts.
        // Special items (8 items) are eligible only if allotted in sd_item_allotment for this sub-district or district.
        // For Palakkad -> 40 items. For Idukki -> 48 items. Other districts -> their proper items.
        $builder->groupStart();
        $builder->where('IM.is_common', 'Y');
        if ($subDistrictCode > 0 || $revDistrictCode > 0) {
            $builder->orWhere("IM.item_code IN (SELECT item_code FROM sd_item_allotment WHERE (sub_district_code = {$subDistrictCode} AND {$subDistrictCode} > 0) OR (rev_district_code = {$revDistrictCode} AND {$revDistrictCode} > 0))", null, false);
        }
        $builder->groupEnd();

        // Filter by Category (LP, UP, HS, HSS)
        if (!empty($filters['category']) && strtoupper($filters['category']) !== 'ALL') {
            $cat = strtoupper(trim($filters['category']));
            if ($cat === 'LP') {
                $builder->groupStart()->like('FM.fest_name', 'LP')->orLike('FM.fest_name', 'LOWER PRIMARY')->groupEnd();
            } elseif ($cat === 'UP') {
                $builder->groupStart()->like('FM.fest_name', 'UP')->orLike('FM.fest_name', 'UPPER PRIMARY')->groupEnd();
            } elseif ($cat === 'HSS') {
                $builder->groupStart()->like('FM.fest_name', 'HSS')->orLike('FM.fest_name', 'HIGHER SECONDARY')->groupEnd();
            } elseif ($cat === 'HS') {
                $builder->groupStart()
                    ->groupStart()->like('FM.fest_name', 'HS')->orLike('FM.fest_name', 'HIGH SCHOOL')->groupEnd()
                    ->notLike('FM.fest_name', 'HSS')
                    ->notLike('FM.fest_name', 'HIGHER SECONDARY')
                    ->groupEnd();
            }
        }

        // Filter by Date
        if (!empty($filters['date']) && $filters['date'] !== 'All') {
            $builder->where('DATE(SIM.start_time)', $filters['date']);
        }

        // Filter by Stage
        if (!empty($filters['stage']) && $filters['stage'] !== 'All') {
            $builder->where('SM.stage_id', (int)$filters['stage']);
        }

        // Filter by Specific Item Codes (e.g. for PDF generation)
        if (!empty($filters['item_codes']) && is_array($filters['item_codes'])) {
            $builder->whereIn('IM.item_code', array_map('intval', $filters['item_codes']));
        }

        $builder->orderBy('IM.fest_id ASC, IM.item_code ASC');
        $query = $builder->get();
        $results = $query ? $query->getResultArray() : [];

        // Enrich items with formatted date, time, and dynamic replacements
        foreach ($results as &$item) {
            $d = !empty($item['start_time']) && $item['start_time'] !== '0000-00-00 00:00:00' ? strtotime($item['start_time']) : null;
            $item['date_formatted'] = $d ? date('d-m-Y', $d) : '-';
            $item['time_formatted'] = $d ? date('h:i A', $d) : '-';
            $item['stage_display']  = !empty($item['stage_name']) ? $item['stage_name'] : 'Stage 1';
            $item['stage']          = $item['stage_display'];
            $item['stage_desc']     = !empty($item['stage_desc']) ? $item['stage_desc'] : $item['stage'];

            // Venue: use host venue from sd_kalolsavam_master (competition location), unless stage_desc provides a distinct hall/room
            $stageDesc = trim($item['stage_desc'] ?? '');
            if (!empty($stageDesc) && strcasecmp($stageDesc, $item['stage_display']) !== 0 && stripos($stageDesc, 'Stage') === false) {
                $item['venue_display'] = $stageDesc;
            } else {
                $item['venue_display'] = $defaultVenue;
            }
            $item['venue']         = $item['venue_display'];
            $item['fest_venue']    = $defaultVenue;
            $item['sub_district']  = $subDistrictName;
            $item['district']      = $districtName;
            $item['item_category'] = (!empty($item['is_off_stage']) && $item['is_off_stage'] === 'Y') ? 'Off-Stage' : 'On-Stage';

            $code = $item['category_code'] ?? 'LP';
            $item['category_display'] = in_array($code, ['LP', 'UP', 'HS', 'HSS']) ? ($code . ' TAMIL KALAIVIZHA') : (!empty($item['fest_name']) ? $item['fest_name'] : 'TAMIL KALAIVIZHA');
            $item['fest_name'] = $item['category_display'];
        }
        unset($item);

        return $results;
    }

    /**
     * Get a realistic sample item for live designer preview
     */
    public function get_sample_item($subDistrictCode = 0)
    {
        $sdRow = $this->db->table('sub_district_master SDM')
            ->select('SDM.sub_district_name, RDM.rev_district_name')
            ->join('rev_district_master RDM', 'RDM.rev_district_code = SDM.rev_district_code', 'left')
            ->where('SDM.sub_district_code', (int)$subDistrictCode)
            ->get()
            ->getRowArray();
        $subDistrictName = !empty($sdRow['sub_district_name']) ? trim($sdRow['sub_district_name']) : 'Chittur';
        $districtName    = !empty($sdRow['rev_district_name']) ? trim($sdRow['rev_district_name']) : 'Palakkad';

        $kal = $this->db->table('sd_kalolsavam_master')
            ->select('venue')
            ->where('sub_district_code', (int)$subDistrictCode)
            ->get()
            ->getRowArray();
        $defaultVenue = !empty($kal['venue']) && trim($kal['venue']) !== '' ? trim($kal['venue']) : 'GHSS Kozhippara';

        $items = $this->get_subdistrict_items($subDistrictCode);
        if (!empty($items)) {
            $first = $items[0];
            $catCode = !empty($first['category_code']) ? $first['category_code'] : 'LP';
            $catTitle = in_array($catCode, ['LP', 'UP', 'HS', 'HSS']) ? ($catCode . ' TAMIL KALAIVIZHA') : (!empty($first['fest_name']) ? $first['fest_name'] : 'LP TAMIL KALAIVIZHA');

            return [
                'item_code'    => (string)($first['item_code'] ?? '351'),
                'item_name'    => $first['item_name'] ?? 'STORY WRITING',
                'category'     => $catTitle,
                'category_name'=> $catTitle,
                'category_code'=> $catCode,
                'sub_district' => $first['sub_district'] ?? $subDistrictName,
                'district'     => $first['district'] ?? $districtName,
                'fest_venue'   => $defaultVenue,
                'stage'        => (!empty($first['stage_display']) && $first['stage_display'] !== '-') ? $first['stage_display'] : 'Stage 1',
                'stage_desc'   => (!empty($first['stage_desc']) && $first['stage_desc'] !== '-') ? $first['stage_desc'] : 'Main Auditorium',
                'venue'        => (!empty($first['venue_display']) && $first['venue_display'] !== '-') ? $first['venue_display'] : $defaultVenue,
                'date'         => (!empty($first['date_formatted']) && $first['date_formatted'] !== '-') ? $first['date_formatted'] : '30-09-2026',
                'time'         => (!empty($first['time_formatted']) && $first['time_formatted'] !== '-') ? $first['time_formatted'] : '10:05 AM',
                'event_name'   => !empty($first['item_category']) ? $first['item_category'] : 'Off-Stage',
                'event_number' => (string)($first['item_code'] ?? '351'),
                'section'      => (!empty($first['is_team_item']) && $first['is_team_item'] === 'Y') ? 'Group' : 'Individual',
            ];
        }

        // Fallback realistic sample matching sketch
        return [
            'item_code'    => '351',
            'item_name'    => 'STORY WRITING',
            'category'     => 'LP TAMIL KALAIVIZHA',
            'category_name'=> 'LP TAMIL KALAIVIZHA',
            'category_code'=> 'LP',
            'sub_district' => $subDistrictName,
            'district'     => $districtName,
            'fest_venue'   => $defaultVenue,
            'stage'        => 'Stage 1',
            'stage_desc'   => 'Main Auditorium',
            'venue'        => $defaultVenue,
            'date'         => '30-09-2026',
            'time'         => '10:05 AM',
            'event_name'   => 'Off-Stage',
            'event_number' => '351',
            'section'      => 'Individual',
        ];
    }

    /**
     * Log generation history
     */
    public function log_generation($logData)
    {
        $now = date('Y-m-d H:i:s');
        $insert = [
            'template_id'       => (int)($logData['template_id'] ?? 0),
            'sub_district_code' => (int)($logData['sub_district_code'] ?? 0),
            'category'          => $logData['category'] ?? 'ALL',
            'filter_data'       => is_array($logData['filter_data'] ?? null) ? json_encode($logData['filter_data']) : ($logData['filter_data'] ?? null),
            'item_count'        => (int)($logData['item_count'] ?? 0),
            'pdf_path'          => $logData['pdf_path'] ?? null,
            'generated_by'      => (int)($logData['generated_by'] ?? 1),
            'created_at'        => $now,
        ];

        return $this->db->table('cover_generation_logs')->insert($insert);
    }
}
