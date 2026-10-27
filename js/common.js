// JavaScript Document
function ltrim(str) {
    for (var k = 0; k < str.length && isWhitespace(str.charAt(k)); k++)
        ;
    return str.substring(k, str.length);
}
function rtrim(str) {
    for (var j = str.length - 1; j >= 0 && isWhitespace(str.charAt(j)); j--)
        ;
    return str.substring(0, j + 1);
}
function trim(str) {
    return ltrim(rtrim(str));
}
function isAlphaNumeric(val) {
    if (val.match(/^[a-zA-Z0-9]+$/)) {
        return true;
    } else {
        return false;
    }
}
function isWhitespace(charToCheck) {
    var whitespaceChars = " \t\n\r\f";
    return (whitespaceChars.indexOf(charToCheck) != -1);
}
var delete_msg = 'Do you really want to delete?';
/*
 *Fucntion for checking the field value having the valid email
 */
function isValidEmail(email) {
    var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    if (filter.test(email)) {
        return true;
    } else {
        return false;
    }
}
function fncCancel(url) {
    window.location = url;
}
function height_adjust(id) {
    if ($(id).getHeight() < 500) {
        if (window.navigator.userAgent.indexOf('MSIE 6.0') != -1)
            $(id).style.height = '500px';
        $(id).style.minHeight = '500px';
    }
}
function show_hide(ele) {
    if ($(ele).style.display == 'none') {
        $(ele).appear();
    } else {
        $(ele).fade();
    }
}
function show_menu(el) {
    el.getElementsByTagName('ul')[0].style.left = 'auto';
    el.getElementsByTagName('ul')[0].style.display = 'block';
}
function hide_menu(el) {
    el.getElementsByTagName('ul')[0].style.left = '-999em';
}
function ajax_loder(ele) {
    $(ele).innerHTML = '<img src="' + image_path + 'ajax_loader.gif">';
}
function ajax_loder2(ele) {
    
    var element = $(ele);
    if (element) {
        
        if (typeof image_path !== 'undefined') {
            element.innerHTML = '<img src="' + image_path + 'ajax_loader2.gif">';
        } else {
            element.innerHTML = '<img src="images/ajax_loader2.gif">';
        }
        
    } else {
        console.error('ajax_loder2: Element not found:', ele);
        console.error('ajax_loder2: Available elements with similar IDs:');
        // Debug: List all elements with IDs starting with 'confirm'
        var allElements = document.getElementsByTagName('*');
        for (var i = 0; i < allElements.length; i++) {
            if (allElements[i].id && allElements[i].id.indexOf('confirm') === 0) {
                console.error('Found element:', allElements[i].id);
            }
        }
    }
}
function compareDates(startDate, endDate) {
    var point1 = 0;
    var point2 = 0;
    var arrDate1 = startDate.split("-");
    var useDate1 = new Date(arrDate1[2], arrDate1[1] - 1, arrDate1[0]);
    var arrDate2 = endDate.split("-");
    var useDate2 = new Date(arrDate2[2], arrDate2[1] - 1, arrDate2[0]);
    var year1 = arrDate1[2];
    var month1 = arrDate1[1];
    var day1 = arrDate1[0];
    var year2 = arrDate2[2];
    var month2 = arrDate2[1];
    var day2 = arrDate2[0];
    if (year1 > year2) {
        point1++;
    } else if (year1 < year2) {
        point2++;
    } else {
        point1++;
        point2++;
    }
    if (month1 > month2 && point1 > 0) {
        point1++;
    } else if (month1 < month2 && point2 > 0) {
        point2++;
    } else {
        point1++;
        point2++;
    }
    if (day1 > day2 && point1 > 1) {
        point1++;
    } else if (day1 < day2 && point2 > 1) {
        point2++;
    } else {
        point1++;
        point2++;
    }
    if (point1 > point2)
        return 0;
    else if (point1 <= point2)
        return 1;
}
function isValidURLParse(url) {
    var filterurl = /^[-a-zA-Z0-9@:%_\+.~#?&//=]+$/;
    if (filterurl.test(url)) {
        return true;
    } else {
        return false;
    }
}
function delete_confirm(url, msg) {
    swal({
        title: "Are you sure?",
        text: msg,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#d33",
        reverseButtons: true
    }).then(function(willDelete) {
        if (willDelete) {
            window.location = url;
        } else {
            swal("Your action has been canceled.", {
                icon: "info",
            });
        }
    }).catch(function(error) {
        
    });
}


function addteacher() {
    var id = parseInt($('hidTeachers').value) + 1;
    
    var porow = document.createElement('div');
    porow.className = 'teachersTextBox';
    porow.style.width = '100%';

    var rowDiv = document.createElement('div');
    rowDiv.className = 'row mb-2';

    // Create Team Manager column (Top label, bottom input)
    var col1 = document.createElement('div');
    col1.className = 'col-md-6 mb-3';

    var lbl1 = document.createElement('label');
    lbl1.className = 'form-label fw-bold text-muted small text-uppercase';
    lbl1.textContent = 'Team Manager ' + id;
    col1.appendChild(lbl1);

    var teacher = document.createElement('input');
    teacher.type = 'text';
    teacher.id = 'txtTeacher_' + id;
    teacher.name = 'txtTeacher_' + id;
    teacher.className = 'form-control';
    teacher.placeholder = 'Enter teacher name';
    teacher.onkeyup = function() { this.value = this.value.toUpperCase(); };
    col1.appendChild(teacher);

    // Create Phone column (Top label, bottom input with remove button)
    var col2 = document.createElement('div');
    col2.className = 'col-md-6 mb-3';

    var lbl2 = document.createElement('label');
    lbl2.className = 'form-label fw-bold text-muted small text-uppercase';
    lbl2.textContent = 'Phone';
    col2.appendChild(lbl2);

    var inputGroup = document.createElement('div');
    inputGroup.className = 'input-group';

    var phone = document.createElement('input');
    phone.type = 'text';
    phone.id = 'txtPhone_' + id;
    phone.name = 'txtPhone_' + id;
    phone.className = 'form-control';
    phone.placeholder = 'Phone number';
    phone.maxLength = '11';
    phone.onkeypress = function(event) { return numbersonly(this, event || window.event, false); };
    inputGroup.appendChild(phone);

    var removeBtn = document.createElement('button');
    removeBtn.className = 'btn btn-outline-danger';
    removeBtn.type = 'button';
    removeBtn.textContent = '✕';
    removeBtn.title = 'Remove';
    removeBtn.onclick = function() {
        porow.remove();
    };
    inputGroup.appendChild(removeBtn);

    col2.appendChild(inputGroup);

    // Append columns to row
    rowDiv.appendChild(col1);
    rowDiv.appendChild(col2);

    porow.appendChild(rowDiv);

    // Append to teachers list
    var addBtnDiv = document.getElementById("teachersAddBtnDiv");
    if (addBtnDiv && $("teachersRow")) {
        $("teachersRow").insertBefore(porow, addBtnDiv);
    } else if ($("teachersRow")) {
        $("teachersRow").appendChild(porow);
    }

    // Update hidden field
    $('hidTeachers').value = id;
}

function addTotal()
{
    var total = 0;
    var categories = [
        ['txtTotalLP', 'divLP'],
        ['txtTotalUP', 'divUP'],
        ['txtTotalHS', 'divHS'],
        ['txtTotalHSS', 'divHSS'],
        ['txtTotalVHSS', 'divVHSC']
    ];
    categories.forEach(function (category) {
        var input = document.getElementById(category[0]);
        var container = document.getElementById(category[1]);
        if (input && container && container.style.display !== 'none' && trim(input.value) !== '') {
            total += parseInt(input.value, 10) || 0;
        }
    });
    var totalInput = document.getElementById('txtTotal');
    if (totalInput) totalInput.value = total;
}


function numbersonly(myfield, e, dec) {
    var key;
    var keychar;

    if (window.event)
        key = window.event.keyCode;
    else if (e)
        key = e.which;
    else
        return true;
    keychar = String.fromCharCode(key);

    var val = myfield.value;
    if (val && keychar == '.') {
        if (val.split(".").length > 1) {
            return false;
        }
    }

    // control keys
    if ((key == null) || (key == 0) || (key == 8) || (key == 9) || (key == 13) || (key == 27))
        return true;

    // numbers
    else if ((("0123456789").indexOf(keychar) > -1))
        return true;

    // decimal point jump
    else if ((dec) && (keychar == "."))
    {
        //myfield.form.elements[dec].focus();
        return true;
    }
    else
        return false;
}

function change_text_max_to_focus(frm_txt, to_txt, obj)
{
    var one = String(obj.value).charAt(0);
    if (one == 1 && $('txtClass').value > 10)
        max_chr = 4;
    else
        max_chr = 3;
    if ($(frm_txt).value.length >= max_chr)
    {
        $(to_txt).focus();
    }
}

function fetch_school_details()
{
    var schoolCodeEl = $('txtSchoolCode');
    var school_code = schoolCodeEl ? trim(schoolCodeEl.value) : '';
    if (school_code === '' || school_code === '0') {
        if (typeof hideGlobalLoader === 'function') {
            hideGlobalLoader();
        }
        return false;
    }
    if (typeof showGlobalLoader === 'function') {
        showGlobalLoader('Fetching details...');
    }
    ajax_loder2('divSchoolCode');
    var oOptions = {
        method: "post",
        parameters: {'code': school_code},
        onFailure: function (oXHR, oJson) {
            if (typeof hideGlobalLoader === 'function') hideGlobalLoader();
        },
        onComplete: function () {
            if (typeof hideGlobalLoader === 'function') hideGlobalLoader();
        },
        onSuccess: function (transport) {
            var response = transport.responseText;

            $('divEntryForm').innerHTML = response;

            // Reinitialize listeners and update visibility states after DOM content changes
            if (typeof initializePage === 'function') {
                initializePage();
            }

            // Close School Details modal when school data is loaded
            var schoolModal = bootstrap.Modal.getInstance(document.getElementById('schoolDetailsModal'));
            if (schoolModal) {
                schoolModal.hide();
            }
            if (typeof hideGlobalLoader === 'function') hideGlobalLoader();
        }
    };
    var oRequest = new Ajax.Updater({
        //success: "divCustomerInfo"
    }, path + "schools/registration/get_school_details", oOptions);
}


function fetch_special_order_school_details()
{
    var schoolCodeEl = $('txtSchoolCode');
    var school_code = schoolCodeEl ? trim(schoolCodeEl.value) : '';
    if (school_code === '' || school_code === '0') {
        if (typeof hideGlobalLoader === 'function') {
            hideGlobalLoader();
        }
        return false;
    }
    // Skip re-fetching when nothing changed (avoids duplicate request on blur).
    if (schoolCodeEl && schoolCodeEl.getAttribute('data-soe-last') === school_code) {
        if (typeof hideGlobalLoader === 'function') {
            hideGlobalLoader();
        }
        return false;
    }
    if (typeof showGlobalLoader === 'function') {
        showGlobalLoader('Fetching details...');
    }
    var oOptions = {
        method: "post",
        parameters: {'code': school_code},
        onFailure: function (oXHR, oJson) {
            swal ( "Oops!" ,  "An error occurred: " + oXHR.status,  "error" );
            if (typeof hideGlobalLoader === 'function') hideGlobalLoader();
        },
        onComplete: function () {
            if (typeof hideGlobalLoader === 'function') hideGlobalLoader();
        },
        onSuccess: function (transport) {
            var response = transport.responseText;

            var current = document.getElementById('divEntryForm');
            if (current) {
                var temp = document.createElement('div');
                temp.innerHTML = response;
                // The AJAX response is the full view which is itself wrapped in
                // <div id="divEntryForm">; swap the content instead of nesting.
                var node = temp.querySelector('#divEntryForm') || temp.firstElementChild;
                current.innerHTML = node ? node.innerHTML : response;
            }

            // Reinitialize listeners and update visibility states after DOM content changes
            if (typeof initializePage === 'function') {
                initializePage();
            }

            // Close School Details modal when school data is loaded
            var schoolModal = bootstrap.Modal.getInstance(document.getElementById('schoolDetailsModal'));
            if (schoolModal) {
                schoolModal.hide();
            }
            if (typeof hideGlobalLoader === 'function') hideGlobalLoader();
        }
    };
    var oRequest = new Ajax.Updater({
        //success: "divCustomerInfo"
    }, path + "schools/special_order_entry/get_school_details", oOptions);
}



function fncCheckSchoolDeatils() {
    if (!isValidEmail($('txtSchoolEmail').value) && $('txtSchoolEmail').value != '')
    {
        swal({
            title: "Oops!",
            text: "Please Enter a Valid E-mail!!",
            icon: "error",
            button: "OK"
        });
        $('txtSchoolEmail').focus();
        return false;

    }


    if ((parseInt($('txtStandardFrom').value) > parseInt($('txtStandardTo').value)) || (parseInt($('txtStandardFrom').value) == 0 || parseInt($('txtStandardTo').value) == 0)) {
        swal({
            title: "Oops!",
            text: "Please select valid standards",
            icon: "error",
            button: "OK"
        });
        $('txtStandardFrom').focus();
        return false;
    }
    if (check_div('LP')) {
        if ($('txtTotalLP').value == '' || trim($('txtTotalLP').value) <= 0) {
            swal ( "Oops!" ,  "Please enter number of LP students" ,  "error" )
        //alert('Please enter number of LP students');
            $('txtTotalLP').focus();
            return false;
        }
    } else {
        if (trim($('txtTotalLP').value) != '' && trim($('txtTotalLP').value) != 0) {
            swal ( "Oops!" ,  "You are not selected LP standards. Please clear the number of LP students" ,  "error" )
        //alert('You are not selected LP standards. Please clear the number of LP students');
            $('txtTotalLP').focus();
            return false;
        }
    }
    if (check_div('UP')) {
        if ($('txtTotalUP').value == '' || trim($('txtTotalUP').value) <= 0) {
            swal ( "Oops!" ,  "Please enter number of UP students" ,  "error" )
        //alert('Please enter number of UP students');
            $('txtTotalUP').focus();
            return false;
        }
    } else {
        if (trim($('txtTotalUP').value) != '' && trim($('txtTotalUP').value) != 0) {
            swal ( "Oops!" ,  "You are not selected UP standards. Please clear the number of UP students" ,  "error" )
        //alert('You are not selected UP standards. Please clear the number of UP students');
            $('txtTotalUP').focus();
            return false;
        }
    }

    if (check_div('HS')) {
        if ($('txtTotalHS').value == '' || trim($('txtTotalHS').value) <= 0) {
            swal ( "Oops!" ,  "Please enter number of HS students" ,  "error" )
        //alert('Please enter number of HS students');
            $('txtTotalHS').focus();
            return false;
        }
    } else {
        if (trim($('txtTotalHS').value) != '' && trim($('txtTotalHS').value) != 0) {
            swal ( "Oops!" ,  "You are not selected HS standards. Please clear the number of HS students" ,  "error" )
        //alert('You are not selected HS standards. Please clear the number of HS students');
            $('txtTotalHS').focus();
            return false;
        }
    }

    if (check_div('HSS')) {
        if ($('txtTotalHSS').value == '' || trim($('txtTotalHSS').value) <= 0) {
            if ($('txtTotalVHSS').value == '' || trim($('txtTotalVHSS').value) <= 0) {
                swal ( "Oops!" ,  "Please enter number of HSS or VHSS students" ,  "error" )
        //alert('Please enter number of HSS or VHSS students');
                $('txtTotalHSS').focus();
                return false;
            }
        }
    } else {
        if ((trim($('txtTotalHSS').value) != '' || trim($('txtTotalVHSS').value) != '') && (trim($('txtTotalHSS').value) != 0 || trim($('txtTotalVHSS').value) != 0)) {
            swal ( "Oops!" ,  "You are not selected HSS or VHSS standards. Please clear the number of HSS or VHSS students" ,  "error" )
        //alert('You are not selected HSS or VHSS standards. Please clear the number of HSS or VHSS students');
            $('txtTotalHSS').focus();
            return false;
        }
    }
    if (trim($('txtPrincipal').value) == '' && trim($('txtHeadmaster').value) == '')
    {
        swal ( "Oops!" ,  "Please enter Principal/Headmaster" ,  "error" )
        //alert('Please enter Principal/Headmaster');
        $('txtPrincipal').focus();
        return false;
    }
    if (trim($('txtPrincipal').value) != '' && trim($('txtPrincipalPhone').value) == '')
    {
        swal ( "Oops!" ,  "Please enter Principal Phone number" ,  "error" )
        //alert('Please enter Principal Phone number');
        $('txtPrincipalPhone').focus();
        return false;
    }
    if (trim($('txtHeadmaster').value) != '' && trim($('txtHeadmasterPhone').value) == '')
    {
        swal ( "Oops!" ,  "Please enter Headmaster Phone number" ,  "error" )
        //alert('Please enter Headmaster Phone number');
        $('txtHeadmasterPhone').focus();
        return false;
    }
    var name = '';
    var phone = '';
    var phone_con = false;
    var name_con = false;
    for (var j = 1; j <= $('hidTeachers').value; j++) {
        name = 'txtTeacher_' + j;
        phone = 'txtPhone_' + j;
        if ($(name)) {
            if (trim($(name).value) != '') {
                name_con = true;
            }
            if ($(phone) && trim($(phone).value) != '') {
                if (trim($(name).value) == '')
                {
                    swal ( "Oops!" ,  "Please enter the Team Manager name" ,  "error" )
                    //alert('Please enter the Team Manager name');
                    $(name).focus();
                    return false;
                }
                phone_con = true;
            }
        }
    }
    if (!name_con)
    {
        swal ( "Oops!" ,  "Please enter atleast one Team Manager" ,  "error" )
        //alert('Please enter atleast one Team Manager');
        $('txtTeacher_1').focus();
        return false;
    }
    if (!phone_con)
    {
        swal ( "Oops!" ,  "Please enter atleast one Team Manager phone" ,  "error" )
        //alert('Please enter atleast one Team Manager phone');
        return false;
    }
    swal({
        title: "Save School Profile",
        text: "Are you sure you want to save school profile details?",
        icon: "info",
        buttons: {
            cancel: {
                text: "Cancel",
                value: null,
                visible: true,
                className: "btn btn-secondary",
                closeModal: true,
            },
            confirm: {
                text: "Yes, Save",
                value: true,
                visible: true,
                className: "btn btn-primary",
                closeModal: true
            }
        }
    }).then(function(isConfirm) {
        if (isConfirm) {
            $('formSchool').submit();
        }
    });
}

function check_div(std)
{
    if (std == 'LP') {
        if ($('txtStandardFrom').value == 1) {
            return true;
        } else {
            return false;
        }
    }

    if (std == 'UP') {
        if ($('txtStandardTo').value > 4 && $('txtStandardFrom').value <= 5) {
            return true;
        } else {
            return false;
        }
    }
    if (std == 'HS') {
        if ($('txtStandardTo').value > 7 && $('txtStandardFrom').value <= 8) {
            return true;
        } else {
            return false;
        }
    }
    if (std == 'HSS') {
        if ($('txtStandardTo').value > 10) {
            return true;
        } else {
            return false;
        }
    }
}

function fncEditSchoolDeatils() {
    if (window.location.href.indexOf('schools/profile') !== -1) {
        $('formSchool').action = path + 'schools/profile/edit';
    } else {
        $('formSchool').action = path + 'schools/registration/edit_school_details';
    }
    $('formSchool').submit();
}

function showItemValidationError(msg) {
    var saveBtn = document.getElementById('save_participant_btn');
    if (saveBtn) {
        saveBtn.disabled = false;
        var defaultText = (document.getElementById('hidADNO') && document.getElementById('hidADNO').value) ? 'Update Participant' : 'Save Participant';
        saveBtn.innerHTML = saveBtn.getAttribute('data-original-text') || defaultText;
    }

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Registration Not Saved',
            html: '<div style="text-align:left;font-size:0.92rem;">' +
                  '<div style="margin-bottom:0.4rem;font-weight:600;">The participant was not saved. Please fix the following:</div>' +
                  '<ul style="margin:0;padding-left:1.2rem;"><li>' + msg + '</li></ul>' +
                  '<div style="margin-top:0.6rem;color:#64748b;font-size:0.85rem;">Please modify the item and try saving again.</div></div>',
            confirmButtonText: 'OK',
            target: 'body'
        });
    } else if (typeof swal !== 'undefined') {
        swal('Registration Not Saved', msg, 'error');
    } else {
        alert(msg);
    }
}

