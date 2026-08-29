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
    var clear = document.createElement('div');
    clear.className = 'clear_both';
    porow.className = 'teachersTextBox';

    // Create teacher input field
    var teacher = document.createElement('input');
    teacher.type = 'text';
    teacher.id = 'txtTeacher_' + id;
    teacher.name = 'txtTeacher_' + id;
    teacher.className = 'form-control';
    teacher.onkeyup = function() { this.value = this.value.toUpperCase(); };

    // Create phone input field
    var phone = document.createElement('input');
    phone.type = 'text';
    phone.id = 'txtPhone_' + id;
    phone.name = 'txtPhone_' + id;
    phone.className = 'form-control';
    phone.maxLength = '11';
    phone.onkeypress = function() { return numbersonly(this, event, false); };

    // Create phone label
    var phone1 = document.createElement('label');
    phone1.id = 'txtPhone1_' + id;
    phone1.innerHTML = '&nbsp;&nbsp;&nbsp;Phone : ';

    // Append elements to the row
    porow.appendChild(teacher);
    porow.appendChild(phone1);
    porow.appendChild(phone);

    // Append to teachers row
    $("teachersRow").appendChild(clear);
    $("teachersRow").appendChild(porow);

    // Update hidden field
    $('hidTeachers').value = id;
}

function addTotal()
{
    var total = 0;
    if (trim($('txtTotalLP').value) != '')
        total += parseInt($('txtTotalLP').value * 1);
    if (trim($('txtTotalUP').value) != '')
        total += parseInt($('txtTotalUP').value * 1);
    if (trim($('txtTotalHS').value) != '')
        total += parseInt($('txtTotalHS').value * 1);
    if (trim($('txtTotalHSS').value) != '')
        total += parseInt($('txtTotalHSS').value * 1);
    if (trim($('txtTotalVHSS').value) != '')
        total += parseInt($('txtTotalVHSS').value * 1);
    $('txtTotal').value = total;
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


    var school_code = $('txtSchoolCode').value;
    ajax_loder2('divSchoolCode');
    var oOptions = {
        method: "post",
        parameters: {'code': school_code},
        onFailure: function (oXHR, oJson) {
        //swal ( "Oops!" ,  "An error occurred: " + oXHR.status" ,  "error" )
        //alert("An error occurred: " + oXHR.status);
        },
        onSuccess: function (transport) {
            var response = transport.responseText;

            $('divEntryForm').innerHTML = response;

            // Show Add Participant button after successful school data load
            var addParticipantBtn = document.getElementById('addParticipantBtn');
            if (addParticipantBtn) {
                addParticipantBtn.style.display = 'inline-block';
            }

            // Close School Details modal when school data is loaded
            var schoolModal = bootstrap.Modal.getInstance(document.getElementById('schoolDetailsModal'));
            if (schoolModal) {
                schoolModal.hide();
            }
        }
    };
    var oRequest = new Ajax.Updater({
        //success: "divCustomerInfo"
    }, path + "schools/registration/get_school_details", oOptions);
}


