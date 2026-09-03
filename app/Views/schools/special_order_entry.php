<?php
$participants        = $participants        ?? [];
$orders              = $orders              ?? [];
$bank_details        = $bank_details        ?? [];
$participant_details = $participant_details ?? [];
$school_details      = $school_details      ?? [];
$selected_participant = $selected_participant ?? [];
$school_show         = @$school_show        ?? '';

$sc = @$school_details[0] ?? array();
$hasSchool = (bool) (isset($sc['school_code']) && $sc['school_code'] != '');

if (!empty($selected_participant) && count($selected_participant) > 0) {
    $disblock = 'block';
} else {
    $disblock = 'none';
}
$isNewMode = ($disblock == 'block');

if (!empty($participant_details) && count($participant_details) > 0) {
    $list_show = 'block';
} else {
    $list_show = 'none';
}

$class_array = array();
if (!empty($sc['class_start'])) {
    for ($i = (int) @$sc['class_start']; $i <= (int) @$sc['class_end']; $i++) {
        $class_array[$i] = $i;
    }
    if (count($class_array) === 0) {
        $class_array[''] = 'Class';
    }
} else {
    $class_array[''] = 'Class';
}
?>
<style>
#divEntryForm { font-family: 'Poppins', 'Nunito', system-ui, sans-serif; }
#divEntryForm .soe-hero {
    position: relative; overflow: hidden; display: flex; align-items: center; gap: 20px;
    background: linear-gradient(135deg, #4c1d95 0%, #6d28d9 45%, #4f46e5 100%);
    border-radius: 20px; padding: 26px 30px; color: #fff; margin-bottom: 24px;
    box-shadow: 0 18px 40px -18px rgba(88, 60, 190, .55);
}
#divEntryForm .soe-hero::before,
#divEntryForm .soe-hero::after {
    content: ""; position: absolute; border-radius: 50%; pointer-events: none;
}
#divEntryForm .soe-hero::before { width: 280px; height: 280px; background: rgba(255,255,255,.09); top: -130px; right: -50px; }
#divEntryForm .soe-hero::after  { width: 170px; height: 170px; background: rgba(255,255,255,.07); bottom: -90px; left: 36%; }
#divEntryForm .soe-hero-icon {
    width: 64px; height: 64px; border-radius: 18px; flex: 0 0 auto; font-size: 32px;
    background: rgba(255,255,255,.16); display: flex; align-items: center; justify-content: center;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.25);
}
#divEntryForm .soe-hero-kicker { font-size: 11px; letter-spacing: 3px; text-transform: uppercase; opacity: .8; }
#divEntryForm .soe-hero-title { font-size: 24px; font-weight: 700; margin: 2px 0 2px; }
#divEntryForm .soe-hero-sub { font-size: 13px; opacity: .85; margin: 0; }
#divEntryForm .soe-breadcrumb { margin: 0 0 0 auto; background: rgba(255,255,255,.14); padding: 7px 16px; border-radius: 999px; }
#divEntryForm .soe-breadcrumb a { color: #fff; opacity: .8; text-decoration: none; }
#divEntryForm .soe-breadcrumb .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.55); }
#divEntryForm .soe-breadcrumb .active { color: #fff; }

#divEntryForm .soe-card {
    border: 0; border-radius: 18px; box-shadow: 0 10px 30px -14px rgba(30, 41, 59, .25);
    overflow: hidden; margin-bottom: 22px;
}
#divEntryForm .soe-card-body { padding: 22px 24px; }
#divEntryForm .soe-card-head { display: flex; align-items: center; gap: 14px; padding: 18px 24px; border-bottom: 1px solid #eef1f6; background: #fff; }
#divEntryForm .soe-card-head h6 { margin: 0; font-weight: 700; color: #1e293b; font-size: 15px; }
#divEntryForm .soe-card-head small { color: #94a3b8; font-size: 12px; }
#divEntryForm .soe-tile { width: 44px; height: 44px; border-radius: 13px; display: flex; align-items: center; justify-content: center; font-size: 21px; color: #fff; flex: 0 0 auto; }
#divEntryForm .soe-tile-violet { background: linear-gradient(135deg, #8b5cf6, #6366f1); box-shadow: 0 8px 16px -8px rgba(99,102,241,.7); }
#divEntryForm .soe-tile-amber  { background: linear-gradient(135deg, #f59e0b, #f97316); box-shadow: 0 8px 16px -8px rgba(245,158,11,.7); }
#divEntryForm .soe-tile-green  { background: linear-gradient(135deg, #10b981, #0ea5e9); box-shadow: 0 8px 16px -8px rgba(16,185,129,.7); }

