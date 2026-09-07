<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADL Implementation List - DOLE TUPAD</title>
    
    <!-- Select2 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
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

        @media print {
            body { background-color: #ffffff; }
            #sidebar, .top-navbar, .no-print { display: none !important; }
            #main-content { margin-left: 0 !important; }
        }
    </style>
</head>

<body>

    <!-- Navbar Inclusion -->
    <?php $this->load->view('templates/navbar'); ?>

    <div id="main-content">
        
        <!-- Sidebar Inclusion -->
        <?php $this->load->view('templates/sidebar'); ?>

        <main class="p-3 p-md-4 flex-grow-1">
            
            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-folder2-open text-primary me-2"></i>ADL Implementation List
                    </h3>
                    <p class="text-muted small mb-0">List of recorded ADL Implementation</p>
                </div>
            </div>

            <!-- Content Area Start -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    
                    <!-- Filter Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <form method="GET" action="" class="row g-3" id="filterForm">
                                <div class="col-md-5">
                                    <label for="implementation_province" class="form-label fw-semibold">Implementation Province</label>
                                    <select name="implementation_province" id="implementation_province" class="form-select">
                                        <option value="">-- Select Province --</option>
                                        <?php foreach ($provinces as $prov): ?>
                                            <option value="<?= $prov['provCode']; ?>" <?= ($selected_province == $prov['provCode']) ? 'selected' : ''; ?>>
                                                <?= $prov['provDesc']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-5">
                                    <label for="implementation_area" class="form-label fw-semibold">Implementation Area</label>
                                    <select name="implementation_area" id="implementation_area" class="form-select">
                                        <option value="">-- Select Area / Municipality --</option>
                                        <!-- Populated dynamically via AJAX -->
                                    </select>
                                </div>

                                <div class="col-md-2 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                    <a href="<?= site_url('adl/transaction_report'); ?>" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="transactionTable" class="table table-striped table-hover align-middle w-100">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ADL No.</th>
                                            <th>ADL Reference No.</th>
                                            <th>Implementation Province</th>
                                            <th>Implementation Area</th>
                                            <th>Proponent</th>
                                            <th>Sponsor</th>
                                            <th>Date Coordinated</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($transactions)): ?>
                                            <?php foreach ($transactions as $row): ?>
                                                <tr>
                                                    <td><strong><?= html_escape($row['adl_no']); ?></strong></td>
                                                    <td><strong><?= html_escape($row['implementation_reference_no']); ?></strong></td>
                                                    <td><?= html_escape($row['implementation_province_name'] ?? 'N/A'); ?></td>
                                                    <td><?= html_escape($row['implementation_area_name'] ?? 'N/A'); ?></td>
                                                    <td><?= html_escape($row['implementation_proponent']); ?></td>
                                                    <td><?= html_escape($row['implementation_sponsor']); ?></td>
                                                    <td><?= html_escape($row['date_coordinated']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Content Area End -->

        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small no-print">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Select2 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function () {
        // Initialize DataTable with Pagination and Search
        $('#transactionTable').DataTable({
            "language": {
                "emptyTable": "No transaction records found. Please select a filtered area."
            },
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100]
        });

        // Sidebar Toggle Handler
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

    $(document).ready(function () {
        function loadMunicipalities(provCode, selectedArea = '') {
            if (provCode) {
                $.ajax({
                    url: "<?= site_url('adl/get_municipalities_by_province'); ?>",
                    type: "GET",
                    data: { provCode: provCode },
                    dataType: "json",
                    success: function (data) {
                        $('#implementation_area').empty().append('<option value="">-- Select Area / Municipality --</option>');
                        $.each(data, function (key, value) {
                            let isSelected = (value.cityCode == selectedArea || value.citymunCode == selectedArea) ? 'selected' : '';
                            $('#implementation_area').append('<option value="' + (value.cityCode || value.citymunCode) + '" ' + isSelected + '>' + value.citymunDesc + '</option>');
                        });
                    }
                });
            } else {
                $('#implementation_area').empty().append('<option value="">-- Select Area / Municipality --</option>');
            }
        }

        // Trigger on change
        $('#implementation_province').change(function () {
            let provCode = $(this).val();
            loadMunicipalities(provCode);
        });

        // Preload if province is already selected
        let initialProv = $('#implementation_province').val();
        let initialArea = "<?= $selected_area ?? ''; ?>";
        if (initialProv) {
            loadMunicipalities(initialProv, initialArea);
        }
    });
    </script>
</body>

</html>