function validateParticipantFormItems(isUpdate, adno) {
    var txtADNO = document.getElementById('txtADNO');
    var txtClass = document.getElementById('txtClass');
    var txtGender = document.getElementById('txtGender');
    var selectedClass = txtClass ? parseInt(trim(txtClass.value), 10) : 0;
    var selectedGender = txtGender ? trim(txtGender.value).toUpperCase() : '';

    var limits = window.participantDistrictLimits || { max_single_items: 3, max_group_items: 2 };
    var maxSingle = parseInt(limits.max_single_items, 10) || 3;
    var maxGroup = parseInt(limits.max_group_items, 10) || 2;

    var mainCodes = [];
    var pinnanyCodes = [];
    var allEnteredCodes = {};

    // 1. Check main item codes
    for (var i = 1; i <= 10; i++) {
        var el = document.getElementById('txtItemCode_' + i);
        if (el && trim(el.value) !== '') {
            var code = trim(el.value);
            if (allEnteredCodes[code]) {
                showItemValidationError('Repeated item code: ' + code);
                if (el) el.focus();
                return false;
            }
            allEnteredCodes[code] = true;
            mainCodes.push(code);
        }
    }

    // 2. Check pinnany item codes
    for (var i = 1; i <= 5; i++) {
        var el = document.getElementById('txtPinnanyCode_' + i);
        if (el && trim(el.value) !== '') {
            var code = trim(el.value);
            if (allEnteredCodes[code]) {
                showItemValidationError('Repeated item code: ' + code);
                if (el) el.focus();
                return false;
            }
            allEnteredCodes[code] = true;
            pinnanyCodes.push(code);
        }
    }

    // 3. Validate main items
    var festSingles = {};
    var festGroups = {};

    for (var m = 0; m < mainCodes.length; m++) {
        var code = mainCodes[m];
        var item = (window.loadedItemsMap && window.loadedItemsMap[code]) || 
                   (Array.isArray(window.allFestivalItems) && window.allFestivalItems.find(function(it) {
                       return String(it.item_code).trim() === code || parseInt(it.item_code, 10) === parseInt(code, 10);
                   }));

        if (!item) {
            showItemValidationError('Invalid Item Code: ' + code + '.');
            return false;
        }

        // Validate Class
        var cStart = parseInt(item.class_start, 10);
        var cEnd = parseInt(item.class_end, 10);
        if (!isNaN(cStart) && !isNaN(cEnd) && selectedClass > 0) {
            if (selectedClass < cStart || selectedClass > cEnd) {
                showItemValidationError(code + ' is not valid for Class ' + selectedClass);
                return false;
            }
        }

        // Validate Gender
        var iGender = (item.gender || item.item_gender || 'C').toUpperCase();
        if (iGender !== 'C' && selectedGender !== 'C' && selectedGender !== '' && selectedGender !== iGender) {
            showItemValidationError(code + ' Wrong Gender');
            return false;
        }

        // Validate Festival single vs group item limits
        var festId = item.fest_id || 'default';
        var iType = (item.item_type || 'S').toUpperCase();

        if (iType === 'G') {
            festGroups[festId] = (festGroups[festId] || 0) + 1;
            if (festGroups[festId] > maxGroup) {
                showItemValidationError('Maximum number of group items exceeded. Maximum group items: ' + maxGroup + '. Maximum single items: ' + maxSingle + '.');
                return false;
            }
        } else {
            festSingles[festId] = (festSingles[festId] || 0) + 1;
            if (festSingles[festId] > maxSingle) {
                showItemValidationError('Maximum number of single items exceeded. Maximum single items: ' + maxSingle + '. Maximum group items: ' + maxGroup + '.');
                return false;
            }
        }
    }

    // 4. Validate Pinnany items
    for (var p = 0; p < pinnanyCodes.length; p++) {
        var pCode = pinnanyCodes[p];
        var pItem = (window.loadedItemsMap && window.loadedItemsMap[pCode]) || 
                    (Array.isArray(window.allFestivalItems) && window.allFestivalItems.find(function(it) {
                        return String(it.item_code).trim() === pCode || parseInt(it.item_code, 10) === parseInt(pCode, 10);
                    }));

        if (!pItem) {
            showItemValidationError('Invalid Item Code: ' + pCode + '.');
            return false;
        }

    }

    return true;
}

