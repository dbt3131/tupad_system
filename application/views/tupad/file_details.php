<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- 
      DYNAMIC TITLE
      - Purpose: Displays the specific file name in the browser tab.
      - Controller: Passed as `$file_name` from `Tupad.php` -> `view_file_data()` method.
    -->
    <title>File Records - <?php echo htmlspecialchars($file_name); ?></title>

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
        }

        /* --- MAIN CONTENT & NAVBAR --- */
        #main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease-in-out;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: auto;
        }

        #main-content.expanded {
            margin-left: 0;
        }

        .table-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 1.5rem;
            width: 100%;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        /* Responsive Fixes for Mobile / Small Screens */
        @media (max-width: 991.98px) {
            #main-content {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }
    </style>
</head>

<body>
    <!-- 
      NAVBAR TEMPLATE VIEW
      - Purpose: Displays top navigation bar.
    -->
    <?php $this->load->view('templates/navbar'); ?>
    
    <!-- Main Content Wrapper -->
    <div id="main-content">
        
        <!-- 
          SIDEBAR TEMPLATE VIEW
          - Purpose: Displays side navigation.
        -->
        <?php $this->load->view('templates/sidebar'); ?>

        <!-- Main Workspace -->
        <main class="p-3 p-md-4 flex-grow-1">
            
            <!-- Navigation Back Button & Title -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <!-- Back Button: Returns to the batch files list -->
                    <a href="<?php echo site_url('tupad/view_files'); ?>" class="btn btn-sm btn-outline-secondary mb-2">
                        <i class="bi bi-arrow-left me-1"></i> Back to File List
                    </a>
                    <h3 class="fw-bold mb-0 text-break">
                        <i class="bi bi-file-earmark-excel text-success me-2"></i><?php echo htmlspecialchars($file_name); ?>
                    </h3>
                    <p class="text-muted small mb-0">Displaying records imported from this file.</p>
                </div>
            </div>

            <!-- Table Card -->
            <div class="table-card">
                <div class="p-3 table-responsive">
                    <!-- 
                      RECORDS TABLE (SERVER-SIDE)
                      - Purpose: To display thousands of records efficiently without crashing the browser.
                      - Note: <tbody> is empty because DataTables handles it dynamically via AJAX.
                    -->
                    <table id="recordsTable" class="table table-striped table-hover align-middle text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Ext Name</th>
                                <th>Date of Birth</th>
                                <th>Barangay</th>
                                <th>Municipality</th>
                                <th>Province</th>
                                <th>Uploaded By</th>
                                <th>Date Uploaded</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated dynamically via server-side processing -->
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
    
    <!-- Sidebar Toggle & DataTables Setup -->
    <script>
        $(document).ready(function () {
            // 1. Pass file_name safely from PHP to JavaScript variable
            var fileName = "<?php echo htmlspecialchars($file_name, ENT_QUOTES); ?>";

            // 2. Sidebar Toggle script for navbar hamburger menu
            $(document).on('click', '#sidebarToggle', function () {
                if ($(window).width() < 992) {
                    $('#sidebar').toggleClass('show-mobile');
                } else {
                    $('#sidebar').toggleClass('collapsed');
                    $('#main-content').toggleClass('expanded');
                }
            });

            /*
             * 3. DATATABLES SERVER-SIDE INITIALIZATION
             * - Fixed alignment with HTML <thead> structure: Barangay -> Municipality -> Province
             * - Added `defaultContent` fallback to prevent DataTables "Requested unknown parameter" warnings.
             */
            $('#recordsTable').DataTable({
                pageLength: 25,
                responsive: true,
                processing: true, 
                serverSide: true, 
                deferRender: true,
                searchDelay: 500, 
                ajax: {
                    url: "<?php echo site_url('tupad/get_records_by_file_json'); ?>",
                    type: "POST",
                    data: function (d) {
                        d.file_name = fileName; 
                        return d; 
                    }
                },
                columns: [
                    // 1. First Name
                    { data: 'tupad_fname', defaultContent: '', render: function(data) { return data ? data.toUpperCase() : ''; } },
                    
                    // 2. Middle Name
                    { data: 'tupad_mname', defaultContent: '', render: function(data) { return data ? data.toUpperCase() : ''; } },
                    
                    // 3. Last Name
                    { data: 'tupad_lname', defaultContent: '', render: function(data) { return data ? data.toUpperCase() : ''; } },
                    
                    // 4. Ext Name
                    { data: 'tupad_ext', defaultContent: '', render: function(data) { return data ? data.toUpperCase() : ''; } },
                    
                    // 5. Date of Birth
                    { 
                        data: null, 
                        defaultContent: 'N/A',
                        render: function (data, type, row) {
                            if (!row.tupad_dob_month && !row.tupad_dob_day && !row.tupad_dob_year) return 'N/A';
                            return (row.tupad_dob_month || '') + '/' + (row.tupad_dob_day || '') + '/' + (row.tupad_dob_year || '');
                        }
                    },
                    
                    // 6. Barangay
                    { data: 'barangay_name', defaultContent: 'N/A' },
                    
                    // 7. Municipality
                    { data: 'municipality_name', defaultContent: 'N/A' },
                    
                    // 8. Province
                    { data: 'province_name', defaultContent: 'N/A' },
                    
                    // 9. Uploaded By
                    { 
                        data: null, 
                        defaultContent: 'N/A',
                        render: function (data, type, row) {
                            var uploader = $.trim((row.uploader_fname || '') + ' ' + (row.uploader_lname || ''));
                            return uploader !== '' ? uploader : 'N/A';
                        }
                    },
                    
                    // 10. Date Uploaded
                    { 
                        data: 'uploaded_at',
                        defaultContent: 'N/A',
                        render: function (data) {
                            if (!data) return 'N/A';
                            var date = new Date(data);
                            return date.toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                        }
                    },
                    
                    // 11. Status
                    { 
                        data: 'tupad_active',
                        defaultContent: '0',
                        render: function (data) {
                            if (data == '0') {
                                return '<span class="badge bg-success-subtle text-success fw-semibold">Active</span>';
                            } else {
                                return '<span class="badge bg-secondary-subtle text-secondary fw-semibold">Inactive</span>';
                            }
                        }
                    },

                    // 12. Actions Column (Set Inactive Button)
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        defaultContent: '',
                        render: function (data, type, row) {
                            if (row.tupad_active == '0') {
                                return '<button type="button" class="btn btn-sm btn-outline-danger btn-set-inactive" data-id="' + row.id + '">' +
                                       '<i class="bi bi-x-circle me-1"></i> Set Inactive</button>';
                            }
                            return '<span class="text-muted small">N/A</span>';
                        }
                    }
                ]
            });

            // 4. Handle click event for setting a record inactive
            $(document).on('click', '.btn-set-inactive', function () {
                var recordId = $(this).data('id');
                var $btn = $(this);

                if (confirm('Are you sure you want to set this entry to inactive?')) {
                    $btn.prop('disabled', true);

                    $.ajax({
                        url: "<?php echo site_url('tupad/set_record_inactive/'); ?>" + recordId,
                        type: "POST",
                        dataType: "json",
                        success: function (response) {
                            if (response.status === 'success') {
                                // Reload table data without resetting the current pagination page
                                $('#recordsTable').DataTable().ajax.reload(null, false);
                            } else {
                                alert(response.message);
                                $btn.prop('disabled', false);
                            }
                        },
                        error: function () {
                            alert('An error occurred while processing your request.');
                            $btn.prop('disabled', false);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>