#divEntryForm .soe-lookup {
    position: relative; display: flex; align-items: center;
    border: 2px solid #e2e8f0; border-radius: 14px; background: #f8fafc; transition: border-color .2s, box-shadow .2s;
}
#divEntryForm .soe-lookup:focus-within { border-color: #7c3aed; box-shadow: 0 0 0 4px rgba(124,58,237,.12); background: #fff; }
#divEntryForm .soe-lookup .soe-lookup-icon { padding: 0 4px 0 15px; color: #94a3b8; font-size: 18px; }
#divEntryForm .soe-lookup input {
    border: 0; background: transparent; box-shadow: none; outline: none; font-size: 17px; font-weight: 600;
    letter-spacing: 2px; color: #334155; flex: 1 1 auto; min-width: 0;
}
#divEntryForm .soe-lookup input::placeholder { letter-spacing: .4px; font-weight: 400; color: #b6c2d1; }
#divEntryForm .soe-lookup .soe-fetch-btn {
    border: 0; margin: 5px; border-radius: 11px; padding: 9px 16px; color: #fff; font-weight: 600;
    background: linear-gradient(135deg, #7c3aed, #4f46e5); box-shadow: 0 8px 16px -8px rgba(79,70,229,.8);
    transition: transform .15s, filter .15s;
}
#divEntryForm .soe-lookup .soe-fetch-btn:hover { filter: brightness(1.08); transform: translateY(-1px); }

#divEntryForm .soe-school-info { margin-top: 18px; padding: 18px; border-radius: 14px; background: linear-gradient(135deg, #eef2ff, #f5f3ff); border: 1px solid #e0e7ff; }
#divEntryForm .soe-school-name { font-weight: 700; font-size: 16px; color: #312e81; display: flex; align-items: center; gap: 8px; }
#divEntryForm .soe-chip {
    display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 999px;
    font-size: 12px; font-weight: 600; background: #fff; color: #4338ca; border: 1px solid #c7d2fe;
    box-shadow: 0 2px 6px -2px rgba(67,56,202,.3);
}
#divEntryForm .soe-chip-amber { color: #b45309; border-color: #fde68a; background: #fff7ed; }
#divEntryForm .soe-school-meta { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
#divEntryForm .soe-empty-state { text-align: center; padding: 26px 10px 12px; color: #94a3b8; }
#divEntryForm .soe-empty-state .soe-empty-icon { width: 74px; height: 74px; margin: 0 auto 10px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 32px; color: #cbd5e1; }

#divEntryForm .soe-seg { display: flex; gap: 10px; padding: 6px; background: #f1f5f9; border-radius: 14px; }
#divEntryForm .soe-seg .soe-seg-btn {
    flex: 1 1 0; border: 0; border-radius: 11px; padding: 10px 12px; font-weight: 600; font-size: 13.5px;
    color: #64748b; background: transparent; transition: all .18s;
}
#divEntryForm .soe-seg .soe-seg-btn i { margin-right: 6px; }
#divEntryForm .soe-seg .soe-seg-btn.active { background: #fff; color: #4f46e5; box-shadow: 0 4px 12px -4px rgba(15,23,42,.25); }

#divEntryForm .soe-field { margin-bottom: 14px; }
#divEntryForm .soe-field label { font-size: 12.5px; font-weight: 700; color: #475569; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
#divEntryForm .soe-field label i { color: #7c3aed; }
#divEntryForm .soe-field .form-control,
#divEntryForm .soe-field .form-select,
#divEntryForm .soe-field select.form-control {
    border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 10px 14px; font-size: 14px; color: #1e293b;
    background-color: #fff; transition: border-color .15s, box-shadow .15s;
}
#divEntryForm .soe-field .form-control:focus,
#divEntryForm .soe-field .form-select:focus { border-color: #7c3aed; box-shadow: 0 0 0 4px rgba(124,58,237,.12); outline: 0; }