function validateParticipantAdmissionNumber(txtADNO, txtClass, hidHSSNo, hidVHSSNo) {
    if (!txtADNO || trim(txtADNO.value) == '') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Oops!', text: 'Please enter the admission number' });
        } else if (typeof swal !== 'undefined') {
            swal("Oops!", "Please enter the admission number", "error");
        } else {
            alert('Please enter the admission number');
        }
        if (txtADNO) txtADNO.focus();
        return false;
    }

    txtADNO.value = trim(txtADNO.value).toUpperCase();
    var admn_val = txtADNO.value;

    if (txtClass && parseInt(trim(txtClass.value), 10) > 10) {
        // Auto-clean any space, hyphen or symbol between prefix and number (e.g. "H 1234", "H-1234" -> "H1234")
        admn_val = admn_val.replace(/^([HV])[\s\-_]+(\d+)/i, '$1$2');
        txtADNO.value = admn_val;

        var prefix = admn_val.substring(0, 1);
        if (prefix !== 'H' && prefix !== 'V') {
            var hssErrorHtml = '<div style="text-align: left; font-size: 0.95rem; line-height: 1.6;">' +
                'For Higher Secondary, please enter prefix before Admission Number:<br><br>' +
                '• <b>HSS:</b> Add <b>H</b> before Admission Number (Example: <b>H1234</b>)<br>' +
                '• <b>Vocational:</b> Add <b>V</b> before Admission Number (Example: <b>V1234</b>)' +
                '</div>';
            var hssErrorText = 'For Higher Secondary, please enter prefix before Admission Number:\n' +
                '- HSS: Add H before Admission Number (Example: H1234)\n' +
                '- Vocational: Add V before Admission Number (Example: V1234)';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Admission Number',
                    html: hssErrorHtml
                });
            } else if (typeof swal !== 'undefined') {
                swal("Invalid Admission Number", hssErrorText, "error");
            } else {
                alert(hssErrorText);
            }
            txtADNO.focus();
            return false;
        }

        var numDigits = admn_val.substring(1);
        if (numDigits === '' || isNaN(numDigits) || !/^\d+$/.test(numDigits)) {
            var numHtml = '<div style="text-align: left; font-size: 0.95rem; line-height: 1.6;">' +
                'Please enter valid numeric digits after the prefix.<br><br>' +
                '• <b>HSS:</b> Example: <b>H1234</b><br>' +
                '• <b>Vocational:</b> Example: <b>V1234</b>' +
                '</div>';
            var numText = 'Please enter valid numeric digits after the prefix (Example: H1234 or V1234).';
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Invalid Admission Number', html: numHtml });
            } else if (typeof swal !== 'undefined') {
                swal("Invalid Admission Number", numText, "error");
            } else {
                alert(numText);
            }
            txtADNO.focus();
            return false;
        }
    } else if (txtClass && parseInt(trim(txtClass.value), 10) <= 10) {
        var cls = parseInt(trim(txtClass.value), 10);
        var firstChr = admn_val.substring(0, 1);
        if (firstChr === 'H' || firstChr === 'V') {
            var notHssMsg = 'Prefix \'' + firstChr + '\' is only for Higher Secondary (Class 11 & 12).<br>For Class ' + cls + ', please enter numeric admission number only.';
            var notHssText = 'Prefix \'' + firstChr + '\' is only for Higher Secondary (Class 11 & 12). For Class ' + cls + ', please enter numeric admission number only.';
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Invalid Admission Number', html: notHssMsg });
            } else if (typeof swal !== 'undefined') {
                swal("Invalid Admission Number", notHssText, "error");
            } else {
                alert(notHssText);
            }
            txtADNO.focus();
            return false;
        }
        if (isNaN(admn_val) || !/^\d+$/.test(admn_val)) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Oops!', text: 'Please enter a valid numeric admission number' });
            } else if (typeof swal !== 'undefined') {
                swal("Oops!", "Please enter a valid numeric admission number", "error");
            } else {
                alert('Please enter a valid numeric admission number');
            }
            txtADNO.focus();
            return false;
        }
    } else {
        if (isNaN(admn_val) || !/^\d+$/.test(admn_val)) {
            var defaultChr = admn_val.substring(0, 1);
            if (defaultChr === 'H' || defaultChr === 'V') {
                var dNum = admn_val.substring(1);
                if (dNum === '' || isNaN(dNum) || !/^\d+$/.test(dNum)) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: 'Invalid Admission Number', text: 'Please enter valid numeric digits following the prefix.' });
                    } else if (typeof swal !== 'undefined') {
                        swal("Invalid Admission Number", "Please enter valid numeric digits following the prefix.", "error");
                    } else {
                        alert('Please enter valid numeric digits following the prefix.');
                    }
                    txtADNO.focus();
                    return false;
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Oops!', text: 'Please enter a valid numeric admission number' });
                } else if (typeof swal !== 'undefined') {
                    swal("Oops!", "Please enter a valid numeric admission number", "error");
                } else {
                    alert('Please enter a valid numeric admission number');
                }
                txtADNO.focus();
                return false;
            }
        }
    }

    return true;
}

