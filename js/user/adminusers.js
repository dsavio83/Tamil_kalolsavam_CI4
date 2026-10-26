// JavaScript Document - Admin Users Management

document.addEventListener('DOMContentLoaded', function() {
    // Ensure modal is attached directly to document.body to prevent Bootstrap backdrop stacking traps
    var modalEl = document.getElementById('userModal');
    if (modalEl && modalEl.parentNode !== document.body) {
        document.body.appendChild(modalEl);
    }
});

/**
 * Open Modal for Adding a New Admin User
 */
function openAddUserModal() {
    var modalEl = document.getElementById('userModal');
    if (!modalEl) return;

    // Ensure modal is attached to body
    if (modalEl.parentNode !== document.body) {
        document.body.appendChild(modalEl);
    }

    // Reset submit button state
    var submitBtn = document.getElementById('btnSubmitUser');
    if (submitBtn) submitBtn.disabled = false;
    var submitIcon = document.getElementById('btnSubmitUserIcon');
    if (submitIcon) submitIcon.classList.remove('d-none');
    var submitSpinner = document.getElementById('btnSubmitUserSpinner');
    if (submitSpinner) submitSpinner.classList.add('d-none');
    if (document.getElementById('btnSubmitUserText')) document.getElementById('btnSubmitUserText').innerText = 'Save Admin User';

    // Reset hidden & form fields
    if (document.getElementById('hidUserId')) document.getElementById('hidUserId').value = '';
    if (document.getElementById('txtNewUserName')) document.getElementById('txtNewUserName').value = '';
    if (document.getElementById('txtNewPassword')) {
        document.getElementById('txtNewPassword').value = '';
        document.getElementById('txtNewPassword').placeholder = 'Enter password';
    }
    if (document.getElementById('pwdRequiredStar')) document.getElementById('pwdRequiredStar').style.display = 'inline';
    if (document.getElementById('pwdHelpText')) document.getElementById('pwdHelpText').innerText = 'Minimum 4 characters recommended.';

    // Reset role and territory
    if (document.getElementById('userType')) document.getElementById('userType').value = '0';
    if (document.getElementById('cmbDistrict')) document.getElementById('cmbDistrict').value = '0';
    
    var divDist = document.getElementById('divDistrict');
    var divSub = document.getElementById('divSubdistrict');
    var divSch = document.getElementById('divSchool');
    if (divDist) divDist.style.display = 'none';
    if (divSub) divSub.style.display = 'none';
    if (divSch) divSch.style.display = 'none';

    // Reset Titles & Actions
    if (document.getElementById('userModalLabel')) document.getElementById('userModalLabel').innerText = 'Add New Admin User';
    if (document.getElementById('userModalSubTitle')) document.getElementById('userModalSubTitle').innerText = 'Configure administrative credentials and territorial access.';
    
    var iconContainer = document.getElementById('modalIconContainer');
    if (iconContainer) {
        iconContainer.className = 'rounded-3 bg-primary-subtle text-primary border border-primary-subtle p-2 d-inline-flex align-items-center justify-content-center';
    }
    var modalIcon = document.getElementById('userModalIcon');
    if (modalIcon) {
        modalIcon.className = 'bi bi-person-plus-fill fs-5';
    }

    var formUser = document.getElementById('formUser');
    if (formUser) {
        formUser.action = path + 'user/admin_users/add_admin';
    }

    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        var myModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        myModal.show();
    }
}

/**
 * Open Modal for Editing an Existing Admin User
 * Supports either an HTML element (with data-* attributes) or direct arguments
 */
