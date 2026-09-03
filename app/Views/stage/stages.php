<style>
/* ── Stage Table Styles (scoped to .stage-details-page) ── */
.sd-table-toolbar {
  padding: 18px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  border-bottom: 1px solid rgba(226,232,240,0.6);
}
.sd-table-toolbar h5 {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 600;
  color: #1e293b;
}
.sd-table-toolbar .sd-count-badge {
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  color: #fff;
  font-size: 0.78rem;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 20px;
  margin-left: 8px;
  vertical-align: middle;
}

/* Toolbar Add Button */
.sd-toolbar-add-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  color: #fff;
  border: none;
  padding: 8px 20px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.85rem;
  cursor: pointer;
  transition: all 0.25s ease;
  text-decoration: none;
  white-space: nowrap;
}
.sd-toolbar-add-btn:hover {
  box-shadow: 0 4px 16px rgba(79,70,229,0.35);
  transform: translateY(-1px);
  color: #fff;
}
.sd-toolbar-add-btn i {
  font-size: 1rem;
}

.sd-search-wrap {
  position: relative;
}
.sd-search-wrap i {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 0.9rem;
  pointer-events: none;
}
.sd-search-wrap input {
  padding: 8px 14px 8px 36px;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.85rem;
  width: 220px;
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
  background: #fff;
  font-family: 'Open Sans', sans-serif;
}
.sd-search-wrap input:focus {
  border-color: #7c3aed;
  box-shadow: 0 0 0 3px rgba(124,58,237,0.1);
}
.sd-search-wrap input::placeholder {
  color: #a1a1aa;
}

/* Table */
.sd-stages-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 0.9rem;
}
.sd-stages-table thead th {
  background: #f8fafc;
  font-size: 0.76rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #64748b;
  padding: 12px 16px;
  border-bottom: 2px solid #e2e8f0;
  white-space: nowrap;
}
.sd-stages-table tbody tr {
  transition: background 0.15s;
}
.sd-stages-table tbody tr:hover {
  background: rgba(124,58,237,0.04);
}
.sd-stages-table tbody tr:nth-child(even) {
  background: #f9fafb;
}
.sd-stages-table tbody tr:nth-child(even):hover {
  background: rgba(124,58,237,0.04);
}
.sd-stages-table tbody td,
.sd-stages-table tbody th {
  padding: 14px 16px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

/* Number pill */
.sd-num-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border-radius: 10px;
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  color: #fff;
  font-size: 0.8rem;
  font-weight: 700;
}