function fetch_special_order_school_details()
{
    var school_code = $('txtSchoolCode').value;
    ajax_loder2('divSchoolCode');
    var oOptions = {
        method: "post",
        parameters: {'code': school_code},
        onFailure: function (oXHR, oJson) {
        swal ( "Oops!" ,  "An error occurred: " + oXHR.status,  "error" );
        //alert("An error occurred: " + oXHR.status);
        },
        onSuccess: function (transport) {
            var response = transport.responseText;
            $('divEntryForm').innerHTML = response;

            // Show Add Participant button after successful school data load
            var addParticipantBtn = document.getElementById('addParticipantBtn');
            if (addParticipantBtn) {
                addParticipantBtn.style.display = 'inline-block';
            }

            // Close School Details modal when school data is loaded
            var schoolModal = bootstrap.Modal.getInstance(document.getElementById('schoolDetailsModal'));
            if (schoolModal) {
                schoolModal.hide();
            }
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
        if (trim($(name).value) != '') {
            name_con = true;
        }
        if (trim($(phone).value) != '') {
            if (trim($(name).value) == '')
            {
                swal ( "Oops!" ,  "Please enter the Team Manager name" ,  "error" )
                //alert('Please enter the Team Manager name');
                $(name).focus();
                return false;
            }
            phone_con = true;
            /*else if(trim($(phone).value) == '')
             {
             swal ( "Oops!" ,  "Please enter the Team Manager phone" ,  "error" )
            //alert('Please enter the Team Manager phone');
             $(phone).focus();
             return false;
             }*/
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
        $('txtPhone_1').focus();
        return false;
    }
    $('formSchool').submit();
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
    $('formSchool').action = path + 'schools/registration/edit_school_details';
    $('formSchool').submit();
}

function fncSaveParticipant() {
    if (trim($('txtADNO').value) == '') {
        swal ( "Oops!" ,  "Please enter the admission number" ,  "error" )
        //alert('Please enter the admission number');
        $('txtADNO').focus();
        return false;
    }
    var admn_no = trim($('txtADNO').value);
    if (trim($('txtClass').value) > 10)
    {
        var hss_vhse_chr = trim($('txtADNO').value).substring(0, 1);
        if (!((hss_vhse_chr == 'V' && $('hidVHSSNo').value > 0) || (hss_vhse_chr == 'H' && $('hidHSSNo').value > 0)))
        {
            swal ( "Oops!" ,  "Invalid admission number" ,  "error" )
        //alert('Invalid admission number');
            $('txtADNO').focus();
            return false;
        }
        admn_no = trim($('txtADNO').value).substring(1);
    }

    if (isNaN(admn_no))
    {
        swal ( "Oops!" ,  "Invalid admission number" ,  "error" )
        //alert('Invalid admission number');
        $('txtADNO').focus();
        return false;
    }

    if (trim($('txtParticipantName').value) == '') {
        swal ( "Oops!" ,  "Please enter the participant name" ,  "error" )
        //alert('Please enter the participant name');
        $('txtParticipantName').focus();
        return false;
    }else{
        var alpha = /^[a-zA-Z\s.]+$/;
        if (!$('txtParticipantName').value.match(alpha)) {
            swal ( "Oops!" ,  "Please enter a valid name" ,  "error" )
        //alert('Please enter a valid name.');
            $('txtParticipantName').focus();
            return false;
        }
    }

    

    var items = 0;
    var fieldId = '';
    var fieldElement;
    for (var i = 1; i <= 10; i++) {
        fieldId = 'txtItemCode_' + i;
        fieldElement = document.getElementById(fieldId);
        if (fieldElement && trim(fieldElement.value) != '') {
            items++;
        }
    }
    var pinnani = 0;
    var pinnanyId = '';
    var pinnanyElement;
    for (var i = 1; i <= 5; i++) {
        pinnanyId = 'txtPinnanyCode_' + i;
        pinnanyElement = document.getElementById(pinnanyId);
        if (pinnanyElement && trim(pinnanyElement.value) != '') {
            pinnani++;
        }
    }

    if ((items == 0) && (pinnani == 0)) {
        swal ( "Oops!" ,  "Please enter the item code" ,  "error" )
        //alert('Please enter the item code');
        var firstItem = document.getElementById('txtItemCode_1');
        if (firstItem) firstItem.focus();
        return false;
    }
    
    // Show spinner on button
    var saveBtn = document.getElementById('save_participant_btn');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
    }

    // Store original button state for potential refresh
    if (saveBtn) {
        saveBtn.setAttribute('data-original-text', 'Save Participant');
    }

    document.getElementById('formParticipant').submit();
}

function editParticipant(adno) {
    var oOptions = {
        method: "post",
        parameters: {'hidSchoolId': $('hidSchoolId').value, 'hidADNO': adno},
        onFailure: function (oXHR, oJson) {
            alert("An error occurred: " + oXHR.status);
        },
        onSuccess: function (transport) {
            var response = transport.responseText;
            var data = JSON.parse(response);
            
            // populate fields with null checks
            var el;
            el = document.getElementById('hidADNO');
            if (el) el.value = adno;
            
            el = document.getElementById('txtADNO');
            if (el) el.value = data.admn_no || '';
            
            el = document.getElementById('txtParticipantName');
            if (el) el.value = data.participant_name || '';
            
            el = document.getElementById('txtClass');
            if (el) el.value = data.class || '';
            
            el = document.getElementById('txtGender');
            if (el) el.value = data.gender || '';
            
            // Clear all item codes first
            for (let i = 1; i <= 10; i++) {
                el = document.getElementById('txtItemCode_' + i);
                if (el) el.value = '';
            }
            
            // Populate item codes from data
            if (data.items && Array.isArray(data.items)) {
                for (let i = 0; i < data.items.length && i < 10; i++) {
                    el = document.getElementById('txtItemCode_' + (i + 1));
                    if (el && data.items[i] && data.items[i].item_code) {
                        el.value = data.items[i].item_code;
                    }
                }
            }
            
            // Clear all pinnany codes first
            for (let i = 1; i <= 5; i++) {
                el = document.getElementById('txtPinnanyCode_' + i);
                if (el) el.value = '';
            }
            
            // Populate pinnany codes from data
            if (data.pinnany && Array.isArray(data.pinnany)) {
                for (let i = 0; i < data.pinnany.length && i < 5; i++) {
                    el = document.getElementById('txtPinnanyCode_' + (i + 1));
                    if (el && data.pinnany[i] && data.pinnany[i].pinnany_code) {
                        el.value = data.pinnany[i].pinnany_code;
                    }
                }
            }
            
            el = document.getElementById('participantModalLabel');
            if (el) el.innerText = 'Edit Participant';
            
            el = document.getElementById('save_participant_btn');
            if (el) {
                el.innerText = 'Update Participant';
                el.disabled = false;
                el.setAttribute('onclick', 'fncUpdateParticipant(\'' + adno + '\')');
            }

            // Handle photo display in edit modal with enhanced cache busting
            var photoPreviewContainer = document.getElementById('photoPreviewContainer');
            var noPhotoMessage = document.getElementById('noPhotoMessage');
            var currentPhoto = document.getElementById('currentPhoto');

            if (data.photo_url && data.photo_url.trim() !== '' && data.photo_url !== 'false') {
                
                // Show photo preview with enhanced cache busting
                if (photoPreviewContainer) photoPreviewContainer.style.display = 'block';
                if (noPhotoMessage) noPhotoMessage.style.display = 'none';
                if (currentPhoto) {
                    // Add cache busting to ensure fresh photo load
                    var enhancedPhotoUrl = data.photo_url;
                    var timestamp = new Date().getTime();
                    var cacheBuster = Math.random().toString(36).substring(7);

                    if (enhancedPhotoUrl.indexOf('?') > -1) {
                        enhancedPhotoUrl += '&edit=' + timestamp + '&cache=' + cacheBuster + '&modal=1';
                    } else {
                        enhancedPhotoUrl += '?edit=' + timestamp + '&cache=' + cacheBuster + '&modal=1';
                    }

                    
                    currentPhoto.src = enhancedPhotoUrl;
                    currentPhoto.style.display = 'block';

                    currentPhoto.onload = function() {
                    };

                    currentPhoto.onerror = function() {
                        this.onerror = null;
                        var fallbackTimestamp = new Date().getTime();
                        var fallbackCacheBuster = Math.random().toString(36).substring(7);
                        this.src = path + 'photos/img_user_nophoto.png?t=' + fallbackTimestamp + '&cache=' + fallbackCacheBuster;
                        this.alt = 'Photo not available';
                    };
                }
            } else {
                
                // Show no photo message
                if (photoPreviewContainer) photoPreviewContainer.style.display = 'none';
                if (noPhotoMessage) noPhotoMessage.style.display = 'block';
                if (currentPhoto) {
                    currentPhoto.src = '';
                    currentPhoto.style.display = 'none';
                }
            }

            // show modal
            var myModal = new bootstrap.Modal(document.getElementById('participantModal'));
            myModal.show();
        }
    };
    var oRequest = new Ajax.Request(path + "schools/registration/get_participant_data", oOptions);
}

function deleteParticipant(adno) {
    

    // Use browser confirm directly to avoid SweetAlert2 issues
    if (confirm('Do you really want to delete the participant?')) {
        

        try {
            // Set form values using multiple fallback methods
            var hidADNO = null;
            var formParticipant = null;

            // Try multiple ways to find elements
            hidADNO = document.getElementById('hidADNO');
            if (!hidADNO) {
                hidADNO = document.querySelector('input[name="hidADNO"]');
            }
            if (!hidADNO) {
                hidADNO = document.querySelector('input[id="hidADNO"]');
            }

            formParticipant = document.getElementById('formParticipant');
            if (!formParticipant) {
                formParticipant = document.querySelector('form[action*="delete_participant_details"]');
            }
            if (!formParticipant) {
                formParticipant = document.querySelector('form[id="formParticipant"]');
            }

            if (hidADNO) {
                hidADNO.value = adno;
                
            } else {
                console.error('CRITICAL: hidADNO element not found!');
                alert('Error: Could not find form field. Please refresh the page and try again.');
                return;
            }

            if (formParticipant) {
                formParticipant.action = path + 'schools/registration/delete_participant_details';
                

                // Submit form
                formParticipant.submit();
                
            } else {
                console.error('CRITICAL: formParticipant element not found!');
                alert('Error: Could not find form. Please refresh the page and try again.');
                return;
            }

            
        } catch (error) {
            console.error('Error during delete operation:', error);
            alert('Error during delete operation: ' + error.message);
        }
    } else {
        
    }
}



function fncCancelParticipant() {
    $('formParticipant').action = path + 'schools/registration';
    $('formParticipant').submit();
}

function fncUpdateParticipant(adno) {
    if (trim($('txtADNO').value) == '') {
        swal ( "Oops!" ,  "Please enter the admission number" ,  "error" )
        //alert('Please enter the admission number');
        $('txtADNO').focus();
        return false;
    }
    var admn_no = trim($('txtADNO').value);
    if (trim($('txtClass').value) > 10)
    {
        var hss_vhse_chr = trim($('txtADNO').value).substring(0, 1);
        if (!((hss_vhse_chr == 'V' && $('hidVHSSNo').value > 0) || (hss_vhse_chr == 'H' && $('hidHSSNo').value > 0)))
        {
            swal ( "Oops!" ,  "Invalid admission number" ,  "error" )
        //alert('Invalid admission number');
            $('txtADNO').focus();
            return false;
        }
        admn_no = trim($('txtADNO').value).substring(1);
    }

    if (isNaN(admn_no))
    {
        swal ( "Oops!" ,  "Invalid admission number" ,  "error" )
        //alert('Invalid admission number');
        $('txtADNO').focus();
        return false;
    }

    if (trim($('txtParticipantName').value) == '') {
        swal ( "Oops!" ,  "Please enter the participant name" ,  "error" )
        //alert('Please enter the participant name');
        $('txtParticipantName').focus();
        return false;
    }else{
        var alpha = /^[a-zA-Z\s.]+$/;
        if (!$('txtParticipantName').value.match(alpha)) {
            swal ( "Oops!" ,  "Please enter a valid name" ,  "error" )
        //alert('Please enter a valid name.');
            $('txtParticipantName').focus();
            return false;
        }
    }

    // Show original participant photo before update
    var photoPreviewContainer = document.getElementById('photoPreviewContainer');
    var noPhotoMessage = document.getElementById('noPhotoMessage');
    var currentPhoto = document.getElementById('currentPhoto');

    // Make AJAX call to get current participant photo
    var photoData = null;

    var oOptions = {
        method: "post",
        asynchronous: false,
        parameters: {'hidSchoolId': $('hidSchoolId').value, 'hidADNO': adno},
        onFailure: function (oXHR, oJson) {
            console.error("Error getting participant photo: " + oXHR.status);
        },
        onSuccess: function (transport) {
            var response = transport.responseText;
            photoData = JSON.parse(response);
        }
    };

    // Make synchronous AJAX call to get photo data before form submission
    if (typeof Ajax !== 'undefined' && Ajax.Request) {
        var oRequest = new Ajax.Request(path + "schools/registration/get_participant_data", oOptions);
    } else {
        // Fallback for browsers that don't support Ajax.Request
        var xhr = new XMLHttpRequest();
        xhr.open("POST", path + "schools/registration/get_participant_data", false);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        var params = 'hidSchoolId=' + encodeURIComponent($('hidSchoolId').value) + '&hidADNO=' + encodeURIComponent(adno);
        xhr.send(params);

        if (xhr.status === 200) {
            photoData = JSON.parse(xhr.responseText);
        }
    }

    // Display the photo if data was retrieved successfully
    if (photoData && photoData.photo_url && photoData.photo_url.trim() !== '') {
        // Show photo preview with enhanced cache busting
        if (photoPreviewContainer) photoPreviewContainer.style.display = 'block';
        if (noPhotoMessage) noPhotoMessage.style.display = 'none';
        if (currentPhoto) {
            // Add cache busting to ensure fresh photo load
            var enhancedPhotoUrl = photoData.photo_url;
            var timestamp = new Date().getTime();
            var cacheBuster = Math.random().toString(36).substring(7);

            if (enhancedPhotoUrl.indexOf('?') > -1) {
                enhancedPhotoUrl += '&update=' + timestamp + '&cache=' + cacheBuster;
            } else {
                enhancedPhotoUrl += '?update=' + timestamp + '&cache=' + cacheBuster;
            }

            currentPhoto.src = enhancedPhotoUrl;
            currentPhoto.style.display = 'block';

            currentPhoto.onload = function() {
            };

            currentPhoto.onerror = function() {
                this.onerror = null;
                this.src = path + 'photos/img_user_nophoto.png?t=' + new Date().getTime();
                this.alt = 'Photo not available';
            };
        }
    } else {
        // Show no photo message
        if (photoPreviewContainer) photoPreviewContainer.style.display = 'none';
        if (noPhotoMessage) noPhotoMessage.style.display = 'block';
        if (currentPhoto) {
            currentPhoto.src = '';
            currentPhoto.style.display = 'none';
        }
    }



    var items = 0;
    var fieldId = '';
    var fieldElement;
    for (var i = 1; i <= 10; i++) {
        fieldId = 'txtItemCode_' + i;
        fieldElement = document.getElementById(fieldId);
        if (fieldElement && trim(fieldElement.value) != '') {
            items++;
        }
    }
    var pinnani = 0;
    var pinnanyId = '';
    var pinnanyElement;
    for (var i = 1; i <= 5; i++) {
        pinnanyId = 'txtPinnanyCode_' + i;
        pinnanyElement = document.getElementById(pinnanyId);
        if (pinnanyElement && trim(pinnanyElement.value) != '') {
            pinnani++;
        }
    }

    if ((items == 0) && (pinnani == 0)) {
        swal ( "Oops!" ,  "Please enter the item code" ,  "error" )
        //alert('Please enter the item code');
        var firstItem = document.getElementById('txtItemCode_1');
        if (firstItem) firstItem.focus();
        return false;
    }

    var hidAdnoEl = document.getElementById('hidADNO');
    if (hidAdnoEl) hidAdnoEl.value = adno;
    
    // Show spinner on button
    var saveBtn = document.getElementById('save_participant_btn');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';
        // Store original button state for potential refresh
        saveBtn.setAttribute('data-original-text', 'Update Participant');
    }

    document.getElementById('formParticipant').action = path + 'schools/registration/update_participant_details';
    document.getElementById('formParticipant').submit();

    // Enhanced photo refresh for participant list after update
    setTimeout(function() {
        refreshParticipantPhotos();
        
    }, 1500);
}