function participantPhotoIsPresent() {
    var hiddenField = document.getElementById('cropped_image_data');
    if (hiddenField && hiddenField.value && hiddenField.value.trim() !== '') {
        return true;
    }

    var userfile = document.getElementById('userfile');
    if (userfile && userfile.files && userfile.files.length > 0) {
        return true;
    }

    var currentPhoto = document.getElementById('currentPhoto');
    if (currentPhoto && currentPhoto.style.display !== 'none') {
        var src = currentPhoto.getAttribute('src');
        if (src && src.trim() !== '') {
            return true;
        }
    }

    return false;
}

function clearParticipantPhotoHighlight() {
    var preview = document.getElementById('photoPreview');
    if (preview) {
        preview.classList.remove('photo-required-error');
    }
}

function validateParticipantPhoto() {
    if (participantPhotoIsPresent()) {
        clearParticipantPhotoHighlight();
        return true;
    }

    var preview = document.getElementById('photoPreview');
    if (preview) {
        preview.classList.add('photo-required-error');
        if (typeof preview.scrollIntoView === 'function') {
            preview.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    var message = 'Participant photo is required. Please upload a photo using "Take Photo" or "Choose from Gallery" and crop it to the 3:4 ratio.';

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'warning',
            title: 'Photo Required',
            html: '<div style="text-align:left;font-size:0.95rem;line-height:1.6;">'
                + '<p style="margin:0 0 0.6rem;">A participant photo is <b>mandatory</b>. The record cannot be saved without it.</p>'
                + '<p style="margin:0;color:#64748b;font-size:0.88rem;">'
                + '<i class="bi bi-camera-fill me-1"></i>Use <b>Take Photo</b> or <b>Choose from Gallery</b>, then crop to the fixed <b>3:4</b> ratio.'
                + '</p></div>',
            confirmButtonText: '<i class="bi bi-check2-circle me-1"></i> OK, I\'ll Upload',
            confirmButtonColor: '#4338ca',
            allowOutsideClick: false
        }).then(function () {
            var takePhotoBtn = document.querySelector('button[onclick="triggerCameraCapture()"]');
            if (takePhotoBtn) {
                takePhotoBtn.focus();
            }
        });
    } else {
        alert(message);
    }

    return false;
}

function fncSaveParticipant() {
    var txtADNO = document.getElementById('txtADNO');
    var txtClass = document.getElementById('txtClass');
    var txtParticipantName = document.getElementById('txtParticipantName');
    var hidVHSSNo = document.getElementById('hidVHSSNo');
    var hidHSSNo = document.getElementById('hidHSSNo');

    if (!validateParticipantAdmissionNumber(txtADNO, txtClass, hidHSSNo, hidVHSSNo)) {
        return false;
    }

    if (!txtParticipantName || trim(txtParticipantName.value) == '') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Oops!', text: 'Please enter the participant name' });
        } else if (typeof swal !== 'undefined') {
            swal("Oops!", "Please enter the participant name", "error");
        } else {
            alert('Please enter the participant name');
        }
        if (txtParticipantName) txtParticipantName.focus();
        return false;
    } else {
        var alpha = /^[a-zA-Z\s.]+$/;
        if (!txtParticipantName.value.match(alpha)) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Oops!', text: 'Please enter a valid name' });
            } else if (typeof swal !== 'undefined') {
                swal("Oops!", "Please enter a valid name", "error");
            } else {
                alert('Please enter a valid name.');
            }
            txtParticipantName.focus();
            return false;
        }
    }

    if (!validateParticipantPhoto()) {
        return false;
    }

    var items = 0;
    for (var i = 1; i <= 10; i++) {
        var fieldElement = document.getElementById('txtItemCode_' + i);
        if (fieldElement && trim(fieldElement.value) != '') {
            items++;
        }
    }
    var pinnani = 0;
    for (var i = 1; i <= 5; i++) {
        var pinnanyElement = document.getElementById('txtPinnanyCode_' + i);
        if (pinnanyElement && trim(pinnanyElement.value) != '') {
            pinnani++;
        }
    }

    if ((items == 0) && (pinnani == 0)) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Oops!', text: 'Please enter the item code' });
        } else if (typeof swal !== 'undefined') {
            swal("Oops!", "Please enter the item code", "error");
        } else {
            alert('Please enter the item code');
        }
        var firstItem = document.getElementById('txtItemCode_1');
        if (firstItem) firstItem.focus();
        return false;
    }

    // Validate item limits and compatibility before saving
    if (typeof validateParticipantFormItems === 'function') {
        if (!validateParticipantFormItems(false)) {
            return false;
        }
    }
    
    // Show spinner on button
    var saveBtn = document.getElementById('save_participant_btn');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
        saveBtn.setAttribute('data-original-text', 'Save Participant');
    }

    var formParticipant = document.getElementById('formParticipant');
    if (formParticipant) {
        if (typeof getCsrfToken === 'function') {
            var tok = getCsrfToken();
            if (tok) {
                var cInp = formParticipant.querySelector('input[name="csrf_test_name"]');
                if (!cInp) {
                    cInp = document.createElement('input');
                    cInp.type = 'hidden';
                    cInp.name = 'csrf_test_name';
                    formParticipant.appendChild(cInp);
                }
                cInp.value = tok;
            }
        }
        var participantRoute = (typeof PARTICIPANTS_REGISTRATION_ROUTE !== 'undefined') ? PARTICIPANTS_REGISTRATION_ROUTE : 'schools/registration';
        formParticipant.action = (typeof path !== 'undefined' ? path : '') + participantRoute + '/save_participant';
        formParticipant.submit();
    }
}

