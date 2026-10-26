// JavaScript Document
function getResultEntryBase() {
    if (typeof window !== 'undefined' && (window.location.href.indexOf('district/resultentry') !== -1 || window.location.pathname.indexOf('district/resultentry') !== -1)) {
        return 'district/resultentry/';
    }
    return 'result/resultentry/';
}

function moveFocus(currentInput, nextInputId, maxLen) {
    if (!currentInput) return;
    var max = maxLen || currentInput.maxLength || 2;
    if (max > 0 && currentInput.value && currentInput.value.length > max) {
        currentInput.value = currentInput.value.slice(0, max);
    }
    if (currentInput.value && currentInput.value.length >= max) {
        var nextEl = document.getElementById(nextInputId);
        if (nextEl) {
            nextEl.focus();
            if (typeof nextEl.select === 'function') {
                nextEl.select();
            }
        }
    }
}

function autoFocusOnFourDigits(input) {
    if (input && input.value.length === 4) {
        var nextEl = document.getElementById('txt_reg_no');
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

function getEntryBlockInfo(container) {
    if (!container) return null;
    if (container.getAttribute('data-entry-blocked') !== 'yes') return null;
    return {
        reason: container.getAttribute('data-entry-block-reason') || '',
        message: container.getAttribute('data-entry-block-message') || ''
    };
}

function showProgramInProgressAlert(message) {
    if (typeof Swal === 'undefined') return;
    var msg = message || 'The Stage Manager has started this program but has not completed it yet.';
    Swal.fire({
        title: 'Mark Entry Locked',
        html: '<div class="text-start">' +
            '<p class="fw-semibold mb-1">This program is currently <span class="text-warning">in progress</span> on stage.</p>' +
            '<p class="mb-1 text-muted">' + msg.replace(/</g, '&lt;') + '</p>' +
            '<p class="mb-0 text-muted">Please type a different item code, or come back after the Stage Manager completes the program.</p>' +
            '</div>',
        icon: 'warning',
        confirmButtonText: 'OK',
        customClass: { popup: 'rounded-4 shadow-lg border-0' }
    });
}

function handleItemLoadBlock(container, item_code) {
    var info = getEntryBlockInfo(container);
    if (!info) return false;

    var itemCodeInput = document.getElementById('txtItemCode');
    if (itemCodeInput) {
        if (item_code) itemCodeInput.value = item_code;
        itemCodeInput.focus();
        if (typeof itemCodeInput.select === 'function') itemCodeInput.select();
    }

    if (info.reason === 'program_in_progress') {
        showProgramInProgressAlert(info.message);
    }
    return true;
}

function onItemCodeInput(el) {
    if (el && el.value.length === 3) {
        fetch_item_details_result();
    }
}

var _isFetchingItemResult = false;
var _lastFetchedItemCode = null;

function fetch_item_details_result(force, itemCodeParam)
{
    var item_code = (itemCodeParam && String(itemCodeParam).trim() !== '') ? String(itemCodeParam).trim() : '';
    if (!item_code) {
        var itemCodeEl = document.getElementById('txtItemCode');
        if (itemCodeEl && itemCodeEl.value.trim() !== '') {
            item_code = itemCodeEl.value.trim();
        }
    }
    if (!item_code) {
        var hidItemEl = document.getElementById('hidItemId');
        if (hidItemEl && hidItemEl.value.trim() !== '') {
            item_code = hidItemEl.value.trim();
        }
    }
    if (item_code) {
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
        targetUrl += getResultEntryBase() + "get_item_details_result";

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

                    // If mark entry is blocked (e.g. program started but not finished),
                    // keep the Code input value/focus and raise the Sweet Alert.
                    if (handleItemLoadBlock(divEl, item_code)) {
                        _isFetchingItemResult = false;
                        _lastFetchedItemCode = item_code;
                        hideItemSpinner();
                        return;
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

                        // If mark entry is blocked (e.g. program started but not finished),
                        // keep the Code input value/focus and raise the Sweet Alert.
                        if (handleItemLoadBlock(divEl, item_code)) {
                            _isFetchingItemResult = false;
                            _lastFetchedItemCode = item_code;
                            hideItemSpinner();
                            return;
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
            var oRequest = new Ajax.Updater({}, path + getResultEntryBase() + "get_item_details_result", oOptions);
        }
    } else {
        _isFetchingItemResult = false;
        _lastFetchedItemCode = null;
        hideItemSpinner();
        return false;
    }
}

function setSaveButtonLoading(loading, isEditing) {
    var saveBtn = document.getElementById('saveButton');
    if (!saveBtn) return;

    var spinner = document.getElementById('saveBtnSpinner');
    var icon = document.getElementById('saveBtnIcon');
    var text = document.getElementById('saveBtnText');

    if (!spinner || !icon || !text) {
        saveBtn.innerHTML = '<span id="saveBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span> ' +
            '<i id="saveBtnIcon" class="bi ' + (isEditing ? 'bi-save2' : 'bi-check2-circle') + '"></i> ' +
            '<span id="saveBtnText">' + (isEditing ? 'Update' : 'Save') + '</span>';
        spinner = document.getElementById('saveBtnSpinner');
        icon = document.getElementById('saveBtnIcon');
        text = document.getElementById('saveBtnText');
    }

    if (loading) {
        saveBtn.disabled = true;
        saveBtn.classList.add('disabled');
        if (spinner) {
            spinner.classList.remove('d-none');
            spinner.classList.add('d-inline-block');
        }
        if (icon) {
            icon.classList.add('d-none');
        }
        if (text) {
            text.textContent = isEditing ? 'Updating...' : 'Saving...';
        }
    } else {
        saveBtn.disabled = false;
        saveBtn.classList.remove('disabled');
        if (spinner) {
            spinner.classList.remove('d-inline-block');
            spinner.classList.add('d-none');
        }
        if (icon) {
            icon.classList.remove('d-none');
            icon.className = 'bi ' + (isEditing ? 'bi-save2' : 'bi-check2-circle');
        }
        if (text) {
            text.textContent = isEditing ? 'Update' : 'Save';
        }
    }
}

function fncSaveResultEntry()
{
    var hidResultIdConsole = document.getElementById('resultEntry_hidResultId');
    var isEditing = hidResultIdConsole && hidResultIdConsole.value && hidResultIdConsole.value.trim() !== '';

    // Get elements using modern JavaScript
    var codeNoEl = document.getElementById('txt_code_no');
    var regNoEl = document.getElementById('txt_reg_no');
    var totalMarkEl = document.getElementById('totalMark');
    var hidNoJudgeEl = document.getElementById('hidNoJudge');
    var formEl = document.getElementById('resultEntry');
    var hidItemIdEl = document.getElementById('hidItemId');
    var txtItemCodeEl = document.getElementById('txtItemCode');

    if (!codeNoEl || trim(codeNoEl.value) == '')
    {
        setSaveButtonLoading(false, isEditing);
        Swal.fire("Oops!", "Please enter the code", "error");
        if (codeNoEl) codeNoEl.focus();
        return;
    }
    if (!regNoEl || trim(regNoEl.value) == '')
    {
        setSaveButtonLoading(false, isEditing);
        Swal.fire("Oops!", "Please enter the register number", "error");
        if (regNoEl) regNoEl.focus();
        return;
    }

    var total_marks = 0;
    var numJudges = hidNoJudgeEl ? parseInt(hidNoJudgeEl.value) : 0;
    
    for (var i = 1; i <= numJudges; i++) {
        var markEl = document.getElementById('mark_' + i);
        if (!markEl || trim(markEl.value) == '')
        {
            setSaveButtonLoading(false, isEditing);
            Swal.fire("Oops!", "Please enter the mark " + i, "error");
            if (markEl) markEl.focus();
            return;
        }
        if (trim(markEl.value) * 1 > 100)
        {
            setSaveButtonLoading(false, isEditing);
            Swal.fire("Oops!", "Please enter valid mark " + i, "error");
            if (markEl) markEl.focus();
            return;
        }

        total_marks += markEl.value * 1;
    }
    if (!totalMarkEl || trim(totalMarkEl.value) == '')
    {
        setSaveButtonLoading(false, isEditing);
        Swal.fire("Oops!", "Please enter the total marks", "error");
        if (totalMarkEl) totalMarkEl.focus();
        return;
    }

    if (total_marks != totalMarkEl.value)
    {
        setSaveButtonLoading(false, isEditing);
        Swal.fire("Oops!", "Please check the total marks entered", "error");
        if (totalMarkEl) totalMarkEl.focus();
        return;
    }

    // Activate the spinner on Save button!
    setSaveButtonLoading(true, isEditing);

    var itemCode = (hidItemIdEl && hidItemIdEl.value) ? hidItemIdEl.value : ((txtItemCodeEl && txtItemCodeEl.value) ? txtItemCodeEl.value : '');

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
    targetUrl += getResultEntryBase() + "save_result_entry";

    if (typeof fetch !== 'undefined') {
        var formData = new FormData();
        formData.append('txt_code_no', codeNoEl.value.trim());
        formData.append('txt_reg_no', regNoEl.value.trim());
        formData.append('totalMark', totalMarkEl.value.trim());
        formData.append('hidItemId', itemCode);
        formData.append('hidNoJudge', numJudges);
        formData.append('hidResultId', hidResultIdConsole ? hidResultIdConsole.value.trim() : '');
        for (var j = 1; j <= numJudges; j++) {
            var mEl = document.getElementById('mark_' + j);
            if (mEl) formData.append('mark_' + j, mEl.value.trim());
        }
        if (csrfToken) {
            formData.append('csrf_test_name', csrfToken);
        }

        fetch(targetUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
            },
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
                throw new Error('Server returned status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            // Save process is complete -> hide spinner!
            setSaveButtonLoading(false, false);

            if (data.status === 'success') {
                // Show success toast notification
                if (typeof Toastify !== 'undefined') {
                    Toastify({
                        text: data.message || "Result updated successfully!",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #10b981, #059669)",
                            borderRadius: "8px",
                            boxShadow: "0 4px 12px rgba(16, 185, 129, 0.25)"
                        }
                    }).showToast();
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || 'Result updated successfully!',
                        showConfirmButton: false,
                        timer: 2500
                    });
                }

                // Smoothly refresh the results roster table & progress
                fetch_item_details_result(true, itemCode);
            } else {
                Swal.fire("Oops!", data.message || "Failed to save result.", "error");
            }
        })
        .catch(err => {
            console.warn('Fetch save failed, falling back to form submit:', err);
            // Fallback to standard form submit
            if (!formEl) {
                formEl = document.forms['resultEntry'] || document.querySelector('form#resultEntry');
            }
            if (formEl) {
                if (hidItemIdEl && (!hidItemIdEl.value || hidItemIdEl.value === '')) {
                    hidItemIdEl.value = itemCode;
                }
                var fieldsToEnsure = ['txt_code_no', 'txt_reg_no', 'totalMark', 'hidItemId', 'hidNoJudge', 'resultEntry_hidResultId'];
                for (var j = 1; j <= numJudges; j++) {
                    fieldsToEnsure.push('mark_' + j);
                }
                fieldsToEnsure.forEach(function(fId) {
                    var el = document.getElementById(fId);
                    if (el) {
                        var fieldName = el.name || fId;
                        if (!formEl.contains(el)) {
                            var hidden = formEl.querySelector('input[name="' + fieldName + '"]');
                            if (!hidden) {
                                hidden = document.createElement('input');
                                hidden.type = 'hidden';
                                hidden.name = fieldName;
                                formEl.appendChild(hidden);
                            }
                            hidden.value = el.value;
                        }
                    }
                });

                _lastFetchedItemCode = null;
                formEl.target = '_self';
                formEl.action = targetUrl;
                formEl.submit();
            } else {
                setSaveButtonLoading(false, isEditing);
                Swal.fire("Error", "Could not connect to server. Please try again.", "error");
            }
        });
    } else {
        // Fallback for browsers without fetch
        if (!formEl) {
            formEl = document.forms['resultEntry'] || document.querySelector('form#resultEntry');
        }
        if (formEl) {
            if (hidItemIdEl && (!hidItemIdEl.value || hidItemIdEl.value === '')) {
                hidItemIdEl.value = itemCode;
            }
            _lastFetchedItemCode = null;
            formEl.target = '_self';
            formEl.action = targetUrl;
            formEl.submit();
        }
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
        setSaveButtonLoading(false, true);
        if (saveBtn) {
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
        formEl.action = path + getResultEntryBase() + 'edit_result_entry';
        formEl.submit();
    }
}

