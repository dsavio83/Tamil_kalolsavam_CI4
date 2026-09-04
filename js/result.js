// JavaScript Document
function moveFocus(currentInput, nextInputId, maxLen) {
    var max = maxLen || currentInput.maxLength || 2;
    if (currentInput && currentInput.value.length >= max) {
        var nextEl = document.getElementById(nextInputId);
        if (nextEl) nextEl.focus();
    }
}

function autoCalculateTotal() {
    var hidNoJudgeEl = document.getElementById('hidNoJudge');
    var totalMarkEl = document.getElementById('totalMark');
    if (!hidNoJudgeEl || !totalMarkEl) return;
    
    var numJudges = parseInt(hidNoJudgeEl.value) || 0;
    var total = 0;
    var countFilled = 0;
    
    for (var i = 1; i <= numJudges; i++) {
        var markEl = document.getElementById('mark_' + i);
        if (markEl && markEl.value.trim() !== '') {
            var val = parseFloat(markEl.value);
            if (!isNaN(val)) {
                total += val;
                countFilled++;
            }
        }
    }
    
    if (countFilled === numJudges && numJudges > 0) {
        totalMarkEl.value = total;
    }
}

function onJudgeMarkInput(el, nextInputId) {
    autoCalculateTotal();
    moveFocus(el, nextInputId, 2);
}

function checkTotalMark(totalMarkInput) {
    if (totalMarkInput && totalMarkInput.value.length === 3) {
        var saveBtn = document.getElementById("saveButton");
        if (saveBtn) saveBtn.click();
    }
}

function showItemSpinner() {
    var spin = document.getElementById('itemCodeSpinner');
    if (spin) {
        spin.classList.remove('d-none');
        spin.classList.add('d-inline-flex');
    }
}

function hideItemSpinner() {
    var spin = document.getElementById('itemCodeSpinner');
    if (spin) {
        spin.classList.remove('d-inline-flex');
        spin.classList.add('d-none');
    }
}

function onItemCodeInput(el) {
    if (el && el.value.length === 3) {
        fetch_item_details_result();
    }
}

var _isFetchingItemResult = false;
var _lastFetchedItemCode = null;

function fetch_item_details_result(force)
{
    var itemCodeEl = document.getElementById('txtItemCode');
    if (itemCodeEl && itemCodeEl.value.trim() !== '') {
        var item_code = itemCodeEl.value.trim();
        if (!force && item_code === _lastFetchedItemCode && !_isFetchingItemResult) {
            return false;
        }
        if (_isFetchingItemResult) {
            return false;
        }
        _isFetchingItemResult = true;
        _lastFetchedItemCode = item_code;
        showItemSpinner();
        
        var targetUrl = (typeof path !== 'undefined' && path) ? path : '';
        if (!targetUrl) {
            targetUrl = window.location.origin + window.location.pathname.split('index.php')[0];
        }
        if (window.location.href.indexOf('index.php') !== -1 && targetUrl.indexOf('index.php') === -1) {
            targetUrl = targetUrl.replace(/\/$/, '') + '/index.php/';
        }
        if (!targetUrl.endsWith('/')) {
            targetUrl += '/';
        }
        targetUrl += "result/resultentry/get_item_details_result";

        // Resolve current CSRF token dynamically
        var csrfToken = (typeof getCsrfToken === 'function') ? getCsrfToken() : '';
        if (!csrfToken) {
            var cookieMatch = document.cookie.match(/(?:^|;\s*)csrf_cookie_name=([^;]+)/);
            if (cookieMatch && cookieMatch[1]) {
                csrfToken = decodeURIComponent(cookieMatch[1]);
            } else {
                var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (csrfMeta) csrfToken = csrfMeta.getAttribute('content') || '';
            }
        }

        // Use modern fetch API if available, fallback to Ajax
        if (typeof fetch !== 'undefined') {
            var formData = new FormData();
            formData.append('code', item_code);
            if (csrfToken) {
                formData.append('csrf_test_name', csrfToken);
            }
            
            fetch(targetUrl, {
                method: 'POST',
                headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {},
                body: formData
            })
            .then(response => {
                var newHeaderToken = response.headers.get('X-CSRF-TOKEN');
                if (newHeaderToken) {
                    if (typeof updateCsrfToken === 'function') {
                        updateCsrfToken(newHeaderToken);
                    } else {
                        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        if (csrfMeta) csrfMeta.setAttribute('content', newHeaderToken);
                    }
                }
                if (!response.ok) {
                    throw new Error('Server error: ' + response.status);
                }
                return response.text();
            })
            .then(data => {
                var divEl = document.getElementById('divEntryForm');
                if (divEl) {
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(data, 'text/html');
                    var newDiv = doc.getElementById('divEntryForm');
                    if (newDiv) {
                        divEl.innerHTML = newDiv.innerHTML;
                    } else {
                        divEl.innerHTML = data;
                    }

                    // Clear item code input so subsequent item codes can be entered immediately
                    var updatedItemEl = document.getElementById('txtItemCode');
                    if (updatedItemEl) {
                        updatedItemEl.value = '';
                    }

                    var codeNoEl = document.getElementById('txt_code_no');
                    if (codeNoEl) {
                        codeNoEl.focus();
                    } else if (updatedItemEl) {
                        updatedItemEl.focus();
                    }
                }
                _isFetchingItemResult = false;
                _lastFetchedItemCode = null;
                hideItemSpinner();
            })
            .catch(error => {
                console.error('Error:', error);
                _isFetchingItemResult = false;
                _lastFetchedItemCode = null;
                hideItemSpinner();
            });
        } else if (typeof Ajax !== 'undefined') {
            var oOptions = {
                method: "post",
                parameters: {'code': item_code, 'csrf_test_name': csrfToken},
                requestHeaders: csrfToken ? {'X-CSRF-TOKEN': csrfToken} : {},
                onFailure: function (oXHR, oJson) {
                    console.error("An error occurred: " + oXHR.status);
                    _isFetchingItemResult = false;
                    _lastFetchedItemCode = null;
                    hideItemSpinner();
                },
                onSuccess: function (transport) {
                    var response = transport.responseText;
                    var divEl = document.getElementById('divEntryForm');
                    if (divEl) {
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(response, 'text/html');
                        var newDiv = doc.getElementById('divEntryForm');
                        if (newDiv) {
                            divEl.innerHTML = newDiv.innerHTML;
                        } else {
                            divEl.innerHTML = response;
                        }

                        // Clear item code input
                        var updatedItemEl = document.getElementById('txtItemCode');
                        if (updatedItemEl) {
                            updatedItemEl.value = '';
                        }

                        var codeNoEl = document.getElementById('txt_code_no');
                        if (codeNoEl) {
                            codeNoEl.focus();
                        } else if (updatedItemEl) {
                            updatedItemEl.focus();
                        }
                    }
                    _isFetchingItemResult = false;
                    _lastFetchedItemCode = null;
                    hideItemSpinner();
                }
            };
            var oRequest = new Ajax.Updater({}, path + "result/resultentry/get_item_details_result", oOptions);
        }
    } else {
        _isFetchingItemResult = false;
        _lastFetchedItemCode = null;
        hideItemSpinner();
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
        _lastFetchedItemCode = null;
        formEl.target = '_self';
        formEl.action = path + 'result/resultentry/save_result_entry';
        formEl.submit();
    }
}

