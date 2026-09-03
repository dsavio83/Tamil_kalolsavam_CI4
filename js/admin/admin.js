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
    $('sel_district_id').value = id;
    $('list_district').action = path + 'welcome/sub_district_details';
    $('list_district').submit();
}
function fncListClustertDetails(id)
{
    $('sel_sub_district_id').value = id;
    $('list_district').action = path + 'welcome/cluster_details';
    $('list_district').submit();
}
function fncSaveKalolsavamCSV()
{
    $('formKalolsavam').submit();
}

function nonclusterschool(subdist)
{
    $('hidClusterId').value = subdist;
    $('clustschool').action = path + 'welcome/nonclustdetails';
    $('clustschool').submit();

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
    $('formKalolsavam').submit();
    return true;

}

function fnc_change_confirmation_status(schoolName, schoolCode, elementId) {
    Swal.fire({
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
            // Show loading state
            Swal.fire({
                title: 'Resetting...',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });

            new Ajax.Request(path + 'ajax/loadajax/change_confirmation_status', {
                method: 'post',
                parameters: {
                    school_code: schoolCode
                },
                onSuccess: function(response) {
                    var responseText = response.responseText.trim();
                    if (responseText === 'No') {
                        // Update the UI to show "No" (unconfirmed)
                        var element = $(elementId);
                        if (element) {
                            element.innerHTML = 'No';
                        }

                        // Hide the reset button since it's now reset to "No"
                        var resetBtn = $('reset_' + schoolCode);
                        if (resetBtn) {
                            resetBtn.style.display = 'none';
                        }

                        Swal.fire({
                            title: 'Success!',
                            text: 'Confirmation status has been reset successfully.',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        });
                    } else {
                        Swal.fire({
                            title: 'Failed!',
                            text: 'Failed to reset: ' + responseText,
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                onFailure: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred during reset. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}