function editParticipant(adno, btn, schoolCode) {
    var hidADNO = document.getElementById('hidADNO');
    if (hidADNO) hidADNO.value = adno;

    var schCode = schoolCode ||
                  (document.getElementById('hidSchoolId') && document.getElementById('hidSchoolId').value ? document.getElementById('hidSchoolId').value : '') ||
                  (document.getElementById('formSchoolHidSchoolId') && document.getElementById('formSchoolHidSchoolId').value ? document.getElementById('formSchoolHidSchoolId').value : '') ||
                  (document.querySelector('input[name="hidSchoolId"]') ? document.querySelector('input[name="hidSchoolId"]').value : '');

    var hidSchoolIdEl = document.getElementById('hidSchoolId');
    if (hidSchoolIdEl && schCode) hidSchoolIdEl.value = schCode;

    // Immediately prepare modal title, button and action for edit
    var modalLabel = document.getElementById('participantModalLabel');
    if (modalLabel) modalLabel.innerText = 'Edit Participant';

    var saveBtn = document.getElementById('save_participant_btn');
    if (saveBtn) {
        saveBtn.innerText = 'Update Participant';
        saveBtn.disabled = false;
        saveBtn.setAttribute('onclick', 'fncUpdateParticipant(\'' + adno + '\')');
    }

    var formParticipant = document.getElementById('formParticipant');
    if (formParticipant) {
        var participantRoute = (typeof PARTICIPANTS_REGISTRATION_ROUTE !== 'undefined') ? PARTICIPANTS_REGISTRATION_ROUTE : 'schools/registration';
        formParticipant.action = (typeof path !== 'undefined' ? path : '') + participantRoute + '/update_participant_details';
    }

    var txtADNO = document.getElementById('txtADNO');
    var txtParticipantName = document.getElementById('txtParticipantName');
    var txtClass = document.getElementById('txtClass');
    var txtGender = document.getElementById('txtGender');
    var userfile = document.getElementById('userfile');

    if (txtADNO) txtADNO.value = adno;
    if (txtParticipantName) txtParticipantName.value = '';
    if (txtClass) txtClass.value = '';
    if (txtGender) txtGender.value = '';
    if (userfile) userfile.value = '';

    // Clear item inputs first
    for (var c = 1; c <= 5; c++) {
        var inpC = document.getElementById('txtItemCode_' + c);
        if (inpC) inpC.value = '';
        var dC = document.getElementById('itemDetails_' + c);
        if (dC) dC.style.display = 'none';
        var iC = document.getElementById('itemTypeIcon_' + c);
        if (iC) { iC.style.display = 'none'; iC.innerHTML = ''; }
        var nC = document.getElementById('itemName_' + c);
        if (nC) nC.innerText = '';
    }
    for (var p = 1; p <= 3; p++) {
        var inP = document.getElementById('txtPinnanyCode_' + p);
        if (inP) inP.value = '';
        var dP = document.getElementById('pinnanyDetails_' + p);
        if (dP) dP.style.display = 'none';
        var iP = document.getElementById('pinnanyTypeIcon_' + p);
        if (iP) { iP.style.display = 'none'; iP.innerHTML = ''; }
        var nP = document.getElementById('pinnanyName_' + p);
        if (nP) nP.innerText = '';
    }

    var requestUrl = (typeof path !== 'undefined' ? path : '') + 'schools/registration/get_participant_data';
    var formData = new FormData();
    formData.append('hidSchoolId', schCode);
    formData.append('school_code', schCode);
    formData.append('hidADNO', adno);
    formData.append('admn_no', adno);
    if (typeof getCsrfToken === 'function') {
        var csrfToken = getCsrfToken();
        if (csrfToken) formData.append('csrf_test_name', csrfToken);
    }

    fetch(requestUrl, {
        method: 'POST',
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.csrf_hash) {
            if (typeof updateCsrfToken === 'function') {
                updateCsrfToken(data.csrf_hash);
            }
            var csrfInputs = document.querySelectorAll('input[name="csrf_test_name"]');
            for (var i = 0; i < csrfInputs.length; i++) {
                csrfInputs[i].value = data.csrf_hash;
            }
        }

        if (data.error) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error', text: data.error });
            } else if (typeof swal !== 'undefined') {
                swal("Oops!", data.error, "error");
            } else {
                alert(data.error);
            }
            return;
        }

        if (txtADNO) txtADNO.value = data.admn_no || '';
        if (txtParticipantName) txtParticipantName.value = data.participant_name || '';
        if (txtClass) txtClass.value = data.class || '';
        if (txtGender) txtGender.value = data.gender || '';

        // Populate item codes from response
        if (data.items && Array.isArray(data.items)) {
            var regularIdx = 1;
            var pinnanyIdx = 1;
            for (var i = 0; i < data.items.length; i++) {
                var itm = data.items[i];
                if (itm.item_type === 'P' || itm.is_pinnany === 'Y') {
                    if (pinnanyIdx <= 3) {
                        var pInput = document.getElementById('txtPinnanyCode_' + pinnanyIdx);
                        if (pInput && itm.item_code) {
                            pInput.value = itm.item_code;
                            if (typeof window.updatePinnanyCodeIcon === 'function') {
                                window.updatePinnanyCodeIcon(pinnanyIdx, itm.item_type || 'P', itm.item_name || '');
                            }
                        }
                        pinnanyIdx++;
                    }
                } else {
                    if (regularIdx <= 5) {
                        var regInput = document.getElementById('txtItemCode_' + regularIdx);
                        if (regInput && itm.item_code) {
                            regInput.value = itm.item_code;
                            if (typeof window.updateItemCodeIcon === 'function') {
                                window.updateItemCodeIcon(regularIdx, itm.item_type || 'S', itm.item_name || '');
                            }
                        }
                        regularIdx++;
                    }
                }
            }
        }

        // Re-render available items so current assigned items are excluded
        if (typeof window.loadItemsByClass === 'function') {
            window.loadItemsByClass();
        }

        var modalLabel = document.getElementById('participantModalLabel');
        if (modalLabel) modalLabel.innerText = 'Edit Participant';

        var saveBtn = document.getElementById('save_participant_btn');
        if (saveBtn) {
            saveBtn.innerText = 'Update Participant';
            saveBtn.disabled = false;
            saveBtn.setAttribute('onclick', 'fncUpdateParticipant(\'' + adno + '\')');
        }

        // Handle photo display in edit modal
        if (data.photo_url && data.photo_url !== 'false' && data.photo_url !== 'null' && data.photo_url.trim() !== '') {
            if (typeof loadModalPhoto === 'function') {
                loadModalPhoto(data.photo_url);
            }
        } else {
            if (typeof removeCurrentPhoto === 'function') {
                removeCurrentPhoto();
            }
        }

        var modalEl = document.getElementById('participantModal');
        if (modalEl) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var myModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                myModal.show();
            } else if (typeof window.jQuery !== 'undefined' && typeof window.jQuery(modalEl).modal === 'function') {
                window.jQuery(modalEl).modal('show');
            } else {
                modalEl.style.display = 'block';
                modalEl.classList.add('show');
                document.body.classList.add('modal-open');
            }
        }
    })
    .catch(function(err) {
        console.error('Error fetching participant data:', err);
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error loading participant data' });
        } else {
            alert('Error loading participant data');
        }
    });
}

function deleteParticipant(adno) {
    var executeDelete = function () {
        try {
            var hidADNO = document.getElementById('hidADNO') || document.querySelector('input[name="hidADNO"]');
            var formParticipant = document.getElementById('formParticipant') || document.querySelector('form[action*="delete_participant_details"]') || document.querySelector('form[id="formParticipant"]');

            if (hidADNO) {
                hidADNO.value = adno;
            } else {
                console.error('CRITICAL: hidADNO element not found!');
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not find form field. Please refresh the page and try again.' });
                } else {
                    alert('Error: Could not find form field. Please refresh the page and try again.');
                }
                return;
            }

            if (formParticipant) {
                var participantRoute = (typeof PARTICIPANTS_REGISTRATION_ROUTE !== 'undefined') ? PARTICIPANTS_REGISTRATION_ROUTE : 'schools/registration';
                formParticipant.action = (typeof path !== 'undefined' ? path : '') + participantRoute + '/delete_participant_details';
                formParticipant.submit();
            } else {
                console.error('CRITICAL: formParticipant element not found!');
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not find form. Please refresh the page and try again.' });
                } else {
                    alert('Error: Could not find form. Please refresh the page and try again.');
                }
                return;
            }
        } catch (error) {
            console.error('Error during delete operation:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Error during delete operation: ' + error.message });
            } else {
                alert('Error during delete operation: ' + error.message);
            }
        }
    };

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Delete Participant?',
            text: 'Are you sure you want to delete this participant (AD No: ' + adno + ')?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Deleting participant. Please wait...',
                    allowOutsideClick: false,
                    didOpen: function () { Swal.showLoading(); }
                });
                executeDelete();
            }
        });
    } else {
        if (confirm('Are you sure you want to delete participant ' + adno + '?')) {
            executeDelete();
        }
    }
}



function fncCancelParticipant() {
    $('formParticipant').action = path + 'schools/registration';
    $('formParticipant').submit();
}function fncUpdateParticipant(adno) {
    var txtADNO = document.getElementById('txtADNO');
    var txtClass = document.getElementById('txtClass');
    var txtParticipantName = document.getElementById('txtParticipantName');
    var hidVHSSNo = document.getElementById('hidVHSSNo');
    var hidHSSNo = document.getElementById('hidHSSNo');

    if (!validateParticipantAdmissionNumber(txtADNO, txtClass, hidHSSNo, hidVHSSNo)) {
        return false;
    }

    if (!txtParticipantName || trim(txtParticipantName.value) == '') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Oops!', text: 'Please enter the participant name' });
        } else if (typeof swal !== 'undefined') {
            swal("Oops!", "Please enter the participant name", "error");
        } else {
            alert('Please enter the participant name');
        }
        if (txtParticipantName) txtParticipantName.focus();
        return false;
    } else {
        var alpha = /^[a-zA-Z\s.]+$/;
        if (!txtParticipantName.value.match(alpha)) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Oops!', text: 'Please enter a valid name' });
            } else if (typeof swal !== 'undefined') {
                swal("Oops!", "Please enter a valid name", "error");
            } else {
                alert('Please enter a valid name.');
            }
            txtParticipantName.focus();
            return false;
        }
    }

    if (!validateParticipantPhoto()) {
        return false;
    }

    var items = 0;
    for (var i = 1; i <= 10; i++) {
        var fieldElement = document.getElementById('txtItemCode_' + i);
        if (fieldElement && trim(fieldElement.value) != '') {
            items++;
        }
    }
    var pinnani = 0;
    for (var i = 1; i <= 5; i++) {
        var pinnanyElement = document.getElementById('txtPinnanyCode_' + i);
        if (pinnanyElement && trim(pinnanyElement.value) != '') {
            pinnani++;
        }
    }

    if ((items == 0) && (pinnani == 0)) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Oops!', text: 'Please enter the item code' });
        } else if (typeof swal !== 'undefined') {
            swal("Oops!", "Please enter the item code", "error");
        } else {
            alert('Please enter the item code');
        }
        var firstItem = document.getElementById('txtItemCode_1');
        if (firstItem) firstItem.focus();
        return false;
    }

    // Validate item limits and compatibility before updating
    if (typeof validateParticipantFormItems === 'function') {
        if (!validateParticipantFormItems(true, adno)) {
            return false;
        }
    }

    var hidAdnoEl = document.getElementById('hidADNO');
    if (hidAdnoEl) hidAdnoEl.value = adno;
    
    // Show spinner on button
    var saveBtn = document.getElementById('save_participant_btn');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';
        saveBtn.setAttribute('data-original-text', 'Update Participant');
    }

    var formParticipant = document.getElementById('formParticipant');
    if (formParticipant) {
        if (typeof getCsrfToken === 'function') {
            var tok = getCsrfToken();
            if (tok) {
                var cInp = formParticipant.querySelector('input[name="csrf_test_name"]');
                if (!cInp) {
                    cInp = document.createElement('input');
                    cInp.type = 'hidden';
                    cInp.name = 'csrf_test_name';
                    formParticipant.appendChild(cInp);
                }
                cInp.value = tok;
            }
        }
        var participantRoute = (typeof PARTICIPANTS_REGISTRATION_ROUTE !== 'undefined') ? PARTICIPANTS_REGISTRATION_ROUTE : 'schools/registration';
        formParticipant.action = (typeof path !== 'undefined' ? path : '') + participantRoute + '/update_participant_details';
        formParticipant.submit();
    }
}

