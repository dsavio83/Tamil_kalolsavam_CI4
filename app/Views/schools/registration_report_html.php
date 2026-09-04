<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo get_festival_title(); ?> - Registration Report</title>
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
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 18px;
            font-weight: normal;
        }
        .school-details {
            margin-bottom: 30px;
        }
        .school-details table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .school-details td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .school-details .label {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 20%;
        }
        .participants-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .participants-table th,
        .participants-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        .participants-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .participants-table td.center {
            text-align: center;
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
            <h1><?php echo get_festival_title(); ?></h1>
            <h2><?php echo @$school_details[0]['sub_district_name'] . ' Subdistrict'; ?></h2>
        </div>

        <!-- School Details -->
        <div class="school-details">
            <table>
                <tr>
                    <td class="label">School Code:</td>
                    <td><?php echo @$school_details[0]['school_code']; ?></td>
                    <td class="label">School Name:</td>
                    <td><?php echo @$school_details[0]['school_name']; ?></td>
                </tr>
                <tr>
                    <td class="label">Standard:</td>
                    <td>From: <?php echo @$school_details[0]['class_start']; ?></td>
                    <td class="label">To:</td>
                    <td><?php echo @$school_details[0]['class_end']; ?></td>
                </tr>
                <tr>
                    <td class="label">School Type:</td>
                    <td colspan="3">
                        <?php
                        if (@$school_details[0]['school_type'] == 'G')
                            $type = 'Government';
                        else if (@$school_details[0]['school_type'] == 'A')
                            $type = 'Aided';
                        else if (@$school_details[0]['school_type'] == 'U')
                            $type = 'Unaided';
                        else
                            $type = '';
                        echo $type;
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="label">Phone:</td>
                    <td colspan="3"><?php echo (@$school_details[0]['school_phone']) ? @$school_details[0]['school_phone'] : ''; ?></td>
                </tr>
                <tr>
                    <td class="label">Headmaster:</td>
                    <td><?php echo (@$school_details[0]['hm_name']) ? @$school_details[0]['hm_name'] : ''; ?></td>
                    <td class="label">Phone:</td>
                    <td><?php echo (@$school_details[0]['hm_phone']) ? @$school_details[0]['hm_phone'] : ''; ?></td>
                </tr>
                <tr>
                    <td class="label">Principal:</td>
                    <td><?php echo (@$school_details[0]['principal_name']) ? @$school_details[0]['principal_name'] : ''; ?></td>
                    <td class="label">Phone:</td>
                    <td><?php echo (@$school_details[0]['principal_phone']) ? @$school_details[0]['principal_phone'] : ''; ?></td>
                </tr>
                <tr>
                    <td class="label" style="vertical-align: top;">Team Managers:</td>
                    <td colspan="3">
                        <?php
                        if (@$school_details[0]['teachers'] != '') {
                            $teachers = explode("#@#", $school_details[0]['teachers']);
                            echo '<table style="width: 100%; border: none;">';
                            for ($i = 0; $i < count($teachers); $i++) {
                                $teachers_details = explode('#$#', $teachers[$i]);
                                if (count($teachers_details) > 1) {
                                    echo '<tr><td style="border: none; padding: 2px;">' . @$teachers_details[0] . '</td><td style="border: none; padding: 2px;">&nbsp;&nbsp;&nbsp;Phone: </td><td style="border: none; padding: 2px;">' . @$teachers_details[1] . '</td></tr>';
                                }
                            }
                            echo '</table>';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="label">Total Students:</td>
                    <td colspan="3">
                        LP <?php echo @$school_details[0]['strength_lp']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        UP <?php echo @$school_details[0]['strength_up']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        HS <?php echo @$school_details[0]['strength_hs']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        HSS <?php echo @$school_details[0]['strength_hss']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        VHSE <?php echo @$school_details[0]['strength_vhss']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Total <?php echo @$school_details[0]['total_strength']; ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Participants Table -->
        <table class="participants-table">
            <thead>
                <tr>
                    <th width="5%">Sl.No</th>
                    <th width="10%">Admn No.</th>
                    <th width="25%">Name of participant</th>
                    <th width="8%">Class</th>
                    <th width="10%">Boy / Girl</th>
                    <th width="42%">Item code</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($participant_details) && is_array($participant_details)) {
                    $count = 0;
                    for ($j = 0; $j < count($participant_details); $j++) {
                        $count++;
                        ?>
                        <tr>
                            <td class="center"><?php echo $count; ?></td>
                            <td class="center"><?php echo isset($participant_details[$j]['admn_no']) ? $participant_details[$j]['admn_no'] : ''; ?></td>
                            <td><?php echo isset($participant_details[$j]['participant_name']) ? $participant_details[$j]['participant_name'] : ''; ?></td>
                            <td class="center"><?php echo isset($participant_details[$j]['class']) ? $participant_details[$j]['class'] : ''; ?></td>
                            <td class="center"><?php echo (isset($participant_details[$j]['gender']) && $participant_details[$j]['gender'] == 'B') ? 'Boy' : 'Girl'; ?></td>
                            <td>
                                <?php
                                if (isset($participant_details[$j]['item_code']) && is_array($participant_details[$j]['item_code'])) {
                                    $cnt_item = 1;
                                    foreach ($participant_details[$j]['item_code'] as $items) {
                                        if ($cnt_item == 6) {
                                            echo '<br>';
                                        }
                                        echo @$items . '&nbsp;&nbsp;';
                                        $cnt_item++;
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="6" style="text-align:center;">No participants found</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <table>
                <tr>
                    <td colspan="3" style="text-align: center;">
                        Certified that above details has been verified and found correct
                    </td>
                </tr>
            </table>
            
            <div class="signature-section">
                <table>
                    <tr>
                        <td style="text-align: left; width: 30%;">
                            Place: ..............................<br><br>
                            Date: .............................
                        </td>
                        <td style="text-align: center; width: 40%;">
                            <br><br>
                            (School Seal)
                        </td>
                        <td style="text-align: right; width: 30%;">
                            <br><br>
                            Principal/Headmaster
                        </td>
                    </tr>
                </table>
            </div>
            
            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #333;">
                Name & Signature of Team Manager
            </div>
        </div>
    </div>
</body>
</html>
