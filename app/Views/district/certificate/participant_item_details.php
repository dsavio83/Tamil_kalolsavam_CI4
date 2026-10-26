<input type="hidden" name="hidUservalue" id="hidUservalue" value="SHOW" />

<div class="row justify-content-center">
  <div class="col-12">
    <!-- Search Box Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
      <div class="card-header bg-white py-3 px-4 border-bottom border-light d-flex align-items-center gap-3">
        <div class="rounded-3 bg-primary-subtle p-2 text-primary d-inline-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
          <i class="bi bi-search fs-5"></i>
        </div>
        <div>
          <h6 class="mb-0 fw-bold text-dark" style="font-size: 15px;">Search Student by Register Number</h6>
          <small class="text-muted" style="font-size: 13px;">Type student register number to load eligible events and print certificate</small>
        </div>
      </div>

      <div class="card-body p-4">
        <div class="row align-items-end g-3 mb-3">
          <div class="col-md-7 col-12">
            <label for="txtParticipantId" class="form-label fw-semibold text-secondary d-flex align-items-center gap-2 mb-1" style="font-size: 14px;">
              <i class="bi bi-person-vcard-fill text-primary"></i> Register Number
            </label>
            <div class="input-group">
              <span class="input-group-text bg-light border-secondary-subtle text-muted" style="font-size: 14px;">
                <i class="bi bi-hash"></i>
              </span>
              <input type="text" 
                     class="form-control border-secondary-subtle font-monospace" 
                     id="txtParticipantId" 
                     name="txtParticipantId" 
                     value="<?php echo htmlspecialchars($participant_id ?? ''); ?>" 
                     placeholder="e.g. 1001"
                     autocomplete="off"
                     style="font-size: 14px;"
                     onkeypress="if(event.key === 'Enter'){ event.preventDefault(); fetch_participant_details_result(); } return numbersonly(this, event, false);" />
              <button type="button" class="btn btn-primary px-4 d-inline-flex align-items-center gap-2 fw-semibold" style="font-size: 14px;" onclick="fetch_participant_details_result();">
                <i class="bi bi-search"></i> Search
              </button>
            </div>
          </div>
          <div class="col-md-5 col-12">
            <div class="p-2 bg-light rounded-3 border d-flex align-items-center gap-2" style="font-size: 12.5px;">
              <i class="bi bi-info-circle text-primary flex-shrink-0"></i>
              <span class="text-muted">Enter register number and click Search or pick a student from the roster below.</span>
            </div>
          </div>
        </div>

        <?php if (!empty($participant_details)): ?>
          <!-- Participant Profile Badge -->
          <div class="p-4 mb-4 rounded-4 border d-flex flex-wrap align-items-center gap-3 bg-light shadow-sm" style="padding: 1.35rem 1.5rem !important;">
            <div class="rounded-circle bg-primary bg-gradient text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 52px; height: 52px; font-size: 22px;">
              <i class="bi bi-person-check-fill"></i>
            </div>
            <div class="flex-grow-1">
              <h5 class="fw-bold mb-1.5 text-dark" style="font-size: 1.1rem;">
                <?php echo htmlspecialchars($participant_details); ?>
              </h5>
              <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="badge bg-white text-dark border font-monospace px-3 py-1.5" style="font-size: 13px;">
                  Reg No: <strong>#<?php echo htmlspecialchars($participant_id ?? ''); ?></strong>
                </span>
                <?php if (!empty($participant_info['admn_no'])): ?>
                  <span class="badge bg-white text-dark border px-3 py-1.5" style="font-size: 13px;">
                    Admn No: <?php echo htmlspecialchars($participant_info['admn_no']); ?>
                  </span>
                <?php endif; ?>
                <?php if (!empty($participant_info['class'])): ?>
                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5" style="font-size: 13px;">
                    Class: <?php echo htmlspecialchars($participant_info['class']); ?>
                  </span>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Item Dropdown Selection -->
          <?php if (!empty($item_drop_down)): ?>
            <div class="p-4 rounded-4 border bg-white shadow-sm mb-3" style="padding: 1.35rem 1.5rem !important;">
              <div class="row align-items-center g-3">
                <div class="col-md-7 col-12">
                  <label for="item_code" class="form-label fw-semibold text-secondary d-flex align-items-center gap-2 mb-1" style="font-size: 14px;">
                    <i class="bi bi-award-fill text-warning"></i> Select Participated Event
                  </label>
                  <div style="font-size: 14px;">
                    <?php echo $item_drop_down; ?>
                  </div>
                  <small class="text-muted d-block mt-1" style="font-size: 12px;">Choose 'All Items' to print all certificates or pick a specific event.</small>
                </div>
                <div class="col-md-5 col-12 text-md-end pt-2 pt-md-0">
                  <button type="submit" class="btn btn-success btn-lg rounded-pill px-4 shadow-sm w-100 w-md-auto d-inline-flex align-items-center justify-content-center gap-2" style="font-size: 14px;">
                    <i class="bi bi-printer-fill fs-5"></i>
                    <span class="fw-bold">Generate Certificate (PDF)</span>
                  </button>
                </div>
              </div>
            </div>
          <?php else: ?>
            <div class="alert alert-warning d-flex align-items-center gap-2 rounded-3 mb-0" role="alert" style="font-size: 13.5px;">
              <i class="bi bi-exclamation-circle-fill flex-shrink-0 fs-5"></i>
              <div>No participated events with confirmed certificate eligibility found for this student.</div>
            </div>
          <?php endif; ?>

        <?php elseif (!empty($participant_id)): ?>
          <!-- Not Found State -->
          <div class="alert alert-warning d-flex align-items-center gap-2 rounded-3 mb-0" role="alert" style="font-size: 13.5px;">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 fs-5"></i>
            <div>No participant found with Register Number #<strong><?php echo htmlspecialchars($participant_id); ?></strong> or results are not yet published.</div>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>
