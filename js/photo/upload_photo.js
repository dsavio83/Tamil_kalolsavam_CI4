// JavaScript Document
function fetch_studdetails()
{
    //swal("Oops!", "kiiiiii", "error");
    //var festid	=	$('txtSchoolCode').value;
    if (document.getElementById('txtSchoolCode').value == '')
    {
        swal("Oops!", "Please enter the School Code", "error");
        document.getElementById('txtSchoolCode').focus();
        return false;
    }
    if (document.getElementById('txtRegNum').value == '')
    {
        swal("Oops!", "Please enter the Register Number", "error");
        document.getElementById('txtRegNum').focus();
        return false;
    }
    else
    {
        //var regno		=	document.getElementById('txtRegNum').value;
        var baseUrl = document.getElementById('baseurl').value;
        document.getElementById('formreg_photo').action = baseUrl + "pictures/pictures/regnum_wise_photo_interface";
        document.getElementById('formreg_photo').submit();
    }

}

function check_upload()
{
    //swal("Oops!", "hiii", "error");
    if (document.formreg_photo.userfile.value == '')
    {
        swal("Oops!", "Please select the file", "error");
        document.formreg_photo.userfile.focus();
        return false;
    }

}

function check_upload_schoolwise()
{
    var sum = document.formschool_photo.hidtot.value;
    //swal("Oops!", "summmmm"+sum);
    var flag = 0;
    for (i = 1; i <= sum; i++)
    {
        //swal("Oops!", "val->"+i+document.getElementById('userfile'+i).value, "error");
        if (document.getElementById('userfile' + i).value != '')
        {
            flag = 1;
            break;
        }
    }

    if (flag == 0)
    {
        swal("Oops!", "Please select any file", "error");
        return false;
    }

}


function fetch_schoolwise_studdetails()
{
    //swal("Oops!", "hiiiiii", "error");
    //var festid	=	$('txtSchoolCode').value;
    if (document.getElementById('txtSchoolCode').value == '')
    {
        swal("Oops!", "Please enter the School Code", "error");
        document.getElementById('txtSchoolCode').focus();
        return false;
    }

    else
    {
        var baseUrl = document.getElementById('baseurl').value;
        document.getElementById('formschool_photo').action = baseUrl + "pictures/pictures/school_wise_photo_interface";
        document.getElementById('formschool_photo').submit();
    }

}

