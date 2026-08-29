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
} 

@media (min-width: 1200px) {
  #main, #footer {
    margin-left: 0px;
  }
}

#main {
  margin-top: 40px;
  padding: 0px;
  transition: all 0.3s;
}

.headfont{
  font-size:20px;
  font-weight:bold;
  padding:12px;
}

.header-primary {
    font-size: 1.0em; /* Adjust the font size as needed */
    font-weight: bold;
    color: #FFFFFF; /* Use your desired primary color */
    background: #007bff;
    /* margin-bottom: 10px; Adjust the margin as needed */
    padding:10px;
    border-radius:5px 5px 0 0;
}

@media (min-width: 1400px) {
            #login-container {
                margin-left: -250px;
            }
        }

        @media (min-width: 1200px) {
            #login-container {
                margin-left: -250px;
            }
        }

        @media (min-width: 1200px) {
            #main, #footer {
                margin-left: 0px;
            }
        }

        #main {
            margin-top: 40px;
            padding: 0px;
            transition: all 0.3s;
        }

        .headfont {
            font-size: 20px;
            font-weight: bold;
            padding: 12px;
        }

        .header-primary {
            font-size: 1.0em;
            font-weight: bold;
            color: #FFFFFF;
            background: #007bff;
            padding: 10px;
            border-radius: 5px 5px 0 0;
        }

        iframe {
            overflow: hidden;
            width: 100%;
            border: none;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fff8f0 0%, #ffebee 100%);
            min-height: 100vh;
            padding-top: 70px;
        }

        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://placehold.co/1200x600/FF6B35/FFFFFF?text=தமிழ்+கலைவிழா+2025') center/cover no-repeat;
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .section-title {
            position: relative;
            margin-bottom: 50px;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, #FF6B35, #D62828);
            border-radius: 2px;
        }

        .event-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .venue-card {
            background: linear-gradient(135deg, #FF6B35 0%, #D62828 100%);
            color: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
        }

        .btn-gradient {
            background: linear-gradient(to right, #FF6B35, #D62828);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(214, 40, 40, 0.3);
        }

        .footer {
            background: #1a1a1a;
            color: white;
            padding: 40px 0;
            margin-top: 60px;
        }

        .animate-fade-in {
            animation: fadeInUp 1s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-in {
            animation: slideInUp 0.8s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tamil-font {
            font-family: 'Noto Sans Tamil', sans-serif;
        }
</style>
<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">
   <div class="d-flex align-items-center justify-content-between">
      <a href="#" class="logo d-flex align-items-center">
      <img src="<?php echo base_url(); ?>assets/img/logo.png" alt="">
      <span class="d-none d-lg-block">Tamil Kalaivizha</span>
      </a>
      <!-- <i class="bi bi-list toggle-sidebar-btn"></i> -->
   </div>
   <!-- End Logo -->
   <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
         <li class="nav-item dropdown pe-3">
            <?php if ($this->session->get('dname')) : ?>
            <?php else : ?>
            <!-- Show login modal trigger if user is not logged in -->
						 <a href="<?php echo base_url(); ?>publishresult/publicresult/resultview" class="btn btn-warning" target="_blank">View Result</a>
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
            <?php endif; ?>
         </li>
         <!-- End Profile Nav -->
      </ul>
   </nav>
   <!-- End Icons Navigation -->
</header>
<!-- End Header -->
<!-- Bootstrap Modal for Login -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true" data-bs-backdrop="static">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="loginModalLabel">Login to Your Account</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <?php echo form_open(base_url() . 'login', array('name' => 'loginform', 'id' => 'loginform', 'onsubmit' => 'return checkLogin();')); ?>
         <div class="modal-body">
            <!-- Your login form goes here -->
            <label for="yourUsername" class="form-label visually-hidden">Username</label>
            <div class="input-group has-validation mb-3">
                <span class="input-group-text"><i class="bi bi-person-fill text-primary"></i></span>
                <?php echo form_input("txtUserName", (isset($_POST['txtUserName']) and trim($_POST['txtUserName']) != '' and trim($_POST['txtUserName']) != 'text') ? $_POST['txtUserName'] : get_cookie('username') ?? '', 'class="form-control" placeholder="Enter your username" required'); ?>
                <div class="invalid-feedback">Please enter your username.</div>
            </div>

            <label for="yourPassword" class="form-label visually-hidden">Password</label>
            <div class="input-group has-validation mb-3">
                <span class="input-group-text"><i class="bi bi-lock-fill text-primary"></i></span>
                <?php echo form_password("txtPassword", get_cookie('password') ?? '', 'class="form-control" id="txtPassword" placeholder="Enter your password" required'); ?>
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="invalid-feedback">Please enter your password!</div>
            </div>

            <div class="form-check mb-3">
               <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
               <label class="form-check-label" for="rememberMe">Remember me</label>
            </div>
            
            <?php echo form_submit('login', 'Login', 'class="btn btn-primary w-100"'); ?>
         </div>
      </div>
   </div>
</div>
<?php echo form_close(); ?>
<!-- ------------------------------------------------------------------------------------------------- -->

<!-- Landing page content -->
    <main id="main">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 animate-fade-in">
                        <h1 class="display-3 fw-bold mb-4">தமிழ் கலைவிழா 2025</h1>
                        <p class="lead mb-4">சித்தூர் உபமாவட்டம் SKHSS நல்லேப்பள்ளியில் நடைபெறும் இந்த கலைவிழாவில் தமிழ் மாணவர்கள் மற்றும் தமிழ் ஆசிரியர்கள்,, தமிழ் ஆர்வலர்கள், பெற்றோர்கள், பொதுமக்கள் அனைவரையும் அன்போடு வரவேற்கிறோம்.</p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <div class="d-flex align-items-center bg-white bg-opacity-25 px-4 py-2 rounded-pill">
                                <i class="bi bi-geo-alt-fill me-2"></i>
                                <span>SKHSS நல்லேப்பள்ளி, சித்தூர்</span>
                            </div>
                            <div class="d-flex align-items-center bg-white bg-opacity-25 px-4 py-2 rounded-pill">
                                <i class="bi bi-calendar-check-fill me-2"></i>
                                <span>அக்டோபர் 29 முதல் நவம்பர் 01, 2025</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="py-5" id="about">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center mb-5">
                        <h2 class="section-title fw-bold text-dark">கலை விழா பற்றி</h2>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center animate-slide-in">
                        <p class="lead text-muted">
                            தமிழ் கலைவிழா 2025 என்பது தமிழ் மொழியின் சிறப்பையும், கலாச்சாரத்தின் ஆழத்தையும் கொண்டாடும் ஒரு முக்கியமான நிகழ்ச்சியாகும். 
                            இந்த விழாவில் இளைஞர்கள், கலைஞர்கள் மற்றும் தமிழ் ஆர்வலர்கள் அனைவரும் ஒன்றிணைந்து தமிழின் பெருமையை உலகறிய உரைக்கின்றனர்.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Events Section -->
        <section class="py-5 bg-light" id="events">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center mb-5">
                        <h2 class="section-title fw-bold text-dark">நிகழ்ச்சிகள்</h2>
                    </div>
                </div>
                <div class="row">
					<div class="row">
  <!-- கதை எழுதுதல் -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card event-card h-100">
      <div class="card-body text-center p-4">
        <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
          <i class="bi bi-journal-text fs-4"></i>
        </div>
        <h5 class="card-title fw-bold">கதை எழுதுதல்</h5>
        <p class="card-text text-muted">உங்கள் கற்பனை திறனையும் மொழி வளத்தையும் வெளிப்படுத்தி சுவாரஸ்யமான மற்றும் உணர்வூட்டும் தமிழ்க் கதைகளை எழுதுங்கள்.</p>
      </div>
    </div>
  </div>

  <!-- கவிதை எழுதுதல் -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card event-card h-100">
      <div class="card-body text-center p-4">
        <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
          <i class="bi bi-pencil-square fs-4"></i>
        </div>
        <h5 class="card-title fw-bold">கவிதை எழுதுதல்</h5>
        <p class="card-text text-muted">உங்களின் உணர்வுகள், சிந்தனைகள் மற்றும் மொழியின் இனிமையை இணைத்து ஆழமான தமிழ்க் கவிதைகளை படைத்து அனைவரையும் கவருங்கள்.</p>
      </div>
    </div>
  </div>

  <!-- கட்டுரை எழுதுதல் -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card event-card h-100">
      <div class="card-body text-center p-4">
        <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
          <i class="bi bi-file-earmark-text fs-4"></i>
        </div>
        <h5 class="card-title fw-bold">கட்டுரை எழுதுதல்</h5>
        <p class="card-text text-muted">சமூகப் பிரச்சினைகள், கலாச்சாரம், அறிவியல், சுற்றுச்சூழல் போன்ற தலைப்புகளில் உங்கள் பார்வையை தெளிவாகவும் தர்க்கபூர்வமாகவும் வெளிப்படுத்துங்கள்.</p>
      </div>
    </div>
  </div>

  <!-- நாடகம் -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card event-card h-100">
      <div class="card-body text-center p-4">
        <div class="bg-success bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
          <i class="bi bi-people fs-4"></i>
        </div>
        <h5 class="card-title fw-bold">நாடகம்</h5>
        <p class="card-text text-muted">கதாபாத்திரங்கள், உரையாடல்கள், உணர்வுகள் ஆகியவற்றை இணைத்து மேடைமேல் உயிரோட்டமிக்க தமிழ் நாடகத்தை வெளிப்படுத்துங்கள்.</p>
      </div>
    </div>
  </div>

  <!-- மெல்லிசை -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card event-card h-100">
      <div class="card-body text-center p-4">
        <div class="bg-info bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
          <i class="bi bi-music-note-beamed fs-4"></i>
        </div>
        <h5 class="card-title fw-bold">மெல்லிசை</h5>
        <p class="card-text text-muted">இனிமையான குரலிலும் சரியான சுரத்திலும் பாடி, தமிழ் மெல்லிசையின் இனிமையை வெளிப்படுத்துங்கள்.</p>
      </div>
    </div>
  </div>

  <!-- நாட்டுப்புறப் பாடல் -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card event-card h-100">
      <div class="card-body text-center p-4">
        <div class="bg-warning bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
          <i class="bi bi-mic fs-4"></i>
        </div>
        <h5 class="card-title fw-bold">நாட்டுப்புறப் பாடல்</h5>
        <p class="card-text text-muted">பாரம்பரிய இசை மற்றும் பண்பாட்டின் செழுமையை உணர்த்தும் நாட்டுப்புறப் பாடல்களை ஆற்றலுடன் பாடி அனைவரையும் கவருங்கள்.</p>
      </div>
    </div>
  </div>

  <!-- தேசபக்திப் பாடல் -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card event-card h-100">
      <div class="card-body text-center p-4">
        <div class="bg-danger bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
          <i class="bi bi-flag fs-4"></i>
        </div>
        <h5 class="card-title fw-bold">தேசபக்திப் பாடல்</h5>
        <p class="card-text text-muted">தேசத்தின் பெருமை, வீரத்தை, ஒற்றுமையை உணர்த்தும் தமிழில் தேசபக்திப் பாடல்களை உணர்வுடன் பாடி, பார்வையாளர்களின் மனதில் தேசப்பற்று ஊட்டுங்கள்.</p>
      </div>
    </div>
  </div>

  <!-- செய்யுள் ஒப்புவித்தல் -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card event-card h-100">
      <div class="card-body text-center p-4">
        <div class="bg-info bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
          <i class="bi bi-chat-square-quote fs-4"></i>
        </div>
        <h5 class="card-title fw-bold">செய்யுள் ஒப்புவித்தல்</h5>
        <p class="card-text text-muted">பாரம்பரிய இலக்கியக் கவிதைகளின் உணர்வு, பாவம், ரசனை ஆகியவற்றை குரல் நயத்துடனும் உடல்மொழியுடனும் அழகாக வெளிப்படுத்துங்கள்.</p>
      </div>
    </div>
  </div>

  <!-- தனியாள் நடிப்பு -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card event-card h-100">
      <div class="card-body text-center p-4">
        <div class="bg-secondary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
          <i class="bi bi-person-standing fs-4"></i>
        </div>
        <h5 class="card-title fw-bold">தனியாள் நடிப்பு</h5>
        <p class="card-text text-muted">எந்தவொரு கருத்தையோ உணர்வையோ சொற்கள் இன்றி, முகபாவனைகள் மற்றும் உடல் மொழியின் மூலம் சிறப்பாக வெளிப்படுத்தும் உங்கள் நடிப்பு திறனை வெளிப்படுத்துங்கள்.</p>
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
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                            <i class="bi bi-star text-white"></i>
                        </div>
                        <h4 class="text-white mb-0">தமிழ் கலைவிழா 2025</h4>
                    </div>
                    <p class="text-muted">© 2025 தமிழ் கலைவிழா. அனைத்து உரிமைகளும் பாதுகாக்கப்பட்டவை.</p>
                </div>
            </div>
        </div>
    </footer>

<!-- ------------------------------------------------------------------------------------------ -->
<!-- java codes start -->
<script type="text/javascript">
   function checkLogin() {
       
    // Add your validation logic here
    var username = $("#txtUserName").val().trim();
        var password = $("#txtPassword").val().trim();

        // Example: Check if the username and password match
        if (username === "your_valid_username" && password === "your_valid_password") {
            // Close the modal
            $("#loginModal").modal("hide");
            return true; // Allow form submission
            $('loginform').submit();
        } else {
            alert("Invalid username or password. Please try again.");
            return false; // Prevent form submission
        }
       
   }

   document.addEventListener("DOMContentLoaded", function () {
        var togglePassword = document.getElementById("togglePassword");
        var passwordField = document.getElementById("txtPassword");

        togglePassword.addEventListener("click", function () {
            var passwordFieldType = passwordField.type;

            if (passwordFieldType === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        });
    });
    
   // Add animation on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-slide-in');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.event-card, .venue-card, .card').forEach(card => {
                observer.observe(card);
            });
        });
</script>