function fnschgpwdAdd() {

    if ($('txtOLDPassword').value == "") {
        swal ( "Oops!" ,  "please enter current password" ,  "error" )
        //alert("please enter current password");
        $('txtOLDPassword').focus();
        return false;
    }

    if ($('txtNewPassword').value == "") {
        swal ( "Oops!" ,  "please enter password" ,  "error" )
        //alert("please enter password");
        $('txtNewPassword').focus();
        return false;
    }
    if ($('txtCFMPassword').value == "") {
        swal ( "Oops!" ,  "please Re-Type your password" ,  "error" )
        //alert("please Re-Type your password");
        $('txtCFMPassword').focus();
        return false;
    }
    if (($('txtNewPassword').value) != ($('txtCFMPassword').value)) {
        swal ( "Oops!" ,  "Your reentered  password is not correct" ,  "error" )
        //alert("Your reentered  password is not correct");
        $('txtCFMPassword').focus();
        return false;
    }

    if (($('txtOLDPassword').value) == ($('txtNewPassword').value))
    {
        swal ( "Oops!" ,  "Old Password and New Password are Same!!! Please enter a new one" ,  "error" )
        //alert("Old Password and New Password are Same!!! Please enter a new one");
        $('txtNewPassword').focus();
        return false;
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
    $('hidClusterId').value = shid;
    $('clustschool').action = path + 'welcome/cluster_schools/';
    $('clustschool').submit();
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

    // Debug: Check what elements exist
    
    var testElement = $(element_id);
    

    if (testElement) {
        
        
    }

    // Check if element exists
    if (!testElement) {
        console.error('CRITICAL ERROR: Element not found:', element_id);
        console.error('Available elements with confirm in ID:');
        var allElements = document.getElementsByTagName('*');
        for (var i = 0; i < allElements.length; i++) {
            if (allElements[i].id && allElements[i].id.indexOf('confirm') !== -1) {
                console.error('Found element:', allElements[i].id, 'Tag:', allElements[i].tagName);
            }
        }
        alert('Error: Element not found - ' + element_id);
        return;
    }

    
    

    message = 'Do you really want to reset the confirmation status of ' + message;
    
    if (confirm(message))
    {
        
        
        ajax_loder2(element_id);
        var oOptions = {
            method: "post",
            parameters: {'school_code': school_code},
            onFailure: function (oXHR, oJson) {
                console.error('AJAX failure in common.js:', oXHR, oJson);
                swal ( "Oops!" ,  "An error occurred: " + oXHR.status ,  "error" )
                //alert("An error occurred: " + oXHR.status);
            },
            onSuccess: function (transport) {
                var response = transport.responseText;
                

                // Check if elements exist before updating
                var element = $(element_id);
                var disElement = $(element_id + '_dis');

                if (!element) {
                    console.error('Element not found after AJAX:', element_id);
                    return;
                }

                if (response == 'Yes' || response == 'No') {
                    element.innerHTML = response;
                    if (disElement) disElement.innerHTML = '';
                    
                    
                } else {
                    console.error('Unexpected response:', response);
                    console.error('Response type:', typeof response);
                    alert('Unexpected response: ' + response);
                }
            }
        };
        
        
        

        if (typeof Ajax !== 'undefined' && Ajax.Request) {
            
            var oRequest = new Ajax.Request(path + "ajax/change_confirmation_status", oOptions);
        } else {
            
            // Fallback for browsers that don't support Ajax.Request
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
                alert('Network error: ' + error.message);
            });
        }
    }
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
    }
    for (let i = 1; i <= 5; i++) {
        el = document.getElementById('txtPinnanyCode_' + i);
        if (el) el.value = '';
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
