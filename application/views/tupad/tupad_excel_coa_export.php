<?php
// Prevent caching for Excel download payload
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=COA_Quarterly_Report_" . date('Ymd') . ".xls");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>COA Quarterly Report</title>
</head>
<body>
    <table>
        <tr>
            <td colspan="19" style="font-weight: bold; font-size: 14px;">DEPARTMENT OF LABOR AND EMPLOYMENT</td>
        </tr>
        <tr>
            <td colspan="19" style="font-weight: bold; font-size: 12px;">CONSOLIDATED QUARTERLY REPORT ON GOVERNMENT PROJECT/PROGRAM/ACTIVITIES (PPA)</td>
        </tr>
        <tr>
            <td colspan="19" style="font-size: 10px; font-style: italic;">As of <?= date('F d, Y'); ?></td>
        </tr>
        <tr><td colspan="19"></td></tr>
    </table>

    <table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr style="background-color: #d9d9d9; text-align: center; font-weight: bold;">
                <th rowspan="2">Agency/Adress</th>
                <th rowspan="2">Project/Program/Activity Name</th>
                <th colspan="2">Location</th>
                <th rowspan="2">Physical Target</th>
                <th rowspan="2">No. of Days</th>
                <th rowspan="2">Total Project Cost</th>
                <th rowspan="2">Proposed Date to Start (DD/MM/YYYY)</th>
                <th rowspan="2">Actual Date Started (DD/MM/YYYY)</th>
                <th rowspan="2">No. of Extensions</th>
                <th rowspan="2">Target Completion Date (DD/MM/YYYY)</th>
                <th rowspan="2">Actual Date of Completion (DD/MM/YYYY)</th>
                <th colspan="3">Project Status this Quarter</th>
                <th rowspan="2">Total Cost Incurred to date</th>
                <th rowspan="2">Remarks</th>
                <th rowspan="2">Mode of Procurement</th>
                <th rowspan="2">Contractor (If applicable)</th>
            </tr>
            <tr style="background-color: #d9d9d9; text-align: center; font-weight: bold;">
                <th>Province</th>
                <th>LGU/Municipality</th>
                <th>Target Completion Date (DD/MM/YYYY)</th>
                <th>Actual Date of Completion (DD/MM/YYYY)</th>
                <th>% of Completion</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($report_data)): ?>
                <?php foreach ($report_data as $row): ?>
                    <tr>
                        <td>DOLE RO3</td>
                        <td>TUPAD</td>
                        <td><?= html_escape($row['province_name'] ?? ''); ?></td>
                        <td><?= html_escape($row['municipality_name'] ?? ''); ?></td>
                        <td style="text-align: center;"><?= html_escape($row['target'] ?? ''); ?></td>
                        <td style="text-align: center;"><?= html_escape($row['no_of_days'] ?? ''); ?></td>
                        <td style="text-align: right;"><?= !empty($row['adl_amount']) ? number_format((float)$row['adl_amount'], 2) : ''; ?></td>
                        <td style="text-align: center;"><?= !empty($row['ongoing_implementation_start_date']) ? date('d/m/Y', strtotime($row['ongoing_implementation_start_date'])) : ''; ?></td>
                        <td style="text-align: center;"><?= !empty($row['ongoing_implementation_start_date']) ? date('d/m/Y', strtotime($row['ongoing_implementation_start_date'])) : ''; ?></td>
                        <td></td>
                        <td style="text-align: center;"><?= !empty($row['ongoing_implementation_end_date']) ? date('d/m/Y', strtotime($row['ongoing_implementation_end_date'])) : ''; ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><?= html_escape($row['remarks'] ?? ''); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="19" style="text-align: center; color: #555;">No records found for the selected period.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Signature Block for Excel Download Only -->
    <table>
        <tr><td colspan="19"></td></tr>
        <tr><td colspan="19"></td></tr>
        <tr>
            <td colspan="3" style="font-weight: bold; font-size: 10px;">Prepared by:</td>
            <td colspan="6"></td>
            <td colspan="3" style="font-weight: bold; font-size: 10px;">Reviewed by:</td>
            <td colspan="7"></td>
        </tr>
        <tr><td colspan="19"></td></tr>
        <tr>
            <td colspan="4" style="font-weight: bold; text-align: center; border-bottom: 1px solid #000; text-transform: uppercase;">
                <?= html_escape($user_fullname); ?>
            </td>
            <td colspan="5"></td>
            <td colspan="4" style="font-weight: bold; text-align: center; border-bottom: 1px solid #000; text-transform: uppercase;">
                AURITA L. LAXAMANA
            </td>
            <td colspan="6"></td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center; font-size: 9px; color: #555;"><?= html_escape($user_position); ?></td>
            <td colspan="5"></td>
            <td colspan="4" style="text-align: center; font-size: 9px; color: #555;">Chief LEO, TSSD II</td>
            <td colspan="6"></td>
        </tr>
    </table>
</body>
</html>