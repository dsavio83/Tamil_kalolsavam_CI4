<?php
if (empty($fest_details)) {
    $sub_district_code = !empty($school_details[0]['sub_district_code']) ? $school_details[0]['sub_district_code'] : session()->get('SUB_DISTRICT');
    if (!empty($sub_district_code)) {
        $db = \Config\Database::connect();
        $fest_details = $db->table('sd_kalolsavam_master')
            ->where('sub_district_code', (int)$sub_district_code)
            ->where('status', 'O')
            ->get()->getResultArray();
        if (empty($fest_details)) {
            $fest_details = $db->table('sd_kalolsavam_master')
                ->where('sub_district_code', (int)$sub_district_code)
                ->get()->getResultArray();
        }
    }
}

$fest_title = !empty($fest_details[0]['sub_dist_kalolsavam_name']) ? preg_replace('/\s+/', ' ', trim($fest_details[0]['sub_dist_kalolsavam_name'])) : get_festival_title();
$venue = !empty($fest_details[0]['venue']) ? trim($fest_details[0]['venue']) : '';
$start_date = (!empty($fest_details[0]['start_date']) && $fest_details[0]['start_date'] != '0000-00-00') ? date('d M Y', strtotime($fest_details[0]['start_date'])) : '';
$end_date = (!empty($fest_details[0]['end_date']) && $fest_details[0]['end_date'] != '0000-00-00') ? date('d M Y', strtotime($fest_details[0]['end_date'])) : '';
$date_range = ($start_date && $end_date) ? ($start_date . ' - ' . $end_date) : ($start_date ?: $end_date);
$logo = !empty($fest_details[0]['logo_name']) ? trim($fest_details[0]['logo_name']) : '';
$logo_path = $logo ? pdf_safe_logo_image('sub_district', $logo, false) : '';

