<div class="card border-0 shadow-sm rounded-4 mb-4 text-center py-5">
  <div class="card-body py-4 px-3">
    <div class="mb-3">
      <span class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center p-4 border shadow-sm">
        <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
      </span>
    </div>
    <h4 class="fw-bold text-dark mb-2">No Items or Participants Found</h4>
    <p class="text-secondary mb-1" style="font-size: 1.05rem;">
      போட்டிகள் / பங்கேற்பாளர்கள் எதுவும் கிடைக்கவில்லை.
    </p>
    <p class="text-muted small max-w-md mx-auto mb-4" style="max-width: 540px;">
      தேர்வு செய்யப்பட்ட பிரிவில் (<strong><?php echo esc($fest_title ?? 'Selected Festival'); ?></strong>) தற்போது பதிவு செய்யப்பட்ட பங்கேற்பாளர்கள் அல்லது போட்டிகள் எதுவும் இல்லை.
      மற்றொரு பிரிவை அல்லது அனைத்துப் போட்டிகளையும் பார்க்க கீழே உள்ள பட்டனை கிளிக் செய்யவும்.
    </p>

    <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
      <a href="<?php echo base_url(); ?>stage/item_participant/participant_nodetails?cmbFestType=all<?php echo !empty($sub_district) ? '&cmbSubDistrict=' . (int)$sub_district : ''; ?>" 
         class="btn btn-primary px-4 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-2">
        <i class="bi bi-collection-fill"></i>
        <span>Show All Festivals / Categories (அனைத்தும்)</span>
      </a>
      <?php if ((int)session()->get('USER_TYPE') !== 2): ?>
      <a href="<?php echo base_url(); ?>stage/item_participant/participant_nodetails?cmbFestType=1<?php echo !empty($sub_district) ? '&cmbSubDistrict=' . (int)$sub_district : ''; ?>" 
         class="btn btn-outline-secondary px-4 py-2 rounded-pill d-inline-flex align-items-center gap-2">
        <i class="bi bi-stars"></i>
        <span>LP Tamil Kalaivizha</span>
      </a>
      <?php endif; ?>
    </div>
  </div>
</div>
