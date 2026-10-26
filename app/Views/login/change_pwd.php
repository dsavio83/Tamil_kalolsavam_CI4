<style>
    .avatar-icon-wrapper {
        width: 90px;
        height: 90px;
        background-color: #eff6ff;
        border-radius: 50%;
        color: #3b82f6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.8rem;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.1);
        margin-bottom: 15px;
    }
    .badge-role {
        background-color: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 6px 16px;
        border-radius: 30px;
        letter-spacing: 0.3px;
    }
    .card-settings {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        background: #ffffff;
    }
    .settings-section-title {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #4b5563;
        letter-spacing: 1.2px;
        margin-bottom: 20px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f3f4f6;
        display: flex;
        align-items: center;
    }
    .settings-section-title i {
        color: #3b82f6;
        font-size: 1.1rem;
    }
    .form-label-custom {
        font-size: 0.925rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
        display: inline-block;
    }
    .input-group-custom {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.2s ease;
        background-color: #ffffff;
        display: flex;
        flex-wrap: nowrap;
        align-items: stretch;
    }
    .input-group-custom:focus-within {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }
    .input-group-custom .input-group-text {
        border: none;
        background-color: #f9fafb;
        color: #6b7280;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-right: 1px solid #e5e7eb;
    }
    .input-group-custom .form-control {
        border: none !important;
        padding: 10px 14px;
        font-size: 0.95rem;
        color: #1f2937;
        box-shadow: none !important;
        flex: 1 1 auto;
        width: 1%;
        min-width: 0;
        background-color: transparent;
    }
    .input-group-custom .form-control::placeholder {
        font-size: 0.9rem;
        color: #9ca3af;
    }
    .btn-update-profile {
        background: #2563eb;
        color: #ffffff;
        border: none;
        padding: 10px 24px;
        font-weight: 600;
        font-size: 0.925rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }
    .btn-update-profile:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
        color: #ffffff;
    }
    .btn-cancel-profile {
        padding: 10px 24px;
        font-weight: 600;
        font-size: 0.925rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        border: 1px solid #d1d5db;
        color: #4b5563;
        background-color: #ffffff;
    }
    .btn-cancel-profile:hover {
        background-color: #f9fafb;
        color: #1f2937;
        border-color: #9ca3af;
        transform: translateY(-1px);
    }
    .profile-info-item {
        font-size: 0.9rem;
        color: #4b5563;
    }
</style>

<?php
echo form_open('login/change_Pwd_insert', array('id' => 'formPWD'));

// Include the CSRF token manually since the filter is not active globally
echo form_hidden(service('security')->getTokenName(), service('security')->getHash());

$name = "";
$emailid = "";
$mobilenumber = "";
if (count($checkval) > 0) {
    $name = $checkval[0]['name'];
    $emailid = $checkval[0]['email'];
    $mobilenumber = $checkval[0]['mobile'];
}

$request = service('request');
$postName = $request->getPost('Name');
$postMobile = $request->getPost('Mobile_Number');
$postEmail = $request->getPost('Email_id');

$valName = ($postName !== null) ? $postName : ($name !== null ? $name : '');
$valMobile = ($postMobile !== null) ? $postMobile : ($mobilenumber !== null ? $mobilenumber : '');
$valEmail = ($postEmail !== null) ? $postEmail : ($emailid !== null ? $emailid : '');
?>

