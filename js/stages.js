// JavaScript Document

function clicktoviewfestdet(festid)
{
    $('hidfestid').value = festid;
    swal("Good job!", "You clicked the button!", "error");
    //swal("Oops!", "$('hidfestid').value", "error");
    //alert($('hidfestid').value);
    $('festdet').action = path + 'publishresult/resultindex/festival_allitem';
    $('festdet').submit();
}
function finisheditemdet(festid)
{
    $('hidfestid').value = festid;
    //swal("Oops!", $("#" + 'hidfestid').val(), "error");
    //alert($('hidfestid').value);
    $('festdet').action = path + 'publishresult/resultindex/finished_item';
    $('festdet').submit();

}
function remainderitemdet(festid)
{
    $('hidfestid').value = festid;
    //swal("Oops!", $("#" + 'hidfestid').val(), "error");
    //alert($('hidfestid').value);
    $('festdet').action = path + 'publishresult/resultindex/incomplete_item';
    $('festdet').submit();
}
function fnsAddStage()
{
    if (!document.getElementById('txtStageName').value)
    {
        swal("Oops!", "Please Enter Stage name", "error");
    //alert("Please Enter Stage name");
        document.getElementById('txtStageName').focus();
        return false;
    }
    if (!document.getElementById('txtStageDescription').value)
    {
        swal("Oops!", "Please Enter Stage Description", "error");
    //alert("Please Enter Stage Description");
        document.getElementById('txtStageDescription').focus();
        return false;
    }
    return true;
}
function gradewise()
{
    if ($('txtSchoolCode').value == "")
    {
        swal("Oops!", "Enter School Code", "error");
    //alert("Enter School Code");
        $('txtSchoolCode').focus();
        return false;
    }
}
function checkitemwise()
{
    if ($('cmbFestType').value == 0)
    {
        swal("Oops!", "Select a Festival from list", "error");
    //alert("Select a Festival from list");
        $('cmbFestType').focus();
        return false;
    }
    if ($('cbo_item').value == 0)
    {
        swal("Oops!", "select a item from list", "error");
    //alert("select a item from list");
        $('cbo_item').focus();
        return false;
    }

}
function rankwise()
{
    if ($('cmbFestType').value == 0)
    {
        swal("Oops!", "Select a Festival from list", "error");
    //alert("Select a Festival from list");
        $('cmbFestType').focus();
        return false;
    }
}
function deleteStage(stageId) {
    if (!confirm('Really want to delete the user?')) {
        return false;
    }
    $('hidStageId').value = stageId;
    $('editStage').action = path + 'stage/stage_details/delete_stage_detials';
    $('editStage').submit();
}

function editStage(stageId) {
    $('hidStageId').value = stageId;
    $('editStage').action = path + 'stage/stage_details/edit_stage_detials';
    $('editStage').submit();
}

function fnsUpdateStage(stageId) {
    $('hidStId').value = stageId;
    $('formStage').action = path + 'stage/stage_details/update_stage_detials';
    $('formStage').submit();
}

function cancel()
{
    $('formStage').action = path + 'stage/stage_details/';
    $('formStage').submit();
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
                //swal("Oops!", "An error occurred: " + oXHR.status, "error");
                //alert("An error occurred: " + oXHR.status);
            },
            onSuccess: function (transport) {
                var response = transport.responseText;
                $('divEntryForm').innerHTML = response;
            }
        };
        var oRequest = new Ajax.Updater({
            //success: "divCustomerInfo"
        }, path + "stage/allotment/get_item_details", oOptions);
        //swal("Oops!", "Success", "error");
    //alert("succ");
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
    //alert('Please select stage');
        $('cmbStage').focus();
        return false;
    }

    if ($('txtDate').value == '')
    {
        swal("Oops!", "Please select date", "error");
    //alert('Please select date');
        $('txtDate').focus();
        return false;
    }

    if ($('txtHour').value == '' || $('txtHour').value == 'HH')
    {
        swal("Oops!", "Please enter hou", "error");
    //alert('Please enter hour');
        $('txtHour').focus();
        return false;
    }

    if ($('txtMin').value == '' || $('txtMin').value == 'MM')
    {
        swal("Oops!", "Please enter minute", "error");
    //alert('Please enter minute');
        $('txtMin').focus();
        return false;
    }

    if ($('cmbNoOfCluster').value == '0')
    {
        swal("Oops!", "Please select number of clusters", "error");
    //alert('Please select number of clusters');
        $('cmbNoOfCluster').focus();
        return false;
    }

    if ($('cmbNoOfJudges').value == '0')
    {
        swal("Oops!", "Please select number of judges", "error");
    //alert('Please select number of judges');
        $('cmbNoOfJudges').focus();
        return false;
    }
    if (update == 1) {
        $('formAllotment').action = path + 'stage/allotment/update_allotment';
    }
    $('formAllotment').submit();
}

function fncUpdateCluster()
{
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
