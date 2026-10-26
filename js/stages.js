// JavaScript Document

function clicktoviewfestdet(festid)
{
    $('hidfestid').value = festid;
    swal("Good job!", "You clicked the button!", "error");
    $('festdet').action = path + 'publishresult/resultindex/festival_allitem';
    $('festdet').submit();
}
function finisheditemdet(festid)
{
    $('hidfestid').value = festid;
    $('festdet').action = path + 'publishresult/resultindex/finished_item';
    $('festdet').submit();
}
function remainderitemdet(festid)
{
    $('hidfestid').value = festid;
    $('festdet').action = path + 'publishresult/resultindex/incomplete_item';
    $('festdet').submit();
}
function gradewise()
{
    if ($('txtSchoolCode').value == "")
    {
        swal("Oops!", "Enter School Code", "error");
        $('txtSchoolCode').focus();
        return false;
    }
}
function checkitemwise()
{
    if ($('cmbFestType').value == 0)
    {
        swal("Oops!", "Select a Festival from list", "error");
        $('cmbFestType').focus();
        return false;
    }
    if ($('cbo_item').value == 0)
    {
        swal("Oops!", "select a item from list", "error");
        $('cbo_item').focus();
        return false;
    }
}
function rankwise()
{
    if ($('cmbFestType').value == 0)
    {
        swal("Oops!", "Select a Festival from list", "error");
        $('cmbFestType').focus();
        return false;
    }
}
function fetch_item_details()
{
    if ($('txtItemCode').value.length >= 3) {
        var item_code = $('txtItemCode').value;
        ajax_loder2('divSchoolCode');
        var oOptions = {
            method: "post",
            parameters: {'code': item_code},
            onFailure: function (oXHR, oJson) {
            },
            onSuccess: function (transport) {
                var response = transport.responseText;
                $('divEntryForm').innerHTML = response;
            }
        };
        var oRequest = new Ajax.Updater({
        }, path + "stage/allotment/get_item_details", oOptions);
    } else {
        return false;
    }
}

function clearText(fieldId, value) {
    if ($(fieldId).value == value) {
        $(fieldId).value = '';
    } else if ($(fieldId).value == '') {
        $(fieldId).value = value;
    }
}

function fncCheckAllotmentDeatils(update)
{
    if ($('cmbStage').value == '0')
    {
        swal("Oops!", "Please select stage", "error");
        $('cmbStage').focus();
        return false;
    }
    if ($('txtDateTime')) {
        var dtVal = $('txtDateTime').value.trim();
        if (!dtVal) {
            swal("Oops!", "Please select Start Time", "error");
            $('txtDateTime').focus();
            return false;
        }
        var parts = dtVal.split('T');
        if (parts.length === 2) {
            if ($('txtDate')) $('txtDate').value = parts[0];
            var timeParts = parts[1].split(':');
            if ($('txtHour')) $('txtHour').value = timeParts[0] || '00';
            if ($('txtMin')) $('txtMin').value = timeParts[1] || '00';
        }
    } else {
        if ($('txtDate').value == '')
        {
            swal("Oops!", "Please select date", "error");
            $('txtDate').focus();
            return false;
        }
        if ($('txtHour').value == '' || $('txtHour').value == 'HH')
        {
            swal("Oops!", "Please enter hour", "error");
            $('txtHour').focus();
            return false;
        }
        if ($('txtMin').value == '' || $('txtMin').value == 'MM')
        {
            swal("Oops!", "Please enter minute", "error");
            $('txtMin').focus();
            return false;
        }
    }
    if ($('cmbNoOfCluster').value == '0')
    {
        swal("Oops!", "Please select number of clusters", "error");
        $('cmbNoOfCluster').focus();
        return false;
    }
    if ($('cmbNoOfJudges').value == '0')
    {
        swal("Oops!", "Please select number of judges", "error");
        $('cmbNoOfJudges').focus();
        return false;
    }
    if (update == 1) {
        $('formAllotment').action = path + 'stage/allotment/update_allotment';
        showFormSpinner('submit_button', 'Updating...');
    } else {
        showFormSpinner('submit_button', 'Saving...');
    }
    $('formAllotment').submit();
}

