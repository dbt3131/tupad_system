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
            margin: 0;
            padding: 0;
        }

        /* --- MAIN CONTENT & NAVBAR --- */
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

        .filter-card, .table-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            width: 100%;
        }

        .table-card {
            overflow: hidden;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .dataTables_wrapper .dataTables_filter, 
        .dataTables_wrapper .dataTables_length {
            display: flex;
            justify-content: center;
            text-align: center !important;
        }

        /* Responsive Fixes for Mobile / Small Screens (< 992px) */
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

        <!-- Main Workspace -->
        <main class="p-3 p-md-4 flex-grow-1">
            
            <!-- Navigation Back Button & Title -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <a href="<?php echo site_url('tupad/view_files'); ?>" class="btn btn-sm btn-outline-secondary mb-2">
                        <i class="bi bi-arrow-left me-1"></i> Back to File List
                    </a>
                    <h3 class="fw-bold mb-0">
                        <i class="bi bi-file-earmark-excel text-success me-2"></i>File Records
                    </h3>
                    <p class="text-muted small mb-0">
                        <?php echo !empty($file_name) ? 'File: ' . htmlspecialchars($file_name) : 'Displaying imported records.'; ?>
                    </p>
                </div>
            </div>

            <!-- DYNAMIC LOCATION FILTER CARD -->
            <div class="filter-card p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-funnel me-1"></i> Filter by Location (PSGC Code)
                    </h6>
                    <button type="button" id="resetFilters" class="btn btn-sm btn-link text-decoration-none p-0 text-muted">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filters
                    </button>
                </div>
                <div class="row g-3 align-items-end">
                    <!-- Province Dropdown -->
                    <div class="col-md-3">
                        <label for="filter_province" class="form-label small fw-semibold">Province</label>
                        <select id="filter_province" class="form-select form-select-sm">
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
                        <label for="filter_city" class="form-label small fw-semibold">City / Municipality</label>
                        <select id="filter_city" class="form-select form-select-sm" disabled>
                            <option value="">-- Select Province First --</option>
                        </select>
                    </div>

                    <!-- Barangay Dropdown -->
                    <div class="col-md-3">
                        <label for="filter_barangay" class="form-label small fw-semibold">Barangay</label>
                        <select id="filter_barangay" class="form-select form-select-sm" disabled>
                            <option value="">-- Select City First --</option>
                        </select>
                    </div>

                    <!-- Duplicity Check Action Button -->
                    <!-- Example Form Wrapper for Duplicity Check -->
                </div>
            </div>

            <!-- Table Card -->
            <div class="table-card">
                <div class="p-3 table-responsive">
                    <table id="recordsTable" class="table table-striped table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th>VIEW PROFILE</th>
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

        <!-- Footer -->
        <footer class="bg-white border-top p-3 text-center text-muted small">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>  
    $(document).ready(function () {
        // Sidebar Toggle Trigger (Restored)
        $(document).on('click', '#sidebarToggle', function () {
            if ($(window).width() < 992) {
                $('#sidebar').toggleClass('show-mobile');
            } else {
                $('#sidebar').toggleClass('collapsed');
                $('#main-content').toggleClass('expanded');
            }
        });

        var fileName = "<?php echo isset($file_name) ? addslashes($file_name) : ''; ?>";
        
        // Explicitly target get_records_json for this page layout
        var ajaxUrl = "<?php echo site_url('tupad/get_records_json'); ?>";

        // 1. Initialize DataTables with Server-Side Processing
        var table = $('#recordsTable').DataTable({
            pageLength: 25,
            responsive: true,
            processing: true,
            deferRender: true,
            serverSide: true,
            searchDelay: 500,
            deferLoading: 0, // <--- PREVENTS AUTOMATIC INITIAL LOAD
            language: {
                loadingRecords: "Please select a location filter above to load records...", 
                emptyTable: "No records found. Please select a province, city, or barangay."
            },
            ajax: {
                url: ajaxUrl,
                type: "POST",
                data: function (d) {
                    if (fileName) {
                        d.file_name = fileName;
                    }
                    
                    var province = $('#filter_province').val();
                    var city = $('#filter_city').val();
                    var barangay = $('#filter_barangay').val();

                    // Attach custom dropdown location filters
                    d.province = province;
                    d.city = city;
                    d.barangay = barangay;

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
                        return '<a href="' + profileUrl + '" class="btn btn-sm btn-primary" title="View Profile">' +
                                '<i class="bi bi-eye"></i> View</a>';
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
                { data: 'province_name' },      // Now displays description from join query
                { data: 'municipality_name' }, // Now displays description from join query
                { data: 'barangay_name' },      // Now displays description from join query
    
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
                            return '<span class="badge bg-success-subtle text-success fw-semibold">Active</span>';
                        } else {
                            return '<span class="badge bg-secondary-subtle text-secondary fw-semibold">Inactive</span>';
                        }
                    }
                }
            ]
        });






        // 1. PROVINCE FILTER
        $('#filter_province').on('change', function () {
            var provCode = $(this).val();
            $('#filter_city').html('<option value="">Loading Cities...</option>').prop('disabled', true);
            $('#filter_barangay').html('<option value="">-- Select City First --</option>').prop('disabled', true);
            table.ajax.reload();

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

        // 2. CITY / MUNICIPALITY FILTER
        $('#filter_city').on('change', function () {
            var citymunCode = $(this).val();
            $('#filter_barangay').html('<option value="">Loading Barangays...</option>').prop('disabled', true);
            table.ajax.reload();

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

        // 3. BARANGAY FILTER
        $('#filter_barangay').on('change', function () {
            table.ajax.reload();
        });

      
    });
    </script>

</body>

</html>