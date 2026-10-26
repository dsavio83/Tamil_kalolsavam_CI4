<?php

namespace App\Controllers\Item;

class Item_Master extends \App\Controllers\BaseController {

    function __construct () {
        parent::__construct ();
        $this->template->add_js('js/item/item_master.js');
        $this->load->model('Item/Item_Master_Model');
        $this->load->model('General_Model');
        $this->load->model('Session_Model');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->Session_Model->is_user_logged();
        if (!in_array($this->session->get('USER_GROUP'), array('A', 'W')) &&
            !in_array($this->session->get('USER_TYPE'), array(0, 1, 2, 3))) {
            header("Location: " . base_url() . 'welcome/');
            exit;
        }
    }

    private function getCurrentUserInfo() {
        $user_type = (int)$this->session->get('USER_TYPE');
        $user_id = (int)$this->session->get('USERID');
        $user_name = (string)($this->session->get('USER_NAME') ?: $this->session->get('USER'));
        $district_code = (int)$this->session->get('DISTRICT');
        $sub_district_code = (int)$this->session->get('SUB_DISTRICT');

        $db = \Config\Database::connect();

        $district_name = '';
        if ($district_code > 0) {
            $dRow = $db->table('rev_district_master')->select('rev_district_name')->where('rev_district_code', $district_code)->where('status', 'Y')->get()->getRowArray();
            $district_name = $dRow['rev_district_name'] ?? '';
        }

        $sub_district_name = '';
        if ($sub_district_code > 0) {
            $sdRow = $db->table('sub_district_master')->select('sub_district_name, rev_district_code')->where('sub_district_code', $sub_district_code)->get()->getRowArray();
            $sub_district_name = $sdRow['sub_district_name'] ?? '';
            if (empty($district_name) && !empty($sdRow['rev_district_code'])) {
                $dRow = $db->table('rev_district_master')->select('rev_district_name')->where('rev_district_code', $sdRow['rev_district_code'])->where('status', 'Y')->get()->getRowArray();
                $district_name = $dRow['rev_district_name'] ?? '';
            }
        }

        $user_full_name = '';
        if ($user_id > 0) {
            $uRow = $db->table('user_master')->select('name')->where('user_id', $user_id)->get()->getRowArray();
            $user_full_name = $uRow['name'] ?? '';
        }

        // Role title
        switch ($user_type) {
            case 0:
                $role_title = 'IT Administrator (Super Admin)';
                $role_badge_class = 'bg-danger-subtle text-danger border-danger-subtle';
                break;
            case 1:
                $role_title = 'State Administrator';
                $role_badge_class = 'bg-primary-subtle text-primary border-primary-subtle';
                break;
            case 2:
                $role_title = 'District Administrator (' . ($district_name ?: "Dist #{$district_code}") . ')';
                $role_badge_class = 'bg-info-subtle text-info border-info-subtle';
                break;
            case 3:
                $sdTitle = $sub_district_name ?: "Sub-Dist #{$sub_district_code}";
                if ($district_name) $sdTitle .= ", {$district_name}";
                $role_title = 'Sub-District Administrator (' . $sdTitle . ')';
                $role_badge_class = 'bg-success-subtle text-success border-success-subtle';
                break;
            default:
                $role_title = 'Administrator';
                $role_badge_class = 'bg-secondary-subtle text-secondary border-secondary-subtle';
                break;
        }

        return [
            'user_id'            => $user_id,
            'user_type'          => $user_type,
            'user_name'          => $user_name,
            'user_full_name'     => $user_full_name,
            'district_code'      => $district_code,
            'district_name'      => $district_name,
            'sub_district_code'  => $sub_district_code,
            'sub_district_name'  => $sub_district_name,
            'role_title'         => $role_title,
            'role_badge_class'   => $role_badge_class,
        ];
    }

