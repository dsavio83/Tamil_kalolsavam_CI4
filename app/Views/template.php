<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php  echo (isset($title) && trim($title) != '')  ? $title   : 'Palakkad District Kalaivizha <span id="year-range"></span>'; ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="<?php echo base_url(); ?>assets/img/favicon.png" rel="icon">
  <link href="<?php echo base_url(); ?>assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  
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

    /* Responsive adjustments */
    @media (max-width: 576px) {
      #custom-alert,
      #custom-error-alert {
        width: 60%;
        margin-right: 0;
        border-radius: 0;
      }
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
  <script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>js/common.js"></script>
  <script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>js/prototype.js"></script>
  
  

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
<!-- ======= Header ======= -->
  

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


        <?php print $content ?>


</main>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="<?php echo base_url(); ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>


  <!-- Template Main JS File -->
  <script src="<?php echo base_url(); ?>assets/js/main.js"></script>
   <script src="<?php echo base_url(); ?>assets/js/tost.js"></script>

<script>
  function showAlert(message) {
    var customAlert = document.getElementById('custom-alert');
    var alertMessage = document.getElementById('alert-message');

    if (customAlert && alertMessage) {
      alertMessage.innerHTML = message;
      customAlert.style.opacity = '1';

      setTimeout(function () {
        customAlert.style.opacity = '0';
      }, 5000);
    }
  }

  function showErrorAlert(message) {
    var customErrorAlert = document.getElementById('custom-error-alert');
    var errorAlertMessage = document.getElementById('error-alert-message');

    if (customErrorAlert && errorAlertMessage) {
      errorAlertMessage.innerHTML = message;
      customErrorAlert.style.opacity = '1';

      setTimeout(function () {
        customErrorAlert.style.opacity = '0';
      }, 5000);
    }
  }

  function showSuccessAlert(message) {
    var customSuccessAlert = document.getElementById('custom-success-alert');
    var successAlertMessage = document.getElementById('success-alert-message');

    if (customSuccessAlert && successAlertMessage) {
        successAlertMessage.innerHTML = message;
        customSuccessAlert.style.opacity = '1';

        setTimeout(function () {
            customSuccessAlert.style.opacity = '0';
        }, 5000);
    }
}
  // Assuming $message and $error are PHP variables containing the messages
  showAlert("<?php echo isset($message) ? htmlspecialchars($message) : ''; ?>");
  showErrorAlert("<?php echo isset($error) ? htmlspecialchars($error) : ''; ?>");
  showSuccessAlert("<?php echo isset($success) ? htmlspecialchars($success) : ''; ?>");
  
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
