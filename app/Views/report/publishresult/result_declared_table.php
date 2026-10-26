<style>
@font-face {
    font-family: 'TAU-Pallai';
    src: url('<?= base_url("assets/fonts/TAU-Paalai.ttf") ?>') format('truetype');
    font-weight: normal;
    font-style: normal;
}
@font-face {
    font-family: 'TAU-Pallai';
    src: url('<?= base_url("assets/fonts/TAU-Paalai Bold.ttf") ?>') format('truetype');
    font-weight: bold;
    font-style: normal;
}
.font-tamil {
    font-family: 'TAU-Pallai', 'Mukta Malar', 'Noto Sans Tamil', sans-serif !important;
    font-size: 14px !important;
}

/* Winner Card Styling (Mobile-First) */
.rd-winner-card {
    border: none;
    border-radius: 1.25rem;
    background: #ffffff;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    margin-bottom: 1rem;
}
.rd-winner-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
}
.rd-winner-card.rank-1-card {
    border: 2px solid #fef08a;
    background: linear-gradient(180deg, #fffdf5 0%, #ffffff 100%);
}
.rd-winner-card.rank-2-card {
    border: 2px solid #e2e8f0;
    background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
}
.rd-winner-card.rank-3-card {
    border: 2px solid #fed7aa;
    background: linear-gradient(180deg, #fffaf5 0%, #ffffff 100%);
}

.rd-rank-ribbon {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.35rem 0.85rem;
    border-radius: 2rem;
    font-weight: 700;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}
.ribbon-rank-1 {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #ffffff;
}
.ribbon-rank-2 {
    background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
    color: #ffffff;
}
.ribbon-rank-3 {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    color: #ffffff;
}
.ribbon-rank-default {
    background: #e2e8f0;
    color: #475569;
}

.rd-avatar-container {
    width: 76px;
    height: 76px;
    border-radius: 50%;
    overflow: hidden;
    position: relative;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    background: #f1f5f9;
}
.rd-avatar-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.rd-higher-level-badge {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    padding: 0.4rem 0.85rem;
    border-radius: 0.65rem;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);
}
.rd-points-pill {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
    padding: 0.25rem 0.65rem;
    border-radius: 1rem;
    font-weight: 700;
    font-size: 0.85rem;
}

/* Mobile cards vs Desktop table */
.rd-mobile-view {
    display: block;
}
.rd-desktop-view {
    display: none;
}
@media (min-width: 992px) {
    .rd-mobile-view {
        display: none;
    }
    .rd-desktop-view {
        display: block;
    }
}
</style>

<?php if (!empty($details)) { ?>
    <div class="mb-4 text-center">
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-primary bg-opacity-10 text-primary fw-bold mb-2">
            <i class="bi bi-trophy-fill text-warning"></i>
            <span>வெற்றிப் பட்டியல் &bull; Winners & Points</span>
        </div>
        <h3 class="h4 fw-bold mb-1 font-tamil text-dark">
            <?php echo esc($details[0]['item_name'] ?? ''); ?> (<?php echo esc($details[0]['item_code'] ?? ''); ?>)
        </h3>
        <p class="text-muted small mb-0 font-tamil">
            <?php echo esc($details[0]['fest_name'] ?? ''); ?> &bull; மொத்த வெற்றியாளர்கள்: <?php echo count($details); ?>
        </p>
    </div>

    <!-- Mobile-First Winner Cards Layout -->
    <div class="rd-mobile-view">
        <div class="row g-3">
            <?php
            $count = 0;
            foreach ($details as $row) {
                if (($row['is_publish'] ?? 'Y') === 'N') continue;
                $count++;
                $rank = (int)($row['rank'] ?? 0);
                $cardClass = ($rank === 1) ? 'rank-1-card' : (($rank === 2) ? 'rank-2-card' : (($rank === 3) ? 'rank-3-card' : ''));
                $ribbonClass = ($rank === 1) ? 'ribbon-rank-1' : (($rank === 2) ? 'ribbon-rank-2' : (($rank === 3) ? 'ribbon-rank-3' : 'ribbon-rank-default'));
                $rankText = ($rank === 1) ? '1st Prize / முதல் பரிசு' : (($rank === 2) ? '2nd Prize / 2-ம் பரிசு' : (($rank === 3) ? '3rd Prize / 3-ம் பரிசு' : 'Rank ' . $rank));

                // Photo resolution
                $reg = $row['admn_no'] ?? '';
                $sch = $row['school_code'] ?? '';
                $sub_dist = $row['sub_district_code'] ?? '';
                $pic_url = base_url('photos/img_user_nophoto.png');

                if (!empty($Photo['pic'][$reg])) {
                    $pic_url = $Photo['pic'][$reg];
                } elseif (!empty($sub_dist) && !empty($sch)) {
                    foreach (['jpg', 'jpeg', 'png'] as $ext) {
                        $cand = 'photos/' . $sub_dist . '/' . $sch . '/' . $sch . '_' . $reg . '.' . $ext;
                        if (file_exists(ROOTPATH . $cand)) {
                            $pic_url = base_url($cand);
                            break;
                        }
                    }
                }
            ?>
                <div class="col-12">
                    <div class="card rd-winner-card <?php echo $cardClass; ?> p-3">
                        <div class="rd-rank-ribbon <?php echo $ribbonClass; ?>">
                            <?php if ($rank === 1) { ?>
                                <i class="bi bi-trophy-fill"></i>
                            <?php } elseif ($rank === 2 || $rank === 3) { ?>
                                <i class="bi bi-award-fill"></i>
                            <?php } else { ?>
                                <i class="bi bi-star"></i>
                            <?php } ?>
                            <span><?php echo $rankText; ?></span>
                        </div>

                        <div class="d-flex align-items-start gap-3 mt-2">
                            <div class="rd-avatar-container">
                                <img src="<?php echo $pic_url; ?>" alt="Participant" onerror="this.src='<?php echo base_url('photos/img_user_nophoto.png'); ?>'">
                            </div>
                            <div class="flex-grow-1 pe-4">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <h4 class="h6 fw-bold mb-0 text-dark"><?php echo esc($row['participant_name']); ?></h4>
                                    <span class="badge bg-light text-dark border small">வகுப்பு: <?php echo esc($row['class']); ?></span>
                                </div>
                                <div class="small text-muted mb-2">
                                    <span>பதிவு எண்: <strong><?php echo esc($row['participant_id']); ?></strong></span>
                                    <?php if (!empty($row['code_no'])) { ?>
                                        &bull; <span>செஸ்ட் எண்: <strong><?php echo esc($row['code_no']); ?></strong></span>
                                    <?php } ?>
                                </div>

                                <div class="p-2 rounded bg-light bg-opacity-75 mb-2">
                                    <div class="fw-semibold text-dark small font-tamil">
                                        <i class="bi bi-building me-1 text-primary"></i>
                                        <?php echo esc($row['school_name']); ?>
                                        <span class="text-muted">(<?php echo esc($row['school_code']); ?>)</span>
                                    </div>
                                    <?php if (!empty($row['sub_district_name'])) { ?>
                                        <div class="small text-secondary font-tamil mt-1">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                            உப-மாவட்டம்: <strong><?php echo esc($row['sub_district_name']); ?></strong>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <?php
                                        $gr = strtoupper($row['grade'] ?? '');
                                        if ($gr === 'A') echo '<span class="badge bg-success rounded-pill px-2.5 py-1">Grade A</span>';
                                        elseif ($gr === 'B') echo '<span class="badge bg-warning text-dark rounded-pill px-2.5 py-1">Grade B</span>';
                                        elseif ($gr === 'C') echo '<span class="badge bg-info text-dark rounded-pill px-2.5 py-1">Grade C</span>';
                                        elseif (!empty($gr)) echo '<span class="badge bg-secondary rounded-pill px-2.5 py-1">Grade ' . esc($gr) . '</span>';
                                        ?>
                                        <span class="rd-points-pill">
                                            <i class="bi bi-star-fill text-warning me-1"></i><?php echo esc($row['point']); ?> Pts
                                        </span>
                                    </div>

                                    <?php if ($rank === 1) { ?>
                                        <div class="rd-higher-level-badge font-tamil">
                                            <i class="bi bi-check-circle-fill"></i>
                                            <span>மாவட்ட போட்டிக்குத் தகுதி</span>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Desktop Table View Layout -->
    <div class="rd-desktop-view">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 70px;">பரிசு / Rank</th>
                            <th class="text-center" style="width: 70px;">புகைப்படம்</th>
                            <th>பதிவு எண் / பெயர்</th>
                            <th>வகுப்பு</th>
                            <th>செஸ்ட் எண்</th>
                            <th>பள்ளி & உப-மாவட்டம்</th>
                            <th class="text-center">தரம் (Grade)</th>
                            <th class="text-center">புள்ளிகள்</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = 0;
                        foreach ($details as $row) {
                            if (($row['is_publish'] ?? 'Y') === 'N') continue;
                            $count++;
                            $rank = (int)($row['rank'] ?? 0);

                            // Photo resolution
                            $reg = $row['admn_no'] ?? '';
                            $sch = $row['school_code'] ?? '';
                            $sub_dist = $row['sub_district_code'] ?? '';
                            $pic_url = base_url('photos/img_user_nophoto.png');

                            if (!empty($Photo['pic'][$reg])) {
                                $pic_url = $Photo['pic'][$reg];
                            } elseif (!empty($sub_dist) && !empty($sch)) {
                                foreach (['jpg', 'jpeg', 'png'] as $ext) {
                                    $cand = 'photos/' . $sub_dist . '/' . $sch . '/' . $sch . '_' . $reg . '.' . $ext;
                                    if (file_exists(ROOTPATH . $cand)) {
                                        $pic_url = base_url($cand);
                                        break;
                                    }
                                }
                            }
                        ?>
                            <tr class="<?php echo ($rank === 1) ? 'table-warning bg-opacity-25' : ''; ?>">
                                <td class="text-center">
                                    <?php if ($rank === 1) { ?>
                                        <span class="badge ribbon-rank-1 rounded-pill px-2.5 py-1.5 fs-7"><i class="bi bi-trophy-fill me-1"></i>1st</span>
                                    <?php } elseif ($rank === 2) { ?>
                                        <span class="badge ribbon-rank-2 rounded-pill px-2.5 py-1.5 fs-7"><i class="bi bi-award-fill me-1"></i>2nd</span>
                                    <?php } elseif ($rank === 3) { ?>
                                        <span class="badge ribbon-rank-3 rounded-pill px-2.5 py-1.5 fs-7"><i class="bi bi-award me-1"></i>3rd</span>
                                    <?php } else { ?>
                                        <span class="badge bg-light text-dark border"><?php echo $count; ?></span>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <img src="<?php echo $pic_url; ?>" class="rounded-circle shadow-sm" style="width: 48px; height: 48px; object-fit: cover;" alt="Photo" onerror="this.src='<?php echo base_url('photos/img_user_nophoto.png'); ?>'">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo esc($row['participant_name']); ?></div>
                                    <small class="text-muted">Reg No: <strong><?php echo esc($row['participant_id']); ?></strong></small>
                                    <?php if ($rank === 1) { ?>
                                        <div class="text-success small fw-semibold font-tamil mt-0.5"><i class="bi bi-check-circle-fill me-1"></i>மாவட்ட போட்டிக்குத் தகுதி (Eligible)</div>
                                    <?php } ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?php echo esc($row['class']); ?></span>
                                </td>
                                <td>
                                    <strong><?php echo esc($row['code_no']); ?></strong>
                                </td>
                                <td>
                                    <div class="font-tamil fw-semibold text-dark"><?php echo esc($row['school_name']); ?></div>
                                    <div class="small text-muted font-tamil">
                                        <span class="text-primary fw-medium"><?php echo esc($row['school_code']); ?></span>
                                        <?php if (!empty($row['sub_district_name'])) { ?>
                                            &bull; <i class="bi bi-geo-alt-fill text-danger"></i> <?php echo esc($row['sub_district_name']); ?>
                                        <?php } ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $gr = strtoupper($row['grade'] ?? '');
                                    if ($gr === 'A') echo '<span class="badge bg-success rounded-pill px-2.5 py-1">Grade A</span>';
                                    elseif ($gr === 'B') echo '<span class="badge bg-warning text-dark rounded-pill px-2.5 py-1">Grade B</span>';
                                    elseif ($gr === 'C') echo '<span class="badge bg-info text-dark rounded-pill px-2.5 py-1">Grade C</span>';
                                    elseif (!empty($gr)) echo '<span class="badge bg-secondary rounded-pill px-2.5 py-1">Grade ' . esc($gr) . '</span>';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <strong class="text-success fs-6"><?php echo esc($row['point']); ?> Pts</strong>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4 bg-light">
        <div class="text-muted mb-3">
            <i class="bi bi-hourglass-split display-4 text-warning"></i>
        </div>
        <h4 class="h5 fw-bold text-dark font-tamil">முடிவுகள் இன்னும் அறிவிக்கப்படவில்லை</h4>
        <p class="text-muted mb-0 font-tamil">தேர்ந்தெடுக்கப்பட்ட போட்டிக்கான மதிப்பீட்டு முடிவுகள் சரிபார்க்கப்பட்டு விரைவில் வெளியிடப்படும்.</p>
    </div>
<?php } ?>