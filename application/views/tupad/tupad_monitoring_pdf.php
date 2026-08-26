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
/* Prevent accidental extra pages and control page size */
@page {
    size: A4 portrait; /* or landscape */
    margin: 10mm 10mm; /* tighter margins to fit everything */
}

body, html {
    height: 100%;
    margin: 0;
    padding: 0;
    font-size: 13px; /* Slightly reduce font size to guarantee single-page fit */
}

/* Ensure the main container doesn't overflow */
#main-content {
    min-height: auto !important;
}

/* Prevent page breaks inside cards and tables */
.card, .table-responsive, table {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
}

/* Print-specific cleanup */
@media print {
    body {
        background-color: #ffffff !important;
    }
    
    /* Hide scrollbars or overflowing elements */
    * {
        overflow: visible !important;
    }
}

@media print {
    /* Hide navigation, sidebars, and action buttons when printing/saving */
    #sidebar, navbar, .btn, footer, .d-flex.gap-2 {
        display: none !important;
    }
    
    /* Reset layout margins for full-page PDF printing */
    body {
        background-color: #ffffff !important;
        color: #000000 !important;
    }
    
    #main-content {
        margin-left: 0 !important;
        padding: 0 !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }
}

        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 70px;
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

        /* Sidebar Styling Layout Defaults */
        #sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1040;
            transition: all 0.3s ease-in-out;
        }

        #sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        /* Main Content Fluid Alignment */
        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease-in-out;
        }

        #main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Responsive Mobile Adjustments */
        @media (max-width: 991.98px) {
            #sidebar {
                left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.show-mobile {
                left: 0;
            }
            #main-content, #main-content.expanded {
                margin-left: 0 !important;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR TEMPLATE VIEW -->
    <?php $this->load->view('templates/navbar'); ?>

    <!-- SIDEBAR TEMPLATE VIEW -->
    <?php $this->load->view('templates/sidebar'); ?>

    <!-- Main Content Wrapper -->
    <div id="main-content">
        
        <!-- Main Workspace -->
        <main class="p-3 p-md-4 flex-grow-1" style="min-height: calc(100vh - 120px);">
            
            <!-- FLASH MESSAGES SECTION -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?= html_escape($this->session->flashdata('success')); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= html_escape($this->session->flashdata('error')); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

           <!-- Page Header Area with Back and PDF Download Buttons -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1">
            <i class="bi bi-file-earmark-text text-primary me-2"></i>TUPAD Monitoring Report
        </h3>
        <p class="text-muted small mb-0">Overview of generated tracking, allocation, GPAI, and PPE records</p>
    </div>
    <div class="d-flex gap-2">
       <!-- Download / Save as PDF Button -->
<?php if (!empty($start_date) && !empty($end_date)): ?>
    <button type="button" onclick="printReportToPDF()" class="btn btn-danger shadow-sm">
        <i class="bi bi-file-pdf me-1"></i> Save as PDF Report
    </button>
<?php endif; ?>

        <!-- Back Button pointing explicitly to the Monitoring Report route -->
        <a href="<?= site_url('tupad_monitoring'); ?>" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Monitoring Report
        </a>
    </div>
</div>

            <!-- Report Header Info -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="header mb-4 text-center">
                        <h4 class="text-muted mb-1 fs-6">Department of Labor and Employment</h4>
                        <h2 class="fw-bold text-primary fs-4">TUPAD Monitoring Report</h2>
                        <div class="meta-info text-secondary small">
                            <strong>Period:</strong> <?= !empty($start_date) ? html_escape($start_date) : 'N/A'; ?> to <?= !empty($end_date) ? html_escape($end_date) : 'N/A'; ?>
                        </div>
                    </div>

                    <?php 
                        $total_target = 0;
                        $total_disbursed = 0;
                        $total_gpai = 0;
                        $total_ppe = 0;
                        
                        if (!empty($allocations) && is_array($allocations)) {
                            foreach ($allocations as $row) {
                                $total_target += intval($row['physical_target'] ?? 0);
                                $total_disbursed += floatval($row['total_project_funds'] ?? 0);
                                $total_gpai += intval($row['gpai_count'] ?? 0); 
                                $total_ppe += intval($row['ppe_count'] ?? 0);
                            }
                        }
                    ?>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm align-middle">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Date Coordinated</th>
                                    <th>ADL No.</th>
                                    <th>Reference No.</th>
                                    <th>Assigned Province</th>
                                    <th>LGU Municipality / City</th>
                                    <th>Batch No</th>
                                    <th>Physical Target</th>
                                    <th>GPAI</th>
                                    <th>PPE</th>
                                    <th>Disbursed Amount</th>
                                    <th>Period of Work</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($allocations) && is_array($allocations)): ?>
                                    <?php 
                                        // Build Prepared By name from the first result record
                                        $first_row = $allocations[0];
                                        $prepared_by = 'N/A';
                                        if (!empty($first_row['reg_fname'])) {
                                            $m_initial = !empty($first_row['reg_mname']) ? strtoupper(substr($first_row['reg_mname'], 0, 1)) . '.' : '';
                                            $prepared_by = trim(html_escape($first_row['reg_fname'] . ' ' . $m_initial . ' ' . $first_row['reg_lname'] . ' ' . $first_row['reg_extname']));
                                        }
                                    ?>
                                    <?php foreach ($allocations as $row): ?>
                                        <tr>
                                            <td class="text-center"><?= html_escape($row['date_coordinated']); ?></td>
                                            <td class="text-center"><?= html_escape($row['adl_no']); ?></td>
                                            <td class="text-center"><?= html_escape($row['reference_no']); ?></td>
                                            <td class="text-start"><?= html_escape($row['province_name'] ?? 'N/A'); ?></td>
                                            <td class="text-start"><?= html_escape($row['lgu_municipality_city']); ?></td>
                                            <td class="text-center"><?= html_escape($row['batch_no']); ?></td>
                                            <td class="text-center"><?= number_format($row['physical_target'] ?? 0); ?></td>
                                            <td class="text-center"><?= number_format($row['gpai_count'] ?? 0); ?></td>
                                            <td class="text-center"><?= number_format($row['ppe_count'] ?? 0); ?></td>
                                            <td class="text-end">₱<?= number_format($row['total_project_funds'] ?? 0, 2); ?></td>
                                            <td class="text-center"><?= html_escape($row['period_start']); ?> to <?= html_escape($row['period_end']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="table-secondary fw-bold">
                                        <td colspan="6" class="text-end">OVERALL SUMMARY:</td>
                                        <td class="text-center"><?= number_format($total_target); ?></td>
                                        <td class="text-center"><?= number_format($total_gpai); ?></td>
                                        <td class="text-center"><?= number_format($total_ppe); ?></td>
                                        <td class="text-end">₱<?= number_format($total_disbursed, 2); ?></td>
                                        <td></td>
                                    </tr>
                                <?php else: ?>
                                    <?php $prepared_by = 'N/A'; ?>
                                    <tr>
                                        <td colspan="11" class="text-center py-4 text-muted">No monitoring logs found for the selected period date.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Prepared By Section -->
                    <div class="row mt-5">
                        <div class="col-md-4">
                            <p class="mb-1 text-muted small">Prepared By:</p>
                            <br>
                            <br>
                            <h5 class="fw-bold text-dark border-bottom pb-2 mb-1" style="display: inline-block; min-width: 250px;">
                                <?= $prepared_by; ?>
                            </h5>
                            <p class="text-muted small mb-0">TUPAD Encoder / Staff</p>
                        </div>
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

    <!-- Sidebar Collapse/Expand Script -->
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

        function printReportToPDF() {
        // Opens the PDF view in a new window/tab and automatically triggers print
        let printWindow = window.open('<?= site_url('tupad_monitoring?start_date=' . $start_date . '&end_date=' . $end_date . '&pdf=1'); ?>', '_blank');
        
        printWindow.onload = function() {
            setTimeout(function() {
                printWindow.print();
            }, 500); // Small delay to ensure styles and tables render fully
        };
    }
    </script>
</body>

</html>