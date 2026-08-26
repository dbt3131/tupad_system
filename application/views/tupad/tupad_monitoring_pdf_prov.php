<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TUPAD Monitoring PDF Report - TUPAD IS</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #1e3a8a;
            --primary-light: #2563eb;
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

        .custom-report-table {
            font-size: 0.68rem;
            border-color: #000 !important;
            white-space: nowrap;
            width: 100%;
        }

        .custom-report-table th, 
        .custom-report-table td {
            border: 1px solid #7f7f7f !important;
            padding: 3px 4px !important;
            vertical-align: middle;
        }

        .custom-report-table .main-headers th {
            background-color: #ffffff;
            color: #000;
            font-weight: 700;
            font-size: 0.65rem;
        }

        .underline-title {
            color: #002060;
            text-decoration: underline;
        }

        .bg-blue-summary {
            background-color: #b4c6e7 !important;
            color: #000;
        }

        .bg-yellow-header {
            background-color: #ffe599 !important;
            color: #000;
        }

        .bg-green-header {
            background-color: #c6efce !important;
            color: #000;
        }

        .bg-bright-green {
            background-color: #00ff00 !important;
            color: #000;
        }

        .bg-dark-blue-header {
            background-color: #1b365d !important;
            color: #fff !important;
        }

        .bg-light-yellow {
            background-color: #fff2cc !important;
        }

        .bg-light-blue-col {
            background-color: #8ea9db !important;
        }

        /* OPTIMIZED LANDSCAPE PRINT STYLES */
        @media print {
            @page {
                size: A4 landscape;
                margin: 5mm;
            }

            html, body {
                width: 100% !important;
                height: auto !important;
                background-color: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 0.58rem !important;
            }

            .no-print,
            #navbar-template,
            #sidebar,
            footer,
            .btn {
                display: none !important;
            }

            #main-content,
            main,
            .card,
            .card-body,
            .table-responsive {
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                width: 100% !important;
                overflow: visible !important;
            }

            .custom-report-table {
                width: 100% !important;
                table-layout: auto !important;
                font-size: 0.58rem !important;
            }

            .custom-report-table th, 
            .custom-report-table td {
                border: 1px solid #000 !important;
                padding: 2px 3px !important;
                word-wrap: break-word !important;
                white-space: normal !important;
            }

            tr, td, th {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR TEMPLATE VIEW -->
    <div id="navbar-template" class="no-print">
        <?php $this->load->view('templates/navbar'); ?>
    </div>

    <!-- Main Content Wrapper -->
    <div id="main-content">
        
        <!-- SIDEBAR TEMPLATE VIEW -->
        <div id="sidebar" class="no-print">
            <?php $this->load->view('templates/sidebar'); ?>
        </div>

        <!-- Main Workspace -->
        <main class="p-3 p-md-4 flex-grow-1" style="min-height: calc(100vh - 120px);">
            
            <!-- Page Header & Actions Area -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 no-print">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-file-pdf text-danger me-2"></i>TUPAD Monitoring PDF Preview
                    </h3>
                    <p class="text-muted small mb-0">Previewing report for period: <?= html_escape($start_date ?? ''); ?> to <?= html_escape($end_date ?? ''); ?></p>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-danger btn-sm px-3">
                        <i class="bi bi-printer me-1"></i> Print / Save as PDF
                    </button>
                    <a href="<?= site_url('tupad_monitoring/tupad_monitoring_prov?start_date=' . ($start_date ?? '') . '&end_date=' . ($end_date ?? '')); ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to Filter View
                    </a>
                </div>
            </div>

            <?php 
                // PRE-CALCULATE TOTALS BEFORE PRINTING THE HEADER
                $total_disbursed = 0;
                $total_target    = 0;

                if (!empty($allocations) && is_array($allocations)) {
                    foreach ($allocations as $item) {
                        $total_disbursed += floatval($item['total_project_funds'] ?? 0);
                        
                        $gpai   = intval($item['gpai_count'] ?? 0);
                        $target = intval($item['final_physical_target'] ?? 0);
                        $ppe    = intval($item['final_physical_ppe_requested'] ?? 0);
                        
                        $total_target += ($gpai > 0) ? $gpai : (($target > 0) ? $target : $ppe);
                    }
                }
            ?>

            <!-- Table Card Container -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle custom-report-table text-center m-0">
                            <thead>
                                <!-- TOP SECTION SUMMARY HEADER -->
                                <tr class="header-section-bar">
                                    <th colspan="5" class="text-start ps-3 fw-bold underline-title">
                                        C. GPAI ENROLLED ALLOCATIONS (ongoing implementation/implemented)
                                    </th>
                                    <th class="bg-blue-summary fw-bold text-end pe-2">
                                        ₱<?= number_format($total_disbursed, 2); ?>
                                    </th>
                                    <th class="bg-blue-summary fw-bold text-center">
                                        <?= number_format($total_target); ?>
                                    </th>
                                    <th colspan="2" class="bg-yellow-header fw-bold">SHORT TERM (10-30 DAYS)</th>
                                    <th colspan="2" class="bg-green-header fw-bold">LONG TERM (31-90 DAYS)</th>
                                    <th colspan="3"></th>
                                    <th class="bg-bright-green fw-bold">FOR REQUEST (PPE)</th>
                                    <th colspan="2"></th>
                                    <th colspan="2" class="bg-dark-blue-header text-white fw-bold">DATE OF SUBMISSION OF WORK PROGRAM</th>
                                </tr>

                                <!-- MAIN COLUMN HEADERS -->
                                <tr class="main-headers text-uppercase">
                                    <th>DATE COORDINATED</th>
                                    <th>ADL NO.</th>
                                    <th>REFERENCE NO.</th>
                                    <th>SPONSOR</th>
                                    <th>PROPONENT/RECIPIENT</th>
                                    <th>ACTUAL COMPUTATION OF SUBSIDY<br><small>(wage+GPAI+PPE+remittance fee)</small></th>
                                    <th>NO. OF BENEFICIARIES</th>
                                    <th>NO. OF BENEFICIARIES</th>
                                    <th>NO. OF DAYS</th>
                                    <th>NO. OF BENEFICIARIES</th>
                                    <th>NO. OF DAYS</th>
                                    <th>AREA OF IMPLEMENTATION</th>
                                    <th>STATUS</th>
                                    <th>SCHEDULE OF ORIENTATION</th>
                                    <th class="bg-bright-green">FOR REQUEST (PPE)</th>
                                    <th>SCHEDULE OF PPE PICK UP</th>
                                    <th>FO REMARKS<br><small>(Target Date, Explanation, Commitment, Issues in Implementation)</small></th>
                                    <th>MODE OF PAYOUT</th>
                                    <th class="bg-dark-blue-header text-white">DATE OF SUBMISSION OF WORK PROGRAM</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($allocations) && is_array($allocations)): ?>
                                    <?php foreach ($allocations as $row): ?>
                                        <?php 
                                            // 1. Beneficiary count priority fallback
                                            $gpai   = intval($row['gpai_count'] ?? 0);
                                            $target = intval($row['final_physical_target'] ?? 0);
                                            $ppe    = intval($row['final_physical_ppe_requested'] ?? 0);
                                            
                                            $target_count = ($gpai > 0) ? $gpai : (($target > 0) ? $target : $ppe);

                                            $raw_terms    = strtolower(trim($row['term'] ?? ''));
                                            $days_of_work = trim($row['no_of_days_work'] ?? $row['no_days_of_work'] ?? '');

                                            // Extract numeric values from work days
                                            $numeric_days = intval(preg_replace('/[^0-9]/', '', $days_of_work));

                                            // 2. Keyword Check FIRST (Short vs Long term prioritization)
                                            if (strpos($raw_terms, 'short') !== false) {
                                                $is_long = false;
                                            } elseif (strpos($raw_terms, 'long') !== false) {
                                                $is_long = true;
                                            } else {
                                                $is_long = ($numeric_days > 30);
                                            }

                                            $is_short = !$is_long;

                                            $display_days = ($numeric_days > 0) ? $numeric_days : '';

                                            $short_beneficiaries = $is_short ? ($target_count > 0 ? $target_count : '') : '';
                                            $short_days          = $is_short ? $display_days : '';

                                            $long_beneficiaries  = $is_long ? ($target_count > 0 ? $target_count : '') : '';
                                            $long_days           = $is_long ? $display_days : '';
                                        ?>
                                        <tr>
                                            <td class="text-nowrap"><?= !empty($row['date_coordinated']) ? date('l, F j, Y', strtotime($row['date_coordinated'])) : ''; ?></td>
                                            <td><?= html_escape($row['adl_no'] ?? ''); ?></td>
                                            <td class="fw-semibold text-break" style="max-width: 150px;"><?= html_escape($row['reference_no'] ?? ''); ?></td>
                                            <td><?= html_escape($row['sponsor'] ?? 'DOLE RO3'); ?></td>
                                            <td class="text-start fw-semibold"><?= html_escape($row['proponent'] ?? $row['lgu_municipality_city'] ?? ''); ?></td>
                                            
                                            <td class="fw-bold bg-light-yellow text-end pe-2">
                                                ₱<?= number_format(floatval($row['total_project_funds'] ?? 0), 2); ?>
                                            </td>
                                            <td class="fw-bold bg-light-yellow">
                                                <?= number_format($target_count); ?>
                                            </td>

                                            <!-- SHORT TERM -->
                                            <td class="fw-bold text-center">
                                                <?= !empty($short_beneficiaries) ? number_format($short_beneficiaries) : ''; ?>
                                            </td>
                                            <td class="text-center">
                                                <?= html_escape($short_days); ?>
                                            </td>

                                            <!-- LONG TERM -->
                                            <td class="fw-bold text-center">
                                                <?= !empty($long_beneficiaries) ? number_format($long_beneficiaries) : ''; ?>
                                            </td>
                                            <td class="text-center">
                                                <?= html_escape($long_days); ?>
                                            </td>

                                            <td class="text-uppercase"><?= html_escape($row['lgu_municipality_city'] ?? ''); ?></td>
                                            <td>
                                                <?php if (isset($row['implementation_status']) && $row['implementation_status'] == 1): ?>
                                                    Paid
                                                <?php else: ?>
                                                    Not Yet Paid
                                                <?php endif; ?>
                                            </td>
                                            <td><?= html_escape($row['orientation_schedule'] ?? ''); ?></td>
                                            <td><?= html_escape($row['final_physical_ppe_requested'] ?? ''); ?></td>
                                            <td><?= html_escape($row['ppe_pickup_schedule'] ?? ''); ?></td>
                                            <td><?= html_escape($row['remarks'] ?? ('Payout : ' . ($row['period_end'] ?? ''))); ?></td>
                                            <td>Onsite Caravan</td>
                                            <td class="bg-light-blue-col">
                                                <?= html_escape($row['date_submission_work_program'] ?? ''); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="19" class="text-center py-4 text-muted">
                                            No records available for PDF generation.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top p-3 text-center text-muted small no-print">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Toggle Script -->
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