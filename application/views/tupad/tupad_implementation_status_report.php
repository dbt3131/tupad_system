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
                <thead class="table-dark text-center align-middle">
                    <tr>
                        <th rowspan="2">ADL NO</th>
                        <th rowspan="2">REFERENCE NO</th>
                        <th rowspan="2">SPONSOR</th>
                        <th rowspan="2">PROPONENT</th>
                        <th rowspan="2">AREA OF IMPLEMENTATION</th>
                        <th colspan="3">TARGET</th>
                        <th colspan="3">IMPLEMENTED</th>
                        <th colspan="2">FOR PAYROLL SUBMISSION</th>
                        <th colspan="2">FOR GSIS ENROLLMENT</th>
                        <th colspan="4">ON-GOING IMPLEMENTATION</th>
                        <th colspan="2">NOT YET IMPLEMENTED</th>
                        <th rowspan="2">REMARKS</th>
                    </tr>
                    <tr>
                        <!-- TARGET -->
                        <th>BENEFICIARIES</th>
                        <th>COORDINATED SUBSIDY</th>
                        <th>NO OF WORK DAYS</th>
                        <!-- IMPLEMENTED -->
                        <th>BENEFICIARIES</th>
                        <th>AMOUNT (WAGES)</th>
                        <th>PAYOUT DATE</th>
                        <!-- FOR ISSUANCE PPES -->
                        <th>NO OF BENEFICIARIES</th>
                        <th>AMOUNT (WAGES)</th>
                        <!-- FOR GSIS ENROLLMENT -->
                        <th>NO OF BENEFICIARIES</th>
                        <th>AMOUNT (WAGES)</th>
                        <!-- ON-GOING IMPLEMENTATION -->
                        <th>NO OF BENEFICIARIES</th>
                        <th>AMOUNT (WAGES)</th>
                        <th>EMPLOYMENT PERIOD</th>
                        <th>TARGET PAYOUT</th>
                        <!-- NOT YET IMPLEMENTED -->
                        <th>NO OF BENEFICIARIES</th>
                        <th>AMOUNT WAGES</th>
                    </tr>
                </thead>
               <tbody>
    <?php if (!empty($report_data)): ?>
        <?php foreach ($report_data as $row): ?>
            
            
            
            
            <?php 
// Check if GSIS enrollment fields are truly empty, zero, or default 0000-00-00
$is_gsis_empty = (empty($row['gsis_enrollment_date']) || $row['gsis_enrollment_date'] == '0000-00-00');
                 //(empty($row['gsis_enrollment_benefs']) || $row['gsis_enrollment_benefs'] == 0) && 
                 //(empty($row['gsis_enrollment_amount']) || $row['gsis_enrollment_amount'] == 0 || $row['gsis_enrollment_amount'] == '0');

if ($is_gsis_empty) {
    // If GSIS fields are empty/zero, calculate and display the values
    $gsis_beneficiaries = $row['ppes_count'] ?? 0;
    $ppes_count  = $row['ppes_count'] ?? 0;
    $no_of_days  = $row['no_of_days'] ?? 0;
    $wage_amount = $row['wage_amount'] ?? 600; 
    
    $gsis_amount = ($ppes_count * $no_of_days) * $wage_amount;
} else {
    // If GSIS fields already contain actual data, leave the columns blank
    $gsis_beneficiaries = '';
    $gsis_amount = '';
}
?>




            <tr>
                <td class="fw-semibold text-center"><?= html_escape($row['adl_no']); ?></td>
                <td><?= html_escape($row['implementation_reference_no']); ?></td>
                <td><?= html_escape($row['implementation_sponsor']); ?></td>
                <td><?= html_escape($row['p_name']); ?></td>
                <td><?= html_escape($row['area_description']); ?></td>
                <!-- TARGET -->
                <td class="text-center"><?= number_format($row['target'] ?? 0); ?></td>
                <td class="text-end">&#8369; <?= number_format($row['subsidy_cost'] ?? 0, 2); ?></td>
                <td class="text-center"><?= html_escape($row['no_of_days'] ?? ''); ?></td>
                <!-- IMPLEMENTED -->
                <td class="text-center"><?= html_escape($row['implemented_beneficiaries'] ?? ''); ?></td>
                <td class="text-end"><?= isset($row['implemented_amount']) ? '&#8369; ' . number_format($row['implemented_amount'], 2) : ''; ?></td>
                <td class="text-center"><?= html_escape($row['payout_date'] ?? ''); ?></td>
                <!-- FOR PAYROLL SUBMISSION -->
                <td class="text-center"><?= html_escape($row['ppes_beneficiaries'] ?? ''); ?></td>
                <td class="text-end"><?= isset($row['ppes_amount']) ? '&#8369; ' . number_format($row['ppes_amount'], 2) : ''; ?></td>
                <!-- FOR GSIS ENROLLMENT -->
               <td class="text-center"><?= ($gsis_beneficiaries !== '' && $gsis_beneficiaries > 0) ? number_format($gsis_beneficiaries) : ''; ?></td>
<td class="text-end"><?= ($gsis_amount !== '' && $gsis_amount > 0) ? '&#8369; ' . number_format($gsis_amount, 2) : ''; ?></td>
                <!-- ON-GOING IMPLEMENTATION -->
                <td class="text-center"><?= html_escape($row['ongoing_beneficiaries'] ?? ''); ?></td>
                <td class="text-end"><?= isset($row['ongoing_amount']) ? '&#8369; ' . number_format($row['ongoing_amount'], 2) : ''; ?></td>
                <td class="text-center"><?= html_escape($row['employment_period'] ?? ''); ?></td>
                <td class="text-center"><?= html_escape($row['target_payout'] ?? ''); ?></td>
                <!-- NOT YET IMPLEMENTED -->
                <td class="text-center"><?= html_escape($row['not_yet_beneficiaries'] ?? ''); ?></td>
                <td class="text-end"><?= isset($row['not_yet_amount']) ? '&#8369; ' . number_format($row['not_yet_amount'], 2) : ''; ?></td>
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