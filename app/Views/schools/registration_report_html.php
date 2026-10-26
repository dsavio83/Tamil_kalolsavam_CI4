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

$logo_web_url = '';
if (!empty($logo)) {
    if (file_exists(FCPATH . 'uploads/sub_district/' . $logo)) {
        $logo_web_url = base_url('uploads/sub_district/' . $logo);
    } elseif (file_exists(FCPATH . 'uploads/sub_district/thumb_' . $logo)) {
        $logo_web_url = base_url('uploads/sub_district/thumb_' . $logo);
    }
}
$school_type = '';
if (@$school_details[0]['school_type'] == 'G') $school_type = 'Government';
else if (@$school_details[0]['school_type'] == 'A') $school_type = 'Aided';
else if (@$school_details[0]['school_type'] == 'U') $school_type = 'Unaided';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $fest_title; ?> - Registration Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .report-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 1000px;
            margin: 0 auto;
        }
        .header {
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-logo img {
            max-height: 70px;
            width: auto;
        }
        .header-text {
            text-align: right;
        }
        .header-text h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #000;
        }
        .header-text .venue {
            margin: 4px 0 2px;
            font-size: 15px;
            color: #333;
        }
        .header-text .dates {
            margin: 0;
            font-size: 14px;
            color: #444;
        }
        .report-title {
            text-align: center;
            font-size: 17px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 0;
            margin-bottom: 14px;
            color: #000;
        }
        .school-details {
            margin-bottom: 20px;
        }
        .school-details table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .school-details td {
            padding: 6px 8px;
            border: 1px solid #000;
            vertical-align: middle;
        }
        .school-details .label {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .participants-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
            margin-bottom: 25px;
        }
        .participants-table th,
        .participants-table td {
            border: 1px solid #000;
            padding: 6px 6px;
        }
        .participants-table th {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: center;
        }
        .participants-table td.center {
            text-align: center;
        }
        .participants-table td.left {
            text-align: left;
        }
        .footer {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .footer table {
            width: 80%;
            margin: 0 auto;
        }
        .footer td {
            padding: 10px;
            text-align: center;
        }
        .signature-section {
            margin-top: 30px;
        }
        .signature-section table {
            width: 100%;
        }
        .signature-section td {
            padding: 20px;
            vertical-align: top;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }
        .print-btn:hover {
            background-color: #0056b3;
        }
        @media print {
            body {
                background-color: white;
                margin: 0;
            }
            .report-container {
                box-shadow: none;
                margin: 0;
                padding: 20px;
            }
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print Report</button>
    
    <div class="report-container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="header-logo">
                    <?php if (!empty($logo_web_url)) { ?>
                        <img src="<?php echo $logo_web_url; ?>" alt="Festival Logo">
                    <?php } ?>
                </div>
                <div class="header-text">
                    <h1><?php echo $fest_title; ?></h1>
                    <?php if (!empty($venue)) { ?><div class="venue"><?php echo $venue; ?></div><?php } ?>
                    <?php if (!empty($date_range)) { ?><div class="dates"><?php echo $date_range; ?></div><?php } ?>
                </div>
            </div>
        </div>

        <!-- Registration Details Heading -->
        <h2 class="report-title">Registration Details</h2>

        <!-- School Details -->
        <div class="school-details">
            <table>
                <tr>
                    <td class="label" style="width: 17%;">School Code :</td>
                    <td style="width: 28%;"><?php echo htmlspecialchars($school_details[0]['school_code']); ?></td>
                    <td class="label" style="width: 16%;">School Name :</td>
                    <td style="width: 39%; font-weight: bold;"><?php echo htmlspecialchars($school_details[0]['school_name']); ?></td>
                </tr>
                <tr>
                    <td class="label">Sub District :</td>
                    <td><?php echo htmlspecialchars($school_details[0]['sub_district_name'] ?? ''); ?></td>
                    <td class="label">School Type :</td>
                    <td><?php echo $school_type; ?></td>
                </tr>
                <tr>
                    <td class="label">Standard :</td>
                    <td>From : <?php echo htmlspecialchars($school_details[0]['class_start']); ?> &nbsp;&nbsp; To : <?php echo htmlspecialchars($school_details[0]['class_end']); ?></td>
                    <td class="label">School Email :</td>
                    <td><?php echo htmlspecialchars($school_details[0]['school_email'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label">Headmaster :</td>
                    <td><?php echo htmlspecialchars($school_details[0]['hm_name'] ?? '-'); ?></td>
                    <td class="label">Mobile :</td>
                    <td><?php echo htmlspecialchars($school_details[0]['hm_phone'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label">Principal :</td>
                    <td><?php echo htmlspecialchars($school_details[0]['principal_name'] ?? '-'); ?></td>
                    <td class="label">Mobile :</td>
                    <td><?php echo htmlspecialchars($school_details[0]['principal_phone'] ?? '-'); ?></td>
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
                    <td class="label" style="vertical-align: top;">Team Manager(s) :</td>
                    <td style="vertical-align: top;"><?php echo $tm_name_str; ?></td>
                    <td class="label" style="vertical-align: top;">Mobile :</td>
                    <td style="vertical-align: top;"><?php echo $tm_phone_str; ?></td>
                </tr>
                <tr>
                    <td class="label" style="vertical-align: middle;">Total number of students :</td>
                    <td colspan="3" style="padding: 0px;">
                        <table style="width: 100%; border-collapse: collapse; text-align: center; margin: 0; font-size: 13px;">
                            <tr style="background-color: #fafafa;">
                                <td style="border: 1px solid #777; padding: 5px; font-weight: bold; width: 14%;">LP</td>
                                <td style="border: 1px solid #777; padding: 5px; font-weight: bold; width: 14%;">UP</td>
                                <td style="border: 1px solid #777; padding: 5px; font-weight: bold; width: 14%;">HS</td>
                                <td style="border: 1px solid #777; padding: 5px; font-weight: bold; width: 14%;">HSS</td>
                                <td style="border: 1px solid #777; padding: 5px; font-weight: bold; width: 16%;">VHSE</td>
                                <td style="border: 1px solid #777; padding: 5px; font-weight: bold; width: 28%; background-color: #e9ecef;">Total</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #777; padding: 5px;"><?php echo (int)@$school_details[0]['strength_lp']; ?></td>
                                <td style="border: 1px solid #777; padding: 5px;"><?php echo (int)@$school_details[0]['strength_up']; ?></td>
                                <td style="border: 1px solid #777; padding: 5px;"><?php echo (int)@$school_details[0]['strength_hs']; ?></td>
                                <td style="border: 1px solid #777; padding: 5px;"><?php echo (int)@$school_details[0]['strength_hss']; ?></td>
                                <td style="border: 1px solid #777; padding: 5px;"><?php echo (int)@$school_details[0]['strength_vhss']; ?></td>
                                <td style="border: 1px solid #777; padding: 5px; font-weight: bold; background-color: #f4f4f4;"><?php echo (int)@$school_details[0]['total_strength']; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Participants Table -->
        <table class="participants-table">
            <thead>
                <tr>
                    <th style="width: 5%;">Sl.No</th>
                    <th style="width: 10%;">Admn No.</th>
                    <th style="width: 25%;">Name of participant</th>
                    <th style="width: 7%;">Class</th>
                    <th style="width: 9%;">Boy / Girl</th>
                    <th style="width: 8.8%;">Item 1</th>
                    <th style="width: 8.8%;">Item 2</th>
                    <th style="width: 8.8%;">Item 3</th>
                    <th style="width: 8.8%;">Item 4</th>
                    <th style="width: 8.8%;">Item 5</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($participant_details) && is_array($participant_details)) {
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
                            <td class="center"><?php echo $count; ?></td>
                            <td class="center"><?php echo htmlspecialchars($participant_details[$j]['admn_no'] ?? ''); ?></td>
                            <td class="left"><?php echo htmlspecialchars($participant_details[$j]['participant_name'] ?? ''); ?></td>
                            <td class="center"><?php echo htmlspecialchars($participant_details[$j]['class'] ?? ''); ?></td>
                            <td class="center"><?php echo (($participant_details[$j]['gender'] ?? '') == 'B') ? 'Boy' : 'Girl'; ?></td>
                            <td class="center"><?php echo $it1; ?></td>
                            <td class="center"><?php echo $it2; ?></td>
                            <td class="center"><?php echo $it3; ?></td>
                            <td class="center"><?php echo $it4; ?></td>
                            <td class="center"><?php echo $it5; ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="10" style="text-align:center; padding: 12px;">No registered participants found for this school.</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <div style="text-align: center; font-size: 14px; margin-bottom: 25px;">
                Certified that above details has been verified and found correct
            </div>
            
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="text-align: left; width: 35%; border: none; font-size: 14px; vertical-align: bottom;">
                        Place : ..............................<br><br>
                        Date &nbsp;: .............................
                    </td>
                    <td style="text-align: center; width: 30%; border: none; font-size: 14px; vertical-align: bottom;">
                        (School Seal)
                    </td>
                    <td style="text-align: right; width: 35%; border: none; font-size: 14px; vertical-align: bottom;">
                        Principal / Headmaster
                    </td>
                </tr>
            </table>
            
            <div style="text-align: left; margin-top: 35px; font-size: 14px;">
                <strong>Name & Signature of Team Manager :</strong> ........................................................................
            </div>
        </div>
    </div>
</body>
</html>
