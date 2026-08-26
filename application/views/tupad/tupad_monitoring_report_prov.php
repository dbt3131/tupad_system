<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TUPAD Monitoring Report - TUPAD IS</title>

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
    </style>
</head>

<body>

    <!-- NAVBAR TEMPLATE VIEW -->
    <?php $this->load->view('templates/navbar'); ?>

    <!-- Main Content Wrapper -->
    <div id="main-content">
        
        <!-- SIDEBAR TEMPLATE VIEW -->
        <?php $this->load->view('templates/sidebar'); ?>

        <!-- Main Workspace -->
        <main class="p-3 p-md-4 flex-grow-1" style="min-height: calc(100vh - 120px);">
            
            <!-- Page Header Area -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-file-earmark-bar-graph text-primary me-2"></i>TUPAD Monitoring Report
                    </h3>
                    <p class="text-muted small mb-0">Track targets, disbursed amounts, assigned provinces, and municipalities by period date.</p>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-3">
                    <form method="get" action="<?= site_url('tupad_monitoring/tupad_monitoring_prov'); ?>" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Period Start Date</label>
                            <input type="date" name="start_date" value="<?= !empty($start_date) ? html_escape($start_date) : ''; ?>" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Period End Date</label>
                            <input type="date" name="end_date" value="<?= !empty($end_date) ? html_escape($end_date) : ''; ?>" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm px-4">
                                <i class="bi bi-filter me-1"></i> Filter Report
                            </button>
                            <a href="<?= site_url('tupad_monitoring/tupad_monitoring_prov'); ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
                            
                            <?php if (!empty($start_date) && !empty($end_date)): ?>
                                <a href="<?= site_url('tupad_monitoring/tupad_monitoring_prov?start_date=' . $start_date . '&end_date=' . $end_date . '&pdf=1'); ?>" target="_blank" class="btn btn-danger btn-sm ms-auto">
                                    <i class="bi bi-file-pdf me-1"></i> View/Print PDF Report
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table Card Container -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-2">
                    <?php 
                        $has_dates = (!empty($start_date) && !empty($end_date));
                        $raw_allocations = ($has_dates && !empty($allocations) && is_array($allocations)) ? $allocations : [];

                        // Filter array to ONLY include items where final_physical_target has a value
                        $display_allocations = array_filter($raw_allocations, function($item) {
                            return isset($item['final_physical_target']) 
                                && $item['final_physical_target'] !== '' 
                                && $item['final_physical_target'] !== null 
                                && floatval($item['final_physical_target']) > 0;
                        });

                        // Pre-calculate summary totals based ONLY on filtered data
                        $total_disbursed = 0;
                        $total_target = 0;

                        if (!empty($display_allocations)) {
                            foreach ($display_allocations as $item) {
                                $total_disbursed += floatval($item['total_project_funds'] ?? 0);
                                $total_target += floatval($item['final_physical_target'] ?? 0);
                            }
                        }
                    ?>
              
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle custom-report-table text-center m-0">
                            <thead>
                                <!-- TOP SECTION SUMMARY HEADER -->
                                <tr class="header-section-bar">
                                    <th colspan="6" class="text-start ps-3 fw-bold underline-title">
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
                                    <th class="col-date">DATE COORDINATED</th>
                                    <th class="col-id">PRISM ID</th>
                                    <th class="col-adl">ADL NO.</th>
                                    <th class="col-ref">REFERENCE NO.</th>
                                    <th class="col-sponsor">SPONSOR</th>
                                    <th class="col-proponent">PROPONENT/RECIPIENT</th>
                                    <th class="col-subsidy">ACTUAL COMPUTATION OF SUBSIDY<br><small>(wage+GPAI+PPE+remittance fee)</small></th>
                                    <th class="col-num">NO. OF BENEFICIARIES</th>
                                    <th class="col-num">NO. OF BENEFICIARIES</th>
                                    <th class="col-num">NO. OF DAYS</th>
                                    <th class="col-num">NO. OF BENEFICIARIES</th>
                                    <th class="col-num">NO. OF DAYS</th>
                                    <th class="col-area">AREA OF IMPLEMENTATION</th>
                                    <th class="col-status">STATUS</th>
                                    <th class="col-date">SCHEDULE OF ORIENTATION</th>
                                    <th class="bg-bright-green col-num">FOR REQUEST (PPE)</th>
                                    <th class="col-date">SCHEDULE OF PPE PICK UP</th>
                                    <th class="col-remarks">FO REMARKS<br><small>(Target Date, Explanation, Commitment, Issues in Implementation)</small></th>
                                    <th class="col-payout">MODE OF PAYOUT</th>
                                    <th class="bg-dark-blue-header text-white col-date">DATE OF SUBMISSION OF WORK PROGRAM</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($display_allocations)): ?>
                                    <?php foreach ($display_allocations as $row): ?>
                                        <?php 
                                            $target = intval($row['final_physical_target'] ?? 0);

                                            $raw_terms    = strtolower(trim($row['term'] ?? ''));
                                            $days_of_work = trim($row['no_of_days_work'] ?? '');

                                            // Extract pure numbers from work days
                                            $numeric_days = intval(preg_replace('/[^0-9]/', '', $days_of_work));

                                            // 1. Check term column string FIRST
                                            if (strpos($raw_terms, 'short') !== false) {
                                                $is_long = false;
                                            } elseif (strpos($raw_terms, 'long') !== false) {
                                                $is_long = true;
                                            } else {
                                                // 2. Fall back to numeric day count
                                                $is_long = ($numeric_days > 30);
                                            }

                                            $is_short = !$is_long;

                                            // Display numbers only
                                            $display_days = ($numeric_days > 0) ? $numeric_days : '';

                                            $short_beneficiaries = $is_short ? ($target > 0 ? $target : 0) : '';
                                            $short_days          = $is_short ? $display_days : '';

                                            $long_beneficiaries  = $is_long ? ($target > 0 ? $target : 0) : '';
                                            $long_days           = $is_long ? $display_days : '';
                                        ?>
                                        <tr>
                                            <td class="col-date"><?= !empty($row['date_coordinated']) ? date('l, F j, Y', strtotime($row['date_coordinated'])) : ''; ?></td>
                                            <td class="col-id"><?= html_escape($row['unique_id'] ?? ''); ?></td>
                                            <td class="col-adl"><?= html_escape($row['adl_no'] ?? ''); ?></td>
                                            <td class="fw-semibold col-ref"><?= html_escape($row['reference_no'] ?? ''); ?></td>
                                            <td class="col-sponsor"><?= html_escape($row['sponsor'] ?? 'DOLE RO3'); ?></td>
                                            <td class="text-start fw-semibold col-proponent"><?= html_escape($row['proponent'] ?? $row['lgu_municipality_city'] ?? ''); ?></td>
                                            
                                            <td class="fw-bold text-end pe-2 col-subsidy">
                                                ₱<?= number_format(floatval($row['total_project_funds'] ?? 0), 2); ?>
                                            </td>
                                            <td class="fw-bold col-num">
                                                <?= number_format($target); ?>
                                            </td>

                                            <!-- SHORT TERM (10-30 DAYS) -->
                                            <td class="text-center col-num">
                                                <?= !empty($short_beneficiaries) ? number_format($short_beneficiaries) : ''; ?>
                                            </td>
                                            <td class="text-center col-num">
                                                <?= html_escape($short_days); ?>
                                            </td>

                                            <!-- LONG TERM (31-90 DAYS) -->
                                            <td class="fw-bold text-center col-num">
                                                <?= !empty($long_beneficiaries) ? number_format($long_beneficiaries) : ''; ?>
                                            </td>
                                            <td class="text-center col-num">
                                                <?= html_escape($long_days); ?>
                                            </td>

                                            <td class="text-uppercase col-area"><?= html_escape($row['lgu_municipality_city'] ?? ''); ?></td>
                                            <td class="col-status">
                                                <?php if (isset($row['implementation_status']) && $row['implementation_status'] == 1): ?>
                                                    <span class="badge-pill pill-green">Paid</span>
                                                <?php else: ?>
                                                    <span class="badge-pill pill-gray-light text-muted">Not Yet Paid</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="col-date"><?= html_escape($row['orientation_schedule'] ?? ''); ?></td>
                                            <td class="col-num"><?= html_escape($row['ppe_request'] ?? ''); ?></td>
                                            <td class="col-date"><?= html_escape($row['ppe_pickup_schedule'] ?? ''); ?></td>
                                            <td class="col-remarks"><?= html_escape($row['remarks'] ?? ''); ?></td>
                                            <td class="col-payout">
                                                <span class="badge-pill pill-cyan"><?= html_escape($row['mode_of_payout'] ?? ''); ?></span>
                                            </td>
                                            <td class="bg-light-blue-col col-date">
                                                <?= html_escape($row['date_submission_work_program'] ?? ''); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="20" class="text-center py-4 text-muted">
                                            <?= (!$has_dates) ? 'Please select a Period Start Date and End Date to view monitoring reports.' : 'No monitoring logs found with a target count for the selected period date.'; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PROFESSIONAL SPREADSHEET STYLES -->
            <style>
                .table-responsive {
                    max-width: 100%;
                    overflow-x: auto !important;
                }

                .custom-report-table {
                    font-size: 0.72rem;
                    border-color: #000 !important;
                    width: 100%;
                    border-collapse: collapse;
                }

                .custom-report-table th, 
                .custom-report-table td {
                    border: 1px solid #7f7f7f !important;
                    padding: 6px 8px !important;
                    vertical-align: middle;
                    white-space: nowrap; /* Keeps content on a single line by default */
                }

                /* Column Headers */
                .custom-report-table .main-headers th {
                    background-color: #ffffff;
                    color: #000;
                    font-weight: 700;
                    font-size: 0.68rem;
                    text-align: center;
                    white-space: normal !important; /* Headers wrap word-by-word naturally */
                    word-break: normal !important;
                    line-height: 1.25;
                }

                /* Specific Column Sizing & Wrapping Rules */
                .col-date       { min-width: 140px; }
                .col-id         { min-width: 110px; }
                .col-adl        { min-width: 110px; }
                
                .col-ref { 
                    min-width: 220px; 
                    max-width: 260px; 
                    white-space: normal !important; 
                    word-break: break-word !important; 
                }

                .col-sponsor    { min-width: 110px; }
                .col-proponent  { min-width: 160px; white-space: normal !important; }
                .col-subsidy    { min-width: 140px; }
                .col-num        { min-width: 75px; }
                .col-area       { min-width: 130px; }
                .col-status     { min-width: 100px; }
                .col-payout     { min-width: 120px; }
                .col-remarks    { min-width: 180px; white-space: normal !important; }

                /* Header Color Highlights */
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

                /* Body Data Highlights */
                .bg-light-yellow {
                    background-color: #fff2cc !important;
                }

                .bg-light-blue-col {
                    background-color: #8ea9db !important;
                }

                /* Custom Pill Badges */
                .badge-pill {
                    display: inline-block;
                    padding: 3px 10px;
                    border-radius: 12px;
                    font-size: 0.68rem;
                    font-weight: 600;
                    white-space: nowrap;
                }

                .pill-gray-light {
                    background-color: #f2f2f2;
                    border: 1px solid #d9d9d9;
                    color: #595959;
                }

                .pill-cyan {
                    background-color: #ddebf7;
                    border: 1px solid #bdd7ee;
                    color: #2f5597;
                }

                .pill-green {
                    background-color: #e2efda;
                    border: 1px solid #c6e0b4;
                    color: #375623;
                }
            </style>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top p-3 text-center text-muted small">
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