<style>
@media (min-width: 1400px) {
  #login-container {
    margin-left: -250px;
  }
}

@media (min-width: 1200px) {
  #login-container {
    margin-left: -250px;
  }
  #main, #footer {
    margin-left: 0px;
  }
} 

#main {
  margin-top: 50px;
  padding: 0px;
  transition: all 0.3s;
}

body {
  font-family: 'TAU-Pallai', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(135deg, #f8faff 0%, #f1f5f9 100%);
  min-height: 100vh;
  padding-top: 60px;
}

.hero-section {
  background: linear-gradient(135deg, rgba(30, 41, 59, 0.92) 0%, rgba(15, 23, 42, 0.95) 100%), url('https://images.unsplash.com/photo-1514525253161-7a46d19cd819?q=80&w=1600&auto=format&fit=crop') center/cover no-repeat;
  color: white;
  padding: 70px 0 60px;
  text-align: center;
  position: relative;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.section-title {
  position: relative;
  margin-bottom: 40px;
  font-weight: 700;
  color: #1e293b;
}

.section-title:after {
  content: '';
  position: absolute;
  bottom: -12px;
  left: 50%;
  transform: translateX(-50%);
  width: 70px;
  height: 4px;
  background: linear-gradient(to right, #3b82f6, #6366f1);
  border-radius: 4px;
}

/* District Card Styles */
.district-card {
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 16px;
  background: #ffffff;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
}

.district-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(59, 130, 246, 0.12);
  border-color: #bfdbfe;
}

.district-header-palakkad {
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
  color: white;
  padding: 1.25rem 1.5rem;
}

.district-header-idukki {
  background: linear-gradient(135deg, #065f46 0%, #10b981 100%);
  color: white;
  padding: 1.25rem 1.5rem;
}

.district-header-wayanad {
  background: linear-gradient(135deg, #854d0e 0%, #eab308 100%);
  color: white;
  padding: 1.25rem 1.5rem;
}

.subdistrict-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 14px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 50rem;
  color: #334155;
  font-size: 0.9rem;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.2s ease;
  margin: 4px;
}

.subdistrict-pill:hover {
  background: #3b82f6;
  color: #ffffff;
  border-color: #3b82f6;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
}

.event-card {
  transition: all 0.3s ease;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}

.event-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 25px rgba(0,0,0,0.08);
  border-color: #cbd5e1;
}

.footer {
  background: #0f172a;
  color: #94a3b8;
  padding: 40px 0 30px;
  margin-top: 60px;
}

.tamil-font {
  font-family: 'TAU-Pallai', 'Noto Sans Tamil', sans-serif;
}
</style>

<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center bg-white shadow-sm" style="z-index: 1020;">
   <div class="container-fluid d-flex align-items-center justify-content-between px-3 px-md-4">
      <a href="<?php echo base_url(); ?>" class="logo d-flex align-items-center text-decoration-none">
         <img src="<?php echo base_url(); ?>assets/img/logo.png" alt="Logo" style="max-height: 40px;" class="me-2">
         <span class="d-none d-sm-block fw-bold text-dark fs-5">தமிழ் கலைவிழா 2025</span>
      </a>
      
      <div class="d-flex align-items-center gap-2">
         <a href="<?php echo base_url(); ?>publishresult/publicresult/resultview" class="btn btn-outline-primary d-inline-flex align-items-center gap-2 rounded-pill px-3 py-1 fw-semibold">
            <i class="bi bi-trophy-fill text-warning"></i> <span>View Results</span>
         </a>
         <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-pill px-3 py-1 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#loginModal">
            <i class="bi bi-box-arrow-in-right"></i> <span>Login</span>
         </button>
      </div>
   </div>
</header>
<!-- End Header -->

<!-- Bootstrap Modal for Login -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true" data-bs-backdrop="static">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
         <div class="modal-header bg-primary text-white p-3 px-4">
            <h5 class="modal-title d-flex align-items-center gap-2 fs-5" id="loginModalLabel">
               <i class="bi bi-shield-lock-fill"></i> User & School Login
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         
         <form id="loginform" name="loginform" onsubmit="return handleAjaxLogin(event);" method="post" accept-charset="utf-8">
            <?php echo csrf_field(); ?>
            <div class="modal-body p-4">
               <div class="mb-3">
                   <label for="txtUserName" class="form-label small fw-semibold text-dark mb-1">Username / School Code</label>
                   <div class="input-group has-validation">
                       <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-fill text-primary"></i></span>
                       <input type="text" name="txtUserName" id="txtUserName" value="<?php echo (isset($_POST['txtUserName']) and trim($_POST['txtUserName']) != '' and trim($_POST['txtUserName']) != 'text') ? htmlspecialchars($_POST['txtUserName']) : htmlspecialchars(get_cookie('username') ?? ''); ?>" class="form-control border-start-0" placeholder="Enter your username or school code" required autocomplete="username">
                   </div>
                   <div id="txtUserNameFeedback" class="invalid-feedback d-none small mt-1">Please enter your username.</div>
               </div>

               <div class="mb-3">
                   <label for="txtPassword" class="form-label small fw-semibold text-dark mb-1">Password</label>
                   <div class="input-group has-validation">
                       <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-primary"></i></span>
                       <input type="password" name="txtPassword" id="txtPassword" value="<?php echo htmlspecialchars(get_cookie('password') ?? ''); ?>" class="form-control border-start-0 border-end-0" placeholder="Enter your password" required autocomplete="current-password">
                       <button class="btn btn-outline-secondary border-start-0" type="button" id="togglePassword">
                           <i class="bi bi-eye"></i>
                       </button>
                   </div>
                   <div id="txtPasswordFeedback" class="invalid-feedback d-none small mt-1">Please enter your password.</div>
               </div>

               <div class="d-flex justify-content-between align-items-center mb-4">
                  <div class="form-check">
                     <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                     <label class="form-check-label small text-muted" for="rememberMe">Remember me</label>
                  </div>
                   <a href="<?php echo base_url('downloads/serve/chittur_itemcodes.html'); ?>" class="small text-decoration-none text-primary" target="_blank">Item Codes</a>
               </div>
               
               <button type="submit" name="login" value="Login" id="btnLoginSubmit" class="btn btn-primary w-100 py-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 rounded-3 shadow-sm">
                   <i class="bi bi-box-arrow-in-right fs-5"></i> <span>Login to Account</span>
               </button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Main Landing page content -->
<main id="main">
    <!-- Interactive District Explorer Section (Palakkad, Idukki, Wayanad) -->
    <section class="py-4 bg-white" id="districts">
        <div class="container">
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8 text-center">
                    <h2 class="section-title fw-bold tamil-font">மாவட்டங்கள் & துணை மாவட்ட முடிவுகள்</h2>
                    <p class="text-muted fs-6">
                        உங்கள் மாவட்டத்தைத் தேர்வுசெய்து, அந்தந்த துணை மாவட்டத்தின் நேரடி பொது முடிவுகளை உடனுக்குடன் பார்க்கவும்.
                    </p>
                </div>
            </div>

            <!-- District Cards Grid (Dynamically Generated from Database) -->
            <div class="row g-4 justify-content-center">
                <?php if (!empty($districts_data)) : foreach ($districts_data as $idx => $d) : 
                    $headerClass = ($d['district_code'] == 9) ? 'district-header-palakkad' : (($d['district_code'] == 6) ? 'district-header-idukki' : 'district-header-wayanad');
                    $badgeColor = ($d['district_code'] == 9) ? 'text-primary' : (($d['district_code'] == 6) ? 'text-success' : 'text-warning');
                    $trophyColor = ($d['district_code'] == 9) ? 'text-primary' : (($d['district_code'] == 6) ? 'text-success' : 'text-danger');
                ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="district-card h-100 d-flex flex-column">
                            <div class="<?php echo $headerClass; ?> d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="fw-bold mb-0 tamil-font"><?php echo esc($d['tamil_name']); ?></h4>
                                    <small class="text-white-50"><?php echo esc($d['district_name']); ?> District</small>
                                </div>
                                <span class="badge bg-white <?php echo $badgeColor; ?> rounded-pill px-2 py-1 fw-bold">
                                    <?php echo count($d['sub_districts']); ?> துணை மாவட்டங்கள்
                                </span>
                            </div>

                            <!-- District Results Primary Action Button -->
                            <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <span class="small text-muted fw-semibold"><i class="bi bi-geo-alt-fill me-1 text-danger"></i>மாவட்ட முடிவுகள்:</span>
                                <a href="<?php echo base_url('publishresult/publicresult/resultview?district=' . $d['district_code']); ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                                    <i class="bi bi-trophy-fill text-warning"></i> டிஸ்டிக் ரிசல்ட் (District Results)
                                </a>
                            </div>

                            <div class="p-3 flex-grow-1">
                                <div class="mb-2">
                                    <small class="text-muted fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                        <i class="bi bi-grid-fill me-1 text-secondary"></i>துணை மாவட்டங்கள் (Sub-Districts):
                                    </small>
                                </div>
                                <div class="d-flex flex-wrap gap-1">
                                    <?php if (!empty($d['sub_districts'])) : foreach ($d['sub_districts'] as $sd) : ?>
                                        <a href="<?php echo base_url('publishresult/publicresult/resultview?sub_district=' . $sd['sub_district_code']); ?>" class="subdistrict-pill" title="<?php echo esc($sd['sub_district_name']); ?> Results">
                                            <i class="bi bi-trophy <?php echo $trophyColor; ?>"></i> <?php echo esc($sd['sub_district_name']); ?>
                                        </a>
                                    <?php endforeach; endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-5 bg-light" id="about">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center mb-4">
                    <h2 class="section-title fw-bold tamil-font">கலை விழா பற்றி</h2>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <p class="lead text-muted tamil-font">
                        தமிழ் கலைவிழா 2025 என்பது தமிழ் மொழியின் சிறப்பையும், கலாச்சாரத்தின் ஆழத்தையும் கொண்டாடும் ஒரு முக்கியமான மாநில மற்றும் மாவட்ட அளவிலான விழாவாகும். 
                        இவ்விழாவில் மாணவ மாணவிகள் தங்கள் திறமைகளை வெளிப்படுத்தி, தமிழின் பெருமையை நிலைநாட்டுகின்றனர்.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Events Section -->
    <section class="py-5 bg-white" id="events">
        <div class="container">
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8 text-center">
                    <h2 class="section-title fw-bold tamil-font">முக்கிய நிகழ்ச்சிகள்</h2>
                    <p class="text-muted">கலைவிழாவில் நடைபெறும் முக்கிய போட்டிகள் மற்றும் நிகழ்வுகள்</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card event-card h-100 p-3 text-center">
                        <div class="card-body">
                            <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 55px; height: 55px;">
                                <i class="bi bi-journal-text fs-4"></i>
                            </div>
                            <h5 class="card-title fw-bold tamil-font">கதை & கவிதை எழுதுதல்</h5>
                            <p class="card-text text-muted small">கற்பனைத் திறன், மொழி வளம் மற்றும் சொல் நயங்களை வெளிப்படுத்தும் படைப்புப் போட்டிகள்.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card event-card h-100 p-3 text-center">
                        <div class="card-body">
                            <div class="bg-success bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 55px; height: 55px;">
                                <i class="bi bi-people fs-4"></i>
                            </div>
                            <h5 class="card-title fw-bold tamil-font">நாடகம் & தனியாள் நடிப்பு</h5>
                            <p class="card-text text-muted small">கதாபாத்திரங்கள், உடல்மொழி மற்றும் உணர்ச்சிகரமான கலைவெளிப்பாடுகள்.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card event-card h-100 p-3 text-center">
                        <div class="card-body">
                            <div class="bg-warning bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 55px; height: 55px;">
                                <i class="bi bi-music-note-beamed fs-4"></i>
                            </div>
                            <h5 class="card-title fw-bold tamil-font">இசை & நாட்டுப்புறப் பாடல்</h5>
                            <p class="card-text text-muted small">மெல்லிசை, தேசபக்தி மற்றும் பாரம்பரிய நாட்டுப்புறப் பாடல்களின் சங்கமம்.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <h5 class="text-white mb-0 tamil-font">தமிழ் கலைவிழா 2025 - 2026</h5>
                </div>
                <p class="small text-muted mb-0">© 2025 தமிழ்நாடு & கேரளா பள்ளிக் கல்வித்துறை. அனைத்து உரிமைகளும் பாதுகாக்கப்பட்டவை.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script type="text/javascript">
   function handleAjaxLogin(e) {
       if (e) e.preventDefault();

       var usernameInput = document.getElementById('txtUserName');
       var passwordInput = document.getElementById('txtPassword');
       var userFeedback = document.getElementById('txtUserNameFeedback');
       var pwdFeedback = document.getElementById('txtPasswordFeedback');

       // Reset previous validation
       if (usernameInput) usernameInput.classList.remove('is-invalid');
       if (passwordInput) passwordInput.classList.remove('is-invalid');
       if (userFeedback) userFeedback.classList.add('d-none');
       if (pwdFeedback) pwdFeedback.classList.add('d-none');

       var username = usernameInput ? usernameInput.value.trim() : '';
       var password = passwordInput ? passwordInput.value.trim() : '';

       if (!username) {
           if (usernameInput) usernameInput.classList.add('is-invalid');
           if (userFeedback) {
               userFeedback.textContent = 'Please enter your username or school code.';
               userFeedback.classList.remove('d-none');
           }
           showTopRightToast('Please enter your username or school code.', 'error');
           if (usernameInput) usernameInput.focus();
           return false;
       }

       if (!password) {
           if (passwordInput) passwordInput.classList.add('is-invalid');
           if (pwdFeedback) {
               pwdFeedback.textContent = 'Please enter your password.';
               pwdFeedback.classList.remove('d-none');
           }
           showTopRightToast('Please enter your password.', 'error');
           if (passwordInput) passwordInput.focus();
           return false;
       }

       var submitBtn = document.getElementById('btnLoginSubmit');
       var originalBtnHtml = submitBtn ? submitBtn.innerHTML : 'Login';
       if (submitBtn) {
           submitBtn.disabled = true;
           submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Verifying...';
       }

       // Show global loader
       if (typeof window.showGlobalLoader === 'function') {
           window.showGlobalLoader('Verifying credentials & authenticating... Please wait', true);
       }

       var formEl = document.getElementById('loginform');
       var formData = new FormData(formEl);

       fetch('<?php echo base_url("login/ajax_login"); ?>', {
           method: 'POST',
           body: formData,
           headers: {
               'X-Requested-With': 'XMLHttpRequest'
           }
       })
       .then(function(res) {
           return res.json();
       })
       .then(function(data) {
           if (data.status === 'success') {
               if (typeof window.showGlobalLoader === 'function') {
                   window.showGlobalLoader('Login Successful! Redirecting to Dashboard...', true);
               }
               window.location.href = data.redirect;
           } else {
               // ERROR: Do NOT close login modal!
               if (typeof window.hideGlobalLoader === 'function') {
                   window.hideGlobalLoader();
               }
               if (submitBtn) {
                   submitBtn.disabled = false;
                   submitBtn.innerHTML = originalBtnHtml;
               }

               // Highlight field
               if (data.field === 'username' && usernameInput) {
                   usernameInput.classList.add('is-invalid');
                   if (userFeedback) {
                       userFeedback.textContent = data.message;
                       userFeedback.classList.remove('d-none');
                   }
                   usernameInput.focus();
               } else if (passwordInput) {
                   passwordInput.classList.add('is-invalid');
                   if (pwdFeedback) {
                       pwdFeedback.textContent = data.message;
                       pwdFeedback.classList.remove('d-none');
                   }
                   passwordInput.focus();
               }

               // Show exact reason in Toastify at top right
               showTopRightToast(data.message || 'Invalid credentials entered.', 'error');
           }
       })
       .catch(function(err) {
           console.error('Login error:', err);
           if (typeof window.hideGlobalLoader === 'function') {
               window.hideGlobalLoader();
           }
           if (submitBtn) {
               submitBtn.disabled = false;
               submitBtn.innerHTML = originalBtnHtml;
           }
           showTopRightToast('An unexpected network error occurred. Please try again.', 'error');
       });

       return false;
   }

   function showTopRightToast(msg, type) {
       if (typeof Toastify !== 'undefined') {
           var bg = type === 'success' ? 'linear-gradient(to right, #10b981, #059669)' : 'linear-gradient(to right, #ef4444, #dc2626)';
           Toastify({
               text: msg,
               duration: 5000,
               gravity: "top",
               position: "right",
               stopOnFocus: true,
               style: {
                   background: bg,
                   borderRadius: "8px",
                   boxShadow: "0 8px 20px rgba(0, 0, 0, 0.2)",
                   fontWeight: "500",
                   fontSize: "14px"
               }
           }).showToast();
       } else if (typeof Swal !== 'undefined') {
           Swal.fire({
               toast: true,
               position: 'top-end',
               icon: type,
               title: msg,
               showConfirmButton: false,
               timer: 5000
           });
       } else {
           alert(msg);
       }
   }

   document.addEventListener("DOMContentLoaded", function () {
        var togglePassword = document.getElementById("togglePassword");
        var passwordField = document.getElementById("txtPassword");

        if (togglePassword && passwordField) {
            togglePassword.addEventListener("click", function () {
                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    togglePassword.innerHTML = '<i class="bi bi-eye-slash"></i>';
                } else {
                    passwordField.type = "password";
                    togglePassword.innerHTML = '<i class="bi bi-eye"></i>';
                }
            });
        }
   });
</script>