#divEntryForm .soe-save-btn {
    border: 0; border-radius: 12px; padding: 11px 26px; font-weight: 700; color: #fff; font-size: 14px;
    background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 10px 20px -8px rgba(16,185,129,.7);
    transition: transform .15s, filter .15s;
}
#divEntryForm .soe-save-btn:hover { transform: translateY(-2px); filter: brightness(1.05); }
#divEntryForm .soe-update-btn {
    border: 0; border-radius: 12px; padding: 11px 26px; font-weight: 700; color: #fff; font-size: 14px;
    background: linear-gradient(135deg, #f59e0b, #f97316); box-shadow: 0 10px 20px -8px rgba(249,115,22,.7);
    transition: transform .15s, filter .15s;
}
#divEntryForm .soe-update-btn:hover { transform: translateY(-2px); filter: brightness(1.05); }
#divEntryForm .soe-cancel-btn {
    border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 10px 22px; font-weight: 600; color: #475569; background: #fff;
}
#divEntryForm .soe-cancel-btn:hover { border-color: #fecaca; color: #dc2626; background: #fef2f2; }

/* Table */
#divEntryForm .soe-table { margin: 0; }
#divEntryForm .soe-table thead th {
    background: linear-gradient(135deg, #4f46e5, #6d28d9); color: #fff; font-size: 12px; text-transform: uppercase;
    letter-spacing: .8px; font-weight: 700; padding: 12px 14px; border: 0; white-space: nowrap;
}
#divEntryForm .soe-table thead th:first { border-top-left-radius: 0; }
#divEntryForm .soe-table tbody td { padding: 12px 14px; vertical-align: middle; border-color: #f1f5f9; color: #334155; font-size: 13.5px; }
#divEntryForm .soe-table tbody tr { transition: background .15s; }
#divEntryForm .soe-table tbody tr:hover { background: #f8fafc; }
#divEntryForm .soe-item-pill {
    display: inline-flex; align-items: center; gap: 7px; background: #eef2ff; color: #4338ca; font-weight: 600;
    padding: 5px 12px; border-radius: 10px; font-size: 12.5px; border: 1px solid #e0e7ff;
}
#divEntryForm .soe-order-pill {
    background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; font-weight: 600; padding: 4px 11px; border-radius: 999px; font-size: 12px;
}
#divEntryForm .soe-class-badge, #divEntryForm .soe-gender-badge {
    min-width: 34px; text-align: center; display: inline-block; font-weight: 700; border-radius: 9px;
    padding: 4px 8px; font-size: 12px;
}
#divEntryForm .soe-class-badge { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
#divEntryForm .soe-gender-badge { width: 42px; border-radius: 999px; }
#divEntryForm .soe-gender-badge.g-boy  { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
#divEntryForm .soe-gender-badge.g-girl { background: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8; }
#divEntryForm .soe-avatar {
    width: 34px; height: 34px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; color: #fff; margin-right: 8px; background: linear-gradient(135deg, #8b5cf6, #6366f1);
    box-shadow: 0 4px 8px -4px rgba(99,102,241,.6);
}
#divEntryForm .soe-adno { font-family: 'JetBrains Mono', Consolas, monospace; font-weight: 700; color: #0f172a; background: #f1f5f9; padding: 3px 8px; border-radius: 8px; font-size: 12px; }
#divEntryForm .soe-icon-btn {
    width: 34px; height: 34px; border-radius: 10px; border: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 15px; transition: all .15s;
}
#divEntryForm .soe-icon-btn.btn-edit { background: #e0e7ff; color: #4338ca; }
#divEntryForm .soe-icon-btn.btn-edit:hover { background: #4338ca; color: #fff; }
#divEntryForm .soe-icon-btn.btn-del { background: #fee2e2; color: #dc2626; margin-left: 6px; }
#divEntryForm .soe-icon-btn.btn-del:hover { background: #dc2626; color: #fff; }
#divEntryForm .soe-count-badge {
    background: linear-gradient(135deg, #10b981, #0ea5e9); color: #fff; border-radius: 999px; padding: 3px 12px; font-size: 12px; font-weight: 700;
}
#divEntryForm .soe-toggle-pinnany .form-check-input { cursor: pointer; }
#divEntryForm .soe-toggle-pinnany .form-label { font-size: 13px; font-weight: 600; color: #334155; }
#divEntryForm .soe-collapse-link { font-size: 13px; font-weight: 600; color: #6366f1; cursor: pointer; text-decoration: none; }
#divEntryForm .soe-collapse-link:hover { color: #4f46e5; }
#divEntryForm .soe-note { font-size: 12px; color: #94a3b8; }
@media (max-width: 768px) {
    #divEntryForm .soe-hero { flex-direction: column; align-items: flex-start; text-align: left; }
    #divEntryForm .soe-breadcrumb { margin-left: 0; }
}
</style>

