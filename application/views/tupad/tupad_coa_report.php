<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>COA Quarterly Report - DOLE TUPAD</title>

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

        .table-responsive {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        /* Strict Table Structure Alignment */
        table.coatable th, table.coatable td {
            font-size: 0.75rem;
            vertical-align: middle;
            text-align: center;
            white-space: nowrap;
        }
        
        table.coatable th {
            background-color: #f1f5f9;
            color: #1e293b;
            border-color: #cbd5e1 !important;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <?php $this->load->view('templates/navbar'); ?>
    <?php $this->load->view('templates/sidebar'); ?>

    <div id="main-content">
        <main class="p-3 p-md-4 flex-grow-1">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 no-print">
                <div>
                    <h5 class="fw-bold mb-1 text-uppercase text-secondary" style="font-size: 0.8rem;">Department of Labor and Employment</h5>
                    <h3 class="fw-bold mb-1 text-dark" style="font-size: 1.15rem;">CONSOLIDATED QUARTERLY REPORT ON GOVERNMENT PROJECT/PROGRAM/ACTIVITIES (PPA)</h3>
                    <p class="text-muted small mb-0">As of <?= date('F d, Y'); ?></p>
                </div>
            </div>

            <!-- Filter Form Card -->
            <div class="card border-0 shadow-sm mb-4 no-print">
                <div class="card-body">
                    <form method="GET" action="<?= site_url('tupad_report/coa_tupad_report'); ?>" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label fw-semibold small">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= html_escape($start_date ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label fw-semibold small">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= html_escape($end_date ?? ''); ?>">
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-filter me-1"></i> Filter
                            </button>
                            <a href="<?= site_url('tupad_report/coa_tupad_report'); ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Inside your filter card form actions, alongside Filter button -->
<div class="col-md-4 d-flex gap-2">
    <a href="<?= site_url('tupad_report/export_coa_excel') . '?' . http_build_query($_GET); ?>" class="btn btn-success w-50">
        <i class="bi bi-file-earmark-excel me-1"></i> Export XLSX
    </a>
</div>

            <!-- Data Table Display matching COA Template -->
            <div class="table-responsive p-2">
                <table class="table table-bordered table-striped coatable mb-0">
                    <thead>
                        <tr>
                            <th rowspan="2">Agency/Adress</th>
                            <th rowspan="2">Project/Program/Activity Name</th>
                            <th colspan="2">Location</th>
                            <th rowspan="2">Physical Target</th>
                            <th rowspan="2">No. of Days</th>
                            <th rowspan="2">Total Project Cost</th>
                            <th rowspan="2">Proposed Date to Start<br><small>(DD/MM/YYYY)</small></th>
                            <th rowspan="2">Actual Date Started<br><small>(DD/MM/YYYY)</small></th>
                            <th rowspan="2">No. of Extensions</th>
                            <th rowspan="2">Target Completion Date<br><small>(DD/MM/YYYY)</small></th>
                            <th rowspan="2">Actual Date of Completion<br><small>(DD/MM/YYYY)</small></th>
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
                                    <td class="text-end"><?= !empty($row['adl_amount']) ? number_format((float)$row['adl_amount'], 2) : ''; ?></td>
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
                                <td colspan="19" class="text-center text-muted py-4 fw-semibold bg-white">
                                    <i class="bi bi-info-circle me-1"></i> No records found for the selected period. Headers are maintained above as required.
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