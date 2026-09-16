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
    </style>
</head>

<body>

    <?php $this->load->view('templates/navbar'); ?>
    <?php $this->load->view('templates/sidebar'); ?>

    <div id="main-content">
        <main class="p-3 p-md-4 flex-grow-1">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 no-print">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-journal-plus text-primary me-2"></i>COA QUARTERLY REPORT
                    </h3>
                    <p class="text-muted small mb-0">Filter and view TUPAD activities report</p>
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

            <!-- Data Table Display -->
            <div class="table-responsive p-3">
                <table class="table table-bordered table-striped align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Agency/Address</th>
                            <th>Project/Program/Activity Name</th>
                            <th>Province</th>
                            <th>LGU/Municipality</th>
                            <th>Physical Target</th>
                            <th>No. of Days</th>
                            <th>Total Project Cost</th>
                            <th>Proposed Date to Start</th>
                            <th>Actual Date Started</th>
                            <th>Target Completion Date</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($report_data)): ?>
                            <?php foreach ($report_data as $row): ?>
                                <tr>
                                    <td>DOLE RO 3</td>
                                    <td>TUPAD</td>
                                    <td><?= html_escape($row['province_name']); ?></td>
                                    <td><?= html_escape($row['municipality_name']); ?></td>
                                    <td class="text-center"><?= html_escape($row['target']); ?></td>
                                    <td class="text-center"><?= html_escape($row['no_of_days']); ?></td>
                                    <td class="text-end"><?= !empty($row['adl_amount']) ? number_format((float)$row['adl_amount'], 2) : '0.00'; ?></td>
                                    <td class="text-center"><?= html_escape($row['ongoing_implementation_start_date']); ?></td>
                                    <td class="text-center"><?= html_escape($row['ongoing_implementation_start_date']); ?></td>
                                    <td class="text-center"><?= html_escape($row['ongoing_implementation_end_date']); ?></td>
                                    <td><?= html_escape($row['remarks']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">No records found for the selected period.</td>
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