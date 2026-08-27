<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reporting Page</title>

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
                        <h3 class="fw-bold mb-1">Reporting Page</h3>
                        <p class="text-muted">All reports can be generated here.</p>
                    </div>
                </div>

                <!-- Modern DataTables Card -->
                <div class="card shadow-sm">
                    <div class="card-body">
               
<!-- Filter Form Section -->
<form method="GET" action="<?= site_url('tupad_report/tupad_summ_report'); ?>" class="row g-3 mb-4 no-print align-items-end">
    <div class="col-md-2">
        <label for="start_date" class="form-label fw-semibold small">Start Date</label>
        <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="<?= isset($start_date) ? html_escape($start_date) : '' ?>">
    </div>
    <div class="col-md-2">
        <label for="end_date" class="form-label fw-semibold small">End Date</label>
        <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="<?= isset($end_date) ? html_escape($end_date) : '' ?>">
    </div>
    <div class="col-md-3">
        <label for="view_type" class="form-label fw-semibold small">Report Breakdown</label>
        <select class="form-select form-select-sm" id="view_type" name="view_type">
            <option value="all" <?= (isset($view_type) && $view_type == 'all') ? 'selected' : '' ?>>All (Provinces & Municipalities)</option>
            <option value="province_only" <?= (isset($view_type) && $view_type == 'province_only') ? 'selected' : '' ?>>Provinces Only</option>
            <option value="municipality_only" <?= (isset($view_type) && $view_type == 'municipality_only') ? 'selected' : '' ?>>Municipalities Only</option>
        </select>
    </div>
    <div class="col-md-5 d-flex">
        <button type="submit" class="btn btn-primary btn-sm me-2"><i class="bi bi-filter"></i> Generate Report</button>
        
        <!-- Export Excel Button -->
        <a href="<?= site_url('tupad_report/export_excel') ?>?start_date=<?= isset($start_date) ? urlencode($start_date) : '' ?>&end_date=<?= isset($end_date) ? urlencode($end_date) : '' ?>&view_type=<?= isset($view_type) ? $view_type : 'all' ?>" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel"></i> Export Excel (.xlsx)
        </a>
    </div>
</form>

<!-- Report Table Matrix Layout -->
<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle text-center small">
        <thead class="table-primary text-uppercase">
            <tr>
                <th class="text-start">
                    <?= (isset($view_type) && $view_type == 'province_only') ? 'PROVINCE' : 'PROVINCE / MUNICIPALITY'; ?>
                </th>
                <?php if (!empty($report_data['bene_types'])): ?>
                    <?php foreach ($report_data['bene_types'] as $type): ?>
                        <th><?= html_escape($type['bene_type_desc']); ?></th>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php 
            $column_totals = [];
            if (!empty($report_data['bene_types'])) {
                foreach ($report_data['bene_types'] as $type) {
                    $column_totals[$type['bene_type_id']] = 0;
                }
            }

            if (isset($view_type) && $view_type == 'province_only') {
                // --- VIEW MODE: PROVINCES ONLY ---
                if (!empty($report_data['provinces'])): 
                    foreach ($report_data['provinces'] as $prov): 
                ?>
                    <tr>
                        <td class="fw-bold text-start"><?= html_escape($prov['provDesc']); ?></td>
                        <?php 
                        foreach ($report_data['bene_types'] as $type): 
                            $count = isset($report_data['matrix'][$prov['provCode']][$type['bene_type_id']]) 
                                     ? $report_data['matrix'][$prov['provCode']][$type['bene_type_id']] 
                                     : 0;
                            
                            $column_totals[$type['bene_type_id']] += $count;
                        ?>
                            <td><?= $count > 0 ? $count : '-'; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php 
                    endforeach; 
                endif;

            } else {
                // --- VIEW MODE: ALL or MUNICIPALITY ONLY ---
                if (!empty($report_data['provinces'])): 
                    foreach ($report_data['provinces'] as $prov): 
                        $prov_munis = array_filter($report_data['municipalities'], function($m) use ($prov) {
                            return $m['provCode'] == $prov['provCode'];
                        });

                        // Print province header section row only if 'all' is selected
                        if (!isset($view_type) || $view_type == 'all'):
                ?>
                    <tr class="table-secondary">
                        <td colspan="<?= count($report_data['bene_types']) + 1 ?>" class="fw-bold text-start text-dark">
                            <i class="bi bi-geo-alt-fill me-1"></i> <?= html_escape($prov['provDesc']); ?>
                        </td>
                    </tr>
                <?php 
                        endif;

                        if (!empty($prov_munis)):
                            foreach ($prov_munis as $muni):
                ?>
                    <tr>
                        <td class="text-start <?= (isset($view_type) && $view_type == 'municipality_only') ? 'fw-semibold' : 'ps-4'; ?>">
                            <?= (isset($view_type) && $view_type == 'municipality_only') ? html_escape($prov['provDesc'] . ' — ' . $muni['citymunDesc']) : html_escape($muni['citymunDesc']); ?>
                        </td>
                        <?php 
                        foreach ($report_data['bene_types'] as $type): 
                            $count = isset($report_data['matrix'][$prov['provCode']][$muni['cityCode']][$type['bene_type_id']]) 
                                     ? $report_data['matrix'][$prov['provCode']][$muni['cityCode']][$type['bene_type_id']] 
                                     : 0;
                            
                            $column_totals[$type['bene_type_id']] += $count;
                        ?>
                            <td><?= $count > 0 ? $count : '-'; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php 
                            endforeach;
                        endif; 
                    endforeach; 
                endif; 
            }
            ?>
        </tbody>
        <tfoot class="table-dark fw-bold">
            <tr>
                <td class="text-start">TOTAL:</td>
                <?php foreach ($report_data['bene_types'] as $type): ?>
                    <td><?= $column_totals[$type['bene_type_id']] > 0 ? $column_totals[$type['bene_type_id']] : '-'; ?></td>
                <?php endforeach; ?>
            </tr>
        </tfoot>
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
</body>

</html>