function editResult(id) {
    var row = document.getElementById('result_row_' + id);
    var consoleEl = document.getElementById('marksEntryConsole');
    var codeNoInput = document.getElementById('txt_code_no');
    var regNoInput = document.getElementById('txt_reg_no');
    var totalMarkInput = document.getElementById('totalMark');
    var hidResultIdConsole = document.getElementById('resultEntry_hidResultId');
    var hidResultIdEl = document.getElementById('hidResultId');
    var consoleTitle = document.getElementById('consoleTitle');
    var saveBtn = document.getElementById('saveButton');
    var cancelBtn = document.getElementById('cancelEditButton');
    var formEl = document.getElementById('resultEntryList');

    if (hidResultIdEl) hidResultIdEl.value = id;

    // Instant client-side edit mode if console inputs are available in DOM
    if (row && consoleEl && codeNoInput && regNoInput) {
        if (hidResultIdConsole) hidResultIdConsole.value = id;
        codeNoInput.value = row.getAttribute('data-code-no') || '';
        regNoInput.value = row.getAttribute('data-participant-id') || '';
        if (totalMarkInput) totalMarkInput.value = row.getAttribute('data-total-mark') || '';

        var rawMarks = row.getAttribute('data-marks') || '';
        var marks = rawMarks.split('#$#');
        var hidNoJudgeEl = document.getElementById('hidNoJudge');
        var numJudges = hidNoJudgeEl ? parseInt(hidNoJudgeEl.value) : marks.length;
        for (var i = 1; i <= numJudges; i++) {
            var markInput = document.getElementById('mark_' + i);
            if (markInput) {
                markInput.value = (marks[i - 1] !== undefined && marks[i - 1] !== '') ? marks[i - 1] : '';
            }
        }

        if (consoleTitle) {
            consoleTitle.innerHTML = '<i class="bi bi-pencil-square text-primary"></i> <strong class="text-dark small">Edit Participant Score</strong>';
        }
        if (saveBtn) {
            saveBtn.innerHTML = '<i class="bi bi-save2"></i> <span>Update</span>';
            saveBtn.title = 'Update Score';
        }
        if (cancelBtn) {
            cancelBtn.classList.remove('d-none');
        }

        consoleEl.classList.remove('d-none');
        consoleEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        codeNoInput.focus();
        return;
    }

    // Fallback: server-side submit
    if (formEl) {
        _lastFetchedItemCode = null;
        formEl.target = '_self';
        formEl.action = path + 'result/resultentry/edit_result_entry';
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
                _lastFetchedItemCode = null;
                formEl.target = '_self';
                formEl.action = path + 'result/resultentry/delete_result_entry';
                formEl.submit();
            }
        }
    });
}

