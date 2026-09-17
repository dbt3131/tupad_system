<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>COA Quarterly Report — DOLE TUPAD</title>

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

        /* Modernized Cards & Form Inputs */
        .card {
            border: 1px solid var(--card-border);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -2px rgba(0, 0, 0, 0.02);
        }

        .form-control, .form-select {
            border-color: #cbd5e1;
            padding: 0.6rem 0.85rem;
            font-size: 0.875rem;
            border-radius: 0.5rem;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        /* Modern Table Wrapper & Design */
        .table-responsive {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }

        table.coatable {
            margin-bottom: 0;
        }

        table.coatable th, table.coatable td {
            font-size: 0.78rem;
            vertical-align: middle;
            text-align: center;
            white-space: nowrap;
            padding: 0.75rem 0.6rem;
        }
        
        table.coatable th {
            background-color: #f8fafc;
            color: #334155;
            border-color: #e2e8f0 !important;
            font-weight: 600;
            letter-spacing: -0.01em;
        }

        table.coatable tbody tr {
            transition: background-color 0.15s ease;
        }

        table.coatable tbody tr:hover {
            background-color: #f1f5f9 !important;
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
                    <h3 class="fw-bold mb-1 text-dark" style="font-size: 1.25rem;">Consolidated Quarterly Report on Government PPA</h3>
                    <p class="text-muted small mb-0"><i class="bi bi-calendar3 me-1"></i> As of <?= date('F d, Y'); ?></p>
                </div>
            </div>

            <!-- Modern Filter & Action Form Card -->
            <div class="card border-0 mb-4 no-print">
                <div class="card-body p-4">
                    <form method="GET" action="<?= site_url('tupad_report/coa_tupad_report'); ?>" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label fw-semibold small text-secondary">Start Date</label>
                            <input type="date" class="form-control shadow-none" id="start_date" name="start_date" value="<?= html_escape($start_date ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label fw-semibold small text-secondary">End Date</label>
                            <input type="date" class="form-control shadow-none" id="end_date" name="end_date" value="<?= html_escape($end_date ?? ''); ?>">
                        </div>
                        <div class="col-md-6 d-flex gap-2">
                            <button type="submit" class="btn px-4 fw-semibold flex-grow-1 shadow-sm text-white" style="background-color: #0f172a; border-color: #0f172a;">
    <i class="bi bi-filter me-1"></i> Filter Records
</button>
                            <a href="<?= site_url('tupad_report/coa_tupad_report'); ?>" class="btn btn-outline-secondary px-3" title="Reset Filters">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                            <a href="<?= site_url('tupad_report/export_coa_excel') . '?' . http_build_query($_GET); ?>" class="btn btn-success fw-semibold text-nowrap shadow-sm">
                                <i class="bi bi-file-earmark-excel me-1"></i> Export XLSX
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Table Display matching COA Template -->
            <div class="table-responsive p-0">
                <table class="table table-bordered table-striped coatable mb-0">
                    <thead>
                        <tr>
                            <th rowspan="2">Agency/Address</th>
                            <th rowspan="2">Project/Program/Activity Name</th>
                            <th colspan="2">Location</th>
                            <th rowspan="2">Physical Target</th>
                            <th rowspan="2">No. of Days</th>
                            <th rowspan="2">Total Project Cost</th>
                            <th rowspan="2">Proposed Date to Start<br><small class="text-muted">(DD/MM/YYYY)</small></th>
                            <th rowspan="2">Actual Date Started<br><small class="text-muted">(DD/MM/YYYY)</small></th>
                            <th rowspan="2">No. of Extensions</th>
                            <th rowspan="2">Target Completion Date<br><small class="text-muted">(DD/MM/YYYY)</small></th>
                            <th rowspan="2">Actual Date of Completion<br><small class="text-muted">(DD/MM/YYYY)</small></th>
                            <th colspan="2">Project Status this Quarter</th>
                            <th rowspan="2">Cost Incurred to Date</th>
                            <th rowspan="2">Remarks</th>
                            <th rowspan="2">Mode of Procurement</th>
                            <th rowspan="2">Contractor (If applicable)</th>
                        </tr>
                        <tr>
                            <th>Province</th>
                            <th>LGU/Municipality</th>
                            <th>% of Completion</th>
                            <th>Total Cost Incurred this Quarter</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($report_data) && is_array($report_data)): ?>
                            <?php foreach ($report_data as $row): ?>
                                <tr>
                                    <td>DOLE RO3</td>
                                    <td>TUPAD</td>
                                    <td><?= html_escape($row['province_name'] ?? ''); ?></td>
                                    <td><?= html_escape($row['municipality_name'] ?? ''); ?></td>
                                    <td><?= html_escape($row['target'] ?? ''); ?></td>
                                    <td><?= html_escape($row['no_of_days'] ?? ''); ?></td>
                                    <td class="text-end fw-medium"><?= !empty($row['adl_amount']) ? number_format((float)$row['adl_amount'], 2) : ''; ?></td>
                                    <td><?= !empty($row['ongoing_implementation_start_date']) ? date('d/m/Y', strtotime($row['ongoing_implementation_start_date'])) : ''; ?></td>
                                    <td><?= !empty($row['ongoing_implementation_start_date']) ? date('d/m/Y', strtotime($row['ongoing_implementation_start_date'])) : ''; ?></td>
                                    <td></td>
                                    <td><?= !empty($row['ongoing_implementation_end_date']) ? date('d/m/Y', strtotime($row['ongoing_implementation_end_date'])) : ''; ?></td>
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
                                <td colspan="19" class="text-center text-muted py-5 bg-white">
                                    <div class="py-3">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                        <span class="fw-semibold">No records found for the selected period matching the required data parameters.</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small no-print">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
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