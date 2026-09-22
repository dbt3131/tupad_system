<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<table border="1">
    <thead>
        <tr>
            <th colspan="22" style="background-color: #1e3a8a; color: #ffffff; font-size: 14px; text-align: center; font-weight: bold;">
                DOLE TUPAD IMPLEMENTATION STATUS REPORT
            </th>
        </tr>
        <?php if (!empty($start_date) && !empty($end_date)): ?>
        <tr>
            <th colspan="22" style="text-align: center; font-style: italic;">
                Period: <?= html_escape($start_date); ?> to <?= html_escape($end_date); ?>
            </th>
        </tr>
        <?php endif; ?>
        <tr style="background-color: #334155; color: #ffffff; text-align: center; font-weight: bold;">
            <th rowspan="2">ADL NO</th>
            <th rowspan="2">REFERENCE NO</th>
            <th rowspan="2">SPONSOR</th>
            <th rowspan="2">PROPONENT</th>
            <th rowspan="2">AREA OF IMPLEMENTATION</th>
            <th colspan="3" style="background-color: #1e40af;">TARGET</th>
            <th colspan="3" style="background-color: #166534;">IMPLEMENTED</th>
            <th colspan="2" style="background-color: #854d0e;">FOR PAYROLL SUBMISSION</th>
            <th colspan="2" style="background-color: #6b21a8;">FOR GSIS ENROLLMENT</th>
            <th colspan="4" style="background-color: #0369a1;">FOR IMPLEMENTATION</th>
            <th colspan="2" style="background-color: #991b1b;">NOT YET IMPLEMENTED</th>
            <th rowspan="2">REMARKS</th>
        </tr>
        <tr style="background-color: #f1f5f9; text-align: center; font-weight: bold;">
            <th>BENEFICIARIES</th>
            <th>COORDINATED SUBSIDY</th>
            <th>NO OF WORK DAYS</th>
            <th>BENEFICIARIES</th>
            <th>AMOUNT (WAGES)</th>
            <th>PAYOUT DATE</th>
            <th>NO OF BENEFICIARIES</th>
            <th>AMOUNT (WAGES)</th>
            <th>NO OF BENEFICIARIES</th>
            <th>AMOUNT (WAGES)</th>
            <th>NO OF BENEFICIARIES</th>
            <th>AMOUNT (WAGES)</th>
            <th>EMPLOYMENT PERIOD</th>
            <th>TARGET PAYOUT</th>
            <th>NO OF BENEFICIARIES</th>
            <th>AMOUNT WAGES</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($report_data)): ?>
            <?php foreach ($report_data as $row): ?>
                <?php 
                $no_of_days  = $row['no_of_days'] ?? 0;
                $wage_amount = $row['wage_amount'] ?? 0;
                $target      = $row['target'] ?? 0;

                // 1. GSIS Check logic
                $is_gsis_empty = (empty($row['gsis_enrollment_date']) || $row['gsis_enrollment_date'] == '0000-00-00');
                $is_ppe_empty  = (empty($row['ppes_date_issued']) || $row['ppes_date_issued'] == '0000-00-00');
                
                if ($is_gsis_empty && !$is_ppe_empty) {
                    $gsis_beneficiaries = $row['ppes_count'] ?? 0;
                    $gsis_amount = ($gsis_beneficiaries * $no_of_days) * $wage_amount;
                } else {
                    $gsis_beneficiaries = '';
                    $gsis_amount = '';
                }

                $completed_period = $row['completed_employment_amount'] ?? '';
                $is_completed_valid = (!empty($completed_period) && $completed_period !== '0000-00-00');

                // 3. For Payroll Submission Logic
                if ($is_completed_valid) {
                    $ppes_beneficiaries = $row['ongoing_implementation_benefs'] ?? 0;
                    $ppes_amount = ($ppes_beneficiaries > 0) ? ($ppes_beneficiaries * $no_of_days) * $wage_amount : '';
                } else {
                    $ppes_beneficiaries = '';
                    $ppes_amount = '';
                }

                // 4. Implemented Column Logic
                $raw_payout_date_impl = $row['payout_date'] ?? '';
                $is_valid_past_payout = false;

                if (!empty($raw_payout_date_impl) && $raw_payout_date_impl !== '0000-00-00') {
                    $current_date_obj = new DateTime('now');
                    $payout_date_obj = new DateTime($raw_payout_date_impl);
                    if ($payout_date_obj <= $current_date_obj) {
                        $is_valid_past_payout = true;
                    }
                }

                if ($is_valid_past_payout && !$is_completed_valid) {
                    $implemented_beneficiaries = $row['gsis_enrollment_benefs'] ?? '';
                    $implemented_amount = (!empty($implemented_beneficiaries) && $implemented_beneficiaries > 0) ? ($implemented_beneficiaries * $no_of_days) * $wage_amount : '';
                    $implemented_payout_date = $raw_payout_date_impl;
                } else {
                    $implemented_beneficiaries = '';
                    $implemented_amount = '';
                    $implemented_payout_date = '';
                }

                // 2. FOR IMPLEMENTATION / ON-GOING IMPLEMENTATION 
                $gsis_date     = $row['gsis_enrollment_date'] ?? '0000-00-00';
                $is_gsis_valid = (!empty($gsis_date) && $gsis_date !== '0000-00-00');

                if ($is_gsis_valid && !$is_valid_past_payout && !$is_completed_valid) {
                    $ongoing_start = $row['ongoing_implementation_start_date'] ?? '0000-00-00';
                    $ongoing_end   = $row['ongoing_implementation_end_date'] ?? '0000-00-00';

                    $is_special_ongoing = ($ongoing_start == '0000-00-00' && $ongoing_end == '0000-00-00');

                    if ($is_special_ongoing) {
                        $ongoing_beneficiaries = $row['gsis_enrollment_benefs'] ?? 0;
                    } else {
                        $ongoing_beneficiaries = $row['ongoing_implementation_benefs'] ?? 0;
                    }

                    $ongoing_amount    = ($ongoing_beneficiaries > 0) ? ($ongoing_beneficiaries * $no_of_days) * $wage_amount : '';
                    $employment_period = $row['orientation_employment_period'] ?? '';
                    
                    $raw_payout_date   = $row['payout_date'] ?? '';
                    $target_payout     = (!empty($raw_payout_date) && $raw_payout_date !== '0000-00-00') ? $raw_payout_date : '';
                } else {
                    $ongoing_beneficiaries = '';
                    $ongoing_amount        = '';
                    $employment_period     = '';
                    $target_payout         = '';
                }
                
                // 5. NOT YET IMPLEMENTED Logic
                $is_empty_val = function($val) {
                    return empty($val) || $val === '0000-00-00' || $val === '0.00' || $val == 0;
                };

                $is_all_empty = (
                    $is_empty_val($row['ppes_date_issued'] ?? '') &&
                    $is_empty_val($row['orientation_date'] ?? '') &&
                    $is_empty_val($row['gsis_enrollment_date'] ?? '') &&
                    $is_empty_val($row['ongoing_implementation_start_date'] ?? '') &&
                    $is_empty_val($row['ongoing_implementation_end_date'] ?? '') &&
                    $is_empty_val($row['completed_employment_amount'] ?? '') &&
                    $is_empty_val($row['payout_amount'] ?? '') &&
                    $is_empty_val($row['payout_date'] ?? '')
                );

                if ($is_all_empty) {
                    $not_yet_beneficiaries = $target;
                    $not_yet_amount = ($target * $no_of_days) * $wage_amount;
                } else {
                    $not_yet_beneficiaries = $row['not_yet_beneficiaries'] ?? '';
                    $not_yet_amount = $row['not_yet_amount'] ?? '';
                }
                ?>
                <tr>
                    <td style="text-align: center; mso-number-format:'\@';"><?= html_escape($row['adl_no']); ?></td>
                    <td><?= html_escape($row['implementation_reference_no']); ?></td>
                    <td><?= html_escape($row['implementation_sponsor']); ?></td>
                    <td><?= html_escape($row['p_name']); ?></td>
                    <td><?= html_escape($row['area_description']); ?></td>
                    
                    <!-- TARGET -->
                    <td style="text-align: center;"><?= $target; ?></td>
                    <td style="text-align: right;"><?= $row['subsidy_cost'] ?? 0; ?></td>
                    <td style="text-align: center;"><?= $no_of_days; ?></td>
                    
                    <!-- IMPLEMENTED -->
                    <td style="text-align: center;"><?= ($implemented_beneficiaries !== '' && $implemented_beneficiaries > 0) ? $implemented_beneficiaries : ''; ?></td>
                    <td style="text-align: right;"><?= ($implemented_amount !== '' && $implemented_amount > 0) ? $implemented_amount : ''; ?></td>
                    <td style="text-align: center;"><?= html_escape($implemented_payout_date); ?></td>
                    
                    <!-- FOR PAYROLL SUBMISSION -->
                    <td style="text-align: center;"><?= ($ppes_beneficiaries !== '' && $ppes_beneficiaries > 0) ? $ppes_beneficiaries : ''; ?></td>
                    <td style="text-align: right;"><?= ($ppes_amount !== '' && $ppes_amount > 0) ? $ppes_amount : ''; ?></td>
                    
                    <!-- FOR GSIS ENROLLMENT -->
                    <td style="text-align: center;"><?= ($gsis_beneficiaries !== '' && $gsis_beneficiaries > 0) ? $gsis_beneficiaries : ''; ?></td>
                    <td style="text-align: right;"><?= ($gsis_amount !== '' && $gsis_amount > 0) ? $gsis_amount : ''; ?></td>
                    
                    <!-- FOR IMPLEMENTATION -->
                    <td style="text-align: center;"><?= ($ongoing_beneficiaries !== '' && $ongoing_beneficiaries > 0) ? $ongoing_beneficiaries : ''; ?></td>
                    <td style="text-align: right;"><?= ($ongoing_amount !== '' && $ongoing_amount > 0) ? $ongoing_amount : ''; ?></td>
                    <td style="text-align: center;"><?= html_escape($employment_period); ?></td>
                    <td style="text-align: center;"><?= html_escape($target_payout); ?></td>
                    
                    <!-- NOT YET IMPLEMENTED -->
                    <td style="text-align: center;"><?= ($not_yet_beneficiaries !== '' && $not_yet_beneficiaries > 0) ? $not_yet_beneficiaries : ''; ?></td>
                    <td style="text-align: right;"><?= ($not_yet_amount !== '' && $not_yet_amount > 0) ? $not_yet_amount : ''; ?></td>
                    
                    <!-- REMARKS -->
                    <td><?= html_escape($row['remarks'] ?? ''); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="22" style="text-align: center;">No records found for the selected date period.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>