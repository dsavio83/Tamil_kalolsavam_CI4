<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result Declared - Tamil Kalaivizha</title>
    <link rel="shortcut icon" type="image/x-icon" href="https://photos.app.goo.gl/4Tv9UCTHxEAN44Pm7">
    <link rel="mask-icon" href="https://photos.app.goo.gl/4Tv9UCTHxEAN44Pm7" color="#111">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://s3-us-west-2.amazonaws.com/s.cdpn.io/4579/bootstrap-table.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .result-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .result-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
            font-size: 1rem;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .photo-cell img {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .back-btn {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            border: none;
            border-radius: 8px;
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .back-btn:hover {
            color: white;
            background: linear-gradient(135deg, #5a6268 0%, #545b62 100%);
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .no-results i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .result-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .result-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .result-photo {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }

        .card-text {
            font-size: 0.9rem;
            color: #666;
			line-height: 22px;
        }

        .card-text strong {
            color: #333;
        }

        @media (max-width: 768px) {
            .result-card {
                margin-bottom: 1rem;
            }

            .result-photo {
                width: 60px;
                height: 60px;
            }

            .card-title {
                font-size: 1rem;
            }

            .card-text {
                font-size: 0.8rem;
            }
        }

        .animate__bounceIn {
            animation: bounceIn 1s ease-in-out;
        }

        .animate__fadeInUp {
            animation: fadeInUp 0.6s ease-in-out;
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 40px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .rank-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .rank-1 {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        }

        .rank-2 {
            background: linear-gradient(135deg, #C0C0C0 0%, #A8A8A8 100%);
        }

        .rank-3 {
            background: linear-gradient(135deg, #CD7F32 0%, #A0522D 100%);
        }

        .eligible-message {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            margin-top: 0.5rem;
            text-align: center;
            font-weight: 600;
        }

        /* Mobile Card Layout */
        .mobile-cards {
            display: block;
        }

        /* Desktop Table Layout */
        .desktop-table {
            display: none;
        }

        @media (min-width: 992px) {
            .mobile-cards {
                display: none;
            }

            .desktop-table {
                display: block;
            }
        }

        .index-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .table-responsive {
            border-radius: 15px;
            overflow: hidden;
        }

        .table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
            font-size: 1rem;
            padding: 1rem;
            text-align: center;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            text-align: center;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .eligible-text {
            font-size: 0.8rem;
            color: #28a745;
            font-weight: 600;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="result-header">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="result-title">
                        <i class="fas fa-trophy me-3"></i>
                        Competition Results
                    </h1>
                    <!-- <p class="lead">Tamil Kalaivizha - <?php echo $details[0]['fest_name']; ?></p> -->
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <a href="<?php echo base_url(); ?>publishresult/publicresult/resultview" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Back to Results
        </a>

        <!-- Item Selection Dropdown -->
        <div class="mb-4">
            <label for="itemSelect" class="form-label">Select Item:</label>
            <select class="form-select" id="itemSelect" name="itemSelect">
                <option value="">-- Select an Item --</option>
                <?php foreach ($items as $item) { ?>
                    <option value="<?php echo $item['item_code']; ?>"><?php echo $item['item_name']; ?></option>
                <?php } ?>
            </select>
        </div>

        <!-- Results Container -->
        <div id="results-container">
            <div class="no-results">
                <i class="fas fa-info-circle text-muted"></i>
                <h4>Select an Item</h4>
                <p class="text-muted">Please select an item from the dropdown to view the results.</p>
            </div>
        </div>

        <!-- Cup Icon Animation -->
        <div id="cup-animation" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
            <i class="fas fa-trophy text-warning" style="font-size: 5rem;"></i>
        </div>
   </div>

   <!-- Bootstrap JS -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
   <!-- jQuery -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
   <!-- Confetti JS -->
   <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>

   <script>
       $(document).ready(function() {
           $('#itemSelect').change(function() {
               var itemid = $(this).val();
               var festid = '<?php echo $festid; ?>';
               if (itemid) {
                   // Show cup animation
                   $('#cup-animation').show().addClass('animate__animated animate__bounceIn');
                   setTimeout(function() {
                       $('#cup-animation').hide();
                   }, 2000);

                   $.ajax({
                       url: '<?php echo base_url(); ?>publishresult/publicresult/get_results_by_item',
                       type: 'POST',
                       data: { festid: festid, itemid: itemid },
                       success: function(response) {
                           $('#results-container').html(response);
                           // Add blast animation
                           $('#results-container').addClass('animate__animated animate__fadeInUp');
                           // Trigger confetti
                           confetti({
                               particleCount: 100,
                               spread: 70,
                               origin: { y: 0.6 }
                           });
                           // Update title with selected item
                           var selectedItem = $('#itemSelect option:selected').text();
                           $('.result-title').html('<i class="fas fa-trophy me-3 animate__animated animate__bounceIn"></i>Competition Results - ' + selectedItem);
                       },
                       error: function() {
                           $('#results-container').html('<div class="no-results"><i class="fas fa-exclamation-triangle text-danger"></i><h4>Error</h4><p class="text-muted">Failed to load results. Please try again.</p></div>');
                       }
                   });
               } else {
                   $('#results-container').html('<div class="no-results"><i class="fas fa-info-circle text-muted"></i><h4>Select an Item</h4><p class="text-muted">Please select an item from the dropdown to view the results.</p></div>');
               }
           });
       });
   </script>
</body>
 
</html>

