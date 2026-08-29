// JavaScript Document
function fetch_item_details_result()
{
    var itemCodeEl = document.getElementById('txtItemCode');
    if (itemCodeEl && itemCodeEl.value.length <= 4) {
        var item_code = itemCodeEl.value;
        
        // Use modern fetch API if available, fallback to Ajax
        if (typeof fetch !== 'undefined') {
            var formData = new FormData();
            formData.append('code', item_code);
            
            fetch(path + "result/resultentry/get_item_details_result", {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                var divEl = document.getElementById('divEntryForm');
                if (divEl) divEl.innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        } else if (typeof Ajax !== 'undefined') {
            var oOptions = {
                method: "post",
                parameters: {'code': item_code},
                onFailure: function (oXHR, oJson) {
                    console.error("An error occurred: " + oXHR.status);
                },
                onSuccess: function (transport) {
                    var response = transport.responseText;
                    var divEl = document.getElementById('divEntryForm');
                    if (divEl) divEl.innerHTML = response;
                }
            };
            var oRequest = new Ajax.Updater({}, path + "result/resultentry/get_item_details_result", oOptions);
        }
    } else {
        return false;
    }
}

function fncSaveResultEntry()
{
    // Get elements using modern JavaScript
    var codeNoEl = document.getElementById('txt_code_no');
    var regNoEl = document.getElementById('txt_reg_no');
    var totalMarkEl = document.getElementById('totalMark');
    var hidNoJudgeEl = document.getElementById('hidNoJudge');
    var formEl = document.getElementById('resultEntry');
    
    if (!codeNoEl || trim(codeNoEl.value) == '')
    {
        Swal.fire("Oops!", "Please enter the code", "error");
        if (codeNoEl) codeNoEl.focus();
        return;
    }
    if (!regNoEl || trim(regNoEl.value) == '')
    {
        Swal.fire("Oops!", "Please enter the register number", "error");
        if (regNoEl) regNoEl.focus();
        return;
    }
    var mark_id = '';
    var total_marks = 0;
    var numJudges = hidNoJudgeEl ? parseInt(hidNoJudgeEl.value) : 0;
    
    for (var i = 1; i <= numJudges; i++) {
        var markEl = document.getElementById('mark_' + i);
        if (!markEl || trim(markEl.value) == '')
        {
            Swal.fire("Oops!", "Please enter the mark " + i, "error");
            if (markEl) markEl.focus();
            return;
        }
        if (trim(markEl.value) * 1 > 100)
        {
            Swal.fire("Oops!", "Please enter valid mark " + i, "error");
            if (markEl) markEl.focus();
            return;
        }

        total_marks += markEl.value * 1;
    }
    if (!totalMarkEl || trim(totalMarkEl.value) == '')
    {
        Swal.fire("Oops!", "Please enter the total marks", "error");
        if (totalMarkEl) totalMarkEl.focus();
        return;
    }

    if (total_marks != totalMarkEl.value)
    {
        Swal.fire("Oops!", "Please check the total marks entered", "error");
        if (totalMarkEl) totalMarkEl.focus();
        return;
    }

    if (formEl) {
        formEl.action = path + 'result/resultentry/save_result_entry/';
        formEl.submit();
    }
}

function editResult(id) {
    var hidResultIdEl = document.getElementById('hidResultId');
    var formEl = document.getElementById('resultEntryList');
    if (hidResultIdEl) hidResultIdEl.value = id;
    if (formEl) {
        formEl.action = path + 'result/resultentry/edit_result_entry/';
        formEl.submit();
    }
}

function deleteResult(id) {
    Swal.fire({
        title: "Delete Result",
        text: "Do you want to delete the result?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            var hidResultIdEl = document.getElementById('hidResultId');
            var formEl = document.getElementById('resultEntryList');
            if (hidResultIdEl) hidResultIdEl.value = id;
            if (formEl) {
                formEl.action = path + 'result/resultentry/delete_result_entry/';
                formEl.submit();
            }
        }
    });
}

function fncCancel() {
    var formEl = document.getElementById('resultEntryList');
    if (formEl) {
        formEl.action = path + 'result/resultentry/';
        formEl.submit();
    }
}
// function confirmResutlEntry()
// {
//     if (confirm('Do you really want to confirm the results?'))
//     {
//         $('resultEntryList').action = path + 'result/resultentry/confirm_result_entry';
//         $('resultEntryList').submit();
//     }
// }
function confirmResutlEntry() {
    Swal.fire({
        title: "Confirm Results Entry",
        text: "Do you really want to confirm the results?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Confirm",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            var formEl = document.getElementById('resultEntryList');
            if (formEl) {
                formEl.action = path + 'result/resultentry/confirm_result_entry';
                formEl.submit();
            }
        }
    });
}

function getItemResult(itemcode)
{
    $('hidItemId').value = itemcode;
    $('formIWPq').action = path + 'result/resultentry/';
    $('formIWPq').submit();
}
function resetResultConfirmation(itemcode, message, element_id)
{
    //swal("Oops!", "---" + itemcode + "----" + message + "----" + element_id, "error");
        //alert('---'+itemcode+'----'+message+'----'+element_id);
    message = 'Do you really want to reset the Result confirmation status of ' + message;
    if (confirm(message))
    {
        ajax_loder2(element_id);
        var oOptions = {
            method: "post",
            parameters: {'item_code': itemcode},
            onFailure: function (oXHR, oJson) {
                //swal("Oops!", "An error occurred: " + oXHR.status, "error");
                //alert("An error occurred: " + oXHR.status);
            },
            onSuccess: function (transport) {
                
                var response = transport.responseText;
                $(element_id).innerHTML = '';
                $(element_id + '_dis').innerHTML = response;
            }
        };
        var oRequest = new Ajax.Updater({
            //success: "divCustomerInfo"
        }, path + "ajax/loadajax/reset_result_confirmation_status", oOptions);
    }
}


function resultcommon(itm)
{
    $('hiditemcode').value = itm;
    //swal ( "Oops!" ,  "$('hiditemcode').value" ,  "error" );
    //alert($('hiditemcode').value);
    $('common_report').action = path + "report/timefestreportpdf/common_report";
    $('common_report').submit();
}
function resultallcommon()
{
    $('hiditemcode').value = 'ALL';
    $('common_report').action = path + "report/timefestreportpdf/common_report";
    $('common_report').submit();
}

function resultprintout(itm)
{
    $('hiditemcode').value = itm;
    //swal ( "Oops!" ,  "$('hiditemcode').value" ,  "error" );
    //alert($('hiditemcode').value);
    $('resreport').action = path + "report/timefestreportpdf/confidential";
    $('resreport').submit();
}
function resultallprint()
{
    $('hiditemcode').value = 'ALL';
    $('resreport').action = path + "report/timefestreportpdf/confidential";
    $('resreport').submit();
}
function printConfidentialReport()
{
    var formEl = document.getElementById('resultEntryList');
    if (formEl) {
        formEl.action = path + 'result/resultentry/printConfidentialReport';
        formEl.submit();
    }
}