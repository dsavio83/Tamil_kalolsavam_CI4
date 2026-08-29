<!DOCTYPE html>
<html>
<head>
    <title>School Registration Report</title>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            padding: 0;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.main-table {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #000;
        }
        th {
            background-color: #f2f2f2;
        }
        .signature-section {
            margin-top: 50px;
            width: 100%;
        }
        .signature-section td {
            border: none;
            padding: 10px;
        }
        .print-button {
            text-align: center;
            margin: 20px 0;
        }
        .print-button button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .print-button button:hover {
            background-color: #45a049;
        }
        @media print {
            .print-button {
                display: none;
            }
            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="print-button">
        <button onclick="window.print()">Print Report</button>
    </div>

    <div class="header">
        <h2>Tamil Kalaivizha 2025-26</h2>
        <h3><?php echo @$school_details[0]['sub_district_name'] . ' Subdistrict'; ?></h3>
        <hr/>
    </div>

    <table class="main-table">
        <tr>
            <td width="50%">School Code:<strong> <?php echo @$school_details[0]['school_code']; ?></strong></td>

            <td width="50%">School Name:<strong> <?php @print($school_details[0]['school_name']) ?></strong></td>

        </tr>
        <tr>
            <td>Standard:
				From: <strong> <?php echo @$school_details[0]['class_start']; ?></strong>
				To: <strong><?php echo @$school_details[0]['class_end']; ?></strong>
			</td>
           
            <td>School Type:
					<strong><?php
                if (@$school_details[0]['school_type'] == 'G')
                    $type = 'Government';
                else if (@$school_details[0]['school_type'] == 'A')
                    $type = 'Aided';
                else if (@$school_details[0]['school_type'] == 'U')
                    $type = 'Unaided';
                else
                    $type = '';
                echo $type;
                ?></strong>
			</td>
        </tr>
        
       
        <tr>
            <td>Headmaster:
				<strong><?php echo (@$school_details[0]['hm_name']) ? @$school_details[0]['hm_name'] : ''; ?></strong></td>
            <td>Phone:
				<strong><?php echo (@$school_details[0]['hm_phone']) ? @$school_details[0]['hm_phone'] : ''; ?></strong></td>
        </tr>
        <tr>
            <td>Principal:
				<strong><?php echo (@$school_details[0]['principal_name']) ? @$school_details[0]['principal_name'] : ''; ?></strong></td>
            <td>Phone:
				<strong><?php echo (@$school_details[0]['principal_phone']) ? @$school_details[0]['principal_phone'] : ''; ?></strong></td>
        </tr>
		</table>
        <tr>
            <td valign="top">Team Managers:
                <?php
                if (@$school_details[0]['teachers'] != '') {
                    $teachers = explode("#@#", $school_details[0]['teachers']);
                    echo '<table border="0" cellpadding="5" cellspacing="0" width="100%">';
                    for ($i = 0; $i < count($teachers); $i++) {
                        $teachers_details = explode('#$#', $teachers[$i]);
                        if (count($teachers_details) > 1) {
                            echo '<tr>
                                <td width="50%"> Name of Team Manager: <strong>' . @$teachers_details[0] . '</strong></td>
                                <td width="50%">Phone: <strong>' . @$teachers_details[1] . '</strong></td>
                            </tr>';
                        }
                    }
                    echo '</table>';
                }
                ?>
            </td>
        </tr>
    </table>

    <?php if (count(@$participant_details) > 0) { ?>
    <h3>Participant Details</h3>
    <table class="main-table">
        <tr>
            <th>Sl No</th>
            <th>Photo</th>
            <th>Participant Name</th>
            <th>Class</th>
            <th>Gender</th>
            <th>Adm No</th>
            <th>Item Code</th>
        </tr>
        <?php
        $i = 1;
        foreach ($participant_details as $participant) {
            // Get participant photo
            $participant_photo = '';
            if (!empty($participant['admn_no']) && !empty($school_details[0]['school_code'])) {
                $this->load->model('pictures/Photos_Model');
                $photo_data = $this->Photos_Model->get_Photo_With_Thumbnail(
                    $school_details[0]['school_code'] . "_" . $participant['admn_no'],
                    $school_details[0]['school_code']
                );
                if ($photo_data && ($photo_data['thumbnail'] || $photo_data['full'])) {
                    $participant_photo = $photo_data['thumbnail'] ? $photo_data['thumbnail'] : $photo_data['full'];
                }
            }

            // Handle item codes array
            $item_codes = '';
            if (isset($participant['item_code']) && is_array($participant['item_code'])) {
                $item_codes = implode(', ', $participant['item_code']);
            } elseif (isset($participant['item_code'])) {
                $item_codes = $participant['item_code'];
            }
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td style="text-align: center;">
                    <?php if ($participant_photo): ?>
                        <img src="<?php echo $participant_photo; ?>" alt="Photo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;">
                    <?php else: ?>
                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #f8f9fa; border: 1px dashed #dee2e6; display: inline-flex; align-items: center; justify-content: center; font-size: 10px; color: #adb5bd;">
                            No Photo
                        </div>
                    <?php endif; ?>
                </td>
                <td><?php echo $participant['participant_name']; ?></td>
                <td><?php echo $participant['class']; ?></td>
                <td><?php echo ($participant['gender'] == 'B') ? 'Boy' : 'Girl'; ?></td>
                <td><?php echo $participant['admn_no']; ?></td>
                <td><?php echo $item_codes; ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php } ?>

    <?php if (count(@$fees_details) > 0) { ?>
    <!-- <h3>Fees Details</h3>
    <table class="main-table">
        <tr>
            <th>Sl No</th>
            <th>Item Code</th>
            <th>Item Name</th>
            <th>Category</th>
            <th>Participants</th>
            <th>Fee</th>
        </tr>
        <?php
        $i = 1;
        $total = 0;
        foreach ($fees_details as $fees) {
            $total += $fees['fee'];
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $fees['item_code']; ?></td>
                <td><?php echo $fees['item_name']; ?></td>
                <td><?php echo $fees['category_name']; ?></td>
                <td><?php echo $fees['participants']; ?></td>
                <td align="right"><?php echo $fees['fee']; ?></td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td colspan="5" align="right"><strong>Total</strong></td>
            <td align="right"><strong><?php echo $total; ?></strong></td>
        </tr>
    </table> -->
    <?php } ?>

    <table class="signature-section">
        <tr>
            <td colspan="3" align="center">
                Certified that above details has been verified and found correct
                <br /><br /><br />
            </td>
        </tr>
        <tr>
            <td align="left" width="30%">
                Place&nbsp;:&nbsp;..............................<br /><br />
                Date&nbsp;&nbsp;:&nbsp;.............................
            </td>
            <td align="center" width="40%">
                <br /><br />
                (School Seal)
            </td>
            <td align="right" width="30%">
                <br /><br />
                Principal/Headmaster
            </td>
        </tr>
    </table>
</body>
</html>
