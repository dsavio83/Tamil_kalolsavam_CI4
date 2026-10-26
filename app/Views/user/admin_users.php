<?php
$states = $states ?? [];
$user_types = $user_types ?? [];
$sub_districts = $sub_districts ?? [];
$schools = $schools ?? [];
$retvalue = $retvalue ?? [];
$global_stats = $global_stats ?? [];

// KPI Metrics from Global Stats
$total_count = $global_stats['total'] ?? count($retvalue);
$state_count = $global_stats['state'] ?? 0;
$dist_count = $global_stats['district'] ?? 0;
$subdist_count = $global_stats['subdistrict'] ?? 0;
?>

<style>
/* Modern SaaS Design System for Admin Users */
:root {
  --admin-primary: #2563eb;
  --admin-primary-dark: #1d4ed8;
  --admin-bg-light: #f8fafc;
  --admin-border-color: #e2e8f0;
}

.hero-header-card {
  background: #ffffff;
  border: 1px solid var(--admin-border-color);
  border-radius: 16px;
  box-shadow: 0 4px 15px -3px rgba(15, 23, 42, 0.05);
  padding: 1.25rem 1.5rem;
}

/* Interactive KPI Cards (Borderless Modern Style) */
.kpi-tile {
  background: #ffffff;
  border: none !important;
  border-radius: 14px;
  padding: 1rem 1.15rem;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  cursor: pointer;
  position: relative;
  overflow: hidden;
  user-select: none;
  box-shadow: 0 2px 10px -2px rgba(15, 23, 42, 0.06);
}
.kpi-tile:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 22px -4px rgba(15, 23, 42, 0.1);
  border: none !important;
}
.kpi-tile.active-kpi {
  border: none !important;
  box-shadow: 0 4px 14px -2px rgba(15, 23, 42, 0.12) !important;
  background-color: #f8fafc;
}
.kpi-tile::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  transition: height 0.2s ease;
}
.kpi-tile.active-kpi::before {
  height: 4px;
}
.kpi-all {
  border: none !important;
}
.kpi-all::before { background: linear-gradient(90deg, #475569, #334155); }
.kpi-state::before { background: linear-gradient(90deg, #f43f5e, #e11d48); }
.kpi-district::before { background: linear-gradient(90deg, #10b981, #059669); }
.kpi-subdist::before { background: linear-gradient(90deg, #0ea5e9, #0284c7); }

.kpi-icon-box {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* Enterprise Table Container */
.roster-card {
  background: #ffffff;
  border: 1px solid var(--admin-border-color);
  border-radius: 16px;
  box-shadow: 0 4px 20px -3px rgba(15, 23, 42, 0.05);
  overflow: hidden;
}

.roster-card .card-header {
  background: #ffffff;
  border-bottom: 1px solid #f1f5f9;
  padding: 1.1rem 1.35rem;
}

.roster-card .table thead th {
  background-color: #f8fafc;
  color: #475569;
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  border-bottom: 1px solid #e2e8f0;
  padding: 11px 16px;
}

.roster-card .table tbody td {
  padding: 13px 16px;
  border-bottom: 1px solid #f1f5f9;
}

.roster-card .table tbody tr {
  transition: background-color 0.15s ease;
}
.roster-card .table tbody tr:hover {
  background-color: #f8fafc;
}

/* Role Pills */
.role-badge {
  font-size: 0.76rem;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}
.role-badge-state { background-color: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }
.role-badge-district { background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
.role-badge-subdist { background-color: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; }
.role-badge-school { background-color: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe; }

/* User Avatar */
.avatar-badge {
  width: 38px;
  height: 38px;
  min-width: 38px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
}

/* Credential Snippet */
.credential-tag {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 2px 7px;
  font-family: var(--bs-font-monospace);
  font-size: 0.73rem;
  color: #334155;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

/* Action Buttons */
.btn-action-edit {
  color: #0284c7;
  background-color: #f0f9ff;
  border: 1px solid #bae6fd;
  border-radius: 20px;
  padding: 4px 11px;
  font-size: 0.78rem;
  font-weight: 600;
  transition: all 0.15s ease;
}
.btn-action-edit:hover {
  color: #ffffff;
  background-color: #0284c7;
  border-color: #0284c7;
}

.btn-action-delete {
  color: #e11d48;
  background-color: #fff1f2;
  border: 1px solid #fecdd3;
  border-radius: 20px;
  padding: 4px 9px;
  font-size: 0.78rem;
  transition: all 0.15s ease;
}
.btn-action-delete:hover {
  color: #ffffff;
  background-color: #e11d48;
  border-color: #e11d48;
}

/* Segmented Control Filter Tabs */
.segment-tabs {
  background: #f1f5f9;
  padding: 3px;
  border-radius: 10px;
  display: inline-flex;
  gap: 2px;
}
.segment-btn {
  border: none;
  background: transparent;
  padding: 5px 12px;
  font-size: 0.76rem;
  font-weight: 600;
  color: #64748b;
  border-radius: 8px;
  transition: all 0.18s ease;
  user-select: none;
}
.segment-btn:hover {
  color: #1e293b;
}
.segment-btn.active {
  background: #ffffff;
  color: #0f172a;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
}
</style>

<div class="container-fluid px-0">

  <!-- 1. Hero Header & Global Actions Bar -->
  <div class="hero-header-card mb-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; background: linear-gradient(135deg, #2563eb, #1e40af);">
          <i class="bi bi-shield-lock-fill fs-4"></i>
        </div>
        <div>
          <div class="d-flex align-items-center gap-2 mb-0.5">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5 text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">
              System Security
            </span>
            <span class="text-muted small">&bull;</span>
            <span class="text-muted small" style="font-size: 0.76rem;">Privilege Management</span>
          </div>
          <h4 class="fw-bold text-dark mb-0" style="font-size: 1.35rem; letter-spacing: -0.3px;">
            Admin Users &amp; Access Control
          </h4>
          <p class="text-muted small mb-0 mt-0.5" style="font-size: 0.8rem;">
            Manage administrative credentials, role hierarchies, and territorial authentication scopes.
          </p>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="d-flex align-items-center gap-2">
        <!-- PDF Export -->
        <button type="button" class="btn btn-outline-danger rounded-pill px-3 py-2 shadow-xs fw-semibold d-inline-flex align-items-center gap-1.5" onclick="generate_sub_dist_admin_pdf();" title="Export Administrative Directory as PDF">
          <i class="bi bi-file-earmark-pdf-fill"></i>
          <span>Generate PDF</span>
        </button>

        <!-- Add User Modal Trigger -->
        <button type="button" class="btn btn-primary rounded-pill px-3.5 py-2 shadow-sm fw-semibold d-inline-flex align-items-center gap-2" onclick="openAddUserModal();" title="Add a New Admin User">
          <i class="bi bi-person-plus-fill fs-6"></i>
          <span>Add New Admin</span>
        </button>
      </div>
    </div>
  </div>

  <!-- 2. Interactive KPI Stat Cards (Doubles as 1-Click Role Filters) -->
  <div class="row g-2.5 mb-3">
    <!-- All Accounts -->
    <div class="col-6 col-md-3">
      <div class="kpi-tile kpi-all active-kpi" onclick="filterByRoleBadge(0, this);" title="Show All Admin Accounts">
        <div class="d-flex align-items-center gap-3">
          <div class="kpi-icon-box bg-secondary-subtle text-secondary">
            <i class="bi bi-people-fill fs-5"></i>
          </div>
          <div class="overflow-hidden">
            <div class="text-muted fw-bold text-truncate" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">All Admins</div>
            <div class="fw-bolder text-dark lh-1 mt-1" style="font-size: 1.35rem;"><?php echo $total_count; ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- State Admins -->
    <div class="col-6 col-md-3">
      <div class="kpi-tile kpi-state" onclick="filterByRoleBadge(1, this);" title="Filter State Admins">
        <div class="d-flex align-items-center gap-3">
          <div class="kpi-icon-box bg-danger-subtle text-danger">
            <i class="bi bi-shield-shaded fs-5"></i>
          </div>
          <div class="overflow-hidden">
            <div class="text-muted fw-bold text-truncate" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">State Admins</div>
            <div class="fw-bolder text-danger lh-1 mt-1" style="font-size: 1.35rem;"><?php echo $state_count; ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- District Admins -->
    <div class="col-6 col-md-3">
      <div class="kpi-tile kpi-district" onclick="filterByRoleBadge(2, this);" title="Filter District Admins">
        <div class="d-flex align-items-center gap-3">
          <div class="kpi-icon-box bg-success-subtle text-success">
            <i class="bi bi-buildings-fill fs-5"></i>
          </div>
          <div class="overflow-hidden">
            <div class="text-muted fw-bold text-truncate" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">District Admins</div>
            <div class="fw-bolder text-success lh-1 mt-1" style="font-size: 1.35rem;"><?php echo $dist_count; ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sub-District Admins -->
    <div class="col-6 col-md-3">
      <div class="kpi-tile kpi-subdist" onclick="filterByRoleBadge(3, this);" title="Filter Sub-District Admins">
        <div class="d-flex align-items-center gap-3">
          <div class="kpi-icon-box bg-primary-subtle text-primary">
            <i class="bi bi-pin-map-fill fs-5"></i>
          </div>
          <div class="overflow-hidden">
            <div class="text-muted fw-bold text-truncate" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">Sub-Dist Admins</div>
            <div class="fw-bolder text-primary lh-1 mt-1" style="font-size: 1.35rem;"><?php echo $subdist_count; ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 3. Registered Accounts Table Card -->
  <div class="roster-card mb-4">
    <!-- Card Header with Segmented Filter Tabs & Instant Search -->
    <div class="card-header">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5">
        
        <!-- Left: Title & Segmented Quick Tabs -->
        <div class="d-flex align-items-center flex-wrap gap-2.5">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-person-lines-fill text-primary fs-5"></i>
            <span class="fw-bold text-dark" style="font-size: 0.95rem;">Administrative Directory</span>
          </div>

          <!-- Segment Tabs -->
          <div class="segment-tabs d-none d-lg-inline-flex" id="roleSegmentTabs">
            <button type="button" class="segment-btn active" data-role="0" onclick="filterByRoleSegment(0, this)">All (<?php echo $total_count; ?>)</button>
            <button type="button" class="segment-btn" data-role="1" onclick="filterByRoleSegment(1, this)">State (<?php echo $state_count; ?>)</button>
            <button type="button" class="segment-btn" data-role="2" onclick="filterByRoleSegment(2, this)">District (<?php echo $dist_count; ?>)</button>
            <button type="button" class="segment-btn" data-role="3" onclick="filterByRoleSegment(3, this)">Sub-District (<?php echo $subdist_count; ?>)</button>
          </div>
        </div>

        <!-- Right: Instant Live Search Bar -->
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1 font-monospace" style="font-size: 0.72rem;" id="userCountBadge">
            Total: <?php echo $total_count; ?> Users
          </span>
          <div class="input-group input-group-sm" style="width: 260px;">
            <span class="input-group-text bg-light border-end-0 text-muted rounded-start-pill ps-3">
              <i class="bi bi-search small"></i>
            </span>
            <input type="text" id="tableSearch" class="form-control border-start-0 rounded-end-pill small font-monospace shadow-none" placeholder="Instant search directory..." onkeyup="filterUserTable();" autocomplete="off" />
          </div>
        </div>

      </div>
    </div>

    <!-- Hidden Form for Edit and Delete operations -->
    <?php echo form_open('user/admin_users/', array('id' => 'editUser', 'class' => 'm-0')); ?>
    <input type="hidden" name="UserIdty" id="UserIdty" value="" />
    <?php echo form_close(); ?>

    <!-- Hidden Form for PDF Export -->
    <?php echo form_open('user/admin_users/sub_district_admin_list_pdf_creation', array('id' => 'pdfExportForm', 'target' => '_blank', 'class' => 'm-0')); ?>
    <input type="hidden" name="generate_pdf" value="1" />
    <?php echo form_close(); ?>

    <div class="table-responsive">
      <table class="table align-middle mb-0" id="userRosterTable" style="font-size: 0.87rem;">
        <thead>
          <tr>
            <th scope="col" class="text-center py-2.5 ps-3" style="width: 50px;">#</th>
            <th scope="col" class="py-2.5">User &amp; Credentials</th>
            <th scope="col" class="py-2.5">Administrative Role</th>
            <th scope="col" class="py-2.5">Territorial Jurisdiction</th>
            <th scope="col" class="py-2.5">Security Status</th>
            <th scope="col" class="text-center py-2.5 pe-3" style="width: 130px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($retvalue) && is_array($retvalue)): ?>
            <?php
            $i = 1;
            foreach ($retvalue as $value):
              $user_type = (int)$value['user_type'];
              $user_type_label = 'School Admin';
              $badge_class = 'role-badge-school';
              $avatar_bg = 'background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #6d28d9;';
              $avatar_icon = 'bi-mortarboard-fill';

              if ($user_type === 1) {
                $user_type_label = 'State Admin';
                $badge_class = 'role-badge-state';
                $avatar_bg = 'background: linear-gradient(135deg, #ffe4e6, #fecdd3); color: #e11d48;';
                $avatar_icon = 'bi-shield-shaded';
              } elseif ($user_type === 2) {
                $user_type_label = 'District Admin';
                $badge_class = 'role-badge-district';
                $avatar_bg = 'background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #059669;';
                $avatar_icon = 'bi-buildings-fill';
              } elseif ($user_type === 3) {
                $user_type_label = 'Sub-district Admin';
                $badge_class = 'role-badge-subdist';
                $avatar_bg = 'background: linear-gradient(135deg, #e0f2fe, #bae6fd); color: #0284c7;';
                $avatar_icon = 'bi-pin-map-fill';
              }

              $genPwd = (string)($value['generated_password'] ?? '');
              $isChanged = ($value['is_change_password'] ?? '') === 'Y';
            ?>
              <tr class="user-row" 
                  data-role="<?php echo $user_type; ?>"
                  data-search="<?php echo strtolower(esc($value['user_name'] . ' ' . ($value['name'] ?? '') . ' ' . $user_type_label . ' ' . ($value['rev_district_name'] ?? '') . ' ' . ($value['sub_district_name'] ?? '') . ' ' . ($value['school_name'] ?? ''))); ?>">
                <!-- Index -->
                <td class="text-center text-muted small font-monospace py-3 ps-3">
                  <?php echo $i; ?>
                </td>

                <!-- User & Credentials -->
                <td class="py-3">
                  <div class="d-flex align-items-center gap-3">
                    <div class="avatar-badge shadow-xs" style="<?php echo $avatar_bg; ?>">
                      <i class="bi <?php echo $avatar_icon; ?>"></i>
                    </div>
                    <div>
                      <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold font-monospace text-dark" style="font-size: 0.92rem;"><?php echo esc($value['user_name']); ?></span>
                        <?php if (!empty($value['name'])): ?>
                          <span class="text-muted small fw-normal">(<?php echo esc($value['name']); ?>)</span>
                        <?php endif; ?>
                      </div>
                      
                      <!-- Password & Copy Snippet -->
                      <?php if (!empty($genPwd)): ?>
                        <div class="d-flex align-items-center gap-1.5 mt-1">
                          <span class="credential-tag">
                            <i class="bi bi-key-fill text-primary" style="font-size: 0.72rem;"></i>
                            <span><?php echo esc($genPwd); ?></span>
                            <button type="button" class="btn btn-link btn-sm p-0 ms-1 text-primary" onclick="copyCredentials('<?php echo addslashes(esc($value['user_name'])); ?>', '<?php echo addslashes(esc($genPwd)); ?>')" title="Copy Credentials">
                              <i class="bi bi-clipboard" style="font-size: 0.78rem;"></i>
                            </button>
                          </span>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>

                <!-- Role Badge -->
                <td class="py-3">
                  <span class="role-badge <?php echo $badge_class; ?>">
                    <i class="bi <?php echo $avatar_icon; ?>" style="font-size: 0.74rem;"></i>
                    <span><?php echo esc($user_type_label); ?></span>
                  </span>
                </td>

                <!-- Territorial Jurisdiction -->
                <td class="py-3">
                  <?php if ($user_type === 1): ?>
                    <span class="badge bg-light text-muted border px-2.5 py-1 small rounded-pill">
                      <i class="bi bi-globe2 text-primary me-1"></i>Statewide Jurisdiction
                    </span>
                  <?php elseif ($user_type === 2): ?>
                    <div class="d-flex align-items-center gap-1.5 text-dark fw-semibold">
                      <i class="bi bi-geo-alt-fill text-success small"></i>
                      <span><?php echo esc($value['rev_district_name'] ?? 'District ' . $value['rev_district_code']); ?></span>
                    </div>
                  <?php elseif ($user_type === 3): ?>
                    <div class="text-dark fw-semibold d-flex align-items-center gap-1.5">
                      <i class="bi bi-geo-alt-fill text-success small"></i>
                      <span><?php echo esc($value['rev_district_name'] ?? ''); ?></span>
                    </div>
                    <div class="text-muted small d-flex align-items-center gap-1.5 mt-0.5" style="font-size: 0.78rem;">
                      <i class="bi bi-pin-map-fill text-primary small"></i>
                      <span><?php echo esc($value['sub_district_name'] ?? 'Sub-District ' . $value['sub_district_code']); ?></span>
                    </div>
                  <?php elseif ($user_type >= 4): ?>
                    <div class="text-dark fw-semibold small">
                      <?php echo esc($value['rev_district_name'] ?? ''); ?> &bull; <?php echo esc($value['sub_district_name'] ?? ''); ?>
                    </div>
                    <div class="text-muted small font-monospace d-flex align-items-center gap-1.5 mt-0.5" style="font-size: 0.76rem;">
                      <i class="bi bi-building text-warning small"></i>
                      <span><?php echo esc($value['school_code']); ?><?php echo !empty($value['school_name']) ? ' - ' . esc($value['school_name']) : ''; ?></span>
                    </div>
                  <?php endif; ?>
                </td>

                <!-- Security Status -->
                <td class="py-3">
                  <?php if ($isChanged): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small fw-medium">
                      <i class="bi bi-shield-check me-1"></i>Customized
                    </span>
                  <?php else: ?>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 small fw-medium">
                      <i class="bi bi-shield-exclamation me-1"></i>Default Key
                    </span>
                  <?php endif; ?>
                </td>

                <!-- Actions -->
                <td class="text-center py-3 pe-3">
                  <div class="d-inline-flex align-items-center gap-1.5">
                    <button type="button" class="btn btn-action-edit shadow-xs d-inline-flex align-items-center gap-1" title="Edit Admin User"
                            data-user-id="<?php echo esc($value['user_id']); ?>"
                            data-user-name="<?php echo esc($value['user_name']); ?>"
                            data-user-type="<?php echo esc($value['user_type']); ?>"
                            data-district="<?php echo esc($value['rev_district_code']); ?>"
                            data-subdistrict="<?php echo esc($value['sub_district_code']); ?>"
                            data-school="<?php echo esc($value['school_code']); ?>"
                            onclick="openEditUserModal(this);">
                      <i class="bi bi-pencil-fill"></i><span>Edit</span>
                    </button>
                    <button type="button" class="btn btn-action-delete shadow-xs d-inline-flex align-items-center justify-content-center" title="Delete Admin User"
                            onclick="deleteUser('<?php echo esc($value['user_id']); ?>', '<?php echo esc($user_type_label); ?>', '<?php echo addslashes(esc($value['user_name'])); ?>', this);">
                      <i class="bi bi-trash-fill"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php
              $i++;
            endforeach;
            ?>
          <?php else: ?>
            <tr id="emptyRow">
              <td colspan="6" class="text-center py-5 text-muted">
                <div class="py-3">
                  <div class="rounded-circle bg-light d-inline-flex p-3 mb-2 text-secondary">
                    <i class="bi bi-inbox fs-2"></i>
                  </div>
                  <h6 class="fw-bold text-dark mb-1">No admin accounts found in directory</h6>
                  <p class="text-muted small mb-0">Use the "Add New Admin" button above to create administrative accounts.</p>
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
/**
 * Active Role Filter State
 */
var currentActiveRole = 0;

/**
 * Filter Table by Role Badge (KPI Tiles)
 */
function filterByRoleBadge(roleId, tileEl) {
  currentActiveRole = parseInt(roleId);

  // Update KPI Tile styles
  document.querySelectorAll('.kpi-tile').forEach(function(el) {
    el.classList.remove('active-kpi');
  });
  if (tileEl) {
    tileEl.classList.add('active-kpi');
  }

  // Update Segment tabs
  document.querySelectorAll('#roleSegmentTabs .segment-btn').forEach(function(btn) {
    if (parseInt(btn.getAttribute('data-role')) === currentActiveRole) {
      btn.classList.add('active');
    } else {
      btn.classList.remove('active');
    }
  });

  filterUserTable();
}

/**
 * Filter Table by Segment Tab
 */
function filterByRoleSegment(roleId, btnEl) {
  currentActiveRole = parseInt(roleId);

  // Update Segment tabs
  document.querySelectorAll('#roleSegmentTabs .segment-btn').forEach(function(btn) {
    btn.classList.remove('active');
  });
  if (btnEl) {
    btnEl.classList.add('active');
  }

  // Update KPI Tiles
  var kpiClasses = ['kpi-all', 'kpi-state', 'kpi-district', 'kpi-subdist'];
  document.querySelectorAll('.kpi-tile').forEach(function(el) {
    el.classList.remove('active-kpi');
    if (kpiClasses[currentActiveRole] && el.classList.contains(kpiClasses[currentActiveRole])) {
      el.classList.add('active-kpi');
    }
  });

  filterUserTable();
}

/**
 * Instant Client-Side Live Table Filter (Role + Search Query)
 */
function filterUserTable() {
  var searchInput = document.getElementById('tableSearch');
  var query = searchInput ? searchInput.value.toLowerCase().trim() : '';
  var rows = document.querySelectorAll('#userRosterTable tbody tr.user-row');
  var visibleCount = 0;

  rows.forEach(function(row) {
    var searchData = row.getAttribute('data-search') || '';
    var rowRole = parseInt(row.getAttribute('data-role')) || 0;

    var matchesSearch = (query === '' || searchData.indexOf(query) > -1);
    var matchesRole = (currentActiveRole === 0 || rowRole === currentActiveRole);

    if (matchesSearch && matchesRole) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  var badge = document.getElementById('userCountBadge');
  if (badge) {
    badge.innerText = 'Showing: ' + visibleCount + ' Users';
  }
}

/**
 * Jurisdiction Cascade Helpers for Modal
 */
function loadDistrict() {
  var divDist = document.getElementById('divDistrict');
  var divSub = document.getElementById('divSubdistrict');
  var divSch = document.getElementById('divSchool');
  var userTypeEl = document.getElementById('userType');

  if (divDist) divDist.style.display = 'none';
  if (divSub) divSub.style.display = 'none';
  if (divSch) divSch.style.display = 'none';

  if (document.getElementById('cmbDistrict')) document.getElementById('cmbDistrict').selectedIndex = 0;
  if (document.getElementById('cmbSubDistrict')) document.getElementById('cmbSubDistrict').selectedIndex = 0;
  if (document.getElementById('cmbSchool')) document.getElementById('cmbSchool').selectedIndex = 0;

  if (userTypeEl && userTypeEl.value != 1 && userTypeEl.value != 0) {
    if (divDist) divDist.style.display = 'block';
  }
}

function loadSubDistrict(selectedSubDistrictVal, selectedSchoolVal) {
  var userTypeEl = document.getElementById('userType');
  var userTypeValue = userTypeEl ? parseInt(userTypeEl.value) : 0;

  if (userTypeValue > 2) {
    var divSub = document.getElementById('divSubdistrict');
    if (divSub) divSub.style.display = 'block';

    var combo = document.getElementById('divSubdistrictCombo');
    if (combo) {
      combo.innerHTML = '<div class="d-flex align-items-center gap-2 text-muted small py-1.5"><span class="spinner-border spinner-border-sm text-primary" role="status"></span> Loading sub-districts...</div>';
    }

    var distSelect = document.getElementById('cmbDistrict');
    var district_code = distSelect ? distSelect.value : 0;

    var basePath = (typeof path !== 'undefined') ? path : '<?php echo site_url("/"); ?>';
    var formData = new FormData();
    formData.append('district', district_code);
    if (typeof getCsrfToken === 'function') {
      formData.append('csrf_test_name', getCsrfToken());
    }

    fetch(basePath + 'ajax/loadajax/get_subdistrict_details', {
      method: 'POST',
      body: formData,
      headers: (typeof getCsrfToken === 'function' && getCsrfToken()) ? { 'X-CSRF-TOKEN': getCsrfToken() } : {}
    })
    .then(function(response) {
      if (!response.ok) throw new Error('HTTP ' + response.status);
      return response.text();
    })
    .then(function(html) {
      if (combo) {
        combo.innerHTML = html;
        var newSub = document.getElementById('cmbSubDistrict');
        if (newSub) {
          newSub.className = 'form-select form-select-sm rounded-3';
          if (selectedSubDistrictVal) {
            newSub.value = selectedSubDistrictVal;
            if (userTypeValue > 3 && selectedSchoolVal) {
              loadSchool(selectedSchoolVal);
            }
          }
        }
      }
    })
    .catch(function(err) {
      console.error('Error loading subdistricts:', err);
      if (combo) combo.innerHTML = '<span class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i>Failed to load sub-districts</span>';
    });
  }
}

function loadSchool(selectedSchoolVal) {
  var userTypeEl = document.getElementById('userType');
  var userTypeValue = userTypeEl ? parseInt(userTypeEl.value) : 0;

  if (userTypeValue > 3) {
    var divSchool = document.getElementById('divSchool');
    if (divSchool) divSchool.style.display = 'block';

    var combo = document.getElementById('divSchoolCombo');
    if (combo) {
      combo.innerHTML = '<div class="d-flex align-items-center gap-2 text-muted small py-1.5"><span class="spinner-border spinner-border-sm text-primary" role="status"></span> Loading schools...</div>';
    }

    var subSelect = document.getElementById('cmbSubDistrict');
    var subdistrict_code = subSelect ? subSelect.value : 0;

    var basePath = (typeof path !== 'undefined') ? path : '<?php echo site_url("/"); ?>';
    var formData = new FormData();
    formData.append('subdistrict', subdistrict_code);
    if (typeof getCsrfToken === 'function') {
      formData.append('csrf_test_name', getCsrfToken());
    }

    fetch(basePath + 'ajax/loadajax/get_school_details', {
      method: 'POST',
      body: formData,
      headers: (typeof getCsrfToken === 'function' && getCsrfToken()) ? { 'X-CSRF-TOKEN': getCsrfToken() } : {}
    })
    .then(function(response) {
      if (!response.ok) throw new Error('HTTP ' + response.status);
      return response.text();
    })
    .then(function(html) {
      if (combo) {
        combo.innerHTML = html;
        var newSchool = document.getElementById('cmbSchool');
        if (newSchool) {
          newSchool.className = 'form-select form-select-sm rounded-3';
          if (selectedSchoolVal) {
            newSchool.value = selectedSchoolVal;
          }
        }
      }
    })
    .catch(function(err) {
      console.error('Error loading schools:', err);
      if (combo) combo.innerHTML = '<span class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i>Failed to load schools</span>';
    });
  }
}

/**
 * Direct Modal and Action Handlers
 */
function openAddUserModal() {
  var modalEl = document.getElementById('userModal');
  if (!modalEl) {
    console.error('Modal element #userModal not found in DOM');
    return;
  }

  // Ensure modal is attached directly to document.body
  if (modalEl.parentNode !== document.body) {
    document.body.appendChild(modalEl);
  }

  // Reset submit button state
  var submitBtn = document.getElementById('btnSubmitUser');
  if (submitBtn) submitBtn.disabled = false;
  var submitIcon = document.getElementById('btnSubmitUserIcon');
  if (submitIcon) submitIcon.classList.remove('d-none');
  var submitSpinner = document.getElementById('btnSubmitUserSpinner');
  if (submitSpinner) submitSpinner.classList.add('d-none');
  if (document.getElementById('btnSubmitUserText')) document.getElementById('btnSubmitUserText').innerText = 'Save Admin User';

  // Reset fields
  if (document.getElementById('hidUserId')) document.getElementById('hidUserId').value = '';
  if (document.getElementById('txtNewUserName')) document.getElementById('txtNewUserName').value = '';
  if (document.getElementById('txtNewPassword')) {
    document.getElementById('txtNewPassword').value = '';
    document.getElementById('txtNewPassword').placeholder = 'Enter password';
  }
  if (document.getElementById('pwdRequiredStar')) document.getElementById('pwdRequiredStar').style.display = 'inline';
  if (document.getElementById('pwdHelpText')) document.getElementById('pwdHelpText').innerText = 'Minimum 4 characters recommended.';

  // Reset role and territory
  if (document.getElementById('userType')) document.getElementById('userType').value = '0';
  if (document.getElementById('cmbDistrict')) document.getElementById('cmbDistrict').value = '0';
  
  var divDist = document.getElementById('divDistrict');
  var divSub = document.getElementById('divSubdistrict');
  var divSch = document.getElementById('divSchool');
  if (divDist) divDist.style.display = 'none';
  if (divSub) divSub.style.display = 'none';
  if (divSch) divSch.style.display = 'none';

  // Titles & styling
  if (document.getElementById('userModalLabel')) document.getElementById('userModalLabel').innerText = 'Add New Admin User';
  if (document.getElementById('userModalSubTitle')) document.getElementById('userModalSubTitle').innerText = 'Configure administrative credentials and territorial access.';
  
  var iconContainer = document.getElementById('modalIconContainer');
  if (iconContainer) {
    iconContainer.className = 'rounded-3 bg-primary-subtle text-primary border border-primary-subtle p-2 d-inline-flex align-items-center justify-content-center';
  }
  var modalIcon = document.getElementById('userModalIcon');
  if (modalIcon) {
    modalIcon.className = 'bi bi-person-plus-fill fs-5';
  }

  var formUser = document.getElementById('formUser');
  if (formUser) {
    var basePath = (typeof path !== 'undefined') ? path : '<?php echo site_url("/"); ?>';
    formUser.action = basePath + 'user/admin_users/add_admin';
  }

  if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
    var myModal = bootstrap.Modal.getOrCreateInstance(modalEl);
    myModal.show();
  }
}

function openEditUserModal(elemOrId, userName, userType, revDistrictCode, subDistrictCode, schoolCode) {
  var modalEl = document.getElementById('userModal');
  if (!modalEl) return;

  var clickedBtn = (typeof elemOrId === 'object' && elemOrId !== null && elemOrId.nodeType === 1) ? elemOrId : null;
  var originalBtnHtml = '';
  if (clickedBtn) {
    originalBtnHtml = clickedBtn.innerHTML;
    clickedBtn.disabled = true;
    clickedBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span><span>Loading...</span>';
  }

  function restoreEditBtn() {
    if (clickedBtn && originalBtnHtml) {
      clickedBtn.innerHTML = originalBtnHtml;
      clickedBtn.disabled = false;
      clickedBtn = null;
    }
  }

  if (modalEl.parentNode !== document.body) {
    document.body.appendChild(modalEl);
  }

  var userId = '';
  if (clickedBtn) {
    userId = clickedBtn.getAttribute('data-user-id') || (clickedBtn.dataset ? clickedBtn.dataset.userId : '');
    userName = clickedBtn.getAttribute('data-user-name') || (clickedBtn.dataset ? clickedBtn.dataset.userName : '');
    userType = clickedBtn.getAttribute('data-user-type') || (clickedBtn.dataset ? clickedBtn.dataset.userType : '');
    revDistrictCode = clickedBtn.getAttribute('data-district') || (clickedBtn.dataset ? clickedBtn.dataset.district : '');
    subDistrictCode = clickedBtn.getAttribute('data-subdistrict') || (clickedBtn.dataset ? clickedBtn.dataset.subdistrict : '');
    schoolCode = clickedBtn.getAttribute('data-school') || (clickedBtn.dataset ? clickedBtn.dataset.school : '');
  } else {
    userId = elemOrId;
  }

  // Reset submit button state
  var submitBtn = document.getElementById('btnSubmitUser');
  if (submitBtn) submitBtn.disabled = false;
  var submitIcon = document.getElementById('btnSubmitUserIcon');
  if (submitIcon) submitIcon.classList.remove('d-none');
  var submitSpinner = document.getElementById('btnSubmitUserSpinner');
  if (submitSpinner) submitSpinner.classList.add('d-none');
  if (document.getElementById('btnSubmitUserText')) document.getElementById('btnSubmitUserText').innerText = 'Update Admin User';

  if (document.getElementById('hidUserId')) document.getElementById('hidUserId').value = userId;
  if (document.getElementById('txtNewUserName')) document.getElementById('txtNewUserName').value = userName;
  if (document.getElementById('txtNewPassword')) {
    document.getElementById('txtNewPassword').value = '';
    document.getElementById('txtNewPassword').placeholder = 'Leave blank to keep unchanged';
  }
  if (document.getElementById('pwdRequiredStar')) document.getElementById('pwdRequiredStar').style.display = 'none';
  if (document.getElementById('pwdHelpText')) document.getElementById('pwdHelpText').innerText = 'Leave blank to preserve current active password.';

  if (document.getElementById('userType')) {
    document.getElementById('userType').value = userType;
  }

  if (document.getElementById('userModalLabel')) document.getElementById('userModalLabel').innerText = 'Edit Admin User: ' + userName;
  if (document.getElementById('userModalSubTitle')) document.getElementById('userModalSubTitle').innerText = 'Update credentials, roles, and territorial jurisdiction.';
  
  var iconContainer = document.getElementById('modalIconContainer');
  if (iconContainer) {
    iconContainer.className = 'rounded-3 bg-warning-subtle text-warning-emphasis border border-warning-subtle p-2 d-inline-flex align-items-center justify-content-center';
  }
  var modalIcon = document.getElementById('userModalIcon');
  if (modalIcon) {
    modalIcon.className = 'bi bi-pencil-square fs-5';
  }

  var formUser = document.getElementById('formUser');
  if (formUser) {
    var basePath = (typeof path !== 'undefined') ? path : '<?php echo site_url("/"); ?>';
    formUser.action = basePath + 'user/admin_users/update_admin_detials';
  }

  var divDist = document.getElementById('divDistrict');
  var divSub = document.getElementById('divSubdistrict');
  var divSch = document.getElementById('divSchool');

  if (userType == 1) {
    if (divDist) divDist.style.display = 'none';
    if (divSub) divSub.style.display = 'none';
    if (divSch) divSch.style.display = 'none';
  } else {
    if (divDist) divDist.style.display = 'block';
    if (document.getElementById('cmbDistrict')) {
      document.getElementById('cmbDistrict').value = revDistrictCode || 0;
    }

    if (userType > 2 && revDistrictCode) {
      if (divSub) divSub.style.display = 'block';
      if (typeof loadSubDistrict === 'function') {
        loadSubDistrict(subDistrictCode, schoolCode);
      }
    } else {
      if (divSub) divSub.style.display = 'none';
      if (divSch) divSch.style.display = 'none';
    }
  }

  if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
    var myModal = bootstrap.Modal.getOrCreateInstance(modalEl);
    myModal.show();
    setTimeout(restoreEditBtn, 400);
  } else {
    restoreEditBtn();
  }
}

function setSubmitSpinner(isLoading, loadingText) {
  var submitBtn = document.getElementById('btnSubmitUser');
  var submitIcon = document.getElementById('btnSubmitUserIcon');
  var submitSpinner = document.getElementById('btnSubmitUserSpinner');
  var submitText = document.getElementById('btnSubmitUserText');
  if (submitBtn) {
    submitBtn.disabled = isLoading;
  }
  if (submitIcon) {
    if (isLoading) submitIcon.classList.add('d-none');
    else submitIcon.classList.remove('d-none');
  }
  if (submitSpinner) {
    if (isLoading) submitSpinner.classList.remove('d-none');
    else submitSpinner.classList.add('d-none');
  }
  if (submitText && loadingText) {
    submitText.innerText = loadingText;
  }
}

function submitAdminUserForm() {
  var userId = document.getElementById('hidUserId') ? document.getElementById('hidUserId').value : '';
  if (userId && userId !== '') {
    if (!document.getElementById('txtNewUserName').value.trim()) {
      if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
        Swal.fire({ title: "Validation Error", text: "Please enter user name", icon: "warning", confirmButtonColor: "#2563eb" });
      } else if (typeof swal === 'function') swal("Oops!", "Please enter user name", "error");
      else alert("Please enter user name");
      document.getElementById('txtNewUserName').focus();
      return false;
    }
    if (document.getElementById('userType').value == 0) {
      if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
        Swal.fire({ title: "Validation Error", text: "Please select user role", icon: "warning", confirmButtonColor: "#2563eb" });
      } else if (typeof swal === 'function') swal("Oops!", "Select user role", "error");
      else alert("Select user role");
      document.getElementById('userType').focus();
      return false;
    }
    setSubmitSpinner(true, 'Updating...');
    fnsUserUpdate(userId);
  } else {
    if (fnsUserAdd()) {
      setSubmitSpinner(true, 'Saving...');
      var form = document.getElementById('formUser');
      if (form) {
        var basePath = (typeof path !== 'undefined') ? path : '<?php echo site_url("/"); ?>';
        form.action = basePath + 'user/admin_users/add_admin';
        form.submit();
      }
    }
  }
}

function fnsUserAdd() {
  if (!document.getElementById('txtNewUserName') || !document.getElementById('txtNewUserName').value.trim()) {
    if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
      Swal.fire({ title: "Validation Error", text: "Please enter user name", icon: "warning", confirmButtonColor: "#2563eb" });
    } else if (typeof swal === 'function') swal("Oops!", "Please Enter User name", "error");
    else alert("Please Enter User name");
    if (document.getElementById('txtNewUserName')) document.getElementById('txtNewUserName').focus();
    return false;
  }
  if (!document.getElementById('txtNewPassword') || !document.getElementById('txtNewPassword').value.trim()) {
    if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
      Swal.fire({ title: "Validation Error", text: "Please enter password", icon: "warning", confirmButtonColor: "#2563eb" });
    } else if (typeof swal === 'function') swal("Oops!", "Please Enter Password", "error");
    else alert("Please Enter Password");
    if (document.getElementById('txtNewPassword')) document.getElementById('txtNewPassword').focus();
    return false;
  }
  if (document.getElementById('userType') && document.getElementById('userType').value == 0) {
    if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
      Swal.fire({ title: "Validation Error", text: "Please select user role", icon: "warning", confirmButtonColor: "#2563eb" });
    } else if (typeof swal === 'function') swal("Oops!", "Select User type", "error");
    else alert("Select User type");
    document.getElementById('userType').focus();
    return false;
  }
  if (document.getElementById('userType') && document.getElementById('userType').value > 1) {
    if (!document.getElementById('cmbDistrict') || document.getElementById('cmbDistrict').value == 0) {
      if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
        Swal.fire({ title: "Validation Error", text: "Please select Revenue District", icon: "warning", confirmButtonColor: "#2563eb" });
      } else if (typeof swal === 'function') swal("Oops!", "Select Revenue District", "error");
      else alert("Select Revenue District");
      if (document.getElementById('cmbDistrict')) document.getElementById('cmbDistrict').focus();
      return false;
    }
  }
  if (document.getElementById('userType') && document.getElementById('userType').value > 2) {
    if (!document.getElementById('cmbSubDistrict') || document.getElementById('cmbSubDistrict').value == 0) {
      if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
        Swal.fire({ title: "Validation Error", text: "Please select Sub-District", icon: "warning", confirmButtonColor: "#2563eb" });
      } else if (typeof swal === 'function') swal("Oops!", "Select Sub-District", "error");
      else alert("Select Sub-District");
      if (document.getElementById('cmbSubDistrict')) document.getElementById('cmbSubDistrict').focus();
      return false;
    }
  }
  if (document.getElementById('userType') && document.getElementById('userType').value > 3) {
    if (!document.getElementById('cmbSchool') || document.getElementById('cmbSchool').value == 0) {
      if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
        Swal.fire({ title: "Validation Error", text: "Please select School", icon: "warning", confirmButtonColor: "#2563eb" });
      } else if (typeof swal === 'function') swal("Oops!", "Select School", "error");
      else alert("Select School");
      if (document.getElementById('cmbSchool')) document.getElementById('cmbSchool').focus();
      return false;
    }
  }
  return true;
}

function fnsUserUpdate(userId) {
  if (document.getElementById('hidUserId')) {
    document.getElementById('hidUserId').value = userId;
  }
  var form = document.getElementById('formUser');
  if (form) {
    var basePath = (typeof path !== 'undefined') ? path : '<?php echo site_url("/"); ?>';
    form.action = basePath + 'user/admin_users/update_admin_detials';
    form.submit();
  }
}

/**
 * Delete User Account with SweetAlert2 Confirmation
 */
function deleteUser(userId, UserType, UserName, btnEl) {
  var title = 'Delete ' + (UserType || 'User') + '?';
  var text = 'Are you sure you want to delete "' + UserName + '"? This administrative action cannot be undone.';

  if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
    Swal.fire({
      title: title,
      text: text,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e11d48',
      cancelButtonColor: '#64748b',
      confirmButtonText: '<i class="bi bi-trash-fill me-1"></i> Yes, Delete',
      cancelButtonText: 'Cancel',
      showLoaderOnConfirm: true,
      preConfirm: function() {
        if (btnEl) {
          btnEl.disabled = true;
          btnEl.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        }
        executeDeleteUser(userId, btnEl);
      },
      allowOutsideClick: function() { return !Swal.isLoading(); }
    });
  } else if (typeof swal === 'function') {
    swal({
      title: title,
      text: text,
      icon: 'warning',
      buttons: ['Cancel', 'Yes, Delete'],
      dangerMode: true
    }).then(function(willDelete) {
      if (willDelete) {
        if (btnEl) {
          btnEl.disabled = true;
          btnEl.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        }
        executeDeleteUser(userId, btnEl);
      }
    });
  } else {
    if (confirm(text)) {
      if (btnEl) {
        btnEl.disabled = true;
        btnEl.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
      }
      executeDeleteUser(userId, btnEl);
    }
  }
}

function executeDeleteUser(userId, btnEl) {
  if (btnEl) {
    btnEl.disabled = true;
    btnEl.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
  }
  if (document.getElementById('UserIdty')) {
    document.getElementById('UserIdty').value = userId;
  }
  var form = document.getElementById('editUser');
  if (form) {
    var basePath = (typeof path !== 'undefined') ? path : '<?php echo site_url("/"); ?>';
    form.action = basePath + 'user/admin_users/delete_admin_detials';
    form.submit();
  }
}

function generate_sub_dist_admin_pdf() {
  var form = document.getElementById('pdfExportForm');
  if (form) {
    form.submit();
  }
}

/**
 * Quick Copy Credentials Helper
 */
function copyCredentials(username, pwd) {
  var text = "Username: " + username + "\nPassword: " + pwd;
  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(text).then(function() {
      showToast("Credentials copied to clipboard!");
    });
  } else {
    var textArea = document.createElement("textarea");
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    try {
      document.execCommand('copy');
      showToast("Credentials copied to clipboard!");
    } catch (err) {
      alert("Username: " + username + " | Password: " + pwd);
    }
    document.body.removeChild(textArea);
  }
}

function showToast(msg) {
  if (typeof Toastify === 'function') {
    Toastify({
      text: msg,
      duration: 3000,
      gravity: "top",
      position: "right",
      style: { background: "linear-gradient(to right, #0ea5e9, #2563eb)" }
    }).showToast();
  } else {
    alert(msg);
  }
}

// Global window registration
window.openAddUserModal = openAddUserModal;
window.openEditUserModal = openEditUserModal;
window.submitAdminUserForm = submitAdminUserForm;
window.fnsUserAdd = fnsUserAdd;
window.fnsUserUpdate = fnsUserUpdate;
window.deleteUser = deleteUser;
window.executeDeleteUser = executeDeleteUser;
window.generate_sub_dist_admin_pdf = generate_sub_dist_admin_pdf;
window.copyCredentials = copyCredentials;
window.showToast = showToast;
window.filterUserTable = filterUserTable;
window.filterByRoleBadge = filterByRoleBadge;
window.filterByRoleSegment = filterByRoleSegment;
window.loadDistrict = loadDistrict;
window.loadSubDistrict = loadSubDistrict;
window.loadSchool = loadSchool;
</script>
