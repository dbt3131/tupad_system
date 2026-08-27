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

        .card {
            border: 1px solid var(--card-border);
            border-radius: 0.5rem;
        }

        @media print {
            body { background-color: #ffffff; }
            #sidebar, .top-navbar, .no-print { display: none !important; }
            #main-content { margin-left: 0 !important; }
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
        <main class="p-3 p-md-4 flex-grow-1">
            <div class="container-fluid px-0">
                <div class="row mb-4">
                    <div class="col-12">
                        <h3 class="fw-bold mb-1">Activity History</h3>
                        <p class="text-muted">All activities executed in the system are recorded here.</p>
                    </div>
                </div>

                <!-- Modern DataTables Card -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <!-- Temporary Debug Check -->

                            <table id="activityTable" class="table table-striped table-hover align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3">#</th>
                                        <th class="py-3">User First Name</th>
                                        <th class="py-3">Activity Description</th>
                                        <th class="py-3">Activity Date</th>
                                    </tr>
                                </thead>
                                <tbody>
    <?php if (!empty($activities) && is_array($activities)): ?>
        <?php $no = 1; foreach ($activities as $row): ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['reg_fname'] ?? ''); ?></td>
                <td><?= htmlspecialchars($row['activity_desc'] ?? ''); ?></td>
                <td><?= htmlspecialchars($row['activity_date'] ?? ''); ?></td>
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
        // Initialize DataTables plugin
        $('#activityTable').DataTable({
            "order": [[3, "desc"]], // Sort by date descending by default
            "pageLength": 10,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search activities..."
            }
        });

        // Sidebar responsive toggle logic
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