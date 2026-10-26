<style type="text/css">
<!--
    .style_title {
        font-family: 'TAU-Pallai', 'Segoe UI', Tahoma, sans-serif;
        font-size: 14px;
        font-weight: bold;
        color: #1e3a8a;
    }
    .style_subtitle {
        font-family: 'TAU-Pallai', 'Segoe UI', Tahoma, sans-serif;
        font-size: 11px;
        font-weight: bold;
        color: #475569;
    }
    .style_footer {
        font-family: 'TAU-Pallai', 'Segoe UI', Tahoma, sans-serif;
        font-size: 8px;
        color: #64748b;
    }
    .pdf_list_table {
        border-collapse: collapse;
        width: 100%;
    }
    .pdf_list_table th {
        font-family: 'TAU-Pallai', 'Segoe UI', Tahoma, sans-serif;
        font-size: 10px;
        font-weight: bold;
        color: #1e293b;
        background-color: #e2e8f0;
        border: 1px solid #94a3b8;
        padding: 5px 4px;
    }
    .pdf_list_table td {
        font-family: 'TAU-Pallai', 'Segoe UI', Tahoma, sans-serif;
        font-size: 10px;
        color: #0f172a;
        border: 1px solid #cbd5e1;
        padding: 4px 5px;
    }
    .table_row_first {
        background-color: #ffffff;
    }
    .table_row_second {
        background-color: #f8fafc;
    }
-->
</style>
<page backtop="26mm" backbottom="14mm" backleft="10mm" backright="10mm">
    <page_header>
        <table style="width: 100%;" cellpadding="0" cellspacing="0">
            <tr>
                <td style="text-align: center; width: 100%; height: 22px;">
                    <span class="style_title">Admin Users - Username &amp; Password Directory</span>
                </td>
            </tr>
            <tr>
                <td style="text-align: center; width: 100%;">
                    <span class="style_subtitle">
                        <?php
                        $heading_parts = array();
                        if (!empty($user_type_name)) {
                            $heading_parts[] = $user_type_name;
                        }
                        if (!empty($sub_district_name)) {
                            $heading_parts[] = $sub_district_name . ' Sub-District';
                        } elseif (!empty($district_name)) {
                            $heading_parts[] = $district_name . ' District';
                        }
                        if (!empty($heading_parts)) {
                            echo implode(' &bull; ', $heading_parts);
                        } else {
                            echo 'State, District &amp; Sub-District Accounts';
                        }
                        ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td style="width: 100%; padding-top: 4px;"><hr style="color: #cbd5e1; height: 1px;" /></td>
            </tr>
        </table>
    </page_header>
    <page_footer>
        <table style="width: 100%;" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 100%;"><hr style="color: #cbd5e1; height: 1px;" /></td>
            </tr>
            <tr>
                <td style="text-align: center; width: 100%;" class="style_footer">
                    Tamil Kalolsavam 2025 &bull; Confidential Administrative Credentials &bull; Generated on <?php echo date("F j, Y, g:i a"); ?>
                </td>
            </tr>
        </table>
    </page_footer>    
    <table class="pdf_list_table" cellspacing="0" cellpadding="0">
        <tr>
            <th align="center" width="35">Sl.No.</th>
            <th align="left" width="140">User Name</th>
            <th align="left" width="130">Gen. Password</th>
            <?php if (empty($user_type_name)) { ?>
                <th align="left" width="110">User Type</th>
            <?php } ?>
            <?php if (empty($district_name)) { ?>
                <th align="left" width="125">District</th>
            <?php } ?>
            <?php if (empty($sub_district_name)) { ?>
                <th align="left" width="130">Sub-District</th>
            <?php } ?>
        </tr>
        <?php
        $i = 1;
        if (!empty($retvalue) && is_array($retvalue)) {
            foreach ($retvalue as $value) {
                $class_name = ($i % 2 == 0) ? 'table_row_first' : 'table_row_second';
                if ($value['user_type'] == 1) {
                    $user_type = 'State Admin';
                } else if ($value['user_type'] == 2) {
                    $user_type = 'District Admin';
                } else if ($value['user_type'] == 3) {
                    $user_type = 'Sub-district Admin';
                } else {
                    $user_type = 'School Admin';
                }
                ?>
                <tr>
                    <td align="center" class="<?php echo $class_name; ?>"><?php echo $i; ?></td>
                    <td align="left" class="<?php echo $class_name; ?>"><b><?php echo htmlspecialchars($value['user_name'] ?? ''); ?></b></td>
                    <td align="left" class="<?php echo $class_name; ?>" style="font-family: monospace; font-weight: bold;"><?php echo htmlspecialchars($value['generated_password'] ?: '-'); ?></td>
                    <?php if (empty($user_type_name)) { ?>
                        <td align="left" class="<?php echo $class_name; ?>"><?php echo $user_type; ?></td>
                    <?php } ?>
                    <?php if (empty($district_name)) { ?>
                        <td align="left" class="<?php echo $class_name; ?>"><?php echo htmlspecialchars($value['rev_district_name'] ?: '-'); ?></td>
                    <?php } ?>
                    <?php if (empty($sub_district_name)) { ?>
                        <td align="left" class="<?php echo $class_name; ?>"><?php echo htmlspecialchars($value['sub_district_name'] ?: '-'); ?></td>
                    <?php } ?>
                </tr>
                <?php
                $i++;
            }
        } else {
            $colspan = 3;
            if (empty($user_type_name)) $colspan++;
            if (empty($district_name)) $colspan++;
            if (empty($sub_district_name)) $colspan++;
            ?>
            <tr>
                <td colspan="<?php echo $colspan; ?>" align="center" style="padding: 15px; color: #64748b;">No admin records found.</td>
            </tr>
            <?php
        }
        ?>
    </table>
</page>