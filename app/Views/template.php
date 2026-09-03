<?php
$dynamic_fest_title = '';
$session = session();
$sub_dist_code = $session->get('SUB_DISTRICT');
$dist_code = $session->get('DISTRICT');
$school_code = $session->get('SCHOOL_CODE');

try {
    $db = \Config\Database::connect();

    if ($school_code && empty($sub_dist_code)) {
        $sch = $db->table('school_master')->select('sub_district_code, rev_district_code')->where('school_code', $school_code)->get()->getRowArray();
        if ($sch) {
            $sub_dist_code = $sch['sub_district_code'];
            $dist_code = $sch['rev_district_code'];
        }
    }

    if ($sub_dist_code) {
        $sub_fest = $db->table('sub_dist_kalolsavam_master')->select('sub_dist_kalolsavam_name')->where('sub_district_code', $sub_dist_code)->get()->getRowArray();
        if (!empty($sub_fest['sub_dist_kalolsavam_name'])) {
            $dynamic_fest_title = $sub_fest['sub_dist_kalolsavam_name'];
        } else {
            $sub_m = $db->table('sub_district_master')->select('sub_district_name')->where('sub_district_code', $sub_dist_code)->get()->getRowArray();
            if (!empty($sub_m['sub_district_name'])) {
                $dynamic_fest_title = $sub_m['sub_district_name'] . ' Sub-District Tamil Kalolsavam';
            }
        }
    }

    if (empty($dynamic_fest_title) && $dist_code) {
        $dist_fest = $db->table('dist_kalolsavam_master')->select('dist_kalolsavam_name')->where('rev_district_code', $dist_code)->get()->getRowArray();
        if (!empty($dist_fest['dist_kalolsavam_name'])) {
            $dynamic_fest_title = $dist_fest['dist_kalolsavam_name'];
        } else {
            $dist_m = $db->table('rev_district_master')->select('rev_district_name')->where('rev_district_code', $dist_code)->get()->getRowArray();
            if (!empty($dist_m['rev_district_name'])) {
                $dynamic_fest_title = $dist_m['rev_district_name'] . ' District Tamil Kalolsavam';
            }
        }
    }

    if (empty($dynamic_fest_title)) {
        $gen_fest = $db->table('kalolsavam_master')->select('kalolsavam_name')->where('status', 'O')->get()->getRowArray();
        if (!empty($gen_fest['kalolsavam_name'])) {
            $dynamic_fest_title = $gen_fest['kalolsavam_name'];
        }
    }
} catch (\Throwable $e) {
    // Fallback if DB not ready
}

