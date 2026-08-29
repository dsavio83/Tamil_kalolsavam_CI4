function fncUpdateKalolsavam($kalolsavam_type, id)
{
    if ($kalolsavam_type == 'state')
    {
        if (!trim($('txtKalolsavamName').value))
        {
            swal("Oops!", "Please Enter a Kalolsavam Name", "error");
            $('txtKalolsavamName').value = '';
            $('txtKalolsavamName').focus();
            return false;
        }
        if (!trim($('txtKalolsavamYear').value))
        {
            swal("Oops!", "Please Enter Kalolsavam Year", "error");
            $('txtKalolsavamYear').value = '';
            $('txtKalolsavamYear').focus();
            return false;
        }
    }
    if (!$('txtKalolsavamVenue').value)
    {
        swal("Oops!", "Please Enter a Venu", "error");
        $('txtKalolsavamVenue').value = '';
        $('txtKalolsavamVenue').focus();
        return false;
    }
    $('formKalolsavam').action = path + 'admin/kalolsavam/update_kalolsavam';
    $('formKalolsavam').submit();
    return true;
}
function fncEditKalolsavam(id)
{
    $('sel_kalolsavam_id').value = id;
    $('list_formKalolsavam').action = path + 'admin/kalolsavam/edit_kalolsavam';
    $('list_formKalolsavam').submit();
}
function fncCancelKalolsavam()
{
    window.open(path + 'admin/kalolsavam', '_parent');
}
function fncSaveKalolsavam()
{
    if (!trim($('txtKalolsavamName').value))
    {
        swal("Oops!", "Please Enter a Kalolsavam Name", "error");
        $('txtKalolsavamName').value = '';
        $('txtKalolsavamName').focus();
        return false;
    }
    if (!trim($('txtKalolsavamYear').value))
    {
        swal("Oops!", "Please Enter Kalolsavam Year", "error");
        $('txtKalolsavamYear').value = '';
        $('txtKalolsavamYear').focus();
        return false;
    }
    if (!$('txtKalolsavamVenue').value)
    {
        swal("Oops!", "Please Enter a Venu", "error");
        $('txtKalolsavamVenue').value = '';
        $('txtKalolsavamVenue').focus();
        return false;
    }
    $('formKalolsavam').submit();
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
    

    // Make AJAX call to reset confirmation status using Prototype.js
    new Ajax.Request(path + 'ajax/loadajax/change_confirmation_status', {
        method: 'post',
        parameters: {
            school_code: schoolCode
        },
        onSuccess: function(response) {
            

            // Always treat as success since controller returns 'No' for all cases
            

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

            
        },
        onFailure: function(xhr) {
            
            alert('Error occurred while resetting. Please try again.');
        }
    });
}

