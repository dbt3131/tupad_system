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
  




<!-- ================= REPORTS HUB PAGE ================= -->
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Reports Dashboard</h1>
            <p class="text-muted mb-0">Access, filter, and generate reports.</p>
        </div>
        <div>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold">
                <i class="bi bi-shield-check me-1"></i> Official Records
            </span>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="row g-4">
        
        <!-- Report 1: ADL Distribution Report -->
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 report-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3 mb-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-file-earmark-bar-graph fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">ADL Distribution Report</h5>
                    <p class="text-muted small mb-4 flex-grow-1">View and track distribution breakdowns, beneficiary counts, and allocations related to ADL.</p>
                    <a href="<?= site_url('adl/adl_report'); ?>" class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                        <span>Open Report</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Report 2: COA Quarterly Report -->
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 report-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-3 mb-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-journal-check fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">COA Quarterly Report</h5>
                    <p class="text-muted small mb-4 flex-grow-1">Generate compliant quarterly reports structured specifically for Commission on Audit standards.</p>
                    <a href="<?= site_url('tupad_report/coa_tupad_report_page'); ?>" class="btn btn-outline-success btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                        <span>Open Report</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Report 3: Implementation Status -->
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 report-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-3 mb-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-activity fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Implementation Status</h5>
                    <p class="text-muted small mb-4 flex-grow-1">Monitor progress trackers and ongoing execution phases for TUPAD projects.</p>
                    <a href="<?= site_url('tupad_report/tupad_implementation_status_report'); ?>" class="btn btn-outline-warning text-dark btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                        <span>Open Report</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Report 4: Beneficiaries Summary -->
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 report-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="icon-box bg-info bg-opacity-10 text-info rounded-3 mb-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Beneficiaries Summary</h5>
                    <p class="text-muted small mb-4 flex-grow-1">Access summarized deductions per ADL.</p>
                    <a href="<?= site_url('tupad_report/tupad_summ_report'); ?>" class="btn btn-outline-info btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                        <span>Open Report</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>


<!-- Report 3: Implementation Status -->
<div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 report-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="icon-box rounded-3 mb-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(236, 72, 153, 0.1); color: #db2777;">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Transparency Report</h5>
                    <p class="text-muted small mb-4 flex-grow-1">List of TUPAD beneficiaries.</p>
                    <a href="<?= site_url('tupad_transparency/index'); ?>" class="btn btn-sm w-100 d-flex align-items-center justify-content-center gap-2 text-dark" style="border-color: #db2777; background-color: #fff; color: #db2777 !important; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#db2777'; this.style.color='#ffffff';" onmouseout="this.style.backgroundColor='#fff'; this.style.color='#db2777';">
                        <span>Open Report</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        
<!-- Report 3: Implementation Status -->
<div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 report-card">
                <div class="card-body d-flex flex-column p-4">
                    <div class="icon-box rounded-3 mb-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(236, 72, 153, 0.1); color: #db2777;">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">CQPR Report</h5>
                    <p class="text-muted small mb-4 flex-grow-1">List of ADL Implemented.</p>
                    <a href="<?= site_url('tupad_cqpr/index'); ?>" class="btn btn-sm w-100 d-flex align-items-center justify-content-center gap-2 text-dark" style="border-color: #5cd668; background-color: #fff; color: #72db5a !important; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#77ee5c'; this.style.color='#ffffff';" onmouseout="this.style.backgroundColor='#fff'; this.style.color='#5fe56d';">
                        <span>Open Report</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>




    </div>
</div>

<style>
    .report-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .report-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.08) !important;
    }
</style>




























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