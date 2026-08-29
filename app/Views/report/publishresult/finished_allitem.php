<!doctype html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title> Finished Items</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
      <style>
         html,
         body,
         .intro {
         height: 100%;
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         margin: 0;
         padding: 0;
         }
         /* Hide scrollbars */
         .table-scroll::-webkit-scrollbar {
         display: none;
         }
         .table-scroll {
         -ms-overflow-style: none; /* IE and Edge */
         scrollbar-width: none; /* Firefox */
         }
         table td,
         table th {
         text-overflow: ellipsis;
         white-space: nowrap;
         overflow: hidden;
         }
         thead th {
         color: #fff;
         }
         .card {
         border-radius: 1rem;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
         border: none;
         }
         .table-scroll {
         border-radius: 1rem;
         }
         .table-scroll table thead th {
         font-size: 1.25rem;
         }
         thead {
         top: 0;
         position: sticky;
         }
         .table-hover tbody tr:hover {
         background-color: rgba(102, 126, 234, 0.1);
         transform: scale(1.01);
         transition: all 0.3s ease;
         }
         .table-striped tbody tr:nth-of-type(odd) {
         background-color: rgba(255, 255, 255, 0.1);
         }
         .text-info {
         color: #667eea !important;
         font-weight: bold;
         }
         .pagetitle h1 {
         color: #fff;
         }
         .breadcrumb {
         background: rgba(255, 255, 255, 0.1);
         border-radius: 0.5rem;
         }
         .breadcrumb-item a {
         color: #fff;
         }
         .breadcrumb-item.active {
         color: #ffd700;
         }
      </style>
   </head>
   <body>
      <div class="container">
         <div class="pagetitle">
            <h1>Finished Items Report</h1>
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" title="Home">Home</a></li>
                <li class="breadcrumb-item active">Finished Items</li>
              </ol>
            </nav>
         </div>
         <h1 class="text-info mt-4 mb-3">
            <center> Finished Festival Items List</center>
         </h1>
         <section class="intro">
            <div class="bg-image h-100" style="background-color: #f5f7fa;">
               <div class="mask d-flex align-items-center h-100">
                  <div class="container">
                     <div class="row justify-content-center">
                        <div class="col-12">
                           <div class="card">
                              <div class="card-body">
                                 <div class="table-responsive table-scroll" data-mdb-perfect-scrollbar="true" style="position: relative; height: calc(100vh - 300px); overflow-y: auto;">
                                    <table class="table table-striped table-hover">
                                       <thead style="background-color:#002d72;">
                                          <tr class="text-center">
                                             <th>
                                                <span class="text-danger"> Finished Items</span>
                                             </th>
                                          </tr>
                                          <tr class="text-center">
                                             <th scope="col">Items Name</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          <?php
                                             foreach ($totitem as $value) {
                                                 ?>
                                          <tr>
                                             <td><?php echo $value['item_code'] . '-' . $value['item_name']; ?> </td>
                                          </tr>
                                          <?php
                                             }
                                             ?>
                                    </table>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </section>
      </div>
   </body>
</html>
