// JavaScript Document

function forceRemoveModalBackdrop() {
    var backdrops = document.querySelectorAll('.modal-backdrop');
    for (var i = 0; i < backdrops.length; i++) {
        backdrops[i].remove();
    }
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
}

(function () {
    document.addEventListener('hidden.bs.modal', function (event) {
        if (event.target && event.target.id === 'schoolMasterModal') {
            forceRemoveModalBackdrop();
        }
    });
})();

function schoolMasterLoadingHtml() {
    return '<div class="text-center py-5" id="schoolMasterLoading">' +
        '<div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">' +
        '<span class="visually-hidden">Loading...</span></div>' +
        '<p class="text-muted mt-3 mb-0">Loading school details...</p>' +
        '</div>';
}

function loadSchoolMasterModal(userId) {
    if (loadSchoolMasterModal._busy) {
        return;
    }
    loadSchoolMasterModal._busy = true;

    var params = 'UserIdty=' + encodeURIComponent(userId ? userId : '');
    var modalEl = document.getElementById('schoolMasterModal');
    if (!modalEl) {
        loadSchoolMasterModal._busy = false;
        return;
    }

    var targetContent = modalEl.querySelector('.modal-content');
    var targetBody = modalEl.querySelector('.modal-body');
    if (!targetContent || !targetBody) {
        loadSchoolMasterModal._busy = false;
        return;
    }

    targetBody.innerHTML = schoolMasterLoadingHtml();
    forceRemoveModalBackdrop();
    var existing = bootstrap.Modal.getInstance(modalEl);
    if (!existing) {
        existing = new bootstrap.Modal(modalEl);
    }
    existing.show();

    var xhr = new XMLHttpRequest();
    xhr.open('POST', path + 'schools/school_master/edit_school_detials', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onreadystatechange = function () {
        if (xhr.readyState !== 4) {
            return;
        }
        loadSchoolMasterModal._busy = false;

        var hideModal = function () {
            var inst = bootstrap.Modal.getInstance(modalEl);
            if (inst) {
                inst.hide();
            }
            forceRemoveModalBackdrop();
        };

        if (xhr.status !== 200) {
            hideModal();
            swal('Oops!', 'Unable to load the school form. Please try again.', 'error');
            return;
        }
        var resp = null;
        try {
            resp = JSON.parse(xhr.responseText);
        } catch (e) {
            resp = null;
        }
        if (!resp || !resp.success || !resp.html) {
            hideModal();
            swal('Oops!', resp && resp.message ? resp.message : 'Invalid server response.', 'error');
            return;
        }

        var temp = document.createElement('div');
        temp.innerHTML = resp.html.trim();
        var contentEl = temp.querySelector('.modal-content');
        var respBody = contentEl ? contentEl.querySelector('.modal-body') : null;
        if (!respBody) {
            hideModal();
            swal('Oops!', 'Invalid server response.', 'error');
            return;
        }

        targetBody.innerHTML = respBody.innerHTML;
        forceRemoveModalBackdrop();

        if (modalEl.classList.contains('show')) {
            if (!bootstrap.Modal.getInstance(modalEl)) {
                existing = new bootstrap.Modal(modalEl);
            }
            existing.show();
        }
    };
    xhr.send(params);
}

function showAddSchoolModal() {
    loadSchoolMasterModal('');
}

function fncAddSchool(usertype)
{
    var schoolName = document.getElementById('txtSchoolName');
    if (!schoolName || !schoolName.value.trim())
    {
        swal("Missing Information", "Please enter the school name.", "warning");
        if (schoolName) schoolName.focus();
        return false;
    }

    var schoolType = document.getElementById('cmbSchoolType');
    if (!schoolType || schoolType.value == 0 || schoolType.value == '')
    {
        swal("Missing Information", "Please select a school management type.", "warning");
        if (schoolType) schoolType.focus();
        return false;
    }

    if (usertype < 2) {
        var dist = document.getElementById('cmbDistrict');
        if (dist && (dist.value == 0 || dist.value == ''))
        {
            swal("Missing Information", "Please select a revenue district.", "warning");
            dist.focus();
            return false;
        }
    }
    if (usertype < 3) {
        var eduDist = document.getElementById('cmbEduDistrict');
        if (eduDist && (eduDist.value == 0 || eduDist.value == ''))
        {
            swal("Missing Information", "Please select an education district.", "warning");
            eduDist.focus();
            return false;
        }

        var subDist = document.getElementById('cmbSubDistrict');
        if (subDist && (subDist.value == 0 || subDist.value == ''))
        {
            swal("Missing Information", "Please select a sub-district.", "warning");
            subDist.focus();
            return false;
        }
    }

    var btn = document.getElementById('btnAddSchool') || document.getElementById('btnUpdateSchool') || document.querySelector('#formUser button[type="submit"]') || document.querySelector('#formUser button[type="button"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Updating School...';
    }

    var formUser = document.getElementById('formUser');
    if (formUser) {
        formUser.submit();
    }
    return true;
}

function fncUpdateSchool(userId, usertype) {
    var schoolName = document.getElementById('txtSchoolName');
    if (!schoolName || !schoolName.value.trim())
    {
        swal("Missing Information", "Please enter the school name.", "warning");
        if (schoolName) schoolName.focus();
        return false;
    }

    var schoolType = document.getElementById('cmbSchoolType');
    if (!schoolType || schoolType.value == 0 || schoolType.value == '')
    {
        swal("Missing Information", "Please select a school management type.", "warning");
        if (schoolType) schoolType.focus();
        return false;
    }

    if (usertype < 2) {
        var dist = document.getElementById('cmbDistrict');
        if (dist && (dist.value == 0 || dist.value == ''))
        {
            swal("Missing Information", "Please select a revenue district.", "warning");
            dist.focus();
            return false;
        }
    }
    if (usertype < 3) {
        var eduDist = document.getElementById('cmbEduDistrict');
        if (eduDist && (eduDist.value == 0 || eduDist.value == ''))
        {
            swal("Missing Information", "Please select an education district.", "warning");
            eduDist.focus();
            return false;
        }

        var subDist = document.getElementById('cmbSubDistrict');
        if (subDist && (subDist.value == 0 || subDist.value == ''))
        {
            swal("Missing Information", "Please select a sub-district.", "warning");
            subDist.focus();
            return false;
        }
    }

    var btn = document.getElementById('btnUpdateSchool') || document.getElementById('btnAddSchool') || document.querySelector('#formUser button[type="button"]') || document.querySelector('#formUser button[type="submit"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Updating School...';
    }

    var hidUserId = document.getElementById('hidUserId');
    if (hidUserId) hidUserId.value = userId;

    var formUser = document.getElementById('formUser');
    if (formUser) {
        formUser.action = path + 'schools/school_master/update_school_detials';
        formUser.submit();
    }
}


function deleteUser(userId) {
    if (!confirm('Really want to delete the user?')) {
        return false;
    }
    $('UserIdty').value = userId;
    $('editUser').action = path + 'schools/school_master/delete_school_detials';
    $('editUser').submit();
}

function editUser(userId) {
    loadSchoolMasterModal(userId);
    return false;
}


function cancel()
{
    var modalEl = document.getElementById('schoolMasterModal');
    if (modalEl) {
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
            forceRemoveModalBackdrop();
        } else {
            forceRemoveModalBackdrop();
        }
    }
    return false;
}

