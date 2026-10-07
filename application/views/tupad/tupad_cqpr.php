<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DOLE TUPAD - CQPR Report</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

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
  
            <!-- ================= REPORTS HUB PAGE ================= -->
            <div class="container-fluid px-2 py-2">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 fw-bold text-dark mb-1">CQPR Report</h1>
                        <p class="text-muted mb-0">Access, filter, and monitor TUPAD project reports.</p>
                    </div>
                </div>

                               <!-- Reminder Notice -->
            <div class="alert alert-info border-0 shadow-sm mb-4 no-print bg-white border-start border-primary border-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill text-primary fs-5 me-2"></i>
                    <div class="small">
                        <strong>Reminder:</strong> Start Date and End Date filters are based on the <strong>Payout Date</strong> field found in the <strong>Payment and Payout Tab</strong>. All ADL Implementation without uploaded benefs list will not be displayed here.
                    </div>
                </div>
            </div>

                <!-- Filter Card -->
                <div class="card border-0 shadow-sm mb-4 no-print">
                    <div class="card-body">
                        <form method="GET" action="" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small text-muted">Start Date</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= isset($start_date) ? $start_date : '' ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small text-muted">End Date</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= isset($end_date) ? $end_date : '' ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-muted">Province</label>
                                <select name="province" class="form-select form-select-sm">
                                    <option value="">-- ALL PROVINCES --</option>
                                    <?php foreach ($provinces as $prov): ?>
                                        <option value="<?= $prov['provCode'] ?>" <?= (isset($selected_province) && $selected_province == $prov['provCode']) ? 'selected' : '' ?>>
                                            <?= $prov['provDesc'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-filter"></i> Filter
                                </button>
                                <a href="<?= site_url('tupad_cqpr') ?>" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </a>
                            </div>

                                                           <div class="col-md-12 text-end mt-2">
    <a href="<?= site_url('tupad_cqpr/export_xlsx?' . $_SERVER['QUERY_STRING']) ?>" class="btn btn-success btn-sm">
        <i class="bi bi-file-earmark-excel"></i> Export to XLSX
    </a>
</div>
                        </form>
                    </div>
                </div>

                <!-- Data Table Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="cqprTable" class="table table-bordered table-striped table-hover align-middle mb-0 text-center" style="font-size: 11px; width: 100%;">
                                <thead class="table-dark text-uppercase">
                                    <tr>
                                        <th>Name & Nature of Project</th>           <!-- 1 -->
                                        <th>Name of Implementer</th>              <!-- 2 -->
                                        <th>Barangay</th>                         <!-- 3 -->
                                        <th>City/Municipality</th>                  <!-- 4 -->
                                        <th>Province</th>                         <!-- 5 -->
                                        <th>District</th>                         <!-- 6 -->
                                        <th>Income Class</th>                     <!-- 7 -->
                                        <th>Work Period<br>(Short Term) <br><font color="yellow" size="0.8px">(completed_employment_benefs - adl_transactions)</font></th>      <!-- 8 -->
                                        <th>Work Period<br>(Long Term) <br><font color="yellow" size="0.8px">(completed_employment_benefs - adl_transactions)</font></th>       <!-- 9 -->
                                        <th>Total</th>                            <!-- 10 -->
                                        <th>No. of Females</th>                   <!-- 11 -->
                                        <th>Type</th>                             <!-- 12 -->
                                        <th>Amount Released</th>                  <!-- 13 -->
                                        <th>Date Released</th>                    <!-- 14 -->
                                        <th>Fund Source</th>                      <!-- 15 -->
                                        <th>Project Status</th>                   <!-- 16 -->
                                        <th>Convergence Initiative</th>           <!-- 17 -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($reports)): ?>
                                        <?php foreach ($reports as $row): ?>
                                            <tr>
                                                <td class="text-start"><?= html_escape($row['nature_of_works']) ?></td>
                                                <td>DOLE RO3 / <?= html_escape($row['implementation_province_desc']) ?> FIELD OFFICE (DIRECT ADMINISTRATION)</td>
                                                <td class="text-uppercase"><?= html_escape($row['implementation_barangay_name']) ?></td>
                                                <td><?= html_escape($row['implementation_city_desc']) ?></td>
                                                <td><?= html_escape($row['implementation_province_desc']) ?></td>
                                                <td><?= html_escape($row['implementation_district']) ?></td>
                                                <td><?= html_escape($row['implementation_classification']) ?></td>
                                                <td><?= $row['short_term'] ?></td>
                                                <td><?= $row['long_term'] ?></td>
                                                <td><strong><?= $row['total_term'] ?></strong></td>
                                                <td><?= $row['female_count'] ?></td>
                                                <td><?= html_escape($row['tupad_types']) ?></td>
                                                <td class="text-end"><?= number_format($row['amount_released'], 2) ?></td>
                                                <td><?= html_escape($row['payout_date']) ?></td>
                                                <td><?= html_escape($row['fund_source']) ?></td>
                                                <td><?= html_escape($row['project_status']) ?></td>
                                                <td><?= html_escape($row['tupad_convergence']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
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

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

  <script>
    $(document).ready(function () {
        // Toggle Sidebar
        $(document).on('click', '#sidebarToggle', function (e) {
            e.preventDefault();
            if ($(window).width() < 992) {
                $('#sidebar').toggleClass('show-mobile');
            } else {
                $('#sidebar').toggleClass('collapsed');
                $('#main-content').toggleClass('expanded');
            }
        });

        // Initialize DataTables
        $('#cqprTable').DataTable({
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "ordering": true,
            "scrollX": true,
            "language": {
                "search": "Search table:",
                "lengthMenu": "Show _MENU_ entries per page",
                "emptyTable": "Please select a Date Range or Province filter to view report records."
            }
        });
    });
    </script>
</body>

</html>