<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>File Records</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">

    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>

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
            margin: 0;
            padding: 0;
        }

        #main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease-in-out;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-width));
            max-width: 100%;
            overflow-x: hidden;
        }

        #main-content.expanded {
            margin-left: 0;
            width: 100%;
        }

        /* Modern Card Containers */
        .filter-card, .table-card {
            background: #ffffff;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            width: 100%;
            overflow: hidden;
        }

        .filter-card {
            padding: 1.75rem;
        }

        .table-card {
            padding: 1.5rem 1.75rem;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Floating Modernized Table Rows */
        .table {
            border-collapse: separate;
            border-spacing: 0 0.5rem;
            margin-bottom: 0 !important;
            color: var(--text-main);
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
            padding: 1rem 1rem;
            white-space: nowrap;
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
            padding: 1rem 1rem;
            vertical-align: middle;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            white-space: nowrap;
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

        /* Form Control Improvements */
        .form-control, .form-select {
            border: 1px solid var(--card-border);
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            background-color: #f8fafc;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        /* DataTables Controls Layout Overhaul */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--card-border);
            border-radius: 0.5rem;
            padding: 0.4rem 1rem;
            background-color: #f8fafc;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            background-color: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid var(--card-border);
            border-radius: 0.5rem;
            padding: 0.3rem 2rem 0.3rem 0.75rem;
            background-color: #f8fafc;
        }

        /* Pagination Style */
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

        .pagination .page-item .page-link:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }

        @media (max-width: 991.98px) {
            #main-content {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }
    </style>
</head>

<body>
     <?php $this->load->view('templates/navbar'); ?>

    <div id="main-content">
         <?php $this->load->view('templates/sidebar'); ?>

        <main class="p-3 p-md-4 flex-grow-1">
            
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="fw-bold mb-1 text-dark">
                        <i class="bi bi-file-earmark-excel text-success me-2"></i>File Records
                    </h3>
                    <p class="text-muted small mb-0">
                        <?php echo !empty($file_name) ? 'File: ' . htmlspecialchars($file_name) : 'Displaying imported records.'; ?>
                    </p>
                </div>
                <div>
                    <span class="badge bg-white text-primary border px-3 py-2 shadow-sm rounded-pill fw-semibold">
                        <i class="bi bi-database me-1"></i> Data Management
                    </span>
                </div>
            </div>

            <!-- DYNAMIC LOCATION FILTER CARD -->
            <div class="filter-card">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h6 class="fw-bold mb-0 text-primary d-flex align-items-center">
                        <i class="bi bi-funnel me-2"></i> Filter by Location (PSGC Code)
                    </h6>
                    <button type="button" id="resetFilters" class="btn btn-sm btn-link text-decoration-none p-0 text-muted fw-medium">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filters
                    </button>
                </div>
                <div class="row g-3 align-items-end">
                    <!-- Province Dropdown -->
                    <div class="col-md-3">
                        <label for="filter_province" class="form-label small fw-semibold text-secondary">Province</label>
                        <select id="filter_province" class="form-select">
                            <option value="">-- All Provinces --</option>
                            <?php if (!empty($provinces)): ?>
                                <?php foreach ($provinces as $prov): ?>
                                    <option value="<?php echo htmlspecialchars(str_pad($prov['provCode'], 9, '0', STR_PAD_LEFT)); ?>"
                                            data-name="<?php echo htmlspecialchars($prov['provDesc']); ?>">
                                        <?php echo htmlspecialchars($prov['provDesc']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- City / Municipality Dropdown -->
                    <div class="col-md-3">
                        <label for="filter_city" class="form-label small fw-semibold text-secondary">City / Municipality</label>
                        <select id="filter_city" class="form-select" disabled>
                            <option value="">-- Select Province First --</option>
                        </select>
                    </div>

                    <!-- Barangay Dropdown -->
                    <div class="col-md-3">
                        <label for="filter_barangay" class="form-label small fw-semibold text-secondary">Barangay</label>
                        <select id="filter_barangay" class="form-select" disabled>
                            <option value="">-- Select City First --</option>
                        </select>
                    </div>

                    <!-- Manual Filter Submit Button -->
                    <div class="col-md-3 d-flex gap-2">
                        <button type="button" id="applyFilter" class="btn btn-primary w-100 fw-semibold shadow-sm" style="background: var(--primary-gradient); border: none;">
                            <i class="bi bi-search me-1"></i> Filter Records
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="table-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold m-0 text-dark d-flex align-items-center">
                        <i class="bi bi-table text-primary me-2"></i> Records List
                    </h5>
                </div>
                <div class="table-responsive">
                    <table id="recordsTable" class="table align-middle w-100">
                        <thead>
                            <tr>
                                <th>View Profile</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Ext Name</th>
                                <th>Date of Birth</th>
                                <th>Province</th>
                                <th>Municipality</th>
                                <th>Barangay</th>        
                                <th>Uploaded By</th>
                                <th>Date Uploaded</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables server-side processing populates this via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>  
    $(document).ready(function () {
        $(document).on('click', '#sidebarToggle', function () {
            if ($(window).width() < 992) {
                $('#sidebar').toggleClass('show-mobile');
            } else {
                $('#sidebar').toggleClass('collapsed');
                $('#main-content').toggleClass('expanded');
            }
        });

        var fileName = "<?php echo isset($file_name) ? addslashes($file_name) : ''; ?>";
        var ajaxUrl = "<?php echo site_url('tupad/get_records_json'); ?>";

        var storageKeyProv = 'tupad_filter_province_' + (fileName ? fileName : 'general');
        var storageKeyCity = 'tupad_filter_city_' + (fileName ? fileName : 'general');
        var storageKeyBrgy = 'tupad_filter_barangay_' + (fileName ? fileName : 'general');

        // Clear out old local storage on fresh page load to prevent sticky filters
        localStorage.removeItem(storageKeyProv);
        localStorage.removeItem(storageKeyCity);
        localStorage.removeItem(storageKeyBrgy);

        var table = $('#recordsTable').DataTable({
            pageLength: 25,
            responsive: true,
            processing: true,
            deferRender: true,
            serverSide: true,
            searchDelay: 500,
            deferLoading: 0,
            language: {
                loadingRecords: "Please select a location filter and click 'Filter Records'...", 
                emptyTable: "No records found. Please choose your filters and click 'Filter Records'."
            },
            ajax: {
                url: ajaxUrl,
                type: "POST",
                data: function (d) {
                    if (fileName) {
                        d.file_name = fileName;
                    }
                    
                    d.province = $('#filter_province').val();
                    d.city = $('#filter_city').val();
                    d.barangay = $('#filter_barangay').val();

                    return d; 
                }
            },
            columns: [
                { 
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        var profileUrl = "<?php echo site_url('tupad/view_profile/'); ?>" + row.id;
                        return '<a href="' + profileUrl + '" class="btn btn-sm btn-primary px-3 shadow-sm rounded-pill fw-medium" title="View Profile" target="_blank" style="background: var(--primary-gradient); border: none;">' +
                                '<i class="bi bi-eye me-1"></i> View</a>';
                    }
                },
                { data: 'tupad_fname', render: function(data) { return data ? data.toUpperCase() : ''; } },
                { data: 'tupad_mname', render: function(data) { return data ? data.toUpperCase() : ''; } },
                { data: 'tupad_lname', render: function(data) { return data ? data.toUpperCase() : ''; } },
                { data: 'tupad_ext', render: function(data) { return data ? data.toUpperCase() : ''; } },
                { 
                    data: null, 
                    render: function (data, type, row) {
                        return (row.tupad_dob_month || '') + '/' + (row.tupad_dob_day || '') + '/' + (row.tupad_dob_year || '');
                    }
                },
                { data: 'province_name' },      
                { data: 'municipality_name' }, 
                { data: 'barangay_name' },      
                { 
                    data: null, 
                    render: function (data, type, row) {
                        var uploader = $.trim((row.uploader_fname || '') + ' ' + (row.uploader_lname || ''));
                        return uploader !== '' ? uploader : 'N/A';
                    }
                },
                { 
                    data: 'uploaded_at',
                    render: function (data) {
                        if (!data) return 'N/A';
                        var date = new Date(data);
                        return date.toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                    }
                },
                { 
                    data: 'tupad_active',
                    render: function (data) {
                        if (data == '0') {
                            return '<span class="badge bg-success-subtle text-success px-2.5 py-1.5 fw-semibold border border-success-subtle rounded-pill">Active</span>';
                        } else {
                            return '<span class="badge bg-secondary-subtle text-secondary px-2.5 py-1.5 fw-semibold border border-secondary-subtle rounded-pill">Inactive</span>';
                        }
                    }
                }
            ]
        });

        $('#filter_province').on('change', function () {
            var provCode = $(this).val();
            
            if (provCode) {
                localStorage.setItem(storageKeyProv, provCode);
            } else {
                localStorage.removeItem(storageKeyProv);
            }
            localStorage.removeItem(storageKeyCity);
            localStorage.removeItem(storageKeyBrgy);

            $('#filter_city').html('<option value="">Loading Cities...</option>').prop('disabled', true);
            $('#filter_barangay').html('<option value="">-- Select City First --</option>').prop('disabled', true);

            if (provCode) {
                $.ajax({
                    url: "<?php echo site_url('tupad/get_cities'); ?>",
                    type: "POST",
                    data: { provCode: provCode },
                    dataType: "json",
                    success: function (data) {
                        var options = '<option value="">-- All Cities --</option>';
                        $.each(data, function (index, item) {
                            var cCode = item.citymunCode || item.psgcCode || item.cityCode;
                            options += '<option value="' + cCode + '">' + item.citymunDesc + '</option>';
                        });
                        $('#filter_city').html(options).prop('disabled', false);
                    }
                });
            } else {
                $('#filter_city').html('<option value="">-- Select Province First --</option>');
            }
        });

        $('#filter_city').on('change', function () {
            var citymunCode = $(this).val();
            
            if (citymunCode) {
                localStorage.setItem(storageKeyCity, citymunCode);
            } else {
                localStorage.removeItem(storageKeyCity);
            }
            localStorage.removeItem(storageKeyBrgy);

            $('#filter_barangay').html('<option value="">Loading Barangays...</option>').prop('disabled', true);

            if (citymunCode) {
                $.ajax({
                    url: "<?php echo site_url('tupad/get_barangays'); ?>",
                    type: "POST",
                    data: { citymunCode: citymunCode },
                    dataType: "json",
                    success: function (data) {
                        var options = '<option value="">-- All Barangays --</option>';
                        $.each(data, function (index, item) {
                            var bCode = item.brgyCode || item.psgcCode;
                            options += '<option value="' + bCode + '">' + item.brgyDesc + '</option>';
                        });
                        $('#filter_barangay').html(options).prop('disabled', false);
                    }
                });
            } else {
                $('#filter_barangay').html('<option value="">-- Select City First --</option>');
            }
        });

        $('#filter_barangay').on('change', function () {
            var brgyCode = $(this).val();
            if (brgyCode) {
                localStorage.setItem(storageKeyBrgy, brgyCode);
            } else {
                localStorage.removeItem(storageKeyBrgy);
            }
        });

        // MANUALLY TRIGGER TABLE RELOAD WHEN BUTTON IS CLICKED
        $('#applyFilter').on('click', function() {
            table.ajax.reload();
        });

        $('#resetFilters').on('click', function() {
            localStorage.removeItem(storageKeyProv);
            localStorage.removeItem(storageKeyCity);
            localStorage.removeItem(storageKeyBrgy);

            $('#filter_province').val('');
            $('#filter_city').html('<option value="">-- Select Province First --</option>').prop('disabled', true);
            $('#filter_barangay').html('<option value="">-- Select City First --</option>').prop('disabled', true);
            table.clear().draw();
        });
      
    });
    </script>

</body>

</html>