function getEl(id) {
    return document.getElementById(id) || (typeof $ === 'function' ? $(id) : null);
}

function getItemCertificate(itemcode) {
    var hid = getEl('hidItemId');
    var form = getEl('formIWPq');
    if (hid && form) {
        hid.value = itemcode;
        form.action = path + 'certificate/certificate/get_certificate_itemwise';
        form.submit();
    }
}

function getPinnaniParticipant(element_value) {
    var target = getEl('all_participant_id');
    var hidItem = getEl('hidItemId');
    if (typeof ajax_loder2 === 'function') ajax_loder2('all_participant_id');

    var postData = 'item_code=' + encodeURIComponent(hidItem ? hidItem.value : '') + 
                   '&captain_id=' + encodeURIComponent(element_value);

    fetch(path + 'ajax/loadajax/get_all_group_participants', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: postData
    })
    .then(function(res) { return res.text(); })
    .then(function(html) {
        if (target) target.innerHTML = html;
    })
    .catch(function(err) {
        if (typeof swal === 'function') {
            swal('Oops!', 'An error occurred while loading participants', 'error');
        }
    });
}

function getSchoolWiseCertificate(schoolCode) {
    var hid = getEl('hidSchoolCode');
    var form = getEl('formIWPq');
    if (hid && form) {
        hid.value = schoolCode;
        form.action = path + 'certificate/certificate/get_certificate_school_wise';
        form.submit();
    }
}

function get_school_items(element_value) {
    var hidSchool = getEl('hidSchoolCode');
    var itemBox = getEl('all_item_code');
    var cptBox = getEl('all_captain_id');
    var grpBox = getEl('all_group_participant_id');

    if (itemBox) itemBox.innerHTML = '<select class="form-select form-select-lg rounded-3 shadow-none border-secondary-subtle" name="item_code" id="item_code"><option value="0">All Items</option></select>';
    if (cptBox) cptBox.innerHTML = '<select class="form-select form-select-lg rounded-3 shadow-none border-secondary-subtle" name="captain_id" id="captain_id"><option value="0">All Participants</option></select>';
    if (grpBox) grpBox.innerHTML = '<select class="form-select form-select-lg rounded-3 shadow-none border-secondary-subtle" name="participant_id" id="participant_id"><option value="0">All Participants</option></select>';

    if (!element_value || element_value === '0') return;

    if (typeof ajax_loder2 === 'function') ajax_loder2('all_item_code');

    var postData = 'school_code=' + encodeURIComponent(hidSchool ? hidSchool.value : '') + 
                   '&fest_id=' + encodeURIComponent(element_value);

    fetch(path + 'ajax/loadajax/get_school_items', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: postData
    })
    .then(function(res) { return res.text(); })
    .then(function(html) {
        if (itemBox) itemBox.innerHTML = html;
    })
    .catch(function() {
        if (typeof swal === 'function') swal('Oops!', 'Unable to load school items', 'error');
    });
}

function get_school_captains(element_value) {
    var hidSchool = getEl('hidSchoolCode');
    var cptBox = getEl('all_captain_id');
    var grpBox = getEl('all_group_participant_id');

    if (cptBox) cptBox.innerHTML = '<select class="form-select form-select-lg rounded-3 shadow-none border-secondary-subtle" name="captain_id" id="captain_id"><option value="0">All Participants</option></select>';
    if (grpBox) grpBox.innerHTML = '<select class="form-select form-select-lg rounded-3 shadow-none border-secondary-subtle" name="participant_id" id="participant_id"><option value="0">All Participants</option></select>';

    if (!element_value || element_value === '0') return;

    if (typeof ajax_loder2 === 'function') ajax_loder2('all_captain_id');

    var postData = 'school_code=' + encodeURIComponent(hidSchool ? hidSchool.value : '') + 
                   '&item_code=' + encodeURIComponent(element_value);

    fetch(path + 'ajax/loadajax/get_school_captains', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: postData
    })
    .then(function(res) { return res.text(); })
    .then(function(html) {
        if (cptBox) cptBox.innerHTML = html;
    })
    .catch(function() {
        if (typeof swal === 'function') swal('Oops!', 'Unable to load school captains', 'error');
    });
}

function get_school_group_participants(element_value) {
    var hidSchool = getEl('hidSchoolCode');
    var itemInput = getEl('item_code');
    var grpBox = getEl('all_group_participant_id');

    if (!element_value || element_value === '0') {
        if (grpBox) grpBox.innerHTML = '<select class="form-select form-select-lg rounded-3 shadow-none border-secondary-subtle" name="participant_id" id="participant_id"><option value="0">All Participants</option></select>';
        return;
    }

    if (typeof ajax_loder2 === 'function') ajax_loder2('all_group_participant_id');

    var postData = 'school_code=' + encodeURIComponent(hidSchool ? hidSchool.value : '') + 
                   '&item_code=' + encodeURIComponent(itemInput ? itemInput.value : '') + 
                   '&captain_id=' + encodeURIComponent(element_value);

    fetch(path + 'ajax/loadajax/get_all_group_participants', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: postData
    })
    .then(function(res) { return res.text(); })
    .then(function(html) {
        if (grpBox) grpBox.innerHTML = html;
    })
    .catch(function() {
        if (typeof swal === 'function') swal('Oops!', 'Unable to load group participants', 'error');
    });
}

function fetch_participant_details_result() {
    var pInput = getEl('txtParticipantId');
    if (!pInput || !pInput.value.trim()) {
        return false;
    }
    var participant_id = pInput.value.trim();
    var contentEl = getEl('content_id');

    if (contentEl) {
        contentEl.style.transition = 'opacity 0.2s';
        contentEl.style.opacity = '0.5';
    }

    var postData = 'participant_id=' + encodeURIComponent(participant_id);

    fetch(path + 'ajax/loadajax/get_participant_details', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: postData
    })
    .then(function(res) { return res.text(); })
    .then(function(html) {
        if (contentEl) {
            contentEl.innerHTML = html;
            contentEl.style.opacity = '1';
            // Re-focus or position cursor in txtParticipantId if still present
            var newPInput = getEl('txtParticipantId');
            if (newPInput) {
                newPInput.focus();
                var len = newPInput.value.length;
                newPInput.setSelectionRange(len, len);
            }
        }
    })
    .catch(function() {
        if (contentEl) contentEl.style.opacity = '1';
        if (typeof swal === 'function') {
            swal('Oops!', 'Unable to search participant details', 'error');
        }
    });
}
