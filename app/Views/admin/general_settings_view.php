<style>
:root {
  --primary-accent: #2563eb;
  --primary-hover: #1d4ed8;
  --slate-border: #e2e8f0;
  --slate-bg: #f8fafc;
  --card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
}

.general-settings-container {
  padding: 12px 0 35px 0;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  color: #0f172a;
}

.settings-hero-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid var(--slate-border);
  box-shadow: var(--card-shadow);
  padding: 1.5rem 1.75rem;
  margin-bottom: 1.5rem;
}

.settings-header-icon-box {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
  color: #2563eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.6rem;
  flex-shrink: 0;
}

.settings-card {
  background: #ffffff;
  border-radius: 14px;
  border: 1px solid var(--slate-border);
  box-shadow: var(--card-shadow);
  overflow: hidden;
}

.settings-info-note {
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  border-radius: 10px;
  padding: 0.8rem 1rem;
  color: #1e40af;
  font-size: 0.9rem;
  margin: 1rem 1.25rem;
}

.district-table {
  width: 100%;
  border-collapse: collapse;
}

.district-table thead th {
  background: var(--slate-bg);
  color: #475569;
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  text-align: left;
  padding: 0.85rem 1.25rem;
  border-bottom: 1px solid var(--slate-border);
}

.district-table tbody td {
  padding: 0.7rem 1.25rem;
  border-bottom: 1px solid #f1f5f9;
  font-size: 0.92rem;
  vertical-align: middle;
}

.district-table tbody tr:last-child td {
  border-bottom: none;
}

.district-table tbody tr:hover {
  background: #f8fafc;
}

.district-input {
  width: 90px;
  padding: 0.4rem 0.6rem;
  border: 1px solid var(--slate-border);
  border-radius: 8px;
  font-size: 0.92rem;
}

.district-input:focus {
  outline: none;
  border-color: var(--primary-accent);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}

.btn-save-row {
  background: var(--primary-accent);
  color: #fff;
  border: none;
  padding: 0.4rem 1rem;
  border-radius: 8px;
  font-size: 0.85rem;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-save-row:hover {
  background: var(--primary-hover);
}

.table-responsive {
  overflow-x: auto;
}
</style>

<div class="general-settings-container">
  <div class="settings-hero-card">
    <div class="d-flex align-items-center gap-3">
      <div class="settings-header-icon-box">
        <i class="bi bi-sliders"></i>
      </div>
      <div>
        <h4 class="mb-1 fw-bold">General Settings</h4>
        <p class="mb-0 text-muted">Configure participant item selection limits for each district.</p>
      </div>
    </div>
  </div>

  <div class="settings-card">
    <div class="settings-info-note">
      <i class="bi bi-info-circle me-1"></i>
      These limits apply per participant within the district, and are enforced during school registration.
      A row is saved only when you click <strong>Save</strong> for that district.
    </div>

    <?php if (!empty($show_all_districts_form)) { ?>
    <form method="post" action="<?php echo base_url(); ?>admin/general_settings/update">
      <div class="px-3 pt-3 pb-2 d-flex align-items-center gap-2 flex-wrap" style="border-bottom:1px solid #f1f5f9;">
        <input type="hidden" name="all_districts" value="1">
        <label class="mb-0 fw-semibold" for="all_single">Set all districts</label>
        <input type="number" id="all_single" name="max_single_items" class="district-input" value="3" min="0">
        <span class="text-muted small">single</span>
        <input type="number" id="all_group" name="max_group_items" class="district-input" value="2" min="0">
        <span class="text-muted small">group</span>
        <button type="submit" class="btn-save-row">Apply to All</button>
      </div>
    </form>
    <?php } ?>

    <div class="table-responsive">
      <table class="district-table">
        <thead>
          <tr>
            <th style="width:60px;">Code</th>
            <th>District</th>
            <th style="width:160px;">Max Single Items</th>
            <th style="width:160px;">Max Group Items</th>
            <th style="width:90px;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($district_settings)) { foreach ($district_settings as $d) { ?>
          <tr>
            <td class="text-muted"><?php echo (int)$d['rev_district_code']; ?></td>
            <td class="fw-semibold"><?php echo htmlspecialchars((string)$d['rev_district_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            <form method="post" action="<?php echo base_url(); ?>admin/general_settings/update">
              <input type="hidden" name="rev_district_code" value="<?php echo (int)$d['rev_district_code']; ?>">
              <td>
                <input type="number" name="max_single_items" class="district-input"
                       value="<?php echo (int)$d['max_single_items']; ?>" min="0">
              </td>
              <td>
                <input type="number" name="max_group_items" class="district-input"
                       value="<?php echo (int)$d['max_group_items']; ?>" min="0">
              </td>
              <td>
                <button type="submit" class="btn-save-row">Save</button>
              </td>
            </form>
          </tr>
          <?php } } else { ?>
          <tr><td colspan="5" class="text-muted text-center py-4">No district settings found.</td></tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