function fncUpdateCluster()
{
    showFormSpinner('update_button', 'Updating...');
    $('formUpdateClusterParticipant').submit();
}

function getitemstagedet(itemcode)
{
    $('txtItemCode').value = itemcode;
    $('formIWPq').action = path + 'stage/allotment';
    $('formIWPq').submit();
}

function clickresultdeclered(festid)
{
    $('hidfestId').value = festid;
    $('festrep').action = path + 'publishresult/publicresult/result_declared';
    $('festrep').submit();
}
function cickpointdeclared(festid)
{
    $('hidfestId').value = festid;
    $('festrep').action = path + 'publishresult/publicresult/schoolpoints';
    $('festrep').submit();
}

function clicktoviewfestdet_public(festid)
{
    $('hidfestid').value = festid;
    $('festdet').action = path + 'publishresult/publicresult/festival_allitem';
    $('festdet').submit();
}

function finisheditemdet_public(festid)
{
    $('hidfestid').value = festid;
    $('festdet').action = path + 'publishresult/publicresult/finished_item';
    $('festdet').submit();
}

function remainderitemdet_public(festid)
{
    $('hidfestid').value = festid;
    $('festdet').action = path + 'publishresult/publicresult/incomplete_item';
    $('festdet').submit();
}

/* ══════════════════════════════════════════════
   BUTTON SPINNER HELPER
   Shows a Bootstrap spinner inside a submit button,
   disables it and lets the form submit normally.
   ══════════════════════════════════════════════ */

function showFormSpinner(btn, label) {
    if (typeof btn === 'string') {
        btn = document.getElementById(btn);
    }
    if (!btn) {
        return true;
    }
    if (label === undefined) {
        label = btn.value || btn.textContent || 'Please wait';
    }
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + label;
    btn.style.pointerEvents = 'none';
    setTimeout(function() {
        btn.disabled = true;
    }, 50);
    return true;
}

/* ══════════════════════════════════════════════
   STAGE MODAL & INTERACTIVITY — Add / Edit / Delete / View Toggle / Live Search
   ══════════════════════════════════════════════ */

var sdStageModal = null;
var sdActiveFilter = 'all';

function sdGetModal() {
    if (!sdStageModal) {
        var el = document.getElementById('sdStageModal');
        if (el) sdStageModal = new bootstrap.Modal(el);
    }
    return sdStageModal;
}

function sdSwitchView(mode) {
    var gridView = document.getElementById('sdGridView');
    var tableView = document.getElementById('sdTableView');
    var btnGrid = document.getElementById('sdBtnGridView');
    var btnTable = document.getElementById('sdBtnTableView');

    if (!gridView || !tableView) return;

    if (mode === 'table') {
        gridView.style.display = 'none';
        tableView.style.display = 'block';
        if (btnGrid) btnGrid.classList.remove('active');
        if (btnTable) btnTable.classList.add('active');
    } else {
        gridView.style.display = 'grid';
        tableView.style.display = 'none';
        if (btnGrid) btnGrid.classList.add('active');
        if (btnTable) btnTable.classList.remove('active');
    }
    try {
        localStorage.setItem('tk_stage_view_mode', mode);
    } catch(e) {}
}

function sdSetFilter(filterType, btnEl) {
    sdActiveFilter = filterType;
    var pills = document.querySelectorAll('.sd-filter-btn');
    pills.forEach(function(p) { p.classList.remove('active'); });
    if (btnEl) btnEl.classList.add('active');

    var searchVal = (document.getElementById('sdLiveSearch') || {}).value || '';
    sdHandleLiveSearch(searchVal);
}