function openEditUserModal(elemOrId, userName, userType, revDistrictCode, subDistrictCode, schoolCode) {
    var modalEl = document.getElementById('userModal');
    if (!modalEl) return;

    var clickedBtn = (typeof elemOrId === 'object' && elemOrId !== null && elemOrId.nodeType === 1) ? elemOrId : null;
    var originalBtnHtml = '';
    if (clickedBtn) {
        originalBtnHtml = clickedBtn.innerHTML;
        clickedBtn.disabled = true;
        clickedBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span><span>Loading...</span>';
    }

    function restoreEditBtn() {
        if (clickedBtn && originalBtnHtml) {
            clickedBtn.innerHTML = originalBtnHtml;
            clickedBtn.disabled = false;
            clickedBtn = null;
        }
    }

    // Ensure modal is attached to body
    if (modalEl.parentNode !== document.body) {
        document.body.appendChild(modalEl);
    }

    var userId = '';
    if (clickedBtn) {
        userId = clickedBtn.getAttribute('data-user-id') || (clickedBtn.dataset ? clickedBtn.dataset.userId : '');
        userName = clickedBtn.getAttribute('data-user-name') || (clickedBtn.dataset ? clickedBtn.dataset.userName : '');
        userType = clickedBtn.getAttribute('data-user-type') || (clickedBtn.dataset ? clickedBtn.dataset.userType : '');
        revDistrictCode = clickedBtn.getAttribute('data-district') || (clickedBtn.dataset ? clickedBtn.dataset.district : '');
        subDistrictCode = clickedBtn.getAttribute('data-subdistrict') || (clickedBtn.dataset ? clickedBtn.dataset.subdistrict : '');
        schoolCode = clickedBtn.getAttribute('data-school') || (clickedBtn.dataset ? clickedBtn.dataset.school : '');
    } else {
        userId = elemOrId;
    }

    // Reset submit button state
    var submitBtn = document.getElementById('btnSubmitUser');
    if (submitBtn) submitBtn.disabled = false;
    var submitIcon = document.getElementById('btnSubmitUserIcon');
    if (submitIcon) submitIcon.classList.remove('d-none');
    var submitSpinner = document.getElementById('btnSubmitUserSpinner');
    if (submitSpinner) submitSpinner.classList.add('d-none');
    if (document.getElementById('btnSubmitUserText')) document.getElementById('btnSubmitUserText').innerText = 'Update Admin User';

    // Set user ID and username
    if (document.getElementById('hidUserId')) document.getElementById('hidUserId').value = userId;
    if (document.getElementById('txtNewUserName')) document.getElementById('txtNewUserName').value = userName;
    if (document.getElementById('txtNewPassword')) {
        document.getElementById('txtNewPassword').value = '';
        document.getElementById('txtNewPassword').placeholder = 'Leave blank to keep unchanged';
    }
    if (document.getElementById('pwdRequiredStar')) document.getElementById('pwdRequiredStar').style.display = 'none';
    if (document.getElementById('pwdHelpText')) document.getElementById('pwdHelpText').innerText = 'Leave blank to preserve current active password.';

    // Set User Type
    if (document.getElementById('userType')) {
        document.getElementById('userType').value = userType;
    }

    // Modal Titles & Button Styling
    if (document.getElementById('userModalLabel')) document.getElementById('userModalLabel').innerText = 'Edit Admin User: ' + userName;
    if (document.getElementById('userModalSubTitle')) document.getElementById('userModalSubTitle').innerText = 'Update credentials, roles, and territorial jurisdiction.';
    
    var iconContainer = document.getElementById('modalIconContainer');
    if (iconContainer) {
        iconContainer.className = 'rounded-3 bg-warning-subtle text-warning-emphasis border border-warning-subtle p-2 d-inline-flex align-items-center justify-content-center';
    }
    var modalIcon = document.getElementById('userModalIcon');
    if (modalIcon) {
        modalIcon.className = 'bi bi-pencil-square fs-5';
    }

    var formUser = document.getElementById('formUser');
    if (formUser) {
        formUser.action = path + 'user/admin_users/update_admin_detials';
    }

    // Configure Jurisdiction Selects
    var divDist = document.getElementById('divDistrict');
    var divSub = document.getElementById('divSubdistrict');
    var divSch = document.getElementById('divSchool');

    if (userType == 1) {
        // State Admin
        if (divDist) divDist.style.display = 'none';
        if (divSub) divSub.style.display = 'none';
        if (divSch) divSch.style.display = 'none';
    } else {
        if (divDist) divDist.style.display = 'block';
        if (document.getElementById('cmbDistrict')) {
            document.getElementById('cmbDistrict').value = revDistrictCode || 0;
        }

        if (userType > 2 && revDistrictCode) {
            if (divSub) divSub.style.display = 'block';
            loadSubDistrict(subDistrictCode, schoolCode);
        } else {
            if (divSub) divSub.style.display = 'none';
            if (divSch) divSch.style.display = 'none';
        }
    }

    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        var myModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        myModal.show();
        setTimeout(restoreEditBtn, 400);
    } else {
        restoreEditBtn();
    }
}

