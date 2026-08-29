// JavaScript Document
function fnsUserAdd()
{
    if (!document.getElementById('txtNewUserName').value)
    {
        swal("Oops!", "Please Enter User name", "error");
        document.getElementById('txtNewUserName').focus();
        return false;
    }
    if (!document.getElementById('txtNewPassword').value)
    {
        swal("Oops!", "Please Enter Password", "error");
        document.getElementById('txtNewPassword').focus();
        return false;
    }
    /*if(document.getElementById('userType').value==0)
     {
     swal("Oops!", "select User type", "error");
     document.getElementById('userType').focus();
     return false;
     }*/
    return true;

}

function deleteUser(userId) {
    if (!confirm('Really want to delete the user?')) {
        return false;
    }
    $('UserIdty').value = userId;
    $('editUser').action = path + 'user/user_registration/delete_user_detials';
    $('editUser').submit();
}

function editUser(userId) {
    $('UserIdty').value = userId;
    $('editUser').action = path + 'user/user_registration/edit_user_detials';
    $('editUser').submit();
}

function fnsUserUpdate(userId) {
    $('hidUserId').value = userId;
    $('formUser').action = path + 'user/user_registration/update_user_detials';
    $('formUser').submit();
}

function cancel()
{
    $('formUser').action = path + 'user/user_registration/';
    $('formUser').submit();
}