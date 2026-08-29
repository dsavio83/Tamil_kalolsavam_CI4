<?php
echo form_open('login/change_Pwd_insert', array('id' => 'formPWD'));

$name = "";
$emailid = "";
$mobilenumber = "";
if (count($checkval) > 0) {
    $name = $checkval[0]['name'];
    $emailid = $checkval[0]['email'];
    $mobilenumber = $checkval[0]['mobile'];
}
?>

<!-- Modern Page Header -->
<div class="pagetitle">
    <h1><i class="bi bi-gear me-2"></i>Account Settings</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome">Home</a></li>
            <li class="breadcrumb-item active">Change Password</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">

            <!-- Main Card -->
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-key me-2"></i>Change Password & Profile
                    </h4>
                </div>

                <div class="card-body p-4">

                    <!-- Password Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="section-title mb-3 pb-2 border-bottom">
                                <i class="bi bi-shield-lock me-2"></i>Password Information
                            </h6>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="txtOLDPassword" class="form-label fw-semibold">
                                <i class="bi bi-lock me-1"></i>Current Password <span class="text-danger">*</span>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <?php echo form_password("txtOLDPassword", '', 'id="txtOLDPassword" class="form-control form-control-lg" placeholder="Enter current password"'); ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="txtNewPassword" class="form-label fw-semibold">
                                <i class="bi bi-key me-1"></i>New Password <span class="text-danger">*</span>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <?php echo form_password("txtNewPassword", '', 'id="txtNewPassword" class="form-control form-control-lg" placeholder="Enter new password"'); ?>
                            <div class="form-text">Password must be at least 8 characters long</div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="txtCFMPassword" class="form-label fw-semibold">
                                <i class="bi bi-check-circle me-1"></i>Confirm Password <span class="text-danger">*</span>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <?php echo form_password("txtCFMPassword", '', 'id="txtCFMPassword" class="form-control form-control-lg" placeholder="Confirm new password"'); ?>
                        </div>
                    </div>

                    <!-- Profile Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="section-title mb-3 pb-2 border-bottom">
                                <i class="bi bi-person me-2"></i>Profile Information
                            </h6>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="Name" class="form-label fw-semibold">
                                <i class="bi bi-person-badge me-1"></i>Full Name <span class="text-danger">*</span>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <?php echo form_input("Name", $this->input->post('Name') ?
                            $this->input->post('Name') : $name, 'id="Name" class="form-control form-control-lg" placeholder="Enter your full name" onkeyup="javascript:this.value=this.value.toUpperCase();"'); ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="Mobile_Number" class="form-label fw-semibold">
                                <i class="bi bi-phone me-1"></i>Mobile Number <span class="text-danger">*</span>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <?php echo form_input("Mobile_Number", $this->input->post('Mobile_Number') ? $this->input->post('Mobile_Number') : $mobilenumber, 'id="Mobile_Number" class="form-control form-control-lg" placeholder="Enter mobile number" onkeypress="javascript:return numbersonly(this, event, false);"'); ?>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="Email_id" class="form-label fw-semibold">
                                <i class="bi bi-envelope me-1"></i>Email Address <span class="text-danger">*</span>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <?php echo form_input("Email_id", $this->input->post('Email_id') ? $this->input->post('Email_id') : ((@count($checkval) > 0) ? @$checkval[0]['email'] : ''), 'id="Email_id" class="form-control form-control-lg" placeholder="Enter email address"'); ?>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-12 text-center">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <?php echo form_submit('Change Password', 'Update Password & Profile', 'onClick="javascript: return fnschgpwdAdd()" class="btn btn-primary btn-lg px-5 me-md-2"'); ?>
                                <?php if ($checkval[0]['is_change_password'] == 'Y') { ?>
                                    <?php echo form_button('Cancel', 'Cancel', 'onClick="javascript:gotohome()" class="btn btn-outline-secondary btn-lg px-5"'); ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
          
<?php
echo form_close();
?>