function setSubmitSpinner(isLoading, loadingText) {
    var submitBtn = document.getElementById('btnSubmitUser');
    var submitIcon = document.getElementById('btnSubmitUserIcon');
    var submitSpinner = document.getElementById('btnSubmitUserSpinner');
    var submitText = document.getElementById('btnSubmitUserText');
    if (submitBtn) {
        submitBtn.disabled = isLoading;
    }
    if (submitIcon) {
        if (isLoading) submitIcon.classList.add('d-none');
        else submitIcon.classList.remove('d-none');
    }
    if (submitSpinner) {
        if (isLoading) submitSpinner.classList.remove('d-none');
        else submitSpinner.classList.add('d-none');
    }
    if (submitText && loadingText) {
        submitText.innerText = loadingText;
    }
}

/**
 * Handle Modal Submit (Add or Update)
 */
function submitAdminUserForm() {
    var userId = document.getElementById('hidUserId') ? document.getElementById('hidUserId').value : '';
    if (userId && userId !== '') {
        // Editing existing user
        if (!document.getElementById('txtNewUserName').value.trim()) {
            if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                Swal.fire({ title: "Validation Error", text: "Please enter a user name", icon: "warning", confirmButtonColor: "#0d6efd" });
            } else if (typeof swal === 'function') {
                swal("Oops!", "Please enter user name", "error");
            } else {
                alert("Please enter user name");
            }
            document.getElementById('txtNewUserName').focus();
            return false;
        }
        if (document.getElementById('userType').value == 0) {
            if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                Swal.fire({ title: "Validation Error", text: "Please select a user role", icon: "warning", confirmButtonColor: "#0d6efd" });
            } else if (typeof swal === 'function') {
                swal("Oops!", "Select user role", "error");
            } else {
                alert("Select user role");
            }
            document.getElementById('userType').focus();
            return false;
        }
        setSubmitSpinner(true, 'Updating...');
        fnsUserUpdate(userId);
    } else {
        // Adding new user
        if (fnsUserAdd()) {
            setSubmitSpinner(true, 'Saving...');
            var form = document.getElementById('formUser');
            if (form) {
                form.action = path + 'user/admin_users/add_admin';
                form.submit();
            }
        }
    }
}

function fnsUserAdd()
{
    if (!document.getElementById('txtNewUserName') || !document.getElementById('txtNewUserName').value.trim())
    {
        if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
            Swal.fire({ title: "Validation Error", text: "Please enter user name", icon: "warning", confirmButtonColor: "#0d6efd" });
        } else if (typeof swal === 'function') swal("Oops!", "Please Enter User name", "error");
        else alert("Please Enter User name");
        if (document.getElementById('txtNewUserName')) document.getElementById('txtNewUserName').focus();
        return false;
    }
    if (!document.getElementById('txtNewPassword') || !document.getElementById('txtNewPassword').value.trim())
    {
        if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
            Swal.fire({ title: "Validation Error", text: "Please enter password", icon: "warning", confirmButtonColor: "#0d6efd" });
        } else if (typeof swal === 'function') swal("Oops!", "Please Enter Password", "error");
        else alert("Please Enter Password");
        if (document.getElementById('txtNewPassword')) document.getElementById('txtNewPassword').focus();
        return false;
    }
    if (document.getElementById('userType') && document.getElementById('userType').value == 0)
    {
        if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
            Swal.fire({ title: "Validation Error", text: "Please select user role", icon: "warning", confirmButtonColor: "#0d6efd" });
        } else if (typeof swal === 'function') swal("Oops!", "Select User type", "error");
        else alert("Select User type");
        document.getElementById('userType').focus();
        return false;
    }
    if (document.getElementById('userType') && document.getElementById('userType').value > 1)
    {
        if (!document.getElementById('cmbDistrict') || document.getElementById('cmbDistrict').value == 0)
        {
            if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                Swal.fire({ title: "Validation Error", text: "Please select Revenue District", icon: "warning", confirmButtonColor: "#0d6efd" });
            } else if (typeof swal === 'function') swal("Oops!", "Select Revenue District", "error");
            else alert("Select Revenue District");
            if (document.getElementById('cmbDistrict')) document.getElementById('cmbDistrict').focus();
            return false;
        }
    }
    if (document.getElementById('userType') && document.getElementById('userType').value > 2)
    {
        if (!document.getElementById('cmbSubDistrict') || document.getElementById('cmbSubDistrict').value == 0)
        {
            if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                Swal.fire({ title: "Validation Error", text: "Please select Sub-District", icon: "warning", confirmButtonColor: "#0d6efd" });
            } else if (typeof swal === 'function') swal("Oops!", "Select Sub-District", "error");
            else alert("Select Sub-District");
            if (document.getElementById('cmbSubDistrict')) document.getElementById('cmbSubDistrict').focus();
            return false;
        }
    }
    if (document.getElementById('userType') && document.getElementById('userType').value > 3)
    {
        if (!document.getElementById('cmbSchool') || document.getElementById('cmbSchool').value == 0)
        {
            if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
                Swal.fire({ title: "Validation Error", text: "Please select School", icon: "warning", confirmButtonColor: "#0d6efd" });
            } else if (typeof swal === 'function') swal("Oops!", "Select School", "error");
            else alert("Select School");
            if (document.getElementById('cmbSchool')) document.getElementById('cmbSchool').focus();
            return false;
        }
    }

    return true;
}

