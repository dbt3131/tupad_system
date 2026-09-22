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
            
            /* Ensure colors print properly */
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
                        <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 fw-semibold" style="font-size: 0.7rem;">DOLE RO3</span>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Department of Labor and Employment</span>
                    </div>
                    <h3 class="fw-bold mb-1 text-dark" style="font-size: 1.25rem;">TUPAD Implementation Status</h3>
                    <p class="text-muted small mb-0"><i class="bi bi-calendar3 me-1"></i> As of <?= date('F d, Y'); ?></p>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="card shadow-sm mb-4 no-print border-0">
                <div class="card-body">
                    <form method="get" action="<?= site_url('tupad_report/tupad_implementation_status_report'); ?>" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label fw-semibold small">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= html_escape($start_date ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label fw-semibold small">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= html_escape($end_date ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter me-1"></i> Generate Report</button>
                            <button type="button" onclick="window.print();" class="btn btn-outline-secondary"><i class="bi bi-printer"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Report Results Table -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle text-nowrap" style="font-size: 0.8rem;">
                            <thead class="text-center align-middle">
                                <tr>
                                    <th rowspan="2" class="th-basic">ADL NO</th>
                                    <th rowspan="2" class="th-basic">REFERENCE NO</th>
                                    <th rowspan="2" class="th-basic">SPONSOR</th>
                                    <th rowspan="2" class="th-basic">PROPONENT</th>
                                    <th rowspan="2" class="th-basic">AREA OF IMPLEMENTATION</th>
                                    <th colspan="3" class="th-target">TARGET</th>
                                    <th colspan="3" class="th-implemented">IMPLEMENTED</th>
                                    <th colspan="2" class="th-payroll">FOR PAYROLL SUBMISSION</th>
                                    <th colspan="2" class="th-gsis">FOR GSIS ENROLLMENT</th>
                                    <th colspan="4" class="th-ongoing">FOR IMPLEMENTATION</th>
                                    <th colspan="2" class="th-notyet">NOT YET IMPLEMENTED</th>
                                    <th rowspan="2" class="th-remarks">REMARKS</th>
                                </tr>
                                <tr>
                                    <!-- TARGET -->
                                    <th class="th-target">BENEFICIARIES</th>
                                    <th class="th-target">COORDINATED SUBSIDY</th>
                                    <th class="th-target">NO OF WORK DAYS</th>
                                    <!-- IMPLEMENTED -->
                                    <th class="th-implemented">BENEFICIARIES</th>
                                    <th class="th-implemented">AMOUNT (WAGES)</th>
                                    <th class="th-implemented">PAYOUT DATE</th>
                                    <!-- FOR ISSUANCE PPES -->
                                    <th class="th-payroll">NO OF BENEFICIARIES</th>
                                    <th class="th-payroll">AMOUNT (WAGES)</th>
                                    <!-- FOR GSIS ENROLLMENT -->
                                    <th class="th-gsis">NO OF BENEFICIARIES</th>
                                    <th class="th-gsis">AMOUNT (WAGES)</th>
                                    <!-- ON-GOING IMPLEMENTATION -->
                                    <th class="th-ongoing">NO OF BENEFICIARIES</th>
                                    <th class="th-ongoing">AMOUNT (WAGES)</th>
                                    <th class="th-ongoing">EMPLOYMENT PERIOD</th>
                                    <th class="th-ongoing">TARGET PAYOUT</th>
                                    <!-- NOT YET IMPLEMENTED -->
                                    <th class="th-notyet">NO OF BENEFICIARIES</th>
                                    <th class="th-notyet">AMOUNT WAGES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($report_data)): ?>
                                    <?php foreach ($report_data as $row): ?>
                                        
                                        <?php 
                                        // Common variables
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

                                        // Check Completed Period Condition
                                        $completed_period = $row['completed_employment_amount'] ?? '';
                                        $is_completed_valid = (!empty($completed_period) && $completed_period !== '0000-00-00');

                                        // 3. For Payroll Submission Logic (Updated per instructions)
                                        if ($is_completed_valid) {
                                            $ppes_beneficiaries = $row['ongoing_implementation_benefs'] ?? 0;
                                            $ppes_amount = ($ppes_beneficiaries > 0) ? ($ppes_beneficiaries * $no_of_days) * $wage_amount : '';
                                        } else {
                                            $ppes_beneficiaries = '';
                                            $ppes_amount = '';
                                        }

                                        // 4. Implemented Column Logic (Removed data if For Payroll Submission is active based on completed period)
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

                                        // Check if ALL these fields are empty
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
                                            // Default back to original logic if not all are empty
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
                                            
                                            <!-- TARGET -->
                                            <td class="text-center"><?= number_format($target); ?></td>
                                            <td class="text-end">&#8369; <?= number_format($row['subsidy_cost'] ?? 0, 2); ?></td>
                                            <td class="text-center"><?= html_escape($no_of_days); ?></td>
                                            
                                            <!-- IMPLEMENTED -->
                                            <td class="text-center"><?= ($implemented_beneficiaries !== '' && $implemented_beneficiaries > 0) ? number_format($implemented_beneficiaries) : ''; ?></td>
                                            <td class="text-end"><?= ($implemented_amount !== '' && $implemented_amount > 0) ? '&#8369; ' . number_format($implemented_amount, 2) : ''; ?></td>
                                            <td class="text-center"><?= html_escape($implemented_payout_date); ?></td>
                                            
                                            <!-- FOR PAYROLL SUBMISSION -->
                                            <td class="text-center"><?= ($ppes_beneficiaries !== '' && $ppes_beneficiaries > 0) ? number_format($ppes_beneficiaries) : ''; ?></td>
                                            <td class="text-end"><?= ($ppes_amount !== '' && $ppes_amount > 0) ? '&#8369; ' . number_format($ppes_amount, 2) : ''; ?></td>
                                            
                                            <!-- FOR GSIS ENROLLMENT -->
                                            <td class="text-center"><?= ($gsis_beneficiaries !== '' && $gsis_beneficiaries > 0) ? number_format($gsis_beneficiaries) : ''; ?></td>
                                            <td class="text-end"><?= ($gsis_amount !== '' && $gsis_amount > 0) ? '&#8369; ' . number_format($gsis_amount, 2) : ''; ?></td>
                                            
                                            <!-- FOR IMPLEMENTATION -->
                                            <td class="text-center"><?= ($ongoing_beneficiaries !== '' && $ongoing_beneficiaries > 0) ? number_format($ongoing_beneficiaries) : ''; ?></td>
                                            <td class="text-end"><?= ($ongoing_amount !== '' && $ongoing_amount > 0) ? '&#8369; ' . number_format($ongoing_amount, 2) : ''; ?></td>
                                            <td class="text-center"><?= html_escape($employment_period); ?></td>
                                            <td class="text-center"><?= html_escape($target_payout); ?></td>
                                            
                                            <!-- NOT YET IMPLEMENTED -->
                                            <td class="text-center"><?= ($not_yet_beneficiaries !== '' && $not_yet_beneficiaries > 0) ? number_format($not_yet_beneficiaries) : ''; ?></td>
                                            <td class="text-end"><?= ($not_yet_amount !== '' && $not_yet_amount > 0) ? '&#8369; ' . number_format($not_yet_amount, 2) : ''; ?></td>
                                            
                                            <!-- REMARKS -->
                                            <td><?= html_escape($row['remarks'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
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