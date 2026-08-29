// JavaScript Document



function fetch_item_from_festival(festid)
{


    //var festid	=	$('txtSchoolCode').value;
    var oOptions = {
        method: "post",
        parameters: {'fest_id': festid},
        onFailure: function (oXHR, oJson) {
            //swal("Oops!", "An error occurred: " + oXHR.status, "error");
        },
        onSuccess: function (transport) {
            var response = transport.responseText;
            $('cmbitem').innerHTML = response;
        }
    };
    var oRequest = new Ajax.Updater({
        //success: "divCustomerInfo"
    }, path + "ajax/loadajax/fetch_item_from_festival", oOptions);
}

function fetch_report_schooldetails(festid)
{
    //var festid	=	$('txtSchoolCode').value;
    var oOptions = {
        method: "post",
        parameters: {'fest_id': festid},
        onFailure: function (oXHR, oJson) {
            //swal("Oops!", "An error occurred: " + oXHR.status, "error");
        },
        onSuccess: function (transport) {
            var response = transport.responseText;
            $('cmbitem').innerHTML = response;
        }
    };
    var oRequest = new Ajax.Updater({
        //success: "divCustomerInfo"
    }, path + "ajax/ajax2/fetch_school_from_festival", oOptions);
}


function fnschgpwdAddrr()
{
    if ($(txtSchoolCode.value) != '')
    {
        swal("Oops!", "Please Enter a valid School code", "error");
        return false;


    }

}

function fetch_item_details()
{
    var txtItemCode = $('txtItemCode').value;
    if (txtItemCode == 0) {
        swal("Oops!", "Select Item", "error");
        //alert('Select Item');
        $('txtItemCode').focus();
        return false;
    }



    var oOptions = {
        method: "post",
        parameters: {'code': txtItemCode},
        onFailure: function (oXHR, oJson) {
            swal("Oops!", "An error occurred: " + oXHR.status, "error");
        },
        onSuccess: function (transport) {
            var response = transport.responseText;
            $('txtItemName').innerHTML = response;
        }

    };

    var oRequest = new Ajax.Updater({
        //success: "divCustomerInfo"
    }, path + "ajax/ajax2/fetch_item_name", oOptions);
}
function notSelected()
{  // alert ("hai", "error");
    var cmbFest = $('cmbFestType').value;
    if (cmbFest == 0)
    {
        swal("Oops!", "Select a Festival", "error");
        //alert('Select a Festival');
        return false;
    }
    else
        return true;
}
function notItemSelected(cmbFestType, cbo_item)
{
    //alert ("hai", "error");
    var cmbFest = $('cmbFestType').value;
    var cbo = $('cbo_item').value;
    if ((cmbFest == 0) || (cbo == ''))
    {

        swal("Oops!", "Select a Festival", "error");
        //alert('Select a Festival');
        return false;

    }
    else
        return true;
}

function fetch_all_festival_result(festid)//vip
{


    //var festid	=	$('txtSchoolCode').value;
    var oOptions = {
        method: "post",
        parameters: {'fest_id': festid},
        onFailure: function (oXHR, oJson) {
            //swal("Oops!", "An error occurred: " + oXHR.status, "error");
        },
        onSuccess: function (transport) {
            var response = transport.responseText;
            $('cmbitem').innerHTML = response;
        }
    };
    var oRequest = new Ajax.Updater({
        //success: "divCustomerInfo"
    }, path + "ajax/loadajax/fetch_all_festival_result", oOptions);
}
function fetch_item_from_festival2(festid)
{


    //var festid	=	$('txtSchoolCode').value;
    var oOptions = {
        method: "post",
        parameters: {'fest_id': festid},
        onFailure: function (oXHR, oJson) {
            //swal("Oops!", "An error occurred: " + oXHR.status, "error");
        },
        onSuccess: function (transport) {
            var response = transport.responseText;
            $('cmbitem').innerHTML = response;
        }
    };
    var oRequest = new Ajax.Updater({
        //success: "divCustomerInfo"
    }, path + "ajax/loadajax/fetch_item_from_festival2", oOptions);
}
function fetch_rankwise_participant_result(festid)
{


    //var festid	=	$('txtSchoolCode').value;
    var oOptions = {
        method: "post",
        parameters: {'fest_id': festid},
        onFailure: function (oXHR, oJson) {
            //swal("Oops!", "An error occurred: " + oXHR.status, "error");
        },
        onSuccess: function (transport) {
            var response = transport.responseText;
            $('cmbitem').innerHTML = response;
        }
    };
    var oRequest = new Ajax.Updater({
        //success: "divCustomerInfo"
    }, path + "ajax/loadajax/rankwise_participant_result", oOptions);
}