function sdHandleLiveSearch(query) {
    var q = (query || '').toLowerCase().trim();
    var clearBtn = document.getElementById('sdSearchClearBtn');
    if (clearBtn) {
        clearBtn.style.display = q ? 'block' : 'none';
    }

    var cards = document.querySelectorAll('.sd-stage-card');
    var rows = document.querySelectorAll('#sdMainDataTable tbody tr');
    var visibleCount = 0;

    // Filter Cards
    cards.forEach(function(card) {
        var name = (card.getAttribute('data-stage-name') || '').toLowerCase();
        var desc = (card.getAttribute('data-stage-desc') || '').toLowerCase();
        var fType = card.getAttribute('data-filter-type') || '';

        var matchesFilter = (sdActiveFilter === 'all') || (sdActiveFilter === fType);
        var matchesSearch = !q || name.indexOf(q) > -1 || desc.indexOf(q) > -1;

        if (matchesFilter && matchesSearch) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // Filter Table rows
    rows.forEach(function(row) {
        var name = (row.getAttribute('data-stage-name') || '').toLowerCase();
        var desc = (row.getAttribute('data-stage-desc') || '').toLowerCase();
        var fType = row.getAttribute('data-filter-type') || '';

        var matchesFilter = (sdActiveFilter === 'all') || (sdActiveFilter === fType);
        var matchesSearch = !q || name.indexOf(q) > -1 || desc.indexOf(q) > -1;

        if (matchesFilter && matchesSearch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    // Empty state handling
    var emptyBox = document.getElementById('sdEmptyState');
    var emptyTitle = document.getElementById('sdEmptyTitle');
    var emptyText = document.getElementById('sdEmptyText');
    if (emptyBox) {
        if (visibleCount === 0 && (q || sdActiveFilter !== 'all')) {
            emptyBox.style.display = 'block';
            if (emptyTitle) emptyTitle.textContent = 'No matching stages found';
            if (emptyText) emptyText.textContent = 'No stages match your search "' + (q || sdActiveFilter) + '".';
        } else if (cards.length === 0) {
            emptyBox.style.display = 'block';
            if (emptyTitle) emptyTitle.textContent = 'No Stages Defined Yet';
            if (emptyText) emptyText.textContent = 'To schedule festival competitions, start by creating your first stage using the button below.';
        } else {
            emptyBox.style.display = 'none';
        }
    }
}

function sdClearSearch() {
    var input = document.getElementById('sdLiveSearch');
    if (input) {
        input.value = '';
        input.focus();
    }
    sdHandleLiveSearch('');
}

function sdPickPreset(presetText) {
    var descInput = document.getElementById('sdTxtStageDescription');
    if (descInput) {
        descInput.value = presetText;
        descInput.focus();
        descInput.classList.add('is-valid');
        setTimeout(function() {
            descInput.classList.remove('is-valid');
        }, 1200);
    }
}

function sdOpenAddModal(defaultStageName) {
    var modal = sdGetModal();
    if (!modal) return;
    document.getElementById('sdModalIcon').className = 'bi bi-plus-circle-fill';
    document.getElementById('sdModalTitle').textContent = 'Add New Stage';
    document.getElementById('sdSubmitBtnText').textContent = 'Save Stage';
    document.getElementById('sdStageForm').action = path + 'stage/stage_details/add_stage';
    document.getElementById('sdHidStId').value = '';

    var nameSelect = document.getElementById('sdTxtStageName');
    if (nameSelect) {
        if (defaultStageName) {
            nameSelect.value = defaultStageName;
        } else {
            nameSelect.value = '';
        }
    }
    var descInput = document.getElementById('sdTxtStageDescription');
    if (descInput) descInput.value = '';

    modal.show();
}

function sdOpenEditModal(stageId, stageName, stageDesc) {
    var modal = sdGetModal();
    if (!modal) return;
    document.getElementById('sdModalIcon').className = 'bi bi-pencil-square';
    document.getElementById('sdModalTitle').textContent = 'Edit Stage Details';
    document.getElementById('sdSubmitBtnText').textContent = 'Update Stage';
    document.getElementById('sdStageForm').action = path + 'stage/stage_details/update_stage_detials';
    document.getElementById('sdHidStId').value = stageId;

    var nameSelect = document.getElementById('sdTxtStageName');
    if (nameSelect) nameSelect.value = stageName;

    var descInput = document.getElementById('sdTxtStageDescription');
    if (descInput) descInput.value = stageDesc;

    modal.show();
}

function sdSubmitStage() {
    var stageName = document.getElementById('sdTxtStageName').value;
    var stageDesc = document.getElementById('sdTxtStageDescription').value;

    if (!stageName) {
        showErrorAlert('Please select a stage name');
        document.getElementById('sdTxtStageName').focus();
        return;
    }
    if (!stageDesc || !stageDesc.trim()) {
        showErrorAlert('Please enter a stage description / venue');
        document.getElementById('sdTxtStageDescription').focus();
        return;
    }

    var form = document.getElementById('sdStageForm');
    var formData = new FormData(form);
    if (typeof getCsrfToken === 'function') {
        var csrf = getCsrfToken();
        if (csrf && !formData.has('csrf_test_name')) {
            formData.append('csrf_test_name', csrf);
        }
    }

    var submitBtn = document.getElementById('sdModalSubmitBtn');
    var originalText = submitBtn.innerHTML;
    var submitLabel = (document.getElementById('sdSubmitBtnText') || {}).textContent || 'Saving';

    showFormSpinner(submitBtn, submitLabel + '...');

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;

        if (data.success) {
            var modal = sdGetModal();
            if (modal) modal.hide();
            showSuccessAlert(data.message || 'Stage details saved successfully!');
            setTimeout(function() { location.reload(); }, 1000);
        } else {
            showErrorAlert(data.message || 'An error occurred. Please try again.');
        }
    })
    .catch(function() {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        showErrorAlert('Network error. Please try again.');
    });
}

function sdDeleteStage(stageId, triggerEl) {
    swal({
        title: 'Delete Stage?',
        text: 'Are you sure you want to delete this stage? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then(function(result) {
        if (result.isConfirmed || result === true) {
            if (triggerEl) {
                triggerEl.style.pointerEvents = 'none';
                triggerEl.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            }
            var formData = new FormData();
            formData.append('hidStageId', stageId);
            if (typeof getCsrfToken === 'function') {
                var csrf = getCsrfToken();
                if (csrf) {
                    formData.append('csrf_test_name', csrf);
                }
            }

            fetch(path + 'stage/stage_details/delete_stage_detials', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    showSuccessAlert(data.message || 'Stage deleted successfully!');
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    showErrorAlert(data.message || 'Failed to delete stage.');
                    if (triggerEl) {
                        triggerEl.style.pointerEvents = '';
                        triggerEl.innerHTML = '<i class="bi bi-trash3-fill"></i>';
                    }
                }
            })
            .catch(function() {
                showErrorAlert('Network error. Please try again.');
                if (triggerEl) {
                    triggerEl.style.pointerEvents = '';
                    triggerEl.innerHTML = '<i class="bi bi-trash3-fill"></i>';
                }
            });
        }
    });
}

// Restore saved view mode on page load
document.addEventListener('DOMContentLoaded', function() {
    try {
        var savedMode = localStorage.getItem('tk_stage_view_mode') || 'grid';
        sdSwitchView(savedMode);
    } catch(e) {}
});

/* ══════════════════════════════════════════════
   LEGACY — Keep for backward compatibility
   ══════════════════════════════════════════════ */

function fnsAddStage() {
    sdOpenAddModal();
    return false;
}

function editStage(stageId) {
    var row = document.querySelector('tr[data-stage-id="' + stageId + '"]');
    if (row) {
        sdOpenEditModal(stageId, row.dataset.stageName, row.dataset.stageDesc);
    }
    return false;
}

function deleteStage(stageId) {
    sdDeleteStage(stageId);
    return false;
}
