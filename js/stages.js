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
    if (!btn || btn.disabled) {
        return true;
    }
    if (label === undefined) {
        label = btn.value || btn.textContent || 'Please wait';
    }
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + label;
    return true;
}

/* ══════════════════════════════════════════════
   STAGE MODAL — Add / Edit / Delete via AJAX
   ══════════════════════════════════════════════ */

var sdStageModal = null;

function sdGetModal() {
    if (!sdStageModal) {
        var el = document.getElementById('sdStageModal');
        if (el) sdStageModal = new bootstrap.Modal(el);
    }
    return sdStageModal;
}

function sdOpenAddModal() {
    var modal = sdGetModal();
    if (!modal) return;
    document.getElementById('sdModalIcon').className = 'bi bi-plus-circle';
    document.getElementById('sdModalTitle').textContent = 'Add Stage';
    document.getElementById('sdSubmitBtnText').textContent = 'Add Stage';
    document.getElementById('sdStageForm').action = path + 'stage/stage_details/add_stage';
    document.getElementById('sdHidStId').value = '';
    document.getElementById('sdTxtStageName').value = '';
    document.getElementById('sdTxtStageDescription').value = '';
    modal.show();
}

function sdOpenEditModal(stageId, stageName, stageDesc) {
    var modal = sdGetModal();
    if (!modal) return;
    document.getElementById('sdModalIcon').className = 'bi bi-pencil-square';
    document.getElementById('sdModalTitle').textContent = 'Edit Stage';
    document.getElementById('sdSubmitBtnText').textContent = 'Update Stage';
    document.getElementById('sdStageForm').action = path + 'stage/stage_details/update_stage_detials';
    document.getElementById('sdHidStId').value = stageId;
    document.getElementById('sdTxtStageName').value = stageName;
    document.getElementById('sdTxtStageDescription').value = stageDesc;
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
    if (!stageDesc) {
        showErrorAlert('Please enter a stage description');
        document.getElementById('sdTxtStageDescription').focus();
        return;
    }

    var form = document.getElementById('sdStageForm');
    var formData = new FormData(form);
    var submitBtn = document.getElementById('sdModalSubmitBtn');
    var originalText = submitBtn.innerHTML;
    var submitLabel = (document.getElementById('sdSubmitBtnText') || {}).textContent || 'Saving';

    showFormSpinner(submitBtn, submitLabel + '...');

    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;

        if (data.success) {
            var modal = sdGetModal();
            if (modal) modal.hide();
            showSuccessAlert(data.message || 'Stage saved successfully');
            setTimeout(function() { location.reload(); }, 1200);
        } else {
            showErrorAlert(data.message || 'An error occurred');
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
        if (result.isConfirmed) {
            if (triggerEl) {
                triggerEl.style.pointerEvents = 'none';
                triggerEl.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            }
            var formData = new FormData();
            formData.append('hidStageId', stageId);

            fetch(path + 'stage/stage_details/delete_stage_detials', {
                method: 'POST',
                body: formData
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    showSuccessAlert(data.message || 'Stage deleted successfully');
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    showErrorAlert(data.message || 'Failed to delete stage');
                }
            })
            .catch(function() {
                showErrorAlert('Network error. Please try again.');
            });
        }
    });
}

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