    /**
     * Central authorization guard for Item Master operations.
     *
     * Policy (full scoped write / legacy parity):
     *  - USER_TYPE 0 (IT Admin) and 1 (State Admin): full access to ALL items.
     *  - USER_TYPE 2 (District Admin): may access Common items OR items allotted
     *    to any sub-district within their own revenue district.
     *  - USER_TYPE 3 (Sub-District Admin): may access Common items OR items
     *    allotted to their own sub-district only.
     *  - USER_TYPE 5 (School) and unknown: denied.
     *
     * The user's scope is always derived from the authenticated session, never
     * from client-supplied parameters.
     *
     * @param array|null  $itemRow    Server-side loaded target item (or null).
     * @param string      $operation  read|edit|delete|allotment_read|allotment_write
     * @return bool
     */
    private function authorizeItemAccess(?array $itemRow, string $operation): bool {
        if (empty($itemRow)) {
            return false;
        }
        $ui = $this->getCurrentUserInfo();
        $ut = (int)$ui['user_type'];

        // LP (festival category ID 1) is not available to district admins.
        if ($ut === 2 && (int)($itemRow['fest_id'] ?? 0) === 1) {
            return false;
        }

        // IT Admin and State Admin hold full read/write privilege on every item.
        if ($ut === 0 || $ut === 1) {
            return true;
        }

        // School users are never granted Item Management operations.
        if ($ut === 5) {
            return false;
        }

        // Common items are readable and mutable by district/sub-district admins
        // (full scoped write / legacy parity).
        if (($itemRow['is_common'] ?? 'Y') === 'Y') {
            return true;
        }

        $itemCode = $itemRow['item_code'] ?? 0;

        // District Admin: item must be allotted to their own district.
        if ($ut === 2) {
            return $this->Item_Master_Model->item_is_allotted_to_district($itemCode, (int)$ui['district_code']);
        }

        // Sub-District Admin: item must be allotted to their own sub-district.
        if ($ut === 3) {
            return $this->Item_Master_Model->item_is_allotted_to_sub_district($itemCode, (int)$ui['sub_district_code']);
        }

        return false;
    }

