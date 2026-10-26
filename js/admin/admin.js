function fncUpdateKalolsavam($kalolsavam_type, id)
{
    var nameEl = document.getElementById('txtKalolsavamName');
    var yearEl = document.getElementById('txtKalolsavamYear');
    var venueEl = document.getElementById('txtKalolsavamVenue');
    var formEl = document.getElementById('formKalolsavam');

    if ($kalolsavam_type == 'state')
    {
        if (nameEl && !nameEl.value.trim())
        {
            if (typeof Swal !== 'undefined') {
                Swal.fire("Required Field", "Please enter Kalolsavam Name", "warning");
            } else {
                alert("Please enter Kalolsavam Name");
            }
            nameEl.focus();
            return false;
        }
        if (yearEl && !yearEl.value.trim())
        {
            if (typeof Swal !== 'undefined') {
                Swal.fire("Required Field", "Please enter Kalolsavam Year", "warning");
            } else {
                alert("Please enter Kalolsavam Year");
            }
            yearEl.focus();
            return false;
        }
    }
    if (venueEl && !venueEl.value.trim())
    {
        if (typeof Swal !== 'undefined') {
            Swal.fire("Required Field", "Please enter Venue", "warning");
        } else {
            alert("Please enter Venue");
        }
        venueEl.focus();
        return false;
    }
    if (formEl) {
        formEl.action = path + 'admin/kalolsavam/update_kalolsavam';
        formEl.submit();
    }
    return true;
}

function fncEditKalolsavam(id)
{
    var selIdEl = document.getElementById('sel_kalolsavam_id');
    var formEl = document.getElementById('list_formKalolsavam');
    if (selIdEl && formEl) {
        selIdEl.value = id;
        formEl.action = path + 'admin/kalolsavam/edit_kalolsavam';
        formEl.submit();
    }
}

function fncCancelKalolsavam()
{
    var formCard = document.getElementById('formCard');
    var kalolsavamList = document.getElementById('kalolsavamList');
    if (formCard) {
        formCard.style.display = 'none';
        if (kalolsavamList) {
            kalolsavamList.style.display = 'block';
            kalolsavamList.scrollIntoView({ behavior: 'smooth' });
        }
    } else {
        window.location.href = path + 'admin/kalolsavam';
    }
}

function fncSaveKalolsavam()
{
    var nameEl = document.getElementById('txtKalolsavamName');
    var yearEl = document.getElementById('txtKalolsavamYear');
    var venueEl = document.getElementById('txtKalolsavamVenue');
    var formEl = document.getElementById('formKalolsavam');

    if (nameEl && !nameEl.value.trim())
    {
        if (typeof Swal !== 'undefined') {
            Swal.fire("Required Field", "Please enter Kalolsavam Name", "warning");
        } else {
            alert("Please enter Kalolsavam Name");
        }
        nameEl.focus();
        return false;
    }
    if (yearEl && !yearEl.value.trim())
    {
        if (typeof Swal !== 'undefined') {
            Swal.fire("Required Field", "Please enter Kalolsavam Year", "warning");
        } else {
            alert("Please enter Kalolsavam Year");
        }
        yearEl.focus();
        return false;
    }
    if (venueEl && !venueEl.value.trim())
    {
        if (typeof Swal !== 'undefined') {
            Swal.fire("Required Field", "Please enter Venue", "warning");
        } else {
            alert("Please enter Venue");
        }
        venueEl.focus();
        return false;
    }
    if (formEl) {
        formEl.action = path + 'admin/kalolsavam/add_kalolsavam';
        formEl.submit();
    }
    return true;
}
function fncListSubDistrictDetails(id)
{
    var sel = document.getElementById('sel_district_id');
    if (sel) sel.value = id;
    var form = document.getElementById('list_district');
    if (form) {
        form.action = path + 'welcome/sub_district_details';
        form.submit();
    }
}
function fncListClustertDetails(id)
{
    var basePath = (typeof path !== 'undefined') ? path : '';
    if (basePath && !basePath.endsWith('/')) basePath += '/';
    window.location.href = basePath + 'welcome/cluster_details?sel_sub_district_id=' + encodeURIComponent(id);
}
function fncSaveKalolsavamCSV()
{
    $('formKalolsavam').submit();
}

