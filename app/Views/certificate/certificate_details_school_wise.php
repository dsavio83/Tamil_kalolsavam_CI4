<div class="pagetitle d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h3 mb-1 text-gray-800 fw-bold d-flex align-items-center gap-2">
      <i class="bi bi-building text-primary"></i> Certificate - School Wise
    </h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome">Home</a></li>
        <li class="breadcrumb-item">Certificate</li>
        <li class="breadcrumb-item active">School Wise</li>
      </ol>
    </nav>
  </div>
  <div class="d-flex align-items-center gap-2">
    <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill d-flex align-items-center gap-2" onclick="window.print();">
      <i class="bi bi-printer-fill"></i> Print Table
    </button>
  </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm rounded-3 mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 fs-5"></i>
    <div><?php echo session()->getFlashdata('error'); ?></div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php echo form_open('', array('id' => 'formIWPq')); ?>
<input type="hidden" name="hidSchoolCode" id="hidSchoolCode" value="" />

<div class="row justify-content-center">
  <div class="col-12">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
      <div class="card-header bg-white py-3 px-4 border-bottom border-light d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-circle bg-primary-subtle p-2 text-primary d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi bi-houses-fill fs-5"></i>
          </div>
          <div>
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
              <span>Participating Schools</span>
              <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fs-7">
                <?php echo count($school_details); ?> Schools
              </span>
            </h5>
            <small class="text-muted">Select a school to print certificates by festival, item, or group</small>
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="schoolCertTable" style="font-size: 13.5px;">
            <thead class="table-light text-secondary border-bottom">
              <tr>
                <th class="py-3 px-3 text-center" style="width: 60px;">#</th>
                <th class="py-3 px-3">School Name & Code</th>
                <th class="py-3 px-3 text-center" style="width: 120px;">Participation</th>
                <th class="py-3 px-3 text-center" style="width: 80px;"><span class="badge bg-success-subtle text-success px-2 py-1">A Grade</span></th>
                <th class="py-3 px-3 text-center" style="width: 80px;"><span class="badge bg-info-subtle text-info px-2 py-1">B Grade</span></th>
                <th class="py-3 px-3 text-center" style="width: 80px;"><span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1">C Grade</span></th>
                <th class="py-3 px-3 text-center" style="width: 130px;">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($school_details) && is_array($school_details)): ?>
                <?php $sno = 0; foreach ($school_details as $row): $sno++; ?>
                <tr>
                  <td class="text-center fw-semibold text-muted py-3 px-3"><?php echo $sno; ?></td>
                  <td class="py-3 px-3">
                    <div class="d-flex align-items-center gap-2">
                      <span class="badge bg-light text-dark border px-2 py-1 font-monospace fw-bold">
                        <?php echo $row['school_code']; ?>
                      </span>
                      <a href="javascript:void(0)" onclick="getSchoolWiseCertificate('<?php echo $row['school_code']; ?>')" class="fw-semibold text-decoration-none text-primary link-opacity-75-hover">
                        <?php echo htmlspecialchars($row['school_name']); ?>
                      </a>
                    </div>
                  </td>
                  <td class="text-center fw-semibold text-dark py-3 px-3">
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
                      <?php echo (int)$row['no_participation']; ?> Students
                    </span>
                  </td>
                  <td class="text-center fw-bold text-success py-3 px-3"><?php echo (int)($row['a_grade'] ?? 0); ?></td>
                  <td class="text-center fw-bold text-info py-3 px-3"><?php echo (int)($row['b_grade'] ?? 0); ?></td>
                  <td class="text-center fw-bold text-warning-emphasis py-3 px-3"><?php echo (int)($row['c_grade'] ?? 0); ?></td>
                  <td class="text-center py-3 px-3">
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1 shadow-sm" onclick="getSchoolWiseCertificate('<?php echo $row['school_code']; ?>')">
                      <span>View</span> <i class="bi bi-chevron-right fs-8"></i>
                    </button>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                    No schools found with published results for certificate generation.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php echo form_close(); ?>

<script>
function getSchoolWiseCertificate(schoolCode) {
  var hid = document.getElementById('hidSchoolCode');
  var form = document.getElementById('formIWPq');
  if (hid && form) {
    hid.value = schoolCode;
    form.action = '<?php echo base_url("certificate/certificate/get_certificate_school_wise"); ?>';
    form.submit();
  }
}
</script>