/**
 * Delete User Account with SweetAlert Confirmation
 */
function deleteUser(userId, UserType, UserName, btnEl) {
    var title = 'Delete ' + (UserType || 'User') + '?';
    var text = 'Are you sure you want to delete "' + UserName + '"? This administrative action cannot be undone.';

    if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash-fill me-1"></i> Yes, Delete',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            preConfirm: function() {
                if (btnEl) {
                    btnEl.disabled = true;
                    btnEl.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                }
                executeDeleteUser(userId, btnEl);
            },
            allowOutsideClick: function() { return !Swal.isLoading(); }
        });
    } else if (typeof swal === 'function') {
        swal({
            title: title,
            text: text,
            icon: 'warning',
            buttons: ['Cancel', 'Yes, Delete'],
            dangerMode: true
        }).then(function(willDelete) {
            if (willDelete) {
                if (btnEl) {
                    btnEl.disabled = true;
                    btnEl.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                }
                executeDeleteUser(userId, btnEl);
            }
        });
    } else {
        if (confirm(text)) {
            if (btnEl) {
                btnEl.disabled = true;
                btnEl.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            }
            executeDeleteUser(userId, btnEl);
        }
    }
}

function executeDeleteUser(userId, btnEl) {
    if (btnEl) {
        btnEl.disabled = true;
        btnEl.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    }
    if (document.getElementById('UserIdty')) {
        document.getElementById('UserIdty').value = userId;
    }
    var form = document.getElementById('editUser');
    if (form) {
        form.action = path + 'user/admin_users/delete_admin_detials';
        form.submit();
    }
}

function editUser(userId) {
    if (document.getElementById('UserIdty')) {
        document.getElementById('UserIdty').value = userId;
    }
    var form = document.getElementById('editUser');
    if (form) {
        form.action = path + 'user/admin_users/edit_admin_detials';
        form.submit();
    }
}

function fnsUserUpdate(userId) {
    if (document.getElementById('hidUserId')) {
        document.getElementById('hidUserId').value = userId;
    }
    var form = document.getElementById('formUser');
    if (form) {
        form.action = path + 'user/admin_users/update_admin_detials';
        form.submit();
    }
}

function cancel()
{
    var modalEl = document.getElementById('userModal');
    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        var myModal = bootstrap.Modal.getInstance(modalEl);
        if (myModal) myModal.hide();
    }
    window.location.href = path + 'user/admin_users/';
}

