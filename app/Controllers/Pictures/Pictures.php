<?php

namespace App\Controllers\Pictures;

 
class Pictures extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->Session_Model->is_user_logged(true);
        $this->template->add_js('js/photo/upload_photo.js');
        $this->template->add_js('js/report/staticreport.js');
        $this->template->add_js('js/report/reportjs.js');
        $this->load->library('upload');
        $this->load->library('image_lib');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->load->model('pictures/Photos_Model');
        $this->load->model('general_model');
    }

    /*     * *** Admission No wise Photo Uploading ***** */

    function regnum_wise_photo_interface($school_code = NULL, $admn_no = NULL) {
        $this->template->write('title', '');
        if ($school_code == '' && $admn_no == '') {
            $schoolcode = $this->input->post('txtSchoolCode');
            $reg_no = $this->input->post('txtRegNum');
        } else {
            $schoolcode = $school_code;
            $reg_no = $admn_no;
        }

        if ($reg_no == '' && $schoolcode == '') {
            $this->template->write_view('content', 'photos/reg_num_wise_photo');
        } else if ($reg_no != '' && $schoolcode != '') {
            $img = $schoolcode . "_" . $reg_no;
            $dataReturn['participant_det'] = $this->Photos_Model->get_participant_details($reg_no, $schoolcode);
            $dataReturn['Photo'] = $this->Photos_Model->get_Photo_With_Thumbnail($img, $schoolcode);
            $dataReturn['item_det'] = $this->Photos_Model->get_item_details($reg_no, $schoolcode);
            //echo "<br /><br /><br />--><br /><br />".var_dump(@$dataReturn['participant_det']);
            if ($dataReturn['participant_det']) {
                $this->template->write_view('content', 'photos/reg_num_wise_photo', $dataReturn);
            } else {
                $errors_msg = "Not a valid school code and admission number";
                $this->template->write('error', $errors_msg);
                $this->template->write_view('content', 'photos/reg_num_wise_photo');
            }
        }

        $this->template->load();
    }

    //********** ends here ************ 
    //************** function to upload the photo **************

    function upload() {
        if ($this->input->post('upload')) {
            $school_code = $this->input->post('hidschcode');
            $admn_no = $this->input->post('hidadmn');

            // Validate input data
            if (empty($school_code) || empty($admn_no)) {
                $this->template->write('error', 'Invalid school code or admission number.');
                $this->regnum_wise_photo_interface($school_code, $admn_no);
                return;
            }

            // Validate file upload
            if (empty($_FILES['userfile']['name'])) {
                $this->template->write('error', 'Please select a photo file to upload.');
                $this->regnum_wise_photo_interface($school_code, $admn_no);
                return;
            }

            $subDistrict = $this->General_Model->get_data('sd_school_master', 'sub_district_code', array('school_code' => $school_code));
            if (empty($subDistrict)) {
                $this->template->write('error', 'Invalid school code.');
                $this->regnum_wise_photo_interface($school_code, $admn_no);
                return;
            }
            $subDistrict = $subDistrict[0]['sub_district_code'];

            $this->load->library('upload');
            $this->load->library('image_lib');

            // Enhanced file validation
            $file_extension = strtolower(pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
            if (!in_array($file_extension, $allowed_extensions)) {
                $this->template->write('error', 'Invalid file type. Only JPG, PNG, and GIF files are allowed.');
                $this->regnum_wise_photo_interface($school_code, $admn_no);
                return;
            }

            // Check file size (1MB = 1048576 bytes)
            if ($_FILES['userfile']['size'] > 1048576) {
                $this->template->write('error', 'File size too large. Maximum size allowed is 1MB.');
                $this->regnum_wise_photo_interface($school_code, $admn_no);
                return;
            }

            $_FILES['userfile']['name'] = $school_code . "_" . $admn_no . '.' . $file_extension;
            $config['upload_path'] = 'photos/' . $subDistrict . '/' . $school_code;
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = '1024'; // 1MB in KB
            $config['max_width'] = '2000';
            $config['max_height'] = '2000';
            $config['overwrite'] = TRUE;
            $config['file_name'] = $school_code . "_" . $admn_no . '.' . $file_extension;

            if (!is_dir($config['upload_path'])) {
                if (!mkdir($config['upload_path'], 0755, true)) {
                    $this->template->write('error', 'Failed to create upload directory.');
                    $this->regnum_wise_photo_interface($school_code, $admn_no);
                    return;
                }
            }

            // Delete existing photo if exists
            $this->deletePhoto($school_code . "_" . $admn_no, $config['upload_path']);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload()) {
                $error = array('error' => $this->upload->display_errors());
                $this->template->write('error', $this->upload->display_errors());
                $this->regnum_wise_photo_interface($school_code, $admn_no);
            } else {
                $finfo = $this->upload->data();

                // Create thumbnail
                $config1['image_library'] = 'gd2';
                $config1['source_image'] = $finfo['full_path'];
                $config1['create_thumb'] = TRUE;
                $config1['thumb_marker'] = '_thumb';
                $config1['maintain_ratio'] = TRUE;
                $config1['width'] = 150;
                $config1['height'] = 150;
                $this->image_lib->initialize($config1);

                if ($this->image_lib->resize()) {
                    $data = array('upload_data' => $this->upload->data());
                    $this->template->write('success', 'Photo uploaded successfully.');
                    $this->regnum_wise_photo_interface($school_code, $admn_no);
                } else {
                    $this->template->write('error', 'Failed to create thumbnail: ' . $this->image_lib->display_errors());
                    $this->regnum_wise_photo_interface($school_code, $admn_no);
                }
                $this->image_lib->clear();
            }
        }
    }

    function bulk_upload() {
        if ($this->input->post('upload')) {
            $total = $this->input->post('hidtot');

            // Validate total count
            if (empty($total) || !is_numeric($total) || $total <= 0) {
                $this->template->write('error', 'Invalid upload data.');
                $this->school_wise_photo_interface();
                return;
            }

            $flag = 0;
            $success_count = 0;
            $error_count = 0;
            $errors = array();
            $this->load->library('upload');
            $this->load->library('image_lib');

            for ($i = 1; $i <= $total; $i++) {
                $school_code = $this->input->post('hidschcode' . $i);
                $admn_no = $this->input->post('hidadmn' . $i);

                // Validate required data
                if (empty($school_code) || empty($admn_no)) {
                    continue;
                }

                $subDistrict = $this->General_Model->get_data('sd_school_master', 'sub_district_code', array('school_code' => $school_code));
                if (empty($subDistrict)) {
                    $errors[] = "Invalid school code: $school_code for admission number: $admn_no";
                    $error_count++;
                    continue;
                }
                $subDistrict = $subDistrict[0]['sub_district_code'];

                $userfile = 'userfile' . $i;
                $file_selected = $_FILES[$userfile]['name'];

                if ($file_selected != '') {
                    $flag = 1;

                    // Enhanced file validation
                    $file_extension = strtolower(pathinfo($_FILES[$userfile]['name'], PATHINFO_EXTENSION));
                    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
                    if (!in_array($file_extension, $allowed_extensions)) {
                        $errors[] = "Invalid file type for admission number $admn_no. Only JPG, PNG, and GIF files are allowed.";
                        $error_count++;
                        continue;
                    }

                    // Check file size (1MB = 1048576 bytes)
                    if ($_FILES[$userfile]['size'] > 1048576) {
                        $errors[] = "File size too large for admission number $admn_no. Maximum size allowed is 1MB.";
                        $error_count++;
                        continue;
                    }

                    $img = $school_code . "_" . $admn_no;
                    $config['file_name'] = $school_code . "_" . $admn_no . '.' . $file_extension;
                    $config['upload_path'] = "photos/$subDistrict/$school_code";
                    $config['allowed_types'] = 'gif|jpg|jpeg|png';
                    $config['max_size'] = '1024'; // 1MB in KB
                    $config['max_width'] = '2000';
                    $config['max_height'] = '2000';
                    $config['overwrite'] = TRUE;

                    if (!is_dir($config['upload_path'])) {
                        if (!mkdir($config['upload_path'], 0755, true)) {
                            $errors[] = "Failed to create upload directory for school: $school_code";
                            $error_count++;
                            continue;
                        }
                    }

                    $this->deletePhoto($img, $config['upload_path']);
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload($userfile)) {
                        $error_msg = $this->upload->display_errors();
                        $errors[] = "Upload failed for admission number $admn_no: $error_msg";
                        $error_count++;
                    } else {
                        $finfo = $this->upload->data();

                        // Create thumbnail
                        $config1['image_library'] = 'gd2';
                        $config1['source_image'] = $finfo['full_path'];
                        $config1['create_thumb'] = TRUE;
                        $config1['thumb_marker'] = '_thumb';
                        $config1['maintain_ratio'] = TRUE;
                        $config1['width'] = 150;
                        $config1['height'] = 150;
                        $this->image_lib->initialize($config1);

                        if ($this->image_lib->resize()) {
                            $success_count++;
                        } else {
                            $errors[] = "Failed to create thumbnail for admission number $admn_no";
                            $error_count++;
                        }
                        $this->image_lib->clear();
                    }
                }
            }//for each ends

            // Display results
            if ($flag == 0) {
                $this->template->write('error', 'No files selected for upload.');
            } else {
                if ($success_count > 0) {
                    $this->template->write('success', "Successfully uploaded $success_count photo(s).");
                }
                if ($error_count > 0) {
                    $this->template->write('error', "Failed to upload $error_count photo(s). Errors: " . implode(', ', $errors));
                }
            }

            $this->school_wise_photo_interface($school_code);
        }//if ends
    }

    function school_wise_photo_interface($school_code = NULL) {

        $this->template->write('title', '');
        if ($school_code == '') {
            $schoolcode = $this->input->post('txtSchoolCode');
        } else {
            $schoolcode = $school_code;
        }

        if ($schoolcode == '') {
            $this->template->write_view('content', 'photos/school_wise_photo');
            $this->template->load();
        } else {
            $dataReturn['participant_details'] = $this->Photos_Model->get_count_schoolwise_participant_details($schoolcode);
            if ($dataReturn['participant_details']) {
                $count1 = count($dataReturn['participant_details']);
                $set_session_data['pag_school'] = $schoolcode . "/" . $count1;
                $this->Session_Model->set_session($set_session_data);
                $this->pag1();
            } else {
                $errors_msg = "Not a valid school code";
                $this->template->write('error', $errors_msg);
                $this->template->write_view('content', 'photos/school_wise_photo');
                $this->template->load();
            }
        }
    }

    function pag1($intOffset = 0) {
        $pag_det = $this->session->get('pag_school');
        $pag_det = explode('/', $pag_det);
        $pag_school = $pag_det[0];
        $pag_count = $pag_det[1];
    
        $this->load->library('pagination');
        $config['per_page'] = '10';
        $config['base_url'] = base_url() . 'pictures/pictures/pag1/';
        $config['uri_segment'] = 4;
    
        $tot = $pag_count;
        $num = $pag_count / 10;
        $total_pages1 = ($pag_count / $config['per_page']);
        $total_pages = round($total_pages1);
        $dataReturn['tot_page'] = $total_pages;
        $config['total_rows'] = $tot;
    
        // Customize all pagination links with button class
        $config['first_link'] = '<button type="button" class="btn btn-light">First</button> &nbsp;';
        $config['last_link'] = '<button type="button" class="btn btn-light">Last</button> &nbsp;';
        $config['prev_link'] = '<button type="button" class="btn btn-light">Preview</button> &nbsp;';
        $config['next_link'] = '<button type="button" class="btn btn-light">Next</button>  &nbsp;';
        $config['num_tag_open'] = '<button type="button" class="btn btn-light">';
        $config['num_tag_close'] = '</button> &nbsp;';
        
        // Customize the current or selected pagination number
        $config['cur_tag_open'] = '<button type="button" class="btn btn-primary">';
        $config['cur_tag_close'] = '</button> &nbsp;';
    
        $this->pagination->initialize($config);
        $ext = @end(explode('/', current_url()));
        $dataReturn['cur_num'] = $ext++;
    
        $dataReturn['participant_det'] = $this->Photos_Model->get_schoolwise_participant_details($pag_school, $config['per_page'], $intOffset);
        $dataReturn['Photo'] = $this->Photos_Model->get_schoolwise_Photo_With_Thumbnail($dataReturn, $pag_school);
        $dataReturn['school_det'] = $this->General_Model->get_data('sd_school_master', '*', array('school_code' => $pag_school));
        $dataReturn['pagination'] = $this->pagination->create_links();
        $this->template->write_view('content', 'photos/school_wise_photo', $dataReturn);
        $this->template->load();
    }
    
    
    

    function deletePhoto($photo, $directory) {
        if (substr($directory, -1) !== '/') {
            $directory.="/";
        }
        exec("rm $directory$photo.*");
    }

}

?>
