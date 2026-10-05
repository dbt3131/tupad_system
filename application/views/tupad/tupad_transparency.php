<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TUPAD Transparency & Reports - DOLE</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- DataTables CSS -->
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
            font-size: 14px;
        } 

        .record-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        @media print {
            body { background-color: #ffffff !important; }
            #sidebar, .top-navbar, .no-print { display: none !important; }
            #main-content { margin-left: 0 !important; padding: 0 !important; }
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <?php $this->load->view('templates/navbar'); ?>

    <div id="main-content">
        <!-- Sidebar -->
        <?php $this->load->view('templates/sidebar'); ?>

        <!-- Main Workspace Area -->
        <main class="p-3 p-md-4 flex-grow-1">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-3 no-print">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-file-earmark-spreadsheet me-2"></i>TUPAD Beneficiary Reports & Transparency</h3>
                    <p class="text-muted small mb-0">Department of Labor and Employment &bull; Multi-Sheet Excel Generation</p>
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
            <div class="record-card p-4 mb-4 no-print">
                <form id="filterForm" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label fw-semibold small">Start Payout Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label fw-semibold small">End Payout Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date">
                    </div>
                    <div class="col-md-3">
                        <label for="province_code" class="form-label fw-semibold small">Province</label>
                        <select class="form-select" id="province_code" name="province_code">
                            <option value="">All Provinces</option>
                            <?php foreach($provinces as$prov): ?>
                                <option value="<?= $prov->provCode; ?>"><?= $prov->provDesc; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="button" id="btnFilter" class="btn btn-primary w-50 shadow-sm"><i class="bi bi-filter me-1"></i> Filter</button>
                        <button type="button" id="btnExport" class="btn btn-success w-50 shadow-sm"><i class="bi bi-file-excel me-1"></i>Transparency Report</button>
                    </div>
                </form>
            </div>

            <!-- Data Table Card -->
            <div class="record-card p-4">
                <div class="table-responsive">
                    <table id="beneficiaryTable" class="table table-striped table-hover align-middle w-100">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Sex</th>
                                <th>Birthdate</th>
                                <th>Age</th>
                                <th>Address</th>
                                <th>Barangay</th>
                                <th>Municipality</th>
                                <th>Province</th>
                                <th>Beneficiary Type</th>
                                <th>Tupad Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamic Data via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small no-print">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    let table = $('#beneficiaryTable').DataTable();

    function loadTableData() {
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();

        // Only dates are mandatory now; province can be left as "All Provinces"
        if (!startDate || !endDate) {
            alert('Please select both a Start Date and an End Date before filtering.');
            table.clear().draw(); 
            return;
        }

        $.ajax({
            url: "<?= site_url('tupad_transparency/fetch_data'); ?>",
            type: "POST",
            data: $('#filterForm').serialize(),
            dataType: "json",
            success: function (response) {
                table.clear();
                if (response.status === 'success' && response.data.length > 0) {
                    let counter = 1;
                    response.data.forEach(row => {
                        table.row.add([
                            counter++,
                            `${row.tupad_lname || ''}, ${row.tupad_fname || ''} ${row.tupad_mname || ''} ${row.tupad_ext || ''}`,
                            row.tupad_gender || '',
                            `${row.tupad_dob_month || '/'}/${row.tupad_dob_day || '/'}/${row.tupad_dob_year || ''}`,
                            row.tupad_age || '',
                            row.tupad_street || '',
                            row.brgy_name || '',
                            row.city_name || '',
                            row.province_name || '',
                            row.tupad_dependent || '',
                            row.bene_type_desc || ''
                        ]);
                    });
                }
                table.draw();
            }
        });
    }

    $('#btnFilter').on('click', function () {
        loadTableData();
    });

    $('#btnExport').on('click', function () {
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();

        if (!startDate || !endDate) {
            alert('Please select both a Start Date and an End Date before exporting.');
            return;
        }

        let params = $('#filterForm').serialize();
        window.location.href = "<?= site_url('tupad_transparency/export_excel?'); ?>" + params;
    });

    // Sidebar Toggle Handler
    $(document).on('click', '#sidebarToggle', function (e) {
        e.preventDefault();
        if ($(window).width() < 992) {$('#sidebar').toggleClass('show-mobile');
        } else {
            $('#sidebar').toggleClass('collapsed');
            $('#main-content').toggleClass('expanded');
        }
    });
});
</script>

</body>
</html>