function loadDistrict()
{
    var divDist = document.getElementById('divDistrict');
    var divSub = document.getElementById('divSubdistrict');
    var divSch = document.getElementById('divSchool');
    var userTypeEl = document.getElementById('userType');

    if (divDist) divDist.style.display = 'none';
    if (divSub) divSub.style.display = 'none';
    if (divSch) divSch.style.display = 'none';

    if (document.getElementById('cmbDistrict')) document.getElementById('cmbDistrict').selectedIndex = 0;
    if (document.getElementById('cmbSubDistrict')) document.getElementById('cmbSubDistrict').selectedIndex = 0;
    if (document.getElementById('cmbSchool')) document.getElementById('cmbSchool').selectedIndex = 0;

    if (userTypeEl && userTypeEl.value != 1 && userTypeEl.value != 0) {
        if (divDist) divDist.style.display = 'block';
    }
}

/**
 * Load Sub-districts via Fetch API with robust spinner & error handling
 */
function loadSubDistrict(selectedSubDistrictVal, selectedSchoolVal)
{
    var userTypeEl = document.getElementById('userType');
    var userTypeValue = userTypeEl ? parseInt(userTypeEl.value) : 0;

    if (userTypeValue > 2)
    {
        var divSub = document.getElementById('divSubdistrict');
        if (divSub) divSub.style.display = 'block';

        var combo = document.getElementById('divSubdistrictCombo');
        if (combo) {
            combo.innerHTML = '<div class="d-flex align-items-center gap-2 text-muted small py-1.5"><span class="spinner-border spinner-border-sm text-primary" role="status"></span> Loading sub-districts...</div>';
        }

        var distSelect = document.getElementById('cmbDistrict');
        var district_code = distSelect ? distSelect.value : 0;

        var formData = new FormData();
        formData.append('district', district_code);
        if (typeof getCsrfToken === 'function') {
            formData.append('csrf_test_name', getCsrfToken());
        }

        fetch(path + 'ajax/loadajax/get_subdistrict_details', {
            method: 'POST',
            body: formData,
            headers: (typeof getCsrfToken === 'function' && getCsrfToken()) ? { 'X-CSRF-TOKEN': getCsrfToken() } : {}
        })
        .then(function(response) {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.text();
        })
        .then(function(html) {
            if (combo) {
                combo.innerHTML = html;
                var newSub = document.getElementById('cmbSubDistrict');
                if (newSub) {
                    newSub.className = 'form-select form-select-sm rounded-3';
                    if (selectedSubDistrictVal) {
                        newSub.value = selectedSubDistrictVal;
                        if (userTypeValue > 3 && selectedSchoolVal) {
                            loadSchool(selectedSchoolVal);
                        }
                    }
                }
            }
        })
        .catch(function(err) {
            console.error('Error loading subdistricts:', err);
            if (combo) combo.innerHTML = '<span class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i>Failed to load sub-districts</span>';
        });
    }
}

/**
 * Load Schools via Fetch API with robust spinner & error handling
 */
function loadSchool(selectedSchoolVal)
{
    var userTypeEl = document.getElementById('userType');
    var userTypeValue = userTypeEl ? parseInt(userTypeEl.value) : 0;

    if (userTypeValue > 3)
    {
        var divSchool = document.getElementById('divSchool');
        if (divSchool) divSchool.style.display = 'block';

        var combo = document.getElementById('divSchoolCombo');
        if (combo) {
            combo.innerHTML = '<div class="d-flex align-items-center gap-2 text-muted small py-1.5"><span class="spinner-border spinner-border-sm text-primary" role="status"></span> Loading schools...</div>';
        }

        var subSelect = document.getElementById('cmbSubDistrict');
        var subdistrict_code = subSelect ? subSelect.value : 0;

        var formData = new FormData();
        formData.append('subdistrict', subdistrict_code);
        if (typeof getCsrfToken === 'function') {
            formData.append('csrf_test_name', getCsrfToken());
        }

        fetch(path + 'ajax/loadajax/get_school_details', {
            method: 'POST',
            body: formData,
            headers: (typeof getCsrfToken === 'function' && getCsrfToken()) ? { 'X-CSRF-TOKEN': getCsrfToken() } : {}
        })
        .then(function(response) {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.text();
        })
        .then(function(html) {
            if (combo) {
                combo.innerHTML = html;
                var newSchool = document.getElementById('cmbSchool');
                if (newSchool) {
                    newSchool.className = 'form-select form-select-sm rounded-3';
                    if (selectedSchoolVal) {
                        newSchool.value = selectedSchoolVal;
                    }
                }
            }
        })
        .catch(function(err) {
            console.error('Error loading schools:', err);
            if (combo) combo.innerHTML = '<span class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i>Failed to load schools</span>';
        });
    }
}

