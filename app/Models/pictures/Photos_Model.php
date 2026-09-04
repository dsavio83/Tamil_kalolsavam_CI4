<?php

namespace App\Models\Pictures;

class Photos_Model extends \App\Models\BaseModel {
 
    function __construct () {
        parent::__construct ();
    }
function get_participant_details($reg_no,$sch_code)
{
$dist	=	$this->session->get('DISTRICT');
//echo "<br />dist------>".$dist;
$result=$this->db->query("select PD.*,SM.school_name from sd_participant_details as PD,sd_school_master as SM where PD.admn_no=? and PD.school_code = ? and PD.school_code=SM.school_code and SM.rev_district_code=?", [$reg_no, $sch_code, $dist]);
//echo $result;
if(is_object($result))
{
if($result->getNumRows() > 0)
{
return $result->getResultArray();
}
}
}

function get_item_details($reg_no,$sch_code)
{
$dist	=	$this->session->get('DISTRICT');
$result=$this->db->query("select IM.item_name from sd_participant_item_details as PID,sd_school_master as SM,item_master as IM where PID.admn_no=? and PID.school_code = ? and PID.school_code=SM.school_code and SM.rev_district_code=? and IM.item_code=PID.item_code", [$reg_no, $sch_code, $dist]);
//echo $result;
if(is_object($result))
{
if($result->getNumRows() > 0)
{
return $result->getResultArray();
}
}
}

/********   Retrieving the Photo  *******/

function get_Photo($name, $schoolcode, $sub_district_code = null)
{
    if ($sub_district_code === null || $sub_district_code === '') {
        $sub_district_code = $this->session->get('SUB_DISTRICT');
    }
    if (empty($sub_district_code) && !empty($schoolcode)) {
        $sch = $this->db->query("SELECT sub_district_code FROM sd_school_master WHERE school_code = ? LIMIT 1", [$schoolcode]);
        if (is_object($sch) && $sch->getNumRows() > 0) {
            $row = $sch->getResultArray();
            $sub_district_code = $row[0]['sub_district_code'];
        }
    }
    $dirname = "photos/" . $sub_district_code . "/" . $schoolcode . "/";
    $exts = array('jpg', 'jpeg', 'gif', 'png');

    foreach($exts as $ext) {
        $filepath = $dirname . $name . '.' . $ext;
        if (file_exists(ROOTPATH . $filepath)) {
            return base_url() . $filepath;
        }
    }

    // Return null if no photo found
    return null;
}

/********   Get Photo with Thumbnail  *******/

function get_Photo_With_Thumbnail($name, $schoolcode)
{
    $dirname = "photos/" . $this->session->get('SUB_DISTRICT') . "/" . $schoolcode . "/";
    $exts = array('jpg', 'jpeg', 'gif', 'png');

    $photo_data = array('full' => null, 'thumbnail' => null);

    foreach($exts as $ext) {
        $filepath = $dirname . $name . '.' . $ext;
        $thumbpath = $dirname . $name . '_thumb.' . $ext;

        if (file_exists(ROOTPATH . $filepath)) {
            $photo_data['full'] = base_url() . $filepath;
        }

        if (file_exists(ROOTPATH . $thumbpath)) {
            $photo_data['thumbnail'] = base_url() . $thumbpath;
        }

        // If we found the full image, break out of the loop
        if ($photo_data['full'] !== null) {
            break;
        }
    }

    return $photo_data;
}

/********   Check if Photo Exists  *******/

function photo_exists($name, $schoolcode)
{
    $dirname = "photos/" . $this->session->get('SUB_DISTRICT') . "/" . $schoolcode . "/";
    $exts = array('jpg', 'jpeg', 'gif', 'png');

    foreach($exts as $ext) {
        if (file_exists(ROOTPATH . $dirname . $name . '.' . $ext)) {
            return true;
        }
    }

    return false;
}

/********   Delete Photo and Thumbnail  *******/

function delete_photo_files($name, $schoolcode)
{
    $dirname = "photos/" . $this->session->get('SUB_DISTRICT') . "/" . $schoolcode . "/";
    $exts = array('jpg', 'jpeg', 'gif', 'png');
    $deleted = false;

    foreach($exts as $ext) {
        $filepath = ROOTPATH . $dirname . $name . '.' . $ext;
        $thumbpath = ROOTPATH . $dirname . $name . '_thumb.' . $ext;

        if (file_exists($filepath)) {
            unlink($filepath);
            $deleted = true;
        }

        if (file_exists($thumbpath)) {
            unlink($thumbpath);
            $deleted = true;
        }
    }

    return $deleted;
}

function get_count_schoolwise_participant_details($sch_code)
{
$dist	=	$this->session->get('DISTRICT');
//echo "<br />dist------>".$dist;
$result=$this->db->query("select PD.* from sd_participant_details as PD,sd_school_master as SM where PD.school_code = ? and PD.school_code=SM.school_code and SM.rev_district_code=? order by PD.admn_no", [$sch_code, $dist]);
//echo $result;
if(is_object($result))
{
if($result->getNumRows() > 0)
{
return $result->getResultArray();
}
}
}

function get_schoolwise_participant_details($num=1,$offset=4,$sch_code)
{
$dist	=	$this->session->get('DISTRICT');

$result=$this->db->query("select PD.* from sd_participant_details as PD,sd_school_master as SM where PD.school_code = ? and PD.school_code=SM.school_code and SM.rev_district_code=? order by PD.class,PD.participant_name LIMIT ?,?", [$sch_code, $dist, (int)$offset, (int)$num]);
//echo $result;
if(is_object($result))
{
if($result->getNumRows() > 0)
{
return $result->getResultArray();
}
}
}

function get_schoolwise_Photo($arr,$schoolcode)
{
$PhotoReturn	=	false;
//echo "<br /><br />".var_dump($arr)."<br /><br /><br />";
foreach($arr['participant_det'] as $stud_row)
{
$regno							=	$stud_row['admn_no'];
$img								=	$schoolcode."_".$regno;
$PhotoReturn['pic'][$regno]	    =	$this->get_Photo($img, $schoolcode);
//echo "<br /><br />---->".$stud_row['admn_no']."<br /><br />";
}
// var_dump($PhotoReturn);
return $PhotoReturn;
}

function get_schoolwise_Photo_With_Thumbnail($arr,$schoolcode)
{
$PhotoReturn	=	false;
//echo "<br /><br />".var_dump($arr)."<br /><br /><br />";
foreach($arr['participant_det'] as $stud_row)
{
$regno							=	$stud_row['admn_no'];
$img								=	$schoolcode."_".$regno;
$PhotoReturn['pic'][$regno]	    =	$this->get_Photo_With_Thumbnail($img, $schoolcode);
//echo "<br /><br />---->".$stud_row['admn_no']."<br /><br />";
}
// var_dump($PhotoReturn);
return $PhotoReturn;
}

function get_subdistwise_Photo($arr)
{
//echo "<br /><br />".var_dump($arr)."<br /><br /><br />";
$PhotoReturn	=	false;
foreach($arr['participant_det'] as $stud_row)
{
$regno							=	$stud_row['admn_no'];
$schoolcode						=	$stud_row['school_code'];
$img								=	$schoolcode."_".$regno;
$PhotoReturn['pic'][$regno]	    =	$this->get_Photo($img, $schoolcode);
//echo "<br /><br />---->".$stud_row['admn_no']."<br /><br />";
}
// var_dump($PhotoReturn);
return $PhotoReturn;

}

function get_participant_regno_Photo($arr)
{
$PhotoReturn	=	false;
foreach($arr['partcard'] as $stud_row)
{
$regno							=	$stud_row['admn_no'];
$schoolcode						=	$stud_row['school_code'];
$sub_district_code				=	$stud_row['sub_district_code'];
$img								=	$schoolcode."_".$regno;
$PhotoReturn['pic'][$regno]	    =	$this->get_Photo($img, $schoolcode, $sub_district_code);
}

foreach($arr['partcard1'] as $stud_row)
{
$regno							=	$stud_row['admn_no'];
$schoolcode						=	$stud_row['school_code'];
$sub_district_code				=	$stud_row['sub_district_code'];
$img								=	$schoolcode."_".$regno;
$PhotoReturn['pic'][$regno]	    =	$this->get_Photo($img, $schoolcode, $sub_district_code);
}

foreach($arr['partcard2'] as $stud_row)
{
$regno							=	$stud_row['admn_no'];
$schoolcode						=	$stud_row['school_code'];
$sub_district_code				=	$stud_row['sub_district_code'];
$img								=	$schoolcode."_".$regno;
$PhotoReturn['pic'][$regno]	    =	$this->get_Photo($img, $schoolcode, $sub_district_code);
}
return $PhotoReturn;
}

function get_rank_Photo($arr)
{
$PhotoReturn	=	false;
//echo "<br /><br />".var_dump($arr);
foreach($arr['details'] as $stud_row)
{
$regno							=	$stud_row['admn_no'];
$schoolcode						=	$stud_row['school_code'];
$img								=	$schoolcode."_".$regno;
$PhotoReturn['pic'][$regno]	    =	$this->get_Photo($img, $schoolcode);
}
return $PhotoReturn;
}

function get_rank_Photo_public($arr)
{
$PhotoReturn	=	false;
//echo "<br /><br />".var_dump($arr);
foreach($arr['details'] as $stud_row)
{
$regno							=	$stud_row['admn_no'];
$schoolcode						=	$stud_row['school_code'];
$sub_district_code				=	$stud_row['sub_district_code'];
$img								=	$schoolcode."_". $regno;
// Use public photo path without session dependency
$dirname = "photos/" . $sub_district_code . "/" . $schoolcode . "/";
$exts = array('jpg', 'jpeg', 'gif', 'png');

foreach($exts as $ext) {
    $filepath = $dirname . $img . '.' . $ext;
    if (file_exists(ROOTPATH . $filepath)) {
        $PhotoReturn['pic'][$regno] = base_url() . $filepath;
        break;
    } else {
        $PhotoReturn['pic'][$regno] = base_url() . "photos/img_user_nophoto.png";
    }
}
}
return $PhotoReturn;
}

function get_special_entry_photo($arr)
{
$PhotoReturn	=	false;
foreach($arr['sd_participant_details'] as $stud_row)
{
$regno							=	$stud_row['admn_no'];
$schoolcode						=	$stud_row['school_code'];
$img								=	$schoolcode."_".$regno;
$PhotoReturn['pic'][$regno]	    =	$this->get_Photo($img, $schoolcode);
}
return $PhotoReturn;

}



}
?>