<!-- Modern Header -->
<div class="pagetitle mb-4">
    <h1><i class="bi bi-gear-fill me-2 text-primary"></i>Account Settings</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome">Home</a></li>
            <li class="breadcrumb-item active">Change Password</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        
        <!-- Left Column: User Profile Card -->
        <div class="col-12 col-xl-4 mb-4">
            <div class="card card-settings text-center p-4 h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-start py-4">
                    <div class="avatar-icon-wrapper">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark"><?php echo esc($name ? $name : 'User Profile'); ?></h5>
                    <div class="mb-4">
                        <span class="badge-role">
                            <?php 
                            $utype = $this->session->get('USER_TYPE');
                            if ($utype == 0 || $utype == 1) echo 'Webmaster';
                            elseif ($utype == 2) echo 'District Admin';
                            elseif ($utype == 3) echo 'Subdistrict Admin';
                            else echo 'School User';
                            ?>
                        </span>
                    </div>
                    
                    <hr class="w-100 my-3 text-muted">
                    
                    <div class="w-100 text-start mt-3">
                        <div class="d-flex align-items-center mb-3 profile-info-item">
                            <span class="text-primary me-3 fs-5"><i class="bi bi-person-badge"></i></span>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Username</small>
                                <span class="fw-semibold text-dark"><?php echo esc($this->session->get('USER_NAME')); ?></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3 profile-info-item">
                            <span class="text-primary me-3 fs-5"><i class="bi bi-telephone"></i></span>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Mobile Number</small>
                                <span class="fw-semibold text-dark font-monospace"><?php echo esc($mobilenumber ? $mobilenumber : 'Not provided'); ?></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center profile-info-item">
                            <span class="text-primary me-3 fs-5"><i class="bi bi-envelope"></i></span>
                            <div class="text-truncate">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Email Address</small>
                                <span class="fw-semibold text-dark" title="<?php echo esc($emailid); ?>"><?php echo esc($emailid ? $emailid : 'Not provided'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Security & Profile Settings Form -->
        <div class="col-12 col-xl-8 mb-4">
            <div class="card card-settings h-100">
                <div class="card-body p-4 p-md-5">
                    
                    <!-- Section: Password Security -->
                    <div class="settings-section-title">
                        <i class="bi bi-shield-lock-fill me-2"></i><span>Password Security</span>
                        <small class="text-muted fw-normal ms-2 text-capitalize" style="font-size: 0.76rem; letter-spacing: normal;">(Optional: Leave blank to keep current password)</small>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <label for="txtOLDPassword" class="form-label form-label-custom">Current Password <small class="text-muted fw-normal">(Only if changing)</small></label>
                            <div class="input-group input-group-custom">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <?php echo form_password("txtOLDPassword", '', 'id="txtOLDPassword" class="form-control" placeholder="Enter current password"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <label for="txtNewPassword" class="form-label form-label-custom">New Password <small class="text-muted fw-normal">(Optional)</small></label>
                            <div class="input-group input-group-custom">
                                <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                <?php echo form_password("txtNewPassword", '', 'id="txtNewPassword" class="form-control" placeholder="Enter new password"'); ?>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label for="txtCFMPassword" class="form-label form-label-custom">Confirm New Password <small class="text-muted fw-normal">(Optional)</small></label>
                            <div class="input-group input-group-custom">
                                <span class="input-group-text"><i class="bi bi-shield-fill-check"></i></span>
                                <?php echo form_password("txtCFMPassword", '', 'id="txtCFMPassword" class="form-control" placeholder="Confirm new password"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section: Profile Details -->
                    <div class="settings-section-title mt-4">
                        <i class="bi bi-person-fill me-2"></i><span>Profile Information</span>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <label for="Name" class="form-label form-label-custom">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group input-group-custom">
                                <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                                <?php echo form_input("Name", $valName, 'id="Name" class="form-control" placeholder="Enter full name" onkeyup="javascript:this.value=this.value.toUpperCase();"'); ?>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label for="Mobile_Number" class="form-label form-label-custom">Mobile Number <span class="text-danger">*</span></label>
                            <div class="input-group input-group-custom">
                                <span class="input-group-text"><i class="bi bi-phone-fill"></i></span>
                                <?php echo form_input("Mobile_Number", $valMobile, 'id="Mobile_Number" class="form-control" placeholder="10-digit number" onkeypress="javascript:return numbersonly(this, event, false);"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12 col-md-6">
                            <label for="Email_id" class="form-label form-label-custom">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group input-group-custom">
                                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                <?php echo form_input("Email_id", $valEmail, 'id="Email_id" class="form-control" placeholder="Enter email address"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="d-flex flex-wrap gap-3 mt-4 pt-3 border-top justify-content-md-start justify-content-center">
                        <?php echo form_submit('Change Password', 'Save Changes', 'onClick="javascript: return fnschgpwdAdd()" class="btn btn-update-profile"'); ?>
                        <?php if (count($checkval) > 0 && isset($checkval[0]['is_change_password']) && $checkval[0]['is_change_password'] == 'Y') { ?>
                            <?php echo form_button('Cancel', 'Cancel', 'onClick="javascript:gotohome()" class="btn btn-cancel-profile"'); ?>
                        <?php } ?>
                    </div>
                    
                </div>
            </div>
        </div>

    </div>
</section>

<?php
echo form_close();
?>