function fnschgpwdAdd() {
    var oldPwd = $('txtOLDPassword') ? $('txtOLDPassword').value.trim() : '';
    var newPwd = $('txtNewPassword') ? $('txtNewPassword').value.trim() : '';
    var cfmPwd = $('txtCFMPassword') ? $('txtCFMPassword').value.trim() : '';

    var isChangingPwd = (oldPwd !== '' || newPwd !== '' || cfmPwd !== '');

    if (isChangingPwd) {
        if (oldPwd == "") {
            swal ( "Oops!" ,  "please enter current password" ,  "error" );
            $('txtOLDPassword').focus();
            return false;
        }
        if (newPwd == "") {
            swal ( "Oops!" ,  "please enter password" ,  "error" );
            $('txtNewPassword').focus();
            return false;
        }
        if (newPwd.length < 2 || newPwd.length > 16) {
            swal ( "Oops!" ,  "New password must be between 2 and 16 characters" ,  "error" );
            $('txtNewPassword').focus();
            return false;
        }
        if (cfmPwd == "") {
            swal ( "Oops!" ,  "please Re-Type your password" ,  "error" );
            $('txtCFMPassword').focus();
            return false;
        }
        if (newPwd != cfmPwd) {
            swal ( "Oops!" ,  "Your reentered password is not correct" ,  "error" );
            $('txtCFMPassword').focus();
            return false;
        }
        if (oldPwd == newPwd) {
            swal ( "Oops!" ,  "Old Password and New Password are Same!!! Please enter a new one" ,  "error" );
            $('txtNewPassword').focus();
            return false;
        }
    }

    if ($('Name').value == "") {
        swal ( "Oops!" ,  "please enter Your Name" ,  "error" )
        //alert("please enter Your Name");
        $('Name').focus();
        return false;
    }
    if ($('Mobile_Number').value == "") {
        swal ( "Oops!" ,  "please enter Your Mobile Number or Contact Number" ,  "error" )
        //alert("please enter Your Mobile Number or Contact Number");
        $('Mobile_Number').focus();
        return false;
    }
    if ($('Email_id').value == "") {
        swal ( "Oops!" ,  "please enter your email  id" ,  "error" )
        //alert("please enter your email  id ");
        $('Email_id').focus();
        return false;
    }
    else {
        var email = ($('Email_id').value);
        var rt = isValidEmail(email);
        if (rt == false) {
            swal ( "Oops!" ,  "please enter a valid  email id" ,  "error" )
        //alert("please enter a valid  email id ");
            return false;
        }
        else {
            return true;
        }

    }

}

function gotohome()
{
    $('formPWD').action = path;
    $('formPWD').submit();
}

function confirm_school_data() {
    swal({
        title: "Confirm School Entry",
        text: "Do you want to confirm school entry details?\nOnce confirmed, the entry details cannot be modified!",
        icon: "info",
        showCancelButton: true,
        confirmButtonText: "Yes, Confirm",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#28a745",
        reverseButtons: true
    }).then(function(willConfirm) {
        if (willConfirm) {
            // User clicked OK, proceed with confirmation
            swal({
                title: "Confirm School Entry",
                text: "Confirmation Success",
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            });
            $('formFinalize').action = path + 'schools/registration/finalize_data';
            $('formFinalize').submit();
        } else {
            // User clicked Cancel, do nothing
            swal({
                title: "Confirm School Entry",
                text: "Cancel Confirmation",
                icon: "error",
                timer: 2000,
                showConfirmButton: false
            });
            return false;
        }
    });
}

function create_csv_school_data()
{
    $('formFinalize').action = path + 'schools/registration/create_csv_generation';
    $('formFinalize').submit();
    $('formFinalize').action = path + 'schools/registration/finalize_data';
}
function create_school_report()
{
    $('formFinalize').action = path + 'schools/registration_report/';
    $('formFinalize').target = '_blank';
    $('formFinalize').submit();
    $('formFinalize').target = '_self';
}

function add_special_order_participant()
{
    if ($('chkAddParticipant').checked == true) {
        $('newEntry').style.display = 'block';
        $('cmbParticipant').value = '0';
        $('cmbParticipant').disabled = true;
        //$('cmbParticipant').style.display	=	'none';
    } else {
        $('newEntry').style.display = 'none';
        $('cmbParticipant').disabled = false;
        //$('cmbParticipant').style.display	=	'block';
    }
}

function fncSaveSpecialOrderParticipant()
{
    if (trim($('cmbOrder').value) == '0') {
        swal ( "Oops!" ,  "Please select the order type" ,  "error" )
        //alert('Please select the order type');
        $('cmbOrder').focus();
        return false;
    }
    if ($('chkAddParticipant').checked == true) {
        if (trim($('txtADNO').value) == '') {
            swal ( "Oops!" ,  "Please enter the admission number" ,  "error" )
        //alert('Please enter the admission number');
            $('txtADNO').focus();
            return false;
        }
        if (trim($('txtParticipantName').value) == '') {
            swal ( "Oops!" ,  "Please enter the participant name" ,  "error" )
        //alert('Please enter the participant name');
            $('txtParticipantName').focus();
            return false;
        }
    } else {
        if (trim($('cmbParticipant').value) == '0') {
            swal ( "Oops!" ,  "Please select the participant" ,  "error" )
        //alert('Please select the participant');
            $('cmbParticipant').focus();
            return false;
        }
    }
    if (trim($('txtItemCode_1').value) == '') {
        swal ( "Oops!" ,  "Please enter the item code" ,  "error" )
        //alert('Please enter the item code');
        $('txtItemCode_1').focus();
        return false;
    }

    if ($('chkAddParticipant').checked == true) {
        if (trim($('txtADNO').value) == '') {
            swal ( "Oops!" ,  "Please enter the admission number" ,  "error" )
        //alert('Please enter the admission number');
            $('txtADNO').focus();
            return false;
        }
    }

    

    $('formParticipant').submit();
}

function editSpecialOrderParticipant(adno) {
    $('hidPiId').value = adno;
    $('formParticipant').action = path + 'schools/special_order_entry/edit_participant_details';
    $('formParticipant').submit();
}

function deleteSpecialOrderParticipant(adno, itemcode) {
    swal({
        title: "Are you sure?",
        text: "Do you really want to delete the participant?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#d33",
        reverseButtons: true
    }).then(function(willDelete) {
        if (willDelete) {
            // User clicked OK, proceed with deletion
            $('hidADNO').value = adno;
            $('hidItemId').value = itemcode;
            $('formParticipant').action = path + 'schools/special_order_entry/delete_participant_details';
            $('formParticipant').submit();
        } else {
            // User clicked Cancel, do nothing
        }
    }).catch(function(error) {
        console.error('Error in special order delete confirmation:', error);
    });
}

function fncCancelSpecialOrderParticipant() {
    $('formParticipant').action = path + 'schools/special_order_entry/';
    $('formParticipant').submit();
}

function fncShowHideStd() {
    if (check_div('LP')) {
        $('divLP').style.display = 'block';
    } else {
        $('divLP').style.display = 'none';
        $('txtTotalLP').value = 0;
    }
    if (check_div('UP')) {
        $('divUP').style.display = 'block';
    } else {
        $('divUP').style.display = 'none';
        $('txtTotalUP').value = 0;
    }
    if (check_div('HS')) {
        $('divHS').style.display = 'block';
    } else {
        $('divHS').style.display = 'none';
        $('txtTotalHS').value = 0;
    }
    if (check_div('HSS')) {
        $('divHSS').style.display = 'block';
        $('divVHSC').style.display = 'block';
    } else {
        $('divHSS').style.display = 'none';
        $('divVHSC').style.display = 'none';
        $('txtTotalHSS').value = 0;
        $('txtTotalVHSS').value = 0;
    }
    addTotal();
}