function loadEduDistrict()
{
    if ($('cmbDistrict').value > 0)
    {
        $('divEduDistrict').style.display = 'block';
        ajax_loder2('divEdudistrictCombo');
        if ($('cmbEduDistrict'))
            $('cmbEduDistrict').selectedIndex = 0;

        var district_code = $('cmbDistrict').value;
        var oOptions = {
            method: "post",
            parameters: {'district': district_code},
            onFailure: function (oXHR, oJson) {
                //swal("Oops!", "xxxx", "error");
    //alert("An error occurred: " + oXHR.status);
            },
            onSuccess: function (transport) {
                var response = transport.responseText;
                $('divEdudistrictCombo').innerHTML = response;
            }
        };
        var oRequest = new Ajax.Updater({
            //success: "divCustomerInfo"
        }, path + "ajax/loadajax/get_edu_district_details", oOptions);
    }
}

function loadSubDistrict()
{
    if ($('cmbEduDistrict').value > 0)
    {
        $('divSubdistrict').style.display = 'block';
        ajax_loder2('divSubdistrictCombo');
        if ($('cmbSubDistrict'))
            $('cmbSubDistrict').selectedIndex = 0;

        //var district_code	=	$('cmbDistrict').value;
        var edu_district = $('cmbEduDistrict').value;
        var oOptions = {
            method: "post",
            parameters: {'edu_district': edu_district},
            onFailure: function (oXHR, oJson) {
                //swal("Oops!", "xxxx", "error");
    //alert("An error occurred: " + oXHR.status);
            },
            onSuccess: function (transport) {
                var response = transport.responseText;
                $('divSubdistrictCombo').innerHTML = response;
            }
        };
        var oRequest = new Ajax.Updater({
            //success: "divCustomerInfo"
        }, path + "ajax/loadajax/get_subdistrict_details_of_edu_district", oOptions);
    }
}

function loadSchool()
{
    return;
}

function loadSubDistrictFilter()
{
    if ($('cmbDistrictFilter').value > 0)
    {
        $('divSubDistrictFilter').style.display = 'block';
        ajax_loder2('divSubDistrictFilter');
        if ($('cmbSubDistrictFilter'))
            $('cmbSubDistrictFilter').selectedIndex = 0;
        if ($('cmbSchoolFilter'))
            $('cmbSchoolFilter').selectedIndex = 0;

        var district_code = $('cmbDistrictFilter').value;
        var oOptions = {
            method: "post",
            parameters: {'district': district_code, 'name': 'cmbSubDistrictFilter', 'function': 'loadSchoolFilter'},
            onFailure: function (oXHR, oJson) {
                //swal("Oops!", "xxxx", "error");
    //alert("An error occurred: " + oXHR.status);
            },
            onSuccess: function (transport) {
                var response = transport.responseText;
                $('divSubDistrictFilter').innerHTML = response;
            }
        };
        var oRequest = new Ajax.Updater({
            //success: "divCustomerInfo"
        }, path + "ajax/loadajax/get_subdistrict_details_small", oOptions);
    }
}