function loadDistrictFilter()
{
    var divDist = document.getElementById('divDistrictFilter');
    var divSub = document.getElementById('divSubDistrictFilter');
    var divSch = document.getElementById('divSchoolFilter');
    var notice = document.getElementById('divStatewideNotice');
    var controls = document.getElementById('divTerritoryControls');
    var userTypeEl = document.getElementById('userTypeFilter');
    var userTypeValue = userTypeEl ? parseInt(userTypeEl.value) : 0;

    if (userTypeValue === 1) {
        // State Admin has statewide jurisdiction, show notice and hide district, subdistrict, school
        if (notice) notice.style.display = 'flex';
        if (controls) controls.style.display = 'none';
        if (divDist) divDist.style.display = 'none';
        if (divSub) divSub.style.display = 'none';
        if (divSch) divSch.style.display = 'none';
        if (document.getElementById('cmbDistrictFilter')) document.getElementById('cmbDistrictFilter').value = '0';
        if (document.getElementById('cmbSubDistrictFilter')) document.getElementById('cmbSubDistrictFilter').value = '0';
        if (document.getElementById('cmbSchoolFilter')) document.getElementById('cmbSchoolFilter').value = '0';
    } else {
        // Show district filter & territory controls
        if (notice) notice.style.display = 'none';
        if (controls) controls.style.display = 'flex';
        if (divDist) divDist.style.display = 'block';
        var distSelect = document.getElementById('cmbDistrictFilter');
        if (distSelect && distSelect.value != '0' && distSelect.value != '') {
            if (divSub) divSub.style.display = 'block';
            loadSubDistrictFilter();
        } else {
            if (divSub) divSub.style.display = 'none';
            if (divSch) divSch.style.display = 'none';
        }
    }
}

function loadSubDistrictFilter(selectedVal)
{
    var divSub = document.getElementById('divSubDistrictFilter');
    var combo = document.getElementById('divSubDistrictFilterCombo') || divSub;
    var distSelect = document.getElementById('cmbDistrictFilter');
    var district_code = distSelect ? distSelect.value : 0;

    if (!district_code || district_code == '0') {
        if (divSub) divSub.style.display = 'none';
        var divSch = document.getElementById('divSchoolFilter');
        if (divSch) divSch.style.display = 'none';
        return;
    }

    if (divSub) divSub.style.display = 'block';
    if (combo) {
        combo.innerHTML = '<div class="d-flex align-items-center gap-2 text-muted small py-1.5"><span class="spinner-border spinner-border-sm text-primary" role="status"></span> Loading...</div>';
    }

    var formData = new FormData();
    formData.append('district', district_code);
    formData.append('name', 'cmbSubDistrictFilter');
    formData.append('function', 'loadSchoolFilter');
    if (typeof getCsrfToken === 'function') {
        formData.append('csrf_test_name', getCsrfToken());
    }

    fetch(path + 'ajax/loadajax/get_subdistrict_details_small', {
        method: 'POST',
        body: formData,
        headers: (typeof getCsrfToken === 'function' && getCsrfToken()) ? { 'X-CSRF-TOKEN': getCsrfToken() } : {}
    })
    .then(function(response) { return response.text(); })
    .then(function(html) {
        if (combo) {
            combo.innerHTML = html;
            var newSub = document.getElementById('cmbSubDistrictFilter');
            if (newSub) {
                newSub.className = 'form-select form-select-sm rounded-3';
                if (selectedVal) newSub.value = selectedVal;
            }
        }
    })
    .catch(function(err) {
        console.error('Error in loadSubDistrictFilter:', err);
        if (combo) combo.innerHTML = '<span class="text-danger small">Error</span>';
    });
}