function goschooldet(shid) {
    $('hidSchoolId').value = shid;
    $('clustschool').action = path + 'schools/registration/';
    $('clustschool').submit();
}

function fncShowClusterSchools(shid) {
    var hidClusterId = document.getElementById('hidClusterId');
    var clustschool = document.getElementById('clustschool');
    if (hidClusterId) hidClusterId.value = shid;
    if (clustschool) {
        clustschool.action = path + 'welcome/cluster_schools';
        clustschool.submit();
    }
}

function fncConfirmSubDistAdmin() {
    swal({
        title: "Confirmation",
        text: "Do you want to confirm? \nOnce confirmed, the entry details cannot be modified or add any school!!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        confirmButtonColor: "#28a745",
        reverseButtons: true
    }).then(function(confirmed) {
        if (confirmed) {
            // User clicked "Yes," proceed with confirmation
            $('confirm_sub_dist').action = path + 'welcome/confirm_sub_dist_schools';
            $('confirm_sub_dist').submit();
        } else {
            // User clicked "No" or closed the dialog, show an error alert
            swal("Cancelled", "Entry details remain unchanged.", "error");
        }
    }).catch(function(error) {
        console.error('Error in confirmation dialog:', error);
    });
}

function printContent(element_id)
{
    var DocumentContainer = document.getElementById(element_id);
    var WindowObject = window.open('', "TrackHistoryData",
            "width='100%',height='100%',top=10,left=10,toolbars=no,scrollbars=yes,status=no,resizable=no");
    WindowObject.document.writeln(DocumentContainer.innerHTML);
    WindowObject.document.close();
    WindowObject.focus();
    WindowObject.print();
    WindowObject.close();
}

function fnc_change_confirmation_status(message, school_code, element_id)
{
    // Validate parameters
    if (typeof element_id === 'undefined' || element_id === null || element_id === '') {
        console.error('CRITICAL: element_id is undefined/null/empty:', element_id);
        alert('Error: Invalid element ID');
        return;
    }

    if (typeof school_code === 'undefined' || school_code === null || school_code === '') {
        console.error('CRITICAL: school_code is undefined/null/empty:', school_code);
        alert('Error: Invalid school code');
        return;
    }

    var testElement = document.getElementById(element_id);
    if (!testElement && typeof $ === 'function') {
        testElement = $(element_id);
    }
    if (!testElement) {
        console.error('CRITICAL ERROR: Element not found:', element_id);
        return;
    }

    Swal.fire({
        title: 'Reset Confirmation Status?',
        text: "Do you really want to reset the confirmation status of " + message + "?",
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

            ajax_loder2(element_id);
            var oOptions = {
                method: "post",
                parameters: {'school_code': school_code},
                onFailure: function (oXHR, oJson) {
                    console.error('AJAX failure in common.js:', oXHR, oJson);
                    Swal.fire({
                        title: 'Error!',
                        text: "An error occurred: " + oXHR.status,
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                },
                onSuccess: function (transport) {
                    var response = transport.responseText.trim();
                    var element = document.getElementById(element_id);
                    var disElement = document.getElementById(element_id + '_dis');
                    var resetBtn = document.getElementById('reset_' + school_code) || document.getElementById('reset_nonschool_' + school_code);

                    if (!element) {
                        console.error('Element not found after AJAX:', element_id);
                        return;
                    }

                    if (response == 'Yes' || response == 'No') {
                        element.innerHTML = response;
                        if (disElement) disElement.innerHTML = '';
                        if (resetBtn) resetBtn.style.display = 'none';
                        
                        Swal.fire({
                            title: 'Success!',
                            text: 'Confirmation status has been reset successfully.',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        });
                    } else {
                        console.error('Unexpected response:', response);
                        Swal.fire({
                            title: 'Failed!',
                            text: 'Failed to reset: ' + response,
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                }
            };

            if (typeof Ajax !== 'undefined' && Ajax.Request) {
                var oRequest = new Ajax.Request(path + "ajax/change_confirmation_status", oOptions);
            } else {
                fetch(path + "ajax/change_confirmation_status", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(oOptions.parameters)
                })
                .then(function(response) {
                    return response.text();
                })
                .then(function(data) {
                    oOptions.onSuccess({responseText: data});
                })
                .catch(function(error) {
                    console.error('Fetch error:', error);
                    oOptions.onFailure({status: error.message});
                });
            }
        }
    });
}





function fnc_export_photo()
{
    $('export_dist_data').action = path + 'welcome/export_photo/';
    $('export_dist_data').submit();
}

function clearForm() {
    var el;
    el = document.getElementById('txtADNO');
    if (el) el.value = '';
    el = document.getElementById('txtParticipantName');
    if (el) el.value = '';
    el = document.getElementById('txtClass');
    if (el) el.value = '';
    el = document.getElementById('txtGender');
    if (el) el.value = '';
    el = document.getElementById('userfile');
    if (el) el.value = '';

    for (let i = 1; i <= 10; i++) {
        el = document.getElementById('txtItemCode_' + i);
        if (el) el.value = '';
        var d = document.getElementById('itemDetails_' + i);
        if (d) d.style.display = 'none';
        var ic = document.getElementById('itemTypeIcon_' + i);
        if (ic) { ic.style.display = 'none'; ic.innerHTML = ''; }
    }
    for (let i = 1; i <= 5; i++) {
        el = document.getElementById('txtPinnanyCode_' + i);
        if (el) el.value = '';
        var pd = document.getElementById('pinnanyDetails_' + i);
        if (pd) pd.style.display = 'none';
        var pic = document.getElementById('pinnanyTypeIcon_' + i);
        if (pic) { pic.style.display = 'none'; pic.innerHTML = ''; }
    }

    el = document.getElementById('hidADNO');
    if (el) el.value = '';
    el = document.getElementById('participantModalLabel');
    if (el) el.innerText = 'Add Participant';
    el = document.getElementById('save_participant_btn');
    if (el) {
        el.innerText = 'Save Participant';
        el.setAttribute('onclick', 'fncSaveParticipant()');
    }

    // Clear photo preview and reset photo display
    var photoPreviewContainer = document.getElementById('photoPreviewContainer');
    var noPhotoMessage = document.getElementById('noPhotoMessage');
    var currentPhoto = document.getElementById('currentPhoto');

    if (photoPreviewContainer) photoPreviewContainer.style.display = 'none';
    if (noPhotoMessage) noPhotoMessage.style.display = 'block';
    if (currentPhoto) {
        currentPhoto.src = '';
        currentPhoto.style.display = 'none';
    }

    // Refresh participant list photos after clearing form
    setTimeout(function() {
        refreshParticipantPhotos();
    }, 500);
}

function closeSchoolModal() {
    var schoolModal = bootstrap.Modal.getInstance(document.getElementById('schoolDetailsModal'));
    if (schoolModal) {
        schoolModal.hide();
    }
}

// Function to preview selected photo before upload
function previewPhoto(input) {
    var photoPreviewContainer = document.getElementById('photoPreviewContainer');
    var noPhotoMessage = document.getElementById('noPhotoMessage');
    var currentPhoto = document.getElementById('currentPhoto');

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            // Show photo preview with selected file
            if (photoPreviewContainer) photoPreviewContainer.style.display = 'block';
            if (noPhotoMessage) noPhotoMessage.style.display = 'none';
            if (currentPhoto) {
                currentPhoto.src = e.target.result;
                currentPhoto.style.display = 'block';
            }
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        // No file selected - show no photo message
        if (photoPreviewContainer) photoPreviewContainer.style.display = 'none';
        if (noPhotoMessage) noPhotoMessage.style.display = 'block';
        if (currentPhoto) currentPhoto.style.display = 'none';
    }
}

// Function to remove current photo
function removeCurrentPhoto() {
    var photoPreviewContainer = document.getElementById('photoPreviewContainer');
    var noPhotoMessage = document.getElementById('noPhotoMessage');
    var currentPhoto = document.getElementById('currentPhoto');
    var fileInput = document.getElementById('userfile');

    // Hide photo preview
    if (photoPreviewContainer) photoPreviewContainer.style.display = 'none';
    if (noPhotoMessage) noPhotoMessage.style.display = 'block';
    if (currentPhoto) {
        currentPhoto.src = '';
        currentPhoto.style.display = 'none';
    }

    // Clear file input
    if (fileInput) fileInput.value = '';
}

// Function to handle post-submission photo refresh and modal management
function handlePostSubmission() {
    

    // Refresh participant photos after successful submission
    setTimeout(function() {
        refreshParticipantPhotos();

        // Reset modal state
        var saveBtn = document.getElementById('save_participant_btn');
        if (saveBtn) {
            saveBtn.disabled = false;
            var originalText = saveBtn.getAttribute('data-original-text') || 'Save Participant';
            saveBtn.innerHTML = originalText;
        }

        // Close modal after successful submission
        var participantModal = bootstrap.Modal.getInstance(document.getElementById('participantModal'));
        if (participantModal) {
            setTimeout(function() {
                participantModal.hide();
                // Force cleanup of modal backdrop
                forceRemoveModalBackdrop();
            }, 1000);
        }

        
    }, 1000);
}

// Function to refresh photo when modal is shown
function refreshModalPhoto() {
    
    var currentPhoto = document.getElementById('currentPhoto');
    if (currentPhoto && currentPhoto.src && currentPhoto.src.trim() !== '') {
        var currentSrc = currentPhoto.src;
        var timestamp = new Date().getTime();
        var cacheBuster = Math.random().toString(36).substring(7);

        if (currentSrc.indexOf('?') > -1) {
            currentPhoto.src = currentSrc.split('?')[0] + '?modal=' + timestamp + '&refresh=' + cacheBuster;
        } else {
            currentPhoto.src = currentSrc + '?modal=' + timestamp + '&refresh=' + cacheBuster;
        }

        
    }
}

// Function to trigger photo refresh from server response
function triggerPhotoRefresh() {
    
    handlePostSubmission();
}

// Global function that can be called after successful AJAX operations
window.refreshPhotosAfterUpdate = function() {
    
    refreshParticipantPhotos();
};

// Global function to force refresh all photos (can be called from browser console for debugging)
window.forceRefreshPhotos = function() {
    
    forceRefreshAllPhotos();
};

// Enhanced function to handle photo refresh after form submission
function handleFormSubmission() {
    

    // Force refresh all photos after a delay
    setTimeout(function() {
        forceRefreshAllPhotos();
        
    }, 1500);
}

// Function to refresh participant list photos after save/update
function refreshParticipantPhotos() {
    

    // Force refresh of images by updating their src attributes with new timestamps
    var participantImages = document.querySelectorAll('img[src*="photos"]');
    

    participantImages.forEach(function(img, index) {
        var currentSrc = img.src;
        var timestamp = new Date().getTime();
        var cacheBuster = Math.random().toString(36).substring(7);
        var baseSrc = '';

        // Remove existing cache parameters to get base URL
        if (currentSrc.indexOf('?') > -1) {
            baseSrc = currentSrc.split('?')[0];
        } else {
            baseSrc = currentSrc;
        }

        // Add fresh cache busting parameters - focus only on photo loading
        var newSrc = baseSrc + '?t=' + timestamp + '&refresh=' + cacheBuster + '&v=' + cacheBuster + '&ts=' + timestamp;

        img.src = newSrc;
        

        // Add error handling for each image - only photo fallback logic
        img.onerror = function() {
            this.onerror = null;
            var fallbackTimestamp = new Date().getTime();
            var fallbackCacheBuster = Math.random().toString(36).substring(7);
            this.src = path + 'photos/img_user_nophoto.png?t=' + fallbackTimestamp + '&cache=' + fallbackCacheBuster;
            this.alt = 'Photo not available';
        };

        // Add load success logging
        img.onload = function() {
        };
    });

    // Also refresh any "no photo" placeholders - only if they exist
    var noPhotoImages = document.querySelectorAll('img[src*="img_user_nophoto.png"]');
    if (noPhotoImages.length > 0) {
        noPhotoImages.forEach(function(img, index) {
            var timestamp = new Date().getTime();
            var cacheBuster = Math.random().toString(36).substring(7);
            if (img.src.indexOf('?') > -1) {
                img.src = img.src.split('?')[0] + '?t=' + timestamp + '&refresh=' + cacheBuster;
            } else {
                img.src = img.src + '?t=' + timestamp + '&refresh=' + cacheBuster;
            }
        });
    }
}

// Enhanced function to ensure photos load properly on page load and after updates
function ensureAllPhotosLoad() {
    

    // Handle participant photos in list - focus only on photo loading
    var participantImages = document.querySelectorAll('.participant-photo, img[src*="photos"]');
    participantImages.forEach(function(img) {
        // Set up error handling for fallback
        img.onerror = function() {
            
            this.onerror = null;
            this.src = path + 'photos/img_user_nophoto.png?t=' + new Date().getTime();
            this.alt = 'Photo not available';
        };

        // Force reload if image is cached but not displaying
        if (img.complete && img.naturalWidth === 0) {
            var currentSrc = img.src;
            var timestamp = new Date().getTime();
            var cacheBuster = Math.random().toString(36).substring(7);
            if (currentSrc.indexOf('?') > -1) {
                img.src = currentSrc.split('?')[0] + '?t=' + timestamp + '&force=' + cacheBuster;
            } else {
                img.src = currentSrc + '?t=' + timestamp + '&force=' + cacheBuster;
            }
        }

        // Ensure photo displays by adding load event
        img.onload = function() {
        };
    });

    // Also handle modal photos - only photo loading logic
    var modalPhoto = document.getElementById('currentPhoto');
    if (modalPhoto) {
        modalPhoto.onerror = function() {
            this.onerror = null;
            this.src = path + 'photos/img_user_nophoto.png?t=' + new Date().getTime();
            this.alt = 'Photo not available';
        };

        modalPhoto.onload = function() {
        };
    }
}

// Function to initialize photo loading on page load
function initializePhotoLoading() {
    

    // Ensure all photos load properly immediately
    ensureAllPhotosLoad();

    // Refresh photos after a short delay to ensure DOM is fully loaded
    setTimeout(function() {
        
        refreshParticipantPhotos();
    }, 500);

    // Set up periodic refresh for participant list photos
    setInterval(function() {
        refreshParticipantPhotos();
    }, 30000); // Refresh every 30 seconds

    
}

// Enhanced function to get and display participant photos
function loadParticipantPhotos() {
    

    // Find all participant photo elements
    var participantImages = document.querySelectorAll('img[src*="photos"]');

    participantImages.forEach(function(img, index) {
        var currentSrc = img.src;

        // Skip if already has cache busting parameters
        if (currentSrc.indexOf('?') === -1 || (!currentSrc.includes('t=') && !currentSrc.includes('refresh='))) {
            var timestamp = new Date().getTime();
            var cacheBuster = Math.random().toString(36).substring(7);

            if (currentSrc.indexOf('?') > -1) {
                img.src = currentSrc + '&t=' + timestamp + '&refresh=' + cacheBuster;
            } else {
                img.src = currentSrc + '?t=' + timestamp + '&refresh=' + cacheBuster;
            }

            
        }

        // Ensure error handling
        img.onerror = function() {
            this.onerror = null;
            this.src = path + 'photos/img_user_nophoto.png?t=' + new Date().getTime();
            this.alt = 'Photo not available';
        };

        // Force reload for cached images
        if (img.complete && img.naturalWidth === 0) {
            var originalSrc = img.src.split('?')[0];
            var timestamp = new Date().getTime();
            img.src = originalSrc + '?reload=' + timestamp + '&force=' + Math.random().toString(36).substring(7);
        }
    });
}

// Call initialization when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializePhotoLoading();

    // Additional photo loading for page reload scenarios
    setTimeout(function() {
        loadParticipantPhotos();
    }, 100);
});