var _isDeletingResult = false;

function deleteResult(id) {
    if (_isDeletingResult) return;

    var row = document.getElementById('result_row_' + id);
    var regNo = row ? (row.getAttribute('data-participant-id') || '') : '';

    Swal.fire({
        title: 'Delete Result',
        html: '<p class="small text-muted mb-0">Do you really want to delete this result' + (regNo ? ' <span class="fw-bold">(Reg No: ' + regNo + ')</span>' : '') + '?</p>' +
              '<p class="small text-danger fw-semibold mt-2 mb-0">இந்த முடிவை நீக்க நீங்கள் உறுதியாக இருக்கிறீர்களா?</p>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="bi bi-trash3 me-1"></i> Yes, Delete',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'rounded-4 shadow-lg border-0',
            confirmButton: 'btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm',
            cancelButton: 'btn btn-light rounded-pill px-4 py-2 fw-semibold text-secondary'
        },
        buttonsStyling: false
    }).then(function(result) {
        if (result.isConfirmed) {
            performDeleteResult(id);
        }
    });
}

function performDeleteResult(id) {
    if (_isDeletingResult) return;
    _isDeletingResult = true;

    var hidResultIdEl = document.getElementById('hidResultId');
    var itemCodeEl = document.getElementById('hidItemId');
    var itemCode = itemCodeEl ? itemCodeEl.value : '';
    if (hidResultIdEl) hidResultIdEl.value = id;

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
    targetUrl += getResultEntryBase() + 'delete_result_entry';

    Swal.fire({
        title: 'Deleting Result',
        html: '<div class="delete-progress-wrap">' +
                '<div class="delete-progress-bin">' +
                  '<div class="delete-progress-lid"></div>' +
                  '<div class="delete-progress-body"></div>' +
                  '<div class="delete-progress-paper delete-progress-paper-a"></div>' +
                  '<div class="delete-progress-paper delete-progress-paper-b"></div>' +
                  '<div class="delete-progress-paper delete-progress-paper-c"></div>' +
                '</div>' +
                '<div class="delete-progress-bar"><span></span></div>' +
                '<p class="delete-progress-text">Deleting, please wait...</p>' +
                '<p class="small text-muted mb-0 mt-1">முடிவு நீக்கப்படுகிறது...</p>' +
              '</div>',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        showCloseButton: false,
        customClass: { popup: 'rounded-4 shadow-lg border-0 p-3' }
    });

    var csrfToken = (typeof getCsrfToken === 'function') ? getCsrfToken() : '';
    var formData = new FormData();
    formData.append('hidResultId', id);
    formData.append('hidItemId', itemCode || '');
    if (csrfToken) {
        formData.append('csrf_test_name', csrfToken);
    }

    fetch(targetUrl, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
        },
        body: formData
    })
    .then(function(response) {
        var newHeaderToken = response.headers.get('X-CSRF-TOKEN');
        if (newHeaderToken) {
            if (typeof updateCsrfToken === 'function') {
                updateCsrfToken(newHeaderToken);
            } else {
                var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (csrfMeta) csrfMeta.setAttribute('content', newHeaderToken);
            }
        }
        return response.json();
    })
    .then(function(data) {
        _isDeletingResult = false;
        Swal.close();
        if (data.status === 'success') {
            if (typeof Toastify !== 'undefined') {
                Toastify({
                    text: data.message || 'Result deleted successfully!',
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #10b981, #059669)",
                        borderRadius: "8px",
                        boxShadow: "0 4px 12px rgba(16, 185, 129, 0.25)"
                    }
                }).showToast();
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message || 'Result deleted successfully!',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
            fetch_item_details_result(true, itemCode);
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Failed',
                    text: data.message || 'Failed to delete the result.',
                    icon: 'error',
                    customClass: { popup: 'rounded-4 shadow-lg border-0' }
                });
            }
        }
    })
    .catch(function(err) {
        console.warn('AJAX delete failed, falling back to form submit:', err);
        _isDeletingResult = false;
        Swal.close();
        var formEl = document.getElementById('resultEntryList');
        if (formEl) {
            _lastFetchedItemCode = null;
            formEl.target = '_self';
            formEl.action = targetUrl;
            formEl.submit();
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Error',
                text: 'Could not connect to server. Please try again.',
                icon: 'error',
                customClass: { popup: 'rounded-4 shadow-lg border-0' }
            });
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
        setSaveButtonLoading(false, false);
        if (saveBtn) {
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
        formEl.action = path + getResultEntryBase().replace(/\/$/, '');
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
                formEl.action = path + getResultEntryBase() + 'confirm_result_entry';
                formEl.submit();
            }
        }
    });
}

function getItemResult(itemcode)
{
    if (!itemcode) return;
    var baseUrl = (typeof path !== 'undefined' && path) ? path : '';
    window.location.href = baseUrl + getResultEntryBase() + 'index/' + encodeURIComponent(itemcode);
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
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
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
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
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
        formEl.action = path + getResultEntryBase() + 'printConfidentialReport';
        formEl.submit();
        formEl.target = '_self';
    }
}

function selectParticipantFast(pid) {
    var regEl = document.getElementById('txt_reg_no');
    if (regEl) {
        regEl.value = pid;
        var m1 = document.getElementById('mark_1');
        if (m1) m1.focus();
    }
}