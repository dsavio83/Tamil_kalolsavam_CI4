<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Festival Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <style>
      html,
body,
.intro {
  height: 100%;
  background-color: #f5f7fa;
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
  border-radius: .5rem;
}

.table-scroll {
  border-radius: .5rem;
}

.table-scroll table thead th {
  font-size: 1.25rem;
}
thead {
  top: 0;
  position: sticky;
}
  </style>
  </head>
  <body>
<div class="container">
<h1 class="text-info mt-4 mb-3"><center> All Festival List</center></h1>
<section class="intro">
  <div class="bg-image h-100" style="background-color: #f5f7fa;">
    <div class="mask d-flex align-items-center h-100">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <div class="table-responsive table-scroll" data-mdb-perfect-scrollbar="true" style="position: relative; height: 400px">
                  <table class="table table-striped">
                    <thead style="background-color:#002d72;">
                        <tr class="text-center">
                            <th colspan="7">
                                <span class="text-danger"><?php echo $totitem[0]['fest_name']; ?> </span>
                            </th>
                        </tr>
                      <tr class="text-center">
                        <th scope="col">Item</th>
                        <th scope="col">Stage No.</th>
                        <th scope="col">Cluster</th>
                        <th scope="col">Participants</th>
                        <th scope="col">Item Type</th>
                        <th scope="col">Maximum Time</th>
                        <th scope="col">Date of Item</th>
                    </tr>
                    </thead>
                    <tbody>
        <?php
        $bg = 0;
        foreach ($totitem as $value) {
            if ($value['item_type'] == 'S')
                $item = 'Single';
            else
                $item = 'Group';

            if ($bg == 0) {
                $bgcolor = "#fff";
                $bg = 1;
            } else {
                $bgcolor = "#fff";
                $bg = 0;
            }
            ?>
            <tr>
                <td><?php echo $value['item_code'] . ' - ' . $value['item_name']; ?> </td>
                <td class="text-center"><?php echo $value['stage_name']; ?></td>
                <td class="text-center"><?php echo $value['no_of_cluster']; ?></td>
                <td class="text-center"><?php echo $value['no_of_participant']; ?></td>
                <td class="text-center"><?php echo $item; ?></td>
                <td class="text-center"><?php echo $value['max_time'] . ' ' . $value['time_type']; ?></td>
                <td class="text-center"><?php echo datetophpmodel($value['ddt']); ?></td>
            </tr>
            <?php
        }
        ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