/* Stage name with avatar badge */
.sd-stage-name {
  display: flex;
  align-items: center;
  gap: 10px;
}
.sd-stage-avatar {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
  color: #fff;
  font-weight: 700;
  flex-shrink: 0;
}
.sd-stage-avatar--1 { background: linear-gradient(135deg, #4f46e5, #6366f1); }
.sd-stage-avatar--2 { background: linear-gradient(135deg, #7c3aed, #a855f7); }
.sd-stage-avatar--3 { background: linear-gradient(135deg, #10b981, #34d399); }
.sd-stage-avatar--4 { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
.sd-stage-avatar--5 { background: linear-gradient(135deg, #ef4444, #f87171); }
.sd-stage-avatar--6 { background: linear-gradient(135deg, #06b6d4, #22d3ee); }
.sd-stage-name-text {
  font-weight: 600;
  color: #1e293b;
}

/* Description muted */
.sd-desc-text {
  color: #64748b;
  font-size: 0.85rem;
  max-width: 260px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  display: inline-block;
}

/* Action buttons */
.sd-action-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  border-radius: 10px;
  border: 1.5px solid transparent;
  background: transparent;
  cursor: pointer;
  transition: all 0.2s;
  color: #64748b;
  font-size: 0.95rem;
  text-decoration: none;
}
.sd-action-btn:hover {
  transform: translateY(-1px);
}
.sd-action-btn--edit {
  color: #4f46e5;
}
.sd-action-btn--edit:hover {
  background: rgba(79,70,229,0.08);
  border-color: rgba(79,70,229,0.2);
  color: #4f46e5;
}
.sd-action-btn--delete {
  color: #ef4444;
}
.sd-action-btn--delete:hover {
  background: rgba(239,68,68,0.08);
  border-color: rgba(239,68,68,0.2);
  color: #ef4444;
}

/* Empty state */
.sd-empty-state {
  text-align: center;
  padding: 48px 24px;
  color: #94a3b8;
}
.sd-empty-state i {
  font-size: 3rem;
  margin-bottom: 12px;
  display: block;
  opacity: 0.5;
}
.sd-empty-state p {
  margin: 0;
  font-size: 0.95rem;
}

/* Responsive table */
@media (max-width: 767.98px) {
  .sd-table-toolbar {
    flex-direction: column;
    align-items: stretch;
  }
  .sd-search-wrap input { width: 100%; }
  .sd-stages-table { font-size: 0.82rem; }
  .sd-stages-table thead th,
  .sd-stages-table tbody td,
  .sd-stages-table tbody th { padding: 10px 10px; }
  .sd-desc-text { max-width: 140px; }
}
</style>

<div class="row">
<div class="col-12">
<div class="sd-glass sd-anim-2" style="margin-top: 28px;">

  <!-- Toolbar -->
  <div class="sd-table-toolbar">
    <div class="d-flex align-items-center">
      <h5><i class="bi bi-list-ul text-primary me-2"></i>Stages</h5>
      <span class="sd-count-badge"><?php echo count($stages); ?></span>
    </div>
    <div class="d-flex align-items-center gap-3">
      <button type="button" class="sd-toolbar-add-btn" data-bs-toggle="modal" data-bs-target="#sdStageModal" onclick="sdOpenAddModal()">
        <i class="bi bi-plus-lg"></i> Add Stage
      </button>
      <div class="sd-search-wrap">
        <i class="bi bi-search"></i>
        <input type="text" id="sdStageSearch" placeholder="Search stages..." autocomplete="off">
      </div>
    </div>
  </div>

  <?php if (count($stages) > 0): ?>
  <div class="table-responsive">
    <table class="sd-stages-table" id="sdStagesTable">
      <thead>
        <tr>
          <th>#</th>
          <th>Stage Name</th>
          <th>Description</th>
          <th style="width:60px; text-align:center;">Edit</th>
          <th style="width:60px; text-align:center;">Delete</th>
        </tr>
      </thead>
      <tbody>
        <?php
            $i = 1;
            foreach ($stages as $value) {
                $avatarClass = 'sd-stage-avatar--' . (($i - 1) % 6 + 1);
        ?>
        <tr data-stage-id="<?php echo $value['stage_id']; ?>" data-stage-name="<?php echo htmlspecialchars($value['stage_name'], ENT_QUOTES); ?>" data-stage-desc="<?php echo htmlspecialchars($value['stage_desc'], ENT_QUOTES); ?>">
          <td><span class="sd-num-pill"><?php echo $i; ?></span></td>
          <td>
            <div class="sd-stage-name">
              <span class="sd-stage-avatar <?php echo $avatarClass; ?>"><i class="bi bi-mic-stage"></i></span>
              <span class="sd-stage-name-text"><?php echo ($value['stage_name']); ?></span>
            </div>
          </td>
          <td><span class="sd-desc-text" title="<?php echo htmlspecialchars($value['stage_desc']); ?>"><?php echo ($value['stage_desc']); ?></span></td>
          <td style="text-align:center;">
            <a href="javascript:void(0)" onclick="sdOpenEditModal('<?php echo $value['stage_id']; ?>', '<?php echo addslashes($value['stage_name']); ?>', '<?php echo addslashes($value['stage_desc']); ?>')" class="sd-action-btn sd-action-btn--edit" title="Edit Stage"><i class="bi bi-pencil-square"></i></a>
          </td>
          <td style="text-align:center;">
            <a href="javascript:void(0)" onclick="sdDeleteStage('<?php echo $value['stage_id']; ?>', this)" class="sd-action-btn sd-action-btn--delete" title="Delete Stage"><i class="bi bi-trash3"></i></a>
          </td>
        </tr>
        <?php $i++; } ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="sd-empty-state">
    <i class="bi bi-mic-stage"></i>
    <p>No stages have been created yet. Click "Add Stage" to get started.</p>
  </div>
  <?php endif; ?>

</div>
</div>
</div>

<script>
(function(){
  var input = document.getElementById('sdStageSearch');
  if (!input) return;
  var table = document.getElementById('sdStagesTable');
  if (!table) return;
  var rows = table.querySelectorAll('tbody tr');
  input.addEventListener('keyup', function(){
    var q = this.value.toLowerCase();
    for (var i = 0; i < rows.length; i++) {
      var txt = rows[i].textContent.toLowerCase();
      rows[i].style.display = txt.indexOf(q) > -1 ? '' : 'none';
    }
  });
  input.addEventListener('keydown', function(e){
    if (e.key === 'Enter') { e.preventDefault(); }
  });
})();
</script>

<!-- ═══ CLOSE shared page container (opened in add_stage.php) ═══ -->
</div>
</div>
