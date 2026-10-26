<?php

namespace App\Models\District\Certificate;

use App\Models\BaseModel;

class Certificate_Model extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get District certificate template coordinates.
     * Enforces default font 'TAU-Pallai' at size 14 for Tamil documents.
     */
    public function get_certificate_template(int $districtCode): array
    {
        $template = $this->db->table('d_certificate_template')
            ->where('district_code', $districtCode)
            ->get()->getRowArray();

        if (!$template) {
            // Seed default layout with TAU-Pallai font at size 14
            $default = [
                'district_code' => $districtCode,
                'name_x'        => 120,
                'name_y'        => 140,
                'name_font'     => 'TAU-Pallai',
                'name_size'     => 14,
                'item_x'        => 120,
                'item_y'        => 165,
                'item_font'     => 'TAU-Pallai',
                'item_size'     => 14,
                'category_x'    => 120,
                'category_y'    => 190,
                'category_font' => 'TAU-Pallai',
                'category_size' => 14,
                'grade_x'       => 120,
                'grade_y'       => 215,
                'grade_font'    => 'TAU-Pallai',
                'grade_size'    => 14,
                'class_x'       => 120,
                'class_y'       => 240,
                'class_font'    => 'TAU-Pallai',
                'class_size'    => 14,
                'school_x'      => 120,
                'school_y'      => 265,
                'school_font'   => 'TAU-Pallai',
                'school_size'   => 14,
                'sub_dist_x'    => 120,
                'sub_dist_y'    => 290,
                'sub_dist_font' => 'TAU-Pallai',
                'sub_dist_size' => 14,
                'dist_x'        => 120,
                'dist_y'        => 315,
                'dist_font'     => 'TAU-Pallai',
                'dist_size'     => 14,
                'date_x'        => 120,
                'date_y'        => 340,
                'date_font'     => 'TAU-Pallai',
                'date_size'     => 14,
                'place_x'       => 120,
                'place_y'       => 365,
                'place_font'    => 'TAU-Pallai',
                'place_size'    => 14,
                'photoX'        => 460,
                'photoY'        => 140,
                'photoWidth'    => 100,
                'photoHight'    => 120,
                'page_style'    => 'P',
                'line_height'   => 2,
                'top_margin'    => 10,
                'left_margin'   => 10,
                'right_margin'  => 10,
            ];
            $this->db->table('d_certificate_template')->insert($default);
            return $default;
        }

        return $template;
    }

    /**
     * Save / Update certificate template settings
     */
    public function save_certificate_template(int $districtCode, array $postData = []): bool
    {
        return $this->save_certificate_details($districtCode);
    }

    /**
     * Get certificates eligible for printing
     */
    public function get_certificates(int $districtCode, array $filters = []): array
    {
        $this->db->select('
            dcm.*,
            im.item_name,
            im.item_type,
            im.fest_id,
            fm.fest_name,
            dp.participant_name,
            dp.class,
            dp.gender,
            dp.photo_filename,
            dp.photo_status,
            sm.school_name,
            sdm.sub_district_name,
            rdm.rev_district_name
        ');
        $this->db->from('d_certificate_master AS dcm');
        $this->db->join('item_master AS im', 'im.item_code = dcm.item_code');
        $this->db->join('festival_master AS fm', 'fm.fest_id = im.fest_id', 'left');
        $this->db->join('d_participant_details AS dp', 'dp.d_participant_id = dcm.d_participant_id');
        $this->db->join('d_school_master AS sm', 'sm.school_code = dcm.school_code', 'left');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = dcm.sub_district_code', 'left');
        $this->db->join('rev_district_master AS rdm', 'rdm.rev_district_code = dcm.district_code', 'left');
        $this->db->where('dcm.district_code', $districtCode);
        $this->db->where('dcm.is_withheld', 'N');
        $this->db->where('im.fest_id !=', 1);

        if (!empty($filters['item_code'])) {
            $this->db->where('dcm.item_code', (int)$filters['item_code']);
        }
        if (!empty($filters['school_code'])) {
            $this->db->where('dcm.school_code', (float)$filters['school_code']);
        }
        if (!empty($filters['sub_district_code'])) {
            $this->db->where('dcm.sub_district_code', (int)$filters['sub_district_code']);
        }
        if (!empty($filters['rank'])) {
            $this->db->where('dcm.rank', (int)$filters['rank']);
        }
        if (!empty($filters['grade'])) {
            $this->db->where('dcm.grade', $filters['grade']);
        }

        $this->db->orderBy('dcm.item_code', 'ASC');
        $this->db->orderBy('FIELD(dcm.rank, 1, 2, 3, 4, 0)');
        $this->db->orderBy('dcm.grade', 'ASC');

        $query = $this->db->get();
        return $query ? $query->getResultArray() : [];
    }

    public function get_font_array()
    {
        return [
            'TAU-Pallai' => 'TAU-Pallai',
            'Arial'      => 'Arial',
            'courier'    => 'Courier',
            'helvetica'  => 'Helvetica',
            'times'      => 'Times',
            'MTCORSVA'   => 'MTCORSVA',
        ];
    }

    public function get_font_size_array()
    {
        return array_combine(range(9, 28), range(9, 28));
    }

    public function get_line_height_array()
    {
        return array_combine(range(1, 30), range(1, 30));
    }

    protected function get_input_field(string $name, $default = null)
    {
        if (isset($_POST[$name]) && $_POST[$name] !== '') {
            return $_POST[$name];
        }
        if ($this->input) {
            $val = $this->input->post($name);
            if ($val !== null && $val !== '') {
                return $val;
            }
        }
        if ($this->request && method_exists($this->request, 'getPost')) {
            $val = $this->request->getPost($name);
            if ($val !== null && $val !== '') {
                return $val;
            }
        }
        if (isset($_REQUEST[$name]) && $_REQUEST[$name] !== '') {
            return $_REQUEST[$name];
        }
        return $default;
    }

    public function save_certificate_details(int $dist_code = 0, int $sub_dist_code = 0): bool
    {
        if ($dist_code <= 0) {
            $dist_code = (int)($this->session->get('DISTRICT') ?? 0);
        }
        if ($dist_code <= 0) {
            $dist_code = (int)($this->session->get('district_code') ?? 0);
        }
        if ($dist_code <= 0) {
            $row = $this->db->table('d_certificate_template')->select('district_code')->get()->getFirstRow('array');
            $dist_code = !empty($row['district_code']) ? (int)$row['district_code'] : 9;
        }

        $page_style = (string)$this->get_input_field('cboPageStyle', 'L');
        $is_portrait = ($page_style === 'P');

        $getCoord = function(string $name, int $default) {
            $v = $this->get_input_field($name, null);
            if ($v !== null && $v !== '') {
                return (int)$v;
            }
            return $default;
        };

        $data = [];
        $data['title_x']       = $getCoord('txtTitleX', $is_portrait ? 40 : 70);
        $data['title_y']       = $getCoord('txtTitleY', $is_portrait ? 45 : 35);
        $data['title_font']    = (string)$this->get_input_field('cboTitleFont', 'TAU-Pallai');
        $data['title_size']    = (int)$this->get_input_field('cboTitleSize', 16);
        $data['title']         = (string)$this->get_input_field('txtTitle', 'KERALA STATE TAMIL FESTIVAL 2026');

        $data['name_x']        = $getCoord('txtNameX', $is_portrait ? 45 : 70);
        $data['name_y']        = $getCoord('txtNameY', $is_portrait ? 85 : 65);
        $data['name_font']     = (string)$this->get_input_field('cboNameFont', 'TAU-Pallai');
        $data['name_size']     = (int)$this->get_input_field('cboNameSize', 14);

        $data['class_x']       = $getCoord('txtClassX', $is_portrait ? 45 : 70);
        $data['class_y']       = $getCoord('txtClassY', $is_portrait ? 100 : 75);
        $data['class_font']    = (string)$this->get_input_field('cboClassFont', 'TAU-Pallai');
        $data['class_size']    = (int)$this->get_input_field('cboClassSize', 14);

        $data['school_x']      = $getCoord('txtSchoolX', $is_portrait ? 45 : 70);
        $data['school_y']      = $getCoord('txtSchoolY', $is_portrait ? 115 : 85);
        $data['school_font']   = (string)$this->get_input_field('cboSchoolFont', 'TAU-Pallai');
        $data['school_size']   = (int)$this->get_input_field('cboSchoolSize', 14);

        $data['sub_dist_x']    = $getCoord('txtSubdistrictX', $is_portrait ? 45 : 70);
        $data['sub_dist_y']    = $getCoord('txtSubdistrictY', $is_portrait ? 130 : 95);
        $data['sub_dist_font'] = (string)$this->get_input_field('cboSubDistFont', 'TAU-Pallai');
        $data['sub_dist_size'] = (int)$this->get_input_field('cboSubDistSize', 14);

        $data['dist_x']        = $getCoord('txtDistrictX', $is_portrait ? 45 : 70);
        $data['dist_y']        = $getCoord('txtDistrictY', $is_portrait ? 145 : 105);
        $data['dist_font']     = (string)$this->get_input_field('cboDistFont', 'TAU-Pallai');
        $data['dist_size']     = (int)$this->get_input_field('cboDistSize', 14);

        $data['item_x']        = $getCoord('txtItemX', $is_portrait ? 45 : 70);
        $data['item_y']        = $getCoord('txtItemY', $is_portrait ? 160 : 115);
        $data['item_font']     = (string)$this->get_input_field('cboItemFont', 'TAU-Pallai');
        $data['item_size']     = (int)$this->get_input_field('cboItemSize', 14);

        $data['category_x']    = $getCoord('txtCategoryX', $is_portrait ? 45 : 70);
        $data['category_y']    = $getCoord('txtCategoryY', $is_portrait ? 175 : 125);
        $data['category_font'] = (string)$this->get_input_field('cboCategoryFont', 'TAU-Pallai');
        $data['category_size'] = (int)$this->get_input_field('cboCategorySize', 14);

        $data['grade_x']       = $getCoord('txtGradeX', $is_portrait ? 45 : 70);
        $data['grade_y']       = $getCoord('txtGradeY', $is_portrait ? 190 : 135);
        $data['grade_font']    = (string)$this->get_input_field('cboGradeFont', 'TAU-Pallai');
        $data['grade_size']    = (int)$this->get_input_field('cboGradeSize', 14);

        $data['ehs_X']         = $getCoord('txtehsX', $is_portrait ? 45 : 70);
        $data['ehs_Y']         = $getCoord('txtehsY', $is_portrait ? 205 : 145);
        $data['ehs_font']      = (string)$this->get_input_field('cboehsFont', 'TAU-Pallai');
        $data['ehs_size']      = (int)$this->get_input_field('cboehsSize', 14);

        $data['date_x']        = $getCoord('txtDateX', $is_portrait ? 35 : 45);
        $data['date_y']        = $getCoord('txtDateY', $is_portrait ? 240 : 170);
        $data['date_font']     = (string)$this->get_input_field('cboDateFont', 'TAU-Pallai');
        $data['date_size']     = (int)$this->get_input_field('cboDateSize', 14);

        $data['place_x']       = $getCoord('txtPlaceX', $is_portrait ? 140 : 190);
        $data['place_y']       = $getCoord('txtPlaceY', $is_portrait ? 240 : 170);
        $data['place_font']    = (string)$this->get_input_field('cboPlaceFont', 'TAU-Pallai');
        $data['place_size']    = (int)$this->get_input_field('cboPlaceSize', 14);

        $data['photoX']        = $getCoord('photoX', $is_portrait ? 150 : 235);
        $data['photoY']        = $getCoord('photoY', 45);
        $data['photoWidth']    = $getCoord('photoWidth', 150);
        $data['photoHight']    = $getCoord('photoHight', 200);
        if ($data['photoWidth'] <= 0) $data['photoWidth'] = 150;
        if ($data['photoHight'] <= 0) $data['photoHight'] = 200;

        $data['page_style']    = $page_style;
        $data['line_height']   = (int)$this->get_input_field('cboLineHeight', 5);
        $data['top_margin']    = (int)$this->get_input_field('cboTopMargin', 10);
        $data['left_margin']   = (int)$this->get_input_field('cboLeftMargin', 15);
        $data['right_margin']  = (int)$this->get_input_field('cboRightMargin', 15);
        $data['type_id']       = (int)$this->get_input_field('cboCtType', 1);
        $data['label_print']   = (string)$this->get_input_field('cboLabelPrint', 'Y');

        $existing = $this->db->table('d_certificate_template')
            ->where('district_code', $dist_code)
            ->orderBy('ct_id', 'ASC')
            ->get()->getRowArray();

        $data['district_code'] = $dist_code;

        if ($existing && !empty($existing['ct_id'])) {
            return (bool)$this->db->table('d_certificate_template')
                ->where('ct_id', (int)$existing['ct_id'])
                ->update($data);
        } else {
            $data['sub_district_code'] = $sub_dist_code ?: 0;
            return (bool)$this->db->table('d_certificate_template')->insert($data);
        }
    }

    /**
     * District certificate template by district code (with fallback coordinates).
     */
    public function get_certificate_details(int $distCode = 0, int $subDistCode = 0): array
    {
        if ($distCode <= 0) {
            $distCode = (int)($this->session->get('DISTRICT') ?? 0);
        }
        if ($distCode <= 0) {
            $distCode = (int)($this->session->get('district_code') ?? 0);
        }
        if ($distCode <= 0) {
            $distCode = (int)($this->session->get('REV_DISTRICT') ?? 0);
        }
        if ($distCode <= 0) {
            $userId = (int)($this->session->get('USERID') ?? 0);
            if ($userId > 0) {
                $u = $this->db->table('user_master')->select('rev_district_code')->where('user_id', $userId)->get()->getRowArray();
                if (!empty($u['rev_district_code'])) {
                    $distCode = (int)$u['rev_district_code'];
                }
            }
        }
        if ($distCode <= 0) {
            $distCode = 9;
        }

        $template = $this->db->table('d_certificate_template')
            ->where('district_code', $distCode)
            ->orderBy('ct_id', 'ASC')
            ->get()->getRowArray();

        $is_portrait = (!empty($template['page_style']) && $template['page_style'] === 'P');

        $fallback = array(
            'ct_id'             => $template['ct_id'] ?? null,
            'sub_district_code' => $subDistCode,
            'district_code'     => $distCode,
            'page_style'        => !empty($template['page_style']) ? $template['page_style'] : ($is_portrait ? 'P' : 'L'),
            'line_height'       => !empty($template['line_height']) ? $template['line_height'] : 5,
            'top_margin'        => !empty($template['top_margin']) ? $template['top_margin'] : 10,
            'left_margin'       => !empty($template['left_margin']) ? $template['left_margin'] : 15,
            'right_margin'      => !empty($template['right_margin']) ? $template['right_margin'] : 15,
            'type_id'           => !empty($template['type_id']) ? $template['type_id'] : 1,
            'label_print'       => !empty($template['label_print']) ? $template['label_print'] : 'Y',
            'title'             => !empty($template['title']) ? $template['title'] : 'KERALA STATE TAMIL FESTIVAL 2026 - PALAKKAD',
            'title_x' => $is_portrait ? 40 : 70,  'title_y' => $is_portrait ? 45 : 35,  'title_font' => 'TAU-Pallai', 'title_size' => 16,
            'name_x'  => $is_portrait ? 45 : 70,  'name_y'  => $is_portrait ? 85 : 65,  'name_font' => 'TAU-Pallai', 'name_size' => 14,
            'class_x' => $is_portrait ? 45 : 70,  'class_y' => $is_portrait ? 100 : 75, 'class_font' => 'TAU-Pallai', 'class_size' => 14,
            'school_x'=> $is_portrait ? 45 : 70,  'school_y'=> $is_portrait ? 115 : 85, 'school_font' => 'TAU-Pallai', 'school_size' => 14,
            'sub_dist_x' => $is_portrait ? 45 : 70, 'sub_dist_y' => $is_portrait ? 130 : 95, 'sub_dist_font' => 'TAU-Pallai', 'sub_dist_size' => 14,
            'dist_x'  => $is_portrait ? 45 : 70,  'dist_y'  => $is_portrait ? 145 : 105, 'dist_font' => 'TAU-Pallai', 'dist_size' => 14,
            'item_x'  => $is_portrait ? 45 : 70,  'item_y'  => $is_portrait ? 160 : 115, 'item_font' => 'TAU-Pallai', 'item_size' => 14,
            'category_x' => $is_portrait ? 45 : 70, 'category_y' => $is_portrait ? 175 : 125, 'category_font' => 'TAU-Pallai', 'category_size' => 14,
            'grade_x' => $is_portrait ? 45 : 70,  'grade_y' => $is_portrait ? 190 : 135, 'grade_font' => 'TAU-Pallai', 'grade_size' => 14,
            'ehs_X'   => $is_portrait ? 45 : 70,  'ehs_Y'   => $is_portrait ? 205 : 145, 'ehs_font' => 'TAU-Pallai', 'ehs_size' => 14,
            'date_x'  => $is_portrait ? 35 : 45,  'date_y'  => $is_portrait ? 240 : 170, 'date_font' => 'TAU-Pallai', 'date_size' => 14,
            'place_x' => $is_portrait ? 140 : 190, 'place_y' => $is_portrait ? 240 : 170, 'place_font' => 'TAU-Pallai', 'place_size' => 14,
            'photoX'  => $is_portrait ? 150 : 235, 'photoY'  => 45, 'photoWidth' => 150, 'photoHight' => 200
        );

        if (!$template) {
            return [$fallback];
        }

        // Fill any unset or null/empty string coordinates with sensible defaults
        foreach ($fallback as $k => $v) {
            if (!isset($template[$k]) || $template[$k] === '' || $template[$k] === null) {
                $template[$k] = $v;
            }
        }

        return [$template];
    }

    /**
     * Certificate eligibility condition from the district certificate template type.
     */
    public function get_certificate_type_condition(): string
    {
        $district = (int)$this->session->get('DISTRICT');
        $row = $this->db->table('d_certificate_template')
            ->select('type_id')
            ->where('district_code', $district)
            ->orderBy('ct_id', 'ASC')
            ->get()->getRowArray();

        $where = '';
        if (!empty($row)) {
            switch ((int)$row['type_id']) {
                case 1:  $where = "(cm.grade = 'A')"; break;
                case 2:  $where = "(cm.grade IN ('A','B'))"; break;
                case 3:  $where = "(cm.grade IN ('A','B','C'))"; break;
                case 4:  $where = "cm.rank != 0 and cm.rank < 4"; break;
                case 5:  $where = "(cm.rank < 3 and cm.rank != 0)"; break;
                case 6:  $where = "(cm.rank = 1)"; break;
                case 7:  $where = "(cm.rank = 1  and cm.grade = 'A')"; break;
                case 8:  $where = "((cm.rank < 4 and cm.rank != 0)  or cm.grade IN ('A'))"; break;
                case 9:  $where = "(cm.grade IN ('A','B','C',''))"; break;
            }
        }
        return $where;
    }

    /**
     * Item wise certificate overview for a festival / school.
     */
    public function get_item_certificate($festtype, $school_code = '')
    {
        $district = $this->session->get('DISTRICT');
        $cm_sd  = !empty($district) ? " AND cm.district_code = " . (int)$district : "";
        $rm_sd  = !empty($district) ? " AND rm.district_code = " . (int)$district : "";
        $rm1_sd = !empty($district) ? " AND rm1.district_code = " . (int)$district : "";

        $this->db->select('count( p.d_participant_id ) AS cpt, p.item_code, i.item_type, 
			i.item_name, f.fest_name,rs.is_certificate_printed,
			(SELECT COUNT(cm.grade) FROM d_certificate_master cm WHERE cm.is_withheld="N" AND cm.item_code=p.item_code AND cm.admn_no=cm.parent_admn_no  AND cm.grade = "A" ' . $cm_sd . ' GROUP BY cm.grade) AS a_grade,
			(SELECT COUNT(cm.grade) FROM d_certificate_master cm WHERE cm.is_withheld="N" AND cm.item_code=p.item_code AND cm.admn_no=cm.parent_admn_no  AND cm.grade = "B" ' . $cm_sd . ' GROUP BY cm.grade) AS b_grade,
			(SELECT COUNT(cm.grade) FROM d_certificate_master cm WHERE cm.is_withheld="N" AND cm.item_code=p.item_code AND cm.admn_no=cm.parent_admn_no  AND cm.grade = "C" ' . $cm_sd . ' GROUP BY cm.grade) AS c_grade,
			 (SELECT count(rm.d_participant_id) FROM d_result_master rm WHERE rm.item_code=p.item_code ' . $rm_sd . ') AS participated_no,
			 IF((SELECT count(rm1.item_code) FROM d_result_master rm1 
					WHERE rm1.item_code=p.item_code ' . $rm1_sd . ') > 0 ,
					IF((	SELECT count(rm1.item_code) FROM d_result_master rm1 
							WHERE rm1.item_code=p.item_code ' . $rm1_sd . ' AND rm1.is_finish ="N"
						) > 0, "No", "Yes"
					),
					"No"
			) AS is_confirmed', FALSE);
        $this->db->from('d_participant_item_details AS p');
        $rs_join = 'rs.item_code = p.item_code' . (!empty($district) ? ' AND rs.district_code = p.district_code' : '');
        $this->db->join('d_result_master AS rs', $rs_join);
        $this->db->join('item_master AS i', 'i.item_code = p.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->where('i.fest_id !=', 1);
        if (!empty($district)) {
            $this->db->where('p.district_code', $district);
        }
        if ('' == $school_code)
            $this->db->where('i.fest_id', $festtype);
        $this->db->where('p.is_captain', 'Y');
        $this->db->groupBy('p.item_code');
        $this->db->having("is_confirmed = 'Yes'");
        $this->db->orderBy('p.item_code');
        $getdet = $this->db->get();
        return $getdet ? $getdet->getResultArray() : [];
    }

    public function get_item_certificate_single($festtype)
    {
        if ((int)$festtype === 1) {
            return [];
        }
        $district = $this->session->get('DISTRICT');
        $cm_sd  = !empty($district) ? " AND cm.district_code = " . (int)$district : "";
        $rm_sd  = !empty($district) ? " AND rm.district_code = " . (int)$district : "";
        $rm1_sd = !empty($district) ? " AND rm1.district_code = " . (int)$district : "";

        $this->db->select('count( p.d_participant_id) AS cpt, p.item_code, i.item_type, i.item_name, 
		f.fest_name,
		(SELECT COUNT(cm.grade) FROM d_certificate_master cm WHERE cm.item_code=p.item_code AND cm.grade = "A" ' . $cm_sd . ' GROUP BY cm.grade) AS a_grade,
		(SELECT COUNT(cm.grade) FROM d_certificate_master cm WHERE cm.item_code=p.item_code AND cm.grade = "B" ' . $cm_sd . ' GROUP BY cm.grade) AS b_grade,
		(SELECT COUNT(cm.grade) FROM d_certificate_master cm WHERE cm.item_code=p.item_code AND cm.grade = "C" ' . $cm_sd . ' GROUP BY cm.grade) AS c_grade,
		(SELECT count(rm.d_participant_id) FROM d_result_master rm WHERE rm.item_code=p.item_code ' . $rm_sd . ') AS participated_no,
		 IF((SELECT count(rm1.item_code) FROM d_result_master rm1 
				WHERE rm1.item_code=p.item_code ' . $rm1_sd . ') > 0 ,
				IF((	SELECT count(rm1.item_code) FROM d_result_master rm1 
						WHERE rm1.item_code=p.item_code ' . $rm1_sd . ' AND rm1.is_finish ="N"
					) > 0, "No", "Yes"
				),
				"No"
		) AS is_confirmed', FALSE);

        $this->db->from('d_certificate_master cm');
        $p_join = 'cm.item_code=p.item_code AND p.d_participant_id=cm.d_participant_id' . (!empty($district) ? ' AND p.district_code=cm.district_code' : '');
        $this->db->join('d_participant_item_details AS p', $p_join);
        $this->db->join('item_master AS i', 'i.item_code = p.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->where('i.fest_id !=', 1);
        if (!empty($district)) {
            $this->db->where('cm.district_code', $district);
        }
        $this->db->where('i.fest_id', $festtype);
        $this->db->where('i.item_type', 'S');
        $this->db->groupBy('p.item_code');
        $this->db->having("is_confirmed = 'Yes'");
        $this->db->orderBy('p.item_code');
        $getdet = $this->db->get();
        return $getdet ? $getdet->getResultArray() : [];
    }

    public function get_captains_details($item_code, $school_code = '')
    {
        $certificate_type_cont = $this->get_certificate_type_condition();
        if ($certificate_type_cont) {
            $this->db->where($certificate_type_cont);
        }
        $district = $this->session->get('DISTRICT');
        if (!empty($district)) {
            $this->db->where('cm.district_code', $district);
            $this->db->where('p.district_code', $district);
        }

        $this->db->select('cm.d_participant_id AS participant_id, pd.participant_name');
        $this->db->from('d_certificate_master cm');
        $this->db->join('d_participant_details AS pd', 'pd.d_participant_id = cm.d_participant_id');
        $this->db->join('d_participant_item_details AS p', 'cm.item_code = p.item_code AND p.d_participant_id = cm.d_participant_id');
        $this->db->where('cm.item_code', $item_code);
        $this->db->where('cm.is_withheld', 'N');
        $this->db->where('p.is_captain', 'Y');

        if (!empty($school_code))
            $this->db->where('cm.school_code', $school_code);
        $this->db->orderBy('cm.d_participant_id', 'ASC');
        $captains = $this->db->get()->getResultArray();
        if (isset($captains) && count($captains) > 0) {
            $captains_array[0] = 'All Participant';
            foreach ($captains as $captain) {
                $captains_array[$captain['participant_id']] = $captain['participant_id'] . ' - ' . $captain['participant_name'];
            }
            return $captains_array;
        }
        return FALSE;
    }

    public function is_group_item($item_code)
    {
        $this->db->select('item_type,max_pinnani');
        $this->db->where('item_code', $item_code);
        $item_details = $this->db->get('item_master')->getResultArray();
        if (is_array($item_details) && count($item_details)) {
            if ($item_details[0]['max_pinnani'] > 0) {
                return 'G';
            }
            return $item_details[0]['item_type'];
        }
        return FALSE;
    }

    public function get_group_participants($item_code, $captain_id, $school_code = '')
    {
        $certificate_type_cont = $this->get_certificate_type_condition();
        if ($certificate_type_cont) {
            $this->db->where($certificate_type_cont);
        }
        $district = $this->session->get('DISTRICT');
        if (!empty($district)) {
            $this->db->where('cm.district_code', $district);
        }

        $this->db->select('cm.d_participant_id AS participant_id, pd.participant_name');
        $this->db->from('d_certificate_master cm');
        $this->db->join('d_participant_details AS pd', 'pd.d_participant_id = cm.d_participant_id');
        $this->db->where('cm.item_code', $item_code);
        if (!empty($captain_id) && 0 != $captain_id) {
            $this->db->where('cm.parent_admn_no =(SELECT parent_admn_no FROM d_certificate_master WHERE d_participant_id=? AND item_code=?' . (!empty($district) ? ' AND district_code=' . (int)$district : '') . ' GROUP BY d_participant_id)', [$captain_id, $item_code]);
        }

        $this->db->where('cm.is_withheld', 'N');
        if (!empty($school_code))
            $this->db->where('cm.school_code', $school_code);
        $this->db->orderBy('cm.d_participant_id', 'ASC');
        $captains = $this->db->get()->getResultArray();
        if (isset($captains) && count($captains) > 0) {
            $participant_select_box = '<select class="form-select form-select-lg rounded-3 shadow-none border-secondary-subtle" name="participant_id" id="participant_id"><option value="0">All Participants</option>';
            foreach ($captains as $captain) {
                $participant_select_box .= '<option value="' . $captain['participant_id'] . '">' . $captain['participant_id'] . ' - ' . $captain['participant_name'] . '</option>';
            }
            $participant_select_box .= '</select>';
            return $participant_select_box;
        }
        return FALSE;
    }

    public function get_participant_details($item_code, $captain_id, $item_type, $participant_id = '')
    {
        $certificate_type_cont = $this->get_certificate_type_condition();
        if ($certificate_type_cont) {
            $this->db->where($certificate_type_cont);
        }
        $district = $this->session->get('DISTRICT');
        if (!empty($district)) {
            $this->db->where('cm.district_code', $district);
        }

        $this->db->select('cm.item_code, cm.d_participant_id AS participant_id, pd.participant_name, sm.school_code, sm.school_name, sd.sub_district_name, 
							rd.rev_district_name, rd.rev_district_name AS district_name,
							i.item_name, f.fest_name, pd.class, pd.gender,cm.rank, pd.admn_no, cm.grade, cm.is_withheld,p.spo_id');
        $this->db->from('d_certificate_master cm');
        $this->db->join('d_participant_details AS pd', 'pd.d_participant_id = cm.d_participant_id');
        $this->db->join('d_school_master AS sm', 'sm.school_code = cm.school_code');
        $this->db->join('sub_district_master AS sd', 'sm.sub_district_code=sd.sub_district_code');
        $this->db->join('rev_district_master AS rd', 'rd.rev_district_code = cm.district_code', 'left');
        if ('S' == $item_type) {
            $this->db->join('d_participant_item_details AS p', 'cm.item_code=p.item_code AND p.d_participant_id=cm.d_participant_id' . (!empty($district) ? ' AND p.district_code=cm.district_code' : ''));
        } else if ('G' == $item_type) {
            $this->db->join('d_participant_item_details AS p', 'cm.item_code=p.item_code AND p.admn_no=cm.parent_admn_no' . (!empty($district) ? ' AND p.district_code=cm.district_code' : ''));
        }

        $this->db->join('item_master AS i', 'i.item_code = p.item_code');
        $this->db->join('festival_master AS f', 'i.fest_id = f.fest_id');
        $this->db->where('i.fest_id !=', 1);
        $this->db->orderBy('FIELD(`cm`.`rank`, 1, 2, 3, 4, 0)');
        $this->db->orderBy('cm.grade', 'ASC');
        $this->db->where('cm.is_withheld', 'N');

        if ('' != $participant_id && '0' != $participant_id && 0 != $participant_id) {
            if (!empty($captain_id) && $captain_id != 'all' && '0' != $captain_id && 0 != $captain_id) {
                $this->db->where('p.d_participant_id', $captain_id);
            }
            $this->db->where('cm.d_participant_id', $participant_id);
        } else {
            if ($captain_id != 'all' && !empty($captain_id) && $captain_id !== 0 && $captain_id !== '0') {
                if ('S' == $item_type) {
                    $this->db->where('cm.d_participant_id', $captain_id);
                } else if ('G' == $item_type) {
                    $this->db->where('p.d_participant_id', $captain_id);
                }
            }
        }
        $this->db->where('cm.item_code', $item_code);
        $participant_details = $this->db->get();
        return $participant_details ? $participant_details->getResultArray() : [];
    }

    public function school_details_certificate_wise()
    {
        $district = $this->session->get('DISTRICT');
        $spd_sd = !empty($district) ? " AND spd.district_code = " . (int)$district : "";
        $rm_sd  = !empty($district) ? " AND rm.district_code = " . (int)$district : "";
        $cm1_sd = !empty($district) ? " AND cm1.district_code = " . (int)$district : "";
        if ($district) {
            $this->db->where('cm.district_code', $district);
            $this->db->where('rm.district_code', $district);
        }
        $this->db->select('sm.school_name, sm.school_code, 
				(SELECT SUM(spd.point) FROM d_school_point_details spd WHERE spd.school_code=cm.school_code ' . $spd_sd . ')  AS total_point, 
				(SELECT COUNT(rm.d_participant_id) FROM d_result_master rm WHERE rm.school_code=sm.school_code ' . $rm_sd . ') as no_participation, 
				
				(SELECT COUNT(cm1.grade) FROM d_certificate_master cm1 WHERE cm1.school_code=cm.school_code AND 
				is_withheld = "N" AND cm1.parent_admn_no=cm1.admn_no AND cm1.grade = "A" ' . $cm1_sd . ') AS a_grade,
				
				(SELECT COUNT(cm1.grade) FROM d_certificate_master cm1 WHERE cm1.school_code=cm.school_code AND is_withheld = "N"  AND cm1.parent_admn_no=cm1.admn_no AND cm1.grade = "B" ' . $cm1_sd . ') AS b_grade,
				(SELECT COUNT(cm1.grade) FROM d_certificate_master cm1 WHERE cm1.school_code=cm.school_code AND is_withheld = "N"  AND cm1.parent_admn_no=cm1.admn_no AND cm1.grade = "C" ' . $cm1_sd . ') AS c_grade', FALSE);
        $this->db->from('d_school_master AS sm');
        $this->db->join('d_certificate_master AS cm', 'cm.school_code=sm.school_code');
        $this->db->join('d_result_master AS rm', 'rm.school_code=cm.school_code');
        $this->db->orderBy('sm.school_code', 'ASC');
        $this->db->groupBy('sm.school_code, sm.school_name, cm.school_code');
        $result = $this->db->get();
        return $result ? $result->getResultArray() : [];
    }

    public function get_school_festival_details($school_code)
    {
        $certificate_type_cont = $this->get_certificate_type_condition();
        if ($certificate_type_cont) {
            $this->db->where($certificate_type_cont);
        }
        $district = $this->session->get('DISTRICT');
        if (!empty($district)) {
            $this->db->where('cm.district_code', $district);
        }
        $this->db->select('fm.fest_id, fm.fest_name');
        $this->db->from('d_certificate_master cm');
        $this->db->join('item_master i', 'i.item_code=cm.item_code');
        $this->db->join('festival_master fm', 'fm.fest_id=i.fest_id');
        $this->db->where('fm.fest_id !=', 1);
        $this->db->groupBy('fm.fest_id, fm.fest_name');
        $this->db->where('cm.is_withheld', 'N');
        $this->db->where('cm.school_code', $school_code);
        $fest_array = $this->db->get()->getResultArray();
        if (is_array($fest_array) && count($fest_array) > 0) {
            $result_array[0] = 'All Festival';
            foreach ($fest_array as $fest) {
                if ((int)$fest['fest_id'] === 1) continue;
                $result_array[$fest['fest_id']] = $fest['fest_name'];
            }
            return $result_array;
        }
        return FALSE;
    }

    public function get_school_item_details($school_code, $fest_id)
    {
        if ((int)$fest_id === 1) {
            return FALSE;
        }
        $certificate_type_cont = $this->get_certificate_type_condition();
        if ($certificate_type_cont) {
            $this->db->where($certificate_type_cont);
        }
        $district = $this->session->get('DISTRICT');
        if (!empty($district)) {
            $this->db->where('cm.district_code', $district);
        }
        $this->db->select('i.item_code, i.item_name');
        $this->db->from('d_certificate_master cm');
        $this->db->join('item_master i', 'i.item_code=cm.item_code');
        $this->db->where('i.fest_id !=', 1);
        if (0 != $fest_id)
            $this->db->where('i.fest_id', $fest_id);
        $this->db->groupBy('i.item_code');
        $this->db->where('cm.is_withheld', 'N');
        $this->db->where('cm.school_code', $school_code);
        $item_array = $this->db->get()->getResultArray();
        $item_select_box = '';
        if (is_array($item_array) && count($item_array) > 0) {
            $item_select_box = '<select class="form-select form-select-lg rounded-3 shadow-none border-secondary-subtle" name="item_code" id="item_code" onChange="javascript:get_school_captains(this.value);"><option value="0">All Items</option>';
            foreach ($item_array as $item) {
                $item_select_box .= '<option value="' . $item['item_code'] . '">' . $item['item_code'] . ' - ' . $item['item_name'] . '</option>';
            }
            $item_select_box .= '</select>';
            return $item_select_box;
        }
        return FALSE;
    }

    public function get_school_participant_details($school_code = '', $festival = '', $item_code = '', $captain_id = '', $participant_id = '')
    {
        if ((int)$festival === 1) {
            return [];
        }
        $certificate_type_cont = $this->get_certificate_type_condition();
        if ($certificate_type_cont) {
            $this->db->where($certificate_type_cont);
        }
        $district = $this->session->get('DISTRICT');
        if (!empty($district)) {
            $this->db->where('cm.district_code', $district);
        }

        $this->db->select('cm.item_code, cm.d_participant_id AS participant_id, pd.participant_name, sm.school_code, sm.school_name, sd.sub_district_name,
							rd.rev_district_name, rd.rev_district_name AS district_name,
							i.item_name, f.fest_name, pd.class, pd.gender, cm.grade,cm.rank, cm.is_withheld, pd.admn_no, 0 AS spo_id');
        $this->db->from('d_certificate_master cm');
        $this->db->join('item_master i', 'i.item_code=cm.item_code');
        $this->db->join('d_school_master sm', 'sm.school_code=cm.school_code');
        $this->db->join('sub_district_master AS sd', 'sm.sub_district_code=sd.sub_district_code');
        $this->db->join('rev_district_master AS rd', 'rd.rev_district_code = cm.district_code', 'left');
        $this->db->join('d_participant_details AS pd', 'pd.d_participant_id = cm.d_participant_id');
        $this->db->join('festival_master f', 'f.fest_id=i.fest_id');
        $this->db->where('f.fest_id !=', 1);
        if (!empty($school_code) && 0 != $school_code) {
            $this->db->where('sm.school_code', $school_code);
        }
        if (!empty($festival) && 0 != $festival) {
            $this->db->where('f.fest_id', $festival);
        }
        if (!empty($item_code) && 0 != $item_code) {
            $this->db->where('cm.item_code', $item_code);
        }
        if (!empty($captain_id) && 0 != $captain_id) {
            $captain_sub_filter = !empty($district) ? " AND district_code=" . (int)$district : "";
            if (!empty($item_code) && 0 != $item_code) {
                $this->db->where('cm.parent_admn_no = (SELECT parent_admn_no FROM d_certificate_master WHERE item_code=' . (int)$item_code . ' AND d_participant_id=' . (int)$captain_id . $captain_sub_filter . ' LIMIT 1)', NULL, FALSE);
            } else {
                $this->db->where('cm.parent_admn_no = (SELECT parent_admn_no FROM d_certificate_master WHERE d_participant_id=' . (int)$captain_id . $captain_sub_filter . ' LIMIT 1)', NULL, FALSE);
            }
        }
        if (!empty($participant_id) && 0 != $participant_id) {
            $this->db->where('cm.d_participant_id', $participant_id);
        }

        $participant_details = $this->db->get();
        return $participant_details->getResultArray();
    }

    public function get_participant_details_with_id($participant_id)
    {
        $certificate_type_cont = $this->get_certificate_type_condition();
        if ($certificate_type_cont) {
            $this->db->where($certificate_type_cont);
        }
        $district = $this->session->get('DISTRICT');
        if (!empty($district)) {
            $this->db->where('cm.district_code', $district);
        }
        $this->db->select('cm.d_participant_id AS participant_id, pd.participant_name, sm.school_code, sm.school_name, pd.admn_no');
        $this->db->from('d_certificate_master cm');
        $this->db->join('d_participant_details pd', 'pd.d_participant_id = cm.d_participant_id');
        $this->db->join('d_school_master sm', 'sm.school_code=cm.school_code');
        $this->db->where('cm.d_participant_id', $participant_id);
        $this->db->groupBy('cm.d_participant_id');
        $participant_details = $this->db->get();
        return $participant_details->getResultArray();
    }

    public function get_participant_item_details($participant_id)
    {
        $certificate_type_cont = $this->get_certificate_type_condition();
        if ($certificate_type_cont) {
            $this->db->where($certificate_type_cont);
        }
        $district = $this->session->get('DISTRICT');
        if (!empty($district)) {
            $this->db->where('cm.district_code', $district);
        }
        $this->db->select('i.item_code, i.item_name');
        $this->db->from('d_certificate_master cm');
        $this->db->join('item_master i', 'i.item_code=cm.item_code');
        $this->db->where('cm.d_participant_id', $participant_id);
        $item_details = $this->db->get()->getResultArray();
        $result_array = array();
        if (is_array($item_details) && count($item_details) > 0) {
            $result_array[0] = 'All Items';
            foreach ($item_details as $item) {
                $result_array[$item['item_code']] = $item['item_name'];
            }
            return $result_array;
        }
        return FALSE;
    }
}
