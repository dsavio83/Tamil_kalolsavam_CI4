function fnschgpwdAddrr()
{
    if (document.getElementById('txtSchoolCode').value == "")
    {
        swal("Oops!", "Please Enter a valid School code", "error");
        return false;
    }
}
function resultprintout(itemcode)
{
    swal("Oops!", "Savio", "error");
    alert(itemcode);
    //($('hiditemcode').value=itemcode;
    // alert(($('hiditemcode').value);
//	($('resreport').action=path+"report/timefestreportpdf/confidential";
    //($('resreport').submit();
}
function checkvalsubmit()
{
    if ($('cmbFestType').value == 0) {
        swal("Oops!", "Please select festival", "error");
        $('cmbFestType').focus();
        return false;
    }
    if ($('cbo_item').value == 0) {
        swal("Oops!", "Please select an item from list", "error");
        $('cbo_item').focus();
        return false;
    }

}
function callsheetfirst()
{
    if ($('cmbFestType').value == 0) {
        swal("Oops!", "Please select a festival", "error");
        $('cmbFestType').focus();
        return false;
    }
    if ($('cbo_item').value == 0) {
        swal("Oops!", "Please select a item", "error");
        $('cbo_item').focus();
        return false;
    }
}

function tabulationitemsubmit()
{

    if ($('cbo_item').value == 0) {
        swal("Oops!", "Please select a item", "error");
        $('cbo_item').focus();
        return false;
    }
    if (trim($('txtParticipantNum').value) == '') {
        swal("Oops!", "Please Enter no of Participants", "error");
        $('txtParticipantNum').focus();
        return false;
    }


}
function callsheet()
{
    if ($('cmbFestType1').value == 0) {
        swal("Oops!", "Please select a festival", "error");
        $('cmbFestType1').focus();
        return false;
    }


}
function cluster()
{
    if ($('cmbFestType1').value == 0) {
        swal("Oops!", "Please select a festival", "error");
        $('cmbFestType1').focus();
        return false;
    }
}
function clureport()
{
    if ($('cmbFestType').value == 0) {
        swal("Oops!", "Please select a festival", "error");
        $('cmbFestType').focus();
        return false;
    }
    if ($('cbo_item').value == 0) {
        swal("Oops!", "Please select a item", "error");
        $('cbo_item').focus();
        return false;
    }

}
function cluster_doc()
{
    if ($('cmbFestType1').value == 0) {
        swal("Oops!", "Please select a festival", "error");
        $('cmbFestType1').focus();
        return false;
    }
    $('cluster').action = path + 'report/prereportpdf/cluster_report_all_doc';
    $('cluster').submit();
    $('cluster').action = path + 'report/prereportpdf/cluster_report_all';
    return false;
}
function fetch_callsheet_festival(festid)
{
    //alert(festid)

    //var festid	=	$('txtSchoolCode').value;
    var oOptions = {
        method: "post",
        parameters: {'fest_id': festid},
        onFailure: function (oXHR, oJson) {
            //swal("Oops!", "An error occurred: " + oXHR.status);
        },
        onSuccess: function (transport) {
            var response = transport.responseText;
            $('cmbitem').innerHTML = response;
        }
    };
    var oRequest = new Ajax.Updater({
        //success: "divCustomerInfo"
    }, path + "ajax/ajax2/fetch_callsheet_festival", oOptions);

}

function fetch_schooldetails(festid)
{
    //var festid	=	$('txtSchoolCode').value;
    var oOptions = {
        method: "post",
        parameters: {'fest_id': festid},
        onFailure: function (oXHR, oJson) {
            //swal("Oops!", "An error occurred: " + oXHR.status);
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
function higher_schooldetails(festid)
{
    //var festid	=	$('txtSchoolCode').value;
    var oOptions = {
        method: "post",
        parameters: {'fest_id': festid},
        onFailure: function (oXHR, oJson) {
            //swal("Oops!", "An error occurred: " + oXHR.status);
        },
        onSuccess: function (transport) {
            var response = transport.responseText;
            $('school_name').innerHTML = response;
        }
    };
    var oRequest = new Ajax.Updater({
        //success: "divCustomerInfo"
    }, path + "ajax/ajax2/fetch_school_from_festival", oOptions);
}

function morethanone()
{
    if ($('cmbFestType').value == 0) {
        swal("Oops!", "Please select a festival", "error");
        $('cmbFestType').focus();
        return false;
    }

}
function morethanonelimit()
{
    if ($('cmbFestType').value == 0) {
        swal("Oops!", "Please select a festival", "error");
        $('cmbFestType').focus();
        return false;
    }
    if ($('txtLimitcode').value == "") {
        swal("Oops!", "Please enter the limit value", "error");
        $('txtLimitcode').focus();
        return false;
    }

}

function fetch_item_from_festival2(festid)
{
    //var festid	=	$('txtSchoolCode').value;
    var oOptions = {
        method: "post",
        parameters: {'fest_id': festid},
        onFailure: function (oXHR, oJson) {
            //swal("Oops!", "An error occurred: " + oXHR.status);
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
function fetch_item_from_participant(festid)
{


    //var festid	=	$('txtSchoolCode').value;
    var oOptions = {
        method: "post",
        parameters: {'fest_id': festid},
        onFailure: function (oXHR, oJson) {
            //swal("Oops!", "An error occurred: " + oXHR.status);
        },
        onSuccess: function (transport) {
            var response = transport.responseText;
            $('cmbitem').innerHTML = response;
        }
    };
    var oRequest = new Ajax.Updater({
        //success: "divCustomerInfo"
    }, path + "ajax/loadajax/fetch_item_from_participantt", oOptions);
}
function fetch_higherlevel_participant(festid)
{


    //var festid	=	$('txtSchoolCode').value;
    var oOptions = {
        method: "post",
        parameters: {'fest_id': festid},
        onFailure: function (oXHR, oJson) {
            //swal("Oops!", "An error occurred: " + oXHR.status);
        },
        onSuccess: function (transport) {
            var response = transport.responseText;
            $('cmbitem').innerHTML = response;
        }
    };
    var oRequest = new Ajax.Updater({
        //success: "divCustomerInfo"
    }, path + "ajax/loadajax/fetch_higherlevel", oOptions);
}