function loadSchoolFilter(selectedVal)
{
    var divSch = document.getElementById('divSchoolFilter');
    var combo = document.getElementById('divSchoolFilterCombo') || divSch;
    var subSelect = document.getElementById('cmbSubDistrictFilter');
    var subdistrict_code = subSelect ? subSelect.value : 0;

    if (!subdistrict_code || subdistrict_code == '0') {
        if (divSch) divSch.style.display = 'none';
        return;
    }

    if (divSch) divSch.style.display = 'block';
    if (combo) {
        combo.innerHTML = '<div class="d-flex align-items-center gap-2 text-muted small py-1.5"><span class="spinner-border spinner-border-sm text-primary" role="status"></span> Loading...</div>';
    }

    var formData = new FormData();
    formData.append('subdistrict', subdistrict_code);
    formData.append('name', 'cmbSchoolFilter');
    if (typeof getCsrfToken === 'function') {
        formData.append('csrf_test_name', getCsrfToken());
    }

    fetch(path + 'ajax/loadajax/get_school_details_small', {
        method: 'POST',
        body: formData,
        headers: (typeof getCsrfToken === 'function' && getCsrfToken()) ? { 'X-CSRF-TOKEN': getCsrfToken() } : {}
    })
    .then(function(response) { return response.text(); })
    .then(function(html) {
        if (combo) {
            combo.innerHTML = html;
            var newSch = document.getElementById('cmbSchoolFilter');
            if (newSch) {
                newSch.className = 'form-select form-select-sm rounded-3';
                if (selectedVal) newSch.value = selectedVal;
            }
        }
    })
    .catch(function(err) {
        console.error('Error in loadSchoolFilter:', err);
        if (combo) combo.innerHTML = '<span class="text-danger small">Error</span>';
    });
}

function generateSubDistAdmin()
{
    var form = document.getElementById('formUser');
    if (form) {
        form.action = path + 'user/admin_users/generate_sub_dist_admins';
        form.submit();
    }
}

function generate_sub_dist_admin_pdf()
{
    var pdfInput = document.getElementById('generate_pdf');
    if (pdfInput) pdfInput.value = 1;
    var form = document.getElementById('filter');
    if (form) {
        form.action = path + 'user/admin_users/sub_district_admin_list_pdf_creation';
        form.target = '_blank';
        form.submit();
        form.target = '_self';
        form.action = path + 'user/admin_users/';
    }
}

/**
 * Toggle Password Visibility
 */
function togglePasswordVisibility(inputId, btnEl) {
    var input = document.getElementById(inputId);
    if (!input) return;
    if (input.type === 'password') {
        input.type = 'text';
        if (btnEl) btnEl.innerHTML = '<i class="bi bi-eye-slash-fill text-primary"></i>';
    } else {
        input.type = 'password';
        if (btnEl) btnEl.innerHTML = '<i class="bi bi-eye-fill"></i>';
    }
}

/**
 * Quick Copy Credentials Helper
 */
function copyCredentials(username, pwd) {
    var text = "Username: " + username + "\nPassword: " + pwd;
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            showToast("Credentials copied to clipboard!");
        });
    } else {
        var textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showToast("Credentials copied to clipboard!");
        } catch (err) {
            alert("Username: " + username + " | Password: " + pwd);
        }
        document.body.removeChild(textArea);
    }
}

function showToast(msg) {
    if (typeof Toastify === 'function') {
        Toastify({
            text: msg,
            duration: 3000,
            gravity: "top",
            position: "right",
            style: { background: "linear-gradient(to right, #0d6efd, #0dcaf0)" }
        }).showToast();
    } else {
        alert(msg);
    }
}

// Global window attachments to ensure availability across execution contexts
window.openAddUserModal = openAddUserModal;
window.openEditUserModal = openEditUserModal;
window.submitAdminUserForm = submitAdminUserForm;
window.fnsUserAdd = fnsUserAdd;
window.deleteUser = deleteUser;
window.executeDeleteUser = executeDeleteUser;
window.editUser = editUser;
window.fnsUserUpdate = fnsUserUpdate;
window.cancel = cancel;
window.loadDistrict = loadDistrict;
window.loadSubDistrict = loadSubDistrict;
window.loadSchool = loadSchool;
window.loadDistrictFilter = loadDistrictFilter;
window.loadSubDistrictFilter = loadSubDistrictFilter;
window.loadSchoolFilter = loadSchoolFilter;
window.generateSubDistAdmin = generateSubDistAdmin;
window.generate_sub_dist_admin_pdf = generate_sub_dist_admin_pdf;
window.togglePasswordVisibility = togglePasswordVisibility;
window.copyCredentials = copyCredentials;
window.showToast = showToast;