// Function to handle page reload photo refresh
function refreshPhotosOnReload() {
    
    loadParticipantPhotos();
}

// Call on window load as well for complete page loading
window.addEventListener('load', function() {
    setTimeout(function() {
        refreshPhotosOnReload();
    }, 200);
});

// Enhanced function to ensure photos load properly on page load
function ensurePhotosLoad() {
    

    // Add error handling to all participant photos
    var participantImages = document.querySelectorAll('.participant-photo, img[src*="photos"]');
    participantImages.forEach(function(img) {
        img.onerror = function() {
            
            this.onerror = null;
            var fallbackTimestamp = new Date().getTime();
            var fallbackCacheBuster = Math.random().toString(36).substring(7);
            this.src = path + 'photos/img_user_nophoto.png?t=' + fallbackTimestamp + '&cache=' + fallbackCacheBuster;
            this.alt = 'Photo not available';
        };

        // Force reload if image is cached but not displaying
        if (img.complete && img.naturalWidth === 0) {
            var currentSrc = img.src;
            var timestamp = new Date().getTime();
            var cacheBuster = Math.random().toString(36).substring(7);
            if (currentSrc.indexOf('?') > -1) {
                img.src = currentSrc.split('?')[0] + '?t=' + timestamp + '&force=' + cacheBuster + '&reload=' + timestamp;
            } else {
                img.src = currentSrc + '?t=' + timestamp + '&force=' + cacheBuster + '&reload=' + timestamp;
            }
        }
    });
}

// Function to force refresh all photos on the page
function forceRefreshAllPhotos() {
    

    var allImages = document.querySelectorAll('img[src*="photos"], .participant-photo');
    allImages.forEach(function(img, index) {
        var currentSrc = img.src;
        var baseSrc = '';

        // Get base URL without cache parameters
        if (currentSrc.indexOf('?') > -1) {
            baseSrc = currentSrc.split('?')[0];
        } else {
            baseSrc = currentSrc;
        }

        // Add comprehensive cache busting
        var timestamp = new Date().getTime();
        var cacheBuster = Math.random().toString(36).substring(7);
        var newSrc = baseSrc + '?t=' + timestamp + '&force=' + cacheBuster + '&refresh=' + cacheBuster + '&v=' + timestamp;

        img.src = newSrc;
        
    });

    // Also refresh no-photo images
    var noPhotoImages = document.querySelectorAll('img[src*="img_user_nophoto.png"]');
    noPhotoImages.forEach(function(img) {
        var timestamp = new Date().getTime();
        var cacheBuster = Math.random().toString(36).substring(7);
        if (img.src.indexOf('?') > -1) {
            img.src = img.src.split('?')[0] + '?t=' + timestamp + '&refresh=' + cacheBuster;
        } else {
            img.src = img.src + '?t=' + timestamp + '&refresh=' + cacheBuster;
        }
    });
}