$school_type = '';
if (@$school_details[0]['school_type'] == 'G') $school_type = 'Government';
else if (@$school_details[0]['school_type'] == 'A') $school_type = 'Aided';
else if (@$school_details[0]['school_type'] == 'U') $school_type = 'Unaided';
?>
<page backtop="22mm" backbottom="35mm" backleft="7.5mm" backright="7.5mm">
    <page_header>
        <table style="width: 100%;" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 20%; text-align: left; vertical-align: top;">
                    <?php if (!empty($logo_path) && (file_exists($logo_path) || filter_var($logo_path, FILTER_VALIDATE_URL))) { ?>
                        <img src="<?php echo $logo_path; ?>" height="58">
                    <?php } ?>
                </td>
                <td style="width: 80%; text-align: right; vertical-align: top;" align="right">
                    <strong style="font-size: 13.5pt; font-weight: bold; line-height: 1.3;"><?php echo $fest_title; ?></strong><br />
                    <?php if (!empty($venue)) { ?><span style="font-size: 11pt; line-height: 1.4;"><?php echo $venue; ?></span><br /><?php } ?>
                    <?php if (!empty($date_range)) { ?><span style="font-size: 10.5pt; line-height: 1.4;"><?php echo $date_range; ?></span><?php } ?>
                </td>
            </tr>
            <tr>
                <td style="width: 100%; padding-top: 4px;" colspan="2"><hr style="border: none; border-top: 1.5px solid #000000; margin: 0;"/></td>
            </tr>
        </table>
    </page_header>
    
    <page_footer>
        <table style="width: 100%;" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="center" colspan="3" style="width: 100%; font-size: 10pt; padding-bottom: 15px;">
                    Certified that above details has been verified and found correct
                </td>
            </tr>
            <tr>
                <td align="left" style="width: 35%; font-size: 10pt; vertical-align: bottom;">
                    Place&nbsp;:&nbsp;..............................<br /><br />
                    Date&nbsp;&nbsp;:&nbsp;.............................
                </td>
                <td align="center" style="width: 30%; font-size: 10pt; vertical-align: bottom;">
                    (School Seal)
                </td>
                <td align="right" style="width: 35%; font-size: 10pt; vertical-align: bottom;">
                    Principal / Headmaster
                </td>
            </tr>
            <tr>
                <td colspan="3" style="width: 100%; padding-top: 8px;">
                    <hr style="border: none; border-top: 1px solid #888888; margin: 0 0 4px 0;"/>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center; width: 100%; font-size: 8.5pt; color: #555;">
                    page [[page_cu]]/{nb}
                </td>
            </tr>
        </table>
    </page_footer>

    <h2 align="center" style="font-size: 12.5pt; margin-top: 0px; margin-bottom: 5px; font-weight: bold; text-transform: uppercase;">Registration Details</h2>

    <!-- School Information Table -->
    <table border="1" cellpadding="3" cellspacing="0" style="font-size: 9.5pt; border-collapse: collapse;">
        <tr>
            <td width="128" style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">School Code :</td>
            <td width="185" style="border: 1px solid #000000;"><?php echo htmlspecialchars($school_details[0]['school_code']); ?></td>
            <td width="107" style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">School Name :</td>
            <td width="245" style="border: 1px solid #000000; font-weight: bold;"><?php echo htmlspecialchars($school_details[0]['school_name']); ?></td>
        </tr>
        <tr>
            <td width="128" style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">Sub District :</td>
            <td width="185" style="border: 1px solid #000000;"><?php echo htmlspecialchars($school_details[0]['sub_district_name'] ?? ''); ?></td>
            <td width="107" style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">School Type :</td>
            <td width="245" style="border: 1px solid #000000;"><?php echo $school_type; ?></td>
        </tr>
        <tr>
            <td width="128" style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">Standard :</td>
            <td width="185" style="border: 1px solid #000000;">From : <?php echo htmlspecialchars($school_details[0]['class_start']); ?> &nbsp;&nbsp; To : <?php echo htmlspecialchars($school_details[0]['class_end']); ?></td>
            <td width="107" style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">School Email :</td>
            <td width="245" style="border: 1px solid #000000;"><?php echo htmlspecialchars($school_details[0]['school_email'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td width="128" style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">Headmaster :</td>
            <td width="185" style="border: 1px solid #000000;"><?php echo htmlspecialchars($school_details[0]['hm_name'] ?? '-'); ?></td>
            <td width="107" style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">Mobile :</td>
            <td width="245" style="border: 1px solid #000000;"><?php echo htmlspecialchars($school_details[0]['hm_phone'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td width="128" style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">Principal :</td>
            <td width="185" style="border: 1px solid #000000;"><?php echo htmlspecialchars($school_details[0]['principal_name'] ?? '-'); ?></td>
            <td width="107" style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000;">Mobile :</td>
            <td width="245" style="border: 1px solid #000000;"><?php echo htmlspecialchars($school_details[0]['principal_phone'] ?? '-'); ?></td>
        </tr>
        <?php
        $tm_names = [];
        $tm_phones = [];
        if (!empty($school_details[0]['teachers'])) {
            $teachers = explode("#@#", $school_details[0]['teachers']);
            foreach ($teachers as $t) {
                if (trim($t) === '') continue;
                $parts = explode('#$#', $t);
                $tName = trim($parts[0] ?? '');
                $tPhone = trim($parts[1] ?? '');
                if ($tName !== '') {
                    $tm_names[] = htmlspecialchars($tName);
                    $tm_phones[] = htmlspecialchars($tPhone !== '' ? $tPhone : '-');
                }
            }
        }
        $tm_name_str = !empty($tm_names) ? implode('<br>', $tm_names) : '-';
        $tm_phone_str = !empty($tm_phones) ? implode('<br>', $tm_phones) : '-';
        ?>
        <tr>
            <td width="128" style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000; vertical-align: top;">Team Manager(s) :</td>
            <td width="185" style="border: 1px solid #000000; vertical-align: top;"><?php echo $tm_name_str; ?></td>
            <td width="107" style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000; vertical-align: top;">Mobile :</td>
            <td width="245" style="border: 1px solid #000000; vertical-align: top;"><?php echo $tm_phone_str; ?></td>
        </tr>
        <tr>
            <td width="128" style="font-weight: bold; background-color: #f2f2f2; border: 1px solid #000000; vertical-align: middle;">Total number of students :</td>
            <td width="537" colspan="3" style="border: 1px solid #000000; padding: 0px;">
                <table border="1" cellpadding="3" cellspacing="0" style="border-collapse: collapse; text-align: center; font-size: 9pt;">
                    <tr style="background-color: #fafafa;">
                        <td width="75" style="font-weight: bold; border: 1px solid #777;">LP</td>
                        <td width="75" style="font-weight: bold; border: 1px solid #777;">UP</td>
                        <td width="75" style="font-weight: bold; border: 1px solid #777;">HS</td>
                        <td width="75" style="font-weight: bold; border: 1px solid #777;">HSS</td>
                        <td width="87" style="font-weight: bold; border: 1px solid #777;">VHSE</td>
                        <td width="125" style="font-weight: bold; border: 1px solid #777; background-color: #e9ecef;">Total</td>
                    </tr>
                    <tr>
                        <td width="75" style="border: 1px solid #777;"><?php echo (int)@$school_details[0]['strength_lp']; ?></td>
                        <td width="75" style="border: 1px solid #777;"><?php echo (int)@$school_details[0]['strength_up']; ?></td>
                        <td width="75" style="border: 1px solid #777;"><?php echo (int)@$school_details[0]['strength_hs']; ?></td>
                        <td width="75" style="border: 1px solid #777;"><?php echo (int)@$school_details[0]['strength_hss']; ?></td>
                        <td width="87" style="border: 1px solid #777;"><?php echo (int)@$school_details[0]['strength_vhss']; ?></td>
                        <td width="125" style="border: 1px solid #777; font-weight: bold; background-color: #f4f4f4;"><?php echo (int)@$school_details[0]['total_strength']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Participants Table -->
    <table border="1" cellpadding="3" cellspacing="0" style="margin-top: 10px; font-size: 9.5pt; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #e9ecef;">
                <td width="34" style="border: 1px solid #000000; text-align: center; font-weight: bold;">Sl.No</td>
                <td width="66" style="border: 1px solid #000000; text-align: center; font-weight: bold;">Admn No.</td>
                <td width="151" style="border: 1px solid #000000; text-align: left; font-weight: bold;">Name of participant</td>
                <td width="42" style="border: 1px solid #000000; text-align: center; font-weight: bold;">Class</td>
                <td width="60" style="border: 1px solid #000000; text-align: center; font-weight: bold;">Boy / Girl</td>
                <td width="52" style="border: 1px solid #000000; text-align: center; font-weight: bold;">Item 1</td>
                <td width="52" style="border: 1px solid #000000; text-align: center; font-weight: bold;">Item 2</td>
                <td width="52" style="border: 1px solid #000000; text-align: center; font-weight: bold;">Item 3</td>
                <td width="52" style="border: 1px solid #000000; text-align: center; font-weight: bold;">Item 4</td>
                <td width="52" style="border: 1px solid #000000; text-align: center; font-weight: bold;">Item 5</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            for ($j = 0; $j < count($participant_details); $j++) {
                $count++;
                $items = !empty($participant_details[$j]['item_code']) && is_array($participant_details[$j]['item_code']) 
                         ? array_values($participant_details[$j]['item_code']) 
                         : [];
                $it1 = !empty($items[0]) ? htmlspecialchars($items[0]) : '';
                $it2 = !empty($items[1]) ? htmlspecialchars($items[1]) : '';
                $it3 = !empty($items[2]) ? htmlspecialchars($items[2]) : '';
                $it4 = !empty($items[3]) ? htmlspecialchars($items[3]) : '';
                $it5 = '';
                if (count($items) > 5) {
                    $remaining = array_slice($items, 4);
                    $it5 = htmlspecialchars(implode(', ', $remaining));
                } elseif (!empty($items[4])) {
                    $it5 = htmlspecialchars($items[4]);
                }
                ?>
                <tr>
                    <td width="34" style="border: 1px solid #000000; text-align: center;"><?php echo $count; ?></td>
                    <td width="66" style="border: 1px solid #000000; text-align: center;"><?php echo htmlspecialchars($participant_details[$j]['admn_no']); ?></td>
                    <td width="151" style="border: 1px solid #000000; text-align: left;"><?php echo htmlspecialchars($participant_details[$j]['participant_name']); ?></td>
                    <td width="42" style="border: 1px solid #000000; text-align: center;"><?php echo htmlspecialchars($participant_details[$j]['class']); ?></td>
                    <td width="60" style="border: 1px solid #000000; text-align: center;"><?php echo ($participant_details[$j]['gender'] == 'B') ? 'Boy' : 'Girl'; ?></td>
                    <td width="52" style="border: 1px solid #000000; text-align: center;"><?php echo $it1; ?></td>
                    <td width="52" style="border: 1px solid #000000; text-align: center;"><?php echo $it2; ?></td>
                    <td width="52" style="border: 1px solid #000000; text-align: center;"><?php echo $it3; ?></td>
                    <td width="52" style="border: 1px solid #000000; text-align: center;"><?php echo $it4; ?></td>
                    <td width="52" style="border: 1px solid #000000; text-align: center;"><?php echo $it5; ?></td>
                </tr>
                <?php
            }
            if ($count === 0) {
                ?>
                <tr>
                    <td colspan="10" style="border: 1px solid #000000; text-align: center; padding: 10px;">No registered participants found for this school.</td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

    <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-top: 30px;">
        <tr>
            <td style="width: 100%; text-align: left; font-size: 10pt;">
                <strong>Name & Signature of Team Manager :</strong> ........................................................................
            </td>
        </tr>
    </table>
</page>
