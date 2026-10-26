<div class="pagetitle mb-4">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
        <span class="rounded-3 bg-warning-subtle text-warning-emphasis p-2 d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 42px; height: 42px;">
          <i class="bi bi-trophy-fill fs-5"></i>
        </span>
        <span>District Dual Championship Leaderboard</span>
      </h1>
      <nav>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>welcome" class="text-decoration-none">Home</a></li>
          <li class="breadcrumb-item">District</li>
          <li class="breadcrumb-item active">Championship Board</li>
        </ol>
      </nav>
    </div>
    <div class="d-flex gap-2">
      <a href="<?php echo base_url(); ?>welcome" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 shadow-sm">
        <i class="bi bi-arrow-left"></i>
        <span>Dashboard</span>
      </a>
    </div>
  </div>
</div>

<!-- Nav Tabs for Dual Championship -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
  <div class="card-header bg-white border-0 pt-3 px-4 pb-0">
    <ul class="nav nav-tabs nav-tabs-bordered d-flex" id="championshipTabs" role="tablist">
      <li class="nav-item flex-fill" role="presentation">
        <button class="nav-link w-100 active fw-bold py-3 text-dark d-flex align-items-center justify-content-center gap-2" id="subdist-tab" data-bs-toggle="tab" data-bs-target="#subdist-trophy" type="button" role="tab">
          <i class="bi bi-award-fill text-warning fs-5"></i>
          <span>1. Sub-District Ever-Rolling Trophy</span>
        </button>
      </li>
      <li class="nav-item flex-fill" role="presentation">
        <button class="nav-link w-100 fw-bold py-3 text-dark d-flex align-items-center justify-content-center gap-2" id="school-tab" data-bs-toggle="tab" data-bs-target="#school-championship" type="button" role="tab">
          <i class="bi bi-building-fill text-primary fs-5"></i>
          <span>2. Overall School Championship</span>
        </button>
      </li>
    </ul>
  </div>

  <div class="card-body p-0">
    <div class="tab-content" id="championshipTabsContent">
      
      <!-- Tab 1: Sub-District Ever-Rolling Trophy -->
      <div class="tab-pane fade show active" id="subdist-trophy" role="tabpanel">
        <div class="p-4 bg-light border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div>
            <h5 class="fw-bold mb-0 text-dark">Inter-Subdistrict Overall Points Standings</h5>
            <small class="text-muted">Calculated from all confirmed, unwithheld District Kalolsavam results.</small>
          </div>
          <span class="badge bg-warning text-dark px-3 py-2 rounded-pill font-monospace fw-bold">
            <?php echo count($subdist_trophy ?? []); ?> Competing Sub-Districts
          </span>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-4" style="width: 70px;">Rank</th>
                <th>Sub-District Name</th>
                <th class="text-center">Schools</th>
                <th class="text-center">1st Ranks</th>
                <th class="text-center">2nd Ranks</th>
                <th class="text-center">3rd Ranks</th>
                <th class="text-center">Grade A</th>
                <th class="pe-4 text-end">Total Points</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($subdist_trophy)): ?>
                <?php foreach ($subdist_trophy as $idx => $st): ?>
                  <tr class="<?php echo ($idx === 0) ? 'table-warning fw-bold' : ''; ?>">
                    <td class="ps-4">
                      <?php if ($idx === 0): ?>
                        <span class="badge bg-warning text-dark rounded-circle p-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                          <i class="bi bi-trophy-fill"></i>
                        </span>
                      <?php elseif ($idx === 1): ?>
                        <span class="badge bg-secondary text-white rounded-circle p-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                      <?php elseif ($idx === 2): ?>
                        <span class="badge bg-secondary-subtle text-secondary rounded-circle p-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                      <?php else: ?>
                        <span class="text-muted ps-2"><?php echo $idx + 1; ?></span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="fw-bold text-dark fs-6"><?php echo esc($st['sub_district_name'] ?: 'Sub-District ' . $st['sub_district_code']); ?></div>
                      <small class="text-muted font-monospace">Code: <?php echo esc($st['sub_district_code']); ?></small>
                    </td>
                    <td class="text-center"><?php echo esc($st['total_schools']); ?></td>
                    <td class="text-center"><span class="badge bg-warning-subtle text-warning-emphasis rounded-pill"><?php echo esc($st['count_first']); ?></span></td>
                    <td class="text-center"><span class="badge bg-secondary-subtle text-secondary rounded-pill"><?php echo esc($st['count_second']); ?></span></td>
                    <td class="text-center"><span class="badge bg-light text-muted border rounded-pill"><?php echo esc($st['count_third']); ?></span></td>
                    <td class="text-center"><span class="badge bg-success-subtle text-success rounded-pill"><?php echo esc($st['count_a']); ?></span></td>
                    <td class="pe-4 text-end">
                      <span class="fs-5 fw-bold text-primary"><?php echo esc($st['total_points']); ?></span>
                      <small class="text-muted d-block">pts</small>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center py-5 text-muted">
                    <i class="bi bi-trophy fs-1 text-secondary opacity-50 d-block mb-2"></i>
                    <span>No results confirmed yet. Points will accumulate here once events are published.</span>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tab 2: School Championship -->
      <div class="tab-pane fade" id="school-championship" role="tabpanel">
        <div class="p-4 bg-light border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div>
            <h5 class="fw-bold mb-0 text-dark">School Standings Across Revenue District</h5>
            <small class="text-muted">Top participating schools by points earned in individual and group items.</small>
          </div>
          <span class="badge bg-primary text-white px-3 py-2 rounded-pill font-monospace fw-bold">
            <?php echo count($school_points ?? []); ?> Participating Schools
          </span>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-4" style="width: 70px;">Rank</th>
                <th>School Name &amp; Code</th>
                <th>Sub-District</th>
                <th class="text-center">1st Ranks</th>
                <th class="text-center">2nd Ranks</th>
                <th class="text-center">Grade A</th>
                <th class="text-center">Grade B</th>
                <th class="pe-4 text-end">Total Points</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($school_points)): ?>
                <?php foreach ($school_points as $idx => $sp): ?>
                  <tr class="<?php echo ($idx === 0) ? 'table-warning fw-bold' : ''; ?>">
                    <td class="ps-4">
                      <?php if ($idx === 0): ?>
                        <span class="badge bg-warning text-dark rounded-circle p-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                          <i class="bi bi-trophy-fill"></i>
                        </span>
                      <?php elseif ($idx === 1): ?>
                        <span class="badge bg-secondary text-white rounded-circle p-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                      <?php elseif ($idx === 2): ?>
                        <span class="badge bg-secondary-subtle text-secondary rounded-circle p-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                      <?php else: ?>
                        <span class="text-muted ps-2"><?php echo $idx + 1; ?></span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="fw-bold text-dark"><?php echo esc($sp['school_name'] ?: 'School ' . $sp['school_code']); ?></div>
                      <small class="text-muted font-monospace">Code: <?php echo esc($sp['school_code']); ?></small>
                    </td>
                    <td>
                      <span class="badge bg-light text-dark border rounded-pill"><?php echo esc($sp['sub_district_name']); ?></span>
                    </td>
                    <td class="text-center"><span class="badge bg-warning-subtle text-warning-emphasis rounded-pill"><?php echo esc($sp['count_first']); ?></span></td>
                    <td class="text-center"><span class="badge bg-secondary-subtle text-secondary rounded-pill"><?php echo esc($sp['count_second']); ?></span></td>
                    <td class="text-center"><span class="badge bg-success-subtle text-success rounded-pill"><?php echo esc($sp['count_a']); ?></span></td>
                    <td class="text-center"><span class="badge bg-primary-subtle text-primary rounded-pill"><?php echo esc($sp['count_b']); ?></span></td>
                    <td class="pe-4 text-end">
                      <span class="fs-5 fw-bold text-primary"><?php echo esc($sp['total_points']); ?></span>
                      <small class="text-muted d-block">pts</small>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center py-5 text-muted">
                    <i class="bi bi-building fs-1 text-secondary opacity-50 d-block mb-2"></i>
                    <span>No school points recorded yet.</span>
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