<div id="divEntryForm">
    <!-- ============ HERO / PAGE HEADER ============ -->
    <div class="soe-hero">
        <div class="soe-hero-icon"><i class="bi bi-stars"></i></div>
        <div>
            <div class="soe-hero-kicker">Kalolsavam Schools</div>
            <h1 class="soe-hero-title">Special Order Entry</h1>
            <p class="soe-hero-sub">Register teams &amp; participants under special order competitions</p>
        </div>
        <nav class="ms-auto">
            <ol class="breadcrumb soe-breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
                <li class="breadcrumb-item active">Special Order Entry</li>
            </ol>
        </nav>
    </div>

    <div class="row g-4">
        <!-- ============ LEFT : SCHOOL LOOKUP ============ -->
        <div class="col-xl-5 col-lg-6">
            <div class="card soe-card">
                <div class="soe-card-head">
                    <div class="soe-tile soe-tile-violet"><i class="bi bi-building-check"></i></div>
                    <div>
                        <h6>School Lookup</h6>
                        <small>Enter the school code to begin</small>
                    </div>
                </div>
                <div class="soe-card-body">
                    <?php echo form_open('schools/special_order_entry', array('id' => 'formSchool')); ?>
                    <input type="hidden" name="hidTeachers" id="hidTeachers" value="1">
                    <input type="hidden" name="hidSchoolId" id="hidSchoolIdFormSchool"
                           value="<?php echo @$sc['school_code'] ? esc(@$sc['school_code']) : esc((string) $this->input->post('hidSchoolId')); ?>">

                    <div class="soe-lookup">
                        <span class="soe-lookup-icon"><i class="bi bi-search"></i></span>
                        <?php echo form_input('txtSchoolCode', (string) (@$sc['school_code'] ?? @$this->input->post('hidSchoolId') ?? ''),
                            'id="txtSchoolCode" maxlength="6" autocomplete="off" data-soe-last="' . (string) (@$sc['school_code'] ?? '') . '"
                            onkeypress="javascript:return numbersonly(this, event, false);"
                            onkeydown="javascript:if(event.key === \'Enter\'){event.preventDefault(); fetch_special_order_school_details();}"
                            onblur="javascript:fetch_special_order_school_details()"'); ?>
                        <button type="button" class="soe-fetch-btn" onclick="javascript:fetch_special_order_school_details()">
                            <i class="bi bi-arrow-right-circle-fill me-1"></i>Fetch
                        </button>
                    </div>
                    <?php echo form_close(); ?>

                    <?php if ($hasSchool) { ?>
                        <div class="soe-school-info">
                            <div class="soe-school-name">
                                <i class="bi bi-mortarboard-fill"></i>
                                <span><?php echo esc(@$sc['school_name']); ?></span>
                            </div>
                            <div class="soe-school-meta">
                                <span class="soe-chip"><i class="bi bi-hash"></i><?php echo esc(@$sc['school_code']); ?></span>
                                <?php if (!empty($sc['class_start']) && !empty($sc['class_end'])) { ?>
                                    <span class="soe-chip"><i class="bi bi-journal-text"></i>Class <?php echo (int) $sc['class_start']; ?> – <?php echo (int) $sc['class_end']; ?></span>
                                <?php } ?>
                                <?php if (!empty($sc['school_phone'])) { ?>
                                    <span class="soe-chip soe-chip-amber"><i class="bi bi-telephone-fill"></i><?php echo esc($sc['school_phone']); ?></span>
                                <?php } ?>
                                <?php if (!empty($sc['hm_name'])) { ?>
                                    <span class="soe-chip soe-chip-amber"><i class="bi bi-person-badge-fill"></i>HM: <?php echo esc($sc['hm_name']); ?></span>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="soe-empty-state">
                            <div class="soe-empty-icon"><i class="bi bi-patch-question-fill"></i></div>
                            <div class="fw-semibold text-muted">No school selected yet</div>
                            <small>Type the 6-digit school code above and press Fetch.<br>Participant entry will unlock here.</small>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- ============ RIGHT : PARTICIPANT ENTRY ============ -->
        <div class="col-xl-7 col-lg-6">
            <div class="card soe-card">
                <div class="soe-card-head">
                    <div class="soe-tile soe-tile-amber"><i class="bi bi-person-plus-fill"></i></div>
                    <div>
                        <h6>Register Participant</h6>
                        <small><?php echo $hasSchool ? 'Entry for ' . esc(@$sc['school_name']) : 'Select a school first'; ?></small>
                    </div>
                </div>

                <div class="soe-card-body">
                    <div id="divAddParticipants" style="display:<?php echo (@$school_show == 'show') ? 'block' : 'none'; ?>">
                        <?php echo form_open('schools/special_order_entry/save_participant', array('id' => 'formParticipant')); ?>
                        <input type="hidden" name="hidSchoolId" id="hidSchoolId"
                               value="<?php echo @$sc['school_code'] ? esc(@$sc['school_code']) : esc((string) $this->input->post('hidSchoolId')); ?>">
                        <input type="hidden" name="hidADNO" id="hidADNO" value="">
                        <input type="hidden" name="hidItemId" id="hidItemId" value="">
                        <input type="hidden" name="hidPiId" id="hidPiId" value="" >
                        <input type="hidden" name="chkAddParticipant" id="chkAddParticipant" value="YES" <?php echo $isNewMode ? '' : 'checked'; ?> style="display:none;">

                        <!-- Segmented control : existing vs new -->
                        <div class="soe-seg mb-4">
                            <button type="button" id="soeModeList" class="soe-seg-btn <?php echo $isNewMode ? '' : 'active'; ?>"
                                    onclick="javascript:specialOrderEntryMode('list')">
                                <i class="bi bi-person-lines-fill"></i>Existing Participant
                            </button>
                            <button type="button" id="soeModeNew" class="soe-seg-btn <?php echo $isNewMode ? 'active' : ''; ?>"
                                    onclick="javascript:specialOrderEntryMode('new')">
                                <i class="bi bi-person-plus"></i>New Participant
                            </button>
                        </div>

                        <!-- Existing participant -->
                        <div class="row" id="divExistingParticipant">
                            <div class="col-12 soe-field">
                                <label for="cmbParticipant"><i class="bi bi-list-ul"></i>Participant</label>
                                <?php
                                echo form_dropdown('cmbParticipant', $participants, @$selected_user[0]['rev_district_code'] ?? '',
                                    'id="cmbParticipant" class="form-select" ' . ($isNewMode ? 'disabled' : ''));
                                ?>
                            </div>
                        </div>

                        <!-- New participant details -->
                        <div id="newEntry" style="display:<?php echo $disblock; ?>;">
                            <div class="row g-2">
                                <div class="col-sm-6 soe-field">
                                    <label for="txtADNO"><i class="bi bi-credit-card-2-front-fill"></i>Admission No.</label>
                                    <?php echo form_input('txtADNO', (string) (@$selected_participant[0]['admn_no'] ?? ''),
                                        'class="form-control" id="txtADNO" maxlength="5" autocomplete="off" onkeyup="javascript:this.value=this.value.toUpperCase();"'); ?>
                                </div>
                                <div class="col-sm-6 soe-field">
                                    <label for="txtParticipantName"><i class="bi bi-person-badge"></i>Participant Name</label>
                                    <?php echo form_input('txtParticipantName', (string) (@$selected_participant[0]['participant_name'] ?? ''),
                                        'class="form-control" id="txtParticipantName" autocomplete="off" onkeyup="javascript:this.value=this.value.toUpperCase();"'); ?>
                                </div>
                                <div class="col-sm-6 soe-field">
                                    <label for="txtClass"><i class="bi bi-layers"></i>Class</label>
                                    <?php echo form_dropdown('txtClass', $class_array, @$selected_participant[0]['class'] ?? '', 'id="txtClass" class="form-select"'); ?>
                                </div>
                                <div class="col-sm-6 soe-field">
                                    <label for="txtGender"><i class="bi bi-gender-ambiguous"></i>Gender</label>
                                    <?php echo form_dropdown('txtGender', array('' => 'Gender', 'B' => 'Boy', 'G' => 'Girl'), @$selected_participant[0]['gender'] ?? '', 'id="txtGender" class="form-select"'); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Special order -->
                        <div class="row g-2">
                            <div class="col-md-4 soe-field">
                                <label for="cmbOrder"><i class="bi bi-award-fill"></i>Special Order</label>
                                <?php echo form_dropdown('cmbOrder', $orders, @$selected_participant[0]['spo_id'] ?? '', 'id="cmbOrder" class="form-select"'); ?>
                            </div>
                            <div class="col-md-4 soe-field">
                                <label for="txtItemCode_1"><i class="bi bi-trophy-fill"></i>Item Code</label>
                                <?php echo form_input('txtItemCode_1', (string) (@$selected_participant[0]['item_code'] ?? ''),
                                    'class="form-control" id="txtItemCode_1" maxlength="4" onkeypress="javascript:return numbersonly(this, event, false);"'); ?>
                            </div>
                            <div class="col-md-4 soe-field">
                                <label for="txtCaptionAdNo"><i class="bi bi-people-fill"></i>Captain AD No.</label>
                                <?php echo form_input('txtCaptionAdNo', (string) (@$selected_participant[0]['parent_admn_no'] ?? ''),
                                    'class="form-control" id="txtCaptionAdNo" maxlength="6" onkeyup="javascript:this.value=this.value.toUpperCase();"'); ?>
                            </div>
                            <div class="col-12 d-flex align-items-center justify-content-between">
                                <div class="soe-toggle-pinnany form-check form-switch mb-0">
                                    <?php
                                    $pinnany_checked = (@$selected_participant[0]['item_type'] == 'P');
                                    echo form_checkbox('chkIsPinnany', 'YES', $pinnany_checked, 'id="chkIsPinnany" class="form-check-input"');
                                    ?>
                                    <label class="form-label ms-2 mb-0" for="chkIsPinnany">Is Pinnany</label>
                                </div>
                                <a class="soe-collapse-link" data-bs-toggle="collapse" href="#soeBankCollapse" role="button">
                                    <i class="bi bi-bank me-1"></i>Bank details (optional)
                                </a>
                            </div>
                        </div>

                        <!-- Bank details (optional, collapsed) -->
                        <div class="collapse <?php echo (trim((string) @$selected_participant[0]['account_no']) != '') ? 'show' : ''; ?>" id="soeBankCollapse">
                            <div class="row g-2 mt-1">
                                <div class="col-md-6 soe-field">
                                    <label for="txtBank"><i class="bi bi-bank"></i>Bank Name</label>
                                    <?php echo form_dropdown('txtBank', $bank_details, @$selected_participant[0]['bank_id'] ?? '', 'id="txtBank" class="form-select"'); ?>
                                </div>
                                <div class="col-md-6 soe-field">
                                    <label for="txtBranch"><i class="bi bi-diagram-3"></i>Branch Name</label>
                                    <?php echo form_input('txtBranch', (string) (@$selected_participant[0]['branch_name'] ?? ''), 'class="form-control" id="txtBranch" onkeyup="javascript:this.value=this.value.toUpperCase();"'); ?>
                                </div>
                                <div class="col-md-6 soe-field">
                                    <label for="txtAccNo"><i class="bi bi-credit-card-fill"></i>Account No.</label>
                                    <?php echo form_input('txtAccNo', (string) (@$selected_participant[0]['account_no'] ?? ''),
                                        'class="form-control" id="txtAccNo" onkeypress="javascript:return numbersonly(this, event, false);" onCopy="return false" onDrag="return false" onDrop="return false" onPaste="return false"'); ?>
                                </div>
                                <div class="col-md-6 soe-field">
                                    <label for="txtIfscCode"><i class="bi bi-braces"></i>IFSC Code</label>
                                    <?php echo form_input('txtIfscCode', (string) (@$selected_participant[0]['ifsc_code'] ?? ''),
                                        'class="form-control" id="txtIfscCode" maxlength="11" onkeyup="javascript:this.value=this.value.toUpperCase();"'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="soe-field">
                            <label for="txtRemarks"><i class="bi bi-chat-square-text-fill"></i>Remarks</label>
                            <?php echo form_textarea('txtRemarks', (string) (@$selected_participant[0]['spo_remarks'] ?? ''), 'class="form-control" id="txtRemarks" style="height:90px;"'); ?>
                            <small class="soe-note">Optional note about this registration.</small>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                            <?php if (count(@$selected_participant) > 0) { ?>
                                <button type="button" class="soe-update-btn" id="update_participant"
                                        onclick="javascript:fncUpdateSpecialOrderParticipant('<?php echo esc(@$selected_participant[0]['admn_no']); ?>')">
                                    <i class="bi bi-check2-circle me-1"></i>Update Details
                                </button>
                            <?php } else { ?>
                                <button type="button" class="soe-save-btn" id="save_participant"
                                        onclick="javascript:fncSaveSpecialOrderParticipant()">
                                    <i class="bi bi-check2-circle me-1"></i>Save Details
                                </button>
                            <?php } ?>
                            <button type="button" class="soe-cancel-btn" onclick="javascript:fncCancelSpecialOrderParticipant()">
                                <i class="bi bi-x-lg me-1"></i>Clear / New Entry
                            </button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>

                    <?php if (@$school_show != 'show') { ?>
                        <div class="soe-empty-state">
                            <div class="soe-empty-icon"><i class="bi bi-key-fill"></i></div>
                            <div class="fw-semibold text-muted">Entry locked</div>
                            <small>Fetch a valid school to start registering special order participants.</small>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ REGISTERED LIST ============ -->
    <div class="card soe-card" id="divParticipantList" style="display:<?php echo $list_show; ?>;">
        <div class="soe-card-head">
            <div class="soe-tile soe-tile-green"><i class="bi bi-table"></i></div>
            <div>
                <h6>Registered Special Orders</h6>
                <small>Entries for the selected school</small>
            </div>
            <span class="soe-count-badge ms-auto" id="soeCountBadge"><?php echo count($participant_details); ?> entries</span>
        </div>
        <div class="table-responsive">
            <table class="table soe-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Captain</th>
                        <th>AD No.</th>
                        <th>Reg No.</th>
                        <th>Participant</th>
                        <th class="text-center">Class</th>
                        <th class="text-center">B/G</th>
                        <th>Order</th>
                        <?php if (isset($is_edit) and $is_edit != 'no') { ?>
                            <th class="text-center">Actions</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 1;
                    $prev_item_code = '';
                    $prev_parent_adno = '';
                    for ($j = 0; $j < count($participant_details); $j++) {
                        $item_rowspan = 1;
                        $caption_rowspan = 1;
                        $captain_set = false;
                        if ($prev_parent_adno != $participant_details[$j]['parent_admn_no'] or $prev_item_code != $participant_details[$j]['item_code']) {
                            $caption_rowspan = get_array_double_val_count($participant_details, 'parent_admn_no', $participant_details[$j]['parent_admn_no'], 'item_code', $participant_details[$j]['item_code']);
                            $prev_parent_adno = $participant_details[$j]['parent_admn_no'];
                            $captain_set = true;
                        }
                        $is_captain_row = ($captain_set && $participant_details[$j]['parent_admn_no'] == $participant_details[$j]['admn_no']);
                        $g = strtoupper((string) @$participant_details[$j]['gender']);
                        $name = (string) @$participant_details[$j]['participant_name'];
                        $initial = $name !== '' ? substr($name, 0, 1) : '?';
                        ?>
                        <tr>
                            <?php if ($captain_set) { ?>
                                <td class="fw-bold text-muted" rowspan="<?php echo $caption_rowspan; ?>"><?php echo $count; ?></td>
                                <?php $count++; ?>
                            <?php } ?>
                            <?php if ($prev_item_code != $participant_details[$j]['item_code']) {
                                $item_rowspan = get_array_val_count($participant_details, 'item_code', $participant_details[$j]['item_code']);
                                $prev_item_code = $participant_details[$j]['item_code']; ?>
                                <td rowspan="<?php echo $item_rowspan; ?>">
                                    <span class="soe-item-pill">
                                        <i class="bi bi-trophy-fill"></i>
                                        <?php echo esc(@$participant_details[$j]['item_code']) . ' - ' . esc(@$participant_details[$j]['item_name']); ?>
                                    </span>
                                </td>
                            <?php } ?>
                            <?php if ($captain_set) { ?>
                                <td rowspan="<?php echo $caption_rowspan; ?>">
                                    <span class="soe-adno"><i class="bi bi-crown-fill text-warning me-1"></i><?php echo esc(@$participant_details[$j]['parent_admn_no']); ?></span>
                                </td>
                            <?php } ?>
                            <td><span class="soe-adno"><?php echo esc(@$participant_details[$j]['admn_no']); ?></span></td>
                            <td><span class="soe-adno"><?php echo esc(@$participant_details[$j]['participant_id']); ?></span></td>
                            <td>
                                <span class="soe-avatar"><?php echo esc($initial); ?></span>
                                <?php echo esc($name); ?>
                                <?php if ($participant_details[$j]['is_captain'] == 'Y') { ?>
                                    <span class="soe-order-pill ms-1"><i class="bi bi-person-check-fill"></i> Captain</span>
                                <?php } ?>
                            </td>
                            <td class="text-center"><span class="soe-class-badge"><?php echo esc(@$participant_details[$j]['class']); ?></span></td>
                            <td class="text-center">
                                <span class="soe-gender-badge <?php echo ($g == 'B') ? 'g-boy' : 'g-girl'; ?>"
                                      title="<?php echo ($g == 'B') ? 'Boy' : 'Girl'; ?>">
                                    <i class="bi <?php echo ($g == 'B') ? 'bi-gender-male' : 'bi-gender-female'; ?>"></i>
                                </span>
                            </td>
                            <td><span class="soe-order-pill"><?php echo esc(@$participant_details[$j]['spo_title']); ?></span></td>
                            <?php if (isset($is_edit) and $is_edit != 'no') { ?>
                                <?php if ($captain_set) { ?>
                                    <td class="text-center" rowspan="<?php echo $caption_rowspan; ?>">
                                        <button type="button" class="soe-icon-btn btn-edit" title="Edit entry"
                                                onclick="javascript:editSpecialOrderParticipant('<?php echo esc(@$participant_details[$j]['pi_id']); ?>')">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <button type="button" class="soe-icon-btn btn-del" title="Delete entry"
                                                onclick="javascript:deleteSpecialOrderParticipant('<?php echo esc(@$participant_details[$j]['parent_admn_no']); ?>', '<?php echo esc(@$participant_details[$j]['item_code']); ?>')">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </td>
                                    <?php $captain_set = false; ?>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
// Segment toggle between "existing participant" and "new participant" modes.
function specialOrderEntryMode(mode) {
    var chk   = document.getElementById('chkAddParticipant');
    var cmb   = document.getElementById('cmbParticipant');
    var entry = document.getElementById('newEntry');
    var bList = document.getElementById('soeModeList');
    var bNew  = document.getElementById('soeModeNew');
    if (!chk) return;

    if (mode === 'new') {
        chk.checked = true;
        if (cmb) { cmb.disabled = true; cmb.value = '0'; }
        if (entry) entry.style.display = 'block';
        if (bList) bList.classList.remove('active');
        if (bNew)  bNew.classList.add('active');
    } else {
        chk.checked = false;
        if (cmb) { cmb.disabled = false; cmb.value = '0'; }
        if (entry) entry.style.display = 'none';
        if (bList) bList.classList.add('active');
        if (bNew)  bNew.classList.remove('active');
    }
}

// Dedicated save for special orders (keeps participant form target).
function fncSaveSpecialOrderParticipant() {
    var cmbOrder = document.getElementById('cmbOrder');
    if (!cmbOrder || cmbOrder.value === '0') {
        swal("Oops!", "Please select the order type", "error");
        if (cmbOrder) cmbOrder.focus();
        return false;
    }
    var chk = document.getElementById('chkAddParticipant');
    if (chk && chk.checked === true) {
        var adno = document.getElementById('txtADNO');
        if (!adno || trim(adno.value) === '') {
            swal("Oops!", "Please enter the admission number", "error");
            if (adno) adno.focus();
            return false;
        }
        var nm = document.getElementById('txtParticipantName');
        if (!nm || trim(nm.value) === '') {
            swal("Oops!", "Please enter the participant name", "error");
            if (nm) nm.focus();
            return false;
        }
    } else {
        var participant = document.getElementById('cmbParticipant');
        if (!participant || participant.value === '0') {
            swal("Oops!", "Please select the participant", "error");
            if (participant) participant.focus();
            return false;
        }
    }
    var item = document.getElementById('txtItemCode_1');
    if (!item || trim(item.value) === '') {
        swal("Oops!", "Please enter the item code", "error");
        if (item) item.focus();
        return false;
    }
    showGlobalLoader('Saving entry...');
    document.getElementById('formParticipant').submit();
}

// Dedicated update for special orders (posts to the special-order endpoint).
function fncUpdateSpecialOrderParticipant(adno) {
    var adnoEl = document.getElementById('txtADNO');
    if (!adnoEl || trim(adnoEl.value) === '') {
        swal("Oops!", "Please enter the admission number", "error");
        if (adnoEl) adnoEl.focus();
        return false;
    }
    var cmbOrder = document.getElementById('cmbOrder');
    if (!cmbOrder || cmbOrder.value === '0') {
        swal("Oops!", "Please select the order type", "error");
        if (cmbOrder) cmbOrder.focus();
        return false;
    }
    var item = document.getElementById('txtItemCode_1');
    if (!item || trim(item.value) === '') {
        swal("Oops!", "Please enter the item code", "error");
        if (item) item.focus();
        return false;
    }
    var hidADNO = document.getElementById('hidADNO');
    if (hidADNO) hidADNO.value = adno;
    showGlobalLoader('Updating entry...');
    var form = document.getElementById('formParticipant');
    form.action = path + 'schools/special_order_entry/update_participant_details';
    form.submit();
}

// Keep keyboard Enter on school code from submitting the surrounding form.
if (document.addEventListener) {
    document.addEventListener('DOMContentLoaded', function () {
        var modeBtn = document.getElementById('soeModeNew');
        var chk = document.getElementById('chkAddParticipant');
        if (modeBtn && chk && chk.checked === true) {
            specialOrderEntryMode('new');
        }
    });
}
</script>