function nonclusterschool(subdist)
{
    var hidClusterId = document.getElementById('hidClusterId');
    var clustschool = document.getElementById('clustschool');
    if (hidClusterId) hidClusterId.value = subdist;
    if (clustschool) {
        clustschool.action = path + 'welcome/nonclustdetails';
        clustschool.submit();
    }
}
function fncExportSubDistrictData()
{
    $('confirm_sub_dist').action = path + 'export/export_sub_district_data';
    $('confirm_sub_dist').submit();
}
function fncExportDistrictData()
{
    $('export_dist_data').action = path + 'export/export_district_data';
    $('hidExport').value = 'TRUE';
    $('export_dist_data').submit();
}

function nonclustschooldet(shid) {
    $('hidSchoolId').value = shid;
    $('noncluster').action = path + 'schools/registration/';
    $('noncluster').submit();
}

function printContentt(element_id)
{
    var DocumentContainer = document.getElementById(element_id);
    var WindowObject = window.open('', "TrackHistoryData",
            "width='100%',height='100%',top=10,left=10,toolbars=no,scrollbars=yes,status=no,resizable=no", "error");
    WindowObject.document.writeln(DocumentContainer.innerHTML);
    WindowObject.document.close();
    WindowObject.focus();
    WindowObject.print();
    WindowObject.close();
}
function fncrestoreKalolsavamdatabase() {
    var fileInput = document.getElementById('kalolsavamdatabase');
    if (!fileInput || !fileInput.value) {
        alert('Please select a .sql database backup file to restore.');
        return false;
    }
    var confirmBox = document.getElementById('i_confirm');
    if (!confirmBox || !confirmBox.checked) {
        alert('Please tick the confirmation box before restoring the database.');
        return false;
    }
    // Append the server-side confirmation value required for restore.
    var form = document.getElementById('formKalolsavam');
    if (!form) {
        return false;
    }
    var inp = document.getElementById('confirm_restore');
    if (!inp) {
        inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'confirm_restore';
        inp.id = 'confirm_restore';
        inp.value = 'RESTORE';
        form.appendChild(inp);
    } else {
        inp.value = 'RESTORE';
    }
    form.submit();
    return true;
}

function fnc_change_confirmation_status(schoolName, schoolCode, elementId) {
    var swalFn = (typeof Swal !== 'undefined') ? Swal : (typeof swal !== 'undefined' ? swal : null);
    if (swalFn && swalFn.fire) {
        swalFn.fire({
            title: 'Reset Confirmation Status?',
            text: "Do you really want to reset the confirmation status of " + schoolName + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Reset',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                swalFn.fire({
                    title: 'Resetting...',
                    text: 'Please wait...',
                    allowOutsideClick: false,
                    didOpen: function() {
                        swalFn.showLoading();
                    }
                });
                executeChangeConfirmation(schoolName, schoolCode, elementId, swalFn);
            }
        });
    } else {
        if (confirm("Do you really want to reset the confirmation status of " + schoolName + "?")) {
            executeChangeConfirmation(schoolName, schoolCode, elementId, null);
        }
    }
}

function executeChangeConfirmation(schoolName, schoolCode, elementId, swalFn) {
    var formData = new URLSearchParams();
    formData.append('school_code', schoolCode);

    fetch(path + 'ajax/loadajax/change_confirmation_status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(function(res) { return res.text(); })
    .then(function(responseText) {
        var resp = responseText.trim();
        if (resp === 'No') {
            var element = document.getElementById(elementId);
            if (element) {
                element.innerHTML = 'No';
            }
            var resetBtn = document.getElementById('reset_' + schoolCode);
            if (resetBtn) {
                resetBtn.style.display = 'none';
            }
            var resetNonBtn = document.getElementById('reset_nonschool_' + schoolCode);
            if (resetNonBtn) {
                resetNonBtn.style.display = 'none';
            }
            if (swalFn && swalFn.fire) {
                swalFn.fire({
                    title: 'Success!',
                    text: 'Confirmation status has been reset successfully.',
                    icon: 'success',
                    confirmButtonColor: '#28a745'
                });
            } else {
                alert('Confirmation status has been reset successfully.');
            }
        } else {
            if (swalFn && swalFn.fire) {
                swalFn.fire({
                    title: 'Failed!',
                    text: 'Failed to reset: ' + resp,
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            } else {
                alert('Failed to reset: ' + resp);
            }
        }
    })
    .catch(function(err) {
        console.error('Reset status error:', err);
        if (swalFn && swalFn.fire) {
            swalFn.fire({
                title: 'Error!',
                text: 'Network or server communication error.',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        } else {
            alert('Error communicating with server.');
        }
    });
}