    /**
     * Confirm every submitted sub-district code falls within the caller's
     * own write scope. Used by add/allotment-write to prevent a district or
     * sub-district admin from assigning an item to an unauthorized territory.
     *
     * @param array|null $subDistrictCodes
     * @return bool
     */
    private function subDistrictCodesInUserScope(?array $subDistrictCodes): bool {
        $ui = $this->getCurrentUserInfo();
        $ut = (int)$ui['user_type'];

        // IT Admin / State Admin may assign any sub-district.
        if ($ut === 0 || $ut === 1) {
            return true;
        }
        if (empty($subDistrictCodes)) {
            return true;
        }

        $codes = array_values(array_filter(array_map('intval', (array)$subDistrictCodes)));
        if ($ut === 2) {
            $district = (int)$ui['district_code'];
            foreach ($codes as $code) {
                if (!$this->Item_Master_Model->subdistrict_belongs_to_district($code, $district)) {
                    return false;
                }
            }
            return true;
        }
        if ($ut === 3) {
            $ownSd = (int)$ui['sub_district_code'];
            foreach ($codes as $code) {
                if ($code !== $ownSd) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Set an error message and re-render the scoped list WITHOUT exposing the
     * unauthorized target item. Used by HTML form endpoints.
     *
     * @param string $message
     */
    private function denyItemAccess(string $message = 'You are not authorized to perform this action on the selected item.') {
        $this->template->write('error', $message);
        $this->Content['selected_item'] = array();
        $this->Content['show_edit_item'] = '';
        $this->index();
    }

    function index() {
        if (!isset($this->Content) || !is_array($this->Content)) {
            $this->Content = array();
        }
        if (!isset($this->Content['selected_item'])) {
            $this->Content['selected_item'] = array();
        }

        $userInfo = $this->getCurrentUserInfo();
        $this->Content['current_user_info'] = $userInfo;

        // Fetch items filtered by the user's role and jurisdiction
        $this->Content['retvalue'] = $this->Item_Master_Model->get_item_details(
            '',
            $userInfo['user_type'],
            $userInfo['district_code'],
            $userInfo['sub_district_code']
        );

        $this->Content['festivals'] = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id, fest_name', '', '-- Select Festival --', 'fest_name');
        $this->Content['vibhagams'] = $this->General_Model->prepare_select_box_data('vibhagam_master', 'vibhagam_id, vibhagam_name', '', '-- Select Vibhagam --', 'vibhagam_name');
        $this->Content['all_vibhagams'] = $this->Item_Master_Model->get_all_vibhagams_with_festival();
        $this->Content['districts_with_subdistricts'] = $this->Item_Master_Model->get_all_districts_with_subdistricts();

        if ((int)$userInfo['user_type'] === 2) {
            $this->Content['all_vibhagams'] = array_values(array_filter(
                $this->Content['all_vibhagams'],
                static function ($vibhagam) {
                    return (int)($vibhagam['fest_id'] ?? 0) !== 1;
                }
            ));
            $this->Content['districts_with_subdistricts'] = array_values(array_filter(
                $this->Content['districts_with_subdistricts'],
                static function ($district) use ($userInfo) {
                    return (int)($district['rev_district_code'] ?? 0) === (int)$userInfo['district_code'];
                }
            ));
        } elseif ((int)$userInfo['user_type'] === 3) {
            $ownSubDistrict = (int)$userInfo['sub_district_code'];
            $this->Content['districts_with_subdistricts'] = array_values(array_filter(
                array_map(static function ($district) use ($ownSubDistrict) {
                    $district['sub_districts'] = array_values(array_filter(
                        $district['sub_districts'] ?? [],
                        static function ($subDistrict) use ($ownSubDistrict) {
                            return (int)($subDistrict['sub_district_code'] ?? 0) === $ownSubDistrict;
                        }
                    ));
                    return $district;
                }, $this->Content['districts_with_subdistricts']),
                static function ($district) {
                    return !empty($district['sub_districts']);
                }
            ));
        }

        $this->template->write_view('content', 'item/item_master', $this->Content);
        $this->template->load();
    }

    function add_item_details() {
        if ($this->Item_Master_Model->check_item_code_exists('', $this->input->post('item_code'))) {
            $this->template->write('error', 'Item Code already exists');
            $this->index();
            return;
        }

        $is_common    = ($this->input->post('is_common') === 'N') ? 'N' : 'Y';
        $subdistricts = (array)$this->input->post('sub_districts');

        // Write-scope enforcement: a district/sub-district admin may only assign
        // a new specific item to sub-districts within their own jurisdiction.
        // Common items remain creatable by any Item Management administrator
        // (full scoped write / legacy parity). The caller's scope comes from the
        // authenticated session, never from client-supplied values.
        if ($is_common === 'N' && !$this->subDistrictCodesInUserScope($subdistricts)) {
            $this->denyItemAccess();
            return;
        }

        $userId = (int)$this->session->get('USERID') ?: 1;

        $data['item_code']        = trim($this->input->post('item_code'));
        $data['vibhagam_id']      = trim($this->input->post('vibhagam_id'));
        $data['fest_id']          = trim($this->input->post('fest_id'));
        $data['item_name']        = trim($this->input->post('item_name'));
        $data['gender']           = trim($this->input->post('gender'));
        $data['item_type']        = trim($this->input->post('item_type'));
        $data['max_time']         = (int) $this->input->post('max_time');
        $data['time_type']        = trim($this->input->post('time_type'));
        $data['max_participants'] = (int) $this->input->post('max_participants');
        $data['max_pinnani']      = (int) $this->input->post('max_pinnani');
        $data['is_off_stage']     = trim($this->input->post('is_off_stage'));
        $data['is_common']        = $is_common;
        $data['value_points']     = trim((string)$this->input->post('value_points'));
        $data['created_by']       = $userId;
        $data['created_at']       = date('Y-m-d H:i:s');

        if ((int)$this->session->get('USER_TYPE') === 2 && (int)$data['fest_id'] === 1) {
            $this->denyItemAccess('LP Kalaivizha is not available for district administrators.');
            return;
        }

        // Validation: Ensure the chosen Vibhagam belongs to the chosen Festival Category
        if (!$this->Item_Master_Model->is_valid_festival_vibhagam((int)$data['fest_id'], (int)$data['vibhagam_id'])) {
            $this->template->write('error', 'Selected Vibhagam does not belong to the chosen Festival Category');
            $this->index();
            return;
        }

        $insertedId = $this->Item_Master_Model->save_item_details($data);
        if ($insertedId) {
            // Save allotments if specific item (already validated to be within user scope)
            if ($is_common === 'N') {
                $this->Item_Master_Model->save_item_allotments($data['item_code'], $subdistricts, $userId);
            }

            // Handle optional value points image upload
            $vpUploaded = false;
            $vpFile = $this->request->getFile('value_points_image');
            if ($vpFile && $vpFile->isValid() && !$vpFile->hasMoved()) {
                $ext = strtolower($vpFile->getClientExtension());
                if (in_array($ext, ['png', 'jpg', 'jpeg'])) {
                    $targetDir = FCPATH . 'value_points/';
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0777, true);
                    }
                    $vpFile->move($targetDir, $data['item_code'] . '.png', true);
                    $vpUploaded = true;
                }
            }

            if (!$vpUploaded && !empty($data['value_points'])) {
                $this->autoGenerateValuePointsImage($data['item_code'], $data['value_points']);
            }

            $this->template->write('message', 'Item details saved successfully');
        } else {
            $this->template->write('error', 'Failed to Add Item Details');
        }
        $this->index();
    }

    function edit_item_detials() {
        $itemId = $this->input->post('ItemIdty');
        $target = $this->Item_Master_Model->select_item_details($itemId);
        $targetRow = $target[0] ?? null;

        // Load + authorize the target server-side before exposing any detail.
        if (!$this->authorizeItemAccess($targetRow, 'read')) {
            $this->denyItemAccess();
            return;
        }

        $this->Content['selected_item'] = $target;
        $this->Content['show_edit_item'] = '1';
        $this->index();
    }

    function update_item_detials() {
        $itemId = $this->input->post('ItemIdty');

        // Load the target server-side and authorize the WRITE before any mutation.
        $existingRow = $this->Item_Master_Model->select_item_details($itemId);
        $existing = $existingRow[0] ?? null;
        if (!$this->authorizeItemAccess($existing, 'edit')) {
            $this->denyItemAccess();
            return;
        }

        $is_common    = ($this->input->post('is_common') === 'N') ? 'N' : 'Y';
        $subdistricts = (array)$this->input->post('sub_districts');

        // Re-allotment write-scope: a district/sub-district admin may only assign
        // a specific item to sub-districts within their own jurisdiction.
        if ($is_common === 'N' && !$this->subDistrictCodesInUserScope($subdistricts)) {
            $this->denyItemAccess();
            return;
        }

        if ($this->Item_Master_Model->check_item_code_exists($itemId, $this->input->post('item_code'))) {
            $this->template->write('error', 'Item Code already exists');
            $this->index();
            return;
        }

        $userId = (int)$this->session->get('USERID') ?: 1;

        // Capture the existing item_code BEFORE it is overwritten with the new value.
        // Used to clean up allotment rows under the old code in case it changed.
        $old_item_code = !empty($existing['item_code']) ? (int)$existing['item_code'] : null;

        $data['item_code']        = trim($this->input->post('item_code'));
        $data['vibhagam_id']      = trim($this->input->post('vibhagam_id'));
        $data['fest_id']          = trim($this->input->post('fest_id'));
        $data['item_name']        = trim($this->input->post('item_name'));
        $data['gender']           = trim($this->input->post('gender'));
        $data['item_type']        = trim($this->input->post('item_type'));
        $data['max_time']         = (int) $this->input->post('max_time');
        $data['time_type']        = trim($this->input->post('time_type'));
        $data['max_participants'] = (int) $this->input->post('max_participants');
        $data['max_pinnani']      = (int) $this->input->post('max_pinnani');
        $data['is_off_stage']     = trim($this->input->post('is_off_stage'));
        $data['is_common']        = $is_common;
        $data['value_points']     = trim((string)$this->input->post('value_points'));

        if ((int)$this->session->get('USER_TYPE') === 2 && (int)$data['fest_id'] === 1) {
            $this->denyItemAccess('LP Kalaivizha is not available for district administrators.');
            return;
        }

        // Validation: Ensure the chosen Vibhagam belongs to the chosen Festival Category
        if (!$this->Item_Master_Model->is_valid_festival_vibhagam((int)$data['fest_id'], (int)$data['vibhagam_id'])) {
            $this->template->write('error', 'Selected Vibhagam does not belong to the chosen Festival Category');
            $this->index();
            return;
        }

        if ($this->Item_Master_Model->update_item_details($itemId, $data)) {
            if ($old_item_code !== null && ($old_item_code !== (int)$data['item_code'])) {
                // Keep every district's allotments when an authorized district
                // editor changes the code; other admins retain their scope.
                if (in_array((int)$this->session->get('USER_TYPE'), [2, 3], true)) {
                    $this->Item_Master_Model->move_item_allotments($old_item_code, (int)$data['item_code']);
                } else {
                    $this->Item_Master_Model->delete_item_allotments($old_item_code);
                }

                // Rename existing value points image if code changed
                $targetDir = FCPATH . 'value_points/';
                $oldImg = $targetDir . $old_item_code . '.png';
                $newImg = $targetDir . $data['item_code'] . '.png';
                if (is_file($oldImg) && !is_file($newImg)) {
                    @rename($oldImg, $newImg);
                }
            }

            // Handle optional value points image upload
            $vpUploaded = false;
            $vpFile = $this->request->getFile('value_points_image');
            if ($vpFile && $vpFile->isValid() && !$vpFile->hasMoved()) {
                $ext = strtolower($vpFile->getClientExtension());
                if (in_array($ext, ['png', 'jpg', 'jpeg'])) {
                    $targetDir = FCPATH . 'value_points/';
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0777, true);
                    }
                    $vpFile->move($targetDir, $data['item_code'] . '.png', true);
                    $vpUploaded = true;
                }
            }

            if (!$vpUploaded && !empty($data['value_points'])) {
                $this->autoGenerateValuePointsImage($data['item_code'], $data['value_points']);
            }

            if ($is_common === 'N') {
                if ((int)$this->session->get('USER_TYPE') === 2) {
                    $this->Item_Master_Model->save_district_item_allotments(
                        $data['item_code'],
                        $subdistricts,
                        (int)$this->session->get('DISTRICT'),
                        $userId
                    );
                } elseif ((int)$this->session->get('USER_TYPE') === 3) {
                    $this->Item_Master_Model->save_subdistrict_item_allotments(
                        $data['item_code'],
                        $subdistricts,
                        (int)$this->session->get('SUB_DISTRICT'),
                        $userId
                    );
                } else {
                    // Submitted checkbox list is authoritative for state-level editors.
                    $this->Item_Master_Model->save_item_allotments($data['item_code'], $subdistricts, $userId);
                }
            } else {
                if ((int)$this->session->get('USER_TYPE') === 2) {
                    $this->Item_Master_Model->delete_district_item_allotments($data['item_code'], (int)$this->session->get('DISTRICT'));
                } elseif ((int)$this->session->get('USER_TYPE') === 3) {
                    $this->Item_Master_Model->delete_subdistrict_item_allotments($data['item_code'], (int)$this->session->get('SUB_DISTRICT'));
                } else {
                    $this->Item_Master_Model->delete_item_allotments($data['item_code']);
                }
            }
            $this->template->write('message', 'Item details updated successfully');
        } else {
            $this->template->write('error', 'Failed to Update Item Details');
        }
        $this->index();
    }

    function delete_item_detials() {
        $itemId = $this->input->post('ItemIdty');

        // Load + authorize the target server-side before any destructive action.
        $target = $this->Item_Master_Model->select_item_details($itemId);
        $targetRow = $target[0] ?? null;
        if (!$this->authorizeItemAccess($targetRow, 'delete')) {
            $this->denyItemAccess();
            return;
        }

        $this->Item_Master_Model->delete_item_details($itemId);
        $this->template->write('message', 'Item details deleted successfully');
        $this->index();
    }

    /**
     * AJAX endpoint: Get sub-district allotment codes for an item
     */
    function get_item_allotment_details() {
        $item_code = $this->input->get_post('item_code');
        if (empty($item_code)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Missing item_code']);
        }

        // Resolve the target item server-side and authorize the allotment read.
        $targetRow = $this->Item_Master_Model->select_item_by_code($item_code);
        if (!$this->authorizeItemAccess($targetRow, 'allotment_read')) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Not authorized to view allotment details for this item.',
            ]);
        }

        $allotted = $this->Item_Master_Model->get_item_allotted_subdistrict_codes($item_code);
        return $this->response->setJSON([
            'status'                 => 'success',
            'item_code'              => (int)$item_code,
            'allotted_sub_districts' => $allotted,
        ]);
    }

    /**
     * Render the 3-column value points table image with TAU-Maduram title and TAU-Paalai rows
     */
    private function autoGenerateValuePointsImage(string $itemCode, string $valuePointsText) {
        $text = trim($valuePointsText);
        if ($text === '') return;

        $script = FCPATH . 'scripts/render_vp_table.js';
        if (!is_file($script)) return;

        $lines = preg_split('/\r\n|\r|\n/', $text);
        $titleLines = [];
        $rows = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            if (preg_match('/^(\d+)\s*[\)\.]\s*(?:[\-\–]\s*)?(.*)$/u', $line, $m)) {
                $num = $m[1];
                $rest = trim($m[2]);
                if (preg_match('/^(.*?)\s*[\-\–]\s*([^\-\–]+)$/u', $rest, $rm)) {
                    $desc = trim($rm[1]);
                    $marks = trim($rm[2]);
                } else {
                    $desc = $rest;
                    $marks = '';
                }
                $rows[] = ['no' => $num, 'desc' => $desc, 'marks' => $marks];
            } else {
                $titleLines[] = $line;
            }
        }

        $itemPayload = json_encode([
            'item_code' => $itemCode,
            'title'     => implode('<br>', $titleLines),
            'rows'      => $rows
        ], JSON_UNESCAPED_UNICODE);

        @shell_exec('node ' . escapeshellarg($script) . ' --inline-json ' . escapeshellarg($itemPayload));
    }

}