function fncCancel() {
    var consoleEl = document.getElementById('marksEntryConsole');
    var codeNoInput = document.getElementById('txt_code_no');
    var regNoInput = document.getElementById('txt_reg_no');
    var totalMarkInput = document.getElementById('totalMark');
    var hidResultIdConsole = document.getElementById('resultEntry_hidResultId');
    var hidResultIdEl = document.getElementById('hidResultId');
    var consoleTitle = document.getElementById('consoleTitle');
    var saveBtn = document.getElementById('saveButton');
    var cancelBtn = document.getElementById('cancelEditButton');
    var hidCntItemList = document.getElementById('hidCntItemList');
    var hidTotalPart = document.getElementById('hidTotalPart');
    var formEl = document.getElementById('resultEntryList');

    if (consoleEl && codeNoInput && regNoInput) {
        if (hidResultIdConsole) hidResultIdConsole.value = '';
        if (hidResultIdEl) hidResultIdEl.value = '';
        codeNoInput.value = '';
        regNoInput.value = '';
        if (totalMarkInput) totalMarkInput.value = '';

        var hidNoJudgeEl = document.getElementById('hidNoJudge');
        var numJudges = hidNoJudgeEl ? parseInt(hidNoJudgeEl.value) : 0;
        for (var i = 1; i <= numJudges; i++) {
            var markInput = document.getElementById('mark_' + i);
            if (markInput) markInput.value = '';
        }

        if (consoleTitle) {
            consoleTitle.innerHTML = '<i class="bi bi-pencil-square text-primary"></i> <strong class="text-dark small">Enter Participant Score</strong>';
        }
        if (saveBtn) {
            saveBtn.innerHTML = '<i class="bi bi-check2-circle"></i> <span>Save</span>';
            saveBtn.title = 'Save Score (Enter)';
        }
        if (cancelBtn) {
            cancelBtn.classList.add('d-none');
        }

        var cnt = hidCntItemList ? parseInt(hidCntItemList.value) : 0;
        var total = hidTotalPart ? parseInt(hidTotalPart.value) : 0;
        if (total > 0 && cnt >= total) {
            consoleEl.classList.add('d-none');
        } else {
            codeNoInput.focus();
        }
        return;
    }

    if (formEl) {
        _lastFetchedItemCode = null;
        formEl.target = '_self';
        formEl.action = path + 'result/resultentry';
        formEl.submit();
    }
}

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
                formEl.target = '_self';
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
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Reset Confirmation?',
            html: 'Are you sure you want to reset confirmation for <br><span class="badge bg-primary fs-6 mt-2 py-2 px-3">' + message + '</span>?<br><br><small class="text-muted">This will reopen result entry and mark edits for this event.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-arrow-counterclockwise me-1"></i> Yes, Reset Confirmation',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            showClass: {
                popup: 'animate__animated animate__bounceIn'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp animate__faster'
            },
            customClass: {
                popup: 'rounded-4 shadow-lg border-0 p-4',
                confirmButton: 'btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm',
                cancelButton: 'btn btn-light rounded-pill px-4 py-2 fw-semibold text-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Resetting Status...',
                    text: 'Please wait while confirmation status is being reset.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                var formData = new FormData();
                formData.append('item_code', itemcode);

                fetch(path + "ajax/loadajax/reset_result_confirmation_status", {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === 'No') {
                        var btnEl = document.getElementById(element_id);
                        var disEl = document.getElementById(element_id + '_dis');
                        if (btnEl) btnEl.innerHTML = '<span class="text-muted">-</span>';
                        if (disEl) disEl.innerHTML = '<span class="badge bg-secondary bg-opacity-75 rounded-pill px-2.5 py-1">Pending</span>';

                        Swal.fire({
                            title: 'Reset Completed!',
                            html: 'Result confirmation for <strong>' + message + '</strong> has been reset successfully.',
                            icon: 'success',
                            timer: 2200,
                            showConfirmButton: false,
                            showClass: {
                                popup: 'animate__animated animate__zoomIn'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__zoomOut animate__faster'
                            },
                            customClass: {
                                popup: 'rounded-4 shadow-lg border-0'
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'Unable to reset confirmation status. Please try again.',
                            icon: 'error',
                            customClass: {
                                popup: 'rounded-4 shadow-lg border-0'
                            }
                        });
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while connecting to the server.',
                        icon: 'error',
                        customClass: {
                            popup: 'rounded-4 shadow-lg border-0'
                        }
                    });
                });
            }
        });
    } else {
        if (confirm('Do you really want to reset the Result confirmation status of ' + message + '?')) {
            var formData = new FormData();
            formData.append('item_code', itemcode);
            fetch(path + "ajax/loadajax/reset_result_confirmation_status", {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                var btnEl = document.getElementById(element_id);
                var disEl = document.getElementById(element_id + '_dis');
                if (btnEl) btnEl.innerHTML = '<span class="text-muted">-</span>';
                if (disEl) disEl.innerHTML = '<span class="badge bg-secondary bg-opacity-75 rounded-pill px-2.5 py-1">Pending</span>';
            });
        }
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
        formEl.target = '_blank';
        formEl.action = path + 'result/resultentry/printConfidentialReport';
        formEl.submit();
        formEl.target = '_self';
    }
}