if (empty($dynamic_fest_title)) {
    $dynamic_fest_title = 'Tamil Kalolsavam';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo (isset($title) && trim($title) != '') ? $title : esc($dynamic_fest_title) . ' <span id="year-range"></span>'; ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <meta name="csrf-token" content="<?php echo csrf_hash(); ?>">

  <!-- Favicons -->
  <link href="<?php echo base_url(); ?>assets/img/favicon.png" rel="icon">
  <link href="<?php echo base_url(); ?>assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  
  <style>
    /* Add your custom styles here */
    #custom-alert,
    #custom-error-alert {
      position: fixed;
      top: 70px;
      right: 10px;
      width: 360px;
      z-index: 1000;
      opacity: 0;
      transition: opacity 0.5s ease-in-out;
    }
    .toastify {
      overflow: hidden !important;
      padding: 14px 28px !important;
    }
    .toastify::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      height: 3px;
      background: rgba(255, 255, 255, 0.5);
      width: 100%;
      animation: toast-progress 4s linear forwards;
    }
    @keyframes toast-progress {
      from { width: 100%; }
      to { width: 0%; }
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
      #custom-alert,
      #custom-error-alert {
        width: 60%;
        margin-right: 0;
        border-radius: 0;
      }
    }

    /* Global Preloader / Loading Animation */
    #global-preloader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.94);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 999999;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.25s ease, visibility 0.25s ease;
      pointer-events: none;
    }
    #global-preloader.active {
      display: flex !important;
      opacity: 1 !important;
      visibility: visible !important;
      pointer-events: auto !important;
    }
    .preloader-content {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
    }
    .spinner-wrapper {
      position: relative;
      width: 80px;
      height: 80px;
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .custom-loader-ring {
      position: absolute;
      width: 100%;
      height: 100%;
      border: 3.5px solid transparent;
      border-top-color: #4154f1;
      border-right-color: #2eca6a;
      border-bottom-color: #ff771d;
      border-radius: 50%;
      animation: ring-spin 1.1s cubic-bezier(0.5, 0, 0.5, 1) infinite;
    }
    .custom-loader-ring-inner {
      position: absolute;
      width: 62px;
      height: 62px;
      border: 2.5px dashed rgba(65, 84, 241, 0.4);
      border-radius: 50%;
      animation: ring-spin-reverse 2s linear infinite;
    }
    .custom-loader-pulse {
      position: absolute;
      width: 46px;
      height: 46px;
      background: rgba(65, 84, 241, 0.1);
      border-radius: 50%;
      animation: pulse-glow 1.4s ease-in-out infinite alternate;
    }
    .loader-icon {
      font-size: 22px;
      color: #4154f1;
      z-index: 2;
      animation: icon-bounce 1.4s ease-in-out infinite alternate;
    }
    .loader-text {
      font-family: "Poppins", "Nunito", sans-serif;
      font-size: 16px;
      font-weight: 600;
      color: #012970;
      letter-spacing: 0.5px;
      margin-bottom: 4px;
    }
    .loader-subtext {
      font-family: "Open Sans", sans-serif;
      font-size: 12px;
      font-weight: 500;
      color: #899bbd;
      letter-spacing: 0.3px;
    }
    @keyframes ring-spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    @keyframes ring-spin-reverse {
      0% { transform: rotate(360deg); }
      100% { transform: rotate(0deg); }
    }
    @keyframes pulse-glow {
      0% { transform: scale(0.85); opacity: 0.5; }
      100% { transform: scale(1.15); opacity: 1; }
    }
    @keyframes icon-bounce {
      0% { transform: translateY(-2px) scale(0.95); }
      100% { transform: translateY(2px) scale(1.08); }
    }
  </style>
  
  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?php echo base_url(); ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo base_url(); ?>assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <!-- Boxicons removed - not needed for this application -->
  <!--<link href="<?php echo base_url(); ?>assets/vendor/quill/quill.snow.css" rel="stylesheet">-->
  <!--<link href="<?php echo base_url(); ?>assets/vendor/quill/quill.bubble.css" rel="stylesheet">-->
  <!--<link href="<?php echo base_url(); ?>assets/vendor/remixicon/remixicon.css" rel="stylesheet">-->
  <!--<link href="<?php echo base_url(); ?>assets/vendor/simple-datatables/style.css" rel="stylesheet">-->
  <!-- jQuery and SweetAlert -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- DataTables -->
  <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script>
  jQuery.noConflict();
  </script>
  <script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>js/common.js?v=20260830d"></script>
  <script>
    window._nativeArrayMethods = {
      map: Array.prototype.map,
      filter: Array.prototype.filter,
      reduce: Array.prototype.reduce,
      reduceRight: Array.prototype.reduceRight,
      forEach: Array.prototype.forEach,
      every: Array.prototype.every,
      some: Array.prototype.some,
      indexOf: Array.prototype.indexOf,
      lastIndexOf: Array.prototype.lastIndexOf
    };
  </script>
  <script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>js/prototype.js"></script>
  <script>
    if (window._nativeArrayMethods) {
      for (var _m in window._nativeArrayMethods) {
        if (window._nativeArrayMethods[_m]) {
          Array.prototype[_m] = window._nativeArrayMethods[_m];
        }
      }
    }

    // Clean prototype properties for modern JavaScript compatibility
    (function() {
      if (typeof Object.defineProperty === 'function') {
        ['each', '_each', 'clear', 'first', 'last', 'compact', 'flatten', 'without', 'uniq', 'intersect', 'clone', 'inspect', 'findAll', 'select', 'detect', 'collect', 'member', 'inGroupsOf', 'sortBy', 'toArray', 'zip', 'size', 'find'].forEach(function(prop) {
          if (Array.prototype.hasOwnProperty(prop)) {
            try {
              Object.defineProperty(Array.prototype, prop, { enumerable: false, configurable: true, writable: true });
            } catch (e) {}
          }
        });
      }
    })();
  </script>
  
  

  <!-- Template Main CSS File -->
  <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet">
  <?php echo (isset($_styles)) ? $_styles : ''; ?>
        <script language="javascript" type="text/javascript">
            //<![CDATA[
            var path = '<?php echo base_url(); ?>';
            var image_path = '<?php echo base_url('images/'); ?>';
            //]]>
        </script>
    <?php echo (isset($_scripts)) ? $_scripts : ''; ?>

</head>

<body>
  <!-- Global Page Loading Animation / Preloader -->
  <div id="global-preloader">
    <div class="preloader-content">
      <div class="spinner-wrapper">
        <div class="custom-loader-ring"></div>
        <div class="custom-loader-ring-inner"></div>
        <div class="custom-loader-pulse"></div>
        <i class="bi bi-stars loader-icon"></i>
      </div>
      <div class="loader-text" id="global-loader-text">Loading...</div>
      <div class="loader-subtext"><?php echo esc($dynamic_fest_title); ?></div>
    </div>
  </div>

<!-- ======= Header ======= -->
  

  <!-- Mobile Sidebar Backdrop -->
  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

  <?php if (isset($left_panel) && trim($left_panel) != '') { ?>
      <?php echo $left_panel; ?>
  <?php } ?>

  <?php
      if (isset($header) && trim($header) != '') {
          echo $header;
      }
      if (isset($menu) && trim($menu) != '') {
          echo $menu;
      } 
  ?>

  <main id="main" class="main">
      
        <?php if (isset($message) && trim($message) != '') { ?>
  <div id="custom-alert" class="alert alert-info alert-dismissible fade" role="alert">
    <span id="alert-message"><?php echo @$message; ?></span>
  </div>
<?php } ?>

<?php if (isset($error) && trim($error) != '') { ?>
  <div id="custom-error-alert" class="alert alert-danger alert-dismissible fade" role="alert">
    <span id="error-alert-message"><?php echo @$error; ?></span>
    <button type="button" class="btn close" data-dismiss="alert" aria-label="Close">
  </div>
<?php } ?>

<?php if (isset($success) && trim($success) != '') { ?>
  <div id="custom-success-alert" class="alert alert-success alert-dismissible fade" role="alert">
    <span id="success-alert-message"><?php echo @$success; ?></span>
    <button type="button" class="btn close" data-dismiss="alert" aria-label="Close">
  </div>
<?php } ?>

        <?php print $content ?>

</main>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="<?php echo base_url(); ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>


  <!-- Template Main JS File -->
  <script src="<?php echo base_url(); ?>assets/js/main.js"></script>
   <script src="<?php echo base_url(); ?>assets/js/tost.js"></script>
   <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
  // Override swal and alert globally to use Toastify
  var originalSwal = window.swal;
  window.swal = function() {
      var args = arguments;
      var title = "";
      var message = "";
      var type = "";
      
      if (args.length === 1 && typeof args[0] === "object") {
          var opts = args[0];
          title = opts.title || "";
          message = opts.text || "";
          type = opts.icon || opts.type || "";
          
          if (opts.buttons || opts.showCancelButton || opts.buttons === true || typeof opts.confirmButtonText !== 'undefined') {
              return originalSwal.apply(this, args);
          }
      } else if (args.length >= 1) {
          if (args.length === 1) {
              message = args[0];
          } else if (args.length === 2) {
              title = args[0];
              message = args[1];
          } else if (args.length >= 3) {
              title = args[0];
              message = args[1];
              type = args[2];
          }
      }
      
      if (type === "confirm" || message.toLowerCase().includes("confirm") || title.toLowerCase().includes("confirm")) {
          return originalSwal.apply(this, args);
      }
      
      var bg = "";
      var textToShow = title ? (title + ": " + message) : message;
      type = type ? type.toLowerCase() : "";
      
      if (type === "success") {
          bg = "linear-gradient(to right, #10b981, #34d399)"; // green
      } else if (type === "error" || type === "failed") {
          bg = "linear-gradient(to right, #ef4444, #f87171)"; // red
      } else if (type === "warning") {
          bg = "linear-gradient(to right, #f97316, #fb923c)"; // orange
      } else if (type === "info") {
          bg = "linear-gradient(to right, #3b82f6, #60a5fa)"; // blue
      } else {
          if (textToShow.toLowerCase().includes("oops") || textToShow.toLowerCase().includes("please") || textToShow.toLowerCase().includes("error") || textToShow.toLowerCase().includes("invalid")) {
              bg = "linear-gradient(to right, #f97316, #fb923c)"; // warning (orange)
          } else {
              bg = "linear-gradient(to right, #3b82f6, #60a5fa)"; // info (blue)
          }
      }
      
      Toastify({
        text: textToShow,
        duration: 4000,
        gravity: "top",
        position: "right",
        stopOnFocus: true,
        style: {
          background: bg,
          borderRadius: "8px",
          boxShadow: "0 4px 12px rgba(0, 0, 0, 0.15)"
        }
      }).showToast();
  };

  window.alert = function(message) {
      if (message && message.trim() !== '') {
        Toastify({
          text: message,
          duration: 4000,
          gravity: "top",
          position: "right",
          stopOnFocus: true,
          style: {
            background: "linear-gradient(to right, #f97316, #fb923c)", // Orange warning for browser alert
            borderRadius: "8px",
            boxShadow: "0 4px 12px rgba(0, 0, 0, 0.15)"
          }
        }).showToast();
      }
  };

  function showAlert(message) {
    if (message && message.trim() !== '') {
      Toastify({
        text: message,
        duration: 4000,
        gravity: "top",
        position: "right",
        stopOnFocus: true,
        style: {
          background: "linear-gradient(to right, #3b82f6, #60a5fa)",
          borderRadius: "8px",
          boxShadow: "0 4px 12px rgba(59, 130, 246, 0.2)"
        }
      }).showToast();
    }
  }

  function showErrorAlert(message) {
    if (message && message.trim() !== '') {
      // Decode entities like &lt;br&gt; or HTML tags if any, clean them for Toastify
      var cleanMessage = message.replace(/<[^>]*>/g, ' ');
      
      Toastify({
        text: cleanMessage,
        duration: 4000,
        gravity: "top",
        position: "right",
        stopOnFocus: true,
        style: {
          background: "linear-gradient(to right, #ef4444, #f87171)",
          borderRadius: "8px",
          boxShadow: "0 4px 12px rgba(239, 68, 68, 0.2)"
        }
      }).showToast();

      // If login modal is present on the page, keep it open/show it!
      var loginModalEl = document.getElementById('loginModal');
      if (loginModalEl) {
        var myModal = new bootstrap.Modal(loginModalEl);
        myModal.show();
      }
    }
  }

  function showSuccessAlert(message) {
    if (message && message.trim() !== '') {
      Toastify({
        text: message,
        duration: 4000,
        gravity: "top",
        position: "right",
        stopOnFocus: true,
        style: {
          background: "linear-gradient(to right, #10b981, #34d399)",
          borderRadius: "8px",
          boxShadow: "0 4px 12px rgba(16, 185, 129, 0.2)"
        }
      }).showToast();
    }
  }

  <?php
  $session = session();
  if ($session->getFlashdata('success')) {
      $success = $session->getFlashdata('success');
  }
  if ($session->getFlashdata('error')) {
      $error = $session->getFlashdata('error');
  }
  if ($session->getFlashdata('message')) {
      $message = $session->getFlashdata('message');
  }
  ?>
  // Assuming $message and $error are PHP variables containing the messages
  showAlert("<?php echo isset($message) ? htmlspecialchars($message) : ''; ?>");
  showErrorAlert("<?php echo isset($error) ? htmlspecialchars($error) : ''; ?>");
  showSuccessAlert("<?php echo isset($success) ? htmlspecialchars($success) : ''; ?>");
  
  // Global Preloader Helper Functions
  var globalLoaderTimer = null;

  window.showGlobalLoader = function(customText, isPermanent) {
    var preloader = document.getElementById('global-preloader');
    var textEl = document.getElementById('global-loader-text');
    if (textEl) {
      textEl.textContent = customText || "Loading...";
    }
    if (preloader) {
      preloader.classList.add('active');
    }
    if (globalLoaderTimer) {
      clearTimeout(globalLoaderTimer);
      globalLoaderTimer = null;
    }
    // Safety auto-dismiss so loader never permanently locks the screen
    globalLoaderTimer = setTimeout(function() {
      window.hideGlobalLoader();
    }, isPermanent ? 10000 : 6000);
  };

  window.hideGlobalLoader = function() {
    var preloader = document.getElementById('global-preloader');
    if (preloader) {
      preloader.classList.remove('active');
    }
    if (globalLoaderTimer) {
      clearTimeout(globalLoaderTimer);
      globalLoaderTimer = null;
    }
  };

  // Guarantee loader is hidden immediately on DOM ready and load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      window.hideGlobalLoader();
    });
  } else {
    window.hideGlobalLoader();
  }
  window.addEventListener('load', function() {
    window.hideGlobalLoader();
  });
  setTimeout(function() {
    window.hideGlobalLoader();
  }, 100);

  // Link navigation trigger
  document.addEventListener('click', function(e) {
    var link = e.target ? e.target.closest('a') : null;
    if (link) {
      var href = link.getAttribute('href');
      var target = (link.getAttribute('target') || '').toLowerCase();
      if (href && !href.startsWith('#') && !href.startsWith('javascript:') && target !== '_blank' && !link.hasAttribute('data-bs-toggle') && !link.hasAttribute('data-bs-target') && !link.hasAttribute('download')) {
        if (!e.defaultPrevented && !e.ctrlKey && !e.metaKey && !e.shiftKey) {
          window.showGlobalLoader("Loading page...", true);
        }
      }
    }
  });

  // Form submit trigger - do NOT trigger on new-tab/blank submissions or forms opting out
  document.addEventListener('submit', function(e) {
    var form = e.target;
    var target = form ? (form.getAttribute('target') || '').toLowerCase() : '';
    if (!e.defaultPrevented && target !== '_blank' && (!form || !form.classList.contains('no-global-loader'))) {
      window.showGlobalLoader("Saving & Processing...", false);
    }
  });

  // Prototype.js Global AJAX Hook
  if (typeof Ajax !== 'undefined' && Ajax.Responders) {
    Ajax.Responders.register({
      onComplete: function() {
        window.hideGlobalLoader();
      },
      onFailure: function() {
        window.hideGlobalLoader();
      },
      onException: function() {
        window.hideGlobalLoader();
      }
    });
  }

  // CSRF Token Global AJAX Handler
  (function() {
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfMeta) return;
    var token = csrfMeta.getAttribute('content');
    if (!token) return;

    // Patch fetch() to include CSRF header on same-origin POST requests
    var _origFetch = window.fetch;
    window.fetch = function(url, opts) {
      opts = opts || {};
      var method = (opts.method || 'GET').toUpperCase();
      if (method === 'POST' || method === 'PUT' || method === 'PATCH' || method === 'DELETE') {
        opts.headers = opts.headers || {};
        if (opts.headers instanceof Headers) {
          opts.headers.set('X-CSRF-TOKEN', token);
        } else if (typeof opts.headers === 'object' && !(opts.headers instanceof Headers)) {
          opts.headers['X-CSRF-TOKEN'] = token;
        }
      }
      return _origFetch.call(window, url, opts);
    };

    // Patch Prototype.js Ajax.Request to include CSRF header on POST requests
    if (typeof Ajax !== 'undefined' && Ajax.Request) {
      var _origSetRequestHeaders = Ajax.Request.prototype.setRequestHeaders;
      Ajax.Request.prototype.setRequestHeaders = function() {
        _origSetRequestHeaders.call(this);
        if (this.method === 'post') {
          this.transport.setRequestHeader('X-CSRF-TOKEN', token);
        }
      };
    }

    // Patch jQuery AJAX to include CSRF header (if present)
    if (typeof jQuery !== 'undefined' && jQuery.ajaxSetup) {
      jQuery.ajaxSetup({
        beforeSend: function(xhr) {
          xhr.setRequestHeader('X-CSRF-TOKEN', token);
        }
      });
    }

    // Auto-inject CSRF hidden field into all POST forms on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function() {
      var forms = document.querySelectorAll('form[method="post"], form[method="POST"]');
      for (var i = 0; i < forms.length; i++) {
        var f = forms[i];
        if (!f.querySelector('input[name="csrf_test_name"]')) {
          var inp = document.createElement('input');
          inp.type = 'hidden';
          inp.name = 'csrf_test_name';
          inp.value = token;
          f.appendChild(inp);
        }
      }
    });
  })();

  // jQuery Global AJAX Hook
  if (typeof jQuery !== 'undefined') {
    jQuery(document).ajaxStop(function() {
      window.hideGlobalLoader();
    }).ajaxError(function() {
      window.hideGlobalLoader();
    });
  }

  // Dynamically set the year in the title
  document.addEventListener("DOMContentLoaded", function() {
      const currentYear = new Date().getFullYear();
      const nextYear = currentYear + 1;
      const yearRange = `${currentYear} - ${nextYear}`;
      document.title = document.title.replace('<span id="year-range"></span>', yearRange);
  });
        
</script>


</body>

</html>
