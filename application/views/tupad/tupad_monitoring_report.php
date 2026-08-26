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

            <!-- Date Filtering Form Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-3">
                    <form method="get" action="<?= site_url('tupad_monitoring'); ?>" class="row g-3 align-items-end">
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
                            <a href="<?= site_url('tupad_monitoring'); ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
                            
                            <?php if (!empty($start_date) && !empty($end_date)): ?>
                                <a href="<?= site_url('tupad_monitoring?start_date=' . $start_date . '&end_date=' . $end_date . '&pdf=1'); ?>" target="_blank" class="btn btn-danger btn-sm ms-auto">
                                    <i class="bi bi-file-pdf me-1"></i> View/Print PDF Report
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm text-center align-middle" style="font-size: 0.85rem;">
                <thead class="table-dark">
                    <tr>
                        <th>Date Coordinated</th>
                        <th>ADL No.</th>
                        <th>Reference No.</th>
                        <th>Assigned Province</th>
                        <th>LGU Municipality / City</th>
                        <th>Batch No</th>
                        <th>Physical Target</th>
                        <th>GPAI Total</th>
                        <th>PPE Total</th>
                        <th>Disbursed Amount (Total Funds)</th>
                        <th>Period of Work</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $has_dates = (!empty($start_date) && !empty($end_date));
                        $display_allocations = $has_dates ? $allocations : [];
                    ?>

                    <?php if (!empty($display_allocations) && is_array($display_allocations)): ?>
                        <?php 
                            $total_target = 0;
                            $total_gpai = 0;
                            $total_ppe = 0;
                            $total_disbursed = 0;
                        ?>
                        <?php foreach ($display_allocations as $row): ?>
                            <?php 
                                $total_target += intval($row['physical_target']);
                                $total_gpai += intval($row['gpai_count']);
                                $total_ppe += intval($row['ppe_count']);
                                $total_disbursed += floatval($row['total_project_funds']);
                            ?>
                            <tr>
                                <td><?= html_escape($row['date_coordinated']); ?></td>
                                <td><?= html_escape($row['adl_no']); ?></td>
                                <td><code><?= html_escape($row['reference_no']); ?></code></td>
                                <td class="fw-semibold text-start"><?= html_escape($row['province_name'] ?? 'N/A'); ?></td>
                                <td class="text-start"><?= html_escape($row['lgu_municipality_city']); ?></td>
                                <td><?= html_escape($row['batch_no']); ?></td>
                                <td class="fw-bold text-success"><?= number_format($row['physical_target']); ?></td>
                                <td class="fw-bold text-info"><?= number_format($row['gpai_count']); ?></td>
                                <td class="fw-bold text-warning"><?= number_format($row['ppe_count']); ?></td>
                                <td class="fw-bold text-primary">₱<?= number_format($row['total_project_funds'], 2); ?></td>
                                <td><?= html_escape($row['period_start']); ?> to <?= html_escape($row['period_end']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-secondary fw-bold">
                            <td colspan="6" class="text-end">OVERALL SUMMARY:</td>
                            <td class="text-success"><?= number_format($total_target); ?></td>
                            <td class="text-info"><?= number_format($total_gpai); ?></td>
                            <td class="text-warning"><?= number_format($total_ppe); ?></td>
                            <td class="text-primary">₱<?= number_format($total_disbursed, 2); ?></td>
                            <td></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">
                                <?= (!$has_dates) ? 'Please select a Period Start Date and End Date to view monitoring reports.' : 'No monitoring logs found for the selected period date.'; ?>
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