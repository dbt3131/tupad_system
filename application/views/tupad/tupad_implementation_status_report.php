<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DOLE TUPAD</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #1e3a8a;
            --primary-hover: #172554;
            --accent-color: #2563eb;
            --bg-body: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --card-border: #e2e8f0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            overflow-x: hidden;
        } 

        #sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        #main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #sidebar.collapsed {
            margin-left: calc(var(--sidebar-width) * -1);
        }

        #main-content.expanded {
            margin-left: 0;
        }

        /* Custom Header Group Colors */
        .th-basic { background-color: #334155 !important; color: #ffffff !important; }      /* Slate Dark */
        .th-target { background-color: #1e40af !important; color: #ffffff !important; }     /* Deep Blue */
        .th-implemented { background-color: #166534 !important; color: #ffffff !important; }/* Forest Green */
        .th-payroll { background-color: #854d0e !important; color: #ffffff !important; }    /* Dark Amber/Brown */
        .th-gsis { background-color: #6b21a8 !important; color: #ffffff !important; }       /* Deep Purple */
        .th-ongoing { background-color: #0369a1 !important; color: #ffffff !important; }    /* Ocean Blue */
        .th-notyet { background-color: #991b1b !important; color: #ffffff !important; }     /* Dark Red */
        .th-remarks { background-color: #475569 !important; color: #ffffff !important; }    /* Muted Slate */

        @media (max-width: 991.98px) {
            #sidebar {
                transform: translateX(-100%);
                margin-left: 0 !important;
            }
            #sidebar.show-mobile {
                transform: translateX(0);
            }
            #main-content {
                margin-left: 0 !important;
            }
        }

        @media print {
            body { background-color: #ffffff; }
            #sidebar, .top-navbar, .no-print { display: none !important; }
            #main-content { margin-left: 0 !important; }
            
            .th-basic, .th-target, .th-implemented, .th-payroll, .th-gsis, .th-ongoing, .th-notyet, .th-remarks {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    <?php $this->load->view('templates/navbar'); ?>
    <?php $this->load->view('templates/sidebar'); ?>

    <div id="main-content">
        <main class="p-3 p-md-4 flex-grow-1">
            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 no-print">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-dark bg-opacity-10 text-dark px-2 py-1 fw-semibold" style="font-size: 0.7rem;">DOLE RO3</span>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Department of Labor and Employment</span>
                    </div>
                    <h3 class="fw-bold mb-1 text-dark" style="font-size: 1.25rem;">TUPAD Implementation Status</h3>
                    <p class="text-muted small mb-0"><i class="bi bi-calendar3 me-1"></i> As of <?= date('F d, Y'); ?></p>
                </div>
                <div>
                    <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#howItWorksModal">
                        <i class="bi bi-question-circle me-1"></i> How This Page Works
                    </button>
                </div>
            </div>

           <!-- Filter Form -->
            <div class="card shadow-sm mb-4 no-print border-0">
                <div class="card-body">
                    <form method="get" action="<?= site_url('tupad_report/tupad_implementation_status_report'); ?>" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label fw-semibold small">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= html_escape($start_date ?? ''); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label fw-semibold small">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= html_escape($end_date ?? ''); ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label for="province" class="form-label fw-semibold small">Province</label>
                            <select class="form-select" id="province" name="province">
                                <option value="">-- All Provinces --</option>
                                <option value="summary" <?= (isset($selected_province) && $selected_province == 'summary') ? 'selected' : ''; ?>>SUMMARY (Region 3)</option>
                                <?php if (!empty($provinces)): ?>
                                    <?php foreach ($provinces as $prov): ?>
                                        <option value="<?= $prov['provCode']; ?>" <?= (isset($selected_province) && $selected_province == $prov['provCode']) ? 'selected' : ''; ?>>
                                            <?= html_escape($prov['provDesc']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-dark w-100"><i class="bi bi-filter me-1"></i> Generate</button>
                            <a href="<?= site_url('tupad_report/export_excel?start_date=' . $start_date . '&end_date=' . $end_date . '&province=' . ($selected_province ?? '')); ?>" class="btn btn-dark">
                                Excel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

<div class="alert alert-secondary py-2 small mb-3 no-print">
    <i class="bi bi-info-circle me-1"></i> <strong>Note:</strong> All completed and fully paid implementations are automatically hidden from this report.
</div>


            <!-- Report Results Table -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle text-nowrap" style="font-size: 0.8rem;">
                            <thead class="text-center align-middle">
                                <?php if (isset($selected_province) && $selected_province === 'summary'): ?>
                                    <!-- SUMMARY HEADERS -->
                                    <tr>
                                        <th rowspan="2" class="th-basic">PROVINCE</th>
                                        <th colspan="3" class="th-target">TARGET</th>
                                        <th colspan="2" class="th-notyet">NOT YET IMPLEMENTED</th>
                                        <th colspan="2" class="th-gsis">FOR GSIS ENROLLMENT</th>
                                        <th colspan="2" class="th-ongoing">FOR IMPLEMENTATION</th>
                                        <th colspan="2" class="th-implemented">IMPLEMENTED</th>
                                        <th colspan="2" class="th-payroll">(COMPLETED FOR PROCESSING) FOR PAYROLL SUBMISSION</th>
                                    </tr>
                                    <tr>
                                        <th class="th-target">BENEFICIARIES</th>
                                        <th class="th-target">COORDINATED SUBSIDY</th>
                                        <th class="th-target">NO OF WORK DAYS</th>
                                        <th class="th-notyet">NO OF BENEFICIARIES</th>
                                        <th class="th-notyet">AMOUNT WAGES</th>
                                        <th class="th-gsis">NO OF BENEFICIARIES</th>
                                        <th class="th-gsis">AMOUNT (WAGES)</th>
                                        <th class="th-ongoing">NO OF BENEFICIARIES</th>
                                        <th class="th-ongoing">AMOUNT (WAGES)</th>
                                        <th class="th-implemented">BENEFICIARIES</th>
                                        <th class="th-implemented">AMOUNT (WAGES)</th>
                                        <th class="th-payroll">NO OF BENEFICIARIES</th>
                                        <th class="th-payroll">AMOUNT (WAGES)</th>
                                    </tr>
                                <?php else: ?>
                                    <!-- DEFAULT HEADERS -->
                                    <tr>
                                        <th rowspan="2" class="th-basic">ADL NO</th>
                                        <th rowspan="2" class="th-basic">REFERENCE NO</th>
                                        <th rowspan="2" class="th-basic">SPONSOR</th>
                                        <th rowspan="2" class="th-basic">PROPONENT</th>
                                        <th rowspan="2" class="th-basic">AREA OF IMPLEMENTATION</th>
                                        <th colspan="3" class="th-target">TARGET</th>
                                        <th colspan="2" class="th-notyet">NOT YET IMPLEMENTED</th>
                                        <th colspan="2" class="th-gsis">FOR GSIS ENROLLMENT</th>
                                        <th colspan="4" class="th-ongoing">FOR IMPLEMENTATION</th>
                                        <th colspan="3" class="th-implemented">IMPLEMENTED</th>
                                        <th colspan="2" class="th-payroll">FOR PAYROLL SUBMISSION</th>
                                        <th rowspan="2" class="th-remarks">REMARKS</th>
                                    </tr>
                                    <tr>
                                        <th class="th-target">BENEFICIARIES</th>
                                        <th class="th-target">COORDINATED SUBSIDY</th>
                                        <th class="th-target">NO OF WORK DAYS</th>
                                        <th class="th-notyet">NO OF BENEFICIARIES</th>
                                        <th class="th-notyet">AMOUNT WAGES</th>
                                        <th class="th-gsis">NO OF BENEFICIARIES</th>
                                        <th class="th-gsis">AMOUNT (WAGES)</th>
                                        <th class="th-ongoing">NO OF BENEFICIARIES</th>
                                        <th class="th-ongoing">AMOUNT (WAGES)</th>
                                        <th class="th-ongoing">EMPLOYMENT PERIOD</th>
                                        <th class="th-ongoing">TARGET PAYOUT</th>
                                        <th class="th-implemented">BENEFICIARIES</th>
                                        <th class="th-implemented">AMOUNT (WAGES)</th>
                                        <th class="th-implemented">PAYOUT DATE</th>
                                        <th class="th-payroll">NO OF BENEFICIARIES</th>
                                        <th class="th-payroll">AMOUNT (WAGES)</th>
                                    </tr>
                                <?php endif; ?>
                            </thead>
                            <tbody>
                                <?php if (!empty($report_data)): ?>
                                    <?php 
                                    // If summary mode is active, aggregate rows per province first
                                    if (isset($selected_province) && $selected_province === 'summary') {
                                        $summary_grouped = [];

                                        foreach ($report_data as $row) {
                                            // Check if fully paid/complete; skip if true
                                            $is_paid = (
                                                !empty($row['payment_alob_no']) && $row['payment_alob_no'] !== '0' &&
                                                !empty($row['payment_dv_no']) && $row['payment_dv_no'] !== '0' &&
                                                !empty($row['payment_check_no']) && $row['payment_check_no'] !== '0' &&
                                                !empty($row['payment_amount']) && $row['payment_amount'] !== '0' && $row['payment_amount'] !== '0.00' &&
                                                !empty($row['payment_date']) && $row['payment_date'] !== '0000-00-00'
                                            );
                                            if ($is_paid) {
                                                continue;
                                            }

                                            $prov_name = !empty($row['province_name']) ? $row['province_name'] : 'UNKNOWN PROVINCE';
                                            
                                            if (!isset($summary_grouped[$prov_name])) {
                                                $summary_grouped[$prov_name] = [
                                                    'target_ben' => 0, 'target_subsidy' => 0, 'work_days' => 0,
                                                    'impl_ben' => 0, 'impl_amt' => 0,
                                                    'ppes_ben' => 0, 'ppes_amt' => 0,
                                                    'gsis_ben' => 0, 'gsis_amt' => 0,
                                                    'ongoing_ben' => 0, 'ongoing_amt' => 0,
                                                    'not_yet_ben' => 0, 'not_yet_amt' => 0,
                                                    'count' => 0
                                                ];
                                            }

                                            $no_of_days  = $row['no_of_days'] ?? 0;
                                            $wage_amount = $row['wage_amount'] ?? 0;
                                            $target      = $row['target'] ?? 0;

                                            $summary_grouped[$prov_name]['target_ben'] += $target;
                                            $summary_grouped[$prov_name]['target_subsidy'] += ($row['subsidy_cost'] ?? 0);
                                            $summary_grouped[$prov_name]['work_days'] += $no_of_days;

                                            // GSIS calculations
                                            $is_gsis_empty = (empty($row['gsis_enrollment_date']) || $row['gsis_enrollment_date'] == '0000-00-00');
                                            $is_ppe_empty  = (empty($row['ppes_date_issued']) || $row['ppes_date_issued'] == '0000-00-00');
                                            if ($is_gsis_empty && !$is_ppe_empty) {
                                                $g_ben = $row['ppes_count'] ?? 0;
                                                $g_amt = ($g_ben * $no_of_days) * $wage_amount;
                                            } else {
                                                $g_ben = 0; $g_amt = 0;
                                            }
                                            $summary_grouped[$prov_name]['gsis_ben'] += $g_ben;
                                            $summary_grouped[$prov_name]['gsis_amt'] += $g_amt;

                                            // Payment Check & Payroll Calculations
                                            $completed_period = $row['completed_employment_amount'] ?? '';
                                            $is_completed_valid = (!empty($completed_period) && $completed_period !== '0000-00-00');

                                            if ($is_completed_valid && !$is_paid) {
                                                $p_ben = $row['ongoing_implementation_benefs'] ?? 0;
                                                $p_amt = ($p_ben > 0) ? ($p_ben * $no_of_days) * $wage_amount : 0;
                                            } else {
                                                $p_ben = 0; $p_amt = 0;
                                            }
                                            $summary_grouped[$prov_name]['ppes_ben'] += $p_ben;
                                            $summary_grouped[$prov_name]['ppes_amt'] += $p_amt;

                                            // Implemented Check (Strictly tomorrow onwards)
                                            $ongoing_end_date = $row['ongoing_implementation_end_date'] ?? '';
                                            $is_expired_end_date = false;

                                            if (!empty($ongoing_end_date) && $ongoing_end_date !== '0000-00-00') {
                                                $endDateObj = new DateTime($ongoing_end_date);
                                                $endDateObj->setTime(0, 0, 0);
                                                
                                                $todayObj = new DateTime('now');
                                                $todayObj->setTime(0, 0, 0);

                                                if ($endDateObj < $todayObj) {
                                                    $is_expired_end_date = true;
                                                }
                                            }

                                            if ($is_expired_end_date && !$is_completed_valid) {
                                                $i_ben = $row['ongoing_implementation_benefs'] ?? 0;
                                                $i_amt = ($i_ben > 0) ? ($i_ben * $no_of_days) * $wage_amount : 0;
                                            } else {
                                                $i_ben = 0; 
                                                $i_amt = 0;
                                            }

                                            $summary_grouped[$prov_name]['impl_ben'] += $i_ben;
                                            $summary_grouped[$prov_name]['impl_amt'] += $i_amt;

                                            // Ongoing calculations
                                            $gsis_date = $row['gsis_enrollment_date'] ?? '0000-00-00';
                                            if (!empty($gsis_date) && $gsis_date !== '0000-00-00' && !$is_expired_end_date && !$is_completed_valid) {
                                                $ongoing_start = $row['ongoing_implementation_start_date'] ?? '0000-00-00';
                                                $ongoing_end   = $row['ongoing_implementation_end_date'] ?? '0000-00-00';
                                                if ($ongoing_start == '0000-00-00' && $ongoing_end == '0000-00-00') {
                                                    $o_ben = $row['gsis_enrollment_benefs'] ?? 0;
                                                } else {
                                                    $o_ben = $row['ongoing_implementation_benefs'] ?? 0;
                                                }
                                                $o_amt = ($o_ben > 0) ? ($o_ben * $no_of_days) * $wage_amount : 0;
                                            } else {
                                                $o_ben = 0; $o_amt = 0;
                                            }
                                            $summary_grouped[$prov_name]['ongoing_ben'] += $o_ben;
                                            $summary_grouped[$prov_name]['ongoing_amt'] += $o_amt;

                                            // Not yet implemented calculations
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
                                                $n_ben = $target;
                                                $n_amt = ($target * $no_of_days) * $wage_amount;
                                            } else {
                                                $n_ben = $row['not_yet_beneficiaries'] ?? 0;
                                                $n_amt = $row['not_yet_amount'] ?? 0;
                                            }
                                            $summary_grouped[$prov_name]['not_yet_ben'] += $n_ben;
                                            $summary_grouped[$prov_name]['not_yet_amt'] += $n_amt;
                                            $summary_grouped[$prov_name]['count']++;
                                        }

                                        $grand_summary = [
                                            'target_ben' => 0, 'target_subsidy' => 0, 'work_days' => 0,
                                            'impl_ben' => 0, 'impl_amt' => 0,
                                            'ppes_ben' => 0, 'ppes_amt' => 0,
                                            'gsis_ben' => 0, 'gsis_amt' => 0,
                                            'ongoing_ben' => 0, 'ongoing_amt' => 0,
                                            'not_yet_ben' => 0, 'not_yet_amt' => 0
                                        ];

                                        foreach ($summary_grouped as $prov_name => $s):
                                            $grand_summary['target_ben'] += $s['target_ben'];
                                            $grand_summary['target_subsidy'] += $s['target_subsidy'];
                                            $grand_summary['work_days'] += $s['work_days'];
                                            $grand_summary['impl_ben'] += $s['impl_ben'];
                                            $grand_summary['impl_amt'] += $s['impl_amt'];
                                            $grand_summary['ppes_ben'] += $s['ppes_ben'];
                                            $grand_summary['ppes_amt'] += $s['ppes_amt'];
                                            $grand_summary['gsis_ben'] += $s['gsis_ben'];
                                            $grand_summary['gsis_amt'] += $s['gsis_amt'];
                                            $grand_summary['ongoing_ben'] += $s['ongoing_ben'];
                                            $grand_summary['ongoing_amt'] += $s['ongoing_amt'];
                                            $grand_summary['not_yet_ben'] += $s['not_yet_ben'];
                                            $grand_summary['not_yet_amt'] += $s['not_yet_amt'];
                                        ?>
                                            <tr>
                                                <td class="fw-semibold"><?= html_escape($prov_name); ?></td>
                                                <!-- TARGET -->
                                                <td class="text-center"><?= number_format($s['target_ben']); ?></td>
                                                <td class="text-end">&#8369; <?= number_format($s['target_subsidy'], 2); ?></td>
                                                <td class="text-center"><?= number_format($s['work_days']); ?></td>
                                                <!-- NOT YET IMPLEMENTED -->
                                                <td class="text-center"><?= $s['not_yet_ben'] > 0 ? number_format($s['not_yet_ben']) : ''; ?></td>
                                                <td class="text-end"><?= $s['not_yet_amt'] > 0 ? '&#8369; ' . number_format($s['not_yet_amt'], 2) : ''; ?></td>
                                                <!-- FOR GSIS ENROLLMENT -->
                                                <td class="text-center"><?= $s['gsis_ben'] > 0 ? number_format($s['gsis_ben']) : ''; ?></td>
                                                <td class="text-end"><?= $s['gsis_amt'] > 0 ? '&#8369; ' . number_format($s['gsis_amt'], 2) : ''; ?></td>
                                                <!-- FOR IMPLEMENTATION -->
                                                <td class="text-center"><?= $s['ongoing_ben'] > 0 ? number_format($s['ongoing_ben']) : ''; ?></td>
                                                <td class="text-end"><?= $s['ongoing_amt'] > 0 ? '&#8369; ' . number_format($s['ongoing_amt'], 2) : ''; ?></td>
                                                <!-- IMPLEMENTED -->
                                                <td class="text-center"><?= $s['impl_ben'] > 0 ? number_format($s['impl_ben']) : ''; ?></td>
                                                <td class="text-end"><?= $s['impl_amt'] > 0 ? '&#8369; ' . number_format($s['impl_amt'], 2) : ''; ?></td>
                                                <!-- FOR PAYROLL SUBMISSION -->
                                                <td class="text-center"><?= $s['ppes_ben'] > 0 ? number_format($s['ppes_ben']) : ''; ?></td>
                                                <td class="text-end"><?= $s['ppes_amt'] > 0 ? '&#8369; ' . number_format($s['ppes_amt'], 2) : ''; ?></td>
                                            </tr>
                                        <?php endforeach; ?>

                                        <!-- GRAND TOTAL ROW FOR SUMMARY VIEW -->
                                        <tr class="table-warning fw-bold">
                                            <td class="text-end">GRAND TOTAL:</td>
                                            <td class="text-center"><?= number_format($grand_summary['target_ben']); ?></td>
                                            <td class="text-end">&#8369; <?= number_format($grand_summary['target_subsidy'], 2); ?></td>
                                            <td class="text-center"><?= number_format($grand_summary['work_days']); ?></td>
                                            <td class="text-center"><?= $grand_summary['not_yet_ben'] > 0 ? number_format($grand_summary['not_yet_ben']) : ''; ?></td>
                                            <td class="text-end"><?= $grand_summary['not_yet_amt'] > 0 ? '&#8369; ' . number_format($grand_summary['not_yet_amt'], 2) : ''; ?></td>
                                            <td class="text-center"><?= $grand_summary['gsis_ben'] > 0 ? number_format($grand_summary['gsis_ben']) : ''; ?></td>
                                            <td class="text-end"><?= $grand_summary['gsis_amt'] > 0 ? '&#8369; ' . number_format($grand_summary['gsis_amt'], 2) : ''; ?></td>
                                            <td class="text-center"><?= $grand_summary['ongoing_ben'] > 0 ? number_format($grand_summary['ongoing_ben']) : ''; ?></td>
                                            <td class="text-end"><?= $grand_summary['ongoing_amt'] > 0 ? '&#8369; ' . number_format($grand_summary['ongoing_amt'], 2) : ''; ?></td>
                                            <td class="text-center"><?= $grand_summary['impl_ben'] > 0 ? number_format($grand_summary['impl_ben']) : ''; ?></td>
                                            <td class="text-end"><?= $grand_summary['impl_amt'] > 0 ? '&#8369; ' . number_format($grand_summary['impl_amt'], 2) : ''; ?></td>
                                            <td class="text-center"><?= $grand_summary['ppes_ben'] > 0 ? number_format($grand_summary['ppes_ben']) : ''; ?></td>
                                            <td class="text-end"><?= $grand_summary['ppes_amt'] > 0 ? '&#8369; ' . number_format($grand_summary['ppes_amt'], 2) : ''; ?></td>
                                        </tr>

                                    <?php } else { 
                                        // STANDARD DETAILED VIEW 
                                        $total_target_ben = 0; $total_target_subsidy = 0;
                                        $total_impl_ben = 0; $total_impl_amt = 0;
                                        $total_ppes_ben = 0; $total_ppes_amt = 0;
                                        $total_gsis_ben = 0; $total_gsis_amt = 0;
                                        $total_ongoing_ben = 0; $total_ongoing_amt = 0;
                                        $total_not_yet_ben = 0; $total_not_yet_amt = 0;

                                        foreach ($report_data as $row) {
                                            $is_paid = (
                                                !empty($row['payment_alob_no']) && $row['payment_alob_no'] !== '0' &&
                                                !empty($row['payment_dv_no']) && $row['payment_dv_no'] !== '0' &&
                                                !empty($row['payment_check_no']) && $row['payment_check_no'] !== '0' &&
                                                !empty($row['payment_amount']) && $row['payment_amount'] !== '0' && $row['payment_amount'] !== '0.00' &&
                                                !empty($row['payment_date']) && $row['payment_date'] !== '0000-00-00'
                                            );
                                            if ($is_paid) {
                                                continue;
                                            }

                                            $no_of_days  = $row['no_of_days'] ?? 0;
                                            $wage_amount = $row['wage_amount'] ?? 0;
                                            $target      = $row['target'] ?? 0;

                                            $total_target_ben += $target;
                                            $total_target_subsidy += ($row['subsidy_cost'] ?? 0);

                                            $is_gsis_empty = (empty($row['gsis_enrollment_date']) || $row['gsis_enrollment_date'] == '0000-00-00');
                                            $is_ppe_empty  = (empty($row['ppes_date_issued']) || $row['ppes_date_issued'] == '0000-00-00');
                                            
                                            if ($is_gsis_empty && !$is_ppe_empty) {
                                                $g_ben = $row['ppes_count'] ?? 0;
                                                $g_amt = ($g_ben * $no_of_days) * $wage_amount;
                                            } else {
                                                $g_ben = 0; $g_amt = 0;
                                            }
                                            $total_gsis_ben += $g_ben;
                                            $total_gsis_amt += $g_amt;

                                            $completed_period = $row['completed_employment_amount'] ?? '';
                                            $is_completed_valid = (!empty($completed_period) && $completed_period !== '0000-00-00');

                                            if ($is_completed_valid && !$is_paid) {
                                                $p_ben = $row['ongoing_implementation_benefs'] ?? 0;
                                                $p_amt = ($p_ben > 0) ? ($p_ben * $no_of_days) * $wage_amount : 0;
                                            } else {
                                                $p_ben = 0; $p_amt = 0;
                                            }
                                            $total_ppes_ben += $p_ben;
                                            $total_ppes_amt += $p_amt;

                                            $ongoing_end_date = $row['ongoing_implementation_end_date'] ?? '';
                                            $is_expired_end_date = false;
                                            if (!empty($ongoing_end_date) && $ongoing_end_date !== '0000-00-00') {
                                                $endDateObj = new DateTime($ongoing_end_date);
                                                $endDateObj->setTime(0, 0, 0);
                                                $todayObj = new DateTime('now');
                                                $todayObj->setTime(0, 0, 0);
                                                if ($endDateObj < $todayObj) {
                                                    $is_expired_end_date = true;
                                                }
                                            }

                                            if ($is_expired_end_date && !$is_completed_valid) {
                                                $i_ben = $row['ongoing_implementation_benefs'] ?? 0;
                                                $i_amt = ($i_ben > 0) ? ($i_ben * $no_of_days) * $wage_amount : 0;
                                            } else {
                                                $i_ben = 0; $i_amt = 0;
                                            }
                                            $total_impl_ben += $i_ben;
                                            $total_impl_amt += $i_amt;

                                            $gsis_date = $row['gsis_enrollment_date'] ?? '0000-00-00';
                                            if (!empty($gsis_date) && $gsis_date !== '0000-00-00' && !$is_expired_end_date && !$is_completed_valid) {
                                                $ongoing_start = $row['ongoing_implementation_start_date'] ?? '0000-00-00';
                                                $ongoing_end   = $row['ongoing_implementation_end_date'] ?? '0000-00-00';
                                                if ($ongoing_start == '0000-00-00' && $ongoing_end == '0000-00-00') {
                                                    $o_ben = $row['gsis_enrollment_benefs'] ?? 0;
                                                } else {
                                                    $o_ben = $row['ongoing_implementation_benefs'] ?? 0;
                                                }
                                                $o_amt = ($o_ben > 0) ? ($o_ben * $no_of_days) * $wage_amount : 0;
                                            } else {
                                                $o_ben = 0; $o_amt = 0;
                                            }
                                            $total_ongoing_ben += $o_ben;
                                            $total_ongoing_amt += $o_amt;
                                            
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
                                                $n_ben = $target;
                                                $n_amt = ($target * $no_of_days) * $wage_amount;
                                            } else {
                                                $n_ben = $row['not_yet_beneficiaries'] ?? 0;
                                                $n_amt = $row['not_yet_amount'] ?? 0;
                                            }
                                            $total_not_yet_ben += $n_ben;
                                            $total_not_yet_amt += $n_amt;
                                        }
                                        ?>

                                        <!-- GRAND TOTAL ROW AT THE TOP -->
                                        <tr class="table-warning fw-bold">
                                            <td colspan="5" class="text-end">GRAND TOTAL:</td>
                                            <td class="text-center"><?= number_format($total_target_ben); ?></td>
                                            <td class="text-end">&#8369; <?= number_format($total_target_subsidy, 2); ?></td>
                                            <td></td>
                                            <td class="text-center"><?= $total_not_yet_ben > 0 ? number_format($total_not_yet_ben) : ''; ?></td>
                                            <td class="text-end"><?= $total_not_yet_amt > 0 ? '&#8369; ' . number_format($total_not_yet_amt, 2) : ''; ?></td>
                                            <td class="text-center"><?= $total_gsis_ben > 0 ? number_format($total_gsis_ben) : ''; ?></td>
                                            <td class="text-end"><?= $total_gsis_amt > 0 ? '&#8369; ' . number_format($total_gsis_amt, 2) : ''; ?></td>
                                            <td class="text-center"><?= $total_ongoing_ben > 0 ? number_format($total_ongoing_ben) : ''; ?></td>
                                            <td class="text-end"><?= $total_ongoing_amt > 0 ? '&#8369; ' . number_format($total_ongoing_amt, 2) : ''; ?></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-center"><?= $total_impl_ben > 0 ? number_format($total_impl_ben) : ''; ?></td>
                                            <td class="text-end"><?= $total_impl_amt > 0 ? '&#8369; ' . number_format($total_impl_amt, 2) : ''; ?></td>
                                            <td></td>
                                            <td class="text-center"><?= $total_ppes_ben > 0 ? number_format($total_ppes_ben) : ''; ?></td>
                                            <td class="text-end"><?= $total_ppes_amt > 0 ? '&#8369; ' . number_format($total_ppes_amt, 2) : ''; ?></td>
                                            <td></td>
                                        </tr>

                                        <?php foreach ($report_data as $row): ?>
                                            <?php 
                                            $is_paid = (
                                                !empty($row['payment_alob_no']) && $row['payment_alob_no'] !== '0' &&
                                                !empty($row['payment_dv_no']) && $row['payment_dv_no'] !== '0' &&
                                                !empty($row['payment_check_no']) && $row['payment_check_no'] !== '0' &&
                                                !empty($row['payment_amount']) && $row['payment_amount'] !== '0' && $row['payment_amount'] !== '0.00' &&
                                                !empty($row['payment_date']) && $row['payment_date'] !== '0000-00-00'
                                            );
                                            if ($is_paid) {
                                                continue;
                                            }

                                            $no_of_days  = $row['no_of_days'] ?? 0;
                                            $wage_amount = $row['wage_amount'] ?? 0;
                                            $target      = $row['target'] ?? 0;

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

                                            if ($is_completed_valid && !$is_paid) {
                                                $ppes_beneficiaries = $row['ongoing_implementation_benefs'] ?? 0;
                                                $ppes_amount = ($ppes_beneficiaries > 0) ? ($ppes_beneficiaries * $no_of_days) * $wage_amount : '';
                                            } else {
                                                $ppes_beneficiaries = '';
                                                $ppes_amount = '';
                                            }

                                            $ongoing_end_date = $row['ongoing_implementation_end_date'] ?? '';
                                            $is_expired_end_date = false;
                                            if (!empty($ongoing_end_date) && $ongoing_end_date !== '0000-00-00') {
                                                $endDateObj = new DateTime($ongoing_end_date);
                                                $endDateObj->setTime(0, 0, 0);
                                                $todayObj = new DateTime('now');
                                                $todayObj->setTime(0, 0, 0);
                                                if ($endDateObj < $todayObj) {
                                                    $is_expired_end_date = true;
                                                }
                                            }

                                            if ($is_expired_end_date && !$is_completed_valid) {
                                                $implemented_beneficiaries = $row['ongoing_implementation_benefs'] ?? '';
                                                $implemented_amount = (!empty($implemented_beneficiaries) && $implemented_beneficiaries > 0) ? ($implemented_beneficiaries * $no_of_days) * $wage_amount : '';
                                                $implemented_payout_date = $row['payout_date'] ?? '';
                                            } else {
                                                $implemented_beneficiaries = '';
                                                $implemented_amount = '';
                                                $implemented_payout_date = '';
                                            }

                                            $gsis_date = $row['gsis_enrollment_date'] ?? '0000-00-00';
                                            if (!empty($gsis_date) && $gsis_date !== '0000-00-00' && !$is_expired_end_date && !$is_completed_valid) {
                                                $ongoing_start = $row['ongoing_implementation_start_date'] ?? '0000-00-00';
                                                $ongoing_end   = $row['ongoing_implementation_end_date'] ?? '0000-00-00';
                                                if ($ongoing_start == '0000-00-00' && $ongoing_end == '0000-00-00') {
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
                                                <td class="fw-semibold text-center"><?= html_escape($row['adl_no']); ?></td>
                                                <td><?= html_escape($row['implementation_reference_no']); ?></td>
                                                <td><?= html_escape($row['implementation_sponsor']); ?></td>
                                                <td><?= html_escape($row['p_name']); ?></td>
                                                <td><?= html_escape($row['area_description']); ?></td>
                                                <td class="text-center"><?= number_format($target); ?></td>
                                                <td class="text-end">&#8369; <?= number_format($row['subsidy_cost'] ?? 0, 2); ?></td>
                                                <td class="text-center"><?= html_escape($no_of_days); ?></td>
                                                <td class="text-center"><?= ($not_yet_beneficiaries !== '' && $not_yet_beneficiaries > 0) ? number_format($not_yet_beneficiaries) : ''; ?></td>
                                                <td class="text-end"><?= ($not_yet_amount !== '' && $not_yet_amount > 0) ? '&#8369; ' . number_format($not_yet_amount, 2) : ''; ?></td>
                                                <td class="text-center"><?= ($gsis_beneficiaries !== '' && $gsis_beneficiaries > 0) ? number_format($gsis_beneficiaries) : ''; ?></td>
                                                <td class="text-end"><?= ($gsis_amount !== '' && $gsis_amount > 0) ? '&#8369; ' . number_format($gsis_amount, 2) : ''; ?></td>
                                                <td class="text-center"><?= ($ongoing_beneficiaries !== '' && $ongoing_beneficiaries > 0) ? number_format($ongoing_beneficiaries) : ''; ?></td>
                                                <td class="text-end"><?= ($ongoing_amount !== '' && $ongoing_amount > 0) ? '&#8369; ' . number_format($ongoing_amount, 2) : ''; ?></td>
                                                <td class="text-center"><?= html_escape($employment_period); ?></td>
                                                <td class="text-center"><?= html_escape($target_payout); ?></td>
                                                <td class="text-center"><?= ($implemented_beneficiaries !== '' && $implemented_beneficiaries > 0) ? number_format($implemented_beneficiaries) : ''; ?></td>
                                                <td class="text-end"><?= ($implemented_amount !== '' && $implemented_amount > 0) ? '&#8369; ' . number_format($implemented_amount, 2) : ''; ?></td>
                                                <td class="text-center"><?= html_escape($implemented_payout_date); ?></td>
                                                <td class="text-center"><?= ($ppes_beneficiaries !== '' && $ppes_beneficiaries > 0) ? number_format($ppes_beneficiaries) : ''; ?></td>
                                                <td class="text-end"><?= ($ppes_amount !== '' && $ppes_amount > 0) ? '&#8369; ' . number_format($ppes_amount, 2) : ''; ?></td>
                                                <td><?= html_escape($row['remarks'] ?? ''); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php } ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="22" class="text-center text-muted py-4">
                                            <?= (!empty($start_date)) ? 'No records found for the selected date period.' : 'Please select a start and end date to display report records.'; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small no-print">
            &copy; <?= date('Y'); ?> Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

<!-- How This Page Works Modal -->
    <div class="modal fade no-print" id="howItWorksModal" tabindex="-1" aria-labelledby="howItWorksModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-dark text-white px-4 py-3 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-white bg-opacity-10 p-2 rounded-3 text-info">
                            <i class="bi bi-info-circle-fill fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="howItWorksModalLabel">How This Page Works</h5>
                            <p class="text-white-50 small mb-0">Status display guidelines and automated reporting logic</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                        <h6 class="text-uppercase text-secondary fw-bold fs-7 mb-3 tracking-wide">Status Display Guidelines Per Column</h6>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-2 mt-1"><i class="bi bi-1-circle-fill"></i></div>
                                <div class="small text-muted"><strong>For GSIS Enrollment:</strong> Kapag recorded napo sa system ang PPES details.</div>
                            </div>
                            <div class="d-flex align-items-start gap-3">
                                <div class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-2 mt-1"><i class="bi bi-2-circle-fill"></i></div>
                                <div class="small text-muted"><strong>For Implementation:</strong> Kapag recorded napo sa system and GSIS enrollment details.</div>
                            </div>
                            <div class="d-flex align-items-start gap-3">
                                <div class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-2 mt-1"><i class="bi bi-3-circle-fill"></i></div>
                                <div class="small text-muted"><strong>Implemented:</strong> Kapag lumipas napo ang Implementation End Date or recorded na ang completed employment details.</div>
                            </div>
                            <div class="d-flex align-items-start gap-3">
                                <div class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-2 mt-1"><i class="bi bi-4-circle-fill"></i></div>
                                <div class="small text-muted"><strong>Submission of Payroll:</strong> Kapag recorded napo ang completed employment details.</div>
                            </div>
                            <div class="d-flex align-items-start gap-3">
                                <div class="badge bg-danger bg-opacity-10 text-danger p-2 rounded-2 mt-1"><i class="bi bi-5-circle-fill"></i></div>
                                <div class="small text-muted"><strong>Excluded Data:</strong> Kapag fully completed napo ang implementation and payout.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0 px-4 py-3">
                    <button type="button" class="btn btn-dark btn-sm px-4 rounded-pill shadow-sm" data-bs-dismiss="modal">Got it</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    $(document).ready(function () {
        $(document).on('click', '#sidebarToggle', function (e) {
            e.preventDefault();
            if ($(window).width() < 992) {
                $('#sidebar').toggleClass('show-mobile');
            } else {
                $('#sidebar').toggleClass('collapsed');
                $('#main-content').toggleClass('expanded');
            }
        });
    });
    </script>
</body>

</html>