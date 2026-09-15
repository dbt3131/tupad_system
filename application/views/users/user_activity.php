<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Activity History</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- DataTables CSS Bootstrap 5 Integration -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            --bg-body: #f4f6f9;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --card-border: #e2e8f0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            overflow-x: hidden;
        } 

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            background: #ffffff;
            overflow: hidden;
        }

        .card-header-custom {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.5rem 1.75rem;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0 0.5rem;
            margin-bottom: 0 !important;
        }

        .table thead th {
            background-color: #f8fafc !important;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            border-top: none;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
        }

        .table tbody tr {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.01);
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.04);
            background-color: #ffffff !important;
        }

        .table tbody td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
        }

        .table tbody td:first-child {
            border-left: 1px solid #f1f5f9;
            border-top-left-radius: 0.5rem;
            border-bottom-left-radius: 0.5rem;
        }

        .table tbody td:last-child {
            border-right: 1px solid #f1f5f9;
            border-top-right-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--card-border);
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            background-color: #f8fafc;
            transition: all 0.2s;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            background-color: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid var(--card-border);
            border-radius: 0.5rem;
            padding: 0.4rem 2rem 0.4rem 0.75rem;
            background-color: #f8fafc;
        }

        .pagination .page-item .page-link {
            border: none;
            border-radius: 0.375rem;
            margin: 0 3px;
            color: #475569;
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            background-color: #f1f5f9;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-gradient);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
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

    <div id="main-content">
        <?php $this->load->view('templates/sidebar'); ?>

        <main class="p-3 p-md-4 flex-grow-1">
            <div class="container-fluid px-0">
                <div class="row mb-4 align-items-center">
                    <div class="col-sm-8">
                        <h3 class="fw-bold mb-1 text-dark">Activity History</h3>
                        <p class="text-muted mb-0">Track and monitor all core operations and user workflows across the system.</p>
                    </div>
                    <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
                        <span class="badge bg-white text-primary border px-3 py-2 shadow-sm rounded-pill fw-semibold">
                            <i class="bi bi-shield-check me-1"></i> Secure System Log
                        </span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h5 class="card-title fw-bold m-0 text-dark d-flex align-items-center">
                            <i class="bi bi-clock-history text-primary me-2"></i> System Logs
                        </h5>
                    </div>
                    <div class="card-body px-4 py-3">
                        <div class="table-responsive">
                            <table id="activityTable" class="table align-middle w-100">
                                <thead>
                                    <tr>
                                        <th>Activity Date</th>
                                        <th>User Details</th>
                                        <th>Activity Description</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($activities) && is_array($activities)): ?>
                                        <?php foreach ($activities as $row): ?>
                                            <?php 
                                                $fname = $row['reg_fname'] ?? 'Unknown User';
                                                $initial = strtoupper(substr($fname, 0, 1));
                                                $position = $row['position_description'] ?? 'N/A';
                                                $division = 'Division ' . ($row['division_description'] ?? 'N/A');
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($row['activity_date'] ?? ''); ?></div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-3 shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background: linear-gradient(135deg, #6366f1, #a855f7); font-size: 0.85rem;">
                                                            <?= $initial; ?>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark"><?= htmlspecialchars($fname); ?></div>
                                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                                <span class="fw-medium text-secondary"><?= htmlspecialchars($position); ?></span> &bull; 
                                                                <span class="italic"><?= htmlspecialchars($division); ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-secondary fw-medium"><?= htmlspecialchars($row['activity_desc'] ?? ''); ?></span>
                                                </td>
                                                <td>
                                                    <?php if(!empty($row['remarks'])): ?>
                                                        <span class="badge bg-light text-secondary border px-2 py-1 fw-normal"><?= htmlspecialchars($row['remarks']); ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted fst-italic" style="font-size: 0.85rem;">None</span>
                                                    <?php endif; ?>
                                                </td>
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
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS & BS5 Setup -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function () {
        $('#activityTable').DataTable({
            "language": {
                "emptyTable": "No activity history found.",
                "search": "",
                "searchPlaceholder": "Search logs..."
            },
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100],
            "columnDefs": [
                {
                    "targets": 0,
                    "type": "string" 
                }
            ],
            "order": [[0, "desc"]] 
        });

        $('.dataTables_filter input').addClass('form-control form-control-sm');
        $('.dataTables_length select').addClass('form-select form-select-sm');

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