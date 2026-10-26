<?php
echo form_open('', array('id' => 'formIWPq'));
?>
<input type="hidden" name="hidItemId" id="hidItemId" value="" />

<?php
// Compute summary statistics
$all_items = array_merge($single ?? [], $itempart ?? []);
$total_items_count = count($all_items);
$confirmed_count = 0;
$entered_count = 0;
$pending_count = 0;

foreach ($all_items as $item) {
    if (($item['is_confirmed'] ?? '') === 'Yes') {
        $confirmed_count++;
    }
    if ((int)($item['participated_no'] ?? 0) > 0) {
        $entered_count++;
    } else {
        $pending_count++;
    }
}
$fest_display_name = $itempart[0]['fest_name'] ?? ($single[0]['fest_name'] ?? 'Festival Items');
?>

<!-- Results Summary Table Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
  <div class="card-header text-white py-3 px-4 border-0 rounded-top-4 d-flex align-items-center justify-content-between" style="background-color: #1e293b;">
    <div class="d-flex align-items-center gap-3">
      <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: rgba(255, 255, 255, 0.2);">
        <i class="bi bi-card-checklist text-white fs-5"></i>
      </div>
      <div>
        <h5 class="card-title mb-0 text-white fw-bold fs-6"><?php echo htmlspecialchars($fest_display_name); ?></h5>
        <small class="text-white-50" style="font-size: 0.8rem;">Status of result entry and confirmation across all events</small>
      </div>
    </div>
    <button type="button" class="btn btn-light rounded-pill px-3 py-1.5 fw-bold btn-sm shadow-none d-flex align-items-center gap-1.5" onClick="javascript:printContent('print_content'); return false;">
      <i class="bi bi-printer text-dark"></i>
      <span class="text-dark">Print Status</span>
    </button>
  </div>

  <!-- KPI Quick Stats Row -->
  <div class="p-3 bg-light border-bottom border-light-subtle">
    <div class="row g-2">
      <div class="col-6 col-md-3">
        <div class="p-2 px-3 bg-white rounded-3 border text-center shadow-none">
          <small class="text-muted d-block" style="font-size: 0.72rem;">Total Events</small>
          <strong class="text-dark fs-6"><?php echo $total_items_count; ?></strong>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="p-2 px-3 bg-white rounded-3 border text-center shadow-none">
          <small class="text-muted d-block" style="font-size: 0.72rem;">Confirmed</small>
          <strong class="text-success fs-6"><?php echo $confirmed_count; ?></strong>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="p-2 px-3 bg-white rounded-3 border text-center shadow-none">
          <small class="text-muted d-block" style="font-size: 0.72rem;">Scores Entered</small>
          <strong class="text-primary fs-6"><?php echo $entered_count; ?></strong>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="p-2 px-3 bg-white rounded-3 border text-center shadow-none">
          <small class="text-muted d-block" style="font-size: 0.72rem;">Pending</small>
          <strong class="text-warning fs-6"><?php echo $pending_count; ?></strong>
        </div>
      </div>
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table id="paginationNumbers" class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
        <thead class="table-light text-secondary border-bottom border-light-subtle">
          <tr>
            <th class="ps-4 py-3 text-start">Event Code & Name</th>
            <th class="py-3 text-center" width="100">Type</th>
            <th class="py-3 text-center" width="110">Registered</th>
            <th class="py-3 text-center" width="100">Entered</th>
            <th class="py-3 text-center" width="100">Pending</th>
            <th class="py-3 text-center" width="120">Status</th>
            <?php if ($this->session->get('USER_GROUP') == 'A' || $this->session->get('USER_GROUP') == 'W') { ?>
              <th class="pe-4 py-3 text-center" width="120">Reset</th>
            <?php } ?>
          </tr>
        </thead>
        <tbody>
          <?php
          // Helper renderer for items
          $render_rows = function($items, $prefix) {
              $isAdmin = ($this->session->get('USER_GROUP') == 'A' || $this->session->get('USER_GROUP') == 'W');
              for ($j = 0; $j < count($items); $j++) {
                  $item = $items[$j];
                  $typeLabel = ($item['item_type'] == 'G' || $item['item_type'] == 'g') ? 'Group' : 'Single';
                  $typeBadgeClass = ($typeLabel == 'Group') ? 'bg-secondary' : 'bg-primary';
                  $pendingCount = (int)$item['cpt'] - (int)$item['participated_no'];
                  $isConfirmed = ($item['is_confirmed'] == 'Yes');
                  $elementId = "result_confirm_{$prefix}{$j}";
                  ?>
                  <tr>
                    <td class="ps-4 py-2.5">
                      <a href="<?php echo base_url('result/resultentry/index/' . $item['item_code']); ?>" class="text-decoration-none fw-bold text-dark d-inline-flex align-items-center gap-1.5 item-link">
                        <span class="badge bg-light text-dark border font-monospace"><?php echo htmlspecialchars((string)$item['item_code']); ?></span>
                        <span class="text-primary"><?php echo htmlspecialchars($item['item_name']); ?></span>
                        <i class="bi bi-arrow-right-short text-muted fs-6"></i>
                      </a>
                    </td>
                    <td class="text-center">
                      <span class="badge <?php echo $typeBadgeClass; ?> bg-opacity-75 rounded-pill px-2.5 py-1"><?php echo $typeLabel; ?></span>
                    </td>
                    <td class="text-center fw-bold text-secondary"><?php echo (int) $item['cpt']; ?></td>
                    <td class="text-center fw-bold text-success"><?php echo (int) $item['participated_no']; ?></td>
                    <td class="text-center">
                      <?php if ($pendingCount > 0) { ?>
                        <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1"><?php echo $pendingCount; ?></span>
                      <?php } else { ?>
                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5"><i class="bi bi-check-lg text-success"></i> 0</span>
                      <?php } ?>
                    </td>
                    <td id="<?php echo $elementId; ?>_dis" class="text-center">
                      <?php if ($isConfirmed) { ?>
                        <span class="badge bg-success rounded-pill px-2.5 py-1"><i class="bi bi-check-circle-fill me-1"></i>Confirmed</span>
                      <?php } else { ?>
                        <span class="badge bg-secondary bg-opacity-75 rounded-pill px-2.5 py-1">Pending</span>
                      <?php } ?>
                    </td>
                    <?php if ($isAdmin) { ?>
                      <td id="<?php echo $elementId; ?>" class="pe-4 text-center">
                        <?php if ($isConfirmed) { ?>
                          <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-0.5 fw-semibold" style="font-size: 0.78rem;" onClick="javascript:resetResultConfirmation('<?php echo $item['item_code']; ?>', '<?php echo htmlspecialchars($item['item_code'] . ' - ' . $item['item_name'], ENT_QUOTES); ?>', '<?php echo $elementId; ?>');">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                          </button>
                        <?php } else { ?>
                          <span class="text-muted">-</span>
                        <?php } ?>
                      </td>
                    <?php } ?>
                  </tr>
                  <?php
              }
          };

          if (!empty($single)) {
              $render_rows($single, 'single');
          }
          if (!empty($itempart)) {
              $render_rows($itempart, 'group');
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Hidden Print Container -->
<div id="print_content" class="d-none">
  <div class="p-4">
    <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($fest_display_name); ?> - Item Results Status</h4>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Item Code & Name</th>
          <th>Type</th>
          <th>No of Students</th>
          <th>Result Entered</th>
          <th>Result Not Entered</th>
          <th>Confirmed</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($all_items as $item) {
            $typeLabel = ($item['item_type'] == 'G' || $item['item_type'] == 'g') ? 'Group' : 'Single';
            $pendingCount = (int)$item['cpt'] - (int)$item['participated_no'];
        ?>
          <tr>
            <td><?php echo htmlspecialchars($item['item_code'] . ' - ' . $item['item_name']); ?></td>
            <td><?php echo $typeLabel; ?></td>
            <td><?php echo (int) $item['cpt']; ?></td>
            <td><?php echo (int) $item['participated_no']; ?></td>
            <td><?php echo $pendingCount; ?></td>
            <td><?php echo htmlspecialchars($item['is_confirmed']); ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Animate.css for SweetAlert animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<script>
function resetResultConfirmation(itemcode, message, element_id) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Reset Confirmation?',
            html: 'Are you sure you want to reset confirmation for <br><span class="badge bg-primary fs-6 mt-2 py-2 px-3">' + message + '</span>?<br><br><small class="text-muted">This will reopen result entry and mark edits for this event.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-arrow-counterclockwise me-1"></i> Yes, Reset Confirmation',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            showClass: {
                popup: 'animate__animated animate__bounceIn'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp animate__faster'
            },
            customClass: {
                popup: 'rounded-4 shadow-lg border-0 p-4',
                confirmButton: 'btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm',
                cancelButton: 'btn btn-light rounded-pill px-4 py-2 fw-semibold text-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Resetting Status...',
                    text: 'Please wait while confirmation status is being reset.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                var formData = new FormData();
                formData.append('item_code', itemcode);

                fetch(path + "ajax/loadajax/reset_result_confirmation_status", {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === 'No') {
                        var btnEl = document.getElementById(element_id);
                        var disEl = document.getElementById(element_id + '_dis');
                        if (btnEl) btnEl.innerHTML = '<span class="text-muted">-</span>';
                        if (disEl) disEl.innerHTML = '<span class="badge bg-secondary bg-opacity-75 rounded-pill px-2.5 py-1">Pending</span>';

                        Swal.fire({
                            title: 'Reset Completed!',
                            html: 'Result confirmation for <strong>' + message + '</strong> has been reset successfully.',
                            icon: 'success',
                            timer: 2200,
                            showConfirmButton: false,
                            showClass: {
                                popup: 'animate__animated animate__zoomIn'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__zoomOut animate__faster'
                            },
                            customClass: {
                                popup: 'rounded-4 shadow-lg border-0'
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'Unable to reset confirmation status. Please try again.',
                            icon: 'error',
                            customClass: {
                                popup: 'rounded-4 shadow-lg border-0'
                            }
                        });
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while connecting to the server.',
                        icon: 'error',
                        customClass: {
                            popup: 'rounded-4 shadow-lg border-0'
                        }
                    });
                });
            }
        });
    } else {
        if (confirm('Do you really want to reset the Result confirmation status of ' + message + '?')) {
            var formData = new FormData();
            formData.append('item_code', itemcode);
            fetch(path + "ajax/loadajax/reset_result_confirmation_status", {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                var btnEl = document.getElementById(element_id);
                var disEl = document.getElementById(element_id + '_dis');
                if (btnEl) btnEl.innerHTML = '<span class="text-muted">-</span>';
                if (disEl) disEl.innerHTML = '<span class="badge bg-secondary bg-opacity-75 rounded-pill px-2.5 py-1">Pending</span>';
            });
        }
    }
}
</script>

<?php
echo form_close();
?>